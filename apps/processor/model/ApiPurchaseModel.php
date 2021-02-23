<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiPurchaseMode.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @date		: 2015年6月27日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiPurchaseModel
{

	/** 根据起版ID查询起版信息 **/
	public static function GetQiBianGoodsByQBId($qb_id){
	    
		$keys=array('qb_id');
		$vals=array($qb_id);
		
		$ret=ApiModel::purchase_api($keys,$vals,'GetQiBianGoodsByQBId');
		$data = !$ret['error']?$ret['data']:array();
		return $data;
	}

}

?>