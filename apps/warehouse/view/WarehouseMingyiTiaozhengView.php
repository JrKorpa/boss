<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseMingyiTiaozhengView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-16 16:02:18
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseMingyiTiaozhengView extends View
{
	protected $_id;
	protected $_goods_id;
	protected $_goods_sn;
	protected $_type;
	protected $_shizhong;
	protected $_yanse;
	protected $_jingdu;
	protected $_qiegong;
	protected $_yuanshimingyichengben;
	protected $_xianzaimingyi_old;
	protected $_xianzaimingyi_new;
	protected $_shuoming;
	protected $_addname;
	protected $_addtime;
	protected $_checkname;
	protected $_checktime;
	protected $_info;
	protected $_status;


	public function get_id(){return $this->_id;}
	public function get_goods_id(){return $this->_goods_id;}
	public function get_goods_sn(){return $this->_goods_sn;}
	public function get_type(){return $this->_type;}
	public function get_shizhong(){return $this->_shizhong;}
	public function get_yanse(){return $this->_yanse;}
	public function get_jingdu(){return $this->_jingdu;}
	public function get_qiegong(){return $this->_qiegong;}
	public function get_yuanshimingyichengben(){return $this->_yuanshimingyichengben;}
	public function get_xianzaimingyi_old(){return $this->_xianzaimingyi_old;}
	public function get_xianzaimingyi_new(){return $this->_xianzaimingyi_new;}
	public function get_shuoming(){return $this->_shuoming;}
	public function get_addname(){return $this->_addname;}
	public function get_addtime(){return $this->_addtime;}
	public function get_checkname(){return $this->_checkname;}
	public function get_checktime(){return $this->_checktime;}
	public function get_info(){return $this->_info;}
	public function get_status(){return $this->_status;}

}
?>