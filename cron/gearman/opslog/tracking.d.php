<?php


function on_tracking($data, $db) {

	$db->exec("insert into tracking(module, controller, action, num) values('{$data['mod']}', '{$data['con']}', '{$data['act']}', 1) on DUPLICATE KEY update num = num + 1;");	
}


?>