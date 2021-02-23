<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseIqcOpraView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-18 18:17:59
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseIqcOpraView extends View
{
	protected $_id;
	protected $_rece_detail_id;
	protected $_opra_code;
	protected $_opra_uname;
	protected $_opra_time;
	protected $_opra_info;


	public function get_id(){return $this->_id;}
	public function get_rece_detail_id(){return $this->_rece_detail_id;}
	public function get_opra_code(){return $this->_opra_code;}
	public function get_opra_uname(){return $this->_opra_uname;}
	public function get_opra_time(){return $this->_opra_time;}
	public function get_opra_info(){return $this->_opra_info;}

}
?>