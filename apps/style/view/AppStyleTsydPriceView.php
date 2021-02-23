<?php
/**
 *  -------------------------------------------------
 *   @file		: AppStyleTsydPriceView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-05-10 00:02:24
 *   @update	:
 *  -------------------------------------------------
 */
class AppStyleTsydPriceView extends View
{
	protected $_id;
	protected $_style_sn;
	protected $_style_name;
	protected $_work;
	protected $_carat;
	protected $_xiangkou_min;
	protected $_xiangkou_max;
	protected $_k_weight;
	protected $_pt_weight;
	protected $_k_price;
	protected $_pt_price;
	protected $_jumpto;
	protected $_pic;
	protected $_group_sn;


	public function get_id(){return $this->_id;}
	public function get_style_sn(){return $this->_style_sn;}
	public function get_style_name(){return $this->_style_name;}
	public function get_work(){return $this->_work;}
	public function get_carat(){return $this->_carat;}
	public function get_xiangkou_min(){return $this->_xiangkou_min;}
	public function get_xiangkou_max(){return $this->_xiangkou_max;}
	public function get_k_weight(){return $this->_k_weight;}
	public function get_pt_weight(){return $this->_pt_weight;}
	public function get_k_price(){return $this->_k_price;}
	public function get_pt_price(){return $this->_pt_price;}
	public function get_jumpto(){return $this->_jumpto;}
	public function get_pic(){return $this->_pic;}
	public function get_group_sn(){return $this->_group_sn;}

}
?>