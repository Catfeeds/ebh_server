<?php
/**
 * 评论相关控制器
 */
class ReviewController extends CControl{
	public function __construct() {
        parent::__construct();
        $user = Ebh::app()->user->getloginuser();
		if(empty($user)) {	//如果用户验证失败，则返回-1
			echo array('status'=>-1,'msg'=>'用户信息已过期');
			exit();
		}
    }
	/**
	*我的评论列表
	*/
    public function index() {
		$crid = $this->input->post('rid');
		$reviewlist = array();
		if(empty($crid) || !is_numeric($crid) || $crid <= 0) {
			echo json_encode($reviewlist);
			exit();
		}
        $user = Ebh::app()->user->getloginuser();
        $reviewmodel = $this->model('Review');
        $queryarr = array();
		$page = $this->input->post('page');
		if(empty($page) || !is_numeric($page)) {
			$page = 1;
		}
		$queryarr['page'] = $page;
        $queryarr['crid'] = $crid;
        $queryarr['uid'] = $user['uid'];
        $reviews = $reviewmodel->getreviewlistbycrid($queryarr);
		foreach($reviews as $review){
			$review['date'] = empty($review['date']) ? '' : date('Y-m-d H:i:s',$review['date']);
			$message = $review['message'];
			//$message = preg_replace("/\[emo(\S{1,2})\]/is","<img src=\"http://static.ebanhui.com/ebh/tpl/default/images/\\1.gif\" />",$message);	//替换表情符为img标签 此处直接app替换
			$review['faceurl'] = 'http://static.ebanhui.com/ebh/tpl/default/images/';
			$review['message'] = $message;
			$reviewlist[] = $review;
		}
		echo json_encode($reviewlist);
	}
	/**
     * 添加评论
     */
    public function add() {
    	$crid = $this->input->post('rid');
		$cwid = $this->input->post('cid');
		$msg = $this->input->post('message');
		$score = $this->input->post('score');
		$type = 'courseware';
		if(NULL !== $type && $type == 'courseware') {
			
			if(!is_numeric($cwid) || $cwid <= 0) {
				echo json_encode(array('status'=>1,'msg'=>'没有指定课件'));
				exit;
			}
			if(!isset($msg) || strlen($msg) == 0) {
				echo json_encode(array('status'=>1,'msg'=>'评论不能为空'));
				exit;
			}

			//检查是否有敏感词语
			EBH::app()->helper('simpledict');
			if (!checkSensitive($msg)){
				echo json_encode(array('status'=>2,'msg'=>'评论失败，您提交的内容含有敏感词语！'));
				exit;
			}

			$user = Ebh::app()->user->getloginuser();
			$fromip = $this->input->getip();
			$param = array('uid'=>$user['uid'],'toid'=>$cwid,'opid'=>8192,'type'=>$type,'subject'=>$msg,'score'=>intval($score),'credit'=>0,'upid'=>0,'value'=>0,'fromip'=>$fromip,'dateline'=>time());
			$reviewmodel = $this->model('Review');
			$result = $reviewmodel->insert($param);
			if($result > 0) {
				echo json_encode(array('status'=>0,'msg'=>'评论成功'));
				fastcgi_finish_request();
				$coursemodel = $this->model('Courseware');	//增加课件评论数
				$coursemodel->addreviewnum($cwid);
			} else {
				echo json_encode(array('status'=>1,'msg'=>'评论失败'));
			}
		}
    }
	/**
	*删除我的评论
	*/
	public function del() {
		$result = array();
		$crid = $this->input->post('rid');
		$logid = $this->input->post('id');
		if(empty($crid) || !is_numeric($crid) || $crid <= 0 || empty($logid) || !is_numeric($logid) || $logid <= 0) {
			$result['status'] = 1;
			echo json_encode($result);
			exit();
		}
		$user = Ebh::app()->user->getloginuser();
		$reviewmodel = $this->model('Review');
		$param = array('uid'=>$user['uid'],'logid'=>$logid);
		$affectrows = $reviewmodel->deletereview($param);
		if($affectrows > 0) {
			$result['status'] = 0;
			echo json_encode($result);
		} else {
			$result['status'] = 1;
			echo json_encode($result);
		}
	}

	/*
	*学生评论
	*/
	public function student(){
		$user = Ebh::app()->user->getloginuser();
		$crid = $this->input->post('rid');
		$reviewmodel = $this->model('review');
		$params = parsequery();
		$page = $this->input->post('page');
		if(empty($page) || !is_numeric($page)) {
			$page = 1;
		}
		$params['page'] = $page;
		$params['crid'] = $crid;
		$params['uid'] = $user['uid'];
		$params['displayorder'] = 'r.logid desc';
		$params['pagesize'] = 20;
		$params['status'] = 1;
		$params['rcrid'] = 1;
		$reviews = $reviewmodel->getReviewListForInterface($params);
		if(empty($reviews)){
			echo json_encode(array());
			exit;
		}
		$reviews = parseEmotion($reviews);
		$reviews = EBH::app()->lib('UserUtil')->setFaceSize('50_50')->init($reviews,array('uid','tid'),true);
		foreach ($reviews as &$review) {
			$scoreimg = str_repeat('<img src="http://static.ebanhui.com/ebh/tpl/default/images/icon_star_2.gif"/>', $review['score']);
			$scoreimg .= str_repeat('<img src="http://static.ebanhui.com/ebh/tpl/default/images/icon_star_1.gif"/>', 5 - intval($review['score']));
			$review['date'] = date('Y-m-d H:i',$review['dateline']);
			$review['scoreimg'] = $scoreimg;
		}
		echo json_encode($reviews);
	}

	/*
	*老师回复
	*/
	public function teacher(){
		$user = Ebh::app()->user->getloginuser();
		$crid = $this->input->post('rid');
		$reviewmodel = $this->model('review');
		$params = parsequery();
		$page = $this->input->post('page');
		if(empty($page) || !is_numeric($page)) {
			$page = 1;
		}
		$params['page'] = $page;
		$params['crid'] = $crid;
		$params['uid'] = $user['uid'];
		$params['displayorder'] = 'r.logid desc';
		$params['pagesize'] = 20;
		$params['rev'] = 1;
		$params['status'] = 1;
		$params['replysubject'] = 1;
		$reviews = $reviewmodel->getReviewListForInterface($params);
		if(empty($reviews)){
			echo json_encode(array());
			exit;
		}
		$reviews = parseEmotion($reviews);
		$reviews = EBH::app()->lib('UserUtil')->setFaceSize('50_50')->init($reviews,array('uid','tid','replyuid'),true);
		foreach ($reviews as &$review) {
			$scoreimg = str_repeat('<img src="http://static.ebanhui.com/ebh/tpl/default/images/icon_star_2.gif"/>', $review['score']);
			$scoreimg .= str_repeat('<img src="http://static.ebanhui.com/ebh/tpl/default/images/icon_star_1.gif"/>', 5 - intval($review['score']));
			$review['date'] = date('Y-m-d H:i',$review['dateline']);
			if(!empty($review['replydateline'])){
				$review['replaydate'] = date('Y-m-d H:i',$review['replydateline']);
			}else{
				$review['replaydate'] = "";
			}
			$review['scoreimg'] = $scoreimg;
		}
		echo json_encode($reviews);
	}	
}
