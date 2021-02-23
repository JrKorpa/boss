<?php
/**
 *  -------------------------------------------------
 *   @file		: DeliveryListController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-29 18:54:22
 *   @update	:
 *  ------------------------DeliveryList-------------------------
 */
class DeliveryListController extends CommonController
{
	protected $smartyDebugEnabled = true;
	protected $whitelist = array('download','search','print_order','batch_print');
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$ExpView = new ExpressView(new ExpressModel(1));
		$SalesChannelsModel = new SalesChannelsModel(1);
        $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
		$this->render('logistics_delivery_search_form.html',array(
			'bar'=>Auth::getBar(),'ExpView'=>$ExpView,
			'getSalesChannelsInfo'=>$getSalesChannelsInfo
			)
			);
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'down_info' => 	_Request::get('down_info')?_Request::get('down_info'):'',
			'freight_no' => _Request::get('freight_no'),
			'express_id' => _Request::get('express_id'),
			'order_no'	  => _Request::get('order_no'),
			'department'	  => _Request::get('department'),
			'remark'	  => _Request::get('remark'),
			'is_print'	  => _Request::get('is_print'),
			'create_name'	  => _Request::get('create_name'),
			'date_time_s' => _Request::get('date_time_s'),
			'date_time_e' => _Request::get('date_time_e'),
			
			'channel_id' => _Request::get('channel_id'),
			'out_order_sn' => _Request::get('out_order_sn'),
			'is_tsyd' => _Request::get('is_tsyd'),
			'page_size' => _Request::get('page_size')

		);

		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array(
			'freight_no'  => $args['freight_no'],
			'express_id'  => $args['express_id'],
			'order_no'	  => $args['order_no'],
			'order_no'	  => $args['order_no'],
			'create_name' => $args['create_name'],
			'department'  => _Request::get('department'),
			'remark'	  => _Request::get('remark'),
			'is_print'	  => _Request::get('is_print'),
			'channel_id'	  => _Request::get('channel_id'),
			'out_order_sn'	  => _Request::get('out_order_sn'),
			'is_tsyd'	  => _Request::get('is_tsyd'),
			'page_size'=>$args['page_size']?$args['page_size']:10
			);

		if(($args['date_time_s'] !='') && ($args['date_time_e'] != '')){
			$where['date_time_s'] = strtotime($args['date_time_s']);
			$where['date_time_e'] = strtotime($args['date_time_e'])+86399;
		}

		//$model = new ShipFreightModel(43);
		$model = new ShipFreightModel(56);//只读数据库
		//var_dump($args['down_info']);exit;
		//导出功能
		if($args['down_info']=='down_info'){
            //error_reporting(E_ALL);
			$data = $model->pageList($where,$page,10000000,false);
			$this->download($data);
			exit;
		}
		$data = $model->pageList($where,$page,$where['page_size'],false);
		$SalesChannelsModel = new SalesChannelsModel(1);
        $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
        //获取所有数据
        $allSalesChannelsData = array();
        foreach ($getSalesChannelsInfo as $val) {
            $allSalesChannelsData[$val['id']] = $val['channel_name'];
        }
		foreach($data['data'] as $key=>$val)
		{
            $data['data'][$key]['channel'] = '';
			foreach(explode(',',$data['data'][$key]['channel_id']) as $channel){
                $data['data'][$key]['channel'] .= isset($allSalesChannelsData[$channel])?$allSalesChannelsData[$channel].',':'';
            }
            $data['data'][$key]['show_id'] = $data['data'][$key]['id'];
            $data['data'][$key]['id'] = str_replace(',','-',$data['data'][$key]['id']);
            $data['data'][$key]['channel'] = rtrim($data['data'][$key]['channel'],',');
		    $data['data'][$key]['order_no'] = explode(',',$data['data'][$key]['order_no']);
            $data['data'][$key]['is_print'] = explode(',',$data['data'][$key]['is_print']);
            $data['data'][$key]['express_id'] = explode(',',$data['data'][$key]['express_id']);
            $data['data'][$key]['create_time'] = explode(',',$data['data'][$key]['create_time']);
        }
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'logistics_delivery_search_page';
		$ExpView = new ExpressView(new ExpressModel(1));
		$this->render('logistics_delivery_search_list.html',array(
			'pa'=>Util::page($pageData),'page_list'=>$data,
			'ExpView'=>$ExpView
		));
	}

	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		//取得快递公司信息
		$ex_model		= new ExpressModel(1);
		$info_express   = $ex_model->getAllExpress();
		//取得部门信息
		$dep_model      = new DepartmentModel(1);
		$info_dep       = $dep_model->getList();
		foreach($info_dep as $k=>$v)
		{
			$info_dep[$k]['name1']=$v['name'];
			$info_dep[$k]['name']=str_repeat('&nbsp;',(count(explode('-',$v['tree_path']))-1)*3).$v['name'];
		}
		$result['content'] = $this->fetch('logistics_delivery_info.html',array(
			'view'=>new ShipFreightView(new ShipFreightModel(43)),
			'info_express' =>$info_express,
			'info_dep'=>$info_dep,
			'view1'=>new DepartmentView(new DepartmentModel(1))
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	//批量登记渲染页
	public function batch_add ()
	{
		$result = array('success' => 0,'error' => '');
		//取得快递公司信息
		$ex_model		= new ExpressModel(1);
		$info_express   = $ex_model->getAllExpress();
		//取得部门信息
		$dep_model      = new DepartmentModel(1);
		$info_dep       = $dep_model->getList();
		foreach($info_dep as $k=>$v)
		{
			$info_dep[$k]['name1']=$v['name'];
			$info_dep[$k]['name']=str_repeat('&nbsp;',(count(explode('-',$v['tree_path']))-1)*3).$v['name'];
		}
		$result['content'] = $this->fetch('logistics_batch_delivery_info.html',array(
				'view'=>new ShipFreightView(new ShipFreightModel(43)),
				'info_express' =>$info_express,
				'info_dep'=>$info_dep,
				'view1'=>new DepartmentView(new DepartmentModel(1))
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
		$result = array('success' => 0,'error' => '');
		//取得快递公司信息
		$ex_model		= new ExpressModel(1);
		$info_express   = $ex_model->getAllExpress();
		//var_dump($info_express);exit;
		//取得部门信息
		$dep_model      = new DepartmentModel(1);
		$info_dep       = $dep_model->getList();
		foreach($info_dep as $k=>$v)
		{
			$info_dep[$k]['name1']=$v['name'];
			$info_dep[$k]['name']=str_repeat('&nbsp;',(count(explode('-',$v['tree_path']))-1)*3).$v['name'];

		}
        $model = new ShipFreightModel(43);
        $data = $model->pageList(['id'=>$id],1,1,false)['data'][0];
        $result['content'] = $this->fetch('logistics_delivery_info.html',array(
            'view'=>new ShipFreightView($data),
            'info_express' =>$info_express,
            'info_dep'=>$info_dep,
            'view1'=>new DepartmentView(new DepartmentModel(1))
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
		$view = new ShipFreightView(new ShipFreightModel($id,43));
		$exp_no = $view->get_freight_no();
		$expModel = Express::getIns();
		$info = $expModel->getExpInfo($exp_no);
		$this->render('logistics_delivery_show.html',array(
			'view'=>$view,'info'=>$info,'bar'=>Auth::getViewBar(),
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$olddo = array();
		$newdo=array(
			'freight_no'		=> _Post::get('freight_no'),
			'sender'		=> _Post::get('sender'),
			'department'			=>_Post::get('department'),
			'consignee'		=> _Post::get('consignee'),
			'cons_address'			=> _Post::get('cons_address'),
			'express_id'			=> _Post::get('express_id'),
			'remark'			=> _Post::get('note'),
			'create_time'			=>time(),
			'create_id'			=>Auth::$userId,
			'create_name'			=>$_SESSION['userName'],
			'is_tsyd'        =>1,
			);

		$newmodel =new ShipFreightModel(44);
		$ret = $newmodel->select2("freight_no","freight_no='{$newdo['freight_no']}' and is_deleted=0",$type=1);
		if($ret){
			$result['error'] = "快递单号重复";
			Util::jsonExit($result);
		}

		if($newdo['express_id'] != 10 && !empty($newdo['freight_no'])){
			$express_v =  new ExpressView(new ExpressModel($newdo['express_id'] ,1));
			$rule = $express_v->get_freight_rule();
			/*
			if($rule && !preg_match($rule,$newdo['freight_no'])){
				$result['error'] ="快递单号与快递公司不符！";
				Util::jsonExit($result);
			};*/
		}
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
	 *	batch_insert，批量登记
	 */
	public function batch_insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

		$freight_no_str = $_REQUEST['freight_no'];
		$freight_no_str=trim(preg_replace('/(\s+|,+)/',',',$freight_no_str));
		$freight_no_str=rtrim($freight_no_str,',');
		$newmodel =new ShipFreightModel(44);
		$ret = $newmodel->select2("freight_no","freight_no in ({$freight_no_str}) and is_deleted=0",$type=1);
		if($ret){
			$result['error'] = "快递单号重复";
			Util::jsonExit($result);
		}
		$freight_no_arr = explode(",", $freight_no_str);
		foreach($freight_no_arr as $val){
			$olddo = array();
			$newdo=array(
					'freight_no'		=> $val,
					'sender'		=> _Post::get('sender'),
					'department'			=>_Post::get('department'),
					//'consignee'		=> _Post::get('consignee'),
				//'cons_address'			=> _Post::get('cons_address'),
					'express_id'			=> _Post::get('express_id'),
					'remark'			=> _Post::get('note'),
					'create_time'			=>time(),
					'create_id'			=>Auth::$userId,
					'create_name'			=>$_SESSION['userName'],
			);

			$newmodel =new ShipFreightModel(44);
			$res = $newmodel->saveData($newdo,$olddo);
			if($res !== false)
			{
				$result['success'] = 1;
			}
			else
			{
				$result['error'] = '添加失败';
			}
		}

		Util::jsonExit($result);
	}

	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$ids = explode(',',_Post::get('id'));
        $order_no_list = explode(',',_Post::get('order_no'));
        $newdo=array(
            'freight_no'		=> _Post::get('freight_no'),
            'express_id'			=> _Post::get('express_id')
        );
        foreach($ids as $id){
            $newmodel =  new ShipFreightModel($id,44);
            $olddo = $newmodel->getDataObject();
            $newdo['id']=$id;
            $res = $newmodel->saveData($newdo,$olddo);
            if($res !== false)
            {
                $result['success'] = 1;
            }else{
                $result['error'] = '修改失败';
                break;
            }
        }
        foreach($order_no_list as $order_no){
                if($order_no)
                {
                    /*
                    $ori_str=array('order_sn'=>$order_no,'express_id'=>$newdo['express_id'],'freight_no'=>$newdo['freight_no']);
                    ksort($ori_str);
                    $ori_str=json_encode($ori_str);
                    $data=array("filter"=>$ori_str,"sign"=>md5('sales'.$ori_str.'sales'));
                    $ret=Util::httpCurl(Util::getDomain().'/api.php?con=sales&act=updateAddressWay',$data);
                    */
                    $sales_api = new ApiModel();
                    $sales_api->sales_api(array('order_sn', 'express_id', 'freight_no'), array($order_no, $newdo['express_id'],$newdo['freight_no']), 'updateAddressWay');
                    
                    $ex_model= new ExpressModel(1);
                    $ex_model= $ex_model->getAllExpress();
                    $aa=array();
                    foreach($ex_model as $k=>$m)
                    {
                        $aa[$m['id']]=$m['exp_name'];
                    }
                    $remark='备注:修改快递信息，';
                    if($olddo['express_id']!==$newdo['express_id'])
                    {
                        $old=isset($aa[$olddo['express_id']])?$aa[$olddo['express_id']]:'';
                        $new=isset($aa[$newdo['express_id']])?$aa[$newdo['express_id']]:'';
                        $remark.='快递方式由'.$old.'改为'.$new.'&nbsp;';
                    }
                    if($olddo['freight_no']!==$newdo['freight_no'])
                    {
                        $remark.='快递单号由'.$olddo['freight_no'].'改为'.$newdo['freight_no'];
                    }
                    $status=ApiModel::checkOrderStatus(array('order_no','create_user','remark'),array($order_no,Auth::$userName,$remark),'AddOrderLog');
                }
        }
		Util::jsonExit($result);
	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$ids = explode('-',$params['id']);
        foreach($ids as $id){
            $model = new ShipFreightModel($id,44);
            $model->setValue('is_deleted',1);
            $model->save();
        }
		$result['success'] = 1;
		Util::jsonExit($result);
	}

	public function download($data) {

		if ($data['data']) {
			//获取全部的有效的销售渠道
		/* 	$SalesChannelsModel = new SalesChannelsModel(1);
			$getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
			//获取所有数据
			$allSalesChannelsData = array();
			foreach ($getSalesChannelsInfo as $val){
				$allSalesChannelsData[$val['id']] = $val['channel_name'];
			}
			foreach ($data['data'] as &$val) {
				$val['status'] = $model->getStatusList($val['status']);
				$val['department'] = $allSalesChannelsData[$val['department']];
			}
			unset($val); */
			//取得快递公司信息
			$ex_model		= new ExpressModel(1);
			$info_express   = $ex_model->getAllExpress();
			
			
			$modelor = new SalesModel(27);
				
			$customer_source_model = new CustomerSourcesModel(1);
				
				// print_r($data['data']);
				 // die();
			//遍历 拼接快递公司名称
			foreach($data['data'] as $key=>$val){
				// 物流管理-快件管理-快件列表,下载,表格增加一列[客户来源],取顾客订单的[客户来源],如果导出有多个订单号，只显示第一个订单号的客户来源，不管来源是否一样
				// 2015-11-04
				$orno = $val['order_no'];//要考虑多个订单号的情况
				$ornoarr = explode(",",$orno);
				$ono = $ornoarr[0];
				$source_id_arr = $modelor->getOrderSourceId($ono);
				
				$source_id = empty($source_id_arr["customer_source_id"])?"":$source_id_arr["customer_source_id"];
				$customer_arr = $customer_source_model->getCustomerSourcesList("`source_name`", array('id' => $source_id));
				$cs_name = empty($customer_arr[0]["source_name"])?"":$customer_arr[0]["source_name"];
				$data['data'][$key]['customer_source_name'] = $cs_name;
				// print_r($customer_source_name);
				// die();
				////////////////////////////////////////////////////////////////////
				
                $data['data'][$key]['is_print'] = explode(',',$data['data'][$key]['is_print']);
                $data['data'][$key]['express_id'] = explode(',',$data['data'][$key]['express_id']);
                $data['data'][$key]['create_time'] = explode(',',$data['data'][$key]['create_time']);

                $data['data'][$key]['express_name'] = '';
                $flag = 0;
                foreach($data['data'][$key]['express_id'] as $express_id){
                    foreach($info_express as $v){

                        if($express_id==$v['id']){
                            $data['data'][$key]['express_name'].=$v['exp_name'].',';
                            $flag = 1;
                            break;
                        }
                    }
                    if($flag == 0)
                        $data['data'][$key]['express_name'].='未查到,';
                    else
                        $flag = 0;
                }

                $is_prints = $data['data'][$key]['is_print'];
                $data['data'][$key]['is_print'] = '';
                foreach($is_prints as $is_print){
                    $data['data'][$key]['is_print'].=($is_print==1?'未打印':'已打印').',';
                }

                $create_times = $data['data'][$key]['create_time'];
                $data['data'][$key]['create_time'] = '';
                foreach($create_times as $create_time){
                    $data['data'][$key]['create_time'].=date('Y-m-d H:i:s',$create_time).',';
                }

			}
			// print_r($data['data']);
				// die();

			//BOSS-701 2015-11-07 物流管理-快件管理-快件列表,下载,表格增加一列[外部订单],取列表显示的外部订单号即可，同一个快递单号如果有多个，导出多个外部订单号	
			$down = $data['data'];
			// print_r($down);
			// die;
			$xls_content = "订单号,快递号,寄件人,寄件部门,订单金额,发件缘由,打印时间,打印状态,快递公司,收货地址,添加时间,操作人,客户来源,外部订单号\r\n";
			$xls_content = iconv("utf-8", "gbk", $xls_content);
			foreach ($down as $key => $val) {
				$xls_content1 = str_replace(',',' ',$val['order_no']) . "\t,";
				$xls_content1 .= str_replace(',',' ',$val['freight_no']) . "\t,";
				$xls_content1 .= str_replace(',',' ',iconv("utf-8","gbk",$val['sender'])) . ",";
				$xls_content1 .= str_replace(',',' ',iconv("utf-8","gbk",$val['department'])). ",";
				$xls_content1 .= str_replace(',',' ',iconv("utf-8","gbk",$val['order_mount'])) . ",";
				$xls_content1 .= str_replace(',',' ',iconv("utf-8","gbk",rtrim($val['remark'],','))) . ",";
				$xls_content1 .= str_replace(',',' ',iconv("utf-8","gbk",$val['print_date'])) . ",";
				$xls_content1 .= str_replace(',',' ',iconv("utf-8","gbk",rtrim($val['is_print'],','))). ",";
				$xls_content1 .= str_replace(',',' ',iconv("utf-8","gbk",rtrim($val['express_name'],','))) . ",";
				$xls_content1 .= str_replace(',',' ',iconv("utf-8","gbk",$val['cons_address'])) . ",";
				$xls_content1 .= str_replace(',',' ',iconv("utf-8","gbk",rtrim($val['create_time'],','))) . ",";
				$xls_content1 .= str_replace(',',' ',iconv("utf-8","gbk",$val['create_name'])) . ",";
				$xls_content1 .= str_replace(',',' ',iconv("utf-8","gbk",$val['customer_source_name'])) . ",";
				$xls_content1 .= str_replace(',',' ',iconv("utf-8","gbk",$val['out_order_id'])) . "\t\n";
				
				
				$xls_content .= $xls_content1;
			}
			// print_r($xls_content);
				// die();
				
		} else {
			$xls_content = '没有数据！';
			$xls_content = iconv("utf-8", "gbk", $xls_content);
		}
		header("Content-type: text/html; charset=gbk");
		header("Content-type:aplication/vnd.ms-excel");
		header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv");
		echo $xls_content;		
		exit;
	}

	public function print_order($params){
		//var_dump($_SESSION);exit;

		$id =_Request::get('id');
		if(_Request::get('chk_value')){
			$id =_Request::get('chk_value');
		}
		//取得快递公司信息
		$ex_model		= new ExpressModel(1);
		$info_express   = $ex_model->getAllExpress();
		//echo "<pre>";print_r($info_express);exit;
		$model = new ShipFreightModel(43);
		$data = $model->getshipfreightById($id);
		$num =count($data);
		//遍历 拼接快递公司名称
	 	foreach($data as $key=>$val){
			foreach($info_express as $v){
				if($val['express_id']==$v['id']){
					$data[$key]['express_name']=$v['exp_name'];
					break;
				}else{
					$data[$key]['express_name']='未查到';
				}
			}
		}
		// echo "<pre>";print_r($data);exit;
		$this->render('print_order.htm', array(
			'data'=>$data,
			'num'=>$num
		));
	}
	//记录 订单打印时间和打印状态
	public function updateprintstatus($params){
		$ids =_Request::get('chk_value');
		$arr_id=explode(',', $ids);
		$date=date('Y-m-d H:i:s');
		foreach($arr_id as $v){
			$newmodel =  new ShipFreightModel($v,44);
			$newmodel->setValue('is_print',2);
			$newmodel->setValue('print_date',$date);
			$res = $newmodel->save(true);

		}
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

	//修改快递方式 +  展示模版
	public function updateShipMethod($params){
		$result = array('success' => 0,'error' => '');
		/** 修改快递方式 逻辑实现代码 **/
		if(isset($params['a']) && !empty($params['a'])){
			$model = new ShipFreightModel( $params['id'] , 43 );
			$model->setValue('express_id', $params['express_id']);
			if($model->save(true)){
				$result['success'] = 1;
				$result['error'] = '修改成功';
 			}else{
				$result['error'] = '修改快递方式失败';
 			}

			Util::jsonExit($result);
		}

		/** 以下代码是展示模版功能 **/
		$id = isset($params['id']) ? $params['id'] : 0;
		if($id == 0){
			$result['error'] = '参数错误';
			Util::jsonExit($result);
		}
		$model = new ShipFreightModel( $id , 43 );
		$express_id = $model->getValue('express_id');

		//取得快递公司信息
		$ex_model		= new ExpressModel(1);
		$info_express   = $ex_model->getAllExpress();
		$result['content'] = $this->fetch('update_ship_method.html',array(
			'info_express'=>$info_express,
			'express_id'=>$express_id,
			'view'=>new ShipFreightView(new ShipFreightModel($id,43)),
		));
		$result['title'] = '修改快递方式';
		Util::jsonExit($result);
	}
	//add by zhangruiying
	function checkStatus()
	{
		$id=_Request::get('id');
		$result=array('status'=>1,'error_msg'=>'');
		if(empty($id))
		{
			$result=array('status'=>2,'error_msg'=>'请选择要操作的记录！');
		}
		$model=new ShipFreightModel($id,43);
		$row=$model->getDataObject();
		if(empty($row))
		{
			$result=array('status'=>3,'error_msg'=>'对象不存在！');
		}
		if($row['order_no'])
		{
			$order_no=$row['order_no'];

			$status=ApiModel::checkOrderStatus(array('order_no'),array($order_no),'checkOrderStatus');
			if($status==false)
			{
				$result=array('status'=>4,'error_msg'=>'已发货和已收货确认的不能修改');
			}
		}
		Util::jsonExit($result);

	}
	
	/** 获取快递公司列表 **/
	public function getAllExpress(){
		$ex_model = new ExpressModel(1);
		return $ex_model->getAllExpress();
	}
	/** 获取公司列表 **/
	public function getCompanyList(){
		$model = new CompanyModel(1);
		return $model->getCompanyTree();//公司列表
	}
	//批量打印
	public function batch_print()
	{
		/** 获取快递公司列表 **/
		$kuaidi = $this->getAllExpress();
		/** 获取公司列表 **/
		$company = $this->getCompanyList();
		/** 获取仓库列表 **/
		
		
		$newModel = new ShipFreightModel(44);
		$model = new ShipParcelDetailModel(43);
		$ids=  _Request::getString('ids');
		$ids=explode(',',$ids);
		$muti=array();
		$pf="批发客户";
		$WarehouseModel=new WarehouseModel(21);
		foreach($ids as $id)
		{
			$temp=array();
			$info = $newModel->select2($fields = ' `express_id`, `freight_no`,`order_no`,`is_tsyd`' , $where = ' `id` = '.$id , $type= 2);
			if($info['is_tsyd']==0){
				die('序号为'.$id."不是天生一对订单生成的快递单");
			}
			foreach($kuaidi as $val){
				if($val['id'] == $info['express_id']){
					$info['express_id'] = $val['exp_name'];
					break;
				}
			}
			
			$order_sn=$info['order_no'];
			$bill_arr=explode(',', $order_sn);
			$date=array();
			foreach ($bill_arr as $bill_no){
				
				$billArr=$WarehouseModel->getWarehouseBill($bill_no);
				$billArr['bill_no']=$bill_no;
				$to_customer_id=$billArr['to_customer_id'];
				if($pf != '批发客户' && $pf != $to_customer_id){
					die('选择的批发销售单必须是同一个批发客户，才允许批量打印');
				}else{
					$pf=$to_customer_id;					
				}
				$date[]=$billArr;
				
			}
			
			$temp['data']=$date;
			$temp['info']=$info;
			$temp['time']=date('Y-m-d H:i:s');			
			$muti[]=$temp;
		}
		$ids=implode(',',$ids);
		$this->render('print_baoguo.html',array(
				'muti'=>$muti,
				'ids'=>$ids,
				'is_muti'=>1
		));
	
	}
	
	/** 更改包裹单打印状态 **/
	public function changePrintStatus($params){
		$result = array('success' => 0,'error' =>'');
		$id = $params['id'];
		$ids=  explode(',', $id);
		foreach($ids as $id)
		{
			$model = new ShipFreightModel($id,44);
			$model->setValue('is_print', 1);
			$sta = $model->save();
			
				$result['success'] = 1;
			
		}
	
		Util::jsonExit($result);
	}
	
	
}?>
