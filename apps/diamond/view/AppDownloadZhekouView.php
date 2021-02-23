<?php
/**
 *  -------------------------------------------------
 *   @file		: DiamondInfoLogView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-17 18:23:12
 *   @update	:
 *  -------------------------------------------------
 */
class AppDownloadZhekouView extends View
{
	protected $_id;
	protected $_from_ad;
	protected $_operation_type;
	protected $_operation_content;
	protected $_create_time;
	protected $_create_user;


	public function get_id(){return $this->_id;}
	public function get_from_ad(){return $this->_from_ad;}
	public function get_operation_type(){return $this->_operation_type;}
	public function get_operation_content(){return $this->_operation_content;}
	public function get_create_time(){return $this->_create_time;}
	public function get_create_user(){return $this->_create_user;}
		
}
?>