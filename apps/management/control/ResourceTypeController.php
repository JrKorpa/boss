<?php
/**
 *  -------------------------------------------------
 *   @file		: ResourceTypeController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-19 10:47:10
 *   @update	:
 *  -------------------------------------------------
 */
class ResourceTypeController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		if(Auth::$userType!=1)
		{
			echo '无权操作';
			die;
		}
		$this->render('resource_type_search_form.html',array('bar'=>Auth::getBar()));
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
		);

		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();

		$model = new ResourceTypeModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'resource_type_search_page';
		$this->render('resource_type_search_list.html',array(
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
		$result['content'] = $this->fetch('resource_type_info.html',array(
			'view'=>new ResourceTypeView(new ResourceTypeModel(1))
		));
		if(Auth::$userType != 1){$result['content'] = '对不起，您没有权限!';}
		$result['title'] = '添加-资源类型';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('resource_type_info.html',array(
			'view'=>new ResourceTypeView(new ResourceTypeModel($id,1))
		));
		if(Auth::$userType != 1){$result['content'] = '对不起，您没有权限!';}
		$result['title'] = '编辑-资源类型';
		Util::jsonExit($result);
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ()
	{
		$result = array('success' => 0,'error' =>'');

		foreach ($_POST as $k => $v) {
			$$k = _Post::get($k);
		}

		$olddo = array();
		$newdo=array(
			'label'=>$label,
			'code'=>$code,
			'main_table'=>$main_table,
			'user_table'=>$user_table,
			'fields'=>$fields,
			'foreigh_key'=>$foreigh_key,
			'is_system'=>$is_system,
			'is_enabled'=>$is_enabled,
			'is_deleted'=>'0',
			'note'=>$note,
		);

		$newmodel =  new ResourceTypeModel(2);
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

		foreach ($_POST as $k => $v) {
			$$k = _Post::get($k);
		}

		$newmodel =  new ResourceTypeModel($id,2);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id'=>$id,
			'label'=>$label,
			'code'=>$code,
			'main_table'=>$main_table,
			'user_table'=>$user_table,
			'fields'=>$fields,
			'foreigh_key'=>$foreigh_key,
			'is_system'=>$is_system,
			'is_enabled'=>$is_enabled,
			'is_deleted'=>'0',
			'note'=>$note,
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
		if(Auth::$userType != 1){return false;}
		$id = intval($params['id']);
		$model = new ResourceTypeModel($id,2);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
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
}

?>