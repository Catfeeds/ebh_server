<?php
	/**
	* 学习记录model对应的ebh_studylog表	
	* 主要记录和查询学生听课时间
	*/
	class StudylogModel extends CModel{
		/**
		*添加听课记录，如果已经存在就更新最大的时间
		*todo:此处可能涉及积分问题，暂留
		*/
		public function addlog($param) {
			if(empty($param['cwid']) || empty($param['uid']) || empty($param['ctime']) || empty($param['ltime']) ) {
				return false;
			}
			$cwid = $param['cwid'];	//课件编号
			$uid = $param['uid'];	//用户编号
			$ctime = $this->_ensureCtime($cwid,$param['ctime']); //课件时长
			$ltime = $param['ltime'];	//学习持续时间
			$setarr = array('cwid'=>$cwid,'uid'=>$uid,'ctime'=>$ctime,'ltime'=>$ltime,'startdate'=>SYSTIME,'lastdate'=>SYSTIME);
			$result = $this->db->insert('ebh_playlogs',$setarr);

			return $result;
		}
		/**
		*修改听课记录
		*/
		public function updatelog($param) {
			if(empty($param['logid']) || empty($param['uid']) || empty($param['ctime']) || empty($param['ltime']) ) {
				return false;
			}
			$result = FALSE;
			$uid = $param['uid'];	//用户编号
			$ctime = $param['ctime'];	//课件时长
			$ltime = $param['ltime'];	//学习持续时间
			$logid = $param['logid'];
			$sql = 'SELECT p.logid,p.ctime,p.ltime FROM ebh_playlogs p WHERE p.logid='.$logid; 
			$row = $this->db->query($sql)->row_array();
			if(empty($row))
				return FALSE;
			$wherearr = array('logid'=>$logid,'uid'=>$uid);
			$setarr = array('lastdate'=>SYSTIME);
			if($row['ctime'] != $ctime)
				$setarr['ctime'] = $ctime;
			if($row['ltime'] < $ltime)
				$setarr['ltime'] = $ltime;
				$result = $this->db->update('ebh_playlogs',$setarr,$wherearr);

			return $result;
		}

		/**
		*添加听课记录，如果已经存在就更新最大的时间
		*todo:此处可能涉及积分问题，暂留
		*/
		public function addlogs($param) {
			if(empty($param['cwid']) || empty($param['uid']) || empty($param['ctime']) || empty($param['ltime']) ) {
				return false;
			}
			$cwid = $param['cwid'];	//课件编号
			$uid = $param['uid'];	//用户编号
			$ctime = $param['ctime'];	//课件时长
			$ltime = $param['ltime'];	//学习持续时间
			$finished = $param['finished']; //是否听完
			
			$cache = Ebh::app()->getCache();
			$keyparam = array('uid'=>$uid,'cwid'=>$cwid);
			$id1 = $cache->getcachekey('playlogs_total',$keyparam);
			$id2 = $cache->getcachekey('playlogs_each',$keyparam);
			if(!empty($param['logid'])){
				$row = $cache->get($id1);
				// log_message('第一次请求之后数据走缓存');
			}else{
				$cache->remove($id1);
				$cache->remove($id2);
				// log_message('第一次清除缓存，数据走数据库');
			}
			if(empty($row)){
				$existssql = 'SELECT p.logid,p.ctime,p.ltime FROM ebh_playlogs p WHERE p.cwid='.$cwid.' and p.uid='.$uid .' and totalflag=1';
				$row = $this->db->query($existssql)->row_array();
			}
			if(!empty($row)) {	//记录存在则更新记录(总记录)
				$logid = $row['logid'];
				$wherearr = array('logid'=>$logid);
				$setarr = array('lastdate'=>SYSTIME);
				if($row['ctime'] != $ctime){
					$setarr['ctime'] = $ctime;
					$row['ctime'] = $ctime;
				}
				if($row['ltime'] < $ltime){
					$setarr['ltime'] = $ltime;
					$row['ltime'] = $ltime;
				}
				if($finished == 1)
					$setarr['finished'] = 1;
				$result = $this->db->update('ebh_playlogs',$setarr,$wherearr);
				$cache->set($id1,$row,86400);

			} else {	//不存在则生成新纪录(总记录)
				$setarr = array('cwid'=>$cwid,'uid'=>$uid,'ctime'=>$ctime,'ltime'=>$ltime,'startdate'=>(SYSTIME-$ltime),'lastdate'=>SYSTIME,'totalflag'=>1);
				if($finished == 1)
					$setarr['finished'] = 1;
				$result = $this->db->insert('ebh_playlogs',$setarr);
			}
			if(empty($param['logid'])){
				$logid = 0;
			}else{
				$logid = $param['logid'];
			}
			if(!empty($logid)){
				$row2 = $cache->get($id2);
				if(empty($row2)){
					$existssql_one = 'SELECT p.logid,p.ctime,p.ltime FROM ebh_playlogs p WHERE p.cwid='.$cwid.' and p.uid='.$uid .' and totalflag=0 and p.logid='.$logid;
					$row2 = $this->db->query($existssql_one)->row_array();
				}
			}
			if(!empty($row2)) {	//记录存在则更新记录(每次听课单条记录)
				$logid = $row2['logid'];
				$wherearr = array('logid'=>$logid);
				$setarr2 = array('lastdate'=>SYSTIME);

				if($row2['ctime'] != $ctime){
					$setarr2['ctime'] = $ctime;
					$row2['ctime'] = $ctime;
				}
				if($row2['ltime'] < $ltime){
					$setarr2['ltime'] = $ltime;
					$row2['ltime'] = $ltime;
				}
				if($finished == 1)
					$setarr2['finished'] = 1;
				$result2 = $this->db->update('ebh_playlogs',$setarr2,$wherearr);
				// if($result2){
					$cache->set($id2,$row2,86400);
				// }
			} else {	//不存在则生成新纪录(每次听课单条记录)
				$setarr2 = array('cwid'=>$cwid,'uid'=>$uid,'ctime'=>$ctime,'ltime'=>$ltime,'startdate'=>(SYSTIME-$ltime),'lastdate'=>SYSTIME,'totalflag'=>0);
				if($finished == 1)
					$setarr2['finished'] = 1;
				$logid = $this->db->insert('ebh_playlogs',$setarr2);
			}
			return $logid;
		}

		//确保ctime正确性
		private function _ensureCtime($cwid = 0,$ctime = 0) {
			$sql = 'select cwlength from ebh_coursewares  where cwid = '.$cwid.' limit 1';
			$course = $this->db->query($sql)->row_array();
			if(empty($course)) {
				log_message('ensureCtim出错 cwid:' .$cwid.' 对应课件不存在');
			}
			return $course['cwlength'] > 0 ? $course['cwlength'] : $ctime;
		}
	}