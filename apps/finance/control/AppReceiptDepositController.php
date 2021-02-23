<?php

/**
 *  -------------------------------------------------
 *   @file		: AppReceiptDepositController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-19 14:29:53
 *   @update	:
 *  -------------------------------------------------
 */
class AppReceiptDepositController extends CommonController {

    protected $smartyDebugEnabled = false;
    protected $whitelist = array('download', 'printReceipt');

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
        
        $this->render('app_receipt_deposit_search_form.html', array('bar' => Auth::getBar(),'channellist'=>$channellist));
    }

    /**
     * 获取对应支付方式名称
     * @param type $id
     * @return type
     */
    public function getPayTypeName($id) {
        $payView = new PaymentView(new PaymentModel($id,1));
        return $payView->get_pay_name();
    }
    
    /**
     * 	search，列表
     */
    public function search($params) {
        if($_SESSION['userType']==1){
            $department = _Request::getString('pay_department')?_Request::getString('pay_department'):0;
        }else{
            if(isset($_REQUEST['pay_department'])){
                $department = _Request::getString('pay_department')?_Request::getString('pay_department'):($_SESSION['qudao']?$_SESSION['qudao']:-1);
            }else{
                $department = _Request::getString('pay_department')?_Request::getString('pay_department'):($_SESSION['qudao']?current(explode(',', $_SESSION['qudao'])):-1);
            }
        }
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'order_sn' => _Request::getString('order_sn'),
            'receipt_sn' => _Request::getString('receipt_sn'),
            'status' => _Request::getInt('status'),
            'pay_department' => $department,
            'pay_start_time' => _Request::getString('pay_start_time'),
            'pay_end_time' => _Request::getString('pay_end_time'),
            'add_start_time' => _Request::getString('add_start_time'),
            'add_end_time' => _Request::getString('add_end_time'),
            'type'=> _Request::getString('type'),
                //'参数' = _Request::get("参数");
        );

        $where = array();
//        $channerids = '';
//        $ChannelM = new SalesChannelsModel(1);
//        if($args['pay_department']!=''){
//            $channeridarr =  $ChannelM->getOwns($args['type'],$args['pay_department']);
//            if(!empty($channeridarr)){
//                foreach($channeridarr as $key=>$val){
//                    $channerids.=$val['id'].',';
//                }
//                $where['channerids']=rtrim($channerids,',');
//            }else{
//                $where['channerids']=0;
//            }
//
//        }else{
//            $where['channerids']=false;
//        }


        $page = _Request::getInt("page", 1);
        $where['order_sn'] = $args['order_sn'];
        $where['receipt_sn'] = $args['receipt_sn'];
        $where['status'] = $args['status'];
        $where['pay_start_time'] = $args['pay_start_time'];
        $where['pay_end_time'] = $args['pay_end_time'];
        $where['pay_department'] = $args['pay_department'];
        $where['add_start_time'] = $args['add_start_time'];
        $where['add_end_time'] = $args['add_end_time'];

        $model = new AppReceiptDepositModel(29);


        $data = $model->pageList($where, $page, 25, false);

        if ($data['data']) {
            foreach ($data['data'] as &$val) {
                $val['status'] = $model->getStatusList($val['status']);
                $val['pay_type'] = $this->getPayTypeName($val['pay_type']);
            }
            unset($val);
        }

        $SalesChannelsModel = new SalesChannelsModel(1);
        $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
        //获取所有数据
        $allSalesChannelsData = array();
        foreach ($getSalesChannelsInfo as $val){
            $allSalesChannelsData[$val['id']] = $val['channel_name'];
        }

        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'app_receipt_deposit_search_page';
        $this->render('app_receipt_deposit_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data,
            'allSalesChannelsData'=>$allSalesChannelsData
        ));
    }

    public function download($param) {
        if($_SESSION['userType']==1){
            $department = _Request::getString('pay_department')?_Request::getString('pay_department'):0;
        }else{
            if(isset($_REQUEST['pay_department'])){
                $department = _Request::getString('pay_department')?_Request::getString('pay_department'):($_SESSION['qudao']?$_SESSION['qudao']:-1);
            }else{
                $department = _Request::getString('pay_department')?_Request::getString('pay_department'):($_SESSION['qudao']?substr($_SESSION['qudao'], 0,1):-1);
            }
        }
        $args = array(
            'order_sn' => _Request::getString('order_sn'),
            'receipt_sn' => _Request::getString('receipt_sn'),
            'status' => _Request::getInt('status'),
            'pay_department' => $department,
            'pay_start_time' => _Request::getString('pay_start_time'),
            'pay_end_time' => _Request::getString('pay_end_time'),
            'add_start_time' => _Request::getString('add_start_time'),
            'add_end_time' => _Request::getString('add_end_time')
                //'参数' = _Request::get("参数");
        );
        $model = new AppReceiptDepositModel(29);

        $data = $model->pageList($args, 1, 10000000000000000, false);
        if ($data['data']) {
            //获取全部的有效的销售渠道
            $SalesChannelsModel = new SalesChannelsModel(1);
            $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
            //获取所有数据
            $allSalesChannelsData = array();
            foreach ($getSalesChannelsInfo as $val){
                $allSalesChannelsData[$val['id']] = $val['channel_name'];
            }
            foreach ($data['data'] as &$val) {
                $val['status'] = $model->getStatusList($val['status']);
                $val['department'] = $allSalesChannelsData[$val['department']];
            }
            unset($val);
            $down = $data['data'];
            $xls_content = "定金收据号码,状态,收款金额,收款方式,收款时间,收款人,收款方,订单号,客户姓名,收据日期,操作人\r\n";
            foreach ($down as $key => $val) {
                $xls_content .= $val['receipt_sn'] . ",";
                $xls_content .= $val['status'] . ",";
                $xls_content .= $val['pay_fee'] . ",";
                $xls_content .= $this->getPayTypeName($val['pay_type']). ",";
                $xls_content .= $val['pay_time'] . ",";
                $xls_content .= $val['pay_user'] . ",";
                $xls_content .= $val['department'] . ",";
                $xls_content .= $val['order_sn'] . ",";
                $xls_content .= $val['customer'] . ",";
                $xls_content .= $val['add_time'] . ",";
                $xls_content .= $val['add_user'] . "\n";
            }
        } else {
            $xls_content = '没有数据！';
        }
        header("Content-type: text/html; charset=gbk");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv");
        echo iconv("utf-8", "gbk", $xls_content);
        exit;
    }

    public function printReceipt() {
        $model = new AppReceiptDepositModel(29);
        $receipt_sn = _Request::getString('receipt_sn');
        $print = $model->getRowList($receipt_sn);
        //获取大写数字
        $money = array("0" => "零", "1" => "壹", "2" => "贰", "3" => "叁", "4" => "肆", "5" => "伍", "6" => "陆", "7" => "柒", "8" => "捌", "9" => "玖", "10" => "拾");

        $print['money'] = strrev($print['pay_fee']);
        $payView = new PaymentView(new PaymentModel($print['pay_type'],1));
        $print['pay_type'] = $payView->get_pay_name();
        $this->render('deposit_print.html', array('bigmoney' => $print['money'], 'money' => $money, 'print' => $print));
    }

    public function printCount() {
        $id = _Request::getInt('id');
        $model = new AppReceiptDepositModel($id, 30);
        $info = $model->getDataObject();
        $num = $info['print_num'] + 1;
        $model->setValue('print_num', $num);
        $res = $model->save(true);
        if ($res) {
            //插入定金日志
            $_model = new AppReceiptDepositLogModel(30);
            $receiptlogdata ['receipt_id'] = $id;
            $receiptlogdata ['receipt_action'] = '定金收据打印';
            $receiptlogdata ['add_time'] = date("Y-m-d H:i:s");
            $receiptlogdata ['add_user'] = $_SESSION['userName'];
            $_model->saveData($receiptlogdata, array());
        }
    }

    public function getListBySn($param) {
        $result = array('success' => 0, 'error' => '');
        $receipt_sn = _Request::getString('deposit_sn');
        if ($receipt_sn != '') {
            $model = new AppReceiptDepositModel(29);
            $print = $model->getRowList($receipt_sn);
            if($print['status'] == 2){
                $result['error'] = '该单据已使用';
                Util::jsonExit($result);
            }
            $result['success'] = 1;
            $result['ret'] = $print;
        }
        Util::jsonExit($result);
    }

    /**
     * 	add，渲染添加页面
     */
    public function add() {
        $result = array('success' => 0, 'error' => '');
		$paymentModel = new PaymentModel(1);
		$paymentList = $paymentModel->getEnabled();
        $SalesChannelsModel = new SalesChannelsModel(1);
        if($_SESSION['userType'] == 1){
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
        }else{
            $ids = explode(',', $_SESSION['qudao']);
            $channellist = $SalesChannelsModel->getSalesChannel($ids);
        }
        $result['content'] = $this->fetch('app_receipt_deposit_info.html', array(
            'view' => $paymentList,
            'channellist' => $channellist
        ));
        $result['title'] = '添加';
        Util::jsonExit($result);
    }
	
	/**
     * 	showLog，查看日志页面
     */
    public function showLog($params) {
        $id = intval($params["id"]);
        $result = array('success' => 0, 'error' => '');
		$model = new AppReceiptDepositLogModel(29);
		$where['receipt_id'] = $id;
		$page = _Request::getInt("page", 1);
		$data = $model->pageList($where,$page,10,0);
        $result['content'] = $this->fetch('app_receipt_deposit_show.html', array(
            'view' => $data
        ));
        $result['title'] = '查看日志';
        Util::jsonExit($result);
    }
	
	/**
     * 	cancel，作废
     */
    public function cancel($params) {
        $result = array('success' => 0, 'error' => '');
        $id = _Post::getInt('id');
        $zuofei_time = date("Y-m-d H:i:s");
 
        $model = new AppReceiptDepositModel($id, 30);
        if($model->getValue('status')==2){
        	$result['error'] = '点过款的不可以作废！';
        	Util::jsonExit($result);
        }
        $model->setValue('status',3);
        $model->setValue('zuofei_time', $zuofei_time);
        $res = $model->save(true);
        if ($res !== false) {
            $result['success'] = 1;
            //插入定金日志
            $_model = new AppReceiptDepositLogModel(30);
            $receiptlogdata ['receipt_id'] = $id;
            $receiptlogdata ['receipt_action'] = '定金收据作废';
            $receiptlogdata ['add_time'] = date("Y-m-d H:i:s");
            $receiptlogdata ['add_user'] = $_SESSION['userName'];
            $_model->saveData($receiptlogdata, array());
        } else {
            $result['error'] = '作废失败';
        }
        Util::jsonExit($result);
    }

    /**
     * 	edit，渲染修改页面
     */
    public function edit($params) {
        $id = intval($params["id"]);
        $tab_id = intval($params["tab_id"]);
        $result = array('success' => 0, 'error' => '');
        $result['content'] = $this->fetch('app_receipt_deposit_info.html', array(
            'view' => new AppReceiptDepositView(new AppReceiptDepositModel($id, 29)),
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
        $this->render('app_receipt_deposit_show.html', array(
            'view' => new AppReceiptDepositView(new AppReceiptDepositModel($id, 29)),
            'bar' => Auth::getViewBar()
        ));
    }

    /**
     * 	insert，信息入库
     */
    public function insert($params) {
        $result = array('success' => 0, 'error' => '');
        $_order_sn = _Post::getString('order_sn');
        $customer = _Post::getString('customer');
        $pay_fee = _Post::getFloat('pay_fee');
        $pay_time = _Post::getString('pay_time');
        $card_no = _Post::getString('card_no');
        $card_voucher = _Post::getString('card_voucher');
        $department = _Post::getInt('department');
        if(empty($pay_time)){
            $result['error'] = '支付时间不能为空';
            Util::jsonExit($result);  
        }
        if ($pay_time > (date("Y-m-d") . " 23:59:59")) {
            $result['error'] = '支付时间不能大于当前时间';
            Util::jsonExit($result);
        }
        $pay_type = _Post::getInt('pay_type');
        $pay_type_value = _Post::getString('pay_type_value');
        
        $action_note = _Post::getString('action_note');
        if($pay_fee <= 0){
            $result['error'] = '支付金额应该大于0，请重新输入';
            Util::jsonExit($result);
        }
        if ($pay_fee > 9999999.99) {
            $result['error'] = '支付金额最大9999999.99，请重新输入';
            Util::jsonExit($result);
        }
        
        if ($_order_sn) {
            $OrderModel = new ApiOrderModel();
            //验证订单是否存在 
            $order_info = $OrderModel->getOrderListBySn($_order_sn);
            if (empty($order_info) || $order_info == "NULL" || count($order_info) < 1) {
                $result['error'] = '此订单不存在，请重新输入';
                Util::jsonExit($result);
            }
             if($order_info['order_amount'] < $pay_fee){
                $result['error'] = '收取定金的金额不能大于订单总金额!';
                Util::jsonExit($result);
            }
            if ($order_info['order_status'] ==3) {
                 $result['error'] = '此订单已取消';
                 Util::jsonExit($result);
            }elseif ($order_info['order_status'] ==4) {
                  $result['error'] = '此订单已关闭!';
                 Util::jsonExit($result);
            }
            if ($order_info['apply_close'] == 1) {
                $result['error'] = '此订单已申请关闭!';
                Util::jsonExit($result);
            }
            if ($order_info['order_pay_status'] != 1) {
                $result['error'] = '此订单已付款!';
                Util::jsonExit($result);
            }
            
            if ($order_info['money_paid'] > 0) {
                $result['error'] = '此订单已经支付过，请重新输入';
                Util::jsonExit($result);
            }
            $department = $order_info['department_id'];
        }else{
            if($pay_type_value != '现金'){
                $result['error'] = '无订单号定金收据只支持现金支付方式';
                Util::jsonExit($result);
            }
            if ($department < 1) {
                $result['error'] = '销售渠道不能为空';
                Util::jsonExit($result);
            }
        }
        //取得渠道归属
        $channelModel = new SalesChannelsModel(1);
        $code = $channelModel->getChannelOwnCode($department,1);
        if(!$code){
            $result['error'] = '销售渠道的渠道归属为空，需要编辑销售渠道！';
            Util::jsonExit($result);
        }
        //生成定金收据编号
        $newmodel = new AppReceiptDepositModel(30);
	 	$deposit_sn = $newmodel->create_receipt($code);
        if(!$deposit_sn){
            $result['error'] = '定金收据号码生成失败';
            Util::jsonExit($result);
        }
        
        $olddo = array();
        $newdo = array();
        $newdo['order_sn'] = $_order_sn;
        $newdo['receipt_sn'] = $deposit_sn;
        $newdo['customer'] = $customer;
        $newdo['department'] = $department;
        $newdo['pay_fee'] = $pay_fee;
        $newdo['pay_type'] = $pay_type;
        $newdo['pay_time'] = $pay_time;
        $newdo['card_no'] = $card_no;
        $newdo['card_voucher'] = $card_voucher;
        $newdo['status'] = 1;
        $newdo['print_num'] = 0;
        $newdo['pay_user'] = $_SESSION['userName'];
        $newdo['remark'] = $action_note;
        $newdo['add_time'] = date("Y-m-d H:i:s");
        $newdo['add_user'] = $_SESSION['userName'];
        $res = $newmodel->saveData($newdo, $olddo);
        if ($res !== false) {
            
            $Bespokemodel = new AppOrderPayActionModel(30);
            //更新预约成交状态
            if($_order_sn&&$order_info['order_amount']>0&&!empty($order_info['bespoke_id'])){
                $Bespokemodel->updateBespokeDeal_Status($order_info['bespoke_id']);
            }

            $_model = new AppReceiptDepositLogModel(30);
            $_newdo = array();
            $_newdo['receipt_id'] = $res;
            $_newdo['receipt_action'] = '添加定金收据成功';
            $_newdo['add_time'] = date("Y-m-d H:i:s");
            $_newdo['add_user'] = $_SESSION['userName'];
            $_model->saveData($_newdo, $olddo);

            $orderModel = new ApiOrderModel();
            $logInfo = [
                'order_sn'=>$newdo['order_sn'],
                'order_status'=>'2',
                'shipping_status'=>'0',
                'pay_status'=>'2',
                'create_user'=>$_SESSION['realName'],
                'remark'=>'收取定金:'.$newdo['pay_fee'].'元'
            ];
            //写入订单日志
            $orderModel->mkOrderInfoLog($logInfo);

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
        echo '<pre>';
        print_r($_POST);
        echo '</pre>';
        exit;

        $newmodel = new AppReceiptDepositModel($id, 30);

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
        $model = new AppReceiptDepositModel($id, 30);
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
    /**
    *获取会员名字并根据订单号自动匹配
    */
    public function getMemberByOrderSn(){
        $order_sn = _Post::getString('order_sn');
         $OrderModel = new ApiOrderModel();
        $order_info = $OrderModel->getOrderListBySn($order_sn);
        //通过接口获取会员名字
        if (!empty($order_info['user_id'])) {
            $user_id = $order_info['user_id'];    
        }else{
            $user_id = "";
        }
        
        $newmodel = new AppReceiptDepositModel(30);
        $user_info = $newmodel->get_user_name($user_id);
        if($user_info['error']==1){
            Util::jsonExit(array('error'=>1));
        }
        //$userName = $user_info['data']['member_name'];
        Util::jsonExit($user_info['data']);
        //end
    }

    public function getTree(){
        $type = _Request::get('type');
        $model = new CompanyModel(1);
        $res = $model->getAllDCS();

        switch($type){
            case '1':{
                echo   $this->fetch('app_receipt_pay_deposit_option.html',array(
                    'list'=>$res[1],
                ));
            }
            case '2':{
                echo  $this->fetch('app_receipt_pay_deposit_option.html',array(
                    'list'=>$res[2],
                ));
            }
            case '3':{
                echo  $this->fetch('app_receipt_pay_deposit_option.html',array(
                    'list'=>$res[3],
                ));
            }
            default:{
                return false;
            }
        }

    }

}

?>