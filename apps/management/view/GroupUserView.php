<?php
/**
 *  -------------------------------------------------
 *   @file		: GroupUserView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-22 11:36:28
 *   @update	:
 *  -------------------------------------------------
 */
class GroupUserView extends View
{
	protected $_id;
	protected $_user_id;
	protected $_group_id;


	public function get_id(){return $this->_id;}
	public function get_user_id(){return $this->_user_id;}
	public function get_group_id(){return empty($this->_group_id) ? 0 : $this->_group_id;}


	public function set_group_id ($group_id)
	{
		$this->_group_id = $group_id;
	}

	public function getUserTree ()
	{
		$m = $this->getModel();
		//var_dump($this->get_group_id());exit;
		return $m->getUserTree($this->get_group_id(),$this->get_id());
	}

}
?>