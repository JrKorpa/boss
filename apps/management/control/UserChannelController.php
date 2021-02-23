<?php
/**
 *  -------------------------------------------------
 *   @file		: UserChannelController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-29 19:06:11
 *   @update	:
 *  -------------------------------------------------
 */
class UserChannelController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		if(Auth::$userType>1)
		{
			die('仅允许管理员操作！');
		}            
		$this->render('user_channel_search_form.html',array('bar'=>Auth::getBar(),'view'=>new UserChannelView(new UserChannelModel(1))));
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
			'user_id'=>_Request::getInt('user_id'),
                        'channel_id'=>  _Request::getInt('channel_id')
		);
		$page = _Request::getInt("page",1);

		$model = new UserChannelModel(1);
		$data = $model->pageList($args,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'user_channel_search_page';
		$this->render('user_channel_search_list.html',array(
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
		$result['content'] = $this->fetch('user_channel_info.html',array(
			'view'=>new UserChannelView(new UserChannelModel(1))
		));
		$result['title'] = '用户关联渠道';
		Util::jsonExit($result);
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$user_id = _Post::getList('user_id');
		$channel_id = array_unique(_Post::getList('channel_id'));
                $auth_check = _Post::getInt('auth_check');

		if(count($user_id)==0)
		{
			$result['error'] ="请选择渠道操作员！";
			Util::jsonExit($result);
		}
		if(count($channel_id)==0)
		{
			$result['error'] ="请选择渠道！";
			Util::jsonExit($result);
		}
                set_time_limit(0);
		$newmodel = new UserChannelModel(2);
		$res = $newmodel->saveUserChannelData($user_id,$channel_id,$auth_check);
		if($res !== false){
			$result['success'] = 1;
			$result['data'] = is_bool($res) ? '' : $res;
		}else{
			$result['error'] = "添加失败";
		}
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$model = new UserChannelModel($id,1);
		$data = $model->getInfo();
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('user_channel_info1.html',array(
			'data'=>$data
		));
		$result['title'] = '用户渠道数据权限';
		Util::jsonExit($result);
	}


	/**
	 *	update，更新信息入库
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');
		$power = _Post::getInt('power');

		$newmodel = new UserChannelModel($id,2);
		$newmodel->setValue('power',$power);
		$res = $newmodel->save(true);
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '保存失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$ids = _Post::getList('_ids');
        foreach ($ids as $key => $id) {
            $model = new UserChannelModel($id,2);
            $do = $model->getDataObject();
            $model->deleteExtendPermission($do['channel_id'],$do['user_id']);

            $res = $model->delete();
        }
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染授权页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$model = new UserChannelModel($id,1);
		$data = $model->getInfo();
		$this->render('user_channel_show.html',array(
			'data'=>$data,
			'bar'=>Auth::getViewBar()
		));
	}

	/*
	*	listMenu,菜单列表
	*/
	public function listMenu ()
	{
		$user_id = _Post::getInt('user_id');
		$channel_id = _Post::getInt('channel_id');
		$this->render('user_channel_permission_menu_list.html',array('user_id'=>$user_id,'channel_id'=>$channel_id));
	}

	/*
	*	menuList,渠道菜单列表
	*/
	public function menuList ()
	{
		$user_id = _Request::getInt('user_id');
		$channel_id = _Request::getInt('channel_id');
		$model = new UserChannelModel(1);
		$data = $model->getMenuData($user_id,$channel_id);
		Util::jsonExit($data);
	}

	/*
	*	saveMenu,渠道菜单授权
	*/
	public function saveMenu ()
	{
		$result = array('success' => 0,'error' => '');
		$user_id = _Post::getInt('user_id');
		$channel_id = _Post::getInt('channel_id');
		$ids = _Post::getList('pids');
		$model = new UserChannelModel(2);
		$res = $model->saveMenu($user_id,$channel_id,$ids);
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '保存失败';
		}
		Util::jsonExit($result);
	}

	/*
	*	listOpr,操作权限
	*/
	public function listOpr ()
	{
		$user_id = _Request::getInt('user_id');
		$channel_id = _Request::getInt('channel_id');
		$model = new UserChannelModel(1);
		$data =$model->getOperation($user_id,$channel_id);
		if($data)
		{
			$this->render('user_channel_show_opr.html',array(
				'data'=>$data
			));
		}
		else
		{
			die('没有操作');
		}

	}

	/*
	*	saveOpr,保存操作权限
	*/
	public function saveOpr ()
	{
		$oprs = _Post::getList('rbac');
		$user_id = _Post::getInt('user_id');
		$channel_id = _Post::getInt('channel_id');
		$model = new UserChannelModel(2);
		$res = $model->saveOperation($user_id,$channel_id,$oprs);

		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '保存失败';
		}
		Util::jsonExit($result);
	}

	/*
	*	listButton,列表按钮权限
	*/
	public function listButton ()
	{
		$user_id = _Request::getInt('user_id');
		$channel_id = _Request::getInt('channel_id');
		$model = new UserChannelModel(1);
		$data = $model->getListButton($user_id,$channel_id);
		if($data)
		{
			$this->render('user_channel_show_list_button.html',array(
				'data'=>$data
			));
		}
		else
		{
			die('没有按钮');
		}

	}

	/*
	*	saveListButton,保存列表授权
	*/
	public function saveListButton ()
	{
		$btns = _Post::getList('rbac');
		$user_id = _Post::getInt('user_id');
		$channel_id = _Post::getInt('channel_id');
		$model = new UserChannelModel(2);
		$res = $model->saveListButton($user_id,$channel_id,$btns);

		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '保存失败';
		}
		Util::jsonExit($result);
	}

	/*
	*	listViewButton,查看按钮权限
	*/
	public function listViewButton ()
	{
		$user_id = _Request::getInt('user_id');
		$channel_id = _Request::getInt('channel_id');
		$model = new UserChannelModel(1);
		$data = $model->getViewButton($user_id,$channel_id);
		if($data)
		{
			$this->render('user_channel_show_view_button.html',array(
				'data'=>$data
			));
		}
		else
		{
			die('没有按钮');
		}
	}

	/*
	*	saveViewButton,保存查看按钮
	*/
	public function saveViewButton ()
	{
		$btns = _Post::getList('rbac');
		$user_id = _Post::getInt('user_id');
		$channel_id = _Post::getInt('channel_id');
		$model = new UserChannelModel(2);
		$res = $model->saveViewButton($user_id,$channel_id,$btns);

		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '保存失败';
		}
		Util::jsonExit($result);
	}

	/*
	*	listRel,明细对象列表
	*/
	public function listRel ()
	{
		$user_id = _Request::getInt('user_id');
		$channel_id = _Request::getInt('channel_id');
		$model = new UserChannelModel(1);
		$data = $model->getSubdetail($user_id,$channel_id);
		if($data)
		{
			$this->render('user_channel_show_subdetail.html',array(
				'data'=>$data
			));
		}
		else
		{
			die('没有明细');
		}

	}

	/*
	*	saveSubdetail,保存明细
	*/
	public function saveSubdetail ()
	{
		$dtls = _Post::getList('rbac');
		$user_id = _Post::getInt('user_id');
		$channel_id = _Post::getInt('channel_id');
		$model = new UserChannelModel(2);
		$res = $model->saveSubdetail($user_id,$channel_id,$dtls);

		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '保存失败';
		}
		Util::jsonExit($result);
	}

	/*
	*	listRelButton,明细按钮列表
	*/
	public function listRelButton ()
	{
		$user_id = _Request::getInt('user_id');
		$channel_id = _Request::getInt('channel_id');
		$model = new UserChannelModel(1);
		$data = $model->getSubdetailButton($user_id,$channel_id);
		if($data)
		{
			$this->render('user_channel_show_subdetail_button.html',array(
				'data'=>$data
			));
		}
		else
		{
			die('没有明细或明细没有按钮');
		}

	}

	/*
	*	saveSubdetailButton,明细按钮保存
	*/
	public function saveSubdetailButton ()
	{
		$btns = _Post::getList('rbac');
		$user_id = _Post::getInt('user_id');
		$channel_id = _Post::getInt('channel_id');
		$model = new UserChannelModel(2);
		$res = $model->saveSubdetailButton($user_id,$channel_id,$btns);

		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '保存失败';
		}
		Util::jsonExit($result);
	}

	/*
	*	listRelOpr,明细操作列表
	*/
	public function listRelOpr ()
	{
		$user_id = _Request::getInt('user_id');
		$channel_id = _Request::getInt('channel_id');
		$model = new UserChannelModel(1);
		$data = $model->getSubdetailOperation($user_id,$channel_id);
		if($data)
		{
			$this->render('user_channel_show_subdetail_operation.html',array(
				'data'=>$data
			));
		}
		else
		{
			die('没有明细或明细没有操作');
		}
	}

	/*
	*	saveSubdetailOperation,明细操作授权
	*/
	public function saveSubdetailOperation ()
	{
		$oprs = _Post::getList('rbac');
		$user_id = _Post::getInt('user_id');
		$channel_id = _Post::getInt('channel_id');
		$model = new UserChannelModel(2);
		$res = $model->saveSubdetailOperation($user_id,$channel_id,$oprs);

		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '保存失败';
		}
		Util::jsonExit($result);
	}

	/*
	*	属性控制列表
	*/
	public function listScope ()
	{
		$user_id = _Post::getInt('user_id');
		$channel_id = _Post::getInt('channel_id');
		$model = new UserChannelModel(2);
		$data = $model->listScope($user_id,$channel_id);
		if($data)
		{
			$this->render('user_channel_show_scope_list.html',array('user_id'=>$user_id,'channel_id'=>$channel_id,'data'=>$data));
		}
		else
		{
			die('没有需要控制的属性');
		}
	}

	public function saveScope ()
	{
		$result = array('success' => 0,'error' => '');
		$user_id = _Post::getInt('user_id');
		$channel_id = _Post::getInt('channel_id');
		$data = _Post::getList('s');
		$model = new UserChannelModel(2);
		$res = $model->saveScope($user_id,$channel_id,$data);

		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '保存失败';
		}
		Util::jsonExit($result);
	}

	public function copyPermission ()
	{
		$result = array('success' => 0,'error' => '');
		$id = _Post::getInt('id');//渠道管控记录id
		$model = new UserChannelModel($id,1);
		$do = $model->getDataObject();
		$data = $model->getUsers($do['user_id']);
		$result['content'] = $this->fetch('user_channel_copy_list.html',array('id'=>$id,'data'=>$data));
		$result['title'] = '复制用户渠道权限';
		Util::jsonExit($result);
	}

	public function getChannels ()
	{
		$user_id = _Post::getInt('user_id');
		$id = _Post::getInt('id');
		$model = new UserChannelModel(1);
		$data = $model->getChannels($user_id,$id);
		$this->render('user_channel_copy_options.html',array('data'=>$data));
	}

	public function savePermission ()
	{
		$result = array('success' => 0,'error' => '');
		$id = _Post::getInt('id');//记录id
		$user_id = _Post::getInt('user_id');
		$channel_id = _Post::getList('channel_id');
		$model = new UserChannelModel($id,2);
		$do = $model->getDataObject();
		$res = $model->savePermission($do['user_id'],$do['channel_id'],$user_id,$channel_id);
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '保存失败';
		}
		Util::jsonExit($result);
	}
}

?>