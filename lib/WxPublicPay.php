<?php
class WxPublicPay{
	public function alipayTo($param = array()){
		require_once("WxPayPubHelper/WxPayPubHelper.php");
		//使用jsapi接口
		$jsApi = new JsApi_pub();
		//=========步骤1：网页授权获取用户openid============
		$openid = $param['wxopid'];
		//获取提交的商品名称
		$product_name = preg_replace("/\s/","", $param['body']);
		//获取提交的订单号
		$out_trade_no= trim($param['out_trade_no']);
		//总费用
		$total_fee = intval($param['total_fee']);
		//=========步骤2：使用统一支付接口，获取prepay_id============
		//使用统一支付接口
		$unifiedOrder = new UnifiedOrder_pub();
		$unifiedOrder->setParameter("openid","$openid");//商品描述
		$unifiedOrder->setParameter("body",$product_name);//商品描述
		//自定义订单号，此处仅作举例

		$timeStamp = time();
		$unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号 
		$unifiedOrder->setParameter("total_fee",$total_fee);//总金额
		$unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址 
		$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
		$prepay_id = $unifiedOrder->getPrepayId();
		if(empty($prepay_id)){
			return -5;
		}
		//=========步骤3：使用jsapi调起支付============
		$jsApi->setPrepayId($prepay_id);

		$jsApiParameters = $jsApi->getParameters();
		return $jsApiParameters;
	}

	//返回获取微信code的url
	public function getWxCode($url = ""){
		require_once("WxPayPubHelper/WxPayPubHelper.php");
		// 使用jsapi接口
		if(empty($url)){
			$url = urlencode("http://www.ebh.net/wxbind/xbind.html");
		}
		$jsApi = new JsApi_pub();
		$codeUrl = $jsApi->createOauthUrlForCode($url);
		return $codeUrl;
	}
}