<?php
/**
 *  -------------------------------------------------
 *   @file		: PayApplyGoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-08 21:53:13
 *   @update	:
 *  -------------------------------------------------
 */
class PayApplyGoodsModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'pay_apply_goods';
        $this->_dataObject = array();
		parent::__construct($id,$strConn);
	}

	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";

		if($where['apply_id'] !== "")
		{
			$sql .= " AND apply_id = ".$where['apply_id'];
		}
		$sql .= " ORDER BY id ASC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	function getDataOfApplyId($apply_id)
	{
		$sql = "select * from ".$this->table();
		$sql .= " where apply_id = '$apply_id'";

		return $this->db()->getAll($sql);
	}



	public function addData($id='',$datas)
	{
		foreach($datas as $k => $v)
		{
			$datas[$k]['apply_id'] = $id;
		}
		if($datas)
		{
			return $this->insertAll($datas);
		}
	}

	public function update($valueArr,$whereArr)
	{
		$field = '';
		$where = ' 1';
		foreach($valueArr as $k => $v)
		{
			$field .= "$k = '$v',";
		}
		foreach($whereArr as $k => $v)
		{
			$where .= " AND $k = '$v'";
		}
		$field = substr($field,0,-1);
		$sql = "UPDATE ".$this->table()." SET ".$field;
        $sql .= " WHERE ".$where;
		return $this->db()->query($sql,array());
	}

	public function deleteOfApplyId($apply_id)
	{
		$sql = "delete from ".$this->table();
		$sql .= " where apply_id = ".$apply_id;
		return $this->db()->query($sql,array());
	}
}

?>