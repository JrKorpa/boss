<?php
/**
 *  -------------------------------------------------
 *   @file		: ButtonController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-24 11:41:04
 *   @update	:
 *  -------------------------------------------------
 */
class ButtonController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('button_search_form.html',array('view'=>new ButtonView(new ButtonModel(1)),'bar'=>Auth::getBar()));
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
			'label'	=>_Request::get("label"),
			'tips'	=>_Request::get("tips"),
			'c_id'	=>_Request::getint("c_id"),
			'is_deleted'=>0,
		);
		$page = _Request::getInt("page",1);
		$where = array();
		$where['label'] = $args['label'];
		$where['tips'] = $args['tips'];
		$where['c_id'] = $args['c_id'];
		$where['is_deleted']=$args['is_deleted'];
		$model = new ButtonModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'button_search_page';
		$this->render('button_search_list.html',array(
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
		$result['content'] = $this->fetch('button_info.html',array(
			'view'=>new ButtonView(new ButtonModel(1))
		));
		$result['title']='按钮-添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('button_info.html',array(
			'view'=>new ButtonView(new ButtonModel($id,1))
		));
		$result['title']='按钮-编辑';
		Util::jsonExit($result);
	}

	/**
	 *	getControls，ajax
	 */
	public function getControls ()
	{
		$a_id = _Post::getInt('app_id');
		$model = new ButtonModel(1);
		$data = $model->getControls($a_id);
		$this->render('button_info_options.html',array('data'=>$data));
	}

	/**
	 *	getOperations，ajax
	 */
	public function getOperations ()
	{
		$c_id = _Post::getInt('c_id');
		$model = new ButtonModel(1);
		$data = $model->getOperations($c_id);
		$this->render('button_info_options2.html',array('data'=>$data));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$label = _Post::get('label');
		$type = _Post::getInt('type');
		$tips = _Post::get('tips');
		$class_id = _Post::getInt('class_id');
		$function_id = _Post::getInt('function_id');
		$icon_id = _Post::getInt('icon_id');
		$a_id = _Post::getInt('a_id');
		$c_id = _Post::getInt('c_id');
		$o_id = _Post::getInt('o_id');
		$data_url = _Post::get('data_url');
		$data_title = _Post::get('data_title');
		$cust_function = _Post::get('cust_function');
		
		$display_order = _Post::getInt('display_order');
		$display_order = $display_order>0?$display_order:time();

		if($label=='')
		{
			$result['error'] ="按钮名称不能为空！";
			Util::jsonExit($result);
		}
		/* if(mb_strlen($label)>10)
		{
			$result['error'] ="按钮名称最长10个汉字！";
			Util::jsonExit($result);
		} */
		if(!Util::isChinese($label))
		{
			$result['error'] ="按钮名称只能填汉字！";
			Util::jsonExit($result);
		}
		if(!$class_id)
		{
			$result['error'] ="按钮样式不能为空！";
			Util::jsonExit($result);
		}
		if(!$function_id)
		{
			$result['error'] ="按钮事件不能为空！";
			Util::jsonExit($result);
		}
		if(!$icon_id)
		{
			$result['error'] ="按钮图标不能为空！";
			Util::jsonExit($result);
		}

		if(!in_array($type,array(1,2,3))){
			$result['error'] = "类型的信息不正确";
			Util::jsonExit($result);
		}

		if($function_id<5)
		{//同步、刷新、离开不需要地址
			$data_url="";
		}
		else
		{
			if($data_url=="")
			{
				$result['error'] ="按钮请求地址不能为空！";
				Util::jsonExit($result);
			}
		}

		if($function_id==21){
			if($cust_function=='')
			{
				$result['error'] ="自定义函数不能为空！";
				Util::jsonExit($result);
			}
		}
		else
		{
			$cust_function='';
		}

	/*	if($function_id==21)
		{
			if($data_title=="")
			{
				$result['error'] ="页签标题不能为空！";
				Util::jsonExit($result);
			}
		}*/
//		if(!Util::isLegal($tips))
//		{
//			$result['error'] ="按钮提示不合法！";
//			Util::jsonExit($result);
//		}
		/* if(mb_strlen($tips)>10)
		{
			$result['error'] ="按钮提示最多输入10个字符！";
			Util::jsonExit($result);
		} */
//		if(!Util::isLegal($data_title))
//		{
//			$result['error'] ="页签标题不合法！";
//			Util::jsonExit($result);
//		}
		/* if(mb_strlen($data_title)>10)
		{
			$result['error'] ="页签标题最多输入10个字符！";
			Util::jsonExit($result);
		} */
		$olddo = array();
		$newdo=array(
			"label"=>$label,
			"class_id"=>$class_id,
			"function_id"=>$function_id,
			"icon_id"=>$icon_id,
			"data_url"=>$data_url,
			"tips"=>$tips,
			"cust_function"=>$cust_function,
			"data_title"=>$data_title,
			"type"=>$type,
			"a_id"=>$a_id,
			"c_id"=>$c_id,
			"o_id"=>$o_id,
			'display_order'=>$display_order,
			'type'=>$type
		);

		$newmodel =  new ButtonModel(2);
		$has = $newmodel->has($newdo);
		if($has)
		{
			$result['error'] = '按钮已存在';
			Util::jsonExit($result);
		}
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$cModel = new ControlModel($c_id,1);
			$cdo = $cModel->getDataObject();
			$newmodel->makePermission(array('id'=>$res,'name'=>$cdo['label'].'-'.$label.'-按钮权限','type'=>'BUTTON'));
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
		$type = _Post::getInt('type');
		$tips = _Post::get('tips');
		$class_id = _Post::getInt('class_id');
		$function_id = _Post::getInt('function_id');
		$icon_id = _Post::getInt('icon_id');
		$a_id = _Post::getInt('a_id');
		$c_id = _Post::getInt('c_id');
		$o_id = _Post::getInt('o_id');
		$data_url = _Post::get('data_url');
		$data_title = _Post::get('data_title');
		$cust_function = _Post::get('cust_function');
		$display_order = _Post::getInt('display_order',time());


		if($label=='')
		{
			$result['error'] ="按钮名称不能为空！";
			Util::jsonExit($result);
		}
		/* if(mb_strlen($label)>15)
		{
			$result['error'] ="按钮名称最长15个字符！";
			Util::jsonExit($result);
		} */
		if(!Util::isChinese($label))
		{
			$result['error'] ="按钮名称只能填汉字！";
			Util::jsonExit($result);
		}
		if(!$class_id)
		{
			$result['error'] ="按钮样式不能为空！";
			Util::jsonExit($result);
		}
		if(!$function_id)
		{
			$result['error'] ="按钮事件不能为空！";
			Util::jsonExit($result);
		}
		if(!$icon_id)
		{
			$result['error'] ="按钮图标不能为空！";
			Util::jsonExit($result);
		}

		if(!in_array($type,array(1,2,3))){
			$result['error'] = "类型的信息不正确";
			Util::jsonExit($result);
		}

		if($function_id<5)
		{//同步、刷新、离开不需要地址
			$data_url="";
		}
		else
		{
			if($data_url=="")
			{
				$result['error'] ="按钮请求地址不能为空！";
				Util::jsonExit($result);
			}
		}

		if($function_id==21){
			if($cust_function=='')
			{
				$result['error'] ="自定义函数不能为空！";
				Util::jsonExit($result);
			}
		}
		else
		{
			$cust_function='';
		}

//		if($function_id==21)
//		{
//			if($data_title=="")
//			{
//				$result['error'] ="页签标题不能为空！";
//				Util::jsonExit($result);
//			}
//		}
//		if(!Util::isLegal($tips))
//		{
//			$result['error'] ="按钮提示不合法！";
//			Util::jsonExit($result);
//		}
		/* if(mb_strlen($tips)>15)
		{
			$result['error'] ="按钮提示最多输入15个字符！";
			Util::jsonExit($result);
		} */
//		if(!Util::isLegal($data_title))
//		{
//			$result['error'] ="页签标题不合法！";
//			Util::jsonExit($result);
//		}
		/* if(mb_strlen($data_title)>15)
		{
			$result['error'] ="页签标题最多输入15个字符！";
			Util::jsonExit($result);
		} */
		$newmodel =  new ButtonModel($id,2);
		$newdo=array(
			"id"=>$id,
			"label"=>$label,
			"class_id"=>$class_id,
			"function_id"=>$function_id,
			"icon_id"=>$icon_id,
			"data_url"=>$data_url,
			"tips"=>$tips,
			"cust_function"=>$cust_function,
			"data_title"=>$data_title,
			"a_id"=>$a_id,
			"c_id"=>$c_id,
			"o_id"=>$o_id,
			"type"=>$type,
		    "display_order"=>$display_order
		);
		$has = $newmodel->has($newdo);
		if($has)
		{
			$result['error'] = '按钮已存在';
			Util::jsonExit($result);
		}
		$olddo = $newmodel->getDataObject();

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$cModel = new ControlModel($c_id,1);
			$cdo = $cModel->getDataObject();
			$newmodel->makePermission(array('id'=>$id,'name'=>$cdo['label'].'-'.$label.'-按钮权限','type'=>'BUTTON'));
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
		$model = new ButtonModel($id,2);
		$do = $model->getDataObject();
		if($do['is_system'])
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}

		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		if($res !== false){
			$model->deletePermission(array('id'=>$id,'type'=>'BUTTON'));
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

	/**
	 *	ajax,根据type生成事件下拉
	 */
	public function listAll()
	{
		$type=_Post::getInt('type');
		$model=new ButtonModel(1);
		$data = $model->listAlls($type);
		return $this->render('button_info_options1.html',array('data'=>$data));
	}
}

?>