<?php
/**
 *  -------------------------------------------------
 *   @file		: PermissionController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-19 11:51:55
 *   @update	:
 *  -------------------------------------------------
 */
class PermissionController extends CommonController
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
		$model = new PermissionModel(1);
		if(Auth::$userType<3)
		{
			$bar = Auth::getBar();
		}
		else
		{
			$bar = '';
		}
		$this->render('permission_search_form.html',array('bar'=>$bar,'view'=>new PermissionView($model)));
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
			'name'  =>_Request::get('name'),
			'type'  =>_Request::getInt('type'),
			'is_deleted'=>0
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array(
			'name'=>$args['name'],'type'=>$args['type'],'is_deleted'=>$args['is_deleted']
		);

		$model = new PermissionModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'permission_search_page';
		$this->render('permission_search_list.html',array(
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
		$result['content'] = $this->fetch('permission_info.html',array(
			'view'=>new PermissionView(new PermissionModel(1))
		));
		$result['title'] = '权限-添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('permission_info.html',array(
			'view'=>new PermissionView(new PermissionModel($id,1))
		));
		$result['title'] = '权限-编辑';
		Util::jsonExit($result);
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$name = _Post::getString('name');
		$code = _Post::get('code');
		$type = _Post::get('type');
		$resource_id = _Post::getInt('resource_id');
		$note = _Post::get('note');

		//开始校验

		if($name=='')
		{
			$result['error'] ="权限名称不能为空！";
			Util::jsonExit($result);
		}

		if($code=='')
		{
			$result['error'] ="编码不能为空！";
			Util::jsonExit($result);
		}

		if(!is_numeric($type)){
			$result['error'] ="资源类型非法！";
			Util::jsonExit($result);
		}
		if(!$resource_id)
		{
			$result['error'] ="请选择目标资源！";
			Util::jsonExit($result);
		}


		$olddo = array();
		$newdo=array(
			'name'=>$name,
			'code'=>$code,
			'type'=>$type,
			'resource_id'=>$resource_id,
			'note'=>$note
		);

		$newmodel =  new PermissionModel(2);
		//todo 查重

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
		$name = _Post::getString('name');
		$code = _Post::get('code');
		$type = _Post::get('type');
		$resource_id = _Post::getInt('resource_id');
		$note = _Post::get('note');

		if($name=='')
		{
			$result['error'] ="权限名称不能为空！";
			Util::jsonExit($result);
		}

		if($code=='')
		{
			$result['error'] ="编码不能为空！";
			Util::jsonExit($result);
		}

		if(!is_numeric($type)){
			$result['error'] ="资源类型非法！";
			Util::jsonExit($result);
		}
		if(!$resource_id)
		{
			$result['error'] ="请选择目标资源！";
			Util::jsonExit($result);
		}


		$newmodel =  new PermissionModel($id,2);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id'=>$id,
			'name'=>$name,
			'code'=>$code,
			'type'=>$type,
			'resource_id'=>$resource_id,
			'note'=>$note
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
		$model = new PermissionModel($id,2);
		$do = $model->getDataObject();
		if($do['is_system'])
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
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

	public function getResource ()
	{
		$type_id = _Post::getInt('type');
		$model = new PermissionModel(1);
		$data = $model->getResource($type_id);
		$this->render('permission_info_options.html',array('data'=>$data));
	}
}

?>