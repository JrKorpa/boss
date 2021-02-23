<?php
/**
 *  -------------------------------------------------
 *   @file		: PermissionModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-19 12:18:36
 *   @update	:
 *  -------------------------------------------------
 */
class PermissionModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'permission';
        $this->_dataObject = array("id"=>"权限id",
"type"=>"资源类型id",
"name"=>"名称",
"code"=>"编码",
"resource_id"=>"资源id",
"note"=>"描述",
"is_system"=>"系统内置",
"is_deleted"=>"是否删除");
		parent::__construct($id,$strConn);
	}
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT m.*,resource_type.label from permission as m LEFT JOIN resource_type on resource_type.id=m.type";
		$str = '';
		if(isset($where['is_deleted']))
		{
			$str .= "`m`.`is_deleted`='".$where['is_deleted']."' AND ";
		}
		if($where['name'] != "")
		{
			$str .= "`m`.`name` LIKE \"%".addslashes($where['name'])."%\" AND ";
		}
		if($where['type'])
		{
			$str .= "`m`.`type`=".$where['type']." AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}

		$sql .= " ORDER BY m.id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	public function getResource ($type_id)
	{
		$sql = "SELECT * FROM `resource_type` WHERE `id`='{$type_id}'";
		$row = $this->db()->getRow($sql);
		$main_table = $row['main_table'];
		if($row['code']=='MENU')
		{
			$sql = "SELECT id,label FROM `".$main_table."` WHERE `is_deleted`=0 AND `is_enabled`=1 ORDER BY display_order DESC";
		}
		else if($row['code']=='BUTTON')
		{
			$sql = "SELECT main.id,CONCAT(c.label,'-',main.label) AS label FROM `".$main_table."` AS main LEFT JOIN control AS c ON main.c_id=c.id WHERE main.id>3 ORDER BY main.display_order DESC";
		}
		else if($row['code']=='OPERATION')
		{
			$sql = "SELECT main.id,CONCAT(c.label,'-',main.label) AS label FROM `".$main_table."` AS main LEFT JOIN control AS c ON main.c_id=c.id ORDER BY main.id DESC";
		}
		else if($row['code']=='DATA')
		{
			$sql = "SELECT id,label FROM `".$main_table."` ORDER BY id DESC";
		}
		else
		{
			$sql = "SELECT id,label FROM `".$main_table."` ORDER BY id DESC";
		}
		$data = $this->db()->getAll($sql);
		return $data;
	}
}

?>