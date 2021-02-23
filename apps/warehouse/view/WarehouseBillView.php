<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-17 22:43:04
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillView extends View
{
	protected $_id;
	protected $_bill_no;
	protected $_bill_type;
	protected $_bill_status;
	protected $_order_sn;
	protected $_goods_num;
	protected $_put_in_type;
	protected $_jiejia;
	protected $_send_goods_sn;
	protected $_pro_id;
	protected $_pro_name;
	protected $_goods_total;
	protected $_goods_total_jiajia;
	protected $_shijia;
	protected $_pifajia;
	protected $_to_warehouse_id;
	protected $_to_warehouse_name;
	protected $_to_company_id;
	protected $_to_company_name;
	protected $_from_company_id;
	protected $_from_company_name;
	protected $_bill_note;
	protected $_yuanshichengben;
	protected $_check_user;
	protected $_check_time;
	protected $_create_user;
	protected $_create_time;
	protected $_fin_check_status;
	protected $_fin_check_user;
	protected $_fin_check_time;
	protected $_to_customer_id;
	protected $_tuihuoyuanyin;
	protected $_confirm_delivery;
	protected $_is_tsyd;
	protected $_production_manager_name;
	protected $_sign_user;
	protected $_sign_time;
    protected $_out_warehouse_type;
    protected $_label_price_total;
    protected $_p_type;
    protected $_company_from;
    protected $_is_invoice;



	public function get_id(){return $this->_id;}
	public function get_bill_no(){return $this->_bill_no;}
	public function get_bill_type(){return $this->_bill_type;}
	public function get_bill_status(){return $this->_bill_status;}
	public function get_order_sn(){return $this->_order_sn;}
	public function get_goods_num(){return $this->_goods_num;}
	public function get_put_in_type(){return $this->_put_in_type;}
	public function get_jiejia(){return $this->_jiejia;}
	public function get_send_goods_sn(){return $this->_send_goods_sn;}
	public function get_pro_id(){return $this->_pro_id;}
	public function get_pro_name(){return $this->_pro_name;}
	public function get_goods_total(){return $this->_goods_total;}
	public function get_goods_total_jiajia(){return $this->_goods_total_jiajia;}
	public function get_shijia(){return $this->_shijia;}
	public function get_pifajia(){return $this->_pifajia;}
	public function get_to_warehouse_id(){return $this->_to_warehouse_id;}
	public function get_to_warehouse_name(){return $this->_to_warehouse_name;}
	public function get_to_company_id(){return $this->_to_company_id;}
	public function get_to_company_name(){return $this->_to_company_name;}
	public function get_from_company_id(){return $this->_from_company_id;}
	public function get_from_company_name(){return $this->_from_company_name;}
	public function get_bill_note(){return $this->_bill_note;}
	public function get_yuanshichengben(){return $this->_yuanshichengben;}
	public function get_check_user(){return $this->_check_user;}
	public function get_check_time(){return $this->_check_time;}
	public function get_create_user(){return $this->_create_user;}
	public function get_create_time(){return $this->_create_time;}
	public function get_fin_check_status(){return $this->_fin_check_status;}
	public function get_fin_check_user(){return $this->_fin_check_user;}
	public function get_fin_check_time(){return $this->_fin_check_time;}
	public function get_to_customer_id(){return $this->_to_customer_id;}
	public function get_tuihuoyuanyin(){return $this->_tuihuoyuanyin;}
	public function get_chajia(){return (intval(($this->_xiaoshoujia*100))-intval(($this->_mingyijia*100)))/100;}


	public function get_chajia_pf(){return (intval(($this->_shijia*100))-intval(($this->_pifajia*100)))/100;}
	public function get_chajia_pf_th(){return (intval(($this->_pifajia*100))-intval(($this->_shijia*100)))/100;}

	public function get_chajia_show($xiaoshoujia,$mingyijia){ (intval(($xiaoshoujia*100))-intval(($mingyijia*100)))/100;}
	public function get_confirm_delivery(){return $this->_confirm_delivery;}
	public function get_is_tsyd(){return $this->_is_tsyd;}
	public function get_production_manager_name(){return $this->_production_manager_name;}
	public function get_sign_user(){return $this->_sign_user;}
	public function get_sign_time() {
		if (empty($this->_sign_time)) return '';
		
	    if (Util::endwith($this->_sign_time, '00:00:00')) {
	    	return substr($this->_sign_time, 0, strlen($this->_sign_time) - 8);
	    }
	    
	    return $this->_sign_time;
	}
    public function get_out_warehouse_type(){return $this->_out_warehouse_type;}
    public function get_label_price_total(){return $this->_label_price_total;}
    public function get_p_type(){return $this->_p_type;}
    public function get_company_from(){return $this->_company_from;}
    public function get_is_invoice(){return $this->_is_invoice;}

	/**
	* 根据单据ID bill_id 返回调拨单的 订单号+ 快递单号
	* $bill_id Int 单据ID
	* $type String order_sn=>获取订单号/ship_number=>获取快递单号
	*/
	public function getMinfo($bill_id, $type='ship_number'){
		$model = new WarehouseBillInfoMModel(21);
		$data = $model->getMinfo($bill_id);
		if($type === 'ship_number'){
			return $data['ship_number'];
		}
	}

	public function getCompany(){
		$model = $this->getModel();
		//var_dump($model->company());exit;
		return $model->company();
	}

	public function getSelfCompany(){
		$list =array();
		if(SYS_SCOPE=='boss'){
		    $model = $this->getModel();
		    $list = $model->company();
		}else{
			if($_SESSION['companyId']==58){
			    $model = $this->getModel();
			    $list = $model->company();
			}else{
			    $view_company_model = new CompanyModel(1);
                $list = $view_company_model->select2("*","(id in (58,'{$_SESSION['companyId']}') or sd_company_id='{$_SESSION['companyId']}') and is_deleted='0'",1);
            }
        }     
		//var_dump($model->company());exit;
		return $list;
	}

	public function getWarehouse(){
		$model = $this->getModel();
		return $model->warehouse();
	}

	public function getBillType($type_sn = false){
		$model = $this->getModel();
		return $model->getBillType($type_sn);
	}

	//获取加工商列表
	public function getSupliers()
	{
		$supplier = new ApiProModel();
		$suppliers = $supplier->GetSupplierList(array('status'=>1));//调用加工商接口
		return $suppliers;
	}
	
	public function is_p_bill_sign_enabled() {
	    return strtotime($this->get_create_time()) >= strtotime('2017-1-1');
	}
        //验证是否可以查看采购价
    public function checkBillHCaiGouJia($id)
    {
        $WarehouseBillModel = new WarehouseBillModel($id,21);
        $company_model = new CompanyModel(1);
        $is_company = Auth::user_is_from_base_company();
        $do = $WarehouseBillModel->getDataObject();
        $is_show_caigoujia = true;//是否可以查看采购价
        $to_is_shengdai = false;
        $from_is_shengdai = false;
        $to_company_id   = $do['to_company_id'];
        $from_company_id = $do['from_company_id'];
        $companyId = $_SESSION['companyId'];//当前所在公司
        //经销商，个体店，直营店隐藏列表的采购价，单头的成本总计；
        $is_shengdai = $company_model->select2(' count(*) ' , " is_deleted = 0 and id = '{$companyId}' and is_shengdai = 1 " , $type = '3');
        //如果是总部批发给省代的单据，省代查看时隐藏采购价（不受权限管控，就是不显示）
        //入库公司是否总公司
        if(in_array($to_company_id,array('58','445', '515'))){
            $to_is_shengdai = true;
        }
        //出库公司是否省代
        if($from_company_id){
            $res = $company_model->select2(' `is_shengdai` ' , " is_deleted = 0 and id = '{$from_company_id}'" , $type = '3');
            if($res == '1') $from_is_shengdai = true;
        }
        if($from_is_shengdai == true && $to_is_shengdai == true && $is_shengdai != false) $is_show_caigoujia = false;
        //非总公司、非省代的  采购成本和名义成本不能看且不受权限管控，批发价受权限管控；
        if(!$is_company && $is_shengdai == false) $is_show_caigoujia = false;
        return $is_show_caigoujia;
    }
}
?>