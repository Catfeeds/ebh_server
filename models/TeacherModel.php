<?php

/*
  教师
 */

class TeacherModel extends CModel {
    /*
      教师列表
      @param array $param
      @return array
     */

    public function getteacherlist($param) {
        $wherearr = array();
        $sql = 'select u.uid,u.realname,u.username,u.nickname,u.citycode,u.status,u.mobile,u.credit,u.logincount,t.tag,t.phone,t.agency,a.realname as agentname from ebh_teachers t left join ebh_users u on t.teacherid=u.uid left join ebh_agents a on t.agentid=a.agentid';
        if (!empty($param['q']))
            $wherearr[] = ' ( u.realname like \'%' . $this->db->escape_str($param['q']) . '%\' or u.username like \'%' . $this->db->escape_str($param['q']) . '%\')';
        if (!empty($wherearr))
            $sql.= ' WHERE ' . implode(' AND ', $wherearr);
        $sql.=' order by teacherid desc';
        if (!empty($param['limit']))
            $sql.= ' limit ' . $param['limit'];

        return $this->db->query($sql)->list_array();
    }

    /*
      教师总数
      @param array $param
      @return int
     */

    public function getteachercount($param) {
        $wherearr = array();
        $sql = 'select count(*) count from ebh_teachers t left join ebh_users u on t.teacherid=u.uid';
        if (!empty($param['q']))
            $wherearr[] = ' ( u.realname like \'%' . $this->db->escape_str($param['q']) . '%\' or u.username like \'%' . $this->db->escape_str($param['q']) . '%\')';
        if (!empty($wherearr))
            $sql.= ' WHERE ' . implode(' AND ', $wherearr);
        //var_dump($sql);
        $count = $this->db->query($sql)->row_array();
        return $count['count'];
    }
	/**
	* 添加教师的课件数
	* @param int uid 教师用户编号
	* @param int num 添加的课件数量
	*/
	public function addcoursenum($uid,$num = 1) {
		$where = 'teacherid='.$uid;
        $setarr = array('cwcount'=>'cwcount+'.$num);
        $this->db->update('ebh_teachers',array(),$where,$setarr);
	}
    /*
      修改教师信息(同时修改对应user表信息)
      $param array $param
      @return int
     */

    public function editteacher($param) {
        $afrows = FALSE;    //影响行数
        $userarr = array();
        //修改user表信息
        if (!empty($param['password']))
            $userarr['password'] = md5($param['password']);
        if (isset($param['status']))
            $userarr['status'] = $param['status'];
        if (isset($param['realname']))
            $userarr['realname'] = $param['realname'];
        if (isset($param['nickname']))
            $userarr['nickname'] = $param['nickname'];
        if (isset($param['sex']))
            $userarr['sex'] = $param['sex'];
        if (isset($param['mobile']))
            $userarr['mobile'] = $param['mobile'];
        if (isset($param['email']))
            $userarr['email'] = $param['email'];
        if (isset($param['citycode']))
            $userarr['citycode'] = $param['citycode'];

        if (isset($param['face'])){
			if(!is_array($param['face']))
				$userarr['face'] = $param['face'];
			else
				$userarr['face'] = $param['face']['upfilepath'];
		}
		if(isset($param['mysign']))
			$userarr['mysign'] = $param['mysign'];
        $wherearr = array('uid' => $param['uid']);
        if (!empty($userarr)) {
            $afrows = $this->db->update('ebh_users', $userarr, $wherearr);
        }
        //修改teacher表信息
        $teacherarr = array();
        
        if (isset($param['realname']))
            $teacherarr['realname'] = $param['realname'];
		if (isset($param['nickname']))
            $teacherarr['nickname'] = $param['nickname'];
        if (isset($param['phone']))
            $teacherarr['phone'] = $param['phone'];
		if (isset($param['fax']))
            $teacherarr['fax'] = $param['fax'];
		if (isset($param['sex']))
            $teacherarr['sex'] = $param['sex'];
		if (isset($param['mobile']))
            $teacherarr['mobile'] = $param['mobile'];
        if (isset($param['citycode']))
            $teacherarr['address'] = $param['citycode'];
        if (isset($param['message']))
            $teacherarr['message'] = $param['message'];
        if (isset($param['schoolage']))
            $teacherarr['schoolage'] = $param['schoolage'];
        if (isset($param['tag']))
            $teacherarr['tag'] = $param['tag'];
        if (isset($param['profile']))
            $teacherarr['profile'] = $param['profile'];
		if (isset($param['vitae']))
            $teacherarr['vitae'] = $param['vitae'];
		if (isset($param['profitratio']))
            $teacherarr['profitratio'] = $param['profitratio'];
        if (isset($param['bankcard']))
            $teacherarr['bankcard'] = $param['bankcard'];
        if (isset($param['agency']))
            $teacherarr['agency'] = $param['agency'];
        $wherearr = array('teacherid' => $param['uid']);
        if(isset($param['agentid'])){
        	$teacherarr['agentid'] = $param['agentid'];
        }
        if (isset($param['birthdate']))
        	$teacherarr['birthdate'] = $param['birthdate'];
        if (!empty($teacherarr)) {
 
            $afrows = $this->db->update('ebh_teachers', $teacherarr, $wherearr);
        }

        return $afrows;
    }

    /*
      教师详情
      @param int $uid
      @return array
     */

    public function getteacherdetail($uid) {
        $sql = 'select u.uid,u.username,u.realname,u.nickname,u.face,u.email,u.sex,u.groupid,u.mysign,u.balance,u.credit,t.profile,t.address,t.birthdate as birthday from ebh_users u left join ebh_teachers t on u.uid = t.teacherid where teacherid = ' . $uid;
        return $this->db->query($sql)->row_array();
    }

    /*
      删除教师
      @param int $uid
      @return bool
     */

    public function deleteteacher($uid) {
		$this->db->begin_trans();
		$this->db->delete('ebh_teachers','teacherid='.$uid);
		$this->db->delete('ebh_users','uid='.$uid);
		$sql = 'select crid from ebh_roomteachers where tid='.$uid;
		$cridarr = $this->db->query($sql)->row_array();
		if(!empty($cridarr)){
			$this->db->delete('ebh_roomteachers','tid='.$uid);
			foreach($cridarr as $crid)
				$this->db->update('ebh_classrooms', array(), array('crid' => $crid), array('teanum' => 'teanum-1'));
		}
		if ($this->db->trans_status() === FALSE) {
            $this->db->rollback_trans();
            return FALSE;
        } else {
            $this->db->commit_trans();
        }
		return TRUE;
    }

    /*
      添加教师
      @param array $param
      @return int
     */

    public function addteacher($param) {
		if(!empty($param['username']))
			$userarr['username'] = $param['username'];
		if(!empty($param['password']))
			$userarr['password'] = md5($param['password']);
		if(!empty($param['realname']))
			$userarr['realname'] = $param['realname'];
		if(isset($param['nickname']))
			$userarr['nickname'] = $param['nickname'];
		if(!empty($param['dateline']))
			$userarr['dateline'] = $param['dateline'];
		if(isset($param['sex']))
			$userarr['sex'] = $param['sex'];
		if(!empty($param['mobile']))
			$userarr['mobile'] = $param['mobile'];
		if(!empty($param['citycode']))
			$userarr['citycode'] = $param['citycode'];
        //$userarr['address'] = $param['address'];
        //$userarr['email'] = $param['email'];
		if(!empty($param['face']))
			$userarr['face'] = $param['face'];
		$userarr['status'] = 1;
		$userarr['groupid'] = 5;
        //var_dump($userarr);
        $uid = $this->db->insert('ebh_users', $userarr);
        if ($uid) {
            $teacherarr['teacherid'] = $uid;
			if(!empty($param['realname']))
				$teacherarr['realname'] = $param['realname'];
			if(isset($param['nickname']))
				$teacherarr['nickname'] = $param['nickname'];
			if(isset($param['sex']))
				$teacherarr['sex'] = $param['sex'];
            //$teacherarr['birthdate'] = $param['birthdate'];
			if(!empty($param['phone']))
				$teacherarr['phone'] = $param['phone'];
			if(!empty($param['mobile']))
				$teacherarr['mobile'] = $param['mobile'];
            //$teacherarr['native'] = $param['native'];
            //$teacherarr['citycode'] = $param['citycode'];
			if(!empty($param['citycode']))
				$teacherarr['address'] = $param['citycode'];
            //$teacherarr['msn'] = $param['msn'];
            //$teacherarr['qq'] = $param['qq'];
            //$teacherarr['email'] = $param['email'];
            //$teacherarr['face'] = $param['face'];
			if(!empty($param['vitae']))
				$teacherarr['vitae'] = $param['vitae'];
			if(!empty($param['fax']))
				$teacherarr['fax'] = $param['fax'];
			if(!empty($param['tag']))
				$teacherarr['tag'] = $param['tag'];
			if(isset($param['schoolage']))
				$teacherarr['schoolage'] = $param['schoolage'];
			if(!empty($param['profile']))
				$teacherarr['profile'] = $param['profile'];
            //var_dump($teacherarr);
            if(isset($param['profitratio']))
            $teacherarr['profitratio'] = $param['profitratio'];
        	if(isset($param['bankcard']))
            $teacherarr['bankcard'] = $param['bankcard'];
        	if(isset($param['agentid'])){
        		$teacherarr['agentid'] = $param['agentid'];
        	}
        	if (isset($param['agency']))
            	$teacherarr['agency'] = $param['agency'];
            if (isset($param['message']))
            $teacherarr['message'] = $param['message'];
            $res = $this->db->insert('ebh_teachers', $teacherarr);
            //var_dump($uid.'___'.$res.'````');
            
        }return $uid;
    }

    /*
      代理商列表
      @return array
     */

    public function getagentlist() {
        $sql = 'select u.uid,u.username from ebh_users u join ebh_agents a on a.agentid=u.uid order by uid desc';
        return $this->db->query($sql)->list_array();
    }
	
	
	/*
	获取班级的教师列表
	@param int $crid
	*/
	public function getclassteacherlist($crid){
		$sql = 'select ct.*,u.username,u.realname,c.crid 
			from ebh_classteachers ct 
			join ebh_users u on (u.uid = ct.uid) 
			join ebh_classes c on (c.classid = ct.classid) 
			where c.crid='.$crid;
		return $this->db->query($sql)->list_array();
	}
	/*
	获取学校的教师列表
	@param int $crid
	@param array $param
	*/
	public function getroomteacherlist($crid,$param){
		$sql = 'SELECT u.sex,u.face,u.mobile,u.uid,u.username,t.teacherid,t.realname,0 as folderid 
			from ebh_roomteachers rt 
			join ebh_users u on(rt.tid=u.uid) 
			join ebh_teachers t on(t.teacherid=u.uid)';
		
		$wherearr[] = 'rt.crid='.$crid;
		if (!empty($param['q']))
            $wherearr[] = ' (u.username like \'%' . $this->db->escape_str($param['q']) . '%\' or u.realname like \'%' . $this->db->escape_str($param['q']) . '%\')';
		if(!empty($wherearr))
			$sql.= ' where '.implode(' AND ',$wherearr);
		if(!empty($param['order']))
			$sql.= ' order by '.$param['order'];
		if(!empty($param['limit']))
			$sql.= ' limit '.$param['limit'];
		else {
			if (empty($param['page']) || $param['page'] < 1)
				$page = 1;
			else
				$page = $param['page'];
			$pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
			$start = ($page - 1) * $pagesize;
			$sql .= ' limit ' . $start . ',' . $pagesize;
		}
		//echo $sql;
		return $this->db->query($sql)->list_array();
	}
	
	/*
	获取学校的教师数量
	@param int $crid
	@param array $param
	*/
	public function getroomteachercount($crid,$param){
		$sql = 'select count(*) count from ebh_roomteachers rt
			join ebh_users u on(rt.tid=u.uid) 
			join ebh_teachers t on(t.teacherid=u.uid)';
		$wherearr[] = 'rt.crid='.$crid;
		if (!empty($param['q']))
            $wherearr[] = ' (u.username like \'%' . $this->db->escape_str($param['q']) . '%\' or u.realname like \'%' . $this->db->escape_str($param['q']) . '%\')';
		if(!empty($wherearr))
			$sql.= ' where '.implode(' AND ',$wherearr);
		$count = $this->db->query($sql)->row_array();
		return $count['count'];
	}
	
	/*
	添加学校教师
	@param array $param
	*/
	public function addroomteacher($param){
		if(!empty($param['tid']))
			$setarr['tid'] = $param['tid'];
		if(!empty($param['crid']))
			$setarr['crid'] = $param['crid'];
		if(isset($param['status']))
			$setarr['status'] = $param['status'];
		if(!empty($param['cdateline']))
			$setarr['cdateline'] = $param['cdateline'];
		if(!empty($param['role']))
			$setarr['role'] = $param['role'];
		$this->db->update('ebh_classrooms',array(),array('crid'=>$param['crid']),array('teanum'=>'teanum+1'));
		return $this->db->insert('ebh_roomteachers',$setarr);
		
	}
	
	/*
	删除学校教师
	@param array $param   tid,crid
	*/
	public function deleteroomteacher($param){
		$wherearr['tid'] = $param['tid'];
		$wherearr['crid'] = $param['crid'];
		$this->db->begin_trans();
		$this->db->update('ebh_classrooms',array(),array('crid'=>$param['crid']),array('teanum'=>'teanum-1'));
		$this->db->delete('ebh_roomteachers',$wherearr);
		$this->db->delete('ebh_teacherfolders',$wherearr);
		$sql = 'select classid from ebh_classes where crid='.$param['crid'];
		$classes = $this->db->query($sql)->list_array();
		if(!empty($classes)){
			$classids ='';
			foreach($classes as $class){
				if(!empty($classids))
					$classids.=','.$class['classid'];
				else
					$classids = $class['classid'];
			}
			$sql = 'delete from ebh_classteachers where uid = '.$param['tid'].' and classid in ('.$classids.')';
			$this->db->query($sql);
		}
		
		if ($this->db->trans_status() === FALSE) {
            $this->db->rollback_trans();
            return FALSE;
        } else {
            $this->db->commit_trans();
        }
        return TRUE;
		
	}
	
	/*
	学校教师的信息
	@param int $uid
	*/
	public function getroomteacherdetail($uid){
		$sql = 'select uid,username,realname,mobile from ebh_users where uid='.$uid;
		return $this->db->query($sql)->row_array();
	}
	//zwx取教师名字
	public function getteachername($uid){
		$sql = 'select realname from ebh_teachers where teacherid = '.$uid;
		return $this->db->query($sql)->row_array();
	}
	
	/*
	获取课程的教师列表
	@param array $param
	*/
	public function getcourseteacherlist($crid){
		$sql = 'select tf.folderid,tf.tid,u.username,u.realname
			from ebh_teacherfolders tf 
			join ebh_users u on tf.tid = u.uid
			join ebh_folders f on f.folderid = tf.folderid
			where tf.crid = '.$crid;
			// echo $sql;
		return $this->db->query($sql)->list_array();
	}
	
	/*
	获取教师的课件数
	@param array $param
	*/
	public function getteachercwcount($param){
		// if(!empty($param['crid']))
			// $wherearr['crid'] = $param['crid'];
		$wherearr = array();
		if(!empty($param['uids']))
			$wherearr[] = 'uid in('.$param['uids'].')';
		$sql = 'select count(*) count from ebh_coursewares ';
		$sql.= ' where '.implode(' AND ',$wherearr);
		// echo $sql;
		$count = $this->db->query($sql)->row_array();
		return $count['count'];
	}
	
	/*
	学校教师列表及回答数
	@param array $param
	*/
	public function getRoomTeacherListAnswerCount($param){
		$sql = 'select u.username,u.realname,u.uid,
		(select count(*) from ebh_askquestions aq 
		join ebh_askanswers aa on aa.qid=aq.qid where aa.uid=u.uid AND aa.shield=0 AND aq.shield=0) count 
			from ebh_users u
			join ebh_roomteachers rt on(rt.tid=u.uid)';
			
		$wherearr[]= 'crid='.$param['crid'];
		$sql.= ' where '.implode(' AND ',$wherearr);
		// echo $sql;
		return $this->db->query($sql)->list_array();
	}
	
	/*
	学校教师列表及作业数
	@param int $crid
	*/
	public function getRoomTeacherListExamCount($crid){
		$sql = 'SELECT u.uid,u.username,t.teacherid,t.realname,st.count,st.quescount,0 as folderid 
			from ebh_roomteachers rt 
			join ebh_users u on(rt.tid=u.uid) 
			join ebh_teachers t on(t.teacherid=u.uid)
			left join (select se.uid,count(*) as count,sum(se.quescount) as quescount from ebh_schexams se where se.crid ='.$crid.' group by se.uid) st on (st.uid=t.teacherid)
			where rt.crid='.$crid;
		
		return $this->db->query($sql)->list_array();
	}
	/*
	学校教师列表及课件数
	@param int $crid
	*/
	public function getRoomTeacherListCWCount($crid){
		$sql = 'SELECT u.mobile,u.uid,u.username,t.teacherid,t.realname,(select count(*) from ebh_coursewares cw join ebh_roomcourses rc on rc.cwid = cw.cwid where uid = t.teacherid and cw.status = 1 and rc.crid ='.$crid.') as cwcount,0 as folderid 
			from ebh_roomteachers rt 
			join ebh_users u on(rt.tid=u.uid) 
			join ebh_teachers t on(t.teacherid=u.uid)
			where rt.crid='.$crid;
		
		return $this->db->query($sql)->list_array();
	}
/**
     *获取教师的select控件
     *@author zkq
     *@param String $name
     *@param String $uid
     *@param int $selected
     *@return String
     */
    public function getTeacherSelect($name='uid',$id='uid',$selected=0){
		$sql = 'select u.uid,u.username from ebh_teachers t join ebh_users u on t.teacherid = u.uid';
        $teacherarr = $this->db->query($sql)->list_array();
        $s='<select name="'.$name.'" id="'.$id.'">';
        foreach ($teacherarr as $tv) {
            if($selected==$tv['uid']){
                $s.='<option value='.$tv['uid'].' selected=selected>'.$tv['username'].'</option>';
            }else{
                $s.='<option value='.$tv['uid'].'>'.$tv['username'].'</option>';
            }
            
        }
        $s.='</select>';
        return $s;
    }
    /**
     *判断教师是否存在
     *@author zkq
     *@param int $teacherid
     *@return  bool
     */
    public function isExits($teacherid=0){
    	$teacherid = intval($teacherid);
    	if(empty($teacherid)){
    		return false;
    	}
    	$sql = 'select count(*) count from ebh_teachers t where t.teacherid = '.$teacherid.' limit 1 ';
    	$res = $this->db->query($sql)->row_array();
    	if(empty($res['count'])){
    		return false;
    	}else{
    		return true;
    	}
    }
	
	public function addMultipleTeachers($tarr,$crid){
		$teanum = count($tarr);
		$sql='insert into ebh_users (username,password,realname,mobile,dateline,status,groupid,sex) values ';
		foreach($tarr as $user){
			$username = $user['username'];
			$password = md5($user['password']);
			$realname = $user['realname'];
			$mobile = $user['mobile'];
			$dateline = SYSTIME;
			$status = 1;
			$groupid = 5;
			$sex = $user['sex'];
			$sql.= "('$username','$password','$realname','$mobile',$dateline,$status,$groupid,$sex),";
		}
		$sql = rtrim($sql,',');
		$this->db->query($sql);
		$fromuid = $this->db->insert_id();
		
		$sql = 'insert into ebh_teachers (teacherid,realname,mobile) values ';
		for($i=0;$i<$teanum;$i++){
			$teacherid = $fromuid + $i;
			$realname = $tarr[$i]['realname'];
			$mobile = $tarr[$i]['mobile'];
			$sql.= "($teacherid,'$realname','$mobile'),";
		}
		$sql = rtrim($sql,',');
		$this->db->query($sql);
		
		$sql = 'insert into ebh_roomteachers (crid,tid,status,cdateline,role) values ';
		for($i=0;$i<$teanum;$i++){
			$tid = $fromuid + $i;
			$status = 1;
			$cdateline = SYSTIME;
			$role = 1;
			$sql.= "($crid,$tid,$status,$cdateline,$role),";
		}
		$sql = rtrim($sql,',');
		$this->db->query($sql);
		
		$this->db->update('ebh_classrooms',array(),array('crid'=>$crid),array('teanum'=>'teanum+'.$teanum));
		return $fromuid;
		
	}
	/**
	 *返回教室老师select控件
	 *@param int $crid
	 *@param String $attr 用户自定义节点
	 *@param int $seleted 默认选中的老师uid
	 *@param int $hidden 不需要显示的老师的uid
	 */
	public function getSchoolTeacherSelect($crid,$attr='',$selected=-1,$hidden=-1,$hasqxz=true){
		$sql = 'select u.uid,u.username,u.realname from ebh_roomteachers rt 
				left join ebh_teachers t on rt.tid = t.teacherid
				left join ebh_users u on t.teacherid = u.uid 
				WHERE rt.crid = '.intval($crid);
		$teachers = $this->db->query($sql)->list_array();
		$s = '<select '.$attr.'>';
		if($hasqxz===true){
			$s.='<option value="">请选择</option>';
		}
		foreach ($teachers as $teacher) {
			if($teacher['uid']==$hidden){
				continue;
			}
			$realname = empty($teacher['realname'])?'':'('.$teacher['realname'].')';
			if($teacher['uid']==$selected){
				$s.='<option selected=selected value="'.$teacher['uid'].'">'.$teacher['username'].$realname.'</option>';
			}else{
				$s.='<option value="'.$teacher['uid'].'">'.$teacher['username'].$realname.'</option>';
			}
			
		}
		$s.='</select>';
		return $s;
	}

	 /*
      教师详情
      @param int $uid
      @return array
     */

    public function getTeacherInfo($uid) {
        $sql = 'select u.username,u.realname,u.nickname,u.face,u.sex,t.phone,u.mobile,t.profile from ebh_users u left join ebh_teachers t on u.uid = t.teacherid where teacherid = ' . $uid;
        return $this->db->query($sql)->row_array();
    }

    /**
     * 返回教师课程列表
     * @param $tid 教师ID
     * @param $roominfo 所在网校信息
     * @param int $limit 查询限制
     * @param bool $setKey 是否以课程ID为键
     * @return array
     */
    public function getCoursesForTeacher($tid, $roominfo, $limit = 0, $setKey = false) {
        if (empty($roominfo['crid']) || !isset($roominfo['isschool']) || !isset($roominfo['property'])) {
            return array();
        }
        $tid = intval($tid);
        $crid = intval($roominfo['crid']);
        $isschool = intval($roominfo['isschool']);
        $property = intval($roominfo['property']);
        $offset = 0;
        $top = 0;
        if (!empty($limit)) {
            if (is_array($limit)) {
                $page = !empty($limit['page']) ? intval($limit['page']) : 0;
                $page = max(1, $page);
                $top = !empty($limit['pagesize']) ? intval($limit['pagesize']) : 20;
                $top = max(1, $top);
                $offset = ($page - 1) * $top;
            } else {
                $top = intval($limit);
            }
        }
        if ($isschool == 7 && $property == 3) {
            $scope = $this->db->query(
                'SELECT MAX(`rgt`) AS `rgt`,MIN(`lft`) AS `lft` FROM `ebh_classteachers` `a` 
                LEFT JOIN `ebh_classes` `b` ON `b`.`classid`=`a`.`classid` WHERE `b`.`crid`='.$crid.' AND `a`.`uid`='.$tid)
                ->row_array();
            if (empty($scope)) {
                return array();
            }
            $classids = $this->db->query(
                'SELECT `classid` FROM `ebh_classes` WHERE `crid`='.$crid.' AND `lft`>='.$scope['lft'].' AND `rgt`<='.$scope['rgt'])
                ->list_field('classid');
            $sql = 'SELECT `folderid` FROM `ebh_classcourses` WHERE `classid` IN('.implode(',', $classids).')';
        } else {
            $sql = 'SELECT `folderid` FROM `ebh_teacherfolders` WHERE `tid`='.$tid.' AND `crid`='.$crid;
        }
        $folderids = $this->db->query($sql)->list_field('folderid');
        if (empty($folderids)) {
            return array();
        }
        if ($isschool == 7) {
            //分层网校
            $wheres = array(
                '`a`.`crid`='.$crid,
                '`a`.`defind_course`=1',
                '`a`.`status`=0',
                '`a`.`folderid` IN('.implode(',', $folderids).')',
                '`b`.`del`=0',
                '`b`.`folderlevel`=2',
                '`b`.`crid`='.$crid,
                '`c`.`status`=1',
                'IFNULL(`d`.`ishide`,0)=0'
            );
            $sql = 'SELECT `a`.`itemid`,`b`.`folderid`,`b`.`foldername`,`b`.`img`,`b`.`speaker`,IFNULL(`e`.`grank`,0) AS `rank` FROM `ebh_pay_items` `a` 
                    LEFT JOIN `ebh_folders` `b` ON `b`.`folderid`=`a`.`folderid` 
                    LEFT JOIN `ebh_pay_packages` `c` ON `c`.`pid`=`a`.`pid` 
                    LEFT JOIN `ebh_pay_sorts` `d` ON `d`.`sid`=`a`.`sid`
                    LEFT JOIN `ebh_courseranks` `e` ON `e`.`folderid`=`a`.`folderid`
                    WHERE '.implode(' AND ', $wheres).' GROUP BY `b`.`folderid` ORDER BY `rank` ASC,`a`.`itemid` DESC';
        } else {
            //普通网校
            $wheres = array(
                '`a`.`folderid` IN('.implode(',', $folderids).')',
                '`a`.`folderlevel=2',
                '`a`.`crid`='.$crid,
                '`a`.`del`=0'
            );
            $sql = 'SELECT `a`.`folderid`,`a`.`foldername`,`a`.`img`,`a`.`speaker`,0 AS `itemid`,IFNULL(`b`.`grank`,0) AS `rank` 
                    FROM `ebh_folders` `a` LEFT JOIN `ebh_courseranks` `b` ON `a`.`folderid`=`b`.`folderid`
                    WHERE '.implode(' AND ', $wheres).' GROUP BY `a`.`folderid` ORDER BY `rank` ASC,`folderid` DESC';
        }
        if ($top > 0) {
            $sql .= ' LIMIT '.$offset.','.$top;
        }
        return $this->db->query($sql)->list_array($setKey ? 'folderid' : '');
    }
}