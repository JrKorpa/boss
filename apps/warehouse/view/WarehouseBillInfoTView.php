<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoTView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-20 15:29:32
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoTView extends View
{
	protected $_id;
	protected $_bill_id;
	protected $_ruku_method;
	protected $_ruku_type;
	protected $_send_goods_sn;
	protected $_pro_id;
	protected $_pro_name;
	protected $_jiejia;


	public function get_id(){return $this->_id;}
	public function get_bill_id(){return $this->_bill_id;}
	public function get_ruku_method(){return $this->_ruku_method;}
	public function get_ruku_type(){return $this->_ruku_type;}
	public function get_send_goods_sn(){return $this->_send_goods_sn;}
	public function get_pro_id(){return $this->_pro_id;}
	public function get_pro_name(){return $this->_pro_name;}
	public function get_jiejia(){return $this->_jiejia;}

}
?>