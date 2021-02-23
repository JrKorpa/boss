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

//$old_mysqli=new mysqli('192.168.1.79','kela_jxc','kela$%jxc','jxc') or die("数据库连接失败！") ; 
$old_mysqli=new mysqli('192.168.1.94','report_user','mzSBcvSPBsxvDcDy','kela_report_fin') or die("数据库连接失败！") ; 
//$new_mysqli=new mysqli('192.168.1.93','cuteman','QW@W#RSS33#E#','warehouse_shipping') or die("数据库连接失败！") ; 
$new_mysqli=new mysqli('192.168.1.93','cuteman','QW@W#RSS33#E#','warehouse_shipping') or die("数据库连接失败！") ; 

//-------------------------- 新系统的单据信息 和 老系统的单据信息

$sql = "select a.id a_id,a.goods_id a_goods_id,a.warehouse a_warehouse,a.company a_company,a.is_on_sale a_is_on_sale,a.addtime a_addtime,a.change_time a_change_time,a.weixiu_status a_weixiu_status,a.order_id a_order_id,a.check_time a_check_time,a.status a_status,a.type a_type,b.id b_id,b.goods_id b_goods_id,b.warehouse b_warehouse,b.company b_company,b.is_on_sale b_is_on_sale,b.addtime b_addtime,b.change_time b_change_time,b.weixiu_status b_weixiu_status,b.order_id b_order_id,b.check_time b_check_time,b.status b_status,b.type b_type
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
		if($val['a_warehouse'] == $val['b_warehouse'] && $val['a_company'] == $val['b_company']){
			continue;
		}
		
		if($val['a_status'] == 1 && $val['b_status'] == 1){
			$ab11++;
			//0
		}elseif($val['a_status'] == 1 && $val['b_status'] == 2){
			$ab12++;
			//0
		}elseif($val['a_status'] == 2 && $val['b_status'] == 1){
			$ab21++;
			//0
		}elseif($val['a_status'] == 2 && $val['b_status'] == 2){
			$ab22++;
		}else{
			$ab++;
			//0
		}
	}
	var_dump($ab11,$ab12,$ab21,$ab22,$ab);
}






