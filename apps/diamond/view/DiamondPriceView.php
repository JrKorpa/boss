<?php

/**
 *  -------------------------------------------------
 *   @file		: DiamondPriceView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 11:31:14
 *   @update	:
 *  -------------------------------------------------
 */
class DiamondPriceView extends View {

    protected $_id;
    protected $_shape;
    protected $_clarity;
    protected $_color;
    protected $_min;
    protected $_max;
    protected $_price;
    protected $_addtime;
    protected $_version;

    public function get_id() {
        return $this->_id;
    }

    public function get_shape() {
        return $this->_shape;
    }

    public function get_clarity() {
        return $this->_clarity;
    }

    public function get_color() {
        return $this->_color;
    }

    public function get_min() {
        return $this->_min;
    }

    public function get_max() {
        return $this->_max;
    }

    public function get_price() {
        return $this->_price;
    }
    public function get_version() {
        return $this->_version;
    }

    public function get_addtime() {
        return $this->_addtime;
    }

    public function getClarityList() {
        $model = new DiamondPriceModel(19);
        $data = $model->getClarityList();
        return $data;
    }
    
    public function getColorList() {
        $model = new DiamondPriceModel(19);
        $data = $model->getColorList();
        return $data;
    }
    
    public function getVersionList() {
        $model = new DiamondPriceModel(19);
        return $model->getVersionList();
    }
    
    public function getLastId() {
        $model = new DiamondPriceModel(19);
        $row = $model->getLastId();
        return $row['version'];
    }
    
}

?>