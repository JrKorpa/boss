<?php
/**
 *  -------------------------------------------------
 *   @file		: GroupController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-16 15:32:08
 *   @update	:
 *  -------------------------------------------------
 */
class GroupController extends CommonController
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
		$this->render('group_search_form.html',array('bar'=>Auth::getBar()));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$model = new GroupModel(1);
		$data = $model->getList();
		$this->render('group_search_list.html',array(
			'data'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('group_info.html',array(
			'view'=>new GroupView(new GroupModel(1))
		));
		$result['title'] = '工作组-添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('group_info.html',array(
			'view'=>new GroupView(new GroupModel($id,1))
		));
		$result['title'] = '工作组-编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$this->render('group_show.html',array(
			'view'=>new GroupView(new GroupModel($id,1)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$name = _Post::get('name');
		$code = _Post::get('code');
		$parent_id = _Post::getInt('parent_id');
		$note = _Post::get('note');
		$display_order = time();
		if($name=='')
		{
			$result['error'] ="工作组名称不能为空！";
			Util::jsonExit($result);
		}

		if(!Util::isChinese($name))
		{
			$result['error'] ="工作组名称只能是汉字";
			Util::jsonExit($result);
		}
		if(mb_strlen($name)>50)
		{
			$result['error'] ="工作组名称最多输入50个汉字";
			Util::jsonExit($result);
		}

		if(empty($code)){
			$result['error'] ="编码不能为空！";
			Util::jsonExit($result);
		}
		if(preg_match('/[^\w-]/u', $code))
		{
			$result['error'] ="编码只能包含字母和横线！";
			Util::jsonExit($result);
		}
		if(mb_strlen($code)>50)
		{
			$result['error'] ="工作组编码最多输入50个字符";
			Util::jsonExit($result);
		}
		if($note && !Util::isLegal($note))
		{
			$result['error'] ="描述里有非法字符";
			Util::jsonExit($result);
		}
		if(mb_strlen($note)>255)
		{
			$result['error'] ="描述最多输入255个字符";
			Util::jsonExit($result);
		}

		$model =  new GroupModel($parent_id,1);
		$pdo = $model->getDataObject();

		if($parent_id!=0){
			if(is_null($pdo)){
				$result['error'] ="您所选中的上级分类不存在！";
				Util::jsonExit($result);
			}
		}

		$tree_path = $pdo['tree_path'];
		if(count(explode('-', $tree_path)) > 4){
			$result['error'] ="组别深度不可以大于5层！";
			Util::jsonExit($result);
		}

		$newmodel =  new GroupModel(2);
		$has = $newmodel->hasCode($code);
		if($has)
		{
			$result['error'] ="操作失败,此编码已存在！";
			Util::jsonExit($result);
		}

		if($tree_path==null)
		{
			$tree_path = 0;
		}
		else
		{
			$tree_path .= "-".$parent_id;
		}

		if($parent_id)
		{
			$pids = $pdo['pids'];
			if($pids)
			{
				$pids.=",".$parent_id;
			}
			else
			{
				$pids = $parent_id;
			}
		}
		else
		{
			$pids='';
		}

		$olddo = array();
		$newdo=array(
			"name"=>$name,
			"code"=>$code,
			"note"=>$note,
			"display_order"=>$display_order,
			"parent_id"=>$parent_id,
			"tree_path"=>$tree_path,
			"childrens"=>0,
			"pids"=>$pids
		);
		$res = $newmodel->saveDatas($newdo,$olddo);
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
		$name = _Post::get('name');
		$code = _Post::get('code');
		$parent_id = _Post::getInt('parent_id');
		$note = _Post::get('note');
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');

		if($name=='')
		{
			$result['error'] ="工作组名称不能为空！";
			Util::jsonExit($result);
		}

		if(!Util::isChinese($name))
		{
			$result['error'] ="工作组名称只能是汉字";
			Util::jsonExit($result);
		}
		if(mb_strlen($name)>50)
		{
			$result['error'] ="工作组名称最多输入50个汉字";
			Util::jsonExit($result);
		}

		if(empty($code)){
			$result['error'] ="编码不能为空！";
			Util::jsonExit($result);
		}
		if(preg_match('/[^\w-]/u', $code))
		{
			$result['error'] ="编码只能包含字母和横线！";
			Util::jsonExit($result);
		}
		if(mb_strlen($code)>50)
		{
			$result['error'] ="工作组编码最多输入50个字符";
			Util::jsonExit($result);
		}
		if($note && !Util::isLegal($note))
		{
			$result['error'] ="描述里有非法字符";
			Util::jsonExit($result);
		}
		if(mb_strlen($note)>255)
		{
			$result['error'] ="描述最多输入255个字符";
			Util::jsonExit($result);
		}

		$newmodel =  new GroupModel($id,2);
		$has = $newmodel->hasCode($code);
		if($has)
		{
			$result['error'] ="操作失败,此编码已存在！";
			Util::jsonExit($result);
		}

		$olddo = $newmodel->getDataObject();
		$newdo = $olddo;
		$newdo['name'] =$name;
		$newdo['code'] =$code;
		$newdo['note'] =$note;

		if($parent_id!=$olddo['parent_id'])
		{//分类改变
			$model =  new GroupModel($parent_id,1);
			$pdo = $model->getDataObject();

			if($parent_id!=0){
				if(is_null($pdo)){
					$result['error'] ="您所选中的上级分类不存在！";
					Util::jsonExit($result);
				}
			}

			$tree_path = $pdo['tree_path'];
			if(count(explode('-', $tree_path)) > 4){
				$result['error'] ="组别深度不可以大于5层！";
				Util::jsonExit($result);
			}
			if($tree_path==null)
			{
				$tree_path = 0;
			}
			else
			{
				$tree_path .= "-".$parent_id;
			}
			$newdo['parent_id'] = $parent_id;
			$newdo['tree_path'] = $tree_path;

			if($parent_id)
			{
				$pids = $pdo['pids'];
				if($pids)
				{
					$pids.=",".$parent_id;
				}
				else
				{
					$pids = $parent_id;
				}
			}
			else
			{//变成顶级
				$pids='';
			}
			$newdo['pids'] = $pids;
			$res = $newmodel->saveDatas($newdo,$olddo);
		}
		else
		{//没有改变分类
			if($newdo['pids']==null)
			{
				$newdo['pids']='';
			}
			$res = $newmodel->saveData($newdo,$olddo);
		}

		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;
			$result['title'] = $name;
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
		$model = new GroupModel($id,2);
		$do = $model->getDataObject();
		if($do['is_system'])
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		if($do['childrens'])
		{
			$result['error'] = "有下级工作组，禁止删除";
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
	 *	moveup,上移
	 */
	public function moveup ()
	{
		$result = array('success' => 0,'error' => '');
		$id = _Post::getInt('id');
		$model = new GroupModel($id,2);
		$res = $model->move($id);
		if($res == 1){
			$result['success'] = 1;
		}
		else if ($res==3)
		{
			$result['error'] = "已经是第一个了";
		}
		else
		{
			$result['error'] = "移动失败";
		}
		Util::jsonExit($result);
	}

	/**
	 *	movedown,下移
	 */
	public function movedown ()
	{
		$result = array('success' => 0,'error' => '');
		$id = _Post::getInt('id');
		$model = new GroupModel($id,2);
		$res = $model->move($id,false);
		if($res == 1){
			$result['success'] = 1;
		}
		else if ($res==3)
		{
			$result['error'] = "已经是最后一个了";
		}
		else
		{
			$result['error'] = "移动失败";
		}
		Util::jsonExit($result);
	}
}

?>