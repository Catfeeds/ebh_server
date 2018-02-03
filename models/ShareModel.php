<?php
/*
*分销比例记录
*/
class ShareModel extends CModel{

	/**
	 *获取用户分销比例
	 */
	public function getUserSharePre($uid=0,$crid=0) {
		$uid = intval($uid);
		$crid = intval($crid);
		if (empty($uid) || empty($crid)) {
			return false;
		}
		$shareSql = 'select percent from ebh_usershares where status=0 and uid='.$uid.' and crid='.$crid;
		$userShare = $this->db->query($shareSql)->row_array();
		return $userShare;
	}

	/**
     * 分销后插入，金钱记录表
     * 
     */
    public function addRecorder($param=array()){
        if(empty($param)){
            return false;
        }
        $data = array();
        if(!empty($param['shareuid'])){
            $data['uid'] = $param['shareuid'];
        }
        $data['cate'] = 1;
        $data['dateline'] = time();
        $data['status'] = 1;
        return $this->db->insert('ebh_records',$data);
    }

    /**
    *生成充值记录，退钱就是充值
    */
    public function addCharge($param = array()) {
        if(empty($param))
            return false;
        $data = array();
        $this->db->begin_trans();
        $param['rid'] = $this->addRecorder($param);
        if(!empty($param['rid'])){
            $data['rid'] = $param['rid'];
        }
        $data['uid'] = 0;
        if(!empty($param['shareuid'])){
            $data['useuid'] = $param['shareuid'];
        }
        //订单号，用于退款把金额解冻
        if(!empty($param['orderid'])){
            $data['ordernumber'] = $param['orderid'];
        }
        if(!empty($param['payfrom'])){
            $data['buyer_info'] = $param['payfrom'];
        }
        if(!empty($param['sharedetail'])){
            $data['cardno'] = $param['sharedetail'];
        } else {
            $data['cardno'] = '分销返利';
        }
        if(!isset($param['isfreeze'])){
            $data['isfreeze'] = 1;//默认都是冻结状态
        }
        if(!empty($param['crid'])){
            $data['buyer_id'] = $param['crid'];//记录在哪个网校购买的方便统计该网校的分销金额解冻时间
        }
        $data['type'] = 12;//分销获利，暂时冻结
        if(!empty($param['sharefee'])){
        	$sharefee = $param['sharefee'];
            //充钱
            $sqltouser = "update ebh_users set freezebalance = freezebalance + ".$sharefee." where uid =".intval($param['shareuid']);
            $this->db->query($sqltouser);
            $data['value'] = $param['sharefee'];
        }
        //余额
        $sqltouser = "select balance from ebh_users where uid =".intval($param['shareuid']);
        $res = $this->db->query($sqltouser)->row_array();
        if(!empty($res['balance'])){
            $data['curvalue'] = $res['balance'];
            if ($param['uid'] == $param['shareuid']) {//自己的分销记录当前的钱
                $data['curvalue'] = $data['curvalue'] - $param['totalfee'];
            }
        }
        $data['status'] = 1;
        if(!empty($param['payip'])){
            $data['fromip'] = $param['payip'];
        }
        $data['paytime'] = time();
        $data['dateline'] = time();
        $this->db->insert('ebh_charges',$data);
        if($this->db->trans_status() === FALSE){
            $this->db->rollback_trans();
            return false;
        }else{
            $this->db->commit_trans();
            return true;
        } 
    }
}
?>