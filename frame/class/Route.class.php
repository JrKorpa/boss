<?php

/**
 *  -------------------------------------------------
 *   @file		: Route.class.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		:
 *   @update	:
 *  -------------------------------------------------
 */

/**
 * 	路由核心类
 *
 */
class Route {

        static protected $_request;
        static protected $__controllers = array();

        /**
         * 	dispatch，路由转发
         */
        static public function dispatch() {
                self::checkUrl();  //可能导致页面跳转

                DBSessionHandler::start();
                DBSessionHandler::gc(ini_get("session.gc_maxlifetime"));
                	
                if (isset($_GET['kela'])) {
                        $hasLogin = true;
                } else {
                        $hasLogin = Auth::checkLogin(); //会话
                }
                	
                $controller = strtolower(self::$_request->controller);
                if (!$hasLogin && $controller!= 'login' && $controller!= 'static') {
                        if (defined('SSO')) {
                                // 保证登录验证成功后，能回到之前想要访问的页面
                                if (self::$_request->module != 'management') {
                                	self::setAsAutoLaunch($_SERVER['REQUEST_URI'], _Request::getString("title", "默认标签"));
                                }
                                
                                $session_id = DBSessionHandler::getSessionId();
                                DBSessionHandler::destroy($session_id);
                                Auth::getInstance()->unsetLoginCookie();
                                
                                $login_url = Util::get_defined_array_var('SSO', 'login');
                                if (Util::isAjax()) {
                                	//Util::jsonExit(array('error' => '登录信息验证失败，无法处理您的请求', 'success' => 0, 'redir_url' => $login_url));
                                	echo '登录信息验证失败或网络间断，无法处理您的请求，请按F5刷新页面。';
                                	exit; //double check;
                                } 
                                header('Location:'.$login_url);
                                exit;
                        } else {
                                self::$_request->controller = 'Login';
                                self::$_request->action = 'index';
                                if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest') { 
                                        echo "kela";
                                        exit;
                                }
                                Util::jump("/index.php?mod=management&con=Login&act=index");
                                exit;
                        }
                }  
                
                App::create_app_dir();
                $controller = self::getController(self::$_request->controller, self::$_request->module);

                if ($controller == null) {//控制器不存在
                        if (APP_DEBUG) {
                                throw new FileException('控制器' . ucfirst(self::$_request->controller) . 'Control.php不存在!');
                        }
                        self::$_request->controller = 'static';
                        self::$_request->action = 'http404';
                        self::$_request->module = 'management';
                        $controller = self::getController(self::$_request->controller, self::$_request->module);
                        Request::getInstance(self::$_request);
                }
                if ($controller == null) {
                        die('请设置错误请求的处理界面');
                }               
                $controller->run();
        }

        /**
         * 	getRequestByUrl，根据 URL 生成 Request 对象 可以根据传递 mod或子域名获取要执行的功能模块
         *
         * 	@param String $url 地址
         * 	@param Boolean $auto404 是否自动跳转
         *
         * 	@return Request 对象
         *
         */
        static public function getRequestByUrl($url = null, $auto404 = true) {
                $host = Util::getDomain();
                /* if ($host && strstr($host, 'kela.cn') === false) {
                        return NULL;
                } */
                if (!$url) {
                        $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "";
                }

                if (!$url) {
                        return null;
                }

                $request = Request::getInstance(true);
                $request->userUrl = $url;

                //子域名
                //$dname = $_SERVER['HTTP_HOST'];  //代理服务器无法取得该值

                if (strpos($host, '.')) {
                        $request->subDomain = substr($host, 0, strpos($host, '.'));
                        if ($request->subDomain == "frameworkdemo") {
                                $request->subDomain = "management";
                        }
                }

                //按特定格式解析即可，不需要做rewrite
                // domain/?mod=module&con=controller&act=action
                //重构为根据传递mod优先,配置子域名其次
                $module = isset($_REQUEST['mod']) ? trim($_REQUEST['mod']) : '';
                if ($module === '') {
                        //$module = $request->subDomain;
                        $module = "management";
                }
                $request->module = $module == "frameworkdemo" ? "management" : $module;
                $request->controller = isset($_REQUEST['con']) ? trim($_REQUEST['con']) : ($request->module == "management" ? 'main' : '');

                $request->action = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'index';
                $request->params = $_REQUEST;
                if (empty($request->controller)) {
                        $request->controller = 'static';
                        $request->action = 'http404';
                        Log::record('请传递正确的控制器名称!' . PHP_EOL . __FILE__);
                }
                self::$_request = $request;
                return self::$_request;
        }

        /**
         * 	$urlCorrectionEnabled
         * 	开启url纠正，当请求url与标准url不一致时，将产生一个http 301跳转
         * 	可通过重载此变量为false关闭它
         *
         */
        static protected $urlCorrectionEnabled = true;

        /**
         * 	checkUrl，检查Url并跳转
         *
         *
         *
         */
        static public function checkUrl() {
                if (!self::$urlCorrectionEnabled || !isset(self::$_request))
                        return false;
                $standardUrl = $_SERVER['REQUEST_URI'];
                if (strpos($standardUrl, "?") > 0) {
                        $standardUrl = substr($standardUrl, 0, strpos($standardUrl, "?"));
                }
                if (!empty($_SERVER["QUERY_STRING"])) {
                        $standardUrl .= "?" . $_SERVER["QUERY_STRING"];
                }
                $dir = KELA_ROOT . '/apps/' . self::$_request->module;
                if (!is_dir($dir) && self::$_request->module != self::$_request->subDomain) {
                        $standardUrl = "http://" . self::buildSubDomain() . $standardUrl;
                }

                if ($standardUrl && $_SERVER["REQUEST_URI"] != $standardUrl && php_sapi_name() != 'cli') {
                    header("HTTP/1.1 301 Moved Permanently");
                        header("Location: " . $standardUrl);
                } else {
                        //业务常量
                        $data_configFile = APP_ROOT . self::$_request->module . '/configs/data_config.php';
                        if (is_file($data_configFile)) {
                                include_once($data_configFile);
                        }
                }
        }

        /**
         * 	buildSubDomain，创建子域名
         *
         */
        static public function buildSubDomain() {
                $host = Util::getDomain();
                $host = array_slice(explode(".", $host), 1);
                array_unshift($host, self::$_request->module);
                return implode(".", $host);
        }

        /**
         * 	getController，控制器工厂
         * 	@param $controllerName 控制器名称
         * 	@param $module 当前模块名称
         *
         */
        static public function getController($controllerName, $module = APP_NAME) {
                //controller命名规则 MainController,首字母大写的控制器名称，Controller
                //控制器命名规则 防止控制器重名，其它功能模块的控制器需加模块名称前缀 如StylemainController JXCmainController
                $controller = ucfirst($controllerName);
                if (!isset(self::$__controllers[$controller])) {
                        $controllerClass = $controller . 'Controller';
                        $class_file = KELA_ROOT . '/apps/' . $module . '/control/' . $controllerClass . ".php";


                        if (!file_exists($class_file)) {
                                if (APP_DEBUG) {
                                        throw new FileException($class_file . "不存在！");
                                }
                                return null;
                        }
                        if (APP_DEBUG) {
                                $content = file_get_contents($class_file);
                                preg_match('/class\s+(.+)Controller\s+extends/', $content, $matches);
                                if ($matches[1] != $controller) {
                                        throw new FileException('注意' . $class_file . "大小写！");
                                }
                        }
                        require_once($class_file);
                        self::$__controllers[$controller] = new $controllerClass;
                }
                return self::$__controllers[$controller];
        }

        /**
         * 	path404，获取错误页面路径
         *
         * 	@param $request Request对象
         *
         * 	返回：错误页面路径
         *
         */
        static public function path404($request = null) {
                if ($request == null) {
                        $request = self::$_request;
                }
                return "http://" . $request->module . ROOT_DOMAIN . ERROR_PAGE_404;
        }

        /**
         * 	errorRedirect404，404跳转
         *
         * 	返回：404跳转
         *
         */
        static public function errorRedirect404() {
                $url = self::path404();
                header('HTTP/1.1 404 Not Found');
                header('status: 404 Not Found');
                header('location:' . $url);
                exit;
        }
        
        static public function setAsAutoLaunch($url, $title) {
        	// tab_id一般都不会紧跟在?之后
        	//if (Util::isAjax()) return;
        	
        	$tab = time();
        	$idx = strrpos($url,'&tab_id=');
        	if ($idx !== false) {
        		list($tab_id_vals) = explode('&', substr($url, $idx + 1));
        		if (strrpos($tab_id_vals,'#') !== false) {
        			list($tab_id_vals) = explode('#',$tab_id_vals);
        			list(, $tab) = explode('=',$tab_id_vals);
        		}
        	} else {
        		if (strrpos($url,'#') !== false) {
        			list($url,$search) = explode('#',$url);
        			if (strlen($search)) $search = '#'.$search;
        		}
        		
        		$url .= strrpos($url,'?') === false ? '?tab_id=' : '&tab_id=';
        		$url .= $tab;
        	}
        	
        	setcookie("xlu", json_encode(
        		array('ln' => $url, 'ti' => $title, 'tb' => 'tab-'.$tab)
        	),0,'/',ROOT_DOMAIN);
        }

}

?>