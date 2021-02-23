<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductShipmentModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-12 14:23:57
 *   @update	:
 *  -------------------------------------------------
 */
class ProductShipmentModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'product_shipment';
        $this->_dataObject = array("id"=>"ID",
			"bc_id"=>"布产单ID",
"shipment_number"=>"出货单号",
"num"=>"出货数量",
"info"=>"备注",
"opra_uid"=>"操作人ID",
"opra_uname"=>"操作人姓名",
"opra_time"=>"操作时间");
		parent::__construct($id,$strConn);
	}

	function pageList ($where)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";

		if($where['bc_id'] !== "")
		{
			$sql .= " AND bc_id = ".$where['bc_id'];
		}

		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getAll($sql);
		return $data;
	}

	//出货单号在布产单中是否存在过（一个出货单号在一个布产单中只能出现一次）
	function getExistNumberOfBcid($bc_id,$shmt_number)
	{
		$sql = "SELECT COUNT(*) FROM ".$this->table()." WHERE bc_id = ".$bc_id." AND shipment_number = '".$shmt_number."'";
		if($this->db()->getOne($sql))
		{
			return true;
		}
		return false;
	}

	//布产的出货总数量
	function getSumNum($bc_id)
	{
		$sql = "SELECT SUM(num) FROM ".$this->table()." WHERE bc_id = ".$bc_id;
		return $this->db()->getOne($sql);
	}

	//布产的报废总数量
	function getSumBfNum($bc_id)
	{
		$sql = "SELECT SUM(bf_num) FROM ".$this->table()." WHERE bc_id = ".$bc_id;
		return $this->db()->getOne($sql);
	}
	
	//根据布产ID删除工厂出货记录
	function deleteByBcId($bc_id)
	{   
	    if(!is_numeric($bc_id)){
	        return false;
	    }
	    $sql = "delete from ".$this->table()." WHERE bc_id = ".$bc_id;
	    return $this->db()->query($sql);
	}

}?>