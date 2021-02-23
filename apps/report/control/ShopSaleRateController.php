<?php
/**
 *  -------------------------------------------------
 * @file: ShopSaleRateController.php
 * @desc: 各店某时间内成品占比和转化率报表
 * @link:  www.kela.cn
 * @author	: gengchao
 * @date: 2016-01-04 14:15:23
 *  -------------------------------------------------
 */
class ShopSaleRateController extends CommonController
{
    protected $smartyDebugEnabled = false;
    protected $whitelist = array("downloads");

    /**
     *	index，搜索框
     */
    public function index($params) {
        //获取体验店的信息
        $this->render('shopsalerate_report_form.html',
            array(
                'bar' => Auth::getBar(),
            )
        );
    }

    // 各店成交率统计表
    public function search() {
        $data = $this->_search_data();
        $this->render('shopsalerate_report_list.html', $data);
    }
    private function _search_data() {
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'begintime' => _Request::get("begintime"),
            'endtime'   => _Request::get("endtime", date('y-m'))
        );

        if(empty($args['begintime']) || empty($args['endtime'])) {
            die('请选择时间');
        }
        if($args['begintime'] > $args['endtime']) {
            die('结束时间必须大于开始时间');
        }
        $begindate = explode('-', $args['begintime']);
        $enddate = explode('-', $args['endtime']);
        $begin_year = $begindate[0];
        $begin_month = $begindate[1];
        $end_year = $enddate[0];
        $end_month = $enddate[1];

        $model = new TydReportModel(59);
        $monthesData = array();
        while(intval($begin_year.$begin_month) <= intval($end_year.$end_month)) {
            if ($begin_month==12) {
                $end_year_month = ($begin_year+1).'-01';
            } else {
                $endmonth = $begin_month+1;
                $endmonth = intval($endmonth)<10 ? '0'.intval($endmonth) : $endmonth;
                $end_year_month = $begin_year.'-'.$endmonth;
            }
            $where = array(
                'begintime' => $begin_year.'-'.$begin_month.'-01 00:00:00',
                'endtime' => $end_year_month.'-01 00:00:00'
            );
            $month = $begin_year.'年'.$begin_month.'月';

            $data = $model->getShopsCpSaleRate($where);
            $monthesData[$month]['cp_amounts'] = array_column($data, 'cp_amount', 'department_id');
            $monthesData[$month]['total_amounts'] = array_column($data, 'total_amount', 'department_id');

            $data = $model->getShopsInshopRate($where);
            $monthesData[$month]['deal_counts'] = array_column($data, 'deal_count', 'department_id');
            $monthesData[$month]['re_counts'] = array_column($data, 're_count', 'department_id');


            if ($begin_month==12) {
                $begin_year++;
                $begin_month = '01';
            } else {
                $begin_month = intval($begin_month) + 1;
                $begin_month = intval($begin_month)<10 ? '0'.intval($begin_month) : $begin_month;
            }
        }

        $model = new ShopCfgChannelModel(1);
        $shops = $model->getallshop(array('shop_type' => 1));
        return array('shops'=>$shops, 'monthesData'=>$monthesData);
    }

    /**
     * 下载
     */
    public function downloads() {
        $data = $this->_search_data();

        ini_set('memory_limit','-1');
        set_time_limit(0);
        header("Content-Type: text/html; charset=gb2312");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=".iconv('utf-8','gb2312',"各店成品成交占比和预约成交转化率").".xls");

        if ($data) {
            $shops = $data['shops'];
            $monthesData = $data['monthesData'];
            $month_tds1 = '';
            $month_tds2 = '';
            foreach($monthesData as $month=>$data) {
                $month_tds1 .= "<th colspan='2'>{$month}</th>";
                $month_tds2 .= "<th>成品指标%</th><th>转化率指标%</th>";
            }
            $csv_body = '<table border="1">';
            $csv_body .= '<tr><th></th><th></th>'.$month_tds1.'</tr>';
            $csv_body .= '<tr><th>部门</th><th>开业日期</th>'.$month_tds2.'</tr>';
            foreach ($shops as $key => $val ) {
                $shop_id = $val['id'];
                $csv_body.="<tr><td>".$val['shop_name']."</td><td>".$val['start_shop_time']."</td>";
                foreach ($monthesData as $data) {
                    $cp_sale_rate = $deal_bes_rate = 0;
                    if (isset($data['total_amounts'][$shop_id]) && $data['total_amounts'][$shop_id]>0) {
                        $cp_sale_rate = round(100*$data['cp_amounts'][$shop_id]/$data['total_amounts'][$shop_id], 2);
                    }
                    if (isset($data['re_counts'][$shop_id]) && $data['re_counts'][$shop_id]>0) {
                        $deal_bes_rate = round(100*$data['deal_counts'][$shop_id]/$data['re_counts'][$shop_id], 2);
                    }
                    $csv_body.="<td>{$cp_sale_rate}</td><td>{$deal_bes_rate}</td>";
                }
            }
            $csv_body.="</table>";
            echo $csv_body;
        } else {
            echo '没有数据！';
        }
        exit;
    }
}
?>