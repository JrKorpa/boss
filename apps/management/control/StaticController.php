<?php
/**
 *  -------------------------------------------------
 *   @file		: StaticController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		:
 *   @update	:
 *  -------------------------------------------------
 */
class StaticController extends Controller
{
	protected $smartyDebugEnabled = false;
	function index ($params)
	{
		$this->http404();
	}

	public function http404 ()
	{
		$msg = '您请求的页面不存在！';
		Log::record($msg.PHP_EOL.__FILE__,"FATAL");
		$this->errAction();
	}
}

?>