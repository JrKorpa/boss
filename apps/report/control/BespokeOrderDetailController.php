<?php
/**
 * -------------------------------------------------
 * @desc : 预约成交明细报表
 * @file : BespokeOrderDetailController.php
 * @link :  www.kela.cn
 * @author	: gengchao
 * @date	 2016-01-06 17:15:23
 * -------------------------------------------------
 */
class BespokeOrderDetailController extends CommonController
{
    protected $smartyDebugEnabled = false;
    protected $whitelist = array("downloads");

    /**
     * index，搜索框
     */
    public function index($params) {
        //获取体验店的信息
        $data = $this->getMyDepartments();
        $this->render('bespoke_order_detail_form.html',
            array(
                'bar' => Auth::getBar(),
                'allshop' => $data
            )
        );
    }

    // 各店成交率统计表
    public function search() {
        $data = $this->_search_data();
        $this->render('bespoke_order_detail_list.html',
            array(
                'data' => $data,
                'rows' => count($data)
            )
        );
    }

    private function _search_data() {
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'department_id' => _Request::get("shop_id"),
            'begintime' => _Request::get("begintime"),
            'endtime' => _Request::get("endtime", date('y-m-d'))
        );

        if (empty($args['begintime']) || empty($args['endtime'])) {
            die('请选择时间');
        }
        if ($args['begintime'] > $args['endtime']) {
            die('结束时间必须大于开始时间');
        }
        $days = $this->getDatePeriod($args['begintime'], $args['endtime']);
        if ($days > 100) {
            die('请查询100天范围内的信息!');
        }

        //开始拿数据
        $tydModel = new TydReportModel(59);
        $list = $tydModel->getBespokeOrderDetails($args);
        $payModel = new PaymentModel(2);
        $paylist = $payModel->getList();
        $hashlist = array_column($paylist, 'pay_name', 'id');
        foreach ($list as &$item) {
            $item['order_status'] = $this->dd->getEnum('order.order_status', $item['order_status']);
            $item['order_pay_status'] = $this->dd->getEnum('order.order_pay_status', $item['order_pay_status']);
            $item['buchan_status'] = $this->dd->getEnum('order.buchan_status', $item['buchan_status']);
            $item['send_good_status'] = $this->dd->getEnum('order.send_good_status', $item['send_good_status']);
            $item['order_pay_type'] = isset($hashlist[$item['order_pay_type']]) ? $hashlist[$item['order_pay_type']] : '';

            $item['is_xianhuo'] = $item['is_xianhuo']==1 ? '现货单' : '期货单';
            $item['is_stock_goods'] = $item['is_stock_goods']==1 ? '现货' : '期货';
        }
        return $list;
    }

    /**
     * 下载
     */
    public function downloads() {
        $data = $this->_search_data();

        ini_set('memory_limit', '-1');
        set_time_limit(0);
        header("Content-Type: text/html; charset=gb2312");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=" . iconv('utf-8', 'gb2312', "预约成交明细报表") . ".xls");

        if ($data) {
            $csv_body = '<table border="1"><tr>
                <td style="text-align: center;">销售渠道</td>
                <td style="text-align: center;">预约来源</td>
                <td style="text-align: center;">预约号</td>
                <td style="text-align: center;">预约客服</td>
                <td style="text-align: center;">BDD订单号</td>
                <td style="text-align: center;">订单类型</td>
                <td style="text-align: center;">订单状态</td>
                <td style="text-align: center;">支付状态</td>
                <td style="text-align: center;">支付类型</td>
                <td style="text-align: center;">实付金额</td>
                <td style="text-align: center;">订单总金额</td>
                <td style="text-align: center;">发货状态</td>
                <td style="text-align: center;">布产状态</td>
                <td style="text-align: center;">制单时间</td>
                <td style="text-align: center;">制单人</td>
                <td style="text-align: center;">订单备注</td>
                <td style="text-align: center;">货号</td>
                <td style="text-align: center;">款号</td>
                <td style="text-align: center;">商品名称</td>
                <td style="text-align: center;">现货/期货</td>
            </tr>';
            foreach ($data as $key => $val) {
                $csv_body .= "<tr>";
                $csv_body .= "<td>" . $val['channel_name'] . "</td><td>" . $val['source_name'] . "</td>";
                $csv_body .= "<td>" . $val['bespoke_sn'] . "</td><td>" . $val['make_order'] . "</td>";
                $csv_body .= "<td>" . $val['order_sn'] . "</td>";
                $csv_body .= "<td>" . $val['is_xianhuo'] . "</td><td>" . $val['order_status'] . "</td>";
                $csv_body .= "<td>" . $val['order_pay_status'] . "</td><td>" . $val['order_pay_type'] . "</td>";
                $csv_body .= "<td>" . $val['money_paid'] . "</td><td>" . $val['order_amount'] . "</td>";
                $csv_body .= "<td>" . $val['send_good_status'] . "</td><td>" . $val['buchan_status'] . "</td>";
                $csv_body .= "<td>" . $val['create_time'] . "</td><td>" . $val['create_user'] . "</td>";
                $csv_body .= "<td>" . $val['order_remark'] . "</td><td>" . $val['goods_id'] . "</td>";
                $csv_body .= "<td>" . $val['goods_sn'] . "</td><td>" . $val['goods_name'] . "</td>";
                $csv_body .= "<td>" . $val['is_stock_goods']."</td></tr>";
            }
            $csv_body .= "</table>";
            echo $csv_body;
        } else {
            echo '没有数据！';
        }
        exit;
    }

}
?>