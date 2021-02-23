<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductOqcOpraView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-12 12:05:59
 *   @update	:
 *  -------------------------------------------------
 */
class ProductOqcOpraView extends View
{
	protected $_id;
	protected $_bc_id;
	protected $_oqc_result;
	protected $_oqc_reason;
	protected $_oqc_info;
	protected $_opra_uid;
	protected $_opra_uname;
	protected $_opra_time;


	public function get_id(){return $this->_id;}
	public function get_bc_id(){return $this->_bc_id;}
	public function get_oqc_result(){return $this->_oqc_result;}
	public function get_oqc_reason(){return $this->_oqc_reason;}
	public function get_oqc_info(){return $this->_oqc_info;}
	public function get_opra_uid(){return $this->_opra_uid;}
	public function get_opra_uname(){return $this->_opra_uname;}
	public function get_opra_time(){return $this->_opra_time;}

}
?>