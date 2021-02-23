<?php
/**
 *  -------------------------------------------------
 *   @file		: BoxGoodsLogController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 19:06:59
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillTjController extends CommonController
{
	protected $smartyDebugEnabled = true;
	protected $whitelist = array("search");

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('warehouse_bill_tj_search_form.html',array(
			'bar'=>Auth::getBar(),
			'dd'=> new DictView(new DictModel(1))
		));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$args = array(
			'mod'				=> _Request::get("mod"),
			'con'				=> substr(__CLASS__, 0, -10),
			'act'				=> __FUNCTION__,
		);
		$where = array(

		);
		$model = new WarehouseBillModel(21);
		$data = $model->getBillByBillstatus();

		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_bill_tj_search_page';
		$this->render('warehouse_bill_tj_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'admin_name'=>$_SESSION['userName'],
			'dd'=> new DictView(new DictModel(1)),
            'view' => new WarehouseBillView(new WarehouseBillModel(21))
		));
	}

}

?>