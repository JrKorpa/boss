<?php
/**
 *  -------------------------------------------------
 *   @file		: MaterialInventoryController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @date		: 2018-01-18 14:01:12
 *   @update	:
 *  -------------------------------------------------
 */
class MaterialInventoryJinController extends CommonController
{
	protected $smartyDebugEnabled = false;

	protected $whitelist = array('search');
	

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $proModel = new ApiProModel();
        $Processor_list = $proModel->GetSupplierList(array('status'=>1,'code'=>'wkc'));
	    $model = new MaterialInventoryJinModel(21);
		$this->render('material_inventory_search_form.html',
		    array(
		        'bar'=>Auth::getBar(),
		        'view'=>new MaterialInventoryJinView($model),
                'Processor_list'=>$Processor_list
		    ));
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
			//'参数' = _Request::get("参数");
            'goods_sn' => _Request::getString("goods_sn"),
            'warehouse_id' => _Request::getInt("warehouse_id"),
            'goods_name' => _Request::getString("goods_name"),
            'style_sn' => _Request::getString("style_sn"),
            'style_name' => _Request::getString("style_name"),
            'supplier_id' => _Request::getInt("supplier_id"),
            'catetory1' => _Request::getString("catetory1"),
            'catetory2' => _Request::getString("catetory2"),
            'catetory3' => _Request::getString("catetory3"),
            'goods_spec' => _Request::get("goods_spec"),
            'cost' => _Request::get("cost"),
            'number_index' => _Request::getString("number_index"),

		);
		$page = _Request::getInt("page",1);
		$where = $args;
		if(!empty($where['goods_sn'])){
		   $where['goods_sn'] = explode(' ',preg_replace("/\s+/is",' ',$where['goods_sn']));
		}
		//$where['group_by'] = 'yes';
		/**
		 * 判断是否是下载
		 */
		$dow_info = empty(_Request::get('dow_info')) ? null : _Request::get('dow_info');

		$model = new MaterialInventoryJinModel(21);
		$data = $model->pageList($where,$page,30,false,$dow_info);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'material_inventory_search_page';
		$this->render('material_inventory_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
		    'view'=>new MaterialInventoryJinView($model)
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('material_inventory_info.html',array(
			'view'=>new MaterialInventoryJinView(new MaterialInventoryJinModel(21))
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
		$result['content'] = $this->fetch('material_inventory_info.html',array(
			'view'=>new MaterialInventoryJinView(new MaterialInventoryJinModel($id,21)),
			'tab_id'=>$tab_id
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
		$this->render('material_inventory_show.html',array(
			'view'=>new MaterialInventoryJinView(new MaterialInventoryJinModel($id,21)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;
		$olddo = array();
		$newdo=array();

		$newmodel =  new MaterialInventoryJinModel(22);
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
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;

		$newmodel =  new MaterialInventoryJinModel($id,22);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
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
		$model = new MaterialInventoryJinModel($id,22);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		//联合删除？
		//$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
}

?>