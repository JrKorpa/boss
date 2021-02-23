<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseInvoiceInfoView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-28 10:27:45
 *   @update	:
 *  -------------------------------------------------
 */
class BaseInvoiceInfoView extends View
{
	protected $_id;
	protected $_invoice_num;
	protected $_price;
	protected $_title;
	protected $_content;
	protected $_status;
	protected $_create_user;
	protected $_create_time;
	protected $_use_time;
	protected $_cancel_user;
	protected $_cancel_time;
	protected $_order_sn;
	protected $_type;


	public function get_id(){return $this->_id;}
	public function get_invoice_num(){return $this->_invoice_num;}
	public function get_price(){return $this->_price;}
	public function get_title(){return $this->_title;}
	public function get_content(){return $this->_content;}
	public function get_status(){return $this->_status;}
	public function get_create_user(){return $this->_create_user;}
	public function get_create_time(){return $this->_create_time;}
	public function get_use_time(){return $this->_use_time;}
	public function get_cancel_user(){return $this->_cancel_user;}
	public function get_cancel_time(){return $this->_cancel_time;}
	public function get_order_sn(){return $this->_order_sn;}
	public function get_type(){return $this->_type?$this->_type:1;}

}
?>