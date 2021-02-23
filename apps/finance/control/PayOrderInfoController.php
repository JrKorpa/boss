<?php

/**
 *  -------------------------------------------------
 *   @file		: PayOrderInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-26 15:33:31
 *   @update	:
 *  -------------------------------------------------
 */
class PayOrderInfoController extends CommonController {

    protected $smartyDebugEnabled = false;
    protected $whitelist = array('download');

    /**
     * 	index，搜索框
     */
    public function index($params) {
        $model = new CustomerSourcesModel(1);
        $source_list = $model->getSourcesPay();
        $paymentModel = new PaymentModel(1);
        $paymentList = $paymentModel->getList();
        $dempartModel = new SalesChannelsModel(1);
        $dempartList = $dempartModel->getSalesChannel(array('1','3','10','71'));
        $this->render('pay_order_info_search_form.html', array(
			'departlist'=>$dempartList,
			'payment'=>$paymentList,
			'source_list'=>$source_list,
			'view' => new PayOrderInfoView(new PayOrderInfoModel(29)),
			'bar' => Auth::getBar()));
    }

    /**
     * 	search，列表
     */
    public function search($params) {
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'storage_mode' => _Request::getList('storage_mode'),
            'kela_sn' => _Request::getString('kela_sn'),
            'pay_name' => _Request::getInt('pay_name'),
            'status' => _Request::getInt('status'),
            'department' => _Request::getInt('department'),
            'from_ad' => _Request::getString('from_ad'),
            'external_id' => _Request::getString('external_id'),
            'apply_number' => _Request::getString('apply_number'),
            'order_time_start' => _Request::getString('order_time_start'),
            'order_time_end' => _Request::getString('order_time_end'),
            'shipping_time_start' => _Request::getString('shipping_time_start'),
            'shipping_time_end' => _Request::getString('shipping_time_end'),
                //'参数' = _Request::get("参数");
        );
        $page = _Request::getInt("page", 1);
        $where = array();
        $where['storage_mode'] = $args['storage_mode'];
        $where['kela_sn'] = $args['kela_sn'];
        $where['pay_name'] = $args['pay_name'];
        $where['status'] = $args['status'];
        $where['department'] = $args['department'];
        $where['from_ad'] = $args['from_ad'];
        $where['external_id'] = $args['external_id'];
        $where['apply_number'] = $args['apply_number'];
        $where['order_time_start'] = $args['order_time_start'];
        $where['order_time_end'] = $args['order_time_end'];
        $where['shipping_time_start'] = $args['shipping_time_start'];
        $where['shipping_time_end'] = $args['shipping_time_end'];

        $model = new PayOrderInfoModel(29);
        $data = $model->pageList($where, $page, 10, false);
		$sourceModel = new CustomerSourcesModel(1);
		$SalesChannelsModel = new SalesChannelsModel(1);

        if ($data['data']) {
            foreach ($data['data'] as &$val) {
                $val['ad_name'] = $sourceModel->getSourceNameById($val['from_ad']);
				$val['dep_name'] = $SalesChannelsModel->getNameByid($val['department']);
            }
            unset($val);
        }
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'pay_order_info_search_page';
        $this->render('pay_order_info_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data
        ));
    }

    public function download() {

        $where = array(
            'storage_mode' => _Request::getString('storage_mode'),
            'kela_sn' => _Request::getString('kela_sn'),
            'pay_name' => _Request::getInt('pay_name'),
            'status' => _Request::getInt('status'),
            'department' => _Request::getInt('department'),
            'from_ad' => _Request::getString('from_ad'),
            'external_id' => _Request::getString('external_id'),
            'apply_number' => _Request::getString('apply_number'),
            'order_time_start' => _Request::getString('order_time_start'),
            'order_time_end' => _Request::getString('order_time_end'),
            'shipping_time_start' => _Request::getString('shipping_time_start'),
            'shipping_time_end' => _Request::getString('shipping_time_end')
        );
        $model = new PayOrderInfoModel(29);
        $data = $model->getInfoList($where);
        if (count($data)>0) {
            foreach ($data as &$val) {
                $val['status'] = $model->getStatusList($val['status']);
            }
            unset($val);
        }
        $title = array(
            'BDD制单号',
            '制单人',
            '外部订单号',
            '下单时间',
            '发货时间',
            'BDD金额',
            '销账金额',
            '来源部门',
            '订单来源',
            '支付方式',
            '申请状态',
            '应收申请单',
            '外部金额',
            '制单误差',
            '销账误差',
            '财务误差');
        if (is_array($data) && count($data)>0) {
            foreach ($data as $k => $v) {
                $val = array(
                    $v['kela_sn'] . "\t ",
                    $v['make_order'],
                    $v['external_sn'] . "\t ",
                    $v['order_time'],
                    $v['shipping_time'],
                    $v['kela_total_all'], //BDD金额
                    $v['jxc_total_all'], //销账金额
                    $v['dep_name'],
                    $v['ad_name'],
                    $v['pay_name'],
                    $v['status'],
                    $v['apply_number'],
                    $v['external_total_all'],
                    $v['kela_total_all'] - $v['external_total_all'], //制单误差：BDD金额 减去 外部金额
                    $v['jxc_total_all'] - $v['kela_total_all'], //销账误差：销账金额 减去 BDD金额
                    $v['jxc_total_all'] - $v['external_total_all']  //账务误差：销账金额 减去 外部金额
                );
                $val = eval('return ' . iconv('utf-8', 'gbk', var_export($val, true) . ';'));
                $content[] = $val;
            }
        }
        $ymd = date("Ymd_His", time() + 8 * 60 * 60);
        header("Content-Disposition: attachment;filename=" . iconv('utf-8', 'gbk', '销售明细列表') . $ymd . ".csv");
        $fp = fopen('php://output', 'w');
        $title = eval('return ' . iconv('utf-8', 'gbk', var_export($title, true) . ';'));
        fputcsv($fp, $title);
        if(count($data)>0){
            foreach ($content as $k => $v) {
                fputcsv($fp, $v);
            }
        }else{
            $remark = iconv('utf-8', 'gbk', '没有数据！');
            fputcsv($fp, array($remark));
        }
        fclose($fp);
        exit;
    }


}

?>