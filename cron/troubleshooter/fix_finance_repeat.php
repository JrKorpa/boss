<?php
include_once dirname(__FILE__).'/template.php';
// 清洗财务重复提报问题
$sql = "DELETE p from finance.app_order_pay_action p INNER JOIN 
(
	select order_sn, min(pay_id) as min_id, max(`STATUS`) as max_status, order_amount, deposit, remark, pay_type, balance, pay_time, order_consignee, department, opter_name, system_flg, is_type, out_order_sn from finance.app_order_pay_action
	where `order_id` > 1935211 and `is_type` = 1 and `status` != 4
	group by order_sn, order_amount, deposit, remark, pay_type, balance, pay_time, order_consignee, department, opter_name, system_flg, is_type, out_order_sn
	HAVING(count(1) > 1)
) g on g.order_sn = p.order_sn and g.order_amount=p.order_amount and g.deposit=p.deposit and g.remark=p.remark and g.pay_type=p.pay_type and g.balance=p.balance and g.pay_time=p.pay_time and g.order_consignee =p.order_consignee and g.department=p.department and g.opter_name=p.opter_name and g.system_flg=p.system_flg and g.is_type=p.is_type and g.out_order_sn=p.out_order_sn
where (g.max_status = 1 and p.pay_id > g.min_id) or (g.max_status > 1 and p.`status` < g.max_status)"; 

$fixer = new Template();
$fixer->exec($sql); 

?>





