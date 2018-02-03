<?php

/**
 * 用户登录控制器(家长)
 */
class PloginController extends CControl {
    public function index() {
		$username = $this->input->post('user');
		$passwd = $this->input->post('passwd');
		$result = array();
		if( (empty($username) || empty($passwd)) ) {
			$result['status'] = 1;
			$result['msg'] = "账号或者密码为空";
			echo json_encode($result);
			exit();
		}
		$usermodel = $this->model('user');
		$user = $usermodel->plogin($username,$passwd);
		if(empty($user)) {
			$result['status'] = 1;
			$result['msg'] = '账号不正确';
			echo json_encode($result);
			exit();
		}
		if($user['status'] == 0){
			$result['status'] = -1;
			$result['msg'] = '账号被禁用';
			echo json_encode($result);
			exit();
		}
		
		$result['status'] = 0;
		$result['realname'] = $user['realname'];
		$result['nickname'] = $user['nickname'];
		//$result['nickname'] = $user['nickname'];
		$result['credit'] = $user['credit'];
		$result['face'] = $user['face'];
		if(empty($result['face'])) {
			$sex = empty($user['sex']) ? 'man' : 'woman';
            $type = $user['groupid'] == 5 ? 't' : 'm';
            $defaulturl = 'http://static.ebanhui.com/ebh/tpl/default/images/'.$type.'_'.$sex.'.jpg';
            $face = empty($user['face']) ? $defaulturl : $user['face'];
            $facethumb = getthumb($face,'120_120');
            $result['face'] = $facethumb;
		}
		$result['groupid'] = $user['groupid'];
		$result['uid'] = $user['uid'];
		$k = $this->getKey($user);
		$result['k'] = $k;
		echo json_encode($result);
	}
	/**
	*return new valid user token key
	*/
	private function getKey($user) {
		$uid = $user['uid'];
		$pwd = $user['password'];
		$ip = $this->input->getip();
		$time = SYSTIME;
		$skey = "$pwd\t$uid\t$ip\t$time";
		$auth = authcode($skey, 'ENCODE');
		return $auth;
	}
}
