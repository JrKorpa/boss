<?php
/**
 *  -------------------------------------------------
 *   @file		: ExtraDiscountScopeView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-09-29 11:22:42
 *   @update	:
 *  -------------------------------------------------
 */
class ExtraDiscountScopeView extends View
{
	protected $_id;
	protected $_dep_id;
	protected $_dep_name;
	protected $_style_sn_id;
	protected $_style_channel_id;
    protected $_style_channel_name;
	protected $_goods_type;
	protected $_discount_upper;
	protected $_discount_floor;
	protected $_push_money;
	protected $_priority;


	public function get_id(){return $this->_id;}
	public function get_dep_id(){return $this->_dep_id;}
	public function get_dep_name(){return $this->_dep_name;}
	public function get_style_sn_id(){return $this->_style_sn_id;}
    public function get_style_channel_id(){return $this->_style_channel_id;}
	public function get_style_channel_name(){return $this->_style_channel_name;}
	public function get_goods_type(){return $this->_goods_type;}
	public function get_discount_upper(){return $this->_discount_upper;}
	public function get_discount_floor(){return $this->_discount_floor;}
	public function get_push_money(){return $this->_push_money;}
	public function get_priority(){return $this->_priority;}

    public function getSalesChannels(){
        $model = new SalesChannelsModel(1);
        return $model->getSalesChannelsInfo("id,channel_name",null);        
    }

}
?>