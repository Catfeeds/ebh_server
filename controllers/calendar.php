<?php
/**
 * 学生学习课表相关控制器
 */
class CalendarController extends CControl{
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
		$result = array('listencount'=>array(),'examcount'=>array(),'askcount'=>array());
		if(empty($crid) || !is_numeric($crid) || $crid <= 0) {
			echo json_encode($result);
			exit();
		}
		$startDate = $this->input->post('begindate');
        $endDate = $this->input->post('enddate');
		$startDate = strtotime($startDate);
		$endDate = strtotime($endDate);
		if($startDate === FALSE || $endDate === FALSE) {
			echo json_encode($result);
            exit();
		}
		$user = Ebh::app()->user->getloginuser();
		$endDate = $endDate + 86400;
        $param = array('uid'=>$user['uid'],'crid'=>$crid,'startDate'=>$startDate,'endDate'=>$endDate);
		$exammodel = $this->model('Exam');
		$result['listencount'] = $exammodel->getStudyCount($param);//听课笔记
        $result['examcount'] = $exammodel->getExamCountByDate($param);	//做作业记录
        $result['askcount'] = $exammodel->getAskCount($param);//查看答疑答题
		echo json_encode($result);
	}
}
