<?php

/**
 *  -------------------------------------------------
 *   @file		: BaseOrderInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com
 *   @date		: 2015-01-28 12:36:56
 *   @update	:
 *  -------------------------------------------------
 */
class UpdateOrderwphController extends CommonController {
	    protected $whitelist = array('downcsv','deldowncsv','wphdow');
		
    /**
     * 	index
     */
    public function index($params) {
        $this->render('update_order_whp_info.html', array('bar' => Auth::getBar()));
    }
	
    public function downcsv(){
        $title = array(
			'订单号',
            'SKU',
            '商品价格',
        );
        Util::downloadCsv("批量改价唯品会订单",$title,'');
    }
	public function deldowncsv(){
		    $title = array(
            '订单号',
            'SKU',
        );
        Util::downloadCsv("批量删除唯品会订单",$title,'');
	}
    public function wphdow(){
            $title = array(
            '订单号',
            '商品优惠金额',
        );
        Util::downloadCsv("唯品会订单维护",$title,'');
    }
	/**
	 *	add，渲染添加页面
	 */
	public function addwphupd ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('add_order_wph_upd_info.html',array(
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	add，渲染添加页面
	 */
	public function addwphdel ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('add_order_wph_del_info.html',array(
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

    /**
     *  insert，渲染添加页面
     */
    public function insertwph ()
    {
        $result = array('success' => 0,'error' => '');
        $result['content'] = $this->fetch('add_order_wph_insert_info.html',array(
        ));
        $result['title'] = '添加';
        Util::jsonExit($result);
    }
	 
	public function uploaddelwphinfo()
	{
		$result = array('success' => 0, 'error' => '');
		$upload_name = $_FILES;
		$orderModel= new BaseOrderInfoModel(27);
        if (!$upload_name) {
            $result['error'] = '文件不能为空';
            Util::jsonExit($result);
        }
		//var_dump($upload_name);die;
        if (Upload::getExt($upload_name['file_order']['name']) != 'csv') {
            $result['error'] = '请上传csv格式文件';
            Util::jsonExit($result);
        }

		$file = fopen($upload_name['file_order']['tmp_name'], 'r');
        while ($data = fgetcsv($file)) {
			foreach($data as $k => $v){
				$data[$k] = iconv("GBK","UTF-8",$v);
			}
			$data_r[]=$data;
			$data_order[] = $data[0];
        }
		$data_order=array_splice($data_order,1);
		$header_target="订单号,SKU";
		$header=implode(',',array_shift($data_r));

		if($header != $header_target){
			$result['error'] = '表头出错';
			Util::jsonExit($result);
		}
		if(!$data_r){
            $result['error'] = '无信息上传!';
            Util::jsonExit($result);
        }
		foreach($data_r as $k=>$v){
			$v = array_map('trim',$v);
			if($v[0]==''){
				$result['error'] = '第'.($k+2).'行订单号为空';
				Util::jsonExit($result);
			}
			if($v[1]==''){
				$result['error'] = '第'.($k+2).'行SKU编号为空';
				Util::jsonExit($result);
			}
		}
		$model = new baseorderinfomodel(28);
		/*验证唯品会订单*/
		//var_dump($data_order);die;
		$order_goods_info = $model->GetwphOrderinfo($data_order);
		//var_dump($order_goods_info);die;
		foreach($order_goods_info as $key => $val)
		{
	 		 if('2' == $val['order_status']){
				$result['error'] = '订单'.$val['order_sn'].'已审核不允许删除商品';
				Util::jsonExit($result);
			} 
			if('2034' != $val['customer_source_id']){
				$result['error'] = '订单'.$val['order_sn'].'非唯品会订单来源';
				Util::jsonExit($result);
			}
			if(empty($val['details_remark'])){
				$result['error'] = '订单'.$val['order_sn'].'SKU编号为空';
				Util::jsonExit($result);
			}
		}	
			//var_dump($order_goods_info);die;
			$baseArr = array();
			$sku_list = array();
			$order_snList = array();
		foreach($order_goods_info as $keys =>$vals){
			$baseArr[$vals['order_sn']] = $vals['details_remark'];
			$sku_list[$vals['details_remark']] = $vals['id'];
			$sku_goodsId_list[$vals['details_remark']] = $vals['goods_id'];
			$order_snList[$vals['order_sn']] = $vals['order_id'];
		}
		//var_dump($order_goods_info,$data_r);die;
		foreach($data_r as $key => $val)
		{
			$sku=[$val[1]];
				//var_dump($sku,$baseArr);die;
			$skuexist=in_array($sku[0],$baseArr);
			//var_dump($skuexist);die;
			if($baseArr[$val[0]] != $val[1]){
				$result['error'] = '订单'.$val[0].'和SKU'.$val[1].'不对应';
				Util::jsonExit($result);
			}
			if($skuexist == false){
				$result['error'] = '订单'.$val[0].'货品不存在';
				Util::jsonExit($result);
			}
				
			
		}
		//var_dump($baseArr,$sku_list,$order_snList);die;
		foreach($data_r as $key => $val)
		{		
			
				$val = array_map('trim',$val);
				$id = $sku_list[$val[1]];
				$goods_id = $sku_goodsId_list[$val[1]];
				$order_sn = $val[0];
				$order_id = $order_snList[$val[0]];
				$order_info = $orderModel->getOrderInfoBySn($order_sn);
				//解绑
				 $warehouseModel = new ApiWarehouseModel();
				$reat = $warehouseModel->JiebasjiaGoodsInfoByGoodsId(array('order_goods_id'=>$id,'goods_id'=>$goods_id,'bind_type'=>2));
				$orderModel->DeletewphOrdergoods($id);
				$price=$orderModel->Selectwphogprice($order_id);
				if(empty($price)){
					$price =0;
				}
				$info = array('order_id'=>$order_id,'order_price'=>$price);
				$model->UpdatewphOrderAccount($info);
				
				
				$logInfo = array(
					 'order_id'=>$order_info['id'],
					 'order_status'=>$order_info['order_status'],
					 'shipping_status'=>$order_info['send_good_status'],
					 'pay_status'=>$order_info['order_pay_status'],
					 'create_user'=>$_SESSION['userName'],
					 'create_time'=>date("Y-m-d H:i:s"),
					'remark'=>'SKU编号'.$val[1].',已删除,订单价格修改为'.$price.'',
				);
				//日志
				$orderModel->addOrderAction($logInfo);
			
		}
		$result['success'] = 1;
		Util::jsonExit($result);
	}
	public function uploadwphUpdateprice()
	{
		$result = array('success' => 0, 'error' => '');
		$upload_name = $_FILES;
		$orderModel= new BaseOrderInfoModel(27);
        if (!$upload_name) {
            $result['error'] = '文件不能为空';
            Util::jsonExit($result);
        }	

        if (Upload::getExt($upload_name['file_price']['name']) != 'csv') {
            $result['error'] = '请上传csv格式文件';
            Util::jsonExit($result);
        }

		$file = fopen($upload_name['file_price']['tmp_name'], 'r');
        while ($data = fgetcsv($file)) {
			foreach($data as $k => $v){
				$data[$k] = iconv("GBK","UTF-8",$v);
			}
			$data_r[]=$data;
			$data_order[] = $data[0];
        }
		$data_order=array_splice($data_order,1);
		//var_dump($data_order);die;
		$header_target="订单号,SKU,商品价格";
		$header=implode(',',array_shift($data_r));

		if($header != $header_target){
			$result['error'] = '表头出错';
			Util::jsonExit($result);
		}
		if(!$data_r){
            $result['error'] = '无信息上传!';
            Util::jsonExit($result);
        }
		foreach($data_r as $k=>$v){
			$v = array_map('trim',$v);
			if($v[0]==''){
				$result['error'] = '第'.($k+2).'行订单号为空';
				Util::jsonExit($result);
			}
			if($v[1]==''){
				$result['error'] = '第'.($k+2).'行SKU编号为空';
				Util::jsonExit($result);
			}
			if($v[2]=='' && $v[2]=='0'){
				$result['error'] = '第'.($k+2).'行价格出错';
				Util::jsonExit($result);
			}
		}
		$model = new baseorderinfomodel(28);
		/*验证唯品会订单*/
		//var_dump($data_order);die;
		$order_goods_info = $model->GetwphOrderinfo($data_order);
		if(empty($order_goods_info)){
			$result['error'] = '数据不存在!';
			Util::jsonExit($result);
		}
		foreach($order_goods_info as $key => $val)
		{
			$val = array_map('trim',$val);
			if('1' != $val['order_pay_status']){
				$result['error'] = '订单'.$val['order_sn'].'非未付款状态不能修改';
				Util::jsonExit($result);
			}
			if('2034' != $val['customer_source_id']){
				$result['error'] = '订单'.$val['order_sn'].'非唯品会订单来源';
				Util::jsonExit($result);
			}
			if(empty($val['details_remark'])){
				$result['error'] = '订单'.$val['order_sn'].'SKU编号为空';
				Util::jsonExit($result);
			}
		}
		$baseArr = array();
		$sku_list = array();
		$new = array();
			//var_dump($order_goods_info);die;
		foreach($order_goods_info as $key =>$vals){
			//var_dump($vals);die;
			$baseArrsku[$vals['order_sn']] = $vals['details_remark'];
			$baseArr[$vals['order_sn']][$vals['details_remark']] = $vals['goods_price'];
			$sku_list[$vals['details_remark']] = $vals['id'];
			$order_snList[$vals['order_sn']] = $vals['order_id'];
		}
		//var_dump($baseArr,$sku_list,$order_snList);die;
		foreach($data_r as $key => $val)
		{
			if($baseArrsku[$val[0]] != $val[1]){
				$result['error'] = '订单'.$val[0].'和SKU'.$val[1].'不对应';
				Util::jsonExit($result);
			}
			$val = array_map('trim',$val);
			if($baseArr[$val[0]][$val[1]] == $val[2]){
				unset($baseArr[$val[0]][$val[1]]);
			}else{
				$new = array(
					'id' => $sku_list[$val[1]],
					'goods_price' => $val[2]
				);
				//var_dump($new);die;
				$orderModel->UpdatewphOrderprice($new);
				$orderog_price = $model->SelectwphOgprice($order_snList[$val[0]]);
				//var_dump($orderog_price);die;
					$info = array(
						'order_price'=>$orderog_price,
						'order_id'=>$order_snList[$val[0]]
					);
					//var_dump($info);die;
					$model->UpdatewphOrderAccount($info);
				 $order_info = $orderModel->getOrderInfoBySn($val[0]);
				 $logInfo = array(
					 'order_id'=>$order_info['id'],
					 'order_status'=>$order_info['order_status'],
					 'shipping_status'=>$order_info['send_good_status'],
					 'pay_status'=>$order_info['order_pay_status'],
					 'create_user'=>$_SESSION['userName'],
					 'create_time'=>date("Y-m-d H:i:s"),
					 'remark'=>'SKU编号'.$val[1].' 的价格有修改为 '.$val[2].',订单修改为'.$info['order_price'].'',
					 );
					//日志
					
				$orderModel->addOrderAction($logInfo);
			}		
		}
		$result['success'] = 1;
		Util::jsonExit($result);
    }

    public function uploadinsertwphinfo()
    {
        $result = array('success' => 0, 'error' => '');
        $upload_name = $_FILES;
        if (!$upload_name) {
            $result['error'] = '文件不能为空';
            Util::jsonExit($result);
        }   
        if (Upload::getExt($upload_name['file_order']['name']) != 'csv') {
            $result['error'] = '请上传csv格式文件';
            Util::jsonExit($result);
        }

        $file = fopen($upload_name['file_order']['tmp_name'], 'r');
        while ($data = fgetcsv($file)) {
            foreach($data as $k => $v){
                $data[$k] = iconv("GBK","UTF-8",$v);
            }
            $data_r[]=$data;
            $data_order[] = $data[0];
        }
        $data_order=array_splice($data_order,1);
        //var_dump($data_order);die;
        $header_target="订单号,商品优惠金额";
        $header=implode(',',array_shift($data_r));

        if($header != $header_target){
            $result['error'] = '表头出错';
            Util::jsonExit($result);
        }
        if(!$data_r){
            $result['error'] = '无信息上传!';
            Util::jsonExit($result);
        }
        foreach($data_r as $k=>$v){
            $v = array_map('trim',$v);
            if($v[0]==''){
                $result['error'] = '第'.($k+2).'行订单号为空';
                Util::jsonExit($result);
            }
            if($v[1]<=0){
                $result['error'] = '第'.($k+2).'行商品优惠金额必须大于0';
                Util::jsonExit($result);
            }
        }
        $model = new BaseOrderInfoModel(27);
        $detailModel = new AppOrderDetailsModel(28);
        /*验证唯品会订单*/
        //var_dump($data_order);die;
        $order_goods_info = $model->GetwphOrderinfo($data_order);
        if(empty($order_goods_info)){
            $result['error'] = '数据不存在!';
            Util::jsonExit($result);
        }
        foreach($order_goods_info as $key => $val)
        {
            $val = array_map('trim',$val);
            if('1' != $val['order_pay_status']){
                $result['error'] = '订单'.$val['order_sn'].'非未付款状态不能修改';
                Util::jsonExit($result);
            }
            if('5' == $val['delivery_status']){
                $result['error'] = '订单'.$val['order_sn'].'已配货状态不能修改';
                Util::jsonExit($result);
            }
            if('2' == $val['send_good_status']){
                $result['error'] = '订单'.$val['order_sn'].'已发货状态不能修改';
                Util::jsonExit($result);
            }
            if('2034' != $val['customer_source_id']){
                $result['error'] = '订单'.$val['order_sn'].'非唯品会订单来源';
                Util::jsonExit($result);
            }
            /*if('13' != $val['department_id']){
                $result['error'] = '订单'.$val['order_sn'].'非B2C销售部渠道';
                Util::jsonExit($result);
            }*/
        }

        //开始处理逻辑
        $res = $model->exctrWphOrderInfo($data_r);
        if($res['error'] != 1){
            $result['success'] = 1;
            Util::jsonExit($result);
        }
        $result['error'] = $res['msg'];
        Util::jsonExit($result);
        //开始事物
        /*$pdolist[27] = $model->db()->db();
        $pdolist[28] = $detailModel->db()->db();
        try{
            foreach ($pdolist as $pdo){
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
                $pdo->beginTransaction(); //开启事务
            }
        }catch (Exception $e){
            $error = "操作失败，事物回滚！提示：系统批量开启事物时发生异常！";
            Util::rollbackExit($error,$pdolist);
        }

        foreach($data_r as $key => $val)
        {   

            $order_sn = trim($val[0]);
            $favorable_price = trim($val[1]);
            $favorable_price = str_replace(',', '', $favorable_price);
            $orderInfo = $model->getOrderInfoByOrdersn($order_sn);
            if(count($orderInfo) != '1'){
                $error = '操作失败,事物回滚!提示：订单'.$order_sn.'中【是否赠品】为否的商品只有一件才能批量修改，如果有多件非赠品的商品，不允许上传，可以到订单界面单独修改金额';
                Util::rollbackExit($error,$pdolist);
            }
            $orderData = $orderInfo[0];
            $old_favorable_price = $orderData['favorable_status'] == 3 ?$orderData['favorable_price']:0;
            $price = $orderData['goods_price'] - $old_favorable_price;
            if($favorable_price >= $price){
                $error = '操作失败,事物回滚!提示：订单'.$order_sn.'中商品优惠金额必须＜订单的商品价格之和';
                Util::rollbackExit($error,$pdolist);
            }
            //重新计算订单金额
            $favorable_price_par = $old_favorable_price+$favorable_price;
            //保存优惠金额
            $res = $detailModel->updateDetailPrice($favorable_price_par,$orderData['id']);
            if(!$res){
                $error ="操作失败,事物回滚!提示：保存优惠金额失败";
                Util::rollbackExit($error,$pdolist);
            }

            //订单金额 = 订单金额-这次的优惠
            $orderamount = $orderData['order_amount']-$favorable_price ;
            $order_unpaid = $orderData['money_unpaid']-$favorable_price;
            $infoPrice = $model->getOrderPriceInfoByorder_sn($order_sn);
            $favorable_price_new = $infoPrice['favorable_price'] + $favorable_price;//商品优惠
            $info['order_amount'] = $orderamount;
            $info['favorable_price'] = $favorable_price_new;
            $info['money_unpaid'] = $order_unpaid;
            $res = $model->updateOrderPrice($info,$orderData['acc_id']);
            if(!$res){
                $error ="操作失败,事物回滚!提示：保存订单金额失败";
                Util::rollbackExit($error,$pdolist);
            }
            $order_info = $model->getOrderInfoBySn($order_sn);
            $logInfo = array(
                'order_id'=>$order_info['id'],
                'order_status'=>$order_info['order_status'],
                'shipping_status'=>$order_info['send_good_status'],
                'pay_status'=>$order_info['order_pay_status'],
                'create_user'=>$_SESSION['userName'],
                'create_time'=>date("Y-m-d H:i:s"),
                'remark'=>'修改优惠金额:￥'.$favorable_price
            );
            //日志
            $res = $model->addOrderAction($logInfo);
            if(!$res){
                $error ="操作失败,事物回滚!提示：保存订单日志失败";
                Util::rollbackExit($error,$pdolist);
            }
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
        }*/
    }
}