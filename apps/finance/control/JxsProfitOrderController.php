<?php
/**
 *  -------------------------------------------------
 *   @file		: JxsProfitOrderController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-07-17 01:39:49
 *   @update	:
 *  -------------------------------------------------
 */
class JxsProfitOrderController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('exportOrder', 'exportOrderDetail', 'searchAncCalc');
	
	private $profit_type = array(
	    '1'=>array('金条','经销商利润 = 商品克重{0} * {1}元/克'), 
	    '2'=>array('金饰品','经销商利润 = 商品克重{0} * {1}元/克'),
	    '3'=>array('铂金','经销商利润 = 商品克重{0} * {1}元/克'),
	    '4'=>array('裸钻','经销商利润 = 总销售金额{0} - 货品成本{1} - （总销售金额{0} * 返点率{2}）'),
	    '5'=>array('其他镶嵌类','经销商利润 = 总销售金额{0} - 货品成本{1} - （总销售金额{0} * 返点率{2}）'),
	    '6'=>array('其他', '暂无定义')
	);
    /*
        channel_profit_args 第一层部门，第二层类型
    */
	private $channel_profit_args = array(
	    '1'=> array('1'=> 1,'2'=>5,'3'=>10,'4'=>'16%','5'=>'16%'),
	    '2'=> array('1'=> 1,'2'=>5,'3'=>10,'4'=>'21%','5'=>'21%'),
	    '3'=> array('1'=> 1,'2'=>5,'3'=>10,'4'=>'25%','5'=>'25%'),
	    '13'=> array('1'=> 1,'2'=>5,'3'=>10,'4'=>'25%','5'=>'25%'),
	    '52'=> array('1'=> 1,'2'=>5,'3'=>10,'4'=>'16%','5'=>'16%'),
	    '71'=> array('1'=> 1,'2'=>5,'3'=>10,'4'=>'24%','5'=>'24%')
	);

	private $channel_profit_args20151103 = array(
	    '1'=> array('1'=> 1,'2'=>5,'3'=>10,'4'=>'21%','5'=>'21%'),
	    '2'=> array('1'=> 1,'2'=>5,'3'=>10,'4'=>'26%','5'=>'26%'),
	    '3'=> array('1'=> 1,'2'=>5,'3'=>10,'4'=>'30%','5'=>'30%'),
	    '13'=> array('1'=> 1,'2'=>5,'3'=>10,'4'=>'30%','5'=>'30%'),
	    '52'=> array('1'=> 1,'2'=>5,'3'=>10,'4'=>'21%','5'=>'21%'),
	    '71'=> array('1'=> 1,'2'=>5,'3'=>10,'4'=>'29%','5'=>'29%')
	);

    /**
	 *	index，搜索框
	 */
	public function index ($params)
	{
	    $api = new ApiManagementModel();
	    $jxs_list = $api->getJxslist();
		$this->render('jxs_profit_order_search_form.html',array('bar'=>Auth::getBar(), 'jxs_list'=>$jxs_list));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
        if(empty($params['jxs_id'])){
            exit("请至少选择一家店");
        }
        list($where,$args) = $this->getWhereCondition($params, __FUNCTION__);

        $page = _Request::getInt("page",1);
        $page_size =  _Request::getInt("page_size", 50);
        
		$model = new JxsProfitOrderModel(29);
        $data = $model->pageList($where,$page,$page_size,false);
        $pageData = $data;

        if (!empty($pageData['data'])) {
            $api = new ApiManagementModel();
            $order_sources = $api->GetSalesChannelsByIds(array_column($pageData['data'],'department_id'));
            
    		$order_ids = array_column($pageData['data'],'order_id');
            $detail_model = new JxsOrderDetailModel(29);
            $order_details = $detail_model->findByOrderID($order_ids);
            foreach($data['data'] as $key => $order){
                foreach ($order_sources as $source) {
                    if ($order['department_id'] == $source['id']) {
                        $data['data'][$key]['department_name'] = $source['channel_name'];
                        break;
                    }
                }
                $data['data'][$key]['details'] = [];
                foreach($order_details as $sub_key => &$detail){
                    if($order['order_id'] == $detail['order_id']){
                        $data['data'][$key]['details'][] = $this->getProfitTypeDesc($order['department_id'], $detail,$order['calc_date']);
                        unset($order_details[$sub_key]);
                    }
                }
            }
        }
		
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'jxs_profit_order_search_page';

		$this->render('jxs_profit_order_search_list.html',array(
			'pa'=>Util::page($pageData),
		    'show_checkbox' => empty($where['profit_id']),
			'page_list'=>$data
		));
	}
	
	public function batchCalcOrder($params) {
	    $now = date('Y-m-d H:i:s',time());
	    $result = array('success' => 0, 'error' => '');
		if (!isset($params['_ids']) || empty($params['_ids'])) {
			$result['error'] = '参数异常';
			Util::jsonExit($result);
		}

        //结算单
        //$params['_ids'];
		$profit_order_model = new JxsProfitOrderModel(29);
        $orderList = $profit_order_model->getOrderByWhere($params);

        $jsList = array();
        if(!empty($orderList)){
            $jxsList = array();
            $sum_profit = 0;
            foreach($orderList as $key => $val){
                $calc_status = $val['calc_status'];
                if($calc_status == 1){
                    $result['error'] = '单据已经结算，请刷新后尝试!';
                    Util::jsonExit($result);
                }
                $jxsList[] = $val['jxs_id'];
                $id = $val['id'];
                $order_id = $val['order_id'];
                $goods_id = $val['goods_id'];
                $order_sn = $val['order_sn'];
                $calc_profit = $val['calc_profit'];
                if(!isset($jsList[$id]['order_sn'])){
                    $jsList[$id]['order_sn'] = $order_sn;

                }
                if(!isset($jsList[$id]['calc_profit'])){
                    $jsList[$id]['calc_profit'] = 0;
                }
                $jsList[$id]['date'] = $now;
                $jsList[$id]['goods_id'][] = $goods_id;

                if(!$profit_order_model->getD($order_sn,$goods_id)){
                    $jsList[$id]['calc_profit'] += $calc_profit;
                }
            }
            foreach($jsList as $key => $val){
                if($val['calc_profit'] < 0 ){
                    $jsList[$key]['calc_profit'] = 0;
                }
                $sum_profit += $jsList[$key]['calc_profit'];
            }
        }else{
            $result['error'] = '单据信息不存在，请刷新后尝试!';
            Util::jsonExit($result);
        }

		
		if (!isset($params['__jxs_id___']) || empty($params['__jxs_id___'])) {
            $jxsList = array_unique($jxsList);
    	    if (empty($jxsList) || count($jxsList) != 1) {
    	        $result['error'] = '经销商数据异常';
    	        Util::jsonExit($result);
    	    }
            $jxs = $jxsList[0];
		} else {
		    $jxs = $params['__jxs_id___'];
		}
	    
	    $newdo=array();
	    
	    $newdo['created_date'] = $now;
	    $newdo['created_by'] = $_SESSION['userName'];
	    $newdo['calc_date'] = $now;
	    $newdo['jxs_id'] = $jxs;
	    $newdo['status'] = 1;
	    $newdo['calc_profit'] = $sum_profit;

	    $newmodel = new JxsProfitBillModel(30);
	    $pdo = $newmodel->db()->db();
	    $pdo->beginTransaction();
	    
	    $res = $newmodel->saveData($newdo,array());
	    if($res !== false)
	    {
            $flag = $profit_order_model->updateCalc($jsList,$res);
	        if ($flag !== false) {
	            $pdo->commit();
	            $result['success'] = 1;
	            Util::jsonExit($result);
	        } 
	    }
	    
        $pdo->rollBack();
        $result['error'] = '结算失败';
        Util::jsonExit($result);
	}
	
	public function searchAndCalc($params) {
	    list($where) = $this->getWhereCondition($params, __FUNCTION__);
	    $where['ex_calced'] = true;
	    $model = new JxsProfitOrderModel(30);
	    $data = $model->pageList($where,0,PHP_INT_MAX,false);
	    
	    if (!empty($data['data'])) {	    
	        $_ids = array_column($data['data'],'id');
	        $this->batchCalcOrder(array('_ids' => $_ids, '__jxs_id___' => $where['jxs_id']));
	    }
	    
	    Util::jsonExit(array('success' => 0, 'error' => '没有找到符合结算条件的订单'));
	}
	
	public function exportOrder($params) {
	    list($where) = $this->getWhereCondition($params, __FUNCTION__);
	    
	    $model = new JxsProfitOrderModel(29);
	    $data = $model->pageList($where,0,50000,false);
	    
	    if (!empty($data['data'])) {
	        $api = new ApiManagementModel();
	        $order_sources = $api->GetSalesChannelsByIds(array_column($data['data'],'department_id'));
	        $jxs = $api->getJxslist(array($params['jxs_id']));

	        $xls_content = "经销商,订单号,商品数量,订单来源,下单时间,发货时间,利润金额,结算金额,结算状态,收货地址\r\n";
	        foreach($data['data'] as $key => $order){
	            foreach ($jxs as $j) {
	                if ($j['id'] == $order['jxs_id']) {
	                    $xls_content .= $j['shop_name'] . ",";
	                    break;
	                }
	            }
	            $xls_content .= $order['order_sn'] . ",";
	            $xls_content .= $order['item_count'] . ",";
	            foreach ($order_sources as $source) {
	                if ($order['department_id'] == $source['id']) {
	                    $xls_content .= $source['channel_name'] . ",";
	                    break;
	                }
	            }
	            $xls_content .= $order['create_time'] . ",";
	            $xls_content .= $order['send_goods_time'] . ",";
	            $xls_content .= $order['calc_profit'] . ",";
	            $xls_content .= $order['real_profit'] . ",";
	            switch ($order['calc_status']) {
	                case '1': $xls_content .= "已结算,";break;
	                case '2': $xls_content .= "已取消,";break;
	                default: $xls_content .= "未结算,";break;
	            }
	            $xls_content .= $order['address'] . "\n";
	        }
	    } else {
	        $xls_content = '没有数据！';
	    }
	    
	    header("Content-type: text/html; charset=gbk");
	    header("Content-type:aplication/vnd.ms-excel");
	    header("Content-Disposition:filename=" . iconv("utf-8", "utf-8", "经销商利润订单" . date("Y-m-d")) . ".csv");
	    echo iconv("utf-8", "gbk", $xls_content);
	    exit;
	}
	
	public function exportOrderDetail($params) {
	    list($where) = $this->getWhereCondition($params, __FUNCTION__);
	    
	    $model = new JxsProfitOrderModel(29);
	    $data = $model->pageList($where,0,50000,false);
	    
	    if (!empty($data['data'])) {
	        $api = new ApiManagementModel();
	        $order_sources = $api->GetSalesChannelsByIds(array_column($data['data'],'department_id'));
	        $jxs = $api->getJxslist(array($params['jxs_id']));
	        
	        $style_api = new ApiStyleModel();
	        $cats = $style_api->GetCatTypes();
	        $product_types = $style_api->GetProductTypes();
	        
	        $order_ids = array_column($data['data'],'order_id');
	        $detail_model = new JxsOrderDetailModel(29);
	        $order_details = $detail_model->findByOrderID($order_ids);
	        
	        $xls_content = "经销商,订单号,订单来源,下单时间,货号,金重,产品线,分类,名义成本,销售价,结算类别,利润金额,结算公式\r\n";
	        foreach($data['data'] as $key => $order){
	            foreach($order_details as $sub_key => &$detail){
	                if($order['order_id'] == $detail['order_id']){
	                    
	                    foreach ($jxs as $j) {
	                        if ($j['id'] == $order['jxs_id']) {
	                            $xls_content .= $j['shop_name'] . ",";
	                            break;
	                        }
	                    }
	                    $xls_content .= $order['order_sn'] . ",";
	                    foreach ($order_sources as $source) {
	                        if ($order['department_id'] == $source['id']) {
	                            $xls_content .= $source['channel_name'] . ",";
	                            break;
	                        }
	                    }
	                    $xls_content .= $order['create_time'] . ",";
	                    $xls_content .= $detail['goods_id'] . ",";
	                    $xls_content .= $detail['jinzhong'] . ",";
	                    foreach ($product_types as $p) {
	                        if ($detail['product_type'] == $p['product_type_id']) {
	                            $xls_content .= $p['product_type_name'] . ",";
	                            break;
	                        }
	                    }
	                    foreach ($cats as $c) {
	                        if ($detail['cat_type'] == $c['cat_type_id']) {
	                            $xls_content .= $c['cat_type_name'] . ",";
	                            break;
	                        }
	                    }
	                    $xls_content .= $detail['cost_price'] . ",";
	                    $xls_content .= $detail['trading_price'] . ",";
	                    
	                    $detail = $this->getProfitTypeDesc($order['department_id'], $detail,$order['calc_date']);
	                    $xls_content .= $detail['profit_name'] . ",";
	                    $xls_content .= $detail['calc_profit'] . ",";
	                    $xls_content .= $detail['profit_desc'] . "\n";

	                    unset($order_details[$sub_key]);
	                }
	            }
	        }
	    } else {
	        $xls_content = '没有数据！';
	    }
	    
	    header("Content-type: text/html; charset=gbk");
	    header("Content-type:aplication/vnd.ms-excel");
	    header("Content-Disposition:filename=" . iconv("utf-8", "utf-8", "经销商利润订单明细" . date("Y-m-d")) . ".csv");
	    echo iconv("utf-8", "gbk", $xls_content);
	    exit;
	}
	
	private  function getWhereCondition($params, $func) {
	    
	    $args = array(
	        'mod'	=> _Request::get("mod"),
	        'con'	=> substr(__CLASS__, 0, -10),
	        'act'	=> $func,
	        'jxs_id'=> _Request::getInt("jxs_id"),
	        'calc_status'=> _Request::getInt("calc_status"),
	        'start_time'=> _Request::getString("start_time"),
	        'end_time'=> _Request::getString("end_time"),
	        'profit_id' => _Request::getInt("profit_id"),
	        'profit_type' => _Request::getInt('profit_type'),
	        'department_id' => _Request::getInt('department_id'),
	        'start_money' => _Request::getFloat('start_money'),
	        'end_money' => _Request::getFloat('end_money')
	    );
	    
	    $where = array();
        $where['jxs_id'] = $args['jxs_id'];
        if ($args['calc_status'] >= 0) $where['calc_status'] = $args['calc_status'];
        $where['profit_id'] = $args['profit_id'];
        $where['start_time'] = $args['start_time'];
        $where['end_time'] = $args['end_time'];
        $where['department_id'] = $args['department_id'];
        $where['profit_type'] = $args['profit_type'];
        $where['start_money'] = $args['start_money'];
        $where['end_money'] = $args['end_money'];
        
        return [$where,$args];
	}
	
	private function getProfitTypeDesc($dept, &$detail, $calc_date) {
        if(is_null($calc_date) || $calc_date == '0000-00-00 00:00:00' || $calc_date >= '2015-11-03 23:59:59'){
            $updateprofit_type = $this->channel_profit_args20151103[$dept][$detail['profit_type']];
        }else{
            $updateprofit_type = $this->channel_profit_args[$dept][$detail['profit_type']];
        }


	    $profit_name = '';
	    $profit_desc = '';
	    if (!empty($detail['profit_type'])) {
	         switch (intval($detail['profit_type'])) {
	             case 1:
	             case 2:
	             case 3:
	                 $profit_name = $this->profit_type[$detail['profit_type']][0];
	                 $profit_desc = $this->format($this->profit_type[$detail['profit_type']][1], $detail['jinzhong'], $updateprofit_type);
	                 break;
	             case 4:
	             case 5:
	                 $profit_name = $this->profit_type[$detail['profit_type']][0];
	                 $profit_desc = $this->format($this->profit_type[$detail['profit_type']][1], $detail['trading_price'], $detail['cost_price'], $updateprofit_type);
	                 break;
	             case 6:
	                 $profit_name = $this->profit_type[$detail['profit_type']][0];
	                 $profit_desc = $this->profit_type[$detail['profit_type']][1];
	             default:
	                 break;
	         }
	    }

	    $detail['profit_name'] = $profit_name;
	    $detail['profit_desc'] = $profit_desc;
	    return $detail;
	}
	
	private function format() {
	    $args = func_get_args();
	    if (count($args) == 0) {
	        return;
	    }
	    if (count($args) == 1) {
	        return $args[0];
	    }
	    $str = array_shift($args);
	    $str = preg_replace_callback('/\\{(0|[1-9]\\d*)\\}/', create_function('$match', '$args = '.var_export($args, true).'; return isset($args[$match[1]]) ? $args[$match[1]] : $match[0];'), $str);
	    return $str;
	}

}

?>