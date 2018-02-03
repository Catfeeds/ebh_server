<?php

/**
 * 控制器
 */
class RoomController extends CControl {
    public function index() {
		$roomlist = array();
		$user = Ebh::app()->user->getloginuser();
		$roommodel = $this->model('Classroom');
		if(!empty($user)) {
			if($user['groupid'] == 5){
				$roomlist = $roommodel->getTeacherRooms($user['uid']);
			}else{
				$roomlist = $roommodel->getroomlistbyuid($user['uid']);
			}
		}
		//如果用户没有任何平台，那么自动注册一个
		if(empty($roomlist)) {
			$appsetting = Ebh::app()->getConfig()->load('appsetting');
			if(!empty($appsetting) && !empty($appsetting['democrid'])) {
				$demoroom = $roommodel->getDemoRoomByRid($appsetting['democrid']);
				if(!empty($demoroom))
					$roomlist[] = $demoroom;
			}
		}
		echo json_encode($roomlist);
	}

	public function getroom(){
		$crid = $this->input->post('rid');
		$domain = trim($this->input->post('domain'));
		if((empty($crid) || !is_numeric($crid)) && empty($domain)){
			echo json_encode(array());
		}
		$roominfo = $this->model('classroom')->getclassroomdetail(intval($crid), $domain);
		echo json_encode($roominfo);
	}
}
