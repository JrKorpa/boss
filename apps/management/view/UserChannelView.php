<?php
/**
 *  -------------------------------------------------
 *   @file		: UserChannelView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-29 19:06:11
 *   @update	:
 *  -------------------------------------------------
 */
class UserChannelView extends View
{
	protected $_id;
	protected $_user_id;
	protected $_channel_id;


	public function get_id(){return $this->_id;}
	public function get_user_id(){return $this->_user_id;}
	public function get_channel_id(){return $this->_channel_id;}

	public function set_user_id ($id)
	{
		$this->_user_id = $id;
	}

	public function getChannelTree ($uid)
	{
		$m = $this->getModel();
		return $m->getChannelList($uid);
	}
        
        public function getChannelUsers() {
  		$m = $this->getModel();
		return $m->getChannelUsers();          
        }
        
        public function getAllChannels() {
  		$m = $this->getModel();
		return $m->getAllChannels();          
        }
        
        
}
?>