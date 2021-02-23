<?php
/**
 *  -------------------------------------------------
 *   @file		: AppBespokeInfoView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 16:18:25
 *   @update	:
 *  -------------------------------------------------
 */
class AppBespokeInfoView extends View
{
	protected $_bespoke_id;
	protected $_bespoke_sn;
	protected $_department_id;
	protected $_mem_id;
	protected $_customer_source_id;
	protected $_customer;
	protected $_customer_mobile;
	protected $_customer_email;
	protected $_customer_address;
	protected $_create_time;
	protected $_bespoke_inshop_time;
	protected $_real_inshop_time;
	protected $_make_order;
	protected $_accecipt_man;
	protected $_bespoke_status;
	protected $_queue_status;
	protected $_salesstage;
	protected $_brandimage;
	protected $_re_status;
	protected $_remark;
    protected $_is_delete;


	public function get_bespoke_id(){return $this->_bespoke_id;}
	public function get_bespoke_sn(){return $this->_bespoke_sn;}
	public function get_department_id(){return $this->_department_id;}
	public function get_mem_id(){return $this->_mem_id;}
	public function get_customer_source_id(){return $this->_customer_source_id;}
	public function get_customer(){return $this->_customer;}
	public function get_customer_mobile(){return $this->_customer_mobile;}
	public function get_customer_email(){return $this->_customer_email;}
	public function get_customer_address(){return $this->_customer_address;}
	public function get_create_time(){return $this->_create_time;}
	public function get_bespoke_inshop_time(){return $this->_bespoke_inshop_time;}
	public function get_real_inshop_time(){return $this->_real_inshop_time;}
	public function get_make_order(){return $this->_make_order;}
	public function get_accecipt_man(){return $this->_accecipt_man;}
	public function get_bespoke_status(){return $this->_bespoke_status;}
	public function get_queue_status(){return $this->_queue_status;}
	public function get_salesstage(){return $this->_salesstage;}
	public function get_brandimage(){return $this->_brandimage;}
	public function get_re_status(){return $this->_re_status;}
	public function get_remark(){return $this->_remark;}
	public function get_is_delete(){return $this->_is_delete;}
	
	public function get_resolved_customer_mobile() {
		if ($_SESSION['userName'] == $this->_accecipt_man || strlen($this->_customer_mobile) < 8) return $this->_customer_mobile;
		return substr($this->_customer_mobile, 0, 3).'****'.substr($this->_customer_mobile, 7);
	}

    public function getSouceName(){
        $smodel = new CustomerSourcesModel(1);
      return   $smodel->getCustomerSourceNameById($this->_customer_source_id);
    }



}
?>