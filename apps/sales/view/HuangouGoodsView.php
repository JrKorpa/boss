<?php
/**
 *  -------------------------------------------------
 *   @file		: HuangouGoodsView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2018-01-05 11:06:19
 *   @update	:
 *  -------------------------------------------------
 */
class HuangouGoodsView extends View
{
	protected $_id;
	protected $_channel_id;
	protected $_style_sn;
	protected $_label_price;
	protected $_sale_price;
	protected $_create_user;
	protected $_create_time;
	protected $_update_user;
	protected $_update_time;


	public function get_id(){return $this->_id;}
	public function get_channel_id(){return $this->_channel_id;}
	public function get_style_sn(){return $this->_style_sn;}
	public function get_label_price(){return $this->_label_price;}
	public function get_sale_price(){return $this->_sale_price;}
	public function get_create_user(){return $this->_create_user;}
	public function get_create_time(){return $this->_create_time;}
	public function get_update_user(){return $this->_update_user;}
	public function get_update_time(){return $this->_update_time;}
    public function getSalesChannels(){
        $model = new SalesChannelsModel(1);
        return $model->getSalesChannelsInfo("id,channel_name",null);        
    }

}
?>