<?php
/**
 * 个人空间相关接口
 */
class SnsblogsController extends CControl{
	private $user;
	public function __construct() {
		parent::__construct();
		$user = Ebh::app()->user->getloginuser();
		if(empty($user)) {	//如果用户验证失败，则返回-1
			echo json_encode(array('status'=>-1,'msg'=>'用户信息已过期'));
			exit();
		}
		$this->user = $user;
    }
    
    /**
     * 获取日志详情
     */
    public function getBlog(){
    	$user = $this->user;
    	$param['bid'] = intval($this->input->post('bid'));
    	$param['uid'] = intval($this->input->post('uid'));
    	
    	if($param['bid']<=0 || $param['uid']<=0){
    		echo json_encode(array('code'=>-1));
    		exit;
    	}
    	$model = $this->model('blog');
    	$blog = $model->getlist($param);	
    	if(empty($blog) || $blog[0]['status'] > 0){
    		echo json_encode(array('code'=>-2));
    		exit;
    	}
    	
    	//过滤图片
    	if(!empty($blog[0]['images'])){
    		$imgmodel = $this->model('Image');
    		$gids = explode(',',$blog[0]['images']);
    		$images = $imgmodel->getimgs($gids);
    
    		$_UP = Ebh::app()->getConfig()->load('upconfig');
    		$rurl = 'http://static.ebanhui.com/sns/images/jin_650_350.png';
    		
    		foreach ($images as $item){
    			if($item['status'] == 1){
    				$furl = $_UP['pic']['showpath'].$item['path'];
    				$blog[0]['content'] = str_ireplace($furl, $rurl, $blog[0]['content']);
    			}
    		}
    	}
    	//获取评论列表
    	$param['toid'] = $param['bid'];
    	$param['category'] = $blog[0]['iszhuan'] == 1 ? 4 : 2;
    	$param['fid'] = 0;
    	$param['limit'] = '0,2';
    	$commentmodel = $this->model("Comment");
    	$comments = $commentmodel->getcommentlist($param);
    	$blog[0]['replys'] = $comments;
    	echo json_encode($blog[0]);
    }
    
    //获取个人日志分类
    public function getcate(){
    	$user = $this->user;
    	$model = $this->model('blog');
    	$cates = $model->getcate(array('uid'=>$user['uid']));
    	echo json_encode(array('cates'=>$cates));
    }
    
	/**
	 * 点赞
	 */
	public function upclick(){
		$bid = intval($this->input->post('bid'));
		$user = $this->user;
		if($bid<=0){
			echo json_encode(array('code'=>false,'msg'=>"param error..."));
			exit(0);
		}
		$Blogupclickmodel = $this->model('Blogupclick');
		//验证重复性
		$checked = $Blogupclickmodel->checkclicked($user['uid'],$bid);
		if($checked==true){
			echo json_encode(array('code'=>false,'msg'=>"您已经赞过了"));
			exit(0);
		}
		$data = array(
				'uid'=>$user['uid'],
				'bid'=>$bid,
				'dateline'=>time()
		);
		$upck = $Blogupclickmodel->addredislist($data);
		if($upck){
			//获取一条日志详情
			$blogModel = $this->model('Blog');
			$blog = $blogModel->getlist(array('bid'=>$bid));
			$info['touid'] = $blog[0]['uid'];
			$info['title'] = $blog[0]['title'];
			$info['toid'] = $blog[0]['bid'];
			$info['category'] = $blog[0]['iszhuan'] == 1 ? 4 :2;
			if($info['touid'] != $user['uid']){
				//发布一条通知
				$ntModel = $this->model('Notices');
				//发布一条通知
				$notice = array(
						'fromuid'=>	$user['uid'],
						'touid'=>$info['touid'],
						'message'=>json_encode(
								array(
										'fid'=>0
								)
						),
						'type'=>3,
						'category'=>$info['category'],
						'toid'=>$info['toid'],
						'dateline'=>time(),
				);
				$ntModel->add($notice);
				//更新通知数
				$baseModel = $this->model('Baseinfos');
				$baseModel->updateone(array(),$info['touid'],array('nzcount'=>'nzcount + 1'));
			}
		}
		echo json_encode(array('code'=>($upck>0)?true:false));
	}
	
	//转发
	public function transfer(){
		$user = $this->user;
		$zhuid = intval($this->input->post('zhuid'));
		$pbid = intval($this->input->post('pbid'));
		$cid = intval($this->input->post('cid'));
		$permission = intval($this->input->post('permission'));
		if($user['uid'] == $zhuid){
			$code = 2;
			echo json_encode(array('code'=>$code));
			exit;
		}
		$model = $this->model('blog');
		//是否已转载
		$zhwhere['uid'] = $user['uid'];
		$zhwhere['pbid'] = $pbid;
		$zhwhere['iszhuan'] = 1;
		$zhuan = $model->getlist($zhwhere);
		if(count($zhuan) > 0){
			$code = 3;
			echo json_encode(array('code'=>$code));
			exit;
		}
		//找到需转载的日志
		$blog = $model->getlist(array('bid'=>$pbid));
		if(empty($blog)){
			$code = -1;
			echo json_encode(array('code'=>$code));
			exit;
		}
		$setarr['uid'] = $user['uid'];
		$setarr['pbid'] = $pbid;
		$setarr['tbid'] = ($blog[0]['tbid'] > 0) ? $blog[0]['tbid'] : $pbid; 
		$setarr['iszhuan'] = 1;
		$setarr['title'] = $blog[0]['title'];
		$setarr['content'] = $blog[0]['content'];
		$setarr['tutor'] = $blog[0]['tutor'];
		$setarr['cid'] = $cid;
		$setarr['permission'] = $permission;
		$setarr['dateline'] = time();
		$setarr['images'] = $blog[0]['images'];
		$setarr['ip'] = getip();
		$result = $model->add($setarr);
		if($result){
			//更新上级转载的日志转载数
			$where['uid'] = $blog[0]['uid'];
			$where['bid'] = $pbid;
			$sparam['zhcount'] = 'zhcount + 1';
			$pupdate = $model->update(array(),$where,$sparam);
			//更新顶级转载的日志转载数
			if($blog[0]['iszhuan']){
				$wheres['bid'] = $blog[0]['tbid'];
				$tupdate = $model->update(array(),$wheres,$sparam);
			}
			//提取博主信息
			$model = $this->model("Baseinfos");
			$author = $model->getuserinfo(array(array('uid'=>$blog[0]['uid'])));
			$other['uid'] = $author[0]['uid'];
			$other['realname'] = !empty($author[0]['realname']) ? $author[0]['realname'] : $author[0]['username'];
			$other['bid'] = $setarr['pbid'];
			
			$newfeeds = array(
					'fromuid'=>$user['uid'],
					'message'=>json_encode(array(
							'title'=>$setarr['title'],
							'tutor'=>!empty($setarr['tutor']) ? $setarr['tutor'] : '',
							'images'=>!empty($setarr['images']) ? $setarr['images'] : '',
							'type'=>'blog',
							'referuser'=>$other,
					)),
					'category'=>4,
					'toid'=>$setarr['pbid'],
					'dateline'=>time()
			);
			$dynamicmodel = $this->model('Dynamic');
			$dynamicmodel->publish($newfeeds,$user['uid']);
			$code = $pupdate > 0 ? 1: -1;
			
			if($code){
				$info['touid'] = $blog[0]['uid'];
				$info['title'] = $blog[0]['title'];
				$info['toid'] = $blog[0]['bid'];
				$info['category'] = $blog[0]['iszhuan'] == 1 ? 4 :2;
				$ntModel = $this->model('Notices');
				//发布一条通知
				$notice = array(
						'fromuid'=>$user['uid'],
						'touid'=>$info['touid'],
						'message'=>json_encode(
								array(
										'fid'=>0,
										'content'=>''
								)
						),
						'type'=>4,
						'category'=>$info['category'],
						'toid'=>$info['toid'],
						'dateline'=>time(),
				);
				$ntModel->add($notice);
				//更新通知数
				$baseModel = $this->model('Baseinfos');
				$baseModel->updateone(array(),$info['touid'],array('nfcount'=>'nfcount + 1'));
			}
		}
		echo json_encode(array('code'=>$code));
	}
	
	//评论处理
	public function reply(){
		$user = $this->user;
		$uid = $user['uid']; 
		$bid = intval($this->input->post('bid'));
		$pcid = intval($this->input->post('pcid'));
		$touid = intval($this->input->post('touid'));
		$content = h(strip_tags($this->input->post('content')));
		if($bid<=0){
			echo json_encode(array('code'=>-1));
			exit(0);
		}
		$blogmodel = $this->model('Blog');
		$blog = $blogmodel->getlist(array('bid'=>$bid));
		$touid = $touid > 0 ? $touid : $blog[0]['uid'];
		$isreply = $pcid > 0 ? true : false;
		$commentmodel = $this->model("Comment");
		$baseinfomodel = $this->model("baseinfos");
		$touser = $baseinfomodel->getuserinfo(array(0=>array('uid'=>$touid)));
		$touser = $touser[0];
		
		$fnickname = !empty($user['realname'])?$user['realname']:$user['username'];
		$tnickname = !empty($touser['realname'])?$touser['realname']:$touser['username'];
		
		$ip = getip();
		$data = array(
				'pcid'=>$pcid,
				'fromuid'=>	$uid,
				'touid'=>$touid,
				'message'=>json_encode(array(
						'content'=>$content,
						'fromuser'=>array(
								'uid'=>$uid,
								'realname'=>$fnickname,
								'face'=>$user['face'],
								'sex'=>$user['sex'],
								'groupid'=>$user['groupid']
						),
						'touser'=>array(
								'uid'=>$touid,
								'realname'=>$tnickname,
								'face'=>$touser['face'],
								'sex'=>$touser['sex'],
								'groupid'=>$touser['groupid']
						),
				)),
				'type'=>2,
				'category'=>$blog[0]['iszhuan'] == 1 ? 4 :2,
				'toid'=>$bid,
				'dateline'=>time(),
				'ip'=>$ip
		);
		$cid = $commentmodel->add($data);
		//评论成功 评论数加1
		if($cid>0){
			//更新日志评论数
			$blogmodel = $this->model('Blog');
			$where['bid'] = $bid;
			$sparam['cmcount'] = 'cmcount + 1';
			$update = $blogmodel->update(array(),$where,$sparam);
			
			$comment  = $commentmodel->getcommentbycid($cid);
			$comment['message'] = json_decode($comment['message'],true);
			if($touid != $uid){
				$ntModel = $this->model('Notices');
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
										'fid'=>0,
										'isreply'=>$isreply,
										'content'=>$comment['message']['content'],
										'orimsg'=>$ocomment
								)
						),
						'type'=>2,
						'category'=>$blog[0]['iszhuan'] == 1 ? 4 :2,
						'toid'=>$bid,
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
	 * 评论删除
	 */
	public function delreply(){
		$fromuid = intval($this->input->post('fromuid'));
		$cid = intval($this->input->post('cid'));
		$bid = intval($this->input->post('bid'));
		$user = $this->user;
		
		if($cid==0||$fromuid==0||$fromuid!=$user['uid']||$bid==0){
			echo json_encode(array('code'=>-1));
			exit();
		}
		
		$commentmodel = $this->model("comment");
		$comment  = $commentmodel->getcommentbycid($cid);
		if($comment['fromuid']!=$fromuid){
			echo json_encode(array('code'=>-2));
			exit();
		}
		$param = array(
				'status'=>1,
		);
		$ck = $commentmodel->edit($param,$cid);
		if($ck>0){
			//更新日志评论数
			$blogmodel = $this->model('Blog');
			$where['bid'] = $bid;
			$sparam['cmcount'] = 'cmcount - 1';
			$update = $blogmodel->update(array(),$where,$sparam);
		}
		echo json_encode(array('code'=>($ck>0)?true:false));
	}
	
	/**
	 * 获取更多评论
	 */
	public function getreplyajax(){
		$bid = intval($this->input->post('bid'));
		$lastcid = intval($this->input->post('lastcid'));
		
		if($bid<=0||$lastcid<=0){
			echo json_encode(array('code'=>-1));
			exit(0);
		}
		$blogmodel = $this->model('Blog');
		$blog = $blogmodel->getlist(array('bid'=>$bid));
	
		$param = array(
				'toid'=>$bid,
				'category' => $blog[0]['iszhuan'] == 1 ? 4 : 2,
				'fid' => 0,
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
}