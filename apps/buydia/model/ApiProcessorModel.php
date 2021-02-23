<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiProcessorModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Zhanglijuan <82739364@qq.com>
 *   @date		: 2015年1月20日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiProcessorModel
{
	/**获取加工商列表**/
	//默认取有效的供应商，如果要取全部的，传入空的。
	function GetSupplierList($keys=array('status'), $vals=array(1))
	{
		$ret = ApiModel::process_api($keys,$vals,'GetSupplierList');
		return $ret['data'];
	}
}

?>