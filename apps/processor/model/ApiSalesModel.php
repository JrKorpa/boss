<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiSalesMode.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: JUAN
 *   @date		: 2015年3月5日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiSalesModel
{
	//推送数据到订单，更改订单下明细的布产状态
    function GetStyleInfoBySn($goods_id,$buchan_status){

		$keys =array('update_data');
		$vals =array(array(array('id'=>$goods_id,'buchan_status'=>$buchan_status)));
		$ret = ApiModel::sales_api($keys, $vals, 'UpdateOrderDetailStatus');
		return $ret;
    }

	/**
	 * 布产货品属性修改：信息回写
	 */
	public function EditOrderGoodsInfo($detail_id,$apply_info){
		$keys =array('detail_id','apply_info');
		$vals =array($detail_id,$apply_info);
		$ret = ApiModel::sales_api($keys, $vals, 'EditOrderGoodsInfo');
		return $ret;
	}

	/**
	* 推送订单日志
	*/
	public static function addOrderAction($order_id , $order_status , $shipping_status , $pay_status , $create_time , $create_user , $remark){
		$keys =array('order_id','order_status', 'shipping_status' , 'pay_status' , 'create_time' , 'create_user' , 'remark');
		$vals =array($order_id, $order_status, $shipping_status, $pay_status, $create_time, $create_user, $remark);
		$ret = ApiModel::sales_api($keys, $vals, 'addOrderAction');
		return $ret;
	}

	/** 根据order_sn查询提货单信息 **/
	public static function GetPrintBillsInfo($order_sn){
		$keys=array('order_sn');
		$vals=array($order_sn);
		$ret=ApiModel::sales_api($keys,$vals,'GetPrintBillsInfo');
		return $ret;
	}

	/** 根据orderid查询外部订单信息 **/
	public static function GetOutOrderInfoByOrderId($order_id){
		$keys=array('order_id');
		$vals=array($order_id);
		$ret = ApiModel::sales_api($keys,$vals,"GetOutOrderSn");
		//var_dump($ret);exit;
		return $ret;
	}

	/** 获取订单有效商品明细 不带分页 **/
	public static function GetOrderDetailByOrderId($order_sn) {
		$order_list = array();
		$keys=array('order_sn','is_return');
		$vals=array($order_sn,0);
		$ret=ApiModel::sales_api($keys,$vals,'GetOrderInfoByOrdersn');
		if(!$ret['error'])
		{
			$order_list = $ret['return_msg']['data'];
		}
		//echo "<pre>";print_r($order_list);exit;
		return $order_list;
	}

	//根据订单号 获取订单信息
	public static function GetDeliveryStatus($order_sn, $fields=''){
		$keys=array('order_sn' , 'fields');
		$vals=array($order_sn, $fields);
		$ret=ApiModel::sales_api($keys,$vals,'GetDeliveryStatus');
		return $ret;
	}

	//通过证书号查询订单信息
	public function getOrderInfoByCate($cate){
		$ret=ApiModel::sales_api(['zhengshuhao'],[$cate],'getOrderInfoByCate');
		return $ret['return_msg'];
	}
	public function getOrderDetailList($where){
	    $keys = array();
	    $vals = array();
	    foreach($where as $key=>$val){
	        $keys[] = $key;
	        $vals[] = $val;
	    }
	    $ret=ApiModel::sales_api($keys,$vals,'getOrderDetailList');
	    return $ret;
	}
	//通过订单编号获取订单信息
	public function getQiBanIdByWhere($order_sn,$goods_sn){
	    $keys=array('order_sn','goods_sn');
		$vals=array($order_sn,$goods_sn);
	    $ret=ApiModel::sales_api($keys,$vals,'getQiBanIdByWhere');
	    return $ret['return_msg'];
	}
	//根据布产ID获取订单商品明细（提示：一个布产ID对应一个商品）
	public function getOrderDetailByBCID($bc_id){
	    $keys=array('bc_id');
	    $vals=array($bc_id);
	    $ret=ApiModel::sales_api($keys,$vals,'getOrderDetailByBCId');
	    return $ret;
	}
}

?>