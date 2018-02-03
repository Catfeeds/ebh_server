<?php

/**
 * 手机新浪微博用户登录接口
 */
class SinaLoginController extends CControl {
    public function index() {
    	//获取并且组织post参数
		$rec = $this->input->post();
		//传入的uid用于用户验证
		$uid = empty($rec['uid'])?'':$rec['uid'];
		$access_token = empty($rec['access_token'])?'':$rec['access_token'];
		$result = array();

		if(empty($uid) || empty($access_token)) {
			$result['status'] = 1;
			$result['msg'] = "登录参数没有传递完整!";
			echo json_encode($result);
			exit();
		}

		//根据access_token获取用户信息
		$url_c = 'https://api.weibo.com/oauth2/get_token_info';
		$param_c = array(
			'access_token'=>$access_token
		);
	 	$sinauser = $this->get_url_contents($url_c,$param_c);

	 	if(empty($sinauser)){
	 		$result['status'] = 1;
			$result['msg'] = "接口调用错误,请联系管理员!";
			echo json_encode($result);
			exit();
	 	}
	 	//获取新浪返回过来的用户信息
	 	$sinauid = $sinauser->uid;
	 	$sinaopid = $sinauser->uid;

	 	if($sinauid!=$uid){
	 		$result['status'] = 1;
			$result['msg'] = "用户参数非法,登录冒充!";
			echo json_encode($result);
			exit();
	 	}
		
		$usermodel = $this->model('user');
		$user = $usermodel->openlogin($sinaopid,'sina');
		if(empty($user)) {
			//新账号创建处理
			$param_sina = array(
				'access_token'=>$access_token,
				'uid'=>$sinauid,
				'sinaopid'=>$sinaopid
			);
			$uid = $this->addNewUser($param_sina);
			if(empty($uid)){
				$result['msg'] = "生成新用户失败!";
				$result['status'] = 1;
				echo json_encode($result);
				exit();
			}
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
	 *生成随机的10位用户名(以sina开头),随机的8位密码
	 **/
	private function addNewUser($param = array()){
		$sinaUserInfo = $this->get_user_info($param);
		if(empty($sinaUserInfo['id'])){
			//获取Sina用户数据错误
			return 0;
		}
		$userModel = $this->model('user');
		while(true){
			$username = 'sina'.strtolower(random(8));
			if($userModel->exists($username) == false){
				break;
			}
		}
		//生成8位数字密码
		$password = random(8,true);

		$opid = $param['sinaopid'];

		$usermodel = $this->model('user');
		$param['username'] = $username;
		$param['password'] = $password;
		$param['sex'] = $sinaUserInfo['gender'] == 'm'?0:1;
		$param['nickname'] = $sinaUserInfo['name'];
		$param['dateline'] = SYSTIME;
		$param['sinaopid'] = $opid;
		$uid = $userModel->addmember($param);
		$userinfo = $usermodel->openlogin($opid,'sina');
		$credit = $this->model('credit');
		$credit->addCreditlog(array('ruleid'=>1,'uid'=>$userinfo['uid']));
		return $uid;	
	}

	/*
	获取新浪微博用户信息
	*/
	private function get_user_info($param = array()){
		$appkey = "565d7f55f75cf56f389b1a6a91333029";
		$get_user_info = "https://api.weibo.com/2/users/show.json?"
			. "source=" . $appkey
			. "&access_token=" . $param["access_token"]
			. "&uid=" . $param["uid"];

		$info = $this->get_url_contents_get($get_user_info);
		$arr = json_decode($info, true);
		return $arr;
	}


	/*
	获取远程页面信息(post)
	*/
	function get_url_contents($url,$data = array()){
		$data = http_build_query($data);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url );
		curl_setopt($ch, CURLOPT_POST, 1 );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data );
		$result =  curl_exec($ch);
		curl_close($ch);
		return json_decode($result);
	}

	/*
	获取远程页面信息(get)
	*/
	private function get_url_contents_get($url){

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
