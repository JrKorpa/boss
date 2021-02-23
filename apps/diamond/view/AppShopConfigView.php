<?php
/**
 *  -------------------------------------------------
 *   @file		: AppShopConfigView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-18 14:56:43
 *   @update	:
 *  -------------------------------------------------
 */
class AppShopConfigView extends View
{
	protected $_id;
	protected $_name;
	protected $_code;
	protected $_value;


	public function get_id(){return $this->_id;}
	public function get_name(){return $this->_name;}
	public function get_code(){return $this->_code;}
	public function get_value(){return $this->_value;}

}
?>