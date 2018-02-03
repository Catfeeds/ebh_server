<?php
/**
 *网校列表控制器
 */
class SchoolController extends CControl{
	public function index(){
		$classroommodel = $this->model('classroom');
		$q = $this->input->post('q');
		$page = $this->input->post('page');
		$sortmode = (int)$this->input->post('sortmode');
		$isschool = (int)$this->input->post('isschool');
		$property = (int)$this->input->post('property');
		$filterorder = (int)$this->input->post('filterorder');

		$param['q'] = $q;
		$param['page'] = $page;
		$param['property'] = $property;
		$param['isschool'] = $isschool;
		$param['filterorder'] = $filterorder;

		if($sortmode == 0)
			$param['order'] = 'cr.crid desc';
		//按价格
		elseif($sortmode ==1 )
			$param['order'] = 'cr.crprice desc,cr.displayorder asc';
		//按人气
		elseif($sortmode ==2 )
			$param['order'] = 'cr.stunum desc';
		//按最新
		elseif($sortmode ==3 )
			$param['order'] = 'cr.crid desc';
		//按推荐
		elseif($sortmode ==4 )
			$param['order'] = 'cr.displayorder asc';

		$defimg = Ebh::app()->getConfig()->load('defimg');
		$classroomlist = $classroommodel->getclassroomall($param);
		foreach ($classroomlist as &$classroom) {
			if(empty($classroom['cface'])){
				$classroom['cface'] = $defimg['classroom_face'];
			}
			$classroom['rid'] = $classroom['crid'];
			$classroom['summary'] = strip_tags($classroom['summary']);
		}
		echo json_encode($classroomlist);
	}

	/**
	*全校课程
	*/
	public function allcourse() {
		$user = Ebh::app()->user->getloginuser();
		if(empty($user)){
			$uid = 0;
		}else{
			$uid = $user['uid'];
		}
		$crid = $this->input->post('rid');
		if(empty($crid)){
			echo json_encode(array());
			exit;
		}
		$foldermodel = $this->model('Folder');
		$page = $this->input->post('page');
		if(empty($page) || !is_numeric($page)) {
			$page = 1;
		}
		$queryarr = array();
		$queryarr['page'] = $page;
		$crid = $this->input->post('rid');
		$queryarr['crid'] = $crid;
		$queryarr['pagesize'] = 2000;

		$roominfo = $this->getRoomInfo($crid);
		if($roominfo['isschool'] == 7) {
			//获取网校服务包中的课程
			$folderlist = $this->model('payitem')->getItemList($queryarr);
			$fid_in = array();
			foreach ($folderlist as $fkey => $folder) {
				$fid_in[] = $folder['folderid'];
			}
			if(empty($fid_in)){
				return array();
			}
			$param = array(
				'folderid'=>implode(',',$fid_in),
				'pagesize'=>$queryarr['pagesize'],
				'power'=>0
			);
			$folderlist = $foldermodel->getfolderlist($param);
		}else{
			$folderlist = $foldermodel->getfolderlist($queryarr);
		}

		if(empty($folderlist)){
			echo json_encode(array());
			exit;
		}
		//权限信息注入
		$folderlist = Ebh::app()->lib('PowerUtil')->init($folderlist,$uid)->setCrid($crid)->groupFolderByPackage($folderlist);//打包成标准接口返回数据格式
		foreach ($folderlist as &$folder) {
			if(empty($folder['tname'])){
				$folder['tname'] = "";
			}
			if(empty($folder['summary'])){
				$folder['summary'] = "";
			}
			$viewnum = Ebh::app()->lib('Viewnum')->getViewnum('folder',$folder['fid']);
			$folder['viewnum'] = empty($viewnum)?$folder['viewnum']:$viewnum;
		}
		echo json_encode($folderlist);
	}

    /**
     * 全新课程预览
     */
	public function courselist() {
        $roominfo = Ebh::app()->room->getcurroom();
        if (empty($roominfo)) {
            echo json_decode(array());
            exit();
        }
        $pagesize = $this->input->post('pagesize');
        $page = $this->input->post('page');
        $pid = intval($this->input->post('pid'));
        if ($pagesize != NULL && $page !== NULL) {
            $limit = array(
                'pagesize' => $pagesize,
                'page' => $page
            );
        } else if ($pagesize !== NULL) {
            $limit = $pagesize;
        } else {
            $limit = NULL;
        }
        if ($roominfo['isschool'] != 7) {
            $courselist = $this->model('Folder')->getCourseList($roominfo['crid'], $roominfo['isschool'], $pid, $limit);
        } else {
            $payItemModel = $this->model('Payitem');
            $mine = $payItemModel->getSchoolItems($roominfo['crid']);
            $courselist = array();
            $uniques = array();
            if (!empty($mine)) {
                $ranks = array_column($mine, 'prank');
                $itemids = array_keys($mine);
                //排序课程：包中的排序号升序、服务项ID降序
                array_multisort($ranks, SORT_ASC, SORT_NUMERIC,
                    $itemids, SORT_DESC, SORT_NUMERIC, $mine);
                foreach ($mine as $item) {
                    if (!isset($courselist[$item['pid']])) {
                        $courselist[$item['pid']] = array(
                            'pid' => $item['pid'],
                            'pname' => $item['pname'],
                            'items' => array()
                        );
                    }
                    if (isset($uniques[$item['folderid']])) {
                        //跳过重复课程
                        continue;
                    }
                    $courselist[$item['pid']]['items'][] = array(
                        'itemid' => $item['itemid'],
                        'folderid' => $item['folderid'],
                        'foldername' => $item['foldername'],
                        'img' => $item['img'],
                        'speaker' => $item['speaker'],
                        'pname' => $item['pname']
                    );
                    $uniques[$item['folderid']] = $item['folderid'];
                }
            }
            $others = $payItemModel->getSchItems($roominfo['crid']);

            if (!empty($others)) {
                $displays = $crids = $ranks = $itemids = array();
                foreach ($others as $item) {
                    $displays[] = $item['rdisplayorder'];
                    $crids[] = $item['crid'];
                    $ranks[] = $item['prank'];
                    $itemids[] = $item['itemid'];
                }
                array_multisort($displays, SORT_ASC, SORT_NUMERIC,
                    $crids, SORT_DESC, SORT_NUMERIC,
                    $ranks, SORT_ASC, SORT_NUMERIC,
                    $itemids, SORT_DESC, SORT_NUMERIC, $others);
                foreach ($others as $item) {
                    if (!isset($courselist[$item['pid']])) {
                        $courselist[$item['pid']] = array(
                            'pid' => $item['pid'],
                            'pname' => $item['pname'],
                            'items' => array()
                        );
                    }
                    if (isset($uniques[$item['folderid']])) {
                        //跳过重复课程
                        continue;
                    }
                    $courselist[$item['pid']]['items'][] = array(
                        'itemid' => $item['itemid'],
                        'folderid' => $item['folderid'],
                        'foldername' => $item['foldername'],
                        'img' => $item['img'],
                        'speaker' => $item['speaker'],
                        'pname' => $item['pname']
                    );
                    $uniques[$item['folderid']] = $item['folderid'];
                }
            }
            if ($pid > 0) {
                array_walk($courselist, function(&$pack, $pid, $filterpid) {
                    if ($pid != $filterpid) {
                        $pack['items'] = array();
                    }
                }, $pid);
            }
            $courselist = array_values($courselist);
        }
        array_walk($courselist, function(&$pack, $pid, $limit) {
            if ($pid < $limit['start'] || $pid > $limit['end']) {
                $pack['items'] = array();
            }
        }, array(
            'start' => ($page - 1) * $pagesize,
            'end' => ($page - 1) * $pagesize + $pagesize
        ));
        //$courselist = array_slice($courselist, ($page - 1) * $pagesize, $pagesize);
        echo json_encode($courselist);
        exit();
    }

	/**
	 *获取学校信息
	 */
	private function getRoomInfo($crid = 0){
		return $this->model('classroom')->getclassroomdetail($crid);
	}
}