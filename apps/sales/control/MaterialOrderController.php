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
class MaterialOrderController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('printBill','printBillMaterial','printBillGift');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
	    $model = new MaterialOrderModel(210);
		$this->render('material_order_search_form.html',array(
		    'bar'=>Auth::getBar(),
		    'view'=>new MaterialOrderView($model)
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
		    'bill_status'=>_Request::getInt("bill_status"),		    
		    'goods_sn' => _Request::getString("goods_sn"),
		    'goods_name' => _Request::getString("goods_name"),
		    'style_sn' => _Request::getString("style_sn"),
		    'style_name' => _Request::getString("style_name"),		   
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
		
		$model = new MaterialOrderModel(210);
		$data = $model->pageList($where,$page,10,false,$dow_info);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'material_order_search_page';
		$this->render('material_order_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
		    'view'=>new MaterialOrderView($model),
		    'show_detail' => $args['show_detail'], 
		));
	}
	
	/**
	 *	单据明细列表
	 */
	public function searchOrderGoods ($params)
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
	    $model = new MaterialOrderModel($bill_id,210);
	    $data = $model->billGoodsPageList($where,$page,10,false);
        foreach ($data['data'] as $key => $v) {
        	$img_array = $model->getStyle_Img($v['style_sn']);
        	$data['data'][$key]['style_img'] = !empty($img_array) ? $img_array['thumb_img'] : '';
        }	    
	    $pageData = $data;
	    $pageData['filter'] = $args;
	    $pageData['jsFuncs'] = 'material_order_goods_search_page';
	    $this->render('material_order_goods_search_list.html',array(
	        'pa'=>Util::page($pageData),
	        'page_list'=>$data,
	        'billView'=>new MaterialOrderView($model)
	         
	    ));
	}
	/**
	 * 添加单据明细index
	 */
	public function addOrderGoods(){
	    $result = array('success' => 0,'error' => '','title'=>'添加商品');
	    $bill_id = _Request::getInt('_id');
	    $billModel = new MaterialOrderModel($bill_id,210);
	    $billView = new MaterialOrderView($billModel);
	   
	    $bill_status = $billView->get_bill_status();
	    if($bill_status !=1){
	        $result['content'] = "单据不是已保存状态，不能操作！";
	        Util::jsonExit($result);
	    }
		if($_SESSION['userName']!=$billView->get_create_user()){
		    $result['content'] = "制单人不是本人不能添加！";
		    Util::jsonExit($result);
		}	   
	    $show_tpl = 'material_order_goods_add.html';
	   
	    //echo $show_tpl;
	    $result['content'] = $this->fetch($show_tpl,array(
	        'view'=>$billView
	    ));
	    Util::jsonExit($result);
	}
	/**
	 *	添加单据明细search
	 */
	public function addOrderGoodsSearch ($params)
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
	        'goods_type' => _Request::getInt('goods_type',1),
	    );
	    $bill_id = _Request::getInt("_id");
	    $billModel = new MaterialOrderModel($bill_id,210);
	    $billView = new MaterialOrderView($billModel);
	  
	    $page = _Request::getInt("page",1);
	    $where = $args;
	    $where['goods_status'] = 1;//;
	    $where['order_by_field'] = 'goods_sn'; 
	    $model = new MaterialOrderModel(210);	    
	    $data = $model->goodspageList($where,$page,25,false,$bill_id);
	    //echo "<pre>"; print_r($data);
        foreach ($data['data'] as $key => $v) {
        	$img_array = $model->getStyle_Img($v['style_sn']);
        	$data['data'][$key]['style_img'] = !empty($img_array) ? $img_array['thumb_img'] : '';
        }
	    $pageData = $data;
	    $pageData['filter'] = $args;
	    $pageData['jsFuncs'] = 'material_order_goods_add_list_page';
	    $this->render('material_order_goods_add_list.html',array(
	        'pa'=>Util::page($pageData),
	        'page_list'=>$data,
	        'billView'=>$billView,
	    ));
	}
	
	/**
	 * 申请单单据明细添加
	 * @param unknown $params
	 */
	public function insertOrderGoods($params){
	    
	    $result = array("success"=>0,'error'=>'');
	    
	    $bill_id = _Request::getInt("id");
	    $billModel = new MaterialOrderModel($bill_id,210);
	    $billView = new MaterialOrderView($billModel);
	    $bill_info = $billModel->getDataObject();
	    if(empty($bill_info)){
	        $result['error'] = "参数错误！";
	        Util::jsonExit($result);
	    }
	    
	    $bill_status = $billView->get_bill_status();
	    if($bill_status!=1){
	        $result['error'] = "单据不是保存状态！";
	        Util::jsonExit($result);
	    }
	    
	    try{
	        $billGoodsModel = new MaterialOrderGoodsModel(210);
	        
	        $pdolist[210] = $billGoodsModel->db()->db();
	        
	        Util::beginTransaction($pdolist);
	        
	        $res = $this->buildOrderGoodsList($bill_info);
	        if($res['success']==0){
	            throw new Exception($res['error']);
	        }
	        $billGoodsList = $res['data'];
    	    foreach ($billGoodsList as $newdo){
    	        $billGoodsModel->saveData($newdo,array());
    	        $result['error'] = $newdo['goods_num'];
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
	protected function buildOrderGoodsList($bill_info){
	    $result = array("success"=>0,'error'=>'','data'=>array());

	    $goods_sn_list = _Post::getList("goods_sn");
	    $goods_qty_list = _Post::getList("goods_qty");
	    $goods_price_list = _Post::getList("goods_price");
	    $min_qty_list = _Post::getList("min_qty");
	    $pack_qty_list = _Post::getList("pack_qty");
	    
	    $line = 0;
	    $billGoodsModel = new MaterialOrderGoodsModel(210);
	    foreach ($goods_qty_list as $key=>$goods_qty){
	        $line++;

	        $tip_title = "【第{$line}行】";
	        
	        if(empty($goods_qty)){
	            continue;
	        }else if(!is_numeric($goods_qty)){
	            $result['error'] = $tip_title."数量不合法，请填写大于0的数字！";
	            return $result;
	        }
	        $min_qty  = $min_qty_list[$key];  
	        $pack_qty = $pack_qty_list[$key];
	        if($goods_qty< $min_qty){
	            $result['error'] = $tip_title."添加数量不能小于起订数量！";
	            return $result;
	        }
	        if($goods_qty%$pack_qty !=0){
	            $result['error'] = $tip_title."添加数量必须为装箱量的倍数！";
	            return $result;
	        }
	        if(empty($goods_sn_list[$key])){
	            $result['error'] = $tip_title."货号不能为空！";
	            return $result;
	        }else{
	            $goods_sn = $goods_sn_list[$key];
	        }
	        if(empty($goods_price_list[$key]) || !is_numeric($goods_price_list[$key])){
	            $result['error'] = $tip_title."参考售价不合法，请填写大于0的数字！";
	            return $result;
	        }else{
	           $goods_price = $goods_price_list[$key]/1;
	        }
	       
	        //验证重复添加
	        $count = $billGoodsModel->getCount(array('bill_id'=>$bill_info['id'],'goods_sn'=>$goods_sn));
	        if($count>0){
	            $result['error'] = $tip_title."货品编号{$goods_sn}已添加过！";
	            return $result;
	        }
	        $data = array(
	            'bill_id'=>$bill_info['id'],
	            'goods_sn'=>$goods_sn,//货号	          
	            'goods_num'=>$goods_qty,//入库数量
	            'goods_price'=>$goods_price,//成本价 
	           
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
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('material_order_add.html',array(
			'view'=>new MaterialOrderView(new MaterialOrderModel(210))
		));
		$result['title'] = '添加申请单';
		Util::jsonExit($result);
	}
	/**
	 *	采购入库单
	 */
	public function insert ($params)
	{
	    $result = array('success' => 0,'error' =>''); 
	    $olddo = array();
	    $newdo = array(       
	        'department_id'=>_Post::getInt('department_id'),
	        'bill_no'=>uniqid(),	       
	        'bill_note'=>_Post::getString('bill_note'),
	        'create_user'=>$_SESSION['userName'],
	        'address'=>_Post::getString('address'),
	        'address_checked'=>_Post::getInt('address_checked'),
	        'create_time'=>date('Y-m-d H:i:s'),
	    );
	    $res = $this->checkData($newdo);
	    if($res['success']==0){
	        $result['error'] = $res['error'];
	        Util::jsonExit($result);
	    }
	    $newdo = $res['data'];
	    $newmodel =  new MaterialOrderModel(210);
	    try{
	        $pdolist[210] = $newmodel->db()->db();
	        Util::beginTransaction($pdolist);
	
	        $insert_id = $newmodel->saveData($newdo,$olddo);
	        $newmodel = new MaterialOrderModel($insert_id,210);
	        $bill_no = $newmodel->createBillNo($insert_id,"O");
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
				
		$view = new MaterialOrderView(new MaterialOrderModel($id,210));
		$bill_status = $view->get_bill_status();
		
		if($bill_status!=1){
		    $result['content'] = "单据状态不是已保存状态！";
		    Util::jsonExit($result);
		}
		//echo $_SESSION['userName']; echo $view->get_create_user();
		if($_SESSION['userName']!=$view->get_create_user()){
		    $result['content'] = "制单人不是本人不能编辑！";
		    Util::jsonExit($result);
		}		
		$show_tpl = 'material_order_info.html';
		
		
		
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
		$model=new MaterialOrderModel($id,210);
		$view = new MaterialOrderView($model);
		
		$where = array('bill_id'=>$id);
		$result = $model->billGoodsPageList($where,1,9999,false);
		$goods_sum = array('amount'=>0,'material_amount'=>0,'gift_amount'=>0,'goods_num'=>0);
		foreach ($result['data'] as $k=>$goods){
		    if($goods['goods_type']==1){
		        $goods_sum['material_amount'] += $goods['goods_price'] * $goods['goods_num'];
		    }else if($goods['goods_type'] == 2){
		        $goods_sum['gift_amount'] += $goods['goods_price'] * $goods['goods_num'];
		    }
		    $goods_sum['goods_num'] += $goods['goods_num'];
		}
		$goods_sum['amount'] = $goods_sum['material_amount'] + $goods_sum['gift_amount'];
		$show_tpl = 'material_order_show.html';		
		$this->render($show_tpl,array(
			'view'=>$view,
			'bar'=>Auth::getViewBar(),
			'goods_sum'=>$goods_sum
		));
	}

	
	/**
	 * 校验数据
	 * @param unknown $data
	 * @return multitype:number string
	 */
	protected function checkData($data){
	    $result = array('success'=>0,'error'=>'');
	    if(empty($data['address'])){
	        $result['error'] = "邮寄地址不能为空";
	        return $result;
	    }
	    if(empty($data['address_checked'])){
	        $result['error'] = "请确认地址";
	        return $result;
	    }
	    unset($data['address_checked']);
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
		
		
		$newmodel =  new MaterialOrderModel($id,210);

		$olddo = $newmodel->getDataObject();
        
        $newdo = array(
            'id'=>$olddo['id'],               
            'department_id'=>_Post::getInt('department_id'),
            'bill_note'=>_Post::getString('bill_note'),
            'address'=>_Post::getString('address'),
            'address_checked'=>_Post::getInt('address_checked'),
        );
        $res = $this->checkData($newdo);
        if($res['success']==0){
            $result['error'] = $res['error'];
            Util::jsonExit($result);
        }
        $newdo = $res['data'];
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
	 *	deleteOrderGoods，删除
	 */
	public function deleteOrderGoods ($params)
	{
		$result = array('success' => 0,'error' => '');
		$bill_id = _Request::getInt('bill_id');
		$id = _Request::getInt("id");
		$billModel = new MaterialOrderModel($bill_id,210);
		$billInfo = $billModel->getDataObject();
		if($billInfo['bill_status']!=1){
		    $result['error'] = "单据不是保存状态，不能删除！";
		    Util::jsonExit($result);
		}
		if($_SESSION['userName']!=$billInfo['create_user']){
		    $result['error'] = "制单人不是本人不能删除！";
		    Util::jsonExit($result);
		}

		$model = new MaterialOrderGoodsModel($id,210);		
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
	public function checkOrderPass($params){
	    $result = array('success' => 0,'error' => '');
	    $model = new MaterialOrderModel(210);
	    $bill_id = _Request::getInt('id');
	    $res = $model->checkOrderPass($bill_id);
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
	public function checkOrderCancel($params){
	    $result = array('success' => 0,'error' => '');
	    
	    $bill_id = _Request::getInt('id');
	    $billModel = new MaterialOrderModel($bill_id,210);
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
	 * 赠品发货
	 * @param unknown $params
	 */
	public function sendOrder($params){
	    $result = array('success' => 0,'error' => '');
	    
	    $bill_id = _Request::getInt('id');
	    $billModel = new MaterialOrderModel($bill_id,210);
	    $res = $billModel->sendGoods($bill_id,4);
	    if($res === true){
	        $result['success'] = 1;
	    }else{
	        $result['error'] = "操作失败：".$res;
	    }
	    Util::jsonExit($result);
	}


	/**
	 * 物料发货
	 * @param unknown $params
	 */
	public function sendGoods($params){
	    $result = array('success' => 0,'error' => '');
	    
	    $bill_id = _Request::getInt('id');
	    $billModel = new MaterialOrderModel($bill_id,210);
	    $res = $billModel->sendGoods($bill_id,5);
	    if($res === true){
	        $result['success'] = 1;
	    }else{
	        $result['error'] = "操作失败：".$res;
	    }
	    Util::jsonExit($result);
	}
	/**
	 * 单据打印
	 * 
	 */

	public function printBill($params){
		$id = _Request::get('id');
		$billModel = new MaterialOrderModel($id,210);
		$data = $billModel->getDataObject();
		$view = new MaterialOrderView(new MaterialOrderModel($id,210)); //获取单头信息
		$where = array('bill_id'=>$id);	 
		if(!empty($params['goods_type'])){
		    $where['goods_type'] = $params['goods_type'];
		}   
		$result = $billModel->billGoodsPageList($where,1,1000,false);
		$detailData = $result['data'];
		$goods_sum['goods_num'] = 0;
		$goods_sum['goods_price'] = 0;
		$goods_sum['goods_amount'] = 0;
		$goods_list = array();
		foreach ($result['data'] as $k=>$goods){
		    $goods['goods_amount'] = $goods['goods_num'] * $goods['goods_price'];
		    $goods_list[] = $goods;
		    $goods_sum['goods_num'] += $goods['goods_num'];
		    $goods_sum['goods_amount'] += $goods['goods_amount'];
		}
		$this->render('material_print.html', array(
			'data' => $data,
			'view' => $view,
			'goods_list' => $goods_list,
			'goods_sum' => $goods_sum,
		));
	}

	public function printBillMaterial(){
	    $this->printBill(array("goods_type"=>1));
	}
	
	public function printBillGift(){
	    $this->printBill(array("goods_type"=>2));
	}
	
}

?>