<?php
/**
 *  -------------------------------------------------
 *   @file		: GroupRoleController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-22 11:10:29
 *   @update	:
 *  -------------------------------------------------
 */
class GroupRoleController extends CommonController
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
		//		Util::M('group_role','cuteframe');	//生成模型后请注释该行
//		Util::V('group_role');	//生成视图后请注释该行
		$this->render('group_role_search_form.html',array('bar'=>Auth::getBar()));
	}

	/**
	 *	groupList,组树形列表
	 */
	public function groupList ()
	{
		$model = new GroupModel(1);
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
			'group_id'=>_Request::getInt('group_id')
		);

		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['group_id'] = $args['group_id'];

		$model = new GroupRoleModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'group_role_search_page';
		$this->render('group_role_search_list.html',array(
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
		$group_id = _Request::getInt('group_id');
		$v = new GroupRoleView(new GroupRoleModel(1));
		$v->setGroupId($group_id);
		$result['content'] = $this->fetch('group_role_info.html',array(
			'view'=>$v
		));
		$result['title'] = '组角色-添加';
		Util::jsonExit($result);
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$group_id = _Post::getInt('group_id');
		$role_id = _Post::getList('role_id');
		if(!$role_id)
		{
			$result['error'] ="请选择角色！";
			Util::jsonExit($result);
		}
		$arr = array();
		foreach ($role_id as $key => $val )
		{
			$arr[$key]['group_id'] = $group_id;
			$arr[$key]['role_id'] = $val;
		}

		$newmodel =  new GroupRoleModel(2);
		try{
			$newmodel->insertAll($arr,'group_role');
		}
		catch(Exception $e)
		{
			$result['error'] = '添加失败';
			Util::jsonExit($result);
		}
		$result['success'] = 1;
		Util::jsonExit($result);
	}


	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new GroupRoleModel($id,2);
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