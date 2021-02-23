<?php

/**
 *  -------------------------------------------------
 *   @file		: RelStyleFactoryView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 10:34:21
 *   @update	:
 *  -------------------------------------------------
 */
class RelStyleFactoryView extends View {

    protected $_f_id;
    protected $_style_id;
    protected $_style_sn;
    protected $_factory_id;
    protected $_factory_sn;
    protected $_factory_fee;
    protected $_xiangkou;
    protected $_is_def;
    protected $_is_factory;
    protected $_is_cancel;

    public function get_f_id() {
        return $this->_f_id;
    }

    public function get_style_id() {
        return $this->_style_id;
    }

    public function get_style_sn() {
        return $this->_style_sn;
    }

    public function get_factory_id() {
        return $this->_factory_id;
    }

    public function get_factory_sn() {
        return $this->_factory_sn;
    }

    public function get_factory_fee() {
        return $this->_factory_fee;
    }

    public function get_xiangkou() {
        return $this->_xiangkou;
    }

    public function get_is_def() {
        return $this->_is_def;
    }

    public function get_is_factory() {
        return $this->_is_factory;
    }

    public function get_is_cancel() {
        return $this->_is_cancel;
    }

    public function getProcessorList() {
        $factoryModel = new RelStyleFactoryModel();
        return $factoryModel->getProcessorList();
    }

}

?>