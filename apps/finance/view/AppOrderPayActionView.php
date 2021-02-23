<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderPayActionView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-19 18:16:49
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderPayActionView extends View
{
	protected $_pay_id;
	protected $_order_id;
	protected $_order_sn;
	protected $_goods_sn;
	protected $_goods_name;
	protected $_order_time;
	protected $_order_amount;
	protected $_deposit;
	protected $_balance;
	protected $_attach_sn;
	protected $_remark;
	protected $_pay_time;
	protected $_pay_type;
	protected $_pay_channel;
	protected $_order_consignee;
	protected $_pay_account;
	protected $_pay_sn;
	protected $_proof_sn;
	protected $_leader;
	protected $_leader_check;
	protected $_opter_name;
	protected $_repay_time;
	protected $_department;
	protected $_status;
	protected $_pay_checker;
	protected $_pay_check_time;
	protected $_system_flg;


	public function get_pay_id(){return $this->_pay_id;}
	public function get_order_id(){return $this->_order_id;}
	public function get_order_sn(){return $this->_order_sn;}
	public function get_goods_sn(){return $this->_goods_sn;}
	public function get_goods_name(){return $this->_goods_name;}
	public function get_order_time(){return $this->_order_time;}
	public function get_order_amount(){return $this->_order_amount;}
	public function get_deposit(){return $this->_deposit;}
	public function get_balance(){return $this->_balance;}
	public function get_attach_sn(){return $this->_attach_sn;}
	public function get_remark(){return $this->_remark;}
	public function get_pay_time(){return $this->_pay_time;}
	public function get_pay_type(){return $this->_pay_type;}
	public function get_pay_channel(){return $this->_pay_channel;}
	public function get_order_consignee(){return $this->_order_consignee;}
	public function get_pay_account(){return $this->_pay_account;}
	public function get_pay_sn(){return $this->_pay_sn;}
	public function get_proof_sn(){return $this->_proof_sn;}
	public function get_leader(){return $this->_leader;}
	public function get_leader_check(){return $this->_leader_check;}
	public function get_opter_name(){return $this->_opter_name;}
	public function get_repay_time(){return $this->_repay_time;}
	public function get_department(){return $this->_department;}
	public function get_status(){return $this->_status;}
	public function get_pay_checker(){return $this->_pay_checker;}
	public function get_pay_check_time(){return $this->_pay_check_time;}
	public function get_system_flg(){return $this->_system_flg;}

}
?>