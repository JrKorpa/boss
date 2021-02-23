<?php
/**
 *  -------------------------------------------------
 *   @file		: AppAttributeValueModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 15:06:02
 *   @update	:
 *  -------------------------------------------------
 */
class AppAttributeValueModel extends Model
{
    public $_prefix = 'att_value';

    public $caizhi_arr = array("18K","PT950");
    public $color_arr=array("白"=>"W","黄"=>"Y","玫瑰金"=>"R","分色"=>"C","彩金"=>"H","玫瑰黄"=>"RY","玫瑰白"=>"RW","黄白"=>"YW","白黄（黄为电分色）"=>"WY");
    public $color_value_arr=array("白"=>1,"黄"=>2,"玫瑰金"=>3,"分色"=>4,"彩金"=>5,"玫瑰黄"=>6,"玫瑰白"=>7,"黄白"=>8,"白黄（黄为电分色）"=>9);

    function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_attribute_value';
        $this->_dataObject = array("att_value_id"=>"属性值ID",
            "attribute_id"=>"属性id",
            "att_value_name"=>"属性值名称",
            "att_value_status"=>"状态:1启用;0停用",
            "create_time"=>"创建时间",
            "create_user"=>"创建人",
            "att_value_remark"=>"记录备注");
		parent::__construct($id,$strConn);
	}
    
    //获取颜色对应字母，用于生成商品时
    public function getColor() {
        return $this->color_arr;
    }
    
    //获取颜色对应字母，用于生成商品时
    public function getColorValue() {
        return $this->color_value_arr;
    }
    
    //获取材质对应
    public function getCaizhi() {
        return $this->caizhi_arr;
    }
    
    /**
	 *	pageList，分页列表
	 *
	 *	@url ApplicationController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT a.*,b.attribute_name FROM `".$this->table()."` AS a LEFT JOIN app_attribute AS b ON a.attribute_id = b.attribute_id WHERE 1 ";
               
		if($where['att_value_name'] != "")
		{
			$sql .= " AND a.att_value_name like \"%".addslashes($where['att_value_name'])."%\"";
		}
		if($where['att_value_status'] != "")
		{
			$sql .= " AND a.att_value_status =".$where['att_value_status'];
		}
        if($where['attribute_id'] != ""){
            $sql .= " AND b.attribute_id=".$where['attribute_id'];
        }
		
		$sql .= " ORDER BY a.att_value_id DESC";
               // echo $sql;die;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
        
        function getAttributeValue($where){
            $sql = "SELECT att_value_id,att_value_name FROM `".$this->table()."`  WHERE 1 ";
           
            if(isset($where['att_value_id'])){
                $sql.= " AND att_value_id in (".$where['att_value_id'].")";
            }
            
            return $this->db()->getAll($sql);
        }
        
        
        /*
         * 获取属性的属性名和编码 验证是否有重复
        */
        public function getAttributeName($att_value_name,$attribute_id){
        	$sql = "SELECT `att_value_name` FROM `{$this->table()}` WHERE 1";
        	if($att_value_name!=''){
        		$sql .= " and  `att_value_name`='{$att_value_name}'";
        	}
        	if($attribute_id!=''){
        		$sql .= " and `attribute_id`='{$attribute_id}'";
        	}
        	return $this->db()->getRow($sql);
        }
        
        /*
 
        /*
         * 获取属性的属性名
        */
        public function getAttrNameByid($where){
        	$sql = "SELECT `att_value_name` FROM `{$this->table()}`";
        	if($where['att_value_id']!=''){
        		$sql .= " WHERE `att_value_id`='{$where['att_value_id']}'";
        	}
        	return $this->db()->getRow($sql);
        }
}

?>