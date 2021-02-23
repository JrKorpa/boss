<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 19:07:53
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseView extends View
{
	protected $_id;
	protected $_name;
	protected $_code;
	protected $_remark;
	protected $_create_time;
	protected $_create_user;
	protected $_is_delete;
    protected $_type;
    protected $_is_default;


	public function get_id(){return $this->_id;}
	public function get_name(){return $this->_name;}
	public function get_code(){return $this->_code;}
        public function get_type(){return $this->_type;}
	public function get_remark(){return $this->_remark;}
	public function get_create_time(){return $this->_create_time;}
	public function get_create_user(){return $this->_create_user;}
	public function get_is_delete(){return isset($this->_is_delete)?$this->_is_delete:1;}
	public function get_is_default(){return isset($this->_is_default)?$this->_is_default:0;}
	//public function get_is_delete(){return $this->_is_delete;}
	public function get_pid()
	{
		$id = $this->_id;
		//根据仓库id去查询公司id
		if ($id)
		{
			$warehouseRelModel = new WarehouseRelModel(21);
			$res = $warehouseRelModel->select(array('warehouse_id'=>$id), false);
			return $res['company_id'];
		}
	}

	/** 返回指定仓库下的货品数量 **/
	public function getGoodsNum($warehouse_id){
		$model = new WarehouseModel(21);
		$data = $model->check_warehouse_goods($warehouse_id);
		return count($data);
	}
}
?>