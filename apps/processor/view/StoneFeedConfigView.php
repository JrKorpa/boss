<?php
/**
 * 裸石供料配置 
 *  -------------------------------------------------
 *   @file		: PeishiListView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @date		: 2017-06-20
 *   @update	:
 *  -------------------------------------------------
 */
class StoneFeedConfigView extends View
{

	protected $_id;
	protected $_color;//'颜色',
	protected $_clarity;//'净度',
	protected $_cert;//'证书类型',
	protected $_carat_min;//'石重下限（最小值）',
	protected $_carat_max;//'石重上限(最大值)',
	protected $_factory_id;//'工厂ID',
	protected $_factory_name;//'工厂名称',
	protected $_feed_type;//'供料类型 ',
	protected $_prority_sort;//'优先级排序',
	protected $_create_time;//'添加时间',
	protected $_create_user;//'添加人',
	protected $_is_enable;//'是否可用',
	
	protected $_attrModel;
	protected $_procModel;
	public function __construct($obj){
	    parent::__construct($obj);
	    $this->_attrModel = new GoodsAttributeModel(17);
	    $this->_procModel = new AppProcessorInfoModel(13);
	}
	public function get_id(){return $this->_id;}
	public function get_color(){return $this->_color;}
	public function get_clarity(){return $this->_clarity;}
	public function get_cert(){return $this->_cert;}
	public function get_carat_min(){return $this->_carat_min;}
	public function get_carat_max(){return $this->_carat_max;}
	public function get_factory_id(){return $this->_factory_id;}
	public function get_factory_name(){return $this->_factory_name;}
	public function get_feed_type(){return $this->_feed_type;}
	public function get_prority_sort(){return $this->_prority_sort;}
	public function get_create_time(){return $this->_create_time;}
	public function get_create_user(){return $this->_create_user;}
	public function get_is_enable(){return $this->_is_enable;}
	
	//供应商列表
	public function getFactoryList(){
        return $this->_procModel->getProList();
	}
	public function getColorList() {
	    $colorList1 = array('ALL'=>'ALL');
	    $colorList2 = $this->_attrModel->getColorList();	    
	    $colorList = array_merge($colorList1,$colorList2);
	    $colorList = array_unique($colorList);
	    return $colorList;
	}
	
	public function getClarityList() {
	    $clarityList1 = array('ALL'=>'ALL');
	    $clarityList2 = $this->_attrModel->getClarityList();
	    $clarityList = array_merge($clarityList1,$clarityList2);
	    $clarityList = array_unique($clarityList);
	    return $clarityList;
	}
	
	public function getShapeList() {
	    $shapeList1 = array('ALL'=>'ALL');
	    $shapeList2 = $this->_attrModel->getShapeList();
	    $shapeList = array_merge($shapeList1,$shapeList2);
	    $shapeList = array_unique($shapeList1);
	    return $shapeList;
	}
	
	public function getCertList() {
	    $certList1 = array('ALL'=>'ALL');
	    $certList2 = $this->_attrModel->getCertList();
	    $certList = array_merge($certList1,$certList2);
	    $certList = array_unique($certList);
	    return $certList;
	}
	
	public function getStatusList(){
	    $data = array(1=>'启用',0=>'禁用');
	    return $data;
	}
}
?>