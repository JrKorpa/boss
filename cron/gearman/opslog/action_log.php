<?php


function on_action_log($data, $db) {

	$log = $data['log_info'];
	if (empty($log)) return;
	
	$db->insert('user_operation_log', $log);	
	echo "log ({$log['module']},{$log['controller']},{$log['action']}) processed.".PHP_EOL;
}


?>