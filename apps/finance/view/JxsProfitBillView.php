<?php
/**
 *  -------------------------------------------------
 *   @file		: JxsProfitBillView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-07-17 00:49:38
 *   @update	:
 *  -------------------------------------------------
 */
class JxsProfitBillView extends View
{
	protected $_id;
	protected $_jxs_id;
	protected $_created_date;
	protected $_created_by;
	protected $_calc_profit;
	protected $_calc_date;
	protected $_status;


	public function get_id(){return $this->_id;}
	public function get_jxs_id(){return $this->_jxs_id;}
	public function get_created_date(){return $this->_created_date;}
	public function get_created_by(){return $this->_created_by;}
	public function get_calc_profit(){return $this->_calc_profit;}
	public function get_calc_date(){return $this->_calc_date;}
	public function get_status(){return $this->_status;}

}
?>