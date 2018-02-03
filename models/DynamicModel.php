<?php
/**
 *feeds相关信息
 *
 */
class DynamicModel extends CModel {
	private $feedsmodel = null;
	private $inboxmodel = null;
	private $outboxmodel = null;
	private $followmodel = null;
	private $baseinfomodel = null;
	private $commentmodel = null;
	private $delmodel = null;
	private $upclickmodel = null;
	private $roommodel = null;
	private $blackmodel = null;
	private $redis = null;
	private $snsdb = null;
	
	public function __construct(){
		parent::__construct();
		$this->feedsmodel = $this->model("Feeds");
		$this->inboxmodel = $this->model("Inbox");
		$this->outboxmodel = $this->model("Outbox");
		$this->followmodel = $this->model("Follow");
		$this->baseinfomodel =  $this->model("Baseinfos");
		$this->commentmodel = $this->model('Comment');
		$this->delmodel = $this->model('Dels');
		$this->upclickmodel = $this->model('Upclick');
		$this->roommodel = $this->model('Classroomfeeds');
		$this->blackmodel = $this->model('Blacklist');
		$this->snsdb = Ebh::app()->getOtherDb("snsdb");
		$this->redis = Ebh::app()->getCache("cache_redis");
	}
	
	//获取用户黑名单列表
	private function getblacklist($uid){
		$key = 'blacklist_'.$uid.'_'.md5($uid);
		$cache = $this->redis;
		$blacklist = array();
		$data = $cache->lrange($key,0,-1);
		if(!empty($data)){
			$blacklist = $data;
		}else{
			$blacklistmodel = $this->blackmodel;
			$blacklist = $blacklistmodel->getlist(array('fromuid'=>$uid,'state'=>0));
			if(!empty($blacklist)){
				foreach ($blacklist as $item){
					$blacklist[] = $item['touid'];
				}
			}
		}
		return $blacklist;
	}
	
	/**
	 * 发布  一条记录 产生动态消息
	 * @param unknown $feeds
	 * @param unknown $uid
	 */
	public function publish($feeds,$uid){
		//加个 ip来源
		$ip = Ebh::app()->getInput()->getip();
		$feeds['ip'] = $ip;
		//写入feeds
		$fid  = $this->feedsmodel->add($feeds);
		//写入发件箱outbox
		if($fid>0){
			$outbox = array(
					'fid'=>$fid,
					'uid'=>	$uid,
					'ip'=>$ip,
					'dateline'=>time()
			);
			$this->outboxmodel->add($outbox);
			
			//插入网校/班级动态表
			$this->addclassandroomfeeds($uid,$fid);
		}
		
		return $fid;
	}
	/**
	 * 转发  产生一条新动态
	 * @param unknown $fid
	 * @param unknown $uid
	 * @param unknown $content
	 */
	public function transfer($fid,$uid,$content){
		$feeds  = $this->feedsmodel->getfeedsbyfid($fid);
		$feeds = $this->baseinfomodel->getuserinfo(array(0=>$feeds),"fromuid");
		$feeds = $feeds[0];
		
		if($feeds['iszhuan']){
			$refermessage = json_decode($feeds['message'],true);
			$refer =  $refermessage['refer'];
			$refer_nickname = $refermessage['referuser']['realname'];
			$refer_face = $refermessage['referuser']['face'];
			$refer_uid = $refermessage['referuser']['uid'];
		}else{
			$refer_nickname = !empty($feeds['realname'])?$feeds['realname']:$feeds['username'];
			$refer_face = $feeds['face'];
			$refer_uid = $feeds['fromuid'];
			$refer = json_decode($feeds['message'],true);
		}
		
		$ip = Ebh::app()->getInput()->getip();
		$newfeeds = array(
				'fromuid'=>$uid,
				'message'=>json_encode(array(
						'content'=>$content,
						'images'=>'',
						'type'=>'mood',
						'refer'=>$refer,
						'referuser'=>array(
							'realname'=>$refer_nickname,
							'face'=>$refer_face,
							'uid'=>$refer_uid,	
							),
				)),
				'category'=>$feeds['category'],
				'toid'=>$feeds['toid'],
				'dateline'=>time(),
				'ip'=>$ip
		);
		
		//写入feeds
		$newfid  = $this->feedsmodel->add($newfeeds);
		//写入发件箱outbox
		if($newfid>0){
			$outbox = array(
				'fid'=>$newfid,
				'uid'=>	$uid,
				'pfid'=>$fid,
				'tfid'=>($feeds['tfid']>0)?$feeds['tfid']:$fid,
				'iszhuan'=>1,	
				'dateline'=>time(),
				'ip'=>$ip		
			);
			$outid = $this->outboxmodel->add($outbox);
			//插入网校/班级动态表
			$this->addclassandroomfeeds($uid,$newfid);
			
			if($outid>0){
				//转发成功 更新转发次数
				$this->outboxmodel->update(array('zhcount'=>true),$outbox['tfid']);//顶级
				if($outbox['tfid']!=$fid){
					$this->outboxmodel->update(array('zhcount'=>true),$fid);//父级
				}
			}
			
			//自己转发自己的不通知
			if($uid != $feeds['fromuid']){
				$ntModel = $this->model('Notices');
				//发布一条通知
				$notice = array(
						'fromuid'=>	$uid,
						'touid'=>$feeds['fromuid'],
						'message'=>json_encode(
								array(
										'fid'=>$feeds['fid'],
										'content'=>$content
								)
						),
						'type'=>4,
						'category'=>1,
						'toid'=>$feeds['toid'],
						'dateline'=>time(),
				);
				$ntModel->add($notice);
				//更新通知数
				$baseModel = $this->model('Baseinfos');
				$baseModel->updateone(array(),$feeds['fromuid'],array('nfcount'=>'nfcount + 1'));
			}
		}
		return $newfid;
	}
	
	/**
	 * 网校/班级动态表分离
	 * 分别向所在网校插入动态
	 * 分别向所在班级插入动态
	 * @param $uid 用户uid
	 * @param $fid 新产生的动态fid
	 */
	public function addclassandroomfeeds($uid,$fid){
		//写入网校动态
		$cridarr = $this->baseinfomodel->getusercrid($uid);
		if(!empty($cridarr)){
			foreach($cridarr as $crid){
				$roomfeeds = array(
						'uid'=>$uid,
						'fid'=>$fid,
						'crid'=>$crid,
						'dateline'=>SYSTIME
				);
				//var_dump($roomfeeds);exit;
				$this->roommodel->addroomfeeds($roomfeeds);
			}
		}
				
		//写入班级动态
		$classidarr = $this->baseinfomodel->getuserclassid($uid);	
		if(!empty($classidarr)){
			foreach($classidarr as $classid){
				$classfeeds = array(
						'uid'=>$uid,
						'classid'=>$classid,
						'fid'=>$fid,
						'dateline'=>SYSTIME
				);
				$this->roommodel->addclassfeeds($classfeeds);
			}
		}
	}
	
	/**
	 * 获取用户动态信息
	 * @param unknown $uid
	 * @param number $start 分别对应取向上/向下数据最近的那条记录的fid
	 * @param number $len
	 * @param string $flag 默认old取以前的旧的 ,new 取当前最新的
	 * @param string $type 默认读取所有all 值包括 all,myfollow, myclass,myschool
	 * @return feedslist
	 */
	public function getFeeds($uid,$len=10,$flag="old",$start=0,$type="all"){
		$feedslist = array();
		switch ($type){
			case 'all':
				$feedslist = $this->getAllFeeds($uid,$len,$flag,$start);
				break;
			case 'myindex':
				$feedslist = $this->getMyFeeds($uid,$len,$flag,$start);
				break;
			case 'myfollow':
				$feedslist = $this->getFollowFeeds($uid,$len,$flag,$start);
				break;
			case 'myclass':
				$feedslist = $this->getClassFeeds($uid,$len,$flag,$start);
				break;
			case 'myschool':
				$feedslist = $this->getRoomFeeds($uid,$len,$flag,$start);
				break;
					
		}
		return  $feedslist;
	}
	
	/**
	 * 获取全部动态
	 *
	 */
	public function getAllFeeds($uid,$len=10,$flag="old",$start=0){
		$cidarr = $this->baseinfomodel->getusercrid($uid);
		$fuidarr =$this->getfollowuidarr($uid,true);
		$blacklist = $this->getblacklist($uid);
		
		if($flag=="old"){//查看以前的n条记录
			$uidin = empty($fuidarr)?" o.fid <0 ":" f.fromuid in ( ".implode(",", $fuidarr)." ) ";
			$cridin = empty($cidarr)?" r.fid < 0 ":" r.crid in ( ".implode(",", $cidarr)." ) ";
			$condition = ($start!=0)? " and r.fid< $start and r.status = 0 " : " and r.status = 0 and f.status = 0";
			$condition2 = ($start!=0)? " and o.fid < $start and f.status = 0 " : " and f.status = 0 ";
			$orderby = " order by fid DESC ";
			$limit = " limit $len ";
			//过滤加入黑名单用户的feeds
			if(!empty($blacklist)){
				$condition .= " and r.uid not in (".implode(',', $blacklist).") ";
				$condition2 .= " and o.uid not in (".implode(',', $blacklist).") ";
			}
			
			$sql = "(
			select distinct r.fid,o.outid,o.pfid,o.tfid,o.iszhuan,o.uid,o.upcount,o.cmcount,o.zhcount,o.dateline,f.fromuid,f.message,f.category,f.toid from ebh_sns_roomfeeds r
			left join ebh_sns_feeds f on r.fid = f.fid
			left join ebh_sns_outboxs o on o.fid = f.fid
			where {$cridin}{$condition} )
			union
			(select o.fid,o.outid,o.pfid,o.tfid,o.iszhuan,o.uid,o.upcount,o.cmcount,o.zhcount,o.dateline,f.fromuid,f.message,f.category,f.toid from  ebh_sns_outboxs o
			left join ebh_sns_feeds f on o.fid = f.fid
			where {$uidin}{$condition2})
			{$orderby}{$limit}
			";
	
		}elseif($flag=="new"){//查看最新的n条记录
			$uidin = empty($fuidarr)?" o.fid <0 ":" f.fromuid in ( ".implode(",", $fuidarr)." ) ";
			$cridin = empty($cidarr)?" r.fid < 0 ":" r.crid in ( ".implode(",", $cidarr)." ) ";
			$condition = " and r.fid > $start and r.status = 0 " ;
				$condition2 = ($start!=0)? " and o.fid > $start and f.status = 0 " : " and f.status = 0 ";
				$orderby = " order by fid DESC ";
						$limit = " limit $len ";
			
			//过滤加入黑名单用户的feeds
			if(!empty($blacklist)){
				$condition .= " and r.uid not in (".implode(',', $blacklist).") ";
				$condition2 .= " and o.uid not in (".implode(',', $blacklist).") ";
			}
			$sql = "(
				select distinct r.fid,o.outid,o.pfid,o.tfid,o.iszhuan,o.uid,o.upcount,o.cmcount,o.zhcount,o.dateline,f.fromuid,f.message,f.category,f.toid from ebh_sns_roomfeeds r
				left join ebh_sns_feeds f on r.fid = f.fid
				left join ebh_sns_outboxs o on o.fid = f.fid
				where {$cridin}{$condition} )
				union
				(select o.fid,o.outid,o.pfid,o.tfid,o.iszhuan,o.uid,o.upcount,o.cmcount,o.zhcount,o.dateline,f.fromuid,f.message,f.category,f.toid from  ebh_sns_outboxs o
				left join ebh_sns_feeds f on o.fid = f.fid
				where {$uidin}{$condition2})
				{$orderby}{$limit}
				";
			}
			//查询符合条件的feeslist
			//echo $sql;
			$feeds = $this->snsdb->query($sql)->list_array();
			//组装评论
			$feeds = $this->getfeedswithreplys($feeds);
			//feeds信息格式化
			$feeds = $this->feedsformat($feeds,$uid);
			
			return $feeds;
	}
	
	/**
	 * 获取关注动态
	 */
	public function getFollowFeeds($uid,$len=10,$flag="old",$start=0){
		$blacklist = $this->getblacklist($uid);
		$fuidarr =$this->getfollowuidarr($uid);
		if($flag=="old"){//查看以前的n条记录
			$uidin = empty($fuidarr)?" o.fid <0 ":" o.uid in ( ".implode(",", $fuidarr)." ) ";
			$condition = ($start!=0)? " and o.fid < $start and f.status = 0 " : " and f.status = 0 ";
			$orderby = " order by o.fid DESC ";
			$limit = " limit $len ";
			if(!empty($blacklist)){
				$condition .= " and o.uid not in (".implode(',',$blacklist).")";
			}
			
			$sql = "select o.outid,o.fid,o.pfid,o.tfid,o.iszhuan,o.uid,o.upcount,o.cmcount,o.zhcount,o.dateline,f.fromuid,f.message,f.category,f.toid from  ebh_sns_outboxs o
			left join ebh_sns_feeds f on o.fid = f.fid
			where {$uidin}{$condition}{$orderby}{$limit}
			";
	
		}elseif($flag=="new"){//查看最新的n条记录
				$uidin = empty($fuidarr)?" o.fid <0  ":" o.uid in ( ".implode(",", $fuidarr)." ) ";
				$condition = " and o.fid > $start and f.status = 0 " ;
				$orderby = " order by o.fid DESC ";
				$limit = " limit $len ";
				if(!empty($blacklist)){
					$condition .= " and o.uid not in (".implode(',',$blacklist).")";
				}
				
				$sql = "select o.outid,o.fid,o.pfid,o.tfid,o.iszhuan,o.uid,o.upcount,o.cmcount,o.zhcount,o.dateline,f.fromuid,f.message,f.category,f.toid from  ebh_sns_outboxs o
				left join ebh_sns_feeds f on o.fid = f.fid
				where {$uidin}{$condition}{$orderby}{$limit}
				";
				}
				//echo $sql;
				//查询符合条件的feeslist
				$feeds = $this->snsdb->query($sql)->list_array();
				//组装评论
				$feeds = $this->getfeedswithreplys($feeds);
				//feeds信息格式化
				$feeds = $this->feedsformat($feeds);
				return $feeds;
	}
	
	/**
	* 获取我的班级
	*/
	public function getClassFeeds($uid,$len=10,$flag="old",$start=0){
		$blacklist = $this->getblacklist($uid);
		$classidarr = $this->baseinfomodel->getuserclassid($uid);
		if($flag=="old"){//查看以前的n条记录
			$classidin = empty($classidarr)?" c.fid < 0 ":" c.classid in ( ".implode(",", $classidarr)." ) ";
			$condition = ($start!=0)? " and c.fid< $start and c.status = 0 " : " and c.status = 0 ";
			$orderby = " order by c.fid DESC ";
			$limit = " limit $len ";
			if(!empty($blacklist)){
				$condition .= " and c.uid not in (".implode(',',$blacklist).")";
			}
			$sql = "select distinct c.fid,o.outid,o.fid,o.pfid,o.tfid,o.iszhuan,o.uid,o.upcount,o.cmcount,o.zhcount,o.dateline,f.fromuid,f.message,f.category,f.toid from ebh_sns_classfeeds c
			left join ebh_sns_feeds f on c.fid = f.fid
			left join ebh_sns_outboxs o on o.fid = f.fid
			where {$classidin}{$condition}{$orderby}{$limit}
			";
		}elseif($flag=="new"){//查看最新的n条记录
			$classidin = empty($classidarr)?" c.fid < 0 ":" c.classid in ( ".implode(",", $classidarr)." ) ";
			$condition = " and c.fid > $start and c.status = 0 " ;
			$orderby = " order by c.fid DESC ";
			$limit = " limit $len ";
			if(!empty($blacklist)){
				$condition .= " and c.uid not in (".implode(',',$blacklist).")";
			}
			
			$sql = "select distinct c.fid,o.outid,o.fid,o.pfid,o.tfid,o.iszhuan,o.uid,o.upcount,o.cmcount,o.zhcount,o.dateline,f.fromuid,f.message,f.category,f.toid from  ebh_sns_classfeeds c
			left join ebh_sns_feeds f on c.fid = f.fid
			left join ebh_sns_outboxs o on o.fid = f.fid
			where {$classidin}{$condition}{$orderby}{$limit}
			";
		}
		//查询符合条件的feeslist
		//echo $sql;
		$feeds = $this->snsdb->query($sql)->list_array();
		//组装评论
		$feeds = $this->getfeedswithreplys($feeds);
		//feeds信息格式化
		$feeds = $this->feedsformat($feeds);
		return $feeds;
	}
	
	/**
	* 获取我的学校
	*
	*/
	public function getRoomFeeds($uid,$len=10,$flag="old",$start=0){
		$blacklist = $this->getblacklist($uid);
		$cidarr = $this->baseinfomodel->getusercrid($uid);
		if($flag=="old"){//查看以前的n条记录
			$cridin = empty($cidarr)?" r.fid < 0 ":" r.crid in ( ".implode(",", $cidarr)." ) ";
			$condition = ($start!=0)? " and r.fid< $start and r.status = 0 " : " and r.status = 0 ";
			$orderby = " order by r.fid DESC ";
			$limit = " limit $len ";
			if(!empty($blacklist)){
				$condition .= " and r.uid not in (".implode(',', $blacklist).")";
			}
			
			$sql = "select distinct r.fid,o.outid,o.fid,o.pfid,o.tfid,o.iszhuan,o.uid,o.upcount,o.cmcount,o.zhcount,o.dateline,f.fromuid,f.message,f.category,f.toid from ebh_sns_roomfeeds r
			left join ebh_sns_feeds f on r.fid = f.fid
			left join ebh_sns_outboxs o on o.fid = f.fid
			where {$cridin}{$condition}{$orderby}{$limit}
			";	
		}elseif($flag=="new"){//查看最新的n条记录
			$cridin = empty($cidarr)?" r.fid < 0 ":" r.crid in ( ".implode(",", $cidarr)." ) ";
			$condition = " and r.fid > $start and r.status = 0 " ;
			$orderby = " order by r.fid DESC ";
			$limit = " limit $len ";
			if(!empty($blacklist)){
				$condition .= " and r.uid not in (".implode(',', $blacklist).")";
			}	
			
			$sql = "select distinct r.fid,o.outid,o.fid,o.pfid,o.tfid,o.iszhuan,o.uid,o.upcount,o.cmcount,o.zhcount,o.dateline,f.fromuid,f.message,f.category,f.toid from ebh_sns_roomfeeds r
			left join ebh_sns_feeds f on r.fid = f.fid
			left join ebh_sns_outboxs o on o.fid = f.fid
			where {$cridin}{$condition}{$orderby}{$limit}
			";
		}
		//查询符合条件的feeslist
		//echo $sql;
		$feeds = $this->snsdb->query($sql)->list_array();
		//组装评论
		$feeds = $this->getfeedswithreplys($feeds);
		//feeds信息格式化
		$feeds = $this->feedsformat($feeds);
		return $feeds;
	}
	
	/**
	 * 获取我的动态
	 */
	public function getMyFeeds($uid,$len=10,$flag="old",$start=0){
		if($flag=="old"){//查看以前的n条记录
			$uidin = " o.uid  = $uid ";
			$condition = ($start!=0)? " and o.fid < $start and f.status = 0 " : " and f.status = 0 ";
			$orderby = " order by o.fid DESC ";
			$limit = " limit $len ";
	
			$sql = "select o.outid,o.fid,o.pfid,o.tfid,o.iszhuan,o.uid,o.upcount,o.cmcount,o.zhcount,o.dateline,f.fromuid,f.message,f.category,f.toid from  ebh_sns_outboxs o
			left join ebh_sns_feeds f on o.fid = f.fid
			where {$uidin}{$condition}{$orderby}{$limit}
			";
	
		}elseif($flag=="new"){//查看最新的n条记录
			$uidin = " o.uid  = $uid ";
			$condition = " and o.fid > $start and f.status = 0 " ;
			$orderby = " order by o.fid DESC ";
			$limit = " limit $len ";
	
				$sql = "select o.outid,o.fid,o.pfid,o.tfid,o.iszhuan,o.uid,o.upcount,o.cmcount,o.zhcount,o.dateline,f.fromuid,f.message,f.category,f.toid from  ebh_sns_outboxs o
				left join ebh_sns_feeds f on o.fid = f.fid
				where {$uidin}{$condition}{$orderby}{$limit}
				";
			}
			//echo $sql;
			//查询符合条件的feeslist
			$feeds = $this->snsdb->query($sql)->list_array();
			//组装评论
			$feeds = $this->getfeedswithreplys($feeds);
			//feeds信息格式化
			$feeds = $this->feedsformat($feeds);
			return $feeds;
	}
		
	/**
	 * 获取某一个人发表的动态
	 * @param unknown $uid
	 * @param unknown $myuid
	 * @param number $len
	 * @param number $start
	 * @return unknown
	 */
	public function getOnesFeeds($uid,$myuid,$len=10,$start=0){
		$blacklist = $this->getblacklist($uid);
		$condition = ($start!=0)? " and o.fid < $start and f.status = 0 " : " and f.status = 0 ";
		if(!empty($blacklist)){
			$condition .= " and o.uid not in (".implode(',', $blacklist).")";
		}
		$orderby = " order by o.fid DESC ";
		$limit = " limit $len ";
		
		$sql = "select o.outid,o.fid,o.pfid,o.tfid,o.iszhuan,o.uid,o.upcount,o.cmcount,o.zhcount,o.dateline,f.fromuid,f.message,f.category,f.toid from  ebh_sns_outboxs o
					left join ebh_sns_feeds f on o.fid = f.fid
					where o.uid  = $uid
					{$condition}{$orderby}{$limit}";
			
		//查询符合条件的feeslist
		$feeds = $this->snsdb->query($sql)->list_array();
		 
		//组装评论
		$feeds = $this->getfeedswithreplys($feeds);
		
		//feeds信息格式化
		$feeds = $this->feedsformat($feeds,$myuid);
		
		return $feeds;
	}
	
	/**
	 * 获取关注的好友uid
	 * @param unknown $uid
	 * @return unknown|multitype:
	 */
	public function  getfollowuidarr($uid,$hasmyself=false){
		//查看用户的关注好友
		$follows = $this->followmodel->getmyfollows($uid);
		$uidarr = array();
		if(!empty($follows)){
			$uidarr = array_map(function($arr){return $arr['uid']; }, $follows);
		}
		if($hasmyself){
			$uidarr[] = $uid;
		}
		return  $uidarr;
	}
	
	/**
	 * 组装评论,返回动态
	 * @param  $feeds 动态list
	 * @return feeds
	 */
	public function getfeedswithreplys($feeds){
		//合并feeds
		$fidarr = array_map(function($arr){return $arr['fid'];}, $feeds);
	
		//查询动态关联的评论
		$replylists = $this->commentmodel->getfeedscomments($fidarr);
	
		if(!empty($feeds)){
			foreach($feeds as &$feed ){
				//校验转发的父级是否被删除
				if($feed['iszhuan']==1){
					$checkdel = $this->delmodel->checkfeedsdelete($feed['tfid']);
					$feed['refer_top_delete'] = $checkdel;
				}else{
					$feed['refer_top_delete'] = false;
				}
				
				//组装comments
				foreach($replylists as $key=>$replys){
					if($feed['fid']  == $key){
						$replys['replys'] = array_map(
								function($arr){
									if(!empty($arr)){
										$arr['message'] = json_decode($arr['message'],true);
										return $arr;
									}
								}, $replys['replys']);
								$feed['replys'] = $replys['replys'];
								$feed['replycount'] = $replys['count'];
					}
				}
				$feed['message'] = json_decode($feed['message'],true);
			}
		}
		return $feeds;
	}
	
	/**
	 * feeds信息格式化
	 * 加上点赞、关注、还有图片等信息 
	 */
	private function feedsformat($feeds,$uid){
		//拼接符合调节的feeds的fid
		$fidarr = array_map(function($arr){return $arr['fid'];}, $feeds);
		//查询动态关联的点赞
		$uplists = $this->upclickmodel->getbatchups($fidarr);
		if(!empty($feeds)){
			foreach($feeds as &$feed ){
				//获取feed所有者与获取的uid的关注关系
				$result = $this->followmodel->checkfollowed(array(array('fuid'=>$feed['uid'])),$uid);
				$feed['followed'] = $result[0]['followed'];
				
				$message = $feed['message'];
				if(!empty($message['images'])){
					$fmtimg = explode(',', $message['images']);
					$result = $this->imgfmts($fmtimg);
					$message['images'] = $result;
				}
				if(!empty($message['refer']) && !empty($message['refer']['images'])){
					$rfmtimg = explode(',', $message['refer']['images']);
					$rresult = $this->imgfmts($rfmtimg);
					$message['refer']['images'] = $rresult;
				}
				$feed['message'] = $message;
					
				//组装upclicks
				foreach ($uplists as $uk=>$ups){
					if($feed['fid'] == $uk){
						$feed['upclicks'] = $ups['users'];
					}
				}
			}
		}
			
		//dump($feeds);exit;
		return $feeds;
	}
	
	
	/**
	 * 手机端图片格式化
	 */
	private function imgfmts($imgs){
		if(empty($imgs)){
			return false;	
		}
		$imgstr = '';
		$user = Ebh::app()->user->getloginuser();
		$imgmodel = $this->model('Image');
		$arr = $imgmodel->getimgs($imgs);
		
		$up = Ebh::app()->getConfig()->load('upconfig');
		$thumsize = $up['snspic']['wapthumsize'];
		if(!empty($arr)){
			foreach ($arr as $item){
				if(strpos($item['sizes'], $thumsize) === false){
					$imgdata[$item['gid']] = $up['snspic']['savepath'].$item['path'];
				}
			}
		}
		if(!empty($imgdata)){
			//调用crul处理图片裁剪
			$post_url = $up['snspic']['server'][0];
			$post_url = str_replace('uploadimage.html', '', $post_url);
			$post_url .= 'snsupload/cutimgsbysize.html';
			$param = $imgdata;
			$param['k'] = $user['k'];
			$res = do_post($post_url, $param);
			$result = json_decode($res,true);
			if(!empty($result)){
				$imgids = array_keys($result);
				//更新数据库
				$result = $imgmodel->addto(array('size'=>$thumsize),$imgids);
			}
		}
		foreach ($arr as $item){
			if($item['status'] == 1){
				$item['path'] = 'jin_210_110.png';
			}
			$imgstr .= $item['path'].',';
		}
		return substr($imgstr, 0,strlen($imgstr)-1);
	} 
}