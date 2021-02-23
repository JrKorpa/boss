<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 19:06:59
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseController extends Controller
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$company_model = new CompanyModel(1);
		$company_info = $company_model -> getCompanyTree();
		$this->assign('company_info',$company_info);//公司
                $this->dd = new DictView(new DictModel(1));
                $type=$this->dd->getEnumArray('warehouse.type');
                $this->assign('type', $type);
		$this->render('warehouse_search_form.html',array('bar'=>Auth::getBar()));
	}
	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$args = array(
			'mod'		=> _Request::get("mod"),
			'con'		=> substr(__CLASS__, 0, -10),
			'act'		=> __FUNCTION__,
			'code'      => _Request::get('code'),
			'name'      => _Request::get('name'),
			'company_id'=> _Request::get('company_id'),
			'is_delete' => _Request::getString('is_delete'),
                        'type' => _Request::getString('type')
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['name']		= $args['name'];
		$where['code']		= $args['code'];
		$where['is_delete'] = $args['is_delete'];
		$where['company_id'] = $args['company_id'];
                $where['type'] = $args['type'];
		$model = new WarehouseModel(21);
		$data = $model->pageList($where,$page,10,false);
		$company_model = new CompanyModel(1);
		foreach($data['data'] as $k => $row){
			$data['data'][$k]['company_name'] = $company_model -> getCompanyName($row['company_id']);
		}
		//var_dump($data);exit;
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_search_page';
		$this->render('warehouse_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'view' => new WarehouseView(new WarehouseModel(21)),
                        'dd'=>new DictView(new DictModel(1))
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$this->dd = new DictView(new DictModel(1));
		$company_model = new CompanyModel(1);
		$company_info = $company_model -> getCompanyTree();

		$this->assign('company_info',$company_info);//公司
		$this->assign('dd',$this->dd);//数据字典

		$type=$this->dd->getEnumArray('warehouse.type');
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('warehouse_info.html',array(
			'view'=>new WarehouseView(new WarehouseModel(21)),
			'type'=>$type
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params['id']);
		$this->dd = new DictView(new DictModel(1));
		$this->assign('dd',$this->dd);//数据字典
		$company_model = new CompanyModel(1);
		$company_info = $company_model -> getCompanyTree();
		$this->assign('company_info',$company_info);//公司
		$result = array('success' => 0,'error' => '');
                $type=$this->dd->getEnumArray('warehouse.type');
                $this->assign('type',$type);//公司
		$result['content'] = $this->fetch('warehouse_info.html',array(
			'view'=>new WarehouseView(new WarehouseModel($id,21))
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
		$name = _Post::get('name');
		$code = _Post::get('code');
		$remark = _Post::get('remark');
		$pid = _Post::getInt('pid');
        $type= _Post::get('type');
		$is_delete = 1;
		$date_time = date('Y-m-d H:i:s',time());

		if($name == ''){
			$result['error'] = '请输入仓库名!';
			Util::jsonExit($result);
		}
		if($code == ''){
			$result['error'] = '请输入仓库编号!';
			Util::jsonExit($result);
		}
                if($type == ''){
			$result['error'] = '请选择仓库类型!';
			Util::jsonExit($result);
		}
		if($pid == ''){
			$result['error'] = '请选择仓库所在的公司!';
			Util::jsonExit($result);
		}

		$olddo = array();
		$newdo=array(
			"name"=>$name,
			"code"=>$code,
			"remark"=>$remark,
			'create_time'=>$date_time,
			'create_user'=>$_SESSION['userName'],
			'is_delete'=>$is_delete,
            'type'=>$type
		);

		$newmodel =  new WarehouseModel(22);
		//检测仓库编号是否重复
		$flag = $newmodel->check_code($code);
		if ($flag)
		{
			$result['error'] = '仓库编码重复';
			Util::jsonExit($result);
		}
		$id = $newmodel->saveData($newdo,$olddo);

		//根据公司id 获取公司名
		$company_model = new CompanyModel(1);
		$company_name = $company_model->getCompanyName($pid);
		//添加公司仓库关联表
		$warehouserel_model  =  new WarehouseRelModel(22);
		$content = array(
			'warehouse_id'=>$id,
			'company_id'=>$pid,
			'create_time'=>$date_time,
			'company_name'=>$company_name
			);
		$res = $warehouserel_model->saveData($content,$olddo);

		//给新增的仓库自动添加一个默认柜位
		$box_info = array(
			'warehouse_id' => $id,
			'box_sn' => '0-00-0-0',
			'create_name' => 'system',
			'create_time' => $date_time,
		);
		$boxModel = new WarehouseBoxModel(22);
		$boxModel->saveData($box_info,$olddo);

		if($res !== false && $id !== false)
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
		$name = _Post::get('name');
		$code = _Post::get('code');
		$is_default= _Post::get('is_default')=="1" ? 1 : 0;
		$remark = _Post::get('remark');
		$pid = _Post::getInt('pid');//新的公司id
                $type = _Post::getInt('type');
		if($name == ''){
			$result['error'] = '请输入仓库名!';
			Util::jsonExit($result);
		}
		if($code == ''){
			$result['error'] = '请输入仓库编号!';
			Util::jsonExit($result);
		}
                if($type == ''){
			$result['error'] = '请选择仓库类型!';
			Util::jsonExit($result);
		}
		if($pid == ''){
			$result['error'] = '请选择仓库所在的公司!';
			Util::jsonExit($result);
		}

		$newmodel =  new WarehouseModel($id,22);
		$olddo = $newmodel->getDataObject();
		$date_time = date('Y-m-d H:i:s',time());

		$newdo=array(
			"id"=>$id,
			"name"=>$name,
			"code"=>$code,
			"is_default"=>$is_default,
			"remark"=>$remark,
                        "type"=>$type
		);

		//检查编号重复
		if( $olddo['code'] != $newdo['code'] )
		{
			if( $newmodel->check_code($code) ){
				$result['error'] = '仓库编码 '. $newdo["code"] . ' 已存在';
				Util::jsonExit($result);
			}
		}
		//公司是否修改
		$warehouserel_model  =  new WarehouseRelModel(22);
		$data = $warehouserel_model->select(array('warehouse_id'=>$id));
		if ($data)
		{
			//根据公司id 获取公司名
			$company_model = new CompanyModel(1);
			$company_name = $company_model->getCompanyName($pid);
			//var_dump($data);
			if ($data[0]['company_id'] != $pid)
			{
				//修改数据
				$content = array(
				'id'=>$data[0]['id'],
				'company_id'=>$pid,
				'company_name'=>$company_name
				);
				$warehouserel_model  =  new WarehouseRelModel($data[0]['id'],22);
				$res = $warehouserel_model->saveData($content,$warehouserel_model->getDataObject());
				//var_dump($res);exit;
			}
		}

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
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
		$model = new WarehouseModel($id,22);

		if($model->check_warehouse_goods($id)){
			$result['error'] = "该仓库下已经有商品入库，不能删除!";
			Util::jsonExit($result);
		}

		// $warehouserel_model  =  new WarehouseRelModel(22);
		// $warehouserel_model->del($id);

		$res = $model-> deleteList($id);

		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}


	/** 禁用仓库 **/
	public function stopWarehouse($params){
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);	//仓库ID
		$model = new WarehouseModel($id,22);

		if($model->check_warehouse_goods($id)){
			$result['error'] = "该仓库下已经有商品入库，不能禁用!";
			Util::jsonExit($result);
		}

		$res = $model->all_off($id);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "禁用失败";
		}
		Util::jsonExit($result);
	}

	/** 启用仓库 **/
	public function upWarehouse($params){
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new WarehouseModel($id,22);

		//检测所属公司 是否被删除
		$relModel = new WarehouseRelModel(21);
		$company_id = $relModel->GetCompanyByWarehouseId($id);
		$company_model = new CompanyModel($company_id, 1);
		$company_ex = $company_model->getValue('is_deleted');
		if($company_ex){
			$result['error'] = "不能启用，因为当前仓库所属公司已经被删除";
			Util::jsonExit($result);
		}

		$olddo = $model->getDataObject();
		$newdo = array('id'=>$id,'is_delete'=>1);
		$res = $model->saveData($newdo,$olddo);

		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "启用失败";
		}
		Util::jsonExit($result);
	}

	/** 检测仓库下是否有柜位 **/
	public function searchBoxByWarehouse($params){
		$warehouse_id = $params['id'];
		$result = array('success' => 0,'error' => '');
		$model = new WarehouseModel(21);
		$res = $model->checkBoxByWarehouse($warehouse_id);
		if($res){
			$result['success'] = 1;
			$result['error'] = '该仓库下有柜位存在';
		}else{
			$result['error'] = '该仓库下没有柜位';
		}
		Util::jsonExit($result);
	}
}

?>
