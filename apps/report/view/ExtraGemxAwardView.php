<?php
/**
 *  -------------------------------------------------
 *   @file		: ExtraGemxAwardView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-09-29 11:19:12
 *   @update	:
 *  -------------------------------------------------
 */
class ExtraGemxAwardView extends View
{
	protected $_id;
	protected $_gemx_max;
	protected $_gemx_min;
	protected $_award;


	public function get_id(){return $this->_id;}
	public function get_gemx_max(){return $this->_gemx_max;}
	public function get_gemx_min(){return $this->_gemx_min;}
	public function get_award(){return $this->_award;}

}
?>