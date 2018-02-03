<?php
/**
 *微信接口
 */
class OweixinController extends CControl{
	public function getCodeUrl(){
		$wxLibs = Ebh::app()->lib('WxPublicPay');
		$url = $this->input->post('url');
		
		$scrid = $this->input->post('scrid');
		if(empty($scrid)){
			$scrid = 0;
		}
		$attach = $this->input->post('attach');
		if(empty($attach)){
			$attach = '';
		}

		$tourl = $this->input->post('tourl');
		if(empty($tourl)) {
			$tourl = '';
		}
		$roominfo = $this->model('classroom')->getclassroomdetail($scrid);
		$crname = !empty($roominfo)?$roominfo['crname']:'';
		$res = $wxLibs->getWxCode($url);
		$package = array('crname'=>$crname,'scrid'=>$scrid,'attach'=>$attach,'tourl'=>$tourl);
		if(!empty($res)){
			$res = str_replace("STATE", base64_encode(json_encode($package)), $res);
		}
		echo json_encode(array('codeurl'=>$res,'status'=>0));
	}
}