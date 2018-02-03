<?php
/**
 * Created by PhpStorm.
 * User: Yun
 * Date: 2017/6/13
 * Time: 17:49
 * 记录用户注册信息
 */
class RegisterLog{
    //记录一个用户注册日志
    public function addOneRegisterLog($logdata){
        if(empty($logdata['uid'])){
            return FALSE;
        }
        $llmodel = Ebh::app()->model('Loginlog');
        $client['uid'] = $logdata['uid'];
        if(!empty($logdata['crid'])){
            $client['crid'] = $logdata['crid'];
        }
        if(empty($logdata['dateline'])){
            $client['dateline'] = SYSTIME;
        }
        if(!empty($logdata['ip'])){
            $client['ip'] = $logdata['ip'];
        }
        if(!empty($logdata['ismobile'])){
            $client['ismobile'] = $logdata['ismobile'];
        }
        if(!empty($logdata['system'])){
            $client['system'] = $logdata['system'];
        }
        if(!empty($logdata['browser'])){
            $client['browser'] = $logdata['browser'];
        }
        if(!empty($logdata['broversion'])){
            $client['broversion'] = $logdata['broversion'];
        }
        if(!empty($logdata['screen'])){
            $client['screen'] = $logdata['screen'];
        }
        if(!empty($logdata['parentcode'])){
            $client['parentcode'] = $logdata['parentcode'];
        }
        if(!empty($logdata['citycode'])){
            $client['citycode'] = $logdata['citycode'];
        }
        if(!empty($logdata['isp'])){
            $client['isp'] = $logdata['isp'];
        }
        if(!empty($logdata['logtype'])){
            $client['logtype'] = $logdata['logtype'];
        }
        if(!empty($logdata['othertype'])){
            $client['othertype'] = $logdata['othertype'];
        }
        $llmodel->addOneRegisterLog($client);
    }
}