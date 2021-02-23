<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiWarehouseModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: zhenghan
 *   @date		: 2015年7月13日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiWarehouseModel
{
	/*获取客户姓名by订单号*/
	public static function GetWarehouseGoodsByGoodsid($goods_id){
		$keys=array('goods_id');
		$vals=array($goods_id);
		$ret=ApiModel::warehouse_api($keys,$vals,'GetWarehouseGoodsByGoodsid');
		return $ret;
	}

}

?>