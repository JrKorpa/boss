<?php
/**
 *  -------------------------------------------------
 *   @file		: MaterialGoodsView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2018-01-18 11:08:37
 *   @update	:
 *  -------------------------------------------------
 */
class MaterialGoodsView extends View
{
	protected $_id;
	protected $_style_sn;
	protected $_style_name;
	protected $_goods_sn;
	protected $_goods_name;
	protected $_goods_spec;
	protected $_catetory1;
	protected $_catetory2;
	protected $_catetory3;
	protected $_create_user;
	protected $_create_time;
	protected $_update_user;
	protected $_update_time;
	protected $_cost;
    protected $_unit;
    protected $_goods_sale_price;
    protected $_goods_jiajialv;
    protected $_goods_type;
    protected $_caizhi;
    protected $_min_qty;
    protected $_pack_qty;
    protected $_remark;
    
	public function get_id(){return $this->_id;}
	public function get_style_sn(){return $this->_style_sn;}
	public function get_style_name(){return $this->_style_name;}
	public function get_goods_sn(){return $this->_goods_sn;}
	public function get_goods_name(){return $this->_goods_name;}
	public function get_goods_spec(){return $this->_goods_spec;}
	public function get_catetory1(){return $this->_catetory1;}
	public function get_catetory2(){return $this->_catetory2;}
	public function get_catetory3(){return $this->_catetory3;}
	public function get_create_user(){return $this->_create_user;}
	public function get_create_time(){return $this->_create_time;}
	public function get_update_user(){return $this->_update_user;}
	public function get_update_time(){return $this->_update_time;}
	public function get_cost(){return $this->_cost;}
    public function get_unit(){return $this->_unit;}
    public function get_goods_sale_price(){return $this->_goods_sale_price;}
    public function get_goods_jiajialv(){return $this->_goods_jiajialv;}
    public function get_goods_type(){return $this->_goods_type;}
    public function get_caizhi(){return $this->_caizhi;}
    public function get_min_qty(){return $this->_min_qty;}
    public function get_pack_qty(){return $this->_pack_qty;}
    public function get_remark(){return $this->_remark;}
}
?>