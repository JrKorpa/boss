<?php
/**
 *  -------------------------------------------------
 *   @file		: MaterialInventoryView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2018-01-18 14:01:12
 *   @update	:
 *  -------------------------------------------------
 */
class MaterialInventoryView extends View
{
	protected $_id;
	protected $_goods_sn;
	protected $_supplier_id;
	protected $_warehouse_id;
	protected $_batch_sn;
	protected $_inventory_qty;
	protected $_cost_price;


	public function get_id(){return $this->_id;}
	public function get_goods_sn(){return $this->_goods_sn;}
	public function get_supplier_id(){return $this->_supplier_id;}
	public function get_warehouse_id(){return $this->_warehouse_id;}
	public function get_batch_sn(){return $this->_batch_sn;}
	public function get_inventory_qty(){return $this->_inventory_qty;}
	public function get_cost_price(){return $this->_cost_price;}
    
	public function get_company_list()
	{
	    $model     = new CompanyModel(1);
	    $company   = $model->getCompanyTree();//公司列表
	    return $company;
	}
	/**
	 * 获取有效的仓库
	 * @return unknown
	 */
	public function get_warehouse_list()
	{
	    $model	= new WarehouseModel(21);
	    $warehouse  = $model->select2("id,name","is_delete=1 and type=12","all");
	    return $warehouse;
	}
	/**
	 * 获取供应商列表
	 * @return unknown
	 */
	public function get_supplier_list(){
	    $model = new ApiProModel();
	    $pro_list = $model->GetSupplierList(array('status'=>1,'code'=>'wkc'));
	    return $pro_list;
	}
}
?>