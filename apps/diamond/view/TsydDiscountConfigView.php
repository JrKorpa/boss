<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseLzDiscountConfigView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-19 15:37:36
 *   @update	:
 *  -------------------------------------------------
 */
class TsydDiscountConfigView extends View
{
	protected $_id;
	protected $_channel_id;
	protected $_type;
	protected $_zhekou;
	protected $_enabled;
    public $_discount_type =array('1'=>'普通<0.5克拉','2'=>'普通0.5（含）~1.0克拉','3'=>'普通1.0（含）~1.5克拉','4'=>'普通1.5（含）克拉以上','5'=>'星耀<0.5克拉','6'=>'星耀0.5（含）~1.0克拉','7'=>'星耀1.0（含）~1.5克拉','8'=>'星耀1.5（含）克拉以上','9'=>'天生一对裸石','10'=>'天生一对成品','11'=>'成品');


	public function get_id(){return $this->_id;}
	public function get_channel_id(){return $this->_channel_id;}
	public function get_type(){return $this->_type;}
	public function get_zhekou(){return $this->_zhekou;}
	public function get_enabled(){return $this->_enabled;}
    public function get_discount_type(){
        return $this->_discount_type;
    }

}
?>