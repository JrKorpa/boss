<?php
/**
 *  -------------------------------------------------
 *   @file		: RolePermissionController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-22 19:05:37
 *   @update	:
 *  -------------------------------------------------
 */
class RolePermissionController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		if(Auth::$userType>2)
		{
			die('操作禁止');
		}
		$this->render('role_permission_search_form.html');
	}

	/**
	 * Rolelist 获取角色列表
	 */
	public function roleList ()
	{
		$model = new RolePermissionModel(1);
		$data = $model->getRoleList();
		Util::jsonExit($data);
	}

	/**
	 *	菜单权限列表
	 */
	public function listMenu ()
	{
		$role_id = _Post::getInt('role_id');
		$this->render('role_permission_menu_list.html',array('role_id'=>$role_id));
	}

	/**
	 *	menuList,菜单列表 ajax
	 */
	public function menuList ()
	{
		$role_id = _Request::getInt('role_id');
		$is_menu = _Request::getInt('is_menu');
		$model = new RolePermissionModel(1);
		$data = $model->getMenuData($role_id,$is_menu);
		Util::jsonExit($data);
	}

	/**
	 *	saveMenu,保存菜单授权
	 */
	public function saveMenu ()
	{
		$result = array('success' => 0,'error' => '');
		$role_id = _Post::getInt('role_id');
		$ids = _Post::getList('pids');
		$model = new RolePermissionModel(2);
		$res = $model->saveMenu($role_id,$ids);
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
		$role_id = _Post::getInt('role_id');
		$this->render('role_permission_button_list.html',array('role_id'=>$role_id));
	}

	/**
	 * buttonList,菜单按钮列表
	 */
	public function buttonList ()
	{
		$permission_id = _Request::getInt('permission_id');//菜单权限id
		$role_id = _Request::getInt('role_id');
		$rid = _Request::getInt('rid');//菜单id
		$model = new RolePermissionModel(1);
		$data = $model->getButtonData($role_id,$permission_id,$rid);
		if($data)
		{
			$this->render('role_permission_button_info.html',array('role_id'=>$role_id,'parent_id'=>$permission_id,'data'=>$data));
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
		$role_id = _Post::getInt('role_id');
		$parent_id = _Post::getInt('parent_id');
		$ids = _Post::getList('ids');
		$model = new RolePermissionModel(2);
		$res = $model->saveButton($role_id,$parent_id,$ids);
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
	 * listViewButton,查看页按钮列表
	 */
	public function listViewButton ()
	{
		$role_id = _Post::getInt('role_id');
		$this->render('role_permission_view_button_list.html',array('role_id'=>$role_id));
	}

	/**
	 * viewButtonList,某菜单的查看按钮列表
	 */
	public function viewButtonList ()
	{
		$permission_id = _Request::getInt('permission_id');
		$role_id = _Request::getInt('role_id');
		$rid = _Request::getInt('rid');
		$model = new RolePermissionModel(1);
		$data = $model->getViewButtonData($role_id,$permission_id,$rid);
		if($data)
		{
			$this->render('role_permission_view_button_info.html',array('role_id'=>$role_id,'parent_id'=>$permission_id,'data'=>$data));
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
		$role_id = _Post::getInt('role_id');
		$parent_id = _Post::getInt('parent_id');
		$ids = _Post::getList('ids');
		$model = new RolePermissionModel(2);
		$res = $model->saveViewButton($role_id,$parent_id,$ids);
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
	 * listButton,操作权限列表
	 */
	public function listOpr ()
	{
		$role_id = _Post::getInt('role_id');
		$this->render('role_permission_opr_list.html',array('role_id'=>$role_id));
	}

	/**
	 * oprList,操作列表
	 */
	public function oprList ()
	{
		$permission_id = _Request::getInt('permission_id');
		$role_id = _Request::getInt('role_id');
		$rid = _Request::getInt('rid');
		$model = new RolePermissionModel(1);
		$data = $model->getOprData($role_id,$permission_id,$rid);
		if($data)
		{
			$this->render('role_permission_opr_info.html',array('role_id'=>$role_id,'parent_id'=>$permission_id,'data'=>$data));
		}
		else
		{
			echo '没有操作';
		}
	}

	/**
	 *  saveOpr,保存操作授权
	 */
	public function saveOpr ()
	{
		$result = array('success' => 0,'error' => '');
		$role_id = _Post::getInt('role_id');
		$parent_id = _Post::getInt('parent_id');
		$ids = _Post::getList('ids');
		$model = new RolePermissionModel(2);
		$res = $model->saveOperation($role_id,$parent_id,$ids);
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
	 *	明细列表容器
	 */
	public function listRel ()
	{
		$role_id = _Post::getInt('role_id');
		$this->render('role_permission_rel_list.html',array('role_id'=>$role_id));
	}

	/**
	 *	menuListDetail,含有明细对象的菜单列表 ajax
	 */
	public function menuListDetail ()
	{
		$role_id = _Request::getInt('role_id');
		$model = new RolePermissionModel(1);
		$data = $model->menuListDetail($role_id);
		Util::jsonExit($data);
	}

	/**
	 *	明细对象列表
	 */
	public function relList ()
	{
		$permission_id = _Request::getInt('permission_id');
		$role_id = _Request::getInt('role_id');
		$rid = _Request::getInt('rid');
		$model = new RolePermissionModel(1);
		$data = $model->getRelData($role_id,$permission_id,$rid);
		if($data)
		{
			$this->render('role_permission_rel_info.html',array('role_id'=>$role_id,'parent_id'=>$permission_id,'data'=>$data));
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
		$role_id = _Post::getInt('role_id');
		$parent_id = _Post::getInt('parent_id');
		$ids = _Post::getList('ids');
		$model = new RolePermissionModel(2);
		$res = $model->saveRel($role_id,$parent_id,$ids);

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
	 *	listRelButton,明细按钮容器
	 */
	public function listRelButton ()
	{
		$role_id = _Post::getInt('role_id');
		$this->render('role_permission_rel_button_list.html',array('role_id'=>$role_id));
	}

	/**
	 *	menuDetail 对象与明细树 ajax
	 */
	public function menuDetail ()
	{
		$role_id = _Request::getInt('role_id');
		$model = new RolePermissionModel(1);
		$data = $model->getMenuDetail($role_id);
		Util::jsonExit($data);
	}

	/**
	 *	relButtonList,明细按钮列表
	 */
	public function relButtonList ()
	{
		$permission_id = _Request::getInt('permission_id');
		$role_id = _Request::getInt('role_id');
		$rid = _Request::getInt('rid');
		$model = new RolePermissionModel(1);
		$data = $model->getRelButtonData($role_id,$permission_id,$rid);
		if($data)
		{
			$this->render('role_permission_rel_button_info.html',array('role_id'=>$role_id,'parent_id'=>$permission_id,'data'=>$data));
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
		$role_id = _Post::getInt('role_id');
		$parent_id = _Post::getInt('parent_id');
		$ids = _Post::getList('ids');
		$model = new RolePermissionModel(2);
		$res = $model->saveRelButton($role_id,$parent_id,$ids);

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
		$role_id = _Post::getInt('role_id');
		$this->render('role_permission_rel_opr_list.html',array('role_id'=>$role_id));
	}

	/**
	 *	relOprList,某明细列表按钮
	 */
	public function relOprList ()
	{
		$permission_id = _Request::getInt('permission_id');
		$role_id = _Request::getInt('role_id');
		$rid = _Request::getInt('rid');
		$model = new RolePermissionModel(1);
		$data = $model->getRelOprData($role_id,$permission_id,$rid);
		if($data)
		{
			$this->render('role_permission_rel_opr_info.html',array('role_id'=>$role_id,'parent_id'=>$permission_id,'data'=>$data));
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
		$role_id = _Post::getInt('role_id');
		$parent_id = _Post::getInt('parent_id');
		$ids = _Post::getList('ids');
		$model = new RolePermissionModel(2);
		$res = $model->saveRelOpr($role_id,$parent_id,$ids);

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