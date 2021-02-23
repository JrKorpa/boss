<?php

/*
 *  -------------------------------------------------
 *   @file		: Controller.class.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author		: Laipiyang <462166282@qq.com>
 *   @date		:
 *   @update		:
 *  -------------------------------------------------
 */

abstract class Controller {

        public $request = null; //请求参数
        protected $smarty = null; //视图Smarty
        protected $headerInfo = array();
        protected $smartyDebugEnabled = false;

        /**
         * 	$smartyCacheEnabled
         * 	true表示开启开启smarty缓存
         * 	如果页面内容是随会话状态而改变的，请重载此变量并设置为 false
         *
         */
        protected $smartyCacheEnabled = false;

        /**
         * 	构造函数
         *
         */
        function __construct() {
                $this->smarty();
        }

        /**
         * 	view，配置smarty
         *
         */
        private function smarty() {
                //echo APP_SMARTY_TEMPLATES_DIR.strtolower(substr(get_class($this),0,-10))."/";exit;
                $smarty = new Smarty();
                $smarty->template_dir = APP_SMARTY_TEMPLATES_DIR . strtolower(substr(get_class($this), 0, -10)) . "/"; //设置模板页面的目录
                $smarty->compile_dir = APP_SMARTY_COMPILE_DIR; //smarty的编译缓存目录
                $smarty->left_delimiter = KELA_LEFT; //左定界符
                $smarty->right_delimiter = KELA_RIGHT; //右定界符
                $smarty->cache_dir = APP_SMARTY_CACHE_DIR . $this->cacheDir();

                if ($this->smartyCacheEnabled && strlen($this->cachePrefix()) < 160) {
                        $smarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
                }
                $smarty->setCacheLifetime(3600);
                $smarty->debugging = $this->smartyDebugEnabled; //smarty的调试
                return $this->smarty = &$smarty;
        }

        /**
         * 	cachePrefix
         *  自定义 smarty 缓存前缀，以使缓存能够区分具体的请求参数
         *  默认使用 url 区分
         */
        protected function cachePrefix() {
                return urlencode($_SERVER["REQUEST_URI"]);
        }

        /**
         * 	cacheDir
         *  自定义 smarty 缓存路径，分片缓存文件
         *  默认两层路径缓存
         */
        protected function cacheDir() {
                $hash = md5($_SERVER["REQUEST_URI"]);
                return substr($hash, 0, 2) . '/' . substr($hash, 2, 2) . '/';
        }

        /**
         * 	run，控制器和动作转发
         *
         * 	@param Object $_request request 对象
         *
         */
        //控制器和动作转发
        function run($_request = NULL) {
                $this->request = is_null($_request) ? Route::getRequestByUrl() : $_request;

                //控制器转发
                if (method_exists($this, 'init'))
                        $this->init($this->request->action, ucfirst(substr(get_class($this), 0, -10)));

                $this->initExtra();
                //动作转发
                if (method_exists($this, $this->request->action)) {
                        $act = $this->request->action;
                        $this->$act($this->request->params);
                } else {
                        $msg = get_class($this) . '.php不存在' . $this->request->action . '方法';
                        if (APP_DEBUG) {
                                throw new MethodException($msg);
                        } else {
                                Log::record($msg . PHP_EOL . __FILE__, "FATAL");
                                $this->errAction();
                        }
                }
        }

        /**
         * 	index
         *  须重载的方法，控制器至少有一个 index 行为
         */
        abstract public function index($params);

        /**
         * 	errAction，错误处理页面
         *
         */
        protected function errAction() {
                if (!is_file('/public/destnation/404.html')) {
                        die('请创建404错误处理页面！');
                }
                Route::errorRedirect404('404');
        }

        /**
         * 	buildUrl
         *  产生本控制器相应 action 的 url 链接
         *  此方法可以不在 request 上下文中被调用，因此子类的继承代码不要处理 request 对象
         *
         *  @param String $action 目标 action
         *  @param Array $param 其他查询参数
         *
         */
        public function buildUrl($action = 'index', $param = array()) {
                return $_SERVER["REQUEST_URI"];
        }

        /**
         * 	isViewCached
         *  判断生成的视图是否仍被缓存，可以用于跳过非动态内容的处理过程
         */
        public function isViewCached($page) {
                $__isViewCached = null;
                if ($this->smartyCacheEnabled) {
                        $__isViewCached = $this->smarty->isCached($page, $this->cachePrefix());
                }
                return $__isViewCached;
        }

        /**
         * 	redirect，产生客户端跳转
         *
         * 	@param String/Array $action
         *      如为string，且以"location:"开头，直接调用header执行跳转，否则跳转到本控制器内相应action；
         *      如为array，则将元素1解释为controller，元素2为action，在控制器间跳转
         * 	@param Array $param 其他参数
         * 	@param Integer $type 1：php跳转，2：js顶层 frame 跳转
         *
         * 	@return void
         *
         */
        public function redirect($action, $param = array(), $type = 1) {
                $location = "";
                if (is_string($action) && substr($action, 0, 9) == 'location:') {
                        $location .= substr($action, 9);
                } else {
                        $url = '';
                        if (is_array($action)) {
                                $controller = Route::getController($action[0]);
                                if ($controller == null) {
                                        $msg = ucfirst($action[0]) . 'Controller.php不存在';
                                        if (APP_DEBUG) {
                                                throw new MethodException($msg);
                                        } else {
                                                Log::record($msg . PHP_EOL . __FILE__, "FATAL");
                                                $this->errAction();
                                        }
                                }
                                $action = isset($action[1]) ? $action[1] : 'index';
                                $url .= $controller->buildUrl($action, $param);
                        } else {
                                $url .= $this->buildUrl($action, $param);
                        }
                        $location .= $url;
                }
                if ($location) {
                        $location = preg_match('/^http:\/\//', $location) ? "$location" : Util::getDomain() . $location;
                        if ($type == 1) {
                                header('location:' . $location);
                        } else if ($type == 2) {
                                print ("<script type=\"text/javascript\">window.top.location.href=\"$location\";</script>");
                        }
                }
        }

        /**
         * 	析构函数
         * 	保存错误日志
         *
         */
        function __destruct() {
                if (!empty(Log::$log)) {
                        Log::save();
                }
        }

        /**
         * initExtra
         *  主站页面全局附加设置
         */
        protected function initExtra() {
                
        }

        /**
         * 	getBreadcrumb，面包屑导航，返回一个html结果数组
         *
         * 	@param $action 即请求的 action 方法，等同于$this->request->action
         *
         * 	@return Array
         *
         */
        public function getBreadcrumb($action = 'index') {
                return array();
        }

        /**
         * 	setHeader
         *  设置 html 页面的 header 内容，包括 title, keywords, description 等
         *  多次调用的参数会合并
         * 	@param Array $header_info 键值数组
         */
        public function setHeader($header_info) {
                $this->headerInfo = array_merge($this->headerInfo, $header_info);
                $this->assign('page_header', (object) $this->headerInfo);
                foreach ($header_info as $key => $value) {
                        $f = 'header' . ucfirst($key);
                        $this->request->$f = $value;
                }
        }

        /**
         * 	assign，模板赋值
         *
         * 	@param String $key 模版变量名
         * 	@param mixed $value 变量值
         *
         */
        public function assign($key, $value) {
                $this->smarty->assign($key, $value);
        }

        /**
         * 	render，渲染模板
         *
         * 	@param String $page 模板文件名
         * 	@param Array $params 其他模板变量键=>值数组
         *
         */
        public function render($page, $params = array()) {
                echo $this->fetch($page, $params);
        }

        /**
         * 	fetch，抓取模板
         *
         * 	@param String $page 模板文件名
         * 	@param Array $params 其他模板变量键=>值数组
         *
         */
        public function fetch($page, $params = array()) {
                foreach ($params as $varName => $value) {
                        $this->assign($varName, $value);
                }
                if (!$this->isViewCached($page)) {
                        $this->assign('breadcrumb', $this->getBreadcrumb($this->request->action));
                }
                $dir = str_replace('\\', '/', $this->smarty->template_dir[0]);
                if (!is_file($dir . $page)) {
                        return '模板不存在：' . substr($dir, strlen(KELA_ROOT)) . $page;
                }
                return $this->smarty->fetch($page, $this->cachePrefix());
        }
        
        /**
         * 操作日志写入
         * @param string $remark 备注信息
         * @param array $data array(
         * "pkdata"=>array(),
         * 'newdata'=>array(),
         * 'olddata'=>array(),
         * 'fields'=>array()
         * );
         */
        public function operationLog($type="update",$data=array()){
            $mod = $this->request->module;
            $con = $this->request->controller;
            $act = $this->request->action;
            $request_url = $this->request->userUrl;
            $remark = '';
            $type = strtolower($type);
        
            //修改操作记录
            if($type=="update"){
        
                $pkdata  = array();
                $newdata = array();
                $olddata = array();
                $fields  = array();
                if(isset($data['newdata']) && is_array($data['newdata'])){
                    $newdata = $data['newdata'];
                }
                if(isset($data['olddata']) && is_array($data['olddata'])){
                    $olddata = $data['olddata'];
                }
                if(isset($data['fields']) && is_array($data['fields'])){
                    $fields = $data['fields'];
                }
                if(isset($data['pkdata']) && is_array($data['pkdata'])){
                    $pkdata = $data['pkdata'];
                }
                
                if(!empty($newdata) && !empty($olddata) && !empty($pkdata)){
        
                    $pk_key   = current(array_keys($pkdata));
                    $pk_value = current($pkdata);
        
                    foreach($newdata as $key=>$vo){
        
                        if(isset($olddata[$key]) && $vo != $olddata[$key]){
                             
                            if(isset($fields[$key]) && count($fields[$key])<20){
                                $field_name = $fields[$key];
                            }else{
                                $field_name = $key;
                            }
                             
                            $remark.="[".$field_name."(".$key.")]由【".$olddata[$key]."】改成【".$vo."】,";
                        }
                    }
                    if($remark==''){
                        $remark ="对 {$pk_key}={$pk_value}的数据进行修改，数据未发生变化。";
                    }else{
                        $remark ="对 {$pk_key}={$pk_value}的数据进行修改，将".trim($remark,',')."。";
                    }
                }                
                
                //end $type=="update" 情况
            }
            //删除操作记录
            else if($type =="delete"){
                $pkdata  = array();
                if(isset($data['pkdata']) && is_array($data['pkdata'])){
                    $pkdata = $data['pkdata'];
                }
                if(!empty($pkdata)){
                    $pk_key   = current(array_keys($pkdata));
                    $pk_value = current($pkdata);
                    $remark ="对 {$pk_key}={$pk_value}的数据进行删除。";
                }
                //end $type=="delete" 情况
            }
            else if ($type == 'insert') {
            	/*AsyncDelegate::dispatch("opslog", array('event' => 'action_log', 'log_info' => array(
            			'module'=>$mod,
            			'controller'=>$con,
            			'action'=>$act,
            			'request_url'=>$request_url,
            			'remark'=>'新增',
            			'data'=>json_encode($data),
            			'create_user'=>Auth::$userName,
            			'ip'=>Util::getClicentIp(),
            			'create_time'=>date('Y-m-d H:i:s'),
            	)));*/
            	return;
            }
            else{
                return false;
            }
            if(isset($data['remark'])){
                $remark .= $data['remark'];
            }
            if($remark != ''){
                $saveData = array(
                    'module'=>$mod,
                    'controller'=>$con,
                    'action'=>$act,
                    'request_url'=>$request_url,
                    'remark'=>$remark,
                    'data'=>json_encode($data),
                    'create_user'=>Auth::$userName,
                    'ip'=>Util::getClicentIp(),
                    'create_time'=>date('Y-m-d H:i:s'),
                );
                $doamin = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'';
                if(!in_array($doamin,array('boss.kela.cn','zhanting.kela.cn'))){
                   $logModel = new UserOperationLogModel(1);
                   $logModel->saveData($saveData,array());
                }else{
                   //AsyncDelegate::dispatch("opslog", array('event' => 'action_log', 'log_info' => $saveData));
                }
            }else{
                return false;
            }
        }

}

?>