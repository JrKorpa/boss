<?php

/**
 *  -------------------------------------------------
 *   @file		: BaseStyleInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 13:40:44
 *   @update	:
 *  -------------------------------------------------
 */
class BaseCpdzCodeModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'base_cpdz_code';
        $this->pk='id';
        $this->_prefix='';
        
        $this->_dataObject = array(
            "id"=>"ID",
            "code"=>"成品定制码",
            "style_channel_id"=>"款式来源渠道ID",
            "style_channel"=>"款式来源渠道名称",
            "order_detail_id"=>"订单商品主键ID",
            "price"=>"价格",
            "create_user"=>"创建人",
            "create_time"=>"创建时间",            
            "use_status"=>"使用状态:1未使用 2使用中 3已使用",
        );
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url ApplicationController/search
     */
    function pageList($where, $page, $pageSize = 15, $useCache = true) {
        $sql = "SELECT `this`.*,aod.goods_sn as style_sn,aod.order_id,boi.order_sn,boi.create_user as sales_user,
            `sc`.channel_name as sales_channel,aod.create_time as use_time  
            FROM `" . $this->table() . "` as `this` 
            LEFT JOIN `app_order`.`app_order_details` as `aod` ON `this`.`order_detail_id` = `aod`.`id` 
            LEFT JOIN `app_order`.`base_order_info` as `boi` ON `aod`.order_id=`boi`.id 
            LEFT JOIN `cuteframe`.`sales_channels` as `sc` on `boi`.department_id=`sc`.id 
            WHERE 1 ";
        //定制码        
        if(!empty($where['code'])){
            $sql .=" and this.code='{$where['code']}'";
        }
        //款式来源渠道
        if(!empty($where['style_channel_id'])){
            $sql .=" and this.style_channel_id={$where['style_channel_id']}";
        }
        //创建人
        if(Auth::$userType<>1){
            $sql .=" and this.create_user = '{$_SESSION['userName']}'";
        }else if(!empty($where['create_user']) && $_SESSION['userName']=="admin"){
            $sql .=" and this.create_user like '%{$where['create_user']}%'";
        }
        //使用状态
        if(!empty($where['use_status'])){
            $sql .=" and this.use_status={$where['use_status']}";
        }
        //订单号
        if(!empty($where['order_sn'])){
            $sql .=" and boi.order_sn='{$where['order_sn']}'";
        }
        //销售渠道
        if(!empty($where['sales_channel_id'])){
            $sql .=" and boi.department_id={$where['sales_channel_id']}";
        } 
        //销售顾问 
        if(!empty($where['sales_user'])){
            $sql .=" and boi.create_user='%{$where['sales_user']}%'";
        }         
        $sql .= " ORDER BY `this`.`id` DESC";
        //echo $sql;
        $data = $this->db()->getPageListNew($sql, array(), $page, $pageSize, $useCache);

        return $data;
    }

    
}

