<?php
/**
脚本要求：
名词解释：
进销存货品：jxc.kela.cn
仓库货品：boss.kela.cn

1.两边货品同步。
	数量相等，状态相同。
	进销存货品：jxc_goods		1,943,578	MyISAM	utf8_general_ci	1.0 GB	
	仓库货品：warehouse_goods	  113,326	InnoDB	utf8_general_ci	60.6 MB		


//查出所有关联的订单及商品信息
SELECT og.goods_id, o.order_id, o.`order_time` , o.status, o.type, j.is_on_sale
FROM jxc_order o, jxc_order_goods og, jxc_goods j
WHERE o.order_id = og.order_id
AND og.goods_id = j.goods_id
AND o.order_time >=  '2015-04-21'
AND o.status
IN ( 1, 2 ) 
AND o.type NOT 
IN (
 'W'
)
ORDER BY  `og`.`goods_id` , o.order_id DESC 


//只统计一条数据的最终状态
//SELECT og.goods_id, o.order_id, o.`order_time` , o.status, o.type, j.is_on_sale

SELECT j.id,j.goods_id,j.is_on_sale, j.addtime,j.weixiu_status, j.company, j.warehouse, j.change_time

SELECT j.id,j.goods_id,j.warehouse,j.company,j.is_on_sale,j.addtime,j.change_time,j.weixiu_status	

*/

header("Content-type:text/html;charset=utf8;");
error_reporting(E_ALL);

function change($a,$step=5){
    $i_leng= count($a);
    $x = array();
    for($i=0;$i<$i_leng;$i++){
        $x = $i/$step;
        $y = $i%$step;
        $new[$x][$y] = $a[$i];
    }
    return $new;
}

$old_mysqli=new mysqli('192.168.1.79','kela_jxc','kela$%jxc','jxc') or die("数据库连接失败！") ; 
//$old_mysqli=new mysqli('192.168.1.94','report_user','mzSBcvSPBsxvDcDy','kela_report_fin') or die("数据库连接失败！") ; 
$new_mysqli=new mysqli('192.168.1.93','cuteman','QW@W#RSS33#E#','warehouse_shipping') or die("数据库连接失败！") ; 

$output_input_jxc = 0;
$output_input_warehouse = 10;

if($output_input_jxc){

	$sql = "truncate table jxc_goods_20150502;";
	$new_mysqli->query($sql);


	$oldsql = "select j.id,j.goods_id,j.warehouse,j.company,j.is_on_sale,j.change_time,j.weixiu_status,o.order_id,o.addtime,o.checktime, o.status, o.type
	FROM jxc_order o, jxc_order_goods og, jxc_goods j, (
	SELECT og.goods_id g, MAX( o.order_id ) oid
	FROM jxc_order o, jxc_order_goods og
	WHERE o.order_id = og.order_id
	AND o.order_time >=  '2015-04-21'
	AND o.status
	IN ( 1, 2 ) 
	AND o.type NOT 
	IN (
	 'W'
	)
	GROUP BY og.goods_id
	)c
	WHERE og.goods_id = c.g
	AND o.order_id = c.oid
	AND o.order_id = og.order_id
	AND og.goods_id = j.goods_id";

	$result=$old_mysqli->query($oldsql);
	if ($result) {
		if($result->num_rows>0){                                               //判断结果集中行的数目是否大于0
			while($row =$result->fetch_assoc() ){                        //循环输出结果集中的记录
				if(is_null($row['weixiu_status'])){
					$row['weixiu_status']=0;
				}

				$list[$row['goods_id']] = $row;
			}
		}
	}else{
		die('no jxc data');
	}
	sort($list);
	$list = change($list,1000);


	$insert_field = "id,goods_id,warehouse,company,is_on_sale,change_time,weixiu_status,order_id,addtime ,check_time,status, type";
	foreach($list as $k => $part){
		$sql = "";
		$value_sql = array();
		foreach($part as $row){
			$value_sql[]="('".implode("','",$row)."')";
		}
		$sql = "insert into jxc_goods_20150502($insert_field) value ".implode(',',$value_sql).";";
		$new_mysqli->query($sql);

	}
}
/*
//新系统
(
SELECT og.goods_id g, MAX( o.id ) oid
FROM warehouse_bill o, warehouse_bill_goods og
WHERE o.id = og.bill_id
GROUP BY og.goods_id
)c

*/
if($output_input_warehouse){
	$sql = "truncate table warehouse_goods_20150502;";
	$new_mysqli->query($sql);

	$newsql = "select j.id,j.goods_id,j.warehouse_id warehouse,j.company_id company,j.is_on_sale,j.change_time,j.weixiu_status,o.id order_id, o.`create_time` ,o.check_time,o.bill_status, o.bill_type
	FROM warehouse_bill o, warehouse_bill_goods og, warehouse_goods j, (
	SELECT og.goods_id g, MAX( o.id ) oid
	FROM warehouse_bill o, warehouse_bill_goods og
	WHERE o.id = og.bill_id
    AND o.bill_status IN ( 1, 2 ) 
    AND o.bill_type NOT 
    IN (
     'W'
    )
	GROUP BY og.goods_id
	)c
	WHERE og.goods_id = c.g
	AND o.id = c.oid
	AND o.id = og.bill_id
	AND og.goods_id = j.goods_id";

	$result=$new_mysqli->query($newsql);
	if ($result) {
		if($result->num_rows>0){                                               //判断结果集中行的数目是否大于0
			while($row =$result->fetch_assoc() ){                        //循环输出结果集中的记录
				if(is_null($row['weixiu_status'])){
					$row['weixiu_status']=0;
				}
				if(is_null($row['change_time'])){
					$row['change_time']='0000-00-00 00:00:00';
				}
				$list[$row['goods_id']] = $row;
			}
		}
	}else{
		die('no warehouse data');
	}
        sort($list);	
	$num = $argv[1];
	$list = change($list,$num);


	$insert_field = "id,goods_id,warehouse,company,is_on_sale,change_time,weixiu_status,order_id,addtime ,check_time,status, type";
	foreach($list as $k => $part){
		$sql = "";
		$value_sql = array();
		foreach($part as $row){
			$value_sql[]="('".implode("','",$row)."')";
		}
		$sql = "insert into warehouse_goods_20150502($insert_field) value ".implode(',',$value_sql).";";
		$new_mysqli->query($sql);
	}
}


die;
/*
select a.*,b.* 
select IF(a.status=1 and b.status=1,if(a.addtime>b.addtime,a.addtime,b.addtime),111),a.status,b.status,a.addtime,b.addtime
select IF(a.status=2 and b.status=1,b.addtime,111),a.status,b.status,a.addtime,b.addtime

select IF(a.status=1 and b.status=2,a.addtime,111),a.status,b.status,a.addtime,b.addtime
select a.goods_id,IF(a.status=2 and b.status=2,if(a.check_time>b.check_time,a.check_time,b.check_time),111),a.is_on_sale ais_on_sale,b.is_on_sale bis_on_sale,a.status astatus,b.status bstatus,a.check_time acheck_time,b.check_time bcheck_time
From jxc_goods_20150502 a,warehouse_goods_20150502 b
where a.goods_id = b.goods_id 
AND ((a.is_on_sale=0 and b.is_on_sale!=1) or 
(a.is_on_sale=1 and b.is_on_sale!=2) or 
(a.is_on_sale=2 and b.is_on_sale!=3) or 
(a.is_on_sale=3 and b.is_on_sale!=5) or 
(a.is_on_sale=4 and b.is_on_sale!=4) or 
(a.is_on_sale=5 and b.is_on_sale!=10) or 
(a.is_on_sale=7 and b.is_on_sale!=9) or 
(a.is_on_sale=8 and b.is_on_sale!=11) or 
(a.is_on_sale=9 and b.is_on_sale!=8) or 
(a.is_on_sale=10 and b.is_on_sale!=12) or 
(a.is_on_sale=11 and b.is_on_sale!=6) or 
(a.is_on_sale=12 and b.is_on_sale!=7) or 
(b.is_on_sale!=100) or
(a.is_on_sale!=100) 
)
AND a.status=2 and b.status=2
AND a.status=2 and b.status=1
AND a.status=1 and b.status=2
AND a.status=1 and b.status=1









select IF(a.status=1 and b.status=1,if(a.addtime>b.addtime,a.addtime,b.addtime),111),a.status,b.status,a.addtime,b.addtime
select IF(a.status=2 and b.status=1,b.addtime,111),a.status,b.status,a.addtime,b.addtime

select IF(a.status=1 and b.status=2,a.addtime,111),a.status,b.status,a.addtime,b.addtime

select a.goods_id,IF(a.status=2 and b.status=2,if(a.check_time>b.check_time,a.check_time,b.check_time),111),a.is_on_sale ais_on_sale,b.is_on_sale bis_on_sale,a.status astatus,b.status bstatus,a.type atype,b.type btype,a.check_time acheck_time,b.check_time bcheck_time
From jxc_goods_20150502 a,warehouse_goods_20150502 b
where a.goods_id = b.goods_id 
AND a.status=2 and b.status=2
AND a.is_on_sale=1 and b.is_on_sale!=2


AND a.is_on_sale=0 and b.is_on_sale!=1			//0


AND a.is_on_sale=2 and b.is_on_sale!=3
AND a.is_on_sale=3 and b.is_on_sale!=5
AND a.is_on_sale=4 and b.is_on_sale!=4
AND a.is_on_sale=5 and b.is_on_sale!=10
AND a.is_on_sale=7 and b.is_on_sale!=9
AND a.is_on_sale=8 and b.is_on_sale!=11
AND a.is_on_sale=9 and b.is_on_sale!=8
AND a.is_on_sale=10 and b.is_on_sale!=12
AND a.is_on_sale=11 and b.is_on_sale!=6
AND a.is_on_sale=12 and b.is_on_sale!=7


(b.is_on_sale!=100) or
(a.is_on_sale!=100) 
)
*/


