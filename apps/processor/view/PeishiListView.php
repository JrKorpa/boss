<?php
/**
 *  -------------------------------------------------
 *   @file		: PeishiListView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-14 21:00:23
 *   @update	:
 *  -------------------------------------------------
 */
class PeishiListView extends View
{
	protected $_id;
	protected $_order_sn;
	protected $_rec_id;
	protected $_peishi_status;
	protected $_add_time;
	protected $_last_time;
	protected $_add_user;

	public function __construct($obj){
	    parent::__construct($obj);
	    $this->_attrModel = new GoodsAttributeModel(17);
	}
	public function get_id(){return $this->_id;}
	public function get_order_sn(){return $this->_order_sn;}
	public function get_rec_id(){return $this->_rec_id;}
	public function get_peishi_status(){return $this->_peishi_status;}
	public function get_add_time(){return $this->_add_time;}
	public function get_last_time(){return $this->_last_time;}
	public function get_add_user(){return $this->_add_user;}
	
	public function getColorList() {
	    return $this->_attrModel->getColorList();
	}
	
	public function getClarityList() {
	    return $this->_attrModel->getClarityList();
	}
	
	public function getShapeList() {
	    $shapeList = $this->_attrModel->getShapeList();
	    array_push($shapeList,'无');
	    return $shapeList;
	}
	
	public function getCertList() {
	    return $this->_attrModel->getCertList();
	}
	
	public function getBuchanTypeList(){
	    return array('普通件'=>'普通件','加急件'=>'加急件');	    
	}
	public function getBuchanCatList(){
	    return array('1'=>'备货单(采购单)','2'=>'客单(订单)',);//客单、备货单
	}
	public function getStoneCatList(){
	    return array("0"=>"无","1"=>"圆钻","2"=>"异形钻","3"=>"珍珠","4"=>"翡翠","5"=>"红宝石","6"=>"蓝宝石","7"=>"和田玉","8"=>"水晶","9"=>"珍珠贝","10"=>"碧玺","11"=>"玛瑙","12"=>"月光石","13"=>"托帕石","14"=>"石榴石","15"=>"绿松石","16"=>"芙蓉石","17"=>"祖母绿","18"=>"贝壳","19"=>"橄榄石","20"=>"彩钻","21"=>"葡萄石","22"=>"海蓝宝","23"=>"坦桑石","24"=>"粉红宝","25"=>"沙佛莱","26"=>"粉红蓝宝石");
	}
	
	public function getCompanyType($company_id){
	    $model = new CompanyModel($company_id,1);
	    $company_type = $model->getValue("company_type");
	    return $company_type;
	}

}
?>