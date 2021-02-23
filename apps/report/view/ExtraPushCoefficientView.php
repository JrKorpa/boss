<?php
/**
 *  -------------------------------------------------
 *   @file		: ExtraPushCoefficientView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-09-29 11:04:34
 *   @update	:
 *  -------------------------------------------------
 */
class ExtraPushCoefficientView extends View
{
	protected $_id;
	protected $_dep_id;
	protected $_dep_name;
	protected $_station;
	protected $_bonus_gears;
	protected $_add_performance_standard;
	protected $_excess_price;
	protected $_push_money_coefficient;


	public function get_id(){return $this->_id;}
	public function get_dep_id(){return $this->_dep_id;}
	public function get_dep_name(){return $this->_dep_name;}
	public function get_station(){return $this->_station;}
	public function get_bonus_gears(){return $this->_bonus_gears;}
	public function get_add_performance_standard(){return $this->_add_performance_standard;}
	public function get_excess_price(){return $this->_excess_price;}
	public function get_push_money_coefficient(){return $this->_push_money_coefficient;}

}
?>