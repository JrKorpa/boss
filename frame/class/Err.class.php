<?php
/**
 *  -------------------------------------------------
 *   @file		: Err.class.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-05-13
 *   @update	:
 *  -------------------------------------------------
 */
/**
 *	错误处理类
 *
 *
 *
 */
class Err
{
	/**
	 *	trace，异常处理
	 *
	 *	@param mixed $exception 异常对象
	 *
	 *	@return Array
	 *
	 */
	public static function trace($e=null)
    {
		if($e==null || !($e instanceof Exception)) return;

		$traceInfo=array();
        $time = date("Y-m-d H:i:s");

        $trace = $e->getTrace();
		krsort($trace);
		$trace[] = array('file' => $e->getFile(), 'line' => $e->getLine(), 'function' => 'break');
		foreach ($trace as $error)
		{
			if (!empty($error['function']))
			{
				$fun = '';
				if (!empty($error['class']))
				{
					$fun .= $error['class'] . $error['type'];
				}
				$fun .= $error['function'] . '(';
				if (!empty($error['args']))
				{
					$mark = '';
					foreach ($error['args'] as $arg)
					{
						$fun .= $mark;
						if (is_array($arg))
						{
							$fun .= print_r($arg,true);
						}
						else if (is_bool($arg))
						{
							$fun .= $arg ? 'true' : 'false';
						}
						else if (is_int($arg))
						{
							$fun .= APP_DEBUG ? $arg : '%d';
						}
						else if (is_float($arg))
						{
							$fun .= APP_DEBUG ? $arg : '%f';
						}
						else if (is_string($arg))
						{
							if($error['function']=='handle_error')
							{
								$_arg = (strlen(trim($arg))>10) ? substr(trim($arg), 0, 10).' ...' : trim($arg) ;
								$fun .= APP_DEBUG ? '\'' . htmlspecialchars($arg) . '\'' : '\'' . htmlspecialchars($_arg) . '\'';
							}
							else
							{
								$fun .= '\'' . htmlspecialchars($arg) . '\'';
							}
						}
						else
						{

						}
						$mark = ', ';
					}
				}
				$fun .= ')';
				$error['function'] = $fun;
			}
			if (!isset($error['line']))
			{
				continue;
			}
			$traceInfo[] = array('time'=>$time,'file' => str_replace('\\', '/', $error['file']), 'line' => $error['line'], 'function' => $error['function']);
		}

        // 记录 Exception 日志
        if(defined('LOG_RECORD') && LOG_RECORD && !APP_DEBUG)
		{
            Log::Write($e->getMessage());
        }
		//  调试模式下显示
		if(APP_DEBUG)
		{
			self::showError($e->getMessage(),$traceInfo);
		}
		return $traceInfo;
    }

	/**
	 * showError，显示错误
	 *
	 * @param string $errorMsg
	 * @param Array $traceInfo
	 *
	 */
	public static function showError ($errorMsg,$traceInfo)
	{
		@ob_end_clean();//清除界面输出
		$str = <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>Error</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="ROBOTS" content="NOINDEX,NOFOLLOW,NOARCHIVE" />
	<style type="text/css">
	<!--
	body { background-color: white; color: black; font: 9pt/11pt verdana, arial, sans-serif;}
	#container {margin: 10px;}
	#message {width: 1024px; color: black;}
	.red {color: red;}
	a:link {font: 9pt/11pt verdana, arial, sans-serif; color: red;}
	a:visited {font: 9pt/11pt verdana, arial, sans-serif; color: #4e4e4e;}
	h1 {color: #FF0000; font: 18pt "Verdana"; margin-bottom: 0.5em;}
	.bg1 {background-color: #FFFFCC;}
	.bg2 {background-color: #EEEEEE;}
	.table {background: #AAAAAA; font: 11pt Menlo,Consolas,"Lucida Console"}
	.info {
		background: none repeat scroll 0 0 #F3F3F3;
		border: 0px solid #aaaaaa;
		border-radius: 10px 10px 10px 10px;
		color: #000000;
		font-size: 11pt;
		line-height: 160%;
		margin-bottom: 1em;
		padding: 1em;
	}

	.help {
		background: #F3F3F3;
		border-radius: 10px 10px 10px 10px;
		font: 12px verdana, arial, sans-serif;
		text-align: center;
		line-height: 160%;
		padding: 1em;
	}

	.sql {
		background: none repeat scroll 0 0 #FFFFCC;
		border: 1px solid #aaaaaa;
		color: #000000;
		font: arial, sans-serif;
		font-size: 9pt;
		line-height: 160%;
		margin-top: 1em;
		padding: 4px;
	}
	-->
	</style>
</head>
<body>
<div id="container">
<h1>Error</h1>
<div class='info'>$errorMsg</div>
EOT;
		if (!empty($traceInfo)) {
			$str .='<div class="info">';
			$str .='<p><strong>PHP Debug</strong></p>';
			$str .='<table cellpadding="5" cellspacing="1" width="100%" class="table"><tbody>';
			if (is_array($traceInfo))
			{
				$str .='<tr class="bg2"><td>No.</td><td>File</td><td>Line</td><td>Code</td></tr>';
				foreach ($traceInfo as $k => $msg)
				{
					$k++;
					$str .='<tr class="bg1">';
					$str .='<td>' . $k . '</td>';
					$str .='<td>' . $msg['file'] . '</td>';
					$str .='<td>' . $msg['line'] . '</td>';
					$str .='<td>' . $msg['function'] . '</td>';
					$str .='</tr>';
				}
			}
			else
			{
				$str .='<tr><td><ul>' . $traceInfo . '</ul></td></tr>';
			}
			$str .='</tbody></table></div>';
		}
		$str .=<<<EOT
</div>
</body>
</html>
EOT;
		echo $str;
	}
}
?>