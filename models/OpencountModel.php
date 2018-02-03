<?php
/**
 *用户开通支付类
 */
class OpencountModel extends CModel {
    /**
	*获取学生用户开通支付交易数
	*/
	public function getUserOpenCount($param) {
		if(empty($param['uid']))
			return 0;
		$count = 0;
		$sql = 'select count(*) count from ebh_tempstudents t';
		$wherearr = array();
		$wherearr[] = 't.uid='.$param['uid'];
		$wherearr[] = 't.status=1';
		if(!empty($param['crid']))
			$wherearr[] = 't.crid='.$param['crid'];
		$sql .= ' WHERE ' . implode(' AND ', $wherearr);
		$row = $this->db->query($sql)->row_array();
		if(!empty($row))
			$count = $row['count'];
        return $count;
	}
}
