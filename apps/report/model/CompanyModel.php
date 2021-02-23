<?php
/**
 *  -------------------------------------------------
 *   @file		: CompanyModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-31 17:53:07
 *   @update	:
 *  -------------------------------------------------
 */
class CompanyModel extends Model
{
	public $parent_company_name;
	public $create_user_name;
	protected $pagedata=array();
	protected $newdata=array();
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'company';
        $this->_dataObject = array("id"=>"主键",
"company_sn"=>"编码",
"company_name"=>"名称",
"parent_id"=>"PID",
"contact"=>"联系人",
"phone"=>"联系电话",
"address"=>"公司地址",
"bank_of_deposit"=>"开户银行",
"account"=>"开户银行账户",
"receipt"=>"是否能力开发票 0=不开1=有",
"is_sign"=>"是否财务签字；0为否；1为是",
"remark"=>"备注",
"create_user"=>"创建人",
"create_time"=>"创建时间",
"is_system"=>"是否系统内置",
"is_deleted"=>"是否有效 0有效1无效");

		parent::__construct($id,$strConn);
		$this->getParentCompanyName($this->getValue('parent_id'));
		$this->getcreate_user($this->getValue('create_user'));
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ApplicationController/search
	 */
	public function yArray ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT m.* FROM ".$this->table()." as m WHERE `is_deleted`=".$where['is_deleted'];

		if($where['company_name'] != "")
		{
			$sql .= " AND m.company_name like \"%".addslashes($where['company_name'])."%\"";
		}
		if($where['contact'] != "")
		{
			$sql .= " AND m.contact like \"%".addslashes($where['contact'])."%\"";
		}
		$sql .= " ORDER BY m.id DESC";

		$this->pagedata  = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
	}

	public function fArray($parent_id=0,$lev=0){
		foreach($this->pagedata['data'] as $key=>$val){
			if($val['parent_id']==$parent_id){
				$val['company_name'] = str_repeat('&nbsp;&nbsp;', $lev).$val['company_name'];
				$this->newdata['data'][]=$val;
				$this->fArray($val['id'],$lev+1);
			}
		}

		return $this->newdata;
	}



	public function pageList($where,$page,$pageSize=10,$useCache=true){
		$this->yArray($where,$page,$pageSize,$useCache);
		$this->fArray();
		return $this->pagedata;
	}

	public function getCompanyTree()
	{
		$sql = "SELECT * FROM ".$this->table()." WHERE is_deleted='0'";

		return $this->db()->getAll($sql);

	}

	public function getCompanyExist($company_id,$department_id){

		$sql ="SELECT * FROM company_department WHERE company_id=".$company_id;
		$res = $this->db()->getOne($sql);
		if($res===false){
			$sql ="SELECT * FROM company WHERE id=".$company_id;
			return  $this->db()->getOne($sql);
		}

		$sql = "SELECT * FROM company_department WHERE exists(SELECT null  from company as u WHERE u.id=".$company_id." AND is_deleted=0) AND dep_id=".$department_id." AND company_id<>".$company_id;
		return $this->db()->getOne($sql);
	}

	public function getCompanyExists($company_id){
		if(!$company_id){
			return false;
		}
		$sql = "SELECT * FROM `".$this->table()."` where `id`=".$company_id." AND `is_deleted`=0";
		return $this->db()->getOne($sql);
	}

	public function getList(){
		$sql ="SELECT * FROM `company` WHERE parent_id=0";
		$pdata = $this->db()->getRow($sql);
		$pdata['level']=1;
		$pdata['name']=$pdata['company_name'];
		$sql ="SELECT * FROM `company` WHERE `parent_id`=1";
		$sdata = $this->db()->getAll($sql);
		$arr = array($pdata);
		foreach ($sdata as $k=>$val) {
			$val['level']=$k+2;
			$val['name'] =$val['company_name'];
			$arr[]= $val;
		}
	return $arr;
	}

	public function getCompanyName($id){
		if($id){
			$sql = "SELECT `company_name` FROM `company` WHERE id=".$id;
			$data = $this->db()->getOne($sql);
			return $data;
		}else{
			return false;
		}

	}

	public function getParentCompanyName($parent_id){
		if($parent_id){
			$sql = "SELECT `company_name` FROM `company` WHERE 1 AND id=".$parent_id;
			$data = $this->db()->getOne($sql);
			$this->parent_company_name = $data;
		}

	}

	public function getcreate_user($user_id){
		if($user_id){
			$sql = "SELECT `real_name` FROM user WHERE id=".$user_id;
			$data = $this->db()->getOne($sql);
			$this->create_user_name = $data;
		}
	}


    /**
     * 获取所有的体验店  部门 和公司
     * @return array
     */
    public function getAllDCS(){
        $arr = array();
        $sql = "select `id`,`company_name` from company WHERE is_deleted=0";
        $com = $this->db()->getAll($sql);
        $arr = array();
        foreach($com as $key=>$val){
            $arr[3][$val['id']]=$val['company_name'];
        }


        $sql = "select `id`,`shop_name` from shop_cfg WHERE is_delete=0";
        $shop= $this->db()->getAll($sql);
        foreach($shop as $key=>$val){
            $arr[2][$val['id']]=$val['shop_name'];
        }

        $sql = "select `id`,`name` from department WHERE is_deleted=0";
        $depart = $this->db()->getAll($sql);

        foreach($depart as $key=>$val){
            $arr[1][$val['id']]=$val['name'];
        }
        return $arr;

    }







}

?>