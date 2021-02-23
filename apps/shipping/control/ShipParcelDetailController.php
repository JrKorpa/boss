<?php
/**
 *  -------------------------------------------------
 *   @file		: ShipParcelDetailController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-06 18:27:56
 *   @update	:
 *  -------------------------------------------------
 */
class ShipParcelDetailController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/** 获取公司列表 **/
	public function getCompanyList(){
		$model = new CompanyModel(1);
		return $model->getCompanyTree();//公司列表
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
			'_id' => _Request::get("_id"),
			//'参数' = _Request::get("参数");
		);
		$page = _Request::getInt("page",1);
		$where = array();
		$where['_id'] = $args['_id'];

		//公司列表
		$arr = $this->getCompanyList();
		foreach($arr as $key => $val){
			$companyList[$val['id']] = $val['company_name'];
		}
		//获取仓库
		$warehouseList = array();
		$warehouseArr = ApiWarehouseModel::getWarehouseList();
		foreach($warehouseArr as $w){
			$warehouseList[$w['id']] = $w['name'];
		}
		//取得快递公司信息
		$ex_model = new ExpressModel(1);
		$info_express = $ex_model->getAllExpress();
		$kuaidi = array();
		foreach($info_express as $kd){
			$kuaidi[$kd['id']] = $kd['exp_name'];
		}

		$model = new ShipParcelDetailModel(43);
		$data = $model->pageList($where,$page,10,false);

		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'ship_parcel_detail_search_page';
		$this->render('ship_parcel_detail_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'bar'=>Auth::getBar(),
			'companyList'=>$companyList,
			'warehouseList' =>$warehouseList,
			'kuaidi'=>$kuaidi,
		));
	}

	/** 检测单据是否处于发货状态 **/
	public function checkFahuo($params){
		$result = array('success' => 0,'error' =>'');
		$send = $params['send'];	//单据的发货状态
		$type = $params['type'];
		if($send == 2){	//已发货
			$result['error'] = $type == 'add' ? '单据已发货，不能添加明细' : '单据已发货，不能删除明细';
		}else{
			$result['success'] = 1;
		}
		Util::jsonExit($result);
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$_id = _Post::getInt('_id');
		/** 检测是否发货，发货后不能新增 **/
		$model = new ShipParcelModel($_id, 43);
		if( $model->getValue('send_status') == 2){
			$result['success'] = 1;
			$result['error'] = '快递单已发货，不能添加明细';
			Util::jsonExit($result);
		}

		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('ship_parcel_detail_info.html',array(
			'view'=>new ShipParcelDetailView(new ShipParcelDetailModel(43)),
			'_id'=>$_id,
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$v =new ShipParcelDetailView(new ShipParcelDetailModel($id,43));
		$result['content'] = $this->fetch('ship_parcel_detail_info.html',array(
			'view'=>$v,
			'tab_id'=>$tab_id,
			'_id'=>$v->get_parcel_id()
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{

		$result = array('success' => 0,'error' =>'');
		$_id = _Post::getInt('_id');//主表主键
		$model = new ShipParcelModel($_id, 43);
		$zhuancang_sn = _Request::get('zhuancang_sn');
		
		//判断调拨单是否已存在
		$newmodel = new ShipParcelDetailModel(43);
		$ret = $newmodel->select2("zhuancang_sn", "parcel_id={$_id} and zhuancang_sn='{$zhuancang_sn}'" , $type = 3);
		if($ret){
			$result['error'] = '调拨单号重复！';
			Util::jsonExit($result);
		}
		if(empty($zhuancang_sn)){
			$result['error'] = '请填写调拨单号';
			Util::jsonExit($result);
		}
		// m是调拨单 wf维修调拨单
		$type="M";
		if($zhuancang_sn[1]=='F'){
			$type='WF';
		}
		$ret = ApiWarehouseModel::CheckBillByBillSn($zhuancang_sn, $bill_type = "{$type}") ;
		/**
		*（1）	输入调拨单号，判断调拨单号有效
		*（2）	在做展厅发货的时候，输入的调拨单，不允许添加取消状态的调拨单
		*（3）	判断调拨单是否已经被加入了其他包裹
		*（4）	判断是否有调拨单所入仓库的快递包裹 （根据仓库获取该仓库所属的公司，该所属公司 === 展厅）
		*（5）	包裹总金额不超过30万
		 * */
		#（1）	输入调拨单号，判断调拨单号有效。
		if(empty($ret)){
			$result['error'] = '没有查到该调拨单号';
			Util::jsonExit($result);
		}

		#（2）在做展厅发货的时候，输入的调拨单，不允许添加取消状态的调拨单
		if($ret['bill_status'] == 3){
			$result['error'] = "调拨单: <span style='color:red;'>{$zhuancang_sn}</span> 已取消，不允许发货";
			Util::jsonExit($result);
		}

		#（3）	判断调拨单是否已经被加入了其他包裹
		/*$detailModel =  new ShipParcelDetailModel(43);
		$res = $detailModel->select2(' `id`, `parcel_id` ', " `zhuancang_sn` = '{$zhuancang_sn}' ", $type = 2);
		if(!empty($res)){
			$result['error'] = '该调拨单已经在其他包裹中绑定，不能再添加到当前包裹';
			Util::jsonExit($result);
		}*/
		$express_sn = $model->getValue('express_sn');
		if( $ret['ship_number'] != '' && $ret['ship_number'] != $express_sn){
			$result['error'] = '该调拨单已经在其他包裹中绑定，不能再添加到当前包裹';
			Util::jsonExit($result);
		}
		
		#（4）	判断是否有调拨单所入仓库的快递包裹
		/*
		$kuaidi_to_company = $model->getValue('company_id');
		if($ret['to_company_id'] != $kuaidi_to_company ){
			$result['error'] = '该调拨单与当前包裹不是同一个入库公司，不能添加到包裹';
			Util::jsonExit($result);
		}*/
		#（5）	包裹总金额不超过30万 ， 获取转仓单的明细信息
		//通过接口 获取 转仓单明细信息
		$detail_goods = ApiWarehouseModel::getDetailByBillSn($bill_no =$zhuancang_sn, $bill_type = 'M');
		$data = array(
			'top_jiage' => 0,
			'goods_num' => 0,
			'goods_sn' =>'',
			'goods_name' => '',
		);

		$amount = $model->getValue('amount');		//单据金额
//var_dump($detail_goods,$amount);exit;
		if( !empty($detail_goods) ){
			foreach($detail_goods as $key => $val ){
				$data['top_jiage'] += floatval($val['sale_price']);
				$data['goods_num'] += $val['num'];
				$data['goods_sn'] .= ','.$val['goods_sn'];
				$data['goods_name'] .= ','.$val['goods_name'];
			}
			$amount +=$data['top_jiage'];
		}

		if( $amount >= 400000 ){
			//$result['error'] = '添加失败！当前单据总金额超过了40万';
			//Util::jsonExit($result);
		}

		$arr = $this->getCompanyList();
		$companyList = array();
		foreach($arr as $key => $val){
			$companyList[$val['id']] = $val['company_name'];
		}
		
		$shouhuoren = $ret['to_company_name'];
		$order_sn   = $ret['order_sn'];
		//如果有订单号的，根据订单号查询收货人
		$salesModel = new SalesModel(27);
        if(!empty($order_sn)){
            $address = $salesModel->getAddressByOrderSn($order_sn);
            if(!empty($address['consignee'])){
                $shouhuoren = $address['consignee'];
            }
        }
		$newdo=array(
			'parcel_id'=>$_id,
			'shouhuoren'=>$shouhuoren,
			'zhuancang_sn'=>$zhuancang_sn,
			'from_place_id'=>$ret['from_company_id'],
			'to_warehouse_id'=>$ret['to_warehouse_id'],
			'num'=>$data['goods_num'],
			'amount'=>$data['top_jiage'],
			'goods_sn'=>ltrim($data['goods_sn'] , ','),
			'goods_name'=>ltrim($data['goods_name'] , ','),
			'create_user'=>$_SESSION['userName'],
			'create_time'=>date('Y-m-d H:i:s'),
			'order_sn' => $order_sn,
		);

		$newmodel =  new ShipParcelDetailModel(44);
		$res = $newmodel->insertDetail($newdo,$amount, $_id , $ret['id'] , $express_sn);
                 //回写到订单日志 start
                $express_data = $newmodel->getExpressSn($newdo['parcel_id']);
                $express = new ExpressView(new ExpressModel(1));
                $express_name = $express->get_name($express_data['express_id']);
                $order_sn = $newdo['order_sn'];
                $bill_no = $newdo['zhuancang_sn'];//获取调拨单下的货
                $goods_info = ApiModel::warehouse_api(array('bill_no'), array($bill_no), "getDetailByBillSn");
                if(!empty($order_sn)) {
                    /* foreach ($goods_info as $k => $v){
                        $goods_name = $v['goods_name'];
                        $time = date('Y-m-d H:i:s');
                        $user = $_SESSION['userName'];
                        $remark = $goods_name.'&nbsp;'.$express_name." 快递单号：".$express_data['express_sn'];
                        $rs = ApiModel::sales_api(array("order_no","create_user","remark"), array($order_sn,$user,$remark), "AddOrderLog");
                   
                    } */
               	//订单日志只需要显示一行记录，格式如下：“展厅发货，货品（千足金金条,S925玉髓吊坠），顺丰速运 快递单号：321123654563” 
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

	public function amountMax() 
	{
		//var_dump($_REQUEST);exit;
		$result = array('success' => 0,'error' =>'');
		$_id = _Post::getInt('_id');//主表主键
		$model = new ShipParcelModel($_id, 43);
		$zhuancang_sn = _Request::get('zhuancang_sn');
		
		if(empty($zhuancang_sn)){
			$result['error'] = '请填写调拨单号';
			$result['num'] = 1;
			Util::jsonExit($result);
		}
		// m是调拨单 wf维修调拨单
		$type="M";
		if($zhuancang_sn[1]=='F'){
			$type='WF';
		}
		$ret = ApiWarehouseModel::CheckBillByBillSn($zhuancang_sn, $bill_type = "{$type}") ;
		/**
		*（1）	输入调拨单号，判断调拨单号有效
		*（2）	在做展厅发货的时候，输入的调拨单，不允许添加取消状态的调拨单
		*（3）	判断调拨单是否已经被加入了其他包裹
		*（4）	判断是否有调拨单所入仓库的快递包裹 （根据仓库获取该仓库所属的公司，该所属公司 === 展厅）
		*（5）	包裹总金额不超过30万
		 * */
		#（1）	输入调拨单号，判断调拨单号有效。
		if(empty($ret)){
			$result['num'] = 1;
			$result['error'] = '没有查到该调拨单号';
			Util::jsonExit($result);
		}

		#（2）在做展厅发货的时候，输入的调拨单，不允许添加取消状态的调拨单
		if($ret['bill_status'] == 3){
			$result['num'] = 1;
			$result['error'] = "调拨单: <span style='color:red;'>{$zhuancang_sn}</span> 已取消，不允许发货";
			Util::jsonExit($result);
		}

		#（3）	判断调拨单是否已经被加入了其他包裹
		$express_sn = $model->getValue('express_sn');
		if( $ret['ship_number'] != '' && $ret['ship_number'] != $express_sn){
			$result['num'] = 1;
			$result['error'] = '该调拨单已经在其他包裹中绑定，不能再添加到当前包裹';
			Util::jsonExit($result);
		}
		#（4）	判断是否有调拨单所入仓库的快递包裹
		$kuaidi_to_company = $model->getValue('company_id');
		if($ret['to_company_id'] != $kuaidi_to_company ){
			$result['num'] = 2;
			$result['error'] = '该调拨单目标展厅与当前入库公司('.$ret['to_company_name'].')不一致,';
			Util::jsonExit($result);
		}
		#（5）	包裹总金额不超过30万 ， 获取转仓单的明细信息
		//通过接口 获取 转仓单明细信息
		$detail_goods = ApiWarehouseModel::getDetailByBillSn($bill_no =$zhuancang_sn, $bill_type = 'M');
		$data = array(
			'top_jiage' => 0,
			'goods_num' => 0,
			'goods_sn' =>'',
			'goods_name' => '',
		);

		$amount = $model->getValue('amount');		//单据金额

		if( !empty($detail_goods) ){
			foreach($detail_goods as $key => $val ){
				$data['top_jiage'] += floatval($val['sale_price']);
				$data['goods_num'] += $val['num'];
				$data['goods_sn'] .= ','.$val['goods_sn'];
				$data['goods_name'] .= ','.$val['goods_name'];
			}
			$amount +=$data['top_jiage'];
		}

		if( $amount >= 400000 ){
			$result['num'] = 2;
			$result['error'] = '当前单据总金额超过了40万';
			Util::jsonExit($result);
		}
		$result['success'] = 1;
		Util::jsonExit($result);
	}

	/** 添加 无调拨单 show  **/
	public function addNo(){
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('ship_parcel_detail_info_no.html',array(
			'view'=>new ShipParcelDetailView(new ShipParcelDetailModel(43)),
			'_id'=>_Post::getInt('_id')
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/** 无调拨单插入 **/
	public function noMinsertDate($params){
		$result = array('success' => 0,'error' => '');
		$shouhuoren = $params['shouhuoren'];
		$goods_name = $params['goods_name'];
		$num_danwei = $params['num_danwei'] ? $params['num_danwei']:'件';
		$_id = $params['_id'];
		$num = intval($params['num']);
		if($shouhuoren == ''){
			$result['error'] = '请输入收货人';
			Util::jsonExit($result);
		}else if(mb_strlen($shouhuoren) > 35){
			$result['error'] = '输入的收货人长度不能超过35个字符';
			Util::jsonExit($result);
		}
		if($goods_name == ''){
			$result['error'] = '请输入货品名称';
			Util::jsonExit($result);
		}else if(mb_strlen($shouhuoren) > 255){
			$result['error'] = '输入的货品名称长度不能超过255个字符';
			Util::jsonExit($result);
		}
		if($num == ''){
			$result['error'] = '请输入数字类型的货品数量';
			Util::jsonExit($result);
		}else if(intval($num) <= 0){
			$result['error'] = '货品数量不能等于 或 小于 0';
			Util::jsonExit($result);
		}
		if(mb_strlen($num) > 10){
			$result['error'] = '货品数量不能10位数';
			Util::jsonExit($result);
		}

		$olddo = array();
		$newdo=array(
			'parcel_id'=>$_id,
			'shouhuoren'=>$shouhuoren,
			'goods_name'=>$goods_name,
			'num'=>$num,
			'num_danwei'=>$num_danwei,
			'create_user'=>$_SESSION['userName'],
			'create_time' => date('Y-m-d H:i:s'),
		);
		$newmodel =  new ShipParcelDetailModel(44);
		$res = $newmodel->saveData($newdo,$olddo);

		$model = new ShipParcelModel($_id , 44);
		$old_num = $model->getValue('num');
 		$olddo2 = $model->getDataObject();
		$newdo2 = array(
			'id' => $_id,
			'num' => $old_num + $num,
		);

		if($res !== false)
		{
			$result['success'] = 1;
			//跟新主表的货品数量
			$model->saveData($newdo2,$olddo2);
		}
		else
		{
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$num = intval($params['num']);
		$amount = !empty($params['amount']) ? $params['amount'] : 0;
		$baoguo_id = $params['baoguo_id'];
		$model = new ShipParcelDetailModel($id,44);
		$do = $model->getDataObject();

		$bill_no = $model->getValue('zhuancang_sn');	//调拨单号

		$res = $model->deleteDetail($id, $num,$amount, $baoguo_id, $bill_no);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
	
	//判断是否有调拨单所入仓库的快递包裹
	public function  checkCompany(){
		
		
	}
	
	
	
}

?>