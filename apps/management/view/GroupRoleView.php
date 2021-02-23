<?php
/**
 *  -------------------------------------------------
 *   @file		: GroupRoleView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-22 11:13:09
 *   @update	:
 *  -------------------------------------------------
 */
class GroupRoleView extends View
{
	protected $_id;
	protected $_group_id;
	protected $_role_id;


	public function get_id(){return $this->_id;}
	public function get_group_id(){return $this->_group_id;}
	public function get_role_id(){return $this->_role_id;}

	public function getRoleTree () 
	{
		$m = $this->getModel();
		return $m->getRoleTree($this->get_group_id());
	}

	public function setGroupId ($id) 
	{
		$this->_group_id = $id;
	}
}
?>