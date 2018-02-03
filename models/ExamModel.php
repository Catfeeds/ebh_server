<?php

/**
 * 作业类model
 */
class ExamModel extends CModel {

	/**
     * 根据时间返回以日期为单位的学生作业记录列表
     * @param array $param
     * @return listarray
     */
    public function getExamCountByDate($param) {
		if(empty($param['uid']))
			return FALSE;
		$sql = "select count(*) as e,DATE_FORMAT(FROM_UNIXTIME(s.dateline) ,'%Y-%m-%d') as d from ebh_schexamanswers s ".
                "left join ebh_schexams sc on (sc.eid = s.eid) ";
		if(!empty($param['crid']))
			$wherearr[] = 'sc.crid='.$param['crid'];
		$wherearr[] = 's.uid='.$param['uid'];
		if(!empty($param['tid']))
			$wherearr[] = 'sc.uid='.$param['tid'];
		if(!empty($param['startDate']))
			$wherearr[] = 's.dateline>='.$param['startDate'];
		if(!empty($param['endDate']))
			$wherearr[] = 's.dateline<'.$param['endDate'];
		$sql .= ' WHERE '.implode(' AND ',$wherearr);
		$sql .= ' group by d';
		return $this->db->query($sql)->list_array();
    }
    /**
     * 根据时间返回以日期为单位的学生听课笔记列表
     * @param array $param
     * @return listarray
     */
    public function getNoteCount($param) {
		if(empty($param['uid']))
			return FALSE;
		$sql = "select count(*) as e,DATE_FORMAT(FROM_UNIXTIME(n.dateline) ,'%Y-%m-%d') as d from ebh_notes n ".
                "join ebh_coursewares c on (n.cwid=c.cwid) ";
		$wherearr[] = 'n.uid='.$param['uid'];
		if(!empty($param['tid']))
			$wherearr[] = 'c.uid='.$param['tid'];
		if(!empty($param['crid']))
			$wherearr[] = 'n.crid='.$param['crid'];
		if(empty($param['startDate']))
			$wherearr[] = 'n.dateline>='.$param['startDate'];
		if(empty($param['endDate']))
			$wherearr[] = 'n.dateline<'.$param['endDate'];
		$sql .= ' WHERE '.implode(' AND ',$wherearr);
		$sql .= ' group by d';
        return $this->db->query($sql)->list_array();
    }
    /**
     * 根据时间返回以日期为单位的学生学习记录列表
     * @param array $param
     * @return listarray
     */
    public function getStudyCount($param) {
		if(empty($param['uid']))
			return FALSE;
		$sql = "select count(*) as e,DATE_FORMAT(FROM_UNIXTIME(p.lastdate) ,'%Y-%m-%d') as d from ebh_playlogs p ".
				"join ebh_roomcourses rc on (rc.cwid = p.cwid) ";
		$wherearr[] = 'p.uid='.$param['uid'];
		$wherearr[] = 'p.totalflag= 0';
		if(!empty($param['crid']))
			$wherearr[] = 'rc.crid='.$param['crid'];
		if(!empty($param['startDate']))
			$wherearr[] = 'p.lastdate>='.$param['startDate'];
		if(!empty($param['endDate']))
			$wherearr[] = 'p.lastdate<'.$param['endDate'];
        $sql .= ' WHERE '.implode(' AND ',$wherearr);
		$sql .= ' group by d';
        return $this->db->query($sql)->list_array();
    }
    /**
     * 根据时间返回以日期为单位的学生错题记录列表
     * @param array $param
     * @return listarray
     */
    public function getErrorCount($param) {
		if(empty($param['uid']))
			return FALSE;
		$sql = "select count(*) as e,DATE_FORMAT(FROM_UNIXTIME(er.dateline) ,'%Y-%m-%d') as d from ebh_schquestions q ".
                "join ebh_errorbook er on (FIND_IN_SET(er.exid,q.eid) and er.qid=q.qnumber) ";
		$wherearr[] = 'er.uid='.$param['uid'];
		if(!empty($param['crid']))
			$wherearr[] = 'q.crid='.$param['crid'];
		if(empty($param['startDate']))
			$wherearr[] = 'er.dateline>='.$param['startDate'];
		if(empty($param['endDate']))
			$wherearr[] = 'er.dateline<'.$param['endDate'];
		$sql .= ' WHERE '.implode(' AND ',$wherearr);
		$sql .= ' group by d';
        return $this->db->query($sql)->list_array();
    }
	/**
	* 根据时间返回以日期为单位的学生答疑答题记录列表
    * @param array $param
    * @return listarray
	*/
	public function getAskCount($param){//答疑
		if(empty($param['uid']))
			return FALSE;
		$sql = "select count(*) as e,DATE_FORMAT(FROM_UNIXTIME(a.dateline) ,'%Y-%m-%d') as d from ebh_askanswers a ".
				"join ebh_askquestions aq on(a.qid=aq.qid)";
		$wherearr[] = 'a.uid='.$param['uid'];
		if(!empty($param['crid']))
			$wherearr[] = 'aq.crid='.$param['crid'];
		if(!empty($param['startDate']))
			$wherearr[] = 'a.dateline>='.$param['startDate'];
		if(!empty($param['endDate']))
			$wherearr[] = 'a.dateline<'.$param['endDate'];
		if(!isset($param['shield'])){
			$wherearr[] = 'a.shield = 0';
		}else{
			$wherearr[] = 'a.shield = '.$param['shield'];
		}
		$sql .= ' WHERE '.implode(' AND ',$wherearr);
		$sql .= ' group by d';
        return $this->db->query($sql)->list_array();
	}
  

	/**
	*根据学生编号获取学校学生所在班级下的作业
	*/
	public function getExamListByMemberid($param) {
        if (isset($param['filteranswer']) === true) {
            //未答过作业
            return $this->homeWork($param);
        }

        if (isset($param['hasanswer']) === true) {
            if (isset($param['astatus']) === false) {
                //已完成作业
                return $this->scrap($param);
            }
            //已做作业，未提交，草稿箱
            return $this->scrap($param, false);
        }











		if(empty($param['uid']))
			return FALSE;
		$sql = 'SELECT e.eid,e.title,e.dateline as date,e.score totalscore,e.answercount,u.username,u.realname,u.sex,'.
            'u.face,a.dateline adate,a.totalscore score,a.completetime as usetime,a.status as astatus,'.
            'cw.title as cwtitle from ebh_schexams e '.
				'LEFT JOIN ebh_schexamanswers a on (e.eid = a.eid AND a.uid='.$param['uid'].') '.
				'JOIN ebh_users u on (u.uid = e.uid) '.
				'LEFT JOIN ebh_coursewares cw on (e.cwid = cw.cwid) ';
		$wherearr = array();
		if(!empty($param['crid']))
			$wherearr[] = 'e.crid='.$param['crid'];
		if(!empty($param['classid'])) {
			if(!empty($param['grade'])) {	// 根据年级过滤，一般在布置作业到年级时有效
				if(isset($param['district'])) {	// 根据校区过滤，一般在布置作业到年级时有效
					$wherearr[] = '(e.classid = '.$param['classid']. ' or e.grade = '.$param['grade'].' and e.district = '.$param['district'].')';
				} else {
					$wherearr[] = '(e.classid = '.$param['classid']. ' or e.grade = '.$param['grade'].')';
				}
			} else {
				$wherearr[] = 'e.classid='.$param['classid'];
			}
		}
		if(!empty($param['classids'])){
			$wherearr[] = 'e.classid in ('.implode(',',$param['classids']).")";
		}
		$wherearr[] = 'e.status = 1';
		if(isset($param['filteranswer']))	//过滤学生是否已经答题了，此处传值表示只显示学生未答的
			$wherearr[] = '(a.aid IS NULL or a.status = 0)';
		if(isset($param['hasanswer']))	//过滤学生是否已经答题了，此处传值表示只显示学生已答的
			$wherearr[] = 'a.aid IS NOT NULL';
		if(isset($param['subtime'])) {	// 根据时间获取记录数
			$wherearr[] = 'e.dateline > '.$param['subtime'];
		}
		if(!empty($param['q']))	//按作业标题搜索
			$wherearr[] = 'title like \'%'.$this->db->escape_str($param['q']).'%\'';
		if(!empty($param['abegindate'])) {	//答题开始时间
			$wherearr[] = 'a.dateline>='.$param['abegindate'];
		}
		if(!empty($param['aenddate'])) {	//答题完成时间
			$wherearr[] = 'a.dateline<'.$param['aenddate'];
		}
		if(!empty($param['folderid'])) {	//作业所属课程
			$wherearr[] = 'e.folderid = '.$param['folderid'];
		}
		if(!empty($param['cwid'])){
			$wherearr[] = 'e.cwid = '.$param['cwid'];
		}
		if(isset($param['astatus'])) {	// 草稿箱状态，0为答题草稿箱 1为已提交
			$wherearr[] = 'a.status = '.$param['astatus'];
		}
		$sql .= ' WHERE '.implode(' AND ',$wherearr);
		if(!empty($param['order']))
			$sql .= ' ORDER BY '.$param['order'];
		else
			$sql .= ' ORDER BY e.eid DESC';
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
     * 未完成作业
     * @param $param
     * @return mixed
     */
    public function homeWork($param) {
        if (empty($param['uid']) === true || empty($param['classid']) === true && empty($param['classids']) === true) {
            return false;
        }
        $uid = (int) $param['uid'];
        //作业条件
        $schexam_where_arr = array();

        if (empty($param['classids']) === true) {
            $schexam_where_arr[] = "`classid`=" . intval($param['classid']);
        } else {
            if (is_array($param['classids']) === false) {
                return false;
            }
            $classids = implode(',', $param['classids']);
            $schexam_where_arr[] = "`classid` IN($classids)";
        }

        if (empty($param['crid']) === false) {
            $schexam_where_arr[] = "`crid`=" . intval($param['crid']);
        }
        if (empty($param['folderid']) === false) {
            //作业所属课程
            $schexam_where_arr[] = "`folderid`=" . intval($param['folderid']);
        }
        if (empty($param['cwid']) === false){
            //作业所属课件
            $schexam_where_arr[] = "`cwid`=" . intval($param['cwid']);
        }

        $schexam_where_arr[] = "`status`=1";
        if (empty($param['q']) === false) {
            $schexam_where_arr[] = "`title` LIKE '%" . $this->db->escape_str($param['q']) . "'%";
        }
        if (empty($param['subtime']) === false) {
            //试卷添加时间
            $schexam_where_arr[] = "`dateline`>=" . intval($param['subtime']);
        }
        $schexam_where_arr[] = "NOT EXISTS(SELECT 1 FROM `ebh_schexamanswers` WHERE ".
            "`ebh_schexamanswers`.`uid`=$uid AND `ebh_schexams`.`eid`=`ebh_schexamanswers`.`eid`)";

        $where_str = implode(' AND ', $schexam_where_arr);
        $sql = "SELECT `uid`,`eid`,`title`,`dateline` AS `date`,`score` AS `totalscore`,`answercount`,`cwid` FROM ".
            "`ebh_schexams` WHERE $where_str";

        if (empty($param['grade']) === false) {
            array_shift($schexam_where_arr);
            array_unshift($schexam_where_arr, "`grade`=" . intval($param['grade']));
            if (isset($param['district']) === true) {
                $schexam_where_arr[] = "`district`=" . intval($param['district']);
            }
            $where_str = implode(' AND ', $schexam_where_arr);
            $sql .= " UNION SELECT `uid`,`eid`,`title`,`dateline` AS `date`,`score` AS `totalscore`,".
                "`answercount`,`cwid` FROM `ebh_schexams` WHERE $where_str";
        }

        if(empty($param['order']) === false) {
            $sql .= " ORDER BY `" . $param['order'] . "`";
        } else {
            $sql .= " ORDER BY `eid` DESC";
        }

        $limit = 20;
        if (empty($param['limit']) === false) {
            $limit = (int) $param['limit'];
            $limit = max($limit, 1);
        }
        $page = 1;
        if (empty($param['page']) === false) {
            $page = (int) $param['page'];
            $page = max($page, 1);
        }
        $offset = ($page - 1) * $limit;
        $sql .= " LIMIT $offset,$limit";

        $result = $this->db->query($sql)->list_array();
        //Ebh::app()->getLog()->log($sql);
        if (empty($result) === false) {
            //获取布置作业老师信息
            $uid_arr = array_column($result, 'uid');
            if (empty($uid_arr) === true) {
                return false;
            }
            $uid_arr_str = implode(',', $uid_arr);
            $sql = "SELECT `uid`,`username`,`realname`,`sex`,`face` FROM `ebh_users` WHERE `uid` IN($uid_arr_str)";
            $teacher = $this->db->query($sql)->list_array('uid');
            if (empty($teacher) === true) {
                return false;
            }
            $cwid_arr = array_column($result, 'cwid');
            $cwid_arr_str = implode(',', $cwid_arr);
            $sql = "SELECT `cwid`,`title` FROM `ebh_coursewares` WHERE `cwid` IN($cwid_arr_str)";
            $coursewares = $this->db->query($sql)->list_array('cwid');
            foreach ($result as &$item) {
                if (isset($teacher[$item['uid']]) === true) {
                    $item['username'] = $teacher[$item['uid']]['username'];
                    $item['realname'] = $teacher[$item['uid']]['realname'];
                    $item['sex'] = $teacher[$item['uid']]['sex'];
                    $item['face'] = $teacher[$item['uid']]['face'];
                }
                if (empty($coursewares) === false && isset($coursewares[$item['cwid']])) {
                    $item['cwtitle'] = $coursewares[$item['cwid']]['title'];
                }
                $item['adate'] = 0;
                $item['score'] = 0;
                $item['usetime'] = 0;
            }
        }
        return $result;
    }
    /**
     * 作业
     * @param $param
     * @param $is_post
     * @return bool
     */
    public function scrap($param, $is_post = true) {
        if (empty($param['uid']) === true || empty($param['classid']) === true && empty($param['classids']) === true) {
            return false;
        }
        $uid = (int) $param['uid'];
        //作业条件
        $where_arr = array();

        $where_arr[] = "`a`.`uid`=$uid";
        $where_arr[] = $is_post === true ? "`a`.`status`=1" : "`a`.`status`=0";
        if (empty($param['abegindate']) === false) {
            //答题开始时间
            $where_arr[] = "`a`.`dateline`>=" . intval($param['abegindate']);
        }
        if (empty($param['aenddate']) === false) {
            //答题结束时间
            $where_arr[] = "`a`.`dateline`<=" . intval($param['aenddate']);
        }

        $where_arr[] = "`a`.`eid`=`s`.`eid`";
        if (empty($param['crid']) === false) {
            $where_arr[] = "`s`.`crid`=" . intval($param['crid']);
        }
        $where_arr[] = "`s`.`status`=1";
        if (empty($param['classids']) === true) {
            $range_str = "`s`.`classid`=" . intval($param['classid']);
            if (empty($param['grade']) === false) {
                $range_str = "($range_str OR `s`.`grade`=" . intval($param['grade']);
                if (isset($param['district']) === true) {
                    $range_str .= " AND `s`.`district`=" . intval($param['district']);
                }
                $range_str .= ")";
                $where_arr[] = $range_str;
            }
        } else {
            if (is_array($param['classids']) === false) {
                return false;
            }
            $classids = implode(',', $param['classids']);
            $where_arr[] = "`s`.`classid` IN($classids)";
        }

        if (empty($param['folderid']) === false) {
            //作业所属课程
            $where_arr[] = "`s`.`folderid`=" . intval($param['folderid']);
        }
        if (empty($param['cwid']) === false){
            //作业所属课件
            $where_arr[] = "`s`.`cwid`=" . intval($param['cwid']);
        }

        if (empty($param['q']) === false) {
            $where_arr[] = "`s`.`title` LIKE '%" . $this->db->escape_str($param['q']) . "'%";
        }
        if (empty($param['subtime']) === false) {
            //试卷添加时间
            $where_arr[] = "`s`.`dateline`>=" . intval($param['subtime']);
        }

        $where_str = implode(' AND ', $where_arr);
        $sql = "SELECT `s`.`uid`,`s`.`eid`,`s`.`title`,`s`.`dateline` AS `date`,`s`.`score` AS `totalscore`,".
            "`s`.`answercount`,`s`.`cwid`,`a`.`dateline` AS `adate`,`a`.`totalscore` AS `score`,".
            "`a`.`completetime` AS `usetime` FROM ".
            "`ebh_schexamanswers` AS `a`,`ebh_schexams` AS `s` WHERE $where_str";

        if(empty($param['order']) === false) {
            $sql .= " ORDER BY `" . $param['order'] . "`";
        } else {
            $sql .= " ORDER BY `eid` DESC";
        }

        $limit = 20;
        if (empty($param['limit']) === false) {
            $limit = (int) $param['limit'];
            $limit = max($limit, 1);
        }
        $page = 1;
        if (empty($param['page']) === false) {
            $page = (int) $param['page'];
            $page = max($page, 1);
        }
        $offset = ($page - 1) * $limit;
        $sql .= " LIMIT $offset,$limit";

        $result = $this->db->query($sql)->list_array();
        //Ebh::app()->getLog()->log($sql);
        if (empty($result) === false) {
            //获取布置作业老师信息
            $uid_arr = array_column($result, 'uid');
            if (empty($uid_arr) === true) {
                return false;
            }
            $uid_arr_str = implode(',', $uid_arr);
            $sql = "SELECT `uid`,`username`,`realname`,`sex`,`face` FROM `ebh_users` WHERE `uid` IN($uid_arr_str)";
            $teacher = $this->db->query($sql)->list_array('uid');
            if (empty($teacher) === true) {
                return false;
            }
            $cwid_arr = array_column($result, 'cwid');
            $cwid_arr_str = implode(',', $cwid_arr);
            $sql = "SELECT `cwid`,`title` FROM `ebh_coursewares` WHERE `cwid` IN($cwid_arr_str)";
            $coursewares = $this->db->query($sql)->list_array('cwid');
            foreach ($result as &$item) {
                if (isset($teacher[$item['uid']]) === true) {
                    $item['username'] = $teacher[$item['uid']]['username'];
                    $item['realname'] = $teacher[$item['uid']]['realname'];
                    $item['sex'] = $teacher[$item['uid']]['sex'];
                    $item['face'] = $teacher[$item['uid']]['face'];
                }
                if (empty($coursewares) === false && isset($coursewares[$item['cwid']])) {
                    $item['cwtitle'] = $coursewares[$item['cwid']]['title'];
                }
            }
        }
        return $result;
    }

	/**
	*根据学生编号获取学校学生所在班级下的作业记录总数
	*/
	public function getExamListCountByMemberid($param) {
        if (isset($param['filteranswer']) === true) {
            //统计未答过作业
            return $this->countHomeWork($param);
        }

        if (isset($param['hasanswer']) === true) {
            if (isset($param['astatus']) === false) {
                //统计已完成作业
                return $this->countScrap($param);
            }
            //统计已做作业，未提交，草稿箱
            return $this->countScrap($param, false);
        }
	}

    /**
     * 统计未做作业
     * @param $param
     * @return bool
     */
    public function countHomeWork($param) {
        if (empty($param['uid']) === true || empty($param['classid']) === true && empty($param['classids']) === true) {
            return false;
        }
        $uid = (int) $param['uid'];
        //作业条件
        $schexam_where_arr = array();

        if (empty($param['classids']) === true) {
            $schexam_where_arr[] = "`classid`=" . intval($param['classid']);
        } else {
            if (is_array($param['classids']) === false) {
                return false;
            }
            $classids = implode(',', $param['classids']);
            $schexam_where_arr[] = "`classid` IN($classids)";
        }

        if (empty($param['crid']) === false) {
            $schexam_where_arr[] = "`crid`=" . intval($param['crid']);
        }
        if (empty($param['folderid']) === false) {
            //作业所属课程
            $schexam_where_arr[] = "`folderid`=" . intval($param['folderid']);
        }
        if (empty($param['cwid']) === false){
            //作业所属课件
            $schexam_where_arr[] = "`cwid`=" . intval($param['cwid']);
        }
        $schexam_where_arr[] = "`status`=1";
        if (empty($param['q']) === false) {
            $schexam_where_arr[] = "`title` LIKE '%" . $this->db->escape_str($param['q']) . "'%";
        }
        if (empty($param['subtime']) === false) {
            //试卷添加时间
            $schexam_where_arr[] = "`dateline`>=" . intval($param['subtime']);
        }
        $schexam_where_arr[] = "NOT EXISTS(SELECT 1 FROM `ebh_schexamanswers` WHERE ".
            "`ebh_schexamanswers`.`uid`=$uid AND `ebh_schexams`.`eid`=`ebh_schexamanswers`.`eid`)";

        $where_str = implode(' AND ', $schexam_where_arr);
        $sql = "SELECT COUNT(1) AS `c` FROM (SELECT `eid` FROM ".
            "`ebh_schexams` WHERE $where_str";

        if (empty($param['grade']) === false) {
            array_shift($schexam_where_arr);
            array_unshift($schexam_where_arr, "`grade`=" . intval($param['grade']));
            if (isset($param['district']) === true) {
                $schexam_where_arr[] = "`district`=" . intval($param['district']);
            }
            $where_str = implode(' AND ', $schexam_where_arr);
            $sql .= " UNION SELECT `eid` FROM ".
                "`ebh_schexams` WHERE $where_str";
        }
        $sql .= ") AS `union_data`";

        $result = $this->db->query($sql)->row_array();
        //Ebh::app()->getLog()->log($sql);
        if (empty($result) === true) {
            return false;
        }
        return $result['c'];
    }

    /**
     * 统计作业
     * @param $param
     * @param bool $is_post
     * @return bool
     */
    public function countScrap($param, $is_post = true) {
        if (empty($param['uid']) === true || empty($param['classid']) === true && empty($param['classids']) === true) {
            return false;
        }
        $uid = (int) $param['uid'];
        //作业条件
        $where_arr = array();

        $where_arr[] = "`a`.`uid`=$uid";
        $where_arr[] = $is_post === true ? "`a`.`status`=1" : "`a`.`status`=0";
        if (empty($param['abegindate']) === false) {
            //答题开始时间
            $where_arr[] = "`a`.`dateline`>=" . intval($param['abegindate']);
        }
        if (empty($param['aenddate']) === false) {
            //答题结束时间
            $where_arr[] = "`a`.`dateline`<=" . intval($param['aenddate']);
        }

        $where_arr[] = "`a`.`eid`=`s`.`eid`";
        if (empty($param['crid']) === false) {
            $where_arr[] = "`s`.`crid`=" . intval($param['crid']);
        }
        $where_arr[] = "`s`.`status`=1";
        if (empty($param['classids']) === true) {
            $range_str = "`s`.`classid`=" . intval($param['classid']);
            if (empty($param['grade']) === false) {
                $range_str = "($range_str OR `s`.`grade`=" . intval($param['grade']);
                if (isset($param['district']) === true) {
                    $range_str .= " AND `s`.`district`=" . intval($param['district']);
                }
                $range_str .= ")";
                $where_arr[] = $range_str;
            }
        } else {
            if (is_array($param['classids']) === false) {
                return false;
            }
            $classids = implode(',', $param['classids']);
            $where_arr[] = "`s`.`classid` IN($classids)";
        }
        if (empty($param['folderid']) === false) {
            //作业所属课程
            $where_arr[] = "`s`.`folderid`=" . intval($param['folderid']);
        }
        if (empty($param['cwid']) === false){
            //作业所属课件
            $where_arr[] = "`s`.`cwid`=" . intval($param['cwid']);
        }
        if (empty($param['q']) === false) {
            $where_arr[] = "`s`.`title` LIKE '%" . $this->db->escape_str($param['q']) . "'%";
        }
        if (empty($param['subtime']) === false) {
            //试卷添加时间
            $where_arr[] = "`s`.`dateline`>=" . intval($param['subtime']);
        }

        $where_str = implode(' AND ', $where_arr);
        $sql = "SELECT COUNT(1) AS `c` FROM ".
            "`ebh_schexamanswers` AS `a`,`ebh_schexams` AS `s` WHERE $where_str";


        $result = $this->db->query($sql)->row_array();
        if (empty($result) === true) {
            return false;
        }
        //Ebh::app()->getLog()->log($sql);
        return $result['c'];
    }

	//获取学生针对某个作业的回答情况(学校版本)
	public function getStuExamAnswerInfo($param = array()){
		if(empty($param)){
			return array();
		}
		$sql = 'select se.eid,se.title,se.score,se.folderid,sea.totalscore from ebh_schexams se join ebh_schexamanswers sea on se.eid = sea.eid';
		$wherearr = array();
		if(!empty($param['uid'])){
			$wherearr[] ='sea.uid = '.$param['uid'];
		}
		if(!empty($param['eid'])){
			$wherearr[] ='sea.eid = '.$param['eid'];
		}
		if(isset($param['status'])){
			$wherearr[] ='sea.status = '.$param['status'];
		}
		if(!empty($param['cwid'])){
			$wherearr[] = 'se.cwid = '.$param['cwid'];
		}
		if(!empty($wherearr)){
			$sql.= ' WHERE '.implode(' AND ',$wherearr);
		}

		if(!empty($param['limit'])) {
			$sql .= ' limit '.$param['limit'];
		} else{
			$sql .= ' limit 0,100 ';
		}
		return $this->db->query($sql)->list_array();
	}


/**
	*学校学生我的错题本列表
	*/
	public function myscherrorbooklist($param = array()) {
		if(empty($param['uid']))
			return FALSE;
		$sql = 'SELECT e.exid,e.eid,ex.title as etitle,q.ques,e.qid,e.dateline,q.quetype,q.falsenum,q.score,e.uid,e.erranswers,q.title,e.uid from ebh_schquestions q '.
				'join ebh_errorbook e on (q.qid=e.quesid) '.
				'join ebh_schexams ex on (ex.eid in (q.eid)) '.
				'join ebh_classstudents c on (e.uid=c.uid) '.
				'join ebh_classes cs on (cs.classid=c.classid) ';
		$wherearr = array();
		if(!empty($param['crid'])) {
			$wherearr[] = 'q.crid='.$param['crid']; 
			$wherearr[] = 'cs.crid='.$param['crid'];
		}
		$wherearr[] = 'e.uid='.$param['uid'];
		$wherearr[] = 'q.ques !=\'\'';
		if(!empty($param['quetype'])){
			$wherearr[] = 'q.quetype ="'.$this->db->escape_str($param['quetype']).'"';
		}else{
			$wherearr[] = 'q.quetype !=\'H\'';
		}
		if(!empty($param['folderid'])){
			$wherearr[] = 'q.folderid='.$param['folderid']; 
		}
		if(!empty($param['chapterid'])){
			$wherearr[] = 'q.chapterid='.$param['chapterid']; 
		}
		$wherearr[] = 'ex.title !=\'\'';
		if(!empty($param['q']))
			$wherearr[] = 'q.title like \'%'.$this->db->escape_str($param['q']).'%\'';
		if(!empty($param['startDate']))
			$wherearr[] = 'e.dateline>='.$param['startDate'];
		if(!empty($param['endDate']))
			$wherearr[] = 'e.dateline<'.$param['endDate'];
		
		$sql .= ' WHERE '.implode(' AND ',$wherearr);
		if(!empty($param['order']))
			$sql .= ' order by '.$param['order'];
		else
			$sql .= ' order by  e.eid desc';
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
		$list = $this->db->query($sql)->list_array();
		$errorbooks = array();
		foreach($list as $l) {
			$l['subject'] = preg_replace('/(<[^>]*>)|(<[^>]*$)/', ' ', $l['title']); 
			$l['ques'] =  base64str(unserialize($l['ques']));				
			$errorbooks [] = $l;
		}
		return $errorbooks;
	}
	
	/**
	 * 删除错题本
	 */
	public function delerrorbook($param){
		if(empty($param['eid']) || empty($param['uid'])){
			return false;
		}
		$wherearr['eid'] = $param['eid'];
		$wherearr['uid'] = $param['uid'];
		return $this->db->delete('ebh_errorbook',$wherearr);
	}
	/**
	*获取作业基本信息
	*@param $eid int 作业编号
	*/
	public function getExamInfo($eid) {
		$sql = "select e.title,e.crid,e.classid,e.grade,e.district,e.folderid,e.cwid From ebh_schexams e where e.eid=$eid";
		return $this->db->query($sql)->row_array();
	}
}

?>