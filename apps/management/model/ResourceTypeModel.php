<?php
/**
 *  -------------------------------------------------
 *   @file		: ResourceTypeModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-19 10:53:58
 *   @update	:
 *  -------------------------------------------------
 */
class ResourceTypeModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'resource_type';
        $this->_dataObject = array("id"=>"主键id",
"label"=>"显示标识",
"code"=>"编码",
"main_table"=>"主表",
"user_table"=>"相关表",
"fields"=>"字段",
"foreigh_key"=>"外键",
"is_system"=>"是否系统内置",
"is_enabled"=>"是否启用",
"is_deleted"=>"是否删除",
"note"=>"备注");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url MessageController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 and is_deleted = 0 ";

		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	public function getTypeOption()
	{
		$sql = "SELECT label,id FROM `".$this->table()."` WHERE is_enabled=1 and is_deleted=0";
		$data = $this->db()->getAll($sql);
		return $data;
	}
}

?>