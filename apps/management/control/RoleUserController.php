<?php
/**
 *  -------------------------------------------------
 *   @file		: RoleUserController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-22 11:10:14
 *   @update	:
 *  -------------------------------------------------
 */
class RoleUserController extends CommonController
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
		$this->render('role_user_search_form.html',array('bar'=>Auth::getBar()));
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
			'role_id'=>_Request::getInt('role_id'),
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['role_id'] = $args['role_id'];

		$model = new RoleUserModel(1);

		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'role_user_search_page';
		$this->render('role_user_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$role_id = _Post::get('role_id');
		$v = new RoleUserView(new RoleUserModel(1));
		$v->set_role_id($role_id);
		$result['content'] = $this->fetch('role_user_info.html',array(
			'view'=>$v
		));
		$result['title'] = '角色用户-添加';
		Util::jsonExit($result);
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

		$role_id = _Post::get('role_id');

		if(!isset($_POST['user_arr']))
		{
			$result['error'] ="请选择用户！";
			Util::jsonExit($result);
		}
		$user_arr = $_POST['user_arr'];

		$arr = array();
		foreach ($user_arr as $key => $val )
		{
			$arr[$key]['role_id'] = $role_id;
			$arr[$key]['user_id'] = $val;
		}
		$newmodel =  new RoleUserModel(2);
		try{
			$newmodel->insertAll($arr,'role_user');
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
		$model = new RoleUserModel($id,2);
		$res = $model->delete();
//		print_r($res);exit;
		if($res !== false){
			$result['success'] = 1;

		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

	/**
	 * Rolelist 获取角色列表
	 */
	public function roleList ()
	{
		$model = new RoleUserModel(1);
		$data = $model->getRoleList();
		Util::jsonExit($data);
	}
}

?>