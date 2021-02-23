<?php
/**
 * 成品定制码
 *  -------------------------------------------------
 *   @file		: BaseCpdzCodeView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: gaopeng
 *   @date		: 2017-07-19 13:40:44
 *   @update	:
 *  -------------------------------------------------
 */
class BaseCpdzCodeView extends View {

    protected $_id;
    protected $_code;
    protected $_price;
    protected $_order_detail_id;
    protected $_use_status;
    protected $_style_channel_id;
    protected $_style_channel;    
    protected $_create_user;
    protected $_create_time;
       

    public function get_id() {
        return $this->_id;
    }
    
    public function get_code() {
        return $this->_code;
    }
    
    public function get_price() {
        return $this->_price;
    }
    
    public function get_use_status() {
        return $this->_use_status;
    }
    
    public function get_order_detail_id() {
        return $this->_order_detail_id;
    }
        
    public function get_style_channel_id() {
        return $this->_style_channel_id;
    }
    public function get_style_channel() {
        return $this->_style_channel;
    }
    
    public function get_create_user() {
        return $this->_create_user;
    }
    public function get_create_time() {
        return $this->_create_time;
    }
    
    public function getSalesChannels(){
        $model = new SalesChannelsModel(1);
        return $model->getSalesChannelsInfo("id,channel_name",null);        
    }

}

?>