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
//$new_mysqli=new mysqli('192.168.1.93','cuteman','QW@W#RSS33#E#','warehouse_shipping') or die("数据库连接失败！") ; 
$new_mysqli=new mysqli('192.168.1.93','cuteman','QW@W#RSS33#E#','warehouse_shipping') or die("数据库连接失败！") ; 

$status = array();
$statusSQL = array();
$statusTb2 = array();
$statusTb2SQL = array();
$statusJXCSQL = array();

$Ais_on_sale_list = array(
	0=>1,
	1=>2,
	2=>3,
	3=>5,
	4=>5,
	5=>10,
	7=>9,
	8=>11,
	9=>8,
	10=>12,
	11=>6,
	12=>7
);
$Bis_on_sale_list = array_flip($Ais_on_sale_list);

//-------------------------- 新系统的单据信息 和 老系统的单据信息

$sql = "select a.id a_id,a.goods_id a_goods_id,a.warehouse a_warehouse,a.company a_company,a.is_on_sale a_is_on_sale,a.addtime a_addtime,a.change_time a_change_time,a.weixiu_status a_weixiu_status,a.order_id a_order_id,a.check_time a_check_time,a.status a_status,a.type a_type,b.id b_id,b.goods_id b_goods_id,b.warehouse a_warehouse,b.company b_company,b.is_on_sale b_is_on_sale,b.addtime b_addtime,b.change_time b_change_time,b.weixiu_status b_weixiu_status,b.order_id b_order_id,b.check_time b_check_time,b.status b_status,b.type b_type
From jxc_goods_20150502 a,warehouse_goods_20150502 b
where a.goods_id = b.goods_id";
$samelist = array();
$result=$new_mysqli->query($sql);
if ($result) {
	$list = array();
	if($result->num_rows>0){                 
		while($row =$result->fetch_assoc() ){  
			$list[] = $row;
			$samelist[] = $row['a_goods_id'];
		}
	}
	$ab11=$ab12=$ab21=$ab22=$ab = 0;
	$error = array();
	
	foreach($list as $key => $val){
		if($val['a_is_on_sale'] == 0 && $val['b_is_on_sale'] == 1){
			continue;
		}elseif($val['a_is_on_sale'] == 1 && $val['b_is_on_sale'] == 2){
			continue;
		}elseif($val['a_is_on_sale'] == 2 && $val['b_is_on_sale'] == 3){
			continue;
		}elseif($val['a_is_on_sale'] == 3 && $val['b_is_on_sale'] == 5){
			continue;
		}elseif($val['a_is_on_sale'] == 4 && $val['b_is_on_sale'] == 4){
			continue;
		}elseif($val['a_is_on_sale'] == 5 && $val['b_is_on_sale'] == 10){
			continue;
		}elseif($val['a_is_on_sale'] == 7 && $val['b_is_on_sale'] == 9){
			continue;
		}elseif($val['a_is_on_sale'] == 8 && $val['b_is_on_sale'] == 11){
			continue;
		}elseif($val['a_is_on_sale'] == 9 && $val['b_is_on_sale'] == 8){
			continue;
		}elseif($val['a_is_on_sale'] == 10 && $val['b_is_on_sale'] == 12){
			continue;
		}elseif($val['a_is_on_sale'] == 11 && $val['b_is_on_sale'] == 6){
			continue;
		}elseif($val['a_is_on_sale'] == 12 && $val['b_is_on_sale'] == 7){
			continue;
		}elseif($val['b_is_on_sale'] == 100){
			continue;
		}elseif($val['a_is_on_sale'] == 100){
			continue;
		}

		if($val['a_is_on_sale'] == 100 && $val['b_is_on_sale'] == 100){
			$error['is_on_sale100'][] = $val;
		}
		
		if($val['a_status'] == 1 && $val['b_status'] == 1){
			$ab11++;
			//0
		}elseif($val['a_status'] == 1 && $val['b_status'] == 2){
			$ab12++;
			//10
			$val['real_is_on_sale'] = $val['a_is_on_sale'];
			$error['is_on_sale'][] = $val;
			//$statusSQL[] = "update warehouse_goods set is_on_sale = ".getBIsonsale($val['a_is_on_sale'],$Ais_on_sale_list)." where goods_id = '".$val['a_goods_id']."';";
			$numsql = "select goods_id from warehouse_goods where goods_id = '".$val['a_goods_id']."' and is_on_sale!=100";
			$numResult = $new_mysqli->query($numsql);
			if($numResult->num_rows>0){
				$statusSQL[] = "update warehouse_goods set is_on_sale = 100 where goods_id = '".$val['a_goods_id']."';";
			}

			
			$status[] = $val['a_goods_id']." TB b From : ".$val['b_is_on_sale']." To ".$val['a_is_on_sale']."\r\n";
		}elseif($val['a_status'] == 2 && $val['b_status'] == 1){
			$ab21++;
			//0
		}elseif($val['a_status'] == 2 && $val['b_status'] == 2){
			$ab22++;
			//1473
			if($val['a_check_time']>$val['b_check_time']){
				$val['real_is_on_sale'] = $val['a_is_on_sale'];
				$error['is_on_sale'][] = $val;
				//$statusSQL[] = "update warehouse_goods set is_on_sale = ".getBIsonsale($val['a_is_on_sale'],$Ais_on_sale_list)." where goods_id = '".$val['a_goods_id']."';";
				$numsql = "select goods_id from warehouse_goods where goods_id = '".$val['a_goods_id']."' and is_on_sale!=100";
				$numResult = $new_mysqli->query($numsql);
				if($numResult->num_rows>0){
					$statusSQL[] = "update warehouse_goods set is_on_sale = 100 where goods_id = '".$val['a_goods_id']."';";
				}

				//$status[] = $val['a_goods_id']." TB b From : ".$val['b_is_on_sale']." To ".$val['a_is_on_sale']."\r\n";
			}else{
				$val['real_is_on_sale'] = $val['b_is_on_sale'];
				$error['is_on_sale'][] = $val;
				//$statusSQL[] = "update warehouse_goods set is_on_sale = ".getAIsonsale($val['b_is_on_sale'],$Bis_on_sale_list)." where goods_id = '".$val['a_goods_id']."';";
				$numsql = "select goods_id from jxc_goods where goods_id = '".$val['a_goods_id']."' and is_on_sale!=100";
				$numResult = $old_mysqli->query($numsql);
				if($numResult->num_rows>0){
					$statusJXCSQL[] = "update jxc_goods set is_on_sale = 100 where goods_id = '".$val['b_goods_id']."';";
				}

				//$status[] = $val['a_goods_id']." TB a From : ".$val['a_is_on_sale']." To ".$val['b_is_on_sale']."\r\n";
			}
		}else{
			$ab++;
			//0
		}
	}
	//var_dump($ab11,$ab12,$ab21,$ab22,$ab,$status);//$error);
}

//老系统有单据，新系统未同步
$samelist;

$sql = "select a.id a_id,a.goods_id a_goods_id,a.warehouse a_warehouse,a.company a_company,a.is_on_sale a_is_on_sale,a.addtime a_addtime,a.change_time a_change_time,a.weixiu_status a_weixiu_status,a.order_id a_order_id,a.check_time a_check_time,a.status a_status,a.type a_type
From jxc_goods_20150502 a";
$result=$new_mysqli->query($sql);
if ($result) {
	$list = array();
	if($result->num_rows>0){
		$difflist = array();
		while($row =$result->fetch_assoc() ){  
			if(!in_array($row['a_goods_id'],$samelist)){
				$difflist[] = $row;
			}
		}
		foreach($difflist as $key => $val){
			if( $val['a_is_on_sale'] == 100){
				continue;
			}
			$val['real_is_on_sale'] = $val['a_is_on_sale'];
			$error['is_on_sale'][] = $val;
			$numsql = "select goods_id from warehouse_goods where goods_id = '".$val['a_goods_id']."' and is_on_sale!=100";
			$numResult = $new_mysqli->query($numsql);
			if($numResult->num_rows>0){
				$statusTb2SQL[] = "update warehouse_goods set is_on_sale =100 where goods_id = '".$val['a_goods_id']."';";
			}

			//$statusTb2SQL[] = "update warehouse_goods set is_on_sale = ".getBIsonsale($val['a_is_on_sale'],$Ais_on_sale_list)." where goods_id = '".$val['a_goods_id']."';";
			$statusTb2[] = $val['a_goods_id']." TB b From : 000 To ".$val['a_is_on_sale']."\r\n";
		}
	}
}

//var_dump($status,$statusTb2);
$t = implode("\r\n",$statusSQL);
file_put_contents("/data/www/sql_same.txt",$t."\r\n",FILE_APPEND );

$t = implode("\r\n",$statusTb2SQL);
file_put_contents("/data/www/sql_oldhave.txt",$t."\r\n",FILE_APPEND );

$t = implode("\r\n",$statusJXCSQL);
file_put_contents("/data/www/sql_jxc_oldhave.txt",$t."\r\n",FILE_APPEND );

function getAIsonsale($is_on_sale,$Bis_on_sale_list){
	return $Bis_on_sale_list[$is_on_sale];
}
function getBIsonsale($is_on_sale,$Ais_on_sale_list){
	return $Ais_on_sale_list[$is_on_sale];
}





