<?php
/**
 *  -------------------------------------------------
 *   @file		: JxsProfitBillController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-07-17 00:49:36
 *   @update	:
 *  -------------------------------------------------
 */
class JxsProfitBillController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
	    $api = new ApiManagementModel();
	    $jxs_list = $api->getJxslist();
		$this->render('jxs_profit_bill_search_form.html',array('bar'=>Auth::getBar(),'jxs_list'=>$jxs_list));
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
			'jxs_id'=> _Request::getInt("jxs_id"),
            'status'=> _Request::getInt("status"),
            'start_time'=> _Request::getString("start_time"),
            'end_time'=> _Request::getString("end_time")
		);
		$page = _Request::getInt("page",1);
		$where = array();
        $where['jxs_id'] = $args['jxs_id'];
        $where['status'] = $args['status'];
        $where['start_time'] = $args['start_time'];
        $where['end_time'] = $args['end_time'];

		$model = new JxsProfitBillModel(29);
		$data = $model->pageList($where,$page,50,false);
		
		$api = new ApiManagementModel();
		$jxs_list = $api->getJxslist();
		foreach ($jxs_list as $val) {
		    $jxs[$val['id']] = $val;
		}
		
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'jxs_profit_bill_search_page';
		$this->render('jxs_profit_bill_search_list.html',array(
			'pa'=>Util::page($pageData),
		    'jxs' => $jxs,
			'page_list'=>$data
		));
	}

	/**
	 *	cacel，取消结算
	 */
	public function batchCancel($params)
	{
		$result = array('success' => 0,'error' =>'');
		if (!isset($params['_ids']) || empty($params['_ids'])) {
		    $result['error'] = '参数异常';
		    Util::jsonExit($result);
		}
		
		$model = new JxsProfitOrderModel(30);
		$pdo = $model->db()->db();
		$pdo->beginTransaction();
		
	    $flag = $model->cancelCalculatedOrder($params['_ids']);
	    if ($flag === false) {
	        $pdo->rollBack();
	        $result['error'] = '操作失败';
	        Util::jsonExit($result);
	    }
	    
	    $bill_model = new JxsProfitBillModel(30);
	    $flag = $bill_model->cancelBill($params['_ids']);
	    if ($flag === false) {
	        $pdo->rollBack();
	        $result['error'] = '操作失败';
	        Util::jsonExit($result);
	    }
		
	    $result['success'] = 1;
	    $pdo->commit();
		Util::jsonExit($result);
	}
	
	public function show($params) {
	    $id = intval($params["id"]);
	    $model = new JxsProfitBillModel($id,29);
	    $data = $model->getDataObject();
	    if ($data) {
	       $api = new ApiManagementModel();
	       $jxs = $api->getJxslist(array($data['jxs_id']));
	    
    	   $this->render('jxs_profit_bill_show.html',array(
    	        'view'=>new JxsProfitBillView($model),
    	        'jxs_name' => $jxs[0]['shop_name'],
    	        'bar'=>Auth::getViewBar()
    	   ));
	    }
	}
}

?>