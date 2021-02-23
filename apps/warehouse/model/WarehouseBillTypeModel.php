<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillTypeModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 10:27:23
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillTypeModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'warehouse_bill_type';
        $this->_dataObject = array("id"=>" ",
			"type_name"=>"单据类型名称",
			"type_SN"=>"单据类型字母标识",
			"opra_name"=>"操作人",
			"opra_uid"=>"操作id",
			"opra_time"=>"操作时间",
			"opra_ip"=>"操作人IP",
			"is_enabled"=>"是否启用",
			"in_out"	=>"出库入库"

		);
		parent::__construct($id,$strConn);
	}
	
	public function pageList($where,$page,$pageSize=10,$useCache=true){
		$sql = "SELECT `id`,`type_name`,`type_SN`,`opra_uid`,`opra_name`,`opra_time`,`opra_ip`,`is_enabled`,`in_out` FROM `{$this->table()}`";
		$sql .= " where 1 ";
		if($where['type_name'] != "")
		{
			$sql .= " AND `type_name` like \"%".addslashes($where['type_name'])."%\"";
		}
		if($where['is_enabled'] !=="")
		{
			$sql .= " AND `is_enabled` = ".addslashes($where['is_enabled']);
		}
		if($where['type_SN'] !=="")
		{
			$sql .= " AND `type_SN` = '".addslashes($where['type_SN'])."'";
		}
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	
	public function check_type_SN($type_SN)
	{
		return $this->db()->getOne("select count(*) from ".$this->table()." where `type_SN` ='$type_SN'");
	}

	public function getList($where = array())
	{
		$sql = "select `type_SN`,`type_name` from ".$this->table()." where 1";
		if(!empty($where['type_SN']))
		{
//			$sql .= " and `type_SN` ='$type_SN'";
			$sql .= " and `type_SN` ='".$where['type_SN']."'";
		}
		if(!empty($where['is_enabled']))
		{
			$sql .= " and `is_enabled` = ".$where['is_enabled'];
		}
		$typelist = $this->db()->getAll($sql);
		foreach($typelist as $key => $value)
		{
			$list[$value['type_SN']] = $value['type_name'];
		}
		return $list;
	}
}

?>