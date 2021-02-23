<?php
/**
 *  -------------------------------------------------
 *   @file		: CompanydepartmentModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 11:54:27
 *   @update	:
 *  -------------------------------------------------
 */
class CompanyDepartmentModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'company_department';
        $this->_dataObject = array("id"=>" ",
"company_id"=>" ",
"dep_id"=>" ");
		parent::__construct($id,$strConn);
	}

	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{

		if($where['dept_id']!==''){
			$sql = "SELECT c.company_name,c.contact,c.phone,cd.id as id,c.id as company_id FROM `company_department` as cd LEFT JOIN company as c on cd.company_id=c.id WHERE cd.dep_id=".$where['dept_id']." AND c.is_deleted=0 ORDER BY c.id DESC";
			$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
			return $data;
		}

		if($where['company_id']!==''){
			$sql ="SELECT d.name,d.code,cd.id,d.id as dep_id FROM company_department as cd LEFT JOIN department as d on cd.dep_id=d.id WHERE cd.company_id =".$where['company_id']." and d.is_deleted=0";
			$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
			return $data;
		}
	}

	/*根据公司id或者部门id取出对应的id合集*/
	/**
	 *
	 * @param $where array 根据（公司和部门id取出相对应的集合）
	 *
	 * @return       返回一个集合字符串
	 */
	public function idGather($where){
		if($where['dept_id']!==''){
		    $sql="SELECT group_concat(cd.company_id) as company_id FROM `company_department` as cd ";
			return $this->db()->getOne($sql);
		}
		if($where['company_id']!==''){
			$sql ="SELECT group_concat(cd.dep_id) as dep_id FROM company_department as cd ";
			return $this->db()->getOne($sql);

		}
		return '';


	}


}

?>