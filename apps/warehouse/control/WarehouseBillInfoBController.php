<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoBController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-18 17:09:51
 *   @update	:
 *   	退货返厂单
 *	warehouse_bill 			主表
 *	warehouse_bill_info_b 	附表
 *	warehouse_bill_goods		明细表
 *  -------------------------------------------------
 */
class WarehouseBillInfoBController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('printBill','printSum','printHedui');

/**
  页面下拉数据
*/
  	//加工商
	public function jiagongshang($arr = array('status'=>1))
	{
		$model_p = new ApiProModel();
		$pro_list = $model_p->GetSupplierList($arr);//调用加工商接口
		return $pro_list;
	}

    //获取供应商
    public function getCompayList()
    {   
        $is_gys = _Request::get('is_gys');
        $is_sd = _Request::get('is_sd');
        $type_company = _Request::get('type_company');
        $array = array('status'=>1);
        //2、浩鹏系统，供应商做退货返厂单，加工商取公司信息维护里的 关联供应商
        if($is_gys == 1 && !empty($type_company)){
            $array['p_id'] = $type_company;
        }elseif($is_sd == 1 && $is_gys != 1){
            $array['p_id'] = '597';//经销商自采供应商
        }else{

        }
        //var_dump($array);die;
        $res = $this->jiagongshang($array);
        $this->render('option_company.html',array(
                'data'=>$res
        ));
    }
/**
  页面下拉数据
*/
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
	 *	add，渲染添加页面
	 */
	public function add ()
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }		
		//$company_list = $this->getCompanyList();
        $company_list = $this->getCompanyListById();
		//if(!count($company_list))
		//{
			//die("<font size='14' color='red'>请先配置仓库。。</font>");
		//}
		$this->dd = new DictView(new DictModel(1));
		$this->assign('dd',$this->dd);//数据字典
		$this->render('warehouse_bill_info_b_info.html', array(
			'view_bill' => new WarehouseBillView(new WarehouseBillModel(21)),
			'jiagongshang'=>$this->jiagongshang(),
			'company_list'=>$company_list,
			));
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = $params['id'];
		/*$company_list = $this->getCompanyList();
		if(!count($company_list))
		{
			die("<font size='14' color='red'>请先配置仓库。。</font>");
		}*/
		$this->render('warehouse_bill_info_b_info_edit.html',array(
			'view_bill' => new WarehouseBillView(new WarehouseBillModel($id, 21)),
			'jiagongshang'=>$this->jiagongshang(),
			//'company_list'=>$company_list,
		));
		

	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$pid =  trim($params['pid']);
		$from_company = $params['from_company'];
		$in_warehouse_type = $params['in_warehouse_type'];
		if( $pid == ''){
			$result['error'] = '请选择加工商';
			Util::jsonExit($result);
		}
		if( $from_company == ''){
			$result['error'] = '请选择出库仓库';
			Util::jsonExit($result);
		}
		if( $in_warehouse_type == ''){
			$result['error'] = '请选择入库方式';
			Util::jsonExit($result);
		}
		$company = explode("|",$from_company);
		$from_company_id = $company[0];
		$from_company_name = $company[1];
		$prc = explode("|",$pid);
		$prc_id = $prc[0];
		$prc_name = $prc[1];

		$model = new WarehouseBillInfoBModel(22);
		$result = array('success' => 0,'error' =>'');
		$bill_no = '';
		$bill = array(
			'bill_type'=>'B',
			'goods_num'=>0,
			'bill_no'=>$bill_no,
			'bill_note'=>_Request::get('bill_note'),
			'goods_total'=>0,
			'shijia'=>0,
			'create_user'=>$_SESSION['userName'],
			'create_time'=>date('Y-m-d H:i:s'),
			'from_company_id' => $from_company_id, 	//存储出库公司
			'from_company_name' => $from_company_name,
			'pro_id'=> $prc_id,
			'order_sn'=> $params['kela_order_sn'],
			'put_in_type'=> $params['in_warehouse_type'],
			'pro_name'=> $prc_name
			);
		/*
		$info_b = array(
			'pid'=> $prc_id,
			'kela_order_sn'=> $params['kela_order_sn'],
			'in_warehouse_type'=> $params['in_warehouse_type'],
			'prc_name'=> $prc_name
			);*/

		$goods = ( isset($_POST['data']) && !empty($_POST['data']) ) ? $_POST['data'] : '';
		if(empty($goods)){
			$result['error'] = '请添加单据明细';
			Util::jsonExit($result);
		}

		$sum = 0;
		$dd = new DictModel(1);
		$put_in_type = $dd->getEnumArray('warehouse.put_in_type');

		$goodsModel = new WarehouseGoodsModel(21);
		//var_dump($goods);exit;
		foreach($goods as $k => $row){
			//var_dump($row[14]);exit;
			//剔除啥也没填的明细列表
			if( ($row[0] == '') && ($row[1] == '') && ($row[2] == '') && ($row[3] == '') && ($row[4] == '') && ($row[5] == '') && ($row[6] == '') && ($row[7] == '') && ($row[8] == '') ){
				unset($goods[$k]);continue;
			}
			$goods[$k]['addtime'] = date('Y-m-d H:i:s');
			$goods[$k]['bill_no'] = $bill['bill_no'];
			//验证明细
			if(empty($goods)){
				$result['error'] = '请输入货品明细';
				Util::jsonExit($result);
			}

			$num = $k+1;

			/** 针对明细中的货品进行各种验证^_^ **/
			if($row[0] == ''){
				$result['error'] = "\n\r第{$num}个货品未填货号!!!";
				Util::jsonExit($result);
			}
			if($row[15] < 0){
				$result['error'] = "\n\r第{$num}个货品退货价不能为负数!!!";
				Util::jsonExit($result);
			}

			if( $row[12] =='' || $row[12] == 0){
				$result['error'] = "\n\r第{$num}个货品的数量不能为空或0!";
				Util::jsonExit($result);
			}
			$goodsModel = new WarehouseGoodsModel(21);
			$goodsInfo = $goodsModel->getGoodsByGoods_id($row[0]);
			if( $bill['from_company_id']  != $goodsInfo['company_id']){
				$result['error'] = "\n\r第{$num}个货品的不是当前出库公司的货，不允许制单!";
				Util::jsonExit($result);
			}

            if(defined('IS_ZHOUSHAN_SYS') && IS_ZHOUSHAN_SYS=='YES'){
               if($bill['pro_id']<>$goodsInfo['prc_id']){
					$result['error'] = "\n\r第{$num}个货品的供应商和当前单据的加工商不同，不允许制单!";
					Util::jsonExit($result);               	   
               }

            }


            $bill['to_warehouse_id']= !empty($goodsInfo['warehouse_id']) ? $goodsInfo['warehouse_id'] :  $bill['to_warehouse_id'] ;
            $bill['to_warehouse_name']= !empty($goodsInfo['warehouse']) ? $goodsInfo['warehouse'] :  $bill['to_warehouse_name'] ;
 
			if($row[3] == ''){
				$result['error'] = "\n\r第{$num}个货品请选择'入库方式'选项!!!";
				Util::jsonExit($result);
			}else{
				//var_dump($put_in_type);exit;
				/** 转化入库方式 **/
				foreach($put_in_type as $pk => $pv){
					if($row[3] == $pv['label']){
						$goods[$k][3] = $pv['name'];
					}
				}
			}
			
			if($row[12]){
				$row[12] =$row[12]=="无"?0:1;
			}

			if($goods[$k][3] != $bill['put_in_type']){
				$result['error'] = "第{$num}个货品的'入库方式'与当前单据的入库方式不一致!!!";
				Util::jsonExit($result);
			}

			$bill['goods_num'] = ++$sum;
			$bill['goods_total'] += $row[14];
			$bill['shijia'] += $row[15];

                
                if($bill['from_company_id']==58 && !empty($goodsInfo) && $goodsInfo['warehouse_id'] != ''){
					$checksession = $this->checkSession($goodsInfo['warehouse_id']);
					if(is_string($checksession)){
						$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>[".$goodsInfo['warehouse']."</b>] ".$checksession."</span>,请授权后再来处理");
						Util::jsonExit($result);
					} 
                }			

			//echo 111;exit;
			/* 黄文銮提要求，去掉判断---JUAN
			//检测货品是否绑定了订单，如果绑定了订单，不允许做退货返厂单
			$order_goods_id = $goodsModel->getOrderGoodsId($row[0]);
			if($order_goods_id != 0){
				$result['error'] = "第{$num}个货品 <span style='color:red;'>{$row[0]}</span> 绑定了订单，不能制退货返厂单";
				Util::jsonExit($result);
			}*/
		}
		//var_dump($bill);exit;
		$res = $model->insert_shiwu($bill, $goods);
		if($res['success'] != 0)
		{
			$result['success'] = 1;
			$result['x_id'] = $res['x_id'];
			$result['label'] = $res['label'];
			$result['tab_id'] = mt_rand();
			$result['error'] = '添加成功！';
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
	{//print_r($_POST);exit;
		$result = array('success' => 0,'error' =>'');
		$company = explode("|",$params['from_company']);
		$from_company_id = $company[0];
		$from_company_name = $company[1];
		$pid = $params['pid'];

		if( $pid == ''){
			$result['error'] = '请选择加工商';
			Util::jsonExit($result);
		}
		$id = _Post::getInt('id');
		$amountTotal = $params['amountTotal'];

		$model = new WarehouseBillModel($id, 21);
		/** 如果单据是审核/取消状态 不允许修改 **/
		$status = $model->getValue('bill_status');
		if($status == 2){
			$result['error'] = '单据已审核，不能修改';
			Util::jsonExit($result);
		}else if($status == 3){
			$result['error'] = '单据已取消，不能修改';
			Util::jsonExit($result);
		}
		
		$newmodel =  new WarehouseBillInfoBModel(22);
		$goods_old = $newmodel->getGoodsList($id);
		$del_goods_ids = '';
		$old_goods_ids = '';
		foreach($goods_old as $old_v){
			$del_goods_ids .= ','.$old_v['id'];
			$old_goods_ids .= ",'".$old_v['goods_id']."'";
		}
		$del_goods_ids = ltrim($del_goods_ids, ',');	//要删除的明细ID
		$old_goods_ids = ltrim($old_goods_ids, ',');	//要删除的明细货号

		$prc = explode("|", $pid);
		$prc_id = $prc[0];
		$prc_name = $prc[1];

		#获取跟新后的主表信息
		$bill = array(
			'bill_type'=>'B',
			'goods_num'=>0,
			'bill_note'=>_Request::get('bill_note'),
			'goods_total'=>0,
			'shijia'=>0,
			'mingyijia'=>0,
			'from_company_id' => $from_company_id, 	//存储出库仓
			'pro_id' => $prc_id,
			'order_sn'=>_Request::get('kela_order_sn'),
			'in_put_type'=>_Request::get('in_warehouse_type'),
			'pro_name'=>$prc_name
			);

		/*
		$info_b = array(
			'pid'=> $prc_id,
			'kela_order_sn'=>_Request::get('kela_order_sn'),
			'in_warehouse_type'=>_Request::get('in_warehouse_type'),
			'prc_name'=>$prc_name,
			);
		*/

		$dd = new DictModel(1);
		$put_in_type = $dd->getEnumArray('warehouse.put_in_type');

		$goodsModel = new WarehouseGoodsModel(21);
		$goods = $_POST['data'];
		$sum = 0;	//初始化货品数量
		foreach($goods as $k => $row){
			//剔除啥也没填的明细列表
			if( ($row[0] == '') && ($row[1] == '') && ($row[2] == '') && ($row[3] == '') && ($row[4] == '') && ($row[5] == '') && ($row[6] == '') && ($row[7] == '') ){
				unset($goods[$k]);continue;
			}
			$goods[$k]['addtime'] = date('Y-m-d H:i:s');
			$goods[$k]['bill_no'] =$model->getValue('bill_no');
			//验证明细
			if(empty($goods)){
				$result['error'] = '请输入货品明细';
				Util::jsonExit($result);
			}

			$num = $k+1;
			/** 针对明细中的货品进行各种验证^_^ **/
			if($row[0] == ''){
				$result['error'] = "第{$num}个货品未填货号!!!";
				Util::jsonExit($result);
			}
			if($row[15] < 0){
				$result['error'] = "第{$num}个货品退货价不能小于0!!!";
				Util::jsonExit($result);
			}

			$goodsInfo = $goodsModel->getGoodsByGoods_id($row[0]);
			if( $bill['from_company_id']  != $goodsInfo['company_id']){
				$result['error'] = "\n\r第{$num}个货品的不是当前出库公司的货，不允许制单!";
				Util::jsonExit($result);
			}

            if(defined('IS_ZHOUSHAN_SYS') && IS_ZHOUSHAN_SYS=='YES'){
               if($bill['pro_id']<>$goodsInfo['prc_id']){
					$result['error'] = "\n\r第{$num}个货品的供应商和当前单据的加工商不同，不允许制单!";
					Util::jsonExit($result);               	   
               }

            }
			
			/** 转化入库方式 **/
			foreach($put_in_type as $pk => $pv){
				if($row[3] == $pv['label']){
					$goods[$k][3] = $pv['name'];
				}
			}
			if($row[13]){
				$row[13] =$row[13]=="无"?0:1;
			}
			
			if($goods[$k][3] != $bill['in_put_type']){
				$result['error'] = "第{$num}个货品的'入库方式'与当前单据的入库方式不一致。";
				Util::jsonExit($result);
			}

            if($bill['from_company_id']==58 && !empty($goodsInfo) && $goodsInfo['warehouse_id'] != ''){
					$checksession = $this->checkSession($goodsInfo['warehouse_id']);
					if(is_string($checksession)){
						$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>[".$goodsInfo['warehouse']."</b>] ".$checksession."</span>,请授权后再来处理");
						Util::jsonExit($result);
					} 
            }	
			//$bill['mingyijia'] += $row[14];//成本价
			$bill['goods_num'] = ++$sum;
			$bill['shijia'] += $row[15];//退货价
			$bill['goods_total'] += $row[14];//退货价





		}
		/** 2015-12-25 zzm boss-1014 **/
		if (abs($amountTotal) > 100) {
            $result['error'] = "入库成本尾差不充许超过±100元，请检查是否制单错误。";
            Util::jsonExit($result);
        }
		$res = $newmodel->update_shiwu($bill, $goods, $del_goods_ids, $id,$old_goods_ids);
		if($res['success'] == 1)
		{
			$result['success'] = 1;
			$result['error'] = $res['error'];
		}
		else
		{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}

	/**
	* 详细页
	*/
	public function show($params){
		//$id = intval($params['id']);
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

		$result = array('success' => 0,'error' => '');
		$model = new WarehouseBillModel($id, 21);
		
		$jiagongshang = array();
		$arr = $this->jiagongshang();
		foreach ($arr as $key => $value) {
			$jiagongshang[$value['id']] = $value['name'];
		}
		
		// print_r($id);
		// exit;
		
		//获取取单据取消时的操作用户和操作时间
		$WarehouseBillModel = new WarehouseBillModel(21);
		$billcloseArr=$WarehouseBillModel->get_bill_close_status($id);
		
		$status = $model->getValue('bill_status');
		$this->render('warehouse_bill_info_b_info_show.html',array(
			'view_bill' => new WarehouseBillView(new WarehouseBillModel($id, 21)),
			'jiagongshang'=>$this->jiagongshang(),
			'status' => $status,	//1保存；2/审核；3/取消
			'bar' => Auth::getViewBar(),
			'jiagongshang'=>$jiagongshang,		//获取加工商列表
			'billcloseArr'=>$billcloseArr,
			'isViewChengbenjia'=>$this->isViewChengbenjia(),
				
		));	
	}
		
		
		/**
		 * mkJson 生成Json表单
		 */
		public function mkJson(){
			$dd = new DictView(new DictModel(1));
			$id = _Post::getInt('id');		
			//$arr = Util::iniToArray(APP_ROOT.'warehouse/data/from_table_bill_m.tab');
			$view = new WarehouseBillInfoBView(new WarehouseBillInfoBModel(21));
			$arr = $view->js_table;
		
			if(!$id){
				$arr['data_bill_b'] = [
				];
			}else{
				$arr['data_bill_b'] = $view->getTableGoods($id);
				if($arr['data_bill_b']){
					foreach($arr['data_bill_b'] as $key =>$val){
						//var_dump($val[3]);exit;
						if($val[3]){
							$arr['data_bill_b'][$key][3]=$dd->getEnum("warehouse.put_in_type",$val[3]);
						}

						$val[13] = (string)$val[13];
						if($val[13]==='1')
						{
							//echo 111;
							$val[13] = '已结价';
						}
						else if ($val[13]==='0')
						{
							//echo 222;
							$val[13] = '未结价';
						}
						else 
						{
							//echo 333;
							$val[13] = '无';
						}
						$arr['data_bill_b'][$key][13]=$val[13];
						//$arr['data_bill_b'][$key][13]=$val[13]==1?'已结价':'未结价';
				
					}
				}
				//var_dump($arr);exit;
				
			}
			$json = json_encode($arr);
			echo $json;
		}

	/** 根据货号，查询货品信息 (table插件查询使用) **/
	public function getGoodsInfoByGoodsID($params){
		$goods_id = $params['goods_id'];
		$company = explode("|",$params['from_company_id']);
		$in_warehouse_type = $params['in_warehouse_type'];
		$from_company_id = $company[0];
		$from_company_name = $company[1];

		$dd = new DictModel(1);

		if($goods_id){
			$arr = Util::iniToArray(APP_ROOT.'warehouse/data/from_table_bill_b.tab');
			$label = $arr['label'];
			$model = new WarehouseBillGoodsModel(21);
			$goods = $model->getGoodsInfoByGoodsID($label,$goods_id);
			if(!empty($goods)){
				if($goods['company_id'] != $from_company_id)
				{
					$error = "货号为<span style='color:red;'>{$goods_id}</span>不是所选公司的货品，不能制退货返厂单";
					$return_json = ['success' =>0 , 'error'=>$error];
					echo json_encode($return_json);exit;
				}

				if($goods['put_in_type'] != $in_warehouse_type)
				{
					$error = "货号为<span style='color:red;'>{$goods_id}</span>和单据所选入库方式不同，不能制退货返厂单";
					$return_json = ['success' =>0 , 'error'=>$error];
					echo json_encode($return_json);exit;
				}
				/*黄文銮提要求，去掉判断---JUAN
				if($goods['order_goods_id'] != 0)
				{
					$error = "货号为<span style='color:red;'>{$goods_id}</span>已经绑定了订单，不能制退货返厂单";
					$return_json = ['success' =>0 , 'error'=>$error];
					echo json_encode($return_json);exit;
				}*/

				if($goods['is_on_sale'] == 2){	//库存状态
					//是否结价0、默认无。1、未结价。2、已结价
					if($goods['jiejia'] == 0){
						$goods['jiejia'] = '无';
					}
					if($goods['jiejia'] == 1){
						$goods['jiejia'] = '未结价';
					}
					if($goods['jiejia'] == 2){
						$goods['jiejia'] = '已结价';
					}

					$return_json = ["{$goods['goods_name']}", "{$goods['goods_sn']}", "{$dd->getEnum('warehouse.put_in_type', $goods['put_in_type'])}",
						"{$goods['zhushiyanse']}", "{$goods['zhushilishu']}", "{$goods['zuanshidaxiao']}", "{$goods['fushilishu']}","{$goods['fushizhong']}",
						"{$goods['yanse']}","{$goods['jingdu']}","{$goods['zhengshuhao']}","{$goods['num']}","{$goods['jiejia']}", "{$goods['mingyichengben']}", "{$goods['mingyichengben']}", "{$goods['yuanshichengbenjia']}"];
					echo $json = json_encode($return_json);exit;
				}else{
					$error = "货号为<span style='color:red;'>{$goods_id}</span>的货品不是库存状态，不能制退货返厂单";
					$return_json = ['success' =>0 , 'error'=>$error];
					echo $json = json_encode($return_json);exit;
				}
			}else{
				$error = "仓库查不到货号为<span style='color:red;'>{$goods_id}</span>的货品";
				$return_json = ['success' =>0 , 'error'=>$error];
				echo $json = json_encode($return_json);exit;
			}
		}
	}


	/** 审核单据 **/
	public function checkBillInfoB($params){
		$bill_id = $params['id'];
		$bill_no = $params['bill_no'];

		$model = new WarehouseBillModel($bill_id, 21);
		/** 如果单据是审核/取消状态 不允许修改 **/
		$status = $model->getValue('bill_status');
		if($status == 2){
			$result['error'] = '单据已审核，不能重复审核';
			Util::jsonExit($result);
		}else if($status == 3){
			$result['error'] = '单据已取消，不能审核';
			Util::jsonExit($result);
		}
		/** 不能审核自己的单据 **/
		
		$create_user = $model->getValue('create_user');
		if($create_user == $_SESSION['userName']){
			$result['error'] = '不能审核自己的单据';
			Util::jsonExit($result);
		}
/*		if(!in_array($_SESSION['userName'],array("admin","sz张宇","谭碧玉","梁全升","程丹蕾","韦芦芸"))){
			if($create_user == $_SESSION['userName']){
				$result['error'] = '不能审核自己的单据';
				Util::jsonExit($result);
			}
	    }*/
		/** 供应商不存在结算列表中 **/
		$payModel = new WarehouseBillPayModel(21);
        $proarr = $payModel->getProArr($bill_id)?$payModel->getProArr($bill_id):array();
		//if(!in_array($lmodel->getPro($id),$proarr)){
		if(!in_array($model->getValue('pro_id'),$proarr)){
			$result['error'] = "<span style='color: #ff0000'>警告1：供应商不存在结算列表中！</span>";
			Util::jsonExit($result);
		}
		$Gmodel = new WarehouseGoodsModel(21);
		$Bmodel = new WarehouseBillInfoBModel(22);
		$data = $Bmodel->getGoodsList($bill_id);
		$goods_ids = "";
		if ($data)
		{
			foreach ($data as $key=>$val)
			{
				if ($key)
				{
					$goods_ids .= ",'".$val['goods_id']."'";
				}
				else
				{
					$goods_ids .= "'".$val['goods_id']."'";
				}

                //1、权限管控--用户需要有货号所在仓库的退货返厂单制单、取消权限，才可以保存、取消成功，否则提示：没有权限操作
                //2、权限管控--用户需要有货号所在仓库的退货返厂单审核权限，才可以审核成功，否则提示：没有权限操作
                $g_info = $Gmodel->GetGoodsbyGoodid($val['goods_id']);
                if($model->getValue('from_company_id')==58 && !empty($g_info) && $g_info['warehouse_id'] != ''){
					$checksession = $this->checkSession($g_info['warehouse_id']);
					if(is_string($checksession)){
						$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>[".$g_info['warehouse']."</b>] ".$checksession."</span>,请授权后再来处理");
						Util::jsonExit($result);
					} 
                }
			}
		}

		$res = $Bmodel->checkBillInfoB($bill_id, $bill_no,$goods_ids);
		if($res){
			$result['success'] = 1;
			$result['error'] = '审核成功';
            //生成成本尾差
            $billModel = new WarehouseBillModel($bill_id, 21);
            $chengbenjia_goods = $billModel->getValue('goods_total');
            $billModel->cha_deal($bill_id, $chengbenjia_goods);
			//AsyncDelegate::dispatch('warehouse', array('event' => 'bill_B_checked', 'bill_id' => $bill_id, 'goods_ids' => $goods_ids));
		}else{
			$result['error'] = '审核失败';
		}
		Util::jsonExit($result);
	}

	/** 取消单据 **/
	public function closeBillInfoB($params){
		$result = array('success' => 0,'error' =>'');
		$bill_id = $params['id'];
		$bill_no = $params['bill_no'];

		$model = new WarehouseBillModel($bill_id, 21);
		/** 如果单据是审核/取消状态 不允许修改 **/
		$status = $model->getValue('bill_status');
		if($status == 2){
			$result['error'] = '单据已审核，不能取消';
			Util::jsonExit($result);
		}else if($status == 3){
			$result['error'] = '单据已取消，不能重复取消';
			Util::jsonExit($result);
		}
        $Gmodel = new WarehouseGoodsModel(21);
		$model = new WarehouseBillInfoBModel(22);
		$data = $model->getGoodsList($bill_id);
		$goods_ids = "";
		if ($data)
		{
			foreach ($data as $key=>$val)
			{
				if ($key)
				{
					$goods_ids .= ",'".$val['goods_id']."'";
				}
				else
				{
					$goods_ids .= "'".$val['goods_id']."'";
				}

                //1、权限管控--用户需要有货号所在仓库的退货返厂单制单、取消权限，才可以保存、取消成功，否则提示：没有权限操作
                //2、权限管控--用户需要有货号所在仓库的退货返厂单审核权限，才可以审核成功，否则提示：没有权限操作
                $g_info = $Gmodel->GetGoodsbyGoodid($val['goods_id']);
                if(!empty($g_info) && $g_info['warehouse_id'] != ''){
                    $check_warehouse_id = $this->checkSession($g_info['warehouse_id']);
                    if($check_warehouse_id !== true){
                        $result['error'] = $val['goods_id'].":您没有<span style='color: #ff0000;'><b>[".$g_info['warehouse']."</b>] ".$check_warehouse_id."</span>,请授权后再来处理";
                        Util::jsonExit($result);
                    }
                }
			}
		}
        

		$res = $model->closeBillInfoB($bill_id,$bill_no,$goods_ids);
		if($res){
			$result['success'] = 1;
			$result['error'] = '单据取消成功';
			//修正可销售商品状态
			$goods = explode(',',$goods_ids);
			$change=[];$where=[];
			foreach ($goods as $k=>$v) {
				$where[$k]['goods_id'] = $v;
				$change[$k]['is_sale'] = '1';	//上架
				$change[$k]['is_valid'] = '1';	//有效
			}
			$ApiSalePolcy = new ApiSalepolicyModel();
			$ApiSalePolcy->setGoodsUnsell($change,$where);


		}else{
			$result['error'] = '单据取消失败';
		}
		Util::jsonExit($result);
	}

	public function search(){

		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'bill_id'=>_Request::get('id'),
		);

		//是否结价
		$jiejia_type = array(0=> '无', 1 => '已结价', 2=>'未结价');


		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['bill_id'] =$args['bill_id'];
		$model = new WarehouseBillGoodsModel(21);
		$data = $model->pageList($where,$page,10,false);
		$pageData=$data;
		$pageData['filter'] = $args;
		//$pageData['recordCount'] = count($pageData['data']);
		$pageData['jsFuncs'] = 'warehouse_bill_info_b_b_search_page';
		$this->render('warehouse_bill_info_b_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'dd' => new DictModel(1),
			'jiejia_type'=>$jiejia_type,
			'isViewChengbenjia'=>$this->isViewChengbenjia(),
		));
	}

	//打印详情
	public function printBill() {
		//获取单据bill_id
		$id = _Request::get('id');
		$dd =new DictModel(1);
		$model = new WarehouseBillModel($id,21);
		$data  = $model->getDataObject();
		//获取详情表中数据 拼接到data
		$newmodel = new WarehouseBillInfoBModel(21);
		/*
		$bill_b_info = $newmodel->getRowForBill_id($id);
		foreach($bill_b_info as $key=>$val){
			$data[$key]=$val;
		}
		*/
		//获取货品信息
		$goods_info = $model->getDetail($id);
		//获取加工商支付信息
		$amount=0;
		$BillPay = $newmodel->getBillPay($id);
		foreach($BillPay as $val){
			$amount +=$val['amount'];
		}


		$this->render('tuihuo_print.htm', array(
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


		$newmodel = new WarehouseBillInfoBModel(21);
		/*
                $billBinfo = $newmodel->getRowForBill_id($id);

		foreach($billBinfo as $key=>$val){
			$data[$key]=$val;
		}
		*/

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

		$this->render('tuihuo_print_detail.html', array(
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
		//获取单据bill_id
		$id = _Request::get('id');
		//数据字典
		$dd =new DictModel(1);
		$model = new WarehouseBillModel($id,21);
		$data  = $model->getDataObject();
		$newmodel = new WarehouseBillInfoBModel(21);
		/*
		$bill_b_info = $newmodel->getRowForBill_id($id);
		//获取详情表信息 塞进data
		foreach($bill_b_info as $key=>$val){
			$data[$key]=$val;
		}*/
		//获取商品详情
		$goods_info = $model->getDetail($id);

		
		//获取加工商支付信息.
		$amount=0;
		$BillPay = $newmodel->getBillPay($id);
		foreach($BillPay as $val){
			$amount +=$val['amount'];
		}

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
		$zhushilishuxiaoji = $zhushizhongxiaoji = 0;
		$zhushitongji[]=0;
		foreach ($ZhuchengseInfo['zhushidata'] as $val) {
			$zhushilishuxiaoji += $val['zhushilishu'];
			$zhushizhongxiaoji += $val['zuanshidaxiao'];
			$zhushitongji[] = $val;
		}
		//副石信息
		$fushilishuxiaoji = $fushizhongxiaoji = 0;
		$fushitongji[]=0;
		foreach ($ZhuchengseInfo['fushidata'] as $val) {
			$fushilishuxiaoji += $val['fushilishu'];
			$fushizhongxiaoji += $val['fushizhong'];
			$fushitongji[] = $val;
		}


		$this->render('tuihuo_print_ex.htm', array(
				'dd' => $dd,
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

/***
**通过货号获取信息
***/

public function getGoodsInfos(){
	$dd = new DictView(new DictModel(1));
	$g_ids = _Get::getList('g_ids');
	$g_ids = array_filter($g_ids);
	$bill_id = _Get::getInt('bill_id');
	//验证出货公司
	$from_company_info=0;
		if(_Request::get('from_company')){
			$from_company_info =explode("|", _Request::get('from_company'));			
		}
	$from_company=$from_company_info[1]?$from_company_info[1]:100000;
	
	//验证入库方式
	$put_in_type=_Request::get('in_warehouse_type');
	$put_in_type =$put_in_type?$put_in_type:10000;
	$model = new WarehouseGoodsModel(21);

	$view = new WarehouseBillInfoBView($model);
	$res = $model->table_GetGoodsInfo($g_ids,$view->js_table['lable'],2,$bill_id,0,0,0,$from_company,$put_in_type);
	//结果集中数字改为对应名字
	if($res['success']){
		foreach($res['success'] as $key =>$val){
			if($val[2]){
				$res['success'][$key][2]=$dd->getEnum("warehouse.put_in_type",$val[2]);
			}
			//echo $val[12];
			$val[12] = (string)$val[12];
			if($val[12]==='1')
			{
				//echo 111;
				$val[12] = '已结价';
			}
			else if ($val[12]==='0')
			{
				//echo 222;
				$val[12] = '未结价';
			}
			else 
			{
				//echo 333;
				$val[12] = '无';
			}
			//exit;
			$res['success'][$key][12]=$val[12];
				
		}
	}
	echo json_encode($res);exit;
}
    
    //根据当前用户获取出库公司
    public function getCompanyListById($value='')
    {
        //用户当前所在公司是总公司、浩鹏，则出库公司可选 总公司、浩鹏以及省代公司、供应商公司；
        $is_zong = Auth::user_is_from_base_company();
        $company_model = new CompanyModel(1);
        $company_list = array();//出库公司
        if($is_zong){
            $company[] = array('id'=>58,'company_name'=>'总公司','company_sn'=>'SZ','is_flong'=>'0','is_gys'=>'0','processor_id'=>'','is_shengdai'=>'');
            $company_info = $company_model->select2(" id,company_name,company_sn,'1' as is_flong,if(company_type=4,1,0) as is_gys,processor_id,is_shengdai " , " is_deleted = 0 and (is_shengdai =1 or company_type = 4 )" , $type = '1');
            $company_list = array_merge($company,$company_info);
        }else{
            //用户当前所在公司是供应商A，则出库公司只能选 供应商A；
            //用户当前所在公司是省代公司B，则出库公司只能选 省代公司B；
            $companyId = $_SESSION['companyId'];//当前所在公司
            $is_check = $company_model->select2(' id,is_shengdai,company_type,company_name,company_sn,processor_id ', " is_deleted = 0 and id = '{$companyId}' " , $type = '2');
            if(!empty($is_check)){
                $is_check['is_gys'] = '0';//0其他
                if($is_check['is_shengdai'] == 1 || $is_check['company_type'] == 4){
                    $is_check['is_flong'] = '1';
                    if($is_check['company_type'] == 4) $is_check['is_gys'] = '1';//1供应商
                    $company_list[] = $is_check;
                }
            }
            //用户当前所在公司not in（总公司、浩鹏or供应商or省代公司），则出库公司为空。
        }
        //var_dump($company_list);die;
        return $company_list;
    }


}


/**END CLASS **/



?>
