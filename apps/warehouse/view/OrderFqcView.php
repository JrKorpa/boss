<?php
/**
 *  -------------------------------------------------
 *   @file		: OrderFqcView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-28 15:59:57
 *   @update	:
 *  -------------------------------------------------
 */
class OrderFqcView extends View
{
	protected $_id;
	protected $_order_sn;
	protected $_problem_type;
	protected $_problem;
	protected $_datatime;
	protected $_remark;
	protected $_is_pass;


	public function get_id(){return $this->_id;}
	public function get_order_sn(){return $this->_order_sn;}
	public function get_problem_type(){return $this->_problem_type;}
	public function get_problem(){return $this->_problem;}
	public function get_datatime(){return $this->_datatime;}
	public function get_remark(){return $this->_remark;}
	public function get_is_pass(){return $this->_is_pass;}

}
?>