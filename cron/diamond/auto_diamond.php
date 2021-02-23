<?php
/*[#YYB+
-- 裸钻自动获取
#]*/
error_reporting(E_ALL ^ E_DEPRECATED);
date_default_timezone_set("Asia/Shanghai");
header("Content-type:text/html;charset=utf8;");

define('IN_ECS',true);
define('ROOT_PATH', str_replace('auto_diamond.php', '', str_replace('\\', '/', __FILE__)));


define('KELA_ROOT', str_replace('\\','/',realpath(rtrim(ROOT_PATH,'/').'/../../')));//定义网站根目录

require(ROOT_PATH."data_config.ini.php");   //全局配置
require(ROOT_PATH."dia_model.php"); //裸钻数据模型类，添加更新操作
require(ROOT_PATH."dia_config.php");    //裸钻API配置
require(ROOT_PATH."dia_api.php");   //裸钻API
require(ROOT_PATH."app_mysql.php"); //数据对象
require(KELA_ROOT."/frame/common/data_config.php"); //网站配置

$config = array();
$config['db_type'] = 'mysql';
$config['db_port'] = 3306;
$config['db_name'] = 'front';

// release:
$config['db_host'] = '192.168.1.192';
$config['db_user'] = 'cuteman';
$config['db_pwd'] = 'QW@W#RSS33#E#';

// debug:
/*$config['db_host'] = '192.168.0.91';
$config['db_user'] = 'root';
$config['db_pwd'] = '123456';*/
/*
$config['db_host'] = '192.168.0.95';
$config['db_user'] = 'cuteman';
$config['db_pwd'] = 'QW@W#RSS33#E#';
*/
$db=new KELA_API_DB($config);

if($argv[1]){
    $_GET['act']=$argv[1];
    $_GET['isdownload']=$argv[2];
    $_GET['ft']=$argv[3];
}
$action = trim($_GET['act']);
//对应裸钻的供应商 
//$fromad_arr = array(1=>'kela',2=>'fiveonezuan',3=>'venus',4=>'dharam',5=>'diamondbyhk',6=>'diarough',7=>'emd',8=>'gd',9=>'jb',10=>'kapu',11=>'kgk',12=>'hy',13=>'leo',14=>'kiran',15=>'vir',16=>'karp',17=>'enjoy',18=>'changning', 19=>'kb',20=>"kg");
//查询定时程序是否开启
$from_ad_name = $action;

$sql="SELECT * FROM `diamond_vendor` WHERE `title` = '{$from_ad_name}'";
$kaiInfo = $db->getRow($sql);

if($kaiInfo){
    //judge is open
    if($kaiInfo['activate']){
        //it is ok.  
        if($from_ad_name=='leo' && !in_array(date('w'),array('0','6'))){
            echo $from_ad_name."只有周六抓取";
            exit;
        }
        $from_ad_ids[$kaiInfo['title']]=$kaiInfo['vendor_id'];
    }else{
        echo $from_ad_name."定时抓取程序暂时关闭";
        exit;
    }
    $from_ad = $kaiInfo['vendor_id'];
}else{
    echo $from_ad_name."供应商不存在，请添加";
    exit;
}

$dia_db=$db;
$db_dia=$db;

define('DIAMOND_ACTIVE_OPEN',false);
//define
//20150820 7=>6.6
//20150826 6.6=>6.8
// 2016-1-11 7 -> 7.2
$usdToRmb = THE_EXCHANGE_RATE;       //美元转人民币
/*if ($from_ad == 6) { // diarough
    $usdToRmb = 7;
}*/

define("DOLLARTORMB",   $usdToRmb);

define("JIAJIALV",  1.043);     //加价率
// define("TIAOJIALV",  1.03);     //调价率  --暂时不用
define("MARKET_RATE",   1.5);       //市场/BDD
define("MEMBER_RATE",   0.9);       //vip/BDD
define("MEMBER_RATE_60",    0.9);       //vip/BDD
define("JXCCHENGBENTOSHOP",     1.44);  //进销存成本价转销售价  小于70分
define("JXCCHENGBENTOSHOP_70",      1.44);  //进销存成本价转销售价 大于70分
define("JXCCHENGBENTOSHOP_100",     1.44);  //进销存成本价转销售价 大于70分
define("JXCCHENGBENTOSHOP_EGL",     2); //EGL进销存成本价转销售价

$atd = new autodiamond($from_ad , $_GET['isdownload'] , $_GET['ft']);
