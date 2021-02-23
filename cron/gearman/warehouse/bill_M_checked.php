<?php

function on_bill_M_checked($data, $db) {

	if (!isset($data['bill_id']) || empty($data['bill_id'])) return false;
	
	$bill_id = $data['bill_id'];
	if ($bill_id > 0) {
		echo 'start processing M bill:'.$bill_id.PHP_EOL;
		
		$bill = $db->getRow("select check_time, bill_no from warehouse_bill where id = {$bill_id}");
		$check_time = $bill['check_time'];
		$bill_no = $bill['bill_no'];
		
		if (isset($data['goods_ids'])) {
			$goods_ids = preg_split('/,/', $data['goods_ids'], -1, PREG_SPLIT_NO_EMPTY);
		} else {
			$goods_ids = $db->getAll("select goods_id from warehouse_bill_goods where bill_id= {$bill_id}");
			$goods_ids = array_column($goods_ids, 'goods_id');
		}
		
		foreach($goods_ids as $gid) {
			try {
				$db->exec("
update goods_io i inner join (
SELECT g.goods_id, b.to_warehouse_id FROM warehouse_bill_goods g
inner join warehouse_bill b on b.id = g.bill_id and b.bill_status in (2, 4)
where g.goods_id = {$gid} and g.bill_id < {$bill_id} and ifnull(b.to_warehouse_id, 0) > 0 order by g.bill_id desc limit 1
) g on g.goods_id = i.goods_id and i.warehouse_id = g.to_warehouse_id
set i.out_time = '{$check_time}', i.out_bill_no = '{$bill_no}'
where g.goods_id = {$gid}");
			} catch(Exception $ex) {
				file_put_contents(date('Ymd').'-bill_M_checked.log',  json_encode(array('goods_id'=> $gid, 'bill_id' => $bill_id, 'error'=> $ex->getMessage())).PHP_EOL, FILE_APPEND);
			}
		}
		
		$db->exec(
"insert into goods_io(goods_id,warehouse_id,in_time,birth_time,in_bill_no) 
select wbg.goods_id, b.to_warehouse_id as warehouse_id, b.check_time as in_time, g.addtime as birth_time, b.bill_no as in_bill_no from warehouse_bill_goods wbg 
INNER JOIN warehouse_bill b on b.id = wbg.bill_id and b.bill_type ='M'
inner join warehouse_goods g on g.goods_id = wbg.goods_id
where b.id = {$bill_id};");
	}
}

?>