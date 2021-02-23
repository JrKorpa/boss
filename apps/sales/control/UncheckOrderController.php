<?php


class UncheckOrderController extends CommonController {
        protected $smartyDebugEnabled = false;

        public function index($params) {
            $this->getSourceList();
            $this->getCustomerSources();
            $SalesChannelsModel = new SalesChannelsModel(1);
            $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
            //获取所有数据
            $allSalesChannelsData = array();
            foreach ($getSalesChannelsInfo as $val) {
            $allSalesChannelsData[$val['id']] = $val['channel_name'];
            }
            $this->render('uncheckorder_search_form.html', array('bar' => Auth::getBar(), 'sales_channels_idData' => $allSalesChannelsData, ));
        }



    public function search($params) {
        $this->getSourceList();
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'order_sn' => _Request::get("order_sn"),
            'out_order_sn'=>_Request::getString('out_order_sn'),
            'create_user' => _Request::getString("create_user"),
            'order_check_status' => _Request::get("order_check_status"),
            'order_pay_status' => _Request::get("order_pay_status"),
            //'order_department' => _Request::get("order_department"),
            'department_id' => _Request::get("department_id"),
            'buchan_status' => _Request::get("buchan_status"),
            'delivery_status' => _Request::get("delivery_status"),
            'send_good_status' => _Request::get("send_good_status"),
            'customer_source' => _Request::get("customer_source"),
            'consignee' => _Request::get("consignee"),
            'mobile' => _Request::get("mobile"),
            'is_delete' => (!isset($_REQUEST['is_delete']) or isset($_REQUEST['is_delete']) && $_REQUEST['is_delete'] == '') ? 2000 : _Request::getInt("is_delete"),
        );

        $page = _Request::getInt("page", 1);
        $where = array(
            'order_sn' => $args['order_sn'],
            'create_user' => $args['create_user'],
            'order_status' =>5,
            'order_check_status' => $args['order_check_status'],
            'order_pay_status' => $args['order_pay_status'],
           // 'order_department' => $args['order_department'],
            'department_id' => $args['department_id'],
            'send_good_status' => $args['send_good_status'],
            'delivery_status' => $args['delivery_status'],
            'buchan_status' => $args['buchan_status'],
            'customer_source' => $args['customer_source'],
            'consignee' => $args['consignee'],
            'mobile' => $args['mobile'],
            'is_delete' => $args['is_delete']
        );
		//支付方式
        $paymentModel = new PaymentModel(1);
		$paymentList = $paymentModel->getList();
		$pay_type[0]="展厅订购";
		foreach($paymentList as $payment){
			$pay_type[$payment['id']]=$payment['pay_name'];
		}

        $model = new BaseOrderInfoModel(27);
        if(!empty($args['out_order_sn'])){
            $order_sn=$model->getOrdersnByOutsn($args['out_order_sn']);
            if(empty($order_sn)){
                //外部订单号问题
                $where['order_sn_out']=1;
            }else{
                $where['order_sn_out']=$order_sn;
            }
        }else{
            $where['order_sn_out']='';
        }
        $data = $model->pageList($where, $page, 10, false);

        $user_name = array();
        if ($data['data']['data']) {
            $customer_source_model = new CustomerSourcesModel(1);
            $_value = '';
            $departmentModel = new DepartmentModel(1);
            foreach ($data['data']['data'] as $k => $v) {
                $user_name[$k] = $model->getMember_Info_userId($v['user_id']);
                if (isset($v['user_id']) && !empty($v['user_id'])) {
                    if ($user_name[$k]['data'] != '未查询到此会员') {
                        $data['data']['data'][$k]['user_id'] = $user_name[$k]['data']['member_name'];
                    } else {
                        $data['data']['data'][$k]['user_id'] = '';
                    }
                } else {
                    $data['data']['data'][$k]['user_id'] = '';
                }

                $customer_source_name = $customer_source_model->getCustomerSourcesList("`source_name`", array('id' => $v['customer_source_id']));


                if (count($customer_source_name) > 0) {
                    $data['data']['data'][$k]['customer_source_name'] = $customer_source_name[0]['source_name'];
                } else {
                    $data['data']['data'][$k]['customer_source_name'] = $_value;
                }
                $data['data']['data'][$k]['department_name'] = $departmentModel->getNameById($v['department_id']);
            }
        }

        //获取全部的有效的销售渠道
        $SalesChannelsModel = new SalesChannelsModel(1);
        $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
        //获取所有数据
        $allSalesChannelsData = array();
        foreach ($getSalesChannelsInfo as $val) {
            $allSalesChannelsData[$val['id']] = $val['channel_name'];
        }

        $pageData = $data['data'];
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'unckeckorder_search_page';
        $this->render('uncheckorder_search_list.html', array(
            'pa' => Util::page($pageData),
            'allSalesChannelsData' => $allSalesChannelsData,
            'page_list' => $data['data'],
            'all_price' => $data['all_price'],
			'pay_type' => $pay_type
        ));
    }

    public function getSourceList() {
        //渠道
        $SalesChannelsModel = new SalesChannelsModel(1);
        $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
        //获取所有数据
        $allSalesChannelsData = array();
        foreach ($getSalesChannelsInfo as $val) {
            $allSalesChannelsData[$val['id']] = $val['channel_name'];
        }
        $this->assign('sales_channels_idData', $allSalesChannelsData);
    }

    public function getCustomerSources() {
        //客户来源
        $CustomerSourcesModel = new CustomerSourcesModel(1);
        $CustomerSourcesList = $CustomerSourcesModel->getCustomerSourcesList("`id`,`source_name`");
        $this->assign('customer_source_list', $CustomerSourcesList);
    }

    //驳回
    public function reject(){
        $result = array('success' => 0,'error' => '');
        $order_id= _Request::getInt('id');
        $OderModel = new BaseOrderInfoModel($order_id,28);
        $do = $OderModel->getDataObject();
        if($do['order_status']!=5){
            $result['error'] = "只有审核未通过的订单才能驳回";
        }else{
            $OderModel->setValue('order_status',3);
            $res = $OderModel->save(true);
            if($res!==false){
                $result['success'] = 1;
            }else{
                $result['error'] = "驳回失败";
            }
        }
        Util::jsonExit($result);
    }


    public function check() {
        $id = _Post::get('id');
        $result = array('success' => 0, 'error' => '');
        $model = new BaseOrderInfoModel($id, 28);
        $status = $model->getValue('order_status');
        $pay_status = $model->getValue('order_pay_status');
        $order_sn = $model->getValue('order_sn');
        //只有待审核状态才能审核
        if ($status != 1) {
            $result['error'] = '此订单已经审核，不能审核';
            Util::jsonExit($result);
        }
        //判断地址表中 是否存在收获地址
        $ret = $model->getAddressByid($id);
        if (count($ret) < 1) {
            $result['error'] = "没有设置收获地址 不可以审核通过！";
            Util::jsonExit($result);
        }
        //获取订单商品信息
        $orderDetailModel = new AppOrderDetailsModel(27);
        $goods_info = $orderDetailModel->getGoodsByOrderId(array('order_id' => $id));
        if (empty($goods_info)) {
            $result['error'] = '此订单还没有添加商品,请添加！';
            Util::jsonExit($result);
        }

        //审核后的状态为2
        $order_status = 2;
        $model->setValue('check_user', $_SESSION['userName']);
        $model->setValue('check_time', date('Y-m-d H:i:s'));
        $model->setValue('order_status', $order_status);
        if ($model->save()) {
            //操作日志
            $ation['order_id'] = $id;
            $ation['order_status'] = $order_status;
            $ation['shipping_status'] = 1;
            $ation['pay_status'] = $pay_status;
            $ation['create_user'] = $_SESSION['userName'];
            $ation['create_time'] = date("Y-m-d H:i:s");
            $ation['remark'] = "审核订单";
            $model->addOrderAction($ation, $id);
            //往布产单中推送数据
            $result['success'] = 1;
            Util::jsonExit($result);
        } else {
            $result['error'] = '修改失败';
            Util::jsonExit($result);
        }
    }

    //通过将待审核的订单改为有效状态
    public function passOrder(){
        $result = array('success' => 0,'error' => '');
        $order_id= _Request::getInt('id');
        $OderModel = new BaseOrderInfoModel($order_id,28);
        $do = $OderModel->getDataObject();
        if($do['order_status']!=5){
            $result['error'] = "只有审核未通过的订单才能通过";
        }else{
            $OderModel->setValue('order_status',1);
            $res = $OderModel->save(true);
            if($res!==false){
                $result['success'] = 1;
            }else{
                $result['error'] = "通过失败";
            }
        }
        Util::jsonExit($result);
    }
}
