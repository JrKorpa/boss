<?php
/*
*  -------------------------------------------------
*   @file		: define.php
*   @link		:  www.kela.cn
*   @copyright	: 2014-2024 kela Inc
*   @author		: Laipiyang <462166282@qq.com>
*   @date		: 2014-04-28
*   @update		:
*  -------------------------------------------------
*/
//路径
if(version_compare(phpversion(), '5.3', 'lt')){
	define('KELA_PATH',str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));//定义框架所在目录
}else{
	define('KELA_PATH',str_replace('\\','/',realpath(dirname(__DIR__))));//定义框架所在目录
}
define('KELA_ROOT', str_replace('\\','/',realpath(rtrim(KELA_PATH,'/').'/../')));//定义网站根目录
define('KELA_CLASS', rtrim(KELA_PATH,'/').'/class/');//定义框架类目录

define('AUTHOR_TOKEN_KEY', "KE^LA-US_ER");
//define("AUTH_KEY", "VX_BBkw-df9y9ci7Z6QdNZKPco");
define("AUTH_KEY", "VXKELA");//最长8位

//URL
define ("ROOT_DOMAIN", "local.boss.com");

define('DB_SEQUENCE_TABLENAME', 'kela_seq');
define('APP_ROOT', KELA_ROOT.'/apps/');
define('KELA_LEFT', '<%');
define('KELA_RIGHT', '%>');

define('LOG_LEVEL','EMERG,ALERT,ERR');
define('LOG_RECORD',true);
define('LOG_FILE_SIZE', '1048576');//1M
define('LOG_FILE', 'kela.log');

/*发送邮件配置信息*/
define('EMAIL_HOST',    'smtp.exmail.qq.com');
define('EMAIL_USER',    'server@kela.cn');
define('EMAIL_PASSWORD','s123456');
define('EMAIL_FROM',    'server@kela.cn');
define('EMAIL_PORT',    465);
define('EMAIL_FROM_NAME','');

define('USER_UPDATE_TIME', 7); //用户修改密码时间
/*
define('MEMCACHE_SERVER','192.168.0.94');
define('MEMCACHE_PORT','11211');
*/
define('DD_USE_MEMCACHE', false); //数据字典是否使用memcache

define('JOB_SERVER', json_encode(array(
    array('host'=> '192.168.0.93', 'port'=> 4730),
   // array('host'=> '192.168.1.61', 'port'=> 4730)
)));

