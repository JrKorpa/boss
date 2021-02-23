<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillPayView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-30 18:25:39
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillPayView extends View
{
	protected $_id;
	protected $_bill_id;
	protected $_pro_id;
	protected $_pro_name;
	protected $_pay_content;
	protected $_pay_method;
	protected $_tax;
	protected $_amount;


	public function get_id(){return $this->_id;}
	public function get_bill_id(){return $this->_bill_id;}
	public function get_pro_id(){return $this->_pro_id;}
	public function get_pro_name(){return $this->_pro_name;}
	public function get_pay_content(){return $this->_pay_content;}
	public function get_pay_method(){return $this->_pay_method;}
	public function get_tax(){return $this->_tax;}
	public function get_amount(){return $this->_amount;}

}
?>