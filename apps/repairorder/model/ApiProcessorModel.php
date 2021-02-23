<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiPurchaseModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Zhangyuanyuan <1041522879@qq.com>
 *   @date		: 2015年1月19日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiProcessorModel extends Model 
{
    //添加布产日志 
    function add_order_log($args = array()){
        $ret=ApiModel::process_api($args,'AddOrderLog');
     //   var_dump($ret);exit;
        return $ret;
    }
    
  
}

?>