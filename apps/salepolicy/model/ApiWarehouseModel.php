<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiProModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	:
 *   @date		: 2015年1月19日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiWarehouseModel
{

	/** 获取公司下的仓库**/
	function getWarehouseTree($company_id){
        $keys = array('company_id');
        $vals = array($company_id);
        $ret=ApiModel::warehouse_api($keys,$vals,'GetWarehouseList');
        return $ret['return_msg'];
    }

    function getWaregoodisonsale($goods_id){
        $keys = array('goods_id');
        $vals = array($goods_id);
        $ret=ApiModel::warehouse_api($keys,$vals,'GetGoodsInfoByGoods');
        return $ret['return_msg'];
    }



}

?>