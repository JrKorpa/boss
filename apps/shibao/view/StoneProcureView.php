<?php
/**
 *  -------------------------------------------------
 *   @file		: StoneProcureView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-28 15:47:27
 *   @update	:
 *  -------------------------------------------------
 */
class StoneProcureView extends View
{
	protected $_id;
	protected $_pro_sn;
	protected $_pro_type;
	protected $_pro_ct;
	protected $_pro_total;
	protected $_check_status;
	protected $_create_id;
	protected $_create_user;
	protected $_create_time;
	protected $_check_plan;
	protected $_is_batch;
	protected $_note;
	protected $_refuse_cause;


	public function get_id(){return $this->_id;}
	public function get_pro_sn(){return $this->_pro_sn;}
	public function get_pro_type(){return $this->_pro_type;}
	public function get_pro_ct(){return $this->_pro_ct;}
	public function get_pro_total(){return $this->_pro_total;}
	public function get_check_status(){return $this->_check_status;}
	public function get_create_id(){return $this->_create_id;}
	public function get_create_user(){return $this->_create_user;}
	public function get_create_time(){return $this->_create_time;}
	public function get_check_plan(){return $this->_check_plan;}
	public function get_is_batch(){return ($this->_id)?$this->_is_batch:0;}
	public function get_note(){return $this->_note;}
	public function get_refuse_cause(){return $this->_refuse_cause;}

	public function get_check_search(){
		$status = [
			['id'=>'0','label'=>'未操作'],
			['id'=>'1','label'=>'审核中'],
			['id'=>'2','label'=>'已驳回'],
			['id'=>'4','label'=>'待审核'],
		];
		return $status;
	}

}
?>