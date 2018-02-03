<?php
/**
 * 优惠券模型类
 */
class CouponsModel extends CModel{
    //获取优惠券详情
	public function getOne($param = array()) {
        $sql = 'SELECT * FROM `ebh_coupons` c';
        $wherearr = array();
        if(empty($param['uid']) && empty($param['code'])){
        	return array();
        }
        if(!empty($param['uid'])){
        	$wherearr[] = ' c.uid = '.$param['uid'];  	
        }
        if(!empty($param['code'])){
        	$wherearr[] = ' c.code = '.$this->db->escape($param['code']);
        }
        if(!empty($wherearr)) {
            $sql .= ' WHERE '.implode(' AND ',$wherearr);
        }
        return $this->db->query($sql)->row_array();
    }
    //新增一条优惠券
    public function add($param = array()){
    	$setarr = array();
    	if(!empty($param['uid'])){
    		$setarr['uid'] = $param['uid'];
    	}
    	if(!empty($param['code'])){
    		$setarr['code'] = $param['code'];
    	}
    	if(!empty($param['orderid'])){
    		$setarr['orderid'] = $param['orderid'];	
    	}
    	if(!empty($param['crid'])){
    		$setarr['crid'] = $param['crid'];
    	}
    	if(isset($param['fromtype'])){
    		$setarr['fromtype'] = $param['fromtype'];
    	}
    	if(!empty($param['createtime'])){
    		$setarr['createtime'] = $param['createtime'];
    	}
    	return $this->db->insert('ebh_coupons',$setarr);
    }
    //检测优惠券是否存在
    public function checkcoupon($coupon){
    	$sql = "SELECT count(*) count FROM `ebh_coupons` WHERE code = '{$coupon}'";
    	$row = $this->db->query($sql)->row_array();	
		if(!empty($row) && $row['count']>0){
			return true;
		}else{
			return false;
		}
    }
    
}
