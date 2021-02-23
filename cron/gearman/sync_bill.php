<?php

require_once('MysqlDB.class.php');
require_once('Worker.php');

$db_conf = [
	'dsn'=>"mysql:host=192.168.1.132;dbname=warehouse_shipping",
	'user'=>"cuteman",
	'password'=>"QW@W#RSS33#E#",
	'charset' => 'utf8'
];

$job_server_list = array(
	['host'=> '192.168.1.65', 'port' => 4730]
);

$worker = new Worker($job_server_list, $db_conf, true);

$db = new MysqlDB($db_conf);
$list = $db->getAll("
SELECT b.id, b.bill_no from warehouse_bill b left join bill_sync s on s.bill_id = b.id
inner join cuteframe.company c on c.id = b.to_company_id and c.company_type = 3
inner join cuteframe.company s on s.id = b.from_company_id and (s.company_type  = 4 or s.id = 58)
where b.check_time >='2018-08-15' and s.latest_push_time is null and ((b.bill_type = 'P' and b.bill_status = 2) 
or (b.bill_type = 'WF' and b.bill_status = 1)) order by b.id desc limit 100");

foreach($list as $item) {	
	$worker->dispatch('ishop', 'ishop', array('event' => 'sync_bill', 'bill_id' => $item['id'], 'bill_no' => $item['bill_no']));
	sleep(2);
}

/*
$list = $db->getAll(
"SELECT b.id, b.bill_no from warehouse_bill b left join bill_sync s on s.bill_id = b.id
left join cuteframe.company c on c.id = b.to_company_id and c.company_type = 3
where s.latest_push_time is null and 
((b.bill_type = 'H' or b.bill_type ='WF') and b.to_company_id = 58 and b.bill_status = 2 and b.company_from ='ishop')
order by b.id desc limit 100; ");

foreach($list as $item) {	
	$worker->dispatch('ishop', 'ishop', array('event' => 'sync_bill', 'bill_id' => $item['id'], 'bill_no' => $item['bill_no']));
	sleep(2);
}
*/
