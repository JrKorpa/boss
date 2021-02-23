<?php
/**
 *  -------------------------------------------------
 *   @file		: AppStyleJxsView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2018-05-16 14:12:30
 *   @update	:
 *  -------------------------------------------------
 */
class AppStyleJxsView extends View
{
	protected $_id;
	protected $_style_name;
	protected $_style_sn;
	protected $_status;
	protected $_add_user;
	protected $_add_time;
	protected $_ban_user;
	protected $_ban_time;


	public function get_id(){return $this->_id;}
	public function get_style_name(){return $this->_style_name;}
	public function get_style_sn(){return $this->_style_sn;}
	public function get_status(){return $this->_status;}
	public function get_add_user(){return $this->_add_user;}
	public function get_add_time(){return $this->_add_time;}
	public function get_ban_user(){return $this->_ban_user;}
	public function get_ban_time(){return $this->_ban_time;}

}
?>