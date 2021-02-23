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
    //获取仓储商品
    public function getWarehouseGoodsInfo($where){
       foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }

        $ret=ApiModel::warehouse_api($keys,$vals,'GetWarehouseGoodsByGoodsid');
        return $ret;
    }

    //绑定货品
    public function BindGoodsInfoByGoodsId($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }

        $ret=ApiModel::warehouse_api($keys,$vals,'BindGoodsInfoByGoodsId');
        return $ret;
    }
    /*
     * 检查是货号还是款号
     */
    public function CheckStyleSn($goods_sn){
		if(!preg_match ("/^[A-Za-z]/i", $goods_sn)){
			return 1;//纯数字的是货号
		}else{
			return 2;//是款号
		}
	}
	/*
     * 得到结算商列表
     */
	public function getWarehouseBillPayList($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        $ret=ApiModel::warehouse_api($keys,$vals,'GetWarehouseBillPayList');
        return $ret;
    }
	
	/*
     * 更新结价状态
     */
	
	public function UpdateJiejiaByGoodsId($goods_id){
		$ret = ApiModel::warehouse_api(array('goods_id'), array($goods_id), 'UpdateJiejia');
		return $ret;
    }
	
	public function getWarehouseBillGoods($where){
		foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }

        $ret=ApiModel::warehouse_api($keys,$vals,'GetWarehouseBillGoods');
        return $ret;
    }

}

?>