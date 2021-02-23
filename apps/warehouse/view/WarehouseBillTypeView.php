<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillTypeView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 10:27:23
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillTypeView extends View
{
	protected $_id;
	protected $_type_name;
	protected $_type_SN;
	protected $_opra_name;
	protected $_opra_time;
	protected $_opra_uid;
	protected $_opra_ip;
	protected $_is_enabled;
	protected $_in_out;


	public function get_id(){return $this->_id;}
	public function get_type_name(){return $this->_type_name;}
	public function get_type_SN(){return $this->_type_SN;}
	public function get_opra_name(){return $this->_opra_name;}
	public function get_opra_time(){return $this->_opra_time;}
	public function get_opra_ip(){return $this->_opra_ip;}
	public function get_opra_uid(){return $this->_opra_uid;}
	public function get_is_enabled(){return $this->_is_enabled;}
	public function get_in_out(){return $this->_in_out;}
}
?>