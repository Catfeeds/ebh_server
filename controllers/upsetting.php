<?php

/**
 * 修改个人基本信息
 */
class UpsettingController extends CControl {
    public function index() {
		$user = Ebh::app()->user->getloginuser();
		$result = array();
		if(!empty($user)) {
			$oldpass = $this->input->post('oldpass');
			$newpass = $this->input->post('newpass');
			$nickname = $this->input->post('nickname');
			$realname = $this->input->post('realname');
			$sex = $this->input->post('sex');
			$birthday = $this->input->post('birthday');
			$email = $this->input->post('email');
			$address = $this->input->post('address');
			$mobile = $this->input->post('mobile');
			$qq = $this->input->post('qq');
			$profile = $this->input->post('profile');
			$mysign = $this->input->post('mysign');
			
			$editparam = array();
			$editparam['uid'] = $user['uid'];
			if(!empty($oldpass) && !empty($newpass)) {	//老密码和新密码不为空，则修改密码
				if(md5(trim($oldpass)) != $user['password']) {
					$result['status'] = 1;
					$result['msg'] = '原密码不正确';
					echo json_encode($result);
					exit();
				}
				$passwd = trim($newpass);
				if(strlen($passwd) < 6 || strlen($passwd) > 18) {
					$result['status'] = 1;
					$result['msg'] = '密码6-16位';
					echo json_encode($result);
					exit();
				}
				$editparam['password'] = $passwd;
			}
			
			if(isset($nickname)) {	//昵称
				$nickname = h($nickname);
				if(empty($nickname)){
					$editparam['nickname'] = '';
				}else{
					$editparam['nickname'] = $nickname;
				}
			}
			
			if(isset($realname)) {	//真实姓名
				$realname = h($realname);
				if(empty($realname)){
					$editparam['realname'] = '';
				}else{
					$editparam['realname'] = $realname;
				}
			}
			if(isset($mobile)) {	//手机
				if(empty($mobile)){
					$editparam['mobile'] = '';
				}else{
					$editparam['mobile'] = $mobile;
				}
			}
			if(is_numeric($sex)) {	//性别
				if($sex != 1) 
					$sex = 0;
				$editparam['sex'] = $sex;
			}
			if(isset($birthday)) {	//生日
				$birthday = strtotime($birthday);
				if($birthday !== FALSE) {
					$editparam['birthdate'] = $birthday;
				}
			}
			if(isset($qq)) {	//手机
				if(empty($qq)){
					$editparam['qq'] = '';
				}else{
					$editparam['qq'] = $qq;
				}
			}
			
			if(isset($profile)) {	//简介
				$profile = h($profile);
				if(empty($profile)){
					$editparam['profile'] = '';
				}else{
					$editparam['profile'] = $profile;
				}
			}

			if(isset($mysign)) {	//简介
				$profile = h($mysign);
				if(empty($mysign)){
					$editparam['mysign'] = '';
				}else{
					$editparam['mysign'] = $mysign;
				}
			}

			$usermodel = $this->model('user');
			if(isset($email)) {	//
				$email = trim($email);
				if(empty($email)){
					$email = '';
				}else{
					$emailreg = '/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/';
					if(!preg_match($emailreg, $email)) {
						$result['status'] = 1;
						$result['msg'] = '请填写有效的E-mail地址';
						echo json_encode($result);
						exit();
					}
					if($usermodel->existsEmail($email)) {
						$result['status'] = 1;
						$result['msg'] = '邮箱已存在';
						echo json_encode($result);
						exit();
					}
				}
				
				$editparam['email'] = $email;
			}
			
			if(isset($address)) {
				$address = h($address);
				$address = trim($address);
				if(empty($address)){
					$editparam['address'] = '';
				}else{
					$editparam['address'] = $address;
				}
			}
			$face = $this->uploadface();//修改头像处理
			if(!empty($face)){
				$editparam['face'] =  $face;
			}
			//开始编辑
			if(isset($editparam)) {
				if($user['groupid'] == 5){
					$teachermodel = $this->model('teacher');
					$eresult = $teachermodel->editteacher($editparam);
				}else{
					$membermodel = $this->model('Member');
					$eresult = $membermodel->editmember($editparam);
				}
				if($eresult !== FALSE) {	//更新成功
					$result['status'] = 0;
					if(!empty($editparam['password'])) {
						$key = $this->getKey($user['uid'],$editparam['password']);
						$result['k'] = $key;
					}
					if(!empty($editparam['face'])){
						$result['face'] = $editparam['face'];
					}else{
						$result['face'] = "";
					}
					$credit = $this->model('credit');
					$credit->addCreditlog(array('ruleid'=>3));
					
				} else {	//更新失败
					$result['status'] = 1;
					$result['msg'] = '更新失败';
				}
			}
		} else {
			$result['status'] = -1;
			$result['msg'] = '用户已过期';
		}
		echo json_encode($result);
	}
	/**
	*修改头像
	*/
	private function uploadface() {
		$upfield = 'face';
		$uplib = Ebh::app()->lib('Uploader');
		//上传配置
		$config = array(
			"savePath" => "uploads/" ,             //存储文件夹
			"showPath" => "uploads/" ,              //显示文件夹
			"maxSize" => 5242880 ,                   //允许的文件最大尺寸，单位字节 5M
			"allowFiles" => array(".jpg" , ".jpeg" )  //允许的文件格式
		);
		$_UP = Ebh::app()->getConfig()->load('upconfig');
        $up_type = 'avatar';
        $savepath = 'uploads/';
        $showpath = 'uploads/';
        if(!empty($_UP[$up_type]['savepath'])){
            $savepath = $_UP[$up_type]['savepath'];
        }
        if(!empty($_UP[$up_type]['showpath'])){
            $showpath = $_UP[$up_type]['showpath'];
        }
		$config['savePath'] = $savepath;
		$config['showPath'] = $showpath;
		$uplib->init($upfield,$config);
		$info = $uplib->getFileInfo();
		//如果是图片，并且需要裁减，则根据尺寸进行裁减
		if($info['state'] == 'SUCCESS') { //答疑上传的图片需要裁减
			Ebh::app()->helper('image');
			$imagepath = $info['url'];
			$imagesapath = $savepath.$imagepath;
			thumb($imagesapath,'120_120');
			thumb($imagesapath,'100_100');
			thumb($imagesapath,'78_78');
			thumb($imagesapath,'50_50');
			thumb($imagesapath,'40_40');
			$credit = $this->model('credit');
			$credit->addCreditlog(array('ruleid'=>2));
			$credit->addCreditlog(array('ruleid'=>3));
			return $info['showurl'];
		}
		return '';
	}
	/**
	*返回最新的用户
	*key 修改用户密码后需要重新返回key
	*/
	private function getKey($uid,$pwd) {
		$ip = $this->input->getip();
		$time = SYSTIME;
		$pwd = md5($pwd);
		$skey = "$pwd\t$uid\t$ip\t$time";
		$auth = authcode($skey, 'ENCODE');
		return $auth;
	}
}