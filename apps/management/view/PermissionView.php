<?php
/**
 *  -------------------------------------------------
 *   @file		: PermissionView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-20 10:39:53
 *   @update	:
 *  -------------------------------------------------
 */
class PermissionView extends View
{
	protected $_id;
	protected $_type;
	protected $_name;
	protected $_code;
	protected $_resource_id;
	protected $_desc;
	protected $_is_deleted;
	protected $_is_system;


	public function get_id(){return $this->_id;}
	public function get_type(){return $this->_type;}
	public function get_name(){return $this->_name;}
	public function get_code(){return $this->_code;}
	public function get_resource_id(){return $this->_resource_id;}
	public function get_desc(){return $this->_desc;}
	public function get_is_deleted(){return $this->_is_deleted;}
	public function get_is_system(){return $this->_is_system;}

	public function getTypeOption () 
	{
		$m = new ResourceTypeModel(1);
		return $m->getTypeOption();
	}
}
?>