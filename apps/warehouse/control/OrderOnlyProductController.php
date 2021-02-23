<?php
/**
 *  -------------------------------------------------
 *   @file		: OrderBatchFqcController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: zhangruiying
 *   @date		: 2015-01-14 11:04:15
 *   @update	:
 *
	质检订单号限制：已配货状态 未发货或已到店状态 才能质检
	质检通过：需要验证是否生成了销售单  发货状态改为允许发货
	质检未通过：销售单取消 配货状态改为配货中
 *  -------------------------------------------------
 */
class OrderOnlyProductController extends CommonController
{
	protected $smartyDebugEnabled = true;
	protected $whitelist = array("search","daochu","printCode");
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('order_only_product_search_form.html',array(
			'bar'=>Auth::getBar(),
		));
	}
	function search()
	{
	    $order_sn=_Request::get('ids');	   
	    $order_sn=str_replace("\n", ",", $order_sn);
		$down_info= _Request::get('down_info')?_Request::get('down_info'):'';
		$orderArr=array();		
		if(!empty($order_sn)) $orderArr=explode(',', $order_sn);		
		$order_str='';
		foreach ($orderArr as $k=>$v){
			if($k==0){
				$order_str.="'$v'";
			}else{
				$order_str.=",'$v'";
			}
			
			
		}
	
	
		$to_company_id=58;
		$to_warehouse_id=523;
		$bill_status='1,2';
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where=array(
			'order_sn' => $order_str,
			'to_company_id' => $to_company_id,	
			'to_warehouse_id' => $to_warehouse_id,
			'bill_status' => $bill_status
				
		);
		$model = new OrderOnlyProductModel(21);
		//print_r($where) ;exit;
		if($down_info=='down_info'){
			
			$data = $model->pageList($where,$page,90000000,false);						
			//echo '<pre>';print_r($data['data']);echo '</pre>';die;
			$this->download($data);
			exit;
		}
		
		$data = $model->pageList($where,$page,4000,false);
		//print_r($data);exit;
		$this->render('order_only_product_search_list.html',array(
				
				'page_list'=>$data,	'SYS_SCOPE'=>SYS_SCOPE	
			
		));
		 
	}
	
	
	
	
	//导出
	public function download($data) {		
		if ($data['data']) {
	   
			$down = $data['data'];
			$xls_content = "订单号,调拨单号,订单支付状态,货号,款号,货号所在仓库,货号状态\r\n";
			
			foreach ($down as $key => $list2) {
				foreach ($list2 as $key1 => $val) {
					$xls_content .= $val['order_sn']. ",";
					$xls_content .= $val['bill_no']. ",";
					$xls_content .= $val['pay_status']. ",";
					$xls_content .= $val['goods_id']. ",";
					$xls_content .= $val['goods_sn']. ",";
					$xls_content .= $val['warehouse']. ",";
					$xls_content .= $val['is_on_sale']. "\n";
				}
			}
		} else {
			$xls_content = '没有数据！';
		}
	
		header("Content-type:text/csv;charset=gbk");
		header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv");
		header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
		header('Expires:0');
		header('Pragma:public');
		echo iconv("utf-8", "gbk//IGNORE", $xls_content);
	
		exit;
	
	}
	
	
}

?>