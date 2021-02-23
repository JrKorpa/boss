<?php
/**
 *  -------------------------------------------------
 *   @file		: DiamondListView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 16:13:09
 *   @update	:
 *  -------------------------------------------------
 */
class ColorDiamondListView extends View
{

        public function getColor_arr() {
            return ColorDiamondListModel::$Color_arr;
        }
        
        public function getShape_arr() {
            return ColorDiamondListModel::$Shape_arr;
        }
        
        public function getClarity_arr() {
            return ColorDiamondListModel::$Clarity_arr;
        }
        
        public function getColor_grade_arr() {
            return ColorDiamondListModel::$Color_grade_arr;
        }
        
        public function getCert_arr() {
            return ColorDiamondListModel::$Cert_arr;
        }
        
        public function getFromad_arr() {
        	return ColorDiamondListModel::$Fromad_arr;
        }
        
        
        
        public function getFluorescence_arr() {
        	return ColorDiamondListModel::$Fluorescence_arr;
        }
        
        public function getPolish_arr() {
        	return ColorDiamondListModel::$Polish_arr;
        }
        
        public function getSymmetry_arr() {
        	return ColorDiamondListModel::$Symmetry_arr;
        }
        
        
        
      /*   public function getClarityList() {
            return ColorDiamondListModel::$Clarity_arr;
        }
        
        public function getShapeList() {
            return ColorDiamondListModel::$Shape_arr;
        }
        
        public function getCertList() {
            return ColorDiamondListModel::$Cert_arr;
        }
        
        //来源
        public function getGoodTypeList(){
            return array('1'=>'现货',2=>'期货');
        }
        
        //货品类型
        public function getFromAdList(){
            return array('1'=>'BDD',2=>'51钻');
        } */
        
}
?>