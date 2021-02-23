<?php
/**
 *  -------------------------------------------------
 *   @file		: AppAdjApplyView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-21 19:03:33
 *   @update	:
 *  -------------------------------------------------
 */
class AppAdjApplyView extends View
{
	protected $_apply_id;
	protected $_pay_apply_number;
	protected $_status;
	protected $_pay_number;
	protected $_make_time;
	protected $_make_name;
	protected $_check_time;
	protected $_check_name;
	protected $_company;
	protected $_prc_id;
	protected $_prc_name;
	protected $_pay_type;
	protected $_amount;
	protected $_total_cope;
	protected $_total_dev;
	protected $_adj_reason;
	protected $_record_type;
	protected $_overrule_reason;
	protected $_fapiao;


	public function get_apply_id(){return $this->_apply_id;}
	public function get_pay_apply_number(){return $this->_pay_apply_number;}
	public function get_status(){return $this->_status;}
	public function get_pay_number(){return $this->_pay_number;}
	public function get_make_time(){return $this->_make_time;}
	public function get_make_name(){return $this->_make_name;}
	public function get_check_time(){return $this->_check_time;}
	public function get_check_name(){return $this->_check_name;}
	public function get_company(){return $this->_company;}
	public function get_prc_id(){return $this->_prc_id;}
	public function get_prc_name(){return $this->_prc_name;}
	public function get_pay_type(){return $this->_pay_type;}
	public function get_amount(){return $this->_amount;}
	public function get_total_cope(){return $this->_total_cope;}
	public function get_total_dev(){return $this->_total_dev;}
	public function get_adj_reason(){return $this->_adj_reason;}
	public function get_record_type(){return $this->_record_type;}
	public function get_overrule_reason(){return $this->_overrule_reason;}
	public function get_fapiao(){return $this->_fapiao;}

//获取结算商列表
	public function get_process_list()
	{
		$model = new AppAdjApplyModel(29);
		$process_list = $model -> getNameList();
		return $process_list;
	}
}
?>