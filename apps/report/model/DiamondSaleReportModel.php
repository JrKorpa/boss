<?php
/**
 *  -------------------------------------------------
 *   @file		: DiamondSaleReportModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: hxw <731884339@qq.com>
 *   @date		: 2015-08-28 11:24:15
 *   @update	:
 *  -------------------------------------------------
 */
class DiamondSaleReportModel extends Model
{
	/**
	 *	pageList，分页列表
	 *
	 *	@url DiamondSaleReportController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT `o`.`order_sn`,
SUBSTRING( `o`.`check_time`, 1, 4 ) AS `years`,
SUBSTRING( `o`.`check_time`, 6, 2 ) AS `month`,
SUBSTRING( `o`.`check_time`, 9, 2 ) AS `day`,
`ad`.`channel_name`,
`source`.`ad_name`,
`og`.`goods_sn`,
`og`.`goods_id`,
`og`.`goods_count`,
`og`.`cart`, 
`og`.`color`, 
`og`.`clarity`, 
`og`.`cut`, 
`og`.`goods_price`, 
IF(`o`.`is_xianhuo`=1,'现货','期货') AS `is_xianhuo`, 
`og`.`zhengshuhao`
FROM 
`app_order`.`base_order_info` AS `o`,
`cuteframe`.`ecs_ad` AS `source`, 
`cuteframe`.`sales_channels` AS `ad`, 
`app_order`.`app_order_details` AS `og`";
		$str = "`o`.`customer_source_id` = `source`.`ad_id`
AND `o`.`department_id` = `ad`.`id`
AND `o`.`id` = `og`.`order_id`
AND `o`.`order_status` = 2 
AND `o`.`order_pay_status`>1
AND `og`.`goods_type` =  'lz' AND";
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
            $str .= "`og`.`goods_id`='".$where['goods_id']."' AND ";
        }
        /*if(!empty($where['goods_status']))
        {
            $str .= "`od`.`is_on_sale`=".$where['goods_status']." AND ";
        }*/
        if ($where['start_time'] !== '')
        {
            $str .= " `o`.`check_time` >= '".$where['start_time']." 00:00:00' AND ";
        }
        if ($where['end_time'] !== '')
        {
            $str .= " `o`.`check_time` <= '".$where['end_time']." 23:59:59' AND ";
        }
        if(SYS_SCOPE == 'zhanting'){
            $str .= " `o`.`check_time` >= '2019-01-01 00:00:00' AND ";
        }
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `og`.`goods_id` DESC";
        //echo $sql;die;
		$data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
        //echo '<pre>';
        //print_r($data);die;
		return $data;
	}
}

?>