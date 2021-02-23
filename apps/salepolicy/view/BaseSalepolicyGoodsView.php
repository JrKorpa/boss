<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseSalepolicyGoodsView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-03 18:25:10
 *   @update	:
 *  -------------------------------------------------
 */
class BaseSalepolicyGoodsView extends View
{
	protected $_id;
	protected $_goods_id;
	protected $_goods_sn;
	protected $_goods_name;
	protected $_chengbenjia;
	protected $_stone;
	protected $_finger;
	protected $_caizhi;
	protected $_yanse;
	protected $_category;
	protected $_product_type;
	protected $_isXianhuo;
	protected $_is_sale;
	protected $_add_time;
	protected $_type;
	protected $_together_id;


	public function get_id(){return $this->_id;}
	public function get_goods_id(){return $this->_goods_id;}
	public function get_goods_sn(){return $this->_goods_sn;}
	public function get_goods_name(){return $this->_goods_name;}
	public function get_chengbenjia(){return $this->_chengbenjia;}
	public function get_stone(){return $this->_stone;}
	public function get_finger(){return $this->_finger;}
	public function get_caizhi(){return $this->_caizhi;}
	public function get_yanse(){return $this->_yanse;}
	public function get_category(){return $this->_category;}
	public function get_product_type(){return $this->_product_type;}
	public function get_isXianhuo(){return $this->_isXianhuo;}
	public function get_is_sale(){return $this->_is_sale;}
	public function get_add_time(){return $this->_add_time;}
	public function get_together_id(){return $this->_together_id;}
	public function get_type(){return $this->_type;}

}
?>