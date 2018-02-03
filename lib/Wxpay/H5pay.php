<?php

/**
 * @describe:H5微信支付
 * @User:tzq
 * Class H5pay
 */
class H5pay {


    /**
     * @describe:拼接xml
     * @User:tzq
     * @param $params
     * @return null|string
     */
    public function toXml($params){

       return "<xml>
　　　　　　<appid>{$params['appid']}</appid>
　　　　　　<body>{$params['body']}</body>
　　　　　　<mch_id>{$params['mch_id']}</mch_id>
　　　　　　<nonce_str>{$params['nonce_str']}</nonce_str>
　　　　　　<notify_url>{$params['notify_url']}</notify_url>
　　　　　　<out_trade_no>{$params['out_trade_no']}</out_trade_no>
　　　　　　<scene_info>{$params['scene_info']}</scene_info>
　　　　　　<spbill_create_ip>{$params['spbill_create_ip']}</spbill_create_ip>
　　　　　　<total_fee>{$params['total_fee']}</total_fee>
　　　　　　<trade_type>{$params['trade_type']}</trade_type>
　　　　　　<sign>{$params['sign']}</sign>
　　　　　　</xml>";


    }

    /**
     * @describe:curl_请求数据
     * @User:tzq
     * @param $url
     * @param $data
     * @return mixed
     */
    public  function http_post($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    /**
     * @describe:获取请求客户端Ip地址
     * @User:tzq
     * @return string
     */
    public function getClientIp()
    {
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches[0] : '';
    }



    /**
     * @describe:获取签名字符串
     * @User:tzq
     * @param $params
     * @param $key
     * @return string
     */
    public function getSign($params,$key){
        ksort($params);
        $str = '';
        foreach ($params as $k=>$v){
            $str .= $k.'='.$v.'&';
        }
        $str .= 'key='.$key;
        return strtoupper(MD5($str));

    }

    /**
     * @describe:将xml转成数组
     * @User:tzq
     * @param $dataxml
     * @return array
     */
    public function toArray($dataxml){
        $array = (array) simplexml_load_string($dataxml, 'SimpleXMLElement',LIBXML_NOCDATA);
        return $array;
    }
}