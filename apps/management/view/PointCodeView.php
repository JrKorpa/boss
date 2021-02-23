<?php
/**
 * Created by PhpStorm.
 * User: liaoweixian
 * Date: 2018/6/11
 * Time: 17:03
 */

class PointCodeView extends View
{
    protected $_id;
    protected $_channel_id;
    protected $_channel_name;
    protected $_point_code;
    protected $_use_proportion;
    protected $_status;
    protected $_order_sn;
    protected $_use_people_name;
    protected $_use_people_id;
    protected $_created_id;
    protected $_created_name;
    protected $_create_time;
    protected $_update_time;

    public function get_id(){return $this->_id;}
    public function get_channel_id(){return $this->_channel_id;}
    public function get_channel_name(){return $this->_channel_name;}
    public function get_point_code(){return $this->_point_code;}
    public function get_use_proportion(){return $this->_use_proportion;}
    public function get_status(){return $this->_status;}
    public function get_order_sn(){return $this->_order_sn;}
    public function get_use_people_name(){return $this->_use_people_name;}
    public function get_use_people_id(){return $this->_use_people_id;}
    public function get_created_id(){return $this->_created_id;}
    public function get_created_name(){return $this->_created_name;}
    public function get_create_time(){return $this->_create_time;}
    public function get_update_time(){return $this->_update_time;}
}