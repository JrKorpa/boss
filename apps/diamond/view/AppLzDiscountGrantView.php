<?php
/**
 *  -------------------------------------------------
 *   @file		: AppLzDiscountGrantView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-19 18:32:50
 *   @update	:
 *  -------------------------------------------------
 */
class AppLzDiscountGrantView extends View
{
	protected $_id;
	protected $_user_id;
	protected $_type;
	protected $_zhekou;
	protected $_mima;
	protected $_create_user_id;
	protected $_create_user;
	protected $_createtime;
	protected $_endtime;
	protected $_use_user_id;
	protected $_use_user;
	protected $_usetime;
	protected $_status;
    public $_diamond_type =array('1'=>'普通（现货）<0.5克拉','2'=>'普通（现货）0.5（含）~1.0克拉','3'=>'普通（现货）1.0（含）~1.5克拉','4'=>'普通（现货）1.5（含）克拉以上','5'=>'星耀<0.5克拉','6'=>'星耀0.5（含）~1.0克拉','7'=>'星耀1.0（含）~1.5克拉','8'=>'星耀1.5（含）克拉以上','9'=>'天生一对裸石','10'=>'天生一对成品','11'=>'成品','12'=>'普通（期货）<0.5克拉','13'=>'普通（期货）0.5（含）~1.0克拉','14'=>'普通（期货）1.0（含）~1.5克拉','15'=>'普通（期货）1.5（含）克拉以上','16'=>'香榭巴黎','17'=>'皇室公主');


    public function get_id(){return $this->_id;}
	public function get_user_id(){return $this->_user_id;}
	public function get_type(){return $this->_type;}
	public function get_zhekou(){return $this->_zhekou;}
	public function get_mima(){return $this->_mima;}
	public function get_create_user_id(){return $this->_create_user_id;}
	public function get_create_user(){return $this->_create_user;}
	public function get_createtime(){return $this->_createtime;}
	public function get_endtime(){return $this->_endtime;}
	public function get_use_user_id(){return $this->_use_user_id;}
	public function get_use_user(){return $this->_use_user;}
	public function get_usetime(){return $this->_usetime;}
	public function get_status(){return $this->_status;}
    public function get_diamond_type(){
        return $this->_diamond_type;
    }
}
?>