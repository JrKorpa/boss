<?php
/**
 *  -------------------------------------------------
 *   @file		: GroupuserController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-22 11:30:28
 *   @update	:
 *  -------------------------------------------------
 */
class GroupUserController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('group_user','cuteframe');	//生成模型后请注释该行
		//Util::V('group_user');	//生成视图后请注释该行
		if(Auth::$userType>2)
		{
			die('操作禁止');
		}

		$this->render('group_user_search_form.html',array('bar'=>Auth::getBar()));
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
		$where = array(
			'group_id'=>$args['group_id'],
		);

		$model = new GroupUserModel(1);
		$data = $model->pageList($where,$page,10,false);
//		echo '<pre>';
//		print_r($data);exit;
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'group_user_search_page';
		$this->render('group_user_search_list.html',array(
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
		/*echo $group_id;exit;*/
		$v = new GroupUserView(new GroupUserModel(1));
		$v->set_group_id($group_id);
		$result['content'] = $this->fetch('group_user_info.html',array(
			'view'=>$v
		));
		$result['title'] = '组用户-添加';
		Util::jsonExit($result);
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{

		$result = array('success' => 0,'error' =>'');
		$user_arr = _Post::getList('user_id');
		if(count($user_arr)==0)
		{
			$result['error'] = "请选择用户";
			Util::jsonExit($result);
		}
		$group_id =_Post::getInt('group_id');
		$newmodel =  new GroupUserModel(2);
		$res = $newmodel->getGroupexist($group_id);
		if($res===false){
			$result['error']="没有选择一个合理的组";
			Util::jsonExit($result);
		}

		$arr = array();
		foreach ($user_arr as $key => $val )
		{
			$arr[$key]['group_id'] = $group_id;
			$resu = $newmodel->getUserExist($val,$group_id);
			if($resu===false){
				$result['error'] = '该用户已在该组或者该用户不存在';
				Util::jsonExit($result);
			}
			$arr[$key]['user_id'] = $val;
		}
		try{
			$newmodel->insertAll($arr,'group_user');
		}
		catch(Exception $e)
		{
			$result['error'] = '添加失败';
			Util::jsonExit($result);
		}
		$result['success'] = 1;
		Util::jsonExit($result);

	}

	public function listGroup(){
		$result = array('success' => 0,'error' => '');
		$group_id = _Request::getInt('group_id');
		$model =  new GroupUserModel(2);
		$data = $model->getGroupUser($group_id);
		$result['content'] = $this->fetch('group_user_sort.html',array('data'=>$data));
		$result['title'] = '用户组-排序';
		Util::jsonExit($result);
	}

	public function saveSort(){
		$result = array('success' => 0,'error' => '');
		$sort = _Post::getList('group_user');
		$sort = array_values($sort);
		$model = new GroupUserModel(2);
		$res = $model->saveSort($sort);
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
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new GroupUserModel($id,2);
		$res = $model->delete();

		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

	public function groupList ()
	{
		$model = new GroupModel(1);
		$data = $model->getGroupTree();
		Util::jsonExit($data);
	}
}

?>