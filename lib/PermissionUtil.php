<?php
class PermissionUtil{
	public function __construct(){

	}
	/**
	 *获取学校信息
	 */
	private function getRoomInfo($crid = 0){
		return Ebh::app()->model('classroom')->getclassroomdetail($crid);
	}

	/**
	 *课程权限验证信息注入(注入itemid字段,用户有权限则itemid为0,否则大于0[表示没有权限，要收费啦])[仅针对isschool=7的学校],其它学校itemid一律为0
	 */
	public function premissionInsert($folderlist = array(),$crid = 0){
		$folders = $folderlist;
		$roominfo = $this->getRoomInfo($crid);
		if($roominfo['isschool'] == 7) {	//收费分成学校，则未开通或已过期的课程，就显示阴影和开通按钮
			$user = Ebh::app()->user->getloginuser();
			$userpermodel = Ebh::app()->model('Userpermission');
			if(!empty($user)){
				$myperparam = array('uid'=>$user['uid'],'crid'=>$roominfo['crid'],'filterdate'=>1);
				$myfolderlist = $userpermodel->getUserPayFolderList($myperparam);
			}else{
				$myfolderlist = array();
			}
			$roomfolderlist = $userpermodel->getPayItemByCrid($roominfo['crid']);
			$folderlist = array();
			foreach($folders as $myfolder) {
				$myfolder['haspower'] = 0;
				$myfolder['itemid'] = 0;
				if($myfolder['fprice'] == 0) {
					$myfolder['haspower'] = 1;
				}
				$folderlist[$myfolder['fid']] = $myfolder;
			}
			$ofolderidstr = '';	//如果有权限的课程没有在当前页的课程内，则需要单独加上
			foreach($myfolderlist as $myfolder1) {	//看看哪些有权限
				if(isset($folderlist[$myfolder1['fid']])) {
					$folderlist[$myfolder1['fid']]['haspower'] = 1;
				} else {
					if(empty($ofolderidstr)) {
						$ofolderidstr = $myfolder1['fid'];
					} else {
						$ofolderidstr = $ofolderidstr.','.$myfolder1['fid'];
					}
				}
			}
			if(!empty($ofolderidstr)) {
				$foldermodel = Ebh::app()->model('Folder');
				$oqueryarr = array('folderid'=>$ofolderidstr);
				$ofolderlist = $foldermodel->getfolderlist($oqueryarr);
				if(!empty($ofolderlist)) {
					foreach($ofolderlist as $ofolder) {
						$ofolder['haspower'] = 1;
						$folderlist[$ofolder['fid']] = $ofolder;
					}
				}
			}
			foreach($roomfolderlist as $myfolder2) {
				if(isset($folderlist[$myfolder2['fid']])) {
					if($folderlist[$myfolder2['fid']]['haspower'] == 0) {
						// $checkurl = 'http://'.$roominfo['domain'].'.'.$this->uri->curdomain.'/ibuy.html?itemid='.$myfolder2['itemid'];	//购买url
						$folderlist[$myfolder2['fid']]['itemid'] = intval($myfolder2['itemid']);
					}
				}
			}
			$folders = $folderlist;

		}
		$returnFolders = array();
		foreach ($folders as $folder) {
			unset($folder['fprice']);
			unset($folder['haspower']);
			if(!isset($folder['itemid'])){
				$folder['itemid'] = 0;
			}
			array_push($returnFolders, $folder);
		}
		return $returnFolders;
	}
}