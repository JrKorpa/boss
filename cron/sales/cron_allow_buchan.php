<?php

error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
define('FLAG',FALSE);

//$con_order = mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','app_order');
$con_order = mysqli_connect('203.130.44.199','cuteman','QW@W#RSS33#E#','app_order');
//$con_supplier = mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','kela_supplier');
$con_supplier = mysqli_connect('203.130.44.199','cuteman','QW@W#RSS33#E#','kela_supplier');

//查询需要布产的数据(1）订单是【期货】类型、货品状态也是【期货】；2）订单是【已审核】状态；3）订单支付状态是【财务备案】和【已付款】状态；4）订单录单来源是【批量导单】和[外部订单]。)
$where = '';
if(FLAG){
	$start_time = date("Y-m-d")." 00:00:00";
	$end_time = date("Y-m-d H:i:s");
	$where = " AND `oi`.`create_time`>='{$start_time}' AND `oi`.`create_time`<='{$end_time}'";
}

$attr_names =array('cart'=>'石重','clarity'=>'净度','color'=>'颜色','zhengshuhao'=>'证书号','caizhi'=>'材质','jinse'=>'金色','jinzhong'=>'金重','zhiquan'=>'指圈','kezi'=>'刻字','face_work'=>'表面工艺');

$sql = "SELECT count(*) FROM `base_order_info` as `oi`,`app_order_details` as `od` WHERE `oi`.`id`=`od`.`order_id` AND `oi`.`is_xianhuo`=0 AND `od`.`is_stock_goods`=0 AND `oi`.`order_status`=2 AND `oi`.`order_pay_status` in (3,4) AND `oi`.`referer` in ('批量导单','外部订单')".$where." limit 0,1";
$t = mysqli_query($con_order,$sql);
$cnt = mysqli_fetch_row($t);
$len = $cnt[0];

$num = 500;
$forsize = ceil($len / $num);
//$forsize = 1;

for($ii = 1; $ii <= $forsize; $ii ++){
    $offset = ($ii - 1) * $num;
    $sql = "SELECT `od`.*,`oi`.`order_sn`,`oi`.`consignee`,`oi`.`customer_source_id`,`oi`.`department_id` FROM `base_order_info` as `oi`,`app_order_details` as `od` WHERE `oi`.`id`=`od`.`order_id` AND `oi`.`is_xianhuo`=0 AND `od`.`is_stock_goods`=0 AND `oi`.`order_status`=2 AND `oi`.`order_pay_status` in (3,4) AND `oi`.`referer` in ('批量导单','外部订单')".$where. " limit $offset,$num";
    $res = mysqli_query($con_order,$sql);
    $goods_arr = array();
	$val_sql=array();    
	$jj = 0;
    while ( $val = mysqli_fetch_assoc($res) ){
		//查看此商品是否已经开始布产
		$_sql = "SELECT count(*) FROM `product_goods_rel` WHERE `status`=0 AND `goods_id`='".$val['id']."'";
		$_t = mysqli_query($con_supplier,$sql);
		$_cnt = mysqli_fetch_row($t);
		$_len = $_cnt[0];
		if($_len){
			continue;
		}
		$new_style_info = array();
		foreach ($attr_names as $a_key=>$a_val){
			$xmp['code'] = $a_key;
			$xmp['name'] = $a_val;
			$xmp['value'] = $val[$a_key];
			$new_style_info[]= $xmp;
		}
		$goods_arr[$jj]['p_id'] =	$val['id'];
		$goods_arr[$jj]['p_sn'] =  $val['order_sn'];
		$goods_arr[$jj]['style_sn'] = $val['goods_sn'];
		$goods_arr[$jj]['goods_name'] = $val['goods_name'];
		$goods_arr[$jj]['bc_style'] = '普通件';
		$goods_arr[$jj]['xiangqian'] = $val['xiangqian'];
		$goods_arr[$jj]['goods_type'] = $val['goods_type'];
		$goods_arr[$jj]['cat_type'] = $val['cat_type'];
		$goods_arr[$jj]['product_type'] = $val['product_type'];
		$goods_arr[$jj]['num'] = $val['goods_count'];
		$goods_arr[$jj]['info'] = $val['details_remark'];
		$goods_arr[$jj]['consignee'] = $val['consignee'];
		$goods_arr[$jj]['attr'] = $new_style_info;
		$goods_arr[$jj]['customer_source_id'] = $val['customer_source_id'];
		$goods_arr[$jj]['channel_id'] = $val['department_id'];
		$goods_arr[$jj]['create_user']=$val['create_user'];
		$jj++;
    }
	$keys = array('insert_data','from_type');
	$vals = array($goods_arr,2);//2代表订单来源

	$ret=processor_api($keys,$vals,'AddProductInfo');
	if($ret['error']==0){
		if(!empty($ret['data'])){
			$update_sql = "UPDATE `app_order_details` set `bc_id`= CASE id ";
			$ids = implode(',',array_column($ret['data'],'id'));
			foreach ($ret['data'] as $v) {
				$update_sql .= sprintf("WHEN %d THEN %d ", $v['id'], $v['buchan_sn']);
			} 
			$update_sql .= "END WHERE `id` IN ($ids)"; 
			echo $update_sql;
			mysqli_query($con_order,$update_sql);
		}
	}
}

echo "\n\n\n\n完成ok！";
die();

function processor_api($keys,$vals,$method){
	//验证密钥
	$token="processor";
	$args=array();
	foreach($keys as $k=>$v){
		$v=trim($v);
		if(!empty($v)){
			$args[$keys[$k]]=$vals[$k];
		}
	}
	ksort($args);
	$ori_str=json_encode($args);
	$data=array("filter"=>$ori_str,"sign"=>md5($token.$ori_str.$token));
	$ret=httpCurl(Util::getDomain()."/api.php?con=processor&act=".$method,$data,false,true,30);
	$ret=json_decode($ret,true);
	
	if($ret['error']>0){
		return array('data'=>$ret['error_msg'],'error'=>1);
	}else{
		return array('data'=>$ret['return_msg'],'error'=>0);
	}
}

function httpCurl($url, $post = '') {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
	curl_setopt($ch, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if (!empty($post)) {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	}
	return curl_exec($ch);
}


