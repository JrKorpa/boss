<?php
/**
 *  -------------------------------------------------
 *   @file		: GoodsWarehouseView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-11 10:55:30
 *   @update	:
 *  -------------------------------------------------
 */
class GoodsWarehouseView extends View
{
	protected $_id;
	protected $_good_id;
	protected $_warehouse_id;
	protected $_box_id;
	protected $_add_time;
	protected $_create_time;
	protected $_create_user;

	public function get_id(){return $this->_id;}
	public function get_good_id(){return $this->_good_id;}
	public function get_warehouse_id(){return $this->_warehouse_id;}
	public function get_box_id(){return $this->_box_id;}
	public function get_add_time(){return $this->_add_time;}
	public function get_create_time(){return $this->_create_time;}
	public function get_create_user(){return $this->_create_user;}

	/** 根据货号，获取货品所在的仓库 **/
	public function getWarehouse($good_id){
		$warehouse = '';
		$model = new GoodsWarehouseModel(21);
		$data = $model->getWarehouseInfo($good_id);
		if($data['warehouse_id'] !=''){
			$warehouseView = new WarehouseView(new WarehouseModel($data['warehouse_id'], 21));
			$warehouse = $warehouseView->get_name();
		}
		return $warehouse;
	}
        /*获取所有公司列表*/
        public function getCompanyList(){
            $model     = new CompanyModel(1);
		$company   = $model->getCompanyTree();//公司列表
		return $company;
            
        }

	/** 获取所有仓库数据列表 **/
	public function getWarehouseList(){
		$model = new WarehouseModel(21);
		return $model->select( array('is_delete' => 1));
	}

	/** 根据筐位ID，获取筐位名 **/
	public function getBoxName($box_id){
		$BoxModel = new WarehouseBoxModel($box_id , 21);
		return $BoxModel->getValue('box_sn');
	}

}
?>