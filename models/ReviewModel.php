<?php 
    //评论类
class ReviewModel extends CModel{
    /*
    评论列表
    @param array $param
    @return array
    */
    public function getreviewlist($param){
        $sql = ' select r.logid,r.subject,u.username,r.good,r.useful,r.bad,r.dateline,r.fromip,r.type,u.sex,u.face,u.groupid,r.score,r.subject from ebh_reviews r left join ebh_users u on r.uid = u.uid';
        $setarr = array();
        if(!empty($param['q']))
            $wherearr[] = ' (r.subject like \'%'. $this->db->escape_str($param['q']) .'%\' or u.username like \'%' . $this->db->escape_str($param['q']) .'%\')';
        if (!empty($param['type']))
            $wherearr[] = 'r.type = \''.$param['type'].'\'';
        if (!empty($param['toid']))
            $wherearr[] = 'toid = '.$param['toid'];
        if (!empty($param['opid']))
            $wherearr[] = 'opid = '.$param['opid'];
        if (!empty($param['upid']))
            $wherearr[] = 'r.upid = '.$param['upid'];
        if(!empty($wherearr))
            $sql.= ' WHERE '.implode(' AND ',$wherearr);
        if(!empty($param['order']))
            $sql.= ' order by ' .$param['order'];
        else
            $sql.= ' order by r.logid desc';
        if(!empty($param['limit']))
            $sql.= ' limit ' . $param['limit'];
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
    /*
    评论数量
    @param array $param
    @return int
    */
    public function getreviewcount($param){
        if(!empty($param['rev'])){//老师回复评论
            $sql = 'select count(*) count from ebh_reviews r join ebh_users u on (u.uid = r.uid) join ebh_coursewares c on (c.cwid = r.toid) join ebh_roomcourses rc on (rc.cwid = c.cwid) join ebh_teachers t on (t.teacherid = c.uid ) ';

        }else if(!empty($param['rcrid'])){//用于学生的评论列表
            $sql = 'select count(*) count from ebh_reviews r join ebh_users u  on u.uid=r.uid join ebh_coursewares c on (c.cwid = r.toid) join ebh_roomcourses rc on (rc.cwid = c.cwid) join ebh_teachers t on (t.teacherid = c.uid )';
        }else{
            $sql = 'select count(*) count from ebh_reviews r left join ebh_users u  on u.uid=r.uid  ';
        }
        if(!empty($param['q'])){
            if(!empty($param['rev'])){//查询老师的真实姓名及评论
                $wherearr[] = ' (r.subject like \'%'. $this->db->escape_str($param['q']) .'%\' or t.realname like \'%' . $this->db->escape_str($param['q']) .'%\')';
            }else{
                $wherearr[] = ' (r.subject like \'%'. $this->db->escape_str($param['q']) .'%\' or u.username like \'%' . $this->db->escape_str($param['q']) .'%\')';    
            }
        }
        if (!empty($param['crid'])) {
            $wherearr[] = 'rc.crid ='.$param['crid'];
        }
        if (!empty($param['type']))
            $wherearr[] = 'r.type = \''.$param['type'].'\'';
        if (!empty($param['toid']))
            $wherearr[] = 'toid = '.$param['toid'];
        if (!empty($param['opid']))
            $wherearr[] = 'opid = '.$param['opid'];
        if (!empty($param['upid']))
            $wherearr[] = 'r.upid = '.$param['upid'];
        if (!empty($param['uid']))
            $wherearr[] = 'r.uid = '.$param['uid'];
        if (isset($param['status']))
            $wherearr[] = 'c.status = '.$param['status'];
        if (isset($param['shield']))
            $wherearr[] = 'r.shield != '.$param['shield'];
        if (isset($param['replysubject']))
            $wherearr[] = 'r.replysubject != \'\'';
        if(!empty($wherearr))
            $sql.= ' WHERE '.implode(' AND ',$wherearr);
        $count = $this->db->query($sql)->row_array();
        return $count['count'];
    }
    /*
    删除评论
    @param int $logid
    @return bool
    */
    public function deletereview($logid){
        return $this->db->delete('ebh_reviews','logid='.$logid);
        // $sql = 'delete r.* from ebh_reviews r where r.logid='.$logid;
        // return $this->db->simple_query($sql);
    }

    /**
     * 插入评论数据
     * @param type $param
     * @return type
     */
    public function insert_bak($param = array()) {
            $setarr = array();
            if (!empty($param['upid'])) {
                $setarr['upid'] = $param['upid'];
            }
            if (isset($param['service'])) {
                $setarr['service'] = $param['service'];
            }
            if (isset($param['environment'])) {
                $setarr['environment'] = $param['environment'];
            }
            if (isset($param['score'])) {
                $setarr['score'] = $param['score'];
            }
            if (isset($param['useful'])) {
                $setarr['useful'] = $param['useful'];
            }
            if (isset($param['useless'])) {
                $setarr['useless'] = $param['useless'];
            }
            if (isset($param['viewnum'])) {
                $setarr['viewnum'] = $param['viewnum'];
            }
            if (isset($param['replynum'])) {
                $setarr['replynum'] = $param['replynum'];
            }
            if (!empty($param['subject'])) {
                $setarr['subject'] = $param['subject'];
            }
            if (isset($param['good'])) {
                $setarr['good'] = $param['good'];
            }
            if (isset($param['bad']) ) {
                $setarr['bad'] = $param['bad'];
            }
            if (!empty($param['type'])) {
                $setarr['type'] = $param['type'];
            }
            if (!empty($param['uid'])) {
                $setarr['uid'] = $param['uid'];
            }
            if (!empty($param['levels'])) {
                $setarr['levels'] = $param['levels'];
            }
            if (!empty($param['toid'])) {
                $setarr['toid'] = $param['toid'];
            }
            if (!empty($param['opid'])) {
                $setarr['opid'] = $param['opid'];
            }
            if (!empty($param['fromip'])) {
                $setarr['fromip'] = $param['fromip'];
            }
            if (!empty($param['dateline'])) {
                $setarr['dateline'] = $param['dateline'];
            }
             $logid = $this->db->insert('ebh_reviews', $setarr);
        return $logid;
    }
     /**
     * 插入评论数据
     * @param type $param
     * @return type
     */
    public function insert($param = array()) {
            $setarr = array();
            if (!empty($param['upid'])) {
                $setarr['upid'] = $param['upid'];
            }
            if (isset($param['service'])) {
                $setarr['service'] = $param['service'];
            }
            if (isset($param['environment'])) {
                $setarr['environment'] = $param['environment'];
            }
            if (isset($param['score'])) {
                $setarr['score'] = $param['score'];
            }
            if (isset($param['useful'])) {
                $setarr['useful'] = $param['useful'];
            }
            if (isset($param['useless'])) {
                $setarr['useless'] = $param['useless'];
            }
            if (isset($param['viewnum'])) {
                $setarr['viewnum'] = $param['viewnum'];
            }
            if (isset($param['replynum'])) {
                $setarr['replynum'] = $param['replynum'];
            }
            if (isset($param['subject'])) {
                $setarr['subject'] = $param['subject'];
            }
            if (isset($param['good'])) {
                $setarr['good'] = $param['good'];
            }
            if (isset($param['bad']) ) {
                $setarr['bad'] = $param['bad'];
            }
            if (!empty($param['type'])) {
                $setarr['type'] = $param['type'];
            }
            if (!empty($param['uid'])) {
                $setarr['uid'] = $param['uid'];
            }
            if (!empty($param['levels'])) {
                $setarr['levels'] = $param['levels'];
            }
            if (!empty($param['toid'])) {
                $setarr['toid'] = $param['toid'];
            }
            if (!empty($param['opid'])) {
                $setarr['opid'] = $param['opid'];
            }
            if (!empty($param['fromip'])) {
                $setarr['fromip'] = $param['fromip'];
            }
            if (!empty($param['dateline'])) {
                $setarr['dateline'] = $param['dateline'];
            }
             $logid = $this->db->insert('ebh_reviews', $setarr);
        return $logid;
    }

    /**
     * 插入ebh_logs表记录
     * @param type $param
     * @return boolean
     */
//    public function insertlog($param) {
//        $setarr = array();
//        if (empty($param['uid']) || empty($param['opid']) || empty($param['toid']) || empty($param['type']))
//            return FALSE;
//        $setarr['uid'] = $param['uid'];
//        $setarr['opid'] = $param['opid'];
//        $setarr['toid'] = $param['toid'];
//        $setarr['type'] = $param['type'];
//        if (!empty($param['subject'])) {
//            $setarr['message'] = $param['subject'];
//        }
//        if (!empty($param['value'])) {
//            $setarr['value'] = $param['value'];
//        }
//        if (!empty($param['credit'])) {
//            $setarr['credit'] = $param['credit'];
//        }
//        if (!empty($param['fromip'])) {
//            $setarr['fromip'] = $param['fromip'];
//        }
//        $setarr['dateline'] = SYSTIME;
//        $logid = $this->db->insert('ebh_logs', $setarr);
//        return $logid;
//    }

    /**
     * 根据课件编号获取评论列表
     * @param type $queryarr
     * @return boolean
     */
    public function getReviewListByCwid($queryarr = array()) {
        if (empty($queryarr['cwid']))
            return FALSE;
        if (empty($queryarr['page']) || $queryarr['page'] < 1)
            $page = 1;
        else
            $page = $queryarr['page'];
        $pagesize = empty($queryarr['pagesize']) ? 10 : $queryarr['pagesize'];
        $start = ($page - 1) * $pagesize;
        $sql = 'select r.logid,r.dateline,r.subject,r.score,r.uid,u.uid,u.username,u.realname,u.sex,u.face,u.groupid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r ' .
                'join ebh_users u on (u.uid = r.uid) ' .
                'where r.toid=' . $queryarr['cwid'] . ' and r.type=\'courseware\' and r.shield=0 order by r.logid desc ';
        $sql .= 'limit ' . $start . ',' . $pagesize;
        return $this->db->query($sql)->list_array();

    }

     /**
     * 根据用户编号获取评论列表及课件详情信息(用于评论交流)
     * @params type $params
     * @return boolean
     */
    public function getReviewListByUid($params) {
        if(!empty($params['rev'])){
             $sql = 'select  r.logid,r.subject,r.`type`,r.uid,r.`type`,c.message,r.toid,c.title,c.tag,c.summary,c.message,c.cwname,c.dateline,c.displayorder,r.score,u.realname,u.username,c.cwurl,t.realname,t.nickname,r.shield,rc.crid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) join ebh_coursewares c on (c.cwid = r.toid) join ebh_roomcourses rc on (rc.cwid = c.cwid) join ebh_teachers t on (t.teacherid = c.uid ) ';
        }else{
            $sql = 'select r.logid,r.subject,r.`type`,r.uid,r.`type`,c.message,r.toid,c.title,c.tag,c.summary,c.message,c.cwname,c.dateline,c.displayorder,r.score,u.realname,u.username,c.cwurl,t.realname,t.nickname,r.shield,u.sex,u.face,rc.crid,r.replyuid,r.replysubject,r.replydateline from ebh_reviews r join ebh_users u on (u.uid = r.uid) join ebh_coursewares c on (c.cwid = r.toid) join ebh_roomcourses rc on (rc.cwid = c.cwid) join ebh_teachers t on (t.teacherid = c.uid )';
        }
        $wherearr = array();
 
        if (!empty($params ['uid'])) {
            $wherearr[] = ' r.uid ='.$params['uid'];
        }
        if (!empty($params ['crid'])) {
            $wherearr[] = ' rc.crid ='.$params['crid'];
        }
        if (isset($params ['upid'])) {
            $wherearr[] = ' r.upid !='.$params['upid'];
        }
        if (isset($params ['shield'])) {
            $wherearr[] = ' r.shield !='.$params['shield'];
        }
        if (isset($params ['replysubject'])) {
            $wherearr[] = ' r.replysubject !=\'\' ';
        }
        if(!empty($params['q']))
            $wherearr[] = ' (r.subject like \'%'. $this->db->escape_str($params['q']) .'%\' or t.realname like \'%' . $this->db->escape_str($params['q']) .'%\')';
        if(isset($params['status'])){
            $wherearr[] = ' c.status ='.$params['status'];
        }
        $sql .= ' WHERE '.implode(' AND ', $wherearr);
        if(!empty($params['order'])) {
            $sql .= ' ORDER BY '.$params['order'];
        } else {
            $sql .= ' ORDER BY r.logid desc';
        }
        if(!empty($params['limit'])) {
            $sql .= ' limit '.$params['limit'];
        } else {
            if (empty($params['page']) || $params['page'] < 1)
                $page = 1;
            else
                $page = $params['page'];
            $pagesize = empty($params['pagesize']) ? 10 : $params['pagesize'];
            $start = ($page - 1) * $pagesize;
            $sql .= ' limit ' . $start . ',' . $pagesize;
        }   
        $reviews = $this->db->query($sql)->list_array();
        return $reviews;
    }


    /**
     * 根据课件编号获取评论列表记录数(评论数不包括账号已删除的评论)
     * @param type $queryarr
     * @return type
     */
    public function getReviewCountByCwid($queryarr = array()) {
        $count = 0;
        $sql = 'SELECT count(*) count from ebh_reviews r join ebh_users u on (u.uid = r.uid) ' .
                'where r.toid=' . $queryarr['cwid'] . ' and shield = 0 and r.type=\'courseware\'';
        $countrow = $this->db->query($sql)->row_array();
        if (!empty($countrow))
            $count = $countrow['count'];
        return $count;
    }

    /**
     * 获取平台下的评论和回复信息
     */
    public function getreviewlistbycrid($param) {

        $sql = 'select r.logid,r.upid,r.dateline,r.subject,u.username,u.realname,c.uid as authorid,c.cwid,c.title,c.cwurl,r.shield,u.uid,u.sex,u.groupid,u.face,r.replyuid,r.replysubject,r.replydateline from  ebh_reviews r ' .
                'join ebh_users u on (u.uid = r.uid) ' .
                'join ebh_coursewares c on c.cwid=r.toid ' .
                'join ebh_roomcourses rc on (c.cwid = rc.cwid) ';
        $wherearr = array();
        if (!empty($param['crid']))  //教室编号
            $wherearr[] = 'rc.crid=' . $param['crid'];
        if (!empty($param['uid']))   //教师编号
            $wherearr[] = 'c.uid=' . $param['uid'];
        if (isset($param['status']))
            $wherearr[] = 'c.status=' . $param['status'];
        // else
            // $wherearr[] = 'c.status=1';
        
        $wherearr[] = 'r.opid=8192';
        $wherearr[] = 'r.type=\'courseware\'';
        $sql .= ' WHERE ' . implode(' AND ', $wherearr);
        if (!empty($param['order']))
            $sql .= ' ORDER BY ' . $param['order'];
        else
            $sql .= ' ORDER BY r.logid DESC ';
        if (!empty($param['limit']))
            $sql .= ' LIMIT ' . $param['limit'];
        else {
            if (empty($param['page']) || $param['page'] < 1)
                $page = 1;
            else
                $page = $param['page'];
            $pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
            $start = ($page - 1) * $pagesize;
            $sql .= 'limit ' . $start . ',' . $pagesize;
        }
        $reviews = $this->db->query($sql)->list_array();
        return $reviews;
    }

    /**
     * 获取平台下的评论和回复信息记录数
     */
    public function getreviewlistcountbycrid($param) {
        $count = 0;
        $sql = 'select count(*) count from ebh_reviews r ' .
                'join ebh_coursewares c on c.cwid=r.toid ' .
                'join ebh_roomcourses rc on (c.cwid = rc.cwid) ';
        $wherearr = array();
        if (!empty($param['crid']))  //教室编号
            $wherearr[] = 'rc.crid=' . $param['crid'];
        if (!empty($param['uid']))   //教师编号
            $wherearr[] = 'c.uid=' . $param['uid'];
        if (isset($param['time']))   //时间，可根据时间来获取评论数
            $wherearr[] = 'r.dateline>' . $param['time'];
        if (isset($param['status']))
            $wherearr[] = 'c.status=' . $param['status'];
         else
             $wherearr[] = 'c.status=1';
        if (isset($param['shield']))   
            $wherearr[] = 'r.shield=' . $param['shield'];
        if (isset($param['replysubject']))
            $wherearr[] = 'r.replysubject= \'\' ';
        $wherearr[] = 'r.opid=8192';
        $wherearr[] = 'r.type=\'courseware\'';
        $sql .= ' WHERE ' . implode(' AND ', $wherearr);
        $row = $this->db->query($sql)->row_array();
        if (!empty($row))
            $count = $row['count'];
        return $count;
    }

    /**
    * 根据条件获取最后一次评论等日志的时间
    */
    public function getLastLogTime($param) {
        $lasttime = 0;
        $sql = 'select l.dateline from ebh_reviews l';
        $wherearr = array();
        if(!empty($param['logid'])) {
            $wherearr[] = 'l.logid = '.$param['logid'];
        }
        if(!empty($param['uid'])) {
            $wherearr[] = 'l.uid = '.$param['uid'];
        }
        if(!empty($param['toid'])) {
            $wherearr[] = 'l.toid = '.$param['toid'];
        }
        if(!empty($param['opid'])) {
            $wherearr[] = 'l.opid = '.$param['opid'];
        }
        if(!empty($param['value'])) {
            $wherearr[] = 'l.value = '.$param['value'];
        }
        if(!empty($param['type'])) {
            $wherearr[] = 'l.type = \''.$param['type'].'\'';
        }
        if(empty($wherearr))
            return FALSE;
        $sql .= ' WHERE '.implode(' AND ',$wherearr);
        $sql .= ' order by l.logid desc ';
        $row = $this->db->query($sql)->row_array();
        if(!empty($row))
            $lasttime = $row['dateline'];
        return $lasttime;
    }
    /**
    *获取个人评论的评分值
    */
    public function getReviewScore($params){
        $score = 0;
        $sql = ' select r.score from ebh_reviews r ';
        $wherearr = array();
        if (!empty($params['type']))
            $wherearr[] = 'r.type = \''.$params['type'].'\'';
        if (!empty($params['uid']))
            $wherearr[] = 'r.uid= '.$params['uid'];
        if (!empty($params['toid']))
            $wherearr[] = 'r.toid = '.$params['toid'];
        if (!empty($params['opid']))
            $wherearr[] = 'r.opid= '.$params['opid'];
        if (!empty($params['value']))
            $wherearr[] = 'r.value= '.$params['value'];
        if(!empty($wherearr))
            $sql.= ' WHERE '.implode(' AND ',$wherearr);
        if(!empty($params['order']))
            $sql.= ' order by '.$params['order'];
        if(!empty($params['limit']))
            $sql.= ' limit ' . $params['limit'];
        $row = $this->db->query($sql)->row_array();
        if(!empty($row)) 
            $score = $row['score'];
        return $score;
    }

     /**
     * 对评论的屏蔽(教师屏蔽该课件的学生评论)
     */
    function upShield($param) {
        $setarr = array('shield' => 1);
        $wherearr = array('logid' => $param['logid']);
        $afrows = $this->db->update('ebh_reviews', array(), $wherearr, $setarr);
        return $afrows;
        
    }

     /**
     * 回复评论
     */
    function update($param) {
        $wherearr = array('logid' => $param['logid']);
        if (!empty($param ['replyuid'])) {
            $setarr['replyuid'] = $param['replyuid'];
        }
        if (!empty($param ['replysubject'])) {
            $setarr['replysubject'] = $param['replysubject'];
        }
        if (!empty($param ['replydateline'])) {
            $setarr['replydateline'] = $param['replydateline'];
        }
        $replay = $this->db->update('ebh_reviews', $setarr, $wherearr);
        return $replay;
    }


    /**
     * 根据用户编号获取评论列表及课件详情信息(用于评论交流)
     * @params type $params
     * @return boolean
     */
    public function getReviewListForInterface($params) {
        if(!empty($params['rev'])){
             $sql = 'select  r.logid,r.subject,r.type,r.uid,c.uid as tid ,r.type,c.message,r.toid,c.title,c.tag,c.summary,c.message,c.cwname,c.dateline,c.displayorder,r.score,c.cwurl,r.shield,rc.crid,r.replyuid,r.replysubject,r.replydateline,c.ism3u8,c.status from ebh_reviews r join ebh_coursewares c on (c.cwid = r.toid) join ebh_roomcourses rc on (rc.cwid = c.cwid)';
        }else{
            $sql = 'select r.logid,r.subject,r.type,r.uid,r.type,c.message,r.toid,c.title,c.tag,c.summary,c.message,c.cwname,c.dateline,c.displayorder,r.score,c.uid as tid,c.cwurl,r.shield,rc.crid,r.replyuid,r.replysubject,r.replydateline,c.ism3u8,c.status from ebh_reviews r join ebh_coursewares c on (c.cwid = r.toid) join ebh_roomcourses rc on (rc.cwid = c.cwid)';
        }
        $wherearr = array();
 
        if (!empty($params ['uid'])) {
            $wherearr[] = ' r.uid ='.$params['uid'];
        }
        if (!empty($params ['crid'])) {
            $wherearr[] = ' rc.crid ='.$params['crid'];
        }
        if (isset($params ['upid'])) {
            $wherearr[] = ' r.upid !='.$params['upid'];
        }
        if (isset($params ['shield'])) {
            $wherearr[] = ' r.shield !='.$params['shield'];
        }
        if (isset($params ['replysubject'])) {
            $wherearr[] = ' r.replysubject !=\'\' ';
        }
        if(!empty($params['q']))
            $wherearr[] = ' (r.subject like \'%'. $this->db->escape_str($params['q']) .'%\' or t.realname like \'%' . $this->db->escape_str($params['q']) .'%\')';
        if(isset($params['status'])){
            $wherearr[] = ' c.status ='.$params['status'];
        }
        if (!empty($params ['type'])) {
            $wherearr[] = ' r.type = \''.$params['type'].'\'';
        }
        $sql .= ' WHERE '.implode(' AND ', $wherearr);
        if(!empty($params['order'])) {
            $sql .= ' ORDER BY '.$params['order'];
        } else {
            $sql .= ' ORDER BY r.logid desc';
        }
        if(!empty($params['limit'])) {
            $sql .= ' limit '.$params['limit'];
        } else {
            if (empty($params['page']) || $params['page'] < 1)
                $page = 1;
            else
                $page = $params['page'];
            $pagesize = empty($params['pagesize']) ? 10 : $params['pagesize'];
            $start = ($page - 1) * $pagesize;
            $sql .= ' limit ' . $start . ',' . $pagesize;
        }   
        $reviews = $this->db->query($sql)->list_array();
        return $reviews;
    }
}
?>
