<?php
/**
 *  -------------------------------------------------
 *   @file		: ButtonClassController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-25 15:16:47
 *   @update	:
 *  -------------------------------------------------
 */
class ButtonClassController extends CommonController
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
		$this->render('button_class_search_form.html',array('bar'=>Auth::getBar()));
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

		$model = new ButtonClassModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'button_class_search_page';
		$this->render('button_class_search_list.html',array(
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
		$result['content'] = $this->fetch('button_class_info.html',array(
			'view'=>new ButtonClassView(new ButtonClassModel(1))
		));
		$result['title'] = '样式-添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('button_class_info.html',array(
			'view'=>new ButtonClassView(new ButtonClassModel($id,1))
		));
		$result['title'] = '样式-编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$this->render('button_class_show.html',array(
			'view'=>new ButtonClassView(new ButtonClassModel($id,1))
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$classname = _Post::get('classname');
		if($classname=='')
		{
			$result['error'] ="样式名称不能为空！";
			Util::jsonExit($result);
		}

		if(preg_match('/[^\w\.\/\-]/',$classname))
		{
			$result['error'] ="样式名称不合法！";
			Util::jsonExit($result);
		}
		if(mb_strlen($classname)>20)
		{
			$result['error'] ="样式名称长度不合法！";
			Util::jsonExit($result);
		}
		$olddo = array();
		$newdo=array(
			'classname'=>$classname
		);

		$newmodel = new ButtonClassModel(2);
		$has = $newmodel->hasClassName($classname);
		if($has)
		{
			$result['error'] ="样式已存在！";
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
		$classname = _Post::get('classname');
		if($classname=='')
		{
			$result['error'] ="样式名称不能为空！";
			Util::jsonExit($result);
		}
		if(preg_match('/[^\w\.\/\-]/',$classname))
		{
			$result['error'] ="样式名称不合法！";
			Util::jsonExit($result);
		}
		if(mb_strlen($classname)>20)
		{
			$result['error'] ="样式名称长度不合法！";
			Util::jsonExit($result);
		}

		$newmodel =  new ButtonClassModel($id,2);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id'=>$id,
			'classname'=>$classname
		);
		$has = $newmodel->hasClassName($classname);
		if($has)
		{
			$result['error'] ="样式已存在！";
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
		$model = new ButtonClassModel($id,2);
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