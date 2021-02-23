<?php
/**
 *  -------------------------------------------------
 *   @file		: AppFactoryApplyView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 10:37:58
 *   @update	:
 *  -------------------------------------------------
 */
class AppFactoryApplyView extends View
{
	protected $_apply_id;
	protected $_style_id;
	protected $_style_sn;
	protected $_f_id;
	protected $_factory_id;
	protected $_factory_name;
	protected $_factory_sn;
	protected $_xiangkou;
	protected $_factory_fee;
	protected $_type;
	protected $_status;
	protected $_apply_num;
	protected $_make_name;
	protected $_crete_time;
	protected $_check_name;
	protected $_check_time;
	protected $_info;


	public function get_apply_id(){return $this->_apply_id;}
	public function get_style_id(){return $this->_style_id;}
	public function get_style_sn(){return $this->_style_sn;}
	public function get_f_id(){return $this->_f_id;}
	public function get_factory_id(){return $this->_factory_id;}
	public function get_factory_name(){return $this->_factory_name;}
	public function get_factory_sn(){return $this->_factory_sn;}
	public function get_xiangkou(){return $this->_xiangkou;}
	public function get_factory_fee(){return $this->_factory_fee;}
	public function get_type(){return $this->_type;}
	public function get_status(){return $this->_status;}
	public function get_apply_num(){return $this->_apply_num;}
	public function get_make_name(){return $this->_make_name;}
	public function get_crete_time(){return $this->_crete_time;}
	public function get_check_name(){return $this->_check_name;}
	public function get_check_time(){return $this->_check_time;}
	public function get_info(){return $this->_info;}

}
?>