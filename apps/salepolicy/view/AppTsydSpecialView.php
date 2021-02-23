<?php
/**
 *  -------------------------------------------------
 *   @file		: AppTsydSpecialView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-08-25 17:30:09
 *   @update	:
 *  -------------------------------------------------
 */
class AppTsydSpecialView extends View
{
	protected $_id;
	protected $_style_name;
	protected $_style_sn;
	protected $_add_user;
	protected $_add_time;


	public function get_id(){return $this->_id;}
	public function get_style_name(){return $this->_style_name;}
	public function get_style_sn(){return $this->_style_sn;}
	public function get_add_user(){return $this->_add_user;}
	public function get_add_time(){return $this->_add_time;}

}
?>