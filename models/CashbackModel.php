<?php
/**
 * 返现记录模型类
*/
class CashbackModel extends CModel{
	//新增一条返现记录
	public function add($param){
		$setarr = array();
		if(!empty($param['uid'])){
			$setarr['uid'] = $param['uid'];
		}
		if(!empty($param['reward'])){
			$setarr['reward'] = $param['reward']; 
		}
		if(!empty($param['time'])){
			$setarr['time'] = $param['time'];
		}
		if(!empty($param['synctime'])){
			$setarr['synctime'] = $param['synctime'];
		}
		if(!empty($param['crname'])){
			$setarr['crname'] = $param['crname'];
		}
		if(!empty($param['fromcrid'])){
			$setarr['fromcrid'] = $param['fromcrid'];
		}
		if(!empty($param['fromuid'])){
			$setarr['fromuid'] = $param['fromuid'];	
		}
		if(!empty($param['fromname'])){
			$setarr['fromname'] = $param['fromname'];
		}
		if(!empty($param['fromip'])){
			$setarr['fromip'] = $param['fromip'];
		}
		if(!empty($param['servicestxt'])){
			$setarr['servicestxt'] = $param['servicestxt'];
		}
		if(isset($param['status'])){
			$setarr['status'] = $param['status'];
		}
		return $this->db->insert('ebh_cashback',$setarr);
	}
	//获取返现记录列表
	public function getCashbackList($param){
		$sql = "SELECT id, time, fromname, fromcrid, crname, servicestxt, reward, status FROM `ebh_cashback` c";
		$wherearr = array();
		if(!empty($param['uid'])){
			$wherearr[] = ' c.uid = '.$param['uid'];
		}
		if(!empty($param['fromname'])){
			$wherearr[] = ' c.fromname LIKE \'%'.$param['fromname'].'%\'';
		}
		if(!empty($param['crname'])){
			$wherearr[] = ' c.crname LIKE \'%'.$param['crname'].'%\'';
		}
		if(isset($param['status'])){
			$wherearr[] = ' c.status = '.$param['status'];
		}
		if(!empty($wherearr)) {
			$sql .= ' WHERE '.implode(' AND ',$wherearr);
		}
		if(!empty($param['orderby'])) {
			$sql .= ' ORDER BY '.$param['orderby'];
		} else {
			$sql .= ' ORDER BY c.uid, c.time DESC';
		}
		if(!empty($param['limit'])) {
			$sql .= ' limit '. $param['limit'];
		}
		return $this->db->query($sql)->list_array();
	}
	//获取返现记录条目
	public function getCashbackcount($param){
		$sql = "SELECT count(*) count FROM `ebh_cashback` c";
		$wherearr = array();
		if(!empty($param['uid'])){
			$wherearr[] = ' c.uid = '.$param['uid'];
		}
		if(!empty($param['fromname'])){
			$wherearr[] = ' c.fromname LIKE \'%'.$param['fromname'].'%\'';
		}
		if(!empty($param['crname'])){
			$wherearr[] = ' c.crname LIKE \'%'.$param['crname'].'%\'';
		}
		if(isset($param['status'])){
			$wherearr[] = ' c.status = '.$param['status'];
		}
		if(!empty($wherearr)) {
			$sql .= ' WHERE '.implode(' AND ',$wherearr);
		}
		$row = $this->db->query($sql)->row_array();
		return empty($row) ? 0 : $row['count'];
	}
	//统计返现奖励总额
	public function getCashbackreward($param){
		if(empty($param['uid'])){
			return false;
		}
		$sql = "SELECT SUM(reward) AS total FROM `ebh_cashback` WHERE uid = ".$param['uid'];
		if(isset($param['status'])){
			$sql .= " AND status = ".$param['status'];
		}
		$row = $this->db->query($sql)->row_array();
		$total = empty($row) ? 0 : $row['total'];
		return $total;
	}
	//获取用户未入账金额列表
	public function getunrecorded($param){
		$sql = "SELECT id, uid, reward as totalreward FROM `ebh_cashback` WHERE status = 0";
		if(!empty($param['difftime'])){
			$sql .= " AND (`time` + ". $param['difftime'] .") <= ".$param['nowtime'];
		}
		if(!empty($param['orderby'])) {
			$sql .= ' ORDER BY '.$param['orderby'];
		} else {
			$sql .= ' ORDER BY uid, time DESC';
		}
		if(!empty($param['limit'])) {
			$sql .= ' limit '. $param['limit'];
		}
		return $this->db->query($sql)->list_array();
	}
	//更新返现记录用户状态
	public function updatestate($param){
		if(empty($param['uid']) || empty($param['money']) || !isset($param['status'])){
			return false;
		}
		$this->db->begin_trans();
		$res1 = $this->db->update('ebh_users',array(),'uid = '.$param['uid'],array('balance'=>'balance + '.$param['money']));
		$res2 = $this->db->update('ebh_cashback',array('status'=>1,'synctime'=>$param['synctime']),'id = '.$param['id']);
		if (!$res1 || !$res2) {
			$this->db->rollback_trans();
			return false;
		} else {
			$this->db->commit_trans();
		}
		return true;
	}
}