<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseTypeModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-07 20:30:25
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseTypeModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'purchase_type';
        $this->_dataObject = array("id"=>"采购类型ID",
"t_name"=>"采购类型名称",
"is_auto"=>"是否支持系统自动匹配 1：匹配 0：不匹配",
"add_name"=>"添加人ID",
"add_time"=>"添加时间",
"is_enabled"=>"是否开启：1=开启，0=关闭",
"is_enabled"=>"是否开启：1=开启，0=关闭",
"is_system"=>"是否是系统内置 0：否 / 1：是");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ApplicationController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE `is_deleted`='0'";

		if($where['t_name'] != "")
		{
			$sql .= " AND t_name like \"%".addslashes($where['t_name'])."%\"";
		}

		if($where['is_enabled'] !== "")
		{
			$sql .= " AND is_enabled = ".$where['is_enabled'];
		}
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	function getList($is_enabled = 1)
	{
		$sql = "SELECT id,t_name from ".$this->table()." WHERE is_deleted = '0' and is_enabled = ".$is_enabled;
		$data = $this->db()->getAll($sql);
		return $data;
	}
	function getTname($tid)
	{
		$sql = "SELECT t_name from ".$this->table()." WHERE id = ".$tid;
		$tname = $this->db()->getOne($sql);
		return $tname;
	}

	function getOfname($t_name)
	{
		$sql = "SELECT `id`,`t_name`,`is_auto`,`is_enabled`,`is_deleted`,`is_system` FROM ".$this->table()." WHERE `t_name` = '".$t_name."'";
		$row = $this->db()->getRow($sql);
		return $row;
	}
}

?>