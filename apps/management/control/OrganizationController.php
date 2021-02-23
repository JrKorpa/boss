<?php
/**
 *  -------------------------------------------------
 *   @file		: OrganizationController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-13 17:14:08
 *   @update	:
 *  -------------------------------------------------
 */
class OrganizationController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index,左树右表
	 */
	public function index ($params)
	{
		$this->render('organization_search_form.html',array('bar'=>Auth::getBar()));
	}

	/**
	 *	deptList，树形列表
	 */
	public function deptList ()
	{
		$model = new DepartmentModel(1);
		$data = $model->getList();
		Util::jsonExit($data);
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
			'dept_id'=>_Request::getInt('dept_id'),
			'level'=>_Request::getInt('level'),
			'position'=>_Request::getInt('position'),
			'account'=>_Request::getString('account')
		);
		$page = _Request::getInt("page",1);
		$where = array();
		$where['dept_id'] = $args['dept_id'];
		$where['level'] = $args['level'];
		$where['position'] = $args['position'];
		$where['account'] = $args['account'];
		$model = new OrganizationModel(1);
		$data = $model->pageList($where,$page,10,false);
		
		$RoleModel=new RoleModel(1);
		$role_arr=$RoleModel->getRoleList();
		$roleArr=array();
		foreach ($role_arr as $v){
			$roleArr[$v['id']]=$v['label'];
		}
		
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'organization_search_page';
		$this->render('organization_search_list.html',array(
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
		$dept_id = _Request::getInt('dept_id');
		$v = new OrganizationView(new OrganizationModel(1));
		$v->set_dept_id($dept_id);
		$result['content'] = $this->fetch('organization_info.html',array(
			'view'=>$v
		));
		$result['title'] = '岗位-添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('organization_info.html',array(
			'view'=>new OrganizationView(new OrganizationModel($id,1))
		));
		$result['title'] = '岗位-编辑';
		Util::jsonExit($result);
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$user_id = _Post::getInt('user_id');
		$position = _Post::getInt('position');
		$level = _Post::getInt('level');
		$dept_id = _Post::getInt('dept_id');
		if(!$user_id)
		{
			$result['error'] = "请选择用户";
			Util::jsonExit($result);
		}
		$newmodel =  new OrganizationModel(2);

		$respo = $newmodel->hasPosition($position);
		if(!$respo){
			$result['error'] = "不存在的职位";
			Util::jsonExit($result);
		}


		$resle = $newmodel->hasPosition($level);
		if(!$resle){
			$result['error'] = "不存在的职级";
			Util::jsonExit($result);
		}


		$olddo = array();
		$newdo=array(
			'dept_id'=>$dept_id,
			'user_id'=>$user_id,
			'position'=>$position,
			'level'=>$level
		);

		$newmodel =  new OrganizationModel(2);
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
		$id = _Post::getInt('id');
		$user_id = _Post::getInt('user_id');
		$position = _Post::getInt('position');
		$level = _Post::getInt('level');
		$dept_id = _Post::getInt('dept_id');
		if(!$user_id)
		{
			$result['error'] = "请选择用户";
			Util::jsonExit($result);
		}

		$newmodel =  new OrganizationModel($id,2);
		$respo = $newmodel->hasPosition($position);
		if(!$respo){
			$result['error'] = "不存在的职位";
			Util::jsonExit($result);
		}


		$resle = $newmodel->hasPosition($level);
		if(!$resle){
			$result['error'] = "不存在的职级";
			Util::jsonExit($result);
		}

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id'=>$id,
			'dept_id'=>$dept_id,
			'user_id'=>$user_id,
			'position'=>$position,
			'level'=>$level
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
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
		$model = new OrganizationModel($id,2);
		$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
}

?>