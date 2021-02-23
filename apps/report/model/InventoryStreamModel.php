<?php
/**
 *  -------------------------------------------------
 *   @file		: InventoryStreamModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-04-13 10:58:36
 *   @update	:
 *  -------------------------------------------------
 */
class InventoryStreamModel extends Model
{

    //拼接sql
    public function getSql($where=array())
    {
        //不要用*,修改为具体字段
        $sql = "SELECT
        g.zhengshuleibie, 
        IF (
            g.zuanshidaxiao < 0.05,
            0,
        IF (
            g.zuanshidaxiao < 0.1,
            0.05,
        IF (
            g.zuanshidaxiao < 0.2,
            0.1,
        IF (
            g.zuanshidaxiao < 0.3,
            0.2,

        IF (
            g.zuanshidaxiao < 0.4,
            0.3,

        IF (
            g.zuanshidaxiao < 0.5,
            0.4,

        IF (
            g.zuanshidaxiao < 0.6,
            0.5,

        IF (
            g.zuanshidaxiao < 0.7,
            0.6,

        IF (
            g.zuanshidaxiao < 0.8,
            0.7,

        IF (
            g.zuanshidaxiao < 0.9,
            0.8,

        IF (
            g.zuanshidaxiao < 1.0,
            0.9,

        IF (
            g.zuanshidaxiao < 1.5,
            1.0,
            1.5
        )
        )
        )
        )
        )
        )
        )
        )
        )
        )
        )
        ) AS zuanshidaxiao,
         g.yanse,
         g.jingdu,
         sum(1) AS 'all_kucun',
         sum(IF(g.put_in_type = 1, 1, 0)) AS 'buy_kucun'
        FROM
            warehouse_goods g";
        $str = "g.is_on_sale = 2 AND g.warehouse not in('批发展厅库','批发借货','唯品会已售出产品库','线下生产镶石库','客订单裸钻库','网络待取库','上海南京东路体验店待取','北京西单体验店待取','苏州广济南路体验店待取','杭州分公司环球中心体验店待取','合肥分公司长江中路银泰体验店待取','乌鲁木齐新华北路体验店待取','重庆分公司解放碑体验店待取','深圳南山分公司后海保利体验店待取','南京分公司新街口体验店待取','青岛分公司香港中路体验店待取') AND ";
//      if($where['xxx'] != "")
//      {
//          $str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//      }
        if(!empty($where['dia_type']))
        {
            $str .= "g.zhengshuleibie ='".$where['dia_type']."' AND ";
        }else{
            $str .= "g.zhengshuleibie IN('GIA',
            'EGL','AGL',
            'HRD-D',
            'HRD-S') AND ";
        }
        if($str)
        {
            $str = rtrim($str,"AND ");//这个空格很重要
            $sql .=" WHERE ".$str;
        }
        
        $sql .= " GROUP BY
        g.zhengshuleibie,
IF (
    g.zuanshidaxiao < 0.05,
    0,
IF (
    g.zuanshidaxiao < 0.1,
    0.05,
IF (
    g.zuanshidaxiao < 0.2,
    0.1,
IF (
    g.zuanshidaxiao < 0.3,
    0.2,

IF (
    g.zuanshidaxiao < 0.4,
    0.3,

IF (
    g.zuanshidaxiao < 0.5,
    0.4,

IF (
    g.zuanshidaxiao < 0.6,
    0.5,

IF (
    g.zuanshidaxiao < 0.7,
    0.6,

IF (
    g.zuanshidaxiao < 0.8,
    0.7,

IF (
    g.zuanshidaxiao < 0.9,
    0.8,

IF (
    g.zuanshidaxiao < 1.0,
    0.9,

IF (
    g.zuanshidaxiao < 1.5,
    1.0,
    1.5
)
)
)
)
)
)
)
)
)
)
)
),
 g.yanse,
 g.jingdu";
        //echo $sql;die;
        return $sql;
    }

	/**
	 *	pageList，分页列表
	 *
	 *	@url InventoryStreamController/search
	 */
	function pageList ($where)
	{
        $sql = "insert into  warehouse_goods_sale 
select bg.id,b.bill_no,b.check_time,g.goods_id,bg.shijia,g.yuanshichengbenjia,g.zhengshuleibie,
IF (
    g.zuanshidaxiao < 0.05,
    0,
IF (
    g.zuanshidaxiao < 0.1,
    0.05,
IF (
    g.zuanshidaxiao < 0.2,
    0.1,if(g.zuanshidaxiao<0.3,0.2,if(g.zuanshidaxiao<0.4,0.3,if(g.zuanshidaxiao<0.5,0.4,if(g.zuanshidaxiao<0.6,0.5,if(g.zuanshidaxiao<0.7,0.6,if(g.zuanshidaxiao<0.8,0.7,if(g.zuanshidaxiao<0.9,0.8,if(g.zuanshidaxiao<1.0,0.9,if(g.zuanshidaxiao<1.5,1.0,1.5) ) ) ) ) )))) ) )) as zuanshidaxiao,
g.yanse,g.jingdu,
left(subdate(b.check_time,if(date_format(b.check_time,'%w')=0,7,date_format(b.check_time,'%w'))-1),4),
WEEKOFYEAR(b.check_time) as week,
concat(left(subdate(b.check_time,if(date_format(b.check_time,'%w')=0,7,date_format(b.check_time,'%w'))-1),10),'->',left(adddate(subdate(b.check_time,if(date_format(b.check_time,'%w')=0,7,date_format(b.check_time,'%w'))-1),6),10)) as daydate
from warehouse_bill_goods bg left join warehouse_goods_sale s on bg.id=s.id,warehouse_bill b,warehouse_goods g  where bg.bill_id=b.id and 
bg.goods_id=g.goods_id and (b.bill_type='S' or (b.bill_type='P' and b.from_company_id = 58)) and b.bill_status=2 and b.check_time>='2016-12-01 00:00:00' and g.zhengshuleibie in ('AGL','GIA','EGL','HRD-D','HRD-S') and s.id is null and b.check_time>if((select max(check_time) from warehouse_goods_sale) is null,'2016-12-01 00:00:00',(select max(check_time) from warehouse_goods_sale))";

        $this->db()->query($sql);
		$sql = $this->getSql($where);
		$data = $this->db()->getAll($sql);
		return $data;
	}

    //取销售数据
    public function getSaleStoneData()
    {
        $year=date('Y');
        $sql = "select zhengshuleibie,zuanshidaxiao,yanse,jingdu,year,week,count(id) as count,daydate from warehouse_goods_sale where year='$year'
group by zhengshuleibie,zuanshidaxiao,yanse,jingdu,year,week order by week";
        return $this->db()->getAll($sql);
    }
}

?>