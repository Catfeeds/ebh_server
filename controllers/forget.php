<?php
/*
忘记密码
*/
class ForgetController extends CControl{
	public function index(){
		if($this->input->post('email')){
			$email = trim ( $this->input->post('email') );
			$from = $this->input->post('from');
			$user = $this->model('user');
			$res = $user->getUserByEmail($email);
			if ($res) {
				$mail = Ebh::app()->lib('Mail');
				$member = $this->model('member');
				$username = $res['username'];
				$uid = $res['uid'];
				$message = $mail->getmessage($username);
				$time = $mail->encrypt ( SYSTIME );
				
				$activecode = $this->getactivecode ( $uid ); //得到激活码
				$code = $mail->encrypt($username);//加密账号
				if(!empty($from)){
					$message = str_replace ( "%s", "http://".$from.".ebh.net/forget/pwd.html?code=$code&activecode=$activecode&ut=$time", $message );
				}else{
					$message = str_replace ( "%s", "http://www.ebh.net/forget/pwd.html?code=$code&activecode=$activecode&ut=$time", $message );
				}
				$issuccess = $mail->sendemail ( array ($email ), '忘记密码', $message );
				if ($issuccess > 0) {
					$filelen=strrpos($email,'@');
					$filename_name=substr($email, $filelen+1);
					if($filename_name=='gmail.com'){
						$mailurl = 'http://www.gmail.com';
					}else{
						$mailurl = 'http://mail.'.$filename_name;
					}
					$result['status'] = 1;
					$result['msg'] = '请到邮箱地址'.$email.'收取来自e板会的邮件';
					$result['mailurl'] = $mailurl;
					echo json_encode($result);
					exit();
				}
			} else{
				$result['status'] = 2;
				$result['msg'] = '邮箱不存在!';
				$result['mailurl'] = '';
				echo json_encode($result);
				exit();
			}
		}else{
				$result['status'] = 3;
				$result['msg'] = '参数没有传递!';
				$result['mailurl'] = '';
				echo json_encode($result);
				exit();
		}
	}

	/*
	修改密码
	*/
	public function pwd(){
		if($this->input->post('code') && $this->input->post('activecode')){
			$mail = $mail = Ebh::app()->lib('Mail');
			$username = $mail->decrypt($this->input->post('code'));
			$usermodel = $this->model('user');
			$user = $usermodel->getuserbyusername($username);
			$log = $this->model('log');
			$param['uid'] = $user['uid'];
			$param['type'] = 'member';
			$param['opid'] = 16777216;
			$param['value'] = 0;
			$logdetail = $log->getOneLog($param);
			if($logdetail){
				$userarr['password'] = $this->input->post('passwd');
				$res = $usermodel->update($userarr,$user['uid']);
				if($res){
					$wherearr['logid'] = $logdetail['logid'];
					$log->_update(array('value'=>1),$wherearr);
					echo json_encode(array('status'=>0,'msg'=>'操作成功'));
				}
			}else{
				echo json_encode(array('status'=>1,'msg'=>'验证信息已经过期'));
			}
		}
		
	}	
	/*
	获取activecode
	*/
	private function getactivecode($uid){
		$log = $this->model('log');
		$random = random(15);
		$param['uid'] = $uid;
		$param['toid'] = $uid;
		$param['opid'] = 16777216;
		$param['type'] = 'member';
		
		$log->_update(array('value'=>1),$param);
		$param['fromip'] = $this->input->getip();
		$param['message'] = $random;
		$log->_insert($param);
		return $random;
	}
    
}
?>