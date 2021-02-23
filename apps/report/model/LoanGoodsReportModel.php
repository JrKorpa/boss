<?php
/**
 *  -------------------------------------------------
 *   @file		: LoanGoodsReportModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: hxw <731884339@qq.com>
 *   @date		: 2015-08-28 11:24:15
 *   @update	:
 *  -------------------------------------------------
 */
class LoanGoodsReportModel extends Model
{
	/**
	 *	pageList，分页列表
	 *
	 *	@url LoanGoodsReportModel/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true,$bill_type='')
	{
		//不要用*,修改为具体字段
		$field = "`wg`.`goods_id`,
        `wg`.`warehouse`, 
        `wg`.`goods_sn`, 
        `wg`.`goods_name`, 
        `wg`.`company`, 
        (SELECT self_age FROM warehouse_goods_age WHERE goods_id = `wg`.`goods_id`) as `kuling`, 
        `b`.`bill_no`, 
        case when `b`.`bill_status`=1 then '已保存' when `b`.`bill_status`=2 then '已审核' when `b`.`bill_status`=3 then '已取消' end  AS `bill_status`, 
        `b`.`check_user`, 
        `b`.`create_time`, 
        `b`.`bill_note`,
        `b`.`check_time`,
        `b`.`create_user`,
        `b`.`id` bill_id,
        `b`.`to_warehouse_id`";
		$pre_sql = "SELECT * FROM (";
		$sql_union = $sql = $sql_triple = "SELECT wb.* FROM ( SELECT  
        ".$field." FROM `warehouse_shipping`.`warehouse_bill` AS `b` , 
        `warehouse_shipping`.`warehouse_bill_goods` AS `g` , 
        `warehouse_shipping`.`warehouse_goods` AS `wg`";
		//调出仓为“借货”
		/*
        $str = "`g`.`bill_id` = `b`.`id`
        AND `g`.`goods_id` = `wg`.`goods_id`
        AND `b`.`from_company_id` = 58
        AND `wg`.`warehouse_id` = 4
        AND `wg`.`is_on_sale` in(2,5)
        AND `b`.`bill_status` = 1 AND ";
        */
		
		$str = "`g`.`bill_id` = `b`.`id`
        AND `g`.`goods_id` = `wg`.`goods_id`
        AND `wg`.`company_id` = 58
        AND `wg`.`warehouse_id` = 4
        AND `wg`.`is_on_sale` in(2,5)
        AND `b`.`bill_status` != 3  AND ";
        //调入仓为“借货”
        $union_str = "`g`.`bill_id` = `b`.`id`
        AND `g`.`goods_id` = `wg`.`goods_id`
        AND `b`.`to_company_id` = 58
        AND `b`.`to_warehouse_id` = 4
        AND `wg`.`is_on_sale` in(2,5)
        AND `b`.`bill_status` != 3 
        AND EXISTS(SELECT 1 FROM warehouse_bill WHERE id = (SELECT bill_id FROM warehouse_bill_goods WHERE goods_id = wg.goods_id ORDER BY id DESC LIMIT 1) AND to_warehouse_id=4) AND ";
        //借货后取消的
        $triple_str = "`g`.`bill_id` = `b`.`id`
        AND `g`.`goods_id` = `wg`.`goods_id`
        AND `b`.`to_company_id` = 58
        AND `b`.`to_warehouse_id` = 4
        AND `wg`.`is_on_sale` in(2,5)
        AND `b`.`bill_status` != 3 
        AND EXISTS(SELECT 1 FROM warehouse_bill WHERE id = (SELECT bill_id FROM warehouse_bill_goods WHERE goods_id = wg.goods_id ORDER BY id DESC LIMIT 1) AND bill_status=3 AND bill_type = 'M') AND ";
        $sql = $pre_sql.$sql;
        $orderby_str = " ORDER BY CASE `b`.`bill_status` WHEN 2 THEN `b`.`check_time` ELSE `b`.`create_time` END DESC";
//		if($where['xxx'] != "")
//		{
//			$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//		}
//		if(!empty($where['xx']))
//		{
//			$str .= "`xx`='".$where['xx']."' AND ";
//		}
        if(!empty($where['goods_id']))
        {
            $str .= "`wg`.`goods_id`='".$where['goods_id']."' AND ";
            $union_str .= "`wg`.`goods_id`='".$where['goods_id']."' AND ";
            $triple_str .= "`wg`.`goods_id`='".$where['goods_id']."' AND ";
        }
        /*if(!empty($where['goods_status']))
        {
            $str .= "`od`.`is_on_sale`=".$where['goods_status']." AND ";
        }*/

          if ($bill_type == 'M')
        {
             $str .= "`b`.`bill_type`='M' AND ";
             $union_str .= "`b`.`bill_type`='M' AND ";
             $triple_str .= "`b`.`bill_type`='M' AND ";
        }

        if ($where['start_time'] !== '')
        {
            $str .= " `b`.`check_time` >= '".$where['start_time']." 00:00:00' AND ";
            $union_str .= " `b`.`check_time` >= '".$where['start_time']." 00:00:00' AND ";
            $triple_str .= " `b`.`check_time` >= '".$where['start_time']." 00:00:00' AND ";
        }
        if ($where['end_time'] !== '')
        {
            $str .= " `b`.`check_time` <= '".$where['end_time']." 23:59:59' AND ";
            $union_str .= " `b`.`check_time` <= '".$where['end_time']." 23:59:59' AND ";
            $triple_str .= " `b`.`check_time` <= '".$where['end_time']." 23:59:59' AND ";
        }
        if ($where['create_start_time'] !== '')
        {
            $str .= " `b`.`create_time` >= '".$where['create_start_time']." 00:00:00' AND ";
            $union_str .= " `b`.`create_time` >= '".$where['create_start_time']." 00:00:00' AND ";
            $triple_str .= " `b`.`create_time` >= '".$where['create_start_time']." 00:00:00' AND ";
        }
        if ($where['create_end_time'] !== '')
        {
            $str .= " `b`.`create_time` <= '".$where['create_end_time']." 23:59:59' AND ";
            $union_str .= " `b`.`create_time` <= '".$where['create_end_time']." 23:59:59' AND ";
            $triple_str .= " `b`.`create_time` <= '".$where['create_end_time']." 23:59:59' AND ";
        }
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		if($union_str)
		{
			$union_str = rtrim($union_str,"AND ");//这个空格很重要
			$sql_union .=" WHERE ".$union_str;
		}
		if($triple_str){
			$triple_str = rtrim($triple_str,"AND ");//这个空格很重要
			$sql_triple .=" WHERE ".$triple_str;
		}
		
        $sql .= $orderby_str.") as `wb` group by `wb`.`goods_id`";
       // $sql .= " UNION ".$sql_union.$orderby_str.") as `wb` group by `wb`.`goods_id`"; 
        //$sql .= " UNION ".$sql_triple.$orderby_str.") as `wb` group by `wb`.`goods_id`";
        $sql .= ") wu group by `goods_id`";
       // echo $sql;exit;
		$data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	
}

?>