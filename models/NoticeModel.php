<?php
/**
 * 通知Model类 NoticeModel
 */
class NoticeModel extends CModel{
    /**
     * 获取通知列表
     * @param type $queryarr
     * @return type
     */
    public function getnoticelist($queryarr) {
        $sql = 'SELECT n.noticeid as id,n.title,n.message,n.type,n.dateline as date,u.username,u.realname FROM ebh_notices n '.
                'LEFT JOIN ebh_users u on (u.uid = n.uid) ';
        $wherearr = array();
        if(!empty($queryarr['crid']))   //所在学校
            $wherearr[] = 'n.crid='.$queryarr['crid'];
		if(!empty($queryarr['uid']))   //发送人编号
            $wherearr[] = 'n.uid='.$queryarr['uid'];
        if(!empty($queryarr['ntype']))  //通知类型,1为全校师生 2为全校教师 3为全校学生 4为班级学生
            $wherearr[] = 'n.ntype in ('.$queryarr['ntype'] .')';
		if(!empty($queryarr['classid']))	//过滤接收通知的班级编号
			$wherearr[] = '(FIND_IN_SET('.$queryarr['classid'].',n.cids) or n.ntype in(1,3))';
        if(!empty($wherearr))
            $sql .= ' WHERE '.implode (' AND ', $wherearr);
        if(!empty($queryarr['order']))
            $sql .= ' ORDER BY '.$queryarr['order'];
        else
            $sql .= ' ORDER BY n.noticeid desc ';
        if(!empty($queryarr['limit']))
            $sql .= 'limit '.$queryarr['limit'];
        else {
            if(empty($queryarr['page']) || $queryarr['page'] < 1)
                $page = 1;
            else
                $page = $queryarr['page'];
            $pagesize = empty($queryarr['pagesize']) ? 10 : $queryarr['pagesize'];
            $start = ($page - 1) * $pagesize ;
            $sql .= 'limit '.$start.','.$pagesize;
        }
        return $this->db->query($sql)->list_array();
    }
	/*
	获取通知详情
	*/
	public function getNoticeDetail($param){
		$wherearr = array();
		$sql = 'select n.noticeid as id,n.title,n.message,n.dateline as date,n.type,u.username,u.realname from ebh_notices n ' .
			 'LEFT JOIN ebh_users u on (u.uid = n.uid) ';;
		$wherearr[] = 'crid='.$param['crid'];
		$wherearr[] = 'noticeid='.$param['noticeid'];
		$sql.= ' where '.implode(' AND ',$wherearr);
		return $this->db->query($sql)->row_array();
		
	}
	/**
	*添加通知的浏览数
	*/
	public function addviewnum($noticeid) {
		$wherearr = array('noticeid'=>$noticeid);
		$setarr = array('viewnum'=>'viewnum+1');
		return $this->db->update('ebh_notices',array(),$wherearr,$setarr);
	}
	/**
	*根据给定时间获取发送到学生的最新通知数
	*/
	public function getnewnoticecountbytime($param) {
		$sql = 'select count(*) count from ebh_notices n';
		$wherearr = array();
        if(!empty($param['crid']))   //所在学校
            $wherearr[] = 'n.crid='.$param['crid'];
		if (isset($param['subtime']))
			$wherearr[] = 'n.dateline > '.$param['subtime'];
		if(!empty($param['ntype']))  //通知类型,1为全校师生 2为全校教师 3为全校学生 4为班级学生
            $wherearr[] = 'n.ntype in ('.$param['ntype'] .')';
		if(!empty($param['classid']))	//过滤接收通知的班级编号
			$wherearr[] = '(FIND_IN_SET('.$param['classid'].',n.cids) or n.ntype in(1,3))';
		if (!empty($wherearr))
            $sql .= ' WHERE ' . implode(' AND ', $wherearr);
		$row = $this->db->query($sql)->row_array();
		if(!empty($row))
			$count = $row['count'];
        return $count;
	}
}
