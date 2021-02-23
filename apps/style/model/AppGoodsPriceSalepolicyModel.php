<?php

/**
 * 商品按款定价MODEL（对接官网数据）
 *  -------------------------------------------------
 *   @file		: AppPriceByStyleModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *  -------------------------------------------------
 */
class AppGoodsPriceSalepolicyModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_goodsprice_salepolicy';
        $this->_dataObject = array(
            "id" => "自增ID",
            "style_sn" => "款号",
            "channel_id" => "渠道ID",
            "jiajialv" => '加价率',
            "sta_value" => '固定值',
            "create_time" => '创建时间'          
        );
        parent::__construct($id, $strConn);
    }
    /**
     * 分页查询
     * @param unknown $where
     * @param unknown $page
     * @param number $pageSize
     * @param string $useCache
     */
    public function pageList($where, $page, $pageSize = 10, $useCache = true){
        
        $where_sql = "where 1=1";
        if(isset($where['is_delete'])){
            $where_sql .=" AND is_delete=".(int)$where['is_delete'];
        }
        if(!empty($where['channel_id'])){
            $where_sql .=" AND channel_id={$where['channel_id']}";
        }
        
        if(!empty($where['style_sn'])){
            if(is_array($where['style_sn'])){
                $where_sql .=" AND style_sn in ('".implode("','",$where['style_sn'])."')";
            }else{
                $where_sql .=" AND style_sn='{$where['style_sn']}'";
            }
        }
        
        $sql = "select * from ".$this->table()." ".$where_sql." order by id desc";
        $data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
        return $data;
    }
    /**
     * 根据款号获取政策列表
     * @param unknown $style_sn
     */
    public function getListBySn($style_sn,$style_id=null){
    	if($style_id != null){
    		$where=" and s.style_id='{$style_id}'";
    	}else{
    		$where='';
    	}
        $sql = "select s.*,b.attr_data from ".$this->table()." as s left join app_goodsprice_by_style as b on s.style_id=b.id where s.style_sn='{$style_sn}'".$where." and s.is_delete=0";
        return $this->db()->getAll($sql);
    }
    
    
    /**
     * 根据款号获取政策列表(分页)
     * @param unknown $style_sn
     */
    public function getListPageBySn($where, $page, $pageSize = 10, $useCache = true){
    	$where_sql = "where 1=1";
    	if(isset($where['is_delete'])){
    		$where_sql .=" AND s.is_delete=".(int)$where['is_delete'];
    	}
    	if(!empty($where['style_sn'])){
    		$where_sql .=" AND s.style_sn='{$where['style_sn']}'";
    	}
    	if(!empty($where['style_id'])){
    		$where_sql .=" AND s.style_id='{$where['style_id']}'";
    	}
		if(!empty($where['channel_id'])){
    		$where_sql .=" AND s.channel_id='{$where['channel_id']}'";
    	}
    	
    	
    	$sql = "select s.*,b.attr_data,b.kela_price from ".$this->table()." as s inner join app_goodsprice_by_style as b on s.style_id=b.id {$where_sql} order by s.update_time desc";
    	$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
        return $data;
    }
    /**
     * 检查销售政策是否重复
     * @param unknown $style_sn
     * @param unknown $channel_id
     */
    public function checkExists($style_sn,$channel_id){
        $sql = "select count(*) from ".$this->table()." where style_sn='{$style_sn}' and channel_id={$channel_id} and is_delete=0";
        return $this->db()->getOne($sql);
    }
    /**
     * 删除
     * @param $id
     * @param $is_delete 0 假删除，1真删除
     */
    public function deleteById($id,$is_delete=0){
        if($is_delete){
           $sql = "delete from ".$this->table()." where ";
        }else{
            $sql = "update ".$this->table()." set is_delete=1 where ";
        }
        if(is_array($id)){
            $sql .="id in(".implode(",",$id).")";
        }else{
            $sql .="id =".$id."";
        }
        return $this->db()->query($sql);
    }
    /**
     * 删除
     * @param $id
     * @param $is_delete 0 假删除，1真删除
     */
    public function deleteByStyleSn($style_sn,$is_delete=0){
        if($is_delete){
            $sql = "delete from ".$this->table()." where ";
        }else{
            $sql = "update ".$this->table()." set is_delete=1 where ";
        }
        $sql .="style_sn ='{$style_sn}'";
        return $this->db()->query($sql);
    }
    
    /**
     * 检查销售政策是否重复
     * @param unknown $style_id
     * @param unknown $channel_id
     */
    public function checkExistsss($style_id,$channel_id){
    	$sql = "select count(*) from ".$this->table()." where style_id='{$style_id}' and channel_id={$channel_id}";
    	return $this->db()->getOne($sql);
    }
    
    /**
     * 插入销售政策，有的就更新（根据$style_id,$channel_id判断）
     * @param unknown $style_sn
     * @param unknown $channel_id
     */
    public function updateSalepolicy($params){    	
    	$style_ids = $params['style_ids'];
    	$style_sn = $params['style_sn'];
    	$channel_ids = $params['channel_ids'];
    	$jiajialv = $params['jiajialv'];
    	$sta_value = $params['sta_value'];
    	$time = date("Y-m-d H:i:s",time());
    	foreach ($style_ids as $style_id){
        	foreach ($channel_ids as $channel_id){
        		$sql = "select count(*) from app_goodsprice_salepolicy where style_id='{$style_id}' and channel_id={$channel_id}";
        		$exits = $this->db()->getOne($sql);
        		if(!$exits){
        			$sql="insert into `app_goodsprice_salepolicy` (`style_sn`,`style_id`,`channel_id`,`jiajialv`,`sta_value`,`create_time`) values('{$style_sn}','{$style_id}',{$channel_id},{$jiajialv},{$sta_value},'{$time}')";
        		    $this->db()->query($sql);
        		}else{
        			 $sql="update `app_goodsprice_salepolicy` set `jiajialv`={$jiajialv},`sta_value`={$sta_value},`update_time`='{$time}',`is_delete`=0 where `style_id`='{$style_id}' and `channel_id`={$channel_id}";
        			 $this->db()->query($sql);
        		}
        	}
    	}
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
    	}
    	if(!empty($not_ids)){
    		$sql .=" AND style_id not in('".implode("','",$not_ids)."')";
    	}
    	return $this->db()->query($sql);
    }
    
}
