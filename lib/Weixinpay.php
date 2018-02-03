<?php
class Weixinpay{
	public function __construct(){
		$this->initReqHandler();
	}
	public function doOrder($param = array()){
		
		require_once ("Wxpay/ResponseHandler.class.php");
		require ("Wxpay/client/TenpayHttpClient.class.php");
		//获取提交的商品名称
		$product_name = preg_replace("/\s/","", $param['body']);
		//获取提交的订单号
		$out_trade_no= trim($param['out_trade_no']);
		//总费用
		$total_fee = intval($param['total_fee']);

		$outparams =array();

		$this->reqHandler->Token = $Token= $this->getAccessToken().'';
		if ( $Token !='' ){
			//=========================
			//生成预支付单
			//=========================
			//设置packet支付参数
			$WxPayConfig = Ebh::app()->getConfig()->load('wxpay');
			$packageParams =array();		
			
			$packageParams['bank_type']		= 'WX';	            //支付类型
			$packageParams['body']			= $product_name;					//商品描述
			$packageParams['fee_type']		= '1';				//银行币种
			$packageParams['input_charset']	= 'UTF-8';		    //字符集
			$packageParams['notify_url']	= $WxPayConfig['notify_url'];	    //通知地址
			$packageParams['out_trade_no']	= $out_trade_no;		        //商户订单号
			$packageParams['partner']		= $WxPayConfig['PARTNER'];		        //设置商户号
			$packageParams['total_fee']		= $total_fee;			//商品总金额,以分为单位
			$packageParams['spbill_create_ip']= EBH::app()->getInput()->getip();  //支付机器IP
			//获取package包
			$package= $this->reqHandler->genPackage($packageParams);
			$time_stamp = time();
			$nonce_str = md5(rand());
			
			//设置支付参数
			$signParams =array();
			$signParams['appid']	= $WxPayConfig['APP_ID'];
			$signParams['appkey']	= $WxPayConfig['APP_KEY'];
			$signParams['noncestr']	=$nonce_str;
			$signParams['package']	=$package;
			$signParams['timestamp']=$time_stamp;
			$signParams['traceid']	= $out_trade_no;
			//生成支付签名
			$sign = $this->reqHandler->createSHA1Sign($signParams);
			//增加非参与签名的额外参数
			$signParams['sign_method']		='sha1';
			$signParams['app_signature']	=$sign;
			//剔除appkey
			unset($signParams['appkey']); 
			//获取prepayid
			$prepayid=$this->reqHandler->sendPrepay($signParams);

			if ($prepayid != null) {
				$pack	= 'Sign=WXPay';
				//输出参数列表
				$prePayParams =array();
				$prePayParams['appid']		= $WxPayConfig['APP_ID'];
				$prePayParams['appkey']		= $WxPayConfig['APP_KEY'];
				$prePayParams['noncestr']	=$nonce_str;
				$prePayParams['package']	=$pack;
				$prePayParams['partnerid']	= $WxPayConfig['PARTNER'];
				$prePayParams['prepayid']	=$prepayid;
				$prePayParams['timestamp']	=$time_stamp;
				//生成签名
				$sign=$this->reqHandler->createSHA1Sign($prePayParams);

				$outparams['retcode']=0;
				$outparams['retmsg']='ok';
				$outparams['appid']= $WxPayConfig['APP_ID'];
				$outparams['partnerid']= $WxPayConfig['PARTNER'];
				$outparams['noncestr']=$nonce_str;
				$outparams['package']=$pack;
				$outparams['prepayid']=$prepayid;
				$outparams['timestamp']=$time_stamp;
				$outparams['sign']=$sign;

			}else{
				$outparams['retcode']=-2;
				$outparams['retmsg']='错误：获取prepayId失败';
			}
		}else{
			$outparams['retcode']=-1;
			$outparams['retmsg']='错误：获取不到Token';
		}

		return $outparams;
	}

    /**
     * @describe:h5微信支付
     * @User:tzq
     * @param array $param
     * @return array
     */
	public function doH5Pay($param = array()){

        require_once 'WxpayAPI_php_v3/lib/WxPay.Api.php';
        $input = new WxPayUnifiedOrder();
        $input->SetBody($param['body']);
        $input->SetAttach(isset($param['attach'])?$param['attach']:'附加字段');
        $input->SetOut_trade_no($param['out_trade_no']);
        $input->SetTotal_fee($param['total_fee']);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetSpbill_create_ip($param['spbill_create_ip']);
//$input->SetGoods_tag("test");
        $wxconfig =Ebh::app()->getConfig()->load('wxpay');
        $input->SetNotify_url($wxconfig['notify_url']);
        $input->SetTrade_type("MWEB");
        $input->SetSceneInfo(json_encode($param['scene_info'],JSON_UNESCAPED_UNICODE));
//$input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);

        return $order;

	}

	/**
	 *获取accessToken
	 */
	private function getAccessToken($fresh = false){
		$cache = Ebh::app()->getCache();
		if(!$fresh) {	//默认不刷新，则从缓存中取
			if(isset($this->_access_token)){
				return $this->_access_token;
			}	
			//从缓存中取
			$access_token = $cache->get('weixinpay_access_token');
			if(!empty($access_token)) {
				$this->_access_token = $access_token;
				return $access_token;
			}
		}
		
		//获取token值
		$reqHandler = $this->reqHandler;
		$url= $reqHandler->tokenUrl . '?grant_type=client_credential&appid='.$reqHandler->app_id .'&secret='.$reqHandler->app_secret;
		$json = $reqHandler->httpSend($url,'GET','');
		if( $json != ""){
			$tk = json_decode($json);
			if( $tk->access_token != "" )
			{
				$this->_access_token =$tk->access_token;
				$expire_time = $tk->expires_in;
				$expire_time = intval($expire_time) - 1000;
				if($expire_time <= 0){
					$expire_time = 1000;
				}
				$cache->set('weixinpay_access_token',$this->_access_token,$expire_time);
			}else{
				$this->_access_token = '';
			}
		}
		
		return $this->_access_token;
	}

	private function initReqHandler(){
		require_once ("Wxpay/RequestHandler.class.php");
		$reqHandler = new RequestHandler();
		$WxPayConfig = Ebh::app()->getConfig()->load('wxpay');
		$reqHandler->init($WxPayConfig['APP_ID'], $WxPayConfig['APP_SECRET'], $WxPayConfig['PARTNER_KEY'], $WxPayConfig['APP_KEY']);
		$this->reqHandler = $reqHandler;
		return $reqHandler;
	}
}