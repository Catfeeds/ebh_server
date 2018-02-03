<?php
/**
 *sns心情发布模型类
 */
class MoodsModel extends CModel {
	private $snsdb = null;
	public function __construct(){
		parent::__construct();
		$this->snsdb = Ebh::app()->getOtherDb("snsdb");
	}
	public function add($param){
		$setarr = array();
		if(!empty($param['content'])){
			$setarr['content'] = $this->db->escape_str($param['content']);
		}
		if(!empty($param['images'])){
			$setarr['images'] = $param['images'];
		}
		if(!empty($param['uid'])){
			$setarr['uid'] = $param['uid'];
		}
		if(!empty($param['dateline'])){
			$setarr['dateline'] = $param['dateline'];
		}
		if(!empty($param['permission'])){
			$setarr['permission'] = $param['permission'];
		}
		if(!empty($param['images'])){
			$setarr['images'] = $param['images'];
		}
		if(isset($param['status'])){
			$setarr['status'] = $param['status'];
		}
		if(isset($param['ip'])){
			$setarr['ip'] = $param['ip'];
		}
		return $this->snsdb->insert("ebh_sns_moods",$setarr);
	}
	//获取一条心情
	public function getmoodsbyfid($mid){
		$sql = "select mid, uid, content, images, dateline from ebh_sns_moods where mid = $mid";
		return $this->snsdb->query($sql)->row_array();
	}
}