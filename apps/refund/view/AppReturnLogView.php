<?php
/**
 *  -------------------------------------------------
 *   @file		: AppReturnLogView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-13 11:05:18
 *   @update	:
 *  -------------------------------------------------
 */
class AppReturnLogView extends View
{
	protected $_id;
	protected $_return_id;
	protected $_even_time;
	protected $_even_user;
	protected $_even_content;


	public function get_id(){return $this->_id;}
	public function get_return_id(){return $this->_return_id;}
	public function get_even_time(){return $this->_even_time;}
	public function get_even_user(){return $this->_even_user;}
	public function get_even_content(){return $this->_even_content;}

}
?>