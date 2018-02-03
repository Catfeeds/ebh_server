<?php
/*
年卡
*/
class YearcardModel extends CModel{
	/*
	年卡列表
	@param array $param
	@return array
	*/
	public function getyearcardlist($param){
		$wherearr = array();
		$sql = 'select y.cardid,y.cardnumber,y.status,y.time,y.dateline,y.period,ct.cityname,cr.crname 
			from ebh_yearcards y 
			left join ebh_cities ct on y.citycode = ct.citycode 
			left join ebh_classrooms cr on y.crid = cr.crid
			';
		if(!empty($param['q']))
			$wherearr[] = ' (y.cardnumber like \'%'. $this->db->escape_str($param['q']) .'%\')';
		if(!empty($param['crid']))
			$wherearr[] = 'cr.crid ='.intval($param['crid']);
		if(!empty($param['citycode'])){
			$wherearr[] = 'y.citycode like \''.intval($param['citycode']).'%\'';
		}
		if(!empty($wherearr))
			$sql.= ' WHERE '.implode(' AND ',$wherearr);
		$sql.=' order by y.cardid desc';
		if(!empty($param['limit']))
			$sql.= ' limit ' . $param['limit'];
		//var_dump($sql);
		return $this->db->query($sql)->list_array();
	}
	/*
	年卡数量
	@param array $param
	@return int
	*/
	public function getyearcardcount($param){
		$wherearr = array();
		$sql = 'select count(*) count
			from ebh_yearcards y 
			left join ebh_cities ct on y.citycode = ct.citycode 
			left join ebh_classrooms cr on y.crid = cr.crid
			';
		if(!empty($param['q']))
			$wherearr[] = ' (y.cardnumber like \'%'. $this->db->escape_str($param['q']) .'%\')';
		if(!empty($param['crid']))
			$wherearr[] = 'cr.crid ='.intval($param['crid']);
		if(!empty($param['citycode'])){
			$wherearr[] = 'y.citycode like \''.intval($param['citycode']).'%\'';
		}
		if(!empty($wherearr))
			$sql.= ' WHERE '.implode(' AND ',$wherearr);
		
		$count = $this->db->query($sql)->row_array();
		return $count['count'];
	}
	/**
	*根据卡号获取年卡信息
	*@param string $cardnumber 卡号
	*/
	public function getYearcardByCardnumber($cardnumber,$crid = 0) {
		$sql = 'select c.cardid,c.cardnumber,c.time,c.dateline,c.period,c.status,c.cardpass,c.activedate,c.crid from ebh_yearcards c';
		$sql .= ' where c.cardnumber='.$this->db->escape($cardnumber);
		if(!empty($crid)){
			$sql .= ' AND c.crid = '.$this->db->escape_str($crid);
		}
		return $this->db->query($sql)->row_array();
	}
	/**
	*更新年卡信息，一般为年卡激活时用
	*/
	public function update($param) {
		if(empty($param['cardid']))
			return FALSE;
		$wherearr = array('cardid'=>$param['cardid']);
		$setarr = array();
		if(isset($param['status'])) {
			$setarr['status'] = $param['status'];
		}
		if(isset($param['activedate']))
			$setarr['activedate'] = $param['activedate'];
		else if(isset($param['status']) && $param['status'] == 1) {
			$setarr['activedate'] = SYSTIME;
		}
		return $this->db->update('ebh_yearcards',$setarr,$wherearr);
	}
	/*
	删除
	@param int $cardid
	@return bool
	*/
	public function deleteyearcard($cardid){
		$sql = 'delete y.* from ebh_yearcards y where cardid='.$cardid;
		return $this->db->simple_query($sql);
	}
	/*
	添加年卡
	@param array $param
	*/
	public function addyearcard($param){
		$crno = '1'.substr($param['crid'],-3);

		$crid = intval($param['crid']);
		
		$sql = 'select y.cardnumber from ebh_yearcards y where crid = \''.$crid .'\' order by y.cardid desc limit 1';
		$newrow = $this->db->query($sql)->row_array();
		$cardnumber = '000001';
		$curnumber = 1;
		if($newrow){
			$curnumber=intval(substr($newrow['cardnumber'],strlen($crno)))+1;
			if(strlen($curnumber)!=6){
				$cardnumber=str_pad('', (6-strlen($curnumber)),'0').$curnumber;
			}
		}
		$cardarr['cardpass'] = $crno.$cardnumber;
		$cardarr['dateline'] = time();
		$cardarr['period'] = intval($cardarr['dateline'])+5*365*24*60*60;
		$cardarr['status'] = 0;
		$cardarr['crid'] = $param['crid'];
		$cardarr['citycode'] = '';
		$cardarr['time'] = $param['time'];
		for($i=0;$i<$param['num'];$i++){
			$cardpass = '';
			for($j=0;$j<12;$j++){
				if($j == 0) {
					$cardpass.=rand(1,9);
				} else {
					$cardpass.=rand(0,9);
				}
			}
			$cardarr['cardnumber'] = $cardpass;
			$this->db->insert('ebh_yearcards',$cardarr);
			$curnumber ++;
			if(strlen($curnumber)!=6){
				$cardnumber=str_pad('', (6-strlen($curnumber)),'0').$curnumber;
			}
			
			$cardarr['cardpass'] = $crno.$cardnumber;
		
		}
		
	}
	/*
	添加年卡
	@param array $param
	*/
	public function addyearcard_old($param){
		$crno = '1'.substr($param['crid'],-3);
		
		$sql = 'select y.cardnumber from ebh_yearcards y where cardnumber like \''.$crno .'%\' order by y.cardid desc limit 1';
		$newrow = $this->db->query($sql)->row_array();
		$cardnumber = '000001';
		$curnumber = 1;
		if($newrow){
			$curnumber=intval(substr($newrow['cardnumber'],strlen($crno)))+1;
			if(strlen($curnumber)!=6){
				$cardnumber=str_pad('', (6-strlen($curnumber)),'0').$curnumber;
			}
		}
		$cardarr['cardnumber'] = $crno.$cardnumber;
		$cardarr['dateline'] = time();
		$cardarr['period'] = intval($cardarr['dateline'])+5*365*24*60*60;
		$cardarr['status'] = 0;
		$cardarr['crid'] = $param['crid'];
		$cardarr['citycode'] = '';
		$cardarr['time'] = $param['time'];
		for($i=0;$i<$param['num'];$i++){
			$cardpass = '';
			for($j=0;$j<8;$j++){
				$cardpass.=rand(0,9);
			}
			$cardarr['cardpass'] = $cardpass;
			$this->db->insert('ebh_yearcards',$cardarr);
			$curnumber ++;
			if(strlen($curnumber)!=6){
				$cardnumber=str_pad('', (6-strlen($curnumber)),'0').$curnumber;
			}
			
			$cardarr['cardnumber'] = $crno.$cardnumber;
		
		}
		
	}
	/**
	*简单的sql执行方法
	*@param String  $sql
	*@return int (effects_rows)
	*/
	public function _query($sql){
		return $this->db->query($sql);
	}
}
?>