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
class GoodsCheckController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $warehouseModel ;//仓库模块Model
    protected $salesModel     ;//销售模块Model
    protected $processorModel ;//供应商模块Model
    protected $returnLogModel ;//退款单操作日志 
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $this->render('goods_check_search_form.html',array('view'=>new AppReturnGoodsView(new AppReturnGoodsModel(31)),'bar'=>Auth::getBar()));
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
		    'return_goods_id'=>_Request::getString('return_goods_id'),
		);
		$page = _Request::getInt("page",1);
		$where = array(
            'check_status'=>2,
            'return_id'=>$args['return_id'],
            'order_sn'=>$args['order_sn'],
            'return_type'=>$args['return_type'],
            'start_time'=>$args['start_time'],
            'end_time'=>$args['end_time'],
            'department'=>$args['department'],
        );
		if(!empty($args['return_goods_id'])){
		    $where['return_goods_id'] = str_replace(',', ' ',$args['return_goods_id']);
		    $where['return_goods_id'] = preg_replace('/\s+/is',' ',$where['return_goods_id']);
		    $where['return_goods_id'] = explode(' ',$where['return_goods_id']);
		}
		if($args['return_id']){
			$where =array();
			$where['return_id']=$args['return_id'];
		}
         
		$model = new AppReturnGoodsModel(31);
		$data = $model->pageList($where,$page,50,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'goods_check_search_page';
		$this->render('goods_check_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	check，库管审核
	 */
	public function check ($params)
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
            die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }
		$result = array('success' => 0,'error' => '','title'=>'库管批量审核');
		$ids = _Request::getList('_ids');

        if(empty($ids)){
            $result['content'] = "提示：_ids 参数为空。";
            Util::jsonExit($result);
        }
        
        $ReturnCheckModel = new AppReturnCheckModel(31);
        $SaleModel = new SalesModel(27);
        $warehouseModel = new WarehouseModel(21);//仓库模块Model
        $SalesChannelsModel = new SalesChannelssModel(1);   
        
        $returnPriceArr = array();
        $companyList    = array();
        $to_warehouse_list = array();
        $bill_s_info_unbind = array();
        $is_have_bills = 0;//全局 是否有S单
        $is_send_goods  = 0;//是否发货
        $ids_count = count($ids);
        $noBills = $hasBills = array();
        $order_has_invoice = array();//订单发票退款提醒
        foreach ($ids as $id){
            $_is_send_goods = 0;
            $ReturnGoodsModel = new AppReturnGoodsModel($id,31);
            $do = $ReturnGoodsModel->getDataObject();
            $orderInfo = $ReturnCheckModel->getOrderPriceSatausGoods($do['order_goods_id']);
            $send_good_status = $orderInfo['send_good_status'];
            $delivery_status = $orderInfo['delivery_status'];
            if(in_array($send_good_status,array(2,4,5)) && $delivery_status == 5){
                $is_send_goods = 1;
                $_is_send_goods = 1;
            }
            $bill_s_info=array();
            $bill_s_info_unbind = array();//未查出绑定货品id
            if($do['order_goods_id']>0){                
                //通过商品明细id找出与之绑定的货号           
                //$bangding_goods_id = $warehouseModel->getWarehouseGoodsInfo($do['order_goods_id']);
                $bill_s_info = $warehouseModel->getBillSInfoByOrderSn_New($do['order_sn'],$do['order_goods_id']);
                if(empty($bill_s_info)){//如果未找到绑定的货号，则查找该订单最后一个S单；
                    $bill_s_all_id = $warehouseModel->getBillSidByOrder_sn($do['order_sn']);
                    if(!empty($bill_s_all_id)){
                        $bill_s_str_id = implode(",",array_column($bill_s_all_id, 'id'));
                        $bill_s_info_unbind = $warehouseModel->getBillSInfoBy_ids($bill_s_str_id);
                    }
                    if($ids_count>1 && !empty($bill_s_info_unbind)){
                        $result['content'] = "提示：退单流水号【{$id}】申请退货商品无法找到绑定的S单唯一货号，不能批量审批，请单独审批。";
                        Util::jsonExit($result);
                    }
                } 
             
                if(!empty($bill_s_info) || !empty($bill_s_info_unbind)){
                    $_is_send_goods = 1;
                    $is_have_bills = 1;
                }
                
                if($_is_send_goods ==1 && empty($bill_s_info) && empty($bill_s_info_unbind)){
                    $result['content'] = "<h4>提示：退单流水号【{$id}】数据异常，订单{$do['order_sn']}找不到已审核的销售单 或  退货货号状态不对！<br/><br/>请确认：订单是【有S单】且【S单已审核】以及 退货货号状态是【已销售】</h3>";
                    Util::jsonExit($result);
                }

             
            }
            //查询审核记录 $returnCheckInfo，验证能否审核
            $returnCheckInfo = $ReturnCheckModel->getAppReturnCheckByReturnId($id);
            if(empty($do['check_status']) || empty($returnCheckInfo)){
                $result['content'] = "提示：退单流水号【{$id}】主管还未操作！请确认哦。";
                Util::jsonExit($result);
            }elseif(!empty($returnCheckInfo['goods_status'])){
                $result['content'] = "提示：退单流水号【{$id}】库管已审核过！请确认哦。";
                Util::jsonExit($result);
            }else {
                $order_id = $do['order_id'];
                $returnPrice = $orderInfo['goods_price'] != ''?$orderInfo['goods_price']:$do['real_return_amount'];
                $returnPriceArr[] = array('id'=>$id,'order_sn'=>$do['order_sn'],'price'=>$returnPrice);
                $from_company_id = $warehouseModel->getFromCompanyIdByOrder_sn($do['order_sn']);
                if(empty($hasBills) && $from_company_id>0){
                    $hasBills = array('from_company_id'=>$from_company_id,'return_id'=>$id);                    
                }else {
                    if(empty($noBills) && $from_company_id<1){
                        $noBills = array('from_company_id'=>$from_company_id,'return_id'=>$id); 
                    }
                    if($from_company_id>0 && $from_company_id<>$hasBills['from_company_id']){
                        $result['content'] = "提示：退单流水号【{$id}】的出库公司与退单流水号【{$hasBills['return_id']}】的出库公司不一致，不能批量提交！";
                        Util::jsonExit($result);
                    }                    
                                        
                }
                //echo $from_company_id.'--';
                //print_r($noBills);print_r($hasBills);echo "\r\n";
                //未发货，前面的单据有出库公司
                if(!empty($noBills) && !empty($hasBills)){
                    $result['content'] = "提示：退单流水号【{$noBills['return_id']}】没有S单，不能和有S单的退款单【{$hasBills['return_id']}】批量提交！";
                    Util::jsonExit($result);
                }
                
                if(empty($companyList) && $from_company_id>0){
                    //深圳分公司销售出去的必须也退回到深圳分公司，不可以直接退回总公司。
                    //既深圳分公司销账产生的S单，此S单里货号如发生退货必须先退深圳分公司。
                    if(!empty($from_company_id) && $from_company_id == 445){
                        $companyList[445] ='BDD深圳分公司';
                    }else{                        
                        $department_id=$SaleModel->getDepartment_id($order_id);
                        $companyArr=$SalesChannelsModel->getCompanyByDepartment($department_id);
                        if(!empty($companyArr)){
                            $companyList[]=$companyArr;
                        }
                        $companySalesArr=$SalesChannelsModel->getCompanyById($_SESSION ['userId']);
                        if(!empty($companySalesArr)){
                            foreach ($companySalesArr as $v){
                                $companyList[]=$v;
                            }
                        }
                        if(!empty($companyList)){
                            $companyList = array_combine(array_column($companyList, 'id'), array_column($companyList, 'company_name'));
                        }                        
                    }
                    if($from_company_id>0){
                        $to_warehouse_list = $warehouseModel->getWarehouseByCompany($from_company_id);
                    }
                    //如果出库公司不是445深圳分公司，踢出445公司
                    if($from_company_id != '445' && isset($companyList[445])){
                       unset($companyList[445]);
                    }
                }
                
                //
                if(!empty($do['order_sn']) && !in_array($do['order_sn'],$order_has_invoice)){
                    $res = $ReturnCheckModel->checkOrderHasInvoice($do['order_sn']);
                    if($res){
                        $order_has_invoice[] = $do['order_sn'];
                    }
                }
                
            } 
            //调拨单入库仓库列表
            $m_warehouse_list = $warehouseModel->getWarehouseByCompany(58);
           // print_r($bill_s_info);print_r($bill_s_info_unbind);
//var_dump($is_have_bills);
            $result['content'] = $this->fetch('goods_check_info.html',
                array(
                    'ids'=>$ids,
        			'companylist' => $companyList,//公司列表
                    'returnPriceArr'=> $returnPriceArr,
                    'bill_s_info'=>$bill_s_info_unbind,//S单下的货品
                    'from_company_id'=>!empty($hasBills['from_company_id'])?$hasBills['from_company_id']:'',//S单出库公司ID
                    'm_warehouse_list'=>$m_warehouse_list,
                    'to_warehouse_list'=>$to_warehouse_list,
                    'is_have_bills' =>$is_have_bills,//是否有S单
                    'is_send_goods' =>$is_send_goods,//是否发货
                    'order_has_invoice'=>implode(',',$order_has_invoice),//已开票订单号
            ));
        }
		Util::jsonExit($result);
	}
	public function insert($params){
	    $result = array('success' => 0,'error' =>'');
	    $ids = _Post::getList('_ids');
        if(empty($ids)){
	        $result['error'] = 'ids is empty！';
	        Util::jsonExit($result);
	    }

	    $goods_res = _Post::getString('goods_res');//库管审核意见
	    $goods_status = _Post::getInt('goods_status');//库管审核状态
	    $t_goods_id   = _Post::getString('t_goods_id');//需要生成退货单的货号
	    $company_id = _Post::getInt('company_id',0);
	    $warehouse_id = _Post::getInt('warehouse_id',0);
	    $m_warehouse_id = _Post::getInt('m_warehouse_id',0);
	    $is_create_billd = _Post::getInt('is_create_billd',0);
	    if(empty($goods_res)){
	        $result['error'] = '库管审核意见不能为空！';
	        Util::jsonExit($result);
	    }
	    if(empty($goods_status)){
	        $result['error'] = '请选择库管审核状态：审核通过  or 审核驳回？';
	        Util::jsonExit($result);
	    }
	    if($goods_status==1 && empty($is_create_billd)){
	        $result['error'] = '请选择是否生成销售退货单！';
	        Util::jsonExit($result);
	    }
	
	    $warehouseModel= new WarehouseModel(21);//仓库模块Model
	    $salesModel = new SalesModel(27);//销售模块Model
	    $processorModel = new ProcessorModel(14);//供应商模块Model
	    $returnLogModel = new AppReturnLogModel(27);//退款单操作日志
	     
	    $this->warehouseModel = $warehouseModel;
	    $this->salesModel     = $salesModel;
	    $this->processorModel = $processorModel;
	    $this->returnLogModel = $returnLogModel;
	
	    $post = array(
	        'goods_status'=>$goods_status,
	        't_goods_id'=>$t_goods_id,
	        'company_id' => $company_id,
	        'warehouse_id'=> $warehouse_id,
	        'm_warehouse_id'=>$m_warehouse_id,//调拨单仓库ID
	        'is_create_billd' =>$is_create_billd,//是否生成销售退货单
	        'goods_res'=> $goods_res
	    );
	
	    //创建事物PDO对象(本次操作共有3个数据库垮库操作)
	    $pdolist[14] = $processorModel->db()->db();//kela_supplier数据库PDO
	    $pdolist[21] = $warehouseModel->db()->db();//warehouse_shipping数据库PDO
	    $pdolist[27] = $salesModel->db()->db();//app_order数据库PDO
	    //开启事物
	    foreach($pdolist as $pdo){
	        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
	        $pdo->beginTransaction(); //开启事务
	    }
	     
	    foreach ($ids as $id){	         
	        $res = $this->goodsCheckOne($id,$post);	        
	        if($res['success']==0){
	            $msg = $res['error'];
	            Util::rollbackExit($msg,$pdolist);
	        }
            $result['error'] .=$res['error'];	         
	    }
	    //$msg = "ok！";
	    //Util::rollbackExit($msg,$pdolist);
	    try{
	        foreach($pdolist as $pdo){                
	            $pdo->commit(); //事务提交
	            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
	        }
	        $result['success'] = 1;
	        Util::jsonExit($result);
	    }catch (Exception $e){
	        $msg = "操作失败， 请重新尝试！";
	        Util::rollbackExit($msg,$pdolist);
	    }
	}
	//单个库管审核
	public function goodsCheckOne($id,$post){
	    $result = array('success' => 0,'error' =>'');	    
        
        $warehouseModel =$this->warehouseModel;//仓库模块Model
        $salesModel = $this->salesModel;//销售模块Model
        $processorModel =$this->processorModel;//供应商模块Model
        $returnLogModel = $this->returnLogModel;//退款单操作日志
        
        $t_goods_id = $post['t_goods_id'];
        $goods_res  = $post['goods_res'];
        $company_id = $post['company_id'];
        $warehouse_id = $post['warehouse_id'];
        $m_warehouse_id = $post['m_warehouse_id'];
        $goods_status = $post['goods_status'];
        $is_create_billd = $post['is_create_billd'];
	    $returnGoodsModel = new AppReturnGoodsModel($id,27);//退货表实体Model
	    $do = $returnGoodsModel->getDataObject();
	    if(empty($do)){
	        $result['error'] = "退款流水号【{$id}】不存在！";
	        return $result;
	    }
	    $order_sn = $do['order_sn'];
	    $order_goods_id = $do['order_goods_id'];
	    $order_info = $salesModel->getBaseOrderInfoByOrderSn($order_sn);
	    if(empty($order_info)){
	        $result['error'] = "关联的订单【{$order_sn}】不存在！";
	        return $result;
	    }
	    //库管驳回
	    if($goods_status==2){
	        return $this->rejectOne($id, $goods_res, $order_info);
	    }
	    $send_good_status = $order_info['send_good_status'];//发货状态
	    $delivery_status = $order_info['delivery_status'];//配货状态
	    $referer = $order_info['referer'];//录单来源
	    $detail_goods = $salesModel->getAppOrderDetailByDetailId($order_goods_id);
	    $goods_id   = $detail_goods['goods_id'];//货品编号
	    $is_zp      = $detail_goods['is_zp']; //是否赠品
	    $is_finance = $detail_goods['is_finance'];//是否销账（注意：1不需要销账，2需要销账）
	    $is_stock_goods = $detail_goods['is_stock_goods'];//现货期货
        $jifenma_code = $detail_goods['jifenma_code'];
        $daijinquan_code = $detail_goods['daijinquan_code'];
        //$send_good_status_name = $this->dd->getEnum('order.send_good_status',$send_good_status);
        //$delivery_status_name = $this->dd->getEnum('sales.delivery_status',$delivery_status);
	    if($is_create_billd == 1){
	        if(!in_array($send_good_status,array(2,4,5)) && $delivery_status == 5){
	            $result['error'] = "订单{$order_sn}未发货，不能生成销售退货单";
                return $result;
	        }
            if($company_id < 1){
                $result['error'] = '公司不能为空！';
                return $result;
            }
            if($warehouse_id < 1){
                $result['error'] = '仓库不能为空！';
                return $result;
            }

            $warehouseCount=$warehouseModel->getWarehouseIsDelete($warehouse_id);
            $companyCount=$warehouseModel->getCompanyIsDelete($company_id);
            if($warehouseCount[0]['count']==0){
                $result['error'] = '仓库已关闭';
                return $result;
			}
			if( $companyCount[0]['count']==0){
                $result['error'] = '公司已关闭';
                return $result;
			}
            if(!empty($t_goods_id)){
                $warehouseModel->updateBangDingOrderGoods_id($t_goods_id, $order_goods_id);
            }
            $detail_goods_list = $warehouseModel->getBillSInfoByOrderSn_New($order_sn,$order_goods_id);
            if(empty($detail_goods_list)){
                $result['error'] = "订单{$order_sn}找不到已审核的销售单 或   退货货号状态不对！<br/>请确认：订单是【有S单】且【S单已审核】以及 退货货号状态是【销售中】";
                return $result;
            }
            $return_goods_id = array_column($detail_goods_list, 'goods_id');
            $return_goods_id = implode(',',$return_goods_id);
            $returnGoodsModel->setValue('return_goods_id',$return_goods_id);
            
            
            if($company_id==445 && empty($m_warehouse_id)){
                $result['error'] = "请选择调拨入库仓！";
                return $result;
            }
            $jxc_order = $warehouseModel->createReturnGoodsBill($order_sn,$id,$_SESSION['userName'], $warehouse_id,$detail_goods_list,$m_warehouse_id);
            if(!$jxc_order['status']){
                $result['error'] = $jxc_order['msg'];
                return $result;
            }
	        //更新账单编号到app_return_goods表
	        $returnGoodsModel->setValue('jxc_order', $jxc_order['bill_no']);
	        //订单操作日志
	        if(!empty($jxc_order['bill_m_no'])){
	            $orderLogReamrk = '退款/退货单'.$id.':自动生成销售退货单'.$jxc_order['bill_no'].', 调拨单'.$jxc_order['bill_m_no'].',货号'.$jxc_order['goods_id'];
	        }else{
	            $orderLogReamrk = '退款/退货单'.$id.':自动生成销售退货单'.$jxc_order['bill_no'].', 货号'.$jxc_order['goods_id'];
	        }
	        $data = array (
	            'order_id'    =>$order_info['id'],
	            'order_status'=>$order_info['order_status'],
	            'shipping_status'=>$order_info['send_good_status'],
	            'pay_status' =>$order_info['order_pay_status'],
	            'create_user'=>$_SESSION['userName'],
	            'create_time'=>date ('Y-m-d H:i:s'),
	            'remark'    =>$orderLogReamrk
	        );
	        $res = $salesModel->addOrderAction($data);
	        if(!$res){
	            $result['error'] = "退货订单日志写入失败！";
	            return $result;
	        }	
	        if(!empty($jxc_order['bill_m_no'])){
	            $data = array (
	                'order_id'    =>$order_info['id'],
	                'order_status'=>$order_info['order_status'],
	                'shipping_status'=>$order_info['send_good_status'],
	                'pay_status' =>$order_info['order_pay_status'],
	                'create_user'=>$_SESSION['userName'],
	                'create_time'=>date ('Y-m-d H:i:s'),
	                'remark'    =>'库管审核退货，自动生成调拨单'.$jxc_order['bill_m_no'].'，货号'.$jxc_order['goods_id']
	            );
	            $res = $salesModel->addOrderAction($data);
	            if(!$res){
	                $result['error'] = "退货调拨订单日志写入失败！";
	                return $result;
	            }
	        }    
            $order_invoice=$salesModel->getOrderInfoForInvoice($order_sn);
	    } 
	    else {
	        if(in_array($send_good_status,array(2,4,5)) && $delivery_status == 5){
	            $result['error'] = "订单{$order_sn}状态符合生成销售退货单，请确认！";
	            return $result;
	        }	        
	        
	        $where = array('order_sn'=>$order_sn,'bill_type'=>'S','bill_status'=>1);
	        $hasBill_S = $warehouseModel->checkHasBillByWhere($where);
	        if($hasBill_S){
	            $result['error'] = "订单{$order_sn}正在等待发货中，不能审核！请等待FQC质检不通过，然后再来审核。";
	            return $result;
	        } 
	        /* $hasBill_S = $warehouseModel->getBillSInfoByOrderSn_New($order_sn,$order_goods_id);
	        if(!empty($detail_goods_list)){
	            $result['error'] = "订单{$order_sn}有绑定S单，必须要生成销售退货单！";
	            return $result;
	        }  */
	        
	        $warehouse_goods = $warehouseModel->getWarehouseGoodsInfo($order_goods_id);
	        if(!empty($warehouse_goods)){
	            //解除仓库货品绑定
	            $data = array(	                
	                'order_goods_id'=>0,
	            );
	            $unBindResult = $warehouseModel->updateWarehouseGoods($data, "order_goods_id='{$order_goods_id}'");
	            if(!$unBindResult){
	                $result['error'] = '库存货品解绑失败！error:'.__LINE__;
	                return $result;
	            }
	        } else {
	            //如果有布产,尝试解除和供应商之间关系
	            $data_processor = array('goods_id'=>$order_goods_id);
	            $relieveResult = $processorModel->relieveProduct($data_processor);
	            if(!$relieveResult['status']){
	                $result['error'] = '解除供应商关系失败！error:'.__LINE__;
	                return $result;
	            }
	        }	        
	    }// end if $is_product ==1
	    //添加库管审核意见
	    $updatedata = array ();	    
	    $updatedata ['goods_res'] = $goods_res;
	    $updatedata ['goods_comfirm_id'] = $_SESSION['userId'];
	    $updatedata ['goods_status'] = $goods_status;
	    $updatedata ['goods_time'] = date ("Y-m-d H:i:s");
	    // 事业部添加备注
	    $goods_type  = $detail_goods['goods_type'];
	    $luozuan_arr = array ('lz', 'luozuan', 'caizuan_goods');
	    $ScModel = new SalesChannelsModel($order_info['department_id'],1);
	    $orderScInfo = $ScModel->getDataObject();
	    $channel_class = $orderScInfo['channel_class'];//1线上，2线下
	    
	    if(in_array($goods_type,$luozuan_arr) && $channel_class == '2' && $is_stock_goods == '0'){
	        $is_fal=false;
	    }elseif(in_array($goods_type,$luozuan_arr) && $referer == '婚博会' && $channel_class == '2'){
	        $is_fal=false;
	    }elseif($detail_goods['goods_sn'] == 'DIA' && $channel_class == '2' && $is_stock_goods == '0'){
	        $is_fal=false;
	    }elseif($detail_goods['goods_sn'] == 'DIA' && $referer == '婚博会' && $channel_class == '2'){
	        $is_fal=false;
	    }else{
	        $is_fal=true;
	    }
	    
	    if ($is_fal) {
	        $updatedata ['cto_id'] = $_SESSION['userId'];
	        $updatedata ['cto_status'] = 1;
	        $updatedata ['cto_res'] = "退单".$id.'系统操作,不需事业部操作';
	        $updatedata ['cto_time'] = date ( "Y-m-d H:i:s" );
	        $is_def_cto = 1;
	    }
	    //更新审核状态
	    $updateResult = $salesModel->udpateAppReturnCheck($updatedata,"return_id='{$id}'");
	    if(!$updateResult){
	        $result['error'] = '更新审核表AppReturnCheck对应的审核阶段的字段失败！error:'.__LINE__;
	        return $result;
	    }
	    $returnGoodsModel->setValue('check_status', 2);
	    //$returnGoodsModel->save(true);
	    if(!empty($order_goods_id)){
	        $data = array(
	            'apply_return' => 2 //订单信息退款状态改为：退款中
	        );
	        $updateResult = $salesModel->updateBaseOrderInfo($data,"order_sn='{$order_sn}'");
	        if(!$updateResult){
	            $result['error'] = '订单状态更改失败！error:'.__LINE__;;
	            return $result;
	        }
	    }
	    //添加备注
	    $insertnote = array ();
	    $insertnote ['return_id'] = $id;
	    $insertnote ['even_time'] = date("Y-m-d H:i:s");
	    $insertnote ['even_user'] = $_SESSION ['userName'];
	    $insertnote ['even_content'] = "退单".$id.'库管审核通过,审核意见:' . $goods_res;	     
	    $res = $returnLogModel->saveData($insertnote, array());
	    if(!$res){
	        $result['error'] = '库管审核意见写入失败！error:'.__LINE__;
	        return $result;
	    }
	    if ($is_fal) {
	        $insertnote = array ();
	        $insertnote ['return_id'] = $id;
	        $insertnote ['even_time'] = date("Y-m-d H:i:s");
	        $insertnote ['even_user'] = $_SESSION ['userName'];
	        $insertnote ['even_content'] = "退单".$id.'事业部负责人：非裸钻、非彩钻订单事业部负责人默认批准';
	        $res = $returnLogModel->saveData($insertnote, array());
	        if(!$res){
	            $result['error'] = '事业部负责人自动审核写入失败！error:'.__LINE__;
	            return $result;
	        }
	    }	        
	    //订单操作日志：库管已经审核通过
	    $insert_action = array ();
	    $insert_action ['order_id'] = $order_info ['id'];
	    $insert_action ['order_status'] = $order_info ['order_status'];
	    $insert_action ['shipping_status'] = $order_info ['send_good_status'];
	    $insert_action ['pay_status'] = $order_info ['order_pay_status'];
	    $insert_action ['remark'] = '退款/退货单'.$id.':库管审核通过';
	    $insert_action ['create_user'] = $_SESSION ['userName'];
	    $insert_action ['create_time'] = date ( 'Y-m-d H:i:s' );
	    $res = $salesModel->addOrderAction($insert_action);
	    if(!$res){
	        $result['error'] = '订单日志(库管审核通过)写入失败！error:'.__LINE__;
	        return $result;
	    }
	    if(!empty($is_def_cto)){
	        //订单操作日志
	        $insert_action = array ();
	        $insert_action ['order_id'] = $order_info ['id'];
	        $insert_action ['order_status'] = $order_info ['order_status'];
	        $insert_action ['shipping_status'] = $order_info ['send_good_status'];
	        $insert_action ['pay_status'] = $order_info ['order_pay_status'];
	        $insert_action ['remark'] = '退款/退货单'.$id.':事业部负责人默认审核通过';
	        $insert_action ['create_user'] = $_SESSION ['userName'];
	        $insert_action ['create_time'] = date ('Y-m-d H:i:s' );
	        $res = $salesModel->addOrderAction($insert_action);
	        if(!$res){
	            $result['error'] = '订单日志写入失败！error:'.__LINE__;
	            return $result;
	        }
	        $returnGoodsModel->setValue('check_status',3);
	    }
	     
	    $apply_return_amount=$returnGoodsModel->getValue('apply_return_amount');
	    //申请退款为0时仓库审核通过财务审核和现场财务审核自动通过boss-803 $apply_return_amount==0
	    if($order_info['order_pay_status'] == 4 || $apply_return_amount==0){
	         
	        $returnGoodsModel->setValue('check_status',6);
	    
	        //现场财务意见
	        $updatedata = array ();
	        $updatedata ['goods_res'] = $goods_res;
	        $updatedata ['goods_comfirm_id'] = Auth::$userId;
	        $updatedata ['goods_status'] = $goods_status;
	        $updatedata ['goods_time'] = date ("Y-m-d H:i:s");
	        // 添加备注
	        $goods_type  = $detail_goods['goods_type'];
	        $updatedata ['deparment_finance_id'] = 1;//Auth::$userId;
	        $updatedata ['deparment_finance_status'] = 1;
	        $updatedata ['deparment_finance_res'] = "退单".$id.'系统操作,0元退款不需现场财务操作';
	        $updatedata ['deparment_finance_time'] = date ( "Y-m-d H:i:s" );
	        //更新审核状态
	        $updateResult = $salesModel->udpateAppReturnCheck($updatedata,"return_id='{$id}'");
	        	
	        if(!$updateResult){
	            $result['error'] = '订单状态更改失败！error:'.__LINE__;
	            return $result;
	        }
	    
	    
	        //财务意见
	        $updatedata = array ();
	        $updatedata ['goods_res'] = $goods_res;
	        $updatedata ['goods_comfirm_id'] = Auth::$userId;
	        $updatedata ['goods_status'] = $goods_status;
	        $updatedata ['goods_time'] = date ("Y-m-d H:i:s");
	        // 添加备注
	        $goods_type  = $detail_goods['goods_type'];
	        $updatedata ['finance_id'] = 1;//Auth::$userId;
	        $updatedata ['finance_status'] = 1;
	        $updatedata ['finance_res'] = "退单".$id.'系统操作,0元退款不需财务操作';
	        $updatedata ['finance_time'] = date ( "Y-m-d H:i:s" );
	         
	        //更新审核状态
	        $updateResult = $salesModel->udpateAppReturnCheck($updatedata,"return_id='{$id}'");
	         
	        if(!$updateResult){
	            $result['error'] = '订单状态更改失败！error:'.__LINE__;
	            return $result;
	        }
	    
	    
	    
	        //订单操作日志：现场财务意见审核通过
	        $insert_action = array ();
	        $insert_action ['order_id'] = $order_info ['id'];
	        $insert_action ['order_status'] = $order_info ['order_status'];
	        $insert_action ['shipping_status'] = $order_info ['send_good_status'];
	        $insert_action ['pay_status'] = $order_info ['order_pay_status'];
	        $insert_action ['remark'] = '退款/退货单'.$id.':现场财务审核通过';
	        $insert_action ['create_user'] = $_SESSION ['userName'];
	        $insert_action ['create_time'] = date ( 'Y-m-d H:i:s' );
	        $res = $salesModel->addOrderAction($insert_action);
	        if(!$res){
	            $result['error'] = '订单日志(现场财务意见)写入失败！error:'.__LINE__;
	            return $result;
	        }
	    
	         
	        //订单操作日志： 财务意见审核通过
	        $insert_action = array ();
	        $insert_action ['order_id'] = $order_info ['id'];
	        $insert_action ['order_status'] = $order_info ['order_status'];
	        $insert_action ['shipping_status'] = $order_info ['send_good_status'];
	        $insert_action ['pay_status'] = $order_info ['order_pay_status'];
	        $insert_action ['remark'] = '退款/退货单'.$id.':财务审核通过';
	        $insert_action ['create_user'] = $_SESSION ['userName'];
	        $insert_action ['create_time'] = date ( 'Y-m-d H:i:s' );
	        $res = $salesModel->addOrderAction($insert_action);
	        if(!$res){
	            $result['error'] = '订单日志(财务意见)写入失败！error:'.__LINE__;
	            return $result;
	        }
	        $res=$salesModel->updateOrderAccountRealReturnPrice($id);
	        if(!$res){
	            $result['error'] = '更新订单金额表失败！error:'.__LINE__;
	            return $result;
	        }
            $res = $returnGoodsModel->save(true);
            if(!$res){
                $result['error'] = '退款状态更改失败！error:'.__LINE__;
                return $result;
            } 

            /* 实例化 */
            //$newmodel = new AppReturnGoodsModel($id, 32);
            //订单退款状态结束
            $order_id = $returnGoodsModel->getvalue('order_id');
            $res2=$returnGoodsModel->getReturnGoodsByWhere($order_id);
            if(!$res2){
                $val = $returnGoodsModel->returnapply($order_id);
                if(!$val){
                    $result['error'] = '订单退款状态结束失败！error:'.__LINE__;
                    return $result;
                }
            }
	    }else{
            $res = $returnGoodsModel->save(true);
            if(!$res){
                $result['error'] = '退款状态更改失败！error:'.__LINE__;
                return $result;
            }                
        }
	    
        
        $invoice_error = ''; 
        //如果 
        if(SYS_SCOPE=='boss' && $is_create_billd == 1){
            $order_pay_type_limit= json_decode(INVOICE_ORDER_PAY_TYPE_LIMIT,true);
            if(!empty($order_invoice) && in_array($order_invoice['order_pay_type'],$order_pay_type_limit)){
                        include_once(APP_ROOT."shipping/modules/invoice_api/invoice.php");
                        include_once(APP_ROOT."shipping/modules/invoice_api/DESDZFP.class.php");
                        $invoice_res = Invoice::searchOrder($order_sn); 
                        //print_r($invoice_res);
                        $invoice_info = null;
                        $invoice_sn = '';
                        $invoice_num = '';
                        $pre_date = '';
                        if(!empty($invoice_res) && is_array($invoice_res) && $invoice_res['result']=='success' && !empty($invoice_res['list'])){
                                    $invoice_list =  $invoice_res['list'];    
                                    //print_r($invoice_list);                                
                                    for($i=count($invoice_list)-1;$i>=0;$i--) {
                                        $invoice_info = $invoice_list[$i];
                                        if($invoice_info['c_bhsje']>0){
                                            $invoice_sn = $invoice_info['c_fpdm'];
                                            $invoice_num = $invoice_info['c_fphm'];
                                            $pre_date = $invoice_info['c_kprq'];
                                            break;
                                        }    
                                    }                                   

                        }else{
                                    $invoice_error .="订单:{$order_sn}未找到可冲红的正票信息"; 
                        }



                        if(!empty($invoice_info) && !empty($invoice_sn) && !empty($invoice_num) && !empty($pre_date)){                             
                                $inv_res = Invoice::makeOrder2($order_invoice,$invoice_sn,$invoice_num,$pre_date);
                                //print_r($inv_res);
                                if(is_array($inv_res) && $inv_res['status']=='0000' && $inv_res['message']=='同步成功'){
                                            $invoice_num = $inv_res['fpqqlsh'];
                                            //$invoice_error .="订单:{$order_sn} 自动开具红冲发票流水号".$invoice_num;
                                            $add_base_invoice_info_data = array(
                                                                    'invoice_num' => $invoice_num,
                                                                    'price' => array_sum(array_column($order_invoice['detail'],'goods_price'))*-1,
                                                                    'title' => $order_invoice['invoice_title'],
                                                                    'content' => $order_invoice['invoice_content'],
                                                                    'status' => 2,
                                                                    'create_user' => $_SESSION['userName'],                                        
                                                                    'create_time' => date('Y-m-d H:i:s'),
                                                                    'use_user' => $_SESSION['userName'],
                                                                    'use_time' => date('Y-m-d H:i:s'),
                                                                    'order_sn' => $order_sn,
                                                                    'type' =>2,  
                                                                    );
                                            $insert_finance_invoice_res = $salesModel->add_base_invoice_info($add_base_invoice_info_data);
                                            $insert_action ['remark'] ='库管审核自动开具负数发票流水号:{$invoice_num}'; 
                                            $salesModel->addOrderAction($insert_action);
                                }else{

                                            if(!empty($inv_res['status']) && !empty($inv_res['message']) && $inv_res['status']=='9106' && $inv_res['message']=='订单编号不能重复'){
                                                $inv_res = Invoice::searchOrder($order_sn);
                                                //print_r($inv_res);
                                                if(!empty($inv_res['result']) && $inv_res['result']=='success' && !empty($inv_res['list'][0]['c_fpqqlsh']))
                                                    $invoice_num = $inv_res['list'][0]['c_fpqqlsh'];
                                                else
                                                    $invoice_error .='订单{$order_sn} 冲红报错:订单发票已存在,接口查询订单发票失败';
                                            }else{
                                                if(empty($inv_res['message']))
                                                    $invoice_error .= '订单{$order_sn} 冲红报错:发票接口异常';
                                                else
                                                    $invoice_error .= "订单{$order_sn} 冲红报错: ".$inv_res['message'];
                                            }                    
                                }
                        }   
            } 
                
            $result['error'] =$invoice_error;
            //return $result;                   
        }

        /*if($returnGoodsModel->getValue('return_by')==1 && in_array($send_good_status,array(1,4))){
            $salesModel->clear_order_goods_point($order_goods_id);
            if(!empty($jifenma_code)){
                $salesModel->update_jifenma_status($jifenma_code);
            }
            if(!empty($daijinquan_code)){
                $update_daijinquan_status_data = array('used_time'=>null ,'order_sn'=>'' ,'is_used'=>0,'daijinquan_code'=>$daijinquan_code);
                Util::point_api_update_daijinquan($update_daijinquan_status_data);
            }
        }*/

		/**
		 * 申请退款 退款类型为退商品 并且是打卡和转单的 需要解绑代金卷，在原有订单上保存代金卷的记录
		 */
		if($returnGoodsModel->getValue('return_by')==1 && in_array($returnGoodsModel->getValue('return_type'),[2,3]) )
		{
			if(!empty($daijinquan_code)){
        $update_daijinquan_status_data = array('used_time'=>null ,'order_sn'=>'' ,'is_used'=>0,'daijinquan_code'=>$daijinquan_code);
        $salesModel->remove_order_details_jifenma($order_goods_id);
				Util::point_api_update_daijinquan($update_daijinquan_status_data);
			}
		}

		/**
		 * 积分码逻辑
		 */
		if($returnGoodsModel->getValue('return_by')==1 && in_array($send_good_status,array(1,4)))
		{
			$salesModel->clear_order_goods_point($order_goods_id);
			if(!empty($jifenma_code))
			{
				$salesModel->update_jifenma_status($jifenma_code);
			}
		}

	    $result['success'] = 1;
	    return $result;
	    
	}    
    //库管驳回    
    public function rejectOne($id,$goods_res,$order_info){
        
      $salesModel = $this->salesModel;
      $returnLogModel = $this->returnLogModel;
      $returnGoodsModel = new AppReturnGoodsModel($id,27);//退货表实体Model
   // $returnGoodsModel->setValue('check_status', 2);
      $returnGoodsModel->savedates($id);    
	  //$returnGoodsModel->setLeaderStatus($id);
         //添加库管审核意见
      $updatedata = array ();
      $updatedata ['leader_status'] = 0;//主管审批意见 置空
      $updatedata ['leader_res'] = '';//主管审批意见 置空
      $updatedata ['leader_id'] = null;//主管审批意见 置空
      $updatedata ['leader_time'] = null;//主管审批时间 置空
      
      $updatedata ['goods_status'] = 2;
      $updatedata ['goods_res'] = $goods_res;
      $updatedata ['goods_comfirm_id'] = $_SESSION['userId']; 
      $updatedata ['goods_time'] = date ("Y-m-d H:i:s");

      // 改变产品状态
      $order_goods_id=$returnGoodsModel->getValue('order_goods_id');
      if($order_goods_id>0){
        $updateReturn=$salesModel->udpateAppOrderDetailsReturn($order_goods_id);
      }
      //更新审核状态
      $updateResult = $salesModel->udpateAppReturnCheck($updatedata,"return_id='{$id}'");
      if(!$updateResult){
          $msg = '库管驳回失败！error:'.__LINE__;
          $result['error'] = $msg;
	      return $result;
      }
      //库管审核日志
      $logData = array();
      $logData['return_id'] = $id;
      $logData['even_time'] = date("Y-m-d H:i:s");
      $logData['even_user'] = $_SESSION ['userName'];
      $logData['even_content'] = "部门库管审核驳回，驳回原因:" . $goods_res;
      $res = $returnLogModel->saveData($logData, array());
      if(!$res){
          $result['error'] = '库管驳回日志写入失败！error:'.__LINE__;
          return $result;
      }
       //订单操作日志：库管审核驳回
       $insert_action = array ();
       $insert_action ['order_id'] = $order_info ['id'];
       $insert_action ['order_status'] = $order_info ['order_status'];
       $insert_action ['shipping_status'] = $order_info ['send_good_status'];
       $insert_action ['pay_status'] = $order_info['order_pay_status'];
       $insert_action ['remark'] =  '退款/退货单'.$id.':库管审核驳回;驳回原因:' . $goods_res;
       $insert_action ['create_user'] = $_SESSION ['userName'];
       $insert_action ['create_time'] = date ( 'Y-m-d H:i:s' );
       $res = $salesModel->addOrderAction($insert_action);
       if(!$res){            
          $result['error'] = '库管驳回日志写入失败！error:'.__LINE__;
	      return $result;
       }  
       $result['success'] = 1;
       return $result; 
       
  }
        
        
   //ln 和供应商解除关系
   public function unBangProcessor($data){
       $processorModel = new ApiProcessorModel();
       $processorModel->relieveProduct($data);
   }
    
   //ln 和仓储解除关系
   public function unBangWarehouse($data){
       $warehouseModel = new ApiWarehouseModel();
       $warehouseModel->BindGoodsInfoByGoodsId($data);
   }
   
   //ln 现货仓储绑定
    public function getWarehouseGoods($data){
         //现货需要：查看此商品是否已经绑定仓储
         $warehouseModel = new ApiWarehouseModel();
         $warehouse_goods =$warehouseModel->getWarehouseGoodsInfo($data);
        
         return $warehouse_goods;
    }
    
   //ln 生成销售退货单
   public function createSaleBackGoods($order_sn,$return_id,$create_user,$warehouse_id,$order_goods){
      // createReturnGoodsBill
      //print_r($order_goods);exit();
       $warehouseModel = new ApiWarehouseModel();
       $jxc_order = $warehouseModel->createReturnGoodsBill(array('order_sn'=>$order_sn,'return_id'=>$return_id,'create_user'=>$create_user,'warehouse_id'=>$warehouse_id,'order_goods'=>$order_goods));
       return $jxc_order;
   }
    
   //ln 销售政策上架
   public function updateSalepolicy($data) {
        $salePolicyModel = new ApiSalePolicyModel();
        $salePolicyModel->UpdateAppPayDetail($data);
    }
}

?>
