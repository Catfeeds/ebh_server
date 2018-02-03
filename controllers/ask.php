<?php
/**
 * 答疑相关控制器
 */
class AskController extends CControl{
	public function __construct() {
        parent::__construct();
        $this->user = $user = Ebh::app()->user->getloginuser();
		if(empty($user)) {	//如果用户验证失败，则返回-1
			echo json_encode(array('status'=>-1,'msg'=>'用户信息已过期'));
			exit();
		}
    }
    public function index(){
		$type = $this->input->post('type');
		if($this->user['groupid'] == 6 || empty($type)){//学生
			$this->getStudentAsk();
			exit;
		}else{//老师
			$this->getTeacherAsk();
			exit;
		}
    }
	/**
	*答疑列表
	*/
    public function getStudentAsk() {
		$user = Ebh::app()->user->getloginuser();
		$type = $this->input->post('type');
		$asklist = array();
		if($type == 1) {	//我的问题
			$asklist = $this->my();
		} else if($type == 2) {	//我的回答
			$asklist = $this->myanswer();
		} else if($type == 3) {	//我的关注
			$asklist = $this->myfavorit();
		} else {	//所有问题
			$asklist = $this->all();
		}
		formatDate($asklist,array('date'),array('date'),'Y-m-d');
		$mylist = array();
		//取得最后一个回答该问题的用户信息及时间格式化
		$lastuid = array();
		foreach($asklist as $key=> $myask) {
			if(!empty($myask['answerdate'])) {
				$myask['answerdate'] = date('Y-m-d H:i:s',$myask['answerdate']);
			} else {
				$myask['answerdate'] = '';
			}
			if($myask['lastansweruid'] >0){
				$lastuid[$key] = $myask['lastansweruid'];
			}
			$name = empty($myask['realname']) ? $myask['username'] : $myask['realname'];
			$myask['author'] = $name;

			$sex = empty($myask['sex']) ? 'man' : 'woman';
			$type = $myask['groupid'] == 6 ?'m':'t';
			$defaulturl = 'http://static.ebanhui.com/ebh/tpl/default/images/'.$type.'_'.$sex.'.jpg';
			$face = empty($myask['face']) ? $defaulturl : $myask['face'];
			$facethumb = getthumb($face,'120_120');
			$myask['face'] = $facethumb;
			$myask['answered'] = 0;
			unset($myask['realname']);
			unset($myask['username']);
			$mylist[] = $myask;
		}
		//获取所有最后一个回答问题的用户详情
		if(!empty($lastuid)){
			$userModel = $this->model('User');
			$userinfos = $userModel->getUserInfoByUid($lastuid);
			foreach ($userinfos as $user){
				$fuserinfos[$user['uid']] = $user;
			}
			//提取最后一个回答问题的用户姓名与头像
			foreach ($lastuid as $k=>$v){
				if(isset($fuserinfos[$v])){
					$tmp = array();
					$tmp['username'] = !empty($fuserinfos[$v]['realname']) ? $fuserinfos[$v]['realname'] : $fuserinfos[$v]['username'];
					$tmp['face'] = getavater($fuserinfos[$v]);
					$mylist[$k]['lastansuser'] = $tmp;
				}
			}
		}
        
		echo json_encode($mylist);
	}

	public function getTeacherAsk(){
		$user = Ebh::app()->user->getloginuser();
		$type = $this->input->post('type');
		$asklist = array();
		if($type == 1) {	//我的问题
			$asklist = $this->my_t();
		} else if($type == 2) {	//课程问题
			$asklist = $this->folderask();
		}
		if(!empty($asklist)){
				$asklist = EBH::app()->lib('UserUtil')->setFaceSize('120_120')->init($asklist,array('uid'),true);
		}
		formatDate($asklist,array('dateline'),array('date'),'Y-m-d');
		$mylist = array();
		foreach($asklist as $myask) {
			if(!empty($myask['answerdate'])) {
				$myask['answerdate'] = date('Y-m-d H:i:s',$myask['answerdate']);
			} else {
				$myask['answerdate'] = '';
			}
			
			if(!empty($myask['answered'])) {
				$myask['answered'] = 1;
			} else {
				$myask['answered'] = 0;
			}
			$mylist[] = array(
				'qid'=>$myask['qid'],
				'title'=>$myask['title'],
				'author'=>$myask['uid_name'],
				'catname'=>$myask['foldername'],
				'answerdate'=>$myask['answerdate'],
				'answercount'=>$myask['answercount'],
				'hasbest'=>$myask['hasbest'],
				'face'=>$myask['uid_face'],
				'date'=>$myask['date'],
				'answered'=>$myask['answered'],
				'imagesrc'=>$myask['imagesrc'],
				'message'=>$myask['message']
			);
		}
		echo json_encode($mylist);
	}
	/**
	*学校所有问题
	*/
	private function all(){
		$crid = $this->input->post('rid');
		if(empty($crid) || !is_numeric($crid)) {
			$crid = 0;
		}
		$folderid = $this->input->post('folderid');
		if(empty($folderid) || !is_numeric($folderid)) {
			$folderid = 0;
		}
		$cwid = $this->input->post('cwid');
		if(empty($cwid) || !is_numeric($cwid)){
			$cwid = 0;
		}
        $user = Ebh::app()->user->getloginuser();
        $q = $this->input->get('q');
        $queryarr = array();
        $queryarr['crid'] = $crid;
		$queryarr['q'] = $q;
		$queryarr['shield'] = 0;
		$queryarr['folderid'] = $folderid;
		$queryarr['cwid'] = $cwid;
		$page = $this->input->post('page');
		if(empty($page) || !is_numeric($page)) {
			$page = 1;
		}
		$queryarr['page'] = $page;
        $askmodel = $this->model('Askquestion');
        $asklist = $askmodel->getallasklist($queryarr);
        //更新评论用户状态时间
        $statemodel = $this->model('Userstate');
        $typeid = 2;
        $statemodel->insert($crid,$user['uid'],$typeid,SYSTIME);
        return $asklist;
	}
	/**
	*学校我的问题
	*/
	private function my() {
        $crid = $this->input->post('rid');
		if(empty($crid) || !is_numeric($crid)) {
			$crid = 0;
		}
		$folderid = $this->input->post('folderid');
		if(empty($folderid) || !is_numeric($folderid)) {
			$folderid = 0;
		}
		$cwid = $this->input->post('cwid');
		if(empty($cwid) || !is_numeric($cwid)){
			$cwid = 0;
		}
        $user = Ebh::app()->user->getloginuser();
        $q = $this->input->post('q');
        $queryarr = array();
		$queryarr['crid'] = $crid;
		//$queryarr['q'] = $q;
		$queryarr['shield'] = 0;
		$queryarr['folderid'] = $folderid;
		$queryarr['cwid'] = $cwid;
		$page = $this->input->post('page');
		if(empty($page) || !is_numeric($page)) {
			$page = 1;
		}
		$queryarr['page'] = $page;
        $queryarr['uid'] = $user['uid'];
        
        $askmodel = $this->model('Askquestion');
        $asklist = $askmodel->getallasklist($queryarr);
		return $asklist;
    }
	/**
     * 我的回答
     */
    private function myanswer() {
        $crid = $this->input->post('rid');
		if(empty($crid) || !is_numeric($crid)) {
			$crid = 0;
		}
        $user = Ebh::app()->user->getloginuser();
        $q = $this->input->post('q');
        $queryarr = array();
        $queryarr['crid'] = $crid;
        $queryarr['uid'] = $user['uid'];
        $queryarr['shield'] = 0;
		$begindate = $this->input->post('begindate');
		if(!empty($begindate)) {	//过滤回答时间
			$starttime = strtotime($begindate);
			if($starttime !== FALSE) {
				$queryarr['startDate'] = $starttime;
			}
		}
		$enddate = $this->input->post('enddate');
		if(!empty($enddate)) {	//过滤答题时间
			$endtime = strtotime($enddate);
			if($endtime !== FALSE) {
				$queryarr['endDate'] = $endtime + 86400;
			}
		}

		$page = $this->input->post('page');
		if(empty($page) || !is_numeric($page)) {
			$page = 1;
		}
		$queryarr['page'] = $page;
        $askmodel = $this->model('Askquestion');
        $asklist = $askmodel->getasklistbyanswers($queryarr);
		return $asklist;
    }
	/**
     * 我的关注
     */
    private function myfavorit() {
        $crid = $this->input->post('rid');
		if(empty($crid) || !is_numeric($crid)) {
			$crid = 0;
		}
        $user = Ebh::app()->user->getloginuser();
        $q = $this->input->get('q');
        $queryarr = array();
        $queryarr['crid'] = $crid;
		$page = $this->input->post('page');
		if(empty($page) || !is_numeric($page)) {
			$page = 1;
		}
		$queryarr['page'] = $page;
        $queryarr['uid'] = $user['uid'];
        $askmodel = $this->model('Askquestion');
        $asklist = $askmodel->getasklistbyfavorit($queryarr);
		return $asklist;
    }
	/**
	*提问
	*成功 $result['status'] = 0 失败 1
	*/
	public function add() {
		$user = Ebh::app()->user->getloginuser();
		$crid = $this->input->post('rid');
		if(empty($crid) || !is_numeric($crid)) {
			$crid = 0;
		}
		$result = array();
		if($crid < 0) {
			$result['status'] = 1;
			echo json_encode($result);
			exit();
		}
		//处理权限
		if($user['groupid'] == 6) {
			$check = Ebh::app()->room->checkstudent(TRUE);
			if($check != 1) {
				$result['status'] = 1;
				echo json_encode($result);
				exit();
			}
		}
		$type = $this->input->post('type');
		if(empty($type)) {
			$type = 0;
		} else {
			$type = intval($type);
		}

		if($type == 1) {	//针对回答操作
			return $this->addanswer();
		}
		$qid = $this->input->post('qid');
		$qid = intval($qid);
		$folderid = $this->input->post('fid');
		$folderid = intval($folderid);
		$tid = $this->input->post('tid');
		$tid = intval($tid);
        $cwid = intval($this->input->post('cwid'));
		$title = $this->input->post('title');	//问题标题
//		if(!empty($title))
//			$title = iconv("GBK","UTF-8//IGNORE",$title) ;
		$message = $this->input->post('txt');		//问题正文
//		if(!empty($message))
//			$message = iconv("GBK","UTF-8//IGNORE",$message) ;
		$img = $this->input->post('img');
		$imgname = $this->input->post('imgname');
		//检查是否有敏感词语
		EBH::app()->helper('simpledict');
		if (!checkSensitive($title) || !checkSensitive($message)){
			$result['status'] = 2;
			echo json_encode($result);
			exit;
		}
		
		$imagefileinfo = $this->uploadfile('image','pic','277_195');	//图片上传处理
		
		$imagesrc = '';
		$imagename = '';
		if($imagefileinfo['state'] == 'SUCCESS') {
			$imagesrc = $imagefileinfo['showurl'];
			$imagename = $imagefileinfo['name'];
		}
		$audiofileinfo = $this->uploadfile('audio');	//音频上传处理
		$audiosrc = '';
		$audioname = '';
		if($audiofileinfo['state'] == 'SUCCESS') {
			$audiosrc = $audiofileinfo['showurl'];
			$audioname = $audiofileinfo['name'];
		}
		$attfileinfo = $this->uploadfile('att');	//相关附件上传处理
		$attsrc = '';
		$attname = '';
		if($attfileinfo['state'] == 'SUCCESS') {
			$attsrc = $attfileinfo['showurl'];
			$attname = $attfileinfo['name'];
		}
		$fromip = $this->input->getip();
		$param = array('uid'=>$user['uid'],'crid'=>$crid,'folderid'=>$folderid,'tid'=>$tid,'title'=>$title,'message'=>$message,'imagename'=>$imgname,'imagesrc'=>$img,'audioname'=>$audioname,'audiosrc'=>$audiosrc,'attname'=>$attname,'attsrc'=>$attsrc,'fromip'=>$fromip);
		$askmodel = Ebh::app()->model('Askquestion');
		if($qid > 0) {	//编辑问题
			$isedit = true;
			$param['qid'] = $qid;
			$affectrow = $askmodel->update($param);
			if($affectrow !== FALSE) {
				$result['status'] = 0;
			} else {
				$result['status'] = 1;
			}
		} else {	//添加问题
            if ($cwid > 0) {
                if ($courseware = $this->model('courseware')->getcoursedetail($cwid)) {
                    $param['cwid'] = $cwid;
                    $param['cwname'] = $courseware['cwname'];
                }

            }
			$qid = $askmodel->insert($param);
			if($qid > 0) {
				//教室问题数加1
				$this->model('classroom')->addasknum($crid);
				$result['status'] = 0;	//成功返回0
			} else {
				$result['status'] = 1;
			}
		}
		echo json_encode($result);

		fastcgi_finish_request();
		if($qid > 0 && !$isedit){
			Ebh::app()->lib('PushUtils')->PushAskToTeacher($qid);//信鸽推送
			Ebh::app()->lib('ThirdPushUtils')->PushAskToTeacher($qid); //第三方推送

			$credit = $this->model('credit');
			$credit->addCreditlog(array('ruleid'=>15,'qid'=>$qid));
		}
		if($qid > 0 && $audiofileinfo['state'] == 'SUCCESS') {
			$sourcepath = $audiofileinfo['url'];
			$this->amr2mp3($qid,0,$user['uid'],$sourcepath,$audiosrc);
		}

		//从微信服务器下载图片文件
		$pic_id = $this->input->post('pic_id');
		if(!empty($pic_id)){//微信附件上传处理
			$url = 'http://up.ebh.net/wxupload.html';
			$data = array('type'=>'img','media_id'=>$pic_id,'size'=>'277_195');
			$imagefileinfo = do_post($url,$data,false);
			if(!empty($imagefileinfo) && ($imagefileinfo->state == 'SUCCESS')) {
				$imagesrc = $imagefileinfo->showurl;
				$imagename = $imagefileinfo->name;
			}
		}
		$voice_id = $this->input->post('voice_id');
		//从微信服务器下载音频
		if(!empty($voice_id)){
			$url = 'http://up.ebh.net/wxupload.html';
			$data = array('type'=>'audio','media_id'=>$voice_id,'size'=>'277_195');
			$audiofileinfo = do_post($url,$data,false);
			if(!empty($audiofileinfo) && ($audiofileinfo->state == 'SUCCESS') ) {
				$sourcepath = $audiofileinfo->url;
				$audiosrc = $audiofileinfo->showurl;
				$url = 'http://up.ebh.net/wxupload/amr2mp3.html';
				$data = array('sourcepath'=>$sourcepath,'audiosrc'=>$audiosrc);
				$res = do_post($url,$data,false);
				if($res->status == 0) {
					$askmodel = $this->model('Askquestion');
					$aiext = strripos($audiosrc,'.');
					$mp3ext = '.mp3';
					$audiodestsrc = substr($audiosrc,0,$aiext).$mp3ext; 
					if(empty($aid)) {	//修改提问
						$param = array('qid'=>$qid,'uid'=>$user['uid'],'audiosrc'=>$audiodestsrc);
						$afrows = $askmodel->update($param);
					} else {	//修改回答
						$param = array('qid'=>$qid,'aid'=>$aid,'uid'=>$user['uid'],'audiosrc'=>$audiodestsrc);
						$afrows = $askmodel->update($param);
					}
				}
			}
		}
		if(empty($imagesrc)){
			return;
		}
		$param = array(
			'qid'=>$qid,
			'uid'=>$user['uid'],
			'imagesrc'=>$imagesrc,
			'imagename'=>$imagename,
		);
				
		$affectrow = $askmodel->update($param);
		if($affectrow !== FALSE) {
			// $result['status'] = 0;
		} else {
			log_message('修改问题：'.$qid.'失败，触发原因：从微信服务器下载图片成功了，但是写入数据库的时候失败啦');
		}
		
	}
	/**
	*添加或编辑我的回答
	*成功 $result['status'] = 0 失败 1
	*/
	private function addanswer() {
		$user = Ebh::app()->user->getloginuser();
		$askmodel = Ebh::app()->model('Askquestion');
		$qid = $this->input->post('qid');
		$qid = intval($qid);
		$aid = $this->input->post('aid');
		$aid = intval($aid);
		$result = array();
		if($qid <= 0) {
			$result['status'] = 1;
			echo json_encode($result);
			exit();
		}
		$message = h($_POST['txt']);
//		if(!empty($message))
//			$message = iconv("GB2312","UTF-8//IGNORE",$message) ;

		//检查是否有敏感词语
		EBH::app()->helper('simpledict');
		if (!checkSensitive($message)){
			$result['status'] = 2;
			echo json_encode($result);
			exit;
		}

		$imagefileinfo = $this->uploadfile('image','pic','277_195');	//图片上传处理
		$imagesrc = '';
		$imagename = '';
		if($imagefileinfo['state'] == 'SUCCESS') {
			$imagesrc = $imagefileinfo['showurl'];
			$imagename = $imagefileinfo['name'];
		}
		$audiofileinfo = $this->uploadfile('audio');	//音频上传处理
		
		$audiosrc = '';
		$audioname = '';
		if($audiofileinfo['state'] == 'SUCCESS') {
			$audiosrc = $audiofileinfo['showurl'];
			$audioname = $audiofileinfo['name'];
		}
		
		$attfileinfo = $this->uploadfile('att');	//相关附件上传处理
		$attsrc = '';
		$attname = '';
		if($attfileinfo['state'] == 'SUCCESS') {
			$attsrc = $attfileinfo['showurl'];
			$attname = $attfileinfo['name'];
		}
		$fromip = $this->input->getip();
		$param = array('qid'=>$qid,'uid'=>$user['uid'],'message'=>$message,'audioname'=>$audioname,'audiosrc'=>$audiosrc,'imagename'=>$imagename,'imagesrc'=>$imagesrc,'attname'=>$attname,'attsrc'=>$attsrc,'fromip'=>$fromip);
		
		$isedit = false; //修复第457报notice警告
		if($aid > 0) {	//编辑回答
			$isedit = true;
			$param['aid'] = $aid;
			$affectrow = $askmodel->updateanswer($param);
			if($affectrow !== FALSE) {
				$result['status'] = 0;
			} else {
				$result['status'] = 1;
			}
		} else {	//添加回答

			$aid = $askmodel->addanswer($param);
			if($aid > 0) {
				$result['status'] = 0;
			} else {
				$result['status'] = 1;
			}
		}
		echo json_encode($result);
		if($aid > 0) {
			fastcgi_finish_request();
			if(!$isedit){
				Ebh::app()->lib('PushUtils')->PushAskToStudent($qid);
				Ebh::app()->lib('ThirdPushUtils')->PushAskToStudent($qid);
				
				$credit = $this->model('credit');
				$credit->addCreditlog(array('ruleid'=>21,'qid'=>$qid));
			}
			$ask = $askmodel->getdetailaskbyqid($qid, $user['uid']);
			$upparam = array(
                'qid'=>$qid,
                'uid'=>$ask['uid'],
                'lastansweruid'=>$user['uid']
            );
	        $askmodel->update($upparam);
			if($ask['tid'] == $user['uid']){
	            $askmodel->setAnswered($qid,1);
	            //短信发送
               EBH::app()->lib('SMS')->run($qid,$user['uid'],2);
	        }
	        if($audiofileinfo['state'] == 'SUCCESS'){
	        	$sourcepath = $audiofileinfo['url'];
				$this->amr2mp3($qid,$aid,$user['uid'],$sourcepath,$audiosrc);
	        }
		}
	}
	/**
* 上传答疑的相关附件
* @param string $upfield 上传$_FILES的字段名
* @param string $type 附件类型
* @param string $imagesize 当为图片类型时，需要处理的图片缩略图尺寸
*/
	private function uploadfile($upfield='',$type='',$imagesize='') {
		if(empty($upfield))
			return '';
		$uplib = Ebh::app()->lib('Uploader');
		//上传配置
		$config = array(
			"savePath" => "uploads/" ,             //存储文件夹
			"showPath" => "uploads/" ,              //显示文件夹
			"maxSize" => 5242880 ,                   //允许的文件最大尺寸，单位字节 5M
			"allowFiles" => array( ".ebh" , ".ebhp" , ".wav" , ".jpg" , ".jpeg" ,".png",".amr" ,".mp3" )  //允许的文件格式
		);
		$_UP = Ebh::app()->getConfig()->load('upconfig');
		$up_type = 'ask';
		$savepath = 'uploads/';
		$showpath = 'uploads/';
		if(!empty($_UP[$up_type]['savepath'])){
			$savepath = $_UP[$up_type]['savepath'];
		}
		if(!empty($_UP[$up_type]['showpath'])){
			$showpath = $_UP[$up_type]['showpath'];
		}
		$config['savePath'] = $savepath;
		$config['showPath'] = $showpath;
		$uplib->setFolder(NULL);
		$uplib->setName(NULL);
		$uplib->init($upfield,$config);
		$info = $uplib->getFileInfo();
		//如果是图片，并且需要裁减，则根据尺寸进行裁减
		if($type == 'pic' && $info['state'] == 'SUCCESS' && !empty($imagesize)) { //答疑上传的图片需要裁减
			Ebh::app()->helper('image');
			$imagepath = $info['url'];
			$imagesapath = $savepath.$imagepath;
			// thumb($imagesapath,$imagesize);
			thumb($imagesapath,$imagesize)||copyimg($imagesapath,$imagesize);
		}
		return $info;
	}

	/**
	*问题详情
	*/
	public function detail() {
		$askitem = array();
		$user = Ebh::app()->user->getloginuser();
		$qid = $this->input->post('qid');
		$crid = $this->input->post('rid');
		$key = $this->input->post('k');
		$qid = intval($qid);
		$crid = intval($crid);
		if(!empty($user) && $qid > 0 && $crid > 0) {	//非法参数
			$key = urlencode($key);
			$askitem['aurl'] = "http://www.ebanhui.com/sitecp.html?action=ctlogin&k=$key&type=iask&qid=$qid&rid=$crid";
		}
		echo json_encode($askitem);
	}
	/**
	*设置最佳答案
	*/
	public function best() {
		$qid = $this->input->post('qid');
		$aid = $this->input->post('aid');
        $user = Ebh::app()->user->getloginuser();
		$result = array();
		if ($qid === NULL || !is_numeric($qid) || $aid === NULL || !is_numeric($aid)) {
			$result['status'] = 1;
            echo json_encode();
            exit();
        }
		$param = array('uid' => $user['uid'], 'qid' => $qid, 'aid'=>$aid);
		$askmodel = $this->model('Askquestion');
		$res = $askmodel->setbest($param);
		if (!empty($res)) {
            $result['status'] = 0;
			$credit = $this->model('credit');
			$credit->addCreditlog(array('ruleid'=>14,'aid'=>$aid));
        } else {
            $result['status'] = 1;
        }
		echo json_encode($result);
	}
	/**
	*添加或者取消关注
	*/
	public function addfavorite() {
		$qid = $this->input->post('qid');
        $user = Ebh::app()->user->getloginuser();
		$result = array();
        if ($qid === NULL || !is_numeric($qid) || $qid <= 0) {
            $result['status'] = 1;
            echo json_encode();
            exit();
        }
        $type = $this->input->post('type');	//type为1表示关注 0表示取消关注
		if($type != 1) {
			$type = 0;
		}
        $param = array('uid' => $user['uid'], 'qid' => $qid);
        $askmodel = $this->model('Askquestion');
        if ($type == 1) {
            $result = $askmodel->addfavorit($param);
        } else {
            $result = $askmodel->delfavorit($param);
        }
        if ($result > 0) {	//操作成功
            $result['status'] = 0;
            echo json_encode();
        } else {
			$result['status'] = 1;
            echo json_encode();
		}
	}
	/**
	*删除我的问题
	*/
	public function del() {
		$qid = $this->input->post('qid');
		$crid = $this->input->post('rid');
        $user = Ebh::app()->user->getloginuser();
		$result = array();
        if ($qid === NULL || !is_numeric($qid) || $qid <= 0 || $crid === NULL || !is_numeric($crid) || $crid <= 0 ) {
            $result['status'] = 1;
			echo json_encode($result);
			exit();
        }
        $askmodel = $this->model('Askquestion');
        $ask = $askmodel->getaskbyqid($qid);
        if (empty($ask) || $ask['crid'] != $crid || $ask['uid'] != $user['uid']) {
            $result['status'] = 1;
			echo json_encode($result);
			exit();
        }
        $delresult = $askmodel->delask($qid);
        if ($delresult) {
            $result['status'] = 0;
			echo json_encode($result);
        } else {
            $result['status'] = 1;
			echo json_encode($result);
        }
	}
	/**
	*删除我的回答
	*/
	public function delanswer() {
		$qid = $this->input->post('qid');
		$aid = $this->input->post('aid');
        $user = Ebh::app()->user->getloginuser();
		$result = array();
        if ($qid === NULL || !is_numeric($qid) || $qid <= 0 || $aid === NULL || !is_numeric($aid) || $aid <= 0) {
            $result['status'] = 1;
			echo json_encode($result);
			exit();
        }
        $param = array('qid' => $qid, 'aid' => $aid, 'uid' => $user['uid']);
        $askmodel = $this->model('Askquestion');
        $delresult = $askmodel->delanswer($param);
        if ($delresult > 0) {
            $result['status'] = 0;
			echo json_encode($result);
        } else {
			$result['status'] = 1;
			echo json_encode($result);
		}
	}
	/**
     * 添加问题的感谢
     */
    public function addthank() {
		$type = $this->input->post('type');
		if($type == 1) {	//type为1表示表示对回答的感谢
			return $this->addthankanswer();
		}
		$result = array();
        $qid = $this->input->post('qid');
        $user = Ebh::app()->user->getloginuser();
        if ($qid === NULL || !is_numeric($qid) || $qid <= 0) {
            $result['status'] = 1;
			echo json_encode($result);
            exit();
        }
		$reviewmodel = $this->model('Review');
		$logparam =  array('uid'=>$user['uid'],'toid'=>$qid,'opid'=>1,'type'=>'addthankanswer');//value 0为投票，不需要加入review表 1为评论 需要加入review表
		$lasttime = $reviewmodel->getLastLogTime($logparam);
		$today = date('Y-m-d');
		$todaybegintime = strtotime($today);
		if(!empty($lasttime) && ($lasttime >= $todaybegintime) ) {	//一天只能一次投票
			$result['status'] = 1;
			$result['msg'] = '今天已经感谢过了';
			echo json_encode($result);
			exit();
		}
        $askmodel = $this->model('Askquestion');
        $addresult = $askmodel->addthank($qid);
        if ($addresult > 0) {
			$logparam['message'] = '回答感谢';
			$logparam['fromip'] = $this->input->getip();
			$reviewmodel->insertlog($logparam);
            $result['status'] = 0;
			echo json_encode($result);
        } else {
			$result['status'] = 1;
			echo json_encode($result);
		}
    }

    /**
     * 添加回答的感谢
     */
    private function addthankanswer() {
        $qid = $this->input->post('qid');
        $aid = $this->input->post('aid');
        $user = Ebh::app()->user->getloginuser();
        if ($qid === NULL || !is_numeric($qid) || $aid === NULL || !is_numeric($aid)) {
            $result['status'] = 1;
			echo json_encode($result);
            exit();
        }
		$reviewmodel = $this->model('Review');
		$logparam =  array('uid'=>$user['uid'],'toid'=>$aid,'opid'=>1,'type'=>'addthankanswer');//value 0为投票，不需要加入review表 1为评论 需要加入review表
		$lasttime = $reviewmodel->getLastLogTime($logparam);
		$today = date('Y-m-d');
		$todaybegintime = strtotime($today);
		if(!empty($lasttime) && ($lasttime >= $todaybegintime) ) {	//一天只能一次投票
			$result['status'] = 1;
			$result['msg'] = '今天已经感谢过了';
			echo json_encode($result);
			exit();
		}
        $param = array('qid' => $qid, 'aid' => $aid);
        $askmodel = $this->model('Askquestion');
        $addresult = $askmodel->addthankanswer($param);
        if ($addresult > 0) {
			$logparam['message'] = '回答感谢';
			$logparam['fromip'] = $this->input->getip();
			$reviewmodel->insertlog($logparam);
            $result['status'] = 0;
			echo json_encode($result);
        } else {
			$result['status'] = 1;
			echo json_encode($result);
		}
    }
	/**
	*将上传的amr音频转换成MP3格式，并且更新数据库
	*@param int $qid 原问题id
	*@param int $aid 回答编号，如果为0表示此问题针对提问的修改
	*@param int $uid 所属用户编号
	*@param string $sourcepath 原文件保存路径
	*@param string $destpath 目标文件保存路径
	*@param string $audiosrc 源文件保存相对路径
	*/
	private function amr2mp3($qid,$aid,$uid,$sourcepath,$audiosrc) {
		$mp3ext = '.mp3';
		$iext = strripos($sourcepath,'.');
		$sname = substr($sourcepath,0,$iext);
		$ext = substr($sourcepath,$iext);
		if($ext == '.wav' || $ext == '.mp3')
			return TRUE;
		$destpath = $sname.$mp3ext;

		$_UP = Ebh::app()->getConfig()->load('upconfig');
		
		$up_type = 'ask';
		$savepath = 'uploads/';
		$showpath = 'uploads/';
		if(!empty($_UP[$up_type]['savepath'])){
			$savepath = $_UP[$up_type]['savepath'];
		}

		$ffmpeglib = Ebh::app()->lib('Ffmpeg');
		$result = $ffmpeglib->amr2mp3($savepath.$sourcepath,$savepath.$destpath);
		$afrows = false;
		if($result) {
			$askmodel = $this->model('Askquestion');
			$aiext = strripos($audiosrc,'.');
			$audiodestsrc = substr($audiosrc,0,$aiext).$mp3ext; 
			if(empty($aid)) {	//修改提问
				$param = array('qid'=>$qid,'uid'=>$uid,'audiosrc'=>$audiodestsrc);
				$afrows = $askmodel->update($param);
			} else {	//修改回答
				$param = array('qid'=>$qid,'aid'=>$aid,'uid'=>$uid,'audiosrc'=>$audiodestsrc);
				$afrows = $askmodel->update($param);
			}
		}
		if($afrows !== false)
			return TRUE;
		return FALSE;
	}

	//问题详情(附加第一页回答)
	public function qdetail() {
		$user = Ebh::app()->user->getloginuser();
		$switchuser = EBH::app()->lib('UserUtil')->setFaceSize('40_40')->init(array($user),array('uid'),true);
		$user = array(
			'uid'=>$switchuser[0]['uid'],
			'name'=>$switchuser[0]['uid_name'],
			'face'=>$switchuser[0]['uid_face'],
			'groupid'=>$switchuser[0]['groupid']
		);
		$qid = $this->input->post('qid');
		$crid = $this->input->post('rid');
		$ret = array();
        $ret['user'] = $user;
		//答疑详情
		$askmodel = $this->model('askquestion');
		$qdetail = $askmodel->getdetailaskbyqid($qid,$user['uid']);
		$askmodel->setviewnum($qid,'viewnum+1');
		if(empty($qdetail)){
			$ret['status'] = 1;
			echo json_encode($ret);exit;
		}else{
			$ret['status'] = 0;
		}
		$switchqdetail = EBH::app()->lib('UserUtil')->setFaceSize('40_40')->init(array($qdetail),array('uid'),true);
		if(!empty($switchqdetail[0]['imagesrc'])){
			$switchqdetail[0]['small_imagesrc'] = $switchqdetail[0]['imagesrc'];
		}else{
			$switchqdetail[0]['small_imagesrc'] = "";
		}
		$qdetail = array(
			'qid'=>$switchqdetail[0]['qid'],
			'uid'=>$switchqdetail[0]['uid'],
			'name'=>$switchqdetail[0]['uid_name'],
			'face'=>$switchqdetail[0]['uid_face'],
			'folderid'=>$switchqdetail[0]['folderid'],
			'foldername'=>$switchqdetail[0]['foldername'],
			'answercount'=>$switchqdetail[0]['answercount'],
			'thankcount'=>$switchqdetail[0]['thankcount'],
			'hasbest'=>$switchqdetail[0]['hasbest'],
			'status'=>$switchqdetail[0]['status'],
			'audiosrc'=>$switchqdetail[0]['audiosrc'],
			'audioname'=>$switchqdetail[0]['audioname'],
			'imagename'=>$switchqdetail[0]['imagename'],
			'imagesrc'=>$switchqdetail[0]['imagesrc'],
			'small_imagesrc'=>$switchqdetail[0]['small_imagesrc'],
			'title'=>$switchqdetail[0]['title'],
			'message'=>$switchqdetail[0]['message'],
			'dateline'=>date('Y-m-d H:i',$switchqdetail[0]['dateline']),
			'audiotime'=>$switchqdetail[0]['audiotime']
		);
		$ret['question'] = $qdetail;
		$ret['answers'] = $this->answerlist(true);
		echo json_encode($ret);
		fastcgi_finish_request();
		Ebh::app()->lib('Viewnum')->addViewnum('askquestion',$qid);
	}

	/**
	 *获取回答列表
	 */
	public function answerlist($ifReturn = false){
		//回答答疑列表
		$param = array();
		$qid = $this->input->post('qid');
		$param['shield'] = 0;
		$param['page'] = intval($this->input->post('page'));
		$askmodel = $this->model('askquestion');
		$askanswers = $askmodel->getdetailanswersbyqid($qid,$param);
		if(!empty($askanswers)){
			$askanswers = EBH::app()->lib('UserUtil')->setFaceSize('40_40')->init($askanswers,array('uid'),true);
			$newAskAnswers = array();
			foreach ($askanswers as  $askanswer) {
				$newAskAnswers[] = array(
					'aid'=>$askanswer['aid'],
					'uid'=>$askanswer['uid'],
					'audio'=>$askanswer['audio'],
					'image'=>$askanswer['image'],
					'name'=>$askanswer['uid_name'],
					'face'=>$askanswer['uid_face'],
					'txt'=>$askanswer['txt'],
					'isbest'=>$askanswer['isbest'],
					'dateline'=>date('Y-m-d H:i',$askanswer['date'])
				);
			}
			$askanswers = $newAskAnswers;
		}
		if($ifReturn == true){
			return $askanswers;
		}else{
			echo json_encode($askanswers);
		}
	}

	/**
	 *获取教师课程问题
	 */
	public function folderask(){
		$crid = $this->input->post('rid');
		$uid = $this->user['uid'];
		$param = array(
			'crid'=>$crid,
			'uid'=>$uid
		);
		$folderlist = $this->model('folder')->getTeacherFolderList1($param);
		if(empty($folderlist)){
			return array();
		}
		$folderids = getFieldArr($folderlist,'folderid');
		$page = intval($this->input->post('page'));
		if(empty($page)){
			$page = 1;
		}
		$param_for_ask = array(
			'page'=>$page,
			'shield'=>0
		);
		$askQModel = $this->model('askquestion');
		return  $askQModel->get_folder_ask($folderids,$param_for_ask);
	}

	/**
	 *获取要求当前教师回答的问题
	*/
	public function my_t(){
		$crid = intval($this->input->post('rid'));
		$page = intval($this->input->post('page'));
		$tid = $this->user['uid'];
		if(empty($page)){
			$page = 1;
		}
		$param = array(
			'crid'=>$crid,
			'page'=>$page,
			'tid'=>$tid,
			'shield'=>0
		);
		$askQModel = $this->model('askquestion');
		return $askQModel->get_required_ask($param);
	}

	
	public function getMedia($media_id,$imagesize=''){
		if(empty($media_id)){
			return;
		}
		$uplib = Ebh::app()->lib('WxUploader');
		//上传配置
		$config = array(
			"savePath" => "uploads/" ,             //存储文件夹
			"showPath" => "uploads/" ,              //显示文件夹
			"maxSize" => 5242880 ,                   //允许的文件最大尺寸，单位字节 5M
			"allowFiles" => array( ".ebh" , ".ebhp" , ".wav" , ".jpg" , ".jpeg" ,".png",".amr" ,".mp3" )  //允许的文件格式
		);
		$_UP = Ebh::app()->getConfig()->load('upconfig');
		$up_type = 'ask';
		$savepath = 'uploads/';
		$showpath = 'uploads/';
		if(!empty($_UP[$up_type]['savepath'])){
			$savepath = $_UP[$up_type]['savepath'];
		}
		if(!empty($_UP[$up_type]['showpath'])){
			$showpath = $_UP[$up_type]['showpath'];
		}
		$config['savePath'] = $savepath;
		$config['showPath'] = $showpath;
		$uplib->setFolder(NULL);
		$uplib->setName(NULL);
		$uplib->init($media_id ,$config);

		$info = $uplib->getFileInfo();
		//如果是图片，并且需要裁减，则根据尺寸进行裁减
		if($info['state'] == 'SUCCESS' && !empty($imagesize)) { //答疑上传的图片需要裁减
			if(strpos($info['type'],"image")!==FALSE){
				Ebh::app()->helper('image');
				$imagepath = $info['url'];
				$imagesapath = $savepath.$imagepath;
				// thumb($imagesapath,$imagesize);
				thumb($imagesapath,$imagesize)||copyimg($imagesapath,$imagesize);
			}
		}
		return $info;
	}
	//获取问+答达人榜数据
	public function topaskandanswer(){
		$crid = intval($this->input->post('crid'));
		$askModel = $this->model('Askquestion');
		$qarr = empty($crid) ? array() : array('crid'=>$crid);
		$ret = $askModel->gettopaskandanswer($qarr);
		
		//获取用户信息
		$nusers = $uid = $users = array();
		if(!empty($ret)){
			$userModel = $this->model('User');
			foreach ($ret as $user){
				$uid[] = $user['uid'];
				$numarr[$user['uid']] = $user['tnum'];
			}
			$users = $userModel->getUserInfoByUid($uid);
			//用户信息格式化
			foreach ($users as $key=>$item){
				$users[$key]['name'] = !empty($item['realname']) ? $item['realname'] : $item['username'];
				$users[$key]['face'] = !empty($item['face']) ? $item['face'] : getavater($item);
				$users[$key]['num'] = $numarr[$item['uid']];
				$info[$item['uid']] = $users[$key]; 
			}	
			//顺序组装数据
			foreach ($uid as $kk=>$vv){
				if (!empty($info[$vv]))
					$nusers[] = $info[$vv];
			}
		}
		echo json_encode($nusers);
	}

	//上传答疑图片
	public function uploadimg(){
		$file = $this->input->post();
		foreach ($file as $key => $value) {
			$_FILES['img'][$key] = $file[$key];
		}
		$uplib = Ebh::app()->lib('Uploader');
		//上传配置
		$config = array(
			"savePath" => "uploads/" ,             //存储文件夹
			"showPath" => "uploads/" ,              //显示文件夹
			"maxSize" => 5242880 ,                   //允许的文件最大尺寸，单位字节 5M
			"allowFiles" => array(".jpg" , ".jpeg" , ".png" , ".gif" )  //允许的文件格式
		);
		$_UP = Ebh::app()->getConfig()->load('upconfig');
		$up_type = 'ask';
		$savepath = 'uploads/';
		$showpath = 'uploads/';
		if(!empty($_UP[$up_type]['savepath'])){
			$savepath = $_UP[$up_type]['savepath'];
		}
		if(!empty($_UP[$up_type]['showpath'])){
			$showpath = $_UP[$up_type]['showpath'];
		}
		$config['savePath'] = $savepath;
		$config['showPath'] = $showpath;
		$uplib->setFolder(NULL);
		$uplib->setName(NULL);
		//echo json_encode($file['name']);
		$uplib->init('img',$config);
		$info = $uplib->getFileInfo();
		echo json_encode($info);
	}
}
