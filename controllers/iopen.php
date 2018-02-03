<?php
/**
 * wap手机端微信/微博/QQ处理接口
 * @author Eker
 * @email qq704855854@126.com
 *
 */
class IopenController extends CControl {
	
	/**
	 * 微信绑定处理
	 */
	public function weixin(){
		$unionid = @ $this->input->post('unionid');
		$openid = @ $this->input->post('openid');
		
		if(!empty($openid)){
			//先查询
			$userow = $this->model("Open")->getUserInfoByWeixin($unionid);
			if(empty($userow['wxopid'])){
				//该用户 微信扫码过来的 回写wxopid
				$this->model("User")->update(array('wxopid'=>$openid),$userow['uid']);
			}
			
			if(!empty($userow)){
				renderJson(1,'',$userow);
			}else{
				renderJson(2,'未绑定账号');
			}
		}else{
			renderJson(-1,'缺少openid');
		}
	}
	
	/**
	 * 微信账号绑定处理 --支持没有绑定过,创建新账号
	 * 
	 */
	public function weixinv2(){
    	$unionid = @ $this->input->post('unionid');
    	$openid = @ $this->input->post('openid');
    	
    	if(!empty($openid)){
    	    //先查询
    	    $userow = $this->model("Open")->getUserInfoByWeixin($unionid);
    	    if(empty($userow['wxopid'])){
    	        //该用户 微信扫码过来的 回写wxopid
    	        $this->model("User")->update(array('wxopid'=>$openid),$userow['uid']);
    	    }
    	    if(!empty($userow)){
    	        renderJson(1,'',$userow);
    	    }else{
    	        //创建账号
    	        $post = $this->input->post();
    	        $param = array(
    	            'nickname'=>$post['nickname'],
    	            'face'=>$post['face'],
    	            'sex'=>$post['sex'],
    	            'unionid'=>$post['unionid'],
    	            'openid'=>$post['openid'],
    	        );
    	        $userow = $this->createUser($param, 'wx');
    	        renderJson(1,'',$userow);
    	    }
    	}else{
    	    renderJson(-1,'缺少openid');
    	}
    }

    /**
     * 获取微信用户表信息
     * @param unknown $openid
     */
    public function getweixinuserinfo(){
        $openid = $this->input->post('openid');
        $userinfo = array($openid);
        if(!empty($openid)){
            $info = $this->model("Open")->getweixinInfo($openid);
            if(!empty($info)){
                renderJson(1,'',$info);
            }else{
                renderJson(-1,'用户信息不存在!');
            }
        }else{
            renderJson(-1,'缺少openid!');
        }
    }

    /**
	 * 创建新用户
	 */
	private function  createUser($param,$type){
		$sex = 0;//性别 0男  1女
		$openid = $param['openid'];
		$unionid = @ $param['unionid'];
		$nickname = $param['nickname'];
		$face = !empty($param['face']) ? $param['face'] : '';

		$data = array();
		if($type == 'qq'){
			$data['qqopid'] = $openid;
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
		$user = array('uid'=>$uid,'realname'=>$data['realname'],'sex'=>$data['sex'],'username'=>$username,'passwrod'=>$password,'openid'=>$openid,'unionid'=>$unionid);
		
		$this->afterCreateUser($user);
		
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
									'from'=>'gzh'
							)
					)
			);
			$retflag = $bdmodel->doBind($bdata,$user['uid']);
				
			//更新主表wxopenid字段
			if(!empty($retflag)){
				$udata = array(
						'wxunionid'=>$data['unionid'],
						'wxopid'=>$data['openid']//这个要注意下 公众号过来的是 wxopid这个字段 @eker 2016年5月18日10:41:21
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
}