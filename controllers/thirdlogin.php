<?php
/**
 *第三方登录控制器
 *	数据库导入：检测thirduser表，并将其中的数据导入到系统中(学生：检测用户，不存在则创建,检测对应的班级，不存在则创建，检测对应的所属班级，不在该班级s则添加，学校属性检测，不在学校则自动添加)
 *														 (教师：检测用户，不存在则创建,检测对应的班级，不存在则创建，检测对应的所属班级，不在该班级s则添加，学校属性检测，不在学校则自动添加,教研组检测不存在则创建，不属于则添加)
 *
 *	根据url返回key(用户导入检测)和对应的请求参数
 *
 */
class ThirdloginController extends CControl{ 
	//数据库对象
	private $db = NULL;
	//第三方appid
	private $appid = NULL;
	//第三方账号导入系统，用户名前缀
	private $prefix = NULL;
	//当前学校管理员uid
	private $roomManagerUid = 0;

	private $applist = array( 
		'10125029349'=>array('appsec'=>'3984948596859','prefix'=>'mz_','rooms'=>array('jxhlw','zgzx','jxsz','wxyz','ys','qihui')),
		'10125029200'=>array('appsec'=>'39843234436859','prefix'=>'ry_','rooms'=>array('ys','zhenhai')),
		'10125029401'=>array('appsec'=>'3982323443859','prefix'=>'zh_','rooms'=>array('zhxy','zhxyxx','zhxygz','ykt100')),
	);

	private $grademap = array('0'=>'默认年级','1'=>'一年级','2'=>'二年级','3'=>'三年级','4'=>'四年级','5'=>'五年级','6'=>'六年级','7'=>'初一','8'=>'初二','9'=>'初三','10'=>'高一','11'=>'高二','12'=>'高三','13'=>'其它年级');


	public function __construct(){
		parent::__construct();
		set_time_limit(0);
		$this->db = Ebh::app()->getDb();
		Ebh::app()->getDb()->set_con(0);
		$this->appid = '10125029349';
		$this->prefix = 'mz_';
	}

	//url请求入口
	public function index(){
		echo $this->urlLogic();
	}


	//数据库导入入口
	public function doimport(){
		echo json_encode(array('errno'=>'0','errmsg'=>'后台导入中...'));
		fastcgi_finish_request();
		$count = 0;
		while(true){
			$res = $this->_nextDataFromDb();
			if($res['errno'] === '-1'){
				log_message('没有要导入的数据啦,导入数据'.$count.'条');
				break; //没有要导入的数据
			}
			if($res['errno'] !== '0'){
				log_message($res['msg']);
				echo $res['msg'];
				break;
			}
			$r = $this->_run($res);
			
			$id = $res['data']['user']['id'];
			if($r['errno'] === '0'){
				$this->_updateUserForExport($id,1);
			}else{
				$this->_updateUserForExport($id,2,$r['errmsg']);
			}
			$count++;
		}
	}


	private function _run($res){
		$user = $res['data']['user'];
		$roominfos = $res['data']['roominfos'];
		$groupnames = $res['data']['groupnames'];
		$curroom = $res['data']['curroom'];
		$this->roomManagerUid = $curroom['uid'];
		$crid = $res['data']['crid'];
		$res = $this->_ensureUser($user,$crid);
		if($res['errno'] !== '0'){
			return $res;
		}
		$user = $res['data'];
		
		$res = $this->_fixSearchable($user,$curroom);
		if($res['errno'] !== '0'){
			return $res;
		}

		if( $user['groupid'] == 6 ) {
			$res = $this->_ensureStuInRoom($crid,$user);
			if($res['errno'] !== '0'){
				return $res;
			}
		}
		$roominfo_fixed = TRUE;
		foreach ($roominfos as $roominfo) {
			$res = $this->_fixClass($user,$roominfo);
			if($res['errno'] !== '0'){
				$roominfo_fixed = FALSE;
				break;
			}
		}
		if($roominfo_fixed === FALSE){
			return $res;
		}

		$group_fixed = TRUE;
		if( ($user['groupid'] == 5) && !empty($groupnames)) {
			//处理组信息
			foreach ($groupnames as $groupname) {
				$res = $this->_fixGroup($user['uid'],$groupname,$crid);
				if($res['errno'] !== '0'){
					$group_fixed = FALSE;
					break;
				}
			}
		}
		if($group_fixed === FALSE){
			return $res;
		}

		if($user['groupid'] == 5 && empty($roominfos)) {
			$r = $this->_addTeacherToRoom($user['uid'],$crid);
			if($r['errno'] !== '0'){
				return $r;
			}
		}

		return array('errno'=>'0','errmsg'=>'','data'=>$user);
		//通知队列消息处理成功...ack
		//else nack
	}


	/**
	 *获取下一条需要导入的数据
	 */
	private function _nextDataFromDb(){
		$res = array('errno'=>'0','errmsg'=>'','data'=>array());
		$user_for_import = $this->_getNextLineForExport();
		if(empty($user_for_import)){
			$res['errno'] = '-1';
			$res['errmsg'] = '没有要处理的数据';
			return $res;
		}

		($user_for_import['role'] == 1) && ($groupid = 5) || ($groupid = 6);

		$user = array(
			'username'=>$user_for_import['mobile'],
			'realname'=>$user_for_import['uname'],
			'mobile'=>$user_for_import['mobile'],
			'passwd'=>$user_for_import['passwd'],
			'sex'=>$user_for_import['sex'],
			'groupid'=>$groupid,
			'id'=>$user_for_import['id']
		);


		$roomdomain = $user_for_import['room'];
		$curroom = $this->_getRoomByDomain($roomdomain);
		if(empty($curroom)){
			$res['errno'] = '90003';
			$res['errmsg'] = '学校不存在'.$roomdomain;
			return $res;
		}
		$crid = $curroom['crid'];

		$classinfo = $user_for_import['classstr'];
		$roominfos = array();
		if(!empty($classinfo)){
			$classinfo_each = explode('@@',$classinfo);
			foreach ($classinfo_each as $cinfo) {
				list($grade,$class,$classname) = explode(':',$cinfo);
				$grade = intval($grade);
				$class = intval($class);
				if(empty($grade) || empty($class) || empty($classname)){
					$res['errno'] = '90001';
					$res['errmsg'] = '班级/年级信息不正确'.$classinfo;
					return $res;
				}
				$roominfos[] = array('grade'=>$grade,'class'=>$class,'crid'=>$crid,'classname'=>$classname);
			}
		}
		// if(empty($roominfos)){
		// 	$res['errno'] = '90002';
		// 	$res['errmsg'] = '班级/年级信息不正确'.$classinfo;
		// 	return $res;
		// }

		$res['data']['user'] = $user;
		$res['data']['roominfos'] = $roominfos;
		$groupnames = $user_for_import['groupstr'];
		$res['data']['groupnames'] = explode('@@',$groupnames);
		$res['data']['crid'] = $crid;
		$res['data']['curroom'] = $curroom;
		return $res;
	}


	//从传入参数获取数据(暂时和从数据库获取数据一样,后面可能要修改)
	private function _nextDataFromParam($user_for_import = array()){
		$res = array('errno'=>'0','errmsg'=>'','data'=>array());
		($user_for_import['role'] == 1) && ($groupid = 5) || ($groupid = 6);

		$user = array(
			'username'=>$user_for_import['mobile'],
			'realname'=>$user_for_import['uname'],
			'mobile'=>$user_for_import['mobile'],
			'passwd'=>$user_for_import['passwd'],
			'sex'=>$user_for_import['sex'],
			'groupid'=>$groupid,
			'id'=>$user_for_import['id']
		);


		$roomdomain = $user_for_import['room'];
		$curroom = $this->_getRoomByDomain($roomdomain);
		if(empty($curroom)){
			$res['errno'] = '90003';
			$res['errmsg'] = '学校不存在'.$roomdomain;
			return $res;
		}
		$crid = $curroom['crid'];

		$classinfo = $user_for_import['classstr'];
		$roominfos = array();
		if(!empty($classinfo)){
			$classinfo_each = explode('@@',$classinfo);
			foreach ($classinfo_each as $cinfo) {
				list($grade,$class,$classname) = explode(':',$cinfo);
				$grade = intval($grade);
				$class = intval($class);
				if(empty($grade) || empty($class) || empty($classname)){
					$res['errno'] = '90001';
					$res['errmsg'] = '班级/年级信息不正确/班级名称'.$classinfo;
					return $res;
				}
				$roominfos[] = array('grade'=>$grade,'class'=>$class,'crid'=>$crid,'classname'=>$classname);
			}
		}
		
		// if(empty($roominfos)){
		// 	$res['errno'] = '90002';
		// 	$res['errmsg'] = '班级/年级信息不正确'.$classinfo;
		// 	return $res;
		// }


		$res['data']['user'] = $user;
		$res['data']['roominfos'] = $roominfos;
		$groupnames = $user_for_import['groupstr'];
		$res['data']['groupnames'] = explode('@@',$groupnames);
		$res['data']['crid'] = $crid;
		$res['data']['curroom'] = $curroom;
		return $res;

	}


	//修正用户,user,ouser没有则创建，同时存在但是uid不一致返回失败
	private function _ensureUser($user = array(),$crid = 0){
		$res = array('errno'=>'0','errmsg'=>'','data'=>array());
		$ousername = $user['id'];
		$username = $this->prefix.$ousername;
		$realname = $user['realname'];
		$passwd = $user['passwd'];
		$mobile = $user['mobile'];
		$id = $user['id'];
		$groupid = $user['groupid'];
		$sex = $user['sex'];
		$usertag = 0;
		if($groupid == 5){
			$usertag = 1;
		}
		$appid = $this->appid;

		$old_data_fiexed_res = $this->_fixOuser(array(
			'id'=>$id,
			'passwd'=>$passwd,
			'mobile'=>$mobile
		));
		if($old_data_fiexed_res['errno'] !== '0'){
			return $old_data_fiexed_res;
		}
		$uid = !empty($old_data_fiexed_res['data']['uid'])?$old_data_fiexed_res['data']['uid']:0;
		$this->db->begin_trans();
		if(!empty($uid) && is_numeric($uid)){
			$user = $this->_getuserbyuid($uid);
		}else{
			$user = $this->_getuserbyusername($username);
		}
		if(empty($user)){
			$toadd = 1;
			$param = array('username'=>$username,'mpassword'=>$passwd,'mobile'=>$mobile,'nickname'=>$username,'realname'=>$realname,'sex'=>$sex);
			if($groupid == 5){
				$uid = $this->_addteacher($param);
			}else{
				$uid = $this->_addmember($param);
			}
			$user = $param;
			$user['uid'] = $uid;
			$user['groupid'] = $groupid;
			$user['password'] = $user['mpassword'];
			if(empty($uid)){
				$res['errno'] = '1';
				$res['errmsg'] = 'user表创建用户失败';
				$this->db->rollback_trans();
				return $res;
			}
		}else{
			$toadd = 0;
			$uid = $user['uid'];
		}

		$ouser = $this->_getOuserByUserNameAndAppid($ousername,$appid);
		if(empty($ouser)){
			$nparam = array(
				'uid'=>$uid,
				'useruid'=>intval($id),
				'username'=>$ousername,
				'userpass'=>$passwd,
				'userarr'=>0,
				'appid'=>$appid,
				'crid'=>$crid,
				'usertag'=>$usertag
			);
			$ouid = $this->_addOuser($nparam);
			$ouser = $nparam;
			$ouser['ouid'] = $ouid;
			if(empty($ouid)){
				$res['errno'] = '2';
				$res['errmsg'] = 'ouser表创建用户失败';
				$this->db->rollback_trans();
				return $res;
			}
		}else{
			//比对密码是否修改
			if($ouser['userpass'] != $passwd){
				//todo
				$param = array('userpass'=>$passwd);
				$where = array('ouid'=>$ouser['ouid']);
				$res_update = $this->_ouserUpdate($param,$where);
				if($res_update == 0){	
					$res['errno'] = '3';
					$res['errmsg'] = 'ouser升级密码失败';
					$this->db->rollback_trans();
					return $res;
				}
			}
		}

		if($user['uid'] != $ouser['uid']) {
			//user ouser表存在对应用户，但是ouser里的uid和user里的uid不一致
			$res['errno'] = '4';
			$res['errmsg'] = 'user ouser表存在对应用户，但是ouser里的uid和user里的uid不一致';
			$this->db->rollback_trans();
		}else{
			$res['data'] = $user;
			$this->db->commit_trans();
		}
		if($toadd == 1){
			Ebh::app()->lib('XNums')->add('user');
			if($user['groupid'] == 5){
	        	Ebh::app()->lib('XNums')->add('teacher');
			}
		}
		return $res;
	}

	//确保学生在学校里面
	private function _ensureStuInRoom($crid,$user){
		$res = array('errno'=>'0','errmsg'=>'','data'=>array());
		$uid = $user['uid'];
		//获取用户是否在此平台
		$ruser = $this->_getroomuserdetail($crid,$uid);
		if(empty($ruser)) {	//不存在 
			$enddate = 0;
			$param = array('crid'=>$crid,'uid'=>$user['uid'],'begindate'=>SYSTIME,'enddate'=>$enddate,'cnname'=>$user['realname'],'sex'=>$user['sex']);
			$result = $this->_roomUserInsert($param);
			if($result === FALSE){
				$res['errno'] = '8076';
				$res['errmsg'] = '学生加入教室失败';
			}
		}
		return $res;
	}

	//修正班级
	private function _fixClass($user,$roominfo){
		$res = array('errno'=>'0','errmsg'=>'','data'=>array());
		$res_getclass = $this->_getClass($roominfo);
		if($res_getclass['errno'] !== '0'){
			return $res_getclass;
		}
		$classdetail = $res_getclass['data'];
		if($user['groupid'] == 5) {
			return $this->_fixClass_Teacher($user['uid'],$classdetail);
		}else{
			return $this->_fixClass_Student($user['uid'],$classdetail);
		}
	}


	//老师加入班级
	private function _fixClass_Teacher($tid,$classdetail){
		$res = array('errno'=>'0','errmsg'=>'','data'=>array());
		$crid = $classdetail['crid'];
		$classid = $classdetail['classid'];

		//教师不在教室则将教师添加到教室
		$exist = $this->_isTeacherHasInRoom($tid,$crid);
		if($exist == 0){
			$param['tid'] = $tid;
			$param['crid'] = $crid;
			$param['status'] = 1;
			$param['cdateline'] = SYSTIME;
			$param['role'] = 1;
			$res_add = $this->_addroomteacher($param);
			if($res_add === FALSE){
				$res['errno'] = '31';
				$res['errmsg'] = '将教师添加到教室失败';
				return $res;
			}
		}

		//将教师添加到班级，存在则不操作 ebh_classteachers
		$sql = 'select count(1) as count from ebh_classteachers where uid = '.$tid.' AND classid = '.$classid;
		$ininfo = $this->db->query($sql)->row_array();
		if($ininfo['count'] != 0){ //存在不做任何操作
			return $res;
		}
		$param = array('uid'=>$tid,'classid'=>$classid);
		$res_insert = $this->db->insert('ebh_classteachers',$param);		
		if($res_insert === FALSE){
			$res['errno'] = '32';
			$res['errmsg'] = '将教师添加到班级失败';
		}
		return $res;
	}


	//学生加入班级
	private function _fixClass_Student($uid,$classdetail){
		$res = array('errno'=>'0','errmsg'=>'','data'=>array());
		$crid = $classdetail['crid'];
		$classid = $classdetail['classid'];
		$sql = 'select count(1) as count from ebh_classstudents where uid = '.$uid.' AND classid = '.$classid;
		$ininfo = $this->db->query($sql)->row_array();
		if($ininfo['count'] != 0){ //存在不做任何操作
			return $res;
		}
		$res_insert = $this->_addclassstudent(array('uid'=>$uid,'classid'=>$classid,'crid'=>$crid));
		if($res_insert === FALSE){
			$res['errno'] = '33';
			$res['errmsg'] = '将学生添加到班级失败';
		}
		return $res;
	}


	//修正组信息(教师放到组里面)
	private function _fixGroup($uid = 0,$groupname = 0,$crid = 0){
		$res_group = $this->_getGroup($groupname,$crid);
		if($res_group['errno'] !== '0'){
			return $res_group;
		}
		$tgroupid = $res_group['data']['groupid'];
		$res = array('errno'=>'0','errmsg'=>'','data'=>array());
		$sql = 'select tid,crid,groupid from ebh_teachergroups where tid='.$uid.' AND crid = '.$crid.' AND groupid = '.$tgroupid. ' limit 1';
		$ininfo = $this->db->query($sql)->row_array();
		if(!empty($ininfo)){
			return $res;
		}
		//添加教师到分组
		$param = array('tid'=>$uid,'groupid'=>$tgroupid,'crid'=>$crid);
		$r = $this->db->insert('ebh_teachergroups',$param);
		if($r === FALSE){
			$res['errno'] = '61';
			$res['errmsg'] = '将教师添加到分组失败';
		}
		return $res;
	}

	//根据array('crid'=>10330,'grade'=>3,'class'=>5)获取班级信息(没有则自动创建)
	private function _getClass($roominfo){
		$res = array('errno'=>'0','errmsg'=>'','data'=>array());
		$grade = $roominfo['grade'];
		$crid = $roominfo['crid'];
		$classname = $roominfo['classname'];
		if(!empty($classname)){
			$sql = 'select * from ebh_classes where crid = '.$crid.' AND classname = \''.$this->db->escape_str($classname).'\'';
			$classdetail = $this->db->query($sql)->row_array();
			if(empty($classdetail)){
				//创建班级
				$param = array('crid'=>$crid,'classname'=>$classname,'grade'=>$grade,'district'=>0);
				$classid = $this->_addclass($param);
				if(!empty($classid)){
					$classdetail = $param;
					$classdetail['classid'] = $classid;
				}else{
					$res['errno'] = '11';
					$res['errmsg'] = '创建班级失败sql:'.$sql;
					return $res;
				}
			}
			$res['data'] = $classdetail;
			return $res;
		}else{
			$res['errno'] = '12';
			$res['errmsg'] = '年级信息映射失败';
			return $res;
		}
	}


	//根据组名获取组信息，没有则自动创建
	private function _getGroup($groupname = '',$crid = 0){
		$res = array('errno'=>'0','errmsg'=>'','data'=>array());
		$groupinfo = $this->_getTGroupByNameAndCrid($groupname,$crid);
		if(empty($groupinfo)){
			//创建组
			$param = array(
				'groupname'=>$groupname,
				'crid'=>$crid,
				'uid'=>$this->roomManagerUid
			);
			$tgroupid = $this->_TgroupInsert($param);
			if($tgroupid == 0){
				$res['errno'] = '51';
				$res['errmsg'] = '创建组:'.$groupname.'组[crid:'.$crid.']失败';
				return $res;
			}
			$groupinfo = $param;
			$groupinfo['groupid'] = $tgroupid;
		}
		$res['data'] = $groupinfo;
		return $res;
	}

	//教师添加到学校
	private function _addTeacherToRoom($tid=0,$crid=0){
		$res = array('errno'=>'0','errmsg'=>'','data'=>array());
		//教师不在教室则将教师添加到教室
		$exist = $this->_isTeacherHasInRoom($tid,$crid);
		if($exist == 0){
			$param['tid'] = $tid;
			$param['crid'] = $crid;
			$param['status'] = 1;
			$param['cdateline'] = SYSTIME;
			$param['role'] = 1;
			$res_add = $this->_addroomteacher($param);
			if($res_add === FALSE){
				$res['errno'] = '31';
				$res['errmsg'] = '将教师添加到教室失败';
				return $res;
			}
		}
		return $res;
	}

	//可查询数据表
	private function _fixSearchable($user = array(),$curroom) {
		$res = array('errno'=>'0','errmsg'=>'','data'=>array());
		$sql = 'select count(1) as count from ebh_searchableclassrooms where username = \''.$this->db->escape_str($user['username']).'\'';
		$r = $this->db->query($sql)->row_array();
		if($r['count'] != 0){
			return $res;
		}
		$param = array(
			'username'=>$user['username'],
			'realname'=>$user['realname'],
			'sex'=>$user['sex'],
			'crname'=>$curroom['crname'],
			'upcrid'=>$curroom['crid']
		);
		$insert_result = $this->db->insert('ebh_searchableclassrooms',$param);
		if($insert_result === FALSE) {
			$res['errno'] = '90007';
			$res['errmsg'] = '可查询表用户新增失败';
		}
		return $res;
	}

	// #################################################################数据修正模型开始###################################################################

	private function _addOuser($param = array()){
		if(!empty($param['uid']))
			$userarr['uid'] = $param['uid'];
		if(!empty($param['useruid']))
			$userarr['useruid'] = $param['useruid'];
		if(isset($param['username']))
			$userarr['username'] = $param['username'];
		if(isset($param['userpass']))
			$userarr['userpass'] = $param['userpass'];
		if(!empty($param['usertag']))
			$userarr['usertag'] = $param['usertag'];
		if(!empty($param['appid']))
			$userarr['appid'] = $param['appid'];
		if(!empty($param['crid']))
			$userarr['crid'] = $param['crid'];
		return $this->db->insert('ebh_ousers',$userarr);
	}

	/*
	添加班级
	@param array $param crid,classname
	@return int $classid 班级号
	*/
	public function _addclass($param){
		$setarr['crid'] = $param['crid'];
		$setarr['classname'] = trim($param['classname'],' ');
		$setarr['classname'] = str_replace('　','',$setarr['classname']);
		if(isset($param['grade']))
			$setarr['grade'] = $param['grade'];
		if(isset($param['district']))
			$setarr['district'] = $param['district'];
		$setarr['dateline'] = SYSTIME;
		return $this->db->insert('ebh_classes',$setarr);
	}

	/*
	添加学生到classstudent表
	@param array $param crid classid uid
	*/
	public function _addclassstudent($param){
		$setarr['uid'] = $param['uid'];
		$setarr['classid'] = $param['classid'];
		$this->db->update('ebh_classes',array(),array('classid'=>$param['classid']),array('stunum'=>'stunum+1'));
		$this->db->update('ebh_classrooms',array(),array('crid'=>$param['crid']),array('stunum'=>'stunum+1'));
		return $this->db->insert('ebh_classstudents',$setarr);
	}

	//教师组添加一条记录
	public function _TgroupInsert($param = array()){
		if(empty($param)){
			return 0;
		}
		return $this->db->insert('ebh_tgroups',$param);
	}

	/**
	*根据第三方账号获取对应的OUser对象
	*/
	private function _getOuserByUserNameAndAppid($username = '',$appid = 0) {
		$username = $this->db->escape($username);
        $sql = "select u.ouid,u.uid,u.userpass,u.usertag from ebh_ousers u where u.username=$username and appid = $appid limit 1";
        $ouser = $this->db->query($sql)->row_array();
		return $ouser;
	}


	//矫正旧数据
	private function _fixOuser($ouser = array()){
		$res = array(
			'errno'=>'0',
			'errmsg'=>'',
			'data'=>array()
		);
		if(empty($ouser) || !is_numeric($ouser['id'])){
			$res['errno'] = '90005';
			$res['errmsg'] = 'fixouser param error';
			return $res;
		}
		$useruid = intval($ouser['id']);
		$sql = 'select ou.ouid,ou.uid,ou.username,ou.appid from ebh_ousers ou where ou.useruid='.$useruid.' AND ou.appid=\'\' limit 1';
		$row = $this->db->query($sql)->row_array();
		if(empty($row)){
			return $res;
		}

		$param = array(
			'username'=>$useruid,
			'appid'=>$this->appid
		);
		$wherearr = array(
			'ouid'=>$row['ouid']
		);
		$affect_row = $this->_ouserUpdate($param,$wherearr);
		if($affect_row === FALSE){
			$res['errno'] = '90001';
			$res['errmsg'] = '升级ouser失败';
			return $res;
		}

		//修改ebh_users用户名
		$uid = $row['uid'];
		$username = $this->prefix.$useruid;
		$param = array(
			'username'=>$username
		);
		$wherearr = array(
			'uid'=>$uid
		);
		$affect_row = $this->db->update('ebh_users',$param,$wherearr);
		if($affect_row === FALSE){
			$res['errno'] = '90002';
			$res['errmsg'] = '升级user失败';
			return $res;
		}
		$res['data']['uid'] = $uid;
		return $res;
	}

	//根据组名和学校id获取组信息
	private function _getTGroupByNameAndCrid($groupname='',$crid=''){
		$sql = 'select groupid,groupname from ebh_tgroups where crid = '.$crid.' AND groupname = \''.$this->db->escape_str($groupname).'\'';
		return $this->db->query($sql)->row_array();

	}

	private function _getRoomByDomain($domain = '') {
        $sql = 'select crid,uid,crname from ebh_classrooms where domain=\''.$this->db->escape_str($domain).'\' limit 1';
        return $this->db->query($sql)->row_array();
    }

    private function _getNextLineForExport(){
    	$sql = 'select id,mobile,passwd,sex,role,uname,room,classstr,groupstr from thirduser where tag != 1 limit 1';
    	return $this->db->query($sql)->row_array();
    }

    private function _updateUserForExport($id = 0,$tag = 0,$errmsg = ''){
    	$param = array(
    		'tag'=>$tag,
    		'errmsg'=>$errmsg
    	);
    	$where = array(
    		'id'=>$id
    	);
    	return $this->db->update('thirduser',$param,$where);
    }

    	/*
	添加会员
	@param array $param
	@return int
	*/
	private function _addmember($param){
		if(!empty($param['username']))
			$userarr['username'] = $param['username'];
		if(!empty($param['password']))
			$userarr['password'] = md5($param['password']);
		if (!empty($param['mpassword']))	//md5加密后的用户密码
                $userarr['password'] = $param['mpassword'];
		if(isset($param['realname']))
			$userarr['realname'] = $param['realname'];
		if(isset($param['nickname']))
			$userarr['nickname'] = $param['nickname'];
		if(!empty($param['dateline']))
			$userarr['dateline'] = $param['dateline'];
		if(isset($param['sex']))
			$userarr['sex'] = $param['sex'];
		if(!empty($param['mobile']))
			$userarr['mobile'] = $param['mobile'];
		if(!empty($param['citycode']))
			$userarr['citycode'] = $param['citycode'];
		if(isset($param['address']))
			$userarr['address'] = $param['address'];
		if(!empty($param['email']))
			$userarr['email'] = $param['email'];
		if(!empty($param['face']))
			$userarr['face'] = $param['face'];
		if(!empty($param['qqopid']))
			$userarr['qqopid'] = $param['qqopid'];
		if(!empty($param['sinaopid']))
			$userarr['sinaopid'] = $param['sinaopid'];
		
		if(!empty($param['wxopenid']))
			$userarr['wxopenid'] = $param['wxopenid'];
		
		if(!empty($param['schoolname']))
			$userarr['schoolname'] = $param['schoolname'];
		$userarr['status'] = 1;
		$userarr['groupid'] = 6;
		// var_dump($userarr);
		$uid = $this->db->insert('ebh_users',$userarr);
		if($uid){
			$memberarr['memberid'] = $uid;
			if(isset($param['realname']))
				$memberarr['realname'] = $param['realname'];
			if(isset($param['nickname']))
				$memberarr['nickname'] = $param['nickname'];
			if(isset($param['sex']))
				$memberarr['sex'] = $param['sex'];
			if(!empty($param['birthdate']))
				$memberarr['birthdate'] = $param['birthdate'];
			if(!empty($param['phone']))
				$memberarr['phone'] = $param['phone'];
			if(!empty($param['mobile']))
				$memberarr['mobile'] = $param['mobile'];
			if(!empty($param['native']))
				$memberarr['native'] = $param['native'];
			if(!empty($param['citycode']))
				$memberarr['citycode'] = $param['citycode'];
			if(isset($param['address']))
				$memberarr['address'] = $param['address'];
			if(!empty($param['msn']))
				$memberarr['msn'] = $param['msn'];
			if(!empty($param['qq']))
				$memberarr['qq'] = $param['qq'];
			if(!empty($param['email']))
				$memberarr['email'] = $param['email'];
			if(!empty($param['face']))
				$memberarr['face'] = $param['face'];
			if(isset($param['profile']))
				$memberarr['profile'] = $param['profile'];
			$memberid = $this->db->insert('ebh_members',$memberarr);
			
		}
		return $uid;
	}

	 /*
      添加教师
      @param array $param
      @return int
     */

    private function _addteacher($param) {
		if(!empty($param['username']))
			$userarr['username'] = $param['username'];
		if(!empty($param['password']))
			$userarr['password'] = md5($param['password']);
		if (!empty($param['mpassword']))	//md5加密后的用户密码
                $userarr['password'] = $param['mpassword'];
		if(!empty($param['realname']))
			$userarr['realname'] = $param['realname'];
		if(isset($param['nickname']))
			$userarr['nickname'] = $param['nickname'];
		if(!empty($param['dateline']))
			$userarr['dateline'] = $param['dateline'];
		if(isset($param['sex']))
			$userarr['sex'] = $param['sex'];
		if(!empty($param['mobile']))
			$userarr['mobile'] = $param['mobile'];
		if(!empty($param['citycode']))
			$userarr['citycode'] = $param['citycode'];
		if(!empty($param['face']))
			$userarr['face'] = $param['face'];
		$userarr['status'] = 1;
		$userarr['groupid'] = 5;
        $uid = $this->db->insert('ebh_users', $userarr);
        if ($uid) {
            $teacherarr['teacherid'] = $uid;
			if(!empty($param['realname']))
				$teacherarr['realname'] = $param['realname'];
			if(isset($param['nickname']))
				$teacherarr['nickname'] = $param['nickname'];
			if(isset($param['sex']))
				$teacherarr['sex'] = $param['sex'];
			if(!empty($param['phone']))
				$teacherarr['phone'] = $param['phone'];
			if(!empty($param['mobile']))
				$teacherarr['mobile'] = $param['mobile'];
			if(!empty($param['fax']))
				$teacherarr['fax'] = $param['fax'];
			if(!empty($param['tag']))
				$teacherarr['tag'] = $param['tag'];
			if(isset($param['schoolage']))
				$teacherarr['schoolage'] = $param['schoolage'];
			if(!empty($param['profile']))
				$teacherarr['profile'] = $param['profile'];
            if(isset($param['profitratio']))
            $teacherarr['profitratio'] = $param['profitratio'];
        	if(isset($param['bankcard']))
            $teacherarr['bankcard'] = $param['bankcard'];
        	if(isset($param['agentid'])){
        		$teacherarr['agentid'] = $param['agentid'];
        	}
        	if (isset($param['agency']))
            	$teacherarr['agency'] = $param['agency'];
            if (isset($param['message']))
            $teacherarr['message'] = $param['message'];
            $res = $this->db->insert('ebh_teachers', $teacherarr);
            
        }return $uid;
    }

    //判断教师是否在教室里
    private function _isTeacherHasInRoom($uid = 0,$crid = 0){
    	$sql = 'select count(1) count from ebh_roomteachers rt where rt.tid = '.$uid.' AND rt.crid = '.$crid;
    	$res = $this->db->query($sql)->row_array();
    	return $res['count'];
    }

    /**
     * 根据教室和学员编号获取学员在教室内的信息详情
     * @param type $crid
     * @param type $uid
     * @return type
     */
    private function _getroomuserdetail($crid,$uid) {
        $sql = "select ru.cstatus,ru.rbalance,ru.begindate,ru.enddate from ebh_roomusers ru where ru.crid=$crid and ru.uid=$uid";
        return $this->db->query($sql)->row_array();
    }

    /*
	添加学校教师
	@param array $param
	*/
	private function _addroomteacher($param){
		if(!empty($param['tid']))
			$setarr['tid'] = $param['tid'];
		if(!empty($param['crid']))
			$setarr['crid'] = $param['crid'];
		if(isset($param['status']))
			$setarr['status'] = $param['status'];
		if(!empty($param['cdateline']))
			$setarr['cdateline'] = $param['cdateline'];
		if(!empty($param['role']))
			$setarr['role'] = $param['role'];
		$this->db->update('ebh_classrooms',array(),array('crid'=>$param['crid']),array('teanum'=>'teanum+1'));
		return $this->db->insert('ebh_roomteachers',$setarr);
		
	}

	/**
	* 更新第三方账号信息
	*/
	private function _ouserUpdate($param = array(),$where = array()) {
		if(empty($where['ouid']))
			return FALSE;
		$setarr = array();
		if(!empty($param['userpass'])) {
			$setarr['userpass'] = $param['userpass'];
		}
		if(!empty($param['username'])) {
			$setarr['username'] = $param['username'];
		}
		if(!empty($param['appid'])) {
			$setarr['appid'] = $param['appid'];
		}
		if (empty($setarr))
			return FALSE;
		return $this->db->update('ebh_ousers', $setarr, $where);
	}

	/**
    * 根据username获取用户基本信息  场景：学校后台添加教师
    * @param int $uid
    * @return array 
    */
	private function _getuserbyusername($username) {
		$sql = 'select u.uid,u.username,u.password,u.password as mpassword,u.groupid,u.realname,u.sex,u.email,u.mysign from ebh_users u where u.username = \''.$this->db->escape_str($username).'\'';
		return $this->db->query($sql)->row_array();
	}
	private function _getuserbyuid($uid = 0) {
		$sql = 'select u.uid,u.username,u.password,u.password as mpassword,u.groupid,u.realname,u.sex,u.email,u.mysign from ebh_users u where u.uid = '.$uid;
		return $this->db->query($sql)->row_array();
	}


	// #################################################################数据修正模型结束######################################################################


    ########################################url登录###################################################
    //入口
	public function urlLogic(){
		$key = $this->input->post('param');
		if(empty($key)) {
			$this->_renderJson('600000','param is null');
			exit();
		}
		$key = base64_decode($key);
		if(empty($key)) {
			$this->_renderJson('600001','key is null');
		}
		parse_str($key,$keylist);
		if(!is_array($keylist)) {
			$this->_renderJson('600002','key is not valid');
		}
		if(!$this->_checkSign($keylist)) {
			$this->_renderJson('600003','key is not valid');
		}
		if(empty($keylist['op'])) {
			$keylist['op'] = 'study';
		}
		if(empty($keylist['appid'])){
			$this->_renderJson('600013','appid is not null');
		}
		$this->appid = $keylist['appid'];
		if(!empty($this->applist[$this->appid])){
			$this->prefix = $this->applist[$this->appid]['prefix'];
		}
		$appinfo = $this->_getAppInfo($keylist['appid']);
		$username = $keylist['id'];
		$appid = $keylist['appid'];
		if(empty($appid) || empty($username)){
			$this->_renderJson('600001','user info args missing');
		}
		$ouser = $this->_getOuserByUserNameAndAppid($username,$appid);
		if(empty($ouser)){
			$res = $this->_nextDataFromParam($keylist);
			if($res['errno'] !== '0'){
				$this->_renderJson($res['errno'],$res['errmsg']);
			}
			$r = $this->_run($res);
			if($r['errno'] !== '0'){
				$this->_renderJson($r['errno'],$r['errmsg']);
				// $this->_renderJson('7000012','user not exits and regist error 2');
			}
			$user = $r['data'];
		}else{
			$res = $this->_nextDataFromParam($keylist);
			if($res['errno'] !== '0'){
				$this->_renderJson($res['errno'],$res['errmsg']);
				// $this->_renderJson('7000011','user not exits and regist error 3');
			}
			$r = $this->_run($res);
			if($r['errno'] !== '0'){
				$this->_renderJson($r['errno'],$r['errmsg']);
				// $this->_renderJson('7000012','user not exits and regist error 4');
			}
			//第三方用户表密码匹配
			if($ouser['userpass'] !== $keylist['passwd']){
				$param = array(
					'userpass'=>$keylist['passwd']
				);
				$where = array(
					'ouid' => $ouser['ouid']
				);
				$this->_ouserUpdate($param,$where);
			}
			$uid = $ouser['uid'];
			$user = $this->_getUserByUid($uid);
		}

		$to = $keylist['room'];
		if(empty($to)){
			$this->_renderJson('700002','room not missing');
		}

		if(!in_array($to,$appinfo['rooms'])){
			$this->_renderJson('700003','room illige');
		}

		$room = $this->_getRoomByDomain($to);
		if(empty($room)){
			$this->_renderJson('700004','room not exits');
		}

		$k = $this->_getKey($user);
		$this->_renderJson('0','ok',array('k'=>$k,'user'=>$user,'room'=>$room,'reqdata'=>$keylist));
	}

	 /**
     * 插入ebh_roomusers记录，主要用于学员和教室的绑定
     * @param type $param
     * @return boolean
     */
    public function _roomUserInsert($param) {
        if (empty($param['crid']) || empty($param['uid']))
            return FALSE;
        $setarr = array();
        $setarr['crid'] = $param['crid'];
        $setarr['uid'] = $param['uid'];
        if (!empty($param ['cdateline'])) { //记录添加时间
            $setarr ['cdateline'] = $param ['cdateline'];
        } else {
            $setarr ['cdateline'] = SYSTIME;
        }
        if (!empty($param ['begindate'])) { //服务开始时间
            $setarr ['begindate'] = $param ['begindate'];
        }
        if (!empty($param ['enddate'])) {   //服务结束时间
            $setarr ['enddate'] = $param ['enddate'];
        }
        if (!empty($param ['cnname'])) {   //学生真实姓名，此处只做存档用
            $setarr ['cnname'] = $param ['cnname'];
        }
		if (isset($param ['cstatus'])) { //状态，1正常 0 锁定
            $setarr ['cstatus'] = $param['cstatus'];
        }
        if (isset($param ['sex'])) {   //性别
            $setarr ['sex'] = $param ['sex'];
        }
        if (isset($param ['birthday'])) {   //出生日期
            $setarr ['birthday'] = $param ['birthday'];
        }
        if (!empty($param ['mobile'])) {   //联系方式
            $setarr ['mobile'] = $param ['mobile'];
        }
        if (!empty($param ['email'])) {   //邮箱
            $setarr ['email'] = $param ['email'];
        }

        $afrows = $this->db->insert('ebh_roomusers',$setarr);
        return $afrows;
    }


	##############################url导入##################################################################################
	/**
	*验证
	*/
	private function _checkSign($param) {
		if(empty($param['appid']) || empty($param['sign']) || empty($param['t']))
			return FALSE;
		$t = intval($param['t']);
		$curtime = SYSTIME;
		if(($curtime - $t) > 86400) {	//有效期1天
			return FALSE;
		}
		$appsec = $this->_getAppSecret($param['appid']);
		if(empty($appsec))
			return FALSE;
		$sign = $param['sign'];
		unset($param['sign']);
		$newsign = $this->_buildsign($param,$appsec);
		if($newsign == $sign)
			return TRUE;
		return FALSE;
	}

	private function _buildsign($arr,$appsec) {
		$sign = $appsec;
		foreach($arr as $ak=>$av) {
			$sign .=$ak.$av;
		}
		$sign = md5($sign);
		return $sign;
	}

	/**
	*根据应用ID获取对应的第三方应用信息
	*/
	private function _getAppInfo($appid) {
		if(isset($this->applist[$appid]))
			return $this->applist[$appid];
		return FALSE;
	}

	/**
	*根据AppID获取对应的加密key
	*/
	private function _getAppSecret($appid) {
		$appinfo = $this->_getAppInfo($appid);
		if(!empty($appinfo))
			return $appinfo['appsec'];
		return FALSE;
	}

	/**
	*return new valid user token key
	*/
	private function _getKey($user) {
		$uid = $user['uid'];
		$pwd = $user['password'];
		$ip = $this->input->getip();
		$time = SYSTIME;
		$skey = "$pwd\t$uid\t$ip\t$time";
		$auth = authcode($skey, 'ENCODE');
		return $auth;
	}

	//返回json数据并结束脚本
	private function _renderJson($code = 0,$msg = "",$data = array()){
		$jsonArr =  array(
			'code'=>$code,
			'msg'=>$msg,
			'data'=>$data
		);
		echo json_encode($jsonArr);
		exit;
	}
	##############################url导入##################################################################################
}