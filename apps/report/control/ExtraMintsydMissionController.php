<?php
/**
 *  -------------------------------------------------
 *   @file		: ExtraMintsydMissionController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-09-29 11:14:39
 *   @update	:
 *  -------------------------------------------------
 */
class ExtraMintsydMissionController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $model = new CompanyModel(1);
        $companys = $model->getCompanyTree();
		$this->render('extra_mintsyd_mission_search_form.html',array('bar'=>Auth::getBar(),'companys'=>$companys));
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
            'dep_id'   => _Request::get("dep_id"),
            'sale_name'   => _Request::get("sale_name"),
            'search_date'   => _Request::get("search_date"),
			//'参数' = _Request::get("参数");


		);
		$page = _Request::getInt("page",1);
		$where = array(
            'dep_id'=>$args['dep_id'],
            'sale_name'=>$args['sale_name'],
            'search_date'=>$args['search_date']
            );

		$model = new ExtraMintsydMissionModel(27);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'extra_mintsyd_mission_search_page';
		$this->render('extra_mintsyd_mission_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
        $model = new CompanyModel(1);
        $companys = $model->getCompanyTree();
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('extra_mintsyd_mission_info.html',array(
			'view'=>new ExtraMintsydMissionView(new ExtraMintsydMissionModel(27)),
            'companys'=>$companys
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
        $model = new CompanyModel(1);
        $companys = $model->getCompanyTree();
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('extra_mintsyd_mission_info.html',array(
			'view'=>new ExtraMintsydMissionView(new ExtraMintsydMissionModel($id,27)),
			'tab_id'=>$tab_id,
            'companys'=>$companys
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$this->render('extra_mintsyd_mission_show.html',array(
			'view'=>new ExtraMintsydMissionView(new ExtraMintsydMissionModel($id,27)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
        $tab_id = _Post::getInt('tab_id');
        $dep_id = _Post::get('dep_id');
        $sale_name = _Post::get('sale_name');
        $minimum_price = _Post::get('minimum_price');
        $tsyd_mission = _Post::get('tsyd_mission');
        $task_date = _Post::get('task_date');
        $dep = explode('|', $dep_id);
		$olddo = array();
		$newdo=array(
            'dep_id'=>$dep[0],
            'dep_name'=>$dep[1],
            'sale_name'=>$sale_name,
            'minimum_price'=>$minimum_price,
            'tsyd_mission'=>$tsyd_mission,
            'task_date'=>$task_date
            );
		$newmodel =  new ExtraMintsydMissionModel(28);
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
		$dep_id = _Post::get('dep_id');
        $sale_name = _Post::get('sale_name');
        $minimum_price = _Post::get('minimum_price');
        $tsyd_mission = _Post::get('tsyd_mission');
        $task_date = _Post::get('task_date');
        $dep = explode('|', $dep_id);

		$newmodel =  new ExtraMintsydMissionModel($id,28);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
            'id'=>$id,
            'dep_id'=>$dep[0],
            'dep_name'=>$dep[1],
            'sale_name'=>$sale_name,
            'minimum_price'=>$minimum_price,
            'tsyd_mission'=>$tsyd_mission,
            'task_date'=>$task_date
		);
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;	
			$result['title'] = '修改此处为想显示在页签上的字段';
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
		$model = new ExtraMintsydMissionModel($id,28);
		//$do = $model->getDataObject();
		//$valid = $do['is_system'];
		//if($valid)
		//{
			//$result['error'] = "当前记录为系统内置，禁止删除";
			//Util::jsonExit($result);
		//}
		//$model->setValue('is_deleted',1);
		//$res = $model->save(true);
		//联合删除？
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