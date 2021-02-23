<?php
/**
 *  -------------------------------------------------
 *   @file		: AppStyleFeeView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-26 16:53:06
 *   @update	:
 *  -------------------------------------------------
 */
class ConfigVisitAllowView extends View
{
	protected $_id;
	protected $_user_name;
	protected $_xilie;

	public function get_id(){return $this->_id;}
	public function get_user_name(){return $this->_user_name;}
	public function get_xilie(){return $this->_xilie;}
}
?>