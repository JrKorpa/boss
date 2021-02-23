<?php
/**
 *  -------------------------------------------------
 *   @file		: AppMemberAccountView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 12:23:01
 *   @update	:
 *  -------------------------------------------------
 */
class AppMemberAccountView extends View
{
	protected $_id;
	protected $_memeber_id;
	protected $_current_money;
	protected $_total_money;
	protected $_total_point;
	protected $_is_deleted;


	public function get_id(){return $this->_id;}
	public function get_memeber_id(){return $this->_memeber_id;}
	public function get_current_money(){return $this->_current_money;}
	public function get_total_money(){return $this->_total_money;}
	public function get_total_point(){return $this->_total_point;}
	public function get_is_deleted(){return $this->_is_deleted;}

}
?>