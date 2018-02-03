<?php
/**
 *权限检测控制器
 *
 */
class CheckController extends CControl{
	public function __construct(){
		parent::__construct();
		$this->user = Ebh::app()->user->getloginuser();
		if(empty($this->user)){
			echo json_encode(array('status'=>1,'msg'=>'用户没有登录！'));
			exit;
		}
	}
	public function index(){
		$crid = $this->input->post('rid');
		if(empty($crid) || !is_numeric($crid)){
			echo json_encode(array('status'=>1,'msg'=>'教室信息不正确！'));
			exit;
		}
		$type = $this->input->post('type');
		if($type == 's'){
			echo json_encode($this->_checkstudent($crid));
			exit;
		}else if($type == 't'){
			echo json_encode($this->_checkteacher($crid));
			exit;
		}else{

		}
	}

	private function _checkstudent($crid){
		$ret = array();
		$ret['status'] = 1;
        if (empty($this->user) || $this->user['groupid'] != 6) {
           $ret['msg'] = '当前用户不是学生！';
           return $ret;
        }
        $room = $this->model('classroom')->getclassroomdetail($crid);
        if (empty($room)) {
            $ret['msg'] = '教室不存在！';
            return $ret;
        }
        $demoroomConfig = Ebh::app()->getConfig()->load('appsetting');
        if($demoroomConfig['democrid'] == $crid){
        	$ret['status'] = 0;
        	$ret['msg'] = "演示学校";
        	return $ret;
        }
        $roommodel = $this->model('Classroom');
        $charge = ($room['isschool'] == 6) ? true : false;	//是否为收费平台
        $check = $roommodel->checkstudent($this->user['uid'], $room['crid'],$charge);
        if ($check != 1) {
            if ($check == 2) {
	            $ret['msg'] = '已过期！';
	            return $ret;
            } else {
                $ret['msg'] = '无权限！';
	            return $ret;
            }
        }
        $ret['status'] = 0;
        $ret['msg'] = '验证成功!';
        return $ret;
	}

	private function _checkteacher($crid = 0){
		$ret = array();
		$ret['status'] = 1;
		$roommodel = $this->model('Classroom');
        $check = $roommodel->checkteacher($this->user['uid'], $crid);
        if($check == 1){//有权限
        	$ret['status'] = 0;
        	$ret['msg'] = '验证成功';
        }else{
        	$ret['msg'] = '验证失败';
        }
        return $ret;
	}
}