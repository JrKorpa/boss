<?php
/**
 *  -------------------------------------------------
 *   @file		: NetDealRateReportController.php
 *   @desc     : 网销订单接待成单率
 *   @author	: gengchao
 *   @date		: 2015-12-28 16:15:23
 *  -------------------------------------------------
 */
class NetDealRateReportController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist = array("downloads");

	/**
	 *	index，搜索框
	 */
    public function index($params) {
        //获取体验店的信息
        $data = $this->getMyDepartments();
        $this->render('netdealrate_report_form.html',
            array(
                'bar' => Auth::getBar(),
                'allshop'=>$data
            )
        );
    }

    // 各店成交率统计表
    public function search() {
        $sourcedata = $this->_search_data();
        $this->render('netdealrate_report_list.html',
            array(
                'data'=>$sourcedata,
                'rows'=>count($sourcedata)
            )
        );
    }
    private function _search_data() {
        $args = array(
            'department_id' => _Request::get("shop_id"),
            'begintime' => _Request::get("begintime"),
            'endtime'   => _Request::get("endtime", date('y-m-d'))
        );
        $this->assign('args_where', "department_id={$args['department_id']}&begintime={$args['begintime']}&endtime={$args['endtime']}");

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
        $is_admin = $_SESSION['userType']==1;
        $account = $_SESSION['userName'];
        $shop_id = $args['department_id'];
        $saleChannelModel = new SalesChannelsModel(1);
        $shopPerson = $saleChannelModel->getShopPersonById($shop_id);
        // 店长权限查看所有 网销用户
        $shop_leaders = array_unique(array_filter(explode(',', $shopPerson['dp_leader_name'])));
        $netsalers = array_unique(array_filter(explode(',', $shopPerson['dp_people_name'])));
        if ($is_admin || ($account && in_array($account, $shop_leaders))) {
            $people_names = array_merge($shop_leaders, $netsalers);
        } elseif ($account && in_array($account, $netsalers)) {
            $people_names = array($account);
        } else {
            die('您不是当前店铺的网销人员，不能查看！');
        }

        $tydmodel = new TydReportModel(59);

        //163总公司网销----------
        $is_zgsWx = false;//是否总公司网销
        if($args['department_id'] == 163 && !empty($people_names)){
            $is_zgsWx = true;
            $args['department_id'] = '';//总公司网销可以查看所有渠道自己添加的信息
        }
        //-----------------------end

        $where = array();
        $where['real_inshop_time_start'] = $args['begintime'];
        $where['real_inshop_time_end'] = $args['endtime'];
        $where['department_id'] = $args['department_id'];

        //163总公司网销----------
        if($is_zgsWx){
            $where['make_order'] = implode("','", $people_names);
        }
        //-----------------------end

        $where['bespoke_status'] = 2;  //已经审核的
        $where['re_status'] = 1;  //已到店
        if($is_zgsWx){
            $data = $tydmodel->statNetInshopBesCount($where, 'make_order');
        }else{
            $data = $tydmodel->statNetInshopBesCount($where, 'accecipt_man');
        }
        $inshop_data = array_column($data, 'count', 'accecipt_man');

        //163总公司网销----------
        if($is_zgsWx){
            //统计出所有通过总公司网销的预约顾问
            $people_names = array_filter(array_column($data, 'accecipt_man'));
        }
        //-----------------------end

        $where['deal_status'] = 1;
        if($is_zgsWx){
            $data = $tydmodel->statNetInshopBesCount($where, 'make_order');
        }else{
            $data = $tydmodel->statNetInshopBesCount($where, 'accecipt_man');
        }
        $deal_data = array_column($data, 'count', 'accecipt_man');

        $where = array();
        $where['begintime'] = $args['begintime'];
        $where['endtime'] = $args['endtime'];
        $where['department_id'] = $args['department_id'];
        
        $where['is_zp'] = 0; //非赠品
        $data = $tydmodel->statNetDealOrderCount($where, 'accecipt_man');
        $order_deal_data = array_column($data, 'count', 'accecipt_man');

        // 组装数据
        $sourcedata = array();
        foreach ($people_names as $username) {
            $item = array();
            $item['username'] = $username;
            $item['bes_inshop_count'] = isset($inshop_data[$username]) ? $inshop_data[$username] : 0;
            $item['bes_deal_count'] = isset($deal_data[$username]) ? $deal_data[$username] : 0;
            $item['order_deal_count'] = isset($order_deal_data[$username]) ? $order_deal_data[$username] : 0;
            if($item['bes_inshop_count']>0) {
                $item['rate'] = round($item['bes_deal_count']/$item['bes_inshop_count']*100, 1);
            } else {
                $item['rate'] = 0;
            }
            $sourcedata[] = $item;
        }
        return $sourcedata;
    }
    // 各店成交率统计表 下载
    public function downloads() {
        $data = $this->_search_data();

        ini_set('memory_limit','-1');
        set_time_limit(0);
        header("Content-Type: text/html; charset=utf8");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=".iconv('utf-8','gb2312',"网销相关报表").".xls");

        if ($data) {
            $csv_body="<table border='1'><tr>
                <th>销售顾问</th>
                <th>网销预约到店数</th>
                <th>网销预约成交数</th>
                <th>网销订单成交数</th>
                <th>成交率（%）</th>
            </tr>";
            foreach ($data as $key => $val ) {
                $csv_body.="<tr>";
                $csv_body.="<td>".$val['username']."</td><td>".$val['bes_inshop_count']."</td>";
                $csv_body.="<td>".$val['bes_deal_count']."</td><td>".$val['order_deal_count']."</td><td>".$val['rate']."</td>";
                $csv_body.="</tr>";
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