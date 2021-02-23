<?php
/**
 *  -------------------------------------------------
 *   @file		: UserView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-26 18:00:24
 *   @update	:
 *  -------------------------------------------------
 */
class UserView extends View
{
	protected $_id;
	protected $_account;
	protected $_password;
	protected $_code;
	protected $_real_name;
	protected $_is_on_work;
	protected $_is_enabled;
	protected $_is_deleted;
	protected $_gender;
	protected $_birthday;
	protected $_mobile;
	protected $_phone;
	protected $_qq;
	protected $_email;
	protected $_address;
	protected $_join_date;
	protected $_user_type;
	protected $_up_pwd_date;
	protected $_uin;
	protected $_is_system;
	protected $_is_warehouse_keeper;
	protected $_is_channel_keeper;
	protected $_icd;
	protected $_company_id;
	protected $_role_id;
    protected $_internship;
    protected $_trun_date;

	public function get_id(){return $this->_id;}
	public function get_account(){return $this->_account;}
	public function get_password(){return $this->_password;}
	public function get_code(){return $this->_code;}
	public function get_real_name(){return $this->_real_name;}
	public function get_is_on_work(){return $this->_is_on_work;}
	public function get_is_enabled(){return $this->_is_enabled;}
	public function get_is_deleted(){return $this->_is_deleted;}
	public function get_gender(){return !empty($this->_gender) ? $this->_gender : 0 ;}
	public function get_birthday(){return $this->_birthday;}
	public function get_mobile(){return $this->_mobile;}
	public function get_phone(){return $this->_phone;}
	public function get_qq(){return $this->_qq;}
	public function get_email(){return $this->_email;}
	public function get_address(){return $this->_address;}
	public function get_join_date(){return $this->_join_date;}
	public function get_user_type(){return $this->_user_type ? $this->_user_type : 3;}
	public function get_up_pwd_date(){return $this->_up_pwd_date;}
	public function get_uin(){return $this->_uin;}
	public function get_is_system(){return $this->_is_system;}
	public function get_is_warehouse_keeper(){return !empty($this->_is_warehouse_keeper) ? 1 : 0 ;}
	public function get_is_channel_keeper(){return !empty($this->_is_channel_keeper) ? 1 : 0 ;}
	public function get_icd(){return $this->_icd;}
	public function get_company_id(){return $this->_company_id;}
	public function get_role_id(){return $this->_role_id;}
    public function get_internship(){return $this->_internship;}
    public function get_trun_date(){return $this->_trun_date;}
}
?>