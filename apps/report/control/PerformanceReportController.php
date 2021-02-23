<?php

/**
 *  -------------------------------------------------
 *   @file		: PerformanceReportController.php
 *   @link		:  www.kela.cn
 *   @update	:2015年9月16日 11:16:50
 *  业绩统计报表
 *  -------------------------------------------------
 */
class PerformanceReportController extends Controller
{

    private $performanceModel;
    protected $whitelist = array("search");
    public $limit_time = '2019-01-01';

    private $referer = array(
        1 => '婚博会',
        2 => '非婚博会'
    );

    private $isZp = array(
        0 => '否',
        1 => '是'
    );

    function __construct()
    {
        parent::__construct();
        $this->performanceModel = new PerformanceReportModel(27);
    }

    /**
     * index，搜索框
     */
    public function index($params)
    {
        $myDepartment = $this->getmyDepartment();
        //$this->assign('onlySale', count($sourceList) == 1);
        //$this->assign('sales_channels_idData', $sourceList);
        
        $this->getCustomerSources();
        $this->render('performance_report_search_form.html', array(
            'bar' => Auth::getBar(),
            'referer' => $this->referer,
            'isZp' => $this->isZp,
            'allshop' => $myDepartment
        ));
    }

    public function getShops(){
        $shop_type = _Post::getInt('shop_type');
        
		//获取体验店的信息
		$model = new ShopCfgChannelModel(59);
		$data = $model->getallshopqita();
        $ret = array();
        foreach($data as $key => $val){
            if($shop_type == 1 && $val['shop_type'] == 1){
                $ret[$val['id']] = $val['shop_name'];
            }
            if($shop_type == 2 && $val['shop_type'] == 2){
                $ret[$val['id']] = $val['shop_name'];
            }
            if($shop_type == 0){
                $ret[$val['id']] = $val['shop_name'];
            }
            if($shop_type == 3){
                if(!in_array($val['shop_type'],array(1,2))){
                    $ret[$val['id']] = $val['shop_name'];
                }
            }
        }

        $userChannelmodel = new UserChannelModel(59);
        $data_chennel = $userChannelmodel->getChannels($_SESSION['userId'],0);
        $myChannel="<option value=''></option>";
        foreach($data_chennel as $key => $val){
            if(!empty($ret) && array_key_exists($val['id'],$ret)){
                //$myChannel[$val['id']] = $val['channel_name'];
                $myChannel .= "<option value='".$val['id']."'>".$val['channel_name']."</option>";
            }
        }
        //$res = $this->fetch('shopcount_search_getshops.html',array('myChannel'=>$myChannel));
        //echo $res;
        $result = $myChannel;
        Util::jsonExit($result);
    }

    /**
     * search，列表
     */
    public function search($params)
    {
        ini_set('memory_limit','-1');
        set_time_limit(0);
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, - 10),
            'act' => __FUNCTION__,
            'shop_type' => _Request::getString('shop_type'), //体验店类型
            'department_id' => _Request::getList('shop_id'), // 销售渠道
            'customer_source_id' => _Request::getList("customer_source_id"), // 客户来源
            'is_zp' => _Request::getString("is_zp"), // 是否赠品
            'start_time' => _Request::getString("start_time"), // 制单开始时间
            'end_time' => _Request::getString("end_time"), // 制单结束时间
            'referer' => _Request::getString("referer"),
            'sel_excel' => _Request::getString("sel_excel"),
            'is_gold'=>_Request::getInt("is_gold")
        );

        if(!empty($args['start_time']) && $args['start_time']<$this->limit_time && SYS_SCOPE == 'zhanting'){
            $args['start_time'] = $this->limit_time;
        }
        
        // 获取其有权限渠道
        $departmentList = $this->getDepartmentList();
        foreach($departmentList as $key => $val){
            $channalNames[$val['id']] = $val['channel_name'];  
        }
        // 没有渠道权限直接返回
        if (empty($departmentList)) {
            exit('您没有权限查看!请先联系管理员授权渠道!');
        }
        $myDepartment = array();
        foreach ($departmentList as $department) {
            array_push($myDepartment, $department['id']);
        }
        // 如果为指定渠道则限定该用户所有有权限的渠道信息
        if (empty($args['department_id'])) {
            $shop_type = $args['shop_type'];
            
            //获取体验店的信息
            $model = new ShopCfgChannelModel(59);
            $data = $model->getallshopqita();
            $ret = array();
            foreach($data as $key => $val){
                if($shop_type == 1 && $val['shop_type'] == 1){
                    $ret[$val['id']] = $val['shop_name'];
                }
                if($shop_type == 2 && $val['shop_type'] == 2){
                    $ret[$val['id']] = $val['shop_name'];
                }
                if($shop_type == 0){
                    $ret[$val['id']] = $val['shop_name'];
                }
                if($shop_type == 3){
                    if(!in_array($val['shop_type'],array(1,2))){
                        $ret[$val['id']] = $val['shop_name'];
                    }
                }
            }

            $userChannelmodel = new UserChannelModel(59);
            $data_chennel = $userChannelmodel->getChannels($_SESSION['userId'],0);
            $myDepartment=array();
            foreach($data_chennel as $key => $val){
                if(!empty($ret) && array_key_exists($val['id'],$ret)){
                    //$myChannel[$val['id']] = $val['channel_name'];
                    $myDepartment[]= $val['id'];
                }
            }
            $validDepartment = $myDepartment;
            if(!$validDepartment){
                $validDepartment[]='josjdfowjojo33';
            }
        } else {
            $validDepartment = $args['department_id'];
        }
        $where = array(
            //'department_id' => $validDepartment,
            'from_ad' => implode(',',$args['customer_source_id']),
            'is_zp' => $args['is_zp'],
            'start_time' => $args['start_time'],
            'end_time' => $args['end_time'],
            'referer' => $args['referer']
        );

        // 如果未输入开始时间则表示开始时间为系统到目前为止之前所有的数据
        // 如果未输入结束时间则表示结束时间为当前时间
        /*if (! empty($where['start_time']) && ! empty($where['start_time'])) {
            if (strtotime($where['start_time']) > strtotime($where['start_time'])) {
                exit('开始时间不能大于结束时间!');
            }
        }
        $resultList = $this->performanceModel->pageAllList($where);
        foreach($resultList as $key => $val){
            $depList[$val['department_id']]['sum'] = $val;
        }

		//当前发货订单商品总金额
		$sendgoodsList = $this->performanceModel->getSendgoodsPrice($where);
		$sendgoodsprice = 0;
		foreach($sendgoodsList as $sendv)
		{
            if(!array_key_exists($sendv['department_id'],$depList))
            {
                $val = array();
                $val['department_id'] = $sendv['department_id'];
                $val['ordersum'] = 0;
                $val['orderamount'] = 0;
                $val['ordergoodsamount'] = 0;
                $val['money_paid'] = 0;
                $val['money_unpaid'] = 0;
                $val['real_return_price'] = 0;
                $depList[$sendv['department_id']]['sum'] = $val;
            }   
        }


		foreach($sendgoodsList as $sendv)
		{
			$allsendgoodsmoney=0;
			//商品数量
			$gcount = $sendv['goods_count'];
			//商品价格
			$gprice = $sendv['goods_price'];
			//优惠状态，只有为3的时候 价格才需要减去
			$gfavorable_status = $sendv['favorable_status'];
			//优惠价格
			$gfavor_price = $sendv['favorable_price'];
			//判断优惠是否通过
			if($gfavorable_status == 3)
			{
				//优惠通过(价格等于商品价格减去优惠价)
				$plus = bcsub($gprice,$gfavor_price,2);
				$money = bcmul($gcount,$plus,2);
			}else{
				$money = bcmul($gcount,$gprice,2);
			}
			$allsendgoodsmoney = bcadd($allsendgoodsmoney,$money,2);
			
			$sendgoodsprice = bcadd($sendgoodsprice,$money,2);
            if (empty($depList[$sendv['department_id']]['htj']['sendgoodsprice'])) {
                $depList[$sendv['department_id']]['htj']['sendgoodsprice'] = $allsendgoodsmoney;
            } else {
                $depList[$sendv['department_id']]['htj']['sendgoodsprice'] += $allsendgoodsmoney;
            }
		}
		

        $resultGoodsList = $this->performanceModel->pageAllGoodsList($where);
        foreach($resultGoodsList as $key => $val){
            $total = $val['lz_count'] + $val['cp_count'];
            $total_price = $val['lz_sum_price'] + $val['cp_sum_price'];
            $val['lz_num_percent'] = $val['lz_count']>0?round($val['lz_count']/$total,4)*100:0;
            $val['cp_num_percent'] = $val['cp_count']>0?round($val['cp_count']/$total,4)*100:0;
            $val['lz_price_percent'] = $val['lz_sum_price']>0?round($val['lz_sum_price']/$total_price,4)*100:0;
            $val['cp_price_percent'] = $val['cp_sum_price']>0?round($val['cp_sum_price']/$total_price,4)*100:0;
            $depList[$val['department_id']]['tj'] = $val;
        }


        $rpList = $this->performanceModel->getReturnPrice($where);
        foreach($rpList as $key => $val){
            $depList[$val['department_id']]['sum']['department_id'] = $val['department_id'];
            $depList[$val['department_id']]['sum']['rp'] = $val['rp'];
        }

        $rgList = $this->performanceModel->getReturnGoods($where);

        foreach($rgList as $key => $val){
            $depList[$val['department_id']]['sum']['department_id'] = $val['department_id'];
            $depList[$val['department_id']]['sum']['rg'] = $val['rg'];
        } 

        $totalInfo = array(
            'yeji' => 0,
            'ordersum' => 0,
            'ordergoodsamount' => 0,
            'orderamount' => 0,
            'money_paid' => 0,
            'money_unpaid' => 0,
            'real_return_price' => 0,
            'rg' => 0,
            'rp' => 0,

            'lz_count' => 0,
            'lz_sum_price' => 0,
            'lz_num_percent' => 0,
            'lz_price_percent' => 0,
 
            'cp_count' => 0,
            'cp_sum_price' => 0,
            'cp_num_percent' => 0,
            'cp_price_percent' => 0,
			//当期发货订单商品总金额
			'sendordergoodsamount'=>0
        );

        foreach($depList as $key => $val){
            
            //退款商品总金额计算 begin
            $where['department_id'] = $val['sum']['department_id'];
            $val['sum']['rg'] = $this->performanceModel->getRetrunGoodsAmount($where);
            $depList[$key]['sum']['rg'] = $val['sum']['rg']; 
            //退款商品总金额计算 end
            
            
            $val['sum']['ordergoodsamount'] = isset($val['sum']['ordergoodsamount']) ? $val['sum']['ordergoodsamount'] : 0;
            $val['sum']['rg'] = isset($val['sum']['rg']) ? $val['sum']['rg'] : 0;
            $val['sum']['rp'] = isset($val['sum']['rp']) ? $val['sum']['rp'] : 0;
            $val['tj']['sendgoodsprice'] = isset($val['htj']['sendgoodsprice']) ? $val['htj']['sendgoodsprice'] : 0;

            $depList[$key]['sum']['yeji'] = $val['sum']['ordergoodsamount'] - $val['sum']['rg'] - $val['sum']['rp'];
            $depList[$key]['sum']['channel_name'] = @$channalNames[$val['sum']['department_id']];

            if(!isset($val['sum']['ordersum'])){
                $depList[$key]['sum']['ordersum'] = 0;
            }
            if(!isset($val['sum']['ordergoodsamount'])){
                $depList[$key]['sum']['ordergoodsamount'] = 0;
            }
            if(!isset($val['sum']['orderamount'])){
                $depList[$key]['sum']['orderamount'] = 0;
            }
            if(!isset($val['sum']['money_paid'])){
                $depList[$key]['sum']['money_paid'] = 0;
            }
            if(!isset($val['sum']['money_unpaid'])){
                $depList[$key]['sum']['money_unpaid'] = 0;
            }
            if(!isset($val['sum']['real_return_price'])){
                $depList[$key]['sum']['real_return_price'] = 0;
            }
            if(!isset($val['sum']['rg'])){
                $depList[$key]['sum']['rg'] = 0;
            }
            if(!isset($val['sum']['rp'])){
                $depList[$key]['sum']['rp'] = 0;
            }
            if(!isset($val['tj']['lz_count'])){
                $depList[$key]['tj']['lz_count'] = 0;
            }
            if(!isset($val['tj']['lz_sum_price'])){
                $depList[$key]['tj']['lz_sum_price'] = 0;
            }
            if(!isset($val['tj']['cp_count'])){
                $depList[$key]['tj']['cp_count'] = 0;
            }
            if(!isset($val['tj']['cp_sum_price'])){
                $depList[$key]['tj']['cp_sum_price'] = 0;
            }

            if(!isset($val['tj']['lz_num_percent'])){
                $depList[$key]['tj']['lz_num_percent'] = 0;
            }
            if(!isset($val['tj']['cp_num_percent'])){
                $depList[$key]['tj']['cp_num_percent'] = 0;
            }
            if(!isset($val['tj']['lz_price_percent'])){
                $depList[$key]['tj']['lz_price_percent'] = 0;
            }
            if(!isset($val['tj']['lz_sum_price'])){
                $depList[$key]['tj']['lz_sum_price'] = 0;
            }
            if(!isset($val['tj']['cp_price_percent'])){
                $depList[$key]['tj']['cp_price_percent'] = 0;
            }
            if(!isset($val['tj']['cp_sum_price'])){
                $depList[$key]['tj']['cp_sum_price'] = 0;
            }

            $totalInfo['yeji'] += $depList[$key]['sum']['yeji'];
            $totalInfo['ordersum'] += $val['sum']['ordersum'];
			
            $totalInfo['ordergoodsamount'] += $val['sum']['ordergoodsamount'];
            $totalInfo['orderamount'] += $val['sum']['orderamount'];
            $totalInfo['money_paid'] += $val['sum']['money_paid'];
            $totalInfo['money_unpaid'] += $val['sum']['money_unpaid'];
            $totalInfo['real_return_price'] += $val['sum']['real_return_price'];
                       
            $totalInfo['rg'] += $val['sum']['rg'];            
            $totalInfo['rp'] += $val['sum']['rp'];

            $totalInfo['lz_count'] += $val['tj']['lz_count'];
            $totalInfo['lz_sum_price'] += $val['tj']['lz_sum_price'];
            $totalInfo['cp_count'] += $val['tj']['cp_count'];
            $totalInfo['cp_sum_price'] += $val['tj']['cp_sum_price'];
			$totalInfo['sendordergoodsamount'] += $val['tj']['sendgoodsprice'];
        }
        
        $total = $totalInfo['lz_count'] + $totalInfo['cp_count'];
        $total_price = $totalInfo['lz_sum_price'] + $totalInfo['cp_sum_price'];

        
        $totalInfo['lz_num_percent'] = $total>0 ? round($totalInfo['lz_count']/$total,4)*100:0;
        $totalInfo['cp_num_percent'] = $total>0 ? round($totalInfo['cp_count']/$total,4)*100:0;
        $totalInfo['lz_price_percent'] = $totalInfo['lz_sum_price']>0?round($totalInfo['lz_sum_price']/$total_price,4)*100:0;
        $totalInfo['cp_price_percent'] = $totalInfo['cp_sum_price']>0?round($totalInfo['cp_sum_price']/$total_price,4)*100:0;

        ksort($depList);*/
        //var_dump($validDepartment);die;
        if(empty($validDepartment)) exit('请选中一家体验店');
        $validDepartment = array_unique($validDepartment);
        $dataInfo = array();
        $model = new AppPerformanceCountModel(51);//只读数据库
        foreach ($validDepartment as $dep_id) {
            $dataList = array(
                    'orderTongji' => array(),
                    //'orderData' => $orderData,
                    'returngoods' => 0,
                    'plusreturnprice'=>0,
                    'returnprice' => 0,
                    'lsdata'=>array(),
                    'flsdata'=>array(),
                    'xydata'=>array(),
                    'tsyddata'=>array(),
                    'gold_info'=>array()
                );
            $where['department'] = $dep_id;
            $payModel =new PaymentModel(1);
            $payid = $payModel->getIdbyName('北京京东世纪贸易有限公司');
            if( $where['department'] == '9')
            {
                $where['order_pay_type'] = $payid;
            }
            //var_dump($where);
            
            //<0>所有的订单信息
            $orderData= $model->GetOrderList($where);
            //var_dump($orderData);die;
            //不含300元以下商品
            $orderDataHuanGou = $model->GetOrderListHuanGou($where);
            if(empty($orderData)){
                //die('未找到满足条件的订单信息!');
                continue;
            }
            
            //获取所有天生的款号
            $alltsydsn = $model->getalltsydgoodssn();
            if(!empty($alltsydsn))
            {
                $alltsydsn = array_column($alltsydsn,'style_sn');
            }else{
                $alltsydsn=array(); 
            }
            
            
            
            $lz_flz_return = array(
                'lsdata'=>array('num'=>0,'amount'=>0),
                'flsdata'=>array('num'=>0,'amount'=>0),
                'tsyddata'=>array('num'=>0,'amount'=>0),
                'xydata'=>array('num'=>0,'amount'=>0)
            );
            $ky_lz_flz_return = $lz_flz_return;
            $ng_lz_flz_return = $lz_flz_return;
            $returngoods = 0;
            $returnprice = 0;
            //var_dump($where);
            //<1>当期实退商品金额
            $returngoods = $model->getRetrunGoodsAmount($where);

            //<2>退款不退货总金额
            $returnprice = $model->getReturnPrice($where);
            //<#3>（先退款不退货 然后又退货的商品总金额）;
            
            //先获取return_goods表中的order_goods_id(订单明细自增id)
            $returngoods_orderids = $model->getRetrunGoodsOrderid($where);
            //var_dump($returngoods_orderids);
            
            //跨月金额
            $plus_returnprice= 0 ;
            $returng_details = array();
            $return_nog_details = array();
            $ng_return_details = array();
            if(!empty($returngoods_orderids))
            {
                $oids = array_column($returngoods_orderids,'order_goods_id');
                //+跨月的(退款不退货的)总金额
                $plus_returnprice = $model->getReturnPrice($where,$oids);
                
                
                
                //下面是把退货和退款的分别计算在裸石和成品的
                
                //1.1:如果当期有退货的,那么计算出,当期这些退货的商品是属于裸石还是成品
                $returng_details = $model->getDetailsbyid($oids);
                
                //1.2:统计出退货的商品裸钻数量和成品数量以及金额
                $lz_flz_return = $this->checklzorflz($returng_details,$alltsydsn);
                //print_r($lz_flz_return);
                
                
                //3.1:获取当期退货，并且之前有退款不退货的记录明细
                $return_nog_details = $model->getNogoodsReturoids($where,$oids);
                if(!empty($return_nog_details))
                {
                    $ngids = array_column($return_nog_details,'order_goods_id');
                    $ng_return_details = $model->getDetailsbyid($ngids,2);
                    //3.2:统计出退货的商品 之前有退款不退货的商品裸钻数量和成品数量以及金额
                    $ky_lz_flz_return = $this->checklzorflz($ng_return_details,$alltsydsn);
                    //print_r($ky_lz_flz_return);
                }
                
            }
            
            //2.1 统计当期退款不退货(不退商品的)的订单明细自增id\
            $noreturng_details = array();
            $noreturngoods_orderids = $model->getRetrunGoodsOrderid($where,2);
            if(!empty($noreturngoods_orderids))
            {
                $nids = array_column($noreturngoods_orderids,'order_goods_id');
                //print_r($nids);die();
                
                //根据订单明细自增id获取出订单的明细
                $noreturng_details = $model->getDetailsbyid($nids,2);
                //print_r($noreturng_details);die();
                $ng_lz_flz_return = $this->checklzorflz($noreturng_details,$alltsydsn);
                //print_r($ng_lz_flz_return);
                //根据明细,把数据平摊到裸石和成品上面去,数量不做处理
            }
            
            
            //<3>订单商品明细
            $goodsData = $model->pageAllGoodsList($where);
            //<4>当期发货订单商品明细
            $sendgoodsData = $model->getSendgoodsPrice($where);
            
            
            // 4.1  统计当期发货数量和金额
            $allsendgoodsmoney = $this->getsendgoodsmondy($sendgoodsData);
            //BOSS-1708门店业绩统计区分黄金和非黄金
            $gold_info = $this->differentiateWhetherGold($goodsData,$sendgoodsData,$returng_details,$ng_return_details,$noreturng_details,$alltsydsn);
            //var_dump($gold_info);die;
            //5 统计订单的数据
            $orderTongji = $this->getorderarr($orderData,$orderDataHuanGou);
            $orderTongji['order_send_goods_sum_amount'] = $allsendgoodsmoney ; //当期发货订单商品总金额
            
            //总业绩
            $orderTongji['performance_count'] = $orderTongji['order_goods_sum_amount'] - $returngoods - $returnprice + $plus_returnprice;
            
            
            //下面是计算各自的数量,金额和占比
            $lsdata = array(
                'num'=>0,        //件数
                'amount'=>0,     //金额
                'allnum'=>0,     //件数占比
                'allamount'=>0,  //金额占比
            );
            $flsdata = $lsdata;
            //下面是跟进订单的明细具体的算法咯   20161220
            $percentdata = $this->getpercent($goodsData,$alltsydsn);
            
            //返回裸石,成品的数据
            $percentdata = $this->getlsflsdata($percentdata,$lz_flz_return,$ng_lz_flz_return,$ky_lz_flz_return);
            
            //var_dump($gold_info);die;
            
            //这里把裸石和成品的发生退货的也都区分开来
            //当期实退商品总额(裸石还是成品)
            
            
            //当期实退金额(退款不退货)
            
            
            //当期实退商品 在之前发生过退款不退货的记录总和
            
            $dataList = array(
                    'orderTongji' => $orderTongji,
                    //'orderData' => $orderData,
                    'returngoods' => $returngoods,
                    'plusreturnprice'=>$plus_returnprice,
                    'returnprice' => $returnprice,
                    'lsdata'=>$percentdata['lsdata'],
                    'flsdata'=>$percentdata['flsdata'],
                    'xydata'=>$percentdata['xydata'],
                    'tsyddata'=>$percentdata['tsyddata'],
                    'gold_info'=>$gold_info
                );
            //var_dump($dataList);die;
            //区分黄金非黄金
            $dataInfo[$dep_id] = $this->differentiateGold($dataList,$args['is_gold']);
        }
        $totalInfo = array(
            'performance_count'=>0,
            'order_num'=>0,
            'order_goods_sum_amount'=>0,
            'order_sum_amount'=>0,
            'order_sum_paid'=>0,
            'order_sum_unpaid'=>0,
            'order_real_return_price'=>0,
            'returngoods'=>0,
            'returnprice'=>0,
            'order_send_goods_sum_amount'=>0,
            'suc_order_num'=>0,
            'hb_order_num'=>0,
            'zp_suc_order_num'=>0,
            'tsyddata_num'=>0,
            'tsyddata_amount'=>0,
            'xydata_num'=>0,
            'xydata_amount'=>0,
            'lsdata_num'=>0,
            'lsdata_amount'=>0,
            'lsdata_allnum'=>0,
            'lsdata_allmount'=>0,
            'flsdata_num'=>0,
            'flsdata_amount'=>0,
            'flsdata_allnum'=>0,
            'flsdata_allmount'=>0
            );
        //统计总
        if(!empty($dataInfo)){
            foreach ($dataInfo as $key => $value) {
                //var_dump($value);die;
                $totalInfo['performance_count'] += $value['orderTongji']['performance_count'];//总业绩
                $totalInfo['order_num'] += $value['orderTongji']['order_num'];//订单数量
                $totalInfo['order_goods_sum_amount'] += $value['orderTongji']['order_goods_sum_amount'];//订单商品总金额
                $totalInfo['order_sum_amount'] += $value['orderTongji']['order_sum_amount'];//订单总金额
                $totalInfo['order_sum_paid'] += $value['orderTongji']['order_sum_paid'];//已付款
                $totalInfo['order_sum_unpaid'] += $value['orderTongji']['order_sum_unpaid'];//应收尾款
                $totalInfo['order_real_return_price'] += $value['orderTongji']['order_real_return_price'];//实退金额
                $totalInfo['returngoods'] += $value['returngoods'];//当期实退商品总金额
                $totalInfo['returnprice'] += $value['returnprice'];//当前实退金额（退款不退货）
                $totalInfo['order_send_goods_sum_amount'] += $value['orderTongji']['order_send_goods_sum_amount'];//当期发货订单商品总金额（回款）
                $totalInfo['suc_order_num'] += $value['orderTongji']['suc_order_num'];//实际成交客户数
                $totalInfo['hb_order_num'] += $value['orderTongji']['hb_order_num'];//实际成交客户数（不含换购）
                $totalInfo['zp_suc_order_num'] += $value['orderTongji']['zp_suc_order_num'];//$value['gold_info']['gold_num'];//赠品客户数
                $totalInfo['tsyddata_num'] += $value['tsyddata']['num'];//天生一对件数
                $totalInfo['tsyddata_amount'] += $value['tsyddata']['amount'];//天生一对金额
                $totalInfo['xydata_num'] += $value['xydata']['num'];//星耀件数
                $totalInfo['xydata_amount'] += $value['xydata']['amount'];//星耀金额
                
                $totalInfo['lsdata_num'] += $value['lsdata']['num'];//裸钻件数
                $totalInfo['lsdata_amount'] += $value['lsdata']['amount'];//裸钻金额
                //$totalInfo['lsdata_allnum'] += $value['lsdata']['allnum'];//裸钻件数占比
                //$totalInfo['lsdata_allmount'] += $value['lsdata']['allmount'];//裸钻金额占比
                $totalInfo['flsdata_num'] += $value['flsdata']['num'];//成品件数
                $totalInfo['flsdata_amount'] += $value['flsdata']['amount'];//成品金额
                //$totalInfo['flsdata_allnum'] += $value['flsdata']['allnum'];//成品件数占比
                //$totalInfo['flsdata_allmount'] += $value['flsdata']['allmount'];//成品金额占比
            }
            $allnum = $totalInfo['lsdata_num']+$totalInfo['flsdata_num'];
            $allamount = bcadd($totalInfo['lsdata_amount'], $totalInfo['flsdata_amount'],2);
            //件数占比
            if($allnum > 0 )
            {
                $totalInfo['lsdata_allnum'] = sprintf("%.2f",($totalInfo['lsdata_num'] * 100 / $allnum));
                $totalInfo['flsdata_allnum'] = sprintf("%.2f",($totalInfo['flsdata_num'] * 100 /$allnum));
            }else{
                $totalInfo['lsdata_allnum'] = 0;
                $totalInfo['flsdata_allnum'] = 0;
            }
            //金额占比
            if($allamount > 0 )
            {
                $totalInfo['lsdata_allmount'] = sprintf("%.2f",($totalInfo['lsdata_amount'] * 100 / $allamount));
                $totalInfo['flsdata_allmount'] = sprintf("%.2f",($totalInfo['flsdata_amount'] * 100 /$allamount));
            }else{
                $totalInfo['lsdata_allmount'] = 0;
                $totalInfo['flsdata_allmount'] = 0;
            }
        }
        //var_dump($dataInfo);die;
        if($args['sel_excel']=='excel'){
			$this->groupdownload($dataInfo,$channalNames,$totalInfo);
			exit;
        }

        $this->render('performance_report_search_list.html', array(
            'data' => $dataInfo,
            'channalNames'=>$channalNames,
            'totalInfo' => $totalInfo
        ));
    }

    //区分黄金非黄金
    public function differentiateGold($dataList, $is_gold)
    {
        //var_dump($is_gold);die;
        //var_dump($dataList['gold_info']);die;
        if($is_gold == 1){//黄金
            $dataList['orderTongji']['performance_count'] = $dataList['gold_info']['gold_performance_total'];//总业绩
            $dataList['orderTongji']['order_num'] = $dataList['gold_info']['gold_order_num'];//订单数量
            $dataList['orderTongji']['order_goods_sum_amount'] = $dataList['gold_info']['gold_order_goods_total_price'];//订单商品总金额
            $dataList['orderTongji']['order_sum_amount'] = $dataList['gold_info']['gold_order_total_price'];//订单总金额
            $dataList['orderTongji']['order_sum_paid'] = $dataList['gold_info']['gold_order_sum_paid'];//已付款
            $dataList['orderTongji']['order_sum_unpaid'] = $dataList['gold_info']['gold_order_sum_unpaid'];//应收尾款
            $dataList['orderTongji']['order_real_return_price'] = $dataList['gold_info']['gold_order_real_return_price'];//实退金额
            $dataList['returngoods'] = $dataList['gold_info']['gold_now_return_goods_total'];//当期实退商品总金额
            $dataList['returnprice'] = $dataList['gold_info']['gold_now_return_real_total'];//当前实退金额（退款不退货）
            $dataList['orderTongji']['order_send_goods_sum_amount'] = $dataList['gold_info']['gold_allsendgoodsmoney'];//当期发货订单商品总金额（回款）
            $dataList['orderTongji']['suc_order_num'] = $dataList['gold_info']['gold_num'];//实际成交客户数
            $dataList['orderTongji']['hb_order_num'] = $dataList['gold_info']['gold_num_hg'];//实际成交客户数（不含换购）
            $dataList['orderTongji']['zp_suc_order_num'] = 0;//$dataList['gold_info']['gold_num'];//赠品客户数
            $dataList['tsyddata']['num'] = $dataList['gold_info']['gold_tsyddata_num'];//天生一对件数
            $dataList['tsyddata']['amount'] = $dataList['gold_info']['gold_tsyddata_amount'];//天生一对金额
            $dataList['xydata']['num'] = $dataList['gold_info']['gold_xydata_num'];//星耀件数
            $dataList['xydata']['amount'] = $dataList['gold_info']['gold_xydata_amount'];//星耀金额
            $dataList['lsdata']['num'] = $dataList['gold_info']['gold_lsdata_num'];//裸钻件数
            $dataList['lsdata']['amount'] = $dataList['gold_info']['gold_lsdata_amount'];//裸钻金额
            $dataList['lsdata']['allnum'] = $dataList['gold_info']['gold_lsdata_allnum'];//裸钻件数占比
            $dataList['lsdata']['allmount'] = $dataList['gold_info']['gold_lsdata_allmount'];//裸钻金额占比
            $dataList['flsdata']['num'] = $dataList['gold_info']['gold_flsdata_num'];//成品件数
            $dataList['flsdata']['amount'] = $dataList['gold_info']['gold_flsdata_amount'];//成品金额
            $dataList['flsdata']['allnum'] = $dataList['gold_info']['gold_flsdata_allnum'];//成品件数占比
            $dataList['flsdata']['allmount'] = $dataList['gold_info']['gold_flsdata_allmount'];//成品金额占比
        }elseif($is_gold == 2){//非黄金
            $dataList['orderTongji']['performance_count'] = $dataList['gold_info']['no_gold_performance_total'];//总业绩
            $dataList['orderTongji']['order_num'] = $dataList['gold_info']['no_gold_order_num'];//订单数量
            $dataList['orderTongji']['order_goods_sum_amount'] = $dataList['gold_info']['no_gold_order_goods_total_price'];//订单商品总金额
            $dataList['orderTongji']['order_sum_amount'] = $dataList['gold_info']['no_gold_order_total_price'];//订单总金额
            $dataList['orderTongji']['order_sum_paid'] = $dataList['gold_info']['no_gold_order_sum_paid'];//已付款
            $dataList['orderTongji']['order_sum_unpaid'] = $dataList['gold_info']['no_gold_order_sum_unpaid'];//应收尾款
            $dataList['orderTongji']['order_real_return_price'] = $dataList['gold_info']['no_gold_order_real_return_price'];//实退金额
            $dataList['returngoods'] = $dataList['gold_info']['no_gold_now_return_goods_total'];//当期实退商品总金额
            $dataList['returnprice'] = $dataList['gold_info']['no_gold_now_return_real_total'];//当前实退金额（退款不退货）
            $dataList['orderTongji']['order_send_goods_sum_amount'] = $dataList['gold_info']['no_gold_allsendgoodsmoney'];//当期发货订单商品总金额（回款）
            $dataList['orderTongji']['suc_order_num'] = $dataList['gold_info']['no_gold_num'];//实际成交客户数
            $dataList['orderTongji']['hb_order_num'] = $dataList['gold_info']['no_gold_num_hg'];//实际成交客户数（不含换购）
            $dataList['orderTongji']['zp_suc_order_num'] = 0;//$dataList['gold_info']['gold_num'];//赠品客户数
            $dataList['tsyddata']['num'] = $dataList['gold_info']['no_gold_tsyddata_num'];//天生一对件数
            $dataList['tsyddata']['amount'] = $dataList['gold_info']['no_gold_tsyddata_amount'];//天生一对金额
            $dataList['xydata']['num'] = $dataList['gold_info']['no_gold_xydata_num'];//星耀件数
            $dataList['xydata']['amount'] = $dataList['gold_info']['no_gold_xydata_amount'];//星耀金额
            $dataList['lsdata']['num'] = $dataList['gold_info']['no_gold_lsdata_num'];//裸钻件数
            $dataList['lsdata']['amount'] = $dataList['gold_info']['no_gold_lsdata_amount'];//裸钻金额
            $dataList['lsdata']['allnum'] = $dataList['gold_info']['no_gold_lsdata_allnum'];//裸钻件数占比
            $dataList['lsdata']['allmount'] = $dataList['gold_info']['no_gold_lsdata_allmount'];//裸钻金额占比
            $dataList['flsdata']['num'] = $dataList['gold_info']['no_gold_flsdata_num'];//成品件数
            $dataList['flsdata']['amount'] = $dataList['gold_info']['no_gold_flsdata_amount'];//成品金额
            $dataList['flsdata']['allnum'] = $dataList['gold_info']['no_gold_flsdata_allnum'];//成品件数占比
            $dataList['flsdata']['allmount'] = $dataList['gold_info']['no_gold_flsdata_allmount'];//成品金额占比
        }else{

        }
        //销毁无用变量
        unset($dataList['gold_info']);
//var_dump($dataList);die;
        return $dataList;
    }

    /**
     * 获取销售渠道
     */
    private function getDepartmentList()
    {
        $model = new UserChannelModel(1);
        $data = $model->getChannels($_SESSION['userId'], 0);
        if (empty($data)) {
            die('请先联系管理员授权渠道!');
        }
        return $data;
    }

    //search.1  统计当期发货数量和金额
    public function getsendgoodsmondy($sendgoodsData = array())
    {
        $allsendgoodsmoney= 0 ;
        if(!empty($sendgoodsData))
        {
            foreach($sendgoodsData as $sendv)
            {
                //商品数量
                $gcount = $sendv['goods_count'];
                //商品价格
                $gprice = $sendv['goods_price'];
                //优惠状态，只有为3的时候 价格才需要减去
                $gfavorable_status = $sendv['favorable_status'];
                //优惠价格
                $gfavor_price = $sendv['favorable_price'];
                //判断优惠是否通过
                if($gfavorable_status == 3)
                {
                    //优惠通过(价格等于商品价格减去优惠价)
                    $plus = bcsub($gprice,$gfavor_price,2);
                    $money = bcmul($gcount,$plus,2);
                }else{
                    $money = bcmul($gcount,$gprice,2);
                }
                $allsendgoodsmoney = bcadd($allsendgoodsmoney,$money,2);
            }
        }
        return $allsendgoodsmoney;
    }

    //search.6 根据订单明细，区分成品和裸钻 数量 以及金额
    public function checklzorflz($orderdetails,$alltsydsn=array())
    {
        if(empty($orderdetails))
        {
            return array(
                'lsdata'=>array('num'=>0,'amount'=>0),
                'flsdata'=>array('num'=>0,'amount'=>0),
                'tsyddata'=>array('num'=>0,'amount'=>0),
                'xydata'=>array('num'=>0,'amount'=>0)
            );
        }
        /*三、区分成品，裸石规则：①工厂配钻，工厂镶嵌；②需工厂镶嵌；③不需镶嵌；④客户先看钻再返厂镶嵌；⑤镶嵌4c裸钻；⑥镶嵌4c裸钻，客户先看钻；⑦成品；⑧半成品
            （1） 镶嵌要求：②④⑤⑥，钻石证书类型为EGL，金额=托+钻的金额，成品数量为1
            （2） 镶嵌要求：③，单独下了一个EGL裸钻，EGL裸钻算一个成品
            （3） 非（1）（2）两点并且主石重>=0.2，镶嵌要求不管是什么，裸石就是裸石，非裸石就是成品
            （4） 非（1）（2）两点并且主石重<0.2，镶嵌要求：②④⑤⑥，金额=托+钻的金额，成品数量为1
            （5） 非（1）（2）两点并且主石重<0.2，镶嵌要求：③，裸钻算成品
            （6） 非以上条件，镶嵌要求：①⑦⑧ 裸石是裸石，非裸石是成品*/
        $wone = array('需工厂镶嵌','客户先看钻再返厂镶嵌','镶嵌4c裸钻','镶嵌4c裸钻，客户先看钻');
        //$wtwo = array('工厂配钻','成品','半成品');
        $wthree = array('工厂配钻，工厂镶嵌','成品','半成品');
        $lznum=0;
        $flznum=0;
        $lzamount=0;
        $flzamount =0;
        $tsydnum = 0;
        $tsydamount = 0;
        $xynum =0;
        $xyamount=0;
        $allnum =0;
        $allamount=0;
        $allzs = array();
        
        //普通证书号
        //天生一对证书号
        $zhengshuhao_w1 = array();
        //条件4的证书号
        $zhengshuhao_w4 = array();
        //天生一对的证书号
        $zhengshuhao_tsyd = array();
        
        
        //条件一的统计
        $cp_w1_num = 0;
        $cp_w1_money=0;
        //条件二的统计
        $cp_w2_num = 0;
        $cp_w2_money=0;
        //条件三的统计
        $cp_w3_num = 0;
        $cp_w3_money=0;
        $lz_w3_num = 0;
        $lz_w3_money=0;
        //条件四的统计
        $cp_w4_num = 0;
        $cp_w4_money=0;
        //条件5的统计
        $cp_w5_num=0;
        $cp_w5_money=0;
        $lz_w5_num =0;
        $lz_w5_money=0;
        //条件6的统计
        $cp_w6_num=0;
        $cp_w6_money=0;
        $lz_w6_num = 0;
        $lz_w6_money=0;
        
        
        
        //天生一对各种条件统计
        $tsyd_w1_num=0;
        $tsyd_w1_money =0;
        $tsyd_w2_num=0;
        $tsyd_w2_money =0;
        $tsyd_w3_num=0;
        $tsyd_w3_money =0;
        //星耀统计
        $xynum =0;
        $xyamount=0;
        
        //print_r($orderdetails);
        //die();
        foreach($orderdetails as $k=>$obj)
        {
            $xq = $obj['xiangqian'];
            $num = $obj['goods_count'];
            $gtype = $obj['goods_type'];
            
            $cart = $obj['cart'];   //石重
            $cert = $obj['cert'];   //证书类型
            $zhengshuhao = trim($obj['zhengshuhao'])=='' ? '空' : $obj['zhengshuhao'];
            $goods_sn = $obj['goods_sn'];
            //商品金额
            if($obj['favorable_status']==3)
            {
                $money = $obj['goods_count']*( $obj['goods_price']- $obj['favorable_price']);
            }else{
                $money = $obj['goods_count'] * $obj['goods_price']; 
            }
            //如果镶嵌要求为2,4,5,6并且证书类型为EGL,则为成品  //金额=托+钻的金额，成品数量为1 (钻石多少数量就多少)
            
            if(in_array($zhengshuhao,$zhengshuhao_w1))
            {
                $cp_w1_money += $money;
            }elseif(in_array($zhengshuhao,$zhengshuhao_w4))
            {
                $cp_w4_money+=$money;
            }elseif(in_array($xq,$wone) && ($cert == 'EGL' || $cert =='AGL'))
            {
                if(!in_array($zhengshuhao,$zhengshuhao_w1))
                {
                    $cp_w1_num+= $num;
                    $cp_w1_money += $money;
                    if($zhengshuhao != '空')
                    {
                        array_push($zhengshuhao_w1,$zhengshuhao);
                    }
                }
            }elseif(( $xq == '不需工厂镶嵌' ||  $xq == '不需镶嵌')  && ($cert == 'EGL' || $cert =='AGL')){
                $cp_w2_num+=$num;
                $cp_w2_money += $money;
            }elseif($cert !='EGL' && $cert != 'AGL' && $cart >= 0.2)
            {
                //条件三
                if( $gtype =='lz'){
                    $lz_w3_num+= $num;
                    $lz_w3_money += $money; 
                }else{
                    $cp_w3_num+= $num;
                    $cp_w3_money += $money;     
                }
            }elseif($cert !='EGL' && $cert != 'AGL' && $cart < 0.2){
                //条件四
                if(in_array($xq,$wone)){
                    if(!in_array($zhengshuhao,$zhengshuhao_w4))
                    {
                        if($zhengshuhao != '空')
                        {
                            array_push($zhengshuhao_w4,$zhengshuhao);
                        }
                        $cp_w4_num += $num;
                        $cp_w4_money+=$money;
                    }
                }elseif($xq=='不需工厂镶嵌' || $xq=='不需镶嵌'){
                    //条件五
                    /*if($gtype == 'lz')
                    {
                        $lz_w5_num+=$num;
                        $lz_w5_money+=$money;
                    }else{
                                
                    }*/
                    $cp_w5_num += $num;
                    $cp_w5_money += $money;
                }elseif(in_array($xq,$wthree)){
                    //条件六
                    if($gtype !='lz')
                    {
                        $cp_w6_num += $num;
                        $cp_w6_money += $money; 
                    }else{
                        $lz_w6_num += $num;
                        $lz_w6_money += $money; 
                    }   
                }
            }elseif(in_array($xq,$wthree))
            {
                //条件六
                if($gtype !='lz')
                {
                    $cp_w6_num += $num;
                    $cp_w6_money += $money; 
                }else{
                    $lz_w6_num += $num;
                    $lz_w6_money += $money; 
                }
            }
            //1和4  20分以上
            //2和5  20分一下
            //下面是天生一对的 //和星耀的
            
            //判断天生一对的托
            //如果在数组里面了,说明过来的是托  只判断证书号  因为表里面的有些数据证书类型是不对的
            /*if(in_array($zhengshuhao,$zhengshuhao_tsyd))
            {
                if($cart >= 0.2){
                    if(in_array($goods_sn,$alltsydsn))
                    {
                        $tsyd_w1_num += $num;
                        $tsyd_w1_money += $money;
                    }   
                }else{
                    $tsyd_w1_money += $money;
                }
                continue;
            }
            
            if($cert =='HRD-D')
            {
                if( $cart >= 0.2 )
                {
                    if(!in_array($zhengshuhao,$zhengshuhao_tsyd))
                    {
                        $tsyd_w1_num += $num;
                        $tsyd_w1_money += $money;
                        if($zhengshuhao != '空')
                        {
                            array_push($zhengshuhao_tsyd,$zhengshuhao);
                        }
                    }
                }else{
                    //如果是20分以下
                    if(!in_array($zhengshuhao,$zhengshuhao_tsyd)){
                        $tsyd_w2_num += $num;
                        $tsyd_w2_money += $money;
                        if($zhengshuhao != '空'){
                            array_push($zhengshuhao_tsyd,$zhengshuhao);
                        }
                    }
                }   
            }else{
                //如果是单个的天生一对的托
                if($zhengshuhao == '空' && in_array($goods_sn,$alltsydsn))
                {
                    $tsyd_w3_num += $num;
                    $tsyd_w3_money += $money;   
                }
            }*/

            /*天生一对销售件数=数量1+数量2
            数量1=销售订单商品证书类型为“HRD-D”,商品类型非“lz”非“zp”并且钻石大小大于0的商品数量
            数量2=销售订单商品系列归属为“天生一对”并且钻石大小为0或者空的商品数量
            天生一对销售金额：金额1+金额2
            金额1=销售订单商品证书类型为“HRD-D”并且钻石大小大于0的商品成交价总和
            金额2=销售订单商品系列归属为“天生一对”并且钻石大小为0或者空的商品成交价总和*/
            //天生一对
            if($cert == 'HRD-D' && !in_array($gtype, array('lz','zp')) && $cart >0){
                $tsyd_w1_num += $num;
            }
            if($cert == 'HRD-D' && $cart >0){
                $tsyd_w1_money += $money;
            }

            //天生一对特殊款（不包含证书类型是HRD-D）
            //款号在销售政策管理-销售商品里的“天生一对特殊款”里存在算天生一对销售
            if($cert != 'HRD-D' && $this->checkTsydSpecial($goods_sn)){
                $tsyd_w3_num += $num;
                if($xq == '需工厂镶嵌' && !empty($zhengshuhao)){
                    $tsyd_w3_money += $money;
                    $tsyd_w3_money += $this->getTsydDiaPrice($zhengshuhao);
                }elseif($xq != '需工厂镶嵌'){
                    $tsyd_w3_money += $money;
                }
            }

            //款式归属为天生一对
            if(in_array($goods_sn,$alltsydsn) && $cart<=0 && !$this->checkTsydSpecial($goods_sn)){
                $tsyd_w2_num += $num;
                $tsyd_w2_money += $money;
            }

    
            //星耀统计
            if($cert == 'HRD-S' && $gtype == 'lz')
            {
                $xynum+=$num;
                $xyamount+=$money;
            }
            //总数
    
            //星耀统计
            /*if($cert == 'HRD-S')
            {
                $xynum+=$num;
                $xyamount+=$money;
            }*/
            //总数
        }

        $lznum = $lz_w3_num+$lz_w5_num+$lz_w6_num;
        $lzamount = $lz_w3_money + $lz_w5_money+$lz_w6_money;

        $flznum = $cp_w1_num+$cp_w2_num+$cp_w3_num+$cp_w4_num+$cp_w5_num+$cp_w6_num;
        $flzamount = $cp_w1_money+$cp_w2_money+$cp_w3_money+$cp_w4_money+$cp_w5_money+$cp_w6_money;
        
        //$tsydnum = $tsyd_w1_num+$tsyd_w2_num+$tsyd_w3_num;
        //$tsydamount = $tsyd_w1_money+$tsyd_w2_money+$tsyd_w3_money;

        $tsydnum = $tsyd_w1_num+$tsyd_w2_num+$tsyd_w3_num;
        $tsydamount = $tsyd_w1_money+$tsyd_w2_money+$tsyd_w3_money;
        
        $allnum =$lznum+$flznum;
        $allamount = $lzamount+$flzamount;
        
        $returndata = array(
            'lsdata'=>array('num'=>$lznum,'amount'=>$lzamount),
            'flsdata'=>array('num'=>$flznum,'amount'=>$flzamount),
            'tsyddata'=>array('num'=>$tsydnum,'amount'=>$tsydamount),
            'xydata'=>array('num'=>$xynum,'amount'=>$xyamount)
        );
        return $returndata;
    }

    private function getCustomerSources()
    {
        // 客户来源
        $CustomerSourcesModel = new CustomerSourcesModel(1);
        $CustomerSourcesList = $CustomerSourcesModel->getCustomerSourcesList("`id`,`source_name`");
        $this->assign('customer_source_list', $CustomerSourcesList);
    }

    //search.5 统计满足条件的订单的数据
    public function getorderarr($orderData = array(),$orderDataHuanGou =array())
    {
        
        $orderTongji = array();
        $orderTongji['order_num'] = 0;//订单总数
        $orderTongji['zp_suc_order_num'] = 0;//订单总数（赠品单）
        $orderTongji['order_sum_amount'] = 0;//订单总金额
        $orderTongji['order_goods_sum_amount'] = 0;//订单商品总金额
        $orderTongji['order_sum_paid'] = 0;//已付金额
        $orderTongji['order_sum_unpaid'] = 0;//未付金额
        $orderTongji['order_real_return_price'] = 0; //实退金额
        $orderTongji['goods_amount'] = 0;
        //订单之外的数据定义
        $cusinfo = array(); //定义一个数组用来容纳 同一个手机号 同一天的订单
        $zp_cusinfo = array();  //定义一个数组用来容纳 同一个手机号 同一天的订单(赠品)
        $hbinfo = array(); //定义一个数组用来容纳 需要合并的订单数据
        $orderTongji['suc_order_num'] = 0;//成单数
        $orderTongji['hb_order_num'] = 0 ;//(新增)合并的订单数
        
        $mobilearr = array();   //新增合并订单的手机号数组
        
        //开始循环订单数据
        foreach($orderData as $key => $order){
            $orderTongji['order_num']++;
            $orderTongji['order_sum_amount'] += $order['order_amount'];
            $orderTongji['order_goods_sum_amount'] += $order['goods_amount'] - $order['favorable_price'];
            $orderTongji['order_sum_paid'] += $order['money_paid'];
            $orderTongji['order_sum_unpaid'] += $order['money_unpaid'];
            $orderTongji['order_real_return_price'] += $order['real_return_price'];
            
            //同一个手机号 同月算一个订单 不含赠品单
            $createtime = substr($order['pay_date'],0,7);
            if($order['is_zp'] != '1'){
                $cusinfo[$createtime][] = $order['mobile'];
            }else{
                $zp_cusinfo[$createtime][] = $order['mobile'];
            }
            
            //增加合并订单(不含300一下订单统计) 不含赠品单
            //if($order['is_zp'] != '1' && $order['order_amount']> 300)
            //if($order['is_zp'] != '1' && $order['order_amount']> 300 && !in_array($order['mobile'],$mobilearr))
            //{
                //$orderTongji['hb_order_num']++;
                //array_push($mobilearr,$order['mobile']);
                //$hb_order_info[$createtime][] = $order['mobile'];
            //}
            
        }

        $hb_order_info = array();
        //循环换购订单
        foreach ($orderDataHuanGou as $key => $order) {
            //同一个手机号 同月算一个订单 不含赠品单
            $createtime = substr($order['pay_date'],0,7);
            //增加合并订单(不含300一下订单统计) 不含赠品单
            if($order['is_zp'] != '1')
            {
                $hb_order_info[$createtime][] = $order['mobile'];
            }
        }
        //tongji成交num 同一个手机号 同月算一个订单 不含赠品单
        if(!empty($cusinfo)){
            foreach ($cusinfo as $val) {
                $orderTongji['suc_order_num'] += count(array_flip($val));
            }
        }
        //tongji成交num 同一个手机号 同月算一个订单 赠品单
        if(!empty($zp_cusinfo)){
            foreach ($zp_cusinfo as $val) {
                $orderTongji['zp_suc_order_num'] += count(array_flip($val));
            }
        }
        //1根据搜索条件段内查询结果同一个手机号算一个订单（2）订单金额大于300元（不含300）
        //改1561 :同一个手机号 同月算一个订单
        if(!empty($hb_order_info)){
            foreach ($hb_order_info as $val) {
                $orderTongji['hb_order_num']+= count(array_flip($val));
            }
        }
        //$orderTongji['hb_order_num']+= count(array_flip($hbinfo));
        return $orderTongji;
    }

	//封装函数用来返回自己所属的体验店或者渠道
	public function getmyDepartment()
	{
		//获取体验店的信息
		$model = new ShopCfgChannelModel(59);
		$data = $model->getallshop();
        $List_pagt = array();
        foreach ($data as $key => $value) {
            # code...
            $List_pagt[$value['id']] = $value;
        }
        $userChannelmodel = new UserChannelModel(59);
        $data_chennel = $userChannelmodel->getChannels($_SESSION['userId'],0);
        if(empty($data_chennel)){
            die('请先联系管理员授权渠道!');
        }
        $myDepartment = array();
        foreach($data_chennel as $key => $val){
            $myDepartment[$key] = isset($List_pagt[$val['id']]) ? $List_pagt[$val['id']] : '';
            if(!$myDepartment[$key]){
                unset($myDepartment[$key]);
            }
        }
		return $myDepartment;
	}

	//导出
	public function groupdownload($data,$channalNames,$totalInfo) {


        header("Content-Type: text/html; charset=gb2312");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=".iconv('utf-8','gb2312',"业绩统计报表").".xls");

        $csv_body="<table border='1'><tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td colspan='2'>天生一对</td><td colspan='2'>星耀</td><td colspan='4'>裸钻</td><td colspan='4'>成品</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
        $csv_body.="<tr><td>销售渠道</td><td>总业绩</td><td>订单数量</td><td>订单商品总金额</td><td>订单总金额</td><td>已付款</td><td>应收尾款</td><td>实退金额</td><td>当期实退商品总金额</td><td>当前实退金额（退款不退货）</td><td>当期发货订单商品总金额（回款）</td><td>实际成交客户数</td><td>实际成交客户数（不含换购）</td><td>赠品客户数</td><td>件数</td><td>金额</td><td>件数</td><td>金额</td><td>件数</td><td>金额</td><td>件数占比</td><td>金额占比</td><td>件数</td><td>金额</td><td>件数占比</td><td>金额占比</td></tr>";
		if ($data) {
            foreach ($data as $k => $val ) {
                $csv_body.="<tr><td>".$channalNames[$k]."</td><td>".$val['orderTongji']['performance_count']."</td><td>".$val['orderTongji']['order_num']."</td><td>".$val['orderTongji']['order_goods_sum_amount']."</td><td>".$val['orderTongji']['order_sum_amount']."</td><td>".$val['orderTongji']['order_sum_paid']."</td><td>".$val['orderTongji']['order_sum_unpaid']."</td><td>".$val['orderTongji']['order_real_return_price']."</td><td>".$val['returngoods']."</td><td>".$val['returnprice']."</td><td>".$val['orderTongji']['order_send_goods_sum_amount']."</td><td>".$val['orderTongji']['suc_order_num']."</td><td>".$val['orderTongji']['hb_order_num']."</td><td>".$val['orderTongji']['zp_suc_order_num']."</td><td>".$val['tsyddata']['num']."</td><td>".$val['tsyddata']['amount']."</td><td>".$val['xydata']['num']."</td><td>".$val['xydata']['amount']."</td><td>".$val['lsdata']['num']."</td><td>".$val['lsdata']['amount']."</td><td>".$val['lsdata']['allnum']."</td><td>".$val['lsdata']['allmount']."</td><td>".$val['flsdata']['num']."</td><td>".$val['flsdata']['amount']."</td><td>".$val['flsdata']['allnum']."</td><td>".$val['flsdata']['allmount']."</td></tr>";
            }
            if($totalInfo){
                    $csv_body.="<tr><td>总计：</td><td>".$totalInfo['performance_count']."</td><td>".$totalInfo['order_num']."</td><td>".$totalInfo['order_goods_sum_amount']."</td><td>".$totalInfo['order_sum_amount']."</td><td>".$totalInfo['order_sum_paid']."</td><td>".$totalInfo['order_sum_unpaid']."</td><td>".$totalInfo['order_real_return_price']."</td><td>".$totalInfo['returngoods']."</td><td>".$totalInfo['returnprice']."</td><td>".$totalInfo['order_send_goods_sum_amount']."</td><td>".$totalInfo['suc_order_num']."</td><td>".$totalInfo['hb_order_num']."</td><td>".$totalInfo['zp_suc_order_num']."</td><td>".$totalInfo['tsyddata_num']."</td><td>".$totalInfo['tsyddata_amount']."</td><td>".$totalInfo['xydata_num']."</td><td>".$totalInfo['xydata_amount']."</td><td>".$totalInfo['lsdata_num']."</td><td>".$totalInfo['lsdata_amount']."</td><td>".$totalInfo['lsdata_allnum']."%</td><td>".$totalInfo['lsdata_allmount']."%</td><td>".$totalInfo['flsdata_num']."</td><td>".$totalInfo['flsdata_amount']."</td><td>".$totalInfo['flsdata_allnum']."%</td><td>".$totalInfo['flsdata_allmount']."%</td></tr>";                
            }
            $csv_footer="</table>";
            echo $csv_body.$csv_footer;
		} else {
			echo '没有数据！';
		}

	}

    //search.6 统计订单明细中,成品,裸钻的总数,总金额,和占比
    public function getpercent($orderdetails,$alltsydsn=array())
    {
        if(empty($orderdetails))
        {
            return false;
        }
        /*三、区分成品，裸石规则：①工厂配钻，工厂镶嵌；②需工厂镶嵌；③不需镶嵌；④客户先看钻再返厂镶嵌；⑤镶嵌4c裸钻；⑥镶嵌4c裸钻，客户先看钻；⑦成品；⑧半成品
            （1） 镶嵌要求：②④⑤⑥，钻石证书类型为EGL，金额=托+钻的金额，成品数量为1
            （2） 镶嵌要求：③，单独下了一个EGL裸钻，EGL裸钻算一个成品
            （3） 非（1）（2）两点并且主石重>=0.2，镶嵌要求不管是什么，裸石就是裸石，非裸石就是成品
            （4） 非（1）（2）两点并且主石重<0.2，镶嵌要求：②④⑤⑥，金额=托+钻的金额，成品数量为1
            （5） 非（1）（2）两点并且主石重<0.2，镶嵌要求：③，裸钻算成品
            （6） 非以上条件，镶嵌要求：①⑦⑧ 裸石是裸石，非裸石是成品*/
        $wone = array('需工厂镶嵌','客户先看钻再返厂镶嵌','镶嵌4c裸钻','镶嵌4c裸钻，客户先看钻');
        //$wtwo = array('工厂配钻','成品','半成品');
        $wthree = array('工厂配钻，工厂镶嵌','成品','半成品');
        $lznum=0;
        $flznum=0;
        $lzamount=0;
        $flzamount =0;
        $tsydnum = 0;
        $tsydamount = 0;
        $xynum =0;
        $xyamount=0;
        $allnum =0;
        $allamount=0;
        $allzs = array();
        
        //普通证书号
        //天生一对证书号
        $zhengshuhao_w1 = array();
        //条件4的证书号
        $zhengshuhao_w4 = array();
        //天生一对的证书号
        $zhengshuhao_tsyd = array();
        
        
        //条件一的统计
        $cp_w1_num = 0;
        $cp_w1_money=0;
        //条件二的统计
        $cp_w2_num = 0;
        $cp_w2_money=0;
        //条件三的统计
        $cp_w3_num = 0;
        $cp_w3_money=0;
        $lz_w3_num = 0;
        $lz_w3_money=0;
        //条件四的统计
        $cp_w4_num = 0;
        $cp_w4_money=0;
        //条件5的统计
        $cp_w5_num=0;
        $cp_w5_money=0;
        $lz_w5_num =0;
        $lz_w5_money=0;
        //条件6的统计
        $cp_w6_num=0;
        $cp_w6_money=0;
        $lz_w6_num = 0;
        $lz_w6_money=0;
        
        
        
        //天生一对各种条件统计
        $tsyd_w1_num=0;
        $tsyd_w1_money =0;
        $tsyd_w2_num=0;
        $tsyd_w2_money =0;
        $tsyd_w3_num=0;
        $tsyd_w3_money =0;
        //星耀统计
        $xynum =0;
        $xyamount=0;
        
        //print_r($orderdetails);
        //die();
        foreach($orderdetails as $k=>$obj)
        {
            $xq = $obj['xiangqian'];
            $num = $obj['goods_count'];
            $gtype = $obj['goods_type'];
            
            $cart = $obj['cart'];   //石重
            $cert = $obj['cert'];   //证书类型
            $zhengshuhao = trim($obj['zhengshuhao'])=='' ? '空' : $obj['zhengshuhao'];
            $goods_sn = $obj['goods_sn'];
            //是否已退
            $is_return = $obj['is_return'];
            if($is_return)
            {
                //continue;
            }
            
            //商品金额
            if($obj['favorable_status']==3)
            {
                $money = $obj['goods_count']*( $obj['goods_price']- $obj['favorable_price']);
            }else{
                $money = $obj['goods_count'] * $obj['goods_price']; 
            }
            //如果镶嵌要求为2,4,5,6并且证书类型为EGL,则为成品  //金额=托+钻的金额，成品数量为1 (钻石多少数量就多少)
            
            if(in_array($zhengshuhao,$zhengshuhao_w1))
            {
                $cp_w1_money += $money;
            }elseif(in_array($zhengshuhao,$zhengshuhao_w4))
            {
                $cp_w4_money+=$money;
            }elseif(in_array($xq,$wone) && ($cert == 'EGL' || $cert =='AGL'))
            {
                if(!in_array($zhengshuhao,$zhengshuhao_w1))
                {
                    $cp_w1_num+= $num;
                    $cp_w1_money += $money;
                    if($zhengshuhao !='空')
                    {
                        array_push($zhengshuhao_w1,$zhengshuhao);
                    }
                }
            }elseif(( $xq == '不需工厂镶嵌' ||  $xq == '不需镶嵌')  && ($cert == 'EGL' || $cert =='AGL')){
                $cp_w2_num+=$num;
                $cp_w2_money += $money;
            }elseif($cert !='EGL' && $cert != 'AGL' && $cart >= 0.2)
            {
                //条件三
                if( $gtype =='lz'){
                    $lz_w3_num+= $num;
                    $lz_w3_money += $money; 
                }else{
                    $cp_w3_num+= $num;
                    $cp_w3_money += $money;     
                }
            }elseif($cert !='EGL' && $cert != 'AGL' && $cart < 0.2){
                //条件四
                if(in_array($xq,$wone)){
                    if(!in_array($zhengshuhao,$zhengshuhao_w4))
                    {
                        if($zhengshuhao !='空'){
                            array_push($zhengshuhao_w4,$zhengshuhao);
                        }
                        $cp_w4_num += $num;
                        $cp_w4_money+=$money;
                    }
                }elseif($xq=='不需工厂镶嵌' || $xq=='不需镶嵌'){
                    //条件五
                    /*if($gtype == 'lz')
                    {
                        $lz_w5_num+=$num;
                        $lz_w5_money+=$money;
                    }else{
                                
                    }*/
                    $cp_w5_num += $num;
                    $cp_w5_money += $money;
                }elseif(in_array($xq,$wthree)){
                    //条件六
                    if($gtype !='lz')
                    {
                        $cp_w6_num += $num;
                        $cp_w6_money += $money; 
                    }else{
                        $lz_w6_num += $num;
                        $lz_w6_money += $money; 
                    }   
                }
            }elseif(in_array($xq,$wthree))
            {
                //条件六
                if($gtype !='lz')
                {
                    $cp_w6_num += $num;
                    $cp_w6_money += $money; 
                }else{
                    $lz_w6_num += $num;
                    $lz_w6_money += $money; 
                }
            }
            
            
            //1和4  20分以上
            //2和5  20分一下
            //下面是天生一对的 //和星耀的
            
            
            //判断天生一对的托
            //如果在数组里面了,说明过来的是托  只判断证书号  因为表里面的有些数据证书类型是不对的
            /*if(in_array($zhengshuhao,$zhengshuhao_tsyd))
            {
                if($cart >= 0.2){
                    if(in_array($goods_sn,$alltsydsn))
                    {
                        $tsyd_w1_num += $num;
                        $tsyd_w1_money += $money;
                    }   
                }else{
                    $tsyd_w1_money += $money;
                }
                continue;
            }
            if($cert =='HRD-D')
            {
                if( $cart >= 0.2 )
                {
                    if(!in_array($zhengshuhao,$zhengshuhao_tsyd))
                    {
                        $tsyd_w1_num += $num;
                        $tsyd_w1_money += $money;
                        if($zhengshuhao !='空')
                        {
                            array_push($zhengshuhao_tsyd,$zhengshuhao);
                        }
                    }
                }else{
                    //如果是20分以下
                    if(!in_array($zhengshuhao,$zhengshuhao_tsyd)){
                        $tsyd_w2_num += $num;
                        $tsyd_w2_money += $money;
                        if($zhengshuhao !='空')
                        {
                            array_push($zhengshuhao_tsyd,$zhengshuhao);
                        }
                    }
                }   
            }else{
                //如果是单个的天生一对的托
                if($zhengshuhao == '空' && in_array($goods_sn,$alltsydsn))
                {
                    $tsyd_w3_num += $num;
                    $tsyd_w3_money += $money;   
                }
            }*/
            /*天生一对销售件数=数量1+数量2
            数量1=销售订单商品证书类型为“HRD-D”,商品类型非“lz”非“zp”并且钻石大小大于0的商品数量
            数量2=销售订单商品系列归属为“天生一对”并且钻石大小为0或者空的商品数量
            天生一对销售金额：金额1+金额2
            金额1=销售订单商品证书类型为“HRD-D”并且钻石大小大于0的商品成交价总和
            金额2=销售订单商品系列归属为“天生一对”并且钻石大小为0或者空的商品成交价总和*/
            //天生一对
            if($cert == 'HRD-D' && !in_array($gtype, array('lz','zp')) && $cart >0){
                $tsyd_w1_num += $num;
            }
            if($cert == 'HRD-D' && $cart >0){
                $tsyd_w1_money += $money;
            }

            //天生一对特殊款（不包含证书类型是HRD-D）
            //款号在销售政策管理-销售商品里的“天生一对特殊款”里存在算天生一对销售
            if($cert != 'HRD-D' && $this->checkTsydSpecial($goods_sn)){
                $tsyd_w3_num += $num;
                if($xq == '需工厂镶嵌' && !empty($zhengshuhao)){
                    $tsyd_w3_money += $money;
                    $tsyd_w3_money += $this->getTsydDiaPrice($zhengshuhao);
                }elseif($xq != '需工厂镶嵌'){
                    $tsyd_w3_money += $money;
                }
            }

            //款式归属为天生一对
            if(in_array($goods_sn,$alltsydsn) && $cart<=0 && !$this->checkTsydSpecial($goods_sn)){
                $tsyd_w2_num += $num;
                $tsyd_w2_money += $money;
            }

    
            //星耀统计
            if($cert == 'HRD-S' && $gtype == 'lz')
            {
                $xynum+=$num;
                $xyamount+=$money;
            }
            //总数

        }
        $lznum = $lz_w3_num+$lz_w5_num+$lz_w6_num;
        $lzamount = $lz_w3_money + $lz_w5_money+$lz_w6_money;
        
        
        $flznum = $cp_w1_num+$cp_w2_num+$cp_w3_num+$cp_w4_num+$cp_w5_num+$cp_w6_num;
        $flzamount = $cp_w1_money+$cp_w2_money+$cp_w3_money+$cp_w4_money+$cp_w5_money+$cp_w6_money;
        
        $tsydnum = $tsyd_w1_num+$tsyd_w2_num+$tsyd_w3_num;
        $tsydamount = $tsyd_w1_money+$tsyd_w2_money+$tsyd_w3_money;
        
        $allnum =$lznum+$flznum;
        $allamount = $lzamount+$flzamount;
        
        
        $returndata = array(
            'lsdata'=>array('num'=>$lznum,'amount'=>$lzamount),
            'flsdata'=>array('num'=>$flznum,'amount'=>$flzamount),
            'tsyddata'=>array('num'=>$tsydnum,'amount'=>$tsydamount),
            'xydata'=>array('num'=>$xynum,'amount'=>$xyamount)
        );
        
        //件数占比
        if($allnum > 0 )
        {
            $returndata['lsdata']['allnum'] = sprintf("%.2f",($lznum * 100 / $allnum)) . '%';
            $returndata['flsdata']['allnum'] = sprintf("%.2f",($flznum * 100 /$allnum)) . '%';
        }else{
            $returndata['lsdata']['allnum'] = 0;
            $returndata['flsdata']['allnum'] = 0;
        }
        //金额占比
        if($allamount > 0 )
        {
            $returndata['lsdata']['allmount'] = sprintf("%.2f",($lzamount * 100 / $allamount)) . '%';
            $returndata['flsdata']['allmount'] = sprintf("%.2f",($flzamount * 100 /$allamount)) . '%';
        }else{
            $returndata['lsdata']['allmount'] = 0;
            $returndata['flsdata']['allmount'] = 0;
        }
        return $returndata;
    }

    //根据当期订单明细统计订单总业绩
    //$returng_details 退商品
    //$ng_return_details 获取当期退货，并且之前有退款不退货的记录明细
    //$noreturng_details 退款不退货
    public function differentiateWhetherGold($goodsData,$sendgoodsData,$returng_details,$ng_return_details,$noreturng_details,$alltsydsn)
    {
        //var_dump($goodsData);die;
        //黄金商品总金额
        //非黄金商品总金额
        $gold_data = array(
            'gold_performance'=>0,//黄金业绩不含退款
            'no_gold_performance'=>0,//非黄金业绩不含退款
            'gold_num'=>0,//黄金实际成交客户数
            'no_gold_num'=>0,//非黄金实际成交客户数
            'gold_num_hg'=>0,//黄金实际成交客户数不含换购
            'no_gold_num_hg'=>0,//非黄金实际成交客户数不含换购
            'gold_allsendgoodsmoney'=>0,//黄金当期发货订单商品总金额(回款)
            'no_gold_allsendgoodsmoney'=>0,//非黄金当期发货订单商品总金额(回款)
            'gold_performance_total'=>0,//黄金总业绩
            'no_gold_performance_total'=>0,//非黄金总业绩
            'gold_now_return_goods_total'=>0,//黄金当期实退货商品总金额
            'no_gold_now_return_goods_total'=>0,//非黄金当期实退货商品总金额
            'gold_now_return_real_total'=>0,//黄金当期实退金额（退款不退货）
            'no_gold_now_return_real_total'=>0,//非黄金当期实退金额（退款不退货）
            'no_gold_sale_cp_num'=>0,//非黄金成品销售件数
            'no_gold_num_percentage'=>0,//非黄金件数占比（非黄金件数占比=非黄金成品销售件数/（非黄金成品销售件数+裸石销售件数））
            'gold_order_num'=>0,//黄金订单数量
            'no_gold_order_num'=>0,//非黄金订单数量
            'gold_order_goods_total_price'=>0,//黄金订单商品总金额
            'no_gold_order_goods_total_price'=>0,//非黄金订单商品总金额
            'gold_order_total_price'=>0,//黄金订单总金额
            'no_gold_order_total_price'=>0,//非黄金订单总金额
            'gold_order_sum_paid'=>0,//黄金已付金额
            'no_gold_order_sum_paid'=>0,//非黄金已付金额
            'gold_order_sum_unpaid'=>0,//黄金应收尾款
            'no_gold_order_sum_unpaid'=>0,//非黄金应收尾款
            'gold_order_real_return_price'=>0,//黄金实退金额
            'no_gold_order_real_return_price'=>0,//非黄金实退金额
            );
        $no_gold_goods_info = array();//当期所有非黄金商品信息
        $gold_goods_info = array();//当期所有黄金商品信息
        $gold_allsendgoodsmoney = 0;//黄金发货金额
        $no_gold_allsendgoodsmoney = 0;//非黄金发货金额
        $gold_info_num = array();//黄金成交客户数
        $no_gold_info_num = array();//非黄金成交客户数
        $gold_info_num_hg = array();//黄金成交客户数（不包含换购）
        $no_gold_info_num_hg = array();//非黄金成交客户数（不包含换购）
        $gold = array('普通黄金','定价黄金');
        $gold_order_sn = array();//黄金订单号
        $no_gold_order_sn = array();//非黄金订单号
        if(!empty($goodsData)){
            foreach ($goodsData as $k => $goods) {
                $product_type1 = $goods['product_type1'];
                $product_type_name = $goods['product_type_name'];
                //商品金额
                if($goods['favorable_status']==3)
                {
                    //$money = $goods['goods_count']*( $goods['goods_price']- $goods['favorable_price']);
                    $money = bcmul($goods['goods_count'],bcsub($goods['goods_price'],$goods['favorable_price'],2),2);
                }else{
                    $money = bcmul($goods['goods_count'],$goods['goods_price'],2); 
                }
                //var_dump($goods);die;
                //同一个手机号 同月算一个订单 不含赠品单
                $createtime = substr($goods['pay_date'],0,7);
                //var_dump($createtime);
                $is_hg = bccomp(300,$money,2);
                //var_dump($money,$is_hg);die;
                if(in_array($product_type1, $gold) || in_array($product_type_name, $gold)){
                    $gold_data['gold_performance'] += $money;
                    $gold_order_sn[] = $goods['order_sn'];

                    if($goods['is_zp'] != '1'){
                        $gold_info_num[$createtime][] = $goods['mobile'];
                    }

                    if($goods['is_zp'] != '1' && $is_hg != 1){
                        $gold_info_num_hg[$createtime][] = $goods['mobile'];
                    }
                    //黄金
                    $gold_goods_info[] = $goods;
                }else{
                    $gold_data['no_gold_performance'] += $money;
                    $no_gold_order_sn[] = $goods['order_sn'];

                    if($goods['is_zp'] != '1'){
                        $no_gold_info_num[$createtime][] = $goods['mobile'];
                    }

                    if($goods['is_zp'] != '1' && $is_hg != 1){
                        $no_gold_info_num_hg[$createtime][] = $goods['mobile'];
                    }
                    //非黄金商品
                    $no_gold_goods_info[] = $goods;
                }
            }
            //var_dump($no_gold_info_num);die;
            if(!empty($gold_info_num)){
                foreach ($gold_info_num as $val) {
                    $gold_data['gold_num'] += count(array_flip($val));
                }
            }
            if(!empty($no_gold_info_num)){
                foreach ($no_gold_info_num as $val) {
                    $gold_data['no_gold_num'] += count(array_flip($val));
                }
            }
            if(!empty($gold_info_num_hg)){
                foreach ($gold_info_num_hg as $val) {
                    $gold_data['gold_num_hg'] += count(array_flip($val));
                }
            }
            if(!empty($no_gold_info_num_hg)){
                foreach ($no_gold_info_num_hg as $val) {
                    $gold_data['no_gold_num_hg'] += count(array_flip($val));
                }
            }
        }
        //订单号
        $gold_order_sn = array_unique($gold_order_sn);
        $no_gold_order_sn = array_unique($no_gold_order_sn);
        $gold_order_info = $this->getOrderInfoBySn($gold_order_sn);
        $no_gold_order_info = $this->getOrderInfoBySn($no_gold_order_sn);
        //var_dump($no_gold_order_info);die;
        $gold_data['gold_order_num'] = $gold_order_info['ordersum'];
        $gold_data['no_gold_order_num'] = $no_gold_order_info['ordersum'];
        $gold_data['gold_order_goods_total_price'] = $gold_order_info['ordergoodsamount'];
        $gold_data['no_gold_order_goods_total_price'] = $no_gold_order_info['ordergoodsamount'];
        $gold_data['gold_order_total_price'] = $gold_order_info['orderamount'];
        $gold_data['no_gold_order_total_price'] = $no_gold_order_info['orderamount'];
        $gold_data['gold_order_sum_paid'] = $gold_order_info['money_paid'];
        $gold_data['no_gold_order_sum_paid'] = $no_gold_order_info['money_paid'];
        $gold_data['gold_order_sum_unpaid'] = $gold_order_info['money_unpaid'];
        $gold_data['no_gold_order_sum_unpaid'] = $no_gold_order_info['money_unpaid'];
        $gold_data['gold_order_real_return_price'] = $gold_order_info['real_return_price'];
        $gold_data['no_gold_order_real_return_price'] = $no_gold_order_info['real_return_price'];
        //var_dump($gold_data);die;
        //统计退货金额 分黄金和非黄金
        $allsendgoodsmoney_info = $this->checkGoldType($sendgoodsData,$gold);
        $gold_data['gold_allsendgoodsmoney'] = $allsendgoodsmoney_info['gold_return_price'];//黄金当期发货订单商品总金额(回款)
        $gold_data['no_gold_allsendgoodsmoney'] = $allsendgoodsmoney_info['no_gold_return_price'];//非黄金当期发货订单商品总金额(回款)
        //统计退货金额 分黄金和非黄金
        $return_goods_price = $this->checkGoldType($returng_details,$gold);
        //获取当期退货，并且之前有退款不退货的记录明细 分黄金和非黄金
        $ng_return_goods_price = $this->checkGoldType($ng_return_details,$gold);
        //退款不退货 分黄金和非黄金
        $noreturn_goods_price = $this->checkGoldType($noreturng_details,$gold);
        //黄金退款金额总计
        $return_data_total = bcadd(bcadd($return_goods_price['gold_return_price'],$ng_return_goods_price['gold_return_price'],2),$noreturn_goods_price['gold_return_price'],2);
        //非黄金退款金额总计
        $no_return_data_total = bcadd(bcadd($return_goods_price['no_gold_return_price'],$ng_return_goods_price['no_gold_return_price'],2),$noreturn_goods_price['no_gold_return_price'],2);
        //黄金总业绩
        $gold_data['gold_performance_total'] = bcsub($gold_data['gold_performance'],$return_data_total,2);
        //非黄金总业绩
        $gold_data['no_gold_performance_total'] = bcsub($gold_data['no_gold_performance'],$no_return_data_total,2);
        //黄金当期实退货商品总金额
        $gold_data['gold_now_return_goods_total'] = bcadd($return_goods_price['gold_return_price'],$ng_return_goods_price['gold_return_price'],2);
        //非黄金当期实退货商品总金额
        //var_dump($return_goods_price['no_gold_return_price'],$ng_return_goods_price['no_gold_return_price']);
        $gold_data['no_gold_now_return_goods_total'] = bcadd($return_goods_price['no_gold_return_price'],$ng_return_goods_price['no_gold_return_price'],2);
        //黄金当期实退金额（退款不退货）
        $gold_data['gold_now_return_real_total'] = $noreturn_goods_price['gold_return_price'];
        //非黄金当期实退金额（退款不退货）
        $gold_data['no_gold_now_return_real_total'] = $noreturn_goods_price['no_gold_return_price'];

        //黄金成品销售信息
        $gold_goods_info_num = $this->getpercent($gold_goods_info,$alltsydsn);
        //非黄金成品销售件数
        $no_gold_goods_info_num = $this->getpercent($no_gold_goods_info,$alltsydsn);
        //var_dump($no_gold_goods_info_num);die;
        //非黄金成品销售件数 退款
        //var_dump($return_goods_price['no_gold_goods']);die;
        $no_gold_goods_info_return_num = $this->checklzorflz($return_goods_price['no_gold_goods'],$alltsydsn);
        //var_dump($no_gold_goods_info_return_num);die;
        //var_dump($no_gold_goods_info_num,$no_gold_goods_info_return_num);die;
        //非黄金成品销售件数
        $gold_data['no_gold_sale_cp_num'] = bcsub($no_gold_goods_info_num['flsdata']['num'],$no_gold_goods_info_return_num['flsdata']['num']);
        //非黄金裸石销售件数
        $gold_data['no_gold_sale_ls_num'] = bcsub($no_gold_goods_info_num['lsdata']['num'],$no_gold_goods_info_return_num['lsdata']['num']);

        //非黄金裸石销售金额
        $no_gold_sale_ls_amount = bcsub($no_gold_goods_info_num['lsdata']['amount'],$no_gold_goods_info_return_num['lsdata']['amount'],2);

        //非黄金成品销售金额
        $no_gold_sale_cp_amount = bcsub($no_gold_goods_info_num['flsdata']['amount'],$no_gold_goods_info_return_num['flsdata']['amount'],2);

        //非黄金成品销售件数+裸石销售件数
        $no_gold_cp_ls_sale_num = bcadd($gold_data['no_gold_sale_cp_num'],$gold_data['no_gold_sale_ls_num'],2);

        //非黄金成品件数占比（非黄金件数占比=非黄金成品销售件数/（非黄金成品销售件数+裸石销售件数））
        $gold_data['no_gold_num_percentage'] = round(($gold_data['no_gold_sale_cp_num']/$no_gold_cp_ls_sale_num)*100,2);

        //非黄金裸石件数占比（非黄金件数占比=非黄金成品销售件数/（非黄金成品销售件数+裸石销售件数））
        $gold_data['no_gold_num_ls_percentage'] = round(($gold_data['no_gold_sale_ls_num']/$no_gold_cp_ls_sale_num)*100,2);

        $no_gold_cp_ls_sale_amount = bcadd($no_gold_sale_ls_amount,$no_gold_sale_cp_amount,2);
        //非黄金裸石金额占比（非黄金件数占比=非黄金成品销售金额/（非黄金成品销售金额+裸石销售金额））
        $no_gold_ls_amount_percentage = round(($no_gold_sale_ls_amount/$no_gold_cp_ls_sale_amount)*100,2);

        //非黄金成品金额占比（非黄金件数占比=非黄金成品销售金额/（非黄金成品销售金额+裸石销售金额））
        $no_gold_cp_amount_percentage = round(($no_gold_sale_cp_amount/$no_gold_cp_ls_sale_amount)*100,2);

        if($no_gold_goods_info_num != false){
            //成品 裸石 天生一对 星耀 等数量金额 占比
            $gold_data['no_gold_lsdata_num'] = $gold_data['no_gold_sale_ls_num'];//非黄金 裸石数量
            $gold_data['no_gold_lsdata_amount'] = $no_gold_sale_ls_amount;//非黄金 裸石金额
            $gold_data['no_gold_lsdata_allnum'] = $gold_data['no_gold_num_ls_percentage'];//非黄金 裸石数量占比
            $gold_data['no_gold_lsdata_allmount'] = $no_gold_ls_amount_percentage;//非黄金 裸石金额占比

            $gold_data['no_gold_flsdata_num'] = $gold_data['no_gold_sale_cp_num'];//非黄金 非裸石数量
            $gold_data['no_gold_flsdata_amount'] = bcsub($no_gold_goods_info_num['flsdata']['amount'],$no_gold_goods_info_return_num['flsdata']['amount'],2);//非黄金 非裸石金额
            $gold_data['no_gold_flsdata_allnum'] = $gold_data['no_gold_num_percentage'];//非黄金 非裸石数量占比
            $gold_data['no_gold_flsdata_allmount'] = $no_gold_cp_amount_percentage;//非黄金 非裸石金额占比

            //天生一对
            $gold_data['no_gold_tsyddata_num'] = $no_gold_goods_info_num['tsyddata']['num'];//非黄金 天生一对数量
            $gold_data['no_gold_tsyddata_amount'] = $no_gold_goods_info_num['tsyddata']['amount'];//非黄金 天生一对金额

            //星耀
            $gold_data['no_gold_xydata_num'] = $no_gold_goods_info_num['xydata']['num'];//非黄金 星耀数量
            $gold_data['no_gold_xydata_amount'] = $no_gold_goods_info_num['xydata']['amount'];//非黄金 星耀金额
        }else{
            //成品 裸石 天生一对 星耀 等数量金额 占比
            $gold_data['no_gold_lsdata_num'] = 0;
            $gold_data['no_gold_lsdata_amount'] = 0;
            $gold_data['no_gold_lsdata_allnum'] = 0;
            $gold_data['no_gold_lsdata_allmount'] = 0;
            $gold_data['no_gold_flsdata_num'] = 0;
            $gold_data['no_gold_flsdata_amount'] = 0;
            $gold_data['no_gold_flsdata_allnum'] = 0;
            $gold_data['no_gold_flsdata_allmount'] = 0;
            //天生一对
            $gold_data['no_gold_tsyddata_num'] = 0;
            $gold_data['no_gold_tsyddata_amount'] = 0;
            //星耀
            $gold_data['no_gold_xydata_num'] = 0;
            $gold_data['no_gold_xydata_amount'] = 0;
        }

        if($gold_goods_info_num != false){
            //成品 裸石 天生一对 星耀 等数量金额 占比
            $gold_data['gold_lsdata_num'] = $gold_goods_info_num['lsdata']['num'];//非黄金 裸石数量
            $gold_data['gold_lsdata_amount'] = $gold_goods_info_num['lsdata']['amount'];//非黄金 裸石金额
            $gold_data['gold_lsdata_allnum'] = $gold_goods_info_num['lsdata']['allnum'];//非黄金 裸石数量占比
            $gold_data['gold_lsdata_allmount'] = $gold_goods_info_num['lsdata']['allmount'];//非黄金 裸石金额占比

            $gold_data['gold_flsdata_num'] = $gold_goods_info_num['flsdata']['num'];//非黄金 非裸石数量
            $gold_data['gold_flsdata_amount'] = $gold_goods_info_num['flsdata']['amount'];//非黄金 非裸石金额
            $gold_data['gold_flsdata_allnum'] = $gold_goods_info_num['flsdata']['allnum'];//非黄金 非裸石数量占比
            $gold_data['gold_flsdata_allmount'] = $gold_goods_info_num['flsdata']['allmount'];//非黄金 非裸石金额占比

            //天生一对
            $gold_data['gold_tsyddata_num'] = $gold_goods_info_num['tsyddata']['num'];//非黄金 天生一对数量
            $gold_data['gold_tsyddata_amount'] = $gold_goods_info_num['tsyddata']['amount'];//非黄金 天生一对金额

            //星耀
            $gold_data['gold_xydata_num'] = $gold_goods_info_num['xydata']['num'];//非黄金 星耀数量
            $gold_data['gold_xydata_amount'] = $gold_goods_info_num['xydata']['amount'];//非黄金 星耀金额
        }else{
            //成品 裸石 天生一对 星耀 等数量金额 占比
            $gold_data['gold_lsdata_num'] = 0;
            $gold_data['gold_lsdata_amount'] = 0;
            $gold_data['gold_lsdata_allnum'] = 0;
            $gold_data['gold_lsdata_allmount'] = 0;
            $gold_data['gold_flsdata_num'] = 0;
            $gold_data['gold_flsdata_amount'] = 0;
            $gold_data['gold_flsdata_allnum'] = 0;
            $gold_data['gold_flsdata_allmount'] = 0;
            //天生一对
            $gold_data['gold_tsyddata_num'] = 0;
            $gold_data['gold_tsyddata_amount'] = 0;
            //星耀
            $gold_data['gold_xydata_num'] = 0;
            $gold_data['gold_xydata_amount'] = 0;
        }
        //var_dump($gold_data);
        return $gold_data;
    }

    //区分是否黄金和非黄金 的退款金额
    public function checkGoldType($details_return,$gold)
    {
        $return_data = array();
        $return_data['gold_return_price'] = 0;
        $return_data['no_gold_return_price'] = 0;
        $return_data['no_gold_goods'] = array();//非黄金退款货品
        if(!empty($details_return)){
            foreach ($details_return as $goods) {
                //商品金额
                if($goods['favorable_status']==3)
                {
                    //$money = $goods['goods_count']*( $goods['goods_price']- $goods['favorable_price']);
                    $money = bcmul($goods['goods_count'],bcsub($goods['goods_price'],$goods['favorable_price'],2),2);
                }else{
                    $money = bcmul($goods['goods_count'],$goods['goods_price'],2); 
                }

                if(in_array($goods['product_type1'], $gold) || in_array($goods['product_type_name'], $gold)){
                    $return_data['gold_return_price'] = bcadd($return_data['gold_return_price'],$money,2);
                }else{
                    $return_data['no_gold_return_price'] = bcadd($return_data['no_gold_return_price'],$money,2);
                    $return_data['no_gold_goods'][] = $goods;
                }
            }
        }
        return $return_data;
    }

    //根据订单号，返回订单的基本信息
    public function getOrderInfoBySn($order_sn=array())
    {
        if(empty($order_sn)) return array(
            'ordersum' => 0,
              'orderamount' => 0,
              'ordergoodsamount' => 0,
              'money_paid' => 0,
              'money_unpaid' => 0,
              'real_return_price' => 0);
        $model = new PerformanceReportModel(51);//只读数据库
        //<0>所有的订单信息
        $data= $model->getOrderInfoBySn($order_sn);
        return $data;
    }

    public function getlsflsdata($newdata,$stdata,$dqdata,$kydata)
    {
        /*print_r($newdata);
        echo '<br/>';
        print_r($stdata);
        echo '<br/>';
        print_r($dqdata);
        echo '<br/>';
        print_r($kydata);
        echo '<br/>';
        die();*/
        $lastdata = array();
        //裸石数量
        $lastdata['lsdata']['num'] =  $newdata['lsdata']['num'] - $stdata['lsdata']['num'];
        //裸石金额
        $lastdata['lsdata']['amount'] = round($newdata['lsdata']['amount'] - $stdata['lsdata']['amount'] - $dqdata['lsdata']['amount'] + $kydata['lsdata']['amount']);
        
        //成品数量
        $lastdata['flsdata']['num'] =  $newdata['flsdata']['num'] - $stdata['flsdata']['num'];
        //成品金额
        $lastdata['flsdata']['amount'] =  round($newdata['flsdata']['amount'] - $stdata['flsdata']['amount'] - $dqdata['flsdata']['amount'] + $kydata['flsdata']['amount']);
        
        //天生一对数量
        $lastdata['tsyddata']['num'] =  $newdata['tsyddata']['num'] - $stdata['tsyddata']['num'];
        //天生一对金额
        $lastdata['tsyddata']['amount'] =  round($newdata['tsyddata']['amount'] - $stdata['tsyddata']['amount'] - $dqdata['tsyddata']['amount'] + $kydata['tsyddata']['amount']);
        
        //星耀数量
        $lastdata['xydata']['num'] =  $newdata['xydata']['num'] - $stdata['xydata']['num'];
        //星耀金额
        $lastdata['xydata']['amount'] =  round($newdata['xydata']['amount'] - $stdata['xydata']['amount'] - $dqdata['xydata']['amount'] + $kydata['xydata']['amount']);
        
        $allnum  = $lastdata['lsdata']['num']+$lastdata['flsdata']['num'];
        $allamount = round($lastdata['lsdata']['amount']+$lastdata['flsdata']['amount']);
        
        //件数占比
        if($allnum > 0 )
        {
            $lastdata['lsdata']['allnum'] = sprintf("%.2f",($lastdata['lsdata']['num'] * 100 / $allnum)) . '%';
            $lastdata['flsdata']['allnum'] = sprintf("%.2f",($lastdata['flsdata']['num'] * 100 /$allnum)) . '%';
        }else{
            $lastdata['lsdata']['allnum'] = 0;
            $lastdata['flsdata']['allnum'] = 0;
        }
        //金额占比
        if($allamount > 0 )
        {
            $lastdata['lsdata']['allmount'] = sprintf("%.2f",($lastdata['lsdata']['amount'] * 100 / $allamount)) . '%';
            $lastdata['flsdata']['allmount'] = sprintf("%.2f",($lastdata['flsdata']['amount'] * 100 /$allamount)) . '%';
        }else{
            $lastdata['lsdata']['allmount'] = 0;
            $lastdata['flsdata']['allmount'] = 0;
        }
        return $lastdata;
    }

    //3、    业绩统计
    //天生一对销售件数：款号在销售政策管理-销售商品里的“天生一对特殊款”里存在算天生一对销售（不包含证书类型是HRD-D的数据）
    //天生一对销售金额：
    //①   款号在销售政策管理-销售商品里的“天生一对特殊款”里存在，此款的镶嵌要求如果是“需工厂镶嵌”并且证书号列有值，那么找到此钻石的销售记录，金额=托+钻的金额（不包含证书类型是HRD-D的数据）
    //②   款号在销售政策管理-销售商品里的“天生一对特殊款”里存在，此款的镶嵌要求如果非“需工厂镶嵌”，金额=托的金额（不包含证书类型是HRD-D的数据）
    private function checkTsydSpecial($style_sn)
    {
        $model = new AppPerformanceCountModel(51);//只读数据库
        $res = $model->getTsydSpecial($style_sn);
        return $res;
    }

    //根据证书号查询证书号商品的成交价
    private function getTsydDiaPrice($zhengshuhao){
        if(empty($zhengshuhao) || $zhengshuhao == '空') return 0;
        $model = new AppPerformanceCountModel(51);//只读数据库
        $res = $model->getTsydDiaPrice($zhengshuhao);
        if($res) return $res;
        return 0;
    }
}
