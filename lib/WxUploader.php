<?php
/**
 * 从微信服务器上传文件到本地服务器的类
 */
class WxUploader
{
    private $mediaId;            //微信媒体唯一标识
    private $config;               //配置信息
    private $fileName = NULL;             //新文件名
    private $fullName;             //完整文件名,即从当前配置目录开始的URL
	private $folder = NULL;				//文件存储的相对文件夹 如按日期来的 2014/04/25/
    private $showurl;               //文件显示地址，如http://img.ebanhui.com/ebh/2014/03/11/13123123.jpg
    private $fileSize;             //文件大小
    private $fileType;             //文件类型
    private $stateInfo;            //上传状态信息,
    private $access_token;          //微信access_token
    private $response_header;       //微信媒体服务器响应头信息
    private $WxUtils;
    private $stateMap = array(    //上传状态映射表，国际化用户需考虑此处数据的国际化
        "SUCCESS" ,                //上传成功标记，在UEditor中内不可改变，否则flash判断会出错
        "文件大小超出 upload_max_filesize 限制" ,
        "文件大小超出 MAX_FILE_SIZE 限制" ,
        "文件未被完整上传" ,
        "没有文件被上传" ,
        "上传文件为空" ,
        "POST" => "文件大小超出 post_max_size 限制" ,
        "SIZE" => "文件大小超出网站限制" ,
        "TYPE" => "不允许的文件类型" ,
        "DIR" => "目录创建失败" ,
        "IO" => "输入输出错误" ,
        "UNKNOWN" => "未知错误" ,
        "MOVE" => "文件保存时出错",
        "DIR_ERROR" => "创建目录失败",
        "REQUEST_REMOTE_FAIL"=>"请求微信媒体服务器异常"
    );

    /**
     * 初始化函数
     * @param string $mediaId 表单名称
     * @param array $config  配置项
     * @param bool $base64  是否解析base64编码，可省略。若开启，则$fileField代表的是base64编码的字符串表单名
     */
    public function init( $mediaId , $config , $refresh = false)
    {
        if(!empty($mediaId)){
            $this->mediaId = $mediaId;
        }
        if(!empty($config)){
            $this->config = $config;
        }
        if(empty($this->mediaId) || empty($this->config)){
            return;
        }

        $this->WxUtils = Ebh::app()->lib('WxUtils');
        $this->access_token = $this->WxUtils->getAccessToken($refresh);
        // log_message('access_token:'.$this->access_token);
        $this->stateInfo = $this->stateMap[ 0 ];
        $this->get_media_data();
    }

    private function get_media_data(){
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".$this->access_token."&media_id=".$this->mediaId;
        $curl = curl_init($url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        $mediaData = curl_exec($curl);
        $errno = curl_errno($curl);
        if($errno === 0){
            $this->response_header = curl_getinfo($curl);
            $this->fileType = $this->response_header['content_type'];
            if($this->getFileExt() == ".plain"){
                $errorInfo = json_decode($mediaData);
                $errmsg = $errorInfo->errmsg;
                $errcode = $errorInfo->errcode;
                if($errcode == 42001){//access_token 过期 重新发起请求
                     $this->get_media_data(null,null,true);
                     return;
                }
            }
            curl_close($curl);
            $this->InputStreamToFile($mediaData);
        }else{
            $this->stateInfo = $this->stateMap['REQUEST_REMOTE_FAIL'];
            log_message("请求微信媒体服务器异常:url:".$url);
        }
        
    }

    /**
     * 处理文件流上传
     * @param $base64Data
     * @return mixed
     */
    private function InputStreamToFile( $InputStreamData)
    {
        $this->fileName = $this->getName();
        $this->fullName = $this->getFolder() . '/' . $this->fileName;
        if ( !file_put_contents( $this->config[ "savePath" ].$this->fullName , $InputStreamData ) ) {
            $this->stateInfo = $this->getStateInfo( "IO" );
            return;
        }
        $this->fileSize = strlen( $InputStreamData );
        $this->showurl = $this->config["showPath"].$this->fullName;
    }

    /**
     * 获取当前下载成功文件的各项信息
     * @return array
     */
    public function getFileInfo()
    {
        return array(
            "name" => $this->fileName ,
            "url" => $this->fullName ,
            "showurl" => $this->showurl,
            "size" => $this->fileSize ,
            "type" => $this->fileType ,
            "state" => $this->stateInfo
        );
    }

    /**
     * 重命名文件
     * @return string
     */
    private function getName()
    {
		if($this->fileName !== NULL)
			return $this->fileName;
        return $this->fileName = time() . rand( 1 , 10000 ) . $this->getFileExt();
    }
	/**
	* 设置保存的文件名
	* 如 sestName('13123123.jpg');
	*/
	public function setName($fileName) {
		$this->fileName = $fileName;
	}

    /**
     * 获取文件扩展名
     * @return string
     */
    private function getFileExt()
    {
        $content_type = $this->response_header['content_type'];
        return '.'.substr($content_type, strrpos($content_type,'/')+1);
    }

    /**
     * 按照日期自动创建存储文件夹
     * @return string
     */
    private function getFolder()
    {
		if($this->folder !== NULL)
			return $this->folder;
        $pathStr = $this->config[ "savePath" ];
        //以天存档
        $yearpath = Date('Y', SYSTIME) . "/";
        $monthpath = $yearpath . Date('m', SYSTIME) . "/";
        $dayspath = $monthpath . Date('d', SYSTIME) . "/";
        if (!file_exists($pathStr))
            mkdir($pathStr);
        if (!file_exists($pathStr . $yearpath))
            mkdir($pathStr . $yearpath);
        if (!file_exists($pathStr . $monthpath))
            mkdir($pathStr . $monthpath);
        if (!file_exists($pathStr . $dayspath))
            mkdir($pathStr . $dayspath);
        return ltrim($dayspath, '.');
    }
	/**
	*设置存放相对文件夹
	*此方法与setName方法结合，可实现文件的覆盖
	*/
	public function setFolder($folder) {
		$this->folder = $folder;
	}
}