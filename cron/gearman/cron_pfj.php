<?php
include 'Worker.php';
require_once 'MysqlDB.class.php';

$db_conf = [
		'dsn'=>"mysql:host=192.168.1.192;dbname=front",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
	];

// 是否是增量
$is_delta = false;
if (isset($argv[1]) && $argv[1] == 'delta') {
	$is_delta = true;
	$conn = new MysqlDB($db_conf);
	$count = $conn->getOne('SELECT count(1) from diamond_info where ifnull(pifajia,0) = 0');
	if ($count < 2200) return;
}

$job_server_list = array(
	['host'=> '192.168.1.58', 'port' => 4730]
);
$worker = new Worker($job_server_list, $db_conf, true);
$worker->dispatch('task', 'boss', array('event' => 'dia_upserted', 'refresh_pifajia' => 1, 'delta' => $is_delta));

?>
