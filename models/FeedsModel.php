<?php
/**
 *动态feeds模型
 */
class FeedsModel extends CModel {
	private $snsdb = null;
	public function __construct(){
		parent::__construct();
		$this->snsdb = Ebh::app()->getOtherDb("snsdb");
	}
	
	/**
	 * feeds添加
	 */
	public function add($param){
		$setarr = array();
	
		if(!empty($param['fromuid'])){
			$setarr['fromuid'] = $param['fromuid'];
		}
		if(!empty($param['message'])){
			$setarr['message'] = $param['message'];
		}
		if(!empty($param['category'])){
			$setarr['category'] = $param['category'];
		}
		if(!empty($param['toid'])){
			$setarr['toid'] = $param['toid'];
		}
		if(!empty($param['dateline'])){
			$setarr['dateline'] = $param['dateline'];
		}
		if(!empty($param['ip'])){
			$setarr['ip'] = $param['ip'];
		}
		return  $this->snsdb->insert("ebh_sns_feeds",$setarr);
	}
	/**
	 * 查询feeds
	 */
	public function getfeedslist($param){
		$where = array();
		$sql = "select fid,fromuid,message,category,toid,dateline from ebh_sns_feeds";
		if(!empty($param['fid'])){
			$where[] = " fid = ".$param['fid'];
		}
		if(!empty($param['fidarr'])){
			$where[] = " fid in( ".implode(",", $param['fidarr'])." )";
		}
		if(!empty($param['condition'])){
			$where[] = $param['condition'];
		}
		
		//过滤删除 不符合要求的动态
		$where[] = " status = 0";
		
		if(!empty($where)){
			$sql.=" where ".implode(" AND ",$where);
		}
		
		if(!empty($param['orderbby'])){
			$sql.=" ORDER BY ".$param['orderbby'];
		}else{
			$sql.=" ORDER BY  fid DESC";
		}
		
		if(!empty($param['limit'])){
			$sql.=" LIMIT ".$param['limit'];
		}else{
			$sql.=" LIMIT 10 ";
		}
		//echo $sql;
		return $this->snsdb->query($sql)->list_array();
		
	}

	/**
	 * 查询feeds数量
	 * @param unknown $param
	 */
	public function getfeedscount($param){
		$where = array();
		$sql = "select count(*) count from ebh_sns_feeds";
		
		if(empty($param['fid'])&&empty($param['fidarr'])){
			return 0;
		}else{
			if(!empty($param['fid'])){
				$where[] = " fid = ".$param['fid'];
			}
			if(!empty($param['fidarr'])){
				$where[] = " fid in( ".implode(",", $param['fidarr'])." )";
			}
		}
		
		if(!empty($param['condition'])){
			$where[] = $param['condition'];
		}
		
		//过滤删除 不符合要求的动态
		$where[] = " status = 0";
		
		if(!empty($where)){
			$sql.=" where ".implode(" AND ",$where);
		}
		//echo $sql;
		$row =  $this->snsdb->query($sql)->row_array();
		if(empty($row['count'])){
			$row['count'] = 0;
		}
		//dump($row['count']);
		return  $row['count'];
	}
	
	/**
	 * 获取一条动态信息
	 */
	public function getfeedsbyfid($fid){
		$sql = "select f.fid,f.fromuid,f.message,f.category,f.toid,f.dateline,b.outid,b.pfid,b.tfid,b.upcount,b.cmcount,b.zhcount,b.iszhuan from ebh_sns_feeds f left join ebh_sns_outboxs b on f.fid = b.fid where f.fid = $fid and f.status = 0 ";
		return $this->snsdb->query($sql)->row_array();
	}
}