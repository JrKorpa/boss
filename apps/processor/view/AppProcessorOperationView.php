<?php

/**
 *  -------------------------------------------------
 *   @file		: AppProcessorOperationView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-15 17:12:26
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorOperationView extends View {

    protected $_id;
    protected $_processor_id;
    protected $_name;
    protected $_operation_type;
    protected $_operation_content;
    protected $_create_time;
    protected $_create_user;
    protected $_create_user_id;

    public function get_id() {return $this->_id;}
    public function get_processor_id() {return $this->_processor_id;}
    public function get_name() {return $this->_name;}
    public function get_operation_type() {return $this->_operation_type;}
    public function get_operation_content() {return $this->_operation_content;}
    public function get_create_time() {return $this->_create_time;}
    public function get_create_user() {return $this->_create_user;}
    public function get_create_user_id() {return $this->_create_user_id;}

    public function getTypeList() {
        $model = new AppProcessorOperationModel(13);
        return $model->getTypeList();
    }

}

?>