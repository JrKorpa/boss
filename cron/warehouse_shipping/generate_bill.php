<?php

header("Content-type:text/html;charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require_once('MysqlDB.class.php');
set_time_limit(0);
ini_set('memory_limit','2000M');

$new_conf = [
		'dsn'=>"mysql:host=172.18.150.131;dbname=warehouse_shipping",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
];

$db = new MysqlDB($new_conf);

/*
CASE g.is_on_sale 
WHEN 1 THEN '收货中' 
WHEN 2 THEN '库存' 
WHEN 3 THEN '已销售'
WHEN 4 THEN '盘点中'
WHEN 5 THEN '转仓中'
WHEN 6 THEN '损益中'
WHEN 7 THEN '已报损'
WHEN 8 THEN '返厂中'
WHEN 9 THEN '已返厂'
WHEN 10 THEN '销售中'
WHEN 11 THEN '退货中'
WHEN 12 THEN '作废'
WHEN 13 THEN '加价调拨中'
end*/

$sql = "select g.company_id,c.company_name,prc_id,s.name,(select wholesale_id from warehouse_shipping.jxc_wholesale j where j.sign_company=g.company_id) as to_customer_id,count(g.goods_id) as goods_num,sum(g.yuanshichengbenjia) as yuanshichengben,sum(g.biaoqianjia) as label_price,sum(g.jingxiaoshangchengbenjia-g.management_fee) as pifajia,sum(g.mingyichengben) as mingyichengbenjia 
from warehouse_shipping.warehouse_goods g left join kela_supplier.app_processor_info s on g.prc_id=s.id left join cuteframe.company c on g.company_id=c.id
where is_on_sale in (1,2,4,5,6,8,10,11)
and is_on_sale=2
and g.prc_id>0
and company_id in ( select id from cuteframe.company where company_type=2 and id<>488 or id=58)
group by company_id,prc_id
order by company_id,prc_id";

$list1 = $db->getAll($sql);


//外协公司入库
$sql = "select g.company_id,g.warehouse_id,g.warehouse,c.company_name,prc_id,s.name,(select wholesale_id from warehouse_shipping.jxc_wholesale j where j.sign_company=g.company_id) as to_customer_id,count(g.goods_id) as goods_num,sum(g.yuanshichengbenjia) as yuanshichengben,sum(g.biaoqianjia) as label_price,sum(g.jingxiaoshangchengbenjia-g.management_fee) as pifajia,sum(g.mingyichengben) as mingyichengbenjia 
from warehouse_shipping.warehouse_goods g left join kela_supplier.app_processor_info s on g.prc_id=s.id left join cuteframe.company c on g.company_id=c.id
where is_on_sale in (1,2,4,5,6,8,10,11)
and is_on_sale=2
and g.prc_id>0
and company_id in (652,653,696,698,721)
group by company_id,prc_id
order by company_id,prc_id";

$list2 = $db->getAll($sql);



$create_time = date("Y-m-d H:i:s");
$check_time = date("Y-m-d H:i:s",time()+2000);
$create_time_p = date("Y-m-d H:i:s",time()+2200);
$check_time_p = date("Y-m-d H:i:s",time()+2300);

$db->db()->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
$db->db()->beginTransaction();//开启事务
try{
		foreach ($list1 as $key => $info) {
				//1、添加Bill主表
				$sql = "INSERT INTO warehouse_shipping.`warehouse_bill` (
					`id`, `bill_no`, `bill_type`, `bill_status`,
					`goods_num`, `to_warehouse_id`, `to_warehouse_name`,
					`to_company_id`,`to_company_name`, 
					`from_company_id`, `from_company_name`,`company_id_from`,
					`order_sn`,`bill_note`,
					`check_user`, `check_time`, `create_user`, `create_time`,
					`put_in_type`,`send_goods_sn`,
					 `pro_id`, `pro_name`,`jiejia`,
					 `goods_total`, `yuanshichengben`, `shijia`,
					 `production_manager_name`,`label_price_total`
					) VALUES (
					NULL, '0', 'L', 2,
					{$info['goods_num']}, '1873', '浩鹏后库', 
					'58','总公司',
					0, '','1', 
					'','', 
					'李丽珍','{$check_time}','王永红', '{$create_time}',
					'1', '',
					'{$info['prc_id']}', '{$info['name']}', '1',
					'{$info['yuanshichengben']}', '{$info['yuanshichengben']}', '0',
					'','{$info['label_price']}')";
                
				$db->query($sql);
                //throw new Exception();
				$id = $db->insertId();

				$bill_no= create_bill_no('L',$id);
				$sql = "UPDATE `warehouse_bill` SET `bill_no`='{$bill_no}' WHERE `id`={$id}";
				$db->query($sql);
				$sql = "insert into warehouse_shipping.warehouse_bill_goods (id,bill_id,bill_no,bill_type,goods_id,goods_sn,goods_name,num,warehouse_id,caizhi,jinzhong,jingdu,yanse,zhengshuhao,zuanshidaxiao,in_warehouse_type,account,addtime,pandian_guiwei,sale_price,yuanshichengben,label_price)
				 select 0,'{$id}','{$bill_no}','L',g.goods_id,g.goods_sn,g.goods_name,1,1873,g.caizhi,g.jinzhong,g.jingdu,g.yanse,g.zhengshuhao,g.zuanshidaxiao,1,1,'2019-01-01 00:00:00','0-00-0-0',g.yuanshichengbenjia,g.yuanshichengbenjia,g.biaoqianjia from warehouse_goods g where is_on_sale=2 and company_id='{$info['company_id']}' and prc_id='{$info['prc_id']}'";
		        $db->query($sql); 
		        $sql = "insert into warehouse_shipping.warehouse_bill_pay values (0,'{$id}','{$info['prc_id']}','{$info['name']}','3','2','2','{$info['yuanshichengben']}')";
		        $db->query($sql); 
		        
		        if($info['company_id']<>58){
		        	$sql = "INSERT INTO `warehouse_bill` (`bill_no`, `bill_status`, `bill_type`, `goods_num` , `bill_note` , `goods_total`,`shijia` , `pifajia` , `create_user` , `create_time`,`check_user`,`check_time` ,`from_company_id` , `from_company_name`, `to_customer_id`, `to_company_id`, `to_company_name`, `to_warehouse_id`, `to_warehouse_name`,`company_id_from`, `out_warehouse_type`,`label_price_total`,`p_type`,is_invoice,sign_user,sign_time) VALUES ('',4 , 'P' ,  {$info['goods_num']} , '' , {$info['yuanshichengben']} , {$info['pifajia']} , {$info['mingyichengbenjia']} , '王永红' , '{$create_time_p}','李丽珍','{$check_time_p}' ,'58', '总公司', '{$info['to_customer_id']}', {$info['company_id']}, '{$info['company_name']}', '99999999', '','1', '1', '{$info['label_price']}', '经销商备货','0','门店','{$check_time_p}')";
	                //echo $sql;
	                $db->query($sql); 
	                $bill_id = $db->insertId();
	                $bill_no= create_bill_no('P',$bill_id);
				    $sql = "UPDATE `warehouse_bill` SET `bill_no`='{$bill_no}' WHERE `id`={$bill_id}";
				    $db->query($sql);
				    $sql = "insert into warehouse_shipping.warehouse_bill_goods (id,bill_id,bill_no,bill_type,goods_id,goods_sn,goods_name,num,caizhi,jinzhong,jingdu,yanse,zhengshuhao,zuanshidaxiao,in_warehouse_type,account,addtime,pandian_guiwei,pifajia,sale_price,shijia,dep_settlement_type,settlement_time,management_fee,label_price)
				    select 0,'{$bill_id}','{$bill_no}','P',g.goods_id,g.goods_sn,g.goods_name,1,g.caizhi,g.jinzhong,g.jingdu,g.yanse,g.zhengshuhao,g.zuanshidaxiao,g.put_in_type,0,'{$create_time_p}','0-00-0-0',g.mingyichengben,g.yuanshichengbenjia,g.jingxiaoshangchengbenjia-g.management_fee,'2','{$check_time_p}',g.management_fee,g.biaoqianjia  from warehouse_goods g where is_on_sale=2 and company_id='{$info['company_id']}' and prc_id='{$info['prc_id']}'";
                    $db->query($sql);
		        }
                $sql ="update warehouse_goods g set g.hidden='0',addtime='{$check_time}',change_time='{$check_time}' where is_on_sale=2 and company_id='{$info['company_id']}' and prc_id='{$info['prc_id']}'";              
                $db->query($sql);		        
		        echo $bill_no.PHP_EOL;
		}






		foreach ($list2 as $key => $info) {
				//1、添加Bill主表
				$sql = "INSERT INTO warehouse_shipping.`warehouse_bill` (
					`id`, `bill_no`, `bill_type`, `bill_status`,
					`goods_num`, `to_warehouse_id`, `to_warehouse_name`,
					`to_company_id`,`to_company_name`, 
					`from_company_id`, `from_company_name`,`company_id_from`,
					`order_sn`,`bill_note`,
					`check_user`, `check_time`, `create_user`, `create_time`,
					`put_in_type`,`send_goods_sn`,
					 `pro_id`, `pro_name`,`jiejia`,
					 `goods_total`, `yuanshichengben`, `shijia`,
					 `production_manager_name`,`label_price_total`
					) VALUES (
					NULL, '0', 'L', 2,
					{$info['goods_num']}, '{$info['warehouse_id']}', '{$info['warehouse']}', 
					'{$info['company_id']}','{$info['company_name']}',
					0, '','1', 
					'','', 
					'李丽珍','{$check_time}','王永红', '{$create_time}',
					'1', '',
					'{$info['prc_id']}', '{$info['name']}', '1',
					'{$info['yuanshichengben']}', '{$info['yuanshichengben']}', '0',
					'','{$info['label_price']}')";
                
				$db->query($sql);
                //throw new Exception();
				$id = $db->insertId();

				$bill_no= create_bill_no('L',$id);
				$sql = "UPDATE `warehouse_bill` SET `bill_no`='{$bill_no}' WHERE `id`={$id}";
				$db->query($sql);
				$sql = "insert into warehouse_shipping.warehouse_bill_goods (id,bill_id,bill_no,bill_type,goods_id,goods_sn,goods_name,num,warehouse_id,caizhi,jinzhong,jingdu,yanse,zhengshuhao,zuanshidaxiao,in_warehouse_type,account,addtime,pandian_guiwei,sale_price,yuanshichengben,label_price)
				 select 0,'{$id}','{$bill_no}','L',g.goods_id,g.goods_sn,g.goods_name,1,'{$info['warehouse_id']}',g.caizhi,g.jinzhong,g.jingdu,g.yanse,g.zhengshuhao,g.zuanshidaxiao,1,1,'2019-01-01 00:00:00','0-00-0-0',g.yuanshichengbenjia,g.yuanshichengbenjia,g.biaoqianjia from warehouse_goods g where is_on_sale=2 and company_id='{$info['company_id']}' and prc_id='{$info['prc_id']}'";
		        $db->query($sql); 
		        $sql = "insert into warehouse_shipping.warehouse_bill_pay values (0,'{$id}','{$info['prc_id']}','{$info['name']}','3','2','2','{$info['yuanshichengben']}')";
		        $db->query($sql); 
		       
                $sql ="update warehouse_goods g set g.hidden='0',addtime='{$check_time}',change_time='{$check_time}' where is_on_sale=2 and company_id='{$info['company_id']}' and prc_id='{$info['prc_id']}'";              
                $db->query($sql);		        
		        echo $bill_no.PHP_EOL;
		}








		$db->db()->commit();
		$db->db()->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
}catch(Exception $e){
	    echo $sql.json_encode($e);
		$db->db()->rollback();//事务回滚
		$db->db()->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return $e->getMessage();	
}		




    function create_bill_no($type, $bill_id = '1')
    {
        $bill_id = substr($bill_id, -4);
        $bill_no = $type . date('Ymd', time()) . rand(100, 999) . str_pad($bill_id, 4,
            "0", STR_PAD_LEFT);
       
        return $bill_no;
    }

?>