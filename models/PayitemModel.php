<?php
/*
服务包内项目
*/
class PayitemModel extends CModel{
	/**
	*获取服务包内项目列表
	*/
	public function getItemList($param) {
		$sql = 'select i.itemid,i.pid,i.crid,i.folderid,i.iname,i.isummary,i.iprice,i.imonth,i.iday,i.dateline,i.providercrid,i.comfee,i.roomfee,i.providerfee,r.crname,r.summary,r.cface,r.domain,r.coursenum,r.examcount,r.ispublic,p.pname,t.tname,t.tid,i.isyouhui,i.iprice_yh,i.comfee_yh,i.roomfee_yh,i.providerfee_yh,f.isschoolfree,i.limitnum,i.islimit from ebh_pay_items i join ebh_classrooms r on (i.crid = r.crid) join ebh_pay_packages p on p.pid=i.pid left join ebh_pay_terms t on t.tid=p.tid left join ebh_folders f on f.folderid=i.folderid';
		$wherearr = array('i.status=0','p.status=1','f.del=0');
		if(!empty($param['pid'])) {
			$wherearr[] = 'i.pid='.$param['pid'];
		}
		if(!empty($param['pidlist'])) {	//根据pid的列表获取数据，如 1,2形式
			$wherearr[] = 'i.pid in('.$param['pidlist'].')';
		}
		if(!empty($param['itemidlist'])) {	//根据itemid组合获取详情列表，如1,2形式
			$wherearr[] = 'i.itemid in('.$param['itemidlist'].')';
		}
		if(!empty($param['tid'])){
			$wherearr[] = 'p.tid='.$param['tid'];
		}
		if(!empty($param['crid'])) {
			$wherearr[] = 'i.crid='.$param['crid'];
		}
		if(!empty($param['folderid'])) {
			$wherearr[] = 'i.folderid='.$param['folderid'];
		}
		if(!empty($param['q'])){
			$q = $this->db->escape_str($param['q']);
			$wherearr[] = '(i.iname like \'%'.$q.'%\' or p.pname like \'%'.$q.'%\' )';
		}
		if (!empty($param['crid'])) {
            $wherearr[] = '`i`.`crid`='.intval($param['crid']);
        }

		if(!empty($wherearr)) {
			$sql .= ' WHERE ' . implode(' AND ', $wherearr);
		}
		if(!empty($param['displayorder'])) {
            $sql .= ' ORDER BY '.$param['displayorder'];
        } else {
            $sql .= ' ORDER BY itemid desc';
        }
		if(!empty($param['limit'])) {
            $sql .= ' limit '. $param['limit'];
        } else {
			if (empty($param['page']) || $param['page'] < 1)
				$page = 1;
			else
				$page = $param['page'];
			$pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
			$start = ($page - 1) * $pagesize;
            $sql .= ' limit ' . $start . ',' . $pagesize;
        }
		return $this->db->query($sql)->list_array();
	}
	/**
	*获取服务包内项目列表数量
	*/
	public function getItemListCount($param) {
		$count = 0;
		$sql = 'select count(*) count from ebh_pay_items i join ebh_classrooms r on (i.crid = r.crid) join ebh_pay_packages p on p.pid=i.pid';
		$wherearr = array();
		if(!empty($param['pid'])) {
			$wherearr[] = 'i.pid='.$param['pid'];
		}
		if(!empty($param['itemidlist'])) {	//根据itemid组合获取详情列表，如1,2形式
			$wherearr[] = 'i.itemid in('.$param['itemidlist'].')';
		}
		if(!empty($param['crid'])) {
			$wherearr[] = 'i.crid='.$param['crid'];
		}
		if(!empty($param['folderid'])) {
			$wherearr[] = 'i.folderid='.$param['folderid'];
		}
		if(!empty($wherearr)) {
			$sql .= ' WHERE ' . implode(' AND ', $wherearr);
		}
		$row = $this->db->query($sql)->row_array();
		if(!empty($row))
			$count = $row['count'];
		return $count;
	}
	/**
	*获取服务包内项目列表(针对课程)
	*/
	public function getItemFolderList($param) {
		$sql = 'select i.itemid,i.pid,i.crid,i.folderid,i.iname,i.isummary,i.iprice,i.imonth,i.iday,i.grade,i.sid,s.sname,f.foldername,f.summary,f.img,f.coursewarenum,f.viewnum,f.ispublic,f.fprice,f.speaker,s.showbysort,s.ishide,s.imgurl simg,s.content,f.credit from ebh_pay_items i '.
				'join ebh_folders f on (i.folderid = f.folderid) '.
				'left join ebh_pay_sorts s on (s.sid=i.sid)';
		$wherearr = array();
		if(!empty($param['pid'])) {
			$wherearr[] = 'i.pid='.$param['pid'];
		}
		if(!empty($param['pidlist'])) {	//根据pid的列表获取数据，如 1,2形式
			$wherearr[] = 'i.pid in('.$param['pidlist'].')';
		}
		if(!empty($param['itemidlist'])) {	//根据itemid组合获取详情列表，如1,2形式
			$wherearr[] = 'i.itemid in('.$param['itemidlist'].')';
		}

		if(!empty($param['crid'])) {
			$wherearr[] = 'i.crid='.$param['crid'];
		}
		if(!empty($param['folderid'])) {
			$wherearr[] = 'i.folderid='.$param['folderid'];
		}
		if(!empty($param['needsid'])){
			$wherearr[] = 'i.sid<>0';
		}
		if(isset($param['power']))
			$wherearr[] = 'f.power in ('.$param['power'].')';
		if(!empty($wherearr)) {
			$sql .= ' WHERE ' . implode(' AND ', $wherearr);
		}
		if(!empty($param['displayorder'])) {
            $sql .= ' ORDER BY '.$param['displayorder'];
        } else {
            $sql .= ' ORDER BY pid desc';
        }
		if(!empty($param['limit'])) {
            $sql .= ' limit '. $param['limit'];
        } else {
			if (empty($param['page']) || $param['page'] < 1)
				$page = 1;
			else
				$page = $param['page'];
			$pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
			$start = ($page - 1) * $pagesize;
            $sql .= ' limit ' . $start . ',' . $pagesize;
        }
		return $this->db->query($sql)->list_array();
	}
	/**
	*获取服务包内项目列表数量
	*/
	public function getItemListFolderCount($param) {
		$count = 0;
		$sql = 'select count(*) count from ebh_pay_items i join ebh_folder f on (i.folderid = f.folderid)';
		$wherearr = array();
		if(!empty($param['pid'])) {
			$wherearr[] = 'i.pid='.$param['pid'];
		}
		if(!empty($param['itemidlist'])) {	//根据itemid组合获取详情列表，如1,2形式
			$wherearr[] = 'i.itemid in('.$param['itemidlist'].')';
		}
		if(!empty($param['crid'])) {
			$wherearr[] = 'i.crid='.$param['crid'];
		}
		if(!empty($param['folderid'])) {
			$wherearr[] = 'i.folderid='.$param['folderid'];
		}
		if(!empty($wherearr)) {
			$sql .= ' WHERE ' . implode(' AND ', $wherearr);
		}
		$row = $this->db->query($sql)->row_array();
		if(!empty($row))
			$count = $row['count'];
		return $count;
	}
	/**
	*根据itemid获取服务明细项详情
	*/
	public function getItemByItemid($itemid) {
		$sql = "select i.itemid,i.pid,i.iname,i.isummary,i.iprice,i.imonth,i.iday,i.folderid,i.sid,cr.crid,cr.crname,p.pname,p.crid pcrid,f.fprice,cr.domain,f.speaker,f.detail,i.providercrid,i.comfee,i.roomfee,i.providerfee from ebh_pay_items i join ebh_classrooms cr on i.crid=cr.crid join ebh_pay_packages p on p.pid=i.pid join ebh_folders f on i.folderid=f.folderid where i.itemid=$itemid"; 
		return $this->db->query($sql)->row_array();
	}
	/**
	*根据sid获取服务明细项列表
	*/
	public function getItemBySidOrItemid($param = array()) {
		if(empty($param['sid']) && empty($param['itemid']))
			return FALSE;
		$sql = "select i.itemid,i.pid,i.iname,i.isummary,i.iprice,i.imonth,i.iday,i.folderid,i.sid,cr.crid,cr.crname,p.pname,p.crid pcrid,f.fprice,cr.domain,f.speaker,f.detail,i.iprice_yh,i.isyouhui,i.cannotpay,i.limitnum,i.islimit from ebh_pay_items i join ebh_classrooms cr on i.crid=cr.crid join ebh_pay_packages p on p.pid=i.pid join ebh_folders f on i.folderid=f.folderid";
		$wherearr = array('i.status=0','p.status=1');
		if(!empty($param['sid']))
			$wherearr[] = 'i.sid='.$param['sid'];
		if(!empty($param['itemid']))
			$wherearr[] = 'i.itemid='.$param['itemid'];
		if (!empty($param['crid'])) {
            $wherearr[] = 'i.crid='.intval($param['crid']);
        }

		if(!empty($wherearr)) {
			$sql .= ' WHERE ' . implode(' AND ', $wherearr);
		}
		if(!empty($param['orderby'])){
		    $sql .= ' ORDER BY '.$param['orderby'];
		}
		return $this->db->query($sql)->list_array();
	}

	
	
	public function add($param){
		$spiarr['iname'] = $param['iname'];
		$spiarr['pid'] = $param['pid'];
		$spiarr['crid'] = $param['crid'];
		$spiarr['iprice'] = $param['iprice'];
		if(!empty($param['isummary']))
			$spiarr['isummary'] = $param['isummary'];
		if(!empty($param['folderid']))
			$spiarr['folderid'] = $param['folderid'];
		if(!empty($param['sid']))
			$spiarr['sid'] = $param['sid'];
		if(!empty($param['iday']))
			$spiarr['iday'] = $param['iday'];
		elseif(!empty($param['imonth']))
			$spiarr['imonth'] = $param['imonth'];
		if(!empty($param['providercrid']))
			$spiarr['providercrid'] = $param['providercrid'];
		if(!empty($param['comfee']))
			$spiarr['comfee'] = $param['comfee'];
		if(!empty($param['roomfee']))
			$spiarr['roomfee'] = $param['roomfee'];
		if(!empty($param['providerfee']))
			$spiarr['providerfee'] = $param['providerfee'];
			
		$spiarr['dateline'] = SYSTIME;
		
		return $this->db->insert('ebh_pay_items',$spiarr);
	}
	
	public function edit($param){
		if(empty($param['itemid']))
			exit;
		$spiarr['iname'] = $param['iname'];
		$spiarr['pid'] = $param['pid'];
		$spiarr['crid'] = $param['crid'];
		$spiarr['isummary'] = $param['isummary'];
		$spiarr['iprice'] = $param['iprice'];
		$spiarr['folderid'] = $param['folderid'];
		$spiarr['sid'] = $param['sid'];
		if(isset($param['comfee']))
			$spiarr['comfee'] = $param['comfee'];
		if(isset($param['roomfee']))
			$spiarr['roomfee'] = $param['roomfee'];
		if(isset($param['providerfee']))
			$spiarr['providerfee'] = $param['providerfee'];
		if(!empty($param['iday'])){
			$spiarr['iday'] = $param['iday'];
			$spiarr['imonth'] = 0;
		}elseif(!empty($param['imonth'])){
			$spiarr['imonth'] = $param['imonth'];
			$spiarr['iday'] = 0;
		}
		return $this->db->update('ebh_pay_items',$spiarr,'itemid='.$param['itemid']);
	}
	public function deleteitem($itemid){
		return $this->db->delete('ebh_pay_items','itemid='.$itemid);
	}
	
	/*
	无权限的服务项
	*/
	public function getItemFolderListNotPaid($param) {
		$sql = 'select i.itemid,i.pid,i.crid,i.folderid,i.iname,i.iprice,i.imonth,i.iday,f.foldername,f.img,f.ispublic,f.fprice,f.coursewarenum,i.sid from ebh_pay_items i '.
				'join ebh_folders f on (i.folderid = f.folderid) ';
		$wherearr = array();
		if(!empty($param['pid'])) {
			$wherearr[] = 'i.pid='.$param['pid'];
		}
		if(!empty($param['pidlist'])) {	//根据pid的列表获取数据，如 1,2形式
			$wherearr[] = 'i.pid in('.$param['pidlist'].')';
		}
		if(!empty($param['itemidlist'])) {	//根据itemid组合获取详情列表，如1,2形式
			$wherearr[] = 'i.itemid in('.$param['itemidlist'].')';
		}

		if(!empty($param['crid'])) {
			$wherearr[] = 'i.crid='.$param['crid'];
		}
		if(!empty($param['folderid'])) {
			$wherearr[] = 'i.folderid='.$param['folderid'];
		}
		if(isset($param['power']))
			$wherearr[] = 'f.power in ('.$param['power'].')';
		//不在用户权限表,并且课程不免费
		$wherearr[] = 'i.itemid not in (select itemid from ebh_userpermisions where uid='.$param['uid'].' and crid='.$param['crid'].' and enddate>='.(SYSTIME-86400).')';
		$wherearr[] = 'f.fprice>0';
		if(!empty($wherearr)) {
			$sql .= ' WHERE ' . implode(' AND ', $wherearr);
		}
		if(!empty($param['displayorder'])) {
            $sql .= ' ORDER BY '.$param['displayorder'];
        } else {
            $sql .= ' ORDER BY pid desc';
        }
		if(!empty($param['limit'])) {
            $sql .= ' limit '. $param['limit'];
        } else {
			if (empty($param['page']) || $param['page'] < 1)
				$page = 1;
			else
				$page = $param['page'];
			$pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
			$start = ($page - 1) * $pagesize;
            $sql .= ' limit ' . $start . ',' . $pagesize;
        }
		return $this->db->query($sql)->list_array();
	}
	
	/**
     * 根据itemid字符串组获取课程信息
     * @param string $itemids
     * @return array 
     */
	public function getFolderListByItemids($itemids,$crid=0){
		if(empty($itemids))
			return array();
		$sql = 'select i.itemid,i.folderid,i.iname,f.img,f.del,i.isummary summary 
				from ebh_folders f 
				join ebh_pay_items i on f.folderid=i.folderid';
		$sql.= ' where itemid in ('.$itemids.') ';
		if(!empty($crid)){
			$sql.= ' and f.crid='.$crid;
		}
		return $this->db->query($sql)->list_array('itemid');
	}

    /**
     * 获取服务项，过滤课程相同的服务项，只保留最后一个服务项
     * @param $sid 服务包分类ID
     * @param $pid 服务包ID
     * @param $crid 网校ID
     * @param int $limit 限制条件
     * @return array
     */
	public function getItemsForSort($sid, $pid, $crid, $limit = 0, $orderby ='') {
	    $pid = intval($pid);
	    $sid = intval($sid);
	    $crid = intval($crid);
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
	    if ($sid > 0) {
            $package = $this->db->query(
                'SELECT `sid`,`sname`,`a`.`pid`,`b`.`pname`,`a`.`showbysort` FROM `ebh_pay_sorts` `a` JOIN `ebh_pay_packages` `b` ON `b`.`pid`=`a`.`pid` WHERE `a`.`sid`='.$sid.' AND `a`.`pid`='.$pid)
                ->row_array();
        } else {
            $package = $this->db->query(
                'SELECT `pid`,`pname` FROM `ebh_pay_packages` WHERE `pid`='.$pid)
                ->row_array();
            if (!empty($package)) {
                $package['sid'] = 0;
                $package['sname'] = '其他';
                $package['showbysort'] = 0;
            }
        }
	    if (empty($package)) {
	        return array();
        }

        $wheres = array(
            '`a`.`crid`='.$crid,
            '`a`.`pid`='.$pid,
            '`a`.`sid`='.$sid,
            '`a`.`status`=0',
            '`b`.`del`=0',
            '`b`.`folderlevel`>1',
            '`b`.`power`=0'
        );
        $sql = 'SELECT `a`.`folderid` `fid`,`a`.`itemid`,`a`.`crid`,`a`.`iprice`,`a`.`iname`,`a`.`cannotpay`,`b`.`fprice`,`b`.`foldername` `name`,`b`.`img` face,0 as tname,`b`.`isschoolfree`,`b`.`speaker`,`b`.`viewnum`,`b`.`coursewarenum` FROM `ebh_pay_items` `a` JOIN `ebh_folders` `b` ON `b`.`folderid`=`a`.`folderid` WHERE '.implode(' AND ', $wheres);
        if(!empty($orderby)){
            $sql .= ' ORDER BY '.$orderby;
        }else{
            $sql .= ' ORDER BY `a`.`itemid` ASC';
        }
        if ($top > 0) {
            $sql .= ' LIMIT '.$offset.','.$top;
        }
        //本校课程
        $package['items'] = $this->db->query($sql)->list_array('itemid');
        if (!empty($package['items'])) {
            return $package;
        }
        $package['showbysort'] = 0;
        $wheres = array(
            '`a`.`crid`='.$crid,
            '`a`.`del`=0',
            '`b`.`pid`='.$pid,
            '`b`.`sid`='.$sid,
            '`b`.`status`=0',
            '`c`.`del`=0',
            '`c`.`folderlevel`>1',
            '`c`.`power`=0'
        );
        $sql = 'SELECT `b`.`folderid` `fid`,`b`.`itemid`,`b`.`crid`,`a`.`price` AS `iprice`,`b`.`iname`,`c`.`fprice`,`c`.`foldername` `name`,`c`.`img` face,0 as tname,`c`.`isschoolfree`,`c`.`speaker`,`c`.`viewnum`,`c`.`coursewarenum` FROM `ebh_schsourceitems` `a` JOIN `ebh_pay_items` `b` ON `b`.`itemid`=`a`.`itemid` 
                JOIN `ebh_folders` `c` ON `c`.`folderid`=`b`.`folderid` WHERE '.implode(' AND ', $wheres).' ORDER BY `c`.`displayorder`,`b`.`itemid` ASC';
	    if ($top > 0) {
	        $sql .= ' LIMIT '.$offset.','.$top;
        }
	    $package['items'] = $this->db->query($sql)->list_array();
	    return $package;
    }

    /**
     * 获取免费课程服务项相关服务项集
     * @param $itemid 服务项
     * @param $crid 网校ID
     * @param $is_schoolmate 是否学校学生
     * @return array
     */
    public function getFreeItems($itemid, $crid, $is_schoolmate) {
        $itemid = intval($itemid);
        $crid = intval($crid);
        $sql = 'SELECT `a`.`itemid`,`a`.`sid`,`a`.`iprice`,`b`.`isschoolfree`,IFNULL(`c`.`showbysort`,0) AS `showbysort` 
                FROM `ebh_pay_items` `a` JOIN `ebh_folders` `b` ON `b`.`folderid`=`a`.`folderid` 
                LEFT JOIN `ebh_pay_sorts` `c` ON `c`.`sid`=`a`.`sid` 
                WHERE `a`.`itemid`='.$itemid.' AND `a`.`crid`='.$crid.' AND `a`.`status`=0 AND `b`.`del`=0 AND `b`.`power`=0 AND `b`.`folderlevel`=2 AND IFNULL(`c`.`ishide`,0)=0';
        $item = $this->db->query($sql)->row_array();
        if (empty($item)) {
            //读取企业选课ID
            $wheres = array(
                '`a`.`crid`='.$crid,
                '`a`.`itemid`='.$itemid,
                '`a`.`del`=0',
                '`b`.`status`=0',
                '`d`.`status`=1',
                '`e`.`del`=0',
                '`e`.`power`=0',
                '`e`.`folderlevel`=2'
            );
            $sql = 'SELECT `a`.`itemid`,`a`.`price` AS `iprice`, `b`.`sid`, 0 AS `isschoolfree`,0 AS `showbysort` 
                FROM `ebh_schsourceitems` `a` 
                JOIN `ebh_pay_items` `b` ON `b`.`itemid`=`a`.`itemid` 
                JOIN `ebh_classrooms` `c` ON `c`.`crid`=`a`.`crid` 
                JOIN `ebh_pay_packages` `d` ON `d`.`pid`=`b`.`pid`
                JOIN `ebh_folders` `e` ON `e`.`folderid`=`b`.`folderid` 
                WHERE '.implode(' AND ', $wheres);
            $course = $this->db->query($sql)->row_array();
            if (empty($course) || $course['iprice'] > 0) {
                return array();
            }
            return array($itemid => $course);
        }
        if (empty($item) || !($item['iprice'] == 0 || !empty($item['isschoolfree']) && $is_schoolmate)) {
            return array();
        }
        $ret = array(
            $itemid => $item
        );
        if (!empty($item['showbysort'])) {
            $sql = 'SELECT `a`.`itemid`,`a`.`iprice`,`b`.`isschoolfree` FROM `ebh_pay_items` `a` LEFT JOIN `ebh_folders` `b` ON `b`.`folderid`=`a`.`folderid` WHERE `a`.`sid`='.$item['sid'].' AND `a`.`crid`='.$crid.' AND `a`.`status`=0 AND `b`.`del`=0';
            $items = $this->db->query($sql)->list_array('itemid');
            unset($items[$itemid]);
            foreach ($items as $k => $i) {
                if (!($i['iprice'] == 0 || !empty($i['isschoolfree']) && $is_schoolmate)) {
                    return array();
                }
                $ret[$k] = $i;
            }
        }
	    return $ret;
    }

    /**
     * 企业选课课程
     * @param $itemid
     * @param $crid
     * @return mixed
     */
    public function getSchCourse($itemid, $crid) {
        $itemid = intval($itemid);
        $crid = intval($crid);
        $wheres = array(
            '`a`.`crid`='.$crid,
            '`a`.`itemid`='.$itemid,
            '`a`.`del`=0',
            '`b`.`status`=0',
            '`d`.`status`=1',
            '`e`.`del`=0',
            '`e`.`power`=0',
            '`e`.`folderlevel`=2'
        );
        $sql = 'SELECT `a`.`price` AS `iprice`,`a`.`month` AS `imonth`,`b`.`itemid`,`b`.`pid`,`b`.`iname`,`b`.`isummary`,0 AS `iday`,`b`.folderid,`b`.`sid`,`a`.`price` AS `iprice_yh`,0 AS `isyouhui`,`a`.`crid`,`c`.`crname`,`c`.`domain`,`d`.`pname`,`d`.`crid` `pcrid`,`e`.`fprice`,`e`.`speaker`,`e`.`detail` 
                FROM `ebh_schsourceitems` `a` 
                JOIN `ebh_pay_items` `b` ON `b`.`itemid`=`a`.`itemid` 
                JOIN `ebh_classrooms` `c` ON `c`.`crid`=`a`.`crid` 
                JOIN `ebh_pay_packages` `d` ON `d`.`pid`=`b`.`pid`
                JOIN `ebh_folders` `e` ON `e`.`folderid`=`b`.`folderid` 
                WHERE '.implode(' AND ', $wheres);
        return $this->db->query($sql)->list_array();
    }

    /**
     * 企业选课课程详情
     * @param $itemid
     * @param $crid
     * @return mixed
     */
    public function getSchCourseInfo($itemid, $crid) {
        $itemid = intval($itemid);
        $crid = intval($crid);
        $roomdetail = $this->db->query(
            'SELECT `profitratio` FROM `ebh_classrooms` WHERE `crid`='.$crid)
            ->row_array();
        if (empty($roomdetail)) {
            return array();
        }
        $wheres = array(
            '`a`.`crid`='.$crid,
            '`a`.`itemid`='.$itemid,
            '`a`.`del`=0',
            '`b`.`status`=0',
            '`d`.`status`=1',
            '`e`.`del`=0',
            '`e`.`power`=0',
            '`e`.`folderlevel`=2'
        );
        $fields = array(
            '`b`.`itemid`','`b`.`pid`','`b`.`iname`','`b`.`isummary`','`b`.`iprice`', '`a`.`crid`','`a`.`compercent`','`a`.`roompercent`','`a`.`providerpercent`','`a`.`sourcecrid`','`b`.`folderid`', '`b`.`dateline`','`b`.`providercrid`',
            '`c`.`crname`','`c`.`summary`','`c`.`cface`','`c`.`domain`','`c`.`coursenum`','`c`.`examcount`','`c`.`ispublic`',
            '`d`.`pname`', '`f`.`tname`', '`f`.`tid`', '0 AS `isyouhui`', '`a`.`price` AS `iprice_yh`',
            '0 AS `isschoolfree`',
            '`a`.`price` AS `iprice`', '`a`.`month` AS `imonth`', '0 AS `iday`'
        );
        $sql = 'SELECT '.implode(',', $fields).' 
                FROM `ebh_schsourceitems` `a` 
                JOIN `ebh_pay_items` `b` ON `b`.`itemid`=`a`.`itemid` 
                JOIN `ebh_classrooms` `c` ON `c`.`crid`=`a`.`crid` 
                JOIN `ebh_pay_packages` `d` ON `d`.`pid`=`b`.`pid`
                JOIN `ebh_folders` `e` ON `e`.`folderid`=`b`.`folderid`
                LEFT JOIN `ebh_pay_terms` `f` ON `f`.`tid`=`d`.`tid`
                WHERE '.implode(' AND ', $wheres);
        $ret = $this->db->query($sql)->list_array();
        if (!empty($ret)) {
        	//只支持单课购买
        	if (empty($ret[0]['compercent']) && empty($ret[0]['roompercent']) && empty($ret[0]['providerpercent'])) {
        		$profitsql = 'select compercent,roompercent,providerpercent from ebh_schsources where crid='.$crid.' and sourcecrid='.$ret[0]['sourcecrid'].' limit 1';
	        	$profit = $this->db->query($profitsql)->row_array();
	        	if (empty($profit['compercent']) && empty($profit['roompercent']) && empty($profit['providerpercent'])) {
	        		$profitratio = unserialize($roomdetail['profitratio']);
	        	} else {
	        		$profitratio['company'] = $profit['compercent'];
	        		$profitratio['teacher'] = $profit['roompercent'];
	        		$profitratio['agent'] = $profit['providerpercent'];
	        	}
	            
	            
        	} else {
        		$profitratio['company'] = $ret[0]['compercent'];
        		$profitratio['teacher'] = $ret[0]['roompercent'];
        		$profitratio['agent'] = $ret[0]['providerpercent'];
        	}

        	$pre = $profitratio['company'] + $profitratio['agent'] + $profitratio['teacher'];
            foreach ($ret as &$orderparam) {
            	$orderparam['providercrid'] = $orderparam['sourcecrid'];
                $orderparam['comfee'] = sprintf('%.2f', $orderparam['iprice'] * $profitratio['company'] / $pre);
                $orderparam['roomfee'] = sprintf('%.2f', $orderparam['iprice'] * $profitratio['teacher'] / $pre);
                $orderparam['providerfee'] = $orderparam['iprice'] - $orderparam['comfee'] - $orderparam['roomfee'];

                $orderparam['comfee_yh'] = $orderparam['comfee'];
                $orderparam['roomfee_yh'] = $orderparam['roomfee'];
                $orderparam['providerfee_yh'] = $orderparam['providerfee'];
            }
        	
        }
        return $ret;
    }

    /**
     * 本网校服务项
     * @param int $crid 网校ID
     * @param int $pid 服务包ID
     * @param $sid 服务项分类ID
     * @return array
     */
    public function getSchoolItems($crid, $pid = 0, $sid = null) {
        $wheres = array(
            '`a`.`crid`='.$crid,
            '`a`.`status`=0',
            '`b`.`del`=0',
            '`b`.`power`=0',
            '`b`.`folderlevel`=2',
            '`c`.`status`=1',
            'IFNULL(`d`.`ishide`,0)=0'
        );
        if ($pid > 0) {
            $wheres[] = '`a`.`pid`='.$pid;
            if ($sid !== null) {
                $wheres[] = '`a`.`sid`='.$sid;
            }
        }
        $sql = 'SELECT `a`.`iprice`,`a`.`cannotpay`,`a`.`iname`,`a`.`itemid`,`a`.`pid`,`a`.`sid`,`b`.`folderid`,`b`.`foldername`,`b`.`img`,`b`.`speaker`,`c`.`pname`,`c`.`displayorder`,`d`.`sname`,`d`.`sdisplayorder`,IFNULL(`e`.`prank`,0) AS `prank` FROM `ebh_pay_items` `a` 
                JOIN `ebh_folders` `b` ON `b`.`folderid`=`a`.`folderid` 
                JOIN `ebh_pay_packages` `c` ON `c`.`pid`=`a`.`pid` 
                LEFT JOIN `ebh_pay_sorts` `d` ON `d`.`sid`=`a`.`sid`
                LEFT JOIN `ebh_courseranks` `e` ON `e`.`folderid`=`b`.`folderid` AND `e`.`crid`=`b`.`crid` 
                WHERE '.implode(' AND ', $wheres);
        $ret = $this->db->query($sql)->list_array('itemid');
        if (empty($ret)) {
            return array();
        }
        return $ret;
    }

    /**
     * 网校企业选课课程
     * @param int $crid 网校ID
     * @param int $pid 服务包ID
     * @param $sid 服务项分类ID
     * @return array
     */
    public function getSchItems($crid, $pid = 0, $sid = null) {
        $wheres = array(
            '`a`.`crid`='.$crid,
            '`a`.`del`=0',
            '`b`.`status`=0',
            '`d`.`del`=0',
            '`d`.`power`=0',
            '`d`.`folderlevel`=2',
            '`e`.`status`=1',
            'IFNULL(`f`.`ishide`,0)=0'
        );
        if ($pid > 0) {
            $wheres[] = '`b`.`pid`='.$pid;
            if ($sid !== null) {
                $wheres[] = '`b`.`sid`='.$sid;
            }
        }
        $sql = 'SELECT `a`.`price`,`b`.`iname`,`b`.`cannotpay`,`b`.`itemid`,`b`.`pid`,`b`.`sid`,`c`.`displayorder` AS `rdisplayorder`,`c`.`crid`,`d`.`folderid`,`d`.`foldername`,`d`.`speaker`,`d`.`img`,`e`.`pname`,`e`.`displayorder`,`f`.`sname`,`f`.`sdisplayorder`,IFNULL(`g`.`prank`,0) AS `prank` 
                FROM `ebh_schsourceitems` `a` JOIN `ebh_pay_items` `b` ON `b`.`itemid`=`a`.`itemid` 
                JOIN `ebh_classrooms` `c` ON `c`.`crid`=`a`.`sourcecrid` 
                JOIN `ebh_folders` `d` ON `d`.`folderid`=`b`.`folderid`
                JOIN `ebh_pay_packages` `e` ON `e`.`pid`=`b`.`pid`
                LEFT JOIN `ebh_pay_sorts` `f` ON `f`.`sid`=`b`.`sid` 
                LEFT JOIN `ebh_courseranks` `g` ON `g`.`folderid`=`d`.`folderid` AND `g`.`crid`=`d`.`crid` 
                WHERE '.implode(' AND ', $wheres);
        $ret = $this->db->query($sql)->list_array('itemid');
        if (empty($ret)) {
            return array();
        }
        return $ret;
    }
}