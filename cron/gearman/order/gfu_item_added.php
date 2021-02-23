<?php

require_once dirname(dirname(__FILE__)).'/Utils.class.php';
require_once dirname(dirname(__FILE__)).'/MysqlDB.class.php';
require_once dirname(dirname(__FILE__)).'/libs/gfu/GfuClient.php';

function on_gfu_item_added($data, $ori_db) {

	if (!isset($data['order_id']) || !isset($data['goods_ids']) || empty($data['goods_ids'])) return false;
	
	$order_id = $data['order_id'];
	$order_sn = $data['order_sn'];
	$goods_ids = $data['goods_ids'];
	
	if ($data['sys_scope'] == 'zhanting') {
		global $db_conf;
		$boss_db = new MysqlDB($db_conf['boss']);
	} else {
		$boss_db = $ori_db;
	}

	// 1. 确保货品是港福的
	$gid_barcode_dict = array();
	foreach($goods_ids as $gid) {
		$gid_arr = Utils::eexplode('-', $gid);
		if (count($gid_arr) <> 2) {
			// 港福货号有特殊, BDD款号-港福条码；此处暂不理裸钻
			continue;
		}

		$gid_barcode_dict[$gid] = $gid_arr[1];
	}
	
	// 2. 去港福下单
	$gfu_goods_ids = implode("','", array_keys($gid_barcode_dict));
	$gfu_goods = $boss_db->getAll("select s.gfu_id, k.kl_goods_id, k.cost_price from front.warehouse_goods_gfu_kt k inner join front.warehouse_goods_gfu s on s.goods_sn = k.goods_sn where kl_status = 2 and kl_order_sn = '{$order_sn}' and kl_goods_id in ('{$gfu_goods_ids}');");

	if (empty($gfu_goods) || count($gid_barcode_dict) <> count($gfu_goods)) {
		echo '货品数据可能有问题， 请核实订单号:'.$order_sn.' 货号:'.$gfu_goods_ids.PHP_EOL;
		mail("quanxiaoyun@kela.cn", '货品数据问题', '订单号:'.$order_sn.' 货号:'.$gfu_goods_ids);
		return;	
	}

	$order_items = $ori_db->getAll("select id, goods_id, zhengshuhao, details_remark, kezi, zhiquan, face_work, xiangqian from app_order.app_order_details where order_id = {$order_id} and goods_id in ('{$gfu_goods_ids}');");
	$order_items = Utils::indexArray($order_items, 'goods_id');

	$gfu_order_goods = array();
	$remarks = array();
	foreach($gfu_goods as $g) {
		$item = $order_items[$g['kl_goods_id']][0];
		$gfu_order_goods[] = array(
			'goodsType' => 3,
			'goodsId'   => $g['gfu_id'],
			'barcode'   => $gid_barcode_dict[$g['kl_goods_id']],
			'kezi'	    => $item['kezi'],
			'diaCertNo' => $item['zhengshuhao'],
			'price'     => 0
		);
		$remarks[] = '	【刻字】:'. $item['kezi'] .'	【指圈号】:'.$item['zhiquan'].'	【表面工艺】:'.$item['face_work'].'	【镶嵌要求】:'.$item['xiangqian'].'	【说明】:'.$item['details_remark'];
	}

	echo 'try to create order in gfu...'.PHP_EOL;
	$gfu_order_sn = create_gfu_order($gfu_order_goods, $data['sys_scope'], $order_sn, implode(',', $remarks));
	if (!empty($gfu_order_sn)) {
		echo '去港福下单成功，单号为 '.$gfu_order_sn.PHP_EOL;
		$sqls = array();
		foreach($order_items as $k=>$it) {
			$sqls[] = "update front.warehouse_goods_gfu_kt set gfu_order_sn = '{$gfu_order_sn}', kl_item_id = {$it[0]['id']}, kl_status = 3 where kl_goods_id = '{$k}' and kl_order_sn = '{$order_sn}' and kl_status = 2 ";
		}

		// 回写港福订单号
		$boss_db->exec(implode(';', $sqls));
		echo '回写订单号及货品状态成功'.PHP_EOL;
		return;
	}

	// 3. 下单失败，回写
	echo 'fail to create order in gfu.'.PHP_EOL;
	global $worker;
	foreach($gid_barcode_dict as $k => $v) {
		// 在订单日志里增加日志
		$remark = '<font color="red">下手太慢了，'.$k.' 该货品已被同行抢先锁定了';
		$ori_db->exec(
"INSERT INTO app_order.app_order_action (`order_id`, `order_status`, `shipping_status`, `pay_status`, `create_user`, `create_time`, `remark`)
select id, order_status, send_good_status, order_pay_status, 'system', now(), '{$remark}' from app_order.base_order_info where id = {$order_id};");

		$worker->dispatch('opslog', $data['sys_scope'], array('event' => 'devnotify', 'msg' => "{$data['sys_scope']} 订单号{$order_sn}，商品 {$k} 去港福下单失败了，快去确认。"));
	}
}

function create_gfu_order($pre_order_goods, $sys_scope, $kl_order_sn, $remark) {

	$gfu = GfuClient::getInstance();
	$resp = $gfu->createOrder($pre_order_goods, '订单'.$kl_order_sn.$remark, $sys_scope == 'boss');

	if ($resp['error'] == 1) {
		return false;
	}

	return $resp['order_no'];
}


	


?>