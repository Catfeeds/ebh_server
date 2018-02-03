<?php
/**
 *sns删除处理模型类
 */
class DelsModel extends CModel {
	/**
	 * 新增一条删除
	 */
	private $snsdb = null;
	
	public function __construct(){
		parent::__construct();
		$snsdb = Ebh::app()->getOtherDb("snsdb");
		$this->snsdb = $snsdb;
	}
	public function add($param){
		if(!empty($param['toid'])){
			$setarr['toid'] = $param['toid'];
		}
		if(!empty($param['uid'])){
			$setarr['uid'] = $param['uid'];
		}
		if(!empty($param['type'])){
			$setarr['type'] = $param['type'];
		}
		if(!empty($param['dateline'])){
			$setarr['dateline'] = $param['dateline'];
		}
		return $this->snsdb->insert("ebh_sns_dels",$setarr);
	}
	/**
	 * 检测动态时候被删除
	 */
	public function checkfeedsdelete($fid){
		$sql = "select count(*) count  from ebh_sns_dels where toid = $fid and type = 1 ";
		$row = $this->snsdb->query($sql)->row_array();
		if($row['count']>0){
			return true;
		}else{
			return false;
		}
	}
}