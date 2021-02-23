<?php
/**
 *  -------------------------------------------------
 *   @file		: VirtualBillGoodsView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-11-04 16:43:23
 *   @update	:
 *  -------------------------------------------------
 */
class VirtualBillGoodsView extends View
{
	protected $_id;
	protected $_bill_id;
	protected $_virtual_id;
	protected $_business_type;
	protected $_order_sn;
	protected $_goods_id;
	protected $_style_sn;
	protected $_ingredient_color;
	protected $_gold_weight;
	protected $_torr_type;
	protected $_product_line;
	protected $_style_type;
	protected $_finger_circle;
	protected $_credential_num;
	protected $_main_stone_weight;
	protected $_main_stone_num;
	protected $_deputy_stone_weight;
	protected $_deputy_stone_num;
	protected $_resale_price;


	public function get_id(){return $this->_id;}
	public function get_bill_id(){return $this->_bill_id;}
	public function get_virtual_id(){return $this->_virtual_id;}
	public function get_business_type(){return $this->_business_type;}
	public function get_order_sn(){return $this->_order_sn;}
	public function get_goods_id(){return $this->_goods_id;}
	public function get_style_sn(){return $this->_style_sn;}
	public function get_ingredient_color(){return $this->_ingredient_color;}
	public function get_gold_weight(){return $this->_gold_weight;}
	public function get_torr_type(){return $this->_torr_type;}
	public function get_product_line(){return $this->_product_line;}
	public function get_style_type(){return $this->_style_type;}
	public function get_finger_circle(){return $this->_finger_circle;}
	public function get_credential_num(){return $this->_credential_num;}
	public function get_main_stone_weight(){return $this->_main_stone_weight;}
	public function get_main_stone_num(){return $this->_main_stone_num;}
	public function get_deputy_stone_weight(){return $this->_deputy_stone_weight;}
	public function get_deputy_stone_num(){return $this->_deputy_stone_num;}
	public function get_resale_price(){return $this->_resale_price;}

}
?>