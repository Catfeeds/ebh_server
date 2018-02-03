<?php

/**
 * CUser用户组件类
 */
class CUser extends CComponent {

    private $user = NULL;
    
    public function getloginuser() {
        if (isset($this->user))
            return $this->user;
        $input = EBH::app()->getInput();
        $usermodel = $this->model('user');
        $auth = $input->post('k');
     	if(empty($auth)){
     		$auth = $input->cookie('auth');
     	}   
        if (!empty($auth)) {
     	    $loginArg = explode("\t", authcode($auth, 'DECODE'));
            $ip = '';
            $time = '';
            $password = '';
            $uid = '';
            foreach ($loginArg as $k => $v) {
     	        switch ($k) {
                    case 0:
                        $password = $v;
                        break;
                    case 1:
                        $uid = intval($v);
                        break;
                    case 2:
                        $ip = $v;
                        break;
                    case 3:
                        $time = $v;
                        break;
                }
            }

            $curip = $input->getip();
//            if($curip != $ip)
//                return FALSE;
            $uid = intval($uid);
            if ($uid <= 0) {
                return FALSE;
            }
            $user = $usermodel->getloginbyuid($uid,$password,TRUE);
            if(!empty($user)){
                $user['k'] = $auth;
                $this->user = $user;
                return $user;
            }else{
                return FALSE;
            }
        }
        return FALSE;
    }

}
