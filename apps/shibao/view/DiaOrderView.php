<?php
/**
 *  -------------------------------------------------
 *   @file		: DiaOrderView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: liyanhong <462166282@qq.com>
 *   @date		: 2015-03-16 11:12:23
 *   @update	:
 *  -------------------------------------------------
 */
class DiaOrderView extends View
{
	protected $_order_id;
	protected $_type;
	protected $_status;
	protected $_fin_status;
	protected $_order_time;
	protected $_in_warehouse_type;
	protected $_account_type;
	protected $_adjust_type;
	protected $_send_goods_sn;
	protected $_prc_id;
	protected $_prc_name;
	protected $_goods_num;
	protected $_goods_zhong;
	protected $_goods_total;
	protected $_shijia;
	protected $_make_order;
	protected $_addtime;
	protected $_check_order;
	protected $_checktime;
	protected $_fin_check;
	protected $_fin_check_time;
	protected $_info;
	protected $_times;


	public function get_order_id(){return $this->_order_id;}
	public function get_type(){return $this->_type;}
	public function get_status(){return $this->_status;}
	public function get_fin_status(){return $this->_fin_status;}
	public function get_order_time(){return $this->_order_time;}
	public function get_in_warehouse_type(){return $this->_in_warehouse_type;}
	public function get_account_type(){return $this->_account_type;}
	public function get_adjust_type(){return $this->_adjust_type;}
	public function get_send_goods_sn(){return $this->_send_goods_sn;}
	public function get_prc_id(){return $this->_prc_id;}
	public function get_prc_name(){return $this->_prc_name;}
	public function get_goods_num(){return $this->_goods_num;}
	public function get_goods_zhong(){return $this->_goods_zhong;}
	public function get_goods_total(){return $this->_goods_total;}
	public function get_shijia(){return $this->_shijia;}
	public function get_make_order(){return $this->_make_order;}
	public function get_addtime(){return $this->_addtime;}
	public function get_check_order(){return $this->_check_order;}
	public function get_checktime(){return $this->_checktime;}
	public function get_fin_check(){return $this->_fin_check;}
	public function get_fin_check_time(){return $this->_fin_check_time;}
	public function get_info(){return $this->_info;}
	public function get_times(){return $this->_times;}

}
?>