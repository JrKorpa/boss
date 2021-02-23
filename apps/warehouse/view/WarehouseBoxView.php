<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBoxView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 17:34:45
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBoxView extends View
{
	protected $_id;
	protected $_warehouse_id;
	protected $_box_sn;
	protected $_create_time;
	protected $_create_name;
	protected $_is_deleted;
	protected $_info;


	public function get_id(){return $this->_id;}
	public function get_warehouse_id(){return $this->_warehouse_id;}
	public function get_box_sn(){return $this->_box_sn;}
	public function get_create_time(){return $this->_create_time;}
	public function get_create_name(){return $this->_create_name;}
	public function get_is_deleted(){return $this->_is_deleted;}
	public function get_info(){return $this->_info;}

	/** 根据warehouse_id 获取仓库名 **/
	public function getWarehouseName($warehouse_id){
		/*$warehouseView = new WarehouseView(new WarehouseModel($warehouse_id, 21));
		return $warehouseView->get_name();*/
		$model = new WarehouseModel(21);
		$sql = "SELECT `name` FROM `warehouse` WHERE `id`=$warehouse_id LIMIT 1";
		return $model->db()->getOne($sql);
	}

	/** 根据柜位ID box_id 获取柜位下的货品数量 **/
	public function getGoodsSumByBox($box_id){
		$model = new WarehouseBoxModel(21);
		return $model->sumGoodsByBox($box_id);
	}

}
?>