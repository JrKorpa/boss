<?php

/**
 *  -------------------------------------------------
 *   @file		: AppReceiveShouldView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-02 09:41:38
 *   @update	:
 *  -------------------------------------------------
 */
class AppReceiveShouldView extends View {

    protected $_should_id;
    protected $_should_number;
    protected $_status;
    protected $_total_status;
    protected $_from_ad;
    protected $_total_cope;
    protected $_total_real;
    protected $_maketime;
    protected $_makename;
    protected $_checktime;
    protected $_checkname;

    public function get_should_id() {
        return $this->_should_id;
    }

    public function get_should_number() {
        return $this->_should_number;
    }

    public function get_status() {
        return $this->_status;
    }

    public function get_total_status() {
        return $this->_total_status;
    }

    public function get_from_ad() {
        return $this->_from_ad;
    }

    public function get_total_cope() {
        return $this->_total_cope;
    }

    public function get_total_real() {
        return $this->_total_real;
    }

    public function get_maketime() {
        return $this->_maketime;
    }

    public function get_makename() {
        return $this->_makename;
    }

    public function get_checktime() {
        return $this->_checktime;
    }

    public function get_checkname() {
        return $this->_checkname;
    }

    public function getMaxDate() {
        return date("Y-m-d");
    }

}

?>