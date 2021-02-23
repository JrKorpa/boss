<?php
/**
 *  -------------------------------------------------
 *   @file		: CompanydepartmentView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 11:54:27
 *   @update	:
 *  -------------------------------------------------
 */
class CompanyDepartmentView extends View
{
	protected $_id;
	protected $_company_id;
	protected $_dep_id;


	public function get_id(){return $this->_id;}
	public function get_company_id(){return $this->_company_id;}
	public function get_dep_id(){return $this->_dep_id;}

}
?>