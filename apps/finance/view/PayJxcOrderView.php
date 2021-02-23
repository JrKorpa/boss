<?php
/**
 *  -------------------------------------------------
 *   @file		: PayJxcOrderView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-26 16:08:05
 *   @update	:
 *  -------------------------------------------------
 */
class PayJxcOrderView extends View
{
	protected $_order_id;
	protected $_jxc_order;
	protected $_kela_sn;
	protected $_type;
	protected $_status;
	protected $_goods_num;
	protected $_chengben;
	protected $_shijia;
	protected $_addtime;
	protected $_checktime;
	protected $_hexiaotime;
	protected $_hexiao_number;
	protected $_is_return;
	protected $_returntime;


	public function get_order_id(){return $this->_order_id;}
	public function get_jxc_order(){return $this->_jxc_order;}
	public function get_kela_sn(){return $this->_kela_sn;}
	public function get_type(){return $this->_type;}
	public function get_status(){return $this->_status;}
	public function get_goods_num(){return $this->_goods_num;}
	public function get_chengben(){return $this->_chengben;}
	public function get_shijia(){return $this->_shijia;}
	public function get_addtime(){return $this->_addtime;}
	public function get_checktime(){return $this->_checktime;}
	public function get_hexiaotime(){return $this->_hexiaotime;}
	public function get_hexiao_number(){return $this->_hexiao_number;}
	public function get_is_return(){return $this->_is_return;}
	public function get_returntime(){return $this->_returntime;}

}
?>