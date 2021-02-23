<?php

/**
 *  -------------------------------------------------
 *   @file		: PayHexiaoView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-26 16:39:43
 *   @update	:
 *  -------------------------------------------------
 */
class PayHexiaoView extends View {

    protected $_id;
    protected $_check_sale_number;
    protected $_status;
    protected $_from_ad;
    protected $_order_num;
    protected $_goods_num;
    protected $_chengben;
    protected $_shijia;
    protected $_maketime;
    protected $_makename;
    protected $_checktime;
    protected $_checkname;
    protected $_apply_number;
    protected $_cash_type;

    public function get_id() {
        return $this->_id;
    }

    public function get_check_sale_number() {
        return $this->_check_sale_number;
    }

    public function get_status() {
        return $this->_status;
    }

    public function get_from_ad() {
        return $this->_from_ad;
    }

    public function get_order_num() {
        return $this->_order_num;
    }

    public function get_goods_num() {
        return $this->_goods_num;
    }

    public function get_chengben() {
        return $this->_chengben;
    }

    public function get_shijia() {
        return $this->_shijia;
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

    public function get_apply_number() {
        return $this->_apply_number;
    }

    public function get_cash_type() {
        return $this->_cash_type;
    }

    public function get_from_name() {
        $model = new CustomerSourcesModel(1);
        $return = $model->getSourceNameById($this->_from_ad);
        return $return;
    }

}

?>