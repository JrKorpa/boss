<?php
/**
 *  -------------------------------------------------
 *   @file		: AppReturnGoodsView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-13 14:27:42
 *   @update	:
 *  -------------------------------------------------
 */
class AppReturnGoodsView extends View
{
	protected $_return_id;
	protected $_department;
	protected $_apply_user_id;
	protected $_order_id;
	protected $_order_sn;
	protected $_order_goods_id;
	protected $_should_return_amount;
	protected $_apply_return_amount;
	protected $_real_return_amount;
	protected $_confirm_price;
	protected $_return_res;
	protected $_return_type;
	protected $_return_card;
	protected $_consignee;
	protected $_mobile;
	protected $_bank_name;
	protected $_apply_time;
	protected $_pay_id;
	protected $_pay_res;
	protected $_pay_status;
	protected $_pay_attach;
	protected $_pay_order_sn;
	protected $_jxc_order;
	protected $_zhuandan_amount;
	protected $_check_status;


	public function get_return_id(){return $this->_return_id;}
	public function get_department(){return $this->_department;}
	public function get_apply_user_id(){return $this->_apply_user_id;}
	public function get_order_id(){return $this->_order_id;}
	public function get_order_sn(){return $this->_order_sn;}
	public function get_order_goods_id(){return $this->_order_goods_id;}
	public function get_should_return_amount(){return $this->_should_return_amount;}
	public function get_apply_return_amount(){return $this->_apply_return_amount;}
	public function get_real_return_amount(){return $this->_real_return_amount;}
	public function get_confirm_price(){return $this->_confirm_price;}
	public function get_return_res(){return $this->_return_res;}
	public function get_return_type(){return $this->_return_type;}
	public function get_return_card(){return $this->_return_card;}
	public function get_consignee(){return $this->_consignee;}
	public function get_mobile(){return $this->_mobile;}
	public function get_bank_name(){return $this->_bank_name;}
	public function get_apply_time(){return $this->_apply_time;}
	public function get_pay_id(){return $this->_pay_id;}
	public function get_pay_res(){return $this->_pay_res;}
	public function get_pay_status(){return $this->_pay_status;}
	public function get_pay_attach(){return $this->_pay_attach;}
	public function get_pay_order_sn(){return $this->_pay_order_sn;}
	public function get_jxc_order(){return $this->_jxc_order;}
	public function get_zhuandan_amount(){return $this->_zhuandan_amount;}
	public function get_check_status(){return $this->_check_status;}
    
    
    public function getDepartmentList($param=0) {
        $SalesChannelsModel = new SalesChannelsModel(1);
        if($_SESSION['userType'] == 1){
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
        }else{
            $ids = explode(',', $_SESSION['qudao']);
            $channellist = $SalesChannelsModel->getSalesChannel($ids);
        }
        return $channellist;
    }

}
?>