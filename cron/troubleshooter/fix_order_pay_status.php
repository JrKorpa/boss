<?php

include_once dirname(__FILE__).'/template.php';

// 清洗订单支付状态不对,发货状态不对的问题
$sql = "
update app_order.base_order_info o inner JOIN (
	SELECT i.id, i.order_pay_status, (case when a.money_unpaid=0 then 3 when a.money_paid = 0 and a.money_unpaid > 0 then 1 else 2 end) as real_pay_status from app_order.base_order_info i 
	inner join app_order.app_order_account a on a.order_id = i.id
	where i.id > 1935211 and i.apply_return = 1 and i.apply_close = 0 and i.order_status = 2 and i.is_delete = 0 and i.order_pay_status != 4 and i.referer not in ('双11抓单','双11预售')
) g on g.id = o.id and g.order_pay_status != g.real_pay_status
set o.order_pay_status = g.real_pay_status;

update app_order.base_order_info o inner JOIN (
	SELECT i.id from app_order.base_order_info i 
	where i.id > 1935211 and i.apply_return = 1 and i.apply_close = 0 and i.order_status = 2 and i.is_delete = 0 and i.order_pay_status=1 and EXISTS(select 1 from app_order.app_order_action where order_id = i.id and locate('点款成功:', remark) =1) and i.referer not in ('双11抓单','双11预售')
) g on g.id = o.id
set o.order_pay_status = 2;

update app_order.base_order_info i 
left join (
select order_id, max(action_id) as id from app_order.app_order_action where order_id>1935211 and locate('恢复支付操作', remark) = 1 group by order_id
) hg on hg.order_id = i.id
left join (
 select order_id, max(action_id) as id from app_order.app_order_action where order_id>1935211 and locate('点款成功:', remark) = 1 group by order_id 
) zc on zc.order_id = i.id
left join (
	SELECT order_id, min(pay_time) as pay_date from finance.app_order_pay_action where STATUS != 4 and order_id>1935211  group by order_id
) p on p.order_id = i.id
inner join app_order.app_order_account c on c.order_id = i.id
set i.order_pay_status = 1
where i.id > 1935211 and i.apply_return = 1 and i.apply_close = 0 and i.order_status = 2 and i.is_delete = 0 and i.is_zp = 0 and i.order_pay_status in (2, 3) and i.referer not in ('双11抓单','双11预售') 
and (case 
		when p.order_id is not null then 1
		when c.money_unpaid = 0 then 1
		when hg.id is null and zc.id is not null then 1 
	    when hg.id is null and zc.id is null then 0
        when hg.id is not null and hg.id > zc.id then 0
		when hg.id is not null and hg.id < zc.id then 1
	end) = 0"; 

$fixer = new Template();
$fixer->exec($sql);
		
?>

