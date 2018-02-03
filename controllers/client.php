<?php

/**
 * 用户设备限制信息控制器
 */
class ClientController extends CControl {
    public function index() {
		$method = $this->input->post('method');
		switch ($method) {
			case 'limitnum':
				$this->_getLimitNum();
				break;
			case 'userclients':
				$this->_getClientList();
				break;
			case 'add':
				$this->_saveClient();
				break;
			case 'update':
				$this->_saveClient(TRUE);
				break;
			default:
				break;
		}
	}
	/**
	*获取网校的每用户限制绑定设备数
	*/
	private function _getLimitNum() {
		$systemsetting = Ebh::app()->room->getSystemSetting();
		$limitnum = $systemsetting['limitnum'];
		$arr = array('limitnum'=>$limitnum);
		echo json_encode($arr);
	}
	/**
	*获取用户的已绑定列表
	*/
	private function _getClientList() {
		$uid = 0;
		$crid = 0;
		$list = array();
		if(NULL != $this->input->post('uid')) {
			$uid = intval($this->input->post('uid'));
		}
		if(NULL != $this->input->post('crid')) {
			$crid = intval($this->input->post('crid'));
		}
		if($uid > 0 && $crid > 0) {
			$clientmodel = $this->model('Userclient');
			$list = $clientmodel->getClientsByUid($uid,$crid);
		}
		echo json_encode($list);
	}
	/**
    *保存用户设备登录信息
    */
    private function _saveClient($isupdate = FALSE) {
		$result = 0;
		if(NULL === $this->input->post()) {
			echo json_encode(array('result'=>$result));
			exit();
		}
		$client = $this->input->post();
		$ucmodel = Ebh::app()->model('Userclient');
        if($isupdate) {
            $addresult = $ucmodel->update($client);
        } else {
            $addresult = $ucmodel->add($client);
        }
		if($addresult)
			$result = 1;
		echo json_encode(array('result'=>$result));
    }
	
}
