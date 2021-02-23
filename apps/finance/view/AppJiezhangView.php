<?php
/**
 *  -------------------------------------------------
 *   @file		: AppJiezhangView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-21 19:04:39
 *   @update	:
 *  -------------------------------------------------
 */
class AppJiezhangView extends View
{
	protected $_id;
	protected $_qihao;
	protected $_start_time;
	protected $_end_time;
	protected $_year;


	public function get_id(){return $this->_id;}
	public function get_qihao(){return $this->_qihao;}
	public function get_start_time(){return $this->_start_time;}
	public function get_end_time(){return $this->_end_time;}
	public function get_year(){return $this->_year;}

	public function getYears(){
		$year = date('Y');
		for($i=0;$i<10;$i++){
			$years[$year] = $year;
			$year = $year -1;
		}
		return $years;
	}

}
?>