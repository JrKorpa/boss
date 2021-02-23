<?php
/**
 *  -------------------------------------------------
 *   @file		: RelCatAttributeModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 21:06:40
 *   @update	:
 *  -------------------------------------------------
 */
class RelCatAttributeModel extends Model
{
        public $_prefix = 'rel';
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'rel_cat_attribute';
        $this->_dataObject = array("rel_id"=>" ",
"cat_id"=>"款式id",
"cat_type_id"=>"分类名称id",
"product_type_id"=>"产品线名称id",
"attribute_id"=>"属性id",
"is_show"=>"是否显示：1是0否",
"is_default"=>"是否默认：1是0否",
"is_require"=>"是否必填：1是0否",
"status"=>"状态:1启用;0停用",
"attr_type"=>"属性类型:1基本属性;2销售属性;3商品属性",
"create_time"=>"创建时间",
"create_user"=>"创建人",
"default_val"=>"默认值是什么",
"info"=>"备注");
		parent::__construct($id,$strConn);
	}
        
       /**
	 *	pageList，分页列表
	 *
	 *	@url ApplicationController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."`  WHERE 1 ";

		if($where['attribute_id'] != "")
		{
			$sql .= " AND attribute_id =".$where['attribute_id'];
		}
		if($where['cat_type_id'] != "")
		{
			$sql .= " AND cat_type_id =".$where['cat_type_id'];
		}
		if($where['product_type_id'] != "")
		{
			$sql .= " AND product_type_id =".$where['product_type_id'];
		}
		
		$sql .= " ORDER BY rel_id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
        
    /*
     * 获取相关数据
     */
    function getList ($where)
	{
		//$sql = "SELECT a.attribute_id,a.is_show,a.is_default,a.is_require,b.show_type,b.attribute_name,c.att_value_id,c.att_value_name FROM `".$this->table()."` as a ,app_attribute as b,app_attribute_value as c  WHERE a.attribute_id = b.attribute_id and b.attribute_id=c.attribute_id ";
        $sql = "SELECT a.`attribute_id`,a.`is_show`,a.`is_default`,a.`is_require`,b.`show_type`,b.`attribute_name` FROM `rel_cat_attribute` as a inner join `app_attribute` as b on a.`attribute_id` = b.`attribute_id`  ";
                //判断显示和状态
		$sql .=" WHERE `a`.`status`=1 ";
        if(isset($where['cat_type_id']) && $where['cat_type_id'] != "")
		{
			$sql .= " AND `a`.`cat_type_id` = ".$where['cat_type_id'];
		}
		if(isset($where['product_type_id']) && $where['product_type_id'] != "")
		{
			$sql .= " AND `a`.`product_type_id` = ".$where['product_type_id'];
		}
		$sql .= " ORDER BY `a`.`rel_id` DESC";
		return $this->db()->getAll($sql);
	}

    /*
     * 获取相关数据
     */
    function getAttr($where,$attribute_id)
	{
        $sql = "SELECT c.att_value_id,c.att_value_name
        FROM `rel_cat_attribute` as a 
        inner join `app_attribute` as b on a.`attribute_id` = b.`attribute_id` 
        inner join `app_attribute_value` as c on a.`attribute_id` = c.`attribute_id`
        ";
                //判断显示和状态
		$sql .=" WHERE 1 ";
        if(isset($where['cat_type_id']) && $where['cat_type_id'] != "")
		{
			$sql .= " AND a.`cat_type_id` = ".$where['cat_type_id'];
		}
		if(isset($where['product_type_id']) && $where['product_type_id'] != "")
		{
			$sql .= " AND a.`product_type_id` = ".$where['product_type_id'];
		}
        $sql .= " AND a.`attribute_id` = ".$attribute_id;
		$sql .= " ORDER BY a.`rel_id` DESC";
		return $this->db()->getAll($sql);
	}

    public function getAttributeList($where)
    {
        # code...
        $sql = "SELECT a.`attribute_id`,a.`is_show`,a.`is_default`,a.`is_require`,b.`show_type`,b.`attribute_name` FROM `rel_cat_attribute` as a inner join `app_attribute` as b on a.`attribute_id` = b.`attribute_id`";
            //判断显示和状态
        $sql .=" WHERE `a`.`status`=1 ";
        if(isset($where['cat_type_id']) && $where['cat_type_id'] != "")
        {
            $sql .= " AND `a`.`cat_type_id` = ".$where['cat_type_id'];
        }
        if(isset($where['product_type_id']) && $where['product_type_id'] != "")
        {
            $sql .= " AND `a`.`product_type_id` = ".$where['product_type_id'];
        }
        $sql .= " ORDER BY `a`.`rel_id` DESC";
        return $this->db()->getAll($sql);
    }

    public function getAttributeValuesList($where)
    {
        # code...
        $sql = "select `att_value_id`,`att_value_name` from `app_attribute_value` where `attribute_id` = ".$where['attribute_id']." and `att_value_status`=1";
        return $this->db()->getAll($sql);
    }

    

    /*
     * 获取产品线，分类对于的属性和编码
     */
    function  getCatAttrInfo($where){
         $sql = "SELECT a.attribute_id,b.attribute_code,b.attribute_name,b.show_type "
                 . "FROM `rel_cat_attribute` as a inner join app_attribute as b on a.attribute_id = b.attribute_id ";
            //判断显示和状态
        //$sql .="";
        if(isset($where['cat_type_id']))
        {
            $sql .= " AND a.cat_type_id =".$where['cat_type_id'];
        }
        if(isset($where['product_type_id']))
        {
            $sql .= " AND a.product_type_id =".$where['product_type_id'];
        }
        if(isset($where['attribute_id'])){
            $sql .= " AND b.attribute_id =".$where['attribute_id'];
        }        
        return $this->db()->getAll($sql);
    }    

    /*
     * 生成款的属性表
     * luna
     */
    function createTable($table_name, $field_arr){
        return $this->db()->createStyleTable ($table_name, $field_arr);
    }
    
    /*
     * 向款的属性表，插入数据
     * luna
     */
    function inseartStyleList($table_name,$fields_name,$data){
        $str = '';
        foreach ($data as $d_key=>$d_val){
             $str ="";
             foreach ($d_val as $t_key=>$t_val){
                  foreach ($t_val as $n_key=>$n_val){
                      if(in_array($n_key, $fields_name)){
                          $str .="$n_key = '$n_val',";
                      }
                  }
                  
              }
            
             $d_key_arr = explode("_", $d_key);
             $style_id = $d_key_arr[0];
             $str.=" style_id=".$style_id." , cat_type_id=".$d_key_arr[1].",product_type_id=".$d_key_arr[2];
             //判断是否已有数据
             $style_info = $this->getStyleList($table_name, array('style_id'=>$style_id));
            
             if($style_info){//更新
                 $sql = "UPDATE  $table_name SET $str WHERE style_id=".$style_id;
             }else{
                 $sql = "INSERT INTO $table_name SET $str";
             }
             $sql = rtrim($sql,",");
             $res = $this->db()->query($sql);
        }
        return $res;
    }
    
    /*
     * 获取款属性信息
     */
    public function getStyleList($table_name,$where){
        $sql = "SELECT * FROM `".$table_name."` WHERE 1 ";
        if(isset($where['style_id'])){
            $sql.=" AND style_id =".$where['style_id'];
        }
        
        return $this->db()->getAll($sql);
    }
    
    /*
     * 获取所有字段
     * luna
     */
    function getStyleFields($table_name){
        return $this->db()->getFields($table_name) ;
    }
    
    
}

