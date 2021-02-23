<?php
/**
 *  -------------------------------------------------
 *   @file		: AppProcessorProcessView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-19 21:46:16
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorProcessView extends View
{
	protected $_id;
	protected $_process_name;
	protected $_business_type;
	protected $_business_scope;
	protected $_department_id;
	protected $_department_name;
	protected $_is_enabled;
	protected $_is_deleted;
	protected $_create_user_id;
	protected $_create_user;
	protected $_create_time;


	public function get_id(){return $this->_id;}
	public function get_process_name(){return $this->_process_name;}
	public function get_business_type(){return $this->_business_type;}
	public function get_business_scope(){return $this->_business_scope;}
	public function get_department_id(){return $this->_department_id;}
	public function get_is_enabled(){return ($this->_id)?$this->_is_enabled:1;}
	public function get_is_deleted(){return $this->_is_deleted;}
	public function get_create_user_id(){return $this->_create_user_id;}
	public function get_create_user(){return $this->_create_user;}
	public function get_create_time(){return $this->_create_time;}

	public function get_department_name($id){
		if($id){
			$sql = 'SELECT `name` FROM `department` WHERE `id` = '.$id;
			$this->_department_name = DB::cn(1)->getOne($sql);
			return $this->_department_name;
		}else{
			return '';
		}
	}

	/**
	 * 生产审批流程名称
	 * @param $id 流程ID
	 * @return string
	 */
	public function processUser($id){
		$str = '';
		if($id) {
			$sql = 'SELECT `user_id` FROM `app_processor_user` WHERE `process_id` = ' . $id . ' ORDER BY `user_order`';
			$res = DB::cn(13)->getAll($sql);
			foreach ($res as $k => $v) {
				$sql = 'SELECT `real_name` FROM `user` WHERE `id` = ' . $v['user_id'];
				$real_name = DB::cn(1)->getOne($sql);
				$str .= (!$k)?$real_name:'>>>'.$real_name;
			}
		}
		return $str;
	}



}
?>