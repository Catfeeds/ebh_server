<?php
/**
 * 用户状态处理Model类UserstateModel
 */
class UserstateModel extends CModel{
    /**
     * 生成或更新用户状态时间记录
     * @param type $crid
     * @param type $userid
     * @param type $typeid
     * @param type $time
     * @return type
     */
    public function insert($crid,$userid,$typeid,$time){
        $sql = "REPLACE INTO ebh_userstates (crid,userid,typeid,subtime) VALUES($crid,$userid,$typeid,$time)";
	$result = $this->db->query($sql);
        return $result;
    }
    /**
     * 获取用户的最后状态时间
     * @param type $crid
     * @param type $uid
     * @param type $typeid
     * @return type
     */
    public function getsubtime($crid,$uid,$typeid) {
        $subtime = 0;
        $sql = "SELECT subtime FROM ebh_userstates WHERE crid=$crid AND userid=$uid AND typeid=$typeid";
        $row = $this->db->query($sql)->row_array();
        if(!empty($row))
            $subtime = $row['subtime'];
        return $subtime;
    }
}
