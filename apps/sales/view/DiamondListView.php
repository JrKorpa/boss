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
class DiamondListView extends View
{

        public function getCutList() {
            return DiamondListModel::$Cut_arr;
        }
        
        public function getPolishList() {
            return DiamondListModel::$Polish_arr;
        }
        
        public function getSymmetryList() {
            return DiamondListModel::$Symmetry_arr;
        }
        
        public function getFluorescenceList() {
            return DiamondListModel::$Fluorescence_arr;
        }
        
        public function getColorList() {
            return DiamondListModel::$Color_arr;
        }
        
        public function getClarityList() {
            return DiamondListModel::$Clarity_arr;
        }
        
        public function getShapeList() {
            return DiamondListModel::$Shape_arr;
        }
        
        public function getCertList() {
            return DiamondListModel::$Cert_arr;
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