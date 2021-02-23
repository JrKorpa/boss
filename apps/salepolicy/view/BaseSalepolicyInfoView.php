<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseSalepolicyInfoView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-11 10:54:58
 *   @update	:
 *  -------------------------------------------------
 */
class BaseSalepolicyInfoView extends View
{
	protected $_policy_id;
	protected $_policy_name;
	protected $_policy_start_time;
	protected $_policy_end_time;
	protected $_create_time;
	protected $_create_user;
	protected $_create_remark;
	protected $_check_user;
	protected $_check_time;
	protected $_zuofei_time;
	protected $_check_remark;
	protected $_bsi_status;
	protected $_is_together;
	protected $_is_delete;
	protected $_jiajia;
	protected $_sta_value;
	protected $_is_favourable;
	protected $_is_default;
	protected $_color;//主石颜色
	protected $_clarity;//主石净度
	
	protected $_product_type;
	protected $_tuo_type;
	protected $_huopin_type;
	protected $_cat_type;
	protected $_range_begin;
	protected $_range_end;
    protected $_zhushi_begin;
	protected $_zhushi_end;
	protected $_is_kuanprice;
	protected $_xilie;
	protected $_cert;

	public function get_policy_id(){return $this->_policy_id;}
	public function get_policy_name(){return $this->_policy_name;}
	public function get_policy_start_time(){return $this->_policy_start_time;}
	public function get_policy_end_time(){return $this->_policy_end_time;}
	public function get_create_time(){return $this->_create_time;}
	public function get_create_user(){return $this->_create_user;}
	public function get_create_remark(){return $this->_create_remark;}
	public function get_check_user(){return $this->_check_user;}
	public function get_check_time(){return $this->_check_time;}
	public function get_zuofei_time(){return $this->_zuofei_time;}
	public function get_check_remark(){return $this->_check_remark;}
	public function get_bsi_status(){return $this->_bsi_status;}
	public function get_is_together(){return $this->_is_together?$this->_is_together:1;}
	public function get_is_delete(){return $this->_is_delete;}
	public function get_jiajia(){return $this->_jiajia;}
	public function get_sta_value(){return $this->_sta_value;}
	public function get_is_favourable(){return $this->_is_favourable;}
	public function get_is_default(){return $this->_is_default;}
	
	public function get_product_type(){return $this->_product_type;}
	public function get_tuo_type(){return $this->_tuo_type;}
	public function get_huopin_type(){return $this->_huopin_type;}
	public function get_cat_type(){return $this->_cat_type;}
	public function get_range_begin(){return $this->_range_begin;}
	public function get_range_end(){return $this->_range_end;}
    public function get_zhushi_begin(){return $this->_zhushi_begin;}
	public function get_zhushi_end(){return $this->_zhushi_end;}
	public function get_is_kuanprice(){return $this->_is_kuanprice;}
	public function get_xilie(){return $this->_xilie;}
	public function get_cert(){return $this->_cert;}
	public function get_color(){return $this->_color;}
	public function get_clarity(){return $this->_clarity;}
}
?>