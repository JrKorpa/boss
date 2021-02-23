<?php
/**
 *  -------------------------------------------------
 *   @file		: UserWarehouseController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-30 10:30:05
 *   @update	:
 *  -------------------------------------------------
 */
class UserWarehouseController extends CommonController
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
		$this->render('user_warehouse_search_form.html',array('bar'=>Auth::getBar()));
	}

	/*
	*	leftList,仓库列表
	*/
	public function leftList ()
	{
		$model = new ApiWarehouseModel();
		$data = $model->getList();//调用仓库接口
		$this->render('user_warehouse_left_list.html',array('data'=>$data));
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
			'house_id'=>_Request::getInt('house_id'),
                        'search_field'=>  _Request::getString('search_field')
		);

		$page = _Request::getInt("page",1);

		$model = new UserWarehouseModel(1);
		$data = $model->pageList($args,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'user_warehouse_search_page';
		$this->render('user_warehouse_search_list.html',array(
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
		$house_id = _Post::getInt('id');
		$v = new UserWarehouseView(new UserWarehouseModel(1));
		$v->set_house_id($house_id);
		$result['content'] = $this->fetch('user_warehouse_info.html',array(
			'view'=>$v
		));
		$result['title'] = '关联库管';
		Util::jsonExit($result);
	}

	/**
	* batchAdd,批量添加
	*/
	public function batchAdd ()
	{
		$result = array('success' => 0,'error' => '');
		$v = new UserWarehouseView(new UserWarehouseModel(1));
                $model = new CompanyModel(1);
		$data = $model -> getCompanyTree();
//		$model = new ApiWarehouseModel();
//		$data = $model->getList();
		$result['content'] = $this->fetch('user_warehouse_batch_info.html',array(
			'view'=>$v,
			'company'=>$data
		));
		$result['title'] = '库管关联仓库';
		Util::jsonExit($result);
	}
        
        /**
         * getCompanyHouses,根据公司id取仓库
         */
        public function getCompanyHouses() {
                $id = _Post::getList('company_id');
                $model = new ApiWarehouseModel();
		$data = $model->getListByCompanyId(implode(',',$id));
		$this->render('user_warehouse_batch_info_options.html',array('data'=>$data));
        }

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$model = new UserWarehouseModel($id,1);
		$data = $model->getInfo();
		$this->render('user_warehouse_show.html',array(
			'data'=>$data,
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

		$house_id = _Post::getInt('house_id');
		$user_arr = array_unique(_Post::getList('user_arr'));
		if(count($user_arr)==0)
		{
			$result['error'] ="请选择用户！";
			Util::jsonExit($result);
		}
		$auth_check = _Post::getInt('auth_check');

//		$arr = array();
//		foreach ($user_arr as $key => $val )
//		{
//			$arr[$key]['house_id'] = $house_id;
//			$arr[$key]['user_id'] = $val;
//		}
		set_time_limit(0);
		$newmodel = new UserWarehouseModel(2);
		$res = $newmodel->saveWarehouseData($house_id,$user_arr,$auth_check);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "添加失败";
		}
		Util::jsonExit($result);
	}

	/**
	* batchInsert,批量入库
	*/
	public function batchInsert ()
	{
		$result = array('success' => 0,'error' =>'');
//		Util::L(time());
		//$user_id = _Post::getInt('user_id');
		$user_ids = _Post::getList('user_id');
		$house_ids = array_unique(_Post::getList('house_id'));
		if(count($house_ids)==0)
		{
			$result['error'] ="请选择仓库！";
			Util::jsonExit($result);
		}
		$auth_check = _Post::getInt('auth_check');
		set_time_limit(0);
		$newmodel = new UserWarehouseModel(2);

		//$res = $newmodel->batchInsertAll($user_id,$house_ids,$auth_check);
		//Util::L(time());
		foreach ($user_ids as $user_id){
		    $res = $newmodel->batchInsertAll($user_id,$house_ids,$auth_check);
    		if($res === false){
    			$result['error'] = "添加失败";
    			Util::jsonExit($result);
    		}
		}
		$result['success'] = 1;
		Util::jsonExit($result);
	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		/*
		$id = intval($params['id']);
		$model = new UserWarehouseModel($id,2);
		$res = $model->delete();
		*/
        $ids = _Post::getList('_ids');
        $del_arr=array();
        foreach ($ids as $key => $id) {
    		$model = new UserWarehouseModel($id,2);
    		$del_arr[]=array('user_id'=>$model->getValue('user_id'),'source_id'=>$model->getValue('house_id'));
    		$res = $model->delete();
        }	
        $res = $model->deletePermissionAll($del_arr);	
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

	/*
	*	listMenu,菜单列表
	*/
	public function listMenu ()
	{
		$user_id = _Post::getInt('user_id');
		$house_id = _Post::getInt('house_id');
		$this->render('user_warehouse_permission_menu_list.html',array('user_id'=>$user_id,'house_id'=>$house_id));
	}

	/*
	*	menuList,仓库菜单列表
	*/
	public function menuList ()
	{
		$user_id = _Request::getInt('user_id');
		$house_id = _Request::getInt('house_id');
		$model = new UserWarehouseModel(1);
		$data = $model->getMenuData($user_id,$house_id);
		Util::jsonExit($data);
	}

	/*
	*	saveMenu,菜单授权
	*/
	public function saveMenu ()
	{
		$result = array('success' => 0,'error' => '');
		$user_id = _Post::getInt('user_id');
		$house_id = _Post::getInt('house_id');
		$ids = _Post::getList('pids');
		$model = new UserWarehouseModel(2);
		$res = $model->saveMenu($user_id,$house_id,$ids);
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
		$house_id = _Request::getInt('house_id');
		$model = new UserWarehouseModel(1);
		$data =$model->getOperation($user_id,$house_id);
		if($data)
		{
			$this->render('user_warehouse_show_opr.html',array(
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
		$house_id = _Post::getInt('house_id');
		$model = new UserWarehouseModel(2);
		$res = $model->saveOperation($user_id,$house_id,$oprs);

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
		$house_id = _Request::getInt('house_id');
		$model = new UserWarehouseModel(1);
		$data = $model->getListButton($user_id,$house_id);
		if($data)
		{
			$this->render('user_warehouse_show_list_button.html',array(
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
		$house_id = _Post::getInt('house_id');
		$model = new UserWarehouseModel(2);
		$res = $model->saveListButton($user_id,$house_id,$btns);

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
		$house_id = _Request::getInt('house_id');
		$model = new UserWarehouseModel(1);
		$data = $model->getViewButton($user_id,$house_id);
		if($data)
		{
			$this->render('user_warehouse_show_view_button.html',array(
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
		$house_id = _Post::getInt('house_id');
		$model = new UserWarehouseModel(2);
		$res = $model->saveViewButton($user_id,$house_id,$btns);

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
		$house_id = _Request::getInt('house_id');
		$model = new UserWarehouseModel(1);
		$data = $model->getSubdetail($user_id,$house_id);
		if($data)
		{
			$this->render('user_warehouse_show_subdetail.html',array(
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
		$house_id = _Post::getInt('house_id');
		$model = new UserWarehouseModel(2);
		$res = $model->saveSubdetail($user_id,$house_id,$dtls);

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
		$house_id = _Request::getInt('house_id');
		$model = new UserWarehouseModel(1);
		$data = $model->getSubdetailButton($user_id,$house_id);
		if($data)
		{
			$this->render('user_warehouse_show_subdetail_button.html',array(
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
		$house_id = _Post::getInt('house_id');
		$model = new UserWarehouseModel(2);
		$res = $model->saveSubdetailButton($user_id,$house_id,$btns);

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
		$house_id = _Request::getInt('house_id');
		$model = new UserWarehouseModel(1);
		$data = $model->getSubdetailOperation($user_id,$house_id);
		if($data)
		{
			$this->render('user_warehouse_show_subdetail_operation.html',array(
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
		$house_id = _Post::getInt('house_id');
		$model = new UserWarehouseModel(2);
		$res = $model->saveSubdetailOperation($user_id,$house_id,$oprs);

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
		$house_id = _Post::getInt('house_id');

		$model = new UserWarehouseModel(2);
		$data = $model->listScope($user_id,$house_id);
		if($data)
		{
			$this->render('user_warehouse_show_scope_list.html',array('user_id'=>$user_id,'house_id'=>$house_id,'data'=>$data));
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
		$house_id = _Post::getInt('house_id');
		$data = _Post::getList('s');
		$model = new UserWarehouseModel(2);
		$res = $model->saveScope($user_id,$house_id,$data);

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
		$id = _Post::getInt('id');//仓库管控记录id
                $model = new UserWarehouseModel($id,1);
                $do=$model->getDataObject();
		$data = $model->getHouseKeepers();
		$result['content'] = $this->fetch('user_warehouse_copy_list.html',array('id'=>$id,'user_id'=>$do['user_id'],'data'=>$data));
		$result['title'] = '复制用户仓库权限';
		Util::jsonExit($result);
	}

	public function getHouses ()
	{
		$user_id = _Post::getInt('user_id');
		$uid = _Post::getInt('uid');
		$id = _Post::getInt('id');

                $model = new ApiWarehouseModel();
		$data = $model->getList();
                $model = new UserWarehouseModel($id,1);
		$data1 = $model->getWareList($user_id);
                $ids = array_column($data1,'house_id');
                $newArr = array();
                $sameid=0;
                if($uid==$user_id){
                     $do=$model->getDataObject();
                     $sameid = $do['house_id'];
                }
                if(is_array($ids) && count($ids))
                {
                        foreach ($data as $value) 
                        {
                                if(in_array($value['id'], $ids) && $sameid!=$value['id'])
                                {
                                        $newArr[] = $value;
                                }
                        }
                }
		$this->render('user_warehouse_copy_options.html',array('data'=>$newArr));
	}

	public function savePermission ()
	{
		$result = array('success' => 0,'error' => '');
		$id = _Post::getInt('id');//记录id
		$user_id = _Post::getInt('user_id');
		$house_id = _Post::getList('house_id');
		$model = new UserWarehouseModel($id,2);
		$do = $model->getDataObject();
		$res = $model->savePermission($do['user_id'],$do['house_id'],$user_id,$house_id);
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