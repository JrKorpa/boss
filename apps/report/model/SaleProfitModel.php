<?php
/**
 *  -------------------------------------------------
 *   @file		: SaleProfitModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-04-17 14:49:15
 *   @update	:
 *  -------------------------------------------------
 */
class SaleProfitModel extends Model
{

	/**
	 *	pageList，分页列表
	 *
	 *	@url SaleProfitController/search
	 */
	function pageList ($where)
	{
		//不要用*,修改为具体字段
		$year=$where['year'];		
        $way =$where['way'];
        $report_type = $where['report_type'];
        $timetostion = time();
        if($year != date("Y")){
            $timetostion = strtotime($year."-12-31");
        }
        $timetostion_m = $timetostion;
        $timetostion = strtotime ("-1 day", $timetostion);
         
		
		$dia_type=array('其他','GIA','HRD-D','HRD-S');
		$str = '';
        $res = array();
		if(SYS_SCOPE=="boss"){
            if($way == 1){
                /*
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
                bg.goods_id=g.goods_id and g.cat_type1='裸石' and b.bill_type='S' and b.bill_status=2 and b.check_time>='2017-12-01 00:00:00' and g.zhengshuleibie in ('GIA','EGL','HRD-D','HRD-S') and s.id is null and b.check_time>if((select max(check_time) from warehouse_goods_sale) is null,'2017-12-01 00:00:00',(select max(check_time) from warehouse_goods_sale))";
                $this->db()->query($sql);

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
                bg.goods_id=g.goods_id and g.cat_type1<>'裸石' and b.bill_type='S' and b.bill_status=2 and b.check_time>='2017-12-01 00:00:00' and g.zhengshuleibie in ('GIA','EGL','HRD-D','HRD-S') and s.id is null and (g.pinpai ='' or g.pinpai is null )";
                $this->db()->query($sql);

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
                from warehouse_bill_goods bg left join warehouse_goods_sale s on bg.id=s.id,warehouse_bill b,warehouse_goods g 
                left join warehouse_goods g2 on if(g.pinpai='' or g.pinpai is null ,'-999',g.pinpai)=g2.zhengshuhao and g2.cat_type='裸石' left join warehouse_goods_sale g3 on g2.goods_id=g3.goods_id    
                where bg.bill_id=b.id and 
                bg.goods_id=g.goods_id and g.cat_type1<>'裸石' and b.bill_type='S' and b.bill_status=2 and b.check_time>='2017-12-01 00:00:00' and g.zhengshuleibie in ('GIA','EGL','HRD-D','HRD-S') and s.id is null and (g.pinpai ='' or g.pinpai is null ) 
                and  g3.goods_id is  null";
                $this->db()->query($sql);
                */
                $sql1 = "insert into  warehouse_goods_sale                    
                        select bg.id,b.bill_no,b.check_time,g.goods_id,bg.shijia,if(g.yuanshichengbenjia_zs>0,g.yuanshichengbenjia_zs,g.yuanshichengbenjia) as yuanshichengbenjia,
                        if(g.zhengshuleibie  in ('GIA','HRD-D','HRD-S'),g.zhengshuleibie,'其他') as zhengshuleibie,
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
                        bg.goods_id=g.goods_id and g.cat_type1='裸石' and b.bill_type='S' and b.bill_status=2 and b.check_time>='2017-12-01 00:00:00' and g.zhengshuleibie<>'' and s.id is null and b.check_time>if((select max(check_time) from warehouse_goods_sale) is null,'2017-12-01 00:00:00',(select max(check_time) from warehouse_goods_sale))";
                    
                
                $sql2 = "insert into  warehouse_goods_sale 
                        select bg.id,b.bill_no,b.check_time,g.goods_id,bg.shijia,if(g.yuanshichengbenjia_zs>0,g.yuanshichengbenjia_zs,g.yuanshichengbenjia) as yuanshichengbenjia,
                        if(g.zhengshuleibie  in ('GIA','HRD-D','HRD-S'),g.zhengshuleibie,'其他') as zhengshuleibie,
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
                        bg.goods_id=g.goods_id and g.cat_type1<>'裸石' and b.bill_type='S' and b.bill_status=2 and b.check_time>='2017-12-01 00:00:00' and g.zhengshuleibie<>'' and s.id is null and (g.pinpai ='' or g.pinpai is null ) ";

                 $sql3 = "insert into  warehouse_goods_sale  
                        select distinct bg.id,b.bill_no,b.check_time,g.goods_id,bg.shijia,if(g.yuanshichengbenjia_zs>0,g.yuanshichengbenjia_zs,g.yuanshichengbenjia) as yuanshichengbenjia,
                        if(g.zhengshuleibie  in ('GIA','HRD-D','HRD-S'),g.zhengshuleibie,'其他') as zhengshuleibie,
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
                        from warehouse_bill_goods bg left join warehouse_goods_sale s on bg.id=s.id,warehouse_bill b,warehouse_goods g 
                        left join warehouse_goods g2 on convert(if(g.pinpai='' or g.pinpai is null ,'-999',g.pinpai),CHAR)=g2.zhengshuhao and g2.cat_type='裸石' left join warehouse_goods_sale g3 on g2.goods_id=g3.goods_id    
                        where bg.bill_id=b.id and 
                        bg.goods_id=g.goods_id and g.cat_type1<>'裸石' and b.bill_type='S' and b.bill_status=2 and b.check_time>='2017-12-01 00:00:00' and g.zhengshuleibie<>'' and s.id is null and  g3.goods_id is  null  
                        and g.goods_id not in 
                                (
                                    select g2.goods_id
                                    from warehouse_bill_goods bg2,warehouse_bill b2,warehouse_goods g2 
                                            left join warehouse_goods g22 on convert(if(g2.pinpai='' or g2.pinpai is null ,'-999',g2.pinpai),CHAR)=g22.zhengshuhao and g22.cat_type='裸石'
                                            left join warehouse_goods_sale g32 on g22.goods_id=g32.goods_id   
                                    where bg2.bill_id=b2.id 
                                            and bg2.goods_id=g2.goods_id and g2.cat_type1<>'裸石' and b2.bill_type='S' and b2.bill_status=2 and b2.check_time>='2017-12-01 00:00:00' and g2.zhengshuleibie in ('GIA','AGL','EGL','HRD-D','HRD-S') 
                                            and g32.goods_id is not null
                                )";
                   
                $this->db()->query($sql1);
                $this->db()->query($sql2);
                $this->db()->query($sql3);
               
                $sql = "select zhengshuleibie,year,week,daydate,sum(shijia) as shijia,sum(yuanshichengbenjia) as chengbenjia, concat(round((sum(shijia)-sum(yuanshichengbenjia))/sum(shijia)*100,2),'%') as lirun,count(1) as lishu from warehouse_goods_sale where year='$year' group by zhengshuleibie,year,week";
                //      if($where['xxx'] != "")
                //      {
                //          $str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
                //      }
                //      if(!empty($where['xx']))
                //      {
                //          $str .= "`xx`='".$where['xx']."' AND ";
                //      }
                if($str)
                {
                    $str = rtrim($str,"AND ");//这个空格很重要
                    $sql .=" WHERE ".$str;
                }
                //$sql .= " ORDER BY `id` DESC";
                $data = $this->db()->getAll($sql);
                $list=array();
                foreach ($data as $key => $v) {
                    $list[$v['zhengshuleibie']][$v['week']]=$v;
                }   

                $week=intval(date('W',$timetostion)+1);
                foreach ($dia_type as $key => $v) {
                    for($i=1;$i<=$week;$i++){
                        if(!isset($list[$v][$i])){
                            $res[$v][$i]=array('zhengshuleibie' =>$v,
                                               'year' => $year,
                                               'week' => $i,
                                               'daydate' =>'',
                                               'shijia' => 0,
                                               'chengbenjia' => 0,
                                               'lirun' =>0,
                                               'lishu'=>0);
                        }else{
                            $res[$v][$i]=$list[$v][$i];
                        }   
                    }
                    $res[$v][0]=array(
                                'shijia'=>array_sum(array_column($res[$v],'shijia')),
                                'chengbenjia'=>array_sum(array_column($res[$v],'chengbenjia')),
                                'lirun'=>array_sum(array_column($res[$v],'shijia'))==0 ? 0: round((array_sum(array_column($res[$v],'shijia'))-array_sum(array_column($res[$v],'chengbenjia')))/array_sum(array_column($res[$v],'shijia'))*100,2).'%',
                                'lishu'=>array_sum(array_column($res[$v],'lishu'))
                                 ); 
                            
                }           
                
            }elseif($way == 2){
                $sql1 = "insert into  warehouse_goods_sale_month                   
                        select bg.id,b.bill_no,b.check_time,g.goods_id,bg.shijia,if(g.yuanshichengbenjia_zs>0,g.yuanshichengbenjia_zs,g.yuanshichengbenjia) as yuanshichengbenjia,
                        if(g.zhengshuleibie  in ('GIA','HRD-D','HRD-S'),g.zhengshuleibie,'其他') as zhengshuleibie,
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
                        MONTH(b.check_time) as month,
                        concat(left(subdate(b.check_time,if(date_format(b.check_time,'%w')=0,7,date_format(b.check_time,'%w'))-1),10),'->',left(adddate(subdate(b.check_time,if(date_format(b.check_time,'%w')=0,7,date_format(b.check_time,'%w'))-1),6),10)) as daydate
                        from warehouse_bill_goods bg left join warehouse_goods_sale_month s on bg.id=s.id,warehouse_bill b,warehouse_goods g  where bg.bill_id=b.id and 
                        bg.goods_id=g.goods_id and g.cat_type1='裸石' and b.bill_type='S' and b.bill_status=2 and b.check_time>='2016-12-01 00:00:00' and g.zhengshuleibie<>'' and s.id is null and b.check_time>if((select max(check_time) from warehouse_goods_sale_month) is null,'2016-12-01 00:00:00',(select max(check_time) from warehouse_goods_sale_month)) ";

                 $sql2 = "insert into  warehouse_goods_sale_month 
                        select bg.id,b.bill_no,b.check_time,g.goods_id,bg.shijia,if(g.yuanshichengbenjia_zs>0,g.yuanshichengbenjia_zs,g.yuanshichengbenjia) as yuanshichengbenjia,
                        if(g.zhengshuleibie  in ('GIA','HRD-D','HRD-S'),g.zhengshuleibie,'其他') as zhengshuleibie,
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
                        MONTH(b.check_time) as month,
                        concat(left(subdate(b.check_time,if(date_format(b.check_time,'%w')=0,7,date_format(b.check_time,'%w'))-1),10),'->',left(adddate(subdate(b.check_time,if(date_format(b.check_time,'%w')=0,7,date_format(b.check_time,'%w'))-1),6),10)) as daydate
                        from warehouse_bill_goods bg left join warehouse_goods_sale_month s on bg.id=s.id,warehouse_bill b,warehouse_goods g  where bg.bill_id=b.id and 
                        bg.goods_id=g.goods_id and g.cat_type1<>'裸石' and b.bill_type='S' and b.bill_status=2 and b.check_time>='2017-12-01 00:00:00' and g.zhengshuleibie<>'' and s.id is null and (g.pinpai ='' or g.pinpai is null ) "; 
                 $sql3 = "insert into  warehouse_goods_sale_month   
                        select distinct bg.id,b.bill_no,b.check_time,g.goods_id,bg.shijia,if(g.yuanshichengbenjia_zs>0,g.yuanshichengbenjia_zs,g.yuanshichengbenjia) as yuanshichengbenjia,
                        if(g.zhengshuleibie  in ('GIA','HRD-D','HRD-S'),g.zhengshuleibie,'其他') as zhengshuleibie,
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
                        MONTH(b.check_time) as month,
                        concat(left(subdate(b.check_time,if(date_format(b.check_time,'%w')=0,7,date_format(b.check_time,'%w'))-1),10),'->',left(adddate(subdate(b.check_time,if(date_format(b.check_time,'%w')=0,7,date_format(b.check_time,'%w'))-1),6),10)) as daydate
                        from warehouse_bill_goods bg left join warehouse_goods_sale_month s on bg.id=s.id,warehouse_bill b,warehouse_goods g  
                        left join warehouse_goods g2 on convert(if(g.pinpai='' or g.pinpai is null ,'-999',g.pinpai),CHAR)=g2.zhengshuhao and g2.cat_type='裸石' left join warehouse_goods_sale_month g3 on g2.goods_id=g3.goods_id
                        where bg.bill_id=b.id and 
                        bg.goods_id=g.goods_id and g.cat_type1<>'裸石' and b.bill_type='S' and b.bill_status=2 and b.check_time>='2017-10-01 00:00:00' and g.zhengshuleibie<>'' and s.id is null and  g3.goods_id is  null 
                        and g.goods_id not in (
                             select  g9.goods_id   
                                  from warehouse_bill_goods bg9,warehouse_bill b9,warehouse_goods g9  
                                 left join warehouse_goods g29 on convert(if(g9.pinpai='' or g9.pinpai is null ,'-999',g9.pinpai),CHAR)=g29.zhengshuhao and g29.cat_type='裸石' left join warehouse_goods_sale_month g39 on g29.goods_id=g39.goods_id
                             where bg9.bill_id=b9.id and 
                                 bg9.goods_id=g9.goods_id and g9.cat_type1<>'裸石' and b9.bill_type='S' and b9.bill_status=2 and b9.check_time>='2017-10-01 00:00:00' and g9.zhengshuleibie<>'' and  g39.goods_id is not  null 
                        )";                        
          

                $this->db()->query($sql1);
                $this->db()->query($sql2);
                $this->db()->query($sql3);




                $sql = "select zhengshuleibie,year,month,daydate,sum(shijia) as shijia,sum(yuanshichengbenjia) as chengbenjia, concat(round((sum(shijia)-sum(yuanshichengbenjia))/sum(shijia)*100,2),'%') as lirun,count(1) as lishu from warehouse_goods_sale_month where year='$year' group by zhengshuleibie,year,month";
                //      if($where['xxx'] != "")
                //      {
                //          $str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
                //      }
                //      if(!empty($where['xx']))
                //      {
                //          $str .= "`xx`='".$where['xx']."' AND ";
                //      }
                if($str)
                {
                    $str = rtrim($str,"AND ");//这个空格很重要
                    $sql .=" WHERE ".$str;
                }
                //$sql .= " ORDER BY `id` DESC";
                $data = $this->db()->getAll($sql);
                $list=array();
                foreach ($data as $key => $v) {
                    $list[$v['zhengshuleibie']][$v['month']]=$v;
                }   

                $month=intval(date('m',$timetostion_m));
                foreach ($dia_type as $key => $v) {
                    for($i=1;$i<=$month;$i++){
                        if(!isset($list[$v][$i])){
                            $res[$v][$i]=array('zhengshuleibie' =>$v,
                                               'year' => $year,
                                               'month' => $i,
                                               'daydate' =>'',
                                               'shijia' => 0,
                                               'chengbenjia' => 0,
                                               'lirun' =>0,
                                               'lishu'=>0);
                        }else{
                            $res[$v][$i]=$list[$v][$i];
                        }   
                    }
                    $res[$v][0]=array(
                                'shijia'=>array_sum(array_column($res[$v],'shijia')),
                                'chengbenjia'=>array_sum(array_column($res[$v],'chengbenjia')),
                                'lirun'=>array_sum(array_column($res[$v],'shijia'))==0 ? 0: round((array_sum(array_column($res[$v],'shijia'))-array_sum(array_column($res[$v],'chengbenjia')))/array_sum(array_column($res[$v],'shijia'))*100,2).'%' ,
                                'lishu'=>array_sum(array_column($res[$v],'lishu'))
                                ); 
                            
                }
            }else{

            }

        }
        if(SYS_SCOPE=="zhanting"){
		    if($year < 2019) return $res;
            if($report_type == 1){
                    if($way == 1){
                        $sql1 = "insert into  warehouse_goods_sale                             
                                select bg.id,b.bill_no,b.check_time,g.goods_id,bg.shijia,g.yuanshichengbenjia,
                                if(g.zhengshuleibie  in ('GIA','HRD-D','HRD-S'),g.zhengshuleibie,'其他') as zhengshuleibie,
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
                                from warehouse_bill_goods bg left join warehouse_goods_sale s on bg.id=s.id,warehouse_bill b,warehouse_goods g  where bg.bill_id=b.id and b.from_company_id in ( select id from cuteframe.company c where c.company_type=2) and 
                                bg.goods_id=g.goods_id and g.cat_type1='裸石' and b.bill_type='S' and b.bill_status=2 and b.check_time>='2017-12-01 00:00:00' and g.zhengshuleibie<>'' and s.id is null and b.check_time>if((select max(check_time) from warehouse_goods_sale) is null,'2017-12-01 00:00:00',(select max(check_time) from warehouse_goods_sale))";
                            
                        $sql2 = "insert into  warehouse_goods_sale 
                                select bg.id,b.bill_no,b.check_time,g.goods_id,bg.shijia,g.yuanshichengbenjia,
                                if(g.zhengshuleibie  in ('GIA','HRD-D','HRD-S'),g.zhengshuleibie,'其他') as zhengshuleibie,
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
                                from warehouse_bill_goods bg left join warehouse_goods_sale s on bg.id=s.id,warehouse_bill b,warehouse_goods g  where bg.bill_id=b.id and b.from_company_id in ( select id from cuteframe.company c where c.company_type=2) and 
                                bg.goods_id=g.goods_id and g.cat_type1<>'裸石' and b.bill_type='S' and b.bill_status=2 and b.check_time>='2017-12-01 00:00:00' and g.zhengshuleibie<>'' and s.id is null and (g.pinpai ='' or g.pinpai is null ) ";

                        $sql3 = "insert into  warehouse_goods_sale 

                                select distinct bg.id,b.bill_no,b.check_time,g.goods_id,bg.shijia,g.yuanshichengbenjia,
                                if(g.zhengshuleibie  in ('GIA','HRD-D','HRD-S'),g.zhengshuleibie,'其他') as zhengshuleibie,
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
                                from warehouse_bill_goods bg left join warehouse_goods_sale s on bg.id=s.id,warehouse_bill b,warehouse_goods g 
                                left join warehouse_goods g2 on convert(if(g.pinpai='' or g.pinpai is null ,'-999',g.pinpai),CHAR)=g2.zhengshuhao and g2.cat_type='裸石' left join warehouse_goods_sale g3 on g2.goods_id=g3.goods_id    
                                where bg.bill_id=b.id and b.from_company_id in ( select id from cuteframe.company c where c.company_type=2) and 
                                bg.goods_id=g.goods_id and g.cat_type1<>'裸石' and b.bill_type='S' and b.bill_status=2 and b.check_time>='2017-12-01 00:00:00' and g.zhengshuleibie<>'' and s.id is null and  g3.goods_id is  null 
                                and g.goods_id not in 
                                (
                                    select g2.goods_id
                                    from warehouse_bill_goods bg2,warehouse_bill b2,warehouse_goods g2 
                                            left join warehouse_goods g22 on convert(if(g2.pinpai='' or g2.pinpai is null ,'-999',g2.pinpai),CHAR)=g22.zhengshuhao and g22.cat_type='裸石'
                                            left join warehouse_goods_sale g32 on g22.goods_id=g32.goods_id   
                                    where bg2.bill_id=b2.id and b2.from_company_id in ( select id from cuteframe.company c2 where c2.company_type=2) and 
                                            bg2.goods_id=g2.goods_id and g2.cat_type1<>'裸石' and b2.bill_type='S' and b2.bill_status=2 and b2.check_time>='2017-12-01 00:00:00' and g2.zhengshuleibie<>''  
                                            and g32.goods_id is not null
                                )";
                            
                        $this->db()->query($sql1);
                        $this->db()->query($sql2);
                        $this->db()->query($sql3);
                        $sql = "select zhengshuleibie,year,week,daydate,sum(shijia) as shijia,sum(yuanshichengbenjia) as chengbenjia, concat(round((sum(shijia)-sum(yuanshichengbenjia))/sum(shijia)*100,2),'%') as lirun,count(1) as lishu from warehouse_goods_sale where year='$year' group by zhengshuleibie,year,week";
                        
                        if($str)
                        {
                            $str = rtrim($str,"AND ");//这个空格很重要
                            $sql .=" WHERE ".$str;
                        }
                        //$sql .= " ORDER BY `id` DESC";
                        //echo $sql;
                        $data = $this->db()->getAll($sql);
                        $list=array();
                        foreach ($data as $key => $v) {
                            $list[$v['zhengshuleibie']][$v['week']]=$v;
                        }   

                        $week=intval(date('W',$timetostion)+1);
                        foreach ($dia_type as $key => $v) {
                            for($i=1;$i<=$week;$i++){
                                if(!isset($list[$v][$i])){
                                    $res[$v][$i]=array('zhengshuleibie' =>$v,
                                                       'year' => $year,
                                                       'week' => $i,
                                                       'daydate' =>'',
                                                       'shijia' => 0,
                                                       'chengbenjia' => 0,
                                                       'lirun' =>0,
                                                       'lishu'=>0);
                                }else{
                                    $res[$v][$i]=$list[$v][$i];
                                }   
                            }
                            $res[$v][0]=array(
                                        'shijia'=>array_sum(array_column($res[$v],'shijia')),
                                        'chengbenjia'=>array_sum(array_column($res[$v],'chengbenjia')),
                                        'lirun'=>array_sum(array_column($res[$v],'shijia'))==0 ? 0: round((array_sum(array_column($res[$v],'shijia'))-array_sum(array_column($res[$v],'chengbenjia')))/array_sum(array_column($res[$v],'shijia'))*100,2).'%',
                                        'lishu'=>array_sum(array_column($res[$v],'lishu'))
                                         ); 
                                    
                        }           
                        
                    }elseif($way == 2){
                         $sql1 = "insert into  warehouse_goods_sale_month                            
                                select bg.id,b.bill_no,b.check_time,g.goods_id,bg.shijia,g.yuanshichengbenjia,
                                if(g.zhengshuleibie  in ('GIA','HRD-D','HRD-S'),g.zhengshuleibie,'其他') as zhengshuleibie,
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
                                MONTH(b.check_time) as month,
                                concat(left(subdate(b.check_time,if(date_format(b.check_time,'%w')=0,7,date_format(b.check_time,'%w'))-1),10),'->',left(adddate(subdate(b.check_time,if(date_format(b.check_time,'%w')=0,7,date_format(b.check_time,'%w'))-1),6),10)) as daydate
                                from warehouse_bill_goods bg left join warehouse_goods_sale_month s on bg.id=s.id,warehouse_bill b,warehouse_goods g  where bg.bill_id=b.id 
                                and b.from_company_id in ( select id from cuteframe.company c where c.company_type=2) and 
                                bg.goods_id=g.goods_id and g.cat_type1='裸石' and b.bill_type='S' and b.bill_status=2 and b.check_time>='2017-12-01 00:00:00' and g.zhengshuleibie<>'' and s.id is null and b.check_time>if((select max(check_time) from warehouse_goods_sale_month) is null,'2017-12-01 00:00:00',(select max(check_time) from warehouse_goods_sale_month)) "; 

                        $sql2 = "insert into  warehouse_goods_sale_month 
                                select bg.id,b.bill_no,b.check_time,g.goods_id,bg.shijia,g.yuanshichengbenjia,
                                if(g.zhengshuleibie  in ('GIA','HRD-D','HRD-S'),g.zhengshuleibie,'其他') as zhengshuleibie,
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
                                MONTH(b.check_time) as month,
                                concat(left(subdate(b.check_time,if(date_format(b.check_time,'%w')=0,7,date_format(b.check_time,'%w'))-1),10),'->',left(adddate(subdate(b.check_time,if(date_format(b.check_time,'%w')=0,7,date_format(b.check_time,'%w'))-1),6),10)) as daydate
                                from warehouse_bill_goods bg left join warehouse_goods_sale_month s on bg.id=s.id,warehouse_bill b,warehouse_goods g  where bg.bill_id=b.id 
                                and b.from_company_id in ( select id from cuteframe.company c where c.company_type=2) and 
                                bg.goods_id=g.goods_id and g.cat_type1<>'裸石' and b.bill_type='S' and b.bill_status=2 and b.check_time>='2017-12-01 00:00:00' and g.zhengshuleibie<>'' and s.id is null and (g.pinpai ='' or g.pinpai is null )";
         
                        $sql3 = "insert into  warehouse_goods_sale_month 
                                select distinct bg.id,b.bill_no,b.check_time,g.goods_id,bg.shijia,g.yuanshichengbenjia,
                                if(g.zhengshuleibie  in ('GIA','HRD-D','HRD-S'),g.zhengshuleibie,'其他') as zhengshuleibie,
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
                                MONTH(b.check_time) as month,
                                concat(left(subdate(b.check_time,if(date_format(b.check_time,'%w')=0,7,date_format(b.check_time,'%w'))-1),10),'->',left(adddate(subdate(b.check_time,if(date_format(b.check_time,'%w')=0,7,date_format(b.check_time,'%w'))-1),6),10)) as daydate
                                from warehouse_bill_goods bg left join warehouse_goods_sale_month s on bg.id=s.id,warehouse_bill b,warehouse_goods g  
                                left join warehouse_goods g2 on convert(if(g.pinpai='' or g.pinpai is null ,'-999',g.pinpai),CHAR)=g2.zhengshuhao and g2.cat_type='裸石' left join warehouse_goods_sale_month g3 on g2.goods_id=g3.goods_id
                                where bg.bill_id=b.id 
                                and b.from_company_id in ( select id from cuteframe.company c where c.company_type=2) and 
                                bg.goods_id=g.goods_id and g.cat_type1<>'裸石' and b.bill_type='S' and b.bill_status=2 and b.check_time>='2017-12-01 00:00:00' and g.zhengshuleibie<>'' and s.id is null and  g3.goods_id is  null 
                                and g.goods_id not in 
                                (
                                    select g9.goods_id
                                    from warehouse_bill_goods bg9,warehouse_bill b9,warehouse_goods g9  
                                       left join warehouse_goods g29 on convert(if(g9.pinpai='' or g9.pinpai is null ,'-999',g9.pinpai),CHAR)=g29.zhengshuhao and g29.cat_type='裸石' left join warehouse_goods_sale_month g39 on g29.goods_id=g39.goods_id
                                    where bg9.bill_id=b9.id 
                                       and b9.from_company_id in ( select id from cuteframe.company c9 where c9.company_type=2) and 
                                       bg9.goods_id=g9.goods_id and g9.cat_type1<>'裸石' and b9.bill_type='S' and b9.bill_status=2 and b9.check_time>='2017-12-01 00:00:00' and g9.zhengshuleibie<>'' and  g39.goods_id is not  null
                                )";
                           
                        $this->db()->query($sql1);
                        $this->db()->query($sql2);
                        $this->db()->query($sql3);

                        $sql = "select zhengshuleibie,year,month,daydate,sum(shijia) as shijia,sum(yuanshichengbenjia) as chengbenjia, concat(round((sum(shijia)-sum(yuanshichengbenjia))/sum(shijia)*100,2),'%') as lirun,count(1) as lishu from warehouse_goods_sale_month where year='$year' group by zhengshuleibie,year,month";
                        
                        if($str)
                        {
                            $str = rtrim($str,"AND ");//这个空格很重要
                            $sql .=" WHERE ".$str;
                        }
                        //$sql .= " ORDER BY `id` DESC";
                        $data = $this->db()->getAll($sql);
                        $list=array();
                        foreach ($data as $key => $v) {
                            $list[$v['zhengshuleibie']][$v['month']]=$v;
                        }   

                        $month=intval(date('m',$timetostion_m));
                        foreach ($dia_type as $key => $v) {
                            for($i=1;$i<=$month;$i++){
                                if(!isset($list[$v][$i])){
                                    $res[$v][$i]=array('zhengshuleibie' =>$v,
                                                       'year' => $year,
                                                       'month' => $i,
                                                       'daydate' =>'',
                                                       'shijia' => 0,
                                                       'chengbenjia' => 0,
                                                       'lirun' =>0,
                                                       'lishu'=>0);
                                }else{
                                    $res[$v][$i]=$list[$v][$i];
                                }   
                            }
                            $res[$v][0]=array(
                                        'shijia'=>array_sum(array_column($res[$v],'shijia')),
                                        'chengbenjia'=>array_sum(array_column($res[$v],'chengbenjia')),
                                        'lirun'=>array_sum(array_column($res[$v],'shijia'))==0 ? 0: round((array_sum(array_column($res[$v],'shijia'))-array_sum(array_column($res[$v],'chengbenjia')))/array_sum(array_column($res[$v],'shijia'))*100,2).'%' ,
                                        'lishu'=>array_sum(array_column($res[$v],'lishu'))
                                        ); 
                                    
                        }
                    }else{

                    }

            }elseif($report_type == 2){    
                    if($way == 1){
                        $sql="insert into  warehouse_goods_salep 
                        select bg.id,b.bill_no,b.to_customer_id,b.check_time,g.goods_id,bg.shijia,g.yuanshichengbenjia,if(g.zhengshuleibie  in ('GIA','HRD-D','HRD-S'),g.zhengshuleibie,'其他') as zhengshuleibie,
                        if(g.zuanshidaxiao<0.3,0.2,if(g.zuanshidaxiao<0.4,0.3,if(g.zuanshidaxiao<0.5,0.4,if(g.zuanshidaxiao<0.6,0.5,if(g.zuanshidaxiao<0.7,0.6,if(g.zuanshidaxiao<0.8,0.7,if(g.zuanshidaxiao<0.9,0.8,if(g.zuanshidaxiao<1.0,0.9,if(g.zuanshidaxiao<1.5,1.0,1.5) ) ) ) ) ) ) )) as zuanshidaxiao,
                        g.yanse,g.jingdu,
                        left(subdate(b.check_time,if(date_format(b.check_time,'%w')=0,7,date_format(b.check_time,'%w'))-1),4) as year,
                        WEEKOFYEAR(b.check_time) as week,
                        concat(left(subdate(b.check_time,if(date_format(b.check_time,'%w')=0,7,date_format(b.check_time,'%w'))-1),10),'->',left(adddate(subdate(b.check_time,if(date_format(b.check_time,'%w')=0,7,date_format(b.check_time,'%w'))-1),6),10)) as daydate
                        from warehouse_bill_goods bg left join warehouse_goods_salep p on bg.id=p.id,warehouse_bill b,warehouse_goods g  where bg.bill_id=b.id and 
                        bg.goods_id=g.goods_id and g.cat_type1='裸石' and b.from_company_id = 58 and b.bill_type='P' and (b.bill_status=2 or b.bill_status=4) and b.check_time>='2016-12-01 00:00:00' 
                        and g.zhengshuleibie<>'' and p.id is null and b.check_time>=if((select max(check_time) from warehouse_goods_salep) is null,'2016-12-01 00:00:00',(select max(check_time) from warehouse_goods_salep))
                        "; 
                        $this->db()->query($sql);
                        $sql="select zhengshuleibie,j.wholesale_name,year,week,daydate,sum(shijia) as shijia,sum(yuanshichengbenjia) as chengbenjia, concat(round((sum(shijia)-sum(yuanshichengbenjia))/sum(shijia)*100,2),'%') as lirun,count(1) as lishu from warehouse_goods_salep p left join jxc_wholesale j on p.to_customer_id=j.wholesale_id where year='$year' group by zhengshuleibie,to_customer_id,year,week"; 

                        if($str)
                        {
                            $str = rtrim($str,"AND ");//这个空格很重要
                            $sql .=" WHERE ".$str;
                        }
                        //$sql .= " ORDER BY `id` DESC";
                        $data = $this->db()->getAll($sql);
                        $list=array();
                        foreach ($data as $key => $v) {
                            $list[$v['zhengshuleibie']][$v['wholesale_name']][$v['week']]=$v;
                        }   

                        $week=intval(date('W',$timetostion));

                        foreach ($dia_type as $key => $v) {
                            $jxsarr=array_keys($list[$v]);
                            //echo "<pre>";print_r($jxsarr);
                            foreach ($jxsarr as $keyjxs => $vjxs) {
                                for($i=1;$i<=$week;$i++){
                                    if(!isset($list[$v][$vjxs][$i])){
                                        $res[$v][$vjxs][$i]=array('zhengshuleibie' =>$v,
                                                           'year' => $year,
                                                           'week' => $i,
                                                           'daydate' =>'',
                                                           'shijia' => 0,
                                                           'chengbenjia' => 0,
                                                           'lirun' =>0,
                                                           'lishu'=>0);
                                    }else{
                                        $res[$v][$vjxs][$i]=$list[$v][$vjxs][$i];
                                    }   
                                }
                                $res[$v][$vjxs][0]=array(
                                        'lishu'=>array_sum(array_column($res[$v][$vjxs],'lishu')),
                                        'shijia'=>array_sum(array_column($res[$v][$vjxs],'shijia')),
                                        'chengbenjia'=>array_sum(array_column($res[$v][$vjxs],'chengbenjia')),
                                        'lirun'=>array_sum(array_column($res[$v][$vjxs],'shijia'))==0 ? 0: round((array_sum(array_column($res[$v][$vjxs],'shijia'))-array_sum(array_column($res[$v][$vjxs],'chengbenjia')))/array_sum(array_column($res[$v][$vjxs],'shijia'))*100,2).'%' 
                                         );                     
                            }   

                        }
                    }elseif($way == 2){
                        $sql = "insert into  warehouse_goods_salep_month 
                        select bg.id,b.bill_no,b.to_customer_id,b.check_time,g.goods_id,bg.shijia,g.yuanshichengbenjia,if(g.zhengshuleibie  in ('GIA','HRD-D','HRD-S'),g.zhengshuleibie,'其他') as zhengshuleibie,
                        if(g.zuanshidaxiao<0.3,0.2,if(g.zuanshidaxiao<0.4,0.3,if(g.zuanshidaxiao<0.5,0.4,if(g.zuanshidaxiao<0.6,0.5,if(g.zuanshidaxiao<0.7,0.6,if(g.zuanshidaxiao<0.8,0.7,if(g.zuanshidaxiao<0.9,0.8,if(g.zuanshidaxiao<1.0,0.9,if(g.zuanshidaxiao<1.5,1.0,1.5) ) ) ) ) ) ) )) as zuanshidaxiao,
                        g.yanse,g.jingdu,
                        left(subdate(b.check_time,if(date_format(b.check_time,'%w')=0,7,date_format(b.check_time,'%w'))-1),4) as year,
                        MONTH(b.check_time) as month,
                        concat(left(subdate(b.check_time,if(date_format(b.check_time,'%w')=0,7,date_format(b.check_time,'%w'))-1),10),'->',left(adddate(subdate(b.check_time,if(date_format(b.check_time,'%w')=0,7,date_format(b.check_time,'%w'))-1),6),10)) as daydate
                        from warehouse_bill_goods bg left join warehouse_goods_salep_month p on bg.id=p.id,warehouse_bill b,warehouse_goods g  where bg.bill_id=b.id and 
                        bg.goods_id=g.goods_id and g.cat_type1='裸石' and b.from_company_id = 58 and b.bill_type='P' and (b.bill_status=2 or b.bill_status=4) and b.check_time>='2016-12-01 00:00:00' 
                        and g.zhengshuleibie<>'' and p.id is null and b.check_time>=if((select max(check_time) from warehouse_goods_salep_month) is null,'2016-12-01 00:00:00',(select max(check_time) from warehouse_goods_salep_month))";

                        $this->db()->query($sql);

                        $sql = "select zhengshuleibie,j.wholesale_name,year,month,daydate,sum(shijia) as shijia,sum(yuanshichengbenjia) as chengbenjia, concat(round((sum(shijia)-sum(yuanshichengbenjia))/sum(shijia)*100,2),'%') as lirun,count(1) as lishu from warehouse_goods_salep_month p left join jxc_wholesale j on p.to_customer_id=j.wholesale_id where year='$year' group by zhengshuleibie,to_customer_id,year,month";
                      
                        if($str)
                        {
                            $str = rtrim($str,"AND ");//这个空格很重要
                            $sql .=" WHERE ".$str;
                        }
                        //$sql .= " ORDER BY `id` DESC";
                        $data = $this->db()->getAll($sql);
                        $list=array();
                        foreach ($data as $key => $v) {
                            $list[$v['zhengshuleibie']][$v['wholesale_name']][$v['month']]=$v;
                        }   

                        $month=intval(date('m',$timetostion));

                        foreach ($dia_type as $key => $v) {
                            $jxsarr=array_keys($list[$v]);
                            //echo "<pre>";print_r($jxsarr);
                            foreach ($jxsarr as $keyjxs => $vjxs) {
                                for($i=1;$i<=$month;$i++){
                                    if(!isset($list[$v][$vjxs][$i])){
                                        $res[$v][$vjxs][$i]=array('zhengshuleibie' =>$v,
                                                           'year' => $year,
                                                           'month' => $i,
                                                           'daydate' =>'',
                                                           'shijia' => 0,
                                                           'chengbenjia' => 0,
                                                           'lirun' =>0,
                                                           'lishu' =>0);
                                    }else{
                                        $res[$v][$vjxs][$i]=$list[$v][$vjxs][$i];
                                    }   
                                }
                                $res[$v][$vjxs][0]=array(
                                        'lishu'=>array_sum(array_column($res[$v][$vjxs],'lishu')),
                                        'shijia'=>array_sum(array_column($res[$v][$vjxs],'shijia')),
                                        'chengbenjia'=>array_sum(array_column($res[$v][$vjxs],'chengbenjia')),
                                        'lirun'=>array_sum(array_column($res[$v][$vjxs],'shijia'))==0 ? 0: round((array_sum(array_column($res[$v][$vjxs],'shijia'))-array_sum(array_column($res[$v][$vjxs],'chengbenjia')))/array_sum(array_column($res[$v][$vjxs],'shijia'))*100,2).'%' 
                                         );                     
                            }   

                        }
                    }else{

                    }
            }else{

            }        
        }
        return $res;
	}
}

?>