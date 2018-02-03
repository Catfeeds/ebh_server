<?php
/**
 * 个人空间相关接口
 */
class SnsfeedsController extends CControl{
	private $typearr;
	private $user;
	public function __construct() {
		parent::__construct();
		$this->typearr = array('all','myfollow','myclass','myschool','myindex');
		$user = Ebh::app()->user->getloginuser();
		if(empty($user)) {	//如果用户验证失败，则返回-1
			echo json_encode(array('status'=>-1,'msg'=>'用户信息已过期'));
			exit();
		}
		$this->user = $user;
    }
    
	/**
	*获取新鲜事 param uid:用户号,lastfid:最后一条fid,type:all(全部),myfollow(我的关注),myclass(我的班级),myindex(我发布的)
	*/
    public function getFeeds(){
    	$uid = intval($this->input->post('uid'));
    	$self = intval($this->input->post('self'));
    	$lastfid = intval($this->input->post('lastfid'));
    	$type = $this->input->post('t');
    	//接口测试自己$uid = 10797;$self = 1;$lastfid=0别人$uid = 12442;$self = 0;$lastfid=0
    	$type = empty($type) ? 'all' : $type;
    	$user = $this->user;
    	
    	if($uid<=0 || in_array($type, $this->typearr)==false){
    		echo json_encode(array('code'=>-1));
    		exit;
    	}
    	$dynamicModel = $this->model('Dynamic');
    	$baseinfomodel = $this->model('Baseinfos');
    	if($self){
    		$feeds = $dynamicModel->getFeeds($uid,10,'old',$lastfid,$type);
    	}else{
    		$feeds = $dynamicModel->getOnesFeeds($uid,$user['uid'],10,$lastfid);
    	}
    	if(!empty($feeds)){
    		$feeds = $baseinfomodel->getuserinfo($feeds,"fromuid");
    	}  	 	
		echo json_encode($feeds);
	}
	
	/**
	 * 点赞
	 */
	public function upclick(){
		$user = $this->user;
		$uid = intval($this->input->post('uid'));
		$fid = intval($this->input->post('fid'));
		
		//测试$fid=238;$uid=10797;
		if($fid<=0 || $uid != $user['uid']){
			echo json_encode(array('code'=>false));
			exit(0);
		}
		$model = $this->model('Upclick');
		
		//验证重复性
		$checked = $model->checkclicked($uid,$fid);
		if($checked==true){
			echo json_encode(array('code'=>false));
			exit(0);
		}
		
		$data = array(
				'uid'=>$uid,
				'fid'=>$fid,
				'dateline'=>time()
		);
		$upck = $model->addredislist($data);
		
		if($upck){
			//获取一条动态详情
			$feedModel = $this->model('Feeds');
			$feed = $feedModel->getfeedsbyfid($fid);
			//自己点赞自己不通知
			if($feed['fromuid'] != $uid){
				$ntModel = $this->model('Notices');
				//发布一条通知
				$notice = array(
						'fromuid'=>	$uid,
						'touid'=>$feed['fromuid'],
						'message'=>json_encode(
								array(
										'fid'=>$fid
								)
						),
						'type'=>3,
						'category'=>1,
						'toid'=>$feed['toid'],
						'dateline'=>time(),
				);
				$ntModel->add($notice);
				//更新通知数
				$baseModel = $this->model('Baseinfos');
				$baseModel->updateone(array(),$feed['fromuid'],array('nzcount'=>'nzcount + 1'));
			}
		}
		echo json_encode(array('code'=>($upck>0)?true:false));
	}
	//发布
	public function publish(){
		$user = $this->user;
		$uid = intval($this->input->post('uid'));
		$content = $this->input->post('content');
		$imgs = $this->input->post('images');
		if($uid<=0 || $uid != $user['uid']){
			echo json_encode(array('code'=>false,'message'=>'参数错误'));
			exit(0);
		}
		$imgs = json_decode($imgs,true);
		$imgModel = $this->model('Image');
		$moodsModel = $this->model('Moods');
		$dynamicmodel = $this->model("Dynamic");
		$up = Ebh::app()->getConfig()->load('upconfig');
		//图片处理
		$ip = Ebh::app()->getInput()->getip();
		$gids = $cutimg = array();
		if(count($imgs) >0){
			foreach ($imgs as $item){
				$cutimg[] = $up['snspic']['savepath'].$item['path'];
			}
		}
		//裁剪图片，尺寸与电脑端兼容
		if(!empty($cutimg)){
			//调用crul处理图片裁剪
			$post_url = $up['snspic']['server'][0];
			$post_url = str_replace('uploadimage.html', '', $post_url);
			$post_url .= 'snsupload/cutimgs.html';
			$cparam['k'] = $user['k'];
			$cparam['datas'] = json_encode($cutimg);
			$res = do_post($post_url, $cparam);
			$rets = json_decode($res,true);
			if(count($rets)){
				foreach ($imgs as $key=>$item){
					$sizes = $item['sizes'].','.$rets[$key]['cutsize'];
					$setimg['uid'] = $uid;
					$setimg['path'] = $item['path'];
					$setimg['sizes'] = $sizes;
					$setimg['dateline'] = SYSTIME;
					$setimg['ip'] = $ip;
					$insert = $imgModel->add($setimg);
					$gids[] = $insert;
				}	
			}
		}
		//生成一条新鲜事
		$setmood['images'] = !empty($gids) ? implode(',', $gids) : ''; 
		$setmood['content'] = $content;
		$setmood['uid'] = $uid;
		$setmood['ip'] = $ip;
		$setmood['dateline'] = SYSTIME;
		$insertmood = $moodsModel->add($setmood);
		$success = $insertmood > 0 ? true : false;
		if($success){
			//发布一条消息
			$feeds = array(
					'touid'=>$uid,
					'fromuid'=>	$uid,
					'message'=>json_encode(array(
							'content'=>$setmood['content'],
							'images'=>$setmood['images'],
							'type'=>'mood',
					)),
					'category'=>1,
					'toid'=>$insertmood,
					'dateline'=>SYSTIME,
			);
			$fid = $dynamicmodel->publish($feeds,$uid);
		}
		$result = $success && $fid >0 ? true : false;
		echo json_encode(array('code'=>$result));
	}
	//转发
	public function transfer(){
		$user = $this->user;
		$uid = intval($this->input->post('uid'));
		$fid = intval($this->input->post('pfid'));
		$content = h(strip_tags($this->input->post('content')));
		
		//测试$uid=10797;$fid=241;$content='test test test---';
		if($uid<=0||$fid<=0||$uid != $user['uid']){
			echo json_encode(array('code'=>false,'message'=>'参数错误'));
			exit(0);
		}
		//检查字数
		if(strlen($content)>500*3){
			echo json_encode(array('code'=>false,'message'=>"已超过最大限制500字",'html'=>'','len'=>mb_strlen($content,'utf8')));
			exit(0);
		}
		$dynamicmodel = $this->model("Dynamic");
		$feedmodel = $this->model("Feeds");
		$baseinfomodel = $this->model("baseinfos");
		
		//先校验转载引用顶级是否被删除
		$delmodel = $this->model("Dels");
		$outboxmodel = $this->model("Outbox");
		$outbox = $outboxmodel->getoutboxbyfid($fid);
		
		$topisdel = false;
		$isdel = $delmodel->checkfeedsdelete($fid);
		
		if(!empty($outbox['tfid'])){
			$topisdel = $delmodel->checkfeedsdelete($outbox['tfid']);
		}
		
		if($isdel||$topisdel||empty($outbox)){
			echo json_encode(array('code'=>false,'message'=>'抱歉，此动态已经被删除或不存在，无法进行转发哦。'));
			exit(0);
		}
		$nfid = $dynamicmodel->transfer($fid,$uid,$content);
		
		echo json_encode(array('code'=>($nfid>0)?true:false));
	}
	
	//评论
	public function reply(){
		//参数校验
		$user = $this->user;
		$uid = intval($this->input->post('uid'));
		$fid = intval($this->input->post('fid'));
		$content = h(strip_tags($this->input->post('content')));
		$touid = intval($this->input->post('touid'));
		$pcid = intval($this->input->post('pcid'));
		$touid = $touid>0?$touid:$uid;
		$pcid = $pcid>0?$pcid:0;
		$isreply = $pcid > 0 ? true : false;
		//测试$content='test test test---';fid=249;$pcid=0;$touid=10797;
		if($uid<=0 || $fid<=0 || $uid != $user['uid']){
			echo json_encode(array('code'=>-1));
			exit(0);
		}
		//检查字数
		if(strlen($content)>500*3){
			echo json_encode(array('code'=>-2));
			exit(0);
		}
		$commentmodel = $this->model("Comment");
		$baseinfomodel = $this->model("baseinfos");
		$feedmodel = $this->model("Feeds");
		$dynamicmodel = $this->model("Dynamic");
		$outboxmodel = $this->model("Outbox");
		$feeds = $feedmodel->getfeedsbyfid($fid);
		
		if(empty($feeds)){
			echo json_encode(array('code'=>-3));
			exit(0);
		}
		$touser = $baseinfomodel->getuserinfo(array(0=>array('uid'=>$touid)));
		$touser = $touser[0];
		
		$fnickname = !empty($user['realname'])?$user['realname']:$user['username'];
		$tnickname = !empty($touser['realname'])?$touser['realname']:$touser['username'];
		
		$ip = $this->input->getip();
		$data = array(
				'pcid'=>$pcid,
				'fid'=>	$fid,
				'fromuid'=>	$uid,
				'touid'=>$touid,
				'message'=>json_encode(array(
						'content'=>$content,
						'fromuser'=>array(
								'uid'=>$uid,
								'realname'=>$fnickname,
								'face'=> $user['face'],
								'sex'=> $user['sex'],
								'groupid'=>$user['groupid']
						),
						'touser'=>array(
								'uid'=>$touid,
								'realname'=>$tnickname,
								'face'=> $touser['face'],
								'sex'=> $touser['sex'],
								'groupid'=>$touser['groupid']
						),
				)),
				'category'=>$feeds['category'],
				'toid'=>	$feeds['toid'],
				'dateline'=>time(),
				'ip'=>$ip
					
		);
		$cid = $commentmodel->add($data);
		
		//评论成功 评论数加1
		if($cid>0){
			$outboxmodel->update(array('cmcount'=>true),$fid);
			$comment  = $commentmodel->getcommentbycid($cid);
			$comment['message'] = json_decode($comment['message'],true);
			
			if($touid != $uid){
				$ntModel = $this->model('Notices');
				//获取原评论内容用于通知显示
				if($isreply){
					$oricomment = $commentmodel->getcommentbycid($pcid);
					$ocomment = json_decode($oricomment['message'],1);
				}else{
					$ocomment = array();
				}
				//发布一条通知
				$notice = array(
						'fromuid'=>	$uid,
						'touid'=>$touid,
						'message'=>json_encode(
								array(
										'fid'=>$fid,
										'isreply'=>$isreply,
										'content'=>$comment['message']['content'],
										'orimsg'=>$ocomment
								)
						),
						'type'=>2,
						'category'=>1,
						'toid'=>$feeds['toid'],
						'dateline'=>time(),
				);
				$ntModel->add($notice);
				//更新通知数
				$baseModel = $this->model('Baseinfos');
				$baseModel->updateone(array(),$touid,array('npcount'=>'npcount + 1'));
			}
		}
		
		echo json_encode(array('code'=>($cid>0)?true:false));
	}
	
	/**
	 * 获取关注列表 
	 */
	public function getfollows(){
		$user = $this->user;
		$uid = intval($this->input->post("uid"));
		$page = intval($this->input->post("page"));
		$q = trim($this->input->post("q"));
		
		$model = $this->model('Follow');
		$basemodel = $this->model("Baseinfos");
		if(empty($q)){
			$param['uid'] = $uid;
			$param['limit'] = (max(0,$page-1) * 10).",10";
			$follows = $model->getfollowlist($param);
			
			//组装个人信息
			if(!empty($follows)){
				$follows = $basemodel->getuserinfo($follows,"fuid");
				$follows = $model->checkfollow($follows,$user['uid'],'fuid');
			}
		}else{
			$params = array(
					'uid'=>$uid,
			);
			$follows = $model->getfollowlist($params);
			if(!empty($follows)){
				$follows = $basemodel->getuserinfo($follows,"fuid");
				$follows = $model->checkfollow($follows,$user['uid'],'fuid');
				//过滤
				foreach($follows as $key=>$follow){
					if(!(preg_match("/$q/", $follow['remark'])
							||preg_match("/$q/", $follow['username'])
							||preg_match("/$q/", $follow['realname'])
							||preg_match("/$q/", $follow['nickname'])
					)){
						unset($follows[$key]);
					}
				}			
			}
		}
		echo json_encode($follows);
	}
	/**
	 * 获取粉丝列表
	 */	
	public function getfans(){
		$user = $this->user;
		$page = intval($this->input->post('page'));
		$fuid = intval($this->input->post('fuid'));
		$model = $this->model("Follow");
		$basemodel = $this->model("Baseinfos");
		
		$pagesize = 10;
		$param = array(
			'fuid'=>$fuid,
			'limit'=>max(0,($page-1)*$pagesize)." , $pagesize"
		);
		$fans = $model->getfollowlist($param);
		
		//组装个人信息
		if(!empty($fans)){
			$basemodel = $this->model("Baseinfos");
			$fans = $basemodel->getuserinfo($fans,"uid");
			//检测互相关注
			$fans = $model->checkfollow($fans,$user['uid'],'uid');
			//dump($fans);
		}
		echo json_encode($fans);
	}
	/**
	 * 添加关注
	 */
	public function addfan(){
		$user = $this->user;
		$fuid = intval($this->input->post("fuid"));
		$uid = intval($this->input->post("uid"));
		//测试 $fuid = 12218
		if($fuid<=0 || $uid != $user['uid']){
			echo json_encode(array('code'=>false));
			exit;
		}
		$model = $this->model("Follow");
		$param = array(
				'uid'=>$user['uid'],
				'fuid'=>$fuid,
		);
		//获取我的关注
		$rows = $model->getfollowlist($param);
		if(!empty($rows)){
			echo json_encode(array('code'=>-2));
			exit;	
		}
		$ck = $model->addone($param);
		if($ck){
			$ntModel = $this->model('Notices');
			//发布一条通知
			$notice = array(
					'fromuid'=>	$user['uid'],
					'touid'=>$fuid,
					'message'=>json_encode(
							array(
									'topic'=>'',
									'comment'=>'关注了你'
							)
					),
					'type'=>5,
					'category'=>5,
					'toid'=>0,
					'dateline'=>time(),
			);
			$ntModel->add($notice);
			//更新通知数
			$baseModel = $this->model('Baseinfos');
			$baseModel->updateone(array(),$fuid,array('ngcount'=>'ngcount + 1'));
		}
		echo json_encode(array("code"=>($ck>0)?true:false));
		exit(0);
	}
	
	/**
	 * 取消关注 
	 */
	public function cancel(){
		$user = $this->user;
		$fuid = $this->input->post("fuid");
		$type = $this->input->post("type");
		if($fuid<=0){
			echo json_encode(array('code'=>false));
			exit;
		}
		$model = $this->model("Follow");
		if($type=="follow"){//取消关注
			$ck = $model->cancelone($user['uid'],$fuid);
		}elseif($type=="fans"){//取消粉丝
			$ck = $model->cancelone($fuid,$user['uid']);
		}
		if($ck){
			echo json_encode(array("code"=>true));
		}else{
			echo json_encode(array("code"=>false));
		}
	}
	
	/**
	 * 获取更多评论
	 */
	public function getreplyajax(){
		$fid = intval($this->input->post('fid'));
		$lastcid = intval($this->input->post('lastcid'));
		if($fid<=0||$lastcid<=0){
			echo 'param error...';
			exit(0);
		}
		$param = array(
				'fid'=>$fid,
				'condition'=>"cid > $lastcid",
				'limit'=>"5"
		);
		$commentmodel = $this->model("Comment");
		$replys = $commentmodel->getcommentlist($param);
		
		if(!empty($replys)){
			echo json_encode(array('code'=>true,'data'=>$replys));
		}else{
			echo json_encode(array('code'=>false));
		}
		exit(0);
	}
	
	/**
	 * 评论删除
	 */
	public function delreply(){
		$fromuid = intval($this->input->post('fromuid'));
		$cid = intval($this->input->post('cid'));
		$user = $this->user;
		//测试
		if($cid<=0||$fromuid<=0||$fromuid!=$user['uid']){
			echo json_encode(array('code'=>false));
			exit();
		}
		$commentmodel = $this->model("comment");
		$outboxmodel = $this->model("Outbox");
		$comment  = $commentmodel->getcommentbycid($cid);
		
		if($comment['fromuid']!=$fromuid){
			echo 'param error...';
			exit();
		}
		
		$param = array(
				'status'=>1,
		);
		$ck = $commentmodel->edit($param,$cid);
		if($ck>0){
			//删除成功 评论数减1
			$outboxmodel->update(array('cmcount'=>true),$comment['fid'],'reduce');
		}
		echo json_encode(array('code'=>($ck>0)?true:false));
	}
	
	/**
	 * 根据uid获取用户相关信息
	 */
	public function getuserinfo(){
		$uid = intval($this->input->post('uid'));
		$model = $this->model("Baseinfos");
		$curr = $model->getuserinfo(array(array('uid'=>$uid)));
		echo json_encode($curr);
	}
	
	/**
	 * 根据uid获取用户关注数、粉丝数、访客数
	 */
	public function getuserbasenum(){
		$uid = intval($this->input->post('uid'));
		$fmodel = $this->model("Follow");
		$bmodel = $this->model("Baseinfos");
		//关注数
		$follownum = $fmodel->getfollowcount(array('uid'=>$uid));
		//粉丝数
		$fansnum = $fmodel->getfollowcount(array('fuid'=>$uid));	
		//访客数(访客数 = 缓存数+数据库保存的值)
		$extinfo = $bmodel->getuserinfo(array(array('uid'=>$uid)));
		$thekey = 'count_'.$uid.'_'.md5($uid);
		$cache = Ebh::app()->getCache('cache_redis');
		$cachenum = intval($cache->get($thekey));
		$viewsnum = $extinfo[0]['viewsnum'] + $cachenum;
		
		$arr['followsnum'] = $follownum;
		$arr['fansnum'] = $fansnum;
		$arr['views'] = $viewsnum;
		echo json_encode($arr);
	}

	/**
	 * 设置个性签名
	 */
	public function setsign(){
		$user = $this->user;
		$uid = intval($this->input->post('uid'));
		$sign = $this->input->post('sign');
		if($uid == $user['uid']){
			$model = $this->model('User');
			$param['mysign'] = $sign;
			$result = $model->update($param,$uid);
			$code = $result > 0 ? true : false;
		}else{
			$code = false;
		}
		echo json_encode(array('code'=>$code));
	}
	
	/**
	 * 访客统计
	 */
	public function visit(){
		$user = $this->user;
		$vuid = intval($this->input->post('vuid'));
		if($vuid <= 0){
			echo json_encode(array('code'=>false));
			exit;
		}
		$visitorModel = $this->model('visitor');
		$visitorModel->visitor($user,array('uid'=>$vuid));
	} 
}