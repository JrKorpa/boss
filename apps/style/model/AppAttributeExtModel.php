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
class AppAttributeExtModel extends Model
{
    public $_prefix = 'attribute';
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_attribute_ext';
        $this->_dataObject = array("attribute_id"=>"属性id",
"attr_show_name"=>"属性显示名称",
"is_diamond_attr"=>"是否是钻石属性",
"require_confirm"=>"是否需要用户确认");
                
		parent::__construct($id,$strConn);
	}

    
    public function getAttributeExt($id){
        $sql = "select * from app_attribute_ext where attribute_id={$id}";
        return $this->db()->getRow($sql);
    }
    
    public function insert($data){
        $keys = array_keys($data);
        $sql = "insert into ".$this->table()."(`".implode("`,`",$keys)."`) values('".implode("','",$data)."')";
        return $this->db()->query($sql);
    }
    
  
}

?>