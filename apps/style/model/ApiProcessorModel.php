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
class ApiProcessorModel
{
    function GetSupplierList(){
        $keys = array('status');
        $vals = array(1);
        $ret=ApiModel::processor_api($keys,$vals,'GetSupplierList');
        return $ret;
    }

    function GetSupplierListName($id){
        $keys = array('id');
        $vals = array($id);
        $ret=ApiModel::processor_api($keys,$vals,'GetSupplierName');
        return $ret;
    }

}

?>