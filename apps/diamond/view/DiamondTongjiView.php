<?php
/**
 *  -------------------------------------------------
 *   @file		: DiamondInfoView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 16:13:09
 *   @update	:
 *  -------------------------------------------------
 */
class DiamondTongjiView extends View
{
	protected $_goods_id;
	protected $_goods_sn;
	protected $_goods_name;
	protected $_goods_number;
	protected $_market_price;
	protected $_shop_price;
	protected $_member_price;
	protected $_chengben_jia;
	protected $_carat;
	protected $_clarity;
	protected $_cut;
	protected $_color;
	protected $_shape;
	protected $_depth_lv;
	protected $_table_lv;
	protected $_symmetry;
	protected $_polish;
	protected $_fluorescence;
	protected $_cert;
	protected $_cert_id;
	protected $_gemx_zhengshu;
	protected $_status=1;
	protected $_add_time;
	protected $_is_active=1;
	protected $_from_ad;
	protected $_good_type;


	public function get_goods_id(){return $this->_goods_id;}
	public function get_goods_sn(){return $this->_goods_sn;}
	public function get_goods_name(){return $this->_goods_name;}
	public function get_goods_number(){return $this->_goods_number;}
	public function get_good_type(){return $this->_good_type;}
	public function get_from_ad(){return $this->_from_ad;}
	public function get_market_price(){return $this->_market_price;}
	public function get_shop_price(){return $this->_shop_price;}
	public function get_member_price(){return $this->_member_price;}
	public function get_chengben_jia(){return $this->_chengben_jia;}
	public function get_carat(){return $this->_carat;}
	public function get_clarity(){return $this->_clarity;}
	public function get_cut(){return $this->_cut;}
	public function get_color(){return $this->_color;}
	public function get_shape(){return $this->_shape;}
	public function get_depth_lv(){return $this->_depth_lv;}
	public function get_table_lv(){return $this->_table_lv;}
	public function get_symmetry(){return $this->_symmetry;}
	public function get_polish(){return $this->_polish;}
	public function get_fluorescence(){return $this->_fluorescence;}
	public function get_cert(){return $this->_cert;}
	public function get_cert_id(){return $this->_cert_id;}
	public function get_gemx_zhengshu(){return $this->_gemx_zhengshu;}
	public function get_status(){return $this->_status;}
	public function get_add_time(){return $this->_add_time;}
	public function get_is_active(){return $this->_is_active;}

        public function getCutList() {
            return DiamondInfoModel::$Cut_arr;
        }
        
        public function getPolishList() {
            return DiamondInfoModel::$Polish_arr;
        }
        
        public function getSymmetryList() {
            return DiamondInfoModel::$Symmetry_arr;
        }
        
        public function getFluorescenceList() {
            return DiamondInfoModel::$Fluorescence_arr;
        }
        
        public function getColorList() {
            return DiamondInfoModel::$Color_arr;
        }
        
        public function getClarityList() {
            return DiamondInfoModel::$Clarity_arr;
        }
        
        public function getShapeList() {
            return DiamondInfoModel::$Shape_arr;
        }
        
        public function getCertList() {
            return DiamondInfoModel::$Cert_arr;
        }
        
        //来源
        public function getGoodTypeList(){
            return array('1'=>'现货',2=>'期货');
        }
        
        //货品类型
        public function getFromAdList(){
            return array('1'=>'BDD',2=>'51钻');
        }
        
}
?>