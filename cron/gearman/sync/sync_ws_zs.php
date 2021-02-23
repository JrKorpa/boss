<?php

$file = __DIR__ .'/sync_sqls/ws_'.date('Ymd_h').'.sql';
$data_path_dir = dirname($file);
if (!is_dir($data_path_dir)) {
	@mkdir($data_path_dir, 0777, true);
}

$sql_tmpl = "mysqldump -u %s -h %s --password=%s --skip-add-locks --add-drop-table -B %s --tables %s > %s";
$cmd = sprintf($sql_tmpl, 'cuteman', '192.168.1.59', 'QW@W#RSS33#E#', 
'warehouse_shipping',
'warehouse warehouse_box warehouse_rel',
$file
);
file_put_contents(dirname($file).'/sync_ws_zs.log', date("Y-m-d H:i:s").' - dump...'.PHP_EOL, FILE_APPEND);
exec($cmd);

$sql_tar = "mysql -u %s -h %s --password=%s -D %s  < %s";
$command = sprintf($sql_tar, 'cuteman', '192.168.1.71', 'QW@W#RSS33#E#', 'warehouse_shipping', $file);
file_put_contents(dirname($file).'/sync_ws_zs.log', date("Y-m-d H:i:s").' - importing...'.PHP_EOL, FILE_APPEND);
exec($command);

file_put_contents(dirname($file).'/sync_ws_zs.log', date("Y-m-d H:i:s").' - done!!!'.PHP_EOL, FILE_APPEND);

?>
