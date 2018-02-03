<?php
/**
 *微信工具类，主要用来获取access_token,jsapi_ticket等信息,以及其打包信息等
 */
class WxUtils{
	public function __construct(){
		$config = Ebh::app()->getConfig()->load('weixin');
		$this->_appID 	= $config['appID'];
		$this->_appsecret 	= $config['appsecret'];

	}
	public function getAccessToken($fresh = false){
		if(is_numeric($this->getAccess_token($fresh))){
			return $this->getAccess_token(true);
		}else{
			return $this->_access_token;
		}
	}

	public function getJSApiTicket($fresh = false){
		return $this->get_jsapi_ticket($fresh);
	}

	/**
	 * 	作用：产生随机字符串，不长于32位
	 */
	public function createNoncestr( $length = 32 ) 
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {  
			$str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
		}  
		return $str;
	}

	/**
	 * 	作用：格式化参数，签名过程需要使用
	 */
	function formatBizQueryParaMap($paraMap, $urlencode)
	{
		$buff = "";
		ksort($paraMap);
		foreach ($paraMap as $k => $v)
		{
		    if($urlencode)
		    {
			   $v = urlencode($v);
			}
			$buff .= $k . "=" . $v . "&";
		}
		$reqPar;
		if (strlen($buff) > 0) 
		{
			$reqPar = substr($buff, 0, strlen($buff)-1);
		}
		return $reqPar;
	}

	/*
	*获取微信接口全局 access_token
	*@param bool $fresh 是否直接从微信服务端重新取值
	*/
	private function getAccess_token($fresh = FALSE){
		$cache = Ebh::app()->getCache();
		if(!$fresh) {	//默认不刷新，则从缓存中取

			if(isset($this->_access_token))	
				return $this->_access_token;
			//从缓存中取
			$access_token = $cache->get('access_token');
			if(!empty($access_token)) {
				$this->_access_token = $access_token;
				return $access_token;
			}
		}
		// log_message('从微信服务接口中取');
		//从微信服务接口中取
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->_appID."&secret=".$this->_appsecret;
		$tokenjson = $this->getHttpResponseGET($url);
		$tokenarr = json_decode($tokenjson);
		if(!empty($tokenarr->errcode)){
			//获取jsApiTicket失败
			log_message('wx_token 获取失败！');
			return $tokenarr->errcode;
		}
		$expire_time = $tokenarr->expires_in;
		$expire_time = intval($expire_time) - 1000;
		if($expire_time <= 0)
			$expire_time = 1000;
		$access_token = $tokenarr->access_token;
		$cache->set('access_token',$access_token,$expire_time);
		$this->_access_token = $access_token;
		return $access_token;
	}

	public function getJSApiSignPackage($fresh = FALSE){
		$data_package = array();
		$noncestr = $this->createNoncestr(32);
		$jsapi_ticket = $this->get_jsapi_ticket($fresh);
		$timestamp = time();
		$url = 'http://wap.ebh.net/myroom/ask/doask.html';
		$param = array(
			'noncestr'=>$noncestr,
			'jsapi_ticket'=>$jsapi_ticket,
			'timestamp'=>$timestamp,
			'url'=>$url
		);
		$string = $this->formatBizQueryParaMap($param,false);
		$signature = sha1($string);
		unset($param['jsapi_ticket']);
		$param['signature'] = $signature;
		$param['appid'] = $this->_appID;
		$data_package = $param;
		return $data_package;
	}

	/*
	*获取微信接口全局 jsapi_ticket
	*@param bool $fresh 是否直接从微信服务端重新取值
	*/
	private function get_jsapi_ticket($freshticket = FALSE,$freshtoken = FALSE){
		$cache = Ebh::app()->getCache();
		if(!$freshticket) {	//默认不刷新，则从缓存中取
			if(isset($this->_ticket))	
				return $this->_ticket;
			//从缓存中取
			$ticket = $cache->get('jsapi_ticket');
			if(!empty($ticket)) {
				$this->_ticket = $ticket;
				return $ticket;
			}
		}
		//从微信服务接口中取
		$url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$this->getAccess_token($freshtoken).'&type=jsapi';
		$ticketjson = $this->getHttpResponseGET($url);
		$ticketarr = json_decode($ticketjson);
		if(!empty($ticketarr->errcode)){
			if($ticketarr->errcode == '42001'){
				//tooken失效 重新获取token
				log_message('jsApiTicket 重新获取！');
				return $this->get_jsapi_ticket($freshticket,true);
			}else{
				//获取jsApiTicket失败
				log_message('jsApiTicket 获取失败！');
				return "";
			}
		}
		$expire_time = $ticketarr->expires_in;
		$expire_time = intval($expire_time) - 1000;
		if($expire_time <= 0)
			$expire_time = 1000;
		$ticket = $ticketarr->ticket;
		$cache->set('jsapi_ticket',$ticket,$expire_time);
		$this->_ticket = $ticket;
		return $ticket;
	}

	function getHttpResponseGET($url)
	{
		if (ini_get("allow_url_fopen") == "1")
			return file_get_contents($url);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result =  curl_exec($ch);
		curl_close($ch);

		return $result;
	}
}