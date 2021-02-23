<?php
/**
 *  -------------------------------------------------
 *   @file		: RoleController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-17 14:21:53
 *   @update	:
 *  -------------------------------------------------
 */
class RoleController extends CommonController
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
		$this->render('role_search_form.html',array('bar'=>Auth::getBar()));
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
			"label" =>_Request::get('label'),
			"code"  =>strtoupper(_Request::get('code'))
		);
		$page = _Request::getInt("page",1);
		$where = array();
		$where['label']=$args['label'];
		$where['code'] =$args['code'];

		$model = new RoleModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'role_search_page';

		$this->render('role_search_list.html',array(
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
		$result['content'] = $this->fetch('role_info.html',array(
			'view'=>new RoleView(new RoleModel(1))
		));
		$result['title'] = '角色-添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('role_info.html',array(
			'view'=>new RoleView(new RoleModel($id,1))
		));
		$result['title'] = '角色-编辑';
		Util::jsonExit($result);
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$label = _Post::get('label');
		$code = strtoupper(_Post::get('code'));
		$note = _Post::get('note');
		if($label=='')
		{
			$result['error'] ="角色名称不能为空！";
			Util::jsonExit($result);
		}
		if(!Util::isChinese($label))
		{
			$result['error'] ="角色名称只能填汉字！";
			Util::jsonExit($result);
		}
		if(mb_strlen($label)>40)
		{
			$result['error'] ="角色名称最多输入40个汉字！";
			Util::jsonExit($result);
		}

		if($code=='')
		{
			$result['error'] ="编号不能为空！";
			Util::jsonExit($result);
		}
		if(!Util::isField($code))
		{
			$result['error'] ="编号非法！";
			Util::jsonExit($result);
		}
		if(mb_strlen($code)>40)
		{
			$result['error'] ="编号最多输入40个字符！";
			Util::jsonExit($result);
		}
		if(mb_strlen($note)>250)
		{
			$result['error'] ="描述最多输入250个字符！";
			Util::jsonExit($result);
		}
		if(!Util::isLegal($note))
		{
			$result['error'] ="描述不能有非法字符！";
			Util::jsonExit($result);
		}
		$olddo = array();
		$newdo=array(
			'label'=>$label,
			'code'=>$code,
			'note'=>$note
		);
		$newmodel =  new RoleModel(2);
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
		$label = _Post::get('label');
		$code = strtoupper(_Post::get('code'));
		$note = _Post::get('note');
		//开始验证
		if($label=='')
		{
			$result['error'] ="角色名称不能为空！";
			Util::jsonExit($result);
		}
		if(!Util::isChinese($label))
		{
			$result['error'] ="角色名称只能填汉字！";
			Util::jsonExit($result);
		}
		if(mb_strlen($label)>40)
		{
			$result['error'] ="角色名称最多输入40个汉字！";
			Util::jsonExit($result);
		}

		if($code=='')
		{
			$result['error'] ="编号不能为空！";
			Util::jsonExit($result);
		}
		if(!Util::isField($code))
		{
			$result['error'] ="编号非法！";
			Util::jsonExit($result);
		}
		if(mb_strlen($code)>40)
		{
			$result['error'] ="编号最多输入40个字符！";
			Util::jsonExit($result);
		}
		if(mb_strlen($note)>250)
		{
			$result['error'] ="描述最多输入250个字符！";
			Util::jsonExit($result);
		}
		if(!Util::isLegal($note))
		{
			$result['error'] ="描述不能有非法字符！";
			Util::jsonExit($result);
		}


		$newmodel =  new RoleModel($id,2);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id'=>$id,
			'label'=>$label,
			'code'=>$code,
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
		if($id==1)
		{
			$result['error'] = "内置记录，禁止删除";
			Util::jsonExit($result);
		}
		$model = new RoleModel($id,2);
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