<?php
header("Content-type:text/html;charset=utf-8");
require_once('DealData.class.php');


$new_conf = [
	'dsn'=>"mysql:host=203.130.44.199;dbname=warehouse_shipping",
	'user'=>"cuteman",
	'password'=>"QW@W#RSS33#E#",
];
$old_conf = [
        'dsn'     => "mysql:host=192.168.1.79;dbname=jxc;",
        'user'    => "root",
        'password'=> "zUN5IDtRF5R@",
        'charset' => 'utf8'
	];
/*
$new_conf = [
	'dsn'=>"mysql:host=192.168.1.63;dbname=warehouse_shipping",
	'user'=>"yangfuyou",
	'password'=>"yangfuyou1q2w3e",
];

$old_conf = [
        'dsn'     => "mysql:host=192.168.1.52;dbname=jxc;",
        'user'    => "develop",
        'password'=> "123456",
        'charset' => 'utf8'
	];
 
*/

$n_table = 'warehouse_goods';	
$model = new DealData($new_conf,$old_conf);
$model->ModifyGoodsJiejia();
?>