<?php

/**
 * 企业选课，网校来源信息关联表
 */
class SchsourceModel extends CModel {
	
	/**
	 * 根据记录id,获取已选课程
	 * @param int $sourceid
	 * @return array
	 */
	public function getSelectedItems($param){
		if(empty($param['sourceid']) && empty($param['crid'])){
			return FALSE;
		}
		$sql = 'select s.sourceid,itemid,folderid,price,month,del,s.crid,s.sourcecrid,s.name from ebh_schsourceitems si 
				join ebh_schsources s on s.crid=si.crid and s.sourcecrid=si.sourcecrid';
		if(!empty($param['sourceid'])){
			$wherearr[]= 's.sourceid='.$param['sourceid'];
		}
		if(!empty($param['crid'])){
			$wherearr[]= 's.crid='.$param['crid'];
		}
		if(isset($param['del'])){
			$wherearr[]= 'si.del ='.$param['del'];
		}
		if(!empty($param['itemid'])){
			$wherearr[]= 'si.itemid='.$param['itemid'];
		}
		$sql.= ' where '.implode(' AND ',$wherearr);
		$sql.= ' ORDER BY `s`.`sort` ASC,`s`.`dateline` DESC';
		return $this->db->query($sql)->list_array('itemid');
	}
	
	/**
	 * 获取课程列表
	 * @param array $param
	 * @return list
	 */
	public function getItemList($param) {
		if (empty($param['crid']) && empty($param['itemids']))
			return FALSE;
		$sql = 'select i.iname,i.itemid,i.folderid,p.pname,s.sname,i.pid,i.sid,i.crid 
				from ebh_pay_items i
				left join ebh_pay_sorts s on i.sid=s.sid
				join ebh_pay_packages p on p.pid=i.pid';
		$wherearr = array('i.`status`=0','p.`status`=1');
		if(!empty($param['pid'])){
			$wherearr[] = 'i.pid=' . $param['pid'];
			if(!empty($param['sid'])){
				$wherearr[] = 'i.sid=' . $param['sid'];
			}
		}
		if(!empty($param['q'])){
			$q = $this->db->escape_str($param['q']);
			$wherearr[] = '(i.iname like \'%'.$q.'%\' or p.pname like \'%'.$q.'%\' or s.sname like \'%'.$q.'\')';
		}
		if(!empty($param['itemids'])){
			$wherearr[] = 'i.itemid in ('.$param['itemids'].')';
		}
		if(!empty($param['crid'])){
			$wherearr[] = 'i.crid='.$param['crid'];
			$wherearr[] = 'p.crid='.$param['crid'];
		}
		$sql .= ' WHERE ' . implode(' AND ', $wherearr);
		$sql .= ' order by p.displayorder asc,p.pid desc,s.sid desc,i.itemid desc';
		return $this->db->query($sql)->list_array();
	}

    /**
     * 获取课程服务项
     * @param int $folderid 课程ID
     * @param int $crid 网校ID
     * @return mixed
     */
	public function getPayItemByFolderid($folderid, $crid) {
	    $wheres = array(
	        '`a`.`crid`='.$crid,
            '`a`.`del`=0',
            '`b`.`status`=0',
            '`c`.`folderid`='.$folderid,
            '`c`.`del`=0',
            '`c`.`power`=0',
            '`c`.`folderlevel`>1'
        );
        $sql = 'SELECT `a`.`itemid`,`a`.`price`,`a`.`sourcecrid`,`c`.`folderid` FROM `ebh_schsourceitems` `a` JOIN `ebh_pay_items` `b` ON `b`.`itemid`=`a`.`itemid` 
                JOIN `ebh_folders` `c` ON `c`.`folderid`=`b`.`folderid` WHERE '.implode(' AND ', $wheres);
        log_message($sql);
        return $this->db->query($sql)->row_array();
    }
}
