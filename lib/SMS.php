<?php
/**
 *答疑短信提醒
 */
class SMS{
    public function run($qid,$tid,$type){
        $_SMS = EBH::app()->getConfig()->load('sms');
        $roominfo = Ebh::app()->room->getcurroom();
        if(!in_array($roominfo['crid'], $_SMS['crids'])){
            return;
        }
        if(empty($qid) || empty($tid) || empty($type)){
            log_message('短信发送:参数错误');
            return ;
        }
        if($type==1){//发送短信
            $this->_sendMessage($qid,$tid);
        }else{
            //取消发送
            $this->_cancelMessage($qid,$tid);
        }
    }
    /**
     *短信发送
     */
    private function _sendMessage($qid,$tid=0){
        if(empty($qid) || $tid==0){
            return;
        }
        $teacherModel = EBH::app()->model('teacher');
        $teacherInfo = $teacherModel->getteacherdetail($tid);
        if(empty($teacherInfo) || empty($teacherInfo['mobile'])){
            log_message('短信发送：'.$tid.' 手机不存在');
            return;
        }
        $param = array();
        $param['qid'] = $qid;
        $param['mobile'] = $teacherInfo['mobile'];
        $param['tname'] = empty($teacherInfo['realname'])?$teacherInfo['username']:$teacherInfo['realname'];
        $user = EBH::app()->user->getloginuser();
        $param['stuname'] = empty($user['realname'])?$user['username']:$user['realname'];
        $roominfo = EBH::app()->room->getcurroom();
        $param['crname'] = $roominfo['crname'];
        $classInfo =  EBH::app()->model('classes')->getClassByUid($roominfo['crid'],$user['uid']);
        $param['classname'] = $classInfo['classname'];
        $param['type'] = 1;//要求发送短信
        $_SMS = EBH::app()->getConfig()->load('sms');
        //调用短信发送接口
        $this->_curl_post($_SMS['server'],$param);
    }
    /**
     *短信发送取消
     */
    private function _cancelMessage($qid,$tid=0){
        $param = array(
            'qid'=>$qid,
            'type'=>2,
        );
        $_SMS = EBH::app()->getConfig()->load('sms');
        //调用短信发送接口
        $this->_curl_post($_SMS['server'],$param);
    }
    private function _curl_post($url,$data){
        log_message('短信发送记录：'.serialize($data));
		log_message(print_r($data,true));
        $post_data = $data;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
		return $output;
    }
	/**
	*发送手机短信通用方法
	*@param string $mobile 手机号码
	*@param string $msg 短信内容
	*@param string $sign 短信签名
	*@param string $ext 扩展号
	*/
	public function send($mobile,$msg,$sign='【e板会】',$ext='') {
		if(empty($mobile))
			return FALSE;
		$result = $this->_sendByMT($mobile,$msg,$sign);
		log_message('result:'.$result);
	}

	private function _sendByMT($mobile,$msg,$sign) {
		if(substr($mobile,0,3) == '+86') {
			$mobile = substr($mobile,0,1);
		} else if(substr($mobile,0,2) != '86') {
			$mobile = '86'.$mobile;
		}
		$url = 'http://210.51.190.233:8085/mt/MT.ashx';
		$src = 'svnlan';
		$pwd = 'ebanhui2015';
		$serviceID = 'SEND';
		$codec = '';
		$data = array('src'=>$src,'pwd'=>$pwd,'serviceID'=>$serviceID,'dest'=>$mobile,'msg'=>$sign.$msg);
		$result = $this->_curl_post($url,$data);
		return $result;
	}

    /**
    *发送手机短信通用方法
    *@param string $mobile 手机号码
    *@param string $msg 短信内容
    *@param string $sign 短信签名
    *@param string $ext 扩展号
    */
    public function send_fix($mobile,$msg,$sign='【e板会】',$ext='') {
        if(empty($mobile))
            return FALSE;
        $result = $this->_sendByMT2($mobile,$msg,$sign);
        log_message('result:'.$result);
    }

    private function _sendByMT2($mobile,$msg,$sign) {
        $url = 'http://api.106msg.com/TXTJK.aspx?type=send';
        // $url = 'http://www.ebh.net/test.html';
        $ua = '850596731';
        $pwd = '850596731';
        $gwid = '43';
        $msg = $sign.$msg;
        $msg = mb_convert_encoding($msg, "GBK", "UTF-8");
        $data = array('ua'=>$ua,'pwd'=>$pwd,'gwid'=>$gwid,'mobile'=>$mobile,'msg'=>$msg);
        $result = $this->_curl_post($url,$data);
        return $result;
    }


    // --------------------------阿里大鱼开始-----------------------------
    public function send_dayu($mobile,$code,$sign = "e板会"){
        if(empty($mobile))
            return FALSE;
        $result = $this->_sendByMT3($mobile,$code,$sign);
        if( !empty($result) && !empty($result->result) && ($result->result->err_code !== '0') ){
            //发送失败写日志
            log_message('result:'.var_export($result,true));
        }
    }

    public function _sendByMT3($mobile,$code,$sign){
        include_once "aliyun_dayu/TopSdk.php";
        date_default_timezone_set('Asia/Shanghai'); 
        $c = new TopClient;
        $c->format = 'json';
        $c->appkey = '23319024';
        $c->secretKey = 'abaaf4ec86ecd9e8d174ac866aa9c48e';
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType("normal");
        $req->setSmsFreeSignName($sign);
        $req->setSmsParam("{\"code\":\"".$code."\"}");
        $req->setRecNum($mobile);
        $req->setSmsTemplateCode("SMS_5425150");
        return $c->execute($req);
    }
    // --------------------------阿里大鱼结束-----------------------------
}