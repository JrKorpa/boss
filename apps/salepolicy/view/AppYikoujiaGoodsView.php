<?php
/**
 *  -------------------------------------------------
 *   @file		: AppSalepolicyGoodsView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-17 18:36:47
 *   @update	:
 *  -------------------------------------------------
 */
class AppYikoujiaGoodsView extends View
{
	protected $_id;	
	protected $_goods_id;
	protected $_goods_sn;
	protected $_price;
	protected $_caizhi;
	protected $_small;
	protected $_sbig;
	protected $_policy_id;
	protected $_tuo_type;
	public function __construct($obj){
	    parent::__construct($obj);
	    $this->_attrModel = new GoodsAttributeModel(17);
	}
	public function get_id(){return $this->_id;}
	public function get_goods_id(){return $this->_goods_id;}
	public function get_goods_sn(){return $this->_goods_sn;}
	public function get_price(){return $this->_price;}
	public function get_caizhi(){return $this->_caizhi;}
	public function get_small(){return $this->_small;}
	public function get_sbig(){return $this->_sbig;}
	public function get_policy_id(){return $this->_policy_id;}
	public function get_tuo_type(){return $this->_tuo_type;}

    public function getColorList() {
        return $this->_attrModel->getColorList();
    }
    
    public function getClarityList() {
        return $this->_attrModel->getClarityList();
    }
    
    public function getShapeList() {
        return $this->_attrModel->getShapeList();
    }
    
    public function getCertList() {
        return $this->_attrModel->getCertList();
    }

}
?>