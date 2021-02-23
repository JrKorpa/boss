<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseOrderInfoView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2019-06-21 17:54:25
 *   @update	:
 *  -------------------------------------------------
 */
class BaseOrderInfoView extends View
{
	protected $_id;
	protected $_order_sn;
	protected $_old_order_id;
	protected $_bespoke_id;
	protected $_old_bespoke_id;
	protected $_user_id;
	protected $_consignee;
	protected $_mobile;
	protected $_order_status;
	protected $_order_pay_status;
	protected $_order_pay_type;
	protected $_delivery_status;
	protected $_send_good_status;
	protected $_buchan_status;
	protected $_customer_source_id;
	protected $_department_id;
	protected $_create_time;
	protected $_create_user;
	protected $_check_time;
	protected $_check_user;
	protected $_genzong;
	protected $_recommended;
	protected $_recommender_sn;
	protected $_modify_time;
	protected $_order_remark;
	protected $_referer;
	protected $_is_delete;
	protected $_apply_close;
	protected $_is_xianhuo;
	protected $_is_print_tihuo;
	protected $_effect_date;
	protected $_is_zp;
	protected $_pay_date;
	protected $_apply_return;
	protected $_weixiu_status;
	protected $_update_time;
	protected $_shipfreight_time;
	protected $_is_real_invoice;
	protected $_out_company;
	protected $_discount_point;
	protected $_reward_point;
	protected $_jifenma_point;
	protected $_zhuandan_cash;
	protected $_hidden;
	protected $_birthday;
	protected $_profile_id;


	public function get_id(){return $this->_id;}
	public function get_order_sn(){return $this->_order_sn;}
	public function get_old_order_id(){return $this->_old_order_id;}
	public function get_bespoke_id(){return $this->_bespoke_id;}
	public function get_old_bespoke_id(){return $this->_old_bespoke_id;}
	public function get_user_id(){return $this->_user_id;}
	public function get_consignee(){return $this->_consignee;}
	public function get_mobile(){return $this->_mobile;}
	public function get_order_status(){return $this->_order_status;}
	public function get_order_pay_status(){return $this->_order_pay_status;}
	public function get_order_pay_type(){return $this->_order_pay_type;}
	public function get_delivery_status(){return $this->_delivery_status;}
	public function get_send_good_status(){return $this->_send_good_status;}
	public function get_buchan_status(){return $this->_buchan_status;}
	public function get_customer_source_id(){return $this->_customer_source_id;}
	public function get_department_id(){return $this->_department_id;}
	public function get_create_time(){return $this->_create_time;}
	public function get_create_user(){return $this->_create_user;}
	public function get_check_time(){return $this->_check_time;}
	public function get_check_user(){return $this->_check_user;}
	public function get_genzong(){return $this->_genzong;}
	public function get_recommended(){return $this->_recommended;}
	public function get_recommender_sn(){return $this->_recommender_sn;}
	public function get_modify_time(){return $this->_modify_time;}
	public function get_order_remark(){return $this->_order_remark;}
	public function get_referer(){return $this->_referer;}
	public function get_is_delete(){return $this->_is_delete;}
	public function get_apply_close(){return $this->_apply_close;}
	public function get_is_xianhuo(){return $this->_is_xianhuo;}
	public function get_is_print_tihuo(){return $this->_is_print_tihuo;}
	public function get_effect_date(){return $this->_effect_date;}
	public function get_is_zp(){return $this->_is_zp;}
	public function get_pay_date(){return $this->_pay_date;}
	public function get_apply_return(){return $this->_apply_return;}
	public function get_weixiu_status(){return $this->_weixiu_status;}
	public function get_update_time(){return $this->_update_time;}
	public function get_shipfreight_time(){return $this->_shipfreight_time;}
	public function get_is_real_invoice(){return $this->_is_real_invoice;}
	public function get_out_company(){return $this->_out_company;}
	public function get_discount_point(){return $this->_discount_point;}
	public function get_reward_point(){return $this->_reward_point;}
	public function get_jifenma_point(){return $this->_jifenma_point;}
	public function get_zhuandan_cash(){return $this->_zhuandan_cash;}
	public function get_hidden(){return $this->_hidden;}
	public function get_birthday(){return $this->_birthday;}
	public function get_profile_id(){return $this->_profile_id;}

}
?>