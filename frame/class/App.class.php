<?php
/**
 *  -------------------------------------------------
 *   @file		: App.class.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		:
 *   @update	:
 *  -------------------------------------------------
 */
class App
{//类定义开始
    protected static $theApp = NULL;
    protected $__controllers = array();


	/**
	 *	__construct，构造函数
	 *	@param String $appName 模块名称
	 *	@param Boolean $forcelogin 是否强制登录
	 *
	 */
	public function __construct($appName = "management",$forcelogin = true)
	{
		self::$theApp = $this;
		if(!defined("APP_NAME"))
		{
			define ('APP_NAME', $appName);
		}
	}

	/**
	 *	run，应用入口
	 *	完成调用控制器及对应方法，加载对应的model
	 *
	 */
    public function run()
	{
        $this->initAppSet();
		$request = Route::getRequestByUrl(null,false);
		$this->initAppDefine($request);
		//AsyncDelegate::dispatch('opslog', array('event' => 'tracking', 'mod' => $request->module, 'con' => $request->controller, 'act' => $request->action ));
		Route::dispatch();
    }

	/**
	 *	initAppSet 初始化参数
	 *
	 */
    private function initAppSet()
	{
        ini_set("session.cookie_domain", ROOT_DOMAIN);
        date_default_timezone_set('Asia/Shanghai');
        ini_set('memory_limit', '256M');
    }

	/**
	 *	initAppDefine 定义应用模块路径
	 */
    private function initAppDefine($request)
	{
		define('APP_DIR', KELA_ROOT.'/apps/'.$request->module);
		//define('__JS__', KELA_ROOT.'/apps/'.$request->module."/js");
		define('APP_SMARTY_TEMPLATES_DIR', APP_DIR.'/templates/');
		define('APP_SMARTY_COMPILE_DIR',APP_DIR.'/tmp/template_c/');
		define('APP_SMARTY_CACHE_DIR', APP_DIR.'/tmp/cache/');
		define("LOG_PATH", APP_DIR.'/logs/');
		define("APP_WIDGET", APP_DIR.'/widgets/');
    }

	/**
	 *	create_app_dir，初始化项目结构
	 *
	 */
	public static function create_app_dir ()
	{
		$dirs = array(
			APP_DIR.'/data/',//项目缓存数据目录
			//APP_DIR.'/js/',//项目js目录
			APP_DIR.'/logs/',//项目日志目录
			APP_DIR.'/control/',//项目控制器目录
			APP_DIR.'/model/',//项目模型目录
			APP_DIR.'/view/',//项目视图目录
			APP_DIR.'/templates/',//项目模板页面目录
			APP_DIR.'/tmp/',//创建项目缓存目录
			APP_DIR.'/tmp/cache/',
			APP_DIR.'/tmp/template_c/',//创建编译缓存目录
		);
		Util::xmkdir($dirs);
	}

	/**
	 *	check_tpl，检查公共模板
	 *
	 */
	public static function check_tpl ()
	{
		if(!is_file('apps/management/templates/main/index.html'))
		{
			die('没有管理页面');
		}
	}
}
?>