<?php
/**
 * 服务产品开通和充值控制器
 */
class IbuyController extends CControl {
	public function __construct(){
		parent::__construct();
		$user = Ebh::app()->user->getloginuser();
		if(empty($user)){
			$info = array(
				'status'=>-3,
				'msg'=>'用户没有登录'
			);
			echo json_encode($info);exit;
		}
		$this->user = $user;
	}
    public function index() {
		$user = $this->user;
		$itemid = $this->input->post('itemid');	//服务项编号
		$sid = $this->input->post('sid');	//服务包分类编号
		if((empty($itemid) || !is_numeric($itemid) || $itemid <= 0) && (empty($sid) || !is_numeric($sid) || $sid <= 0)) {
			$info = array(
				'status'=>-4,
				'msg'=>'参数不正确',
				'itemid'=>$itemid,
				'sid'=>$sid,
				'iname'=>"",
				'iprice'=>"0.00"
			);
			echo json_encode($info);
			exit();
		}
		if(!empty($user)) {
			$this->second();
		} else {	//必须先登录才能进行充值等操作
			$info = array(
				'status'=>-3,
				'msg'=>'用户没有登录',
				'itemid'=>$itemid,
				'sid'=>$sid,
				'iname'=>"",
				'iprice'=>"0.00"
			);
			echo json_encode($info);
            exit();
		}
		
    }
    public function bundleinfo() {
        $user = $this->user;
        if (empty($user)) {
            $info = array(
                'status'=>-3,
                'msg'=>'用户没有登录'
            );
            echo json_encode($info);
            exit();
        }
        $bid = $this->input->post('bid');
        $bundle = array();
        echo json_encode(array(
            'status' => 0,
            'bundle' => array(
                'bundlename' => '课程包名称',
                'price' => 100
            )
        ));
        exit();
    }
	/**
	*开通第二步，登录后的界面处理
	*/
	private function second() {
		$itemid = $this->input->post('itemid');
		$sid = $this->input->post('sid');
		$crid = intval($this->input->post('rid'));
		$param = array();
		if(!empty($sid))
			$param['sid'] = $sid;
		else if(!empty($itemid))
			$param['itemid'] = $itemid;{
		}
		$param['crid'] = $crid;
		$param['orderby'] = ' `f`.`displayorder` ASC,`i`.`itemid` DESC ';
		$user = $this->user;
		$pitemmodel = $this->model('PayItem');
		$itemlist = $pitemmodel->getItemBySidOrItemid($param);

        $listSorts = Ebh::app()->getConfig()->load('othersetting');
 		if(empty($sid) && !empty($itemlist[0]['sid']) && !empty($listSorts['list_sorts']) && in_array($crid, $listSorts['list_sorts'])){
 			$sid = $itemlist[0]['sid'];
 			$param2['sid'] = $sid;
 			$param2['orderby'] = ' `f`.`displayorder` ASC,`i`.`itemid` DESC ';
 			$itemlist = $pitemmodel->getItemBySidOrItemid($param2);
 		}
		$isschsource = FALSE;
		if (empty($itemlist) && !empty($crid)) {
			//查找公共课程
			$isschsource = TRUE;
			$itemlist = $pitemmodel->getSchCourse($itemid, $crid);
		}
		if(empty($itemlist)){
			$itemInfo = array('status'=>-2,'msg'=>'没有找到对应项目的信息');
			echo json_encode($itemInfo);exit;
		}

		//已开通课程列表
		$crid = 0;
		$crname = '';
		if(!empty($itemlist[0]['crid'])){
			$crid = $itemlist[0]['crid'];
			$crname = $itemlist[0]['crname'];
		}
		$itemlist = Ebh::app()->lib('PowerUtil')->init($itemlist,$user['uid'])->setCrid($crid)->insertBuyInfoIntoItemidList($itemlist);	
		$itemInfo = array();
		//过滤不能支付的服务项
        $itemlist = array_filter($itemlist, function($item) {
           return empty($item['cannotpay']);
        });
		foreach ($itemlist as $item) {
			$itime = !empty($item['iday'])?$item['iday'].'天':$item['imonth'].'月';
			$itemInfo[] = array(
				'itemid'=>$item['itemid'],
				'iname'=>$item['iname'],
				'itime'=>$itime,
				'iprice'=>$item['iprice'].'元',
				'flag'=>$item['flag'],
				'iprice_yh'=>$item['iprice_yh'],
				'limitnum'=>empty($item['limitnum'])?0:$item['limitnum'],
				'islimit'=>empty($item['islimit'])?0:$item['islimit'],
				'crid'=>$crid
			);
		}
		$data_package = array(
			'status'=>0,
			'itemList'=>$itemInfo,
			'crid'=>$crid,
			'crname'=>$crname,
			'isschsource'=>$isschsource
		);

		/*分配分类信息,用于分类购买:START*/
		$sortdetail = array();
		if(!empty($sid)){
			$sortmodel = $this->model('paysort');
			$sortdetail = $sortmodel->getSortdetail($sid);
			if(empty($sortdetail)){
				$sortdetail = array();
			}
		}
		$data_package['sortdetail'] = $sortdetail;
		/*分配分类信息,用于分类购买:END*/
		
		$data_package['balance'] = $user['balance'];//余额
		
		//是否有优惠码
		$othersetting = Ebh::app()->getConfig()->load('othersetting');
		$favourable = true;
		if (!empty($othersetting['dis_favourable'])) {
			$dis_favourable = $othersetting['dis_favourable'];
			unset($othersetting);
			if (is_array($dis_favourable) && !empty($roominfo) && in_array($crid, $dis_favourable)) {
				$favourable = false;
			}
		}
		$data_package['favourable'] = $favourable;
		$showcard = false;
		if(in_array($crid,array(10631,10515,12434,14283,13603,10622))) //支持激活卡的学校
			$showcard = true;
		$data_package['showcard'] = $showcard;
		echo json_encode($data_package);
		exit;
	}
	/**
	*生成订单信息
	*@param $payfrom 来源
	*/
	private function buildOrder($payfrom = 0,$couponcode = '') {
		$crid = intval($this->input->post('rid'));
		$user = $this->user;
		if(empty($user))
			return FALSE;
        $bid = intval($this->input->post('bid'));
        if ($bid > 0) {
            //生成课程包订单
            $roominfo = Ebh::app()->room->getcurroom($crid);
            if (empty($roominfo)) {
                return false;
            }
            return $this->buildOrder_bundle($payfrom, $bid, $roominfo);
        }
		$itemidlist = $this->input->post('itemid');
		if(empty($itemidlist))
			return FALSE;
		if(is_scalar($itemidlist)){
			$itemidlist = array($itemidlist);
		}
		foreach($itemidlist as $itemid) {	//详情编号必须都为正整数
			if(!is_numeric($itemid) || $itemid <= 0)
				return FALSE;
		}
		$itemidstr = implode(',',$itemidlist);
		$pitemmodel = $this->model('PayItem');
		$itemparam = array('itemidlist'=>$itemidstr, 'crid' => $crid);
		$itemlist = $pitemmodel->getItemList($itemparam);
		if (empty($itemlist)) {
			$itemid = reset($itemidlist);
			$itemlist = $pitemmodel->getSchCourseInfo($itemid, $crid);
		} else {
			//非企业选课的，查看开通限制
			if(count($itemlist) == 1 && !empty($itemlist[0]['islimit']) && $itemlist[0]['limitnum']>0){
				$openlimit = Ebh::app()->lib('OpenLimit');
				$openstatus = $openlimit->checkStatus($itemlist[0]);
				if(!$openstatus){//状态设置为无法报名
					return FALSE;
				}
			}
		}
		if(empty($itemlist))
			return FALSE;
		$payordermodel = $this->model('PayOrder');
		$orderparam = array();
		
		$orderparam['dateline'] = SYSTIME;
		$orderparam['ip'] = $this->input->getip();
		$orderparam['uid'] = $user['uid'];
		$orderparam['payfrom'] = $payfrom;
		$orderparam['couponcode'] = !empty($couponcode) ? $couponcode : ''; //优惠码
		$ordername = '';	//订单名称
		$remark = '';		//订单备注
		$totalfee = 0;
		$comfee = 0;	//公司分到总额
		$roomfee = 0;	//平台分到总额
		$providerfee = 0;	//内容提供商分到总额
        $is_schoolmate  = $this->model('Classroom')->checkstudent($user['uid'], $itemlist[0]['crid'], true);
		for($i = 0; $i < count($itemlist); $i ++) {
			//全校免费并且是该校学生，价格置0
			if ($itemlist[$i]['isschoolfree'] && $is_schoolmate == 1) {
                $itemlist[$i]['iprice'] = 0;
			}

			$itemlist[$i]['fee'] = $itemlist[$i]['iprice'];
			$itemlist[$i]['oname'] = $itemlist[$i]['iname'];
			$itemlist[$i]['omonth'] = $itemlist[$i]['imonth'];
			$itemlist[$i]['oday'] = $itemlist[$i]['iday'];
			$itemlist[$i]['osummary'] = $itemlist[$i]['isummary'];
			$itemlist[$i]['uid'] = $user['uid'];
			$itemlist[$i]['pid'] = $itemlist[$i]['pid'];
			$pid = $itemlist[$i]['pid'];
			$itemlist[$i]['rname'] = $itemlist[$i]['crname'];
			//如果该课程参加了优惠并且使用优惠券处理
			if($itemlist[$i]['isyouhui'] && !empty($couponcode)){
				$itemlist[$i]['fee'] = $itemlist[$i]['iprice_yh'];
				$itemlist[$i]['comfee'] = $itemlist[$i]['comfee_yh'];
				$itemlist[$i]['roomfee'] = $itemlist[$i]['roomfee_yh'];
				$itemlist[$i]['providerfee'] = $itemlist[$i]['providerfee_yh'];
				$totalfee += $itemlist[$i]['iprice_yh'];
			}else{
				$itemlist[$i]['comfee'] = $itemlist[$i]['comfee'];
				$itemlist[$i]['roomfee'] = $itemlist[$i]['roomfee'];
				$itemlist[$i]['providerfee'] = $itemlist[$i]['providerfee'];
				$totalfee += $itemlist[$i]['iprice'];
			}
			$comfee += $itemlist[$i]['comfee'];
			$roomfee += $itemlist[$i]['roomfee'];
			$providerfee += $itemlist[$i]['providerfee'];
			if(empty($ordername)) 
				$ordername = $itemlist[$i]['oname'];
			else
				$ordername .= ','.$itemlist[$i]['oname'];
			$theremark = $itemlist[$i]['iname'].'_'.(empty($itemlist[$i]['omonth']) ? $itemlist[$i]['oday'].' 天 _':$itemlist[$i]['omonth'].' 月 _').$itemlist[$i]['fee'].' 元';
			if(empty($remark)) {
				$remark = $theremark;
			} else {
				$remark .= '/'.$theremark;
			}
			$providercrid = $itemlist[$i]['providercrid'];
		}
		$orderparam['crid'] = $itemlist[0]['crid'];
		$orderparam['providercrid'] = $itemlist[0]['providercrid'];	//来源平台crid
		$orderparam['pid'] = $pid;
		$orderparam['itemlist'] = $itemlist;
		$orderparam['totalfee'] = $totalfee;
		$orderparam['comfee'] = $comfee;
		$orderparam['roomfee'] = $roomfee;
		$orderparam['providerfee'] = $providerfee;
		$orderparam['ordername'] = '开通 '.$ordername.' 服务';
		$orderparam['remark'] = $remark;

		//分销信息,目前只支持不捆绑销售的课程
        $sharekey = $this->input->post('sharekey');
        if (!empty($sharekey)) {
        	$shareInfo = $this->parse_sharekey($sharekey);
        }
        //判断是否分销，获取分销比例
		if (!empty($shareInfo[6]) && !empty($shareInfo[3]) && count($itemlist)==1 && $shareInfo[6]!=$user['uid']) {
			if ($shareInfo[1] != $itemlist[0]['itemid'] && $shareInfo[0]!='school') {//学校分销支持，所有单包
				exit;
			}
			$schoolShareInfo = $this->model('Classroom')->getShareInfo($shareInfo[3]);
			if (!empty($schoolShareInfo['isshare'])) {//开启的逻辑
				$userShare = $this->model('Share')->getUserSharePre($shareInfo[6],$shareInfo[3]);//比例
				$shareuid = $shareInfo[6];
				if (empty($userShare['percent'])) {//没有，用默认
					$sharepre = $schoolShareInfo['sharepercent'];
				} else {
					$sharepre = $userShare['percent'];
				}
			}
			
		}
		if (!empty($shareuid) && !empty($sharepre)) {
			$orderparam['isshare'] = 1;
			$orderparam['shareuid'] = $shareuid;
			$orderparam['sharefee'] = sprintf('%.2f',$orderparam['roomfee']*$sharepre/100);
			$orderparam['roomfee'] = sprintf('%.2f',$orderparam['roomfee']*(100-$sharepre)/100);
			foreach ($orderparam['itemlist'] as &$lvalue) {
				$lvalue['isshare'] = 1;
				$lvalue['shareuid'] = $shareuid;
				$lvalue['sharefee'] = sprintf('%.2f',$lvalue['roomfee']*$sharepre/100);
				$lvalue['roomfee'] = sprintf('%.2f',$lvalue['roomfee']*(100-$sharepre)/100);
			}
		}
		$orderid = $payordermodel->addOrder($orderparam);
		if($orderid > 0) {
			$orderparam['orderid'] = $orderid;
			return $orderparam;
		}	
		return $orderparam;
	}

	/**
	*选择支付宝充值操作
	*/
	public function alipay() {
		$myorder = $this->buildOrder(3);
		if(empty($myorder)) {
			json_encode(array('status'=>-1,'msg'=>'订单生产失败！'));
			exit();
		}

		$domain = $myorder['itemlist'][0]['domain'];

		$notify_url = 'http://www.ebh.net/ibuy/aliwapnotify.html';
        //页面跳转同步通知页面路径
        $return_url = 'http://www.ebh.net/ibuy/aliwapreturn.html';

        //必填
        //商户订单号
        $out_trade_no = $myorder['orderid'];
        //商户网站订单系统中唯一订单号，必填
        //订单名称
        $subject = $myorder['ordername'];
        //必填
        //付款金额
		$total_fee = $myorder['totalfee'];
        //必填
        //订单描述
        $body = $myorder['remark'];
        //商品展示地址
        $show_url = 'http://wap.ebh.net';
        //支付请求是否来自html5手机
        $client = intval($this->input->post('client'));

		$param = array('notify_url'=>$notify_url,'return_url'=>$return_url,'trade_no'=>$out_trade_no,'subject'=>$subject,'total_fee'=>$total_fee,'body'=>$body,'show_url'=>$show_url,'status'=>0,'client'=>$client);
		if(!empty($client)){
			$alilib = Ebh::app()->lib('Alipaywap');
		}else{
			$param['notify_url'] = 'http://www.ebh.net/ibuy/aliappnotify.html';
			$param['return_url'] = '';
			$alilib = Ebh::app()->lib('AlipayForApp');
		}
		//提交支付宝
		$alilib->alipayTo($param);
	}

	/**
	*选择微信充值操作(非公众账号支付接口)
	*/
	public function weixinpay() {
		$user = $this->user;
		$myorder = $this->buildOrder(9);
		if(empty($myorder)) {
			json_encode(array('status'=>-1,'msg'=>'订单生产失败！'));
			exit();
		}
		$domain = $myorder['itemlist'][0]['domain'];
        //必填
        //商户订单号
        $out_trade_no = $myorder['orderid'];
        //商户网站订单系统中唯一订单号，必填
        //订单名称
        $subject = $myorder['ordername'];
        $subject = shortstr($subject,80,'');
        //必填
        //付款金额
		$total_fee = $myorder['totalfee'] * 100; //微信接口要求total_fee必须为int类型
        //必填
        //订单描述
        $body = $myorder['remark'];
        $body = shortstr($body,80,'');
        //商品展示地址
        $show_url = 'http://'.$domain.'.'.$this->uri->curdomain;
		$param = array('out_trade_no'=>$out_trade_no,'subject'=>$subject,'total_fee'=>$total_fee,'body'=>$body,'show_url'=>$show_url);
		//返回订单参数用于APP对微信服务器做出支付订单生成请求
		$weixinlib = Ebh::app()->lib('Weixinpay');
		$param = $weixinlib->doOrder($param);
		if(!empty($param) && $param['retcode'] === 0){
			$param['status'] = 0;
		}else{
			$param['status'] = 1;
		}
		$param['method'] = 'pay';
		echo json_encode($param);
	}

    /**
     * @describe:微信h5支付
     * @User:tzq
     */
	public function weixinh5pay() {

		$myorder = $this->buildOrder(9);
		if(empty($myorder)) {
			json_encode(array('status'=>-1,'msg'=>'订单生产失败！'));
			exit();
		}
		$domain = $myorder['itemlist'][0]['domain'];
        //必填
        //商户订单号
        $out_trade_no = $myorder['orderid'];
        //商户网站订单系统中唯一订单号，必填
        //订单名称
        $subject = $myorder['ordername'];
        $subject = shortstr($subject,80,'');
        //必填
        //付款金额
		$total_fee = $myorder['totalfee'] * 100; //微信接口要求total_fee必须为int类型
        //必填
        //订单描述
        $body = $myorder['remark'];
        $body = shortstr($body,80,'');
        //商品展示地址
        $show_url = 'http://'.$domain.'.'.$this->uri->curdomain;
        //场景信息
        $scene_info = array('h5_info'=>array('type'=>'Wap','wap_url'=>$show_url,'wap_name'=>$this->input->post('wap_name')));

        //交易类型
        $trade_type = 'MWEB';

		$param = array('out_trade_no'=>$out_trade_no,'subject'=>$subject,'total_fee'=>$total_fee,'body'=>$body,'show_url'=>$show_url,'scene_info'=>$scene_info,'trade_type'=>$trade_type,'spbill_create_ip'=>$this->input->post('realip'));
		//返回订单参数用于APP对微信服务器做出支付订单生成请求
		$weixinlib = Ebh::app()->lib('Weixinpay');
		$param = $weixinlib->doH5Pay($param);
		//判断返回结果
		if(isset($param['return_code']) && $param['return_code'] === 'SUCCESS' && isset($param['result_code']) && $param['result_code'] == 'SUCCESS'){
			$param['status'] = 0;
			$param['orderid'] = $myorder['orderid'];
		}else{
			$param['status'] = 1;
			$param['msg'] = isset($param['return_msg'])?$param['return_msg']:'服务器繁忙,请稍后再试！';
		}
		$param['method'] = 'pay';
		echo json_encode($param);
	}

	/**
	*选择微信充值操作(公众账号支付接口)
	*/
	public function wxpublicpay(){
		$user = $this->user;
		$wxopid = $this->input->post('wxopenid');
		if(empty($wxopid)){
			echo json_encode(array('status'=>-4,'msg'=>'无法取得openid'));
			exit;
		}
		$couponcode = trim($this->input->post('couponcode'));
		$iscoupon = false;
		if(!empty($couponcode)){
			$couponModel = $this->model('Coupons');
			$coupon = $couponModel->getOne(array('code'=>$couponcode));
			if(!empty($coupon) && ($coupon['uid'] != $user['uid'])){
				$iscoupon = true;
			}
		}
		$couponcode = $iscoupon ? $couponcode : '';
		$myorder = $this->buildOrder(9,$couponcode);
		if(empty($myorder)) {
			json_encode(array('status'=>-1,'msg'=>'网站订单生成失败！'));
			exit();
		}
		$domain = $myorder['itemlist'][0]['domain'];
        //必填
        //商户订单号
        $out_trade_no = $myorder['orderid'];
        //商户网站订单系统中唯一订单号，必填
        //订单名称
        $subject = $myorder['ordername'];
        $subject = shortstr($subject,80,'');
        //必填
        //付款金额
		$total_fee = $myorder['totalfee'] * 100; //微信接口要求total_fee必须为int类型
        //必填
        //订单描述
        $body = $myorder['remark'];
        $body = shortstr($body,80,'');
        //商品展示地址
        $show_url = 'http://wap.ebh.net';
		$param = array('out_trade_no'=>$out_trade_no,'subject'=>$subject,'total_fee'=>$total_fee,'body'=>$body,'show_url'=>$show_url,'wxopid'=>$wxopid);
		//返回订单参数用于APP对微信服务器做出支付订单生成请求
		$weixinlib = Ebh::app()->lib('WxPublicPay');
		$res = $weixinlib->alipayTo($param);
		if($res == -5){
			echo json_encode(array('status'=>-5,'msg'=>'微信订单生成失败,请稍后再试！'));
			exit();
		}
		$ret = array(
			'status'=>0,
			'data'=>$res
		);
		echo json_encode($ret);
	}

	

	/**
	*通过账户余额支付
	*/
	public function bpay() {
		$result = array('status'=>1);
		$user = $this->user;
		$totalfee = floatval($this->input->post('totalfee'));
		if($user['balance'] < $totalfee) {	//对生成订单前做一次余额是否充足判断
			$result['msg'] = '余额不足';
			echo json_encode($result);
			exit();
		}
		$couponcode = trim($this->input->post('couponcode'));
		$iscoupon = false;
		if(!empty($couponcode)){
			$couponModel = $this->model('Coupons');
			$coupon = $couponModel->getOne(array('code'=>$couponcode));
			if(!empty($coupon) && ($coupon['uid'] != $user['uid'])){
				$iscoupon = true;
			}
		}
		$couponcode = $iscoupon ? $couponcode : '';
		$myorder = $this->buildOrder(8,$couponcode);	//生成订单，8为余额支付
		if(empty($myorder)) {	//订单生成失败
			$result['msg'] = '订单生成失败';
			echo json_encode($result);
			exit();
		}
		
		if($user['balance'] < $myorder['totalfee']) {	//生成订单后再做一次余额是否充足判断，避免 post totalfee造假
			$result['msg'] = '余额不足';
			echo json_encode($result);
			exit();
		}
		//处理权限
		$doresult = $this->notifyBOrder($myorder);
		if(empty($doresult)) {
			$result['msg'] = '开通失败';
			echo json_encode($result);
			exit();
		}
		//开通成功，则进行扣费操作
		$usermodel = $this->model('User');
		$sharekey = $this->input->post('sharekey');
		//分销购买自己的分销产品，钱包重新获取,暂时关闭
		/*if ($sharekey) {
			$shareInfo = $this->parse_sharekey($sharekey);
			if (!empty($shareInfo[6]) && $shareInfo[6] == $user['uid']) {
				$userInfo = $usermodel->getuserbyuid($user['uid']);
				$user['balance'] = $userInfo['balance'];
			}
		}*/
		$ubalance = $user['balance'] - $myorder['totalfee'];
		$uparam = array('balance'=>$ubalance);
		$uresult = $usermodel->update($uparam,$user['uid']);
		$result['status'] = 0;
		$credit = $this->model('credit');
		$credit->addCreditlog(array('ruleid'=>23,'detail'=>$myorder['itemlist'][0]['oname']));
		echo json_encode($result);
	}

	/**
	*处理余额支付的订单状态和学生权限
	*/
	private function notifyBOrder($myorder) {
		Ebh::app()->getDb()->set_con(0);
		//商户订单号
		$orderid = $myorder['orderid'];
		if(!is_numeric($orderid) || $orderid <=0){
			return FALSE;
		}

		//交易号
		$pordermodel = $this->model('Payorder');
		$myorder = $pordermodel->getOrderById($orderid);
		//处理订单详情中的内容
		if(empty($myorder['detaillist'])) {
			return FALSE;
		}

		$providercrids = array();
		foreach($myorder['detaillist'] as $detail) {
			$detail['uid'] = $myorder['uid'];
			$detail['crid'] = $myorder['crid'];
			$this->doOrderItem($detail);
			$detailprovidercrid = $detail['providercrid'];
			if(!empty($detailprovidercrid) && !isset($providercrids[$detailprovidercrid]))
				$providercrids[$detailprovidercrid] = $detailprovidercrid;
		}
		//更新订单状态
		$myorder['status'] = 1;
		$myorder['payip'] = $this->input->getip();
		$myorder['paytime'] = SYSTIME;
		$providercount = count($providercrids);
		if($providercount > 1) {
			for ($i = 0; $i < count($myorder['detaillist']); $i ++) {
				if($i == 0) {
					$myorder['providercrid'] = $myorder['detaillist'][$i]['providercrid'];
					$myorder['totalfee'] = $myorder['detaillist'][$i]['fee'];
					$myorder['comfee'] = $myorder['detaillist'][$i]['comfee'];
					$myorder['roomfee'] = $myorder['detaillist'][$i]['roomfee'];
					$myorder['providerfee'] = $myorder['detaillist'][$i]['providerfee'];
					$myorder['ordername'] = '开通 '.$myorder['detaillist'][$i]['oname'].' 服务';
					$myorder['remark'] = $myorder['detaillist'][$i]['oname'].'_'.(empty($myorder['detaillist'][$i]['omonth']) ? $myorder['detaillist'][$i]['oday'].' 天 _':$myorder['detaillist'][$i]['omonth'].' 月 _').$myorder['detaillist'][$i]['fee'].' 元';
				} else {
					$neworder = $myorder;
					$neworder['providercrid'] = $myorder['detaillist'][$i]['providercrid'];
					$neworder['totalfee'] = $myorder['detaillist'][$i]['fee'];
					$neworder['comfee'] = $myorder['detaillist'][$i]['comfee'];
					$neworder['roomfee'] = $myorder['detaillist'][$i]['roomfee'];
					$neworder['providerfee'] = $myorder['detaillist'][$i]['providerfee'];
					$neworder['ordername'] = '开通 '.$myorder['detaillist'][$i]['oname'].' 服务';
					$neworder['remark'] = $myorder['detaillist'][$i]['oname'].'_'.(empty($myorder['detaillist'][$i]['omonth']) ? $myorder['detaillist'][$i]['oday'].' 天 _':$myorder['detaillist'][$i]['omonth'].' 月 _').$myorder['detaillist'][$i]['fee'].' 元';
					$neworderid = $pordermodel->addOrder($neworder,TRUE);
					$myorder['detaillist'][$i]['orderid'] = $neworderid;
				}
			}
		}
		$myorder['itemlist'] = $myorder['detaillist'];
		$uresult = $pordermodel->updateOrder($myorder);

		//使用优惠券后返利处理
		$userModel = $this->model('User');  
		$tmpuser = $userModel->getUserInfoByUid($myorder['uid']);
		if(empty($tmpuser)){
			log_message('订单关联用户uid:'.$myorder['uid'].'获取失败');	
		}
		$user = $tmpuser[0];
		$couponModel = $this->model('Coupons');
		if(!empty($myorder['couponcode'])){
			$reward = 0;
			$ip = $this->input->getip();
			$cashbackModel = $this->model('Cashback');
			$coupon = $couponModel->getOne(array('code'=>$myorder['couponcode']));
			//优惠码可用
			if(!empty($coupon) && $coupon['uid'] != $user['uid']){
				foreach ($myorder['itemlist'] as $item){
					$reward = $item['fee'] - $item['comfee'] - $item['roomfee'] - $item['providerfee'];
					if($reward<=0){
						continue;
					}
					$cparam['uid'] = $coupon['uid'];
					$cparam['fromcrid'] = $item['crid'];
					$cparam['crname'] = $item['rname'];
					$cparam['fromuid'] = $user['uid'];
					$cparam['fromname'] = !empty($user['realname']) ? $user['realname'] : $user['username'];
					$cparam['servicestxt'] = '开通&nbsp;'.$item['oname'];
					$cparam['reward'] = $reward;
					$cparam['fromip'] = $ip;
					$cparam['time'] = SYSTIME;
					//依次加入记录至返现记录表
					$ret = $cashbackModel->add($cparam);
					if(!$ret){
						log_message('开通&nbsp;'.$item['oname'].'&nbsp;&nbsp;返利失败,关联uid:'.$cparam['uid']);
					}
				}
			}
		}
		//生成属于自己的优惠码
		$mycoupon = $couponModel->getOne(array('uid'=>$user['uid']));
		if(empty($mycoupon)){
			$couponarr['uid'] = $user['uid'];
			$couponarr['code'] = $this->getcouponcode();
			$couponarr['createtime'] = SYSTIME;
			$couponarr['orderid'] = $orderid;
			$couponarr['crid'] = $myorder['crid'];
			$myret = $couponModel->add($couponarr);
			if(!$myret){
				log_message('生成优惠码失败,关联uid:'.$couponarr['uid']);
			}
		}

		//处理分销返利情况
		if (!empty($myorder['isshare']) && !empty($myorder['sharefee']) && !empty($myorder['shareuid'])) {
			$myorder['sharedetail'] = empty($user['realname'])?$user['username']:$user['realname'].' '.$myorder['ordername'].'  价格: <em>'.$myorder['totalfee'].'</em>';
			$res = $this->model('Share')->addCharge($myorder);
			if (empty($res)) {
				log_message('分销失败,关联uid:'.$myorder['shareuid']);
			}
		}
		
		return $myorder;
	}

	/**
	*支付成功后处理订单详情（主要为生成权限）
	*/
	private function doOrderItem($orderdetail) {
		$crid = $orderdetail['crid'];
		$folderid = $orderdetail['folderid'];
		$uid = $orderdetail['uid'];
		$omonth= $orderdetail['omonth']; 
		$oday= $orderdetail['oday'];
		$roommodel = $this->model('Classroom');
		$roominfo = $roommodel->getRoomByCrid($crid);
		if(empty($roominfo)) {
            return FALSE;
        }
		$usermodel = $this->model('User');
		$user = $usermodel->getuserbyuid($uid);
		if(empty($user)) {
            return FALSE;
        }

		//获取用户是否在此平台
		$rumodel = $this->model('Roomuser');
		$ruser = $rumodel->getroomuserdetail($crid,$uid);
		$type = 0;
		if(empty($ruser)) {	//不存在 
			$enddate = 0;
			if(!empty($crid)) {
				if(!empty($omonth)) {
					$enddate = strtotime("+$omonth month");
				} else {
					$enddate = strtotime("+$oday day");
				}
			}
			$param = array('crid'=>$crid,'uid'=>$user['uid'],'begindate'=>SYSTIME,'enddate'=>$enddate,'cnname'=>$user['realname'],'sex'=>$user['sex']);
			$result = $rumodel->insert($param);
			$type = 1;
			if($result !== FALSE) {
				if($roominfo['isschool'] == 6 || $roominfo['isschool'] == 7) {	//如果是收费学校，则会将账号默认添加到学校的第一个班级中
					$this->setmyclass($crid,$user['uid'],$folderid);
				} else {
					//更新教室学生数
					$roommodel->addstunum($crid);
				}
			}
		} else {	//已存在
			if($roominfo['isschool'] == 6 || $roominfo['isschool'] == 7){
				$this->setmyclass($roominfo['crid'],$user['uid'],$folderid);//防止中途改变学校类型,导致学生在学校里面但是不在班级里面(网校改成学校) zkq 2014.07.22
			}
			$enddate=$ruser['enddate'];
			$newenddate=0;
			if(!empty($crid)) {
				if(!empty($omonth)) {
					if(SYSTIME>$enddate){//已过期的处理
						$newenddate=strtotime("+$omonth month");
					}else{	//未过期，则直接在结束时间后加上此时间
						$newenddate=strtotime( date('Y-m-d H:i:s',$enddate)." +$omonth month");
					}
				}else {
					if(SYSTIME>$enddate){//已过期的处理
						$newenddate=strtotime("+$oday day");
					}else{	//未过期，则直接在结束时间后加上此时间
						$newenddate=strtotime( date('Y-m-d H:i:s',$enddate)." +$oday day");
					}
				}
			}
			$param = array('crid'=>$crid,'uid'=>$user['uid'],'enddate'=>$newenddate,'cstatus'=>1);
			$result = $rumodel->update($param);
			$type = 2;
		}

		//处理用户权限
		$userpmodel = $this->model('UserPermission');
		if(empty($orderdetail['folderid'])) {
			$myperm = $userpmodel->getPermissionByItemId($orderdetail['itemid'],$uid);
		} else {
			$myperm = $userpmodel->getPermissionByFolderId($orderdetail['folderid'],$uid);
		}
		$startdate = 0;
		$enddate = 0;
		if(empty($myperm)) {	//不存在则添加权限，否则更新
			$startdate = SYSTIME;
			if(!empty($omonth)) {
				$enddate = strtotime("+$omonth month");
			} else {
				$enddate = strtotime("+$oday day");
			}
			$ptype = 0;
			if(!empty($folderid) || !empty($crid)) {
				$ptype = 1;
			}
			$perparam = array('itemid'=>$orderdetail['itemid'],'type'=>$ptype,'uid'=>$uid,'crid'=>$crid,'folderid'=>$folderid,'startdate'=>$startdate,'enddate'=>$enddate);
			$result = $userpmodel->addPermission($perparam);
		} else {
			$enddate=$myperm['enddate'];
			$newenddate=0;
			if(!empty($omonth)) {
				if(SYSTIME>$enddate){//已过期的处理
					$newenddate=strtotime("+$omonth month");
				}else{	//未过期，则直接在结束时间后加上此时间
					$newenddate=strtotime( date('Y-m-d H:i:s',$enddate)." +$omonth month");
				}
			}else {
				if(SYSTIME>$enddate){//已过期的处理
					$newenddate=strtotime("+$oday day");
				}else{	//未过期，则直接在结束时间后加上此时间
					$newenddate=strtotime( date('Y-m-d H:i:s',$enddate)." +$oday day");
				}
			}
			$enddate = $newenddate;
			$myperm['enddate'] = $enddate;
			if(!empty($orderdetail['itemid'])) {
				$myperm['itemid'] = $orderdetail['itemid'];
			}
			$result = $userpmodel->updatePermission($myperm);
		}
		//用户平台信息更新成功则生成记录并更新年卡信息
		return $result;
	}

	/**
	*设置用户的默认班级信息
	* 一般为收费学校用户开通学校服务时候处理，需要将学生加入到默认的班级中
	* 如果不存在新班级，则需要创建一个默认班级
	*/
	private function setmyclass($crid,$uid,$folderid) {
		$classmodel = $this->model('Classes');
		//先判断是否已经加入班级，已经加入则无需重新加入
		$noreturn = FALSE;//不返回默认班级
		$myclass = $classmodel->getClassByUid($crid,$uid,$noreturn);
		if(empty($myclass)) {
			//获取课程对应的年级和地区信息
			$grade = 0;
			$district = 0;
			$folderInfo = $this->model('folder')->getfolderbyid($folderid);
			$classname = "默认班级";
			if(!empty($folderInfo)){
				$grade = $folderInfo['grade'];
				$district = $folderInfo['district'];
				$grademap = Ebh::app()->getConfig()->load('grademap');
				if(array_key_exists($grade, $grademap)){
					$classname = $grademap[$grade].'默认班级';
				}
			}
			$classid = 0;
			$defaultclass = $classmodel->getDefaultClass($crid,$grade,$district);
			if(empty($defaultclass)) {	//不存在默认班级，则创建默认班级
				$param = array('crid'=>$crid,'classname'=>$classname,'grade'=>$grade,'district'=>$district);
				$classid = $classmodel->addclass($param);
			} else {
				$classid = $defaultclass['classid'];
			}
			$param = array('crid'=>$crid,'classid'=>$classid,'uid'=>$uid);
			$classmodel->addclassstudent($param);
		}
	}
	/***微信扫码支付逻辑开始***/
	//微信扫码订单生成
	public function wxnativepay(){
		$user = $this->user;
		$myorder = $this->buildOrder(9);
		if(empty($myorder)) {
			json_encode(array('status'=>-1,'msg'=>'网站订单生成失败！'));
			exit();
		}
		$attach = md5($user['uid'].'_'.$myorder['orderid']);
        //商户订单号
        $out_trade_no = $myorder['orderid'];
        //订单名称
        $subject = $myorder['ordername'];
        $subject = shortstr($subject,80,'');
        //付款金额
		$total_fee = $myorder['totalfee']*100;
        //订单描述
        $body = $myorder['remark'];
        $body = shortstr($body,80,'');
        $notify_url = 'http://www.ebh.net/ibuy/wxnativenotify.html';
		$param = array('out_trade_no'=>$out_trade_no,'subject'=>$subject,'total_fee'=>$total_fee,'body'=>$body,'notify_url'=>$notify_url,'attach'=>$attach);
		$weixinlib = Ebh::app()->lib('WxNativePay');
		$res = $weixinlib->alipayTo($param);
		if(!empty($res)){
			$res['orderid'] = $out_trade_no;
			$res['cachekey'] = $attach;
		}else{
			$res['orderid'] = 0;
			$res['cachekey'] = '';
		}
		echo json_encode($res);
	}
	/***微信扫码支付逻辑结束***/
	public function aliQrpay() {
        $user = $this->user;
        $myorder = $this->buildOrder(9);
        if(empty($myorder)) {
            json_encode(array('status'=>-1,'msg'=>'网站订单生成失败！'));
            exit();
        }
        $width = intval($this->input->post('width'));
        $attach = md5($user['uid'].'_'.$myorder['orderid']);
        //商户订单号
        $out_trade_no = SYSTIME.':'.$myorder['orderid'];
        //订单名称
        $subject = $myorder['ordername'];
        $subject = shortstr($subject,80,'');
        //付款金额
        $total_fee = $myorder['totalfee'];
        //订单描述
        $body = $myorder['remark'];
        $body = shortstr($body,80,'');
        $notify_url = 'http://www.ebh.net/ibuy/alipay_notify.html';
        $param = array(
            'notify_url' => $notify_url,
            'return_url' => 'http://wap.ebh.net/ibuy/paysuccess.html',
            'trade_no' => $out_trade_no,
            'subject' => $subject,
            'total_fee' => $total_fee,
            'body' => $body,
            'show_url' => 'http://wap.ebh.net/ibuy/paysuccess.html',
            'width' => $width > 0 ? $width : 500
        );
        $alipay = Ebh::app()->lib('Alipay');
        $res = $alipay->alipayToQR($param);

        echo json_encode(array('res' => $res, 'attach' => $attach));
    }
	//验证优惠码
	public function verifycoupon(){
		$couponcode = $this->input->post('couponcode');
		$model = $this->model('Coupons');
		$row = $model->getOne(array('code'=>$couponcode));
		if(empty($row)){
			echo json_encode(array('code'=>-1,'msg'=>'验证失败'));
			exit;
		}else{
			if($this->user['uid'] == $row['uid']){
				echo json_encode(array('code'=>-2,'msg'=>'您不能使用自己的优惠码'));
			}else{
				echo json_encode(array('code'=>0));
			}
		}
	}
	
	/**
	*通过激活卡支付
	*/
	public function scardpay() {
		$result = array('status'=>0);
		$itemidlist = $this->input->post('itemid');
		$allowlist = $this->input->post('allowlist');//亚投可以激活多个课程
		if(empty($itemidlist) || (count($itemidlist)!=1 && !$allowlist)){
			$result['msg'] = '单张激活卡只能开通一门课程';
			echo json_encode($result);
			exit;
		}
		$user = $this->user;
		$crid = $this->input->post('rid');
		if(empty($user)){
			$result['msg'] = '用户未登录';
			echo json_encode($result);
			exit;
		}
		if($user['groupid'] == 5){
			$result['msg'] = '教师账号不能开通';
			echo json_encode($result);
			exit;
		}
		
		//获取学校卡号
		$yearcardmodel = $this->model('yearcard');
		$cardnumber = $this->input->post('cardnumber');
		if(empty($cardnumber)){
			$result['msg'] = '激活码不能为空';
			echo json_encode($result);
			exit;
		}
		$cardnumber = strtoupper($cardnumber);
		$cardinfo = $yearcardmodel->getYearcardByCardnumber($cardnumber,$crid);
		if( empty($cardinfo) ){
			$result['msg'] = '激活码不正确，开通失败';
			echo json_encode($result);
			exit;
		}else if( $cardinfo['status'] == 1 ){
			$result['msg'] = '激活码已被使用，开通失败';
			echo json_encode($result);
			exit;
		}

		$myorder = $this->buildOrder(1);	//生成订单，激活卡当做年卡使
		
		if(empty($myorder)) {	//订单生成失败
			$result['msg'] = '订单生成失败';
			echo json_encode($result);
			exit();
		}
		$cardpass = $cardinfo['cardpass'];
		$myorder['ordernumber'] = $cardpass;
		//处理权限
		$doresult = $this->notifyBOrder($myorder);
		if(empty($doresult)) {
			$result['msg'] = '开通失败';
			echo json_encode($result);
			exit();
		}
		//开通成功，则进行销卡操作
		$uparam = array(
			'cardid'=>$cardinfo['cardid'],
			'status'=>1,
			'activedate'=>SYSTIME
		);
		$uresult = $yearcardmodel->update($uparam);
		$result['status'] = 1;
		$credit = $this->model('credit');
		$credit->addCreditlog(array('ruleid'=>23,'detail'=>$myorder['itemlist'][0]['oname']));
		echo json_encode($result);
	}
	
	
	//生成优惠码
	private function getcouponcode(){
		$couponcode = $this->generatestr();
		//检测是否重复
		$model = $this->model('Coupons');
		$ck = $model->checkcoupon($couponcode);
		if($ck){
			$couponcode = $this->getcouponcode();
		}
		return $couponcode;
	}
	/**
	 * 生成随机数
	 * @param number $length
	 * @return string
	 */
	protected function generatestr( $length = 6 ){
		// 密码字符集，可任意添加你需要的字符
		$chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$password = '';
		for ( $i = 0; $i < $length; $i++ )
		{
			// 这里提供两种字符获取方式
			// 第一种是使用 substr 截取$chars中的任意一位字符；
			// 第二种是取字符数组 $chars 的任意元素
			// $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
			$password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
		}
		return $password;
	}

	/**
 	 * 对分销的参数解析
 	 */
    function parse_sharekey($sharekey) {
    	//有激活码的情况，分销不成立
    	$cardnumber = $this->input->post('cardnumber');
    	if (!empty($cardnumber)) {
    		return false;
    	}
        if (empty($sharekey)) {
            return false;
        }
        $sharekey = str_replace(' ', '+', $sharekey);
        $sharekey = explode('%',authcode($sharekey, 'DECODE'));
        return $sharekey;
    }

    /**
     * 生成课程包订单
     * @param $payfrom 支付来源
     * @param $bid 课程包ID
     * @param $roominfo 当前网校信息
     * @return array
     */
    private function buildOrder_bundle($payfrom, $bid, $roominfo) {
        $user = $this->user;
        if(empty($user)) {
            return FALSE;
        }
        $model = $this->model('Bundle');
        $bundle = $model->bundleDetail($bid);
        if (empty($bundle) || !empty($bundle['cannotpay']) || empty($roominfo) || $bundle['crid'] != $roominfo['crid']) {
            //只能生成本网校的课程包订单
            return false;
        }
		//课程包设置了限制报名时,查询开通人数
		if(!empty($bundle['islimit']) && $bundle['limitnum']>0){
			$openlimit = Ebh::app()->lib('OpenLimit');
			$openstatus = $openlimit->checkStatus($bundle);
			
			if(!$openstatus){//状态设置为无法报名
				return FALSE;
			}
		}
        //////////////////////////////////////////////////////
        //课程信息
        $bundle['courses'] = $model->getCourseList($bid, false);
		if (empty($bundle['courses'])) {
		    return false;
        }
        //////////////////////////////////

        $roominfo = $this->model('Classroom')->getclassroomdetail($bundle['crid']);
        $profitratio = unserialize($roominfo['profitratio']);
        $payordermodel = $this->model('PayOrder');
        $orderparam = array();
		$orderparam['bid'] = $bid;
        $orderparam['dateline'] = SYSTIME;
        $orderparam['ip'] = $this->input->getip();
        $orderparam['uid'] = $user['uid'];
        $orderparam['payfrom'] = $payfrom;
        $orderparam['couponcode'] = ''; //优惠码
        $orderparam['ordername'] = '开通'.$bundle['name'].'服务';	//订单名称
        $orderparam['remark'] = $bundle['name'].'课程包，价格：'.$bundle['bprice'].'元';		//订单备注
        $orderparam['totalfee'] = $bundle['bprice'];	//订单总额
        if (!empty($profitratio)) {
            $profitratio['baseTotal'] = $baseTotal = array_sum($profitratio);
            $orderparam['comfee'] = round($bundle['bprice'] * $profitratio['company'] / $baseTotal, 2);
            $orderparam['providerfee'] = round($bundle['bprice'] * $profitratio['agent'] / $baseTotal, 2);
            $orderparam['roomfee'] = $bundle['bprice'] - $orderparam['comfee'] - $orderparam['providerfee'];
        }

        $orderparam['crid'] = $roominfo['crid'];
        $orderparam['cwid'] = 0;
        $orderparam['providercrid'] = $bundle['crid'];	//来源平台crid
        $orderparam['pid'] = $bundle['pid'];
        $orderparam['itemlist'] = array_map(function($course) {
            return array(
                'itemid' => $course['itemid'],
                'cwid' => 0,
                'pid' =>$course['pid'],
                'folderid' => $course['folderid'],
                'omonth' => $course['imonth'],
                'oday' => $course['iday'],
                'oname' => $course['iname'],
                'iprice' => $course['iprice']
            );
        }, $bundle['courses']);
        $iprices = array_column($bundle['courses'], 'iprice');
        $acount = array_sum($iprices);
        unset($iprices);
        //包中课程的价格按比例重新换算
        array_walk($orderparam['itemlist'], function(&$item, $k, $args) {
            $item['uid'] = $args['uid'];
            $item['rname'] = $args['roominfo']['crname'];
            $item['osummary'] = $args['bundlename'].'-'.$item['oname'].(!empty($item['omonth']) ? $item['omonth'].'月' : $item['oday'].'天');
            $item['fee'] = round($item['iprice'] * $args['bprice'] / $args['acount'], 2);
            $item['comfee'] = round($item['fee'] * $args['profitratio']['company'] / $args['profitratio']['baseTotal'], 2);
            $item['providerfee'] = round($item['fee'] * $args['profitratio']['agent'] / $args['profitratio']['baseTotal'], 2);
            $item['roomfee'] = $item['fee'] - $item['comfee'] - $item['providerfee'];
            $item['domain'] = $args['roominfo']['domain'];
            $item['crid'] = $args['roominfo']['crid'];
        }, array(
            'uid' => $user['uid'],
            'roominfo' => $roominfo,
            'bundlename' => $bundle['name'],
            'acount' => $acount,
            'bprice' => $bundle['bprice'],
            'profitratio' => $profitratio
        ));
        $orderid = $payordermodel->addOrder($orderparam, true);
        if($orderid > 0) {
            $orderparam['orderid'] = $orderid;
            return $orderparam;
        }
        return $orderparam;
    }
}
