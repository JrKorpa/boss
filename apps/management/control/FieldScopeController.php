<?php
/**
 *  -------------------------------------------------
 *   @file		: FieldScopeController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-06 09:27:11
 *   @update	:
 *  -------------------------------------------------
 */
class FieldScopeController extends CommonController
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
		$this->render('field_scope_search_form.html',array('bar'=>Auth::getBar(),'view'=>new FieldScopeView(new FieldScopeModel(1))));
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
			'c_id'=>_Request::getInt('c_id'),
			'label'=>_Request::get('label'),
			'code'=>_Request::get('code')
			//'参数' = _Request::get("参数");


		);
		$page = _Request::getInt("page",1);
		$where = array();
		$where['is_deleted']=$args['is_deleted'];
		$where['c_id']=$args['c_id'];
		$where['label']=$args['label'];
		$where['code']=$args['code'];

		$model = new FieldScopeModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'field_scope_search_page';
		$this->render('field_scope_search_list.html',array(
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
		$result['content'] = $this->fetch('field_scope_info.html',array(
			'view'=>new FieldScopeView(new FieldScopeModel(1))
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
		$result['content'] = $this->fetch('field_scope_info.html',array(
			'view'=>new FieldScopeView(new FieldScopeModel($id,1)),
			'tab_id'=>$tab_id
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
		$newdo = array();
		$newdo['label'] = _Post::get('label');
		$newdo['c_id'] = _Post::getInt('c_id');
		$newdo['code'] = _Post::get('code');
		$newdo['is_enabled'] = _Post::getInt('is_enabled');
		if($newdo['label']=='')
		{
			$result['error'] ="属性标识不能为空！";
			Util::jsonExit($result);
		}

		if(!Util::isChinese($newdo['label']))
		{
			$result['error'] ="属性标识只能是汉字！";
			Util::jsonExit($result);
		}
		if(mb_strlen($newdo['label'])>20)
		{
			$result['error'] ="属性标识不能超过20个汉字！";
			Util::jsonExit($result);
		}
		if(!$newdo['c_id'])
		{
			$result['error'] ="请选择控制器！";
			Util::jsonExit($result);
		}

		if($newdo['code']=='')
		{
			$result['error'] ="属性编码不能为空！";
			Util::jsonExit($result);
		}
		if(!preg_match('/^[a-z0-9_\.]+$/iu',$newdo['code']))
		{
			$result['error'] ="属性编码不合法！";
			Util::jsonExit($result);
		}
		if(mb_strlen($newdo['code'])>50)
		{
			$result['error'] ="属性编码不能超过50个字符！";
			Util::jsonExit($result);
		}

		$newmodel =  new FieldScopeModel(2);
		//查重
		if($newmodel->hasCode($newdo['code']))
		{
			$result['error'] ="属性重复！";
			Util::jsonExit($result);
		}

		$olddo = array();
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$cModel = new ControlModel($newdo['c_id'],1);
			$cdo = $cModel->getDataObject();
			$newmodel->makePermission(array('id'=>$res,'name'=>$cdo['label'].'-'.$newdo['label'].'-属性权限','type'=>'SCOPE','code'=>'SCOPE_'.strtoupper($newdo['code'])));
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

		$newdo = array();
		$newdo['id'] = _Post::getInt('id');
		$newdo['label'] = _Post::get('label');
		$newdo['c_id'] = _Post::getInt('c_id');
		$newdo['code'] = _Post::get('code');
		$newdo['is_enabled'] = _Post::getInt('is_enabled');
		if($newdo['label']=='')
		{
			$result['error'] ="属性标识不能为空！";
			Util::jsonExit($result);
		}

		if(!Util::isChinese($newdo['label']))
		{
			$result['error'] ="属性标识只能是汉字！";
			Util::jsonExit($result);
		}
		if(mb_strlen($newdo['label'])>20)
		{
			$result['error'] ="属性标识不能超过20个汉字！";
			Util::jsonExit($result);
		}
		if(!$newdo['c_id'])
		{
			$result['error'] ="请选择控制器！";
			Util::jsonExit($result);
		}

		if($newdo['code']=='')
		{
			$result['error'] ="属性编码不能为空！";
			Util::jsonExit($result);
		}
		if(!preg_match('/^[a-z0-9_\.]+$/iu',$newdo['code']))
		{
			$result['error'] ="属性编码不合法！";
			Util::jsonExit($result);
		}
		if(mb_strlen($newdo['code'])>50)
		{
			$result['error'] ="属性编码不能超过50个字符！";
			Util::jsonExit($result);
		}

		$newmodel =  new FieldScopeModel($newdo['id'],2);
		//查重
		if($newmodel->hasCode($newdo['code']))
		{
			$result['error'] ="属性重复！";
			Util::jsonExit($result);
		}

		$olddo = $newmodel->getDataObject();

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$cModel = new ControlModel($newdo['c_id'],1);
			$cdo = $cModel->getDataObject();
			$newmodel->makePermission(array('id'=>$newdo['id'],'name'=>$cdo['label'].'-'.$newdo['label'].'-属性权限','type'=>'SCOPE','code'=>'SCOPE_'.strtoupper($newdo['code'])));
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
		$model = new FieldScopeModel($id,2);
		$do = $model->getDataObject();
		if($do['is_enabled'])
		{
			$result['error'] = "当前记录已启用，禁止删除";
			Util::jsonExit($result);
		}
		$res = true;
		if(!$do['is_deleted'])
		{
			$model->setValue('is_deleted',1);
			$res = $model->save(true);
			$model->deletePermission(array('id'=>$id,'type'=>'SCOPE'));
		}
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
}

?>