<?php

/**
 *  -------------------------------------------------
 *   @file		: AppReceiveApplyView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-26 18:44:32
 *   @update	:
 *  -------------------------------------------------
 */
class AppReceiveApplyView extends View {

    protected $_id;
    protected $_apply_number;
    protected $_status;
    protected $_should_number;
    protected $_from_ad;
    protected $_cash_type;
    protected $_make_time;
    protected $_make_name;
    protected $_check_time;
    protected $_check_name;
    protected $_amount;
    protected $_total;
    protected $_external_total_all;
    protected $_kela_total_all;
    protected $_jxc_total_all;
    protected $_check_sale_number;

    public function get_id() {
        return $this->_id;
    }

    public function get_apply_number() {
        return $this->_apply_number;
    }

    public function get_status() {
        return $this->_status;
    }

    public function get_should_number() {
        return $this->_should_number;
    }

    public function get_from_ad() {
        return $this->_from_ad;
    }

    public function get_cash_type() {
		return $this->_cash_type;
    }

    public function get_make_time() {
        return $this->_make_time;
    }

    public function get_make_name() {
        return $this->_make_name;
    }

    public function get_check_time() {
        return $this->_check_time;
    }

    public function get_check_name() {
        return $this->_check_name;
    }

    public function get_amount() {
        return $this->_amount;
    }

    public function get_total() {
        return $this->_total;
    }

    public function get_external_total_all() {
        return $this->_external_total_all;
    }

    public function get_kela_total_all() {
        return $this->_kela_total_all;
    }

    public function get_jxc_total_all() {
        return $this->_jxc_total_all;
    }

    public function get_check_sale_number() {
        return $this->_check_sale_number;
    }

    public function getFromAdList($param = 0) {
        $modle = new EcsAdModel(29);
        return $modle->getAdList();
    }

    public function getStatusList($param = 0) {
        $modle = new AppReceiveApplyModel(29);
        return $modle->getStatusList($param);
    }


    public function get_from_name() {
        $model = new CustomerSourcesModel(1);
        $return = $model->getSourceNameById($this->_from_ad);
        return $return;
    }

}

?>