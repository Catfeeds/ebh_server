<?php
/**
 * 名师团队
 * Created by PhpStorm.
 * User: ycq
 * Date: 2017/6/19
 * Time: 16:42
 */
class MasterModel extends CModel{
    /**
     * 名师团队列表
     * @param $crid 网校ID
     * @param int $limit 查询返回限制
     * @return mixed
     */
    public function getList($crid, $limit = 0) {
        $crid = intval($crid);
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
        $sql = 'SELECT `a`.`tid`,`c`.`realname`,`c`.`username`,`b`.`professionaltitle`,`c`.`sex`,`c`.`face`,`c`.`groupid` FROM `ebh_masters` `a` 
                LEFT JOIN `ebh_teachers` `b` ON `b`.`teacherid`=`a`.`tid` 
                LEFT JOIN `ebh_users` `c` ON `c`.`uid`=`a`.`tid` WHERE `a`.`crid`='.$crid.
            ' AND `b`.`teacherid` IS NOT NULL AND `c`.`uid` IS NOT NULL ORDER BY `a`.`displayorder` DESC,`a`.`dateline` DESC';
        if ($top > 0) {
            $sql .= ' LIMIT '.$offset.','.$top;
        }
        return $this->db->query($sql)->list_array();
    }

    /**
     * 名师基本信息
     * @param $tid 教师ID
     * @param $crid 网校ID
     * @return mixed
     */
    public function detail($tid, $crid) {
        $tid = intval($tid);
        $crid = intval($crid);
        $sql = 'SELECT `a`.`tid`,`c`.`realname`,`c`.`username`,`b`.`professionaltitle`,`b`.`profile`,`c`.`sex`,`c`.`face`,`c`.`groupid` FROM `ebh_masters` `a` 
                LEFT JOIN `ebh_teachers` `b` ON `b`.`teacherid`=`a`.`tid` 
                LEFT JOIN `ebh_users` `c` ON `c`.`uid`=`a`.`tid` WHERE `a`.`tid`='.$tid.' AND `a`.`crid`='.$crid.
            ' AND `b`.`teacherid` IS NOT NULL AND `c`.`uid` IS NOT NULL';
        return $this->db->query($sql)->row_array();
    }
}