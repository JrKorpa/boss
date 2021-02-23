<?php
/**
 *  -------------------------------------------------
 *   @file		: AppWsdExchangeView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-12-25 17:49:45
 *   @update	:
 *  -------------------------------------------------
 */
class AppWsdExchangeView extends View
{
	protected $_wsd_id;
	protected $_wsd_code;
	protected $_wsd_name;
	protected $_wsd_mobile;
	protected $_wsd_user;
	protected $_wsd_department;
    protected $_wsd_department_name;
    protected $_wsd_is_bespoke;
	protected $_wsd_time;


	public function get_wsd_id(){return $this->_wsd_id;}
	public function get_wsd_code(){return $this->_wsd_code;}
	public function get_wsd_name(){return $this->_wsd_name;}
	public function get_wsd_mobile(){return $this->_wsd_mobile;}
	public function get_wsd_user(){return $this->_wsd_user;}
	public function get_wsd_department(){return $this->_wsd_department;}
    public function get_wsd_department_name(){return $this->_wsd_department_name;}
    public function get_wsd_is_bespoke(){return $this->_wsd_is_bespoke;}
	public function get_wsd_time(){return $this->_wsd_time;}

}
?>