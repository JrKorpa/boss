<?php
/**
 *   批量订单审核，布产控制器
 *  -------------------------------------------------
 *   @file		: BatchConfirmOrderController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-08-03 
 *   @update	:
 *  -------------------------------------------------
 */
class BatchConfirmOrderController extends CommonController
{
     /**
      * 默认页
      */
     public function  index($params){
         
         $this->render('batch_confirm_order_info.html');
     }
     
     /**
      * 获取批量订单编号
      */
     private function _getOrderSnList($params){

         $result['content'] = '';
     
         if(empty($params['order_sn'])){
             $result['error'] = "批量订单ID为空";
             Util::jsonExit($result);
         }
          
         $order_sn_split = explode("\n",$params['order_sn']);
     
         //获取最终合法订单编号
         $order_sn_list = array();
         foreach($order_sn_split as $vo){
             //获取不为空和不重复的订单id
             if(trim($vo)!='' && !in_array($vo,$order_sn_list)){
                 $order_sn_list[]=trim($vo);
             }
         }
         if(empty($order_sn_list)){
             $result['error'] = "批量合法订单ID为空";
             Util::jsonExit($result);
         }
         return $order_sn_list;
     }
     
     /**
      * 批量订单审核
      */
     public  function  confirm($params){

         $error = array();//验证错误记录
         $data  = array();//订单合法信息
         $result['content']='';
         
         $order_sn_list = $this->_getOrderSnList($params);
         $model = new BaseOrderInfoModel(28);
         $orderDetailModel = new AppOrderDetailsModel(27);
         
         //循环订单ID
         foreach($order_sn_list as $order_sn){
             $orderInfo = $model->getOrderInfoBySn($order_sn);
             
             //检验订单编号是否有效
             if(empty($orderInfo)){
                 $error[$order_sn][] = "订单编号无效";
                 continue;
             }

             $order_id = $orderInfo['id'];
             $order_status = $orderInfo['order_status'];
             $apply_close = $orderInfo['apply_close'];
             
             if($order_status == 4) {
                 $error[$order_sn][] = '订单已关闭';
                 continue;
             }
             if($order_status != 1) {
                 $error[$order_sn][] = '订单已经审核';
                 continue;
             }
             if($apply_close == 1){
                 $error[$order_sn][] = '订单已申请关闭';
                 continue;
             }
             //验证收货地址
             $ret = $model->getAddressByid($order_id);
             if (empty($ret) || empty($ret[0]['address'])) {
                 $error[$order_sn][] = "没有收获地址";
             }
             
             //验证订单内是否有商品
             $goods_info = $orderDetailModel->getGoodsByOrderId(array('order_id' =>$order_id));
             $haveZp=false;
             if (empty($goods_info)) {
                 $error[$order_sn][] = '订单内没有商品';
             }else{
                 $xiangqianf = array();
                 foreach($goods_info as $k=>$v){
                     if(($v['goods_type']!='zp')&&($v['xiangqian']=='')){
                         $xiangqianf[]=$v['goods_id'];
                     }
                     if($v['goods_type']=='zp'){
                         $haveZp = true;
                     }
                 }
                 if(!empty($xiangqianf)){
                     $xiangqianfs =trim(implode(',',$xiangqianf),',');
                     $error[$order_sn][] = '商品货号为'.$xiangqianfs.'没有填写镶嵌要求！';
                 }             
             }
                          
             //将没有出错的订单数据保存起来
             if(empty($error[$order_sn])){
                 $orderInfo['goods_info'] = $goods_info;
                  $orderInfo['is_zp'] = $haveZp?1:0;
                  $data[$order_sn] = $orderInfo;                  
             }

         }

         $pdoFlag = false;
         if(empty($error)){
             $pdo = $model->db()->db();
             $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
             $pdo->beginTransaction();//开启事务
             $payment_list = $this->getPaymentsBeiAn();
             
             $order_count =0;
             foreach ($data as $vo){
                 $order_status = 2;
                 $model = new BaseOrderInfoModel($vo['id'], 28);
                 //如果支付方式是财务备案将支付状态改成财务备案，但如果是京东部门且含有彩钻、裸钻商品，则为部分付款（支付定价）
                 $is_pay_part = $this->_isPayPart($vo, $vo['goods_info']);
                 if ($is_pay_part) {
                     $model->setValue('order_pay_status', 2);
                 } elseif (!empty($payment_list[$vo['order_pay_type']])){
                     $model->setValue('order_pay_status', 4);
                     $model->setValue('pay_date', date("Y-m-d H:i:s",time()));//第一次点款时间
                     if(!in_array(0,array_column($vo['goods_info'],'is_stock_goods'))&&$vo['is_xianhuo']==1){
                         $model->setValue('delivery_status', 2);
                     }
                 }
                 
                 $model->setValue('is_zp', $vo['is_zp']);
                 $model->setValue('check_user', Auth::$userName);
                 $model->setValue('check_time', date('Y-m-d H:i:s'));
                 $model->setValue('order_status', $order_status);

                //财务备案现货自动配货
                if(!empty($payment_list[$vo['order_pay_type']])){
                    $model->setValue('order_pay_status', 4);
                    $model->setValue('pay_date', date("Y-m-d H:i:s",time()));//第一次点款时间
                    if(!in_array(0,array_column($vo['goods_info'],'is_stock_goods'))&&$vo['is_xianhuo']==1){
                        $model->setValue('delivery_status', 2);
                    }
                }
                //财务备案期货自动配货
                if(!empty($payment_list[$vo['order_pay_type']]) && $vo['is_xianhuo']==0){
                    $result = $this->_allow_buchan($vo['id']);
                    if ($result['success']==0) {
                         $pdoFlag = true;//出错标志
                         $pdo->rollback();//事务回滚
                         $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                         break;
                    }else{
                        $vo['order_pay_status']=4;
                    }
                }
                 //鉴定这个订单是否全是赠品
                 $arr = array('zp');
                 $typearr = array_column($goods_info,'goods_type');
                 $rea = array_diff($typearr,$arr);
                 //获取未付金额
                 $ret = $model->getAccountInfo($vo['order_sn']);
                 if(empty($rea)&&$ret['order_amount']==0){
                     $model->setValue('order_pay_status', 3);
                 }
                 
                 if($model->save() !== false){
                     //操作日志
                     $ation['order_id'] = $vo['id'];
                     $ation['order_status'] = $order_status;
                     $ation['shipping_status'] = 1;
                     $ation['pay_status'] = $vo['order_pay_status'];
                     $ation['create_user'] = Auth::$userName;
                     $ation['create_time'] = date("Y-m-d H:i:s");
                     $ation['remark'] = "订单批量操作，批量审核成功";
                     $logRet = $model->addOrderAction($ation,$vo['id']);
                     if(!$logRet){
                         $pdoFlag = true;//出错标志
                         $pdo->rollback();//事务回滚
                         $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                         break;
                     }else{
                         $order_count++;
                     }
                 }else{
                     $pdoFlag = true;//出错标志
                     $pdo->rollback();//事务回滚
                     $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                     break;
                 }
                 
             }
             
             if($pdoFlag === true){
                 $result['error'] = "审核失败！数据库操作发生错误，事物已回滚！";
                 Util::jsonExit($result);
             }else{
                 $pdo->commit();
                 $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                 $result['success'] = 1;
                 $result['content'] = "订单审核成功！总计:".$order_count.'<br/><hr/>';
                 //获取日志错误内容
                 foreach($data as $key=>$vo){
                     $result['content'].="{$key}：审核成功!<br/>";                    
                 }
                 Util::jsonExit($result);
             }
         }else{
             //获取日志错误内容
             $result['content'] = "订单审核验证失败，共有【".count($error)."】个订单不符合审核条件<hr/>";
             foreach($error as $key=>$vo1){
                 $result['content'].="{$key}：";
                 foreach ($vo1 as $vo2){
                     $result['content'] .='【'.$vo2.'】,';
                 }
                 $result['content'] =trim($result['content'],',')."<hr/>";
             }
             $result['error'] = "订单审核验证失败,共有【".count($error)."】个不符条件的订单";
             Util::jsonExit($result);
         }
     }
    //判断是否是支付定金的订单，条件暂定：京东部门且含有彩钻、裸钻商品
    private function _isPayPart($order=array(), $order_infos=array()) {
        if ($order['department_id']==71) {
            foreach ($order_infos as $goods) {
                if ($goods['goods_type']=='lz' || $goods['goods_type']=='caizuan_goods') {
                    return true;
                }
            }
        }
        return false;
    }

     /**
      * 批量布产
      */
     public  function  buchan($params){

         $db_error = array();//API垮裤操作产生的错误预警
         
         $error = array();//错误记录
         $data  = array();//订单合法信息
         $result['content']='';
         
         $order_sn_list = $this->_getOrderSnList($params);
         $model = new BaseOrderInfoModel(28);
         $orderDetailModel = new AppOrderDetailsModel(27);
         $processorApiModel = new ApiProcessorModel();
         $diamondApiModel = new ApiDiamondModel();
         foreach($order_sn_list as $order_sn){
             
             $orderInfo = $model->getOrderInfoBySn($order_sn);
             //检验订单编号是否有效
             if(empty($orderInfo)){
                 $error[$order_sn][] = "订单编号无效";
                 continue;
             }
             $order_id = $orderInfo['id'];
             $order_status = $orderInfo['order_status'];
             $pay_status = $orderInfo['order_pay_status'];
             $apply_close = $orderInfo['apply_close'];
             $buchan_status = $orderInfo['buchan_status'];
             $is_xianhuo = $orderInfo['is_xianhuo'];
             $order_remark = $orderInfo['order_remark'];
             if($order_status != 2) {
                 $error[$order_sn][] = '订单不是已审核状态'.$order_status;
             }
             
             if($pay_status == 1 && $orderInfo['referer'] != '婚博会'){
                 $error[$order_sn][] = "订单未付款";
             }
             
             if($buchan_status>=2){
                 $error[$order_sn][] = '订单已布产';
                 continue;
             }
             
             /* if($is_xianhuo==1){
                 $error[$order_sn][] = '现货单不能布产';
                 continue;
             } */
             $detail_goods = $orderDetailModel->getGoodsByOrderId(array('order_id' =>$order_id));

             if(empty($detail_goods)){
                 $error[$order_sn][] = "订单商品不存在";
                 continue;
             }             

             foreach($detail_goods as $key=>$val){
                 //如果是现货
                 if($val['is_stock_goods'] == 1 && $val['is_peishi']==0){
                     //不是期货不能布产
                     unset($detail_goods[$key]);
                     continue;
                 }
                 if($val['is_peishi']==1){
                     $zhengshuhao = $val['zhengshuhao'];
                     $ret = $diamondApiModel->getDiamondInfoByCertId($zhengshuhao);
                     if($ret['error']==0){
                         $detail_goods[$key]['diamond'] = $ret['data'];
                     }else{
                         $error[$order_sn][] = "货号为{$val['goods_id']}的商品证书号不存在";
                     }
                 }
                 
                 $detail_id = $val['id'];
                 //查看此商品是否已经开始布产
                 $buchan_info = $processorApiModel->CheckGoodsProductInfo($detail_id,$order_sn);
                 if(!empty($buchan_info['data'])){
                     //查出该商品布产关联信息，表示已经布产,需要忽略
                     unset($detail_goods[$key]);
                     continue;
                 } 
             }
             if(empty($detail_goods)){
                 $error[$order_sn][]="没有符合布产条件的商品";
             }else if(empty($error[$order_sn])){
                 //将可布产商品存放到data
                 $data['order_info'][$order_sn] = $orderInfo;
                 $data['detail_goods'][$order_sn] = $detail_goods;
             }
             
         }
         
         //如果没有出错,进行商品布产入库
         if(empty($error)){
              $attr_names =array(
                 'cart'=>'石重',
                 'clarity'=>'净度',
                 'color'=>'颜色',
                 'zhengshuhao'=>'证书号',
                 'caizhi'=>'材质',
                 'jinse'=>'金色',
                 'jinzhong'=>'金重',
                 'zhiquan'=>'指圈',
                 'kezi'=>'刻字',
                 'face_work'=>'表面工艺'
              );
              //写入订单日志Model
              $orderActionModel = new AppOrderActionModel(28);
              if(empty($data['detail_goods']) || empty($data['order_info'])){
                  $result['error'] = "系统内部错误,error line:";
                  Util::jsonExit($result);
              }
              

            foreach ($data['detail_goods'] as $order_sn=>$detail_goods){
               $goods_arr = array();
               foreach($detail_goods as $key=>$val){
                  
                  $orderInfo = $data['order_info'][$order_sn];
                  $order_id = $orderInfo['id'];
                  $detail_id = $val['id'];
                  
                  $new_style_info = array();
                  foreach ($attr_names as $a_key=>$a_val){
                      $xmp['code'] = $a_key;
                      $xmp['name'] = $a_val;
                      $xmp['value'] = $val[$a_key];
                      $new_style_info[]= $xmp;
                  }
                  $goods_arr[$key] = $val;
                  $goods_arr[$key]['p_id'] = $detail_id;
                  $goods_arr[$key]['p_sn'] =  $order_sn;
                  $goods_arr[$key]['style_sn'] = $val['goods_sn'];
                  $goods_arr[$key]['goods_name'] = $val['goods_name'];
                  $goods_arr[$key]['bc_style'] = empty($val['bc_style'])?'普通件':$val['bc_style'];
                  $goods_arr[$key]['xiangqian'] = $val['xiangqian'];
                  $goods_arr[$key]['goods_type'] = $val['goods_type'];
                  $goods_arr[$key]['cat_type'] = $val['cat_type'];
                  $goods_arr[$key]['product_type'] = $val['product_type'];
                  $goods_arr[$key]['num'] = $val['goods_count'];
                  $goods_arr[$key]['info'] = $val['details_remark'];
                  $goods_arr[$key]['consignee'] = $orderInfo['consignee'];
                  $goods_arr[$key]['attr'] = $new_style_info;
                  $goods_arr[$key]['customer_source_id'] = $orderInfo['customer_source_id'];
                  $goods_arr[$key]['channel_id'] = $orderInfo['department_id'];
                  $goods_arr[$key]['create_user']= $_SESSION['userName'];
                  $goods_arr[$key]['is_peishi']=$val['is_peishi'];
				  $goods_arr[$key]['caigou_info'] = $order_remark;
                  if ($val['is_peishi']==1) {
                      $goods_arr[$key]['is_peishi'] = 1;
                      $goods_arr[$key]['diamond'] = $val['diamond'];
                  }                 
                }

                $bc_ret = $processorApiModel->AddProductInfo($goods_arr);
                if(isset($bc_ret['error']) && $bc_ret['error']==0){
                    $bc_sn_list = array();
                    foreach($bc_ret['data'] as $va){
                         
                        $bc_sn_list[]= $va['final_bc_sn'];
                         
                        $detailsModel = new AppOrderDetailsModel($va['id'],28);
                        $detailsModel->setValue('bc_id', $va['buchan_sn']);
                        $detailsModel->save();
                    }
                    
                    //同步订单表布产状态
                    $baseOrderInfoModel = new BaseOrderInfoModel($order_id, 28);
                    $baseOrderInfoModel->setValue('effect_date', date("Y-m-d H:i:s"));
                    $baseOrderInfoModel->setValue('buchan_status', 2);//变成允许布产
                    $res1 = $baseOrderInfoModel->save();
                    if($res1 ===false){
                        //同步订单布产状态失败,由于API跨数据库操作，暂时无法进行事务回滚处理
                        //本次异常不中断程序
                        //$db_error[$order_sn][] = "布产成功但同步订单布产状态失败";
                    }
                    //订单操作日志
                    $buchan_sn_split = trim(implode(",",$bc_sn_list),',');
                    $ation['order_status'] = $orderInfo['order_status'];
                    $ation['order_id'] = $orderInfo['id'];
                    $ation['shipping_status'] = $orderInfo['send_good_status'];
                    $ation['pay_status'] = $orderInfo['order_pay_status'];
                    $ation['create_user'] = Auth::$userName;
                    $ation['create_time'] = date("Y-m-d H:i:s");
                    $ation['remark'] = "订单批量操作，批量布产成功，自动生成布产单号".$buchan_sn_split;
                    $res2 = $orderActionModel->saveData($ation, array());
                    if($res2 ===false){
                        //添加订单布产日志失败,由于API跨数据库操作，暂时无法进行事务回滚处理
                        //本次异常不中断程序
                        //$db_error[$order_sn][] = "订单布产日志写入失败";
                    }                    
                
                }else{
                    //布产发布失败,由于API跨数据库操作，暂时无法进行事务回滚处理
                    //本次异常不中断程序
                    $db_error[$order_sn][] = isset($bc_ret['data'])?"系统异常":$bc_ret['data'];
                }
                  
              }
              $order_num = count($data['order_info']);
              $fail_num = count($db_error);
              if($fail_num > 0){                  
                  $success_num = $order_num - $fail_num; 
                  $result['success'] = 1;
                  $result['content'] = "批量布产操作成功！成功订单【".$success_num."】，失败订单【".$fail_num."】;失败的订单请尝试重新布产！<hr/>";
              }else{
                  $result['success'] = 1;
                  $result['content'] = "批量布产操作成功！订单总数:【".count($data['order_info']).'】<hr/>';
              }
              //获取日志错误内容
              foreach($data['order_info'] as $key=>$vo){
                  if(isset($db_error[$key])){
                      $db_error_split= implode(',',$db_error[$key]);
                      // $tip="订单布产发生了数据库操作异常，可能导致数据不同步，请复制保存本次预警提示:";
                      // $result['content'].="{$key}：<font color='red'>{$tip}</font>{$db_error_split}<br/><hr/>";
                      $result['content'].="<font color='red'>{$key}</font>:{$db_error_split}<br/><hr/>";
                  }else{
                      $result['content'].="{$key}：布产成功,布产商品总数【".count($data['detail_goods'][$key])."】<br/>";
                  }
              }
              Util::jsonExit($result);
             
         }else {
             //获取日志错误内容
             $result['content'] = "批量布产验证失败，共有【".count($error)."】个订单不符合布产条件<hr/>";
             foreach($error as $key=>$vo1){
                 $result['content'].="{$key}：";
                 foreach ($vo1 as $vo2){
                     $result['content'] .='【'.$vo2.'】,';
                 }
                 $result['content'] =trim($result['content'],',')."<hr/>";
             }
             $result['error'] = "订单布产验证失败,共有【".count($error)."】个不符条件的订单";
             Util::jsonExit($result);
         }
     }

    //允许布产
    private function _allow_buchan($order_id) {
        $result = array('success' => 0, 'error' => '');
        $model = new BaseOrderInfoModel($order_id, 27);
        $orderInfo = $model->getDataObject();
        $order_sn = $model->getValue('order_sn');

        //获取订单商品中的期货
        $detailModel = new AppOrderDetailsModel(27);
        //订单商品全部布产不允许布产
        $bc_status=$detailModel->getGoodsBcStatus($order_id);
        if(empty($bc_status)) {
            $result['error'] = $order_sn."：订单所有商品已经生成布产单,如果需要重新布产,请到布产列表重新提交！";
            return $result;
        }
        $order_detail_data = $detailModel->getGoodsByOrderId(array('order_id'=>$order_id,'is_stock_goods'=>0));
        $bc_ret = $this->AddBuchanDan($orderInfo,$order_detail_data);
        if($bc_ret['error'] == 1){
            $result['error'] = $bc_ret['data'];
            return $result;
        }

        //获取布产单号
        if($bc_ret['data']){
            $a = $bc_ret['data'];
            foreach($a as $va){
                $detailsModel = new AppOrderDetailsModel($va['id'],28);
                $detailsModel->setValue('bc_id', $va['buchan_sn']);
                $detailsModel->save();
            }
            $buchan_sn = implode(",",array_column($a,'final_bc_sn'));
            //写入日志
            $orderActionModel = new AppOrderActionModel(28);
            //操作日志
            $ation['order_status'] = $orderInfo['order_status'];
            $ation['order_id'] = $orderInfo['id'];
            $ation['shipping_status'] = $orderInfo['send_good_status'];
            $ation['pay_status'] = $orderInfo['order_pay_status'];
            $ation['create_user'] = $_SESSION['userName'];
            $ation['create_time'] = date("Y-m-d H:i:s");
            $ation['remark'] = $orderInfo['order_sn']."订单允许布产成功！布产单号为：".$buchan_sn;
            $orderActionModel->saveData($ation, array());
        }

        $_model = new BaseOrderInfoModel($order_id, 28);
        $_model->setValue('effect_date', date("Y-m-d H:i:s"));
        $_model->setValue('buchan_status', 2);//变成允许布产
        $_model->setValue('order_pay_status', 4);//财务备案
        $res = $_model->save();
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = $order_sn."：布产操作失败";
        }
        return $result;
    }

    //把定制商品进行布产
    public function AddBuchanDan($orderinfo,$detail_goods){
        $order_sn			= $orderinfo['order_sn'];
        $consignee			= $orderinfo['consignee'];
		$customer_source_id = $orderinfo['customer_source_id'];
		$department_id		= $orderinfo['department_id'];
        
		//判断是否独立销售 is_alone
		//有指圈号(zhiquan)&&证书号(zhengshuhao)不是 K||c  基本上都是空托      ==>单独售卖
		//goods_type = lz 一定是祼钻                                        ==>单独售卖
		if($orderinfo['is_xianhuo'] == 0){
		    $is_lz_order = 0;
		    foreach ($detail_goods as $x){
		        if($x['goods_type'] == 'lz'){//判断订单里是否有祼钻
		            $is_lz_order = 1;
		        }
		    }
		    foreach ($detail_goods as $k=>$v){
		        $detail_goods[$k]['is_alone'] = 0;
		        preg_match('/^(W|M)-(\w+)-(\w{1})-(\d+)-(\d{2})$/',$detail_goods[$k]['goods_id'],$matches);
		        if($v['goods_type'] == 'lz'){//单独售卖
		            $detail_goods[$k]['is_alone'] = 1;
		        }elseif( !empty($matches))  {//戒托
		            $detail_goods[$k]['is_alone'] = 1;
		        }elseif( empty($v['zhengshuhao']) && $is_lz_order)  {//如果是祼钻订单则独立售卖
		            $detail_goods[$k]['is_alone'] = 1;
		        }elseif( empty($v['zhengshuhao']) && !$is_lz_order)  {//证书号为空 且非祼钻订单
		            
		        }elseif( strripos($v['zhengshuhao'],'K') !== false || strripos($v['zhengshuhao'],'C') !== false)  {//成品售卖
		            
		        }else{
		            $orderDetailModel = new AppOrderDetailsModel(27);
		            $goods_info = $orderDetailModel->getGoodsInfoByZhengshuhao($v['zhengshuhao'],$v['id']);
		            if($goods_info){//单独售卖
		                $detail_goods[$k]['is_alone'] = 1;
		            }
		        }
		    }
		}
		
        $processorApiModel = new ApiProcessorModel();
        $diamondApiModel  = new ApiDiamondModel();
        //找到此订单是否已经存在布产的单
        $attr_names =array('cart'=>'主石单颗重','zhushi_num'=>'主石粒数','clarity'=>'主石净度','color'=>'主石颜色','cert'=>'证书类型','zhengshuhao'=>'证书号','caizhi'=>'材质','jinse'=>'金色','jinzhong'=>'金重','zhiquan'=>'指圈','kezi'=>'刻字','face_work'=>'表面工艺');
        if(!empty($detail_goods)){
            $goods_arr = array();
            foreach($detail_goods as $key=>$val){
                if($val['is_stock_goods'] == 1 && empty($val['is_peishi'])){
                    continue;
                }
                $detail_id = $val['id'];
                //查看此商品是否已经开始布产
                //$buchan_info = $processorApiModel->GetGoodsRelInfo($detail_id);
                $buchan_info = $processorApiModel->CheckGoodsProductInfo($detail_id,$order_sn);
                if(!empty($buchan_info['data'])){
                    continue;
                }
                
                /* if($buchan_info['error']==0){
                    continue;
                } */
                $new_style_info = array();
                foreach ($attr_names as $a_key=>$a_val){
                    $xmp['code'] = $a_key;
                    $xmp['name'] = $a_val;
                    $xmp['value'] = $val[$a_key];
                    $new_style_info[]= $xmp;
                }
                $goods_num = $val['goods_count'];
                
                if($val['is_peishi']==1){
                    $zhengshuhao = $val['zhengshuhao'];
                    $ret = $diamondApiModel->getDiamondInfoByCertId($zhengshuhao);
                    if($ret['error']==0){
                        $goods_arr[$key]['diamond'] = $ret['data'];
                    }else{
                        $result['error'] = "货号为{$val['goods_id']}的商品证书号不存在";
                        Util::jsonExit($result);
                    }
                }
                
                $goods_arr[$key]['p_id'] =	$detail_id;
                $goods_arr[$key]['p_sn'] =  $order_sn;
                $goods_arr[$key]['style_sn'] = $val['goods_sn'];
                $goods_arr[$key]['goods_name'] = $val['goods_name'];
                $goods_arr[$key]['bc_style'] = empty($val['bc_style'])?'普通件':$val['bc_style'];
                $goods_arr[$key]['xiangqian'] = $val['xiangqian'];
                $goods_arr[$key]['goods_type'] = $val['goods_type'];
                $goods_arr[$key]['cat_type'] = $val['cat_type'];
                $goods_arr[$key]['product_type'] = $val['product_type'];
                $goods_arr[$key]['num'] = $goods_num;
                $goods_arr[$key]['info'] = $val['details_remark'];
                $goods_arr[$key]['consignee'] = $consignee;
                $goods_arr[$key]['attr'] = $new_style_info;
				$goods_arr[$key]['customer_source_id'] = $customer_source_id;
				$goods_arr[$key]['channel_id'] = $department_id;
				//$goods_arr[$key]['create_user']=$orderinfo['create_user'];
                //把布产单的创建人从订单制单人修改成点击允许布产的操作人   add liuri by 20150605
				$goods_arr[$key]['create_user']=$_SESSION['userName'];
				$goods_arr[$key]['is_peishi'] = $val['is_peishi'];
				$goods_arr[$key]['is_alone'] = $val['is_alone'];
                $goods_arr[$key]['diamond_type'] = '0';
                //$goods_arr[$key]['qiban_type'] = '2';//默认
                $goods_arr[$key]['origin_dia_type'] = '0';
                //end
            }
			//var_dump($goods_arr);exit;
            $res = array('data'=>'','error'=>0);
            //添加布产单
            if(!empty($goods_arr)){
                $res = $processorApiModel->AddProductInfo($goods_arr);
            }
            //$res['buchan_info'] = $buchan_info;
            //$res['goods_arr'] = $goods_arr;
            return $res;
        }
    }
}