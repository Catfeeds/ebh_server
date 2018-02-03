<?php
/**
 *教室与课件关系表
 */
class RoomcourseModel extends CModel{
	//获取教室课件列表
	public function getRoomCourseList($param =array()){
		$sql = 'select rc.crid,rc.cwid,rc.folderid from ebh_roomcourses rc';
		$wherearr = array();
		if(!empty($param['crid'])){
			$wherearr[] = 'rc.crid = '.$param['crid'];
		}
		if(!empty($param['cwid'])){
			$wherearr[] = 'rc.cwid = '.$param['cwid'];
		}
		if(!empty($param['cwid_in']) && is_array($param['cwid_in'])){
			$wherearr[] = 'rc.cwid in ('.implode(',', $param['cwid_in']).')';
		}
		if(!empty($param['folderid'])){
			$wherearr[] = 'rc.folderid = '.$param['folderid'];
		}
		if(!empty($wherearr)){
			$sql.= ' WHERE '.implode(' AND ', $wherearr);
		}
		if(!empty($param['order'])){
			$sql.=' order by '.$param['order'];
		}
		if(!empty($param['limit'])){
			$sql.=' limit '.$param['limit'];
		}else{
			$sql.=' limit 10';
		}
		return $this->db->query($sql)->list_array();
	}

	//获取有课件但是没有课程的作业列表
	public function getExamWithCwidButNotFolderid($crid = 0){
		if(empty($crid)){
			return;
		}
		$sql = 'select se.eid,se.cwid from ebh_schexams se where se.crid = '.$crid.' and se.cwid > 0 and se.folderid = 0';
		return $this->db->query($sql)->list_array();
	}
	//获取学分表中有eid但是没有folderid记录的数据
	public function getSLogWithCwidButNotFolderid($crid = 0){
		if(empty($crid)){
			return;
		}
		$sql = 'select scl.logid,scl.cwid from ebh_schcreditlog scl where cwid > 0 and folderid = 0 and crid = '.$crid;
		return $this->db->query($sql)->list_array();
	}
	
	//批量修复课件对应的丢失课程的作业
	public function mupdate($params = array()){
		$sql = 'UPDATE ebh_schexams SET folderid = CASE eid ';
		$wtArr = array();
		$inArr = array();
		foreach ($params as $param) {
			$wtArr[] = ' WHEN '.$param['eid'].' THEN '.$param['folderid'];
			$inArr[] = $param['eid'];
		}
		if(empty($wtArr)){
			return -2;//不需要更新
		}
		$sql.= implode(' ', $wtArr).' END WHERE eid IN ('.implode(',', $inArr).')';
		$this->db->query($sql);
		return $this->db->affected_rows();
	}

	//批量修复课件对应的丢失课程的作业
	public function mupdate_logs($params = array()){
		$sql = 'UPDATE ebh_schcreditlog SET folderid = CASE logid ';
		$wtArr = array();
		$inArr = array();
		foreach ($params as $param) {
			$wtArr[] = ' WHEN '.$param['logid'].' THEN '.$param['folderid'];
			$inArr[] = $param['logid'];
		}
		if(empty($wtArr)){
			return -2;//不需要更新
		}
		$sql.= implode(' ', $wtArr).' END WHERE logid IN ('.implode(',', $inArr).')';
		$this->db->query($sql);
		return $this->db->affected_rows();
	}

	//获取指定课件对应对的folderid
	public function getFolderByCwid($cwid=0,$crid=0){
		if(empty($cwid) || empty($crid)){
			return 0;
		}
		$sql = 'select folderid from ebh_roomcourses rc where rc.crid = '.$crid.' and rc.cwid = '.$cwid.' limit 1';
		return $this->db->query($sql)->row_array();
	}

	//作业关联课件
	public function examLinkCourse($param = array()){
		if(empty($param) || empty($param['eid']) || !is_numeric($param['cwid']) || empty($param['uid']) || empty($param['crid']) ){
			return ;
		}

		if(!empty($param['cwid'])){
			$folderInfo = $this->getFolderByCwid($param['cwid'],$param['crid']);
			if(empty($folderInfo)){
				return;
			}
			$folderid = $folderInfo['folderid'];
			$setArr = array(
				'cwid'=>$param['cwid'],
				'folderid'=>$folderid,
			);
		}else{
			$setArr = array(
				'cwid'=>$param['cwid']
			);
		}
		

		$where = array(
			'eid'=>$param['eid'],
			'crid'=>$param['crid'],
			'uid'=>$param['uid']
		);
		return $this->db->update('ebh_schexams',$setArr,$where);
	}
}
