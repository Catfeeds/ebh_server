<?php
/**
 *sns点赞模型类
 */
class UpclickModel extends CModel {
	private $outboxmodel = NULL;
	private $redis = null;
	private $snsdb = null;
	const LIST_LEN = 50;
	
	public function __construct(){
		parent::__construct();
		$this->snsdb = Ebh::app()->getOtherDb("snsdb");
		$this->outboxmodel = $this->model('Outbox');
		$this->redis = Ebh::app()->getCache('cache_redis');
	}
	//添加一条
	public function add($param){
		$setarr = array();
		if(!empty($param['uid'])){
			$setarr['uid'] = $param['uid'];
		}
		if(!empty($param['fid'])){
			$setarr['fid'] = $param['fid'];
		}

		if(!empty($param['dateline'])){
			$setarr['dateline'] = $param['dateline'];
		}
		
		$upid =  $this->snsdb->insert("ebh_sns_ups",$setarr);
		if($upid>0){
			$this->outboxmodel->update(array('upcount'=>true),$param['fid']);
		}
		return $upid;
	}

	/**
	 * 验证是否点过赞
	 */
	public function checkclicked($uid,$fid){
		$key = $this->getrediskey("up_list_".$fid);
		$list = $this->redis->lrange($key,0,-1);
		foreach($list as $uparr){
			$uparrs = unserialize($uparr);
			if(!empty($uparrs)){
				if(($uparrs['uid']==$uid)&&($uparrs['fid']==$fid)){
					return true;
				}
			}
		}
		$sql = "select count(*) count from ebh_sns_ups where uid = $uid and fid = $fid";
		$row = $this->snsdb->query($sql)->row_array();
		if($row['count']>0){
			return true;
		}
		return false;
	}
	/**
	 * 从链表添加
	 * @param unknown $param
	 */
	public function addredislist($param){
		$key = $this->getrediskey("up_list_".$param['fid']);
		$listlen = $this->redis->llen($key);
		//dump($listlen);
		if(($listlen>=0) && ($listlen<self::LIST_LEN)){
			//存链表
			$updata = array(
				'uid'=>$param['uid'],
				'fid'=>$param['fid'],
				'dateline'=>$param['dateline']		
			);
			$ret = $this->redis->rpush($key,serialize($updata));

		}else{
			//先量表同步mysql 后存链表
			$listdata =$this->redis->lrange($key,0,self::LIST_LEN);
			$msql = "INSERT INTO `ebh_sns_ups`(`uid`,`fid`,`dateline`)VALUES";
			foreach($listdata as $data){
				$ldata =  unserialize($data);
				$uid = $ldata['uid'];
				$fid = $ldata['fid'];
				$dateline = $ldata['dateline'];
				$msql.="($uid,$fid,$dateline),";
			}
			$msql = rtrim($msql,",");
			//echo $msql;
			$mk = $this->snsdb->simple_query($msql);
			if($mk){
				$this->redis->del($key);
			}
			//存链表
			$updata = array(
					'uid'=>$param['uid'],
					'fid'=>$param['fid'],
					'dateline'=>$param['dateline']
			);
			$ret = $this->redis->rpush($key,serialize($updata));
		}
		
		if($ret>0){
			$this->outboxmodel->update(array('upcount'=>true),$param['fid']);
		}
		
		return $ret;
	}
	//获取点赞uid
	public function getbatchups($fids){
		if(empty($fids)){
			return false; 
		}
		$upuids = array();
		foreach ($fids as $val){
			$key = $this->getrediskey("up_list_".$val);
			$uplist = $this->redis->lrange($key,0,-1);
			foreach ($uplist as $key=>$up){
				$uparrs = unserialize($up);
				if(!empty($uparrs)){
					if(@!in_array($uparrs['uid'],$uparrs[$val])){
						$upuids[$val][] = $uparrs['uid'];
					}
				}
			}
		}
		$sql = "select fid, uid from `ebh_sns_ups` where fid in (".implode(',',$fids).")";
		$rows = $this->snsdb->query($sql)->list_array();
		if(!empty($rows)){
			foreach ($rows as $item){
				$akey = $item['fid'];
				if(@!in_array($item['uid'],$upuids[$akey])){
					$upuids[$akey][] = $item['uid'];
				}
			}
		}
		
		//获取点赞前三个用户的基本信息
		$usermodel = $this->model('User');
		$uparr = array();
		if(!empty($upuids)){
			foreach ($upuids as $fk=>$uids){
				$duids = array_slice($uids,0,3);
				$users = $usermodel->getUserInfoByUid($duids);
				$uparr[$fk]['users'] = $users;
			}
		}
		return $uparr;
	}
}