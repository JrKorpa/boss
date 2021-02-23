<?php
/**
 *  -------------------------------------------------
 *   @file		: StoneBagEnterView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-03-23 14:24:36
 *   @update	:
 *  -------------------------------------------------
 */
class StoneBagEnterView extends View
{
	protected $_id;
	protected $_dia_package;
	protected $_purchase_price;
	protected $_status;
	protected $_sup_id;
	protected $_sup_name;
	protected $_specification;
	protected $_color;
	protected $_neatness;
	protected $_cut;
	protected $_symmetry;
	protected $_polishing;
	protected $_fluorescence;
	protected $_lose_efficacy_time;
	protected $_lose_efficacy_cause;
	protected $_lose_efficacy_user;


	public function get_id(){return $this->_id;}
	public function get_dia_package(){return $this->_dia_package;}
	public function get_purchase_price(){return $this->_purchase_price;}
	public function get_status(){return $this->_status;}
	public function get_sup_id(){return $this->_sup_id;}
	public function get_sup_name(){return $this->_sup_name;}
	public function get_specification(){return $this->_specification;}
	public function get_color(){return $this->_color;}
	public function get_neatness(){return $this->_neatness;}
	public function get_cut(){return $this->_cut;}
	public function get_symmetry(){return $this->_symmetry;}
	public function get_polishing(){return $this->_polishing;}
	public function get_fluorescence(){return $this->_fluorescence;}
	public function get_lose_efficacy_time(){return $this->_lose_efficacy_time;}
	public function get_lose_efficacy_cause(){return $this->_lose_efficacy_cause;}
	public function get_lose_efficacy_user(){return $this->_lose_efficacy_user;}

}
?>