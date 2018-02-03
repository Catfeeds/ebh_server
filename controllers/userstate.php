<?php

/**
 * 用户信息控制器类 UserstateController
 * 主要处理用户最新的待批作业数，答疑数，评论数等
 */
class UserstateController extends CControl {
    public function __construct() {
        parent::__construct();
        $user = Ebh::app()->user->getloginuser();
        if(empty($user)) {  //如果用户验证失败，则返回-1
            echo array('status'=>-1,'msg'=>'用户信息已过期');
            exit();
        }
    }
    /*
     * 获取根据type和时间对应的用户需处理的记录数
     */
    public function index() {
        $crid = $this->input->post('rid');
        $type = $this->input->post('type'); //需要查看的类型
        if ($type !== NULL) {
            $typecounts = array();
            if (is_numeric($type)) {
                $typecounts[$type] = $this->_gettypecount($type,$crid);
            } else if (is_array($type)) {
                foreach ($type as $typeid) {
                    if (is_numeric($typeid)) {
                        $typecounts[$typeid] = $this->_gettypecount($typeid,$crid);
                    }
                }
            }
            echo json_encode($typecounts);
        }
    }
    /**
     * 根据分类获取该分类和用户状态时间下的记录数
     * @param type $type
     * @return int 记录数
     */
    private function _gettypecount($type,$crid) {
        $count = 0;
        $user = Ebh::app()->user->getloginuser();
        $statemodel = $this->model('Userstate');
        $subtime = $statemodel->getsubtime($crid,$user['uid'],$type);
        if($type == 5){ //通知
            $noticemodel = $this->model('Notice');
            if(empty($subtime)){
                $subtime = strtotime('1970');
            }
            $param = array(
                'crid'=>$crid,
                'subtime'=>$subtime
            );
            $count = $noticemodel->getnewnoticecountbytime($param);
            $statemodel->insert($crid,$user['uid'],$type,time());
        }
        return $count;
    }

}
