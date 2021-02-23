<?php
/**
 *  -------------------------------------------------
 *   @file		: UserWarehouseView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-30 10:30:05
 *   @update	:
 *  -------------------------------------------------
 */
class UserWarehouseView extends View
{
	protected $_id;
	protected $_user_id;
	protected $_house_id;


	public function get_id(){return $this->_id;}
	public function get_user_id(){return $this->_user_id;}
	public function get_house_id(){return $this->_house_id;}

	public function set_house_id ($id)
	{
		$this->_house_id=$id;
	}

	public function getUserList ($id)
	{
		$m = $this->getModel();
		return $m->getUserList($id);
	}

	public function getHouseKeepers ()
	{
		$m = $this->getModel();
		return $m->getHouseKeepers();
	}
}
?>