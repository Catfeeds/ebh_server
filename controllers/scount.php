<?php
/**
 *获取一些数据数量控制器
 */
class ScountController extends CControl{
	public function __construct() {

        parent::__construct();
        $user = Ebh::app()->user->getloginuser();
		if(empty($user)) {	//如果用户验证失败，则返回-1
			echo array('status'=>-1,'msg'=>'用户信息已过期');
			exit();
		}

    }
    public function index(){
    	$res = array(
    		'status'=>0,
    		'msg'=>'获取成功',
    		'data'=>array(
    			'review'=>0,
    			'favourit'=>0,
    			'fs'=>0
    		)
    	);

    	$review = $this->review();

    	$favourit = $this->favourit();

    	$fs = $this->getfs();

    	$res['data']['review'] = intval($review);
    	$res['data']['favourit'] = intval($favourit);
    	$res['data']['fs'] = intval($fs);
    	echo json_encode($res);
    }
	/*
	 *获取回复数
	*/
	public function review(){
		$user = Ebh::app()->user->getloginuser();
		$crid = $this->input->post('rid');
		if(empty($crid)){
			return 0;
		}
		$reviewmodel = $this->model('review');
		$param['crid'] = $crid;
		$param['uid'] = $user['uid'];
		$param['status'] = 1;
		if($user['groupid'] ==  6){
			$param['rcrid'] = 1;
		}else{
			$param['rev'] = 1;
			$param['replysubject'] = 1;
		}
		return $reviewmodel->getreviewcount($param);
	}

	/**
	 *获取关注数
	 */
	public function favourit(){
		$user = Ebh::app()->user->getloginuser();
		$model = $this->model('Follow');
		$result = $model->getfollowcount(array('uid'=>$user['uid']));
		return $result;
	}
	
	/**
	 *获取粉丝数
	 */
	public function getfs(){
		$user = Ebh::app()->user->getloginuser();
		$model = $this->model('Follow');
		$result = $model->getfollowcount(array('fuid'=>$user['uid']));
		return $result;
	}
}