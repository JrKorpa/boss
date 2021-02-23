<?php
/**
 *  -------------------------------------------------
 *   @file		: PayHexiaoDetailView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-28 14:50:44
 *   @update	:
 *  -------------------------------------------------
 */
class PayHexiaoDetailView extends View
{
	protected $_detail_id;
	protected $_hx_id;
	protected $_jxc_order;
	protected $_type;
	protected $_goods_num;
	protected $_chengben;
	protected $_shijia;
	protected $_overrule_reason;


	public function get_detail_id(){return $this->_detail_id;}
	public function get_hx_id(){return $this->_hx_id;}
	public function get_jxc_order(){return $this->_jxc_order;}
	public function get_type(){return $this->_type;}
	public function get_goods_num(){return $this->_goods_num;}
	public function get_chengben(){return $this->_chengben;}
	public function get_shijia(){return $this->_shijia;}
	public function get_overrule_reason(){return $this->_overrule_reason;}

}
?>