<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoBView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-18 17:09:51
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoDView extends View
{
	protected $_id;
	protected $_bill_id;
	protected $_kela_order_sn;
	protected $_pid;
	protected $_in_warehouse_type;


	public function get_id(){return $this->_id;}
	public function get_bill_id(){return $this->_bill_id;}
	public function get_kela_order_sn(){return $this->_kela_order_sn;}
	public function get_pid(){return $this->_pid;}
	public function get_in_warehouse_type(){return $this->_in_warehouse_type;}

	



}
?>