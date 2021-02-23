<?php
/**
 *  -------------------------------------------------
 *   @file		: AppMemeberCardView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 13:57:20
 *   @update	:
 *  -------------------------------------------------
 */
class AppMemeberCardView extends View
{
	protected $_id;
	protected $_mem_card_sn;
	protected $_mem_card_level;
	protected $_mem_card_uptime;
	protected $_men_card_type;
	protected $_mem_card_status;
	protected $_is_deleted;
	protected $_addby_id;
	protected $_add_time;
        

        public function get_id(){return $this->_id;}
	public function get_mem_card_sn(){return $this->_mem_card_sn;}
	public function get_mem_card_level(){return $this->_mem_card_level;}
	public function get_mem_card_uptime(){return $this->_mem_card_uptime;}
	public function get_men_card_type(){return $this->_men_card_type;}
	public function get_mem_card_status(){return !empty($this->_mem_card_status)?$this->_mem_card_status:2;}
	public function get_is_deleted(){return $this->_is_deleted;}
	public function get_addby_id(){return $this->_addby_id;}
	public function get_add_time(){return $this->_add_time;}
       
	/**
	 * giveMember 授予会员
	 * @param $tel
	 */
	public function giveMember($tel){
		$sql = 'SELECT member_id,member_name FROM base_member_info WHERE member_phone = '.$tel;
		$member = DB::cn(17)->getRow($sql);
		print_r($member);
	}

}
?>