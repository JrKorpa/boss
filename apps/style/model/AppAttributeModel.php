<?php
/**
 *  -------------------------------------------------
 *   @file		: AppAttributeModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 11:11:09
 *   @update	:
 *  -------------------------------------------------
 */
class AppAttributeModel extends Model
{
        public $_prefix = 'attribute';
        public $_show_type_arr = array(1=>'文本框',2=>'单选',3=>'多选',4=>'下拉列表');
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_attribute';
        $this->_dataObject = array("attribute_id"=>"属性id",
"attribute_name"=>"属性名称",
"attribute_code"=>"属性编码",
"attribute_status"=>"状态:1启用;0停用",
"show_type"=>"展示方式：1文本框，2单选，3多选，4下拉",
"create_time"=>"创建时间",
"create_user"=>"创建人",
"attribute_remark"=>"记录备注");
                
		parent::__construct($id,$strConn);
	}
        
        /**
	 *	pageList，分页列表
	 *
	 *	@url ApplicationController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT a.*,b.attribute_sort FROM `".$this->table()."` as a,app_attribute_ext as b  WHERE a.attribute_id=b.attribute_id ";

		if($where['attribute_name'] != "")
		{
			$sql .= " AND a.attribute_name like \"%".addslashes($where['attribute_name'])."%\"";
		}
		if($where['attribute_status'] != "")
		{
			$sql .= " AND a.attribute_status =".addslashes($where['attribute_status']);
		}
		
		$sql .= " ORDER BY b.attribute_sort asc,a.attribute_id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
        
        /*
         * 获取所有属性值
         */
        public function getCtlList () 
	{
		$sql = "SELECT * FROM `".$this->table()."` ";
		return $this->db()->getAll($sql);
	}
    
	/*
	 * 获取所有启用状态的属性值
	*/
	public function getCtlListon ()
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE `attribute_status`=1";
		return $this->db()->getAll($sql);
	}
    /*
     * 获取属性的属性值信息
     */
    public function getAttributAndValue($where){
    	//var_dump($where);exit;
        $sql = "SELECT a.`attribute_id`,b.`att_value_id`,b.`att_value_name` FROM `app_attribute` AS a,`app_attribute_value` AS b WHERE a.`attribute_id`=b.`attribute_id` ";
        if(!empty($where['attribute_name'])){
            $attribute_name = $where['attribute_name'];
            $sql .= "AND `attribute_name` LIKE '".$attribute_name."' ";
        }
        if(!empty($where['attribute_code'])){
        	$sql .= "AND a.`attribute_code` = '".$where['attribute_code']."' ";
        }
        return $this->db()->getAll($sql);
    }
    
    /*
     * 获取属性的属性名和编码 验证是否有重复
    */
    public function getAttributeName($attribute_name,$attribute_code){
    	$sql = "SELECT `attribute_name`,`attribute_code` FROM `{$this->table()}`";
    	if($attribute_name!=''){
    		$sql .= " WHERE `attribute_name`='{$attribute_name}'";
    	}
    	if($attribute_code!=''){
    		$sql .= "or `attribute_code`='{$attribute_code}'";
    	}
    	//echo $sql;exit;
    	return $this->db()->getRow($sql);
    }
    
    /*
     * 获取属性信息
     */
    public function getAttributeInfoByName($attribute_name){
        $sql = "SELECT `attribute_id`,`attribute_code` FROM `{$this->table()}` WHERE `attribute_name`='{$attribute_name}' ";
    	return $this->db()->getRow($sql);
    }
    
    public function getAttributeExt($id){
        $sql = "select * from app_attribute_ext where attribute_id={$id}";
        return $this->db()->getRow($sql);
    }
  
}

?>