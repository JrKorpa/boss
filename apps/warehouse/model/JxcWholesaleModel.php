<?php
/**
 *  -------------------------------------------------
 *   @file		: JxcWholesaleModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-25 19:01:18
 *   @update	:
 *  -------------------------------------------------
 */
class JxcWholesaleModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'jxc_wholesale';
		$this->pk='wholesale_id';
		$this->_prefix='';
        $this->_dataObject = array(
            "wholesale_id"=>" ",
            "wholesale_sn"=>"批发客户编号",
            "wholesale_name"=>"批发客户名称",
            "wholesale_credit"=>"授信额度",
            "wholesale_status"=>"开启状态  1=开启，0=关闭",
            "add_name"=>"添加人",
            "add_time"=>"添加时间",
            "sign_required"=>"是否需求签收",
            "sign_company"=>"签收公司"            
        );
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url JxcWholesaleController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT w.*, c.company_name FROM `".$this->table()."` w left join cuteframe.company c on c.id = w.sign_company ";
		$str = '';
//		if($where['xxx'] != "")
//		{
//			$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//		}
		if(!empty($where['wholesale_sn']))
		{
			$str .= "`wholesale_sn`='".$where['wholesale_sn']."' AND ";
		}
		if(!empty($where['wholesale_name']))
		{
			$str .= "`wholesale_name`='".$where['wholesale_name']."' AND ";
		}
		if(!empty($where['wholesale_credit']))
		{
			$str .= "`wholesale_credit`='".$where['wholesale_credit']."' AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `wholesale_id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	
	
	/**
	 * 普通查询
	 * @param $type one 查询单个字段， row查询一条记录 all 查询多条记录
	 */
	public function select2($fields = ' * ' , $where = " 1 " , $type = 'one'){
		$sql = "SELECT {$fields} FROM `".$this->table()."` WHERE {$where}";
		if($type == 'one'){
			$res = $this->db()->getOne($sql);
		}else if($type == 'row'){
			$res = $this->db()->getRow($sql);
		}else if($type == 'all'){
			$res = $this->db()->getAll($sql);
		}
		return $res;
	}

	/***************/
	public function getTocustByCompanyID($company_id){
	    $sql="select distinct s.wholesale_id,s.wholesale_name from warehouse_bill b,jxc_wholesale s where b.to_customer_id=s.wholesale_id and b.from_company_id='".$company_id."' and bill_type='P' ";	
	    $res=$this->db()->getAll($sql);
	    return $res;
	}
}

?>