<?php

/*
 *  -------------------------------------------------
 *   @file		: Kela.class.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author		: Laipiyang <462166282@qq.com>
 *   @date		:
 *   @update		:
 *  -------------------------------------------------
 */
/*
 *  -------------------------------------------------
 *   框架核心引导类
 * 	类库自动加载，加载核心文件，初始化项目结构，Trace记录
 *  -------------------------------------------------
 */

class Kela {

        /**
         * 	handle_exception，异常处理
         *
         * 	@param mixed $exception 异常对象
         *
         */
        public static function handle_exception(Exception $e) {
                if ($e instanceof DBException) {//数据库连接异常
                        Err::trace($e);
                } else if ($e instanceof PDOException) {//数据库操作异常
                        Err::trace($e);
                } else if ($e instanceof FileException) {//文件异常
                        Err::trace($e);
                } else if ($e instanceof MethodException) {//方法未找到异常
                        Err::trace($e);
                } else if ($e instanceof ObjectException) {//对象属性异常
                        Err::trace($e);
                } else {
                        Err::trace($e);
                }
        }

        /**
         * 	handle_error，错误处理
         *
         * 	@param string $errNo 错误代码
         * 	@param string $errStr 错误信息
         * 	@param string $errFile 出错文件
         * 	@param string $errLine 出错行
         *
         */
        public static function handle_error($errNo, $errStr, $errFile, $errLine) {
                list($showTrace, $logTrace) = self::debugBacktrace();
                if (defined('LOG_RECORD') && LOG_RECORD) {
                        $messageSave = self::getErrByNo($errNo) . ':' . $errStr . PHP_EOL . 'PHP:' . $logTrace;
                        Log::Write($messageSave);
                }
                if (APP_DEBUG) {
                        Err::showError("<li>$errStr</li>", $showTrace);
                }
        }

        /**
         * 	getErrByNo
         *
         *  1 E_ERROR 致命错误。
         * 	2 E_WARNING 运行时警告 (非致命错误)。
         * 	4 E_PARSE 编译时语法解析错误。
         * 	8 E_NOTICE 运行时通知。
         * 	16 E_CORE_ERROR 在PHP初始化启动过程中发生的致命错误。
         * 	32 E_CORE_WARNING PHP初始化启动过程中发生的警告 (非致命错误) 。
         * 	64 E_COMPILE_ERROR 致命编译时错误。
         * 	128 E_COMPILE_WARNING 编译时警告 (非致命错误)。
         * 	256	E_USER_ERROR 用户产生的错误信息。类似 E_ERROR, 但是是由用户自己在代码中使用PHP函数 trigger_error()来产生的。
         * 	512	E_USER_WARNING 用户产生的警告信息。类似 E_WARNING, 但是是由用户自己在代码中使用PHP函数 trigger_error()来产生的。
         * 	1024 E_USER_NOTICE 用户产生的通知信息。类似 E_NOTICE, 但是是由用户自己在代码中使用PHP函数 trigger_error()来产生的。
         * 	2048 E_STRICT 启用 PHP 对代码的修改建议，以确保代码具有最佳的互操作性和向前兼容性。
         * 	4096 E_RECOVERABLE_ERROR 可被捕捉的致命错误。如果该错误没有被用户自定义句柄捕获 (参见 set_error_handler())，将成为一个 E_ERROR从而脚本会终止运行。
         * 	8192 E_DEPRECATED 运行时通知。启用后将会对在未来版本中可能无法正常工作的代码给出警告。
         * 	16384 E_USER_DEPRECATED 用户产生的警告信息。 类似 E_DEPRECATED, 但是是由用户自己在代码中使用PHP函数 trigger_error()来产生的。
         * 	30719 E_ALL E_STRICT出外的所有错误和警告信息。 30719 in PHP 5.3.x, 6143 in PHP 5.2.x, 2047 previously
         *
         *
         */
        public static function getErrByNo($type) {
                switch ($type) {
                        case 1:
                                $err = '致命错误';
                                break;
                        case 2:
                                $err = '警告';
                                break;
                        case 4:
                                $err = '语法错误';
                                break;
                        case 8:
                                $err = '运行出错';
                                break;
                        case 16:
                                $err = '初始化时发生致命错误';
                                break;
                        case 32:
                                $err = '初始化时警告';
                                break;
                        case 64:
                                $err = '编译错误';
                                break;
                        case 128:
                                $err = '编译警告';
                                break;
                        case 256:
                                $err = '自定义错误';
                                break;
                        case 512:
                                $err = '自定义警告';
                                break;
                        case 1024:
                                $err = '自定义出错';
                                break;
                        case 2048:
                                $err = '兼容警告';
                                break;
                        default:
                                $err = '错误';
                }
                return $err;
        }

        /**
         *
         * 	dealError，捕获致命错误等
         */
        public static function dealError() {
                $e = error_get_last();
                if (is_array($e)) {
                        $err = self::getErrByNo($e['type']);
                        $msg = $err . ":" . $e['message'] . "\r\n" . $e['file'] . "\r\n" . $e['line'];
                        Log::write($msg);
                }
        }

        /**
         * 代码执行过程回溯信息
         *
         */
        public static function debugBacktrace() {
                $skipFunc[] = '';
                $show = $log = '';
                $debugBacktrace = debug_backtrace();
                //var_dump ($debugBacktrace);
                rsort($debugBacktrace);
                foreach ($debugBacktrace as $k => $error) {
                        if (!isset($error['file'])) {
                                // 利用反射API来获取方法/函数所在的文件和行数
                                try {
                                        if (isset($error['class'])) {
                                                $reflection = new ReflectionMethod($error['class'], $error['function']);
                                        } else {
                                                $reflection = new ReflectionFunction($error['function']);
                                        }
                                        $error['file'] = $reflection->getFileName();
                                        $error['line'] = $reflection->getStartLine();
                                        $args = $error['args'];
                                        if ($args) {
                                                if ($error['function'] == "handle_error" && $error['class'] == "Kela") {
                                                        break;
                                                } else {
                                                        $error['info'] = print_r($args[count($args) - 1], true);
                                                }
                                        }
                                } catch (Exception $e) {
                                        continue;
                                }
                        }
                        $file = $error['file'];
                        $func = isset($error['class']) ? $error['class'] : '';
                        $func .= isset($error['type']) ? $error['type'] : '';
                        $func .= isset($error['function']) ? $error['function'] : '';
                        $arg = isset($error['info']) ? $error['info'] : '';
                        if (in_array($func, $skipFunc)) {
                                break;
                        }
                        $error['line'] = sprintf('%04d', $error['line']);

                        $show .= '<li>[Line: ' . $error['line'] . ']' . $file . '(' . $func . ')</li>';
                        $show .= 'args:' . $arg;
                        $log .=!empty($log) ? PHP_EOL : '';
                        $log .= $file . ':' . $error['line'];
                }
                return array($show, $log);
        }

        //方法不存在的处理
        public function __call($name, $args) {
                if (APP_DEBUG) {
                        echo '您使用的方法' . $name . '不存在<br />';
                        var_export($args);
                } else {
                        exit('Forbidden');
                }
        }

        //输出处理
        public function __toString() {
                return APP_DEBUG ? '核心类不能直接输出' : '操作非法';
        }

        // 根据数组生成常量定义
        private static function array_define($array) {
                $content = '';
                foreach ($array as $key => $val) {
                        $key = strtoupper($key);
                        $content .= 'if(!defined(\'' . $key . '\')) ';
                        if (is_int($val) || is_float($val)) {
                                $content .= "define('" . $key . "'," . $val . ");";
                        } elseif (is_bool($val)) {
                                $val = ($val) ? 'true' : 'false';
                                $content .= "define('" . $key . "'," . $val . ");";
                        } elseif (is_string($val)) {
                                $content .= "define('" . $key . "','" . addslashes($val) . "');";
                        }
                }
                return $content;
        }

        // 编译文件
        private static function compile($file) {
                $content = file_get_contents($file);
                $content = substr(trim($content), 5);
                if ('?>' == substr($content, -2))
                        $content = substr($content, 0, -2);
                return $content;
        }

        /*
         * 	去空格，去除注释包括单行及多行注释
         */

        private static function strip_whitespace($content) {
                $str = ""; //合并后的字符串
                $data = token_get_all($content);
                $end = false; //没结束如$v = "hdphp"中的等号;
                for ($i = 0, $count = count($data); $i < $count; $i++) {
                        if (is_string($data[$i])) {
                                $end = false;
                                $str .= $data[$i];
                        } else {
                                switch ($data[$i][0]) { //检测类型
                                        //忽略单行多行注释
                                        case T_COMMENT:
                                        case T_DOC_COMMENT:
                                                break;
                                        //去除格
                                        case T_WHITESPACE:
                                                if (!$end) {
                                                        $end = true;
                                                        $str .= " ";
                                                }
                                                break;
                                        //定界符开始
                                        case T_START_HEREDOC:
                                                $str .= "<<<KELAPHP\n";
                                                break;
                                        //定界符结束
                                        case T_END_HEREDOC:
                                                $str .= "KELAPHP;\n";
                                                //类似str;分号前换行情况
                                                for ($m = $i + 1; $m < $count; $m++) {
                                                        if (is_string($data[$m]) && $data[$m] == ';') {
                                                                $i = $m;
                                                                break;
                                                        }
                                                        if ($data[$m] == T_CLOSE_TAG) {
                                                                break;
                                                        }
                                                }
                                                break;

                                        default:
                                                $end = false;
                                                $str .= $data[$i][1];
                                }
                        }
                }
                return $str;
        }

        public static function start() {
                // 注册AUTOLOAD方法
                spl_autoload_register('Kela::autoload');
                //捕获Fatal Error等错误
                register_shutdown_function('Kela::dealError');
                // 自定义错误函数 E_ERROR, E_PARSE 或者 E_CORE_ERROR
                set_error_handler('Kela::handle_error');
                // 异常捕获
                set_exception_handler('Kela::handle_exception');
                // 屏蔽Smarty的错误
                Smarty::muteExpectedErrors();

                $runtimefile = KELA_PATH . '/~runtime.php';
                if (!APP_DEBUG && is_file($runtimefile)) {
                        require $runtimefile;
                } else {
                        //常量编译
                        $_define = get_defined_constants(true);
                        $compile = self::array_define($_define['user']);

                        //编译核心文件
                        $files = scandir(KELA_CLASS);
                        array_unshift($files, 'Err.class.php', 'Log.class.php', 'Model.class.php', 'View.class.php');
                        $files = array_unique($files);
                        
                        foreach ($files as $key => $val) {
                                if ($val == '.' || $val == '..' || $val == '.svn' || $val == '.DS_Store' || $val == '.git') {
                                        unset($files[$key]);
                                } else {
                                        if ($val != 'Kela.class.php') {
                                            if ($val == 'MemcachedSessionHandler.class.php' && (!defined('MEMCACHE_SERVER') || empty(MEMCACHE_SERVER))) {
                                                continue;
                                            }
                                            if ($val == 'DBSessionHandler.class.php' && defined('MEMCACHE_SERVER') && !empty(MEMCACHE_SERVER)) {
                                                continue;
                                            }
                                            
                                            include KELA_CLASS . $val;
                                            if (!APP_DEBUG)
                                                    $compile .= self::compile(KELA_CLASS . $val);
                                        }
                                }
                        }
                        if (!APP_DEBUG) {
                                file_put_contents($runtimefile, self::strip_whitespace('<?php ' . $compile));
                        }
                }
        }

        /**
         * 	autoload，类库自动加载
         * 	@param String $class 对象类名
         * 	@return void
         *
         */
        public static function autoload($class) {
                //print_r($class.PHP_EOL);
                $namespaces = preg_split("/\s*\\\s*/",$class,-1,PREG_SPLIT_NO_EMPTY);
                
                if (count($namespaces) == 2) {
                    static::resolve_class($namespaces[1], KELA_ROOT.'/apps/'.$namespaces[0]);
                } else {
                    if (defined('APP_DIR')) {
                        $dir = APP_DIR;
                    } else if (isset($_GET['mod'])) {
                        $dir = $_GET['mod'];     //normal
                    } else if (isset($_REQUEST['con'])) {
                        $dir = $_REQUEST['con']; //api
                    } else {
                        $dir = "management";
                    }
                    static::resolve_class($namespaces[0], $dir);
                }                
        }
        
        private static function resolve_class($class, $module_dir) {
            
            if (ucwords(strtolower(substr($class, -10))) == 'Controller' && $class != 'Controller' && is_file($module_dir . '/control/' . $class . '.php')) {//子控制器
                require_once $module_dir . '/control/' . $class . '.php';
                return;
            }

            if ($class == 'Smarty') {
                include_once(KELA_PATH . '/smarty/Smarty.class.php');
                return;
            }
            
            $class_name_length = strlen($class);
            $path = '';
            if (ucwords(strtolower(substr($class, -5))) == 'Model' && $class_name_length > 5) {
                if (is_file($module_dir . '/model/' . $class . '.php')) {//子模型
                    $path = $module_dir . '/model/' . $class . '.php';
                } else if (is_file(KELA_ROOT . '/apps/management/model/' . $class . '.php')) {
                    $path = KELA_ROOT . '/apps/management/model/' . $class . '.php';
                }
            }
            
            if (!$path) {
                if (ucwords(strtolower(substr($class, -4))) == 'View' && $class_name_length > 4) {
                    if (is_file($module_dir . '/view/' . $class . '.php')) {//子视图
                        $path = $module_dir . '/view/' . $class . '.php';
                    } else if (is_file(KELA_ROOT . '/apps/management/view/' . $class . '.php')) {
                        $path = KELA_ROOT . '/apps/management/view/' . $class . '.php';
                    }
                }
            }
            
            if (!$path) {
                $path = KELA_CLASS . $class.'.class.php';
            }
            
            if (is_file($path)) {
                require_once $path;
            }
        }

}

?>