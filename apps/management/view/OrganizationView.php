<?php
/**
 *  -------------------------------------------------
 *   @file		: OrganizationView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-13 17:16:37
 *   @update	:
 *  -------------------------------------------------
 */
class OrganizationView extends View
{
	protected $_id;
	protected $_dept_id;
	protected $_position;
	protected $_level;
	protected $_user_id;


	public function get_id(){return $this->_id;}
	public function get_dept_id(){return empty($this->_dept_id) ? 0 : $this->_dept_id;}
	public function get_position(){return $this->_position;}
	public function get_level(){return $this->_level;}
	public function get_user_id(){return $this->_user_id;}

	public function set_dept_id ($dept_id) 
	{
		$this->_dept_id = $dept_id;
	}

	public function getUserTree () 
	{
		$m = $this->getModel();
		return $m->getUserTree($this->get_dept_id(),$this->get_id());
	}
}
?>