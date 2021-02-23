<?php
/**
 *  -------------------------------------------------
 *   @file		: DiaBillView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-03-22 15:40:08
 *   @update	:
 *  -------------------------------------------------
 */
class DiaBillSearchView extends View
{
	protected $_id;
	protected $_bill_no;
    protected $_processors_id;
    protected $_factory_id;
	protected $_processors;
	protected $_factory;
	protected $_price_total;
	protected $_dia_package;
	protected $_create_user;
    protected $_check_user;
	protected $_num;
	protected $_weight;
	protected $_paper_no;
	protected $_create_time;
	protected $_check_time;
	protected $_status;
    protected $_remark;


	public function get_id(){return $this->_id;}
	public function get_bill_no(){return $this->_bill_no;}
    public function get_processors_id(){return $this->_processors_id;}
    public function get_factory_id(){return $this->_factory_id;}
	public function get_processors(){return $this->_processors;}
	public function get_factory(){return $this->_factory;}
	public function get_price_total(){return $this->_price_total;}
	public function get_dia_package(){return $this->_dia_package;}
	public function get_create_user(){return $this->_create_user;}
    public function get_check_user(){return $this->_check_user;}
	public function get_num(){return $this->_num;}
	public function get_weight(){return $this->_weight;}
	public function get_paper_no(){return $this->_paper_no;}
	public function get_create_time(){return $this->_create_time;}
	public function get_check_time(){return $this->_check_time;}
	public function get_status(){return $this->_status;}
    public function get_remark(){return $this->_remark;}

}
?>