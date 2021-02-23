<?php
/**
 *  -------------------------------------------------
 *   @file		: NetSaleReportController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: gengchao
 *   @date		: 2015-11-18 10:15:23
 *  -------------------------------------------------
 */
class NetSaleReportController extends CommonController
{
    protected $smartyDebugEnabled = false;
    protected $whitelist = array("downloads");

    /**
     *	index，搜索框
     */
    public function index($params) {
        //获取体验店的信息
        $data = $this->getMyDepartments();
        $this->render('netsale_report_form.html',
            array(
                'bar' => Auth::getBar(),
                'allshop'=>$data
            )
        );
    }

    // 各店成交率统计表
    public function search() {
        $sourcedata = $this->_search_data();
        $this->render('netsale_report_list.html',
            array(
                'data'=>$sourcedata,
                'rows'=>count($sourcedata)
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
            'endtime'   => _Request::get("endtime", date('y-m-d'))
        );

        if(empty($args['department_id'])) {
            die('请选择体验店!');
        }
        if(empty($args['begintime']) || empty($args['endtime'])) {
            die('请选择时间');
        }
        if($args['begintime'] > $args['endtime']) {
            die('结束时间必须大于开始时间');
        }
        $days = $this->getDatePeriod($args['begintime'],$args['endtime']);
        if($days>100) {
            die('请查询100天范围内的信息!');
        }
        // 店长、网销、其他 的不同访问权限
        $is_admin = $_SESSION['userType']==1;
        $account = $_SESSION['userName'];
        $shop_id = $args['department_id'];
        $saleChannelModel = new SalesChannelsModel(1);
        $shopPerson = $saleChannelModel->getShopPersonById($shop_id);
        // 店长权限查看所有 网销用户
        $shop_leaders = array_unique(array_filter(explode(',', $shopPerson['dp_leader_name'])));
        $netsalers = array_unique(array_filter(explode(',', $shopPerson['dp_is_netsale'])));
        if ($is_admin || ($account && in_array($account, $shop_leaders))) {
            $args['netsale_names'] = $netsalers;
        } elseif ($account && in_array($account, $netsalers)) {
            $args['netsale_names'] = array($account);
        } else {
            die('您不是当前店铺的网销人员，不能查看！');
        }

        //开始拿数据
        $Model = new TydReportModel(59);
        $where = array();
        $where['begintime'] = $args['begintime'];
        $where['endtime'] = $args['endtime'];
        $where['department_id'] = $args['department_id'];
        $where['make_order'] = $is_admin ? null : $args['netsale_names']; // 网销人员
        $where['is_zp'] = 0; // 非赠品

        //传值
        $where_sql = '';
        foreach($where as $k => $v){
            $where_sql .= "&".$k.'='.$v;
        }
        $this->assign('args_where',$where_sql);

        //总公司网销------------------boss-1212
        if($args['department_id'] == 163){
            $where['department_id'] = '';
            $where['make_order'] = $args['netsale_names'];
        }
        //----------------------------end*/
        
        // 1.新增 裸钻、成品统计
        $new_data = $Model->getNetSaleStat($where);
        // 2.退货 裸钻、成品统计
        $return_goods_data = $Model->getNetSaleReturnGoodsStat($where);
        $return_data = $Model->getNetSaleReturnStat($where);
        $return_data = array_column($return_data, 'return_amount', 'make_order');
        // 3.发货 裸钻、成品统计
        $ship_data = $Model->getNetSaleShipStat($where);

        // 组装数据
        $sourcedata = array();
        foreach ($args['netsale_names'] as $username) {
            $item = array();
            $item['username'] = $username;
            // 1.新增统计
            $item['newlz_count'] = $item['newlz_amount'] = $item['newcp_count'] = $item['newcp_amount'] = 0;
            foreach ($new_data as $stat) {
                if (trim($stat['make_order'])==trim($username)) {
                    $item['newlz_count'] = $stat['lz_count'];
                    $item['newlz_amount'] = $stat['lz_amount'];
                    $item['newcp_count'] = $stat['cp_count'];
                    $item['newcp_amount'] = $stat['cp_amount'];
                }
            }
            // 2.退货统计
            $item['returnlz_count'] = $item['returncp_count'] = $item['returnlz_amount'] = $item['returncp_amount'] = 0;
            foreach ($return_goods_data as $stat) {
                if (trim($stat['make_order'])==trim($username)) {
                    $item['returnlz_count'] = $stat['lz_return_goods_count'];
                    $item['returncp_count'] = $stat['cp_return_goods_count'];
                    $item['returnlz_amount'] = $stat['lz_return_goods_amount'];
                    $item['returncp_amount'] = $stat['cp_return_goods_amount'];
                }
            }
            // 3.退款不退货
            $item['loss_goods_amount'] = isset($return_data[$username]) ? $return_data[$username] : 0;
            // 4.发货统计
            $item['shiplz_count'] = $item['shiplz_amount'] = $item['shipcp_count'] = $item['shipcp_amount'] = 0;
            foreach ($ship_data as $stat) {
                if (trim($stat['make_order'])==trim($username)) {
                    $item['shiplz_count'] = $stat['lz_count'];
                    $item['shiplz_amount'] = $stat['lz_amount'];
                    $item['shipcp_count'] = $stat['cp_count'];
                    $item['shipcp_amount'] = $stat['cp_amount'];
                }
            }
            $item['total_gain'] = $item['newlz_amount'] + $item['newcp_amount'] - $item['returnlz_amount'] - $item['returncp_amount'] - $item['loss_goods_amount'];
            $item['total_ship'] = $item['shiplz_amount'] + $item['shipcp_amount'];
            $sourcedata[] = $item;
        }
        return $sourcedata;
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
        header("Content-Disposition:filename=".iconv('utf-8','gb2312',"网销销售报表").".xls");

        if ($data) {
            $csv_body='<table border="1"><tr>
                <th rowspan="2" style="vertical-align: middle;">网销名</th>
                <th colspan="4" style="text-align: center;">新增</th>
                <th colspan="4" style="text-align: center;">退货商品金额</th>
                <th rowspan="2" style="vertical-align: middle;">退款不退货金额</th>
                <th rowspan="2" style="vertical-align: middle;">总业绩</th>
                <th colspan="4" style="text-align: center;">回款</th>
                <th rowspan="2" style="vertical-align: middle;">回款总金额</th>
            </tr>
            <tr>
                <th>裸钻数</th>
                <th>裸钻金额</th>
                <th>成品数</th>
                <th>成品金额</th>
                <th>裸钻数</th>
                <th>裸钻金额</th>
                <th>成品数</th>
                <th>成品金额</th>
                <th>裸钻数</th>
                <th>裸钻金额</th>
                <th>成品数</th>
                <th>成品金额</th>
            </tr>';
            foreach ($data as $key => $val ) {
                $csv_body.="<tr><td>".$val['username']."</td>";
                $csv_body.="<td>".$val['newlz_count']."</td><td>".$val['newlz_amount']."</td>";
                $csv_body.="<td>".$val['newcp_count']."</td><td>".$val['newcp_amount']."</td>";
                $csv_body.="<td>".$val['returnlz_count']."</td><td>".$val['returnlz_amount']."</td>";
                $csv_body.="<td>".$val['returncp_count']."</td><td>".$val['returncp_amount']."</td>";
                $csv_body.="<td>".$val['loss_goods_amount']."</td><td>".$val['total_gain']."</td>";
                $csv_body.="<td>".$val['shiplz_count']."</td><td>".$val['shiplz_amount']."</td>";
                $csv_body.="<td>".$val['shipcp_count']."</td><td>".$val['shipcp_amount']."</td>";
                $csv_body.="<td>".$val['total_ship']."</td></tr>";
            }
            $csv_body.="</table>";
            echo $csv_body;
        } else {
            echo '没有数据！';
        }
        exit;
    }
    /**
     *	回款总金额
     */
    public function total_ship(){
        //var_dump($_REQUEST);
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'department_id' => _Request::get("department_id"),
            'name' => _Request::get("name"),
            'begintime' => _Request::get("begintime"),
            'endtime'   => _Request::get("endtime", date('y-m-d'))
        );
        
        if(empty($args['department_id'])) {
            die('请选择体验店!');
        }
        if(empty($args['begintime']) || empty($args['endtime'])) {
            die('请选择时间');
        }
        if($args['begintime'] > $args['endtime']) {
            die('结束时间必须大于开始时间');
        }
        $days=$this->getDatePeriod($args['begintime'],$args['endtime']);
        if($days>100) {
            die('请查询100天范围内的信息!');
        }
        // 店长、网销、其他 的不同访问权限
        //开始拿数据
        $Model = new TydReportModel(59);
        $where = array();
        $where['begintime'] = $args['begintime'];
        $where['endtime'] = $args['endtime'];
        $where['department_id'] = $args['department_id'];
        //总公司网销-------------boss1212
        if($args['department_id'] == 163){
            $where['department_id'] = '';
        }
        //-----------------------end
        $where['make_order'] = $args['name']; // 网销人员
        $where['is_zp'] = 0; // 非赠品
        // 3.发货 裸钻、成品统计
        $ship_data = $Model->getNetSaleShipStatDetail($where);
        $this->render('netsale_report_total_ship_list.html',
            array(
                'data'=>$ship_data,
                'rows'=>count($ship_data)
            )
        );
    }
}?>