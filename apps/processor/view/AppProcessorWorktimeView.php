<?php
/**
 *  -------------------------------------------------
 *   @file		: AppProcessorWorktimeView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-01 10:14:48
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorWorktimeView extends View
{
	protected $_pw_id;
	protected $_processor_id;
	protected $_normal_day;
	protected $_wait_dia;
	protected $_behind_wait_dia;
	protected $_ykqbzq;
	protected $_is_rest;
	protected $_order_problem;
	protected $_order_type;
	protected $_is_work;
	protected $_wkqbzq;
	protected $_now_wait_dia;
	protected $_holiday_time;


	public function get_pw_id(){return $this->_pw_id;}
	public function get_processor_id(){return $this->_processor_id;}
	public function get_normal_day(){return $this->_normal_day;}
	public function get_wait_dia(){return $this->_wait_dia;}
	public function get_behind_wait_dia(){return $this->_behind_wait_dia;}
	public function get_ykqbzq(){return $this->_ykqbzq;}
	public function get_is_rest(){return $this->_is_rest?$this->_is_rest:1;}
	public function get_order_problem(){return $this->_order_problem;}
	public function get_order_type(){return $this->_order_type;}
	public function get_is_work(){return $this->_is_work;}
	public function get_wkqbzq(){return $this->_wkqbzq;}
	public function get_now_wait_dia(){return $this->_now_wait_dia;}
	public function get_holiday_time(){return $this->_holiday_time;}

}
?>