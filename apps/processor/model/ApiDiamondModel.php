<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiDiamondModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @date		: 2015年6月27日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiDiamondModel
{

	//根据证书号获取裸砖基本信息
	public function getDiamondInfoByCertId($cert_id)
	{
	    $keys = array('cert_id');
	    $vals = array($cert_id);
	    $ret = ApiModel::diamond_api($keys,$vals,'GetDiamondByCert_id');
	    return $ret;
	}
	//修改裸钻基本信息
	public function editDiamondInfoByCertId($data){
	    $keys = array('data');
	    $vals = array($data);
	    $ret = ApiModel::diamond_api($keys,$vals,'editDiamondInfoMulti');
	    return $ret;
	} 

}

?>