<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderCartView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-27 19:44:38
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderCartView extends View
{
	protected $_id;
	protected $_session_id;
	protected $_goods_id;
	protected $_goods_sn;
	protected $_product_type;
	protected $_goods_type;
	protected $_goods_name;
	protected $_goods_price;
	protected $_favorable_price;
	protected $_goods_count;
	protected $_is_stock_goods;
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
	protected $_create_time;
	protected $_modify_time;
	protected $_create_uid;
	protected $_create_user;
    protected $_department_id;
    protected $_type;
    protected $_policy_goods_id;
    protected $_kuan_sn;


    public function get_id(){return $this->_id;}
	public function get_session_id(){return $this->_session_id;}
	public function get_goods_id(){return $this->_goods_id;}
	public function get_goods_sn(){return $this->_goods_sn;}
	public function get_product_type(){return $this->_product_type;}
	public function get_goods_type(){return $this->_goods_type;}
	public function get_goods_name(){return $this->_goods_name;}
	public function get_goods_price(){return $this->_goods_price;}
	public function get_favorable_price(){return $this->_favorable_price;}
	public function get_goods_count(){return $this->_goods_count;}
	public function get_is_stock_goods(){return $this->_is_stock_goods;}
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
	public function get_create_time(){return $this->_create_time;}
	public function get_modify_time(){return $this->_modify_time;}
	public function get_create_uid(){return $this->_create_uid;}
	public function get_create_user(){return $this->_create_user;}
    public function get_department_id(){return $this->_department_id;}
    public function get_type() {return $this->_type;}
    public function get_policy_goods_id(){return $this->_policy_goods_id;}
    public function get_kuan_sn(){return $this->_kuan_sn;}
    

}
?>