<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiShippingModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	:
 *   @date		: 2015年1月19日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiShippingModel
{

    //根据调拨单号 查询该调拨单号是否绑定了包裹单
    public static function CheckExistBing($bill_no){
        $keys=array('bill_no');
        $vals=array($bill_no);
        $ret=ApiModel::shipping_api($keys,$vals,'CheckExistBing');
        return $ret;
    }
  

}?>