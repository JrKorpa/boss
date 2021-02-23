<?php
/**
 *  -------------------------------------------------
 *   @file		: GroupRoleModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-22 11:13:09
 *   @update	:
 *  -------------------------------------------------
 */
class GroupRoleModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'group_role';
        $this->_dataObject = array("id"=>"主键",
"group_id"=>"组id",
"role_id"=>"角色id");
		parent::__construct($id,$strConn);
	}

	public function pageList ($where,$page,$pageSize=10,$useCache=true) 
	{
		$sql = "SELECT m.id,r.label,r.code FROM `".$this->table()."` AS m LEFT JOIN `role` AS r ON m.role_id=r.id";
		if($where['group_id'])
		{
			$sql .=" WHERE m.group_id=".$where['group_id'];	
		}
		$sql .=" ORDER BY m.id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	public function getRoleTree ($group_id) 
	{
		$sql = "SELECT id,label,code FROM `role` WHERE NOT EXISTS (SELECT * FROM `group_role` WHERE role.id=group_role.role_id and group_role.group_id=".$group_id.") AND `is_deleted`=0";
		if(Auth::$userType>1)
		{
			$sql .=" AND id>1";	//不可以给组添加授权管理员角色，只能由超级管理员操作
		}
		return $this->db()->getAll($sql);
	}
}

?>