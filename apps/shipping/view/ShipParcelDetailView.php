<?php
/**
 *  -------------------------------------------------
 *   @file		: ShipParcelDetailView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-06 18:27:56
 *   @update	:
 *  -------------------------------------------------
 */
class ShipParcelDetailView extends View
{
	protected $_id;
	protected $_parcel_id;
	protected $_zhuancang_sn;
	protected $_from_place_id;
	protected $_to_warehouse_id;
	protected $_shouhuoren;
	protected $_num;
	protected $_amount;
	protected $_goods_sn;
	protected $_goods_name;
	protected $_create_user;
	protected $_create_time;


	public function get_id(){return $this->_id;}
	public function get_parcel_id(){return $this->_parcel_id;}
	public function get_zhuancang_sn(){return $this->_zhuancang_sn;}
	public function get_from_place_id(){return $this->_from_place_id;}
	public function get_to_warehouse_id(){return $this->_to_warehouse_id;}
	public function get_shouhuoren(){return $this->_shouhuoren;}
	public function get_num(){return $this->_num;}
	public function get_amount(){return $this->_amount;}
	public function get_goods_sn(){return $this->_order_sn;}
	public function get_goods_name(){return $this->_goods_name;}
	public function get_create_user(){return $this->_create_user;}
	public function get_create_time(){return $this->_create_time;}

}
?>