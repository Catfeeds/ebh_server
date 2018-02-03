<?php
/**
 *sns通知类模型
 */
class NoticesModel extends CModel {
	//获取通知
	private $snsdb = null;
	public function __construct(){
		parent::__construct();
		$snsdb = Ebh::app()->getOtherDb("snsdb");
		$this->snsdb = $snsdb;
	}
	public function getlist($param){
		$sql = "select nid, fromuid, touid, isread, isdel, category, toid, message, type, dateline from ebh_sns_notices";
		if(!empty($param['nid'])){
			$where[] = 'nid = '.$param['nid'];
		}
		if(!empty($param['fromuid'])){
			$where[] = 'fromuid = '.$param['fromuid'];
		}
		if(!empty($param['touid'])){
			$where[] = 'touid = '.$param['touid'];
		}
		if(isset($param['isread'])){
			$where[] = 'isread = '.$param['isread'];
		}
		if(isset($param['isdel'])){
			$where[] = 'isdel = '.$param['isdel'];
		}
		if(!empty($param['category'])){
			$where[] = 'category = '.$param['category'];
		}
		if(!empty($param['toid'])){
			$where[] = 'toid = '.$param['toid'];
		}
		if(!empty($param['type'])){
			$where[] = 'type = '.$param['type'];
		}
		if(!empty($where)){
			$sql.= " WHERE ".implode(" AND ",  $where);
		}
		if(!empty($param['orderbby'])){
			$sql .=" ORDER BY ".$param['orderbby'];
		}else{
			$sql .=" ORDER BY isread ASC, dateline DESC";
		}
		if(!empty($param['limit'])){
			$sql .=" LIMIT ".$param['limit'];
		}
		return $this->snsdb->query($sql)->list_array();
	}
	//增加通知
	public function add($param){
		if(!empty($param['fromuid'])){
			$setarr['fromuid'] = $param['fromuid'];		
		}
		if(!empty($param['touid'])){
			$setarr['touid'] = $param['touid'];
		}
		if(!empty($param['isread'])){
			$setarr['isread'] = $param['isread'];
		}
		if(!empty($param['isdel'])){
			$setarr['isdel'] = $param['isdel'];
		}
		if(!empty($param['category'])){
			$setarr['category'] = $param['category'];
		}
		if(!empty($param['toid'])){
			$setarr['toid'] = $param['toid'];
		}
		if(!empty($param['message'])){
			$setarr['message'] = $param['message'];
		}
		if(!empty($param['type'])){
			$setarr['type'] = $param['type']; 
		}
		if(!empty($param['dateline'])){
			$setarr['dateline'] = $param['dateline'];
		}
		return $this->snsdb->insert("ebh_sns_notices",$setarr);
	}
	//更新通知
	public function update($param,$wharr){
		if(empty($wharr['uid'])){
			return false;
		}else{
			$where = ' touid = '.$wharr['uid'];
		}
		if(!empty($wharr['nids'])){
			$where .= " AND nid IN (".implode(',',$wharr['nids']).") ";
		}
		if(!empty($wharr['type'])){
			$where .= " AND type = ".$wharr['type'];
		}
		if(!empty($param['isread'])){
			$setarr['isread'] = $param['isread'];
		}
		if(!empty($param['isdel'])){
			$setarr['isdel'] = $param['isdel'];
		}
		return $this->snsdb->update("ebh_sns_notices",$setarr,$where);
	}
	//获取未删通知赞、关注、评论、转发数
	public function getcount($uid){
		$sql = "select type, count(*) count from ebh_sns_notices where touid = $uid and isdel=0 group by type";
		$list = $this->snsdb->query($sql)->list_array($sql);
		$arr[2] = $arr[3] = $arr[4] = $arr[5] = 0;
		if(!empty($list)){
			foreach ($list as $val){
				$arr[$val['type']] = $val['count'];
			}
		}
		return $arr;
	}
}