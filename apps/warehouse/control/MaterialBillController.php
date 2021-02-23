<?php
/**
 *  -------------------------------------------------
 *   @file		: MaterialBillController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc 
 *   @date		: 2018-01-18 14:00:47
 *   @update	:
 *  -------------------------------------------------
 */
class MaterialBillController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array( 'printBill','printSalesBill','search');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
	    $model = new MaterialBillModel(21);
		$this->render('material_bill_search_form.html',array(
		    'bar'=>Auth::getBar(),
		    'view'=>new MaterialBillView($model)
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
            'bill_no'=>_Request::getString("bill_no"),
		    'bill_type'=>_Request::getString("bill_type"),
		    'bill_status'=>_Request::getInt("bill_status"),
		    'in_warehouse_id' => _Request::getInt("in_warehouse_id"),
		    'out_warehouse_id' => _Request::getInt("out_warehouse_id"),
		    'goods_sn' => _Request::getString("goods_sn"),
		    'goods_name' => _Request::getString("goods_name"),
		    'style_sn' => _Request::getString("style_sn"),
		    'style_name' => _Request::getString("style_name"),
		    'supplier_id' => _Request::getInt("supplier_id"),
		    'catetory1' => _Request::getString("catetory1"),
		    'catetory2' => _Request::getString("catetory2"),
		    'goods_spec' => _Request::getString("goods_spec"),
			'check_time_start'  => _Request::get("check_time_start"),
			'check_time_end'    => _Request::get("check_time_end"),
			'time_start'		=> _Request::get("time_start"),
			'time_end'			=> _Request::get("time_end"),
			'department_id'     =>_Request::get("department_id"),
            'show_detail'       =>_Request::getString("show_detail"),
		);
		$page = _Request::getInt("page",1);
		$where = $args;
		if(!empty($where['bill_no'])){
		    $where['bill_no'] = explode(' ',preg_replace("/\s+/is",' ',$where['bill_no']));
		}

		/**
		 * 判断是否是下载
		 */
		$dow_info = empty(_Request::get('dow_info')) ? null : _Request::get('dow_info');
		
		$model = new MaterialBillModel(21);
		$data = $model->pageList($where,$page,10,false,$dow_info);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'material_bill_search_page';
		$this->render('material_bill_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
		    'view'=>new MaterialBillView($model),
		    'show_detail' => $args['show_detail'], 
		));
	}
	
	/**
	 *	单据明细列表
	 */
	public function searchBillGoods ($params)
	{
	    $args = array(
	        'mod'	=> _Request::get("mod"),
	        'con'	=> substr(__CLASS__, 0, -10),
	        'act'	=> __FUNCTION__,
	        //'参数' = _Request::get("参数");
	        'bill_id'=>_Request::getInt('_id'),	
	    );
	    $page = _Request::getInt("page",1);
	    $bill_id = _Request::getInt('_id');
	    $where = array('bill_id'=>$bill_id);	    
	    $model = new MaterialBillModel($bill_id,21);
	    $data = $model->billGoodsPageList($where,$page,10,false);
	    $pageData = $data;
	    $pageData['filter'] = $args;
	    $pageData['jsFuncs'] = 'material_bill_goods_search_page';
	    $this->render('material_bill_goods_search_list.html',array(
	        'pa'=>Util::page($pageData),
	        'page_list'=>$data,
	        'billView'=>new MaterialBillView($model)
	         
	    ));
	}
	/**
	 * 添加单据明细index
	 */
	public function addBillGoods(){
	    $result = array('success' => 0,'error' => '','title'=>'添加商品');
	    $bill_id = _Request::getInt('_id');
	    $billModel = new MaterialBillModel($bill_id,21);
	    $billView = new MaterialBillView($billModel);
	    $bill_type = $billView->get_bill_type();
	    $bill_status = $billView->get_bill_status();
	    if($bill_status !=1){
	        $result['content'] = "单据不是已保存状态，不能操作！";
	        Util::jsonExit($result);
	    }
	    if($billView->get_bill_cat()==1){
	        $show_tpl = 'material_bill_goods_add_rk.html';
	    }else if($billView->get_bill_cat()==2){
	        $show_tpl = 'material_bill_goods_add_ck.html';
	    }else{
	        $result['content'] = "不支持单据类型{$bill_type}！";
	        Util::jsonExit($result);
	    }
	    //echo $show_tpl;
	    $result['content'] = $this->fetch($show_tpl,array(
	        'view'=>$billView
	    ));
	    Util::jsonExit($result);
	}
	/**
	 *	添加单据明细search
	 */
	public function addBillGoodsSearchRK ($params)
	{
	    $args = array(
	        'mod'	=> _Request::get("mod"),
	        'con'	=> substr(__CLASS__, 0, -10),
	        'act'	=> __FUNCTION__,
	        //'参数' = _Request::get("参数");
	        'style_sn' => _Request::get('style_sn'),
	        'goods_sn'  => _Request::get('goods_sn'),
	        'goods_name' => _Request::get('goods_name'),
	        'style_name' => _Request::get('style_name'),
	    );
	    $bill_id = _Request::getInt("_id");
	    $billModel = new MaterialBillModel($bill_id,21);
	    $billView = new MaterialBillView($billModel);
	    $bill_type = $billView->get_bill_type();
	    $page = _Request::getInt("page",1);
	    $where = $args;
	    $where['notin_bill_id'] = $bill_id;
	    $where['order_by_field'] = 'goods_sn';
	    if($bill_type=='WH' && !empty($billView->get_from_bill_no())){
	        $data['data'] = $billModel->getBill_detail($billView->get_from_bill_no()); 
        }else{
		    $model = new MaterialGoodsModel(21);	    
		    $data = $model->pageList($where,$page,25,false);
        }
	    $pageData = $data;
	    $pageData['filter'] = $args;
	    $pageData['jsFuncs'] = 'material_bill_goods_add_list_page';
	    $this->render('material_bill_goods_add_list_rk.html',array(
	        'pa'=>Util::page($pageData),
	        'page_list'=>$data,
	        'billView'=>$billView,
	    ));
	}
	/**
	 *	添加单据明细search
	 */
	public function addBillGoodsSearchCK ($params)
	{
	    $args = array(
	        'mod'	=> _Request::get("mod"),
	        'con'	=> substr(__CLASS__, 0, -10),
	        'act'	=> __FUNCTION__,
	        //'参数' = _Request::get("参数");
	        'goods_sn'  => _Request::get('goods_sn'),
	        'goods_name' => _Request::get('goods_name'),
	        'style_sn'  => _Request::get('style_sn'),
	        'style_name'  => _Request::get('style_name'),
	        'warehouse_id' => _Request::get('warehouse_id'),
	        'supplier_id' => _Request::get('supplier_id'),
	        'number_index'=>1,
	    );
	    $bill_id = _Request::getInt("_id");
	    $page = _Request::getInt("page",1);
	    $where = $args;
	    if(!empty($where['goods_sn'])){
	        $where['goods_sn'] = explode(' ',preg_replace("/\s+/is",' ',$where['goods_sn']));
	    }
	    $billModel = new MaterialBillModel($bill_id,21);
	    $billView = new MaterialBillView($billModel);
	    $company_type = $billView->get_company_type($billModel->getValue('department_id'));
	    if(empty($company_type))
	    	$company_type = 3;
        $bill_type =  $billView->get_bill_type();
        if($bill_type=='WK' || $bill_type=='WB' || $bill_type=='WC') 
            $company_type = 1; 
	    $where['notin_bill_id'] = $bill_id;
	    $where['order_by_field'] = 'inv.goods_sn,inv.batch_sn'; 
	    $model = new MaterialInventoryModel(21);
	    $data = $model->pageList($where,$page,50,false);
        $data['data'] = $model->calcGoodsJiajiaPriceList($data['data'],$company_type);
	    $pageData = $data;
	    $pageData['filter'] = $args;
	    $pageData['jsFuncs'] = 'material_bill_goods_add_list_page';
	    $this->render('material_bill_goods_add_list_ck.html',array(
	        'pa'=>Util::page($pageData),
	        'page_list'=>$data,
	        'billView'=>$billView,
	    ));
	}
	/**
	 * 入库单单据明细添加
	 * @param unknown $params
	 */
	public function insertBillGoodsRK($params){
	    
	    $result = array("success"=>0,'error'=>'');
	    
	    $bill_id = _Request::getInt("id");
	    $billModel = new MaterialBillModel($bill_id,21);
	    $billView = new MaterialBillView($billModel);
	    $bill_info = $billModel->getDataObject();
	    if(empty($bill_info)){
	        $result['error'] = "参数错误！";
	        Util::jsonExit($result);
	    }
	    $bill_type = $billView->get_bill_type();
	    $bill_status = $billView->get_bill_status();
	    if($bill_status!=1){
	        $result['error'] = "单据不是保存状态！";
	        Util::jsonExit($result);
	    }
	    
	    try{
	        $billGoodsModel = new MaterialBillGoodsModel(21);
	        
	        $pdolist[21] = $billGoodsModel->db()->db();
	        
	        Util::beginTransaction($pdolist);
	        
	        $res = $this->buildBillGoodsListRK($bill_info,$bill_type);
	        if($res['success']==0){
	            throw new Exception($res['error']);
	        }
	        $billGoodsList = $res['data'];
    	    foreach ($billGoodsList as $newdo){
    	        $billGoodsModel->saveData($newdo,array());
    	    }
    	    
    	    Util::commitTransaction($pdolist);
    	    
    	    $result['success'] = 1;
    	    Util::jsonExit($result);
	    }catch (Exception $e){
	        $msg = $e->getMessage();
	        Util::rollbackExit($msg,$pdolist);
	    }    
	    
	}
	/**
	 * 格式化Post提交的数据，返回可以直接插入至material_bill_goods表的数据
	 * @param unknown $bill_type
	 * @return multitype:number string multitype:
	 * 返回 ： 
	 */
	protected function buildBillGoodsListRK($bill_info,$bill_type){
	    $result = array("success"=>0,'error'=>'','data'=>array());

	    $goods_sn_list = _Post::getList("goods_sn");
	    $goods_qty_list = _Post::getList("goods_qty");
	    $goods_cost_list = _Post::getList("goods_cost");
	    $in_warehouse_list = _Post::getList("in_warehouse_id");
	    $goods_shijia_list = _Post::getList("goods_shijia");
	    $batch_sn_list = _Post::getList("batch_sn");
	    $qty_limit_list = _Post::getList("qty_limit");


	    $line = 0;
	    $billGoodsModel = new MaterialBillGoodsModel(21);
	    $billModel = new MaterialBillModel(21);
	    foreach ($goods_qty_list as $key=>$goods_qty){
	        $line++;
	        $goods_sn = '';
	        $goods_cost =0;
            $batch_sn = $bill_info['bill_no'];
            $supplier_id = $bill_info['supplier_id'];

	        if(empty($goods_qty)){
	            continue;
	        }else if(!is_numeric($goods_qty)){
	            $result['error'] = "【第{$line}行】数量不合法，请填写大于0的数字！";
	            return $result;
	        }    	        
	        if(empty($goods_sn_list[$key])){
	            $result['error'] = "【第{$line}行】货号不能为空！";
	            return $result;
	        }else{
	            $goods_sn = $goods_sn_list[$key];
	        }
	        if(empty($goods_cost_list[$key]) || !is_numeric($goods_cost_list[$key])){
	            $result['error'] = "【第{$line}行】成本价不合法，请填写大于0的数字！";
	            return $result;
	        }else{
	            $goods_cost = $goods_cost_list[$key]/1;
	            if($bill_type=='WH'){
			        if(empty($goods_shijia_list[$key]) || !is_numeric($goods_shijia_list[$key])){
			            $result['error'] = "【第{$line}行】销售价不合法，请填写大于0的数字！";
			            return $result;
			        }else{
                        $goods_shijia = $goods_shijia_list[$key]/1;
			        }    

	            }	           
	        }
        
            if($bill_type!='WH'){
                $goods_shijia = $goods_cost;
            }
      
 
            if($bill_type=='WH' && !empty($bill_info['from_bill_no'])){
                $batch_sn = !empty($batch_sn_list[$key]) ? $batch_sn_list[$key] : $bill_info['bill_no'];
                if(!empty($qty_limit_list[$key]) &&  $goods_qty > $qty_limit_list[$key] ){
		            $result['error'] = "【第{$line}行】数量不能超过销售出库数量".$qty_limit_list[$key];
		            return $result;                	
                }
                if(!empty($batch_sn_list[$key])){
	                $from_bill_info = $billModel->getBill_Info($batch_sn_list[$key]);	                
	                if(!empty($from_bill_info['supplier_id'])){
	                    $supplier_id = $from_bill_info['supplier_id'];
	                }
	            }    
            } 


	        if(empty($in_warehouse_list[$key])){
	            $result['error'] = "【第{$line}行】入库仓库不能为空！";
	            return $result;
	        }else{
	            $in_warehouse_id = $in_warehouse_list[$key]/1;
	        }
            


	        //验证重复添加
	        $count = $billGoodsModel->getCount(array('bill_id'=>$bill_info['id'],'goods_sn'=>$goods_sn));
	        if($count>0){
	            $result['error'] = "【第{$line}行】货品编号{$goods_sn}已添加过！";
	            return $result;
	        }
	        $data = array(
	            'bill_id'=>$bill_info['id'],
	            'goods_sn'=>$goods_sn,//货号
	            'supplier_id'=>$supplier_id,//供应商
	            'in_warehouse_id'=>$in_warehouse_id,//入库仓库
	            'batch_sn'=>$batch_sn,//批次
	            'num'=>$goods_qty,//入库数量
	            'cost'=>$goods_cost,//成本价 
	            'shijia'=>$goods_shijia,//成本价
	        );    	        
	        $result['data'][] = $data;
	    }
	   
	    if(empty($result['data'])){
	        $result['error'] = "提交表单数据不能为空！";
	        return $result;
	    }
	    $result['success'] = 1;
	    return $result;
	}
	/**
	 * 入库单单据明细添加
	 * @param unknown $params
	 */
	public function insertBillGoodsCK($params){
	    $result = array("success"=>0,'error'=>'');
	    $bill_id = _Request::getInt("id");
	    $billModel = new MaterialBillModel($bill_id,21);
	    $billView = new MaterialBillView($billModel);
	    $bill_info = $billModel->getDataObject();
	    if(empty($bill_info)){
	        $result['error'] = "参数错误！";
	        Util::jsonExit($result);
	    }
	    $bill_type = $billView->get_bill_type();
	    $bill_status = $billView->get_bill_status();
	    if($bill_status!=1){
	        $result['error'] = "单据不是保存状态！";
	        Util::jsonExit($result);
	    }
	    
	     
	    try{
	        $billGoodsModel = new MaterialBillGoodsModel(21);
	        
	        $pdolist[21] = $billGoodsModel->db()->db();
	         
	        Util::beginTransaction($pdolist);
	        	        
	        $res = $this->buildBillGoodsListCK($bill_info,$bill_type);
	        
	        if($res['success']==0){
	            throw new Exception($res['error']);
	        }
	        $billGoodsList = $res['data'];	        
	        
	        foreach ($billGoodsList as $newdo){
	            $billGoodsModel->saveData($newdo,array());
	        }
	        	
	        Util::commitTransaction($pdolist);
	        	
	        $result['success'] = 1;
	        Util::jsonExit($result);
	        
	    }catch (Exception $e){
	        $msg = $e->getMessage();
	        Util::rollbackExit($msg,$pdolist);
	    }
	     
	}
	/**
	 * 格式化Post提交的数据，返回可以直接插入至material_bill_goods表的数据
	 * @param unknown $bill_type
	 * @return multitype:number string multitype:
	 * 返回 ：
	 */
	protected function buildBillGoodsListCK($bill_info,$bill_type){
	    $result = array("success"=>0,'error'=>'','data'=>array());
	    $inventory_id_list = _Post::getList("inventory_id");
	    $goods_sn_list = _Post::getList("goods_sn");
	    $goods_qty_list = _Post::getList("goods_qty");
	    $goods_shijia_list = _Post::getList("goods_shijia");
	
	    $line = 0;
	    $billGoodsModel = new MaterialBillGoodsModel(21);
	    $inventoryModel = new MaterialInventoryModel(21);
	    foreach ($goods_qty_list as $key=>$goods_qty){
	        $line++;
	        if(empty($goods_qty)){
	            continue;
	        }else if(!is_numeric($goods_qty)){
	            $result['error'] = "【第{$line}行】数量不合法，请填写大于0的数字！";
	            return $result;
	        }
	        if(empty($inventory_id_list[$key])){
	            $result['error'] = "【第{$line}行】库存查询失败！";
	            return $result;
	        }else{
	            $inventory_id = $inventory_id_list[$key];
	        }
	        if(empty($goods_sn_list[$key])){
	            $result['error'] = "【第{$line}行】货号不能为空！";
	            return $result;
	        }else{
	            $goods_sn = $goods_sn_list[$key];
	        }
	        if(empty($goods_shijia_list[$key]) || !is_numeric($goods_shijia_list[$key])){
	            $result['error'] = "【第{$line}行】销售价不合法，请填写大于0的数字！";
	            return $result;
	        }else{
	            $goods_shijia = $goods_shijia_list[$key]/1;
	        }	        
	        //验证重复添加
	        $count = $billGoodsModel->getCount(array('bill_id'=>$bill_info['id'],'goods_sn'=>$goods_sn));
	        if($count>0){
	            $result['error'] = "【第{$line}行】货品编号{$goods_sn}已添加过！";
	            return $result;
	        }
	        //查询库存；
	        $inventoryInfo = $inventoryModel->getInventoryInfo("*","id={$inventory_id}");
	        if(empty($inventoryInfo)){
	            $result['error'] = "【第{$line}行】库存查询失败！";
	            return $result;
	        }else if($inventoryInfo['inventory_qty'] < $goods_qty){
	            $result['error'] = "【第{$line}行】出库数量不能大于库存数量！";
	            return $result;
	        }	        
	        $goods_cost = $inventoryInfo['cost'];
	        $data = array(
	            'bill_id'=>$bill_info['id'],
	            'goods_sn'=>$goods_sn,//货号
	            'supplier_id'=>$inventoryInfo['supplier_id'],//供应商
	            'out_warehouse_id'=>$inventoryInfo['warehouse_id'],//出库仓库
	            'batch_sn'=>$inventoryInfo['batch_sn'],//批次
	            'num'=>$goods_qty,//出库数量
	            'cost'=>$inventoryInfo['cost'],//成本价
	            'shijia'=>$goods_shijia,//销售价
	            'inventory_id'=>$inventoryInfo['id'],
	        );
	        $result['data'][] = $data;
	    }
	     
	    if(empty($result['data'])){
	        $result['error'] = "提交表单数据不能为空！";
	        return $result;
	    }
	    $result['success'] = 1;
	    return $result;
	}
	/**
	 *	添加采购入库单
	 */
	public function addRK ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('material_bill_info_rk.html',array(
			'view'=>new MaterialBillView(new MaterialBillModel(21))
		));
		$result['title'] = '添加入库单';
		Util::jsonExit($result);
	}
	/**
	 *	采购入库单
	 */
	public function insertRK ($params)
	{
	    $result = array('success' => 0,'error' =>'');	
	    $olddo = array();
	    $newdo = array(	        
	        'warehouse_id'=>_Post::getInt('warehouse_id'),
	        'supplier_id'=>_Post::getInt('supplier_id'),
	        'department_id'=>_Post::getInt('department_id'),
	        'bill_no'=>uniqid(),
	        'bill_type'=>_Post::get('bill_type'),
	        'bill_note'=>_Post::getString('bill_note'),
	        'create_user'=>$_SESSION['userName'],
	        'create_time'=>date('Y-m-d H:i:s'),
	        'from_bill_no'=>_Post::getString('from_bill_no'),
	    );
	    $res = $this->checkData($newdo);
	    if($res['success']==0){
	        $result['error'] = $res['error'];
	        Util::jsonExit($result);
	    }
	    $newdo = $res['data'];
	    $newmodel =  new MaterialBillModel(22);
	    try{
	        $pdolist[22] = $newmodel->db()->db();
	        Util::beginTransaction($pdolist);
	
	        $insert_id = $newmodel->saveData($newdo,$olddo);
	        $newmodel = new MaterialBillModel($insert_id,22);
	        $bill_no = $newmodel->createBillNo($insert_id,$newdo['bill_type']);
	        $newmodel->setValue("bill_no",$bill_no);
	        $newmodel->save();
	
	        Util::commitTransaction($pdolist);
	       
	        $result['success'] = 1;
	        $result['bill_id'] = $insert_id;
	        $result['bill_no'] = $bill_no;
	        Util::jsonExit($result);
	    }catch (Exception $e){
	        $error = "添加失败!".$e->getMessage();
	        Util::rollbackExit($error,$pdolist);
	    }
	}
	
	/**
	 *	添加出库单
	 */
	public function addCK ()
	{
	    $result = array('success' => 0,'error' => '');
	    $result['content'] = $this->fetch('material_bill_info_ck.html',array(
	        'view'=>new MaterialBillView(new MaterialBillModel(21))
	    ));
	    $result['title'] = '添加出库单';
	    Util::jsonExit($result);
	}
	/**
	 *	添加保存出库单
	 */
	public function insertCK($params)
	{
	    $result = array('success' => 0,'error' =>'');
	
	    $olddo = array();
	    $newdo = array(
	        'warehouse_id'=>_Post::getInt('warehouse_id'),
	        'supplier_id'=>_Post::getInt('supplier_id'),
	        'department_id'=>_Post::getInt('department_id'),
	        'bill_no'=>uniqid(),
	        'bill_type'=>_Post::get('bill_type'),
	        'bill_note'=>_Post::getString('bill_note'),
	        'create_user'=>$_SESSION['userName'],
	        'create_time'=>date('Y-m-d H:i:s'),
	    );
	    $res = $this->checkData($newdo);
	    if($res['success']==0){
	        $result['error'] = $res['error'];
	        Util::jsonExit($result);
	    }
	    $newdo = $res['data'];
	    $newmodel =  new MaterialBillModel(22);
	    try{
	        $pdolist[22] = $newmodel->db()->db();
	        Util::beginTransaction($pdolist);
	
	        $insert_id = $newmodel->saveData($newdo,$olddo);
	        $newmodel = new MaterialBillModel($insert_id,22);
	        $bill_no = $newmodel->createBillNo($insert_id,$newdo['bill_type']);
	        $newmodel->setValue("bill_no",$bill_no);
	        $newmodel->save();
	
	        Util::commitTransaction($pdolist);
	
	        $result['success'] = 1;
	        $result['bill_id'] = $insert_id;
	        $result['bill_no'] = $bill_no;
	        Util::jsonExit($result);
	    }catch (Exception $e){
	        $error = "添加失败!".$e->getMessage();
	        Util::rollbackExit($error,$pdolist);
	    }
	}
	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$tab_id = _Request::getInt("tab_id");
		$result = array('content' => '','title'=>'编辑');
				
		$view = new MaterialBillView(new MaterialBillModel($id,21));
		$bill_status = $view->get_bill_status();
		$bill_type   = $view->get_bill_type();
		if($bill_status!=1){
		    $result['content'] = "单据状态不是已保存状态！";
		    Util::jsonExit($result);
		}
		$show_tpl = 'material_bill_info.html';
		if($view->get_bill_cat()==1){
		    $show_tpl = 'material_bill_info_rk.html';
		}else if($view->get_bill_cat()==2){
		    $show_tpl = 'material_bill_info_ck.html';
		}else{
		    $result['content'] = "不支持单据类型：{$bill_type}";
		    Util::jsonExit($result);
		}
		
		$result['content'] = $this->fetch($show_tpl,array(
			'view'=>$view,
			'tab_id'=>$tab_id
		));
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = _Request::getInt('id');
		$model=new MaterialBillModel($id,21);
		$view = new MaterialBillView($model);
		$total_cost=$model->getTotal($id);		
		$bill_type = $view->get_bill_type();
		$show_tpl = 'material_bill_show.html';		
		$this->render($show_tpl,array(
			'view'=>$view,
			'bar'=>Auth::getViewBar(),
			'total_cost'=>$total_cost
		));
	}

	
	/**
	 * 校验数据
	 * @param unknown $data
	 * @return multitype:number string
	 */
	protected function checkData($data){
	    $result = array('success'=>0,'error'=>'');
	    /*if(empty($data['warehouse_id'])){
	        $result['error'] = "请选择仓库";
	        return $result;
	    }
	    if(empty($data['supplier_id'])){
	        $result['error'] = "请选择供应商";
	        return $result;
	    }*/
	    if(empty($data['id']) && empty($data['bill_type'])){
	        $result['error'] = "请选择单据类型";
	        return $result;
	    }

	    if(in_array($data['bill_type'],['WL','WT','WY']) && empty($data['supplier_id'])){
	        $result['error'] = "供应商不能为空";
	        Util::jsonExit($result);	    	
	    }
	    if($data['bill_type']=='WH' && empty($data['supplier_id']) && empty($data['from_bill_no'])){
	        $result['error'] = "销售出库单必须选择销售出库单或者供应商";
	        Util::jsonExit($result);	    	
	    }	    	    
	    $result['success'] = 1;
	    $result['data'] = $data;
	    return $result;
	}

	/**
	 *	updateRK，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');
		$id = _Post::getInt('id');
		
		
		$newmodel =  new MaterialBillModel($id,22);

		$olddo = $newmodel->getDataObject();
        if($newmodel->getBillCat($olddo['bill_type'])==1){
            $newdo = array(
                'id'=>$olddo['id'],
                'warehouse_id'=>_Post::getInt('warehouse_id'),
                'supplier_id'=>_Post::getInt('supplier_id'),
                'bill_note'=>_Post::getString('bill_note'),
            );
        }else if($newmodel->getBillCat($olddo['bill_type'])==2){
            $newdo = array(
                'id'=>$olddo['id'],
                'warehouse_id'=>_Post::getInt('warehouse_id'),
                'supplier_id'=>_Post::getInt('supplier_id'),
                'department_id'=>_Post::getInt('department_id'),
                'bill_note'=>_Post::getString('bill_note'),
            );
        }else{
            $result['error'] = "不支持单据类型：{$olddo['bill_type']}";
            Util::jsonExit($result);
        }
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;	
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	deleteBillGoods，删除
	 */
	public function deleteBillGoods ($params)
	{
		$result = array('success' => 0,'error' => '');
		$bill_id = _Request::getInt('bill_id');
		$id = _Request::getInt("id");
		$billModel = new MaterialBillModel($bill_id,21);
		$billInfo = $billModel->getDataObject();
		if($billInfo['bill_status']!=1){
		    $result['error'] = "单据不是保存状态，不能删除！";
		    Util::jsonExit($result);
		}
		$model = new MaterialBillGoodsModel($id,22);		
		$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

	/**
	 * 单据审核通过
	 * @param unknown $params
	 */
	public function checkBillPass($params){
	    $result = array('success' => 0,'error' => '');
	    $model = new MaterialBillModel(21);
	    $bill_id = _Request::getInt('id');
	    $res = $model->checkBillPass($bill_id);
	    if($res['success']==1){
	        $result['success'] = 1;
	        Util::jsonExit($result);
	    }else{
	        $result['error'] = $res['error'];
	        Util::jsonExit($result);
	    }
	    
	}
	
	/**
	 * 审核取消
	 * @param unknown $params
	 */
	public function checkBillCancel($params){
	    $result = array('success' => 0,'error' => '');
	    
	    $bill_id = _Request::getInt('id');
	    $billModel = new MaterialBillModel($bill_id,21);
	    $billInfo = $billModel->getDataObject();
	    if($billInfo['bill_status']!=1){
	        $result['error'] = "单据不是保存状态，不能取消！";
	        Util::jsonExit($result);
	    }
	    
	    $billModel->setValue('bill_status',3);//3已取消
	    $billModel->setValue('check_user',$_SESSION['userName']);
	    $billModel->setValue('check_time',date("Y-m-d H:i:s"));
	    
	    $res = $billModel->save();
	    if($res !== false){
	        $result['success'] = 1;
	    }else{
	        $result['error'] = "取消失败";
	    }
	    Util::jsonExit($result);
	}

	/**
	 * 单据打印
	 * 
	 */



	public function printBill(){
        $id = _Request::get('id');
        $billModel = new MaterialBillModel($id,21);
        $data = $billModel->getDataObject();
        $view = new MaterialBillView(new MaterialBillModel($id,21)); //获取单头信息
        if(in_array($data['bill_type'],array('WT','WL','WY','WH'))){
            $this->printReceiptBill($id,$data,$view,$billModel);
        }elseif(in_array($data['bill_type'],array('WB','WP','WC','WK'))){
            $this->printSalesBill($id,$data,$view,$billModel);
        }else{
            //print_r($data);exit;
            $result['error'] = "不支持单据类型：".$data['bill_type'];
            Util::jsonExit($result);
        }

    }


	public function printReceiptBill($id,$data,$view,$billModel){
		$where = array('bill_id'=>$id);	    
		$result = $billModel->billGoodsPageList($where,1,1000,false);
		$detailData = $result['data'];		
		$sum_num = 0;
		$sum_cost = 0;
		$sum_shijia = 0;
		for($i = 0 ; $i < count($detailData); $i++){
			$shijia_sum_price = sprintf('%.2f', $detailData[$i]['shijia'] *  $detailData[$i]['num']);
			$cost_sum_price = sprintf('%.2f',$detailData[$i]['cost'] *  $detailData[$i]['num']);
			$sum_num = $sum_num + $detailData[$i]['num'];
			$sum_cost = $sum_cost + round($detailData[$i]['cost']*$detailData[$i]['num'],2);
			$sum_shijia = $sum_shijia + round($detailData[$i]['shijia']*$detailData[$i]['num'],2);
			$detailData[$i]['sum_shijia'] = $shijia_sum_price;
			$detailData[$i]['sum_cost'] = $cost_sum_price;
		}
		$total = array('num'=>$sum_num,'shijia'=>$sum_shijia,'cost'=>$sum_cost);
		$this->render('material_print.html', array(
			'data' => $data,
			'view' => $view,
			'goods_info' => $detailData,
			'billView'=>new MaterialBillView($billModel),
			'total' => $total
		));
	}

	/**
	 * 销售打印单
	 */
	public function printSalesBill($id,$data,$view,$billModel){

		$where = array('bill_id'=>$id);	    
		$result = $billModel->billGoodsSumPageList($where,1,1000,false);
		$detailData = $result['data'];
		$sum_sales_price = 0;
		$sum_num = 0;
		for($i = 0 ; $i < count($detailData); $i++){
			$sum_sales_price = sprintf('%.2f', ($sum_sales_price+$detailData[$i]['sales_price']));
			$sum_num = $sum_num + $detailData[$i]['num'];
		}
		$total = array('sales_sum'=>$sum_sales_price,'sum_num'=>$sum_num);
		$this->render('material_sales_print.html', array(
			'data' => $data,
			'view' => $view,
			'goods_info' => $detailData,
			'billView'=>new MaterialBillView($billModel),
			'total' => $total
		));
	}

	/**
	 * 销售打印单
	 */
	public function getBillList(){
        $bill_type = _Request::getString("bill_type");
		$billModel = new MaterialBillModel(21);   
		$where = ['bill_type'=>$bill_type];
		$result = $billModel->pageList($where,1,1000);
		if(!empty($result['data'])){
			Util::jsonExit(array('error'=>0,'data'=>array_column($result['data'],'bill_no')));
		}else{
			Util::jsonExit(array('error'=>'1'));
		}
		
	}

    //获取单个
    public function getBillInfo(){
        $bill_no = _Request::getString("bill_no");
        $billModel = new MaterialBillModel(21);
        $result = $billModel->getBill_Info($bill_no);
        if(!empty($result)){
            Util::jsonExit(array('error'=>0,'department_id'=>$result['department_id'],'warehouse_id'=>$result['warehouse_id']));
        }else{
            Util::jsonExit(array('error'=>'1'));
        }

    }

}

?>