<?php
/**
 *  -------------------------------------------------
 *   @file		: ShopCfgView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-30 10:27:57
 *   @update	:
 *  -------------------------------------------------
 */
class ShopCfgView extends View
{
	protected $_id;
	protected $_shop_name;
	protected $_short_name;
	protected $_official_webiste_show;
	protected $_shop_type;
	protected $_shop_address;
	protected $_country_id;
	protected $_province_id;
	protected $_city_id;
	protected $_regional_id;
	protected $_shop_phone;
	protected $_shop_time;
	protected $_shop_traffic;
	protected $_shop_dec;
	protected $_second_url;
	protected $_order;
	protected $_create_user;
	protected $_create_time;
	protected $_is_delete;
	protected $_area;
	protected $_start_shop_time;
	protected $_shop_status;
	protected $_baidu_maps;
	protected $_shopowner;
	protected $_shopowner_tel;
	protected $_shopowner_mail;
	protected $_dealer_name;
	protected $_regional_manager;
	protected $_join_type;
	protected $_shop_responsible_name;
	protected $_shop_responsible_tel;
	protected $_shop_responsible_mail;
	protected $_contract_status;
	protected $_contract_start_time;
	protected $_contract_end_time;
	protected $_trademark_use_fee;
	protected $_credit_guarantee_fee;
	protected $_security_user;
	protected $_diamond_gem_fee;
	protected $_su_jin_fee;
	protected $_gia_diamond_fee;
	protected $_other_diamond_fee;
	protected $_stock_index;
	protected $_development_index;
	protected $_remarks;


	public function get_id(){return $this->_id;}
	public function get_shop_name(){return $this->_shop_name;}
	public function get_short_name(){return $this->_short_name;}
	public function get_official_webiste_show(){return $this->_official_webiste_show;}
	public function get_shop_type(){return $this->_shop_type;}
	public function get_country_id(){return $this->_country_id;}
	public function get_province_id(){return $this->_province_id;}
	public function get_city_id(){return $this->_city_id;}
	public function get_regional_id(){return $this->_regional_id;}
	public function get_shop_address(){return $this->_shop_address;}
	public function get_shop_phone(){return $this->_shop_phone;}
	public function get_shop_time(){return $this->_shop_time;}
	public function get_shop_traffic(){return $this->_shop_traffic;}
	public function get_shop_dec(){return $this->_shop_dec;}
	public function get_second_url(){return $this->_second_url;}
	public function get_order(){return $this->_order;}
	public function get_create_user(){return $this->_create_user;}
	public function get_create_time(){return $this->_create_time;}
	public function get_is_delete(){return $this->_is_delete;}
	public function get_area(){return $this->_area;}
	public function get_start_shop_time(){return $this->_start_shop_time;}
	public function get_shop_status(){return $this->_shop_status;}
	public function get_baidu_maps(){return $this->_baidu_maps;}
	public function get_shopowner(){return $this->_shopowner;}
	public function get_shopowner_tel(){return $this->_shopowner_tel;}
	public function get_shopowner_mail(){return $this->_shopowner_mail;}
	public function get_dealer_name(){return $this->_dealer_name;}
	public function get_regional_manager(){return $this->_regional_manager;}
	public function get_join_type(){return $this->_join_type;}
	public function get_shop_responsible_name(){return $this->_shop_responsible_name;}
	public function get_shop_responsible_tel(){return $this->_shop_responsible_tel;}
	public function get_shop_responsible_mail(){return $this->_shop_responsible_mail;}
	public function get_contract_status(){return $this->_contract_status;}
	public function get_contract_start_time(){return $this->_contract_start_time;}
	public function get_contract_end_time(){return $this->_contract_end_time;}
	public function get_trademark_use_fee(){return $this->_trademark_use_fee;}
	public function get_credit_guarantee_fee(){return $this->_credit_guarantee_fee;}
	public function get_security_user(){return $this->_security_user;}
	public function get_diamond_gem_fee(){return $this->_diamond_gem_fee;}
	public function get_su_jin_fee(){return $this->_su_jin_fee;}
	public function get_gia_diamond_fee(){return $this->_gia_diamond_fee;}
	public function get_other_diamond_fee(){return $this->_other_diamond_fee;}
	public function get_stock_index(){return $this->_stock_index;}
	public function get_development_index(){return $this->_development_index;}
	public function get_remarks(){return $this->_remarks;}

}
?>