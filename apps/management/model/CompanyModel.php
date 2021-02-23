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
"company_sn"=>"公司编码",
"company_name"=>"公司名称",
"parent_id"=>"PID",
"contact"=>"联系人",
"phone"=>"联系电话",
"address"=>"公司地址",
"bank_of_deposit"=>"开户银行",
"account"=>"开户银行账户",
"receipt"=>"是否能开发票 0=不开,1=开",
"is_sign"=>"是否财务签字；0=否；1=是",
"remark"=>"备注",
"create_user"=>"创建人",
"create_time"=>"创建时间",
"is_system"=>"是否系统内置",
"is_deleted"=>"是否有效 0有效1无效",
"company_type"=>"公司类型"           
        );

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

		if(isset($where['company_name']) && $where['company_name'] != "")
		{
			$sql .= " AND m.company_name like \"%".addslashes($where['company_name'])."%\"";
		}
		if(isset($where['contact']) && $where['contact'] != "")
		{
			$sql .= " AND m.contact like \"%".addslashes($where['contact'])."%\"";
		}
		if(isset($where['is_shengdai']) && $where['is_shengdai']!=""){
		    $sql .= " AND m.is_shengdai={$where['is_shengdai']}";
		}
		if(isset($where['sd_company_id']) && $where['sd_company_id']!=""){
		    $sql .= " AND m.sd_company_id={$where['sd_company_id']}";
		}
		$sql .= " ORDER BY m.id DESC";

		$this->pagedata  = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		foreach($this->pagedata['data'] as $key=>$val){
    		$val['sd_company_name']='';
    		if(!empty($val['sd_company_id'])){
    		    $val['sd_company_name'] = $this->getCompanyName($val['sd_company_id']);
      		}
    		$this->pagedata['data'][$key]=$val;
		}
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
    public function select2($field,$where,$type=1){
        $sql = "SELECT {$field} FROM ".$this->table()." where {$where}";
        if($type==1){
            return $this->db()->getAll($sql);
        }elseif($type==2){
            return $this->db()->getRow($sql);
        }elseif($type==3){
            return $this->db()->getOne($sql);
        }
    }

    function getJxcWholesale(){
    	$sql="SELECT wholesale_id,wholesale_name FROM warehouse_shipping.jxc_wholesale WHERE wholesale_status=1 ORDER BY convert( `wholesale_name` USING gbk )";
    	return $rows=$this->db()->getAll($sql);
    }

    public function get_processors() {
    	$sql = "SELECT name, id from kela_supplier.app_processor_info where status = 1";
    	return $this->db()->getAll($sql);
    }
    
    public function getWarehouse($company_type,$diamond_warehouse,$intersect_companyID){
    	$sql="select w.code from warehouse_shipping.warehouse w,warehouse_shipping.warehouse_rel r,company c where w.id=r.warehouse_id and r.company_id=c.id and c.company_type='{$company_type}' and w.diamond_warehouse='{$diamond_warehouse}' and r.company_id<>'{$intersect_companyID}'  and c.is_deleted=0";
        return $this->db()->getAll($sql);
    }

    //获取公司仓库列表 
    public function getWarehouse_Where($where){
    	$_warehouse_list=array();
    	$sql="select w.* from warehouse_shipping.warehouse w,warehouse_shipping.warehouse_rel r,cuteframe.company c where w.id=r.warehouse_id and r.company_id=c.id and w.is_delete=1 ";

    	if(!empty($where)){
    	    if(!empty($where['company_id'])){
                $sql .= " and c.id in ({$where['company_id']})";
    	    }
    	    if(!empty($where['diamond_warehouse'])){
                $sql .= " and w.diamond_warehouse='{$where['diamond_warehouse']}'";
            }
            //echo $sql;exit();
    	    $_warehouse_list = $this->db()->getAll($sql);  
        }
        return $_warehouse_list;
    }

}

?>