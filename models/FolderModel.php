<?php
/**
 * 课程相关model类 FolderModel
 */
class FolderModel extends CModel{
   
    /**
     * 根据课程编号获取课程详情信息
     * @param int $folderid 课程编号
     * @return array 课程信息数组 
     */
    public function getfolderbyid($folderid) {
    	if(empty($folderid))return false;
        $sql = 'select f.folderid,f.foldername,f.displayorder,f.img,f.coursewarenum,f.summary,f.grade,f.district,f.crid,f.upid,f.folderlevel,f.folderpath,f.fprice,f.speaker,f.detail,f.viewnum,f.introduce from ebh_folders f where f.folderid='.$folderid;
		return $this->db->query($sql)->row_array();
    }
	//教室首页大纲导航
	public function byfreecourse(){
//		select c.cwid,c.title,c.summary,c.cwsource,rc.folderid from '.tname('coursewares').' c '
//    					.'join '.tname('roomcourses').' rc on (c.cwid = rc.cwid) '
//    					.'WHERE rc.crid = '.$paramarr ['crid'].' AND  c.status=1 AND rc.isfree = 1 '
//    					.'order by rc.folderid,rc.cdisplayorder';
//		$sql = 'select f.folderid,f.foldername,f.img from ebh_folders';
	}

	/**
	*获取课程列表
	*在cq,hh,fssq大纲导航中可调用到,stores答疑专区
	*/
	public function getfolderlist($param){
		$sql = 'SELECT f.folderid as fid,f.foldername as name,f.img as face,f.coursewarenum as num,f.fprice,f.grade,f.district,f.summary,f.viewnum,f.playmode,f.uid FROM ebh_folders f ';
        $wherearr = array();
 
		if(! empty ( $param ['folderid'] )){
			$wherearr [] = 'f.folderid IN (' . $param ['folderid'] . ')';
		}
		if(! empty ( $param ['crid'] )){
			$wherearr [] = ' f.crid = ' . $param ['crid'];
		}
		if(! empty ( $param ['uid'] )){
			$wherearr [] = ' f.uid = ' . $param ['uid'];
		}
		if(! empty ( $param ['status'] )){
			$wherearr [] = ' f.status = ' . $param ['status'];
		}
		if(! empty ( $param ['folderlevel'] )){
			$wherearr [] = ' f.folderlevel <> ' . $param ['folderlevel'];
		}else{
			$wherearr[] = ' f.folderlevel = 2';
		}
		if(isset ( $param ['upid'] )){
			$wherearr [] = ' f.upid <> ' . $param ['upid'];
		}
		if(! empty ( $param ['coursewarenum '] )){	//过滤课程下课件数为0的课程
			$wherearr [] = ' f.coursewarenum  > 0 ';
		}
		if(isset($param['filternum'])){
			$wherearr [] = ' f.coursewarenum > 0';
		}
		if(isset($param['isschoolfree'])){
			$wherearr [] = ' f.isschoolfree='.$param['isschoolfree'];
		}
		if(isset($param['power'])){
			$wherearr [] = ' f.power='.$param['power'];
		}
		$wherearr [] = ' f.del=0';
        $sql .= ' WHERE '.implode(' AND ', $wherearr);
        if(!empty($param['order'])) {
            $sql .= ' ORDER BY '.$param['order'];
        } else {
            $sql .= ' ORDER BY f.displayorder';
        }
        if(!empty($param['limit'])) {
            $sql .= ' limit '.$param['limit'];
        } else {
			if (empty($param['page']) || $param['page'] < 1)
				$page = 1;
			else
				$page = $param['page'];
			$pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
			$start = ($page - 1) * $pagesize;
			$sql .= ' limit ' . $start . ',' . $pagesize;
        }
		$folderlist = $this->db->query($sql)->list_array();
		$mylist = array();
		foreach($folderlist as $myfolder) {
			if(empty($myfolder['face']))
				$myfolder['face'] = 'http://static.ebanhui.com/ebh/images/nopic.jpg';
			$mylist[] = $myfolder;
		}
        return $mylist;
	}
	//大纲导航数量
	public function getcount($param){
		$count = 0;
		$sql = 'select count(*) count from ebh_folders f';
		$wherearr = array();
		if(! empty ( $param ['folderid'] )){
			$wherearr [] = 'f.folderid IN (' . $param ['folderid'] . ')';
		}
		if(! empty ( $param ['crid'] )){
			$wherearr [] = ' f.crid = ' . $param ['crid'];
		}
		if(! empty ( $param ['status'] )){
			$wherearr [] = ' f.status = ' . $param ['status'];
		}
		if(! empty ( $param ['folderlevel'] )){
			$wherearr [] = ' f.folderlevel <> ' . $param ['folderlevel'];
		}
		if(isset ( $param ['upid'] )){
			$wherearr [] = ' f.upid <> ' . $param ['upid'];
		}
		if(! empty ( $param ['coursewarenum '] )){	//过滤课程下课件数为0的课程
			$wherearr [] = ' f.coursewarenum  > 0 ';
		}
		if(isset($param['filternum'])){
			$wherearr [] = ' f.coursewarenum > 0';
		}
		if(isset($param['isschoolfree'])){
			$wherearr [] = ' f.isschoolfree='.$param['isschoolfree'];
		}
		$sql .= ' WHERE '.implode(' AND ', $wherearr);
		$row = $this->db->query($sql)->row_array();
		if(!empty($row))
			$count = $row['count'];
        return $count;
	}

	/**
	*获取班级对应的教师课程
	*/
	public function getClassFolder($param) {
		$sql = 'select ct.uid from ebh_classteachers ct where ct.classid='.$param['classid'];
		$tidlist = $this->db->query($sql)->list_array();
		$tids = '';
		if(!empty($tidlist)) {
			foreach($tidlist as $tid) {
				if(empty($tids))
					$tids = $tid['uid'];
				else
					$tids .= ','.$tid['uid'];
			}
		}
		if(!empty($tids)) {
			$fsql = 'select f.folderid,f.folderid as fid,f.foldername as name,f.img as face,f.coursewarenum as num,f.fprice,f.grade,f.district from ebh_folders f '.
					'where f.folderid in(select tf.folderid from ebh_teacherfolders tf  '.
					'where tf.tid in ('.$tids.')) and f.crid='.$param['crid'];
			if(!empty($param['limit']))
				$fsql .= ' limit '.$param['limit'];
			else {
				if (empty($param['page']) || $param['page'] < 1)
					$page = 1;
				else
					$page = $param['page'];
				$pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
				$start = ($page - 1) * $pagesize;
				$fsql .= ' limit ' . $start . ',' . $pagesize;
			}
			$folderlist = $this->db->query($fsql)->list_array();
			$mylist = array();
			foreach($folderlist as $myfolder) {
				if(empty($myfolder['face']))
					$myfolder['face'] = 'http://static.ebanhui.com/ebh/images/nopic.jpg';
				$mylist[] = $myfolder;
			}
			return $mylist;
		}
		return FALSE;
	}
	/**
	*获取班级对应的教师课程记录数
	*/
	public function getClassFolderCount($param) {
		$count = 0;
		$sql = 'select ct.uid from ebh_classteachers ct where ct.classid='.$param['classid'];
		$tidlist = $this->db->query($sql)->list_array();
		$tids = '';
		if(!empty($tidlist)) {
			foreach($tidlist as $tid) {
				if(empty($tids))
					$tids = $tid['uid'];
				else
					$tids .= ','.$tid['uid'];
			}
		}
		if(!empty($tids)) {
			$fsql = 'select count(*) count from ebh_folders f '.
					'where f.folderid in(select tf.folderid from ebh_teacherfolders tf  '.
					'where tf.tid in ('.$tids.')) and f.crid='.$param['crid'];
			$countrow = $this->db->query($fsql)->row_array();
			if(!empty($countrow))
				$count = $countrow['count'];
		}
		return $count;
	}
	/**
	*获取学校教师对应的课程列表
	*/
	public function getTeacherFolderList($param) {
		if(empty($param['uid']) && empty($param['crid']))
			return FALSE;
		$sql = 'SELECT u.uid,u.username,u.realname FROM ebh_roomteachers rt '.
				'JOIN ebh_users u on (u.uid = rt.tid)';
		$wherearr = array();
		if(!empty($param['crid']))
			$wherearr[] = 'rt.crid='.$param['crid'];
		if(!empty($param['uid']))
			$wherearr[] = 'rt.tid='.$param['uid'];
		$sql .= ' WHERE '.implode(' AND ',$wherearr);
		$list = $this->db->query($sql)->list_array();
		$ids = '';
		$teacherlist = array();
		foreach($list as $teacher) {
			$teacherlist[$teacher['uid']] = $teacher;
			$teacherlist[$teacher['uid']]['folder'] = array();
			if(empty($ids))
				$ids = $teacher['uid'];
			else
				$ids .= ','.$teacher['uid'];
		}
		if(!empty($ids)) {
			$fsql = 'SELECT f.folderid,f.foldername,tf.tid from ebh_folders f '.
					'join ebh_teacherfolders tf on (tf.folderid=f.folderid) '.
					'WHERE tf.crid='.$param['crid'].' and tf.tid in ('.$ids.')';
			$folders = $this->db->query($fsql)->list_array();
			foreach($folders as $folder) {
				$teacherlist[$folder['tid']]['folder'][] = $folder;
			}
		}
		return $teacherlist;
	}
	/*
	教师的课程数
	*/
	public function getTeacherFolderCount($param){
		$sql = 'select count(*) count from ebh_folders f
			join ebh_teacherfolders tf on f.folderid = tf.folderid';
			$wherearr[]= 'f.crid='.$param['crid'];
			$wherearr[]= 'tf.tid='.$param['uid'];
		$sql.= ' where '.implode(' AND ',$wherearr);
		$count = $this->db->query($sql)->row_array();
		return $count['count'];
	}
	/*
	教师课程列表
	*/
	public function getTeacherFolderList1($param){
		$wherearr = array();
		$sql = 'SELECT f.uid,f.foldername,f.img,f.summary,f.folderpath,f.folderid,f.coursewarenum 
		FROM ebh_folders f 
		join ebh_teacherfolders tf on f.folderid = tf.folderid';
		$wherearr[]= 'f.crid='.$param['crid'];
		$wherearr[]= 'tf.tid='.$param['uid'];
		$sql.= ' where '.implode(' AND ',$wherearr);
		return $this->db->query($sql)->list_array();
	}

	/**
	 *获取学校的收费课程
	 */
	public function getNotFreeFolderList($crid = 0){
		$sql = 'SELECT f.folderid as fid,f.fprice FROM ebh_folders f where f.fprice >0 AND f.crid = '.$crid;
		return $this->db->query($sql)->list_array();
	}
	/*
	设置人气数
	*/
	public function setviewnum($folderid, $num = 1) {
		$where = 'folderid=' . $folderid;
        $setarr = array('viewnum' => $num);
        $this->db->update('ebh_folders', array(), $where, $setarr);
	}

	/**
	 *获取指定课程的教师列表
	 */
	public function getTeacherListOfFolder($folderid = 0,$crid = 0){
		$sql = "SELECT f.tid from ebh_teacherfolders f where f.crid = ".$crid.' AND f.folderid = '.$folderid.' AND f.tid > 0';
		return $this->db->query($sql)->list_array();
	}
	
	/*
	isschool!=7 ,只按年级获取课程
	*/
	public function getClassFolderWithoutTeacher($param){
		$sql = 'select f.folderid,f.foldername,f.coursewarenum,f.img from ebh_folders f '.
					'where f.grade = '.$param['grade'].' and f.crid='.$param['crid'];
		return $this->db->query($sql)->list_array();
	}

	//根据课程folderid数组获取课程信息
	public function getFolderListByFolderids($folderids = array()){
		if(empty($folderids)){
			return array();
		}
		$sql = 'select f.folderid as fid,f.foldername as name,f.img as face,f.coursewarenum as num,f.fprice,f.grade,f.district from ebh_folders f where f.folderid in ('.implode(',', $folderids).') and power=0';
		$folderlist = $this->db->query($sql)->list_array();
		foreach($folderlist as &$folder) {
				if(empty($folder['face']))
					$folder['face'] = 'http://static.ebanhui.com/ebh/images/nopic.jpg';
		}
		return $folderlist;
	}

	//获取学生课程相关信息以及课程学习情况
	//{"folderid":"3424","crid":"10440","foldername":"\u8bed\u6587\u9ad8\u4e00","speaker":"\u4e3b\u8bb2\u8001\u5e08","face":"http:\/\/static.ebanhui.com\/ebh\/images\/nopic.jpg","coursewarenum":14,"dateline":"2015-05-06 10:07","classmatenum":3,"progress":0.33333333333333}
    public function getUserRelativeFolderInfo($folderid = 0 ,$uid = 0){
    	$sql = 'select f.folderid,f.crid,f.foldername,f.speaker,f.introduce,f.img as face,f.summary from ebh_folders f where f.folderid='.$folderid.' limit 1';
    	$folder = $this->db->query($sql)->row_array();
    	if(empty($folder)){
    		return array();
    	}
    	$DEF_IMG = Ebh::app()->getConfig()->load('defimg');
    	$folder['face'] = !empty($folder['face'])?$folder['face']:$DEF_IMG['folder_face'];

        //1.获取该课程第一个有效课件的发布时间,精确的课件数,该课程下课件列表
        $sql = 'select cw.cwid,cw.ism3u8,dateline from ebh_coursewares cw join ebh_roomcourses rc on cw.cwid = rc.cwid where cw.status=1 AND rc.folderid = '.$folderid.' order by cw.cwid asc';
        $courselist = $this->db->query($sql)->list_array();
        $folder['coursewarenum'] = count($courselist);
        if(!empty($courselist)){
        	$first_course = $courselist[0];
        	$folder['dateline'] = date('Y-m-d',$first_course['dateline']);
        }else{
        	$folder['dateline'] = '暂未开始';
        }

        //3.获取该同学在该学校中所在的班级
        $sql = 'select c.classid from ebh_classstudents cs join ebh_classes c on cs.classid = c.classid where cs.uid = '.$uid.' AND c.crid = '.$folder['crid'].' limit 1';
        $res = $this->db->query($sql)->row_array();
        if(!empty($res)){
        	$classid = $res['classid'];
        }else{
        	$classid = 0;
        }

        //4.判断课程是否在服务包里,并且是收费的
        $sql = 'select count(1) count from ebh_pay_items pi where pi.iprice > 0 AND  pi.folderid = '.$folderid.' AND pi.crid = '.$folder['crid']; 
        $res = $this->db->query($sql)->row_array();
        if($res['count'] > 0){//收费课程
        	//获取该课程的同学数(包含自己)
        	$sql = 'select count(1) count from ebh_userpermisions up join ebh_classstudents cs on up.uid = cs.uid where cs.classid = '.$classid.' AND up.folderid = '.$folderid.' AND up.enddate > '.SYSTIME;
        }else{//不收费课程
        	//获取该课程的同学数(包含自己)
        	$sql = 'select count(1) count from ebh_classstudents where classid = '.$classid;
        }
        $res = $this->db->query($sql)->row_array();
        $folder['classmatenum'] = max(($res['count']-1),0);
        //获取该课程下视频课件的cwid
        $cwids = array();
        if(!empty($courselist)){
	        foreach ($courselist as $course) {
	        	if(!empty($course['ism3u8'])){
		        	array_push($cwids,$course['cwid']);
		        }
		    }
        }

        //获取视频课件的播放总记录
        $percent = 0;
        //存在视频课件
        if(!empty($cwids)){
        	$sql = 'select cwid,ctime,ltime from ebh_playlogs pl where pl.totalflag = 1 AND pl.uid = '.$uid.' AND pl.cwid in ('.implode(',',$cwids).')';
        	$playlist = $this->db->query($sql)->list_array();
        	$newplaylist = array();
        	if(!empty($playlist)){
        		foreach ($playlist as $playlog) {
        			$key = 'cw_'.$playlog['cwid'];
        			$newplaylist[$key] = $playlog;
        		}
        	}
        	$playlist = $newplaylist;
        	$percent_total = 0;
        	foreach ($cwids as $cwid) {
        		$key = 'cw_'.$cwid;
        		if(array_key_exists($key,$playlist)){
        			if($playlist[$key]['ctime'] == 0){
        				$ctime = 1;
        			}else{
        				$ctime = $playlist[$key]['ctime'];
        			}
        			$p = min($playlist[$key]['ltime']/$ctime,1);
        			if($p > 0.9){
        				$p = 1;
        			}
        		}else{
        			$p = 0;
        		}
        	}
        	$percent_total += intval($p);
        	$percent = $percent_total / count($cwids);
        }
        $folder['progress'] = $percent;
        return $folder;
    }
    
    //获取学生课程相关的学习进度、分数及获得的学分
    public function getUserExtFolderInfo($folders,$uid){
    	if(empty($folders)){
    		return array();
    	}
    	foreach ($folders as $fkey=>$folder){
    		$tmpfidarr[] = $folder['fid'];
    	}
    	$folderids = implode(',',$tmpfidarr);
    	$param['folderid'] = $folderids;
    	//课程集合下的视频课件
    	$param['limit'] = 10000;
    	$coursewarelist = $this->getCWByFolderid($param);
    	$countlist = $this->getFolderProgressCountByFolderid($param);
    	foreach($countlist as $f){
    		$foldercwcount[$f['folderid']] = $f['count'];
    	}
    	
    	$cwids = '';
    	$cwidfolderidlist =array();
    	foreach($coursewarelist as $cw){
    		$cwids .= $cw['cwid'].',';
    		$cwidfolderidlist[$cw['cwid']] = $cw['folderid'];
    	}
    	$cwids = rtrim($cwids,',');
    	$param['cwid'] = $cwids;
    	$param['uid'] = $uid;
    	
    	//根据cwid获取进度,添加到对应 课程进度 数组中
    	$folderprogress = $foldersumtime = array();
    	if(!empty($param['cwid'])){
	    	$progresslist = $this->getFolderProgressByCwid($param);
	    	foreach($progresslist as $p){
	    		$folderid = $cwidfolderidlist[$p['cwid']];
	    		if($p['percent']*100>=90){
	    			$folderprogress[$folderid][] = 100;
	    			if(empty($foldercredit[$folderid]['study'])){//听课完成的数量
	    				$foldercredit[$folderid]['study'] = 1;
	    			}else{
	    				$foldercredit[$folderid]['study'] += 1;
	    			}
	    		}else{
	    			$folderprogress[$folderid][] = $p['percent']*100;
	    		}
	    	}
	    	
	    	//根据cwid获取听课时长总合,添加到对应 课程时长 数组中
	    	$sumtimelist = $this->getCourseSumTime($param);
	    	foreach($sumtimelist as $s){
	    		$folderid = $cwidfolderidlist[$s['cwid']];
	    		if(empty($foldersumtime[$folderid])){
	    			$foldersumtime[$folderid] = $s['sumtime'];
	    		}else{
	    			$foldersumtime[$folderid] += $s['sumtime'];
	    		}
	    	}
    	}
    	//作业完成情况
    	$examcreditlist = $this->getUserFolderExamCredit(array('uid'=>$uid,'folderid'=>$folderids));
    	foreach($examcreditlist as $examcredit){
    		$folderid = $examcredit['folderid'];
    		if(empty($foldercredit[$folderid]['exam']))
    			$foldercredit[$folderid]['exam'] = $examcredit['examcredit'];
    		else
    			$foldercredit[$folderid]['exam'] += $examcredit['examcredit'];
    	}
    	//作业总数
    	$countlist = $this->getFolderExamCount(array('folderid'=>$folderids));
    	foreach($countlist as $f){
    		$folderexamcount[$f['folderid']] = $f['count'];
    	}
    	//获取学分规则credit, creditrule, creditmode, credittime
    	$folderdetailarr = $this->getfoldersdetail($folderids);
    	foreach ($folderdetailarr as $ikey=>$item){
    		$foldercreditarr[$item['folderid']]['credit'] = $item['credit'];
    		$foldercreditarr[$item['folderid']]['creditrule'] = $item['creditrule'];
    		$foldercreditarr[$item['folderid']]['creditmode'] = $item['creditmode'];
    		$foldercreditarr[$item['folderid']]['credittime'] = $item['credittime'];
    	}
    	//学习进度条、课程学分、获得的学分注入到$folders数组
    	foreach($folders as $k=>$folder){
    		$folderid = $folder['fid'];
    		$folders[$k]['credit'] = !empty($foldercreditarr[$folderid]) ? $foldercreditarr[$folderid]['credit'] : 0;
    		if(!empty($folderprogress[$folderid])){
    			$folders[$k]['percent'] = floor(array_sum($folderprogress[$folderid])/$foldercwcount[$folderid]);
    			if(empty($foldercwcount[$folderid])){
    				$folders[$k]['studyfinishpercent'] = 0;
    			}else{
    				$fscredit = empty($foldercredit[$folderid]['study'])?0:$foldercredit[$folderid]['study'];
    				$folders[$k]['studyfinishpercent'] = $fscredit/$foldercwcount[$folderid];
    			}
    			if(empty($folderexamcount[$folderid])){
    				$folders[$k]['examscorepercent'] = 0;
    			}else{
    				$fecredit = empty($foldercredit[$folderid]['exam'])?0:$foldercredit[$folderid]['exam'];
    				$folders[$k]['examscorepercent'] = $fecredit/$folderexamcount[$folderid];
    			}
    			
    			$credit = $foldercreditarr[$folderid]['credit'];
    			$creditrule = $foldercreditarr[$folderid]['creditrule'];
    			if(empty($creditrule)){
    				$creditrule[0] = 100;
    				$creditrule[1] = 0;
    			}else{
    				$creditrule = explode(':',$creditrule);
    			}
    			$folders[$k]['creditget'] = round($credit*($creditrule[0]*$folders[$k]['studyfinishpercent']+$creditrule[1]*$folders[$k]['examscorepercent'])/100,2);		
    		}
    		else{
    			$folders[$k]['percent'] = 0;
    			$folders[$k]['creditget'] = 0;
    		}
    	}
    	return $folders;
   	}
   	
   	/*
   	 依据folderid获取课件进度
   	*/
   	private function getFolderProgressCountByFolderid($param){
   		$sql = 'select count(*) count,folderid from ebh_roomcourses rc join ebh_coursewares cw on(rc.cwid = cw.cwid)';
   		$wherearr[] = 'rc.folderid in('.$param['folderid'].')';
   		$wherearr[] = 'cw.status=1';
   		$wherearr[] = 'cw.ism3u8=1';
   		// $wherearr[] = '(right(cw.cwurl,4)=\'.flv\' or right(cw.cwurl,5)=\'.ebhp\')';
   		$sql.= ' where '.implode(' AND ',$wherearr);
   		$sql.= ' group by rc.folderid';
   		// echo $sql.'__________';
   		$countlist = $this->db->query($sql)->list_array();
   		return $countlist;
   	}
   	
   	/*
   	 依据cwid获取课件进度
   	*/
   	private function getFolderProgressByCwid($param){
   		$sql = 'select cwid,ltime/ctime percent from ebh_playlogs';
   		$wherearr[] = ' cwid in('.$param['cwid'].')';
   		if(!empty($param['uid']))
   			$wherearr[] = 'uid='.$param['uid'];
   		$wherearr[] = ' totalflag=1';
   		$sql.= ' where '.implode(' AND ',$wherearr);
   		// echo $sql.'___________';
   		return $this->db->query($sql)->list_array();
   	}
   	
   	/*
   	 学生课程学习总时间
   	*/
   	private function getCourseSumTime($param){
   		$sql = 'select cwid,sum(ltime) sumtime from ebh_playlogs ';
   		$wherearr[] = ' cwid in('.$param['cwid'].')';
   		if(!empty($param['uid']))
   			$wherearr[] = 'uid='.$param['uid'];
   		$wherearr[] = ' totalflag=0';
   		$sql.= ' where '.implode(' AND ',$wherearr);
   		$sql.= ' group by cwid';
   		// echo $sql.'___________';
   		return $this->db->query($sql)->list_array();
   	}
   	
   	/*
   	 学生作业答题情况
   	*/
   	private function getUserFolderExamCredit($param){
   		$sql = 'select f.folderid,sum(a.totalscore/e.score) examcredit
		from ebh_schexamanswers a
		join ebh_schexams e on a.eid=e.eid
		join ebh_folders f on f.folderid = e.folderid
		';
   		$wherearr[] = 'a.uid='.$param['uid'];
   		$wherearr[] = 'f.folderid in ('.$param['folderid'].')';
   		$sql .= ' where '.implode(' AND ',$wherearr);
   		$sql .= ' group by f.folderid';
   		// echo $sql;
   		return $this->db->query($sql)->list_array();
   	}
   	
   	/*
   	 课程的作业总数
   	*/
   	private function getFolderExamCount($param){
   		$sql = 'select count(*) count,folderid from ebh_schexams e';
   		$wherearr[] = ' e.folderid in ('.$param['folderid'].')';
   		$sql .= ' where '.implode(' AND ',$wherearr);
   		$sql.= ' group by e.folderid';
   		return $this->db->query($sql)->list_array();
   	}
   	
   	/*
   	 获取课件列表
   	*/
   	private function getCWByFolderid($param){
   		$sql = 'select cw.cwid,rc.folderid,cw.cwurl,cw.title,rc.sid from ebh_coursewares cw join ebh_roomcourses rc on cw.cwid=rc.cwid';
   		$wherearr = array();
   		$wherearr[] = 'rc.folderid in('.$param['folderid'].')';
   		$wherearr[] = 'cw.status=1';
   		$wherearr[] = 'cw.ism3u8=1';
   		// $wherearr[] = '(right(cw.cwurl,4)=\'.flv\' or right(cw.cwurl,5)=\'.ebhp\')';
   		$sql.= ' where '.implode(' AND ',$wherearr);
   		if(!empty($param['limit'])) {
   			$sql .= ' limit '. $param['limit'];
   		}
   		else {
   			if (empty($param['page']) || $param['page'] < 1)
   				$page = 1;
   			else
   				$page = $param['page'];
   			$pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
   			$start = ($page - 1) * $pagesize;
   			$sql .= ' limit ' . $start . ',' . $pagesize;
   		}
   		// echo $sql.'____________';
   		return $this->db->query($sql)->list_array();
   	}
   	
   	/*
   	 获取课程详情
   	 */
   	private function getfoldersdetail($folderids){
   		if(empty($folderids)){
   			return array();
   		}
   		$sql = 'select folderid, credit, creditrule, creditmode, credittime from ebh_folders where folderid in ('.$folderids.')';
   		return $this->db->query($sql)->list_array();
   	}

   	/**
   	 * 根据课程编号获取课程授课教师列表
   	 * @param  array $folderids 课程编号数组
   	 * @return mix            课程授课教师列表
   	 */
   	public function getFolderTeacherList($folderids){
   		if(empty($folderids) || !is_array($folderids)){
   			return FALSE;
   		}
   		$sql = 'SELECT tf.folderid,tf.tid,u.username,u.realname
			from ebh_teacherfolders tf 
			join ebh_users u on tf.tid = u.uid
			WHERE tf.folderid in (' . implode(',', $folderids) . ')';
		return $this->db->query($sql)->list_array();
   	}

    /**
     * 网校课程预览列表
     * @param $crid 网校ID
     * @param $isschool 网校类型
     * @param int $pid 服务包ID
     * @param int $limit 查询限制
     * @param bool $setKey 是否以课程ID为键
     * @return mixed
     */
   	public function getCourseList($crid, $isschool, $pid = 0, $limit = 0, $setKey = false) {
   	    $crid = intval($crid);
   	    $isschool = intval($isschool);
        $offset = 0;
        $top = 0;
        $pid = intval($pid);
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
        if ($isschool == 7) {
            $wheres = array(
                '`a`.`crid`='.$crid,
              //  '`a`.`defind_course`=1',//暂时隐藏这个选项
                '`a`.`status`=0',
                '`b`.`del`=0',
                '`c`.`status`=1',
                'IFNULL(`d`.`ishide`,0)=0'
            );
            if ($pid > 0) {
                $wheres[] = '`a`.`pid`='.$pid;
            }
            $sql = 'SELECT `a`.`itemid`,`a`.`pid`,`b`.`folderid`,`b`.`foldername`,`b`.`img`,`b`.`speaker`,`c`.`pname`,IFNULL(`e`.`prank`,0) AS `rank`
                    FROM `ebh_pay_items` `a` JOIN `ebh_folders` `b` ON `b`.`folderid`=`a`.`folderid` 
                    JOIN `ebh_pay_packages` `c` ON `c`.`pid`=`a`.`pid`
                    LEFT JOIN `ebh_pay_sorts` `d` ON `d`.`sid`=`a`.`sid` 
                    LEFT JOIN `ebh_courseranks` `e` ON `e`.`folderid`=`a`.`folderid`
                    WHERE '.implode(' AND ', $wheres).' GROUP BY `b`.`folderid` ORDER BY `c`.`displayorder`,`c`.`pid` DESC,`rank`,`a`.`itemid` DESC';
        } else {
            $sql = 'SELECT `folderid`,`foldername`,`img`,`speaker` FROM `ebh_folders` WHERE `crid`='.$crid.' AND `folderlevel`=2 AND `del`=0 AND `power`=0 ORDER BY `displayorder` ASC,`folderid` DESC';
        }
        if ($top > 0) {
            $sql .= ' LIMIT '.$offset.','.$top;
        }
        
        //log_message($sql);
        return $this->db->query($sql)->list_array($setKey ? 'folderid' : '');
    }

    /**
     * 校验老师课程权限
     * @param $folderid
     * @param $uid
     * @param $crid
     * @return bool
     */
    public function checkTeacherPermission($folderid,$uid,$crid){
        $sql = 'select count(crid) as count from ebh_teacherfolders where folderid='.$folderid.' and tid='.$uid.' and crid='.$crid;
        $rs  = $this->db->query($sql)->row_array();
        if($rs && $rs['count'] > 0){
            return true;
        }else{
            return false;
        }
    }
}
