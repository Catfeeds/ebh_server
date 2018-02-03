<?php
/**
 *sns收件箱model
 */
class InboxModel extends CModel {
	/**
	 * inbox添加
	 */
	private $snsdb = null;
	
	public function __construct(){
		parent::__construct();
		$snsdb = Ebh::app()->getOtherDb("snsdb");
		$this->snsdb = $snsdb;
	}
	public function add($param){
		$setarr = array();
		if(!empty($param['fid'])){
			$setarr['fid'] = $param['fid'];
		}
		if(!empty($param['uid'])){
			$setarr['uid'] = $param['uid'];
		}
		if(!empty($param['fromuid'])){
			$setarr['fromuid'] = $param['fromuid'];
		}

		if(!empty($param['dateline'])){
			$setarr['dateline'] = $param['dateline'];
		}
		
		return  $this->snsdb->insert("ebh_sns_inboxs",$setarr);
	}
	
	/**
	 * 查询收件箱
	 */
	public function getinboxlist($param){
		$where = array();
		$sql = "select inid,fid,uid,fromuid,upcount,cmcount,zhcount,dateline from ebh_sns_inboxs ";
		if(!empty($param['uid'])){
			$where[] = " uid = ".$param['uid'];
		}
		if(!empty($param['uidarr'])){
			$where[] = " uid in (  ".explode(",", $param['uidarr'])." )";
		}
		if(!empty($param['fromuid'])){
			$where[] = " fromuid = ".$param['fromuid'];
		}
		if(!empty($where)){
			$sql.=" WHERE ".implode(" AND ", $where);
		}
		
		return $this->snsdb->query($sql)->list_array();
	}


}