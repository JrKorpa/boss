<?php
/**
 *  -------------------------------------------------
 *   @file		: TsydJxsDeliveryView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2016-01-28 17:56:56
 *   @update	:
 *  -------------------------------------------------
 */
class TsydJxsDeliveryView extends View
{
	protected $_id;
	protected $_bill_no;
	protected $_bill_type;
	protected $_bill_status;


	public function get_id(){return $this->_id;}
	public function get_bill_no(){return $this->_bill_no;}
	public function get_bill_type(){return $this->_bill_type;}
	public function get_bill_status(){return $this->_bill_status;}

}
?>