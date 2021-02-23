<?php
/**
 *  -------------------------------------------------
 *   @file		: DiaOrderGoodsView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-16 17:29:10
 *   @update	:
 *  -------------------------------------------------
 */
class DiaOrderGoodsView extends View
{
	protected $_og_id;
	protected $_order_id;
	protected $_order_type;
	protected $_shibao;
	protected $_zhengshuhao;
	protected $_zhong;
	protected $_yanse;
	protected $_jingdu;
	protected $_qiegong;
	protected $_duichen;
	protected $_paoguang;
	protected $_yingguang;
	protected $_num;
	protected $_zongzhong;
	protected $_caigouchengben;
	protected $_xiaoshouchengben;
	protected $_add_time;


	public function get_og_id(){return $this->_og_id;}
	public function get_order_id(){return $this->_order_id;}
	public function get_order_type(){return $this->_order_type;}
	public function get_shibao(){return $this->_shibao;}
	public function get_zhengshuhao(){return $this->_zhengshuhao;}
	public function get_zhong(){return $this->_zhong;}
	public function get_yanse(){return $this->_yanse;}
	public function get_jingdu(){return $this->_jingdu;}
	public function get_qiegong(){return $this->_qiegong;}
	public function get_duichen(){return $this->_duichen;}
	public function get_paoguang(){return $this->_paoguang;}
	public function get_yingguang(){return $this->_yingguang;}
	public function get_num(){return $this->_num;}
	public function get_zongzhong(){return $this->_zongzhong;}
	public function get_caigouchengben(){return $this->_caigouchengben;}
	public function get_xiaoshouchengben(){return $this->_xiaoshouchengben;}
	public function get_add_time(){return $this->_add_time;}

}
?>