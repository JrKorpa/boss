<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoLView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-21 17:14:07
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoLView extends View
{
	protected $_id;
	protected $_bill_id;
	protected $_put_in_type;
	protected $_send_goods_sn;
	protected $_prc_id;
	protected $_prc_name;
	protected $_jiejia;


	public function get_id(){return $this->_id;}
	public function get_bill_id(){return $this->_bill_id;}
	public function get_put_in_type(){return $this->_put_in_type;}
	public function get_send_goods_sn(){return $this->_send_goods_sn;}
	public function get_prc_id(){return $this->_prc_id;}
	public function get_prc_name(){return $this->_prc_name;}
	public function get_jiejia(){return $this->_jiejia;}

}
?>