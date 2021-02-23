<?php

include_once dirname(__FILE__).'/template.php';

// 清洗订单第一次支付时间问题
$sql = "
update app_order.base_order_info i inner JOIN
(
	SELECT order_id, min(pay_time) as pay_date from finance.app_order_pay_action where STATUS != 4 group by order_id
) g on g.order_id = i.id
set i.pay_date = g.pay_date
WHERE i.id > 1935211 and i.referer not in ('双11抓单','双11预售') and (i.pay_date is null or i.pay_date = '0000-00-00 00:00:00' or DATE_FORMAT(g.pay_date,'%y%m') != DATE_FORMAT(i.pay_date,'%y%m'));"; 

$fixer = new Template();
$fixer->exec($sql);
		
?>

