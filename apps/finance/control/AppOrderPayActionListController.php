<?php

/**
 *  -------------------------------------------------
 *   @file		: AppOrderPayActionController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-19 18:16:49
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderPayActionListController extends CommonController {

    protected $smartyDebugEnabled = false;
    protected $whitelist = array('printInfo','printInfoSearch');

    /**
     * 	index，搜索框
     */
    public function index($params) {
    	$SalesChannelsModel = new SalesChannelsModel(1);
        if($_SESSION['userType'] == 1){
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
        }else{
            $ids = explode(',', $_SESSION['qudao']);
            $channellist = $SalesChannelsModel->getSalesChannel($ids);
        }
        
        $this->render('app_order_pay_action_list_search_form.html', array('bar' => Auth::getBar(),'channellist'=>$channellist));
    }

    /**
     * 	search，列表
     */
    public function search($params) {
        $startdate = date("Y-m-d");
        $enddate = date("Y-m-d");
        $_start_time = _Request::getString('start_time')?_Request::getString('start_time'):$startdate;
        $_end_time = _Request::getString('end_time')?_Request::getString('end_time'):$enddate;
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
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'department' => $department,
            'status' => _Request::getInt('status'),
            'start_time' =>$_start_time,
            'end_time' => $_end_time,
            'order_sn' => _Request::getString('order_sn'),
            'opter_name' => _Request::getString('opter_name')

        );
        $page = _Request::getInt("page", 1);
        $where = array();
        
        $where['department'] = $args['department'];
        $where['status'] = $args['status'];
        $where['start_time'] = $args['start_time'];
        $where['end_time'] = $args['end_time'];
        $where['order_sn'] = $args['order_sn'];
        $where['opter_name'] = $args['opter_name'];
        
        $model = new AppOrderPayActionModel(301);

		$data_arr=$model->get_data_arr($where['start_time'],$where['end_time']);
        if(count($data_arr)>31){
			die('请查询31天范围内的信息!');
        }

        $data = $model->getAllList($where);
        //$get_Data_All = $model->getAllList($where);
        //统计 状态未提报 订单信息   页面提报状态数量显示
       // echo 111;exit;
       /*$_datas ='';
       if($where['status']==1){
       		//未提报订单数量 和总额
       		$_data=$model->orderTongJi($where,1);
       		$_datas=1;
       		//$_datas[1]=$model->orderTongJi_status($where,1);
       }elseif($where['status']==2){
       		//已提报订单数量 和总额
       		$_data=$model->orderTongJi($where,2);
       		$_datas=2;
       }elseif($where['status']==3){
       		//已审核订单数量 和总额
       		 $_data=$model->orderTongJi($where,3);
       		 $_datas=3;
       }else{	      	
       		$_data=$model->orderTongJi($where,'');
       		$_datas=4;
       }*/
        //$_data=$model->orderTongJi($where,'');   paid_cnt
        $payments=array();
        $paymentModel=new PaymentModel(0);
        $paymentres=$paymentModel->getEnabled();
        foreach ($paymentres as $key => $value) {
            $payments['key_'.$value['id']]['pay_money']=0;
            $payments['key_'.$value['id']]['pay_name']=$value['pay_name'];            
        }



        $result=array();
        $order_amount = array();
        if($data){
            
            $result["unti_money"]=0.00;
            $result["ti_money"]=0.00;
            $result["checked_money"]=0.00;
            $result["unchecked_money"]=0.00;
            $result["unti_money_num"]=0;
            $result["ti_money_num"]=0;
            $result["checked_money_num"]=0;
            $result["unchecked_money"]=0;

            $result["paid_cnt"]=0;
            $result["paid_amont"] = 0.00;
            $result["zhuanzengpin_pay"] = 0.00;

            $result["zhuanzhang_money"]=0.00;
            $result["other_money"]=0.00;
            $result["now_money"]=0.00;
            $result["order_pay"]=0.00;
            $result["zhishou"]=0.00;
            $result['shishou_price']=0.00;
            $result['card_money']=0.00;
            $result['qianfang_money']=0.00;
            $result['zhifubao_money']=0.00;
            $result['weixinpay_money']=0.00;
            $result['ylpos_pay_money']=0.00;
            $result['fkpos_pay_money']=0.00;
            $result['gsyh_pay_money']=0.00;
            $result['order_num']=count($data);
            
            
            foreach($data as $key=>$value){

                
                if($value['pay_type']!=272){
                     $result['zhishou']+=$value['deposit'];   
                }else{
                    $result['zhishou']+=0.00;
                }

                if ($value["order_amount"]== "0")
                {
                    $result["paid_amont"] += $value['deposit'];         // 销售单收款
                    $result["paid_cnt"]+=1;                             // 销售单数量
                }
                
                if(!array_key_exists($value['order_id'], $order_amount)){
                    $result['shishou_price']+=$value['order_amount'];
                    $order_amount[$value['order_id']] = $value['order_id'];
                }

                switch($value["status"]){
                    case '1':// 未提报
                        $result["unti_money"] += $value["deposit"];
                        $result["unti_money_num"]++;
                        break;
                    case '2':// 已提报
                        $result["ti_money"] += $value["deposit"];
                        $result["ti_money_num"]++;
                        break;
                    case '3':// 已审核 
                        $result["checked_money"] += $value["deposit"];
                        $result["checked_money_num"]++;
                        break;
                    case '4':// 未通过 
                        $result["unchecked_money"] += $value["deposit"];
                        $result["unchecked_money_num"]++;
                        break;
                    default:
                }
                

                $all_pay_type = array(); 

                
                /*
                if ($value["pay_type"] == 269)
                {
                    // 现金
                    $result["now_money"] += $value["deposit"]>0?$value["deposit"]:0;
                }
                elseif ($value["pay_type"] == 3 || $value["pay_type"]==276)
                {
                    // 转账
                    $result["zhuanzhang_money"] += $value["deposit"]>0?$value["deposit"]:0;
                }
                elseif ($value["pay_type"] == 272)
                {
                    // 转单
                    $result["order_pay"] += $value["deposit"]>0?$value["deposit"]:0;
                }
                elseif ($value["pay_type"] == "团购")
                {
                    // 团购
                    $result["tuangou_pay"] += $value["deposit"]>0?$value["deposit"]:0;
                }
                elseif ($value["pay_type"] == "异业合作结算")
                {
                    // 异业合作结算
                    $result["yiye_pay"] += $value["deposit"]>0?$value["deposit"]:0;
                }
                elseif ($value["pay_type"] == 224)
                {
                    // 商品转赠品
                    $result["zhuanzengpin_pay"] += $value["deposit"]>0?$value["deposit"]:0;
                }
                elseif ($value["pay_type"] == 227)
                {
                    // 丢货赔偿
                    $result["lost_repay"] += $value["deposit"]>0?$value["deposit"]:0;
                }
                elseif ($value["pay_type"] == 54 || $value["pay_type"] == 55 || $value["pay_type"] == 256 || $value["pay_type"] == 270)
                {

                    // 刷卡
                    $result["card_money"] += $value["deposit"]>0?$value["deposit"]:0.00;
                }elseif ($value["pay_type"] == 285){

                    //钱方支付
                    $result['qianfang_money'] += $value["deposit"]>0?$value['deposit']:0.00;
                }elseif ($value["pay_type"] == 302){

                    //支付宝支付（店面）
                    $result['zhifubao_money'] += $value["deposit"]>0?$value['deposit']:0.00;
                }elseif ($value["pay_type"] == 301){

                    //微信扫码支付
                    $result['weixinpay_money'] += $value["deposit"]>0?$value['deposit']:0.00;
                }elseif ($value["pay_type"] == 318){

                    //银联POS
                    $result['ylpos_pay_money'] += $value["deposit"]>0?$value['deposit']:0.00;
                }elseif ($value["pay_type"] == 286){

                    //福卡POS
                    $result['fkpos_pay_money'] += $value["deposit"]>0?$value['deposit']:0.00;
                }elseif ($value["pay_type"] == 243){

                    //工商银行支付
                    $result['gsyh_pay_money'] += $value["deposit"]>0?$value['deposit']:0.00;
                }
                else
                {
                    // 其他 checked_money
                    $result["other_money"] += $value["deposit"]>0?$value["deposit"]:0;
                }
                */
               if ($value["pay_type"] == "团购")
                {
                    // 团购
                    $result["tuangou_pay"] += $value["deposit"]>0?$value["deposit"]:0;
                }
                elseif ($value["pay_type"] == "异业合作结算")
                {
                    // 异业合作结算
                    $result["yiye_pay"] += $value["deposit"]>0?$value["deposit"]:0;
                }elseif(array_key_exists('key_'.$value["pay_type"],$payments)){
                    $payments['key_'.$value["pay_type"]]['pay_money'] += $value["deposit"]>0?$value['deposit']:0.00;
                }else{
                    // 其他 checked_money
                    $result["other_money"] += $value["deposit"]>0?$value["deposit"]:0;
                }                
            }
        }
        $result['payments']=$payments;

        $SalesChannelsModel = new SalesChannelsModel(1);
        $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
        //获取所有数据
        $allSalesChannelsData = array();
        foreach ($getSalesChannelsInfo as $val){
            $allSalesChannelsData[$val['id']] = $val['channel_name'];
        }

        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'app_order_pay_action_list_search_page';
        $this->render('app_order_pay_action_list_search_list.html', array(
            'allSalesChannelsData'=>$allSalesChannelsData,
            'pa' => Util::page($pageData),
            'page_list' => $data,
            'payView'=>new PaymentView(new PaymentModel(1)),
        	'start_time'=>$where['start_time'],
        	'end_time'=>$where['end_time'],
        	'result'=>$result,
        ));
    }
    
    
    
    public function printInfo($param) {
        $SalesChannelsModel = new SalesChannelsModel(1);
        if($_SESSION['userType'] == 1){
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
        }else{
            $ids = explode(',', $_SESSION['qudao']);
            $channellist = $SalesChannelsModel->getSalesChannel($ids);
        }
            
        $startdate = date("Y-m-d");
        $enddate = date("Y-m-d");
        $_start_time = _Request::getString('start_time')?_Request::getString('start_time'):$startdate;
        $_end_time = _Request::getString('end_time')?_Request::getString('end_time'):$enddate;
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
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'department' => $department,
            'status' => _Request::getInt('status'),
            'start_time' =>$_start_time,
            'end_time' => $_end_time,
            'order_sn' => _Request::getString('order_sn')

        );
        $page = _Request::getInt("page", 1);
        $where = array();
        
        $where['department'] = $args['department'];
        $where['status'] = $args['status'];
        $where['start_time'] = $args['start_time'];
        $where['end_time'] = $args['end_time'];
        $where['order_sn'] = $args['order_sn'];

        $model = new AppOrderPayActionModel(301);
        $data = $model->getAllList($where);
        //统计 状态未提报 订单信息   页面提报状态数量显示
        // echo 111;exit;
//        $_datas ='';
//        if($where['status']==1){
//             //未提报订单数量 和总额
//             $_data=$model->orderTongJi($where,1);
//             $_datas=1;
//             //$_datas[1]=$model->orderTongJi_status($where,1);
//        }elseif($where['status']==2){
//             //已提报订单数量 和总额
//             $_data=$model->orderTongJi($where,2);
//             $_datas=2;
//        }elseif($where['status']==3){
//             //已审核订单数量 和总额
//              $_data=$model->orderTongJi($where,3);
//              $_datas=3;
//        }else{	      	
//             $_data=$model->orderTongJi($where,'');
//             $_datas=4;
//        }
        //var_dump($_data);exit;          
        $result=array();
        $order_amount = array();
        if($data){
            $result["unti_money"]=0.00;
            $result["ti_money"]=0.00;
            $result["checked_money"]=0.00;
            $result["unchecked_money"]=0.00;
            $result["unti_money_num"]=0;
            $result["ti_money_num"]=0;
            $result["checked_money_num"]=0;
            $result["unchecked_money"]=0;

            $result["paid_cnt"]=0;
            $result["paid_amont"] = 0.00;
            $result["zhuanzengpin_pay"] = 0.00;

            $result["zhuanzhang_money"]=0.00;
            $result["other_money"]=0.00;
            $result["now_money"]=0.00;
            $result["order_pay"]=0.00;
            $result["zhishou"]=0.00;
            $result['shishou_price']=0.00;
            $result['card_money']=0.00;
            $result['order_num']=count($data);

            foreach($data as $key=>$value){
                if($value['pay_type']!=272){
                     $result['zhishou']+=$value['deposit'];   
                }else{
                    $result['zhishou']+=0.00;
                }

                if ($value["order_amount"]== "0")
                {
                    $result["paid_amont"] += $value['deposit'];         // 销售单收款
                    $result["paid_cnt"]+=1;                             // 销售单数量
                }
                
                if(!array_key_exists($value['order_id'], $order_amount)){
                    $result['shishou_price']+=$value['order_amount'];
                    $order_amount[$value['order_id']] = $value['order_id'];
                }

                switch($value["status"]){
                    case '1':// 未提报
                        $result["unti_money"] += $value["deposit"];
                        $result["unti_money_num"]++;
                        break;
                    case '2':// 已提报
                        $result["ti_money"] += $value["deposit"];
                        $result["ti_money_num"]++;
                        break;
                    case '3':// 已审核 
                        $result["checked_money"] += $value["deposit"];
                        $result["checked_money_num"]++;
                        break;
                    case '4':// 未通过 
                        $result["unchecked_money"] += $value["deposit"];
                        $result["unchecked_money_num"]++;
                        break;
                    default:
                }
                

                $all_pay_type = array(); 

                
                if ($value["pay_type"] == 269)
                {
                    // 现金
                    $result["now_money"] += $value["deposit"]>0?$value["deposit"]:0;
                }
                elseif ($value["pay_type"] == 3 || $value["pay_type"]==276)
                {
                    // 转账
                    $result["zhuanzhang_money"] += $value["deposit"]>0?$value["deposit"]:0;
                }
                elseif ($value["pay_type"] == 272)
                {
                    // 转单
                    $result["order_pay"] += $value["deposit"]>0?$value["deposit"]:0;
                }
                elseif ($value["pay_type"] == "团购")
                {
                    // 团购
                    $result["tuangou_pay"] += $value["deposit"]>0?$value["deposit"]:0;
                }
                elseif ($value["pay_type"] == "异业合作结算")
                {
                    // 异业合作结算
                    $result["yiye_pay"] += $value["deposit"]>0?$value["deposit"]:0;
                }
                elseif ($value["pay_type"] == 224)
                {
                    // 商品转赠品
                    $result["zhuanzengpin_pay"] += $value["deposit"]>0?$value["deposit"]:0;
                }
                elseif ($value["pay_type"] == 227)
                {
                    // 丢货赔偿
                    $result["lost_repay"] += $value["deposit"]>0?$value["deposit"]:0;
                }
                elseif ($value["pay_type"] == 54 || $value["pay_type"] == 55 || $value["pay_type"] == 256 || $value["pay_type"] == 270)
                {

                    // 刷卡
                    $result["card_money"] += $value["deposit"]>0?$value["deposit"]:0.00;
                }
                else
                {
                    // 其他 checked_money
                    $result["other_money"] += $value["deposit"]>0?$value["deposit"]:0;
                }
            }
        }
      
       // var_dump($_data);
        $SalesChannelsModel = new SalesChannelsModel(1);
        $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
        //获取所有数据
        $allSalesChannelsData = array();
        foreach ($getSalesChannelsInfo as $val){
            $allSalesChannelsData[$val['id']] = $val['channel_name'];
        }

        //$this->render('pay_action_print.html',array('start_date'=>date("Y-m")."-01",'end_date'=>date("Y-m-d"),'channellist'=>$channellist));
        $this->render('pay_action_print.html', array(
            'allSalesChannelsData'=>$allSalesChannelsData,
            'page_list' => $data,'payView'=>new PaymentView(new PaymentModel(1)),
            //'tongji'=>$_data,
        	//'tongjis'=>$_datas,
            'result'=>$result,
            'start_date'=>$_start_time,
            'end_date'=>$_end_time,
            'channellist'=>$channellist,
        ));
    }

    /**
     * 	add，渲染添加页面
     */
    public function add() {
        $result = array('success' => 0, 'error' => '');
        $result['content'] = $this->fetch('app_order_pay_action_info.html', array(
            'view' => new AppOrderPayActionView(new AppOrderPayActionModel(301))
        ));
        $result['title'] = '添加';
        Util::jsonExit($result);
    }

    /**
     * 	saveDeposit，收银提报
     */
    public function saveDeposit() {
        $result = array('success' => 0, 'error' => '');
        $ids = _Post::getList('ids');
        if (count($ids) < 1) {
            $result['error'] = '至少要选中一个序号';
            Util::jsonExit($result);
        }
        $model = new AppOrderPayActionModel(301);
        $_do = $model->getStatusList($ids);
        $do = array();
        foreach ($_do as $key => $value) {
           $do[] = $value['pay_id']; 
        }
        if (count($do) > 0) {
            $ids = array_diff($ids, $do);
        }
        if (empty($ids)) {
            $result['error'] = '操作失败';
            Util::jsonExit($result);
        }
        $res = $model->updateListStatus($ids,2);

        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '作废失败';
        }
        Util::jsonExit($result);
    }

    /**
     * 	payPrice, 点款
     */
    public function payPrice($param) {
        $result = array('success' => 0, 'error' => '');
        $order_id = _Request::getInt('id');
        $the_order_amount = 300;
        $paymentModel = new PaymentModel(1);
        $paymentList = $paymentModel->getList();
        $result['content'] = $this->fetch('app_order_pay_action_info.html', array(
            'the_order_amount' => $the_order_amount,
            'order_id' => $order_id,
            'paymentList' => $paymentList
        ));
        $result['title'] = '点款';
        Util::jsonExit($result);
    }

    /**
     * 	edit，渲染修改页面
     */
    public function edit($params) {
        $id = intval($params["id"]);
        $tab_id = intval($params["tab_id"]);
        $result = array('success' => 0, 'error' => '');
        $result['content'] = $this->fetch('app_order_pay_action_info.html', array(
            'view' => new AppOrderPayActionView(new AppOrderPayActionModel($id, 301)),
            'tab_id' => $tab_id
        ));
        $result['title'] = '编辑';
        Util::jsonExit($result);
    }

    /**
     * 	show，渲染查看页面
     */
    public function show($params) {
        $id = intval($params["id"]);
        $this->render('app_order_pay_action_show.html', array(
            'view' => new AppOrderPayActionView(new AppOrderPayActionModel($id, 301)),
            'bar' => Auth::getViewBar()
        ));
    }

    /**
     * 	insert，信息入库
     */
    public function insert($params) {
        $result = array('success' => 0, 'error' => '');

        $is_dingjin = false;
        $order_id = 12333;
        $order_sn = '483474884';
        $order_time = "2015-01-19 15:46:35";
        $order_amount = 500;
        $deposit = _Post::getFloat('order_deposit');
        $balance = 100;
        $remark = _Post::getString('action_note');
        $pay_time = _Post::getString('pay_time');
        $pay_type = _Post::getString('pay_type');
        $pay_fee = _Post::getFloat('order_deposit');
        $card_no = _Post::getString('card_no');
        $card_voucher = _Post::getString('card_voucher');
        $order_consignee = 'dsf身份登上飞机发牢骚';
        $proof_sn = _Post::getString('deposit_sn');
        if ($proof_sn != '') {
            $is_dingjin = true;
        }
        $department = 4;
        $status = 1;
        $pay_checker = $_SESSION['userName'];
        $pay_check_time = date("Y-m-d");
        $system_flg = 1;
        $orderModel = new ApiOrderModel();
        $orderInfo = $orderModel->getOrderList($order_id);
        $olddo = array();
        $create_time = date("Y-m-d H:i:s");
        $newdo = array();
        $newdo['order_id'] = $order_id;
        $newdo['order_sn'] = $order_sn;
        $newdo['order_time'] = $order_time;
        $newdo['deposit'] = $deposit;
        $newdo['order_amount'] = $order_amount;
        $newdo['balance'] = $balance;
        $newdo['remark'] = $remark;
        $newdo['pay_time'] = $pay_time;
        $newdo['order_consignee'] = $order_consignee;
        $newdo['proof_sn'] = $proof_sn;
        $newdo['department'] = $department;
        $newdo['status'] = $status;
        $newdo['pay_check_time'] = $pay_check_time;
        $newdo['pay_checker'] = $pay_checker;
        $newdo['system_flg'] = $system_flg;
        $newmodel = new AppOrderPayActionModel(301);
        $res = $newmodel->saveData($newdo, $olddo);

        if ($res !== false) {
            if ($is_dingjin) {

                $_modelList = new AppReceiptDepositModel(301);
                $id = $_modelList->getIdBySn($proof_sn);
                $_modelList = new AppReceiptDepositModel($id, 301);
                $_modelList->setValue('status', 2);
                $_res = $_modelList->save(true);

                if ($_res) {
                    $_model = new AppReceiptDepositLogModel(301);
                    $_newdo = array();
                    $_newdo['receipt_id'] = $id;
                    $_newdo['receipt_action'] = '定金收据使用成功';
                    $_newdo['add_time'] = date("Y-m-d H:i:s");
                    $_newdo['add_user'] = $_SESSION['userName'];
                    $_model->saveData($_newdo, $olddo);
                }
            }

            $djModel = new AppReceiptDepositModel(301);
            $receipt_sn = $djModel->create_receipt('DK');

            $_do = array();
            $_do['order_sn'] = $order_sn;
            $_do['receipt_sn'] = $receipt_sn;
            $_do['customer'] = '$customer';
            $_do['department'] = 4;
            $_do['pay_fee'] = $pay_fee;
            $_do['pay_type'] = $pay_type;
            $_do['pay_time'] = $pay_time;
            $_do['card_no'] = $card_no;
            $_do['card_voucher'] = $card_voucher;
            $_do['status'] = 1;
            $_do['print_num'] = 0;
            $_do['pay_user'] = $_SESSION['userName'];
            $_do['remark'] = $remark;
            $_do['add_time'] = date("Y-m-d H:i:s");
            $_do['add_user'] = $_SESSION['userName'];
            $_doModel = new AppReceiptPayModel(301);
            $_doRet = $_doModel->saveData($_do, array());

            if ($_doRet !== FALSE) {
                $log_model = new AppReceiptPayLogModel(301);
                $_newarr = array();
                $_newarr['receipt_id'] = $_doRet;
                $_newarr['receipt_action'] = '添加点款收据成功';
                $_newarr['add_time'] = date("Y-m-d H:i:s");
                $_newarr['add_user'] = $_SESSION['userName'];
                $log_model->saveData($_newarr, array());
                $log_model->save(true);
            }

            $result['success'] = 1;
        } else {
            $result['error'] = '添加失败';
        }
        Util::jsonExit($result);
    }

    /**
     * 	update，更新信息
     */
    public function update($params) {
        $result = array('success' => 0, 'error' => '');
        $_cls = _Post::getInt('_cls');
        $tab_id = _Post::getInt('tab_id');

        $id = _Post::getInt('id');
        $newmodel = new AppOrderPayActionModel($id, 301);

        $olddo = $newmodel->getDataObject();
        $newdo = array(
        );

        $res = $newmodel->saveData($newdo, $olddo);
        if ($res !== false) {
            $result['success'] = 1;
            $result['_cls'] = $_cls;
            $result['tab_id'] = $tab_id;
            $result['title'] = '修改此处为想显示在页签上的字段';
        } else {
            $result['error'] = '修改失败';
        }
        Util::jsonExit($result);
    }

    /**
     * 	delete，删除
     */
    public function delete($params) {
        $result = array('success' => 0, 'error' => '');
        $id = intval($params['id']);
        $model = new AppOrderPayActionModel($id, 301);
        $do = $model->getDataObject();
        $valid = $do['is_system'];
        if ($valid) {
            $result['error'] = "当前记录为系统内置，禁止删除";
            Util::jsonExit($result);
        }
        $model->setValue('is_deleted', 1);
        $res = $model->save(true);
        //联合删除？
        //$res = $model->delete();
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = "删除失败";
        }
        Util::jsonExit($result);
    }


    function updatePayType(){

        $ids = _Request::getList("_ids");
        $result = array('title'=>'更新支付类型','content'=>'');
        if(empty($ids)){
            $result['content'] = "ids is empty！";
            Util::jsonExit($result);
        }

        $paymentModel = new PaymentModel(1);
        $paymentList = $paymentModel->getList();
        if(!empty($paymentList)){
            foreach ($paymentList as $k => $v) {
                if($v['is_enabled'] == 0){
                    unset($paymentList[$k]);
                }
            }
        }


        $model = new AppOrderPayActionModel(301);
        $error_ids= array();
        $error_pay_ids = $model->getStatusList($ids);
        $error_ids = array_column($error_pay_ids,'pay_id');

        if(empty($error_ids)){
            $ids = implode('|',$ids);
            $result['content'] = $this->fetch('update_pay_type.html',array(
                'ids'=>$ids,
                'paymentList'=>$paymentList
            ));
        }else{
            $error_ids = implode(',',$error_ids);
            $result['content'] = "序号为{$error_ids}的收银提货已经提报，不能更改支付类型！";
        }
        Util::jsonExit($result);
    }


    /**
     *	审核、驳回
     */
    public function updatePayTypeAction ($params)
    {
        $result = array('success' => 0,'error' =>'');
        $pay_ids =  _Post::getString('pay_ids');
        $pay_ids = explode('|',$pay_ids);
        if(empty($pay_ids)){
            $result['error'] = '订单流水号为空！';
            Util::jsonExit($result);
        }

        $pay_type = _Post::getInt('pay_type');
        if($pay_type < 1){
            $result['error'] = '支付类型不能为空！';
            Util::jsonExit($result);
        }
        $model = new AppOrderPayActionModel(301);
        $pdolist = $model->db()->db();
        try{
            $pdolist->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
            $pdolist->beginTransaction(); //开启事务
        }catch (Exception $e){
            $error = "操作失败，事物回滚！提示：系统批量开启事物时发生异常！";
            Util::rollbackExit($error,$pdolist);
        }

        foreach ($pay_ids as $pay_id){
            $res = $model->updatePayType($pay_id, $pay_type);
            if(!$res){
                $error = "流水号【{$pay_id}】支付类型更改失败,提示：不是未提报状态";
                Util::rollbackExit($error,$pdolist);
            }
        }

        try{
            //Util::rollbackExit('test',$pdolist);
            //批量提交事物
            $pdolist->commit();
            $pdolist->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
            $result['success'] = 1;
            Util::jsonExit($result);
        }catch (Exception $e){
            $error = "更改失败!".$e->getMessage();
            Util::rollbackExit($error,$pdolist);
        }
    }

}

?>