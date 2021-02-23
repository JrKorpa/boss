<?php
/**
 *  -------------------------------------------------
 *   @file		: RelStyleAttributeModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-11 19:34:35
 *   @update	:
 *  -------------------------------------------------
 */
class RelStyleAttributeModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'rel_style_attribute';
		$this->pk='rel_id';
		$this->_prefix='';
        $this->_dataObject = array("rel_id"=>" ",
"cat_type_id"=>"分类名称id",
"style_id"=>"款式id",
"style_sn"=>"款号",
"cat_id"=>"款式id",
"product_type_id"=>"产品线id",
"attribute_id"=>"属性id",
"attribute_value"=>"属性值",
"show_type"=>"1=>'文本框',2=>'单选',3=>'多选',4=>'下拉列表'",
"create_time"=>"创建时间",
"create_user"=>"创建人",
"info"=>"备注");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url RelStyleAttributeController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
       
        if(isset($where['cat_type_id']) && !empty($where['cat_type_id'])){
            $sql.= " AND `cat_type_id`=".$where['cat_type_id'];
        }
        if(isset($where['product_type_id']) && !empty($where['product_type_id'])){
            $sql.= " AND `product_type_id`=".$where['product_type_id'];
        }
        if(isset($where['attribute_id']) && !empty($where['attribute_id'])){
            $sql.= " AND `attribute_id`=".$where['attribute_id'];
        }
        if(isset($where['style_sn']) && !empty($where['style_sn'])){
            $sql.= " AND `style_sn` LIKE '".$where['style_sn']."%'";
        }
        if(isset($where['style_id']) && !empty($where['style_id'])){
            $sql.= " AND `style_id` =".$where['style_id'];
        }
            
		$sql .= " ORDER BY `rel_id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
        
        /*
         * 获取款对应的属性
         */
        public function getList($where){
            $sql ="SELECT * FROM `".$this->table()."`  WHERE 1 ";
            if(isset($where['style_id'])){
                $sql.=" AND style_id='".$where['style_id']."'";
            }
            
            if(isset($where['attribute_id'])){
                 $sql.=" AND attribute_id='".$where['attribute_id']."'";
            }
            
            if(isset($where['product_type_id'])){
                 $sql.=" AND product_type_id='".$where['product_type_id']."'";
            }
            
            if(isset($where['cat_type_id'])){
                 $sql.=" AND cat_type_id='".$where['cat_type_id']."'";
            }
            
            if(isset($where['show_type'])){
                $sql.=" AND show_type='".$where['show_type']."'";
            }
            
            return $this->db()->getAll($sql);
        }
        
        public function deleteStyleAttribute($id) {
            $sql = "DELETE FROM `".$this->table()."` WHERE `rel_id`=".$id;
            return $this->db()->query($sql);
        }
        
        /*
         * 根据款号查询属性信息是否存在
        */
        public function getInfoByStyle_sn($style_id){
        	$sql = "SELECT `style_id` FROM `{$this->table()}`";
        	if($style_id!=''){
        		$sql .= " WHERE `style_id`={$style_id}";
        	}
			//echo $sql;exit;
        	return $this->db()->getAll($sql);
        }
        
        /*
         * 获取款式的属性
         */
        public function getStyleAttributeByStyleId($where) {
            $str = "";
            if(isset($where['style_id'])){
                $str .=" AND `style_id` =".$where['style_id'];
            }

            if(isset($where['style_sn'])){
                $str .=" AND `style_sn` = '".$where['style_sn']."'";
            }
            
            if(isset($where['attribute_id'])){
                $str .=" AND `attribute_id` =".$where['attribute_id'];
            }
            
            $sql = "SELECT `attribute_value`,`product_type_id`,`cat_type_id` FROM `rel_style_attribute` WHERE 1 ".$str ;
            return $this->db()->getRow($sql);
        }

        /*
        *
        */
        public function deleteXiangkou($style_sn,$attribute_value){            
            if(!empty($attribute_value)){
                $value_list = explode(',',trim($attribute_value,','));
                if(is_array($value_list)){
                    $sql = "delete from app_xiangkou where style_sn='{$style_sn}' and finger in ( select att_value_name from app_attribute_value where attribute_id='5' and att_value_id in (".implode(',',$value_list)."))";
                    //echo $sql;
                    $this->db()->query($sql);
                }
            }
        }
        
}

?>