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
    //添加布产单
    function AddProductInfo($data){
        $keys = array('insert_data','from_type');
        $vals = array($data,2);//2代表订单来源

        $ret=ApiModel::processor_api($keys,$vals,'AddProductInfo');
        return $ret;
    }
    
    //获取是否已经有布产单
    function GetGoodsRelInfo($good_id){
        $keys = array('goods_id');
        $vals = array($good_id);//2代表订单来源

        $ret=ApiModel::processor_api($keys,$vals,'GetGoodsRelInfo');
        return $ret;
    }
    
    //验证订单期货商品是否已布产
    function CheckGoodsProductInfo($good_id,$order_sn){
        $keys = array('goods_id','order_sn');
        $vals = array($good_id,$order_sn);

        $ret=ApiModel::processor_api($keys,$vals,'CheckGoodsProductInfo');
        return $ret;
    }

    //添加布产修改信息
    function AddProductApplyInfo($data){
        $keys = array('apply_data');
        $vals = array($data);//2代表订单来源

        $ret=ApiModel::processor_api($keys,$vals,'AddProductApplyInfo');
        return $ret;
    }
    //根据布产ID获取布产商品属性
    public function getProductAttrByBCId($bc_id){
        $keys = array("bc_id");
        $vals = array($bc_id);
        $ret=ApiModel::processor_api($keys,$vals,'getProductAttrByBCId');
        return $ret;
    }
}

?>