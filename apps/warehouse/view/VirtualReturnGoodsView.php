<?php
/**
 *  -------------------------------------------------
 *   @file		: VirtualReturnGoodsView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-09-12 10:08:11
 *   @update	:
 *  -------------------------------------------------
 */
class VirtualReturnGoodsView extends View
{
	protected $_id;
	protected $_business_type;
	protected $_order_sn;
	protected $_return_status;
	protected $_style_sn;
    protected $_caizhi;
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
	protected $_out_goods_id;
	protected $_place_company_id;
	protected $_place_company_name;
	protected $_place_warehouse_id;
	protected $_place_warehouse_name;
	protected $_guest_name;
	protected $_guest_contact;
	protected $_return_remark;
	protected $_without_apply_time;
	protected $_apply_user;
	protected $_exist_account_gid;
	protected $_weixiu_fee;


	public function get_id(){return $this->_id;}
	public function get_business_type(){return $this->_business_type;}
	public function get_order_sn(){return $this->_order_sn;}
	public function get_return_status(){return $this->_return_status;}
	public function get_style_sn(){return $this->_style_sn;}
    public function get_caizhi(){return $this->_caizhi;}
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
	public function get_out_goods_id(){return $this->_out_goods_id;}
	public function get_place_company_id(){return $this->_place_company_id;}
	public function get_place_company_name(){return $this->_place_company_name;}
	public function get_place_warehouse_id(){return $this->_place_warehouse_id;}
	public function get_place_warehouse_name(){return $this->_place_warehouse_name;}
	public function get_guest_name(){return $this->_guest_name;}
	public function get_guest_contact(){return $this->_guest_contact;}
	public function get_return_remark(){return $this->_return_remark;}
	public function get_without_apply_time(){return $this->_without_apply_time;}
	public function get_apply_user(){return $this->_apply_user;}
	public function get_exist_account_gid(){return $this->_exist_account_gid;}
	public function get_weixiu_fee(){return $this->_weixiu_fee;}

}
?>