<?php

/**
 *  -------------------------------------------------
 *   @file		: ReportBaseOrderInfoController.php
 *   @link		:  www.kela.cn
 *   @usage		:运营报表订单详情控制器
 *   @update	:
 *  -------------------------------------------------
 */
class ReportBaseOrderInfoController extends Controller {

    protected $smartyDebugEnabled = false;
    protected $whitelist = array('printorder','printorder_dz');
    protected $pay_type = array('0'=>'默认','1'=>'展厅订购','2'=>'货到付款');
	protected static $buchan_status = array('1'=>'未操作','2'=>'已布产','3'=>'生产中','4'=>'已出厂','5'=>'不需布产');
    protected $from_arr = array(
        2 => array("ad_name"=> "淘宝B店", "api_path" =>"taobaoOrderApi"),
        "taobaoC" => array("ad_name"=> "淘宝C店", "api_path" =>"taobaoOrderApi"),
        "jingdongA" => array("ad_name"=> "京东", "api_path" =>"jd_jdk_php_2"),
        "jingdongB" => array("ad_name"=> "京东/裸钻", "api_path" =>"jd_jda_php"),
        "jingdongC" => array("ad_name"=> "京东/金条", "api_path" =>"jd_jdb_php"),
        "jingdongD" => array("ad_name"=> "京东/名品手表", "api_path" =>"jd_jdc_php"),
        "jingdongE" => array("ad_name"=> "京东/欧若雅", "api_path" =>"jd_jdd_php"),
        "jingdongF" => array("ad_name"=> "京东SOP", "api_path" =>"jd_jde_php"),
        "paipai" => array("ad_name"=> "拍拍网店", "api_path" =>"paipaiOrder")
    );
    
    //获取传过来的变量
    public function getData()
    {
    	$args = array(
    			'mod'	=> _Request::get("mod"),
    			'con'	=> substr(__CLASS__, 0, -10),
    			'act'	=> __FUNCTION__,
    			'order_department'=>_Request::get("order_department"),
    			'department_id'=>_Request::get("department_id"),
    			'channel_class'=>_Request::get("channel_class"),
    			'order_type'=>_Request::get("order_type"),
    			'buchan_type'=>trim(_Request::get("buchan_type")),
    			'time_type'=>_Request::get("time_type"),
    			'start_time'=>_Request::get("start_time"),
    			'end_time'=>_Request::get("end_time"),
    			'is_delete'=>_Request::get("is_delete"),
    			'order_pay_status'=>_Request::get("order_pay_status"),
    			'send_good_status'=>_Request::get("send_good_status"),
    			'delivery_status'=>_Request::get("delivery_status"),
    			'diff_day'=>_Request::get("diff_day"),
    	);
    	return $args;
    
    }

    /**
     * 	index，搜索框
     */
    public function index($params) {
    	$args=$this->getData();
        $this->getDepartment();
        $this->getSourceList();
        $this->getCustomerSources();
        $paylist=$this->GetPaymentInfo();
        $SalesChannelsModel = new SalesChannelsModel(1);
        $this->dd = new DictView(new DictModel(1));
		$this->getSourceList();
        $this->render('report_base_order_info_search_form.html', array(
        		'bar' => Auth::getBar(),
        		'pay_type'=>$paylist, 
        		'dd' => $this->dd,
        		'buchan_status'=>self::$buchan_status,
        		'args'=>$args
        ));
    }

    public function getSourceList() {
        //渠道
		$model = new UserChannelModel(1);
		$data = $model->getChannels($_SESSION['userId'],0);
		if(empty($data)){
			die('请先联系管理员授权渠道!');
		}
		$this->assign('onlySale',count($data)==1);
        $this->assign('sales_channels_idData', $data);
    }

    /**
     * 	search，列表
     */
    public function search($params) {
        $this->getDepartment();
        $this->getSourceList();
        $pay_type = _Request::getString('pay_type')?_Request::getInt('pay_type'):'';
        $res = $this->ChannelListO();
        //由于增加了店长和销售顾问的区别
        //首先获取全部的实体店的渠道id
        $HB = $this->getShopHB();

        if ($res === true) {
            //获取全部的有效的销售渠道
            $department_id='';
        } else {
            $department_id=$res[0];
        }
        if(isset($_REQUEST['order_department'])&&!empty($_REQUEST['order_department'])){
            $department_id=$_REQUEST['order_department'];
        }
/*
        $HBid = array_column($HB,'id');
        $create_user=_Request::get('create_user');
        if(in_array($department_id,$HBid)){
            //这个渠道属于体验店 店长看全部 销售顾问看个人
            $HBleader = array_column($HB,'dp_leader_name','id');
            $dianzhang = explode(',',$HBleader[$department_id]);
            if(!in_array($_SESSION['realName'],$dianzhang)){
                //不是店长
                $department_id='';
                $create_user=$_SESSION['realName'];
            }

        }
*/
        if(_Request::get("is_post")){
        	if(isset($_POST['start_time'])) $start_time=$_POST['start_time'];
        	else $start_time='';
        	if(isset($_POST['end_time'])) $end_time=$_POST['end_time'];
        	else $end_time='';
        	if(isset($_POST['buchan_type'])) $buchan_type=$_POST['buchan_type'];
        	else $buchan_type='';
        	if(isset($_POST['channel_class'])) $channel_class=$_POST['channel_class'];
        	else $channel_class='';
        	if(isset($_POST['department_id'])) $department_id=$_POST['department_id'];
        	else $department_id='';
        	if(isset($_POST['is_delete'])) $is_delete=$_POST['is_delete'];
        	else $is_delete='2';
        	if(isset($_POST['order_pay_status'])) $order_pay_status=$_POST['order_pay_status'];
        	else $order_pay_status='';
        }
        else{
        	$start_time =_Request::get("start_time");
        	$end_time =_Request::get("end_time");
        	$buchan_type=_Request::get("buchan_type");
        	$channel_class=_Request::get("channel_class");
        	$department_id=_Request::get("department_id");
        	$is_delete=(!isset($_REQUEST['is_delete']) or isset($_REQUEST['is_delete']) && $_REQUEST['is_delete'] == '') ? 2 : _Request::getInt("is_delete");
        	$order_pay_status=_Request::get("order_pay_status");
        }
        $time_type=_Request::getString("time_type")?_Request::getString("time_type"):'add';
        $create_user =_Request::getString('create_user');
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'order_sn' => _Request::getString('order_sn'),
            'out_order_sn'=>_Request::getString('out_order_sn'),
            'create_user' =>$create_user,
            'start_time' =>$start_time,
            'end_time' =>$end_time,
            'order_status' => _Request::get("order_status"),
            'order_check_status' => _Request::get("order_check_status"),
            'order_pay_status' => $order_pay_status,
            'order_department' => $department_id,
            'department_id' => $department_id,
            'buchan_status' => _Request::get("buchan_status"),
            'delivery_status' => _Request::get("delivery_status"),
            'send_good_status' => _Request::get("send_good_status"),
            'customer_source' => _Request::get("customer_source"),
            'consignee' => _Request::get("consignee"),
            'genzong' => _Request::get("genzong"),
            'mobile' => _Request::get("mobile"),
            'is_zp' => _Request::getString("is_zp"),
        	'channel_class' =>$channel_class,
            'order_type' => _Request::getString("order_type"),
        	'buchan_type' =>$buchan_type,
            'close_order'=>isset($_REQUEST['close_order'])?0:1,
            'pay_type'=>$pay_type,
            'is_delete' => $is_delete,
            //默认显示日期
            'hbh_referer' => _Request::getString("hbh_referer"),
        	'down_info' => _Request::get("down_info"),
        );
		/* 
        if(empty($args['start_time'])&&empty($args['end_time'])){
            $args['end_time']=date('Y-m-d');
            $args['start_time']=date('Y-m-d',strtotime($args['end_time'])-15*24*3600);
        } */
        
        $page = _Request::getInt("page", 1);
        $where = array(
            'order_sn' => $args['order_sn'],
            'create_user' => $args['create_user'],
            'order_status' => $args['order_status'],
            'start_time' => $args['start_time'],
            'end_time' => $args['end_time'],
            'order_check_status' => $args['order_check_status'],
            'order_pay_status' => $args['order_pay_status'],
            'order_department' => $args['order_department'],
            'department_id' => $args['department_id'],
            'send_good_status' => $args['send_good_status'],
            'delivery_status' => $args['delivery_status'],
            'buchan_status' => $args['buchan_status'],
            'customer_source' => $args['customer_source'],
            'consignee' => $args['consignee'],
            'genzong' => $args['genzong'],
            'mobile' => $args['mobile'],
            'is_delete' => $args['is_delete'],
            'is_zp' => $args['is_zp'],
            'order_type' => $args['order_type'],
        	'buchan_type' => $args['buchan_type'],
        	'time_type'=>$time_type,
            'close_order' => $args['close_order'],
            'hbh_referer' => $args['hbh_referer'],
        	'diff_day'=>_Request::get("diff_day"),
        	'channel_class' => $args['channel_class'],
        );

        if($pay_type != ''){
            $where['pay_type'] = $pay_type;
        }
        $url='/index.php?';
        foreach($args as $key=> $value){
        	if (is_array($value)) {
        		foreach ($value AS $v) {
        			$url .= $key . '=' . $v . '&';
        		}
        	} else {
        		$url .= $key . '=' . $value . '&';
        	}
        }
        $url=trim($url,'&');
        $_SERVER["REQUEST_URI"]=$url;//清空REQUEST_URI中get时传递过来的参数，$_SERVER["REQUEST_URI"]在Util.class.php分页page方法中有用到
           
        $model = new ReportBaseOrderInfoModel(27);
        $goodsmodel = new AppOrderDetailsModel(27);

        //如果是手机号，订单号，客户姓名 属于精确查找不会走其他限制
        if($where['order_sn']||$where['consignee']||$where['mobile']){
               $where =array();
               if(!empty($args['mobile'])){
                       $where['mobile']=$args['mobile'];
               }elseif(!empty($args['order_sn'])){
			if($args['order_sn']){
            			$args['order_sn']=str_replace(" ",",",$args['order_sn']);
            			$args['order_sn']=array_filter(explode(",",$args['order_sn']));
            			$where['order_sn']=implode("','",$args['order_sn']);
                        $args['order_sn']=implode(",",$args['order_sn']);
        		}
                       //$where['order_sn']=$args['order_sn'];
               }elseif(!empty($args['consignee'])){
                       $where['consignee']=$args['consignee'];
               }
        }elseif(!empty($args['out_order_sn'])){
	   		$order_sn=$model->getOrdersnByOutsn($args['out_order_sn']);
            if(empty($order_sn)){
                //外部订单号问题
                $where = array();
                $where['order_ids']='';
				$where['close_order'] = 1;
            }else{
                $where = array();
                $order_sn= implode(",",array_column($order_sn,'id'));
                $where['order_ids']=$order_sn;
				$where['close_order'] = 1;
            }
		}
		if($args['down_info']=='down_info'){
			ini_set('memory_limit',"-1");
			set_time_limit(0);
			$data = $model->pageList($where,$page,9000000000,false);
			foreach ($data['data'] as $key => $value) {
				
			}
			$this->download($data['data']);
			exit;
		}
        $data = $model->pageList($where, $page, 30, false);
        $user_name = array();
        if ($data['data']['data']) {
            $customer_source_model = new CustomerSourcesModel(1);
            $_value = '';
            $departmentModel = new DepartmentModel(1);
            foreach ($data['data']['data'] as $k => $v) {
                $user_name[$k] = $model->getMember_Info_userId($v['user_id']);
                if (isset($v['user_id']) && !empty($v['user_id'])) {
                    if ($user_name[$k]['data'] != '未查询到此会员') {
                        $data['data']['data'][$k]['user_id'] = $user_name[$k]['data']['member_name'];
                    } else {
                        $data['data']['data'][$k]['user_id'] = '';
                    }
                } else {
                    $data['data']['data'][$k]['user_id'] = '';
                }

                $customer_source_name = $customer_source_model->getCustomerSourcesList("`source_name`", array('id' => $v['customer_source_id']));


                if (count($customer_source_name) > 0) {
                    $data['data']['data'][$k]['customer_source_name'] = $customer_source_name[0]['source_name'];
                } else {
                    $data['data']['data'][$k]['customer_source_name'] = $_value;
                }
                $data['data']['data'][$k]['department_name'] = '';
                if($v['department_id']){
                    $data['data']['data'][$k]['department_name'] = $departmentModel->getNameById($v['department_id']);
                }
                if($v['buchan_status'] == 2){
                    $orderGoods = $goodsmodel->getGoodsByOrderId(array('order_id' => $v['id']));
                    $v['buchaning'] = true;
                    $v['buchanmsg'] = '';
                    foreach($orderGoods as $og){
                        if($og['is_stock_goods'] == 0 && $og['bc_id'] ==0){
                            $v['buchanmsg'] .= "款号 {$og['goods_sn']} 还未生成布产单.";
                            $v['buchaning'] = false;
                        }
                    }
                }else{
                    $v['buchanmsg'] = '';
                    $v['buchaning'] = true;
                }
                $data['data']['data'][$k]['buchanmsg'] = $v['buchanmsg'];
                $data['data']['data'][$k]['buchaning'] = $v['buchaning'];
            }


        }

        //获取全部的有效的销售渠道
        $SalesChannelsModel = new SalesChannelsModel(1);
        $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
        //获取所有数据
        $allSalesChannelsData = array();
        foreach ($getSalesChannelsInfo as $val) {
            $allSalesChannelsData[$val['id']] = $val['channel_name'];
        }
        $payMentModel = new PaymentModel(1);
        $allPay = array_column($payMentModel->getAll(),'pay_name','id');
        $pageData = $data['data'];
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'base_order_info_search_page';
        $this->dd = new DictView(new DictModel(1));
        $this->render('report_base_order_info_search_list.html', array(
            'pa' => Util::page($pageData),
            'allSalesChannelsData' => $allSalesChannelsData,
            'page_list' => $pageData,
            'all_price' => $data['all_price'],
            'pay_type'=>$allPay,
			'buchan_status'=>self::$buchan_status,
        	'dd' => $this->dd,
        ));
    }
	public function download($data){
			//获取全部的有效的销售渠道
			$SalesChannelsModel = new SalesChannelsModel(1);
			$getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
			//获取所有数据
			$allSalesChannelsData = array();
			foreach ($getSalesChannelsInfo as $val) {
				$allSalesChannelsData[$val['id']] = $val['channel_name'];
			}
			$payMentModel = new PaymentModel(1);
			$allPay = array_column($payMentModel->getAll(),'pay_name','id');
			$dd = new DictView(new DictModel(1));
			$util=new Util();
			$title=array('订单号','客户名称','电话号码','制单人','订单金额','已付金额','未付金额','订单状态','支付状态','订购类型','布产状态','配货状态','发货状态','退款状态','订单类型','申请关闭','跟单人','推荐人','制单时间','销售渠道','客户来源','录单来源');
			$csv_data=array();
			if(isset($data['data']) ){
				foreach($data['data'] as &$val){
					$temp=array();
					$temp[]=' '.$val['order_sn'];
					$temp[]=$val['consignee'];
					$temp[]=$val['mobile'];
					$temp[]=$val['create_user'];
					$temp[]=$val['order_amount'];
					
					$temp[]=$val['money_paid'];
					$temp[]=$val['money_unpaid'];
					$temp[]=$dd->getEnum('order.order_status',$val['order_status']);
					$temp[]=$dd->getEnum('order.order_pay_status',$val['order_pay_status']);
					$order_pay_type_key=$val['order_pay_type'];
					$temp[]=isset($pay_type[$order_pay_type_key])?$pay_type[$order_pay_type_key]:'';
					$buchan_status_key=$val['buchan_status'];
					$temp[]=isset($buchan_status[$buchan_status_key])?$buchan_status[$buchan_status_key]:'';
					$temp[]=$dd->getEnum('sales.delivery_status',$val['delivery_status']);
					$temp[]=$dd->getEnum('order.send_good_status',$val['send_good_status']);
					$apply_return='';
					if($val['apply_return']==1) $apply_return='未操作';
					elseif($val['apply_return']==2) $apply_return='正在退款';
					$temp[]=$apply_return;
					$xianhuo_status='';
					if($val['is_xianhuo']==1)$xianhuo_status='现货';
					elseif($val['is_xianhuo']==2)$xianhuo_status='未选商品';
					else $xianhuo_status='期货';
					$temp[]=$xianhuo_status;
					
					$order_status_val='';
					if($val['order_status']==4) $order_status_val='已关闭';
					else{
						if($val['apply_close']==1) $order_status_val='已申请';
						else $order_status_val='未申请';
					}
					$temp[]=$order_status_val;
					
					
					$temp[]=$val['genzong'];
					$temp[]=$val['recommended'];
					$temp[]=substr($val['create_time'],0,10);
					$department_id=$val['department_id'];
					$temp[]=isset($allSalesChannelsData[$department_id])?$allSalesChannelsData[$department_id]:'';
					$temp[]=isset($val['customer_source_name'])?$val['customer_source_name']:'';
					$temp[]=$val['referer'];
					$csv_data[]=$temp;
				}
			}
			$util->downloadCsv('发货时间统计详细报表'.date('Y-m-d'),$title,$csv_data);
	}

    public function getDepartment() {
        $departmentModel = new DepartmentModel(1);
        $departmentInfo = $departmentModel->getDepartmentInfo("`id`,`name`", array('parent_id' => 3));

        $departmentData = array();
        foreach ($departmentInfo as $val) {
            $departmentData[$val['id']] = $val['name'];
        }
        $this->assign('departmentData', $departmentData);
    }

    public  function getShopHB(){
        $model = new SalesChannelsModel(1);
        return  $model->getShopCid();
    }

    /**
     *  gendan，跟单人页面
     */
    public function gendan($params) {
        $id = intval($params['id']);
        $model_s = new BaseOrderInfoModel($id, 27);
        $dep_id = $model_s->getOrderInfoById($id);
        $viewModel = new BaseOrderInfoView($model_s);
        $model = new UserChannelModel(1);
        $make_order = $model->get_user_channel_by_channel_id($dep_id['department_id']);
        //echo '<pre>';
        //print_r($viewModel);die;
        $this->render('base_order_gendan_info.html', array(
            'view' => $viewModel,
            'make_order' => $make_order,
            'tab_id' => _Request::getInt('tab_id'),
        ));
    }

    /**
     *  gendanDo，分配跟单人
     */
    public function gendanDo($params) {

        $result = array('success' => 0,'error' =>'');
        $id = _Request::getInt('id');
        $genzong = _Request::getString('genzong');
        if(empty($genzong)){
            $result['error'] = "跟单人不能为空！";
            Util::jsonExit($result);
        }
        $insert_action = array();
        $insert_action['id'] = $id;
        $insert_action['genzong'] = $genzong;
        $orderModel= new BaseOrderInfoModel(28);
        $res = $orderModel->updateOrderGenDanAction($insert_action);
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

    public function getCustomerSources() {
        //客户来源
        $CustomerSourcesModel = new CustomerSourcesModel(1);
        $CustomerSourcesList = $CustomerSourcesModel->getCustomerSourcesList("`id`,`source_name`");
        $this->assign('customer_source_list', $CustomerSourcesList);
    }

    public function getMemberByPhone() {
//     	echo 'getMemberByPhone';exit;
        $mobile = _Post::getInt('mobile');
        $where = array('member_phone' => $mobile);

        $apiModel = new ApiMemberModel();
        $user_info = $apiModel->getMemberByPhone($where);

        if ($user_info['error'] == 1) {
            Util::jsonExit(array('error' => 1));
        }

        $departmentModel = new DepartmentModel(1);
        $causeInfo = $departmentModel->getDepartmentInfo("`id`,`name`,`parent_id`", array('id' => $user_info['data']['department_id']));

        $user_info['data']['shiyebu_id'] = $causeInfo[0]['parent_id'];
        Util::jsonExit($user_info);
    }

    /*
     * 获取销售政策对应的商品
     */

    public function getStyleAttribute($good_id, $style_info) {
        $sale_attribute_arr = array('goods_sn', 'cart', 'clarity', 'color', 'zhengshuhao', 'caizhi', 'jinse', 'jinzhong', 'zhiquan');
        //款式库货号组成：款号+材质+颜色+镶口+指圈
        $goods_id_arr = explode("-", $good_id);
        $goods_sn = $good_id;
        $caizhi = '';
        $color = '';
        $xiangkou = '';
        $zhiquan = '';

        //从货号上可以知道是属性
        $color_arr = array('W' => "白色", 'Y' => "黄色", 'R' => "红色", 'C' => "玫瑰色");
        $have_attribute = array('goods_sn' => $goods_sn, 'caizhi' => '', 'jinse' => '', 'cart' => '', 'zhiquan' => '', 'clarity' => '', 'color' => '', 'jinzhong' => '', 'zhengshuhao' => '');
        if (count($goods_id_arr) > 5) {
            $goods_sn = $goods_id_arr[0];
            $caizhi = $goods_id_arr[1];
            $color = $goods_id_arr[2];
            $xiangkou = $goods_id_arr[3] / 100;
            $zhiquan = $goods_id_arr[4];
            $have_attribute = array('goods_sn' => $goods_sn, 'caizhi' => $caizhi, 'jinse' => $color_arr[$color], 'cart' => $xiangkou, 'zhiquan' => $zhiquan, 'clarity' => '', 'color' => '', 'jinzhong' => '', 'zhengshuhao' => '');
        }


        $new_attribute_arr = array();
        foreach ($style_info as $key => $val) {
            $attribute_code = $val['attribute_code'];
            $attribute_value = $val['value'];
            if (in_array($attribute_code, $sale_attribute_arr)) {
                $new_attribute_arr[$attribute_code] = $attribute_value;
            }
        }

        //把没有是属性设成空
        foreach ($sale_attribute_arr as $val) {
            if (!array_key_exists($val, $new_attribute_arr)) {
                $new_attribute_arr[$val] = "";
            }
            if (array_key_exists($val, $have_attribute)) {
                $new_attribute_arr[$val] = $have_attribute[$val];
            }
        }
        return $new_attribute_arr;
    }
    //支付方式
    public function GetPaymentInfo(){
    	$payModel=new PaymentModel(1);
    	return  $res = array_column($payModel->getEnabled(),'pay_name','id');
    }
  //返回这个操作权限的的去渠道数组
    public function ChannelListO(){
        if($_SESSION['userType']==1){
            return true;
        }
        $pre = '/([A-Z]{1})/';
        $res =preg_replace($pre,'_$1',$_GET['con']);
        $con =substr($res,1);
        $act = $_GET['act'];
        $act =preg_replace($pre,'_$1',$act);
        $pricheck =strtoupper($con.'_'.$act.'_O');

        $pris = $_SESSION['__operation_p'][3];

        $channelarr=array();
        foreach($pris as $key=>$val){
            if(array_key_exists($pricheck,$val)){
                $channelarr[]=$key;
            }
        }
        return $channelarr;

    }
}

?>
