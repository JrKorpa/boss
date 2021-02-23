<?php
/**
脚本要求：
名词解释：

1.每月晚上定时跑脚本，传销售数据给刚泰。
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
    $sql="
  delete from warehouse_goods_sale_for_gt;
    insert into warehouse_goods_sale_for_gt (
ORGCODE,ORGNAME,filialecode,filialename,shopcode,shopname,countercode,countername,warehousekeeper,outwarehousecode,outwarehousename,custcode,custname,flag,salesordercode,saletype,checkcode,saledate,billmaker,billdate,checker,paymenttype,taximetertype,vipcode,integralnum,channeltypecode,channeltypename,channelcode,salechannel,channelcity,channelprovince,channelarea,productcode,productname,producttypecode,producttypename,stylecode,stylename,styletypecode,styletypename,protypefirstcode,protypefirstname,protypesecondecode,protypesecondename,brand,seriescode,seriesname,seriescode_b,seriesname_b,standard,unit,certificatetype,anti_fakecode,certificatecode,goldtype,oneweight,goldeweight,lastweight,mainstonename,mainstoneshape,mainstonesize,mainstoneform,mainstonestandard,mainstonehome,mainstoneweight,mainstonenum,mainstonecolor,mainstonefineness,mainstonefinenessweight,puredegree,cutdegree,buffing,symmetry,fluorescence,complementstonenum,complementstoneweight,handsize,fingerring,note,suppliercode,suppliername,firstsuppliercode,firstsuppliername,supplierprocode,supplierstylecode,saler1,saler1_royalty,saler2,saler2_royalty,num,price,discount,discountcoupon,cutchange,actualmoney,notaxmoney,taxmoney,taxrate,addmoney1,addmoney2,cutmoney1,normalprocess,specialprocess,allcost,allstonecost,stonecost,discountstonecost,goldcost,goldlose,firstcost,complementstonecost,complementstonenote,actualglodcost,processcost,othercost
)
select '0016' as  ORGCODE,'' as ORGNAME,c.company_sn as filialecode,c.company_name as filialename,c.company_sn as shopcode,c.company_name as shopname,'' as countercode,'' as countername,
			b.create_user as warehousekeeper,g.warehouse_id as outwarehousecode,g.warehouse as outwarehousename,
		     if(b.bill_type in ('S','D'),o.consignee,(select j.wholesale_name from warehouse_shipping.jxc_wholesale j where j.wholesale_id=b.to_customer_id)) as custcode,
		    if(b.bill_type in ('S','D'),o.consignee,(select j.wholesale_name from warehouse_shipping.jxc_wholesale j where j.wholesale_id=b.to_customer_id)) as custname,
		    if(b.bill_type in('D','H'),1,0) as flag,
            if(b.bill_type in ('S','D'),o.order_sn,b.bill_no) as salesordercode,
          b.bill_type as saletype,b.check_time as checkcode,b.check_time as saledate,b.create_user as billmaker,
          	b.create_time as billdate,b.check_user as checker, '' as paymenttype,'' as taximetertype,'' as vipcode,0 as integralnum,
          if(b.bill_type in ('P','H'),'10',  if(b.bill_type='S',if(b.from_company_id in (58,445),'20','30'),    if(b.to_company_id in (58,445),'20','30'))) as channeltypecode,
          if(b.bill_type in ('P','H'),'批发',if(b.bill_type='S',if(b.from_company_id in (58,445),'线上','线下'),if(b.to_company_id in (58,445),'线上','线下'))) as channeltypename,
          if(b.bill_type in ('P','H'),'1010',    if(b.bill_type='S',if(b.from_company_id not in (58,445),'3010',    if(o.department_id=2,'2010',if(o.department_id=71,'2020',if(o.customer_source_id=2034,'2030',  if(o.create_user='system_api','2040','2060'))))),    if(b.to_company_id   not in (58,445),'3010',if(o.department_id=2,'2010',if(o.department_id=71,'2020',if(o.customer_source_id=2034,'2030',if(o.create_user='system_api','2040','2060'))))) )) as channelcode, 
          if(b.bill_type in ('P','H'),'商品批发',if(b.bill_type='S',if(b.from_company_id not in (58,445),'直营门店',if(o.department_id=2,'天猫',if(o.department_id=71,'京东',if(o.customer_source_id=2034,'唯品会',if(o.create_user='system_api','直营电商','其他'))))),if(b.to_company_id   not in (58,445),'直营门店',if(o.department_id=2,'天猫',if(o.department_id=71,'京东',if(o.customer_source_id=2034,'唯品会',if(o.create_user='system_api','直营电商','其他'))))) )) as salechannel, 
          (select region_name from cuteframe.region r2 where r2.region_id=r.city_id) as channelcity,
          (select region_name from cuteframe.region r2 where r2.region_id=r.province_id) as channelprovince,
		  (select region_name from cuteframe.region r2 where r2.region_id=r.regional_id) as channelarea,
          g.goods_id as productcode,g.goods_name as productname,g.product_type1 as producttypecode,product_type1 as producttypename,g.goods_sn as stylecode,
	      g.goods_sn as stylename,g.cat_type1 as styletypecode,g.cat_type1 as styletypename,
	      if(g.product_type1 in ('普通黄金','定价黄金','投资黄金','足金镶嵌'),'10',if(g.product_type1='钻石','20',if(g.product_type1 in ('彩钻','宝石'),'30',if(g.product_type1='PT','40',if(g.product_type1='K金','50',if(g.product_type1='银饰','60',if(g.product_type1='珍珠','70',if(g.product_type1='翡翠','80','90')))))))) as protypefirstcode,
	      if(g.product_type1 in ('普通黄金','定价黄金','投资黄金','足金镶嵌'),'黄金',if(g.product_type1='钻石','钻石',if(g.product_type1 in ('彩钻','宝石'),'彩宝',if(g.product_type1='PT','铂金',if(g.product_type1='K金','K金',if(g.product_type1='银饰','银饰',if(g.product_type1='珍珠','珍珠',if(g.product_type1='翡翠','翡翠','贵金属工艺品')))))))) as protypefirstname,
	      if(g.product_type1 in ('普通黄金','定价黄金','投资黄金','足金镶嵌'),if(g.cat_type1='金条','1030',if(g.cat_type1='摆件','1020','1010')),if(g.product_type1='钻石',if(g.cat_type1='裸石','2010','2020'),if(g.product_type1 in ('彩钻','宝石'),if(g.cat_type1='裸石','3010','3020'),if(g.product_type1='PT','4010',if(g.product_type1='K金','5010',if(g.product_type1='银饰','6010',if(g.product_type1='珍珠','7010',if(g.product_type1='翡翠','8010','9010')))))))) as protypesecondecode,
	      if(g.product_type1 in ('普通黄金','定价黄金','投资黄金','足金镶嵌'),if(g.cat_type1='金条','金条金币',if(g.cat_type1='摆件','黄金摆件','黄金饰品')),if(g.product_type1='钻石',if(g.cat_type1='裸石','裸石','成品'),if(g.product_type1 in ('彩钻','宝石'),if(g.cat_type1='裸石','裸石','成品'),if(g.product_type1='PT','铂金饰品',if(g.product_type1='K金','K金饰品',if(g.product_type1='银饰','银饰饰品',if(g.product_type1='珍珠','珍珠饰品',if(g.product_type1='翡翠','翡翠饰品','贵金属工艺品')))))))) as protypesecondename,
		  'BDD' as brand,'' as seriescode,'' as seriesname,'' as seriescode_b,'' as seriesname_b,
		  if(g.cat_type in ('裸石','彩钻'),concat(g.zuanshidaxiao,'克拉',g.zhushixingzhuang,'-',g.zhushiyanse,'-',g.zhushijingdu,'-',g.qiegong,'-',g.duichen,'-',g.paoguang,'-',g.yingguang),concat(g.goods_sn,'-',g.caizhi,'-',round(g.jietuoxiangkou*100,0),'-',g.shoucun)) as standard,
		  'pcs' as unit ,g.zhengshuleibie as certificatetype,g.goods_id as anti_fakecode,g.zhengshuhao as certificatecode,
		   if(g.tuo_type=1,'成品','空托') as goldtype,CASE
WHEN ifnull(g.zongzhong, '') = '' THEN
	0
ELSE
	cast(g.zongzhong AS DECIMAL(18, 3))
END as oneweight,g.jinzhong as goldeweight,0 as lastweight,	
           g.zhushi as mainstonename,g.zhushixingzhuang as mainstoneshape,g.zuanshidaxiao as mainstonesize,
'' AS mainstoneform,
 g.zhushiguige AS mainstonestandard,
 '' AS mainstonehome,
 g.zuanshidaxiao AS mainstoneweight,
  CASE
WHEN ifnull(g.zhushilishu, '') = '' THEN
	0
ELSE
	cast(
		g.zhushilishu AS DECIMAL (18, 2)
	)
END AS mainstonenum,
 g.zhushiyanse AS mainstonecolor,

		   g.caizhi as mainstonefineness,g.jinzhong as mainstonefinenessweight,g.zhushijingdu as puredegree,g.zhushiqiegong as cutdegree,
		   g.paoguang as buffing,g.duichen as symmetry,g.yingguang as fluorescence,(
	CASE
	WHEN ifnull(g.fushilishu, '') = '' THEN
		0
	ELSE
		 0
	END
) as complementstonenum,g.fushizhong as complementstoneweight,            
		   g.shoucun as handsize,g.shoucun as fingerring,'' as note,	
           g.prc_id as suppliercode,g.prc_name as suppliername,'' as firstsuppliercode,'' as firstsuppliername, '' as supplierprocode,'' as supplierstylecode,  
           o.create_user as saler1,0 as saler1_royalty,'' as saler2,0 as saler2_royalty,
           bg.num as num,0 as price,0 as discount,0 as discountcoupon,0 as cutchange, bg.shijia as actualmoney,0 as notaxmoney,0 as taxmoney,0 as taxrate,0 as addmoney1,0 as addmoney2,0 as cutmoney1,
           '' as normalprocess,'' as specialprocess,
           g.yuanshichengbenjia as allcost,0 as allstonecost,0 as stonecost,0 as discountstonecost,0 as goldcost,0 as goldlose,0 as firstcost,0 as complementstonecost,'' as complementstonenote,0 as actualglodcost,0 as processcost,0 as othercost 
          from warehouse_shipping.warehouse_bill_goods bg,warehouse_shipping.warehouse_goods g,
	        warehouse_shipping.warehouse_bill b force index(bill_type_2)
	        left join app_order.base_order_info o on b.order_sn=o.order_sn
	        left join cuteframe.sales_channels s on o.department_id=s.id 
	        left join app_order.app_order_address r on o.id=r.order_id  
	        left join cuteframe.company c on if(b.bill_type in ('D','H'),b.to_company_id,b.from_company_id)=c.id 
	        where b.id=bg.bill_id and bg.goods_id=g.goods_id and b.bill_status=2 
            and b.check_time>'{$dd} 00:00:00'  
            and b.bill_type in ('D','H','S','P') and if(b.bill_type in ('S','P'),b.from_company_id,b.to_company_id) in (223,297,300,365,489,445,58);
";

$ret = $db->query($sql);

/*    
$kela_arr=array();    
while(1){
	$start = ($page - 1) * $limit;
	echo $start . "\n";
    $sql="select '0016' as  ORGCODE,'' as ORGNAME,c.company_sn as filialecode,c.company_name as filialename,c.company_sn as shopcode,c.company_name as shopname,'' as countercode,'' as countername,
			b.create_user as warehousekeeper,g.warehouse_id as outwarehousecode,g.warehouse as outwarehouse_name,
		     if(b.bill_type in ('S','D'),o.consignee,(select j.wholesale_name from warehouse_shipping.jxc_wholesale j where j.wholesale_id=b.to_customer_id)) as custcode,
		    if(b.bill_type in ('S','D'),o.consignee,(select j.wholesale_name from warehouse_shipping.jxc_wholesale j where j.wholesale_id=b.to_customer_id)) as custname,
		    if(b.bill_type in('D','H'),1,0) as flag,
            if(b.bill_type in ('S','D'),o.order_sn,b.bill_no) as salesordercode,
          b.bill_type as saletype,b.check_time as checkcode,b.check_time as saledate,b.create_user as billmaker,
          	b.create_time as billdate,b.check_user as checker, '' as paymenttype,'' as taximetertype,'' as vipcode,'' as integralnum,
          if(b.bill_type in ('P','H'),'10',  if(b.bill_type='S',if(b.from_company_id in (58,445),'20','30'),    if(b.to_company_id in (58,445),'20','30'))) as channeltypecode,
          if(b.bill_type in ('P','H'),'批发',if(b.bill_type='S',if(b.from_company_id in (58,445),'线上','线下'),if(b.to_company_id in (58,445),'线上','线下'))) as channeltypename,
          if(b.bill_type in ('P','H'),'1010',    if(b.bill_type='S',if(b.from_company_id not in (58,445),'3010',    if(o.department_id=2,'2010',if(o.department_id=71,'2020',if(o.customer_source_id=2034,'2030',  if(o.create_user='system_api','2040','2060'))))),    if(b.to_company_id   not in (58,445),'3010',if(o.department_id=2,'2010',if(o.department_id=71,'2020',if(o.customer_source_id=2034,'2030',if(o.create_user='system_api','2040','2060'))))) )) as channelcode, 
          if(b.bill_type in ('P','H'),'商品批发',if(b.bill_type='S',if(b.from_company_id not in (58,445),'直营门店',if(o.department_id=2,'天猫',if(o.department_id=71,'京东',if(o.customer_source_id=2034,'唯品会',if(o.create_user='system_api','直营电商','其他'))))),if(b.to_company_id   not in (58,445),'直营门店',if(o.department_id=2,'天猫',if(o.department_id=71,'京东',if(o.customer_source_id=2034,'唯品会',if(o.create_user='system_api','直营电商','其他'))))) )) as salechannel, 
          (select region_name from cuteframe.region r2 where r2.region_id=r.city_id) as channelcity,
          (select region_name from cuteframe.region r2 where r2.region_id=r.province_id) as channelprovince,
		  (select region_name from cuteframe.region r2 where r2.region_id=r.regional_id) as channelarea,
          g.goods_id as productcode,g.goods_name as productname,g.product_type1 as producttypecode,product_type1 as producttypename,g.goods_sn as stylecode,
	      g.goods_sn as stylename,g.cat_type1 as styletypecode,g.cat_type1 as styletypename,
	      if(g.product_type1 in ('普通黄金','定价黄金','投资黄金','足金镶嵌'),'10',if(g.product_type1='钻石','20',if(g.product_type1 in ('彩钻','宝石'),'30',if(g.product_type1='PT','40',if(g.product_type1='K金','50',if(g.product_type1='银饰','60',if(g.product_type1='珍珠','70',if(g.product_type1='翡翠','80','90')))))))) as protypefirstcode,
	      if(g.product_type1 in ('普通黄金','定价黄金','投资黄金','足金镶嵌'),'黄金',if(g.product_type1='钻石','钻石',if(g.product_type1 in ('彩钻','宝石'),'彩宝',if(g.product_type1='PT','铂金',if(g.product_type1='K金','K金',if(g.product_type1='银饰','银饰',if(g.product_type1='珍珠','珍珠',if(g.product_type1='翡翠','翡翠','贵金属工艺品')))))))) as protypefirstname,
	      if(g.product_type1 in ('普通黄金','定价黄金','投资黄金','足金镶嵌'),if(g.cat_type1='金条','1030',if(g.cat_type1='摆件','1020','1010')),if(g.product_type1='钻石',if(g.cat_type1='裸石','2010','2020'),if(g.product_type1 in ('彩钻','宝石'),if(g.cat_type1='裸石','3010','3020'),if(g.product_type1='PT','4010',if(g.product_type1='K金','5010',if(g.product_type1='银饰','6010',if(g.product_type1='珍珠','7010',if(g.product_type1='翡翠','8010','9010')))))))) as protypesecondecode,
	      if(g.product_type1 in ('普通黄金','定价黄金','投资黄金','足金镶嵌'),if(g.cat_type1='金条','金条金币',if(g.cat_type1='摆件','黄金摆件','黄金饰品')),if(g.product_type1='钻石',if(g.cat_type1='裸石','裸石','成品'),if(g.product_type1 in ('彩钻','宝石'),if(g.cat_type1='裸石','裸石','成品'),if(g.product_type1='PT','铂金饰品',if(g.product_type1='K金','K金饰品',if(g.product_type1='银饰','银饰饰品',if(g.product_type1='珍珠','珍珠饰品',if(g.product_type1='翡翠','翡翠饰品','贵金属工艺品')))))))) as protypesecondename,
		  'BDD' as brand,'' as seriescode,'' as seriesname,'' as seriescode_b,'' as seriesname_b,
		  if(g.cat_type in ('裸石','彩钻'),concat(g.zuanshidaxiao,'克拉',g.zhushixingzhuang,'-',g.zhushiyanse,'-',g.zhushijingdu,'-',g.qiegong,'-',g.duichen,'-',g.paoguang,'-',g.yingguang),concat(g.goods_sn,'-',g.caizhi,'-',round(g.jietuoxiangkou*100,0),'-',g.shoucun)) as standard,
		  'pcs' as unit ,g.zhengshuleibie as certificatetype,g.goods_id as anti_fakecode,g.zhengshuhao as certificatecode,
		   if(g.tuo_type=1,'成品','空托') as goldtype,g.zongzhong as oneweight,g.jinzhong as goldeweight,'' as lastweight,	
           g.zhushi as mainstonename,g.zhushixingzhuang as mainstoneshape,g.zuanshidaxiao as mainstonesize,
		  '' as mainstoneform,g.zhushiguige as mainstonestandard,'' as mainstonehome,g.zuanshidaxiao as mainstoneweight,g.zhushilishu as mainstonenum,g.zhushiyanse as mainstonecolor,
		   g.caizhi as mainstonefineness,g.jinzhong as mainstonefinenessweight,g.zhushijingdu as puredegree,g.zhushiqiegong as cutdegree,
		   g.paoguang as buffing,g.duichen as symmetry,g.yingguang as fluorescence,g.fushilishu as complementstonenum,g.fushizhong as complementstoneweight,            
		   g.shoucun as handsize,g.shoucun as fingerring,'' as note,	
           g.prc_id as suppliercode,g.prc_name as suppliername,'' as firstsuppliercode,'' as firstsuppliername, '' as supplierprocode,'' as supplierstylecode,  
           o.create_user as saler1,'' as saler1_royalty,'' as saler2,'' as saler2_royalty,
           bg.num as num,'' as price,'' as discount,'' as discountcoupon,'' as cutchange, bg.shijia as actualmoney,'' as notaxmoney,'' as taxmoney,'' as taxrate,'' as addmoney1,'' as addmoney2,'' as cutmoney1,
           '' as normalprocess,'' as specialprocess,
           g.mingyichengben as allcost,'' as allstonecost,'' as stonecost,'' as discountstonecost,'' as goldcost,'' as goldlose,'' as firstcost,'' as complementstonecost,'' as complementstonenote,'' as actualglodcost,'' as processcost,'' as othercost 
          from warehouse_shipping.warehouse_bill_goods bg,warehouse_shipping.warehouse_goods g,
	        warehouse_shipping.warehouse_bill b force index(bill_type_2)
	        left join app_order.base_order_info o on b.order_sn=o.order_sn
	        left join cuteframe.sales_channels s on o.department_id=s.id 
	        left join app_order.app_order_address r on o.id=r.order_id  
	        left join cuteframe.company c on if(b.bill_type in ('D','H'),b.to_company_id,b.from_company_id)=c.id 
	        where b.id=bg.bill_id and bg.goods_id=g.goods_id and b.bill_status=2 
            and b.check_time>'{$dd} 00:00:00'  
            and b.bill_type in ('D','H','S','P') and if(b.bill_type in ('S','P'),b.from_company_id,b.to_company_id) in (223,297,300,365,489,445,58)

            limit $start, $limit";
	$ret = $db->getAll($sql);
	if ($ret == null){
		break;
	}
     

	$number_arr=array('integralnum','oneweight','goldeweight','lastweight','mainstoneweight','mainstonenum','mainstonefinenessweight','complementstonenum','complementstoneweight','saler1_royalty','saler2_royalty','num','price','discount','discountcoupon','cutchange','actualmoney','notaxmoney','taxmoney','taxrate','addmoney1','addmoney2','cutmoney1','allcost','allstonecost','stonecost','discountstonecost','goldcost','goldlose','firstcost','complementstonecost','actualglodcost','processcost','othercost');

	foreach($ret as $r){
			foreach (array_keys($r) as $k => $key) {
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
	        file_put_contents(__DIR__.'/gangtaiimport.log',date('Y-m-d H:i:s').'--销售数据:Failed to get DB handle'.$e->getMessage().PHP_EOL,FILE_APPEND);
            echo "Failed to get DB handle: " . $e->getMessage() . "\n";
            exit();
}



$vals="";
$values="";
$k=0;
$times=0;


$rows=0;
//$dbms->beginTransaction();
foreach ($kela_arr as $key => $val) {
    	try{			
	        $stmt=$dbms->prepare("insert into gtkg_sale_detail_klzs values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
	        $res=$stmt->execute(array_values($val));	        
	        $rows++;
	        echo $rows. "\n"; 
        }catch (PDOException $e) {
        	//$dbms->rollBack(); 
            file_put_contents(__DIR__.'/gangtaiimport.log',date('Y-m-d H:i:s').'--销售数据:'.json_encode($val).PHP_EOL,FILE_APPEND);
            file_put_contents(__DIR__.'/gangtaiimport.log',date('Y-m-d H:i:s').'--销售数据:Failed to execute:'.$e->getMessage().PHP_EOL,FILE_APPEND);        	
            echo "<pre>";
            print_r($val);
            echo "Failed to execute: " . $e->getMessage() . "\n";
            exit();
        }	
}
//$dbms->commit();
$dbms=null;
exit();
*/


