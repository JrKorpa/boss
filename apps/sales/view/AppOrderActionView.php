<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderActionView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-31 12:17:57
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderActionView extends View
{
	protected $_action_id;
	protected $_order_id;
	protected $_order_status;
	protected $_shipping_status;
	protected $_pay_status;
	protected $_create_user;
	protected $_create_time;
	protected $_remark;


	public function get_action_id(){return $this->_action_id;}
	public function get_order_id(){return $this->_order_id;}
	public function get_order_status(){return $this->_order_status;}
	public function get_shipping_status(){return $this->_shipping_status;}
	public function get_pay_status(){return $this->_pay_status;}
	public function get_create_user(){return $this->_create_user;}
	public function get_create_time(){return $this->_create_time;}
	public function get_remark(){return $this->_remark;}

}
?>