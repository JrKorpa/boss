<?php
/**
 *  -------------------------------------------------
 *   @file		: RelStyleLoversView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-20 17:37:08
 *   @update	:
 *  -------------------------------------------------
 */
class RelStyleLoversView extends View
{
	protected $_id;
	protected $_style_id1;
	protected $_style_id2;
	protected $_style_sn1;
	protected $_style_sn2;


	public function get_id(){return $this->_id;}
	public function get_style_id1(){return $this->_style_id1;}
	public function get_style_id2(){return $this->_style_id2;}
	public function get_style_sn1(){return $this->_style_sn1;}
	public function get_style_sn2(){return $this->_style_sn2;}

}
?>