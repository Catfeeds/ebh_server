<?php
/**
 * 学习时间相关控制器
 */
class StudylogController extends CControl{
	public function __construct() {
        parent::__construct();
        $user = Ebh::app()->user->getloginuser();
		if(empty($user)) {	//如果用户验证失败，则返回-1
			echo array('status'=>-1,'msg'=>'用户信息已过期');
			exit();
		}
    }
	/**
	*提交我的学生时间处理
	*/
    public function index() {
		$cwid = $this->input->post('id');
		$result = array();
		if(empty($cwid) || !is_numeric($cwid) || $cwid <= 0) {
			echo 1;
			$result['status'] = 0;
			echo json_encode($result);
			exit();
		}
        $user = Ebh::app()->user->getloginuser();
        $logid = 0;
		if(NULL !== $this->input->post('lid')) {
			$logid = $this->input->post('lid');
		}
		if(!is_numeric($logid) || $logid <= 0) {
			$logid = 0;
		}
		$ctime = $this->input->post('ctime');
		$ltime = $this->input->post('ltime');
		$studymodel = $this->model('Studylog');

		//如果听课时长达到了视频总时长的0.9倍则听课时长标记为总时长，
		//如果听课时长超过了视频总时长的2倍，则听课时长标记为课件总时长的2倍
		if(is_numeric($ctime) && is_numeric($ltime) && ($ctime > 0) && ($ltime > 0) ){
			if( ( $ltime < $ctime ) && ( $ltime/$ctime>0.9 ) ){
				$ltime = $ctime;
			}
			if( $ltime > ($ctime * 2) ){
				$ltime = ( $ctime * 2 );
			}
		}else{
			exit;
		}
		
		$param = array('uid'=>$user['uid'],'cwid'=>$cwid,'ctime'=>$ctime,'ltime'=>$ltime,'logid'=>$logid);
		if($ctime<=$ltime){
			$param['finished'] = 1;
		}else{
			$param['finished'] = 0;
		}
		if( $ltime  > ($ctime * 3) ){
			$result['status'] = $logid;
			echo json_encode($result);
			exit();
		}
		$afrows = $studymodel->addlogs($param);
		$logid = $afrows;
		if($afrows > 0) {
			if($ltime/$ctime>0.9){
				$cwinfo = $this->model('courseware')->getSimplecourseByCwid($cwid);
				$credit = $this->model('credit');
				$credit->addCreditlog(array('ruleid'=>5,'detail'=>$cwinfo['title'],'cwid'=>$cwid));
			}
		} else {
			$logid = 0;
		}
		$result['status'] = $logid;
		echo json_encode($result);
	}

	/**
	 *处理学分(不需要验证用户是否登录)
	 *@param int cwid,crid (post,get)
	 *@return int -2,-1,0,1,2 (-2,表示用户没有权限[未登录],-1表示传入参数有误,0获取学分条件不足,1获取学分成功,2已经获取过学分)
	 */
	public function schcredit(){
		$rec = $this->input->post();
		if(empty($rec)){
			$rec = $this->input->get();
		}
		if(empty($rec)){
			echo -1;exit;
		}

		$user = Ebh::app()->user->getloginuser();
		if(empty($user)){
			echo -2;
			exit;
		}
		//处理传过来的post或者get参数
		$cwid = !empty($rec['id'])?intval($rec['id']):0;
		$crid = !empty($rec['rid'])?intval($rec['rid']):0;
		$uid = $user['uid'];
		//如果参数不合法则退出处理
		if(empty($cwid)||empty($crid)){
			echo -1;
			exit;
		}
		//设置从主数据库读取,防止主从服务器来不及同步的问题
		Ebh::app()->getDb()->set_con(0);

		//判断是否获取过学分
		$g_param = array(
			'uid'=>$uid,
			'cwid'=>$cwid
		);

		if($this->hasGetScore($g_param) == true){
			echo 2;exit;
		}

		//判断是否播放完毕课件
		$p_param = array();
		$p_param['cwid'] = $cwid;
		$p_param['uid'] = $uid;
		$p_param['finished'] = 1;
		$playlogModel = $this->model('playlog');
		$playInfoListA = $playlogModel->getStuLog($p_param);

		//判断课件播放时间是否大于或等于课件时间
		$p_param['finished'] = 0;
		$p_param['checkTime'] = true;
		$playInfoListB = $playlogModel->getStuLog($p_param);

		if(empty($playInfoListB)||empty($playInfoListA)){
			echo 0;exit;
		}
		//循环检测是否获视频播放完毕
		$hasFinished = true;

		//获取作业答题情况
		//1.获取改课件下面所有作业列表
		$e_param = array();
		$e_param['cwid'] = $cwid;
		$e_param['uid'] = $uid;
		$e_param['status'] = 1;
		$examModel = $this->model('exam');
		$answerInfoList = $examModel->getStuExamAnswerInfo($e_param);
		$hasDoExamRight = false;
		foreach ($answerInfoList as $answerInfo) {
			if(intval($answerInfo['totalscore']) == $answerInfo['score']){
				$hasDoExamRight = true;
				$folderid = $answerInfo['folderid'];
				$eid = $answerInfo['eid'];
				break;
			}
		}
		//如果看完视频并且全部答对题目就给学分
		if($hasDoExamRight&&$hasFinished){
			if(empty($folderid)){
				$folderInfo = $this->model('roomcourse')->getFolderByCwid($cwid,$crid);
				if(empty($folderid)){
					echo -5;exit;//没有找到与课件管理的课程
				}
				$folderid = $folderInfo['folderid'];
			}
			$up_param = array(
				'uid'=>$uid,
				'cwid'=>$cwid,
				'folderid'=>$folderid,
				'eid'=>$eid,
				'crid'=>$crid,
				'score'=>1,
				'dateline'=>time(),
				'fromip'=>$this->input->getip()
			);
			$schcreditlogModel = $this->model('schcreditlog');
			echo $schcreditlogModel->_insert($up_param);
		}else{
			echo 0;
		}
	}

	//判断是否获取过学分
	public function hasGetScore($param = array()){
		$schcreditlogModel = $this->model('schcreditlog');
		$scoreGetList = $schcreditlogModel->getSimpleList($param);
		$hasGetScore = false;
		foreach ($scoreGetList as $scoreGet) {
			if($scoreGet['score']>0){
				$hasGetScore = true;
				break;
			}
		}
		return $hasGetScore;
	}
}
