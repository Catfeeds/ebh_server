<?php

/**
 * 学习记录控制器
 */
class ClogController extends CControl {
    public function index() {
		$loglist = array();
		$user = Ebh::app()->user->getloginuser();
		$crid = $this->input->post('rid');
		if(!empty($user) && is_numeric($crid) && $crid > 0) {
			$playmodel = $this->model('Playlog');
			$queryarr = array();
			$begindate = $this->input->post('begindate');
			if(!empty($begindate)) {
				$begintime = strtotime($begindate);
				if($begintime !== FALSE) {
					$endDate = $startDate + 86400;
					$queryarr['startDate'] = $begintime;
					$queryarr['endDate'] = $endDate;
				}
			}
			$enddate = $this->input->post('enddate');
			if(!empty($enddate)) {
				$endtime = strtotime($enddate);
				if($endtime !== FALSE) {
					$endtime = $endtime + 86400;
					$queryarr['endDate'] = $endtime;
				}
			}
			$queryarr['crid'] = $crid;
			$queryarr['uid'] = $user['uid'];
			$queryarr['totalflag'] = 0;
			$queryarr['limit'] = 1000;
			$list = $playmodel->getList($queryarr);
			if(!empty($list)) {
				foreach($list as $mylog) {
					$mylog['startdate'] = empty($mylog['startdate']) ? '': date('Y-m-d H:i:s',$mylog['startdate']);
					$mylog['lastdate'] = empty($mylog['lastdate']) ? '': date('Y-m-d H:i:s',$mylog['lastdate']);
					$cwurl = $mylog['cwurl'];
					$filetype = substr($cwurl, strpos($cwurl, '.') + 1); //文件类型
					$mylog['filetype'] = $filetype;
					if(!empty($mylog['ctime'])){
						$mylog['ctime'] = secondToStr($mylog['ctime']);
					}else{
						$mylog['ctime'] = "0秒";
					}
					if(!empty($mylog['ltime'])){
						$mylog['ltime'] = secondToStr($mylog['ltime']);
					}else{
						$mylog['ltime'] = "0秒";
					}
					unset($mylog['cwurl']);
					$loglist[] = $mylog;
				}
			}
		}
		echo json_encode($loglist);
	}
}