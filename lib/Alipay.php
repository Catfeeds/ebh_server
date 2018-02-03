<?php
/**
 * 支付宝支付类库
 */
class Alipay {
	public function alipayTo($param) {
		log_message('alipay:1----------------------------------------------');
		log_message('alipay:1'.ob_get_flush());
		require_once("alipay/alipay.config.php");
		log_message('alipay:2----------------------------------------------');
		log_message('alipay:2'.ob_get_flush());
		require_once("alipay/lib/alipay_submit.class.php");
		//支付类型
        $payment_type = "1";
        //必填，不能修改
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
        log_message('alipay:3----------------------------------------------');
        log_message('alipay:3'.ob_get_flush());
        //非局域网的外网IP地址，如：221.0.0.1
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service" => "create_direct_pay_by_user",
				"partner" => trim($alipay_config['partner']),
				"payment_type"	=> $payment_type,
				"notify_url"	=> $notify_url,
				"return_url"	=> $return_url,
				"seller_email"	=> $alipay_config['seller_email'],
				"out_trade_no"	=> $out_trade_no,
				"subject"	=> $subject,
				"total_fee"	=> $total_fee,
				"body"	=> $body,
				"show_url"	=> $show_url,
				"anti_phishing_key"	=> $anti_phishing_key,
				"exter_invoke_ip"	=> $exter_invoke_ip,
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
		log_message('alipay:4----------------------------------------------');
		log_message('alipay:4'.ob_get_flush());
		//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);
		log_message('alipay:5----------------------------------------------');
		log_message('alipay:5'.ob_get_flush());
		if(empty($param['client'])){ //来自app请求
			log_message('alipay:6----------------------------------------------');
			log_message('alipay:6'.ob_get_flush());
			$res = $alipaySubmit->buildRequestPara($parameter);
			log_message('alipay:7----------------------------------------------');
			log_message('alipay:7'.ob_get_flush());
			log_message('alipay:'.var_export($res,true));
			log_message('alipay:8----------------------------------------------');
			log_message('alipay:8'.ob_get_flush());
			echo json_encode($res);exit;
		}else{//来自一般网页
			log_message('alipay:9----------------------------------------------');
			log_message('alipay:9'.ob_get_flush());
			$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
			$html_text = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.
						'<html>'.
						'<head>'.
						'<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'.
						'<title>支付宝即时到账交易接口</title>'.
						'</head>'.
						$html_text.
						'</body>'.
						'</html>';
			$ret = array(
				'_html'=>$html_text
			);
			echo json_encode($ret);
		}
	}
	/**
	* 验证支付notify结果并返回验证结果
	*/
	public function checknotify() {
		require_once("alipay/alipay.config.php");
		require_once("alipay/lib/alipay_notify.class.php");
		//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();
		return $verify_result;
	}
	/**
	*验证返回数据是否有效
	*/
	public function checkreturn() {
		require_once("alipay/alipay.config.php");
		require_once("alipay/lib/alipay_notify.class.php");
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyReturn();
		return $verify_result;
	}
	/**
	*根据notify结果 处理notify输出notify页面
	* 如果输出fail 则支付宝会以一定策略重发
	*/
	public function notify($verify_result) {
		if($verify_result)
			echo "success";		//请不要修改或删除
		else
			echo "fail";
	}
}