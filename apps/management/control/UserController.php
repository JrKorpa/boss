<?php
/**
 *  -------------------------------------------------
 *   @file		: UserController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-26 17:55:27
 *   @update	:
 *  -------------------------------------------------
 */
class UserController extends CommonController
{
	protected $smartyDebugEnabled = false;

	protected $whitelist = array('captcha');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{

		$RoleModel=new RoleModel(1); 
		if(Auth::$userType>2)
		{						
			$res=$RoleModel->getUserListFromOrganization(59);
	        if($res){
	        	$res=array_column($res,'user_id');
	        	if(!in_array($_SESSION['userId'], $res))
	        		die('操作禁止');
	        }else{
	        	die('操作禁止');
	        } 
	    }    
		$role_arr=$RoleModel->getRoleList();
		$this->render('user_search_form.html',array('view'=>new UserView(new UserModel(1)),'bar'=>Auth::getBar(),'role_arr'=>$role_arr));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'account'	=> _Request::get("account"),
			'real_name'	=> _Request::get("real_name"),
			'code'	=> _Request::get("code"),
			'is_on_work'	=> (!isset($_REQUEST['is_on_work']) || isset($_REQUEST['is_on_work']) && $_REQUEST['is_on_work']=='') ? 2 :_Request::getInt("is_on_work"),
			'user_type'	=> _Request::getInt("user_type"),
			'role_id'	=> _Request::getInt("role_id"),
			'is_deleted'=>0
		);

		$RoleModel=new RoleModel(1);
		$role_arr=$RoleModel->getRoleList();
		$roleArr=array();
		foreach ($role_arr as $v){
			$roleArr[$v['id']]=$v['label'];
		}
		
		
		$page = _Request::getInt("page",1);
		$where = array();
		$where['account'] = $args['account'];
		$where['real_name'] = $args['real_name'];
		$where['code'] = $args['code'];
		$where['user_type'] = $args['user_type'];
		$where['is_on_work'] = $args['is_on_work'];
		$where['is_deleted'] = $args['is_deleted'];
		$where['role_id'] = $args['role_id'];
		$model = new UserModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'user_search_page';
		$this->render('user_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'roleArr'=>$roleArr,	
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		
		$companyModel     = new CompanyModel(1);
		$company_all   = $companyModel->getCompanyTree();//公司列表
		$RoleModel=new RoleModel(1);
		$role_arr=$RoleModel->getRoleList();
		$result['content'] = $this->fetch('user_info.html',array(
			'view'=>new UserView(new UserModel(1)),
		    'company_all'=>$company_all,
			'role_arr'=>$role_arr,
		));
		$result['title']='用户-添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$result['title']='用户-编辑';
		$model = new UserModel($id,1);
		$do = $model->getDataObject();
		if($do['is_deleted'])
		{
			$result['content'] = '此账户已被删除!';
		}
		else
		{
			$my_type = Auth::$userType;
			if($my_type!=1)
			{
				if($do['user_type']<3)
				{
					if($my_type==$do['user_type'])
					{
						if($id<>Auth::$userId)
						{
							$result['content'] ="无权编辑该用户！";
							Util::jsonExit($result);
						}
					}
					else
					{
						$result['content'] ="无权编辑该类型用户！";
						Util::jsonExit($result);
					}
				}
			}
			
			$companyModel     = new CompanyModel(1);
			$company_all   = $companyModel->getCompanyTree();//公司列表

			$userCompanyModel = new UserCompanyModel(1);
			$user_company = $userCompanyModel->getUserCompanyList(array('user_id'=>$id));
			$user_company = array_column($user_company,"company_id");
			$RoleModel=new RoleModel(1);
			$role_arr=$RoleModel->getRoleList();
			$view = new UserView($model);
			$result['content'] = $this->fetch('user_info.html',array(
				'view'=>$view,
			    'company_all'=>$company_all,
			    'user_company'=>$user_company,
				'role_arr'=>$role_arr
			));
		}
		Util::jsonExit($result);
	}

	/**
	 *	modify，渲染重置密码页面
	 */
	public function modify ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params["id"]);
		$model = new UserModel($id,1);
		$do = $model->getDataObject();
		if($do['is_deleted'])
		{
			$result['content'] = '此账户已被删除!';
		}
		else
		{
			$result['content'] = $this->fetch('modify_user_pass.html',array(
				'view'=>new UserView($model)
			));
		}

		$result['title']='重置密码';
		Util::jsonExit($result);
	}

	/**
	 *	captcha，生成验证码
	 */
	public function captcha ()
	{
		Image::buildImageVerify(4, -1, 'gif', 70, 25, "__modify_captcha");
	}

	/*验证验证码*/
	private function check_captcha($captcha)
	{
		//session_start();
		$session_captcha = $_SESSION['__modify_captcha'];
		$captcha = md5(strtolower($captcha));
		return $session_captcha == $captcha;
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

		$code = strtoupper(_Post::get('code'));
		$real_name = _Post::get('real_name');
		$gender = _Post::getInt('gender');
		$account = _Post::get('account');
		$email = _Post::get('email');
		$user_type = _Post::getInt('user_type');
		$mobile = _Post::get('mobile');
		$qq = _Post::get('qq');
		$birthday = _Post::get('birthday');
		$phone = _Post::get('phone');
		$join_date = _Post::get('join_date');
		$address = _Post::get('address');
		$icd = _Post::get('icd');
		$is_warehouse_keeper = _Post::getInt('is_warehouse_keeper');
		$is_channel_keeper = _Post::getInt('is_channel_keeper');
		$password = _Post::get('password');
		$role_id = _Post::get('role_id');
        $internship = _Post::get('internship');
        $trun_date = _Post::get('trun_date');
		if(empty($role_id)) $role_id=0;

		$my_type = Auth::$userType;
		$company_ids = _Post::getList('company_ids');
		$company_id = !empty($company_ids)?current($company_ids):0;
		
		if($my_type!=1)
		{
			if($user_type<3)
			{
				$result['error'] ="无权添加该类型用户！";
				Util::jsonExit($result);
			}
		}
		if($code && mb_strlen($code)>10)
		{
			$result['error'] ="编码不能超过10个字符";
			Util::jsonExit($result);
		}
		if($code && preg_match("/[^A-Z\d]/",$code))
		{
			$result['error'] ="编码不合法";
			Util::jsonExit($result);
		}

		if ($real_name=='') {
			$result['error'] ="姓名不能为空！";
			Util::jsonExit($result);
		}
		if(!Util::isLegal($real_name))
		{
			$result['error'] ="姓名只能填字母数字和汉字！";
			Util::jsonExit($result);
		}

		if(mb_strlen($real_name)>20)
		{
			$result['error'] ="姓名不能超过20个字符！";
			Util::jsonExit($result);
		}
		if ($account=='') {
			$result['error'] ="账号不能为空！";
			Util::jsonExit($result);
		}
                if(!preg_match('/^[\x{4E00}-\x{9FA5}a-z0-9_]+$/iu', $account)){
			$result['error'] ="账号只能填字母、数字、汉字和下划线！";
			Util::jsonExit($result);
		}

		if(mb_strlen($account)>20)
		{
			$result['error'] ="账号不能超过20个字符！";
			Util::jsonExit($result);
		}
		if ($email=='') {
			$result['error'] ="邮箱不能为空！";
			Util::jsonExit($result);
		}
		if(!Util::isEmail($email))
		{
			$result['error'] ="邮箱格式不正确！";
			Util::jsonExit($result);
		}
		if (!$user_type) {
			$result['error'] ="请选择用户类型！";
			Util::jsonExit($result);
		}
		if(!$mobile)
		{
			$result['error'] ="手机号码必填！";
			Util::jsonExit($result);
		}
		if(!Util::isMobile($mobile))
		{
			$result['error'] ="手机号码格式不正确！";
			Util::jsonExit($result);
		}
		if($qq && !Util::isQQ($qq))
		{
			$result['error'] ="qq号码格式不正确！";
			Util::jsonExit($result);
		}

		if(mb_strlen($icd)>18)
		{
			$result['error'] ="不是正确的身份证号！";
			Util::jsonExit($result);
		}
		if($icd && !preg_match("/^(\d{14}|\d{17})[\dx]$/",$icd))
		{
			//$result['error'] ="身份证号不合法";
			//Util::jsonExit($result);
		}
		if($password=='')
		{
			$result['error'] ="请设置密码！";
			Util::jsonExit($result);
		}
		if(mb_strlen($password)<6)
		{
			$result['error'] ="密码太短！";
			Util::jsonExit($result);
		}

		if(mb_strlen($password)>20)
		{
			$result['error'] ="密码太长！";
			Util::jsonExit($result);
		}

		if(!Util::is_password($password))
		{
			$result['error'] = '密码非法';
			Util::jsonExit($result);
		}
        if(empty($company_ids)){
            $result['error'] = '所拥有公司列表必选！';
            Util::jsonExit($result);
        }

		$olddo = array();

		$newdo=array(
			'code'=>$code,
			'real_name'=>$real_name,
			'gender'=>$gender,
			'account'=>$account,
			'email'=>$email,
			'user_type'=>$user_type,
			'mobile'=>$mobile,
			'qq'=>$qq,
			'icd' => $icd,
			'birthday'=>($birthday)?$birthday:'0000-00-00',
			'phone'=>$phone,
			'join_date'=>($join_date)?$join_date:'0000-00-00',
			'address'=>$address,
			'password'=>Util::xmd5($password),
			'is_on_work'=>1,
			'is_enabled'=>1,
			'is_warehouse_keeper'=>$is_warehouse_keeper,
			'is_channel_keeper'=>$is_channel_keeper,
		    'company_id'=>$company_id,
			'role_id'=>$role_id,
            'internship'=>$internship,
            'trun_date'=>($trun_date)?$trun_date:'0000-00-00'
		);

		$newmodel =  new UserModel(2);
		$has = $newmodel->hasAccount($account);
		if($has){
			$result['error'] ="此账号已存在！";
			Util::jsonExit($result);
		}
//		$has = $newmodel->hasCode($code);
//		if($has){
//			$result['error'] ="此编号已存在！";
//			Util::jsonExit($result);
//		}
        
		$res = $newmodel->saveData($newdo,$olddo,true);
		if($res !== false)
		{   
		    //插入公司记录
		    $this->_insertUserCompany($company_ids,$res['id']);
		    if(Auth::$userName ==$account){
		      Auth::reLogin();//如果修改的是自己账户，重置登录session
		    }
		    //AsyncDelegate::dispatch("opslog", array('event' => 'user_upserted', 'user_id' => $res['id']));
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');
		$code = _Post::get('code');
		$real_name = _Post::get('real_name');
		$gender = _Post::getInt('gender');
		$account = _Post::get('account');
		$email = _Post::get('email');
		$user_type = _Post::getInt('user_type');
		$mobile = _Post::get('mobile');
		$qq = _Post::get('qq');
		$birthday = _Post::get('birthday');
		$phone = _Post::get('phone');
		$join_date = _Post::get('join_date');
		$address = _Post::get('address');
		$icd = _Post::get('icd');
		$is_warehouse_keeper = _Post::getInt('is_warehouse_keeper');
		$is_channel_keeper = _Post::getInt('is_channel_keeper');
		$password = _Post::get('password');
		$role_id = _Post::get('role_id');
        $internship = _Post::get('internship');
        $trun_date = _Post::get('trun_date');
        if(empty($role_id)) $role_id=0;
		$my_type = Auth::$userType;
		$company_ids = _Post::getList('company_ids');
        $company_id = !empty($company_ids)?current($company_ids):0;
        
		if($my_type!=1)
		{
			if($my_type==2)
			{
				if(Auth::$userId<>$id)
				{
					if($user_type==1)
					{
						$result['error'] ="无权编辑成该类型用户！";
						Util::jsonExit($result);
					}
				}
				else
				{
					$user_type=2;
				}
			}
			else
			{
				if($user_type<3)
				{
					$result['error'] ="无权编辑成该类型用户！";
					Util::jsonExit($result);
				}
			}

		}
                
		if($code && mb_strlen($code)>10)
		{
			$result['error'] ="编码不能超过10个字符";
			Util::jsonExit($result);
		}
		if($code && preg_match("/[^A-Z\d]/",$code))
		{
			$result['error'] ="编码不合法";
			Util::jsonExit($result);
		}

		if ($real_name=='')
		{
			$result['error'] ="姓名不能为空！";
			Util::jsonExit($result);
		}
		if(!Util::isLegal($real_name))
		{
			$result['error'] ="姓名只能填字母数字和汉字！";
			Util::jsonExit($result);
		}

		if(mb_strlen($real_name)>20)
		{
			$result['error'] ="姓名不能超过20个字符！";
			Util::jsonExit($result);
		}
		if ($account=='') {
			$result['error'] ="账号不能为空！";
			Util::jsonExit($result);
		}
                if(!preg_match('/^[\x{4E00}-\x{9FA5}a-z0-9_]+$/iu', $account)){
			$result['error'] ="账号只能填字母、数字、汉字和下划线！";
			Util::jsonExit($result);
		}
		if(mb_strlen($account)>20)
		{
			$result['error'] ="账号不能超过20个字符！";
			Util::jsonExit($result);
		}
		if ($email=='') {
			$result['error'] ="邮箱不能为空！";
			Util::jsonExit($result);
		}
		if(!Util::isEmail($email))
		{
			$result['error'] ="邮箱格式不正确！";
			Util::jsonExit($result);
		}
		if (!$user_type) {
			$result['error'] ="请选择用户类型！";
			Util::jsonExit($result);
		}
		if(!$mobile)
		{
			$result['error'] ="手机号码必填！";
			Util::jsonExit($result);
		}
		if(!Util::isMobile($mobile))
		{
			$result['error'] ="手机号码格式不正确！";
			Util::jsonExit($result);
		}
		if($qq && !Util::isQQ($qq))
		{
			$result['error'] ="qq号码格式不正确！";
			Util::jsonExit($result);
		}
		if(mb_strlen($icd)>18)
		{
			$result['error'] ="不是正确的身份证号！";
			Util::jsonExit($result);
		}
		if($icd && !preg_match("/^(\d{14}|\d{17})[\dx]$/",$icd))
		{
			//$result['error'] ="身份证号不合法！";
			//Util::jsonExit($result);
		}
		if($password!='')
		{
			if(mb_strlen($password)<6)
			{
				$result['error'] ="密码太短！";
				Util::jsonExit($result);
			}

			if(mb_strlen($password)>20)
			{
				$result['error'] ="密码太长！";
				Util::jsonExit($result);
			}

			if(!Util::is_password($password))
			{
				$result['error'] = '密码非法';
				Util::jsonExit($result);
			}
		}

        if(empty($company_ids)){
            $result['error'] = '所拥有公司列表必选！';
            Util::jsonExit($result);
        }

		$newdo=array(
			"id"=>$id,
			'code'=>$code,
			'real_name'=>$real_name,
			'gender'=>$gender,
			'account'=>$account,
			'email'=>$email,
			'user_type'=>$user_type,
			'mobile'=>$mobile,
			'qq'=>$qq,
			'icd'=> $icd,
			'birthday'=>($birthday)?$birthday:'0000-00-00',
			'phone'=>$phone,
			'join_date'=>($join_date)?$join_date:'0000-00-00',
			'address'=>$address,
			'is_warehouse_keeper'=>$is_warehouse_keeper,
			'is_channel_keeper'=>$is_channel_keeper,
			'role_id'=>$role_id,
            'internship'=>$internship,
		    'company_id'=>$company_id,
            'trun_date'=>($trun_date)?$trun_date:'0000-00-00'
		);
		if($password)
		{
			$newdo['password']=Util::xmd5($password);
		}

		$newmodel =  new UserModel($id,2);
		$has = $newmodel->hasAccount($account);
		if($has){
			$result['error'] ="此账号已存在！";
			Util::jsonExit($result);
		}
//		$has = $newmodel->hasCode($code);
//		if($has){
//			$result['error'] ="此编号已存在！";
//			Util::jsonExit($result);
//		}

		$olddo = $newmodel->getDataObject();
		$password_changed = false;
		$account_changed =  $olddo['account'] != $newdo['account'];
		if (isset($newdo['password'])) {
			$password_changed = $olddo['password'] != $newdo['password'];
		}
		if(isset($olddo['company_id']) && in_array($olddo['company_id'],$company_ids)){
		    unset($newdo['company_id']);
		}
		
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false){
		    $this->_insertUserCompany($company_ids,$id);
		    if(Auth::$userName == $account){
		      Auth::reLogin();//如果修改的是自己账户，重置登录session
		    }
			$result['success'] = 1;
			//修改日志记录
			$dataLog['pkdata'] = array('id'=>$id);
			$dataLog['newdata'] = $newdo;
			$dataLog['olddata'] = $olddo;
			$dataLog['fields']  = $newmodel->getFieldsDefine();
			$this->operationLog("update",$dataLog);
			if ($password_changed || $account_changed) 	{
				$event = array('event' => 'user_upserted', 'user_id' => $id);
				if ($account_changed) $event['old_account'] = $olddo['account'];
				//AsyncDelegate::dispatch("opslog", $event);
			}
		}else{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}
	/**
	 * 添加公司
	 * @param array $company_ids
	 * @param int $user_id
	 */
    private function _insertUserCompany($company_ids,$user_id){
        
        if(!is_array($company_ids)){
            return false;
        }
        
        $del_ids = array();//待删除的公司ID
        $add_data = array();//待添加的用户ID，公司ID关联记录
        
        $userCompanyModel = new UserCompanyModel(1);
        //获取原始公司ID数组
        $data = $userCompanyModel->getUserCompanyList(array('user_id'=>$user_id));
        $hasCompanyIds = array_column($data,'company_id');
        
        //获取待删除的公司id
        foreach ($hasCompanyIds as $vo){
            if(!in_array($vo,$company_ids)){
                $del_ids[] = $vo;
            }
        }        
        //获取待新添加的公司ID
        foreach ($company_ids as $vo){
            if(!in_array($vo,$hasCompanyIds)){
                $add_data[] = array('company_id'=>$vo,'user_id'=>$user_id);
            }
        }

        //删除去除的记录
        if(!empty($del_ids)){
            $userCompanyModel->deleteCompanyByUser($del_ids, $user_id);  
        }
        //添加新记录  
        if(!empty($add_data)){    
            $userCompanyModel->insertCompanyAll($add_data);
        }
        return true;                
    }
	/**
	 *	setModify，重置密码
	 */
	public function setModify ()
	{
		$result = array('success' => 0,'error' => '');
		$id = _Post::getInt('id');
		$newPass = _Post::get('newPass');
		$confirmPass = _Post::get('confirmPass');
		$modify_captcha = _Post::get('modify_captcha');
		if($modify_captcha=='')
		{
			$result['error'] ="请填写验证码！";
			Util::jsonExit($result);
		}
		if(!$this->check_captcha($modify_captcha))
		{
			$result['error'] ="验证码不正确！";
			Util::jsonExit($result);
		}
		if($newPass=='')
		{
			$result['error'] ="请设置密码！";
			Util::jsonExit($result);
		}
		if(mb_strlen($newPass)<6)
		{
			$result['error'] ="密码太短！";
			Util::jsonExit($result);
		}
		if(mb_strlen($newPass)>20)
		{
			$result['error'] ="密码太长！";
			Util::jsonExit($result);
		}
		if(!Util::is_password($newPass))
		{
			$result['error'] = '密码非法';
			Util::jsonExit($result);
		}
		if($newPass!=$confirmPass)
		{
			$result['error'] ="密码不一致！";
			Util::jsonExit($result);
		}
		$model = new UserModel($id,2);
		$model->setValue('password',Util::xmd5($newPass));
		$res = $model->save(true);
		if($res !== false)
		{
			$result['success'] = 1;
			//修改日志记录
			$dataLog['pkdata'] = array('id'=>$id);
			$dataLog['newdata'] = array("password"=>$newPass);
			$dataLog['olddata'] = array("password"=>"***");
			$dataLog['fields']  = $model->getFieldsDefine();
			$this->operationLog("update",$dataLog);
			//AsyncDelegate::dispatch("opslog", array('event' => 'user_upserted', 'user_id' => $id));
		}
		else
		{
			$result['error'] = '重置失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	setEnabled，启用
	 */
	public function setEnabled ()
	{
		$id = _Post::getInt('id');
		$model = new UserModel($id,2);
		$do = $model->getDataObject();
		if($do['is_deleted'])
		{
			$result['error'] = "当前用户已被删除";
			Util::jsonExit($result);
		}
		if($do['is_enabled'])
		{
			$result['error'] = "当前账户已经启用";
			Util::jsonExit($result);
		}
		$model->setValue('is_enabled',1);
		$res = $model->save(true);
		if($res !== false)
		{
			$result['success'] = 1;
			//修改日志记录
			$dataLog['pkdata'] = array('id'=>$id);
			$dataLog['newdata'] = array("is_enabled"=>1);
			$dataLog['olddata'] = array("is_enabled"=>"0");
			$dataLog['fields']  = $model->getFieldsDefine();
			$this->operationLog("update",$dataLog);
			//AsyncDelegate::dispatch("opslog", array('event' => 'user_upserted', 'user_id' => $id));
		}
		else
		{
			$result['error'] = '启用失败';
		}
		Util::jsonExit($result);
	}


	/**
	 *	setDisabled，停用
	 */
	public function setDisabled ()
	{
		$id = _Post::getInt('id');
		$model = new UserModel($id,2);
		$do = $model->getDataObject();
		if($do['is_deleted'])
		{
			$result['error'] = "当前用户已被删除";
			Util::jsonExit($result);
		}
		if(!$do['is_enabled'])
		{
			$result['error'] = "当前账户已经停用";
			Util::jsonExit($result);
		}

		$model->setValue('is_enabled',0);
		$res = $model->save(true);
		if($res !== false)
		{
			$result['success'] = 1;
			//修改日志记录
			$dataLog['pkdata'] = array('id'=>$id);
			$dataLog['newdata'] = array("is_enabled"=>0);
			$dataLog['olddata'] = array("is_enabled"=>1);
			$dataLog['fields']  = $model->getFieldsDefine();
			$this->operationLog("update",$dataLog);
			//AsyncDelegate::dispatch("opslog", array('event' => 'user_upserted', 'user_id' => $id));
		}
		else
		{
			$result['error'] = '停用失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	setLeave，离职
	 */
	public function setLeave ()
	{
		$id = _Post::getInt('id');
		$model = new UserModel($id,2);
		$do = $model->getDataObject();
		if($do['is_deleted'])
		{
			$result['error'] = "当前用户已被删除";
			Util::jsonExit($result);
		}

		if(!$do['is_on_work'])
		{
			$result['error'] = "当前用户已经离职";
			Util::jsonExit($result);
		}
        $password = Util::random(32);
		$model->setValue('is_on_work',0);
		$model->setValue('password',Util::xmd5($password));//离职后密码随机生成
		$model->setValue('is_enabled',0);//离职后账号停用
		$res = $model->save(true);
		if($res !== false)
		{
			$result['success'] = 1;
			//修改日志记录
			$dataLog['pkdata'] = array('id'=>$id);			
			$dataLog['newdata'] = array("is_enabled"=>0,"is_on_work"=>0,"password"=>$password);
			$dataLog['olddata'] = array("is_enabled"=>1,"is_on_work"=>1,"password"=>"***");
			$dataLog['fields']  = $model->getFieldsDefine();
			$this->operationLog("update",$dataLog);
			//AsyncDelegate::dispatch("opslog", array('event' => 'user_upserted', 'user_id' => $id));
		}
		else
		{
			$result['error'] = '离职失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	setOnWork，入职
	 */
	public function setOnWork ()
	{
		$id = _Post::getInt('id');
		$model = new UserModel($id,2);
		$do = $model->getDataObject();
		if($do['is_deleted'])
		{
			$result['error'] = "当前用户已被删除";
			Util::jsonExit($result);
		}

		if($do['is_on_work'])
		{
			$result['error'] = "当前用户已经在职";
			Util::jsonExit($result);
		}

		$model->setValue('is_on_work',1);
		$res = $model->save(true);
		if($res !== false)
		{
			$result['success'] = 1;
			//修改日志记录
			$dataLog['pkdata'] = array('id'=>$id);
			$dataLog['newdata'] = array("is_on_work"=>1);
			$dataLog['olddata'] = array("is_on_work"=>0);
			$dataLog['fields']  = $model->getFieldsDefine();
			$this->operationLog("update",$dataLog);
		}
		else
		{
			$result['error'] = '入职失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new UserModel($id,2);
		$do = $model->getDataObject();
		if($do['is_deleted'])
		{
			$result['error'] = "当前用户已被删除";
			Util::jsonExit($result);
		}

		if($do['is_system'])
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}

		if($do['is_on_work'])
		{
			$result['error'] = "禁止删除在职员工";
			Util::jsonExit($result);
		}

		if($do['is_enabled'])
		{
			$result['error'] = "该用户已启用，禁止删除";
			Util::jsonExit($result);
		}
		$my_type = Auth::$userType;
		if($my_type!=1)
		{
			if($do['user_type']<3)
			{
				$result['content'] ="无权删除该类型用户！";
				Util::jsonExit($result);
			}
			if(Auth::$userId==$id)
			{
				$result['content'] ="你不能放弃治疗！";
				Util::jsonExit($result);
			}
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
			//日志记录
			$dataLog['pkdata'] = array('id'=>$id);
			$this->operationLog("delete",$dataLog);
			//AsyncDelegate::dispatch("opslog", array('event' => 'user_upserted', 'user_id' => $id));
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}


	/**
	 *	show，渲染查看页面
	 */
	public function show ()
	{
		$id = _Request::getInt('id');
		$model = new UserModel($id,1);
		$do= $model->getDataObject();
		$data['info'] = $do;
		$data['organization']=$model->getOrganization($id);
		$data['group'] = $model->getGroup($id);
		$data['role'] = $model->getRole($id);
                
		$data['channel'] = $do['is_channel_keeper'] ? $model->getChannel($id) : array();
		$data['house'] = $do['is_warehouse_keeper'] ? $model->getHouse($id) : array();
		
		//获取所在公司
		$companyModel     = new CompanyModel(1);
		$company_all   = $companyModel->getCompanyTree();//公司列表

		$company_all = array_column($company_all,'company_name','id');
        //获取当前所在公司
		if(!empty($do['company_id']) && !empty($company_all[$do['company_id']])){
		    $data['info']['company_name'] = $company_all[$do['company_id']];
		}else{
		    $data['info']['company_name'] = '';
		}
		
		//获取所在公司列表
		$userCompanyModel = new UserCompanyModel(1);
		$has_company = $userCompanyModel->getUserCompanyList(array('user_id'=>$id));
		$has_company = array_column($has_company,'company_id');
		$str = '';
		foreach ($has_company as $vo){
		    if(isset($company_all[$vo])){
		        $str .=$company_all[$vo].' , ';
		    }
		}
		$data['info']['has_company'] = trim($str,' , ');
		
		$this->render('user_show.html',array('bar'=>Auth::getViewBar(),
			'data'=>$data
		));
	}

	/**
	 *	显示用户权限
	 */
	public function showPower ()
	{
		$id = _Request::getInt('id');//谁
		$model = new UserModel($id,1);
		$do = $model->getDataObject();
		$user_id = Auth::$userId;//我
		$userType = Auth::$userType;
		if($do['user_type']==1)
		{
			die ('全部权限');
		}

		if($userType==2)
		{
			if($do['user_type']==2 && $id!=$user_id)
			{
				die('无权查看');
			}
		}
		else if ($userType>2)
		{
			if($id!=$user_id)
			{
				die('无权查看');
			}
		}

		$model = new UserModel($id,1);
		$this->render('user_show_power.html',array(
			'data'=>$model->getPermission($id)
		));
	}
}
?>