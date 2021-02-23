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
class ApiProModel
{
	/** 获取加工商列表 ---带分页的 **/
	function getProList($arr = array('status' => 1))
	{
		$ret=ApiModel::pro_api('GetProcessorList',$arr);
		return $ret['return_msg'];
	}

	function GetSupplierList($arr = array())
	{
		$ret = ApiModel::pro_api('GetSupplierList',$arr);
		return $ret['return_msg']['data'];
	}

	/** 获取指定加工商的名字 **/
	public function getProName($arr = array('status' => 1)){
		$ret=ApiModel::pro_api('GetProcessorName',$arr);
		if(empty($ret['return_msg'])){
			return $ret['error'];
		}
		return $ret['return_msg'];
	}

}

?>