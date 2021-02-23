<?php
/**
 *  -------------------------------------------------
 *   @file		: ShopcountReportController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Liulinyan <939942478@qq.com>
 *   @date		: 2015-08-14 10:15:23
 *   @update	:
 *  -------------------------------------------------
 */
class ShopXsgwcountReportController extends CommonController
{
	protected $smartyDebugEnabled = false;
	
	/**
	*	index，搜索框
	*/
	public function index ($params)
	{
		//获取体验店的信息
		$model = new ShopCfgChannelModel(1);
		$data = $model->getallshop();
		$this->render('shopcount_search_xsgw_form.html',
		array(
			'bar' => Auth::getBar(),
			'allshop'=>$data
			)
		);
	}

	/**
	* 	search，列表
	*/
	public function search($params)
	{
		$args = array(
			'mod' => _Request::get("mod"),
			'con' => substr(__CLASS__, 0, -10),
			'act' => __FUNCTION__,
			'shop_id' => _Request::get("shop_id"),
			'orderenter' => _Request::getString("orderenter"),
			'create_user' => _Request::getString("create_user"),
			'begintime' => _Request::get("begintime"),
			'endtime'   => _Request::get("endtime"),
			'is_delete' => 0
		);
		$alldata = array(
			'shopid' => '',
			'cloumn0'=>'',  //体验店id
			'cloumn1'=>'',
			'cloumn2'=>'',
			'cloumn3'=>'',
			'cloumn4'=>'',
			'cloumn5'=>'',
			'cloumn6'=>'',
			'cloumn7'=>'',
			'cloumn8'=>'',
			'cloumn9'=>'',
			'cloumn10'=>'',
			'xsgw'=>'',
			'index'=>1
		);
		if(empty($args['shop_id']))
		{
			$rows = 1;
			$this->render('shopcount_search_xsgw_list.html',array(
				'alldata'=>$alldata,
				'shopname'=>'',
				'rows'=>1
				));
			exit();
		}
		//就根据制单人分组就好了
		$shopid = $args['shop_id'];

		$ordermodel = new BaseOrderCountModel(27);
		//所有的销售顾问
		
		$allxsgw = $ordermodel->getxsgw($shopid);
		//获取体验店名称
		$shopchannelMod = new ShopCfgChannelModel(1);
		$data = $shopchannelMod->shopname($shopid);
		$shopname = $data['channel_name'];
		$alldata = array();
		$createuser = isset($args['create_user'])?$args['create_user']:'';
		if(!empty($createuser))
		{
			$rows = 1;
			$alldata[$createuser] = $this->serisedata($args,$shopname);
		}else{
			$i=1;
			foreach($allxsgw as $v)
			{
				$user = $v['create_user'];
				$args['create_user'] = $user;
				$alldata[$user] = $this->serisedata($args,$shopname);
				$alldata[$user]['index'] = $i;
				$i++;
			}
			$rows = count($allxsgw);
		}
		$this->render('shopcount_search_xsgw_list.html',array(
			'alldata'=>$alldata,
			'shopname'=>$shopname,
			'rows' => $rows
			));
	}
	
	
	
	//根据数组拿取所需要的信息
	public function serisedata($args,$shopname)
	{
		$where = array();
		$where['department_id'] = $args['shop_id'];
		$where['begintime'] = $args['begintime'];
		$where['endtime'] =  $args['endtime'];
		$where['make_order'] = $args['create_user'];
		$where['is_delete'] = $args['is_delete'];
			
		$spokemodel = new ShopcountInfoModel(17);
		//$cloumn 用来展示列表中的列的索引
		$cloumn1 = $spokemodel->getinfocount($where,1); //获取全部预约数
		$cloumn2 = $spokemodel->getinfocount($where,2); //获取实际到店数
		$cloumn3 = $spokemodel->getinfocount($where,2,'all');//预约当期应到数(我的理解就是包含未到店+已到店)
		//预约到店率：实际到店数/预约当期应到数
		if(empty($cloumn2) || empty($cloumn3))
		{
			$cloumn3 = 0;
		}else
		{
			$cloumn3 = ($cloumn2*100/$cloumn3).'%';
		}
		//实际成单数，订单总金额，已付款金额，未付款金额，赠品单数 第一次付款时间（订单状态：已审核并且支付定金，财务备案和已付款的订单)
		$ordermodel = new BaseOrderCountModel(27);
		
		$orderarr['department_id'] = $args['shop_id'];
		$orderarr['referer'] = $args['orderenter'];
		$orderarr['create_user'] = $args['create_user'];
		
		$timearr['begintime'] = $args['begintime'];
		$timearr['endtime'] = $args['endtime'];

		$allorder = $ordermodel->getrelorder($orderarr,$timearr);
		//根据orderdata 得出实际成单数，赠品单数
		
		//实例化付款对象
		$orderpayModel = new AppOrderPayModel(29);
		//$cloumn4 = $orderdata['recordCount'];   //所有成单数
		$cloumn4 = count($allorder);
		//定义三个变量 用来统计所有的值
		$zpnum = 0;          //赠品单数
		$ordercount = 0;    //订单总金额
		$payokcount = 0;    //已付款金额
		$payneedcount = 0;  //未付款金额
		if($cloumn4>0)
		{			
			foreach($allorder as $orderinfo)
			{
				$ordersn = $orderinfo['order_sn'];
				$iszp = $orderinfo['is_zp'];
				if($iszp > 0)
				{
					$zpnum++;
				}
				//根据ordersn拿取财务信息
				//先定义一个空数组
				$payinfo = array('order_amount'=>0,'deposit'=>0,'balance'=>0);
				$paydata = $orderpayModel->getpayinfo($ordersn);
				if(!empty($paydata))
				{
					$payinfo = $paydata;
				}
				$ordercount+=$payinfo['order_amount'];
				$payokcount+=$payinfo['deposit'];
				$payneedcount+=$payinfo['balance'];
			}
		}
		//预约成交率：实际成单数/实际到店数
		if($cloumn4 ==0 || $cloumn2 ==0)
		{
			$cloumn5=0;
		}else{
			$cloumn5 = $cloumn4*100/$cloumn2.'%';
		}
		//客单价：订单总金额/实际成单数
		
		if($ordercount ==0 || $cloumn4==0)
		{
			$cloumn9=0;	
		}else{
			$cloumn9 = ceil($ordercount/$cloumn4);
		}		
		$shopid = $where['department_id'];
		//组装数据
		$alldata = array(
			'shopid' => $shopid,
			'shopname'=>$shopname,  //体验店id
			'cloumn1'=>$cloumn1,
			'cloumn2'=>$cloumn2,
			'cloumn3'=>$cloumn3,
			'cloumn4'=>$cloumn4,
			'cloumn5'=>$cloumn5,
			'cloumn6'=>$ordercount,
			'cloumn7'=>$payokcount,
			'cloumn8'=>$payneedcount,
			'cloumn9'=>$cloumn9,
			'cloumn10'=>$zpnum,
			'xsgw'=>$args['create_user']
		);
		
		return $alldata;
	}	
}?>