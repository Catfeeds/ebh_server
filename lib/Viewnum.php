<?php
/*
人气+1处理
*/
class Viewnum{
	function addViewnum($type,$id){
		// $stime = microtime(true);
		$redis = Ebh::app()->getCache('cache_redis');
		$viewnum = $redis->hget($type.'viewnum',$id);
		$themodel = Ebh::app()->model($type);
        if(empty($viewnum)) {
			if($type == 'courseware'){
				$result = $themodel->getSimplecourseByCwid($id);
			}elseif($type == 'folder'){
				$result = $themodel->getfolderbyid($id);
			}else if($type == 'askquestion'){
				$result = $themodel->getviewnum($id);
			}
			$viewnum = $result['viewnum'];
			$redis->hset($type.'viewnum',$id,$viewnum);
			//ob_clean();
        }
		$viewnum++;
		$redis->hIncrBy($type.'viewnum',$id);
		if($viewnum%50 == 0){
			$themodel->setviewnum($id,$viewnum);
		}
		// $etime = microtime(true);
		// echo $etime - $stime;
	}
	function getViewnum($type,$id){
		$redis = Ebh::app()->getCache('cache_redis');
		$viewnum = $redis->hget($type.'viewnum',$id);
		return $viewnum;
	}
}
?>