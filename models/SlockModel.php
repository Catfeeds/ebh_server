<?php
/*
*锁屏模型
*/
class SlockModel extends CModel{
	//获取锁屏记录
	public function getIList($param = array()){
		$sql = 'select s.lockid,s.title,s.startdate,s.enddate from ebh_classslock cs join ebh_slock s on cs.lockid = s.lockid';
		$whereArr = array();
		if(!empty($param['skey_in'])){
			$whereArr[] = 'cs.skey in (\''.implode('\',\'', $param['skey_in']).'\')';
		}
		if(!empty($param['date_between'])){
			$whereArr[] = 's.startdate < '.$param['date_between'].' AND s.enddate > '.$param['date_between'];
		}
		if(!empty($whereArr)){
			$sql .= ' WHERE '.implode(' AND ', $whereArr);
		}
		return $this->db->query($sql)->list_array();
	}
	//获取锁屏条数
	public function getIListCount($param = array()){
		$sql = 'select count(1) as count from ebh_classslock cs join ebh_slock s on cs.lockid = s.lockid';
		$whereArr = array();
		if(!empty($param['skey_in'])){
			$whereArr[] = 'cs.skey in (\''.implode('\',\'', $param['skey_in']).'\')';
		}
		if(!empty($param['date_between'])){
			$whereArr[] = 's.startdate < '.$param['date_between'].' AND s.enddate > '.$param['date_between'];
		}
		if(!empty($whereArr)){
			$sql .= ' WHERE '.implode(' AND ', $whereArr);
		}
		$res = $this->db->query($sql)->row_array();
		return $res['count'];
	}
}