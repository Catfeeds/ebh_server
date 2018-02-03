<?php
/**
 *微信扫码支付lib
 */
require_once "WxNativePay/lib/WxPay.Api.php";
require_once "WxNativePay/WxPay.NativePay.php";
require_once 'WxNativePay/lib/WxPay.Notify.php';

class WxNativePay extends WxPayNotify{
	private $context = NULL;
	public function alipayTo($param = array()){
		$res = array('url'=>'','sign'=>'','status'=>-1);
		//获取提交的商品名称
		$product_name = preg_replace("/\s/","", $param['body']);
		$attach = $param['attach'];
		//获取提交的订单号
		$out_trade_no = trim($param['out_trade_no']);
		$subject = trim($param['subject']);
		$notify_url = trim($param['notify_url']);
		//总费用
		$total_fee = $param['total_fee'];
		$notify = new NativePay();
		$input = new WxPayUnifiedOrder();
		$input->SetBody($product_name);
		$input->SetAttach($attach);
		$input->SetOut_trade_no($out_trade_no);
		$input->SetTotal_fee($total_fee);
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 600));
		$input->SetGoods_tag($subject);
		$input->SetNotify_url($notify_url);
		$input->SetTrade_type("NATIVE");
		$input->SetProduct_id($out_trade_no);

		$result = $notify->GetPayUrl($input);
		if(!empty($result) && ($result['result_code'] == 'SUCCESS') && ($result['return_code'] == 'SUCCESS') && ($result['return_msg'] == 'OK') ){
			$res['url'] = urlencode($result["code_url"]);
			$res['sign'] = $result['sign'];
			$res['status'] = 0;
		}
		return $res;
	}

	public function checknotify($context){
		$this->context = $context;
		$this->Handle(false);
	}

	//查询订单
	public function Queryorder($transaction_id){
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg){
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		if(empty($this->context)){
			return false;
		}
		return $this->context->_wxnativenotify($data);
	}
}