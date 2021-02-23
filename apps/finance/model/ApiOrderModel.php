<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiOrderModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Zhangyuanyuan <1041522879@qq.com>
 *   @date		: 2015年1月19日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiOrderModel
{
    /**
     * 验证转单流水号是否存在
     * @param type $id
     * @return boolean
     */
    function checkReturnGoods($id){
        if(empty($id)){
            return false;
        }
        $keys=array('id');
        $vals=array($id);
        $ret=ApiModel::refund_api($keys,$vals,'checkReturnGoods');
        return $ret;
    }
    
    function getOrderList($order_id){
        if(empty($order_id)){
            return false;
        }
        $keys=array('order_id');
        $vals=array($order_id);
        $ret=ApiModel::sales_api($keys,$vals,'GetOrderInfo');
        return $ret;
    }
    
    function getAddressById($order_id){
        if(empty($order_id)){
            return false;
        }
        $keys=array('order_id');
        $vals=array($order_id);
        $ret=ApiModel::sales_api($keys,$vals,'getAddressById');
        return $ret;
    }
        
    function getOrderPriceInfo($order_id){
        if(empty($order_id)){
            return false;
        }
        $keys=array('order_id');
        $vals=array($order_id);
        $ret=ApiModel::sales_api($keys,$vals,'getOrderPriceInfo');
        return $ret;
    }
    
    function getInvoiceById($order_id){
        if(empty($order_id)){
            return false;
        }
        $keys=array('order_id');
        $vals=array($order_id);
        $ret=ApiModel::sales_api($keys,$vals,'getInvoiceById');
        return $ret;
    }
    
    function getGifts($order_id){
        if(empty($order_id)){
            return false;
        }
        $keys=array('order_id');
        $vals=array($order_id);
        $ret=ApiModel::sales_api($keys,$vals,'getOrderGiftInfo');
        return $ret;
    }
	
	function getOrderListBySn($order_sn){
        if(empty($order_sn)){
            return false;
        }
        $keys=array('order_sn');
        $vals=array($order_sn);
        $ret=ApiModel::sales_api($keys,$vals,'GetOrderInfoBySn');
        return $ret;
    }

    function GetOrderInfoInvoiceBySn($order_sn){
		if(empty($order_sn)){
			return false;
		}
		$keys=array('order_sn');
		$vals=array($order_sn);
		$ret=ApiModel::sales_api($keys,$vals,'GetOrderInfoInvoiceBySn');
		return $ret;
	}

    function updateOrderInfoInvoiceByid($order_id,$updatedata){
        if(empty($order_id)){
            return false;
        }

        $keys=array('order_id','updatedata');
        $vals=array($order_id,$updatedata);
        $ret=ApiModel::sales_api($keys,$vals,'updateOrderInfoInvoiceByid');
        return $ret;
    }
    
    function updateOrderInfoByOrderId($order_id,$updateData) {
        $keys=array('order_id','deposit');
        $vals=array($order_id,$updateData);
        $ret=ApiModel::sales_api($keys,$vals,'updateOrderInfoByOrderId');
        return $ret;
    }
    
    function updateOrderInfoPayDate($order_id,$pay_date='') {
        if($pay_date == ''){
            $keys=array('order_id');
            $vals=array($order_id);
        }else{
            $keys=array('order_id','pay_date');
            $vals=array($order_id,$pay_date);
        }

        $ret=ApiModel::sales_api($keys,$vals,'updateOrderInfoPayDate');
        return $ret;
    }
    
    function updateOrderPayStatus($order_id) {
        $keys=array('order_id');
        $vals=array($order_id);
        $ret=ApiModel::sales_api($keys,$vals,'updateOrderPayStatus');
        return $ret;
    }
    
    function updatePrice($order_id) {
        if(empty($order_id)){
            return FALSE;
        }
        $keys = array('order_id');
        $vals = array($order_id);
        
        $ret = ApiModel::sales_api($keys, $vals, 'updatePrice');
        return $ret;
    }

    //get订单发票列表
    function GetOrderInvoiceList($where)
    {
        if(isset($where['page'])){
            $keys[] ='page';
            $vals[] =$where['page'];
        }
        if(isset($where['page_size'])){
            $keys[] ='page_size';
            $vals[] =$where['page_size'];
        }
        if(isset($where['invoice_num'])){
            $keys[] ='invoice_num';
            $vals[] =$where['invoice_num'];
        }
        if(isset($where['order_sn'])){
            $keys[] ='order_sn';
            $vals[] =$where['order_sn'];
        }
        if(isset($where['title'])){
            $keys[] ='title';
            $vals[] =$where['title'];
        }
        if(isset($where['type'])){
            $keys[] ='type';
            $vals[] =$where['type'];
        }
        if(isset($where['status'])){
            $keys[] ='status';
            $vals[] =$where['status'];
        }
        if(isset($where['create_user'])){
            $keys[] ='create_user';
            $vals[] =$where['create_user'];
        }
        if(isset($where['content'])){
            $keys[] ='content';
            $vals[] =$where['content'];
        }
        if(isset($where['price_start'])){
            $keys[] ='price_start';
            $vals[] =$where['price_start'];
        }
        if(isset($where['price_end'])){
            $keys[] ='price_end';
            $vals[] =$where['price_end'];
        }
        if(isset($where['start_time'])){
            $keys[] ='start_time';
            $vals[] =$where['start_time'];
        }
        if(isset($where['end_time'])){
            $keys[] ='end_time';
            $vals[] =$where['end_time'];
        }
        //var_dump($keys,$vals);die;
        $ret = ApiModel::sales_api($keys, $vals, 'GetOrderInvoiceList');

        return $ret;    
    
    }
    function pageList($where)
    {
        if(isset($where['page'])){
            $keys[] ='page';
            $vals[] =$where['page'];
        }
        if(isset($where['page_size'])){
            $keys[] ='page_size';
            $vals[] =$where['page_size'];
        }
        if(isset($where['order_id'])){
            $keys[] ='order_id';
            $vals[] =$where['order_id'];
        }
        if(isset($where['order_sn'])){
            $keys[] ='order_sn';
            $vals[] =$where['order_sn'];
        }
        if(isset($where['consignee'])){
            $keys[] ='consignee';
            $vals[] =$where['consignee'];
        }
        if(isset($where['order_pay_status'])){
            $keys[] ='order_pay_status';
            $vals[] =$where['order_pay_status'];
        }
        if(isset($where['no_order_status'])){
            $keys[] ='no_order_status';
            $vals[] =$where['no_order_status'];
        }
        if(isset($where['mobile'])){
            $keys[] ='mobile';
            $vals[] =$where['mobile'];
        }
        if(isset($where['start_time'])){
            $keys[] ='start_time';
            $vals[] =$where['start_time'];
        }
        if(isset($where['end_time'])){
            $keys[] ='end_time';
            $vals[] =$where['end_time'];
        }
        if(isset($where['order_status'])){
            $keys[] ='order_status';
            $vals[] =$where['order_status'];
        }
        if(isset($where['order_department'])){
            $keys[] ='order_department';
            $vals[] =$where['order_department'];
        }
        //zt隐藏
        $keys[] ='hidden';
        $vals[] = SYS_SCOPE == 'zhanting'?true:false;
		$ret = ApiModel::sales_api($keys, $vals, 'GetOrderList');
        return $ret;    
    
    }
    
    //获取订单商品
    public function getOrderGoods($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        
        $ret=ApiModel::sales_api($keys,$vals,'getOrderDetailByOrderId');
        return $ret;
    }

    
    //更改配货状态
    public function EditOrderdeliveryStatus($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        
        $ret=ApiModel::sales_api($keys,$vals,'EditOrderdeliveryStatus');
        return $ret;
    }

    //订单日志
    public function mkOrderInfoLog($logInfo){
        $arr = ['order_id','order_sn','order_status','shipping_status','pay_status','create_user','remark'];
        foreach ($logInfo as $k=>$v) {
            if(!in_array($k,$arr)){
                return false;
            }else{
                $keys[$k] = $k;
                $vals[$k] = $v;
            }
        }
        $ret=ApiModel::sales_api($keys,$vals,'mkOrderLog');
        return $ret;
    }


        //取用户
    public function getMember_Info_userId($user_id){
        if(!empty($user_id)){
            $keys[] ='member_id';
            $vals[] =$user_id;
        }else{
            return false;
        }
        $ret = ApiModel::sale_member_api($keys, $vals, 'GetMemberByMember_id');
        return $ret;
    }

    public function GetOrderInfoBySns($order_sn){
        if(!empty($order_sn)){
            $keys[] ='order_sn';
            $vals[] =$order_sn;
        }else{
            return false;
        }
        $ret = ApiModel::sales_api($keys, $vals, 'GetOrderInfoBySns');
        return $ret;
    }

    public function UpdataOrderStus($order_sn,$pay_type,$user_name){
        if(!empty($order_sn)){
            $keys[] ='order_sn';
            $keys[] ='pay_type';
            $keys[] ='user_name';
            $vals[] =$order_sn;
            $vals[] =$pay_type;
            $vals[] =$user_name;
        }else{
            return false;
        }
        $ret = ApiModel::sales_api($keys, $vals, 'UpdataOrderStus');
        return $ret;
    }


}

?>