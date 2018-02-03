<?php
/**
 *图片资源模型类
 */
class ImageModel extends CModel {
	private $snsdb = null;
	
	public function __construct(){
		parent::__construct();
		$snsdb = Ebh::app()->getOtherDb("snsdb");
		$this->snsdb = $snsdb;
	}
	public function add($param){
		$setarr = array();
		if(!empty($param['uid'])){
			$setarr['uid'] = $param['uid'];
		}
		if(!empty($param['path'])){
			$setarr['path'] = $param['path'];
		}
		if(!empty($param['sizes'])){
			$setarr['sizes'] = $param['sizes'];
		}
		if(!empty($param['dateline'])){
			$setarr['dateline'] = $param['dateline'];
		}
		if(!empty($param['ip'])){
			$setarr['ip'] = $param['ip'];
		}
		return $this->snsdb->insert("ebh_sns_images",$setarr);
	}
	//根据gid获取图片详情
	public function getimgs($arr){
		if(empty($arr)) return array();
		$where = array();
		$sql = "select gid, path, sizes, status from ebh_sns_images";
		if(!empty($arr)){
			$where['gid'] = 'gid in ('.implode(',',$arr).')';
		}
		if(!empty($where)){
			$sql.= " WHERE ".implode(" AND ",  $where);
		}
		return $this->snsdb->query($sql)->list_array();
	}
	public function delete($param){
		$where = array();
		if(!empty($param['uid'])){
			$where['uid'] = $param['uid'];	
		}
		if(!empty($param['gid'])){
			$where['gid'] = $param['gid'];
		}
		if(!empty($param['gids'])){
			$where = ' gid in ('.implode(',', $param['gids']).')';
		}
		return $this->snsdb->delete("ebh_sns_images",$where);
	}
	public function update($param,$gid){
		$setarr = array();
		if(!empty($param['path'])){
			$setarr['path'] = $param['path'];
		}
		if(!empty($param['sizes'])){
			$setarr['sizes'] = $param['sizes'];
		}
		if(!empty($param['dateline'])){
			$setarr['dateline'] = $param['dateline'];
		}
		if(is_array($gid)){
			$where = ' where gid in ('.implode(',', $gid).')';
		}else{
			$where = ' where gid = '.$gid;
		} 
		return $this->snsdb->update("ebh_sns_images",$setarr,$where);
	}
	public function addto($param,$gids){
		if(empty($gids) || empty($param['size'])){
			return false;
		}
		$sql = "update `ebh_sns_images` set `sizes` = CONCAT(`sizes`,',".$param['size']."') where gid in (".implode(',', $gids).")";
		return $this->snsdb->simple_query($sql);
	}
	//提取图片列表
	public function getimglist($param){
		$where = array();
		$sql = "select gid, path, sizes, status from ebh_sns_images";
		if(!empty($param['uid'])){
			$where[] = ' uid = '.$param['uid'];
		}
		if(!empty($param['begindate'])){
			$where[] = ' dateline>='.$param['begindate'];
		}
		if(!empty($param['eenddate'])){
			$where[] = ' dateline<'.$param['enddate'];
		}
		if(isset($param['status'])){
			$where[] = ' status = '.$param['status'];
		}
		if(!empty($where)){
			$sql .= ' WHERE '.implode(' AND ',$where);
		}
		if(!empty($param['orderbby'])){
			$sql .=" ORDER BY ".$param['orderbby'];
		}else{
			$sql .=" ORDER BY dateline DESC";
		}
		if(!empty($param['limit'])){
			$sql .=" LIMIT ".$param['limit'];
		}else{
			$sql .=" LIMIT 7 ";
		}
		return $this->snsdb->query($sql)->list_array();
	}
}