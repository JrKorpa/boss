<?php
/**
 *  -------------------------------------------------
 *   @file		: ButtonFunctionController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-11 10:07:47
 *   @update	:
 *  -------------------------------------------------
 */
class ButtonFunctionController extends CommonController
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
		$this->render('button_function_search_form.html',array('bar'=>Auth::getBar()));
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
			'is_deleted'=>0,
		);
		$where['is_deleted']=$args['is_deleted'];
		$model = new ButtonFunctionModel(1);
		$data = $model->listAll($where);
		$this->render('button_function_search_list.html',array(
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('button_function_info.html',array(
			'view'=>new ButtonFunctionView(new ButtonFunctionModel(1))
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('button_function_info.html',array(
			'view'=>new ButtonFunctionView(new ButtonFunctionModel($id,1))
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$name = _Post::get('name');
		$label = _Post::get('label');
		$tips = _Post::get('tips');
		$type = _Post::get('type');
		if($name=='')
		{
			$result['error'] ="方法名不能为空！";
			Util::jsonExit($result);
		}
		if(!Util::isField($name))
		{
			$result['error'] ="方法名不合法！";
			Util::jsonExit($result);
		}
		if(mb_strlen($name)>40)
		{
			$result['error'] ="方法名长度不合法！";
			Util::jsonExit($result);
		}
		if($label=='')
		{
			$result['error'] ="显示值不能为空！";
			Util::jsonExit($result);
		}
		if(!Util::isChinese($label))
		{
			$result['error'] ="显示值不合法！";
			Util::jsonExit($result);
		}
		if(mb_strlen($label)>10)
		{
			$result['error'] ="显示值长度不合法！";
			Util::jsonExit($result);
		}
		if(mb_strlen($tips)>200)
		{
			$result['error'] ="使用提示长度不合法！";
			Util::jsonExit($result);
		}
		if(!$type)
		{
			$result['error'] ="请选择事件类型！";
			Util::jsonExit($result);
		}
		$olddo = array();
		$newdo=array(
			'name'=>$name,
			'label'=>$label,
			'tips'=>$tips,
			'type'=>$type
		);

		$newmodel =  new ButtonFunctionModel(2);
		if($newmodel->hasName($name))
		{
			$result['error'] ="方法名重复！";
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
		$label = _Post::get('label');
		$tips = _Post::get('tips');
		$type = _Post::get('type');
if($name=='')
		{
			$result['error'] ="方法名不能为空！";
			Util::jsonExit($result);
		}
		if(!Util::isField($name))
		{
			$result['error'] ="方法名不合法！";
			Util::jsonExit($result);
		}
		if(mb_strlen($name)>40)
		{
			$result['error'] ="方法名长度不合法！";
			Util::jsonExit($result);
		}
		if($label=='')
		{
			$result['error'] ="显示值不能为空！";
			Util::jsonExit($result);
		}
		if(!Util::isChinese($label))
		{
			$result['error'] ="显示值不合法！";
			Util::jsonExit($result);
		}
		if(mb_strlen($label)>10)
		{
			$result['error'] ="显示值长度不合法！";
			Util::jsonExit($result);
		}
		if(mb_strlen($tips)>200)
		{
			$result['error'] ="使用提示长度不合法！";
			Util::jsonExit($result);
		}
		if(!$type)
		{
			$result['error'] ="请选择事件类型！";
			Util::jsonExit($result);
		}

		$newmodel =  new ButtonFunctionModel($id,2);
		if($newmodel->hasName($name))
		{
			$result['error'] ="方法名重复！";
			Util::jsonExit($result);
		}
		$olddo = $newmodel->getDataObject();

		$newdo=array(
			'id'=>$id,
			'name'=>$name,
			'label'=>$label,
			'tips'=>$tips,
			'type'=>$type
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
		$model = new ButtonFunctionModel($id,2);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		if($model->hasRelData($id))
		{
			$result['error'] = "存在关联数据，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
}

?>