<?php
/*********************************************************用户工具类***************************************************************
 * 主要用于从一堆数据中提取出用户信息																		                      *
 * 一般用于解决一条数据获取多个用户信息而多次连接users表导致数据库查询性能低下的问题						                      *	
 * ZKQ 2014-11-28																							                      *
 *																											                      *
 *  教程1:																									                      *
 *																											                      *
 *		$object = array(																					                      *
 *			array('uid'=>13192,'tid'=>2,'o'=>'a'),															                      *	
 *			array('uid'=>10797,'tid'=>4,'o'=>'b'),															                      *	
 *			array('uid'=>4,'tid'=>5,'o'=>'c')																                      *
 *		);																									                      *
 *																											                      *
 *		$userdata = EBH::app()->lib('UserUtil')->init($object,array('uid','tid')); //用户组数据获取			                      *
 *		$userdata->setUser(13192); //设置当前用户															                      *
 *		$username = $userdata->getUsername(); //获取用户名字 												                      *
 *		$sex = $userdata->getSex(); //获取用户性别															                      *
 *																											                      *
 *	教程2:																									                      *
 *		$object = array(																					                      *
 *			array('uid'=>13192,'tid'=>2,'o'=>'a'),															                      *	
 *			array('uid'=>10797,'tid'=>4,'o'=>'b'),															                      *	
 *			array('uid'=>4,'tid'=>5,'o'=>'c')																                      *
 *		);																									                      *
 *																											                      *
 *		$newObject = EBH::app()->lib('UserUtil')->init($object,array('uid','tid'),true);用户信息追加到原数组                      *
 *		或者：(设置获取指定大小的头像[没有头像则根据用户组获取默认头像])                                                          *
 *      $newObject = EBH::app()->lib('UserUtil')->setFaceSize('50_50')->init($object,array('uid','tid'),true);用户信息追加到原数组*
 *      此时$newObject中就包含了用户信息数据																                      *		
 *		要获取第一条数据的用户uid对应的用户名字:$newObject[0]['uid_name']									                      *
 *		要获取第二条数据的用户tid对应的用户性别:$newObject[1]['tid_sex']									                      *
 *																											                      *
 **********************************************************************************************************************************/
class UserUtil{
	private $faceSize = "";
	/**
	 *从数据库查询的数据数组$object里面根据$fields($fields数组指定了要从$object数组中的哪些字段提取uid)字段提取用户信息
	 */
	public function init($object = array(),$fields = array(),$ifSuperRevert = false){
		$uids = $this->_getUids($object,$fields);
		if(empty($uids)){
			return $object;
		}
		$usersInfo = EBH::app()->model('user')->getUserInfoByUid($uids);
		$this->userdata = $this->_modifyKeys($usersInfo);
		if($ifSuperRevert !=false){
			return $this->_superRevert($object,$fields);
		}else{
			return $this;
		}
	}
	/**
	 *根据$fileds数组里的字段从objsecs里提取用户uid
	 */
	private function _getUids($objects = array(),$fields = array()){
		$uids = array();
		foreach ($objects as $user) {
			foreach ($fields as $field) {
				if(is_numeric($user[$field]) && $user[$field]>0)
				$uids[] = $user[$field];
			}
		}
		return array_unique($uids);
	}

	/**
	 *将索引数组变成关联数组
	 */
	private function _modifyKeys($users = array()){
		$returnArr = array();
		foreach ($users as $user) {
			$key = 'user_'.$user['uid'];
			$returnArr[$key] = $user;
		}
		return $returnArr;
	}

	private function _getUser($uid = 0){
		$key = 'user_'.$uid;
		if(array_key_exists($key, $this->userdata)){
			return $this->userdata[$key];
		}else{
			return null;
		}
	}
	/**
	 *获取当前用户登录名
	 */
	public function getUsername(){
		$user = $this->curUser;
		if(!empty($user)){
			return $user['username'];
		}else{
			return "";
		}
	}
	/**
	 *获取当前用户真实名字
	 */
	public function getRealname(){
		$user = $this->curUser;
		if(!empty($user)){
			return $user['realname'];
		}else{
			return "";
		}
	}
	/**
	 *获取当前用户名字
	 */
	public function getName(){
		$user = $this->curUser;
		if(!empty($user)){
			return empty($user['realname'])?$user['username']:$user['realname'];
		}else{
			return "";
		}
	}
	/**
	 *获取当前用户性别
	 */
	public function getSex($revert = true){
		$user = $this->curUser;
		if($revert != true){
			return $user['sex'];
		}
		if(!empty($user)){
			return empty($user['sex'])?'男':'女';
		}else{
			return "男";
		}
	}
	/**
	 *获取当前用户头像
	 */
	public function getFace($size = ''){
		if(empty($size)){
			$size = $this->faceSize;
		}
		$user = $this->curUser;
		if(!empty($user)){
			$sex = empty($user['sex']) ? 'man' : 'woman';
			$type = $user['groupid']==6?'m':'t';
			$defaulturl = 'http://static.ebanhui.com/ebh/tpl/default/images/'.$type.'_'.$sex.'.jpg';
			$face = empty($user['face']) ? $defaulturl : $user['face'];
			if(empty($size)){
				return $face;
			}else{
				return getthumb($face,$size);
			}
		}else{
			return "";
		}
	}

	/**
	 *获取用户组信息
	 */
	public function getGroupId(){
		$user = $this->curUser;
		return $user['groupid'];
	}
	/**
	 *设置当前用户
	 */
	public function setUser($uid){
		$this->curUser = $this->_getUser($uid);
		return $this;
	}

	/**
	 *用户信息注入
	 *
	 */
	private function _superRevert($objects = array(),$fields = array()){
		$newObject = array();
		foreach ($objects as $object) {
			foreach ($fields as $field) {
				$this->setUser($object[$field]);
				$object[$field.'_username'] = $this->getUsername();
				$object[$field.'_realname'] = $this->getRealname();
				$object[$field.'_name'] = $this->getName();
				$object[$field.'_sex'] = $this->getSex();
				$object[$field.'_face'] = $this->getFace();
			}
			$newObject[] = $object;
		}
		return $newObject;
	}

	public function setFaceSize($faceSize = ""){
		$this->faceSize = $faceSize;
		return $this;
	}
}