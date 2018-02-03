<?php
/**
 * 限制报名人数(课程,课程包)
 */
class OpenLimit{
	/*
	检查开通情况
	*/
	public function checkStatus(&$item){
		$fromobject = FALSE;
		if(is_object($item)){//obj的先转数组
			$fromobject = TRUE;
			$item = $this->object2array($item);
		}
		if(empty($item['bid']) && empty($item['itemid'])){
			return TRUE;
		}
		$idtype = empty($item['itemid'])?'bid':'itemid';
		$id = $item[$idtype];
		$user = Ebh::app()->user->getloginuser();
		$ocdata['crid'] = $item['crid'];
		$ocdata[$idtype] = $id;
		$ocdata['uid'] = $user['uid'];
		// $api = Ebh::app()->getApiServer('ebh');
		// $opencount = $api->reSetting()->setService('Classroom.Item.openCount')->addParams($ocdata)->request();
		$ordermodel = Ebh::app()->model('Payorder');
		$opencount = $ordermodel->getOpenCount($ocdata);
		
		$item['opencount'] = $opencount['opencount'] > $item['limitnum']?$item['limitnum']:$opencount['opencount'];
		$item['selfcount'] = empty($opencount['selfcount'])?0:$opencount['selfcount'];
		//如果人数达到上限,且曾经未开通,则不能开通
		$cantpay = $item['opencount'] == $item['limitnum'] && empty($item['selfcount']);
		if($fromobject){//从obj转过一次的,转回来
			$item = $this->array2object($item);
		}
		return !$cantpay;
	}
	//数组转对象
	private function array2object($array) {
		if (is_array($array)) {
			$obj = new StdClass();
			foreach ($array as $key => $val){
				$obj->$key = $val;
			}
		} else {
			$obj = $array; 
		}
		return $obj;
	}
	//对象转数组
	private function object2array($object) {
		if (is_object($object)) {
			foreach ($object as $key => $value) {
				$array[$key] = $value;
			}
		} else {
			$array = $object;
		}
		return $array;
	}
}