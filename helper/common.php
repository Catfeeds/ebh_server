<?php

/*
 * 通用方法
 */

function log_message($msg, $level = 'error', $php_error = false) {
    Ebh::app()->getLog()->log($msg, $level, $php_error);
}

/**
 * 返回系统调试信息
 * @param type 是否直接输出信息
 * @return string 返回调试信息字符串
 */
function debug_info($echo = TRUE) {
	if(!IS_DEBUG)
		return FALSE;
    $cost_time = microtime(TRUE) - EBH_BEGIN_TIME;
    $cost_memory = memory_get_usage(TRUE);
    $cost_memoryinfo = '';
    if ($cost_memory > 1048576) {
        $cost_memoryinfo = round($cost_memory / 1048576, 2) . ' Mbytes';
    } else if ($cost_memory > 1024) {
        $cost_memoryinfo = round($cost_memory / 1024, 2) . ' Kbytes';
    } else {
        $cost_memoryinfo = $cost_memory . ' bytes';
    }
    $query_nums = EBH::app()->getDb()->query_nums;
    $info = 'Processed in ' . $cost_time . ' second(s), ' . $query_nums . ' queries ,Memory Allocate is ' . $cost_memoryinfo;
    if ($echo)
        echo $info;
    else {
        return $info;
    }
}
/**
 * 判断是否手机浏览器
 * @return int
 */
function is_mobile(){
    $check = false;
    // returns true if one of the specified mobile browsers is detected
    // 如果监测到是指定的浏览器之一则返回true

    $regex_match="/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";

    $regex_match.="htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";

    $regex_match.="blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";

    $regex_match.="symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";

    $regex_match.="jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";

    $regex_match.=")/i";

    // preg_match()方法功能为匹配字符，既第二个参数所含字符是否包含第一个参数所含字符，包含则返回1既true
    if(!empty($_SERVER['HTTP_USER_AGENT'])){
        $check = preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
    }
    return $check;
}
function geturl($name, $echo = FALSE) {
    if (strpos($name, 'http://') !== FALSE || strpos($name, '.html') !== FALSE) {
        $url = $name;
    } else
        $url = '/' . $name . '.html';
    if ($echo)
        echo $url;
    return $url;
}

/**
 * 切割中文字符串， 中文占2个字节，字母占一个字节
 * @param $string 要切割的字符串
 * @param $start 起始位置
 * @param $length 切割长度
 */
function ssubstrch($string, $start = 0, $length = -1) {
    $p = 0;
    $co = 0;
    $c = '';
    $retstr = '';
    $startlen = 0;
    $len = strlen($string);
    $charset = Ebh::app()->output['charset'];
    for ($i = 0; $i < $len; $i ++) {
        if ($length <= 0) {
            break;
        }
        $c = ord($string {$i});
        if ($charset == 'UTF-8') {
            if ($c > 252) {
                $p = 5;
            } elseif ($c > 248) {
                $p = 4;
            } elseif ($c > 240) {
                $p = 3;
            } elseif ($c > 224) {
                $p = 2;
            } elseif ($c > 192) {
                $p = 1;
            } else {
                $p = 0;
            }
        } else {
            if ($c > 127) {
                $p = 1;
            } else {
                $p = 0;
            }
        }
        if ($startlen >= $start) {
            for ($j = 0; $j < $p + 1; $j ++) {
                $retstr .= $string [$i + $j];
            }
            $length -= ($p == 0 ? 1 : 2);
        }
        $i += $p;
        $startlen++;
    }
    return $retstr;
}

/**
 * 按照给定长度截取字符串
 * @param string $str源字符串
 * @param int $length 需要截取的长度
 * @param string $pre，字符串附加的字符，默认为...
 * @return string 返回截取后的字符串
 */
function shortstr($str, $length = 20, $pre = '...') {
    $resultstr = ssubstrch($str, 0, $length);
    return strlen($resultstr) == strlen($str) ? $resultstr : $resultstr . $pre;
}

function authcode($string, $operation, $key = '', $expiry = 0) {
    $authkey = Ebh::app()->security['authkey'];
    $ckey_length = 4; // 随机密钥长度 取值 0-32;
    // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
    // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
    // 当此值为 0 时，则不产生随机密钥

    $key = md5($key ? $key : $authkey);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}

/**
 * 根据原始图片文件,获取缩略图路径
 * 例子：getthumb('http://www.ebanhui.com/images_avater/2014/01/23/1390475735.jpg','120_120');则返回 http://www.ebanhui.com/images_avater/2014/01/23/1390475735_120_120.jp
 * @param string $imageurl	原始图片的路径
 * @param string $size	获取的规格大小  用"_"分隔开
 * @param string $defaulturl Description
 */
function getthumb($imageurl, $size, $defaulturl = '') {
	if(empty($imageurl))
		return $defaulturl;
    $ipos = strrpos($imageurl, '.');
    if ($ipos === FALSE)
        return $imageurl;
    $newimagepath = substr($imageurl, 0, $ipos) . '_' . $size . substr($imageurl, $ipos);
    return $newimagepath;
}

//生成随机字符串或数字
function random($length, $numeric = 0) {
    PHP_VERSION < '4.2.0' ? mt_srand((double) microtime() * 1000000) : mt_srand();
    $seed = base_convert(md5(print_r($_SERVER, 1) . microtime()), 16, $numeric ? 10 : 35);
    $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
    $hash = '';
    $max = strlen($seed) - 1;
    for ($i = 0; $i < $length; $i++) {
        $hash .= $seed[mt_rand(0, $max)];
    }
    return $hash;
}

/**
 * 解析querystring字符串作为查询参数数组返回
 * @return array
 */
function parsequery() {
    $queryarray = array();
    $uri = Ebh::app()->getUri();
    $queryarray['pagesize'] = 20;
    $queryarray['page'] = $uri->page;
    $queryarray['sortmode'] = $uri->sortmode;
    $queryarray['viewmode'] = $uri->viewmode;
    $queryarray['q'] = Ebh::app()->getInput()->get('q');
    return $queryarray;
}

/**
 * 获取分页html代码
 * @param int $listcount总记录数
 * @param int $pagesize分页大小
 * @return string
 */
function show_page($listcount, $pagesize = 20) {
    $pagecount = @ceil($listcount / $pagesize);
    $uri = Ebh::app()->getUri();
    $curpage = $uri->page;
    $prefixlink = '/' . $uri->codepath;
    if (!empty($uri->itemid))
        $prefixlink .= '/' . $uri->itemid;
    $prefixlink .= '-';
    $suffixlink = '-' . $uri->sortmode . '-' . $uri->viewmode;
    if (!empty($uri->attribarr))
        $suffixlink .= '-' . implode('-', $uri->attribarr);
    $suffixlink .= '.html';
    $query_string = $uri->uri_query_string();
    if (!empty($query_string))
        $suffixlink .= '?' . $query_string;
    if ($curpage > $pagecount) {
        $curpage = $pagecount;
    }
    if ($curpage < 1) {
        $curpage = 1;
    }
    //这里写前台的分页
    $centernum = 10; //中间分页显示链接的个数
    $multipage = '<div class="pages"><div class="listPage">';
    if ($pagecount <= 1) {
        $back = '';
        $next = '';
        $center = '';
        $gopage = '';
    } else {
        $back = '';
        $next = '';
        $center = '';
        $gopage = '<input id="gopage" maxpage="' . $pagecount . '" onblur="if($(this).val()>' . $pagecount . '){$(this).val(' .
                $pagecount . ')}" type="text" size="3" value="" onfocus="this.select();"  onkeyup="this.value=this.value.replace(/\D/g,\'\')" onafterpaste="this.value=this.value.replace(/\D/g,\'\')"><a id="page_go" href="###"  onclick="window.location.href=\'' .
                $prefixlink . '\'+$(this).prev(\'#gopage\').val()+\'' . $suffixlink . '\'">跳转</a>';
        if ($curpage == 1) {
            for ($i = 1; $i <= $centernum; $i++) {
                if ($i > $pagecount) {
                    break;
                }
                if ($i != $curpage) {
                    $center .= '<a href="' . $prefixlink . ($i) . $suffixlink . '">' . $i . '</a>';
                } else {
                    $center .= '<a class="none">' . $i . '</a>';
                }
            }
            $next .= '<a href="' . $prefixlink . ($curpage + 1) . $suffixlink . '" id="next">下一页&gt;&gt;</a>';
        } elseif ($curpage == $pagecount) {
            $back .= '<a href="' . $prefixlink . ($curpage - 1) . $suffixlink . '" id="next">&lt;&lt;上一页</a>';
            for ($i = $pagecount - $centernum + 1; $i <= $pagecount; $i++) {
                if ($i < 1) {
                    $i = 1;
                }
                if ($i != $curpage) {
                    $center .= '<a href="' . $prefixlink . $i . $suffixlink . '">' . $i . '</a>';
                } else {
                    $center .= '<a class="none">' . $i . '</a>';
                }
            }
        } else {
            $back .= '<a href="' . $prefixlink . ($curpage - 1) . $suffixlink . '" id="next">&lt;&lt;上一页</a>';
            $left = $curpage - floor($centernum / 2);
            $right = $curpage + floor($centernum / 2);
            if ($left < 1) {
                $left = 1;
                $right = $centernum < $pagecount ? $centernum : $pagecount;
            }
            if ($right > $pagecount) {
                $left = $centernum < $pagecount ? ($pagecount - $centernum + 1) : 1;
                $right = $pagecount;
            }
            for ($i = $left; $i <= $right; $i++) {
                if ($i != $curpage) {
                    $center .= '<a href="' . $prefixlink . $i . $suffixlink . '">' . $i . '</a>';
                } else {
                    $center .= '<a class="none">' . $i . '</a>';
                }
            }
            $next .= '<a href="' . $prefixlink . ($curpage + 1) . $suffixlink . '" id="next">下一页&gt;&gt;</a>';
        }
    }
    $multipage .= $back . $center . $next . $gopage . '</div></div>';
    $multipage .= '<script type="text/javascript">' . "\n"
            . '$(function(){' . "\n"
            . '$("#gopage").keypress(function(e){' . "\n"
            . 'if (e.which == 13){' . "\n"
            . '$(this).next("#page_go").click()' . "\n"
            . 'cancelBubble(this,e);' . "\n"
            . '}' . "\n"
            . '})' . "\n"
            . '})</script>';
    return $multipage;

}

/**
 * 输出二进制文件
 * @param string $type 输出的文件类型项，此值必须与upconfig对应的项相同
 * @param string $filepath文件保存的相对路径，通过upconfig的savepath可找到绝对路径
 * @param string $filename文件输出的显示名称
 */
function getfile($type = 'course', $filepath, $filename) {
    $_UP = Ebh::app()->getConfig()->load('upconfig');
    $realpath = $_UP[$type]['savepath'] . $filepath;
    $showpath = $_UP[$type]['showpath'];
    if (!file_exists($realpath)) {
        log_message('文件不存在'.$realpath);
    } else {
        $ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
        if ($type != 'course' && $type != 'note') {
            $fname = $filename;
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') || stripos($_SERVER['HTTP_USER_AGENT'], 'trident')) {
                $fname = urlencode($fname);
            } else {
                $fname = str_replace(' ', '', $fname);
            }
        } else {
            $fname = time() . '.ebhp';
        }
        if ($ext == 'swf') {
            header("Content-Type: application/x-shockwave-flash");
        } else {
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . $fname);
        }
        $webtype = Ebh::app()->web['type'];
        if(empty($webtype))
            $webtype = 'nginx';
        if ($webtype == 'nginx') {
            header("X-Accel-Redirect: " . $showpath . $filepath);
        } else {
            header('X-Sendfile:' . $realpath);
        }
        exit();
    }
}
/**
 * 删除文件
 * @param string $type 删除的文件类型项，此值必须与upconfig对应的项相同
 * @param string $filepath文件相对路径，与upconfig的savepath组合起来即为实际路径
 */
function delfile($type = 'course', $filepath) {
    $_UP = Ebh::app()->getConfig()->load('upconfig');
    $realpath = $_UP[$type]['savepath'] . $filepath;
    if (file_exists($realpath)) {
        @unlink($realpath);
    }
}

function remove_xss($val) {
   // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
   // this prevents some character re-spacing such as <java\0script>
   // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
   // $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

   // straight replacements, the user should never need these since they're normal characters
   // this prevents like <IMG SRC=@avascript:alert('XSS')>
   $search = 'abcdefghijklmnopqrstuvwxyz';
   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $search .= '1234567890!@#$%^&*()';
   $search .= '~`";:?+/={}[]-_|\'\\';
   for ($i = 0; $i < strlen($search); $i++) {
      // ;? matches the ;, which is optional
      // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

      // @ @ search for the hex values
      $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
      // @ @ 0{0,7} matches '0' zero to seven times
      $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
   }

   // now the only remaining whitespace attacks are \t, \n, and \r
   $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
   $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
   $ra = array_merge($ra1, $ra2);

   $found = true; // keep replacing as long as the previous round replaced something
   while ($found == true) {
      $val_before = $val;
      for ($i = 0; $i < sizeof($ra); $i++) {
         $pattern = '/';
         for ($j = 0; $j < strlen($ra[$i]); $j++) {
            if ($j > 0) {
               $pattern .= '(';
               $pattern .= '(&#[xX]0{0,8}([9ab]);)';
               $pattern .= '|';
               $pattern .= '|(&#0{0,8}([9|10|13]);)';
               $pattern .= ')*';
            }
            $pattern .= $ra[$i][$j];
         }
         $pattern .= '/i';
         $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
         $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
         if ($val_before == $val) {
            // no replacements were made, so exit the loop
            $found = false;
         }
      }
   }
   return $val;
}
//获取安全html
function h($text, $tags = null) {
    $text   =   trim($text);
    //完全过滤注释
    $text   =   preg_replace('/<!--?.*-->/','',$text);
    //完全过滤动态代码
    $text   =   preg_replace('/<\?|\?'.'>/','',$text);
    //完全过滤js
    $text   =   preg_replace('/<script?.*\/script>/','',$text);

    $text   =   str_replace('[','&#091;',$text);
    $text   =   str_replace(']','&#093;',$text);
    $text   =   str_replace('|','&#124;',$text);
    //过滤换行符
    $text   =   preg_replace('/\r?\n/','',$text);
    //br
    $text   =   preg_replace('/<br(\s*\/)?'.'>/i','[br]',$text);
    $text   =   preg_replace('/<p(\s*\/)?'.'>/i','[p]',$text);
    $text   =   preg_replace('/(\[br\]\s*){10,}/i','[br]',$text);
    //过滤危险的属性，如：过滤on事件lang js
    while(preg_match('/(<[^><]+)( lang|on|action|background|codebase|dynsrc|lowsrc)[^><]+/i',$text,$mat)){
        $text=str_replace($mat[0],$mat[1],$text);
    }
    while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i',$text,$mat)){
        $text=str_replace($mat[0],$mat[1].$mat[3],$text);
    }
    if(empty($tags)) {
        $tags = 'table|td|th|tr|i|b|u|strong|img|p|br|div|strong|em|ul|ol|li|dl|dd|dt|a';
    }
    //允许的HTML标签
    $text   =   preg_replace('/<('.$tags.')( [^><\[\]]*)>/i','[\1\2]',$text);
    $text = preg_replace('/<\/('.$tags.')>/Ui','[/\1]',$text);
    //过滤多余html
    $text   =   preg_replace('/<\/?(html|head|meta|link|base|basefont|body|bgsound|title|style|script|form|iframe|frame|frameset|applet|id|ilayer|layer|name|script|style|xml)[^><]*>/i','',$text);
    //过滤合法的html标签
    while(preg_match('/<([a-z]+)[^><\[\]]*>[^><]*<\/\1>/i',$text,$mat)){
        $text=str_replace($mat[0],str_replace('>',']',str_replace('<','[',$mat[0])),$text);
    }
    //转换引号
    while(preg_match('/(\[[^\[\]]*=\s*)(\"|\')([^\2=\[\]]+)\2([^\[\]]*\])/i',$text,$mat)){
        $text=str_replace($mat[0],$mat[1].'|'.$mat[3].'|'.$mat[4],$text);
    }
    //过滤错误的单个引号
    while(preg_match('/\[[^\[\]]*(\"|\')[^\[\]]*\]/i',$text,$mat)){
        $text=str_replace($mat[0],str_replace($mat[1],'',$mat[0]),$text);
    }
    //转换其它所有不合法的 < >
    $text   =   str_replace('<','&lt;',$text);
    $text   =   str_replace('>','&gt;',$text);
    $text   =   str_replace('"','&quot;',$text);
     //反转换
    $text   =   str_replace('[','<',$text);
    $text   =   str_replace(']','>',$text);
    $text   =   str_replace('|','"',$text);
    //过滤多余空格
    $text   =   str_replace('  ',' ',$text);
    return $text;
}

//64位编码
function base64str($str,$t=false){
	if(is_array($str)){
		foreach($str as $key=>$val ){
			$str[$key]=base64str($val,$t);
		}
	}else{
		if($t){//编码
			$str=base64_encode($str);
		}else{//解码
			$str=base64_decode($str);
		}
	}
	return $str;
}

/**
* 根据字节数获取文件可读性较好的大小
* @param int $bsize 字节数
*/
function getSize($bsize){
	$size = "0字节";
	if (!empty($bsize))
	{
		$gsize = $bsize / (1024 * 1024 * 1024);
		$msize = $bsize / (1024 * 1024);
		$ksize = $bsize / 1024;
		if ($gsize > 1)
		{
			$size = round($gsize,2) . "G";
		}
		else if($msize > 1)
		{
			$size = round($msize,2) . "M";
		}
		else if($ksize > 1)
		{

			$size = round($ksize,0) . "K";
		}
		else
		{
			$size = $bsize . "字节";
		}
	}
	return $size;
}
/**
*显示404页面
*/
function show_404() {
	$view = 'common/error404';
	$viewpath = VIEW_PATH.$view.'.php';
    include $viewpath;
}
/*
*表情图片
*/
function getEmotionarr(){
    $emotionarr = array('微笑','大笑','飞吻','疑问','悲泣','大哭','痛哭','学习雷锋','成交','鼓掌','握手','红唇','玫瑰','爱心','礼物');
    return $emotionarr;
}

/*
*评论表情图片转换
*/
function parseEmotion($reviews){
    $emotionarr = getEmotionarr();
    $matstr = '/\[emo(\S{1,2})\]/is';
	$matstr2 = '/\[em_(\S{1,2})\]/is';
    $emotioncount = count($emotionarr);
    $subject = '';
    foreach($reviews as $k=>$review){
        $subject = $review['subject'];
        preg_match_all($matstr,$subject,$mat);
        foreach($mat[0] as $l=>$m){
            $imgnumber = intval($mat[1][$l]);
            if($imgnumber<$emotioncount)
            $reviews[$k]['subject']=str_replace($m,'<img title="'.$emotionarr[$imgnumber].'" src="http://static.ebanhui.com/ebh/tpl/default/images/'.$imgnumber.'.gif">',$reviews[$k]['subject']);
            
        }
		//qq表情
		preg_match_all($matstr2,$subject,$mat2);
		foreach($mat2[0] as $l=>$m){
			$imgnumber = intval($mat2[1][$l]);
			if($imgnumber<=75)
			$reviews[$k]['subject']=str_replace($m,'<img src="http://static.ebanhui.com/ebh/js/qqFace/arclist/'.$imgnumber.'.gif">',$reviews[$k]['subject']);
			
		}
    }
    return $reviews;
}

/**
 *获取二维数组指定的字段集合
 */
function getFieldArr($param = array(),$filedName=''){
    
    $reuturnArr = array();

    if(empty($filedName)||empty($param)){
        return $reuturnArr;
    }

    foreach ($param as $value) {
        array_push($reuturnArr, $value[$filedName]);
    }

    return $reuturnArr;
}

/*
将秒数转化为天/小时/分/秒
*/
function secondToStr($time){

    $str = '';
    $timearr = array(86400 => '天', 3600 => '小时', 60 => '分', 1 => '秒');
    foreach ($timearr as $key => $value) {
        if ($time >= $key)
            $str .= floor($time/$key) . $value;
        $time %= $key;
    }
    return $str;
}

function do_post($url, $data , $retJson = true){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
    curl_setopt($ch, CURLOPT_POST, TRUE); 
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
    curl_setopt($ch, CURLOPT_URL, $url);
    $ret = curl_exec($ch);
    curl_close($ch);
    if($retJson == false){
        $ret = json_decode($ret);
    }
    return $ret;
}

if (!function_exists('curl_file_create')) {
    function curl_file_create($filename, $mimetype = '', $postname = '') {
        return "@$filename;filename="
            . ($postname ?: basename($filename))
            . ($mimetype ? ";type=$mimetype" : '');
    }
}

//将数组里面的时间数据格式化
function formatDate(&$arrs,$fieldNames=array('dateline'),$toFieldNames=array('dateline'),$formatStr='Y-m-d H:i',$date_map = array()){
    if(empty($arrs)){
        return ;
    }
    if(empty($date_map)){
        $date_map = array(
            date("Y-m-d",strtotime("-2 day")) => '前天',
            date("Y-m-d",strtotime("-1 day")) => '昨天',
            date("Y-m-d") => '今天'
        );
    }
    foreach ($arrs as &$arr) {
        foreach ($fieldNames as $k => $fieldName) {
            if(empty($arr[$fieldName])){
                $arr[$toFieldNames[$k]] = '';
                continue;
            }
            $dateline = date($formatStr,$arr[$fieldName]);
            $dateArr = explode(' ', $dateline);
            if( array_key_exists($dateArr[0], $date_map) ){
                $arr[$toFieldNames[$k]] = $date_map[$dateArr[0]];
            }else{
                $arr[$toFieldNames[$k]] = $dateline;
            }
        }
    }
}

//获取IP
function getip()
{
	if(!empty($_SERVER["HTTP_CLIENT_IP"]))
		$cip = $_SERVER["HTTP_CLIENT_IP"];
	else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
		$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	else if(!empty($_SERVER["REMOTE_ADDR"]))
		$cip = $_SERVER["REMOTE_ADDR"];
	else
		$cip = "127.0.0.1";
	$cip = preg_match('/[\d\.]{7,15}/', $cip, $matches) ? $matches[0] : '';
	return $cip;
}

/**
 * 获取用户头像
 * @param unknown $user
 * @param string $size
 */
function getavater($user,$size='120_120'){
	$defaulturl = "http://static.ebanhui.com/ebh/tpl/default/images/";
	$face = "";
	if(!empty($user['face'])){
		$ext = substr($user['face'], strrpos($user['face'], '.'));
		$face = str_replace($ext,'_'.$size.$ext,$user['face']);
	}else{
		if(isset($user['sex'])){
			if($user['sex']==1){//女
				$face = $user['groupid'] == 5 ? $defaulturl."t_woman.jpg" : $defaulturl."m_woman.jpg";
				$face = str_replace('.jpg','_'.$size.'.jpg',$face);
			}else{//男
				$face = $user['groupid'] == 5 ? $defaulturl."t_man.jpg" : $defaulturl.'m_man.jpg';
				$face = str_replace('.jpg','_'.$size.'.jpg',$face);
			}
		}else{
			$face = $defaulturl.'m_man.jpg';
			$face = str_replace('.jpg','_'.$size.'.jpg',$face);
		}
	}
	return $face;
}

/**
 * json渲染. PS:调用此方法之前若有输出将会出错
 *
 * @param mixed     $data
 * @param int       $code 0成功 非0错误
 * @param string    $msg  错误信息
 * @author echo
 */
if(!function_exists('renderJson')){
	function renderJson( $code = 0, $msg = '',$data = null ,$ifexist = true) {
		$ret = array(
				'code' => (int)$code,
				'status'=>$code,
				'msg'  => $msg,
				'data' => $data,
		);
		echo json_encode($ret);
		if($ifexist === true){
			exit;
		}
	}
}

/**
 * php版本低于php5.5array_column
 */
if(function_exists('array_column') === false) {
    function array_column($arr, $column_name) {
        $tmp = array();
        foreach($arr as $item) {
            $tmp[] = $item[$column_name];
        }
        return $tmp;
    }
}