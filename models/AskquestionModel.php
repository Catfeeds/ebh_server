<?php

/*
  答疑
 */

class AskquestionModel extends CModel {

    /**
     * 添加问题
     * @param type $param
     * @return int
     */
    public function insert($param) {
        if (!empty($param ['crid'])) {
            $setarr['crid'] = $param['crid'];
        }
        if (!empty($param ['folderid'])) {
            $setarr['folderid'] = $param['folderid'];
        }
        if (!empty($param ['tid'])) {
            $setarr['tid'] = $param['tid'];
        }
        if (!empty($param ['catid'])) {
            $setarr['catid'] = $param['catid'];
        }
        if (!empty($param ['grade'])) {
            $setarr['grade'] = $param['grade'];
        }
        if (!empty($param ['uid'])) {
            $setarr['uid'] = $param['uid'];
        }
        if (!empty($param ['title'])) {
            $setarr['title'] = $param['title'];
        }
        if (!empty($param ['message'])) {
            $setarr['message'] = $param['message'];
        }
        if (!empty($param ['catpath'])) {
            $setarr['catpath'] = $param['catpath'];
        }
        if (!empty($param ['audioname'])) {
            $setarr['audioname'] = $param['audioname'];
        }
        if (!empty($param ['audiosrc'])) {
            $setarr['audiosrc'] = $param['audiosrc'];
        }
        if (!empty($param ['imagename'])) {
            $setarr['imagename'] = $param['imagename'];
        }
        if (!empty($param ['imagesrc'])) {
            $setarr['imagesrc'] = $param['imagesrc'];
        }
        if (!empty($param ['attname'])) {
            $setarr['attname'] = $param['attname'];
        }
        if (!empty($param ['attsrc'])) {
            $setarr['attsrc'] = $param['attsrc'];
        }
        if (!empty($param ['catpath'])) {
            $setarr['catpath'] = $param['catpath'];
        }
        if (!empty($param['cwid'])) {
            $setarr['cwid'] = $param['cwid'];
        }
        if (!empty($param['cwname'])) {
            $setarr['cwname'] = $param['cwname'];
        }
        $setarr['dateline'] = SYSTIME;
        if (!empty($param ['fromip'])) {
            $setarr['fromip'] = $param['fromip'];
        }
		if(!empty($param['coverimg'])){
			$setarr['coverimg'] = $param['coverimg'];
		}else{
			$setarr['coverimg'] = '';
		}
        $qid = $this->db->insert('ebh_askquestions', $setarr);
        return $qid;
    }

    /**
     * 更新问题
     * @param type $param
     * @return boolean
     */
    public function update($param) {
        if (empty($param['qid']) && empty($param['uid']))
            return FALSE;
        $wherearr = array('qid' => $param['qid'], 'uid' => $param['uid']);
        if (!empty($param ['folderid'])) {
            $setarr['folderid'] = $param['folderid'];
        }
        if (!empty($param ['catid'])) {
            $setarr['catid'] = $param['catid'];
        }
        if (!empty($param ['grade'])) {
            $setarr['grade'] = $param['grade'];
        }
        if (!empty($param ['title'])) {
            $setarr['title'] = $param['title'];
        }
        if (!empty($param ['message'])) {
            $setarr['message'] = $param['message'];
        }
        if (!empty($param ['catpath'])) {
            $setarr['catpath'] = $param['catpath'];
        }
        if (!empty($param ['audioname'])) {
            $setarr['audioname'] = $param['audioname'];
        }
        if (!empty($param ['audiosrc'])) {
            $setarr['audiosrc'] = $param['audiosrc'];
        }
        if (!empty($param ['imagename'])) {
            $setarr['imagename'] = $param['imagename'];
        }
        if (!empty($param ['imagesrc'])) {
            $setarr['imagesrc'] = $param['imagesrc'];
        }
        if (!empty($param ['attname'])) {
            $setarr['attname'] = $param['attname'];
        }
        if (!empty($param ['attsrc'])) {
            $setarr['attsrc'] = $param['attsrc'];
        }
        if (!empty($param ['catpath'])) {
            $setarr['catpath'] = $param['catpath'];
        }
        if (!empty($param ['fromip'])) {
            $setarr['fromip'] = $param['fromip'];
        }
        if (isset($param ['tid'])) {
            $setarr['tid'] = $param['tid'];
        }
        if (!empty($param['lastansweruid'])){
            $setarr['lastansweruid'] = $param['lastansweruid'];
        }
        if (isset($param['cwid'])){
            $setarr['cwid'] = $param['cwid'];
        }
        if (isset($param['cwname'])){
            $setarr['cwname'] = $param['cwname'];
        }
        $afrows = $this->db->update('ebh_askquestions', $setarr, $wherearr);
        return $afrows;
    }

    /*
      答疑列表（试用用户未登陆）
      @param array $param
      @return array 列表数组
     */

    public function getaskquestionlist($param) {
        $wherearr = array();
        $sql = 'select q.qid,q.catpath,q.dateline,q.crid,q.title,q.message,u.username,q.answercount,q.thankcount,q.hasbest,q.viewnum,u.realname from ebh_askquestions q left join ebh_users u on q.uid=u.uid';
        if (isset($param['crid']))
            $wherearr[] = 'q.crid=' . $param['crid'];
        if (!empty($param['uid']))
            $wherearr[] = 'q.uid=' . $param['uid'];
		if (!empty($param['catid']))
            $wherearr[] = 'q.catid =' . $param['catid'];
		if (!empty($param['catidlist']))
            $wherearr[] = 'q.catid in(' . $param['catidlist'].')';
		if (isset($param['folderid']))
            $wherearr[] = 'q.folderid=' . $param['folderid'];
		if (!empty($param['grade']))
            $wherearr[] = 'q.grade =' . $param['grade'];
        if (!empty($param['q']))
            $wherearr[] = '(q.title like \'%' . $this->db->escape_str($param['q']) . '%\' or q.message like \'%' . $this->db->escape_str($param['q']) . '%\' or u.username like \'%' . $this->db->escape_str($param['q']) . '%\')';
        if (!empty($wherearr))
            $sql.= ' WHERE ' . implode(' AND ', $wherearr);
		if (!empty($param['order'])) {
            $sql .= ' order by ' . $param['order'];
        } else {
            $sql .= ' order by q.qid desc ';
        }
        if(!empty($param['limit'])) {
            $sql .= ' limit '. $param['limit'];
        } else {
			if (empty($param['page']) || $param['page'] < 1)
				$page = 1;
			else
				$page = $param['page'];
			$pagesize = empty($param['pagesize']) ? 30 : $param['pagesize'];
			$start = ($page - 1) * $pagesize;
            $sql .= ' limit ' . $start . ',' . $pagesize;
        }
        //var_dump($sql);
        return $this->db->query($sql)->list_array();
    }

    /*
      答疑数量
      @param array $param
      @return int
     */

    public function getaskquestioncount($param) {
        $wherearr = array();
        $sql = 'select count(*) count from ebh_askquestions q left join ebh_users u on q.uid=u.uid';
        if (!empty($param['q']))
            $wherearr[] = ' (q.title like \'%' . $this->db->escape_str($param['q']) . '%\' or q.message like \'%' . $this->db->escape_str($param['q']) . '%\' or u.username like \'%' . $this->db->escape_str($param['q']) . '%\')';
        if (!empty($param['crid']))
            $wherearr[] = ' crid = ' . $param['crid'];
		if (!empty($param['folderid']))
            $wherearr[] = ' folderid = ' . $param['folderid'];
        if (!empty($wherearr))
            $sql.= ' WHERE ' . implode(' AND ', $wherearr);
        //var_dump($sql);
        $count = $this->db->query($sql)->row_array();
        return $count['count'];
    }

    /*
      删除答疑
      @param int $qid
      @return bool
     */

    public function deleteaskquestion($qid) {
        return $this->db->delete('ebh_askquestions','qid='.$qid);
    }
	/*
	批量删除
	*/
	public function delAll($qidarr){
		$this->db->begin_trans();
		foreach($qidarr as $qid){
			if(!empty($qid))
				$this->db->delete('ebh_askquestions','qid='.$qid);
		}
		if ($this->db->trans_status() === FALSE) {
            $this->db->rollback_trans();
            return FALSE;
        } else {
            $this->db->commit_trans();
        }
		return TRUE;
	}
    /**
     * 教师全部问题列表
     * @param type $param
     * @return type
     */
    public function getallasklist($param) {
        if (empty($param['page']) || $param['page'] < 1)
            $page = 1;
        else
            $page = $param['page'];
        $pagesize = empty($param['pagesize']) ? 30 : $param['pagesize'];
        $start = ($page - 1) * $pagesize;
        $sql = 'select q.qid,q.title,q.message,u.groupid,u.username,u.realname,u.face,u.sex,f.foldername as catname,q.dateline as date,0 as answerdate,q.answercount,q.viewnum,q.hasbest,q.lastansweruid,q.imagesrc from ebh_askquestions q join ebh_users u on (q.uid = u.uid) left join ebh_folders f on (f.folderid = q.folderid)';
        $wherearr = array();
        if (!empty($param['crid']))
            $wherearr[] = 'q.crid=' . $param['crid'];
        if (!empty($param['uid']))
            $wherearr[] = 'q.uid=' . $param['uid']; 
        if (isset($param['shield']))
            $wherearr[] = 'q.shield=' . $param['shield'];
		if (!empty($param['folderid']))
            $wherearr[] = 'q.folderid=' . $param['folderid'];
		if (!empty($param['cwid']))
			$wherearr[] = 'q.cwid=' . $param['cwid'];
        if (!empty($param['q']))
            $wherearr[] = '(q.title like \'%' . $this->db->escape_str($param['q']) . '%\' or u.username like \'%' . $this->db->escape_str($param['q']) . '%\')';
        if (!empty($wherearr))
            $sql.= ' WHERE ' . implode(' AND ', $wherearr);
        if (!empty($param['order'])) {
            $sql .= ' order by ' . $param['order'];
        } else {
            $sql .= ' order by q.qid desc ';
        }
        $sql .= ' limit ' . $start . ',' . $pagesize;
        return $this->db->query($sql)->list_array();
    }

    /**
     * 教师全部问题列表记录数
     * @param type $param
     * @return type
     */
    public function getallaskcount($param) {
        $count = 0;
        $sql = 'select count(*) count from ebh_askquestions q join ebh_users u on (q.uid = u.uid)';
        $wherearr = array();
        if (!empty($param['crid']))
            $wherearr[] = 'q.crid=' . $param['crid'];
        if (!empty($param['uid']))
            $wherearr[] = 'q.uid=' . $param['uid'];
		if (isset($param['folderid']))
            $wherearr[] = 'q.folderid=' . $param['folderid'];
        if (!empty($param['q']))
            $wherearr[] = '(q.title like \'%' . $this->db->escape_str($param['q']) . '%\' or u.username like \'%' . $this->db->escape_str($param['q']) . '%\')';
        if (!empty($wherearr))
            $sql.= ' WHERE ' . implode(' AND ', $wherearr);
        $countrow = $this->db->query($sql)->row_array();
        if (!empty($countrow) && !empty($countrow['count']))
            $count = $countrow['count'];
        return $count;
    }

    /**
     * 删除答疑问题表
     * @param int $qid问题编号
     * @return boolean
     */
    public function delask($qid) {
        $this->db->begin_trans();
        //删除课件评论，ebh_logs和ebh_reviews
        $wherearr = array('qid' => $qid);
        //删除回答记录
        $this->db->delete('ebh_askanswers', $wherearr);
        //删除问题表
        $arows = $this->db->delete('ebh_askquestions', $wherearr);
        if ($this->db->trans_status() === FALSE) {
            $this->db->rollback_trans();
            return FALSE;
        } else {
            $this->db->commit_trans();
        }
        if ($arows > 0)
            return TRUE;
        return FALSE;
    }

    /**
     * 根据问题编号获取问题信息
     * @param int $qid
     * @return array
     * 修复folderid = 0查不到记录导致删除失败改用left join
     */
    public function getaskbyqid($qid) {
        $sql = 'select q.title,q.uid,q.message as txt,q.crid,u.username,u.realname,u.face,u.sex,f.foldername as catname,q.dateline as date,q.audiosrc as audio,q.imagesrc as image,q.answercount,q.viewnum,q.hasbest,q.thankcount from ebh_askquestions q '.
				'left join ebh_users u on (u.uid=q.uid) '.
				'left join ebh_folders f on (f.folderid=q.folderid) '.
				'where q.qid=' . $qid;
        return $this->db->query($sql)->row_array();
    }

    /**
     * 根据问题编号获取详细答题记录列表
     * @param int $qid
     */
    public function getdetailanswersbyqid($qid,$queryarr = array()) {
        if (empty($queryarr['page']) || $queryarr['page'] < 1)
            $page = 1;
        else
            $page = $queryarr['page'];
        $pagesize = empty($queryarr['pagesize']) ? 10 : $queryarr['pagesize'];
        $start = ($page - 1) * $pagesize;

        $sql = 'select a.aid,a.uid,a.message as txt,a.audiosrc as audio,a.imagesrc as image,a.isbest,a.thankcount,a.dateline as date,a.audiotime, u.username,u.realname,u.face,u.sex from ebh_askanswers a '
                . ' join ebh_users u on (u.uid = a.uid) where a.qid=' . $qid;
        if(isset($queryarr['shield'])){
           $sql .= ' AND a.shield = '.$queryarr['shield'];
        }
        $sql .= ' ORDER BY a.isbest desc,a.aid desc';
        $sql .= ' limit ' . $start . ',' . $pagesize;
        return $this->db->query($sql)->list_array();
    }

    /**
     * 根据问题编号获取详细答题记录数量
     * @param int $qid
     */
    public function getdetailanswerscountbyqid($qid) {
        $count = 0;
        $sql = 'select count(*) count from ebh_askanswers a '
                . ' join ebh_users u on (u.uid = a.uid) where a.qid=' . $qid;
        $countrow = $this->db->query($sql)->row_array();
        if (!empty($countrow) && !empty($countrow['count']))
            $count = $countrow['count'];
        return $count;
    }

    /**
     * 获取用户回答过的问题列表
     * @param array $param
     * @return list
     */
    public function getasklistbyanswers($param) {
        if (empty($queryarr['page']) || $queryarr['page'] < 1)
            $page = 1;
        else
            $page = $queryarr['page'];
        $pagesize = empty($queryarr['pagesize']) ? 10 : $queryarr['pagesize'];
        $start = ($page - 1) * $pagesize;
        $sql = 'SELECT q.qid,q.title,u.username,u.realname,u.face,u.groupid,f.foldername as catname,q.dateline as date,a.dateline as answerdate,q.answercount,q.viewnum,q.hasbest FROM ebh_askquestions q ' .
                'LEFT JOIN ebh_askanswers a ON (q.qid = a.qid) ' .
                'LEFT JOIN ebh_users u ON (u.uid = q.uid) ' .
                'LEFT JOIN ebh_folders f on (q.folderid = f.folderid) ';
        $wherearr = array();
        if (!empty($param['crid']))
            $wherearr[] = 'q.crid=' . $param['crid'];
        if (!empty($param['uid']))
            $wherearr[] = 'a.uid=' . $param['uid'];
		if(!empty($param['startDate']))
			$wherearr[] = 'a.dateline>='.$param['startDate'];
		if(!empty($param['endDate']))
			$wherearr[] = 'a.dateline<'.$param['endDate'];
        if(!isset($param['shield'])){
            $wherearr[] = 'a.shield = 0';
        }else{
            $wherearr[] = 'a.shield = '.$param['shield'];
        }
        if (!empty($param['q']))
            $wherearr[] = '(q.title like \'%' . $this->db->escape_str($param['q']) . '%\' or u.username like \'%' . $this->db->escape_str($param['q']) . '%\')';
        if (!empty($wherearr))
            $sql.= ' WHERE ' . implode(' AND ', $wherearr);
        $sql .= ' limit ' . $start . ',' . $pagesize;
        return $this->db->query($sql)->list_array();
    }

    /**
     * 获取用户回答过的问题列表记录数
     * @param array $param
     * @return list
     */
    public function getaskcountbyanswers($param) {
        $count = 0;
        $sql = 'SELECT count(*) count FROM ebh_askquestions q ' .
                'LEFT JOIN ebh_askanswers a ON (q.qid = a.qid) ' .
                'LEFT JOIN ebh_users u ON (u.uid = q.uid) ';
        $wherearr = array();
        if (!empty($param['crid']))
            $wherearr[] = 'q.crid=' . $param['crid'];
        if (!empty($param['uid']))
            $wherearr[] = 'a.uid=' . $param['uid'];
		if(!empty($param['startDate']))
			$wherearr[] = 'a.dateline>='.$param['startDate'];
		if(!empty($param['endDate']))
			$wherearr[] = 'a.dateline<'.$param['endDate'];
        if (!empty($param['q']))
            $wherearr[] = '(q.title like \'%' . $this->db->escape_str($param['q']) . '%\' or u.username like \'%' . $this->db->escape_str($param['q']) . '%\')';
        if (!empty($wherearr))
            $sql.= ' WHERE ' . implode(' AND ', $wherearr);

        $countrow = $this->db->query($sql)->row_array();
        if (!empty($countrow) && !empty($countrow['count']))
            $count = $countrow['count'];
        return $count;
    }

    /**
     * 获取用户关注的问题列表
     * @param array $param
     * @return list
     */
    public function getasklistbyfavorit($param) {
        if (empty($param['page']) || $param['page'] < 1)
            $page = 1;
        else
            $page = $param['page'];
        $pagesize = empty($param['pagesize']) ? 30 : $param['pagesize'];
        $start = ($page - 1) * $pagesize;
        $sql = 'SELECT q.qid,q.title,u.username,u.realname,u.groupid,f.foldername as catname,q.dateline as date,0 as answerdate,q.answercount,q.viewnum,q.hasbest FROM ebh_askquestions q ' .
                'LEFT JOIN ebh_askfavorites a ON (q.qid = a.qid) ' .
                'LEFT JOIN ebh_users u ON (u.uid = q.uid) ' .
                'LEFT JOIN ebh_folders f on (q.folderid = f.folderid) ';
        $wherearr = array();
        if (!empty($param['crid']))
            $wherearr[] = 'q.crid=' . $param['crid'];
        if (!empty($param['uid']))
            $wherearr[] = 'a.uid=' . $param['uid'];
        if (!empty($param['q']))
            $wherearr[] = '(q.title like \'%' . $this->db->escape_str($param['q']) . '%\' or u.username like \'%' . $this->db->escape_str($param['q']) . '%\')';
        if (!empty($wherearr))
            $sql.= ' WHERE ' . implode(' AND ', $wherearr);
        $sql .= ' limit ' . $start . ',' . $pagesize;
        return $this->db->query($sql)->list_array();
    }

    /**
     * 获取用户回答过的问题列表记录数
     * @param array $param
     * @return list
     */
    public function getaskcountbyfavorit($param) {
        $count = 0;
        $sql = 'SELECT count(*) count FROM ebh_askquestions q ' .
                'LEFT JOIN ebh_askfavorites a ON (q.qid = a.qid) ' .
                'LEFT JOIN ebh_users u ON (u.uid = q.uid) ';
        $wherearr = array();

        if (!empty($param['crid']))
            $wherearr[] = 'q.crid=' . $param['crid'];
        if (!empty($param['uid']))
            $wherearr[] = 'a.uid=' . $param['uid'];
        if (!empty($param['q']))
            $wherearr[] = '(q.title like \'%' . $this->db->escape_str($param['q']) . '%\' or u.username like \'%' . $this->db->escape_str($param['q']) . '%\')';
        if (!empty($wherearr))
            $sql.= ' WHERE ' . implode(' AND ', $wherearr);

        $countrow = $this->db->query($sql)->row_array();
        if (!empty($countrow) && !empty($countrow['count']))
            $count = $countrow['count'];
        return $count;
    }

    /**
     * 添加我的关注
     * @param array $param
     * @return int 影响行数
     */
    public function addfavorit($param) {
        $setarr = array('qid' => $param['qid'], 'uid' => $param['uid'], 'dateline' => SYSTIME);
        $afrows = $this->db->insert('ebh_askfavorites', $setarr);
        return $afrows;
    }

    /**
     * 删除我的关注
     * @param array $param
     * @return int 影响行数
     */
    public function delfavorit($param) {
        $wherearr = array();
        if (!empty($param['uid']) && !empty($param['aid'])) {
            $wherearr['uid'] = $param['uid'];
            $wherearr['aid'] = $param['aid'];
        } else if (!empty($param['uid']) && !empty($param['qid'])) {
            $wherearr['uid'] = $param['uid'];
            $wherearr['qid'] = $param['qid'];
        }
        $afrows = $this->db->delete('ebh_askfavorites', $wherearr);
        return $afrows;
    }

    /**
     * 添加感谢
     * @param int $qid
     * @return int
     */
    public function addthank($qid) {
        $wherearr = array('qid' => $qid);
        $setarr = array('thankcount' => 'thankcount+1');
        $afrows = $this->db->update('ebh_askquestions', array(), $wherearr, $setarr);
        return $afrows;
    }

    /**
     * 添加对回答的感谢
     */
    function addthankanswer($param) {
        $setarr = array('thankcount' => 'thankcount+1');
        $wherearr = array('aid' => $param['aid'], 'qid' => $param['qid']);
        $afrows = $this->db->update('ebh_askanswers', array(), $wherearr, $setarr);
        return $afrows;
    }

    /**
     * 添加回答
     */
    function addanswer($param = array()) {
        if (empty($param) || empty($param['qid']) || empty($param['uid']))
            return false;
        $user = Ebh::app()->user->getloginuser();
        if(empty($user)){
            return false;
        }
        if($user['groupid'] == 6){
            $sql = 'select status from ebh_askquestions where qid='.$param['qid'];
            $res = $this->db->query($sql)->row_array();
            if($res['status'] == 1)
                return false;
        }
        $setarr = array();
        $setarr['qid'] = $param['qid'];
        $setarr['uid'] = $param['uid'];
        if (!empty($param ['message'])) {
            $setarr['message'] = $param['message'];
        }
        if (!empty($param ['audioname'])) {
            $setarr['audioname'] = $param['audioname'];
        }
        if (!empty($param ['audiosrc'])) {
            $setarr['audiosrc'] = $param['audiosrc'];
        }
        if (!empty($param ['imagename'])) {
            $setarr['imagename'] = $param['imagename'];
        }
        if (!empty($param ['imagesrc'])) {
            $setarr['imagesrc'] = $param['imagesrc'];
        }
        if (!empty($param ['attname'])) {
            $setarr['attname'] = $param['attname'];
        }
        if (!empty($param ['attsrc'])) {
            $setarr['attsrc'] = $param['attsrc'];
        }
        if (!empty($param ['fromip'])) {
            $setarr['fromip'] = $param['fromip'];
        }
        $setarr['dateline'] = SYSTIME;
        $aid = $this->db->insert('ebh_askanswers', $setarr);
        if ($aid) {
            $this->updateanswercount($param['qid']);
            return $aid;
        } else {
            return 0;
        }
    }
	/**
     * 修改回答
     */
    function updateanswer($param = array()) {
        if (empty($param) || empty($param['qid']) || empty($param['aid']) || empty($param['uid']))
            return false;
		$sql = 'select status from ebh_askquestions where qid='.$param['qid'];
		$wherearr = array('qid' => $param['qid'], 'uid' => $param['uid'], 'aid'=>$param['aid']);
        $setarr = array();
        if (!empty($param ['message'])) {
            $setarr['message'] = $param['message'];
        }
        if (!empty($param ['audioname'])) {
            $setarr['audioname'] = $param['audioname'];
        }
        if (!empty($param ['audiosrc'])) {
            $setarr['audiosrc'] = $param['audiosrc'];
        }
        if (!empty($param ['imagename'])) {
            $setarr['imagename'] = $param['imagename'];
        }
        if (!empty($param ['imagesrc'])) {
            $setarr['imagesrc'] = $param['imagesrc'];
        }
        if (!empty($param ['attname'])) {
            $setarr['attname'] = $param['attname'];
        }
        if (!empty($param ['attsrc'])) {
            $setarr['attsrc'] = $param['attsrc'];
        }
		$afrows = $this->db->update('ebh_askanswers', $setarr, $wherearr);
        return $afrows;
    }

    /**
     * 删除答案
     */
    function delanswer($param = array()) {
        if (empty($param) || empty($param['qid']) || empty($param['uid']) || empty($param['aid']))
            return false;
        $wherearr = array('aid' => $param['aid'], 'qid' => $param['qid'], 'uid' => $param['uid']);
        $afrows = $this->db->delete('ebh_askanswers', $wherearr);
        if ($afrows > 0) {
            $this->updateanswercount($param['qid'], -1);
        }
        return $afrows;
    }

    /**
     * 更新问题的回答数
     * @param type $qid
     * @param type $count
     * @return type
     */
    public function updateanswercount($qid, $count = 1) {
        $setarr = array('answercount' => 'answercount + ' . $count);
        $wherearr = array('qid' => $qid);
        $afrows = $this->db->update('ebh_askquestions', array(), $wherearr, $setarr);
        return $afrows;
    }

    /**
     * 根据时间获取该平台学生最新的问题数
     * @param type $crid
     * @param type $time
     * @return type
     */
    public function getnewaskcountbytime($crid, $time) {
        $count = 0;
        $sql = "SELECT COUNT(*) count FROM ebh_askquestions q  " .
                "WHERE q.crid=$crid AND q.dateline > $time";
        $row = $this->db->query($sql)->row_array();
        if (!empty($row))
            $count = $row['count'];
        return $count;
    }

	/**
	*课件的答疑查询详情
	*/
	//getaskcourse  
	public function getasklistwithfavorite($param){

		$sql = 'SELECT q.qid,q.crid,q.folderid,q.catid,q.grade,q.catpath,q.uid,q.title,q.message,q.imagename,q.imagesrc,q.audioname,q.audiosrc,q.answercount,q.thankcount,q.hasbest,q.status,q.dateline,q.viewnum,u.username,u.realname,fa.aid FROM ebh_askquestions q '
		.'left join ebh_users u on (q.uid=u.uid) '
		.'left join ebh_askfavorites fa on (q.qid = fa.qid and fa.uid = '.$param['auid'].')' ;

		$wherearr = array();
		 if (isset($param['crid']))
            $wherearr[] = 'q.crid=' . $param['crid'];
        if (!empty($param['uid']))
            $wherearr[] = 'q.uid=' . $param['uid'];
		if (!empty($param['catid']))
            $wherearr[] = 'q.catid =' . $param['catid'];
		if (!empty($param['catidlist']))
            $wherearr[] = 'q.catid in(' . $param['catidlist'].')';
		if (isset($param['folderid']))
            $wherearr[] = 'q.folderid=' . $param['folderid'];
		if (!empty($param['grade']))
            $wherearr[] = 'q.grade =' . $param['grade'];
        if (!empty($param['q']))
            $wherearr[] = '(q.title like \'%' . $this->db->escape_str($param['q']) . '%\' or u.username like \'%' . $this->db->escape_str($param['q']) . '%\')';
		if(!empty($wherearr)) {
		$sql .= ' WHERE '.implode(' AND ', $wherearr);
		}
        if(!empty($param['order'])) {
            $sql .= ' ORDER BY '.$param['order'];
        } else {
            $sql .= ' ORDER BY q.qid desc';
        }
        if(!empty($param['limit'])) {
            $sql .= ' limit '. $param['limit'];
        } else {
			if (empty($param['page']) || $param['page'] < 1)
				$page = 1;
			else
				$page = $param['page'];
			$pagesize = empty($param['pagesize']) ? 30 : $param['pagesize'];
			$start = ($page - 1) * $pagesize;
            $sql .= ' limit ' . $start . ',' . $pagesize;
        }
//echo $sql;
		return $this->db->query($sql)->list_array();
	}
	/**
	*查询单条答疑详情
	*@qid 答疑id
	*/
	public function getaskcoursebycwid($qid){
		$sql = 'SELECT q.qid,q.crid,q.folderid,q.catid,q.grade,q.catpath,q.uid,q.title,q.message,q.imagename,q.imagesrc,q.audioname,q.audiosrc,q.answercount,q.thankcount,q.hasbest,q.status,q.dateline,q.viewnum,u.username,fa.aid FROM ebh_askquestions q '
		.'left join ebh_users u on (q.uid=u.uid) '
		.'left join ebh_askfavorites fa on (q.qid = fa.qid) where q.qid = '.$qid;
		return $this->db->query($sql)->row_array();
	}

	/*
	 * 答疑数量
     * @param type $param
     * @return type
	 */
	public function getaskcount($param){
		$sql = 'SELECT count(1) coun FROM ebh_askquestions q left join ebh_users u on (q.uid=u.uid) ';
		$wherearr = array();
		if(!empty($param['crid'])){
			$wherearr[] = ' q.crid ='.$param['crid'];
		}
		if(!empty($param['folderid'])){
			$wherearr[] = ' q.folderid ='.$param['folderid'];
		}
		if(!empty($param['q'])){
			$wherearr[] = '(q.title like \'%' . $this->db->escape_str($param['q']) . '%\' or u.username like \'%' . $this->db->escape_str($param['q']) . '%\')';
		}
		if(!empty($wherearr)) {
            $sql .= ' WHERE '.implode(' AND ',$wherearr);
        }
		$row = $this->db->query($sql)->row_array();
        if (!empty($row))
            $count = $row['coun'];
        return $count;
	}

    /**
     * 获取平台下最新的问题
     * @param type $param
     * @return type
     */
    public function getnewasklistbycrid($param) {
        $sql = 'SELECT q.qid,q.crid,q.folderid,q.uid,q.title,q.dateline,f.foldername,u.username,u.realname FROM ebh_askquestions q ' .
                'LEFT JOIN ebh_folders f on (q.folderid = f.folderid) ' .
                'JOIN ebh_users u on (q.uid = u.uid) ';
        $wherearr = array();
        if (!empty($param['crid']))
            $wherearr[] = 'q.crid = ' . $param['crid'];
        if (!empty($wherearr))
            $sql .= ' WHERE ' . implode(' AND ', $wherearr);
        if (!empty($param['order']))
            $sql .= ' ORDER BY ' . $param['order'];
        else
            $sql .= ' ORDER BY q.qid DESC ';
        if (!empty($param['limit']))
            $sql .= ' LIMIT ' . $param['limit'];
        else
            $sql .= ' LIMIT 0,5 ';
        return $this->db->query($sql)->list_array();
    }
	
	/**
	*热门问题查询(回答数排序)
	*/
	public function getquestionhot($param){
		$sql = 'select q.title,q.qid from ebh_askquestions q ';
        if (!empty($param['order']))
            $sql .= ' ORDER BY ' . $param['order'];
        else
            $sql .= ' ORDER BY q.qid DESC ';
        if (!empty($param['limit']))
            $sql .= ' LIMIT ' . $param['limit'];
        else
            $sql .= ' LIMIT 0,5 ';
		return $this->db->query($sql)->list_array();
	}
	 /**
     * 获取question列表
     * @param array $param 条件参数
     * @return array questionlist
     */
    public function getquestion($param = array()) {
		$sql = 'SELECT q.qid,q.crid,q.folderid,q.catid,q.grade,q.catpath,q.uid,q.title,q.message,q.imagename,q.imagesrc,q.audioname,q.audiosrc,q.answercount,q.thankcount,q.hasbest,q.status,q.dateline,q.viewnum,f.foldername,u.username,fa.aid,cl.crname from ebh_askquestions q '
		.'left join ebh_folders f on (q.folderid = f.folderid) '
		.'left join ebh_users u on (q.uid=u.uid) '
		.'left join ebh_askfavorites fa on (q.qid = fa.qid) '
		.'left join ebh_classrooms cl on (q.crid = cl.crid) ';

        $wherearr = array();
		if(isset($param ['title'])){
			$wherearr[] = ' (q.title like \'%'.$param['title'].'%\' or u.username like \'%'.$param['title'].'%\') ';
		}
		if(isset($param ['folderid'])){
			$wherearr[] = 'q.folderid = '.intval($param['folderid']);
		}
        if(!empty($wherearr)) {
            $sql .= ' WHERE '.implode(' AND ',$wherearr);
        }
        if(!empty($param['order'])) {
            $sql .= ' ORDER BY '.$param['order'];
        } else {
            $sql .= ' ORDER BY q.dateline desc';
        }
        if(!empty($param['limit'])) {
            $sql .= ' limit '. $param['limit'];
        } else {
            $sql .= ' limit 0,10';
        }
        return $this->db->query($sql)->list_array();
	}

//	//答疑数量
//	  public function questioncount($quesarr = array()) {
//        $count = 0;
//        $sql = 'SELECT count(*) count from ebh_askquestions q '.
//                'JOIN ebh_coursewares cw ON r.cwid = cw.cwid ';
//        $sql .= ' WHERE r.folderid='.$quesarr['folderid'];
//        if(!empty($quesarr['q']))
//            $sql .= ' AND cw.title like \'%'.$this->db->escape_str($quesarr['q']).'%\'';
//        $countrow = $this->db->query($sql)->row_array();
//        if(!empty($countrow))
//            $count = $countrow['count'];
//        return $count;
//    }

	
	/**
     * 根据分类获取全部问题列表
     * @param type $param
     * @return type
     */
    public function getasklistbycatid($param) {
        if (empty($param['page']) || $param['page'] < 1)
            $page = 1;
        else
            $page = $param['page'];
        $pagesize = empty($param['pagesize']) ? 30 : $param['pagesize'];
        $start = ($page - 1) * $pagesize;
        $sql = 'select q.qid,q.crid,q.uid,q.catid,q.title,q.answercount,q.hasbest,q.dateline,q.viewnum,q.message,q.thankcount,u.username,u.realname from ebh_askquestions q join ebh_users u on (q.uid = u.uid) ';
        $wherearr = array();
        if (isset($param['crid']))
            $wherearr[] = 'q.crid=' . $param['crid'];
        if (!empty($param['uid']))
            $wherearr[] = 'q.uid=' . $param['uid'];
		if (!empty($param['catid']))
            $wherearr[] = 'q.catid =' . $param['catid'];
		if (!empty($param['catidlist']))
            $wherearr[] = 'q.catid in(' . $param['catidlist'].')';
		if (!empty($param['grade']))
            $wherearr[] = 'q.grade =' . $param['grade'];
        if (!empty($param['q']))
            $wherearr[] = '(q.title like \'%' . $this->db->escape_str($param['q']) . '%\' or u.username like \'%' . $this->db->escape_str($param['q']) . '%\')';
        if (!empty($wherearr))
            $sql.= ' WHERE ' . implode(' AND ', $wherearr);
        if (!empty($param['order'])) {
            $sql .= ' order by ' . $param['order'];
        } else {
            $sql .= ' order by q.qid desc ';
        }
        $sql .= ' limit ' . $start . ',' . $pagesize;
        return $this->db->query($sql)->list_array();
    }
    /**
     * 根据分类获取全部问题列表记录总数
     * @param type $param
     * @return type
     */
    public function getasklistcountbycatid($param) {
        $count = 0;
        $sql = 'select count(*) count from ebh_askquestions q join ebh_users u on (q.uid = u.uid) ';
        $wherearr = array();
        if (!empty($param['crid']))
            $wherearr[] = 'q.crid=' . $param['crid'];
        if (!empty($param['uid']))
            $wherearr[] = 'q.uid=' . $param['uid'];
		if (!empty($param['catid']))
            $wherearr[] = 'q.catid =' . $param['catid'];
		if (!empty($param['catidlist']))
            $wherearr[] = 'q.catid in(' . $param['catidlist'].')';
		if (!empty($param['grade']))
            $wherearr[] = 'q.grade =' . $param['grade'];
        if (!empty($param['q']))
            $wherearr[] = '(q.title like \'%' . $this->db->escape_str($param['q']) . '%\' or u.username like \'%' . $this->db->escape_str($param['q']) . '%\')';
        if (!empty($wherearr))
            $sql.= ' WHERE ' . implode(' AND ', $wherearr);
        $row = $this->db->query($sql)->row_array();
        if (!empty($row))
            $count = $row['count'];
        return $count;
    }
	
	/*
	设为最佳答案
	@param array $param uid,qid,aid
	*/
	public function setBest($param){
		if(empty($param['uid'])||empty($param['qid'])||empty($param['aid']))
			return false;
		$sql = 'select count(*) count 
			from ebh_askquestions q 
			join ebh_askanswers a on q.qid=a.qid';
		$warr = array();
		$warr[]= 'q.uid='.$param['uid'];
		$warr[]= 'q.qid='.$param['qid'];
		$warr[]= 'q.hasbest=0';
		$warr[]= 'a.aid='.$param['aid'];
		$sql.= ' where '.implode(' AND ',$warr);
		$count = $this->db->query($sql)->row_array();
		if($count['count']>0){
			$qarr['hasbest'] = 1;
			$qarr['status'] = 1;
			$wherearr['qid'] = $param['qid'];
			$afrow = $this->db->update('ebh_askquestions',$qarr,$wherearr);
			$aarr['isbest'] = 1;
			$wherearr2['aid'] = $param['aid'];
			$afrow = $this->db->update('ebh_askanswers',$aarr,$wherearr2);
			return $afrow;
		}else{
			return false;
		}
	}

	/**
	*求的答疑最新动态
	*/
	public function getaskanswers(){
		$sql = 'SELECT a.aid,a.qid,q.uid,u.username as qr,us.username as wr FROM ebh_askanswers a '
		.'left join ebh_askquestions q on (a.qid = q.qid) '
		.'left join ebh_users u on (q.uid = u.uid) '
		.'left join ebh_users us on (us.uid = a.uid) where u.username is not null order by a.dateline desc LIMIT 0,5';
        return $this->db->query($sql)->list_array();
	}
	/*
	*我的答疑的访问数
	*/
	 public function addviewnum($qid, $num = 1) {
	//	 echo $qid;exit;
        $where = 'qid=' . $qid;
        $setarr = array('viewnum' => 'viewnum+' . $num);
        $this->db->update('ebh_askquestions', array(), $where, $setarr);
    }

    /**
     *获取教师所属课程的问题列表
     *
    */
    public function getcoursequestionslist($folderids = array(),$param = array()){
        if(empty($folderids)){
            return array();
        }
        if (empty($param['page']) || $param['page'] < 1)
            $page = 1;
        else
            $page = $param['page'];
        $pagesize = empty($param['pagesize']) ? 30 : $param['pagesize'];
        $start = ($page - 1) * $pagesize;
        $in = implode(',',$folderids);
        $sql = 'select a.qid,a.dateline,a.title,a.answercount,q.viewnum,u.uid,u.username,u.realname,f.foldername from ebh_askquestions a  left join ebh_users u on a.uid = u.uid left join ebh_folders f on f.folderid = a.folderid where a.folderid in ('.$in.')';
        if(!empty($param['q'])){
            $sql.= ' AND a.title like \'%'.$param['q'].'%\'';
        }
        $sql .= ' limit ' . $start . ',' . $pagesize;
        return $this->db->query($sql)->list_array();
    }

    /**
     *获取教师所属课程的问题个数
     *
    */
    public function getcoursequestionscount($folderids = array(),$param = array()){
        if(empty($folderids)){
            return 0;
        }
        $in = implode(',',$folderids);
        $sql = 'select count(*) count from ebh_askquestions a  where a.folderid in ('.$in.')';
        if(!empty($param['q'])){
            $sql.= ' AND a.title like \'%'.$param['q'].'%\'';
        }
        $res = $this->db->query($sql)->row_array();
        return $res['count'];
    }

    /**
     *获取老师所教班级问题
     *
     */
    public function getClassesAsk($classids,$param=array(),$folderids){
        if(empty($classids)){
            return array();
        }
        if (empty($param['page']) || $param['page'] < 1)
            $page = 1;
        else
            $page = $param['page'];
        $pagesize = empty($param['pagesize']) ? 30 : $param['pagesize'];
        $start = ($page - 1) * $pagesize;
        $in = implode(',',$classids);
        $in2 = implode(',',$folderids);
        $sql = 'select aq.qid,aq.dateline,aq.title,aq.answercount,q.viewnum,u.username,u.realname,u.uid,f.foldername from ebh_askquestions aq 
                join ebh_users u on u.uid = aq.uid
                join ebh_folders f on f.folderid = aq.folderid
                join ebh_classstudents ct on aq.uid = ct.uid where ct.classid in ('.$in.')';
        if(!empty($folderids)){
            $sql.=' AND f.folderid in('.$in2.') ';
        }
        if(!empty($param['q'])){
            $sql.= ' AND aq.title like \'%'.$param['q'].'%\'';
        }
        $sql .= ' limit ' . $start . ',' . $pagesize;
        return $this->db->query($sql)->list_array();
    }
    /**
     *获取老师所教班级问题数量
     *
     */
    public function getClassesAskCount($classids,$param=array(),$folderids){
        if(empty($classids)){
            return 0;
        }
      
        $in = implode(',',$classids);
        $in2 = implode(',',$folderids);
        $sql = 'select count(*) count from ebh_askquestions aq 
                join ebh_users u on u.uid = aq.uid
                join ebh_folders f on f.folderid = aq.folderid
                join ebh_classstudents ct on aq.uid = ct.uid where ct.classid in ('.$in.')';
        if(!empty($folderids)){
            $sql.=' AND f.folderid in('.$in2.') ';
        }       
        if(!empty($param['q'])){
            $sql.= ' AND aq.title like \'%'.$param['q'].'%\'';
        }
        $res = $this->db->query($sql)->row_array();
        return $res['count'];
    }

     public function getdetailaskbyqid($qid, $uid = 0) {
        if ($uid > 0) {
            $sql = 'select q.qid,q.shield,q.crid,q.uid,q.title,q.message,q.reqid,u.username,u.realname,q.folderid,f.foldername,q.audioname,q.audiosrc,q.imagename,q.imagesrc,q.answercount,q.thankcount,q.hasbest,q.`status`,q.dateline,q.viewnum,q.attname,q.attsrc,q.catpath,q.tid,af.aid,q.catid,q.grade,q.shield,u.sex,u.face,u.groupid,q.cwid,q.cwname,q.audiotime from ebh_askquestions q ' .
                    'join ebh_users u on (u.uid = q.uid) ' .
                    'left join ebh_folders f on (f.folderid = q.folderid) ' .
                    'left join ebh_askfavorites af on (af.qid = q.qid and af.uid=' . $uid . ') ' .
                    'where q.qid=' . $qid ;
        } else {
            $sql = 'select q.qid,q.shield,q.crid,q.uid,q.title,q.message,q.reqid,u.username,u.realname,q.folderid,f.foldername,q.audioname,q.audiosrc,q.imagename,q.imagesrc,q.answercount,q.thankcount,q.hasbest,q.`status`,q.dateline,q.viewnum,q.attname,q.attsrc,q.catpath,q.tid,q.shield,u.groupid,q.cwname,q.audiotime from ebh_askquestions q ' .
                    'join ebh_users u on (u.uid = q.uid) ' .
                    'left join ebh_folders f on (f.folderid = q.folderid) ' .
                    'where q.qid=' . $qid;
        }
        return $this->db->query($sql)->row_array();
    }

    /**
     *获取问题的人气
     */
    public function getviewnum($qid = 0){
        $sql = 'select viewnum from ebh_askquestions where qid = '.$qid.' limit 1';
        return $this->db->query($sql)->row_array();
    }
    /**
     *设置人气
     */
    public function setviewnum($qid, $num = 1) {
        $where = 'qid =' . $qid;
        $setarr = array('viewnum' => $num);
        $this->db->update('ebh_askquestions', array(), $where, $setarr);
    }
     /**
     *获取需要指定老师回答的问题列表
     */
    public function get_required_ask($param = array()){
        $sql = 'select q.qid,q.title,q.uid,q.folderid,q.dateline,q.answered,q.answercount,q.viewnum,q.hasbest,f.foldername,q.imagesrc,q.message from ebh_askquestions q left join ebh_folders f on q.folderid = f.folderid';
        $wherearr = array();
        if(!empty($param['tid'])){
            $wherearr[] = 'q.tid = '.$param['tid'];
        }
        if(!empty($param['crid'])){
            $wherearr[] = 'q.crid = '.$param['crid'];
        }
        if(isset($param['shield'])){
            $wherearr[] = 'q.shield = '.$param['shield'];
        }
        $sql.= ' where '.implode(' AND ',$wherearr);
        if(!empty($param['order'])){
            $sql .= ' order by '.$param['order'];
        }else{
            $sql .= ' order by q.answered asc,q.qid desc';
        }
        if(!empty($param['limit'])) {
            $sql .= ' limit '.$param['limit'];
        } else {
            if (empty($param['page']) || $param['page'] < 1)
                $page = 1;
            else
                $page = $param['page'];
            $pagesize = empty($param['pagesize']) ? 30 : $param['pagesize'];
            $start = ($page - 1) * $pagesize;
            $sql .= ' limit ' . $start . ',' . $pagesize;
        }
        return $this->db->query($sql)->list_array();
    }

    /**
     *获取教师所属课程的问题列表
     *
    */
    public function get_folder_ask($folderids = array(),$param = array()){
        if(empty($folderids)){
            return array();
        }
        if (empty($param['page']) || $param['page'] < 1)
            $page = 1;
        else
            $page = $param['page'];
        $pagesize = empty($param['pagesize']) ? 30 : $param['pagesize'];
        $start = ($page - 1) * $pagesize;
        $in = implode(',',$folderids);
        $sql = 'select q.qid,q.dateline,q.title,q.answercount,q.viewnum,q.hasbest,q.uid,f.foldername,q.imagesrc,q.message from ebh_askquestions q left join ebh_folders f on f.folderid = q.folderid where q.folderid in ('.$in.')';
        if(isset($param['shield'])){
            $sql .= ' AND q.shield = '.$param['shield'];
        }
        if(!empty($param['q'])){
            $sql.= ' AND q.title like \'%'.$param['q'].'%\'';
        }
        if(!empty($param['order'])){
            $sql .= ' order by '.$param['order'];
        }else{
            $sql .= ' order by q.qid desc';
        }
        $sql .= ' limit ' . $start . ',' . $pagesize;
        return $this->db->query($sql)->list_array();
    }

     /**
     * 设置老师是否回答问题
     * @param int $qid 问题编号
     */
    public function setAnswered($qid,$status=0) {
        if(empty($qid)){
            return 0;
        }
        $where = array('qid'=>$qid);
        $setarr = array('answered'=>$status);
        return $this->db->update('ebh_askquestions',$setarr,$where);
    }
    /**
     * 获取提问、答疑达人榜数据(前4条数据)
     */
    public function gettopaskandanswer($param){
    	if(!empty($param['crid'])){
	    	$sql = " select temp.uid, SUM(temp.num) tnum from (select a.uid, count(*) as num from ebh_askquestions a where a.crid = ".$param['crid'];
	    	$sql .= " group by a.uid union all select b.uid, count(*) as num from ebh_askanswers b where b.uid in ( select distinct uid from ebh_roomusers where crid = ".$param['crid']." ) group by b.uid 
	        ) temp group by temp.uid order by tnum desc limit 4 ";
    	}else{
    		$sql = " select temp.uid, SUM(temp.num) tnum from (select a.uid, count(*) as num from ebh_askquestions a where a.crid >0 ";
    		$sql .= " group by a.uid union all select b.uid, count(*) as num from ebh_askanswers b group by b.uid) temp group by temp.uid order by tnum desc limit 4 ";
    	}
    	return $this->db->query($sql)->list_array();
    }
}

?>