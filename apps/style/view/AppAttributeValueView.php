<?php
/**
 *  -------------------------------------------------
 *   @file		: AppAttributeValueView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 15:06:02
 *   @update	:
 *  -------------------------------------------------
 */
class AppAttributeValueView extends View
{
	protected $_att_value_id;
	protected $_attribute_id;
	protected $_att_value_name;
	protected $_att_value_status;
	protected $_create_time;
	protected $_create_user;
	protected $_att_value_remark;
        


	public function get_att_value_id(){return $this->_att_value_id;}
	public function get_attribute_id(){return $this->_attribute_id;}
	public function get_att_value_name(){return $this->_att_value_name;}
	public function get_att_value_status(){return $this->_att_value_status;}
	public function get_create_time(){return $this->_create_time;}
	public function get_create_user(){return $this->_create_user;}
	public function get_att_value_remark(){return $this->_att_value_remark;}
        
    public function getCtlList () 
	{
		$m = new AppAttributeModel(11);
		return $m->getCtlListon();
	}

}
?>