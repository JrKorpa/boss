<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductIqcOpraModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-12 15:15:10
 *   @update	:
 *  -------------------------------------------------
 */
class ProductIqcOpraModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'product_iqc_opra';
        $this->_dataObject = array("id"=>"ID",
"shipment_id"=>"出货明细ID",
"sj_num"=>"实际交货数量",
"bf_num"=>"报废数量",
"iqc_num"=>"IQC未过数量",
"info"=>"备注",
"opra_uid"=>"操作人ID",
"opra_uname"=>"操作人姓名",
"opra_time"=>"操作时间");
		parent::__construct($id,$strConn);
	}
	//根据布产号取iqc质检通过列表
	function getListOfBcid ($bc_id)
	{
		$sql = "SELECT iqc.*,ps.shipment_number FROM `".$this->table()."` as iqc,product_shipment as ps WHERE ps.id = iqc.shipment_id and ps.bc_id =  ".$bc_id;
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getAll($sql);
		return $data;
	}
	//根据布产号取实际交货总数量
	function getSumNumOfBcid($bc_id)
	{
		$sql = "SELECT SUM(sj_num) as sj_num,SUM(bf_num) as bf_num,SUM(iqc_num) as iqc_num FROM ".$this->table()." as iqc,product_shipment as ps WHERE ps.id = iqc.shipment_id and ps.bc_id =  ".$bc_id;
		$num = $this->db()->getRow($sql);
		if(empty($num['sj_num']))$num['sj_num'] = 0;
		if(empty($num['bf_num']))$num['bf_num'] = 0;
		if(empty($num['iqc_num']))$num['iqc_num'] = 0;
		return $num;
	}
}

?>