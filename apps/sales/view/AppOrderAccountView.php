<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderAccountView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-12 12:23:42
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderAccountView extends View
{
	protected $_id;
	protected $_order_id;
	protected $_order_amount;
	protected $_money_paid;
	protected $_money_unpaid;
	protected $_goods_return_price;
	protected $_real_return_price;
	protected $_shipping_fee;
	protected $_goods_amount;
	protected $_favorable_price;
	protected $_coupon_price;


	public function get_id(){return $this->_id;}
	public function get_order_id(){return $this->_order_id;}
	public function get_order_amount(){return $this->_order_amount;}
	public function get_money_paid(){return $this->_money_paid;}
	public function get_money_unpaid(){return $this->_money_unpaid;}
	public function get_goods_return_price(){return $this->_goods_return_price;}
	public function get_real_return_price(){return $this->_real_return_price;}
	public function get_shipping_fee(){return $this->_shipping_fee;}
	public function get_goods_amount(){return $this->_goods_amount;}
	public function get_favorable_price(){return $this->_favorable_price;}
	public function get_coupon_price(){return $this->_coupon_price;}

}
?>