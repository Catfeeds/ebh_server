<?php

/**
 * 控制器
 */
class FolderController extends CControl {
    public function index() {
        $folderlist = array();
        $user = Ebh::app()->user->getloginuser();
        $this->crid = $crid = $this->input->post('rid');
        if(!empty($user) && is_numeric($crid) && $crid > 0) {
            $foldermodel = $this->model('Folder');
            $type = intval($this->input->post('type'));
            if($type == 1) {	//全校课程
                $folderlist = $this->allcourse();
            } else if($type == 2) {	//收藏课程
                $folderlist = $this->favorite();
            } else if($type == 3){
                $folderlist = $this->folder_for_ask();
                echo json_encode($folderlist);
                exit;
            } else if($type == 4) { //免费课程
                $folderlist = $this->freecourse();
            }else {
                $folderlist = $this->mycourse();
            }
        }
        // $folderlist = $this->premissionInsert($folderlist);
        $roominfo = $this->getRoomInfo($crid);
        if($roominfo['isschool'] != 7){
            $folderlist = $this->get_fixed_folderlist($folderlist);
        }
        //获取任课教师
        $folderlist = $this->_getFolderTeachers($folderlist);

        $nfolderlist = $foldermodel->getUserExtFolderInfo($folderlist,$user['uid']);
        if(!empty($nfolderlist)){
            $folderlist = $nfolderlist;
        }
        echo json_encode($folderlist);
    }

    /**
     * 开通过的课程，包括以过期的课程，相同的课程显示最近的课程及相关的服务项服务包
     */
    public function mylessons() {
        $user = Ebh::app()->user->getloginuser();
        $this->crid = $crid = $this->input->post('rid');
        $pid = intval($this->input->post('pid'));
        $is_schoolmate  = $this->model('Classroom')->checkstudent($user['uid'], $crid, true);
        //$roominfo = $this->getRoomInfo($crid);
        $ret = $this->model('Userpermission')->getMyCourses($user['uid'], $this->crid, $is_schoolmate, true);
        $courses = array();
        //排序参数
        $mulArgs = array(
            'groupid' => array(),
            'dateline' => array()
        );
        //课程按包分组
        foreach ($ret as $item) {
            if ($item['itemid'] === null) {
                continue;
            }
            $dateline = $item['dateline'];
            $crid = $item['crid'];
            unset($item['enddate'], $item['crid'], $item['dateline']);
            if ($item['pid'] === null) {
                if (!isset($courses[0])) {
                    $courses[0] = array(
                        'pid' => -1,
                        'pname' => '其他课程',
                        'items' => array()
                    );
                }
                array_unshift($courses[0]['items'], $item);
                $mulArgs['groupid'][0] = PHP_INT_MAX;
                $mulArgs['dateline'][0] = $dateline;
                continue;
            }
            if ($crid != $this->crid) {
                $item['cannotpay'] = 0;
            }
            if (!isset($courses[$item['pid']])) {
                $courses[$item['pid']] = array(
                    'pid' => $item['pid'],
                    'pname' => $item['pname'],
                    'items' => array()
                );
            }
            array_unshift($courses[$item['pid']]['items'], $item);

            $mulArgs['groupid'][$item['pid']] = $crid == $this->crid ? 0 : 1;
            $mulArgs['dateline'][$item['pid']] = $dateline;
        }
        //课程分组排序，优先级：本校网校、其他网校课程、服务包丢失的课程升序；开通时间倒序
        array_multisort($mulArgs['groupid'], SORT_ASC, SORT_NUMERIC,
            $mulArgs['dateline'], SORT_DESC, SORT_NUMERIC, $courses);
        if ($pid != 0) {
            array_walk($courses, function(&$package, $k, $pid) {
                if ($pid != $package['pid']) {
                    unset($package['items']);
                }
            }, $pid);
        }
        echo json_encode($courses);
        exit();
    }

    /**
     * 租赁制网校课程列表
     */
    public function classlessons() {
        $this->crid = $crid = $this->input->post('rid');
        $classid = $this->input->post('classid');
        $user = Ebh::app()->user->getloginuser();
        $classModel = $this->model('Classes');
        $class = $classModel->getClassByUid($crid, $user['uid']);
        if (empty($class)) {
            echo json_encode(array());
            exit();
        }
        $ret = $this->model('Classes')->getCourseList($class['classid'], $crid, $classid == 1);
        if (empty($ret)) {
            echo json_encode(array());
            exit();
        }
        $ret = array(
            array('items' => array_values($ret))
        );
        echo json_encode($ret);
        exit();
    }

    /**
     *我的课程
     */
    private function mycourse2() {
        $user = Ebh::app()->user->getloginuser();
        $queryarr = array();
        $page = $this->input->post('page');
        if(empty($page) || !is_numeric($page)) {
            $page = 1;
        }
        $queryarr['page'] = $page;
        $queryarr['pagesize'] = 100;
        $crid = $this->input->post('rid');
        $classmodel = $this->model('Classes');
        $myclass = $classmodel->getClassByUid($crid,$user['uid']);
        $foldermodel = $this->model('Folder');
        $queryarr['crid'] = $crid;
        if(!empty($myclass['classid']))
            $queryarr['classid'] = $myclass['classid'];
        else{
            return $this->allcourse();
        }
        $folderlist = $foldermodel->getClassFolder($queryarr);
        return $folderlist;
    }

    /**
     *我的课程
     */
    private function mycourse() {
        $user = Ebh::app()->user->getloginuser();
        $queryarr = array();
        $page = $this->input->post('page');
        if(empty($page) || !is_numeric($page)) {
            $page = 1;
        }
        $crid = $this->input->post('rid');
        $folderids = $this->_getFolderids($user['uid'],$crid);
        $folderlist = $this->_getFolderInfo($folderids);
        if(empty($folderlist)){
            return $this->allcourse();
        }
        $folderlist = Ebh::app()->lib('PowerUtil')->init($folderlist,$user['uid'])->setCrid($crid)->groupFolderByPackage($folderlist,true);
        return $folderlist;
    }
    /**
     *全校课程
     */
    private function allcourse2() {
        $crid = $this->input->post('rid');
        $foldermodel = $this->model('Folder');
        $queryarr = array();
        $page = $this->input->post('page');
        if(empty($page) || !is_numeric($page)) {
            $page = 1;
        }
        $queryarr['page'] = $page;
        $queryarr['pagesize'] = 200;
        $crid = $this->input->post('rid');
        $queryarr['crid'] = $crid;
        $folderlist = $foldermodel->getfolderlist($queryarr);
        $folderlist = $this->get_fixed_folderlist($folderlist);
        return $folderlist;
    }

    private function allcourse(){
        $user = Ebh::app()->user->getloginuser();
        $crid = $this->input->post('rid');
        $foldermodel = $this->model('Folder');
        $queryarr = array();
        $page = $this->input->post('page');
        if(empty($page) || !is_numeric($page)) {
            $page = 1;
        }
        $queryarr['page'] = $page;
        $queryarr['pagesize'] = 200;
        $crid = $this->input->post('rid');
        $queryarr['crid'] = $crid;
        $roominfo = $this->getRoomInfo($this->crid);
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
        //获取在服务包其它网校课程
        $folderlist = Ebh::app()->lib('PowerUtil')->init($folderlist,$user['uid'])->setCrid($crid)->groupFolderByPackage($folderlist,true);
        return $folderlist;
    }
    /**
     *收藏的课程
     */
    private function favorite() {
        $crid = $this->input->post('rid');
        $user = Ebh::app()->user->getloginuser();
        $queryarr = array();
        $page = $this->input->post('page');
        if(empty($page) || !is_numeric($page)) {
            $page = 1;
        }
        $queryarr['page'] = $page;
        $queryarr['pagesize'] = 100;
        $queryarr['crid'] = $crid;
        $queryarr['uid'] = $user['uid'];
        $favoritemodel = $this->model('Favorite');
        $folderlist = $favoritemodel->getfolderfavoritelist($queryarr);
        return $folderlist;

    }
    /**
     *获取学校信息
     */
    private function getRoomInfo($crid = 0){
        return $this->model('classroom')->getclassroomdetail($crid);
    }

    /**
     *课程权限验证信息注入(注入itemid字段,用户有权限则itemid为0,否则大于0[表示没有权限，要收费啦])[仅针对isschool=7的学校],其它学校itemid一律为0
     */
    private function premissionInsert($folderlist = array()){
        $folders = $folderlist;
        $roominfo = $this->getRoomInfo($this->crid);
        if($roominfo['isschool'] == 7) {	//收费分成学校，则未开通或已过期的课程，就显示阴影和开通按钮
            $user = Ebh::app()->user->getloginuser();
            $userpermodel = $this->model('Userpermission');
            $myperparam = array('uid'=>$user['uid'],'crid'=>$roominfo['crid'],'filterdate'=>1);
            $myfolderlist = $userpermodel->getUserPayFolderList($myperparam);
            $roomfolderlist = $userpermodel->getPayItemByCrid($roominfo['crid']);
            $folderlist = array();
            foreach($folders as $myfolder) {
                $myfolder['haspower'] = 0;
                $myfolder['itemid'] = 0;
                if($myfolder['fprice'] == 0) {
                    $myfolder['haspower'] = 1;
                }
                $folderlist[$myfolder['fid']] = $myfolder;
            }
            $ofolderidstr = '';	//如果有权限的课程没有在当前页的课程内，则需要单独加上
            foreach($myfolderlist as $myfolder1) {	//看看哪些有权限
                if(isset($folderlist[$myfolder1['fid']])) {
                    $folderlist[$myfolder1['fid']]['haspower'] = 1;
                } else {
                    if(empty($ofolderidstr)) {
                        $ofolderidstr = $myfolder1['fid'];
                    } else {
                        $ofolderidstr = $ofolderidstr.','.$myfolder1['fid'];
                    }
                }
            }
            if(!empty($ofolderidstr)) {
                $foldermodel = $this->model('Folder');
                $oqueryarr = array('folderid'=>$ofolderidstr);
                $ofolderlist = $foldermodel->getfolderlist($oqueryarr);
                if(!empty($ofolderlist)) {
                    foreach($ofolderlist as $ofolder) {
                        $ofolder['haspower'] = 1;
                        $folderlist[$ofolder['fid']] = $ofolder;
                    }
                }
            }
            foreach($roomfolderlist as $myfolder2) {
                if(isset($folderlist[$myfolder2['fid']])) {
                    if($folderlist[$myfolder2['fid']]['haspower'] == 0) {
                        // $checkurl = 'http://'.$roominfo['domain'].'.'.$this->uri->curdomain.'/ibuy.html?itemid='.$myfolder2['itemid'];	//购买url
                        if(empty($myfolder2['itemid'])){
                            $myfolder2['itemid'] = 0;
                        }
                        if(empty($myfolder2['sid'])){
                            $myfolder2['sid'] = 0;
                        }
                        $folderlist[$myfolder2['fid']]['itemid'] = intval($myfolder2['itemid']);
                        $folderlist[$myfolder2['fid']]['sid'] = intval($myfolder2['sid']);
                    }
                }
            }
            $folders = $folderlist;

        }
        $returnFolders = array();
        foreach ($folders as $folder) {
            unset($folder['fprice']);
            unset($folder['haspower']);
            if(!isset($folder['itemid'])){
                $folder['itemid'] = 0;
            }
            if(!isset($folder['sid'])){
                $folder['sid'] = 0;
            }
            array_push($returnFolders, $folder);
        }
        return $returnFolders;
    }

    /**
     *将当前学生的年级课程排到最前面
     */
    private function get_fixed_folderlist($folderlist = array()){
        $folderlist_fixed = array();
        if(empty($folderlist)){
            return $folderlist_fixed;
        }
        $user = Ebh::app()->user->getloginuser();
        if($user['groupid'] == 5){
            return $folderlist_fixed;
        }
        $classmodel = $this->model('Classes');
        $crid = $this->input->post('rid');
        $myclass = $classmodel->getClassByUid($crid,$user['uid']);

        $myfolderlist_district_and_grade = array();//年级和地区都匹配
        $myfolderlist_grade = array();//年级匹配
        $otherfolderlist = array();//年级和地区都不匹配

        if(!empty($myclass)){
            foreach ($folderlist as $folder) {
                if(($folder['grade'] == $myclass['grade']) && ($folder['district'] == $myclass['district'])){
                    unset($folder['grade']);
                    unset($folder['district']);
                    array_push($myfolderlist_district_and_grade, $folder);
                }else if($folder['grade'] == $myclass['grade']){
                    unset($folder['grade']);
                    unset($folder['district']);
                    array_push($myfolderlist_grade, $folder);
                }else{
                    unset($folder['grade']);
                    unset($folder['district']);
                    array_push($otherfolderlist, $folder);
                }
            }
        }else{
            foreach ($folderlist as $folder) {
                unset($folder['grade']);
                unset($folder['district']);
                array_push($otherfolderlist, $folder);
            }
        }
        return array_merge($myfolderlist_district_and_grade,$myfolderlist_grade,$otherfolderlist);
    }

    /**
     *获取课程的老师
     *当前用户的所在班级的老师靠前排
     */
    public function teacher(){
        $fid = $this->input->post('fid');
        $crid = $this->input->post('rid');
        $ret = array();
        $ret_myteacher = array();
        if(!is_numeric($fid) || !is_numeric($crid)){
            echo json_encode($ret);
            exit;
        }
        //获取用户所在的班级
        $user = Ebh::app()->user->getloginuser();
        $uid = $user['uid'];
        $classinfo = $this->model('classes')->getClassByUid($crid,$uid);
        $tidArr = array();
        if(!empty($classinfo)){
            $classid = $classinfo['classid']; //用户所在的班级
            //获取用户所在的班级的老师
            $teachers = $this->model('classes')->getClassTeacherByClassid($classid);
            foreach ($teachers as $teacher) {
                $tidArr[] = $teacher['uid'];
            }
        }

        //获取教fid课程的老师
        $tidlist = $this->model('folder')->getTeacherListOfFolder($fid,$crid);
        if(!empty($tidlist)){
            $tidlist = EBH::app()->lib('UserUtil')->setFaceSize('120_120')->init($tidlist,array('tid'),true);
        }else{
            echo json_encode(array());exit;
        }

        //循环获取教师的用户名,并且把当前用户的老师和其它教Fid课程的老师分开
        foreach ($tidlist as $t) {
            $tmpT = array(
                'tid'=>$t['tid'],
                'tname'=>$t['tid_name'],
                'face'=>$t['tid_face']
            );
            if(in_array($t['tid'], $tidArr)){
                $ret_myteacher[] = $tmpT;
            }else{
                $ret[] = $tmpT;
            }
        }
        //当前用户的老师和其它老师合并(当前用户的老师提到返回结果的最前端)
        $ret = array_merge($ret_myteacher,$ret);
        echo json_encode($ret);
    }

    /**
     *用于提问的课程
     */
    private function folder_for_ask() {
        $crid = $this->input->post('rid');
        $foldermodel = $this->model('Folder');
        $queryarr = array();
        $page = $this->input->post('page');
        if(empty($page) || !is_numeric($page)) {
            $page = 1;
        }
        $queryarr['page'] = $page;
        $queryarr['pagesize'] = 100;
        $crid = $this->input->post('rid');
        $queryarr['crid'] = $crid;
        $folderlist = $foldermodel->getfolderlist($queryarr);
        $folderlist = $this->get_fixed_folderlist($folderlist);
        return $folderlist;
    }

    //根据课程folderid数组获取课程详情
    private function _getFolderInfo($folderids = array()){
        $ret = array();
        if(empty($folderids)){
            return $ret;
        }
        return $this->model('folder')->getFolderListByFolderids($folderids);
    }

    //获取用户课程folderids
    private function _getFolderids($uid,$crid){
        $roominfo = $this->getRoomInfo($crid);
        $myfolderlist = array();
        //开通课程的id
        if($roominfo['isschool']==7){
            $userpermodel = $this->model('Userpermission');
            $myperparam = array('uid'=>$uid,'crid'=>$crid);
            $myfolderlist = $userpermodel->getUserPayFolderList($myperparam);
        }else{
            $foldermodel = $this->model('folder');
            $classmodel = $this->model('Classes');
            $myclass = $classmodel->getClassByUid($crid,$uid);
            $paramf['crid'] = $roominfo['crid'];
            $paramf['classid'] = $myclass['classid'];
            $paramf['limit'] = 100;
            if(!empty($myclass['grade'])){
                $paramf['grade'] = $myclass['grade'];
                $myfolderlist = $foldermodel->getClassFolderWithoutTeacher($paramf);
            }else{
                $myfolderlist = $foldermodel->getClassFolder($paramf);
            }
        }
        $folderids = array();
        if(empty($myfolderlist)) {
            return array();
        }
        foreach ($myfolderlist as $folder) {
            $folderids[] = $folder['folderid'];
        }
        return array_unique($folderids);
    }

    //获取课程信息(连同学生的学习情况)
    public function folderinfo(){
        $ret = array();
        $folderid = $this->input->post('folderid');
        $user = Ebh::app()->user->getloginuser();
        $uid = 0;
        if(!empty($user)){
            /*echo json_encode($ret);
            exit();*/
            $uid = $user['uid'];
        }
        $ret = $this->model('folder')->getfolderlist(array('folderid'=>$folderid));
        if ($uid > 0) {
            //获取学分
            $rettemp = $this->model('folder')->getUserExtFolderInfo($ret,$uid);
            if (!empty($rettemp[0]))
                $ret = $rettemp[0];
        } else {
            echo json_encode($ret[0]);
            exit();
        }
        //获取介绍
        if(!empty($ret) && !empty($ret['introduce'])){
            $intro = unserialize($ret['introduce']);
            $ret['introduce'] = empty($intro) ? '' : $intro;
        }
        //获取任课老师
        $folderlist = $this->_getFolderTeachers(array($ret));
        $ret = $folderlist[0];
        echo json_encode($ret);
    }

    //获取课程简介
    public function intro(){
        $ret = array();
        $folderid = $this->input->post('folderid');
        $res = $this->model('folder')->getfolderbyid($folderid);
        if(!empty($res) && !empty($res['introduce'])){
            $intro = unserialize($res['introduce']);
            if(!empty($intro)){
                $ret = $intro;
            }
        }
        echo json_encode($ret);
    }

    /**
     * 获取课程授课老师
     */
    private function _getFolderTeachers($folderlist) {
        if (empty($folderlist))
            return $folderlist;

        $folderids = array();
        foreach ($folderlist as $folder) {
            $folderids[] = $folder['fid'];
        }
        $folderids = array_unique($folderids);

        $course = array();
        $courseteacherlist = $this->model('folder')->getFolderTeacherList($folderids);
        foreach ($courseteacherlist as $ct){
            $teachername = empty($ct['realname']) ? $ct['username'] : $ct['realname'];
            if(!empty($course[$ct['folderid']]['teachers'])){
                $course[$ct['folderid']]['teachers'].= ','.$teachername;
            }
            else{
                $course[$ct['folderid']]['teachers'] = $teachername;
            }
        }

        //教师信息,浏览数,长图片
        $viewnumlib = Ebh::app()->lib('Viewnum');
        foreach ($folderlist as $key => $value) {
            if (!empty($course[$value['fid']]['teachers']))
                $folderlist[$key]['teachers'] = $course[$value['fid']]['teachers'];
            else
                $folderlist[$key]['teachers'] = '';

            //获取浏览数
            $viewnum = $viewnumlib->getViewnum('folder',$value['fid']);
            if (!empty($viewnum)){
                $folderlist[$key]['viewnum'] = $viewnum;
            }
        }

        return $folderlist;
    }

    /**
     * 分成学校免费课程
     */
    private function freecourse(){
        $roominfo = $this->getRoomInfo($this->crid);
        $user = Ebh::app()->user->getloginuser();
        $schoolfreelist = array();
        //全校免费课程
        if($roominfo['isschool'] == 7){
            $rumodel = $this->model('roomuser');
            $userin = $rumodel->getroomuserdetail($roominfo['crid'],$user['uid']);
            if(!empty($userin)) {
                $schoolfreelist =  $this->model('folder')->getfolderlist(array('crid'=>$roominfo['crid'],'isschoolfree'=>1,'limit'=>100));
            }
        }

        return $schoolfreelist;
    }

    /**
     * 服务包列表
     */
    public function packages() {
        $crid = intval($this->input->post('rid'));
        $itemModel = $this->model('Payitem');
        $schItems = $itemModel->getSchItems($crid);
        $items = $itemModel->getSchoolItems($crid);
        //$bundleModel = $this->model('Bundle');
        //$bundles = $bundleModel->getBundles($crid);
        $items = array_merge($items, $schItems);
        $ret = array();
        //服务包排序优先级：网校排序号、网校ID、服务包排序号、服务包ID
        $displayorders = $pids = $crids = $rdisplayorders = array();
        foreach ($items as $item) {
            if (isset($ret[$item['pid']])) {
                continue;
            }
            $ret[$item['pid']] = array(
                'pid' => $item['pid'],
                'pname' => $item['pname'],
                'crid' => isset($item['crid']) ? $item['crid'] : 0,
                'displayorder' => $item['displayorder']
            );
            $displayorders[] = $item['displayorder'];
            $pids[] = $item['pid'];
            if (isset($item['crid'])) {
                $crids[] = $item['crid'];
                $rdisplayorders[] = max($item['rdisplayorder'], 0);//网校的排序号小于０的置０
            } else {
                $crids[] = 0;
                $rdisplayorders[] = -1;//本校的排序置为-1，排序时放在首位
            }
        }
        if (!empty($ret)) {
            array_multisort($rdisplayorders, SORT_ASC, SORT_NUMERIC,
                $crids, SORT_DESC, SORT_NUMERIC,
                $displayorders, SORT_ASC, SORT_NUMERIC,
                $pids, SORT_DESC, SORT_NUMERIC, $ret);
        }
        echo json_encode($ret);
        exit();
    }

    /**
     * 服务包分类
     */
    public function sorts() {
        $crid = intval($this->input->post('rid'));
        $pid = intval($this->input->post('pid'));
        $itemModel = $this->model('Payitem');
        $schItems = $itemModel->getSchItems($crid, $pid);
        $items = $itemModel->getSchoolItems($crid, $pid);
        $items = array_merge($items, $schItems);
        $ret = array();
        $displayorders = $sids = array();
        $hasOther = false;
        foreach ($items as $item) {
            if (isset($ret['sorts'][$item['sid']])) {
                continue;
            }
            if (empty($ret)) {
                $ret = array(
                    'pid' => $item['pid'],
                    'pname' => $item['pname'],
                    'sorts' => array()
                );
            }
            if (empty($item['sid'])) {
                $hasOther = true;
                continue;
            }
            $ret['sorts'][$item['sid']] = array(
                'sid' => $item['sid'],
                'sname' => $item['sname']
            );
            $displayorders[] = $item['sdisplayorder'];
            $sids[] = $item['sid'];
        }
        if (!empty($ret['sorts'])) {
            array_multisort($displayorders, SORT_ASC, SORT_NUMERIC,
                $sids, SORT_DESC, SORT_NUMERIC, $ret['sorts']);
        }
        if ($hasOther) {
            $ret['sorts'][] = array(
                'sid' => 0,
                'sname' => '其他课程'
            );
        }
        echo json_encode($ret);
        exit();
    }

    /**
     * 服务项
     */
    public function items() {
        $crid = intval($this->input->post('rid'));
        $pid = intval($this->input->post('pid'));
        $sid = intval($this->input->post('sid'));
        $orderby = '`b`.`displayorder` ASC,`a`.`itemid` DESC';
        $ret = $this->model('Payitem')->getItemsForSort($sid, $pid, $crid,0,$orderby);
        if (empty($ret)) {
            echo json_encode(array('acount' => 0));
            exit();
        }
        $user = Ebh::app()->user->getloginuser();
        $uid = $user['uid'];
        if (!empty($ret['items'])) {
            $folderids = array_column($ret['items'], 'fid');
            $folderids = array_unique($folderids);
            $teachers = $this->model('Folder')->getFolderTeacherList($folderids);
            $group = array();
            if (!empty($teachers)) {
                foreach ($teachers as $teacher) {
                    $group[$teacher['folderid']][] = !empty($teacher['realname']) ? $teacher['realname'] : $teacher['username'];
                }
            }
            $user = Ebh::app()->user->getloginuser();
            $uid = $user['uid'];
            /*$itemids = array_column($ret['items'], 'itemid');
            $itemids = array_unique($itemids);*/
            $userpermission = $this->model('Userpermission')->getFolderPermission($uid, $folderids);
            //是否本校学生
            $is_schoolmate  = $this->model('Classroom')->checkstudent($uid, $crid, true);
            //设置服务项课程教师，课程权限
            $ret['acount'] = 0;
            $ret['buy_all'] = true;
            foreach ($ret['items'] as $k => $sitem) {
                $folderid = $sitem['fid'];
                $ret['items'][$k]['fprice'] = $sitem['iprice'];
                //全校免费课程并且是该网校学生,课程价格置0
                if (!empty($sitem['isschoolfree']) && $is_schoolmate) {
                    $ret['items'][$k]['fprice'] = $ret['items'][$k]['iprice'] = 0;
                }
                if (!empty($userpermission[$folderid])) {
                    $ret['items'][$k]['itemid'] = 0;
                    if ($userpermission[$folderid]['crid'] != $crid) {
                        //在别的网校报名过
                        $ret['items'][$k]['crid'] = $userpermission[$folderid]['crid'];
                    }
                } else {
                    $ret['acount'] += $sitem['iprice'];
                    $ret['buy_all'] = false;
                }
                if (empty($group[$folderid])) {
                    $ret['items'][$k]['tname'] = '';
                } else {
                    $usernames = array_unique($group[$folderid]);
                    $ret['items'][$k]['tname'] = implode(',', $usernames);
                }
            }
            unset($teachers, $group, $userpermission);
            //判断课程权限，不区分相同课程的不同服务项
            $ret['items'] = array_values($ret['items']);
        }
        if (empty($ret['showbysort'])) {
            if (empty($ret['items'])) {
                $ret['acount'] = 0;
                $ret['items'] = array();
            }
            $bundleModel = $this->model('Bundle');
            $bundles = $bundleModel->bundleList($crid, array(
                'sid' => $sid,
                'pid' => $pid
            ));
            if (!empty($bundles)) {
                $bids = array_keys($bundles);
                $courses = $bundleModel->courseList($bids);
                if (empty($courses)) {
                    echo json_encode($ret);
                    exit();
                }
                $folderids = array_unique(array_column($courses, 'folderid'));
                $userpermission = $this->model('Userpermission')->getFolderPermission($uid, $folderids);
                foreach ($courses as $course) {
                    if (!isset($bundles[$course['bid']]['viewnum'])) {
                        $bundles[$course['bid']]['viewnum'] = 0;
                        $bundles[$course['bid']]['coursewarenum'] = 0;
                        $bundles[$course['bid']]['hasPower'] = true;
                    }
                    $bundles[$course['bid']]['viewnum'] += $course['viewnum'];
                    $bundles[$course['bid']]['coursewarenum'] += $course['coursewarenum'];
                    if ($bundles[$course['bid']]['hasPower'] && empty($userpermission[$course['folderid']])) {
                        $bundles[$course['bid']]['hasPower'] = false;
                    } else {
                        $bundles[$course['bid']]['fid'] = $course['folderid'];
                    }
                }
                $bundleList = array_map(function($bundle) use($crid) {
                    $bid = empty($bundle['hasPower']) ? $bundle['bid'] : 0;
                    return array(
                        'bid' => $bid,
                        'fid' => $bundle['fid'],
                        'itemid' => $bid,
                        'crid' => $crid,
                        'iprice' => $bundle['bprice'],
                        'iname' => $bundle['name'],
                        'name' => $bundle['name'],
                        'face' => $bundle['cover'],
                        'tname' => '',
                        'isschoolfree' => 0,
                        'speaker' => $bundle['speaker'],
                        'viewnum' => $bundle['viewnum'],
                        'coursewarenum' => $bundle['coursewarenum']
                    );
                }, $bundles);
                $ret['items'] = array_merge($ret['items'], $bundleList);
            }
        }

        echo json_encode($ret);
        exit();
    }

    /**
     * 检查学生在网校下有没开通的课程
     */
    public function check_userpermision() {
        $crid = intval($this->input->post('rid'));
        $user = Ebh::app()->user->getloginuser();
        $uid = $user['uid'];
        $roominfo = $this->getRoomInfo($crid);
        if($roominfo['isschool'] != 7) {
            $ret = $this->model('Classes')->checkUserpermission($uid, $crid);
        } else {
            $ret = $this->model('Userpermission')->checkUserFolderPermision($uid, $crid);
        }
        echo json_encode(array(
            'isschool' => intval($roominfo['isschool']),
            'has' => intval($ret)
        ));
        exit();
    }

    /**
     * 获取免费的服务项
     */
    public function get_freeitems() {
        $crid = intval($this->input->post('rid'));
        $itemid = intval($this->input->post('itemid'));
        $user = Ebh::app()->user->getloginuser();
        $uid = $user['uid'];
        $is_schoolmate  = $this->model('Classroom')->checkstudent($uid, $crid, true);
        $items = $this->model('Payitem')->getFreeItems($itemid, $crid, $is_schoolmate == 1);
        echo json_encode($items);
        exit();
    }

    /**
     * 验证用户是否购买课程包
     */
    public function checkkcb(){
        $crid = intval($this->input->post('rid'));//网校crid
        $sid = intval($this->input->post('sid'));//课程包 分类sid
        $user = Ebh::app()->user->getloginuser();
        $uid = $user['uid'];
        $ret = $this->model('Userpermission')->getkcbPermision($uid,$crid,$sid);
        echo json_encode(array('status'=>$ret));
        exit();
    }

}
