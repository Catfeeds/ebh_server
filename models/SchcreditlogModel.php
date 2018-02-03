<?php
/**
 *学分获取模型
 */
class SchcreditlogModel extends CModel{
	//获取学分列表
	public function getList($param = array()){
		$sql = 'select scl.score,scl.dateline,f.foldername,cw.title from ebh_schcreditlog scl left join ebh_coursewares cw on scl.cwid = cw.cwid left join ebh_folders f on scl.folderid = f.folderid';
		$whereArr = array();
		if(!empty($param['uid'])){
			$whereArr[] = 'scl.uid = '.$param['uid'];
		}
		if(!empty($param['crid'])){
			$whereArr[] = 'scl.crid = '.$param['crid'];
		}
        if(!empty($param['title'])) {
            $whereArr[] = '(cw.title like \'%'.$this->db->escape_str($param['title']).'%\')';
        }
        if(!empty($param['foldername'])) {
            $whereArr[] = '(f.foldername like \'%'.$this->db->escape_str($param['foldername']).'%\')';
        }
		if(!empty($whereArr)){
			$sql.=' WHERE '.implode(' AND ', $whereArr);
		}
		if(!empty($param['order'])){
			$sql.=' order by '.$this->db->escape_str($param['order']);
		}else{
			$sql.=' order by scl.logid desc';
		}
		if(!empty($param['limit'])){
			$sql.=' limit '.$param['limit'];
		}else{
			$sql.=' limit 1000';
		}
		return $this->db->query($sql)->list_array();
	}

	//获取学分列表
	public function getListCount($param = array()){
		$sql = 'select count(*) count from ebh_schcreditlog scl join ebh_coursewares cw on scl.cwid = cw.cwid left join ebh_folders f on scl.folderid = f.folderid';
		$whereArr = array();
		if(!empty($param['uid'])){
			$whereArr[] = 'scl.uid = '.$param['uid'];
		}
		if(!empty($param['crid'])){
			$whereArr[] = 'scl.crid = '.$param['crid'];
		}
        if(!empty($param['title'])) {
            $whereArr[] = '(cw.title like \'%'.$this->db->escape_str($param['title']).'%\')';
        }
        if(!empty($param['foldername'])) {
            $whereArr[] = '(f.foldername like \'%'.$this->db->escape_str($param['foldername']).'%\')';
        }
		if(!empty($whereArr)){
			$sql.=' WHERE '.implode(' AND ', $whereArr);
		}
		$res = $this->db->query($sql)->row_array();
		return $res['count'];
	}

	//获取学分统计(分组)
	public function getTotalList($param = array()){
		$sql = 'select scl.eid,scl.uid,scl.crid,sum(scl.score) as score,scl.dateline,scl.fromip,u.username,u.realname,cs.classname,cs.grade from ebh_classes cs 
		join ebh_classstudents cts on cs.classid = cts.classid 
		left join ebh_schcreditlog scl on scl.uid = cts.uid and scl.crid = cs.crid 
		left join ebh_users u on u.uid = cts.uid';
		$whereArr = array();
		if(!empty($param['uid'])){
			$whereArr[] = 'cts.uid = '.$param['uid'];
		}
		if(!empty($param['uid_in'])){
			$whereArr[] = 'cts.uid in ('.implode(',', $param['uid_in']).')';
		}
		if(!empty($param['crid'])){
			$whereArr[] = 'cs.crid = '.$param['crid'];
		}
		if(!empty($param['q'])) {
            $whereArr[] = '(u.username like \'%'.$this->db->escape_str($param['q']).'%\''.
                    ' or u.realname like \'%'.$this->db->escape_str($param['q']).'%\')';
        }
		if(!empty($whereArr)){
			$sql.=' WHERE '.implode(' AND ', $whereArr);
		}

		$sql.=' group by cts.uid ';

		if(!empty($param['order'])){
			$sql.=' order by '.$this->db->escape_str($param['order']);
		}

		if(!empty($param['limit'])){
			$sql.=' limit '.$param['limit'];
		}else{
			$sql.=' limit 1000';
		}
		return $this->db->query($sql)->list_array();
	}

	public function getTotalListCount($param = array()){
		$sql = 'select count(*) count from ebh_classes cs 
		join ebh_classstudents cts on cs.classid = cts.classid 
		left join ebh_users u on u.uid = cts.uid';
		$whereArr = array();
		if(!empty($param['uid'])){
			$whereArr[] = 'cts.uid = '.$param['uid'];
		}
		if(!empty($param['uid_in'])){
			$whereArr[] = 'cts.uid in ('.implode(',', $param['uid_in']).')';
		}
		if(!empty($param['crid'])){
			$whereArr[] = 'cs.crid = '.$param['crid'];
		}
		if(!empty($param['q'])) {
            $whereArr[] = '(u.username like \'%'.$this->db->escape_str($param['q']).'%\''.
                    ' or u.realname like \'%'.$this->db->escape_str($param['q']).'%\')';
        }
		if(!empty($whereArr)){
			$sql.=' WHERE '.implode(' AND ', $whereArr);
		}
		$res = $this->db->query($sql)->row_array();
		return $res['count'];
	}

	//获取指定用户总学分
	public function getTotalScore($param = array()){

		if(empty($param['crid'])||empty($param['uid'])){
			return 0;
		}

		$sql = 'select sum(scl.score) as totalScore from ebh_schcreditlog scl';

		$whereArr = array();
		$whereArr[] = 'scl.crid = '.$param['crid'];
		$whereArr[] = 'scl.uid = '.$param['uid'];

		$sql.=' WHERE '.implode(' AND ', $whereArr);

		$res = $this->db->query($sql)->row_array();
		return $res['totalScore'];
	}

	//写入一条数据
	public function _insert($param = array()){
		if(empty($param)){
			return;
		}
		return $this->db->insert('ebh_schcreditlog',$param);
	}

	//获取分数记录列表
	public function getSimpleList($param = array()){
		if(empty($param)){
			return array();
		}
		$sql = 'select scl.eid,scl.crid,scl.cwid,scl.score from ebh_schcreditlog as scl';
		$whereArr = array();
		if(!empty($param['crid'])){
			$whereArr[] = 'scl.crid = '.$param['crid'];
		}
		if(!empty($param['eid'])){
			$whereArr[] = 'scl.eid = '.$param['eid'];
		}
		if(!empty($param['cwid'])){
			$whereArr[] = 'scl.cwid = '.$param['cwid'];
		}
		if(!empty($param['uid'])){
			$whereArr[] = 'scl.uid = '.$param['uid'];
		}
		if(!empty($whereArr)){
			$sql.=' WHERE '.implode(' AND ', $whereArr);
		}
		return $this->db->query($sql)->list_array();
	}

	//学生学分同步
	public function schcreditSync($crid,$uid){
		if(empty($crid) || empty($uid) || !is_numeric($crid) || !is_numeric($uid)){
			return -1;//参数不全或者非法
		}
		//所有满分作业的cwid
		$sql_a = 'select distinct(se.cwid) as cwid,se.folderid,se.eid,sea.dateline from ebh_schexamanswers sea join ebh_schexams se on sea.eid = se.eid where sea.status = 1 and sea.uid = '.$uid.' and sea.totalscore = se.score and se.cwid >0';
		$cwInfos = $this->db->query($sql_a)->list_array();
		if(empty($cwInfos)){
			return -2;//表示没有满分作业
		}
		$cwidsArr = array();
		$cwidInfoDb = array();
		foreach ($cwInfos as $cwInfo) {
			$key = 'cw_'.$cwInfo['cwid'];
			$cwidsArr[] = $cwInfo['cwid'];
			$cwidInfoDb[$key] = $cwInfo;
		}

		//已经获取学分的cwid
		$sql_b = 'select cwid from ebh_schcreditlog scl where scl.cwid in ('.implode(',', $cwidsArr).') and uid ='.$uid;
		$cwInfos_b = $this->db->query($sql_b)->list_array();
		$cwidsArr_b = array();
		foreach ($cwInfos_b as $cwInfo_b) {
			$cwidsArr_b[] = $cwInfo_b['cwid'];
		}

		//已经看完课件的cwid
		$sql_c = 'select cwid from ebh_playlogs pl where pl.cwid in ('.implode(',', $cwidsArr).') and uid = '.$uid.' and pl.ctime <= pl.ltime' ;
		$cwInfos_c = $this->db->query($sql_c)->list_array();
		$cwidsArr_c = array();
		foreach ($cwInfos_c as $cwInfo_c) {
			$cwidsArr_c[] = $cwInfo_c['cwid'];
		}
		if(empty($cwidsArr_c)){
			return -3;//表示没有已经看完的课件
		}

		$cwidsOk = array_intersect($cwidsArr,$cwidsArr_c); //所有满足获学分的cwid
		$cwidsOk_notGet = array_diff($cwidsOk,$cwidsArr_b);
		if(empty($cwidsOk_notGet)){
			return -4;//表示无需更新
		}
		$sql = 'insert into ebh_schcreditlog (folderid,eid,uid,crid,cwid,score,dateline,fromip) values';
		$sqlValues = array();
		$ip =  EBH::app()->getInput()->getip();
		foreach ($cwidsOk_notGet as $v) {
			$curCwidInfo = $cwidInfoDb['cw_'.$v];
			$temp = array();
			$temp[] = $curCwidInfo['folderid'];
			$temp[] = $curCwidInfo['eid'];
			$temp[] = $uid;
			$temp[] = $crid;
			$temp[] = $v;
			$temp[] = 1;
			$temp[] = $curCwidInfo['dateline']; 
			$temp[] = '\''.$ip.'\'';
			$sqlValues[] = '('.implode(',', $temp).')';
		}
		$sql.=implode(',', $sqlValues);
		$this->db->query($sql);
		return $this->db->affected_rows();
	}

	//学生学分同步
	public function schcreditTimeSync($crid,$uid){
		//获取已经得到学分的记录
		$sql_a = 'select scl.logid,scl.crid,scl.cwid,scl.dateline from ebh_schcreditlog scl where scl.crid = '.$crid.' and scl.uid = '.$uid;
		$logsList = $this->db->query($sql_a)->list_array();
		if(empty($logsList)){
			return -4; //没有积分记录
		}
		$cwids = array();
		$logsDb = array();
		foreach ($logsList as $log) {
			$key = 'log_'.$log['cwid'];
			$logsDb[$key] = $log;
			$cwids[] = $log['cwid'];
		}
		//获取有资格得到学分的记录
		$sql_b = 'select sea.dateline,se.cwid from ebh_schexams se join ebh_schexamanswers sea on se.eid = sea.eid where se.cwid in ('.implode(',', $cwids).') and sea.status = 1 and sea.uid = '.$uid.' and se.score = sea.totalscore and se.crid = '.$crid;
		$schanswersList = $this->db->query($sql_b)->list_array();
		if(empty($schanswersList)){
			return -3; //没有符合条件的作业记录
		}
		$schanswersDb = array();
		foreach ($schanswersList as $schanswer) {
			$key = 'log_'.$schanswer['cwid'];
			$schanswersDb[$key] = $schanswer;
		}
		
		$sql = 'UPDATE ebh_schcreditlog SET dateline = CASE logid ';
		$wtArr = array();
		$inArr = array();
		foreach ($logsList as $log) {
			$key = 'log_'.$log['cwid'];
			if($logsDb[$key]['dateline'] != $schanswersDb[$key]['dateline']){
				$wtArr[] = ' WHEN '.$logsDb[$key]['logid'].' THEN '.$schanswersDb[$key]['dateline'];
				$inArr[] = $logsDb[$key]['logid'];
			}
		}
		if(empty($wtArr)){
			return -2;//不需要更新
		}
		$sql.= implode(' ', $wtArr).' END WHERE logid IN ('.implode(',', $inArr).')';
		$this->db->query($sql);
		return $this->db->affected_rows();
	}
}