<?php
class PaysortModel extends CModel{
	public function getSortList($param){
		$sql = 'select s.sname,s.sid,s.sdisplayorder,s.showbysort from ebh_pay_sorts s join ebh_pay_packages p on s.pid=p.pid';
		$wherearr = array();
		if(!empty($param['pid']))
			$wherearr[].='p.pid='.$param['pid'];
		if(!empty($wherearr))
			$sql.= ' where '.implode(' AND ',$wherearr);
		return $this->db->query($sql)->list_array();
	}
	public function getSortCount(){
		
	}
	public function add($param){
		if(empty($param['pid']))
			return false;
		$sarr['pid'] = $param['pid'];
		$sarr['sname'] = $param['sname'];
		if(!empty($param['content']))
			$sarr['content'] = $param['content'];
		if(isset($param['sdisplayorder']))
			$sarr['sdisplayorder'] = $param['sdisplayorder'];
		if(isset($param['showbysort']))
			$sarr['showbysort'] = $param['showbysort'];
		if(isset($param['image']['upfilepath']))
			$sarr['imgurl'] = $param['image']['upfilepath'];
		if(isset($param['image']['upfilename']))
			$sarr['imgname'] = $param['image']['upfilename'];
		return $this->db->insert('ebh_pay_sorts',$sarr);
	}
	
	public function edit($param){
		if(empty($param['pid']) || empty($param['sid']))
			return false;
		$sarr['sname'] = $param['sname'];
		if(isset($param['content']))
			$sarr['content'] = $param['content'];
		if(isset($param['sdisplayorder']))
			$sarr['sdisplayorder'] = $param['sdisplayorder'];
		if(isset($param['showbysort']))
			$sarr['showbysort'] = $param['showbysort'];
		if(isset($param['image']['upfilepath']))
			$sarr['imgurl'] = $param['image']['upfilepath'];
		if(isset($param['image']['upfilename']))
			$sarr['imgname'] = $param['image']['upfilename'];
		return $this->db->update('ebh_pay_sorts',$sarr,'sid='.$param['sid']);
		
	}
	
	public function getSortdetail($sid){
		$sql = 'select s.sname,s.sid,s.content,s.showbysort,s.imgurl,s.imgname from ebh_pay_sorts s where sid ='.$sid;
		return $this->db->query($sql)->row_array();
	}

    /**
     * 获取分类的showbysort标志
     * @param $sid_arr
     * @return bool
     */
	public function getShowbysort($sid_arr) {
        if (!is_array($sid_arr) || empty($sid_arr)) {
            return false;
        }
        $sid_arr_str = implode(',', $sid_arr);
        $sql = "SELECT `sid`,`showbysort` FROM `ebh_pay_sorts` WHERE `sid` IN($sid_arr_str)";
        return $this->db->query($sql)->list_array('sid');
    }
    /**
     * 获取打包服务项的信息
     * @param $sids
     * @return bool
     */
    public function sortsPackMemberInfo($sids) {
        if (empty($sids) || !is_array($sids)) {
            return false;
        }
        $sids = array_filter($sids, function($sid) {
            return is_numeric($sid) && $sid > 0;
        });
        if (empty($sids)) {
            return false;
        }
        $sids_str = implode(',', $sids);
        $sql = "SELECT `a`.`itemid`,`a`.`iprice`,`a`.`cannotpay`,`b`.`isschoolfree`,`a`.`sid`,`c`.`showbysort` FROM `ebh_pay_items` `a` 
                JOIN `ebh_folders` `b` ON `a`.`folderid`=`b`.`folderid`
                JOIN `ebh_pay_sorts` `c` ON `a`.`sid`=`c`.`sid`
                WHERE `a`.`sid` IN($sids_str)";
        return $this->db->query($sql)->list_array();
    }

    /**
     * 服务包下分类
     * @param int $pid 服务包ID
     * @param int $crid 网校ID
     * @param int $limit 限制条件
     * @return array
     */
    public function getSortsForPack($pid, $crid, $limit = 0) {
        $package = $this->db->query(
            'SELECT `pid`,`pname` FROM `ebh_pay_packages` WHERE `pid`='.$pid.' AND `status`=1')
            ->row_array();
        if (empty($package)) {
            return array();
        }

        $wheres = array(
            '`a`.`crid`='.$crid,
            '`a`.`pid`='.$pid,
            '`a`.`status`=0',
            '`b`.`del`=0',
            '`b`.`folderlevel`=2',
            '`b`.`power`=0'
        );
        $sql = 'SELECT `a`.`sid` FROM `ebh_pay_items` `a` JOIN `ebh_folders` `b` ON `b`.`folderid`=`a`.`folderid` WHERE '.implode(' AND ', $wheres);
        $sids = $this->db->query($sql)->list_array('sid');
        $wheres = array(
            '`a`.`crid`='.$crid,
            '`a`.`del`=0',
            '`b`.`pid`='.$pid,
            '`b`.`status`=0',
            '`c`.`del`=0',
            '`c`.`folderlevel`=2',
            '`c`.`power`=0'
        );
        $sql = 'SELECT `b`.`sid` FROM `ebh_schsourceitems` `a` JOIN `ebh_pay_items` `b` ON `b`.`itemid`=`a`.`itemid` 
                JOIN `ebh_folders` `c` ON `c`.`folderid`=`b`.`folderid` WHERE '.implode(' AND ', $wheres);
        $osids = $this->db->query($sql)->list_array('sid');
        $sids = array_merge(array_keys($sids), array_keys($osids));
        $sids = array_unique($sids);


        $offset = 0;
        $top = 0;
        if (!empty($limit)) {
            if (is_array($limit)) {
                $page = max(1, !empty($limit['page']) ? intval($limit['page']) : 1);
                $top = !empty($limit['pagesize']) ? intval($limit['pagesize']) : 20;
                $offset = ($page - 1) * $top;
            } else {
                $top = intval($limit);
            }
        }
        $sql = 'SELECT `sid`,`sname` FROM `ebh_pay_sorts` WHERE `sid` IN('.implode(',', $sids).') AND `ishide`=0';
        if ($top > 0) {
            $sql .= ' LIMIT '.$offset.','.$top;
        }
        $package['sorts'] = $this->db->query($sql)->list_array();
        if ($top == 0 || $top > 0 && count($package['sorts']) < $top) {
            $other = $this->db->query('SELECT `itemid` FROM `ebh_pay_items` `a` JOIN `ebh_folders` `b` ON `b`.`folderid`=`a`.`folderid` WHERE `pid`='.$pid.' AND `sid`=0 AND `status`=0 AND `b`.`del`=0 AND `b`.`power`=0 AND `b`.`folderlevel`=2 LIMIT 1')->row_array();
            if (!empty($other)) {
                $package['sorts'][] = array(
                    'sid' => 0,
                    'sname' => '其他'
                );
            }
        }

        return $package;
    }
}