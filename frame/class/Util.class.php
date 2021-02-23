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
class Util {

        const POINT_MALL_API_HOST = 'http://api.kela.cn';

        /**
         * 	xmkdir，批量创建目录
         *
         * 	@param Array $dirs 文件夹路径定义
         * 	@mode Integer $mode 目录权限
         *
         * 	@return void
         *
         */
        public static function xmkdir($dirs, $mode = 0755) {
                foreach ($dirs as $dir) {
                        if (!is_dir($dir))
                                mkdir($dir, $mode);
                }
        }

        /**
         * 	rmkdir，递归创建目录
         *
         * 	@param String $path 路径   a/b/c
         * 	@mode Integer $mode 目录权限
         *
         * 	@return void
         *
         */
        public static function rmkdir($path, $mode = 0755) {
                $path_arr = explode('/', $path);
                $dir_name = '';
                foreach ($path_arr as $val) {
                        if ($val != '.') {
                                $dir_name .= $val . '/';
                                if (!is_dir($dir_name))
                                        mkdir($dir_name, $mode);
                        }
                }
        }

        /**
         * 	rrmdir，递归删除文件及文件夹
         *
         * 	@param String $dir 路径
         *
         * 	@return void
         *
         */
        //递归删除文件及文件夹
        public static function rrmdir($dir, $flag = true) {
                if (!is_dir($dir))
                        return false;
                if (!isset($a)) {
                        $a = $dir; //保留最外层目录
                }
                $od = opendir($dir);
                while ($rd = readdir($od)) {
                        if ($rd <> '.' && $rd <> '..' && $rd <> '.svn') {
                                if (is_dir($dir . '/' . $rd)) {
                                        self::rrmdir($dir . '/' . $rd);
                                        rmdir($dir . '/' . $rd);
                                } else {
                                        unlink($dir . '/' . $rd);
                                }
                        }
                }
                closedir($od);
                if (!$flag || $dir <> $a) {
                        rmdir($dir);
                }
        }

        /**
         * 	statControlNo，统计每个项目控制器数量形成数组
         *
         * 	@return Array
         *
         */
        public static function statControlNo() {
                $dir = KELA_ROOT . '/apps/';
                $mods = scandir($dir);
                $data = array();
                foreach ($mods as $key => $val) {
                        if ($val == '.' || $val == '..' || $val == '.svn') {
                                continue;
                        }
                        if (!is_dir($dir . $val . '/control')) {
                                continue;
                        }
                        $tmp = scandir($dir . $val . '/control');
                        foreach ($tmp as $k => $v) {
                                if ($v == '.' || $v == '..' || $v == '.svn') {
                                        unset($tmp[$k]);
                                        continue;
                                }
                        }
                        $data[] = array('label' => $val, 'data' => count($tmp));
                }
                return $data;
        }

        /*
         * 	统计每个项目控制器文件大小形成数组
         */

        public static function statControlSize() {
                $dir = KELA_ROOT . '/apps/';
                $mods = scandir($dir);
                $data = array();
                foreach ($mods as $key => $val) {
                        if ($val == '.' || $val == '..' || $val == '.svn') {
                                continue;
                        }
                        if (!is_dir($dir . $val . '/control')) {
                                continue;
                        }
                        $tmp = scandir($dir . $val . '/control');
                        $dir_size = 0;
                        foreach ($tmp as $k => $v) {
                                if ($v == '.' || $v == '..' || $v == '.svn') {
                                        continue;
                                }
                                $dir_size += filesize($dir . $val . '/control/' . $v);
                        }
                        $data[] = array('label' => $val, 'data' => $dir_size);
                }
                return $data;
        }

        /*
         * 	获取apache访问日志
         */

        public static function getApacheLog($file) {
                $b = file($file);
                $data = array();
                foreach ($b as $key => $val) {
                        $c = explode(' ', $val);
                        if (strpos($c[6], '/index.php?') !== false) {
                                $data[] = array('ip' => $c[0], 'file' => $c[6], 'time' => substr($c[3], 1));
                        }
                }
                return $data;
        }

        /*
         * 	分析apache日志
         */

        public static function statApacheLog($file) {
                $data = self::getApacheLog($file);
                $datas = array();
                foreach ($data as $val) {
                        $a = parse_url($val['file']);
                        parse_str($a['query'], $res);
                        if (!isset($datas[$res['mod'] . ':' . $res['con'] . ':' . $res['act']])) {

                                $datas[$res['mod'] . ':' . $res['con'] . ':' . $res['act']] = 1;
                        } else {
                                $datas[$res['mod'] . ':' . $res['con'] . ':' . $res['act']] ++;
                        }
                }
                return $datas;
        }

        /**
         * 	encrypt，加密函数
         *
         * 	@param String $str 明文
         * 	@param String $key 密钥
         *
         * 	@return String  密文
         *
         */
        public static function encrypt($str, $key = AUTH_KEY) {
                $coded = '';
                $keylength = strlen($key);
                $count = strlen($str);
                for ($i = 0; $i < $count; $i += $keylength) {
                        $coded .= substr($str, $i, $keylength) ^ $key;
                }
                return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($coded));
        }


        /**
         * 	decrypt，解密函数
         *
         * 	@param String $str 密文
         * 	@param String $key 密钥
         *
         * 	@return  strinSg  明文
         *
         */
        public static function decrypt($str, $key = AUTH_KEY) {
                $coded = '';
                $keylength = strlen($key);
                $str = base64_decode(str_replace(array('-', '_'), array('+', '/'), $str));
                $count = strlen($str);
                for ($i = 0; $i < $count; $i += $keylength) {
                        $coded .= substr($str, $i, $keylength) ^ $key;
                }
                return $coded;
        }


        /**
         * 	random，取随机字符串
         *
         * 	@param int $length 生成的字符串长度
         * 	@param bool $numeric 如果为真，则生成纯数字字符串
         *
         * 	@return String
         *
         */
        public static function random($length, $numeric = 0) {
                if ($numeric) {
                        $hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
                } else {
                        $hash = '';
                        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
                        $max = strlen($chars) - 1;
                        for ($i = 0; $i < $length; $i++) {
                                $hash .= $chars[mt_rand(0, $max)];
                        }
                }
                return $hash;
        }

        /**
         * 	getClicentIp，取客户端公网IP地址
         *
         * 	@return String
         *
         */
        public static function getClicentIp() {
                if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && preg_match_all('/(\d{1,3}\.){3}\d{1,3}/s', $_SERVER['HTTP_X_FORWARDED_FOR'], $mat)) {
                        foreach ($mat[0] as $ip) {
                                if (!preg_match('/^(?:10|172\.16|192\.168)\./', $ip)) {
                                        return $ip;
                                }
                        }
                        return $ip;
                } elseif (isset($_SERVER["HTTP_FROM"]) && preg_match('/(?:\d{1,3}\.){3}\d{1,3}/', $_SERVER["HTTP_FROM"])) {
                        return @$_SERVER["HTTP_FROM"];
                } else {
                        return @$_SERVER['REMOTE_ADDR'];
                }
        }

        /**
         * 	getBrowser，取客户端浏览器类型
         *
         * 	@return String
         *
         */
        public static function getBrowser() {
                if (!empty($_SERVER['HTTP_USER_AGENT'])) {
                        $br = $_SERVER['HTTP_USER_AGENT'];
                        if (preg_match('/MSIE/i', $br)) {
                                $br = 'MSIE';
                        } elseif (preg_match('/Firefox/i', $br)) {
                                $br = 'Firefox';
                        } elseif (preg_match('/Chrome/i', $br)) {
                                $br = 'Chrome';
                        } elseif (preg_match('/Safari/i', $br)) {
                                $br = 'Safari';
                        } elseif (preg_match('/Opera/i', $br)) {
                                $br = 'Opera';
                        } else {
                                $br = 'Other';
                        }
                        return $br;
                } else {
                        return "unkown!";
                }
        }

        /**
         * 	toSafeTags，转换不安全的代码
         *
         * 	@param String $str
         *
         * 	@return String
         *
         */
        public static function toSafeTags($str) {
                $farr = array(//"/\s+/",
                        '/<(\/?)(script|i?frame|style|html|body|title|link|meta|object|embed|\?|\%)([^>]*?)>/isU","/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU', "/javascript:/", "/jnc:/");

                $tarr = array(//" ",
                        "＜\\1\\2\\3＞", "\\1\\2", "", "");

                $str = preg_replace($farr, $tarr, $str);
                return $str;
        }

        /**
         * 	httpHeaderNoCache，设置页面不缓存
         *
         */
        public static function httpHeaderNoCache() {
                header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
                header("Cache-Control: post-check=0, pre-check=0", false);
                header("Pragma: no-cache");
        }

        /**
         * 	caseSds2Dbs，全角的数字字符、字母、空格或'%+-()'字符，由全角到半角
         *
         * 	@param String $string
         *
         * 	@return String
         *
         */
        public static function caseSds2Dbs($string) {
                if (empty($string))
                        return $string;
                $array = array('１' => 1, '２' => 2, '３' => 3, '４' => 4, '５' => 5, '６' => 6, '７' => 7, '８' => 8, '９' => 9, '０' => 0, 'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E', 'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J', 'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O', 'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T', 'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y', 'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd', 'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i', 'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n', 'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's', 'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x', 'ｙ' => 'y', 'ｚ' => 'z', '（' => '(', '）' => ')', '［' => '[', '］' => ']', '【' => '[', '】' => ']', '〖' => '[', '〗' => ']', '「' => '[', '」' => ']', '『' => '[', '』' => ']', '｛' => '{', '｝' => '}', '《' => '<', '》' => '>', '％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-', '：' => ':', '。' => '.', '、' => ',', '，' => '.', '、' => '.', '；' => ',', '？' => '?', '！' => '!', '…' => '-', '‖' => '|', '＂' => '"', '＇' => '`', '｀' => '`', '｜' => '|', '〃' => '"', '　' => ' ');
                $string = str_replace(array_keys($array), $array, $string);
                return $string;
        }

        /**
         * 	isAjax，判断是否为ajax请求
         *
         * 	@return Boolean
         *
         */
        public static function isAjax() {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        return true;
                } else {
                        return false;
                }
        }

        /**
         * 	http，获得当前环境的 HTTP 协议方式
         *
         * 	@return String
         *
         */
        public static function http() {
                return (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
        }

        /**
         * 	getDomain，取得当前的域名
         *
         * 	@return String
         *
         */
        public static function getDomain() {
                // 协议
                $protocol = self::http();
                // 域名或IP地址
                if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
                        $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
                } elseif (isset($_SERVER['HTTP_HOST'])) {
                        $host = $_SERVER['HTTP_HOST'];
                } else {
                        // 端口
                        if (isset($_SERVER['SERVER_PORT'])) {
                                $port = ':' . $_SERVER['SERVER_PORT'];
                                if ((':80' == $port && 'http://' == $protocol) || (':443' == $port && 'https://' == $protocol)) {
                                        $port = '';
                                }
                        } else {
                                $port = '';
                        }

                        if (isset($_SERVER['SERVER_NAME'])) {
                                $host = $_SERVER['SERVER_NAME'] . $port;
                        } elseif (isset($_SERVER['SERVER_ADDR'])) {
                                $host = $_SERVER['SERVER_ADDR'] . $port;
                        }
                }
                return $protocol . $host;
        }

        /**
         * 	isPost，判断是否为post提交
         *
         * 	@return Boolean
         *
         */
        public static function isPost() {
                return strtolower($_SERVER['REQUEST_METHOD']) == 'post';
        }

        /**
         * 	arrayToObject，将数组转化为对象
         *
         * 	@param Array $arr
         *
         * 	@return Object
         *
         */
        public static function arrayToObject($arr) {
                if (gettype($arr) != 'array')
                        return;
                foreach ($arr as $k => $v) {
                        if (gettype($v) == 'array' || getType($v) == 'object') {
                                $arr[$k] = (object) self::arrayToObject($v);
                        }
                }
                return (object) $arr;
        }

        /**
         * 	arrayToObject，将对象转化为数组
         *
         * 	@param Object $obj
         *
         * 	@return Array
         *
         */
        public static function objectToArray($obj) {
                $obj = (array) $obj;
                foreach ($obj as $k => $v) {
                        if (gettype($v) == 'resource')
                                return;
                        if (gettype($v) == 'object' || gettype($v) == 'array') {
                                $obj[$k] = (array) self::objectToArray($v);
                        }
                }
                return $obj;
        }

        /**
         * 	truncate_cn，中文截取
         *
         * 	@param String $string 字符串
         * 	@param Integer $length 截取长度
         * 	@param String $etc 截取后的后缀
         * 	@param String $code 编码
         *
         * 	@return String
         *
         */
        public static function truncate_cn($string, $length = 80, $etc = '...', $code = 'UTF-8') {
                if ($length == 0)
                        return '';
                if ($code == 'UTF-8') {
                        $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
                } else {
                        $pa = "/[\x01-\x7f]|[\xa1-\xff][\xa1-\xff]/";
                }
                preg_match_all($pa, $string, $t_string);
                if (count($t_string[0]) > $length)
                        return join('', array_slice($t_string[0], 0, $length)) . $etc;
                return join('', array_slice($t_string[0], 0, $length)) . $etc;
        }

        /**
         * 	parseStr，解析表名或字段名 goodsType  => goods_type    OffileTel =>office_tel
         *
         * 	@param String $name 字符串
         *
         * 	@return String
         *
         */
        public static function parseStr($name) {
                if (strpos($name, '_') === false) {
                        $name = preg_replace("/[A-Z]/", "_\$0", $name);
                }
                return strtolower(trim($name, "_"));
        }

        /**
         * 	parseStr1，解析表名或字段名  => GoodsType
         *
         * 	@param String $name 字符串
         *
         * 	@return String
         *
         */
        public static function parseStr1($name) {
                if (strpos($name, '_') === false) {
                        return ucfirst($name);
                } else {
                        return self::parseStr2($name);
                }
        }

        /**
         * 	parseStr2，解析表名或字段名 goods_type  => GoodsType
         *
         * 	@param String $name 字符串
         *
         * 	@return String
         *
         */
        public static function parseStr2($name) {
                $arr = explode("_", strtolower($name));
                foreach ($arr as $key => $val) {
                        $arr[$key] = ucfirst($val);
                }
                return implode("", $arr);
        }

        /**
         * 	parseStr3，解析表名成文件夹名  GoodsType=> goodstype,goods_type =>goodstype
         *
         * 	@param String $name 字符串
         *
         * 	@return String
         *
         */
        public static function parseStr3($name) {
                return str_replace("_", "", strtolower($name));
        }

        /**
         * 	V，自动生成subView文件
         *
         * 	@param String $tableName 数据表名
         *
         * 	@return void
         *
         */
        public static function V($tableName = '', $conn = 1) {
                if (!$tableName)
                        die('请输入表名');
                $fields = DB::cn($conn)->getFields(self::parseStr($tableName));
                $protypeStr = '';
                $methodStr = '';
                foreach ($fields as $val) {
                        $protypeStr .="\t" . "protected \$_" . $val['Field'] . ";" . PHP_EOL;
                        $methodStr .="\t" . "public function get_" . $val['Field'] . "(){return \$this->_" . $val['Field'] . ";}" . PHP_EOL;
                }
                $file = APP_DIR . '/view/' . self::parseStr2($tableName) . 'View.php';
                if (!is_file($file)) {
                        $fileTpl = KELA_PATH . '/template/View.tpl.php';
                        $content = file_get_contents($fileTpl);
                        $content = str_replace(array('{DATE}', '{VIEW}', '{FIELDS}', '{FUNCTIONS}'), array(date('Y-m-d H:i:s'), self::parseStr2($tableName), $protypeStr, $methodStr), $content);
                        file_put_contents($file, $content);
                }
        }

        /**
         * 	M，自动生成subModel文件
         *
         * 	@param String $tableName 数据表名
         *
         * 	@return void
         *
         */
        public static function M($tableName = '', $dbname = 'management', $conn = 1) {
                if (!$tableName)
                        die('请输入表名');
                if (!$dbname)
                        die('请输入库名');
                $sql = "SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '" . self::parseStr($tableName) . "' AND TABLE_SCHEMA='{$dbname}'";
                $data = DB::cn($conn)->getAll($sql);
                $str = 'array(';
                foreach ($data as $val) {
                        $str .='"' . $val["COLUMN_NAME"] . '"=>' . ($val["COLUMN_COMMENT"] ? '"' . $val["COLUMN_COMMENT"] . '"' : '" "') . "," . PHP_EOL;
                }
                $str = rtrim($str, ",\r\n");
                $str .=")";
                $file = APP_DIR . '/model/' . self::parseStr2($tableName) . 'Model.php';
                if (!is_file($file)) {
                        $fileTpl = KELA_PATH . '/template/Model.tpl.php';
                        $content = file_get_contents($fileTpl);
                        $content = str_replace(array('{DATE}', '{MODEL}', '{DATA}', '{TABLE}'), array(date('Y-m-d H:i:s'), self::parseStr2($tableName), $str, self::parseStr($tableName)), $content);
                        file_put_contents($file, $content);
                }
        }

        /**
         * 	C，自动生成subController文件
         *
         * 	@param String $fileName 文件名
         *
         * 	@return void
         *
         */
        public static function C($fileName, $dir = 'management') {
                if (!$fileName)
                        die('请输入文件名');
                $file = 'apps/' . $dir . '/control/' . self::parseStr2($fileName) . 'Controller.php';
                if (!is_file($file)) {
                        $fileTpl = 'frame/template/Controller.tpl.php';
                        $content = file_get_contents($fileTpl);
                        $content = str_replace(array('{TABLE}', '{DATE}', '{CONTROLLER}', '{TMPL_PREFIX}'), array(self::parseStr($fileName), date('Y-m-d H:i:s'), self::parseStr2($fileName), self::parseStr($fileName)), $content);
                        file_put_contents($file, $content);
                }
        }

        /**
         * 	L，生成调试文件
         *
         * 	@param String $info 调试信息
         *
         * 	@return void
         *
         */
        public static function L($info, $dir = 'frame/trace.txt') {
                file_put_contents($dir, print_r($info, true) . PHP_EOL, FILE_APPEND);
        }

        /**
         * 	trace，浏览器友好的变量输出
         *
         * 	@param mixed $var 变量
         * 	@param Boolean $echo 输出还是返回
         * 	@param Boolean $strict 是否严格检查
         *
         * 	@return mixed
         *
         */
        public static function trace($var, $echo = true, $strict = true) {
                if (!$strict) {
                        if (ini_get('html_errors')) {
                                $output = print_r($var, true);
                                $output = "<pre>" . htmlspecialchars($output, ENT_QUOTES) . "</pre>";
                        } else {
                                $output = print_r($var, true);
                        }
                } else {
                        ob_start();
                        var_dump($var);
                        $output = ob_get_clean();
                        if (!extension_loaded('xdebug')) {
                                $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
                                $output = '<pre>' . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
                        }
                }
                if ($echo) {
                        echo($output);
                        return null;
                } else {
                        return $output;
                }
        }

        /**
         * 	addslashes_deep，递归转义字符串
         *
         * 	@param String $value 字符串
         *
         * 	@return Array|String
         */
        public static function addslashes_deep($value) {
                return is_array($value) ? array_map('addslashes_deep', $value) : addslashes($value);
        }

        /**
         * 	stripslashes_deep，递归去除转义
         *
         * 	@param String $value 字符串
         *
         * 	@return Array|String
         */
        public static function stripslashes_deep($value) {
                return is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
        }

        /**
         * 	alert，弹框提示函数
         *
         * 	@param String $info 提示信息
         * 	@param Boolean $exit 是否终止
         *
         */
        public static function alert($info, $exit = false) {
                $str = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><script type=\"text/javascript\">alert('" . $info . "');</script>";
                if ($exit) {
                        exit($str);
                } else {
                        echo $str;
                }
        }

        public static function alertUrl($info, $url = '') {
                $str = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><script type=\"text/javascript\">alert('" . $info . "');";
                if (empty($url)) {
                        $str .="javascript:window.history.go(-1);";
                } else {
                        $str .="javascript:window.location.href='" . $url . "'";
                }
                $str .=";</script>";
                echo $str;
                exit;
        }

        /**
         * 	Notice，页面提示跳转函数
         *
         * 	@param String $info 提示信息内容
         * 	@param String $url  跳转地址
         * 	@param String $title 窗口标题
         * 	@param Int $time 跳转时间
         *
         *
         */
        public static function Notice($info, $url = '', $title = '提示信息', $time = 1) {
                if ($url == '')
                        $url = 'javascript:window.history.go(-1);';
                $msg = array('title' => $title, 'info' => $info, 'url' => $url, 'time' => $time);
                include KELA_PATH . '/template/msg.php';
                exit;
        }

        /**
         * 输出json编码的数据并退出程序
         * @param mixed $data
         */
        public static function jsonExit($data) {
                header("Content-type: application/json");
                exit(json_encode($data));
        }
        /**
         * 事物批量回滚，输出json编码的数据并退出程序
         * gaopeng
         */
        public static function rollbackExit($msg,array $pdolist = array()){
            $result = array('success'=>0,'error'=>'');
            if(!empty($pdolist)){
                foreach ($pdolist as $key=>$pdo){
                    if(!is_object($pdo)){
                        $msg .= "<br/>无效PDO数据库链接【{$key}】";
                        continue;
                    }
                    $pdo->rollback();
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
                }
            }
            $result['error'] = $msg;
            self::jsonExit($result);
        }

        /**
         * 组合url连接
         * @param array $arr
         * @return url字符串
         */
        public static function get_pager_url($arr = array()) {
                if (empty($arr))
                        return false;
                $param_url = 'index.php?';
                foreach ($arr AS $key => $value) {
                        $param_url .= $key . '=' . addslashes($value) . '&';
                }
                //$param_url = rtrim($param_url,"&");
                return $param_url;
        }

        /**
         * 	data_uri，创建数据uri以节省HTTP请求
         *
         * 	@param String $fileName 文件名（含路径）
         * 	@param String $mime 方式类型  http://baike.baidu.com/view/160611.htm
         *
         * 	@return 数据URI
         *
         */
        public static function data_uri($fileName, $mime) {
                if (!is_file($fileName))
                        return null;
                return "data:" . $mime . ";base64," . base64_encode(file_get_contents($fileName));
        }

        //$img = Util::data_uri('a.jpg','image/jpeg');//.jpeg,.jpg image/jpeg
        //echo '<img src="'.$img.'" />';

        /**
         * 检查用户名是否符合规定
         *
         * @param STRING $username 要检查的用户名
         * @return 	TRUE or FALSE
         */
        public static function is_username($username) {
                $strlen = strlen($username);
                if (empty($username) || preg_match("/[^a-z\d\x{4e00}-\x{9fa5}_]/ui", $username) == 1) {
                        return false;
                } elseif (20 < $strlen || $strlen < 2) {
                        return false;
                }
                return true;
        }

        /*
         * 	检查密码是否符合规定
         *
         * @param STRING $password 要检查的用户密码
         * @return 	TRUE or FALSE
         */

        public static function is_password($pass) {
                $strlen = strlen($pass);
                if (empty($pass) || preg_match("/[^a-z\d\x{4e00}-\x{9fa5}\_!@\.]/ui", $pass) == 1) {
                        return false;
                } elseif (60 < $strlen || $strlen < 2) {
                        return false;
                }
                return true;
        }

        /**
         * 	isLegal，检查是否合法字符串(字母、汉字、数字)
         * 	合法则返回 true 否则返回 false
         *
         * 	@param String $str
         *
         * 	@return Boolean
         *
         */
        public static function isLegal($str) {
                return (bool) preg_match('/^[\x{4E00}-\x{9FA5}a-z0-9]+$/iu', $str);
        }

        /* 验证时间格式 */

        public static function checkDateTime($dateTime) {
                if (preg_match("/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/s", $dateTime)) {
                        return true;
                } else {
                        return false;
                }
        }

        /**
         * 	isEnglish，是否纯字母
         *
         */
        public static function isEnglish($str) {
                return !(bool) preg_match('/[^a-z]/i', $str);
        }

        /**
         * 	isChinese，是否纯汉字
         *
         */
        public static function isChinese($str) {
                return !(bool) preg_match('/[^\x{4e00}-\x{9fa5}]/u', $str);
        }

        /*
         * isChineseE,是否是英文的组合
         * */

        public static function isChineseE($str) {
                return !(bool) preg_match('/[^\x{4e00}-\x{9fa5}a-z]/iu', $str);
        }

        /**
         * 	isFields，只能输入字母、数字、逗号和下划线
         *
         */
        public static function isFields($str) {
                return (bool) preg_match('/^([a-z0-9,_]+)$/i', $str);
        }

        /**
         * 	isField，只能输入字母、数字和下划线
         *
         */
        public static function isField($str) {
                return (bool) preg_match('/^([a-z0-9_]+)$/i', $str);
        }

        public static function isNum($str) {
                return (bool) preg_match('/^[\d\.]+$/i', $str);
        }

        /**
         * 	isEmail，检查email地址合法性
         * 	合法则返回 true 否则返回 false
         *
         * 	@param String $email
         *
         * 	@return Boolean
         *
         */
        public static function isEmail($email) {
                return (bool) preg_match('/^.+@([_a-z0-9]+\.)+[a-z0-9]{2,4}$/', $email);
        }

        /**
         * 	isMobile，检查手机号码
         */
        public static function isMobile($no) {
                return (bool) preg_match("/(^1\d{10}$)/", $no);
        }

        /**
         * 	isTel，检查固话号码
         */
        public static function isTel($no) {
                return (bool) preg_match("/^(0(10|21|22|23|[1-9][0-9]{2})(-|))?[0-9]{7,8}$/", $no);
        }

        /**
         * 	isPhone，检查电话号码
         */
        public static function isPhone($no) {
                return self::isMobile($no) || self::isTel($no);
        }

        /**
         * 	isQQ，检查QQ号码
         */
        public static function isQQ($no) {
                return (bool) preg_match("/^\d{5,15}$/", $no);
        }

        public static function httpCurl($url, $post = '') {
                $url = trim($url);
                //TODO: 针对特例简单处理
                if (strpos($url, "/") === 0) {
                       $url = self::getDomain() .$url;
                }
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
                curl_setopt($ch, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36');
                if (!empty($post)) {
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                }
                return curl_exec($ch);
        }

        /**
         * 	page，分页函数
         *  -------------------------------------------------
         * 	@param $args array
         * 	@key int recordCount 总记录数
         * 	@key int pageSize 每页显示记录数[默认20]
         * 	@key bool showNav 显示记录导航[默认显示]
         * 	@key bool showDot 多页显示省略号[默认显示]
         * 	@key bool showJump 显示页码跳转[默认显示]
         * 	@key bool isAjax js支持的ajax分页[默认ajax分页]
         * 	@key int showDotNum 多页显示页码数[默认7]
         * 	@key bool showSelect 跳转方式[默认true:文本输入;false:下拉菜单]
         * 	@key string page_var 分页标示[默认用page做分页符]
         * 	@key string url 导航链接[可选]
         * 	@key array filter 筛选条件[可选]
         *  -------------------------------------------------
         * 	$this->smarty->assign('pa',Util::page(array("recordCount"=>1234,"pageSize"=>10,"showNav"=>true,"showDot"=>true,"showJump"=>true,"isAjax"=>true)));
         *  -------------------------------------------------
         * {$pa}
         *  -------------------------------------------------
         * css
         *  -------------------------------------------------
          <style type="text/css">
          {literal}
          .yfy {padding-right:1px; font-size: 0.85em;text-align: center;overflow: hidden;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;-webkit-box-shadow: 0 1px 2px rgba(0,0,0,0.05);-moz-box-shadow: 0 1px 2px rgba(0,0,0,0.05);box-shadow: 0 1px 2px rgba(0,0,0,0.05);
          }
          .yfy .jump,.yfy .current,.yfy .disabled,.yfy a {float:left;padding: 4px 12px;line-height: 20px;text-decoration: none;background-color: #fff;border: 1px solid #ddd;margin-right: -1px;}
          .yfy a:hover,.yfy a:active {background-color: #f5f5f5;}
          .yfy .current,.yfy a:active {cursor: default;background-color: #f5f5f5;color: #999;}
          .yfy .next {border: #ccdbe4 2px solid; margin: 0px 0px 0px 10px;}
          .yfy .next:hover,.yfy .prev:hover {border: #2b55af 2px solid;}
          .yfy .prev {border: #ccdbe4 2px solid;margin: 0px 10px 0px 0px;}
          .yfy .jump{float:left;padding:3px 12px;line-height:20px;text-decoration: none;background-color: #fff;border: 1px solid #ddd;margin-right: -1px;}
          .yfy .jump input{float:left;width:40px !important;line-height:19px;height:19px !important;padding:0;margin: 0;}
          .yfy span.text {float:left;margin:5px 5px 0px;}
          {/literal}
          </style>
         *  -------------------------------------------------
         *  js  ajax function
         *  -------------------------------------------------
         */
        public static function page($args) {
                !isset($args["recordCount"]) && $args["recordCount"] = 0;
                !isset($args["pageSize"]) && $args["pageSize"] = 20;
                !isset($args["showNav"]) && $args["showNav"] = true;
                !isset($args["showDot"]) && $args["showDot"] = true;
                !isset($args["showJump"]) && $args["showJump"] = true;
                !isset($args["isAjax"]) && $args["isAjax"] = true;
                !isset($args["jsFuncs"]) && $args["jsFuncs"] = "alert";
                !isset($args["showSelect"]) && $args["showSelect"] = false;
                !isset($args["showDotNum"]) && $args["showDotNum"] = 7;
                empty($args["url"]) && $args['url'] = $_SERVER["REQUEST_URI"];
                !isset($args["page_var"]) && $args["page_var"] = "page"; //请注意和控制器里的分页符保持一致
                !isset($args["filter"]) && $args["filter"] = array();

                $filter = "";
                //url分析
                $parse_url = parse_url($args["url"]);
                $url_query = $parse_url["query"];
                if ($url_query) {
                        if (is_array($args["filter"]) && $args["filter"]) {
                                parse_str($url_query, $old);
                                foreach ($args["filter"] AS $key => $value) {
                                        if (is_array($value)) {
                                                $tmp = rtrim($key, '[]');
                                        } else {
                                                $tmp = $key;
                                        }

                                        if (!isset($old[$tmp])) {
                                                if (is_array($value)) {
                                                        foreach ($value AS $v) {
                                                                $filter .= $key . '=' . $v . '&';
                                                        }
                                                } else {
                                                        //$filter .= $key . '=' . $value . '&';
                                                        //modify 换行标记处理成空格
                                                        $filter .= $key . '=' . preg_replace('/\n/i',' ', $value) . '&';
                                                }
                                        }
                                }
                        }

                        $page_var = $args['page_var'];
                        $patten = "'(^|&)$page_var=\d*'";
                        $url_query = preg_replace("'(^|&)" . $args["page_var"] . "=\d*'", "", $url_query);
                        $args["url"] = str_replace($parse_url["query"], $url_query, $args["url"]);
                        if ($url_query) {
                                $args["url"].="&" . $filter . $args["page_var"];
                        } else {
                                $args["url"].=$filter . $args["page_var"];
                        }
                } else {
                        if (is_array($args["filter"]) && $args["filter"]) {
                                foreach ($args["filter"] AS $key => $value) {
                                        $filter .= $key . '=' . addslashes($value) . '&';
                                }
                        }
                        $args["url"].="?" . $filter . $args["page_var"];
                }

                //页码计算
                $totalPages = ceil($args["recordCount"] / $args["pageSize"]);
                $page = isset($_GET[$args["page_var"]]) ? intval($_GET[$args["page_var"]]) : 1;
                $page = ($page < 1) ? 1 : $page;
                $page = min($page, $totalPages);
                $prev = $page - 1;
                $next = ($page == $totalPages ? 0 : $page + 1);
                $firstRow = $prev * $args["pageSize"];

                $pl = "<div class='yfy'>";

                //客户端检测
                $is_mob = Util::isMob();


                //导航
                if ($args["showNav"] && !$is_mob) {
                        $pl.="<span class='disabled'>记录：" . ($totalPages ? ($firstRow + 1) : 0) . " - " . min($firstRow + $args["pageSize"], $args["recordCount"]) . " / " . $args["recordCount"] . " &nbsp;</span><span class='disabled'>页：" . $page . " / " . $totalPages . " </span>";
                }
                if ($args["showNav"] && $is_mob) {
                        $pl.="<span class='disabled'>" . $args["recordCount"] . " </span>";
                }

                //不足一页
                if ($totalPages <= 1) {
                        return $pl;
                }

                if ($prev) {
                        $pl.="<a href='" . $args["url"] . "=1'>" . ($is_mob ? '|<' : '首页') . "</a> <a href='" . $args["url"] . "=$prev'>" . ($is_mob ? '<<' : '上一页') . "</a>";
                } else {
                        $pl.='<span class="disabled">' . ($is_mob ? '|<' : '首页') . '</span><span class="disabled">' . ($is_mob ? '<<' : '上一页') . '</span>';
                }

                if ($args["showDot"] && !$is_mob) {
                        $o = $args["showDotNum"]; //中间页码表总长度，为奇数
                        $u = ceil($o / 2); //根据$o计算单侧页码宽度$u
                        $f = $page - $u; //根据当前页$currentPage和单侧宽度$u计算出第一页的起始数字
                        //str_replace('{p}',,$fn)//替换格式
                        if ($f < 0) {
                                $f = 0;
                        }//当第一页小于0时，赋值为0
                        $n = $totalPages; //总页数,20页
                        if ($n < 1) {
                                $n = 1;
                        }//当总数小于1时，赋值为1
                        if ($page == 1) {
                                $pl.='<span class="current">1</span>';
                        } else {
                                $pl.="<a href='" . $args["url"] . "=1'>1</a> ";
                        }

                        for ($i = 1; $i <= $o; $i++) {
                                if ($n <= 1) {
                                        break;
                                }//当总页数为1时
                                $c = $f + $i; //从第$c开始累加计算
                                if ($i == 1 && $c > 2) {
                                        $pl.='<span class="current">...</span>';
                                }
                                if ($c == 1) {
                                        continue;
                                }
                                if ($c == $n) {
                                        break;
                                }
                                if ($c == $page) {
                                        $pl.='<span class="current">' . $page . '</span>';
                                } else {
                                        $pl.="<a href='" . $args["url"] . "=$c'>$c</a> ";
                                }
                                if ($i == $o && $c < $n - 1) {
                                        $pl.='<span class="current">...</span>';
                                }
                                if ($i > $n) {
                                        break;
                                }//当总页数小于页码表长度时
                        }
                        if ($page == $n && $n != 1) {
                                $pl.='<span class="current">' . $n . '</span>';
                        } else {
                                $pl.="<a href='" . $args["url"] . "=$n'>$n</a> ";
                        };
                }
                if ($next) {
                        $pl.="<a href='" . $args["url"] . "=$next'>" . ($is_mob ? '>>' : '下一页') . "</a> <a href='" . $args["url"] . "=$totalPages'>" . ($is_mob ? '>|' : '尾页') . "</a> ";
                } else {
                        $pl.='<span class="disabled">' . ($is_mob ? '>>' : '下一页') . '</span><span class="disabled">' . ($is_mob ? '>|' : '尾页') . '</span>';
                }

                if ($args["showJump"] && !$is_mob) {
                        if ($args["showSelect"]) {
                                $pl.="<span class='text'>跳转到</span><select name='topage' class='jump' size='1' onchange='";
                                if ($args["isAjax"]) {
                                        $pl.=$args["jsFuncs"] . "(\"" . $args["url"] . "=\"+this.value)'>";
                                        //$url."=\'+this.value+\'');'";
                                } else {
                                        $pl .="window.location=\"" . $args["url"] . "=\"+this.value'>";
                                }
                                //$pl.="跳转到<select name='topage' class='jump' size='1' onchange='window.location=\"$url=\"+this.value'>";
                                for ($i = 1; $i <= $totalPages; $i++) {
                                        if ($i == $page) {
                                                $pl.="<option value=" . $i . " selected>" . $i . "</option>";
                                        } else {
                                                $pl.="<option value=" . $i . ">" . $i . "</option>";
                                        }
                                }
                                $pl.="</select> <span class='text'>页</span>";
                        } else {
                                $pl.="<span class='text'>跳转到</span> <input type=\"text\" name=\"topage\" style='width:" . (8 * strlen($totalPages) + 30) . "px' class='jump' value=\"" . $page . "\" id=\"topage\" onchange='";
                                if ($args["isAjax"]) {
                                        $pl.=$args["jsFuncs"] . "(\"" . $args["url"] . "=\"+this.value)' />";
                                } else {
                                        $pl .="window.location=\"" . $args["url"] . "=\"+this.value' />";
                                }
                                $pl.=" <span class='text'>页</span>";
                        }
                }
                $pl.="</div>";
                if ($args["isAjax"]) {
                        $pl = preg_replace("/href='(.+)'/iU", "href='javascript:" . $args["jsFuncs"] . "(\"$1\");'", $pl);
                }
                return $pl;
        }

        /* 用户密码加密串 */

        const SUFFIX = 'kela~/!@#$%^&';

        /**
         * 密码加密
         * @param string $password 密码
         * @param string $suffix   外加加密串
         * @return string
         * 	@url UserController/insert|update|modifyPass
         */
        public static function xmd5($password, $suffix = "") {
                return md5(md5($password . self::SUFFIX) . $suffix);
        }

        /**
         * @param $str     要计算的字符 utf8
         * @param $length	限制长度
         *
         * @return bool     如果字符长度大于限制长度 false
         */
        public static function strCount($str, $length) {
                preg_match_all('/./us', $str, $match);
                $c = count($match[0]);
                if ($c > $length) {
                        return false;
                } else {
                        return true;
                }
        }

        /**
         * isHas	判断是否已存在
         *
         * @param $str		//新增值
         * @param $table	//表名
         * @param $filed	//查询字段
         *
         * @return boolean
         */
        public static function isHas($str, $table, $filed, $n = 1) {
                $sql = 'select count(*) from ' . $table . ' where ' . $filed . " = '$str'";
                $res = DB::cn($n)->getOne($sql);

                return (bool) $res;
        }

        /**
         * isMob	判断是否移动设备
         *
         * @return int
         */
        public static function isMob() {
                $mobile_browser = '0';
                if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
                        $mobile_browser++;
                }
                if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') > 0) or ( (isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
                        $mobile_browser++;
                }

                $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));

                $mobile_agents = array(
                        'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
                        'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
                        'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
                        'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
                        'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
                        'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
                        'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
                        'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
                        'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-');

                if (in_array($mobile_ua, $mobile_agents)) {
                        $mobile_browser++;
                }
                if (isset($_SERVER['ALL_HTTP'])) {
                        if (strpos(strtolower($_SERVER['ALL_HTTP']), 'OperaMini') > 0) {
                                $mobile_browser++;
                        }
                }

                if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') > 0) {
                        $mobile_browser = 0;
                }
                return $mobile_browser;
        }

        /**
         * getMethod 获取类自身的所有公共方法
         *
         * @return int
         */
        public static function getMethod($class_name) {
                if (class_exists('CommonController')) {
                        $_cls = 'CommonController';
                } else {
                        $_cls = 'Controller';
                }
                $class = array_diff(get_class_methods($class_name), get_class_methods($_cls));
                $class[] = 'index';
                return $class;
        }

        /**
         * 转换项目data目录下的tab文件
         * @param $data_path  文件路径
         * @return array|bool
         */
        public static function iniToArray($data_path) {
                $ext = Upload::getExt($data_path);
                if ($ext == 'tab') {
                        $json = parse_ini_file($data_path, true);
                        foreach ($json as $key => $val) {
                                if (strpos($key, 'columns') !== false) {
                                        foreach ($val as $k => $v) {
                                                if ($v === '1') {
                                                        $val[$k] = true;
                                                }
                                                if ($k == 'source' || $k == 'selectOptions') {
                                                        $val[$k] = explode(',', substr($v, 1, -1));
                                                }
                                        }
                                        $json['columns'][] = $val;
                                        unset($json[$key]);
                                }
                        }
                        return $json;
                } else {
                        return false;
                }
        }

        /**
         * 转换编码格式，导出csv数据
         * @param $name     名字
         * @param $title    标题
         * @param $content  内容
         * @return array|bool
         */
        public static function downloadCsv($name, $title, $content) {
                header('Content-Type: application/vnd.ms-excel');
                header("Content-Disposition: attachment;filename=" . iconv('utf-8', 'GB18030', $name) . ".csv");
                header('Cache-Control: max-age=0');
                $fp = fopen('php://output', 'w');
                foreach ($title as $k => $v) {
                        $title[$k] = iconv('utf-8', 'GB18030', $v);
                }
                fputcsv($fp, $title);
                if ($content) {
                        foreach ($content as $k => $v) {
                                foreach ($v as $kk => $vv) {
                                        $v[$kk] = iconv('utf-8', 'GB18030', $vv);
                                }
                                fputcsv($fp, $v);
                        }
                }
                fclose($fp);
                exit;
        }

        /**
         * 转换编码格式，导出csv数据
         * @param $name     名字
         * @param $title    标题
         * @param $content  内容
         * @return array|bool
         */
        public static function downloadCsv2($name, $title, $content) {
                
                header("Content-type: text/html; charset=gbk");
                header("Content-type:aplication/vnd.ms-excel");
                header("Content-Disposition:filename=" . iconv("utf-8", "gbk", $name) . ".csv");

                $xls_content="";
                foreach ($title as $k => $v) {
                        $title[$k] = iconv('utf-8', 'gbk', $v);
                        $xls_content.=$title[$k].",";
                }
                $xls_content.="\r\n";
                if ($content) {
                        foreach ($content as $k => $v) {
                                foreach ($v as $kk => $vv) {
                                        $v[$kk] = iconv('utf-8', 'gbk', $vv);
                                        $xls_content.=$v[$kk].",";
                                }
                                $xls_content.="\r\n";
                        }
                }
                echo $xls_content;
                exit;
        }
        
		public static function downloadCsvNew($name, $conf, $content)
		{
				header('Content-Type: application/vnd.ms-excel');
                header("Content-Disposition: attachment;filename=" . iconv('utf-8', 'gbk', $name) . ".csv");
                header('Cache-Control: max-age=0');
				ob_clean();
				flush();
                $fp = fopen('php://output', 'w');
				$title=array_column($conf,'title');
				$code=array_column($conf,'field');
                foreach ($title as $k => $v) {
                        $title[$k] = iconv('utf-8', 'gbk', $v);
                }
                fputcsv($fp, $title);
                if ($content) {
                        foreach($content as $key=>$v)
						{
							foreach($v as $k=>$r)
							{
								if(!in_array($k,$code))
								{
									unset($v[$k]);
								}
								else
								{
									$v[$k] = iconv('utf-8', 'gbk', $r);
								}
							}

							fputcsv($fp, $v);
						}
                }
                fclose($fp);
                exit;

		}

        /**
         * 数组转换为xml的方法
         * @param $arr		转换的的数组
         * @param int $dom  xml dom容器
         * @param int $item 多维数组每一个明细的容器
         *
         * @return string	xml
         */
        public function arrtoxml($arr, $dom = 0, $item = 0) {
                if (!$dom) {
                        $dom = new DOMDocument("1.0");
                }
                if (!$item) {
                        $item = $dom->createElement("root");
                        $dom->appendChild($item);
                }
                foreach ($arr as $key => $val) {
                        $itemx = $dom->createElement(is_string($key) ? $key : "item");
                        $item->appendChild($itemx);
                        if (!is_array($val)) {
                                $text = $dom->createTextNode($val);
                                $itemx->appendChild($text);
                        } else {
                                $this->arrtoxml($val, $dom, $itemx);
                        }
                }
                return $dom->saveXML();
        }

        /*
         * 	跳转函数,取代header。强制以top方式打开页面，防止程序嵌套。目前只应用于会话过期后跳转登录页
         */

        public static function jump($url, $target = '_top') {
                if (Util::isPost()) {
                        $str = '<meta http-equiv="refresh" content="0;url=\'' . $url . '\'">';
                        echo $str;
                } else {
                        $str = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><script type=\"text/javascript\">";
                        $str .="javascript:window.location.target='" . $target . "';";
                        $str .="javascript:window.location.href='" . $url . "'";
                        $str .=";</script>";
                        echo $str;
                }
                exit;
        }

        /*
         * 	二维数组去重
         */

        public static function array_unique_fb(&$arr, $key) {
                $rAr = array();
                for ($i = 0; $i < count($arr); $i++) {
                        if (!isset($rAr[$arr[$i][$key]])) {
                                $rAr[$arr[$i][$key]] = $arr[$i];
                        }
                }
                $arr = array_values($rAr);
        }

        /*
        *检查远程图片是否真实存在
        * ADD BY ZHANGRUIYING
        */
        public static function check_remote_file_exists($url)
        {
                $curl = curl_init($url);
                // 不取回数据
                curl_setopt($curl, CURLOPT_NOBODY, true);
                // 发送请求
                $result = curl_exec($curl);
                $found = false;
                // 如果请求没有发送失败
                if ($result !== false) {
                        // 再检查http响应码是否为200
                        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                        if ($statusCode == 200) {
                                $found = true;
                        }
                }
                curl_close($curl);
                return $found;
        }

        /*
        *  从常量数组获取指定参数值
        */
        public static function get_defined_array_var($const, $key = '') {
                if (!defined($const)) {
                        return false;
                }                    
                $array = json_decode(constant($const), true);  
                if (empty($array)) {
                        return false;
                }  
                
                if (empty($key)) return $array;
                return array_key_exists($key, $array) ? $array[$key] : false;
        }

        private static function createSign($mod, $act, $keys, $vals) {
                $args=array();
                foreach($keys as $k=>$v){
                        $v=trim($v);
                        if(!empty($v)){
                                $args[$keys[$k]]=$vals[$k];
                        }
                }

                return self::createSignV2($mod, $act, $args);
        }

        private static function createSignV2($mod, $act, $args = array()) {
                $args['x_epoch'] = time();
                $args['x_exp'] = 5;
                ksort($args);

                $filter = json_encode($args);
                $auth_key = self::get_defined_array_var('API_AUTH_KEYS', $mod);
                return array('filter'=> $filter, 'sign' => md5(self::getDomain() . $mod. $act. $filter . $auth_key));
        }

        public static function verifySign($mod, $act, $filter, $sign) {
                $args = json_decode($filter, true);
                if (empty($args)) {
                        return false;
                }

                if (!isset($args['x_epoch']) || !isset($args['x_exp'])) {
                        return false;
                }

                if (!is_numeric($args['x_epoch']) || !is_numeric($args['x_exp'])) {
                        return false;
                }

                $duration = time() - intval($args['x_epoch']);
                if ($duration > 60 * floatval($args['x_exp'])) {
                        return false;
                }

                $auth_key = self::get_defined_array_var('API_AUTH_KEYS', $mod);
                $resign = md5(self::getDomain() . $mod. $act. $filter . $auth_key);
                if ($sign == $resign) {
                        return $args;
                }

                return false;
        }


      public static function verifyOpenSign($mod, $act, $filter, $sign) {
        $args = json_decode($filter, true);

        if (empty($args)) {
            return false;
        }

        if (!isset($args['timestamp']) || !isset($args['limit_time'])) {
            return false;
        }

        if (!is_numeric($args['timestamp']) || !is_numeric($args['limit_time'])) {
            return false;
        }

        $duration = time() - intval($args['timestamp']);
        if ($duration > 60 * floatval($args['limit_time'])) {
            return false;
        }

        if(SYS_SCOPE != 'zhanting') return false;
        $auth_key = 'jsg43gh@6r6';
        $resign = md5(self::getDomain() . $mod. $act. $auth_key. $filter);
        if ($sign == $resign) {
            return $args;
        }

        return false;
    }
        
        public static function sendRequest($mod, $act, $keys, $vals) {
        		$url = self::getDomain().'/api.php?con='.$mod.'&act='.$act;
                $signed_data = self::createSign($mod, $act, $keys,$vals);
                $resp = self::httpCurl($url,$signed_data,false,true,30);
                if (defined('LOG_API_CALL') && LOG_API_CALL) file_put_contents(KELA_ROOT.'/'.$mod.'.log', $act.':'.json_encode($signed_data).PHP_EOL.'resp:'.$resp.PHP_EOL.PHP_EOL, FILE_APPEND);
                return json_decode($resp,true);
        }

        public static function sendRequestV2($mod, $act, $params) {
        		$url = self::getDomain().'/api.php?con='.$mod.'&act='.$act;
                $signed_data = self::createSignV2($mod, $act, $params);
                $resp = self::httpCurl($url,$signed_data,false,true,30);
                if (defined('LOG_API_CALL') && LOG_API_CALL) file_put_contents(KELA_ROOT.'/'.$mod.'.log', $act.':'.json_encode($signed_data).PHP_EOL.'resp:'.$resp.PHP_EOL.PHP_EOL, FILE_APPEND);
                return json_decode($resp,true);             
        }
        
        /***
         * explode a string and return an array with no empty elements
         */
        public static function eexplode($separator,$string) {
            return preg_split("/\s*${separator}\s*/",$string,-1,PREG_SPLIT_NO_EMPTY);
        }
        
        public static function bootboxAlert($info, $exit = true) {
        	$str = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><script type=\"text/javascript\">util.error('" . $info . "');util.closeTab();</script>";
        	if ($exit) {
                exit($str);
        	} else {
                echo $str;
        	}
        }
        
        public static function indexArray($array, $key, $groups = [])
        {
        	$result = [];
        	$groups = (array)$groups;
        	
        	foreach ($array as $element) {
        		$lastArray = &$result;
        		
        		foreach ($groups as $group) {
        			$value = static::getValue($element, $group);
        			if (!array_key_exists($value, $lastArray)) {
        				$lastArray[$value] = [];
        			}
        			$lastArray = &$lastArray[$value];
        		}
        		
        		if ($key === null) {
        			if (!empty($groups)) {
        				$lastArray[] = $element;
        			}
        		} else {
        			$value = static::getValue($element, $key);
        			if ($value !== null) {
        				if (is_float($value)) {
        					$value = (string) $value;
        				}
        				$lastArray[$value] = $element;
        			}
        		}
        		unset($lastArray);
        	}
        	
        	return $result;
        }
        
        static function getValue($array, $key, $default = null)
        {
        	if ($key instanceof \Closure) {
        		return $key($array, $default);
        	}
        	
        	if (is_array($key)) {
        		$lastKey = array_pop($key);
        		foreach ($key as $keyPart) {
        			$array = static::getValue($array, $keyPart);
        		}
        		$key = $lastKey;
        	}
        	
        	if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array)) ) {
        		return $array[$key];
        	}
        	
        	if (($pos = strrpos($key, '.')) !== false) {
        		$array = static::getValue($array, substr($key, 0, $pos), $default);
        		$key = substr($key, $pos + 1);
        	}
        	
        	if (is_object($array)) {
        		// this is expected to fail if the property does not exist, or __get() is not implemented
        		// it is not reliably possible to check whether a property is accessible beforehand
        		return $array->$key;
        	} elseif (is_array($array)) {
        		return (isset($array[$key]) || array_key_exists($key, $array)) ? $array[$key] : $default;
        	} else {
        		return $default;
        	}
        }


        public static function xmlToArray($xml)
        {    
                //禁止引用外部xml实体
                libxml_disable_entity_loader(true);
                $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);        
                return $values;
        }     

        
        public static function endwith($haystack, $needle) {
        	return substr_compare($haystack, $needle, -strlen($needle)) === 0;
        }
        
        public static function startwith($haystack, $needle) {
        	return substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
        }
        
        public static function beginTransaction($pdolist){
            $result = array('success'=>0,'error'=>'');
            if(!empty($pdolist)){
                foreach ($pdolist as $key=>$pdo){
                    if(!is_object($pdo)){
                        throw new Exception("无效PDO对象");
                    }
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
                    $pdo->beginTransaction();//开启事务
                }
            } else{
                throw new Exception("参数错误：pdolist必须为数组");
            }
            return true;
        }
        
        public static function commitTransaction($pdolist){
            if(!empty($pdolist)){
                foreach ($pdolist as $key=>$pdo){
                    if(!is_object($pdo)){
                        throw new Exception("无效PDO对象");
                    }
                    $pdo->commit();//如果没有异常，就提交事务
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                }
            } else{
                throw new Exception("参数错误：pdolist必须为数组");
            }
            return true;
        }
        
        public static function get_model($class, $params = array()) {
            $namespaces = self::eexplode('\\', $class);
            if (count($namespaces) == 2){
                Kela::autoload($class);
                $class = $namespaces[1];
            } else {
                $class = $namespaces[0];
            }
            
            switch (count($params)) {
                case 0:
                    return new $class();
                case 1:
                    return new $class($params[0]);
                case 2:
                    return new $class($params[0], $params[1]);
                case 3:
                    return new $class($params[0], $params[1], $params[2]);
                case 4:
                    return new $class($params[0], $params[1], $params[2], $params[3]);
                case 5:
                    return new $class($params[0], $params[1], $params[2], $params[3], $params[4]);
                default:
                    $refClass = new \ReflectionClass($class);
                    return $refClass->newInstanceArgs($params);
            }
        }

        public static function pointApi($url,$data=null,$method='PUT'){
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);                  
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                if(!empty($data)){
                     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); //定义请求类型，当然那个提交类型那一句就不需要了
                     curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //定义提交的数据
                     curl_setopt($ch,CURLOPT_HTTPHEADER,array("X-HTTP-Method-Override: {$method}"));
                }
                $data = curl_exec($ch);
                curl_close($ch);
                return $data;
        }

    /**
     * @param integer $departmentId
     * @param string $mobile
     * @param integer $activityDate
     * @return array
     * @throws Exception
     */
    public static function point_api_get_config($departmentId, $mobile, $activityDate = null)
    {
        if(empty($activityDate)) {
            $activityDate = time();
        }
        $url = self::POINT_MALL_API_HOST . "/crm/pointrules/{$departmentId}?phone={$mobile}&target_date={$activityDate}";
        $rs = self::pointApi($url);
        if(!$rs) {
            throw new Exception("积分服务接口暂时不可用！");
        }
        $rs = json_decode($rs, true);
        if(!is_array($rs) || count($rs) == 0) {
            throw new Exception("积分服务接口暂时不可用！");
        }
        $config = [
            'is_enable_point' => false,
            'convert_rate' => 1,
            'discount_rules' => [],
            'reward_rules' => [],
            'activity_rate' => [
                'multiple' => 1,
                'activity_name' => ''
            ]
        ];

        foreach ($config as $key => &$val) {
            if(array_key_exists($key, $rs)) {
                $val = $rs[$key];
            }
        }
        return $config;
    }

    public static function point_api_match_discount_rule($discount, $rules,$jintuoType)
    {
        $point_item_config_zhekou = 1;
        if(!empty($rules)) {
            foreach ($rules as $v) {
                if( bccomp($discount, round($v['discount_range_right']/100, 2), 5) == -1 
                        &&  (
                                bccomp($discount, round($v['discount_range_left']/100, 2), 5) == 1 
                                || bccomp($discount, round($v['discount_range_left']/100, 2), 5) == 0
                            )
                        &&  ($jintuoType == $v['goods_type'])
                        ){
                    $point_item_config_zhekou = round($v['point_percent']/100, 2);
                    break;
                }
            }
        }
        return $point_item_config_zhekou;
    }
    
    public static function point_api_match_reward_rule($rules, $goodsType, $xiangqianType, $isStock, $jietuoType, $certType, $styleSn, $carat, $xiangkou)
    {
        $needReward = true;
        $rewardRate = 0;
        //奖励积分： 非裸钻 如果现货，镶嵌要求是非工厂配钻工厂镶嵌或成品 不送积分 ，期货金托类型是非成品 不送积分
        if($goodsType <> 'lz' && $goodsType <> 'caizuan_goods'){
            if($isStock == 1 && $xiangqianType <> '工厂配钻，工厂镶嵌' && $xiangqianType <> '成品') {
                $needReward = false;
            }
            if($isStock <> 1 && $jietuoType <> '1' && $jietuoType <> '成品'){
                $needReward = false;
            }
        }
        if($needReward && !empty($rules)){
            if($jietuoType == '成品'){
                $rewardRate = self::getHighRules($rules,'style_goods',$certType,$carat,$styleSn);
            }
            if($goodsType == 'lz' || $goodsType == 'caizuan_goods'){
                $rewardRate = self::getHighRules($rules,'lz',$certType,$carat,$styleSn);
            }
            if($jietuoType == '空托'){
                $rewardRate = self::getHighRules($rules,'jietuo',$certType,$carat,$styleSn);
            }
           /* foreach ($rules as $rule){
                //必须要有钻石大小
                if($carat >= 0) {
                    //证书类型+商品款号+钻石大小 或者 证书类型+钻石大小  或者商品款号+钻石大小
                    //成品：证书类型+商品款号+钻石大小
                    if ($jietuoType == '成品' && ($styleSn || $certType) && $rule['goods_type']=='style_goods'){
                        //if(isset($rule['cert']) && $certType != $rule['cert'] || empty($rule['goods_sn'] || $styleSn != $rule['goods_sn'])) {
                          //  continue;
                        //}


                         * 成品

                        if(isset($rule['cert']) && isset($rule['goods_sn'])){
                            if($rule['cert'] != $certType || $rule['goods_sn'] != $styleSn){
                                continue;
                            }
                        } else {
                            if(isset($rule['cert']) && $rule['cert'] != $certType){
                                continue;
                            }
                            if(isset($rule['goods_sn']) && $rule['goods_sn'] != $styleSn){
                                continue;
                            }
                        }
                        if (isset($rule['diamond_range_left']) && isset($rule['diamond_range_right']) && $rule['diamond_range_left'] <= $carat && $rule['diamond_range_right'] > $carat) {
                            $rewardRate = $rule['point_percent'];
                            break;
                        } elseif (isset($rule['diamond_range_left']) && $rule['diamond_range_left'] <= $carat) {
                            $rewardRate = $rule['point_percent'];
                            break;
                        } elseif (isset($rule['diamond_range_right']) && $rule['diamond_range_right'] > $carat) {
                            $rewardRate = $rule['point_percent'];
                            break;
                        }
                    }
                    //裸石：证书类型+钻石大小
                    if(($goodsType == 'lz' || $goodsType == 'caizuan_goods') && $rule['goods_type']=='lz'){
                        if(isset($rule['cert']) && $certType != $rule['cert']) {
                            continue;
                        }
                        if (isset($rule['diamond_range_left']) && isset($rule['diamond_range_right']) && $rule['diamond_range_left'] <= $carat && $rule['diamond_range_right'] > $carat) {
                            $rewardRate = $rule['point_percent'];
                            break;
                        } elseif (isset($rule['diamond_range_left']) && $rule['diamond_range_left'] <= $carat) {
                            $rewardRate = $rule['point_percent'];
                            break;
                        } elseif (isset($rule['diamond_range_right']) && $rule['diamond_range_right'] > $carat) {
                            $rewardRate = $rule['point_percent'];
                            break;
                        }
                    }
                    //空托：商品款号+钻石大小
                    if($jietuoType == '空托' && $rule['goods_type'] == 'jietuo') {
                        if (isset($rule['diamond_range_left']) && isset($rule['diamond_range_right']) && $rule['diamond_range_left'] <= $xiangkou && $rule['diamond_range_right'] > $xiangkou) {
                            $rewardRate = $rule['point_percent'];
                            break;
                        } elseif (isset($rule['diamond_range_left']) && $rule['diamond_range_left'] <= $xiangkou) {
                            $rewardRate = $rule['point_percent'];
                            break;
                        } elseif (isset($rule['diamond_range_right']) && $rule['diamond_range_right'] > $xiangkou) {
                            $rewardRate = $rule['point_percent'];
                            break;
                        }
                    }
                }
            }*/
        }
        else {
            $rewardRate = ['id'=>0,'value'=>0];
        }
        $rewardRate['value'] = round($rewardRate['value'] / 100, 2);
        return $rewardRate;
    }


    /**
     * @param $rules
     * @param $ruleGoodsType
     * @param $cert
     * @param $diaSeize
     * @param $styleSn
     * 获取积分比例
     */
    public static function getHighRules($rules,$ruleGoodsType,$cert,$diaSeize,$styleSn){
        $arr = [];
        //$reward = 0;
        foreach ($rules as $rule){
            if($rule['goods_type'] == $ruleGoodsType){
                /**
                 * 证书类型匹配
                 * 1、证书类型+钻石大小+款号
                 * 2、证书类型+钻石大小
                 * 3、证书类型
                 * 第一种匹配到就直接应用
                 * 款号匹配
                 * 1、款号
                 * 2、款号+钻石大小
                 */
                //匹配证书类型
                if(!empty($cert) && $rule['cert'] == $cert){
                    if(!empty($rule['diamond_range_left']) && !empty($rule['diamond_range_right'])){
                        if(!empty($diaSeize) && $rule['diamond_range_left'] <= $diaSeize && $diaSeize <= $rule['diamond_range_right']){
                            //钻石类型+钻石大小+款式编号
                            if(isset($rule['goods_sn'])&& $rule['goods_sn'] == $styleSn){
                                $arr['certificate'][] = $rule;
                            } else {
                                //钻石类型+钻石大小
                                if(empty($rule['goods_sn'])){
                                    $arr['certificate'][] = $rule;
                                }
                            }
                        }
                    } else { //只有证书类型的商品
                        $arr['certificate'][] = $rule;
                    }
                 //匹配款号
                } else {
                    if(isset($rule['goods_sn']) && $rule['goods_sn'] == $styleSn && empty($rule['cert'])) {
                        if(!empty($rule['diamond_range_left']) && !empty($rule['diamond_range_right'])) {
                            if(!empty($diaSeize) && $rule['diamond_range_left'] <= $diaSeize && $diaSeize <= $rule['diamond_range_right']) { // 款号+钻石大小
                                $arr['style'][] = $rule;
                            }
                        } else { //证书号
                                $arr['style'][] = $rule;
                        }
                    }
                }
            }
        }
        $certificate = empty($arr['certificate']) ? [] : $arr['certificate'];
        $style = empty($arr['style']) ? [] : $arr['style'];
        $value = empty($arr['certificate']) ? ( empty($arr['style']) ? [] :  $arr['style'] ) : $arr['certificate'];
        $reward = static::returnRewardAndId($value);
        /* if(count($certificate) > 0 ){
            $thans = array_column($certificate,'point_percent');
            $reward = max($thans);
        } else if(count($style) > 0) {
            $thans = array_column($style,'point_percent');
            $reward = max($thans);
        } */
        return $reward;
    }

    public static function returnRewardAndId(array $rules)
    {
        $reward = ['id'=>0,'value'=>0] ;
        if(count($rules) > 0)
        {
            $thans = array_column($rules,'point_percent');
            $reward['value'] = max($thans);
            foreach($rules as $rule)
            {
                if( $reward['value'] == $rule['point_percent'] )
                {
                    $reward['id'] = $rule['id'];
                }
            }
        }
        return $reward;
    }

    /**
     * @param $pointRules
     * @param $goodsPrice
     * @param $favorablePrice
     * @param $daijinquanPrice
     * @param $zhuandanCash
     * @param $certType
     * @param $carat
     * @param $styleSn
     * @param $goodsType
     * @param $isStock
     * @param $xiangqianType
     * @param $jietuoType
     * @param $xiangkou
     * @param $intuoType
     * @return array [折扣积分，奖励积分，活动翻倍倍数，活动名称]
     */
    public static function point_api_calculate_order_detail_point($pointRules, $goodsPrice, $favorablePrice, $daijinquanPrice, $zhuandanCash, 
            $certType, $carat, $styleSn, $goodsType, $isStock, $xiangqianType, $jietuoType, $xiangkou,$jintuoType)
    {
        if(empty($pointRules) || !$pointRules['is_enable_point']) {
            return [0, 0, 1, ""];
        }

        $baseConvertRate = $pointRules['convert_rate'];
        $discountRules = $pointRules['discount_rules'];
        $rewardRules = $pointRules['reward_rules'];
        $itemDiscountRate = round(($goodsPrice - $favorablePrice - (float)$daijinquanPrice) / $goodsPrice,5) ;
        if($itemDiscountRate <= 0){
            return [0, 0];
        }
        $activityRate = $pointRules['activity_rate']['multiple'];
        $activityName = $pointRules['activity_rate']['activity_name'];
        if($activityRate <= 1) {
            $activityRate = 1;
        }

        //获取折扣积分比例
        $discountRate = Util::point_api_match_discount_rule($itemDiscountRate, $discountRules,$jintuoType);

        //获取CRM额外奖励积分规则
        $rewardRateAndId = self::point_api_match_reward_rule($rewardRules, $goodsType, $xiangqianType, $isStock, $jietuoType, $certType, $styleSn, $carat, $xiangkou);
        $rewardRateId = $rewardRateAndId['id'];
        $rewardRate = $rewardRateAndId['value'];
        $goods_price = $goodsPrice - $favorablePrice - $daijinquanPrice - $zhuandanCash;
        $point_zhekou  = round($goods_price * $baseConvertRate * $discountRate * $activityRate,0);
        $point_jiangli = round($goods_price * $baseConvertRate * $rewardRate ,0);
        return [
            $point_zhekou,
            $point_jiangli,
            $activityRate,
            $activityName,
            $rewardRateId
        ];
    }

        //获取CRM积分兑换比例 即1元金额兑换多少积分
    /**
     * @deprecated use point_api_get_config($departmentId, $phone) instead
     * @return mixed|null
     */
        public static function point_api_base_multiple(){
            $url =self::POINT_MALL_API_HOST . "/crm/pointitemconfig/order_rule";
                return self::pointApi($url);
        } 

        //获取折扣积分兑换比例
    /**
     * @deprecated use point_api_get_config($departmentId, $phone) instead
     * @return mixed|null
     */
        public static function point_api_discount_config($departmentid){
            $url = self::POINT_MALL_API_HOST . "/crm/pointrules/{$departmentid}-1";
                return self::pointApi($url);
        } 

        //获取额外奖励积分兑换比例
    /**
     * @deprecated use point_api_get_config($departmentId, $phone) instead
     * @return mixed|null
     */
        public static function point_api_jiangli_config($departmentid){
            $url = self::POINT_MALL_API_HOST . "/crm/pointrules/{$departmentid}-2";
                return self::pointApi($url);
        } 

        //获取CRM代金券兑换码               
        public static function point_api_get_daijinquan($daijinquan_code){
            $get_daijinquan_duihuanma_url = self::POINT_MALL_API_HOST . "/crm/pointexchangelog/".$daijinquan_code;
                return Util::pointApi($get_daijinquan_duihuanma_url);
        } 

        //更新CRM代金券兑换码状态               
        public static function point_api_update_daijinquan($data){
            $url = self::POINT_MALL_API_HOST . "/crm/pointexchangelog/{$data['daijinquan_code']}";//.$data['daijinquan_code'];
                $update_daijinquan_status_data = array('used_time '=>$data['used_time'] ,'order_sn'=> $data['order_sn'],'bespoke_sn'=>$data['bespoke_sn'] ,'is_used'=>$data['is_used']);
                return self::pointApi($url,http_build_query($update_daijinquan_status_data));
        }   

        //展厅隐藏信息
        public static function zhantingInfoHidden($data) {
            if(SYS_SCOPE == 'zhanting' 
                && $data['hidden'] == '1'){
                return false;
            }
            return true;
        }

        public static function hidden_name($user_name){
            $strlen     = mb_strlen($user_name, 'utf-8');
            $firstStr     = mb_substr($user_name, 0, 1, 'utf-8');
            $lastStr     = mb_substr($user_name, -1, 1, 'utf-8');
            return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
        } 

        public static function hidden_tel($phone)
        {
                //隐藏邮箱
                if (strpos($phone, '@')) {
                    $email_array = explode("@", $phone);
                    $prevfix = (strlen($email_array[0]) < 4) ? "" : substr($phone, 0, 3); //邮箱前缀
                    $count = 0;
                    $str = preg_replace('/([\d\w+_-]{0,100})@/', '***@', $phone, -1, $count);
                    $rs = $prevfix . $str;
                    return $rs;
                } else {
                    //隐藏联系方式中间4位
                    $Istelephone = preg_match('/(0[0-9]{2,3}[\-]?[2-9][0-9]{6,7}[\-]?[0-9]?)/i', $phone); //固定电话
                    if ($Istelephone) {
                        return preg_replace('/(0[0-9]{2,3}[\-]?[2-9])[0-9]{3,4}([0-9]{3}[\-]?[0-9]?)/i', '$1****$2', $phone);
                    } else {
                        return preg_replace('/(1[0-9]{1}[0-9])[0-9]{4}([0-9]{4})/i', '$1****$2', $phone);
                    }
                }

        }
}

?>
