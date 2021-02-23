<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseCaigouTiaozhengView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-30 17:03:50
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseCaigouTiaozhengView extends View
{
	protected $_id;
	protected $_goods_id;
	protected $_goods_sn;
	protected $_type;
	protected $_shizhong;
	protected $_yanse;
	protected $_jingdu;
	protected $_qiegong;
	protected $_yuanshichengbenjia;
	protected $_xianzaichengben_old;
	protected $_xianzaichengben_new;
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
	public function get_yuanshichengbenjia(){return $this->_yuanshichengbenjia;}
	public function get_xianzaichengben_old(){return $this->_xianzaichengben_old;}
	public function get_xianzaichengben_new(){return $this->_xianzaichengben_new;}
	public function get_shuoming(){return $this->_shuoming;}
	public function get_addname(){return $this->_addname;}
	public function get_addtime(){return $this->_addtime;}
	public function get_checkname(){return $this->_checkname;}
	public function get_checktime(){return $this->_checktime;}
	public function get_info(){return $this->_info;}
	public function get_status(){return $this->_status;}

}
?>