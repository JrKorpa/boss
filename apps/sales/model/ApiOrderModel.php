<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Zhangyuanyuan <1041522879@qq.com>
 *   @date		: 2015年1月19日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiOrderModel extends Model {

    function __construct($id = NULL, $strConn = "") {}
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
        if(isset($where['order_pay_status'])){
            $keys[] ='order_pay_status';
            $vals[] =$where['order_pay_status'];
        }
        $ret = ApiModel::sales_api($keys, $vals, 'GetOrderList');

        return $ret;    
    
    }

    function getOrderByOrderSn($orderSn)
    {
        if(!empty($orderSn)){
            $keys[] ='order_sn';
            $vals[] =$orderSn;
        }else{
            return false;
        }
        $ret = ApiModel::sales_api($keys, $vals, 'GetOrderInfo');
        return $ret;
    }
}

?>