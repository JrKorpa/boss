<?php
/**
 *  -------------------------------------------------
 *   @file		: DiyXiangkouConfigView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: gp
 *   @date		: 2017-03-14 16:58:21
 *   @update	:
 *  -------------------------------------------------
 */
class DiyXiangkouConfigView extends View
{
	protected $_id;
	protected $_style_sn;
	protected $_xiangkou;
	protected $_carat_lower_limit;
	protected $_carat_upper_limit;


	public function get_id(){return $this->_id;}
	public function get_style_sn(){return $this->_style_sn;}
	public function get_xiangkou(){return $this->_xiangkou;}
	public function get_carat_lower_limit(){return $this->_carat_lower_limit;}
	public function get_carat_upper_limit(){return $this->_carat_upper_limit;}
	

}
?>