<?php
/*
用户权限,用于服务包
*/
class UserpermissionModel extends CModel{
	
	/**
	*判断用户是否有平台权限
	* @return int 返回验证结果，1表示有权限 2表示已过期 0表示用户已停用 -1表示无权限 -2参数非法
	*/
	public function checkUserPermision($uid,$param = array()) {
		if(empty($param['powerid']) && empty($param['crid']) && empty($param['folderid'])) 
			return -2;
		$flag = 0;	//默认平台权限
		if(!empty($param['powerid']))	//powerid功能点权限
			$flag = 1;
		else if(!empty($param['folderid']))	//课程权限
			$flag = 2;
		if($flag == 1) {
			$sql = 'select p.startdate,p.enddate from ebh_userpermisions p where p.uid = '.$uid.' and p.powerid='.$param['powerid']; 
		} else if($flag == 2) {
			//$sql = 'select p.startdate,p.enddate from ebh_userpermisions p where p.uid = '.$uid.' and p.crid='.$param['crid'].' and p.folderid='.$param['folderid'];
            $sql = 'select p.startdate,p.enddate,p.crid from ebh_userpermisions p where p.uid = '.$uid.' and p.folderid='.$param['folderid'];
        } else {
			$sql = 'select p.startdate,p.enddate from ebh_userpermisions p where p.uid = '.$uid.' and p.crid='.$param['crid'].' and p.folderid=0'; 
		}
		$peritem = $this->db->query($sql)->row_array();
		if(empty($peritem)) {	//无权限		
			return -1;
		}

		if (!empty($peritem['enddate']) && $peritem['enddate'] < (EBH_BEGIN_TIME - 86400))
            return 2;
		return 1;
	}

	/**
	*获取用户已开通的课程
	*/
	public function getUserPayFolderList($param = array()) {
		if(empty($param['uid']))
			return FALSE;
		$sql = "select p.pid,p.itemid,p.crid,p.folderid,p.folderid as fid from ebh_userpermisions p";
		$wherearr = array();
		$wherearr[] = 'p.uid='.$param['uid'];
		if(!empty($param['crid'])) {
			$wherearr[] = 'p.crid='.$param['crid'];
		}
		if(!empty($param['filterdate'])) {	//过滤已过期
			$enddate = SYSTIME - 86400;
			$wherearr[] = 'p.enddate>'.$enddate;
		}
		$sql .= ' WHERE '.implode(' AND ',$wherearr);
		return $this->db->query($sql)->list_array();
	}

	/**
	*获取学校下所有的服务项
	*/
	public function getPayItemByCrid($crid) {
		$sql = "select i.itemid,i.pid,i.crid,i.folderid as fid,i.iprice,i.sid from ebh_pay_items i where i.crid=$crid";
		return $this->db->query($sql)->list_array();
	}
	/**
	*根据用户编号和itemid编号获取权限
	*/
	public function getPermissionByItemId($itemid,$uid) {
		$sql = "select p.pid,p.itemid,p.type,p.powerid,p.uid,p.crid,p.folderid,p.cwid,p.startdate,p.enddate,p.dateline from ebh_userpermisions p where p.itemid=$itemid and p.uid = $uid";
		return $this->db->query($sql)->row_array();
	}
	/**
	*根据用户编号和folderid编号获取权限
	*/
	public function getPermissionByFolderId($folderid,$uid) {
		$sql = "select p.pid,p.itemid,p.type,p.powerid,p.uid,p.crid,p.folderid,p.cwid,p.startdate,p.enddate,p.dateline from ebh_userpermisions p where p.folderid=$folderid and p.uid = $uid";
		return $this->db->query($sql)->row_array();
	}
	/**
	*根据订单明细内容生成订单信息
	*/
	public function addPermission($param = array()) {
		if(empty($param))
			return FALSE;
		$setarr = array();
		if(!empty($param['itemid']))
			$setarr['itemid'] = $param['itemid'];
		if(!empty($param['type']))
			$setarr['type'] = $param['type'];
		if(!empty($param['powerid']))
			$setarr['powerid'] = $param['powerid'];
		if(!empty($param['uid']))
			$setarr['uid'] = $param['uid'];
		if(!empty($param['crid']))
			$setarr['crid'] = $param['crid'];
		if(!empty($param['folderid']))
			$setarr['folderid'] = $param['folderid'];
		if(!empty($param['cwid']))
			$setarr['cwid'] = $param['cwid'];
		if(!empty($param['startdate']))
			$setarr['startdate'] = $param['startdate'];
		if(!empty($param['enddate']))
			$setarr['enddate'] = $param['enddate'];
		if(!empty($param['dateline']))
			$setarr['dateline'] = $param['dateline'];
		else 
			$setarr['dateline'] = SYSTIME;
		$pid = $this->db->insert('ebh_userpermisions',$setarr);
		return $pid;
	}
	
	/**
	*更新订单信息，如果包含明细，则同时更新明细信息
	*/
	public function updatePermission($param = array()) {
		if(empty($param) || empty($param['pid']))
			return FALSE;
		$setarr = array();
		$wherearr = array('pid'=>$param['pid']);
		if(!empty($param['itemid']))
			$setarr['itemid'] = $param['itemid'];
		if(!empty($param['type']))
			$setarr['type'] = $param['type'];
		if(!empty($param['powerid']))
			$setarr['powerid'] = $param['powerid'];
		if(!empty($param['uid']))
			$setarr['uid'] = $param['uid'];
		if(!empty($param['crid']))
			$setarr['crid'] = $param['crid'];
		if(!empty($param['folderid']))
			$setarr['folderid'] = $param['folderid'];
		if(!empty($param['cwid']))
			$setarr['cwid'] = $param['cwid'];
		if(!empty($param['startdate']))
			$setarr['startdate'] = $param['startdate'];
		if(!empty($param['enddate']))
			$setarr['enddate'] = $param['enddate'];
		$afrows = $this->db->update('ebh_userpermisions',$setarr,$wherearr);
		return $afrows;
	}
	/**
	*根据功能点或者平台等信息获取支付服务项
	*@param array $param
	*/
	public function getUserPayItem($param = array()) {
		if(empty($param['powerid']) && empty($param['crid']) && empty($param['folderid'])) 
			return FALSE;
		$flag = 0;	//默认平台权限
		if(!empty($param['powerid']))	//powerid功能点权限
			$flag = 1;
		else if(!empty($param['folderid']))	//课程权限
			$flag = 2;
		if($flag == 2) {
			$sql = 'select i.itemid,i.pid,i.iname,i.isummary,i.crid,i.folderid,i.iprice,i.imonth,i.iday from ebh_pay_items i where i.folderid='.$param['folderid'].' AND i.`status`=0';
			if(!empty($param['crid']))
				$sql .= ' and i.crid='.$param['crid'];
		}  else {
			$sql = 'select i.itemid,i.pid,i.iname,i.isummary,i.crid,i.folderid,i.iprice,i.imonth,i.iday from ebh_pay_items i where i.crid='.$param['crid'].' AND i.`status`=0';
		}
		$payitem = $this->db->query($sql)->row_array();
		return $payitem;
	}
	/**
	*根据功能点或者平台等信息获取支付服务项（如果存在价格为0 或者有课件权限的则返回）
	*@param array $param
	*/
	public function getUserFreePayItem($param = array()) {
		if(empty($param['powerid']) && empty($param['crid']) && empty($param['folderid'])) 
			return FALSE;
		$flag = 0;	//默认平台权限
		if(!empty($param['powerid']))	//powerid功能点权限
			$flag = 1;
		else if(!empty($param['folderid']))	//课程权限
			$flag = 2;
		if($flag == 2) {
			$sql = 'select i.itemid,i.pid,i.iname,i.isummary,i.crid,i.folderid,i.iprice,i.imonth,i.iday from ebh_pay_items i where i.folderid='.$param['folderid'];
		}  else {
			$sql = 'select i.itemid,i.pid,i.iname,i.isummary,i.crid,i.folderid,i.iprice,i.imonth,i.iday from ebh_pay_items i where i.crid='.$param['crid'];
		}
		$ptype = 1;
		if(!empty($param['checkexam']))
			$ptype = 2;
		$sql = $sql." and (i.iprice <= 0 || i.ptype > $ptype)";
		$payitem = $this->db->query($sql)->row_array();
		return $payitem;
	}
	
	/**
	*根据用户编号和itemid编号获取权限
	*/
	public function getPermissionByItemIds($param) {
		if(empty($param['itemids']) || empty($param['uid']) || empty($param['crid'])){
			return array();
		}
		$sql = 'select pid,itemid,p.uid,p.crid,p.folderid,p.cwid,p.dateline from ebh_userpermisions p';
		
		$enddate = SYSTIME - 86400;
		$wherearr[] = 'p.enddate>'.$enddate;
		$wherearr[] = 'uid='.$param['uid'];
		$wherearr[] = 'crid='.$param['crid'];
		$wherearr[] = 'itemid in('.$param['itemids'].')';
		$sql.= ' where '.implode(' AND ',$wherearr);
		return $this->db->query($sql)->list_array('itemid');
	}

    /**
     * 获取用户的课程权限
     * @param $uid 用户ID
     * @param $folderids 课程ID
     * @param $crid 所在网校ID
     * @return mixed
     */
	public function getFolderPermission($uid, $folderids, $crid = 0) {
	    $uid = intval($uid);
	    $crid = intval($crid);
	    if (is_array($folderids)) {
	        $folderids = array_map('intval', $folderids);
	        $folderids = array_unique($folderids);
        } else {
	        $folderids = array(intval($folderids));
        }

        //延迟一天过期
        $now = SYSTIME - 86400;
        $wheres = array(
            '`uid`='.$uid,
            '`folderid` IN('.implode(',', $folderids).')',
            '`enddate`>'.$now
        );
        if ($crid > 0) {
            $wheres[] = '`crid`='.$crid;
        }
        $sql = 'SELECT `folderid`,`enddate`,`crid` FROM `ebh_userpermisions` WHERE '.implode(' AND ', $wheres).' ORDER BY `enddate` ASC';
	    return $this->db->query($sql)->list_array('folderid');
    }

    /**
     * 获取用户的服务项权限
     * @param int $uid 用户ID
     * @param mixed $itemid 服务项ID
     * @param int $crid 网校ID
     * @return mixed
     */
    public function getItemPermission($uid, $itemid, $crid = 0) {
        $wheres = array(
            '`uid`='.$uid
        );
        if (is_array($itemid)) {
            $itemids = array_map('intval', $itemid);
            $itemids = array_unique($itemid);
            $wheres[] = '`itemid` IN('.implode(',', $itemids).')';
        } else {
            $itemids = array(intval($itemid));
            $wheres[] = '`itemid`='.intval($itemid);
        }
        if ($crid > 0) {
            $wheres[] = '`crid`='.$crid;
        }
        //延迟一天过期
        $wheres[] = '`enddate`>'.(SYSTIME - 86400);
        $sql = 'SELECT `itemid`,`crid`,`enddate` FROM `ebh_userpermisions` WHERE '.implode(' AND ', $wheres);
        return $this->db->query($sql)->list_array('itemid');
    }

    /**
     * 判断学生在网校下有无开通的课程权限
     * @param $uid
     * @param $crid
     * @return bool
     */
    public function checkUserFolderPermision($uid, $crid) {
        $uid = intval($uid);
        $crid = intval($crid);
        //延迟一天过期
        $now = SYSTIME - 86400;
        $sql = 'SELECT `folderid`,`enddate` FROM `ebh_userpermisions` WHERE `uid`='.$uid
            .' AND `crid`='.$crid.' AND `enddate`>'.$now.' LIMIT 1';
        $ret = $this->db->query($sql)->row_array();
        if (!empty($ret)) {
            return true;
        }
	    return false;
    }

    /**
     * 获取我的课程，包括已过期的课程(丢弃)
     * @param $uid 学生ID
     * @param $crid 所在网校
     * @param $is_schoolmate 是否网校学生
     * @param int $pid 服务包ID
     * @param $limit 限制条件
     * @return array
     */
    public function getMyLessons($uid, $crid, $is_schoolmate, $pid = 0, $limit = null) {
        $uid = intval($uid);
        $crid = intval($crid);
        $pid = intval($pid);
        $offset = 0;
        $top = 0;
        if (!empty($limit)) {
            if (is_array($limit)) {
                $page = !empty($limit['page']) ? intval($limit['page']) : 1;
                $page = max(1, $page);
                $top = !empty($limit['pagesize']) ? intval($limit['pagesize']) : 20;
                if ($top < 1) {
                    $top = 1;
                }
                $offset = ($page - 1) * $top;
            } else {
                $top = intval($limit);
            }
        }
        $params = array(
            '`a`.`uid`='.$uid,
            '`a`.`crid`='.$crid,
            '`b`.`status`=0',
            '`c`.`del`=0',
            '`d`.`status`=1'
        );
        /*if ($pid > 0) {
            $params[] = '`b`.`pid`='.$pid;
        }*/
        $sql = 'SELECT `a`.`pid` AS `id`,MAX(`a`.`itemid`) AS `itemid`,`a`.`folderid`,`a`.`enddate`,`b`.`iprice`,`b`.`pid`,`b`.`sid`,`b`.`cannotpay` ,`c`.`coursewarenum`,`c`.`credit`
                FROM `ebh_userpermisions` `a` JOIN `ebh_pay_items` `b` ON `b`.`itemid`=`a`.`itemid` 
                JOIN `ebh_folders` `c` ON `c`.`folderid`=`a`.`folderid`
                JOIN `ebh_pay_packages` `d` ON `d`.`pid`=`b`.`pid`
                WHERE '.implode(' AND ', $params).' GROUP BY `a`.`folderid` ORDER BY `a`.`pid` DESC';
        if ($top > 0) {
            $sql .= ' LIMIT '.$offset.','.$top;
        }
        //log_message($sql);
        //读取服务项
        $userpermissions = $this->db->query($sql)->list_array('folderid');
        if (empty($userpermissions)) {
            return array();
        }
        $folderids = array_keys($userpermissions);
        $folderids_str = implode(',', $folderids);
        $itemids = array_column($userpermissions, 'itemid');
        $itemids_str = implode(',', $itemids);
        unset($folderids, $itemids);
        //读取服务项中的课程
        $folders = $this->db->query(
            'SELECT `folderid`,`foldername`,`img`,`isschoolfree` FROM `ebh_folders` WHERE `folderid` IN('.$folderids_str.')')
            ->list_array('folderid');
        //读取课程老师
        $teachers = $this->db->query(
            'SELECT `a`.`folderid`,GROUP_CONCAT(`b`.`username`,\' \',`b`.`realname`) AS `teachers` FROM `ebh_teacherfolders` `a` LEFT JOIN `ebh_users` `b` ON `b`.`uid`=`a`.`tid` WHERE `a`.`crid`='.$crid.' GROUP BY `folderid`')
            ->list_array('folderid');
        //读取服务包
        $sql = 'SELECT `b`.`pname`,`b`.`pid` FROM `ebh_pay_items` `a` JOIN `ebh_pay_packages` `b` ON `b`.`pid`=`a`.`pid` JOIN `ebh_classrooms` `c` ON `c`.`crid`=`b`.`crid` WHERE `a`.`itemid` IN('.$itemids_str.') AND `b`.`pid`>0 AND `c`.`crid` IS NOT NULL GROUP BY `b`.`pid`';
        $packages = $this->db->query($sql)->list_array('pid');
        foreach ($userpermissions as $k => $v) {
            if (empty($packages[$v['pid']])) {
                continue;
            }
            /*if ($pid > 0 && $v['pid'] != $pid) {
                continue;
            }*/
            if (isset($teachers[$k])) {
                $folder_teachers = explode(',', $teachers[$k]['teachers']);
                $folder_teachers = array_map(function($t) {
                    $tmp = explode(',', $t);
                    foreach ($tmp as $titem) {
                        $nameGroup = explode(' ', $titem);
                        if (!empty($nameGroup[1])) {
                            return $nameGroup[1];
                        }
                        return $nameGroup[0];
                    }
                }, $folder_teachers);
                $folders[$k]['tname'] = implode(',', $folder_teachers);
            } else {
                $folders[$k]['tname'] = '';
            }
            $userpermissions[$k] = array_merge($v, $folders[$k]);
            $userpermissions[$k]['fprice'] = $userpermissions[$k]['iprice'];
            if ($userpermissions[$k]['enddate'] > SYSTIME - 86400) {
                $userpermissions[$k]['itemid'] = 0;
            } else {
                //过期，延迟一天，网校学生并且课程是全校免费，将课程价格置为0
                if ($is_schoolmate && !empty($userpermissions[$k]['isschoolfree'])) {
                    $userpermissions[$k]['fprice'] = $userpermissions[$k]['iprice'] = 0;
                }
            }
            $packages[$v['pid']]['items'][] = $userpermissions[$k];
        }
        return $packages;
    }

    /**
     * 学生课程列表
     * @param int $uid 用户ID
     * @param int $crid 网校ID
     * @param bool $isStudent 是否本校学生
     * @param bool $saveExpired 是否保留过期课程
     * @return array
     */
    public function getMyCourses($uid, $crid, $isStudent , $saveExpired = false) {
        $wheres = array(
            '`a`.`uid`='.$uid,
            '`a`.`crid`='.$crid,
            '`a`.`cwid`=0',
            '`b`.`del`=0'
        );
        $now = SYSTIME - 86400;
        if (!$saveExpired) {
            //有效期延时一天
            $wheres[] = '`a`.`enddate`>'.$now;
        }
        $sql = 'SELECT `a`.`dateline`,`a`.`enddate`,`a`.`folderid`,`b`.`foldername`,`b`.`isschoolfree`,`b`.`fprice`,`b`.`img`,`c`.`itemid`,`c`.`sid`,`c`.`iprice`,`c`.`cannotpay`,`c`.`crid`,`d`.`pid`,`d`.`pname` ,`f`.`coursewarenum`,`f`.`credit`
                FROM `ebh_userpermisions` `a` JOIN `ebh_folders` `b` USING(`folderid`) 
                LEFT JOIN `ebh_pay_items` `c` ON `c`.`itemid`=`a`.`itemid` AND `c`.`status`=0 
                LEFT JOIN `ebh_pay_packages` `d` ON `d`.`pid`=`c`.`pid` 
				LEFT JOIN `ebh_folders` `f` ON `a`.`folderid`=`f`.`folderid` 
                WHERE '.implode(' AND ', $wheres).' ORDER BY `a`.`dateline` ASC';
        $ret = $this->db->query($sql)->list_array('folderid');
        if (empty($ret)) {
            return array();
        }
        //读取任课教师
        $folderids = array_keys($ret);
        $sql = 'SELECT `a`.`folderid`,GROUP_CONCAT(`b`.`username`,\' \',`b`.`realname`) AS `teachers` 
                FROM `ebh_teacherfolders` `a` JOIN `ebh_users` `b` ON `b`.`uid`=`a`.`tid` 
                JOIN `ebh_teachers` `c` ON `c`.`teacherid`=`a`.`tid` 
                WHERE `a`.`folderid` in('.implode(',', $folderids).') GROUP BY `folderid`';
        $teachers = $this->db->query($sql)->list_array('folderid');
        if (!empty($teachers)) {
            array_walk($teachers, function(&$teacher) {
                $names = explode(',', $teacher['teachers']);
                $teacher['teachers'] = array();
                foreach ($names as $nameGroup) {
                    $nameGroups = explode(' ', $nameGroup);
                    if (!empty($nameGroups[1])) {
                        $teacher['teachers'][] = $nameGroups[1];
                        continue;
                    }
                    $teacher['teachers'][] = $nameGroups[0];
                }
            });
        } else {
            $teachers = array();
        }

        array_walk($ret, function(&$item, $k, $args) {
            if ($item['enddate'] > $args['now']) {
                $item['itemid'] = 0;
            } else {
                if ($args['isStudent'] && !empty($item['isschoolfree']) && $item['itemid'] !== null) {
                    $item['fprice'] = $item['iprice'] = 0;
                }
            }
            unset($item['isschoolfree']);
            if (isset($args['teachers'][$k])) {
                $item['tname'] = implode(',', $args['teachers'][$k]['teachers']);
                return;
            }
            $item['tname'] = '';
        }, array(
            'teachers' => $teachers,
            'now' => $now,
            'isStudent' => $isStudent
        ));
        return $ret;
    }
  /**
     * 获取课程包对应课程的权限
     */
    public function getkcbPermision($uid,$crid,$sid){
        $ckstatus = false; 
        if(empty($uid)||empty($crid)||empty($sid)){
            return false;
        }
        //先获取课程包关联的课程数
        $kcbnums = 0;
        $sql ='select pi.folderid  from ebh_pay_items pi  left join ebh_pay_sorts ps  on ps.sid = pi.sid
                where pi.crid = '.$crid.' and pi.sid = '.$sid.' and pi.status = 0';
        $kcbrows = $this->db->query($sql)->list_array();
        $kcbnums = count($kcbrows);
        if($kcbnums == 0){
            return false;
        }
        $folderidArr = array();
        $folderidArr = array_map(function($v){ return $v['folderid'];}, $kcbrows);
        
        //读取课程包对应课程的开通情况
        if(!empty($folderidArr)){
            $cksql = 'select count(1) count from ebh_userpermisions where folderid in( '.implode(',', $folderidArr).' ) and enddate >'.SYSTIME.' and uid = '.$uid.' and crid = '.$crid;
            $ckrow = $this->db->query($cksql)->row_array();
            if($ckrow['count']==count($folderidArr)){
                $ckstatus = true;
            }else{
                $ckstatus = false;
            }
        }
               
        return $ckstatus;
    }

    /**
     * 获取学生课程权限
     * @param int $uid 学生用户ID
     * @param int $folderid 课程ID
     * @param int $crid 网校ID
     * @return mixed
     */
    public function getFolderPower($uid, $folderid, $crid = 0) {
        $wheres = array(
            '`uid`='.$uid,
            '`folderid`='.$folderid
        );
        if ($crid > 0) {
            $wheres[] = '`crid`='.$crid;
        }
        $sql = 'SELECT `startdate`,`enddate`,`crid` FROM `ebh_userpermisions` WHERE '.implode(' AND ', $wheres).' ORDER BY `enddate` DESC LIMIT 1';
        return $this->db->query($sql)->row_array();
    }
}