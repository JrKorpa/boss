<?php
/**
 *  -------------------------------------------------
 *   @file		: SalesChannelsView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 15:25:16
 *   @update	:
 *  -------------------------------------------------
 */
class SalesChannelsView extends View
{
	protected $_id;
	protected $_channel_name;
	protected $_channel_code;
	protected $_channel_class;
	protected $_channel_type;
	protected $_channel_own;
	protected $_addby_id;
	protected $_addby_time;
	protected $_updateby_id;
	protected $_update_time;
	protected $_is_deleted;
	protected $_channel_own_id;
	protected $_channel_man;
	protected $_channel_email;
	protected $_channel_phone;
	protected $_company_id;
	protected $_qrcode;
	protected $_is_tsyd;
	protected $_wholesale_id;


	public function get_id(){return $this->_id;}
	public function get_channel_name(){return $this->_channel_name;}
	public function get_channel_code(){return $this->_channel_code;}
	public function get_channel_class(){return $this->_channel_class;}
	public function get_channel_type(){return $this->_channel_type;}
	public function get_channel_own(){return $this->_channel_own;}
	public function get_addby_id(){return $this->_addby_id;}
	public function get_addby_time(){return $this->_addby_time;}
	public function get_updateby_id(){return $this->_updateby_id;}
	public function get_update_time(){return $this->_update_time;}
	public function get_is_deleted(){return $this->_is_deleted;}
	public function get_channel_own_id(){return $this->_channel_own_id;}
	public function get_channel_man(){return $this->_channel_man;}
	public function get_channel_email(){return $this->_channel_email;}
	public function get_channel_phone(){return $this->_channel_phone;}
	public function get_company_id(){return $this->_company_id;}
	public function get_qrcode(){return $this->_qrcode;}
	public function get_is_tsyd(){return $this->_is_tsyd;}
	public function get_wholesale_id(){return $this->_wholesale_id;}
	public function getChannelNameById($id){
		$sql = "SELECT `channel_name` FROM `sales_channels` WHERE `id` = '".$id."'";
		$model = $this->getModel();
		$name = $model->db()->getOne($sql);
		return $name;
	}

	public function getUsers ()
	{
		if(!$this->_id)
		{
			return array();
		}
		$model = $this->getModel();
		return $model->getUsers();
	}

}
?>