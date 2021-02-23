<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseCouponView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-11 10:39:22
 *   @update	:
 *  -------------------------------------------------
 */
class BaseCouponView extends View
{
	protected $_id;
	protected $_coupon_code;
	protected $_coupon_price;
	protected $_coupon_type;
	protected $_coupon_policy;
	protected $_coupon_status;
	protected $_create_time;
	protected $_create_user;


	public function get_id(){return $this->_id;}
	public function get_coupon_code(){return $this->_coupon_code;}
	public function get_coupon_price(){return $this->_coupon_price;}
	public function get_coupon_type(){return $this->_coupon_type;}
	public function get_coupon_policy(){return $this->_coupon_policy;}
	public function get_coupon_status(){return $this->_coupon_status;}
	public function get_create_time(){return $this->_create_time;}
	public function get_create_user(){return $this->_create_user;}
    
    
    public function getCouponPolicy($policy=0) {
        $model = new AppCouponPolicyModel(17);
        return $model->getCouponPolicy($policy);
    }

}
?>