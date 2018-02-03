<?php
/**
 * 收藏相关model类 FavoriteModel
 */
class FavoriteModel extends CModel{
	/**
	*获取学员课程收藏列表
	*/
	public function getfolderfavoritelist($param) {
		if(empty($param['uid']))
			return FALSE;
		$sql = 'SELECT f.folderid as fid,fo.foldername as name,fo.img as face,fo.coursewarenum as num,fo.fprice from ebh_favorites f '.
				'JOIN ebh_folders fo on (f.folderid = fo.folderid) ';
		$wherearr = array();
		$wherearr[] = 'f.uid='.$param['uid'];
		if(!empty($param['crid']))
			$wherearr[] = 'f.crid='.$param['crid'];
		if(!empty($param['folderid']))
			$wherearr[] = 'f.folderid='.$param['folderid'];
		$sql .= ' WHERE '.implode(' AND ',$wherearr);
		if(!empty($param['limit']))
			$sql .= ' limit '.$param['limit'];
		else {
			if (empty($param['page']) || $param['page'] < 1)
				$page = 1;
			else
				$page = $param['page'];
			$pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
			$start = ($page - 1) * $pagesize;
			$sql .= ' limit ' . $start . ',' . $pagesize;
		}
		$folderlist = $this->db->query($sql)->list_array();
		$mylist = array();
		foreach($folderlist as $myfolder) {
			if(empty($myfolder['face']))
				$myfolder['face'] = 'http://static.ebanhui.com/ebh/images/nopic.jpg';
			$mylist[] = $myfolder;
		}
		return $mylist;
	}
}
