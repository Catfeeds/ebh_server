<?php

/*
  教室与会员,前台会员->云教育网校
 */

class RoomuserModel extends CModel {
     /*
      会员加入的网校数
      @param int $uid
     */

    public function getroomcount($uid) {
        $sql = 'select count(*) count from ebh_roomusers where uid=' . $uid;
        $count = $this->db->query($sql)->row_array();
        return $count['count'];
    }

    /*
      会员加入的网校列表
      @param int $uid
     */

    public function getroomlist($uid) {
        $sql = 'select c.cface,c.domain,c.crname,c.crid,c.summary,r.enddate,c.coursenum,c.isschool,c.coursenum,c.examcount from ebh_roomusers r 
			join ebh_classrooms c on r.crid=c.crid
			where r.uid=' . $uid;
        return $this->db->query($sql)->list_array();
    }

     /**
     * 根据教室和学员编号获取学员在教室内的信息详情
     * @param type $crid
     * @param type $uid
     * @return type
     */
    public function getroomuserdetail($crid,$uid) {
        $sql = "select ru.cstatus,ru.rbalance,ru.begindate,ru.enddate from ebh_roomusers ru where ru.crid=$crid and ru.uid=$uid";
        return $this->db->query($sql)->row_array();
    }

    /**
     * 插入ebh_roomusers记录，主要用于学员和教室的绑定
     * @param type $param
     * @return boolean
     */
    public function insert($param) {
        if (empty($param['crid']) || empty($param['uid']))
            return FALSE;
        $setarr = array();
        $setarr['crid'] = $param['crid'];
        $setarr['uid'] = $param['uid'];
        if (!empty($param ['cdateline'])) { //记录添加时间
            $setarr ['cdateline'] = $param ['cdateline'];
        } else {
            $setarr ['cdateline'] = SYSTIME;
        }
        if (!empty($param ['begindate'])) { //服务开始时间
            $setarr ['begindate'] = $param ['begindate'];
        }
        if (!empty($param ['enddate'])) {   //服务结束时间
            $setarr ['enddate'] = $param ['enddate'];
        }
        if (!empty($param ['cnname'])) {   //学生真实姓名，此处只做存档用
            $setarr ['cnname'] = $param ['cnname'];
        }
    if (isset($param ['cstatus'])) { //状态，1正常 0 锁定
            $setarr ['cstatus'] = $param['cstatus'];
        }
        if (isset($param ['sex'])) {   //性别
            $setarr ['sex'] = $param ['sex'];
        }
        if (isset($param ['birthday'])) {   //出生日期
            $setarr ['birthday'] = $param ['birthday'];
        }
        if (!empty($param ['mobile'])) {   //联系方式
            $setarr ['mobile'] = $param ['mobile'];
        }
        if (!empty($param ['email'])) {   //邮箱
            $setarr ['email'] = $param ['email'];
        }

        $afrows = $this->db->insert('ebh_roomusers',$setarr);
        return $afrows;
    }

    /**
     * 更新教室内的学员信息，需要带上$crid和$uid
     * @param type $param
     */
    public function update($param) {
        if (empty($param['crid']) || empty($param['uid']))
            return FALSE;
        $wherearr = array('crid'=>$param['crid'],'uid'=>$param['uid']);
        $setarr = array();
        if (!empty($param ['begindate'])) { //服务开始时间
            $setarr ['begindate'] = $param ['begindate'];
        }
        if (!empty($param ['enddate'])) {   //服务结束时间
            $setarr ['enddate'] = $param ['enddate'];
        }
        if (isset($param['cstatus'])) { //状态，1正常 0 锁定
            $setarr ['cstatus'] = $param['cstatus'];
        }
        if (!empty($param ['rbalance'])) {  //学员在教室内余额，单用于一个教室
            $setarr['rbalance'] = $param['rbalance'];
        }
        if(empty($setarr))
            return FALSE;
        $afrows = $this->db->update('ebh_roomusers',$setarr,$wherearr);
    return $afrows;
    }
    /**
     * 判断是否为校友
     * @param $crid
     * @param $uid
     * @return bool
     */
    public function isAlumni($crid, $uid) {
        $crid = (int) $crid;
        $uid = (int) $uid;
        if ($crid < 1 || $uid < 1) {
            return false;
        }
        $sql = "SELECT 1 AS `exists` FROM `ebh_roomusers` WHERE `uid`=$uid AND `crid`=$crid";
        $exists = $this->db->query($sql)->row_array();
        return !empty($exists);
    }
}