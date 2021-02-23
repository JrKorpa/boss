<?php
/**
 *  -------------------------------------------------
 *   @file		: ShopcountReportController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Liulinyan <939942478@qq.com>
 *   @date		: 2015-09-01 10:15:23
 *   @update	: by gengchao 2016-01-11
 *  -------------------------------------------------
 */
class ShopallcountReportController extends CommonController {
	protected $smartyDebugEnabled = false;
    protected $whitelist = array("downloads", "user_down", "depdown");
	protected $types = array(0=>"全部",-1=>"其他",1=>"异业联盟",2=>"社区",3=>"BDD相关",4=>"团购",5=>"老顾客",6=>"数据",7=>"网络来源");
    public $limit_time = '2019-01-01';
	
	/**
	*	index，搜索框
	*/
	public function index ($params) {
		//获取体验店的信息
		$data = $this->getMyDepartments();
		$this->render('shopallcount_search_form.html',
			array('bar'=>Auth::getBar(), 'allshop'=>$data, 'types'=>$this->types)
		);
	}
	
	public function search($params) {
		$data = $this->_search_data($params);
        $ctinfo = $this->getCustomerSourcesAll();
		$this->render('shopallcount_search_list.html',
			array('data'=>$data['data'], 'rows'=>$data['rows'],'zongji'=>$data['zongji'], 'types'=>$this->types, 'ctinfo'=>$ctinfo)
		);
	}

    //获取所有渠道信息
    public function getCustomerSourcesAll()
    {
        $model = new CustomerSourcesModel(1);
        $data = $model->getCustomerSourcesAllList('`id`,`source_name`');
        return array_column($data, 'source_name', 'id');
    }

	private function _search_data($params=array()) {
		$args = array(
			'mod' => _Request::get("mod"),
			'con' => substr(__CLASS__, 0, -10),
			'act' => __FUNCTION__,
			'department_id' => _Request::get("shop_id"),
			'orderenter' => _Request::getString("orderenter"),
			'fenlei' => _Request::get("fenlei"),
			'begintime' => _Request::get("begintime"),
			'endtime'   => _Request::get("endtime"),
            'shop_type' => _Request::get("shop_type"),
            'make_order' => _Request::getList("salse"),
			'is_delete' => 0
		);
		if(empty($args['begintime']) || empty($args['endtime'])) {
			echo '请选择时间';exit;
		}
		if($args['begintime'] > $args['endtime']) {
			echo '结束时间必须大于开始时间';exit;
		}
        
        if($args['begintime']<$this->limit_time && SYS_SCOPE == 'zhanting'){
            $args['begintime'] = $this->limit_time;
        }

		$days = $this->getDatePeriod($args['begintime'],$args['endtime']);
		if($days>100){
			die('请查询100天范围内的信息!');
		}
		if(empty($args['department_id'])) {
			die('请选择一家体验店吧亲!');
		}

		$shopname = '';//体验店的名称
		$alltyd = $this->getMyDepartments();
		foreach($alltyd as $tydobj) {
			if($tydobj['id'] == $args['department_id']) {
				$shopname = $tydobj['shop_name'];
			}
		}

		//开始拿数据
		$Model = new TydReportModel(59);
        $Modelcount = new TydcountReportModel(59);
        $performanceModel = new PerformanceReportModel(27);
		$where = array();
		$where['bespoke_status'] = 2;  //已经审核的
		if(!empty($args['department_id'])){
			$where['department_id'] = $args['department_id'];
		}
		if(!empty($args['fenlei'])){
			$where['fenlei'] = $args['fenlei'];
		}
        if(!empty($args['make_order'])){
            $where['make_order'] = $args['make_order'];
        }

        //获取两个时间段间的月份
        $moth = $this->getTimeQujian($args['begintime'],$args['endtime']);
        $allbokedata_tmp = array();
        $realbokedata_tmp = array();
        $yindaobokedata_tmp = array();
        $yindaoshidaobokedata_tmp = array();
        $payorderdata_tmp = array();
        $allorderDis_tmp = array();
        $allorderDis_tmp_to = array();
        $allorderDis_tmp_to_num = array();
        foreach ($moth as $end_time => $farst_time) {
            //第一步拿取所有预约的
            $_where = $where;
            $_where['create_time_start'] = $farst_time;
            $_where['create_time_end'] = $end_time;
            $allboke = $Model->getbokecount($_where);
            $allbokedata_tmp[] = array_column($allboke, 'count', 'customer_source_id');

            //第二部拿取 实际到店数
            $_where = $where;
            $_where['real_inshop_time_start'] = $farst_time;
            $_where['real_inshop_time_end'] = $end_time;
            $_where['re_status'] = 1;  //到店状态
            $realboke = $Model->getbokecount($_where);
            $realbokedata_tmp[] = array_column($realboke, 'count', 'customer_source_id');

            //第三部拿取 预约当期应到数
            $_where = $where;
            $_where['bespoke_inshop_time_start'] = $farst_time;
            $_where['bespoke_inshop_time_end'] = $end_time;
            $yindaoboke =  $Model->getbokecount($_where);
            $yindaobokedata_tmp[] = array_column($yindaoboke, 'count', 'customer_source_id');

            //第四部拿取 当期应到预约的实到数
            $_where = $where;
            $_where['bespoke_inshop_time_start'] = $farst_time;
            $_where['bespoke_inshop_time_end'] = $end_time;
            $_where['re_status'] = 1;  //到店状态
            $yindaoshidaoboke =  $Model->getbokecount($_where);
            $yindaoshidaobokedata_tmp[] = array_column($yindaoshidaoboke, 'count', 'customer_source_id');

            //第五步取到店成交的预约单数
            $_where = $where;
            $_where['real_inshop_time_start'] = $args['begintime'];
            $_where['real_inshop_time_end'] = $args['endtime'];
            $_where['re_status'] = 1;  //到店状态
            $_where['deal_status'] = 1;
            $payorder =  $Model->getbokecount($_where,true);
            $payorderdata_tmp[] = array_column($payorder, 'count', 'customer_source_id');

            //实际成交客户数
            $where = array();
            $where['shop_type'] = $args['shop_type'];
            $where['department_id'] = $args['department_id'];
            $where['orderenter'] = $args['orderenter'];
            $where['begintime'] = $farst_time;
            $where['endtime'] = $end_time;
            $where['is_delete'] = $args['is_delete'];
            $where['make_order'] = $args['make_order'];
            $allorderDis_tmp[]=$Modelcount->getOrderInfoDiscs($where);

            //实际成交客户数 不含300以下的
            $where = array();
            $where['shop_type'] = $args['shop_type'];
            $where['department_id'] = $args['department_id'];
            $where['orderenter'] = $args['orderenter'];
            $where['begintime'] = $farst_time;
            $where['endtime'] = $end_time;
            $where['is_delete'] = $args['is_delete'];
            $where['make_order'] = $args['make_order'];
            $allorderDis_tmp_to[]=$Modelcount->getOrderInfoDisTocs($where);

            //实际成交客户数 300以下的
            $where = array();
            $where['shop_type'] = $args['shop_type'];
            $where['department_id'] = $args['department_id'];
            $where['orderenter'] = $args['orderenter'];
            $where['begintime'] = $farst_time;
            $where['endtime'] = $end_time;
            $where['is_delete'] = $args['is_delete'];
            $where['make_order'] = $args['make_order'];
            $allorderDis_tmp_to_num[]=$Modelcount->getOrderInfoDisTocsNum($where);
        }
		$allbokedata = $this->cekScut($allbokedata_tmp);
        $realbokedata = $this->cekScut($realbokedata_tmp);
        $yindaobokedata = $this->cekScut($yindaobokedata_tmp);
        $yindaoshidaobokedata = $this->cekScut($yindaoshidaobokedata_tmp);
        $payorderdata = $this->cekScut($payorderdata_tmp);
        $allorderDis = $this->mergeNumDist($allorderDis_tmp);
        $allorderDisTo = $this->mergeNumDist($allorderDis_tmp_to);
        $allorderDisToNum = $this->mergeNumDist($allorderDis_tmp_to_num);
        $allboke_keys = array_keys($allbokedata);
        $realboke_keys = array_keys($realbokedata);
        $yindaoboke_keys = array_keys($yindaobokedata);
        $yindaoshidaoboke_keys = array_keys($yindaoshidaobokedata);
        $payorder_keys = array_keys($payorderdata);
		//第六步拿取所有的订单信息
		// ordernum,zpnum,orderamount,goodsamount,moneypaid,realreturnprice,moneyunpaid,  customer_source_id,source_name,fenlei
		$allorder = $Model->getordercount($args);

        $where = array();
        $where['shop_type'] = $args['shop_type']; 
        $where['start_time'] = $args['begintime'];
        $where['end_time'] = $args['endtime'];
        $where['fenlei'] = $args['fenlei'];
        $where['make_order'] = $args['make_order'];
        if($args['orderenter']){
            if($args['orderenter'] == '婚博会'){
                $where['referer'] = 1;
            }else{
                $where['referer'] = 2;
            }
        }
        if(!empty($args['department_id'])){
            $where['department_id'] = $args['department_id'];
        }
        $rg_info = $performanceModel->getRetrunGoodsAmountGs($where);
        $rp_info = $performanceModel->getReturnPriceGs($where);
        $rg_info = array_column($rg_info,'customer_source_id');
        $rp_info = array_column($rp_info,'customer_source_id');
		// 预约：allbokenum realbokenum chengjiaobokenum yindaobokenum yindaoshidaobokenum
		// 订单：source_name，ordernum，zpnum，orderamount，moneypaid，moneyunpaid
		/*$sourcedata = array();
		foreach ($allorder as &$item) {
			$fenlei_id = $item['fenlei'];
			$source_id = $item['customer_source_id'];
			$item['allbokenum'] = isset($allbokedata[$source_id]) ? $allbokedata[$source_id] : 0;
			$item['realbokenum'] = isset($realbokedata[$source_id]) ? $realbokedata[$source_id] : 0;
			$item['chengjiaobokenum'] = isset($payorderdata[$source_id]) ? $payorderdata[$source_id] : 0;
			$item['yindaobokenum'] = isset($yindaobokedata[$source_id]) ? $yindaobokedata[$source_id] : 0;
			$item['yindaoshidaobokenum'] = isset($yindaoshidaobokedata[$source_id]) ? $yindaoshidaobokedata[$source_id] : 0;

			$item['daodianlv'] = empty($item['yindaobokenum']) ? 0 : round($item['yindaoshidaobokenum']/$item['yindaobokenum'], 4)*100;
			$item['chengjiaolv'] = empty($item['realbokenum']) ? 0 : round($item['chengjiaobokenum']/$item['realbokenum'], 4)*100;
			$item['kedanjia'] = empty($item['chengjiaobokenum']) ? 0 : round($item['orderamount']/$item['chengjiaobokenum'], 4);

			$sourcedata[$fenlei_id][] = $item;
		}
        //echo '<pre>';
        //print_r($sourcedata);die;
		//汇总统计
		foreach($sourcedata as $fenlie_id=>&$feiliedata) {
			$tongjidata = array(
					'fenlei'=>$fenlie_id,
					'source_name'=>$shopname.$this->types[$fenlie_id].'(总计)',
					'allbokenum'=>0,
					'realbokenum'=>0,
					'chengjiaobokenum'=>0,
					'yindaobokenum'=>0,
					'yindaoshidaobokenum'=>0,
					'ordernum'=>0,
					'zpnum'=>0,
					'orderamount'=>0,
					'moneypaid'=>0,
					'moneyunpaid'=>0
				);
			foreach ($feiliedata as $source_id=>$item) {
				$tongjidata['allbokenum'] += $item['allbokenum'];
				$tongjidata['realbokenum'] += $item['realbokenum'];
				$tongjidata['chengjiaobokenum'] += $item['chengjiaobokenum'];
				$tongjidata['yindaobokenum'] += $item['yindaobokenum'];
				$tongjidata['yindaoshidaobokenum'] += $item['yindaoshidaobokenum'];
				$tongjidata['ordernum'] += $item['ordernum'];
				$tongjidata['zpnum'] += $item['zpnum'];
				$tongjidata['orderamount'] += $item['orderamount'];
				$tongjidata['moneypaid'] += $item['moneypaid'];
				$tongjidata['moneyunpaid'] += $item['moneyunpaid'];
			}
			$tongjidata['daodianlv'] = empty($tongjidata['yindaobokenum']) ? 0 : round($tongjidata['yindaoshidaobokenum']/$tongjidata['yindaobokenum'], 4)*100;
			$tongjidata['chengjiaolv'] = empty($tongjidata['realbokenum']) ? 0 : round($tongjidata['chengjiaobokenum']/$tongjidata['realbokenum'], 4)*100;
			$tongjidata['kedanjia'] = empty($tongjidata['chengjiaobokenum']) ? 0 : round($tongjidata['orderamount']/$tongjidata['chengjiaobokenum'], 4);

			$feiliedata[] = $tongjidata;
		}

		if(empty($sourcedata)) {
			$rows = 0;
		}else{
			$rows = count($sourcedata);
		}*/

        // -----------------------------------------------------------逻辑重新修改开始
        $orderInfo = array();
        if(!empty($allorder)){//订单归属渠道
            foreach ($allorder as $key => $value) {
                $orderInfo[$value['customer_source_id']] = $value;
            }
        }
        $datainfo_keys = array_keys($orderInfo);
        //获取查询后的所有渠道
        $courceall = array();
        $courceall = array_merge($allboke_keys,$realboke_keys,$yindaoboke_keys,$yindaoshidaoboke_keys,$payorder_keys,$datainfo_keys,$rg_info,$rp_info);//合并所有有用渠道
        $courceAlls = array_keys(array_flip($courceall));//维度
        //将所获渠道分类
        $courceAllInfo = array();
        if($courceAlls){
            foreach ($courceAlls as $cource_id) {
                $tp_id = $Model->getFenLeiList($cource_id);
                if($args['fenlei'] != '' && $tp_id != $args['fenlei']){
                    continue;
                }
                $courceAllInfo[$tp_id][] = $cource_id;
            }
        }
        //显示全部渠道的来源信息----------
        /*$csInfo = array();//该渠道下所有来源
        $csInfo = $Model->getCustomerSourcesList($where);

        $courceAllInfo = array();
        if(!empty($csInfo)){
            foreach ($csInfo as $value) {
                $courceAllInfo[$value['fenlei']][] = $value['id'];
            }
        }*/
        //--------------------------------
        $zongji = array();
        
        //统一至渠道
        $num = 0;
        $dataInfo = array();
        foreach ($courceAllInfo as $fenlei_id => $_info) {
            foreach ($_info as $source_id) {
                if(isset($orderInfo[$source_id]) && !empty($orderInfo[$source_id])){
                    $data = $orderInfo[$source_id];
                }else{
                    $data = array(
                                'customer_source_id' => $source_id,
                                'source_name' => '',
                                'fenlei' => $fenlei_id,
                                'ordernum' => 0,
                                'zpnum' => 0,
                                'orderamount' => 0.00,
                                'goodsamount' => 0.00,
                                'moneypaid' => 0.00,
                                'realreturnprice' => 0.00,
                                'moneyunpaid' => 0.00,
                                'source_own_id' => 0,
                                'source_own' => ''
                            );
                }
                $where = array();
                $where['shop_type'] = $args['shop_type']; 
                $where['start_time'] = $args['begintime'];
                $where['end_time'] = $args['endtime'];
                $where['fenlei'] = $args['fenlei'];
                $where['make_order'] = $args['make_order'];
                if($args['orderenter']){
                    if($args['orderenter'] == '婚博会'){
                        $where['referer'] = 1;
                    }else{
                        $where['referer'] = 2;
                    }
                }
                if(!empty($args['department_id'])){
                    $where['department_id'] = $args['department_id'];
                }
                $where['from_ad'] = $source_id;
                $where['salseids'] = $args['make_order'];
                $where['salse'] = $args['make_order'];
                $rg = $performanceModel->getRetrunGoodsAmountA($where);
                $rp = $performanceModel->getReturnPriceA($where);
                $returngoods_orderids = $performanceModel->getRetrunGoodsOrderid($where);
                $plus_returnprice = 0;
                if(!empty($returngoods_orderids))
                {
                    $oids = array_column($returngoods_orderids,'order_goods_id');
                    //+跨月的(退款不退货的)总金额
                    $plus_returnprice = $performanceModel->getReturnPriceA($where,$oids);
                }

                $where['is_tree'] = 1;
                $rgs = $performanceModel->getRetrunGoodsAmountA($where);
                $rps = $performanceModel->getReturnPriceHG($where);
                $returngoods_orderidss = $performanceModel->getRetrunGoodsOrderidHG($where);
                $plus_returnprices = 0;
                if(!empty($returngoods_orderidss))
                {
                    $oids = array_column($returngoods_orderidss,'order_goods_id');
                    //+跨月的(退款不退货的)总金额
                    $plus_returnprices = $performanceModel->getReturnPriceHG($where,$oids);
                }
                $data['zongyeji'] = $data['goodsamount'] - $rg -$rp + $plus_returnprice;
                //去除低于300元换购的
                $where = array();
                $where['department_id'] = $args['department_id'];
                $where['orderenter'] = $args['orderenter'];
                $where['begintime'] = $args['begintime'];
                $where['endtime'] = $args['endtime'];
                $where['make_order'] = $args['make_order'];
                $where['from_ad'] = $source_id;
                //不含换购金额去除单件商品在300元以下的商品
                $price_hg = $Modelcount->getOrderInfoHG($where);
                $huangouprice = $price_hg - $rgs - $rps + $plus_returnprices; //不包含换购金额
                //赠品客户数
                $num = 0;
                $where_zp  =array();
                $where_zp['department_id'] = $args['department_id'];
                $where_zp['shop_type'] = $args['shop_type'];
                $where_zp['orderenter'] = $args['orderenter'];
                $where_zp['is_delete'] = $args['is_delete'];
                $where_zp['make_order'] = $args['make_order'];
                $where_zp['from_ad'] = $source_id;
                foreach ($moth as $end_time => $star_time) {
                    $where_zp['begintime'] = $star_time;
                    $where_zp['endtime'] = $end_time;
                    $num+= $Model->getIsZpNum($where_zp);
                }
                $data['zpnum'] = $num;
                $data['allbokenum'] = isset($allbokedata[$source_id]) ? $allbokedata[$source_id] : 0;
                $data['realbokenum'] = isset($realbokedata[$source_id]) ? $realbokedata[$source_id] : 0;
                $data['chengjiaobokenum'] = isset($payorderdata[$source_id]) ? $payorderdata[$source_id] : 0;
                $data['yindaobokenum'] = isset($yindaobokedata[$source_id]) ? $yindaobokedata[$source_id] : 0;
                $data['yindaoshidaobokenum'] = isset($yindaoshidaobokedata[$source_id]) ? $yindaoshidaobokedata[$source_id] : 0;
                $data['dis_borcs'] = isset($allorderDis[$source_id]) ? $allorderDis[$source_id] : 0;
                $data['dis_borcs_to'] = isset($allorderDisTo[$source_id]) ? $allorderDisTo[$source_id] : 0;
                $data['dis_borcs_to_num'] = isset($allorderDisToNum[$source_id]) ? $allorderDisToNum[$source_id] : 0;
                $data['zongyeji_hg'] = $huangouprice;
                $data['daodianlv'] = empty($data['yindaoshidaobokenum']) ? 0 : round($data['yindaoshidaobokenum']/$data['yindaobokenum'], 4)*100;
                $data['chengjiaolv']=empty($data['realbokenum']) ? 0 : round($data['dis_borcs_to']/$data['realbokenum'], 4)*100;
                $data['kedanjia']=empty($data['dis_borcs_to']+$data['dis_borcs_to_num']) ? 0 : round($data['zongyeji']/($data['dis_borcs_to']+$data['dis_borcs_to_num']),4);
                $data['kedanjia_hg']=empty($data['dis_borcs_to']) ? 0 : round($data['zongyeji_hg']/$data['dis_borcs_to'],4);
                $dataInfo[$fenlei_id][] = $data;
            }
            //汇总统计
            foreach($dataInfo as $fenlie_id => $datalist) {
                $tongjidata = array(
                                'fenlei'=>$fenlie_id,
                                'source_name'=>$shopname.$this->types[$fenlie_id].'(总计)',
                                'allbokenum'=>0,
                                'realbokenum'=>0,
                                'chengjiaobokenum'=>0,
                                'yindaobokenum'=>0,
                                'yindaoshidaobokenum'=>0,
                                'ordernum'=>0,
                                'zpnum'=>0,
                                'orderamount'=>0,
                                'moneypaid'=>0,
                                'moneyunpaid'=>0,
                                'zongyeji'=>0,
                                'dis_borcs_to'=>0,
                                'dis_borcs_to_num'=>0,
                                'dis_borcs'=>0,
                                'zongyeji_hg'=>0
                            );

                foreach ($datalist as $info_c) {
                    $tongjidata['allbokenum'] += $info_c['allbokenum'];
                    $tongjidata['realbokenum'] += $info_c['realbokenum'];
                    $tongjidata['chengjiaobokenum'] += $info_c['chengjiaobokenum'];
                    $tongjidata['yindaobokenum'] += $info_c['yindaobokenum'];
                    $tongjidata['yindaoshidaobokenum'] += $info_c['yindaoshidaobokenum'];
                    $tongjidata['ordernum'] += $info_c['ordernum'];
                    $tongjidata['zpnum'] += $info_c['zpnum'];
                    $tongjidata['orderamount'] += $info_c['orderamount'];
                    $tongjidata['moneypaid'] += $info_c['moneypaid'];
                    $tongjidata['moneyunpaid'] += $info_c['moneyunpaid'];
                    $tongjidata['zongyeji'] += $info_c['zongyeji'];
                    $tongjidata['zongyeji_hg'] += $info_c['zongyeji_hg'];
                    $tongjidata['dis_borcs'] += $info_c['dis_borcs'];
                    $tongjidata['dis_borcs_to'] += $info_c['dis_borcs_to'];
                    $tongjidata['dis_borcs_to_num'] += $info_c['dis_borcs_to_num'];
                }
                $tongjidata['daodianlv'] = empty($tongjidata['yindaobokenum']) ? 0 : round($tongjidata['yindaoshidaobokenum']/$tongjidata['yindaobokenum'], 4)*100;
                $tongjidata['chengjiaolv'] = empty($tongjidata['realbokenum']) ? 0 : round($tongjidata['dis_borcs_to']/$tongjidata['realbokenum'], 4)*100;
                $tongjidata['kedanjia'] = empty($tongjidata['dis_borcs_to']+$tongjidata['dis_borcs_to_num']) ? 0 : round($tongjidata['zongyeji']/($tongjidata['dis_borcs_to']+$tongjidata['dis_borcs_to_num']), 4);
                $tongjidata['kedanjia_hg'] = empty($tongjidata['dis_borcs_to']) ? 0 : round($tongjidata['zongyeji_hg']/$tongjidata['dis_borcs_to'], 4);
            }
            $dataInfo[$fenlie_id][] = $tongjidata;
            $zongji['z_allbokenum'] +=$tongjidata['allbokenum'];
            $zongji['z_realbokenum'] +=$tongjidata['realbokenum'];
            $zongji['z_chengjiaobokenum'] +=$tongjidata['chengjiaobokenum'];
            $zongji['z_yindaobokenum'] +=$tongjidata['yindaobokenum'];
            $zongji['z_yindaoshidaobokenum'] +=$tongjidata['yindaoshidaobokenum'];
            $zongji['z_ordernum'] +=$tongjidata['ordernum'];
            $zongji['z_zpnum'] +=$tongjidata['zpnum'];
            $zongji['z_orderamount'] +=$tongjidata['orderamount'];
            $zongji['z_zongyeji'] +=$tongjidata['zongyeji'];
            $zongji['z_zongyeji_hg'] +=$tongjidata['zongyeji_hg'];
            $zongji['z_moneypaid'] +=$tongjidata['moneypaid'];
            $zongji['z_moneyunpaid'] +=$tongjidata['moneyunpaid'];
            $zongji['z_dis_borcs'] +=$tongjidata['dis_borcs'];
            $zongji['z_dis_borcs_to'] +=$tongjidata['dis_borcs_to'];
            $zongji['z_dis_borcs_to_num'] +=$tongjidata['dis_borcs_to_num'];
            $zongji['z_daodianlv'] = empty($zongji['z_yindaobokenum']) ? 0 : round($zongji['z_yindaoshidaobokenum']/$zongji['z_yindaobokenum'], 4)*100;
            $zongji['z_chengjiaolv'] = empty($zongji['z_realbokenum']) ? 0 : round($zongji['z_dis_borcs_to']/$zongji['z_realbokenum'], 4)*100;
            $zongji['z_kedanjia'] = empty($zongji['z_dis_borcs_to']+$zongji['z_dis_borcs_to_num']) ? 0 : round($zongji['z_zongyeji']/($zongji['z_dis_borcs_to']+$zongji['z_dis_borcs_to_num']), 4);
            $zongji['z_kedanjia_hg'] = empty($zongji['z_dis_borcs_to']) ? 0 : round($zongji['z_zongyeji_hg']/$zongji['z_dis_borcs_to'], 4);
        }
        // -----------------------------------------------------------结束*/
        if(empty($dataInfo)) {
            $rows = 0;
        }else{
            $rows = count($dataInfo);
        }

		return array('data'=>$dataInfo, 'rows'=>$rows, 'zongji'=>$zongji);
	}

    //导出
	public function downloads($params) {
		$_data = $this->_search_data($params);
		$data = $_data['data'];
		$rows = $_data['rows'];
        $zongji = $_data['zongji'];
		$types = $this->types;
        $ctinfo = $this->getCustomerSourcesAll();

        ini_set('memory_limit','-1');
        set_time_limit(0);
        header("Content-Type: text/html; charset=gb2312");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=".iconv('utf-8','gb2312',"各渠道统计报表").".xls");

        $csv_body="<table border='1'><tr><td></td><td></td><td colspan='9'>进店人数管理</td><td colspan='4'>进店销售额管理</td></tr>";
        $csv_body.="<tr><td>来源分类</td><td>客户来源</td><td>新增预约人数</td><td>实际到店人数</td><td>预约预计到店人数</td><td>预约实际到店人数</td><td>预约到店率</td><td>实际成单数</td><td>实际成交客户数（不含换购）</td><td>换购人数</td><td>成交率</td><td>赠品客户数</td><td>总业绩</td><td>总业绩（不含换购）</td><td>客单价</td><td>客单价（不含换购）</td></tr>";
		if ($rows>=1) {
			$res = $data;
            foreach ($res as $k => $v ) {
                foreach($v as $key=>$val){
                    $customer_name = isset($ctinfo[$val['customer_source_id']]) ? $ctinfo[$val['customer_source_id']] : $val['source_name'];
                    $csv_body.="<tr>";
                    if($key<1){
                        $csv_body.="<td style='vertical-align:middle' rowspan='".count($v)."'>".$types[$k]."</td>";
                    }
                    $csv_body.="<td>".$customer_name."</td><td>".$val['allbokenum']."</td><td>".$val['realbokenum']."</td><td>".
						$val['yindaoshidaobokenum']."</td><td>".$val['yindaobokenum']."</td><td>".$val['daodianlv']."%</td><td>".
						$val['ordernum']."</td><td>".$val['dis_borcs_to']."</td><td>".$val['dis_borcs_to_num']."</td><td>".$val['chengjiaolv']."%</td><td>".
						$val['zpnum']."</td><td>".$val['zongyeji']."</td><td>".$val['zongyeji_hg']."</td><td>".
						$val['kedanjia']."</td><td>".$val['kedanjia_hg']."</td>";
                    $csv_body.="</tr>";
                }
            }
            $csv_body.="<tr>";
            $csv_body.="<td style='vertical-align:middle'></td>";
            $csv_body.="<td>总计：</td><td>".$zongji['z_allbokenum']."</td><td>".$zongji['z_realbokenum']."</td><td>".
                $zongji['z_yindaoshidaobokenum']."</td><td>".$zongji['z_yindaobokenum']."</td><td>".$zongji['z_daodianlv']."%</td><td>".
                $zongji['z_ordernum']."</td><td>".$zongji['z_dis_borcs_to']."</td><td>".$zongji['z_dis_borcs_to_num']."</td><td>".$zongji['z_chengjiaolv']."%</td><td>".
                $zongji['z_zpnum']."</td><td>".$zongji['z_zongyeji']."</td><td>".$zongji['z_zongyeji_hg']."</td><td>".
                $zongji['z_kedanjia']."</td><td>".$zongji['z_kedanjia_hg']."</td>";
            $csv_body.="</tr>";
            $csv_footer="</table>";
            echo $csv_body.$csv_footer;
		} else {
			echo '没有数据！';
		}
	}

    //整理导出数据2
    public function _user_search_data($params=array())
    {
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'department_id' => _Request::get("shop_id"),
            'orderenter' => _Request::getString("orderenter"),
            'fenlei' => _Request::get("fenlei"),
            'salse' => _Request::getString("salse"),
            'begintime' => _Request::get("begintime"),
            'endtime'   => _Request::get("endtime"),
            'is_delete' => 0
        );
        if(empty($args['begintime']) || empty($args['endtime'])) {
            echo '请选择时间';exit;
        }
        if($args['begintime'] > $args['endtime']) {
            echo '结束时间必须大于开始时间';exit;
        }

        if($args['begintime']<$this->limit_time && SYS_SCOPE == 'zhanting'){
            $args['begintime'] = $this->limit_time;
        }

        $days = $this->getDatePeriod($args['begintime'],$args['endtime']);
        if($days>100){
            die('请查询100天范围内的信息!');
        }
        if(empty($args['department_id'])) {
            die('请选择一家体验店吧亲!');
        }

        $shopname = '';//体验店的名称
        $alltyd = $this->getMyDepartments();
        foreach($alltyd as $tydobj) {
            if($tydobj['id'] == $args['department_id']) {
                $shopname = $tydobj['shop_name'];
            }
        }

        //开始拿数据
        /*begin*/
        $Model = new TydReportModel(59);
        $Modelcount = new TydcountReportModel(59);
        $where = array();
        $where['bespoke_status'] = 2;  //已经审核的
        if(!empty($args['department_id'])){
            $where['department_id'] = $args['department_id'];
        }
        if(!empty($args['fenlei'])){
            $where['fenlei'] = $args['fenlei'];
        }

        //取出所选店的所以销售顾问
        $userall = $this->getDepUserInfoByDepId($args['department_id']);
        //$userall = array('孙禹烔','郑小丽','刘单雄');
        //$userall = array('郑小丽');
        $cus_key = array();//归属渠道
        //第一步拿取所有预约的
        $_where = $where;
        $_where['create_time_start'] = $args['begintime'];
        $_where['create_time_end'] = $args['endtime'];
        $allbokedata = array();
        if($userall){
            foreach ($userall as $user_name) {
                $_where['make_order'] = $user_name;
                $allboke = $Model->getbokecount($_where);
                $allbokedata[$user_name] = array_column($allboke, 'count', 'customer_source_id');
            }
        }
        foreach ($allbokedata as $uname => $customer) {
            $cus_key[$uname]['allboke_keys'] = array_keys($customer);
        }

        //第二部拿取 实际到店数
        $_where = $where;
        $_where['real_inshop_time_start'] = $args['begintime'];
        $_where['real_inshop_time_end'] = $args['endtime'];
        $_where['re_status'] = 1;  //到店状态
        $realbokedata = array();
        if($userall){
            foreach ($userall as $user_name) {
                $_where['make_order'] = $user_name;
                $realboke = $Model->getbokecount($_where);
                $realbokedata[$user_name] = array_column($realboke, 'count', 'customer_source_id');
            }
        }
        foreach ($realbokedata as $uname => $customer) {
            $cus_key[$uname]['realboke_keys'] = array_keys($customer);
        }

        //第三部拿取 预约当期应到数
        $_where = $where;
        $_where['bespoke_inshop_time_start'] = $args['begintime'];
        $_where['bespoke_inshop_time_end'] = $args['endtime'];
        $yindaobokedata = array();
        if($userall){
            foreach ($userall as $user_name) {
                $_where['make_order'] = $user_name;
                $yindaoboke = $Model->getbokecount($_where);
                $yindaobokedata[$user_name] = array_column($yindaoboke, 'count', 'customer_source_id');
            }
        }
        foreach ($yindaobokedata as $uname => $customer) {
            $cus_key[$uname]['yindaoboke_keys'] = array_keys($customer);
        }

        //第四部拿取 当期应到预约的实到数
        $_where = $where;
        $_where['bespoke_inshop_time_start'] = $args['begintime'];
        $_where['bespoke_inshop_time_end'] = $args['endtime'];
        $_where['re_status'] = 1;  //到店状态
        $yindaoshidaobokedata = array();
        if($userall){
            foreach ($userall as $user_name) {
                $_where['make_order'] = $user_name;
                $yindaoshidaoboke = $Model->getbokecount($_where);
                $yindaoshidaobokedata[$user_name] = array_column($yindaoshidaoboke, 'count', 'customer_source_id');
            }
        }
        foreach ($yindaoshidaobokedata as $uname => $customer) {
            $cus_key[$uname]['yindaoshidaoboke_keys'] = array_keys($customer);
        }

        //第五步取到店成交的预约单数
        $_where = $where;
        $_where['real_inshop_time_start'] = $args['begintime'];
        $_where['real_inshop_time_end'] = $args['endtime'];
        $_where['re_status'] = 1;  //到店状态
        $_where['deal_status'] = 1;
        $payorderdata = array();
        if($userall){
            foreach ($userall as $user_name) {
                $_where['make_order'] = $user_name;
                $payorder = $Model->getbokecount($_where);
                $payorderdata[$user_name] = array_column($payorder, 'count', 'customer_source_id');
            }
        }
        foreach ($payorderdata as $uname => $customer) {
            $cus_key[$uname]['payorder_keys'] = array_keys($customer);
        }

        $orderInfo = array();
        //第六步取到所有订单
        foreach ($userall as $user_name) {
            # code...
            $args['create_user'] = $user_name;
            $insfts = $Model->getordercounts($args);
            $dataInfo = array();
            if(!empty($insfts)){
                foreach ($insfts as $key => $value) {
                    # code...
                    $dataInfo[$value['customer_source_id']] = $value;
                }
            }
            $orderInfo[$user_name] = $dataInfo;
        }

        foreach ($orderInfo as $uname => $customer) {
            $cus_key[$uname]['insfts_keys'] = array_keys($customer);
        }
        $cAll = array();
        $allSalse = array();
        if($args['salse']){
            $allSalse = explode(",", $args['salse']);
        }
        //合并所有顾问下所属渠道
        foreach ($cus_key as $uname => $t) {
            //如果选择了销售顾问，则筛选
            if(!empty($allSalse) && !in_array($uname, $allSalse)){
                continue;
            }
            $cus_info = array_merge($t['allboke_keys'],$t['realboke_keys'],$t['yindaoboke_keys'],$t['yindaoshidaoboke_keys'],$t['payorder_keys'],$t['insfts_keys']);
            $cAll[$uname] = array_keys(array_flip($cus_info));//维度
        }
        //显示全部预约来源-----------------------------
        /*$csInfo = array();//该渠道下所有来源
        $csInfo = $Model->getCustomerSourcesList($where);

        $courceAllInfo = array();
        if(!empty($csInfo)){
            foreach ($userall as $user_name) {
                foreach ($csInfo as $value) {
                    $courceAllInfo[$user_name][$value['fenlei']][] = $value['id'];
                }
            }
        }*/
        //---------------------------------------------
        
        //实际成交客户数
        $wheres = array();
        $allorsderDis_tmp = array();
        $allorderDis_tmp_to = array();
        $allorderDis_tmp_to_num = array();
        $wheres['shop_type'] = $args['shop_type'];
        $wheres['department_id'] = $args['department_id'];
        $wheres['orderenter'] = $args['orderenter'];
        $wheres['begintime'] = $args['begintime'];
        $wheres['endtime'] = $args['endtime'];
        $wheres['is_delete'] = $args['is_delete'];
        foreach ($userall as $key => $uname) {
            $wheres['make_order'] = $uname;
            $allorsderDis_tmp[$uname][] =$Modelcount->getOrderInfoDiscs($wheres);
            //实际s成交客户数 不含300以下的
            $allorderDis_tmp_to[$uname][]=$Modelcount->getOrderInfoDisTocs($wheres);
            $allorderDis_tmp_to_num[$uname][]=$Modelcount->getOrderInfoDisTocsNum($wheres);
        }
        $allorsderDis = $this->mergeNumDis($allorsderDis_tmp);
        $allorsderDisTo = $this->mergeNumDis($allorderDis_tmp_to);
        $allorsderDisToNum = $this->mergeNumDis($allorderDis_tmp_to_num);
        
        
        $courceInfo = array();

        //归属到所属分类
        foreach ($cAll as $uname => $vin) {
            foreach ($vin as $cource_id) {
                $tp_id = $Model->getFenLeiList($cource_id);
                $courceInfo[$uname][$tp_id][] = $cource_id;
            }
        }
        //echo '<pre>';
        //print_r($courceInfo);die;
        
        $performanceModel = new PerformanceReportModel(27);
        // 预约：allbokenum realbokenum chengjiaobokenum yindaobokenum yindaoshidaobokenum
        // 订单：source_name，ordernum，zpnum，orderamount，moneypaid，moneyunpaid
        $sourcedata = array();

        $where = array();
        $where['shop_type'] = $args['shop_type']; 
        $where['start_time'] = $args['begintime'];
        $where['end_time'] = $args['endtime'];
        $where['fenlei'] = $args['fenlei'];
        if($args['orderenter']){
            if($args['orderenter'] == '婚博会'){
                $where['referer'] = 1;
            }else{
                $where['referer'] = 2;
            }
        }
        //获取两个时间段间的月份
        $moth = $this->getTimeQujian($args['begintime'],$args['endtime']);
        if(!empty($args['department_id'])){
            $where['department_id'] = $args['department_id'];
        }
        //整理数据
        foreach ($courceInfo as $username => $info) {

           
            foreach ($info as $fenlei_id => $sourInfo) {
                foreach ($sourInfo as $source_id) {
                    if(isset($orderInfo[$username][$source_id]) && !empty($orderInfo[$username][$source_id])){
                        $data = $orderInfo[$username][$source_id];
                    }else{
                        $data = array(
                                    'customer_source_id' => $source_id,
                                    'source_name' => '',
                                    'fenlei' => $fenlei_id,
                                    'ordernum' => 0,
                                    'zpnum' => 0,
                                    'orderamount' => 0.00,
                                    'goodsamount' => 0.00,
                                    'moneypaid' => 0.00,
                                    'realreturnprice' => 0.00,
                                    'moneyunpaid' => 0.00,
                                    'source_own_id' => 0,
                                    'source_own' => ''
                                );
                    }
                    $where['salseids'] = $username;
                    $where['from_ad'] = $source_id;
                    $where['salse'] = $username;
                    $rg = $performanceModel->getRetrunGoodsAmountA($where);
                    $rp = $performanceModel->getReturnPriceA($where);
                    $returngoods_orderids = $performanceModel->getRetrunGoodsOrderid($where);
                    $plus_returnprice = 0;
                    if(!empty($returngoods_orderids))
                    {
                        $oids = array_column($returngoods_orderids,'order_goods_id');
                        //+跨月的(退款不退货的)总金额
                        $plus_returnprice = $performanceModel->getReturnPriceA($where,$oids);
                    }

                    $where['is_tree'] = 1;
                    $rgs = $performanceModel->getRetrunGoodsAmountA($where);
                    $rps = $performanceModel->getReturnPriceHG($where);
                    $returngoods_orderidss = $performanceModel->getRetrunGoodsOrderidHG($where);
                    $plus_returnprices = 0;
                    if(!empty($returngoods_orderidss))
                    {
                        $oids = array_column($returngoods_orderidss,'order_goods_id');
                        //+跨月的(退款不退货的)总金额
                        $plus_returnprices = $performanceModel->getReturnPriceHG($where,$oids);
                    }
                    $data['zongyeji'] = $data['goodsamount'] - $rg -$rp + $plus_returnprice;
                    //去除低于300元换购的
                    $where = array();
                    $where['department_id'] = $args['department_id'];
                    $where['orderenter'] = $args['orderenter'];
                    $where['begintime'] = $args['begintime'];
                    $where['endtime'] = $args['endtime'];
                    $where['from_ad'] = $source_id;
                    $where['make_order'] = $username;
                    //不含换购金额去除单件商品在300元以下的商品
                    $price_hg = $Modelcount->getOrderInfoHG($where);
                    $huangouprice = $price_hg - $rgs - $rps + $plus_returnprices; //不包含换购金额
                    //赠品客户数
                    $where_zp  =array();
                    $where_zp['department_id'] = $args['department_id'];
                    $where_zp['shop_type'] = $args['shop_type'];
                    $where_zp['orderenter'] = $args['orderenter'];
                    $where_zp['is_delete'] = $args['is_delete'];
                    $where_zp['from_ad'] = $source_id;
                    $where_zp['create_user'] = $username;
                    $num = 0;
                    foreach ($moth as $end_time => $star_time) {
                        $where_zp['begintime'] = $star_time;
                        $where_zp['endtime'] = $end_time;
                        $num+= $Model->getIsZpNum($where_zp);
                    }
                    $data['zpnum'] = $num;
                    $data['zongyeji_hg'] = $huangouprice;
                    $data['dis_borcs'] = isset($allorsderDis[$username][$source_id]) ? $allorsderDis[$username][$source_id] : 0;
                    $data['dis_borcs_to'] = isset($allorsderDisTo[$username][$source_id]) ? $allorsderDisTo[$username][$source_id] : 0;
                    $data['dis_borcs_to_num'] = isset($allorsderDisToNum[$username][$source_id]) ? $allorsderDisToNum[$username][$source_id] : 0;
                    $data['allbokenum'] = isset($allbokedata[$username][$source_id]) ? $allbokedata[$username][$source_id] : 0;
                    $data['realbokenum'] = isset($realbokedata[$username][$source_id]) ? $realbokedata[$username][$source_id] : 0;
                    $data['chengjiaobokenum'] = isset($payorderdata[$username][$source_id]) ? $payorderdata[$username][$source_id] : 0;
                    $data['yindaobokenum'] = isset($yindaobokedata[$username][$source_id]) ? $yindaobokedata[$username][$source_id] : 0;
                    $data['yindaoshidaobokenum'] = isset($yindaoshidaobokedata[$username][$source_id]) ? $yindaoshidaobokedata[$username][$source_id] : 0;
                    $data['daodianlv'] = empty($data['yindaobokenum']) ? 0 : round($data['yindaoshidaobokenum']/$data['yindaobokenum'], 4)*100;
                    $data['chengjiaolv']=empty($data['realbokenum']) ? 0 : round($data['dis_borcs_to']/$data['realbokenum'], 4)*100;
                    $data['kedanjia']=empty($data['dis_borcs_to']+$data['dis_borcs_to_num']) ? 0 : round($data['zongyeji']/($data['dis_borcs_to']+$data['dis_borcs_to_num']),4);
                    $data['kedanjia_hg']=empty($data['dis_borcs_to']) ? 0 : round($data['zongyeji_hg']/$data['dis_borcs_to'],4);
                    $sourcedata[$username][$fenlei_id][] = $data;
                }
            }
        }
        //echo '<pre>';
        //print_r($sourcedata);die;
        
        //汇总统计
        foreach($sourcedata as $uname => $info) {
            foreach ($info as $fenlei_id => $value) {
                $tongjidata = array(
                            'fenlei'=>$fenlei_id,
                            'source_name'=>$shopname.$this->types[$fenlei_id].'(总计)',
                            'allbokenum'=>0,
                            'realbokenum'=>0,
                            'chengjiaobokenum'=>0,
                            'yindaobokenum'=>0,
                            'yindaoshidaobokenum'=>0,
                            'ordernum'=>0,
                            'zpnum'=>0,
                            'orderamount'=>0,
                            'moneypaid'=>0,
                            'moneyunpaid'=>0,
                            'zongyeji'=>0,
                            'zongyeji_hg'=>0,
                            'kedanjia'=>0,
                            'kedanjia_hg'=>0,
                            'dis_borcs_to'=>0,
                            'dis_borcs'=>0
                        );

                foreach ($value as $info_c) {
                    $tongjidata['allbokenum'] += $info_c['allbokenum'];
                    $tongjidata['realbokenum'] += $info_c['realbokenum'];
                    $tongjidata['chengjiaobokenum'] += $info_c['chengjiaobokenum'];
                    $tongjidata['yindaobokenum'] += $info_c['yindaobokenum'];
                    $tongjidata['yindaoshidaobokenum'] += $info_c['yindaoshidaobokenum'];
                    $tongjidata['ordernum'] += $info_c['ordernum'];
                    $tongjidata['zpnum'] += $info_c['zpnum'];
                    $tongjidata['orderamount'] += $info_c['orderamount'];
                    $tongjidata['moneypaid'] += $info_c['moneypaid'];
                    $tongjidata['moneyunpaid'] += $info_c['moneyunpaid'];
                    $tongjidata['zongyeji'] += $info_c['zongyeji'];
                    $tongjidata['zongyeji_hg'] += $info_c['zongyeji_hg'];
                    $tongjidata['dis_borcs_to'] += $info_c['dis_borcs_to'];
                    $tongjidata['dis_borcs_to_num'] += $info_c['dis_borcs_to_num'];
                    $tongjidata['dis_borcs'] += $info_c['dis_borcs'];
                }
                $tongjidata['daodianlv'] = empty($tongjidata['yindaobokenum']) ? 0 : round($tongjidata['yindaoshidaobokenum']/$tongjidata['yindaobokenum'], 4)*100;
                $tongjidata['chengjiaolv'] = empty($tongjidata['realbokenum']) ? 0 : round($tongjidata['dis_borcs_to']/$tongjidata['realbokenum'], 4)*100;
                $tongjidata['kedanjia'] = empty($tongjidata['dis_borcs_to']+$tongjidata['dis_borcs_to_num']) ? 0 : round($tongjidata['zongyeji']/($tongjidata['dis_borcs_to']+$tongjidata['dis_borcs_to_num']), 4);
                $tongjidata['kedanjia_hg'] = empty($tongjidata['dis_borcs_to']) ? 0 : round($tongjidata['zongyeji_hg']/$tongjidata['dis_borcs_to'], 4);
                $info[$fenlei_id][] = $tongjidata;

                $zongji['z_allbokenum'] +=$tongjidata['allbokenum'];
                $zongji['z_realbokenum'] +=$tongjidata['realbokenum'];
                $zongji['z_chengjiaobokenum'] +=$tongjidata['chengjiaobokenum'];
                $zongji['z_yindaobokenum'] +=$tongjidata['yindaobokenum'];
                $zongji['z_yindaoshidaobokenum'] +=$tongjidata['yindaoshidaobokenum'];
                $zongji['z_ordernum'] +=$tongjidata['ordernum'];
                $zongji['z_zpnum'] +=$tongjidata['zpnum'];
                $zongji['z_orderamount'] +=$tongjidata['orderamount'];
                $zongji['z_moneypaid'] +=$tongjidata['moneypaid'];
                $zongji['z_moneyunpaid'] +=$tongjidata['moneyunpaid'];
                $zongji['z_zongyeji'] +=$tongjidata['zongyeji'];
                $zongji['z_zongyeji_hg'] +=$tongjidata['zongyeji_hg'];
                $zongji['z_dis_borcs'] +=$tongjidata['dis_borcs'];
                $zongji['z_dis_borcs_to'] +=$tongjidata['dis_borcs_to'];
                $zongji['z_dis_borcs_to_num'] +=$tongjidata['dis_borcs_to_num'];
                $zongji['z_daodianlv'] = empty($zongji['z_yindaobokenum']) ? 0 : round($zongji['z_yindaoshidaobokenum']/$zongji['z_yindaobokenum'], 4)*100;
                $zongji['z_chengjiaolv'] = empty($zongji['z_realbokenum']) ? 0 : round($zongji['z_dis_borcs_to']/$tongjidata['z_realbokenum'], 4)*100;
                $zongji['z_kedanjia'] = empty($zongji['z_dis_borcs_to']+$zongji['z_dis_borcs_to_num']) ? 0 : round($zongji['z_zongyeji']/($zongji['z_dis_borcs_to']+$zongji['z_dis_borcs_to_num']), 4);
                $zongji['z_kedanjia_hg'] = empty($zongji['z_dis_borcs_to']) ? 0 : round($zongji['z_zongyeji_hg']/$zongji['z_dis_borcs_to'], 4);
            }
            $sourcedata[$uname] = $info;
            
        }
        //echo '<pre>';
        //print_r($sourcedata);die;

        if(empty($sourcedata)) {
            $rows = 0;
        }else{
            $rows = count($sourcedata);
        }

        return array('data'=>$sourcedata, 'rows'=>$rows, 'zongji'=>$zongji);
    }

    //根据销售顾问导出
    public function user_down($params) {

        $_dataInfo = $this->_user_search_data($params);
        $zongji = $_dataInfo['zongji'];
        $types = $this->types;
        $ctinfo = $this->getCustomerSourcesAll();
        ini_set('memory_limit','-1');
        set_time_limit(0);
        header("Content-Type: text/html; charset=gb2312");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=".iconv('utf-8','gb2312',"各渠道统计报表").".xls");

        $csv_body="<table border='1'><tr><td></td><td></td><td></td><td colspan='10'>进店人数管理</td><td colspan='4'>进店销售额管理</td></tr>";
        $csv_body.="<tr><td>销售顾问</td><td>来源分类</td><td>客户来源</td><td>新增预约人数</td><td>实际到店人数</td><td>预约预计到店人数</td><td>预约实际到店人数</td><td>预约到店率</td><td>实际成单数</td><td>实际成交客户数（不含换购）</td><td>换购人数</td><td>成交率</td><td>赠品客户数</td><td>总业绩</td><td>总业绩（不含换购）</td><td>客单价</td><td>客单价（不含换购）</td></tr>";
        if ($_dataInfo['rows'] >= 1) {
            foreach ($_dataInfo['data'] as $uname => $t_info) {
                foreach ($t_info as $f_id => $value) {
                    foreach($value as $k => $v){
                        $customer_name = isset($ctinfo[$v['customer_source_id']]) ? $ctinfo[$v['customer_source_id']] : $v['source_name'];
                        $csv_body.="<tr>";
                        if($k<1){
                            $csv_body.="<td style='vertical-align:middle' rowspan='".count($value)."'>".$uname."</td>";
                            $csv_body.="<td style='vertical-align:middle' rowspan='".count($value)."'>".$types[$f_id]."</td>";
                        }
                        $csv_body.="<td>".$customer_name."</td><td>".$v['allbokenum']."</td><td>".$v['realbokenum']."</td><td>".
                            $v['yindaobokenum']."</td><td>".$v['yindaoshidaobokenum']."</td><td>".$v['daodianlv']."%</td><td>".
                            $v['ordernum']."</td><td>".$v['dis_borcs_to']."</td><td>".$v['dis_borcs_to_num']."</td><td>".$v['chengjiaolv']."%</td><td>".
                            $v['zpnum']."</td><td>".$v['zongyeji']."</td><td>".$v['zongyeji_hg']."</td><td>".
                            $v['kedanjia']."</td><td>".$v['kedanjia_hg']."</td>";
                        $csv_body.="</tr>";
                    }
                }
            }
            $csv_body.="<tr>";
            $csv_body.="<td style='vertical-align:middle'></td>";
            $csv_body.="<td style='vertical-align:middle'></td>";
            $csv_body.="<td>总计：</td><td>".$zongji['z_allbokenum']."</td><td>".$zongji['z_realbokenum']."</td><td>".
                $zongji['z_yindaobokenum']."</td><td>".$zongji['z_yindaoshidaobokenum']."</td><td>".$zongji['z_daodianlv']."%</td><td>".
                $zongji['z_ordernum']."</td><td>".$zongji['z_dis_borcs']."</td><td>".$zongji['z_dis_borcs_to_num']."</td><td>".$zongji['z_chengjiaolv']."%</td><td>".
                $zongji['z_zpnum']."</td><td>".$zongji['z_zongyeji']."</td><td>".$zongji['z_zongyeji_hg']."</td><td>".
                $zongji['z_kedanjia']."</td><td>".$zongji['z_kedanjia_hg']."</td>";
            $csv_body.="</tr>";
            $csv_footer="</table>";
            echo $csv_body.$csv_footer;
        } else {
            echo '没有数据！';
        }
    }

    //取出所属店面的销售人员
    public function getDepUserInfoByDepId($dep_id)
    {
        # code...
        $model = new TydReportModel(59);
        $info = $model->getDepUserInfo($dep_id);
        $names_1 = preg_split('/,/', $info['dp_leader_name'], -1, PREG_SPLIT_NO_EMPTY);
        $names_2 = preg_split('/,/', $info['dp_people_name'], -1, PREG_SPLIT_NO_EMPTY);
        return array_unique(array_merge($names_1, $names_2));
    }


    //提供一个开始日期和结束日期获取这期间的月份
    public function getTimeQujian($start_date,$end_date)
    {
        $month_arr = array();
        $start_timestamp = strtotime($start_date);
        $end_timestamp = strtotime($end_date);

        $start_timestamp = mktime(0, 0, 0, date("n", $start_timestamp), 1, date("Y", $start_timestamp));
        $next_timestamp = $start_timestamp;
        while ($next_timestamp <= $end_timestamp) {
            $str = date("Y-m", $next_timestamp).'-31';
            $month_arr[$str] = date("Y-m-d", $next_timestamp);
            $next_timestamp = mktime(0, 0, 0, date("n", $next_timestamp) + 1, date("j", $next_timestamp), date("Y", $next_timestamp));
        };
        $ret_array = array();
        if(count($month_arr) == 1){
            $ret_array[$end_date] = $start_date;
        }else{
            $farst = array_pop($month_arr);
            array_shift($month_arr);
            $a = date("Y-m", $start_timestamp).'-31';
            $fist_time[$a] = $start_date;
            $end_time[$end_date] = $farst;
            $ret_array = array_merge($fist_time,$month_arr,$end_time);
        }
        return $ret_array;
    }

    //合并数据
    public function cekScut($info)
    {   
        $data = array();
        if($info){
            foreach ($info as $key => $value) {
                foreach ($value as $k => $val) {
                    $data[$k]+=$val;
                }
                
            }
        }
        return $data;
    }

     //合并数据
    public function mergeNumDist($info)
    {
        $data = array();
        $info = array_filter($info);
        if(!empty($info)){
            foreach ($info as $key => $val) {
                if(!empty($val)){
                    foreach ($val as $t => $v) {
                        $data[$v['customer_source_id']] += $v['dis_ordernum'];
                    }
                }
            }
        }
        return $data;
    }

    public function getShops(){
        $shop_type = _Post::getInt('shop_type');
        
        //获取体验店的信息
        $model = new ShopCfgChannelModel(59);
        $data = $model->getallshop();
        $ret = array();
        foreach($data as $key => $val){
            if($shop_type == 1 && $val['shop_type'] == 1){
                $ret[$val['id']] = $val['shop_name'];
            }
            if($shop_type == 2 && $val['shop_type'] == 2){
                $ret[$val['id']] = $val['shop_name'];
            }
            if($shop_type == 3 && $val['shop_type'] == 3){
                $ret[$val['id']] = $val['shop_name'];
            }
            if($shop_type == 0){
                $ret[$val['id']] = $val['shop_name'];
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

    //取销售顾问
    public function getCreateuser(){
        $result = array('success' => 0,'error' => '');
        $department= _Request::get('department');
        $model = new UserChannelModel(1);
        $data = array();
        if(!empty($department)){
            $dp_people_name = array();
            $data = $model->get_channels_person_by_channel_id($department);
            if($data['dp_people_name']=='' || $data['dp_leader_name']==''){
                $data = $model->get_user_channel_by_channel_id($department);
            }else{
                $dp_people_name = explode(",",$data['dp_people_name']);
                $dp_people_name = array_filter($dp_people_name);
                $dp_leader_name = explode(",",$data['dp_leader_name']);
                $dp_leader_name = array_filter($dp_leader_name);
                $data=array();
                $dp_people_me = in_array($_SESSION['userName'],$dp_people_name);
        
                if(in_array($_SESSION['userName'],$dp_leader_name))
                {
                    $alluser = array_merge($dp_people_name,$dp_leader_name);
                    foreach($alluser as $k=>$v)
                    {
                        $data[]['account']=$v;
                    }
                }elseif($dp_people_me){
                    $data[0]['account']=$_SESSION['userName'];  
                }
            }
        }
        if(!empty($data)){
            $content='';
            if(!empty($data)){
                foreach($data as $k => $v){
                    $content.='<option value="'.$v['account'].'">'.$v['account'].'</option>"';
                }
            }
            $result['content']=$content;
        }
        Util::jsonExit($result);
        
    }

     //合并数据
    public function mergeNumDis($info)
    {
        $data = array();
        $info = array_filter($info);
        if(!empty($info)){
            foreach ($info as $key => $name) {
                if(!empty($name)){
                    foreach ($name as $k => $val) {
                        if(!empty($val)){
                            foreach ($val as $t => $v) {
                                $data[$key][$v['customer_source_id']] += $v['dis_ordernum'];
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }

    //根据店面导出
    public function depdown($params)
    {
        set_time_limit(0);
        ini_set('memory_limit','2000M');
        $shop_type = $params['shop_type'];
        
        $begintime = $params['begintime'];
        $endtime = $params['endtime'];
        $orderenter = $params['orderenter'];
        $fenlei = $params['fenlei'];

        if(empty($shop_type)) {
            exit("请选择门店性质");
        }
        if(empty($begintime) || empty($endtime)) {
            exit("请选择导出时间");
        }

        if($begintime<$this->limit_time && SYS_SCOPE == 'zhanting'){
            $begintime = $this->limit_time;
        }

        $days=$this->getDatePeriod($begintime,$endtime);
        if($days>186) exit('亲，请导半年范围内的信息!');

        $where = array(
            'shop_type' => $shop_type,
            'begintime' => $begintime,
            'endtime'   => $endtime,
            'orderenter'=> $orderenter,
            'fenlei'    => $fenlei
        );

        $model = new TydReportModel(59);//getbokecount
        $modelcount = new TydcountReportModel(59);
        
        //取出预约数据；
        $_where = $where;
        $_where['create_time_start'] = $begintime;
        $_where['create_time_end'] = $endtime;
        $bespokeinfo = $model->getbespokeinfo($_where);
        $bespokeinfo = $this->transitionKeyVal($bespokeinfo, 'create_time');

        //第二部拿取 实际到店数
        $_where = $where;
        $_where['real_inshop_time_start'] = $begintime;
        $_where['real_inshop_time_end'] = $endtime;
        $_where['re_status'] = 1;  //到店状态
        $realboke = $model->getbespokeinfo($_where);
        $realboke = $this->transitionKeyVal($realboke, 'real_inshop_time');

        //第三部拿取 预约当期应到数
        $_where = $where;
        $_where['bespoke_inshop_time_start'] = $begintime;
        $_where['bespoke_inshop_time_end'] = $endtime;
        $yindaoboke = $model->getbespokeinfo($_where);
        $yindaoboke = $this->transitionKeyVal($yindaoboke, 'bespoke_inshop_time');

        //第四部拿取 当期应到预约的实到数
        $_where = $where;
        $_where['bespoke_inshop_time_start'] = $begintime;
        $_where['bespoke_inshop_time_end'] = $endtime;
        $_where['re_status'] = 1;  //到店状态
        $ydsdboke = $model->getbespokeinfo($_where);
        $ydsdboke = $this->transitionKeyVal($ydsdboke, 'bespoke_inshop_time');

        //第五步 m取出订单信息
        $_where = $where;
        $_where['begintime'] = $begintime;
        $_where['endtime'] = $endtime;
        $orderinfo = $model->getorderinfo($_where);
        $orderinfo = $this->transitionKeyVal($orderinfo, 'pay_date');

        $deplist = array();
        $depinfo = $this->getMyDepartments();
        foreach ($depinfo as $val) {$deplist[$val['id']] = $val['shop_name'];}
        $shop_type = $this->types;
        $csv_footer = '';
        //遍历预约数据
        foreach ($bespokeinfo as $time_field => $depinfo) {
            foreach ($depinfo as $dep_id => $fenleiinfo) {
                $dep_name = isset($deplist[$dep_id])?$deplist[$dep_id]:'';
                foreach ($fenleiinfo as $fenleikey => $besopkelist) {
                    $realbokeinfo = isset($realboke[$time_field][$dep_id][$fenleikey])?$realboke[$time_field][$dep_id][$fenleikey]:array();
                    $yindaobokeinfo = isset($yindaoboke[$time_field][$dep_id][$fenleikey])?$yindaoboke[$time_field][$dep_id][$fenleikey]:array();
                    $ydsdbokeinfo = isset($ydsdboke[$time_field][$dep_id][$fenleikey])?$ydsdboke[$time_field][$dep_id][$fenleikey]:array();

                    $newly_bespke_num = count($besopkelist);//实际到店
                    $real_bespke_num  = count($realbokeinfo);//预约当期应到数
                    $yindao_bespke_num= count($yindaobokeinfo);//预约当期应到数
                    $ydsdboke_num     = count($ydsdbokeinfo);//预约实际到店数
                    $boke_proportion = bcmul(bcdiv($ydsdboke_num, $yindao_bespke_num, 2), 100, 2);//到店率

                    //订单信息
                    $orderlsitinfo = isset($orderinfo[$time_field][$dep_id][$fenleikey])?$orderinfo[$time_field][$dep_id][$fenleikey]:array();
                    $ordernum = $exchange = $zp_info = $order_num = array();
                    $total_order_price = $ex_order_price = $order_list_num = 0;
                    if(!empty($orderlsitinfo)){
                        foreach ($orderlsitinfo as $k => $val) {
                            $mobile = $val['mobile'];
                            $is_zp = $val['is_zp'];
                            $order_id = $val['id'];
                            if($is_zp != 0){//是否赠品
                                $zp_info[$mobile] = $order_id;continue;
                            }
                            //$order_num[] = $val;
                            $order_list_num++;
                            $order_amount = $val['goodsamount'];//订单总金额
                            $total_order_price = bcadd($total_order_price, $order_amount, 2);
                            //不含换购
                            if(bccomp($order_amount, 300, 2) != -1){
                                $ordernum[$mobile] = $order_id;//排除重复手机号i
                                $ex_order_price = bcadd($ex_order_price, $order_amount, 2);
                            }else{
                                $exchange[$mobile] = $order_id;//换购人数
                            }
                        }
                    }
                    //$order_list_num = count($order_num);//实际成单数
                    $real_order_num = count($ordernum);//实际成交客户数（不含换购）
                    $exchange_num   = count($exchange);//换购人数
                    $zp_num         = count($zp_info);//赠品数量

                    $total_order_num = (float) bcadd($real_order_num, $exchange_num, 2);//总的实际成交人数

                    $affirm_proportion = bcmul(bcdiv($real_order_num, $real_bespke_num, 2), 100, 2);//成交率
                    //var_dump($affirm_proportion);die;
                    //总业绩
                    $_where = $where;
                    $_where['start_time'] = $time_field."-01";
                    $_where['end_time']   = $time_field."-31";
                    $_where['department_id'] = $dep_id;
                    $_where['fenlei']     = $fenleikey;
                    $return_price = $this->getOrderReturnPrice($_where);//总退款金额
                    $ex_return_price = $this->exgetOrderReturnPrice($_where);//总退款金额（不含换购）

                    $total_performance = (float) bcsub($total_order_price, $return_price, 2);//总业绩
                    $total_ex_performance = (float) bcsub($ex_order_price, $ex_return_price, 2);//总业绩（不含换购）

                    $average_order_price = (float) bcdiv($total_performance, $total_order_num, 2);//客单价
                    $exaverage_order_price = (float) bcdiv($total_ex_performance, $real_order_num, 2);//客单价
                    $fenlei_name = isset($shop_type[$fenleikey])?$shop_type[$fenleikey]:'';
                    $summarizing = array(
                        "'".$time_field,
                        $dep_name,
                        $fenlei_name,
                        'newly_bespke_num'=>$newly_bespke_num,
                        'real_bespke_num'=>$real_bespke_num,
                        'yindao_bespke_num'=>$yindao_bespke_num,
                        'ydsdboke_num'=>$ydsdboke_num,
                        'boke_proportion'=>$boke_proportion."%",
                        'order_list_num'=>$order_list_num,
                        'real_order_num'=>$real_order_num,
                        'exchange_num'=>$exchange_num,
                        'affirm_proportion'=>$affirm_proportion."%",
                        'zp_num'=>$zp_num,
                        'total_performance'=>$total_performance,
                        'total_ex_performance'=>$total_ex_performance,
                        'average_order_price'=>$average_order_price,
                        'exaverage_order_price'=>$exaverage_order_price
                    );
                    
                    /*$footerlist = array(
                        "'".$time_field,
                        $dep_name,
                        $fenlei_name,
                        $info['newly_bespke_num'],
                        $info['real_bespke_num'],
                        $info['yindao_bespke_num'],
                        $info['ydsdboke_num'],
                        $info['boke_proportion']."%",
                        $info['order_list_num'],
                        $info['real_order_num'],
                        $info['exchange_num'],
                        $info['affirm_proportion']."%",
                        $info['zp_num'],
                        $info['total_performance'],
                        $info['total_ex_performance'],
                        $info['average_order_price'],
                        $info['exaverage_order_price']
                    );*/
                    $csv_footer.="<tr><td>".implode("</td><td>", $summarizing)."</td></tr>";
                    //$returndata[$time_field][$dep_id][$fenleikey] = $summarizing;
                }
            }
        }

        header("Content-Type: text/html; charset=gb2312");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=".iconv('utf-8','gb2312',time()."各渠道汇总统计").".xls");
        $csv_body="<table border='1'><tr>
        <td colspan='3'></td>
        <td colspan='10'>进店人数管理</td>
        <td colspan='4'>进店销售额管理</td>
        </tr>";
        $title = array('时间', '体验店', '来源分类', '新增预约人数', '实际到店人数', '预约预计到店人数', '预约实际到店人数', '预约到店率', '实际成单数', '实际成交客户数（不含换购）', '换购人数', '成交率', '赠品客户数', '总业绩', '总业绩（不含换购）',' 客单价', '客单价（不含换购）');
        $csv_body.="<tr><td>".implode("</td><td>", $title)."</td></tr>";
        $csv_footer.="</table>";
        echo $csv_body.$csv_footer;
    }

    //抓取退款金额
    public function getOrderReturnPrice($where)
    {
        $performanceModel = new PerformanceReportModel(27);
        $return_price = 0;
        $rg = $performanceModel->getRetrunGoodsAmountA($where);
        $rp = $performanceModel->getReturnPriceA($where);
        $returngoods_orderids = $performanceModel->getRetrunGoodsOrderid($where);
        $plus_returnprice = 0;
        if(!empty($returngoods_orderids))
        {
            $oids = array_column($returngoods_orderids,'order_goods_id');
            //+跨月的(退款不退货的)总金额
            $plus_returnprice = $performanceModel->getReturnPriceA($where,$oids);
        }
        $return_price = bcsub(bcadd($rg, $rp, 2), $plus_returnprice, 2);
        return $return_price;
    }

    //抓取退款金额
    public function exgetOrderReturnPrice($where)
    {
        $performanceModel = new PerformanceReportModel(27);
        $return_price = 0;
        $where['is_tree'] = 1;
        $rg = $performanceModel->getRetrunGoodsAmountA($where);
        $rp = $performanceModel->getReturnPriceHG($where);
        $returngoods_orderids = $performanceModel->getRetrunGoodsOrderidHG($where);
        $plus_returnprice = 0;
        if(!empty($returngoods_orderids))
        {
            $oids = array_column($returngoods_orderids,'order_goods_id');
            //+跨月的(退款不退货的)总金额
            $plus_returnprice = $performanceModel->getReturnPriceHG($where,$oids);
        }
        $return_price = bcsub(bcadd($rg, $rp, 2), $plus_returnprice, 2);
        return $return_price;
    }

    public function transitionKeyVal($arrayin, $time_field='')
    {
        $return_array = array();
        if(!empty($arrayin)){
            foreach ($arrayin as $k => $val) {
                $dep_id = $val['department_id'];
                $fenlei = $val['fenlei'];
                $mobile = isset($val['customer_mobile'])?$val['customer_mobile']:null;
                $time = isset($val[$time_field])?$val[$time_field]:'';
                if(empty($time)) continue;
                $time = substr($time, 0, 7);
                if(empty($mobile)){
                    $return_array[$time][$dep_id][$fenlei][] = $val;
                }else{
                    $return_array[$time][$dep_id][$fenlei][$mobile] = $val;
                }
            }
        }
        return $return_array;
    }
}
?>