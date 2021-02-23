<?php
/**
 *  -------------------------------------------------
 *   @file		: AppDiamondPriceView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 11:15:38
 *   @update	:
 *  -------------------------------------------------
 */
class AppDiamondPriceView extends View
{
	protected $_id;
	protected $_guige_a;
	protected $_guige_b;
	protected $_price;


	public function get_id(){return $this->_id;}
	public function get_guige_a(){return $this->_guige_a;}
	public function get_guige_b(){return $this->_guige_b;}
	public function get_price(){return $this->_price;}

}
?>