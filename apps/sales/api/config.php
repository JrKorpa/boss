<?php
//$db_host	=	"192.168.1.251";
//$db_name    =   "cuteframe";
//$db_name    =   "app_order";
//$db_user    =   "develop";
//$db_pass	=	"123456";

$xx = new IniFile();
$xx->Load(KELA_PATH.'/common/web.config');
$xxx= $xx->GetSection('DbConfig28');

$db_host	=	$xxx['db_host'];
$db_name	=	$xxx['db_name'];
$db_user	=	$xxx['db_user'];
$db_pass	=	$xxx['db_pwd'];
$db_port    =   $xxx['db_port'];

$config = array();
$config['db_type'] = 'mysql';
$config['db_host'] = $db_host;
$config['db_port'] = $db_port;
$config['db_name'] = $db_name;
$config['db_user'] = $db_user;
$config['db_pwd'] = $db_pass;
