<?php
/**
 * 锁屏接口
 */
class SlockController extends CControl {
    public function index() {
		$uid = $this->input->post('uid');
		// 获取学生所在的学校
		$classes = $this->model('classes')->getStudentClasses($uid);
		$skey_in = array();
		foreach ($classes as $class) {
			$skey_in[] = md5($class['classid'].'_0_0_'.$class['crid']);
			$skey_in[] = md5('0_'.$class['grade'].'_'.$class['district'].'_'.$class['crid']);
		}
		$param = array(
			'skey_in'=>array_unique($skey_in),
			'date_between'=>time()
		);
		$getIListCount = $this->model('slock')->getIListCount($param);
		if(!empty($getIListCount)){
			echo 1;
		}else{
			echo 0;
		}
	}
}
