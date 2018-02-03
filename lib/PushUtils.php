<?php
/**
 *信鸽推送工具类
 *主要用于向手机APP推送各种信息
 *zkq
 *	1.集成好的方法
 *  Ebh::app()->lib('PushUtils')->PushCourseToStudent(cwid);//推送新课件
 *	Ebh::app()->lib('PushUtils')->PushAskToStudent(qid);//提问有人回答
 *	Ebh::app()->lib('PushUtils')->PushExamToStudent(eid);//向学生推送作业
 *	Ebh::app()->lib('PushUtils')->PushAskToTeacher(qid);//有人向您提问(推给老师的)
 *  Ebh::app()->lib('PushUtils')->PushNotice(noticeid);//推送各种各种类型的通知
 *  2.自定义送pushAll(),PushTags(),PushAccountList()等
 *
 */
class PushUtils{
	private static $ACCESSID;
	private static $SECRETKEY;
	private $db;
	private $count = 0;

	public function __construct(){
		class_exists('XingeApp') or require(dirname(__FILE__).'/XingeApp/XingeApp.php');
		$this->db = Ebh::app()->getDb();
		$xingeConfig = Ebh::app()->getConfig()->load('xinge');
		$this->nosend = $xingeConfig['nosend'];
		PushUtils::$ACCESSID = $xingeConfig['accessid'];
		PushUtils::$SECRETKEY = $xingeConfig['secretKey'];
		$this->push = new XingeApp(PushUtils::$ACCESSID, PushUtils::$SECRETKEY);
	}

	private function _check($crid = 0){
		$nosend = $this->nosend;
		if(in_array($crid, $nosend) || empty($crid)){//屏蔽的学校
			return false;
		}else{
			return true;
		}
	}
	
	//下发所有设备
	public function pushAll($param = array()){
		$mess = $this->_packData($param);
		return $this->push->PushAllDevices(0, $mess);
	}

	private function _packData($param = array()){
		if(empty($param) || empty($param['mess']) ){
			log_message("推送消息参数不全,必须包含mess");
			throw new Exception("推送消息参数不全,必须包含mess和action字段", 1);
		}
		$field_mess = $param['mess'];
		$field_action = !empty($param['action'])?$param['action']:array();
		$field_style = !empty($param['style'])?$param['style']:"";
		
		$mess = $this->_packMess($field_mess);
		$action = $this->_packAction($field_action);
		$style = $this->_packStyle($field_style);

		$mess->setAction($action);
		$mess->setStyle($style);

		return $mess;
	}

	//打包样式，暂时不支持定制
	private function _packStyle($param_style = ""){
		if(empty($param_style)){
			$style = new Style(0,1,1,0,0);
		}else{
			$style = new Style(0,1,1,0,0);
		}
		return $style;
	}

	//打包行为
	private function _packAction($param_action = array()){
		$action = new ClickAction();
		$action_type = !empty($param_action['type'])?$param_action['type']:"ACTIVITY";
		if($action_type == 'PACKAGE'){
			$action->setActionType(ClickAction::TYPE_PACKAGE);
		}else if($action_type == 'URL'){
			$action->setActionType(ClickAction::TYPE_URL);
			if(empty($param_action['url'])){
				log_message("设置了推送类型为URL，但是检测不到参数中含有url字段");
				throw new Exception("设置了推送类型为URL，但是检测不到参数中含有url字段", 1);
			}else{
				$action->setUrl($param_action['url']);
				#打开url需要用户确认
				$action->setComfirmOnUrl(1);
			}
		}else if($action_type == 'INTENT'){
			$action->setActionType(ClickAction::TYPE_INTENT);
		}else {
			$action->setActionType(ClickAction::TYPE_ACTIVITY);
		}
		return $action;
	}

	//打包消息
	private function _packMess($param_mess = array()){
		if(empty($param_mess) || empty($param_mess['title']) || empty($param_mess['content'])){
			log_message("消息体字段不全，必须包含title和content字段");
			throw new Exception("消息体字段不全，必须包含title和content字段", 1);
		}
		$title = $param_mess['title'];
		$content = $param_mess['content'];
		$expire = !empty($param_mess['expire'])?$param_mess['expire']:86400;

		$mess = new Message();
		$mess->setType(Message::TYPE_NOTIFICATION);
		$mess->setTitle($title);
		$mess->setContent($content);
		$mess->setExpireTime($expire);
		if(!empty($param_mess['type']) && ($param_mess['type'] == "MESSAGE")){
			$mess->setType(Message::TYPE_MESSAGE);
			if(!empty($param_mess['custom']) && is_array($param_mess['custom'])){
				$mess->setCustom($param_mess['custom']);
			}
		}
		return $mess;
	}

	//下发标签选中设备
	public function PushTags($param,$tagList = array()){
		if(empty($tagList)){
			log_message("下发标签选中设备失败,Taglist为空");
			throw new Exception("下发标签选中设备失败,Taglist为空", 1);
		}
		if(!is_array($tagList)){
			log_message("下发标签选中设备失败,Taglist必须为数组");
			throw new Exception("下发标签选中设备失败,Taglist必须为数组", 1);
		}
		$mess = $this->_combileData($param);
		$ret = $this->push->PushTags(0, $tagList, 'OR', $mess);
		return ($ret);
	}

	//查询消息推送状态
	public function QueryPushStatus($pushIdList = array()){
		$ret = $this->push->QueryPushStatus($pushIdList);
		return ($ret);
	}

	//查询设备数量
	public function QueryDeviceCount(){
		$ret = $this->push->QueryDeviceCount();
		return ($ret);
	}

	//查询标签
	public function QueryTags(){
		$ret = $this->push->QueryTags(0,100);
		return ($ret);
	}

	//查询某个tag下token的数量
	public function QueryTagTokenNum($tag){
		$ret = $push->QueryTagTokenNum("tag");
		return ($ret);
	}

	//查询某个token的标签
	public function QueryTokenTags($token){
		$ret = $this->push->QueryTokenTags($token);
		return ($ret);
	}

	//取消定时任务
	public function CancelTimingPush($taskid){
		$ret = $this->push->CancelTimingPush($taskid);
		return ($ret);
	}

	// 设置标签
	public function BatchSetTag($param) {
	    $pairs = $this->_packTagTokenPair($param);
	    $ret = $this->push->BatchSetTag($pairs);
	    return $ret;
	}

	// 删除标签
	public function BatchDelTag($param) {
	    $pairs = $this->_packTagTokenPair($param);
	    $ret = $this->push->BatchDelTag($pairs);
	    return $ret;
	}

	//获取tag和token的键值对
	private function _packTagTokenPair($param = array()){
		$pairs = array();
		foreach($param as $tag=>$token){
  			array_push($pairs, new TagTokenPair($tag,$token));
		}
	    return $pairs;
	}

	//批量推送到指定的用户账号(常用)
	public function PushAccountList($param = array(), $accountList = array()){
		$mess = $this->_packData($param);
		$accountList_p = array_chunk($accountList,80);//将目标对象分成小块，因为信鸽有单次最大推送用户限制
		foreach ($accountList_p as $aList) {
			$count = 0;
			do{
				$res = null;
				$res = $this->push->PushAccountList(0, $aList, $mess);
			}while( (($res==null) && (($count++)<3)) || (($res!=null) && ($res['ret_code'] != 0)) );
		}
		return $res;
	}


	// =============================业务方法开始======================================
	//学生提了一个问题要求老师回答，推送学生问题给老师
	public function PushAskToTeacher($qid = 0){
		//1.根据问题标号查出问题信息
		$sql = 'select aq.crid,aq.qid,aq.title,aq.tid from ebh_askquestions aq where qid = '.$qid.' limit 1';
		$qInfo = $this->db->query($sql)->row_array();
		if(empty($qInfo)){
			log_message("PushAskToTeacher：找不到qid为[".$qid."]的问题,推送失败");
			return false;
		}
		if(!$this->_check($qInfo['crid'])){
			return false;
		}
		if(empty($qInfo['tid'])){//无需推送
			return;
		}
		//2.数据包准备
		$custom = array('type'=>'8','id'=>$qid,'title'=>$qInfo['title']);
		$param = array(
			'mess'=>array(
				'title'=>"有人向您提问",
				'content'=>"有人向您提问",
				'type'=>"MESSAGE",
				'custom'=>$custom,
				'expire'=>86400
			),
			'action'=>array(),
			'style'=>array()
		);
		$accountList = array($qInfo['tid']);
		//3.推送开始
		$res = $this->PushAccountList($param,$accountList);
		if($res == null){
			if($this->count<3){
				$this->count++;
				$this->PushAskToTeacher($qid);
				return;
			}
		}
		//4.推送结果记入日志
		if( !empty($res) && ($res['ret_code']==0 ) ){
			log_message("PushAskToTeacher:成功,qid:".$qid);
		}else{
			log_message("PushAskToTeacher:失败，qid:".$qid);
		}
		return $res;
	}

	// 提问有人回答
	public function PushAskToStudent($qid = 0){
		//1.根据问题标号查出问题信息
		$sql = 'select aq.crid,aq.qid,aq.uid,aq.title from ebh_askquestions aq where qid = '.$qid.' limit 1';
		$qInfo = $this->db->query($sql)->row_array();
		if(empty($qInfo)){
			log_message("PushAskToStudent[".$qid."]的问题,推送失败，查询不到该问题信息");
			return false;
		}
		if(!$this->_check($qInfo['crid'])){
			return false;
		}
		if(empty($qInfo['uid'])){//无需推送
			return;
		}
		//2.数据包准备
		$custom = array('type'=>'4','id'=>$qid,'title'=>$qInfo['title']);
		$param = array(
			'mess'=>array(
				'title'=>"您的问题有人回答了",
				'content'=>"您的问题有人回答了",
				'type'=>"MESSAGE",
				'custom'=>$custom,
				'expire'=>86400
			),
			'action'=>array(),
			'style'=>array()
		);
		$accountList = array($qInfo['uid']);
		//3.推送开始
		$res = $this->PushAccountList($param,$accountList);
		if($res == null){
			if($this->count<3){
				$this->count++;
				$this->PushAskToStudent($qid);
				return;
			}
		}
		//4.推送结果记入日志
		if( !empty($res) && ($res['ret_code']==0 ) ){
			log_message("PushAskToStudent:成功,qid:".$qid);
		}else{
			log_message("PushAskToStudent:失败，qid:".$qid.'detail:'.var_export($res,true));
		}
		return $res;
	}
	

	//把教师新发布的课件推送给学生
	public function PushCourseToStudent($cwid = 0){
		//1.首先查出课件的信息
		$sql = 'select rc.cwid,rc.crid,rc.folderid,f.grade,f.district,cw.title,cw.uid from ebh_roomcourses rc join ebh_folders f on rc.folderid = f.folderid join ebh_coursewares cw on rc.cwid = cw.cwid where rc.cwid = '.$cwid.' limit 1';
		$cInfo = $this->db->query($sql)->row_array();
		if(empty($cInfo)){
			log_message("PushCourseToStudent[".$cwid."]的课件,推送失败，查询不到该课件信息");
			return false;
		}
		if(!$this->_check($cInfo['crid'])){
			return false;
		}
		//2.数据包准备
		$custom = array('type'=>'1','id'=>$cwid,'title'=>$cInfo['title']);
		$param = array(
			'mess'=>array(
				'title'=>"有最新课件发布了",
				'content'=>"有最新课件发布了",
				'type'=>"MESSAGE",
				'custom'=>$custom,
				'expire'=>86400
			),
			'action'=>array(),
			'style'=>array()
		);
		//3.根据课件信息判断出需要推送的用户列表
		//a.教师在该校所教的班级
		$sql_for_classinfo = 'select c.classid from ebh_classes  c join ebh_classteachers ct on c.classid = ct.classid where c.crid = '.$cInfo['crid'].' AND ct.uid = '.$cInfo['uid'].' AND c.grade = '.$cInfo['grade'];
		$classInfo = $this->db->query($sql_for_classinfo)->list_array();
		if(empty($classInfo)){
			log_message("PushCourseToStudent[".$cwid."]的课件,推送失败，查询不到发布教师所教的班级的年级和课件所属课程的所属年级一样的班级");
			return false;
		}
		$classid_in = $this->_getFieldArr($classInfo,'classid');
		//获取班级里的学生信息
		$sql_for_students = 'select cs.uid,cs.classid from ebh_classstudents cs where cs.classid in ('.implode(',', $classid_in).')';
		$studentList = $this->db->query($sql_for_students)->list_array();
		if(empty($studentList)){
			log_message("PushCourseToStudent[".$cwid."]的课件,推送失败，查询不到要推送的目标对象");
			return false;
		}
		$touid = $this->_getFieldArr($studentList,'uid');
		$accountList = $touid;
		//3.推送开始
		$res = $this->PushAccountList($param,$accountList);
		if($res == null){
			if($this->count<3){
				$this->count++;
				$this->PushCourseToStudent($cwid);
				return;
			}
		}
		//4.推送结果记入日志
		if( !empty($res) && ($res['ret_code']==0 ) ){
			log_message("PushCourseToStudent:成功,cwid:".$cwid);
		}else{
			log_message("PushCourseToStudent:失败,cwid:".$cwid);
		}
		return $res;
	}

	//把教师布置的作业推送给学生
	public function PushExamToStudent($eid = 0){
		//1.首先查出作业信息
		$sql_for_exam = 'select se.eid,se.crid,se.title,se.classid,se.grade,se.district from ebh_schexams se where se.eid = '.$eid;
		$eInfo = $this->db->query($sql_for_exam)->row_array();
		if(empty($eInfo)){
			log_message("PushExamToStudent[".$eid."]的作业,推送失败，查询不到该作业信息");
			return false;
		}
		if(!$this->_check($eInfo['crid'])){
			return false;
		}
		//2.数据包准备
		$custom = array('type'=>'2','id'=>$eid,'title'=>$eInfo['title']);
		$param = array(
			'mess'=>array(
				'title'=>"有最新作业发布了",
				'content'=>"有最新作业发布了",
				'type'=>"MESSAGE",
				'custom'=>$custom,
				'expire'=>86400
			),
			'action'=>array(),
			'style'=>array()
		);
		//2.根据作业信息判断出需要推送的用户列表
		if(!empty($eInfo['grade'])){
			$sql_for_classes = 'select classid from ebh_classes c where c.grade = '.$eInfo['grade'].' AND　c.district = '.$eInfo['district'];
			$classList = $this->db->query($sql_for_classes)->list_array();
			if(empty($classList)){
				log_message("PushExamToStudent[".$eid."]的作业,推送失败，查询改作业的班级信息");
				return false;
			}
			$classid_in = $this->_getFieldArr($classList,'classid');
		}else{
			$classid_in = array($eInfo['classid']);
		}
		//获取班级里的学生信息
		$sql_for_students = 'select cs.uid,cs.classid from ebh_classstudents cs where cs.classid in ('.implode(',', $classid_in).')';
		$studentList = $this->db->query($sql_for_students)->list_array();
		if(empty($studentList)){
			log_message("PushExamToStudent[".$eid."]的作业,推送失败，查询不到要推送的目标对象");
			return false;
		}
		$touid = $this->_getFieldArr($studentList,'uid');
		$accountList = $touid;
		//3.推送开始
		$res = $this->PushAccountList($param,$accountList);
		if($res == null){
			if($this->count<3){
				$this->count++;
				$this->PushExamToStudent($eid);
				return;
			}
		}
		//4.推送结果记入日志
		if( !empty($res) && ($res['ret_code']==0 ) ){
			log_message("PushExamToStudent:成功,eid:".$eid);
		}else{
			log_message("PushExamToStudent:失败,eid:".$eid);
		}
		return $res;

	}

	//推送通知给各种人
	public function PushNotice($noticeid = 0){
		//1.获取通知的详情，用来主要用来判断推送的对象
		$sql_for_notice = 'select n.noticeid,n.crid,n.title,n.ntype,n.cids,n.grades,n.districts from ebh_notices n where n.noticeid = '.$noticeid.' limit 1';
		$nInfo = $this->db->query($sql_for_notice)->row_array();
		if(empty($nInfo)){
			log_message("PushNotice[".$noticeid."]的通知,推送失败，查询不到该通知信息");
			return false;
		}
		if(!$this->_check($nInfo['crid'])){
			return false;
		}
		//2.数据包准备
		$custom = array('type'=>'16','id'=>$noticeid,'title'=>$nInfo['title']);
		$param = array(
			'mess'=>array(
				'title'=>"有最新通知发布了",
				'content'=>"有最新通知发布了",
				'type'=>"MESSAGE",
				'custom'=>$custom,
				'expire'=>86400
			),
			'action'=>array(),
			'style'=>array()
		);
		//3.发送对象获取
		//通知类型,1为全校师生 2为全校教师 3为全校学生 4为班级学生 5年级学生
		$touid = array();
		$crid = $nInfo['crid'];
		switch ($nInfo['ntype']) {
			case '1':
				$touid1 = $this->_getRoomTeachers($crid);
				$touid2 = $this->_getRoomStudents($crid);
				$touid = array_merge($touid1,$touid2);
				break;
			case '2':
				$touid = $this->_getRoomTeachers($crid);
				break;
			case '3':
				$touid = $this->_getRoomStudents($crid);
				break;
			case '4':
				$touid = $this->_getClassStudents($nInfo['cids']);
				break;
			case '5':
				$touid = $this->_getGradeStudents($nInfo['crid'],$nInfo['grades']);
				break;
			default:
				break;
		}
		$accountList = $touid;
		//3.推送开始
		$res = $this->PushAccountList($param,$accountList);
		if($res == null){
			if($this->count<3){
				$this->count++;
				$this->PushNotice($noticeid);
				return;
			}
		}
		//4.推送结果记入日志
		if( !empty($res) && ($res['ret_code']==0 ) ){
			log_message("PushNotice:成功,noticeid:".$noticeid);
		}else{
			log_message("PushNotice:失败,noticeid:".$noticeid);
		}
		return $res;
	}
	// =============================业务方法结束======================================

	

	// =============================辅助方法开始======================================
	/**
	 *获取全校教师
	 */
	private function _getRoomTeachers($crid = 0,$ifReturnUidArr = true){
		$sql = 'select rt.tid as uid from ebh_roomteachers rt where rt.status = 1 and rt.crid = '.$crid;
		$res = $this->db->query($sql)->list_array();
		if($ifReturnUidArr){
			$res = $this->_getFieldArr($res,'uid');
		}
		return $res;
	}
	/**
	 *获取全校学生
	 */
	private function _getRoomStudents($crid = 0,$ifReturnUidArr = true){
		$sql = 'select ru.uid from ebh_roomusers ru where ru.cstatus = 1 AND ru.crid = '.$crid;
		$res = $this->db->query($sql)->list_array();
		if($ifReturnUidArr){
			$res = $this->_getFieldArr($res,'uid');
		}
		return $res;
	}

	private function _getClassStudents($classids,$ifReturnUidArr = true){
		if(is_array($classids)){
			$classids = $classids;
		}else if(is_scalar($classids)){
			if(stripos($classids, ',') === false){
				$classids = array(intval($classids));
			}else{
				$classids = explode(',', $classids);
			}
		}
		$sql = 'select ct.uid from ebh_classstudents ct where ct.classid in ('.implode(',', $classids).')';
		$res = $this->db->query($sql)->list_array();
		if($ifReturnUidArr){
			$res = $this->_getFieldArr($res,'uid');
		}
		return $res;
	}

	private function _getGradeStudents($crid = 0,$grades){
		if(is_array($grades)){
			$grades = $grades;
		}else if(is_scalar($grades)){
			if(stripos($grades, ',') === false){
				$grades = array(intval($grades));
			}else{
				$grades = explode(',', $grades);
			}
		}
		$sql_for_classes = 'select * from ebh_classes c where c.crid = '.$crid.' AND c.grade in ('.implode(',', $grades).')';
		$classes = $this->db->query($sql_for_classes)->list_array();
		$classids = $this->_getFieldArr($classes,'classid');
		return $this->_getClassStudents($classids);
	}
	/**
	 *获取二维数组指定的字段集合
	 */
	private function _getFieldArr($param = array(),$filedName=''){
		
		$reuturnArr = array();

		if(empty($filedName)||empty($param)){
			return $reuturnArr;
		}

		foreach ($param as $value) {
			array_push($reuturnArr, $value[$filedName]);
		}

		return $reuturnArr;
	}
	// =============================辅助方法结束======================================
}