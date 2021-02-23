<?php
/**
 *  -------------------------------------------------
 *   @file		: RoleUserModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-22 11:16:25
 *   @update	:
 *  -------------------------------------------------
 */
class RoleUserModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'role_user';
        $this->_dataObject = array("id"=>"序号",
"role_id"=>"角色ID",
"user_id"=>"用户ID");
		parent::__construct($id,$strConn);
	}

	/*
	 * getRoleList,获取角色列表
	 *
	 */
	function getRoleList()
	{
		$sql = "SELECT * FROM `role` WHERE `is_deleted`='0'";
		if(Auth::$userType>1)
		{
			$sql .=" AND id>1";	//只有超级管理员可以给授权管理员角色添加用户和添加授权
		}
		$res = $this->db()->getAll($sql);
		return $res;
	}

	public function getUserlist($rid = 0)
	{

		//$sql = "SELECT id,account,code,real_name FROM `user` WHERE `is_deleted`=0 AND `is_enabled`=1";
		$sql = "SELECT u.id,u.account,u.code,u.real_name from user as u WHERE u.`is_deleted`=0 AND u.`is_enabled`=1 AND u.`user_type`>".Auth::$userType;
		$sql .= " AND NOT EXISTS (SELECT r.user_id from role_user as r WHERE r.user_id = u.id and ";
		$sql .= "r.role_id = ".$rid." )";

		return $this->db()->getAll($sql);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url MessageController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT role_user.id as id,role_user.role_id,`user`.account,`user`.`code`,`user`.real_name,`user`.email,`user`.phone,`user`.mobile,`user`.id as uid from role_user left join `user` on role_user.user_id = `user`.id";

		$sql .= " WHERE 1";

		if($where['role_id'] != "")
		{
			$sql .= " AND role_user.role_id = ".$where['role_id']." + 0";
		}

		$sql .= " ORDER BY role_user.id DESC";

		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	function getRoleUserList($rid = 0)
	{
		$sql = "SELECT u.account,u.`code`,u.real_name,u.id as uid from role_user as ru left join `user` as u on ru.user_id = u.id WHERE ru.role_id = ".$rid;
		$data = $this->db()->getAll($sql);
		return $data;
	}
}

?>