<?php
/**
 *  -------------------------------------------------
 *   @file		: AppPayShouldDetailView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-02 14:46:45
 *   @update	:
 *  -------------------------------------------------
 */
class AppPayShouldDetailView extends View
{
	protected $_id;
	protected $_pay_number;
	protected $_pay_apply_number;
	protected $_total_cope;


	public function get_id(){return $this->_id;}
	public function get_pay_number(){return $this->_pay_number;}
	public function get_pay_apply_number(){return $this->_pay_apply_number;}
	public function get_total_cope(){return $this->_total_cope;}

}
?>