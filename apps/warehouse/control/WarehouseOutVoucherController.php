<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseOutVoucherController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 21:07:31
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseOutVoucherController extends CommonController
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
	/***
	获取有效的仓库
	***/
	public function warehouse()
	{
		$model_w	= new WarehouseModel(21);
		$warehouse  = $model_w->select(array('is_delete'=>1),array("id","name"));
		return $warehouse;
	}
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('warehouse_out_voucher_search_form.html',array('bar'=>Auth::getBar()));
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

		$model = new WarehouseOutVoucherModel(21);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_out_voucher_search_page';
		$this->render('warehouse_out_voucher_search_list.html',array(
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
		$result['content'] = $this->fetch('warehouse_out_voucher_info.html',array(
			'view'=>new WarehouseOutVoucherView(new WarehouseOutVoucherModel(21)),
			'company_list'=>$this->company(),
			'warehouse_list'=>$this->warehouse()
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
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('warehouse_out_voucher_info.html',array(
			'view'=>new WarehouseOutVoucherView(new WarehouseOutVoucherModel($id,21)),
			'company_list'=>$this->company(),
			'warehouse_list'=>$this->warehouse()
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
//	public function show ($params)
//	{
//		die('开发中');
//		$id = intval($params["id"]);
//		$this->render('warehouse_out_voucher_show.html',array(
//			'view'=>new WarehouseOutVoucherView(new WarehouseOutVoucherModel($id,21))
//		));
//	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		//rules验证
		$vd = new Validator();
		$vd->set_rules('voucher_outno', '出库单号',  'require');
		$vd->set_rules('kela_order_sn', 'BDD订单号',  'require');
		$vd->set_rules('voucher_type', '单据类型',  'require');
		$vd->set_rules('warehouse_id', '转出仓',  'require');
		$vd->set_rules('company_id', '转出公司',  'require');
		$vd->set_rules('goods_num', '货品总数',  'require');
		$vd->set_rules('cost_price', '总成本金额',  'require');
		$vd->set_rules('sales_price', '总销售金额',  'require');

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
			'voucher_outno'=>$voucher_outno,
			'kela_order_sn'=>$kela_order_sn,
			'voucher_type'=>$voucher_type,
			'warehouse_id'=>$warehouse_id,
			'company_id'=>$company_id,
			'goods_num'=>$goods_num,
			'cost_price'=>$cost_price,
			'sales_price'=>$sales_price,
			'addby_id'=>$_SESSION['userId'],
			'add_time'=>time(),
			'voucher_stauts'=>1,//默认值
		);

		$newmodel =  new WarehouseOutVoucherModel(22);
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
		$vd->set_rules('voucher_outno', '出库单号',  'require');
		$vd->set_rules('kela_order_sn', 'BDD订单号',  'require');
		$vd->set_rules('voucher_type', '单据类型',  'require');
		$vd->set_rules('warehouse_id', '转出仓',  'require');
		$vd->set_rules('company_id', '转出公司',  'require');
		$vd->set_rules('goods_num', '货品总数',  'require');
		$vd->set_rules('cost_price', '总成本金额',  'require');
		$vd->set_rules('sales_price', '总销售金额',  'require');

		if (!$vd->is_valid($_POST))
		{
			$result['error'] = $vd->get_errors();
			Util::jsonExit($result);
		}
		//接收数据
		foreach ($_POST as $k => $v) {
			$$k = _Post::get($k);
		}
		$newdo=array(
			'id'=>$id,
			'voucher_outno'=>$voucher_outno,
			'kela_order_sn'=>$kela_order_sn,
			'voucher_type'=>$voucher_type,
			'warehouse_id'=>$warehouse_id,
			'company_id'=>$company_id,
			'goods_num'=>$goods_num,
			'cost_price'=>$cost_price,
			'sales_price'=>$sales_price,
			'addby_id'=>$_SESSION['userId'],
			'add_time'=>time(),
		);

		$newmodel =  new WarehouseOutVoucherModel($id,22);
		$olddo = $newmodel->getDataObject();

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
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new WarehouseOutVoucherModel($id,22);
//		$do = $model->getDataObject();
//		$valid = $do['is_system'];
//		if($valid)
//		{
//			$result['error'] = "当前记录为系统内置，禁止删除";
//			Util::jsonExit($result);
//		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		//$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
}

?>