<?php
/**
 *  -------------------------------------------------
 *  仓储接口文件
 *   @file		    : ApiWarehouseMode.php
 *   @link		    :  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	    :
 *   @date		    : 2015年4月13日
 *   @update	    :
 *  -------------------------------------------------
 */
class ApiWarehouseModel
{

    /**
     * 获取所有有效仓库
     */
    public static function getAllWarehouse(){
        $keys=array('all_warehouse');
        $vals=array('1');
        $ret=ApiModel::warehouse_api($keys,$vals,'getAllWarehouse');
        return $ret;
    }

    /**
     * 获取商品属性
     */
    public static function getGoodsAttrs($goods_attr){
        $keys=array('goods_attr');
        $vals=array($goods_attr);
        $ret=ApiModel::warehouse_api($keys,$vals,'getGoodsAttrs');
        return $ret;
    }

    public function getGoodsInfo($where){
        if(!is_array($where)||empty($where)){
            return false;
        }
        $keys=array('where');
        $vals=array($where);
        $ret=ApiModel::warehouse_api($keys,$vals,'getGoodsInfoByAttr');
        return $ret;
    }
//去仓库绑定货
    public static function BindGoodsInfoByGoodsId($goods,$order_gs_id,$bind_type){
        $keys=array('goods_id','order_goods_id','bind_type');
        $vals=array($goods,$order_gs_id,$bind_type);
        $ret=ApiModel::warehouse_api($keys,$vals,'BindGoodsInfoByGoodsId');
        return $ret;
    }
    //提货单 获取订单绑定的商品信息
    public static function GetBingInfo($order_goods_id){
        $keys = array('order_goods_id');
        $vals = array($order_goods_id);
         $ret=ApiModel::warehouse_api($keys,$vals,'GetOrderDetailBing');
        return $ret;
    }

}

?>