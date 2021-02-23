<?php
/**
 *  -------------------------------------------------
 *   @file		: NoaccountMoveBillController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-05-04 10:17:22
 *   @update	:
 *  -------------------------------------------------
 */
class NoaccountMoveBillController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('printBill','printSum','print_q','hedui_goods');
    public $chanpinxian = array('其他饰品','黄金等投资产品','素金饰品','黄金饰品及工艺品','钻石饰品','彩钻饰品','珍珠饰品','彩宝饰品','成品钻','翡翠饰品','配件及特殊包装','非珠宝');
	/**
	 *	index
	 */
	public function index ($params)
	{
		//获取快递公司
		$kuaidiModel = new ExpressModel(1);
		$kuaidi_list = $kuaidiModel->getAllExpress();

		$warehouseModel = new WarehouseModel(21);
		//获取发货方(公司)
		$fahuofang = $this->getCompanyList();

        //已确认生产部使用库位，现申请“WF|维修调拨单”的入库仓包含以下仓库
        //BDD系统的《总公司维修库》，《线下生产镶石库》，《跟单维修库》
        //浩鹏系统的《总公司维修库》，《展厅镶嵌库》，《跟单维修库》
        if(SYS_SCOPE == 'zhanting'){
            $where = " (`name` like '%维修%' or `id` = '712') AND `is_delete` = 1 ";//展厅镶嵌库
        }else{
            $where = " (`name` like '%维修%' or `id` = '693') AND `is_delete` = 1 ";//线下生产镶石库
        }
		//获取维修库（收货方）
		$weixiu_list = $warehouseModel->select2($fields = "`id` , `name` , `code`" , $where , $type = 'all');
        $warehouseids= Util::eexplode(',', $_SESSION['userWareList']);
        $quanxianwarehouseid = $this->WarehouseListO();

        //因为涉及总部各部门间的维修调拨，所以需要在“WF|维修调拨单”菜单中的【发货方】及【收货方】栏位选项中增加以上仓库，并且按照仓库是否有权限进行管控。
        if ($quanxianwarehouseid !== true) {
            foreach ($weixiu_list as $key => $val) {
                if (!in_array($val['id'], $quanxianwarehouseid) && !in_array($val['name'],array('总公司维修库','跟单维修库'))) {
                    unset($weixiu_list[$key]);
                }
            }
        }

        $company=$this->getCompanyList();
        //$model_p = new ApiProModel();
        $goodsAttrModel = new GoodsAttributeModel(17);
        //$caizhi_arr = $goodsAttrModel->getCaizhiList();
        $jinse_arr  = $goodsAttrModel->getJinseList();
        $apiStyleModel = new ApiStyleModel();
        $catList = $apiStyleModel->getCatTypeInfo();

		$this->render('warehouse_bill_info_wf_add.html',array(
			'kuaidi_list' => $kuaidi_list,
			'weixiu_list' =>$weixiu_list,
			'fahuofang' => $fahuofang,
            'company'=>$company,
            'jinse_arr'=>$jinse_arr,
            'catList'=>$catList,
            'chanpinxian'=>$this->chanpinxian,
			'view' => new WarehouseBillView(new WarehouseBillModel(21)),
			'dd'=>new DictView(new DictModel(1)),
			'wfview' => new NoaccountMoveBillView(new NoaccountMoveBillModel(21))
		));
	}


	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$model = new NoaccountMoveBillModel(21);
		$bill_wf_id = $model->select2($fields = '`id`' , $where = " `bill_id` = {$id} " , $type ='one');

		//获取快递公司
		$kuaidiModel = new ExpressModel(1);
		$kuaidi_list = $kuaidiModel->getAllExpress();

		//获取发货方(公司)
		$fahuofang = $this->getCompanyList();
		//获取维修库
		$warehouseModel = new WarehouseModel(21);
		//$weixiu_list = $warehouseModel->select2($fields = "`id` , `name` , `code`" , $where =" `name` like '%维修%' AND `is_delete` = 1 AND `name` !='跟单维修库'", $type = 'all');

        //已确认生产部使用库位，现申请“WF|维修调拨单”的入库仓包含以下仓库
        //BDD系统的《总公司维修库》，《线下生产镶石库》，《跟单维修库》
        //浩鹏系统的《总公司维修库》，《展厅镶嵌库》，《跟单维修库》
        if(SYS_SCOPE == 'zhanting'){
            $where = " (`name` like '%维修%' or `id` = '712') AND `is_delete` = 1 ";//展厅镶嵌库
        }else{
            $where = " (`name` like '%维修%' or `id` = '693') AND `is_delete` = 1 ";//线下生产镶石库
        }
        //获取维修库（收货方）
        $weixiu_list = $warehouseModel->select2($fields = "`id` , `name` , `code`" , $where , $type = 'all');
        //$warehouseids= Util::eexplode(',', $_SESSION['userWareList']);
        //$quanxianwarehouseid = $this->WarehouseListO();

        //因为涉及总部各部门间的维修调拨，所以需要在“WF|维修调拨单”菜单中的【发货方】及【收货方】栏位选项中增加以上仓库，并且按照仓库是否有权限进行管控。
        /*if ($quanxianwarehouseid !== true) {
            foreach ($weixiu_list as $key => $val) {
                if (!in_array($val['id'], $quanxianwarehouseid)) {
                    unset($weixiu_list[$key]);
                }
            }
        }*/

		$wareModel = new WarehouseBillModel($id,21);
		$data = $wareModel->getDataObject();
		//获取订单顾客姓名
		$salesmodel =new SalesModel(27);
		//获取顾客名称
		$consignee = $salesmodel->getConsigneeByOrderSn($data['order_sn']);

		$this->render('warehouse_bill_info_wf_add.html',array(
			'consignee'=>$consignee,
			'kuaidi_list' => $kuaidi_list,
			'weixiu_list' =>$weixiu_list,
			'fahuofang' =>$fahuofang,
			'dd'=>new DictView(new DictModel(1)),
			'view' => new WarehouseBillView(new WarehouseBillModel($id , 21)),
			'wfview'=>new NoaccountMoveBillView(new NoaccountMoveBillModel($bill_wf_id,21))
		));
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$model = new NoaccountMoveBillModel(21);
		$bill_wf_id = $model->select2($fields = '`id`' , $where = " `bill_id` = {$id} " , $type ='one');

		//获取快递公司
		$kuaidiModel = new ExpressModel(1);
		$kuaidi_list = $kuaidiModel->getAllExpress();
		$kuaidi = array();
		foreach($kuaidi_list as $key => $val){
			$kuaidi[$val['id']] = $val['exp_name'];
		}

		$wareModel = new WarehouseBillModel($id,21);
		$data = $wareModel->getDataObject();
		//获取订单顾客姓名
		$salesmodel =new SalesModel(27);
		//获取顾客名称
		$consignee = $salesmodel->getConsigneeByOrderSn($data['order_sn']);

		//获取取单据取消时的操作用户和操作时间
		$WarehouseBillModel = new WarehouseBillModel(21);
		$billcloseArr=$WarehouseBillModel->get_bill_close_status($id);
		
		$this->render('warehouse_bill_info_wf_show.html',array(
			'consignee'=>$consignee,
			'view'=>new WarehouseBillView(new WarehouseBillModel($id,21)),
			'wfview'=>new NoaccountMoveBillView(new NoaccountMoveBillModel($bill_wf_id,21)),
			'bar'=>Auth::getViewBar(),
			'kuaidi' => $kuaidi,
			'billcloseArr'=>$billcloseArr,
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$to_warehouse_id = $params['to_warehouse_id'];
		$ex = explode('|',$to_warehouse_id);
		$to_warehouse_id = $ex[0];
		$to_warehouse_name = $ex[1];
		$px = explode('|',$params['from_company_id']);
		$from_company_id = $px[0];
		$from_company_name = $px[1];

		$order_sn = trim($params['order_sn']);
		$ship_number = trim($params['ship_number']);
		$bill_note = trim($params['bill_note']);
		$to_customer_id = $params['to_customer_id'] ? $params['to_customer_id'] : 0;

        $virtual_id = trim($params['virtual_id']);
		//检测是否有提交订单号，有还要检测它的合法性。
		/*if($order_sn != ''){
			$billModel = new WarehouseBillModel(21);
			$check = $billModel->CheckOrderSn($order_sn);
			if(!$check){
				$result['error'] = "订单号 <span style='color:red;'>{$order_sn}</span> 不存在";
				Util::jsonExit($result);
			}
			//获取订单的明细
			//$bing_id = array();		//搜集订单的明细id
			//$arr = $billModel->GetDetailByOrderSn($order_sn);
			//foreach($arr as $detail){
				//$bing_id[] = $detail['goods_id'];
			//}
		}*/

        if(empty($virtual_id)){
            $result['error'] = "请输入需要调拨的无帐修退流水号，批量请换行隔开";
            Util::jsonExit($result);
        }
        $model = new NoaccountMoveBillModel(22);
        $virtual_id = str_replace(' ',',',$virtual_id);
        $virtual_id = str_replace('，',',',$virtual_id);
        $virtual_id = str_replace(array("\r\n", "\r", "\n"),',',$virtual_id);
        $virtual_id_arr = explode(",", $virtual_id);
        //检查流水号是否存在
        $goodslist = array();
        foreach ($virtual_id_arr as $key => $virtual_id) {
            $rest = $model->checkVirtualid($virtual_id);
            if(empty($rest)){
                $result['error'] = "无帐货号".$virtual_id."不存在！请填写正确的货号";
                Util::jsonExit($result);
            }
            if($rest['return_status'] == 3){
                $result['error'] = "无帐货号".$virtual_id."已发货！请填写正确的货号";
                Util::jsonExit($result);
            }
            $chekBill = $model->checkVirtualReturnBill($virtual_id);
            if(!empty($chekBill)){
                $result['error'] = "无帐货号".$virtual_id."有未审核的无账修退单，不允许调拨";
                Util::jsonExit($result);
            }
            if($rest['place_company_id'] != $from_company_id){
                $result['error'] = "无帐货号".$virtual_id."所在公司与出库公司不匹配，不能制调拨单！";
                Util::jsonExit($result);
            }
            
            $goodslist[] = $rest;
        }
        //根据收货方的仓库，获取该仓库的所属公司
        $wrmodel = new WarehouseRelModel(21);
        $to_company_arr = $wrmodel->select2('`company_id`,`company_name`', $where = "`warehouse_id` = '{$to_warehouse_id}'" , $type = 'getRow');
        $to_company_id = $to_company_arr['company_id'];
        $to_company_name = $to_company_arr['company_name'];
		$bill_info = array(
			'order_sn' => $order_sn,
			'goods_num' => 0,
			'out_company_id' => $from_company_id,
			'out_company_name' => $from_company_name,
			'from_company_id' => $to_company_id,
			'from_company_name' => $to_company_name,
            'from_warehouse_id' => $to_warehouse_id,
            'from_warehouse_name' => $to_warehouse_name,
			'bill_note' => $bill_note,
			'goods_total' => 0,
			'to_customer_id' => $to_customer_id,
			'ship_number' => $ship_number
		);
        //var_dump($bill_info);die;

		
		/***
			1）子分公司只能配送到总公司，不允许子分公司之间流转维修品
			2）总公司可以配送到任意子分公司
			3）总公司允许转到总公司
		*/
		/*if($bill_info['from_company_id'] != 58){
			if($to_company_arr['company_id'] != 58){
				$result['error'] ="子分公司只能配送到总公司，不允许子分公司之间流转维修品";
				Util::jsonExit($result);
			}
		}

		//检测快递单号
		$express_v =  new ExpressView(new ExpressModel($to_customer_id,1));
		$rule = $express_v->get_freight_rule();
		if($rule && !preg_match($rule,$ship_number)){
			$result['error'] ="快递单号与快递公司不符！";
			Util::jsonExit($result);
		};*/

		/*$all_monery = 0;
		if(isset($_POST['data']) && !empty($_POST['data'])){
			$goodsDate = $_POST['data'];
		}else{
			$result['error'] ="请输入货品明细";
			Util::jsonExit($result);
		}
		//$goodsModel = new WarehouseGoodsModel(21);
		foreach($goodsDate AS $key =>$goods){
			if($goods[0] == ''){unset($goodsDate[$key]);continue;}

			//检测货品是否是销售状态，并且制了维修退货单（审核）
			$row = $goodsModel->select2($fields = "`is_on_sale` , `weixiu_status`", $where ="`goods_id` = '{$goods[0]}'" , $is_all = 2);
			if($row['is_on_sale'] != 3){
				$result['error'] ="货号为 <span style='color:red;'>{$goods[0]}</span> 的货品不是已销售状态";
				Util::jsonExit($result);
			}
			if($row['weixiu_status'] != 3){
				$result['error'] ="货号为 <span style='color:red;'>{$goods[0]}</span> 的货品没有制 维修退货单 或 制了 维修退货单 没有审核";
				Util::jsonExit($result);
			}

			//货品所在地为本公司，不允许配送其他公司的货品
			$weixiu_place = $goodsModel->select2($fields = '`weixiu_company_id` , `weixiu_company_name` , `weixiu_warehouse_id` , `weixiu_warehouse_name`', $where ="`goods_id` = '{$goods[0]}'" , $is_all = 2);
			if(empty($weixiu_place)){
				$result['error'] = "货品{$goods[0]}没有查询到维修入库公司，制维修退货单时没有将数据记录";
				Util::jsonExit($result);
			}
			if($weixiu_place['weixiu_company_id'] != $bill_info['from_company_id']){
				$result['error'] = "货品 {$goods[0]} 所在维修入库公司：<span style='color:red;'>{$weixiu_place['weixiu_company_name']}</span><br/>当前发货方：<span style='color:red;'>{$bill_info['from_company_name']}</span>, 不允许配送其他公司的货品";
				Util::jsonExit($result);
			}

			$all_monery += $goods[4];

			//检测提交过来的明细，是否是订单绑定的明细
			if($order_sn != ''){
				if(!in_array($goods[0], $bing_id)){
					$result['error'] = "货号 <span style='color:red;'>{$goods[0]}</span> 不是订单 <span style='color:blue;'>{$order_sn}</span> 明细的货品";
					Util::jsonExit($result);
				}
			}
			if($bill_info['from_company_id'] ==58 && !empty($weixiu_place['weixiu_warehouse_id'])){				
					$checksession = $this->checkSession($weixiu_place['weixiu_warehouse_id']);
					if(is_string($checksession)){
						$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$weixiu_place['weixiu_warehouse_name']."</b>".$checksession."</span>的权限请联系管理员开通");
						Util::jsonExit($result);
					}				
			}			
		}
		$bill_info['goods_num'] = count($goodsDate);
		$bill_info['goods_total'] = $all_monery;*/


		//$model = new WarehouseBillInfoMModel(22);
		//if($_SESSION['userName'] != '徐雪飞' /*&& $_SESSION['userName'] != '张瑞真'*///){		//由于单件货品超过40W 所以临时放开
    		//转仓金额金额限制
    		/*$nrom = $model->transferHouseRule($bill_info['from_company_id'],$bill_info['to_warehouse_id']);
    		if($nrom != 0){
    			if ($all_monery > $nrom)
    			{
    				$result['error'] = '转仓金额不得超过'.($nrom/10000).'万';
    				Util::jsonExit($result);
    			}
    		}
		}*/
        //$from_company_name = explode(' | ', $params['from_company_name']);
        //$from_warehouse_id = explode('|', $params['from_warehouse_id']);
        //$out_company_name = explode(' | ', $params['out_company_name']);
        //$out_warehouse_id = explode('|', $params['out_warehouse_id']);
        //$place_company_id = explode('|', $params['place_company_id']);
        //$place_warehouse_id = explode('|', $params['place_warehouse_id']);
        $c_info = array(
            'bill_status'=>1,
            'bill_type'=>'无账调拨单',
            'out_company_id'=>!empty($bill_info['out_company_id'])?$bill_info['out_company_id']:0,
            'out_company_name'=>$bill_info['out_company_name'],
            //'out_warehouse_id'=>$out_warehouse_id[0],
            'out_warehouse_id'=>0,
            //'out_warehouse_name'=>trim($out_warehouse_id[1]),
            'out_warehouse_name'=>'',
            'from_company_id'=>!empty($bill_info['from_company_id'])?$bill_info['from_company_id']:0,
            'from_company_name'=>$bill_info['from_company_name'],
            //'from_warehouse_id'=>$from_warehouse_id[0],
            'from_warehouse_id'=>!empty($bill_info['from_warehouse_id'])?$bill_info['from_warehouse_id']:0,
            //'from_warehouse_name'=>trim($from_warehouse_id[1]),
            'from_warehouse_name'=>$bill_info['from_warehouse_name'],
            'create_user'=>$_SESSION['userName'],
            'create_time'=>date('Y-m-d H:i:s'),
            'remark'=>$bill_info['bill_note'],
            'to_customer_id'=>$bill_info['to_customer_id'],
            'express_sn'=>$bill_info['ship_number'],
        );
        /*$data = array(
            'return_status'=>1,
            'business_type'=>$params['business_type'],
            'guest_name'=>$params['guest_name'],
            'guest_contact'=>$params['guest_contact'],
            'gold_weight'=>$params['gold_weight'],
            'finger_circle'=>$params['finger_circle'],
            'credential_num'=>$params['credential_num'],
            'main_stone_weight'=>$params['main_stone_weight'],
            'main_stone_num'=>$params['main_stone_num'],
            'deputy_stone_weight'=>$params['deputy_stone_weight'],
            'deputy_stone_num'=>$params['deputy_stone_num'],
            'resale_price'=>$params['resale_price'],
            'out_goods_id'=>$params['out_goods_id'],
            'exist_account_gid'=>$params['exist_account_gid'],
            'style_sn'=>$params['style_sn'],
            'order_sn'=>$params['order_sn'],
            'torr_type'=>$params['torr_type'],
            'ingredient_color'=>$params['ingredient_color'],
            'style_type'=>$params['style_type'],
            'product_line'=>$params['product_line'],
            'place_company_id'=>$place_company_id[0],
            'place_company_name'=>$place_company_id[1],
            'place_warehouse_id'=>$place_warehouse_id[0],
            'place_warehouse_name'=>trim($place_warehouse_id[1]),
            'apply_user'=>$_SESSION['userName'],
            'without_apply_time'=>date('Y-m-d H:i:s'),
            );*/
		
		$res = $model->add_shiwu($goodslist , $c_info);
		if($res['success'] == 1)
		{
			$result['success'] = 1;
			$result['x_id'] = $res['x_id'];
			$result['label'] = $res['label'];
			$result['tab_id'] = mt_rand();
			$result['error'] = '添加无账调拨单成功！';
		}
		else
		{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}

	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = $params['id'];
		$bill_info = array(
			'order_sn' => trim($params['order_sn']),
			'ship_number' => trim($params['ship_number']),
			'bill_note' => trim($params['bill_note']),
			'to_customer_id' => !empty($params['to_customer_id']) ? $params['to_customer_id'] : 0,
			'id' => $id
		);

		//检测是否有提交订单号，有还要检测它的合法性。
		if($bill_info['order_sn'] != ''){
			$billModel = new WarehouseBillModel(21);
			$check = $billModel->CheckOrderSn($bill_info['order_sn']);
			if(!$check){
				$result['error'] = "订单号 <span style='color:red;'>{$bill_info['order_sn']}</span> 不存在";
				Util::jsonExit($result);
			}
			//获取订单的明细
			$bing_id = array();		//搜集订单的明细id
			$arr = $billModel->GetDetailByOrderSn($bill_info['order_sn']);
			foreach($arr as $detail){
				$bing_id[] = $detail['goods_id'];
			}
		}

		$billModel = new WarehouseBillModel($id, 21);
		$bill_info['bill_no'] = $billModel->getValue('bill_no');
		$bill_info['from_company_id'] = $billModel->getValue('from_company_id');
		$bill_info['from_company_name'] = $billModel->getValue('from_company_name');

		$create_user = $billModel->getValue('create_user');
		$now_user = $_SESSION['userName'];
		if($create_user !== $now_user){
			$result['error'] = $now_user.",".$create_user.'亲~ 非本人单据，你是不能编辑的哦！#^_^#  ';
			Util::jsonExit($result);
		}

		$status = $billModel->getValue('bill_status');
		if($status == 2){
			$result['error'] = '单据已审核，不能修改';
			Util::jsonExit($result);
		}else if($status == 3){
			$result['error'] = '单据已取消，不能修改';
			Util::jsonExit($result);
		}

		$all_monery = 0;

		if(isset($_POST['data']) && !empty($_POST['data'])){
			$goodsDate = $_POST['data'];
		}else{
			$result['error'] ="请输入货品明细";
			Util::jsonExit($result);
		}

		$goodsModel = new WarehouseGoodsModel(21);
		$model = new NoaccountMoveBillModel(22);
		foreach($goodsDate AS $key => $goods){
			if($goods[0] == ''){unset($goodsDate[$key]);continue;}


			$weixiu_place = $goodsModel->select2($fields = '`weixiu_company_id` , `weixiu_company_name` , `weixiu_warehouse_id` , `weixiu_warehouse_name`', $where ="`goods_id` = '{$goods[0]}'" , $is_all = 2);
			if(empty($weixiu_place)){
				$result['error'] = "货品{$goods[0]}没有查询到维修入库公司，制维修退货单时没有将数据记录";
				Util::jsonExit($result);
			}
			if($weixiu_place['weixiu_company_id'] != $bill_info['from_company_id']){
				$result['error'] = "货品 {$goods[0]} 所在维修入库公司：<span style='color:red;'>{$weixiu_place['weixiu_company_name']}</span><br/>当前发货方：<span style='color:red;'>{$bill_info['from_company_name']}</span>, 不允许配送其他公司的货品";
				Util::jsonExit($result);
			}

			//检测货品是否是销售状态，并且制了维修退货单（审核）
			$row = $goodsModel->select2($fields = "`is_on_sale` , `weixiu_status`", $where ="`goods_id` = '{$goods[0]}'" , $is_all = 2);
			//判断提交的货品是否为单据原来的明细
			if($model->checkDetail($goods[0] , $id)){
				if($row['is_on_sale'] != 3){
					$result['error'] ="货号为 <span style='color:red;'>{$goods[0]}</span> 的货品不是已销售状态";
					Util::jsonExit($result);
				}
			}else{
				if(!empty($row)){
					if($row['weixiu_status'] == 5 || $row['weixiu_status'] == 6){
						$result['error'] ="货号为 <span style='color:red;'>{$goods[0]}</span> 的货品已经在其他维修退货单中";
						Util::jsonExit($result);
					}
					if($row['weixiu_status'] != 3){
						$result['error'] ="货号为 <span style='color:red;'>{$goods[0]}</span> 的货品没有制 维修退货单 或 制了 维修退货单 没有审核";
						Util::jsonExit($result);
					}
				}
			}
			$all_monery += $goods[4];

			//检测提交过来的明细，是否是订单绑定的明细
			if($bill_info['order_sn'] != ''){
				if(!in_array($goods[0], $bing_id)){
					$result['error'] = "货号 <span style='color:red;'>{$goods[0]}</span> 不是订单 <span style='color:blue;'>{$bill_info['order_sn']}</span> 明细的货品";
					Util::jsonExit($result);
				}
			}
			if($bill_info['from_company_id'] ==58 && !empty($weixiu_place['weixiu_warehouse_id'])){				
					$checksession = $this->checkSession($weixiu_place['weixiu_warehouse_id']);
					if(is_string($checksession)){
						$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$weixiu_place['weixiu_warehouse_name']."</b>".$checksession."</span>的权限请联系管理员开通");
						Util::jsonExit($result);
					}				
			}
			
		}

		$bill_info['goods_num'] = count($goodsDate);
		$bill_info['goods_total'] = $all_monery;


		$res = $model->update_shiwu($bill_info , $goodsDate);
		if($res['success'] == 1){
			$result['success'] = 1;
			$result['error'] = '修改维修调拨单成功！';
		}else{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}

	/**
	 * mkJson 生成Json表单
	 */
/* 	public function mkJson(){
		$id = _Post::getInt('id');
		$arr = Util::iniToArray(APP_ROOT.'warehouse/data/from_table_bill_wf.tab');
		if(!$id){
			$arr['data_bill_wf'] = [];
		}else{
			$arr['data_bill_wf'] = array();
			$model = new NoaccountMoveBillModel(21);
			$data = $model->getBillGoogsListBybill($id);
			foreach($data as $k => $goods){
				$arr['data_bill_wf'][] = ["{$goods['goods_id']}","{$goods['goods_sn']}", "{$goods['jinzhong']}" , "{$goods['zhushilishu']}", "{$goods['lingshoujia']}" , "{$goods['zuixinlingshoujia']}", "{$goods['fushilishu']}", "{$goods['fushizhong']}", "{$goods['yanse']}", "{$goods['jingdu']}", "{$goods['zhengshuhao']}","{$goods['goods_name']}"];
			}
		}
		$json = json_encode($arr);
		echo $json;
	} */


	/**
	 * mkJson 生成Json表单
	 */
	public function mkJson(){
		$id = _Post::getInt('id');
		//$arr = Util::iniToArray(APP_ROOT.'warehouse/data/from_table_bill_m.tab');
		$view = new NoaccountMoveBillView(new NoaccountMoveBillModel(21));
		$arr = $view->js_table;
		//var_dump($arr);exit;
		if(!$id){
			$arr['data_bill_wf'] = [
			];
		}else{
			$arr['data_bill_wf'] = $view->getTableGoods($id);
		}
		$json = json_encode($arr);
		echo $json;
	}

	//插件获取货品信息
	public function getGoodsInfoByGoodsID($params){
		$goods_id = $params['goods_id'];
		$model = new NoaccountMoveBillModel(21);
		$goods = $model->getBillGoogsList($goods_id);
		if(!empty($goods)){
			if($goods['is_on_sale'] != 3){
				$error = "货号为<span style='color:red;'>{$goods_id}</span>的货品不是已销售货品";
				$return_json = ['success' =>0 , 'error'=>$error];
				echo json_encode($return_json);exit;
			}
			if($goods['weixiu_status'] != 3){
				$error = "货号为<span style='color:red;'>{$goods_id}</span>的货品没有制 维修退货单 或 制了维修退货单没有审核";
				$return_json = ['success' =>0 , 'error'=>$error];
				echo json_encode($return_json);exit;
			}
			$return_json = ["{$goods['goods_sn']}", "{$goods['jinzhong']}" , "{$goods['zhushilishu']}", "{$goods['lingshoujia']}" , "{$goods['zuixinlingshoujia']}", "{$goods['fushilishu']}", "{$goods['fushizhong']}", "{$goods['yanse']}", "{$goods['jingdu']}", "{$goods['zhengshuhao']}","{$goods['goods_name']}"];
			echo json_encode($return_json);exit;
		}else{
			$error = "仓库查不到货号为<span style='color:red;'>{$goods_id}</span>的货品";
			$return_json = ['success' =>0 , 'error'=>$error];
			echo json_encode($return_json);exit;
		}
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
		$data = $g_model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_bill_weixiu_goods_list';

		$this->render('warehouse_bill_goods.html',array(
			'pa' =>Util::page($pageData),
			'dd' => new DictView(new DictModel(1)),
			'data' => $data,
		));
	}

	//审核单据
	public function ckeckBill($params){
		$result = array('success' => 0,'error' =>'审核维修调拨单程序异常');
		$id = $params['bill_id'];

		$billModel = new WarehouseBillModel($id, 21);
		$warehouseModel=new WarehouseModel(21);
		$warehouse_name=$warehouseModel->getWarehosueNameForId($billModel->getValue('to_warehouse_id'));

		$status = $billModel->getValue('bill_status');		
		
		$checksession = $this->checkSession($billModel->getValue('to_warehouse_id'));
		if(is_string($checksession)){
			$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$warehouse_name."</b>".$checksession."</span>的权限,请授权后再来处理");
			Util::jsonExit($result);
		}
		if($status == 2){
			$result['error'] = '单据已审核，不能重复操作';
			Util::jsonExit($result);
		}else if($status == 3){
			$result['error'] = '单据已取消，不能审核';
			Util::jsonExit($result);
		}

		$model = new NoaccountMoveBillModel(22);
		$res = $model->ckeckBill($id);
		if($res['success'] == 1){
			$result['success'] = 1;
			$result['error'] = '单据审核成功';
		}else{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}


	//取消单据
	public function closeBill($params){
		$result = array('success' => 0,'error' =>'取消维修调拨单程序异常');
		$id = $params['bill_id'];
		$billModel = new WarehouseBillModel($id, 21);

		$create_user = $billModel->getValue('create_user');
		$now_user = $_SESSION['userName'];
		if($create_user !== $now_user){
			$result['error'] = '亲~ 非本人单据，你是不能取消的哦！#^_^#  ';
			Util::jsonExit($result);
		}

		$status = $billModel->getValue('bill_status');
		if($status == 2){
			$result['error'] = '单据已审核，不能取消';
			Util::jsonExit($result);
		}else if($status == 3){
			$result['error'] = '单据已取消，不能重复操作';
			Util::jsonExit($result);
		}

		$model = new NoaccountMoveBillModel(22);
		$res = $model->closeBill($id);
		if($res['success'] == 1){
			$result['success'] = 1;
			$result['error'] = '单据取消成功';
		}else{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}

	//打印单据明细
	public function printBill($params){

		//获取单据bill_id
		$id = _Request::get('id');
		$model = new WarehouseBillModel($id,21);
		$data  = $model->getDataObject();

		//获取快递公司
		$kuaidiModel = new ExpressModel(1);
		$kuaidi_list = $kuaidiModel->getAllExpress();
		$kuaidi = array();
		foreach($kuaidi_list as $key => $val){
			$kuaidi[$val['id']] = $val['exp_name'];
		}

		$wfModel = new NoaccountMoveBillModel(21);
		$ship_number = $wfModel->select2($fields = '`ship_number`' , $where = "`bill_id` = {$id}" , $type ='one');
		//获取货品明细
		$goods_info = $model->getDetail($id);

		//获取订单顾客姓名
		$salesmodel =new SalesModel(27);
		//获取顾客名称
		$data['consignee'] = $salesmodel->getConsigneeByOrderSn($data['order_sn']);

		$this->render('wf_print_detail.html', array(
			'data' => $data,
			'goods_info' => $goods_info,
			'ship_number' => $ship_number,
			'kuaidi' => $kuaidi,
			'dd'=>new DictView(new DictModel(1)),
		));
	}

	//打印汇总
	public function printSum() {

		//获取单据bill_id
		$id = _Request::get('id');
		$model = new WarehouseBillModel($id,21);
		$wfmodel = new NoaccountMoveBillModel(21);
		//获取快递单号
		$ship_number = $wfmodel->select2($fields = '`ship_number`' , $where = "`bill_id` = {$id}" , $type ='one');

		//获取快递公司
		$kuaidiModel = new ExpressModel(1);
		$kuaidi_list = $kuaidiModel->getAllExpress();
		$psCompanyId = $model->GetBillInfoByid('`to_customer_id`' , "`id` = {$id}" , $type = 'getOne');
		$exp_names = array_column($kuaidi_list, 'exp_name');
		$exp_ids = array_column($kuaidi_list , 'id');
		$exp_id = array_search($psCompanyId, $exp_ids);
		$exp_name = $exp_names[$exp_id];

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
		$salesmodel =new SalesModel(27);
		//获取顾客名称
		$data['consignee'] = $salesmodel->getConsigneeByOrderSn($data['order_sn']);
		$this->render('wf_pring_sum.html', array(
				'data' => $data,
				'goods_info' => $goods_info,
				'zhuchengsetongji' => $zhuchengsetongji,
				'zhuchengsezhongxiaoji' => $zhuchengsezhongxiaoji,
				'zhushilishuxiaoji' => $zhushilishuxiaoji,
				'zhushizhongxiaoji' => $zhushizhongxiaoji,
				'zhushitongji' => $zhushitongji,
				'fushilishuxiaoji' => $fushilishuxiaoji,
				'fushizhongxiaoji' => $fushizhongxiaoji,
				'fushitongji' => $fushitongji,
				'ship_number' => $ship_number,
				'exp_name' =>$exp_name
		));
	}
	//批量查询商品信息 维修调拨单
	public function getGoodsInfos(){
		$g_ids = _Get::getList('g_ids');
		$g_ids = array_filter($g_ids);
		$bill_id = _Get::getInt('bill_id');
        $model = new NoaccountMoveBillModel(21);
        $res = $model->select3(" * "," id in('".implode("','", $g_ids)."') ","all");
		//$model = new WarehouseGoodsModel(21);
		//$view = new NoaccountMoveBillView($model);
		//$res = $model->table_GetGoodsInfo($g_ids,$view->js_table['lable'],3,$bill_id,3);
		//var_dump($res);exit;   
        $data = ['success'=>$res,'error'=>''];
		echo json_encode($data);exit;
	}

	/*
	* 增加核对货品功能
	*
	*/
	public function hedui_goods ($params)
	{
		// var_dump($params);exit;
		
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
	






}?>