<?php
/**
脚本要求：
名词解释：

1.每天晚上定时跑脚本，备份库存数据。
*/

	header("Content-type:text/html;charset=utf-8");
	date_default_timezone_set('Asia/Shanghai');
	require_once('MysqlDB.class.php');
	set_time_limit(0);
	ini_set('memory_limit','2000M');

//$new_mysqli=new mysqli('192.168.1.93','cuteman','QW@W#RSS33#E#','warehouse_shipping') or die("数据库连接失败！") ;
$new_conf = [
		'dsn'=>"mysql:host=192.168.1.132;dbname=warehouse_shipping",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
];
$new_conf1 = [
		'dsn'=>"mysql:host=192.168.1.132;dbname=app_order",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
];
$db = new MysqlDB($new_conf);
//$dbR = new MysqlDB($new_conf1);
//$company_ids = getTydCompanyIds($db);

	$content = "货号,入库方式,款号,模号,名称,张萌类型,款式类型,产品线,系列,状态,主成色,主成色重,主石,主石重,主石颜色,主石净度,切工,抛光,对称,荧光,主石粒数,副石,指圈,金托类型,供应商,公司,仓库,仓库类型,本库库龄,总库龄,名义成本,原始成本,加价成本,证书类型,证书号,BDD订单号,订单付款状态,品牌,裸钻证书类型,系列及款式归属,副石1,副石1粒数,副石1重,副石2,副石2粒数,副石2重,新产品线,新款式分类,供应商货品条码,星耀钻石\n";
	$content = iconv("utf-8","gbk",$content);
	//file_put_contents(__DIR__."/kucun/xin_kucunproline" . $dd . ".csv",$content,FILE_APPEND);

$page = 1;
$limit = 200;
$dd = date("Ymd");
while(1){
	$start = ($page - 1) * $limit;
	echo $start . "\n";
	$sql = "select b.bill_no,b.send_goods_sn,g.buchan_sn,g.goods_id,g.goods_sn,g.mo_sn,g.product_type1,g.cat_type1,g.caizhi,g.jinzhong,g.jinhao,
g.zhuchengsezhongjijia,g.zhuchengsemairudanjia,g.zhuchengsemairuchengben,g.zhuchengsejijiadanjia,
g.zhushi, g.zhushilishu, g.zuanshidaxiao, g.zhushizhongjijia,
 g.zhushiyanse, g.zhushijingdu, g.zhushimairudanjia, 
g.zhushimairuchengben, g.zhushijijiadanjia, g.zhushiqiegong, g.zhushixingzhuang, g.zhushibaohao, 
g.zhushiguige, g.fushi, g.fushilishu, g.fushizhong, g.fushizhongjijia, g.fushiyanse, g.fushijingdu, g.fushimairudanjia,
 g.fushimairuchengben, g.fushijijiadanjia, g.fushixingzhuang, g.fushibaohao, g.fushiguige, g.zongzhong, g.mairugongfeidanjia,
 g.mairugongfei, g.jijiagongfei, g.shoucun, g.danjianchengben,
 g.peijianchengben,g.peijianjinchong,g.qitachengben, g.chengbenjia, g.jijiachengben, 
 if(g.tuo_type=1,'成品','空托') as tuo_type,b.pro_name,
 if(g.zuanshidaxiao>=0 and g.shoucun<>'',
   if(
      (select x.stone from front.app_xiangkou x where round(x.stone*1-0.05,3) <= g.zuanshidaxiao and g.zuanshidaxiao<= round(x.stone*1+0.04,3) and substring_index(x.finger,'-',1)*1 <= g.shoucun*1  and g.shoucun*1 <= substring_index(x.finger,'-',-1)*1 and style_sn=g.goods_sn order by abs(x.stone*1-g.zuanshidaxiao) limit 1) is null,
      (select if(left(g.caizhi,3)='18K',concat(x.g18_weight-x.g18_weight_more2,'-',x.g18_weight+x.g18_weight_more),if(left(g.caizhi,5)='PT950',concat(x.gpt_weight-x.gpt_weight_more2,'-',x.gpt_weight+x.gpt_weight_more),'') ) from front.app_xiangkou x where round(x.stone*1-0.05,3) <= g.zuanshidaxiao and g.zuanshidaxiao<= round(x.stone*1+0.04,3) and substring_index(x.finger,'-',1)*1 <= round(g.shoucun*1,0)  and round(g.shoucun*1,0) <= substring_index(x.finger,'-',-1)*1 and style_sn=g.goods_sn order by abs(x.stone*1-g.zuanshidaxiao) limit 1 )
      ,  
      (select if(left(g.caizhi,3)='18K',concat(x.g18_weight-x.g18_weight_more2,'-',x.g18_weight+x.g18_weight_more),if(left(g.caizhi,5)='PT950',concat(x.gpt_weight-x.gpt_weight_more2,'-',x.gpt_weight+x.gpt_weight_more),'') ) from front.app_xiangkou x where round(x.stone*1-0.05,3) <= g.zuanshidaxiao and g.zuanshidaxiao<= round(x.stone*1+0.04,3) and substring_index(x.finger,'-',1)*1 <= g.shoucun*1  and g.shoucun*1 <= substring_index(x.finger,'-',-1)*1 and style_sn=g.goods_sn order by abs(x.stone*1-g.zuanshidaxiao) limit 1 )
      ) ,
    ( select if(left(g.caizhi,3)='18K',concat(x.g18_weight-x.g18_weight_more2,'-',x.g18_weight+x.g18_weight_more),if(left(g.caizhi,5)='PT950',concat(x.gpt_weight-x.gpt_weight_more2,'-',x.gpt_weight+x.gpt_weight_more),'') ) from front.app_xiangkou x where x.stone*1 = g.jietuoxiangkou  and substring_index(x.finger,'-',1)*1 <= g.shoucun*1  and g.shoucun*1 <= substring_index(x.finger,'-',-1)*1 and x.style_sn=g.goods_sn )
 ) as biaozhunjinzhong,
 if(g.zuanshidaxiao>=0 and g.shoucun<>'',      
   (select concat(min(g2.jinzhong),'-',max(g2.jinzhong)) from warehouse_shipping.warehouse_goods g2 where g2.is_on_sale in (1,2,3,4,5,6,8,10,11) and g2.goods_sn=g.goods_sn and g2.caizhi=g.caizhi and  g2.shoucun=g.shoucun and ((g2.zuanshidaxiao >=(select round(att_value_name*1-0.05,3) as stonemin from front.app_attribute_value where attribute_id=1 and att_value_status=1 and  round(att_value_name*1-0.05,3) <= g.zuanshidaxiao and g.zuanshidaxiao <= round(att_value_name*1+0.04,3) order by abs(g.zuanshidaxiao-att_value_name*1) limit 1)   and g2.zuanshidaxiao <=(select round(att_value_name*1+0.04,3)  from front.app_attribute_value where attribute_id=1 and att_value_status=1 and  round(att_value_name*1-0.05,3) <= g.zuanshidaxiao and g.zuanshidaxiao <= round(att_value_name*1+0.04,3) order by abs(g.zuanshidaxiao-att_value_name*1) limit 1) )  or (g2.jietuoxiangkou-0.05<= g.zuanshidaxiao and g2.jietuoxiangkou+0.04 >= g.zuanshidaxiao))) , ''  ) as lishijinzhong
 from warehouse_shipping.warehouse_bill_goods bg,warehouse_shipping.warehouse_bill b,warehouse_shipping.warehouse_goods g where bg.bill_id=b.id and bg.goods_id=g.goods_id and b.bill_type='L' and b.bill_status=2 and b.check_time>'2017-06-01 00:00:00' and b.check_time<'2017-07-01 00:00:00' limit $start, $limit";
	$ret = $db->getAll($sql);
	if ($ret == null){
		break;
	}
	foreach($ret as $r){
	   
		$str =$r['bill_no'].",".
			iconv("utf-8","gbk",$r['send_goods_sn'] ). "," .
			iconv("utf-8","gbk",$r['buchan_sn'] ). "," .
			iconv("utf-8","gbk",$r['goods_id'] ). "," .
			iconv("utf-8","gbk",$r['goods_sn'] ). "," .
			iconv("utf-8","gbk",$r['mo_sn'] ). "," .
			iconv("utf-8","gbk",$r['product_type1'] ). "," .
			iconv("utf-8","gbk",$r['cat_type1'] ). "," .
			iconv("utf-8","gbk",$r['caizhi'] ). "," .
			iconv("utf-8","gbk",$r['jinzhong'] ). "," .
			iconv("utf-8","gbk",$r['jinhao'] ). "," .
			iconv("utf-8","gbk",$r['zhuchengsezhongjijia'] ). "," .
			iconv("utf-8","gbk",$r['zhuchengsemairudanjia'] ). "," .
			iconv("utf-8","gbk",$r['zhuchengsemairuchengben'] ). "," .
			iconv("utf-8","gbk",$r['zhuchengsejijiadanjia'] ). "," .
			iconv("utf-8","gbk",$r['zhushi'] ). "," .
			iconv("utf-8","gbk",$r['zhushilishu'] ). "," .
			iconv("utf-8","gbk",$r['zuanshidaxiao'] ). "," .
			iconv("utf-8","gbk",$r['zhushizhongjijia'] ). "," .
			iconv("utf-8","gbk",$r['zhushiyanse'] ). "," .
			iconv("utf-8","gbk",$r['zhushijingdu']) . "," .
			iconv("utf-8","gbk",$r['zhushimairudanjia'] ). "," .
			iconv("utf-8","gbk",$r['zhushimairuchengben'] ). "," .
			iconv("utf-8","gbk",$r['zhushijijiadanjia'] ). "," .
			iconv("utf-8","gbk",$r['zhushiqiegong'] ). "," .
			iconv("utf-8","gbk",$r['zhushixingzhuang'] ). "," .
			iconv("utf-8","gbk",$r['zhushibaohao'] ). "," .
			iconv("utf-8","gbk",$r['zhushiguige'] ). "," .
			iconv("utf-8","gbk",$r['fushi'] ). "," .
			iconv("utf-8","gbk",$r['fushilishu'] ). "," .
			iconv("utf-8","gbk",$r['fushizhong'] ). "," .
			iconv("utf-8","gbk",$r['fushizhongjijia'] ). "," .
			iconv("utf-8","gbk",$r['fushiyanse'] ). "," .
			iconv("utf-8","gbk",$r['fushijingdu'] ). "," .
			iconv("utf-8","gbk",$r['fushimairudanjia'] ). "," .
			iconv("utf-8","gbk",$r['fushimairuchengben'] ). "," .
			iconv("utf-8","gbk",$r['fushijijiadanjia'] ). "," .
			iconv("utf-8","gbk",$r['fushixingzhuang'] ). "," .
			iconv("utf-8","gbk",$r['fushibaohao'] ). "," .
			iconv("utf-8","gbk",$r['fushiguige'] ). "," .
			iconv("utf-8","gbk",$r['zongzhong'] ). "," .
			iconv("utf-8","gbk",$r['mairugongfeidanjia'] ). "," .
			iconv("utf-8","gbk",$r['mairugongfei'] ). "," .
			iconv("utf-8","gbk",$r['jijiagongfei'] ). "," .
			iconv("utf-8","gbk",$r['shoucun'] ). "," .
			iconv("utf-8","gbk",$r['danjianchengben'] ). "," .
			iconv("utf-8","gbk",$r['peijianchengben'] ). "," .
			iconv("utf-8","gbk",$r['peijianjinchong'] ). "," .
			iconv("utf-8","gbk",$r['qitachengben'] ). "," .
			iconv("utf-8","gbk",$r['chengbenjia'] ). "," .
			iconv("utf-8","gbk",$r['jijiachengben'] ). "," .
			iconv("utf-8","gbk",$r['tuo_type'] ). "," .
			iconv("utf-8","gbk",$r['pro_name'] ). "," .
			iconv("utf-8","gbk",$r['biaozhunjinzhong'] ). "," .
			iconv("utf-8","gbk",$r['lishijinzhong'] ). "\n" ;

						 

		file_put_contents(__DIR__."/kucun/jinzhong" . $dd . ".csv",$str,FILE_APPEND);
	}
	$page++;
}
?>