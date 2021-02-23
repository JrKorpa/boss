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
class ApiSalePolicyModel
{
    //修改可销售商品状态
    function UpdateAppPayDetail($data){

        $keys = array('update_data');
        $vals = array($data);
        $ret=ApiModel::sale_policy_api($keys,$vals,'UpdateAppPayDetail');
        return $ret;
    }
    
    //获取商品的信息
    function getBaseSaleplicyGoods($goods_id){

        $keys = array('goods_id');
        $vals = array($goods_id);
        $ret=ApiModel::sale_policy_api($keys,$vals,'getBaseSaleplicyGoods');
        return $ret;
    }

}

?>