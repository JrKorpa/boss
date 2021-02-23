<?php
include 'Worker.php';

$db_conf = [
	'boss' => [
		'dsn'=>"mysql:host=192.168.1.192;dbname=warehouse_shipping",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
		],
	'zhanting' => [
		'dsn'=>"mysql:host=192.168.1.132;dbname=warehouse_shipping",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
	]	
];

$job_server_list = array(
	['host'=> '192.168.1.58', 'port' => 4730],
	['host'=> '192.168.1.61', 'port' => 4730]
);

$ishop_job_server = [
    ['host'=> '192.168.1.65', 'port' => 4730],
];
		
$worker = new Worker($job_server_list, $db_conf, true);
$worker->registry_queue('warehouse');
$worker->start();

?>
