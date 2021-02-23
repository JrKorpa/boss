<?php
/**
 *  -------------------------------------------------
 *   @file		: VipPickDetailsView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @date		: 2017-06-26 21:17:07
 *   @update	:
 *  -------------------------------------------------
 */
class VipPickDetailsView extends View
{
    public function __construct($obj){
        parent::__construct($obj);
        $this->_attrModel = new GoodsAttributeModel(17);
    }
    
    public function getColorList() {
        return $this->_attrModel->getColorList();
    }
    
    public function getClarityList() {
        return $this->_attrModel->getClarityList();
    }
    
    public function getCertList() {
        return $this->_attrModel->getCertList();
    }
    
    public function getCaizhiList() {
        return $this->_attrModel->getCaizhiList();
    }
    
    public function getJinseList() {
        return $this->_attrModel->getJinseList();
    }
    
    public function getXiangqianList() {
        $list = $this->_attrModel->getXiangqianList();
        foreach ($list as $key=>$vo){
            if(preg_match('/4c/is',$vo)){
               unset($list[$key]);
            }
        }
        return $list;
    }    
    public function getFaceworkList() {
        return $this->_attrModel->getFaceworkList();
    }
    //唯品会发货地址
    public static function getAddressList(){
       return VipDeliveryView::getAddressList();       
    }
    //获取数组格式 配送地址 
    public static function getAddressInfo($region){
        return VipDeliveryView::getAddressInfo($region);
    }
    //获取 字符串格式 配送地址
    public static function getAddressName($region){
        return VipDeliveryView::getAddressName($region);
    }
    //获取仓库列表
    public static function getWarehouseList(){        
        return VipDeliveryView::getWarehouseList();
    }
    public static function getAddressInfoByWarehoue($warehouse){
        return VipDeliveryView::getAddressInfoByWarehoue($warehouse);
    }    
    //获取仓库名称
    public static function getWarehouseName($code){
        return VipDeliveryView::getWarehouseName($code);
    }
    public static function getWarehouseValue($code){
       return VipDeliveryView::getWarehouseValue($code);
    }
    //jit 合作模式列表
    public static function  getCoModeList(){
        return array('jit'=>'普通JIT','jit_4a'=>'JIT(分销)');
    }
    //jit 合作模式名称
    public static function getCoModeName($coMode){
        $coModeList = self::getCoModeList();
        if(isset($coModeList[$coMode])){
            return $coModeList[$coMode];
        }else{
            return $coMode;
        }
    }
    //获取订单类别列表  
    public static function  getOrderCateList(){
        return array('normal'=>'普通模式');
    }
    //获取订单类别
    public static function getOrderCateName($orderCateCode){
        $orderCateList = self::getOrderCateList();
        if(isset($orderCateList[$orderCateCode])){
            return $orderCateList[$orderCateCode];
        }else{
            return $orderCateCode;
        }
    }
    public static function getDeliveryStatusList(){
        return VipDeliveryView::getDeliveryStatusList();
    }
    public function getDeliveryStatusName($code){
       return VipDeliveryView::getDeliveryStatusName($code);
       
   }
   public static function getBossPickStatusList(){
       return array(0=>'未完成',1=>'已完成');
   }
   public function getBossPickStatusName($code){
       $bossPickStatusList = self::getBossPickStatusList();
       if(isset($bossPickStatusList[$code])){
           return $bossPickStatusList[$code];
       }else{
           return $code;
       }
   }
    
}
?>