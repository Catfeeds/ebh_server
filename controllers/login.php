<?php

/**
 * 用户登录控制器
 */
class LoginController extends CControl {
	private $life = 86400;	//cache time 
    public function index() {
		$username = $this->input->post('user');
		$passwd = $this->input->post('passwd');
		$wxcode = $this->input->post('wxcode');
		$needbind = false;//需要绑定标识
		$result = array();
		if( (empty($username) || empty($passwd)) && empty($wxcode)) {
			$result['status'] = 1;
			echo json_encode($result);
			exit();
		}
		$usermodel = $this->model('user');
		if(!empty($wxcode) && ( empty($username) || empty($passwd) ) ){//微信code登录
			$wxopid = $wxcode;
			if(empty($wxopid)){
				$result['status'] = 1;
				echo json_encode($result);
				exit();
			}else{
				$user = $usermodel->getUserByWxOpenid($wxopid);
			}
		}else{
			$user = $usermodel->login($username,$passwd);

			//账号密码登录 需要绑定该微信账号
			if(!empty($user) && !empty($wxcode)){
				$needbind = true;
				//log_message(var_export($user,true));
				//log_message(var_export($wxcode,true));
			}
		}
		if(empty($user)) {
			$result['status'] = 1;
			$result['msg'] = '账号不正确';
			echo json_encode($result);
			exit();
		}
        //登录成功，则更新上次登录时间和IP信息
        if(!empty($user)) {
            $this->afterUserLogin($user);
        }

		//处理用户信息
		$newurl = $this->checkUpdate();
		$result['status'] = 0;
		$result['lastlogintime'] = $user['lastlogintime'];
		$result['lastloginip'] = $user['lastloginip'];
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
		$result['url'] = $newurl;
		$result['groupid'] = $user['groupid'];
		$result['uid'] = $user['uid'];
		$result['mysign'] = !empty($user['mysign'])?$user['mysign']:'';
		$result['rank'] = $this->getRank($user);
		$k = $this->getKey($user);
		$result['k'] = $k;
		echo json_encode($result);
		fastcgi_finish_request();
		
		if($needbind){
			$this->wxbind($user,$wxcode);
		}
	}
    /**
     * 登录成功，则更新上次登录时间和IP信息
     */
    private function afterUserLogin($user){
        $clientip = $this->input->getip();
        $usermodel = $this->model('user');
        $userparam = array('lastlogintime'=>SYSTIME,'lastloginip'=>$clientip,'logincount'=>1);
        if($user['groupid'] == 6 && empty($user['allowip']))
            $userparam['allowip'] = $clientip;
        $usermodel->update($userparam,$user['uid']);
    }
	/**
	 * 微信绑定操作
	 */
	private function wxbind($user,$wxcode){
		$userDao = $this->model("User");
		$openDao = $this->model("Open");
		$bindDao = $this->model("Bind");
		
		//先获取微信用户信息
		$wechatObj = Ebh::app()->lib('WechatCallback');//得到微信扩展类的实例
		$userarr = $wechatObj->getWxUserInfo($wxcode);
		$userarr = json_decode(json_encode($userarr),true);

		//已经绑定的ebh用户
		$ebhUser = $userDao->getUserByWxOpenid($wxcode);
		
		
		//log_message(var_export($user,true));
		//log_message(var_export($userarr,true));
		//log_message(var_export($ebhUser,true));
		
		if(!empty($user['wxunionid'])){//已经绑定
			if(!empty($ebhUser)){
				if($ebhUser['uid'] !=$user['uid'] ){//不是同一个用户
					//解绑新user
					$bindDao->doUnbind('wx',$user['uid']);
					//解绑ebh用户
					$bindDao->doUnbind('wx',$ebhUser['uid']);
					//绑定微信用户
					$openDao->dobind('wx',$userarr,$user);
				}else{//是同一个用户 不用处理
					return ;
				}
			}else{
				//解绑新user
				$bindDao->doUnbind('wx',$user['uid']);
				//绑定微信用户
				$openDao->dobind('wx',$userarr,$user);
			}
			
		}else{//user未绑定
			if(!empty($ebhUser)){
				//解绑ebh用户
				$bindDao->doUnbind('wx',$ebhUser['uid']);
			}
			//绑定新user
			$openDao->dobind('wx',$userarr,$user);
		}
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
		if($from != 2 && $from != 3 && $from != 4 && $from != 5 && $from !=6 && $from !=7 )
			return '';
		$update = Ebh::app()->getConfig()->load('update');
		if($from == 2) {
			$newversion = $update['android'];
			$newurl = $update['androidurl'];
		} else if($from == 4) {
			$newversion = $update['android-hd'];
			$newurl = $update['androidurl-hd'];
		} else if($from == 5) {
			$newversion = $update['ipad'];
			$newurl = $update['ipadurl'];
		} else if($from == 6) {
			$newversion = $update['android_xiaoxue'];
			$newurl = $update['androidurl_xiaoxue'];
		} else if($from == 7) {
			$newversion = $update['android_xiaoxue-hd'];
			$newurl = $update['androidurl_xiaoxue-hd'];
		} else {
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
	 *根据微信code获取微信openid
	 */
	private function getwxopidbycode($code = 0){
		$wechatObj = Ebh::app()->lib('WechatCallback');//得到微信扩展类的实例
		//根据带过来的code参数获取微信号的wxopid
		return $wechatObj->getopenidbycode($code);
	}

	//获取用户等级
	public function getRank($user = array()){
		if(empty($user)){
			return '';
		}
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
}
