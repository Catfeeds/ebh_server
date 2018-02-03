<?php

/**
 * 作业列表控制器
 */
class ExamController extends CControl {
	private $democrid = 0;
	private $democlassid = 0;
	public function __construct() {
        parent::__construct();
        $appsetting = Ebh::app()->getConfig()->load('appsetting');
		if(!empty($appsetting)) {
			if(!empty($appsetting['democrid']))
				$this->democrid = $appsetting['democrid'];
			if(!empty($appsetting['democlassid']))
				$this->democlassid = $appsetting['democlassid'];
		}
    }
    public function index() {
		$myexamlist = array();
		$user = Ebh::app()->user->getloginuser();
		$crid = $this->input->post('rid');
		$type = $this->input->post('type');
		if(!empty($user) && is_numeric($crid) && $crid > 0) {
			$examlist = array();
			if($type == 2) {	//已做作业
				$examlist = $this->getDoExam();
			} else if($type == 3) {	//草稿箱
				$examlist = $this->getBoxExam();
			} else if($type == 4) {	//错题集
				$examlist = $this->getErrorBook();
			} else {	//默认为未做作业
				$examlist = $this->getMyExam();
			}
			// formatDate($examlist,array('date','adate'),array('date','adate'),'Y-m-d');
			if(!empty($examlist)) {
				foreach($examlist as $myexam) {
					$name = empty($myexam['realname']) ? $myexam['username'] : $myexam['realname'];
					$myexam['tname'] = $name;
					unset($myexam['username']);
					unset($myexam['realname']);
					$myexam['sdate'] = date('Y-m-d',$myexam['date']);
                    if (isset($myexam['adate'])) {
                        $myexam['sadate'] = date('Y-m-d',$myexam['adate']);
                        $myexam['adate'] = date('Y-m-d',$myexam['adate']);
                    }

					$myexam['title'] = shortstr($myexam['title'],36); //作业标题默认截取18个汉字
					$myexam['date'] = date('Y-m-d',$myexam['date']);

					
					$sex = empty($myexam['sex']) ? 'man' : 'woman';
					$type = 't';
					$defaulturl = 'http://static.ebanhui.com/ebh/tpl/default/images/'.$type.'_'.$sex.'.jpg';
					$face = empty($myexam['face']) ? $defaulturl : $myexam['face'];
					$facethumb = getthumb($face,'120_120');
					$myexam['face'] = $facethumb;
					$myexam['score'] = empty($myexam['score'])?"0":$myexam['score'];
					if(!empty($myexam['usetime'])){
						$myexam['usetime'] = secondToStr($myexam['usetime']);
					}else{
						$myexam['usetime'] = "0秒";
					}
					if(empty($myexam['astatus'])){
						$myexam['astatus'] = 0;
					}else{
						$myexam['astatus'] = 1;
					}
					$myexam['cwtitle'] = !empty($myexam['cwtitle']) ? '('.$myexam['cwtitle'].')':'';
					$myexamlist[] = $myexam;
				}
			}
		}
		echo json_encode($myexamlist);
	}
	/**
	*获取已做作业
	*/
	private function getDoExam() {
        $roominfo = Ebh::app()->room->getcurroom();
        $domain = $roominfo['domain'];
		$exammodel = $this->model('Exam');
		$queryarr = array();
		$folderid = $this->input->post('folderid');
		$folderid = intval($folderid);
		$crid = $this->input->post('rid');
		$user = Ebh::app()->user->getloginuser();
		//获取班级信息
		$classesmodel = $this->model('Classes');
        if($domain == 'lcyhg') {
            //绿城育华学校获取所有班级
            $classids = $classesmodel->getClassidsByUid($roominfo['crid'],$user['uid']);
            $queryarr['classids'] = $classids;
        } else {
            $myclass = $classesmodel->getClassByUid($crid, $user['uid']);
            $queryarr['classid'] = $myclass['classid'];
            if (!empty($myclass['grade'])) {    //班级有年级信息，则显示此年级下的所有作业
                $queryarr['grade'] = $myclass['grade'];
                $queryarr['district'] = $myclass['district'];
            }
        }
		$begindate = $this->input->post('begindate');
		if(!empty($begindate)) {	//过滤答题时间
			$starttime = strtotime($begindate);
			if($starttime !== FALSE) {
				$queryarr['abegindate'] = $starttime;
			}
		}
		$enddate = $this->input->post('enddate');
		if(!empty($enddate)) {	//过滤答题时间
			$endtime = strtotime($enddate);
			if($endtime !== FALSE) {
				$queryarr['aenddate'] = $endtime + 86400;
			}
		}

		$page = $this->input->post('page');
		if(empty($page) || !is_numeric($page)) {
			$page = 1;
		}
		$queryarr['crid'] = $crid;
		$queryarr['uid'] = $user['uid'];
		
		$queryarr['hasanswer'] = 1;
		// $queryarr['astatus'] = 1;
		$queryarr['page'] = $page;
		$pagesize = 40;
		$queryarr['pagesize'] = $pagesize;
		$queryarr['folderid'] = $folderid;
		$examlist = $exammodel->getExamListByMemberid($queryarr);
		return $examlist;
	}
	/**
	*获取草稿箱作业
	*/
	private function getBoxExam() {
        $roominfo = Ebh::app()->room->getcurroom();
        $domain = $roominfo['domain'];
		$exammodel = $this->model('Exam');
		$queryarr = array();
		$folderid = $this->input->post('folderid');
		$folderid = intval($folderid);
		$crid = $this->input->post('rid');
		$user = Ebh::app()->user->getloginuser();
		$classesmodel = $this->model('Classes');
        if($domain == 'lcyhg') {
            //绿城育华学校获取所有班级
            $classids = $classesmodel->getClassidsByUid($roominfo['crid'],$user['uid']);
            $queryarr['classids'] = $classids;
        } else {
            $myclass = $classesmodel->getClassByUid($crid, $user['uid']);
            $queryarr['classid'] = $myclass['classid'];
            if (!empty($myclass['grade'])) {    //班级有年级信息，则显示此年级下的所有作业
                $queryarr['grade'] = $myclass['grade'];
                $queryarr['district'] = $myclass['district'];
            }
        }
		$page = $this->input->post('page');
		if(empty($page) || !is_numeric($page)) {
			$page = 1;
		}
		$queryarr['crid'] = $crid;
		$queryarr['uid'] = $user['uid'];
		$queryarr['hasanswer'] = 1;
		$queryarr['astatus'] = 0;
		$queryarr['page'] = $page;
		$pagesize = 40;
		$queryarr['pagesize'] = $pagesize;
		$queryarr['folderid'] = $folderid;
		$examlist = $exammodel->getExamListByMemberid($queryarr);
		return $examlist;
	}
	/**
	*获取未做作业
	*/
	private function getMyExam() {
		$roominfo = Ebh::app()->room->getcurroom();
		$domain = $roominfo['domain'];
		$exammodel = $this->model('Exam');
		$queryarr = array();
		$crid = $this->input->post('rid');
		$folderid = $this->input->post('folderid');
		$folderid = intval($folderid);
		$cwid = intval($this->input->post('cwid'));
		$user = Ebh::app()->user->getloginuser();
		$classesmodel = $this->model('Classes');
		if($domain == 'lcyhg'){//绿城育华学校获取所有班级
			$classids = $classesmodel->getClassidsByUid($roominfo['crid'],$user['uid']);
			$queryarr['classids'] = $classids;
		} else {
			$myclass = $classesmodel->getClassByUid($crid,$user['uid']);	
			if(empty($myclass)) {
				if($crid == $this->democrid) {	//如果为演示平台，则默认一个班级
					$myclass = array('classid'=>$this->democlassid);
				} else {
					return FALSE;
				}
			}
			$queryarr['classid'] = $myclass['classid'];
			if(!empty($myclass['grade'])) {	//班级有年级信息，则显示此年级下的所有作业
				$queryarr['grade'] = $myclass['grade'];
				$queryarr['district'] = $myclass['district'];
			}
		}
		$page = $this->input->post('page');
		if(empty($page) || !is_numeric($page)) {
			$page = 1;
		}
		$queryarr['crid'] = $crid;
		$queryarr['uid'] = $user['uid'];
		$queryarr['filteranswer'] = 1;
		$queryarr['page'] = $page;
		$pagesize = 40;
		$queryarr['pagesize'] = $pagesize;
		$queryarr['folderid'] = $folderid;
		$queryarr['cwid'] = $cwid;
		$examlist = $exammodel->getExamListByMemberid($queryarr);
		return $examlist;
	}

	public function getErrorBook(){
		$user = Ebh::app()->user->getloginuser();
		$queryarr = array();
		$crid = $this->input->post('rid');
		$page = $this->input->post('page');
		$folderid = $this->input->post('folderid');
		if(empty($page) || !is_numeric($page) || $page <=0) {
			$page = 1;
		}
		$pagesize = $this->input->post('pagesize');
		if(empty($pagesize) || !is_numeric($pagesize) || $pagesize <=0){
			$pagesize = 20;
		}
		$queryarr['page'] = $page;
		$queryarr['pagesize'] = $pagesize;
		$queryarr['crid'] = $crid;
		$queryarr['uid'] = $user['uid'];
		$queryarr['folderid'] = $folderid;
		$errors = $this->model('exam')->myscherrorbooklist($queryarr);
		
		foreach($errors as $k=>&$error) {
			$error['title'] = str_replace("<br>","",$error['etitle']);
			$error['erranswers'] = base64str(unserialize($error['erranswers']));
			if(!empty($error['ques'][0])){
				$error['ques'] = is_array($error['ques'][0])?$error['ques'][0]:$error['ques'];
			}
			if(stripos($error['ques']['subject'],"<object")!==false && stripos($error['ques']['subject'],"http://")===false) {
				$pattern = '/\/static\/flash\/dewplayer-bubble.swf/is';
				$error['ques']['subject'] = preg_replace($pattern, 'http://exam.ebanhui.com/static/flash/dewplayer-bubble.swf', $error['ques']['subject']);
			}
			if(stripos($error['ques']['subject'],"<img")!==false && stripos($error['ques']['subject'],"http://")===false) {
				$error['ques']['subject'] = preg_replace('/\/uploads\//', 'http://exam.ebanhui.com/uploads/', $error['ques']['subject']);
			}
			if(preg_match('/[\)\）]$/s',trim(strip_tags($error['ques']['subject'])))!==false) {
				$error['ques']['subject'] = preg_replace('/）/s', ')', $error['ques']['subject']);
				$error['ques']['subject'] = preg_replace('/（/s', '(', $error['ques']['subject']);
				$error['ques']['subject'] = preg_replace('/\([^\)]+\)$/', '', $error['ques']['subject']);
			}
			$error['ques']['subject'] = trim(str_replace("<br>","",$error['ques']['subject']));
			//去标签
			$error['ques']['subject'] = strip_tags($error['ques']['subject']);
			$error['subject'] = strip_tags($error['subject']);
			//过滤#input#
			$error['ques']['subject'] = str_replace("#input#", '______', $error['ques']['subject']);
			$error['subject'] = str_replace("#input#", '______', $error['subject']);
		}
		echo json_encode($errors);
		exit();
	}
	
	//删除错题集
	public function delerrorbook(){
		$eid = intval($this->input->post('eid'));
		$user = Ebh::app()->user->getloginuser();
		if($eid<=0){
			echo json_encode(array('code'=>false));
			exit;
		}
		$exammodel = $this->model('exam');
		$result = $exammodel->delerrorbook(array('uid'=>$user['uid'],'eid'=>$eid));
		echo json_encode(array('code'=>$result));
	} 	
}
