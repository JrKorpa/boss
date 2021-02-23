<?php
/**
 *  -------------------------------------------------
 *   @file		: ButtonIconController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-25 17:01:36
 *   @update	:
 *  -------------------------------------------------
 */
class ButtonIconController extends CommonController
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
		$this->render('button_icon_search_form.html',array('bar'=>Auth::getBar()));
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
		$page = _Request::getInt("page",1);
		$where = array();

		$model = new ButtonIconModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'button_icon_search_page';
		$this->render('button_icon_search_list.html',array(
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
		$result['content'] = $this->fetch('button_icon_info.html',array(
			'view'=>new ButtonIconView(new ButtonIconModel(1))
		));
		$result['title'] = '图标-添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('button_icon_info.html',array(
			'view'=>new ButtonIconView(new ButtonIconModel($id,1))
		));
		$result['title'] = '图标-编辑';
		Util::jsonExit($result);
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$name = _Post::get('name');
		if($name=='')
		{
			$result['error'] ="图标名称不能为空！";
			Util::jsonExit($result);
		}
		if(preg_match('/[^\w\.\/\-]/',$name))
		{
			$result['error'] ="样式名称不合法！";
			Util::jsonExit($result);
		}
		if(mb_strlen($name)>40)
		{
			$result['error'] ="图标名称长度不合法！";
			Util::jsonExit($result);
		}
		$olddo = array();
		$newdo=array(
			'name'=>$name
		);

		$newmodel =  new ButtonIconModel(2);
		$has = $newmodel->hasName($name);
		if($has)
		{
			$result['error'] ="图标已存在！";
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
		$id = _Post::getInt('id');
		$name = _Post::get('name');
		if($name=='')
		{
			$result['error'] ="图标名称不能为空！";
			Util::jsonExit($result);
		}
		if(preg_match('/[^\w\.\/\-]/',$name))
		{
			$result['error'] ="样式名称不合法！";
			Util::jsonExit($result);
		}
		if(mb_strlen($name)>40)
		{
			$result['error'] ="图标名称长度不合法！";
			Util::jsonExit($result);
		}


		$newmodel =  new ButtonIconModel($id,2);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id'=>$id,
			'name'=>$name
		);
		$has = $newmodel->hasName($name);
		if($has)
		{
			$result['error'] ="图标已存在！";
			Util::jsonExit($result);
		}
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
		$model = new ButtonIconModel($id,2);
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