<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 11:04:15
 *   @update	:
 *
	质检订单号限制：已配货状态 未发货状态 才能质检
	质检通过：需要验证是否生成了销售单  发货状态改为允许发货
	质检未通过：销售单取消 配货状态改为配货中

	质检未通过：订单状态不做任何限制  2015/6/25 星期四
	质检通过：  检查状态 调用公共方法  修改注意下  VerifyOrderStatus
 *  -------------------------------------------------
 */
class OrderFqcController extends CommonController
{
	protected $smartyDebugEnabled = true;
	public $type_pro = array(
		'1' =>array('1'=>'核实','2'=>'刻字','3'=>'等待发货','4'=>'其他'),
		'2' =>array('1'=>'未作帐','2'=>'配错','3'=>'少配','4'=>'多配' ,'5'=>'金额销错','6'=>'为付完款','7s'=>'其他'),
		'3' =>array('1'=>'划痕','2'=>'翻新','3'=>'货不对图','4'=>'证书与图片不符','5'=>'其他')
		);
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('order_fqc_search_form.html',array(
			'bar'=>Auth::getBar(),
		));
	}
	/**
	 *	search，列表 显示订单信息  暂时没用
	 */
	public function search1 ($params)
	{

		$order_sn = _Post::getString('order_sn');
		$keys=array('order_sn');
		$vals=array($order_sn);
		$orderInfo=ApiModel::sales_api('GetOrderInfoRow',$keys,$vals);
		$goodsInfo=ApiModel::sales_api('GetOrderInfoByOrdersn',$keys,$vals);
		$data = $orderInfo['return_msg'];
		$goods_data = $goodsInfo['return_msg']['data'];
		//取得该订单的明细id相关的货号
		$model_s = new WarehouseBillInfoSModel(22);
		$goods_rel = $model_s->getGoodsidByOrderSn($order_sn);
		$arr_order_id_goods = array();
		if ($goods_rel['error'] == 0)
		{
			$Khd_dd = $goods_rel['data'];
			//将二维数组转换为一位数组
			foreach ($Khd_dd as $val)
			{
				$arr_order_id_goods[$val['order_goods_id']] = $val['goods_id'];
			}
			//var_dump($arr_order_id_goods);var_dump($goods_data);exit;
			foreach($goods_data as $key=>$val)
			{
				if (isset($arr_order_id_goods[$val['id']]))
				{
					$val['goods_id_jxc'] = $arr_order_id_goods[$val['id']];
				}
				else
				{
					$val['goods_id_jxc'] = '';
				}
				$style_aip = ApiModel::style_api(array('style_sn'),array($val['goods_sn']),'GetStyleGallery');
				if(isset($style_aip['thumb_img']))
				{//45°图
                   $val['thumb_img'] =  $style_aip['thumb_img'];
                }
				else
				{
					$val['thumb_img'] = '';
				}
				$goods_data[$key]   = $val;
			}
			//var_dump($goods_data);exit;
		}
		else
		{
			$html ="<div class='alert alert-info'>订单号".$order_sn."销售单有问题</div>";
			Util::jsonExit($html);
		}
		#检测是否有退款操作
		$exit_tuikuan = ApiModel::sales_api('isHaveGoodsCheck',$keys,$vals);

		$CustomerSourcesModel = new CustomerSourcesModel(1);
		$sources_name = $CustomerSourcesModel->getSourceNameById($data['customer_source_id']);
		$data['order_source'] = $sources_name ;
		$this->render('order_fqc_search_list.html',array(
				'data'=>$data,
				'goods_data' => $goods_data,
				'dd'=>new DictView(new DictModel(1)),
				'bar'=>Auth::getBar(),
				'exit_tuikuan'=>$exit_tuikuan['return_msg']
			));

	}
	/**
	 * search，列表 显示订单信息
	 */
	public function search($params)
	{
	    $order_sn = _Post::getString('order_sn');
	    if (empty($order_sn)) {
	        $html = "<div class='alert alert-info'>订单号不能为空，请重新输入</div>";
	        Util::jsonExit($html);
	    }
	    $orderFqcModel = new OrderFqcModel(21);
	    $orderInfo = $orderFqcModel->getOrderInfoByOrderSn($order_sn);
	    if (! count($orderInfo) || empty($orderInfo)) {
	        $html = "<div class='alert alert-info'>订单号" . $order_sn . "不存在，请重新输入</div>";
	        Util::jsonExit($html);
	    }
	    $order_id = $orderInfo['id'];
	    // add order action end
	
	    // 已配货状态 未发货状态 才能质检
	    // 申请关闭和关闭
	    // req NEW-569 申请关闭和关闭状态fqc质检也显示信息
	    $is_close = '';
	    if ($orderInfo['apply_close'] == 1 || $orderInfo['order_status'] == 4) {
	        // $html ="<div class='alert alert-info'>订单：".$order_sn." 已申请关闭或关闭</div>";
	        // Util::jsonExit($html);
	        $is_close = 1;
	    }
	    // 已配货 5
	    // exit($data['delivery_status']);
        if ($orderInfo['delivery_status'] != 5) {
            $html = "<div class='alert alert-info'>订单号 <span style='color:red;'>" . $order_sn . "</span> 还没有配货，请先配货</div>";
            Util::jsonExit($html);
        }

        // 1未发货 4允许发货 5已到店
        if (! ($orderInfo['send_good_status'] == 1 || $orderInfo['send_good_status'] == 5 || $orderInfo['send_good_status'] == 4)) {
            $html = "<div class='alert alert-info'>订单号" . $order_sn . "发货状态错误，只有未发货和已到店的才可以质检</div>";
            Util::jsonExit($html);
        }
	    // 取得该订单的明细id相关的货号
	    $model_s = new WarehouseBillInfoSModel(22);
	    $goods_rel = $model_s->getGoodsidByOrderSn($order_sn);
	    $arr_order_id_goods = array();
	
	    $goods_data = $orderFqcModel->getOrderGoodsInfoByOrdersn($order_sn);
	    if ($goods_rel['error'] == 0) {
	        $Khd_dd = $goods_rel['data'];
	        // 将二维数组转换为一位数组
	        foreach ($Khd_dd as $val) {
	            $arr_order_id_goods[$val['order_goods_id']] = $val['goods_id'];
	        }
	
	        $goodsSnArra = array();
	        foreach ($goods_data as $key => $val) {
	            if (! empty($val['goods_sn'])) {
	                array_push($goodsSnArra, $val['goods_sn']);
	            }
	        }
	
	        $styleSnArr = array();
	        if (! empty($goodsSnArra)) {
	            $galleryInfo = $orderFqcModel->GetStyleGallery($goodsSnArra);
	            if (! empty($galleryInfo)) {
	                foreach ($galleryInfo as $key => $val) {
	                    $styleSnArr[$val['style_sn']] = $val['thumb_img'];
	                }
	            }
	        }
	
	        foreach ($goods_data as $key => $val) {
	            if (isset($arr_order_id_goods[$val['id']])) {
	                $val['goods_id_jxc'] = $arr_order_id_goods[$val['id']];
	            } else {
	                $val['goods_id_jxc'] = '';
	            }
	            $val['thumb_img'] = '';
	            if (! empty($val['goods_sn']) && isset($styleSnArr[$val['goods_sn']])) {
	                $val['thumb_img'] = $styleSnArr[$val['goods_sn']];
	            }
	            if (! isset($val['bc_id'])) {
	                $val['bc_id'] = '';
	            }
	            $goods_data[$key] = $val;
	        }
	    } else {
	        $html = $goods_rel['msg'];
	        Util::jsonExit($html);
	    }
	    $CustomerSourcesModel = new CustomerSourcesModel(1);
	    $sources_name = $CustomerSourcesModel->getSourceNameById($orderInfo['customer_source_id']);
	    $orderInfo['order_source'] = $sources_name;
	    // 获取订单的赠品信息
	    $SalesModel = new SalesModel(27);
	    $gift = $SalesModel->getOrderGiftInfo($order_id);
	    $zengpin = array(
	        'remark' => '',
	        'goods' => ''
	    );
	    if($gift){
	        $zengpin['remark'] =  $gift['remark'];
	        // edit end
	       
		        $gift_id_arr = explode(',',  $gift['gift_id']);
		        $gift_num_arr = explode(',',  $gift['gift_num']);
		        foreach ($gift_id_arr as $key => $val) {
		        	if(array_key_exists($val, $this->gifts)){
		              $zengpin['goods'] .=  $this->gifts[$val] . $gift_num_arr[$key] . '个&nbsp';
		        	}
		        }
	        
	    }
        $hidden = Util::zhantingInfoHidden($orderInfo);
	    $order_action = $orderFqcModel->getOrderActionList($order_id, $hidden);
	    $this->render('order_fqc_search_list.html', array(
	        'data' => $orderInfo,
	        'goods_data' => $goods_data,
	        'dd' => new DictView(new DictModel(1)),
	        'bar' => Auth::getBar(),
	        'exit_tuikuan' => $orderInfo['apply_return'],
	        'order_action' => $order_action,
	        'is_close' => $is_close,
	        'zengpin' => $zengpin,
            'hidden'=>$hidden
	    ));
	}
	
	/**
	 *	fqc_pass_no,质检未通过;
	 */
	public function fqc_pass_no ()
	{
		$result = array('success' => 0,'error' => '');
		$order_sn = $_REQUEST['order_sn'];
		$keys=array('order_sn');
		$vals=array($order_sn);
		$orderInfo=ApiModel::sales_api('GetOrderInfo',$keys,$vals);
		$data = $orderInfo['return_msg'];

		#已配货状态 未发货状态 才能质检
		//已配货 	5
		if ($data['delivery_status'] != 5)
		{
			$html ="<div class='alert alert-info'>订单号".$order_sn."配货状态错误，需要配货完成才可以</div>";
			$result['content'] = $html;
			Util::jsonExit($result);
		}
		//未发货 	1 已到店 5
		if (!($data['send_good_status'] == 1 || $data['send_good_status'] == 5 || $data['send_good_status'] == 4))//未发货 已到店	允许发货 1
		{
			$html ="<div class='alert alert-info'>订单号".$order_sn."发货状态错误，只有未发货、允许发货或已到店的才可以质检</div>";
			$result['content'] = $html;
			Util::jsonExit($result);
		}

		/** 获取顶级导航列表**/
		$model = new OrderFqcConfModel(21);
		$top_menu = $model->get_top_menu();
		#检查是否生成销售单
		$model_s = new WarehouseBillInfoSModel(22);
		$e_res   =$model_s->xiaoshou_exit($order_sn);
		if ($e_res<=0)//销售单不存在
		{
			$html ="<div class='alert alert-info'>订单号".$order_sn."不存在销售单</div>";
			$result['content'] = $html;
			Util::jsonExit($result);
		}
		$result['content'] = $this->fetch('order_fqc_info.html',array(
			'view'=>new OrderFqcView(new OrderFqcModel(21)),
			'order_sn' => $order_sn,'top_menu' => $top_menu
		));
		$result['title'] = '质检';
		Util::jsonExit($result);
	}
	public function check_is_fqc($order_sn)
	{
	    $saleModel = new SalesModel(27);
	    $baseOrderInfo = $saleModel->getBaseOrderInfoByOrderSn($order_sn);
	    if(empty($baseOrderInfo)){
	        return $order_sn.":订单号不存在！<br />";
	    }else if($baseOrderInfo['apply_return']==2){
	        return $order_sn.":有未完成的退款申请，不能操作<br />";
	    }else if ($baseOrderInfo['apply_close']==1){
	        return $order_sn.":审核关闭状态，不能操作<br />";
	    }else if ($baseOrderInfo['delivery_status'] != 5){//已配货 	5
	        return $order_sn.":配货状态错误，需要配货完成才可以质检<br />";
	    }else if($baseOrderInfo['send_good_status'] == 4){
	        return $order_sn.":订单已质检,不允许重复质检<br />";
	    }else if (!($baseOrderInfo['send_good_status'] == 1 || $baseOrderInfo['send_good_status'] == 5)){//未发货 已到店	1
	        return $order_sn.":发货状态错误，只有未发货和已到店才可以质检<br />";
	    }
	
	    $model_s = new WarehouseBillInfoSModel(22);
	    $e_res   =$model_s->xiaoshou_exit($order_sn);
	    if ($e_res<=0){//销售单不存在
	        return $order_sn.":不存在销售单<br />";
	    }
	
	    $baseOrderData = array('send_good_status'=>4);
	    $res = $saleModel->updateBaseOrderInfo($baseOrderData,"order_sn='{$order_sn}'");
	    if($res == false){
	        return $order_sn.":程序异常更新订单发货状态失败请联系开发人员<br />";
	    }
	
	    $newmodel =  new OrderFqcModel(22);
	    $newdo=array("order_sn"=>$order_sn,'datatime'=>date('Y-m-d H:i:s'),'is_pass'=>1,'admin'=>$_SESSION['userName']);
	    $res = $newmodel->saveData($newdo,array());
	    if($res !== false)
	    {
	        $remark = 'FQC质检通过，允许发货';
	        $saleModel->AddOrderLog($order_sn,$remark);
	    }
	    else
	    {
	        return $order_sn.':保存质检记录失败！<br />';
	    }
	    return true;
	}
	
	/**
	 *	fqc_pass,质检通过 添加质检通过记录
	 */
	public function fqc_pass ()
	{
		$order_sn		= _Post::get('order_sn');
		#将发货状态改为允许发货 允许发货 	4
		$salesModel = new SalesModel(27);
		$orderFqcModel = new OrderFqcModel(22);

		$baseOrderInfo = $salesModel->getBaseOrderInfoByOrderSn($order_sn);
		$error = false;
		if(empty($baseOrderInfo)){
		    $error = $order_sn.":订单号不存在！<br />";
		}else if($baseOrderInfo['apply_return']==2){
		    $error = $order_sn.":有未完成的退款申请，不能操作<br />";
		}else if ($baseOrderInfo['apply_close']==1){
		   $error = $order_sn.":审核关闭状态，不能操作<br />";
		}else if ($baseOrderInfo['delivery_status'] != 5){//已配货 	5
		   $error = $order_sn.":配货状态错误，需要配货完成才可以质检<br />";
		}else if($baseOrderInfo['send_good_status'] == 4){
		   $error = $order_sn.":订单已质检,不允许重复质检<br />";
		}else if (!($baseOrderInfo['send_good_status'] == 1 || $baseOrderInfo['send_good_status'] == 5)){//未发货 已到店	1
		   $error = $order_sn.":发货状态错误，只有未发货和已到店才可以质检<br />";
		}
		
		$model_s = new WarehouseBillInfoSModel(22);
		$e_res   =$model_s->xiaoshou_exit($order_sn);
		if ($e_res<=0){//销售单不存在
		    $error = $order_sn.":不存在销售单<br />";
		}		
		if($error !==false){
		    $result['error'] = $error;
		    Util::jsonExit($result);
		}
		$pdolist[22] = $orderFqcModel->db()->db();
		$pdolist[27] = $salesModel->db()->db();
		try{
		    foreach ($pdolist as $pdo){
		        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
		        $pdo->beginTransaction(); //开启事务
		    }
		}catch (Exception $e){
		    $error = "操作失败，事物回滚！提示：系统批量开启事物时发生异常！";
		    Util::rollbackExit($error,$pdolist);
		}
		try{		
    		$data = array('send_good_status'=>4);
    		$doTip = "更改订单发货状态";
    		$salesModel->updateBaseOrderInfo($data, "order_sn='{$order_sn}'");

    		$doTip = "保存质检记录";
    		$newdo=array(
    		    "order_sn"			=>$order_sn,
    		    'datatime'			=>date('Y-m-d H:i:s'),
    		    'is_pass'			=>1,
    		    'admin'				=> $_SESSION['userName']
    		);
    		$orderFqcModel->saveData($newdo,array());    		
    		$doTip = "添加订单日志";
    		$remark = "FQC质检通过，允许发货";
            $salesModel->AddOrderLog($order_sn, $remark);  
		}catch (Exception $e){
		    $msg = $doTip."失败，事物已回滚！"; 
		    Util::rollbackExit($msg);
		}
		
		try{
		    //批量提交事物
		    foreach ($pdolist as $pdo){
		        $pdo->commit();
		        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
		    }
		    $result['success'] = 1;
		    Util::jsonExit($result);
		    		
		}catch (Exception $e){
		    $error = "操作失败，事物回滚！提示：系统批量提交事物时发生异常！";
		    Util::rollbackExit($error,$pdolist);
		}
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('warehouse_bill_info.html',array(
			'view'=>new WarehouseBillView(new WarehouseBillModel($id,1))
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
		$c = new TestController();
		$c->index();exit;
		$this->render('warehouse_bill_show.html',array(
			'view'=>new WarehouseBillView(new WarehouseBillModel($id,1))
		));
	}

	/**
	 *	insert，质检不通过；1、销售单取消 2、配货状态改为配货中
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$order_sn		= _Post::get('order_sn');
		$problem_type	= _Post::get('problem_type');
		$problem		= _Post::get('problem');
		$remark			= _Post::get('remark');
		$date_time		= date('Y-m-d H:i:s',time());
		$olddo = array();
		$newdo=array(
			"order_sn"			=>$order_sn,
			"problem_type"		=>$problem_type,
			'problem'			=>$problem,
			'remark'			=>$remark,
			'datatime'			=>$date_time,
			'is_pass'			=>0,
			'admin'				=>$_SESSION['userName']
			);
		//var_dump($newdo);exit;

		#验证订单状态是否能操作  质检未通过不需验证任何订单状态    2015/6/25 星期四
		//$apisalesmodel =  ApiSalesModel::VerifyOrderStatus($order_sn);

		#取消销售单状态
		$model_s = new WarehouseBillInfoSModel(22);
		$s_res   =$model_s->cancel_info_s($order_sn,$newdo);
		if ($s_res['status'] != true)
		{
			$result['error'] = $s_res['error'];
			Util::jsonExit($result);
		}
		#配货状态修改为配货中
		$model_sale = new ApiSalesModel();
		$sale_res = $model_sale->EditOrderdeliveryStatus(array($order_sn),3,$date_time,$_SESSION['userId']);
		if ($sale_res['error'])
		{
			$result['error'] = '操作失败-配货';
			Util::jsonExit($result);
		}
		#保存质检不通过记录
		$newmodel =  new OrderFqcModel(22);
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			//修改可销售商品状态
			$goodsInfo=ApiModel::sales_api('GetOrderInfoByOrdersn',array('order_sn'),array($newdo['order_sn']));
			$goods_data = $goodsInfo['return_msg']['data'];
			$change=[];$where=[];
			foreach ($goods_data as $k => $v) {
				$where[$k]['goods_id'] = $v['goods_id'];
				$change[$k]['is_sale'] = '1';	//上架
				$change[$k]['is_valid'] = '1';	//有效
			}
			$ApiSalePolcy = new ApiSalepolicyModel();
			$ApiSalePolcy->setGoodsUnsell($change,$where);
			//变更订单的发货状态（变成未发货）
			$xxx = ApiModel::sales_api('UpdateSendGoodStatus',array('order_sn','send_good_status'),array(array($newdo['order_sn']), 1));
			/*-----------------------*/
			//获取质检未过原因 放进备注
			$OrderFqcConfModel =new OrderFqcConfModel(21);
			$type1 = $OrderFqcConfModel->select2("cat_name","id=$problem_type",$type="one");
			$type2 = $OrderFqcConfModel->select2("cat_name","id=$problem",$type="one");
			ApiSalesModel::addOrderAction($order_sn , $_SESSION['userName'] , "FQC质检未通过 原因：$type1,$type2".$remark);	//回写订单日志

			/*
			//回写订单操作日志
			$data = ApiSalesModel::GetOrderInfoByOrdersn($order_sn);
			$order_id = $data['return_msg']['id'];
			$order_status = $data['return_msg']['order_status'];
			$send_good_status = $data['return_msg']['send_good_status'];
			$order_pay_status = $data['return_msg']['order_pay_status'];
			$time = date('Y-m-d H:i:s');
			$user = $_SESSION['userName'];

			//获取质检未过原因 放进备注
			$OrderFqcConfModel =new OrderFqcConfModel(21);
			$type1 = $OrderFqcConfModel->select2("cat_name","id=$problem_type",$type="one");
			$type2 = $OrderFqcConfModel->select2("cat_name","id=$problem",$type="one");
			ApiSalesModel::addOrderAction($order_id , $order_status , $send_good_status , $order_pay_status , $time , $user , "FQC质检未通过 原因：$type1,$type2".$remark);	//回写订单日志
			*/
		}
		else
		{
			$result['error'] = '操作失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;

		$newmodel =  new WarehouseBillModel($id,2);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
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
		$model = new WarehouseBillModel($id,2);
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

	public function check(){
		$user_name = $_SESSION['userName'];
		$bill_id = _Post::getInt('bill_id');
		$model = new WarehouseBillModel(21);
		$res = $model->check($user_name,$bill_id);
		echo $res;
	}
	//获取
	public function get_protype ()
	{
		$where= $_REQUEST['id'];
		/** 获取对应二级导航列表**/
		$model = new OrderFqcConfModel(21);
		$second_menu = $model->get_second_menu($where);
		$html ="";
		foreach ($second_menu as $key=>$val)
		{
			$html .= "<option value='{$val['id']}'>{$val['cat_name']}</option>";
		}
		Util::jsonExit($html);
	}

}

?>