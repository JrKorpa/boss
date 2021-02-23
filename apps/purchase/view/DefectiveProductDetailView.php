<?php
/**
 *  -------------------------------------------------
 *   @file		: DefectiveProductDetailView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-19 00:12:42
 *   @update	:
 *  -------------------------------------------------
 */
class DefectiveProductDetailView extends View
{
	protected $_id;
	protected $_info_id;
	protected $_rece_detail_id;
	protected $_xuhao;
	protected $_factory_sn;
	protected $_bc_sn;
	protected $_customer_name;
	protected $_cat_type;
	protected $_total;
	protected $_info;


	public function get_id(){return $this->_id;}
	public function get_info_id(){return $this->_info_id;}
	public function get_rece_detail_id(){return $this->_rece_detail_id;}
	public function get_xuhao(){return $this->_xuhao;}
	public function get_factory_sn(){return $this->_factory_sn;}
	public function get_bc_sn(){return $this->_bc_sn;}
	public function get_customer_name(){return $this->_customer_name;}
	public function get_cat_type(){return $this->_cat_type;}
	public function get_total(){return $this->_total;}
	public function get_info(){return $this->_info;}

}
?>