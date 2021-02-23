<?php

global $lib_path;
require_once($lib_path .'/Utils.class.php');

function on_shop_addr_changed($data, $db) {

	$old_name = $data['old_name'];
	$new_name = $data['new_name'];
	$old_addr = $data['old_addr'];
	$new_addr = $data['new_addr'];
	
	$sql = "update app_order_address a inner join base_order_info i on i.id = a.order_id
set a.shop_name = '{$new_name}', a.address = '{$new_addr}'
where i.send_good_status < 2 and a.shop_name = '{$old_name}';";
	
	$db->exec($sql);
}


?>
