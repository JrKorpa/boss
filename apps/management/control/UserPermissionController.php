<?php
/**
 *  -------------------------------------------------
 *   @file		: UserPermissionController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-25 14:55:46
 *   @update	:
 *  -------------------------------------------------
 */
class UserPermissionController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		if(Auth::$userType>1)
		{
			die('仅允许管理员操作！');
		}
		$this->render('user_permission_search_form.html');
	}

	public function search ()
	{
		$q = strtolower(trim($_GET["q"]));
		if (!$q) return;
		$model = new UserPermissionModel(1);
		$data = $model->searchUser($q);
		foreach ($data as $value) {
			echo $value['real_name']."(".$value['account'].")|".$value['id']."\n";
		}
	}

	/**
	 *	菜单权限列表
	 */
	public function listMenu ()
	{
		$user_id = _Post::getInt('user_id');
		$this->render('user_permission_menu_list.html',array('user_id'=>$user_id));
	}

	/**
	 *	menuList,菜单列表 ajax
	 */
	public function menuList ()
	{
		$user_id = _Request::getInt('user_id');
		$is_menu = _Request::getInt('is_menu');
		$model = new UserPermissionModel(1);
		$data = $model->getMenuData($user_id,$is_menu);
		Util::jsonExit($data);
	}

	/**
	 *	saveMenu,保存菜单授权
	 */
	public function saveMenu ()
	{
		$result = array('success' => 0,'error' => '');
		$user_id = _Post::getInt('user_id');
		$ids = _Post::getList('pids');
		$model = new UserPermissionModel(2);
		$res = $model->saveMenu($user_id,$ids);
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '保存失败';
		}
		Util::jsonExit($result);
	}

	/**
	 * listButton,按钮权限列表
	 */
	public function listButton ()
	{
		$user_id = _Post::getInt('user_id');
		$this->render('user_permission_button_list.html',array('user_id'=>$user_id));
	}

	/**
	 * buttonList,菜单按钮列表
	 */
	public function buttonList ()
	{
		$permission_id = _Request::getInt('permission_id');
		$user_id = _Request::getInt('user_id');
		$rid = _Request::getInt('rid');
		$model = new UserPermissionModel(1);
		$data = $model->getButtonData($user_id,$permission_id,$rid);
		if($data)
		{
			$this->render('user_permission_button_info.html',array('user_id'=>$user_id,'parent_id'=>$permission_id,'data'=>$data));
		}
		else
		{
			echo '没有按钮';
		}
	}

	/**
	 *	saveButton,保存按钮授权
	 */
	public function saveButton ()
	{
		$result = array('success' => 0,'error' => '');
		$user_id = _Post::getInt('user_id');
		$parent_id = _Post::getInt('parent_id');
		$ids = _Post::getList('ids');
		$model = new UserPermissionModel(2);
		$res = $model->saveButton($user_id,$parent_id,$ids);
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '保存失败';
		}
		Util::jsonExit($result);
	}

	/**
	 * listViewButton,按钮权限列表
	 */
	public function listViewButton ()
	{
		$user_id = _Post::getInt('user_id');
		$this->render('user_permission_view_button_list.html',array('user_id'=>$user_id));
	}

	/**
	 * viewButtonList,查看页按钮列表
	 */
	public function viewButtonList ()
	{
		$permission_id = _Request::getInt('permission_id');
		$user_id = _Request::getInt('user_id');
		$rid = _Request::getInt('rid');
		$model = new UserPermissionModel(1);
		$data = $model->getViewButtonData($user_id,$permission_id,$rid);
		if($data)
		{
			$this->render('user_permission_view_button_info.html',array('user_id'=>$user_id,'parent_id'=>$permission_id,'data'=>$data));
		}
		else
		{
			echo '没有按钮';
		}
	}


	/**
	 *	saveViewButton,保存按钮授权
	 */
	public function saveViewButton ()
	{
		$result = array('success' => 0,'error' => '');
		$user_id = _Post::getInt('user_id');
		$parent_id = _Post::getInt('parent_id');
		$ids = _Post::getList('ids');
		$model = new UserPermissionModel(2);
		$res = $model->saveViewButton($user_id,$parent_id,$ids);
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '保存失败';
		}
		Util::jsonExit($result);
	}

	/**
	 * listOpr,操作权限列表
	 */
	public function listOpr ()
	{
		$user_id = _Post::getInt('user_id');
		$this->render('user_permission_opr_list.html',array('user_id'=>$user_id));
	}

	/**
	 * oprList,操作列表
	 */
	public function oprList ()
	{
		$permission_id = _Request::getInt('permission_id');
		$user_id = _Request::getInt('user_id');
		$rid = _Request::getInt('rid');

		$model = new UserPermissionModel(1);
		$data = $model->getOprData($user_id,$permission_id,$rid);
		$this->render('user_permission_opr_info.html',array('user_id'=>$user_id,'parent_id'=>$permission_id,'data'=>$data));
	}

	/**
	 *  saveOpr,保存操作授权
	 */
	public function saveOpr ()
	{
		$result = array('success' => 0,'error' => '');
		$user_id = _Post::getInt('user_id');
		$parent_id = _Post::getInt('parent_id');
		$ids = _Post::getList('ids');
		$model = new UserPermissionModel(2);
		$res = $model->saveOperation($user_id,$parent_id,$ids);
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '保存失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	menuListDetail,含有明细对象的菜单列表 ajax
	 */
	public function menuListDetail ()
	{
		$user_id = _Request::getInt('user_id');
		$model = new UserPermissionModel(1);
		$data = $model->menuListDetail($user_id);
		Util::jsonExit($data);
	}

	/**
	 *	明细列表容器
	 */
	public function listRel ()
	{
		$user_id = _Post::getInt('user_id');
		$this->render('user_permission_rel_list.html',array('user_id'=>$user_id));
	}


	/**
	 *	明细对象列表
	 */
	public function relList ()
	{
		$permission_id = _Request::getInt('permission_id');
		$user_id = _Request::getInt('user_id');
		$rid = _Request::getInt('rid');
		$model = new UserPermissionModel(1);
		$data = $model->getRelData($user_id,$permission_id,$rid);
		if($data)
		{
			$this->render('user_permission_rel_info.html',array('user_id'=>$user_id,'parent_id'=>$permission_id,'data'=>$data));
		}
		else
		{
			echo '没有明细对象';
		}
	}

	/**
	 *	保存明细对象授权
	 */
	public function saveRel ()
	{
		$result = array('success' => 0,'error' => '');
		$user_id = _Post::getInt('user_id');
		$parent_id = _Post::getInt('parent_id');
		$ids = _Post::getList('ids');
		$model = new UserPermissionModel(2);
		$res = $model->saveRel($user_id,$parent_id,$ids);

		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '保存失败';
		}
		Util::jsonExit($result);

	}

	/**
	 *	listRelButton,明细列表容器
	 */
	public function listRelButton ()
	{
		$user_id = _Post::getInt('user_id');
		$this->render('user_permission_rel_button_list.html',array('user_id'=>$user_id));
	}

	/**
	 *	menuDetail 对象与明细树 ajax
	 */
	public function menuDetail ()
	{
		$user_id = _Request::getInt('user_id');
		$model = new UserPermissionModel(1);
		$data = $model->getMenuDetail($user_id);
		Util::jsonExit($data);
	}

	/**
	 *	relButtonList,明细按钮列表
	 */
	public function relButtonList ()
	{
		$permission_id = _Request::getInt('permission_id');
		$user_id = _Request::getInt('user_id');
		$rid = _Request::getInt('rid');
		$model = new UserPermissionModel(1);
		$data = $model->getRelButtonData($user_id,$permission_id,$rid);
		if($data)
		{
			$this->render('user_permission_rel_button_info.html',array('user_id'=>$user_id,'parent_id'=>$permission_id,'data'=>$data));
		}
		else
		{
			echo '没有按钮';
		}
	}

	/**
	 *	saveRelButton,明细按钮授权
	 */
	public function saveRelButton ()
	{
		$result = array('success' => 0,'error' => '');
		$user_id = _Post::getInt('user_id');
		$parent_id = _Post::getInt('parent_id');
		$ids = _Post::getList('ids');
		$model = new UserPermissionModel(2);
		$res = $model->saveRelButton($user_id,$parent_id,$ids);

		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '保存失败';
		}
		Util::jsonExit($result);

	}

	/**
	 *	listRelOpr,明细操作容器
	 */
	public function listRelOpr ()
	{
		$user_id = _Post::getInt('user_id');
		$this->render('user_permission_rel_opr_list.html',array('user_id'=>$user_id));
	}

	/**
	 *	relOprList,某明细列表按钮
	 */
	public function relOprList ()
	{
		$permission_id = _Request::getInt('permission_id');
		$user_id = _Request::getInt('user_id');
		$rid = _Request::getInt('rid');
		$model = new UserPermissionModel(1);
		$data = $model->getRelOprData($user_id,$permission_id,$rid);
		if($data)
		{
			$this->render('user_permission_rel_opr_info.html',array('user_id'=>$user_id,'parent_id'=>$permission_id,'data'=>$data));
		}
		else
		{
			echo '没有操作';
		}
	}

	/**
	 *	saveRelOpr,明细按钮授权
	 */

	public function saveRelOpr ()
	{
		$result = array('success' => 0,'error' => '');
		$user_id = _Post::getInt('user_id');
		$parent_id = _Post::getInt('parent_id');
		$ids = _Post::getList('ids');
		$model = new UserPermissionModel(2);
		$res = $model->saveRelOpr($user_id,$parent_id,$ids);

		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '保存失败';
		}
		Util::jsonExit($result);
	}

	/**
	 * listCopy	权限复制页面
	 */
	public function listCopy(){
                if(Auth::$userType==1){
                        $this->render('user_permission_copy_list.html');
                }
                else
                {
                        die('该功能仅超级管理员可用');
                }
	}

	/**
	 * saveCopy 复制权限
	 */
	public function saveCopy(){

		$user_id = _Post::getInt('user_id');//待授权用户
		$copy_id = _Post::getInt('copy_id');//权限源用户

                if(!$user_id){
			$result['error'] = '请选择待授权用户';
			Util::jsonExit($result);                  
                }
                if(!$copy_id){
			$result['error'] = '请选择权限源用户';
			Util::jsonExit($result);                  
                }
		if($user_id==$copy_id){
			$result['error'] = '复制权限的用户与被复制权限者相同';
			Util::jsonExit($result);
		}
		//这里判断授权者和被复制者的角色等级比较如果同级或者授权者高则不能复制权限


		$model = new UserPermissionModel(2);

		$res = $model->saveCopy($user_id,$copy_id);
		if(!$res){
			$result['error'] = '复制权限失败';
			Util::jsonExit($result);
		}else{
			$result['error'] = '复制权限成功';
			Util::jsonExit($result);
		}
	}


	/*
	 * 删除权限
	 * */
	public function delPerminssions(){
		//被删除的权限
		$id = _Post::getInt('user_id');

		$model = new UserPermissionModel(2);
		$button_table = 'user_button_permission';//按钮权限
		$menu_table = 'user_menu_permission';//菜单权限
		$oper_table = 'user_operation_permission';//操作权限
		$view_button = 'user_view_button_permission';//查看按钮
		$subdetail_table ='user_subdetail_permission';//明细
		$subdetail_button = 'user_subdetail_button_permission';//明细按钮
		$subdetail_operation = 'user_subdetail_operation_permission';//明细操作
		$user_scope = 'user_scope';//属性控制
		$user_warehouse = 'user_warehouse';//仓库管控
		$user_channel = 'user_channel';//渠道管控
		$user_extend_menu = 'user_extend_menu';//扩展菜单权限
		$user_extend_list_button = 'user_extend_list_button';//扩展列表按钮
		$user_extend_view_button = 'user_extend_view_button';//扩展查看按钮
		$user_extend_operation = 'user_extend_operation';//扩展操作权限
		$user_extend_subdetail = 'user_extend_subdetail';//扩展明细权限
		$user_extend_subdetail_button = 'user_extend_subdetail_button';//扩展明细按钮
		$user_extend_subdetail_operation = 'user_extend_subdetail_operation';//扩展明细操作

		$table = array(
			$button_table,
			$menu_table,
			$oper_table,
			$view_button,
			$subdetail_table,
			$subdetail_button,
			$subdetail_operation,
			$user_scope,
			$user_warehouse,
			$user_channel,
			$user_extend_menu,
			$user_extend_list_button,
			$user_extend_view_button,
			$user_extend_operation,
			$user_extend_subdetail,
			$user_extend_subdetail_button,
			$user_extend_subdetail_operation
		);
		$res = $model->cancelPermissions($table,$id);

		if(!$res){
			$result['error'] = '取消权限失败';
			Util::jsonExit($result);
		}else{
			$result['error'] = '取消权限成功';
			Util::jsonExit($result);
		}
	}

	public function listScope ()
	{
		$user_id = _Post::getInt('user_id');
		$model = new UserPermissionModel(2);
		$data = $model->listScope($user_id);
		if($data)
		{
			$this->render('user_permission_scope_list.html',array('user_id'=>$user_id,'data'=>$data));
		}
		else
		{
			die('没有需要控制的属性');
		}
	}

	public function saveScope ()
	{
		$result = array('success' => 0,'error' => '');
		$user_id = _Post::getInt('user_id');
		$data = _Post::getList('s');
		$model = new UserPermissionModel(2);
		$res = $model->saveScope($user_id,$data);

		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '保存失败';
		}
		Util::jsonExit($result);
	}

}

?>