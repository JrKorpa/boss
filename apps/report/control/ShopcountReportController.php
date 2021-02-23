<?php
/**
 *  -------------------------------------------------
 *   @file		: ShopcountReportController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Liulinyan <939942478@qq.com>
 *   @date		: 2015-08-14 10:15:23
 *   @update	:
 *  -------------------------------------------------
 */
class ShopcountReportController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist = array("downloads","showgwreport");
    public $limit_time = '2019-01-01';
	
	/**
	*	index，搜索框
	*/
	public function index ($params)
	{
		$myDepartment = $this->getmyDepartment();	
		$this->render('shopcount_search_form.html',
		array(
			'bar' => Auth::getBar(),
			'allshop'=>$myDepartment
			)
		);
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

	/**
	* 	search，列表
	*/
	public function search($params) {
		$res = $this->_searchData($params);
		$this->render('shopcount_search_list.html',
			array('alldata'=>$res['data'],'alltyd'=>$res['shops'])
		);
	}
	public function _searchData($params)
	{
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

        if($args['begintime']<$this->limit_time && SYS_SCOPE == 'zhanting'){
            $args['begintime'] = $this->limit_time;
        }

		$data_arr=$this->get_data_arr($args['begintime'],$args['endtime']);
		if(count($data_arr)>366){
			die('请查询366天范围内的信息!');
		}
        //获取两个时间段间的月份
        $moth = $this->getTimeQujian($args['begintime'],$args['endtime']);
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

		$args['department_id'] = implode(',', $tydids);
        foreach($ret as $key => $val){
            if(!in_array($key,$search_tydid)){
                unset($ret[$key]);
            }
        }

		//开始拿数据
		/*begin*/
		$Model = new TydcountReportModel(59);
        $allboke_tmp = array();
        $realboke_tmp = array();
        $yindaoshidaoboke_tmp = array();
        $yindaoboke_tmp = array();
        $allorderDis_tmp = array();
        $allorderDis_tmp_to = array();
        $allorderDis_tmp_to_num = array();
        foreach ($moth as $end_time => $farst_time) {
            //第一步拿取 全部预约
            $where = array();
            $where['create_time_start'] = $farst_time;
            $where['create_time_end'] = $end_time;
            $where['bespoke_status'] = 2;  //已经审核的
            if(!empty($args['department_id'])){
                $where['department_id'] = $args['department_id'];
            }
            $where['dis_bespoke_id'] = 1;
            $allboke_tmp[] = $Model->getBespokeInfo($where);

            //第二部拿取 实际到店数
            $where = array();
            $where['real_inshop_time_start'] = $farst_time;
            $where['real_inshop_time_end'] = $end_time;
            $where['bespoke_status'] = 2;
            if(!empty($args['department_id'])){
                $where['department_id'] = $args['department_id'];
            }
            $where['dis_bespoke_id'] = 1;
            $realboke_tmp[] = $Model->getBespokeInfo($where);
            //第三部拿取 当期应到预约的实际到店数
            $where = array();
            $where['bespoke_inshop_time_start'] = $farst_time;
            $where['bespoke_inshop_time_end'] = $end_time;
            $where['bespoke_status'] = 2;  //已经审核的
            $where['re_status'] = 1;  //到店状态
            if(!empty($args['department_id'])){
                $where['department_id'] = $args['department_id'];
            }
            $where['dis_bespoke_id'] = 1;
            $yindaoshidaoboke_tmp[] =  $Model->getBespokeInfo($where);

            //第四部拿取 预约当期应到数
            $where = array();
            $where['bespoke_inshop_time_start'] = $farst_time;
            $where['bespoke_inshop_time_end'] = $end_time;
            $where['bespoke_status'] = 2;  //已经审核的
            if(!empty($args['department_id'])){
                $where['department_id'] = $args['department_id'];
            }
            $where['dis_bespoke_id'] = 1;
            $yindaoboke_tmp[] =  $Model->getBespokeInfo($where);
            //实际成交客户数
            $where = array();
            $where['shop_type'] = $args['shop_type'];
            $where['department_id'] = $args['department_id'];
            $where['orderenter'] = $args['orderenter'];
            $where['begintime'] = $farst_time;
            $where['endtime'] = $end_time;
            $where['is_delete'] = $args['is_delete'];
            $where['dis_bespoke_id'] = 1;
            $allorderDis_tmp[]=$Model->getOrderInfoDis($where);

            //实际成交客户数 不含300以下的
            $where = array();
            $where['shop_type'] = $args['shop_type'];
            $where['department_id'] = $args['department_id'];
            $where['orderenter'] = $args['orderenter'];
            $where['begintime'] = $farst_time;
            $where['endtime'] = $end_time;
            $where['is_delete'] = $args['is_delete'];
            $allorderDis_tmp_to[]=$Model->getOrderInfoDisTo($where);

            //实际成交客户数 含300以下的
            $where = array();
            $where['shop_type'] = $args['shop_type'];
            $where['department_id'] = $args['department_id'];
            $where['orderenter'] = $args['orderenter'];
            $where['begintime'] = $farst_time;
            $where['endtime'] = $end_time;
            $where['is_delete'] = $args['is_delete'];
            $allorderDis_tmp_to_num[]=$Model->getOrderInfoDisToNum($where);
        }
        $allboke = $this->mergeNumAll($allboke_tmp);
        $realboke = $this->mergeNumAll($realboke_tmp);
        $yindaoshidaoboke = $this->mergeNumAll($yindaoshidaoboke_tmp);
        $yindaoboke = $this->mergeNumAll($yindaoboke_tmp);
        $allorderDis = $this->mergeNumDis($allorderDis_tmp);
        $allorderDis_to = $this->mergeNumDis($allorderDis_tmp_to);
        $allorderDis_to_num = $this->mergeNumDis($allorderDis_tmp_to_num);
		//第五步拿取所有的订单信息
		$allorder = $Model->getOrderInfo($args);
		
		//echo '***************订单的<br/>';
		//print_r($allorder);
		//var_dump($allorderDis);
		//die;
        //取退款金额计算总业绩
        
        $performanceModel = new PerformanceReportModel(27);
		//echo '开始整理<br/>';
		//把所有的客户来源id都拿出来
		$alltydid = array();
		$tyddata = array();

		$deplist = array();
		//新增预约
		foreach($allboke as $obj)
		{
			$tydid = $obj['department_id'];
            if(!array_key_exists($tydid,$ret)){
                continue;
            }
			$allbokenum = $obj['count'];
			$tyddata[$tydid]['allbokenum'] = $allbokenum;
			//类型信息
			$deplist[$tydid] = $tydid;
		}
		//实际到店人数
		foreach($realboke as $obj)
		{
			$tydid = $obj['department_id'];
            if(!array_key_exists($tydid,$ret)){
                continue;
            }
			$realbokenum = $obj['count'];
			$tyddata[$tydid]['realbokenum'] = $realbokenum;
			$deplist[$tydid] = $tydid;
		}
		//当期应到预约的实际到店数
		foreach($yindaoshidaoboke as $obj)
		{
			$tydid = $obj['department_id'];
            if(!array_key_exists($tydid,$ret)){
                continue;
            }
			$yindaoshidaobokenum = $obj['count'];
			$tyddata[$tydid]['yindaoshidaobokenum'] = $yindaoshidaobokenum;
			$deplist[$tydid] = $tydid;
		}
		//应到实到的
		foreach($yindaoboke as $obj)
		{
			$tydid = $obj['department_id'];
            if(!array_key_exists($tydid,$ret)){
                continue;
            }
			$yindaobokenum = $obj['count'];
			$tyddata[$tydid]['yindaobokenum'] = $yindaobokenum;
			$deplist[$tydid] = $tydid;
		}

        //实际成交
		foreach($allorderDis as $obj){
			$tydid = $obj['department_id'];
            if(!array_key_exists($tydid,$ret)){
                continue;
            }
			$dis_ordernum = $obj['dis_ordernum'];
			$tyddata[$tydid]['dis_ordernum'] = $dis_ordernum;
			$deplist[$tydid] = $tydid;
		}
        //实际成交 去除商品金额300元以下的商品
        foreach($allorderDis_to as $obj){
            $tydid = $obj['department_id'];
            if(!array_key_exists($tydid,$ret)){
                continue;
            }
            $dis_ordernum_to = $obj['dis_ordernum'];
            $tyddataTo[$tydid]['dis_ordernum_to'] = $dis_ordernum_to;
        }
        //换购人数 商品金额300元以下的商品
        foreach($allorderDis_to_num as $obj){
            $tydid = $obj['department_id'];
            if(!array_key_exists($tydid,$ret)){
                continue;
            }
            $dis_ordernum_to_num = $obj['dis_ordernum'];
            $tyddataToNum[$tydid]['dis_ordernum_to_num'] = $dis_ordernum_to_num;
        }
		foreach($allorder as $obj){
			//订单
			$tydid = $obj['department_id'];         //渠道
            if(!array_key_exists($tydid,$ret)){
                continue;
            }
			$deplist[$tydid] = $tydid;
			$obj['dis_ordernum'] = $tyddata[$tydid]['dis_ordernum'];
            $obj['dis_ordernum_to'] = $tyddataTo[$tydid]['dis_ordernum_to'];
            $obj['dis_ordernum_to_num'] = $tyddataToNum[$tydid]['dis_ordernum_to_num'];
			$orderList[$tydid] = $obj;
		}
		foreach($deplist as $key => $val){
			//订单
			$obj = array();
			if(!isset($orderList[$key])){
				$obj['department_id'] = $key;
				$obj['dis_ordernum']=0;
				$obj['ordernum']=0;
				$obj['orderamount']=0;
				$obj['goodsamount']=0;
				$obj['moneypaid']=0;
				$obj['realreturnprice']=0;
				$obj['moneyunpaid']=0;
				$obj['zpnum']=0;
                $obj['orderamounts']=0;
                $obj['dis_ordernum_to'] =0;
                $obj['dis_ordernum_to_num'] =0;
			}else{
				$obj = $orderList[$key];
			}
            
            $rp = isset($rpLists[$key])?$rpLists[$key]:0;
            //$rg = isset($rgLists[$key])?$rgLists[$key]:0;
            //退款商品总金额计算 begin
            $where = array();
            $where['shop_type'] = $args['shop_type']; 
            $where['start_time'] = $args['begintime'];
            $where['end_time'] = $args['endtime'];
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
            $where['department_id'] = $key;
            $where['is_tree'] = '';
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
			$tydid = $obj['department_id'];         //渠道
			$dis_ordernum = $obj['dis_ordernum'];           //订单总数
            $dis_ordernum_to = $obj['dis_ordernum_to'];           //订单总数
            $dis_ordernum_to_num = $obj['dis_ordernum_to_num'];           //订单总数
			$ordernum = $obj['ordernum'];           //订单总数
			$orderamount = $obj['goodsamount'] - $rg - $rp + $plus_returnprice;     //订单总额-退款金额
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
            $where = array();
            $where['department_id'] = $key;
            $where['orderenter'] = $args['orderenter'];
            $where['begintime'] = $args['begintime'];
            $where['endtime'] = $args['endtime'];
            //不含换购金额去除单件商品在300元以下的商品
            $price_hg = $Model->getOrderInfoHG($where);
            $huangouprice = $price_hg - $rgs - $rps + $plus_returnprices; //不包含换购金额
			$goodsamount = $obj['goodsamount'];     //商品总额
			$moneypaid = $obj['moneypaid'];         //已经付款
			$realreturnprice = $obj['realreturnprice'];   //实退
			$moneyunpaid = $obj['moneyunpaid'];     //未付款
			$zpnum = $obj['zpnum'];                 //赠品单数
            //赠品客户数
            $num = 0;
            $where_zp  =array();
            $where_zp['department_id'] = $key;
            $where_zp['shop_type'] = $args['shop_type'];
            $where_zp['orderenter'] = $args['orderenter'];
            $where_zp['is_delete'] = $args['is_delete'];
            foreach ($moth as $end_time => $star_time) {
                $where_zp['begintime'] = $star_time;
                $where_zp['endtime'] = $end_time;
                $num+= $Model->getZpnum($where_zp);
            }
			
			$tyddata[$tydid]['dis_ordernum']=$dis_ordernum;
			$tyddata[$tydid]['ordernum']=$ordernum;
			$tyddata[$tydid]['orderamount']=$orderamount;
			$tyddata[$tydid]['goodsamount']=$goodsamount;
			$tyddata[$tydid]['moneypaid']=$moneypaid;
			$tyddata[$tydid]['realreturnprice']=$realreturnprice;
			$tyddata[$tydid]['moneyunpaid']=$moneyunpaid;
			$tyddata[$tydid]['zpnum']=$num;
            $tyddata[$tydid]['huangouprice']=$huangouprice;
            $tyddata[$tydid]['dis_ordernum_to']=$dis_ordernum_to;
            $tyddata[$tydid]['dis_ordernum_to_num']=$dis_ordernum_to_num;

			//预约
			if(!isset($tyddata[$tydid]['allbokenum'])){
				$tyddata[$tydid]['allbokenum'] = 0;
			}
			if(!isset($tyddata[$tydid]['dis_ordernum'])){
				$tyddata[$tydid]['dis_ordernum'] = 0;
			}
			if(!isset($tyddata[$tydid]['realbokenum'])){
				$tyddata[$tydid]['realbokenum'] = 0;
			}
			if(!isset($tyddata[$tydid]['yindaoshidaobokenum'])){
				$tyddata[$tydid]['yindaoshidaobokenum'] = 0;
			}
			if(!isset($tyddata[$tydid]['yindaobokenum'])){
				$tyddata[$tydid]['yindaobokenum'] = 0;
			}
            if(!isset($tyddata[$tydid]['dis_ordernum_to'])){
                $tyddata[$tydid]['dis_ordernum_to'] = 0;
            }
            if(!isset($tyddata[$tydid]['dis_ordernum_to_num'])){
                $tyddata[$tydid]['dis_ordernum_to_num'] = 0;
            }

			//预约到店率
			$tyddata[$tydid]['daodianlv']=$tyddata[$tydid]['yindaobokenum']==0?0:round($tyddata[$tydid]['yindaoshidaobokenum']/$tyddata[$tydid]['yindaobokenum'],4)*100;
			//预约成交率
			$tyddata[$tydid]['chengjiaolv']=$tyddata[$tydid]['realbokenum']==0?0:round($tyddata[$tydid]['dis_ordernum_to']/$tyddata[$tydid]['realbokenum'],4)*100;
			//客单价
			$tyddata[$tydid]['kedanjia']=($tyddata[$tydid]['dis_ordernum_to']+$tyddata[$tydid]['dis_ordernum_to_num'])==0?0:round($tyddata[$tydid]['orderamount']/($tyddata[$tydid]['dis_ordernum_to']+$tyddata[$tydid]['dis_ordernum_to_num']),2);
            //客单价(不含换购)
            $tyddata[$tydid]['kedanjia_hg']=$tyddata[$tydid]['dis_ordernum_to']==0?0:round($tyddata[$tydid]['huangouprice']/$tyddata[$tydid]['dis_ordernum_to'],2);
		}
        //var_dump($tyddata);die;
		ksort($tyddata);
		return array('data'=>$tyddata, 'shops'=>$alltyd);
	}
	/**
	* 	downloads，下载
	*/
	public function downloads($params) {
		$res = $this->_searchData($params);
        $this->groupdownload($res['data'], $res['shops']);
		exit; 
	}
    
	//查看体验店销售顾问统计按钮
	public function showlist($params)
	{
		//获取体验店的信息
		$data = $this->getmyDepartment();
		//拿到体验店的id   (这里就是渠道的id)
		$id = intval($params["id"]);
		//就根据制单人分组就好了
		$ordermodel = new BaseOrderCountModel(27);
		//所有的销售顾问
        $begintime = _Request::getString('begintime');
        $endtime = _Request::getString('endtime');
		$allxsgw = $ordermodel->getxsgw($id,$begintime,$endtime);
		$this->render('shopcount_search_xsgw_form.html',
		array(
			'bar' => Auth::getBar(),
			'allshop'=>$data,
			'allxsgw'=>$allxsgw,
			'shop_id'=>$id
			)
		);
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
	
	
	//销售顾问统计搜索
	public function showgwreport($params)
	{
        ini_set('memory_limit','-1');
        set_time_limit(0);
		
		$args = array(
			'mod' => _Request::get("mod"),
			'con' => substr(__CLASS__, 0, -10),
			'act' => __FUNCTION__,
			'department_id' => _Request::get("shop_id"),
			'orderenter' => _Request::getString("orderenter"),
			'create_user' => _Request::getString("create_user"),
			'begintime' => _Request::get("begintime"),
			'endtime'   => _Request::get("endtime"),
			'is_delete' => 0,
            'sel_excel' => _Request::getString("sel_excel")
		);
		if(empty($args['begintime']) || empty($args['endtime']))
		{
			echo '请选择时间';exit;
		}
		if($args['begintime'] > $args['endtime'])
		{
			echo '结束时间必须大于开始时间';exit;
		}

        if($args['begintime']<$this->limit_time && SYS_SCOPE == 'zhanting'){
            $args['begintime'] = $this->limit_time;
        }
        
		$data_arr=$this->get_data_arr($args['begintime'],$args['endtime']);
        if(count($data_arr)>366){
			die('请查询366天范围内的信息!');
        }
		
		if(empty($args['department_id']))
		{
			die('亲,请选择体验店哟');
		}

        $department_id = $args['department_id'];

        $myDepartment = $this->getmyDepartment();
		$alltyd = array();
		foreach($myDepartment as $tydinfo)
		{
			$tydid = $tydinfo['id'];
			$tydname = $tydinfo['shop_name'];
            if($args['department_id'] == $tydid){
                $shopname = $tydname;
            }
		}
        if(empty($shopname)){
			die('亲,请选择体验店哟');
        }
        //获取两个时间段间的月份
        $moth = $this->getTimeQujian($args['begintime'],$args['endtime']);
		//开始拿数据
		/*begin*/
		$Model = new TydcountReportModel(59);
        $allboke_tmp = array();
        $realboke_tmp = array();
        $yindaoshidaoboke_tmp = array();
        $yindaoboke_tmp = array();
        $allorderDis_tmp = array();
        $allorderDis_tmp_to = array();
        $allorderDis_tmp_to_num = array();
		//第一步拿取 全部预约
        foreach ($moth as $end_time => $farst_time) {
            $where = array();
            $where['create_time_start'] = $farst_time;
    		$where['create_time_end'] = $end_time;
    		$where['bespoke_status'] = 2;  //已经审核的
            if(!empty($args['department_id'])){
                $where['department_id'] = $args['department_id'];
            }
            if(!empty($args['create_user'])){
                $where['make_order'] = $args['create_user'];
            }
            $where['dis_bespoke_id'] = 1;
    		$allboke_tmp[] = $Model->getBespokeInfoByMan($where);
    		//echo '全部的******************<br/>';
    		//print_r($allboke);
            //die;
    		
    		//第二部拿取 实际到店数
            $where = array();
    		$where['real_inshop_time_start'] = $farst_time;
    		$where['real_inshop_time_end'] = $end_time;
    		$where['bespoke_status'] = 2;
            if(!empty($args['department_id'])){
                $where['department_id'] = $args['department_id'];
            }
            if(!empty($args['create_user'])){
                $where['make_order'] = $args['create_user'];
            }
            $where['dis_bespoke_id'] = 1;
    		$realboke_tmp[] = $Model->getBespokeInfoByMan($where);
    		//echo '实际到的******************<br/>';
    		//print_r($realboke);
            //die;
    		
    		//第三部拿取 当期应到预约的实际到店数
            $where = array();
    		$where['bespoke_inshop_time_start'] = $farst_time;
    		$where['bespoke_inshop_time_end'] = $end_time;
    		$where['bespoke_status'] = 2;  //已经审核的
    		$where['re_status'] = 1;  //到店状态
            if(!empty($args['department_id'])){
                $where['department_id'] = $args['department_id'];
            }
            if(!empty($args['create_user'])){
                $where['make_order'] = $args['create_user'];
            }
            $where['dis_bespoke_id'] = 1;
    		$yindaoshidaoboke_tmp[] =  $Model->getBespokeInfoByMan($where);
    		//echo '应到的******************<br/>';
            
    		//第四部拿取 预约当期应到数
            $where = array();
    		$where['bespoke_inshop_time_start'] = $farst_time;
    		$where['bespoke_inshop_time_end'] = $end_time;
    		$where['bespoke_status'] = 2;  //已经审核的
            if(!empty($args['department_id'])){
                $where['department_id'] = $args['department_id'];
            }
            if(!empty($args['create_user'])){
                $where['make_order'] = $args['create_user'];
            }
            $where['dis_bespoke_id'] = 1;
    		$yindaoboke_tmp[] =  $Model->getBespokeInfoByMan($where);

            $where = array();
            $where['begintime'] = $farst_time;
            $where['endtime'] = $end_time;
            if(!empty($args['department_id'])){
                $where['department_id'] = $args['department_id'];
            }
            if(!empty($args['create_user'])){
                $where['make_order'] = $args['create_user'];
            }
            $where['orderenter'] = $args['orderenter'];
            $allorderDis_tmp[] = $Model->getOrderInfoForManDis($where);
        //实际成交客户数 不含300以下的
            $where = array();
            $where['begintime'] = $farst_time;
            $where['endtime'] = $end_time;
            if(!empty($args['department_id'])){
                $where['department_id'] = $args['department_id'];
            }
            if(!empty($args['create_user'])){
                $where['make_order'] = $args['create_user'];
            }
            $where['orderenter'] = $args['orderenter'];
            $allorderDis_tmp_to[] = $Model->getOrderInfoForManDisTo($where);

            //实际成交客户数 300以下的
            $where = array();
            $where['begintime'] = $farst_time;
            $where['endtime'] = $end_time;
            if(!empty($args['department_id'])){
                $where['department_id'] = $args['department_id'];
            }
            if(!empty($args['create_user'])){
                $where['make_order'] = $args['create_user'];
            }
            $where['orderenter'] = $args['orderenter'];
            $allorderDis_tmp_to_num[] = $Model->getOrderInfoForManDisToNum($where);
        }
        $allboke = $this->mergeNumAllMan($allboke_tmp);
        $realboke = $this->mergeNumAllMan($realboke_tmp);
        $yindaoshidaoboke = $this->mergeNumAllMan($yindaoshidaoboke_tmp);
        $yindaoboke = $this->mergeNumAllMan($yindaoboke_tmp);
        $allorderDis = $this->mergeNumAllManDis($allorderDis_tmp);
        $allorderDisTo = $this->mergeNumAllManDis($allorderDis_tmp_to);
        $allorderDisToNum = $this->mergeNumAllManDis($allorderDis_tmp_to_num);
		//echo '应到的******************<br/>';
		//print_r($yindaoboke);die;		

        //第五步拿取所有的订单信息

        $where = array();
        $where['begintime'] = $args['begintime'];
		$where['endtime'] = $args['endtime'];
        if(!empty($args['department_id'])){
            $where['department_id'] = $args['department_id'];
        }
        if(!empty($args['create_user'])){
            $where['make_order'] = $args['create_user'];
        }
        $where['orderenter'] = $args['orderenter'];
		$allorder = $Model->getOrderInfoForMan($where);
		//echo '***************订单的<br/>';
		//print_r($allorder);
		//die;

		//echo '开始整理<br/>';
		//把所有的客户来源id都拿出来
		$alltydid = array();
		$tyddata = array();

        $gwlist = array();
		//全部预约
		foreach($allboke as $k => $obj)
		{
			$tyddata[$k]['allbokenum'] = $obj;
			//类型信息
            $gwlist[$k] = $k;
		}
		//实际到店数
		foreach($realboke as $k => $obj)
		{		
			$tyddata[$k]['realbokenum'] = $obj;
            $gwlist[$k] = $k;
		}
		//当期应到预约的实际到店数
		foreach($yindaoshidaoboke as $k => $obj)
		{
			$tyddata[$k]['yindaoshidaobokenum'] = $obj;
            $gwlist[$k] = $k;
		}
		//应到实到的
		foreach($yindaoboke as $k => $obj)
		{
			$tyddata[$k]['yindaobokenum'] = $obj;
            $gwlist[$k] = $k;
		}
		
        foreach($allorderDis as $obj){
			$gwname = $obj['create_user'];
			$dis_ordernum = $obj['dis_ordernum'];
			$tyddata[$gwname]['dis_ordernum'] = $dis_ordernum;
            $deplist[$gwname] = $gwname;
        }

        foreach($allorderDisTo as $obj){
            $gwname = $obj['create_user'];
            $dis_ordernum = $obj['dis_ordernum'];
            $tyddataTo[$gwname]['dis_ordernum_to'] = $dis_ordernum;
        }

        foreach($allorderDisToNum as $obj){
            $gwname = $obj['create_user'];
            $dis_ordernum = $obj['dis_ordernum'];
            $tyddataToNum[$gwname]['dis_ordernum_to_num'] = $dis_ordernum;
        }
        foreach($allorder as $obj){
            //订单
			$gwname = $obj['create_user'];         //渠道
            $gwlist[$gwname] = $gwname;
            $obj['dis_ordernum'] = $tyddata[$gwname]['dis_ordernum'];
            $obj['dis_ordernum_to'] = $tyddataTo[$gwname]['dis_ordernum_to'];
            $obj['dis_ordernum_to_num'] = $tyddataToNum[$gwname]['dis_ordernum_to_num'];
            $orderList[$gwname] = $obj;
        }
        $performanceModel = new PerformanceReportModel(27);
        foreach($gwlist as $key => $val){
            //订单
            $gwname = $val;
            $obj = array();
            if(!isset($orderList[$val])){
                $obj['creat_user'] = $val;
                $obj['dis_ordernum']=0;
                $obj['ordernum']=0;
                $obj['orderamount']=0;
                $obj['goodsamount']=0;
                $obj['moneypaid']=0;
                $obj['realreturnprice']=0;
                $obj['moneyunpaid']=0;
                $obj['zpnum']=0;
                $obj['goodsamounts']=0;
                $obj['dis_ordernum_to']=0;
                $obj['dis_ordernum_to_num']=0;
            }else{
                $obj = $orderList[$val];
            }
            $rp = isset($rpLists[$key])?$rpLists[$key]:0;
            //$rg = isset($rgLists[$key])?$rgLists[$key]:0;
            //退款商品总金额计算 begin
            ////取退款金额计算总业绩
            $where = array();
            $where['start_time'] = $args['begintime'];
            $where['end_time'] = $args['endtime'];
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
            $where['salseids'] = $gwname;
            $where['is_tree'] = '';
            $rg = $performanceModel->getRetrunGoodsAmountA($where);
            $rp = $performanceModel->getReturnPriceA($where);
            $where['create_user'] = $gwname;
            $returngoods_orderids = $performanceModel->getRetrunGoodsOrderid($where);
            $plus_returnprice = 0;
            if(!empty($returngoods_orderids))
            {
                $oids = array_column($returngoods_orderids,'order_goods_id');
                //+跨月的(退款不退货的)总金额
                $plus_returnprice = $performanceModel->getReturnPriceA($where,$oids);
            }
			$dis_ordernum = $obj['dis_ordernum'];           //订单总数
            $dis_ordernum_to = $obj['dis_ordernum_to'];           //订单总数
            $dis_ordernum_to_num = $obj['dis_ordernum_to_num'];           //订单总数

			$ordernum = $obj['ordernum'];           //订单总数
            //var_dump($obj['goodsamount'],$rg,$rp,$plus_returnprice);die;
			$orderamount = $obj['goodsamount'] -$rg-$rp+ $plus_returnprice;     //订单总额
            //不含换购
            $where['is_tree'] = 1;
            $rgs = $performanceModel->getRetrunGoodsAmountHG($where);
            $rps = $performanceModel->getReturnPriceHG($where);
            $where['salse'] = $gwname;
            $returngoods_orderidss = $performanceModel->getRetrunGoodsOrderidHG($where);
            $plus_returnprices = 0;
            if(!empty($returngoods_orderidss))
            {
                $oids = array_column($returngoods_orderidss,'order_goods_id');
                //+跨月的(退款不退货的)总金额
                $plus_returnprices = $performanceModel->getReturnPriceHG($where,$oids);
            }
            $where = array();
            $where['department_id'] = $args['department_id'];
            $where['orderenter'] = $args['orderenter'];
            $where['begintime'] = $args['begintime'];
            $where['endtime'] = $args['endtime'];
            $where['make_order'] = $gwname;
            //不含换购金额去除单件商品在300元以下的商品
            $price_hg = $Model->getOrderInfoHG($where);
            //var_dump($price_hg,$rgs,$rps,$plus_returnprices);die;
            $huangouprice = $price_hg - $rgs - $rps + $plus_returnprices; //不包含换购金额
            $where_zp  =array();
            $where_zp['department_id'] = $args['department_id'];
            $where_zp['shop_type'] = $args['shop_type'];
            $where_zp['orderenter'] = $args['orderenter'];
            $where_zp['is_delete'] = $args['is_delete'];
            $where_zp['make_order'] = $gwname;
            $num = 0;
            foreach ($moth as $end_time => $star_time) {
                $where_zp['begintime'] = $star_time;
                $where_zp['endtime'] = $end_time;
                $num+= $Model->getZpnum($where_zp);
            }
			$goodsamount = $obj['goodsamount'];     //商品总额
            $dis_ordernum_to_num = $obj['dis_ordernum_to_num'];     //商品总额
			$moneypaid = $obj['moneypaid'];         //已经付款
			$realreturnprice = $obj['realreturnprice'];         //实退
			$moneyunpaid = $obj['moneyunpaid'];     //未付款
			$zpnum = $obj['zpnum'];                 //赠品单数

			$tyddata[$gwname]['dis_ordernum']=$dis_ordernum;
            $tyddata[$gwname]['dis_ordernum_to']=$dis_ordernum_to;
			$tyddata[$gwname]['ordernum']=$ordernum;
			$tyddata[$gwname]['orderamount']=$orderamount;
			$tyddata[$gwname]['goodsamount']=$goodsamount;
			$tyddata[$gwname]['moneypaid']=$moneypaid;
			$tyddata[$gwname]['realreturnprice']=$realreturnprice;
			$tyddata[$gwname]['moneyunpaid']=$moneyunpaid;
			$tyddata[$gwname]['zpnum']=$num;
            $tyddata[$gwname]['huangouprice']=$huangouprice;
            $tyddata[$gwname]['dis_ordernum_to_num']=$dis_ordernum_to_num;

            //预约
            if(!isset($tyddata[$gwname]['allbokenum'])){
                $tyddata[$gwname]['allbokenum'] = 0;
            }
            if(!isset($tyddata[$gwname]['realbokenum'])){
                $tyddata[$gwname]['realbokenum'] = 0;
            }
            if(!isset($tyddata[$gwname]['yindaoshidaobokenum'])){
                $tyddata[$gwname]['yindaoshidaobokenum'] = 0;
            }
            if(!isset($tyddata[$gwname]['yindaobokenum'])){
                $tyddata[$gwname]['yindaobokenum'] = 0;
            }

            //预约到店率
			$tyddata[$gwname]['daodianlv']=$tyddata[$gwname]['yindaobokenum']==0?0:round($tyddata[$gwname]['yindaoshidaobokenum']/$tyddata[$gwname]['yindaobokenum'],4)*100;
            //预约成交率
			$tyddata[$gwname]['chengjiaolv']=$tyddata[$gwname]['realbokenum']==0?0:round($tyddata[$gwname]['dis_ordernum_to']/$tyddata[$gwname]['realbokenum'],4)*100;
            //客单价
			$tyddata[$gwname]['kedanjia']=($tyddata[$tydid]['dis_ordernum_to']+$tyddata[$tydid]['dis_ordernum_to_num'])==0?0:round($tyddata[$gwname]['orderamount']/($tyddata[$tydid]['dis_ordernum_to']+$tyddata[$tydid]['dis_ordernum_to_num']),2);
             //客单价 不含换购
            $tyddata[$gwname]['kedanjia_hg']=$tyddata[$gwname]['dis_ordernum_to']==0?0:round($tyddata[$gwname]['huangouprice']/$tyddata[$gwname]['dis_ordernum_to'],2);
        }
        $gwdata = $tyddata;
        /*
        echo "<pre>";
        var_dump($gwdata);
        echo "</pre>";*/
		//为了smarty统计当前循环锁
		$alldata = array();
		$allcount = 0;
		foreach($gwdata as $k=>$v)
		{
			$tmp = '';
			$tmp[$k] = $v;
			array_push($alldata,$tmp);
			$allcount++;
		}
        if($args['sel_excel']=='excel'){
			$this->groupdownload_make_order($alldata,$shopname,$allcount);
			exit;              
        }
		$this->render('shopcount_search_gw_list.html',
			array('alldata'=>$alldata,'shopname'=>$shopname,'rownum'=>$allcount)
			);
		
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
	public function groupdownload($data,$alltyd) {


        header("Content-Type: text/html; charset=gb2312");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=".iconv('utf-8','gb2312',"线下渠道分析统计表").".xls");

        $csv_body="<table border='1'><tr><td></td><td colspan='10'>进店人数管理</td><td colspan='4'>进店销售额管理</td></tr>";
        $csv_body.="<tr><td>体验店</td><td>新增预约人数</td><td>实际到店人数</td><td>预约预计到店人数</td><td>预约实际到店人数</td><td>预约到店率</td><td>实际成单数</td><td>实际成交客户数（不含换购）</td><td>换购人数</td><td>成交率</td><td>赠品客户数</td>
        <td>总业绩</td><td>总业绩（不含换购）</td><td>客单价</td><td>客单价（不含换购）</td></tr>";
		if ($data) {

			$res = $data;

            foreach ($res as $key => $val ) {
                    $csv_body.="<tr>";
                    $csv_body.="<td>".$alltyd[$key]."</td><td>".$val['allbokenum']."</td><td>".$val['realbokenum']."</td><td>".$val['yindaobokenum']."</td><td>".$val['yindaoshidaobokenum']."</td><td>".$val['daodianlv']."%</td><td>".$val['ordernum']."</td><td>".$val['dis_ordernum_to']."</td><td>".$val['dis_ordernum_to_num']."</td><td>".$val['chengjiaolv']."%</td><td>".$val['zpnum']."</td>
                    <td>".$val['orderamount']."</td><td>".$val['huangouprice']."</td><td>".$val['kedanjia']."</td><td>".$val['kedanjia_hg']."</td>";
                    $csv_body.="</tr>";
            }
            $csv_footer="</table>";
            echo $csv_body.$csv_footer;
		} else {
			echo '没有数据！';
		}

	}

	//导出销售顾问
	public function groupdownload_make_order($data,$shopname,$allcount) {


        header("Content-Type: text/html; charset=gb2312");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=".iconv('utf-8','gb2312',"查看店面销售顾问统计").".xls");

        $csv_body="<table border='1'><tr><td></td><td></td><td colspan='9'>进店人数管理</td><td colspan='4'>进店销售额管理</td></tr>";

        $csv_body.="<tr><td>体验店</td><td>销售顾问</td><td>新增预约人数</td><td>实际到店人数</td><td>预约预计到店人数</td><td>预约实际到店人数</td><td>预约到店率</td><td>实际成单数</td><td>实际成交客户数（不含换购）</td><td>换购人数</td><td>成交率</td><td>赠品客户数</td><td>总业绩</td><td>总业绩（不含换购）</td><td>客单价</td><td>客单价（不含换购）</td></tr>";
		if ($data) {

			$res = $data;

            foreach ($res as $k => $v ) {
                foreach($v as $key=>$val){
                    $csv_body.="<tr>";
                    if($k<1){
                        $csv_body.="<td style='vertical-align:middle' rowspan='".$allcount."'>".$shopname."</td>";
                    }

                    $csv_body.="<td>".$key."</td><td>".$val['allbokenum']."</td><td>".$val['realbokenum']."</td><td>".$val['yindaobokenum']."</td><td>".$val['yindaoshidaobokenum']."</td><td>".$val['daodianlv']."%</td><td>".$val['ordernum']."</td><td>".$val['dis_ordernum_to']."</td><td>".$val['dis_ordernum_to_num']."</td><td>".$val['chengjiaolv']."%</td><td>".$val['zpnum']."</td>
                    <td>".$val['orderamount']."</td><td>".$val['huangouprice']."</td><td>".$val['kedanjia']."</td><td>".$val['kedanjia_hg']."</td>";


                    $csv_body.="</tr>";
                }
            }
            $csv_footer="</table>";
            echo $csv_body.$csv_footer;
		} else {
			echo '没有数据！';
		}

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
    public function mergeNumAll($info)
    {
        $data = array();
        $info = array_filter($info);
        if(!empty($info)){
            foreach ($info as $key => $value) {
                foreach ($value as $k => $val) {
                    $cot[$val['department_id']] += $val['count'];
                }
            }

            foreach ($cot as $key => $value) {
                $data[$key]['department_id'] = $key;
                $data[$key]['count'] = $value;
            }
        }
        return $data;
    }

    //合并数据
    public function mergeNumAllMan($info)
    {
        $data = array();
        $info = array_filter($info);
        if(!empty($info)){
            foreach ($info as $key => $value) {
                foreach ($value as $k => $val) {
                    $data[$val['accecipt_man']] += $val['count'];
                }
            }
        }
        return $data;
    }

    //合并数据
    public function mergeNumDis($info)
    {
        $data = array();
        $info = array_filter($info);
        if(!empty($info)){
            foreach ($info as $key => $value) {
                foreach ($value as $k => $val) {
                    $cot[$val['department_id']] += $val['dis_ordernum'];
                }
            }

            foreach ($cot as $key => $value) {
                $data[$key]['department_id'] = $key;
                $data[$key]['dis_ordernum'] = $value;
            }
        }
        return $data;
    }

    public function mergeNumAllManDis($info)
    {
        $data = array();
        $info = array_filter($info);
        if(!empty($info)){
            foreach ($info as $key => $value) {
                foreach ($value as $k => $val) {
                    $cot[$val['create_user']] += $val['dis_ordernum'];
                }
            }

            foreach ($cot as $key => $value) {
                $data[$key]['create_user'] = $key;
                $data[$key]['dis_ordernum'] = $value;
            }
        }
        return $data;
    }
}