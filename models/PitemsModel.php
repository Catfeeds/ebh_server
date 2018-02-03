<?php
/**
 *文章表模型
 */
class PitemsModel extends CPortalModel{
	public function getList($param = array(),$filter=false){
		$sql = 'select i.*,c.name,c.upid from ebh_pitems i left join ebh_pcategories c on i.catid = c.catid';
		$whereArr = array();
		if($filter){
			$whereArr[] = 'length(c.caturl)=0';
		}
		if(!empty($param['catid'])){
			$whereArr[] = 'i.catid = '.intval($param['catid']);
		}
		if(!empty($param['in'])){
			$whereArr[] = 'i.catid in ('.implode(',',$param['in']).')';
		}
		if(!empty($param['status'])){
			$whereArr[] = 'i.status ='.intval($param['status']);
		}
		if(!empty($param['hot'])){
			$whereArr[] = 'i.hot = '.intval($param['hot']);
		}
		if(!empty($param['top'])){
			$whereArr[] = 'i.top = '.intval($param['top']);
		}
		if(!empty($param['best'])){
			$whereArr[] = 'i.best = '.intval($param['best']);
		}
		if((!empty($param['thumb']))&&($param['thumb']===true)){
			$whereArr[] = 'i.thumb!=\'\'';
		}
		if(!empty($param['code'])){
			$whereArr[] = 'c.code=\''.$this->portaldb->escape_str($param['code']).'\'';
		}
		if(!empty($param['q'])){
			$whereArr[] = 'i.subject like \'%'.$this->portaldb->escape_str($param['q']).'%\'';
		}

		if(!empty($whereArr)){
			$sql.=' WHERE '.implode(' AND ',$whereArr);
		}
		if(!empty($param['order'])){
			$sql.=' order by '.$this->portaldb->escape_str($param['order']);
		}else{
			$sql.=' order by i.itemid,i.lastpost desc,i.dateline desc';
		}
		if(!empty($param['limit'])){
			$sql.=' limit '.$param['limit'];
		}
		return $this->portaldb->query($sql)->list_array();
	}

	public function getListCount($param = array(),$filter=false){
		$sql = 'select count(*) count from ebh_pitems i left join ebh_pcategories c on i.catid = c.catid';
		$whereArr = array();
		if($filter){
			$whereArr[] = 'length(c.caturl)=0';
		}
		
		if(!empty($param['catid'])){
			$whereArr[] = 'i.catid = '.intval($param['catid']);
		}
		if(!empty($param['in'])){
			$whereArr[] = 'i.catid in ('.implode(',',$param['in']).')';
		}
		if(!empty($param['status'])){
			$whereArr[] = 'i.status ='.intval($param['status']);
		}
		if(!empty($param['hot'])){
			$whereArr[] = 'i.hot = '.intval($param['hot']);
		}
		if(!empty($param['top'])){
			$whereArr[] = 'i.top = '.intval($param['top']);
		}
		if(!empty($param['best'])){
			$whereArr[] = 'i.best = '.intval($param['best']);
		}
		if((!empty($param['thumb']))&&($param['thumb']===true)){
			$whereArr[] = 'i.thumb!=\'\'';
		}
		if(!empty($param['q'])){
			$whereArr[] = 'i.subject like \'%'.$this->portaldb->escape_str($param['q']).'%\'';
		}
		if(!empty($whereArr)){
			$sql.=' WHERE '.implode(' AND ',$whereArr);
		}
		$res = $this->portaldb->query($sql)->row_array();
		return $res['count'];
	}

	public function _insert($param = array()){
		if(empty($param)){
			return 0;
		}
		$param = $this->portaldb->escape_str($param);
		return $this->portaldb->insert('ebh_pitems',$param);
	}

	public function _update($param = array(),$where){
		if(empty($param)||empty($where)){
			return 0;
		}
		$param = $this->portaldb->escape_str($param);
		$where = $this->portaldb->escape_str($where);
		return $this->portaldb->update('ebh_pitems',$param,$where);
	}

	public function getOneByItemid($itemid = 0){
		$sql = 'select i.* from ebh_pitems i where i.itemid='.intval($itemid).' limit 1';
		return $this->portaldb->query($sql)->row_array();
	}

	public function _delete($itemid){
		if(empty($itemid)){
			return 0;
		}
		if(is_numeric($itemid)){
			$where = array('itemid'=>$itemid);
		}else{
			$where = ' itemid in '.$itemid;
		}
		
		return $this->portaldb->delete('ebh_pitems',$where);
	}
	public function _query($sql){
		return $this->portaldb->query($sql)?1:0;
	}

	public function getEachFiveInfoByCateTree($cateTree = array(),$tag = ''){
		$newCateTree = array();
		foreach ($cateTree as $cate) {
			$newChildren = array();
			foreach ($cate['children'] as $child) {
				$child['articles'] = $this->getLimitedList($child['catid'],8,$tag);
				$newChildren[] = $child;
			}
			$cate['children'] = $newChildren;
			$newCateTree[] = $cate;
		}
		return $newCateTree;
	}

	public function getLimitedList($catid,$number = 5 ,$tag,$order = 'i.displayorder asc,i.itemid desc'){
		if(!empty($tag)){
			$tagInfo = explode('_', $tag);
			$key = $tagInfo[0];
			$value = $tagInfo[1];
			$sql = 'select i.* from ebh_pitems i left join ebh_pcategories c on i.catid = c.catid where i.catid = '.$catid.' AND '.$tagInfo[0].' = '.intval($tagInfo[1]).' order by '.$order.' limit '.$number;
		}else{
			$sql = 'select i.* from ebh_pitems i left join ebh_pcategories c on i.catid = c.catid where i.catid = '.$catid.'  order by '.$order.' limit '.$number;
		}
		
		return $this->portaldb->query($sql)->list_array();
	}
	/**
	 *文章浏览次数加一
	 */
	public function incViewNum($itemid = 0){
		return $this->portaldb->update('ebh_pitems',array(),array('itemid'=>intval($itemid)),array('viewnum'=>'viewnum+1'));
	}
	/**
	 *文章分享次数加一
	 */
	public function incShareNum($itemid = 0){
		return $this->portaldb->update('ebh_pitems',array(),array('itemid'=>intval($itemid)),array('sharenum'=>'sharenum+1'));
	}
	/**
	 *文章评论次数加一
	 */
	public function incReviewNum($itemid = 0){
		return $this->portaldb->update('ebh_pitems',array(),array('itemid'=>intval($itemid)),array('reviewnum'=>'reviewnum+1'));
	}
	/**
	 *文章浏览次数减一
	 */
	public function decReviewNum($itemid = 0){
		return $this->portaldb->update('ebh_pitems',array(),array('itemid'=>intval($itemid)),array('reviewnum'=>'reviewnum-1'));
	}

	/**
	 *获取栏目下面3级最新置顶文章一条
	 */
	public function getOneInTopCate($catid){
		$sql = 'select i.itemid,i.catid,i.subject,i.itemurl,i.thumb from ebh_pitems i left join ebh_pcategories c on c.catid = i.catid where i.top=3 AND c.upid ='.intval($catid).' order by i.itemid desc limit 1';
		return $this->portaldb->query($sql)->row_array();
	}


}