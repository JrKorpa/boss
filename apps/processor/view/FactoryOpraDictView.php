<?php
/**
 *  -------------------------------------------------
 *   @file		: FactoryOpraDictView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ruir
 *   @date		: 2015-04-13 11:55:49
 *   @update	:
 *  -------------------------------------------------
 */
class FactoryOpraDictView extends View
{
	protected $_id;
	protected $_name;
	protected $_create_emp_time;
	protected $_status;
	protected $_edit_time;
	protected $_order_sn;

	public function get_id(){return $this->_id;}
	public function get_name(){return $this->_name;}
	public function get_create_emp_time(){return $this->_create_emp_time;}
	public function get_edit_time(){return $this->_edit_time;}
	public function get_order_sn(){return $this->_order_sn;}
	public function get_status(){return $this->_status;}
}
?>