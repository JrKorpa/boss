<?php
/**
 *  -------------------------------------------------
 *   @file		: ListStyleGoodsView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-13 17:03:57
 *   @update	:
 *  -------------------------------------------------
 */
class ListStyleGoodsView extends View
{
protected $_goods_id;
	protected $_product_type_id;
	protected $_cat_type_id;
	protected $_style_id;
	protected $_style_sn;
	protected $_style_name;
	protected $_goods_sn;
	protected $_shoucun;
	protected $_xiangkou;
	protected $_caizhi;
	protected $_yanse;
	protected $_zhushizhong;
	protected $_zhushi_num;
	protected $_fushizhong1;
	protected $_fushi_num1;
	protected $_fushizhong2;
	protected $_fushi_num2;
	protected $_fushi_chengbenjia_other;
	protected $_weight;
	protected $_jincha_shang;
	protected $_jincha_xia;
	protected $_dingzhichengben;
	protected $_is_ok;
	protected $_last_update;


	public function get_goods_id(){return $this->_goods_id;}
	public function get_product_type_id(){return $this->_product_type_id;}
	public function get_cat_type_id(){return $this->_cat_type_id;}
	public function get_style_id(){return $this->_style_id;}
	public function get_style_sn(){return $this->_style_sn;}
	public function get_style_name(){return $this->_style_name;}
	public function get_goods_sn(){return $this->_goods_sn;}
	public function get_shoucun(){return $this->_shoucun;}
	public function get_xiangkou(){return $this->_xiangkou;}
	public function get_caizhi(){return $this->_caizhi;}
	public function get_yanse(){return $this->_yanse;}
	public function get_zhushizhong(){return $this->_zhushizhong;}
	public function get_zhushi_num(){return $this->_zhushi_num;}
	public function get_fushizhong1(){return $this->_fushizhong1;}
	public function get_fushi_num1(){return $this->_fushi_num1;}
	public function get_fushizhong2(){return $this->_fushizhong2;}
	public function get_fushi_num2(){return $this->_fushi_num2;}
	public function get_fushi_chengbenjia_other(){return $this->_fushi_chengbenjia_other;}
	public function get_weight(){return $this->_weight;}
	public function get_jincha_shang(){return $this->_jincha_shang;}
	public function get_jincha_xia(){return $this->_jincha_xia;}
	public function get_dingzhichengben(){return $this->_dingzhichengben;}
	public function get_is_ok(){return $this->_is_ok;}
	public function get_last_update(){return $this->_last_update;}

}
?>