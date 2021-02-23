<?php

function on_bill_S_created($data, $db) {

	if (!isset($data['order_sn']) || empty($data['order_sn'])) return false;
	
	$order_sn = $data['order_sn'];
	if ($order_sn) {
		echo 'start processing S bill:'.$order_sn.PHP_EOL;
		
		//从待配货列表中删除该订单
		$db->exec("delete from order_distrib_todo where order_sn = '{$order_sn}';");

		//门店发货
		$is_quick_distribute = $data['is_qk_distrib'];
		if ($is_quick_distribute) {
			$order_id = $data['order_id'];
			$now = $data['timestamp'];
			$user = $data['user'];

			//$goods_ids = $db->getAll("select goods_id from app_order.app_order_details where order_id ={$order_id} and is_return = 0 and ifnull(goods_id, '') <> '';");
			$goods_ids = $db->getAll("select g.goods_id from warehouse_shipping.warehouse_bill_goods g inner join warehouse_shipping.warehouse_bill b on b.id = g.bill_id where b.bill_type ='S' and b.order_sn = '{$order_sn}' and b.bill_status in (1,2);");
			$goods_ids_str = implode(',', array_column($goods_ids, 'goods_id'));
			
			$sql = 
			"update app_order.base_order_info as a left join app_order.app_order_details as b on a.id=b.order_id set a.send_good_status='2',a.shipfreight_time='".$now."',b.send_good_status=2 where a.id={$order_id};
			 UPDATE warehouse_shipping.`warehouse_goods`  SET `is_on_sale`=3,`chuku_time`= '{$now}' WHERE goods_id in ({$goods_ids_str});
			 update warehouse_shipping.`warehouse_bill` set bill_status=2, check_time='{$now}',check_user='{$user}'  where order_sn ='{$order_sn}' and bill_type='S' and bill_status=1;
			 UPDATE warehouse_shipping.`goods_warehouse` SET `box_id` = '0', `create_time` = '0000-00-00 00:00:00', `create_user` = '' WHERE `good_id` IN ({$goods_ids_str});	
			 ";

			$order_shipping = $db->getRow("SELECT express_id, freight_no, e.exp_name from app_order.app_order_address a left join cuteframe.express e on e.id = a.express_id and e.is_deleted =0 where order_id = {$order_id};"); 
			if ($order_shipping['express_id'] == '10') {
				$remark = '已发货，上门取货（一键发货）';
			} else {
				$remark = "已发货，{$order_shipping['exp_name']}:{$order_shipping['freight_no']}（一键发货）";
			}

			$sql .= "insert into app_order.app_order_action(order_id, order_status,shipping_status,pay_status,create_user, create_time, remark) select id, order_status, send_good_status, order_pay_status, '{$user}', '{$now}', '{$remark}' from app_order.base_order_info where id = {$order_id};";
			try {
				$db->exec($sql);

				global $worker;
				$data['event'] = 'bill_S_checked';
			    $worker->dispatch("warehouse", $data['sys_scope'], $data);
			} catch(Exception $ex) {
				file_put_contents(date('Ymd').'-bill_S_created.log',  json_encode(array('data'=> $data, 'error'=> $ex->getMessage())).PHP_EOL, FILE_APPEND);
			}

			echo 'finish quick distribution for order:'.$order_sn.PHP_EOL;
		}
		
	}
}

?>