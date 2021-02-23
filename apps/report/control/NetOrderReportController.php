<?php
/**
 *  -------------------------------------------------
 *   @file		: NetOrderReportController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: gengchao
 *   @date		: 2015-11-18 10:15:23
 *  -------------------------------------------------
 */
class NetOrderReportController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist = array("downloads");

	/**
	*	index，搜索框
	*/
    public function index($params) {
        //获取体验店的信息
        $data = $this->getMyDepartments();
        $this->render('netorder_report_form.html',
            array(
                'bar' => Auth::getBar(),
                'allshop'=>$data
            )
        );
    }

    // 各店成交率统计表
    public function search() {
        $sourcedata = $this->_search_data();
        $this->render('netorder_report_list.html',
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
        //1.统计 网销人员的 已审核预约数
        $where = array();
        $where['create_time_start'] = $args['begintime'];
        $where['create_time_end'] = $args['endtime'];
        $where['bespoke_status'] = 2;  //已经审核的
        $where['department_id'] = $args['department_id'];
        $where['make_order'] = $is_admin ? null : $args['netsale_names']; // 网销人员
        //总公司网销-------------
        if($args['department_id'] == 163){
            $where['department_id'] = '';
            $where['make_order'] = $args['netsale_names'];
        }
        //-----------------------end
        $data = $Model->getNetBespokeCount($where);
        $checked_data = array_column($data, 'count', 'make_order');

        //2.统计 网销人员的 实际到店 预约数
        $where = array();
        $where['real_inshop_time_start'] = $args['begintime'];
        $where['real_inshop_time_end'] = $args['endtime'];
        $where['bespoke_status'] = 2;  //已经审核的
        $where['re_status'] = 1;  //到店
        $where['department_id'] = $args['department_id'];
        $where['make_order'] = $is_admin ? null : $args['netsale_names']; // 网销人员
        //总公司网销-------------
        if($args['department_id'] == 163){
            $where['department_id'] = '';
            $where['make_order'] = $args['netsale_names'];
        }
        //-----------------------end
        $data = $Model->getNetBespokeCount($where);
        $inshop_data = array_column($data, 'count', 'make_order');

        //3.统计 网销人员的 实际到店 未成交预约数
        $where['deal_status'] = 2; // 未到店
        $data = $Model->getNetBespokeCount($where);
        $undeal_data = array_column($data, 'count', 'make_order');

        //4.统计订单，amount
        $where = array();
        $where['begintime'] = $args['begintime'];
        $where['endtime'] = $args['endtime'];
        $where['make_order'] = $is_admin ? null : $args['netsale_names']; // 网销人员
        $where['department_id'] = $args['department_id'];
        $where['is_zp'] = 0; //非赠品

        //传值
        $where_sql = '';
        foreach($where as $k => $v){
            $where_sql .= "&".$k.'='.$v;
        }
        $this->assign('args_where',$where_sql);

        //总公司网销-------------
        if($args['department_id'] == 163){
            $where['department_id'] = '';
            $where['make_order'] = $args['netsale_names'];
        }
        //-----------------------end

        $data = $Model->getNetOrderCount($where);
        $onlyData = $Model->getNetOrderCountByMobile($where);
        $order_data_count = array_column($data, 'count', 'make_order');
        $order_data_only_count = array_column($onlyData, 'count', 'make_order');
        $order_data_amount = array_column($data, 'amount', 'make_order');

        $data = $Model->getNetOrderCountOnline($where);
        $order_online_data_count = array_column($data, 'count', 'make_order');

        //5.统计退款订单，退商品
        $data = $Model->getNetOrderReturnCount($where);
        $return_prod_data = array_column($data, 'return_goods_amount', 'make_order');
        $return_order_data = array_column($data, 'return_money_amount', 'make_order');

        // 组装数据
        $sourcedata = array();
        foreach ($args['netsale_names'] as $username) {
            $item = array();
            $item['username'] = $username;
            $item['bes_checked_count'] = isset($checked_data[$username]) ? $checked_data[$username] : 0;
            $item['bes_inshop_count'] = isset($inshop_data[$username]) ? $inshop_data[$username] : 0;
            $item['bes_undeal_count'] = isset($undeal_data[$username]) ? $undeal_data[$username] : 0;
            $item['order_count'] = isset($order_data_count[$username]) ? $order_data_count[$username] : 0;
            $item['order_only_count'] = isset($order_data_only_count[$username]) ? $order_data_only_count[$username] : 0;
            $item['order_amount'] = isset($order_data_amount[$username]) ? $order_data_amount[$username] : 0;
            $item['return_prod_amount'] = isset($return_prod_data[$username]) ? $return_prod_data[$username] : 0;
            $item['order_online_data_count'] = isset($order_online_data_count[$username]) ? $order_online_data_count[$username] : 0;
            $item['return_order_amount'] = isset($return_order_data[$username]) ? $return_order_data[$username] : 0;
            $sourcedata[] = $item;
        }
        return $sourcedata;
    }
    public function downloads() {
        $data = $this->_search_data();

        ini_set('memory_limit','-1');
        set_time_limit(0);
        header("Content-Type: text/html; charset=utf8");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=".iconv('utf-8','gb2312',"网销相关报表").".xls");

        if ($data) {
            $csv_body="<table border='1'><tr>
                <th>网销名</th>
                <th>预约数</th>
                <th>预约到店数</th>
                <th>未成交预约数</th>
                <th>成交单数</th>
                <th>总业绩</th>
                <th>订单商品总金额</th>
                <th>当期退货商品金额</th>
                <th>当期退款不退还金额</th>
            </tr>";
            foreach ($data as $key => $val ) {
                $total = $val['order_amount'] - $val['return_prod_amount'] - $val['return_order_amount'];
                $csv_body.="<tr>";
                $csv_body.="<td>".$val['username']."</td><td>".$val['bes_checked_count']."</td>";
                $csv_body.="<td>".$val['bes_inshop_count']."</td><td>".$val['bes_undeal_count']."</td>";
                $csv_body.="<td>".$val['order_count']."</td><td>".$total."</td><td>".$val['order_amount']."</td>";
                $csv_body.="<td>".$val['return_prod_amount']."</td><td>".$val['return_order_amount']."</td>";
                $csv_body.="</tr>";
            }
            $csv_body.="</table>";
            echo $csv_body;
        } else {
            echo '没有数据！';
        }
        exit;
    }

    /*
    *   预约数  预约到店数	未成交预约数	
    */
    public function bespoke_list(){
        $this->assign('dd',new DictView(new DictModel(1)));
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'a' => _Request::get("a"),
            'name' => _Request::get("name"),
            'department_id' => _Request::get("department_id"),
            'begintime' => _Request::get("begintime"),
            'endtime'   => _Request::get("endtime", date('y-m-d'))
        );
        $a = $args['a'];
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
        $saleChannelModel = new SalesChannelsModel(1);
        $shopPerson = $saleChannelModel->getShopPersonById($args['department_id']);
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

        //开始拿数据
        $Model = new TydReportModel(59);
        //1.统计 网销人员的 已审核预约数
        if($a == 'bes_checked_count'){
            $where = array();
            $where['create_time_start'] = $args['begintime'];
            $where['create_time_end'] = $args['endtime'];
            $where['bespoke_status'] = 2;  //已经审核的
            $where['department_id'] = $args['department_id'];
            //163总公司网销----------
            if($args['department_id'] == 163){
                $where['department_id'] = '';
            }
            //-----------------------end
            $where['make_order'] = $args['name']; // 网销人员
            $data = $Model->getNetBespokeCountDetail($where);
        }elseif($a == 'bes_inshop_count'){
            $where = array();
            $where['real_inshop_time_start'] = $args['begintime'];
            $where['real_inshop_time_end'] = $args['endtime'];
            $where['bespoke_status'] = 2;  //已经审核的
            $where['re_status'] = 1;  //到店
            $where['department_id'] = $args['department_id'];
            //163总公司网销----------
            if($args['department_id'] == 163){
                $where['department_id'] = '';
            }
            //-----------------------end
            $where['make_order'] = $args['name']; // 网销人员
            $data = $Model->getNetBespokeCountDetail($where);
        }elseif($a == 'bes_undeal_count'){
            $where = array();
            $where['real_inshop_time_start'] = $args['begintime'];
            $where['real_inshop_time_end'] = $args['endtime'];
            $where['bespoke_status'] = 2;  //已经审核的
            $where['re_status'] = 1;  //到店
            $where['department_id'] = $args['department_id'];
            //163总公司网销----------
            if($args['department_id'] == 163){
                $where['department_id'] = '';
            }
            //-----------------------end
            $where['deal_status'] = 2; // 未到店
            $where['make_order'] = $args['name']; // 网销人员
            $data = $Model->getNetBespokeCountDetail($where);
        }elseif($a == 'accecipt_inshop_bespokes'){
            $where = array();
            $where['real_inshop_time_start'] = $args['begintime'];
            $where['real_inshop_time_end'] = $args['endtime'];
            $where['bespoke_status'] = 2;  //已经审核的
            $where['re_status'] = 1;  //到店
            $where['department_id'] = $args['department_id'];
            //163总公司网销----------
            if($args['department_id'] == 163){
                $where['department_id'] = '';
                $where['make_order'] = $people_names;
            }
            //-----------------------end
            $where['accecipt_man'] = $args['name']; // 接待人 销售顾问
            $data = $Model->statNetInshopBesCountDetail($where);
        }
        $this->render('netsale_report_bes_list.html',
            array(
                'data'=>$data,
                'rows'=>count($data)
            )
        );
    }

    /*
    *   成交单数 当期退货商品金额
    */
    public function order_list(){
        $this->assign('dd',new DictView(new DictModel(1)));
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'a' => _Request::get("a"),
            'name' => _Request::get("name"),
            'department_id' => _Request::get("department_id"),
            'begintime' => _Request::get("begintime"),
            'endtime'   => _Request::get("endtime", date('y-m-d'))
        );
        $a = $args['a'];
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
        //开始拿数据
        $Model = new TydReportModel(59);
        $where = array();
        $where['begintime'] = $args['begintime'];
        $where['endtime'] = $args['endtime'];
        $where['department_id'] = $args['department_id'];
        //总公司网销----------------boss-1212
        if($args['department_id'] == 163){
            $where['department_id'] = '';
        }
        //--------------------------end*/
        $where['is_zp'] = 0; //非赠品
        if($a == 'order_count'){
            $where['make_order'] = $args['name']; // 网销人员
            $data = $Model->getNetOrderCountDetail($where);
        }elseif($a == 'return_prod_amount'){
            $where['make_order'] = $args['name']; // 网销人员
            $where['return_by'] = 1; //非赠品
            $data = $Model->getNetOrderReturnCountgoodsDetail($where);
        }elseif($a == 'order_online_data_count'){
            $where['make_order'] = $args['name']; // 网销人员
            $data = $Model->getNetOrderCountOnlineDetail($where);
        }elseif($a == 'accecipt_online_orders'){
            
            $where['accecipt_man'] = $args['name']; // 接待人 销售顾问
            $data = $Model->statNetDealOrderCountDetail($where);
        }
        $this->render('netsale_report_ors_list.html',
            array(
                'data'=>$data,
                'rows'=>count($data)
            )
        );
    }

    /*
    *   当期退款不退货金额
    */
    public function res_list(){
        $this->assign('dd',new DictView(new DictModel(1)));
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'a' => _Request::get("a"),
            'name' => _Request::get("name"),
            'department_id' => _Request::get("department_id"),
            'begintime' => _Request::get("begintime"),
            'endtime'   => _Request::get("endtime", date('y-m-d'))
        );
        $a = $args['a'];
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
        //开始拿数据
        $Model = new TydReportModel(59);
        //1.统计 网销人员的 已审核预约数
        if($a == 'return_order_amount'){
            $where = array();
            $where['begintime'] = $args['begintime'];
            $where['endtime'] = $args['endtime'];
            $where['make_order'] = $args['name']; // 网销人员
            $where['department_id'] = $args['department_id'];
            $where['is_zp'] = 0; //非赠品
            $where['return_by'] = 2; //非赠品
            $data = $Model->getNetOrderReturnCountDetail($where);
        }
        $this->render('netsale_report_res_list.html',
            array(
                'data'=>$data,
                'rows'=>count($data)
            )
        );
    }
}

?>