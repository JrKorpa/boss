<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderWeixiuController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166422@qq.com>
 *   @date		: 2015-01-41 17:16:36
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderWeixiuController extends Controller
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }			
		//Util::M('app_order_weixiu_log','app_order',41);	//生成模型后请注释该行
		//Util::V('app_order_weixiu_log',41);	//生成视图后请注释该行
		$this->render('app_order_weixiu_search_form.html',array(
			'bar'		=>Auth::getBar(),
			'dd'		=>new DictView(new DictModel(1)),
			'pro_list'	=> $this->get_pro_list(array())
			));
	}

    public function _search_where($params)
    {
        $args = array(
            'mod'           => _Request::get("mod"),
            'con'           => substr(__CLASS__, 0, -10),
            'act'           => __FUNCTION__,
            'id'                => _Request::get('id'),
            'order_sn'          => _Request::get('order_sn'),
            'rec_id'            => _Request::get('rec_id'),
            'consignee'         => _Request::get('consignee'),
            're_type'           => _Request::get('re_type'),
            'repair_act'        => _Request::get('repair_act'),
            'repair_factory'    => _Request::get('repair_factory'),
            'status'            => _Request::get('status'),
            'order_time_s'      =>_Request::get('order_time_s'),
            'order_time_e'      =>_Request::get('order_time_e'),
            'confirm_time_s'    =>_Request::get('confirm_time_s'),
            'confirm_time_e'    =>_Request::get('confirm_time_e'),
            'factory_time_s'    =>_Request::get('factory_time_s'),
            'factory_time_e'    =>_Request::get('factory_time_e'),
            're_end_time_s'     =>_Request::get('re_end_time_s'),
            're_end_time_e'     =>_Request::get('re_end_time_e'),
            'receiving_time_s'  =>_Request::get('receiving_time_s'),
            'receiving_time_e'  =>_Request::get('receiving_time_e'),
            
            // 2015-10-24增加
            'qc_status'         =>_Request::get('qc_status'),
            'qc_times'          =>_Request::get('qc_times'),
            'qc_nopass_dt_s'    =>_Request::get('qc_nopass_dt_s'),
            'qc_nopass_dt_e'    =>_Request::get('qc_nopass_dt_e'),

            'out_goods_s'    =>_Request::get('out_goods_s'),
            'out_goods_e'    =>_Request::get('out_goods_e'),

            'is_overdue'    =>_Request::get('is_overdue'),//1是 2否
            'channel_class'     =>_Request::get('channel_class')//boss-1135
            
        );
    
        $where  = array(
            'id'                =>  $args['id'],
            'order_sn'          =>  $args['order_sn'],
            'rec_id'            =>  $args['rec_id'],
            'consignee'         =>  $args['consignee'],
            're_type'           =>  $args['re_type'],
            'repair_act'        =>  $args['repair_act'],
            'repair_factory'    =>  $args['repair_factory'],
            'status'            =>  $args['status'],
            'order_time_s'      =>  $args['order_time_s'],
            'order_time_e'      =>  $args['order_time_e'],
            'confirm_time_s'    =>  $args['confirm_time_s'],
            'confirm_time_e'    =>  $args['confirm_time_e'],
            'factory_time_s'    =>  $args['factory_time_s'],
            'factory_time_e'    =>  $args['factory_time_e'],
            're_end_time_s'     =>  $args['re_end_time_s'],
            're_end_time_e'     =>  $args['re_end_time_e'],
            'receiving_time_s'  =>  $args['receiving_time_s'],
            'receiving_time_e'  =>  $args['receiving_time_e'],
            
            'qc_status'     =>  $args['qc_status'],
            'qc_times'      =>  $args['qc_times'],
            'qc_nopass_dt_s'    =>  $args['qc_nopass_dt_s'],
            'qc_nopass_dt_e'    =>  $args['qc_nopass_dt_e'],

            'out_goods_s'    =>$args['out_goods_s'],
            'out_goods_e'    =>$args['out_goods_e'],

            'is_overdue' => $args['is_overdue'],
            'channel_class'    =>  $args['channel_class']
            
            );

        return array('where'=>$where,'args'=>$args);
    }

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$whereinfo = $this->_search_where($params);
        $where = $whereinfo['where'];
        $args = $whereinfo['args'];
        $_page = 10;
        if(!empty($where['is_overdue']) || !empty($where['out_goods_s']) || !empty($where['out_goods_e'])){
            $_page = 50;
        }
		$page   = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$model			= new AppOrderWeixiuModel(41);
		$pro_list		= $this->get_pro_list(array());//全部供应商
		$data			= $model->pageList($where,$page,$_page,false);
		 
		$pageData		= $data;
		$pageData['filter']		= $args;
		$pageData['jsFuncs']	= 'app_order_weixiu_search_page';
		$view =	new AppOrderWeixiuView(new AppOrderWeixiuModel(41));

		$this->render('app_order_weixiu_search_list.html',array(
			'pa'		=>	Util::page($pageData),
			'page_list' =>$data,
			'dd'		=>new DictView(new DictModel(1)),
			'pro_list'	=>$pro_list,
			'view'		=>$view

		));
	}

	//数据清洗的办法
	// http://cuteframe.kela.cn/index.php?mod=repairorder&con=AppOrderWeixiu&act=update_qc_data
	public function update_qc_data ()
	{
		
		
		// 浏览器执行时间不限制
		set_time_limit(0);
		$model = new AppOrderWeixiuModel(41);
		
		$model->update_qc();
	}

	
	public function logList($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array(
			'do_id'=>_Request::getInt("id")
			);

		$model = new AppOrderWeixiuLogModel(41);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_order_weixiu_log_page';
		$this->render('app_order_weixiu_info_log_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$tab_id = _Request::getInt('tab_id');
		$pro_list = $this->get_pro_list(array('status'=>1)) ;//调用接口获取加工商信息有效状态的
		$buchan_arr['bc_id'] =_Request::get('bc_id');
		$buchan_arr['order_detail_id'] =_Request::get('order_detail_id');        
		//var_dump($buchan_arr);exit;
		$SalesModel = new SalesModel(27);
		if(!empty($buchan_arr['order_detail_id'])){
			$orderRows=$SalesModel->getAppOrderDetailsById($buchan_arr['order_detail_id']);
			if(!empty($orderRows)){
				$buchan_arr['p_sn'] = $orderRows['order_sn'];
				$buchan_arr['goods_id'] = $orderRows['goods_id'];
				$buchan_arr['consignee'] = $orderRows['consignee'];				
				$department_id=$orderRows['department_id'];
				if(!empty($department_id)){
					$buchan_arr['channel_class']=$SalesModel->getChannelClass($department_id);
				}
			}else{
				die('请查询布产单对应的订单是否存在!');
			}
		}

       /*  if($order_id){
            $api_res  = ApiSalesModel::getOrderInfoRow($order_id);
            if($api_res['error'] == 0){
                $buchan_arr['p_sn'] = $api_res['return_msg']['order_sn'];
                $buchan_arr['consignee'] = $api_res['return_msg']['consignee'];
                $buchan_arr['channel_class'] = $api_res['return_msg']['channel_class'];
            }
        } */
		$dd = new DictView(new DictModel(1));
		$qcs = $dd->getEnumArray("weixiu.qc_status");
		krsort($qcs);
		// print_r($qcs);
		// die();
		
		$this->render('app_order_weixiu_info.html',array(
			'view'=>new AppOrderWeixiuView(new AppOrderWeixiuModel(41)),
			'pro_list'=>$pro_list,
			'buchan_arr'=>$buchan_arr,
			'dd'=>new DictView(new DictModel(1)),
			'arr_act'=>array(),
			'qcs'=>$qcs,
			'tab_id'=>$tab_id,
			'bar'=>'',
				'is_save'=>	1,
            'action'=> 'add'
		));
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$tab_id = _Request::getInt('tab_id');
		$buchan_arr['bc_id'] ='';
		$buchan_arr['p_sn'] ='';
		$buchan_arr['consignee'] ='';
        $buchan_arr['goods_id'] = '';
		$view = new AppOrderWeixiuView(new AppOrderWeixiuModel($id,41));
		$repair_act = $view->get_repair_act();
		
		//判断保存权限是否存在
		
		//$sign = '保存';
		//if(stripos(Auth::getViewBar(),$sign) === false){ //使用绝对等于
			//不包含
		//	$is_save =0;
		//}else{
			//包含
		//	$is_save =0;
			//$bar= Auth::getViewBar();
			//$bar= str_replace($find,'',Auth::getViewBar());
		//}
		$dd = new DictView(new DictModel(1));
		$qcs = $dd->getEnumArray("weixiu.qc_status");
		krsort($qcs);
		
		$pro_list = $this->get_pro_list(array('status'=>1)) ;//调用接口获取加工商信息
		$this->render('app_order_weixiu_info.html',array(
            'bar' =>Auth::getViewBar(),
			'view'=> $view,
			'pro_list'=>$pro_list,
			'buchan_arr'=>$buchan_arr,
			'dd'=>new DictView(new DictModel(1)),
			'arr_act'=>explode(',',$repair_act),
			'tab_id'=>$tab_id,
			'is_save'=>	0,
			'qcs'=>$qcs,
            'action'=>'edit'
		));
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		die('开发中');
		$id = intval($params["id"]);
		$this->render('app_order_weixiu_show.html',array(
			'view'=>new AppOrderWeixiuView(new AppOrderWeixiuModel($id,1))
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		// var_dump($_REQUEST);exit;
		$result = array('success' => 0,'error' =>'');
		$olddo  = array();
		$tab_id = _Request::get('tab_id');
		$remark = _Request::get('remark');
		$rec_id = trim(_Request::get('rec_id'));
		if($tab_id==0){
			$tab_id='128';
		}
		//$tab_id='tab-128';
	//	var_dump($tab_id);exit;
		/*
		一、原版判断
		order_id :				根据订单号查询、
		1/腾讯维修和库房维修 5,4:	货号必填
		2/非店面维修(售后维修和新货维修)！=3:	判断订单号和布产号是否匹配
		3/是否售后维修：			货品状态已销售=是、货品状态非已销售=否
		4/非（腾讯维修和库房维修 ）:查询布产号来判断是否有产品的维修中的维修单
		///如果用户输入转仓单号：	判断转仓单里的商品的 销售状态 确定是否是售后维修

		二、修改数据：
			如果布产号存在：订单状态改变（维修中）、订单货品状态改变维修中||||订单这边没有对应状态暂时没加
			生成维修日志
			生成订单操作日志
		*/
		$newdo=array(
			'order_id'			=>'',
			'order_time'		=>date("Y-m-d H:i:s"),//制单时间
			'repair_make_order'	=>$_SESSION['userName'],
			'repair_factory'	=>_Request::get('repair_factory'),//工厂
			're_type'			=>trim(_Request::get('re_type')), //维修类型
			'order_sn'			=>_Request::get('order_sn'),//订单号
			'rec_id'			=>$rec_id,//布产号
			'old_goods_id'		=>_Request::get('old_goods_id'),//原来货号			
			'goods_id'			=>_Request::get('goods_id'),//货号
			'change_sn'			=>_Request::get('change_sn'), //转仓单号
			'consignee'			=>_Request::get('consignee'),//客户姓名
			'status'			=>1,//保存
			//'end_time'			=>_Request::get('end_time'),//预计结束时间
			'remark'			=>$remark,//备注
			'repair_act'		=>join(",", $_POST["repair_act"]),//维修动作
			'repair_man'		=>0,
			'after_sale'		=>0, //是否售后维修 0 默认不是
			'order_class'     =>_Request::get('channel_class')?_Request::get('channel_class'):'0',
            'weixiu_price'         =>_Request::get('weixiu_price')?_Request::get('weixiu_price'):0,//维修价格
			);
        if(!empty($newdo['weixiu_price']) && !Util::isNum($newdo['weixiu_price'])){
            $result['error'] = '维修费用不合法！';
            Util::jsonExit($result);
        }
        $newmodel =  new AppOrderWeixiuModel(42);
        //计算预计结束时间
        $newdo['end_time'] = $newmodel->getendday(date('y-m-d'),3);
		//var_dump($rec_id);exit;
		$order_detail_id =_Request::get('order_detail_id');//订单明细ID
		
		/*******************************按照老系统验证去校正*********************************/
		/*#1、腾讯维修和库房维修 :货号必填 ;售后维修和新货维修：判断订单号和布产号是否匹配
		if($newdo['re_type'] == 4 || $newdo['re_type'] == 5)
		{
			if(empty($newdo['goods_id']))
			{
				$result['error'] = '腾讯维修和库房维修，货号为必填项!';
				Util::jsonExit($result);
			}
		}
		else if($newdo['re_type'] != 3)
		{
			//调用加工商接口 售后维修和新货维修：判断订单号和布产号是否匹配
			$api_res  = ApiModel::process_api(array('bc_sn'=>$newdo['rec_id'],'p_sn'=>$newdo['order_sn']),'GetProductInfo');
			// echo '<pre>';print_r($api_res);echo '</pre>';die;
			if($api_res['error'] == 1)
			{
				$result['error'] = '订单号与布产号不匹配!!';
				Util::jsonExit($result);
			}

		}
		#2、如果货号不为空;是否售后维修,则货品状态已销售=是1、货品状态非已销售(或者查不到)=否0
		if(!empty($newdo['goods_id']))
		{
			$api_res  = ApiModel::warehouse_api(array('goods_id'),array($newdo['goods_id']),'GetGoodsInfoByGoods');
			if($api_res['error'] == 0 )
			{
				$newdo['after_sale'] = 1;
			}
			if(empty($api_res['return_msg'])){
				$result['error'] = $api_res['error_msg'];
				Util::jsonExit($result);
			}
			if(!$api_res['return_msg'][0]['order_goods_id']){
				$result['error'] = '该货号未绑定订单详情id!!';
				Util::jsonExit($result);
			}
		}
		if(empty($newdo['goods_id']) && $rec_id){
			$api_res  = ApiModel::warehouse_api(array('buchan_sn'),array($rec_id),'GetGoodsInfoByGoods');
			// echo 'xx<pre>';print_r($api_res);echo '</pre>';die;
			if(empty($api_res['return_msg'])){
				$result['error'] = $api_res['error_msg'];
				Util::jsonExit($result);
			}
			if(!$api_res['return_msg'][0]['order_goods_id']){
				$result['error'] = '该货号未绑定订单详情id!!';
				Util::jsonExit($result);
			}
		}
		//var_dump($newdo['re_type']);exit;
		#3、非（腾讯维修和库房维修 ）:查询布产号来判断是否有产品的维修中的维修单
	 	if($newdo['re_type'] != 4 && $newdo['re_type'] != 5)
		{
			// 腾讯订单没有布产号
			$num=0;
			if($newdo['rec_id']){
				$num = $newmodel->CheckBc($newdo['rec_id']);
			}			 
			if($num >=1)
			{
				$result['error'] = '已经有此产品的维修单';
				Util::jsonExit($result);
			}
		} */
		/*******************************************end******************************************/
        /*******************************新的验证要求(http://req.kela.cn/browse/NEW-482 )*********************************/

        if(empty($newdo['goods_id'])){
            $result['error'] = '货号/无账流水号为必填项!';
            Util::jsonExit($result);
        }

        if($newdo['re_type'] == '6'){//无账维修
            $WarehouseModel = new WarehouseModel(21);
            $virtualinfo = $WarehouseModel->getVirtualReturnGoodsId($newdo['goods_id']);
            if(empty($virtualinfo)){
                $result['error'] = '该无账流水号在系统中不存在!!';
                Util::jsonExit($result);
            }
            $res = $newmodel->saveData($newdo,$olddo);
            if($res !== false){
                $dd = new DictView(new DictModel(1));
                $arr_act = $_POST["repair_act"];
                $str_act = "";
                foreach ($arr_act as $val)
                {
                    $str_act .= $dd->getEnum('weixiu.action',$val).",";
                }
                //添加维修日志
                $arr_log = array(
                        'do_id'         =>$res,
                        'user_name'     =>$_SESSION['userName'],
                        'do_type'       =>'app_order_weixiu,insert',
                        'content'       =>'维修单添加,维修内容：'.$str_act.$newdo['remark']
                );
                
                $model_log = new AppOrderWeixiuLogModel(41);
                $model_log->saveData($arr_log,array());
                $result['success'] = 1;
                $result['x_id'] = $res;
                $result['tab_id'] = $tab_id;
                Util::jsonExit($result);
            }else{
                $result['error'] = '保存失败';
                Util::jsonExit($result);
            }
        }
        
        $api_res  = ApiWarehouseModel::GetWarehouseGoodsByGoodsid($newdo['goods_id']);
      
        if($api_res['error'] == 1){
            $result['error'] = '该货号在系統中不存在!!';
            Util::jsonExit($result);
        }else{
            $goods_info = $api_res['return_msg']['data'];
        }

        if($newdo['re_type'] == 1 || $newdo['re_type'] == 2)  // 1为新货维修，2为售后维修
        {
            if(empty($newdo['order_sn']))
            {
                $result['error'] = '新货维修和售后维修，订单号为必填项!';
                Util::jsonExit($result);
            }
        }
        
        if($goods_info['order_goods_id']){
            $api_res  = ApiSalesModel::getOrderInfoByDetailsId($goods_info['order_goods_id']);
            
            if($api_res['error'] == 0){
                $order_info = $api_res['return_msg'];
                if($newdo['re_type'] == 1 || $newdo['re_type'] == 2){
                    if($order_info['order_sn'] != $newdo['order_sn']){
                        $result['error'] = "货号绑定了订单{$order_info['order_sn']}，与维修单中的订单号不符，请更换货号再保存!";
                        Util::jsonExit($result);
                    }
                }else{
                    $result['error'] = "货号绑定了订单{$order_info['order_sn']}，请选择其他维修类型或更换货号再保存!";
                    Util::jsonExit($result);
                }

            }
        }
   
        if($newdo['re_type'] == 1 || $newdo['re_type'] == 2)  // 1为新货维修，2为售后维修
        {
            if(empty($newdo['order_sn']))
            {
                $result['error'] = '新货维修和售后维修，订单号为必填项!';
                Util::jsonExit($result);
            }
			else{//判断是否已经有相应的维修单，若此维修单状态为【保存、确认、下单、等待】则不可以再创建维修单，2015-7-24日添加，http://bug.kela.cn/browse/NEW-2446
				$where=array(
					'order_sn'=>$newdo['order_sn'],
					'goods_id'=>$newdo['goods_id'],
					'status_in'=>'1,2,3,4',//保存状态为1，确认为2，等待为3，下单为4，完成为5，收货为6，7为取消
				);
				
				$repairorder_list=$newmodel->pageList($where,1,1);
				if(isset($repairorder_list['data'][0]) && $repairorder_list['data'][0]){
					$result['error'] = '此订单号已经有维修单，不可再创建维修单!';
					Util::jsonExit($result);
				}
				//2015-7-24 add end
			}
            $api_res  = ApiSalesModel::getOrderInfoBySn($newdo['order_sn']);
            if($api_res['error'] == 1){
                $result['error'] = '该订单号在系统中不存在!!';
                Util::jsonExit($result);
            }else{
                $order_info = $api_res['return_msg'];
            }

            if($order_info['order_status'] == 4){ //订单状态是已关闭
                $result['error'] = '该订单已关闭，不允许下维修单';
                Util::jsonExit($result);
            }

            if($order_info['order_status'] != 2){ //订单状态不是已审核
				//update by liulinyan 2015-07-27 for "http://bug.kela.cn/browse/NEW-2414"
                $result['error'] = '该订单已关闭，不允许下维修单';
                Util::jsonExit($result);
            }
           
            if($newdo['re_type'] == 1){
                if($order_info['send_good_status'] == 2){ //订单已发货
                    $result['error'] = '该订单已发货，新货维修只能输入未发货的订单';
                    Util::jsonExit($result);
                }
            }
          
            if($newdo['re_type'] == 2){
                if($order_info['send_good_status'] != 2){ //订单未发货
                    $result['error'] = '该订单不是已发货的状态，售后维修只能输入已发货的订单';
                    Util::jsonExit($result);
                }
            } 
            

        }
        
        
        if($newdo['re_type'] == 3){ //店面维修
            $newdo['order_sn'] = '';
            $newdo['rec_id'] = '';
        }

        if($newdo['re_type'] == 5){ //库存维修
            $newdo['order_sn'] = '';
            $newdo['rec_id'] = '';
            $newdo['change_sn'] = '';
        }
        //print_r($newdo);exit;
        /*******************************新的验证要求end*********************************/
       
        $AppProcessorInfoModel=new AppProcessorInfoModel(13);
        $SalesModel = new SalesModel(27);
        $pdo42 = $newmodel->db()->db();
       
        $pdo13 = $AppProcessorInfoModel->db()->db();
        $pdo27 = $SalesModel->db()->db();
         
        $pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
        $pdo42->beginTransaction(); //开启事务
         
        $pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
        $pdo13->beginTransaction(); //开启事务
        
        $pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
        $pdo27->beginTransaction(); //开启事务
        
        
        try{
        
        $res = $newmodel->saveData($newdo,$olddo);
        
		//$res=1;
		$dd = new DictView(new DictModel(1));
		$arr_act = $_POST["repair_act"];
		$str_act = "";
		foreach ($arr_act as $val)
		{
			$str_act .= $dd->getEnum('weixiu.action',$val).",";
		}
		
		
		
		/*原来的
		
		if($res !== false)
		{

			//添加维修日志
			$arr_log = array(
				'do_id'			=>$res,
				'user_name'		=>$_SESSION['userName'],
				'do_type'		=>'app_order_weixiu,insert',
				'content'		=>'维修单添加,布产号：'.$rec_id.',维修内容：'.$str_act.$remark
				);
			$this->add_weixiu_log($arr_log);
			
			//接口添加订单日志
			$pro_name ='';
			$pro_list = $this->get_pro_list(array());
			
			foreach ($pro_list as $val)
			{
				if($val['id']==$newdo['repair_factory']){
					$pro_name =$val['name'];
				}
			}
			
			$model = new ApiSalesModel();
			$res1 =	$model->add_order_log($newdo['order_sn'],$arr_log['user_name'],'维修单添加，维修流水号：'.$res.',维修工厂：'.$pro_name.',布产号：'.$rec_id.',维修内容：'.$str_act.$remark.',维修状态:'.$dd->getEnum('weixiu.status',$newdo['status']));
			
			//改变订单维修状态
			//$ret = $model->change_weixiu_status($newdo['order_sn'],array('weixiu_status'=>1));
			if($goods_info['order_goods_id']){
				$ret = $model->change_weixiu_status($goods_info['order_goods_id'],array('weixiu_status'=>1));
			}elseif($rec_id){
                $api_res  = ApiSalesModel::getOrderDetailByBCId(substr($rec_id,2));
                if($api_res['error'] == 0){
                    $order_detail_info = $api_res['return_msg'];
                    $ret = $model->change_weixiu_status($order_detail_info['id'],array('weixiu_status'=>1));
                }
            }
			
			//接口添加布产日志
			if($rec_id){
				$ApiProcessorModel = new ApiProcessorModel();
				$res2 =	$ApiProcessorModel->add_order_log(array('weixiu_status'=>1,'rec_id'=>$rec_id,'create_user_id'=>$_SESSION['userId'],'create_user'=>$_SESSION['userName'],'remark'=>'维修单添加，维修流水号：'.$res.',维修工厂：'.$pro_name.',布产号：'.$rec_id.',维修内容：'.$str_act.$remark.',维修状态：保存'));
			}
           
			$result['success'] = 1;
			$result['x_id'] = $res;
			$result['tab_id'] = $tab_id;
		}
		else
		{
			$result['error'] = '添加失败';
		}
		
		
		//var_dump($result);exit;
		Util::jsonExit($result);
		
		*/
		
		
		if($res !== false)
		{
			//添加维修日志
			$arr_log = array(
					'do_id'			=>$res,
					'user_name'		=>$_SESSION['userName'],
					'do_type'		=>'app_order_weixiu,insert',
					'content'		=>'维修单添加,布产号：'.$rec_id.',维修内容：'.$str_act.$remark
			);
			
			$model_log = new AppOrderWeixiuLogModel(41);
		    $res2 = $model_log->saveData($arr_log,array());
			//$res2= $newmodel->setWeiXiuLog($arr_log);
			if(!$res2){
				$pdo42->rollback(); //事务回滚
				$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo13->rollback(); //事务回滚
				$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$pdo27->rollback(); //事务回滚
				$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
				$result['error'] = '添加维修日志失败';
				Util::jsonExit($result);
			}
				
			
			//添加订单日志
			$pro_name ='';
			
			$pro_list = $AppProcessorInfoModel->getProList();
			foreach ($pro_list as $val)
			{
				if($val['id']==$newdo['repair_factory']){
					$pro_name =$val['name'];
				}
			}
			
			if($newdo['order_sn'] != ''){
				$res1 =	$SalesModel->AddOrderLog($newdo['order_sn'],$arr_log['user_name'],'维修单添加，维修流水号：'.$res.',维修工厂：'.$pro_name.',布产号：'.$rec_id.',维修内容：'.$str_act.$remark.',维修状态:'.$dd->getEnum('weixiu.status',$newdo['status']));		
			    if(!$res1){
			    	$pdo42->rollback(); //事务回滚
			    	$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			    	$pdo13->rollback(); //事务回滚
			    	$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			    	$pdo27->rollback(); //事务回滚
			    	$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			    	$result['error'] = '添加订单日志失败，请确认 订单号 是否正确';
			    	Util::jsonExit($result);
			    }
			}
		/*	改变订单维修状态
			首先维修单如果填写了布产单号，
			布产单的维修状态需要根据维修订单状态同步更新，
			同时根据布产单号去找到顾客订单相同【布产ID】的商品，
			更新该行维修状态；
			如果维修单没有填写布产单，
			只填写订单号和货号，
			判断货号是否绑定该订单号,
            如果绑定则反写货号绑定的那一行的顾客订单的商品的维修状态，
			未绑定则不反写订单的维修状态；
		    */	
			   //更新订单商品维修状态
			   $res4=true;
			   if(!empty($order_detail_id)){
			       $res4 = $SalesModel->EditOrderGoodsStatusById($order_detail_id,1);
			   	    
			   }elseif($rec_id){
			    	
				   $res4 = $SalesModel->EditOrderGoodsInfo(substr($rec_id,2),1);
                   
				  
			  }elseif(!empty($newdo['order_sn']) && !empty($newdo['goods_id'])){
			  	    $order_detail_id=$SalesModel->getOrderDetailId($newdo['order_sn'],$newdo['goods_id']);
			  	    if(!$order_detail_id){
			  	    	$result['error'] = '订单明细没有绑定货号，请从订单明细里点击维修按钮进行维修';
			  	    	Util::jsonExit($result);
			  	    }
					$res4=$SalesModel->EditOrderGoodsStatusById($order_detail_id,1);
					
			   }
			    
			    if(!$res4){
			    	$pdo42->rollback(); //事务回滚
			    	$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			    	$pdo13->rollback(); //事务回滚
			    	$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			    	$pdo27->rollback(); //事务回滚
			    	$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			    	$result['error'] = '更新订单商品维修状态失败,请确认布产号、订单号、货号信息是否正确';
			    	Util::jsonExit($result);
			    }
			  
			  if($rec_id){			  	
			  	//更新布产单维修状态
			  
			  	$res5 = $AppProcessorInfoModel->editWeixiuStatus(substr($rec_id,2),1);
			  	if(!$res5){
			  		$pdo42->rollback(); //事务回滚
			  		$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			  		$pdo13->rollback(); //事务回滚
			  		$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			  		$pdo27->rollback(); //事务回滚
			  		$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			  		$result['error'] = '更新布产单维修状态失败，请确认布产号是否正确';
			  		Util::jsonExit($result);
			  	}
			  
			  	//添加布产日志
			  	$res3 =	$AppProcessorInfoModel->AddOrderLog(array('rec_id'=>$rec_id,'status'=>1,'create_user_id'=>$_SESSION['userId'],'create_user'=>$_SESSION['userName'],'remark'=>'维修单添加，维修流水号：'.$res.',维修工厂：'.$pro_name.',布产号：'.$rec_id.',维修内容：'.$str_act.$remark.',维修状态：保存'));
			  	if(!$res3){
			  		$pdo42->rollback(); //事务回滚
			  		$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			  		$pdo13->rollback(); //事务回滚
			  		$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			  		$pdo27->rollback(); //事务回滚
			  		$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			  		$result['error'] = '添加布产日志失败,请确认布产号是否正确';
			  		Util::jsonExit($result);
			  	}
			  
			  }
			  
			  
			  

			
			
			
			$result['success'] = 1;
			$result['x_id'] = $res;
			$result['tab_id'] = $tab_id;
		}else{
			
			  $pdo42->rollback(); //事务回滚
			  $pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			  $pdo13->rollback(); //事务回滚
			  $pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			  $pdo27->rollback(); //事务回滚
			  $pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			  $result['error'] = '保存失败';
			  Util::jsonExit($result);
		}
		
		
		
		$pdo42->commit(); //事务提交
		$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		$pdo13->commit(); //事务回滚
		$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		$pdo27->commit(); //事务回滚
		$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
		
		  Util::jsonExit($result);
		}catch (Exception $e){			
			$pdo42->rollback(); //事务回滚
			$pdo42->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$pdo13->rollback(); //事务回滚
			$pdo13->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$pdo27->rollback(); //事务回滚
			$pdo27->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
			$result['error'] ="系统异常！error code:".$e;
			//$result['error'] ="系统异常！error ";
			Util::jsonExit($result);
		}	
		
	}

	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');
		$newmodel =  new AppOrderWeixiuModel($id,42);
		$olddo = $newmodel->getDataObject();
		if(in_array($olddo['status'],array(5,6,7)))
		{
			$result['error'] = '已完毕、已收货、已取消的不可以修改!';
			Util::jsonExit($result);
		}
		$remark_log = _Request::get('remark_log');
		$rec_id =_Request::get('rec_id');
		//var_dump($_REQUEST);exit;
		$newdo=array(
			'id'=>$id,
			'order_id'=>'',
			'repair_factory'=>_Request::get('repair_factory'),//工厂
			're_type'=>$newmodel->getValue('re_type'), //维修类型
			'order_sn'=>_Request::get('order_sn'),//订单号
			'rec_id'=>_Request::get('rec_id'),//布产号
			'goods_id'=>_Request::get('goods_id'),//货号
			'change_sn'=>_Request::get('change_sn'), //转仓单号
			'consignee'=>_Request::get('consignee'),//客户姓名
			//'status'=>0,//保存
			//'end_time'=>_Request::get('end_time'),//预计结束时间
			'remark'=>_Request::get('remark'),//备注
			'repair_act'=>join(",", $_POST["repair_act"]),//维修动作
			'repair_man'=>0,
			'after_sale'=>0, //是否售后维修 0 默认不是
            'order_class'=>_Request::get('channel_class')?_Request::get('channel_class'):'0',
            'weixiu_price'=>_Request::get('weixiu_price')?_Request::get('weixiu_price'):0,//维修费用
			);
        if(!empty($newdo['weixiu_price']) && !Util::isNum($newdo['weixiu_price'])){
            $result['error'] = '维修费用不合法！';
            Util::jsonExit($result);
        }
		/*******************************按照老系统验证去校正*********************************/
		/*#1、腾讯维修和库房维修 :货号必填 ;售后维修和新货维修：判断订单号和布产号是否匹配
		if($newdo['re_type'] == 4 || $newdo['re_type'] == 5)
		{
			if(empty($newdo['goods_id']))
			{
				$result['error'] = '腾讯维修和库房维修，货号为必填项!';
				Util::jsonExit($result);
			}
		}
		else if($newdo['re_type'] != 3)
		{
			//调用加工商接口 售后维修和新货维修：判断订单号和布产号是否匹配
			$api_res  = ApiModel::process_api(array('bc_sn'=>$newdo['rec_id'],'p_sn'=>$newdo['order_sn']),'GetProductInfo');
			//var_dump($api_res);exit;
			if($api_res['error'] == 1)
			{
				$result['error'] = '订单号与布产号不匹配!!';
				Util::jsonExit($result);
			}

		}
		#2、如果货号不为空;是否售后维修,则货品状态已销售=是1、货品状态非已销售(或者查不到)=否0
		if(!empty($newdo['goods_id']))
		{
			$api_res  = ApiModel::warehouse_api(array('goods_id'),array($newdo['goods_id']),'GetGoodsInfoByGoods');
			if($api_res['error'] == 0 )
			{
				$newdo['after_sale'] = 1;
			}
				if(!$api_res['return_msg'][0]['order_goods_id']){
				$result['error'] = '该货号未绑定订单详情id!!';
				Util::jsonExit($result);
			}
		}

		if(empty($newdo['goods_id']) && $rec_id){
			$api_res  = ApiModel::warehouse_api(array('buchan_sn'),array($rec_id),'GetGoodsInfoByGoods');
			if(!$api_res['return_msg'][0]['order_goods_id']){
				$result['error'] = '该货号未绑定订单详情id!!';
				Util::jsonExit($result);
			}
		}*/
		/*******************************************end******************************************/
        /*******************************新的验证要求(http://req.kela.cn/browse/NEW-482 )*********************************/
        if(empty($newdo['goods_id'])){
            $result['error'] = '货号/无账流水号为必填项!';
            Util::jsonExit($result);
        }

        //无账修退单稽核
        if($newdo['re_type'] == '6'){//无账维修
            $WarehouseModel = new WarehouseModel(21);
            $virtualinfo = $WarehouseModel->getVirtualReturnGoodsId($newdo['goods_id']);
            if(empty($virtualinfo)){
                $result['error'] = '该无账流水号在系统中不存在!!';
                Util::jsonExit($result);
            }
            $res = $newmodel->saveData($newdo,$olddo);
            if($res !== false){
                $result['success'] = 1;
                $result['x_id'] = $res;
                $result['tab_id'] = $tab_id;
                Util::jsonExit($result);
            }else{
                $result['error'] = '保存失败';
                Util::jsonExit($result);
            }
        }

        $api_res  = ApiWarehouseModel::GetWarehouseGoodsByGoodsid($newdo['goods_id']);
        if($api_res['error'] == 1){
            $result['error'] = '该货号在系統中不存在!!';
            Util::jsonExit($result);
        }else{
            $goods_info = $api_res['return_msg']['data'];
        }

        if($newdo['re_type'] == 1 || $newdo['re_type'] == 2)  // 1为新货维修，2为售后维修
        {
            if(empty($newdo['order_sn']))
            {
                $result['error'] = '新货维修和售后维修，订单号为必填项!';
                Util::jsonExit($result);
            }
        }

        if($goods_info['order_goods_id']){
            $api_res  = ApiSalesModel::getOrderInfoByDetailsId($goods_info['order_goods_id']);
            if($api_res['error'] == 0){
                $order_info = $api_res['return_msg'];
                if($newdo['re_type'] == 1 || $newdo['re_type'] == 2){
                    if($order_info['order_sn'] != $newdo['order_sn']){
                        $result['error'] = "货号绑定了订单{$order_info['order_sn']}，与维修单中的订单号不符，请更换货号再保存!";
                        Util::jsonExit($result);
                    }
                }else{
                    $result['error'] = "货号绑定了订单{$order_info['order_sn']}，请选择其他维修类型或更换货号再保存!";
                    Util::jsonExit($result);
                }

            }
        }

        if($newdo['re_type'] == 1 || $newdo['re_type'] == 2)  // 1为新货维修，2为售后维修
        {
            if(empty($newdo['order_sn']))
            {
                $result['error'] = '新货维修和售后维修，订单号为必填项!';
                Util::jsonExit($result);
            }

            $api_res  = ApiSalesModel::getOrderInfoBySn($newdo['order_sn']);
            if($api_res['error'] == 1){
                $result['error'] = '该订单号在系统中不存在!!';
                Util::jsonExit($result);
            }else{
                $order_info = $api_res['return_msg'];
            }

            if($order_info['order_status'] == 4){ //订单状态是已关闭
                $result['error'] = '该订单已关闭，不允许下维修单';
                Util::jsonExit($result);
            }

            if($order_info['order_status'] != 2){ //订单状态不是已审核
				//update by liulinyan 2015-07-27 for "http://bug.kela.cn/browse/NEW-2414"
                $result['error'] = '该订单已关闭，不允许下维修单';
                Util::jsonExit($result);
            }

            if($newdo['re_type'] == 1){
                if($order_info['send_good_status'] == 2){ //订单已发货
                    $result['error'] = '该订单已发货，新货维修只能输入未发货的订单';
                    Util::jsonExit($result);
                }
            }

            if($newdo['re_type'] == 2){
                if($order_info['send_good_status'] != 2){ //订单未发货
                    $result['error'] = '该订单不是已发货的状态，售后维修只能输入已发货的订单';
                    Util::jsonExit($result);
                }
            }

        }

        if($newdo['re_type'] == 3){ //店面维修
            $newdo['order_sn'] = '';
            $newdo['rec_id'] = '';
        }

        if($newdo['re_type'] == 5){ //库存维修
            $newdo['order_sn'] = '';
            $newdo['rec_id'] = '';
            //$newdo['change_sn'] = '';
        }
        /*******************************新的验证要求end*********************************/
		$res = $newmodel->saveData($newdo,$olddo);
		//获取维修 内容
		$dd = new DictView(new DictModel(1));
		$repair_act = $newdo['repair_act'];
		$arr_act = explode(',', $repair_act);
		$str_act = "";
		foreach ($arr_act as $val)
		{
			$str_act .= $dd->getEnum('weixiu.action',$val).",";
		}
		$status=$newmodel->getValue('status');
		//var_dump($status);exit;
		$weixiu_status = $dd->getEnum('weixiu.status',$status);
		$remark=$newdo['remark'];
		if($res !== false)
		{
			//添加订单日志
			//接口添加订单日志
			$pro_name ='';
			$pro_list = $this->get_pro_list(array());
			foreach ($pro_list as $val)
			{
				if($val['id']==$newdo['repair_factory']){
					$pro_name =$val['name'];
				}
			}
			$model = new ApiSalesModel();
			$res1 =	$model->add_order_log($newdo['order_sn'],$_SESSION['userName'],'维修单添加，维修流水号：'.$id.',维修工厂：'.$pro_name.',布产号：'.$newdo['rec_id'].',维修内容：'.$str_act.$remark);
			//改变订单维修状态
            if($goods_info['order_goods_id']){
                $ret = $model->change_weixiu_status($goods_info['order_goods_id'],array('weixiu_status'=>1));
            }elseif($rec_id){
                $api_res  = ApiSalesModel::getOrderDetailByBCId(substr($rec_id,2));
                if($api_res['error'] == 0){
                    $order_detail_info = $api_res['return_msg'];
                    $ret = $model->change_weixiu_status($order_detail_info['id'],array('weixiu_status'=>1));
                }
            }
			//接口添加布产日志
			if($newdo['rec_id']){
				$ApiProcessorModel = new ApiProcessorModel();
				$res2 =	$ApiProcessorModel->add_order_log(array('weixiu_status'=>1,'rec_id'=>$newdo['rec_id'],'create_user_id'=>$_SESSION['userId'],'create_user'=>$_SESSION['userName'],'remark'=>'维修单添加，维修流水号：'.$id.',维修工厂：'.$pro_name.',维修内容：'.$str_act.$remark_log.',维修状态：'.$weixiu_status));
			}
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '修改失败';
		}


		Util::jsonExit($result);
		die;
	}
	/*
	*add by zhangruiying
	* 检查状态操作
	*  2015/6/17
	*/
	function checkStatus($old_status,$new_status)
	{
		$result = array('success' =>1,'error' => '');
		if($old_status==7)
		{
			if($new_status==7)
			{
				$result['error']='该维修单已取消不能重复取消！';
			}
			else
			{
				$result['error']='该维修单已取消不能进行其它操作';
			}
			$result['success']=0;
		}
		elseif($old_status==6)
		{
			if($new_status!=7)
			{
				$result['error']='该维修单已收货不能进行该操作！';
				$result['success']=0;
			}
		}
		elseif($old_status==5)
		{
			if($new_status!=7 and $new_status!=6)
			{
				$result['error']='该维修单已维修完毕不能进行该操作！';
				$result['success']=0;
			}
		}
		return $result;

	}

	/**
	change_status,改变状态
	**/
	public function change_status()
	{
		//var_dump($_REQUEST);exit;
		$result = array('success' => 0,'error' => '');
		$id = _Post::getInt('id');
		$status = _Post::getInt('status');
		$model = new AppOrderWeixiuModel($id,42);
		$buchan = $model->getValue('rec_id');
		$order_sn = $model->getValue('order_sn');
		$goods_id = $model->getValue('goods_id');
		$repair_factory = $model->getValue('repair_factory');
		$remark = _Request::get('remark');
		$remark_log = _Request::get('remark_log');//	操作日志备注

        $goods_info = array();
        //var_dump($model->getValue('re_type'));die;
        if($model->getValue('re_type') == '6'){
            $WarehouseModel = new WarehouseModel(21);
            $virtualinfo = $WarehouseModel->getVirtualReturnGoodsId($goods_id);
            if(empty($virtualinfo)){
                $result['error'] = '该无账流水号在系统中不存在!!';
                Util::jsonExit($result);
            }
        }else{
            #2、如果货号不为空;是否售后维修,则货品状态已销售=是1、货品状态非已销售(或者查不到)=否0
            $api_res  = ApiWarehouseModel::GetWarehouseGoodsByGoodsid($goods_id);
            if($api_res['error'] == 1){
                $result['error'] = '该货号在系統中不存在!!';
                Util::jsonExit($result);
            }else{
                $goods_info = $api_res['return_msg']['data'];
            }
        }
		
		 
		$arr_log = array(
				'do_id'			=>$id,
				'user_name'		=>$_SESSION['userName'],
				'do_type'		=>'app_order_weixiu,update'
			);
		
		$check=$this->checkStatus($model->getValue('status'),$status);
		if($check['success']!=1)
		{
			Util::jsonExit($check);
		}
		
		//确认按钮  状态  确认时间  备注
		if ($status == 2)
		{
			$model->setValue('status',$status);
			$model->setValue('confirm_time',date("Y-m-d H:i:s"));
			$arr_log['content'] ="维修单确认,布产号：".$buchan.';'.$remark;
		}
		//取消    修改订单的状态为已出厂
		else if ($status == 7)
		{
			$model->setValue('status',$status);
			$arr_log['content'] ="维修单取消,布产号：".$buchan.';'.$remark;
			//修改未做
		}
		// 等待
		else if ($status == 3)
		{
			$model->setValue('status',$status);
			$arr_log['content'] ="维修单等待,布产号：".$buchan.';'.$remark;
		}
		// 下单
		else if ($status == 4)
		{
			$model->setValue('status',$status);
			$model->setValue('repair_man',$_SESSION['userId']);
			$model->setValue('factory_time',date("Y-m-d H:i:s"));
			$arr_log['content'] ="维修单下单,布产号：".$buchan.';'.$remark;
		}
		//完毕   维修完毕相关修改
		else if ($status == 5)
		{
			//更新数据库状态--质检通过
			$model->EditWeixuQc($id,1);
			
			// 质检次数:点击完毕或者批量完毕,次数再加1,--2015-10-24
			$model->EditQctimes($id,1);
			
			$model->setValue('status',$status);
			$model->setValue('re_end_time',date("Y-m-d H:i:s"));
			$arr_log['content'] ="维修单完毕,布产号：".$buchan.';'.$remark;
		}
		//收货
		else if ($status == 6)
		{
			//更新数据库状态--质检通过
			$model->EditWeixuQc($id,1);
			
			$model->setValue('status',$status);
			$model->setValue('receiving_time',date("Y-m-d H:i:s"));
			$retm = $model->getValue('re_end_time');
			if(empty($retm)||$retm == "0000-00-00 00:00:00")
			{
				// 当完毕时间为空,点击批量收货或者收货时,自动填充完毕时间,--2015-10-23
				$model->setValue('re_end_time',date("Y-m-d H:i:s"));
				// 质检次数:只有当完毕时间为空再去点击收货或者批量收货,
				// 次数需要再加1,完毕时间不为空去点击收货或者批量收货,
				// 次数不需要加1,--2015-10-24
				$model->EditQctimes($id,1);
			}
			
			
			$arr_log['content'] ="维修单收货,布产号：".$buchan.';'.$remark;
		}
		$res = $model->save(true);
		if($res !== false)
		{

			//添加维修日志 1
			$this->add_weixiu_log($arr_log);

			//接口添加订单日志 2
			$dd = new DictView(new DictModel(1));
			$weixiu_status = $dd->getEnum('weixiu.status',$status);
			$pro_name ='';
			$pro_list = $this->get_pro_list(array());
			foreach ($pro_list as $val)
			{
				if($val['id']==$repair_factory){
					$pro_name =$val['name'];
				}
			}

			//获取维修 内容
			$repair_act = $model->getValue('repair_act');
			$arr_act = explode(',', $repair_act);
			$str_act = "";
			foreach ($arr_act as $val)
			{
				$str_act .= $dd->getEnum('weixiu.action',$val).",";
			}
			$model = new ApiSalesModel();
			$res =	$model->add_order_log($order_sn,$_SESSION['userName'],'维修单操作，维修流水号：'.$id.',维修工厂：'.$pro_name.',布产号：'.$buchan.',维修状态：'.$weixiu_status);
			//改变订单维修状态
			//$ret = $model->change_weixiu_status($order_sn,array('weixiu_status'=>$status));
            if(isset($goods_info['order_goods_id']) && !empty($goods_info['order_goods_id'])){
                $ret = $model->change_weixiu_status($goods_info['order_goods_id'],array('weixiu_status'=>$status));
            }elseif($buchan){
                $api_res  = ApiSalesModel::getOrderDetailByBCId(substr($buchan,2));
                if($api_res['error'] == 0){
                    $order_detail_info = $api_res['return_msg'];
                    $ret = $model->change_weixiu_status($order_detail_info['id'],array('weixiu_status'=>$status));
                }
            }


			//接口添加布产日志 3
			if($buchan){
				$ApiProcessorModel = new ApiProcessorModel();
				$res2 =	$ApiProcessorModel->add_order_log(array('weixiu_status'=>$status,'rec_id'=>$buchan,'create_user_id'=>$_SESSION['userId'],'create_user'=>$_SESSION['userName'],'remark'=>'维修单操作，维修流水号：'.$id.',维修工厂：'.$pro_name.',维修内容：'.$str_act.$remark_log.',维修状态：'.$weixiu_status));
			}
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}


	/**
	 batch_order,批量下单
	 **/
	public function batch_order()
	{
		$result = array('success' => 0,'error' => '');
		$ids = $_REQUEST['_ids'];
		foreach($ids as $id){
			$status = 4;
			$model = new AppOrderWeixiuModel($id,42);

			$check=$this->checkStatus($model->getValue('status'),$status);
			if($check['success']!=1)
			{
				$check['error']=$id.":".$check['error'];
				Util::jsonExit($check);
			}
			$buchan = $model->getValue('rec_id');
			$order_sn = $model->getValue('order_sn');
			$repair_factory = $model->getValue('repair_factory');
			$goods_id = $model->getValue('goods_id');
			#2、如果货号不为空;是否售后维修,则货品状态已销售=是1、货品状态非已销售(或者查不到)=否0
            $api_res  = ApiWarehouseModel::GetWarehouseGoodsByGoodsid($goods_id);
            if($api_res['error'] == 1){
                $result['error'] = '该货号在系統中不存在!!';
                Util::jsonExit($result);
            }else{
                $goods_info = $api_res['return_msg']['data'];
            }
			

			$remark = _Request::get('remark_log');//	操作日志备注

			$arr_log = array(
					'do_id'			=>$id,
					'user_name'		=>$_SESSION['userName'],
					'do_type'		=>'app_order_weixiu,update'
			);
			//下单按钮  状态  下单时间  备注
			if ($status == 4)
			{

				$model->setValue('status',$status);
				$model->setValue('repair_man',$_SESSION['userId']);
				$model->setValue('factory_time',date("Y-m-d H:i:s"));
				$arr_log['content'] ="维修单确认,布产号：".$buchan.';'.$remark;
			}
			$res = $model->save(true);
			if($res !== false)
			{

				//添加维修日志 1
				$this->add_weixiu_log($arr_log);

				//接口添加订单日志 2
				$dd = new DictView(new DictModel(1));
				$weixiu_status = $dd->getEnum('weixiu.status',$status);
				$pro_name ='';
				$pro_list = $this->get_pro_list(array());
				foreach ($pro_list as $val)
				{
					if($val['id']==$repair_factory){
						$pro_name =$val['name'];
					}
				}
				//获取维修 内容
				$repair_act = $model->getValue('repair_act');
				$arr_act = explode(',', $repair_act);
				$str_act = "";
				foreach ($arr_act as $val)
				{
					$str_act .= $dd->getEnum('weixiu.action',$val).",";
				}
				$model = new ApiSalesModel();
				$res =	$model->add_order_log($order_sn,$_SESSION['userName'],'批量维修单操作，维修流水号：'.$id.',维修工厂：'.$pro_name.',布产号：'.$buchan.',维修状态：'.$weixiu_status);
				//改变订单维修状态
				//$ret = $model->change_weixiu_status($order_sn,array('weixiu_status'=>$status));
				$ret = $model->change_weixiu_status($api_res['return_msg'][0]['order_goods_id'],array('weixiu_status'=>$status));
                if($goods_info['order_goods_id']){
                    $ret = $model->change_weixiu_status($goods_info['order_goods_id'],array('weixiu_status'=>$status));
                }elseif($buchan){
                    $api_res  = ApiSalesModel::getOrderDetailByBCId(substr($buchan,2));
                    if($api_res['error'] == 0){
                        $order_detail_info = $api_res['return_msg'];
                        $ret = $model->change_weixiu_status($order_detail_info['id'],array('weixiu_status'=>$status));
                    }
                }
				//接口添加布产日志 3
				if($buchan){
					$ApiProcessorModel = new ApiProcessorModel();
					$res2 =	$ApiProcessorModel->add_order_log(array('weixiu_status'=>$status,'rec_id'=>$buchan,'create_user_id'=>$_SESSION['userId'],'create_user'=>$_SESSION['userName'],'remark'=>'批量维修单操作，维修流水号：'.$id.',维修工厂：'.$pro_name.',维修内容：'.$str_act.$remark.',维修状态：'.$weixiu_status));
				}
				$result['success'] = 1;
			}
			else
			{
				$result['error'] = "操作失败";
			}
		}

		Util::jsonExit($result);
	}

	/**
	 batch_order,批量收货
	 **/
	public function batch_goods()
	{
		//var_dump($_REQUEST);exit;
		$result = array('success' => 0,'error' => '');
		$ids = $_REQUEST['_ids'];
		//	var_dump($ids);exit;
		foreach($ids as $id){
			//var_dump($id);exit;
			$status = 6;
			$model = new AppOrderWeixiuModel($id,42);
			$check=$this->checkStatus($model->getValue('status'),$status);
			if($check['success']!=1)
			{
				$check['error']=$id.":".$check['error'];
				Util::jsonExit($check);
			}
			$buchan = $model->getValue('rec_id');
			$order_sn = $model->getValue('order_sn');
			
			$goods_id = $model->getValue('goods_id');
			#2、如果货号不为空;是否售后维修,则货品状态已销售=是1、货品状态非已销售(或者查不到)=否0
            $api_res  = ApiWarehouseModel::GetWarehouseGoodsByGoodsid($goods_id);
            if($api_res['error'] == 1){
                $result['error'] = '该货号在系統中不存在!!';
                Util::jsonExit($result);
            }else{
                $goods_info = $api_res['return_msg']['data'];
            }
			
			$repair_factory = $model->getValue('repair_factory');
			$remark = _Request::get('remark_log');//	操作日志备注
			$arr_log = array(
					'do_id'			=>$id,
					'user_name'		=>$_SESSION['userName'],
					'do_type'		=>'app_order_weixiu,update'
			);
			//收货按钮  状态  收货时间  备注
			if ($status == 6)
			{

				$model->setValue('status',$status);
				$model->setValue('receiving_time',date("Y-m-d H:i:s"));
				$arr_log['content'] ="维修单确认,布产号：".$buchan.';'.$remark;
			}
			$res = $model->save(true);
			//var_dump($res);exit;
			if($res !== false)
			{

				//添加维修日志 1
				$this->add_weixiu_log($arr_log);

				//接口添加订单日志 2
				$dd = new DictView(new DictModel(1));
				$weixiu_status = $dd->getEnum('weixiu.status',$status);
				$pro_name ='';
				$pro_list = $this->get_pro_list(array());
				foreach ($pro_list as $val)
				{
					if($val['id']==$repair_factory){
						$pro_name =$val['name'];
					}
				}

				//获取维修 内容
				$repair_act = $model->getValue('repair_act');
				$arr_act = explode(',', $repair_act);
				$str_act = "";
				foreach ($arr_act as $val)
				{
					$str_act .= $dd->getEnum('weixiu.action',$val).",";
				}
				$model = new ApiSalesModel();
				$res =	$model->add_order_log($order_sn,$_SESSION['userName'],'批量维修单操作，维修流水号：'.$id.',维修工厂：'.$pro_name.',布产号：'.$buchan.',维修状态：'.$weixiu_status);
				//改变订单维修状态
				//$ret = $model->change_weixiu_status($order_sn,array('weixiu_status'=>$status));
                if($goods_info['order_goods_id']){
                    $ret = $model->change_weixiu_status($goods_info['order_goods_id'],array('weixiu_status'=>$status));
                }elseif($buchan){
                    $api_res  = ApiSalesModel::getOrderDetailByBCId(substr($buchan,2));
                    if($api_res['error'] == 0){
                        $order_detail_info = $api_res['return_msg'];
                        $ret = $model->change_weixiu_status($order_detail_info['id'],array('weixiu_status'=>$status));
                    }
                }
				//接口添加布产日志 3
			if($buchan){
					$ApiProcessorModel = new ApiProcessorModel();
					$res2 =	$ApiProcessorModel->add_order_log(array('weixiu_status'=>$status,'rec_id'=>$buchan,'create_user_id'=>$_SESSION['userId'],'create_user'=>$_SESSION['userName'],'remark'=>'批量维修单操作，维修流水号：'.$id.',维修工厂：'.$pro_name.',维修内容：'.$str_act.$remark.',维修状态：'.$weixiu_status));
				}
				$result['success'] = 1;
			}
			else
			{
				$result['error'] = "操作失败";
			}
		}

		Util::jsonExit($result);
	}


	/**
	 batch_goods,批量完成
	 **/
	public function batch_complete()
	{
		$result = array('success' => 0,'error' => '');
		$ids = $_REQUEST['_ids'];
		foreach($ids as $id){
			$status = 5;
			$model = new AppOrderWeixiuModel($id,42);
			$check=$this->checkStatus($model->getValue('status'),$status);
			if($check['success']!=1)
			{
				$check['error']=$id.":".$check['error'];
				Util::jsonExit($check);
			}
			$buchan = $model->getValue('rec_id');
			$order_sn = $model->getValue('order_sn');
			
			$goods_id = $model->getValue('goods_id');
			#2、如果货号不为空;是否售后维修,则货品状态已销售=是1、货品状态非已销售(或者查不到)=否0
            $api_res  = ApiWarehouseModel::GetWarehouseGoodsByGoodsid($goods_id);
            if($api_res['error'] == 1){
                $result['error'] = '该货号在系統中不存在!!';
                Util::jsonExit($result);
            }else{
                $goods_info = $api_res['return_msg']['data'];
            }
			$repair_factory = $model->getValue('repair_factory');
			$remark = _Request::get('remark_log');//	操作日志备注
			$arr_log = array(
					'do_id'			=>$id,
					'user_name'		=>$_SESSION['userName'],
					'do_type'		=>'app_order_weixiu,update'
			);
			//确认按钮  状态  确认时间  备注
			if ($status == 5)
			{

				$model->setValue('status',$status);
				$model->setValue('re_end_time',date("Y-m-d H:i:s"));
				$arr_log['content'] ="维修单确认,布产号：".$buchan.';'.$remark;
			}
			$res = $model->save(true);
			if($res !== false)
			{

				//添加维修日志 1
				$this->add_weixiu_log($arr_log);

				//接口添加订单日志 2
				$dd = new DictView(new DictModel(1));
				$weixiu_status = $dd->getEnum('weixiu.status',$status);
				$pro_name ='';
				$pro_list = $this->get_pro_list(array());
				foreach ($pro_list as $val)
				{
					if($val['id']==$repair_factory){
						$pro_name =$val['name'];
					}
				}

				//获取维修 内容
				$repair_act = $model->getValue('repair_act');
				$arr_act = explode(',', $repair_act);
				$str_act = "";
				foreach ($arr_act as $val)
				{
					$str_act .= $dd->getEnum('weixiu.action',$val).",";
				}
				$model = new ApiSalesModel();
				$res =	$model->add_order_log($order_sn,$_SESSION['userName'],'批量维修单操作，维修流水号：'.$id.',维修工厂：'.$pro_name.',布产号：'.$buchan.',维修状态：'.$weixiu_status);
				//改变订单维修状态
				//$ret = $model->change_weixiu_status($order_sn,array('weixiu_status'=>$status));
				//$ret = $model->change_weixiu_status($api_res['return_msg'][0]['order_goods_id'],array('weixiu_status'=>$status));
                if($goods_info['order_goods_id']){
                    $ret = $model->change_weixiu_status($goods_info['order_goods_id'],array('weixiu_status'=>$status));
                }elseif($buchan){
                    $api_res  = ApiSalesModel::getOrderDetailByBCId(substr($buchan,2));
                    if($api_res['error'] == 0){
                        $order_detail_info = $api_res['return_msg'];
                        $ret = $model->change_weixiu_status($order_detail_info['id'],array('weixiu_status'=>$status));
                    }
                }
				//接口添加布产日志 3
			if($buchan){
					$ApiProcessorModel = new ApiProcessorModel();
					$res2 =	$ApiProcessorModel->add_order_log(array('weixiu_status'=>$status,'rec_id'=>$buchan,'create_user_id'=>$_SESSION['userId'],'create_user'=>$_SESSION['userName'],'remark'=>'批量维修单操作，维修流水号：'.$id.',维修工厂：'.$pro_name.',维修内容：'.$str_act.$remark.',维修状态：'.$weixiu_status));
				}
				$result['success'] = 1;
			}
			else
			{
				$result['error'] = "操作失败";
			}
		}

		Util::jsonExit($result);
	}

        /**
	*add_log,添加日志
	**/
	public function add_log()
	{
		$result = array('success' => 0,'error' => '');
		$id = _Post::getInt('id');
		$status = _Post::getInt('status');
		$model = new AppOrderWeixiuModel($id,42);
		$buchan = $model->getValue('rec_id');
		$remark = _Post::get('remark_log');//	操作日志备注

		$arr_log = array(
				'do_id'			=>$id,
				'user_name'		=>$_SESSION['userName'],
				'do_type'		=>'app_order_weixiu,update'
			);
		
		$check=$this->checkStatus($model->getValue('status'),$status);
		if($check['success']!=1)
		{
			Util::jsonExit($check);
		}
		
		//备注
		if ($status == 1)
		{
			$frequency = $model->getValue('frequency');
			$model->setValue('frequency',$frequency+1);
			$model->setValue('confirm_time',date("Y-m-d H:i:s"));
			$model->save(true);
			$arr_log['content'] ="维修单备注,布产号：".$buchan.';'.$remark;
			$res =$this->add_weixiu_log($arr_log);

		}
		//质检未过
		else if ($status == 2)
		{
			//更新数据库状态--质检未过--2015-10-24
			$model->EditWeixuQc($id,2);
			
			//最新质检未通过时间:记录最新一次操作”质检未通过”,的操作时间--2015-10-24
			$dt = date("Y-m-d H:i:s",time());
			$model->EditQcNopDt($id,$dt);
			
			// 质检次数:新增维修单默认为0,每点击一次”质检未通过”,次数加1,--2015-10-24
			$ret = $model->EditQctimes($id,1);
			
			// print_r($ret);
			// exit;
			
			$arr_log['content'] ="维修单质检未过,布产号：".$buchan.';'.$remark;
			$res =$this->add_weixiu_log($arr_log);

		}
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}
	//打印标签
	public function prints()
	{
		$ids = _Request::get('_ids');   //订单号字符串
		$repair_type = array(
			"1" => "新货维修",
			"2" => "售后维修",
			"3" => "店面维修",
			//"4" => "腾讯维修",
			"5" => "库房维修"
		);
		$html = '';
		$order_sn_str = explode(',', $ids);
		$model = new AppOrderWeixiuModel(42);
		$data = array();
		$dd = new DictView(new DictModel(1));

		foreach ($order_sn_str as $key => $val)
		{
			$weixiuinfo = $model->getWeixiuOrderInfo($val);
			$data['order_sn'] = $weixiuinfo['order_sn'];
			$data['id'] = $val;
			$data['consignee'] = $weixiuinfo['consignee'];
			$data['goods_id']  = $weixiuinfo['goods_id'];
			$data['after_sale'] = $weixiuinfo['after_sale'];
            $data['order_class'] = $weixiuinfo['order_class'];
			$attr = $model->getGoodsAttr($data['goods_id']);
			if(isset($attr['data']['caizhi']) && $attr['data']['caizhi'] != '')
			{
				$data['zhuchengse'] = $attr['data']['caizhi'];
			}
			else
			{
				$data['zhuchengse'] = '';
			}
			if(isset($attr['data']['shoucun']) && $attr['data']['shoucun'] != '')
			{
				$data['shoucun'] = $attr['data']['shoucun'];
			}
			else
			{
				$data['shoucun'] = '';
			}
			if(isset($attr['data']['zuanshidaxiao']) && $attr['data']['zuanshidaxiao'] != '')
			{
				$data['zuanshidaxiao'] = $attr['data']['zuanshidaxiao'];
			}
			else
			{
				$data['zuanshidaxiao'] = '';
			}
			#//整理 ;
			$str_msg = explode(',',$weixiuinfo['repair_act']);
			$str = '';
			foreach ($str_msg as $key=>$val)
			{
				if($key)
				{
					$str .= ','.$dd->getEnum('weixiu.action',$val);
				}
				else
				{
					$str .= $dd->getEnum('weixiu.action',$val);
				}
			}
			$data['repair_act'] = $str;
			$data['remark'] = $weixiuinfo['remark'];
			$maintainType = empty($repair_type[$weixiuinfo['re_type']]) ? '' : $repair_type[$weixiuinfo['re_type']];
			$subStrMaintainType = substr($maintainType,0,3);
			$html.= $this->fetch('weixiu_print.html',array(
					'd' => $data,
					'subStrMaintainType' => $subStrMaintainType
			));
		}
		$this->render('foreach.html', array(
				'html'=>$html,
		));
	}
	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppOrderWeixiuModel($id,42);

		$res = $model->delete(array('id'=>$id));
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
	//添加日志
	public function add_weixiu_log($arr_log)
	{
		$model_log = new AppOrderWeixiuLogModel(42);
		return $model_log->saveData($arr_log,array());

	}
	//获取加工商信息
	public function get_pro_list($arr = array())
	{
		$model = new ApiDataModel();
		return $model->GetSupplierList($arr);
	}

	public function getConsignee()
	{
		$bc_sn = _Post::getString('bc_sn');
		$order_sn = _Post::getString('order_sn');
		$model = new ApiDataModel();
		if($bc_sn != '')
		{
			$data =	$model->getConsignee(['bc_sn'=>$bc_sn]);
			// print_r($data);exit;
			if(isset($data['from_type']) && $data['from_type'] == 2){
				Util::jsonExit($data);
			}else{
				echo '0';
			}
			exit;
		}
		if($order_sn != '')
		{
			// $model = new ApiSalesModel();
			$res =	$model->getConsigneeOrder_sn(['order_sn'=>$order_sn]);	//根据订单号获取用户名
			if($res['error'] == 0)
			{
				Util::jsonExit($res['return_msg']);
			}
			else
			{
				$model = new ApiSalesModel();
				$res = $model->getConsignee($order_sn);
				$ret = array('consignee'=>$res['return_msg']['consignee'], 'channel_class'=>$res['return_msg']['channel_class'], 'bc_sn'=>'');
				Util::jsonExit($ret);
			}
		}

	}

	/**
	 *	index_report，通过报表功能过来的搜索框
	 */
	public function index_report ($params)
	{
		$this->render('app_order_weixiu_search_form_report.html',array(
				'bar'		=>Auth::getBar(),
				'dd'		=>new DictView(new DictModel(1)),
				'pro_list'	=> $this->get_pro_list(array())
		));
	}
	
	/**
	 *	search，列表
	 */
	public function search_report ($params)
	{
		$args = array(
				'mod'			=> _Request::get("mod"),
				'con'			=> substr(__CLASS__, 0, -10),
				'act'			=> __FUNCTION__,
				'id'				=> _Request::get('id'),
				'order_sn'			=> _Request::get('order_sn'),
				'rec_id'			=> _Request::get('rec_id'),
				'consignee'			=> _Request::get('consignee'),
				're_type'			=> _Request::get('re_type'),
				'repair_act'		=> _Request::get('repair_act'),
				'repair_factory'	=> _Request::get('repair_factory'),
				'status'			=> _Request::get('status'),
				'order_time_s'		=>_Request::get('order_time_s'),
				'order_time_e'		=>_Request::get('order_time_e'),
				'confirm_time_s'	=>_Request::get('confirm_time_s'),
				'confirm_time_e'	=>_Request::get('confirm_time_e'),
				'factory_time_s'	=>_Request::get('factory_time_s'),
				'factory_time_e'	=>_Request::get('factory_time_e'),
				're_end_time_s'		=>_Request::get('re_end_time_s'),
				're_end_time_e'		=>_Request::get('re_end_time_e'),
				'receiving_time_s'	=>_Request::get('receiving_time_s'),
				'receiving_time_e'	=>_Request::get('receiving_time_e'),
				'frequency'			=>_Request::get('frequency'),
		);
		$page	= isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where	= array(
				'id'				=>	$args['id'],
				'order_sn'			=>  $args['order_sn'],
				'rec_id'			=>  $args['rec_id'],
				'consignee'			=>  $args['consignee'],
				're_type'			=>  $args['re_type'],
				'repair_act'		=>  $args['repair_act'],
				'repair_factory'	=>  $args['repair_factory'],
				'status'			=>  $args['status'],
				'order_time_s'		=>  $args['order_time_s'],
				'order_time_e'		=>  $args['order_time_e'],
				'confirm_time_s'	=>	$args['confirm_time_s'],
				'confirm_time_e'	=>	$args['confirm_time_e'],
				'factory_time_s'	=>	$args['factory_time_s'],
				'factory_time_e'	=>	$args['factory_time_e'],
				're_end_time_s'		=>	$args['re_end_time_s'],
				're_end_time_e'		=>	$args['re_end_time_e'],
				'receiving_time_s'	=>	$args['receiving_time_s'],
				'receiving_time_e'	=>	$args['receiving_time_e'],
				'frequency'			=>  $args['frequency'],
		);
	
		$model			= new AppOrderWeixiuModel(41);
		$pro_list		= $this->get_pro_list(array());//全部供应商
		$data			= $model->pageList($where,$page,10,false);
		$pageData		= $data;
		$pageData['filter']		= $args;
		$pageData['jsFuncs']	= 'app_order_weixiu_search_page_report';
		$view =	new AppOrderWeixiuView(new AppOrderWeixiuModel(41));
	
		$this->render('app_order_weixiu_search_list_report.html',array(
				'pa'		=>	Util::page($pageData),
				'page_list' =>$data,
				'dd'		=>new DictView(new DictModel(1)),
				'pro_list'	=>$pro_list,
				'view'		=>$view
	
		));
	}

    /**
    * 导出
    */
    public function download()
    {

        $whereinfo = $this->_search_where($params);
        $where = $whereinfo['where'];
        $model          = new AppOrderWeixiuModel(41);
        $data           = $model->pageList($where,1,9999999,false);
        $view = new AppOrderWeixiuView($model);
        $dd = new DictModel(1);
        if(!empty($data['data'])){

            $xls_content = "维修单号,订单号,线上线下,布产号,调拨单号,货号/无账流水号,客户姓名,维修类型,维修工厂,制单人,跟单人,制单时间,预计出厂时间,维修内容,下单时间,状态,质检状态,质检次数,质检人,质检时间,最新质检未过时间,出货时间,出货单号,维修费用,备注,是否超期\r\n";
            foreach ($data['data'] as $key => $val) {
                $type = $dd->getEnum("weixiu.type",$val['re_type']);
                $repair_factory = $view->get_pro_name($val['repair_factory']);
                $repair_man = $view->get_user_name($val['repair_man']);
                $repair_act = $view->get_repair_act_con($val['repair_act']);
                $status = $dd->getEnum("weixiu.status",$val['status']);
                $qc_status = $dd->getEnum("weixiu.qc_status",$val['qc_status']);
                $factory_time = $val['factory_time'] == ""?"未下单":$val['factory_time'];

                $xls_content .= $val['id']. ",";
                $xls_content .= $val['order_sn']. ",";
                $xls_content .= $val['channel_class']. ",";
                $xls_content .= $val['rec_id']. ",";
                $xls_content .= $val['change_sn']. ",";
                $xls_content .= $val['goods_id']. ",";
                $xls_content .= $val['consignee']. ",";
                $xls_content .= $type. ",";
                $xls_content .= $repair_factory. ",";
                $xls_content .= $val['repair_make_order']. ",";
                $xls_content .= $repair_man. ",";
                $xls_content .= $val['order_time']. ",";
                $xls_content .= $val['end_time']. ",";
                $xls_content .= $repair_act. ",";
                $xls_content .= $factory_time. ",";
                $xls_content .= $status. ",";
                $xls_content .= $qc_status. ",";
                $xls_content .= $val['qc_times']. ",";

                $xls_content .= $val['end_user_log']. ",";
                $xls_content .= $val['end_time_log']. ",";

                $xls_content .= $val['qc_nopass_dt']. ",";

                $xls_content .= $val['out_goods_time']. ",";
                $xls_content .= $val['out_goods_bill']. ",";
                $xls_content .= $val['wexiu_price']. ",";
                $xls_content .= $val['remark']. ",";
                $xls_content .= $val['is_overdue']. "\n";
            }
        }else{
            $xis_content = "没有数据！";
        }
        header("Content-type:text/csv;charset=gbk");
        header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv");
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo iconv("utf-8", "gbk//IGNORE", $xls_content);
        exit;
    }

}

?>