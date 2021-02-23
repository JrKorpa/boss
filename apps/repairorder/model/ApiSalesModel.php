<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiSalesMode.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	:
 *   @date		: 2015年3月5日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiSalesModel
{
	/*获取客户姓名by订单号*/
	function getConsignee($order_sn){
		$keys=array('order_sn');
		$vals=array($order_sn);
		$ret=ApiModel::sales_api($keys,$vals,'GetConsigneeByorder_sn_copy');
		return $ret;
	}
	
	/*添加订单日志*/
	function add_order_log($order_sn,$create_user,$remark){
		//var_dump($arr_log);exit;
		$keys=array("order_no","create_user","remark");
		$vals=array($order_sn,$create_user,$remark);
		$ret=ApiModel::sales_api($keys,$vals,'AddOrderLog');
		return $ret;
	}
	
	/*更改订单*/
	/* function change_weixiu_status($order_sn,$update_fileds){
		$keys=array("order_sn","update_fileds");
		$vals=array($order_sn,$update_fileds);
		$ret=ApiModel::sales_api($keys,$vals,'updateOrderArr');
		return $ret;
	} */
	
	/*更改订单商品详情维修状态*/
	function change_weixiu_status($detail_id,$update_fileds){
		$keys=array("detail_id","apply_info");
		$vals=array($detail_id,$update_fileds);
		$ret=ApiModel::sales_api($keys,$vals,'EditOrderGoodsInfo');
		return $ret;
	}

    public static function getOrderInfoByDetailsId($details_id) {
        $keys=array('details_id');
        $vals=array($details_id);
        $ret=ApiModel::sales_api($keys,$vals,'GetOrderInfoByDetailsId');
        return $ret;
    }

    public static function getOrderInfoBySn($order_sn) {
        $keys=array('order_sn');
        $vals=array($order_sn);
        $ret=ApiModel::sales_api($keys,$vals,'GetOrderInfoBySn');
        return $ret;
    }
    public static function getOrderInfoRow($order_id) {
        $keys=array('order_id');
        $vals=array($order_id);
        $ret=ApiModel::sales_api($keys,$vals,'GetOrderInfoRow');
        return $ret;
    }
    public static function getOrderDetailByBCId($bc_id) {
        $keys=array('bc_id');
        $vals=array($bc_id);
        $ret=ApiModel::sales_api($keys,$vals,'getOrderDetailByBCId');
        return $ret;
    }
}

?>