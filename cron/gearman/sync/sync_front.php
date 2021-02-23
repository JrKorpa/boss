<?php

$file = __DIR__ .'/sync_sqls/front_'.date('Ymd_h').'.sql';
$data_path_dir = dirname($file);
if (!is_dir($data_path_dir)) {
	@mkdir($data_path_dir, 0777, true);
}

$sql_tmpl = "mysqldump -u %s -h %s --password=%s --skip-add-locks --add-drop-table -B %s --tables %s > %s";
$cmd = sprintf($sql_tmpl, 'cuteman', '192.168.1.59', 'QW@W#RSS33#E#', 
'front',
'app_attribute  app_attribute_ext app_attribute_value app_cat_type app_jinsun app_material_info app_product_type app_style_baoxianfee app_style_fee app_style_for app_style_gallery app_style_id app_style_xilie app_xiangkou app_xilie_config base_style_info rel_cat_attribute rel_style_attribute rel_style_factory app_style_quickdiy diy_xiangkou_config rel_style_stone',
$file
);
file_put_contents(dirname($file).'/sync_front.log', date("Y-m-d H:i:s").' - dump...'.PHP_EOL, FILE_APPEND);
exec($cmd);

$sql_tar = "mysql -u %s -h %s --password=%s -D %s  < %s";
$command = sprintf($sql_tar, 'cuteman', '192.168.1.132', 'QW@W#RSS33#E#', 'front', $file);
$command_zs = sprintf($sql_tar, 'cuteman', '192.168.1.71', 'QW@W#RSS33#E#', 'front', $file);

file_put_contents(dirname($file).'/sync_front.log', date("Y-m-d H:i:s").' - importing...'.PHP_EOL, FILE_APPEND);
exec($command);
file_put_contents(dirname($file).'/sync_front.log', date("Y-m-d H:i:s").' - done!!!'.PHP_EOL, FILE_APPEND);

file_put_contents(dirname($file).'/sync_front_zs.log', date("Y-m-d H:i:s").' - importing...'.PHP_EOL, FILE_APPEND);
exec($command_zs);
file_put_contents(dirname($file).'/sync_front_zs.log', date("Y-m-d H:i:s").' - done!!!'.PHP_EOL, FILE_APPEND);


$job_server_list = array(
	['host'=> '192.168.1.58', 'port' => 4730],
	['host'=> '192.168.1.61', 'port' => 4730]
);

$gearmanc = new GearmanClient();
foreach ($job_server_list as $serv) {
	$gearmanc->addServer($serv['host'], $serv['port']);
}

$gearmanc->doBackground('buchan', json_encode(array('event' => 'styles_synced', 'sys_scope'=>'zhanting')));


?>
