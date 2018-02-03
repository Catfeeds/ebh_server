<?php
/**
 * 班级ClassesModel类
 */
class ClassesModel extends CModel{
	/**
	*获取用户所在的班级信息
	*@param int $crid教室编号
	*@param int $uid 用户编号
	*/
	public function getClassByUid($crid,$uid,$isreturn = TRUE) {
        if(empty($crid) || empty($uid)){
            return FALSE;
        }
		$sql = "SELECT cs.classid,c.classname,c.grade,c.district from  ebh_classstudents cs ".
				"JOIN ebh_classes c on (c.classid = cs.classid) ".
				"WHERE c.crid=$crid and cs.uid = $uid";
		$classinfo = $this->db->query($sql)->row_array();
		if(empty($classinfo) && $isreturn){
			//没有班级则返回默认班级
			$appsetting = Ebh::app()->getConfig()->load('appsetting');
			if(!empty($appsetting) && !empty($appsetting['democlassid'])) {
				$classid =$appsetting['democlassid'];
				$classinfo = $this->getClassInfo($classid);
			}
		}
		return $classinfo;
	}

	/**
	 * 获取学生用户所有的班级id
	 */
	public function getClassidsByUid($crid,$uid){
		$sql = "SELECT cs.classid,c.classname,c.grade,c.district,c.headteacherid from  ebh_classstudents cs ".
				"JOIN ebh_classes c on (c.classid = cs.classid) ".
				"WHERE c.crid=$crid and cs.uid = $uid";
		$classes = $this->db->query($sql)->list_array();
		$classids = array();
		if(!empty($classes)){
			foreach ($classes as $cla){
				$classids[] = $cla['classid'];
			}
		}
		return $classids;
	}
	
	/**
	 *获取一个班级的老师
	 *
	 */
	public function getClassTeacherByClassid($classid = 0){
		$sql = 'select uid,classid,folderid from ebh_classteachers where classid = '.$classid;
		return $this->db->query($sql)->list_array();
	}
	/**
	*获取教室下默认的班级信息，一般是最新添加的班级
	*/
	public function getDefaultClass($crid,$grade=0,$district=0) {
		if(!empty($grade) || !empty($district)){
			$sql = "select classid,classname from ebh_classes where crid=$crid and status=0 and grade=$grade and district=$district order by classid asc limit 1";
		}else{
			$sql = "select classid,classname from ebh_classes where crid=$crid and status=0 order by classid asc limit 1";
		}
		return $this->db->query($sql)->row_array();

	}
	/*
	添加班级
	@param array $param crid,classname
	@return int $classid 班级号
	*/
	public function addclass($param){
		$setarr['crid'] = $param['crid'];
		$setarr['classname'] = trim($param['classname'],' ');
		$setarr['classname'] = str_replace('　','',$setarr['classname']);
		if(isset($param['grade']))
			$setarr['grade'] = $param['grade'];
		if(isset($param['district']))
			$setarr['district'] = $param['district'];
		$setarr['dateline'] = SYSTIME;
		return $this->db->insert('ebh_classes',$setarr);
	}
	/*
	添加学生到classstudent表
	@param array $param crid classid uid
	*/
	public function addclassstudent($param){
		$setarr['uid'] = $param['uid'];
		$setarr['classid'] = $param['classid'];
		$this->db->update('ebh_classes',array(),array('classid'=>$param['classid']),array('stunum'=>'stunum+1'));
		$this->db->update('ebh_classrooms',array(),array('crid'=>$param['crid']),array('stunum'=>'stunum+1'));
		return $this->db->insert('ebh_classstudents',$setarr);
	}

	//获取学生班级和年级信息
	public function getStudentClasses($uid = 0){
		$sql = 'select ct.classid ,c.grade,c.district,c.crid from ebh_classstudents ct join ebh_classes c on ct.classid = c.classid where uid = '.$uid;
		return $this->db->query($sql)->list_array();
	}
	//获取班级信息
	public function getClassInfo($classid = 0){
		$sql = 'select classid,grade,classname,district from ebh_classes where classid = '.$classid;
		return $this->db->query($sql)->row_array();
	}

    /**
     * 获取班级课程，用于租赁网校
     * @param $classid 班级ID
     * @param int $crid 网校ID
     * @param bool $all 是否返回全部网校课程
     * @return array
     */
	public function getCourseList($classid, $crid = 0, $all = false) {
        $classid = intval($classid);
        $crid = intval($crid);
        if (!$all) {
            $sql = 'SELECT `b`.`folderid`,`b`.`foldername`,`b`.`img`,`b`.`credit`,`b`.`coursewarenum` FROM `ebh_classcourses` `a` 
                    LEFT JOIN `ebh_folders` `b` ON `b`.`folderid`=`a`.`folderid` 
                    WHERE `a`.`classid`='.$classid.' AND `b`.`del`=0 AND `b`.`folderlevel`=2 AND `b`.`power`=0 ORDER BY `a`.`folderid` DESC';
        } else {
            $classinfo = $this->getClassInfo($classid);
            if (empty($classinfo)) {
                return array();
            }
            $folderids = $this->db->query('SELECT `folderid` FROM `ebh_classcourses` WHERE `classid`='.$classid)->list_field('folderid');
            if (!empty($folderids)) {
                $folderids = implode(',', $folderids);
                $itype = '(CASE WHEN `folderid` IN('.$folderids.') THEN 3 WHEN `grade`='.$classinfo['grade'].' AND `district`='.$classinfo['district'].' THEN 2 WHEN `grade`='.$classinfo['grade'].' THEN 1 ELSE 0 END)';
            } else {
                $itype = '(CASE WHEN `grade`='.$classinfo['grade'].' AND `district`='.$classinfo['district'].' THEN 2 WHEN `grade`='.$classinfo['grade'].' THEN 1 ELSE 0 END)';
            }
            $sql = 'SELECT `folderid`,`foldername`,`img`,`cwcredit`,`coursewarenum` 
                FROM `ebh_folders` WHERE `crid`='.$crid.' AND `del`=0 AND `folderlevel`=2 AND `power`=0 ORDER BY '.$itype.' DESC,`folderid` DESC';
        }
        $folderlist = $this->db->query($sql)->list_array('folderid');
        if (empty($folderlist)) {
            return array();
        }
        //读取课程老师
        $teachers = $this->db->query(
            'SELECT `a`.`folderid`,GROUP_CONCAT(`b`.`username`,\' \',`b`.`realname`) AS `teachers` FROM `ebh_teacherfolders` `a` LEFT JOIN `ebh_users` `b` ON `b`.`uid`=`a`.`tid` WHERE `a`.`crid`='.$crid.' GROUP BY `folderid`')
            ->list_array('folderid');
        array_walk($folderlist, function(&$folder, $k, $teachers) {
            $folder['itemid'] = 0;
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
                $folder['tname'] = implode(',', $folder_teachers);
            } else {
                $folder['tname'] = '';
            }
        }, $teachers);
        return $folderlist;
    }

    /**
     * 判断学生是否有课程权限
     * @param $uid
     * @return bool
     */
    public function checkUserpermission($uid, $crid) {
        $class = $this->getClassByUid($crid, $uid);
        if (empty($class)) {
            return false;
        }
        $folder = $this->db->query('SELECT `a`.`folderid` FROM `ebh_classcourses` `a` LEFT JOIN `ebh_folders` `b` ON `b`.`folderid`=`a`.`folderid` WHERE `a`.`classid`='.intval($class['classid']).' AND `b`.`del`=0 AND `b`.`folderlevel`=2 LIMIT 1')->row_array();
	    if (empty($folder)) {
	        return false;
        }
        return true;
    }
}
