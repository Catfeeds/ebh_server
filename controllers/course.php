<?php

/**
 * 课件详情控制器
 * 返回课件详情页url地址和课件播放url地址
 */
class CourseController extends CControl {
    public function index() {
		$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
		$isios = FALSE;
		if(strpos($useragent,'ios') !== FALSE) {
			$isios = TRUE;
		}
		if($isios && strpos($useragent,'iphone') !== FALSE && strpos($useragent,'1.0.3') !== FALSE ) {
			$isios = FALSE;
		}
		if($isios && strpos($useragent,'ipad') !== FALSE && strpos($useragent,'1.0.3') !== FALSE ) {
			$isios = FALSE;
		}
		$course = array();
		$user = Ebh::app()->user->getloginuser();
		$cwid = $this->input->post('id');
		$key = $this->input->post('k');
		if(!empty($user) && is_numeric($cwid) && $cwid > 0) {
			$key = urlencode($key);
			$course['curl'] = "http://www.ebh.net/icourse.html?cwid=$cwid&k=$key&type=view";
			$course['purl'] = "http://www.ebh.net/icourse.html?cwid=$cwid&k=$key";
			$course['thumb'] = "";
			//添加rtmp地址
			$course['rurl'] = "";
			$coursemodel = $this->model('Courseware');
			$mycourse = $coursemodel->getcoursedetail($cwid);
			
			if($mycourse['ism3u8'] == 1 && !$isios) {	//rtmp特殊处理 
				$serverutil = Ebh::app()->lib('ServerUtil');	//生成课件和附件所在服务器地址
				$m3u8source = $serverutil->getM3u8CourseSource();
				if(!empty($m3u8source)) {
					$key = $this->getKey($user);
					$key = urlencode($key);
					$m3u8url = "$m3u8source?k=$key&id=$cwid&.m3u8";
					$course['purl'] = $m3u8url;
					$course['thumb'] = $mycourse['thumb'];
				}
			}
		}
		echo json_encode($course);
	}
	/**
	*生成包含用户信息的key，目前主要
	*/
	private function getKey($user) {
		$uid = $user['uid'];
		$pwd = $user['password'];
		$ip = $this->input->getip();
		$time = SYSTIME;
		$skey = "$pwd\t$uid\t$ip\t$time";
		$auth = authcode($skey, 'ENCODE');
		return $auth;
	}

	/**
	*获取课件详情包含第一页评论
	*/
	public function info() {
		$ret = array();
		$cwid = $this->input->post('id');
		if(!is_numeric($cwid) || $cwid <= 0){
			$ret['status'] = 1;
			$ret['msg'] = "课件编号有误";
            echo json_encode($ret);
			exit();
		}
		$user = Ebh::app()->user->getloginuser();
		if(empty($user)){
			$ret['status'] = -1;
			$ret['msg'] = "用户账户不正确";
            echo json_encode($ret);
			exit();
		}
        $coursemodel = $this->model('Courseware');
        $course = $coursemodel->getcoursedetail($cwid);
        if(empty($course)){
			$ret['status'] = 2;
			$ret['msg'] = "课件不存在";
            echo json_encode($ret);
			exit();
		}
        $rid = intval($this->input->post('rid'));
        if ($rid == 0) {
            $rid = $course['crid'];
        }
		if (!empty($course['classids'])) {
            $classids = explode(',', $course['classids']);
            $classids = array_filter($classids, function($classid) {
               return !empty($classid);
            });
            if (!empty($classids)) {
                $userModel = $this->model('User');
                $classid = $userModel->getClassid($user['uid'], $rid);
                if (empty($classid) || !in_array($classid, $classids)) {
                    $ret['status'] = 3;
                    $ret['msg'] = "课件不存在";
                    echo json_encode($ret);
                    exit();
                }
            }
        }
		if (empty($course['username'])) {
            //课件添加老师被删除，将课件的所有者设置为课件所有网校的管理员
            $coursewareroom = $this->model('classroom')->getclassroomdetail($course['crid']);
            $course['username'] = $coursewareroom['username'];
            $course['realname'] = $coursewareroom['realname'];
            $course['uid'] = $coursewareroom['uid'];
            unset($coursewareroom);
        }
        $notice = $coursemodel->getNotice($cwid);
        $course['notice'] = $notice;
        $checkResult = $this->_checkCoursePower($user['uid'], $course, $rid);
        if($checkResult['check'] != 1){
        	$ret['status'] = 3;
        	$ret['msg'] = '没有权限';
        	$ret['itemid'] = $checkResult['itemid'];
        	if ($ret['itemid'] > 0) {
                $ret['price'] = $checkResult['price'];
                $ret['folderid'] = $checkResult['folderid'];
                $ret['crid'] = $checkResult['crid'];
        	}
        	echo json_encode($ret);
        	exit;
        }
        $course['dateline'] = date('Y-m-d',$course['dateline']);
        $course['purl'] = "";
        if($course['ism3u8'] == 1) {	//rtmp特殊处理 
			$serverutil = Ebh::app()->lib('ServerUtil');	//生成课件和附件所在服务器地址
			$m3u8source = $serverutil->getM3u8CourseSource();
			if(!empty($m3u8source)) {
				$key = $this->getKey($user);
				$key = urlencode($key);
				$m3u8url = "$m3u8source?k=$key&id=$cwid&.m3u8";
				$course['purl'] = $m3u8url;
			}
		}
        //获取资源
        $courseSource = $this->model('Source')->getFileBySid($course['sourceid']);
        $ret['source'] = $courseSource;
//		Ebh::app()->lib('Viewnum')->addViewnum('courseware',$cwid);
//		$viewnum = Ebh::app()->lib('Viewnum')->getViewnum('courseware',$cwid);
		$course['viewnum'] = empty($viewnum)?$course['viewnum']:$viewnum;
		$ret['status'] = 0;
		$ret['course'] = $course;
      	$ret['emotionarr'] = getEmotionarr();
		$ret['reviews'] = $this->review(true);
		echo json_encode($ret);
	}

	public function review($ifReturn = false){
	 	$reviewmodel = $this->model('Review');
	 	if(empty($ifReturn)){
	 		$page = intval($this->input->post('page'));
	 	}else{
	 		$page = 1;
	 	}
	 	$cwid = intval($this->input->post('id'));
	 	$param = array(
	 		'cwid'=>$cwid,
	 		'shield'=>1,
	 		'page'=>$page
		);
		$reviews = $reviewmodel->getReviewListByCwid($param);
		if(!empty($reviews)){
			$reviews = EBH::app()->lib('UserUtil')->setFaceSize('50_50')->init($reviews,array('uid'),true);
			$reviews = parseEmotion($reviews);
			$newreviews = array();
			foreach ($reviews as $review) {
				$scoreimg = str_repeat('<img src="http://static.ebanhui.com/ebh/tpl/default/images/icon_star_2.gif"/>', $review['score']);
				$scoreimg .= str_repeat('<img src="http://static.ebanhui.com/ebh/tpl/default/images/icon_star_1.gif"/>', 5 - intval($review['score']));
				$newreviews[] = array(
					'name'=>$review['uid_name'],
					'dateline'=>date('m-d',$review['dateline']),
					'face'=>$review['uid_face'],
					'subject'=>$review['subject'],
					'score'=>$review['score'],
					'scoreimg'=>$scoreimg,	
					'replysubject'=>$review['replysubject']
				);
			}
			$reviews = $newreviews;
		}else{
			$reviews = array();
		}
		if($ifReturn == false){
			echo json_encode($reviews);
		}else{
			return $reviews;
		}
	}

	//检测课件权限
	private function _checkCoursePower($uid = 0,$course = 0, $rid = 0){
		$ret = array('check'=>1,'itemid'=>0);
		//$crid = $course['crid'];
		//$roominfo = $this->model('classroom')->getclassroomdetail($crid);
        $roominfo = Ebh::app()->room->getcurroom($rid);
        if (empty($roominfo)) {
            $ret['ckeck'] = 0;
            return $ret;
        }
		$crid = $roominfo['crid'];

		$userInfo = $this->model('User')->getUserInfoByUid($uid);
        $userInfo = $userInfo[0];
        /**
         * 如果是老师 查看老师拥有的课程权限
         */
		if($userInfo['groupid'] == 5){
            /*$rs = $this->model('Folder')->checkTeacherPermission($course['folderid'],$uid,$roominfo['crid']);
            if($rs){
                $ret['check'] = 1;
            }else{
                $ret['check'] = 0;
            }*/
            $ret['check'] = 1;
            return $ret;
        }

		if($roominfo['isschool'] == 6 || $roominfo['isschool'] == 7) {
			$check = Ebh::app()->room->checkstudent2($crid,$roominfo);
			if ($check['status'] == 0) {
				$ret['check'] = 1;
			}
			else {
				$ret['check'] = 0;
			}
		}
		//针对isschool为7并且价格不为0的情况还要判断是否有课程权限
		if($roominfo['isschool'] == 7) {
			$perparam = array('crid'=>$roominfo['crid'],'folderid'=>$course['folderid'],'cwid'=>$course['cwid']);
			if($ret['check']){
				//$ret['check'] = Ebh::app()->room->checkStudentPermission($uid,$perparam);
                $userpermissionModel = $this->model('Userpermission');
                $power = $userpermissionModel->getFolderPower($uid, $course['folderid']);
                if (empty($power)) {
                    $ret['check'] = 0;
                } else {
                    $ret['crid'] = $power['crid'];
                    $ret['check'] = ($power['enddate'] > EBH_BEGIN_TIME - 86400) ? 1 : 0;
                }
			}
			if($ret['check'] != 1) {
				$payitem = Ebh::app()->room->getUserPayItem($perparam);
				if (!empty($payitem['itemid'])) {
                    $ret['itemid'] = $payitem['itemid'];
                    $ret['price'] = $payitem['iprice'];
                    $ret['folderid'] = $payitem['folderid'];
                    $ret['crid'] = $payitem['crid'];
                }
				if (empty($ret['itemid'])) {
				    //查找企业选课服务项
                    $schModel = $this->model('Schsource');
                    $item = $schModel->getPayItemByFolderid($course['folderid'], $roominfo['crid']);
                    if (!empty($item)) {
                        $ret['itemid'] = $item['itemid'];
                        $ret['price'] = $item['price'];
                        $ret['folderid'] = $item['folderid'];
                        $ret['crid'] = $item['sourcecrid'];
                    }
                }
			}
		}
		if($course['isfree'] == 1) {	//如果免费课程，则直接能播放
			$ret['check'] = 1;
		}
		return $ret;
	}

	public function getCourseList(){
		$folderid = $this->input->post('folderid');
		$user = Ebh::app()->user->getloginuser();
		$rid = intval($this->input->post('rid'));
        $userModel = $this->model('User');
        $classid = $userModel->getClassid($user['uid'], $rid);
		$param = array(
			'folderid'=>$folderid,
			//'uid'=>$user['uid'],
            'classids' => $classid,
			'pagesize'=>1000
		);
        $only_vedio = $this->input->post('only_vedio');
        if(!empty($only_vedio)) {
            $param['only_vedio'] = 1;
        }
		$res = $this->model('courseware')->getCourseList($param);
		echo json_encode($res);
	}
}
