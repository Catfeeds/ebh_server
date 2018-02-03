<?php
/*
积分
*/
class CreditModel extends CModel{
	/*
	会员积分明细列表
	@param int $uid
	@param array $param
	@return array 
	*/
	public function getCreditList($param){
		$sql = 'select u.username,c.credit,c.detail,c.dateline,i.rulename,i.action,i.description
		from ebh_creditlogs c
		left join ebh_users u on c.uid = u.uid
		left join ebh_creditrules i on c.ruleid = i.ruleid 
		where c.toid='.$param['toid'];
		$sql.= ' order by c.logid desc';
		if(!empty($param['limit']))
			$sql.= ' limit '.$param['limit'];
		else{
			if(empty($param['page']) || $param['page'] < 1)
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
	积分记录数量
	@param int $uid
	*/
	public function getUserCreditCount($param){
		$wherearr = array();
		$sql = 'select count(*) count from ebh_creditlogs';
		if(!empty($param['uid']))
			$wherearr[]= 'uid='.$param['uid'];
		if(!empty($param['toid']))
			$wherearr[]= 'toid='.$param['toid'];
		if(!empty($param['ruleid']))
			$wherearr[]= 'ruleid='.$param['ruleid'];
		if(!empty($param['credit']))
			$wherearr[]= 'credit='.$param['credit'];
		if(!empty($param['dateline']))//特殊条件
			$wherearr[]= $param['dateline'];
		if(!empty($param['type']))
			$wherearr[]= 'type='.$param['type'];
		if(!empty($param['crid']))
			$wherearr[] = 'crid='.$param['crid'];
		if(!empty($param['isact']))
			$wherearr[] = 'isact='.$param['isact'];
		if(!empty($param['datefrom']))
			$wherearr[] = 'dateline>'.$param['datefrom'];
		if(!empty($param['dateto']))
			$wherearr[] = 'dateline<='.$param['dateto'];
		$sql.= ' where '.implode(' AND ',$wherearr);
		// log_message($sql);
		// echo $sql;
		$count = $this->db->query($sql)->row_array();
		return $count['count'];
	}
	/*
	积分兑换记录
	@param array $param
	*/
	public function getOrderList($param){
		$sql = 'SELECT o.*,p.productname,p.image,p.credit FROM ebh_orders o left join ebh_products p ON o.pid=p.productid WHERE o.uid = '.$param['uid'].' ORDER BY o.oid desc ';
		if(!empty($param['limit']))
			$sql.= ' limit '.$param['limit'];
		else{
			if(empty($param['page']) || $param['page'] < 1)
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
	积分兑换数量
	*/
	public function getOrderCount($param){
		$sql = 'select count(*) count from ebh_orders where uid='.$param['uid'];
		if(!empty($param['limit']))
			$sql.= ' limit '.$param['limit'];
		else{
			if(empty($param['page']) || $param['page'] < 1)
				$page = 1;
			else
				$page = $param['page'];
			
			$pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
			$start = ($page - 1) * $pagesize;
			$sql .= ' limit ' . $start . ',' . $pagesize;
		}
		$count = $this->db->query($sql)->row_array();
		return $count['count'];
	}
	/*
	根据ruleid查看积分规则信息
	@param int $ruleid
	*/
	public function getCreditRuleInfo($ruleid){
		$sql = 'select r.rulename,r.action,r.credit,r.actiontype,r.maxaction
			from ebh_creditrules r where r.ruleid='.$ruleid;
		return $this->db->query($sql)->row_array();
	}
	/*
	添加积分日志,并修改积分
	@param array $param ruleid, toid/aid..
	*/
	public function addCreditlog_bak($param){
		if(is_numeric($param))
			$logarr['ruleid'] = $param;
		else
			$logarr['ruleid'] = $param['ruleid'];
		$user = Ebh::app()->user->getloginuser();
		if(!empty($param['uid']))
			$logarr['uid'] = $param['uid'];
		else
			$logarr['uid'] = $user['uid'];
		
		$flag = 0;
		if(!empty($param['uid'])){//指定了受分对象的
			$logarr['toid'] = $param['uid'];
		}else if(!empty($param['aid'])){//指定了答疑号的
			$sql = 'select a.uid from ebh_askanswers a 
				join ebh_askquestions q on (q.qid=a.qid)';
			$warr[] = 'a.aid='.$param['aid'];
			$warr[] = 'q.uid='.$logarr['uid'];
			$warr[] = 'q.thankcount=0';
			$sql.= ' where '.implode(' AND ',$warr);
			$temp = $this->db->query($sql)->row_array();
			$logarr['toid'] = $temp['uid'];
		}else{//没有指定，则为自己
			$logarr['toid'] = $logarr['uid'];
		}
		$ruleinfo = $this->getCreditRuleInfo($logarr['ruleid']);
		//每次都增加
		if($ruleinfo['actiontype'] == 0){
			$flag = 1;
		}
		//只一次
		elseif($ruleinfo['actiontype'] == -1){
			$wherearr['toid'] = $logarr['toid'];
			$wherearr['ruleid'] = $logarr['ruleid'];
			$logcount = $this->getUserCreditCount($wherearr);
			if($logcount>0)
				return ;
			else{
				$flag=1;
			}
		}
		//每天增加有限次数
		elseif($ruleinfo['actiontype'] == -2){
			$today = strtotime(Date('Y-m-d'));
			$wherearr['toid'] = $logarr['toid'];
			$wherearr['ruleid'] = $logarr['ruleid'];
			$wherearr['dateline'] = ' dateline>'.$today.' and dateline<'.($today+86400);
			$logcount = $this->getUserCreditCount($wherearr);
			if($logcount>=$ruleinfo['maxaction'])
				return ;
			else{
				$flag=1;
			}
		}
		//按时间段增加
		else{
			return;
		}
		
		//添加记录并增加toid的积分
		if($flag){
			if($logarr['ruleid'] == 16 && isset($param['productid']) && isset($param['credit'])){//积分兑换
				$logarr['credit'] = $param['credit'];
				$logarr['productid'] = $param['productid'];
			}
			else
				$logarr['credit'] = $ruleinfo['credit'];
			$logarr['dateline'] = SYSTIME;
			$logarr['fromip'] = Ebh::app()->getInput()->getIp();
			$this->db->insert('ebh_creditlogs',$logarr);
			$sparam = array('credit'=>'credit'.$ruleinfo['action'].$logarr['credit']);
			$this->db->update('ebh_users',array(),'uid='.$logarr['toid'],$sparam);
		}
	}
	/*
	积分规则列表
	*/
	public function getCreditRuleList(){
		$sql = 'select * from ebh_creditrules';
		return $this->db->query($sql)->list_array();
	}
	/*
	修改积分规则
	*/
	public function update($param){
		if(empty($param['ruleid']))
			return false;
		$setarr['rulename'] = $param['rulename'];
		$setarr['action'] = $param['action'];
		$setarr['credit'] = $param['credit'];
		$setarr['actiontype'] = $param['actiontype'];
		$setarr['maxaction'] = $param['maxaction'];
		$this->db->update('ebh_creditrules',$setarr,'ruleid='.$param['ruleid']);
	}
	/*
	添加积分规则
	*/
	public function insert($param){
		$setarr['rulename'] = $param['rulename'];
		$setarr['action'] = $param['action'];
		$setarr['credit'] = $param['credit'];
		$setarr['actiontype'] = $param['actiontype'];
		$setarr['maxaction'] = $param['maxaction'];
		$this->db->insert('ebh_creditrules',$setarr);
		
	}
	/*
	删除积分规则
	*/
	public function delete($ruleid){
		if(!empty($ruleid))
			return $this->db->delete('ebh_creditrules','ruleid='.$ruleid);
	}
	
	public function addRegLogs($fromuid,$stunum){
		$sql = 'select credit from ebh_creditrules where ruleid = 1';
		$res = $this->db->query($sql)->row_array();
		$credit = $res['credit'];
		
		$sql = 'insert into ebh_creditlogs (ruleid,uid,toid,credit,dateline,fromip) values ';
		$ip = getip();
		$dateline = SYSTIME;
		for($i=0;$i<$stunum;$i++){
			$uid = $fromuid + $i;
			$sql.= "(1,$uid,$uid,$credit,$dateline,'$ip'),";
		}
		$sql = rtrim($sql,',');
		$this->db->query($sql);
	}

	/*
	添加积分日志,并修改积分
	@param array $param ruleid, toid/aid..
	*/
	public function addCreditlog($param){
		if(is_numeric($param))
			$logarr['ruleid'] = $param;
		else
			$logarr['ruleid'] = $param['ruleid'];
		$user = Ebh::app()->user->getloginuser();
		$roominfo = Ebh::app()->room->getcurroom();
		if(empty($roominfo))
			$crid = 0;
		else 
			$crid = $roominfo['crid'];
		if(!empty($param['uid']))
			$logarr['uid'] = $param['uid'];
		else
			$logarr['uid'] = $user['uid'];
		
		
		
		$flag = 0;
		if(!empty($param['uid'])){//指定了受分对象的
			$logarr['toid'] = $param['uid'];
		}else if(!empty($param['aid'])){//指定了答疑号的,被采纳为最佳答案
			$sql = 'select q.uid,a.uid toid,q.title,q.qid from ebh_askanswers a 
				join ebh_askquestions q on (q.qid=a.qid)';
			$warr[] = 'a.aid='.$param['aid'];
			// $warr[] = 'q.uid='.$logarr['uid'];
			$sql.= ' where '.implode(' AND ',$warr);
			$temp = $this->db->query($sql)->row_array();
			//var_dump($sql);
			
			$logarr['uid'] = empty($param['qid']) ? $temp['qid'] : $param['qid'];//记录qid
			$logarr['toid'] = $temp['toid'];
			$logarr['type'] = 3;
			$logarr['detail'] = $temp['title'];
		}else if(!empty($param['qid'])){//指定了qid的,回答问题
			$sql = 'select q.title,q.qid from ebh_askquestions q';
			$warr[] = 'q.qid='.$param['qid'];
			$sql.= ' where '.implode(' AND ',$warr);
			$temp = $this->db->query($sql)->row_array();
			$logarr['toid'] = $logarr['uid'];
			$logarr['uid'] = $temp['qid'];
			$logarr['detail'] = $temp['title'];
			$logarr['type'] = 3;
		}else if(!empty($param['eid'])){//指定了eid的,完成作业
			$sql = 'select crid,totalscore/score*100 percent from ebh_schexams e 
					join ebh_schexamanswers a on e.eid=a.eid ';
			$warr[] = 'e.eid='.$param['eid'];
			$warr[] = 'a.uid='.$logarr['uid'];
			$sql.= ' where '.implode(' AND ',$warr);
			$temp = $this->db->query($sql)->row_array();
			if($temp['percent']==100)
				$param['credit'] = 10;
			elseif($temp['percent']>=80)
				$param['credit'] = 7;
			elseif($temp['percent']>=60)
				$param['credit'] = 6;
			else
				$param['credit'] = 5;
			$logarr['crid'] = $temp['crid'];
			$logarr['toid'] = $logarr['uid'];
			$logarr['detail'] = $param['detail'];
			$logarr['type'] = 4;
		}elseif(!empty($param['cwid']) && $param['ruleid'] != 5){
			$sql = 'select cw.title,cw.cwid,cw.uid from ebh_coursewares cw';
			$warr[] = 'cw.cwid='.$param['cwid'];
			$sql.= ' where '.implode(' AND ',$warr);
			$temp = $this->db->query($sql)->row_array();
			$logarr['uid'] = $param['cwid'];
			$logarr['detail'] = $temp['title'];
			$logarr['toid'] = $temp['uid'];
			// $logarr['type'] = 0;
		}else{//没有指定，则为自己
			$logarr['toid'] = $logarr['uid'];
		}
		$ruleinfo = $this->getCreditRuleInfo($logarr['ruleid']);
		//每次都增加
		if($ruleinfo['actiontype'] == 0){
			$flag = 1;
		}
		//只一次
		elseif($ruleinfo['actiontype'] == -1){
			$wherearr['toid'] = $logarr['toid'];
			$wherearr['ruleid'] = $logarr['ruleid'];
			$logcount = $this->getUserCreditCount($wherearr);
			if($logcount>0)
				return ;
			else{
				$flag=1;
			}
		}
		//每天增加有限次数
		elseif($ruleinfo['actiontype'] == -2){
			$today = strtotime(Date('Y-m-d'));
			$wherearr['toid'] = $logarr['toid'];
			$wherearr['ruleid'] = $logarr['ruleid'];
			$wherearr['dateline'] = ' dateline>'.$today.' and dateline<'.($today+86400);
			$logcount = $this->getUserCreditCount($wherearr);
			if($logcount>=$ruleinfo['maxaction']){
				if(!empty($param['nocheck'])&&($param['nocheck']==true)){//抽奖再来一次不需要检测最大次数;权限由控制器给出
					$flag=1;
				}else{
					return ;
				}
				
			}else{
				$uniqueconfirm = 0;
				if(!empty($param['cwid'])){
					$wherearr['uid'] = $param['cwid'];
					$wherearr['type'] = 2;
					$uniqueconfirm = 1;
				}elseif(!empty($param['qid'])){
					$wherearr['uid'] = $param['qid'];
					$wherearr['type'] = 3;
					$uniqueconfirm = 1;
				}
				if($uniqueconfirm){
					$logcount = $this->getUserCreditCount($wherearr);
					if($logcount>0)
						return;
					else{
						$logarr['type'] = $wherearr['type'];
						$logarr['uid'] = $wherearr['uid'];
					}
				}
				$flag=1;
			}
		}
		
		//课件特殊处理,学一次只得一次积分,改天再学也没有积分
		elseif($ruleinfo['actiontype'] == 1 && !empty($param['cwid'])){
			$wherearr = array();
			$wherearr['toid'] = $logarr['toid'];
			$wherearr['ruleid'] = $logarr['ruleid'];
			$wherearr['uid'] = $param['cwid'];
			$wherearr['type'] = 2;
			$logcount = $this->getUserCreditCount($wherearr);
			if($logcount>0)
				return;
			else{
				$logarr['type'] = $wherearr['type'];
				$logarr['uid'] = $wherearr['uid'];
				$flag = 1;
			}
		}
		//按时间段增加
		else{
			return;
		}
		
		//添加记录并增加toid的积分
		if($flag){
			if($logarr['ruleid'] == 16 && isset($param['productid']) && isset($param['credit'])){//积分兑换
				$logarr['credit'] = $param['credit'];
				$logarr['productid'] = $param['productid'];
			}
			elseif(isset($param['credit']))
				$logarr['credit'] = $param['credit'];
			else
				$logarr['credit'] = $ruleinfo['credit'];
			$logarr['dateline'] = SYSTIME;
			$logarr['fromip'] = getip();
			if(!empty($param['detail']))
				$logarr['detail'] = $param['detail'];
			
			//活动id添加crid
			$actids = array(5,7,13,14,15,21);
			if(in_array($logarr['ruleid'],$actids)){
				$logarr['crid'] = empty($logarr['crid'])?$crid:$logarr['crid'];
				$tsql = 'select logid from ebh_studentactivitys sa 
						join ebh_activitys a on sa.aid=a.aid';
				$twhere[] = 'uid='.$logarr['toid'];
				$twhere[] = 'sa.crid='.$logarr['crid'];
				$twhere[] = 'endtime+86400>'.SYSTIME;//截止日的23:59
				$twhere[] = 'starttime<='.SYSTIME;
				$tsql .= ' where '.implode(' AND ',$twhere);
				$actloglist = $this->db->query($tsql)->list_array();
				// var_dump($actloglist);
				// log_message($tsql);
				if($actloglist){
					$logarr['isact'] = 1;
					$actlogids = '';
					foreach($actloglist as $actlog){
						$actlogids .= $actlog['logid'].',';
					}
					$actlogids = rtrim($actlogids,',');
					$tuwhere = 'logid in ('.$actlogids.')';
					$this->db->update('ebh_studentactivitys',array(),$tuwhere,array('credit'=>'credit+'.$logarr['credit']));
				
				}
			}
			// exit;
			
			
			$res = $this->db->insert('ebh_creditlogs',$logarr);
			$sparam = array('credit'=>'credit'.$ruleinfo['action'].$logarr['credit']);
			$this->db->update('ebh_users',array(),'uid='.$logarr['toid'],$sparam);
			
			if($ruleinfo['action'] == '+' && $logarr['ruleid'] != 29){
				$redis = $redis = Ebh::app()->getCache('cache_redis');
				$crcache = $redis->hget('credit',$crid);
				if(!is_array($crcache))
					$crcache = unserialize($crcache);
				$day = Date('Y/m/d',SYSTIME);
				if(isset($crcache[$day]))
					$crcache[$day] += $logarr['credit'];
				else
					$crcache[$day] = $logarr['credit'];
				if(!empty($crid))
					$redis->hset('credit',$crid,$crcache);
			}
			return $res;
		}
	}
}
?>