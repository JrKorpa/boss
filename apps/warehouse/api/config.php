<?php
//$db_host	=	"localhost";
//$db_name    =   "warehouse_shipping";
//$db_user    =   "root";
//$db_pass	=	"root";
//require_once "frame/class/IniFile.class.php";

$xx = new IniFile();
$xx->Load(KELA_PATH.'/common/web.config');
$xxx= $xx->GetSection('DbConfig22');

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


?>
