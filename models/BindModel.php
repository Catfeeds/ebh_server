<?php
/**
 * 
 * @author eker
 *用户绑定(qq,mobile,wx,weibo,email)
 */
class BindModel extends CModel{

	/**
	 * 添加绑定
	 * @param unknown $param
	 */
	public function add($param){
		$setarr = array();
		if(empty($param)) 
			return false;
		if(!empty( $param['uid'])){
			$setarr['uid'] = $param['uid'];
		}
		if(isset($param['is_mobile'])){
			$setarr['is_mobile'] = $param['is_mobile'];
		}
		if(!empty( $param['mobile_str'])){
			$setarr['mobile_str'] = $param['mobile_str'];
		}
		if(isset($param['is_email'])){
			$setarr['is_email'] = $param['is_email'];
		}
		if(!empty( $param['email_str'])){
			$setarr['email_str'] = $param['email_str'];
		}
		if(isset($param['is_qq'])){
			$setarr['is_qq'] = $param['is_qq'];
		}
		if(!empty( $param['qq_str'])){
			$setarr['qq_str'] = $param['qq_str'];
		}
		if(isset($param['is_wx'])){
			$setarr['is_wx'] = $param['is_wx'];
		}
		if(!empty( $param['wx_str'])){
			$setarr['wx_str'] = $param['wx_str'];
		}
		if(isset($param['is_weibo'])){
			$setarr['is_weibo'] = $param['is_weibo'];
		}
		if(!empty( $param['weibo_str'])){
			$setarr['weibo_str'] = $param['weibo_str'];
		}
		if(isset($param['is_paypass'])){
			$setarr['is_paypass'] = $param['is_paypass'];
		}
		if(!empty( $param['paypass_str'])){
			$setarr['paypass_str'] = $param['paypass_str'];
		}
		if(isset($param['is_bank'])){
			$setarr['is_bank'] = $param['is_bank'];
		}
		if(isset( $param['bank_str'])){
			$setarr['bank_str'] = $param['bank_str'];
		}
		return $this->db->insert('ebh_binds',$setarr);
	}
	
	/**
	 * 修改
	 * @param unknown $param
	 * @param unknown $uid
	 * @return boolean
	 */
	public function update($param,$uid){
		$setarr = array();
		if(empty($param)||$uid<0||!is_numeric($uid))
			return false;
		if(isset($param['is_mobile'])){
			$setarr['is_mobile'] = $param['is_mobile'];
		}
		if(!empty( $param['mobile_str'])){
			$setarr['mobile_str'] = $param['mobile_str'];
		}
		if(isset($param['is_email'])){
			$setarr['is_email'] = $param['is_email'];
		}
		if(!empty( $param['email_str'])){
			$setarr['email_str'] = $param['email_str'];
		}
		if(isset($param['is_qq'])){
			$setarr['is_qq'] = $param['is_qq'];
		}
		if(isset( $param['qq_str'])){
			$setarr['qq_str'] = $param['qq_str'];
		}
		if(isset($param['is_wx'])){
			$setarr['is_wx'] = $param['is_wx'];
		}
		if(isset( $param['wx_str'])){
			$setarr['wx_str'] = $param['wx_str'];
		}
		if(isset($param['is_weibo'])){
			$setarr['is_weibo'] = $param['is_weibo'];
		}
		if(isset( $param['weibo_str'])){
			$setarr['weibo_str'] = $param['weibo_str'];
		}
		if(isset($param['is_paypass'])){
			$setarr['is_paypass'] = $param['is_paypass'];
		}
		if(!empty( $param['paypass_str'])){
			$setarr['paypass_str'] = $param['paypass_str'];
		}
		if(isset($param['is_bank'])){
			$setarr['is_bank'] = $param['is_bank'];
		}
		if(isset( $param['bank_str'])){
			$setarr['bank_str'] = $param['bank_str'];
		}
		return $this->db->update('ebh_binds',$setarr,array('uid'=>$uid));
	}
	
	/**
	 * 查找一个用户的绑定信息
	 * @param unknown $uid
	 */
	public function getUserBInd($uid){
		$sql = "select u.username,u.realname,u.qqopid,u.sinaopid,u.email,u.mobile,u.wxopenid,b.* from ebh_users u 
					left join ebh_binds b on b.uid = u.uid 
				where u.uid = $uid
				";
		//echo $sql;
		$row = $this->db->query($sql)->row_array();
		return $row;
	}
	
	/**
	 * 绑定处理
	 */
	public function doBind($param,$uid){
		$ret =  false;
		if(empty($param)||$uid<0||!is_numeric($uid))
			return false;
		$row = $this->getUserBInd($uid);
		//log_message(var_export($row,true));
		if(!empty($row['bid'])){//修改
			$ret = $this->update($param, $uid);
		}else{//添加
			$ret = $this->add($param);
		}
		return $ret;
	}
	
	/**
	 * 获取一个用户银行卡绑定信息
	 */
	public function getUserBankInfo($uid){
		if(empty($uid)){
			return array();
		}
		$sql = 'select bid, is_bank, bank_str from ebh_binds where uid = '.$uid;
		return $this->db->query($sql)->row_array();
	}
	
	/**
	 * 绑定银行卡处理
	 */
	public function bindBank($param,$uid){
		$ret = -2;
		if(empty($param)||$uid<0||!is_numeric($uid)){
			return -2;
		}
		$row = $this->getUserBankInfo($uid);
		if(!empty($row)){
			$bank_str = $row['bank_str'];
			$is_bank = $row['is_bank'];
			$bank = array();
			if($is_bank == 1 && !empty($bank_str)){
				$bank_arr = json_decode($bank_str,true);
				$bank = $bank_arr['bank'];	//原银行信息
			}
			if(count($bank) == 10){
				return -3;
			}
			$tmpbank = json_decode($param['bank_str'],true);
			$thebank = $tmpbank['bank'];
			
			$flag = false;
			foreach ($bank as $item){
				if($item['bindex'] == $thebank[0]['bindex']){
					$flag = true;
					break;
				}
			}
			//同一类型卡已绑定则忽略
			if($flag == true){
				return -4;	
			}
			$bank[] = $thebank[0];
			//更新
			$param['bank_str'] = json_encode(array('uid'=>$param['uid'],dateline=>SYSTIME,'bank'=>$bank));
			$ret = $this->update($param, $uid);
		}else{
			$ret = $this->add($param);
		}
		return $ret;
	}
	
	/**
	 * 解绑处理
	 */
	public function doUnbind($type,$uid){
		$ret =  false;
		if($type=='qq'){//qq解绑
			$param = array(
				'is_qq'=>0,
				'qq_str'=>''	
			);
			$ret = $this->update($param, $uid);
			//删除openid
			if($ret){
				$this->db->update("ebh_users",array('qqopid'=>''),array('uid'=>$uid));
			}
		}elseif($type=='wx'){
			$param = array(
					'is_wx'=>0,
					'wx_str'=>''
			);
			$ret = $this->update($param, $uid);
			//删除openid
			if($ret){
				$this->db->update("ebh_users",array('wxopenid'=>'','wxopid'=>'','wxunionid'=>''),array('uid'=>$uid));
			}
			
		}elseif($type=='weibo'){
			$param = array(
					'is_weibo'=>0,
					'weibo_str'=>''
			);
			$ret = $this->update($param, $uid);
			//删除openid
			if($ret){
				$this->db->update("ebh_users",array('sinaopid'=>''),array('uid'=>$uid));
			}
			
		}
		
		return $ret;
	}
	
	/**
	 * 验证邮箱是否已经使用了
	 */
	public function checkemail($eamil){
		$sql = "select count(*) as count from ebh_users u left join ebh_binds b on u.uid = b.uid where b.is_email = 1 and u.email = '{$eamil}'";
		$row = $this->db->query($sql)->row_array();
		if($row['count']>0){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 验证手机号 是否已经绑定了
	 */
	public function checkmobile($mobile){
		$sql = "select count(*) as count from ebh_users u left join ebh_binds b on u.uid = b.uid where b.is_mobile = 1 and u.mobile = '{$mobile}'";
		$row = $this->db->query($sql)->row_array();
		if($row['count']>0){
			return true;
		}else{
			return false;
		}		
	}
	
	/**
	 * 验证支付密码与用户密码是否相同
	 */
	public function checksameuserpwd($uid,$paypassword){
		$ppwd = md5($paypassword);
		$sql = "select count(*) as count from ebh_users where uid = {$uid} and password = '{$ppwd}' ";
		$row = $this->db->query($sql)->row_array();
		return $row['count']>0 ? true : false;
	}
	
	/**
	 * 验证旧的支付密码
	 */
	public function checkoldpaypwd($oldpaypwd,$uid){
		$ppwd = md5($oldpaypwd);
		$sql = "select count(*) as count from ebh_users where uid = {$uid} and paypassword = '{$ppwd}' ";
		$row = $this->db->query($sql)->row_array();
		return $row['count']>0 ? true : false;
	}
	/**
	 * 绑定支付密码
	 */
	public function bindPaypwd($paypwd,$level,$uid){
		$ret = false;
		$param = array();
		$param['uid'] = $uid;
		$param['is_paypass'] = 1;
		$param['paypass_str'] = json_encode(
				array(
					'paypass'=>md5($paypwd),
					'level'=>$level,
					'dateline'=>SYSTIME
					)
				);
		$ret = $this->doBind($param, $uid);
		if(!empty($ret)){
			//更新用户表user
			$this->db->update("ebh_users",array('paypassword'=>md5($paypwd)),array('uid'=>$uid));
		}
		return $ret;
	}
	
	/**
	 * 检测账号是否绑定
	 */
	public function checkbind($openid){
		$sql = "select count(*)  as count from ebh_users where  qqopid = '{$openid}' OR sinaopid = '{$openid}' OR wxunionid = '{$openid}'";
		$row = $this->db->query($sql)->row_array();
		return $row['count']>0 ? true : false;
	}
}