<?php

/**
 * 商品按款定价MODEL（对接官网数据）
 *  -------------------------------------------------
 *   @file		: AppPriceByStyleModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *  -------------------------------------------------
 */
class AppGoodsPriceByStyleModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_goodsprice_by_style';
        $this->pk='id';
        $this->_prefix='';
        $this->_dataObject = array(
            "id" => "自增ID",
            "attr_keys" => "属性组合ID（md5加密）",
            "style_sn" => "款号",
            "attr_data" => '属性组合',
            "market_price" => '市场价格',
            "kela_price" => '销售价格',            
        );
        parent::__construct($id, $strConn);
    }
    /**
     * 官网按款定价分页
     * @param unknown $where
     * @param unknown $page
     * @param number $pageSize
     * @param string $useCache
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        
        $where_sql = "where 1=1";
        //是否删除
        if(isset($where['is_delete'])){
            $where_sql .= " AND a.is_delete =".(int)$where['is_delete'];
        }
        //款号搜索
        if(!empty($where['style_sn'])){
            if(is_array($where['style_sn'])){
                $where['style_sn'] = implode("','",$where['style_sn']);
                $where_sql .= " AND a.style_sn in('".$where['style_sn']."')"; 
            }else{
                $where_sql .= " AND a.style_sn ='".$where['style_sn']."'";
            }
        }
        //产品线搜索
        if(!empty($where['product_type_id'])){
           $where_sql .= " AND b.product_type =".$where['product_type_id'];
        }        
        //产品分类搜索
        if(!empty($where['cat_type_id'])){
            $where_sql .= " AND b.style_type =".$where['cat_type_id'];
        }        
        //款式性别搜索
        if(!empty($where['style_sex'])){
            $where_sql .= " AND b.style_sex =".$where['style_sex'];
        }
        
        $sql = "select * from (select a.*,group_concat(distinct a.kela_price) as all_kela_price,b.style_name,b.product_type,b.style_type,b.style_sex from ".$this->table()." a left join base_style_info b on substring_index(a.style_sn,'|',1)=b.style_sn {$where_sql} group by a.style_sn)t order by create_time desc";

        $data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
        return $data;
    }
    
    /**
     * 查询属性列表
     * @param unknown $style_sn
     */
    public function getAttrListByStyleSn($style_sn){
        $arr = explode("|",$style_sn);
        $style_sn = $arr[0];
        $sql = "SELECT	a.attribute_id,a.style_sn,a.attribute_value,a.product_type_id,a.cat_type_id,a.show_type,b.attribute_name,b.attribute_code,c.is_price_conbined,c.require_confirm
FROM rel_style_attribute a LEFT JOIN app_attribute b ON a.attribute_id = b.attribute_id
LEFT JOIN app_attribute_ext c ON b.attribute_id = c.attribute_id 
where style_sn='{$style_sn}' and b.attribute_status=1 and c.is_price_conbined=1 order by c.attribute_sort asc";
        return $this->db()->getAll($sql);
    }
    
    /**
     * 根据属性值Id获取属性
     * @param unknown $ids
     */
    public function getAttrValByIds($ids){
        $sql = "select att_value_id,att_value_name from app_attribute_value where att_value_status=1 AND att_value_id in({$ids})";
        return $this->db()->getAll($sql);
    }
    /**
     * 根据ID获取实体一行行记录
     * @param string $id
     * @param string $field
     */
    public function getRowById($id,$field="*"){
        $sql = "select {$field} from ".$this->table()." where id='{$id}'";
        return $this->db()->getRow($sql);
    }
    public function getAttrSelectBySn($style_sn){
        $sql = "select attr_select from ".$this->table()." where style_sn='{$style_sn}' order by update_time desc";
        return $this->db()->getOne($sql);
    }
    /**
     * 获取单表 列表记录
     * @param unknown $where
     * @param string $field
     * @return unknown
     */
    public function getListBySn($style_sn,$field="*"){
        $sql = "select {$field} from ".$this->table()." where style_sn='{$style_sn}' and is_delete=0 order by `status` desc";        
        return $this->db()->getAll($sql);
    }
    /**
     * 按款删除
     * @param  $style_sn
     * @param  $not_ids
     * @param  $is_delete 0 假删除 ，1真删除
     */
    public function deleteBySn($style_sn,$not_ids=array(),$is_delete=0){
        if($is_delete){
            $sql = "delete from ".$this->table()." where style_sn='{$style_sn}'";
        }else{
            $sql = "update ".$this->table()." set is_delete=1 where style_sn='{$style_sn}'";
            $del_sql = "delete from ".$this->table()." where style_sn='{$style_sn}' and kela_price<=0";
            if(!empty($not_ids)){
               $del_sql .=" AND id not in('".implode("','",$not_ids)."')";
            }
            $this->db()->query($del_sql);
        }
        if(!empty($not_ids)){
            $sql .=" AND id not in('".implode("','",$not_ids)."')";
        }
        return $this->db()->query($sql);
    }
    
    /**
     * 按款还原
     * @param  $style_sn
     * @param  $not_ids
     */
    public function returnBySn($style_sn,$ids=array()){
    	if(empty($ids)){
    		return false;
    	}
    	$sql = "update ".$this->table()." set is_delete=0 where style_sn='{$style_sn}' AND id in('".implode("','",$ids)."')";   	
    	return $this->db()->query($sql);
    }
    //检查按款定价记录是否重复
    public function checkExistsById($id){
        $sql = "select count(*) from ".$this->table()." where id='{$id}' and is_delete=0";
        return $this->db()->getOne($sql);
    }
    //检查按款定价记录是否重复
    public function checkExistsDelById($id){
    	$sql = "select count(*) from ".$this->table()." where id='{$id}' and is_delete=1";
    	return $this->db()->getOne($sql);
    }
    public function updateAttrSelectBySn($attr_select,$style_sn){
        $time = date("Y-m-d H:i:s");
        $sql = "update ".$this->table()." set attr_select='{$attr_select}',update_time='{$time}' where style_sn='{$style_sn}'";
        return $this->db()->query($sql);
    }
    public function updateIsPriceConbined($style_sn,$attr_ids){
        $arr = explode("|",$style_sn);
        $style_sn_split = implode("','",$arr);
        //重置is_price_conbined
        $reset_sql = "update rel_style_attribute set is_price_conbined=0 where style_sn in('{$style_sn_split}')";
        
        $this->db()->query($reset_sql);
        
        $sql = "update rel_style_attribute set is_price_conbined=1 where style_sn in('{$style_sn_split}')";
        if(is_array($attr_ids)){
            $sql .=" and attribute_id in(".implode(",",$attr_ids).")";
        }else{
            $sql .=" and attribute_id={$attr_ids}";
        }
        return $this->db()->query($sql); 
    }
    /**
     * 批量插入
     * @see Model::insertAll()
     */
    public function replaceIntoAll($dataAll){
        foreach ($dataAll as $data){
            $fields = array_keys($data);
            $valuedata = array_values($data);
    	    $field = implode('`,`', $fields);
    	    $value = implode("','",$valuedata);
    	    $value = str_replace('\\','\\\\',$value);
            $sql = "REPLACE INTO `".$this->table()."` (`" . $field . "`) VALUES ('". $value ."')";
            $this->db()->query($sql);            
        }
    }
    
    
}
