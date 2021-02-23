<?php
/**
 *  -------------------------------------------------
 *   @file		: CompanyView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-31 17:31:40
 *   @update	:
 *  -------------------------------------------------
 */
class CompanyView extends View
{
	protected $_id;
	protected $_company_sn;
	protected $_company_name;
	protected $_parent_id;
	protected $_contact;
	protected $_phone;
	protected $_address;
	protected $_bank_of_deposit;
	protected $_account;
	protected $_receipt;
	protected $_is_sign;
	protected $_remark;
	protected $_create_user;
	protected $_create_time;
	protected $_is_deleted;
	protected $_parent_company_name;
	protected $_create_user_name;
	protected $_is_system;
	protected $_is_shengdai;
	protected $_sd_company_id;
	protected $_company_type;
	protected $_processor_id;
	
	protected $_newdata=array();


	public function __construct($obj){

		parent::__construct($obj);
		$model = $this->getModel();
		$this->Companydata = $model->getCompanyTree();
		$this->_parent_company_name = $model->parent_company_name;
		$this->_create_user_name = $model->create_user_name;
	}


	public function get_id(){return $this->_id;}
	public function get_is_system(){return $this->_is_system;}
	public function get_company_sn(){return $this->_company_sn;}
	public function get_company_name(){return $this->_company_name;}
	public function get_parent_id(){return $this->_parent_id;}
	public function get_contact(){return $this->_contact;}
	public function get_phone(){return $this->_phone;}
	public function get_address(){return $this->_address;}
	public function get_bank_of_deposit(){return $this->_bank_of_deposit;}
	public function get_account(){return $this->_account;}
	public function get_receipt(){return $this->_receipt;}
	public function get_is_sign(){return $this->_is_sign;}
	public function get_remark(){return $this->_remark;}
	public function get_create_user(){return $this->_create_user;}
	public function get_create_time(){return $this->_create_time;}
	public function get_is_deleted(){return $this->_is_deleted;}
	public function get_parent_company_name(){return $this->_parent_company_name;}
	public function get_create_user_name(){return $this->_create_user_name;}
	public function get_is_shengdai(){return $this->_is_shengdai;}
	public function get_sd_company_id(){return $this->_sd_company_id;}
	public function get_company_type(){return $this->_company_type;}
	public function get_processor_id(){return $this->_processor_id;}
	
	public function getCompanyTree ($pid=0,$lev=0)
	{
		foreach($this->Companydata as $key=>$val){

			if($val['parent_id']==$pid){
				$val['company_name'] = str_repeat('&nbsp;&nbsp;', $lev).$val['company_name'];
				$this->_newdata[]=$val;
				$this->getCompanyTree($val['id'],$lev+1);
			}

		}
		return $this->_newdata;
	}


	public function getEnumArray (){
		 $arr = $this->dd->getEnumArray("confirm");
		return $arr = array_reverse($arr,true);
	}

}
?>