<?php

include_once dirname(__FILE__).'/template.php';

// 清洗订单金额不对问题
$sql = "update app_order.app_order_account ac inner join 
(
	SELECT 
		i.id, i.order_sn, 
		d.goods_price, 
		d.favorable_price ,
		IFNULL(f.deposit, 0) as deposit ,
		(IFNULL(d.goods_price,0)-IFNULL(d.favorable_price,0)-IFNULL(d.refund_goods_price,0)+IFNULL(d.refund_favor_price,0)-IFNULL(a.coupon_price,0)+IFNULL(a.shipping_fee,0)-IFNULL(g.spec_return_amount,0)) as order_amount,
		IFNULL(g.real_return_amount, 0) as real_return_amount,
	  (IFNULL(d.goods_price,0)-IFNULL(d.favorable_price,0)-IFNULL(d.refund_goods_price,0)+IFNULL(d.refund_favor_price,0)-IFNULL(a.coupon_price,0)+IFNULL(a.shipping_fee,0)-IFNULL(g.spec_return_amount,0)-IFNULL(f.deposit,0)+IFNULL(g.real_return_amount,0)) as real_money_unpaid
	from app_order.base_order_info i 
	left JOIN
	(
		SELECT order_id, SUM(case when check_status >=4 then real_return_amount else 0 end) as real_return_amount, sum(case when order_goods_id = 0 and check_status >= 4 then real_return_amount else 0 end) as spec_return_amount from app_order.app_return_goods where order_id>1935211 and check_status > 0 group by order_id 
	) g on g.order_id = i.id
	left JOIN
	(
		select order_id, sum(deposit) as deposit from finance.app_order_pay_action
		where order_id>1935211 and is_type = 1 and `status` != 4
		GROUP BY order_id
	) f on f.order_id = i.id
	left JOIN (
		SELECT order_id, sum(goods_price) as goods_price, sum(case when is_return = 1 then goods_price else 0 end) as refund_goods_price, sum(case when favorable_status = 3 then favorable_price else 0 end) as favorable_price, sum(case when is_return = 1 and favorable_status = 3 then favorable_price else 0 end) as refund_favor_price from app_order.app_order_details where order_id>1935211 group by order_id
	) d on d.order_id = i.id
	inner JOIN app_order.app_order_account a on a.order_id = i.id
	where i.id > 1935211 and i.is_delete = 0 and i.apply_close = 0 and i.apply_return = 1 and i.referer not in ('双11抓单','双11预售') and (
		a.goods_amount != IFNULL(d.goods_price,0) or a.favorable_price != IFNULL(d.favorable_price,0) or a.money_paid != IFNULL(f.deposit,0) 
		or a.order_amount != (IFNULL(d.goods_price,0)-IFNULL(d.favorable_price,0)-IFNULL(d.refund_goods_price,0)+IFNULL(d.refund_favor_price,0)-IFNULL(a.coupon_price,0)+IFNULL(a.shipping_fee,0)-IFNULL(g.spec_return_amount,0))
		or a.goods_return_price != IFNULL(g.real_return_amount,0)
		or a.money_unpaid != (IFNULL(d.goods_price,0)-IFNULL(d.favorable_price,0)-IFNULL(d.refund_goods_price,0)+IFNULL(d.refund_favor_price,0)-IFNULL(a.coupon_price,0)+IFNULL(a.shipping_fee,0)-IFNULL(g.spec_return_amount,0)-IFNULL(f.deposit,0)+IFNULL(g.real_return_amount,0))
	) and (IFNULL(d.goods_price,0)-IFNULL(d.favorable_price,0)-IFNULL(d.refund_goods_price,0)+IFNULL(d.refund_favor_price,0)-IFNULL(a.coupon_price,0)+IFNULL(a.shipping_fee,0)-IFNULL(g.spec_return_amount,0)) >= 0 
) g on g.id = ac.order_id
set ac.order_amount = ifnull(g.order_amount,0), ac.goods_amount = ifnull(g.goods_price,0), ac.favorable_price = ifnull(g.favorable_price,0), ac.money_paid = ifnull(g.deposit,0), ac.goods_return_price = ifnull(g.real_return_amount,0), ac.money_unpaid = (case when g.real_money_unpaid <0 then 0 else g.real_money_unpaid end)"; 

$fixer = new Template();
$fixer->exec($sql);
		
?>





