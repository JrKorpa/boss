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
class AppOrderPayActionCheckController extends CommonController {

    protected $smartyDebugEnabled = false;

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
        $this->render('app_order_pay_action_check_search_form.html', array('bar' => Auth::getBar(),'channellist'=>$channellist));
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
                //'参数' = _Request::get("参数");
        );
        $page = _Request::getInt("page", 1);
        $where = array();
        $where['department'] = $args['department'];
        $where['status'] = $args['status'];
        $where['start_time'] = $args['start_time'];
        $where['end_time'] = $args['end_time'];
        $where['order_sn'] = $args['order_sn'];
        $where['opter_name'] = $args['opter_name'];
        $model = new AppOrderPayActionModel(29);

		$data_arr=$model->get_data_arr($where['start_time'],$where['end_time']);
        if(count($data_arr)>31){
			die('请查询31天范围内的信息!');
        }

        $data = $model->getAllList($where);

        $payments=array();
        $paymentModel=new PaymentModel(0);
        $paymentres=$paymentModel->getEnabled();
        foreach ($paymentres as $key => $value) {
            $payments['key_'.$value['id']]['pay_money']=0;
            $payments['key_'.$value['id']]['pay_name']=$value['pay_name'];            
        }



        $result=array();
        $order_amount = array();
        if(!empty($data)){
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

            $result["zhuanzhang_money"]=0.00;
            $result["other_money"]=0.00;
            $result["now_money"]=0.00;
            $result["order_pay"]=0.00;
            $result["zhishou"]=0.00;
            $result['shishou_price']=0.00;
            $result['card_money']=0.00;
            $result['qianfang_money']=0.00;
            $result['order_num']=count($data);
            foreach($data as $key=>$value){
				$result['zhishou']+=$value['deposit'];   

                if ($value["order_amount"]== "0")
                {
                    $result["paid_amont"] += $value['deposit'];         // 销售单收款
                    $result["paid_cnt"]+=1;                              // 销售单数量
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
                }
                else
                {
                    // 其他 checked_money
                    $result["other_money"] += $value["deposit"]>0?$value["deposit"]:0;
                }
                */
                if(array_key_exists('key_'.$value["pay_type"],$payments)){
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
        $pageData['jsFuncs'] = 'app_order_pay_action_check_search_page';

        $this->render('app_order_pay_action_check_search_list.html', array(
            'allSalesChannelsData'=>$allSalesChannelsData,
            'pa' => Util::page($pageData),
            'page_list' => $data,'payView'=>new PaymentView(new PaymentModel(1)),
        	//'tongji'=>'',
        	//'tongji'=>$_data,
        	//'tongjis'=>$_datas,
        	'start_time'=>$where['start_time'],
        	'end_time'=>$where['end_time'],
        	'result'=>$result,
        ));
    }

    /**
     * 	add，渲染添加页面
     */
    public function add() {
        $result = array('success' => 0, 'error' => '');
        $result['content'] = $this->fetch('app_order_pay_action_info.html', array(
            'view' => new AppOrderPayActionView(new AppOrderPayActionModel(29))
        ));
        $result['title'] = '添加';
        Util::jsonExit($result);
    }

    /**
     * 	saveDeposit，会计审核通过
     */
    public function checkTrue() {
        $result = array('success' => 0, 'error' => '');
        $ids = _Post::getList('ids');
        if (count($ids) < 1) {
            $result['error'] = '至少要选中一个序号';
            Util::jsonExit($result);
        }
        $model = new AppOrderPayActionModel(30);
        $_do = $model->getCheckStatus($ids);
        
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
        $res = $model->updateListStatus($ids,3);

        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '作废失败';
        }
        Util::jsonExit($result);
    }
    /**
     * 	saveDeposit，会计审核驳回
     */
    public function checkStop() {
        $result = array('success' => 0, 'error' => '');
        $ids = _Post::getList('ids');
        if (count($ids) < 1) {
            $result['error'] = '至少要选中一个序号';
            Util::jsonExit($result);
        }
        $model = new AppOrderPayActionModel(30);
        $_do = $model->getCheckStatus($ids);
        
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
        $res = $model->updateListStatus($ids,4);

        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '驳回失败';
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
            'view' => new AppOrderPayActionView(new AppOrderPayActionModel($id, 29)),
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
            'view' => new AppOrderPayActionView(new AppOrderPayActionModel($id, 29)),
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
        $newmodel = new AppOrderPayActionModel(30);
        $res = $newmodel->saveData($newdo, $olddo);

        if ($res !== false) {
            if ($is_dingjin) {

                $_modelList = new AppReceiptDepositModel(30);
                $id = $_modelList->getIdBySn($proof_sn);
                $_modelList = new AppReceiptDepositModel($id, 30);
                $_modelList->setValue('status', 2);
                $_res = $_modelList->save(true);

                if ($_res) {
                    $_model = new AppReceiptDepositLogModel(30);
                    $_newdo = array();
                    $_newdo['receipt_id'] = $id;
                    $_newdo['receipt_action'] = '定金收据使用成功';
                    $_newdo['add_time'] = date("Y-m-d H:i:s");
                    $_newdo['add_user'] = $_SESSION['userName'];
                    $_model->saveData($_newdo, $olddo);
                }
            }

            $djModel = new AppReceiptDepositModel(29);
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
            $_doModel = new AppReceiptPayModel(30);
            $_doRet = $_doModel->saveData($_do, array());

            if ($_doRet !== FALSE) {
                $log_model = new AppReceiptPayLogModel(30);
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
        $newmodel = new AppOrderPayActionModel($id, 30);

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
        $model = new AppOrderPayActionModel($id, 30);
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

}

?>