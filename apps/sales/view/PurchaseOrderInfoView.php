<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseOrderInfoView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-07-12 14:44:01
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseOrderInfoView extends View
{
	protected $_id;
	protected $_purchase_id;
	protected $_detail_id;
	protected $_order_sn;
	protected $_dep_name;


	public function get_id(){return $this->_id;}
	public function get_purchase_id(){return $this->_purchase_id;}
	public function get_detail_id(){return $this->_detail_id;}
	public function get_order_sn(){return $this->_order_sn;}
	public function get_dep_name(){return $this->_dep_name;}

}
?>