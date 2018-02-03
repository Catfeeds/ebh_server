<?php
/**
 * 网校名师团队
 * Created by PhpStorm.
 * User: ycq
 * Date: 2017/6/19
 * Time: 16:37
 */
class MasterController extends CControl
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 名师团队列表服务接口
     */
    public function index() {
        $model = $this->model('Master');
        $crid = intval($this->input->post('rid'));
        $pagesize = $this->input->post('pagesize');
        $page = $this->input->post('page');
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
        $masters = $model->getList($crid, $limit);
        echo json_encode($masters);
    }

    /**
     * 名师详情服务接口
     */
    public function detail() {
        $model = $this->model('Master');
        $crid = intval($this->input->post('rid'));
        $tid = intval($this->input->post('tid'));
        $pagesize = $this->input->post('pagesize');
        $page = $this->input->post('page');
        //只返回课程列表，用于ajax分页加载数据
        $getlist = intval($this->input->post('getlist'));
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
        $master = '';
        if (empty($getlist)) {
            $master = $model->detail($tid, $crid);
            if (empty($master)) {
                exit();
            }
        }

        $roominfo = Ebh::app()->room->getcurroom();
        if (empty($roominfo)) {
            echo json_encode(array());
            exit();
        }
        $courselist = $this->model('Teacher')->getCoursesForTeacher($tid, $roominfo, $limit, false);
        echo json_encode(array('master' => $master, 'courselist' => $courselist));
    }
}