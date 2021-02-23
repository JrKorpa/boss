<?php
/**
脚本要求：
名词解释：

1.每天晚上定时跑脚本，数据库存储库存数据。
*/
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require_once('MysqlDB.class.php');
set_time_limit(0);
ini_set('memory_limit','2000M');
$singleAdd = false;

$doTest = false;

if($doTest){
    $new_conf = [
        'dsn'=>"mysql:host=192.168.0.91;dbname=kucun_bak",
        'user'=>"root",
        'password'=>"123456",
        'charset' => 'utf8'
    ];
    $new_conf2 = [
        'dsn'=>"mysql:host=127.0.0.1;dbname=kucun_bak",
        'user'=>"root",
        'password'=>"123456",
        'charset' => 'utf8'
    ];
}else{

    $new_conf = [
        'dsn'=>"mysql:host=192.168.1.59;dbname=warehouse_shipping",
        'user'=>"cuteman",
        'password'=>"QW@W#RSS33#E#",
        'charset' => 'utf8'
    ];
    $new_conf2 = [
        'dsn'=>"mysql:host=192.168.1.72;dbname=kucun_bak",
        'user'=>"kucun_bak",
        'password'=>"n5FRLp0rAbhPpNKV",
        'charset' => 'utf8'
    ];
}

$day = 1 ;
$t = time()-86400*$day;
$date = date("Y-m-d",$t);


$db = new MysqlDB($new_conf);
$dbB = new MysqlDB($new_conf2);

//是否需要执行
$sql = "SELECT do FROM `kucun_bak`.`kucunliang` WHERE dotime = '".date('Y-m-d')."';";
$do = $dbB->getOne($sql);
if($do == 1){
    exit("已执行成功!");
}

//开始执行
$sql = "INSERT INTO `kucun_bak`.`kucunliang` (`id`, `dotime`, `do`,`s_time`,`e_time`) VALUES (NULL, '".date('Y-m-d')."', '0','".date('Y-m-d H:i:s')."','0000-00-00 00:00:00');";
$dbB->query($sql);


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
	$content = "货号,入库方式,款号,模号,名称,张萌类型,款式类型,产品线,系列,状态,主成色,主成色重,主石,主石重,主石颜色,主石净度,切工,抛光,对称,荧光,主石粒数,副石,指圈,金托类型,供应商,公司,仓库,仓库类型,本库库龄,总库龄,成本,证书类型,证书号,BDD订单号,订单付款状态,品牌,裸钻证书类型,系列及款式归属,副石1,副石1粒数,副石1重,副石2,副石2粒数,副石2重,新产品线,新款式分类,原始成本价,名义成本,最新采购价,主石形状,主石规格,主石买入单价,主石买入成本,主石计价单价,副石买入单价,副石买入成本,副石计价单价,副石2买入单价,副石2买入成本,副石2计价单价,数量,是否结价,是否绑定,戒托实际镶口,维修状态,维修公司,维修仓库,金耗,最后销售时间,国际报价,折扣,金饰类型,供应商货品条码,柜位,公司id,库房id,仓库类型id,上架时间,入库时间,入新库时间";
    $content2 = "huohao,rukufangshi,kuanhao,mohao,mingcheng,zhangmengleixing,kuanshileixing,chanpinxian,xilie,zhuangtai,zhuchengse,zhuchengsechong,zhushi,zhushichong,zhushiyanse,zhushijingdu,qiegong,paoguang,duichen,yingguang,zhushilishu,fushi,zhijuan,jintuoleixing,gongyingshang,gongsi,cangku,cangkuleixing,benkukuling,zongkuling,chengben,zhengshuleixing,zhengshuhao,kelandingdanhao,dingdanfukuanzhuangtai,pinpai,luozuanzhengshuleixing,xiliejikuanshiguishu,fushi1,fushi1lishu,fushi1chong,shi2,shi2lishu,shi2chong,xinchanpinxian,xinkuanshifenlei,yuanshichengbenjia,mingyichengben,zuixincaigoujia,zhushixingzhuang,zhushiguige,zhushimairudanjia,zhushimairuchengben,zhushijijiadanjia,fushimairudanjia,fushimairuchengben,fushijijiadanjia,shi2mairudanjia,shi2mairuchengben,shi2jijiadanjia,num,jiejia,order_goods_id,jietuoxiangkou,weixiu_status,weixiu_company_name,weixiu_warehouse_name,jinhao,zuihouxiaoshoushijian,guojibaojia,zuanshizhekou,jinshileixing,supplier_code,box_sn,company_id,warehouse_id,cangkuleixing_id,shangjiashijian,rukushijian,ruxinkushijian";
    
    $c_i = explode(',',$content);
    $c2_i = explode(',',$content2);

    $c_c = count($c_i);

    $table = "warehouse_goods".date("Ymd");
    $str = "create table IF NOT exists ".$table."(\r\n";
    foreach($c_i as $key => $v){
        $str .= "      `".$c2_i[$key]."` varchar(100) DEFAULT NULL COMMENT '".$v."'";
        //if($key < $c_c-1){
            $str .= ",";
        //}
        $str .= "\r\n";
    }
    $str .= "  PRIMARY KEY (`huohao`)\r\n";
    
    $str .= ");";
    $sql = "drop table IF exists $table;";
    $dbB->query($sql);

    $ret = $dbB->query($str);

$page = 1;
$limit = 1000;

while(1){
	$start = ($page - 1) * $limit;
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
            	g.prc_name,
            	g.mo_sn,
            	g.company, 
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
            	if(g.`change_time` = '0000-00-00 00:00:00', 0, (UNIX_TIMESTAMP( NOW( ) ) - UNIX_TIMESTAMP( g.`change_time` ) ) / ( 24 *3600 ) ) AS thisage ,
            	if(g.`addtime` = '0000-00-00 00:00:00', 0, (UNIX_TIMESTAMP( NOW( ) ) - UNIX_TIMESTAMP( g.`addtime` ) ) / ( 24 *3600 )) AS companyage ,
            	g.product_type1,
            	g.cat_type1,
                g.yuanshichengbenjia,g.mingyichengben,g.chengbenjia zuixincaigoujia,
                g.zhushixingzhuang,g.zhushiguige,g.zhushimairudanjia,g.zhushimairuchengben,g.zhushijijiadanjia,
                g.fushimairudanjia,g.fushimairuchengben,g.fushijijiadanjia,
                g.shi2mairudanjia,g.shi2mairuchengben,g.shi2jijiadanjia,
                g.num,g.jiejia,g.order_goods_id,
                g.jietuoxiangkou,g.weixiu_status,g.weixiu_company_name,g.weixiu_warehouse_name,g.jinhao,
                1 as zuihouxiaoshoushijian,g.guojibaojia,g.zuanshizhekou,g.ziyin jinshileixing,g.supplier_code,g.box_sn,company_id,g.warehouse_id,w.type cangkuleixing_id,
                g.addtime rukushijian,g.change_time ruxinkushijian,gage.last_onshelf_dt shangjiashijian

    FROM warehouse_shipping.`warehouse_goods` AS g 
    inner join warehouse_shipping.warehouse_goods_age AS gage on g.id = gage.warehouse_id
    left join front.base_style_info as s ON (s.style_sn = g.goods_sn),
    	warehouse_shipping.warehouse AS w
	WHERE g.warehouse_id = w.id AND g.is_on_sale IN ( 1,2,4,5,6,8,10,11 ) order by g.goods_id desc limit $start, $limit";
	$ret = $db->getAll($sql);
	if ($ret == null){
		break;
	}

    

    $insertData = array();
	foreach($ret as $r){
	    $kelaorderinfo=array('order_sn'=>'','order_amount'=>'');
	    if($r['type'] == 3){//待取状态 去获取BDD订单号和支付状态
    	    $sql = "select bill_id from warehouse_shipping.warehouse_bill_goods where goods_id = '".$r['goods_id']."' AND bill_type = 'M' order by id desc limit 1 ";
    	    $order_id = $db->getOne($sql);
    	    if($order_id){
    	        $sql = "select order_sn from warehouse_shipping.warehouse_bill where id = $order_id  ";
    	        $kela_order_sn = $db->getOne($sql);
    	        if($kela_order_sn){
    	            $sql = "select order_amount,order_sn from app_order.app_order_account as oa
                    inner join app_order.base_order_info as oi on  oi.id = oa.order_id
                    where oa.order_id = $order_id  ";
    	            $tmp = $db->getRow($sql);
    	            $kelaorderinfo['order_sn'] = $tmp['order_sn'];
    	            $kelaorderinfo['order_amount'] = $tmp['order_amount'] > 0 ? '定金' : '全款';
    	        }
    	    }
	    }
         $sql="select xilie from front.base_style_info where style_sn='{$r['goods_sn']}' limit 0,1";
                    
         $xilie = $db->getOne($sql);
         if (!empty($xilie)) $xilie = trim(trim($xilie), ',');
         if (!empty($xilie)) {
             $sql = "select name from front.app_style_xilie where id in ({$xilie})";
             $xilie_name = $db->getAll($sql);
         } else {
             $xilie_name = '';
         }                 
                    
         $name = '';
         if(!empty($xilie_name))
        {
        foreach ($xilie_name as $kk => $v){
             $name .= $v['name'].' ';
           }
         }
        
	    if($r['is_on_sale'] == 3){//待取状态 去获取BDD订单号和支付状态
    	    $sql = "select wb.check_time 
            from warehouse_shipping.warehouse_bill wb 
            inner join warehouse_bill_goods wbg on wb.bill_no = wbg.bill_no 
            where wb.bill_status = 2 AND goods_id = '".$r['goods_id']."' AND wb.bill_type = 'S' order by wb.id desc limit 1 ";
            echo $sql;
    	    $check_time = $db->getOne($sql);
    	    if($check_time){
    	        $r['zuihouxiaoshoushijian'] = $check_time;
            }else{
                $r['zuihouxiaoshoushijian'] = '0000-00-00 00:00:00';
            }
	    }else{
            $r['zuihouxiaoshoushijian'] = '0000-00-00 00:00:00';
        }

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
		$chengbenjia = $r['yuanshichengbenjia'];
		$zhengshuhao = $r['zhengshuhao'];
		$zhengshuleibie = $r['zhengshuleibie'];
		$mo_sn = $r['mo_sn'];
		$zhangmeng_type = getZhangmengType($r);
		$prc_name = $r['prc_name'];
		$tuo = getTuoType($r['tuo_type']);
        $fushi = $r['fushi'];
		$fushilishu = $r['fushilishu'];
		$fushizhong = $r['fushizhong'];
		$shi2 = $r['shi2'];
		$shi2lishu = $r['shi2lishu'];
		$shi2zhong = $r['shi2zhong'];
		$product_type_1 = $r['product_type1'];
		$cat_type_1 = $r['cat_type1'];


		$str = $goods_id . "||" .
			$storage_mode_array[$storage_mode] . "||" .
			$goods_sn . "||" .
			$mo_sn . "||" .
			$goods_name . "||" .
			$zhangmeng_type . "||" .
			$cat_type . "||" .
			$product_type . "||" .
			$xilie2 . "||" .
			$is_on_sale . "||" .
			$caizhi . "||" .

			$caizhizhong . "||" .
			$zhushi . "||" .
			$zhushizhong . "||" .
			$zhushiyanse . "||" .
			$zhushijingdu . "||" .
			$qiegong . "||" .
			$paoguang . "||" .
			$duichen . "||" .
			$yingguang . "||" .
			$zhushilishu . "||" .
			$fushizhong . "||" .
			$shoucun . "||" .
			$tuo . "||" .
			$prc_name . "||" .
			$p_name . "||" .
			$wh_name . "||" .
			$wh_type . "||" .
			//$age . "||" .
			$thisage . "||" .
			$companyage . "||".
			$chengbenjia . "||" .
			$zhengshuleibie . "||" .
			$zhengshuhao . "||" .
			$kelaorderinfo['order_sn'] . "||" .
		    $kelaorderinfo['order_amount']. "||".
			 
			$pinpai . "||" .
			$luozuanzhengshu . "||" .
			
			$name. "||".
			$fushi . "||" .
			$fushilishu . "||" .
			$shi2zhong . "||" .
			$shi2 . "||" .
			$shi2lishu . "||" .
			$shi2zhong . "||" .
			$product_type_1. "||".
			$cat_type_1. "||" .


            $r['yuanshichengbenjia'] . "||" .
            $r['mingyichengben'] . "||" .
            $r['zuixincaigoujia'] . "||" .
            $r['zhushixingzhuang'] . "||" .
            $r['zhushiguige'] . "||" .
            $r['zhushimairudanjia'] . "||" .
            $r['zhushimairuchengben'] . "||" .
            $r['zhushijijiadanjia'] . "||" .
            $r['fushimairudanjia'] . "||" .
            $r['fushimairuchengben'] . "||" .
            $r['fushijijiadanjia'] . "||" .
            $r['shi2mairudanjia'] . "||" .
            $r['shi2mairuchengben'] . "||" .
            $r['shi2jijiadanjia'] . "||" .
            $r['num'] . "||" .
            $r['jiejia'] . "||" .
            $r['order_goods_id'] . "||" .
            $r['jietuoxiangkou'] . "||" .
            $r['weixiu_status'] . "||" .
            $r['weixiu_company_name'] . "||" .
            $r['weixiu_warehouse_name'] . "||" .
            $r['jinhao'] . "||" .
            $r['zuihouxiaoshoushijian'] . "||" .
            $r['guojibaojia'] . "||" .
            $r['zuanshizhekou'] . "||" .
            $r['jinshileixing'] . "||" .
            $r['supplier_code'] . "||" .
            $r['box_sn']  . "||" .
            $r['company_id']  . "||" .
            $r['warehouse_id'] . "||" .
            $r['cangkuleixing_id'] . "||" .
            $r['shangjiashijian'] . "||" .
            $r['rukushijian'] . "||" .
            $r['ruxinkushijian'] ;
            
            $ret = explode('||',$str);

            $insertData[] = "('".implode("','",$ret)."')";

            if($singleAdd){
                $insert_sql="insert into $table (".implode(',',$c2_i).") values ".implode(",",$insertData)."";
                $dbB->query($insert_sql);
            }
	}
    if(!$singleAdd){
        if($insertData){
            $insert_sql="insert into $table (".implode(',',$c2_i).") values ".implode(",",$insertData)."";
            $dbB->query($insert_sql);
        }
    }
	$page++;
}

//执行结束
$sql = "update `kucun_bak`.`kucunliang` set do=1,e_time='".date('Y-m-d H:i:s')."' WHERE dotime = '".date('Y-m-d')."';";
$do = $dbB->query($sql);
exit("OVER");


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



