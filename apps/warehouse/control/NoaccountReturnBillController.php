<?php
/**
 *  -------------------------------------------------
 *   @file		: NoaccountReturnBillController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-24 19:17:39
 *   @update	:
 *  -------------------------------------------------
 */
class NoaccountReturnBillController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('printBill','printSum');

    public $chanpinxian = array('其他饰品','黄金等投资产品','素金饰品','黄金饰品及工艺品','钻石饰品','彩钻饰品','珍珠饰品','彩宝饰品','成品钻','翡翠饰品','配件及特殊包装','非珠宝');

/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }		
		$company=$this->getCompanyList();
		//$model_p = new ApiProModel();
        $goodsAttrModel = new GoodsAttributeModel(17);
        //$caizhi_arr = $goodsAttrModel->getCaizhiList();
        $jinse_arr  = $goodsAttrModel->getJinseList();
        $caizhi_arr = $goodsAttrModel->getCaizhiList();
        $apiStyleModel = new ApiStyleModel();
        $catList = $apiStyleModel->getCatTypeInfo();
		$this->render('warehouse_bill_info_o.html',array(
				'view'=>new WarehouseBillView(new WarehouseBillModel(21)),
				'dd'=>new DictView(new DictModel(1)),
				'company'=>$company,
                'jinse_arr'=>$jinse_arr,
                'caizhi_arr'=>$caizhi_arr,
                'catList'=>$catList,
                'chanpinxian'=>$this->chanpinxian,
                //'companylist'=>$this->company()
		));
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
		);
		$page = _Request::getInt("page",1);
		$where = array();

		$model = new WarehouseBillInfoRModel(21);
		$data = $model->pageList($where,$page,10,false);
	//	var_dump($data);exit;

		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_bill_info_r_search_page';
		$this->render('warehouse_bill_info_o_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$company=$this->getCompanyList();

		$model = new NoaccountReturnBillModel(21);
		$view=new WarehouseBillView(new WarehouseBillModel($id,21));
		$order_sn=$view->get_order_sn();
		$ProductInfoModel=new ProductInfoModel(13);
		$consignee=$ProductInfoModel->getConsigneeOrder_sn($order_sn);
		
		//查询加工商 出库类型
		$this->render('warehouse_bill_info_o_edit.html',array(
			'view'=>new WarehouseBillView(new WarehouseBillModel($id,21)),
			//'c_info'=>$model->select($id),
			'dd'=>new DictView(new DictModel(1)),
			'consignee'=>$consignee	,
			'company'=>$company,
			));
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
			$id = intval($params["id"]);	//单据ID
		$model = new WarehouseBillModel($id,21);
		$view=new WarehouseBillView(new WarehouseBillModel($id,21));
		$order_sn=$view->get_order_sn();
		$ProductInfoModel=new ProductInfoModel(13);
		$consignee=$ProductInfoModel->getConsigneeOrder_sn($order_sn);
		$status = $model->getValue('bill_status');

		#获取单据附加表ID warehouse_bill_info_m
		//$model = new WarehouseBillInfoMModel(21);
		//$row = $model->getMinfo($id);
		
		//获取取单据取消时的操作用户和操作时间
		$WarehouseBillModel = new WarehouseBillModel(21);
		$billcloseArr=$WarehouseBillModel->get_bill_close_status($id);
		
		$this->render('warehouse_bill_info_o_show.html',array(
			'view' => new WarehouseBillView(new WarehouseBillModel($id, 21)),
			'dd' => new DictView(new DictModel(1)),
			'bar'=>Auth::getViewBar(),
			'status'=>$status,
			'consignee'=>$consignee	,
			'billcloseArr'=>$billcloseArr,
			//'row'=>$row,
		));
	}

/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		/*if(!isset($_POST['data'])){
			$result['error'] = "请输入货品明细，否则我没有权利放你通过^_^ ";
			Util::jsonExit($result);
		}*/
		//$data	= $_POST['data'];
		$model  = new NoaccountReturnBillModel(22);
		$time   = date('Y-m-d H:i:s');
		$bill_no = '';
		//$order_sn = trim($params['order_sn']);

		/*if($order_sn != ''){
			//检测是否有提交订单号，有还要检测它的合法性。
			$billModel = new WarehouseBillModel(21);
			$check = $billModel->CheckOrderSn($order_sn);
			if(!$check){
				$result['error'] = "订单号 <span style='color:red;'>{$order_sn}</span> 不存在";
				Util::jsonExit($result);
			}
			//获取订单的明细
			$bing_id = array();		//搜集订单的明细id
			$arr = $billModel->GetDetailByOrderSn($order_sn);
			foreach($arr as $detail){
				$bing_id[] = $detail['goods_id'];
			}
		}*/
        //var_dump($params);die;
		$from_company_name = explode(' | ', $params['from_company_name']);
        $from_warehouse_id = explode('|', $params['from_warehouse_id']);
        $place_company_id = explode('|', $params['place_company_id']);
        $place_warehouse_id = explode('|', $params['place_warehouse_id']);
		$c_info = array(
            'bill_status'=>1,
            'bill_type'=>'无账维修退货单',
			'from_company_id'=>$params['from_company_id'],
            'from_company_name'=>$from_company_name[1],
            'from_warehouse_id'=>$from_warehouse_id[0],
			'from_warehouse_name'=>trim($from_warehouse_id[1]),
            'create_user'=>$_SESSION['userName'],
            'create_time'=>$time,
            'remark'=>trim($params['remark']),
		);
        $data = array(
            'return_status'=>1,
            'business_type'=>$params['business_type'],
            'guest_name'=>$params['guest_name'],
            'guest_contact'=>$params['guest_contact'],
            'gold_weight'=>!empty($params['gold_weight'])?$params['gold_weight']:0,
            'finger_circle'=>$params['finger_circle'],
            'credential_num'=>$params['credential_num'],
            'main_stone_weight'=>!empty($params['main_stone_weight'])?$params['main_stone_weight']:0,
            'main_stone_num'=>!empty($params['main_stone_num'])?$params['main_stone_num']:0,
            'deputy_stone_weight'=>!empty($params['deputy_stone_weight'])?$params['deputy_stone_weight']:0,
            'deputy_stone_num'=>!empty($params['deputy_stone_num'])?$params['deputy_stone_num']:0,
            //'resale_price'=>!empty($params['resale_price'])?$params['resale_price']:0,
            'resale_price'=>0,
            'out_goods_id'=>isset($params['out_goods_id'])?$params['out_goods_id']:0,
            //'exist_account_gid'=>$params['exist_account_gid'],
            'exist_account_gid'=>0,
            'order_sn'=>$params['order_sn'],
            'style_sn'=>$params['style_sn'],
            'torr_type'=>!empty($params['torr_type'])?$params['torr_type']:0,
            'caizhi'=>$params['caizhi'],
            'ingredient_color'=>$params['ingredient_color'],
            'style_type'=>$params['style_type'],
            'product_line'=>$params['product_line'],
            //'place_company_id'=>isset($place_company_id[0])?$place_company_id[0]:0,
            'place_company_id'=>0,
            'place_company_name'=>$place_company_id[1],
            //'place_warehouse_id'=>isset($place_warehouse_id[0])?$place_warehouse_id[0]:0,
            'place_warehouse_id'=>0,
            'place_warehouse_name'=>trim($place_warehouse_id[1]),
            'apply_user'=>$_SESSION['userName'],
            'without_apply_time'=>$time,
            'return_remark'=>'',
            'weixiu_fee'=>$params["weixiu_fee"]/1,
            );
        /*if($c_info['to_company_id']==58){
			$checksession = $this->checkSession($c_info['to_warehouse_id']);
			if(is_string($checksession)){
				$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$c_info['to_warehouse_name']."</b>".$checksession."</span>的权限请联系管理员开通");
				Util::jsonExit($result);
			}
        }*/
		//验证。。。。。每个货品都有效

		//统计  货品总数量  货品成本总价  货品销售价总和
		/*$goodsModel = new WarehouseGoodsModel(21);
		$chengben = 0;
		$tuihuo   = 0;
		foreach ($data as $key => $value)
		{
			/** 剔除什么也没填的数据 **/
			/*if( ($value[0]=='')  ){
				unset($data[$key]);continue;
			}
			unset($data[$key]);
			$data[$key]['goods_id'] = $value[0];
			$data[$key]['goods_sn'] = $value[1];
			$data[$key]['jinzhong'] = $value[2];
			$data[$key]['zuanshidaxiao'] = $value[3];
			$data[$key]['zhengshuhao'] = $value[4];
			$data[$key]['sale_price'] = $value[6];
			$data[$key]['shijia'] = $value[7];
			$data[$key]['caizhi'] = $value[10];
			$data[$key]['jingdu'] = $value[17];
			$data[$key]['yanse'] = $value[18];
			$data[$key]['goods_name'] = $value[19];
			$chengben += $data[$key]['sale_price'];
			// $tuihuo   += $value[9];	//销售价
			//制维修退货单的时候 不判断是否是订单绑定的货。判断是否是订单号有效的销售单里面的货品，并且是已销售的，就可以。
			if($order_sn != ''){
				if(!in_array($value[0], $bing_id)){
					$result['error'] = "货号 <span style='color:red;'>{$value[0]}</span> 不是订单 <span style='color:blue;'>{$order_sn}</span> 明细的货品";
					Util::jsonExit($result);
				}
			}
			
		}

		$c_info['goods_total'] = $chengben;
		$c_info['shijia'] = 0;
		$c_info['goods_num']   = count($data);
		//var_dump($data);exit;*/

		//单据插入  商品插入
		$res= $model->add_info_o($data,$c_info);

		if($res !== false)
		{
			$result['success'] = 1;
			$result['id'] = $res;
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
		//整理数据
		$id = _Post::getInt('id');

		$billModel = new WarehouseBillModel($id, 21);
		$status = $billModel->getValue('bill_status');
		if($status == 2){
			$result['error'] = '当前单据已经审核，不能修改';
			Util::jsonExit($result);
		}
		if($status == 3){
			$result['error'] = '当前单据已经取消，不能修改';
			Util::jsonExit($result);
		}

		$data	= $_POST['data'];
		$model  = new NoaccountReturnBillModel(22);
		$time   = date('Y-m-d H:i:s');
		$bill_no= $_POST['bill_no'];
		$order_sn = trim($params['order_sn']);

		if($order_sn != ''){
			//检测是否有提交订单号，有还要检测它的合法性。
			$billModel = new WarehouseBillModel(21);
			$check = $billModel->CheckOrderSn($order_sn);
			if(!$check){
				$result['error'] = "订单号 <span style='color:red;'>{$order_sn}</span> 不存在";
				Util::jsonExit($result);
			}
			//获取订单的明细
			$bing_id = array();		//搜集订单的明细id
			$arr = $billModel->GetDetailByOrderSn($order_sn);
			foreach($arr as $detail){
				$bing_id[] = $detail['goods_id'];
			}
		}

		$params['to_company_name'] =explode('|', $params['to_company_name']);
		$to_company_name=trim($params['to_company_name'][1]);
		$c_info = array(
			'id'=>$id,
			'bill_no'=>$bill_no,
			'to_company_id'=>$params['to_company_id'],
			'to_company_name'=>$to_company_name,
			'to_warehouse_name'=>$billModel->getValue('to_warehouse_name'),
			'to_warehouse_id'=>$billModel->getValue('to_warehouse_id'),
			'goods_num'=>0,
			'goods_total'=>0,
			'shijia'=>0,

			'bill_note'=>$params['bill_note'],
			'create_user'=>$_SESSION['userName'],
			'create_time'=>$time,
			'bill_type'=>'O',
			'order_sn'=>$order_sn,
			
			);
        if($c_info['to_company_id']==58){
			$checksession = $this->checkSession($c_info['to_warehouse_id']);
			if(is_string($checksession)){
				$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$c_info['to_warehouse_name']."</b>".$checksession."</span>的权限请联系管理员开通");
				Util::jsonExit($result);
			}
        }

		$goodsModel = new WarehouseGoodsModel(21);
		//统计  货品总数量  货品成本总价  货品销售价总和
		$num = 0;
		$chengben = 0;
		$tuihuo   = 0;
		foreach ($data as $key => $value)
		{
			if( $value[0]==''){
				unset($data[$key]);continue;
			}
			unset($data[$key]);
			$data[$key]['goods_id'] = $value[0];
			$data[$key]['goods_sn'] = $value[1];
			$data[$key]['jinzhong'] = $value[2];
			$data[$key]['zuanshidaxiao'] = $value[3];
			$data[$key]['zhengshuhao'] = $value[4];
			$data[$key]['sale_price'] = $value[6];
			$data[$key]['shijia'] = $value[7];
			$data[$key]['caizhi'] = $value[10];
			$data[$key]['jingdu'] = $value[17];
			$data[$key]['yanse'] = $value[18];
			$data[$key]['goods_name'] = $value[19];
			$chengben += $data[$key]['sale_price'];
			$num += 1;
			// $tuihuo   += $value[9];	//退货价

			//制维修退货单的时候 不判断是否是订单绑定的货。判断是否是订单号有效的销售单里面的货品，并且是已销售的，就可以。
			if($order_sn != ''){
				if(!in_array($value[0], $bing_id)){
					$result['error'] = "货号 <span style='color:red;'>{$value[0]}</span> 不是订单 <span style='color:blue;'>{$order_sn}</span> 明细的货品";
					Util::jsonExit($result);
				}
			}
		}
		$c_info['goods_total'] = $chengben;
		$c_info['shijia'] = $tuihuo;
		$c_info['goods_num']   = $num;

		//单据插入  商品插入
		$res   = $model->get_data_o($id);//需要删除的数据信息
		$del_data = '';
		foreach ($res as $key=>$val)
		{
			if ($key)
			{
				$del_data .= ",'".$val['goods_id']."'";
			}
			else
			{
				$del_data .= "'".$val['goods_id']."'";
			}
		}
		//var_dump($data,$c_info,$del_data);exit;
		$res= $model->update_info_o($data,$c_info,$del_data);

		if($res !== false)
		{
		/* 	$modele = new WarehouseBillInfoEModel(22);
			$data = array();
			$modele->add_log($data); */
			$result['success'] = 1;
			$result['error'] = '修改成功！';
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
		$model = new NoaccountReturnBillModel($id,22);
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


	/** 根据货号，查询货品信息 (table插件查询使用) **/
	public function getGoodsInfoByGoodsID($params){

		$goods_id = $params['goods_id'];
		$from_company_id = $params['from_company_id'];
		$bill_id = isset($params['bill_id']) ? $params['bill_id'] : 0 ;
		$model = new NoaccountReturnBillModel(21);
		$model_goods = new  WarehouseGoodsModel(21);

		//判断是否是当前单据的明细，如果是当前单据的明细，说明用户啥也没有输入，掠过查询货品信息的过程
		$exists = false;
		if($bill_id){
			$exists = $model->checkDetail($goods_id, $bill_id);
		}
		//var_dump($params);exit;
		if(!$exists){
			if($goods_id){
				
				$goods = $model_goods->getGoodsInfoByGoodsID($goods_id);
				//var_dump($goods);
				if(!empty($goods)){
					if($goods['is_on_sale'] == 3 && ($goods['weixiu_status'] ==0 ||$goods['weixiu_status'] ==1 ||$goods['weixiu_status'] ==4)){
						$return_json = ["{$goods['goods_sn']}", "{$goods['jinzhong']}", "{$goods['zuanshidaxiao']}", "{$goods['zhengshuhao']}", "{$goods['zhushimairuchengben']}", "{$goods['mingyichengben']}", "0", "{$goods['shoucun']}", "{$goods['changdu']}", "{$goods['caizhi']}", "{$goods['zhushi']}","{$goods['zhushilishu']}","{$goods['fushi']}","{$goods['fushilishu']}","{$goods['fushizhong']}","{$goods['zongzhong']}","{$goods['jingdu']}","{$goods['yanse']}","{$goods['goods_name']}"];
					}else{
						$error = "货号为<span style='color:red;'>{$goods_id}</span>的货品不是销售状态或不是维修状态，不能制维修退货单";
						$return_json = ['success' =>0 , 'error'=>$error];
						echo json_encode($return_json);exit;
					}
				}else{
					$error = "仓库查不到货号为<span style='color:red;'>{$goods_id}</span>的货品";
					$return_json = ['success' =>0 , 'error'=>$error];
				}
				$json = json_encode($return_json);
				echo $json;
			}
		}
	}



	/**
	 * mkJson 生成Json表单
	 */
	public function mkJson()
	{
		$id = _Post::getInt('id');
		if(!$id) //添加页面调用
		{
			$arr = Util::iniToArray(APP_ROOT.'warehouse/data/from_table_o.tab');
			$arr['data'] = [
			// ["","","","","","","","","",""],
			];
		}
		//编辑页面调用
		else
		{
			$arr = Util::iniToArray(APP_ROOT.'warehouse/data/from_table_o_edit.tab');
			//获取其他出库单明细信息根据id
			$model = new NoaccountReturnBillModel(21);
			$res   = $model->get_data_o($id);
			$detail = array();
			foreach($res as $key => $val)
			{
				$detail[$key][]	= $val['goods_id'];
				$detail[$key][]	= $val['goods_sn'];
				$detail[$key][] = $val['jinzhong'];
				$detail[$key][] = $val['zuanshidaxiao'];
				$detail[$key][] = $val['zhengshuhao'];
				$detail[$key][] = $val['zhushimairuchengben'];
				$detail[$key][]	= $val['sale_price'];
				$detail[$key][] = $val['shijia'];
				$detail[$key][] = $val['shoucun'];
				$detail[$key][] = $val['changdu'];
				$detail[$key][] = $val['caizhi'];
				$detail[$key][] = $val['zhushi'];

				$detail[$key][] = $val['zhushilishu'];
				$detail[$key][] = $val['fushi'];
				$detail[$key][] = $val['fushilishu'];
				$detail[$key][] = $val['fushizhong'];
				$detail[$key][] = $val['zongzhong'];
				$detail[$key][] = $val['jingdu'];
				$detail[$key][] = $val['yanse'];
				$detail[$key][] = $val['goods_name'];

			}
			$arr['data'] = $detail;
		}


		$json = json_encode($arr);
		echo $json;
	}


	/** 取消单据 **/
	public function closeBillInfoO($params){
		//var_dump($_REQUEST);exit;
		$result = array('success' => 0,'error' =>'');
		$bill_id = $params['id'];
		$bill_no = $params['bill_no'];
		$model = new WarehouseBillModel($bill_id,21);
		/*boss-789
		$create_user = $model->getValue('create_user');
		$now_user = $_SESSION['userName'];
		if($create_user !== $now_user){
			$off = '<button class="btn btn-sm yellow" onclick="util.closeTab(this);" data-url="" name="离开" title="关闭当前页签" list-id="84" data-title="">离开 <i class="fa fa-mail-reply"></i></button>';
			die('亲~ 非本人单据，你是不能编辑的哦！#^_^#  '.$off);
		}
		*/
          
		$checksession = $this->checkSession($model->getValue('to_warehouse_id'));
		if(is_string($checksession)){
			$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$model->getValue('to_warehouse_name')."</b>".$checksession."</span>的权限,请授权后再来处理");
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

		$model = new NoaccountReturnBillModel(22);
		$goods_list = $model->getBillGoogsList($bill_id);	#获取明细列表
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
		$res = $model->closeBillInfoO($bill_id,$bill_no,$goods_ids);
		if($res){
			$result['success'] = 1;
			$result['error'] = '单据取消成功!!!';
		}else{
			$result['error'] = '单据取消失败!!!';
		}
		Util::jsonExit($result);
	}


	/**审核单据**/
	public function checkBillInfoO($params){
		$result = array('success' => 0,'error' =>'');
		$bill_id = $params['id'];
		$bill_no = $params['bill_no'];

		$model = new WarehouseBillModel($bill_id,21);

		$checksession = $this->checkSession($model->getValue('to_warehouse_id'));
		if(is_string($checksession)){
			$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$model->getValue('to_warehouse_name')."</b>".$checksession."</span>的权限请联系管理员开通");
			Util::jsonExit($result);
		}

		$create_user = $model->getValue('create_user');
		/*if($create_user == $_SESSION['userName']){
		      $result['error'] = '不能审核自己的单据';
		      Util::jsonExit($result);
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
		//如果是发货到展厅的订单的转仓，审核转仓单要触发发货状态为已到店。
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
		//取得单据信息
		$data  = $model->getDataObject();
		$model = new NoaccountReturnBillModel(22);
		$goods_list = $model->getBillGoogsList($bill_id);	#获取明细列表
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
		//var_dump($bill_id,$bill_no,$data,$goods_ids);exit;
		//data:单据信息 goods_ids：所有货品拼接
		$res = $model->checkBillInfoO($bill_id,$bill_no,$data,$goods_ids);
		if($res){
			$result['success'] = 1;
			$result['error'] = '审核成功';
		}else{
			$result['error'] = '审核失败';
		}
		Util::jsonExit($result);
	}



	/** 详情页明细获取 **/
	public function getGoodsInDetails($params){
		//var_dump($_REQUEST);exit;
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
		//var_dump($data);exit;
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_bill_goods_show_page';

		$this->render('warehouse_bill_goods.html',array(
				'pa' =>Util::page($pageData),
				'dd' => new DictView(new DictModel(1)),
				'data' => $data,
		));
	}

	/**
	 * 二级联动 根据公司，获取选中公司下的仓库
	 */
	public function getTowarehouseId()
	{
		$to_company_id = _Request::get('id');
		$model = new NoaccountReturnBillModel(21);
		$data = $model->getWarehouseByCompany($to_company_id);
		$this->render('option.html',array(
			'data'=>$data,
			));
	}
	//打印详情
	public function printBill() {

		$id = _Request::get('id');
		$model = new WarehouseBillModel($id,21);
		$data  = $model->getDataObject();
		$goods_info = $model->getDetail($id);
		$zuanshidaxiao=0;
		$fushizhong=0;
		$jinzhong=0;
		$ProductInfoModel=new ProductInfoModel(13);
		$consignee=$ProductInfoModel->getConsigneeOrder_sn($data['order_sn']);
		
		foreach($goods_info as $val){
			$zuanshidaxiao +=$val['zuanshidaxiao'];
			$fushizhong +=$val['fushizhong'];
			$jinzhong +=$val['jinzhong'];
		}
		$this->render('weixiushouhuo_print.htm', array(
				'data' => $data,
				'goods_info' => $goods_info,
				'jinzhong' => $jinzhong,
				'zuanshidaxiao' => $zuanshidaxiao,
				'fushizhong' => $fushizhong,
				'consignee' => $consignee,
		));

	}
	//打印汇总
	public function printSum() {
		//获取单据bill_id
		$id = _Request::get('id');
		$model = new WarehouseBillModel($id,21);
		$data  = $model->getDataObject();
		//获取货品详情
		$goods_info = $model->getDetail($data["id"]);

		//获取单据信息
		$ZhuchengseInfo = $model->getBillinfo($id);
		//材质信息
		$zhuchengsezhongxiaoji = $zuixinlingshoujiaxiaoji = 0;
		$zhuchengsetongji[]=0;
		foreach($ZhuchengseInfo['zhuchengsedata'] as $val){
			$zhuchengsezhongxiaoji += $val['jinzhong'];
			$zhuchengsetongji[] = $val;
		}
		//主石信息
		$zhushitongji[]=0;
		$zhushilishuxiaoji = $zhushizhongxiaoji = 0;
		foreach ($ZhuchengseInfo['zhushidata'] as $val) {
			$zhushilishuxiaoji += $val['zhushilishu'];
			$zhushizhongxiaoji += $val['zuanshidaxiao'];
			$zhushitongji[] = $val;
		}
		//副石信息
		$fushitongji[]=0;
		$fushilishuxiaoji = $fushizhongxiaoji = 0;
		foreach ($ZhuchengseInfo['fushidata'] as $val) {
			$fushilishuxiaoji += $val['fushilishu'];
			$fushizhongxiaoji += $val['fushizhong'];
			$fushitongji[] = $val;
		}
		//统计数据
		$zuanshidaxiao=0;
		$fushizhong=0;
		$jinzhong=0;
		foreach($goods_info as $val){
			$zuanshidaxiao +=$val['zuanshidaxiao'];
			$fushizhong +=$val['fushizhong'];
			$jinzhong +=$val['jinzhong'];
		}
		$ProductInfoModel=new ProductInfoModel(13);
		$consignee=$ProductInfoModel->getConsigneeOrder_sn($data['order_sn']);
		$this->render('weixiushouhuo_print_ex.htm', array(
			'data' => $data,
			'goods_info' => $goods_info,
			'jinzhong' => $jinzhong,
			'zuanshidaxiao' => $zuanshidaxiao,
			'fushizhong' => $fushizhong,
			'zhuchengsetongji' => $zhuchengsetongji,
			'zhuchengsezhongxiaoji' => $zhuchengsezhongxiaoji,
			'zhushilishuxiaoji' => $zhushilishuxiaoji,
			'zhushizhongxiaoji' => $zhushizhongxiaoji,
			'zhushitongji' => $zhushitongji,
			'fushilishuxiaoji' => $fushilishuxiaoji,
			'fushizhongxiaoji' => $fushizhongxiaoji,
			'fushitongji' => $fushitongji,
			'consignee' => $consignee,
		));

	}

}?>