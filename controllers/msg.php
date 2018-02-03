<?php
//短信验证码控制器
class MsgController extends CControl {
	public function index(){
		$method = $this->input->post('method');
		switch ($method) {
			case 'msg.checkcode':
				$result = $this->_checkcode();
				break;
			default:
				break;
		}
	}

	//校验验证码
	private function _checkcode(){
		$code = $this->input->post('code');
		if(empty($code)){
			$this->_ret('1','没有填写手机验证码');
		}
		$vcode = $this->input->post('vcode');
		if(empty($code)){
			$this->_ret('2','请求非法，vcode信息丢失');
		}
		$vcode_decoded = authcode($vcode,'DECODE');
		if(empty($vcode_decoded)){
			$this->_ret('3','请求非法，vcode信息伪造');
		}
		$vcode_arr = json_decode(base64_decode($vcode_decoded));
		if(empty($vcode_arr) || empty($vcode_arr->code)) {
			$this->_ret('4','请求非法，vcode信息不完整');
		}
		$real_code = $vcode_arr->code;
		if($code === $real_code) {
			$this->_ret('0','校验成功');
		}else{
			$this->_ret('5','验证码错误');
		}
	}

    private function _ret($errcode = '0',$errmsg='',$data = array()){
    	echo json_encode(array('errcode'=>$errcode,'errmsg'=>$errmsg,'data'=>$data));
    	exit;
    }
}