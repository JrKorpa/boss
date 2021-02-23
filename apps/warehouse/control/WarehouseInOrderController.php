<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseInOrderController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 16:54:53
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseInOrderController extends CommonController
{
	protected $smartyDebugEnabled = false;
	/****
	获取公司 列表
	****/
	public function company()
	{
		$model     = new CompanyModel(1);
		$company   = $model->getCompanyTree();//公司列表
		return $company;
	}
	  	//加工商
	public function jiagongshang()
	{
		$model_p = new ApiProModel();
		$pro_list = $model_p->GetSupplierList(array('status'=>1));//调用加工商接口
		return $pro_list;
	}
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('warehouse_in_order_search_form.html',array('bar'=>Auth::getBar()));
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


		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();

		$model = new WarehouseInOrderModel(21);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_in_order_search_page';
		$this->render('warehouse_in_order_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('warehouse_in_order_info.html',array(
			'view'=>new WarehouseInOrderView(new WarehouseInOrderModel(21)),
			'company_list'=>$this->company(),
			'pro_list'=>$this->jiagongshang()
		));
		$result['title'] = '添加—入库单';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$view = new WarehouseInOrderView(new WarehouseInOrderModel($id,21));
		$order_status = $view->get_order_status();
		if($order_status !=='1'){
			$result['content'] = '此单据不允许编辑';
			Util::jsonExit($result);
		}
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('warehouse_in_order_info.html',array(
			'view'=>$view,
			'company_list'=>$this->company(),
			'pro_list'=>$this->jiagongshang()
		));
		$result['title'] = '编辑-入库单';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$result = array('success' => 0,'error' => '');
		$result['title'] = '查看详情';
		$result['content'] = '开发中';
		Util::jsonExit($result);
		exit;
		$id = intval($params["id"]);
		$this->render('warehouse_in_order_show.html',array(
			'view'=>new WarehouseInOrderView(new WarehouseInOrderModel($id,21))
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		//rules验证
		$vd = new Validator();
		$vd->set_rules('order_no', '入库单号',  'require');
		$vd->set_rules('order_type', '单据类型',  'require');
		$vd->set_rules('put_in_type', '入库方式',  'require');
		$vd->set_rules('send_no', '送货单号',  'require');
		$vd->set_rules('goods_num', '货品总数',  'require');
		$vd->set_rules('cost_price', '总成本',  'require');
		$vd->set_rules('pay_price', '总支付',  'require');
		$vd->set_rules('prc_id', '加工商',  'require');
		$vd->set_rules('company_id', '所在公司',  'require');

		if (!$vd->is_valid($_POST))
		{
			$result['error'] = $vd->get_errors();
			Util::jsonExit($result);
		}
		//接收数据
		foreach ($_POST as $k => $v) {
			$$k = _Post::get($k);
		}

		$olddo = array();
		$newdo=array(
			'send_no'=>$send_no,
			'order_type'=>$order_type,
			'put_in_type'=>$put_in_type,
			'order_no'=>$order_no,
			'company_id'=>$company_id,
			'prc_id'=>$prc_id,
			'goods_num'=>$goods_num,
			'cost_price'=>$cost_price,
			'pay_price'=>$pay_price,
			'order_status'=>1,//新增状态默认为1，待审核
			'addby_time'=>time(),
			//还有参数待添加 【未完成】
		);

		$newmodel =  new WarehouseInOrderModel(22);
		$res = $newmodel->saveData($newdo,$olddo);
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

	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');

		//rules验证
		$vd = new Validator();
		$vd->set_rules('order_no', '入库单号',  'require');
		$vd->set_rules('order_type', '单据类型',  'require');
		$vd->set_rules('put_in_type', '入库方式',  'require');
		$vd->set_rules('send_no', '送货单号',  'require');
		$vd->set_rules('goods_num', '货品总数',  'require');
		$vd->set_rules('cost_price', '总成本',  'require');
		$vd->set_rules('pay_price', '总支付',  'require');
		$vd->set_rules('prc_id', '加工商',  'require');
		$vd->set_rules('company_id', '所在公司',  'require');

		if (!$vd->is_valid($_POST))
		{
			$result['error'] = $vd->get_errors();
			Util::jsonExit($result);
		}

		$newmodel =  new WarehouseInOrderModel($id,22);
		//接收数据
		foreach ($_POST as $k => $v) {
			$$k = _Post::get($k);
		}

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id'=>$id,
			'send_no'=>$send_no,
			'order_type'=>$order_type,
			'put_in_type'=>$put_in_type,
			'order_no'=>$order_no,
			'company_id'=>$company_id,
			'prc_id'=>$prc_id,
			'goods_num'=>$goods_num,
			'cost_price'=>$cost_price,
			'pay_price'=>$pay_price,
			'order_status'=>1,//新增状态默认为1，待审核
			//还有参数待添加 【未完成】
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	delete，删除
	 */
//	public function delete ($params)
//	{
//		$result = array('success' => 0,'error' => '');
//		$id = intval($params['id']);
//		$model = new WarehouseInOrderModel($id,22);
//		$model->setValue('is_deleted',1);
//		$res = $model->save(true);
//		//$res = $model->delete();
//		if($res !== false){
//			$result['success'] = 1;
//		}else{
//			$result['error'] = "删除失败";
//		}
//		Util::jsonExit($result);
//	}

	/**
	 * checkOrder 审核入库单
	 */
	public function checkOrder($params){
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new WarehouseInOrderModel($id,22);
		$model->setValue('order_status',3);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}

	/**
	 * checkOff 取消审核
	 */
	public function checkOff($params){
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new WarehouseInOrderModel($id,22);
		$model->setValue('order_status',2);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}

	/**
	 * mkJson 生成Json表单
	 */
	public function mkJson(){
		$id = _Post::getInt('id');

		$arr = Util::iniToArray(APP_ROOT.'warehouse/data/from_table.tab');
		if(!$id){
			$arr['data'] = [
				["1","","货品名称[只读]",'请选择',"","",""],
				["2","","货品名称[只读]",'请选择',"","",""]
			];
		}else{
			$arr['data'] = array(
				["01", "GoodsNo_10001", "货品名称__只读", "款式1", "18.00", "5","1980.05"],
				["02", "GoodsNo_10002", "货品名称__只读", "款式3", "28.00", "5","2980.05"],
				["03", "GoodsNo_10003", "货品名称__只读", "款式5", "38.00", "5","3980.05"],
				["04", "GoodsNo_10004", "货品名称__只读", "款式2", "58.00", "5","1180.05"],
				["05", "GoodsNo_10005", "货品名称__只读", "款式1", "68.00", "5","1580.05"],
				["06", "GoodsNo_10006", "货品名称__只读", "款式4", "98.00", "5","2380.05"],
				["07", "GoodsNo_10006", "货品名称__只读", "款式4", "98.00", "5","2380.05"],
				["08", "GoodsNo_10006", "货品名称__只读", "款式4", "98.00", "5","2380.05"],
				["09", "GoodsNo_10006", "货品名称__只读", "款式4", "98.00", "5","2380.05"],
				["10", "GoodsNo_10006", "货品名称__只读", "款式4", "98.00", "5","2380.05"],
			);
		}
		$json = json_encode($arr);

		echo $json;
	}

	/**
	 * getJson
	 */
	public function getJson(){
		$data = $_POST['data'];

		print_r($data);
	}














}

?>