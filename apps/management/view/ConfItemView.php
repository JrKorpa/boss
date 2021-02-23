<?php
/**
 *  -------------------------------------------------
 *   @file		: ConfItemView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-29 18:07:06
 *   @update	:
 *  -------------------------------------------------
 */
class ConfItemView
{
	protected $_id;
	protected $_item;
	protected $_db_host;
	protected $_db_port;
	protected $_db_name;
	protected $_db_user;
	protected $_db_pwd;
	protected $_note;

	function __construct($id = NULL){
		if($id === null){
			$this->_id = null;
		}else{
			$this->_id = intval($id);
			$param = $this->getParam($id);
			foreach ($param as $k => $v) {
				$k = '_'.$k;
				$this->$k = $v;
			}
		}

	}

	public function get_id(){return $this->_id;}
	public function get_item(){return (empty($this->_id)&&$this->_id!==0)?"DbConfig".$this->getInsertId():$this->_item;}
	public function get_db_host(){return $this->_db_host;}
	public function get_db_port(){return empty($this->_id)?"3306":$this->_db_port;}
	public function get_db_name(){return $this->_db_name;}
	public function get_db_user(){return $this->_db_user;}
	public function get_db_pwd(){return $this->_db_pwd;}
	public function get_note(){return $this->_note;}

	private function getParam($id){
		$model = new ConfItemModel();
		$param = $model->getParam();

		return $param[$id];
	}

	public function getInsertId(){
		$model = new ConfItemModel();
		$param = $model->getParam();
		$insertId = str_pad(count($param),2,"0",STR_PAD_LEFT);
		return $insertId;
	}



}
?>