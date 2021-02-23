<?php
/**
 *  -------------------------------------------------
 *   @file		: OrganizationModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-13 17:16:37
 *   @update	:
 *  -------------------------------------------------
 */
class OrganizationModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'organization';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"主键",
"dept_id"=>"部门id",
"position"=>"职位",
"level"=>"职级",
"user_id"=>"用户id");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url OrganizationController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT `m`.*,`u`.`account`,`u`.`real_name`,IF(`u`.`is_on_work`=1,'在职','离职') as is_on_work,`u`.`role_id`  FROM `".$this->table()."` AS `m` LEFT JOIN `user` AS `u` ON `m`.`user_id`=`u`.`id` WHERE `u`.`is_enabled`='1' AND `u`.`is_deleted`='0'";

		if($where['dept_id'])
		{
			$subsql = "SELECT `id` FROM `department` WHERE `tree_path` LIKE (SELECT concat(`tree_path`,'-',`id`,'-%') FROM `department` WHERE `id`='".$where['dept_id']."')";
			$subsql .= " UNION SELECT `id` FROM `department` WHERE `parent_id`='".$where['dept_id']."'";
			$subsql .= " UNION SELECT `id` FROM `department` WHERE `id`='".$where['dept_id']."'";
			$sql .=" AND `m`.`dept_id` IN (".$subsql.")";
		}
		if($where['position'])
		{
			$sql .=" AND `m`.`position`='".$where['position']."'";	
		}
		if(!empty($where['account']))
		{
			$sql .=" AND `u`.`account`='".$where['account']."'";
		}
		if($where['level'])
		{
			$sql .=" AND `m`.`level`='".$where['level']."'";	
		}
		$sql .= " ORDER BY `m`.`id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	public function getUserTree ($dept_id=0,$id=0) 
	{
		if(!$id)
		{
			$id=0;	
		}
		$sql = "SELECT `u`.`id`,`u`.`account`,`u`.`real_name` FROM `user` AS `u` WHERE `u`.`is_deleted`='0' AND `u`.`is_enabled`='1' AND NOT EXISTS (SELECT `o`.`user_id` FROM `organization` AS `o` WHERE `u`.`id`=`o`.`user_id` AND `o`.`dept_id`='".$dept_id."' AND `o`.`id`<>'".$id."') ";
		return $this->db()->getAll($sql);
	}



	public function hasPosition($position){
			$sql = "SELECT * FROM dict_item WHERE dict_id=8 AND name=".$position;

			return $this->db()->getrow($sql);
	}


	public function hasLevel($level){
		$sql = "SELECT * FROM dict_item WHERE dict_id=7 AND name=".$level;

		return $this->db()->getrow($sql);
	}

	/**
	 * 获取部门人员
	 * @param int $dept_id 部门ID
	 *
	 * @return mixed
	 * @author	: yangxiaotong
	 */
	public function getDeptUser ($dept_id=0)
	{
		$sql = "SELECT `u`.`id`,`u`.`account`,`u`.`real_name` FROM `user` AS `u` WHERE `u`.`is_deleted`='0' AND `u`.`is_enabled`='1' AND EXISTS (SELECT `o`.`user_id` FROM `organization` AS `o` WHERE `u`.`id`=`o`.`user_id` AND `o`.`dept_id`='".$dept_id."') ";
		return $this->db()->getAll($sql);
	}
	
}

?>