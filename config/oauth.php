<?php
/**
 *开放平台登录接口相关配置
 *
 *@author eker
 */
$oauth = array();

//微信扫码登录接口相关配置
$oauth['weixin'] = array(
	'AppID'=>'wxf115f176a047b8cf',
	'AppSecret'	=>'0de7b04e7c3ac9d8331619b900b11080',
	'redirect_uri'=>'http://www.ebh.net/otherlogin/wx_callback.html'	
);

//微信公众号配置
$oauth['wxgzh'] = array(
	'appID'=>'wx975d8f85a286b019',
	'appsecret'=>'0651a801cad257c653bc8c1d177d1f03'
);

//QQ互联接口配置
$oauth['qq'] = array(
	'appid'=>'100298841',
	'appkey'	=>'80cd3e2224eb49b321ac1002559fd39d',
	'redirect_uri'=>'http://www.ebh.net/otherlogin/qq_callback.html'
);

//新浪微博开发平台接口配置
$oauth['sina'] = array(
	'appkey'=>'2109032836',
	'appsecret'	=>'45079729969b3015649a1b0cef2eddc5',
	'redirect_uri'=>'http://www.ebh.net/otherlogin/sina_callback.html'
);

return $oauth;
?>
