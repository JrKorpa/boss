<?php
/**
 *  -------------------------------------------------
 *   @file		: RelChannelPayView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-05-10 11:50:57
 *   @update	:
 *  -------------------------------------------------
 */
class RelChannelPayView extends View
{
	protected $_id;
	protected $_channel_id;
	protected $_channel_name;
	protected $_pay_id;
	protected $_pay_name;


	public function get_id(){return $this->_id;}
	public function get_channel_id(){return $this->_channel_id;}
	public function get_channel_name(){return $this->_channel_name;}
	public function get_pay_id(){return $this->_pay_id;}
	public function get_pay_name(){return $this->_pay_name;}

}
?>