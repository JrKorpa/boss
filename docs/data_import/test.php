<?php
 	# 初始化数据
	header("Content-type:text/html;charset=utf-8");
	date_default_timezone_set('Asia/Shanghai');
	require_once('MysqlDB.class.php');
	set_time_limit(0);
	ini_set('memory_limit','2000M');

 	# 连接数据库
$new_conf = [
	'dsn'=>"mysql:host=192.168.1.93;dbname=warehouse_shipping",
	'user'=>"cuteman",
	'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
];
	$new_conf1 = [
'dsn'=>"mysql:host=192.168.1.93;dbname=app_order",
'user'=>"cuteman",
'password'=>"QW@W#RSS33#E#",
	'charset' => 'utf8'
];
// 旧项目数据库
$old_conf = [
	'dsn'=>"mysql:host=192.168.1.79;dbname=jxc",
	'user'=>"root",
	'password'=>"zUN5IDtRF5R@",
		'charset' => 'utf8'
];
$db = new MysqlDB($new_conf);
$dbR = new MysqlDB($new_conf1);
$olddb = new MysqlDB($old_conf);

$goods_status = array(
	'' => '无',
	'100' => '锁定',
	'1'	=> '收货中',
	'2'	=> '库存',
	'3' => '已销售',
	'4' => '盘点中',
	'5' => '调拨中',
	'6' => '损益中',
	'7' => '已报损',
	'8' => '返厂中',
	'9' => '已返厂',
	'10' => '销售中',
	'11' => '退货中',
	'12' => '作废'
);

//入库方式。0=购买 1=委托加工 2=供销 3=借入
$storage_mode_array = array(
		'1'=>'购买',
		'2'=>'加工',
		'3'=>'代销',
		'4'=>'借入',
);
$warehouse_type = array(
			'1'=>'柜面',
			'2'=>'后库',
			'3'=>'待取',
			'4'=>'冻结',
			'5'=>'赠品',
			'6'=>'活动',
			'7'=>'裸钻',
			'8'=>'拆货',
			'9'=>'退货',
			'10'=>'借货',
			'11'=>'其它'
			);

	$dd = date("Ymd");
	$content = "货号,入库方式,款号,名称,张萌类型,款式类型,产品线,状态,主成色,主成色重,主石,主石重,主石颜色,主石净度,切工,抛光,对称,荧光,主石粒数,副石,指圈,公司,仓库,仓库类型,库龄,本库库龄,本公司库龄,成本,证书号,BDD订单号,订单付款状态,收货单号,制单时间,审核时间\n";
	$content = iconv("utf-8","gbk",$content);
	file_put_contents(__DIR__."/log/11111" . $dd . ".csv",$content,FILE_APPEND);

	$sql = "SELECT
	           g.goods_id,
	           g.put_in_type,
	           g.goods_sn,
	           g.goods_name,
	           g.tuo_type,
	           g.cat_type,
	           g.zhushimairuchengben,
	           g.product_type,g.is_on_sale,
            	g.caizhi,
            	g.jinzhong,
            	g.zhushi,
            	g.zuanshidaxiao,
            	g.zhushiyanse,
            	g.zhushijingdu,
            	g.qiegong,
            	g.paoguang,
            	g.duichen,
            	g.yingguang,
            	g.zhengshuhao,
            	g.zhushilishu,
            	g.fushizhong,
            	g.shoucun,
            	g.company,
            	w.name,
            	w.type,
            	(UNIX_TIMESTAMP( NOW( ) ) - UNIX_TIMESTAMP( g.`addtime` ) ) / ( 24 *3600 ) AS age,
            	if( g.`change_time` = '0000-00-00 00:00:00', 0, (UNIX_TIMESTAMP( NOW( ) ) - UNIX_TIMESTAMP( g.`change_time` ) ) / ( 24 *3600 ) ) AS thisage ,
            	if(g.`addtime` = '0000-00-00 00:00:00', 0, (UNIX_TIMESTAMP( NOW( ) ) - UNIX_TIMESTAMP( g.`addtime` ) ) / ( 24 *3600 )) AS companyage ,
            	g.yuanshichengbenjia
	FROM `warehouse_goods` AS g,
    	warehouse AS w
	WHERE g.warehouse_id = w.id AND g.is_on_sale NOT IN ( 1, 3, 7, 9,11,12,100 ) and g.goods_id in(150409528627,
150409528626,
150409528625,
150409528624,
150409528623,
150409528622,
150409528621,
150409528620,
150409528619,
150409528618,
150409528617,
150409528616,
150409528615,
150416530793,
150416530794,
150416530795,
150416530796,
150416530797,
150416530798,
150416530799,
150416530800,
150416530801,
150416530802,
150416530803,
150416530804,
150416530805,
150416530806,
150416530807,
150416530808,
150416530809,
150429540286,
150429540287,
150429540288,
150429540289,
150429540290,
150429540291,
150429540292,
150429540293,
150429540294,
150429540295,
150429540296,
150429540297,
150429540298,
150429540299,
150429540300,
150429540301,
150429540302,
150429540303,
150429540304,
150429540305,
150429540306,
150429540307,
150429540308,
150429540309) order by g.goods_id asc ";
	$ret = $db->getAll($sql);
	if ($ret == null){
		break;
	}
	foreach($ret as $r){
		echo $r['goods_id'].'\n';
	    //$sql = "select * from jxc_order where goods_id = '".$r['goods_id']."' ";
	    $kelaorderinfo=array('order_sn'=>'','order_amount'=>'');
	    if($r['type'] == 3){//待取状态 去获取BDD订单号和支付状态
    	    $sql = "select bill_id from warehouse_bill_goods where goods_id = '".$r['goods_id']."' AND bill_type = 'M' order by id desc limit 1 ";
    	    $order_id = $db->getOne($sql);
    	    if($order_id){
    	        $sql = "select order_sn from warehouse_bill where id = $order_id  ";
    	        $kela_order_sn = $db->getOne($sql);
    	        if($kela_order_sn){
    	            $sql = "select order_amount,order_sn from app_order_account as oa,base_order_info as oi where oi.id = oa.order_id and oa.order_id = $order_id  ";
    	            $tmp = $dbR->getRow($sql);
    	            $kelaorderinfo['order_sn'] = $tmp['order_sn'];
    	            $kelaorderinfo['order_amount'] = $tmp['order_amount'] > 0 ? '定金' : '全款';
    	        }
    	    }
	    }

		$sql = "select o.order_id,o.addtime,o.checktime from jxc_order as o,jxc_order_goods as og where o.order_id = og.order_id and o.type = 'L' and o.status = 2 and og.goods_id = ".$r['goods_id'];
		$oarr = $olddb->getRow($sql);

		$goods_id = $r['goods_id'];
		$goods_sn = trim($r['goods_sn']);
		$goods_name = $r['goods_name'];
		$storage_mode = $r['put_in_type'];

		$cat_type = $r['cat_type'];
		$product_type = $r['product_type'];

		$is_on_sale = @$goods_status[$r['is_on_sale']];
		$caizhi = $r['caizhi'];
		$caizhizhong = $r['jinzhong'];
		$zhushi = $r['zhushi'];
		$zhushizhong = $r['zuanshidaxiao'];
		$zhushiyanse = $r['zhushiyanse'];
		$zhushijingdu = $r['zhushijingdu'];
		$qiegong = $r['qiegong'];
		$paoguang = $r['paoguang'];
		$duichen = $r['duichen'];
		$yingguang = $r['yingguang'];
		$zhushilishu = $r['zhushilishu'];
		$fushizhong = $r['fushizhong'] > 0 ? 'yes' : 'no';
		$shoucun = $r['shoucun'];
		$p_name = $r['company'];
		$wh_name = $r['name'];
		$wh_type = $warehouse_type[$r['type']];
		$age = ceil($r['age']);
		$thisage = ceil($r['thisage']);
		$companyage = ceil($r['companyage']);
		$chengbenjia = $r['yuanshichengbenjia'];
		$zhengshuhao = $r['zhengshuhao'];
		$zhangmeng_type = getZhangmengType($r);

		$str = $goods_id . "," .
			iconv("utf-8","gbk",$storage_mode_array[$storage_mode]) . "," .
			iconv("utf-8","gbk",$goods_sn) . "," .
			iconv("utf-8","gbk",$goods_name) . "," .
			iconv("utf-8","gbk",$zhangmeng_type) . "," .
			iconv("utf-8","gbk",$cat_type) . "," .
			iconv("utf-8","gbk",$product_type) . "," .
			iconv("utf-8","gbk",$is_on_sale) . "," .
			iconv("utf-8","gbk",$caizhi) . "," .

			$caizhizhong . "," .
			iconv("utf-8","gbk",$zhushi) . "," .
			$zhushizhong . "," .
			iconv("utf-8","gbk",$zhushiyanse) . "," .
			iconv("utf-8","gbk",$zhushijingdu) . "," .
			iconv("utf-8","gbk",$qiegong) . "," .
			$paoguang . "," .
			$duichen . "," .
			$yingguang . "," .
			$zhushilishu . "," .
			$fushizhong . "," .
			$shoucun . "," .
			iconv("utf-8","gbk",$p_name) . "," .
			iconv("utf-8","gbk",$wh_name) . "," .
			iconv("utf-8","gbk",$wh_type) . "," .
			$age . "," .
			$thisage . "," .
			$companyage . ",".
			$chengbenjia . "," .
			iconv("utf-8","gbk",$zhengshuhao) . "," .
			$kelaorderinfo['order_sn'] . "," .
			iconv("utf-8","gbk",$kelaorderinfo['order_amount']). ",".
			'L'.$oarr['order_id'] . ",".
			$oarr['addtime'] .",".
			$oarr['checktime'] . "\n";


		file_put_contents(__DIR__."/log/11111" . $dd . ".csv",$str,FILE_APPEND);
	}

function getZhangmengType($r){
$r['tuo_type'] = iconv("utf-8","gbk",$r['tuo_type']) ;
$r['product_type'] = iconv("utf-8","gbk",$r['product_type']) ;
$r['zhushimairuchengben'] = iconv("utf-8","gbk",$r['zhushimairuchengben']) ;
$r['zhushi'] = iconv("utf-8","gbk",$r['zhushi']) ;
$r['cat_type'] = iconv("utf-8","gbk",$r['cat_type']) ;
$r['caizhi'] = iconv("utf-8","gbk",$r['caizhi']) ;
	if ($r['tuo_type'] > 1){
		$t = "戒托";
	}elseif ($r['product_type'] == '珍珠饰品') {
		$t = "珍珠";
	}elseif ($r['product_type'] == '彩宝及翡翠饰品'){
		$t = "彩宝及翡翠";
	}elseif ($r['zhushimairuchengben'] > 0 && $r['zhushi'] == '钻石'){//主石是钻石
		if ($r['cat_type'] == '女戒'){
			$t = "钻石女戒";
		}elseif ($r['cat_type'] == '男戒'){
			$t = "钻石男戒";
		}elseif ($r['cat_type'] == '情侣戒'){
			$t = "钻石对戒";
		}elseif ($r['cat_type'] == '吊坠'){
			$t = "钻石吊坠";
		}elseif ($r['cat_type'] == '手链'){
			$t = "钻石手链";
		}else{
			$t = "其他";
		}
	}else{
		if ($r['caizhi'] == 'PT950'){
			$t = "素铂金";
		}elseif (substr($r['caizhi'],0,3) == '18k'){
			$t = "18k素金饰品";
		}elseif ($r['caizhi'] == '千足金'){
			if ($r['cat_type'] == '金条'){
				$t = "黄金金条";
			}else{
				$t = "千足金饰品";
			}
		}else{
			$t = "其他";
		}
	}
	return $t;
}
 ?>