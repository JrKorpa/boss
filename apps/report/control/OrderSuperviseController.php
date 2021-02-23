<?php
/**
 *  -------------------------------------------------
 *   @file		: OrderSuperviseController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-08-28 16:44:29
 *   @update	:
 *  -------------------------------------------------
 */
class OrderSuperviseController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('order_supervise_search_form.html',array('bar'=>Auth::getBar()));
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
            //'参数' = _Request::get("参数");
        );
		$model = new WarehouseBillModel(21);
		$data = $model->getSuperviseBill();
		$this->render('order_Supervise_search_list.html',array(
			'page_list'=>$data
		));
	}


}

?>