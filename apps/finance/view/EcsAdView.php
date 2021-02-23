<?php

/**
 *  -------------------------------------------------
 *   @file		: EcsAdView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-27 10:37:56
 *   @update	:
 *  -------------------------------------------------
 */
class EcsAdView extends View {

    protected $_ad_id;
    protected $_ad_sn;
    protected $_ad_department;
    protected $_ad_channel;
    protected $_area;
    protected $_media_name;
    protected $_media_passwd;
    protected $_position_id;
    protected $_media_type;
    protected $_ad_domain;
    protected $_ad_name;
    protected $_ad_link;
    protected $_ad_code;
    protected $_start_date;
    protected $_end_date;
    protected $_link_man;
    protected $_link_email;
    protected $_link_phone;
    protected $_click_count;
    protected $_enabled;
    protected $_fenlei;
    protected $_reset;
    protected $_add_time;
    protected $_last_update_time;
    protected $_ad_cat;

    public function get_ad_id() {
        return $this->_ad_id;
    }

    public function get_ad_sn() {
        return $this->_ad_sn;
    }

    public function get_ad_department() {
        return $this->_ad_department;
    }

    public function get_ad_channel() {
        return $this->_ad_channel;
    }

    public function get_area() {
        return $this->_area;
    }

    public function get_media_name() {
        return $this->_media_name;
    }

    public function get_media_passwd() {
        return $this->_media_passwd;
    }

    public function get_position_id() {
        return $this->_position_id;
    }

    public function get_media_type() {
        return $this->_media_type;
    }

    public function get_ad_domain() {
        return $this->_ad_domain;
    }

    public function get_ad_name() {
        return $this->_ad_name;
    }

    public function get_ad_link() {
        return $this->_ad_link;
    }

    public function get_ad_code() {
        return $this->_ad_code;
    }

    public function get_start_date() {
        return $this->_start_date;
    }

    public function get_end_date() {
        return $this->_end_date;
    }

    public function get_link_man() {
        return $this->_link_man;
    }

    public function get_link_email() {
        return $this->_link_email;
    }

    public function get_link_phone() {
        return $this->_link_phone;
    }

    public function get_click_count() {
        return $this->_click_count;
    }

    public function get_enabled() {
        return $this->_enabled;
    }

    public function get_fenlei() {
        return $this->_fenlei;
    }

    public function get_reset() {
        return $this->_reset;
    }

    public function get_add_time() {
        return $this->_add_time;
    }

    public function get_last_update_time() {
        return $this->_last_update_time;
    }

    public function get_ad_cat() {
        return $this->_ad_cat;
    }

    public function adList() {
        $model = new EcsAdModel(29);
        return $model->getAdList();
    }

    public function companyList() {
        $model = new EcsAdModel(29);
        return $model->getCompanyList();
    }
    
    public function yearList() {
        $model = new EcsAdModel(29);
        $jizhang_list = $model->getJiezhangList();
        $new_year = array();
		foreach($jizhang_list as $k=>$v)
		{
			$new_year[$k] = $v['year'];
		}
        return $new_year;
    }

}

?>