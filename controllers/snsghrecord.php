<?php
/**
 * 个人空间相关接口
 */
class SnsghrecordController extends CControl{
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
     * 获取成长记录
     */
    public function getGhrecord(){
    	$user = $this->user;
    	//个人积分明细
    	$credit = $this->model('credit');
    	$param['pagesize'] = 5;
    	$param['toid'] = $user['uid'];
    	$mycreditlist = $credit->getcreditlist($param);
    	echo json_encode($mycreditlist);
    }
}