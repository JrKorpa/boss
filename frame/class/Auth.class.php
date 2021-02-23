<?php

/**
 *  -------------------------------------------------
 *   @file		: Auth.class.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-05-28
 *   @update	:
 *  -------------------------------------------------
 */


/**
 *  -------------------------------------------------
 *	Auth，身份网关认证
 *
 *  -------------------------------------------------
 */

class Auth
{
	
	protected static $auth = NULL;
	
	
	public static $userId = 0;
	
	public static $userName = "";
	
	public static $realName = '';
	
	public static $userType = 1000;
	
	public static $isHouseKeeper =0;
	
	public static $isChannelKeeper = 0;
	
	public static $lastModify = 0;
	
	public static $qudao='';
	
	public static $bumen='';
	
	public static $companyId=0;
	
	
	public static $menus = array();
	//左	侧动态菜单树
	public static $menu_p = array();
	//菜	单权限
	public static $menu_ps = array();
	//菜	单权限1
	// 	public static $menu_type = array();
	//菜	单类型
	
	public static $operation_p = array();
	//主	、独立对象操作权限
	public static $operation_ps = array();
	//主	、独立对象操作权限1
	
	public static $button_p = array();
	//按	钮权限
	public static $button_ps = array();
	//按	钮权限1
	public static $buttons = array();
	//按	钮权限2
	
	public static $bars = array();
	//列	表页工具条
	public static $_bars = array();
	//查	看页工具条
	public static $__bars = array();
	//明	细工具条
	
	public static $scope = array();
	//属	性控制
	
	
	/**
	 *	getInstance，获取auth实例
	 */
	
	public static function getInstance()
	{
		
		if (self::$auth == null)
		{
			
			self::$auth = new self();
			
			if (!empty($_COOKIE[Util::decrypt('userId', AUTH_KEY)]))
			{
				
				if($_SERVER['REQUEST_URI']!='/index.php?mod=management&con=login&act=logout' && $_SERVER['REQUEST_URI']!='/index.php?mod=management&con=login&act=index'){
					
					self::initAttrs();
					
				}
				
			}
			
		}
		
		return self::$auth;
	}
	
	
	
	/**
	 *	initAttrs，初始化变量
	 */
	
	public  static function initAttrs()
	{
		if($_SERVER['REQUEST_URI']=='/index.php?mod=management&con=login&act=logout' || $_SERVER['REQUEST_URI']=='/index.php?mod=management&con=login&act=index'){
			return;
		}
		
		self::$userId = self::getValFromEnv("userId", 0);
		
		self::$companyId = self::getValFromEnv("companyId", 0);
		
		self::$userName = self::getValFromEnv("userName",'');
		
		self::$realName = self::getValFromEnv("realName", '');
		
		self::$userType = self::getValFromEnv("userType", '');
		
		self::$isHouseKeeper = self::getValFromEnv("isHouseKeeper", 0);
		
		self::$isChannelKeeper = self::getValFromEnv("isChannelKeeper", 0);
		
		self::$lastModify = self::getValFromEnv("lastModify", 0);
		
		self::$menu_p = self::getMenuPermissions();
		//初	始化菜单权限
		//self::$menu_ps = self::getMenuPermissions1();
		
		self::$operation_p = self::getOperationPermissions();
		//初始化操作权限
		//self::$operation_ps = self::getOperationPermissions1();
		
		self::$button_p = self::getButtonPermissions();
		//初始化按钮权限
		//self::$buttons = self::getButtonPermissions1();
		
		//self::$button_ps = self::getButtonPermissions2();
		self::$scope = self::getScopes();
		//初	始化属性控制
	}
	
	
	/**
	 *	checkLogin，判断是否登录
	 *
	 *	判断ip,browser,sessionid,token,userid是否一致，一致为登录，不一致为非法登录
	 *
	 *	@return boolean
	 */
	
	public static function checkLogin($token ='')
	{    
	    if (defined('SSO')) {
	        $token = empty($token) ? self::getValFromEnv('bosso') : $token;
	        if (empty($token)) return false;
	        
	        self::initAttrs();
	        
	        $sso_config = Util::get_defined_array_var('SSO');
	        $freq_check_token_inm = $sso_config['freq_check_token_inm'];
	        $last_check_time = self::getValFromEnv('bosso_last_ck', 0); 
	        
	        $pre_login = empty(self::$userId) || empty(self::$userName);
	        $time_flush_since_last_check = $pre_login ? 0 : ceil((time() - $last_check_time) / 60);
	        
	        if ($pre_login || $time_flush_since_last_check >= $freq_check_token_inm || (mt_rand(1, 11) % 3) == 0) {
	        	
	            $check_url = $sso_config['checkout'];
	            $check_url .= $token;
	            $resp = Util::httpCurl($check_url);

	            setcookie(Util::encrypt("bosso_last_ck",AUTH_KEY),Util::encrypt(time(),AUTH_KEY),0,'/',ROOT_DOMAIN);
	            
	            if (empty($resp)) {
	            	if ($pre_login) return false;
	            	// 如果不是第一次登录, 连续3次调用失败，则认为token无效, 否则暂时忽略该失败调用
	            	if ($time_flush_since_last_check >= $freq_check_token_inm * 3) return false;
	            	return true;
	            }
	            
	            $resp = json_decode($resp, true);
	            if (!isset($resp['status'])) {
	            	if ($pre_login) return false;
	            	// 如果不是第一次登录, 连续3次调用失败，则认为token无效, 否则暂时忽略该失败调用
	            	if ($time_flush_since_last_check >= $freq_check_token_inm * 3) return false;
	            	return true;
	            } else if ($resp['status'] <> 1) {
	            	// 调用成功，但token验证无效
	            	return false;
	            }
	             
	            if ($pre_login) {
	                $lite_user = $resp['data']['user'];
	                
	                $sql = "SELECT group_concat(distinct `cs`.`channel_id`) as `qudao`,group_concat(distinct `o`.`dept_id`) as `bumen`,`u`.* FROM `user` as `u` LEFT JOIN `organization` as `o` ON `u`.`id`=`o`.`user_id` LEFT JOIN `user_channel` as `cs` ON `u`.`id`=`cs`.`user_id` WHERE `u`.`account`='{$lite_user['account']}' limit 1 ";
	                $user = DB::cn(1)->getRow($sql);
                
	                self::$userId = $user['id'];
	                self::$companyId = $user['company_id'];
	                self::$userName = $user['account'];
	                self::$realName = $user['real_name'];
	                self::$userType = $user['user_type'];
	                self::$isHouseKeeper = $user['is_warehouse_keeper'];
	                self::$isChannelKeeper = $user['is_channel_keeper'];
	                self::$lastModify = $user['up_pwd_date'];
	                self::$qudao = $user['qudao'];
	                self::$bumen = $user['bumen'];
	                
	                $sql = "SELECT distinct `w`.`house_id` FROM `user_warehouse` AS `w` WHERE `w`.`user_id`='" . $user['id']. "'";
	                $userWareList = DB::cn(1)->getAll($sql);
	                $_SESSION['userWareList'] = implode(',', array_column($userWareList, 'house_id'));
	                if (count($userWareList)) {
	                	$_SESSION['userWareNow'] = $userWareList[0]['house_id'];
	                }
	                
	                self::setLoginCookies();
	            }
	            
	            if (isset($resp['data']['token'])) {
	                self::saveSsoToken($resp['data']['token']);
	            }
	        }
	        
	        return true;
	    } else {
    		self::initAttrs();
    		
    		$cookiesessionid = DBSessionHandler::getSessionId();
    		if (!empty($cookiesessionid)){
    			if(empty(self::$userId) || empty(self::$userName)) {
    				return false;
    			}
    			
    			return true;
    		}
    		
    		return false;
	    }
	}
	
	/**
	 *	setLoginCookies，初始化cookie、session
	 */
	
	public static function setLoginCookies()
	{
		setcookie(Util::encrypt("isHouseKeeper",AUTH_KEY),Util::encrypt(self::$isHouseKeeper,AUTH_KEY),0,'/',ROOT_DOMAIN);
		
		setcookie(Util::encrypt("isChannelKeeper",AUTH_KEY),Util::encrypt(self::$isChannelKeeper,AUTH_KEY),0,'/',ROOT_DOMAIN);
		
		setcookie(Util::encrypt("lastModify",AUTH_KEY),Util::encrypt(self::$lastModify,AUTH_KEY),0,'/',ROOT_DOMAIN);
		
		$_SESSION['userId'] = self::$userId;
		
		$_SESSION['companyId'] = self::$companyId;
		
		$_SESSION['qudao'] = self::$qudao;
		
		$_SESSION['bumen']=self::$bumen;
		
		$_SESSION['userName'] = self::$userName;
		
		$_SESSION['realName'] = self::$realName;
		
		$_SESSION['userType'] = self::$userType;
	}
	
	
	
	/**
        * unsetLoginCookie
        *  清空cookie，释放session
        */
	
	public function unsetLoginCookie()
	{
		setcookie(Util::encrypt("isHouseKeeper",AUTH_KEY),'',0,'/',ROOT_DOMAIN);
		
		setcookie(Util::encrypt("isChannelKeeper",AUTH_KEY),'',0,'/',ROOT_DOMAIN);
		
		setcookie(Util::encrypt("lastModify",AUTH_KEY),'',0,'/',ROOT_DOMAIN);
		
		if (defined('SSO')) {
			$key = Util::encrypt("bosso",AUTH_KEY);
			unset($_COOKIE[$key]);
		    setcookie($key, null,time() - 3600,'/',ROOT_DOMAIN);
		    
		    $ck_key = Util::encrypt("bosso_last_ck",AUTH_KEY);
		    unset($_COOKIE[$ck_key]);
		    setcookie($ck_key,null,time()-3600,'/',ROOT_DOMAIN);
		}
		
		session_destroy();
	}

	
	/**
	 *	获取动态菜单树，0.0010-
	 */
	
	public static function getLeftTree ($uid)
	{
		
		if(!$uid)
		{
			
			return array();
			
		}
		
		if(empty(self::$userId) || self::$userId != $uid)
		{
			
			Util::jump("/index.php?mod=management&con=Login&act=index");
			
			exit;
			
		}
		
		if(self::$userType==1)
		{
			$sql ="SELECT `b`.`name`,`a`.`label` AS `app_name`,`m`.`application_id`,`g`.`label` AS `g_name`,`m`.`group_id`,`m`.`id`,`m`.`display_order`,`m`.`label`,`m`.`url`,`a`.`display_order` AS `app_order`,`g`.`display_order` AS `g_order`,`bb`.`name` AS `app_icon`,`bbb`.`name` AS `g_icon`,`m`.`is_out` FROM `menu` AS `m` INNER JOIN `menu_group` AS `g` ON `g`.`id`=`m`.`group_id` AND `g`.`is_deleted`=`m`.`is_deleted` INNER JOIN `application` AS `a` ON `a`.`id`=`m`.`application_id` AND `a`.`is_deleted`=`m`.`is_deleted` LEFT JOIN `button_icon` AS `b` ON `b`.`id`=`m`.`icon` LEFT JOIN `button_icon` AS `bb` ON `bb`.`id`=`a`.`icon` LEFT JOIN `button_icon` AS `bbb` ON `bbb`.`id`=`g`.`icon` WHERE `m`.`is_deleted`='0' AND `m`.`is_enabled`='1' AND `a`.`is_enabled`=1 ORDER BY `a`.`display_order` DESC,`g`.`display_order` DESC,`m`.`display_order` DESC ";
		}
		else
		{
			$sql = "(SELECT `b`.`name`,`a`.`label` AS `app_name`,`m`.`application_id`,`g`.`label` AS `g_name`,`m`.`group_id`,`m`.`id`,`m`.`display_order`,`m`.`label`,`m`.`url`,`a`.`display_order` AS `app_order`,`g`.`display_order` AS `g_order`,`bb`.`name` AS `app_icon`,`bbb`.`name` AS `g_icon`,`m`.`is_out` FROM `user_menu_permission` AS `u` INNER JOIN `permission` AS `p` ON `u`.`permission_id`=`p`.`id` INNER JOIN `menu` AS `m` ON `p`.`resource_id`=`m`.`id` AND `p`.`is_deleted`=`m`.`is_deleted` INNER JOIN `menu_group` AS `g` ON `g`.`id`=`m`.`group_id` AND `g`.`is_deleted`=`m`.`is_deleted` INNER JOIN `application` AS `a` ON `a`.`id`=`m`.`application_id` AND `a`.`is_deleted`=`m`.`is_deleted` LEFT JOIN `button_icon` AS `b` ON `b`.`id`=`m`.`icon` LEFT JOIN `button_icon` AS `bb` ON `bb`.`id`=`a`.`icon` LEFT JOIN `button_icon` AS `bbb` ON `bbb`.`id`=`g`.`icon` WHERE `u`.`user_id`='{$uid}' AND `m`.`type`='1' AND `m`.`is_deleted`='0' AND `m`.`is_enabled`='1' AND `a`.`is_enabled`=1 AND `p`.`type`='1' ORDER BY `a`.`display_order` DESC,`g`.`display_order` DESC,`m`.`display_order` DESC)";
			if(self::$isChannelKeeper || self::$isHouseKeeper)
			{
				//					$sql .= " UNION ALL (SELECT `b`.`name`,`a`.`label` AS `app_name`,`m`.`application_id`,`g`.`label` AS `g_name`,`m`.`group_id`,`m`.`id`,`m`.`display_order`,`m`.`label`,`m`.`url`,`a`.`display_order` AS `app_order`,`g`.`display_order` AS `g_order`,`bb`.`name` AS `app_icon`,`bbb`.`name` AS `g_icon` FROM `user_extend_menu` AS `u` INNER JOIN `permission` AS `p` ON `u`.`permission_id`=`p`.`id` INNER JOIN `menu` AS `m` ON `p`.`resource_id`=`m`.`id` AND `p`.`is_deleted`=`m`.`is_deleted` INNER JOIN `menu_group` AS `g` ON `g`.`id`=`m`.`group_id` AND `g`.`is_deleted`=`m`.`is_deleted` INNER JOIN `application` AS `a` ON `a`.`id`=`m`.`application_id` AND `a`.`is_deleted`=`m`.`is_deleted` LEFT JOIN `button_icon` AS `b` ON `b`.`id`=`m`.`icon` LEFT JOIN `button_icon` AS `bb` ON `bb`.`id`=`a`.`icon` LEFT JOIN `button_icon` AS `bbb` ON `bbb`.`id`=`g`.`icon` WHERE `u`.`user_id`='{$uid}' AND `u`.`type`='3' AND `m`.`type`='3' AND `m`.`is_deleted`='0' AND `m`.`is_enabled`='1' AND `a`.`is_enabled`=1 AND `p`.`type`='1' ORDER BY `a`.`display_order` DESC,`g`.`display_order` DESC,`m`.`display_order` DESC)";
				$sql .= " UNION ALL (SELECT `b`.`name`,`a`.`label` AS `app_name`,`m`.`application_id`,`g`.`label` AS `g_name`,`m`.`group_id`,`m`.`id`,`m`.`display_order`,`m`.`label`,`m`.`url`,`a`.`display_order` AS `app_order`,`g`.`display_order` AS `g_order`,`bb`.`name` AS `app_icon`,`bbb`.`name` AS `g_icon`,`m`.`is_out` FROM `user_extend_menu` AS `u` INNER JOIN `permission` AS `p` ON `u`.`permission_id`=`p`.`id` INNER JOIN `menu` AS `m` ON `p`.`resource_id`=`m`.`id` AND `p`.`is_deleted`=`m`.`is_deleted` INNER JOIN `menu_group` AS `g` ON `g`.`id`=`m`.`group_id` AND `g`.`is_deleted`=`m`.`is_deleted` INNER JOIN `application` AS `a` ON `a`.`id`=`m`.`application_id` AND `a`.`is_deleted`=`m`.`is_deleted` LEFT JOIN `button_icon` AS `b` ON `b`.`id`=`m`.`icon` LEFT JOIN `button_icon` AS `bb` ON `bb`.`id`=`a`.`icon` LEFT JOIN `button_icon` AS `bbb` ON `bbb`.`id`=`g`.`icon` WHERE `u`.`user_id`='{$uid}' AND `m`.`is_deleted`='0' AND `m`.`is_enabled`='1' AND `a`.`is_enabled`=1 AND `p`.`type`='1' ORDER BY `a`.`display_order` DESC,`g`.`display_order` DESC,`m`.`display_order` DESC)";
			}
		}
		
		$data = DB::cn(1)->getAll($sql);
		
		if(!$data)
		{
			return array();
		}
		
		// 待处理预约数量提醒
		$bespoke_todo_count = self::getTodoCount();
		
		$_SESSION['bespoke_todo_count'] = $bespoke_todo_count;
		
		$datas = array();
		
		foreach ($data as $key => $val )
		{
			if(!isset($datas[$val['application_id']]))
			{
				$tmp = array();
				
				$tmp['id'] = $val['application_id'];
				
				$tmp['display_order'] = $val['app_order'];
				
				$tmp['icon'] = $val['app_icon'];
				
				$tmp['label'] = $val['app_name'];
				
				$datas[$val['application_id']] = $tmp;
			}
			
			if(!isset($datas[$val['application_id']]['son'][$val['group_id']]))
			{
				$tmp = array();
				
				$tmp['id'] = $val['group_id'];
				
				$tmp['display_order'] = $val['g_order'];
				
				$tmp['icon'] = $val['g_icon'];
				
				$tmp['label'] = $val['g_name'];
				
				$datas[$val['application_id']]['son'][$val['group_id']] = $tmp;
			}
			
			$tmp = array();
			
			$tmp['id'] = $val['id'];
			
			$tmp['display_order'] = $val['display_order'];
			
			$tmp['icon'] = $val['name'];
			
			$tmp['label'] = $val['label'];
			
			$tmp['url'] = $val['url'];
			
			$tmp['is_out'] = $val['is_out'];
			
			$datas[$val['application_id']]['son'][$val['group_id']]['son'][$val['id']] = $tmp;
		}
		
		if(self::$userType==1)
		{
			return $datas;
		}
		
		$orderData = array();
		
		foreach ($datas as $val1 )
		{
			$tmp = $val1;
			
			unset($tmp['son']);
			
			$orderData[$val1['display_order']] = $tmp;
			
			foreach ($val1['son'] as $val2 )
			{
				$tmp = $val2;
				
				unset($tmp['son']);
				
				$orderData[$val1['display_order']]['son'][$val2['display_order']] = $tmp;
				
				foreach ($val2['son'] as $val3 )
				{
					
					$tmp = $val3;
					
					unset($tmp['son']);
					
					$orderData[$val1['display_order']]['son'][$val2['display_order']]['son'][$val3['display_order']] = $tmp;
				}
			}
		}
		
		krsort($orderData);
		
		foreach ($orderData as $k1 => $v1 )
		{
			
			krsort($orderData[$k1]['son']);
			
			foreach ($v1['son'] as $k2 => $v2 )
			{
				krsort($orderData[$k1]['son'][$k2]['son']);
			}
		}
		
		return $orderData;
	}
	
	// 	add by gengchao start, 查询未处理预约数量
	private static function getTodoCount() {
		
		// 		所有店铺人员
		$HB = DB::cn(1)->getAll("select id,dp_leader_name,dp_people_name from sales_channels_person");
		
		$HBleader = array_column($HB,'dp_leader_name','id');
		
		// 		当前用户属于某些店的店长
		$dz_shop_ids = array();
		
		foreach($HBleader as $shop_id=>$strNames) {
			
			if (in_array(Auth::getValFromEnv('userName', '-'), explode(',', $strNames))) {
				
				$dz_shop_ids[$shop_id] = $strNames;
				
			}
			
		}
		
		
		$is_dz = false;
		// 		是否店长
		$qudao = explode(',',Auth::getValFromEnv('qudao', '-1'));
		
		if($dz_shop_ids){
			
			$channel = '';
			
			foreach($dz_shop_ids as $shop_id=>$strNames){
				
				if(in_array($shop_id, $qudao)){
					
					$channel .= $shop_id.",";
					
				}
				
			}
			
			$is_dz=true;
			
			$channel = trim($channel, ",");
			
		}
		else{
			
			$channel = Auth::getValFromEnv('qudao', '-1');
			
		}
		
		if (intval(Auth::getValFromEnv('userType', 0)) == 1) {
			
			$channel = '';
			
			$is_dz = false;
			
		}
		
		
		$start_date = date('Y-m-d', strtotime('-29days', strtotime(date('Y-m-d'))));
		
		$end_date = date('Y-m-d', strtotime('+1days', strtotime(date('Y-m-d'))));
		
		$sql = "select count(1) from app_bespoke_info where bespoke_status in (1,2) and re_status=2
            and bespoke_inshop_time>='{$start_date}' and bespoke_inshop_time<='{$end_date}'";
		
		$is_leader = intval($is_dz);
		
		$sql.=" AND (accecipt_man='".Auth::getValFromEnv('userName', '-')."' or (accecipt_man='' and {$is_leader}))";
		
		if (!empty($channel)) {
			
			$sql.=" AND `department_id` in (".$channel.")";
			
		}
		
		$bespoke_todo_count = DB::cn(17)->getOne($sql);
		
		return $bespoke_todo_count;
		
	}
	
	
	
	/**
	 *	读取菜单权限,将子对象也虚拟为菜单
	 */
	
	private static function getMenuPermissions ()
	{
		
		if(!self::$userId) return null;
		
		if(self::$userType  == 1){
			$_SESSION['__menu_p'] = array();
			return array();
		}
		
		if(isset($_SESSION['__menu_p']))
		{
			return $_SESSION['__menu_p'];
		}
		
		$sql = "(SELECT permission_id,0 AS `source_id`,1 AS `type` FROM `user_menu_permission` WHERE `user_id`='".self::$userId."')";
		
		$sql .= " UNION ALL (SELECT permission_id,0 AS `source_id`,1 AS `type` FROM `user_subdetail_permission` WHERE `user_id`='".self::$userId."')";
		
		if(self::$isChannelKeeper || self::$isHouseKeeper)
		{
			
			$sql .= " UNION ALL (SELECT permission_id,source_id,type FROM `user_extend_menu` WHERE `user_id`='".self::$userId."')";
			
			$sql .= " UNION ALL (SELECT permission_id,source_id,type FROM `user_extend_subdetail` WHERE `user_id`='".self::$userId."')";
			
		}
		
		$sql = "SELECT DISTINCT p.id,p.code,u.source_id,u.type FROM (".$sql.") AS u INNER JOIN `permission` AS p ON u.permission_id=p.id WHERE p.is_deleted=0 ";
		
		$result = DB::cn(1)->getAll($sql);
		
		if(!$result)
		{
			$_SESSION['__menu_p'] = array();
			
			return array();
		}
		
		
		$data = array();
		
		if($result)
		{
			foreach ($result as $key => $val )
			{
				$data[$val['type']][$val['source_id']][$val['id']] = strtoupper($val['code']);
			}
			
			if(!self::$isHouseKeeper)
			{
				unset($data[2]);
			}
			
			if(!self::$isChannelKeeper)
			{
				unset($data[3]);
			}
			
		}
		
		if(self::$userType>1)
		{
			$_SESSION['__menu_p'] = $data;
		}

		return $data;
	}
	
	
	public static function get__menu_ps($code = null)
	{
		if(!self::$userId) return null;
		
		if (!$code) $data = array();
		
		foreach ($_SESSION['__menu_p'] as $val )
		{
			foreach ($val as $v )
			{
				foreach ($v as $k1 => $v1 )
				{
				    if (!$code) {
				        $data[$v1] = $k1;
				    } 
				    else if ($v1 == $code) {
					    return $k1;
					}					
				}
			}
		}
		
		return isset($data) ? $data : false;
	}
	
	
	
	/**
	 *	菜单权限认证
	 */
	
	public static function getMenuAuth ($c)
	{
		
		if(empty($_SESSION)) return false;
		
		if(self::$userType  == 1){
			return true;
		}
		
		$code = strtoupper(Util::parseStr($c)).'_M';
		$p = self::get__menu_ps($code);
		if($p) return true;

		$code1 = 'OBJ_'.strtoupper(Util::parseStr($c));
		$p = self::get__menu_ps($code1);		
		if($p) return true;
		
		//未	查到，检查是否需要控制权限, 如果找不到，则不需要权限控制
		return self::getControlByCode($code1) === false;
	}
	
	
	private static function getControlByCode ($code)
	{
		
		$res = DB::cn(1)->getOne("SELECT id FROM `permission` WHERE `code`='{$code}' AND is_deleted=0");
		
		if($res)
		{
			return true;
		}
		
		return false;
	}
	
	
	
	/**
	 *	读取操作权限
	 */
	
	public static function getOperationPermissions ()
	{
		
		if(!self::$userId) return null;
		
		if(self::$userType  == 1){
			
			return array();
			
		}
		
		if(isset($_SESSION['__operation_p']))
		{
			
			return $_SESSION['__operation_p'];
			
		}
		
		
		$sql = "(SELECT permission_id,0 AS `source_id`,1 AS `type` FROM `user_operation_permission` WHERE `user_id`='".self::$userId."') UNION ALL (SELECT permission_id,0 AS `source_id`,1 AS `type` FROM `user_subdetail_operation_permission` WHERE `user_id`='".self::$userId."')";
		
		if(self::$isChannelKeeper || self::$isHouseKeeper)
		{
			
			$sql .= " UNION ALL (SELECT DISTINCT permission_id,source_id,type FROM `user_extend_operation` WHERE `user_id`='".self::$userId."')";
			
			$sql .= " UNION ALL (SELECT DISTINCT permission_id,source_id,type FROM `user_extend_subdetail_operation` WHERE `user_id`='".self::$userId."' )";
			
		}
		
		
		$sql = "SELECT DISTINCT p.id,p.code,u.source_id,u.type FROM (".$sql.") AS u INNER JOIN `permission` AS p ON u.permission_id=p.id WHERE p.is_deleted=0 AND p.type=3 ";
		
		
		$result = DB::cn(1)->getAll($sql);
			
		$data = array();
		
		if($result)
		{
			
			foreach ($result as $key => $val )
			{
				
				if(!self::$isChannelKeeper && $val['type']==3){
					
					continue;
					
				}
				
				if(!self::$isHouseKeeper && $val['type']==2){
					
					continue;
					
				}
				
				$data[$val['type']][$val['source_id']][strtoupper($val['code'])] = $val['id'];
			}
		}
		
		if(self::$userType>1)
		{
			$_SESSION['__operation_p'] = $data;
		}

		return $data;
	}
	
	
	
	
	/**
	 *	读取操作权限
	 */
	
	public static function get__operation_ps ($code = null)
	{
		if(!self::$userId) return null;
		
		if (!$code) $data = array();
		
		foreach ($_SESSION['__operation_p'] as $val )
		{
			foreach ($val as $v )
			{
				foreach ($v as $k1 => $v1 )
				{
				    if (!$code) {
				        $data[$k1] = $v1;
				    } else if ($k1 == $code) {
				        return $v1;
				    }
				}
			}
		}
		
		return isset($data) ? $data : false;
	}
	
	
	
	/**
	 *	操作权限认证
	 */
	
	public static function getOperationAuth ($c,$act)
	{
		
		if(!self::$userId) return null;
		
		if(self::$userType == 1){
			
			return true;
			
		}
		
		$code = strtoupper(Util::parseStr($c)).'_'.strtoupper(Util::parseStr($act)).'_O';
		
		$p = self::get__operation_ps($code);
		if ($p) {
		    return $p;
		}
				
		return self::getOprByCode($code) === false;
	}
	
	
	private static function getOprByCode ($code)
	{
		
		$res = DB::cn(1)->getOne("SELECT id FROM `permission` WHERE `code`='{$code}' AND `is_deleted` = 0");
		
		if($res)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	
	
	/**
	 *	读取按钮权限
	 */
	
	public static function getButtonPermissions ()
	{
		
		if(!self::$userId) return null;
		
		if(self::$userType  == 1)
		{
			
			$_SESSION['__button_p'] = array();
			
			return array();
		}
		
		if(isset($_SESSION['__button_p']))
		{
			
			return $_SESSION['__button_p'];
		}
		
		
		$sql = "(SELECT parent_id,permission_id,0 AS `source_id`,1 AS `type` FROM `user_button_permission` WHERE `user_id`='".self::$userId."') UNION (SELECT parent_id,permission_id,0 AS `source_id`,1 AS `type` FROM `user_subdetail_button_permission` WHERE `user_id`='".self::$userId."') UNION ALL (SELECT parent_id,permission_id,0 AS `source_id`,1 AS `type` FROM `user_view_button_permission` WHERE `user_id`='".self::$userId."') ";
		
		if(self::$isChannelKeeper || self::$isHouseKeeper)
		{
			
			$sql .= " UNION ALL (SELECT DISTINCT parent_id,permission_id,source_id,type FROM `user_extend_list_button` WHERE `user_id`='".self::$userId."' )";
			
			$sql .= " UNION ALL (SELECT DISTINCT parent_id,permission_id,source_id,type FROM `user_extend_view_button` WHERE `user_id`='".self::$userId."' )";
			
			$sql .= " UNION ALL (SELECT DISTINCT parent_id,permission_id,source_id,type FROM `user_extend_subdetail_button` WHERE `user_id`='".self::$userId."' )";
			
		}
		
		
		$sql = "SELECT DISTINCT p.id,p.code,u.parent_id,u.source_id,u.type FROM (".$sql.") AS u INNER JOIN `permission` AS p ON u.permission_id=p.id WHERE p.is_deleted='0' ";
		
		
		$result = DB::cn(1)->getAll($sql);
		
		if(!$result)
		{
			
			$_SESSION['__button_p'] = array();
			
			return array();
		}
		
		
		$data = array();	
		if($result)
		{
			
			foreach ($result as $key => $val )
			{
				if(!self::$isHouseKeeper && $val['type']==2){
					continue;
				}
				
				if(!self::$isChannelKeeper && $val['type']==3){
					continue;
				}
				
				$data[$val['type']][$val['source_id']][$val['parent_id']][$val['id']][$val['id']] = strtoupper($val['code']);
			}
		}
		
		if(self::$userType >1)
		{
			$_SESSION['__button_p'] = $data;
		}

		return $data;
	}
	
	
	
	/**
	 *	读取按钮权限
	 */
	
	public static function get__buttons($code = null)
	{
		if(!self::$userId) return null;
		
		if (!$code) $data = array();
		
		if($_SESSION['__button_p'])
		{
			foreach ($_SESSION['__button_p'] as $val )
			{
				foreach ($val as $v )
				{
					foreach ($v as $v1 )
					{
						foreach ($v1 as $v2 )
						{
							foreach ($v2 as $k3 => $v3 )
							{
							    if (!$code){
							        $data[$v3] = $k3;
							    } else if ($v3 == $code) {
								    return $k3;
								}
							}
						}
					}
				}
			}
		}
		
		return isset($data) ? $data : false;
	}
	
	
	
	/**
	 *	读取按钮权限
	 */
	
	public static function get__button_ps ($code = null)
	{
		if(!self::$userId) return null;
		
		$data = array();
		foreach ($_SESSION['__button_p'] as $val )
		{
			foreach ($val as $v )
			{
				foreach ($v as $k1 => $v1 )
				{
					foreach ($v1 as $k2 => $v2 )
					{
					    $data[$k1][$k2] = $v2[$k2];
					}					
				}
			}
		}
		
		if ($code) {
    		if (isset($data[$code])) {
    		    return $data[$code];
    		} else {
    		    return array();
    		}
		}
		return $data;
	}
	
	
	/**
	 *	按钮权限认证
	 */
	
	public static function getAuth ($code)
	{
		
		if(empty($_SESSION)) return false;
		
		if(self::$userType == 1){
			
			return true;
		}
		
		$p = self::get__buttons($code);
		if($p) return $p;
		
		return self::getButtonByCode($code) === false;
	}
	
	
	private static function getButtonByCode ($code)
	{
		
		$res = DB::cn(1)->getOne("SELECT id FROM `permission` WHERE `code`='{$code}' AND is_deleted=0");
		
		if($res)
		{
			return true;
		}
		
		return false;
	}
	
	
	
	
	
	/**
	 *	生成列表页按钮工具条
	 *	@param Array $list
	 *	array('BUTTON1','BUTTON2','BUTTO3'……'
	 )
	 *	如果不传递则获取该菜单下所有权限按钮，否则按$list获取权限按钮
	 *	@return String
	 */
	
	public static function getBar ($code = '',$list=array(),$short=false)
	{
		if(gettype($code)=='array')
		{
			
			$list = $code;
			
			$code = '';
		}
		
		if($code=='')
		{
			$backtrace = debug_backtrace();
			
			array_shift($backtrace);
			//去掉自身
			$functionName = $backtrace[0]['function'];
			
			$className = substr($backtrace[0]['class'],0,-10);
			
			$code = strtoupper(Util::parseStr($className)).'_M';
		}

		
		if(self::$userType!=1)
		{
			if(isset(self::$bars[$code]))
			{
				return self::$bars[$code];
			}
		}

		$menu_p = self::get__menu_ps($code);
		if (!$menu_p) {
		  $all = array();
		} else {
		    $all = self::get__button_ps($menu_p);
		}

		if(!$all && self::$userType != 1)
		{
			return '';
		}
		
		if($list)
		{
			foreach ($all as $key => $val )
			{
				if(!in_array($val,$list))
				{
					unset($all[$key]);
				}
			}
		}
		
		$menu_id = self::getMenuIdByPermissionCode($code);
		if(self::$userType==1)
		{
			
			$sql = "SELECT * FROM `button` AS b WHERE EXISTS (SELECT id FROM `menu` AS m WHERE m.`code`='".substr($code,0,-2)."' AND m.c_id=b.c_id) AND b.type=1 AND `b`.`is_deleted`='0' ";
			
			
			if($list)
			{
				
				$sql .=" AND EXISTS (SELECT id FROM `permission` WHERE `code` IN ('".implode("','",$list)."') AND b.id=permission.resource_id) ";
				
			}
			
			if($short)
			{
				
				$subsql = "(".$sql.") UNION (SELECT b.* FROM `button` AS b WHERE `b`.`id`='3' AND `b`.`is_deleted`='0')";
				
			}
			
			else
			{
				
				$subsql = "(".$sql.") UNION (SELECT b.* FROM `button` AS b WHERE `b`.`id` IN (1,2,3) AND `b`.`is_deleted`='0')";
				
			}
			
		}
		
		else
		{
			
			$ssql = "SELECT permission_id FROM `user_button_permission` WHERE `user_id`='".self::$userId."' AND `parent_id`='".$menu_p."'";
			
			
			if(self::$isChannelKeeper)
			{
				
				$ssql .= " UNION ALL (SELECT DISTINCT permission_id FROM `user_extend_list_button` WHERE `user_id`='".self::$userId."' AND `parent_id`='".$menu_p."' AND `type`='3')";
				
			}
			
			if(self::$isHouseKeeper)
			{
				
				$ssql .= " UNION ALL (SELECT DISTINCT permission_id FROM `user_extend_list_button` WHERE `user_id`='".self::$userId."' AND `parent_id`='".$menu_p."' AND `type`='2')";
				
			}
			
			
			$subsql = "SELECT p.resource_id AS id FROM (".$ssql.") AS `u` INNER JOIN `permission` AS `p` ON `u`.`permission_id`=`p`.`id` WHERE p.`is_deleted`=0";
			
			if($all)
			{
				
				$subsql .=" AND p.id IN (".implode(',',array_keys($all)).")";
				
			}
			
		}
		
		$subsql = "SELECT b.* FROM (".$subsql.") AS t LEFT JOIN `button` AS b ON t.id=b.id AND b.is_deleted='0' ORDER BY b.display_order DESC ";
		
		
		$sql = "SELECT main.*,bi.name AS icon_name,bc.classname AS class_name,bf.name AS function_name FROM (".$subsql.") AS main LEFT JOIN `button_icon` AS bi ON main.icon_id=bi.id LEFT JOIN `button_class` AS bc ON main.class_id=bc.id LEFT JOIN `button_function` AS bf ON bf.id=main.function_id";
		
		
		$res =  DB::cn(1)->getAll($sql);
		if(!$res)
		{
			return '';
		}
		
		Util::array_unique_fb($res,'id');
		
		$toolBar = '';
		
		$len = count($res);
		
		foreach ($res as $key=>$val )
		{
			if(!$val['id'])
			{
				continue;
			}
			
			$toolBar .='<div class="btn-group">'.PHP_EOL;
			
			$toolBar .='<button class="btn btn-sm '.$val['class_name'].'" onclick="util.'.$val['function_name'].'(this'.($val['cust_function'] ? ','.$val['cust_function'] : '').');" data-url="'.$val['data_url'].'" name="'.$val['label'].'" title="'.$val['tips'].'"  list-id="'.$menu_id.'"  data-title="'.$val['data_title'].'">'.PHP_EOL;
			
			$toolBar .="\t".$val['label'];
			
			// add by geng, 未到店加上待处理预约数量
			if ($val['label'] == '到店' && $_SESSION['bespoke_todo_count']>0) $toolBar .= '('.$_SESSION['bespoke_todo_count'].')';
			
			$toolBar .=' <i class="fa '.$val['icon_name'].'"></i>'.PHP_EOL;
			
			$toolBar .='</button>'.PHP_EOL.'</div>'.PHP_EOL;
		}
		
		if(self::$userType!=1)
		{
			self::$bars[$code] = $toolBar;
		}
		
		return $toolBar;
	}
	
	
	public static function getMenuIdByPermissionCode($code)
	{
		
		$sql = "SELECT resource_id FROM `permission` WHERE `code`='{$code}' AND is_deleted=0 ";
		
		return DB::cn(1)->getOne($sql);
	}
	
	public static function getViewBar ($list=array())
	{
		$backtrace = debug_backtrace();
		
		array_shift($backtrace);
		//去		掉自身
		$functionName = $backtrace[0]['function'];
		
		$className = substr($backtrace[0]['class'],0,-10);
		
		$code = strtoupper(Util::parseStr($className)).'_M';
		return self::getViewBar2($list, $code);
	}
	
	
	
	
	/**
	 *	生成查看页面按钮工具条
	 *	@param int $id
	 *	@param Array $list
	 *	array('BUTTON1','BUTTON2','BUTTO3'……'
	 )
	 *	按$list获取权限按钮
	 *	@return String
	 */
	
	public static function getViewBar2 ($list=array(), $code = "")
	{
		if (empty($code)) {
			$backtrace = debug_backtrace();
			
			array_shift($backtrace);
			//去		掉自身
			$functionName = $backtrace[0]['function'];
			
			$className = substr($backtrace[0]['class'],0,-10);
			
			$code = strtoupper(Util::parseStr($className)).'_M';
		}
		
		if(self::$userType!=1)
		{
			if(isset(self::$_bars[$code]))
			{
				return self::$_bars[$code];
			}			
		}
		
		$menu_p = self::get__menu_ps($code);
		if (!$menu_p) {
		    $all = array();
		} else {
		    $all = self::get__button_ps($menu_p);
		}
		
		if(!$all && self::$userType != 1)
		{
			return '';
		}
		
		if($list)
		{
			foreach ($all as $key => $val )
			{
				if(!in_array($val,$list))
				{
					unset($all[$key]);
				}
			}
		}
		
		$menu_id = self::getMenuIdByPermissionCode($code);
		if(self::$userType==1)
		{
			if($list)
			{
				$subsql = "(SELECT b.* FROM `button` AS b WHERE EXISTS (SELECT p.* FROM `permission` AS p WHERE p.`code` IN ('".implode("','",$list)."') AND p.`is_deleted`=0 AND p.resource_id=b.id) AND b.type='2' AND b.is_deleted='0' ) UNION ALL (SELECT * FROM `button` WHERE `id`='3') UNION ALL (SELECT * FROM `button` WHERE `id`='4') ORDER BY display_order DESC";
			}
			else
			{
				$subsql = "(SELECT b.* FROM `button` AS b WHERE EXISTS (SELECT * FROM `menu` AS m WHERE m.`id`='".$menu_id."' AND b.c_id=m.c_id) AND b.type='2' AND b.is_deleted='0') UNION (SELECT * FROM `button` WHERE `id`='3') UNION (SELECT * FROM `button` WHERE `id`='4') ORDER BY display_order DESC";
			}
		}
		else
		{
			$ssql = "SELECT DISTINCT permission_id FROM `user_view_button_permission` WHERE `user_id`='".self::$userId."' AND `parent_id`='".$menu_p."'";
			
			if(self::$isChannelKeeper)
			{
				
				$ssql .= " UNION ALL (SELECT DISTINCT permission_id FROM `user_extend_view_button` WHERE `user_id`='".self::$userId."' AND `parent_id`='".$menu_p."'  AND `type`='3')";
				
			}
			
			if(self::$isHouseKeeper)
			{
				
				$ssql .= " UNION ALL (SELECT DISTINCT permission_id FROM `user_extend_view_button` WHERE `user_id`='".self::$userId."' AND `parent_id`='".$menu_p."' AND `type`='2')";
				
			}
			
			
			$subsql = "SELECT p.resource_id AS id FROM (".$ssql.") AS `u` INNER JOIN `permission` AS `p` ON `u`.`permission_id`=`p`.`id` WHERE p.`is_deleted`=0";
			
			if($all)
			{
				
				$subsql .=" AND p.id IN (".implode(',',array_keys($all)).")";
				
			}
			
			
			
			$subsql ="SELECT b.* FROM `button` AS b WHERE EXISTS (".$subsql." AND b.id=p.resource_id) AND b.is_deleted='0' ORDER BY b.display_order DESC";
			
		}
		
		
		$sql = "SELECT DISTINCT main.*,bi.name AS icon_name,bc.classname AS class_name,bf.name AS function_name FROM (".$subsql.") AS main LEFT JOIN `button_icon` AS bi ON main.icon_id=bi.id LEFT JOIN `button_class` AS bc ON main.class_id=bc.id LEFT JOIN `button_function` AS bf ON bf.id=main.function_id";
		
		
		$res =  DB::cn(1)->getAll($sql);
		
		if(!$res)
		{
			return '';
		}
		
		Util::array_unique_fb($res,'id');
		
		$toolBar = '';
		
		$len = count($res);
		
		foreach ($res as $key=>$val )
		{
			
			if(!$val['id'])
			{
				continue;
			}
			
			$toolBar .='<div class="btn-group">'.PHP_EOL;
			
			$toolBar .='<button class="btn btn-sm '.$val['class_name'].'" onclick="util.'.$val['function_name'].'(this'.($val['cust_function'] ? ','.$val['cust_function'] : '').');" data-url="'.$val['data_url'].'" name="'.$val['label'].'" title="'.$val['tips'].'" list-id="'.$menu_id.'" data-title="'.$val['data_title'].'">'.PHP_EOL;
			
			$toolBar .="\t".$val['label'];
			
			$toolBar .=' <i class="fa '.$val['icon_name'].'"></i>'.PHP_EOL;
			
			$toolBar .='</button>'.PHP_EOL.'</div>'.PHP_EOL;
		}
		
		if(self::$userType>1)
		{
			self::$_bars[$code] = $toolBar;
		}
		
		return $toolBar;
	}
		
	public static function build_view_bar($btn_config=array(), $code="") {
		if (empty($code)) {
			$code = strtoupper(Util::parseStr($_REQUEST['con'])).'_M';
		}
		
		$list_bars = self::getViewBar2(array(), $code);
		$list_bars = Util::eexplode('<div', $list_bars);
		
		$matches = array();
		foreach ($list_bars as $k=>$b) {
			foreach ($btn_config as $btn => $rm) {
				if (strpos($b, $btn) !== false) {
				//if (preg_match('/class="btn-group">.*?'.$btn.'.*?<\/div>.*?/is',$b)) {
					if ($rm) unset($list_bars[$k]);
					$matches[] = $btn;
					unset($btn_config[$btn]);
					break;
				}
			}
			
			if (empty($btn_config)) break;
		}
			
		$list_bars = '<div '.implode(' <div ', $list_bars);
		return array($list_bars, $matches);
	}
	
	/**
	 *	生成明细列表页按钮工具条
	 *	@param Array $list
	 *	array('BUTTON1','BUTTON2','BUTTO3'……'
	 )
	 *	如果不传递则获取该明细下所有权限按钮，否则按$list获取权限按钮
	 *	@return String
	 */
	
	public static function getDetailBar ($code,$list=array())
	{
		$code = 'OBJ_'.strtoupper(Util::parseStr($code));
		
		if(self::$userType!=1)
		{
			if(isset(self::$__bars[$code]))
			{
				return self::$__bars[$code];
			}
		}
		
		$menu_p = self::get__menu_ps($code);
		if (!$menu_p) {
		    $all = array();
		} else {
		    $all = self::get__button_ps($menu_p);
		}	
		
		if(!$all && self::$userType != 1)
		{
			return '';
		}
		
		if($list)
		{
			
			foreach ($all as $key => $val )
			{
				
				if(!in_array($val,$list))
				{
					unset($all[$key]);
				}
			}
		}
		
		
		$menu_id = self::getMenuIdByDetailPermissionCode($code);
		
		if(self::$userType==1)
		{
			
			$subsql = "SELECT * FROM `button` AS b WHERE EXISTS (SELECT resource_id FROM `permission` AS m WHERE m.`code`='".$code."' AND m.resource_id=b.c_id) AND b.type=1 AND `b`.`is_deleted`='0' ";
			
			
			if($list)
			{
				
				$subsql .=" AND EXISTS (SELECT id FROM `permission` WHERE `code` IN ('".implode("','",$list)."') AND b.id=permission.resource_id) ";
				
			}
			
		}
		
		else
		{
			
			$ssql = "SELECT permission_id FROM `user_subdetail_button_permission` WHERE `user_id`='".self::$userId."' AND `parent_id`='".$menu_p."'";
			
			
			if(self::$isChannelKeeper)
			{
				
				$ssql .= " UNION ALL (SELECT DISTINCT permission_id FROM `user_extend_subdetail_button` WHERE `user_id`='".self::$userId."' AND `parent_id`='".$menu_p."'  AND `type`='3')";
				
			}
			
			if(self::$isHouseKeeper)
			{
				
				$ssql .= " UNION ALL (SELECT DISTINCT permission_id FROM `user_extend_subdetail_button` WHERE `user_id`='".self::$userId."' AND `parent_id`='".$menu_p."' AND `type`='2')";
				
			}
			
			
			$subsql = "SELECT p.resource_id AS id FROM (".$ssql.") AS `u` INNER JOIN `permission` AS `p` ON `u`.`permission_id`=`p`.`id` WHERE p.`is_deleted`=0";
			
			if($all)
			{
				
				$subsql .=" AND p.id IN (".implode(',',array_keys($all)).")";
				
			}
			
		}
		
		$subsql = "SELECT b.* FROM (".$subsql.") AS t INNER JOIN `button` AS b ON t.id=b.id AND b.is_deleted='0' ORDER BY b.display_order DESC ";
		
		
		$sql = "SELECT DISTINCT main.*,bi.name AS icon_name,bc.classname AS class_name,bf.name AS function_name FROM (".$subsql.") AS main LEFT JOIN `button_icon` AS bi ON main.icon_id=bi.id LEFT JOIN `button_class` AS bc ON main.class_id=bc.id LEFT JOIN `button_function` AS bf ON bf.id=main.function_id";
		
		
		$res =  DB::cn(1)->getAll($sql);
		
		if(!$res)
		{
			return '';
		}
		
		Util::array_unique_fb($res,'id');
		
		$toolBar = '';
		
		$len = count($res);
		
		foreach ($res as $key=>$val )
		{
			if(!$val['id'])
			{
				continue;
			}
			
			$toolBar .='<div class="btn-group">'.PHP_EOL;
			
			$toolBar .='<button class="btn btn-sm '.$val['class_name'].'" onclick="util.'.$val['function_name'].'(this'.($val['cust_function'] ? ','.$val['cust_function'] : '').');" data-url="'.$val['data_url'].'" list-id="'.$menu_id.'" name="'.$val['label'].'" title="'.$val['tips'].'" data-title="'.$val['data_title'].'">'.PHP_EOL;
			
			$toolBar .="\t".$val['label'];
			
			$toolBar .=' <i class="fa '.$val['icon_name'].'"></i>'.PHP_EOL;
			
			$toolBar .='</button>'.PHP_EOL.'</div>'.PHP_EOL;
			
		}
		
		if(self::$userType!=1)
		{
			self::$__bars[$code] = $toolBar;
		}
		
		return $toolBar;
	}
	
	
	private static function getMenuIdByDetailPermissionCode($code)
	{
		
		$sql = "SELECT `id` FROM `menu` WHERE `c_id`=(SELECT `parent_id` FROM `control` WHERE `id`=(SELECT `resource_id` FROM `permission` WHERE `code`='{$code}')) ";
		
		return DB::cn(1)->getOne($sql);
	}
	
	
	public static function getScopes ()
	{
		if(!self::$userId) return null;
		
		if(self::$userType  == 1){
			
			return array();
			
		}
		
		if(isset($_SESSION['__scope']))
		{
			
			return $_SESSION['__scope'];
		}
		
		//有	垃圾数据
		$sql = "SELECT `u`.`scope`,`u`.`type`,`u`.`source_id`,`p`.`code` FROM `user_scope` AS `u`,`permission` AS `p` WHERE `u`.`permission_id`=`p`.`id` AND `p`.`type`=5 AND `u`.`user_id`=".self::$userId;
		
		$res = DB::cn(1)->getAll($sql);
		
		$datas = array();
		
		foreach ($res as $val )
		{
			
			if($val['source_id'])
			{
				
				$datas[$val['type']][$val['source_id']][$val['code']] = $val['scope'];
			}
			
			else
			{
				$datas[$val['type']][$val['code']] = $val['scope'];
			}
		}
		
		$_SESSION['__scope'] = $datas;
		return $datas;
	}
	
	
	public static function canRead ($code,$type=1,$source_id=0)
	{
		
		if(self::$userType==1)
		{
			
			return true;
			
		}

		if(SYS_SCOPE=='zhanting' && self::$userName=='批发合作商'){
			return true;
		}
		
		$_code = 'SCOPE_'.strtoupper($code);
		
		try{
			
			if($source_id)
			{
				
				return isset($_SESSION['__scope'][$type]) && isset($_SESSION['__scope'][$type][$source_id]) && $_SESSION['__scope'][$type][$source_id][$_code]&1;
			}
			
			else
			{
				
				return isset($_SESSION['__scope'][1]) && $_SESSION['__scope'][1][$_code]&1;
			}
			
		}
		
		catch(Exception $e){
			
			return false;
		}
	}
	
	
	public static function canEdit ($code,$type=1,$source_id=0)
	{
		
		if(self::$userType==1)
		{
			return true;
		}
		
		$_code = 'SCOPE_'.strtoupper($code);
		
		try{
			
			if($source_id)
			{
				return isset($_SESSION['__scope'][$type]) && isset($_SESSION['__scope'][$type][$source_id]) && $_SESSION['__scope'][$type][$source_id][$_code]&2;
			}
			
			else
			{
				return isset($_SESSION['__scope'][1]) && $_SESSION['__scope'][1][$_code]&2;
			}
		}
		
		catch(Exception $e){
			
			return false;
		}
	}
	
	//r	esetLogin 自动登录，根据已登录用户名，重置更新用户基本信息到cookie
	public static function reLogin(){
		
		if(!self::$userName){
			
			return false;
		}
		
		$model = new UserModel(1);
		
		$do = $model->getByAccount(self::$userName);
		
		
		self::$userId = $do['id'];
		
		self::$userName = $do['account'];
		
		self::$companyId = empty( $do['company_id'] ) ? '-1' : $do['company_id'];
		
		self::$realName = $do['real_name'];
		
		self::$userType = $do['user_type'];
		
		self::$isHouseKeeper = $do['is_warehouse_keeper'];
		
		self::$isChannelKeeper = $do['is_channel_keeper'];
		
		self::$lastModify = $do['up_pwd_date'];
		
		self::$qudao = $do['qudao'];
		
		self::$bumen = $do['bumen'];
		
		self::setLoginCookies();
	}
	
	
	
	/*
	 * 判断当前公司是否来自总部或总部级别的公司
	 */
	
	public static function is_base_company($company_id) {
		
		return in_array($company_id, array('58','445', '515'));
	}
	
	
	public static function user_is_from_base_company() {
		
		if (self::is_base_company(self::getValFromEnv('companyId', -1))) {
			
			return true;
		}
		
		
		// 		load extend company
		$list = DB::cn(1)->getAll("select distinct company_id from cuteframe.user_extend_company where user_id=".self::$userId);
		
		$ids = array();
		
		foreach ($list as $c) {
			
			if (self::is_base_company($c['company_id'])) {
				
				return true;
			}
			
		}
		
		
		return false;
	}
	
	public static function getValFromEnv($key, $val_if_not_exist = null) {

	    if (isset($_SESSION[$key])) {
	        return $_SESSION[$key];
	    } else {
	        $cookie_key = Util::encrypt($key,AUTH_KEY);
	        if (isset($_COOKIE[$cookie_key])) {
	            return Util::decrypt($_COOKIE[$cookie_key], AUTH_KEY);
	        } else {
	            return $val_if_not_exist;
	        }
	    }
	}
	
	public static function saveSsoToken($token) {	    
	   setcookie(Util::encrypt("bosso",AUTH_KEY),Util::encrypt($token,AUTH_KEY),0,'/',ROOT_DOMAIN);
	}

    
    public static function ishop_check_close_company_type(){
    	if(defined('CLOSE_COMPANY_TYPE_3')){
    		if(CLOSE_COMPANY_TYPE_3=='YES'){
		    	if(!empty($_SESSION['companyId'])){
		            $sql = "select company_type from cuteframe.company where id = '{$_SESSION['companyId']}'"; 
		    	    $company_type = DB::cn(1)->getOne($sql);
		    	    if(!empty($company_type) && $company_type<>3)
		    	    	return false;
		    	    else
		    	    	return true;
		    	}else{
		    		return true;
		    	}
		    }else
		        return  false;	
		}else{
			    return false;
		}    	
    }
 

}

?>