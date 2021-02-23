<?php
/**
 *  -------------------------------------------------
 *   @file		: PayOrderInfoView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-26 15:33:31
 *   @update	:
 *  -------------------------------------------------
 */
class PayOrderInfoView extends View
{
	protected $_order_id;
	protected $_kela_sn;
	protected $_external_sn;
	protected $_make_order;
	protected $_order_time;
	protected $_shipping_time;
	protected $_pay_id;
	protected $_pay_name;
	protected $_department;
	protected $_from_ad;
	protected $_status;
	protected $_apply_number;
	protected $_addtime;
	protected $_kela_total_all;
	protected $_jxc_total_all;
	protected $_external_total_all;


	public function get_order_id(){return $this->_order_id;}
	public function get_kela_sn(){return $this->_kela_sn;}
	public function get_external_sn(){return $this->_external_sn;}
	public function get_make_order(){return $this->_make_order;}
	public function get_order_time(){return $this->_order_time;}
	public function get_shipping_time(){return $this->_shipping_time;}
	public function get_pay_id(){return $this->_pay_id;}
	public function get_pay_name(){return $this->_pay_name;}
	public function get_department(){return $this->_department;}
	public function get_from_ad(){return $this->_from_ad;}
	public function get_status(){return $this->_status;}
	public function get_apply_number(){return $this->_apply_number;}
	public function get_addtime(){return $this->_addtime;}
	public function get_kela_total_all(){return $this->_kela_total_all;}
	public function get_jxc_total_all(){return $this->_jxc_total_all;}
	public function get_external_total_all(){return $this->_external_total_all;}
    
    public function getStatusList() {
        $model = new PayOrderInfoModel(29);
        return $model->getStatusList();
    }

}
?>