<?php
/**
 *学生互动记录接口
 */
class IaclassroomlogController extends CControl{
	public function __construct() {
        parent::__construct();
        $user = Ebh::app()->user->getloginuser();
		if(empty($user)) {	//如果用户验证失败，则返回-1
			echo json_encode(array('status'=>-1,'msg'=>'用户信息已过期'));
			exit();
		}
    }
	//获取互动列表
	public function index(){
		$crid = $this->input->post('rid');
		if(empty($crid) || !is_numeric($crid)){
			echo json_encode(array());
			exit; 
		}
		$q = $this->input->post('q');
		$user = Ebh::app()->user->getloginuser();
		//获取这个学生所在该校所属的班级
		$classesModel = $this->model('classes');
		$stuClassInfo = $classesModel->getClassByUid($crid,$user['uid']);
		if(empty($stuClassInfo)){
			echo json_encode(array());
			exit;
		}
		$classTeacherList = $classesModel->getClassTeacherByClassid($stuClassInfo['classid']);
		if(empty($classTeacherList)){
			echo json_encode(array());
			exit;
		}
		$tidArr = array();
		foreach ($classTeacherList as $classTeacher) {
			array_push($tidArr, $classTeacher['uid']);
		}
		$tid_in = '('.implode(',', $tidArr).')';
		$param = array(
			'crid'=>intval($crid),
			'uid'=>$user['uid'],
			'tid_in'=>$tid_in,
			'q'=>$q,
			'classid'=>$stuClassInfo['classid']
		);
		$iaclassroomlogModel = $this->model('iaclassroomlog');
		$ialogList = $iaclassroomlogModel->getList($param);
		$newIalogList = array();
		foreach ($ialogList as $ialog) {
			$ialog['dateline'] = date('Y-m-d H:i:s',$ialog['dateline']);
			if(empty($ialog['img'])){
				$ialog['img'] = "";
			}
			if(empty($ialog['resource'])){
				$ialog['resource'] = "";
			}
			$newIalogList[] = $ialog;
		}
		// $ialogListCount = $iaclassroomlogModel->getListCount($param);
		if(empty($ialogList)){
			$newIalogList = array();
		}
		echo json_encode($newIalogList);
	}

	public function upanswer(){
		$user = Ebh::app()->user->getloginuser();
		$icid = $this->input->post('icid');
		if(empty($icid) || !is_numeric($icid)){
			echo json_encode(array('status'=>-1,'msg'=>'没有上传互动的icid或者参数非法'));
			exit();
		}
		//上传学生互动的图片
		if($user['groupid'] != 6)
			exit;
		EBH::app()->helper('image');
		$upfield = 'FileName';
		$upinfo = $this->uploadfile($upfield,'','iroom');
		///log_message(print_r($upinfo,true));
		if($upinfo['state'] == 'SUCCESS') {

			//缩略图处理
			$_UP = Ebh::app()->getConfig()->load('upconfig');
			$showpath = $_UP['iroom']['showpath'];
			$savepath = $_UP['iroom']['savepath'];
			$filepath = $savepath.$upinfo['url'];
			thumb($filepath,'126_126')||copyimg($filepath,'126_126');
			thumb($filepath,'800_600')||copyimg($filepath,'800_600');
			
			$ialogparam = array();
			$ialogparam['uid'] = $user['uid'];
			$ialogparam['icid']=$icid;
			$ialogparam['dateline'] = $ialogparam['lastpost'] = time();
			//获取互动记录模型
			$iaclassroomlogModel = $this->model('iaclassroomlog');
			$ialog = $iaclassroomlogModel->getialog($ialogparam);
			$result = -1;
			if(empty($ialog)) {	//如果还没有互动      记录，则直接生成
				$ialogparam['img'] = $upinfo['showurl'];
				$iclogid = $iaclassroomlogModel->_insert($ialogparam);
				if($iclogid > 0)
					$result = 0;
			} else {	//如果已经有互动记录，则更新
				$ialogparam = array();
				$ialogparam['img'] = $upinfo['showurl'];
				$ialogparam['lastpost'] = time();
				$iresult = $iaclassroomlogModel->_update($ialogparam,array('iclogid'=>$ialog['iclogid']));
				if($iresult !== FALSE)
					$result = 0;
			}
			echo json_encode(array('status'=>$result,'msg'=>'操作成功'));
		} else {
			echo json_encode(array('status'=>-1,'msg'=>'图片上传失败'));
		}
	}

	/**
	* 上传主观题答题的相关附件
	* @param string $upfield 上传$_FILES的字段名
	* @param string $url 原文件保存相对路径，如果存在，则直接更新该值 如 2013/04/19/120134aj4ljxx4zkbjyp7x.ebhn
	* @param string $configname 上传的配置项名称，即upconfig.php中对应的项
	*/
	private function uploadfile($upfield='',$url = '',$configname='') {
		$uploader = Ebh::app()->lib('Uploader');
		$uploader->setFolder(NULL);
		$uploader->setName(NULL);
		if(!empty($url)) {
			$pos = strrpos($url,'/');
			$folder = substr($url,0,$pos+1);
			$name = substr($url,$pos+1);
			$uploader->setFolder($folder);
			$uploader->setName($name);
		}
        //上传配置
        $config = array(
            "savePath" => "uploads/", //存储文件夹
            "showPath" => "uploads/", //显示文件夹
            "maxSize" => 209715200, //允许的文件最大尺寸，单位字节
            "allowFiles" => array(".ebh",".jpg",".jpeg",".png",".gif",".ebhn",".wav")  //允许的文件格式
        );
        $_UP = Ebh::app()->getConfig()->load('upconfig');
		$up_type = 'examcourse';
		if(!empty($configname)) {
			$up_type = $configname;
		}
        $upload_name = $upfield;
        $savepath = 'uploads/';
        $showpath = 'uploads/';
        if (!empty($_UP[$up_type]['savepath'])) {
            $savepath = $_UP[$up_type]['savepath'];
        }
        if (!empty($_UP[$up_type]['showpath'])) {
            $showpath = $_UP[$up_type]['showpath'];
        }
        $config['savePath'] = $savepath;
        $config['showPath'] = $showpath;

        $uploader->init($upload_name, $config);
        $info = $uploader->getFileInfo();
        return $info;
	}
}