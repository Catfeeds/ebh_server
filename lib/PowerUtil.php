<?php
/**
 *权限工具类,支持从一个包含fid的二维数组中，把用户权限注入进入,在原先的结果集上添加上对应的itemid,sid(itemid大于0表示没有权限)
 *ZKQ
 ************************************************************************************************************************************************
 *	1.从包含fid的二维数组中提取fid并且打包,主要用于课程封面的显示
 *		$test = array(
 *			array('fid'=>3084,'xx'=>'dd'),
 *			array('fid'=>3082,'xx'=>'dd'),
 *			array('fid'=>3081,'xx'=>'dd')
 *		);
 *		$uid = 383485;
 *		$res = Ebh::app()->lib('PermisionUtil')->init($test,$uid)->groupFolderByPackage($test);//打包成标准接口返回数据格式
 *	
 *		$res = Ebh::app()->lib('PermisionUtil')->init($test,$uid)->groupFolderByPackage($test,false);	//不打包成标准接口格式
 *		返回结果集(标准接口格式)：
 *		array(
 *			array('fid'=>0,'name'=>'高中服务包','xx'=>'dd','iteid'=>0,'sid'=>0),//服务包信息
 *			array('fid'=>3084,'name'=>'高一英语','xx'=>'dd','iteid'=>44,'sid'=>6),//无权限，要购买itemid为44的包
 *			array('fid'=>3082,'name'=>'高二英语','xx'=>'dd','iteid'=>2,'sid'=>1),
 *			array('fid'=>3081,'name'=>'高三物理','xx'=>'dd','iteid'=>0,'sid'=>0),//有权限无需购买
 * 		)
 *
 *********************************************************************************************************************************************************
 *	2.从包含fid的二维数组中提取fid并且打包,主要用于课件显示
 *	$test = array(
 *			array('fid'=>3084,'xx'=>'dd'),
 *			array('fid'=>3082,'xx'=>'dd'),
 *			array('fid'=>3081,'xx'=>'dd')
 *	);
 *  $uid = 383485;
 *  $res = Ebh::app()->lib('PermisionUtil')->init($test,$uid)->insertPower($test);
 *
 *
 *		返回结果集：
 *		array(
 *			array('fid'=>3084,'name'=>'课件1','xx'=>'dd','iteid'=>44,'sid'=>6),//无权限，要购买itemid为44的包
 *			array('fid'=>3082,'name'=>'课件2','xx'=>'dd','iteid'=>2,'sid'=>1),
 *			array('fid'=>3081,'name'=>'课件3','xx'=>'dd','iteid'=>0,'sid'=>0),//有权限无需购买
 * 		)
 *
 *
 */
class PowerUtil{
	public function __construct(){
		$this->db = Ebh::app()->getDb();
		$this->endtime = SYSTIME - 86400;
		$this->uid = 0;
		$this->crid = 0;
		$this->isschool = 0;
	}

	public function init($dataList = array(),$uid = 0){
		$this->dataList = $dataList;
		$this->uid = $uid;
		return $this;
	}

	public function setCrid($crid = 0){
		if(is_numeric($crid) && $crid >0){
			$this->crid = $crid;
			$sql = 'select isschool from ebh_classrooms where crid = '.$crid.' limit 1';
			$roominfo = $this->db->query($sql)->row_array();
			if(!empty($roominfo)){
				$this->isschool = $roominfo['isschool'];
			}else{
				$this->isschool = 0;
			}
		}else{
			$this->isschool = 0;
		}
		return $this;
	}

	//从数据库查询结果中注入权限信息
	public function insertPower(){
		return $this->_run();
	}

	//把一堆课程按照服务包分组
	public function groupFolderByPackage($dataList = array(),$format = true,$ifCatPower = true){
		if(empty($dataList)){
			return array();
		}
		if($this->isschool!=7){
			$dataList = $this->_insertAllFreePower($dataList,false);
			return $dataList;
		}
		$flist = $this->_groupFolderByPackage($dataList);
		if($format){
			if($ifCatPower){
				$flist = $this->_formatFolderList($flist);
			}else{
				$flist = $this->_formatFolderListWithPowerFilter($flist);
			}
		}
		return $flist;
	}

	//根据包含itemid的二维数组判断用户是否购买过对应服务(目前进行是否过期等权限判断)
	public function insertBuyInfoIntoItemidList($dataList = array()){
		if(empty($dataList)){
			return array();
		}
		if($this->isschool!=7){
			$dataList = $this->_insertAllFreePower($dataList,false);
			return $dataList;
		}
		$uid =  $this->uid;
		//1.根据itemid获取课程信息
		$itemid_in = $this->_getFieldArr($dataList,'itemid');
		$sql = 'select up.itemid from ebh_userpermisions up where up.itemid in ('.implode(',', $itemid_in).')'.' AND up.uid = '.$uid.' AND up.enddate>'.(SYSTIME-86400);
		$permisionList = $this->db->query($sql)->list_array();
		$itemid_in_permisionList = $this->_getFieldArr($permisionList,'itemid');

		foreach ($dataList as &$data) {
			if(in_array($data['itemid'], $itemid_in_permisionList)){
				$data['flag'] = 0;
			}else{
				$data['flag'] = 1;
			}
		}
		return $dataList;
	}

	private function _groupFolderByPackage($dataList){
		$this->dataList = $dataList;
		$defimgConf = Ebh::app()->getConfig()->load('defimg');
		$folder_default_face = $defimgConf['folder_face'];
		$folderList = $this->_insertPowerInFolder();
		foreach ($folderList as &$folder) {
			if(empty($folder['face'])){
				$folder['face'] = $folder_default_face;
			}
		}
		return $folderList;
	}

	//格式化服务包和课程,以兼容旧的接口
	private function _formatFolderList($packageList = array()){
		$ret = array();
		$ret_haspower = array();
		$ret_hasnopower = array();
		foreach ($packageList as $package) {
			if($package['status'] == 0){
				continue;
			}
			$flist = $package['flist'];
			if(empty($flist)){
				continue;
			}
			$hasPower = array();
			$hasNoPower = array();
			
			// $ret[] = array('fid'=>0,'name'=>$package['pname'],'face'=>'','num'=>0,'itemid'=>0,'sid'=>0,'viewnum'=>0,'tname'=>"",'summary'=>"",'grade'=>0,'district'=>0);
			$title = array('fid'=>0,'name'=>$package['pname'],'pid'=>$package['pid'],'face'=>'','num'=>0,'itemid'=>0,'iprice'=>0,'iday'=>0,'imonth'=>0,'sid'=>0,'viewnum'=>0,'tname'=>"",'summary'=>"",'grade'=>0,'district'=>0,'cannotpay'=>0);
			foreach ($flist as $folder) {
				$folder['cannotpay'] = !empty($folder['cannotpay'])?$folder['cannotpay']:0;
				if( ($folder['cannotpay']>0) && ($folder['itemid'] > 0) ){
					continue;
				}
				$tmp = array('fid'=>$folder['fid'],'name'=>$folder['name'],'face'=>$folder['face'],'num'=>$folder['num'],'itemid'=>$folder['itemid'],'iprice'=>$folder['iprice'],'iday'=>$folder['iday'],'imonth'=>$folder['imonth'],'sid'=>$folder['sid'],'viewnum'=>$folder['viewnum'],'tname'=>$folder['speaker'],'summary'=>$folder['summary'],'grade'=>$folder['grade'],'district'=>$folder['district'],'cannotpay'=>$folder['cannotpay'],'fprice'=>$folder['fprice']);
				if($folder['itemid'] > 0 && $folder['fprice']>0){
					$hasNoPower[] = $tmp;
				}else{
					$hasPower[] = $tmp;
				}
			}
			if(!empty($hasPower)){
				$ret_haspower[] = $title;
				$ret_haspower = array_merge($ret_haspower,$hasPower);
			}
			if(!empty($hasNoPower)){
				$ret_hasnopower[] = $title;
				$ret_hasnopower = array_merge($ret_hasnopower,$hasNoPower);
			}
			// $ret = array_merge($ret,$hasPower,$hasNoPower);
		}
		$ret = array_merge($ret_haspower,$ret_hasnopower);
		return $ret;
	}


	//不区分是否购买
	private function _formatFolderListWithPowerFilter($packageList = array()){
		$ret = array();
		foreach ($packageList as $package) {
			if($package['status'] == 0){
				continue;
			}
			$flist = $package['flist'];
			if(empty($flist)){
				continue;
			}
			$title = array('fid'=>0,'name'=>$package['pname'],'face'=>'','num'=>0,'itemid'=>0,'iprice'=>0,'iday'=>0,'imonth'=>0,'sid'=>0,'viewnum'=>0,'tname'=>"",'summary'=>"",'grade'=>0,'district'=>0,'cannotpay'=>0);
			$ret[] = $title;
			foreach ($flist as $folder) {
				$folder['cannotpay'] = !empty($folder['cannotpay'])?$folder['cannotpay']:0;
				if( ($folder['cannotpay']>0) && ($folder['itemid'] > 0) ){
					continue;
				}
				$tmp = array('fid'=>$folder['fid'],'name'=>$folder['name'],'face'=>$folder['face'],'num'=>$folder['num'],'itemid'=>$folder['itemid'],'iprice'=>$folder['iprice'],'iday'=>$folder['iday'],'imonth'=>$folder['imonth'],'sid'=>$folder['sid'],'viewnum'=>$folder['viewnum'],'tname'=>$folder['speaker'],'summary'=>$folder['summary'],'grade'=>$folder['grade'],'district'=>$folder['district'],'cannotpay'=>$folder['cannotpay'],'fprice'=>$folder['fprice']);
				$ret[] = $tmp;
			}
		}
		return $ret;
	}


	//将权限信息注入到课程中去
	private function _insertPowerInFolder(){
		$endtime = $this->endtime;
		$dataList = $this->dataList;
		if(empty($dataList)){
			return array();
		}
		$fid_in = $this->_getFieldArr($dataList,'fid');
		//根据fid_in获取所有课程信息
		$allfolderList = $this->_getfolderList($fid_in,'all',false);
		$allfolderList = $this->_insertAllFreePower($allfolderList);
		$allfolderList = $this->_modifyKeys($allfolderList,'fid','fo');

		//获取服务包和服务项信息
		$itemList = $this->_getItemListWithoutKey($fid_in,$this->crid);

		//权限判断,用最大权限截止时间判断课程是否有权限，如果课程在两个服务项里，只要购买了最牛逼的那个服务项（时长最长），则其他的低级服务项(服务时间短的)则不需要购买
		//根据课程fid_in获取用户购买信息
		$permisionList = $this->_getPermisionList($fid_in);

		$packageList = array();

		$folderid_in_item = array();
		foreach ($itemList as &$item) {
			$key_it = 'it_'.$item['fid'];
			$key_fo = 'fo_'.$item['fid'];
			$key_pe = 'pe_'.$item['fid'];
			$key_pp = 'p_'.$item['pid'];

			if(!array_key_exists($key_pp, $packageList)){
				$packageList[$key_pp] = array(
					'pname'=>$item['pname'],
					'status'=>$item['status'],
					'pid'=>$item['pid'],
					'displayorder'=>$item['displayorder'],
					'flist'=>array()
				);
			}
			if(!array_key_exists($key_pe, $permisionList)){//用户么有购买该课程
				if($allfolderList[$key_fo]['fprice'] > 0 ){//课程收费
					$folder = $allfolderList[$key_fo];
					$folder['itemid'] = $item['itemid'];
					$folder['iprice'] = $item['iprice'];
					$folder['iday'] = $item['iday'];
					$folder['imonth'] = $item['imonth'];
					$folder['cannotpay'] = $item['cannotpay'];
					$folder['sid'] = $item['sid'];
					$folder['summary'] = $item['isummary'];
					$packageList[$key_pp]['flist'][] = $folder;
					$folderid_in_item[] = $folder['fid'];
				}else{
					$folder = $allfolderList[$key_fo];
					$folder['itemid'] = 0;
					$folder['iprice'] = 0;
					$folder['iday'] = 0;
					$folder['imonth'] = 0;
					$folder['cannotpay'] = 0;
					$folder['sid'] = 0;
					$folder['cannotpay'] = $item['cannotpay'];
					$folder['summary'] = $item['isummary'];
					$packageList[$key_pp]['flist'][] = $folder;
					$folderid_in_item[] = $folder['fid'];
				}
			}else{//用户购买过该课程则判断是否过期和过去权限
				$maxenddate = $permisionList[$key_pe]['enddate'];
				if(!empty($item['limitdate'])){//判断过去权限
					if( ($endtime < $item['limitdate']) || ( ($maxenddate+86400) > $item['limitdate']) ){
						//有过去权限
						$folder = $allfolderList[$key_fo];
						$folder['itemid'] = 0;
						$folder['iprice'] = 0;
						$folder['iday'] = 0;
						$folder['imonth'] = 0;
						$folder['sid'] = 0;
						$folder['summary'] = $item['isummary'];
						$folder['cannotpay'] = $item['cannotpay'];
						$packageList[$key_pp]['flist'][] = $folder;
						$folderid_in_item[] = $folder['fid'];
					}else{
						//没有过去权限
						$folder = $allfolderList[$key_fo];
						$folder['itemid'] = $item['itemid'];
						$folder['iprice'] = $item['iprice'];
						$folder['iday'] = $item['iday'];
						$folder['imonth'] = $item['imonth'];
						$folder['sid'] = $item['sid'];
						$folder['summary'] = $item['isummary'];
						$folder['cannotpay'] = $item['cannotpay'];
						$packageList[$key_pp]['flist'][] = $folder;
						$folderid_in_item[] = $folder['fid'];	
					}
				}else{//判断过期权限
					if( ($maxenddate + 86400) > $endtime){
						//有权限
						$folder = $allfolderList[$key_fo];
						$folder['itemid'] = 0;
						$folder['iprice'] = 0;
						$folder['iday'] = 0;
						$folder['imonth'] = 0;
						$folder['sid'] = 0;
						$folder['summary'] = $item['isummary'];
						$folder['cannotpay'] = $item['cannotpay'];
						$packageList[$key_pp]['flist'][] = $folder;
						$folderid_in_item[] = $folder['fid'];
					}else{
						//没有权限
						$folder = $allfolderList[$key_fo];
						$folder['itemid'] = $item['itemid'];
						$folder['iprice'] = $item['iprice'];
						$folder['iday'] = $item['iday'];
						$folder['imonth'] = $item['imonth'];
						$folder['sid'] = $item['sid'];
						$folder['summary'] = $item['isummary'];
						$folder['cannotpay'] = $item['cannotpay'];
						$packageList[$key_pp]['flist'][] = $folder;
						$folderid_in_item[] = $folder['fid'];
					}
				}
			}
		}
		$folderid_in_item = array_unique($folderid_in_item);
		//下面我们要把不在服务项里的课程放进默认的服务包里面去
		// $default_package_name = "免费服务";
		// $default_package = array(
		// 	'pname'=>$default_package_name,
		// 	'pid'=>0,
		// 	'displayorder'=>-1,
		// 	'flist'=>array()
		// );
		// $count = 0;
		// foreach ($allfolderList as $folder) {
		// 	if(!in_array($folder['fid'], $folderid_in_item)){
		// 		$default_package['flist'][] = $folder;
		// 		$count++;
		// 	}
		// }
		// if($count > 0){
		// 	array_unshift($packageList, $default_package);
		// }
		return $packageList;
	}

	private function _run(){
		$endtime = $this->endtime;
		$dataList = $this->dataList;
		if(empty($dataList)){
			return array();
		}
		if($this->isschool!=7){
			$dataList = $this->_insertAllFreePower($dataList,false);
			return $dataList;
		}
		$dataList = $this->_insertAllFreePower($dataList);
		$fid_in = $this->_getFieldArr($dataList,'fid');
		//根据fid_in获取收费课程信息，主要用来判断课程是否免费
		$noFreefolderList = $this->_getfolderList($fid_in,'notFree');
		if(empty($noFreefolderList)){//课程全免费
			return $this->_insertAllFreePower($dataList);
		}
		$fid_in = $this->_getFieldArr($noFreefolderList,'fid');

		//根据收费课程的fid_in获取服务项信息
		$itemList = $this->_getItemList($fid_in);
		//根据收费课程fid_in获取用户购买信息
		$permisionList = $this->_getPermisionList($fid_in);

		$hasPower = array();
		$hasNoPower = array();
		//下面遍历要注入权限的数据
		foreach ($dataList as &$data) {
			//如果是免费课程
			$key_fo = 'fo_'.$data['fid'];
			$key_it = 'it_'.$data['fid'];
			$key_pe = 'pe_'.$data['fid'];

			if(!array_key_exists($key_fo, $noFreefolderList)){
				$data['itemid'] = 0;
				$data['sid'] = 0;
				$hasPower[] = $data;
			}else{
				//如果是收费课程，并且课程不在服务项里
				if(!array_key_exists($key_it, $itemList)){
					$data['itemid'] = 0;
					$data['sid'] = 0;
					$hasPower[] = $data;
				}else{
					//在服务项里，但是用户没有购买过
					if(!array_key_exists($key_pe, $permisionList)){
						$checkRes = $this->_checkPower($itemList[$key_it],$data,0);
					}else{
						$checkRes = $this->_checkPower($itemList[$key_it],$data,$permisionList[$key_pe]['enddate']);
					}
					if($checkRes['power']){
						$data['itemid'] = 0;
						$data['sid'] = 0;
						$hasPower[] = $data;
					}else{
						$data['itemid'] = $checkRes['itemid'];
						$data['sid'] = $checkRes['sid'];
						$hasNoPower[] = $data;
					}
				}
			}
		}
		//有权限的往前挤，么权限的往后扔
		return array_merge($hasPower,$hasNoPower);
	}

	//课程全免费
	private function _insertAllFreePower($dataList = array(),$ifIniDate = true){
		$endtime = $this->endtime;
		foreach ($dataList as &$data) {
			if(empty($data['dateline']) && $ifIniDate ){
				$data['dateline'] = $endtime;
			}
			$data['itemid'] = 0;
			$data['sid'] = 0;
			$data['iprice'] = 0;
			$data['imonth'] = 0;
			$data['iday'] = 0;
		}
		return $dataList;
	}

	private function _getfolderList($fid_in = array(),$type = 'all',$ifReturnAssoc = true){
		$sql = 'select f.folderid as fid,f.fprice,f.coursewarenum as num,f.foldername as name,f.img as face,f.uid,f.viewnum,f.summary,f.grade,f.district,f.speaker from ebh_folders f where f.folderid in ('.implode(',', $fid_in).')';
		if($type == 'free'){
			$sql .= ' AND f.fprice = 0';
		}else if($type == 'notFree'){
			$sql .= ' AND f.fprice > 0';
		}
		$folderList = $this->db->query($sql)->list_array();

		$defimgConf = Ebh::app()->getConfig()->load('defimg');
		$folder_default_face = $defimgConf['folder_face'];
		
		foreach ($folderList as &$folder) {
			if(empty($folder['face'])){
				$folder['face'] = $folder_default_face;
			}
		}

		if(!empty($ifReturnAssoc)){
			$folderList = $this->_modifyKeys($folderList,'fid','fo');
		}
		return $folderList;
	}

	private function _getItemList($fid_in = array()){
		$itemList = $this->_getItemListWithoutKey($fid_in);
		$newItemList = array();
		foreach ($itemList as $item) {
			$key = 'it_'.$item['fid'];
			if(!array_key_exists($key, $newItemList)){
				$newItemList[$key] = array();
			}
			$newItemList[$key][] = $item;
		}
		return $newItemList;
	}

	private function _getItemListWithoutKey($fid_in = array(),$crid = 0){
		if($crid >0){
			$sql = 'select pp.pid,pp.pname,pp.limitdate,pp.displayorder,pp.status,pi.itemid,pi.cannotpay,pi.sid,pi.folderid as fid,pi.iname,pi.isummary,pi.iprice,pi.iday,pi.imonth from ebh_pay_items pi join ebh_pay_packages pp on pi.pid = pp.pid where pi.folderid in ('.implode(',', $fid_in).') and pi.crid = '.$crid.' and pi.status=0 and pp.status=1 order by pp.displayorder asc';
		}else{
			$sql = 'select pp.pid,pp.pname,pp.limitdate,pp.displayorder,pp.status,pi.itemid,pi.cannotpay,pi.sid,pi.folderid as fid,pi.iname,pi.isummary,pi.iprice,pi.iday,pi.imonth from ebh_pay_items pi join ebh_pay_packages pp on pi.pid = pp.pid where pi.folderid in ('.implode(',', $fid_in).') and pi.status=0 and pp.status=1 order by pp.displayorder asc';
		}
		$itemList = $this->db->query($sql)->list_array();
		return $itemList;
	}

	private function _getPermisionList($fid_in = array(),$ifReturnAssoc = true){
		$uid = $this->uid;
		$sql = 'select up.itemid,up.folderid as fid,up.enddate from ebh_userpermisions up where up.folderid in ('.implode(',', $fid_in).')'.' AND up.uid ='.$uid;
		$permisionList = $this->db->query($sql)->list_array();
// log_message($sql);
		//获取统一课程最大到期时间(因为有可能统一课程在多个服务项里面)
		$new_permisionList = array();
		foreach ($permisionList as $permision) {
			$key = 'fo_'.$permision['fid'];
			if(!array_key_exists($key, $new_permisionList)){
				$new_permisionList[$key] = $permision;
			}else{
				if($new_permisionList[$key]['enddate'] < $permision['enddate']){
					$new_permisionList[$key] = $permision;
				}
			}
		}
		if(!empty($ifReturnAssoc)){
			$new_permisionList =  $this->_modifyKeys($new_permisionList,'fid','pe');
		}
		return $new_permisionList;
	}


	//权限检测,默认权限和往期权限同时判断,检测不通过的话返回最新发布的服务项信息,以引导用户购买
	private function _checkPower($itemList = array(),$data = array(),$maxenddate = 0){
		$endtime = $this->endtime;
		$returnArr = array('power'=>true,'sid'=>0,'itemid'=>0);
		foreach ($itemList as $item) {
			if(!empty($item['limitdate'])){
				if( (($item['limitdate'] - 86400) < $maxenddate) && ( ($item['limitdate'] + 86400) > $data['dateline'])){
					$returnArr['sid'] = 0;
					$returnArr['itemid'] = 0;
					return $returnArr;
				}else{
					//判断是否有往期权限
					if( ($data['dateline'] - 86400) < $item['limitdate']){
						$returnArr['sid'] = 0;
						$returnArr['itemid'] = 0;
						return $returnArr;
					}else{
						if($returnArr['itemid'] <= $item['itemid']){
							$returnArr['itemid'] = $item['itemid'];
							$returnArr['sid'] = $item['sid'];
						}
					}
				}
			}else if($endtime < $maxenddate){
				$returnArr['sid'] = 0;
				$returnArr['itemid'] = 0;
				return $returnArr;
			}else{
				if($returnArr['itemid'] <= $item['itemid']){
					$returnArr['itemid'] = $item['itemid'];
					$returnArr['sid'] = $item['sid'];
				}
			}
		}
		$returnArr['power'] = false;
		return $returnArr;
	}


	/**
	 *获取二维数组指定的字段集合
	 */
	private function _getFieldArr($param = array(),$filedName=''){
		
		$reuturnArr = array();

		if(empty($filedName)||empty($param)){
			return $reuturnArr;
		}

		foreach ($param as $value) {
			array_push($reuturnArr, $value[$filedName]);
		}
		return array_unique($reuturnArr);
	}

	/**
	 *将索引数组变成关联数组
	 */
	private function _modifyKeys($arrs = array(),$filedName,$prefix = ''){
		$returnArr = array();
		foreach ($arrs as $arr) {
			$key = $prefix.'_'.$arr[$filedName];
			$returnArr[$key] = $arr;
		}
		return $returnArr;
	}
}