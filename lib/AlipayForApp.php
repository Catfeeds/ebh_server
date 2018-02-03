<?php
/**
 * 支付宝支付类库APP专用
 */
class AlipayForApp {
	public function alipayTo($param) {
		require_once("alipayForApp/alipay.config.php");
		require_once("alipayForApp/lib/alipay_rsa.function.php");
        //服务器异步通知页面路径
        $notify_url = $param['notify_url'];
        //需http://格式的完整路径，不能加?id=123这类自定义参数
        //页面跳转同步通知页面路径
        $return_url = $param['return_url'];
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
        //必填
        //商户订单号
        $out_trade_no = $param['trade_no'];
        //商户网站订单系统中唯一订单号，必填
        //订单名称
        $subject = $param['subject'];
        //必填
        //付款金额
        $total_fee = $param['total_fee'];
        //必填
        //订单描述
        $body = $param['body'];
        //商品展示地址
        $show_url = $param['show_url'];
        //需以http://开头的完整路径，例如：http://www.xxx.com/myorder.html
        //防钓鱼时间戳
        $anti_phishing_key = "";
        //若要使用请调用类文件submit中的query_timestamp函数
        //客户端的IP地址
        $exter_invoke_ip = "";
        //非局域网的外网IP地址，如：221.0.0.1
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"partner" => trim($alipay_config['partner']),
				"notify_url"	=> $notify_url,
				"return_url"	=> $return_url,
				"seller_email"	=> $alipay_config['seller_email'],
				"out_trade_no"	=> $out_trade_no,
				"subject"	=> $subject,
				"total_fee"	=> $total_fee,
				"body"	=> $body,
				"show_url"	=> $show_url,
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset'])),
		);
		$private_key_path = $alipay_config['private_key_path'];
		$data = $this->_getOrderInfo($parameter);
		$sign = rsaSign($data,$private_key_path);
		$result = array();
		if($sign){
			$result['status'] = 0;
			$result['sign'] = $sign;
			$result['orderInfo'] = $data;
		}else{
			$result['sign'] = false;
			$result['status'] = 1;
			$result['orderInfo'] = "";
		}
		echo json_encode($result);
	}

	private function _getOrderInfo($param = array()){
		$subject = $param['subject'];
		$body = $param['body'];
		$price = $param['total_fee'];
		// 签约合作者身份ID
		$orderInfo = "partner=" . "\"" . $param['partner'] . "\"";

		// 签约卖家支付宝账号
		$orderInfo .= "&seller_id=" . "\"" . $param['seller_email'] . "\"";

		// 商户网站唯一订单号
		$orderInfo .= "&out_trade_no=" . "\"" . $param['out_trade_no'] . "\"";

		// 商品名称
		$orderInfo .= "&subject=" . "\"" . $subject . "\"";

		// 商品详情
		$orderInfo .= "&body=" . "\"" . $body . "\"";

		// 商品金额
		$orderInfo .= "&total_fee=" . "\"" . $price . "\"";

		// 服务器异步通知页面路径
		$orderInfo .= "&notify_url=" . "\"" . "http://www.ebh.net/ibuy/aliappnotify.html"
				. "\"";

		// 服务接口名称， 固定值
		$orderInfo .= "&service=\"mobile.securitypay.pay\"";

		// 支付类型， 固定值
		$orderInfo .= "&payment_type=\"1\"";

		// 参数编码， 固定值
		$orderInfo .= "&_input_charset=\"utf-8\"";

		// 设置未付款交易的超时时间
		// 默认30分钟，一旦超时，该笔交易就会自动被关闭。
		// 取值范围：1m～15d。
		// m-分钟，h-小时，d-天，1c-当天（无论交易何时创建，都在0点关闭）。
		// 该参数数值不接受小数点，如1.5h，可转换为90m。
		$orderInfo .= "&it_b_pay=\"30m\"";

		// extern_token为经过快登授权获取到的alipay_open_id,带上此参数用户将使用授权的账户进行支付
		// orderInfo .= "&extern_token=" . "\"" + extern_token + "\"";

		// 支付宝处理完请求后，当前页面跳转到商户指定页面的路径，可空
		$orderInfo .= "&return_url=\"http://www.ebh.net/ibuy/aliwapreturn.html\"";

		// 调用银行卡支付，需配置此参数，参与签名， 固定值 （需要签约《无线银行卡快捷支付》才能使用）
		// orderInfo .= "&paymethod=\"expressGateway\"";

		return $orderInfo;
	}
}