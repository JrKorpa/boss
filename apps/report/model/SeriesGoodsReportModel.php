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
class SeriesGoodsReportModel extends Model {

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
    `wbg`.`shijia` AS xiaoshoujia,
    `wb`.`check_time`,
    `cs`.`channel_name`
 FROM `warehouse_shipping`.`warehouse_goods` `g` 
 left join `warehouse_shipping`.`warehouse_bill_goods` `wbg` on `g`.`goods_id` = `wbg`.`goods_id` 
 inner join `warehouse_shipping`.`warehouse_bill` `wb` on `wbg`.`bill_id` = `wb`.`id` and `wb`.`bill_type` = 'S' and `wb`.`bill_status` = 2
 left join `app_order`.`app_order_details` `od` on `od`.`id` = `g`.`order_goods_id`
 inner join `app_order`.`base_order_info` `oi` on `od`.`order_id` = `oi`.`id` 
 inner join `cuteframe`.`sales_channels` `cs` on `cs`.`id` = `oi`.`department_id`
 left join `front`.`base_style_info` `bsi` on `g`.`goods_sn` = `bsi`.`style_sn`";

        $tmp_sql = "SELECT SUM(`wbg`.`shijia`)
 FROM `warehouse_shipping`.`warehouse_goods` `g` 
 left join `warehouse_shipping`.`warehouse_bill_goods` `wbg` on `g`.`goods_id` = `wbg`.`goods_id` 
 inner join `warehouse_shipping`.`warehouse_bill` `wb` on `wbg`.`bill_id` = `wb`.`id` and `wb`.`bill_type` = 'S' and `wb`.`bill_status` = 2
 left join `app_order`.`app_order_details` `od` on `od`.`id` = `g`.`order_goods_id`
 inner join `app_order`.`base_order_info` `oi` on `od`.`order_id` = `oi`.`id` 
 inner join `cuteframe`.`sales_channels` `cs` on `cs`.`id` = `oi`.`department_id`
 left join `front`.`base_style_info` `bsi` on `g`.`goods_sn` = `bsi`.`style_sn`";
        $str = '';
        $tmp = '';
        if($where['goods_sn'] != "")
        {
            $str .= "`g`.`goods_sn` = '".$where['goods_sn']."' AND ";
            $tmp .= "`g`.`goods_sn` = '".$where['goods_sn']."' AND ";
        }
		/*if($where['exchange_name'] != "")
		{
			$str .= "`exchange_name` like \"%".addslashes($where['exchange_name'])."%\" AND ";
		}*/
        if(!empty($where['time_start']))
        {
            $str.="`wb`.`check_time` >= '".$where['time_start']." 00:00:00' AND ";
            $tmp.="`wb`.`check_time` >= '".$where['time_start']." 00:00:00' AND ";
        }
        if(!empty($where['time_end']))
        {
            $str.="`wb`.`check_time` <= '".$where['time_end']." 23:59:59' AND ";
            $tmp.="`wb`.`check_time` <= '".$where['time_end']." 23:59:59' AND ";
        }
		if(!empty($where['goods_status']))
		{
			$str .= "`g`.`is_on_sale`= ".$where['goods_status']." AND ";
            $tmp .= "`g`.`is_on_sale`= ".$where['goods_status']." AND ";
		}
        if(isset($where['xilie']) && !empty($where['xilie'])){
            if(count($where['xilie'])==1){
                 $str.= "`bsi`.`xilie` like '%,".$where['xilie'][0].",%' AND ";
                 $tmp.= "`bsi`.`xilie` like '%,".$where['xilie'][0].",%' AND ";
            }else{
                $str_s = "";
                $tmp_s = "";
                foreach ($where['xilie'] as $val){
                     $str_s.="`bsi`.`xilie` like '%,".$val.",%' or ";
                     $tmp_s.="`bsi`.`xilie` like '%,".$val.",%' or ";
                }
                $str_s = rtrim($str_s," or");
                $tmp_s = rtrim($str_s," or");
                $str .= "(".$str_s.") AND ";
                $tmp .= "(".$str_s.") AND ";
               
            }
        }
        //$str .= "`g`.`is_on_sale` = 3 AND ";
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE " . $str;
        }
        if ($tmp) {
            $tmp = rtrim($tmp, "AND "); //这个空格很重要
            $tmp_sql .=" WHERE " . $tmp;
        }
        $sql .= " ORDER BY `g`.`goods_id` DESC";
        //echo $tmp_sql;die;
        $num_price = $this->db()->getOne($tmp_sql);
        $data = $this->db()->getPageListNew($sql, array(), $page, $pageSize, $useCache);
        $data['num_price'] = $num_price;
        return $data;
    }


    /**
     *  getVisitByUserNmae，取权限
     *
     */
    public function getVisitByUserNmae($user_name)
    {
        if(!$user_name){
            return false;
        }
        $sql = "SELECT `xilie` FROM `front`.`app_xilie_config` WHERE `user_name` = '{$user_name}'";
        //echo $sql;die;
        # code...
        return $this->db()->getOne($sql);
    }

}

?>