<?php
/**
 *  -------------------------------------------------
 *   @file		: AppReturnGoodsController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-13 11:05:03
 *   @update	:
 *  -------------------------------------------------
 */
class FinanceCheckGoodsController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('finance_check_goods_search_form.html',array('view'=>new AppReturnGoodsView(new AppReturnGoodsModel(31)),'bar'=>Auth::getBar()));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		if($_SESSION['userType']==1){
            $department = _Request::getInt('department')?_Request::getInt('department'):0;
        }else{
            if(isset($_REQUEST['department'])){
                $department = _Request::getInt('department')?_Request::getInt('department'):($_SESSION['qudao']?$_SESSION['qudao']:-1);
            }else{
                $department = _Request::getInt('department')?_Request::getInt('department'):($_SESSION['qudao']?current(explode(',', $_SESSION['qudao'])):-1);
            }
        }
		$args = array(
            'mod'   => _Request::get("mod"),
            'con'   => substr(__CLASS__, 0, -10),
            'act'   => __FUNCTION__,
			'return_id'	=> _Request::getInt("return_id"),
			'order_sn'	=> _Request::getString("order_sn"),
			'return_type'	=> _Request::getInt("return_type"),
			'start_time'	=> _Request::getString("start_time"),
			'end_time'	=> _Request::getString("end_time"),
			'department'	=> $department,
		);
		$page = _Request::getInt("page",1);
		$where = array(
            'check_status' => 5,
            'return_id' => _Request::getInt("return_id"),
            'order_sn' => _Request::getString("order_sn"),
            'return_type' => _Request::getInt("return_type"),
            'start_time'=>_Request::getString('start_time'),
            'end_time'=>_Request::getString('end_time'),
            'department'=>_Request::getInt('department'),
            'finance_status' => 1
        );

                if($args['return_id']){
                        $where =array();
                        $where['return_id']=$args['return_id'];
                }
		$model = new AppReturnGoodsModel(31);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'finance_check_goods_search_page';
		$this->render('finance_check_goods_search_list.html',array(
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
		$result['content'] = $this->fetch('finance_check_goods_info.html',array(
			'view'=>new AppReturnGoodsView(new AppReturnGoodsModel(31))
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	check，渲染修改页面
	 */
	public function check ($params)
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }			
		$id = intval($params["id"]);
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('finance_check_goods_info.html',array(
			'view'=>new AppReturnGoodsView(new AppReturnGoodsModel($id,31)),
			'tab_id'=>$tab_id
		));
		$result['title'] = '财务审核';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$this->render('finance_check_goods_show.html',array(
			'view'=>new AppReturnGoodsView(new AppReturnGoodsModel($id,31)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;
		$olddo = array();
		$newdo=array();

		$newmodel =  new AppReturnGoodsModel(32);
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
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');

        $id = _Post::getInt('return_id');
        $status = _Post::getInt('status');
        $finance_res = _Post::getString('finance_res');

        $returngoodsmodel =  new AppReturnGoodsModel($id,32);
		$returncheckidmodel = new AppReturnCheckModel(32);
		$order_id = $returngoodsmodel->getvalue('order_id');		
		$order_amount = $returngoodsmodel->get_monery_by_return_id($order_id,'order_amount');
		$order_amounts=$order_amount['order_amount']*0.9;
        $new_check_status = $returngoodsmodel->getNewCheckStatus($id);
        
        if($new_check_status != 4){

            $result['error'] = '审核状态错误！';
            Util::jsonExit($result);
        }
        $check_id = $returncheckidmodel->getCheckId($id);
        if($check_id == "")
        {
            $result['error'] = '修改失败';
            Util::jsonExit($result);
        }
        $SalesModel=new SalesModel(27);
        $orderInfo=$SalesModel->GetOrderInfoById($order_id);
        $referer=$orderInfo['referer'];
        $order_sn=$orderInfo['order_sn'];
        $send_good_status = $orderInfo['send_good_status'];
        if($referer=='天生一对加盟商'){
        //天生一对订单通过时需要都需要做一个判断，0<本次实退金额=<已付金额-实退金额-已配货商品的批发价-非已配货商品的（【原始零售价】*30%）才允许通过
          $real_return_amount=$returngoodsmodel->getValue('real_return_amount');//本次实退金额
          if($real_return_amount <= 0){
          	$result['error'] = '实退金额必须大于0';
          	Util::jsonExit($result);
          }
          $orderAccountInfo=$SalesModel->getOrderAccountInfoByOrderId($order_id);
          $money_paid=$orderAccountInfo['money_paid'];//已付金额
          $real_return_price1=$orderAccountInfo['real_return_price'];//实退金额
         
          //订单已配货商品的批发价
          $WarehouseModel=new WarehouseModel(21);
          $pfj=$WarehouseModel->getGoodsPfj($order_id);
          //非已配货商品的（【原始零售价】*30%）
          $retail_price=$SalesModel->getOrderDetailRetailPriceByOrderId($order_id);
          if($real_return_amount > $money_paid-$real_return_price1-$pfj-$retail_price*0.3){
          	$result['error'] = '本次实退金额=<已付金额-实退金额-已配货商品的批发价-非已配货商品的（【原始零售价】*30%）才允许通过';
          	Util::jsonExit($result);
          }
        }
        
        if($status == 1){
            
            $olddo = $returngoodsmodel->getDataObject();
            $newdo=array(
                'return_id' => $id,
                'check_status' => 5,
            );
            
        }
		$newmodel = new AppReturnGoodsModel(32);
        $res = $returngoodsmodel->saveData($newdo,$olddo);
		$detail_goods_id = $returngoodsmodel->getValue('order_goods_id');
		if($res && $detail_goods_id!=0){
			$res2=$returngoodsmodel->getReturnGoodsByWhere($order_id);
			if(!$res2){
				$newmodel->returnapply($order_id);
			}
			
		}
        $returncheckmodel = new AppReturnCheckModel($check_id,32);
        $olddo = $returncheckmodel->getDataObject();
        $bak_fee = $returngoodsmodel->getValue('apply_return_amount') - $returngoodsmodel->getValue('real_return_amount');
//        $apiModel = new ApiRefundModel();
//        if($returngoodsmodel->getValue('return_type')==1){
//            $apiModel->updateOrderAccountRealReturnPrice(array('order_id'=>$returngoodsmodel->getValue('order_id'),'zhuandan'=>1,'real_return_price'=>$returngoodsmodel->getValue('apply_return_amount')));
//        }else{
//            $apiModel->updateOrderAccountRealReturnPrice(array('order_id'=>$returngoodsmodel->getValue('order_id'),'real_return_price'=>$returngoodsmodel->getValue('apply_return_amount')));
//        }
        $newdo=array(
            'id' => $check_id,
            'finance_status' => 1,
            'bak_fee' => $bak_fee,
            'finance_res' => $finance_res,
            'finance_id' => $_SESSION['userId'],
            'finance_time' => date("Y-m-d H:i:s", time()),
        );
        $res = $returncheckmodel->saveData($newdo,$olddo);
        //记录日志
        $newlogmodel =  new AppReturnLogModel(32);
        $olddo_log = array();
        $newdo_log=array(
            'return_id' => $id,
            'even_time' => date("Y-m-d H:i:s", time()),
            'even_user' => $_SESSION['userName'],
            'even_content' => '财务部审核：'.$finance_res,
        );
        $res = $newlogmodel->saveData($newdo_log,$olddo_log);
		if($res !== false)
		{
            /*
            if($returngoodsmodel->getValue('order_goods_id')){
                //更新货品退货状态
                $apiModel->updateOrderDetailById($returngoodsmodel->getValue('order_goods_id'), 1);
                $warehouseModel = new ApiWarehouseModel();
                //已发货：客户已经收货的
                $apiModel = new ApiRefundModel();
                $order_info = $apiModel->GetExistOrderSn($returngoodsmodel->getValue('order_sn'),"`oa`.`order_id`,`oi`.`order_status`,`oi`.`send_good_status`,`oi`.`delivery_status`,`oi`.`order_pay_status`,`oi`.`buchan_status`");            
                $detail_goods_id = $returngoodsmodel->getValue('order_goods_id');
                $detail_goods = $apiModel->getGoodsSnByGoodsId($detail_goods_id,"`is_stock_goods`,`goods_id`");
                if($order_info['order_pay_status']!=4){
                    if(($order_info['send_good_status'] == 2 || $order_info['send_good_status'] == 3 || $order_info['send_good_status'] == 5) && $order_info['delivery_status']==5){
                        //生成退货销售单 并审核
                        $warehouseModel->OprationBillD(array('order_sn'=>$returngoodsmodel->getValue('order_sn'),'opra_uname'=>  Auth::$userName,'bill_no'=>$returngoodsmodel->getValue('jxc_order'),'type'=>1));

                    }else{
                         //客户没有收货，判断有木有库存
                        $is_kucun = FALSE;
                        $data_warehouse = array('order_goods_id'=>$detail_goods_id,'bind_type'=>2);
                        //取消销售单
                        $is_cancel = $warehouseModel->CancelBillS(array('order_sn'=>$returngoodsmodel->getValue('order_sn'),'detail_id'=>$returngoodsmodel->getValue('order_goods_id')));
                        if($is_cancel['data']=='操作成功'){
                            $apiModel->updateOrderInfoStatus(array('order_sn'=>$returngoodsmodel->getValue('order_sn'),'send_good_status'=>1,'delivery_status'=>3));
                        }
                        //现货需要：查看此商品是否已经绑定仓储
                        $warehouse_goods =$warehouseModel->getWarehouseGoodsInfo(array('order_goods_id'=>"$detail_goods_id"));
                        //有库存
                        if(!empty($warehouse_goods['data']) && $warehouse_goods['error']==0){
                            if(!empty($warehouse_goods['data']['data']['order_goods_id'])){
                                $is_kucun = TRUE;
                            }
                        }
                        //财务审核解绑
                        if($is_kucun){//有库存
                            $warehouseModel->BindGoodsInfoByGoodsId($data_warehouse);
                        }else{//没有库存
                            $data_processor = array('arr'=>array('goods_id'=>$detail_goods_id));
                            $processorModel = new ApiProcessorModel();
                            $processorModel->relieveProduct($data_processor);
                        }



                    }
                }

                //财务审核解绑
                //销售政策上架
                $new_detail_data = array(array('is_sale'=>1,'goods_id'=>$detail_goods['goods_id']));
                $salePolicyModel = new ApiSalePolicyModel();
                $salePolicyModel->UpdateAppPayDetail($new_detail_data);
            }
            */
            $apiModel = new ApiRefundModel();
            $order_info = $apiModel->GetExistOrderSn($returngoodsmodel->getValue('order_sn'),"`oa`.`order_id`,`oi`.`order_status`,`oi`.`send_good_status`,`oi`.`delivery_status`,`oi`.`order_pay_status`,`oi`.`buchan_status`");            

            //订单日志
            $insert_action = array ();
            $insert_action ['order_id'] = $order_info ['order_id'];
            $insert_action ['order_status'] = $order_info ['order_status'];
            $insert_action ['shipping_status'] = $order_info ['send_good_status'];
            $insert_action ['pay_status'] = $order_info ['order_pay_status'];
            $insert_action ['remark'] = '退款/退货单:财务已经审核';
            $insert_action ['create_user'] = $_SESSION ['userName'];
            $insert_action ['create_time'] = date ( 'Y-m-d H:i:s' );
            $apiModel->AddOrderActionInfo($insert_action);



            /*if($send_good_status==2){
                $SalesModel->app_return_point_add($id);
            }*/
            
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;	
			$result['title'] = '修改此处为想显示在页签上的字段';
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
		$model = new AppReturnGoodsModel($id,32);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		//联合删除？
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
