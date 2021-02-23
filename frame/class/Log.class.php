<?php
/**
 *  -------------------------------------------------
 *   @file		: Log.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		:
 *   @update	:
 *  -------------------------------------------------
 */
/**
 *  -------------------------------------------------
 *	日志处理类
 *
 *  -------------------------------------------------
 */
class Log
{
	// 日志级别
	const FATAL		= 'FATAL'; // 严重错误: 导致系统崩溃无法使用
    const ALERT		= 'ALERT';  // 警戒性错误: 必须被立即修改的错误
    const ERROR		= 'ERROR';  // 一般错误: 一般性错误
    const WARNING	= 'WARNING';  // 警告性错误: 需要发出警告的错误
    const NOTICE	= 'NOTICE';  // 通知: 程序可以运行但是还不够完美的错误
    const INFO		= 'INFO';  // 信息: 程序输出信息
    const DEBUG		= 'DEBUG';  // 调试: 调试信息
    const SQL		= 'SQL';  // SQL：SQL语句 注意只在调试模式开启时有效

    //日志信息
    static $log = array();

	/**
	 *	record，记录日志 并且会过滤未经设置的级别
	 *
	 *	@param string $message 日志信息
	 *	@param string $level  日志级别
	 *
	 */
    static public function record($message,$level = '')
    {
        if (((defined("LOG_RECORD") && LOG_RECORD) || in_array($level,explode(",",LOG_LEVEL))) && APP_DEBUG)
		{
			if($level!='')
			{
				$message =$level.":".$message;
			}
            self::$log[] = date("[ c ]")." ".$message.PHP_EOL;
        }
    }

	/**
	 *	save，日志保存
	 *
	 *	@param integer $type 日志记录方式
	 *	@param string $destination  写入文件名(含路径)
	 *	@param string $extra 额外参数
	 *
	 */
    static function save($type=3,$destination='',$extra='')
    {
		if(!(defined('LOG_RECORD') && LOG_RECORD) || APP_DEBUG){
			return;
		}
		if(empty($destination))
		{
            $destination = LOG_PATH.date('Y_m_d').".log";
		}
        if($type==3)
		{ // 文件方式记录日志信息
            //检测日志文件大小，超过配置大小则备份日志文件重新生成
            if(is_file($destination) && LOG_FILE_SIZE <= filesize($destination) )
			{
                  rename($destination,dirname($destination).'/'.time().'-'.basename($destination));
			}
        }
        error_log(implode("",self::$log).PHP_EOL, $type,$destination ,$extra);
        // 保存后清空日志缓存
        self::$log = array();
    }

	/**
	 *	write，日志直接写入
	 *
	 *	@param string $message 日志信息
	 *	@param string $level  日志级别
	 *	@param integer $type 日志记录方式
	 *	@param string $destination  写入目标
	 *	@param string $extra 额外参数
	 *
	 */
    static function write($message,$level='',$type=3,$destination='',$extra='')
    {
        $now = date("[ c ]");
        if(empty($destination))
		{
			$destination = (defined('LOG_PATH') ? LOG_PATH : KELA_PATH."/").date('Y_m_d').".log";
		}
        if($type==3)
		{ // 文件方式记录日志信息
            //检测日志文件大小，超过配置大小则备份日志文件重新生成
            if(is_file($destination) && LOG_FILE_SIZE <= filesize($destination) )
			{
                rename($destination,dirname($destination).'/'.time().'-'.basename($destination));
			}
        }
		if($level)
		{
			$level.=": ";
		}
		error_log("{$now} {$message}".PHP_EOL."FILE ".$_SERVER["PHP_SELF"].PHP_EOL, $type,$destination ,$extra);
        //clearstatcache();
    }

}

////访问者ip地址，和当前的错误页面
//define('LOG_LEVEL','EMERG,ALERT,ERR');
//define('LOG_RECORD',true);
//define('LOG_PATH', './');//  /当前盘根目录
//define('LOG_FILE_SIZE', '1048576');//1M
//define('LOG_FILE', 'a.log');
//Log::record("test");
//Log::save();
//Log::write("i am a boy");
?>