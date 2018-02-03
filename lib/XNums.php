<?php
/**
 *数量工具类
 *优先取缓存，其次取文件数据库，最后取mysql数据库
 */
class xNums{
	private $db = NULL;
	private $redis = NULL;
	public function __construct(){
		$this->db = Ebh::app()->getDb();
		$this->redis = Ebh::app()->getCache('cache_redis');
	}
	//指定类型添加指定数量正数添加负数减少
	public function add($type,$offset = 1){
		$redis = $this->redis;
		$num = $redis->hget('xnum',$type);
        if(empty($num)) {
        	$num = $this->_getNumFromFileDb($type);
			$redis->hset('xnum',$type,$num);
        }
		$redis->hIncrBy('xnum',$type,$offset);
		if($num%20 == 0){
			//写文件数据库
			$this->_syncFile();
		}
		return $this;
	}

	//重新同步缓存到文件数据库
	public function refresh(){
		$redis = $this->redis;
		$redis->del('xnum');
		$this->_syncFromSql();
		return $this;
	}

	//从数据库同步数量(用于第一次使用该lib的时候)
	public function _syncFromSql(){
		$redis = $this->redis;
		$allowTypes = $this->_getAllowType();
		foreach ($allowTypes as $type) {
			$num = $this->_getNumFromSql($type);
			$redis->hset('xnum',$type,$num);
		}
		$this->_syncFile();
	}
	//获取指定类型的数量
	public function get($type = ''){
		$redis = $this->redis;
		$res = $redis->hget('xnum');
		if(empty($res)){
			$this->refresh();
		}
		if(empty($type)){
			$res = $redis->hget('xnum');
			$allowType = $this->_getAllowType();
			foreach ($allowType as $type) {
				if(!array_key_exists($type, $res)){
					$this->refresh();
					break;
				}
			}
			return $redis->hget('xnum');
		}
		$res = $redis->hget('xnum',$type);
		if(empty($res)){
			$this->refresh();
		}
		return $redis->hget('xnum',$type);
	}

	//将数据同步到文件
	public function _syncFile(){
		$redis = $this->redis;
		$logs_cache = $redis->hget('xnum');
		$this->_writeFileDb($logs_cache);
	}

	//从数据库获取指定类型的数量
	private function _getNumFromSql($type = ''){
		if(!$this->_checkType($type)){
			log_message('xnum:类型'.$type.'不允许');
			exit;
		}
		if(empty($this->db)){
			$this->db = Ebh::app()->getDb();
		}
		if($type == 'teacher'){
			$sql = 'select count(1) count from ebh_teachers';
		}else if($type == 'room'){
			$sql = 'select count(1) count from ebh_classrooms';
		}else if($type == 'user'){
			$sql = 'select count(1) count from ebh_users';
		}else if($type == 'resource'){
			$sql = 'select count(1) count from res_resource';
		}
		if($type == 'resource'){
			$res = Ebh::app()->getOtherDb('freeresourcedb')->query($sql)->row_array();
		}else{
			$res = $this->db->query($sql)->row_array();
		}
		if(!empty($res)){
			return $res['count'];
		}
		return 0;
	}

	//从文件获取指定类型的数量
	private function _getNumFromFileDb($type = ''){
		if(!$this->_checkType($type)){
			log_message('xnum:类型'.$type.'不允许');
			exit;
		}
		$this->_checkFileDb();
		$xnums = Ebh::app()->getConfig()->load('xnum');
		if(empty($xnums) || empty($xnums[$type])){
			return 0;
		}
		return $xnums[$type];
	}

	//判断是否是允许的类型
	private function _checkType($type = ''){
		if(empty($type)){
			return false;
		}
		$allowType = $this->_getAllowType();
		return in_array($type, $allowType);
	}

	private function _getAllowType(){
		return  array(
			'teacher',
			'room',
			'user',
			'resource'
		);
	}

	//检测文件数据库
	private function _checkFileDb(){
		if( !is_file( $file = $this->_getDbPath()) ){
			$this->_createFileDb();
		}else{
			if(!is_writeable($file)){
				log_message($file.'不可写');
				exit;
			}
		}
	}

	//创建文件数据库
	private function _createFileDb(){
		$file = $this->_getDbPath();
		if(empty($file)){
			log_message('创建配置文件路径不存在');
			exit;
		}
		touch($file,0777);
		if(!is_file($file)){
			log_message($file.'创建失败');
			exit;
		}
		if(!is_writeable($file)){
			log_message($file.'不可写');
			exit;
		}
	}

	//写文件数据库
	private function _writeFileDb($data = array()){
		//写文件
		$this->_checkFileDb();
		$file = $this->_getDbPath();
		$str = "<?php\r\n".'$xnum = '.var_export($data,true).';';
		file_put_contents($file, $str);
	}

	//获取文件数据库路径
	private function _getDbPath(){
		return dirname(dirname(__FILE__)).'/config/xnum.php';
	}

	public function delCache(){
		$redis = $this->redis;
		$redis->del('xnum');
	}
}