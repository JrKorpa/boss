<?php
/**
 *  -------------------------------------------------
 *   @file		: OfflineChannelGoldReportController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-08-23 16:21:03
 *   @update	:
 *  -------------------------------------------------
 */
class OfflineChannelGoldReportController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $types = array(0=>"全部",-1=>"其他",1=>"异业联盟",2=>"社区",3=>"BDD相关",4=>"团购",5=>"老顾客",6=>"数据",7=>"网络来源");

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $data = $this->getMyDepartments();
		$this->render('offline_channel_gold_report_search_form.html',array('bar'=>Auth::getBar(), 'allshop'=>$data, 'types'=>$this->types));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
        $data = $this->_search_data($params);
        //var_dump($data);die;
		$this->render('offline_channel_gold_report_search_list.html',array(
			'page_list'=>$data['data'],
            'alltyd'=>$data['alltyd']
		));
	}

    private function _search_data($params=array()) {
        $myDepartment = $this->getmyDepartment();
        $alltyd = array();
        //统计当前用户拥有权限的体验店id
        $search_tydid = array();
        foreach($myDepartment as $tydinfo)
        {
            $tydid = $tydinfo['id'];
            $tydname = $tydinfo['shop_name'];
            $alltyd[$tydid] = $tydname;
            //放进来
            array_push($search_tydid,$tydid);
        }
        $args = array(
                'mod' => _Request::get("mod"),
                'con' => substr(__CLASS__, 0, -10),
                'act' => __FUNCTION__,
                'shop_type' => _Request::get("shop_type"),
                'department_id' => _Request::getList("shop_id"),
                'orderenter' => _Request::getString("orderenter"),
                'begintime' => _Request::get("begintime"),
                'endtime'   => _Request::get("endtime"),
                'is_delete' => 0
        );
        if(empty($args['begintime']) || empty($args['endtime']))
        {
            echo '请选择时间';exit;
        }
        if($args['begintime'] > $args['endtime'])
        {
            echo '结束时间必须大于开始时间';exit;
        }
        $data_arr=$this->get_data_arr($args['begintime'],$args['endtime']);
        if(count($data_arr)>366){
            die('请查询366天范围内的信息!');
        }
        //print_r($args['department_id']);exit;
        if(!empty($args['department_id']))
        {
            $tydids = $args['department_id'];
            $model = new ShopCfgChannelModel(59);
            $data = $model->getallshop();
            $ret = array();
            foreach($data as $key => $val){
                if( in_array($val['id'], $tydids)){
                    $ret[$val['id']] = $val['shop_name'];
                }
            }
        }else{
            $shop_type = $args['shop_type'];

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
            $myChannel=array();
            foreach($data_chennel as $key => $val){
                if(!empty($ret) && array_key_exists($val['id'],$ret)){
                    $myChannel[]= $val['id'];
                }
            }
            $tydids = implode(',',$myChannel);
        }
        $tydids = array_unique($tydids);
        $args['department_id'] = implode(',', $tydids);
        foreach($ret as $key => $val){
            if(!in_array($key,$search_tydid)){
                unset($ret[$key]);
            }
        }
        $model = new OfflineChannelGoldReportModel(27);
        //根据渠道维度取数据 ，实际成单数
        $where = array();
        $where['department_id'] = $args['department_id'];
        $where['orderenter'] = $args['orderenter'];
        $where['begintime'] = $args['begintime'];
        $where['endtime'] = $args['endtime'];
        $where['shop_type'] = $args['shop_type'];
        $real_vol_order = $model->getRealVolOrder($where);//订单明细数据
        $return_model = new PerformanceReportModel(27);
        $Model = new TydcountReportModel(59);
        $data = array();
        foreach ($real_vol_order as $key => $value) {
            $data[$value['department_id']][] = $value;
        }
        $gold = array('普通黄金','定价黄金');
        $saleData = array();//容纳统计的数据
        foreach ($data as $key => $valdata) {
            $gold_data= array();
            $gold_data['gold_performance']=0;//黄金总业绩（未退款）
            $gold_data['no_gold_performance']=0;//非黄金总业绩（未退款）

            $gold_data['gold_performance_hg']=0;//黄金总业绩（不含换购未退款）（不含换购）
            $gold_data['no_gold_performance_hg']=0;//非黄金总业绩（不含换购未退款）（不含换购）

            $gold_data['gold_num']=0;//黄金实际成单数
            $gold_data['no_gold_num']=0;//非黄金实际成单数

            $gold_data['gold_num_hg']=0;//黄金实际成交客户数（不包含换购）
            $gold_data['no_gold_num_hg']=0;//非黄金实际成交客户数（不包含换购）

            $gold_data['gold_performance_total']=0;//黄金总业绩（扣除退款）
            $gold_data['no_gold_performance_total']=0;//非黄金总业绩（扣除退款）

            $gold_data['gold_performance_total_hg']=0;//黄金总业绩（扣除不含换购退款）（不包含换购）
            $gold_data['no_gold_performance_total_hg']=0;//非黄金总业绩（扣除不含换购退款）（不包含换购）

            $gold_data['gold_not_hg_num'] =0;//黄金换购
            $gold_data['no_gold_not_hg_num']=0;//非黄金换购
            $gold_data['gold_kedanjia'] = 0;//黄金客单价
            $gold_data['gold_kedanjia_not_hg']=0;//黄金不含换购客单价
            $gold_data['no_gold_kedanjia'] = 0;//黄金客单价
            $gold_data['no_gold_kedanjia_not_hg']=0;//黄金不含换购客单价
            $gold_data['gold_chengjiaolv'] = 0;//黄金成交率
            $gold_data['no_gold_chengjiaolv'] = 0;//非黄金成交率
            $gold_data['realboke']=0;//实际到店人数

            $gold_info_num = array();
            $gold_info_num_hg = array();
            $no_gold_info_num = array();
            $no_gold_info_num_hg = array();
            $no_gold_goods_info = array();
            $no_gold_info_ck_hg = array();
            $gold_info_ck_hg = array();
            //var_dump($valdata);die;
            foreach ($valdata as $k => $goods) {
                //var_dump($goods);die;
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
                //同一个手机号 同月算一个订单 不含赠品单//实际成单数
                $createtime = substr($goods['pay_date'],0,7);
                $is_hg = bccomp(300,$money,2);
                //var_dump($money,300,$is_hg);die;
                if(in_array($product_type1, $gold) || in_array($product_type_name, $gold)){
                    $gold_data['gold_performance'] += $money;

                    if($goods['is_zp'] != '1'){
                        $gold_info_num[$createtime][] = $goods['mobile'];
                        //var_dump($is_hg != 1);die;
                        if($is_hg != 1){
                            $gold_data['gold_performance_hg'] += $money;
                            $gold_info_num_hg[$createtime][] = $goods['mobile'];
                        }else{
                            $gold_info_ck_hg[$createtime][] = $goods['mobile'];
                        }
                    }
                }else{
                    $gold_data['no_gold_performance'] += $money;

                    if($goods['is_zp'] != '1'){
                        $no_gold_info_num[$createtime][] = $goods['mobile'];
                        if($is_hg != 1){
                            $gold_data['no_gold_performance_hg'] += $money;
                            $no_gold_info_num_hg[$createtime][] = $goods['mobile'];
                        }else{
                            $no_gold_info_ck_hg[$createtime][] = $goods['mobile'];
                        }
                    }
                    //非黄金商品
                    $no_gold_goods_info[] = $goods;
                }
                
            }
            //var_dump($gold_info_num_hg);die;
            if(!empty($gold_info_num)){
                foreach ($gold_info_num as $val) {
                    $gold_data['gold_num'] = count(array_flip($val));
                }
            }
            if(!empty($no_gold_info_num)){
                foreach ($no_gold_info_num as $val) {
                    $gold_data['no_gold_num'] = count(array_flip($val));
                }
            }
            if(!empty($gold_info_num_hg)){
                foreach ($gold_info_num_hg as $val) {
                    $gold_data['gold_num_hg'] = count(array_flip($val));
                }
            }
            if(!empty($no_gold_info_num_hg)){
                foreach ($no_gold_info_num_hg as $val) {
                    $gold_data['no_gold_num_hg'] = count(array_flip($val));
                }
            }
            if(!empty($gold_info_ck_hg)){
                foreach ($gold_info_ck_hg as $val) {
                    $gold_data['gold_not_hg_num'] = count(array_flip($val));
                }
            }
            if(!empty($no_gold_info_ck_hg)){
                foreach ($no_gold_info_ck_hg as $val) {
                    $gold_data['no_gold_not_hg_num'] = count(array_flip($val));
                }
            }

            //获取渠道的退款数据 区分黄金非黄金
            $where['start_time'] = $args['begintime'];
            $where['end_time'] = $args['endtime'];
            if($args['orderenter']){
                $where['referer'] = 1;
                if($args['orderenter'] != '婚博会') $where['referer'] = 2;
            }
            $where['department_id'] = $key;
            $returngoods_orderids = $return_model->getRetrunGoodsOrderid($where);//退款数据 退商品
            $returng_details = array();
            $ng_return_details = array();
            $noreturng_details = array();
            if(!empty($returngoods_orderids)){
                $oids = array_column($returngoods_orderids,'order_goods_id');
                //1.1:如果当期有退货的,那么计算出,当期这些退货的商品是属于裸石还是成品
                $returng_details = $return_model->getDetailsbyid($oids);
            }
            //3.1:获取当期退货，并且之前有退款不退货的记录明细
            $return_nog_details = $return_model->getNogoodsReturoids($where,$oids);
            if(!empty($return_nog_details))
            {
                $ngids = array_column($return_nog_details,'order_goods_id');
                $ng_return_details = $return_model->getDetailsbyid($ngids,2);
            }
            //2.1 统计当期退款不退货(不退商品的)的订单明细自增id
            $noreturngoods_orderids = $return_model->getRetrunGoodsOrderid($where,2);
            if(!empty($noreturngoods_orderids))
            {
                $nids = array_column($noreturngoods_orderids,'order_goods_id');
                //根据订单明细自增id获取出订单的明细
                $noreturng_details = $return_model->getDetailsbyid($nids,2);
            }
            //统计退货金额 分黄金和非黄金
            $return_goods_price = $this->checkGoldType($returng_details,$gold);
            //获取当期退货，并且之前有退款不退货的记录明细 分黄金和非黄金
            $ng_return_goods_price = $this->checkGoldType($ng_return_details,$gold);
            //退款不退货 分黄金和非黄金
            $noreturn_goods_price = $this->checkGoldType($noreturng_details,$gold);
            //var_dump($return_goods_price,$ng_return_goods_price,$noreturn_goods_price);die;
            //黄金退款金额总计
            $return_data_total = bcadd(bcadd($return_goods_price['gold_return_price'],$ng_return_goods_price['gold_return_price'],2),$noreturn_goods_price['gold_return_price'],2);
            //黄金退款金额总计（不含换购）
            $return_data_total_hg = bcadd(bcadd($return_goods_price['gold_return_price_hg'],$ng_return_goods_price['gold_return_price_hg'],2),$noreturn_goods_price['gold_return_price_hg'],2);
            //非黄金退款金额总计
            $no_return_data_total = bcadd(bcadd($return_goods_price['no_gold_return_price'],$ng_return_goods_price['no_gold_return_price'],2),$noreturn_goods_price['no_gold_return_price'],2);
            //非黄金退款金额总计（不含换购）
            $no_return_data_total_hg = bcadd(bcadd($return_goods_price['no_gold_return_price_hg'],$ng_return_goods_price['no_gold_return_price_hg'],2),$noreturn_goods_price['no_gold_return_price_hg'],2);
            //黄金总业绩
            $gold_data['gold_performance_total'] = bcsub($gold_data['gold_performance'],$return_data_total,2);
            //非黄金总业绩
            $gold_data['no_gold_performance_total'] = bcsub($gold_data['no_gold_performance'],$no_return_data_total,2);

            //黄金总业绩（不含换购）
            $gold_data['gold_performance_total_hg'] = bcsub($gold_data['gold_performance_hg'],$return_data_total_hg,2);
            //非黄金总业绩（不含换购）
            $gold_data['no_gold_performance_total_hg'] = bcsub($gold_data['no_gold_performance_hg'],$no_return_data_total_hg,2);

            //客单价，客单价（不含换购）
            //客单价：总业绩/（实际成交客户数（不含换购）+换购客户数）
            //客单价（不含换购）：总业绩（不含换购）/实际成交客户数（不含换购）
            $gold_huangou_num = bcadd($gold_data['gold_num_hg'],$gold_data['gold_not_hg_num'],2);
            $no_gold_huangou_num = bcadd($gold_data['no_gold_num_hg'],$gold_data['no_gold_not_hg_num'],2);
            //var_dump($gold_data['gold_performance_total'],$gold_huangou_num);die;
            $gold_data['gold_kedanjia'] = bcdiv($gold_data['gold_performance_total'],$gold_huangou_num,2);
            $gold_data['gold_kedanjia_not_hg'] = bcdiv($gold_data['gold_performance_total_hg'],$gold_data['gold_num'],2);

            $gold_data['no_gold_kedanjia'] = bcdiv($gold_data['no_gold_performance_total'],$no_gold_huangou_num,2);
            $gold_data['no_gold_kedanjia_not_hg'] = bcdiv($gold_data['no_gold_performance_total_hg'],$gold_data['no_gold_num_hg'],2);

            //成交率 （黄金成交率 非黄金成交率）
            //第二部拿取 实际到店数
            //$args['begintime'];
            //$where['end_time'] = $args['endtime'];
            $where['real_inshop_time_start'] = $args['begintime'];
            $where['real_inshop_time_end'] = $args['endtime'];
            $where['bespoke_status'] = 2;
            $where['department_id'] = $key;
            $where['dis_bespoke_id'] = 1;
            $realboke = $Model->getBespokeInfo($where);
            if(!empty($realboke)) $gold_data['realboke'] = $realboke['count'];
            $gold_data['gold_chengjiaolv'] = bcdiv($gold_data['gold_num'],$gold_data['realboke'],2);
            $gold_data['no_gold_chengjiaolv'] = bcdiv($gold_data['no_gold_num'],$gold_data['realboke'],2);
            if(empty($gold_data['gold_chengjiaolv'])) $gold_data['gold_chengjiaolv'] = 0;
            if(empty($gold_data['no_gold_chengjiaolv'])) $gold_data['no_gold_chengjiaolv'] = 0;
            $saleData[$key] = $gold_data;
        }
        return array('data'=>$saleData,'alltyd'=>$alltyd);
    }

    //区分是否黄金和非黄金 的退款金额
    public function checkGoldType($details_return,$gold)
    {
        $return_data = array();
        $return_data['gold_return_price'] = 0;//黄金退款金额
        $return_data['gold_return_price_hg'] = 0;//黄金不含换购退款金额
        $return_data['no_gold_return_price'] = 0;//非黄金退款金额
        $return_data['no_gold_return_price_hg'] = 0;//非黄金不含换购退款金额
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
                $is_hg = bccomp(300,$money,2);
                if(in_array($goods['product_type1'], $gold) || in_array($goods['product_type_name'], $gold)){
                    if($is_hg != 1){//不含换购
                        $return_data['gold_return_price_hg'] = bcadd($return_data['gold_return_price_hg'],$money,2);
                    }else{
                        $return_data['gold_return_price'] = bcadd($return_data['gold_return_price'],$money,2);
                    }
                }else{
                    if($is_hg != 1){//不含换购
                        $return_data['no_gold_return_price_hg'] = bcadd($return_data['no_gold_return_price_hg'],$money,2);
                    }else{
                        $return_data['no_gold_return_price'] = bcadd($return_data['no_gold_return_price'],$money,2);
                    }
                    $return_data['no_gold_goods'][] = $goods;
                }
            }
        }
        return $return_data;
    }

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('offline_channel_gold_report_info.html',array(
			'view'=>new OfflineChannelGoldReportView(new OfflineChannelGoldReportModel(27))
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('offline_channel_gold_report_info.html',array(
			'view'=>new OfflineChannelGoldReportView(new OfflineChannelGoldReportModel($id,27)),
			'tab_id'=>$tab_id
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$this->render('offline_channel_gold_report_show.html',array(
			'view'=>new OfflineChannelGoldReportView(new OfflineChannelGoldReportModel($id,27)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;
		$olddo = array();
		$newdo=array();

		$newmodel =  new OfflineChannelGoldReportModel(28);
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');

		$id = _Post::getInt('id');
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;

		$newmodel =  new OfflineChannelGoldReportModel($id,28);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;	
			$result['title'] = '修改此处为想显示在页签上的字段';
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new OfflineChannelGoldReportModel($id,28);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		//联合删除？
		//$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

    //封装函数用来返回自己所属的体验店或者渠道
    public function getmyDepartment()
    {
        //获取体验店的信息
        $model = new ShopCfgChannelModel(1);
        $data = $model->getallshop();
        $List_pagt = array();
        foreach ($data as $key => $value) {
            # code...
            $List_pagt[$value['id']] = $value;
        }
        $userChannelmodel = new UserChannelModel(1);
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

    /**
     *  取出指定时间数组
     */
    public function get_data_arr($start_time,$end_time){
        $start_time_str=explode("-",$start_time);
        $end_time_str=explode("-",$end_time);
        $data_arr=array();
        while(true){
            if($start_time_str[0].$start_time_str[1].$start_time_str[2]>$end_time_str[0].$end_time_str[1].$end_time_str[2]) break;
            $data_arr[$start_time_str[0]."-".$start_time_str[1]."-".$start_time_str[2]]=$start_time_str[1]."-".$start_time_str[2];
            $start_time_str[2]++;
            $start_time_str=explode("-",date("Y-m-d",mktime(0,0,0,$start_time_str[1],$start_time_str[2],$start_time_str[0])));
        }
        return $data_arr;
    }

    //导出
    public function download($params) {

        $data = $this->_search_data($params);
        $alltyd = $data['alltyd'];
        header("Content-Type: text/html; charset=gb2312");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=".iconv('utf-8','gb2312',"线下渠道分析区分黄金非黄金").".xls");
        $csv_body="<table border='1'><tr><td></td><td colspan='8'>进店人数管理</td><td colspan='8'>进店销售额管理</td></tr>";
        $csv_body.="<tr><td>体验店</td><td>黄金实际成单数</td><td>黄金实际成交客户数（不含换购）</td><td>黄金换购人数</td><td>黄金成交率</td><td>非黄金实际成单数</td><td>非黄金实际成交客户数（不含换购）</td><td>非黄金换购人数</td><td>非黄金成交率</td><td>黄金总业绩</td><td>黄金总业绩(不含换购）</td><td>黄金客单价</td><td>黄金客单价(不含换购）</td><td>非黄金总业绩</td><td>非黄金总业绩(不含换购）</td><td>非黄金客单价</td><td>非黄金客单价（不含换购）</td></tr>";
        if ($data['data']) {

            $res = $data['data'];
            foreach ($res as $key => $val ) {
                    $csv_body.="<tr>";
                    $csv_body.="<td>".$alltyd[$key]."</td><td>".$val['gold_num']."</td><td>".$val['gold_num_hg']."</td><td>".$val['gold_not_hg_num']."</td><td>".$val['gold_chengjiaolv']."%</td><td>".$val['no_gold_num']."</td><td>".$val['no_gold_num_hg']."</td><td>".$val['no_gold_not_hg_num']."</td><td>".$val['no_gold_chengjiaolv']."%</td><td>".$val['gold_performance_total']."</td><td>".$val['gold_performance_total_hg']."</td><td>".$val['gold_kedanjia']."</td><td>".$val['gold_kedanjia_not_hg']."</td><td>".$val['no_gold_performance_total']."</td><td>".$val['no_gold_performance_total_hg']."</td><td>".$val['no_gold_kedanjia']."</td><td>".$val['no_gold_kedanjia_not_hg']."</td>";
                    $csv_body.="</tr>";
            }
            $csv_footer="</table>";
            echo $csv_body.$csv_footer;
        } else {
            echo '没有数据！';
        }

    }
}

?>