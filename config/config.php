<?php
$config = array(
    'title'=>'',
    'keywords'=>'',
    'description'=>'',
    'db'=>array(
        'dbtype' => 'mysql',
        'dbdriver' => 'mysqli',
        'tablepre' => 'ebh_',
        'pconnect' => false,
        'dbcharset' => 'utf8',
		'autoload' => true,
        'dbhost' => '192.168.0.24',
        'dbuser' => 'root',
        'dbport' => 3306,
        'dbpw' => '123456',
        'dbname' => 'ebh2',
        'slave' => array(
//            array(
//                'dbhost' => '192.168.0.28',
//                'dbuser' => 'root',
//                'dbport' => 3306,
//                'dbpw' => '12345699',
//                'dbname' => 'ebh2',
//            )
        )
    ),
	'snsdb'=>array(
			'dbtype' => 'mysql',
			'dbdriver' => 'mysqli',
			'tablepre' => 'ebh_',
			'pconnect' => false,
			'dbcharset' => 'utf8',
			'autoload' => true,
			'dbhost' => '192.168.0.24',
			//'dbhost' => '192.168.0.28',
			'dbuser' => 'root',
			'dbport' => 3306,
			'dbpw' => '123456',
			//'dbpw' => '12345699',
			'dbname' => 'sns2'),
    'auto_helper'=>array(
        'common'
    ),
    //路由设置
    'route'=>array(
        'url_mode'=>'QUERY_STRING', //路由模式
        'domain'=>'ebh.net',           //网站主域名
        'suffix'=>'',           //路径后缀
        'default'=>'default'        //默认控制器
    ),
    //cookie设置
    'cookie'=>array(
        'prefix'=>'ebh_',
        'domain'=>'.ebh.net',
        'path'=>'/'
    ),
    //log
    'log'=>array(
        'log_path'=>'',                 //日志路径，为空为网站log目录
        'enable'=>true,            //启用日志
        'loglevel'=>1                  //记录日志级别，大于此级别的日志不予记录
    ),
    'cache'=>array(
        'driver'=>'memcache',
        'servers'=>array(
//             array('host'=>'192.168.0.27','port'=>11200)
			array('host'=>'127.0.0.1','port'=>11200)
        )
    ),
    'cache_redis'=>array(
        'driver'=>'redis',
        'servers'=>array(
//            array('host'=>'192.168.0.27','port'=>6379)
			array('host'=>'192.168.0.200','port'=>6379)
        )
    ),
    //输出编码等设置
    'output'=>array('charset'=>'UTF-8'),
    //安全设置
    'security'=>array('authkey'=>'SFDSEFDSDF'),
    //设置WEB服务器软件类型
    'web'=>array('type'=>'nginx')
);
return $config;