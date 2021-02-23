<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductInfomodel.class.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2017-2054 kela Inc
 *   @author	: luochuanrong
 *   @date		:
 *   @update	:
 *	 @description:获取销售/库存明细
 *  -------------------------------------------------
 */
 include_once 'model/CommonModel.class.php';
 class GangtaiModel extends CommonModel
 {
	public function __construct($id = null,$strConn='')
	{   		
		parent::__construct($id,$strConn);
    }

    public function Get_sale_detail($post){
        $start_date=$post['start_date'];
        $end_date=$post['end_date'];
    	$pagesize=$post['pagesize'];
    	$page=$post['page'];
    	$res=array('data'=>array(),'total_page'=>0);    	
    	try{
	        $feilds="select '0016' as  ORGCODE,'' as ORGNAME,c.company_sn as filialecode,c.company_name as filialename,c.company_sn as shopcode,c.company_name as shopname,'' as countercode,'' as countername,
			b.create_user as warehousekeeper,g.warehouse_id as outwarehousecode,g.warehouse as outwarehouse_name,
		    if(b.bill_type='S',o.consignee,(select j.wholesale_name from warehouse_shipping.jxc_wholesale j where j.wholesale_id=b.to_customer_id)) as custcode,
		    if(b.bill_type='S',o.consignee,(select j.wholesale_name from warehouse_shipping.jxc_wholesale j where j.wholesale_id=b.to_customer_id)) as custname,
		    if(b.bill_type='P',0,(select d.is_return from app_order.app_order_details d where d.order_id=o.id and d.goods_id=g.goods_id limit 1)) as flag,
            if(b.bill_type='P',b.bill_no,o.order_sn) as salesordercode,
          if(b.bill_type='P',0,(select d.is_return from app_order.app_order_details d where d.order_id=o.id and d.goods_id=g.goods_id limit 1)) as is_return,
          if(b.bill_type='P',b.bill_no,o.order_sn) as salesordercode,b.bill_type as saletype,b.check_time as checkcode,b.check_time as saledate,b.create_user as billmaker
          	b.create_time as billdate,b.check_user as checker,
          '' as paymenttype,'' as taximetertype,'' as vipcode,'' as integralnum,
          if(b.bill_type='P','10',if(b.from_company_id in (58,445),'20','30')) as channeltypecode,
          if(b.bill_type='P','批发',if(b.from_company_id in (58,445),'线上','线下')) as channeltypename,
          if(b.bill_type='P','1010',if(b.from_company_id not in (58,445),'3010',if(o.department_id=2,'2010',if(o.department_id=71,'2020',if(o.customer_source_id=2034,'2030',if(o.create_user='system_api','2040','2060')))))) as channelcode, 
          if(b.bill_type='P','商品批发',if(b.from_company_id not in (58,445),'直营门店',if(o.department_id=2,'天猫',if(o.department_id=71,'京东',if(o.customer_source_id=2034,'唯品会',if(o.create_user='system_api','直营电商','其他')))))) as salechannel, 
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
		   g.caizhi as goldcolor,'' as golddegree,if(g.tuo_type=1,'成品','空托') as goldtype,g.zongzhong as oneweight,g.jinzhong as goldeweight,'' as lastweight,	
           g.zhushi as mainstonename,g.zhushixingzhuang as mainstoneshape,g.zuanshidaxiao as mainstonesize,
		  '' as mainstoneform,g.zhushiguige as mainstonestandard,'' as mainstonehome,g.zuanshidaxiao as mainstoneweight,g.zhushilishu as mainstonenum,g.zhushiyanse as mainstonecolor,
		   g.caizhi as mainstonefineness,g.jinzhong as mainstonefinenessweight,g.zhushijingdu as puredegree,g.zhushiqiegong as cutdegree,
		   g.paoguang as buffing,g.duichen as symmetry,g.yingguang as fluorescence,g.fushilishu as complementstonenum,g.fushizhong as complementstoneweight,            
		   g.shoucun as handsize,g.shoucun as fingerring,'' as note,	
           g.prc_id as suppliercode,g.prc_name as suppliername,'' as firstsuppliercode,'' as firstsuppliername, as supplierprocode,'' as supplierstylecode,  
           o.create_user as saler1,'' as saler1_royalty,'' as saler2,'' as saler2_royalty,
           bg.num as num,'' as price,'' as discount,'' as discountcoupon,'' as cutchange, bg.shijia as actualmoney,'' as notaxmoney,'' as taxmoney,'' as taxrate,'' as addmoney1,'' as addmoney2,'' as cutmoney1,
           '' as normalprocess,'' as specialprocess,
           g.mingyichengben as allcost,'' as allstonecost,'' as stonecost,'' as discountstonecost,'' as goldcost,'' as goldlose,'' as firstcost,'' as complementstonecost,'' as complementstonenote,'' as actualglodcost,'' as processcost,'' as othercost ";

	        $where="from warehouse_shipping.warehouse_bill_goods bg,warehouse_shipping.warehouse_goods g,
	        warehouse_shipping.warehouse_bill b force index(bill_type_2)
	        left join app_order.base_order_info o on b.order_sn=o.order_sn
	        left join cuteframe.sales_channels s on o.department_id=s.id 
	        left join app_order.app_order_address r on o.id=r.order_id 
	        left join cuteframe.company c on b.from_company_id=c.id
	        where b.id=bg.bill_id and bg.goods_id=g.goods_id and b.bill_status=2 
	        and b.check_time>'{$start_date} 00:00:00' and b.check_time<='{$end_date} 23:59:59' and 
            ((b.from_company_id=58 and  b.bill_type='P')  or   (b.from_company_id in (223,297,300,365,489) and  b.bill_type='S')) ";
	    	
	    	$sql="select count(1) as total_row ".$where;
            $toal_row=$this->db()->getOne($sql);  
	    	$res['total_page']=ceil($toal_row/$pagesize);
	    	$sql=$feilds.$where." limit ".($page-1)*$pagesize.",{$pagesize}";	    	
	    	$res['data']=$this->db()->getAll($sql);
	    	if(!empty($res['data']))
	    	    $res['success']=1;
	    	else
	    		$res['error']=$sql;
	    	//echo $sql;
	    	//file_put_contents('sql.log',$sql);
	    	//print_r($res);
        }catch(Exception $e){
        	//echo json_encode($e);
        	$res['error']=$sql;

        }
        return $res;
     }

    public function Get_inventory_detail($post){
    	$pagesize=$post['pagesize'];
    	$page=$post['page'];
    	$res=array('data'=>array(),'total_page'=>0);    	
    	try{
	        $feilds="select '0016' as  ORGCODE,'GP商贸有限公司' as ORGNAME,c.company_sn,c.company_name,c.company_sn as shop_code,c.company_name as shop_name,'' as box_sn,'' as box_name,
			(case g.put_in_type when 1 then '购买' when 2 then '委托加工' when 3 then '代销' when 4 then '借入' end) as put_in_type,
			(CASE g.is_on_sale WHEN 1 THEN '收货中' WHEN 2 THEN '库存' WHEN 3 THEN '已销售' WHEN 4 THEN '盘点中' WHEN 5 THEN '转仓中' WHEN 6 THEN '损益中' WHEN 7 THEN '已报损' WHEN 8 THEN '返厂中' WHEN 9 THEN '已返厂' WHEN 10 THEN '销售中' WHEN 11 THEN '退货中' WHEN 12 THEN '作废' WHEN 13 THEN '加价调拨中' ELSE '' END) as is_on_sale,
			g.chuku_time,g.addtime,'' as cangkuleixing,'' as cangkuguangliyuan,g.warehouse_id,g.warehouse,'' as jiesuanbumenbianma,'' as jiesuanbumenmingcheng,
			g.goods_id,g.goods_name,g.cat_type1 as goods_type,g.cat_type1 as goods_type_name,g.goods_sn,
			g.goods_sn as goods_sn_name,g.cat_type1 as cat_type,g.cat_type1 as cat_type_name,
	      if(g.product_type1 in ('普通黄金','定价黄金','投资黄金','足金镶嵌'),'10',if(g.product_type1='钻石','20',if(g.product_type1 in ('彩钻','宝石'),'30',if(g.product_type1='PT','40',if(g.product_type1='K金','50',if(g.product_type1='银饰','60',if(g.product_type1='珍珠','70',if(g.product_type1='翡翠','80','90')))))))) as cat_code,
	      if(g.product_type1 in ('普通黄金','定价黄金','投资黄金','足金镶嵌'),'黄金',if(g.product_type1='钻石','钻石',if(g.product_type1 in ('彩钻','宝石'),'彩宝',if(g.product_type1='PT','铂金',if(g.product_type1='K金','K金',if(g.product_type1='银饰','银饰',if(g.product_type1='珍珠','珍珠',if(g.product_type1='翡翠','翡翠','贵金属工艺品')))))))) as cat_code,
	      if(g.product_type1 in ('普通黄金','定价黄金','投资黄金','足金镶嵌'),if(g.cat_type1='金条','1030',if(g.cat_type1='摆件','1020','1010')),if(g.product_type1='钻石',if(g.cat_type1='裸石','2010','2020'),if(g.product_type1 in ('彩钻','宝石'),if(g.cat_type1='裸石','3010','3020'),if(g.product_type1='PT','4010',if(g.product_type1='K金','5010',if(g.product_type1='银饰','6010',if(g.product_type1='珍珠','7010',if(g.product_type1='翡翠','8010','9010')))))))) as cat_code2,
	      if(g.product_type1 in ('普通黄金','定价黄金','投资黄金','足金镶嵌'),if(g.cat_type1='金条','金条金币',if(g.cat_type1='摆件','黄金摆件','黄金饰品')),if(g.product_type1='钻石',if(g.cat_type1='裸石','裸石','成品'),if(g.product_type1 in ('彩钻','宝石'),if(g.cat_type1='裸石','裸石','成品'),if(g.product_type1='PT','铂金饰品',if(g.product_type1='K金','K金饰品',if(g.product_type1='银饰','银饰饰品',if(g.product_type1='珍珠','珍珠饰品',if(g.product_type1='翡翠','翡翠饰品','贵金属工艺品')))))))) as cat_code_name2,
			'BDD' as brand,
			'' as serial_sn,'' as serial_name,'' as sub_serial_sn,'' as sub_serial_name,g.mo_sn,
			if(g.cat_type in ('裸石','彩钻'),concat(g.zuanshidaxiao,'克拉',g.zhushixingzhuang,'-',g.zhushiyanse,'-',g.zhushijingdu,'-',g.qiegong,'-',g.duichen,'-',g.paoguang,'-',g.yingguang),concat(g.goods_sn,'-',g.caizhi,'-',round(g.jietuoxiangkou*100,0),'-',g.shoucun)) as special,
			'pcs' as util,g.product_type1 as product_type,g.zhengshuleibie,'' as anti_forgery,g.zhengshuhao,
			g.caizhi,'' as hanjinliang,g.tuo_type,g.zongzhong,g.jinzhong,'' as yuzhong,
			'' as zhushijibie,'' as zhushimingcheng,g.zhushixingzhuang,g.zuanshidaxiao as zhushidaxiao,
			'' as zhushixingtai,g.zhushiguige,'' as zhushichandi,g.zuanshidaxiao as zhushizhong,g.zhushilishu as zhushishuliang,g.zhushiyanse,
			g.caizhi as zhuchengse,g.jinzhong as zhuchengsezhong,g.zhushijingdu,g.zhushiqiegong,
			g.paoguang,g.duichen,g.yingguang,g.fushilishu as fushishuliang,g.fushizhong,
			g.shoucun,g.shoucun as zhiquan,'' as jiagonggongyi,'' as teshugongyi,'' as remark,
			g.prc_id,g.prc_name,'' as prc_goods_id,'' as prc_goods_sn,
			g.mingyichengben,'' as gongfeidanjia,'' as gongfeie,'' as biaoqianjia,'' as shoujia,'' as xiaoshougongfei,
			'' as zhengshufei,'' as qitafeiyong,'' as yuanshichengben,'' as jiajiachengben,'' as zhushijia,'' as jiesuanjia,'' as shizhijia,'' as nahuojia,
			If( (select max(i.in_time) in_time 	FROM warehouse_shipping.goods_io i 	WHERE  i.in_time is not null and i.goods_id=g.goods_id and i.warehouse_id=g.warehouse_id ) is null,
            0, ceil( (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP((select max(in_time) in_time FROM warehouse_shipping.goods_io i2 WHERE i2.in_time is not null and i2.goods_id=g.goods_id and i2.warehouse_id=g.warehouse_id))) / (24 * 3600)) ) as benkukuling,
            IF(g.`addtime` = '0000-00-00 00:00:00',	0,	ceil((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(g.`addtime`)	) / (24 * 3600))) AS kucuntianshu,
			'' as kucunchengben,'' as kucunjine,'' as kucunbiaojiajine,g.num, 0 as dongjieshuliang,0 as zaijianshuliang,
			if(g.is_on_sale=5,1,0) as diaobozaitushuliang,'' as yujilukushuliang,if(g.is_on_sale=10,1,0) as daifahuoshuliang,
			'' as diaobodaifahuoshuliang,'' as yujichukushuliang,'' as buhegeshuliang,'' as keyongshuliang,now() as currentdate ";

	        $where="from warehouse_shipping.warehouse_goods g left join cuteframe.company c on g.company_id=c.id  where g.is_on_sale in (2,4,5,6,8,10,11) and g.company_id in (223,297,300,365,489)";
	    	$sql="select count(1) as total_row ".$where;
            $toal_row=$this->db()->getOne($sql);            
	    	$res['total_page']=ceil($toal_row/$pagesize);
	    	$sql=$feilds.$where." limit ".($page-1)*$pagesize.",{$pagesize}";
	    	$res['data']=$this->db()->getAll($sql);
	    	if(!empty($res['data']))
	    	    $res['success']=1;	    	
	    	//echo $sql;
	    	//print_r($res);
        }catch(Exception $e){
        	//echo json_encode($e);
        }
        return $res;
     }     

 }

?>