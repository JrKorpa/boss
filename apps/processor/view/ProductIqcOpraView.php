<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductIqcOpraView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-12 15:15:10
 *   @update	:
 *  -------------------------------------------------
 */
class ProductIqcOpraView extends View
{
	protected $_id;
	protected $_shipment_id;
	protected $_sj_num;
	protected $_bf_num;
	protected $_iqc_num;
	protected $_info;
	protected $_opra_uid;
	protected $_opra_uname;
	protected $_opra_time;


	public function get_id(){return $this->_id;}
	public function get_shipment_id(){return $this->_shipment_id;}
	public function get_sj_num(){return $this->_sj_num;}
	public function get_bf_num(){return $this->_bf_num;}
	public function get_iqc_num(){return $this->_iqc_num;}
	public function get_info(){return $this->_info;}
	public function get_opra_uid(){return $this->_opra_uid;}
	public function get_opra_uname(){return $this->_opra_uname;}
	public function get_opra_time(){return $this->_opra_time;}

}
?>