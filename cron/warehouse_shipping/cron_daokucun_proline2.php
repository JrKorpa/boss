<?php
/**
脚本要求：
名词解释：

1.每天晚上定时跑脚本，备份库存数据。
*/

	header("Content-type:text/html;charset=utf-8");
	date_default_timezone_set('Asia/Shanghai');
    define('ROOT_PATH', str_replace('cron_daokucun_proline2.php', '', str_replace('\\', '/', __FILE__)));
    define('KELA_ROOT', str_replace('\\','/',realpath(rtrim(ROOT_PATH,'/').'/../../')));//定义网站根目录
    require_once('MysqlDB.class.php');
    require(KELA_ROOT."/frame/common/data_config.php"); //网站配置
	set_time_limit(0);
	ini_set('memory_limit','2000M');

//$new_mysqli=new mysqli('192.168.1.93','cuteman','QW@W#RSS33#E#','warehouse_shipping') or die("数据库连接失败！") ;
$new_conf = [
		'dsn'=>"mysql:host=192.168.1.192;dbname=warehouse_shipping",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
];
$new_conf1 = [
		'dsn'=>"mysql:host=192.168.1.192;dbname=app_order",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
];
$db = new MysqlDB($new_conf);
$dbR = new MysqlDB($new_conf1);
$company_ids = getTydCompanyIds($db);

$sql="UPDATE warehouse_goods SET xinyaozhanshi=2 WHERE xinyaozhanshi != 2 and zhengshuleibie = 'HRD-S'";
$db->query($sql);
$sql="UPDATE warehouse_goods SET xinyaozhanshi=1 WHERE xinyaozhanshi != 1 and zhengshuleibie != 'HRD-S' and addtime>= '2016-05-13 00:00:00'";
$db->query($sql);

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
		'5'=>'自采'
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
	$content = "货号,入库方式,款号,模号,名称,张萌类型,款式类型,产品线,系列,状态,主成色,主成色重,主石形状,主石,主石重,主石颜色,主石净度,切工,抛光,对称,荧光,主石粒数,副石,指圈,金托类型,供应商,公司,仓库,仓库类型,本库库龄,总库龄,名义成本,原始成本,舟山原始成本,加价成本,证书类型,证书号,BDD订单号,订单付款状态,品牌,裸钻证书类型,系列及款式归属,副石1,副石1粒数,副石1重,副石2,副石2粒数,副石2重,新产品线,新款式分类,供应商货品条码,星耀钻石,税率\n";
	$content = iconv("utf-8","gbk",$content);
	file_put_contents(__DIR__."/kucun/xin_kucunproline" . $dd . ".csv",$content,FILE_APPEND);

$where = "";
if(SYS_SCOPE == 'zhanting'){
    $where.=" and g.company_id in ( select id from cuteframe.company where company_type=2 and id<>488 or id=58) ";
}

$page = 1;
$limit = 1000;

while(1){
	$start = ($page - 1) * $limit;
	echo $start . "\n";
	$sql = "SELECT
	           g.goods_id,
	           g.put_in_type,
	           g.goods_sn,
	           g.goods_name,
	           g.tuo_type,
	           g.cat_type,
	           g.zhushimairuchengben,
	           g.product_type,g.is_on_sale,s.xilie,
            	g.caizhi,
            	g.jinzhong,
            	g.zhushixingzhuang,
            	g.zhushi,
            	g.zuanshidaxiao,
            	g.zhushiyanse,
            	g.zhushijingdu,
            	g.qiegong,
            	g.paoguang,
            	g.duichen,
            	g.yingguang,
            	g.zhengshuhao,
            	g.zhengshuleibie,
            	g.zhushilishu,
            	g.fushizhong,
            	g.shoucun,
                g.prc_id,
            	g.prc_name,
            	g.mo_sn,
            	g.company, 
            	g.company_id,
			    g.pinpai,
				g.luozuanzhengshu,
				g.fushilishu,
				g.fushizhong,
				g.fushi,
				g.shi2,
				g.shi2lishu,
				g.shi2zhong,
            	w.name,
            	w.type,
				case when i.in_time is null then 0 else ceil((UNIX_TIMESTAMP( NOW( ) ) - UNIX_TIMESTAMP( i.in_time ) ) / ( 24 *3600 )) end as thisage,
            	if(g.`addtime` = '0000-00-00 00:00:00', 0, (UNIX_TIMESTAMP( NOW( ) ) - UNIX_TIMESTAMP( g.`addtime` ) ) / ( 24 *3600 )) AS companyage ,
            	g.yuanshichengbenjia,
                g.yuanshichengbenjia_zs,
                g.mingyichengben,
            	g.product_type1,
            	g.cat_type1,
                g.supplier_code,
				g.xinyaozhanshi,
				concat(g.tax_rate,'%') as tax_rate 
	FROM warehouse_shipping.`warehouse_goods` AS g left join front.base_style_info as s ON(s.style_sn = g.goods_sn)
	left join (
		select goods_id, warehouse_id, max(in_time) in_time from warehouse_shipping.goods_io where in_time is not null group by goods_id, warehouse_id 
	) i on i.goods_id = g.goods_id and i.warehouse_id = g.warehouse_id,
    	warehouse_shipping.warehouse AS w
	WHERE g.warehouse_id = w.id AND g.is_on_sale NOT IN ( 1, 3, 7, 9,11,12,100 ) AND g.goods_id NOT IN (150922095848,150922095849,150922095850,150922095851,150922095852,150922095853,150922095854,150922095855,150922095856,150922095857,150922095858,150922095859,150922095860,150922109034,150922109035,150922109036,150922109037,150922109038,150922109039,150922109040,150922109041,150922114971,150922114972,150922114973,150922114974,150922114975108070166) $where order by g.goods_id asc limit $start, $limit";
	$ret = $db->getAll($sql);
	if ($ret == null){
		break;
	}
	foreach($ret as $r){
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
		$sql="select xilie from front.base_style_info where style_sn='{$r['goods_sn']}' limit 0,1";
		$xilie = $db->getOne($sql);

		if (!empty($xilie)) $xilie = trim(trim($xilie), ',');
		if (!empty($xilie)) {
		    $sql = "select name from front.app_style_xilie where id in ('{$xilie}')";
		    $xilie_name = $db->getAll($sql);
		} else {
		    $xilie_name = '';
		}
		$name = '';
		if(!empty($xilie_name)) {
			foreach ($xilie_name as $kk => $v){
				$name .= $v['name'].' ';
			}
		}
		// 添加加价成本价,jiajiachengben
		addJiajiaChengben($db, $company_ids, $r);

		$pinpai = $r['pinpai'];
		$luozuanzhengshu = $r['luozuanzhengshu'];
		$goods_id = $r['goods_id'];
		$goods_sn = trim($r['goods_sn']);
		$goods_name = $r['goods_name'];
		$storage_mode = $r['put_in_type'];

		$cat_type = $r['cat_type'];
		$product_type = $r['product_type'];

		$xilie2 = @$xilie[$r['xilie']];
		$is_on_sale = @$goods_status[$r['is_on_sale']];
		$caizhi = $r['caizhi'];
		$caizhizhong = $r['jinzhong'];
        $zhushixingzhuang = $r['zhushixingzhuang'];
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
		$wh_type = @$warehouse_type[$r['type']];
		//$age = ceil($r['age']);
		$thisage = ceil($r['thisage']);
		$companyage = ceil($r['companyage']);
                $mingyichengben=$r['mingyichengben']; 
		$chengbenjia = $r['yuanshichengbenjia'];
		$chengbenjia_zs = $r['yuanshichengbenjia_zs'];
		$jiajiachengben = $r['jiajiachengben'];
		$zhengshuhao = $r['zhengshuhao'];
		$zhengshuleibie = $r['zhengshuleibie'];
		$mo_sn = $r['mo_sn'];
		$zhangmeng_type = getZhangmengType($r);
		$prc_name = $r['prc_id'];//prc_name
		$tuo = getTuoType($r['tuo_type']);
        $fushi = $r['fushi'];
		$fushilishu = $r['fushilishu'];
		$fushizhong = $r['fushizhong'];
		$shi2 = $r['shi2'];
		$shi2lishu = $r['shi2lishu'];
		$shi2zhong = $r['shi2zhong'];
		$product_type_1 = $r['product_type1'];
		$cat_type_1 = $r['cat_type1'];
        $supplier_code = $r['supplier_code'];
		$xinyaozhanshi=$r['xinyaozhanshi']==2 ? "是":"否";
		$tax_rate = $r['tax_rate'];
		$str = $goods_id . "," .
			iconv("utf-8","gbk",$storage_mode_array[$storage_mode]) . "," .
			iconv("utf-8","gbk",$goods_sn) . "," .
			iconv("utf-8","gbk",$mo_sn) . "," .
			iconv("utf-8","gbk",$goods_name) . "," .
			iconv("utf-8","gbk",$zhangmeng_type) . "," .
			iconv("utf-8","gbk",$cat_type) . "," .
			iconv("utf-8","gbk",$product_type) . "," .
			iconv("utf-8","gbk",$xilie2) . "," .
			iconv("utf-8","gbk",$is_on_sale) . "," .
			iconv("utf-8","gbk",$caizhi) . "," .

			$caizhizhong . "," .
            iconv("utf-8","gbk",$zhushixingzhuang) . "," .
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
			iconv("utf-8","gbk",$tuo) . "," .
			iconv("utf-8","gbk",$prc_name) . "," .
			iconv("utf-8","gbk",$p_name) . "," .
			iconv("utf-8","gbk",$wh_name) . "," .
			iconv("utf-8","gbk",$wh_type) . "," .
			//$age . "," .
			$thisage . "," .
			$companyage . ",".
                        $mingyichengben . ",".
			$chengbenjia . "," .
			$chengbenjia_zs . "," .
			$jiajiachengben . "," .
			iconv("utf-8","gbk",$zhengshuleibie) . "," .
			iconv("utf-8","gbk",$zhengshuhao) . "," .
			$kelaorderinfo['order_sn'] . "," .
		    iconv("utf-8","gbk",$kelaorderinfo['order_amount']). ",".
			 
			iconv("utf-8","gbk",$pinpai) . "," .
			iconv("utf-8","gbk",$luozuanzhengshu) . "," .
			
			iconv("utf-8","gbk",$name). ",".
			iconv("utf-8","gbk",$fushi) . "," .
			$fushilishu . "," .
			$shi2zhong . "," .
			iconv("utf-8","gbk",$shi2) . "," .
			$shi2lishu . "," .
			$shi2zhong . "," .
			iconv("utf-8","gbk",$product_type_1). ",".
			iconv("utf-8","gbk",$cat_type_1). ",".
			iconv("utf-8","gbk",$supplier_code). ",".
            iconv("utf-8","gbk",$xinyaozhanshi).",".
            iconv("utf-8","gbk",$tax_rate). "\n";
			 

		file_put_contents(__DIR__."/kucun/xin_kucunproline" . $dd . ".csv",$str,FILE_APPEND);
	}
	$page++;
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

function getTuoType($tuo_type) {
    switch ($tuo_type) {
        case '1' : return '成品';
        case '2' : return '空托女戒';
        case '3' : return '空托';
        default  : return  '';
    }
}

function getTydCompanyIds($db) {
	$sql="select company_id from cuteframe.sales_channels a join cuteframe.shop_cfg b on a.channel_own_id=b.id
			where shop_type=1 and a.is_deleted=0 and b.is_delete=0";
	$res = $db->getAll($sql);
	$company_ids = array_flip(array_unique(array_column($res, 'company_id')));
	// 排出：58总公司,223上海南京东路体验店,500广州天河分公司广晟大厦体验店, 501广州越秀分公司吉邦大厦体验店
	unset($company_ids['58'], $company_ids['223'], $company_ids['500'], $company_ids['501']);
	// 加上：445 柯兰深圳分公司仓库
	$company_ids['445'] = 1;

	return $company_ids;
}

function getMbillJiajialv($db, $goods_id, $company_id) {
	$sql="select jiajialv from warehouse_shipping.warehouse_bill_goods g join warehouse_shipping.warehouse_bill b on g.bill_id=b.id
			where b.bill_type='M' and goods_id={$goods_id} and to_company_id={$company_id} order by g.id desc limit 1";
	return $db->getOne($sql);
}

// 调拨单、S销售单：加价成本价
function addJiajiaChengben($db, $company_ids, &$item){
	// 默认等于 原始采购价
	$item['jiajiachengben'] = $item['yuanshichengbenjia'];
	if (isset($company_ids[$item['company_id']])) {
		$jiajialv = getMbillJiajialv($db, $item['goods_id'], $item['company_id']);
		if ($jiajialv) {
			$item['jiajiachengben'] = round($item['yuanshichengbenjia'] * (1 + $jiajialv/100),2);
		}
	}
}
