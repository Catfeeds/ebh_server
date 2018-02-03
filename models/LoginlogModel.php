<?php
/**
 *用户登录日志 
 */
class LoginlogModel extends CModel {
	/*
	添加日志
	@param array $param
	@return int
	*/
	public function addLog($param){
		if(empty($param['uid']) || empty($param['crid'])){
			return FALSE;
		}
		$sql = 'select dateline,browser,system from ebh_loginlogs where uid='.$param['uid'].' and crid='.$param['crid'];
		$sql.= ' order by logid desc limit 1';
		$log = $this->db->query($sql)->row_array();
		if(!empty($log) && $log['dateline'] >= SYSTIME-1 && $log['system'] == $param['system'] && $log['browser'] == $param['browser']){//间隔太短的不计
			return FALSE;
		}
			
		$setarr = array();
		if(!empty($param['ip']))
			$setarr['ip'] = $param['ip'];
		if(!empty($param['system']))
			$setarr['system'] = $param['system'];
		if(!empty($param['systemversion']))
			$setarr['systemversion'] = $param['systemversion'];
		if(!empty($param['browser']))
			$setarr['browser'] = $param['browser'];
		if(!empty($param['broversion']))
			$setarr['broversion'] = $param['broversion'];
		if(!empty($param['screen']))
			$setarr['screen'] = $param['screen'];
		if(!empty($param['citycode']))
			$setarr['citycode'] = $param['citycode'];
		if(!empty($param['parentcode']))
			$setarr['parentcode'] = $param['parentcode'];
		if(!empty($param['ismobile']))
			$setarr['ismobile'] = $param['ismobile'];
		if(!empty($param['isp']))
			$setarr['isp'] = $param['isp'];
		$setarr['dateline'] = SYSTIME;
		$setarr['crid'] = $param['crid'];
		$setarr['uid'] = $param['uid'];
		$logid = $this->db->insert('ebh_loginlogs',$setarr);
		return $logid;
	}

    /*
    添加注册日志
    @param array $param
    @return int
    */
    public function addOneRegisterLog($param){
        if(empty($param['uid'])){
            return FALSE;
        }
        $sql = 'select logid,uid from ebh_loginlogs where uid='.$param['uid'];
        $log = $this->db->query($sql)->row_array();
        if(!empty($log)){//注册记录已经存在则不记录
            return FALSE;
        }

        $setarr = array();
        if(!empty($param['ip']))
            $setarr['ip'] = $param['ip'];
        if(!empty($param['system']))
            $setarr['system'] = $param['system'];
        if(!empty($param['systemversion']))
            $setarr['systemversion'] = $param['systemversion'];
        if(!empty($param['browser']))
            $setarr['browser'] = $param['browser'];
        if(!empty($param['broversion']))
            $setarr['broversion'] = $param['broversion'];
        if(!empty($param['screen']))
            $setarr['screen'] = $param['screen'];
        if(!empty($param['citycode']))
            $setarr['citycode'] = $param['citycode'];
        if(!empty($param['parentcode']))
            $setarr['parentcode'] = $param['parentcode'];
        if(!empty($param['ismobile']))
            $setarr['ismobile'] = $param['ismobile'];
        if(!empty($param['parentcode']))
            $setarr['parentcode'] = $param['parentcode'];
        if(!empty($param['citycode']))
            $setarr['citycode'] = $param['citycode'];
        if(!empty($param['isp']))
            $setarr['isp'] = $param['isp'];
        if(!empty($param['logtype']))
            $setarr['logtype'] = $param['logtype'];
        if(!empty($param['othertype']))
            $setarr['othertype'] = $param['othertype'];
        if(!empty($param['crid']))
            $setarr['crid'] = $param['crid'];
        $setarr['dateline'] = SYSTIME;
        $setarr['uid'] = $param['uid'];
        $this->db->insert('ebh_loginlogs',$setarr);
    }

	/*
	根据区域名称查询信息
	*/
	public function getCityByName($cityname){
		if(empty($cityname)){
			return FALSE;
		}
		$sql = 'select citycode from ebh_cities where cityname like \''.$cityname.'%\'';
		return $this->db->query($sql)->row_array();
	}
}
