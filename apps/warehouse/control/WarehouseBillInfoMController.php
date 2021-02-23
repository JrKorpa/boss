<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoMController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 17:24:09
 *   @update	:
 *    调拨单~~~~
 *   2016年1月1日 加入“加价调拨”功能，总公司到分公司的货品要加价调拨
 *  -------------------------------------------------
 */


class WarehouseBillInfoMController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('hedui_goods','printBill','printSum','printSumTo','print_q','printHedui','printHunbohui','printcode');
	private $ARY_MASTER_COMPANY = array('58', '223');

	/****
	获取公司 列表
	 ****/
	public function company()
	{
		$model     = new CompanyModel(1);
		$company   = $model->getCompanyTree();//公司列表
		return $company;
	}
	/***
	获取有效的仓库
	 ***/
	public function warehouse()
	{
		$model_w	= new WarehouseModel(21);
		$warehouse  = $model_w->select(array('is_delete'=>1),array("id","name",'code'));
		return $warehouse;
	}

	/**
	 *	index，渲染添加页面
	 */
	public function index($params)
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }		
		// Get User Info :
		/*$user_id    = Auth::$userId;
		$user_model = new UserModel($user_id,1);
		$user       = $user_model->getDataObject();
		$user_company_id = $user['company_id'];*/
		
		$from_company = $this->getCompanyList();

		$to_company = $this->company();
		/*if ($user_company_id != 58) {
			foreach ($to_company as $key => $value) {
				if ($user_company_id == $value['id'] || $value['id'] == 58) {
					continue;
				}
				unset($to_company[$key]);
			}
		}*/
		
		$this->render('warehouse_bill_info_m_info_add.html',
			array(
				'view' => new WarehouseBillView(new WarehouseBillModel(21)),
				'from_company'=> $from_company,
				'warehouselist' => $this->warehouse(),	//仓库列表
				'companylist' => $to_company,	//公司列表
			));
	}

	/**
	 * 二级联动 根据公司，获取选中公司下的仓库
	 */
	public function getTowarehouseId(){
		$to_company_id = _Request::get('id');
		$model = new WarehouseBillInfoMModel(21);
		$data = $model->getWarehouseByCompany($to_company_id);
		$this->render('option.html',array(
			'data'=>$data,
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'','submits'=>0);
		$companyModel = new CompanyModel(1);
		$to_company_name = $companyModel->getCompanyName(_Request::get('to_company_id'));
		$from_company_name = $companyModel->getCompanyName(_Request::get('from_company_id'));
		$warehouseModel = new WarehouseModel(_Request::get('to_warehouse_id'), 21);
		$to_warehouse_name = $warehouseModel->getValue('name');
		$order_sn          = trim(_Request::get('order_sn'));
		$old_system        = _Request::get('old_system');		//检验是否输入的是老系统的订单，是的话，不做订单与货品的绑定校验
        $label_price_total = _Request::get('label_price_total',0);
        $submits = _Request::get('submits');

		$bill_info = array(
			'to_company_id'=>_Request::get('to_company_id'),
			'to_warehouse_id'=>_Request::get('to_warehouse_id'),
			'from_company_id'=>_Request::get('from_company_id'),
				
			'goods_num'=>0,
			'goods_total'=>0,
			'goods_total_jiajia'=>0,
			'shijia'=>0,
			'yuanshichengben'=>0,

			'bill_note'=>_Request::get('bill_note'),
			'consignee'=>_Request::getString('consignee'),
			'to_warehouse_name'=>$to_warehouse_name,
			'to_company_name'=>$to_company_name,
			'from_company_name'=>$from_company_name,
			'create_user'=>$_SESSION['userName'],
			'create_time'=>date('Y-m-d H:i:s'),
			'bill_type'=>'M',
			'order_sn'=>$order_sn,
            'label_price_total' => $label_price_total
			
		);
		
		
		/***********************************1---单据提交数据空值验证start**********************/
		// 限定条件：Y单出入库公司必须不一致
		/*if ($bill_info['from_company_id'] == $bill_info['to_company_id']) {
			$result['error'] = '加价调拨单的出入库公司必须不一致';
			Util::jsonExit($result);
		}
		*/
		
		//限制
		$to_company_id = $bill_info['to_company_id'];
		$from_company_id = $bill_info['from_company_id'] ;
		if (SYS_SCOPE == 'boss') {
		     //TODO: 58 与各分公司之间可以，分公司内部可以，分公司之间不能做M单
		    if( $to_company_id != 58 && $from_company_id != 58 && $to_company_id != $from_company_id)
		    {
	            $result['error'] = '分公司之间不能制调拨单';
	            Util::jsonExit($result);
		    }
		} else {
		     //TODO: 公司不同就不能做M单
			if( $to_company_id != 58 && $from_company_id != 58 && $to_company_id != $from_company_id) {
				$result['error'] = '分公司之间不能制调拨单';
				Util::jsonExit($result);
			}
		}
		
		if(isset($_POST['data']))
		{
			$goods = $_POST['data'];
		}
		if(empty($goods))
		{
			$result['error'] = '请输入货品明细';
			Util::jsonExit($result);
		}
		if($result['error'] != '')
		{
			$result['error'] = '666';
			Util::jsonExit($result);
		}

		
		if(!$bill_info['to_company_id'])
		{
			$result['error'] = '请选择入库公司';
			Util::jsonExit($result);
		}
		
		if(!$bill_info['to_warehouse_id'])
		{
			$result['error'] = '请选择入库仓库';
			Util::jsonExit($result);
		}
		
		if(!$bill_info['from_company_id'])
		{
			$result['error'] = '请选择出货公司';
			Util::jsonExit($result);
		}
		
		//调拨单，调入仓库是“借货”，备注必填，提示：请在备注栏填写借货原因
		if($bill_info['to_warehouse_id']==4 && empty($bill_info['bill_note'])){
			$result['error'] = '调入仓库为借货，请在备注栏填写借货原因！';
			Util::jsonExit($result);
		}
		
		/********************************************1---单据提交数据空值验证end**********************/
		$arr_order = array();
		if($order_sn && !$old_system){
			/**
			 * 调拨单保存时，需要判断入库仓库，仓库类型是【待取】，必须输入订单号，如果输入的货号绑定了订单，需要判断货号绑定的订单与单据输入的订单号一致，如果货号不绑定，不做判断。
			*/
			$this->CheckDq($bill_info['to_warehouse_id'] , $goods , $order_sn);	
			
			/**start**/
			//1、	订单的转仓，必须输入有效的订单号，并且验证转仓单货品是订单所绑定的货。转仓单的目的展厅必须和订单的展厅一致。
			//2、	如果是发货到展厅的订单的转仓，审核转仓单要触发发货状态为已到店，并且允许配货。
			$arr_order = $this->zhuancang_yz($bill_info['order_sn'],$bill_info['to_company_id']);
			/**end***/
		}
		$goods_id_str = "";

		// 确定加价类型：
		$jiajia_type = 2; // 默认不加价
		$from_company_id = (int) $bill_info['from_company_id'];
		$to_company_id   = (int) $bill_info['to_company_id'];
		if($from_company_id && $to_company_id)
		{
			// 总公司 ---> 分公司
			if (in_array($from_company_id, $this->ARY_MASTER_COMPANY) 
				&& !in_array($to_company_id, $this->ARY_MASTER_COMPANY)) {
				$jiajia_type = 0;
			}
			// 分公司 ---> 总公司
			if (!in_array($from_company_id, $this->ARY_MASTER_COMPANY)
				&& in_array($to_company_id, $this->ARY_MASTER_COMPANY)) {
				$jiajia_type = 1;
			}

            // 分公司 ---> 分公司
            if (!in_array($from_company_id, $this->ARY_MASTER_COMPANY)
                && !in_array($to_company_id, $this->ARY_MASTER_COMPANY)) {
                $jiajia_type = 1;
            }
		}

		$model = new WarehouseBillInfoMModel(21);
		$goodsModel = new WarehouseGoodsModel(21);
		$styleModel = new SelfStyleModel(17);
		/*****货品状态验证?***/
		foreach($goods as $k => $row){		    
			/** 过滤啥也没填的，空的明细信息 **/
			if( ($row[0] == '') && ($row[1] == '') && ($row[2] == '')  && ($row[3] == '')  && ($row[4] == '')  && ($row[5] == '')  && ($row[6] == '')  && ($row[7] == '')  && ($row[8] == '')  && ($row[9] == '') ){
				unset($goods[$k]);continue;
			}
			$num = $k+1;
			$style_sn = $row[1];//款号
			$isSanShengSanShi = $styleModel->isSanShengSanShi($style_sn);
			if(empty($submits) && $isSanShengSanShi && (($from_company_id<>$to_company_id) ||($bill_info['to_warehouse_id']==523 && $from_company_id==58 && $to_company_id==58)) ){
			    $result['submits'] = 1;//提交次数
			    $result['error'] = "“三生三世”产品，请核对防伪标是否配齐!";
			    Util::jsonExit($result);
			}
			/*
			//根据货号，查询warehouse_goods 判断货品是否是库存状态，不是的话，就不能制单
			$status = $model->checkGoodsStutasByGoodsId($row[0]);
			if($status != 2){
				$result['error'] = "第{$num}个货号:{$row[0]}的货品不是库存状态，不能制调拨单!!!";
				Util::jsonExit($result);
			}
			
			//检测货品是否在出库公司中
			$ext = $model->checkGoodsIsInFromCompany($row[0], $bill_info['from_company_id']);
			if(!$ext){
				$result['error'] = "第{$num}个货号:{$row[0]}的货品不属于出库公司的货品，不能制调拨单!";
				Util::jsonExit($result);
			}*/

			$item=$model->getGoodsM($row[0]);
			if($item){
				if($item['is_on_sale'] != 2){
					$result['error'] = "第{$num}个货号:{$row[0]}的货品不是库存状态，不能制调拨单!!!";
					Util::jsonExit($result);
				} 
				if($item['company_id'] != $bill_info['from_company_id']){
					$result['error'] = "第{$num}个货号:{$row[0]}的货品不属于出库公司的货品，不能制调拨单!";
					Util::jsonExit($result);
				}	
			    if($from_company_id==58){
					$checksession = $this->checkSession($item['warehouse_id']);
					if(is_string($checksession)){
						$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$item['warehouse']."</b>".$checksession."</span>的权限请联系管理员开通");
						Util::jsonExit($result);
					}
			    }
			}else{
				$result['error'] = "不能获取第{$num}个货号:{$row[0]}的货品信息，不能制调拨单!";
				Util::jsonExit($result);				
			}			
			/**start**/
			if ($bill_info['order_sn'])//输入了订单号，则校验输入的货号
			{
				$id_goods = $model->getGoodsIdByOrderGoodsID($row[0]);
				if (!in_array($id_goods,$arr_order))
				{
					/*$result['error'] = '货号'.$row['0']."和不是订单号".$bill_info['order_sn']."绑定的货，不可以调拨。";
					Util::jsonExit($result);*/
				}
			}else{
				//没有输入订单号，则校验货号
				$order_goods_id = $goodsModel->getOrderGoodsId($row[0]);
				//如果货品被绑定了，但是又不是转去跟单维修库，也不是同一个公司间的调拨的话，必须要提示。169 = 跟单维修库
				if($order_goods_id && ($bill_info['to_warehouse_id'] != 169) && ($bill_info['from_company_id'] != $bill_info['to_company_id'])){
					$result['error'] = "第{$num}个货号: <span style='color:red;'>{$row[0]}</span> 的货品已经绑定订单，必须输入货品对应订单号或者为此货品解绑，否则不能制调拨单!";
					Util::jsonExit($result);
				}
			}
			
			/**end*/

			/** 各种计算~  写入主表信息**/
			/** 成本价如果没有填，初始化成 0.00**/
			$row[2] = $row[2] ? $row[2] : 0.00;
			
			// 加价率
			$row[15] = $goodsModel->getBillJiajiaInfo($row[16], $jiajia_type, $row[0],null);
			$goods[$k]['jiajialv'] = $row[15];

			// 加价成本：
			$row[14] = $row[2] * (1 + $row[15]/100);

			$bill_info['goods_num'] += 1;
			$bill_info['goods_total'] += $row[2];
			$bill_info['goods_total_jiajia'] += $row[14];

			//取得所有货号
			if ($k)
			{
				$goods_id_str .= ",'".$row[0]."'";
			}
			else
			{
				$goods_id_str .= "'".$row[0]."'";
			}

			$goods[$k]['addtime'] = date('Y-m-d H:i:s');	//明细添加时间
			
		}/** END foreach **/
		//

		/*************************************2 验证货----end*******************************************/

        /**
        *2、网络库实际业务中，出现发错货的情况，将贵的商品用作价格较低的订单发货了。故在M|调拨单绑定唯品会订单的情况下，增加系统稽核：
        *M单的【成本合计】*1.05<顾客订单的【成交价】
        *顾客订单的【客户来源】=唯品会B2C id = 2034
        *如果等于或者高于了M单的成本合计，就不允许保存，并且提示“请检查核实调拨单与订单中的货品价格”；
        */
        $orderlist = array();
        if(!empty($order_sn)){
            $orderlist =ApiModel::sales_api('GetOrderInfoBySn',array('order_sn'),array($order_sn));
        }
        $customer_source_id = 0;
        $order_price = 0;
        if(!empty($orderlist) && isset($orderlist['return_msg']['customer_source_id'])){
            $customer_source_id = $orderlist['return_msg']['customer_source_id'];
            $order_price = $orderlist['return_msg']['order_amount'];
        }

        $total_price = bcmul($bill_info['goods_total'], 1.05, 2);
        if($customer_source_id == 2034 && $total_price>20 && bccomp($total_price, $order_price) != -1){
            $result['error'] = "请检查核实调拨单与订单中的货品价格！";
            Util::jsonExit($result);    
        }

		/************************************3---start--保存***********************************************************/
		$model = new WarehouseBillInfoMModel(22); 

		$goods_id_str = trim($goods_id_str , ',');

		$res = $model->add_shiwu($bill_info,$goods,$goods_id_str);
		/************************************3---end--保存***********************************************************/
// 		$res['success'] =1;
		if($res['success'] == 1)
		{
			$result['success'] = 1;
			$result['x_id'] = $res['x_id'];
			$result['label'] = $res['label'];
			$result['tab_id'] = mt_rand();
			$result['error'] = '添加调拨单成功!!!';
			$result['check'] = $res['check'];
			//AsyncDelegate::dispatch('warehouse', array('event'=>'bill_M_created', 'bill_id' =>$res['x_id']));
		}
		else
		{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}/** End insert **/

	/***
	 * amountMax根据出库公司、和入库仓库计算转仓金额限制
	***/
	public function amountMax($params)
	{
		$result = array('success'=>0,'error'=>'');
		$from_company_id   = $params['from_company_id'];
		$to_company_id     = $params['to_company_id'];
		$to_warehouse_id   = $params['to_warehouse_id'];
		$mingyijia		   = $params['mingyijia'];
		$model = new WarehouseBillInfoMModel(22);
		if($from_company_id != $to_company_id)
		{
			//转仓金额金额限制
			$nrom = $model->transferHouseRule($from_company_id,$to_warehouse_id);
			if($nrom != 0)
			{
				if ($mingyijia > $nrom)
				{
					$result['error'] = '转仓金额已超过'.($nrom/10000).'万元,请确认是否继续转仓';
					Util::jsonExit($result);
				}
		   }
		}
		$result['success'] = 1;
		Util::jsonExit($result);
	}
	/**
	 *	edit，渲染修改页面
	 */
	public function edit($params)
	{
		$id = intval($params['id']);
		$model = new WarehouseBillModel($id,21);

		$create_user = $model->getValue('create_user');
		$now_user = $_SESSION['userName'];
		/*
		if($create_user !== $now_user){
			$off = '<button class="btn btn-sm yellow" onclick="util.closeTab(this);" data-url="" name="离开" title="关闭当前页签" list-id="84" data-title="">离开 <i class="fa fa-mail-reply"></i></button>';
			die('亲~ 非本人单据，你是不能编辑的哦！#^_^#  '.$off);
		}
		*/
		$status = $model->getValue('bill_status');
		/** 获取当前单据的入库仓库 **/
		$view = new WarehouseBillView($model);
		$to_warehouse_id = $view->get_to_warehouse_id();
		$warehouse = $this->warehouse();
		foreach($warehouse as $v){
			if($v['id'] == $to_warehouse_id){
				$warehouse_old = $v;break;
			}
		}

		$form_company=$this->getCompanyList();
		$order_sn = $model->getValue('order_sn');
		$res =ApiModel::sales_api('GetOrderInfoByOrdersn',array('order_sn'),array($order_sn));
		//客户名称
		$consignee = (!empty($res['return_msg']['data'])) ? $res['return_msg']['data'][0]['consignee'] : "";	

		// Get User Info :
		/*$user_id    = Auth::$userId;
		$user_model = new UserModel($user_id,1);
		$user       = $user_model->getDataObject();
		$user_company_id = $user['company_id'];*/
		
		$from_company = $this->getCompanyList();

		$to_company = $this->company();
		/*if ($user_company_id != 58) {
			foreach ($to_company as $key => $value) {
				if ($user_company_id == $value['id'] || $value['id'] == 58) {
					continue;
				}
				unset($to_company[$key]);
			}
		}	*/


		$this->render('warehouse_bill_info_m_info_edit.html',array(
			'dd' => new DictView(new DictModel(1)),
			'form_company'=>$form_company,
			'view'=>$view,
		    'bar'=> Auth::getViewBar(),
			'warehouselist' => $this->warehouse(),
			'companylist' => $to_company,
			'status'=>$status,
			'warehouse_old'=>$warehouse_old,
			'consignee'=>$consignee,
		));
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);	//单据ID
		$model = new WarehouseBillModel($id,21);
		$status = $model->getValue('bill_status');
		//获取调拨单的快递单号
		$view = new WarehouseBillView(new WarehouseBillModel($id, 21));
		$bill_no = $view->get_bill_no();
		$data = ApiModel::shipping_api(array("zhuancang_sn"), array($bill_no), "GetExpressSnByBillno");
		if (isset($data[0]['express_sn']) && !empty($data[0]['express_sn'])){
			$express_sn = $data[0]['express_sn'];
		}else {
			$express_sn = '--';
		}

		$order_sn = $model->getValue('order_sn');
		$res =ApiModel::sales_api('GetOrderInfoByOrdersn',array('order_sn'),array($order_sn));
		$consignee = isset($res['return_msg']['data'][0]['consignee']) ? $res['return_msg']['data'][0]['consignee'] : '';		//客户名称

        //end
		#获取单据附加表ID warehouse_bill_info_m
		$model = new WarehouseBillInfoMModel(21);
		
		//获取取单据取消时的操作用户和操作时间
		$WarehouseBillModel = new WarehouseBillModel(21);
		$billcloseArr=$WarehouseBillModel->get_bill_close_status($id);
		
		
		$this->render('warehouse_bill_info_m_show.html',array(
			'view' => $view,
			'dd' => new DictView(new DictModel(1)),
			'bar'=>Auth::getViewBar(),
			'status'=>$status,
            'express_sn' => $express_sn,
             'consignee' =>$consignee,
			'billcloseArr'=>$billcloseArr,
			'isViewChengbenjia'=>$this->isViewChengbenjia(),
		));
	}
    /**
     *	selectsalepolicy，渲染选择销售政策页面
     */
    public function selectsalepolicy($params)
    {
        $id = intval($params["id"]);
        
        $billModel = new WarehouseBillModel($id, 21);
        $policy_id =$billModel->getGoodsIdinfoByBillId($id);
    
        
        $bill_no = $billModel->getValue('bill_no');
        $goods_id = $billModel->getGoodsidBybillno($bill_no);
		$kk = '';
        foreach ($goods_id as $v) {
            foreach ($v as $k) {
                $kk .= $k . "','";
            }
        }
		$kk2 = '';
        foreach ($policy_id as $v) {
            foreach ($v as $k) {
                $kk2 .= $k . "','";
            }
        }
        $kk2 = rtrim($kk2, ",'");
        $kk2="'".$kk2."'";
        
        $kk = rtrim($kk, ",'");
        $kk="'".$kk."'";
        $policy_goods=$billModel->getNotInpolicy($kk,$kk2,$id);

		$kk3 = '';
        foreach ($policy_goods as $v) {
            foreach ($v as $k) {
                $kk3 .= $k . "','";
            }
        }
        $kk3= rtrim($kk3, ",'");
        $kk3="'".$kk3."'";
        $policy_info=$billModel->getPolicyidBygoodsid($kk);
    
        $this->render('warehouse_bill_info_m_selectsalepolicy.html', array(
        'goods_id' =>$kk,
        'bill_id'=>$id,
        'policy_goods'=>$kk3,
        'policy_info'=>$policy_info,
                 'view' => new WarehouseBillView(new WarehouseBillModel($id, 21))));


    }
    public function getPolicytypeInfo()
    {
        $billModel = new WarehouseBillModel(21);
        $goods_id=_Request::getString('goods_id');
        $policy_goods=_Request::getString('policy_goods');  
        
        $result = array('success' => 0,'error' =>'');
        $policy_type = _Request::getInt('policy_type');
        
        if($policy_type=='1'){
            $policy_info=$billModel->getPolicyidBygoodsid($goods_id);
            $jiajialv=$policy_info['jiajia'];
            $stavalue=$policy_info['sta_value'];
        }
        elseif($policy_type=='2'){
            $policy_info=$billModel->getPolicybillBygoodsid($goods_id);
            $jiajialv=$policy_info['jiajia'];
            $stavalue=$policy_info['sta_value'];
        }
 
        if(!empty($policy_goods) && $policy_goods==""){
            $result['error'] = "$policy_goods 商品未维护相应的销售政策，请与产品中心维护人员联系添加。";
            Util::jsonExit($result);
        }
        
        $result['success']=1;
        $result['msg']=$jiajialv;
        $result['msg1']=$stavalue;
        Util::jsonExit($result);
    }
	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'','submits'=>0);
		$id = _Request::get('id');
		$old_system = _Request::get('old_system');		//校验是否是老系统的订单号，如果是，不校验货品与订单的绑定关系
		$submits = _Request::get('submits');
		/**判断单据是否被审核，或被取消 如果是，那不能修改**/
		$billModel = new WarehouseBillModel($id, 21);
		$create_user = $billModel->getValue('create_user');
		$from_company_id = $billModel->getValue('from_company_id');
		$to_company_id = _Request::get('to_company_id');
		$to_warehouse_id = $billModel->getValue('to_warehouse_id');


/*******************************1---单据验证 start******************************************/	
		$now_user = $_SESSION['userName'];
		if($create_user !== $now_user)
		{
			$result['error'] = $now_user.",".$create_user.'亲~ 非本人单据，你是不能编辑的哦！#^_^#  ';
			Util::jsonExit($result);
		}

		$status = $billModel->getValue('bill_status');
		if($status == 2)
		{
			$result['error'] = '单据已审核，不能修改';
			Util::jsonExit($result);
		}
		else if($status == 3)
		{
			$result['error'] = '单据已取消，不能修改';
			Util::jsonExit($result);
		}
/*******************************1---单据验证 end******************************************/		

/*******************************2---货品验证 start******************************************/		
        $styleModel = new SelfStyleModel(17);
		$goodsModel = new WarehouseGoodsModel(21);
		$newmodel_bill_info_m = new WarehouseBillInfoMModel(22);
		$goods_list = $newmodel_bill_info_m->getBillGoogsList($id);
		#获取明细列表
		//修改明细
		$del_bill_ids = '';
		$del_goods_ids = '';
		foreach ($goods_list as $key => $value) {
		    $style_sn = $value['goods_sn'];//款号
		    $isSanShengSanShi = $styleModel->isSanShengSanShi($style_sn);
		    if(empty($submits) && $isSanShengSanShi && $from_company_id<>$to_company_id){
		        $result['submits'] = 1;//提交次数
		        $result['error'] = "“三生三世”产品，请核对防伪标是否配齐!";
		        Util::jsonExit($result);
		    }
			$del_goods_ids .= ',\''.$value['goods_id'].'\'';
			$return_id = $newmodel_bill_info_m->getIDBygoodsID( $value['goods_id'] );
			if(!$return_id){
				$result['error'] = "货品：<span style='color:red;'>{$value['goods_id']}</span> 已经不存在";
				Util::jsonExit($result);
			}
			$del_bill_ids .= ','.$return_id;	//获取post过来的有效数据ID
		}

		$del_bill_ids = ltrim($del_bill_ids, ',');
		$del_goods_ids = ltrim($del_goods_ids, ',');
		if(!isset($_POST['data'])){
			$result['error'] = '请填写明细列表';
			Util::jsonExit($result);
		}else{
			$goods = $_POST['data'];
		}
		$order_sn = trim(_Request::get('order_sn'));

		$arr_order = array();
		
		if ($order_sn && !$old_system)
		{
			/**
			 * 调拨单保存时，需要判断入库仓库，仓库类型是【待取】，必须输入订单号，如果输入的货号绑定了订单，需要判断货号绑定的订单与单据输入的订单号一致，如果货号不绑定，不做判断。
			*/
			$this->CheckDq($to_warehouse_id , $goods , $order_sn);
			
			/**start**/
			//1、	订单的转仓，必须输入有效的订单号，并且验证转仓单货品是订单所绑定的货。转仓单的目的展厅必须和订单的展厅一致。
			//2、	如果是发货到展厅的订单的转仓，审核转仓单要触发发货状态为已到店，并且允许配货。
			$arr_order = $this->zhuancang_yz($order_sn,$to_company_id);
		}

		// 确定加价类型：
		$jiajia_type = 2; // 默认不加价
		if($from_company_id && $to_company_id)
		{
			// 总公司 ---> 分公司
			if (in_array($from_company_id, $this->ARY_MASTER_COMPANY) 
				&& !in_array($to_company_id, $this->ARY_MASTER_COMPANY)) {
				$jiajia_type = 0;
			}

            // 分公司 ---> 总公司
			if (!in_array($from_company_id, $this->ARY_MASTER_COMPANY)
				&& in_array($to_company_id, $this->ARY_MASTER_COMPANY)) {
				$jiajia_type = 1;
			}

            // 分公司 ---> 分公司
            if (!in_array($from_company_id, $this->ARY_MASTER_COMPANY)
                && !in_array($to_company_id, $this->ARY_MASTER_COMPANY)) {
                $jiajia_type = 1;
            }
		}

		#过滤空的明细
		foreach($goods as $k => $row)
		{
			/** 过滤啥也没填的，空的明细信息 **/
			if( ($row[0] == '') && ($row[1] == '') && ($row[2] == '')  && ($row[3] == '')  && ($row[4] == '')  && ($row[5] == '')  && ($row[6] == '')  && ($row[7] == '')  && ($row[8] == '')  && ($row[9] == '') && ($row[10] == '') )
			{
				unset($goods[$k]);continue;
			}

			$num = $k+1;
            /*
			//检测货品是否在出库公司中
			$ext = $newmodel_bill_info_m->checkGoodsIsInFromCompany($row[0], $from_company_id);
			if(!$ext)
			{
				$result['error'] = "第{$num}个货号:{$row[0]}的货品不属于出库公司的货品，不能制调拨单!";
				Util::jsonExit($result);
			}*/

			$item=$newmodel_bill_info_m->getGoodsM($row[0]);
			if($item){
				if($item['company_id'] != _Request::get('from_company_id')){
					$result['error'] = "第{$num}个货号:{$row[0]}的货品不属于出库公司的货品，不能制调拨单!";
					Util::jsonExit($result);
				}	
			    if($from_company_id==58){
					$checksession = $this->checkSession($item['warehouse_id']);
					if(is_string($checksession)){
						$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$item['warehouse']."</b>".$checksession."</span>的权限请联系管理员开通");
						Util::jsonExit($result);
					}
			    }
			}else{
				$result['error'] = "不能获取第{$num}个货号:{$row[0]}的货品信息，不能制调拨单!";
				Util::jsonExit($result);				
			}


			/**start**/
			if ($order_sn)//输入了订单号，则校验输入的货号
			{
				$id_goods = $newmodel_bill_info_m->getGoodsIdByOrderGoodsID($row[0]);
				//echo $id_goods;exit;
				if (!in_array($id_goods,$arr_order))
				{
					//$result['error'] = '货号'.$row['0']."和不是订单号".$order_sn."绑定的货，不可以调拨。";
					//Util::jsonExit($result);
				}
			}
			else
			{
				//没有输入订单号，则校验货号
				$order_goods_id = $goodsModel->getOrderGoodsId($row[0]);

				//如果货品被绑定了，但是又不是转去跟单维修库，也不是同一个公司间的调拨的话，必须要提示。169 = 跟单维修库
				if($order_goods_id && $to_warehouse_id != 169 && ($from_company_id != $to_company_id)){
					$result['error'] = "第{$num}个货号: <span style='color:red;'>{$row[0]}</span> 的货品已经绑定订单，必须输入货品对应订单号或者为此货品解绑，否则不能制调拨单!";
					Util::jsonExit($result);
				}
			}

			if($row[0] == '')
			{
				$result['error'] .= "\n\r第{$num}个货品未填货号!!!";
			}

			$goods[$k]['addtime'] = date('Y-m-d H:i:s');

			/** 成本价 如果没有填，初始化成 0.00**/
			$goods[$k][2] = $goods[$k][2] ? $goods[$k][2] : 0.00;
			// $goods[$k][13] = $goods[$k][13] ? $goods[$k][13] : 0.00;
			// $goods[$k][14] = $goods[$k][14] ? $goods[$k][14] : 0.00;

			// 加价率
			$goods[$k][14] = $goodsModel->getBillJiajiaInfo($goods[$k][15], $jiajia_type, $goods[$k][0],$id);
			$goods[$k]['jiajialv'] = $goods[$k][14];

			// 加价成本：
			$goods[$k][13] = $goods[$k][2] * (1 + $goods[$k][14]/100);

		}/** end foreach **/
		if(empty($goods))
		{
			$result['error'] = '请输入货品明细';
			Util::jsonExit($result);
		}
		if($result['error'] != '')
		{
			Util::jsonExit($result);
		}

		$companyModel = new CompanyModel(1);
		$to_company_name = $companyModel->getCompanyName(_Request::get('to_company_id'));
		// $from_company_name = $companyModel->getCompanyName(_Request::get('from_company_id'));
		$warehouseModel = new WarehouseModel(_Request::get('to_warehouse_id'), 21);
		$to_warehouse_name = $warehouseModel->getValue('name');
		//修改单据信息
		$bill_info = array(
			'id'=>$id,
			'to_company_id'=>_Request::get('to_company_id'),
			'to_warehouse_id'=>_Request::get('to_warehouse_id'),
			'from_company_id'=>_Request::get('from_company_id'),
			'bill_note'=>_Request::get('bill_note'),

			'goods_num'=>0,
			'goods_total'=>0,
			'goods_total_jiajia'=>0,

			'to_warehouse_name'=>$to_warehouse_name,
			'to_company_name'=>$to_company_name,
			// 'from_company_name'=>$from_company_name,
			'order_sn'=>$order_sn,
            'label_price_total'=>_Request::get('label_price_total',0)
		);
        
		// 限定条件：Y单出入库公司必须不一致
		/*if ($bill_info['from_company_id'] == $bill_info['to_company_id']) {
			$result['error'] = '加价调拨单的出入库公司必须不一致';
			Util::jsonExit($result);
		}*/

		//限制分公司之间不能做加价调拨单
		/*
		if( ($bill_info['to_company_id'] != 58) && ($bill_info['from_company_id'] != 58))
		{
			if($bill_info['to_company_id'] != $bill_info['from_company_id'] ) 
			{
				$result['error'] = '分公司之间不能制调拨单';
				Util::jsonExit($result);
			}
		}*/
		
		//调拨单，调入仓库是“借货”，备注必填，提示：请在备注栏填写借货原因
		if($bill_info['to_warehouse_id']==4 && empty($bill_info['bill_note'])){
			$result['error'] = '调入仓库为借货，请在备注栏填写借货原因！';
			Util::jsonExit($result);
		}
		/** 各种计算~ **/

		foreach ($goods as $gk => $gv) 
		{
			$bill_info['goods_num'] += 1;
			$bill_info['goods_total_jiajia'] += $gv[13];
			$bill_info['goods_total'] += $gv[2];
		}

		if(!$bill_info['to_company_id'])
		{
			$result['error'] = '请选择入库公司';
			Util::jsonExit($result);
		}
		if(!$bill_info['to_warehouse_id'])
		{
			$result['error'] = '请选择入库仓库';
			Util::jsonExit($result);
		}


/*******************************2---货品验证 end******************************************/		

		$billModel = new WarehouseBillModel($id, 21);
		$bill_id = $id;
		$bill_no = $billModel->getValue('bill_no');
		$res = $newmodel_bill_info_m-> update_shiwu($del_bill_ids,$del_goods_ids, $goods, $bill_info, $bill_id, $bill_no);

		if($res['success'] == 1)
		{
			$result['success'] = 1;
			//AsyncDelegate::dispatch('warehouse', array('event'=>'bill_M_updated', 'bill_id' => $bill_id));
		}
		$result['error'] = $res['error'];

		Util::jsonExit($result);
	}

	/**
	 * mkJson 生成Json表单
	 */
	public function mkJson(){
		$id = _Post::getInt('id');

		//$arr = Util::iniToArray(APP_ROOT.'warehouse/data/from_table_bill_m.tab');
		$view = new WarehouseBillInfoMView(new WarehouseBillInfoMModel(21));
		$arr = $view->js_table;

		if(!$id){
			$arr['data_bill_m'] = [
			];
		}else{
			$arr['data_bill_m'] = $view->getTableGoods($id);
		}
		
		$json = json_encode($arr);
		
		echo $json;
	}

	/** 根据货号，查询货品信息 (table插件查询使用) **/
	public function getGoodsInfoByGoodsID($params){
		$goods_id = $params['goods_id'];
		if($goods_id){
			$model = new WarehouseBillInfoMModel(21);
			$goods = $model->getGoodsInfoByGoodsID($goods_id);
			if(!empty($goods)){
				/*if($goods['order_goods_id'] != 0){
					$error = "货号为<span style='color:red;'>{$goods_id}</span>的货品已经绑定订单，不能制调拨单";
					$return_json = ['success' =>0 , 'error'=>$error];
					echo json_encode($return_json);exit;
				}*/
				if($goods['is_on_sale'] == 2){	//库存状态
					$return_json = ["{$goods['goods_sn']}", "{$goods['mingyichengben']}", "{$goods['jinzhong']}", "{$goods['caizhi']}", "{$goods['zhushilishu']}", "{$goods['fushilishu']}", "{$goods['fushizhong']}", "{$goods['yanse']}", "{$goods['jingdu']}", "{$goods['jinhao']}", "{$goods['zhengshuhao']}","{$goods['goods_name']}"];
					echo json_encode($return_json);exit;
				}else{
					$error = "货号为<span style='color:red;'>{$goods_id}</span>的货品不是库存状态，不能制调拨单";
					$return_json = ['success' =>0 , 'error'=>$error];
					echo json_encode($return_json);exit;
				}
			}else{
				$error = "仓库查不到货号为<span style='color:red;'>{$goods_id}</span>的货品";
				$return_json = ['success' =>0 , 'error'=>$error];
				echo json_encode($return_json);exit;
			}
		}
	}

	public function getGoodsInfos()
	{
		$g_ids = _Get::getList('g_ids');
		$g_ids = array_filter($g_ids);
		$bill_id = _Get::getInt('bill_id');
		$from_company_id = _Get::getString('from_company_id');
		$to_company_id = _Get::getString('to_company_id');

		// 0 - 加价，1 - 查M单历史纪录加价，2 - 不加价
		// 确定加价类型：
		$jiajia_type = 2; // 默认不加价
		if($from_company_id && $to_company_id)
		{
			// 总公司 ---> 分公司
			if (in_array($from_company_id, $this->ARY_MASTER_COMPANY) 
				&& !in_array($to_company_id, $this->ARY_MASTER_COMPANY)) {
				$jiajia_type = 0;
			}
			//分公司---》总公司
			if (!in_array($from_company_id, $this->ARY_MASTER_COMPANY)
				&& in_array($to_company_id, $this->ARY_MASTER_COMPANY)) {
				$jiajia_type = 1;
			}
			//分公司---》分公司
			if (!in_array($from_company_id, $this->ARY_MASTER_COMPANY)
				&& !in_array($to_company_id, $this->ARY_MASTER_COMPANY)) {
				$jiajia_type = 1;
			}			
		}

		$model = new WarehouseGoodsModel(21);
		$view = new WarehouseBillInfoMView($model);

		$res = $model->fetchGoodsInfo_M($g_ids, 
			$view->js_table['field_bill_goods'], 
			$view->js_table['field_goods'], 
			$view->js_table['map_field'], 
			2, $bill_id, $jiajia_type);
		echo json_encode($res);exit;
	}

	/**审核单据**/
	public function checkBillInfoM($params){
		
		$result = array('success' => 0,'error' =>'');
		$bill_id = $params['id'];
		$bill_no = $params['bill_no'];

		$model = new WarehouseBillModel($bill_id,21);
		$warehouseBillInfo = $model->getDataObject();
		
		/*
		if ($model->getValue('from_company_id') != '58') {
			exit("t100操作");
		}*/

		if($model->getValue('to_warehouse_id'))

		//根据仓库id获取仓库是否锁定(盘点)
		$model ->check_warehouse_lock($model->getValue('to_warehouse_id'));
		$warehouseModel=new WarehouseModel(21);
		$warehouse_name=$warehouseModel->getWarehosueNameForId($model->getValue('to_warehouse_id'));
		$checksession = $this->checkSession($model->getValue('to_warehouse_id'));
		if(is_string($checksession)){
			$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$warehouse_name."</b>".$checksession."</span>的权限,请授权后再来处理");
			Util::jsonExit($result);
		}
		
		//限制所属仓是物控库的非物控部岗位的角色不能审核调拨单 zzm 2015-12-21 boss-976
		if($_SESSION['userType'] != 1)
		{
			$bg_model = new WarehouseBillGoodsModel(21);
			$warehouse_bill_goods_id = $bg_model->select2('goods_id','bill_id = '.$bill_id,1);
			$wg_model = new WarehouseGoodsModel(21);
			$goods_warehouse_id = $wg_model->select2('warehouse_id','goods_id = '.$warehouse_bill_goods_id,1);
			if($goods_warehouse_id == 516){
				$organ_model = new OrganizationModel(1);
				$permission_arr = array_column($organ_model->getDeptUser(26),'id');
				if(!in_array($_SESSION['userId'],$permission_arr))
				{
					$result['error'] = '【单据中货品在仓库物控库，物控库货品调出时需联系周小汝核对该货品是否允许调拨出库】';
					Util::jsonExit($result);
				}
			}
		}

		$to_company_id     = $model->getValue('to_company_id');
		$from_company_id   = $model->getValue('from_company_id');
		if (SYS_SCOPE == 'boss') {
		    //TODO: 58 与各分公司之间可以，分公司内部可以，分公司之间不能做M单
		    if( $to_company_id != 58 && $from_company_id != 58 && $to_company_id != $from_company_id)
		    {
		        $result['error'] = '分公司之间不能制调拨单';
		        Util::jsonExit($result);
		    }
		} else {
		    //TODO: 公司不同就不能做M单
		    /*
		    if ($to_company_id != $from_company_id) {
		        $result['error'] = '不同公司之间不能制调拨单';
		        Util::jsonExit($result);
		    }*/
		}		

		$create_user = $model->getValue('create_user');
		if(SYS_SCOPE == 'boss' && $create_user == $_SESSION['userName'])
		{
		    #除总公司之间转仓  分公司转仓可以审核自己的单据
		    // 深圳分公司没有仓库，产生的总公司调拨数据基本系统转到深圳分公司，实际数据操作是梁全升部门，基于还是内部之间管理
		    if(!($to_company_id == $from_company_id || ($to_company_id == '58' && $from_company_id == '445') || ($to_company_id == '445' && $from_company_id == '58')))
		    {
		        $result['error'] = '不能审核自己的单据';
		        Util::jsonExit($result);
		    }
		}
		/*
		if(SYS_SCOPE == 'zhanting' && $create_user == $_SESSION['userName'])
		{
		    #除总公司之间转仓  分公司转仓可以审核自己的单据
		    // 深圳分公司没有仓库，产生的总公司调拨数据基本系统转到深圳分公司，实际数据操作是梁全升部门，基于还是内部之间管理
		    if($to_company_id == $from_company_id && $to_company_id == '58')
		    {
		        $result['error'] = '不能审核自己的单据';
		        Util::jsonExit($result);
		    }
		}*/		

		/** 如果单据是审核/取消状态 不允许修改 **/
		$status = $model->getValue('bill_status');
		$order_sn = $model->getValue('order_sn');

		if($status == 2){
			$result['error'] = '单据已审核，不能审核';
			Util::jsonExit($result);
		}else if($status == 3){
			$result['error'] = '单据已取消，不能审核';
			Util::jsonExit($result);
		}
		//如果入库公司不是总公司和深圳分公司 则发货状态为已到店
		/* $flag = $model->checkBillTocompanyid($bill_no);
		if ($flag) {
			if ($order_sn)
			{
				$date_time = date("Y-m-d H:i:s");
				#将发货状态改为允许发货 已到店 已到店 	5
				$model_sale = new ApiSalesModel();
				$sale_res = $model_sale->EditSendGoodsStatus(array($order_sn),5,$date_time,$_SESSION['userId']);
				if ($sale_res['error'])
				{
					$result['error'] = '操作失败';
					Util::jsonExit($result);
				}
			}
		} */


		//取得单据信息
		$data  = $model->getDataObject();
		$model = new WarehouseBillInfoMModel(22);
		$goods_list = $model->getBillGoogsList($bill_id);	#获取明细列表
		//var_dump($goods_list);exit;
		$goods_ids ="";
		foreach ($goods_list as $key=>$val)
		{
			if ($key)
			{
				$goods_ids .= ",'".$val['goods_id']."'";
			}
			else
			{
				$goods_ids .= "'".$val['goods_id']."'";
			}
		}
		//data:单据信息 goods_ids：所有货品拼接
		$res = $model->checkBillInfoM($bill_id,$bill_no,$data,$goods_ids);
		if($res){
			$result['success'] = 1;
			$result['error'] = '审核成功';
			//AsyncDelegate::dispatch('warehouse', array('event'=>'bill_M_checked', 'bill_id' => $bill_id, 'goods_ids' => $goods_ids));
		}else{
			$result['error'] = '审核失败';
		}
		Util::jsonExit($result);
	}

	/** 取消单据 **/
	public function closeBillInfoM($params){
		$result = array('success' => 0,'error' =>'');
		$bill_id = $params['id'];
		$bill_no = $params['bill_no'];

		$model = new WarehouseBillModel($bill_id,21);

        $order_sn = $model->getValue('order_sn');
        $order_id = 0;
        if($order_sn){
            $order_info = $model->getOrderInfoByOrderSn($order_sn);
            if($order_info['allow_shop_time']){
                $order_id = $order_info['order_id'];
            }
        }

        
       /*boss-789 调拨单，需要取消 【只能取消自己制的单】的控制，
        * 所有用户只要有任一调出仓库（同一个单据可以从同一个公司调出多件不同仓库的货号）
        * 的调拨单取消权限即可取消自己或别人做的单据，
        * 没有调出仓库的调拨单取消权限的不能取消 
        */
        /* 
		$create_user = $model->getValue('create_user');
		$now_user = $_SESSION['userName'];
		if($create_user !== $now_user){
			$result['error'] = '亲~ 非本人单据，你是不能编辑的哦！#^_^# ';
			Util::jsonExit($result);
		}
      */
        
        $warehouseArr=$model->get_bill_warehouse($bill_id);
        $warehouse_id='';
        $warehouse_name='';
        if(empty($warehouseArr)){
        	$result['error'] = "单据明细为空，不能操作";
        	Util::jsonExit($result);
        }
        
        foreach ($warehouseArr as $v){
        	if($v['warehouse_id']!=''){
        		$checksession = $this->checkSession($v['warehouse_id']);
        		if(!is_string($checksession)){
        			continue;
        		}
        		$warehouse_name.=' '.$v['warehouse'];
        	}
        }
        if(is_string($checksession)){
        	$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$warehouse_name."</b>".$checksession."</span>的权限,请授权后再来处理");
        	Util::jsonExit($result);
        }
        
        
        
        
		/** 如果单据是审核/取消状态 不允许修改 **/
		$status = $model->getValue('bill_status');
		if($status == 2){
			$result['error'] = '单据已审核，不能修改';
			Util::jsonExit($result);
		}else if($status == 3){
			$result['error'] = '单据已取消，不能修改';
			Util::jsonExit($result);
		}

		$Mmodel = new WarehouseBillInfoMModel(22);
		$goods_list = $Mmodel->getBillGoogsList($bill_id);	#获取明细列表
		$goods_ids ="";//货号所有
		foreach ($goods_list as $key=>$val)
		{
			if ($key)
			{
				$goods_ids .= ",'".$val['goods_id']."'";
			}
			else
			{
				$goods_ids .= "'".$val['goods_id']."'";
			}
		}
		//var_dump($goods_ids);exit;
		$res = $Mmodel->closeBillInfoM($bill_id,$bill_no,$goods_ids);
		if($res){
            if($order_id){
                $model->updateSendTimeByOrderid($order_id);
            }
			$result['success'] = 1;
			$result['error'] = '单据取消成功!!!';
		}else{
			$result['error'] = '单据取消失败!!!';
		}
		Util::jsonExit($result);
	}

	/** 取消调拨单据前，检测调拨单是否绑定了快递单 **/
	public function checkbing($params){
		$res = ApiShippingModel::CheckExistBing($params['bill_no']);
		$return = ($res[0] != 0) ? 1 : 0;
		echo $return;exit;
	}

	/** 详情页明细获取 **/
	public function getGoodsInDetails($params){
		$bill_id = $params['bill_id'];
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'bill_id'	=>$bill_id
		);

		$g_model = new WarehouseBillGoodsModel(21);

		$where = array('bill_id'=>$bill_id);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$data = $g_model->pageList($where,$page,20,false);

		$pageData = $data;
		//$pageData['recordCount'] = count($data['data']);
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_bill_goods_show_page';

		$this->render('warehouse_bill_goods.html',array(
			'pa' => Util::page($pageData),
			'dd' => new DictView(new DictModel(1)),
			'data' => $data,
			'isViewChengbenjia'=>$this->isViewChengbenjia(),
		));
	}


	/**
	 * 根据当前用户，所拥有权限的仓库，获取所属的公司列表
	 * @return $return Array array('UserInfo'=> '拥有权限的仓库+公司 ', 'form_company'  => '公司列表');
	 */
	public function getUserWarehouseList(){
		$user_warehouse_id = $_SESSION['userWareNow'];		//用户当前所在的仓库

		$return = array('UserInfo'=> '', 'form_company'  => '');
		$UserInfo = array();
		$form_company =array();
		//获取当前用户仓库
		$warehouseModel = new WarehouseModel($user_warehouse_id , 21);
		$UserInfo['warehouse_id'] = $user_warehouse_id;
		$UserInfo['warehouse_name'] = $warehouseModel->getValue('name');
		$warehouse = $this->warehouse();
		$quanxianwarehouseid = $this->WarehouseListO();
		if($quanxianwarehouseid !== true){
			foreach($warehouse as $key=>$val){
				if(!in_array($val['id'],$quanxianwarehouseid)){
					unset($warehouse[$key]);
				}
			}
		}

		//取得所拥有这个操作的的仓库
		$quanxianwarehouseid = $this->WarehouseListO();
		//通过仓库id取得相关联的公司
		if($quanxianwarehouseid!==true){
			foreach($quanxianwarehouseid as $key=>$val){
				$form_company[$val]=$this->getCompanyInfo($val);
			}
		}else{
			$model     = new CompanyModel(1);
			$form_company   = $model->getCompanyTree();
			foreach($form_company as $key=>$val){
				$form_company[$key]['company_id']=$val['id'];
			}
		}

		//获取当前用户公司信息
		$WarehouseRelModel = new WarehouseRelModel(21);
		$UserInfo['company_id'] = $WarehouseRelModel->GetCompanyByWarehouseId($user_warehouse_id);
		$companyModel = new CompanyModel(1);
		$UserInfo['company_name'] = $companyModel->getCompanyName($UserInfo['company_id']);

		$return = array('UserInfo'=> $UserInfo, 'form_company'  => $form_company);
		return $return;
	}
	//检查订单是否存在
	public function orderSn_check($params)
	{
		$result = array('success' => 0,'error' =>'');
		$order_sn = trim($params['order_sn']);
		if ($order_sn)
		{
			//订单号在老系统
			$order_api_data = array('order_sn'=>$order_sn);
			ksort($order_api_data);
			$order_api_data=json_encode($order_api_data);
			$order_api_data=array("filter"=>$order_api_data,"sign"=>md5('vT1FhNunLYpeiXswbRQC'.$order_api_data.'vT1FhNunLYpeiXswbRQC'));
			$ret=Util::httpCurl('http://order.kela.cn/order_api/GetOrderInfo',$order_api_data , 'vT1FhNunLYpeiXswbRQC');
			$ret=json_decode($ret,true);
			if(is_array($ret['return_msg']) && !empty($ret['return_msg'])){
				$result['error'] = 'old_system';
				$result['success'] = 2;
				Util::jsonExit($result);
			}


			//订单号在新系统
			$res =ApiModel::sales_api('GetOrderInfoByOrdersn',array('order_sn'),array($order_sn));
			if ($res['error'])
			{
				$result['error'] = '订单不存在';
				Util::jsonExit($result);
			}
		}

		$info = array();
		$info['consignee'] = $res['return_msg']['data'][0]['consignee'];		//客户名称
		$info['sale_channel'] = $res['return_msg']['data'][0]['department_id'];	//销售渠道
		$info['order_status'] = $res['return_msg']['data'][0]['order_status'];	//订单状态
        $info['from_company_id'] = '';  //出库公司
        $customer_source_id = isset($res['return_msg']['data'][0]['customer_source_id'])?$res['return_msg']['data'][0]['customer_source_id']:0;
        $info['customer_source_id'] = $customer_source_id;
		if($info['order_status'] == 3 ||$info['order_status'] == 4){ 			//取消/关闭
			$result['error'] = "订单: <span style='color:red'>{$order_sn}</span> 已取消或关闭";
			Util::jsonExit($result);
		}
		$v_channel = new SalesChannelsView(new SalesChannelsModel($info['sale_channel'],1));
		$change_type = $v_channel->get_channel_type();
		if($change_type != 3){
			/*$result['error'] = '销售渠道不是公司';
			Util::jsonExit($result);*/
		}
		//获取所属公司信息
		$order_id= $res['return_msg']['data'][0]['order_id'];	//订单ID
		$dis =ApiModel::sales_api('getDistributionByOrderId',array('order_id'),array($order_id));
		//通过渠道ID获取公司的信息,门店
		if($dis['return_msg']['data'][0]['distribution_type'] == 1){
			//通过shop_name获取门店信息
			$com = ApiModel::management_api(array('shop_name'),array($dis['return_msg']['data'][0]['shop_name']),'GetShopInfoByShopName');

			if(!empty($com[0]['company_name']) && !empty($com[0]['company_name'])){
				$info['company_name'] = $com[0]['company_name'];   //所属公司名称
				$info['to_company_id'] = $com[0]['company_id'];	//所属公司ID
			}else{
				$info['company_name'] = '';   	//所属公司名称
				$info['to_company_id'] = '';	//所属公司ID
			}
			//获取入库仓库(所属公司 ，启用，最新，待取)
			if(!empty($info['to_company_id'])){
				$waremodel = new WarehouseModel(21);
				$wareinfos = $waremodel->getLastWarehouse($info['to_company_id']);

				$min =time();
				if(!empty($wareinfos)){
					foreach($wareinfos as $k=>$v){
								if((time()-strtotime($wareinfos[$k]['create_time'])) < $min){
									$min = time()-strtotime($v['create_time']);
									$info['warehouse_id'] =$v['id'];
									$info['warehouse_name'] =$v['name'];
								}
							}
					}

			}else{
					$info['warehouse_id'] ='';
					$info['warehouse_name'] ='';
				}
		}else{
			//非门店
			/*$info['company_name'] ='BDD深圳分公司';	//总公司到客户默认BDD深圳分公司
			$info['to_company_id'] ='445';
			$info['warehouse_id'] ='503';
			$info['warehouse_name'] ='深圳分公司';*/
            if($customer_source_id == 2034){
                $info['company_name'] ='总公司';   
                $info['to_company_id'] ='58';
                $info['from_company_id'] = 58;
                $info['warehouse_id'] ='';
                $info['warehouse_name'] ='';
            }else{
                //BOSS-1206-----------
                $info['company_name'] ='';   //总公司到客户默认BDD深圳分公司
                $info['to_company_id'] ='';
                $info['warehouse_id'] ='';
                $info['warehouse_name'] ='';
                //--------------------end
            }
		}
		// file_put_contents('e:/8.sql',serialize($info),FILE_APPEND);
		$result['success'] = 1;
		$result['info'] = $info;
		Util::jsonExit($result);
	}

	//1、	订单的转仓，必须输入有效的订单号，并且验证转仓单货品是订单所绑定的货。转仓单的目的展厅必须和订单的展厅一致。
	//2、	如果是发货到展厅的订单的转仓，审核转仓单要触发发货状态为已到店，并且允许配货。2015/3/7 星期六
	public function zhuancang_yz($order_sn,$to_company_id)
	{
		$arr_order =array();
		$res2 = ApiModel::sales_api('GetOrderInfoRow',array('order_sn'),array($order_sn));
		if ($res2['return_msg'])
		{
			$dep_id = $res2['return_msg']['department_id'];
			if ($to_company_id != $dep_id)
			{
				#因为取不到公司id 暂时搁置
				//$result['error'] = "调拨单的入库公司和订单的渠道来源部门不一致";
				//Util::jsonExit($result);
			}
		}
		else
		{
			$result['error'] = $order_sn.'订单号不存在';
			Util::jsonExit($result);
		}
		//exit;
		$res1 = ApiModel::sales_api('GetGoodsInfobyOrderSN',array('order_sn'),array($order_sn));
		//var_dump($res1);exit;
		if (!$res1['error'])//取出订单明细id
		{
			$data_order = $res1['return_msg'];
			foreach ($data_order as $val)
			{
				$arr_order[] = $val['id'];
			}
			return $arr_order;
		}
		else
		{
			$result['error'] = '该订单不存在商品，请重新检查';
			Util::jsonExit($result);
		}
	}

	//婚博会打印明细
	public function printHunbohui() {
		//获取bill_id单据id
		$id = _Request::get('id');
		//数字词典
		$dd =new DictModel(1);
		$model = new WarehouseBillModel($id,21);

		$data  = $model->getDataObject();
		$newmodel = new WarehouseBillInfoMModel(21);
		$billMinfo = $newmodel->getBillGoogsList($id,'row');

		foreach($billMinfo as $key=>$val){
			$data[$key]=$val;
		}

		//货品详情
		$goods_info = $model->getDetail($id);
		//获取加工商支付信息
		$amount=0;
		$BillPay = $model->getBillPay($id);
		foreach($BillPay as $val){
			$amount +=$val['amount'];
		}
		//计算销售价总计 成本价总计 商品数量
		$fushizhong=0;
		$jinzhong=0;
		$zuanshidaxiao=0;
		//统计 副石重 金重
		foreach($goods_info as $key=>$val){
			$goods_id[] = substr(trim($val['goods_id']), -1,1);
			//获取图片 拼接进数组
			$gallerymodel = new ApiStyleModel();
			$gallery_data = $gallerymodel->getProductGallery($val['goods_sn'],1);
			if(isset($gallery_data['thumb_img'])){
				$goods_info[$key]['goods_img']=$gallery_data['thumb_img'];
			}else{
				$goods_info[$key]['goods_img']='';
			}

			$fushizhong +=$val['fushizhong'];
			$jinzhong +=$val['jinzhong'];
			$zuanshidaxiao +=$val['zuanshidaxiao'];
			$data['cat_type'] = $val['cat_type'];
		}
		array_multisort($goods_id, SORT_ASC, $goods_info);
		//           print_r($goods_info);
//            echo '<hr>';
//            print_r($data);exit;
		$this->render('print_hunbohui_detail.html', array(
			'dd' => $dd,
			'data' => $data,
			'goods_info' => $goods_info,
			'fushizhong' => $fushizhong,
			'jinzhong' => $jinzhong,
			'zuanshidaxiao' => $zuanshidaxiao,
			'BillPay' => $BillPay,
			'amount' => $amount
		));
	}

	//打印核对单
	public function printHedui()
	{
		//获取bill_id单据id
		$id = _Request::get('id');
		//数字词典
		$dd = new DictModel(1);
		$model = new WarehouseBillModel($id,21);
		//打印表头信息
		$data  = $model->getDataObject();


		$newmodel = new WarehouseBillInfoMModel(21);
		$billMinfo = $newmodel->getBillGoogsList($id,'row');

		foreach($billMinfo as $key=>$val){
			$data[$key]=$val;
		}

		//货品详情
		$goods_info = $model->getDetail($id);
		//获取加工商支付信息
		$amount = 0;
		$BillPay = $model->getBillPay($id);
		foreach($BillPay as $val){
			$amount += $val['amount'];
		}
		//计算销售价总计 成本价总计 商品数量
		$fushizhong = 0;
		$jinzhong = 0;
		$zuanshidaxiao = 0;
		$src_total = 0; // 没加价之前的成本合计
		//统计 副石重 金重
		foreach($goods_info as $key=>$val){
			//获取图片 拼接进数组
			$gallerymodel = new ApiStyleModel();
			$ary_gallery_data = $gallerymodel->getProductGallery($val['goods_sn'], 1);
			
			if (count($ary_gallery_data) > 0) {
				$gallery_data = $ary_gallery_data[0];
			}

			if(isset($gallery_data['thumb_img'])){
				$goods_info[$key]['goods_img']=$gallery_data['thumb_img'];
			}else{
				$goods_info[$key]['goods_img']='';
			}

			$fushizhong += $val['fushizhong'];
			$jinzhong += $val['jinzhong'];
			$zuanshidaxiao += $val['zuanshidaxiao'];
			$src_total += $val['sale_price'];
		}

		$consignee = ApiModel::sales_api('getConsigneeByOrderSn',array('order_sn'),array($data['order_sn']));
		
		if(empty($consignee['return_msg'])){
			$consignee['return_msg'] = '';
		}


		$this->render('tiaobo_print_detail.html', array(
			'dd' => $dd,
			'data' => $data,
			'goods_info' => $goods_info,
			'fushizhong' => $fushizhong,
			'jinzhong' => $jinzhong,
			'zuanshidaxiao' => $zuanshidaxiao,
			'BillPay' => $BillPay,
			'amount' => $amount,
			'consignee'=>$consignee['return_msg'],
			'src_total' => number_format($src_total, 2),
		));
	}

	//打印详情
	public function printBill() {
		//获取单据bill_id
		$id = _Request::get('id');
		$model = new WarehouseBillModel($id,21);
		$data  = $model->getDataObject();
		//获取货品明细
		$goods_info = $model->getDetail($id);

		$consignee = ApiModel::sales_api('getConsigneeByOrderSn',array('order_sn'),array($data['order_sn']));
		
		if(empty($consignee['return_msg'])){
			$consignee['return_msg'] = '';
		}

		$this->render('zhuancang_print.htm', array(
			'data' => $data,
			'goods_info' => $goods_info,
			'consignee'=>$consignee['return_msg']

		));

	}
	/*
     * 打印标签
     */
	public function print_q($params){
		$id = intval($params['id']);
		$model = new WarehouseBillInfoMModel(21);
		$data = $model->getBillGoogsList($id);
		$str = "序号,货号,款号,零售价,主成色重,主成色,主石粒数,副石粒数,副石重,颜色,净度,金耗,证书号,货品名称\n";
		$i = 1;
		foreach ($data as $key => $val) {
			$str .= $i.",";
			$str .= $val['goods_id'].",";
			$str .= $val['goods_sn'].",";
			$str .= $val['mingyijia'].",";
			$str .= $val['jinzhong'].",";
			$str .= $val['caizhi'].",";
			$str .= $val['zhushilishu'].",";
			$str .= $val['fushilishu'].",";
			$str .= $val['fushizhong'].",";
			$str .= $val['yanse'].",";
			$str .= $val['jingdu'].",";
			$str .= $val['jinhao'].",";
			$str .= $val['zhengshuhao'].",";
			$str .= $val['goods_name']."\n";
			$i ++;
		}
		header("Content-type: text/html; charset=gbk");
		header("Content-type:aplication/vnd.ms-excel");
		header("Content-Disposition:filename=" . iconv("utf-8", "gbk", date("Y-m-d")) . "_print_detail.csv");
		echo iconv("utf-8", "gbk", $str);

	}
	//打印汇总
	public function printSum() {

		//获取单据bill_id
		$id = _Request::get('id');
		$model = new WarehouseBillModel($id,21);
		$data  = $model->getDataObject();
		//获取货品明细
		$goods_info = $model->getDetail($id);

		//获取单据信息 汇总
		$ZhuchengseInfo = $model->getBillinfo($id);
		//材质信息
		$zhuchengsezhongxiaoji = $zuixinlingshoujiaxiaoji = 0;
		$zhuchengsetongji[]=0;
		foreach($ZhuchengseInfo['zhuchengsedata'] as $key=>$val){
			$zhuchengsezhongxiaoji += $val['jinzhong'];
			$zhuchengsetongji[] = $val;
		}
		//主石信息
		$zhushilishuxiaoji = $zhushizhongxiaoji = 0;
		$zhushitongji[]=0;
		foreach ($ZhuchengseInfo['zhushidata'] as $key=>$val) {
			$zhushilishuxiaoji += $val['zhushilishu'];
			$zhushizhongxiaoji += $val['zuanshidaxiao'];
			$zhushitongji[] = $val;
		}
		//副石信息
		$fushilishuxiaoji = $fushizhongxiaoji = 0;
		$fushitongji[]=0;
		foreach ($ZhuchengseInfo['fushidata'] as $key=>$val) {
			$fushilishuxiaoji += $val['fushilishu'];
			$fushizhongxiaoji += $val['fushizhong'];
			$fushitongji[] = $val;
		}
		
		$consignee = ApiModel::sales_api('getConsigneeByOrderSn',array('order_sn'),array($data['order_sn']));
		
		if(empty($consignee['return_msg'])){
			$consignee['return_msg'] = '';
		}

		$this->render('zhuancang_print_ex.htm', array(
			'data' => $data,
			'consignee'=>$consignee['return_msg'],
			'goods_info' => $goods_info,
			'zhuchengsetongji' => $zhuchengsetongji,
			'zhuchengsezhongxiaoji' => $zhuchengsezhongxiaoji,
			'zhushilishuxiaoji' => $zhushilishuxiaoji,
			'zhushizhongxiaoji' => $zhushizhongxiaoji,
			'zhushitongji' => $zhushitongji,
			'fushilishuxiaoji' => $fushilishuxiaoji,
			'fushizhongxiaoji' => $fushizhongxiaoji,
			'fushitongji' => $fushitongji
		));

	}

    //打印汇总
    public function printSumTo() {

        //获取单据bill_id
        $id = _Request::get('id');
        $model = new WarehouseBillModel($id,21);
        $data  = $model->getDataObject();
        //获取货品明细
        $goods_info = $model->getDetail($id);

        //获取单据信息 汇总
        $ZhuchengseInfo = $model->getBillinfo($id);
        //材质信息
        $zhuchengsezhongxiaoji = $zuixinlingshoujiaxiaoji = 0;
        $zhuchengsetongji[]=0;
        foreach($ZhuchengseInfo['zhuchengsedata'] as $key=>$val){
            $zhuchengsezhongxiaoji += $val['jinzhong'];
            $zhuchengsetongji[] = $val;
        }
        //主石信息
        $zhushilishuxiaoji = $zhushizhongxiaoji = 0;
        $zhushitongji[]=0;
        foreach ($ZhuchengseInfo['zhushidata'] as $key=>$val) {
            $zhushilishuxiaoji += $val['zhushilishu'];
            $zhushizhongxiaoji += $val['zuanshidaxiao'];
            $zhushitongji[] = $val;
        }
        //副石信息
        $fushilishuxiaoji = $fushizhongxiaoji = 0;
        $fushitongji[]=0;
        foreach ($ZhuchengseInfo['fushidata'] as $key=>$val) {
            $fushilishuxiaoji += $val['fushilishu'];
            $fushizhongxiaoji += $val['fushizhong'];
            $fushitongji[] = $val;
        }
        
        $consignee = ApiModel::sales_api('getConsigneeByOrderSn',array('order_sn'),array($data['order_sn']));
        
        if(empty($consignee['return_msg'])){
            $consignee['return_msg'] = '';
        }

        $this->render('zhuancang_print_db_ex.htm', array(
            'data' => $data,
            'consignee'=>$consignee['return_msg'],
            'goods_info' => $goods_info,
            'zhuchengsetongji' => $zhuchengsetongji,
            'zhuchengsezhongxiaoji' => $zhuchengsezhongxiaoji,
            'zhushilishuxiaoji' => $zhushilishuxiaoji,
            'zhushizhongxiaoji' => $zhushizhongxiaoji,
            'zhushitongji' => $zhushitongji,
            'fushilishuxiaoji' => $fushilishuxiaoji,
            'fushizhongxiaoji' => $fushizhongxiaoji,
            'fushitongji' => $fushitongji
        ));

    }

	/**
	 * 调拨单保存时，需要判断入库仓库，仓库类型是【代取】，必须输入订单号，如果输入的货号绑定了订单，需要判断货号绑定的订单与单据输入的订单号一致，如果货号不绑定，不做判断。
	 * @param int $warehouse_id 转入仓ID
	 * @param array $goods 明细货品
	 * @param string $order_sn 订单号
	 */
	public function CheckDq($warehouse_id , $goods , $order_sn){
		$result = array('success' => 0 , 'error' => 'function CheckDq()异常');

		if(!$warehouse_id){
			$result['error'] = '请选择入库仓库';
			Util::jsonExit($result);
		}
		if(!$goods){
			$result['error'] = '请输入调拨明细';
			Util::jsonExit($result);
		}

		$model = new WarehouseModel($warehouse_id , 22);
		$M = new WarehouseBillInfoMModel(21);
		if( $model->getValue('type') == 3 )
		{
			if(empty($order_sn))
			{
				$result['error'] = '入库仓为待取库，必须输入订单号';
				Util::jsonExit($result);
			}
		}
		//检测货品明细是否绑定了订单
		$goods = array_column($goods , 0);
		$res = $M->CheckOrderByGoods($order_sn , $goods);
		if($res['success'] == 0){
			$result['error'] = $res['error'];
			Util::jsonExit($result);
		}
	}

	/********************************************************************************
	一、货品限制（待做）：1、货品状态（库存）、2、货品所在公司（与单据公司一致）、3、货品所在仓库（与单据仓库不能相同）
	二、添加：	  1、全部货品状态改为转仓中 2、检查货品状态是否正确（待做）（因为输入的时候验证不能确保保存时状态正确）
	三、已审核：1、货品状态改为库存 2、所在公司、仓库（改为单据对应）
	3、单据已审核状态   4、检查货品状态是否正确
	四、已取消：将全部货品改为库存
	五、修改 1、删除货品需要改变状态  2、其他按照添加验证
	 *********************************************************************************/
	/**
	 *	edit，核对货品
	 */
	public function hedui_goods ($params)
	{
		//var_dump($_REQUEST);exit;
		
		//var_dump($send_status);exit;
		$bill_no =_Request::get('bill_no');
		$bill_id =_Request::get('bill_id');
		$model = new WarehouseBillModel(21);
		//var_dump($bill_id);exit;
		$goods_info  = $model->getBillGoogsList($bill_id);
		$num =count($goods_info);
		//var_dump($goods_info);exit;
		$result['content'] = $this->fetch('hedui_goods_info.html',array(
				//'view'=>new ShipFreightView(new ShipFreightModel($id,43)),
				'bill_no' =>$bill_no,
				'bill_id' =>$bill_id,
				'goods_info' =>$goods_info,
				'num' =>$num,
				'temp' =>'',
			
		));
		
			$result['title'] = '核对货品';
	
		Util::jsonExit($result);
	}
	
	
	/**
	 *	edit，核对货品
	 */
	public function hedui_search ($params)
	{
		//var_dump($_REQUEST);exit;
	
		//var_dump($send_status);exit;
		$bill_no =_Request::get('bill_no');
		$bill_id =_Request::get('bill_id');
		$result = array('success' => 0,'error' =>'');
		$goods_id =_Request::get('goods_id');
		$model = new WarehouseBillModel(21);
		$is_ret = $model->check_goods_exis($goods_id,$bill_no);
		$newmodel = new WarehouseGoodsModel(21);
		$goods_detail =$newmodel->select2("cat_type,goods_sn,shoucun,zhushiyanse
				,zuanshidaxiao,zhushilishu
				,fushizhong,fushilishu,zhengshuhao", "goods_id={$goods_id}" , $is_all = 2);
		if(isset($goods_detail['goods_sn'])){
			$gallerymodel = new ApiStyleModel();
			//var_dump($goods_detail['goods_sn']);exit;
			$gallery_data = $gallerymodel->getProductGallery("{$goods_detail['goods_sn']}",1);
			if($gallery_data){
				$result['gallery_data'] = $gallery_data[0];
			}else{
				$result['gallery_data'] = '';
			}
		}
		
		//var_dump($gallery_data);exit;
		$temp=array();
		if($is_ret){
			//var_dump($is_ret);exit;
			$temp[] = $is_ret['goods_id'];
			//unset($is_ret[$goods_id]);
			$result['temp']=$temp;
			$result['goods_detail'] = $goods_detail;
			$result['is_ret'] = $is_ret;
			$result['success'] = 1;
		}
		
		//var_dump($goods_detail);exit;
		$result['title'] = '核对货品';
	
		Util::jsonExit($result);
	}
	

}/** END CLASS **/

?>
