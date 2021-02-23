<?php
/**
 *  -------------------------------------------------
 *   @file		: AppReceiptDepositView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-19 14:29:54
 *   @update	:
 *  -------------------------------------------------
 */
class AppReceiptDepositView extends View
{
	protected $_id;
	protected $_order_sn;
	protected $_receipt_sn;
	protected $_customer;
	protected $_department;
	protected $_pay_fee;
	protected $_pay_type;
	protected $_card_no;
	protected $_card_voucher;
	protected $_pay_time;
	protected $_status;
	protected $_print_num;
	protected $_pay_user;
	protected $_remark;
	protected $_add_time;
	protected $_add_user;
	protected $_zuofei_time;


	public function get_id(){return $this->_id;}
	public function get_order_sn(){return $this->_order_sn;}
	public function get_receipt_sn(){return $this->_receipt_sn;}
	public function get_customer(){return $this->_customer;}
	public function get_department(){return $this->_department;}
	public function get_pay_fee(){return $this->_pay_fee;}
	public function get_pay_type(){return $this->_pay_type;}
	public function get_card_no(){return $this->_card_no;}
	public function get_card_voucher(){return $this->_card_voucher;}
	public function get_pay_time(){return $this->_pay_time;}
	public function get_status(){return $this->_status;}
	public function get_print_num(){return $this->_print_num;}
	public function get_pay_user(){return $this->_pay_user;}
	public function get_remark(){return $this->_remark;}
	public function get_add_time(){return $this->_add_time;}
	public function get_add_user(){return $this->_add_user;}
	public function get_zuofei_time(){return $this->_zuofei_time;}

}
?>