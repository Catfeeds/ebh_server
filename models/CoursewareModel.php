<?php

/**
 * CoursewareModel 课件Model类
 */
class CoursewareModel extends CModel {

    /**
     * 根据cwid获取公告
     * @param $cwid
     * @return string
     */
    public function getNotice($cwid){
        if(empty($cwid)){
            return '';
        }

        $redis = Ebh::app()->getCache('cache_redis');
        $notice = $redis->hget('coursenotice_'.$cwid,'notice');
        if($notice){
            return $notice;
        }
        $sql = 'select notice from `ebh_coursewares` where cwid='.$cwid;
        $row = $this->db->query($sql)->row_array();
        if($row){
            return $row['notice'];
        }else{
            return '';
        }
    }
    
    /**
     * 根据课程编号或教室编号获取按照章节排名的课件列表
     * @param int $folderid 课程编号
     * @return array 课件列表数组
     */
    public function getfolderseccourselist($queryarr = array()) {
        if(empty($queryarr['folderid']) && empty($queryarr['crid']))
            return FALSE;
        if (empty($queryarr['page']) || $queryarr['page'] < 1)
            $page = 1;
        else
            $page = $queryarr['page'];
        $pagesize = empty($queryarr['pagesize']) ? 10 : $queryarr['pagesize'];
        $start = ($page - 1) * $pagesize;
        $sql = 'SELECT cw.cwid as id,cw.title as name,cw.uid,cw.dateline,cw.submitat,cw.endat,s.sid,s.sname,cw.cwurl,cw.ism3u8,ifnull(s.displayorder,10000) sdisplayorder,r.folderid as fid from ebh_roomcourses r ' .
            'JOIN ebh_coursewares cw ON r.cwid = cw.cwid ' .
            'LEFT JOIN ebh_sections s ON r.sid=s.sid ';
        $wherearr = array();
        if(!empty($queryarr['uid']))
            $wherearr[] = 'cw.uid='.$queryarr['uid'];
        if(!empty($queryarr['folderid']))
            $wherearr[] = 'r.folderid='.$queryarr['folderid'];
        if(!empty($queryarr['crid']))
            $wherearr[] = 'r.crid='.$queryarr['crid'];
        if(!empty($queryarr['status']))
            $wherearr[] = 'cw.status='.$queryarr['status'];
        if (!empty($queryarr['q']))
            $wherearr[] = ' cw.title like \'%' . $this->db->escape_str($queryarr['q']) . '%\'';
        $sql .= ' WHERE '.implode(' AND ',$wherearr);
        $sql .= ' ORDER BY sdisplayorder ASC,s.sid ASC,r.cdisplayorder ASC,cw.displayorder ASC,cw.cwid DESC ';
        $sql .= ' limit ' . $start . ',' . $pagesize;
        return $this->db->query($sql)->list_array();
    }
    /**
     * 获取平台最新发布的课件数
     */
    public function getnewcourselistcount($queryarr) {
        $count = 0;
        $sql = 'SELECT count(*) count FROM ebh_coursewares c ' .
            'JOIN ebh_roomcourses rc on (c.cwid = rc.cwid) ';
        $wherearr = array();
        if (!empty($queryarr['crid'])) {
            $wherearr[] = 'rc.crid=' . $queryarr['crid'];
        }
        if (!empty($queryarr['crid'])) {
            $wherearr[] = 'c.status = 1';
        }
        if (!empty($queryarr['uid'])) {
            $wherearr[] = 'c.uid=' . $queryarr['uid'];
        }
        if (isset($queryarr['subtime']))
            $wherearr[] = 'c.dateline > '.$queryarr['subtime'];
        if (!empty($wherearr))
            $sql .= ' WHERE ' . implode(' AND ', $wherearr);
        $row = $this->db->query($sql)->row_array();
        if(!empty($row))
            $count = $row['count'];
        return $count;
    }
    /**
     * 添加课件的评论数
     * @param int $cwid
     * @param int $num
     */
    public function addreviewnum($cwid, $num = 1) {
        $where = 'cwid=' . $cwid;
        $setarr = array('reviewnum' => 'reviewnum+' . $num);
        $this->db->update('ebh_coursewares', array(), $where, $setarr);
    }
    /**
     * 添加课件的查看数
     * @param int $cwid
     * @param int $num
     */
    public function addviewnum($cwid, $num = 1) {
        $where = 'cwid=' . $cwid;
        $setarr = array('viewnum' => 'viewnum+' . $num);
        $this->db->update('ebh_coursewares', array(), $where, $setarr);
    }

    /**
     * 获取课件详情
     * @param int $cwid
     * @return array
     */
    public function getcoursedetail($cwid) {
        $sql = 'select c.cwid,c.uid,c.catid,c.title,c.tag,c.logo,c.images,c.isrtmp,c.ism3u8,c.thumb,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,c.status,c.submitat,c.endat,c.cwlength,c.apppreview,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.classids,rc.cdisplayorder,f.foldername,f.fprice,c.viewnum,c.islive,c.liveid,c.live_type,c.sourceid ' .
            'from ebh_coursewares c ' .
            'join ebh_roomcourses rc on (c.cwid = rc.cwid) ' .
            'left join ebh_users u on (u.uid = c.uid) ' .
            'join ebh_folders f on (f.folderid = rc.folderid) '.
            'where c.cwid=' . $cwid;
        return $this->db->query($sql)->row_array();
    }
    /**
     * 获取课件的简单信息
     * @param int $cwid
     * @return array
     */
    public function getSimplecourseByCwid($cwid) {
        $sql = "select cw.cwsource,cw.title,cw.viewnum from ebh_coursewares cw where cw.cwid=$cwid";
        return $this->db->query($sql)->row_array();
    }
    /**
     * 获取平台最新发布的课件
     */
    public function getnewcourselist($queryarr) {
        $sql = 'SELECT c.cwid as id,c.cwid,c.title as name,c.cwurl,c.dateline,c.summary,c.uid,c.ism3u8,c.submitat,c.endat,c.cwlength,rc.folderid as fid,f.foldername as fname FROM ebh_coursewares c ' .
            'JOIN ebh_roomcourses rc on (c.cwid = rc.cwid) '.
            'JOIN ebh_folders f on rc.folderid = f.folderid ';
        $wherearr = array();
        if (!empty($queryarr['crid'])) {
            $wherearr[] = 'rc.crid=' . $queryarr['crid'];
        }
        if (!empty($queryarr['crid'])) {
            $wherearr[] = 'c.status = 1';
        }
        if (!empty($queryarr['uid'])) {
            $wherearr[] = 'c.uid=' . $queryarr['uid'];
        }
        if(!empty($queryarr['abegindate'])) {
            $wherearr[] = 'c.dateline>='.$queryarr['abegindate'];
        }
        if(!empty($queryarr['aenddate'])) {
            $wherearr[] = 'c.dateline<'.$queryarr['aenddate'];
        }
        if(!empty($queryarr['folderids']))
            $wherearr[] = 'rc.folderid in ('.$queryarr['folderids'].')';
        if (!empty($wherearr))
            $sql .= ' WHERE ' . implode(' AND ', $wherearr);
        if (!empty($queryarr['order']))
            $sql .= ' ORDER BY ' . $queryarr['order'];
        else
            $sql .= ' ORDER BY c.cwid DESC ';
        if (!empty($queryarr['limit']))
            $sql .= ' limit ' . $queryarr['limit'];
        else {
            $sql .= ' limit 0,10 ';
        }
        return $this->db->query($sql)->list_array();
    }
    public function setviewnum($cwid, $num = 1) {
        $where = 'cwid=' . $cwid;
        $setarr = array('viewnum' => $num);
        $this->db->update('ebh_coursewares', array(), $where, $setarr);
    }






    //获取课程对应课件的列表数据
    public function getCourseList($param = array(),$fields = '',$format = true){
        if (!empty($param['uid'])) {
            $uid = $param['uid'];
        }

        /*if(empty($uid)){
            return array();
        }*/
        if(empty($fields)){
            $fields = ' cw.cwid,cw.title,cw.summary,cw.cwname,cw.cwlength,cw.dateline,cw.truedateline,cw.logo,cw.ism3u8,cw.cwurl,cw.thumb,cw.uid,cw.submitat,cw.endat,cw.islive,rc.sid,s.sname,ifnull(s.displayorder,10000) sdisplayorder ';
        }
        $sql = 'select '.$fields.' from ebh_coursewares cw join ebh_roomcourses rc on cw.cwid = rc.cwid left join ebh_sections s on rc.sid = s.sid ';
        $wherearr = array();
        if(!empty($param['folderid'])){
            $wherearr[] = 'rc.folderid='.$param['folderid'];
        }
        $wherearr[] = 'cw.status=1';
        if (!empty($param['only_vedio'])) {
            //$wherearr[] = "(substring_index(cw.cwurl,'.',-1) in('flv','mp4','avi','mpeg','mpg','rmvb','rm','mov','swf') or cw.islive=1)";
            $wherearr[] = "(cw.ism3u8=1 or cw.islive=1)";
        }
        if (empty($param['classids'])) {
            $wherearr[] = '(rc.classids=\'\' or rc.classids=\'0\')';
        } else {
            $wherearr[] = '(rc.classids=\'\' or rc.classids=\'0\' or find_in_set('.$param['classids'].',rc.classids))';
        }
        if (!empty($wherearr)){
            $sql .= ' WHERE ' . implode(' AND ', $wherearr);
        }
        if(!empty($param['order'])) {
            $sql .= ' ORDER BY '.$param['order'];
        } else {
            $sql .= ' ORDER BY sdisplayorder ASC,s.sid ASC,rc.cdisplayorder ASC,cw.displayorder ASC,cw.cwid DESC ';
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
        $courselist =  $this->db->query($sql)->list_array();
        $resArr = array();
        if(!empty($courselist) && !empty($format)){
            $percentDb = array();

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
            //存在视频课件
            if(!empty($uid) && !empty($cwids)){
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
                    $percentDb[$key] = $p;
                }
            }
            $userdata = EBH::app()->lib('UserUtil')->init($courselist,array('uid'));
            foreach ($courselist as $course) {
                if(empty($course['sid'])){
                    $course['sid'] = 0;
                }
                if(empty($course['sname'])){
                    $course['sname'] = '其它';
                }
                $key = 'sid_'.$course['sid'];
                if(!array_key_exists($key, $resArr)){
                    $resArr[$key] = array(
                        'sid'=>$course['sid'],
                        'sname'=>$course['sname'],
                        'clist'=>array()
                    );
                }

                $tmp = array();
                $tmp['cwlength'] = secondToStr($course['cwlength']);
                if(empty($tmp['cwlength'])){
                    $tmp['cwlength'] = '暂无';
                }
                if(!empty($course['ism3u8'])){
                    $tmp['type'] = 1;
                }else{
                    $tmp['type'] = 0;
                }
                $tmp['cwid'] = $course['cwid'];
                $tmp['title'] = $course['title'];
                $tmp['summary'] = $course['summary'];
                $tmp['cwname'] = $course['cwname'];
                $tmp['thumb'] = $course['thumb'];
                if(empty($tmp['thumb'])){
                    //取出文件类型

                    $fileExt = explode('.',$course['cwurl']);
                    $fileExt = $fileExt[count($fileExt)-1];

                    $thumbArr = array(
						'xls'   =>  'http://static.ebanhui.com/ebh/images/cwlogo/excel.png',
						'xlsx'   =>  'http://static.ebanhui.com/ebh/images/cwlogo/excel.png',
                        'mp3'   =>  'http://static.ebanhui.com/ebh/images/cwlogo/mp3.png',
                        'ppt'   =>  'http://static.ebanhui.com/ebh/images/cwlogo/ppt.png',
                        'pptx'   =>  'http://static.ebanhui.com/ebh/images/cwlogo/ppt.png',
                        'doc'   =>  'http://static.ebanhui.com/ebh/images/cwlogo/word.png',
                        'docx'   =>  'http://static.ebanhui.com/ebh/images/cwlogo/word.png',
						'pdf'   =>  'http://static.ebanhui.com/ebh/images/cwlogo/pdf.png',
						'zip'   =>  'http://static.ebanhui.com/ebh/images/cwlogo/zip.png',
						'rar'   =>  'http://static.ebanhui.com/ebh/images/cwlogo/zip.png',
                    );

                    if(isset($thumbArr[$fileExt])){
                        $tmp['thumb'] = $thumbArr[$fileExt];
                    }else{
                        $tmp['thumb'] = 'http://static.ebanhui.com/ebh/tpl/default/images/folderimgnew/nopic.jpg';
                    }
                    //$tmp['thumb'] = 'http://static.ebanhui.com/ebh/tpl/2014/images/kustgd.png';
                }
                $userdata->setUser($course['uid']);
                $tmp['tname'] = $userdata->getName();
                $tmp['islive'] = $course['islive'];
                $tmp['ism3u8'] = $course['ism3u8'];
                $tmp['submitat'] = $course['submitat'];
                $tmp['endat'] = $course['endat'];
                $tmp['logo'] = $course['logo'];
                $tmp['truedateline'] = !empty($course['truedateline']) ? $course['truedateline'] : $course['submitat'];
                $percent_key = 'cw_'.$tmp['cwid'];
                //非视频课件进度为100%
                if(!array_key_exists($percent_key,$percentDb) && empty($course['ism3u8'])){
                    $tmp['progress'] = 1;
                }else{
                    $tmp['progress'] = array_key_exists($percent_key,$percentDb) ? $percentDb[$percent_key] : 0;
                }
                $resArr[$key]['clist'][] = $tmp;
            }
        }else{
            $resArr = $courselist;
        }
        return array_values($resArr);
    }
}
