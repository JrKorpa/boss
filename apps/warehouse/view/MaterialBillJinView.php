<?php
/**
 *  -------------------------------------------------
 *   @file		: MaterialBillView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2018-01-18 14:00:47
 *   @update	:
 *  -------------------------------------------------
 */
class MaterialBillJinView extends View
{
	protected $_id;
	protected $_bill_no;
	protected $_bill_type;
	protected $_bill_status;
	protected $_bill_note;
	protected $_create_user;
	protected $_create_time;
	protected $_check_user;
	protected $_check_time;
	protected $_department_id;
	protected $_warehouse_id;
	protected $_supplier_id;
	protected $_from_bill_no;
	public function get_id(){return $this->_id;}
	public function get_bill_no(){return $this->_bill_no;}
	public function get_bill_type(){return $this->_bill_type;}
	public function get_bill_status(){return $this->_bill_status;}
	public function get_bill_note(){return $this->_bill_note;}
	public function get_create_user(){return $this->_create_user;}
	public function get_create_time(){return $this->_create_time;}
	public function get_check_user(){return $this->_check_user;}
	public function get_check_time(){return $this->_check_time;}
	public function get_department_id(){return $this->_department_id;}
	public function get_warehouse_id(){return $this->_warehouse_id;}
	public function get_supplier_id(){return $this->_supplier_id;}
	public function get_from_bill_no(){return $this->_from_bill_no;}
	
	/**
	 * 获取销售渠道名称
	 * @param string $department_id
	 * @return string
	 */
	public function get_department_name($department_id=false){
	   $department_name = '';
       if($department_id===false){
           $department_id = $this->_department_id;
       }
       if(!empty($department_id)){
           $model = new SalesChannelsModel(1);
           $department_name = $model->getNameByid($department_id);
       }
       return $department_name;
	}


	/**
	 * 获取销售渠道公司名称
	 * @param string $department_id
	 * @return string
	 */
	public function get_company_name($department_id=false){
	   $company_name = '';
       if($department_id===false){
           $department_id = $this->_department_id;
       }
       if(!empty($department_id)){
           $model = new SalesChannelsModel(1);
           $company_data = $model->getCompanyByChannelid($department_id);
           if(!empty($company_data))
           	   $company_name = $company_data['company_name'];
       }
       return $company_name;
	}

	/**
	 * 获取销售渠道公司类型
	 * @param string $department_id
	 * @return string
	 */
	public function get_company_type($department_id=false){
	   $company_type = 3;
       if($department_id===false){
           $department_id = $this->_department_id;
       }
       if(!empty($department_id)){
           $model = new SalesChannelsModel(1);
           $company_data = $model->getCompanyByChannelid($department_id);
           if(!empty($company_data))
           	   $company_type = $company_data['company_type'];
       }
       return $company_type;
	}
	
	/**
	 * 获取销售渠道列表
	 * @param string $department_id
	 * @return string
	 */
	public function get_department_list(){
        //$model = new SalesChannelsModel(1);
        //$data = $model->getAllChannelInfo();
	    if(is_object($this->_model)){
	        $model = $this->_model;
	    }else{
	        $model = new MaterialBillJinModel(21);
	    }	
	    return $model->getAllChannelInfo();	     
	}
	
	/**
	 * 获取有效的仓库
	 * @return unknown
	 */
	public function get_warehouse_list()
	{
	    $model	= new WarehouseModel(21);
	    $warehouse  = $model->select2("id,name","is_delete=1 and type=13","all");
	    return $warehouse;
	}
	public function get_warehouse_name($warehouse_id=false)
	{
	    $warehouse = '';
	    if($warehouse_id===false){
	        $warehouse_id = $this->_warehouse_id;
	    }
	    if(!empty($warehouse_id)){
    	     $model	= new WarehouseModel(21);
	         $warehouse  = $model->select2("name","id={$warehouse_id}","one");
	    }
	    return $warehouse;
	}
	/**
	 * 获取供应商列表
	 * @return unknown
	 */
	public function get_supplier_list(){
	    $model = new ApiProModel();
	    $pro_list = $model->GetSupplierList(array('status'=>1));
	    return $pro_list;
	}
	
	public function get_supplier_name($supplier_id=false){
	    $supplier = '';
	    if($supplier_id===false){
	        $supplier_id = $this->_supplier_id;
	    }
	    if(!empty($supplier_id)){
    	    $model = new ApiProModel();
    	    $supplier = $model->getProName(array('id'=>$supplier_id));
    	    $supplier = isset($supplier['data'])?$supplier['data']:'';
	    }
	    return $supplier;
	}
	/**
	 * 获取单据类别，1是入库单 2是出库单    0未知
	 * @param string $bill_type
	 * @return number
	 */
	function get_bill_cat($bill_type=false){
	    if($bill_type===false){
	        $bill_type = $this->_bill_type;
	    }
	    if(is_object($this->_model)){
	        $model = $this->_model;
	    }else{
	        $model = new MaterialBillJinModel(21);
	    }
	    return $model->getBillCat($bill_type);
	}
	
}
?>