<?php

include_once dirname(__FILE__).'/template.php';

// 清洗订单支付状态不对,发货状态不对的问题
$sql = "
update app_order.base_order_info i inner JOIN
(
	SELECT o.id, (case when (o.is_xianhuo = 1 or (o.is_xianhuo = 0 and d.min_buchan in (9, 11))) and o.delivery_status = 1 then 2 else o.delivery_status end) as real_delivery_status from app_order.base_order_info o 
	left JOIN 
	(
		select order_id, min(buchan_status) as min_buchan from app_order.app_order_details 
		where is_stock_goods = 0 and is_return = 0 and buchan_status > 0
		group by order_id
	) d on d.order_id = o.id
	where o.id > 1935211 and o.delivery_status  = 1 and o.delivery_status != (case when (o.is_xianhuo = 1 or (o.is_xianhuo = 0 and d.min_buchan in (9, 11))) and o.delivery_status = 1 and o.order_pay_status = 3 then 2 else o.delivery_status end)
) g on g.id = i.id
set i.delivery_status = g.real_delivery_status;"; 

$fixer = new Template();
$fixer->exec($sql);
		
?>

