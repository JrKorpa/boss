<?php
set_time_limit(0);
header("Content-type:text/html;charset=utf-8");
require '../../frame/common/define.php';//引用常量

define('KELA_VIP_ROOT', KELA_ROOT.'/cron/vip');//定义网站根目录
require_once KELA_VIP_ROOT.'/common.php';
require_once KELA_ROOT.'/frame/class/IniFile.class.php';
require_once KELA_ROOT.'/frame/class/DB.class.php';
require_once KELA_VIP_ROOT.'/db/DbModel.class.php';
require_once KELA_ROOT.'/frame/vendor/vopsdk/VopWrapper.class.php';

$act = isset($_GET['act'])?$_GET['act']:"";
$act = strtolower($act);
$moduleFile = './module/'.$act.'.php';
if(file_exists($moduleFile)){
    require $moduleFile;  
    exit;  
}else{
   $files = scandir('./module/');
    //print_r($files);
    echo "唯品会同步数据API列表:<hr/>";
    $i =0;
    foreach ($files as $file){
        if(strpos($file,".php")){
            $i++;
            $url ="http://".$_SERVER['HTTP_HOST']."/cron/vip/index.php?act=".str_replace(".php","",$file);
            echo "{$i}.<a href='{$url}' target='_blank'>{$url}</a><br/>";
        }
    }
    exit;
}