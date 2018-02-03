<?php
/**
 *班级学校动态model
 */
class ClassroomfeedsModel extends CModel {
	private $snsdb = null;
	public function __construct(){
		parent::__construct();
		$snsdb = Ebh::app()->getOtherDb("snsdb");
		$this->snsdb = $snsdb;
	}
	/**
	 * class_feeds添加
	 */
	public function addclassfeeds($param){
		$setarr = array();
		if(!empty($param['fid'])){
			$setarr['fid'] = $param['fid'];
		}
		if(!empty($param['uid'])){
			$setarr['uid'] = $param['uid'];
		}
		if(!empty($param['classid'])){
			$setarr['classid'] = $param['classid'];
		}

		if(!empty($param['dateline'])){
			$setarr['dateline'] = $param['dateline'];
		}
	
		if(isset($param['status'])){
			$setarr['status'] = $param['status'];
		}
		
		return  $this->snsdb->insert("ebh_sns_classfeeds",$setarr);
	}
	
	/**
	 * room_feeds添加
	 * 
	 */
	public function addroomfeeds($param){
		$setarr = array();
		if(!empty($param['fid'])){
			$setarr['fid'] = $param['fid'];
		}
		if(!empty($param['uid'])){
			$setarr['uid'] = $param['uid'];
		}

		if(!empty($param['crid'])){
			$setarr['crid'] = $param['crid'];
		}
	
		if(!empty($param['dateline'])){
			$setarr['dateline'] = $param['dateline'];
		}
		if(isset($param['status'])){
			$setarr['status'] = $param['status'];
		}
		return  $this->snsdb->insert("ebh_sns_roomfeeds",$setarr);
	}
	
	
	/**
	 * 检测班级动态时候存在
	 */
	public function checkclassexist($uid,$fid,$classid){
		$bool = false;
		$sql = "select count(*) count from ebh_sns_classfeeds where uid = $uid and classid = $classid and fid = $fid ";
		$row = $this->snsdb->query($sql)->row_array();
		if(!empty($row) && ($row['count']>0)){
			$bool = true;
		}
		
		return $bool;
	}
	
	/**
	 * 检测班级动态时候存在
	 */
	public function checkroomexist($uid,$fid,$crid){
		$bool = false;
		$sql = "select count(*) count from ebh_sns_roomfeeds where uid = $uid and crid = $crid and fid = $fid ";
		$row = $this->snsdb->query($sql)->row_array();
		if(!empty($row) && ($row['count']>0)){
			$bool = true;
		}
		return $bool;
	}
	
	
	/**
	 * 网校/班级动态删除
	 */
	public function delroomandclassfeeds($fid,$uid){
		$arr = array('fid'=>$fid,$uid=>$uid);
		$del = array('status'=>1);
		$this->snsdb->update("ebh_sns_roomfeeds",$del,$arr);
		$this->snsdb->update("ebh_sns_classfeeds",$del,$arr);
	}
}