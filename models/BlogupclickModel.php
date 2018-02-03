<?php
/**
 *日志点赞模型类
 */
class BlogupclickModel extends CModel {
	private $blogmodel = NULL;
	private $redis = null;
	private $snsdb = null;
	const LIST_LEN = 50;
	
	public function __construct(){
		parent::__construct();
		$this->snsdb = Ebh::app()->getOtherDb("snsdb");
		$this->blogmodel = $this->model('Blog');
		$this->redis = Ebh::app()->getCache('cache_redis');
	}
	//添加一条
	public function add($param){
		$setarr = array();
		if(!empty($param['uid'])){
			$setarr['uid'] = $param['uid'];
		}
		if(!empty($param['bid'])){
			$setarr['bid'] = $param['bid'];
		}
		if(!empty($param['dateline'])){
			$setarr['dateline'] = $param['dateline'];
		}
		$upid =  $this->snsdb->insert("ebh_sns_blogups",$setarr);
		if($upid>0){
			$where['bid'] = $param['bid'];
			$sparam['upcount'] = 'upcount + 1';
			$this->blogmodel->update(array(),$where,$sparam);
		}
		return $upid;
	}

	/**
	 * 验证是否点过赞
	 */
	public function checkclicked($uid,$bid){
		$key = $this->getrediskey("blogup_list_".$bid);
		$list = $this->redis->lrange($key,0,-1);
		foreach($list as $uparr){
			$uparrs = unserialize($uparr);
			if(!empty($uparrs)){
				if(($uparrs['uid']==$uid)&&($uparrs['bid']==$bid)){
					return true;
				}
			}
		}
		$sql = "select count(*) count from ebh_sns_blogups where uid = $uid and bid = $bid";
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
		$key = $this->getrediskey("blogup_list_".$param['bid']);
		$listlen = $this->redis->llen($key);
		//dump($listlen);
		if(($listlen>=0) && ($listlen<self::LIST_LEN)){
			//存链表
			$updata = array(
				'uid'=>$param['uid'],
				'bid'=>$param['bid'],
				'dateline'=>$param['dateline']		
			);
			$ret = $this->redis->rpush($key,serialize($updata));
		}else{
			//先量表同步mysql 后存链表
			$listdata =$this->redis->lrange($key,0,self::LIST_LEN);
			$msql = "INSERT INTO `ebh_sns_blogups` (`uid`,`fid`,`dateline`) VALUES ";
			foreach($listdata as $data){
				$ldata =  unserialize($data);
				$uid = $ldata['uid'];
				$bid = $ldata['bid'];
				$dateline = $ldata['dateline'];
				$msql .= "($uid,$bid,$dateline),";
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
					'bid'=>$param['bid'],
					'dateline'=>$param['dateline']
			);
			$ret = $this->redis->rpush($key,serialize($updata));
		}
		
		if($ret>0){
			$where['bid'] = $param['bid'];
			$sparam['upcount'] = 'upcount + 1';
			$this->blogmodel->update(array(),$where,$sparam);
		}
		
		return $ret;
	}
	
}