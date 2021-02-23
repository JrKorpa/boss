<?php
/**
 *  -------------------------------------------------
 *   @file		: ControlView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @date		:2017-5-26
 *   @update	:
 *  -------------------------------------------------
 */
class ProductCreationCodeView extends View{
    protected $_id;
    protected $_department_id;
    protected $_create_user;
    protected $_order_sn;
    protected $_department_id_from;
    protected $_sale_user;
    protected $_status;
    protected $_style_sn;
    protected $_transaction_price;
    protected $_created_time;
    protected $_udpated_time;
    /**
     * @return the $_id
     */
    public function getId()
    {
        return $this->_id;
    }
    public function get_id(){
        return $this->_id;
    }
    /**
     * @return the $_department_id
     */
    public function getDepartment_id()
    {
        return $this->_department_id;
    }

    /**
     * @return the $_create_user
     */
    public function getCreate_user()
    {
        return $this->_create_user;
    }

    /**
     * @return the $_order_sn
     */
    public function getOrder_sn()
    {
        return $this->_order_sn;
    }

    /**
     * @return the $_department_id_from
     */
    public function getDepartment_id_from()
    {
        return $this->_department_id_from;
    }

    /**
     * @return the $_sale_user
     */
    public function getSale_user()
    {
        return $this->_sale_user;
    }

    /**
     * @return the $_status
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * @return the $_style_sn
     */
    public function getStyle_sn()
    {
        return $this->_style_sn;
    }

    /**
     * @return the $_transaction_price
     */
    public function getTransaction_price()
    {
        return $this->_transaction_price;
    }

    /**
     * @return the $_created_time
     */
    public function getCreated_time()
    {
        return $this->_created_time;
    }

    /**
     * @return the $_udpated_time
     */
    public function getUdpated_time()
    {
        return $this->_udpated_time;
    }

    /**
     * @param field_type $_id
     */
    public function setId($_id)
    {
        $this->_id = $_id;
    }

    /**
     * @param field_type $_department_id
     */
    public function setDepartment_id($_department_id)
    {
        $this->_department_id = $_department_id;
    }

    /**
     * @param field_type $_create_user
     */
    public function setCreate_user($_create_user)
    {
        $this->_create_user = $_create_user;
    }

    /**
     * @param field_type $_order_sn
     */
    public function setOrder_sn($_order_sn)
    {
        $this->_order_sn = $_order_sn;
    }

    /**
     * @param field_type $_department_id_from
     */
    public function setDepartment_id_from($_department_id_from)
    {
        $this->_department_id_from = $_department_id_from;
    }

    /**
     * @param field_type $_sale_user
     */
    public function setSale_user($_sale_user)
    {
        $this->_sale_user = $_sale_user;
    }

    /**
     * @param field_type $_status
     */
    public function setStatus($_status)
    {
        $this->_status = $_status;
    }

    /**
     * @param field_type $_style_sn
     */
    public function setStyle_sn($_style_sn)
    {
        $this->_style_sn = $_style_sn;
    }

    /**
     * @param field_type $_transaction_price
     */
    public function setTransaction_price($_transaction_price)
    {
        $this->_transaction_price = $_transaction_price;
    }

    /**
     * @param field_type $_created_time
     */
    public function setCreated_time($_created_time)
    {
        $this->_created_time = $_created_time;
    }

    /**
     * @param field_type $_udpated_time
     */
    public function setUdpated_time($_udpated_time)
    {
        $this->_udpated_time = $_udpated_time;
    }

    
}