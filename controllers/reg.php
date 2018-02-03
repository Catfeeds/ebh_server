<?php

/**
 * 用户reg控制器
 */
class RegController extends CControl {
    public function index() {
		$method = $this->input->post('method');
		switch ($method) {
			case 'regist.mobile.check':
				$this->regist_phone_check();
				break;
			case 'regist.mobile.doreg':
				$this->regist_mobile_reg();
				break;
			case 'regist.mobile.email.check':
				$this->regist_phone_email_check();
				break;
			case 'regist.mobile.email.doreg':
				$this->regist_mobile_email_reg();
				break;
			default:
				$this->_doRegist();
				break;
		}
	}

	private function regist_mobile_reg(){
		$result = array('status'=>'0','msg'=>'','data'=>array());
		$vcode = $this->input->post('vcode');
		$vcode_decoded = authcode($vcode,'DECODE');
		if(empty($vcode) || empty($vcode_decoded)) {
			$result = array('status'=>'1','msg'=>'vcode参数校验失败','data'=>array());
			echo json_encode($result);
			exit;
		}
		$vcode_arr = json_decode(base64_decode($vcode_decoded));
		if(empty($vcode_arr)){
			$result = array('status'=>'1','msg'=>'vcode参数解码失败','data'=>array());
			echo json_encode($result);
			exit;
		}
		$user = $vcode_arr->user;
		$passwd = $vcode_arr->passwd;
		$mobile = $vcode_arr->mobile;
		$result = $this->_regist_phone_check($user,$passwd,$mobile);
		if($result['status'] !== '0'){
			echo json_encode($result);
			exit;
		}

		$usermodel = $this->model('user');
		$param = array('username'=>$user,'password'=>$passwd,'mobile'=>$mobile);
		$memberid = $usermodel->addmember($param);
		if($memberid>0) {
			$result['status'] = '0';
			$credit = $this->model('credit');
		    $credit->addCreditlog(array('ruleid'=>1,'uid'=>$memberid));
            //绑定手机
            //向ebh_binds表插入绑定数据
            $bindmodel = $this->model('Bind');
            $binddata = array(
                'uid'=>$memberid,
                'is_mobile'=>1,
                'mobile'=>$mobile,
                'mobile_str'=>json_encode(
                    array('mobile'=>$mobile,
                        'uid'=>$memberid,
                        'dateline'=>SYSTIME
                    )
                )
            );
            $bindmodel->doBind($binddata,$memberid);
		} else {
			$result['status'] = '1';
		}
		echo json_encode($result);
		fastcgi_finish_request();
		$redis = Ebh::app()->getCache('cache_redis');
		$res = $redis->hget('xnum','user');
		if(!empty($res)){
			$redis->hIncrBy('xnum','user',1);
		}
	}

	private function regist_phone_check(){
		$user = $this->input->post('user');
		$passwd = $this->input->post('passwd');
		$mobile = $this->input->post('mobile');
		$result = $this->_regist_phone_check($user,$passwd,$mobile);
		if($result['status'] != '0') {
			echo json_encode($result);
			exit;
		}
		$code = random(6,true);
		$infopackage = array(
			'user'=>$user,
			'passwd'=>$passwd,
			'mobile'=>$mobile,
			'code'=>$code,
			'lasttime'=>SYSTIME
		);
		$result['msg'] = '';
		$result['data']['vcode'] = authcode(base64_encode(json_encode($infopackage)),'ENCODE');
		$result['data']['code'] = $code;
		$result['data']['infopackage'] = $infopackage;
		echo json_encode($result);
		fastcgi_finish_request();
		Ebh::app()->lib('SMS')->send_dayu($mobile,$code);
	}

	//手机注册检测合法性,成功注册返回vcode
	private function _regist_phone_check($user='',$passwd='',$mobile=''){
		$result = array('status'=>'0','msg'=>'','data'=>array());
		if(empty($user) || empty($passwd) || empty($mobile)) {
			$result['status'] = 1;
			$result['msg'] = '用户名密码和手机不能为空';
			return $result;
		}
		$namereg = '/^[a-zA-Z][a-z0-9A-Z_]{5,19}$/';
		if(!preg_match($namereg, $user)) {
			$result['status'] = 1;
			$result['msg'] = '用户名由6-20位英文、数字且以字母开头组成';
			return $result;
		}
		if(strlen($passwd) < 6 || strlen($passwd) > 18) {
			$result['status'] = 1;
			$result['msg'] = '密码应该为6-16位字符';
			return $result;
		}

		$mobilereg = "/^1[3-8]{1}\d{9}$/";
		if(!preg_match($mobilereg, $mobile)) {
			$result['status'] = 1;
			$result['msg'] = '请输入正确的手机号';
			return $result;
		}
		$usermodel = $this->model('user');
		if($usermodel->exists($user)) {
			$result['status'] = 1;
			$result['msg'] = '用户名已被占用，请重新输入';
			return $result;
		}
		if($usermodel->existsMobile($mobile)) {
			$result['status'] = 1;
			$result['msg'] = '手机已绑定，请重新输入';
			return $result;
		}
		return $result;
	}

	private function _doRegist(){
		$username = $this->input->post('user');
		$passwd = $this->input->post('passwd');
		$email = $this->input->post('email');
		$result = array();
		if(empty($username) || empty($passwd) || empty($email)) {
			$result['status'] = 1;
			$result['msg'] = '用户名密码和邮箱不能为空';
			echo json_encode($result);
			exit();
		}
		$namereg = '/^[a-zA-Z][a-z0-9A-Z_]{5,19}$/';
		if(!preg_match($namereg, $username)) {
			$result['status'] = 1;
			$result['msg'] = '用户名由6-20位英文、数字且以字母开头组成';
			echo json_encode($result);
			exit();
		}
		if(strlen($passwd) < 6 || strlen($passwd) > 18) {
			$result['status'] = 1;
			$result['msg'] = '密码应该为6-16位字符';
			echo json_encode($result);
			exit();
		}
		$emailreg = '/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/';
		if(!preg_match($emailreg, $email)) {
			$result['status'] = 1;
			$result['msg'] = '邮箱格式错误，请输入正确的邮箱号码';
			echo json_encode($result);
			exit();
		}
		$usermodel = $this->model('user');
		if($usermodel->exists($username)) {
			$result['status'] = 1;
			$result['msg'] = '用户名已被占用，请重新输入';
			echo json_encode($result);
			exit();
		}
		if($usermodel->existsEmail($email)) {
			$result['status'] = 1;
			$result['msg'] = '邮箱已绑定，请重新输入';
			echo json_encode($result);
			exit();
		}
		$param = array('username'=>$username,'password'=>$passwd,'email'=>$email);
		$memberid = $usermodel->addmember($param);
		if($memberid>0) {
			$result['status'] = 0;
			$credit = $this->model('credit');
		    $credit->addCreditlog(array('ruleid'=>1,'uid'=>$memberid));
		} else {
			$result['status'] = 1;
		}
		echo json_encode($result);
		fastcgi_finish_request();
		$redis = Ebh::app()->getCache('cache_redis');
		$res = $redis->hget('xnum','user');
		if(!empty($res)){
			$redis->hIncrBy('xnum','user',1);
		}
	}


	//手机邮箱同时注册
	private function regist_mobile_email_reg(){
		$result = array('status'=>'0','msg'=>'','data'=>array());
		$vcode = $this->input->post('vcode');
		$vcode_decoded = authcode($vcode,'DECODE');
		if(empty($vcode) || empty($vcode_decoded)) {
			$result = array('status'=>'1','msg'=>'vcode参数校验失败','data'=>array());
			echo json_encode($result);
			exit;
		}
		$vcode_arr = json_decode(base64_decode($vcode_decoded));
		if(empty($vcode_arr)){
			$result = array('status'=>'1','msg'=>'vcode参数解码失败','data'=>array());
			echo json_encode($result);
			exit;
		}
		$user = $vcode_arr->user;
		$passwd = $vcode_arr->passwd;
		$mobile = $vcode_arr->mobile;
		$email = $vcode_arr->email;
		$result = $this->_regist_phone_email_check($user,$passwd,$mobile,$email);
		if($result['status'] !== '0'){
			echo json_encode($result);
			exit;
		}

		$usermodel = $this->model('user');
		$param = array('username'=>$user,'password'=>$passwd,'mobile'=>$mobile,'email'=>$email);
		$memberid = $usermodel->addmember($param);
		if($memberid>0) {
			$result['status'] = '0';
			$credit = $this->model('credit');
		    $credit->addCreditlog(array('ruleid'=>1,'uid'=>$memberid));

            //绑定手机
            //向ebh_binds表插入绑定数据
            $bindmodel = $this->model('Bind');
            $binddata = array(
                'uid'=>$memberid,
                'is_mobile'=>1,
                'mobile'=>$mobile,
                'mobile_str'=>json_encode(
                    array('mobile'=>$mobile,
                        'uid'=>$memberid,
                        'dateline'=>SYSTIME
                    )
                )
            );
            $bindmodel->doBind($binddata,$memberid);
		} else {
			$result['status'] = '1';
		}
        //将注册信息记录到日志
        if ($memberid>0){
            $this->afterUserRegister($memberid);
        }
		echo json_encode($result);
		fastcgi_finish_request();
		$redis = Ebh::app()->getCache('cache_redis');
		$res = $redis->hget('xnum','user');
		if(!empty($res)){
			$redis->hIncrBy('xnum','user',1);
		}
	}
    /**
     * 用户注册后将注册信息记录到日志
     */
    public function afterUserRegister($memberid){



        $logdata = array();
        $logdata = $this->input->post();
        $logdata['uid'] = $memberid;
        $logdata['logtype'] = 6;  //6网校注册方式的创建用户
        $registerloglib = Ebh::app()->lib('RegisterLog');
        $registerloglib->addOneRegisterLog($logdata);
    }

	private function regist_phone_email_check(){
		$user = $this->input->post('user');
		$passwd = $this->input->post('passwd');
		$mobile = $this->input->post('mobile');
		$email = $this->input->post('email');
		$result = $this->_regist_phone_email_check($user,$passwd,$mobile,$email);
		if($result['status'] != '0') {
			echo json_encode($result);
			exit;
		}
		$code = random(6,true);
		$infopackage = array(
			'user'=>$user,
			'passwd'=>$passwd,
			'mobile'=>$mobile,
			'code'=>$code,
			'lasttime'=>SYSTIME,
			'email'=>$email
		);
		$result['msg'] = '';
		$result['data']['vcode'] = authcode(base64_encode(json_encode($infopackage)),'ENCODE');
		$result['data']['code'] = $code;
		$result['data']['infopackage'] = $infopackage;
		echo json_encode($result);
		fastcgi_finish_request();
		Ebh::app()->lib('SMS')->send_dayu($mobile,$code);
	}

	//手机注册检测合法性,成功注册返回vcode
	private function _regist_phone_email_check($user='',$passwd='',$mobile='',$email = ''){
		$result = array('status'=>'0','msg'=>'','data'=>array());
		if(empty($user) || empty($passwd) || empty($mobile)) {
			$result['status'] = 1;
			$result['msg'] = '用户名密码和手机不能为空';
			return $result;
		}
		$usermodel = $this->model('user');
		$namereg = '/^[a-zA-Z][a-z0-9A-Z_]{5,19}$/';
		if(!preg_match($namereg, $user)) {
			$result['status'] = 1;
			$result['msg'] = '用户名由6-20位英文、数字且以字母开头组成';
			return $result;
		}
		if(strlen($passwd) < 6 || strlen($passwd) > 18) {
			$result['status'] = 1;
			$result['msg'] = '密码应该为6-16位字符';
			return $result;
		}

		$emailreg = '/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/';
		if(!preg_match($emailreg, $email)) {
			$result['status'] = 1;
			$result['msg'] = '邮箱格式错误，请输入正确的邮箱号码';
			return $result;
		}

		if($usermodel->existsEmail($email)) {
			$result['status'] = 1;
			$result['msg'] = '邮箱已绑定，请重新输入';
			return $result;
		}

		$mobilereg = "/^1[3-8]{1}\d{9}$/";
		if(!preg_match($mobilereg, $mobile)) {
			$result['status'] = 1;
			$result['msg'] = '请输入正确的手机号';
			return $result;
		}
		
		if($usermodel->exists($user)) {
			$result['status'] = 1;
			$result['msg'] = '用户名已被占用，请重新输入';
			return $result;
		}
		if($usermodel->existsMobile($mobile)) {
			$result['status'] = 1;
			$result['msg'] = '手机已绑定，请重新输入';
			return $result;
		}
		return $result;
	}
}
