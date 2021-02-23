<?php

function boss_on_refresh_order($data, $db) {

	if (!isset($data['order_sn']) && !isset($data['order_id']) && !isset($data['det_id'])) return false;
	
	$order_id = false;
	$order_sn = false;
	$det_id = false;
	
	if (isset($data['order_id']) && !empty($data['order_id'])) {
		$order_id = intval(trim($data['order_id']));
		if ($order_id <= 2617377) return;
	} else if (isset($data['order_sn']) && !empty($data['order_sn'])) {
		$data['order_sn'] = trim($data['order_sn'], "'");
		$prefix = intval(substr($data['order_sn'], 0, 4));
		if ($prefix < 2017) return;
		
		$order_sn = $data['order_sn'];
	} else if (isset($data['det_id']) && !empty($data['det_id'])) {
		$det_id = $data['det_id'];
	}
	
	refresh_order($db, $order_id, $order_sn, $det_id);
}

function zhanting_on_refresh_order($data, $db) {

	if (!isset($data['order_sn']) && !isset($data['order_id']) && !isset($data['det_id'])) return false;
	
	$order_id = false;
	$order_sn = false;
	$det_id = false;
	
	if (isset($data['order_id']) && !empty($data['order_id'])) {
		$order_id = intval(trim($data['order_id']));
		if ($order_id <= 35472) return;
	} else if (isset($data['order_sn']) && !empty($data['order_sn'])) {
		$data['order_sn'] = trim($data['order_sn'], "'");
		$prefix = intval(substr($data['order_sn'], 0, 5));
		if ($prefix < 92017) return;
		
		$order_sn = $data['order_sn'];
	} else if (isset($data['det_id']) && !empty($data['det_id'])) {
		$det_id = $data['det_id'];
	}
		
	refresh_order($db, $order_id, $order_sn, $det_id);
}

function refresh_order($db, $order_id, $order_sn, $det_id) {
	// 查询订单数据，如果订单无效，或申请关闭，或正在退款退货，或已删除，或已配货发货，则不再修改订单信息
	$order_info = false;
	if (!empty($order_id)) {
		$order_info = $db->getRow("select * from base_order_info where id ={$order_id}");
	} else if (!empty($order_sn)) {
		$order_info = $db->getRow("select * from base_order_info where order_sn ='{$order_sn}' ");
	} else {
		$order_info = $db->getRow("select * from base_order_info where id = (select order_id from app_order_details where id = {$det_id})");
	}
	
	if (empty($order_info) || $order_info['referer'] == '智慧门店' ) return false;

	
	if (empty($order_id)) $order_id = $order_info['id'];
	
	$delivery_status = $order_info['delivery_status'];
	
	if ($order_info['order_status'] != '2' || $order_info['apply_close'] != '0' || $order_info['apply_return'] != '1' || $order_info['is_delete'] != '0' || (($order_info['delivery_status'] == 5 || $delivery_status == 5) && $order_info['send_good_status'] > 1)) {
		echo 'Nothing to do for order:'.$order_id.PHP_EOL;
		return false;
	}
		
	echo 'start processing order:'.$order_id.PHP_EOL;
	// 现货期货
	$db->exec("update base_order_info a
	set a.is_xianhuo = (case a.is_xianhuo when 0 then 1 when 1 then 0 end)
	where a.id = '{$order_id}' and a.order_status = 2 and a.is_delete = 0 and a.apply_close = 0 and a.apply_return = 1 and ((a.is_xianhuo = 1 and exists(SELECT 1 from app_order_details d where d.is_stock_goods = 0 and d.order_id = a.id and d.is_return = 0)) or (a.is_xianhuo = 0 and not exists(SELECT 1 from app_order_details d where d.is_stock_goods = 0 and d.order_id = a.id and d.is_return = 0)));");
		
	// 布产状态
	$db->exec("update app_order_details d inner join kela_supplier.product_info p on d.bc_id = p.id set d.buchan_status = p.`status` where d.order_id = '{$order_id}' and d.is_stock_goods = 0 and p.`status` != d.buchan_status;");
	$db->exec("update base_order_info i left JOIN
	(
		select order_id, min(buchan_status) as buchan from app_order_details 
		where is_stock_goods = 0 and is_return = 0 and bc_id > 0
		and order_id = {$order_id}
	) d on d.order_id = i.id
	set i.buchan_status = (case when ifnull(d.buchan,0) in (0, 10) then 1 when d.buchan in (1,2,3) then 2 when d.buchan in (4,7) then 3 when d.buchan = 9 then 4 when d.buchan = 11 then 5 else i.buchan_status end)
	where i.is_xianhuo = 0 and i.id ={$order_id};");
		
	//赠品及销账标志
	$db->exec(
	"update app_order_details d inner join gift_goods s on d.goods_sn = s.goods_number and s.`status` = 1 
	set d.is_zp = (case when d.is_stock_goods = 1 and d.goods_price = d.favorable_price and d.favorable_status = 3 then 1 else 0 end), 
	d.is_finance = (case when d.is_stock_goods = 1 and d.goods_price = d.favorable_price and d.favorable_status = 3 then s.is_xz else 2 end)
	where d.order_id = {$order_id} and d.is_return = 0;

	update base_order_info i inner join 
	(
		select order_id, min(case when is_zp is null then 0 when LENGTH(is_zp) = 0 then 0 else is_zp end) as all_is_zp from app_order_details where is_return = 0 where order_id ={$order_id}
	) g on g.order_id = i.id
	set i.is_zp = g.all_is_zp
	where i.id ={$order_id} and i.is_zp != g.all_is_zp;");

	// 重复点款记录
	$db->exec(
	"DELETE p from finance.app_order_pay_action p INNER JOIN 
	(
		select order_sn, min(pay_id) as min_id, max(`STATUS`) as max_status, order_amount, deposit, remark, pay_type, balance, pay_time, order_consignee, department, opter_name, system_flg, is_type, out_order_sn from finance.app_order_pay_action
		where `order_id` = {$order_id} and `is_type` = 1 and `status` != 4
		group by order_sn, order_amount, deposit, remark, pay_type, balance, pay_time, order_consignee, department, opter_name, system_flg, is_type, out_order_sn
		HAVING(count(1) > 1)
	) g on g.order_sn = p.order_sn and g.order_amount=p.order_amount and g.deposit=p.deposit and g.remark=p.remark and g.pay_type=p.pay_type and g.balance=p.balance and g.pay_time=p.pay_time and g.order_consignee =p.order_consignee and g.department=p.department and g.opter_name=p.opter_name and g.system_flg=p.system_flg and g.is_type=p.is_type and g.out_order_sn=p.out_order_sn
	where (g.max_status = 1 and p.pay_id > g.min_id) or (g.max_status > 1 and p.`status` < g.max_status);");

	$db->exec(
	"update base_order_info o inner JOIN (
		SELECT a.order_id, (case when a.money_unpaid=0 then 3 when a.money_paid = 0 and a.money_unpaid > 0 then 1 else 2 end) as real_pay_status from app_order_account a 
		where a.order_id = {$order_id} 
	) g on g.order_id = o.id 
	set o.order_pay_status = g.real_pay_status
	where o.id = {$order_id} and o.order_pay_status < g.real_pay_status;");
		
	// 配货状态
	$num = $db->exec("update base_order_info set delivery_status = 2 where id={$order_id} and delivery_status = 1 and (order_pay_status = 3 or order_pay_status = 4) and (is_xianhuo = 1 or buchan_status in (4, 5));");
	
	// 证书类型
	$db->exec(
	"update app_order_details a left join front.diamond_info_all d on a.zhengshuhao = d.cert_id and ifnull(d.cert_id, '') <> ''
left join warehouse_shipping.warehouse_goods g on g.goods_id = IF(IFNULL(a.goods_id,'') = '',a.ext_goods_sn,a.goods_id)
set a.cert = IF(IFNULL(g.zhengshuleibie,'') = '',d.cert, g.zhengshuleibie)
where a.order_id = {$order_id} and ((ifnull(d.cert, '') <> '' or ifnull(g.zhengshuleibie, '') <> '') and a.cert <> IF(IFNULL(g.zhengshuleibie,'') = '',d.cert, g.zhengshuleibie));");

	echo $order_id. ':'. $delivery_status .' : '. $order_info['delivery_status'].PHP_EOL;

	if ($num > 0 || $delivery_status == 2 || $delivery_status == 3 || $order_info['delivery_status'] == 2 || $order_info['delivery_status'] == 3) {
		// 确保此订单在待配货列表可以找到
		echo 'checking delivery_status for order:'.$order_id.PHP_EOL;
		$exists = $db->getRow("select order_id from warehouse_shipping.order_distrib_todo where order_id = {$order_id};");
		if (!$exists) {
			$sql = 
				"SELECT id FROM app_order.`base_order_info` AS `a`
				WHERE a.id = {$order_id} and `a`.`referer` <> 'EGL' AND a.referer <> '天生一对加盟商'
				AND `a`.`delivery_status` IN (2, 3)
				AND `a`.`order_status` = '2'
				AND ((a.is_xianhuo = 0 and a.buchan_status in (4, 5)) or a.is_xianhuo = 1)
				AND EXISTS (
					SELECT
						1
					FROM
						app_order.app_order_details aod
					WHERE
						aod.order_id = a.id
					AND aod.is_return = 0
				) ; ";
			$row = $db->getRow($sql);
			if ($row) {
				// 需要添加到待配货列表
				$db->exec(
					"insert into warehouse_shipping.order_distrib_todo
					SELECT DISTINCT
						`a`.id as order_id,
						`a`.order_sn,
						`ad`.distribution_type,
						`ad`.shop_name,
					  `a`.create_user,
					  `a`.customer_source_id,
					  `a`.is_print_tihuo,
						`a`.department_id,
						`a`.create_time,
					  `a`.referer,
						`sc`.company_id as addr_company_id,
						`a`.delivery_status,
						`a`.is_xianhuo,
						`a`.apply_close,
					  `a`.apply_return,
						 (select create_time from app_order.app_order_action act where act.order_id = a.id order by act.action_id desc limit 1) as last_time
					FROM
						app_order.`base_order_info` AS `a`
					LEFT JOIN app_order.app_order_address AS ad ON a.id = ad.order_id
					LEFT JOIN cuteframe.sales_channels sc ON ad.shop_name = sc.channel_own
					WHERE a.id = {$order_id};");
				echo $order_id . ' was added to order_distrib_todo list'.PHP_EOL;
			}
		}
	}
}

?>
