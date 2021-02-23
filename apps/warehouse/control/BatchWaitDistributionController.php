<?php
/**
 * 订单批量换货控制器
 *  -------------------------------------------------
 *   @file		: WarehouseBillController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: gaopeng
 *   @date		: 2015-10-19
 *   @update	:
 *
	质检订单号限制：已配货状态 未发货状态 才能质检
	质检通过：需要验证是否生成了销售单  发货状态改为允许发货
	质检未通过：销售单取消 配货状态改为配货中
 *  -------------------------------------------------
 */
class BatchWaitDistributionController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }

		$this->render('batch_exchange_distribution_search_form.html',array(
			'bar'=>Auth::getBar(),
		));
	}
	
	/**
	 * 过滤提交的批量订单号
	 */
	private function _getOrderSnList($params){
	    
	    $result = array('success'=>'','error'=>'','search_logs'=>'','content'=>'');
 	    if(empty($params['order_sn'])){
	        $result['search_logs'] = "批量订单ID为空";
	        Util::jsonExit($result);
	    }
	
	    $order_sn_split = explode("\n",$params['order_sn']);
	     
	    //获取最终合法订单编号
	    $order_sn_list = array();
	    foreach($order_sn_split as $vo){
	        //获取不为空和不重复的订单id
	        if(trim($vo)!='' && !in_array($vo,$order_sn_list)){
	            $order_sn_list[]=trim($vo);
	        }
	    }
	    if(empty($order_sn_list)){
	        $result['search_logs'] = "批量合法订单ID为空";
	        Util::jsonExit($result);
	    }
	    return $order_sn_list;
	}
	/**
	 * 搜索
	 */
	public function search($params){
	    
	    $result = array('success'=>'','error'=>'','search_logs'=>'','content'=>'');	    
	    $order_sn_list = $this->_getOrderSnList($params);
	    
	    $salesModel = new SalesModel(27);
	    $warehouseGoodsModel = new WarehouseGoodsModel(21);
	    $errors     = array();
	    $goods_list = array();
	    $wholesale_id='天生一对批发客户';
	    foreach($order_sn_list as $key=>$order_sn){
	        $error = false;
	        $baseOrderInfo = $salesModel->getBaseOrderInfoByOrderSn($order_sn);
	        $order_id        = $baseOrderInfo['id'];
	        $delivery_status = $baseOrderInfo['delivery_status'];
	        $apply_close     = $baseOrderInfo['apply_close'];
	        $apply_return    = $baseOrderInfo['apply_return'];
	        $order_status    = $baseOrderInfo['order_status'];
	        if(empty($baseOrderInfo)){
	            $logs[$order_sn][] = array('msg'=>"订单不存在",'type'=>'error');
	            continue;
	        }
	        if($baseOrderInfo['referer']!='天生一对加盟商'){
	        	$logs[$order_sn][] = array('msg'=>"订单不是天生一对订单",'type'=>'error');
	        	continue;
	        }
	        $wholesale_id1=$salesModel->getWholesaleId($order_id);  
	        if(!$wholesale_id1){
	        	$logs[$order_sn][] = array('msg'=>"此订单没有批发客户",'type'=>'error');
	        	continue;
	        }  
	        if($wholesale_id !='天生一对批发客户' && $wholesale_id != $wholesale_id1){
	        	$logs[$order_sn][] = array('msg'=>"此订单和前面订单不是同一批发客户",'type'=>'error');
	        	continue;
	        }else{
	        	$wholesale_id=$wholesale_id1;
	        }
            //符合换货条件的订单：未关闭、未申请关闭、没有进行中的退款流程、非已配货、非已发货，
	        
            //0未操作,1正在退款已申请关闭
            if(!empty($apply_close)){
                $error = true;
                $logs[$order_sn][] = array('msg'=>"订单已申请关闭",'type'=>'error');
                continue;
            }
            //订单已关闭
            if($order_status==4){
                $error = true;
                $logs[$order_sn][] = array('msg'=>"订单已关闭",'type'=>'error');
                continue;
            }
            //1未操作,2正在退款
            if($apply_return!=1){
                $error = true;
                $logs[$order_sn][] = array('msg'=>"订单正在退款",'type'=>'error');
                continue;
            }
           
           
            $data = $salesModel->getAppOrderDetailsPeihuoByOrderId($order_id);
            if(empty($data)){
            	$error = true;
            	$logs[$order_sn][] = array('msg'=>"订单没有允许配货的商品",'type'=>'error');
            	continue;
            }
            if($error==true){
            	continue;
            }else{
            	$logs[$order_sn][] = array('msg'=>"查询成功!",'type'=>'success');
            }
            
            foreach($data as $k=>$v){
                $bind_goods_id = $warehouseGoodsModel->select2('goods_id',"order_goods_id='{$v['id']}'");
                $v['order_sn']  = $order_sn;
                $v['bind_goods_id']  = $bind_goods_id;
                $goods_list[] = $v;                
            }
            
	    }
        $search_logs = "查询成功!";
	    $search_logs = "订单总计:".count($logs)."<hr/>";
	    $i = 0;
        foreach($logs as $key=>$vo1){
             $i++;
             $search_logs.="{$i}.订单{$key}：";
             foreach ($vo1 as $vo2){
                 if($vo2['type']=="error"){
                     $search_logs .='【<font color="red">'.$vo2['msg'].'</font>】,';
                 }else{
                     $search_logs .= $vo2['msg'];
                 }
             }
             $search_logs =trim($search_logs,',')."<hr/>";
        }
        $result['success'] = 1;
        $result['search_logs'] = $search_logs;
	    $result['content'] = $this->fetch('batch_exchange_distribution_search_list.html',array(
            'goods_list' =>$goods_list,
	    	'wholesale_id'	=>$wholesale_id,
        ));	    
	    Util::jsonExit($result);
	}
	/**
	 * 批量换货处理
	 */
	
	
	
	/**
	 * 开始销账
	 * 1/提交的数据将 订单的明细id 与 输入的货号，提交过来。
	 * 2/根据输入的货号，到warehouse_goods库去查询order_goods_id字段的值
	 * 3/查询到的order_goods_id 与对应的订单明细id对比，比对上了，就是该订单绑定的货品，可以配货。对比不上则不是，不予配货
	 */
	public function exchangeXiaozhang($params)
	{
		//定义淘宝和京东的库存
		$departmentid = 0;
		$warehouse_tm_arr = array(482=>'淘宝黄金',484=>'淘宝素金');
		$warehouse_jd_arr = array(483=>'京东黄金',485=>'京东素金');
		$order_sns = $params['order_sns'];
		$goods_ids = $params['goods_ids'];
		$goods_sns = $params['goods_sns'];
		$orderDetailId = $params['orderDetailId'];
		$wholesale_id=$params['wholesale_id'];
		
		//接收，处理提交过来的货号
		$goods_id_str =  substr($goods_ids,1,(strlen($goods_ids)-1));
		$goods_id_arr = explode(',', $goods_id_str);
		
		//接收，处理提交过来的款号
		$goods_sns_str = ltrim($goods_sns, ',');
		$goods_sns_arr = explode(',', $goods_sns_str);
		
		//接收，处理提交过来的订单明细id
		$orderDetailId_str = ltrim($orderDetailId, ',');
		$orderDetailId_arr = explode(',', $orderDetailId_str);
		
		//接收，处理提交过来的款号
		$order_sns_str = ltrim($order_sns, ',');
		$order_sns_arr = explode(',', $order_sns_str);
		
	    if(empty(str_replace(',' , '',$goods_ids)))
		{
			$result['error'] = '请输入销账的货品';
			Util::jsonExit($result);
		}
		//var_dump($params);exit;
		if($params['from_company_id'] == "")
		{
			$result['error'] = '请选择出库公司';
			Util::jsonExit($result);
		}
		$company           = explode("|",$params['from_company_id']);
		$from_company_id   = $company[0];
		$from_company_name = $company[1];
		
	
		
		//$order_id      = $params['order_id'];                    //订单id
		//$order_sn      = $params['order_sn'];                    //订单号
		//$order_money   = $params['order_money'];				 //订单商品总额
		//$orderDetailId = $params['orderDetailId'];				 //订单货品明细id
	
		/** 【需求】张宇提  现在需要恢复以前销账规则先注释 销账准备start**/
	

		$WarehouseGoodsModel = new WarehouseGoodsModel(21);
	
		
		//$result['error'] = "$goods_id_str-非法货号-$orderDetailId_str";
		//Util::jsonExit($result);
		 
		/*
			if(count($goods_id_arr) != count($orderDetailId_arr))
			{  //如果提交的订单明细数量与 输入的货号数量不对等
			$result['error'] = '请填写所有的货号再销账！';
			Util::jsonExit($result);
			}
		*/
	
	
		$goodsWarehouseModel = new GoodsWarehouseModel(21);
		$BillInfoPModel = new WarehouseBillInfoPModel(21);
	
		$goods_warehouse_error = '';    //检测是否上架错误提示语
		$warehouse_goods_error = '';    //错误提示语
	
		$goodsInfo = array();
		$goodsList = array();
		$warehouegoods = array();   //存储仓库货品 信息容器
		$all_price_goods = 0;//此字段用来存原始成本价的总金额---用来计算销售价格
		$zhuancang_goods_arr = array();
	
		$apiModel = new ApiStyleModel();
		$goods_total=0;
		$goods_list=array();
		$goods_str='';
		$processorModel = new SelfProccesorModel(13);
		$salesModel = new SalesModel(27);
		
		foreach($goods_id_arr as $key => $goods_id)
		{
			if($goods_id==''){
				continue;
			}
			/*
				if(!$goods_id)
				{
				$result['error'] = '请填写所有的货号再销账！';
				Util::jsonExit($result);
				}
				*/
			
			if(!is_numeric($goods_id))
			{
				$result['error'] = "非法货号：<span style='color:red;'>{$goods_id}</span> 不是纯数字";
				Util::jsonExit($result);
			}
			$order_sn = $order_sns_arr[$key];
			$order_detail_id=$orderDetailId_arr[$key];
			$order_info=$salesModel->getAppOrderInfoById($order_detail_id);
			$bc_id=$order_info['bc_id'];
			$is_stock_goods=$order_info['is_stock_goods'];
			$order_pay_status=$order_info['order_pay_status'];
			$order_id=$order_info['order_id'];	
			
			if($bc_id > 0) {
				$buchan_info = $processorModel->selectProductInfo("status","id={$bc_id}",2);
				//不需布产11，已取消10，已出厂9，作废8，部分出厂7，质检完成6，质检中5，生产中4，已分配3，待分配2,初始化1
				if($buchan_info['status']!=9 && $buchan_info['status'] !=11){
					if($is_stock_goods==0 || $order_pay_status==1){
						$result['error'] = "此货3号不满足配货条件（不需布产/已出厂/[支付定金/财务备案/已付款且货品类型是现货]），不允许配货";
						Util::jsonExit($result);
					}
				}
					
					
			}elseif($is_stock_goods==0 || $order_pay_status == 1){
				$result['error'] = "此{$order_detail_id}货号不满足配货条件（不需布产/已出厂/[支付定金/财务备案/已付款且货品类型是现货]），不允许配货";
				Util::jsonExit($result);
			}
				
	
				
			$goods_info = $WarehouseGoodsModel->getGoodsByGoods_id($goods_id);
			if(!count($goods_info))
			{
				$result['error'] = "货号：<span style='color:red;'>{$goods_id}</span> 不存在，不允许配货。";
				Util::jsonExit($result);
			}
				
			$goods_total+=$goods_info['mingyichengben'];//计算总价
			//$goods_info['pifajia']=$goods_info['mingyichengben']*(1+0.08);
			$goods_info['detail_id']=$order_detail_id;
			$goods_info['order_sn']=$order_sns_arr[$key];			
			$goods_info['goods_price']=$goods_info['mingyichengben']*(1+0.08);
			$goods_list[]=$goods_info;
			$goods_str.=$goods_id.' ';
				
			//检测货品 是否是绑定这个订单的货
			$order_goods_id = $goods_info['order_goods_id'];
			//如果输入的货号 如果没有绑定的话，判断定的此款是否是可以不绑定也可以配货的
	
			//echo $goods_sns_arr[$key];exit;
			$goods_sn = $goods_sns_arr[$key];
			$style_info = $apiModel->GetStyleInfoBySn($goods_sn);
	    
			if(count($style_info) && $style_info['bang_type'] == 2)//款存在并且是不需绑定的（低值款）
			{
				//判断输入的货号和定的款是否匹配，匹配的过
				if(strtoupper($goods_info['goods_sn']) != strtoupper($style_info['style_sn']))
				{
					$result['error'] = "货号：<span style='color:red;'>{$goods_id}</span> 和订单所定款不同，不允许销账。";
					Util::jsonExit($result);
				}
				$WarehouseGoodsModel->build_goods($orderDetailId_arr[$key],$goods_id);
	
			}else{
				if(!$goods_info['order_goods_id'])
				{
					$result['error'] = "货号：<span style='color:red;'>{$goods_id}</span> 没有绑定订单，不允许配货";
					Util::jsonExit($result);
				}
				//输入的货号如果有绑定的话 就判断下面的
				if($goods_info['order_goods_id'] != $orderDetailId_arr[$key])
				{
					$result['error'] = "货号：<span style='color:red;'>{$goods_id}</span> 绑定的订单信息与订单的明细对应不上，不允许销账!<br/>";
					Util::jsonExit($result);
				}
			}
	
			//验证输入的货号是否 库存状态
			if($goods_info['is_on_sale'] != 2)
			{
				$warehouse_goods_error .= "货号：<span style='color:red;'>{$goods_id}</span> 不是库存状态，不允许销账!<br/>";
			}
				
			//验证非淘宝的订单是否销了淘宝的货品
			//商品所在的仓库
			$warehouseid = $goods_info['warehouse_id'];
			if($departmentid != 2 && in_array($warehouseid,array_keys($warehouse_tm_arr)))
			{
				$warehouse_goods_error .= "货号：<span style='color:red;'>{$goods_id}</span>
				非淘宝的订单，用了淘宝黄金/素金仓库的货品，下单不规范导致，请联系桥林是否允许售卖，该货品不允许销账!<br/>";
			}elseif($departmentid  != 71 && in_array($warehouseid,array_keys($warehouse_jd_arr)))
			{
			$warehouse_goods_error .= "货号：<span style='color:red;'>{$goods_id}</span>
			非京东的订单，用了京东黄金/素金仓库的货品，下单不规范导致，请联系任强是否允许售卖，该货品不允许销账!<br/>";
			}
	
			//根据货号，去拉取仓库里 warehoue_goods 里的商品信息，做写入销售单据明细用
			$warehouegoods_one= $WarehouseGoodsModel->getGoodsByGoods_id($goods_id);
			$warehouegoods_one['detail_id'] = $order_goods_id;
			
			
			$all_price_goods += $warehouegoods_one['yuanshichengbenjia'];//销账价格计算用
            $warehouegoods[] =  $warehouegoods_one;
	
	            ### 判断如果出库公司选中的时BDD深圳分公司，嘿嘿，不要意思，请你等会，我要去判断有没有总公司的货，有的话自动生成调拨单，给你把货从总部调到深圳分公司来 ### @BY CaoCao
	            if($from_company_id == 445 && $goods_info['company_id'] == 58)	//445|BDD深圳分公司  并且 货品在总公司 58
	            {
	            //准备要转仓的货品
	            	$zhuancang_goods_arr[] = $goods_id;
		}
		else if($goods_info['company_id'] != $from_company_id)
		{
		$result['error'] = "货号：<span style='color:red;'>{$goods_id}</span> 不是所选出库公司的货品，不允许配货。";
		Util::jsonExit($result);
		}
		$goodsInfo[] = array('goods_id'=>$goods_id, 'is_delete'=>2);
	
	} /** END froeach **/
	
		if($goods_warehouse_error)
		{
		$result['error'] = $goods_warehouse_error;
				Util::jsonExit($result);
		}
		if($warehouse_goods_error)
		{
		$result['error'] = $warehouse_goods_error;
		Util::jsonExit($result);
		}
	
		/** 2015-12-26 zzm boss-1015 **/
		/*
		if(isset($_GET['compare']) && !empty($_GET['compare'])){
		$warehouegoods_model = new WarehouseGoodsModel(21);
		$total_mingyichenben = $warehouegoods_model->getTotalMingyichengben($goods_id_str);
		if($order_money < $total_mingyichenben){
		$result['error'] = "订单金额低于总成本，是否继续？";
		$result['compare'] = 1;
		Util::jsonExit($result);
		}
		}
	   */
	
		/****对货品明细数据中 销售价格进行计算 并放入数组中  start****/
		//echo $order_money."<br>";
		$price_sum_last = 0;//用于存除最后一个商品的所以商品成本价格总和
		for ($i=0;$i<count($warehouegoods);$i++)
			{
			 if($i==count($warehouegoods)-1){
			 $warehouegoods[$i]['xiaoshoujia'] = $order_money-$price_sum_last;
     	}else{
			 $warehouegoods[$i]['xiaoshoujia'] =  round(( $warehouegoods[$i]['yuanshichengbenjia']*$order_money)/$all_price_goods,2);
			 $price_sum_last += $warehouegoods[$i]['xiaoshoujia'];
			 }
				// echo  $warehouegoods[$i]['xiaoshoujia']."<br>" ;
		}

	//$wholesale_id=$WarehouseGoodsModel->getWholesaleIdByName($wholesale_name);
				
	$pifajia=$goods_total*(1+0.08);
	//配货发货
	$bill_info=array(
			'goods_num'	=>count($goods_list),
			'bill_note'=>'销账自动批发销售(经销商天生一对)',
			'goods_total'=>$goods_total,
			'shijia'=>'0.0',
	        'pifajia'=>$pifajia,
	        'from_company_id'=>$from_company_id,
	        'from_company_name'=>$from_company_name,
			'wholesale_id'=>$wholesale_id	,
			
	);
	// $res = $BillInfoSModel->createBillInfoS($order_id, $order_sn, $goodsInfo, $order_money, $warehouegoods, $from_company_id, $from_company_name, $zhuancang_goods_arr);
	$res = $BillInfoPModel->createBillInfop($bill_info , $goods_list,$order_sn);
	if(!$res['success'])
	{
	 $result['error'] = $res['error'];
	 $result['compare'] = 0;
	}
	else
	{
	$result = array('success'=>1, 'error'=>'销账成功'."  ".$showMsg , 'goods_str' => $goods_str, 'compare' => 0);
			 //修改可销售商品状态
			 $change=[];$where=[];
			 foreach ($goods_id_arr as $k => $v)
				{
				$where[$k]['goods_id'] = $v;
				$change[$k]['is_sale'] = '0';	//下架
				$change[$k]['is_valid'] = '2';	//已销售
				}
	
						$ApiSalePolcy = new ApiSalepolicyModel();
				$ApiSalePolcy->setGoodsUnsell($change,$where);
	
				//同步新货号到订单明细
				foreach ($goods_list as $v){
					    $detailAccountArr=$salesModel->getOrderAccountByDetailId($v['detail_id']);
					    $order_id=$detailAccountArr['order_id'];
					    $goods_price=$detailAccountArr['goods_price'];	
					    $favorable_price=$detailAccountArr['favorable_price'];
					    $order_amount=$detailAccountArr['order_amount']-$goods_price+$v['goods_price']+$favorable_price;//订单总金额
					    $money_unpaid=$order_amount-$detailAccountArr['money_paid']+$detailAccountArr['real_return_price'];//订单未付
					    $money_unpaid=$money_unpaid >= 0 ?$money_unpaid:0;
					    $goods_amount=$detailAccountArr['goods_amount']-$goods_price+$v['goods_price'];
				        $data = array('goods_id'=>$v['goods_id'],'goods_price'=>$v['goods_price'],'favorable_price'=>0,'delivery_status'=>5);
						$res=$salesModel->updateAppOrderDetail($data,"id={$v['detail_id']}");
				        if($res){
				        	$salesModel->updateTable('app_order_account',array('goods_amount'=>$goods_amount,'order_amount'=>$order_amount,'money_unpaid'=>$money_unpaid),"id=".$detailAccountArr['id']);
				        	$salesModel->AddOrderLog($detailAccountArr['order_sn'],"货号".$detailAccountArr['goods_id']."商品价格由{$goods_price}改成{$v['goods_price']},商品优惠清零");
				        }
						
				}

	
				//
				/*----------------*/
				}
				Util::jsonExit($result);
				}
	
	
}

?>