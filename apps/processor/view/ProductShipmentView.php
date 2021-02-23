<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductShipmentView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-12 14:23:57
 *   @update	:
 *  -------------------------------------------------
 */
class ProductShipmentView extends View
{
	protected $_id;
	protected $_bc_id;
	protected $_shipment_number;
	protected $_num;
	protected $_info;
	protected $_opra_uid;
	protected $_opra_uname;
	protected $_opra_time;


	public function get_id(){return $this->_id;}
	public function get_bc_id(){return $this->_bc_id;}
	public function get_shipment_number(){return $this->_shipment_number;}
	public function get_num(){return $this->_num;}
	public function get_info(){return $this->_info;}
	public function get_opra_uid(){return $this->_opra_uid;}
	public function get_opra_uname(){return $this->_opra_uname;}
	public function get_opra_time(){return $this->_opra_time;}

}
?>