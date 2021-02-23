<?php
/**
 *  -------------------------------------------------
 *   @file		: AppCouponPolicyView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-11 15:46:48
 *   @update	:
 *  -------------------------------------------------
 */
class AppCouponPolicyView extends View
{
	protected $_id;
	protected $_policy_name;
	protected $_policy_desc;
	protected $_policy_price;
	protected $_policy_status;
	protected $_policy_type;
	protected $_valid_time_start;
	protected $_valid_time_end;
	protected $_create_time;
	protected $_create_user;
	protected $_check_time;
	protected $_check_user;


	public function get_id(){return $this->_id;}
	public function get_policy_name(){return $this->_policy_name;}
	public function get_policy_desc(){return $this->_policy_desc;}
	public function get_policy_price(){return $this->_policy_price;}
	public function get_policy_status(){return $this->_policy_status;}
	public function get_policy_type(){return $this->_policy_type;}
	public function get_valid_time_start(){return $this->_valid_time_start;}
	public function get_valid_time_end(){return $this->_valid_time_end;}
	public function get_create_time(){return $this->_create_time;}
	public function get_create_user(){return $this->_create_user;}
	public function get_check_time(){return $this->_check_time;}
	public function get_check_user(){return $this->_check_user;}
    
    
    public function getCouponTypeList($param=0) {
        $model = new AppCouponTypeModel(17);
        return $model->getCouponTypeList();
    }

}
?>