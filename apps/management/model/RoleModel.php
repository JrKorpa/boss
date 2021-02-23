<?php
/**
 *  -------------------------------------------------
 *   @file		: RoleModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-17 14:29:54
 *   @update	:
 *  -------------------------------------------------
 */
class RoleModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'role';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"主键",
"label"=>"角色名称",
"code"=>"编码",
"note"=>"描述",
"is_deleted"=>"删除标记",
"is_system"=>"系统内置");
		parent::__construct($id,$strConn);
	}
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT `m`.`id`,`m`.`label`,`m`.`code`,`m`.`is_system`,`m`.`note` FROM `".$this->table()."` AS `m` WHERE `is_deleted`='0'";

		if($where['label'] != "")
		{
			$sql .= " AND `m`.`label` LIKE \"%".addslashes($where['label'])."%\"";
		}
		if($where['code'] != "")
		{
			$sql .= " AND `m`.`code` LIKE \"%".addslashes($where['code'])."%\"";
		}
		$sql .= " ORDER BY `m`.`id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	
	function getRoleList(){
		$sql = "SELECT `m`.`id`,`m`.`label`,`m`.`code`,`m`.`is_system`,`m`.`note` FROM `".$this->table()."` AS `m` WHERE `is_deleted`='0'";
	    return $this->db()->getAll($sql);
	}

	function getUserListFromOrganization($depart_id){
        $sql="select *from organization where dept_id='".$depart_id."'";
        return $this->db()->getAll($sql);
	}
}

?>