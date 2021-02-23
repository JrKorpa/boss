<?php

/**
 *  -------------------------------------------------
 *   @file		: AppCouponLogModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-11 16:52:55
 *   @update	:
 *  -------------------------------------------------
 */
class AppGoodsListModel extends Model {

    /**
     * 	pageList，分页列表
     *
     * 	@url AppCouponTypeController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        //不要用*,修改为具体字段
        $sql = "SELECT 
        `g`.`goods_sn`,
        `g`.`goods_id`,
        `g`.`is_on_sale`,
        `g`.`company`,
        `cs`.`channel_name`,
        `od`.`goods_price`
         FROM `warehouse_shipping`.`warehouse_goods` `g` 
         left join `front`.`base_style_info` `bsi` on `g`.`goods_sn` = `bsi`.`style_sn`
         left join `app_order`.`app_order_details` `od` on `g`.`order_goods_id` = `od`.id 
         left join `app_order`.`base_order_info` `oi` on `od`.`order_id` = `oi`.`id` 
         left join `cuteframe`.`sales_channels` `cs` on `cs`.`id` = `oi`.`department_id`";
        $str = '';
        if($where['goods_sn'] != "")
        {
            $str .= "`g`.`goods_sn` = '".$where['goods_sn']."' AND ";
        }
		/*if($where['exchange_name'] != "")
		{
			$str .= "`exchange_name` like \"%".addslashes($where['exchange_name'])."%\" AND ";
		}
        if(!empty($where['time_start']))
        {
            $str.="`exchange_time` >= '".$where['time_start']." 00:00:00' AND ";
        }
        if(!empty($where['time_end']))
        {
            $str.="`exchange_time` <= '".$where['time_end']." 23:59:59' AND ";
        }*/
		if(!empty($where['goods_status']))
		{
			$str .= "`g`.`is_on_sale`=".$where['goods_status']." AND ";
		}
        if(!empty($where['xilie']))
        {
            $str .= "`bsi`.`xilie` in(".$where['xilie'].") AND ";
        }
        //$str .= "`g`.`is_on_sale` = 3 AND ";
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE " . $str;
        }
        $sql .= " ORDER BY `g`.`goods_id` DESC";
        //echo $sql;die;
        $data = $this->db()->getPageListNew($sql, array(), $page, $pageSize, $useCache);
        //echo '<pre>';
        //print_r($data);die;
        return $data;
    }


    /**
     *  getVisitByUserNmae，取权限
     *
     */
    public function getGoodsStatusById($goods_id)
    {
        if(!$goods_id){
            return false;
        }
        $sql = "SELECT `id` FROM `warehouse_shipping`.`warehouse_goods` `g` WHERE `g`.`goods_id` = {$goods_id}";
        //echo $sql;die;
        # code...
        return $this->db()->getOne($sql);
    }

    /**
     *  getGoodsInfoById //取出货品数据
     *
     */
    public function getGoodsInfoById($goods_id)
    {
        $sql = "SELECT `g`.`goods_id`,
        `g`.`goods_sn`,
        `g`.`prc_name`,
        `g`.`warehouse`,
        `g`.`is_on_sale`,
        `g`.`mingyichengben`,
        `g`.`caigou_chengbenjia`,
        `g`.`chengbenjia`,
        `g`.`caizhi`,
        `g`.`jinzhong`,
        `g`.`shoucun`,
        `g`.`zhushi`,
        `g`.`zuanshidaxiao`,
        `g`.`zhushiyanse`,
        `g`.`zhushijingdu`,
        `g`.`zhengshuhao`,
        `g`.`zhengshuhao2`,
        `g`.`zhengshuleibie`,
        `g`.`box_sn`,
        `g`.`pinpai`,
        `g`.`supplier_code`,
        `bd`.`bill_no`,
        `bd`.`bill_type`,
        `oi`.`create_time`,
        `oi`.`order_sn`,
        (`od`.`goods_price` - IF(`od`.`favorable_price`,`od`.`favorable_price`,0.00)) as `goods_price`
        FROM `warehouse_shipping`.`warehouse_goods` `g`
        left join `warehouse_shipping`.`warehouse_bill_goods` `bd` on `g`.`goods_id` = `bd`.`goods_id`  
        left join `app_order`.`app_order_details` `od` on `od`.`id` = `g`.`order_goods_id` 
        left join `app_order`.`base_order_info` `oi` on `oi`.`id` = `od`.`order_id`
        WHERE `g`.`goods_id` = {$goods_id} ORDER BY `bd`.`addtime` DESC";
        return $this->db()->getRow($sql);
    }

    /**
     *  getGoodsInfoById //取出货品数据
     *
     */
    public function getAllGoodsInfoById($goods_id)
    {
       $sql = "SELECT `g`.`goods_id`,
        `g`.`goods_sn`,
        `g`.`prc_name`,
        `g`.`warehouse`,
        `g`.`goods_name`,
        `g`.`is_on_sale`,
        `g`.`mingyichengben`,
        `g`.`caigou_chengbenjia`,
        `g`.`chengbenjia`,
        `g`.`caizhi`,
        `g`.`jinzhong`,
        `g`.`shoucun`,
        `g`.`zhushi`,
        `g`.`zuanshidaxiao`,
        `g`.`zhushiyanse`,
        `g`.`zhushijingdu`,
        `g`.`zhengshuhao`,
        `g`.`zhengshuhao2`,
        `g`.`zhengshuleibie`,
        `g`.`box_sn`,
        `g`.`pinpai`,
        `g`.`supplier_code`,
        `bd`.`bill_no`,
        `bd`.`bill_type`,
        `oi`.`create_time`,
        `oi`.`order_sn`,
        (`od`.`goods_price` - IF(`od`.`favorable_price`,`od`.`favorable_price`,0.00)) as `goods_price`
        FROM `warehouse_shipping`.`warehouse_goods` `g`
        left join `warehouse_shipping`.`warehouse_bill_goods` `bd` on `g`.`goods_id` = `bd`.`goods_id`  
        left join `app_order`.`app_order_details` `od` on `od`.`id` = `g`.`order_goods_id` 
        left join `app_order`.`base_order_info` `oi` on `oi`.`id` = `od`.`order_id`
        WHERE `g`.`goods_id` IN ('{$goods_id}') ORDER BY `bd`.`addtime` DESC";
        //echo $sql;die;
        return $this->db()->getAll($sql);
    }

}

?>