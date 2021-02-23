<?php
/**
 *  -------------------------------------------------
 *   @file		: MenuController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-09 17:58:07
 *   @update	:
 *  -------------------------------------------------
 */
class MenuController extends CommonController
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
		$this->render('menu_search_form.html',array('view'=>new MenuView(new MenuModel(1)),'bar'=>Auth::getBar()));
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
			'label'=>_Request::get('label'),
			'group_id'=>_Request::getInt('group_id'),
			'type'=>_Request::getInt('type'),
			'is_deleted'=>0
		);
		$page = _Request::getInt("page",1);
		$where = array();
		$where['label'] = $args['label'];
		$where['group_id'] = $args['group_id'];
		$where['type'] = $args['type'];
		$where['is_deleted'] = $args['is_deleted'];

		$model = new MenuModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'menu_search_page';
		$this->render('menu_search_list.html',array(
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
		$result['content'] = $this->fetch('menu_info.html',array(
			'view'=>new MenuView(new MenuModel(1))
		));
		$result['title'] = '菜单-添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('menu_info.html',array(
			'view'=>new MenuView(new MenuModel($id,1))
		));
		$result['title'] = '菜单-编辑';
		Util::jsonExit($result);
	}

	/**
	 *	getControls，ajax
	 */
	public function getControls ()
	{
		$a_id = _Post::getInt('app_id');
		$model = new MenuModel(1);
		$data = $model->getControls($a_id);
		$this->render('menu_info_options.html',array('data'=>$data));
	}

	/**
	 *	getOperations，ajax
	 */
	public function getOperations ()
	{
		$c_id = _Post::getInt('c_id');
		$model = new MenuModel(1);
		$data = $model->getOperations($c_id);
		$this->render('menu_info_options2.html',array('data'=>$data));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$application_id = _Post::getInt('application_id');
		$group_id = _Post::getInt('group_id');
		$c_id = _Post::getInt('c_id');
		$o_id = _Post::getInt('o_id');
		$icon = _Post::getInt('icon');
		$is_enabled = _Post::getInt('is_enabled');
		$label = _Post::get('label');
		$url= _Post::get('url');
		$code= strtoupper(_Post::get('code'));
		$type = _Post::getInt('type');
		$is_out = _Post::getInt('is_out');

		if($label=='')
		{
			$result['error'] ="菜单名称不能为空！";
			Util::jsonExit($result);
		}
		if(!Util::isLegal($label))
		{
			$result['error'] ="菜单名称只能填字母数字或汉字！";
			Util::jsonExit($result);
		}
		if(mb_strlen($label)>20)
		{
			$result['error'] ="菜单名称最长20个字符！";
			Util::jsonExit($result);
		}
		if($code=='')
		{
			$result['error'] ="编码不能为空！";
			Util::jsonExit($result);
		}
		if(mb_strlen($code)>40)
		{
			$result['error'] ="编码最长20个字符！";
			Util::jsonExit($result);
		}
		if($url=='')
		{
			$result['error'] ="菜单链接不能为空！";
			Util::jsonExit($result);
		}
		if(!$group_id)
		{
			$result['error'] ="请选择菜单组！";
			Util::jsonExit($result);
		}
		if(!$c_id)
		{
			$result['error'] ="请选择所属文件！";
			Util::jsonExit($result);
		}
		if(!$o_id)
		{
			$result['error'] ="请选择请求操作！";
			Util::jsonExit($result);
		}

		if(!$type)
		{
			$result['error'] ="请选择菜单类型！";
			Util::jsonExit($result);
		}

		$model =  new MenuModel(2);
		if($model->hasLabel($label))
		{
			$result['error'] = '菜单名称重复!';
			Util::jsonExit($result);
		}
		if($model->hasUrl($url))
		{
			$result['error'] = '菜单链接重复!';
			Util::jsonExit($result);
		}
		if($model->hasCode($code))
		{
			$result['error'] = '菜单编码重复!';
			Util::jsonExit($result);
		}
		$olddo = array();
		$newdo=array(
			'label'=>$label,
			'code'=>$code,
			'application_id'=>$application_id,
			'group_id'=>$group_id,
			'c_id'=>$c_id,
			'o_id'=>$o_id,
			'icon'=>$icon,
			'is_enabled'=>$is_enabled,
			'url'=>$url,
			'display_order'=>time(),
			'type'=>$type,
                        'is_out'=>$is_out
		);

		$res = $model->saveData($newdo,$olddo);
		if($res !== false)
		{
			$model->makePermission(array('id'=>$res,'name'=>$label.'-菜单权限','type'=>'MENU','code'=>$code.'_M'));
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
		$application_id = _Post::getInt('application_id');
		$group_id = _Post::getInt('group_id');
		$c_id = _Post::getInt('c_id');
		$o_id = _Post::getInt('o_id');
		$icon = _Post::getInt('icon');
		$is_enabled = _Post::getInt('is_enabled');
		$label = _Post::get('label');
		$url= _Post::get('url');
		$code= strtoupper(_Post::get('code'));
		$type = _Post::getInt('type');
		$is_out = _Post::getInt('is_out');

		if($label=='')
		{
			$result['error'] ="菜单名称不能为空！";
			Util::jsonExit($result);
		}
		if(!Util::isLegal($label))
		{
			$result['error'] ="菜单名称只能填字母数字或汉字！";
			Util::jsonExit($result);
		}
		if(mb_strlen($label)>20)
		{
			$result['error'] ="菜单名称最长20个字符！";
			Util::jsonExit($result);
		}
		if($code=='')
		{
			$result['error'] ="编码不能为空！";
			Util::jsonExit($result);
		}
		if($url=='')
		{
			$result['error'] ="菜单链接不能为空！";
			Util::jsonExit($result);
		}
		if(!$group_id)
		{
			$result['error'] ="请选择菜单组！";
			Util::jsonExit($result);
		}
		if(!$c_id)
		{
			$result['error'] ="请选择所属文件！";
			Util::jsonExit($result);
		}
		if(!$o_id)
		{
			$result['error'] ="请选择请求操作！";
			Util::jsonExit($result);
		}

		if(!$type)
		{
			$result['error'] ="请选择菜单类型！";
			Util::jsonExit($result);
		}

		$model =  new MenuModel($id,2);
		if($model->hasLabel($label,$id))
		{
			$result['error'] = '菜单名称重复!';
			Util::jsonExit($result);
		}
		if($model->hasUrl($url,$id))
		{
			$result['error'] = '菜单链接重复!';
			Util::jsonExit($result);
		}
		if($model->hasCode($code))
		{
			$result['error'] = '菜单编码重复!';
			Util::jsonExit($result);
		}
		$olddo = $model->getDataObject();
		$newdo=array(
			'id'=>$id,
			'label'=>$label,
			'code'=>$code,
			'application_id'=>$application_id,
			'group_id'=>$group_id,
			'c_id'=>$c_id,
			'o_id'=>$o_id,
			'icon'=>$icon,
			'is_enabled'=>$is_enabled,
			'url'=>$url,
			'type'=>$type,
                        'is_out'=>$is_out
		);

		$res = $model->saveData($newdo,$olddo);
		if($res !== false)
		{
			$model->makePermission(array('id'=>$id,'name'=>$label.'-菜单权限','type'=>'MENU','code'=>$code.'_M'));
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
		$model = new MenuModel($id,2);
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
			$model->deletePermission(array('id'=>$id,'type'=>'MENU'));
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
}
?>