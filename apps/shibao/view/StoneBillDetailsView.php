<?php
/**
 *  -------------------------------------------------
 *   @file		: StoneBillDetailsView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-03-27 13:36:44
 *   @update	:
 *  -------------------------------------------------
 */
class StoneBillDetailsView extends View
{
	protected $_id;
	protected $_bill_id;
	protected $_dia_package;
	protected $_purchase_price;
	protected $_specification;
	protected $_color;
	protected $_neatness;
	protected $_cut;
	protected $_symmetry;
	protected $_polishing;
	protected $_fluorescence;
	protected $_num;
	protected $_weight;
	protected $_price;


	public function get_id(){return $this->_id;}
	public function get_bill_id(){return $this->_bill_id;}
	public function get_dia_package(){return $this->_dia_package;}
	public function get_purchase_price(){return $this->_purchase_price;}
	public function get_specification(){return $this->_specification;}
	public function get_color(){return $this->_color;}
	public function get_neatness(){return $this->_neatness;}
	public function get_cut(){return $this->_cut;}
	public function get_symmetry(){return $this->_symmetry;}
	public function get_polishing(){return $this->_polishing;}
	public function get_fluorescence(){return $this->_fluorescence;}
	public function get_num(){return $this->_num;}
	public function get_weight(){return $this->_weight;}
	public function get_price(){return $this->_price;}

}
?>