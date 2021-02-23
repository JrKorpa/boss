<?php
/**
 *  -------------------------------------------------
 *   @file		: AppReturnCheckView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-13 11:04:54
 *   @update	:
 *  -------------------------------------------------
 */
class AppReturnCheckView extends View
{
	protected $_id;
	protected $_return_id;
	protected $_leader_id;
	protected $_leader_res;
	protected $_leader_status;
	protected $_leader_time;
	protected $_goods_comfirm_id;
	protected $_goods_res;
	protected $_goods_status;
	protected $_goods_time;
	protected $_cto_id;
	protected $_cto_res;
	protected $_cto_status;
	protected $_cto_time;
	protected $_deparment_finance_id;
	protected $_deparment_finance_status;
	protected $_deparment_finance_res;
	protected $_deparment_finance_time;
	protected $_finance_id;
	protected $_bak_fee;
	protected $_finance_res;
	protected $_finance_status;
	protected $_finance_time;


	public function get_id(){return $this->_id;}
	public function get_return_id(){return $this->_return_id;}
	public function get_leader_id(){return $this->_leader_id;}
	public function get_leader_res(){return $this->_leader_res;}
	public function get_leader_status(){return $this->_leader_status;}
	public function get_leader_time(){return $this->_leader_time;}
	public function get_goods_comfirm_id(){return $this->_goods_comfirm_id;}
	public function get_goods_res(){return $this->_goods_res;}
	public function get_goods_status(){return $this->_goods_status;}
	public function get_goods_time(){return $this->_goods_time;}
	public function get_cto_id(){return $this->_cto_id;}
	public function get_cto_res(){return $this->_cto_res;}
	public function get_cto_status(){return $this->_cto_status;}
	public function get_cto_time(){return $this->_cto_time;}
	public function get_deparment_finance_id(){return $this->_deparment_finance_id;}
	public function get_deparment_finance_status(){return $this->_deparment_finance_status;}
	public function get_deparment_finance_res(){return $this->_deparment_finance_res;}
	public function get_deparment_finance_time(){return $this->_deparment_finance_time;}
	public function get_finance_id(){return $this->_finance_id;}
	public function get_bak_fee(){return $this->_bak_fee;}
	public function get_finance_res(){return $this->_finance_res;}
	public function get_finance_status(){return $this->_finance_status;}
	public function get_finance_time(){return $this->_finance_time;}

}
?>