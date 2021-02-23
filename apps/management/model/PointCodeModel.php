<?php
/**
 * Created by PhpStorm.
 * User: liaoweixian
 * Date: 2018/6/11
 * Time: 15:34
 */

class PointCodeModel extends Model
{
    function __construct ($id=NULL,$strConn="")
    {
        $this->_objName = 'point_code';
        $this->pk='id';
        $this->_prefix='';
        $this->_dataObject = array(
            "id"=>"序号",
            'channel_id'=>'渠道id',
            'channel_name'=>'渠道',
            'point_code'=>'积分码',
            'use_proportion'=>'使用比例',
            'status'=>'状态',
            'order_sn'=>'订单号',
            'use_people_name'=>'使用人',
            'created_name'=>'创建人',
            'create_time'=>'创建时间',
            'update_time'=>'更新时间',
            ''=>'',
        );
        parent::__construct($id,$strConn);
    }

    function pageList ($where,$page=1,$pageSize=10,$useCache=true)
    {
        $sql = "SELECT * FROM `".$this->table()."` where 1 = 1 ";
        if(!empty($where['channel_id'])){
            $sql .= " AND channel_id = '{$where['channel_id']}'";
        }
        if(!empty($where['status'])){
            $sql .= " AND status = '{$where['status']}'";
        }
        if(!empty($where['point_code'])){
            $sql .= " AND point_code = '{$where['point_code']}'";
        }
        if(!empty($where['use_proportion'])){
            $sql .= " AND use_proportion = '{$where['use_proportion']}'";
        }
        if(!empty($where['order_sn'])){
            $sql .= " AND order_sn = '{$where['order_sn']}'";
        }
        if(!empty($where['use_people_name'])){
            $sql .= " AND use_people_name = '{$where['use_people_name']}'";
        }
        if(!empty($where['created_name'])){
            $sql .= " AND created_name = '{$where['created_name']}'";
        }
        if(!empty($where['channel_limit'])){            
            $sql .= " AND channel_id in (". implode(',',$where['channel_limit']) .")";
        }                                                

        $sql .= " ORDER BY id DESC";
        //echo $sql;
        $data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
        return $data;
    }

    function getCount($where){
        $sql = "SELECT count(*) FROM `".$this->table()."` where 1 = 1 ";
        if(!empty($where['point_code'])){
            $sql .= " AND point_code = '".$where['point_code']."'";
        }
        return $this->db()->getOne($sql);
    }

}