<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBoxController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 17:34:45
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBoxController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$dd = new DictModel(1);
		$warehouseModel = new WarehouseModel(21);
		$warehouse = $warehouseModel->select(array(), array('id', 'name'));
		$this->render('warehouse_box_search_form.html',array(
				'bar'=>Auth::getBar(),
				'view'=>new WarehouseBoxView(new WarehouseBoxModel(21)),
				'warehouse'=>$warehouse,
				'dd'=>$dd->getEnumArray('is_enabled'),
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
			'warehouse_id' =>_Request::get("warehouse_id"),
			'box_sn' =>_Request::get("box_sn"),
		);

		$page = _Request::getInt("page",1);
		$where = array(
			'warehouse_id'=>$args['warehouse_id'],
			'box_sn'=>$args['box_sn'],
		);

		$model = new WarehouseBoxModel(21);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_box_search_page';
		// echo '<pre>';print_r($data);echo '</pre>';
		$this->render('warehouse_box_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'view'=>new WarehouseBoxView(new WarehouseBoxModel(21)),
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$warehouseModel = new WarehouseModel(21);
		$warehouse = $warehouseModel->select(array(), array('id', 'name'));

		$result['content'] = $this->fetch('warehouse_box_info.html',array(
			'view'=>new WarehouseBoxView(new WarehouseBoxModel(21)),
			'warehouse' => $warehouse,
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
		$warehouseModel = new WarehouseModel(21);
		$warehouse = $warehouseModel->select(array(), array('id', 'name'));

		$result['content'] = $this->fetch('warehouse_box_info.html',array(
			'view'=>new WarehouseBoxView(new WarehouseBoxModel($id,21)),
			'tab_id'=>$tab_id,
			'warehouse' => $warehouse,
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
		$warehouse_id = $params['warehouse_id'];
		$box_sn = $params['box_sn'];
		$info = $params['info'];
		if(!$warehouse_id){
			$result['error'] = '请选择仓库';
			Util::jsonExit($result);
		}
		if($box_sn == ''){
			$result['error'] = '请输入货柜号';
			Util::jsonExit($result);
		}

		$newmodel =  new WarehouseBoxModel(22);
		$is_exist = $newmodel->checkRepeatBoxSn($box_sn,$warehouse_id);
		if($is_exist != ''){
			$result['error'] = '柜号：'.$box_sn.' 已经存在，不能重复添加！';
			Util::jsonExit($result);
		}

		$olddo = array();
		$newdo=array(
			'warehouse_id' => $warehouse_id,
			'box_sn' => $box_sn,
			'info' => $info,
			'create_time' => date('Y-m-d H:i:s'),
			'create_name' => $_SESSION['userName'],
		);
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['error'] = '添加成功';
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
		$newmodel =  new WarehouseBoxModel($id,22);
		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id'=> $id,
			'warehouse_id' => $params['warehouse_id'],
			'box_sn' => $params['box_sn'],
			'info' => $params['info'],
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;
			$result['title'] = '修改柜位';
			$result['error'] = '修改成功';
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
		$id = intval($params['id']);	//柜位ID
		$model = new WarehouseBoxModel($id,22);

		//检测柜位下是否有货品
		$existGoods = $model->checkGoodsInBox($id);
		if(!empty($existGoods)){
			$result['error'] = "该柜位下存在货品，不能禁用";
			Util::jsonExit($result);
		}

		$is_deleted = $model->getValue('is_deleted');
		if($is_deleted == 0){
			$result['error'] = "该柜位已经禁用，不能重复禁用";
			Util::jsonExit($result);
		}

		$model->setValue('is_deleted',0);
		$res = $model->save(true);

		if($res !== false){
			$result['success'] = 1;
			$result['error'] = "禁用成功";
		}else{
			$result['error'] = "禁用失败";
		}
		Util::jsonExit($result);
	}

	/**
	* start  启用柜位
	*/
	public function start($params){
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new WarehouseBoxModel($id,22);

		$is_deleted = $model->getValue('is_deleted');
		if($is_deleted == 1){
			$result['error'] = "该柜位已经启用，不能重复启用";
			Util::jsonExit($result);
		}

		//检测所属仓库是否开启
		$warehouse_id = $model->getValue('warehouse_id');
		$warehouseModel = new WarehouseModel($warehouse_id, 21);
		$warehouse_ex = $warehouseModel->getValue('is_delete');
		if(!$warehouse_ex){
			$result['error'] = "启用失败，该柜位所属仓库已禁用";
			Util::jsonExit($result);
		}

		$model->setValue('is_deleted',1);
		$res = $model->save(true);

		if($res !== false){
			$result['success'] = 1;
			$result['error'] = "启用成功";
		}else{
			$result['error'] = "启用失败";
		}
		Util::jsonExit($result);
	}
}

?>