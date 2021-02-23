<?php
/**
 *  -------------------------------------------------
 *   @file		: AppCouponTypeView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-11 16:52:55
 *   @update	:
 *  -------------------------------------------------
 */
class AppCouponTypeView extends View
{
	protected $_id;
	protected $_type_name;


	public function get_id(){return $this->_id;}
	public function get_type_name(){return $this->_type_name;}

}
?>