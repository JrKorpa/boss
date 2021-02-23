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
class SelfDiamondModel extends SelfModel
{
	
	protected $db;
    function __construct ($strConn="")
	{
		parent::__construct($strConn);
	}
    
	/*
	*通过证书号获取裸钻信息
	*
	*/
    public function getGoodsTypeByCertId($cert_id,$cert_id2=''){
		if(!empty($cert_id2)){
			$sql ="select good_type from front.diamond_info_all where cert_id='".$cert_id."' OR cert_id ='".$cert_id2."'";
		}else{
			$sql ="select good_type from front.diamond_info_all where cert_id='".$cert_id."'";
		}
		return $this->db()->getOne($sql);

    }

}

?>