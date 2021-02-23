<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseOutVoucherView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 21:07:31
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseOutVoucherView extends View
{
	protected $_id;
	protected $_voucher_outno;
	protected $_kela_order_sn;
	protected $_voucher_type;
	protected $_voucher_stauts;
	protected $_warehouse_id;
	protected $_company_id;
	protected $_goods_num;
	protected $_cost_price;
	protected $_sales_price;
	protected $_addby_id;
	protected $_add_time;
	protected $_addby_ip;
	protected $_check_id;
	protected $_check_time;
	protected $_check_ip;
	protected $_note;
	protected $_is_deleted;


	public function get_id(){return $this->_id;}
	public function get_voucher_outno(){return $this->_voucher_outno;}
	public function get_kela_order_sn(){return $this->_kela_order_sn;}
	public function get_voucher_type(){return $this->_voucher_type;}
	public function get_voucher_stauts(){return $this->_voucher_stauts;}
	public function get_warehouse_id(){return $this->_warehouse_id;}
	public function get_company_id(){return $this->_company_id;}
	public function get_goods_num(){return $this->_goods_num;}
	public function get_cost_price(){return $this->_cost_price;}
	public function get_sales_price(){return $this->_sales_price;}
	public function get_addby_id(){return $this->_addby_id;}
	public function get_add_time(){return $this->_add_time;}
	public function get_addby_ip(){return $this->_addby_ip;}
	public function get_check_id(){return $this->_check_id;}
	public function get_check_time(){return $this->_check_time;}
	public function get_check_ip(){return $this->_check_ip;}
	public function get_note(){return $this->_note;}
	public function get_is_deleted(){return $this->_is_deleted;}

}
?>