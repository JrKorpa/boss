<?php
/**
 *  -------------------------------------------------
 *   @file		: AppPayApplyGoodsView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-21 22:08:51
 *   @update	:
 *  -------------------------------------------------
 */
class AppPayApplyGoodsView extends View
{
	protected $_id;
	protected $_apply_id;
	protected $_serial_number;
	protected $_goods_id;
	protected $_total;
	protected $_total_cope;
	protected $_total_dev;
	protected $_dev_direction;
	protected $_overrule_reason;


	public function get_id(){return $this->_id;}
	public function get_apply_id(){return $this->_apply_id;}
	public function get_serial_number(){return $this->_serial_number;}
	public function get_goods_id(){return $this->_goods_id;}
	public function get_total(){return $this->_total;}
	public function get_total_cope(){return $this->_total_cope;}
	public function get_total_dev(){return $this->_total_dev;}
	public function get_dev_direction(){return $this->_dev_direction;}
	public function get_overrule_reason(){return $this->_overrule_reason;}

}
?>