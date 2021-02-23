<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoHController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-21 21:31:17
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoHController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('printBill','printSum');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }		
		//库房列表
		$model_w = new WarehouseModel(21);
		$warehouse = $model_w->select(array('is_delete'=>1),array("id","name",'code'));
		$warehouseids= Util::eexplode(',', $_SESSION['userWareList']);
		$quanxianwarehouseid = $this->WarehouseListO();

		if($quanxianwarehouseid!==true){
			foreach($warehouse as $key=>$val){
				if(!in_array($val['id'],$quanxianwarehouseid)){
					unset($warehouse[$key]);
				}
			}
		}
        $info = $this->get_put_company();
		$this->render('warehouse_bill_info_h.html',array(
			//'bar'=>Auth::getBar(),
			'warehouse' => $warehouse,
			'JxcWholesale' => $info['JxcWholesale'],
            'put_company' => $info['put_company'],
			'view'=>new WarehouseBillView(new WarehouseBillModel(21))

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
			//'参数' = _Request::get("参数");


		);




		$page = _Request::getInt("page",1);
		$where = array();

		$model = new WarehouseBillInfoHModel(21);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_bill_info_h_search_page';
		$this->render('warehouse_bill_info_h_search_list.html',array(
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
		$result['content'] = $this->fetch('warehouse_bill_info_h_info.html',array(
			'view'=>new WarehouseBillInfoHView(new WarehouseBillInfoHModel(21))
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		//库房列表
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$model = new WarehouseBillInfoHModel(21);

        $bar = $this->get_detail_view_bar_new($model);

        $show_pifajia = $bar[0];
        $show_mingyichenggben =$bar[1];
        $show_caigou_price = $bar[2];

		//库房列表
		$model_w = new WarehouseModel(21);
		$warehouse = $model_w->select(array('is_delete'=>1),array("id","name",'code'));
		//$warehouseids= Util::eexplode(',', $_SESSION['userWareList']);
		$quanxianwarehouseid = $this->WarehouseListO();

		if($quanxianwarehouseid!==true){
			foreach($warehouse as $key=>$val){
				if(!in_array($val['id'],$quanxianwarehouseid)){
					unset($warehouse[$key]);
				}
			}
		}
        $WarehouseBillModel = new WarehouseBillModel($id,21);
        $ret = $WarehouseBillModel->getDataObject();
        $to_customer_id =$ret['to_customer_id'];
        $to_company_id =$ret['to_company_id'];
        $to_company_name = $ret['to_company_name'];
        $info = $this->get_put_company();
        $JxcWholesale = $info['JxcWholesale'];
        $put_company = $info['put_company'];
        $orgi_warehouse = in_array($to_company_id,array_keys($put_company));
        //$bar = $this->get_detail_view_bar_new();
        //var_dump($bar);die;
		//查询加工商 出库类型
		$this->render('warehouse_bill_info_h_edit.html',array(
			'view'=>new WarehouseBillView(new WarehouseBillModel($id,21)),
			'dd'=>new DictView(new DictModel(1)),
			'JxcWholesale' => $JxcWholesale,
			'to_customer_id' => $to_customer_id,
            'to_company_id' => $to_company_id,
            'to_company_name'=> $to_company_name,
            'put_company' => $put_company,
			'warehouse' => $warehouse,
            'show_pifajia'=>$show_pifajia,
            'show_mingyichenggben'=>$show_mingyichenggben,
            'show_caigou_price'=>$show_caigou_price,
            'orgi_warehouse'=>$orgi_warehouse,
            'isViewChengbenjia'=>$this->isViewChengbenjia(),
            'is_show_caigoujia'=>$this->checkBillHCaiGouJia($id)
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

		#获取单据附加表ID warehouse_bill_info_m
		//$model = new WarehouseBillInfoMModel(21);
		//$row = $model->getMinfo($id);
		//获取取单据取消时的操作用户和操作时间
		$WarehouseBillModel = new WarehouseBillModel(21);
		$billcloseArr=$WarehouseBillModel->get_bill_close_status($id);
        $bar = $this->get_detail_view_bar_new($model);

        $show_pifajia = $bar[0];
        $show_mingyichenggben =$bar[1];
        $show_caigou_price = $bar[2];
		$show_private_data_zt = Auth::user_is_from_base_company();
        $view = new WarehouseBillView(new WarehouseBillModel($id, 21));
        $model_w = new WarehouseModel(21);
        $warehouse = $model_w->getMasterWarehouse($view->get_to_company_id());
        $warehouseids= Util::eexplode(',', $_SESSION['userWareList']);
        foreach($warehouse as $key=>$val){
            if(!in_array($val['id'],$warehouseids) && $_SESSION['userType'] != '1'){
                unset($warehouse[$key]);
            }
        }
        $orgi_warehouse = in_array($view->get_to_warehouse_id(),array_column($warehouse, 'id'));
		if(defined('IS_ZHOUSHAN_SYS') && IS_ZHOUSHAN_SYS=='YES'){
			$orgi_warehouse =1;
			$warehouse = array();
			$warehouse[] =array('id'=>697,'code'=>'XXHPTHK','name'=>'线下货品退货库');
		}
		$this->render('warehouse_bill_info_h_show.html',array(
			'view' => $view,
			'bar'=>Auth::getViewBar(),
			'status'=>$status,
            'userType'=>$_SESSION['userType'],
            'show_pifajia'=>$show_pifajia,
            'show_mingyichenggben'=>$show_mingyichenggben,
            'show_caigou_price'=>$show_caigou_price,
			'billcloseArr'=>$billcloseArr,
            'to_warehouse'=>$warehouse,
            'orgi_warehouse'=>$orgi_warehouse,
			'isViewChengbenjia'=>$this->isViewChengbenjia(),
            'show_private_data_zt'=>$show_private_data_zt,
            'is_show_caigoujia'=>$this->checkBillHCaiGouJia($id)
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$data = ( isset($_POST['data']) && !empty($_POST['data']) ) ? $_POST['data'] : '';
		if(empty($data)){
			Util::jsonExit($result['error'] = "请添加单据货品明细");
		}
		$model  = new WarehouseBillInfoHModel(22);
        $rel_model       = new WarehouseRelModel(21);
		//制单时间
		$time   = date("Y-m-d H:i:s");
		//退货备注
		$bill_note =_Request::get('bill_note');
		//未定 存哪
		$to_customer_id=_Request::get('to_customer_id');
        $to_company_id=_Request::get('to_company_id');
		$bill_no = '';
		//实价总金额（对应 成本价）
		//批发总金额（对应 名义价）
		//差价总金额（对应 销售价）
		//退货仓id 和 名称
		//if (empty($params['to_warehouse_id']))
		//{
			//$result['error'] = '请选择仓库';
			//Util::jsonExit($result);
		//}

        if (empty($to_company_id))
        {
            $result['error'] = '请选择入库公司';
            Util::jsonExit($result);
        }
        if($to_company_id == 58){
            //（2）   入库公司为总公司默认入库仓为浩鹏后库
            if(SYS_SCOPE == 'zhanting'){
                $to_warehouse_id = 1873;
                $to_warehouse_name = '浩鹏后库';
            }else{
                $to_warehouse_id = 695;
                $to_warehouse_name = '批发展厅待取库';
                if(defined('IS_ZHOUSHAN_SYS') && IS_ZHOUSHAN_SYS=='YES'){
	                $to_warehouse_id = 697;
	                $to_warehouse_name = '线下货品退货库';                	
                }
            }
            
        }else{
            //（1）   入库公司为省代默认入库仓为门店后库
            $relInfo = $rel_model->select3(' `w`.`id`,`w`.`name` ' , " r.warehouse_id = w.id and `r`.`company_id` = {$to_company_id} and `w`.`type` = 2 " , $type = 'getRow');
            if(!empty($relInfo)){
                $to_warehouse_id = $relInfo['id'];
                $to_warehouse_name = $relInfo['name'];
            }else{
                $to_warehouse_id = '';
                $to_warehouse_name = '';
            }
            
        }
		//$ar2 = explode("|",$params['to_warehouse_id']);
		//$to_warehouse_id	= $ar2[0];
		//$to_warehouse_name	= $ar2[1];
        if(empty($to_warehouse_id)){
            $result['error'] = '入库公司未查到后库仓！';
            Util::jsonExit($result);
        }
		//$company_id      = $rel_model->GetCompanyByWarehouseId($to_warehouse_id);
		$company_model   = new CompanyModel(1);
		$company_name    = $company_model->getCompanyName($to_company_id);

		if(!$to_customer_id){
			$result['error'] = '请选择退货客户！';
			Util::jsonExit($result);
		}
		//echo 222;exit;
		$c_info = array(
			'bill_no'=>$bill_no,
			'bill_note'=>$bill_note,
			'create_user'=>$_SESSION['userName'],
			'create_time'=>$time,
			'bill_type'=>'H',
			'to_warehouse_id'=>$to_warehouse_id,
			'to_warehouse_name'=>$to_warehouse_name,
			'to_customer_id'=>$to_customer_id,
			'to_company_id'=>$to_company_id,
			'to_company_name'=>$company_name
			);
		//var_dump($c_info);exit;
		//验证。。。。。每个货品都有效
		//统计  货品总数量  货品成本总价  货品销售价总和

		$goods_sum   = 0;
		$xiaoshoujia = 0;
		$chengbenjia = 0;
		$mingyijia   = 0;

		foreach ($data as $key => $value)
		{
			/** 剔除什么也没填的数据 **/
			if( ($value[0]=='') && ($value[1]=='') && ($value[2]=='') && ($value[3]=='') && ($value[4]=='') && ($value[5]=='') && ($value[6]=='') && ($value[7]=='') && ($value[8]=='')  && ($value[9]=='')  && ($value[10]=='')  && ($value[11]=='') && ($value[12]=='') && ($value[13]=='') && ($value[14]=='')&& ($value[15]=='')&& ($value[16]=='')&& ($value[17]=='')&& ($value[18]=='')&& ($value[19]=='') && ($value[20]=='') ){
				unset($data[$key]);continue;
			}
            //判断是否金额，是否需要解密
            $data[$key][5] = !is_numeric($value[5])?base64_decode($value[5]):$value[5];
            $data[$key][6] = !is_numeric($value[6])?base64_decode($value[6]):$value[6];
            $data[$key][7] = !is_numeric($value[7])?base64_decode($value[7]):$value[7];
            $data[$key][8] = !is_numeric($value[8])?base64_decode($value[8]):$value[8];
			$chengbenjia += $data[$key][5];
			$xiaoshoujia += $data[$key][6];
			$mingyijia += $data[$key][7];

		}
		if (!count($data))
		{
			$result['error'] = '请添加货品';
			Util::jsonExit($result);
		}

		$c_info['goods_num']   = count($data);
		$c_info['goods_total'] = $chengbenjia;//采购价  即原始成本价
		$c_info['shijia'] = $xiaoshoujia;//实价
		$c_info['pifajia'] = $mingyijia;//存退货货品的批发价 

		//单据插入  商品插入
		$res= $model->add_info_h($data,$c_info);

		if($res['success'] == 1)
		{
			$result['success'] = 1;
			$result['id'] = $res['x_id'];
			$result['label'] = $res['label'];
			$result['tab_id'] = mt_rand();
			$result['error'] = '添加批发退货单成功！';
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
		//var_dump($_REQUEST);exit;
		$result = array('success' => 0,'error' =>'');
		//整理数据
		$id = _Post::getInt('id');

		$billModel = new WarehouseBillModel($id, 21);
		$status = $billModel->getValue('bill_status');
		$bill_no = $billModel->getValue('bill_no');
		if($status == 2){
			$result['error'] = '当前单据已经审核，不能修改';
			Util::jsonExit($result);
		}
		if($status == 3){
			$result['error'] = '当前单据已经取消，不能修改';
			Util::jsonExit($result);
		}

		$data	= $_POST['data'];
		$model  = new WarehouseBillInfoHModel(22);
		//制单时间
		//退货备注
		$bill_note =_Request::get('bill_note');
		$c_info = array(
			'id'=>$id,
			'bill_no'=>$bill_no,
			'bill_note'=>$bill_note,
			'create_user'=>$_SESSION['userName'],
			'bill_type'=>'H',
		);
		//统计  货品总数量  货品成本总价  货品销售价总和
		$chengbenjia= 0;
		$mingyijia = 0;
		$xiaoshoujia = 0;
		foreach ($data as $key => $value)
		{
			/** 剔除什么也没填的数据 **/
			if( ($value[0]=='') && ($value[1]=='') && ($value[2]=='') && ($value[3]=='') && ($value[4]=='') && ($value[5]=='') && ($value[6]=='') && ($value[7]=='') && ($value[8]=='')  && ($value[9]=='')  && ($value[10]=='')  && ($value[11]=='') && ($value[12]=='') && ($value[13]=='') && ($value[14]=='')&& ($value[15]=='')&& ($value[16]=='')&& ($value[17]=='')&& ($value[18]=='')&& ($value[19]=='')&& ($value[20]=='')){
				unset($data[$key]);continue;
			}
            //如果是字母解密
            $data[$key][5] = !is_numeric($value[5])?base64_decode($value[5]):$value[5];
            $data[$key][6] = !is_numeric($value[6])?base64_decode($value[6]):$value[6];
            $data[$key][7] = !is_numeric($value[7])?base64_decode($value[7]):$value[7];
            $data[$key][8] = !is_numeric($value[8])?base64_decode($value[8]):$value[8];
            $chengbenjia += $data[$key][5];
            $xiaoshoujia += $data[$key][6];
            $mingyijia += $data[$key][7];

		}
		if (!count($data))
		{
			$result['error'] = '请添加货品';
			Util::jsonExit($result);
		}

		$c_info['goods_num']   = count($data);
		$c_info['goods_total'] = $chengbenjia;
		$c_info['shijia'] = $xiaoshoujia;
		$c_info['pifajia']   = $mingyijia;

		//单据插入  商品插入
		$res   = $model->get_data_h($id);//需要删除的数据信息
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
		$res= $model->update_info_h($data,$c_info,$del_data);

		if($res !== false)
		{
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
		$model = new WarehouseBillInfoHModel($id,22);
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


	/**
	 * mkJson 生成Json表单
	 */
	public function mkJson(){
		$id = _Post::getInt('id');
		//$arr = Util::iniToArray(APP_ROOT.'warehouse/data/from_table_bill_m.tab');
		$view = new WarehouseBillInfoHView(new WarehouseBillInfoCModel(21));
        //实价 入库公司的用户可以看可以编辑 非入库公司用户不能看不能编辑
        $to_company_id = '';
        if($id){
            $model = new WarehouseBillModel($id,22);
            $do = $model->getDataObject();
            $to_company_id = $do['to_company_id'];
        }
        $edit_shijia = false;
        $compInfo = $this->getUserCompanyId();//所拥有公司
        if($to_company_id != '' && !empty($compInfo)){
            if(in_array($to_company_id, $compInfo)) $edit_shijia = true;
        }
        $arr = $view->js_table;
        if($edit_shijia || $_SESSION['userType'] == '1') $arr = $view->js_table_shijia;
		if(!$id){
			$arr['data_bill_h'] = [
			];
		}else{
			$arr['data_bill_h'] = $view->getTableGoods($id);
		}
        $bar = $this->get_detail_view_bar_new();
        $is_ck = $this->checkBillHCaiGouJia($id);
        $show_cg_price = $bar[2];
        $show_pifajia = $bar[0];
        //新建和编辑批发退货单，采购价需要加密隐藏
        if(!empty($arr)){
            foreach ($arr['data_bill_h'] as $key => $value) {
                if($is_ck){
                    if($show_cg_price && $_SESSION['userType'] != '1') $arr['data_bill_h'][$key][5] = base64_encode($value[5]);
                }else{
                    $arr['data_bill_h'][$key][5] = base64_encode($value[5]);
                }
                if($show_pifajia && $_SESSION['userType'] != '1'){
                    $arr['data_bill_h'][$key][6] = base64_encode($value[6]);
                    $arr['data_bill_h'][$key][7] = base64_encode($value[7]);
                    $arr['data_bill_h'][$key][8] = base64_encode($value[8]);
                }
            }
        }
		//var_dump($arr['data_bill_h']);exit;
		$json = json_encode($arr);
		echo $json;
	}


	/** 根据货号，查询货品信息 (table插件查询使用) **/
	public function getGoodsInfoByGoodsID($params){

		//var_dump($_REQUEST);exit;
		$goods_id = $params['goods_id'];
		$bill_id = isset($params['bill_id']) ? $params['bill_id'] : 0 ;
		$to_customer_id = isset($params['to_customer_id']) ? $params['to_customer_id'] : 0 ;
		$model = new WarehouseBillInfoHModel(21);
		//判断是否是当前单据的明细，如果是当前单据的明细，说明用户啥也没有输入，掠过查询货品信息的过程
		$exists = false;
		if($bill_id){
			$exists = $model->checkDetail($goods_id, $bill_id);
		}
		//var_dump($params);exit;
		if(!$exists){
			if($goods_id){
			    $goodsinfo = $model->get_pifa_xiaoshoujia($goods_id,$to_customer_id,3);
				//var_dump($goodsinfo);exit;
				if(empty($goodsinfo)){
					$error = "该货品没查出符合条件批发销售单！";
					$return_json = ['success' =>0 , 'error'=>$error];
					echo json_encode($return_json);exit;
				}
			    $return_json = ["{$goodsinfo['goods_sn']}", "{$goodsinfo['jinzhong']}", "{$goodsinfo['zuanshidaxiao']}", "{$goodsinfo['zhengshuhao']}", "{$goodsinfo['chengbenjia']}","{$goodsinfo['shijia_price']}", "{$goodsinfo['pifa_price']}", "0", "{$goodsinfo['shoucun']}", "{$goodsinfo['changdu']}", "{$goodsinfo['caizhi']}", "{$goodsinfo['zhushi']}","{$goodsinfo['zhushilishu']}","{$goodsinfo['fushi']}","{$goodsinfo['fushilishu']}","{$goodsinfo['fushizhong']}","{$goodsinfo['zongzhong']}","{$goodsinfo['jingdu']}","{$goodsinfo['yanse']}","{$goodsinfo['goods_name']}"];
    			$json = json_encode($return_json);
    			//echo "<pre>";print_r($json);exit;
    			echo $json;
			}
		}
	}


	/** 取消单据 **/
	public function closeBillInfoH($params){
		//var_dump($_REQUEST);exit;
		$result = array('success' => 0,'error' =>'');
		$bill_id = $params['id'];
		$bill_no = $params['bill_no'];
		$model = new WarehouseBillModel($bill_id,21);
		$create_user = $model->getValue('create_user');
		$now_user = $_SESSION['userName'];
		if(SYS_SCOPE=='boss'){
		    if($create_user !== $now_user){
				$result['error'] = '亲~ 非本人单据，你是不能取消的哦！#^_^#  ';
				Util::jsonExit($result);
			}
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

		$model = new WarehouseBillInfoHModel(22);
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
		$res = $model->closeBillInfoH($bill_id,$bill_no,$goods_ids);
		if($res){
			$result['success'] = 1;
			$result['error'] = '单据取消成功!!!';
			//AsyncDelegate::dispatch('warehouse', array('event' => 'bill_H_closed', 'bill_id' => $bill_id, 'goods_ids' => $goods_ids, 'to_company_id' => $data['to_company_id']));

		}else{
			$result['error'] = '单据取消失败!!!';
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
        if(!empty($data['data'])){
            foreach ($data['data'] as &$val) {
                $val['out_warehouse_type'] = $g_model->getOutWarehouseTypeByGoodsId($val['goods_id']);
            }
        }
        $bar = $this->get_detail_view_bar_new();

        $show_pifajia = $bar[0];
        $show_mingyichenggben =$bar[1];
        $show_caigou_price = $bar[2];
        $show_private_data_zt = Auth::user_is_from_base_company();
		//var_dump($data);exit;
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_bill_goods_show_page';

		$this->render('warehouse_bill_goods.html',array(
				'pa' =>Util::page($pageData),
				'dd' => new DictView(new DictModel(1)),
				'data' => $data,
                'userType'=>$_SESSION['userType'],
                'show_pifajia'=>$show_pifajia,
                'show_mingyichenggben'=>$show_mingyichenggben,
                'show_caigou_price'=>$show_caigou_price,
				'isViewChengbenjia'=>$this->isViewChengbenjia(),
                'show_private_data_zt'=>$show_private_data_zt,
                'is_show_caigoujia'=>$this->checkBillHCaiGouJia($bill_id)
		));
	}


	/**审核单据**/
	public function checkBillInfoH($params){
		if(SYS_SCOPE=='zhanting'){
			$this->ZTcheckBillInfoH($params);
		}
		$result = array('success' => 0,'error' =>'');
		$bill_id = $params['id'];
		$bill_no = $params['bill_no'];
        $to_warehouse_id = $params['to_warehouse_id'];
		$model = new WarehouseBillModel($bill_id,21);

        if(!$to_warehouse_id){
            $result['error'] = '请选择入库仓！';
            Util::jsonExit($result);
        }

        $to_warehouse_name=$model->getWarehouseNameByID($to_warehouse_id);


		//根据仓库id获取仓库是否锁定(盘点)
		//$model ->check_warehouse_lock($model->getValue('to_warehouse_id'));
        $model ->check_warehouse_lock($to_warehouse_id);

		//$checksession = $this->checkSession($model->getValue('to_warehouse_id'));
		if(defined('IS_ZHOUSHAN_SYS') && IS_ZHOUSHAN_SYS=='YES'){
            //舟山系统统一批发退货到'线下货品退货库' id=697
		}else{
	        $checksession = $this->checkSession($to_warehouse_id);
			if(is_string($checksession)){
				$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$to_warehouse_name."</b>".$checksession."</span>的权限请联系管理员开通");
				Util::jsonExit($result);
			}
        }
		$create_user = $model->getValue('create_user');
		if($create_user == $_SESSION['userName']){
		  $result['error'] = '不能审核自己的单据';
		  Util::jsonExit($result);
		}

		/** 如果单据是审核/取消状态 不允许修改 **/
		$status = $model->getValue('bill_status');

		if($status == 2){
			$result['error'] = '单据已审核，不能审核';
			Util::jsonExit($result);
		}else if($status == 3){
			$result['error'] = '单据已取消，不能审核';
			Util::jsonExit($result);
		}

		//取得单据信息
		$data  = $model->getDataObject();
		$data['to_warehouse_name']=$to_warehouse_name;
		$newmodel = new WarehouseBillInfoHModel(22);
		$goods_list = $newmodel->getBillGoogsList($bill_id);	#获取明细列表
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
		$res = $newmodel->checkBillInfoH($bill_id,$bill_no,$data,$goods_ids,$to_warehouse_id);
		if($res){
			$result['success'] = 1;
			$result['error'] = '审核成功';
			//AsyncDelegate::dispatch('warehouse', array('event' => 'bill_H_checked', 'bill_id' => $bill_id, 'goods_ids' => $goods_ids, 'to_company_id' => $data['to_company_id']));
		}else{
			$result['error'] = '审核失败';
		}
		Util::jsonExit($result);
	}

	/**浩鹏审核H单据**/
	public function ZTcheckBillInfoH($params){		
		$result = array('success' => 0,'error' =>'');
		$bill_id = $params['id'];
		$bill_no = $params['bill_no'];
        //$to_warehouse_id = $params['to_warehouse_id'];

		$model = new WarehouseBillModel($bill_id,21);

		/** 如果单据是审核/取消状态 不允许修改 **/
		$status = $model->getValue('bill_status');

		if($status <> 1){
			$result['error'] = '单据不是已保存状态，不能审核';
			Util::jsonExit($result);
		}
		//取得单据信息
		$data  = $model->getDataObject();
		$newmodel = new WarehouseBillInfoHModel(22);
		$goods_list = $newmodel->getBillGoogsList($bill_id);	#获取明细列表
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
		$res = $newmodel->ZTcheckBillInfoH($bill_id,$bill_no,$data,$goods_ids);
		if($res){
			$result['success'] = 1;
			$result['error'] = '审核成功';
			//AsyncDelegate::dispatch('warehouse', array('event' => 'bill_H_checked', 'bill_id' => $bill_id, 'goods_ids' => $goods_ids, 'to_company_id' => $data['to_company_id']));
		}else{
			$result['error'] = '审核失败';
		}
		Util::jsonExit($result);
	}


	/**浩鹏签收H单据**/
	public function signBillInfoH($params){
		if(SYS_SCOPE=='boss'){
			exit('不支持此操作');
		}
       
		$result = array('success' => 0,'error' =>'');
		$bill_id = $params['id'];
		$bill_no = $params['bill_no'];
        $remark = $params['remark'];
        $to_warehouse_id = $params['to_warehouse_id'];
		$model = new WarehouseBillModel($bill_id,21);

        if(!$to_warehouse_id){
            $result['error'] = '请选择入库仓！';
            Util::jsonExit($result);
        }

        $to_warehouse_name=$model->getWarehouseNameByID($to_warehouse_id);


		//根据仓库id获取仓库是否锁定(盘点)
		//$model ->check_warehouse_lock($model->getValue('to_warehouse_id'));
        $model ->check_warehouse_lock($to_warehouse_id);

		//$checksession = $this->checkSession($model->getValue('to_warehouse_id'));
        $checksession = $this->checkSession($to_warehouse_id);
		if(is_string($checksession)){
			$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$to_warehouse_name."</b>".$checksession."</span>的权限请联系管理员开通");
			Util::jsonExit($result);
		}

		$create_user = $model->getValue('create_user');
		if($create_user == $_SESSION['userName']){
			$result['error'] = '不能签收自己的单据';
			Util::jsonExit($result);
		}

		/** 如果单据是审核/取消状态 不允许修改 **/
		$status = $model->getValue('bill_status');

		if($status != 2){
			$result['error'] = '单据不是已审核，不能签收';
			Util::jsonExit($result);
		}

		//取得单据信息
		$data  = $model->getDataObject();
		$data['to_warehouse_name']=$to_warehouse_name;
		$newmodel = new WarehouseBillInfoHModel(22);
		$goods_list = $newmodel->getBillGoogsList($bill_id);	#获取明细列表
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
		$res = $newmodel->signBillInfoH($bill_id,$bill_no,$data,$goods_ids,$to_warehouse_id,$remark);
		if($res){
			$result['success'] = 1;
			$result['error'] = '签收成功';
			//AsyncDelegate::dispatch('warehouse', array('event' => 'bill_H_signed', 'bill_id' => $bill_id, 'goods_ids' => $goods_ids, 'to_company_id' => $data['to_company_id']));
		}else{
			$result['error'] = '签收失败';
		}
		Util::jsonExit($result);
	}


	/**浩鹏审核H单据**/
	public function finCheckBillInfoH($params){		
		$result = array('success' => 0,'error' =>'');
		$bill_id = $params['id'];
		$bill_no = $params['bill_no'];
        //$to_warehouse_id = $params['to_warehouse_id'];
        $remark = $params['remark'];
		$model = new WarehouseBillModel($bill_id,21);

		/** 如果单据是审核/取消状态 不允许修改 **/
		$status = $model->getValue('jiejia');

		if($status <> 0){
			$result['error'] = '单据不是未结算状态，不能审核';
			Util::jsonExit($result);
		}
        
        $status = $model->getValue('bill_status');
		if($status <> 4){
			$result['error'] = '单据不是已签收状态，不能审核';
			Util::jsonExit($result);
		}

		//取得单据信息
		$data  = $model->getDataObject();
		$newmodel = new WarehouseBillInfoHModel(22);
		$goods_list = $newmodel->getBillGoogsList($bill_id);	#获取明细列表
		
		//data:单据信息 goods_ids：所有货品拼接
		$res = $newmodel->finCheckBillInfoH($bill_id,$bill_no);
		if($res){
			$result['success'] = 1;
			$result['error'] = '审核成功';
			//AsyncDelegate::dispatch('warehouse', array('event' => 'bill_H_finchecked', 'bill_id' => $bill_id, 'goods_ids' => $goods_ids, 'to_company_id' => $data['to_company_id']));
		}else{
			$result['error'] = '审核失败';
		}
		Util::jsonExit($result);
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
		$shijia_total=0;
		foreach($goods_info as $val){
			$zuanshidaxiao +=$val['zuanshidaxiao'];
			$fushizhong +=$val['fushizhong'];
			$jinzhong +=$val['jinzhong'];
			$shijia_total += $val['shijia'];
		}
		
		$wholesModel = new JxcWholesaleModel(21);
		$whoList = $wholesModel->select2(' `wholesale_id`, `wholesale_name` '," `wholesale_status` = 1 ", 'all');
		$wholesaleArr=array();
		foreach ($whoList as $v){
		    $wholesaleArr[$v['wholesale_id']]=$v['wholesale_name'];
		}
		$this->render('xiaoshoutuihuo_print.htm', array(
				'data' => $data,
				'goods_info' => $goods_info,
				'jinzhong' => $jinzhong,
				'zuanshidaxiao' => $zuanshidaxiao,
				'fushizhong' => $fushizhong,
		        'shijia_total' => $shijia_total,
		        'wholesaleArr'=>$wholesaleArr
		));

	}


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
		$wholesModel = new JxcWholesaleModel(21);
		$whoList = $wholesModel->select2(' `wholesale_id`, `wholesale_name` '," `wholesale_status` = 1 ", 'all');
		$wholesaleArr=array();
		foreach ($whoList as $v){
		    $wholesaleArr[$v['wholesale_id']]=$v['wholesale_name'];
		}
		
		$this->render('xiaoshoutuihuo_print_ex.htm', array(
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
		        'wholesaleArr'=>$wholesaleArr
		));

	}


	public function getGoodsInfos(){

		//var_dump($_REQUEST);exit;
		$g_ids = _Get::getList('g_ids');
		$g_ids = array_filter($g_ids);
		$bill_id = _Get::getInt('bill_id');
		$to_customer_id=_Request::get('to_customer_id');
        $to_company_id = _Request::get('to_company_id');
		if(empty($to_customer_id)){
			//$error = "请选择退货客户！";
			$error = array('1'=>'请选择退货客户！');
			$return_json = ['success' =>0 , 'error'=>$error];
			echo json_encode($return_json);exit;
		}
        if(empty($to_company_id)){
            //$error = "请选择退货客户！";
            $error = array('1'=>'请选择入库公司！');
            $return_json = ['success' =>0 , 'error'=>$error];
            echo json_encode($return_json);exit;
        }
		$model = new WarehouseGoodsModel(21);
		$newmodel = new WarehouseBillInfoHModel(21);
		$view = new WarehouseBillInfoHView($model);
	 	foreach($g_ids as $key=>$goods_id){

			$goodsinfo[$key] = $newmodel->get_pifa_xiaoshoujia($goods_id,$to_customer_id,3);
			//var_dump($goodsinfo);exit;
			//if(empty($goodsinfo[$key]) || in_array($goodsinfo[$key]['company_id'], array(58, 445))){
			if(empty($goodsinfo[$key])){
				$error = array('2'=>'该货品没查出符合条件批发销售单(审核状态 货品状态 退货客户)！');
				$return_json = ['success' =>0 , 'error'=>$error];
				echo json_encode($return_json);exit;
			}
            //6、 批发退货单的货品必须是哪个公司出，只能退回此公司
            if($goodsinfo[$key]['from_company_id'] != $to_company_id){
                $error = array('2'=>'批发退货单的货品必须是哪个公司出，只能退回此公司！');
                $return_json = ['success' =>0 , 'error'=>$error];
                echo json_encode($return_json);exit;
            }
		}
        //
        $bar = $this->get_detail_view_bar_new();
        $show_cg_price = isset($bar[2])?$bar[2]:true;
        $show_pifajia = $bar[0];

        $edit_shijia = false;
        $compInfo = $this->getUserCompanyId();//所拥有公司
        if($to_company_id != '' && !empty($compInfo)){
            if(in_array($to_company_id, $compInfo)) $edit_shijia = true;
        }
		//$res = $model->table_GetGoodsInfo($g_ids,$view->js_table['lable'],3,$bill_id);
		$res = $model->table_GetGoodsInfo($g_ids,$view->js_table['lable'],3,$bill_id, 0,0,0,0,0,array('to_customer_id'=>$to_customer_id));
		//echo "<pre>";print_r($goodsinfo);exit;
		if(!empty($res['success'])){
			foreach($res['success'] as $key=>$val){
                $res['success'][$key][4]=$goodsinfo[$key]['caigoujia'];
                $res['success'][$key][5]=$goodsinfo[$key]['shijia'];
                $res['success'][$key][6]=$goodsinfo[$key]['shijia'];
			    if($show_cg_price && $_SESSION['userType'] != '1') $res['success'][$key][4] = base64_encode($goodsinfo[$key]['caigoujia']);
                if($show_pifajia && $_SESSION['userType'] != '1'){
                    $res['success'][$key][5]=base64_encode($goodsinfo[$key]['shijia']);
                    $res['success'][$key][6]=base64_encode($goodsinfo[$key]['shijia']);
                }
			}
		}
		//var_dump($res);exit;
		echo json_encode($res);exit;
	}

    //获取用户所拥有公司
    public function getUserCompanyId()
    {
        $user_company_model = new UserCompanyModel(1);
        $where = array('user_id'=>$_SESSION['userId']);
        $companyId = $user_company_model->getUserCompanyList($where);
        $companyId = array_column($companyId,'company_id');//所拥有公司
        return $companyId;
    }

    //获取入库公司
    public function get_put_company()
    {
        $JxcWholesaleModel =  new JxcWholesaleModel(22);
        $companyId = $this->getUserCompanyId();
        //1、登陆人员所属公司为门店（非总公司）的人，【退货客户】只能显示自己的所属公司
        $is_company = Auth::user_is_from_base_company();
        $str = '';
        if(!empty($companyId) && !$is_company){
            $str = " and sign_company in(".implode(",", $companyId).") ";
        }
        //根据所拥有公司查询出批发客户
        $JxcWholesale=$JxcWholesaleModel->select2(' * ' , "wholesale_status=1 {$str}" , $type = 'all');
        //2、    增加入库公司
        $put_company = array();
        $company_model = new CompanyModel(1);
        //（4） 如果是总公司的人做单，入库公司就是总公司和所有省代
        if($is_company){
            $put_company['58'] = '总公司';
            $companyInfo = $company_model->select2(' `id`,`company_name` ' , " is_deleted = 0 and (is_shengdai = '1' or company_type=4) " , $type = '1');//取出所有省代公司
            foreach ($companyInfo as $value) {
                $put_company[$value['id']] = $value['company_name'];
            }
        }else{
            $companyInfo = $company_model->select2(' `id`,`company_name`,`is_shengdai`,`sd_company_id`, `company_type`' , " is_deleted = 0 and id in(".implode(",", $companyId).") " , $type = '1');
            if(!empty($companyInfo)){
                $mark_shengdai = false;//是否省代做单
                $put_company_sd = array();//省代公司做单
                $put_company_jx = array();//上级省代公司
                $company_type=0;
                $wai_xie_company=array();
                foreach ($companyInfo as $value) {
                    $sd_company_id = $value['sd_company_id'];
                    if($value['is_shengdai'] == 1){
                        $mark_shengdai = true;
                        $put_company_sd[$value['id']] = $value['company_name'];
                    }
                    if($sd_company_id){
                        $company_name = $company_model->select2(' `company_name` ' , " id = '{$sd_company_id}' " , $type = '3');
                        $put_company_jx[$sd_company_id] = $company_name;
                    }
                    if($value['company_type']==4){
                    	$company_type=4;
                    	$wai_xie_company=$value;
                    } 	
                }
                if($mark_shengdai == true){
                    //（3）如果是省代做单，入库公司就是省代自己
                    $put_company = $put_company_sd;
                    $put_company['58'] = '总公司';
                    //如果创建单据的人是省代的，那么【退货客户】只能是省代下面的经销商
                    if(!empty($put_company)){
                        $jxsInfo = array();
                        foreach ($put_company as $id => $name) {
                            $companyinfo= $company_model->select2(' `id` ' , " sd_company_id = '{$id}' " , $type = '1');
                            if(!empty($companyinfo)){
                                $companyinfo = array_column($companyinfo, 'id');
                                $jxsInfo[$id]=$JxcWholesaleModel->select2(' * ' , "wholesale_status=1 and sign_company in(".implode(",", $companyinfo).") " , $type = 'all');
                            }
                        }
                        if(!empty($jxsInfo)){
                            foreach ($jxsInfo as $val) {
                                foreach ($val as $r) {
                                    $JxcWholesale[] = $r;
                                }
                            }
                        }
                    }
                }else{
                    //（1） 如果是省代下面的经销商，入库公司显示上级省代公司和总公司
                    if(!empty($put_company_jx)){
                        $put_company = $put_company_jx;
                    }
                    if(!empty($wai_xie_company)){
                    	$put_company[$wai_xie_company['id']]=$wai_xie_company['company_name'];
                        $JxcWholesale=$JxcWholesaleModel->getTocustByCompanyID($wai_xie_company['id']);
                    }	
                    //（2） 如果是普通经销商，入库公司只显示总公司
                    $put_company['58'] = '总公司';
                }
            }
        }

        return array('JxcWholesale'=>$JxcWholesale, 'put_company'=>$put_company);
    }
}

?>
