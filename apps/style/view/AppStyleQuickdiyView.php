<?php
/**
 *  -------------------------------------------------
 *   @file		: AppStyleForView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-19 10:41:52
 *   @update	:
 *  -------------------------------------------------
 */
class AppStyleQuickdiyView extends View
{
	protected $_id;
	protected $_style_sn;
	protected $_goods_sn;
	protected $_style_name;
	protected $_caizhi;
	protected $_caizhiyanse;
	protected $_xiangkou;
	protected $_zhiquan;
	protected $_create_user;
	protected $_create_time;
	protected $_status;
	public function get_id(){return $this->_id;}
	public function get_goods_sn(){return $this->_goods_sn;}
	public function get_style_sn(){return $this->_style_sn;}
	public function get_style_name(){return $this->_style_name;}
	public function get_caizhi(){return $this->_caizhi;}
	public function get_caizhiyanse(){return $this->_caizhiyanse;}
	public function get_zhiquan(){return $this->_zhiquan;}
	public function get_create_user(){return $this->_create_user;}
	public function get_create_time(){return $this->_create_time;}
	public function get_status(){return $this->_status;}
}
?>