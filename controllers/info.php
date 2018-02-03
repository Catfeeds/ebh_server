<?php

/**
 * 用户基本信息控制器
 */
class InfoController extends CControl {
    public function index() {
		$userinfo = array();
		$user = Ebh::app()->user->getloginuser();
		if(!empty($user)) {
			if($user['groupid'] == 6){
				$usermodel = $this->model('User');
				$userinfo = $usermodel->getUserInfo($user['uid']);
			}else{
				$teachermodel = $this->model('teacher');
				$userinfo = $teachermodel->getteacherdetail($user['uid']);
			}
			if(!empty($userinfo['birthday'])) {
				$userinfo['birthday'] = date('Y-m-d',$userinfo['birthday']);
			} else {
				$userinfo['birthday'] = '';
			}
			if(empty($userinfo['face'])) {
				$sex = empty($userinfo['sex']) ? 'man' : 'woman';
				$type = $user['groupid'] == 6 ?'m':'t';
				$defaulturl = 'http://static.ebanhui.com/ebh/tpl/default/images/'.$type.'_'.$sex.'.jpg';
				$face = empty($userinfo['face']) ? $defaulturl : $userinfo['face'];
				$facethumb = getthumb($face,'120_120');
				$userinfo['face'] = $facethumb;
			}
			$name = empty($userinfo['realname']) ? $userinfo['username'] : $userinfo['realname'];
			$userinfo['name'] = $name;
			$sex = $userinfo['sex'] == '1' ? '女' : '男';
			$userinfo['qq'] = empty($userinfo['qq']) ? "" : $userinfo['qq'];
            $userinfo['username'] = empty($userinfo['username']) ? "" : $userinfo['username'];
			$userinfo['realname'] = empty($userinfo['realname']) ? "" : $userinfo['realname'];
			$userinfo['profile'] = empty($userinfo['profile']) ? "" : $userinfo['profile'];
			$userinfo['sex'] = $sex;
			$userinfo['mysign'] = !empty($user['mysign'])?$user['mysign']:'';
			$userinfo['rank'] = $this->getRank();
			$userinfo['uid'] = $user['uid'];
		}
		echo json_encode($userinfo);
	}

	//获取用户等级
	public function getRank(){
		$user = Ebh::app()->user->getloginuser();
		if($user['groupid'] == 6){
			$clconfig = Ebh::app()->getConfig()->load('creditlevel');
		}else{
			$clconfig = Ebh::app()->getConfig()->load('creditlevel_t');
		}
		$credit = !empty($user['credit'])?$user['credit']:0;
		$maxrankinfo = $clconfig[count($clconfig)-1];
		$rank = $maxrankinfo['title'];
		foreach ($clconfig as $cl) {
			if($cl['max'] < $credit){
				continue;
			}else{
				$rank = $cl['title'];
				break;
			}
		}
		return $rank;
	}

	/**
	 * 获取班级信息
	 */
	public function getclass(){
		$user = Ebh::app()->user->getloginuser();
		$crid = $this->input->post('rid');
		$classmodel = $this->model('Classes');
		$myclass = $classmodel->getClassByUid($crid,$user['uid']);
		echo json_encode($myclass);
	}
}
