<?php
/**
 *  -------------------------------------------------
 *   @file		: JitxAppOrderDetailsView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2019-06-28 10:36:12
 *   @update	:
 *  -------------------------------------------------
 */
class JitxAppOrderDetailsView extends View
{
	protected $_id;
	protected $_order_id;
	protected $_goods_id;
	protected $_goods_sn;
	protected $_ext_goods_sn;
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
	protected $_cert;
	protected $_zhengshuhao;
	protected $_caizhi;
	protected $_jinse;
	protected $_jinzhong;
	protected $_zhiquan;
	protected $_kezi;
	protected $_face_work;
	protected $_xiangqian;
	protected $_goods_type;
	protected $_favorable_status;
	protected $_cat_type;
	protected $_product_type;
	protected $_kuan_sn;
	protected $_xiangkou;
	protected $_chengbenjia;
	protected $_bc_id;
	protected $_policy_id;
	protected $_is_peishi;
	protected $_is_zp;
	protected $_is_finance;
	protected $_weixiu_status;
	protected $_allow_favorable;
	protected $_qiban_type;
	protected $_delivery_status;
	protected $_retail_price;
	protected $_ds_xiangci;
	protected $_pinhao;
	protected $_dia_type;
	protected $_is_cpdz;
	protected $_tuo_type;
	protected $_zhushi_num;
	protected $_cpdzcode;
	protected $_discount_point;
	protected $_reward_point;
	protected $_daijinquan_code;
	protected $_daijinquan_price;
	protected $_daijinquan_addtime;
	protected $_jifenma_code;
	protected $_jifenma_point;
	protected $_zhuandan_cash;
	protected $_goods_from;


	public function get_id(){return $this->_id;}
	public function get_order_id(){return $this->_order_id;}
	public function get_goods_id(){return $this->_goods_id;}
	public function get_goods_sn(){return $this->_goods_sn;}
	public function get_ext_goods_sn(){return $this->_ext_goods_sn;}
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
	public function get_cert(){return $this->_cert;}
	public function get_zhengshuhao(){return $this->_zhengshuhao;}
	public function get_caizhi(){return $this->_caizhi;}
	public function get_jinse(){return $this->_jinse;}
	public function get_jinzhong(){return $this->_jinzhong;}
	public function get_zhiquan(){return $this->_zhiquan;}
	public function get_kezi(){return $this->_kezi;}
	public function get_face_work(){return $this->_face_work;}
	public function get_xiangqian(){return $this->_xiangqian;}
	public function get_goods_type(){return $this->_goods_type;}
	public function get_favorable_status(){return $this->_favorable_status;}
	public function get_cat_type(){return $this->_cat_type;}
	public function get_product_type(){return $this->_product_type;}
	public function get_kuan_sn(){return $this->_kuan_sn;}
	public function get_xiangkou(){return $this->_xiangkou;}
	public function get_chengbenjia(){return $this->_chengbenjia;}
	public function get_bc_id(){return $this->_bc_id;}
	public function get_policy_id(){return $this->_policy_id;}
	public function get_is_peishi(){return $this->_is_peishi;}
	public function get_is_zp(){return $this->_is_zp;}
	public function get_is_finance(){return $this->_is_finance;}
	public function get_weixiu_status(){return $this->_weixiu_status;}
	public function get_allow_favorable(){return $this->_allow_favorable;}
	public function get_qiban_type(){return $this->_qiban_type;}
	public function get_delivery_status(){return $this->_delivery_status;}
	public function get_retail_price(){return $this->_retail_price;}
	public function get_ds_xiangci(){return $this->_ds_xiangci;}
	public function get_pinhao(){return $this->_pinhao;}
	public function get_dia_type(){return $this->_dia_type;}
	public function get_is_cpdz(){return $this->_is_cpdz;}
	public function get_tuo_type(){return $this->_tuo_type;}
	public function get_zhushi_num(){return $this->_zhushi_num;}
	public function get_cpdzcode(){return $this->_cpdzcode;}
	public function get_discount_point(){return $this->_discount_point;}
	public function get_reward_point(){return $this->_reward_point;}
	public function get_daijinquan_code(){return $this->_daijinquan_code;}
	public function get_daijinquan_price(){return $this->_daijinquan_price;}
	public function get_daijinquan_addtime(){return $this->_daijinquan_addtime;}
	public function get_jifenma_code(){return $this->_jifenma_code;}
	public function get_jifenma_point(){return $this->_jifenma_point;}
	public function get_zhuandan_cash(){return $this->_zhuandan_cash;}
	public function get_goods_from(){return $this->_goods_from;}

}
?>