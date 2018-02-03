<?php
/**
 * 学生计数统计接口相关控制器
 */
class CountController extends CControl{
	public function __construct() {
        parent::__construct();
        $user = Ebh::app()->user->getloginuser();
		if(empty($user)) {	//如果用户验证失败，则返回-1
			echo array('status'=>-1,'msg'=>'用户信息已过期');
			exit();
		}
    }
	/**
	*计数处理
	*/
    public function index() {
		$crid = $this->input->post('rid');
		$result = array('paycount'=>0,'noticecount'=>0,'coursecount'=>0,'examcount'=>0,'askcount'=>0);
		if(empty($crid) || !is_numeric($crid) || $crid <= 0) {
			echo json_encode($result);
			exit();
		}
		$type = $this->input->post('type');
		if(empty($type)) {
			$typearr = array('1','2','3','4','5','6','7','9');
		} else {
			$typearr = explode(',',$type);
		}
		if(in_array('1',$typearr)) {	//交易数
			$result['paycount'] = $this->_gettypecount(6);
		}
		if(in_array('2',$typearr)) {	//最新通知数
			$result['noticecount'] = $this->_gettypecount(5);
		}
		if(in_array('3',$typearr)) {	//最新课件数
			$result['coursecount'] = $this->_gettypecount(2);
		}
		if(in_array('4',$typearr)) {	//最新作业数
			$result['examcount'] = $this->_gettypecount(1);
		}
		if(in_array('5',$typearr)) {	//最新答疑数
			$result['askcount'] = $this->_gettypecount(4);
		}
 		if(in_array('6',$typearr)) {	//网校数
			$result['roomcount'] = $this->_gettypecount(7);
		}
		if(in_array('7',$typearr)) {	//积分数
			$result['credit'] = $this->_gettypecount(8);
		}
		if(in_array('9',$typearr)) {
			$result['snscount'] = $this->_gettypecount(9);
		}
		echo json_encode($result);
	}
	/**
     * 根据分类获取该分类和用户状态时间下的记录数
     * @param type $type
     * @return int 记录数
     */
    private function _gettypecount($type) {
        $count = 0;
        $crid = $this->input->post('rid');
        $user = Ebh::app()->user->getloginuser();
        $statemodel = $this->model('Userstate');
        $subtime = $statemodel->getsubtime($crid,$user['uid'],$type);
        if(empty($subtime)){
            $subtime = strtotime('1970');
        }
		$schooltype = 3;
        if($type == 1) {    //新作业
            $exammodel = $this->model('Exam');
            if($schooltype == 3 || $schooltype == 6) {	//学校类型作业数
				$examparam = array('crid'=>$crid,'uid'=>$user['uid'],'subtime'=>$subtime);
                $count = $exammodel->getExamListCountByMemberid($examparam);
            } else if ($roominfo['isschool'] == 2) {
                $count = $exammodel->getnewexamcountbytime($crid,$user['uid'],$subtime);
            }
        } else if($type == 2) { //新课件
            $coursemodel = $this->model('Courseware');
            $count = $coursemodel->getnewcourselistcount(array('crid'=>$crid,'subtime'=>$subtime));
        } else if($type == 780) { //直播课
            $onlinemodel = $this->model('Onlinecourse');
            $count = $onlinemodel->getnewcourselistcount(array('crid'=>$crid,'subtime'=>$subtime));
        } else if($type == 4) { //最新答疑
            $askmodel = $this->model('Askquestion');
            $count = $askmodel->getnewaskcountbytime($crid,$subtime);
        } else if($type == 5) {	//最新通知数
			$noticemodel = $this->model('Notice');
			$noticeparam = array();
			$noticeparam['crid'] = $crid;
			$noticeparam['subtime'] = $subtime;
			$classmodel = $this->model('Classes');
			$myclass = $classmodel->getClassByUid($crid,$user['uid']);
			$noticeparam['ntype'] = '1,3,4';
			if(!empty($myclass)) {
				$noticeparam['classid'] = $myclass['classid'];
			}
			$count = $noticemodel->getnewnoticecountbytime($noticeparam);
			
		} else if($type == 6) {	//交易数
			$openmodel = $this->model('Opencount');
			$openparam = array('uid'=>$user['uid']);
			$count = $openmodel->getUserOpenCount($openparam);
		} else if($type == 7) {	//网校数
			$roomuser = Ebh::app()->model('roomuser');
			$count = $roomuser->getroomcount($user['uid']);
		} else if($type == 8) {	//积分数
			$count = $user['credit'];
		} else if($type == 9) { //校友圈动态
			$commentmodel = $this->model('comment');
			$count = $commentmodel->getcommentcount(array('touid'=>$user['uid'],'dateline'=>$subtime));
		}
		$statemodel->insert($crid,$user['uid'],$type,time());
        return $count;
    }
}
