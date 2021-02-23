<?php
/**
 *  -------------------------------------------------
 *   @file		: AppProcessorTypeView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 10:51:06
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorTypeView extends View
{
	protected $_id;
	protected $_name;
	protected $_status;
	protected $_create_time;


	public function get_id(){return $this->_id;}
	public function get_name(){return $this->_name;}
	public function get_status(){return $this->_status===0 ? 0 : 1;}
	public function get_create_time(){return $this->_create_time;}

}
?>