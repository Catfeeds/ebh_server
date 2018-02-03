<?php

/**
 * 课件列表控制器
 */
class ClistController extends CControl {
    public function index() {
		$courselist = array();
		$user = Ebh::app()->user->getloginuser();
		$folderid = $this->input->post('fid');
		$crid = $this->input->post('rid');
		if(empty($folderid) || !is_numeric($folderid)){
			echo json_encode(array());
			exit;
		}
		
		//课程增加人气
		Ebh::app()->lib('Viewnum')->addViewnum('folder',$folderid);

		if( empty($crid) ){
			//根据课程获取crid
			$folderInfo = $this->model('folder')->getfolderbyid($folderid);
			$crid = !empty($folderInfo)?$folderInfo['crid']:0;
			if(empty($crid)){
				echo json_encode(array());
				exit;
			}
		}
		$mycourselist = array();
		if(!empty($user) && is_numeric($folderid) && $folderid > 0) {
			$coursemodel = $this->model('Courseware');
			$queryarr = array();
			$page = $this->input->post('page');
			if(empty($page) || !is_numeric($page)) {
				$page = 1;
			}
			$queryarr['page'] = $page;
			$queryarr['folderid'] = $folderid;
			$pagesize = 100;
			$queryarr['pagesize'] = $pagesize;
			$queryarr['status'] = 1;
			$courselist = $coursemodel->getfolderseccourselist($queryarr);
			if(!empty($courselist)){
				foreach ($courselist as &$course) {
					$course['csid'] = $course['sid'];
					unset($course['sid']);
				}
				$courselist = EBH::app()->lib('UserUtil')->setFaceSize('50_50')->init($courselist,array('uid'),true);
				$courselist = Ebh::app()->lib('PowerUtil')->setCrid($crid)->init($courselist,$user['uid'])->insertPower();
			}
			formatDate($courselist,array('dateline'),array('dateline'));
			$sectionlist = array();
			
			foreach($courselist as $course) {
				if(empty($course['csid'])) {
					$course['csid'] = 0;
					$course['sname'] = '其他';
				}
				if($course['ism3u8'] == 1){
					$filetype = 'flv';
				}else{
					$cwurl = $course['cwurl'];
					$filetype = substr($cwurl, strpos($cwurl, '.') + 1); //文件类型
				}
				unset($course['ism3u8']);
				if(!isset($sectionlist[$course['csid']])) {
					$sectionlist[$course['csid']] = $course['sname'];
					$mycourselist[] = array('id'=>0,'name'=>$course['sname'],'filetype'=>'','username'=>'','dateline'=>'','face'=>'','itemid'=>0,'sid'=>0,'submitat'=>0,'endat'=>0);
				}
				$username = $course['uid_name'];
				$dateline = $course['dateline']; 	
//				if($filetype == 'ebhp')
//					continue;
				$mycourselist[] = array('id'=>$course['id'],'name'=>$course['name'],'filetype'=>$filetype,'username'=>$username,'dateline'=>$dateline,'face'=>$course['uid_face'],'itemid'=>$course['itemid'],'sid'=>$course['sid'],'submitat'=>$course['submitat'],'endat'=>$course['endat']);

			}
		}
		echo json_encode($mycourselist);
	}

	/*
	最新课程
	*/
	public function newcourse(){
		$crid = $this->input->post('rid');
		$roominfo = $this->getRoomInfo($crid);
		$cwmodel = $this->model('courseware');
		$user = Ebh::app()->user->getloginuser();
		//开通课程的id
		if($roominfo['isschool']==7){
			$userpermodel = $this->model('Userpermission');
			$myperparam = array('uid'=>$user['uid'],'crid'=>$roominfo['crid']);
			$myfolderlist = $userpermodel->getUserPayFolderList($myperparam);
			if(!empty($myfolderlist)){
				$folderids = '';
				foreach($myfolderlist as $folder){
					$folderids .= $folder['folderid'].',';
				}
				$param['folderids'] = rtrim($folderids,',');
			}
		}else{
			$foldermodel = $this->model('folder');
			$classmodel = $this->model('Classes');
			$myclass = $classmodel->getClassByUid($roominfo['crid'],$user['uid']);
			$paramf['crid'] = $roominfo['crid'];
			$paramf['classid'] = $myclass['classid'];
			$paramf['limit'] = 100;
			if(!empty($myclass['grade'])){
				$paramf['grade'] = $myclass['grade'];
				$myfolderlist = $foldermodel->getClassFolderWithoutTeacher($paramf);
			}else{
				$myfolderlist = $foldermodel->getClassFolder($paramf);
			}
			if(!empty($myfolderlist)){
				$folderids = '';
				foreach($myfolderlist as $folder){
					$folderids .= $folder['folderid'].',';
				}
				$param['folderids'] = rtrim($folderids,',');
			}
		}
		$param['crid'] = $roominfo['crid'];
		$param['limit'] = 200;
		$param['order'] = 'rc.cwid desc';
		$cwlist = $cwmodel->getnewcourselist($param);
		if(!empty($cwlist)){
			//用户信息注入
			$cwlist = EBH::app()->lib('UserUtil')->setFaceSize('50_50')->init($cwlist,array('uid'),true);
			//权限信息注入
			$cwlist = Ebh::app()->lib('PowerUtil')->setCrid($crid)->init($cwlist,$user['uid'])->insertPower();
		}
		$newcwlist = array();
		
		$redis = Ebh::app()->getCache('cache_redis');
		//以cwid倒序取的数据.
		//按时间排序,有submitat取submitat,没有submitat取dateline.
		$cwcount = count($cwlist);
		for($i=0;$i<$cwcount;$i++){
			for($j=$i;$j<$cwcount;$j++){
				$date1 = !empty($cwlist[$i]['submitat'])?$cwlist[$i]['submitat']:$cwlist[$i]['dateline'];
				$date2 = !empty($cwlist[$j]['submitat'])?$cwlist[$j]['submitat']:$cwlist[$j]['dateline'];
				if($date1<$date2){
					$temp = $cwlist[$i];
					$cwlist[$i] = $cwlist[$j];
					$cwlist[$j] = $temp;
				}
			}
		}
		foreach($cwlist as $cw){
			$viewnum = $redis->hget('coursewareviewnum',$cw['cwid']);
			if(!empty($viewnum))
				$cw['viewnum'] = $viewnum;
			$cw['dateline'] = !empty($cw['submitat'])?$cw['submitat']:$cw['dateline'];
			$dayis = date('Y-m-d',$cw['dateline']);
			if($dayis == date('Y-m-d'))
				$dayis = 'z今天';
			elseif($dayis == date('Y-m-d',SYSTIME+86400))
				$dayis = 'y明天';
			elseif($dayis == date('Y-m-d',SYSTIME-86400))
				$dayis = 'x昨天';
			$newcwlist[$dayis][] = $cw;
		}
		//今天->明天->昨天->[日期]->[日期]...排序
		krsort($newcwlist);
		//取前50条
		$showcount = 50;
		$ncwcount = 0;
		$daycount = 0;
		$daylimit = 30; //离列表顶端课件30天的
		$timelimit = $daylimit*86400;
		$topdate = 0;
		foreach($newcwlist as $k=>$daylist){
			if(empty($topdate))
				$topdate = $daylist[0]['dateline'];
			$daycount++;
			foreach($daylist as $l=>$cw){
				$ncwcount++;
				if($ncwcount == $showcount){
					array_splice($newcwlist[$k],$l+1);
					break;
				}
			}
			if($topdate-$daylist[0]['dateline']>$timelimit && $daycount>1){
				array_splice($newcwlist,$daycount-1);
				break;
			}
			if($ncwcount == $showcount){
				array_splice($newcwlist,$daycount);
				break;
			}
		}
		$ret = array();
		if(!empty($newcwlist)){
			$datas = array_keys($newcwlist);
			$topdate_str = $datas[0];
			$topdate = 0;
			switch ($topdate_str) {
				case 'z今天':
				$topdate = SYSTIME;
					break;
				case 'y明天':
					$topdate = SYSTIME+86400;
					break;
				case 'x昨天':
					$topdate = SYSTIME-86400;
					break;
				default:
					$topdate = strtotime($topdate_str);
					if(empty($topdate)){
						$topdate = SYSTIME;
					}
					break;
			}
		}
		$enddate = $topdate-86400*30;
		foreach ($newcwlist as $data_name => $cwlist) {
			$nowdate = strtotime($data_name);
			if(!empty($nowdate)){
				if($nowdate < $enddate){
					break;
				}
			}
			$name = str_replace(array('z','y','x'), '', $data_name);
			$ret[] = array('id'=>0,'name'=>$name,'filetype'=>'','username'=>'','dateline'=>'','face'=>'','itemid'=>0,'sid'=>0,'submitat'=>0,'endat'=>0,'summary'=>'','fname'=>'');
			foreach ($cwlist as $cw) {
				$cw['face'] = $cw['uid_face'];
				//课件类型获取
				if($cw['ism3u8'] == 1){
					$filetype = 'flv';
				}else{
					$cwurl = $cw['cwurl'];
					$filetype = substr($cwurl, strpos($cwurl, '.') + 1); //文件类型
				}
				unset($cw['ism3u8']);
				$cw['filetype'] = $filetype;
				$cw['username'] = $cw['uid_name'];
				unset($cw['cwurl']);
				unset($cw['uid']);
				unset($cw['uid_face']);
				unset($cw['uid_name']);
				unset($cw['uid_username']);
				unset($cw['uid_realname']);
				unset($cw['uid_sex']);
				unset($cw['folderid']);
				$ret[] = $cw;
			}
		}
		echo json_encode($ret);
	}
	/**
	 *获取学校信息
	 */
	private function getRoomInfo($crid = 0){
		return $this->model('classroom')->getclassroomdetail($crid);
	}

	/**
	 *权限信息注入
	 */
	private function premissionInsert($cwlist = array()){
		$user = Ebh::app()->user->getloginuser();
		$crid = intval($this->input->post('rid'));
		$roominfo = $this->getRoomInfo($crid);
		$newcwlist = array();
		if($roominfo['isschool'] != 7){
			foreach ($cwlist as $cw) {
				$cw['itemid'] = 0;
				$newcwlist[] = $cw;
			}
			return $cwlist;
		}
		$userpermodel = $this->model('Userpermission');
		$myperparam = array('uid'=>$user['uid'],'crid'=>$roominfo['crid'],'filterdate'=>1);
		//我已经购买的课程
		$myfolderlist = $userpermodel->getUserPayFolderList($myperparam);
		$myfolderlist = $this->_modifyKeys($myfolderlist);
		//学校的收费课程
		$notFreeFolderList = $this->model('folder')->getNotFreeFolderList($crid);
		$notFreeFolderList = $this->_modifyKeys($notFreeFolderList);

		//学校的收费课程(服务包中)
		$roomfolderlist = $userpermodel->getPayItemByCrid($roominfo['crid']);
		$roomfolderlist = $this->_modifyKeys($roomfolderlist);

		//没有购买的学校收费课程
		$notBuyFolderList = array();
		foreach ($notFreeFolderList as $nkey => $notFreeFolder) {
			if(!array_key_exists($nkey, $myfolderlist)){
				$notBuyFolderList[$nkey] = $notFreeFolder;
  			}
		}
		foreach ($cwlist as $cw) {
			$key = 'f_'.$cw['folderid'];
			if(array_key_exists($key, $notBuyFolderList)){
				if(array_key_exists($key, $roomfolderlist)){
					$cw['itemid'] = intval($roomfolderlist[$key]['itemid']);
				}else{
					$cw['itemid'] = 0; //如果是收费课程但是不在服务包里面的也视为免费
				}
			}else{
				$cw['itemid'] = 0;
			}
			$newcwlist[] = $cw;
		}
		return $newcwlist;
	}
	
	/**
	 *将索引数组变成关联数组
	 */
	private function _modifyKeys($somelist = array()){
		$returnArr = array();
		foreach ($somelist as $some) {
			$key = 'f_'.$some['fid'];
			$returnArr[$key] = $some;
		}
		return $returnArr;
	}
}