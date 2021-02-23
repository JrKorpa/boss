<?php
/**
 *  -------------------------------------------------
 *   @file		: MenuGroupController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-19 15:52:33
 *   @update	:
 *  -------------------------------------------------
 */
class MenuGroupController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index
	 */
	public function index ($params)
	{
		die('我是明细，没有菜单列表');
	}
	/**
	 *	search，菜单组列表
	 */
	public function search ($params)
	{
		if(Auth::$userType>2)
		{
			die('操作禁止');
		}
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'_id' => _Request::get("_id"),
			'label'	=> _Request::get('label')
		);
		$page = _Request::getInt("page",1);
		$where = array();
		$where['label'] = $args['label'];
		$where['_id'] = $args['_id'];

		$model = new MenuGroupModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'menu_group_search_page';
		$this->render('menu_group_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('menu_group_info.html',array(
			'view'=>new MenuGroupView(new MenuGroupModel(1)),
			'_id'=>_Post::getInt('_id')
		));
		$result['title'] = '菜单组-添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$tab_id = _Post::getInt('tab_id');//主记录对应的列表页签id
		$result = array('success' => 0,'error' => '');
		$v = new MenuGroupView(new MenuGroupModel($id,1));
		$result['content'] = $this->fetch('menu_group_info.html',array(
			'view'=>$v,
			'tab_id'=>$tab_id,
			'_id'=>$v->get_application_id()
		));
		$result['title'] = '菜单组-编辑';
		Util::jsonExit($result);
	}


	/**
	 *	insert，添加记录
	 */
	public function insert()
	{
		$result = array('success' => 0,'error' =>'');
		$_id = _Post::getInt('_id');//主表主键
		$label = _Post::get('label');

		$icon = _Post::getInt('icon');
		$is_enabled = _Post::getInt('is_enabled');
		$display_order = time();
		if($label=='')
		{
			$result['error'] ="分组名称不能为空！";
			Util::jsonExit($result);
		}

		if(!Util::isChinese($label))
		{
			$result['error'] ="分组名称只能是汉字！";
			Util::jsonExit($result);
		}

		if(mb_strlen($label)>10)
		{
			$result['error'] ="分组名称不能超过10个汉字！";
			Util::jsonExit($result);
		}

		$olddo = array();
		$newdo=array(
			"label"=>$label,
			"application_id"=>$_id,
			"icon"=>$icon,
			"is_enabled"=>$is_enabled,
			"display_order"=>$display_order
		);

		$newmodel =  new MenuGroupModel(2);
		if($newmodel->hasLabel($label))
		{
			$result['error'] ="分组名称重复！";
			Util::jsonExit($result);
		}
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
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
		$tab_id = _Post::getInt('tab_id');

		$label = _Post::get('label');
		$id = _Post::getInt('id');
		$icon = _Post::getInt('icon');
		$is_enabled = _Post::getInt('is_enabled');
		if($label=='')
		{
			$result['error'] ="分组名称不能为空！";
			Util::jsonExit($result);
		}

		if(!Util::isChinese($label))
		{
			$result['error'] ="分组名称只能是汉字！";
			Util::jsonExit($result);
		}

		if(mb_strlen($label)>10)
		{
			$result['error'] ="分组名称不能超过10个汉字！";
			Util::jsonExit($result);
		}
		$newmodel =  new MenuGroupModel($id,2);
		if($newmodel->hasLabel($label))
		{
			$result['error'] ="分组名称重复！";
			Util::jsonExit($result);
		}
		$olddo = $newmodel->getDataObject();
		$newdo=array(
			"id"=>$id,
			"label"=>$label,
			"icon"=>$icon,
			"is_enabled"=>$is_enabled
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['tab_id'] = $tab_id;
		}
		else
		{
			$result['error'] = '修改失败';
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
		$model = new MenuGroupModel($id,2);
		$do = $model->getDataObject();
		if($do['is_system'])
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		if($model->hasRelData($id))
		{
			$result['error'] = "存在关联数据，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

	/**
	 *	ListMenuGroup,排序页面
	 */
	public function ListMenuGroup ()
	{
		$result = array('success' => 0,'error' => '');
		$_id = _Post::getInt('_id');//主表主键
		$model = new MenuGroupModel(1);
		$data = $model->getMenuGroups($_id);
		$result['content'] = $this->fetch('menu_group_sort.html',array('data'=>$data));
		$result['title'] = '项目-菜单组排序';
		Util::jsonExit($result);
	}

	/**
	 *	saveSort,排序保存
	 */
	public function saveSort ()
	{
		$result = array('success' => 0,'error' => '');
		$datas = _Post::getList('MenuGroupsArray');
		krsort($datas);
		$datas = array_values($datas);
		$model = new MenuGroupModel(1);
		$res = $model->saveSort($datas);
		if($res)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}

	/**
	 *	ListMenu,菜单排序
	 */
	public function ListMenu ()
	{
		$result = array('success' => 0,'error' => '');
		$id = _Post::getInt('id');//菜单组主键
		$model = new MenuGroupModel(1);
		$data = $model->ListMenu($id);
		$result['content'] = $this->fetch('menu_group_menu_sort.html',array('data'=>$data));
		$result['title'] = '菜单排序';
		Util::jsonExit($result);
	}

	/**
	 *	saveMenuSort,排序保存
	 */
	public function saveMenuSort ()
	{
		$result = array('success' => 0,'error' => '');
		$datas = _Post::getList('MenusArray');
		krsort($datas);
		$datas = array_values($datas);
		$model = new MenuGroupModel(1);
		$res = $model->saveMenuSort($datas);
		if($res)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}

}
?>