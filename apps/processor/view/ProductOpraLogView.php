<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductOpraLogView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-11 17:36:16
 *   @update	:
 *  -------------------------------------------------
 */
class ProductOpraLogView extends View
{
	protected $_id;
	protected $_opra_action;
	protected $_opra_uname;
	protected $_opra_time;
	protected $_opra_info;


	public function get_id(){return $this->_id;}
	public function get_opra_action(){return $this->_opra_action;}
	public function get_opra_uname(){return $this->_opra_uname;}
	public function get_opra_time(){return $this->_opra_time;}
	public function get_opra_info(){return $this->_opra_info;}

}
?>