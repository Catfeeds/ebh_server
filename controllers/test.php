<?php

/**
 * 测试控制器
 */
class TestController extends CControl {
    public function index() {
    	$cmodel = $this->model('courseware');
    	$folderid = $this->input->post('folderid');
    	$pagesize = $this->input->post('pagesize');
    	$page = $this->input->post('page');
    	$param = array(
    		'folderid'=>$folderid,
    		'page'=>$page,
    		'pagesize'=>$pagesize
    	);
    	$res = $cmodel->getCourseList($param);
		echo json_encode($res);
	}

    public function folderinfo(){
        $folderModel = $this->model('folder');
        $folderid = $this->input->post('folderid');
        $uid = $this->input->post('uid');
        $res = $folderModel->getUserRelativeFolderInfo($folderid,$uid);
        echo json_encode($res);
    }
}