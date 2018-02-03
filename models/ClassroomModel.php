<?php

/*
 * ClassroomModel教师平台model类
 */

class ClassroomModel extends CModel {
    
    public function getroomlist($param = array(), $select = '') {
        if (!empty($select))
            $sql = 'select ' . $select . ' from ebh_classrooms cr ';
        else
            $sql = 'select cr.crid,cr.upid,cr.catid,cr.crname,cr.summary,cr.dateline,cr.cface,cr.domain,cr.crprice  from ebh_classrooms cr ';
        $wherearr = array();
        if (isset($param['status']))
            $wherearr[] = 'cr.status=' . $param['status'];
        if (isset($param['upid'])) {
            $wherearr[] = 'cr.upid=' . $param['upid'];
        }
        if (!empty($param['filterorder']))
            $wherearr[] = 'cr.displayorder < ' . $param['filterorder'];
        if (!empty($wherearr))
            $sql .= ' WHERE ' . implode(' AND ', $wherearr);
        if (!empty($param['order']))
            $sql .= ' ORDER BY ' . $param['order'];
        else
            $sql .= ' ORDER BY cr.crid desc ';
        if (!empty($param['limit']))
            $sql .= ' limit ' . $param['limit'];
        else
            $sql .= ' limit 0,10';
        return $this->db->query($sql)->list_array();
    }

    /**
     * 根据域名查询room平台信息
     * @param string $domain需查域名
     */
    public function getroomdetailbydomain($domain) {
        $domain = $this->db->escape($domain);
        $sql = "select cr.crid,cr.uid,cr.crname,cr.domain,cr.template,cr.isschool,cr.dateline,cr.summary,cr.crlabel,cr.cface,cr.crqq,cr.craddress,cr.crphone,cr.cremail,cr.modulepower,cr.stumodulepower,cr.lng,cr.lat,cr.crprice from ebh_classrooms cr where cr.domain=$domain";
	//	echo $sql;exit;
        return $this->db->query($sql)->row_array();
    }

    /**
     * 判断学生是否有教室权限
     * @param int $uid 用户编号
     * @param int $crid 对应教室编号
     * @param boolean $charge 是否为收费平台，如为收费平台，则需要验证有效期
     * @return int 返回验证结果，1表示有权限 2表示已过期 0表示用户已停用 -1表示无权限 -2参数非法
     */
    public function checkstudent($uid, $crid, $charge = false) {
        if(empty($uid) || empty($crid)){
            return -2;
        }
        $sql = "select u.`status`,ru.cstatus,ru.begindate,ru.enddate from ebh_users u join ebh_roomusers ru on (u.uid = ru.uid) where u.uid=$uid and ru.crid=$crid";
        $data = $this->db->query($sql)->row_array();
        if (empty($data))
            return -1;
        if ($data['status'] != 1)
            return 0;
        /*if (!empty($data['enddate']) && $data['enddate'] < (EBH_BEGIN_TIME + 86400))
            return 2;
        if ($charge) { //如果为收费平台，如果没有时间或者已过期
            if (empty($data['enddate']) || $data['enddate'] < (EBH_BEGIN_TIME + 86400)) {
                return 2;
            }
        }*/
        return 1;
    }

    /*
      判断教师是否有教室权限
      @param $tid 教师编号
      @param $crid 对应教室编号
      @return 返回验证结果，1表示有权限 0表示用户已停用 -1表示无权限
     */

    function checkteacher($tid, $crid) {
        $sql = "select u.status ustatus,rt.status tstatus from ebh_users u join ebh_roomteachers rt on (u.uid = rt.tid) where u.uid = $tid and crid=$crid";
        $data = $this->db->query($sql)->row_array();
        if (empty($data))
            return -1;
        if ($data['ustatus'] != 1 || $data['tstatus'] != 1)
            return 0;
        return 1;
    }

    /*
      判断教师是否对学校有控制权限，一般为上级学校的所有者能对子集的学校进行管理
      @param $tid 教师编号
      @param $crid 对应教室编号
      @return 返回验证结果，1表示有权限 其他为无权限
     */

    function checkcontrolteacher($tid, $crid) {
        $upid = 0;
        $haspower = 0;
        while (true) {
            $sql = 'select upid from ebh_classrooms where crid=' . $crid;
            $row = $this->db->query($sql)->row_array();
            if (empty($row) || empty($row['upid']))
                break;
            $upsql = 'select upid,uid from ebh_classrooms where crid=' . $row['upid'];
            $uprow = $this->db->query($upsql)->row_array();
            if (empty($uprow))
                break;
            if ($uprow['uid'] == $tid) {
                $haspower = 1;
                break;
            } else {
                $crid = $row['upid'];
            }
        }
        return $haspower;
    }


	/**
     * 根据学生编号获取学生有权限的平台
     * @param int $uid学生编号
     * @return array 平台列表
     */
    function getroomlistbyuid($uid) {
        $sql = 'select c.crid as rid,c.crname as rname,c.summary,c.isschool,c.cface face,rc.enddate from ebh_roomusers rc ' .
                'join ebh_classrooms c on (rc.crid = c.crid) ' .
                'where rc.uid = ' . $uid . ' and rc.cstatus = 1';
        $list = $this->db->query($sql)->list_array();
        $roomlist = array();
        foreach($list as $row) {
        	if($row['isschool'] == 3) {
        		$row['status'] = 1;
        		$row['enddate'] = '无限制';
        	} else {
        		if($row['enddate'] < SYSTIME) {
        			$row['status'] = 0;
        		} else {
					$row['status'] = 1;
				}
        		$row['enddate'] = empty($row['enddate']) ? '' : date('Y-m-d',$row['enddate']);
        	}
			$face = $row['face'];
			if (empty($face))
				$face = 'http://static.ebanhui.com/ebh/tpl/default/images/elist_tx.jpg';
			else if (strpos( $face,'ebanhui.com') === FALSE) {
				$face = 'http://www.ebanhui.com'.$face;
			}
			$row['face'] = $face;
        	$roomlist[] = $row;
        }
        return $roomlist;
    }
	/**
	*根据crid获取演示平台信息
	*/
	public function getDemoRoomByRid($crid) {
		$sql = "select c.crid as rid,c.crname as rname,c.summary,c.cface face,0 as enddate,c.isschool from ebh_classrooms c where crid=$crid";
		$roomitem = $this->db->query($sql)->row_array();
		if(!empty($roomitem)) {
			$roomitem['enddate'] = '无限制';
			$face = $roomitem['face'];
			if (empty($face))
				$face = 'http://static.ebanhui.com/ebh/tpl/default/images/elist_tx.jpg';
			else if (strpos( $face,'ebanhui.com') === FALSE) {
				$face = 'http://www.ebanhui.com'.$face;
			}
			$roomitem['face'] = $face;
		}
		return $roomitem;
	}
	
	
    /**
     * 获取用户在本平台的余额
     * @param int $crid
     * @param int $uid
     */
    function getuserroombalance($crid, $uid) {
        if (empty($crid) || empty($uid))
            return 0;
        $sql = 'select rbalance from ebh_roomusers ru where ru.crid = ' . $crid . ' and ru.uid=' . $uid;
        $balancerow = $this->db->query($sql)->row_array();
        if (empty($balancerow) || empty($balancerow['rbalance']))
            return 0;
        return $balancerow['rbalance'];
    }

    /**
     * 判断用户是否对共享平台有播放权限
     * @param int $crid 共享平台id
     * @param int $uid 用户编号
     * @param int $groupid 用户组编号
     * @return int 1表示有权限，-1表示无权限
     */
    function checkshareuser($crid, $uid, $groupid) {
        if (empty($uid) || empty($crid))
            return false;
        $sql = '';
        if ($groupid == 5) {
            $sql = 'select count(*) count from ebh_roomteachers rt join ebh_roompermissions rp on (rt.crid=rp.crid) where rt.tid=' . $uid;
        } else {
            $sql = 'select count(*) count from ebh_roomusers ru join ebh_roompermissions rp on (ru.crid=rp.crid) where ru.uid=' . $uid;
        }
        $countrow = $this->db->query($sql)->row_array();
        if (empty($countrow) || $countrow['count'] == 0)
            return -1;
        return 1;
    }

	//免费课件(金华,cq,fssq免费试听)
	function getfreecourse($para){
	//	print_r($para);
		$sql = 'SELECT cw.cwid,cw.title,cw.summary,cw.logo,cw.cwsource,r.crid,r.isfree,IFNULL(s.displayorder,1000) sdisplayorder from ebh_roomcourses r left join ebh_coursewares cw on r.cwid = cw.cwid left join ebh_sections s on r.sid = s.sid ';
		$wherearr = array();
	
		if (!empty($para['crid'])) {
            $wherearr[] = ' r.crid in (' . $para['crid'] .') '  ;
        }
		if (!empty($para['status'])) {
            $wherearr[] = ' cw.status in (' . $para['status'] . ') ';
        }
		if (!empty($para['isfree'])) {
        	$wherearr[] = 'r.isfree = '.$para['isfree'];
        }
		if(!empty($wherearr)) {
            $sql .= ' WHERE '.implode(' AND ',$wherearr);
        }
        if(!empty($para['displayorder'])) {
            $sql .= ' ORDER BY '.$para['displayorder'];
        } else {
            $sql .= ' ORDER BY r.displayorder';
        }
        if(!empty($para['limit'])) {
            $sql .= ' limit '. $para['limit'];
        } else {
            $sql .= ' limit 0,10';
        }
	echo $sql;
        return $this->db->query($sql)->list_array();
	}

/*
	后台获取教室列表
	*/
	public function getclassroomlist($param){
		$sql = 'select c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,u.realname from ebh_classrooms c join ebh_users u on u.uid = c.uid';
		if(isset($param['q']))
			$wherearr[] = ' c.crname like \'%'. $this->db->escape_str($param['q']) .'%\'';
		if(!empty($param['crid']))
			$wherearr[] = ' crid = '.$param['crid'];
		if(!empty($wherearr))
			$sql.= ' WHERE '.implode(' AND ',$wherearr);
		$sql.=' order by crid desc';
		if(!empty($param['limit']))
			$sql.= ' limit ' . $param['limit'];
		//var_dump($sql);
		return $this->db->query($sql)->list_array();
	}
	/*
	简单无条件查询，供下拉菜单使用
	*/
	public function getsimpleclassroomlist(){
		$sql = 'select c.crid,c.crname from ebh_classrooms c';
		return $this->db->query($sql)->list_array();
	}
	/*
	后台获取教室数量
	*/
	public function getclassroomcount($param){
		$sql = 'select count(*) count from ebh_classrooms c ';
		if(!empty($param['q']))
			$wherearr[] = ' c.crname like \'%'. $this->db->escape_str($param['q']) .'%\'';
		if(!empty($param['crid']))
			$wherearr[] = ' crid = '.$param['crid'];
		if(!empty($wherearr))
			$sql.= ' WHERE '.implode(' AND ',$wherearr);
		$count = $this->db->query($sql)->row_array();
		return $count['count'];
	}
	/*
	删除教室
	@param $crid 教室编号
	@return int
	*/
	public function deleteclassroom($crid){
		return $this->db->delete('ebh_classrooms','crid='.$crid);
	}
	/*
	添加教室
	@param array $param
	@return int
	*/
	public function addclassroom($param){
		$param['citycode'] = $param['address_qu']?$param['address_qu']:($param['address_shi']?$param['address_shi']:$param['address_sheng']);
		unset($param['address_sheng']);
		unset($param['address_shi']);
		unset($param['address_qu']);
		unset($param['username']);
		
		if(!empty($param['roompermission']))
		{
			$rparr = $param['roompermission'];
			unset($param['roompermission']);
		}
		
		$res = $this->db->insert('ebh_classrooms',$param);
		if($res && $rparr){//共享平台分配
			foreach($rparr as $rp){
				$arr['crid'] = $res;
				$arr['moduleid'] = $rp;
				$arr['moduletype'] = 1;
				$this->db->insert('ebh_roompermissions',$arr);
			}
		}
		
		return $res;
	}
	/*
	编辑教室
	@param array $param
	@return int
	*/
	public function editclassroom($param){
		if(isset($param['status']))
			$setarr['status'] = $param['status'];
		if(!empty($param['crname']))
			$setarr['crname'] = $param['crname'];
		if(!empty($param['cface']))
			$setarr['cface'] = $param['cface'];
		if(!empty($param['uid']))
			$setarr['uid'] = $param['uid'];
		if(!empty($param['catid']))
			$setarr['catid'] = $param['catid'];
		if(isset($param['upid']))
			$setarr['upid'] = $param['upid'];
		if(!empty($param['citycode']))
			$setarr['citycode'] = $param['citycode'];
		if(!empty($param['craddress']))
			$setarr['craddress'] = $param['craddress'];
		if(!empty($param['crphone']))
			$setarr['crphone'] = $param['crphone'];
		if(!empty($param['cremail']))
			$setarr['cremail'] = $param['cremail'];
		if(!empty($param['crqq']))
			$setarr['crqq'] = $param['crqq'];
                if(!empty($param['lng']))
			$setarr['lng'] = $param['lng'];
                if(!empty($param['lat']))
			$setarr['lat'] = $param['lat'];
		if(!empty($param['domain']))
			$setarr['domain'] = $param['domain'];
		if(!empty($param['maxnum']))
			$setarr['maxnum'] = $param['maxnum'];
		if(!empty($param['crlabel']))
			$setarr['crlabel'] = $param['crlabel'];
		if(!empty($param['summary']))
			$setarr['summary'] = $param['summary'];
                if(!empty($param['message']))
			$setarr['message'] = $param['message'];
		if(isset($param['ispublic']))
			$setarr['ispublic'] = $param['ispublic'];
		if(isset($param['isshare']))
			$setarr['isshare'] = $param['isshare'];
		if(isset($param['isschool']))
			$setarr['isschool'] = $param['isschool'];
		if(!empty($param['begindate']))
			$setarr['begindate'] = $param['begindate'];
		if(!empty($param['enddate']))
			$setarr['enddate'] = $param['enddate'];
		if(!empty($param['template']))
			$setarr['template'] = $param['template'];
		if(isset($param['modulepower']))
			$setarr['modulepower'] = $param['modulepower'];
		if(isset($param['stumodulepower']))
			$setarr['stumodulepower'] = $param['stumodulepower'];
		$wherearr = array('crid'=>$param['crid']);
		$row = $this->db->update('ebh_classrooms',$setarr,$wherearr);
		return $row;
	}
	/*
	详情
	@param int $crid
	@return array
	*/
	public function getclassroomdetail($crid, $domain = ''){
		$sql = 'select c.catid,c.crid,c.crname,c.begindate,c.upid,c.enddate,c.maxnum,c.domain,c.status,c.citycode,c.cface,c.craddress,c.crqq,c.crphone,c.cremail,c.crlabel,c.summary,c.ispublic,c.isshare,c.modulepower,c.stumodulepower,c.isschool,c.template,c.property,u.username,u.uid,u.realname,s.refuse_stranger,c.profitratio from ebh_classrooms c join ebh_users u on u.uid = c.uid left join ebh_systemsettings s on s.crid=c.crid';
		if (!empty($crid) && $crid > 0) {
		    $sql .= ' where c.crid='.$crid;
        } else if (!empty($domain)) {
		    $sql .= ' where c.domain='.$this->db->escape($domain);
        } else {
		    return false;
        }
		
		//var_dump( $sql);
		return $this->db->query($sql)->row_array();
	}
	
	
	/*
	域名是否存在
	@param string $domain
	*/
	public function exists_domain($domain){
		$sql = 'select 1 from ebh_classrooms where domain = "'.$domain .'" limit 1';
		return $this->db->query($sql)->row_array();
	}
	/*
	网校名是否存在
	@param string $domain
	*/
	public function exists_crname($crname){
		$sql = 'select 1 from ebh_classrooms where crname = "'.$crname .'" limit 1';
		return $this->db->query($sql)->row_array();
	}
	/*
	教室权限
	$param int $upid 区分老师/学生权限
	@return array
	*/
	public function getroompowerlist($upid){
		$sql = 'select c.catid,c.name from ebh_categories c where c.system=0 and c.visible=1 and c.upid ='.$upid;
		return $this->db->query($sql)->list_array();
	}
	/*
	共享平台分配列表
	@return array
	*/
	public function getsharelist(){
		$sql = 'select c.crid,c.crname from ebh_classrooms c where isshare = 1';
		return $this->db->query($sql)->list_array();
		
	}
	/*
	教室所使用的共享平台
	@param int $crid
	@return array
	*/
	public function getroompermission($crid){
		$sql = 'select r.moduleid from ebh_roompermissions r where r.crid='.$crid;
		return $this->db->query($sql)->list_array();
	}
	/*
	子网校数量
	*/
	public function getzwxcount($crid){
		$sql = 'select count(*) count from ebh_classrooms where upid ='.$crid;
		return $this->db->query($sql)->row_array();
	}
	/*
	子网校列表
	*/
	public function getzwxlist($param){
		$sql = 'SELECT cr.crid,cr.crname,cr.cface,cr.status,cr.domain,cr.score,cr.summary,cr.coursenum,IFNULL(texam.examcount,0)as examcount FROM ebh_classrooms cr LEFT JOIN (select COUNT(*) examcount,crid from ebh_exams group by crid) texam on cr.crid=texam.crid ';
		$wherearr = array();
		if (!empty($param['crid'])) {
            $wherearr[] = ' cr.upid = '.$param['crid'] ;
        }
		if(!empty($wherearr)) {
            $sql .= ' WHERE '.implode(' AND ',$wherearr);
        }
		if(!empty($param['order'])) {
            $sql .= ' ORDER BY '.$param['order'];
        } else {
            $sql .= ' ORDER BY displayorder';
        }
        if(!empty($param['limit'])) {
            $sql .= ' limit '.$param['limit'];
        } else {
            $sql .= ' limit 0，10';
        }
		echo $sql;
		return $this->db->query($sql)->list_array();
	}

	/**
	 * 根据crid获取教室详细信息
	 * @param type $crid
	 * @return type
	 */
	public function getdetailclassroom($crid) {
		$sql = "select cr.crid,cr.uid,cr.crname,cr.domain,cr.template,cr.isschool,cr.summary,cr.crlabel,cr.cface,cr.crqq,cr.craddress,cr.crphone,cr.cremail,cr.modulepower,cr.stumodulepower,cr.bankcard,"
				. "cr.dateline,cr.banner,cr.displayorder,cr.viewnum,cr.score,cr.onlinecount,cr.lng,cr.lat,cr.weibosina,cr.stunum,cr.teanum,cr.coursenum,cr.message,cr.good,cr.bad,cr.useful  from ebh_classrooms cr where cr.crid=$crid";
		return $this->db->query($sql)->row_array();
	}
	/*
	*主要获取教室的message信息
	*/
//	  public function getroomdetailbydomain($domain) {
//        $domain = $this->db->escape($domain);
//        $sql = "select cr.crid,cr.uid,cr.crname,cr.domain,cr.template,cr.isschool,cr.dateline,cr.summary,cr.crlabel,cr.cface,cr.crqq,cr.craddress,cr.crphone,cr.cremail,cr.modulepower,cr.stumodulepower,cr.lng,cr.lat,cr.crprice from ebh_classrooms cr where cr.domain=$domain";
//	//	echo $sql;exit;
//        return $this->db->query($sql)->row_array();
	public function getroomdetail(){
		
	}
	/**
     * 添加教室对应的答疑数
     * @param int $crid 教室编号
     * @param int $num 如为正数则添加，负数则为减少
     */
    public function addasknum($crid,$num=1) {
        $where = 'crid='.$crid;
        $setarr = array('asknum'=>'asknum+'.$num);
        $this->db->update('ebh_classrooms',array(),$where,$setarr);
    }
    /**
     *获取教师的学校列表
     */
    public function getTeacherRooms($tid = 0){
    	$sql = 'select rt.crid as rid,rt.status,cr.crname as rname,cr.summary,cr.cface as face,cr.enddate,cr.isschool from ebh_roomteachers rt join ebh_classrooms cr on rt.crid = cr.crid where rt.tid = '.$tid;
    	$roomlist =  $this->db->query($sql)->list_array();
    	$ret = array();
    	foreach ($roomlist as $room) {
    		if(!empty($room['enddate'])){
    			$room['enddate'] = date('Y-m-d',$room['enddate']);
    		}else{
    			$room['enddate'] = "";
    		}
    		if(empty($room['face']))
				$room['face']  = 'http://static.ebanhui.com/ebh/tpl/default/images/elist_tx.jpg';
			else if (strpos( $room['face'] ,'ebanhui.com') === FALSE) {
				$room['face']  = 'http://www.ebanhui.com'.$room['face'] ;
			}
    		$ret[] = $room;
    	}
    	return $ret;
    }

    /**
	*大厅教室列表显示
	*/
	public function getclassroomall($param){
		$sql = 'SELECT cr.crname,cr.summary,cr.begindate,cr.cface,cr.domain,cr.template,cr.isschool,cr.craddress,cr.ispublic,cr.score,cr.crid,cr.uid FROM ebh_classrooms cr';
		$wherearr = array();
		if(!empty($param['q']))
			$wherearr[] = '(cr.crname like \'%'. $this->db->escape_str($param['q']) .'%\' or cr.domain like \'%'. $this->db->escape_str($param['q']) .'%\')';
		if(!empty($param['property']))
			$wherearr[] = 'property = '.intval($param['property']);
		if(!empty($param['isschool']))
			$wherearr[] = 'isschool = '.intval($param['isschool']);
		if (!empty($param['filterorder']))
            $wherearr[] = 'cr.displayorder < ' . $param['filterorder'];
		if(!empty($param['grade']))
			$wherearr[] = 'cr.grade = '.intval($param['grade']);
		if(!empty($param['citycode']))
			$wherearr[] = 'cr.citycode like \''.$this->db->escape_str($param['citycode']).'%\'';
		if(!empty($param['subject'])){
			$wherearr[] = ' cr.crname like \'%'. $this->db->escape_str($param['subject']) .'%\'';
		}
		if(!empty($wherearr)) {
            $sql .= ' WHERE '.implode(' AND ',$wherearr);
        }
		if(!empty($param['order'])) {
            $sql .= ' ORDER BY '.$param['order'];
        } else {
            $sql .= ' ORDER BY displayorder';
        }
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
		return $this->db->query($sql)->list_array();
	}

	/**
	*根据教室编号获取教室对应的信息
	*/
	public function getRoomByCrid($crid) {
		$domain = '';
		$sql = "select crid,domain,isschool,good,useful,bad,score,viewnum from ebh_classrooms where crid=$crid";
		return $this->db->query($sql)->row_array();
	}

	 /**
     * 添加教室对应的学生数
     * @param int $crid 教室编号
     * @param int $num 如为正数则添加，负数则为减少
     */
    public function addstunum($crid,$num = 1) {
        $where = 'crid='.$crid;
        $setarr = array('stunum'=>'stunum+'.$num);
        $this->db->update('ebh_classrooms',array(),$where,$setarr);
    }
	/**
	*获取教室简短信息
	*/
	public function getSimpleRoom($crid) {
		$sql = "select crid,crname,isschool from ebh_classrooms where crid=$crid";
		return $this->db->query($sql)->row_array();
	}

	/**
     *获取网校的分享信息
     */
    public function getShareInfo($crid=0) {
        $crid = intval($crid);
        if (empty($crid)) {
            return false;
        }
        $sql = 'select isshare,sharepercent from ebh_systemsettings where crid='.$crid;
        return $this->db->query($sql)->row_array();
    }
}
