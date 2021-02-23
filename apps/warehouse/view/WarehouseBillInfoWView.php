<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoWView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-18 18:49:32
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoWView extends View
{
	protected $_id;
	protected $_bill_id;
	protected $_box_sn;


	public function get_id(){return $this->_id;}
	public function get_bill_id(){return $this->_bill_id;}
	public function get_box_sn(){return $this->_box_sn;}

	public function GetWarehouseName($warehouse_id){
		$model = new WarehouseModel($warehouse_id , 21);
		return $model->getValue('name');
	}

}
?>