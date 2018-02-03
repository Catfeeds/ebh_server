<?php
/**
 *用户签到控制器
 */
class SignController extends CControl{
	public function index(){
		$credit = $this->model('credit');
		$res = $credit->addCreditlog(22);
		$ret = array(
			'status'=>1,
			'msg'=>'您今天已经签到,不能重复签到'
		);
		if(!empty($res)){
			$ret['status'] = 0;
			$ret['msg'] = '签到成功';
		}
		echo json_encode($ret);
	}
}