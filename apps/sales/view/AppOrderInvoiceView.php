<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderInvoiceView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-30 14:45:45
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderInvoiceView extends View
{
	protected $_id;
	protected $_order_id;
	protected $_is_invoice;
	protected $_invoice_title;
	protected $_invoice_content;
	protected $_invoice_amount;
	protected $_invoice_address;
	protected $_invoice_num;
	protected $_create_time;
	protected $_use_user;
	protected $_use_time;
	protected $_invoice_status;
	protected $_invoice_type;
	protected $_taxpayer_sn;
	protected $_open_sn;
	protected $_title_type;
	protected $_invoice_email;
	
	public function get_id(){return $this->_id;}
	public function get_order_id(){return $this->_order_id;}
	public function get_is_invoice(){return $this->_is_invoice?1:0;}
	public function get_invoice_title(){return $this->_invoice_title;}
	public function get_invoice_content(){return $this->_invoice_content;}
	public function get_invoice_amount(){return $this->_invoice_amount;}
	public function get_invoice_address(){return $this->_invoice_address;}
	public function get_invoice_num(){return $this->_invoice_num;}
	public function get_create_time(){return $this->_create_time;}
	public function get_use_user(){return $this->_use_user;}
	public function get_use_time(){return $this->_use_time;}
	public function get_invoice_status(){return $this->_invoice_status;}
	public function get_invoice_type(){
	    return $this->_invoice_type;
	}
	public function get_taxpayer_sn(){
	    return $this->_taxpayer_sn;
	}
    public function set_invoice_address($address){
        if(!empty($address)){
           $this->_invoice_address=$address;
        }
        
    }
    public function set_invoice_email($email){
        if(!empty($email)){
           $this->_invoice_email = $email;
        }
    }
    /**
     * 发票状态列表
     * @return multitype:string
     */
    public function get_invoice_status_list(){
        return array(1=>"未开发票",2=>"已开发票",3=>"发票作废");
    }
    
    public function get_title_type(){
        return $this->_title_type;
    }
    public function get_invoice_email(){
        return $this->_invoice_email;
    }


}
?>