<?php
set_time_limit(0);
error_reporting(E_ALL);

$sql = "CALL link_jxs_jxsorder;
		CALL sync_jxs_orders;
		SELECT @batch_id = MAX(batch_id) FROM jxs_order;
		CALL calc_jxs_profit(0, @batch_id);";
$db = mysqli_connect('192.168.1.94', 'cuteman', 'QW@W#RSS33#E#', 'finance');
var_dump(mysqli_query($db, $sql));
?>










