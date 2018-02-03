<?php

/**
 * 用户设备限制信息控制器
 */
class PermissionController extends CControl {
    public function index() {
		$method = $this->input->post('method');
		switch ($method) {
			case 'checkexam':
				$this->_checkExam();
				break;
			default:
				break;
		}
	}
	/**
	*检测学生作业权限
	*/
	private function _checkExam() {
		$uid = 0;
		$eid = 0;
		$crid = 0;
		$result = array('check'=>-2);
		$post = $this->input->post();
		if(NULL != $this->input->post('uid')) {
			$uid = intval($this->input->post('uid'));
		}
		if(NULL != $this->input->post('eid')) {
			$eid = intval($this->input->post('eid'));
		}
		if($uid <=0 || $eid <= 0) {
			echo json_encode($result);
			exit();
		}
		if(NULL != $this->input->post('crid')) {
			$crid = intval($this->input->post('crid'));
		}
		if(NULL != $this->input->post('version')){
			$version = $this->input->post('version');
		}
		
		if(empty($version) || $version != 2){
			$exammodel = $this->model('Exam');
			$exam = $exammodel->getExamInfo($eid);
		}else{//新版获取作业信息
			//数据服务器地址
			$dataserver = EBH::app()->getConfig('dataserver')->load('dataserver');
			$servers = $dataserver['servers'];
			//随机抽取一台服务器
			$target_server = $servers[array_rand($servers,1)];
			$url = 'http://'.$target_server.'/exam/simpleinfo/'.$eid;
			
			$ret = json_decode(do_post($url,array()));
			
			if(!empty($ret) && !empty($ret->datas->info)){
				
				$info = $ret->datas->info;
				
				$exam['crid'] = $info->crid;
				$exam['folderid'] = $info->tid;
				
			}
			
		}
		if(empty($exam) || empty($exam['folderid'])) {
			$result['check'] = -3;
			echo json_encode($result);
			exit();
		}
		if($crid <= 0)
			$crid = $exam['crid'];
		$roommodel = $this->model('Classroom');
		$roominfo = $roommodel->getSimpleRoom($crid);
		if($roominfo['isschool'] != 7) {	//非分成网校 直接返回权限
			$result['check'] = 1;
			echo json_encode($result);
			exit();
		}
		$foldermodel = $this->model('folder');
		$folder = $foldermodel->getfolderbyid($exam['folderid']);
		if($folder['fprice'] == 0){ //免费课程,直接返回权限
			$result['check'] = 1;
			echo json_encode($result);
			exit();
		}
		
		$permissionmodel = $this->model('Userpermission');
		
		$param = array('uid'=>$uid,'crid'=>$crid,'folderid'=>$exam['folderid']);
		$check = $permissionmodel->checkUserPermision($uid,$param);
		if($check != 1) {	//如果无权限则再判断是否有做作业权限
			$param['checkexam'] = 1;
			$freeitem = $permissionmodel->getUserFreePayItem($param);
			if(!empty($freeitem)) {
				$check = 1;
			}
		}
		$result['check'] = $check;
		if($check != 1) {
			$param['crid'] = $crid;
			$payitem = $permissionmodel->getUserPayItem($param);
			if(!empty($payitem)) {
				$result['itemid'] = $payitem['itemid'];
				$result['iname'] = $payitem['iname'];
			}
		}
		echo json_encode($result);
	}
}
