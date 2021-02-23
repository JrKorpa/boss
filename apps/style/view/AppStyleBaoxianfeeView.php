<?php
/**
 *  -------------------------------------------------
 *   @file		: AppStyleBaoxianfeeView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 11:15:38
 *   @update	:
 *  -------------------------------------------------
 */
class AppStyleBaoxianfeeView extends View
{
	protected $_id;
	protected $_min;
	protected $_max;
	protected $_price;
	protected $_status;


	public function get_id(){return $this->_id;}
	public function get_min(){return $this->_min;}
	public function get_max(){return $this->_max;}
	public function get_price(){return $this->_price;}
	public function get_status(){return $this->_status;}

}
?>