<?php
/**
 *  -------------------------------------------------
 *   @file		: AppPriceByStyleView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @update	:
 *  -------------------------------------------------
 */
class AppPriceByStyleView extends View {

    protected $_id;
    protected $_style_id;
    protected $_caizhi;
    protected $_stone_position;
    protected $_stone_cat;
    protected $_tuo_type;
    protected $_zuan_min;
    protected $_zuan_max;
    protected $_zuan_yanse_min;
    protected $_zuan_yanse_max;
    protected $_zuan_jindu_min;
    protected $_zuan_jindu_max;
    protected $_cert;
    protected $_zuan_shape;
    protected $_price;
    protected $_is_delete;

    public function get_id() {
        return $this->_id;
    }

    public function get_style_id() {
        return $this->_style_id;
    }

    public function get_caizhi() {
        return $this->_caizhi;
    }

    public function get_stone_position() {
        return $this->_stone_position;
    }

    public function get_stone_cat() {
        return $this->_stone_cat;
    }

    public function get_tuo_type() {
        return $this->_tuo_type;
    }

    public function get_zuan_min() {
        return $this->_zuan_min;
    }

    public function get_zuan_max() {
        return $this->_zuan_max;
    }

    public function get_zuan_yanse_min() {
        return $this->_zuan_yanse_min;
    }

    public function get_zuan_yanse_max() {
        return $this->_zuan_yanse_max;
    }

    public function get_zuan_jindu_min() {
        return $this->_zuan_jindu_min;
    }

    public function get_zuan_jindu_max() {
        return $this->_zuan_jindu_max;
    }

    public function get_cert() {
        return $this->_cert;
    }

    public function get_zuan_shape() {
        return $this->_zuan_shape;
    }

    public function get_price() {
        return $this->_price;
    }

    public function get_is_delete() {
        return $this->_is_delete;
    }
}
?>