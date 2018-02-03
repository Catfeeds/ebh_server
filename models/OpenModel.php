<?php
/**
 *用户微信/微博/QQ授权登录绑定等
 */
class OpenModel extends CModel {
	
	/**
	 * 获取用户绑定信息
	 * @param unknown $unionid
	 */
	public function getUserInfoByWeixin($unionid){
		$sql = "select uid,wxunionid,wxopenid,wxopid,username,password from ebh_users u where u.wxunionid = '{$unionid}' ";
		$row = $this->db->query($sql)->row_array();
		return $row;
	}
	
	/**
	 * 绑定处理
	 * 对应 ebh_binds表
	 * $type qq,wx,sina
	 * $data 对应的开发平台昵称openid nickname等
	 */
	public function dobind($type,$data,$user){
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
	
}