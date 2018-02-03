<?php
class Mail{
	function sendemail($toarr, $subject, $message='', $from='') {
		$mail = Ebh::app()->getConfig()->load('mailconfig');
		
		//邮件头的分隔符
		$maildelimiter = $mail['maildelimiter'] == 1 ? "\r\n" : ($mail['maildelimiter'] == 2 ? "\r" : "\n");
		//收件人地址中包含账号
		$mailusername = isset($mail['mailusername']) ? $mail['mailusername'] : 1;
		//端口
		$mail['port'] = $mail['port'] ? $mail['port'] : 25;
		$mail['mailsend'] = $mail['mailsend'] ? $mail['mailsend'] : 1;
		//发信方式
		if($mail['mailsend'] == 3) {
			$email_from = empty($from) ? $mail['adminemail'] : $from;
		} else {
			$email_from = $from == '' ? '=?UTF-8?B?'.base64_encode($mail['sitename'])."?= <".$mail['adminemail'].">" : (preg_match('/^(.+?) \<(.+?)\>$/',$from, $mats) ? '=?UTF-8?B?'.base64_encode($mats[1])."?= <$mats[2]>" : $from);
		}
		foreach($toarr as $key => $toemail) {
			$toarr[$key] = preg_match('/^(.+?) \<(.+?)\>$/',$toemail, $mats) ? ($mailusername ? '=?UTF-8?B?'.base64_encode($mats[1])."?= <$mats[2]>" : $mats[2]) : $toemail;
		}
		$email_to = implode(',', $toarr);
		
		$email_subject = '=?UTF-8?B?'.base64_encode(preg_replace("/[\r|\n]/", '', '['.$mail['sitename'].'] '.$subject)).'?=';
		$email_message = chunk_split(base64_encode(str_replace("\n", "\r\n", str_replace("\r", "\n", str_replace("\r\n", "\n", str_replace("\n\r", "\r", $message))))));
		
		$headers = "From: $email_from{$maildelimiter}X-Priority: 3{$maildelimiter}X-Mailer: 51ebh ".$mail['ver']."{$maildelimiter}MIME-Version: 1.0{$maildelimiter}Content-type: text/html; charset=UTF-8{$maildelimiter}Content-Transfer-Encoding: base64{$maildelimiter}";

		if($mail['mailsend'] == 1) {
			
			if(function_exists('mail') && @mail($email_to, $email_subject, $email_message, $headers)) {
				return true;
			}
			return false;
			
		} elseif($mail['mailsend'] == 2) {
			
			if(!$fp = fsockopen($mail['server'], $mail['port'], $errno, $errstr, 30)) {
				return false;
			}
			stream_set_blocking($fp, true);
		
			$lastmessage = fgets($fp, 512);
			if(substr($lastmessage, 0, 3) != '220') {
				return false;
			}
			fputs($fp, ($mail['auth'] ? 'EHLO' : 'HELO')." 51ebh\r\n");
			$lastmessage = fgets($fp, 512);
			if(substr($lastmessage, 0, 3) != 220 && substr($lastmessage, 0, 3) != 250) {
				return false;
			}
			
			while(1) {
				if(substr($lastmessage, 3, 1) != '-' || empty($lastmessage)) {
					break;
				}
				$lastmessage = fgets($fp, 512);
			}
			
			if($mail['auth']) {
				fputs($fp, "AUTH LOGIN\r\n");
				$lastmessage = fgets($fp, 512);
				if(substr($lastmessage, 0, 3) != 334) {
					return false;
				}
				
				fputs($fp, base64_encode($mail['auth_username'])."\r\n");
				$lastmessage = fgets($fp, 512);
				if(substr($lastmessage, 0, 3) != 334) {
					return false;
				}
				
				fputs($fp, base64_encode($mail['auth_password'])."\r\n");
				$lastmessage = fgets($fp, 512);
				if(substr($lastmessage, 0, 3) != 235) {
					return false;
				}
				
				$email_from = $mail['from'];
			}
		
			fputs($fp, "MAIL FROM: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from).">\r\n");
			$lastmessage = fgets($fp, 512);
			if(substr($lastmessage, 0, 3) != 250) {
				fputs($fp, "MAIL FROM: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from).">\r\n");
				$lastmessage = fgets($fp, 512);
				if(substr($lastmessage, 0, 3) != 250) {
					return false;
				}
			}
		
			foreach($toarr as $toemail) {
				$toemail = trim($toemail);
				if($toemail) {
					fputs($fp, "RCPT TO: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $toemail).">\r\n");
					$lastmessage = fgets($fp, 512);
					if(substr($lastmessage, 0, 3) != 250) {
						fputs($fp, "RCPT TO: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $toemail).">\r\n");
						$lastmessage = fgets($fp, 512);
						return false;
					}
				}
			}
			
			fputs($fp, "DATA\r\n");
			$lastmessage = fgets($fp, 512);
			if(substr($lastmessage, 0, 3) != 354) {
				return false;
			}
		
			$headers .= 'Message-ID: <'.gmdate('YmdHs').'.'.substr(md5($email_message.microtime()), 0, 6).rand(100000, 999999).'@'.$_SERVER['HTTP_HOST'].">{$maildelimiter}";
		
			fputs($fp, "Date: ".gmdate('r')."\r\n");
			fputs($fp, "To: ".$email_to."\r\n");
			fputs($fp, "Subject: ".$email_subject."\r\n");
			fputs($fp, $headers."\r\n");
			fputs($fp, "\r\n\r\n");
			fputs($fp, "$email_message\r\n.\r\n");
			$lastmessage = fgets($fp, 512);
			if(substr($lastmessage, 0, 3) != 250) {
			}
			fputs($fp, "QUIT\r\n");
			
			return true;

		} elseif($mail['mailsend'] == 3) {

			ini_set('SMTP', $mail['server']);
			ini_set('smtp_port', $mail['port']);
			ini_set('sendmail_from', $email_from);
		
			if(function_exists('mail') && @mail($email_to, $email_subject, $email_message, $headers)) {
				return true;
			}
			return false;
		}
	}

	//加密
	function encrypt($time="")
	{
		if(empty($time)){
			$time = time();
		}
		return urlencode( strtolower(substr(md5(substr(time(), 7)),0,4)).base64_encode($time));
	}
	//解密
	function decrypt($data){
		substr(urldecode($data),4).'<br>';
		return base64_decode(substr( urldecode($data),4));
	}
	
	function getmessage($username){
		$message = "" . '<p>亲爱的：' .$username.' 你好</p>' . "\n" . '----------------------------------------------------------------------<br />' . "\n" . '您已经申请了忘记密码，请在24小时内更换您的密码，如果不做任何操作，系统将保留原密码。<br />' . "\n" . '----------------------------------------------------------------------<br />' . "\n" . '<br />' . "\n" . '您可以点击下面的链接进行更换密码：</p>' . "\n" . '<p><a href="%s" target="_blank">%s</a></p>' . "\n" . '<p>(如果上面不是链接形式，请将地址手工粘贴到浏览器地址栏再访问)<p>' . '此邮件为系统邮件，请勿直接回复。<br/>' . 'e板会系统     www.ebanhui.com';
		return $message;
	}
}
?>