<?php
/**
 *  -------------------------------------------------
 *   @file		: OperationController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-24
 *   @update	:
 *  -------------------------------------------------
 */
class OperationController extends Controller
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
			'is_deleted'=>0
		);
		$page = _Request::getInt("page",1);
		$where = array();
		$where['_id'] =$args['_id'];
		$where['is_deleted']=$args['is_deleted'];

		$model = new OperationModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'operation_search_page_1';
		$this->render('operation_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	add，显示添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('operation_info.html',array(
			'view'=>new OperationView(new OperationModel(1)),
			'_id'=>_Post::getInt('_id'),
		));
		$result['title'] = '操作-添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，显示修改页面
	 */
	public function edit ($params)
	{
		$result = array('success' => 0,'error' => '');
		$operation_id = intval($params["id"]);
		$tab_id = _Post::getInt('tab_id');//主记录对应的列表页签id
		$view = new OperationView(new OperationModel($operation_id,1));
		$result['content'] = $this->fetch('operation_info.html',array(
			'view'=>$view,
			'tab_id'=>$tab_id,
			'_id'=>$view->get_c_id(),
		));
		$result['title'] = '操作-编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，显示查看页面
	 */


	/**
	 *	insert，添加记录
	 */
	public function insert()
	{
		$result = array('success' => 0,'error' =>'');
		$method_name = _Post::get('method_name');
		$label = _Post::get('label');
		if($method_name=='')
		{
			$result['error'] ="方法名称不能为空！";
			Util::jsonExit($result);
		}
		if(!Util::isFields($method_name))
		{
			$result['error'] ="方法名称不合法！";
			Util::jsonExit($result);
		}
		if(mb_strlen($method_name)>30)
		{
			$result['error'] ="方法名称最多只能填30个汉字！";
			Util::jsonExit($result);
		}
		if($label=='')
		{
			$result['error'] ="显示标识不能为空！";
			Util::jsonExit($result);
		}
		if(!Util::isChinese($label))
		{
			$result['error'] ="显示标识只能填汉字！";
			Util::jsonExit($result);
		}
		if(mb_strlen($label)>30)
		{
			$result['error'] ="显示标识最多只能填30个汉字！";
			Util::jsonExit($result);
		}

		$c_id = _Post::getInt('_id');

		$olddo = array();
		$newdo=array(
			"method_name"=>$method_name,
			"label"=>$label,
			"c_id"=>$c_id
		);

		$newmodel =  new OperationModel(2);
		$has = $newmodel->has($newdo);
		if(!empty($has))
		{
			$result['error'] = '记录重复';
			Util::jsonExit($result);
		}
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$cModel = new ControlModel($c_id,1);
			$cdo = $cModel->getDataObject();
			$newmodel->makePermission(array('id'=>$res,'name'=>$cdo['label'].'-'.$label.'-操作权限','type'=>'OPERATION','code'=>strtoupper(Util::parseStr($cdo['code']).'_'.Util::parseStr($method_name)).'_O'));
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
	public function update ()
	{
		$result = array('success' => 0,'error' =>'');
		$operation_id = _Post::getInt('id');

		$method_name = _Post::get('method_name');
		$label = _Post::get('label');
		if($method_name=='')
		{
			$result['error'] ="方法名称不能为空！";
			Util::jsonExit($result);
		}
		if(!Util::isFields($method_name))
		{
			$result['error'] ="方法名称不合法！";
			Util::jsonExit($result);
		}
		if(mb_strlen($method_name)>30)
		{
			$result['error'] ="方法名称最多只能填30个汉字！";
			Util::jsonExit($result);
		}
		if($label=='')
		{
			$result['error'] ="显示标识不能为空！";
			Util::jsonExit($result);
		}
		if(!Util::isChinese($label))
		{
			$result['error'] ="显示标识只能填汉字！";
			Util::jsonExit($result);
		}
		if(mb_strlen($label)>30)
		{
			$result['error'] ="显示标识最多只能填30个汉字！";
			Util::jsonExit($result);
		}




		$model =  new OperationModel($operation_id,2);
		$olddo = $model->getDataObject();
		$newdo=array(
			"id"=>$operation_id,
			"method_name"=>$method_name,
			"label"=>$label
		);

		$tmpdo = $newdo;
		$tmpdo['c_id'] = $olddo['c_id'];

		$has = $model->has($tmpdo);
		if(!empty($has))
		{
			$result['error'] = '记录重复';
			Util::jsonExit($result);
		}
		$res = $model->saveData($newdo,$olddo);
		if($res !== false)
		{
			$cModel = new ControlModel($tmpdo['c_id'],1);
			$cdo = $cModel->getDataObject();
			$model->makePermission(array('id'=>$operation_id,'name'=>$cdo['label'].'-'.$label.'-操作权限','type'=>'OPERATION','code'=>strtoupper(Util::parseStr($cdo['code']).'_'.Util::parseStr($method_name)).'_O'));
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
		$_id = intval($params['id']);

		$model = new OperationModel($_id,2);
		$do = $model->getDataObject();
		if($do['is_system'])
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		if($model->hasRelData($_id))
		{
			$result['error'] = "存在关联数据，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);

		if($res !== false){
			$model->deletePermission(array('id'=>$_id,'type'=>'OPERATION'));
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
}

?>