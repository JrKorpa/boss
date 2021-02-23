<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillEController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-15 14:43:03
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoEController extends CommonController
{
	protected $smartyDebugEnabled = true;
	protected $whitelist = array('printBill','printSum');
	/**
	 *	新增页面
	 */
	public function index ($params)
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }		
		//库房列表
		$company=$this->getCompanyList();
		$this->render('warehouse_bill_e_add.html',array(
			'view'=>new WarehouseBillView(new WarehouseBillModel(21)),
			'dd'=>new DictView(new DictModel(1)),
			'company'=>$company
			));
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		//库房列表
		$id = intval($params["id"]);

		$model = new WarehouseBillModel($id, 21);
		$create_user = $model->getValue('create_user');
		$now_user = $_SESSION['userName'];
		if($create_user !== $now_user){
			$off = '<button class="btn btn-sm yellow" onclick="util.closeTab(this);" data-url="" name="离开" title="关闭当前页签" list-id="84" data-title="">离开 <i class="fa fa-mail-reply"></i></button>';
			die('亲~ 非本人单据，你是不能编辑的哦！#^_^#  '.$off);
		}

		$company=$this->getCompanyList();

		$this->render('warehouse_bill_e_edit.html',array(
			'view'=>new WarehouseBillView(new WarehouseBillModel($id,21)),
			'company'=>$company
			));
	}

	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');

		$billModel = new WarehouseBillModel($id, 21);

		$create_user = $billModel->getValue('create_user');
		$now_user = $_SESSION['userName'];
		if($create_user !== $now_user){
			$off = '<button class="btn btn-sm yellow" onclick="util.closeTab(this);" data-url="" name="离开" title="关闭当前页签" list-id="84" data-title="">离开 <i class="fa fa-mail-reply"></i></button>';
			die('亲~ 非本人单据，你是不能编辑的哦！#^_^#  '.$off);
		}

		$status = $billModel->getValue('bill_status');
		if($status == 2){
			$result['error'] = '当前单据已经是审核状态，不能做修改操作';
			Util::jsonExit($result);
		}
		if($status == 3){
			$result['error'] = '当前单据已经取消，不能修改';
			Util::jsonExit($result);
		}
		/**字典***/
		$dd= new DictView(new DictModel(1));
		$ruku_type_arr = $dd->getEnumArray('warehouse.put_in_type');
		$ruku_type_arr_new = array();
		foreach ($ruku_type_arr as $key=>$val)
		{
			$ruku_type_arr_new[$val['label']] = $val['name'];
		}
		$jiejia = $dd->getEnumArray('confirm');
		$jiejia_arr = array();
		foreach ($jiejia as $key=>$val)
		{
			$jiejia_arr[$val['label']] = $val['name'];
		}
		//var_dump($ruku_type_arr_new);exit;
		/***字典*/
		//1、删除商品信息 2、添加货品明细
		$model  = new WarehouseBillGoodsModel(22);
		$data_info = $model->get_bill_data($id); //损益单明细组织数据
		$old_goods_id = "";
		foreach ($data_info as $key=>$val)
		{
			if ($key)
			{
				$old_goods_id  .= ",'".$val['goods_id']."'";
			}
			else
			{
				$old_goods_id  .= "'".$val['goods_id']."'";
			}
		}
		$data	= $_POST['data'];//明细
		$bill_no=$_POST['bill_no'];
		$time   = date('Y-m-d H:i:s');
		$e_info = array(
			'id'=>$id,
			'bill_no'=>$bill_no,
			'from_company_id'=>$_POST['from_company_id'],
			'from_company_name'=>$_POST['from_company_name'],
			'goods_num'=>0,
			'chengbenjia'=>0,
			'xiaoshoujia'=>0,
			'bill_note'=>$_POST['bill_note'],
			'create_time'=>$time
			);
		$chengben = 0;
		$tuihuo   = 0;
		$goods_str = '';
		foreach ($data as $key => $value)
		{
			/** 过滤啥也没填的，空的明细信息 **/
			if( ($value[0] == '') && ($value[1] == '') && ($value[2] == '')  && ($value[3] == '')  && ($value[4] == '')  && ($value[5] == '')  && ($value[6] == '')  && ($value[7] == '')  && ($value[8] == '')  && ($value[9] == '')  && ($value[9] == '')  && ($value[9] == '')  && ($value[9] == '')  && ($value[10] == '')  && ($value[11] == '')  && ($value[12] == '')  && ($value[13] == '')  && ($value[14] == '') ){
				unset($data[$key]);continue;
			}
			$chengben += $value[7];
			$tuihuo   += $value[8];
			#对入库方式和是否结价 做处理存库
			$data[$key][1] = $ruku_type_arr_new[$value[1]];
			$data[$key][2]  = $jiejia_arr[$value[2]];
			if($e_info['from_company_id'] ==58){
					$item=$model->getGoodsE($value[0]);
					if($item){
						$checksession = $this->checkSession($item['warehouse_id']);
						if(is_string($checksession)){
							$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$item['warehouse']."</b>".$checksession."</span>的权限请联系管理员开通");
							Util::jsonExit($result);
						}
					}else{
						$result['error'] = "不能获取货号:{$value[0]}的货品信息，不能制批发销售单!";
						Util::jsonExit($result);					
					}	
			} 
		}
		$num = count($data);
		$e_info['chengbenjia'] = $chengben;
		$e_info['xiaoshoujia'] = $tuihuo;
		$e_info['goods_num']   = $num;
		//统计  货品总数量  货品成本总价  货品销售价总和
		//验证。。。。。每个货品都有效
		//单据插入  商品插入
		$model  = new WarehouseBillInfoEModel(22);
		$res= $model->update_info_e($data,$e_info,$old_goods_id);

		if($res['success'] == 1)
		{
			$data = array('bill_id'=>$id,'bill_no'=>$bill_no,'status'=>1,'update_time'=>$time, 'update_user'=>$_SESSION['userName'], 'update_ip'=>Util::getClicentIp());
			$model->add_log($data);
			$result['success'] = 1;
		}
		$result['error'] = $res['error'];
		Util::jsonExit($result);
	}


	/**
	 *	show，渲染查看页面
	 */
	public function show($params)
	{
		$id = intval($params["id"]);	//单据ID
		$model = new WarehouseBillModel($id,21);
		$status = $model->getValue('bill_status');

		#获取单据附加表ID warehouse_bill_info_e
		$model = new WarehouseBillInfoEModel(21);

		//获取取单据取消时的操作用户和操作时间
		$WarehouseBillModel = new WarehouseBillModel(21);
		$billcloseArr=$WarehouseBillModel->get_bill_close_status($id);
		
		$this->render('warehouse_bill_e_show.html',array(
			'view' => new WarehouseBillView(new WarehouseBillModel($id, 21)),
			'dd' => new DictView(new DictModel(1)),
			'bar'=>Auth::getViewBar(),
			'status'=>$status,
			'billcloseArr'=>$billcloseArr,
			'isViewChengbenjia'=>$this->isViewChengbenjia(),
		));
	}

	/**
	 * mkJson 生成Json表单
	 */
	public function mkJson()
	{
		$id = _Post::getInt('id');

		$arr = Util::iniToArray(APP_ROOT.'warehouse/data/from_table_e.tab');
		if(!$id) //添加页面调用
		{
			$arr['data'] = [
				// ["","","","","","","","","",""],
				];
		}else{
			$arr['data'] = array();
			$model = new WarehouseBillGoodsModel(21);
			$data = $model->get_bill_data($id);
			foreach($data as $k => $v){
				$num = $k+1;
				$arr['data'][] =["{$num}", "{$v['goods_id']}", "{$v['goods_sn']}", "{$v['goods_name']}", "{$v['caizhi']}", "{$v['jinzhong']}","{$v['yanse']}","{$v['zuanshidaxiao']}","{$v['sale_price']}","{$v['shijia']}"];
			}

			/** 解决明细编辑时，不满10行，空行无法编辑的页面插件 bug  START**/
			$row = count($arr['data']);
			$mo = $row%10;
			if( $mo != 0 ){
				for ($i=0; $i < (10-$mo); $i++) {
					$arr['data'][] =["", "", "", "", "", "","","","","",""];
				}
			}
			if(empty($arr['data'])){
				for ($i=0; $i < 10; ) {
					$i++;
					$arr['data'][] =["{$i}", "", "", "", "", "","","","","",""];
				}
			}
			/** 解决明细编辑时，不满10行，空行无法编辑的页面插件 bug  END**/
		}
		$json = json_encode($arr);
		echo $json;
	}

	/** 根据货号，查询货品信息 (table插件查询使用) **/
	public function getGoodsInfoByGoodsID($params){
		//var_dump($_REQUEST);exit;
		//获取入库方式
		$dd= new DictView(new DictModel(1));
		$ruku_type_arr = $dd->getEnumArray('warehouse.put_in_type');
		$ruku_type_arr_new = array();
		foreach ($ruku_type_arr as $key=>$val)
		{
			$ruku_type_arr_new[$val['name']] = $val['label'];
		}
		$jiejia = $dd->getEnumArray('confirm');
		$jiejia_arr = array();
		foreach ($jiejia as $key=>$val)
		{
			$jiejia_arr[$val['name']] = $val['label'];
		}

		//var_dump($jiejia_arr);exit;
		$goods_id = $params['goods_id'];
		$from_company_id = explode("|",$params['from_company_id']);
		$from_company_id = $from_company_id[0];
		$bill_no =isset($params['bill_no'])?$params['bill_no']:'';
		if($goods_id){

			$gmodel = new WarehouseGoodsModel(21);
			$goods = $gmodel->getGoodsInfoByGoodsID($goods_id);
			if(!empty($goods)){
				if($goods['company_id'] != $from_company_id)
				{
					$error = "货号为<span style='color:red;'>{$goods_id}</span>不是所选公司的货品，不能制损益单。";
					$return_json = ['success' =>0 , 'error'=>$error];
					echo json_encode($return_json);exit;
				}
				/*黄文銮提要求，去掉判断---JUAN
				if($goods['order_goods_id'] != 0)
				{
					$error = "货号为<span style='color:red;'>{$goods_id}</span>已经绑定订单，不能制损益单。";
					$return_json = ['success' =>0 , 'error'=>$error];
					echo json_encode($return_json);exit;
				}*/

				if($goods['is_on_sale'] == 2){	//库存状态
					$put_in_type = $goods['put_in_type'];
					$jiejia = $goods['jiejia'];
					if($jiejia == Null)
					{
						$jiejia = 0;
					}
					$return_json = ["{$ruku_type_arr_new[$put_in_type]}", "{$jiejia_arr[$jiejia]}","{$goods['goods_sn']}","{$goods['jinzhong']}", "{$goods['zhushilishu']}", "{$goods['zuanshidaxiao']}", "{$goods['yuanshichengbenjia']}", "{$goods['yuanshichengbenjia']}","{$goods['fushilishu']}", "{$goods['fushizhong']}", "{$goods['yanse']}","{$goods['jingdu']}", "{$goods['zhengshuhao']}", "{$goods['goods_name']}"];
				}else{
					//编辑时  判断货品是否属于当前单据
					/*if($goods['is_on_sale'] == 6){
						$model = new WarehouseBillModel(21);
						$goods_info = $model->getGoodsInfoBybillNo($bill_no,$goods_id);
						isset($goods_info['goods_id'])?$goods_info['goods_id']:'';
						if($goods_info['goods_id']==$goods_id){
							$put_in_type = $goods['put_in_type'];
							$jiejia = $goods['jiejia'];
							if($jiejia == Null)
							{
								$jiejia = 0;
							}
							$return_json = ["{$ruku_type_arr_new[$put_in_type]}", "{$jiejia_arr[$jiejia]}","{$goods['goods_sn']}","{$goods['jinzhong']}", "{$goods['zhushilishu']}", "{$goods['zuanshidaxiao']}", "{$goods['yuanshichengbenjia']}", "{$goods['yuanshichengbenjia']}","{$goods['fushilishu']}", "{$goods['fushizhong']}", "{$goods['yanse']}","{$goods['jingdu']}", "{$goods['zhengshuhao']}", "{$goods['goods_name']}"];
						}

					  }else{*/
					  	$error = "货号为<span style='color:red;'>{$goods_id}</span>的货品不是库存状态，不能制损益单";
					  	$return_json = ['success' =>0 , 'error'=>$error];
					  //}

				}
			}else{
				$error = "仓库查不到货号为<span style='color:red;'>{$goods_id}</span>的货品";
				$return_json = ['success' =>0 , 'error'=>$error];
			}
			$json = json_encode($return_json);
			echo $json;
		}
	}



	/**
	 * mkJsonEdit 生成Json表单
	 */
	public function mkJsonEdit()
	{
		$id = _Post::getInt('id');
		$arr = Util::iniToArray(APP_ROOT.'warehouse/data/from_table_e_edit.tab');
		//echo $id;exit;
		//查询损益单明细
		$model = new WarehouseBillGoodsModel(21);
		$res = $model->get_bill_data($id);
		$detail = array();
		/*****字典******************/
		$dd= new DictView(new DictModel(1));
		$ruku_type_arr = $dd->getEnumArray('warehouse.put_in_type');
		$ruku_type_arr_new = array();
		foreach ($ruku_type_arr as $key=>$val)
		{
			$ruku_type_arr_new[$val['name']] = $val['label'];
		}
		$jiejia = $dd->getEnumArray('confirm');
		$jiejia_arr = array();
		foreach ($jiejia as $key=>$val)
		{
			$jiejia_arr[$val['name']] = $val['label'];
		}
		/*********字典*************/
		foreach($res as $key => $val)
		{
			$in_warehouse_type = $val['in_warehouse_type'];
			$account = $val['account'];

			$detail[$key][]	= $val['goods_id'];
			$detail[$key][]	= $ruku_type_arr_new[$in_warehouse_type];
			$detail[$key][]	= $jiejia_arr[$account];
			$detail[$key][]	= $val['goods_sn'];
			$detail[$key][]	= $val['jinzhong'];
			$detail[$key][]	= $val['zhushilishu'];
			$detail[$key][]	= $val['zuanshidaxiao'];
			$detail[$key][]	= $val['sale_price'];
			$detail[$key][]	= $val['shijia'];
			$detail[$key][] = $val['fushilishu'];
			$detail[$key][] = $val['fushizhong'];
			$detail[$key][] = $val['yanse'];
			$detail[$key][] = $val['jingdu'];
			$detail[$key][] = $val['zhengshuhao'];
			$detail[$key][] = $val['goods_name'];
		}


		$arr['data'] = $detail;
		$json = json_encode($arr);
		echo $json;
	}
	/**
	 * 添加损益单
	 */
	public function insert()
	{
		$result = array('success' => 0,'error' =>'');
		if(isset($_POST['data'])){
			$data	= $_POST['data'];
		}else{
			$result['error'] = "请输入单据明细！";
			Util::jsonExit($result);
		}
		$model  = new WarehouseBillInfoEModel(22);
		$time   = date('Y-m-d H:i:s');
		$bill_no = '';

		$company = explode("|",$_POST['from_company_id']);

		$e_info = array(
			'bill_no'=>$bill_no,
			'from_company_id'=>$company[0],
			'from_company_name'=>$company[1],
			'goods_num'=>0,
			'chengbenjia'=>0,
			'xiaoshoujia'=>0,
			'bill_note'=>$_POST['bill_note'],
			'create_user'=>$_SESSION['userName'],
			'create_time'=>$time,
			'bill_type'=>'E'
			);
		//验证。。。。。每个货品都有效
		//统计  货品总数量  货品成本总价  货品销售价总和
		$num = 0;
		$chengben = 0;
		$tuihuo   = 0;
		$goods_str="";

		/**字典***/
		$dd= new DictView(new DictModel(1));
		$ruku_type_arr = $dd->getEnumArray('warehouse.put_in_type');
		$ruku_type_arr_new = array();
		foreach ($ruku_type_arr as $key=>$val)
		{
			$ruku_type_arr_new[$val['label']] = $val['name'];
		}

		$jiejia = $dd->getEnumArray('confirm');
		$jiejia_arr = array();
		foreach ($jiejia as $key=>$val)
		{
			$jiejia_arr[$val['label']] = $val['name'];
		}

		$goodsModel = new WarehouseGoodsModel(21);
		foreach ($data as $key=>$value)
		{
			/** 过滤啥也没填的，空的明细信息 **/
			if( ($value[0] == '') && ($value[1] == '') && ($value[2] == '')  && ($value[3] == '')  && ($value[4] == '')  && ($value[5] == '')  && ($value[6] == '')  && ($value[7] == '')  && ($value[8] == '')  && ($value[9] == '')  && ($value[9] == '')  && ($value[9] == '')  && ($value[9] == '')  && ($value[10] == '')  && ($value[11] == '')  && ($value[12] == '')  && ($value[13] == '')  && ($value[14] == '') ){
				unset($data[$key]);continue;
			}
			$num++;
			$chengben += $value[7];
			$tuihuo   += $value[8];
			#对入库方式和是否结价 做处理存库
			$data[$key][1] = $ruku_type_arr_new[$value[1]];

			$data[$key][2]  = $jiejia_arr[$value[2]];
			//var_dump($value[1]);var_dump($value[2]);exit;
			if(!empty($value[0])){
				$goods_str.=",'".$value[0]."'";
			}
			else
			{
				$result['error'] = "明细中的货品货号不能为空";
				Util::jsonExit($result);
			}
			/*黄文銮提要求，去掉判断---JUAN
			$order_goods_id = $goodsModel->getOrderGoodsId($value[0]);
			if($order_goods_id != 0){
				$result['error'] = "货号为<span style='color:red;'>{$value[0]}</span>已经绑定了订单，不能制损益单。";
				Util::jsonExit($result);
			}*/
			if($e_info['from_company_id'] ==58){
					$item=$model->getGoodsE($value[0]);
					if($item){
						$checksession = $this->checkSession($item['warehouse_id']);
						if(is_string($checksession)){
							$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$item['warehouse']."</b>".$checksession."</span>的权限请联系管理员开通");
							Util::jsonExit($result);
						}
					}else{
						$result['error'] = "不能获取货号:{$value[0]}的货品信息，不能制批发销售单!";
						Util::jsonExit($result);					
					}	
			} 			
		}
		$goods_str = ltrim($goods_str, ',');
		//var_dump($goods_str);exit;

		$e_info['chengbenjia'] = $chengben;
		$e_info['xiaoshoujia'] = $tuihuo;
		$e_info['goods_num']   = $num;
		//单据插入  商品插入
		$res= $model->add_info_e($data,$e_info,$goods_str);

		if ($res['success'] == true)
		{
			//添加日志
			$data = array('bill_id'=>$res['id'],'bill_no'=>$bill_no,'status'=>1,'update_time'=>$time, 'update_user'=>$_SESSION['userName'], 'update_ip'=>Util::getClicentIp());
			$model->add_log($data);
			$result['id'] = $res['id'];
			$result['success'] = 1;
			$result['error'] = '操作成功';
		}
		else
		{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}
	/***
	function:check
	description:审核损益单
	***/
	public function checkBillInfoE()
	{
		$result = array('success' => 0,'error' =>'');
		// 1、货品状态改为已损益 2、单据改为已审核
		$id	   = _Post::getInt('id');

		$billModel = new WarehouseBillModel($id, 21);
		$status = $billModel->getValue('bill_status');
		if($status == 2){
			$result['error'] = '当前单据已经是审核状态，不能重复审核';
			Util::jsonExit($result);
		}
		if($status == 3){
			$result['error'] = '当前单据已经取消，不能做审核操作';
			Util::jsonExit($result);
		}

		$create_user = $billModel->getValue('create_user');
		if($create_user == $_SESSION['userName']){
			$result['error'] = '自己不能审核自己的单据';
			Util::jsonExit($result);
		}
/*		if(!in_array($_SESSION['userName'],array("admin","sz张宇","谭碧玉","梁全升","程丹蕾","韦芦芸"))){
			if($create_user == $_SESSION['userName']){
				$result['error'] = '自己不能审核自己的单据';
				Util::jsonExit($result);
			}
		}	*/

		$gmodel = new WarehouseBillGoodsModel(21);
		$data  = $gmodel->get_bill_data($id);		//获取单据明细
		$time= date("Y-m-d H:i:s");
		$str_id   = '';
		foreach ($data as $key=>$val)
		{
			if ($key == 0)
			{
				$str_id .= "'".$val['goods_id']."'";
			}
			else
			{
				$str_id .= ",'".$val['goods_id']."'";
			}
            if($billModel->getValue('from_company_id')==58){
				$checksession = $this->checkSession($val['warehouse_id']);
				if(is_string($checksession)){
					$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>[".$val['warehouse']."</b>] ".$checksession."</span>,请授权后再来处理");
					Util::jsonExit($result);
				} 
            }			
		}
		$model = new WarehouseBillInfoEModel(22);
		$res   = $model->check_info_e($id,$str_id);
		if ($res)
		{
			//添加日志
			$time = date('Y-m-d H:i:s');
			$billModel = new WarehouseBillModel($id, 22);
			$bill_no = $billModel->getValue('bill_no');
			$data = array('bill_id'=>$id,'bill_no'=>$bill_no,'status'=>2,'update_time'=>$time, 'update_user'=>$_SESSION['userName'], 'update_ip'=>Util::getClicentIp());
			$model->add_log($data);
			$result['success'] = 1;
			$result['error'] = '审核成功';
			//AsyncDelegate::dispatch('warehouse', array('event' => 'bill_E_checked', 'bill_id' => $id, 'goods_ids' => $str_id));
		}
		else
		{
			$result['error'] = '审核失败';
		}
		Util::jsonExit($result);
	}
	/***
	function:cancel
	取消单据
	***/
	public function closeBillInfoE()
	{
		// 1、货品状态还原 2、单据取消
		$time= date("Y-m-d H:i:s");
		$result = array('success' => 0,'error' =>'');
		// 1、货品状态改为已损益 2、单据改为已审核
		$id = _Post::getInt('id');

		$billModel = new WarehouseBillModel($id, 21);

		/*boss-789
		$create_user = $billModel->getValue('create_user');
		$now_user = $_SESSION['userName'];
		if($create_user !== $now_user){
			$off = '<button class="btn btn-sm yellow" onclick="util.closeTab(this);" data-url="" name="离开" title="关闭当前页签" list-id="84" data-title="">离开 <i class="fa fa-mail-reply"></i></button>';
			die('亲~ 非本人单据，你是不能取消的哦！#^_^#  '.$off);
		}
        */
		
		
		$warehouseArr=$billModel->get_bill_warehouse($id);
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
		
		
		
		$status = $billModel->getValue('bill_status');
		if($status == 2){
			$result['error'] = '当前单据已经是审核状态，不能做取消操作';
			Util::jsonExit($result);
		}
		if($status == 3){
			$result['error'] = '当前单据已经取消，不能重复取消';
			Util::jsonExit($result);
		}

		$model = new WarehouseBillGoodsModel(22);
		$data  = $model->get_bill_data($id);
		$str_id   = '';
		foreach ($data as $key=>$val)
		{
			if ($key == 0)
			{
				$str_id .= "'".$val['goods_id']."'";
			}
			else
			{
				$str_id .= ",'".$val['goods_id']."'";
			}
		}
		$model = new WarehouseBillInfoEModel(22);
		$res   = $model->cancel_info_e($id,$str_id);
		if ($res)
		{
			$goods = explode(',',$str_id);
			$change=[];$where=[];
			foreach ($goods as $k=>$v) {
				$where[$k]['goods_id'] = $v;
				$change[$k]['is_sale'] = '1';	//上架
				$change[$k]['is_valid'] = '1';	//有效
			}
			$ApiSalePolcy = new ApiSalepolicyModel();
			$ApiSalePolcy->setGoodsUnsell($change,$where);

			//添加日志
			$bill_no = $model->getValue('bill_no');
			$data = array('bill_id'=>$id,'bill_no'=>$bill_no,'status'=>3,'update_time'=>$time, 'update_user'=>$_SESSION['userName'], 'update_ip'=>Util::getClicentIp());
			$model->add_log($data);
			$result['success'] = 1;
			$result['error'] = '取消成功';
		}
		else
		{
			$result['error'] = '操作失败';
		}
		Util::jsonExit($result);
	}

	public function getGoodsListByBillId($params){
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
		$pageData['jsFuncs'] = 'warehouse_bill_goods_show_page';

		$this->render('warehouse_bill_goods.html',array(
				'pa' =>Util::page($pageData),
				'dd' => new DictView(new DictModel(1)),
				'data' => $data,
				'isViewChengbenjia'=>$this->isViewChengbenjia(),
		));
	}
	//打印明细
	public function printBill()
	{
		$id = _Request::get('id');
		$model = new WarehouseBillModel($id,21);
		$data  = $model->getDataObject();
		$goods_info = $model->getDetail($id);

		$this->render('e_print.htm', array(
				'data' => $data,
				'goods_info' => $goods_info,
		));

	}
	//打印汇总
	public function printSum()
	{
		$id = _Request::get('id');
		$model = new WarehouseBillModel($id,21);
		$data  = $model->getDataObject();
		//$newmodel = new WarehouseBillInfoEModel($id,21);
		$goods_info = $model->getDetail($data["id"]);
		#获取明细信息
		$ZhuchengseInfo = $model->getBillinfo($id);
		//材质信息
		$zhuchengsezhongxiaoji = $zuixinlingshoujiaxiaoji = 0;
		$zhuchengsetongji[]=0;
		foreach($ZhuchengseInfo['zhuchengsedata'] as $val)
		{
			$zhuchengsezhongxiaoji += $val['jinzhong'];
			$zhuchengsetongji[] = $val;
		}
		//主石信息
		$zhushilishuxiaoji = $zhushizhongxiaoji = 0;
		$zhushitongji[]=0;
		foreach ($ZhuchengseInfo['zhushidata'] as $val)
		{
			$zhushilishuxiaoji += $val['zhushilishu'];
			$zhushizhongxiaoji += $val['zuanshidaxiao'];
			$zhushitongji[] = $val;
		}
		//副石信息
		$fushilishuxiaoji = $fushizhongxiaoji = 0;
		$fushitongji[]=0;
		foreach ($ZhuchengseInfo['fushidata'] as $val)
		{
			$fushilishuxiaoji += $val['fushilishu'];
			$fushizhongxiaoji += $val['fushizhong'];
			$fushitongji[] = $val;
		}
		//echo "<pre>";print_r($data);exit;
		$this->render('e_sum_print.htm', array(
				'data' => $data,
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

}

	/************************************************************************************
	货品限制（待做）：货品状态（库存）  、货品所在公司（与单据相对应）、
	添加保存限制：保证货品状态和货品所在公司都正确  货品状态改为损益中
	审核：
	************************************************************************************/


?>