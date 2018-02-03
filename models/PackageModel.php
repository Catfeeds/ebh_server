<?php
class PackageModel extends CModel{
    /**
     * 服务包列表
     * @param $crid 网校ID
     * @param int $limit
     * @param bool $setKey
     * @return mixed
     */
	public function getList($crid, $limit = 0, $setKey = false){
		$offset = 0;
		$top = 0;
		if (!empty($limit)) {
		    if (is_array($limit)) {
		        $page = !empty($limit['page']) ? intval($limit['page']) : 1;
		        $page = max(1, $page);
                $top = !empty($limit['pagesize']) ? intval($limit['pagesize']) : 20;
		        $offset = ($page - 1) * $top;
            } else {
		        $top = intval($limit);
            }
        }
        $sql = 'SELECT `pid`,`pname` FROM `ebh_pay_packages` `a` LEFT JOIN `ebh_classrooms` `b` ON `b`.`crid`=`a`.`crid` AND `a`.`status`=1 WHERE `a`.`crid`='.intval($crid).' AND `b`.`crid` IS NOT NULL';
		if ($top > 0) {
		    $sql .= ' LIMIT '.$offset.','.$top;
        }
        $sql .= ' ORDER BY `a`.`displayorder` ASC,`pid` DESC';
        return $this->db->query($sql)->list_array($setKey ? 'pid' : '');
	}

    /**
     * 服务包菜单
     * @param $crid 网校ID
     * @param bool $setKey 是否以服务包ID设置键
     * @return mixed
     */
	public function getMenuList($crid, $setKey =  false) {
	    $crid = intval($crid);
	    $params = array(
	        '`a`.`crid`='.$crid,
            //'`a`.`defind_course`=1', 暂时隐藏
            '`a`.`status`=0',
            '`b`.`del`=0',
            '`b`.`folderlevel`=2',
            '`c`.`status`=1',
            'IFNULL(`d`.`ishide`,0)=0'
        );
	    $sql = 'SELECT DISTINCT `c`.`pid`,`c`.`pname` FROM `ebh_pay_items` `a` 
                LEFT JOIN `ebh_folders` `b` ON `b`.`folderid`=`a`.`folderid` 
                LEFT JOIN `ebh_pay_packages` `c` ON `c`.`pid`=`a`.`pid`
                LEFT JOIN `ebh_pay_sorts` `d` ON `d`.`sid`=`a`.`sid` 
                WHERE '.implode(' AND ', $params).' ORDER BY `c`.`displayorder`,`c`.`pid` DESC';
	    return $this->db->query($sql)->list_array($setKey ? 'pid' : '');
    }

    /**
     * 服务包列表
     * @param int $crid 网校ID
     * @param array $pids pid集
     * @return mixed
     */
    public function getPackageMenuList($crid, $pids) {
	    $sql = 'SELECT `a`.`pid`,`a`.`pname`,IF(`a`.`crid`='.$crid.',1,0) AS `mine` FROM `ebh_pay_packages` `a` JOIN `ebh_classrooms` `b` ON `b`.`crid`=`a`.`crid` WHERE `a`.`pid` IN('.implode(',', $pids).') AND `a`.`status`=1 ORDER BY `mine` DESC,`b`.`displayorder` ASC,`b`.`crid` DESC';
	    return $this->db->query($sql)->list_array('pid');
    }
}