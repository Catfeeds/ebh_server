<?php
/**
 * 用户设备登录信息记录表
 */
class UserclientModel extends CModel{
	/*
	设备绑定信息
	@param array $param
	@return int
	*/
	public function add($param){
		if(empty($param['uid']))
			return FALSE;
		$setarr = array();
		$setarr['uid'] = $param['uid'];
		if(!empty($param['crid']))
			$setarr['crid'] = $param['crid'];
		if(!empty($param['ismobile']))
			$setarr['ismobile'] = $param['ismobile'];
		if(!empty($param['system']))
			$setarr['system'] = $param['system'];
		if(!empty($param['browser']))
			$setarr['browser'] = $param['browser'];
		if(!empty($param['broversion']))
			$setarr['broversion'] = $param['broversion'];
		if(!empty($param['screen']))
			$setarr['screen'] = $param['screen'];
		if(!empty($param['ip']))
			$setarr['ip'] = $param['ip'];
		if(!empty($param['dateline']))
			$setarr['dateline'] = $param['dateline'];
		if(!empty($param['lasttime']))
			$setarr['lasttime'] = $param['lasttime'];
		if(!empty($param['isext']))
			$setarr['isext'] = $param['isext'];
		if(empty($setarr))
			return FALSE;
		$clientid = $this->db->insert('ebh_userclients',$setarr);
		return $clientid;
	}
	/**
	*编辑绑定信息，主要编辑绑定的时间
	*/
	public function update($param){
		if(empty($param['clientid']))
			return FALSE;
		$setarr = array();
		if(!empty($param['browser']))
			$setarr['browser'] = $param['browser'];
		if(!empty($param['broversion']))
			$setarr['broversion'] = $param['broversion'];
		if(!empty($param['screen']))
			$setarr['screen'] = $param['screen'];
		if(!empty($param['ip']))
			$setarr['ip'] = $param['ip'];
		if(!empty($param['dateline']))
			$setarr['dateline'] = $param['dateline'];
		if(!empty($param['lasttime']))
			$setarr['lasttime'] = $param['lasttime'];
		if(isset($param['isext']))
			$setarr['isext'] = $param['isext'];
		if(empty($setarr))
			return FALSE;
		$wherearr = array('clientid' => $param['clientid']);
		return $this->db->update('ebh_userclients', $setarr, $wherearr);
	}
	/**
	* 根据用户编号获取用户设备绑定信息
	* @param int $uid 用户uid
	*/
	public function getClientsByUid($uid,$crid) {
		$sql = "select clientid,crid,ismobile,system,browser,broversion,screen,ip,dateline,lasttime,isext from ebh_userclients where uid=$uid and crid=$crid";
		return $this->db->query($sql)->list_array();
	}
}