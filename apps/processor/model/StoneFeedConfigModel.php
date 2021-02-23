<?php

/**
 *  裸石供料配置
 *  -------------------------------------------------
 *   @file		: StoneFeedConfigModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @date		: 2017-06-12
 *   @update	:
 *  -------------------------------------------------
 */
class StoneFeedConfigModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'stone_feed_config';
        $this->pk = 'id';
        $this->_prefix = '';
        $this->_dataObject = array(
            "id" => "ID",
            'color'=>'颜色',
            'clarity'=>'净度',
            'cert'=>'证书类型',
            'carat_min'=>'石重下限（最小值）',
            'carat_max'=>'石重上限(最大值)',
            'factory_id'=>'工厂ID',
            'factory_name'=>'工厂名称',
            'feed_type'=>'供料类型 ',
            'prority_sort'=>'优先级排序',
            'create_time'=>'添加时间',
            'create_user'=>'添加人',
            'is_enable'=>'是否可用',            
        );
        parent::__construct($id, $strConn);
    }
    /**
     * 更新优先级排序
     * @param unknown $factory_id
     */
   /*  function updateProritySort($id,$prority_sort,$factory_id){
        $result = array('success'=>0,'msg'=>'');
        try{
            $time = date('Y-m-d H:i:s');
            $sql = "update ".$this->table()." set prority_sort={$prority_sort},modify_time={$time} where id={$id}";
            $this->db()->query($sql);
            $sql = "update stone_feed_config a INNER JOIN (SELECT id,@y :=@y + 1 AS prority_sort FROM stone_feed_config a2,(SELECT @y := 0) b2 WHERE	factory_id={$factory_id} ORDER BY prority_sort asc,modify_time DESC) b on a.id=b.id set a.prority_sort=b.prority_sort where a.pro";                  
            $this->db()->query($sql);
            $result['success'] = 1;            
            return $result;
        }catch (Exception $e){
            $result['success'] = 0;
            $result['msg'] = $e->getMessage();
            return $result;
        }        
    } */
    /**
     * 检查优先级排序是否冲突
     * @param unknown $factory_id
     * @param unknown $prority_sort
     * @param number $id
     */
    function checkProritySort($factory_id,$prority_sort,$id=0){
        $sql = "select count(*) from ".$this->table()." where factory_id={$factory_id} and prority_sort={$prority_sort}";
        if(!empty($id)){
            $sql .=" AND id<>{$id}";            
        }
        return $this->db()->getOne($sql);
    }
    /**
     * 获取优先级
     * @param unknown $id
     * @param unknown $factory_id
     */
    function getNewProritySort($factory_id){
        $sql = "select max(prority_sort) from ".$this->table()." where factory_id={$factory_id}";
        return $this->db()->getOne($sql)+1;
    }
    /**
     * 批量更新状态
     * @param unknown $ids
     * @param unknown $is_enable
     * @return boolean
     */
    public function updateIsEnable($ids,$is_enable){
        if(empty($ids) || !in_array($is_enable,array(0,1))){
            return false;
        }
        if(is_array($ids)){
           $ids = implode(",",$ids);
        }
        $sql = "update ".$this->table()." set is_enable={$is_enable} where id in($ids)";
        return $this->db()->query($sql);
    }
    /**
     * 	pageList，分页列表
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {

        $sql = "select * from ".$this->table()." where ";
        $str = "1=1";
        $orderby = " order by id desc";
        if(!empty($where['factory_id'])){
            $str .= " AND factory_id={$where['factory_id']}";
            $orderby = " order by prority_sort asc";
        }
        if(isset($where['is_enable']) && $where['is_enable']<>''){
            $str .= " AND is_enable={$where['is_enable']}";
        }
        if(!empty($where['feed_type'])){
            $str .= " AND feed_type={$where['feed_type']}";
        }
        if(!empty($where['cert'])){
            $str .= " AND cert='{$where['cert']}'";
        }
        if(!empty($where['color'])){
            $str .= " AND color='{$where['color']}'";
        }
        if(!empty($where['clarity'])){
            $str .= " AND clarity='{$where['clarity']}'";
        }
        if(!empty($where['carat_min'])){
            $str .= " AND carat_min>={$where['carat_min']}";
        }
        if(!empty($where['carat_max'])){
            $str .= " AND carat_max<={$where['carat_max']}";
        }
        if(!empty($where['create_time_begin'])){
            $str .= " AND create_time>='{$where['create_time_begin']}'";
        }
        if(!empty($where['create_time_end'])){
            $str .= " AND create_time<='{$where['create_time_end']} 23:59:59'";
        }
        $sql = $sql.$str.$orderby;
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }
    
   

}

?>