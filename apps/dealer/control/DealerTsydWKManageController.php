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
class DealerTsydWKManageController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */

	public function index ($params)
	{
		//批发客户
		$SelfWarehouseGoodsModel = new SelfWarehouseGoodsModel(21);
		$whoList = $SelfWarehouseGoodsModel->select('jxc_wholesale', ' `wholesale_id` , `wholesale_sn` , `wholesale_name` ' ,  " `wholesale_status` = 1 " , 'all');
		
		$this->render('dealer_tsyd_manage_wk_search_form.html',array(
				'bar'=>Auth::getBar(),
				'whoList'=>$whoList,
				
		));
	}
	
	public function search ($params)
	{
	    $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
            $args = array(
                'mod'	=> _Post::get("mod"),
                'con'	=> substr(__CLASS__, 0, -10),
                'act'	=> __FUNCTION__,
                'bill_no' => _Request::get('bill_no'),
                'to_customer_id'  => _Request::get('to_customer_id'),  
            	'confirm_delivery'  => _Request::get('confirm_delivery'),
                'start_time' => _Request::get('start_time'),
                'end_time'   => _Request::get('end_time'),
                'page' => $page

                );
			//var_dump( $args);exit;
            $where=array(
            	'bill_no'=>$args['bill_no'],
            	'to_customer_id'=>$args['to_customer_id'],
            	'confirm_delivery'=>$args['confirm_delivery'],
            	'start_time'=>$args['start_time'],
            	'end_time'=>$args['end_time'],            	
            	'is_tsyd'=>1,
            	//'bill_status'=>2,
            	'page'=>$args['page'],
            );  
            $model = new SelfWarehouseGoodsModel(21);
            $data  = $model->GetBillInfo($where);
          
            $pageData = $data['data'];
            
            //批发客户
           
            $whoList = $model->select('jxc_wholesale',' `wholesale_id` , `wholesale_sn` , `wholesale_name` ' ," `wholesale_status` = 1 " ,  'all');
            $wholesaleArr=array();
            foreach ($whoList as $v){
            	$wholesaleArr[$v['wholesale_id']]=$v['wholesale_name'];
            }

            $lists = $pageData;  
            //print_r($lists);   
            //总计
            $receivablesCount=0;
            $moneyPaidCount=0;
            $balanceCount=0;
           
            $zongjiArr=array();//总记
            foreach ($lists as $keys => $val) {
               $rowss=$model->getBillGoodsArr($val['id'],$val['confirm_delivery']);
               $list=$rowss['list'];
               foreach($list as $l){
               	   $zongjiArr[$l['order_sn']]['receivables']=$l['receivables'];
               	   $zongjiArr[$l['order_sn']]['money_paid']=$l['money_paid'];
               	   $zongjiArr[$l['order_sn']]['balance']=$l['balance'];
   
               }
               if(empty($list)){
               	  unset($lists[$keys]);
               	  continue;
               }else{
               	  $lists[$keys]['list']=$list;
               	  $lists[$keys]['receivablesCount']=$rowss['receivablesCount'];
               	  $lists[$keys]['moneyPaidCount']=$rowss['moneyPaidCount'];
               	  $lists[$keys]['balanceCount']=$rowss['balanceCount'];
               }
               $lists[$keys]['customer']=$wholesaleArr[$val['to_customer_id']];
              
            }
            $receivablesCount=0;
            $moneyPaidCount=0;
            $balanceCount=0;
            foreach ($zongjiArr as $v){
            	$receivablesCount += $v['receivables'];
            	$moneyPaidCount += $v['money_paid'];
            	$balanceCount += $v['balance'];
            }
            
              
                $page_list=array_slice($lists,($page-1)*10,10);
                $pageData['pageSize']=10;
                $pageData['filter'] = $args;
                $pageData['recordCount'] = count($lists);
                $pageData['jsFuncs'] = 'dealer_tsyd_manage_wk_search_page';
                $this->render('dealer_tsyd_manage_wk_search_list.html',array(
                            'pa'=>Util::page($pageData),
                            'page_list'=>$page_list,
                		    'receivablesCount'=>$receivablesCount,
                		    'moneyPaidCount'=>$moneyPaidCount,
                		    'balanceCount'=>$balanceCount,
                            'dd'=>new DictView(new DictModel(1)),
                            ));
           
	}
	
	
}

?>