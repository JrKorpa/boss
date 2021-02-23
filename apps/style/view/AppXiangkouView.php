<?php
/**
 *  -------------------------------------------------
 *   @file		: AppXiangkouView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-22 23:00:31
 *   @update	:
 *  -------------------------------------------------
 */
class AppXiangkouView extends View
{
	protected $_x_id;
	protected $_style_id;
	protected $_style_sn;
	protected $_stone;
	protected $_finger;
	protected $_main_stone_weight;
	protected $_main_stone_num;
	protected $_sec_stone_weight;
	protected $_sec_stone_num;
	protected $_sec_stone_weight_other;
	protected $_sec_stone_num_other;
	protected $_g18_weight;
	protected $_g18_weight_more;
	protected $_g18_weight_more2;
	protected $_gpt_weight;
	protected $_gpt_weight_more;
	protected $_gpt_weight_more2;
	protected $_sec_stone_price_other;


	public function get_x_id(){return $this->_x_id;}
	public function get_style_id(){return $this->_style_id;}
	public function get_style_sn(){return $this->_style_sn;}
	public function get_stone(){return $this->_stone;}
	public function get_finger(){return $this->_finger;}
	public function get_main_stone_weight(){return $this->_main_stone_weight;}
	public function get_main_stone_num(){return $this->_main_stone_num;}
	public function get_sec_stone_weight(){return $this->_sec_stone_weight;}
	public function get_sec_stone_num(){return $this->_sec_stone_num;}
	public function get_sec_stone_weight_other(){return $this->_sec_stone_weight_other;}
	public function get_sec_stone_num_other(){return $this->_sec_stone_num_other;}
	public function get_g18_weight(){return $this->_g18_weight;}
	public function get_g18_weight_more(){return $this->_g18_weight_more;}
	public function get_g18_weight_more2(){return $this->_g18_weight_more2;}
	public function get_gpt_weight(){return $this->_gpt_weight;}
	public function get_gpt_weight_more(){return $this->_gpt_weight_more;}
	public function get_gpt_weight_more2(){return $this->_gpt_weight_more2;}
	public function get_sec_stone_price_other(){return $this->_sec_stone_price_other;}

}
?>