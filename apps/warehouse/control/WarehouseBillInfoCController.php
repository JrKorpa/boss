<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoCController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-18 17:26:57
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoCController extends CommonController
{
	protected $smartyDebugEnabled = true;
	protected $whitelist = array('printBill','printSum','printHedui');

	//获取数字字典的 入库方式
	public function getPutType(){
		$dd = new DictModel(1);
		$arr = $dd->getEnumArray('warehouse.put_in_type');
		$put_in_type = array();
		foreach($arr as $row){
			$put_in_type[$row['name']] = $row['label'];
		}
		return $put_in_type;
	}

	//根据传来的入库方式 汉字 => 转化为 数字
	public function FlipPutType($ruku){
		$dd = new DictModel(1);
		$arr = $dd->getEnumArray('warehouse.put_in_type');
		$res = 0;
		foreach($arr as $row){
			if($row['label'] == $ruku){
				$res = $row['name'];
				break;
			}
		}
		return $res;
	}

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$company=$this->getCompanyList();

		$model_p = new ApiProModel();
		$pro_list = $model_p->GetSupplierList(array('status'=>1));//调用加工商接口

		$this->render('warehouse_bill_info_c.html',array(
			'view'=>new WarehouseBillView(new WarehouseBillModel(21)),
			'dd'=>new DictView(new DictModel(1)),
			'company'=>$company,
			'pro_list'=>$pro_list
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
			'bill_id'=>_Request::get('id'),
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['bill_id'] =$args['bill_id'];
		$model = new WarehouseBillGoodsModel(21);
		$data = $model->pageList($where,$page,10,false);
		$pageData=$data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_bill_info_c_search_page';
		$this->render('warehouse_bill_info_c_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'isViewChengbenjia'=>$this->isViewChengbenjia(),
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('warehouse_bill_info_c_info.html',array(
			'view'=>new WarehouseBillInfoCView(new WarehouseBillInfoCModel(1))
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
		$company=$this->getCompanyList();
		$model_p = new ApiProModel();
		$pro_list = $model_p->GetSupplierList(array('status'=>1));//调用加工商接口

		//查询加工商 出库类型
		$this->render('warehouse_bill_info_c_edit.html',array(
			'view'=>new WarehouseBillView(new WarehouseBillModel($id,21)),
			'dd'=>new DictView(new DictModel(1)),
			'company'=>$company,
			'pro_list'=>$pro_list
			));
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		//$id = $params['id'];
		#财务连接需要单据号
		if (isset($params['bill_no']))
		{
			$model	= new WarehouseBillModel(21);
			$id     = $model->getIdByBillNo(trim($params['bill_no']));
		}
		else
		{
			$id = intval($params["id"]);
		}
		$billModel = new WarehouseBillModel($id, 21);
		$status = $billModel->getValue('bill_status');


		/**加工商**/
		$model_p = new ApiProModel();
		$pro_list = $model_p->GetSupplierList(array('status'=>1));//调用加工商接口

		//获取取单据取消时的操作用户和操作时间
		$WarehouseBillModel = new WarehouseBillModel(21);
		$billcloseArr=$WarehouseBillModel->get_bill_close_status($id);
		
		$this->render('warehouse_bill_info_c_show.html',array(
			'view'=>new WarehouseBillView(new WarehouseBillModel($id,22)),
			'bar'=>Auth::getViewBar(),
			'status'=>$status,
			'dd'=>new DictModel(1),
			'billcloseArr'=>$billcloseArr,
			'isViewChengbenjia'=>$this->isViewChengbenjia(),
		));
	}


	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		//var_dump($_REQUEST);exit;
		$result = array('success' => 0,'error' =>'');
		$data	= isset($_POST['data'])?$_POST['data']:array();//edit by zhangruiying
		$model  = new WarehouseBillInfoCModel(22);
		$time   = date('Y-m-d H:i:s');
		$bill_no = '';
		$pro = explode("|",$params['pro_id']);
		//var_dump($_REQUEST);exit;
		$from_company_info = explode("|",$params['from_company_id']);
		$c_info = array(
			'bill_no'=>$bill_no,
			'from_company_id'=>trim($from_company_info[0]),
			'from_company_name'=>trim($from_company_info[1]),
			'goods_num'=>0,
			'goods_total'=>0,
			'shijia'=>0,

			'bill_note'=>$params['bill_note'],
			'create_user'=>$_SESSION['userName'],
			'create_time'=>$time,
			'bill_type'=>'C',
			'order_sn'=>$params['order_sn'],
			'pro_id'  =>$pro[0],
			'pro_name'  =>$pro[1],
			'chuku_type'=>$params['chuku_type']
			);
		//验证。。。。。每个货品都有效

		//统计  货品总数量  货品成本总价  货品销售价总和
		$chengben = 0;
		$tuihuo   = 0;
		$goodsModel = new WarehouseGoodsModel(21);
		if(!empty($data))
		{
			
			foreach ($data as $key => $value)
			{
				/** 剔除什么也没填的数据 **/
				if( ($value[0]=='') && ($value[1]=='') && ($value[2]=='') && ($value[3]=='') && ($value[4]=='') && ($value[5]=='') && ($value[6]=='') && ($value[7]=='') && ($value[8]=='') ){
					unset($data[$key]);continue;
				}
				if(SYS_SCOPE=='zhanting'){
					$goods_row=$goodsModel->GetGoodsbyGoodid($value[0]);
					//$value[11]=$goods_row['yuanshichengbenjia'];
				    $chengben += !empty($goods_row) ?  $goods_row['yuanshichengbenjia'] : 0;
					$tuihuo   += $value[9];	//销售价	
				}
				if(SYS_SCOPE=='boss'){
				    $chengben += $value[9];
					$tuihuo   += $value[10];	//销售价					
				}

				$data[$key][3] = $this->FlipPutType($value[3]);//转化入库方式
				/* 黄文銮提要求，去掉判断---JUAN
				$order_goods_id = $goodsModel->getOrderGoodsId($value[0]);
				if($order_goods_id != 0){
					$result['error'] = "货号: <span style='color:red;'>{$value[0]}</span>已经绑定订单，不能制其他出库单。";
					Util::jsonExit($result);
				}*/
				if($c_info['from_company_id'] ==58){
					$item=$model->getGoodsC($value[0]);
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
			$c_info['goods_total'] = $chengben;
			$c_info['shijia'] = $tuihuo;
			$c_info['goods_num']   = count($data);

			//单据插入  商品插入
			$res= $model->add_info_c($data,$c_info);

			if($res['success'] == 1)
			{		 
					
				$result['success'] = 1;
				$result['x_id'] = $res['x_id'];
				$result['label'] = $res['label'];
				$result['tab_id'] = mt_rand();
				$result['error'] = '添加其他出库单成功！';
			}
			else
			{
				$result['error'] = $res['error'];
			}
		}
		else
		{
			$result['error'] ='明细为空不能操作';
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
		$pro = explode("|",$params['pro_id']);

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
		$model  = new WarehouseBillInfoCModel(22);
		$time   = date('Y-m-d H:i:s');
		$bill_no= $_POST['bill_no'];
		$from_company_info = explode("|",$params['from_company_id']);
		//var_dump($from_company_info);exit;
		$c_info = array(
			'id'=>$id,
			'bill_no'=>$bill_no,
			'from_company_id'=>trim($from_company_info[0]),
			'from_company_name'=>trim($from_company_info[1]),
			'goods_num'=>0,
			'goods_total'=>0,
			'shijia'=>0,
			'bill_note'=>$_POST['bill_note'],
			'create_user'=>$_SESSION['userName'],
			'create_time'=>$time,
			'bill_type'=>'C',
			'order_sn'=>$_POST['order_sn'],
			'pro_id'  =>$pro[0],
			'pro_name'  =>$pro[1],
			'chuku_type'=>$_POST['chuku_type']
			);
		//统计  货品总数量  货品成本总价  货品销售价总和
		$num = 0;
		$chengben = 0;
		$xiaoshouchengben   = 0;
		foreach ($data as $key => $value)
		{
			if( ($value[0]=='') && ($value[1]=='') && ($value[2]=='') && ($value[3]=='') && ($value[4]=='') && ($value[5]=='') && ($value[6]=='') && ($value[7]=='') && ($value[8]=='') ){
				unset($data[$key]);continue;
			}
			
			$num += 1;

			if(SYS_SCOPE=='zhanting'){
					$goods_row=$model->getGoodsC($value[0]);
					//$value[11]=$goods_row['yuanshichengbenjia'];
				    $chengben += !empty($goods_row) ?  $goods_row['yuanshichengbenjia'] : 0;
					$xiaoshouchengben += $value[9];				//销售价	
			}
			if(SYS_SCOPE=='boss'){
				$chengben += $value[9];
				$xiaoshouchengben += $value[10];				
			}


			$data[$key][3] = $this->FlipPutType($value[3]);
			if($c_info['from_company_id'] ==58){
					$item=$model->getGoodsC($value[0]);
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
		$c_info['goods_total'] = $chengben;
		$c_info['shijia'] = $xiaoshouchengben;
		$c_info['goods_num']   = $num;

		//单据插入  商品插入
		$res   = $model->get_data_c($id);//需要删除的数据信息
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
		//var_dump($del_data);exit;
		$res= $model->update_info_c($data,$c_info,$del_data);

		if($res['success'] == 1)
		{
			$result['success'] = 1;
			$result['error'] = '修改成功！';
		}
		else
		{
			$result['error'] = $res['error'];
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
		$model = new WarehouseBillInfoCModel($id,2);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}


		/**
	 * mkJson 生成Json表单
	 */
	public function mkJson2222()
	{
		$id = _Post::getInt('id');
		if(!$id) //添加页面调用
		{
			$arr = Util::iniToArray(APP_ROOT.'warehouse/data/from_table_c.tab');
			$arr['data'] = [
				// ["","","","","","","","","",""],
			];
		}
		//编辑页面调用
		else
		{
			$arr = Util::iniToArray(APP_ROOT.'warehouse/data/from_table_c_edit.tab');
			//获取其他出库单明细信息根据id
			$model = new WarehouseBillInfoCModel(21);
			$res   = $model->get_data_c($id);
			$detail = array();
			foreach($res as $key => $val)
			{
				// echo '<pre>';print_r($val);echo '</pre>';die;
				$detail[$key][]	= $val['goods_id']; 			//货号
				$detail[$key][]	= $val['goods_name']; 			//货品名称
				$detail[$key][] = $val['goods_sn']; 			//款号
				$detail[$key][] = $val['in_warehouse_type']; 		//入库方式
				$detail[$key][] = $val['zhushijingdu']; 			//净度
				$detail[$key][] = $val['zhengshuhao']; 			//证书号
				$detail[$key][]	= $val['yanse']; 				//颜色
				$detail[$key][] = $val['zuanshidaxiao']; 			//主石重
				$detail[$key][] = $val['chengbenjia']; 			//成本价
				$detail[$key][]	= $val['xiaoshoujia']; 			//销售成本价
				$detail[$key][]	= $val['jinzhong']; 			//主成色重
				$detail[$key][] = $val['zhushilishu']; 			//主石粒数
				$detail[$key][] = $val['fushizhong']; 		//副石重
				$detail[$key][] = $val['fushilishu']; 				//副石粒数
			}
			$arr['data'] = $detail;
		}


		$json = json_encode($arr);
		echo $json;
	}

	/**
	 * mkJson 生成Json表单
	 */
	public function mkJson(){
		$id = _Post::getInt('id');
	
		//$arr = Util::iniToArray(APP_ROOT.'warehouse/data/from_table_bill_m.tab');
		$view = new WarehouseBillInfoCView(new WarehouseBillInfoCModel(21));
		$arr = $view->js_table;
	//var_dump($arr);exit;
		if(!$id){
			$arr['data_bill_c'] = [
			];
		}else{
			$arr['data_bill_c'] = $view->getTableGoods($id);
		}
		$json = json_encode($arr);
		echo $json;
	}
	/** 根据货号，查询货品信息 (table插件查询使用) **/
	public function getGoodsInfoByGoodsID($params){
		$put_in_type = $this->getPutType();
		$goods_id = $params['goods_id'];
		$from_company_id = $params['from_company_id'];
		$bill_id = isset($params['bill_id']) ? $params['bill_id'] : 0 ;
		$model = new WarehouseBillInfoCModel(21);
		//判断是否是当前单据的明细，如果是当前单据的明细，说明用户啥也没有输入，掠过查询货品信息的过程
		$exists = false;
		if($bill_id){
			$exists = $model->checkDetail($goods_id, $bill_id);
		}

		if(!$exists){
			if($goods_id){
				$goods = $model->getGoodsInfoByGoodsID($goods_id);
				if(!empty($goods)){
					if($goods['company_id'] != $from_company_id)
					{
						$error = "货号为<span style='color:red;'>{$goods_id}</span>不是所选公司的货品，不能制其他出库单。";
						$return_json = ['success' =>0 , 'error'=>$error];
						echo json_encode($return_json);exit;
					}
					/*黄文銮提要求，去掉判断---JUAN
					if($goods['order_goods_id'] != 0)
					{
						$error = "货号为<span style='color:red;'>{$goods_id}</span>已经绑定订单，不能制其他出库单。";
						$return_json = ['success' =>0 , 'error'=>$error];
						echo json_encode($return_json);exit;
					}*/

					//库存状态
					if($goods['is_on_sale'] == 2){
						$goods['put_in_type'] = $put_in_type[$goods['put_in_type']];
						$return_json = ["{$goods['goods_name']}", "{$goods['goods_sn']}", "{$goods['put_in_type']}", "{$goods['zhushijingdu']}", "{$goods['zhengshuhao']}", "{$goods['yanse']}", "{$goods['zuanshidaxiao']}", "{$goods['chengbenjia']}","{$goods['chengbenjia']}","{$goods['jinzhong']}","{$goods['zhushilishu']}","{$goods['fushizhong']}","{$goods['fushilishu']}"];
					}

					//不是库存状态
					if($goods['is_on_sale'] == 8){		//编辑的时候
						$wbgModel = new WarehouseBillGoodsModel(21);
						$c_id = $wbgModel->select2('bill_id', $where = " `goods_id` = '{$goods['goods_id']}' " , $is_all = 1);
						if($bill_id != $c_id){
							$error = "货号为<span style='color:red;'>{$goods_id}</span>的货品不是库存状态，不能制其他出货单";
							$return_json = ['success' =>0 , 'error'=>$error];
							echo json_encode($return_json);exit;
						}
						$goods['put_in_type'] = $put_in_type[$goods['put_in_type']];
						$return_json = ["{$goods['goods_name']}", "{$goods['goods_sn']}", "{$goods['put_in_type']}", "{$goods['zhushijingdu']}", "{$goods['zhengshuhao']}", "{$goods['yanse']}", "{$goods['zuanshidaxiao']}", "{$goods['chengbenjia']}","{$goods['chengbenjia']}","{$goods['jinzhong']}","{$goods['zhushilishu']}","{$goods['fushizhong']}","{$goods['fushilishu']}"];
					}else if($goods['is_on_sale'] != 2){
						//既不是库存状态，又不是当前单据的明细，那么 嘿嘿
						$error = "货号为<span style='color:red;'>{$goods_id}</span>的货品不是库存状态，不能制其他出货单";
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

		/***
	function:check
	description:审核损益单
	***/
	public function checkBillInfoC($params)
	{
		//exit("其它出库单审核待技术确认");
		$result = array('success' => 0,'error' =>'');
		// 1、货品状态改为已损益 2、单据改为已审核
		$id = $params['id'];
		$model = new WarehouseBillInfoCModel(22);
		$data  = $model->get_data_c($id);
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
		$billModel = new WarehouseBillModel($id, 21);
		$status = $billModel->getValue('bill_status');
		if($status == 2){
			$result['error'] = '当前单据已经审核，不能重复做审核操作';
			Util::jsonExit($result);
		}
		if($status == 3){
			$result['error'] = '当前单据已经取消，不能做审核操作';
			Util::jsonExit($result);
		}
		$create_user = $billModel->getValue('create_user');
		if($create_user == $_SESSION['userName']){
			$result['error'] = '不能审核自己的单据';
			Util::jsonExit($result);
		}

        //1、其他出库单审核加一个工作组， 除了之前权限校验外，出库类型为拆货的需判断是否在财务财货审核组才能审核。
        $groupModel = new GroupUserModel(1);
        $chk_type = $billModel->getValue('tuihuoyuanyin');
        $userInfo = $groupModel->getGroupUser(5);//5 其它出库拆货财务审核组
        $userInfo = array_column($userInfo,'user_id');
        if($chk_type == 3 && !in_array(Auth::$userId, $userInfo)){
            $result['error'] = '亲~ 出库类型为拆货，在财务财货审核组的同事才能审核，请授权。';
            Util::jsonExit($result);
        }

		$goods=$model->getDetail($id);
		
		foreach ($goods as $key => $goods_item) {
		  		
			$checksession = $this->checkSession($goods_item['warehouse_id']);
			if(is_string($checksession)){
				$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>[".$goods_item['warehouse']."</b>] ".$checksession."</span>,请授权后再来处理");
				Util::jsonExit($result);
			}      
		}	
		        
		$res   = $model->check_info_c($id,$str_id);
		if ($res)
		{
			$result['success'] = 1;
			$result['error'] = '审核成功';
			//AsyncDelegate::dispatch('warehouse', array('event' => 'bill_C_checked', 'bill_id' => $id, 'goods_ids' => $str_id));
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
	public function closeBillInfoC()
	{
		// 1、货品状态还原 2、单据取消
		$result = array('success' => 0,'error' =>'');
		// 1、货品状态改为已损益 2、单据改为已审核
		$id	   = _Post::getInt('id');
		$model = new WarehouseBillInfoCModel(22);
		$data  = $model->get_data_c($id);
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
		$billModel = new WarehouseBillModel($id, 21);
		$status = $billModel->getValue('bill_status');
		if($status == 2){
			$result['error'] = '当前单据已经审核，不能做取消操作';
			Util::jsonExit($result);
		}
		if($status == 3){
			$result['error'] = '当前单据已经取消，不能做重复取消操作';
			Util::jsonExit($result);
		}
		$res   = $model->cancel_info_c($id,$str_id);
		if ($res)
		{
			$result['success'] = 1;
			$result['error'] = '取消成功';
		}
		else
		{
			$result['error'] = '取消失败';
		}
		Util::jsonExit($result);
	}

	//打印详情
	public function printBill() {
		//获取bill_id单据id
		$id = _Request::get('id');
		//数字词典
		$dd =new DictModel(1);
		$model = new WarehouseBillModel($id,21);
		//打印表头信息
		$data  = $model->getDataObject();
		//拼接 出库类型到data
		$newmodel = new WarehouseBillInfoCModel(21);
		/*
		$bill_c_info = $newmodel->getInfoByBillId($id);
		foreach($bill_c_info as $key=>$val){
			$data[$key]=$val;
		}*/
		//货品详情
		$goods_info = $model->getDetail($id);

		//获取加工商支付信息
		$amount=0;
		$BillPay = $model->getBillPay($id);
		foreach($BillPay as $val){
			$amount +=$val['amount'];
			
		}

		 foreach ($goods_info as $kk => $val) {
			 
			 $goods_id = $val['goods_id'];
			$sql = "SELECT  shoucun from warehouse_goods  WHERE `goods_id` = '".$goods_id."' ";
			$shoucun = $model->db()->getOne($sql);
			$goods_info[$kk]["shoucun"] = $shoucun;
        }
       
		$this->render('chaihuo_print.htm', array(
                                'view'=>new WarehouseBillView(new WarehouseBillModel($id,22)),
				'dd' => $dd,
				'data' => $data,
				'goods_info' => $goods_info,
				'BillPay' => $BillPay,
				'amount' => $amount
		));

	}
        //打印核对单
        public function printHedui(){
            //获取bill_id单据id
		$id = _Request::get('id');
		//数字词典
		$dd =new DictModel(1);
		$model = new WarehouseBillModel($id,21);
		//打印表头信息
		$data  = $model->getDataObject();

		/*
		$newmodel = new WarehouseBillInfoCModel(21);
                $billBinfo = $newmodel->getInfoByBillId($id);

		foreach($billBinfo as $key=>$val){
			$data[$key]=$val;
		}*/

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

			$fushizhong +=$val['fushizhong'];
			$jinzhong +=$val['jinzhong'];
			$zuanshidaxiao +=$val['zuanshidaxiao'];
		}

		$this->render('chaihuo_print_detail.html', array(
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

	//打印汇总
	public function printSum() {
		//获取bill_id单据id
		$id = _Request::get('id');
		//数字词典
		$dd =new DictModel(1);
		$model = new WarehouseBillModel($id,21);
		//打印表头信息
		$data  = $model->getDataObject();
		//拼接 出库类型到data
		$newmodel = new WarehouseBillInfoCModel($id,21);
		/*
		$bill_c_info = $newmodel->getInfoByBillId($id);
		foreach($bill_c_info as $key=>$val){
			$data[$key]=$val;
		}*/
		//货品详情
		$goods_info = $model->getDetail($id);

		//获取加工商支付信息
		$amount=0;
		$BillPay = $model->getBillPay($id);
		foreach($BillPay as $val){
			$amount +=$val['amount'];
		}


		//获取单据信息  汇总
		$ZhuchengseInfo = $newmodel->getBillinfo($id);
		//材质信息
		$zhuchengsezhongxiaoji = $zuixinlingshoujiaxiaoji = 0;
		$amount='';
		foreach($ZhuchengseInfo['zhuchengsedata'] as $val){
			$zhuchengsezhongxiaoji += $val['jinzhong'];
			$zhuchengsetongji[] = $val;
		}
		//主石信息
		$zhushilishuxiaoji = $zhushizhongxiaoji = 0;
		foreach ($ZhuchengseInfo['zhushidata'] as $val) {
			$zhushilishuxiaoji += $val['zhushilishu'];
			$zhushizhongxiaoji += $val['zuanshidaxiao'];
			$zhushitongji[] = $val;
		}
		//副石信息
		$fushilishuxiaoji = $fushizhongxiaoji = 0;
		foreach ($ZhuchengseInfo['fushidata'] as $val) {
			$fushilishuxiaoji += $val['fushilishu'];
			$fushizhongxiaoji += $val['fushizhong'];
			$fushitongji[] = $val;
		}

		$this->render('chaihuo_print_ex.htm', array(
                                'view'=>new WarehouseBillView(new WarehouseBillModel($id,22)),
				'data' => $data,
				'goods_info' => $goods_info,
				'BillPay' => $BillPay,
				'amount' => $amount,
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
	
	public function getGoodsInfos(){
		$g_ids = _Get::getList('g_ids');
		$g_ids = array_filter($g_ids);
		$bill_id = _Get::getInt('bill_id');
		//var_dump($_REQUEST);exit;
		$from_company_info=0;
		if(_Request::get('from_company_id_c')){
			$from_company_info =explode("|", _Request::get('from_company_id_c'));
		}
		$from_company=$from_company_info[1]?$from_company_info[1]:10000;
		$model = new WarehouseGoodsModel(21);
		$view = new WarehouseBillInfoCView($model);
		$res = $model->table_GetGoodsInfo($g_ids,$view->js_table['lable'],2,$bill_id,0,0,0,trim($from_company)); 
		echo json_encode($res);exit;
	}



/*******************************
保存：返厂中8 审核：已返厂9
*******************************/
}

?>