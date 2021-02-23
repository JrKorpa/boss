<?php
/**
 *  -------------------------------------------------
 *   @file		: DeductPercentageMoneyView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2018-05-24 10:45:09
 *   @update	:
 *  -------------------------------------------------
 */
class DeductPercentageMoneyView extends View
{
	protected $_id;
	protected $_search_date;
	protected $_department_id;
	protected $_department_name;
	protected $_sales_name;
	protected $_should_ticheng_price;
	protected $_baodi_price;
	protected $_real_add_price;
	protected $_hbh_add_price;
	protected $_undiscount_add_price;
	protected $_cp_add_price;
	protected $_lzxy_add_price;
	protected $_lzfxy_add_price;
	protected $_tejia_add_price;
	protected $_total_add_price;
	protected $_real_return_price;
	protected $_hbh_return_price;
	protected $_undiscount_return_price;
	protected $_cp_return_price;
	protected $_lzxy_return_price;
	protected $_lzfxy_return_price;
	protected $_tejia_return_price;
	protected $_total_return_price;
	protected $_real_deduct_price;
	protected $_hbh_deduct_price;
	protected $_undiscount_deduct_price;
	protected $_cp_deduct_price;
	protected $_lzxy_deduct_price;
	protected $_lzfxy_deduct_price;
	protected $_tejia_deduct_price;
	protected $_total_deduct_price;
	protected $_is_dabiao;
	protected $_bonus_gears;
	protected $_dabiao_price;
	protected $_cp_shipments_price;
	protected $_lzxy_shipments_price;
	protected $_lzfxy_shipments_price;
	protected $_tejia_shipments_price;
	protected $_shipments_total_price;
	protected $_cp_jiti_price;
	protected $_lzxy_jiti_price;
	protected $_lzfxy_jiti_price;
	protected $_tejia_jiti_price;
	protected $_jiti_total_price;
	protected $_ticheng_factor;
	protected $_ticheng_price;
	protected $_tejia_ticheng_price;
	protected $_tsyd_award_price;
	protected $_tsyd_punish_price;
	protected $_real_should_price;
	protected $_xy_award_price;


	public function get_id(){return $this->_id;}
	public function get_search_date(){return $this->_search_date;}
	public function get_department_id(){return $this->_department_id;}
	public function get_department_name(){return $this->_department_name;}
	public function get_sales_name(){return $this->_sales_name;}
	public function get_should_ticheng_price(){return $this->_should_ticheng_price;}
	public function get_baodi_price(){return $this->_baodi_price;}
	public function get_real_add_price(){return $this->_real_add_price;}
	public function get_hbh_add_price(){return $this->_hbh_add_price;}
	public function get_undiscount_add_price(){return $this->_undiscount_add_price;}
	public function get_cp_add_price(){return $this->_cp_add_price;}
	public function get_lzxy_add_price(){return $this->_lzxy_add_price;}
	public function get_lzfxy_add_price(){return $this->_lzfxy_add_price;}
	public function get_tejia_add_price(){return $this->_tejia_add_price;}
	public function get_total_add_price(){return $this->_total_add_price;}
	public function get_real_return_price(){return $this->_real_return_price;}
	public function get_hbh_return_price(){return $this->_hbh_return_price;}
	public function get_undiscount_return_price(){return $this->_undiscount_return_price;}
	public function get_cp_return_price(){return $this->_cp_return_price;}
	public function get_lzxy_return_price(){return $this->_lzxy_return_price;}
	public function get_lzfxy_return_price(){return $this->_lzfxy_return_price;}
	public function get_tejia_return_price(){return $this->_tejia_return_price;}
	public function get_total_return_price(){return $this->_total_return_price;}
	public function get_real_deduct_price(){return $this->_real_deduct_price;}
	public function get_hbh_deduct_price(){return $this->_hbh_deduct_price;}
	public function get_undiscount_deduct_price(){return $this->_undiscount_deduct_price;}
	public function get_cp_deduct_price(){return $this->_cp_deduct_price;}
	public function get_lzxy_deduct_price(){return $this->_lzxy_deduct_price;}
	public function get_lzfxy_deduct_price(){return $this->_lzfxy_deduct_price;}
	public function get_tejia_deduct_price(){return $this->_tejia_deduct_price;}
	public function get_total_deduct_price(){return $this->_total_deduct_price;}
	public function get_is_dabiao(){return $this->_is_dabiao;}
	public function get_bonus_gears(){return $this->_bonus_gears;}
	public function get_dabiao_price(){return $this->_dabiao_price;}
	public function get_cp_shipments_price(){return $this->_cp_shipments_price;}
	public function get_lzxy_shipments_price(){return $this->_lzxy_shipments_price;}
	public function get_lzfxy_shipments_price(){return $this->_lzfxy_shipments_price;}
	public function get_tejia_shipments_price(){return $this->_tejia_shipments_price;}
	public function get_shipments_total_price(){return $this->_shipments_total_price;}
	public function get_cp_jiti_price(){return $this->_cp_jiti_price;}
	public function get_lzxy_jiti_price(){return $this->_lzxy_jiti_price;}
	public function get_lzfxy_jiti_price(){return $this->_lzfxy_jiti_price;}
	public function get_tejia_jiti_price(){return $this->_tejia_jiti_price;}
	public function get_jiti_total_price(){return $this->_jiti_total_price;}
	public function get_ticheng_factor(){return $this->_ticheng_factor;}
	public function get_ticheng_price(){return $this->_ticheng_price;}
	public function get_tejia_ticheng_price(){return $this->_tejia_ticheng_price;}
	public function get_tsyd_award_price(){return $this->_tsyd_award_price;}
	public function get_tsyd_punish_price(){return $this->_tsyd_punish_price;}
	public function get_real_should_price(){return $this->_real_should_price;}
	public function get_xy_award_price(){return $this->_xy_award_price;}

}
?>