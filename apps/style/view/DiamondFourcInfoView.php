<?php
/**
 *  -------------------------------------------------
 *   @file		: DiamondFourcInfoView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-02-27 15:27:56
 *   @update	:
 *  -------------------------------------------------
 */
class DiamondFourcInfoView extends View
{
	protected $_id;
	protected $_shape;
	protected $_carat_min;
	protected $_carat_max;
	protected $_color;
	protected $_clarity;
	protected $_cert;
	protected $_price;
	protected $_status;
    protected $_attrModel;
    public function __construct($obj){
        parent::__construct($obj);
        $this->_attrModel = new GoodsAttributeModel(17);
    }
	public function get_id(){return $this->_id;}
	public function get_shape(){return $this->_shape;}
	public function get_carat_min(){return $this->_carat_min;}
	public function get_carat_max(){return $this->_carat_max;}
	public function get_color(){return $this->_color;}
	public function get_clarity(){return $this->_clarity;}
	public function get_cert(){return $this->_cert;}
	public function get_price(){return $this->_price;}
	public function get_status(){return $this->_status;}

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