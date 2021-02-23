<?php
/**
 *  -------------------------------------------------
 *   @file		: StyleSaleReportModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2018-03-05 10:26:59
 *   @update	:
 *  -------------------------------------------------
 */
function fmap($var){	
	return trim($var,'\'');
} 
class StyleSaleReportModel extends Model
{



	/**
	 *	pageList，分页列表
	 *
	 *	@url StyleSaleReportController/search
	 */
	function pageList ($where)
	{
		//不要用*,修改为具体字段
		if(empty($where['style_sn'])||empty($where['date_start'])||empty($where['date_end'])){
			return null;
		}
		$style_sns=array_map('fmap',explode(',', $where['style_sn']));


        $qudaos=array('天猫','京东SOP','京东自营');     

        $days=floor((strtotime($where['date_end'])-strtotime($where['date_start']))/86400);     
        $dates=array();
        for($i=0;$i<=$days;$i++){
        	$dates[]= date("Y-m-d",strtotime("+{$i} day",strtotime($where['date_start'])));
        }
        //print_r($style_sns); exit();
        $sql="select g.goods_sn
				,if(o.order_pay_type=24,'天猫',if(o.order_pay_type=310,'京东自营','京东SOP')) as qudao
				,left(b.check_time,10) as day
				,count(g.goods_id) as sale_num
				,sum(bg.shijia) as sale_acount
				from warehouse_bill_goods bg,warehouse_goods g,warehouse_bill b   
				left join app_order.base_order_info o on b.order_sn=o.order_sn
				left join cuteframe.customer_sources cs on o.customer_source_id=cs.id 
				left join cuteframe.sales_channels sc on o.department_id=sc.id 
				where bg.bill_id=b.id and bg.goods_id=g.goods_id and b.bill_type='S' and b.bill_status=2 				
				and sc.channel_class =1
				and o.order_pay_type in (24, 310,245,246)
				and o.order_status=2 AND " ;
		
		if(!empty($where['date_start']))
                $sql .=" b.check_time>='{$where['date_start']} 00:00:00' AND ";
		if(!empty($where['date_end']))
                $sql .=" b.check_time<='{$where['date_end']} 23:59:59' AND ";
		  
		if(!empty($where['style_sn']))
		{
			//$str .= " goods_id like \"%".addslashes($where['goods_id'])."%\"  AND ";
			$sql .= " g.goods_sn in (".$where['style_sn'].") AND ";
		}


        $sql = rtrim($sql,"AND ");
        $sql .= " group by g.goods_sn,if(o.order_pay_type=24,'天猫',if(o.order_pay_type=310,'京东自营','京东SOP')),left(b.check_time,10)";
        $list=array();
        $res=array();
		$data = $this->db()->getAll($sql);
		//echo $sql;
		foreach ($data as $key => $v) {
			$list[$v['goods_sn']][$v['qudao']][$v['day']]=$v;
		}
        foreach ($style_sns as $key1 => $style_sn) {
        	foreach ($qudaos as $key2 => $qudao) {
                foreach ($dates as $key3 => $date) {
                    if(!isset($list[$style_sn][$qudao][$date])){
                                    $res[$style_sn][$qudao][$date]=array('goods_sn' =>$style_sn,
                                                       'qudao' => $qudao,
                                                       'day' => $date,
                                                       'sale_num' =>'0',
                                                       'sale_acount' => 0
                                                      );
                    }else{
                        $res[$style_sn][$qudao][$date]=$list[$style_sn][$qudao][$date];
                    }
                }    
            }           
        }
        $list=null;
		return array('style_sns'=>$style_sns,'qudaos'=>$qudaos,'dates'=>$dates,'data'=>$res);
	}


}

?>