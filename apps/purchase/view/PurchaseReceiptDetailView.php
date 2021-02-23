<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseReceiptDetailView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-15 21:24:19
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseReceiptDetailView extends View
{
	protected $_id;
	protected $_xuhao;
	protected $_status;
	protected $_purchase_receipt_id;
	protected $_purchase_sn;
	protected $_customer_name;
	protected $_bc_sn;
	protected $_style_sn;
	protected $_factory_sn;
	protected $_ring_mouth;
	protected $_product_type;
	protected $_cat_type;
	protected $_hand_inch;
	protected $_material;
	protected $_gross_weight;
	protected $_net_gold_weight;
	protected $_gold_loss;
	protected $_gold_price;
	protected $_main_stone;
	protected $_main_stone_weight;
	protected $_main_stone_num;
	protected $_work_fee;
	protected $_extra_stone_fee;
	protected $_other_fee;
	protected $_fittings_cost_fee;
	protected $_tax_fee;
	protected $_customer_info_stone;
        protected $_zhushiyanse;
        protected $_chengbenjia;
        protected $_zhushijingdu;
        protected $_zhushidanjia;
        protected $_fushilishu;
        protected $_fushizhong;
        protected $_fushidanjia;
        protected $_zhengshuhao;
        protected $_shi2;
        protected $_shi2lishu;
        protected $_shi2zhong;
        protected $_shi2danjia;
        protected $_shi3;
        protected $_shi3lishu;
        protected $_shi3zhong;
        protected $_shi3danjia;





        public function get_id(){return $this->_id;}
	public function get_xuhao(){return $this->_xuhao;}
	public function get_status(){return $this->_status;}
	public function get_purchase_receipt_id(){return $this->_purchase_receipt_id;}
	public function get_purchase_sn(){return $this->_purchase_sn;}
	public function get_customer_name(){return $this->_customer_name;}
	public function get_bc_sn(){return $this->_bc_sn;}
	public function get_style_sn(){return $this->_style_sn;}
	public function get_factory_sn(){return $this->_factory_sn;}
	public function get_ring_mouth(){return $this->_ring_mouth;}
	public function get_product_type(){return $this->_product_type;}
	public function get_cat_type(){return $this->_cat_type;}
	public function get_hand_inch(){return $this->_hand_inch;}
	public function get_material(){return $this->_material;}
	public function get_gross_weight(){return $this->_gross_weight;}
	public function get_net_gold_weight(){return $this->_net_gold_weight;}
	public function get_gold_loss(){return $this->_gold_loss;}
	public function get_gold_price(){return $this->_gold_price;}
	public function get_main_stone(){return $this->_main_stone;}
	public function get_main_stone_weight(){return $this->_main_stone_weight;}
	public function get_main_stone_num(){return $this->_main_stone_num;}
	public function get_work_fee(){return $this->_work_fee;}
	public function get_extra_stone_fee(){return $this->_extra_stone_fee;}
	public function get_other_fee(){return $this->_other_fee;}
	public function get_fittings_cost_fee(){return $this->_fittings_cost_fee;}
	public function get_tax_fee(){return $this->_tax_fee;}
	public function get_customer_info_stone(){return $this->_customer_info_stone;}
        public function get_zhushiyanse(){return $this->_zhushiyanse;}
        public function get_zhushijingdu(){return $this->_zhushijingdu;}
        public function get_chengbenjia(){return $this->_chengbenjia;}
        public function get_fushilishu(){return $this->_fushilishu;}
        public function get_fushizhong(){return $this->_fushizhong;}
        public function get_fushidanjia(){return $this->_fushidanjia;}
        public function get_zhengshuhao(){return $this->_zhengshuhao;}
        public function get_zhushidanjia(){return $this->_zhushidanjia;}
        public function get_shi2(){return $this->_shi2;}
        public function get_shi2zhong(){return $this->_shi2zhong;}
        public function get_shi2danjia(){return $this->_shi2danjia;}
        public function get_shi2lishu(){return $this->_shi2lishu;}
        public function get_shi3(){return $this->_shi3;}
        public function get_shi3zhong(){return $this->_shi3zhong;}
        public function get_shi3danjia(){return $this->_shi3danjia;}
        public function get_shi3lishu(){return $this->_shi3lishu;}

}
?>