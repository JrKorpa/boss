<?php
/**
 *  -------------------------------------------------
 *   @file		: Request.class.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		:
 *   @update	:
 *  -------------------------------------------------
 */
Class Request
{
    private static $instance;//实例
	public $userUrl = "";
    public $standardUrl = "";
    public $headerTitle = "";
    public $headerKeyword = "";
    public $headerDescription = "";
    public $static = "";
    public $urltoken = "";

    public $params = array();
    public $pattern = null;
    public $controller = null;
    public $action = null;
    //public $model = null;
    public $module = '';
	public $subDomain = '';

    public $pageType = 0;
    public $servlet = '';
    public $subPageType = 0;
    public $clientType = 0;

    protected function __construct()
	{

    }

	/**
	 *	getInstance，单例获取Request实例
	 *
	 */
    public static function getInstance($renew = false)
	{
        if (self::$instance == null || $renew)
		{
            self::$instance = new Request($expire = null);
        }
        return self::$instance;
    }

	/**
	 *	setInstance，设置Request实例
	 *
	 */
    public static function setInstance($requestObj)
	{
        self::$instance = $requestObj;
    }
}

?>