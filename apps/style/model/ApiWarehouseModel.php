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
class ApiWarehouseModel
{
    function getGoodsInfoByGoods($where){
       foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }

        $ret=ApiModel::warehouse_api($keys,$vals,'GetGoodsInfoByGoods');
        return $ret;
    }

}

?>