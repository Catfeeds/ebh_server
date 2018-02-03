<?php

/**
 * 应用程序更新控制器
 */
class UpdateController extends CControl {
	private $life = 86400;	//cache time 
    public function index() {
		$result = array();
		$version = $this->input->post('version');
		$from = $this->input->post('from');
		$from = intval($from);
		$newurl = '';
		if($from != 2 && $from != 3 && $from != 4 && $from != 5 && $from !=6 && $from !=7 )		//2安卓 3 iphone 4 安卓高清 5 iPad
			return '';
		$update = Ebh::app()->getConfig()->load('update');
		if($from == 2) {
			$newversion = $update['android'];
			$newurl = $update['androidurl'];
		} else if($from == 4) {
			$newversion = $update['android-hd'];
			$newurl = $update['androidurl-hd'];
		} else if($from == 5) {
			$newversion = $update['ipad'];
			$newurl = $update['ipadurl'];
		} else if($from == 6) {
			$newversion = $update['android_xiaoxue'];
			$newurl = $update['androidurl_xiaoxue'];
		} else if($from == 7) {
			$newversion = $update['android_xiaoxue-hd'];
			$newurl = $update['androidurl_xiaoxue-hd'];
		} else {
			$newversion = $update['ios'];
			$newurl = $update['iosurl'];
		}
		if($version != $newversion) {
			$result['version'] = $newversion;
			$result['url'] = $newurl;
		} else {
			$result['version'] = $newversion;
			$result['url'] = '';
		}
		
		echo json_encode($result);
	}
}
