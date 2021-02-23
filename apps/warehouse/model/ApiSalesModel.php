<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiProModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	:
 *   @date		: 2015年1月19日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiSalesModel
{

    //根据订单号 查询这条数据是否存在
    public static function GetExistOrdersn($order_sn){
        $keys=array('order_sn');
        $vals=array($order_sn);
        $ret=ApiModel::sales_api('CheckOrdersn',$keys,$vals);
        return $ret;
    }
    //  1未配货2配货中3配货缺货4已配货
    public static function EditOrderdeliveryStatus($order_sn,$status=2,$time,$user){
        $keys=array('order_sn','delivery_status','time','user_id');
        $vals=array($order_sn,$status,$time,$user);
        $ret=ApiModel::sales_api('EditOrderdeliveryStatus',$keys,$vals);
        return $ret;
    }

    /** 获取 "允许分配" 的订单，带分页 **/
    public static function GetOrderListPages($style_sn,$customer_source_id,$create_time_end,$create_time_start,$sales_channels_id,$is_print_tihuo,$create_user,$delivery_status_str, $order_sn, $page, $page_size = 10){
        if($order_sn) //搜索条件带订单编号
        {
            $keys = array('style_sn','customer_source_id','create_time_end','create_time_start','sales_channels_id','is_print_tihuo','create_user','delivery_status_str',  'order_sn','apply_close','order_status','page', 'page_size');
            $vals = array($style_sn,$customer_source_id,$create_time_end,$create_time_start,$sales_channels_id,$is_print_tihuo,$create_user,$delivery_status_str, $order_sn,0,2, $page, $page_size);
        }
        else
        {
            $keys = array('style_sn','customer_source_id','create_time_end','create_time_start','sales_channels_id','is_print_tihuo','create_user','delivery_status_str', 'apply_close','order_status','page', 'page_size');
            $vals = array($style_sn,$customer_source_id,$create_time_end,$create_time_start,$sales_channels_id,$is_print_tihuo,$create_user,$delivery_status_str,0,2, $page, $page_size);
        }
        //var_dump($vals);
        $ret=ApiModel::sales_api('GetOrderListPage',$keys,$vals);
        return $ret;
    }
    /** 根据order_sn查询订单信息 **/
    public static function GetOrderInfoByOrdersn($order_sn){
        $keys=array('order_sn');
        $vals=array($order_sn);
        $ret=ApiModel::sales_api('GetOrderInfo',$keys,$vals);
        return $ret;
    }
    /** 根据order_id查询订单信息 **/
    public static function GetOrderInfoByOrderId($order_id){
        $keys=array('order_id');
        $vals=array($order_id);
        $ret=ApiModel::sales_api('GetOrderInfoRow',$keys,$vals);
        return $ret;
    }
    /*根据orderid查询外部订单信息*/
    public static function GetOutOrderInfoByOrderId($order_id){
        //exit('ddd');
        $keys=array('order_id');
        $vals=array($order_id);
        $ret = ApiModel::sales_api("GetOutOrderSn",$keys,$vals);
        return $ret;
    }
    /** 获取订单有效商品明细 不带分页 **/
    public static function GetOrderDetailByOrderId($order_sn) {
		$order_list = array();
        $keys=array('order_sn','is_return');
        $vals=array($order_sn,0);
        $ret=ApiModel::sales_api('GetOrderInfoByOrdersn', $keys,$vals);
		if(!$ret['error'])
		{
			$order_list = $ret['return_msg']['data'];
		}
        return $order_list;
    }
    
    /** 获取订单有效商品明细 不带分页 **/
    public static function getGoodsInfoByZhengshuhao($zhengshuhao,$order_id='') {
        $order_list = array();
        $keys=array('zhengshuhao','order_id');
        $vals=array($zhengshuhao,$order_id);
        $ret=ApiModel::sales_api('getOrderInfoByCate', $keys,$vals);
        if(!$ret['error'])
        {
            $order_list = $ret['return_msg'];
        }
        return $order_list;
    }

    /** 根据订单id(order_id) 获取该订单下的明细 **/
    public static function GetDetailByOrderID($order_id){
        $keys=array('order_id');
        $vals=array($order_id);
        $ret=ApiModel::sales_api('getOrderDetailByOrderId', $keys,$vals);
        return $ret;
    }

     /** 获取订单-商品明细(带分页) **/
    public static function GetOrderDetailByOrderIdPage($order_sn, $page , $page_size = 10) {
        if($order_sn) //搜索条件带订单编号
        {
            $order_sn = '\''.$order_sn.'\'';
            $keys = array('order_sn', 'page', 'page_size');
            $vals = array($order_sn, $page, $page_size);
        }
        else
        {
            $keys = array('page', 'page_size');
            $vals = array($page, $page_size);
        }
        $ret=ApiModel::sales_api('GetOrderInfoByOrdersnPage',$keys,$vals);
        return $ret;
    }

    /** 获取会员信息 **/
    public static function GetUserInfor($user_id){
        $keys=array('member_id');
        $vals=array($user_id);
        $ret=ApiModel::sale_member_api($keys, $vals, 'GetMemberByMember_id');
        return $ret;
    }
	//修改发货状态
    public static function EditSendGoodsStatus($order_sn,$status,$time,$user){
        $keys=array('order_sn','send_good_status','time','user_id');
        $vals=array($order_sn,$status,$time,$user);
        $ret=ApiModel::sales_api('EditSendGoodsStatus',$keys,$vals);
        return $ret;
    }

    /** 根据订单号order_sn 将打印提货单状态变更为打印状态 **/
    public static function updatePrintTihuo($order_sn){
        $keys = array('order_sn');
        $vals = array($order_sn);
        $ret=ApiModel::sales_api('updatePrintTihuo',$keys,$vals);
        return $ret;
    }

    /** 根据order_sn 获取订单的配货状态 **/
    public static function GetDeliveryStatus($order_sn, $fields){
        $keys = array('order_sn', 'fields');
        $vals = array("$order_sn", $fields);
        $ret=ApiModel::sales_api('GetDeliveryStatus',$keys,$vals);
        return $ret;
    }

    /** 根据order_id 获取订单的金额信息 **/
    public static function GetOrderAccountRow($order_id, $fields){
        $keys = array('order_id', 'fields');
        $vals = array("$order_id", $fields);
        $ret=ApiModel::sales_api('GetOrderAccountRow',$keys,$vals);
        return $ret;
    }


    /** 根据order_sn 获取订单的配货状态 **/
    public static function GetDeliveryStatus2($order_sn, $fields ,$has_company=''){
        $keys = array('order_sn', 'fields','has_company');
        $vals = array("'$order_sn'", $fields, $has_company);
        $ret=ApiModel::sales_api('GetDeliveryStatus2',$keys,$vals);
        return $ret;
    }


    /** 根据order_sn查询提货单信息 **/
    public static function GetPrintBillsInfo($order_sn){
        $keys=array('order_sn');
        $vals=array($order_sn);
        $ret=ApiModel::sales_api('GetPrintBillsInfo',$keys,$vals);
        return $ret;
    }

    /**
    * 推送 回写订单日志
    */
    public static function addOrderAction($order_no ,  $create_user , $remark){
        $keys =array('order_no','create_user' , 'remark');
        $vals =array($order_no, $create_user, $remark);
        $ret = ApiModel::sales_api('AddOrderLog' , $keys, $vals);
        return $ret;
    }

    /**
     * 未收货列表
     * @param type $where
     */
    public static function getNotReceivingOrder($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        $ret = ApiModel::sales_api('getNotReceivingOrder' , $keys, $vals);
        if($ret['error']){
            return array();
        }else{
            return $ret['return_msg'];
        }
    }


    /**
     * 根据商品自增id查询一条订单商品详情
     * @param type $where
     */
    public static function getOrderDetailsById($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        $ret = ApiModel::sales_api('getGoodsSnByGoodsId' , $keys, $vals);
        if($ret['error']){
            return array();
        }else{
            return $ret['return_msg'];
        }
    }


    /**
     * 根据商品自增id更新一条订单商品详情
     * @param type $where
     */
    public static function updateOrderDetailsById($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        $ret = ApiModel::sales_api('EditOrderGoodsInfo' , $keys, $vals);
        if($ret['error']){
            return array();
        }else{
            return $ret['return_msg'];
        }
    }


	/******************************************************
	fun:VerifyOrderStatus
	description:通过订单号验证获取信息，验证状态；
	 配货销账、FQC质检(通过或未通过只是在操作按钮是触发)、
	 1 检测是否有退款操作 有则不能操作
	 2 检测是否有关闭操作 有则不能操作
	 3 检测支付状态是否是已付款和财务付款状态，不是则不能操作
	 4 检测审核状态是否为已审核，不是则不能操作
	 5 @type 如果是1 则检测 已配货状态/未发货和已到店状态 (质检通过时需验证)
	para:order_sn 订单号
	*******************************************************/
	public static function VerifyOrderStatus($order_sn,$type=false)
	{
		if(empty($order_sn))
		{
			$result['error'] = "订单号不能为空";
			Util::jsonExit($result);
		}
		/*
		$exit_tuikuan = ApiModel::sales_api('isHaveGoodsCheck',array('order_sn'),array($order_sn));
		if (!$exit_tuikuan['return_msg'])
		{
			$result['error'] = "订单号".$order_sn."有未完成的退款申请，不能操作";
			Util::jsonExit($result);
		}*/
		#检测是否有关闭操作 有则不能操作
		$is_close = ApiModel::sales_api('GetOrderInfoBySn',array('order_sn', 'with_normal_items'),array($order_sn, 1));
		if(empty($is_close['return_msg'])){
		    $result['error'] = "<span style='color:red;'>订单号".$order_sn."查询失败！</span>";
		    Util::jsonExit($result);
		}
		
		$order = $is_close['return_msg']['order'];
		
		if ($order['apply_return'] =='2') {
			$result['error'] = "订单号".$order_sn."有未完成的退款申请，不能操作";
			Util::jsonExit($result);
		}
		
		//var_dump($is_close);exit;
		/* if(in_array($is_close['return_msg']['buchan_status'],array(2,3))){
		    //布产状态:1未操作,2已布产,3生产中,4已出厂,5不需布产
		    $result['error'] = "<span style='color:red;'>订单号".$order_sn."布产后还未出厂，请联系技术处理！</span>";
		    Util::jsonExit($result);
		} */		
		if ($order['send_good_status']==2)
		{
		    $result['error'] = "<span style='color:red;'>订单号".$order_sn."已经发过货，不能重复发货，请核实订单状态！</span>";
		    Util::jsonExit($result);
		}
		if ($order['apply_close']==1)
		{
			$result['error'] = "<span style='color:red;'>订单号".$order_sn."已申请关闭或关闭，只允许操作质检未通过</span>";
			Util::jsonExit($result);
		}
		if (!($order['order_pay_status'] == 3 || $order['order_pay_status'] == 4 || $order['order_pay_status'] == 2))
		{
			$result['error'] = "订单号".$order_sn."支付状态不是已付款或财务备案状态，不能操作";
			Util::jsonExit($result);
		}
		if ($order['order_status'] != 2)
		{
			 $result['error'] = "<span style='color:red;'>订单号".$order_sn."已申请关闭或关闭，只允许操作质检未通过</span>";
			 Util::jsonExit($result);
		}
		if ($type)
		{
			#已配货状态 未发货状态 才能质检
			if ($order['delivery_status'] != 5)//已配货 	5
			{
				$result['error'] = "订单号".$order_sn."配货状态错误，需要配货完成才可以";
				Util::jsonExit($result);
			}
			if (!($order['send_good_status'] == 1 || $order['send_good_status'] == 5))//未发货 已到店	1
			{
				 $result['error'] = "订单号".$order_sn."发货状态错误，只有未发货和已到店才可以质检";
				 Util::jsonExit($result);
			}
		}
		
		return $is_close['return_msg'];
	}

}?>