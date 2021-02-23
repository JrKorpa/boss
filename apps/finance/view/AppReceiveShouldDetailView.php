<?php
/**
 *  -------------------------------------------------
 *   @file		: AppReceiveShouldDetailView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-02 14:49:51
 *   @update	:
 *  -------------------------------------------------
 */
class AppReceiveShouldDetailView extends View
{
	protected $_detail_id;
	protected $_should_id;
	protected $_apply_number;
	protected $_total_cope;


	public function get_detail_id(){return $this->_detail_id;}
	public function get_should_id(){return $this->_should_id;}
	public function get_apply_number(){return $this->_apply_number;}
	public function get_total_cope(){return $this->_total_cope;}

}
?>