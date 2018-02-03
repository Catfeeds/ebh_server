<?php
/**
 *用户Model类
 */
class UserModel extends CModel {
    /**
     * 用username和password判断登录
     * @param type $username
     * @param type $userpass
     * @param boolean $iscoding 是否加密过密码
     * @return boolean 返回用户信息数组
     */
    public function login($username,$userpass,$iscoding = FALSE) {
        $pwd = $iscoding ? $userpass : md5($userpass);
        $username = $this->db->escape($username);
        $sql = "select u.wxopid,u.wxopenid,u.wxunionid,u.uid, u.username,u.realname,u.nickname,u.credit,u.face,u.sex,u.groupid, u.logincount,u.lastlogintime,u.lastloginip,u.password,u.status,u.mysign from ebh_users u where u.username=$username or u.mobile=".$username;
        $user = $this->db->query($sql)->row_array();
        if(empty($user) || $user['password'] != $pwd || $user['status'] == 0) {
            return false;
        }
        return $user;
    }
    /**
     * 用uid和password判断登录
     * @param type $uid
     * @param type $userpass
     * @param boolean $iscoding 是否加密过密码
     * @return boolean 返回用户信息数组
     */
    public function getloginbyuid($uid,$userpass,$iscoding = FALSE) {
        $pwd = $iscoding ? $userpass : md5($userpass);
        $sql = "select u.uid, u.username,u.realname,u.sex,u.email,u.face, u.groupid, u.credit,u.balance,u.logincount,u.password,u.lastloginip,u.status,u.wxopid,u.mysign from ebh_users u where u.uid=$uid";
        $user = $this->db->query($sql)->row_array();
        if(empty($user) || $user['password'] != $pwd || $user['status'] == 0) {
            return false;
        }
        return $user;
    }
	/**
	* 根据用户auth信息获取用户信息
	*/
	public function getloginbyauth($auth) {
		@list($password, $uid) = explode("\t", authcode($auth, 'DECODE'));
        $uid = intval($uid);
        if ($uid <= 0) {
            return FALSE;
        }
        $user = $this->getloginbyuid($uid,$password,TRUE);
		return $user;
	}
	/*
	用户名是否存在
	@param string $username
	*/
	public function exists($username){
		$sql = 'select 1 from ebh_users where username = \''.$this->db->escape_str($username).'\' limit 1';
		$row = $this->db->query($sql)->row_array();
        if(empty($row))
            return FALSE;
        return TRUE;
	}
	/*
	邮箱是否存在
	*/
	public function existsEmail($email){
		$sql = 'select 1 from ebh_users where email = \''.$this->db->escape_str($email).'\' limit 1';
		$row = $this->db->query($sql)->row_array();
        if(empty($row))
            return FALSE;
        return TRUE;
	}

    /*
    手机是否存在
    */
    public function existsMobile($mobile){
        $sql = 'select 1 from ebh_users where mobile = \''.$this->db->escape_str($mobile).'\' limit 1';
        $row = $this->db->query($sql)->row_array();
        if(empty($row))
            return FALSE;
        return TRUE;
    }
        /**
         * 根据uid获取用户基本信息
         * @param int $uid
         * @return array 
         */
        public function getuserbyuid($uid) {
            $sql = 'select u.balance,u.uid,u.username,u.groupid,u.realname,u.status,u.lastlogintime,u.sex,u.credit,u.mysign from ebh_users u where u.uid = '.$uid;
            return $this->db->query($sql)->row_array();
        }
        /**
         * 修改用户信息
         * @param type $param
         * @param type $uid
         */
        public function update($param,$uid) {
            $afrows = FALSE;    //影响行数
            $userarr = array();
            //修改user表信息
            if(!empty($param['username'])){
                $userarr['username'] = $param['username'];
            }
            if (!empty($param['password']))
                $userarr['password'] = md5($param['password']);
            if (isset($param['status']))
                $userarr['status'] = $param['status'];
            if (isset($param['realname']))
                $userarr['realname'] = $param['realname'];
            if (isset($param['nickname']))
                $userarr['nickname'] = $param['nickname'];
            if (isset($param['sex']))
                $userarr['sex'] = $param['sex'];
            if (isset($param['mobile']))
                $userarr['mobile'] = $param['mobile'];
            if (isset($param['email']))
                $userarr['email'] = $param['email'];
            if (isset($param['citycode']))
                $userarr['citycode'] = $param['citycode'];
            if (isset($param['address']))
                $userarr['address'] = $param['address'];
            if (isset($param['face']))
                $userarr['face'] = $param['face'];
			if(!empty($param['qqopid']))
				$userarr['qqopid'] = $param['qqopid'];
			if(!empty($param['sinaopid']))
				$userarr['sinaopid'] = $param['sinaopid'];
			//微信相关的
			if(!empty($param['wxopid']))
				$userarr['wxopid'] = $param['wxopid'];
			if(!empty($param['wxopenid']))
				$userarr['wxopenid'] = $param['wxopenid'];
			if(!empty($param['wxunionid']))
				$userarr['wxunionid'] = $param['wxunionid'];
			
			if(!empty($param['lastlogintime']))
				$userarr['lastlogintime'] = $param['lastlogintime'];
			if(!empty($param['lastloginip']))
                $userarr['lastloginip'] = $param['lastloginip'];
            $sarr = array();
            if(isset($param['logincount'])){
                $sarr['logincount'] = 'logincount+1';
            }
            if(isset($param['balance']))
				$userarr['balance'] = $param['balance'];
            if(!empty($param['mysign'])){
            	$userarr['mysign'] = $param['mysign'];
            }
            $wherearr = array('uid' => $uid);
            if (!empty($userarr)) {
                $afrows = $this->db->update('ebh_users', $userarr, $wherearr,$sarr);
            }
            return $afrows;
        }
		
	/**
    * 根据username获取用户基本信息  场景：学校后台添加教师
    * @param int $uid
    * @return array 
    */
	public function getuserbyusername($username) {
		$sql = 'select u.uid,u.password,u.groupid,u.realname,u.sex,u.email,u.mysign from ebh_users u where u.username = \''.$this->db->escape_str($username).'\'';
		return $this->db->query($sql)->row_array();
	}

	/*
	qq,sina, wx 登录
	*/
	public function openlogin ($opcode,$type,$cookietime=0) {
		if(empty($opcode))
			return FALSE;
		if($type=='sina'){
			$sql = "SELECT uid,username,password FROM ebh_users  WHERE sinaopid='$opcode'";	
		}elseif($type=='wx'){
			$sql = "SELECT uid,username,password FROM ebh_users  WHERE wxunionid='$opcode'";
		}else{
			$sql = "SELECT uid,username,password FROM ebh_users  WHERE qqopid='$opcode'";
		}
		$data = $this->db->query($sql)->row_array();
		if($data){
			return $this->login($data['username'], $data['password'] ,true);	
		}else{
			return false;
		}
	}
	
	/*
	账号关联信息
	*/
	public function getAssociateInfoByUsername($username){
		if(empty($username))
			return FALSE;
		$sql = 'select uid,password,qqopid,sinaopid,wxopid,wxopenid,wxunionid from ebh_users where username=\''.$this->db->escape_str($username).'\'';//echo $sql;
		return $this->db->query($sql)->row_array();
	}
	
	
	/*
	根据邮箱查询用户
	*/
	public function getUserByEmail($email) {
		$sql = 'select uid,username from ebh_users u where u.email=\'' . $this->db->escape_str($email) . '\'';
		return $this->db->query($sql)->row_array();
	}
    /*
    添加会员
    @param array $param
    @return int
    */
    public function addmember($param){
        if(!empty($param['username']))
            $userarr['username'] = $param['username'];
        if(!empty($param['password']))
            $userarr['password'] = md5($param['password']);
        if(!empty($param['mpassword']))
            $userarr['password'] = $param['mpassword'];
        if(isset($param['realname']))
            $userarr['realname'] = $param['realname'];
        if(isset($param['nickname']))
            $userarr['nickname'] = $param['nickname'];
        if(!empty($param['dateline'])){
            $userarr['dateline'] = $param['dateline'];
        }else{
            $userarr['dateline'] = SYSTIME;
        }
        if(isset($param['sex']))
            $userarr['sex'] = $param['sex'];
        if(!empty($param['mobile']))
            $userarr['mobile'] = $param['mobile'];
        if(!empty($param['citycode']))
            $userarr['citycode'] = $param['citycode'];
        if(isset($param['address']))
            $userarr['address'] = $param['address'];
        if(!empty($param['email']))
            $userarr['email'] = $param['email'];
        if(!empty($param['face']))
            $userarr['face'] = $param['face'];
        if(!empty($param['qqopid']))
            $userarr['qqopid'] = $param['qqopid'];
        if(!empty($param['sinaopid']))
            $userarr['sinaopid'] = $param['sinaopid'];
        //微信相关的
        if(!empty($param['wxopid']))
        	$userarr['wxopid'] = $param['wxopid'];
        if(!empty($param['wxopenid']))
        	$userarr['wxopenid'] = $param['wxopenid'];
        if(!empty($param['wxunionid']))
        	$userarr['wxunionid'] = $param['wxunionid'];
        
        $userarr['status'] = 1;
        $userarr['groupid'] = 6;
        // var_dump($userarr);
        $uid = $this->db->insert('ebh_users',$userarr);
        if($uid){
            $memberarr['memberid'] = $uid;
            if(isset($param['realname']))
                $memberarr['realname'] = $param['realname'];
            if(isset($param['nickname']))
                $memberarr['nickname'] = $param['nickname'];
            if(isset($param['sex']))
                $memberarr['sex'] = $param['sex'];
            if(!empty($param['birthdate']))
                $memberarr['birthdate'] = $param['birthdate'];
            if(!empty($param['phone']))
                $memberarr['phone'] = $param['phone'];
            if(!empty($param['mobile']))
                $memberarr['mobile'] = $param['mobile'];
            if(!empty($param['native']))
                $memberarr['native'] = $param['native'];
            if(!empty($param['citycode']))
                $memberarr['citycode'] = $param['citycode'];
            if(isset($param['address']))
                $memberarr['address'] = $param['address'];
            if(!empty($param['msn']))
                $memberarr['msn'] = $param['msn'];
            if(!empty($param['qq']))
                $memberarr['qq'] = $param['qq'];
            if(!empty($param['email']))
                $memberarr['email'] = $param['email'];
            if(!empty($param['face']))
                $memberarr['face'] = $param['face'];
            if(isset($param['profile']))
                $memberarr['profile'] = $param['profile'];
            $memberid = $this->db->insert('ebh_members',$memberarr);
            
        }
        return $uid;
    }
	/**
	*获取用户基本信息
	*/
	public function getUserInfo($uid) {
		$sql = 'select u.username,u.realname,u.nickname,u.sex,u.email,u.address,u.face,u.groupid,u.credit,u.balance,u.qqopid,u.wxopid,u.sinaopid,u.mysign,u.credit,m.qq,m.profile,m.birthdate as birthday from ebh_users u '.
				'join ebh_members m on (u.uid = m.memberid) '.
				' where u.uid='.$uid;
        $row = $this->db->query($sql)->row_array();
		return $row;
	}

    /**
     *根据用户uid查询用户信息(支持数组)
     *
     */
    public function getUserInfoByUid($uid){
        $uidArr = array();
        if(is_scalar($uid)){
            $uidArr = array($uid);
        }
        if(is_array($uid)){
            $uidArr = $uid;
        }
        $in = '('.implode(',',$uidArr).')';
        $sql = 'select uid,username,realname,face,sex,groupid from ebh_users where uid in '.$in;
        return $this->db->query($sql)->list_array();
    }

    /**
     *学生绑定微信
     */
    public function swxbind($param = array()){
        if(empty($param['uid']) || empty($param['wxopid'])){
            return array();
        }
        $user = $this->getUserByWxOpenid($param['wxopid']);
        if(!empty($user) && $user['uid'] == $param['uid']){
            return -1;//已经和本账号绑定
        }else if(!empty($user) && $user['uid']!=$param['uid']){
            return -2;//该微信号已经和别的ebh账号绑定
        }
        $setter = array(
            'wxopid'=>$param['wxopid']
        );
        $uid = $param['uid'];
        return $this->update($setter,$uid);
    }   

    /**
     *根据微信openid获取用户信息
     */
    public function getUserByWxOpenid($wxopid = 0){
        $sql = 'select u.* from ebh_users u where ( wxopid = \''.$wxopid.'\' ) OR (wxopenid = \''.$wxopid.'\' )  OR (wxunionid  = \''.$wxopid.'\')  limit 1';
        return $this->db->query($sql)->row_array(); 
    }
    /**
     *根据用户uid获取微信openid
     */
    public function getOpenidByUid($uid = 0){
        $sql = 'select u.wxopid from ebh_users u where u.uid = '.$uid.' limit 1';
        return $this->db->query($sql)->row_array();
    }

     /**
     * 用username和password判断登录(家长)
     * @param type $username
     * @param type $userpass
     * @param boolean $iscoding 是否加密过密码
     * @return boolean 返回用户信息数组
     */
    public function plogin($username,$userpass,$iscoding = FALSE) {
        $pwd = $iscoding ? $userpass : md5($userpass);
        $username = $this->db->escape($username);
        $sql = "select u.uid, u.username,u.realname,u.nickname,u.credit,u.face,u.sex,u.groupid, u.logincount,u.lastlogintime,u.lastloginip,u.password,u.ppassword,u.status from ebh_users u where u.username=$username";
        $user = $this->db->query($sql)->row_array();
        if(empty($user)){
            return false;
        }
       
        if(!empty($user['ppassword'])){
            if($user['ppassword'] == $pwd){
                return $user;
            }else{
                return false;
            }
        }

        if(!empty($user['password'])){
            if($user['password'] == $pwd){
                return $user;
            }else{
                return false;
            }
        }
        return false;
    }

    public function swxunbind($param = array()){
        if(empty($param) || empty($param['uid']) || !is_numeric($param['uid'])){
            return -1;
        }
        $uid = $param['uid'];
        $setarr = array('wxopid'=>'');
        $wherearr = array('uid'=>$uid);
        return $this->db->update('ebh_users',$setarr,$wherearr);
    }

    //用户登录的时候重新绑定微信账号
    public function wxrebind($uid = 0,$wxopid = ''){
        //1.解除所有绑定指定wxopid的账号绑定
        $wxopid = $this->db->escape_str($wxopid);
        $setarr = array('wxopid'=>'');
        $wherearr = array('wxopid'=>$wxopid);
        $this->db->update('ebh_users',$setarr,$wherearr);
        //2.将当前uid绑定到wxopeind
        $setarr = array('wxopid'=>$wxopid);
        $wherearr = array('uid'=>$uid);
        return $this->db->update('ebh_users',$setarr,$wherearr);
    }

    /**
     * 获取学生的班级ID
     * @param int $uid 学生ID
     * @param int $crid 网校ID
     * @return bool
     */
    public function getClassid($uid, $crid) {
        $sql = 'SELECT `a`.`classid` FROM `ebh_classstudents` `a` JOIN `ebh_classes` `b` ON `b`.`classid`=`a`.`classid` WHERE `a`.`uid`='.$uid.' AND `b`.`crid`='.$crid.' LIMIT 1';
        $ret = $this->db->query($sql)->row_array();
        if (empty($ret)) {
            return false;
        }
        return $ret['classid'];
    }
}
