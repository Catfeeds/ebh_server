<?php
/**
 *微信接口
 */
class WeixinController extends CControl{
	public function __construct(){
		parent::__construct();
		$this->user = Ebh::app()->user->getloginuser();
		if(empty($this->user)){
			echo json_encode(array('status'=>1,'msg'=>'user not login !'));
			exit;
		}
	}
	public function getwxopidbycode(){
		$wxopid = $this->input->post('code');
		// if(empty($code)){
		// 	echo json_encode(array('status'=>1,'msg'=>'code is empty !'));
		// 	exit;
		// }
		// $wechatObj = Ebh::app()->lib('WechatCallback');//得到微信扩展类的实例
		//根据带过来的code参数获取微信号的wxopid
		$wxopid = $this->_getwxopidbycode($code);
		if(empty($wxopid)){
			echo json_encode(array('status'=>1,'msg'=>'get wxopid fail !'));
		}else{
			$ret = array('wxopid'=>$wxopid,'status'=>0);
			echo json_encode($ret);
		}
	}

	//用户绑定微信
	public function uidbind(){
		$wxopid = $this->input->post('code');
		if(empty($wxopid)){
			echo json_encode(array('status'=>1,'msg'=>'wxopid is empty ！'));
			exit;
		}
		$uid = $this->user['uid'];
		$param = array(
			'uid'=>$uid,
			'wxopid'=>$wxopid
		);

		$userModel = $this->model('user');
		$res = $userModel->swxbind($param); //学生微信绑定
		if($res == 1){
			echo json_encode(array('status'=>0,'msg'=>'bind ok !'));
		}else if($res == -1){
			echo json_encode(array('status'=>-1,'msg'=>'has bind !'));
		}else if($res == -2){
			echo json_encode(array('status'=>-2,'msg'=>'has bind other !'));
		}else if($res == 0){
			echo json_encode(array('status'=>-3,'msg'=>'bind fail !'));
		}
	}
	// private function _getwxopidbycode($code = 0){
	// 	if(empty($code)){
	// 		return 0;
	// 	}
	// 	$wechatObj = Ebh::app()->lib('WechatCallback');//得到微信扩展类的实例
	// 	return $wechatObj->getopenidbycode($code);
	// }

	public function ticket(){
		$data_package = Ebh::app()->lib('WxUtils')->getJSApiSignPackage();
		echo json_encode($data_package);
	}

	//微信解绑
	public function unbind(){
		$res = array('status'=>-1,'msg'=>'解绑失败','openid'=>'');
		$user = $this->user;
		$uid = $user['uid'];
		$userModel = $this->model('user');
		$openInfo = $userModel->getOpenidByUid($uid);
		if(!empty($openInfo)){
			$openid = $openInfo['wxopid'];
		}else{
			$openid = '';
		}
		$param = array(
			'uid'=>$uid
		);
		$res = $userModel->swxunbind($param); //学生微信绑定
		if($res != -1){
			$res = array('status'=>0,'msg'=>'解绑成功','openid'=>$openid);
		}
		echo json_encode($res);
	}
}