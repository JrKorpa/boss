<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderFavorableView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-17 17:11:13
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderFavorableView extends View
{
	protected $_id;
	protected $_order_sn;
	protected $_order_id;
	protected $_detail_id;
	protected $_goods_id;
	protected $_goods_sn;
	protected $_goods_name;
	protected $_favorable_price;
	protected $_create_time;


	public function get_id(){return $this->_id;}
	public function get_order_sn(){return $this->_order_sn;}
	public function get_order_id(){return $this->_order_id;}
	public function get_detail_id(){return $this->_detail_id;}
	public function get_goods_id(){return $this->_goods_id;}
	public function get_goods_sn(){return $this->_goods_sn;}
	public function get_goods_name(){return $this->_goods_name;}
	public function get_favorable_price(){return $this->_favorable_price;}
	public function get_create_time(){return $this->_create_time;}

}
?>