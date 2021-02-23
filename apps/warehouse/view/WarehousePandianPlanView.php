<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehousePandianPlanView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-16 15:10:46
 *   @update	:
 *  -------------------------------------------------
 */
class WarehousePandianPlanView extends View
{
	protected $_id;
	protected $_type;
	protected $_guiwei_list;
	protected $_lock_guiwei;
	protected $_all_num;
	protected $_all_price;
	protected $_nomal;
	protected $_overage;
	protected $_loss;
	protected $_opt_admin;
	protected $_opt_date;
	protected $_verify_admin;
	protected $_verify_date;
	protected $_status;
	protected $_info;


	public function get_id(){return $this->_id;}
	public function get_type(){return $this->_type;}
	public function get_guiwei_list(){return $this->_guiwei_list;}
	public function get_lock_guiwei(){return $this->_lock_guiwei;}
	public function get_all_num(){return $this->_all_num;}
	public function get_all_price(){return $this->_all_price;}
	public function get_nomal(){return $this->_nomal;}
	public function get_overage(){return $this->_overage;}
	public function get_loss(){return $this->_loss;}
	public function get_opt_admin(){return $this->_opt_admin;}
	public function get_opt_date(){return $this->_opt_date;}
	public function get_verify_admin(){return $this->_verify_admin;}
	public function get_verify_date(){return $this->_verify_date;}
	public function get_status(){return $this->_status;}
	public function get_info(){return $this->_info;}

}
?>