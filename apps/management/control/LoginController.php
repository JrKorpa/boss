<?php

/**
 *  -------------------------------------------------
 *   @file		: LoginController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: whc	 <18686607080@163.com>
 *   @date		:2014-5-23 下午12:06:00
 *   @update	:
 *  -------------------------------------------------
 */
class LoginController extends Controller {

        /**
         * 登陆页面
         */
        public function index($params) {
            if (defined('SSO')) {
                $login_url = Util::get_defined_array_var('SSO', 'login');
                Util::jump($login_url);
                exit;
            } else {
                $this->render("login.html");
            }
        }

        /**
         * 登陆信息验证
         */
        public function login() {
            if (defined('SSO')) {
                exit;
            } else {
                if (!Util::isAjax()) {
                        header("Location: /index.php?mod=management&con=Login&act=index");
                        exit;
                }
                $result = array("success" => 0, "msg" => "");

                $login_user = _Post::getString("account");
                $login_pwd = _Post::getString("pass");
                if(!isset($_COOKIE[Util::encrypt($login_user,AUTH_KEY)])){
                        setcookie(Util::encrypt($login_user,AUTH_KEY),1,0,'/',ROOT_DOMAIN);
                        $num=2;
                }
                else{
                        setcookie(Util::encrypt($login_user,AUTH_KEY),$_COOKIE[Util::encrypt($login_user,AUTH_KEY)]+1,0,'/',ROOT_DOMAIN);
                        $num=2-$_COOKIE[Util::encrypt($login_user,AUTH_KEY)];
                }

                

                if (!Util::is_username($login_user)) {
                        if($num<=0){
                                $result['msg'] = '该用户超过登陆尝试次数！';
                                Util::jsonExit($result);
                        }
                        $result['msg'] = '用户名非法，还有'.$num.'次尝试机会！';
                        SystemAccessLog::log(null, null, addslashes($login_user) . '=>' . addslashes($login_pwd), 1);
                        Util::jsonExit($result);
                }
                if (!Util::is_password($login_pwd)) {
                        if($num<=0){
                                $result['msg'] = '该用户超过登陆尝试次数！';
                                Util::jsonExit($result);
                        }
                        $result['msg'] = '密码非法，还有'.$num.'次尝试机会！';
                        SystemAccessLog::log(null, null, addslashes($login_user) . '=>' . addslashes($login_pwd), 1);
                        Util::jsonExit($result);
                }

                $model = new UserModel(1);
                $do = $model->getByAccount($login_user);
                if (empty($do['id']) || $do['is_deleted'] == 1) {
                        if($num<=0){
                                $result['msg'] = '该用户超过登陆尝试次数！';
                                Util::jsonExit($result);
                        }
                        $dd = new DictView(new DictModel(1)); //数据字典
                        $result['msg'] = $dd->getEnum('login_status', 1).'，还有'.$num.'次尝试机会！';
                        $result['type'] = 1;
                        $id = !empty($do['id']) ? $do['id'] : null;
                        SystemAccessLog::log($id, $result['type'], $login_user . '=>' . $login_pwd, 1);
                        Util::jsonExit($result);
                }

                $id = $do['id'];
                if ($do['is_enabled'] == 0) {
                        if($num<=0){
                                $result['msg'] = '该用户超过登陆尝试次数！';
                                Util::jsonExit($result);
                        }
                        $dd = new DictView(new DictModel(1)); //数据字典
                        $result['msg'] = $dd->getEnum('login_status', 2);
                        $result['type'] = 2;
                        SystemAccessLog::log($id, 2, '', 1);
                        Util::jsonExit($result);
                }

                $pwd = Util::xmd5($login_pwd);
                //123456  ==  e45782d588c0ac5ef738f45763efe1d1
                //123Abc  ==  6248883015fef52f23785ab26e187635
                //123Qwe  ==  9c97283d6888f22874a5203650b87e5b
                //4008980188  ==  83bc4dc571b3456378af740dbef53d9d
                if ($pwd !== $do['password']) {
                        if($num<=0){
                                $result['msg'] = '该用户超过登陆尝试次数！';
                                Util::jsonExit($result);
                        }
                        $dd = new DictView(new DictModel(1)); //数据字典
                        $result['msg'] = $dd->getEnum('login_status', 3).'，还有'.$num.'次尝试机会！';
                        $result['type'] = 3;
                        SystemAccessLog::log($id, 3, '', 1);
                        Util::jsonExit($result);
                }

                SystemAccessLog::log($id, 4, '', 1);
                DBSessionHandler::setAdminId($do['id']);
//                $_SESSION = null;
                setcookie(Util::encrypt($login_user,AUTH_KEY),'',-1,'/',ROOT_DOMAIN);
                $auth = Auth::getInstance();
                $auth::$userId = $do['id'];
                $auth::$userName = $do['account'];
				$auth::$companyId = empty( $do['company_id'] ) ? '-1' : $do['company_id'];
                $auth::$realName = $do['real_name'];
                $auth::$userType = $do['user_type'];
                $auth::$isHouseKeeper = $do['is_warehouse_keeper'];
                $auth::$isChannelKeeper = $do['is_channel_keeper'];
                $auth::$lastModify = $do['up_pwd_date'];

                $auth::$qudao = $do['qudao'];
                $auth::$bumen = $do['bumen'];

                $auth::setLoginCookies();

                $userWare = new UserWarehouseModel(1);
                $userWareList = $userWare->getWareListNew($do['id']);
                $_SESSION['userWareList'] = implode(',', array_column($userWareList, 'house_id'));
                if (count($userWareList)) {
                        $_SESSION['userWareNow'] = $userWareList[0]['house_id'];
                }

                $result['success'] = 1;
                if ($do['up_pwd_date']) {
                        $date = floor((time() - $do['up_pwd_date']) / 86400);
                        if (USER_UPDATE_TIME <= $date) {
                //                $result['success'] = 2;
                        }
                }
                //sleep(1);//给会话存储和cookie设置留有时间，防止二次登录
                      Util::jsonExit($result);
            }
        }

        /**
         * logout
         * 用户退出系统 清空变量 清空cookie，释放session
         */
        public function logout($params) {
            if (defined('SSO')) {
                $user_id = isset($_SESSION['userId'])?$_SESSION['userId']:'';
            	if (!empty($_SESSION['userId']) || !empty($_SESSION['userName'])) {
                    $auth = Auth::getInstance();
                    $auth->unsetLoginCookie();
                    
                    $session_id = DBSessionHandler::getSessionId();
                    DBSessionHandler::destroy($session_id);
                }
                if($user_id){
                    SystemAccessLog::log($user_id, 5, '', 1);
                }
                $logout_url = Util::get_defined_array_var('SSO', 'logout');
                header("Location:".$logout_url);
                exit;
            } else {
                if (empty($_SESSION['userId'])) {
                        header("Location: /index.php?mod=management&con=Login&act=index");
                        exit;
                }
                SystemAccessLog::log($_SESSION['userId'], 5, '', 1);
                $session_id = DBSessionHandler::getSessionId();
                $auth = Auth::getInstance();
                $auth::$userId = "";
                $auth::$userName = "";
				$auth::$companyId = "";
                $auth::$realName = "";
                $auth::$userType = "";
                $auth->unsetLoginCookie();
                DBSessionHandler::destroy($session_id);
                header("Location: /index.php?mod=management&con=Login&act=index");
                exit;
            }
        }

        /* 定期修改密码页面 */

        public function userModifyPass() {
                if (!isset(Auth::$userId) || empty(Auth::$userName)) {
                        header("Location: /index.php?mod=management&con=Login&act=index");
                        exit;
                }
                $this->render("loginpassword.html", array('date' => USER_UPDATE_TIME, 'url' => _Get::getString('url')));
        }

        /* 定期修改密码提交数据 */

        public function userModifyPassPost() {
                if (!Util::isAjax()) {
                        header("Location: /index.php?mod=management&con=Login&act=index");
                        exit;
                }
                $result = array('success' => 0, 'error' => '');
                if(!isset($_SESSION['userId']))
                {
                        header("Location: /index.php?mod=management&con=Login&act=index");
                        exit;                        
                }
                $uid = $_SESSION['userId'];
                $pwd_type = _Post::getInt('paw_type');
                $oldPass = _Post::get('oldPass');
                $newPass = _Post::get('newPass');
                $confrimPass = _Post::get('confrimPass');
                $url = _Post::getString('url', Util::getDomain());
                if (!Util::is_password($oldPass)) {
                        $result['error'] = "密码非法！";
                        Util::jsonExit($result);
                }
                if (!Util::is_password($newPass)) {
                        $result['error'] = "密码非法！";
                        Util::jsonExit($result);
                }
                if (!Util::is_password($confrimPass)) {
                        $result['error'] = "密码非法！";
                        Util::jsonExit($result);
                }

                if (empty($pwd_type) || $pwd_type < 3) {
                        $result['error'] = "密码中必须包含大写字母小写字母和数字";
                        Util::jsonExit($result);
                }
                if ($oldPass === $newPass) {
                        $result['error'] = "新密码和旧密码不能一样";
                        Util::jsonExit($result);
                }
                if (strlen($newPass) < 6) {
                        $result['error'] = "新密码必须为6位以上";
                        Util::jsonExit($result);
                }
                if ($newPass !== $confrimPass) {
                        $result['error'] = "两次密码不一致";
                        Util::jsonExit($result);
                }
                $model = new UserModel($uid, 1);
                $olddo = $model->getDataObject();
                $pass = $olddo['password'];
                $pwd = Util::xmd5($oldPass);
                if ($pwd !== $pass) {
                        $dd = new DictView(new DictModel(1)); //数据字典
                        $result['error'] = $dd->getEnum('login_status', 3);
                        Util::jsonExit($result);
                }
                $newdo['id'] = $uid;
                $newdo['password'] = Util::xmd5($newPass);
                if($olddo['up_pwd_date'])
                {
                       $newdo['up_pwd_date'] = time(); 
                }

                $data = $model->saveData($newdo, $olddo);
                if ($data !== false) {
                        $result['success'] = 1;
                        $result['url'] = $url;
                        SystemAccessLog::log($_SESSION['userId'], 6, '', 1);
                        setcookie(Util::encrypt("lastModify",AUTH_KEY),Util::encrypt($newdo['up_pwd_date'],AUTH_KEY),0,'/',ROOT_DOMAIN);
//                        $session_id = DBSessionHandler::getSessionId();
//                        DBSessionHandler::destroy($session_id);
//                        $auth = Auth::getInstance();
//                        $auth::$userId = "";
//                        $auth::$userName = "";
//                        $auth::$realName = "";
//                        $auth::$userType = "";
//                        // $auth::$token = "";
//                        $auth->unsetLoginCookie();
                } else {
                        $result['error'] = "服务器未响应，请稍后重试！";
                }
                Util::jsonExit($result);
        }

        /**
         * 	邮件找回密码校验
         */
        public function postRetrievePwd() {
                if (!Util::isAjax()) {
                        header("Location: /index.php?mod=management&con=Login&act=index");
                        exit;
                }
                $result = array('success' => 0, 'error' => '');
                $user_name = _Post::get('user_name');
                $email = _Post::get('email');
                if (!Util::is_username($user_name)) {
                        $result['error'] = "用户名非法！";
                        Util::jsonExit($result);
                }
                if (!Util::isEmail($email)) {
                        $result['error'] = "邮箱非法！";
                        Util::jsonExit($result);
                }

                $model = new UserModel(1);
                $do = $model->getByAccount($user_name);

                if (empty($do) || $do['is_deleted'] == 1) {
                        $dd = new DictView(new DictModel(1)); //数据字典
                        $result['error'] = $dd->getEnum('login_status', 1);
                        Util::jsonExit($result);
                }
                if ($do['email'] !== $email) {
                        $dd = new DictView(new DictModel(1)); //数据字典
                        $result['error'] = $dd->getEnum('login_status', 7);
                        Util::jsonExit($result);
                }

                //返回地址
                $relurl = _Post::getString('url',Util::getDomain());
                $result['success'] = 1;
                $result['url'] = $relurl;
                $result['user_name'] = $user_name;
                $result['email'] = $email;
                Util::jsonExit($result);
        }

        /**
         * 	发送找回密码邮件
         */
        public function retrievePwdEmail() {
                if (!Util::isAjax()) {
                        header("Location: /index.php?mod=management&con=Login&act=index");
                        exit;
                }
                $result = array('success' => 0, 'error' => '');
                $user_name = _Post::get('user_name');
                $email = _Post::get('email');
                $relurl = _Post::getString('url',Util::getDomain());
                if (empty($user_name) || empty($email)) {
                        die();
                }
                if (!Util::is_username($user_name)) {
                        $result['error'] = "用户名非法！";
                        Util::jsonExit($result);
                }
                if (!Util::isEmail($email)) {
                        $result['error'] = "邮箱非法！";
                        Util::jsonExit($result);
                }
                $model = new UserModel(1);
                $do = $model->getByAccount($user_name);
                $getpasstime = time();
                $str = $do['id'] . $do['account'] . $do['email'] . $getpasstime;
                $token = md5($str); //组合验证码

                $url = Util::getDomain() . "/index.php?mod=management&con=login&act=updateRetrievePassword&username=" . urlencode($do['account']) . "&token=" . $token . "&time=" . $getpasstime . "&url=" . base64_encode($relurl);
                $emailbody = "亲爱的" . $email . "：<br/>您在" . date('Y-m-d H:i') . "提交了找回密码请求。请点击下面的链接重置密码（链接10分钟内有效）。<br/><a href='" . $url . "'target='_blank'>" . $url . "</a>";
                $mail = new PHPMailer;
                $mail->Charset="UTF-8";
                $relsmtp = $mail->send_mail($user_name, $email, '系统密码找回', $emailbody);
                $result['success'] = $relsmtp ? 1 : 0;
                Util::jsonExit($result);
        }

        /**
         * 	邮件重置密码页面
         */
        public function updateRetrievePassword() {
                $username = _Get::getString('username');
                $token = _Get::getString('token');
                $time = _Get::getInt('time');
                $model = new UserModel(1);
                if (!Util::is_username($username)) {
                        die("用户名非法！");
                }

                $do = $model->getByAccount($username);
                if (empty($do)) {
                        Util::alert('数据错误');
                        header("Location: /index.php?mod=management&con=Login&act=index");
                        exit;
                }
                $token_data = md5($do['id'] . $username . $do['email'] . $time); //组合验证码
                if ($token_data !== $token) {
                        Util::alert('令牌验证错误,请重新操作.');
                        header("Location: /index.php?mod=management&con=Login&act=index");
                        exit;
                }
                if (time() > ($time + 60 * 1000)) {
                        Util::alert('修改密码已经超时,请重新操作。');
                        header("Location: /index.php?mod=management&con=Login&act=index");
                        exit;
                }
                $this->render("update_retrieve_password.html", array('uesername' => $username,
                        'token' => $token_data,
                        'id' => $do['id'],
                        'time' => $time,
                        'url' => _Get::getString('url')
                ));
        }

        /**
         * 	邮件重置密码
         */
        public function postUpRetrievePassword() {
                if (!Util::isAjax()) {
                        header("Location: /index.php?mod=management&con=Login&act=index");
                        exit;
                }

                $pwd_type = _Post::getInt('paw_type');
                $uesername = _Post::get('uesername');
                $token = _Post::getString('token');
                $id = _Post::get('id');
                $time = _Post::getInt('time');
                $newPass = _Post::get('newPass');
                $confrimPass = _Post::get('confrimPass');
                $url = _Post::getString('url');

                if (!Util::is_password($newPass)) {
                        $result['error'] = "密码非法！";
                        Util::jsonExit($result);
                }
                if (!Util::is_password($confrimPass)) {
                        $result['error'] = "密码非法！";
                        Util::jsonExit($result);
                }

                if (empty($pwd_type) || $pwd_type < 3) {
                        $result['error'] = "密码中必须包含大写字母小写字母和数字";
                        Util::jsonExit($result);
                }
                if (strlen($newPass) < 6) {
                        $result['error'] = "新密码必须为6位以上";
                        Util::jsonExit($result);
                }
                if ($newPass !== $confrimPass) {
                        $result['error'] = "密码不一致！";
                        Util::jsonExit($result);
                }
                $model = new UserModel($id, 1);
                $do = $model->getByAccount($uesername);

                $str = $do['id'] . $uesername . $do['email'] . $time;
                $token_data = md5($str); //组合验证码

                if ($token_data !== $token) {
                        $result['error'] = "令牌验证错误，请重新操作。";
                        Util::jsonExit($result);
                }

                $newdo['id'] = $do['id'];
                $newdo['password'] = Util::xmd5($newPass);
                if($do['up_pwd_date'])
                {
                       $newdo['up_pwd_date'] = time(); 
                }

                $data = $model->saveData($newdo, $do);
                if ($data !== false) {
                        $result['success'] = 1;
                        $result['url'] = base64_decode($url);
                } else {
                        $result['error'] = "服务器未响应，请稍后重试！";
                }
                Util::jsonExit($result);
        }
        
        public function postLogin() {   
        	
        	$virgin = _Request::getString('virgin');

            $user_id = isset($_SESSION['userId'])?$_SESSION['userId']:'';
            if($user_id){
                SystemAccessLog::log($user_id, 4, '', 1);
            }
            
        	if (empty($virgin)) {
        		// 确保当前不存在任何用户信息
        		if (isset($_SESSION['userId']) || isset($_SESSION['userName'])) {
        			$auth = Auth::getInstance();
        			$auth->unsetLoginCookie();
        			
        			$session_id = DBSessionHandler::getSessionId();
        			DBSessionHandler::destroy($session_id);
        		}
        		
        		// 清理之前的cookie
        		foreach ($_COOKIE as $k => $v) {
        			if ($k == 'xlu') continue;
        			setcookie($k, null);
        		}
        		
        		header('Location:'.$_SERVER['REQUEST_URI'].'&virgin=1');
        		exit;
        	}
            $token = _Request::getString('__klc_001');
            if (!empty($token)) {
            	DBSessionHandler::getSessionId();
            	Auth::saveSsoToken($token);
            	
            	Auth::checkLogin($token);
            }

            header('Location: /index.php');
            exit;
        }
}

?>