<?php
/**
 *  -------------------------------------------------
 *   @file		: RoleUserView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-22 11:16:25
 *   @update	:
 *  -------------------------------------------------
 */
class RoleUserView extends View
{
	protected $_id;
	protected $_role_id;
	protected $_user_id;


	public function get_id(){return $this->_id;}
	public function get_role_id(){return $this->_role_id;}
	public function get_user_id(){return $this->_user_id;}

	public function set_role_id ($role_id)
	{
		$this->_role_id = $role_id;
	}

	public function getUserlist($rid){
		$m = $this->getModel();
		return $m->getUserList($rid);
	}

}
?>