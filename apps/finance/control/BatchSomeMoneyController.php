<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseOrderInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-20 16:56:01
 *   @update	:
 *  -------------------------------------------------
 */
class BatchSomeMoneyController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist = array('downcsv');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('batch_some_mony_form.html',array('bar'=>Auth::getBar('BATCH_SOME_MONEY_M',array(),true)));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
        //支付方式
        $paymentModel = new PaymentModel(1);
		$paymentList = $paymentModel->getEnabled();
        //客户来源
		$result['content'] = $this->fetch('batch_some_mony_info.html',array(
            'paymentList'=>$paymentList,
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}   
    /**
     * 批量点款处理
     * @param  $params
     * gaopeng
     */
	public function insert ($params)
	{
        $result=array('success'=>0,'error'=>'');
        if(empty($_FILES['file_order']['tmp_name'])){
            $result['error'] = '请上传文件';
            Util::jsonExit($result);
        }
        $upload_name = $_FILES['file_order'];
        $tmp_name = $upload_name['tmp_name'];
        if (Upload::getExt($upload_name['name']) != 'csv') {
            $result['error'] = '请上传csv格式文件';
            Util::jsonExit($result);
        }
        
        $pay_type=_Request::getInt('pay_type');
        if(empty($pay_type)){
            $result['error'] = '请选择支付方式';
            Util::jsonExit($result);
        }
        
        
        $datalist = array();
        $pdolist  = array();
        $file = fopen($tmp_name, 'r');
        //基础数据校验
        $i=0;$j=0;
        while ($datav = fgetcsv($file)) {
            if($i==0){
                $i++;
                continue;
            }
            $datav = array_map('trim',$datav);
            $order_sn  = iconv('gbk','utf-8',$datav[0]);
            $pay_money = iconv('gbk','utf-8',$datav[1]);
            if($order_sn=="" && $pay_money=""){
                $i++;
                continue;
            }else if($order_sn==''){
                $msg ="第{$i}行 ，订单号不能为空";
                Util::rollbackExit($msg,$pdolist);
            }else if($pay_money<=0){
                $msg ="第{$i}行 ，支付金额不能小于等于0";
                Util::rollbackExit($msg,$pdolist);
            }
            
            $i++;$j++;
            $datalist[$i] = array('order_sn'=>$order_sn,'pay_money'=>$pay_money);
            
        }
        if($j==0){
            $msg ="文件内没有订单数据";
            Util::rollbackExit($msg,$pdolist);
        }
        $model = new AppOrderPayActionModel(29);
        $salesModel = new SalesModel(27);
        $bespokeInfoModel = new AppBespokeInfoModel(18);
        $baseSalepolicyGoods = new BaseSalepolicyGoodsModel(18);
        $diamondModel = new SelfDiamondModel(20);
        
        $pdolist[29] = $model->db()->db();
        $pdolist[28] = $salesModel->db()->db();
        $pdolist[18] = $bespokeInfoModel->db()->db();
        try{
            foreach ($pdolist as $pdo){
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
                $pdo->beginTransaction(); //开启事务
            }
        }catch(Exception $e){
            $msg = "批量事物开启失败";
            Util::rollbackExit($msg,$pdolist);
        }
        $notice = "点款处理结果：<br/>";
        
        foreach($datalist as $i=>$data){    
            $order_sn = $data['order_sn'];
            $pay_money= $data['pay_money'];
            //1.校验订单基本信息，更新已付和未付，订单支付状态，订单支付日志
            $orderInfo = $salesModel->getOrderAccountInfoByOrderSn($order_sn);
            if(empty($orderInfo)){
                $msg ="第{$i}行 ，订单号{$order_sn}无效，系统不存在此订单号";
                Util::rollbackExit($msg,$pdolist);
            } 
            $order_id = $orderInfo['id'];           
            $params = array(
                'order_info'=>$orderInfo,
                'pay_money'=>$pay_money,
                'pay_type'=>$pay_type                
            );            
            $res = $salesModel->updataOrderForDiankuan($params);
            if($res['error']){
                $msg = "第{$i}行,".$res['msg'];
                Util::rollbackExit($msg,$pdolist);
            } 
            //2.点款单据记录添加
            $pay_action = array(
                'Payaction'=>array(
                    "order_id" => $orderInfo["id"],
                    "order_sn" => $orderInfo['order_sn'],
                    "order_time" =>$orderInfo["create_time"],
                    "deposit" => $pay_money,
                    "order_amount" => $orderInfo["order_amount"],
                    "balance" => 0,
                    "pay_time" => date("Y-m-d H:i:s"),
                    "pay_type" =>$pay_type,
                    "order_consignee" => $orderInfo["consignee"],
                    "attach_sn" =>'',
                    "leader" => $_SESSION["userName"],
                    "leader_check" => date("Y-m-d H:i:s"),
                    "opter_name" => $_SESSION["userName"],
                    "status" => "1",
                    "department" => $orderInfo['department_id'],
                    "system_flg" => "2"
                ),
                'AppReceiptPay' =>array(
                    'order_sn' => $orderInfo['order_sn'],
                    'receipt_sn' => $model->create_receipt('BDK'),//?
                    'customer' => $orderInfo['consignee'],
                    'department' => $orderInfo['department_id'],
                    'pay_fee' => $pay_money,
                    'pay_type'=>$pay_type,
                    'pay_time' =>date("Y-m-d H:i:s"),
                    'card_no' =>'',
                    'card_voucher' =>'',
                    'status' => 1,
                    'print_num' => 0,
                    'pay_user' => $_SESSION['userName'],
                    'remark' => "批量点款",
                    'add_time' => date("Y-m-d H:i:s"),
                    'add_user' => $_SESSION['userName'],
                ),
                'AppReceiptPayLog'=>array(
                    'receipt_action' => '添加点款收据成功',
                    'add_time' => date("Y-m-d H:i:s"),
                    'add_user' => $_SESSION['userName'],
                ),
            );
            $res = $model->addPayLog($pay_action);
            if($res['error']==1){
                $msg = "第{$i}行,".$res['msg'];
                Util::rollbackExit($msg,$pdolist);
            }

            //3.预约信息更新
            if($orderInfo['order_amount']>0&&!empty($orderInfo['bespoke_id'])){
                $data = array('deal_status'=>1);//deal_status=1已成交，2未成交
                $where = "bespoke_id ={$orderInfo['bespoke_id']}";
                try{
                   $bespokeInfoModel->update($data,$where);
                }catch (Exception $e){
                    $msg = "第{$i}行,更新预约状态失败";
                    Util::rollbackExit($msg,$pdolist);
                }
            }
            //4.订单商品下架处理
            $order_detail_list = $salesModel->selectAppOrderDetails("*","order_id='{$order_id}'",1);
            if(!empty($order_detail_list)){
                try{
                    foreach ($order_detail_list as $vo){
                        if($vo['is_stock_goods']==1 && !empty($vo['goods_id'])){
                            
                           if($vo['goods_type']=="lz"){                               
                               $doTip = "货号为".$vo['goods_id']."的裸钻下架";
                               $data = array('status'=>2);
                               $where = "goods_id='{$vo['goods_id']}'";
                               $diamondModel->updateDiamondInfo($data, $where);                                
                           }else{
                                //非裸钻现货下架
                                $doTip = "货号为".$vo['goods_id']."的商品下架";
                                $data = array('is_sale'=>0,'is_valid'=>2);
                                $where = "goods_id='{$vo['goods_id']}'";
                                $baseSalepolicyGoods->update($data, $where);
                           }
                           
                           //现货绑定处理
                           //。。。。。。。。。
                        
                        }
                        
                    }
                }catch (Exception $e){
                    $msg = "第{$i}行,".$doTip."失败";
                    Util::rollbackExit($msg,$pdolist);
                }
                
            }
            
            
            if($orderInfo['referer']=="天生一对加盟商"){
                 //5.天生一对加盟商的订单,点款成功后期货订单自动生成布产单
                 $res = $this->AddBuchanDan($orderInfo,$order_detail_list);
                 if($res['error']){
                     $msg = "第{$i}行,".$res['data'];
                     Util::rollbackExit($msg,$pdolist);
                 }else if(is_array($res['data']) && !empty($res['data'])){
                     try{
                         //1.回写布产信息
                         $buchan_sn_str="";
                         foreach ($res['data'] as $vo){
                             $buchan_sn_str.= $vo['final_bc_sn'].",";
                             //回写订单明细布产ID
                             $data1 = array("bc_id"=>$vo['buchan_sn']);
                             $salesModel->updateAppOrderDetail($data1,"id={$vo['id']}");
                             //回写订单主表，布产状态允许布产
                             $data2 = array('buchan_status'=>2);
                             $salesModel->updateBaseOrderInfo($data2,"order_sn='{$order_sn}'");
                         }
                         //2添加订单操作日志
                         $remark = "财务批量点款后自动布产，布产单号:".trim($buchan_sn_str,",");
                         $salesModel->AddOrderLog($order_sn,$remark);
                     }catch (Exception $e){
                         $msg = "第{$i}行,点款后布产失败，请重新尝试！";
                         Util::rollbackExit($msg,$pdolist);
                     }
                 }
                 //6.天生一对加盟商的订单,点款成功后更新app_order_details.delivery_status配货状态
                 foreach ($order_detail_list as $vo){
                     $data = array();
                     $data['delivery_status'] = $vo['delivery_status'];
                     if($vo['delivery_status']==1){
                         if($vo['is_stock_goods']==1){
                             $data['delivery_status'] = 2;//现货，允许配货
                         }else{
                             //期货，已经出厂或不许布产，配货状态为 允许配货
                             if($vo['buchan_status']==9 || $vo['buchan_status']==11){
                                 $data['delivery_status'] = 2;//允许配货
                             }
                         }
                         //更新订单明细的配货状态
                         $res = $salesModel->updateAppOrderDetail($data,'id='.$vo['id']);
                         if(!$res){
                             $msg = "第{$i}行,更新订单配货状态失败！";
                             Util::rollbackExit($msg,$pdolist);
                         }
                     }
                     
                 }
                 
            }
            
            $notice .="订单{$order_sn}点款成功<br/>";
            
        }  
        //批量提交事物    
        try{
            foreach ($pdolist as $pdo){
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
                $pdo->commit();
            }
            $result['success'] = 1;
            $result['content'] = $notice;
            Util::jsonExit($result);
        }catch(Exception $e){
            $msg = "操作失败！批量事物执行失败";
            Util::rollbackExit($msg,$pdolist);
        }
		
	}



    public function downcsv(){
        $title = array(
            '订单号',
            '金额',
        );

        Util::downloadCsv("批量点款",$title,'');
    }

    //把定制商品进行布产
    public function AddBuchanDan($orderinfo,$detail_goods){
        $order_sn			= $orderinfo['order_sn'];
        $consignee			= $orderinfo['consignee'];
        $customer_source_id = $orderinfo['customer_source_id'];
        $department_id		= $orderinfo['department_id'];
    
        $processorApiModel = new ApiProcessorModel();
        $salesModel = new SalesModel(28);
        //找到此订单是否已经存在布产的单
        $attr_names =array('cart'=>'石重','clarity'=>'净度','color'=>'颜色','zhengshuhao'=>'证书号','caizhi'=>'材质','jinse'=>'金色','jinzhong'=>'金重','zhiquan'=>'指圈','kezi'=>'刻字','face_work'=>'表面工艺');
        if(!empty($detail_goods)){
            $goods_arr = array();
            foreach($detail_goods as $key=>$val){
                if($val['is_stock_goods'] == 1 && empty($val['is_peishi'])){
                    continue;
                }
                $detail_id = $val['id'];
                //查看此商品是否已经开始布产
                $buchan_info = $processorApiModel->GetGoodsRelInfo($detail_id,$order_sn);
                //print_r($buchan_info);exit();
                if(!empty($buchan_info['data'])){
                    continue;
                }
    
                $new_style_info = array();
                foreach ($attr_names as $a_key=>$a_val){
                    $xmp['code'] = $a_key;
                    $xmp['name'] = $a_val;
                    $xmp['value'] = $val[$a_key];
                    $new_style_info[]= $xmp;
                }
                $goods_num = $val['goods_count'];
    
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
                $goods_arr[$key]['diamond_type'] = '0';
                //$goods_arr[$key]['qiban_type'] = '2';//默认
                $goods_arr[$key]['origin_dia_type'] = '0';
                //end
            }
            //添加布产单
            if(!empty($goods_arr)){
                $res = $processorApiModel->AddProductInfo($goods_arr);
                return $res;                
            }
            //$res['buchan_info'] = $buchan_info;
            //$res['goods_arr'] = $goods_arr;
        }
        return array('error'=>0,'data'=>array());
    }

    
}

?>