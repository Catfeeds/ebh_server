<?php
/*
日志
*/
class LogModel extends CModel{
	/*
	系统日志列表
	@param array $param
	@return array
	*/
	public function getsystemloglist($param){
		$sqlarr['select'] = 'select l.logid,l.message,l.fromip,l.message,l.toid,l.dateline,o.opname,u.username';
		$sqlarr['from'] = '';
		$sqlarr['from'].= ' from ebh_logs l 
			left join ebh_users u on u.uid=l.uid
			left join ebh_operations o on l.opid =o.opid
			';
		if(!empty($param['type'])){
		if($param['type']=='agent'){
			$sqlarr['select'].=',a.realname too';
			$sqlarr['from'].='left join ebh_agents a on a.agentid=l.toid';
		}
		elseif($param['type']=='ad'){
			$sqlarr['select'].=',i.subject too';
			$sqlarr['from'].='left join ebh_items i on i.itemid=l.toid';
		}
		elseif($param['type']=='classroom'){
			$sqlarr['select'].=',c.crname too';
			$sqlarr['from'].='left join ebh_classrooms c on c.crid=l.toid';
		}
		elseif($param['type']=='courseware'){
			$sqlarr['select'].=',c.title too';
			$sqlarr['from'].='left join ebh_coursewares c on c.cwid=l.toid';
		}
		elseif($param['type']=='teacher'){
			$sqlarr['select'].=',u2.realname too';
			$sqlarr['from'].='left join ebh_users u2 on u2.uid=l.toid';
		}
		elseif($param['type']=='member'){
			$sqlarr['select'].=',u2.realname too';
			$sqlarr['from'].='left join ebh_users u2 on u2.uid=l.toid';
		}
		elseif($param['type']=='roomcourse'){
			$sqlarr['select'].=',c.crname too';
			$sqlarr['from'].='left join ebh_classrooms c on c.crid=l.toid';
		}
		elseif($param['type']=='roomuser'){
			$sqlarr['select'].=',c.crname too';
			$sqlarr['from'].='left join ebh_classrooms c on c.crid=l.toid';
		}
		elseif($param['type']=='folder'){
			$sqlarr['select'].=',c.crname too';
			$sqlarr['from'].='left join ebh_folders f on f.folderid=l.toid ';
			$sqlarr['from'].='left join ebh_classrooms c on c.crid=f.crid';
		}
		elseif($param['type']=='apply'){
			$sqlarr['select'].=',a.realname too';
			$sqlarr['from'].='left join ebh_applys a on a.applyid=l.toid';
		}
		}
		if(!empty($param['q']))
			$wherearr[] = ' (l.message like \'%'. $this->db->escape_str($param['q']) .'%\' or u.username like \'%' . $this->db->escape_str($param['q']) .'%\')';
		if(!empty($param['type']))
			$wherearr[] = ' l.type=\''.$param['type'].'\'';
		if(!empty($param['toid']))
			$wherearr[] = ' l.toid='.intval($param['toid']);
		if(!empty($param['groupid']))
			$wherearr[] = ' l.groupid='.intval($param['groupid']);
		if(!empty($param['logid']))
			$wherearr[] = ' l.logid='.intval($param['logid']);
		if(!empty($param['opid']))
			$wherearr[] = ' l.opid='.intval($param['opid']);
		if(!empty($param['value']))
			$wherearr[] = ' l.value='.intval($param['value']);
		$sql = implode('',$sqlarr);
		if(!empty($wherearr))
			$sql.= ' WHERE '.implode(' AND ',$wherearr);
		
		$sql.=' order by logid desc';
		if(!empty($param['limit']))
			$sql.= ' limit ' . $param['limit'];
		return $this->db->query($sql)->list_array();
	}
	/*
	系统日志数量
	@param array $param
	@return int
	*/
	public function getsystemlogcount($param){
		$sql = 'select count(*) count from ebh_logs l left join ebh_users u on u.uid=l.uid ';
		if(!empty($param['q']))
			$wherearr[] = ' (l.message like \'%'. $this->db->escape_str($param['q']) .'%\' or u.username like \'%' . $this->db->escape_str($param['q']) .'%\')';
		if(!empty($param['type']))
			$wherearr[] = ' l.type=\''.$param['type'].'\'';
		if(!empty($param['toid']))
			$wherearr[] = ' l.toid='.intval($param['toid']);
		if(!empty($param['groupid']))
			$wherearr[] = ' l.groupid='.intval($param['groupid']);
		if(!empty($param['logid']))
			$wherearr[] = ' l.logid='.intval($param['logid']);
		if(!empty($param['opid']))
			$wherearr[] = ' l.opid='.intval($param['opid']);
		if(!empty($wherearr))
			$sql.= ' WHERE '.implode(' AND ',$wherearr);
		$count = $this->db->query($sql)->row_array();
		
		return $count['count'];
	}
	/*
	代理商日志
	*/
	public function getagentlogcount(){
		
	}

	/**
	 *根据logid删除对应记录
	 *@author zkq
	 *@param int $logid/array $logid
	 *@return bool
	 *$logid=array(1,2,3,4,5);或者$logid=6两种格式都兼容
	 *
	 */
	public function deleteByLogId($logid){
		$where='';
		if(is_scalar($logid)){
			$logid = array($logid);
		}
		if(is_array($logid)){
			foreach ($logid as $lv) {
				$where.=' logid = '.intval($lv).' or';
			}
		}
		$where = rtrim($where,'or');
		if($this->db->delete('ebh_logs',$where)!==false){
			return true;
		}else{
			return false;
		}
	}
	/**
	 *根据条件获取单条log记录,该方法调用$this->getsystemloglist()方法
	 *@author zkq
	 *@param array $param
	 */
	public function getOneLog($param = array()){
		$param['limit'] = '1';
		$res = $this->getsystemloglist($param);
		return $res[0];
	}
	/**
	 *新增一条log记录
	 *@author zkq
	 *@param array $param 
	 *@return bool
	 */
	public function _insert($param = array()){
		if(empty($param)){
			return false;
		}
		$setArr = array();
		$setArr['uid'] = isset($param['uid'])?intval($param['uid']):0;
		$setArr['toid'] = isset($param['toid'])?intval($param['toid']):0;
		$setArr['type'] = isset($param['type'])?$param['type']:'';
		$setArr['opid'] = isset($param['opid'])?intval($param['opid']):0;
		$setArr['message'] = isset($param['message'])?$param['message']:'';
		$setArr['value'] = isset($param['value'])?intval($param['value']):0;
		$setArr['credit'] = isset($param['credit'])?intval($param['credit']):0;
		$setArr['fromip'] = $param['fromip'];
		$setArr['dateline'] = isset($param['dateline'])?intval($param['dateline']):time();
		if($this->db->insert('ebh_logs',$setArr)!==false){
			return true;
		}else{
			return false;
		}
	}
	/**
	 *修改一条log记录
	 *@author zkq
	 *@param array $param 
	 *@return bool
	 */
	public function _update($param = array(),$where=array()){
		if(empty($where)){
			return false;
		}
		$setArr = array();
		if(isset($param['toid']))
			$setArr['toid'] = intval($param['toid']);
		if(isset($param['value']))
			$setArr['value'] = intval($param['value']);
		if(isset($param['message']))
			$setArr['message'] = intval($param['message']);
		if(isset($param['fromip']))
			$setArr['fromip'] = intval($param['fromip']);
		if(isset($param['dateline']))
			$setArr['dateline'] = intval($param['dateline']);
			
		// $setArr['toid'] = isset($param['toid'])?intval($param['toid']):0;
		// $setArr['value'] = isset($param['value'])?intval($param['value']):0;
		// $setArr['message'] = isset($param['message'])?$param['message']:'';
		// $setArr['fromip'] = isset($param['fromip'])?$param['fromip']:getip();
		// $setArr['dateline'] = isset($param['dateline'])?intval($param['dateline']):time();
		if($this->db->update('ebh_logs',$setArr,$where)!==false){
			return true;
		}else{
			return false;
		}
	}
}
?>