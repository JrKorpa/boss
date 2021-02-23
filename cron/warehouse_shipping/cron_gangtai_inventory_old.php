<?php
/**
脚本要求：
名词解释：

1.每天晚上定时跑脚本，传库存数据给刚泰。
*/

	header("Content-type:text/html;charset=utf-8");
	date_default_timezone_set('Asia/Shanghai');
	require_once('MysqlDB.class.php');
	set_time_limit(0);
	ini_set('memory_limit','2000M');

	//$new_mysqli=new mysqli('192.168.1.93','cuteman','QW@W#RSS33#E#','warehouse_shipping') or die("数据库连接失败！") ;
	$new_conf = [
			'dsn'=>"mysql:host=192.168.1.59;dbname=warehouse_shipping",
			'user'=>"cuteman",
			'password'=>"QW@W#RSS33#E#",
			'charset' => 'utf8'
	];

	$db = new MysqlDB($new_conf);
	$dd = date("Y-m-d");	
	$page = 1;
	$limit = 5000;    
    $rows=0;
$kela_arr=array();  

while(1){
	$start = ($page - 1) * $limit;
	echo $start . "\n";
    $sql="select '0016' as  ORGCODE,'' as ORGNAME,c.company_sn as filialecode,c.company_name as filialename,c.company_sn as shopcode,c.company_name as shopname,'' as countercode,'' as countername,
			(case g.put_in_type when 1 then '购买' when 2 then '委托加工' when 3 then '代销' when 4 then '借入' end) as inwarehousetype,
			(CASE g.is_on_sale WHEN 1 THEN '收货中' WHEN 2 THEN '库存' WHEN 3 THEN '已销售' WHEN 4 THEN '盘点中' WHEN 5 THEN '转仓中' WHEN 6 THEN '损益中' WHEN 7 THEN '已报损' WHEN 8 THEN '返厂中' WHEN 9 THEN '已返厂' WHEN 10 THEN '销售中' WHEN 11 THEN '退货中' WHEN 12 THEN '作废' WHEN 13 THEN '加价调拨中' ELSE '' END) as inwarehousestate,
			g.chuku_time as outwarehousetime,g.addtime as inwarehousetime,'' as warehousetype,'' as warehousemanager,g.warehouse_id as warehousecode,g.warehouse as warehousename,'' as cleardeptcode,'' as cleardeptname,
			g.goods_id as productcode,g.goods_name as productname,g.product_type1 as producttypecode,product_type1 as producttypename,g.goods_sn as stylecode,
			g.goods_sn as stylename,g.cat_type1 as styletypecode,g.cat_type1 as styletypename,
	      if(g.product_type1 in ('普通黄金','定价黄金','投资黄金','足金镶嵌'),'10',if(g.product_type1='钻石','20',if(g.product_type1 in ('彩钻','宝石'),'30',if(g.product_type1='PT','40',if(g.product_type1='K金','50',if(g.product_type1='银饰','60',if(g.product_type1='珍珠','70',if(g.product_type1='翡翠','80','90')))))))) as protypefirstcode,
	      if(g.product_type1 in ('普通黄金','定价黄金','投资黄金','足金镶嵌'),'黄金',if(g.product_type1='钻石','钻石',if(g.product_type1 in ('彩钻','宝石'),'彩宝',if(g.product_type1='PT','铂金',if(g.product_type1='K金','K金',if(g.product_type1='银饰','银饰',if(g.product_type1='珍珠','珍珠',if(g.product_type1='翡翠','翡翠','贵金属工艺品')))))))) as protypefirstname,
	      if(g.product_type1 in ('普通黄金','定价黄金','投资黄金','足金镶嵌'),if(g.cat_type1='金条','1030',if(g.cat_type1='摆件','1020','1010')),if(g.product_type1='钻石',if(g.cat_type1='裸石','2010','2020'),if(g.product_type1 in ('彩钻','宝石'),if(g.cat_type1='裸石','3010','3020'),if(g.product_type1='PT','4010',if(g.product_type1='K金','5010',if(g.product_type1='银饰','6010',if(g.product_type1='珍珠','7010',if(g.product_type1='翡翠','8010','9010')))))))) as protypesecondecode,
	      if(g.product_type1 in ('普通黄金','定价黄金','投资黄金','足金镶嵌'),if(g.cat_type1='金条','金条金币',if(g.cat_type1='摆件','黄金摆件','黄金饰品')),if(g.product_type1='钻石',if(g.cat_type1='裸石','裸石','成品'),if(g.product_type1 in ('彩钻','宝石'),if(g.cat_type1='裸石','裸石','成品'),if(g.product_type1='PT','铂金饰品',if(g.product_type1='K金','K金饰品',if(g.product_type1='银饰','银饰饰品',if(g.product_type1='珍珠','珍珠饰品',if(g.product_type1='翡翠','翡翠饰品','贵金属工艺品')))))))) as protypesecondename,
			'BDD' as brand,
			'' as seriescode,'' as seriesname,'' as seriescode_b,'' as seriesname_b,g.mo_sn as mouldcode,
			if(g.cat_type in ('裸石','彩钻'),concat(g.zuanshidaxiao,'克拉',g.zhushixingzhuang,'-',g.zhushiyanse,'-',g.zhushijingdu,'-',g.qiegong,'-',g.duichen,'-',g.paoguang,'-',g.yingguang),concat(g.goods_sn,'-',g.caizhi,'-',round(g.jietuoxiangkou*100,0),'-',g.shoucun)) as standard,
			'pcs' as unit ,g.zhengshuleibie as certificatetype,g.goods_id as anti_fakecode,g.zhengshuhao as certificatecode,
			g.caizhi as goldcolor,'' as golddegree,if(g.tuo_type=1,'成品','空托') as goldtype,g.zongzhong as oneweight,g.jinzhong as goldeweight,'' as lastweight,
			g.zhushi as mainstonerank,g.zhushi as mainstonename,g.zhushixingzhuang as mainstoneshape,g.zuanshidaxiao as mainstonesize,
			'' as mainstoneform,g.zhushiguige as mainstonestandard,'' as mainstonehome,g.zuanshidaxiao as mainstoneweight,g.zhushilishu as mainstonenum,g.zhushiyanse as mainstonecolor,
			g.caizhi as mainstonefineness,g.jinzhong as mainstonefinenessweight,g.zhushijingdu as puredegree,g.zhushiqiegong as cutdegree,
			g.paoguang as buffing,g.duichen as symmetry,g.yingguang as fluorescence,g.fushilishu as complementstonenum,g.fushizhong as complementstoneweight,
			g.shoucun as handsize,g.shoucun as fingerring,'' as normalprocess,'' as specialprocess,'' as note,
			g.prc_id as suppliercode,g.prc_name as suppliername,'' as supplierprocode,'' as supplierstylecode,
			g.mingyichengben as allcost,'' as oneprocesscost,'' as allprocesscost,'' as stickerprice,'' as saleprice,'' as saleprocesscost,
			'' as certificatecost,'' as othercost,'' as firstcost,'' as raisecost,'' as mainstoneprice,'' as clearprice,'' as marketprice,'' as agencyprice,
			If( (select max(i.in_time) in_time 	FROM warehouse_shipping.goods_io i 	WHERE  i.in_time is not null and i.goods_id=g.goods_id and i.warehouse_id=g.warehouse_id ) is null,
            0, ceil( (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP((select max(in_time) in_time FROM warehouse_shipping.goods_io i2 WHERE i2.in_time is not null and i2.goods_id=g.goods_id and i2.warehouse_id=g.warehouse_id))) / (24 * 3600)) ) as warehouseage,
            IF(g.`addtime` = '0000-00-00 00:00:00',	0,	ceil((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(g.`addtime`)	) / (24 * 3600))) as inventorydays,
			'' as inventorycost,'' as inventorymoney,'' as inventorystickermoney,g.num as inventorypronum, 0 as freezepronum,0 as checkpronum,
			if(g.is_on_sale=5,1,0) as inwayallotpronum,'' as budgetinpronum,if(g.is_on_sale=10,1,0) as waiteoutpronum,
			'' as inwayallotoutpronum,'' as budgetoutnum,'' as unqualifiedpronum,'' as qualifiedpronum,now() as dates
            from warehouse_shipping.warehouse_goods g left join cuteframe.company c on g.company_id=c.id  where g.is_on_sale in (2,4,5,6,8,10,11) and g.company_id in (223,297,300,365,489,58,445) limit $start, $limit";
	$ret = $db->getAll($sql);
	if ($ret == null){
		break;
	}
     

	$number_arr=array('oneweight','goldeweight','lastweight','mainstonenum','mainstonefinenessweight','complementstonenum','complementstoneweight','allcost','oneprocesscost','allprocesscost','stickerprice','saleprice','saleprocesscost','certificatecost','othercost','firstcost','raisecost','mainstoneprice','clearprice','marketprice','agencyprice','inventorycost','inventorymoney','inventorystickermoney','inventorypronum','freezepronum','checkpronum','inwayallotpronum','budgetinpronum','waiteoutpronum','inwayallotoutpronum','budgetoutnum','unqualifiedpronum','qualifiedpronum');

	foreach($ret as $r){
                        foreach (array_keys($r) as $k => $key) {
                            if(in_array($key,$number_arr)){
                                if(!is_numeric($r[$key])){
                                    $patterns = "/\d+/";
                                    preg_match_all($patterns,$r[$key],$arr);
                                    if($arr[0]){
                                        $r[$key]=$arr[0][0];
                                    }else
                                        $r[$key]=0;
                                }
                                if(empty($r[$key]))
                                    $r[$key]=0;
                            }
                        } 
            $kela_arr[]=$r;
	}
	$page++;
}


$ret=null;
unset($db);
if(empty($kela_arr))
	file_put_contents(__DIR__.'/gangtaiimport.log',date('Y-m-d H:i:s').'--Failed to get data from boss' . PHP_EOL,FILE_APPEND);


try {
            $dbms = new PDO ("dblib:host=211.152.47.12:1433;dbname=GTKG_BQ", "c##gtkg_klzs_user", "1");
            $dbms->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION); 

} catch (PDOException $e) {
	        file_put_contents(__DIR__.'/gangtaiimport.log',date('Y-m-d H:i:s').'--Failed to get DB handle'.$e->getMessage().PHP_EOL,FILE_APPEND);
            echo "Failed to get DB handle: " . $e->getMessage() . "\n";
            exit();
}


$res=$dbms->query("select count(0) from gtkg_repertory_detail_klzs where dates like '{$dd}%'");
if($res->fetchColumn()>0){
   file_put_contents(__DIR__.'/gangtaiimport.log',date('Y-m-d H:i:s').'--当天库存数据已写过,不能重写'.PHP_EOL,FILE_APPEND);
   exit();
}

$vals="";
$values="";
$k=0;
$times=0;


$rows=0;
$dbms->beginTransaction();
foreach ($kela_arr as $key => $val) {
    	try{			
	        $stmt=$dbms->prepare("insert into gtkg_repertory_detail_klzs values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
	        $res=$stmt->execute(array_values($val));	        
	        $rows++;
	        echo $rows. "\n"; 
        }catch (PDOException $e) {
        	$dbms->rollBack(); 
            file_put_contents(__DIR__.'/gangtaiimport.log',date('Y-m-d H:i:s').'--'.json_encode($val).PHP_EOL,FILE_APPEND);
            file_put_contents(__DIR__.'/gangtaiimport.log',date('Y-m-d H:i:s').'--Failed to execute:'.$e->getMessage().PHP_EOL,FILE_APPEND);        	
            echo "Failed to execute: " . $e->getMessage() . "\n";
            exit();
        }	
}
$dbms->commit();
unset($dbms);
exit();




