<?php
/**
 * 裸钻模块新APiModel类（代替Diamond/Api/api.php）
 *  -------------------------------------------------
 *   @file		: SelfDiamondModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: gaopeng
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class SelfProductInfoModel extends SelfModel
{

    function __construct ($strConn="")
	{
		parent::__construct($strConn);
	}

	/*
	* 批量更新分配工厂时间
	*
	*/
	public function updateTo_factory_timeByP_sn($p_sn,$to_factory_time){
		$sql ="UPDATE kela_supplier.product_info SET to_factory_time='".$to_factory_time."' WHERE p_sn='".$p_sn."'";
		return $this->db()->query($sql);

	}



	//查询默认工厂
	public function getFactoryIdByStyle($style_sn){
	    $sql = "SELECT factory_id FROM front.rel_style_factory WHERE style_sn='{$style_sn}' AND is_factory=1";
	    return  $this->db()->getOne($sql);
	}

}

?>