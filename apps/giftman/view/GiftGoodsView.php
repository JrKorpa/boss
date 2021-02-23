<?php
/**
 *  -------------------------------------------------
 *   @file		: GiftGoodsView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-05-12 18:37:15
 *   @update	:
 *  -------------------------------------------------
 */
class GiftGoodsView extends View
{
	protected $_id;
	protected $_name;
	protected $_num;
	protected $_min_num;
	protected $_price;
	protected $_sell_sprice;
	protected $_status;
	protected $_goods_number;
	protected $_sell_type;
	protected $_add_time;
	protected $_update_time;
    protected $_is_xz;
    protected $_sale_way;
    protected $_is_zp;


	public function get_id(){return $this->_id;}
	public function get_name(){return $this->_name;}
	public function get_num(){return $this->_num;}
	public function get_min_num(){return $this->_min_num;}
	public function get_price(){return $this->_price;}
	public function get_sell_sprice(){return $this->_sell_sprice;}
	public function get_status(){return $this->_status;}
	public function get_goods_number(){return $this->_goods_number;}
	public function get_sell_type(){return $this->_sell_type;}
	public function get_add_time(){return $this->_add_time;}
	public function get_update_time(){return $this->_update_time;}
    public function get_is_xz(){return $this->_is_xz;}
    public function get_sale_way(){return $this->_sale_way;}
    public function get_is_zp(){return $this->_is_zp;}

}
?>