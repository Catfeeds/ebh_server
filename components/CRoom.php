<?php

/**
 * CRoom 用于教室平台组件类
 */
class CRoom extends CComponent {

    private $_roominfo = NULL;
	private $_checkstudent = NULL;
	private $_systemsetting = NULL;
    /**
     * 获取当前平台简要信息
     * @param int $crid 网校ID
     * @return array 平台信息
     */
    public function getcurroom($crid = 0) {
        if (isset($this->_roominfo))
            return $this->_roominfo;
        $crid = $crid > 0 ? $crid : Ebh::app()->getInput()->post('rid');
        if (is_numeric($crid) && $crid > 0) {
            $roommodel = $this->model('Classroom');
            $roominfo = $roommodel->getclassroomdetail($crid);
            $this->_roominfo = $roominfo;
            return $roominfo;
        }
        $this->_roominfo = FALSE;
        return FALSE;
    }
    /**
     * 验证当前用户是否对此平台有教师权限
     * @return boolean
     */
    public function checkteacher() {
        $user = Ebh::app()->user->getloginuser();
        if (empty($user) || $user['groupid'] != 5) {
            $url = geturl('login') . '?returnurl=' . geturl('troom');
            header("Location: $url");
            exit();
        }
        $room = $this->getcurroom();
        if (empty($room)) {
            $url = geturl('');
            header("Location: $url");
            exit();
        }
        $roommodel = $this->model('Classroom');
        $check = $roommodel->checkteacher($user['uid'], $room['crid']);
        if ($check != 1) {
            $url = geturl('teacher/choose');
            header("Location: $url");
            return true;
        }
        return true;
    }
    /**
    *判断用户是否有平台权限
    * @return int 返回验证结果，1表示有权限 2表示已过期 0表示用户已停用 -1表示无权限 -2参数非法
    */
    public function checkStudentPermission($uid,$param = array()) {
        if(empty($uid))
            return -2;
        $upmodel = $this->model('Userpermission');
        return $upmodel->checkUserPermision($uid,$param);
    }
    /**
    *根据功能点或者平台等信息获取支付服务项
    *@param array $param
    */
    public function getUserPayItem($param) {
        $upmodel = $this->model('Userpermission');
        return $upmodel->getUserPayItem($param);
    }

    /**
     * 验证当前用户是否有当前平台学生权限
     * @param $return boolean 是否直接返回值而不跳转
     * @return boolean
     */
    public function checkstudent($return = FALSE) {
        if(isset($this->_checkstudent))
            return $this->_checkstudent;
        $user = Ebh::app()->user->getloginuser();
        if (empty($user) || $user['groupid'] != 6) {
            $url = geturl('login') . '?returnurl=' . geturl('myroom');
            header("Location: $url");
            exit();
        }
        $room = $this->getcurroom();
        if (empty($room)) {
            $url = geturl('');
            header("Location: $url");
            exit();
        }
        if($room['ispublic'] == 2) {    //免费试听平台，则学生都能进去
            return true;
        }
        $roommodel = $this->model('Classroom');
        $charge = ($room['isschool'] == 6 || $room['isschool'] == 7) ? true : false;    //是否为收费平台
        $check = $roommodel->checkstudent($user['uid'], $room['crid'],$charge);
        $this->_checkstudent = $check == 1 ? true : $check;
        if ($check != 1 && !$return) {
            if ($check == 2) {
                $url = geturl('over');
            } else {
                $url = geturl('member');
            }
            header("Location: $url");
            exit();
        }
        if($return && $check != 1) {
            return $check;
        }
        return true;
    }

    public function checkstudent2($crid,$room = array()){
        $user = Ebh::app()->user->getloginuser();
        $ret = array();
        $ret['status'] = 1;
        if (empty($user) || $user['groupid'] != 6) {
           $ret['msg'] = '当前用户不是学生！';
           return $ret;
        }
        if(!empty($room)){
            $room = $this->model('classroom')->getclassroomdetail($crid);
        }
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
        $charge = ($room['isschool'] == 6) ? true : false;  //是否为收费平台
        $check = $roommodel->checkstudent($user['uid'], $room['crid'],$charge);
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

	/**
	 * 获取系统设置
	 */
	public function getSystemSetting() {
    	if (isset($this->_systemsetting))
        	return $this->_systemsetting;

		$room = $this->getcurroom();
		$redis = Ebh::app()->getCache('cache_redis');
		$redis_key = 'room_systemsetting_' . $room['crid'];

		$this->_systemsetting = $redis->hget($redis_key);

		if (empty($this->_systemsetting)){
			$this->_systemsetting = $this->model('systemsetting')->getSetting($room['crid']);
			$redis->hMset($redis_key, $this->_systemsetting);
		}

		return $this->_systemsetting;
	}
}
