<?php
/**
 *  -------------------------------------------------
 *   @file		: ExtraCategoryRatioView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-09-29 11:26:14
 *   @update	:
 *  -------------------------------------------------
 */
class ExtraCategoryRatioView extends View
{
	protected $_id;
	protected $_dep_id;
	protected $_dep_name;
	protected $_goods_type;
	protected $_discount;
	protected $_pull_ratio_a;
	protected $_pull_ratio_b;
	protected $_pull_ratio_c;
	protected $_pull_ratio_d;


	public function get_id(){return $this->_id;}
	public function get_dep_id(){return $this->_dep_id;}
	public function get_dep_name(){return $this->_dep_name;}
	public function get_goods_type(){return $this->_goods_type;}
	public function get_discount(){return $this->_discount;}
	public function get_pull_ratio_a(){return $this->_pull_ratio_a;}
	public function get_pull_ratio_b(){return $this->_pull_ratio_b;}
	public function get_pull_ratio_c(){return $this->_pull_ratio_c;}
	public function get_pull_ratio_d(){return $this->_pull_ratio_d;}

}
?>