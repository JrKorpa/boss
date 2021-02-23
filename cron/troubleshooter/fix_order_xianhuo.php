<?php

include_once dirname(__FILE__).'/template.php';

// 清洗订单是否现货单期货单问题
$sql = "
update app_order.base_order_info a
set a.is_xianhuo = (case a.is_xianhuo when 0 then 1 when 1 then 0 end)
where a.id > 1935211 and a.is_delete = 0 and a.apply_close = 0 and a.apply_return = 1 and ((a.is_xianhuo = 1 and exists(SELECT 1 from app_order.app_order_details d where d.is_stock_goods = 0 and d.order_id = a.id and d.is_return = 0)) or
(a.is_xianhuo = 0 and not exists(SELECT 1 from app_order.app_order_details d where d.is_stock_goods = 0 and d.order_id = a.id and d.is_return = 0)))";

$fixer = new Template();
$fixer->exec($sql);
		
?>





