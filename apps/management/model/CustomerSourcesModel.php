<?php
/**
 *  -------------------------------------------------
 *   @file		: CustomerSourcesModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-05 11:29:16
 *   @update	:
 *  -------------------------------------------------
 */
class CustomerSourcesModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'customer_sources';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"序号",
"source_name"=>"来源名称",
"source_code"=>"来源编码",
"source_class"=>"1线上，2线下",
"source_type"=>"1部门，2体验店，3公司",
"source_own_id"=>"渠道ID",
"source_own"=>"来源所属",
"add_id"=>"创建人",
"add_time"=>"创建时间",
"update_id"=>"更新人",
"update_time"=>"修改时间",
"is_deleted"=>"删除标识",
"fenlei"=>"所属分类 0:全部、-1:其他、1:异业联盟、2:社区、3:BDD相关、4:团购、5:老顾客、6:数据、7:网络来源",
);
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url CustomerSourcesController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 AND is_deleted = 0 ";
		if($where['source_name'] != "")
		{
			$sql .= " AND source_name like \"%".addslashes($where['source_name'])."%\"";
		}
		if($where['source_code'] != "")
		{
			$sql .= " AND source_code like \"%".addslashes($where['source_code'])."%\"";
		}
        if($where['fenlei']!=""){
            	$sql .="AND fenlei='".$where['fenlei']."'";
            
        }

        if($where['source_class']!=""){
                $sql .="AND source_class='".$where['source_class']."'";

        }
        if($where['is_enabled']!=""){
            	$sql .="AND is_enabled='".$where['is_enabled']."'";
            
        }
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

    function  getCustomerSourcesAllList($select="*") {
        $sql = "SELECT {$select} FROM `{$this->table()}` ";
        return $this->db()->getAll($sql);
    }

    function  getCustomerSourcesList($select="*",$where=array()) {
        $sql = "SELECT {$select} FROM `{$this->table()}` WHERE `is_enabled`=1 AND `is_deleted`= 0 ";


        if(isset($where['id']) && !empty($where['id'])){

            $sql .= " AND `id` = {$where['id']}  ";
        }
        return $this->db()->getAll($sql);
    }

	public function getSources(){
		$sql = "SELECT `id`,`source_name` FROM ".$this->table()." WHERE `is_deleted`=0";
		return $this->db()->getAll($sql);
	}

	//取财物应收允许收款的客户来源
	public function getSourcesPay($select = "`id`,`source_code`,`source_name`")
	{
		$sql = "SELECT ".$select." FROM ".$this->table()." WHERE is_deleted = 0 AND is_pay = 1";
		return $this->db()->getAll($sql);
	}

    /**
     *  通过id找到数据记录
     */
    public function getCustomerSourceById($id){
        if(empty($id)){
            return false;
        }
		$sql = "SELECT `id`,`source_name`,`source_code`,`source_class`,`source_type`,`source_own_id`,`source_own`,`add_id`,`add_time`,`update_id`,`update_time`,`is_deleted`,`is_enabled` FROM `".$this->table()."` WHERE `".$this->pk."` = $id";
		return $this->db()->getRow($sql);
    }

	public function getSourceNameById($id)
	{
		$sql = "SELECT `source_name` FROM ".$this->table()." WHERE id = ".$id;
		return $this->db()->getOne($sql);
	}

	public function hasName ($name)
	{
		$sql = "SELECT COUNT(1) FROM `".$this->table()."` WHERE `source_name` = '{$name}'";
		if($this->pk())
		{
			$sql .=" AND `id`<>'".$this->pk()."'";
		}
		return  $this->db()->getOne($sql,array(),false);

	}
	public function maxid ()
	{
		$sql = "SELECT max(id)+1 FROM `".$this->table()."`";
	     
		return  $this->db()->getOne($sql);

	}


    public function getCustomerSourceNameById($id){
        if(empty($id)){
            return false;
        }
        $sql = "SELECT `source_name` FROM `".$this->table()."` WHERE `".$this->pk."` = $id";
        return $this->db()->getOne($sql);
    }

    public function getSourceBychanelId($id){
        if(empty($id)){
            return false;
        }
        $sql = "SELECT `source_name`,`id` FROM `".$this->table()."` WHERE `is_deleted`=0 AND
          `source_own_id`=$id";
        return $this->db()->getAll($sql);
    }


	//根据分类id获取所有该分类id下的信息
	public function getalldata($shopid,$fenlei)
	{
		$sql = "select id,source_name,fenlei from " .$this->table();
		$sql .=" where ";
		if( $fenlei!='' )
		{
			$sql .= " fenlei = $fenlei and ";
		}
		$sql .= " 1 ";
		return $this->db()->getAll($sql);
	}
}
?>