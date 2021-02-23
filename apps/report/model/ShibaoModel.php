<?php
/**
 * 供应商API数据模型（代替Proccesor/Api/api.php）
 *  -------------------------------------------------
 *   @file      : ProccesorModel.php
 *   @link      :  www.kela.cn
 *   @copyright : 2014-2024 kela Inc
 *   @author    : Laipiyang <462166282@qq.com>
 *   @date      : 2015-02-10 15:34:30
 *   @update    :
 *  -------------------------------------------------
 */
class ShibaoModel extends SelfModel
{
    protected $db;
    function __construct ($strConn="")
    {
        parent::__construct($strConn);
    } 
    /**
     * 供应商石包汇总 SQL
     * @param unknown $where
     * @return string
     */
    public function getListStoneProcessorSql($where){
        $where_str = "b.bill_type='YSD' and b.status<>3";
        //供应商
        if(!empty($where['processors_id'])){
            $where_str .=" AND b.processors_id={$where['processors_id']}";
        }
        //工厂
        if(!empty($where['factory_id'])){
            $where_str .=" AND b.factory_id={$where['factory_id']}";
        }
        //创建时间
        if(!empty($where['create_time_min'])){
            $where_str .=" AND b.create_time >='{$where['create_time_min']}'";
        }
        if(!empty($where['create_time_max'])){
            $where_str .=" AND b.create_time <='{$where['create_time_max']} 23:59:59'";
        }
        //审核时间
        if(!empty($where['check_time_min'])){
            $where_str .=" AND b.check_time >='{$where['check_time_min']}'";
        }
        if(!empty($where['check_time_max'])){
            $where_str .=" AND b.check_time <='{$where['check_time_max']} 23:59:59'";
        }
        //状态
        if(!empty($where['status'])){
            $where_str .=" AND b.status ={$where['status']}";
        }
        
        $sql = "SELECT b.factory_id,b.factory_name,b.processors_id,b.processors_name,bd.dia_package, sum(bd.num) as total_num,sum(bd.weight) as total_weight,sum(bd.price) as total_price
FROM stone_bill b INNER JOIN stone_bill_details bd ON b.id = bd.bill_id where {$where_str} GROUP BY b.processors_id,bd.dia_package";
        //echo  $sql;
        return $sql;
    }
    /**
     * 供应商石包汇总 分页查询
     * @param unknown $where
     * @param unknown $page
     * @param number $pageSize
     * @param string $useCache
     * @return unknown
     */
    public function pageListStoneProcessor($where,$page,$pageSize=10,$useCache=true){
        $sql = $this->getListStoneProcessorSql($where);
        $data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
        return $data;
    }
     
    /**
     * 工厂石包汇总 sql
     * @param unknown $where
     * @return string
     */  
    public function getListStoneFactorySql($where){
        $where_str = "b.bill_type='YSD' and b.status<>3";
        //供应商
        if(!empty($where['processors_id'])){
            $where_str .=" AND b.processors_id={$where['processors_id']}";
        }
        //工厂
        if(!empty($where['factory_id'])){
            $where_str .=" AND b.factory_id={$where['factory_id']}";
        }
        //创建时间
        if(!empty($where['create_time_min'])){
            $where_str .=" AND b.create_time >='{$where['create_time_min']}'";
        }
        if(!empty($where['create_time_max'])){
            $where_str .=" AND b.create_time <='{$where['create_time_max']} 23:59:59'";
        }
        //审核时间
        if(!empty($where['check_time_min'])){
            $where_str .=" AND b.check_time >='{$where['check_time_min']}'";
        }
        if(!empty($where['check_time_max'])){
            $where_str .=" AND b.check_time <='{$where['check_time_max']} 23:59:59'";
        }
        //状态
        if(!empty($where['status'])){
            $where_str .=" AND b.status ={$where['status']}";
        }

        $sql = "SELECT b.factory_id,b.factory_name,b.processors_id,b.processors_name,bd.dia_package, sum(bd.num) as total_num,sum(bd.weight) as total_weight,sum(bd.price) as total_price FROM 
	stone_bill b INNER JOIN stone_bill_details bd ON b.id = bd.bill_id where {$where_str} GROUP BY b.factory_id,b.processors_id,bd.dia_package";
        //echo $sql;
        return $sql;
    }
    /**
     * 工厂石包汇总 分页查询
     * @param unknown $where
     * @param unknown $page
     * @param number $pageSize
     * @param string $useCache
     * @return unknown
     */
    public function pageListStoneFactory($where,$page,$pageSize=10,$useCache=true){       
        $sql = $this->getListStoneFactorySql($where);
        $data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
        return $data;
    }
    
    /**
     * 用石明细列表SQL
     * @param unknown $where
     * @return string
     */
    public function getListStoneUseSql($where){
        
     $where_str = "b.bill_type='YSD' and b.status<>3";
        //供应商
        if(!empty($where['processors_id'])){
            $where_str .=" AND b.processors_id={$where['processors_id']}";
        }
        //工厂
        if(!empty($where['factory_id'])){
            $where_str .=" AND b.factory_id={$where['factory_id']}";
        }
        //创建时间
        if(!empty($where['create_time_min'])){
            $where_str .=" AND b.create_time >='{$where['create_time_min']}'";
        }
        if(!empty($where['create_time_max'])){
            $where_str .=" AND b.create_time <='{$where['create_time_max']} 23:59:59'";
        }
        //审核时间
        if(!empty($where['check_time_min'])){
            $where_str .=" AND b.check_time >='{$where['check_time_min']}'";
        }
        if(!empty($where['check_time_max'])){
            $where_str .=" AND b.check_time <='{$where['check_time_max']} 23:59:59'";
        }
        //状态
        if(!empty($where['status'])){
            $where_str .=" AND b.status ={$where['status']}";
        }      
        $sql = "SELECT bd.bill_id,b.bill_no, b.create_user,b.create_time, b.check_user,  b.check_time, b.`status`,  b.factory_id, b.factory_name,	b.processors_id,b.processors_name,	bd.dia_package, b.paper_no,   bd.num, bd.weight, bd.purchase_price,  bd.price,
  bd.specification,  b.remark  FROM	stone_bill b INNER JOIN stone_bill_details bd ON b.id = bd.bill_id where {$where_str} order by b.id desc";        
        return $sql;
    }
    /**
     * 用石明细分页查询
     * @param unknown $where
     * @param unknown $page
     * @param number $pageSize
     * @param string $useCache
     * @return unknown
     */
    public function pageListStoneUse($where,$page,$pageSize=10,$useCache=true){
        $sql = $this->getListStoneUseSql($where);
        $data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
        return $data;
    }    
    
}

?>