<?php
/**
 *  -------------------------------------------------
 *   @file		: JxcWholesaleView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-25 19:01:18
 *   @update	:
 *  -------------------------------------------------
 */
class JxcWholesaleView extends View
{
	protected $_wholesale_id;
	protected $_wholesale_sn;
	protected $_wholesale_name;
	protected $_wholesale_credit;
	protected $_wholesale_status;
	protected $_add_name;
	protected $_add_time;
	protected $_sign_required;
	protected $_sign_company;


	public function get_wholesale_id(){return $this->_wholesale_id;}
	public function get_wholesale_sn(){return $this->_wholesale_sn;}
	public function get_wholesale_name(){return $this->_wholesale_name;}
	public function get_wholesale_credit(){return $this->_wholesale_credit;}
	public function get_wholesale_status(){return $this->_wholesale_status;}
	public function get_add_name(){return $this->_add_name;}
	public function get_add_time(){return $this->_add_time;}

	public function get_sign_required(){return $this->_sign_required;}
	public function get_sign_company(){return $this->_sign_company;}
}
?>