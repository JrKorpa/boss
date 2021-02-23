<?php
/**
 *  -------------------------------------------------
 *   @file		: DealerCustomerManageController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: zhangzhimin
 *   @date		: 2015-12-15 11:51:21
 *   @update	:
 *  -------------------------------------------------
 */
class DealerTsydYFManageController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('search');

	
	public function index ($params)
	{
	    $SalesChannelsModel = new SalesChannelsModel(1);
        if($_SESSION['userType'] == 1){
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
        }else{
            $ids = explode(',', $_SESSION['qudao']);
            $channellist = $SalesChannelsModel->getSalesChannel($ids);
        }
		$this->render('dealer_tsyd_manage_yf_search_form.html',array(
				'bar'=>Auth::getBar(),
				'sales_channels_idData' => $channellist,
	
		));
	}

	public function search ($params)
	{
		 $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$args = array(
				'mod'	=> _Post::get("mod"),
				'con'	=> substr(__CLASS__, 0, -10),
				'act'	=> __FUNCTION__,
				'order_sn' => _Request::get('order_sn'),
				'consignee'  => _Request::get('consignee'),
				'mobile'  => _Request::get('mobile'),
				'order_department'  => _Request::get('order_department'),
				'order_status'  => _Request::get('order_status'),
				'order_pay_status'  => _Request::get('order_pay_status'),
				'start_time' => _Request::get('start_time'),
				'end_time'   => _Request::get('end_time'),
				'down_info'			=> _Request::get('down_info')?_Request::get('down_info'):'',
				//'page' => $page,
	
		);
		//var_dump( $args);exit;
		$where=array(
				'order_sn'=>$args['order_sn'],
				'consignee'=>$args['consignee'],
				'mobile'=>$args['mobile'],
				'order_department'=>$args['order_department'],
				'order_status'=>$args['order_status'],				
				'start_time'=>$args['start_time'],
				'end_time'=>$args['end_time'],
				'order_pay_status'=>$args['order_pay_status'],
				'referer'=>'天生一对加盟商',
				//'page'=>$args['page'],
				
		);
		$SalesModel = new SalesModel(27);
		$SelfWarehouseGoodsModel = new SelfWarehouseGoodsModel(21);
		$data  = $SalesModel->GetOrderList($where);
	    foreach ($data as $keys => $val) {	
	    	$order_pay_status=$val['order_pay_status'];    
	    	$order_id=$val['id'];
	    	$money_paid=$val['money_paid'];//已付金额
	    	$order_amount=$val['order_amount'];//订单金额
	    	$real_return_price=$val['real_return_price'];
	    	//订单已配货商品的批发价 
	    	$pfj=$SelfWarehouseGoodsModel->getGoodsPfj($order_id);
	    	//$data[$keys]['pfj']=$pfj;
	    	//非已配货商品的（【原始零售价】）	    	
	    	$retail_price=$SalesModel->getOrderDetailRetailPriceByOrderId($order_id);
	    	//$data[$keys]['retail_price']=$retail_price;
	    	//余额
	    	$balance=($pfj+$retail_price*0.3)-($money_paid-$real_return_price);
             
             if($order_pay_status != 1 && $balance>=0){
               	  unset($data[$keys]);
               	  continue;
             }else{
	             if($order_pay_status != 1){
	                	$data[$keys]['receivables']='';
	                	$data[$keys]['balance']=sprintf("%.2f",$balance);
	              }else{
	              	  $data[$keys]['receivables']=sprintf("%.2f",$order_amount*0.3);
	              	  $data[$keys]['balance']='';
	              }	 
               }
              
            }
            //var_dump($data);
		if($args['down_info']=='down_info'){
			//$data = $SalesModel->GetOrderListPage($where,$page,90000000,false);
			//print_r($data);exit;
			$this->download($data);
			exit;
		}
		//$data  = $SalesModel->GetOrderListPage($where,$page);
		//echo $data;exit;
		
		 $page_list=array_slice($data,($page-1)*10,10);
         $pageData['pageSize']=10;
         $pageData['filter'] = $args;
         $pageData['recordCount'] = count($data);
         $pageData['jsFuncs'] = 'dealer_tsyd_manage_yf_search_page';
         $this->render('dealer_tsyd_manage_yf_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $page_list,
         	'dd'=>new DictView(new DictModel(1)),
        ));
	}
	
	
	
	public function download($data) {	
		$dd =new DictModel(1);
			if ($data) {
				$down = $data;
				$xls_content = "订单号,客户姓名,顾问,订单日期,订单金额,已付金额,应收定金,余额,订单状态,支付状态\r\n";
				foreach ($down as $key => $val) {
					$xls_content .= $val['order_sn']. ",";
					$xls_content .= $val['consignee']. ",";
					$xls_content .= $val['create_user'] . ",";
					
					$xls_content .= $val['create_time'] . ",";
					$xls_content .= $val['order_amount'] . ",";
					$xls_content .= $val['money_paid']. ",";
					$xls_content .= $val['receivables']. ",";
					$xls_content .= $val['balance']. ",";
					$xls_content .= $dd->getEnum('order.order_status',$val['order_status']) . ",";
					$xls_content .= $dd->getEnum('order.order_pay_status',$val['order_pay_status']) . ",\n";

				}
			} else {
				$xls_content = '没有数据！';
			}
	
		
	
		header("Content-type: text/html; charset=gbk");
		header("Content-type:aplication/vnd.ms-excel");
		header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv");
		echo iconv("utf-8", "gbk", $xls_content);
	
	}
	
}

?>