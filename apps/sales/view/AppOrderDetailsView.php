<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderDetailsView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 21:17:07
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderDetailsView extends View
{
	protected $_id;
	protected $_order_id;
	protected $_goods_id;
	protected $_goods_sn;
	protected $_goods_name;
	protected $_goods_price;
	protected $_favorable_price;
	protected $_goods_count;
	protected $_create_time;
	protected $_modify_time;
	protected $_create_user;
	protected $_details_status;
	protected $_send_good_status;
	protected $_buchan_status;
	protected $_is_stock_goods;
	protected $_is_return;
	protected $_details_remark;
	protected $_cart;
	protected $_cut;
	protected $_clarity;
	protected $_color;
	protected $_zhengshuhao;
	protected $_caizhi;
	protected $_jinse;
	protected $_jinzhong;
	protected $_zhiquan;
	protected $_kezi;
	protected $_face_work;
	protected $_xiangqian;
	protected $_goods_type;
	protected $_cat_type;
	protected $_product_type;
	protected $_buchan_id;
	protected $_favorable_status;
	protected $_qiban_type;
    protected $_ds_xiangci;
    protected $_pinhao;
    protected $_dia_type;

	public function get_id(){return $this->_id;}
	public function get_order_id(){return $this->_order_id;}
	public function get_goods_id(){return $this->_goods_id;}
	public function get_goods_sn(){return $this->_goods_sn;}
	public function get_goods_name(){return $this->_goods_name;}
	public function get_goods_price(){return $this->_goods_price;}
	public function get_favorable_price(){return $this->_favorable_price;}
	public function get_goods_count(){return $this->_goods_count;}
	public function get_create_time(){return $this->_create_time;}
	public function get_modify_time(){return $this->_modify_time;}
	public function get_create_user(){return $this->_create_user;}
	public function get_details_status(){return $this->_details_status;}
	public function get_send_good_status(){return $this->_send_good_status;}
	public function get_buchan_status(){return $this->_buchan_status;}
	public function get_is_stock_goods(){return $this->_is_stock_goods;}
	public function get_is_return(){return $this->_is_return;}
	public function get_details_remark(){return $this->_details_remark;}
	public function get_cart(){return $this->_cart;}
	public function get_cut(){return $this->_cut;}
	public function get_clarity(){return $this->_clarity;}
	public function get_color(){return $this->_color;}
	public function get_zhengshuhao(){return $this->_zhengshuhao;}
	public function get_caizhi(){return $this->_caizhi;}
	public function get_jinse(){return $this->_jinse;}
	public function get_jinzhong(){return $this->_jinzhong;}
	public function get_zhiquan(){return $this->_zhiquan;}
	public function get_kezi(){return $this->_kezi;}
	public function get_face_work(){return $this->_face_work;}
	public function get_xiangqian(){return $this->_xiangqian;}
	public function get_xiangkou(){return $this->_xiangkou;}
	public function get_goods_type(){return $this->_goods_type;}
	public function get_cat_type(){return $this->_cat_type;}
	public function get_product_type(){return $this->_product_type;}
	public function get_buchan_id(){return $this->_buchan_id;}
	public function get_favorable_status(){return $this->_favorable_status;}
	public function get_qiban_type(){return $this->_qiban_type;}
    public function get_ds_xiangci(){return $this->_ds_xiangci;}
    public function get_pinhao(){return $this->_pinhao;}
    public function get_dia_type(){return $this->_dia_type;}
}
?>