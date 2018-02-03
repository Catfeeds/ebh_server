<?php
/**
 * 网校模块
 * Created by PhpStorm.
 * User: app
 * Date: 2016/8/29
 * Time: 16:42
*/
class AppmoduleController extends CControl
{
    public function __construct()
    {
        parent::__construct();
        $user = Ebh::app()->user->getloginuser();
        if (empty($user)) {    //如果用户验证失败，则返回-1
            echo json_encode(array('status' => -1, 'msg' => '用户信息已过期'));
            exit();
        }
    }

    public function index()
    {
        $modules = $this->model('roommodules');
        $crid = intval($this->input->post('crid'));
        if ($crid < 1) {
            echo json_encode(array('status' => -2, 'msg' => '非法操作'));
            exit();
        }
        $data = $modules->getRoomModulesForStudent($crid);
        echo json_encode(array(
            'status' => 0,
            'data' => $data
        ));
        exit;
    }

    public function student(){
        $modules = $this->model('appmodule');
        $crid = intval($this->input->post('crid'));
        if ($crid < 1) {
            echo json_encode(array('status' => -2, 'msg' => '非法操作'));
            exit();
        }
        $data = $modules->getstudentmodule(array('crid'=>$crid,'available'=>1,'order'=>'displayorder,moduleid','limit'=>100,'tors'=>'0,2','showmode'=>0));
        echo json_encode(array(
            'status' => 0,
            'data' => $data
        ));
        exit;
    }

    public function info(){
        $modules = $this->model('appmodule');
        $crid = intval($this->input->post('crid'));
        $tors = intval($this->input->post('tors'));
        $type = intval($this->input->post('type'));
        if ($crid < 1) {
            echo json_encode(array('status' => -2, 'msg' => '非法操作'));
            exit();
        }
        $code = $this->input->post('code');


        $data = $modules->getmodulenamebycode(array('modulecode'=>$code,'crid'=>$crid,'tors'=>$tors,'type'=>$type));
        echo json_encode(array(
            'status' => 0,
            'data' => $data
        ));
        exit;
    }
}