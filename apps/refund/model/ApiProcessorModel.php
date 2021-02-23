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
        $ret=ApiModel::processor_api($keys,$vals,'GetProcessorList');
        return $ret;
    }

    /**
     * 和供应商解除关系
     * @param type $where
     * @return type
     */
    function relieveProduct($where) {
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }

        $ret=ApiModel::processor_api($keys,$vals,'relieveProduct');
        return $ret;
    }
    
}

?>