<?php
/**
 *  -------------------------------------------------
 *   @file		: WriteOffCompanyModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-06-23 16:39:11
 *   @update	:
 *  -------------------------------------------------
 */
class WriteOffCompanyModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'write_off_company';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"pay_type_id"=>"订购类型ID",
"pay_type"=>"订购类型");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url WriteOffCompanyController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
                if (isset($where['pay_type_id']) && !empty($where['pay_type_id'])){
                    $str .= "`pay_type_id`='".$where['pay_type_id']."' AND ";
                }
               
                if (isset($where['company_id']) && !empty($where['company_id'])){
                    $str .= "`company_id`='".$where['company_id']."' AND ";
                }
             
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
                //echo $sql;exit;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
        function SavePayTypeData($where) {
            if (isset($where['pay_type_id']) && !empty($where['pay_type_id'])){
                $pay_type_id = $where['pay_type_id'];
                
            }
            if (isset($where['pay_type']) && !empty($where['pay_type'])){
                $pay_type = $where['pay_type'];
                
            }
            if (isset($where['company_id']) && !empty($where['company_id'])){
                $company_id = $where['company_id'];
                
            }
            if (isset($where['company']) && !empty($where['company'])){
                $company = $where['company'];
                
            }
            $sql = "select count(*) from `write_off_company` where pay_type_id=$pay_type_id and company_id={$company_id}";
            $num = $this->db()->getOne($sql);
            if ($num >= 1){
                return false;
            }else{
                $sql = "insert into write_off_company(pay_type_id,pay_type,company_id,company) values ($pay_type_id,'{$pay_type}',$company_id,'{$company}')";
                $res = $this->db()->query($sql);
                return $res;
            }
            
        }
        function delPayTypeData($id){
            $sql = "delete from `write_off_company` where `id`=$id";
            
            $res = $this->db()->query($sql);
            return $res;
        }
	
		public function getwriteoffList()
	   {
			$sql = 'select pay_type_id,company_id from write_off_company';
			return $this->db()->getAll($sql);
	   }
}

?>