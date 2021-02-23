<?php
/**
 *  -------------------------------------------------
 *   @file		: AppDiamondColorView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-02 15:07:13
 *   @update	:
 *  -------------------------------------------------
 */
class AppDiamondColorView extends View
{
	protected $_id;
	protected $_goods_sn;
	protected $_shape;
	protected $_carat;
	protected $_color;
	protected $_clarity;
	protected $_cut;
	protected $_polish;
	protected $_symmetry;
	protected $_fluorescence;
	protected $_measurements;
	protected $_cert;
	protected $_cert_id;
	protected $_price;
	protected $_cost_price;
	protected $_image1;
	protected $_image2;
	protected $_image3;
	protected $_image4;
	protected $_image5;
	protected $_from_ad;
	protected $_is_delete;
	protected $_add_time;
	protected $_color_grade;
	protected $_quantity;
	protected $_warehouse;
	protected $_status;
	protected $_goods_id;
	


	public function get_goods_id(){return $this->_id;}
	public function get_goods_sn(){return $this->_goods_sn;}
	public function get_shape(){return $this->_shape;}
	public function get_carat(){return $this->_carat;}
	public function get_color(){return $this->_color;}
	public function get_color_grade(){return $this->_color_grade;}
	public function get_clarity(){return $this->_clarity;}
	public function get_cut(){return $this->_cut;}
	public function get_polish(){return $this->_polish;}
	public function get_symmetry(){return $this->_symmetry;}
	public function get_fluorescence(){return $this->_fluorescence;}
	public function get_table(){return $this->_table;}
	public function get_depth(){return $this->_depth;}
	public function get_measurements(){return $this->_measurements;}
	public function get_cert(){return $this->_cert;}
	public function get_cert_id(){return $this->_cert_id;}
	public function get_price(){return $this->_price;}
	public function get_cost_price(){return $this->_cost_price;}
	public function get_image1(){return $this->_image1;}
	public function get_image2(){return $this->_image2;}
	public function get_image3(){return $this->_image3;}
	public function get_image4(){return $this->_image4;}
	public function get_image5(){return $this->_image5;}
	public function get_from_ad(){return $this->_from_ad;}
	public function get_is_delete(){return $this->_is_delete;}
	public function get_add_time(){return $this->_add_time;}
	public function get_quantity(){return $this->_quantity;}
	public function get_warehouse(){return $this->_warehouse;}
	public function get_status(){return $this->_status;}
	public function get_good_id(){return $this->_goods_id;}

        public function getColorList() {
            return AppDiamondColorModel::$Color_arr;
        }
        
        public function getShapeList() {
            return AppDiamondColorModel::$Shape_arr;
        }
        
        public function getClarityList() {
            return AppDiamondColorModel::$Clarity_arr;
        }
        
        public function getColor_gradeList() {
            return AppDiamondColorModel::$Color_grade_arr;
        }
        
        public function getCertList() {
            return AppDiamondColorModel::$Cert_arr;
        }
        
        public function getFrom_ad(){
        	return AppDiamondColorModel::$from_ad;
        	
        }
        //货品类型
        public function getGoodTypeList(){
        	return AppDiamondColorModel::$goods_type;
        }
        
        //来源
        public function getFromAdList(){
        	return AppDiamondColorModel::$from_ad;
        }
        
        //对称性
		public function getSymmetryList(){
			
			return AppDiamondColorModel::$symmetry;
		}  

		//抛光
		public function getPolishList(){
				
			return AppDiamondColorModel::$polish;
		}
        
		//荧光
		public function getFluorescenceList(){
		
			return AppDiamondColorModel::$fluorescence;
		}
        
		//状态  1-上架    0-下架
		public function getStatus(){
			return AppDiamondColorModel::$status;
		}
		
}
?>