<?php
/**
 *  -------------------------------------------------
 *   @file		: DefectiveProductDetailModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-19 00:12:42
 *   @update	:
 *  -------------------------------------------------
 */
class DefectiveProductDetailModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'defective_product_detail';
        $this->_dataObject = array("id"=>"ID",
"info_id"=>"主表关联ID",
"rece_detail_id" => "关联purchase_receipt_detail表ID",
"xuhao"=>"质检关联序号",
"factory_sn"=>"工厂模号",
"bc_sn"=>"布产号",
"customer_name"=>"客户名",
"cat_type"=>"款式分类",
"total"=>"金额",
"info"=>"备注：IQC未过原因");
		parent::__construct($id,$strConn);
	}
//editby zhangruiying getListForInfoid($id,$col='*')togetListForInfoid($id,$col='a.*')解决价格覆盖问题
	function getListForInfoid($id,$col='a.*')
	{
		//$sql = "SELECT $col FROM ".$this->table()." WHERE info_id = ".$id;
                //echo $sql;
                $sql = "SELECT $col,b.`make_name`,b.`make_time`,b.`check_name`,b.`check_time` "
                        . "FROM ".$this->table()." as a,`defective_product` AS b where a.info_id=$id and b.id=".$id;
		return $this->db()->getAll($sql);
	}

}

?>