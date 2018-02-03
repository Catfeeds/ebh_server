<?php
/**
 * 课程包
 * Author: ycq
 */
class BundleModel extends CModel {
    /**
     * 课程包课程列表
     * @param int $bid 课程包ID
     * @param bool $simple 是否显示简单信息
     * @param bool $setKey 是否以课程服务项ID为键
     * @return mixed
     */
    public function getCourseList($bid, $simple = true, $setKey = false) {
        $fields = array(
            '`b`.`itemid`',
            '`b`.`iname`',
            '`b`.`pid`',
            '`c`.`folderid`',
            '`c`.`foldername`',
            '`c`.`img`',
            '`c`.`showmode`'
        );
        if (!$simple) {
            $fields[] = '`b`.`iprice`';
            $fields[] = '`b`.`imonth`';
            $fields[] = '`b`.`iday`';
            $fields[] = '`c`.`coursewarenum`';
            $fields[] = '`c`.`viewnum`';
            $fields[] = '`c`.`summary`';
        }
        $wheres = array(
            '`a`.`bid`='.$bid,
            '`a`.`astype`=0',
            '`a`.`status`=0',
            '`b`.`status`=0',
            '`c`.`del`=0'
        );
        $sql = 'SELECT '.implode(',', $fields).' FROM `ebh_bundle_assos` `a` 
                JOIN `ebh_pay_items` `b` ON `b`.`itemid`=`a`.`asid`  
                JOIN `ebh_folders` `c` ON `c`.`folderid`=`b`.`folderid` 
                WHERE '.implode(' AND ', $wheres);
        return $this->db->query($sql)->list_array($setKey ? 'itemid' : '');
    }

    /**
     * 课程包详情
     * @param int $bid 课程包ID
     * @return mixed
     */
    public function bundleDetail($bid) {
        $wheres = array(
            '`a`.`bid`='.$bid,
            '`b`.`status`=1',
            'IFNULL(`c`.`ishide`,0)=0'
        );
        $sql = 'SELECT `a`.`crid`,`a`.`bid`,`a`.`name`,`a`.`remark`,`a`.`cover`,`a`.`pid`,`a`.`sid`,`a`.`speaker`,`a`.`bprice`,`a`.`detail`,`a`.`cannotpay`,`b`.`pname`,IFNULL(`c`.`sname`,\'其他课程\') AS `sname` ,`a`.`limitnum`,`a`.`islimit`
                FROM `ebh_bundles` `a` JOIN `ebh_pay_packages` `b` ON `b`.`pid`=`a`.`pid`
                LEFT JOIN `ebh_pay_sorts` `c` ON `c`.`sid`=`a`.`sid` WHERE '.implode(' AND ', $wheres);
        return $this->db->query($sql)->row_array();
    }

    /**
     * 课程包列表
     * @param int $crid 网校ID
     * @param array $filterParams 过滤条件
     * @param int $limits 限量条件
     * @return array
     */
    public function bundleList($crid, $filterParams = array(), $limits = 0) {
        $wheres = array('`a`.`crid`='.$crid);
        if (!empty($filterParams['pid'])) {
            $wheres[] = '`a`.`pid`='.intval($filterParams['pid']);
        }
        if (!empty($filterParams['pid']) && isset($filterParams['sid'])) {
            $wheres[] = '`a`.`sid`='.intval($filterParams['sid']);
        }
        if (!empty($filterParams['k'])) {
            $wheres[] = '`a`.`name` LIKE '.Ebh()->db->escape('%'.$filterParams['k'].'%');
        }
        if (!empty($filterParams['free'])) {
            $wheres[] = '`a`.`bprice`=0';
        }
        $offset = 0;
        $top = 0;
        if (!empty($limits)) {
            if (is_array($limits)) {
                $page = isset($limits['page']) ? intval($limits['page']) : 1;
                $top = isset($limits['pagesize']) ? intval($limits['pagesize']) : 1;
                $page = max(1, $page);
                $top = max(1, $top);
                $offset = ($page - 1) * $top;
            } else {
                $top = intval($limits);
                $top = max(1, $top);
            }
        }
        $sql = 'SELECT `a`.`bid`,`a`.`name`,`a`.`cover`,`a`.`speaker`,`a`.`remark`,`a`.`bprice`,`a`.`pid`,`a`.`sid`,`a`.`display`,`a`.`displayorder`,`b`.`pname`,`b`.`displayorder` AS `pdisplayorder`,IFNULL(`c`.`sname`,\'其他课程\') AS `sname` 
                FROM `ebh_bundles` `a` JOIN `ebh_pay_packages` `b` ON `b`.`pid`=`a`.`pid`
                LEFT JOIN `ebh_pay_sorts` `c` ON `c`.`sid`=`a`.`sid`';
        if (!empty($filterParams['display'])) {
            $wheres[] = '`a`.`display`=1';
            $sql .= ' WHERE '.implode(' AND ', $wheres).' ORDER BY `a`.`displayorder` ASC,`a`.`bid` DESC';
        } else {
            $sql .= ' WHERE '.implode(' AND ', $wheres).' ORDER BY `a`.`bid` DESC';
        }

        if ($top > 0) {
            $sql .= ' LIMIT '.$offset.','.$top;
        }
        return $this->db->query($sql)->list_array('bid');
    }

    /**
     * 课程包课程统计信息
     * @param array $bids
     * @return array
     */
    public function courseList($bids) {
        $wheres = array(
            '`a`.`bid` IN('.implode(',', $bids).')',
            '`a`.`astype`=0',
            '`a`.`status`=0'
        );
        $sql = 'SELECT `a`.`bid`,`b`.`itemid`,`b`.`folderid`,`b`.`imonth`,`b`.`iday`,`c`.`foldername`,`c`.`coursewarenum`,`c`.`viewnum`,`c`.`showmode`,`c`.`img`,`c`.`summary`,`e`.`grank`,`e`.`prank`,`e`.`srank` 
                FROM `ebh_bundle_assos` `a`
                LEFT JOIN `ebh_pay_items` `b` ON `b`.`itemid`=`a`.`asid` AND `b`.`status`=0 AND `b`.`cannotpay`=0 
                LEFT JOIN `ebh_folders` `c` ON `c`.`folderid`=`b`.`folderid` AND `c`.`del`=0 AND `c`.`power`=0 AND `c`.`folderlevel`>1
                LEFT JOIN `ebh_pay_packages` `d` ON `d`.`pid`=`a`.`asid` AND `d`.`status`=1
                LEFT JOIN `ebh_courseranks` `e` ON `e`.`folderid`=`c`.`folderid` AND `e`.`crid`=`c`.`crid`
                WHERE '.implode(' AND ', $wheres);
        $ret = $this->db->query($sql)->list_array();
        if (empty($ret)) {
            return array();
        }
        return $ret;
    }
}