<?php
/**
 *sns基础信息类
 */
class BaseinfosModel extends CModel {
	private $snsdb = null;
	private $cache = null;
	public function __construct(){
		parent::__construct();
		$snsdb = Ebh::app()->getOtherDb("snsdb");
		$this->snsdb = $snsdb;
		$this->cache = Ebh::app()->getCache('cache_redis');
	}
	//添加一条
	public function addone($param){
		$setarr = array();
		if(!empty($param['uid'])){
			$setarr['uid'] = $param['uid'];
		}
		if(!empty($param['followsnum'])){
			$setarr['followsnum'] = $param['followsnum'];
		}
		if(!empty($param['fansnum'])){
			$setarr['fansnum'] = $param['fansnum'];
		}
		if(!empty($param['viewsnum'])){
			$setarr['viewsnum'] = $param['viewsnum'];
		}
		if(!empty($param['cover'])){
			$setarr['cover'] = $param['cover'];
		}
		if(isset($param['crids'])){
			$setarr['crids'] = $param['crids'];
		}
		return $this->snsdb->insert("ebh_sns_baseinfos",$setarr);
	}
	
	//修改一条
	public function updateone($param ,$uid, $sparam=array()){
		$setarr = array();
		if(!empty($param['uid'])){
			$setarr['uid'] = $param['uid'];
		}
		if(!empty($param['followsnum'])){
			$setarr['followsnum'] = $param['followsnum'];
		}
		if(!empty($param['fansnum'])){
			$setarr['fansnum'] = $param['fansnum'];
		}
		if(!empty($param['viewsnum'])){
			$setarr['viewsnum'] = $param['viewsnum'];
		}
		if(isset($param['cover'])){
			$setarr['cover'] = $param['cover'];
		}
		if(isset($param['crids'])){
			$setarr['crids'] = $param['crids'];
		}
		if(!empty($param['status'])){
			$setarr['status'] = $param['status'];
		}
		return $this->snsdb->update("ebh_sns_baseinfos",$setarr,array('uid'=>$uid),$sparam);
	}
	/**
	 * 获取用户基本信息
	 * 包含有,个人简介,头像,性别,关注,粉丝等
	 * 
	 */
	public function getuserinfo($users,$keys="uid"){	
		if(empty($users[0])) return false;
		$uidarr = array();
		foreach($users as $user){
			array_push($uidarr, $user[$keys]);
		}
		$sql = "select u.uid,u.username,u.balance,u.realname,u.nickname,u.sex,u.face,u.credit,u.groupid,m.hobbies,m.profile,u.mysign from ebh_users u left join ebh_members m on m.memberid = u.uid where u.uid in ( ".implode(",",$uidarr)." )";
		$infots = $this->db->query($sql)->list_array();
		$sql2 = "select uid,followsnum,fansnum,viewsnum,cover,crids,nzcount,npcount,ngcount,nfcount from ebh_sns_baseinfos where uid in(".implode(",",$uidarr).") ";
		$infos = $this->snsdb->query($sql2)->list_array();
		
		//网校数据、作业数、学习数、问题数分别从缓存中获取
		/*手机端暂时去掉网校数据、作业数、学习数、问题是数
		$cache = $this->cache;
		foreach ($infos as $k=>$v){
			$key = $v['uid'].'_nums';
			$usernum = $cache->hget($key);
			if(empty($usernum)){
				//判断下是学生用户还是教师用户
				$isteacher = false;
				foreach ($infots as $kk=>$val){
					if($val['uid'] == $v['uid'] && $val['groupid'] == 5){
						$isteacher = true;
						break;
					}
				}
				if($isteacher){
					//我的问题记录数
					$myaskcount = $this->getmyaskcount(array('uid'=>$v['uid'],'shield'=>0));
					//我布置的作业记录数
					$myexamcount = $this->getRoomTeacherListExamCount($v['uid']);
					//我的学习记录数
					$mystudycount = 0;
				}else{
					//我的问题记录数
					$myaskcount = $this->getmyaskcount(array('uid'=>$v['uid'],'shield'=>0));
					//我的作业记录数
					$myexamcount = $this->getExamListCountByMemberid(array('uid'=>$v['uid'],'hasanswer'=>1));
					//我的学习记录数
					$mystudycount = $this->getStudyCount(array('uid'=>$v['uid'],'totalflag'=>0));
				}
				$cache->hset($key,'homeworknum',$myexamcount);
				$cache->hset($key,'questionnum',$myaskcount);
				$cache->hset($key,'studynum',$mystudycount);
			}else{
				$myaskcount = $usernum['questionnum'];
				$myexamcount = $usernum['homeworknum'];
				$mystudycount = $usernum['studynum'];
			}
			//网校信息
			if(!empty($v['crids'])){
				$cridarr = explode(',', $v['crids']);
				foreach ($cridarr as $crid){
					$roomkey = 'room_info_'.$crid;
					$room = $cache->hget($roomkey);
					//没有缓存直接从数据库里查询
					if(empty($room)){
						$room = $this->getroominfo($crid);
						if(!empty($room)){
							$cache->hMset($roomkey,$room);
						}
					}
					$room['crid'] = $crid;
					$roomarr[] = $room;
				}
				$infos[$k]['roomarr'] = $roomarr;
				unset($roomarr);
			}
			$infos[$k]['questionnum'] = $myaskcount;
			$infos[$k]['homeworknum'] = $myexamcount;
			$infos[$k]['studynum'] = $mystudycount;
		}
		*/
		foreach($users as $key=>&$user){
			//组装ebh库信息
			foreach($infots as $infot){
				if(!empty($infot)&&$infot['uid']==$user[$keys]){
					unset($infot['uid']);
					$user = array_merge($user,$infot);
					break;
				}else{
					continue;
				}
			}
			//组装sns的信息
			foreach($infos as $info){
				if(!empty($info)&&$info['uid']==$user[$keys]){
					unset($info['uid']);
					$user = array_merge($user,$info);
					break;
				}else{
					continue;
				}
			}
		}
		return $users;		
	}
	
	
	/**
	 * 获取用户的班级id
	 */
	public function getuserclassid($uid){
		$sql = "select utype from ebh_sns_baseinfos where uid = $uid";
		$row = $this->snsdb->query($sql)->row_array();
		if($row['utype'] == 5){
			$sql = "select classid from ebh_classteachers where uid = $uid";
		}else{
			$sql = "select classid from ebh_classstudents where uid = $uid";
		}
		$classes = $this->db->query($sql)->list_array();
		$retarr = array();
		if(!empty($classes[0])){
			$retarr = array_map(function($arr){return $arr['classid'];}, $classes);
		}
		return $retarr; 
	}
	
	/**
	 * 获取用户的网校id
	 */
	public function getusercrid($uid){
		//获取
		$sql = "select crids from ebh_sns_baseinfos where uid = $uid ";
		$crids = $this->snsdb->query($sql)->row_array();
		if(!empty($crids)){
			return explode(",", $crids['crids']);
		}
		return false;
	}
	
	/**
	 * 获取网校域名与名称
	 */
	public function getroominfo($crid){
		$sql = "select crname, domain, cface from ebh_classrooms where crid = $crid";
		return $this->db->query($sql)->row_array();
	}
	
	/**
	 * 根据编号获取问题记录数
	 * @param type $param
	 * @return type
	 */
	public function getmyaskcount($param) {
		$count = 0;
		$sql = 'select count( distinct q.qid) count from ebh_askquestions q join ebh_users u on (q.uid = u.uid)';
		$orarr = $wherearr = array();
		if (!empty($param['crid']))
			$wherearr[] = 'q.crid=' . $param['crid'];
		if (!empty($param['uid']))
			$wherearr[] = 'q.uid=' . $param['uid'];
		if (isset($param['folderid']))
			$wherearr[] = 'q.folderid=' . $param['folderid'];
		if(isset($param['shield'])){
			$wherearr[] = 'q.shield =' . $param['shield'];
		}
		if (!empty($param['q']))
			$wherearr[] = '(q.title like \'%' . $this->db->escape_str($param['q']) . '%\' or u.username like \'%' . $this->db->escape_str($param['q']) . '%\')';
		if (!empty($param['aq']))
			$wherearr[] = '(u.username =\'' . $param['aq'] .'\' or u.realname =\'' . $param['aq']. '\')';
		if (!empty($wherearr))
			$sql.= ' WHERE (' . implode(' AND ', $wherearr) . ')';
		if(!empty($param['qids']))
			$orarr[] = ' OR q.qid IN ('.implode(',',$param['qids']).') ';
		if(!empty($orarr))
			$sql.= implode(' ',$orarr);
		$countrow = $this->db->query($sql)->row_array();
		if (!empty($countrow) && !empty($countrow['count']))
			$count = $countrow['count'];
		return $count;
	}
	
	/*
	 教师布置作业数
	@param int $tid
	*/
	public function getRoomTeacherListExamCount($tid){
		$sql = 'SELECT count(*) as count from ebh_schexams se where se.uid = '.$tid;
		$row = $this->db->query($sql)->row_array();
		$count = $row['count'];
		
		return $count;
	}
	
	/**
	*根据学生编号获取学校学生所在班级下的作业记录总数
	*/
	public function getExamListCountByMemberid($param) {
		$count = 0;
		if(empty($param['uid']))
			return $count;
		$sql = 'SELECT count(*) count from ebh_schexams e '.
				'LEFT JOIN ebh_schexamanswers a on (e.eid = a.eid AND a.uid='.$param['uid'].') '.
				'JOIN ebh_users u on (u.uid = e.uid) ';
		$wherearr = array();
		if(!empty($param['crid']))
			$wherearr[] = 'e.crid='.$param['crid'];
		if(!empty($param['classid'])) {	
			if(!empty($param['grade'])) {	// 根据年级过滤，一般在布置作业到年级时有效
				if(isset($param['district'])) {	// 根据校区过滤，一般在布置作业到年级时有效
					$wherearr[] = '(e.classid = '.$param['classid']. ' or e.grade = '.$param['grade'].' and e.district = '.$param['district'].')';
				} else {
					$wherearr[] = '(e.classid = '.$param['classid']. ' or e.grade = '.$param['grade'].')';
				}
			} else {
				$wherearr[] = 'e.classid='.$param['classid'];
			}
		}
		$wherearr[] = 'e.status = 1';
		if(!empty($param['tid'])){
			$wherearr[] = 'e.uid = '.$param['tid'];
		}
		if(isset($param['filteranswer']))	//过滤学生是否已经答题了，此处传值表示只显示学生未答的
			$wherearr[] = 'a.aid IS NULL';
		if(isset($param['hasanswer']))	//过滤学生是否已经答题了，此处传值表示只显示学生已答的
			$wherearr[] = 'a.aid IS NOT NULL';
		if(isset($param['subtime'])) {	// 根据时间获取记录数
			$wherearr[] = 'e.dateline > '.$param['subtime'];
		}
		if(!empty($param['q']))	//按作业标题搜索
			$wherearr[] = 'title like \'%'.$this->db->escape_str($param['q']).'%\'';
		if(!empty($param['abegindate'])) {	//答题开始时间
			$wherearr[] = 'a.dateline>='.$param['abegindate'];
		}
		if(!empty($param['aenddate'])) {	//答题完成时间
			$wherearr[] = 'a.dateline<'.$param['aenddate'];
		}
		if(!empty($param['ebegindate'])) {	//布置时间从
			$wherearr[] = 'e.dateline>='.$param['ebegindate'];
		}
		if(!empty($param['eenddate'])) {	//布置时间到
			$wherearr[] = 'e.dateline<'.$param['eenddate'];
		}
		if(isset($param['astatus'])) {	// 草稿箱状态，0为答题草稿箱 1为已提交
			$wherearr[] = 'a.status = '.$param['astatus'];
		}
		$sql .= ' WHERE '.implode(' AND ',$wherearr);
		//echo $sql;
		$row = $this->db->query($sql)->row_array();
		if(!empty($row)) 
			$count = $row['count'];
		return $count;
	}
	
	/**
	 * 根据参数获取对应的学习记录条数
	 * @param array $param
	 * @return int
	 */
	public function getStudyCount($param){
		$count = 0;
		$sql = 'select count(*) count from ebh_playlogs p '.
				'join ebh_coursewares c on (p.cwid = c.cwid) '.
				'join ebh_roomcourses rc on (rc.cwid = p.cwid) ';
		$wherearr = array();
		if(!empty($param['uid']))
			$wherearr[] = 'p.uid='.$param['uid'];
		if(!empty($param['crid']))
			$wherearr[] = 'rc.crid='.$param['crid'];
		if(!empty($param['startDate']))
			$wherearr[] = 'p.lastdate>='.$param['startDate'];
		if(!empty($param['endDate']))
			$wherearr[] = 'p.lastdate<'.$param['endDate'];
		if(!empty($param['q'])){
			$wherearr[] = ' c.title like \'%'.$param['q'].'%\'';
		}
		if(isset($param['totalflag'])){
			$wherearr[] = 'p.totalflag in ('.$param['totalflag'].')';
		}else{
			$wherearr[] = 'p.totalflag=1';
		}
		if(!empty($param['folderid'])){
			$wherearr[] = 'rc.folderid = '.$param['folderid'];
		}
		if(!empty($wherearr)){
			$sql.=' WHERE '.implode(' AND ',$wherearr);
		}
		$row = $this->db->query($sql)->row_array();
		if(!empty($row))
			$count = $row['count'];
		return $count;
	}
	
	/**
	 * 权限验证
	 * param uid登陆系统用户id
	 * param fromuid被验证用户id
	 * param type权限类型  0所有人 1,我的好友 2,班级师生 3,全校师生 4,仅自己
	 */
	public function checkpermission($uid,$fromuid,$type){
		if($type == 0){
			return true;
		}else if($type == 4){
			return false;
		}else if($type == 1){
			//检测是否是我的好友
			$followModel = $this->model('Follow');
			$bool = $followModel->isfriend($uid,$fromuid);
			return $bool;
		}else if($type == 2){
			//班级检测	
			$sql = 'select classid from ebh_classstudents where uid ='.$uid;
			$myclassids = $this->db->query($sql)->list_array();
			foreach ($myclassids as $cid){
				$myclassroom[] = $cid['classid'];
			}
			$sql = 'select classid from ebh_classstudents where uid ='.$fromuid;
			$fromclassids = $this->db->query($sql)->list_array();
			foreach ($fromclassids as $cid){
				$fromclassroom[] = $cid['classid'];
			}
			$intersection = array_intersect($myclassroom,$fromclassroom);
			$bool = !empty($intersection) ? true : false;
			return $bool;
		}else if($type == 3){
			//学校检测
			$users = $this->getuserinfo(array(array('uid'=>$uid),array('uid'=>$fromuid)));
			$mycrids = $users[0]['crids'];
			$fromcrids = $users[1]['crids'];
			$_mycrids = !empty($mycrids) ? explode(',', $mycrids) : array();
			$_fromcrids = !empty($fromcrids) ? explode(',', $fromcrids) : array();
			$intersection = array_intersect($_mycrids, $_fromcrids);
			$bool = !empty($intersection) ? true : false;
			return $bool;
		}else{
			return false;
		}
	}
	
	/**
	 *根据学生编号获取学校学生所在班级下的作业
	 */
	public function getExamListByMemberid($param) {
		if(empty($param['uid']))
			return FALSE;
		$sql = 'SELECT e.eid,e.crid,e.title,e.dateline,e.score,e.answercount,e.limitedtime,e.folderid,u.uid,u.username,u.face,u.sex,u.realname,u.groupid,a.aid,a.status astatus,a.dateline adateline,a.completetime,a.totalscore from ebh_schexams e '.
				'LEFT JOIN ebh_schexamanswers a on (e.eid = a.eid AND a.uid='.$param['uid'].') '.
				'JOIN ebh_users u on (u.uid = e.uid) ';
		$wherearr = array();
		if(!empty($param['crid']))
			$wherearr[] = 'e.crid='.$param['crid'];
		if(!empty($param['classid'])) {
			if(!empty($param['grade'])) {	// 根据年级过滤，一般在布置作业到年级时有效
				if(isset($param['district'])) {	// 根据校区过滤，一般在布置作业到年级时有效
					$wherearr[] = '(e.classid = '.$param['classid']. ' or e.grade = '.$param['grade'].' and e.district = '.$param['district'].')';
				} else {
					$wherearr[] = '(e.classid = '.$param['classid']. ' or e.grade = '.$param['grade'].')';
				}
			} else {
				$wherearr[] = 'e.classid='.$param['classid'];
			}
		}
		$wherearr[] = 'e.status = 1';
		if(!empty($param['tid'])){
			$wherearr[] = 'e.uid = '.$param['tid'];
		}
		if(isset($param['filteranswer']))	//过滤学生是否已经答题了，此处传值表示只显示学生未答的
			$wherearr[] = 'a.aid IS NULL';
		if(isset($param['hasanswer']))	//过滤学生是否已经答题了，此处传值表示只显示学生已答的
			$wherearr[] = 'a.aid IS NOT NULL';
		if(isset($param['subtime'])) {	// 根据时间获取记录数
			$wherearr[] = 'e.dateline > '.$param['subtime'];
		}
		if(!empty($param['q']))	//按作业标题搜索
			$wherearr[] = 'title like \'%'.$this->db->escape_str($param['q']).'%\'';
		if(!empty($param['abegindate'])) {	//答题开始时间
			$wherearr[] = 'a.dateline>='.$param['abegindate'];
		}
		if(!empty($param['aenddate'])) {	//答题完成时间
			$wherearr[] = 'a.dateline<'.$param['aenddate'];
		}
		if(!empty($param['ebegindate'])) {	//布置时间从
			$wherearr[] = 'e.dateline>='.$param['ebegindate'];
		}
		if(!empty($param['eenddate'])) {	//布置时间到
			$wherearr[] = 'e.dateline<'.$param['eenddate'];
		}
		if(isset($param['astatus'])) {	// 草稿箱状态，0为答题草稿箱 1为已提交
			$wherearr[] = 'a.status = '.$param['astatus'];
		}
		$sql .= ' WHERE '.implode(' AND ',$wherearr);
		if(!empty($param['order']))
			$sql .= ' ORDER BY '.$param['order'];
		else
			$sql .= ' ORDER BY e.eid DESC';
		if(!empty($param['limit']))
			$sql .= ' limit '.$param['limit'];
		else {
			if (empty($param['page']) || $param['page'] < 1)
				$page = 1;
			else
				$page = $param['page'];
			$pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
			$start = ($page - 1) * $pagesize;
			$sql .= ' limit ' . $start . ',' . $pagesize;
		}
		return $this->db->query($sql)->list_array();
	}
	
	/**
	 * 获取当前uid下的所有问题列表包括已回答的问题
	 * @param type $param
	 * @return type
	 */
	public function getmyasklist($param) {
		if (empty($param['page']) || $param['page'] < 1)
			$page = 1;
		else
			$page = $param['page'];
		$pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
		$start = ($page - 1) * $pagesize;
		$sql = 'select distinct q.qid,q.crid,q.uid,q.folderid,q.title,q.answercount,q.hasbest,q.dateline,q.catpath,u.uid,u.sex,u.face,u.username,u.realname,f.foldername,q.status,q.message,q.shield,u.groupid from ebh_askquestions q join ebh_users u on (q.uid = u.uid) left join ebh_folders f on (f.folderid = q.folderid) WHERE 1 AND (';
		$wherearr = array();
		if (!empty($param['crid']))
			$wherearr[] = 'q.crid=' . $param['crid'];
		if (!empty($param['uid']))
			$wherearr[] = 'q.uid=' . $param['uid'];
		if (isset($param['folderid']))
			$wherearr[] = 'q.folderid=' . $param['folderid'];
		if (!empty($param['aq']))
			$wherearr[] = '(u.username =\'' . $param['aq'] .'\' or u.realname =\'' . $param['aq']. '\')';
		if(isset($param['shield'])){
			$wherearr[] = 'q.shield =' . $param['shield'];
		}
		if(!empty($param['qids'])){
			$orarr[] = ' OR q.qid IN ('.implode(',',$param['qids']).')';
		}
		if (!empty($wherearr))
			$sql.= ' (' . implode(' AND ', $wherearr).' ) ';
		if(!empty($orarr))
			$sql.= implode(' ', $orarr);
		$sql .= ')';
		if (!empty($param['q']))
			$sql .= ' AND (q.title like \'%' . $this->db->escape_str($param['q']) . '%\')';
		if (!empty($param['order'])) {
			$sql .= ' order by ' . $param['order'];
		} else {
			$sql .= ' order by q.qid desc ';
		}
		$sql .= ' limit ' . $start . ',' . $pagesize;
		return $this->db->query($sql)->list_array();
	}
	/**
	 * 根据参数获取对应的学习记录列表
	 * @param array $param
	 * @return array
	 */
	public function getPlayList($param=array()){
		$sql = 'select p.logid,p.cwid,p.ctime,p.ltime,p.startdate,p.lastdate,c.title,c.cwurl,c.ism3u8 from ebh_playlogs p '.
				'join ebh_coursewares c on (p.cwid = c.cwid) '.
				'join ebh_roomcourses rc on (rc.cwid = p.cwid) ';
		$wherearr = array();
		if(!empty($param['uid']))
			$wherearr[] = 'p.uid='.$param['uid'];
		if(!empty($param['crid']))
			$wherearr[] = 'rc.crid='.$param['crid'];
		if(!empty($param['startDate']))
			$wherearr[] = 'p.lastdate>='.$param['startDate'];
		if(!empty($param['endDate']))
			$wherearr[] = 'p.lastdate<'.$param['endDate'];
		if(!empty($param['q'])){
			$wherearr[] = ' c.title like \'%'.$param['q'].'%\'';
		}
		if(isset($param['totalflag'])){
			$wherearr[] = 'p.totalflag in ('.$param['totalflag'].')';
		}else{
			$wherearr[] = 'p.totalflag=1';
		}
		if(!empty($param['folderid'])){
			$wherearr[] = 'rc.folderid = '.$param['folderid'];
		}
		if(!empty($wherearr)){
			$sql.=' WHERE '.implode(' AND ',$wherearr);
		}
		if(!empty($param['order'])){
			$sql.=' order by '.$param['order'];
		}else{
			$sql.=' order by p.lastdate desc ';
		}
		if(!empty($param['limit'])){
			$sql.= ' limit '.$param['limit'];
		}else{
			if (empty($param['page']) || $param['page'] < 1)
				$page = 1;
			else
				$page = $param['page'];
			$pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
			$start = ($page - 1) * $pagesize;
			$sql .= ' limit ' . $start . ',' . $pagesize;
		}
		return $this->db->query($sql)->list_array();
	}
	/**
	 * 获取已回答的问题的qid
	 * @param type $param
	 * @return type
	 */
	public function getaskanswersqids($param){
		$wherearr = array();
		if (!empty($param['uid']))
			$wherearr[] = 'a.uid=' . $param['uid'];
		$sql = 'SELECT a.qid FROM ebh_askanswers a ' .
				'LEFT JOIN ebh_users u ON (u.uid = a.uid) ';
		if(!empty($wherearr)){
			$sql .= ' WHERE ' . implode(' AND ',$wherearr);
		}
		$qids = $this->db->query($sql)->list_array();
		//去重
		if(count($qids)>0){
			foreach($qids as $k=>$v){
				$qidarr[] = $v['qid'];
			}
		}
		$qidarr = !empty($qidarr) ? array_unique($qidarr) : array();
		return $qidarr;
	}
	
	/**
	 * 获取课件详情及网校name及课件数coursenum
	 * @param int $cwid
	 * @return array
	 */
	public function getcoursedetails($cwid) {
		$sql = 'select c.cwid,c.uid,c.title,c.tag,c.logo,c.images,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,c.dateline,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,f.foldername,cr.crname,cr.coursenum,cr.domain,cr.viewnum,c.cwurl,c.viewnum,f.coursewarenum,cr.domain ' .
				'from ebh_coursewares c ' .
				'join ebh_roomcourses rc on (c.cwid = rc.cwid) ' .
				'join ebh_users u on (u.uid = c.uid) ' .
				'join ebh_folders f on (f.folderid = rc.folderid) ' .
				'join ebh_classrooms cr on (cr.crid = rc.crid) ' .
				'where c.cwid=' . $cwid;
		return $this->db->query($sql)->row_array();
	}
	
	/**
	 *根据教室和班级编号获取班级下的作业列表(学校版本)
	 */
	public function getschexamlist($param) {
		if(empty($param['crid']))
			return FALSE;
		$sql = 'SELECT e.eid,e.title,e.crid,e.grade,e.limitedtime,e.score,e.dateline,e.status,e.answercount,e.quescount,e.cwid,e.classid,e.district,c.classname,c.stunum FROM ebh_schexams e left join ebh_classes c on e.classid=c.classid';
		
		$wherearr = array();
		$wherearr[] = 'e.crid in ('.$param['crid'].')';
		$isgrade = FALSE;
		if(!empty($param['classid'])) {
			if(!empty($param['grade']) && !empty($param['uid'])) {	//按照年级和科目获取作业
				$district = empty($param['district']) ? 0 : $param['district'];
				$fid_in = $this->getFoldersByGrade($param['crid'],$param['grade'],$param['uid'],$district);
				if(empty($fid_in)) {
					$wherearr[] = 'e.classid='.$param['classid'];
				} else {
					$isgrade = TRUE;
					$wherearr[] = '((e.classid='.$param['classid'].' and e.grade = 0) or (e.folderid in'.$fid_in.' and e.grade='.$param['grade'].' and e.district='.$param['district'].'))';
				}
			} else {
				$wherearr[] = 'e.classid='.$param['classid'];
			}
		}
		if(!empty($param['uid']) && !$isgrade)	//过滤某个教师布置的班级作业
			$wherearr[] = 'e.uid='.$param['uid'];
		if(!empty($param['tid'])){
			$wherearr[] = 'e.uid='.$param['tid'];
		}
		if(isset($param['status']))
			$wherearr[] = 'e.status = '.$param['status'];
		else
			$wherearr[] = 'e.status in (0,1)';
		if(!empty($param['starttime'])){
			$wherearr[] = 'e.dateline >= '.$param['starttime'];
		}
		if(!empty($param['endtime'])){
			$wherearr[] = 'e.dateline <= '.($param['endtime']+86400);
		}
		if(!empty($param['q'])){
			$wherearr[] = 'e.title like \'%'.$this->db->escape_str($param['q']).'%\'';
		}
		$sql .= ' WHERE '.implode(' AND ',$wherearr);
		if(!empty($param['order']))
			$sql .= ' order by '.$param['order'];
		else
			$sql .= ' order by e.eid desc ';
		if(!empty($param['limit']))
			$sql .= ' limit '.$param['limit'];
		else {
			if (empty($param['page']) || $param['page'] < 1)
				$page = 1;
			else
				$page = $param['page'];
			$pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
			$start = ($page - 1) * $pagesize;
			$sql .= ' limit ' . $start . ',' . $pagesize;
		}
		$examlist = $this->db->query($sql)->list_array();
		$myexamlist = array();
		$eidlist = array();
		foreach($examlist as $myexam) {
			if(!empty($param['classid'])&& !empty($myexam['grade']))
				$myexam['answercount'] = 0;
			$myexamlist[$myexam['eid']] = $myexam;
			if(!empty($myexam['grade'])) {
				$eidlist[] = $myexam['eid'];
			}
		}
		if(!empty($eidlist) && !empty($param['classid'])) {	//年级作业的答题数需要调整
			$usql = 'select uid from ebh_classstudents where classid='.$param['classid'];
			$ulist = $this->db->query($usql)->list_array();
			$uids = '';
			foreach($ulist as $urow) {
				if(empty($uids)) {
					$uids = $urow['uid'];
				} else {
					$uids .= ','.$urow['uid'];
				}
			}
			if(!empty($uids)) {
				$csql = 'select eid,count(*) count from ebh_schexamanswers where eid in ('.implode(',',$eidlist).') and uid in ('.$uids.') group by eid';
				$countlist = $this->db->query($csql)->list_array();
				if(!empty($countlist)) {
					foreach($countlist as $mycount) {
						if(isset($myexamlist[$mycount['eid']])) {
							$myexamlist[$mycount['eid']]['answercount'] = $mycount['count'];
						}
					}
				}
			}
		}
		return $myexamlist;
		
	}
	/**
	 *根据教室和班级编号获取班级下的作业列表记录总数(学校版本)
	 */
	public function getschexamlistcount($param) {
		$count = 0;
		if(empty($param['crid']))
			return $count;
		$sql = 'SELECT count(*) count FROM ebh_schexams e ';
		$wherearr = array();
		$wherearr[] = 'e.crid in ('.$param['crid'].')';
		$isgrade = FALSE;
		if(!empty($param['classid'])) {
			if(!empty($param['grade']) && !empty($param['uid'])) {	//按照年级和科目获取作业
				$district = empty($param['district']) ? 0 : $param['district'];
				$fid_in = $this->getFoldersByGrade($param['crid'],$param['grade'],$param['uid'],$district);
				if(empty($fid_in)) {
					$wherearr[] = 'e.classid='.$param['classid'];
				} else {
					$isgrade = TRUE;
					$wherearr[] = '(e.classid='.$param['classid'].' or (e.folderid in'.$fid_in.' and e.grade='.$param['grade'].' and e.district='.$param['district'].'))';
				}
			} else {
				$wherearr[] = 'e.classid='.$param['classid'];
			}
		}
		if(!empty($param['uid']) && !$isgrade)	//过滤某个教师布置的班级作业
			$wherearr[] = 'e.uid='.$param['uid'];
		if(!empty($param['tid'])){
			$wherearr[] = 'e.uid='.$param['tid'];
		}
		if(isset($param['status']))
			$wherearr[] = 'e.status = '.$param['status'];
		else
			$wherearr[] = 'e.status in (0,1)';
		if(!empty($param['starttime'])){
			$wherearr[] = 'e.dateline >= '.$param['starttime'];
		}
		if(!empty($param['endtime'])){
			$wherearr[] = 'e.dateline <= '.($param['endtime']+86400);
		}
		if(!empty($param['q'])){
			$wherearr[] = 'e.title like \'%'.$this->db->escape_str($param['q']).'%\'';
		}
		$sql .= ' WHERE '.implode(' AND ',$wherearr);
		$row = $this->db->query($sql)->row_array();
		if(!empty($row))
			$count = $row['count'];
		return $count;
	}
	public function getFoldersByGrade($crid,$grade,$uid,$district) {
		$sql = "select f.folderid,f.foldername from ebh_teacherfolders tf join ebh_folders f ".
				"ON(tf.folderid = f.folderid) ".
				"where f.crid=$crid and tf.tid=$uid and f.grade=$grade and f.district=$district";
		$foldersList = $this->db->query($sql)->list_array();
		if(!empty($foldersList)){
			foreach ($foldersList as $folder) {
				$fids[] = $folder['folderid'];
			}
			return '('.implode(',', $fids).')';
		}else{
			return '(0)';
		}
	}
	//根据type和对应id获取学业记录详情
	public function fetchschwork($param){
		$type = $param['type'];
		if($type == 1){
			if($param['groupid'] == 5){
				$sql = 'SELECT e.eid,e.title,e.crid,e.grade,e.limitedtime,e.score,e.dateline,e.status,e.answercount,e.quescount,e.cwid,e.classid,e.district FROM ebh_schexams e where e.eid = '.$param['toid'];
			}else{
				$sql = 'SELECT e.eid,e.crid,e.title,e.dateline,e.score,e.answercount,e.limitedtime,e.folderid,u.groupid,u.realname,a.aid,a.status astatus,a.dateline adateline,a.completetime,a.totalscore from ebh_schexams e '.
						'LEFT JOIN ebh_schexamanswers a on (e.eid = a.eid AND a.uid='.$param['uid'].') '.
						'JOIN ebh_users u on (u.uid = e.uid) where e.eid = '.$param['toid'];
			}
			$rows = $this->db->query($sql)->list_array();
		}else if($type == 2){
			$sql = 'select distinct q.qid,q.crid,q.uid,q.folderid,q.title,q.answercount,q.hasbest,q.dateline,q.catpath,u.uid,u.sex,u.face,u.username,u.realname,f.foldername,q.status,q.message,q.shield,u.groupid from ebh_askquestions q join ebh_users u on (q.uid = u.uid) left join ebh_folders f on (f.folderid = q.folderid) '.
					'WHERE q.qid ='.$param['toid'].' and q.uid = '.$param['uid'];
			$rows = $this->db->query($sql)->list_array();
		}else if($type == 3){
			if($param['groupid'] == 5){
				return false;
			}
			$sql = 'select p.logid,p.cwid,p.ctime,p.ltime,p.startdate,p.lastdate,c.title,c.cwurl,c.ism3u8 from ebh_playlogs p '.
				'join ebh_coursewares c on (p.cwid = c.cwid) '.
				'join ebh_roomcourses rc on (rc.cwid = p.cwid) '.
				'where p.logid = '.$param['toid'].' and p.uid = '.$param['uid'];
			$rows = $this->db->query($sql)->list_array();
			//获取文件夹名
			$tmp = $this->getcoursedetails($rows[0]['cwid']);
			$rows[0]['foldername'] = $tmp['foldername'];
		}
		return $rows[0];
	}
	//组装学业信息
	public function ftschworkd($groupid,$type,$data){
		$msg = array();
		if($type == 1){
			if($groupid == 5){
				$msg['title'] = shortstr($data['title'],70);
				$msg['contents'] = '出题时间：'.date('Y-m-d H:i:s',$data['dateline']).'　分数：'.$data['score'].'　答题人数：'.$data['answercount'];
			}else{
				$adateline = empty($data['adateline']) ? '暂无时间' : date('y-m-d H:i',$data['adateline']);
				$msg['title'] = $data['title'];
				$msg['contents'] = '答题时间：'.$adateline.'　用时：'.ceil($data['completetime']/60).'分钟　出题老师：'.shortstr($data['realname'],7)
				.'　出题时间：'.date('y-m-d H:i',$data['dateline']).'　总分/得分：'.$data['score'].'/'.round($data['totalscore'],2)
				.' 　已答人数：'.$data['answercount'];
			}
		}else if($type == 2){
			$msg['title'] = shortstr($data['title'],70);
			$msg['contents'] = '提问时间 ：'.date('Y-m-d H:i:s',$data['dateline']).' 回答数：'.$data['answercount'].' 所属学科：'.$data['foldername'];
			$msg['contents'] .= '<p>'.$data['message'].'</p>';
		}else if($type == 3){
			if($groupid == 6){
				$msg['title'] = shortstr($data['foldername'],70);
				$msg['contents'] = '课件名称：'.shortstr($data['title'],50).'　课件时长：'.$this->getltimestr($data['ctime'])
				.'　持续时间：'.$this->getltimestr($data['ltime']).'　首次时间：'.date('Y-m-d H:i:s',$data['startdate'])
				.'　末次时间：'.date('Y-m-d H:i:s',$data['lastdate']);
			}
		}
		return $msg;
	}
	//时长秒转换成字符显示
	private function getltimestr($ltime) {
		if(empty($ltime))
			return '';
		$h = intval($ltime / 3600);
		$m = intval(($ltime - $h * 3600)/60);
		$s = $ltime -$h * 3600 - $m*60;
		$str = $h.':'.str_pad($m,2,'0',STR_PAD_LEFT).':'.str_pad($s,2,'0',STR_PAD_LEFT);
	
		return $str;
	}
}