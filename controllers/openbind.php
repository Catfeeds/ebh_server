<?php

/**
 * 第三方登录绑定
 */
class OpenbindController extends CControl {
	/**
	 * 绑定或一键注册
	 */
	public function index() {
		$create = $this->input->post('create');
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$type = $this->input->post('type');
		$openid = $this->input->post('openid');
		$unionid = $this->input->post('unionid');
		$sex = $this->input->post('sex');
		$face = $this->input->post('face');
		$nickname = $this->input->post('nickname');

		$member = $this->model('member');
		$usermodel = $this->model('user');

		//先check
		$login_openid = ($type=='wx') ? $unionid :$openid;
		$userinfo = $usermodel->openlogin($login_openid,$type);
		if (!empty($userinfo)){
			$result['status'] = 1;
			$result['msg'] = '该第三方登录已有绑定账号';
			echo json_encode($result);
			exit;
		}


		//直接创建账号
		if($create == 1){


			if($type == 'qq'){
				$userData = array(
					'openid'=>$openid,
					'nickname'=>$nickname,
					'sex'=>$sex
				);
				$userinfo = $this->createUser($userData, 'qq');
			}elseif($type == 'wx'){
				$userData = array(
					'nickname'=>$nickname,
					'sex'=>$sex,
					'unionid'=>$unionid,
					'openid'=>$openid,
                    'face'=>$face
				);
				$userinfo = $this->createUser($userData, 'wx');
			}elseif($type == 'sina'){
				$userData = array(
					'openid'=>$openid,
					'nickname'=>$nickname,
					'sex'=>$sex
				);
				$userinfo = $this->createUser($userData, 'sina');
			}

		} else {//绑定账号

			//验证账号格式
			if(strlen($username)<6 || strlen($username)>20 || !preg_match("/^[a-zA-Z][a-z0-9A-Z_]{5,19}$/",$username)){
				$result['status'] = 1;
				$result['msg'] = '用户名格式不正确';
				echo json_encode($result);
				exit;
			}
			if(strlen($password)<6 || strlen($password) > 16){
				$result['status'] = 1;
				$result['msg'] = '密码格式不正确';
				echo json_encode($result);
				exit;
			}

			if($this->check($username,$password,$type,false,false)){
				$userinfo = $usermodel->getAssociateInfoByUsername($username);
				$uid = $userinfo['uid'];
				
				if($type == 'qq'){
					$data = array(
						'openid'=>$openid,
						'nickname'=>$nickname	
					);
					$this->dobind($type, $data, $userinfo);
				//	$usermodel->update(array('qqopid'=>$openid),$uid);
				}elseif($type == 'wx'){
					$data = array(
							'openid'=>$openid,
							'unionid'=>$unionid,
							'nickname'=>$nickname,
							'sex'=>$sex,
							'headimgurl'=>$face
					);
					$this->dobind($type, $data, $userinfo);
				//	$usermodel->update(array('wxopenid'=>$openid),$uid);
				}elseif($type == 'sina'){
					$data = array(
							'openid'=>$openid,
							'nickname'=>$nickname
					);
					$this->dobind($type, $data, $userinfo);
					
				//	$usermodel->update(array('sinaopid'=>$openid),$uid);
				}
			}else{
				$result['status'] = 2;
				$result['msg'] = '账号错误或该账号已绑定';
				echo json_encode($result);
				exit;
			}

		}

		if (!empty($userinfo)){
			$result['status'] = 0;
			$result['msg'] = '账号绑定成功';
			$result['k'] = $this->getKey($userinfo);
			echo json_encode($result);
			fastcgi_finish_request();
			if ($create == 1){
				$this->afterCreateUser($userinfo);
			}
		}

	}


	/**
	 * 验证用户名 密码
	 * @param unknown $username
	 * @param unknown $password
	 * @param string $ajax
	 * @param string $type
	 * @param string $create
	 */
	function check($username='',$password='',$type='qq',$ajax=true,$create = true){
		$flag = false;
		$retArr = array('code'=>0,'msg'=>'验证失败!!!');
		if($ajax){
			$post =  $this->input->post();
			$username = !empty($post['username'])?$post['username']:"";
			$type = !empty($post['type'])?$post['type']:"";
			$password = !empty($post['password'])?$post['password']:'';
			$create = isset($post['create'])?(bool)$post['create']:true;
		}
		$usermodel = $this->model('user');
		$user = $usermodel->getAssociateInfoByUsername($username);
		
		if(!empty($user)){
			//验证账号是否存在
			if($create){
				$retArr = array('code'=>0,'msg'=>'对不起，帐号已经被注册！');
				$flag = false;
			}else{
				$retArr = array('code'=>1,'msg'=>'');
				$flag = true;
			}
		
			//验证账号+密码
			if(!empty($password) && $create==false){
				if(md5($password) != $user['password']){
					$retArr = array('code'=>0,'msg'=>'对不起，密码错误！');
					$flag = false;
				}else{
					//验证是否已经绑定
					$openid = 'qqopid';
					switch ($type){
						case 'qq':$openid='qqopid';
						break;
						case 'sina':$openid='sinaopid';
						break;
						case 'wx':$openid='wxunionid';
						break;
					}
					if(!empty($user[$openid])){
						$retArr = array('code'=>0,'msg'=>'对不起，该帐号已经关联，请输入其他帐号！');
						$flag = false;
					}else{
						$retArr = array('code'=>1,'msg'=>'');
						$flag = true;
					}
				}
			}
			
		}else{
			if($create){
				$retArr = array('code'=>1,'msg'=>'');
				$flag = true;
			}else{
				$retArr = array('code'=>0,'msg'=>'对不起，您输入的帐号不存在！');
				$flag = false;
			}
		}
		
		//返回消息
		if($ajax){
			echo json_encode($retArr);
		}else{
			return $flag;
		}
		
	}
	
	/**
	 * 绑定处理
	 * 对应 ebh_binds表
	 * $type qq,wx,sina
	 * $data 对应的开发平台昵称openid nickname等
	 */
	private function dobind($type,$data,$user){
		$retflag = false;
		$bdmodel = $this->model("Bind");
		$umodel = $this->model("User");
		if($type=='qq'){//QQ
			$bdata =array(
					'uid'=>$user['uid'],
					'is_qq'=>1,
					'qq_str'=>json_encode(
							array(
								'qq'=>'',
								'uid'=>$user['uid'],
								'openid'=>	$data['openid'],
								'nickname'=>$data['nickname'],	
								'dateline'=>SYSTIME
							)
					)
				);
			//log_message(var_export($bdata,true));
			$retflag = $bdmodel->doBind($bdata,$user['uid']);
				
			//更新主表qqopid字段
			if(!empty($retflag)){
				$udata = array(
					'qqopid'=>$data['openid'],
				);
				$umodel->update($udata,$user['uid']);
			}
		}elseif($type=='wx'){//微信
			$bdata =array(
					'uid'=>$user['uid'],
					'is_wx'=>1,
					'wx_str'=>json_encode(
							array(
								'wx'=>'',
								'uid'=>$user['uid'],
								'openid'=>$data['openid'],
								'unionid'=>$data['unionid'],	
								'nickname'=>$data['nickname'],
								'dateline'=>SYSTIME,
								'from'=>'shaoma'
							)
						)
				);
			$retflag = $bdmodel->doBind($bdata,$user['uid']);
			
			//更新主表wxopenid字段
			if(!empty($retflag)){
				$udata = array(
						'wxunionid'=>$data['unionid'],
						'wxopenid'=>$data['openid']
				);
				$umodel->update($udata,$user['uid']);
			}

		}elseif($type=='sina'){//微博
			$bdata =array(
					'uid'=>$user['uid'],
					'is_weibo'=>1,
					'weibo_str'=>json_encode(
							array(
									'weibo'=>'',
									'uid'=>$user['uid'],
									'sinaopid'=>$data['openid'],
									'nickname'=>$data['nickname'],
									'dateline'=>SYSTIME
							)
					)
			);
			$retflag = $bdmodel->doBind($bdata,$user['uid']);
				
			//更新主表wxopenid字段
			if(!empty($retflag)){
				$udata = array(
						'sinaopid'=>$data['openid'],
				);
				$umodel->update($udata,$user['uid']);
			}
		}
		
		return $retflag;
	}
	

	/**
	 * 创建新用户
	 * $param 需要参数 openid,unionid(非必选),nickname,sex
	 */
	private function  createUser($param,$type){
		$sex = 0;//性别 0男  1女
		$openid = $param['openid'];
		$unionid =  !empty($param['unionid']) ? $param['unionid'] : '';
		$face =  !empty($param['face']) ? $param['face'] : '';
		$nickname = $param['nickname'];

		$data = array();
	
		if($type == 'qq'){
			$data['qqopid'] = $openid;
			$data['sex'] = $sex;
		}elseif($type == 'wx'){
			$data['wxunionid'] = $unionid;
			$data['wxopid'] = $openid;
			$data['sex'] = $param['sex'];
		}elseif($type=='sina'){
			$data['sinaopid'] = $openid;
			$data['sex'] = $param['sex'];
		}
		$mima = $this->generateStr(6);
		$username = $type.rand(1000,9999).$mima;
		$password = $mima;
		$data['username'] = $username;
		$data['mpassword'] = md5($password);
		$data['realname'] = shortstr($nickname,50);//昵称 存在 姓名字段
		$data['dateline'] = SYSTIME;

        $member = $this->model("Member");
        $uid = $member->addmember($data);
        if($uid>0){
            $cuser = array('uid'=>$uid);
            $datas = array('openid'=>$openid,'unionid'=>$unionid,'nickname'=>$nickname);
            $this->dobind($type, $datas, $cuser);
        }
        //微信注册后处理微信头像,生成缩略图并更新到用户数据库
        if($uid>0 && !empty($face)){
            $this->afterWxRegister($uid,$face);
        }
        //将注册信息记录到日志
        if ($uid>0 && !empty($type)){
            $this->afterUserRegister($uid,$type);
        }

        $user = array('uid'=>$uid,'realname'=>$data['realname'],'sex'=>$data['sex'],'username'=>$username,'password'=>md5($password),'openid'=>$openid,'unionid'=>$unionid);
        return $user;
	}
    /**
     * 用户注册后将注册信息记录到日志
     */
    public function afterUserRegister($uid,$type){
        $logdata = array();
        $logdata = $this->input->post();
        $logdata['uid'] = $uid;
        if($type == 'qq'){
            $logdata['othertype'] = 3;
        }elseif($type == 'wx'){
            $logdata['othertype'] = 2;
        }elseif($type=='sina'){
            $logdata['othertype'] = 1;
        }else{
            $logdata['othertype'] = 0;
        }
        $logdata['logtype'] = 6;  //6网校注册方式的创建用户
        $registerloglib = Ebh::app()->lib('RegisterLog');
        $registerloglib->addOneRegisterLog($logdata);
    }
    /**
     * 微信注册后处理微信头像,生成缩略图并更新到用户数据库
     */
    public function afterWxRegister($uid,$face){
        if($uid>0 && !empty($face)){
            $encodeuid =  authcode($uid,'ENCODE');
            $_UP = Ebh::app()->getConfig()->load('upconfig');
            if(!empty($_UP['avatar']['wxnotify'])){
                $wxnotify = $_UP['avatar']['wxnotify'];
                $res = do_post($wxnotify, array('uid'=>$encodeuid, 'face'=>$face), false);
                if(!empty($res->msg) && !empty($res->status) && $res->status== -1){
                    log_message($res->msg);
                }
            }
        }
    }

	/**
	 * 创建用户 指定到默认网校 默认班级 同步sns数据等
	 */
	public function afterCreateUser($user){
		$appconfig = Ebh::app()->getConfig()->load('appsetting');
		$democrid = $appconfig['democrid'];
		$democlassid = $appconfig['democlassid'];
		
		//添加用户到教室
		$param['crid'] = $democrid;
		$param['uid'] = $user['uid'];
		$param['cnname'] = $user['realname'];
		$param['sex'] = $user['sex'];
		$roomuser = $this->model('roomuser');
		$roomuser->insert($param);
		
		//添加用户到网校
		$param['classid'] = $democlassid;
		$classes = $this->model('classes');
		$classes->addclassstudent($param);
		
		//更新SNS的学校学生、班级学生缓存
		Ebh::app()->lib('XNums')->add('user');
		$snslib = Ebh::app()->lib('Sns');
		$snslib->updateClassUserCache(array('classid'=>$democlassid,'uid'=>$user['uid']));
		$snslib->updateRoomUserCache(array('crid'=>$democrid,'uid'=>$user['uid']));
		
		//调用SNS同步接口，类型为4用户网校操作
		$snslib->do_sync($user['uid'], 4);
		
	}
	
	/**
	 * 生成随机字符串
	 * @param number $length
	 * @return string
	 */
	private function generateStr( $length = 8 ) {
		// 密码字符集，可任意添加你需要的字符
		$chars = '0123456789';
	
		$str = '';
		for ( $i = 0; $i < $length; $i++ )
		{
			$str .= $chars[ mt_rand(0, strlen($chars) - 1) ];
		}
	
		return $str;
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