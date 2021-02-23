<?php
/**
 *  -------------------------------------------------
 *   @file		: AnomalyConductioModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: hxw <731884339@qq.com>
 *   @date		: 2015-08-28 11:24:15
 *   @update	:
 *  -------------------------------------------------
 */
class AnomalyConductioModel extends Model
{
	/**
	 *	pageList，分页列表
	 *
	 *	@url AnomalyConductioModel/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true,$bill_type='')
	{
		//不要用*,修改为具体字段
		$field = "`wg`.`goods_id`,
        `wg`.`warehouse`, 
        `wg`.`goods_sn`, 
        `wg`.`goods_name`, 
        `wg`.`company`, 
        (SELECT `self_age` FROM `warehouse_goods_age` WHERE `goods_id` = `wg`.`goods_id`) as `kuling`, 
        `b`.`bill_no`, 
        case when `b`.`bill_status`=1 then '已保存' when `b`.`bill_status`=2 then '已审核' when `b`.`bill_status`=3 then '已取消' end  AS `bill_status`, 
        `b`.`check_user`, 
        `b`.`create_time`, 
        `b`.`bill_note`,
        `b`.`check_time`,
        `b`.`create_user`,
        `b`.`bill_type`,
        `b`.`id`,
        `b`.`to_warehouse_id`";
		$sql = "SELECT 
        ".$field." FROM `warehouse_shipping`.`warehouse_bill` AS `b` , 
        `warehouse_shipping`.`warehouse_bill_goods` AS `g` , 
        `warehouse_shipping`.`warehouse_goods` AS `wg`";

        $str = "`g`.`bill_id` = `b`.`id` 
        AND `g`.`goods_id` = `wg`.`goods_id`
        AND `wg`.`warehouse_id` = 4 
        AND `b`.`bill_status` != 3 
        AND `wg`.`is_on_sale` not in(3, 9, 7, 12) AND ";

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
        }
        /*if(!empty($where['goods_status']))
        {
            $str .= "`od`.`is_on_sale`=".$where['goods_status']." AND ";
        }*/

        if ($where['start_time'] !== '')
        {
            $str .= " `b`.`check_time` >= '".$where['start_time']." 00:00:00' AND ";
        }
        if ($where['end_time'] !== '')
        {
            $str .= " `b`.`check_time` <= '".$where['end_time']." 23:59:59' AND ";
        }
        if ($where['create_start_time'] !== '')
        {
            $str .= " `b`.`create_time` >= '".$where['create_start_time']." 00:00:00' AND ";
        }
        if ($where['create_end_time'] !== '')
        {
            $str .= " `b`.`create_time` <= '".$where['create_end_time']." 23:59:59' AND ";
        }
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	
}

?>