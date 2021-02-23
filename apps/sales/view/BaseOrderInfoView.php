<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseOrderInfoView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-28 12:36:56
 *   @update	:
 *  -------------------------------------------------
 */
class BaseOrderInfoView extends View
{
	protected $_id;
	protected $_order_sn;
	protected $_user_id;
	protected $_consignee;
	protected $_mobile;
	protected $_order_status;
	protected $_order_pay_status;
	protected $_order_pay_type;
	protected $_delivery_status;
	protected $_send_good_status;
	protected $_buchan_status;
	protected $_customer_source_id;
	protected $_department_id;
	protected $_create_time;
	protected $_create_user;
	protected $_check_time;
	protected $_check_user;
	protected $_recommended;
	protected $_recommender_sn;
    protected $_genzong;
	protected $_modify_time;
	protected $_order_remark;
	protected $_is_delete;
	protected $_apply_close;
	protected $_is_xianhuo;
	protected $_is_print_tihuo;
	protected $_is_zp;
	protected $_effect_date;
	protected $_apply_return;
	protected $_referer;
	protected $_is_default;
	protected $_is_favourable;
	protected $_weixiu_status;
	protected $_shipfreight_time;
	protected $_is_real_invoice;
    protected $_hidden;


	public function get_id(){return $this->_id;}
	public function get_order_sn(){return $this->_order_sn;}
	public function get_user_id(){return $this->_user_id;}
	public function get_consignee(){return $this->_consignee;}
	public function get_mobile(){return $this->_mobile;}
	public function get_order_status(){return $this->_order_status;}
	public function get_order_pay_status(){return $this->_order_pay_status;}
	public function get_order_pay_type(){return $this->_order_pay_type;}
	public function get_delivery_status(){return $this->_delivery_status;}
	public function get_send_good_status(){return $this->_send_good_status;}
	public function get_buchan_status(){return $this->_buchan_status;}
	public function get_customer_source_id(){return $this->_customer_source_id;}
	public function get_department_id(){return $this->_department_id;}
	public function get_create_time(){return $this->_create_time;}
	public function get_create_user(){return $this->_create_user;}
    public function get_genzong(){return $this->_genzong;}
	public function get_check_time(){return $this->_check_time;}
	public function get_check_user(){return $this->_check_user;}
	public function get_recommended(){return $this->_recommended;}
	public function get_recommender_sn(){return $this->_recommender_sn;}
	public function get_modify_time(){return $this->_modify_time;}
	public function get_order_remark(){return $this->_order_remark;}
	public function get_is_delete(){return $this->_is_delete;}
	public function get_apply_close(){return $this->_apply_close;}
	public function get_is_xianhuo(){return $this->_is_xianhuo;}
	public function get_is_print_tihuo(){return $this->_is_print_tihuo;}
	public function get_is_zp(){return $this->_is_zp;}
	public function get_effect_date(){return $this->_effect_date;}
	public function get_apply_return(){return $this->_apply_return;}
	public function get_referer(){return $this->_referer;}
    public function get_is_favourable(){return $this->_is_favourable;}
    public function get_is_default(){return $this->_is_default;}
    public function get_weixiu_status(){return $this->_weixiu_status;}
    public function get_shipfreight_time(){return $this->_shipfreight_time;}
    public function get_is_real_invoice(){return $this->_is_real_invoice;}

    
    
    public function getUserName($user_id) {
        $user_name = $this->getModel()->getMember_Info_userId($user_id);
        return $user_name['data']['member_name'];
    }
    
    public function getUserMobile($user_id) {
        $user_name = $this->getModel()->getMember_Info_userId($user_id);
        return $user_name['data']['member_phone'];
    }

    public function getOutSn($order_id){
        $outsn = $this->getModel()->getOurOrderSn($order_id);
        if(!empty($outsn)){
            $outsn =  implode(',',array_column($outsn,'out_order_sn'));
        }else{
            $outsn='';
        }
        return $outsn;
    }


    public function getOutSns($order_id){
        $outsn = $this->getModel()->getOurOrderSn($order_id);
        if(!empty($outsn)){
            $outsn =  array_column($outsn,'out_order_sn');
        }else{
            $outsn=array();
        }
        return $outsn;
    }
    
    public function isHaveGoods($order_id){
        $detailsModel = new AppOrderDetailsModel(27);
        return $detailsModel->getGoodsById($order_id);
    }

    public function conczhuangtai(){
        if(!empty($this->_apply_close)&&!empty($this->_apply_return)){
            $f='/';
        }else{
            $f='';
        }
        if($this->_apply_close==1){
            $close='申请关闭';
        }else{
            $close='';
        }
        if($this->_apply_return==2){
            $returns='退款流程';
        }else{
            $returns='';
        }
       return  $close.$f.$returns;
    }

    public function zhantingHidden(){
        if(SYS_SCOPE == 'zhanting' 
            && $this->_hidden == '1'){
            return false;
        }
        return true;
    }
}
?>