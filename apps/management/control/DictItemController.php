<?php
/**
 *  -------------------------------------------------
 *   @file		: DictItemController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-20 17:05:57
 *   @update	:
 *  -------------------------------------------------
 */
class DictItemController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		die('我是明细，没有菜单列表');
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		if(Auth::$userType>2)
		{
			die('操作禁止');
		}
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'_id' => _Request::get("_id"),
			//'参数' = _Request::get("参数");
		);
		$page = _Request::getInt("page",1);

		$where = array();
		$where['_id'] = $args['_id'];

		$model = new DictItemModel(1);
		$data = $model->pageList($where,$page,10,false);

		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'dict_item_search_page';


		$this->render('dict_item_search_list.html',array(
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

		$result['content'] = $this->fetch('dict_item_info.html',array(
			'view'=>new DictItemView(new DictItemModel(1)),
			'_id'=>_Post::getInt('_id')
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
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$v =new DictItemView(new DictItemModel($id,1));
		$result['content'] = $this->fetch('dict_item_info.html',array(
			'view'=>$v,
			'tab_id'=>$tab_id,
			'_id'=>$v->get_dict_id()
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
		$dict_id = _Post::getInt('_id');
		$label = _Post::get('label');
		$note = _Post::get('note');

		if($label=='')
		{
			$result['error'] = "枚举值必填";
			Util::jsonExit($result);
		}
		if(mb_strlen($label)>10)
		{
			$result['error'] = "枚举值输入长度最多是10个字符";
			Util::jsonExit($result);
		}
		if(mb_strlen($note)>200)
		{
			$result['error'] = "描述输入长度最多是200个字符";
			Util::jsonExit($result);
		}

		$newmodel =  new DictItemModel(2);
		if($newmodel->has($label,$dict_id))
		{
			$result['error'] = "枚举值已存在";
			Util::jsonExit($result);
		}
		$olddo = array();
		$newdo=array(
			'name'=>$newmodel->getName($dict_id),
			'dict_id'=>$dict_id,
			'label'=>$label,
			'note'=>$note,
			'display_order'=>time()
		);

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
		$note = _Post::get('note');
		$dict_id = _Post::getInt('_id');

		if($label=='')
		{
			$result['error'] = "枚举值必填";
			Util::jsonExit($result);
		}
		if(mb_strlen($label)>10)
		{
			$result['error'] = "枚举值输入长度最多是10个字符";
			Util::jsonExit($result);
		}
		if(mb_strlen($note)>200)
		{
			$result['error'] = "描述输入长度最多是200个字符";
			Util::jsonExit($result);
		}

		$newmodel =  new DictItemModel($id,2);
		if($newmodel->has($label,$dict_id))
		{
			$result['error'] = "枚举值已存在";
			Util::jsonExit($result);
		}
		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id'=>$id,
			'label'=>$label,
			'note'=>$note
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '编辑失败';
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
		$model = new DictItemModel($id,2);
		$do=$model->getDataObject();
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
		$model = new DictItemModel($id,2);
		$do=$model->getDataObject();
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