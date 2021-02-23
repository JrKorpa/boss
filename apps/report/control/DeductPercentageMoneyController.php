<?php
/**
 *  -------------------------------------------------
 *   @file		: DeductPercentageMoneyController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-09-29 11:50:09
 *   @update	:
 *  -------------------------------------------------
 */
class DeductPercentageMoneyController extends CommonController
{
	protected $smartyDebugEnabled = false;
    private   $gold_all = array('普通黄金','定价黄金');
    public $search_date = "";//查询时间
    public $limit_time = '2019-01';

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        //1、显示内容见附件：提成核算显示内容.xlsx表格
        //2、查看权限：销售顾问看自己的，有管理权限的可以看全店的
        //3、查询条件：体验店类型，销售渠道（支持多选），销售顾问，时间段（规则跟每月数据导出报表里的导出时间一致）
        //4、导出：导出格式跟页面显示内容一致
        $data = $this->getMyDepartments();
		$this->render('deduct_percentage_money_search_form.html',array('bar'=>Auth::getBar(),'allshop'=>$data));
	}

    //获取天生一对款
    public function get_tsyd_style_sn()
    {
        $model = new DeductPercentageMoneyModel(27);
        //天生一对款
        $alltsydsn = $model->getalltsydgoodssn();

        return $alltsydsn;
    }

    //查询
    public function _search_data($params)
    {
        $args = array(
            'mod'   => _Request::get("mod"),
            'con'   => substr(__CLASS__, 0, -10),
            'act'   => __FUNCTION__,
            'create_user'=>is_array($params['salse']) && isset($params['salse']) && !empty($params['salse'])?$params['salse']:_Request::getList("salse"),
            //'pay_date_start'=>_Request::get("begintime"),
            //'pay_date_end'=>_Request::get("endtime"),
            'search_date' => _Request::getString("search_date")?_Request::getString("search_date"):date("Y-m"),
            'department_id'=>_Request::get("shop_id")

        );
        if(!empty($args['search_date']) && $args['search_date'] < $this->limit_time && SYS_SCOPE == 'zhanting'){
            exit('亲，所选时间段内未查询到相关数据');
        }
        $this->search_date = $args['search_date'];
        if(!empty($args['create_user']) && strstr($args['create_user'][0], ',')){
            $args['create_user'] = explode(",", $args['create_user'][0]);
        }
        if(empty($args['department_id'])) exit('亲，请选择一家体验店');
        $person = $this->getShopPerson();
        $is_shop_manager = false;//是否店长
        $department_id = $args['department_id'];
        if(!empty($person)){
            $personleader = array_column($person,'dp_leader_name','id');
            if(isset($personleader[$department_id])){
                $shop_manager = explode(',',$personleader[$department_id]);
                if(in_array($_SESSION['userName'],$shop_manager)) $is_shop_manager = true;
            }
        }
        $is_management_user = false;
        if($_SESSION['userType'] == 1 || $is_shop_manager == true) $is_management_user = true;
        
        if(empty($args['create_user']) && $is_management_user == false){
            //exit('亲，请选择销售顾问');
            $args['create_user'] = array($_SESSION['userName']);//如果不是管理员或者该渠道店长只能查看自己的提成业绩
        }
        if(empty($args['search_date'])) exit('亲，请选择查询日期');
        //$days=$this->getDatePeriod($args['pay_date_start'],$args['pay_date_end']);
        //if($days>31) exit('亲，请查询一个月范围内的信息!');
        $where = array(
            'create_user'=>$args['create_user'],
            'department_id'=>$args['department_id'],
            //'pay_date_start'=>$args['pay_date_start'],
            //'pay_date_end'=>$args['pay_date_end'],
            'pay_date_start'=>$args['search_date']."-01 00:00:00",
            'pay_date_end'=>$args['search_date']."-31 23:59:59",
            'search_date'=>$args['search_date']
            );
        $where['salseids'] = $this->transitionUserById($where['create_user']);
        //根据渠道ID取出归属公司ID
        $managemodel = new SalesChannelsModel(1);
        $channelinfo = $managemodel->getSalesChannelsInfo(" company_id ", array('id'=>$where['department_id']));
        if(empty($channelinfo) && !isset($channelinfo[0]['company_id'])) exit("渠道归属公司不能为空");
        $company_id = $channelinfo[0]['company_id'];
        $model = new DeductPercentageMoneyModel(27);
        $mintsydmissionmodel = new ExtraMintsydMissionModel(27);//保底任务
        $pushcoefficientmodel = new ExtraPushCoefficientModel(27);//岗位档位提成系数
        $discountscopemodel = new ExtraDiscountScopeModel(27);//特价商品规则
        //取出所有保底任务
        $configuration = array();//配置数据
        $configuration['mintsydmissioninfo'] = $mintsydmissionmodel->getmintsydMissionList(array('company_id'=>$company_id, 'task_date'=>$args['search_date']));
        if(empty($configuration['mintsydmissioninfo'])) exit("查询时间段没有配置保底任务！");
        //取出岗位档位提成系数
        $configuration['extrapushcoefficient'] = $pushcoefficientmodel->select2(' * '," `dep_id` = '{$company_id}' ",'all');
        if(empty($configuration['extrapushcoefficient'])) exit("请配置该店面岗位提成系数！");
        //特价商品规则
        $configuration['pushcoefficient'] = $discountscopemodel->select2(' * ',' 1 ','all');
        //$orderdetails = $model->pageList($where);

        $monthly_model = new MonthlyExportModel(29);//每月导出拿出发货数据
        $dataarr = $monthly_model->getDataC(array('export_type'=>'fahuo'));
        $dep = array($where['department_id']);
        $monthly_where = array(
            'export_type'=>'xinzeng',
            'export_time_start'=>$where['pay_date_start'],
            'export_time_end'=>$where['pay_date_end'],
            'dep'=>$dep,
            'salse'=>$where['create_user']
            );
        //获取新增打标奖
        //（1）达标新增=新增-转退
        //（2）未达标保底任务没有新增达标奖
        //（3）是否达标保底任务：保底新增-保底转退>保底任务未达标，否则未达标
        $xinzeng_info = $monthly_model->pushMoneyInvoking($monthly_where,$dataarr);
        if(empty($xinzeng_info)){
            exit('亲，所选时间段内未查询到相关数据');
        }
        //取出销售的当月其他店面的婚博会订单
        $monthly_where['export_type'] = 'hbh';//婚博会订单
        $hbh_info = $monthly_model->pushMoneyInvoking($monthly_where,$dataarr);
        $xz_dabiao = $this->differentiateAccountingData($xinzeng_info,'make_order',$hbh_info);
        $monthly_where['export_type'] = 'zuantui';//转退业绩
        $monthly_where['salse'] = array_unique(array_column($xinzeng_info,'make_order'));
        $zhuantui_info = $monthly_model->pushMoneyInvoking($monthly_where,$dataarr);
        $zt_dabiao = $this->differentiateAccountingData($zhuantui_info,'account',array());
        //未达标保底任务没有新增达标奖、统计当期发货商品
        $monthly_where['salse'] = $where['create_user'];
        $monthly_where['export_type'] = 'fahuo';
        $sendInfo = $monthly_model->pushMoneyInvoking($monthly_where,$dataarr);

        $sendInfoList = $this->regulation_list_to_make_order($sendInfo,'make_order');
        //（1）达标新增=新增-转退
        $reachTheStandard = $this->reachTheStandardNewly($xz_dabiao,$zt_dabiao,$sendInfoList,$configuration);
        
        //$sendInfo = $model->getSendgoodsPrice($where);
        $sendgoods = $this->send_goods_price($sendInfoList,$reachTheStandard,$configuration);
        //规则
        $data = $this->combine_all($xz_dabiao, $zt_dabiao, $reachTheStandard, $sendgoods, $configuration, $where);
        return $data;
    }

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
        $data = $this->_search_data($params);
		$pageData = $orderdetails;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'deduct_percentage_money_search_page';
		$this->render('deduct_percentage_money_search_list.html',array(
			//'pa'=>Util::page($pageData),
			//'page_list'=>$orderdetails
            'data'=>$data['data'],
            'total'=>$data['total']
		));
	}

    // 统计当期发货数量和金额
    public function send_goods_price($senddata=array(), $reachTheStandard,$configuration)
    {
        $model = new DeductPercentageMoneyModel(27);
        $returninfo = array();
        if(empty($senddata)){
            return $returninfo;
        }
        //取出星耀达标配置信息
        //$xy_info = $configuration['gemxaward'];
        //1、非裸钻(成品) 2、裸钻（星耀），3、裸钻（非星耀）
        foreach ($senddata as $user => $list) {
            $send_data = $cp_info = $xy_info = $fxy_info = array();
            //pushmoney_price
            $send_data['pushmoney_price'] = 0;//默认提成金额为0
            $send_data['cp_tiji_price'] = 0;//成品计提总金额
            $send_data['xy_tiji_price'] = 0;//星耀计提总金额
            $send_data['fxy_tiji_price'] = 0;//非星耀计提总金额
            $send_data['tejia_hk_price'] = 0;//发货特价实际金额
            $send_data['tiji_price_total'] = 0;//计提总金额
            $send_data['particularly_price'] = 0;//特价商品计提金额
            $send_data['particularly_pushmoney_price'] = 0;//特价商品默认提成金额为0
            $is_dabiao = isset($reachTheStandard[$user]['is_dabiao'])?$reachTheStandard[$user]['is_dabiao']:false;
            //统计发货数据中的天生一对和星耀数量
            //$tsyd_xy_data = $this->checklzorflz($list);
            //天生一对数量 、基于已经发货的数据
            //$tsyd_num = $tsyd_xy_data['tsyddata']['num'];
            //$xy_num =   $tsyd_xy_data['xydata']['num'];
            //if($is_dabiao == true){//达标了 才计算发货提成金额
                //取出岗位及是否完成保底任务对应的提成系数：
                //（3）岗位及是否完成保底任务对应的提成系数：先稽核保底任务是否达标再读“岗位档级提成系数”配置表
                //$coefficient_money = $reachTheStandard[$user]['coefficient']['push_money_coefficient'];
                //$push_money_coefficient = isset($coefficient_money)?$coefficient_money:0;//提成系数
                $push_money_coefficient = isset($reachTheStandard[$user]['push_money_coefficient'])?$reachTheStandard[$user]['push_money_coefficient']:0;
                $gold_price = 0;
                $tejia_hk_price = 0;//特价实际发货总金额
                $gold_hk_price  = 0;//黄金实际发货总金额
                foreach ($list as $k => $val) {
                    
                    //（2）发货商品金额（不包含成品定 制码的商品）：发货数据读取的是“财务每月导出”或“每月数据导出”选定时间段的[   每月发货//]数据，商品金额=【成交价】；
                    $knockdown_price = $val['real_hk_price'];//成交价
                    //黄金提计金额
                    $type = $this->qufenGoodsType($val);
                    $goods_type = $type['goods_type'];
                    $diamond_size = $type['cart'];
                    //2017.11.27提成金额：普通黄金：3元/克，定价黄金：10元/克；金重读取发货数据中的【金重】，归类在【成品】类
                    if(in_array($val['product_type1'],$this->gold_all)){//避免金重非法
                        $gold_hk_price = bcadd($gold_hk_price, $knockdown_price, 2);
                    }
                    if(!empty($val['gold_weight']) && is_numeric($val['gold_weight'])){
                        if(in_array($val['product_type1'],$this->gold_all)){
                            $gold_jinzhong = (float) $val['gold_weight'];
                            if($val['product_type1'] == '普通黄金'){
                                $gold_price += bcmul($gold_jinzhong,3,4);
                            }else{
                                $gold_price += bcmul($gold_jinzhong,10,4);
                            }
                            continue;
                        }
                    }
                    //提成：
                    //（1）根据发货商品对应的折扣提成比例：发货数据读取的是“财务每月导出”或“每月数据导出”选定时间段的[每月发货]// 数据，折扣=【成交价】/【原价】，匹配“品类折扣提成比例”配置表的内容；
                    $discount = round(bcdiv($knockdown_price,$val['market_price'],3),2);
                    //$val['cpdzcode'] = 10023;//测试
                    if(!empty($val['cpdzcode'])){//包含成品定制码的商品
                        //根据成品定制码取渠道
                        //$style_channel = $model->getChannelByCpdzcode($val['cpdzcode']);
                        //收集特价商品、统计特价商品
                        //特价商品提成：发货数据读取的是“财务每月导出”或“每月数据导出”选定时间段的[每月发货]数据，成品定制码不为空，匹配“特价商品提成配置表”的内容，输出【成交价】*提成得到的金额；
                        //$push_money = $this->particularly_get_price($val, $discount,$style_channel ,$configuration);
                        //$send_data['particularly_pushmoney_price'] += bcmul($knockdown_price,$push_money,2);
                        //continue;
                        $special_price = 0;
                        if($goods_type == 1){//成品
                            //$special_price = bcadd($special_price, $knockdown_price,2);
                            $special_price = bcmul($knockdown_price, 0.01, 2);
                        }else{
                            //$special_price = bcadd($special_price, bcmul($knockdown_price, 0.7, 2), 2);//裸钻新增系数统一0.7
                            if(bccomp($discount, 0.76, 2) == 0 || bccomp($discount, 0.77, 2) == 0 || bccomp($discount, 0.78, 2) == 0 || $val['cpdzcode'] == 'special_dia'){
                                $special_price = bcmul($knockdown_price, 0.005, 3);
                            }elseif(bccomp($discount, 0.75, 2) == 0){
                                if(bccomp($diamond_size, 1, 2) == -1){
                                    $special_price = 60;
                                }else{
                                    $special_price = 150;
                                }
                            }else{

                            }
                        }
                        $send_data['particularly_price'] = bcadd($send_data['particularly_price'], $special_price, 2);
                        $tejia_hk_price = bcadd($tejia_hk_price, $knockdown_price, 2);//特价商品实际发货金额；
                        continue;
                    }
                    $discount_ratio = $this->mapping_discount($val, $discount);//抓取提成比例
                    //4、计算提成
                    //发货商品对应的折扣提成比例*发货商品金额（不包含成品定制码的商品）*岗位及是否完成保底任务对应的提成系数
                    //var_dump($discount_ratio,$knockdown_price,$push_money_coefficient,'--------------------------');
                    $send_data['pushmoney_price'] += bcmul(bcmul($discount_ratio,$knockdown_price,2),$push_money_coefficient,2);
                    $pushmoney_price = 0;
                    $pushmoney_price = bcmul($discount_ratio,$knockdown_price,2);
                    
                    switch ($goods_type) {
                        case '1':
                            $cp_info[] = $val;
                            $send_data['cp_tiji_price'] = bcadd($send_data['cp_tiji_price'], $pushmoney_price, 2);
                            break;
                        case '2':
                            $xy_info[] = $val;
                            $send_data['xy_tiji_price'] = bcadd($send_data['xy_tiji_price'], $pushmoney_price, 2);
                            break;
                        case '3':
                            $fxy_info[] = $val;
                            $send_data['fxy_tiji_price'] = bcadd($send_data['fxy_tiji_price'], $pushmoney_price, 2);
                            break;
                        default:
                            break;
                    }
                }
            if($is_dabiao == false) $send_data['pushmoney_price'] = 0;
            //}
            //var_dump($send_data['pushmoney_price'],'+++++++++++++++++++++++++++++');
            $send_data['push_money'] = $push_money_coefficient;
            $send_data['cp_price'] = bcadd($this->getsendgoodsmondy($cp_info),$gold_hk_price,2);
            $send_data['xy_price'] = $this->getsendgoodsmondy($xy_info);
            $send_data['fxy_price'] = $this->getsendgoodsmondy($fxy_info);
            //成品发货计提金额包含黄金计提金额
            $send_data['cp_tiji_price'] = bcadd($send_data['cp_tiji_price'], $gold_price);
            //计提总金额
            $send_data['tiji_price_total'] = bcadd(bcadd($send_data['cp_tiji_price'],$send_data['xy_tiji_price'],2),$send_data['fxy_tiji_price'],2);
            //$send_data['fxy_price'] = bcsub($this->getsendgoodsmondy($fxy_info),$this->getsendgoodsmondy($xy_info),2);
            //$send_data['price_total'] = array_sum($send_data);
            $send_data['tejia_hk_price'] = $tejia_hk_price;//特价实际金额
            $send_data['price_total'] = bcadd(bcadd(bcadd($send_data['cp_price'],$send_data['xy_price'],2),$send_data['fxy_price'],2), $tejia_hk_price, 2);//发货实际总金额
            //将黄金的计提总金额乘以挡位提成系数加入总的提成金额里；
            $gold_jiti_price = bcmul($gold_price, $push_money_coefficient, 2);
            $send_data['particularly_pushmoney_price'] = bcmul($send_data['particularly_price'], $push_money_coefficient, 2);
            $send_data['pushmoney_price'] = bcadd($send_data['pushmoney_price'], $gold_jiti_price, 2);//最终发货总提成
            $returninfo[$user] = $send_data;
        }
        return $returninfo;
        
    }

    //特价商品统计
    public function particularly_get_price($details, $dis, $style_channel, $configuration)
    {
        $push_money = 0;//默认提成为0
        $discount_scopeinfo = $configuration['pushcoefficient'];
        //区分商品类型
        $goods_type = $this->qufenGoodsType($details);
        $gtype = $goods_type['goods_type'];
        $data = array();
        foreach ($discount_scopeinfo as $key => $scope) {
            $data[$scope['dep_id']."|".$scope['style_channel_id']."|".$scope['goods_type']] = $scope;
        }
        $scheck = isset($data[$details['company_id']."|".$style_channel."|".$gtype])?$data[$details['company_id']."|".$style_channel."|".$gtype]:array();
        if(!empty($scheck)){
            if(bccomp($scheck['discount_floor'], $dis, 2) == -1 && bccomp($scheck['discount_upper'], $dis, 2) != -1){
                $push_money = $scheck['push_money'];
            }
        }
        return $push_money;
    }

    //发货商品对应的折扣提成比例
    public function mapping_discount($detailsinfo=array())
    {
        //体验店 、商品类型、折扣、石头大小
        $model = new ExtraCategoryRatioModel(27);
        //提成比例
        $pushmoney_ratio = 0;
        //区分商品类型
        $goods_type = $this->qufenGoodsType($detailsinfo);
        $g_type = $goods_type['goods_type'];
        $cart = $goods_type['cart'];
        //商品金额
        //if($detailsinfo['favorable_status']==3)
        //{
        //    $money = $detailsinfo['goods_count']*( $detailsinfo['goods_price']- $detailsinfo['favorable_price']);
        //}else{
        //    $money = $detailsinfo['goods_count'] * $detailsinfo['goods_price']; 
        //}
        $discount = round(bcdiv($detailsinfo['goods_price'],$detailsinfo['market_price'],3),2);
        $where = " dep_id = {$detailsinfo['company_id']} and goods_type = {$g_type} and discount <= '{$discount}' order by discount desc limit 1";
        $extraCategoryInfo = $model->select2(" * "," $where ","row");
        //var_dump($detailsinfo['company_id'],$discount,$g_type,$extradiscount);die;
        //$discount = '0.95';//测试
        //if(!empty($extradiscount) && bccomp($discount, $extradiscount, 2) != -1){
            //$where = " dep_id = {$detailsinfo['company_id']} and goods_type = {$g_type} ";
            //$extraCategoryInfo = $model->select2(" * "," $where ", "all");
            //foreach ($extraCategoryInfo as $key => $value) {
                //var_dump($value['dep_id'] == $detailsinfo['company_id'],$value['goods_type'] == $g_type,bccomp($value['discount'], $discount,2) == 0);die;
                if(!empty($extraCategoryInfo)){
                    if($extraCategoryInfo['dep_id'] == $detailsinfo['company_id'] && $extraCategoryInfo['goods_type'] == $g_type){
                        if(bccomp($cart,0.5,3) == -1){
                            $pushmoney_ratio = $extraCategoryInfo['pull_ratio_a'];
                        }elseif(bccomp($cart,0.5,3) != -1 && bccomp($cart,1,3) == -1){
                            $pushmoney_ratio = $extraCategoryInfo['pull_ratio_b'];
                        }elseif(bccomp($cart,1,3) != -1 && bccomp($cart,1.5,3) == -1){
                            $pushmoney_ratio = $extraCategoryInfo['pull_ratio_c'];
                        }elseif(bccomp($cart,1.5,3) != -1){
                            $pushmoney_ratio = $extraCategoryInfo['pull_ratio_d'];
                        }else{
                            
                        }
                    }
                }
            //}
        //}
        //var_dump(bcdiv($pushmoney_ratio,100,4));
        //print_r($detailsinfo);echo "<br>";       
        return bcdiv($pushmoney_ratio,100,4);
    }

    //search.1  统计当期发货数量和金额
    public function getsendgoodsmondy($sendgoodsData = array())
    {
        $allsendgoodsmoney= 0;
        if(!empty($sendgoodsData))
        {
            foreach($sendgoodsData as $sendv)
            {
                $money = $sendv['real_hk_price'];
                //商品数量
                /*$gcount = $sendv['goods_count'];
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
                }*/
                $allsendgoodsmoney = bcadd($allsendgoodsmoney,$money,2);
            }
        }
        return $allsendgoodsmoney;
    }

    //guizheng数据
    public function combine_all($xz_dabiao=array(),$zt_dabiao=array(),$standard=array(),$sendgoods=array(),$configuration=array(),$where=array())
    {   
        $userData = [];

        $xz_key = !empty($xz_dabiao)?array_keys($xz_dabiao):[];
        $zt_key = !empty($zt_dabiao)?array_keys($zt_dabiao):[];
        $st_key = !empty($standard)?array_keys($standard):[];
        $sg_key = !empty($sendgoods)?array_keys($sendgoods):[];
        $userData = array_merge($xz_key, $zt_key, $st_key, $sg_key);
        $userData = !empty($userData)?array_unique($userData):[];
        $data = array();
        $mintsydmissioninfo = $configuration['mintsydmissioninfo'];//保底任务
        $extrapushcoefficient = $configuration['extrapushcoefficient'];//岗位档位提成系数
        $pushcoefficient = $configuration['pushcoefficient'];//特价商品规则
        $gemxaward = $configuration['gemxaward'];//星耀奖励
        $immobilization = array('cp_xz_price'=>0,
            'xy_xz_price'=>0,
            'fxy_xz_price'=>0,
            'un_discount_price'=>0,
            'hbh_discount_price'=>0,
            'tj_discount_add'=>0,
            'real_total_add'=>0,
            'real_total_account'=>0);
        $sendgoods_default = array(
            'cp_price'=>0,
            'xy_price'=>0,
            'fxy_price'=>0,
            'tejia_hk_price'=>0,
            'price_total'=>0,//
            'cp_tiji_price'=>0,
            'xy_tiji_price'=>0,
            'fxy_tiji_price'=>0,
            'particularly_price'=>0,
            'tiji_price_total'=>0
            );
        $mintsydmissioninfo = $this->array_group_by($mintsydmissioninfo, 'sale_name');
        //定义统计总计
        $total = array('shouldbemadeprice'=>0,
                        'minimum_guarantee'=>0,
                        'xz_dabiao_real_total_add'=>0,
                        'xz_dabiao_hbh_discount_price'=>0,
                        'xz_dabiao_un_discount_price'=>0,
                        'xz_dabiao_cp_xz_price'=>0,
                        'xz_dabiao_xy_xz_price'=>0,
                        'xz_dabiao_fxy_xz_price'=>0,
                        'xz_dabiao_tj_discount_add'=>0,
                        'xz_dabiao_real_total_account'=>0,

                        'zt_dabiao_real_total_add'=>0,
                        'zt_dabiao_hbh_discount_price'=>0,
                        'zt_dabiao_un_discount_price'=>0,
                        'zt_dabiao_cp_xz_price'=>0,
                        'zt_dabiao_xy_xz_price'=>0,
                        'zt_dabiao_fxy_xz_price'=>0,
                        'zt_dabiao_tj_discount_add'=>0,
                        'zt_dabiao_real_total_account'=>0,

                        'reach_the_real_total_add'=>0,
                        'reach_the_hbh_discount_price'=>0,
                        'reach_the_un_discount_price'=>0,
                        'reach_the_cp_xz_price'=>0,
                        'reach_the_xy_xz_price'=>0,
                        'reach_the_fxy_xz_price'=>0,
                        'reach_the_tj_discount_add'=>0,
                        'reach_the_real_total_account'=>0,

                        'reach_the_is_dabiao'=>0,
                        'reach_the_bonus_gears'=>0,
                        'reach_the_excess_price'=>0,

                        'sendgoods_cp_price'=>0,
                        'sendgoods_xy_price'=>0,
                        'sendgoods_fxy_price'=>0,
                        'sendgoods_tejia_hk_price'=>0,
                        'sendgoods_price_total'=>0,

                        'sendgoods_cp_tiji_price'=>0,
                        'sendgoods_xy_tiji_price'=>0,
                        'sendgoods_fxy_tiji_price'=>0,
                        'sendgoods_particularly_tiji_price'=>0,
                        'sendgoods_tiji_price_total'=>0,

                        'sendgoods_push_money'=>0,
                        'sendgoods_pushmoney_price'=>0,
                        'sendgoods_particularly_pushmoney_price'=>0,

                        'reach_the_tysd_price_add_price'=>0,
                        'reach_the_tysd_price_nuprice'=>0,
                        'reach_the_tysd_price_discrepancy_price'=>0,

                        'reach_the_xyaward'=>0
            );
        $newmodel =  new DeductPercentageMoneyModel(27);
        foreach ($userData as $key => $username) {

            $in_list = $newmodel->select2(" * ", " `department_id` = '".$where['department_id']."' AND `search_date` = '".$where['search_date']."' AND `sales_name` = '{$username}' ", "row");
            $rest = array();
            if(!empty($in_list)){
                $rest['minimum_guarantee'] = $in_list['baodi_price'];
                $rest['xz_dabiao'] = array('cp_xz_price'=>$in_list['cp_add_price'],
                    'xy_xz_price'=>$in_list['lzxy_add_price'],
                    'fxy_xz_price'=>$in_list['lzfxy_add_price'],
                    'un_discount_price'=>$in_list['undiscount_add_price'],
                    'hbh_discount_price'=>$in_list['hbh_add_price'],
                    'tj_discount_add'=>$in_list['tejia_add_price'],
                    'real_total_add'=>$in_list['real_add_price'],
                    'real_total_account'=>$in_list['total_add_price']);
                $rest['zt_dabiao'] = array('cp_xz_price'=>$in_list['cp_return_price'],
                    'xy_xz_price'=>$in_list['lzxy_return_price'],
                    'fxy_xz_price'=>$in_list['lzfxy_return_price'],
                    'un_discount_price'=>$in_list['undiscount_return_price'],
                    'hbh_discount_price'=>$in_list['hbh_return_price'],
                    'tj_discount_add'=>$in_list['tejia_return_price'],
                    'real_total_add'=>$in_list['real_return_price'],
                    'real_total_account'=>$in_list['total_return_price']);
                $rest['reach_the'] = array('cp_xz_price'=>$in_list['cp_deduct_price'],
                    'xy_xz_price'=>$in_list['lzxy_deduct_price'],
                    'fxy_xz_price'=>$in_list['lzfxy_deduct_price'],
                    'un_discount_price'=>$in_list['undiscount_deduct_price'],
                    'hbh_discount_price'=>$in_list['hbh_deduct_price'],
                    'tj_discount_add'=>$in_list['tejia_deduct_price'],
                    'real_total_add'=>$in_list['real_deduct_price'],
                    'real_total_account'=>$in_list['total_deduct_price'],
                    'is_dabiao'=>$in_list['is_dabiao'],
                    'bonus_gears'=>$in_list['bonus_gears'],
                    'tysd_price'=>array('add_price'=>$in_list['tsyd_award_price'],'nuprice'=>$in_list['tsyd_punish_price'], 'discrepancy_price'=>$in_list['real_should_price']),
                    'excess_price'=>$in_list['dabiao_price'],
                    'xyaward'=>"");
                $rest['sendgoods'] = array('cp_price'=>$in_list['cp_shipments_price'],
                    'xy_price'=>$in_list['lzxy_shipments_price'],
                    'fxy_price'=>$in_list['lzfxy_shipments_price'],
                    'tejia_hk_price'=>$in_list['tejia_shipments_price'],
                    'price_total'=>$in_list['shipments_total_price'],//
                    'cp_tiji_price'=>$in_list['cp_jiti_price'],
                    'xy_tiji_price'=>$in_list['lzxy_jiti_price'],
                    'fxy_tiji_price'=>$in_list['lzfxy_jiti_price'],
                    'particularly_price'=>$in_list['tejia_jiti_price'],
                    'tiji_price_total'=>$in_list['jiti_total_price'],
                    'push_money'=>$in_list['ticheng_factor'],
                    'pushmoney_price'=>$in_list['ticheng_price'],
                    'particularly_pushmoney_price'=>$in_list['tejia_ticheng_price']);
                $rest['shouldbemadeprice'] = $in_list['should_ticheng_price'];//应发提成（元）
            }else{
                //取出保底任务
                $rest['minimum_guarantee'] = isset($mintsydmissioninfo[$username]['minimum_price'])?$mintsydmissioninfo[$username]['minimum_price']:0;
                //取出天生一对数量
                //$data[$username]['tsyd_mission'] = isset($mintsydmissioninfo[$username]['tsyd_mission'])?$mintsydmissioninfo[$username]['tsyd_mission']:0;
                $rest['xz_dabiao'] = isset($xz_dabiao[$username])?$xz_dabiao[$username]:$immobilization;
                $rest['zt_dabiao'] = isset($zt_dabiao[$username])?$zt_dabiao[$username]:$immobilization;
                $rest['reach_the'] = isset($standard[$username])?$standard[$username]:$immobilization;
                $rest['sendgoods'] = isset($sendgoods[$username])?$sendgoods[$username]:$sendgoods_default;//发货提计
                //$data[$username]['tsyd_list'] = isset($standard[$username])?$standard[$username]:$sendgoods_default;//发货提计
                //应发提成（元）(新增达标奖+提成+天生一对奖惩+星耀奖励+特价提成）
                //var_dump($rest['reach_the']['excess_price'],$rest['sendgoods']['pushmoney_price'],$rest['reach_the']['tysd_price']['discrepancy_price'],$rest['reach_the']['xyaward'],$rest['sendgoods']['particularly_pushmoney_price']);
                //新增达标奖+提成
                $show1 = bcadd($rest['reach_the']['excess_price'],$rest['sendgoods']['pushmoney_price'],2);
                //天生一对奖惩+星耀奖励
                //$show2 = bcadd($rest['reach_the']['tysd_price']['discrepancy_price'],$rest['reach_the']['xyaward'],2);
                $show2 = $rest['reach_the']['tysd_price']['discrepancy_price'];
                $shouldbemadeprice = bcadd(bcadd($show1,$show2,2),$rest['sendgoods']['particularly_pushmoney_price'],2);
                $rest['shouldbemadeprice'] = $shouldbemadeprice;//应发提成（元）
            }
            $data[$username] = $rest;
            //统计总计
            $total['shouldbemadeprice'] = bcadd($total['shouldbemadeprice'],$rest['shouldbemadeprice'],2);
            $total['xz_dabiao_real_total_add'] = bcadd($total['xz_dabiao_real_total_add'],$rest['xz_dabiao']['real_total_add'],2);
            $total['xz_dabiao_hbh_discount_price'] = bcadd($total['xz_dabiao_hbh_discount_price'],$rest['xz_dabiao']['hbh_discount_price'],2);
            $total['xz_dabiao_un_discount_price'] = bcadd($total['xz_dabiao_un_discount_price'],$rest['xz_dabiao']['un_discount_price'],2);
            $total['xz_dabiao_cp_xz_price'] = bcadd($total['xz_dabiao_cp_xz_price'],$rest['xz_dabiao']['cp_xz_price'],2);
            $total['xz_dabiao_xy_xz_price'] = bcadd($total['xz_dabiao_xy_xz_price'],$rest['xz_dabiao']['xy_xz_price'],2);
            $total['xz_dabiao_fxy_xz_price'] = bcadd($total['xz_dabiao_fxy_xz_price'],$rest['xz_dabiao']['fxy_xz_price'],2);
            $total['xz_dabiao_tj_discount_add'] = bcadd($total['xz_dabiao_tj_discount_add'],$rest['xz_dabiao']['tj_discount_add'],2);
            $total['xz_dabiao_real_total_account'] = bcadd($total['xz_dabiao_real_total_account'],$rest['xz_dabiao']['real_total_account'],2);

            $total['zt_dabiao_real_total_add'] = bcadd($total['zt_dabiao_real_total_add'],$rest['zt_dabiao']['real_total_add'],2);
            $total['zt_dabiao_hbh_discount_price'] = bcadd($total['zt_dabiao_hbh_discount_price'],$rest['zt_dabiao']['hbh_discount_price'],2);
            $total['zt_dabiao_un_discount_price'] = bcadd($total['zt_dabiao_un_discount_price'],$rest['zt_dabiao']['un_discount_price'],2);
            $total['zt_dabiao_cp_xz_price'] = bcadd($total['zt_dabiao_cp_xz_price'],$rest['zt_dabiao']['cp_xz_price'],2);
            $total['zt_dabiao_xy_xz_price'] = bcadd($total['zt_dabiao_xy_xz_price'],$rest['zt_dabiao']['xy_xz_price'],2);
            $total['zt_dabiao_fxy_xz_price'] = bcadd($total['zt_dabiao_fxy_xz_price'],$rest['zt_dabiao']['fxy_xz_price'],2);
            $total['zt_dabiao_tj_discount_add'] = bcadd($total['zt_dabiao_tj_discount_add'],$rest['zt_dabiao']['tj_discount_add'],2);
            $total['zt_dabiao_real_total_account'] = bcadd($total['zt_dabiao_real_total_account'],$rest['zt_dabiao']['real_total_account'],2);

            $total['reach_the_real_total_add'] = bcadd($total['reach_the_real_total_add'],$rest['reach_the']['real_total_add'],2);
            $total['reach_the_hbh_discount_price'] = bcadd($total['reach_the_hbh_discount_price'],$rest['reach_the']['hbh_discount_price'],2);
            $total['reach_the_un_discount_price'] = bcadd($total['reach_the_un_discount_price'],$rest['reach_the']['un_discount_price'],2);
            $total['reach_the_cp_xz_price'] = bcadd($total['reach_the_cp_xz_price'],$rest['reach_the']['cp_xz_price'],2);
            $total['reach_the_xy_xz_price'] = bcadd($total['reach_the_xy_xz_price'],$rest['reach_the']['xy_xz_price'],2);
            $total['reach_the_fxy_xz_price'] = bcadd($total['reach_the_fxy_xz_price'],$rest['reach_the']['fxy_xz_price'],2);
            $total['reach_the_tj_discount_add'] = bcadd($total['reach_the_tj_discount_add'],$rest['reach_the']['tj_discount_add'],2);
            $total['reach_the_real_total_account'] = bcadd($total['reach_the_real_total_account'],$rest['reach_the']['real_total_account'],2);

            $total['reach_the_excess_price'] = bcadd($total['reach_the_excess_price'], $rest['reach_the']['excess_price'],2);

            $total['sendgoods_cp_price'] = bcadd($total['sendgoods_cp_price'],$rest['sendgoods']['cp_price'],2);
            $total['sendgoods_xy_price'] = bcadd($total['sendgoods_xy_price'],$rest['sendgoods']['xy_price'],2);
            $total['sendgoods_fxy_price'] = bcadd($total['sendgoods_fxy_price'],$rest['sendgoods']['fxy_price'],2);
            $total['sendgoods_tejia_hk_price'] = bcadd($total['sendgoods_tejia_hk_price'],$rest['sendgoods']['tejia_hk_price'],2);
            $total['sendgoods_price_total'] = bcadd($total['sendgoods_price_total'],$rest['sendgoods']['price_total'],2);

            $total['sendgoods_cp_tiji_price'] = bcadd($total['sendgoods_cp_tiji_price'],$rest['sendgoods']['cp_tiji_price'],2);
            $total['sendgoods_xy_tiji_price'] = bcadd($total['sendgoods_xy_tiji_price'],$rest['sendgoods']['xy_tiji_price'],2);
            $total['sendgoods_fxy_tiji_price'] = bcadd($total['sendgoods_fxy_tiji_price'],$rest['sendgoods']['fxy_tiji_price'],2);
            $total['sendgoods_particularly_tiji_price'] = bcadd($total['sendgoods_particularly_tiji_price'],$rest['sendgoods']['particularly_price'],2);
            $total['sendgoods_tiji_price_total'] = bcadd($total['sendgoods_tiji_price_total'],$rest['sendgoods']['tiji_price_total'],2);

            $total['sendgoods_push_money'] = bcadd($total['sendgoods_push_money'],$rest['sendgoods']['push_money'],2);
            $total['sendgoods_pushmoney_price'] = bcadd($total['sendgoods_pushmoney_price'],$rest['sendgoods']['pushmoney_price'],2);
            $total['sendgoods_particularly_pushmoney_price'] = bcadd($total['sendgoods_particularly_pushmoney_price'],$rest['sendgoods']['particularly_pushmoney_price'],2);
            $total['reach_the_tysd_price_add_price'] = bcadd($total['reach_the_tysd_price_add_price'],$rest['reach_the']['tysd_price']['add_price'],2);
            $total['reach_the_tysd_price_nuprice'] = bcadd($total['reach_the_tysd_price_nuprice'],$rest['reach_the']['tysd_price']['nuprice'],2);
            $total['reach_the_tysd_price_discrepancy_price'] = bcadd($total['reach_the_tysd_price_discrepancy_price'],$rest['reach_the']['tysd_price']['discrepancy_price'],2);

            $total['reach_the_xyaward'] = bcadd($total['reach_the_xyaward'],$rest['reach_the']['xyaward'],2);
        }
        //$data['total'] = $total;
        return array('data'=>$data, 'total'=>$total);
    }

    //二维数组整理成以某个值为key值的整理
    public function array_group_by($arr, $key)  
    {  
        $grouped = [];
        foreach ($arr as $value) {
            $grouped[$value[$key]] = $value; 
            //$grouped[$value[$key]][] = $value; 
        }  
  
        if (func_num_args() > 2) {  
            $args = func_get_args();  
            foreach ($grouped as $key => $value) {  
                $parms = array_merge([$value], array_slice($args, 2, func_num_args()));  
                $grouped[$key] = call_user_func_array('array_group_by', $parms);  
            }  
        }  
        return $grouped;  
    }

    //达标新增=新增-转退
    public function reachTheStandardNewly($xz_dabiao=array(),$zt_dabiao=array(),$sendInfo=array(),$configuration=array())
    {
        $returnInfo = array();
        $immobilization = array('cp_xz_price'=>0,
            'xy_xz_price'=>0,
            'fxy_xz_price'=>0,
            'un_discount_price'=>0,
            'hbh_discount_price'=>0,
            'tj_discount_add'=>0,
            'real_total_add'=>0,
            'real_total_account'=>0,
            'minimum_guarantee_add'=>0,
            'tsyd_num'=>0,
            'xy_num'=>0,
            'xyaward'=>0);
        if(empty($xz_dabiao)) $xz_dabiao = array();
        if(empty($zt_dabiao)) $zt_dabiao = array();
        if(empty($sendInfo))  $sendInfo = array();
        $add_key = array_merge(array_keys($xz_dabiao),array_keys($zt_dabiao),array_keys($sendInfo));
        $add_key = array_unique($add_key);
        //$performance = $unperformance = $immobilization;
        $extrapushcoefficient = $configuration['extrapushcoefficient'];
        foreach ($add_key as $key => $username) {
            $returnInfo[$username] = array(
                'tsyd_mission'=>0,
                'cp_xz_price'=>0,
                'xy_xz_price'=>0,
                'fxy_xz_price'=>0,
                'un_discount_price'=>0,
                'hbh_discount_price'=>0,
                'tj_discount_add'=>0,
                'real_total_add'=>0,
                'real_total_account'=>0,
                'minimum_guarantee_add'=>0,
                'is_dabiao'=>true,
                'coefficient'=>array(),
                'bonus_gears'=>0,
                'excess_price'=>0,
                'push_money_coefficient'=>0,
                'tysd_price'=>array(),
                'xyaward'=>0
                );
            $performance = $unperformance = $immobilization;
            //验证是否达标
            $mintsydmissioninfo = $this->array_group_by($configuration['mintsydmissioninfo'],'sale_name');
            //取出需要达标的天生一对数量
            $returnInfo[$username]['tsyd_mission'] = isset($mintsydmissioninfo[$username]['tsyd_mission'])?$mintsydmissioninfo[$username]['tsyd_mission']:0;
            //取出星耀达标配置信息
            $dabiao_price = isset($mintsydmissioninfo[$username]['minimum_price'])?$mintsydmissioninfo[$username]['minimum_price']:0;
            $dabiao_price = bcmul($dabiao_price,10000,2);//达标配置万为单位；

            if(isset($xz_dabiao[$username])) $performance = $xz_dabiao[$username];
            if(isset($zt_dabiao[$username])) $unperformance = $zt_dabiao[$username];
            $returnInfo[$username]['cp_xz_price'] = bcsub($performance['cp_xz_price'],$unperformance['cp_xz_price'],2);
            $returnInfo[$username]['xy_xz_price'] = bcsub($performance['xy_xz_price'],$unperformance['xy_xz_price'],2);
            $returnInfo[$username]['fxy_xz_price'] = bcsub($performance['fxy_xz_price'],$unperformance['fxy_xz_price'],2);
            $returnInfo[$username]['un_discount_price'] = bcsub($performance['un_discount_price'],$unperformance['un_discount_price'],2);
            $returnInfo[$username]['hbh_discount_price'] = bcsub($performance['hbh_discount_price'],$unperformance['hbh_discount_price'],2);
            $returnInfo[$username]['tj_discount_add'] = bcsub($performance['tj_discount_add'],$unperformance['tj_discount_add'],2);
            $real_total_add = bcsub($performance['real_total_add'],$unperformance['real_total_add'],2);
            $returnInfo[$username]['real_total_add'] = $real_total_add;
            $real_total_account = bcsub($performance['real_total_account'],$unperformance['real_total_account'],2);
            $returnInfo[$username]['real_total_account'] = $real_total_account;
            $minimum_guarantee_price = bcsub($performance['minimum_guarantee_add'],$unperformance['minimum_guarantee_add'],2);
            $returnInfo[$username]['minimum_guarantee_add'] = $minimum_guarantee_price;
            $returnInfo[$username]['is_dabiao'] = true;
            //2017年10月31日变更 4、是否完成保底任务，HR规则是就按【实际总新增-转退】来稽核保底任务配置表，而不是“保底新增-保底转退”，差别就在于，非婚博会的0.7.
            if(bccomp($real_total_add, $dabiao_price, 2) == -1){//bccomp($real_total_account,$dabiao_price,2) == -1
                $returnInfo[$username]['is_dabiao'] = false;
            }
            //获取销售顾问的岗位提成系数（保底新增-保底转退）
            $returnCoefficient = $this->extra_push_coefficient($username, $minimum_guarantee_price, $extrapushcoefficient);
            $returnInfo[$username]['coefficient'] = $returnCoefficient;
            //$push_money_coefficient = isset($returnCoefficient['push_money_coefficient'])?$returnCoefficient['push_money_coefficient']:0;
            $Coefficient = $this->extra_push_coefficient($username, $real_total_account, $extrapushcoefficient);
            $returnInfo[$username]['bonus_gears'] = isset($Coefficient['bonus_gears'])?$Coefficient['bonus_gears']:0;
            $returnInfo[$username]['excess_price'] = isset($Coefficient['excess_price'])?$Coefficient['excess_price']:0;
            if($returnInfo[$username]['is_dabiao'] == false) $returnInfo[$username]['excess_price'] =0;
            $returnInfo[$username]['station'] = isset($Coefficient['station'])?$Coefficient['station']:0;
            $returnInfo[$username]['push_money_coefficient'] = isset($Coefficient['push_money_coefficient'])?$Coefficient['push_money_coefficient']:0;
            $tsyd_num = bcsub($performance['tsyd_num'],$unperformance['tsyd_num']);
            //计算天生一对奖惩
            $tsydInfo = $this->tsydAwardPunishment($tsyd_num,$returnInfo[$username]['tsyd_mission']);
            $returnInfo[$username]['tysd_price'] = $tsydInfo;
            //计算星耀奖励
            $returnInfo[$username]['xyaward'] = bcsub($performance['xyaward'],$unperformance['xyaward'],2);
        }
        return $returnInfo;
    }

    //天生一对奖惩
    public function tsydAwardPunishment($tsyd_num, $mintsydmission)
    {
        $retrunprice = array(
            'add_price'=>0,
            'nuprice'=>0,
            'discrepancy_price'=>0
            );
        //$nuprice = 0;
        //天生一对奖惩 ：卖一件+150，比任务要求少卖一件-80；
        $retrunprice['add_price'] = bcmul($tsyd_num,150,2);
        if(bccomp($tsyd_num, $mintsydmission) == -1){
            $discrepancy_num = bcsub($mintsydmission,$tsyd_num);
            $retrunprice['nuprice'] = bcmul($discrepancy_num,80,2);
        }
        $retrunprice['discrepancy_price'] = bcsub($retrunprice['add_price'],$retrunprice['nuprice'],2);
        return $retrunprice;
    }

    //岗位档位提成系数
    public function extra_push_coefficient($username, $price, $pushInfo=array())
    {
        //根据账户用户名获取所在公司
        $model = new UserModel(1);
        $user_info = $model->getCompanyByUsername($username);
        if(empty($user_info)) return array();//exit("未查到用户名：{$username}");
        //默认岗位 销售顾问
        $station = 1;//暂时默认
        $company_id = $user_info['company_id'];
        $internship = $user_info['internship'];//是否实习期 1是
        $trun_date = !empty($user_info['trun_date'])?substr($user_info['trun_date'],0,7):"";//转正日期
        $coefficientinfo = array();
        foreach ($pushInfo as $key => $value) {
            $coefficientinfo[$value['dep_id']."|".$value['station']][] = $value;
        }
        $returnCoefficient = array();
        $data = isset($coefficientinfo[$company_id."|".$station])?$coefficientinfo[$company_id."|".$station]:array();
        $data = $this->array_sort($data, 'bonus_gears');
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $add_performance_standard = bcmul($value['add_performance_standard'],10000,2); 
                if(bccomp($price,$add_performance_standard,2) == -1){
                    $returnCoefficient = $data[$key-1];
                    break;
                }
            }
            if(empty($returnCoefficient)) $returnCoefficient = array_pop($data);
        }
        if($internship || $trun_date > $this->search_date."-01"){
            $returnCoefficient['push_money_coefficient'] = 0.6;//如果未满实习期 则 提成系数默认都是0.8 time:12y1r——改为0.6 190425段君
        }
        return $returnCoefficient;
    }

    //二维数组按照指定的键值进行排序
    public function array_sort($arr,$keys,$type='asc'){  
        $keysvalue = $new_array = array();  
        foreach ($arr as $k=>$v){  
            $keysvalue[$k] = $v[$keys];  
        }  
        if($type == 'asc'){  
            asort($keysvalue);  
        }else{  
            arsort($keysvalue);  
        }  
        reset($keysvalue);  
        foreach ($keysvalue as $k=>$v){  
            $new_array[$k] = $arr[$k];  
        }  
        return $new_array;  
    }  

    /*1、新增=当月星耀新增（正常店面折扣非婚博会销售，非特价商品）×1.2+当月成品新增（正常店面折扣非婚博会销售，非特价商品）×1.5+当月非星耀裸钻新增（正常店面折扣非婚博会销售，非特价商品）×0.7+低于店面折扣产品新增（所有品类非婚博会销售，非特价商品）×0.4+婚博会销售新增×0.3+所有特价商品新增（非婚博会销售，特价商品）*/
    public function differentiateAccountingData($orderdetails=array(),$groupbyuser='',$hbh_info=array())
    {
        $orderByUser = $this->regulation_list_to_make_order($orderdetails,$groupbyuser);
        $hbhdata = $this->regulation_list_to_make_order($hbh_info,$groupbyuser);
        //查业绩
        foreach ($orderByUser as $user_name => $orderlist) {
            //排除非正常店面折扣，非婚博会销售，非特价商品
            //$order_list = $this->excludefalse($orderlist);
            //合并婚博会数据
            $hbhlist = isset($hbhdata[$user_name])?$hbhdata[$user_name]:array();
            if(!empty($hbhlist)){
                $orderlist = array_merge($orderlist,$hbhlist);//合并当前 销售其他城市婚博会销售数据
            }
            $xinzenginfo[$user_name] = $this->subdivisionType($orderlist);
        }
        return $xinzenginfo;
    }

    //整理数据 make_order
    public function regulation_list_to_make_order($data=array(),$groupbyuser)
    {
        $orderByUser = array();
        //$alltsydsn = $this->get_tsyd_style_sn();
        //维度制单人
        if(!empty($data)){
           foreach ($data as $key => $value) {
                $orderByUser[$value[$groupbyuser]][] = $value;
            } 
        }
        return $orderByUser;
    }

    //将商品信息细分
    //1，当月成品新增（正常店面折扣非婚博会销售，非特价商品）×1.5
    //2，当月星耀新增（正常店面折扣非婚博会销售，非特价商品）×1.2
    //3，当月非星耀裸钻新增（正常店面折扣非婚博会销售，非特价商品）×0.7
    //4，低于店面折扣产品新增（所有品类非婚博会销售，非特价商品）×0.4
    //5，婚博会销售新增×0.3
    //6，所有特价商品新增（非婚博会销售，特价商品）
    public function subdivisionType($order_details=array())
    {
        $order_list = $this->excludefalse($order_details);
        //var_dump(count($order_list['hbh_data'])+count($order_list['cpdz_data'])+count($order_list['no_discount'])+count($order_list['discount']));die;
        //'hbh_data' => array(),'cpdz_data' => array(),'no_discount'=>array(),'discount'=>array()
        //echo array_sum(array_column($order_list['hbh_data'],'goods_price'))."-".array_sum(array_column($order_list['cpdz_data'],'goods_price'))."-".array_sum(array_column($order_list['no_discount'],'goods_price'))."-".array_sum(array_column($order_list['discount'],'goods_price'));
        $xzData = array(
            'cp_xz_price'=>0,//成品
            'xy_xz_price'=>0,//星耀
            'fxy_xz_price'=>0,//非星耀
            'un_discount_price'=>0,//低于折扣下限金额
            'hbh_discount_price'=>0,//婚博会金额
            'tj_discount_add'=>0,//特价商品金额
            'real_total_add'=>0,//实际总新增
            'real_total_account'=>0,//核算x系数总新增
            'minimum_guarantee_add'=>0,//保底新增 、保底转退
            'tsyd_num'=>0,//天生一对数量
            'xy_num'=>0,//星耀数量
            'xyaward'=>0);//星耀奖励
        $normality_add = $this->checklzorflz($order_list['discount']);//区分 成品 天生一对 裸钻  星耀 金额和数量方法
        //黄金
        $gold_info = $this->checklzorflz($order_list['gold_list']);//黄金商品
        $gold_price = $gold_info['flsdata']['amount'];//黄金实际成交价
        //3、特价商品的新增/转退；
        //（HR规则）将上海南京东路店的裸钻特价商品，归类到【裸钻非星耀核算金额】中，并乘以0.7系数；除上海裸钻以外的其余特价商品，归类到【低于折扣下限核算金额（非婚博会）】中，但不*系数0.4，保持1。
        $tj_lz_shanghai = $this->checklzorflz($order_list['tj_lz_shanghai']);
        $tj_not_lz_shanghai = $this->checklzorflz($order_list['tj_not_lz_shanghai']);
        $xzData['tsyd_num'] = $normality_add['tsyddata']['num'];
        $xzData['xy_num'] = $normality_add['xydata']['num'];
        $cp_xz_price = bcmul($normality_add['flsdata']['amount'],1.5,2);//非黄金成品核算金额（非婚博会&高于折扣下限）
        $gold_hs_price = bcmul($gold_price, 0.3, 3);//黄金核算金额*0.3
        $xzData['cp_xz_price'] = bcadd($cp_xz_price, $gold_hs_price, 3);//成品核算金额（非婚博会&高于折扣下限）
        $xzData['xy_xz_price'] = bcmul($normality_add['xydata']['amount'],1.2,2);//裸钻星耀核算金额（非婚博会&高于折扣下限）
        //$fxy_amount = bcsub($normality_add['lsdata']['amount'],$normality_add['xydata']['amount'],2);
        $xzData['fxy_xz_price'] = bcmul(bcadd($normality_add['lsdata']['amount'],$tj_lz_shanghai['total_price'],2),0.7,2);//裸钻非星耀核算金额（非婚博会&高于折扣下限）
        $under_discount_add = $this->checklzorflz($order_list['no_discount']);
        $xzData['un_discount_price'] = bcadd(bcmul($under_discount_add['total_price'],0.5,2),$tj_not_lz_shanghai['total_price'],2);//低于折扣下限核算金额（非婚博会）
        $hbh_discount_add = $this->checklzorflz($order_list['hbh_data']);
        $xzData['hbh_discount_price'] = bcmul($hbh_discount_add['total_price'],0.3,2);//婚博会新增金额
        //$tj_discount_add = $this->checklzorflz($order_list['cpdz_data']);
        //特价商品
        $xzData['tj_discount_add'] = $this->specialGoodsToRules($order_list['cpdz_data']);

        $real_total_order_list = $this->checklzorflz($order_list['real_total_order_list']);
        
        //$xzData['tj_discount_add'] = $tj_discount_add['total_price'];
        //实际总新增
        //$real1 =  $normality_add['total_price'];//正常折扣标准的
        //$real2 =  $under_discount_add['total_price'];//低于折扣标准的
        $real3 =  bcmul($hbh_discount_add['total_price'],0.3,2);//婚博会金额
        $real4 = bcadd($real3, $gold_price, 3);//实际总新增增加黄金金额
        $xzData['real_total_add'] = bcadd($real_total_order_list['total_price'], $real4, 2);
        //$xzData['real_total_add'] = bcadd(bcadd($real1,$real2,2),$real3,2);
        //保底新增、保底转退；
        //保底新增=非婚博会所有品类商品新增+婚博会所有品类商品新增×0.3
        //保底转退=非婚博会所有品类商品转退+婚博会所有品类商品转退×0.3
        //$real3 = bcmul($real3,0.3,2);
        //保底新增 保底转退 只有婚博会 x0.3
        //$xzData['minimum_guarantee_add'] = bcadd(bcadd(bcadd($real1,$real2,2),$real3,2),$xzData['tj_discount_add'],2);
        $xzData['minimum_guarantee_add'] = bcadd(bcadd($real1,$real2,2),$real3,2);
        //总新增核算金额
        $xzData['real_total_account'] = $xzData['cp_xz_price']+$xzData['xy_xz_price']+$xzData['fxy_xz_price']+$xzData['un_discount_price']+$xzData['hbh_discount_price']+$xzData['tj_discount_add'];//+$xzData['tj_discount_add']
        //星耀奖励  
        $xzData['xyaward'] = bcadd(bcadd($normality_add['xydata']['xyaward'],$hbh_discount_add['xydata']['xyaward'],2),$tj_discount_add['xydata']['xyaward'],2);
        return $xzData;
    }

    /**
    *detaile :特价商品计算规则：
    *有成品定制码标记的订单商品        折扣         钻石大小 （ct）          新增系数    发货计提
    *成品 　   　   1   1%
    *裸钻          0.76-0.77  　   0.7 0.50%
    *裸钻             0.75    1以下                 0.7 60元/颗
    *裸钻             0.75    1以上 （含）         0.7 150元/颗
    *双十一特价钻（特殊标记区分普通以上特价裸钻）  　   　   0.7 0.50%
    **/
    public function specialGoodsToRules($information=array())
    {
        $special_price = 0;//特价商品；
        if(empty($information)) return $special_price;
        foreach ($information as $tor => $goods) {
            $discount = 1;//商品折扣
            $data = $this->qufenGoodsType($goods);
            $goods_type = $data['goods_type'];//商品类型；1|成品，2、3|裸钻；默认成品
            $diamond_size = $data['cart'];//钻石大小；

            $goods_money = $goods['goods_price'];//商品成交金额；
            $market_price = $goods['market_price'];//原价
            if($market_price <> 0) $discount = round(bcdiv($goods_money,$market_price,2),2);

            if($goods_type == 1){//成品
                $special_price = bcadd($special_price, $goods_money,2);
            }else{
                $special_price = bcadd($special_price, bcmul($goods_money, 0.7, 2), 2);//裸钻新增系数统一0.7
            }
        }
        return $special_price;
    }
    

    public function checklzorflz($orderdetails)
    {
        //var_dump($orderdetails);die;

        $alltsydsn = $this->get_tsyd_style_sn();
        if(empty($orderdetails))
        {
            return array(
                'total_price'=>0,
                'lsdata'=>array('num'=>0,'amount'=>0),
                'flsdata'=>array('num'=>0,'amount'=>0),
                'tsyddata'=>array('num'=>0,'amount'=>0),
                'xydata'=>array('num'=>0,'amount'=>0,'xyaward'=>0)
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
        $xyaward =0;

        //统计金额
        $cp_money = 0;
        $xy_money = 0;
        $fxy_money = 0;
        
        //print_r($orderdetails);
        //die();
        foreach($orderdetails as $k=>$obj)
        {
            $xq = $obj['xiangqian'];
            $num = $obj['count_num'];
            $gtype = $obj['goods_type'];
            
            $cart = $obj['cart'];   //石重
            $cert = $obj['cert'];   //证书类型
            $zhengshuhao = trim($obj['zhengshuhao'])=='' ? '空' : $obj['zhengshuhao'];
            $goods_sn = $obj['goods_sn'];
            //商品金额
            //if($obj['favorable_status']==3)
            //{
                //$money = $obj['goods_count']*( $obj['market_price']- $obj['favorable_price']);
            //}else{
                //$money = $obj['goods_count'] * $obj['market_price']; 
            //}
            $money = $obj['goods_price'];
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
                //echo $this->checkTsydSpecial($goods_sn)."-".$num."-";
                //echo '<pre>';
                //print_r($obj);
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
                //统计星耀奖励金额
                $xyaward+=$this->xingYaoAward($cart);
            }
            //总数
    
            //星耀统计
            /*if($cert == 'HRD-S')
            {
                $xynum+=$num;
                $xyamount+=$money;
            }*/
            //总数
            
            /*if($cert == 'HRD-S' && $gtype == 'lz'){
                $xy_money += $money;continue;
            }elseif($gtype == 'lz' && in_array($cert, array('DIA','GIA','IGI'))){
                $fxy_money += $money;continue;
            }else{
                $cp_money += $money;continue;
            }*/

            //区分裸石非裸石星耀 1、非裸钻 2、裸钻（星耀），3、裸钻（非星耀）
            if(is_numeric($cart) && bccomp($cart, 0.2, 3) != -1 && $gtype == 'lz'){
                if(in_array($cert, array('DIA','GIA','IGI'))){
                    $fxy_money += $money;
                    continue;
                }elseif($cert == 'HRD-S'){
                    $xy_money += $money;
                    continue;
                }else{

                }
            }
            $cp_money += $money;
        }

        $lznum = $lz_w3_num+$lz_w5_num+$lz_w6_num;
        $lzamount = $lz_w3_money + $lz_w5_money+$lz_w6_money;

        $flznum = $cp_w1_num+$cp_w2_num+$cp_w3_num+$cp_w4_num+$cp_w5_num+$cp_w6_num;
        $flzamount = $cp_w1_money+$cp_w2_money+$cp_w3_money+$cp_w4_money+$cp_w5_money+$cp_w6_money;
        
        //$tsydnum = $tsyd_w1_num+$tsyd_w2_num+$tsyd_w3_num;
        //$tsydamount = $tsyd_w1_money+$tsyd_w2_money+$tsyd_w3_money;
//var_dump($tsyd_w1_num,$tsyd_w2_num,$tsyd_w3_num);die;
        $tsydnum = $tsyd_w1_num+$tsyd_w2_num+$tsyd_w3_num;
        $tsydamount = $tsyd_w1_money+$tsyd_w2_money+$tsyd_w3_money;
        
        //$allnum =$lznum+$flznum;
        //$allamount = $lzamount+$flzamount;
        
        /*$returndata = array(
            'lsdata'=>array('num'=>$lznum,'amount'=>$lzamount),
            'flsdata'=>array('num'=>$flznum,'amount'=>$flzamount),
            'tsyddata'=>array('num'=>$tsydnum,'amount'=>$tsydamount),
            'xydata'=>array('num'=>$xynum,'amount'=>$xyamount,'xyaward'=>$xyaward)
        );*/
        $total_price = bcadd(bcadd($fxy_money,$cp_money,2),$xy_money,2);
        $returndata = array(
            'total_price'=>$total_price,
            'lsdata'=>array('num'=>$lznum,'amount'=>$fxy_money),
            'flsdata'=>array('num'=>$flznum,'amount'=>$cp_money),
            'tsyddata'=>array('num'=>$tsydnum,'amount'=>$tsydamount),
            'xydata'=>array('num'=>$xynum,'amount'=>$xy_money,'xyaward'=>$xyaward)
        );
        return $returndata;
    }

    //星耀统计、奖励
    public function xingYaoAward($cart)
    {
        $price = 0;
        $gemxawardmodel = new ExtraGemxAwardModel(27);//星耀奖励
        $gemxaward = $gemxawardmodel->select2(' * ',' 1 ','all');
        foreach ($gemxaward as $key => $val) {
            if(bccomp($val['gemx_min'], $cart, 2) != 1 && bccomp($val['gemx_max'], $cart, 2) == 1){
                $price = $val['award'];
            }
        }
        return $price;
    }
    

    //排除非正常店面折扣，非婚博会销售，非特价商品
    public function excludefalse($orderlist=array())
    {
        $return_info = array('hbh_data' => array(),'cpdz_data' => array(),'no_discount'=>array(),'discount'=>array(),'tj_lz_shanghai'=>array(),'tj_not_lz_shanghai'=>array(), 'real_total_order_list'=>array(), 'gold_list'=>array());
        if(!empty($orderlist)){
            foreach ($orderlist as $key => $value) {
                //新增和转退排除黄金产品 2017.11.27
                //if(in_array($value['product_type1'], $this->gold_all)) continue;
                if(in_array($value['product_type1'], $this->gold_all)){
                    $return_info['gold_list'][] = $value;
                    continue;
                }
                //判断商品类型
                $type = $this->qufenGoodsType($value);
                //婚博会商品
                if($value['referer'] == '婚博会'){
                    $return_info['hbh_data'][] = $value;
                    continue;
                }

                //所有非婚博会商品
                $return_info['real_total_order_list'][] = $value;

                //特价商品
                if(!empty($value['cpdzcode'])){
                    $return_info['cpdz_data'][] = $value;
                    continue;
                }
                //3、特价商品的新增/转退；
                //（HR规则）将上海南京东路店的裸钻特价商品，归类到【裸钻非星耀核算金额】中，并乘以0.7系数；
                //非上海裸钻以外的其余特价商品，归类到【低于折扣下限核算金额（非婚博会）】中，但不*系数0.4，保持1。
                /*if(!empty($value['cpdzcode'])){
                    if($value['company_id'] == 223 && $type != 1){//不等于1 裸钻
                        $return_info['tj_lz_shanghai'][] = $value;
                    }else{
                        $return_info['tj_not_lz_shanghai'][] = $value;
                    }
                    continue;
                }*/
                //非正常店面折扣
                $is_discount = $this->exclude_no_discount($value);
                if(!$is_discount){
                    $return_info['no_discount'][] = $value;
                    continue;
                }
                //正常店面折扣
                $return_info['discount'][] = $value;
            }
        }
        return $return_info;
    }

    //排除非正常店面折扣
    public function exclude_no_discount($info=array())
    {
        //if($info['cert'] == 'HRD-D') return true;//天生一对不参与折扣 2017年11月1日
        $model = new ExtraCategoryRatioModel(27);
        if(!$info['company_id']){
            echo $info['dep_name']."渠道没有归属公司";die;
        }
        //区分商品类型
        $goods_type = $this->qufenGoodsType($info);
        $g_type = $goods_type['goods_type'];
        $cart = $goods_type['cart'];
        $where_field="";
        if($g_type<>1){
            if(bccomp($cart,0.5,3) == -1){
                $where_field=" and pull_ratio_a>0";
            }elseif(bccomp($cart,0.5,3) != -1 && bccomp($cart,1,3) == -1){
                $where_field=" and pull_ratio_b>0";
            }elseif(bccomp($cart,1,3) != -1 && bccomp($cart,1.5,3) == -1){
                $where_field=" and pull_ratio_c>0";
            }elseif(bccomp($cart,1.5,3) != -1){
                $where_field=" and pull_ratio_d>0";        
            }
        }    
        if($g_type == 3){
            $extradiscount = 0.78;//18-10-22 需求变更 （2）非星耀裸钻（不分钻石大小）低于78折销售的产品，核算新增业绩系数为0.5；
        }else{
           $where = " dep_id = {$info['company_id']} and goods_type = {$g_type} {$where_field} order by discount asc limit 1";
            $extradiscount = $model->select2(" `discount` "," $where "); 
        }

        //商品金额
        //if($info['favorable_status']==3)
        //{
            //$money = $info['goods_count']*( $info['goods_price']- $info['favorable_price']);
        //}else{
            //$money = $info['goods_count'] * $info['goods_price']; 
        //}
        $money = $info['goods_price'];
        if($info['market_price'] <> 0)
            $discount = round(bcdiv($money,$info['market_price'],3),2);
        else
            $discount = 1;
        if(!empty($extradiscount) && bccomp($discount, $extradiscount, 2) != -1){
            return true;
        }else{
            return false;
        }
    }

    //区分裸石非裸石星耀 1、非裸钻 2、裸钻（星耀），3、裸钻（非星耀）
    public function qufenGoodsType($obj=array())
    {
        $cart = $obj['cart'];   //石重
        if(empty($cart)) $cart = 0;
        $gtype = $obj['goods_type']; //商品类型
        $cert = $obj['cert'];   //证书类型
        $type = 1;//非裸钻
        if(is_numeric($cart) && bccomp($cart, 0.2, 3) != -1 && $gtype == 'lz'){
            if(in_array($cert, array('DIA','GIA','IGI'))){
                $type = 3;
            }elseif($cert == 'HRD-S'){
                $type = 2;
            }else{

            }
        }
        return array('goods_type'=>$type, 'cart'=>$cart);
    }

    

    // 用户名称转换ID
    public function transitionUserById($salsename=array())
    {
        $uid = array();
        //姓名转换为ID
        $usrModel = new UserModel(1);
        if(!empty($salsename))
        {
            foreach($salsename as $v)   
            {
                $uid[] = $usrModel->getAccountId($v);
            }
            //获取顾问的id 用作退款申请人那里
            //$where['salseids'] = $uid;
        }
        return array_unique($uid);
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

    public function getShopPerson($id=''){
        $model = new SalesChannelsModel(1);
        return  $model->getShopCid($id);
    }

    //固化
    public function storageData($params)
    {
        $result = array('success' => 0,'error' =>'');
        $_cls = _Post::getInt('_cls');
        $tab_id = _Post::getInt('tab_id');
        $id = _Post::getInt('id');
        $sales_name = _Request::getList('_sale_name');
        $shop_id = _Post::getString('shop_id');
        $search_date = _Post::getString('search_date');
        if(empty($shop_id)){
            $result['error'] = '请选择一家体验店！';
            Util::jsonExit($result);
        }
        if(empty($search_date)){
            $result['error'] = '请选择查询日期！';
            Util::jsonExit($result);
        }
        $newmodel =  new DeductPercentageMoneyModel(28);
        $params['salse'] = $sales_name;
        $data = $this->_search_data($params);
        $data = isset($data['data'])?$data['data']:array();
        //$dd =new DictModel(1);
        if(!empty($data)){
            try {
                $pdo = $newmodel->db()->db();
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
                $pdo->beginTransaction(); //开启事务
                foreach ($data as $sales_name => $val) {
                    //$is_dabiao = $val['reach_the']['is_dabiao']==ture?'是':'否';
                    //$bonus_gears = $dd->getEnum('extra.bonus_gears',$val['reach_the']['bonus_gears']);
                    $in_list = $newmodel->select2(" * ", " `department_id` = '{$shop_id}' AND `search_date` = '{$search_date}' AND `sales_name` = '{$sales_name}' ", "row");
                    if(empty($in_list)){
                        $olddo = array();
                        $newdo=array(
                            'search_date' =>$search_date, //'日期',
                            'department_id' =>$shop_id, //'渠道ID',
                            'department_name' =>"", // '渠道名称',
                            'sales_name' =>$sales_name, // '销售顾问',
                            'should_ticheng_price' => $val['shouldbemadeprice'] ,//应发提成',
                            'baodi_price' => $val['minimum_guarantee'],//保底任务',
                            'real_add_price' => $val['xz_dabiao']['real_total_add'] ,//实际总新增',
                            'hbh_add_price' => $val['xz_dabiao']['hbh_discount_price'] ,//婚博会新增金额    ',
                            'undiscount_add_price' => $val['xz_dabiao']['un_discount_price'] ,//低于折扣下限新增核算金额',
                            'cp_add_price' => $val['xz_dabiao']['cp_xz_price'] ,//成品新增核算金额（非婚博会&高于折扣下限）',
                            'lzxy_add_price' => $val['xz_dabiao']['xy_xz_price'] ,//裸钻星耀新增核算金额（非婚博会&高于折扣下限）',
                            'lzfxy_add_price' => $val['xz_dabiao']['fxy_xz_price'] ,//裸钻非星耀新增核算金额（非婚博会&高于折扣下限）',
                            'tejia_add_price' => $val['xz_dabiao']['tj_discount_add'] ,//特价商品新增金额',
                            'total_add_price' => $val['xz_dabiao']['real_total_account'] ,//总新增核算金额',

                            'real_return_price' => $val['zt_dabiao']['real_total_add'] ,//实际总转退',
                            'hbh_return_price' => $val['zt_dabiao']['hbh_discount_price'] ,//婚博会转退金额',
                            'undiscount_return_price' => $val['zt_dabiao']['un_discount_price'] ,//低于折扣下限转退核算金额',
                            'cp_return_price' => $val['zt_dabiao']['cp_xz_price'] ,//成品转退核算金额（非婚博会&高于折扣下限）',
                            'lzxy_return_price' => $val['zt_dabiao']['xy_xz_price'] ,//裸钻星耀转退核算金额（非婚博会&高于折扣下限）',
                            'lzfxy_return_price' => $val['zt_dabiao']['fxy_xz_price'] ,//裸钻非星耀转退核算金额（非婚博会&高于折扣下限）',
                            'tejia_return_price' => $val['zt_dabiao']['tj_discount_add'] ,//特价商品转退金额',
                            'total_return_price' => $val['zt_dabiao']['real_total_account'],//总转退核算金额',

                            'real_deduct_price' => $val['reach_the']['real_total_add'] ,//实际总新增扣除转退',
                            'hbh_deduct_price' => $val['reach_the']['hbh_discount_price'] ,//婚博会新增扣除转退金额',
                            'undiscount_deduct_price' => $val['reach_the']['un_discount_price'] ,//低于折扣下限核算新增扣除转退金额（非婚博会）',
                            'cp_deduct_price' => $val['reach_the']['cp_xz_price'] ,//成品核算新增扣除转退金额（非婚博会&高于折扣下限）',
                            'lzxy_deduct_price' => $val['reach_the']['xy_xz_price'] ,//裸钻星耀核算新增扣除转退金额（非婚博会&高于折扣下限）',
                            'lzfxy_deduct_price' => $val['reach_the']['fxy_xz_price'] ,//裸钻非星耀核算新增扣除转退金额（非婚博会&高于折扣下限）',
                            'tejia_deduct_price' => $val['reach_the']['tj_discount_add'] ,//特价商品新增扣除转退金额',
                            'total_deduct_price' => $val['reach_the']['real_total_account'] ,//总新增扣除转退核算金额   ',

                            'is_dabiao' =>$val['reach_the']['is_dabiao'], //'是否完成新增保底任务',
                            'bonus_gears' =>$val['reach_the']['bonus_gears'], //'新增完成业绩所属档级',
                            'dabiao_price' => $val['reach_the']['excess_price'] ,//达标新增奖',

                            'cp_shipments_price' => $val['sendgoods']['cp_price'] ,//成品发货总金额',
                            'lzxy_shipments_price' => $val['sendgoods']['xy_price'] ,//裸钻星耀发货总金额',
                            'lzfxy_shipments_price' => $val['sendgoods']['fxy_price'] ,//裸钻非星耀发货总金额',
                            'tejia_shipments_price' => $val['sendgoods']['tejia_hk_price'] ,//特价商品发货总金额',
                            'shipments_total_price' => $val['sendgoods']['price_total'] ,//发货总金额',

                            'cp_jiti_price' => $val['sendgoods']['cp_tiji_price'],//成品发货计提总金额',
                            'lzxy_jiti_price' => $val['sendgoods']['xy_tiji_price'] ,//裸钻星耀发货计提总金额',
                            'lzfxy_jiti_price' => $val['sendgoods']['fxy_tiji_price'] ,//裸钻非星耀发货计提总金额',
                            'tejia_jiti_price' => $val['sendgoods']['particularly_price'] ,//特价商品发货计提总金额',
                            'jiti_total_price' => $val['sendgoods']['tiji_price_total'] ,//发货计提总金额',

                            'ticheng_factor' =>$val['sendgoods']['push_money'], //'档位提成系数',
                            'ticheng_price' => $val['sendgoods']['pushmoney_price'],//提成',
                            'tejia_ticheng_price' => $val['sendgoods']['particularly_pushmoney_price'] ,//特价商品提成',

                            'tsyd_award_price' => $val['reach_the']['tysd_price']['add_price'] ,//天生一对奖励',
                            'tsyd_punish_price' => $val['reach_the']['tysd_price']['nuprice'] ,//天生一对惩罚',
                            'real_should_price' => $val['reach_the']['tysd_price']['discrepancy_price'] ,//实际应发',
                            'xy_award_price' => 0//星耀奖励',
                        );
                        $res = $newmodel->saveData($newdo,$olddo);
                        if($res === false){
                            throw new Exception("固化失败！");
                        }
                    }
                }
                $pdo->commit();
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
            } catch (Exception $e) {
                $error = "操作失败，事物回滚！提示：".$e->getMessage();
                Util::rollbackExit($error,array($pdo));
            }
            $result['success'] = 1;
            Util::jsonExit($result);
        }
    }

    //导出
    public function download($params) {

        $model = new ShopCfgChannelModel(59);
        $cfgdata = $model->getallshop();
        $depinfo = array();
        foreach ($cfgdata as $v) {
            $depinfo[$v['id']] = $v['shop_name'];
        }
        $data = $this->_search_data($params);

        $dow_tilite = date('YmdHis').$depinfo[$params['shop_id']]."店面提成奖金统计报表";
        //$all_total = $data['all_total'];
        //$ctinfo = $this->getCustomerSourcesAll();
        header("Content-Type: text/html; charset=gb2312");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=".iconv('utf-8','gb2312',$dow_tilite).".xls");
        //$data = array();
        if(empty($data['data'])){
           echo "<table border='1'><tr>".iconv('utf-8', 'gb2312', "未查到相关数据")."</tr></table>";die;
        }
        $csv_body="<table border='1'><tr>
        <td colspan='3'></td>
        <td colspan='8'>新增业绩（元）</td>
        <td colspan='8'>转退业绩（元）</td>
        <td colspan='8'>新增扣除转退业绩（元）</td>
        <td colspan='3'></td>
        <td colspan='5'>发货金额（元）</td>
        <td colspan='5'>发货计提金额（元）</td>
        <td colspan='3'>发货提成（元）</td>
        <td colspan='3'>天生一对奖惩（元）</td>
        <td></td>
        </tr>";
        $title = array('职员姓名',
                    '应发提成（元）(新增达标奖+提成+天生一对奖惩+特价提成）',//应发提成（元）(新增达标奖+提成+天生一对奖惩+星耀奖励+特价提成）
                    '新增保底任务（万）',

                    '实际总新增',
                    '婚博会新增金额',
                    '低于折扣下限核算金额（非婚博会）',
                    '成品核算金额（非婚博会&高于折扣下限）',
                    '裸钻星耀核算金额（非婚博会&高于折扣下限）',
                    '裸钻非星耀核算金额（非婚博会&高于折扣下限）',
                    '特价商品',
                    '总新增核算金额',

                    '实际总转退',
                    '婚博会转退金额',
                    '低于折扣下限核算金额（非婚博会）',
                    '成品核算金额（非婚博会&高于折扣下限）',
                    '裸钻星耀核算金额（非婚博会&高于折扣下限）',
                    '裸钻非星耀核算金额（非婚博会&高于折扣下限）',
                    '特价商品',
                    '总转退核算金额',

                    '实际总新增扣除转退',
                    '婚博会新增扣除转退金额',
                    '低于折扣下限核算新增扣除转退金额（非婚博会）',
                    '成品核算新增扣除转退金额（非婚博会&高于折扣下限）',
                    '裸钻星耀核算新增扣除转退金额（非婚博会&高于折扣下限）',
                    '裸钻非星耀核算新增扣除转退金额（非婚博会&高于折扣下限）',
                    '特价商品',
                    '总新增扣除转退核算金额',

                    '是否完成新增保底任务',
                    '新增完成业绩所属档级',
                    '达标新增奖',

                    '成品发货总金额',
                    '裸钻星耀发货总金额',
                    '裸钻非星耀发货总金额',
                    '特价商品发货总金额',
                    '发货总金额',

                    '成品发货计提总金额',
                    '裸钻星耀发货计提总金额',
                    '裸钻非星耀发货计提总金额',
                    '特价商品发货计提总金额',
                    '发货计提总金额',

                    '档位提成系数',
                    '提成（元）',
                    '特价商品提成',

                    '奖励',
                    '惩罚',
                    '实际应发',

                    '星耀奖励（元）');
        $csv_body.="<tr><td>".implode("</td><td>", $title)."</td></tr>";
        //file_put_contents('file.txt',$csv_body);die;
        $dd =new DictModel(1);
        foreach ($data['data'] as $username => $val) {
                $is_dabiao = $val['reach_the']['is_dabiao']==ture?'是':'否';
                $bonus_gears = $dd->getEnum('extra.bonus_gears',$val['reach_the']['bonus_gears']);
                $value_trtd = array(
                    $username,
                    $val['shouldbemadeprice'],
                    $val['minimum_guarantee'],
                    $val['xz_dabiao']['real_total_add'],
                    $val['xz_dabiao']['hbh_discount_price'],
                    $val['xz_dabiao']['un_discount_price'],
                    $val['xz_dabiao']['cp_xz_price'],
                    $val['xz_dabiao']['xy_xz_price'],
                    $val['xz_dabiao']['fxy_xz_price'],
                    $val['xz_dabiao']['tj_discount_add'],
                    $val['xz_dabiao']['real_total_account'],

                    $val['zt_dabiao']['real_total_add'],
                    $val['zt_dabiao']['hbh_discount_price'],
                    $val['zt_dabiao']['un_discount_price'],
                    $val['zt_dabiao']['cp_xz_price'],
                    $val['zt_dabiao']['xy_xz_price'],
                    $val['zt_dabiao']['fxy_xz_price'],
                    $val['zt_dabiao']['tj_discount_add'],
                    $val['zt_dabiao']['real_total_account'],

                    $val['reach_the']['real_total_add'],
                    $val['reach_the']['hbh_discount_price'],
                    $val['reach_the']['un_discount_price'],
                    $val['reach_the']['cp_xz_price'],
                    $val['reach_the']['xy_xz_price'],
                    $val['reach_the']['fxy_xz_price'],
                    $val['reach_the']['tj_discount_add'],
                    $val['reach_the']['real_total_account'],

                    $is_dabiao,
                    $bonus_gears,
                    $val['reach_the']['excess_price'],

                    $val['sendgoods']['cp_price'],
                    $val['sendgoods']['xy_price'],
                    $val['sendgoods']['fxy_price'],
                    $val['sendgoods']['tejia_hk_price'],
                    $val['sendgoods']['price_total'],

                    $val['sendgoods']['cp_tiji_price'],
                    $val['sendgoods']['xy_tiji_price'],
                    $val['sendgoods']['fxy_tiji_price'],
                    $val['sendgoods']['particularly_price'],
                    $val['sendgoods']['tiji_price_total'],

                    $val['sendgoods']['push_money'],
                    $val['sendgoods']['pushmoney_price'],
                    $val['sendgoods']['particularly_pushmoney_price'],

                    $val['reach_the']['tysd_price']['add_price'],
                    $val['reach_the']['tysd_price']['nuprice'],
                    $val['reach_the']['tysd_price']['discrepancy_price'],

                    ''//$val['reach_the']['xyaward']
                    );
                $csv_body.="<tr><td>".implode("</td><td>", $value_trtd)."</td></tr>";
        }
        $total = $data['total'];//总计
        $total_title = array(
            '总计：',
            $total['shouldbemadeprice'],
            '',

            $total['xz_dabiao_real_total_add'],
            $total['xz_dabiao_hbh_discount_price'],
            $total['xz_dabiao_un_discount_price'],
            $total['xz_dabiao_cp_xz_price'],
            $total['xz_dabiao_xy_xz_price'],
            $total['xz_dabiao_fxy_xz_price'],
            $total['xz_dabiao_tj_discount_add'],
            $total['xz_dabiao_real_total_account'],

            $total['zt_dabiao_real_total_add'],
            $total['zt_dabiao_hbh_discount_price'],
            $total['zt_dabiao_un_discount_price'],
            $total['zt_dabiao_cp_xz_price'],
            $total['zt_dabiao_xy_xz_price'],
            $total['zt_dabiao_fxy_xz_price'],
            $total['zt_dabiao_tj_discount_add'],
            $total['zt_dabiao_real_total_account'],

            $total['reach_the_real_total_add'],
            $total['reach_the_hbh_discount_price'],
            $total['reach_the_un_discount_price'],
            $total['reach_the_cp_xz_price'],
            $total['reach_the_xy_xz_price'],
            $total['reach_the_fxy_xz_price'],
            $total['reach_the_tj_discount_add'],
            $total['reach_the_real_total_account'],

            '',
            '',
            $total['reach_the_excess_price'],

            $total['sendgoods_cp_price'],
            $total['sendgoods_xy_price'],
            $total['sendgoods_fxy_price'],
            $total['sendgoods_tejia_hk_price'],
            $total['sendgoods_price_total'],

            $total['sendgoods_cp_tiji_price'],
            $total['sendgoods_xy_tiji_price'],
            $total['sendgoods_fxy_tiji_price'],
            $total['sendgoods_particularly_tiji_price'],
            $total['sendgoods_tiji_price_total'],

            $total['sendgoods_push_money'],
            $total['sendgoods_pushmoney_price'],
            $total['sendgoods_particularly_pushmoney_price'],

            $total['reach_the_tysd_price_add_price'],
            $total['reach_the_tysd_price_nuprice'],
            $total['reach_the_tysd_price_discrepancy_price'],

            ''//$total['reach_the_xyaward']
            );
        $total_body="<tr><td>".implode("</td><td>", $total_title)."</td></tr>";
        $csv_footer="</table>";
        echo $csv_body.$total_body.$csv_footer;

    }
}

?>