<?php
	/**
	* 学习记录model对应的ebh_playlog表	
	* 学生每次播放课件完成后都会添加学习时间记录
	*/
	class PlaylogModel extends CModel{
		/**
		 * 根据参数获取对应的学习记录列表
		 * @param array $param
		 * @return array
		 */
		public function getList($param=array()){
			$sql = 'select p.cwid,p.ctime,c.cwurl,p.ltime,p.startdate,p.lastdate,c.title from ebh_playlogs p '.
					'join ebh_coursewares c on (p.cwid = c.cwid) '.
					'join ebh_roomcourses rc on (rc.cwid = p.cwid) ';
			$wherearr = array();
			if(!empty($param['uid']))
				$wherearr[] = 'p.uid='.$param['uid'];
			if(!empty($param['crid']))
				$wherearr[] = 'rc.crid='.$param['crid'];
			if(isset($param['totalflag']))
				$wherearr[] = 'p.totalflag='.$param['totalflag'];
			if(!empty($param['startDate']))
				$wherearr[] = 'p.lastdate>='.$param['startDate'];
			if(!empty($param['endDate']))
				$wherearr[] = 'p.lastdate<'.$param['endDate'];
			if(!empty($param['q'])){
				$wherearr[] = ' c.title like \'%'.$param['q'].'%\'';
			}
			if(!empty($wherearr)){
				$sql.=' WHERE '.implode(' AND ',$wherearr);
			}
			if(!empty($param['order'])){
				$sql.=' order by '.$param['order'];
			}else{
				$sql.=' order by p.lastdate desc ';
			}
			if(!empty($param['limit'])){
				$sql.= ' limit '.$param['limit'];
			}else{
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
		/**
		 * 根据参数获取对应的学习记录条数
		 * @param array $param
		 * @return int
		 */
		public function getListCount($param){
			$count = 0;
			$sql = 'select count(*) count from ebh_playlogs p '.
					'join ebh_coursewares c on (p.cwid = c.cwid) '.
					'join ebh_roomcourses rc on (rc.cwid = p.cwid) ';
			$wherearr = array();
			if(!empty($param['uid']))
				$wherearr[] = 'p.uid='.$param['uid'];
			if(!empty($param['crid']))
				$wherearr[] = 'rc.crid='.$param['crid'];
			if(!empty($param['startDate']))
				$wherearr[] = 'p.lastdate>='.$param['startDate'];
			if(!empty($param['endDate']))
				$wherearr[] = 'p.lastdate<'.$param['endDate'];
			if(!empty($param['q'])){
				$wherearr[] = ' c.title like \'%'.$param['q'].'%\'';
			}
			if(!empty($wherearr)){
				$sql.=' WHERE '.implode(' AND ',$wherearr);
			}
			$row = $this->db->query($sql)->row_array();
			if(!empty($row))
				$count = $row['count'];
			return $count;
		}

		//获取学生对应课件的播放记录
		public function getStuLog($param = array()){
			if(empty($param)){
				return array();
			}
			$sql='select p.uid,p.cwid,p.totalflag,p.finished from ebh_playlogs p';
			$wherearr = array();
			if(!empty($param['uid'])){
				$wherearr[] = 'p.uid = '.$param['uid'];
			}
			if(!empty($param['cwid'])){
				$wherearr[] = 'p.cwid = '.$param['cwid'];
			}
			if(!empty($param['finished'])){
				$wherearr[] = 'p.finished = '.$param['finished'];	
			}
			if(!empty($param['checkTime'])){
				$wherearr[] = 'p.ctime <= p.ltime';
			}
			if(!empty($param['totalflag'])){
				$wherearr[] = 'p.totalflag = '.$param['totalflag'];
			}
			if(!empty($wherearr)){
				$sql.=' WHERE '.implode(' AND ',$wherearr);
			}
			return $this->db->query($sql)->list_array();
		}
	}