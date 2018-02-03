<?php

/**
 * QQ用户登录控制器
 */
class QqLoginController extends CControl {
    public function index() {
    	//获取并且组织post参数
		$rec = $this->input->post();
		$openid = empty($rec['openid'])?'':$rec['openid'];
		$access_token = empty($rec['access_token'])?'':$rec['access_token'];
		$result = array();

		if(empty($openid) || empty($access_token)) {
			$result['status'] = 1;
			$result['msg'] = "QQ登录参数没有传递完整!";
			echo json_encode($result);
			exit();
		}

		//根据access_token获取openid
		$newopenid = $this->get_openid(array('access_token'=>$access_token));
		//将获取的openid和传入的openid做比较
		if($newopenid != $openid){
			//失败处理
			$result['msg'] = "QQ登录参数非法!";
			$result['status'] = 1;
			echo json_encode($result);
			exit();
		}
		
		$usermodel = $this->model('user');
		$user = $usermodel->openlogin($openid,'qq');
		if(empty($user)) {
			//新账号创建处理
			$param_qq = array(
				'access_token'=>$access_token,
				'appid'=>'100298841',
				'openid'=>$openid
			);
			$uid = $this->addNewUser($param_qq);
			if(empty($uid)){
				$result['msg'] = "生成新用户失败!";
				$result['status'] = 1;
				echo json_encode($result);
				exit();
			}
			$user = $usermodel->openlogin($openid,'qq');
		}
		if($user['groupid'] != 6) {	//只允许学生登录
			$result['status'] = 1;
			echo json_encode($result);
			exit();
		}
		$newurl = $this->checkUpdate();
		$result['status'] = 0;
		$result['realname'] = $user['realname'];
		$result['nickname'] = $user['nickname'];
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
		$result['url'] = $newurl;
		$k = $this->getKey($user);
		$result['k'] = $k;
		echo json_encode($result);
	}
	/**
	*check the update version
	*return new url if have the new version
	*/
	private function checkUpdate() {
		$version = $this->input->post('version');
		$from = $this->input->post('from');
		$from = intval($from);
		$newurl = '';
		if($from != 2 && $from != 3 && $from != 4)
			return '';
		$update = Ebh::app()->getConfig()->load('update');
		if($from == 2) {
			$newversion = $update['android'];
			$newurl = $update['androidurl'];
		} else if($from == 4) {
			$newversion = $update['android-hd'];
			$newurl = $update['androidurl-hd'];
		}else {
			$newversion = $update['ios'];
			$newurl = $update['iosurl'];
		}
		if($version != $newversion) {
			return $newurl;
		}
		return '';

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

	/**
	 *创建新账号
	 *生成随机的8为用户名(以qq开头),随机的8位密码
	 **/
	private function addNewUser($param = array()){
		$qqUserInfo = $this->get_user_info($param);
		if($qqUserInfo['ret']!=0){
			//获取QQ用户数据错误
			return 0;
		}
		$userModel = $this->model('user');
		while(true){
			$username = 'qq'.strtolower(random(6,false));
			if($userModel->exists($username) == false){
				break;
			}
		}
		//生成8位数字密码
		$password = random(8,true);

		$opid = $param['openid'];

		$usermodel = $this->model('user');
		$param['username'] = $username;
		$param['password'] = $password;
		$param['sex'] = $qqUserInfo['gender'] == '男'?0:1;
		$param['nickname'] = $qqUserInfo['nickname'];
		$param['dateline'] = SYSTIME;
		$param['qqopid'] = $opid;
		$uid = $userModel->addmember($param);
		$userinfo = $usermodel->openlogin($opid,'qq');
		$credit = $this->model('credit');
		$credit->addCreditlog(array('ruleid'=>1,'uid'=>$userinfo['uid']));
		return $uid;	
	}

	/*
	获取qq用户信息
	*/
	private function get_user_info($param = array())
	{
		$get_user_info = "https://graph.qq.com/user/get_user_info?"
			. "access_token=" . $param['access_token']
			. "&oauth_consumer_key=" . $param["appid"]
			. "&openid=" . $param["openid"]
			. "&format=json";

		$info = $this->get_url_contents($get_user_info);
		$arr = json_decode($info, true);
		return $arr;
	}

	/*
	 *获取qq的openid
	*/
	private function get_openid($param = array()){

		$graph_url = "https://graph.qq.com/oauth2.0/me?access_token=" 
			. $param['access_token'];

		$str  = $this->get_url_contents($graph_url);
		if (strpos($str, "callback") !== false)
		{
			$lpos = strpos($str, "(");
			$rpos = strrpos($str, ")");
			$str  = substr($str, $lpos + 1, $rpos - $lpos -1);
		}

		$user = json_decode($str);
		if (isset($user->error))
		{
			return 0;
			exit;
		}
		$openid = $user->openid;
		return $user->openid;
	}

	/*
	获取远程页面信息
	*/
	private function get_url_contents($url){

		if (ini_get("allow_url_fopen") == "1")
			return file_get_contents($url);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result =  curl_exec($ch);
		curl_close($ch);

		return $result;
	}
}
