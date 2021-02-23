<?php
/**
 *  -------------------------------------------------
 *   @file		: ZhantingFahuoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-06 10:15:23
 *   @update	:
 *  -------------------------------------------------
 */
class ZhantingFahuoController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/** 获取快递公司列表 **/
	public function getAllExpress(){
		$ex_model = new ExpressModel(1);
		return $ex_model->getAllExpress();
	}

	/** 获取公司列表 **/
	public function getCompanyList(){
		$model = new CompanyModel(1);
		return $model->getCompanyTree();//公司列表
	}

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//获取快递公司
		$getAllExpress = $this->getAllExpress();
		$dd = new DictModel(1);
		$send_status = $dd->getEnumArray('order.send_good_status');
		$confirm = $dd->getEnumArray('confirm');
		// echo '<pre>';print_r($send_status);die;

		$this->render('zhantingfahuo_search_form.html',array(
			'bar'=>Auth::getBar(),
			'express' =>$getAllExpress,
			'send_status'=> $send_status,
			'confirm' => $confirm,
			));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'zhuancang_sn' => _Request::get("zhuancang_sn"),
		);
		$result = array('success' => 0,'error' => '');

		//获取快递公司
		$kuaidi = array();
		$AllExpress = $this->getAllExpress();
		foreach($AllExpress as $val){
			$kuaidi[$val['id']] = $val['exp_name'];
		}

		//获取公司列表
		$companyList = array();
		$arr = $this->getCompanyList();
		foreach($arr as $val){
			$companyList[$val['id']] = $val['company_name'];
		}

		if(empty($args['zhuancang_sn'])){
			echo "<script>util.xalert('请输入调拨单号')</script>";
			exit;
		}
		//根据调拨单，查询出货地ID
		// m是调拨单 wf维修调拨单
		$type="M";
		if($args['zhuancang_sn'][1]=='F'){
			$type='WF';
		}		
		$info = ApiWarehouseModel::CheckBillByBillSn($args['zhuancang_sn'], $bill_type = "{$type}");
		if(!count($info)){
			echo "<script>util.xalert('调拨单 {$args['zhuancang_sn']} 不存在')</script>";
			exit;
		}
		if (in_array($info['to_warehouse_id'],array(659,395,373)))
		{
			$info['to_company_id'] = 505;
		}
		//在展厅发货的时候输入调拨单必须是保存状态 2015-4-2 hlc
		if($info['bill_status'] != 1){
			/*$result['error'] = "调拨单 <span style='color:red;'>{$args['zhuancang_sn']}</span> 不是保存状态，不能进行展厅发货操作";
			Util::jsonExit($result);*/
			echo "<script>util.xalert('调拨单 <span style=\'color:red;\'>". $args['zhuancang_sn'] ."</span> 不是保存状态，不能进行展厅发货操作')</script>";
			exit;
		}

		$model = new ShipParcelModel(43);
		$z_arr = $model->getHaveZc($args['zhuancang_sn']);
		if(count($z_arr))
		{
			echo "<script>util.xalert('调拨单已存在于快递单：<span style=\'color:red;\'>". $z_arr['express_sn'] ."</span>')</script>";
			exit;
		}

		//去未发货的并且目标展厅和调拨单入库公司相同的包裹
		$where = array('company_id'=>$info['to_company_id'],'send_status' => 1,'order_by' =>'id' ,'sort' => 'DESC');
		$data = $model->pageList($where,30);

		if ($_SESSION['userName'] == 'admin')
		{
			//print_r($data);exit;
		}
		if (!count($data['data']))
		{
			echo "<script>util.xalert('没有符合要求的快递单')</script>";
			exit;
		}
		$parce = array();
		foreach($data['data'] as $key => $val)
		{
			//包裹金额 + 调拨单金额  不能超过三十万
			$all_amount = $val['amount'] + $info['goods_total'];
			if($all_amount < 400000)
			{
				$parce = $val;
				break;
			}
		}
		//echo $all_amount;exit;
		//print_r($parce);exit;
		//如果没有找到合适的包裹则取第一个
		if(!count($parce))
		{
			$parce = $data['data']['0'];
			$all_amount = $data['data']['0']['amount']+$info['goods_total'];

		}
		//var_dump($data['data']);exit;
		//var_dump($parce);exit;
		//通过接口 获取 转仓单明细信息
		$detail_goods = ApiWarehouseModel::getDetailByBillSn($bill_no =$params['zhuancang_sn'], $bill_type = 'M');

		$goods_sn_str = "";
		$goods_name_str = "";

		if( !empty($detail_goods) ){
			foreach($detail_goods as $key => $val ){
				$goods_sn_str .= ','.$val['goods_sn'];
				$goods_name_str .= ','.$val['goods_name'];
			}
		}
		$shouhuoren = $info['to_company_name'];
		//如果有订单号的，根据订单号查询收货人
		$salesModel = new SalesModel(27);
		if(!empty($info['order_sn'])){
		    $address = $salesModel->getAddressByOrderSn($info['order_sn']);
		    if(!empty($address['consignee'])){
		        $shouhuoren = $address['consignee'];
		    }
		}
		$newdo=array(
			'parcel_id'=>$parce['id'],
			'shouhuoren'=> $shouhuoren,
			'zhuancang_sn'=>$info['bill_no'],
			'from_place_id'=>$info['from_company_id'],
			'to_warehouse_id'=>$info['to_warehouse_id'],
			'num'=>$info['goods_num'],
			'amount'=>$info['goods_total'],
			'goods_sn'=>ltrim($goods_sn_str , ','),
			'goods_name'=>ltrim($goods_name_str , ','),
			'create_user'=>$_SESSION['userName'],
			'create_time'=>date('Y-m-d H:i:s'),
			'order_sn' => $info['order_sn'],
		);

                //var_dump($newdo);exit;
		$newmodel =  new ShipParcelDetailModel(44);
		$res = $newmodel->insertDetail($newdo, $all_amount , $parce['id'], $info['id'] , $parce['express_sn']);
                //回写到订单日志 start
                $express_data = $newmodel->getExpressSn($parce['id']);
                $express = new ExpressView(new ExpressModel(1));
                $express_name = $express->get_name($express_data['express_id']);
                $bill_no = $newdo['zhuancang_sn'];//获取调拨单下的货
                $goods_info = ApiModel::warehouse_api(array('bill_no'), array($bill_no), "getDetailByBillSn");
                //var_dump($goods_info);exit;
                $order_sn = $newdo['order_sn'];
                if(!empty($order_sn)) {
                    foreach($goods_info as $k => $v) {
                        $time = date('Y-m-d H:i:s');
                        $user = $_SESSION['userName'];
                        $goods_name .= $v['goods_name'].',';                        
                    }   
                    $goods_name = rtrim($goods_name,',');
                    $remark = "展厅发货 &nbsp;货品（".$goods_name."） &nbsp;".$express_name." 快递单号：".$express_data['express_sn'];
                    $rs = ApiModel::sales_api(array("order_no","create_user","remark"), array($order_sn,$user,$remark), "AddOrderLog");
                }
                //add log end
		if(!$res)
		{
			echo "<script>util.xalert('添加失败')</script>";
			exit;
		}

		$model = new ShipParcelModel($parce['id'],43);
                //添加上客户姓名和订单号显示
		$parce_data = $model->getDataObject();
                if (!empty($newdo['order_sn'])) {
                    $parce_data['order_sn'] = $newdo['order_sn'];
                    $consignee = $newmodel->GetConsigneeByOrdersn($newdo['order_sn']);                   
                    $parce_data['consignee'] = $consignee;
                    
                }else {
                    $parce_data['order_sn'] = '--';
                    $parce_data['consignee'] = '--';
                }
                
		$this->render('zhanting_search_list.html',array(
			'data'=>$parce_data,
			'kuaidi' => $kuaidi,
			'companyList'=>$companyList,
		));
	}
	//金额限制
	public function amountMax($params) 
	{
		$result = array('success' => 0,'error' =>'');
		$zhuancang_sn = $params['zhuancang_sn'];
		//获取快递公司
		$kuaidi = array();
		$AllExpress = $this->getAllExpress();
		foreach($AllExpress as $val){
			$kuaidi[$val['id']] = $val['exp_name'];
		}

		//获取公司列表
		$companyList = array();
		$arr = $this->getCompanyList();
		foreach($arr as $val){
			$companyList[$val['id']] = $val['company_name'];
		}

		if(empty($params['zhuancang_sn'])){
			$result['success'] = 1;
			Util::jsonExit($result);

		}
		//根据调拨单，查询出货地ID
		// m是调拨单 wf维修调拨单
		$type="M";
		if($params['zhuancang_sn'][1]=='F'){
			$type='WF';
		}	

		//如果是上海钻交所客单库、等3个仓库则目标展厅是上海
		$info = ApiWarehouseModel::CheckBillByBillSn($params['zhuancang_sn'], $bill_type = "{$type}"); 
		//var_dump($info);exit;
		if (in_array($info['to_warehouse_id'],array(659,395,373)))
		{
			$info['to_company_id'] = 505;
		}
		$model = new ShipParcelModel(43);
		//去未发货的并且目标展厅和调拨单入库公司相同的包裹
		$where = array('company_id'=>$info['to_company_id'],'send_status' => 1,'order_by' =>'id' ,'sort' => 'DESC');
		$data = $model->pageList($where,30);
		$parce = false;
		foreach($data['data'] as $key => $val)
		{
			//包裹金额 + 调拨单金额  不能超过四 十万
			$all_amount = $val['amount'] + $info['goods_total'];
			if($all_amount >= 400000)
			{
				$parce = true;
				break;
			}
		}

		if($parce)
		{
			$result['error'] = "包裹总金额不能超过40万,";
			Util::jsonExit($result);
		}
		$result['success'] = 1;
		Util::jsonExit($result);
	}

	/**
	 *	insert，信息入库 写入包裹单
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		//获取调拨单信息
		if(empty($params['zhuancang_sn'])){
			$result['error'] = '后台程序异常';
			Util::jsonExit($result);
		}
		//获取调拨单信息
		$info = ApiWarehouseModel::CheckBillByBillSn($params['zhuancang_sn'], $bill_type = 'M');

		//通过接口 获取 转仓单明细信息
		$detail_goods = ApiWarehouseModel::getDetailByBillSn($bill_no =$params['zhuancang_sn'], $bill_type = 'M');
		$data = array(
			'top_jiage' => 0,
			'goods_num' => 0,
			'goods_sn' =>'',
			'goods_name' => '',
		);
		$model = new ShipParcelModel( $params['baoguo_id'] , 43);
		$amount = $model->getValue('amount');		//单据金额

		if( !empty($detail_goods) ){
			foreach($detail_goods as $key => $val ){
				$data['top_jiage'] += floatval($val['chengbenjia']);
				$data['goods_num'] += $val['num'];
				$data['goods_sn'] .= ','.$val['goods_sn'];
				$data['goods_name'] .= ','.$val['goods_name'];
			}
			$amount +=$data['top_jiage'];
		}

		if( $amount >= 400000 ){
			$result['error'] = '添加失败！当前单据总金额超过了40万';
			Util::jsonExit($result);
		}

		//公司列表
		$arr = $this->getCompanyList();
		$companyList = array();
		foreach($arr as $key => $val){
			$companyList[$val['id']] = $val['company_name'];
		}
		$shouhuoren = $info['to_company_name'];
		//如果有订单号的，根据订单号查询收货人
		$salesModel = new SalesModel(27);
		if(!empty($info['order_sn'])){
		    $address = $salesModel->getAddressByOrderSn($info['order_sn']);
		    if(!empty($address['consignee'])){
		        $shouhuoren = $address['consignee'];
		    }
		}
		$newdo=array(
			'parcel_id'=>$params['baoguo_id'],
			'shouhuoren'=>$shouhuoren,
			'zhuancang_sn'=>$params['zhuancang_sn'],
			'from_place_id'=>$info['from_company_id'],
			'to_warehouse_id'=>$info['to_warehouse_id'],

			'num'=>$data['goods_num'],
			'amount'=>$data['top_jiage'],
			'goods_sn'=>ltrim($data['goods_sn'] , ','),
			'goods_name'=>ltrim($data['goods_name'] , ','),

			'create_user'=>$_SESSION['userName'],
			'create_time'=>date('Y-m-d H:i:s'),
			'order_sn' => $info['order_sn'],
		);

		$newmodel =  new ShipParcelDetailModel(44);
		$res = $newmodel->insertDetail($newdo, $params['amount'] , $params['baoguo_id']);

		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
	}

	//根据包裹单id获取包裹单的信息
	public function getParcelInfoByID($params){
		$result = array('success' => 0,'error' =>'');
		print_r($params);
	}


}?>