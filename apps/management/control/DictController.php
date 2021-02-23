<?php
/**
 *  -------------------------------------------------
 *   @file		: DictController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-20 17:02:59
 *   @update	:
 *  -------------------------------------------------
 */
class DictController extends CommonController
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
		$this->render('dict_search_form.html',array('bar'=>Auth::getBar()));
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
			'label'=>_Request::get("label"),
			'name'=>_Request::get("name"),
		);
		$page = _Request::getInt("page",1);
		$where = array();
		$where['name']=$args['name'];
		$where['label']=$args['label'];
		$model = new DictModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'dict_search_page';
		$this->render('dict_search_list.html',array(
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
		$result['content'] = $this->fetch('dict_info.html',array(
			'view'=>new DictView(new DictModel(1))
		));
		$result['title'] = '字典-添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('dict_info.html',array(
			'view'=>new DictView(new DictModel($id,1)),
			'tab_id'=>$tab_id
		));
		$result['title'] = '字典-编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);

		$this->render('dict_show.html',array(
			'view'=>new DictView(new DictModel($id,1)),
			'bar'=>Auth::getViewBar(),
			'bar1'=>Auth::getDetailBar('dict_item')
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

		$name = _Post::getString('name');
		$label = _Post::getString('label');
		if($name=='')
		{
			$result['error'] = '属性不能为空';
			Util::jsonExit($result);
		}
		if(mb_strlen($name)>40)
		{
			$result['error'] = '属性输入长度最多是40';
			Util::jsonExit($result);
		}
		if(!preg_match('/^([a-z\._]+)$/i',$name))
		{
			$result['error'] = '属性输入不合法';
			Util::jsonExit($result);
		}

		if($label=='')
		{
			$result['error'] = '标识不能为空';
			Util::jsonExit($result);
		}
		if(mb_strlen($label)>20)
		{
			$result['error'] = '标识输入长度最多是20';
			Util::jsonExit($result);
		}

		$olddo = array();
		$newdo=array(
			'name'=>$name,
			'label'=>$label
		);

		$newmodel =  new DictModel(2);

		//这里对属性值得查重
		if($newmodel->hasName($name))
		{
			$result['error'] = '属性值重复请勿重复添加';
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
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');

		$id = _Post::getInt('id');

		$name = _Post::getString('name');
		$label = _Post::getString('label');
		if($name=='')
		{
			$result['error'] = '属性不能为空';
			Util::jsonExit($result);
		}
		if(mb_strlen($name)>40)
		{
			$result['error'] = '属性输入长度最多是40';
			Util::jsonExit($result);
		}
		if(!preg_match('/^([a-z\._]+)$/i',$name))
		{
			$result['error'] = '属性输入不合法';
			Util::jsonExit($result);
		}

		if($label=='')
		{
			$result['error'] = '标识不能为空';
			Util::jsonExit($result);
		}
		if(mb_strlen($label)>20)
		{
			$result['error'] = '标识输入长度最多是20';
			Util::jsonExit($result);
		}



		$newmodel =  new DictModel($id,2);
		//这里对属性值得查重
		if($newmodel->hasName($name))
		{
			$result['error'] = '属性值重复请勿重复添加';
			Util::jsonExit($result);
		}
		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'name'=>$name,
			'label'=>$label,
			'id'=>$id
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
	 *	delete，禁用
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new DictModel($id,2);
		$do = $model->getDataObject();
		if($do['is_deleted']==1)
		{
			$result['success'] = 1;
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
			$result['error'] = "禁用失败";
		}
		Util::jsonExit($result);
	}


	public function recover ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new DictModel($id,2);
		$do = $model->getDataObject();
		if($do['is_deleted']==0)
		{
			$result['success'] = 1;
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',0);
		$res = $model->save(true);

		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = "启用失败";
		}
		Util::jsonExit($result);
	}


}

?>