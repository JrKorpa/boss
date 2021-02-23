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
class AppOrderPayActionZfController extends CommonController {

    protected $smartyDebugEnabled = false;
    protected $whitelist = array('printorder','printorder_dz');

    /**
     * 	index，搜索框
     */
    public function index($params) {
        $SalesChannelsModel = new SalesChannelsModel(1);
        $paymentModel = new PaymentModel(1);
        //if($_SESSION['userType'] == 1){
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
            $paymentlist = $paymentModel->TgetList();
        //}else{
        //    $ids = explode(',', $_SESSION['qudao']);
        //    $channellist = $SalesChannelsModel->getSalesChannel($ids);
        //}
        $this->render('app_order_pay_action_search_form.html', array('bar' => Auth::getBar(),'sales_channels_idData' => $channellist,'paymentlist'=>$paymentlist));
    }

    /**
     * 	search，列表
     */
    public function search($params) {
        //if($_SESSION['userType']==1){
        //    $department = _Request::getInt('order_department')?_Request::getInt('order_department'):0;
        //}else{
        //    if(isset($_REQUEST['order_sn'])){
        //        $department = $_SESSION['qudao']?$_SESSION['qudao']:-1;
        //    }else{
        //        $department = _Request::getInt('order_department')?_Request::getInt('order_department'):($_SESSION['qudao']?current(explode(',', $_SESSION['qudao'])):-1);
        //    }
        //}
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'order_sn' => _Request::get('order_sn'),
            'pay_type' => _Request::get('pay_type'),
            'order_department' => _Request::getInt('order_department'),
            'start_time' => _Request::get('start_time'),
            'end_time' => _Request::get('end_time'),
            'start_time_p' => _Request::get('start_time_p'),
            'end_time_p' => _Request::get('end_time_p'),
            'out_order_sn' => _Request::get('out_order_sn'),
            'attach_sn' => _Request::get('attach_sn'),
        );

        //echo '<pre>';
        //print_r($args);die;
        $page = _Request::getInt("page", 1);
        $where = array();
        $where['order_sn'] = $args['order_sn'];
        $where['department'] = $args['order_department'];
        $where['pay_type'] = $args['pay_type'];
        $where['start_time'] = $args['start_time'];
        $where['end_time'] = $args['end_time'];
        $where['start_time_p'] = $args['start_time_p'];
        $where['end_time_p'] = $args['end_time_p'];
        $where['out_order_sn'] = $args['out_order_sn'];
        $where['attach_sn'] = $args['attach_sn'];
        $model = new AppOrderPayActionModel(29);
        $data = $model->getAllListA($where,$page,50,false);
        //统计
        $tongji =$data['T'];
        unset($data['T']);
        //渠道
        //支付方式
        $SalesChannelsModel = new SalesChannelsModel(1);
        $paymentModel = new PaymentModel(1);
        //if($_SESSION['userType'] == 1){
        $channellist = array_column($SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",''),'channel_name','id');
        $paymentlist = array_column($paymentModel->TgetList(),'pay_name','id');
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'app_order_pay_action_search_page_a';
        $this->render('app_order_pay_action_search_list.html', array(
            'channellist'=>$channellist,
            'paymentlist'=>$paymentlist,
            'pa' => Util::page($pageData),
            'page_list' => $data,
            'tongji'=>$tongji,
        ));
    }



}

?>