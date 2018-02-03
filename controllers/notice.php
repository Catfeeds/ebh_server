<?php
/**
 * 通知相关控制器
 */
class NoticeController extends CControl{
	public function __construct() {
        parent::__construct();
        $user = Ebh::app()->user->getloginuser();
		if(empty($user)) {	//如果用户验证失败，则返回-1
			echo array('status'=>-1,'msg'=>'用户信息已过期');
			exit();
		}
    }
	/**
	*通知列表
	*/
    public function index() {
		$crid = $this->input->post('rid');
		$noticelist = array();
		if(empty($crid) || !is_numeric($crid) || $crid <= 0) {
			echo json_encode($noticelist);
			exit();
		}
		$user = Ebh::app()->user->getloginuser();
		$classmodel = $this->model('Classes');
		$myclass = $classmodel->getClassByUid($crid,$user['uid']);
		if(!empty($myclass)) {
			$noticemodel = $this->model('Notice');
			$param = array();
			$param['crid'] = $crid;
			$param['ntype'] = '1,3,4';
			$param['classid'] = $myclass['classid'];
			$page = $this->input->post('page');
			$pagesize = $this->input->post('pagesize');
			if(empty($page) || !is_numeric($page)) {
				$page = 1;
			}
			if(empty($pagesize) || !is_numeric($pagesize)){
				$pagesize = 10;
			}
			$param['page'] = $page;
			$param['pagesize'] = $pagesize;
			$mylist = $noticemodel->getnoticelist($param);
			foreach($mylist as $mynotice) {
				if($mynotice['type'] != 1) {
					$mynotice['author'] = '学校';
				} else {
					$mynotice['author'] = empty($mynotice['realname']) ? $mynotice['username'] : $mynotice['realname'];
				}
				unset($mynotice['type']);
				unset($mynotice['realname']);
				unset($mynotice['username']);
				$mynotice['date'] = empty($mynotice['date']) ? '' : date('Y-m-d H:i',$mynotice['date']);
				$mynotice['message'] = empty($mynotice['message']) ? '' : shortstr(strip_tags($mynotice['message']),110);
				$noticelist[] = $mynotice;
			}
		}
		echo json_encode($noticelist);
	}
	/*
	*通知详情
	*/
	public function detail() {
		$notice = array();
		$crid = $this->input->post('rid');
		$noticeid = $this->input->post('id');
		if(empty($noticeid) || !is_numeric($noticeid) || $noticeid <= 0 || empty($crid) || !is_numeric($crid) || $crid <= 0) {
			echo json_encode($notice);
			exit();
		}
		$noticemodel = $this->model('Notice');
		$param = array('crid'=>$crid,'noticeid'=>$noticeid);
		$notice = $noticemodel->getNoticeDetail($param);
		if(!empty($notice)) {
			if($notice['type'] != 1) {
				$notice['author'] = '学校';
			} else {
				$notice['author'] = empty($notice['realname']) ? $notice['username'] : $notice['realname'];
			}
			unset($notice['type']);
			unset($notice['realname']);
			unset($notice['username']);
			$notice['message'] = $notice['message'];
			$data = '<!DOCTYPE HTML>';
			$data.='<html>';
			$data.='<head>'; 
			$data.='<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
			$data.='<meta content="width=device-width, initial-scale=1.0, user-scalable=yes" name="viewport" />';
			$data.='</head>';
			$data.='<body>';
			$data.='<div>'.htmlspecialchars_decode($notice['message']).'</div>';
			$data.='</body>';
			$data.='</html>';
			$notice['message'] = $data;
				//去掉通知详情的html标签，显示为纯文本。
			$notice['date'] = empty($notice['date']) ? '' : date('Y-m-d H:i:s',$notice['date']);

			$noticemodel->addviewnum($noticeid);	//添加查看数
		}
		echo json_encode($notice);
	}
}
