<?php
class IaclassroomlogModel extends CModel{
	/**
	 *获取学生互动列表
	 */
	public function getList($param = array()){
		if(empty($param['uid'])){
			return array();
		}
		$sql = 'select ic.icid,ic.title,ic.resource,ic.dateline,(select img from ebh_iaclassroomlog icl where uid = '.$param['uid'].' and icl.icid = ic.icid) as img,u.username,u.realname,u.nickname from ebh_iaclassroom ic left join ebh_users u on ic.uid = u.uid left join ebh_ia_class iac on ic.icid = iac.icid';
		$whereArr = array();
		if(!empty($param['crid'])){
			$whereArr[] = 'ic.crid = '.$param['crid'];
		}
		if(!empty($param['tid'])){
			$whereArr[] = 'ic.uid = '.$param['tid'];
		}
		if(!empty($param['tid_in'])){
			$whereArr[] = 'ic.uid in '.$param['tid_in'];
		}
		if(!empty($param['q'])) {
            $whereArr[] = '(ic.title like \'%'.$this->db->escape_str($param['q']).'%\')';
        }
        if(!empty($param['classid'])){
        	$whereArr[] = '(isnull(iac.classid) or iac.classid = '.$param['classid'].')';
        }
		if(!empty($whereArr)){
			$sql.=' WHERE '.implode(' AND ',$whereArr);
		}
		if(!empty($param['order'])){
			$sql.= ' order by '.$param['order'];
		}else{
			$sql.= ' order by ic.icid desc';
		}
		if(empty($param['limit'])){
			$sql.= ' limit 1000';
		}else{
			$sql.= ' limit '.$param['limit'];
		}
		return $this->db->query($sql)->list_array();
	}
	/**
	 *获取学生互动列表条数
	 */
	public function getListCount($param = array()){
		$sql = 'select count(*) count from ebh_iaclassroom ic left join ebh_ia_class iac on ic.icid = iac.icid';
		$whereArr = array();
		if(!empty($param['crid'])){
			$whereArr[] = 'ic.crid = '.$param['crid'];
		}
		if(!empty($param['tid'])){
			$whereArr[] = 'ic.uid = '.$param['tid'];
		}
		if(!empty($param['classid'])){
        	$whereArr[] = '(isnull(iac.classid) or iac.classid = '.$param['classid'].')';
        }
		if(!empty($whereArr)){
			$sql.=' WHERE '.implode(' AND ',$whereArr);
		}
		$res = $this->db->query($sql)->row_array();
		return $res['count'];
	}
	/**
	 *获取一条互动记录
	 */
	public function getialog($param = array()){
		$sql = 'select icl.iclogid,icl.uid,icl.icid,icl.img,icl.dateline,icl.lastpost from ebh_iaclassroomlog icl';
		$whereArr = array();
		if(!empty($param['iclogid'])){
			$whereArr[] = 'icl.iclogid='.$param['iclogid'];
		}
		if(!empty($param['uid'])){
			$whereArr[] = 'icl.uid='.$param['uid'];
		}
		if(!empty($param['icid'])){
			$whereArr[] = 'icl.icid='.$param['icid'];
		}
		if(!empty($whereArr)){
			$sql.=' WHERE '.implode(' AND ',$whereArr);
		}
		return $this->db->query($sql)->row_array();
	}

	//添加一条记录
	public function _insert($param = array()){
		if(empty($param)){
			return 0;
		}
		return $this->db->insert('ebh_iaclassroomlog',$param);
	}

	//修改一条记录
	public function _update($param = array(),$where = array()){
		if(empty($param)||empty($where)){
			return 0;
		}
		return $this->db->update('ebh_iaclassroomlog',$param,$where);
	}
	//删除一条记录
	public function _delete($param = array()){
		if(empty($param)){
			return 0;
		}
		return $this->db->delete('ebh_iaclassroomlog',$param);
	}
}