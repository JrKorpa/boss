<?php
/**
 *  -------------------------------------------------
 *   @file		: AppReceiptPayLogView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-21 22:51:50
 *   @update	:
 *  -------------------------------------------------
 */
class AppReceiptPayLogView extends View
{
	protected $_id;
	protected $_receipt_id;
	protected $_receipt_action;
	protected $_add_time;
	protected $_add_user;


	public function get_id(){return $this->_id;}
	public function get_receipt_id(){return $this->_receipt_id;}
	public function get_receipt_action(){return $this->_receipt_action;}
	public function get_add_time(){return $this->_add_time;}
	public function get_add_user(){return $this->_add_user;}

}
?>