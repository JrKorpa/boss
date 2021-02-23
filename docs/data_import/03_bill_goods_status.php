<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>商品修改</title>
	<style>
	#back{float: left; width:100%;height:30px;margin-bottom: 10px;}
	#back button{width:50px;height:30px;}
	#back a{text-align:center;font:12px;text-decoration:none;}
	#left{width:100%;min-height:600px;float:left;background:green;color:#FFF;}
	</style>
</head>
<body>
	<div id="back">
		<button><a href="./revise_goods.php">返回</a></button>
	</div>
<div id="left">
	<div>
	<?php
	/**
	 *  -------------------------------------------------
	 * 文件说明
	 * @file        : bill_goods_status.php
	 * @author      : yangxt <yangxiaotong@163.com>
	 * @version     : 1.0
	 *  -------------------------------------------------
	*/
	date_default_timezone_set('Asia/Shanghai');
	require_once('MysqlDB.class.php');
	set_time_limit(0);
	ini_set('memory_limit','2000M');

	//单据审核替换 1保存,2审核
	$new_dict = [
	//1收货中,2库存,3已销售,4盘点中,5调拨中,6损益中,7已报损,8返厂中,9已返厂,10销售中,11退货中,12作废
		'S'=>['1'=>'10','2'=>'3'],	//销售单
		'B'=>['1'=>'8','2'=>'9'],	//退货返厂
		'M'=>['1'=>'5','2'=>'2'],	//调拨单
		'E'=>['1'=>'6','2'=>'7'],	//损益单
		'C'=>['1'=>'8','2'=>'9'],	//其他出库
		'W'=>['1'=>'4','2'=>'2'],	//盘点
		'T'=>['1'=>'1','2'=>'2'],	//其他收货
		'L'=>['1'=>'1','2'=>'2'],	//收货
		'D'=>['1'=>'11','2'=>'2'],	//销售退货
		'P'=>['1'=>'10','2'=>'3']	//批发销售
		// 'O'=>['1'=>'10','2'=>'10'],	//维修退货
	];
	$old_dict = [
		'L'=>['1'=>'1','2'=>'2'],	//收货
		'F'=>['1'=>'5','2'=>'2'],	//转仓
		'S'=>['1'=>'10','2'=>'3'],	//销售
		'W'=>['1'=>'4','2'=>'2'],	//盘点
		'B'=>['1'=>'3','2'=>'2'],	//销售退货
		'M'=>['1'=>'8','2'=>'9'],	//退货返厂
		'C'=>['1'=>'8','2'=>'9'],	//拆货返厂
		'E'=>['1'=>'6','2'=>'7'],	//损益
		'Z'=>['1'=>'8','2'=>'9'],	//返厂组合
		'X'=>['1'=>'1','2'=>'2'],	//组合收货
		'P'=>['1'=>'10','2'=>'3'],	//批发销售
		'H'=>['1'=>'8','2'=>'9'],	//批发退货
		'T'=>['1'=>'1','2'=>'2'],	//其他收货
		// 'R'=>['1'=>'0','2'=>'0'],	//维修发货
		// 'O'=>['1'=>'0','2'=>'0'],	//维修收货
		// 'WF'=>['1'=>'3','2'=>'3'],	//维修转仓
	];

	@$goods = explode("\r\n", $_POST['goods']);
	if(empty(end($goods))){
		array_pop($goods);
	}
	if(empty($goods)){
		echo "请输入商品";
		exit;
	}
	$n_conf = [
		'dsn'=>"mysql:host=192.168.1.93;dbname=warehouse_shipping;",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset'=>'utf8'
	];
	$o_conf = [
		'dsn'=>"mysql:host=192.168.1.79;dbname=jxc;",
		'user'=>"root",
		'password'=>"n+g1kMY#2]fZ",
		'charset'=>'utf8'
	];

	$n_db = new MysqlDB($n_conf);
	$o_db = new MysqlDB($o_conf);

	foreach ($goods as $gid) {

		//新系统最后一个单据商品信息
		$sql = sprintf("SELECT `g`.`goods_id`,`g`.`bill_type`,`b`.bill_status ,`b`.`to_warehouse_id` AS warehouse_id,
			`b`.`to_warehouse_name` AS warehouse, `b`.`to_company_id` AS company_id,`b`.`to_company_name` AS company,`b`.`create_time`,`b`.`check_time`
			FROM `warehouse_bill_goods` AS `g`,`warehouse_bill` AS `b`
			WHERE `g`.`bill_id` = `b`.`id` AND `b`.`bill_type` <> 'W' 
			AND `g`.`goods_id` = '%s' ORDER BY b.`create_time` DESC",$gid);
		// print_r($sql);echo "\r\n";
		$n_info = $n_db->getRow($sql);
		//旧系统最后一个单据商品信息
		$sql = sprintf("SELECT g.`goods_id`,o.`type`,o.`status`,o.`addtime`,o.`checktime`,o.`to_warehouse_id`,o.`to_warehouse`,o.`to_company_id`,o.`to_company`,o.`from_warehouse_id`,o.`from_warehouse`,o.`from_company_id`,o.`from_company`
			FROM `jxc_order_goods` AS g,`jxc_order` AS o
			WHERE g.`order_id` = o.`order_id` AND o.`type` <> 'W' 
			AND g.`goods_id` = '%s' ORDER BY o.`addtime` DESC",$gid);
		// print_r($sql);exit;
		$o_info = $o_db->getRow($sql);
		// echo '<pre>';
		// print_r($n_info);
		// print_r($o_info);exit;
	///////////////////////////////////////////////////////////////
	////////////////////// 开始处理数据 ///////////////////////////
	///////////////////////////////////////////////////////////////
		$sale = false;
		if(empty($n_info) && !empty($o_info)){
			$company_id = ($o_info['status'] == '2')?$o_info['to_company_id']:$o_info['from_company_id'];
			$company = ($o_info['status'] == '2')?$o_info['to_company']:$o_info['from_company'];
			$warehouse_id = ($o_info['status'] == '2')?$o_info['to_warehouse_id']:$o_info['from_warehouse_id'];
			$warehouse = ($o_info['status'] == '2')?$o_info['to_warehouse']:$o_info['from_warehouse'];
			$bill_type = "old_".$o_info['type'];
			if($o_info['type'] == 'S' && $o_info['status'] == '2'){echo "货品=".$gid." 已销售！";$sale = true;}
		}elseif(!empty($n_info) && !empty($o_info)){
			if($n_info['create_time'] <= $o_info['addtime']){
				$company_id = ($o_info['status'] == '2')?$o_info['to_company_id']:$o_info['from_company_id'];
				$company = ($o_info['status'] == '2')?$o_info['to_company']:$o_info['from_company'];
				$warehouse_id = ($o_info['status'] == '2')?$o_info['to_warehouse_id']:$o_info['from_warehouse_id'];
				$warehouse = ($o_info['status'] == '2')?$o_info['to_warehouse']:$o_info['from_warehouse'];
				$bill_type = "old_".$o_info['type'];
				if($o_info['type'] == 'S' && $o_info['status'] == '2'){echo "货品=".$gid." 已销售！";$sale = true;}
			}else{
				$company_id = $n_info['company_id'];
				$company = $n_info['company'];
				$warehouse_id = $n_info['warehouse_id'];
				$warehouse = $n_info['warehouse'];
				$bill_type = "new_".$n_info['bill_type'];
				if($n_info['bill_type'] == 'S' && $n_info['bill_status'] == '2'){echo "货品=".$gid." 已销售！";$sale = true;}
			}
		}

		if(isset($company_id) && isset($warehouse_id) && $warehouse_id){
			//更新新系统
			$sql = "UPDATE `warehouse_goods` SET `company_id` = '%s',`company` = '%s',`warehouse_id` = '%s',`warehouse` = '%s' WHERE `goods_id` = '%s'";
			$sql = sprintf($sql,$company_id,$company,$warehouse_id,$warehouse,$gid);
			//echo $sql."<br/>";
			$res1 = $n_db->exec($sql);
			//更新老系统
			$sql = "UPDATE `jxc_goods` SET `company` = '%s',`warehouse` = '%s' WHERE `goods_id` = '%s'";
			$sql = sprintf($sql,$company_id,$warehouse_id,$gid);
			//echo $sql."<br/>";
			$res2 = $o_db->exec($sql);
			if($res2 || $res1){
				echo "更正系统：商品=".$gid.",公司=".$company.",仓库=".$warehouse.";  最后单据：".$bill_type.";<br/>";
				if(substr($bill_type,0,3) == 'new' && in_array($n_info['bill_type'],['S','B','M','E','C','W','T','L','D','P'])){
					$is_on_sale = $new_dict[$n_info['bill_type']][$n_info['bill_status']];			
				}
				if(substr($bill_type,0,3) == 'old' && in_array($o_info['type'],['L','F','S','W','B','M','C','E','Z','X','P','H','T'])){
					$is_on_sale = $old_dict[$o_info['type']][$o_info['status']];

				}
				if(isset($is_on_sale) && $is_on_sale){
					$sql = "UPDATE `warehouse_goods` SET `is_on_sale` = '".$is_on_sale."' WHERE `goods_id` = '".$gid."'";
					$n_db->exec($sql);
				}
				if(substr($bill_type,-1) == 'S'){
					$sql = "UPDATE `warehouse_goods` SET `is_on_sale` = '3' WHERE `goods_id` = '".$gid."'";
					$n_db->exec($sql);
					$sql = "UPDATE `jxc_goods` SET `is_on_sale` = '3' WHERE `goods_id` = '".$gid."'";
					$o_db->exec($sql);
				}
			}
		}elseif($sale){
			$sql = "UPDATE `warehouse_goods` SET `is_on_sale` = '3' WHERE `goods_id` = '".$gid."'";
			$n_db->exec($sql);
			$sql = "UPDATE `jxc_goods` SET `is_on_sale` = '3' WHERE `goods_id` = '".$gid."'";
			$o_db->exec($sql);
		}


	}

	?>
</div>
	
</div>
<div id="right">
</div>

</body>
</html>