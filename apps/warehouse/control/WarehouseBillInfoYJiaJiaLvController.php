<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoYJiajialvController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-11-26 12:03:08
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoYJiaJiaLvController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$apiModel = new ApiStyleModel();
		$catType= $apiModel->getCatTypeInfo();
		//array_shift($catType);
		$model = new WarehouseBillInfoYJiajialvModel(21);
		
		$this->render('warehouse_bill_info_y_jiajialv_search_form.html',array(
                    'bar'=>Auth::getBar(),
                    'catType' => $catType,
					'data'=>$model->getJiajialvList(),
                    'dd' => new DictView(new DictModel(1)),
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


		);
		$page = _Request::getInt("page",1);

		
		$where = array(
			'style_type_id'=>_Request::getInt('style_type_id'),
			'id'=>_Request::getInt('id'),
			'create_time_s'=>_Request::getString('create_time_s'),
			'create_time_e'=>_Request::getString('create_time_e'),
			'check_time_s'=>_Request::getString('check_time_s'),
			'check_time_e'=>_Request::getString('check_time_e'),
			'creator' => _Request::getString('creator'),
			'checker'=>_Request::getString('checker'),
			'remark'=>_Request::getString('remark'),
			'status'=> _Request::getInt('status'),
	        /*'ssy_active'=> $args['ssy_active'],*///双十一活动
	    );
		$model = new WarehouseBillInfoYJiajialvModel(21);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_bill_info_y_jiajialv_search_page';
		$this->render('warehouse_bill_info_y_jiajialv_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function getJiaJiaLv ($params)
	{
		$model = new WarehouseBillInfoYJiajialvModel(22);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $model->getJiajialvByStyleTypeId(_Request::getInt("style_type_id"));
		Util::jsonExit($result);
	}
	
	
	/**
	 *	check，审核加价率
	 */
	public function check ($params)
	{
		$id = intval($params['id']);
		$model = new WarehouseBillInfoYJiajialvModel($id, 22);
		$data = $model->getDataObject();
		$result = array('success' => 0,'error' => '');
		if (!empty($data) && $data['status'] == 1 ){
			
			$model->check($id);
			$result['success'] = 1;
			$result['error'] = '加价率审核成功。';
		}else{
			
			$result['error'] = '该加价率状态不符合要求，无法审核';
		}
		
		$result['content'] = $model->getJiajialvByStyleTypeId(_Request::getInt("style_type_id"));
		Util::jsonExit($result);
	}
	
	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$model = new ApiStyleModel();
		$catType= $model->getCatTypeInfo();
		array_shift($catType);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('warehouse_bill_info_y_jiajialv_info.html',array(
			'catType' => $catType,
			'view'=>new WarehouseBillInfoYJiajialvView(new WarehouseBillInfoYJiajialvModel(21))
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
		$result['content'] = $this->fetch('warehouse_bill_info_y_jiajialv_info.html',array(
			'view'=>new WarehouseBillInfoYJiajialvView(new WarehouseBillInfoYJiajialvModel($id,21)),
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
		$this->render('warehouse_bill_info_y_jiajialv_show.html',array(
			'view'=>new WarehouseBillInfoYJiajialvView(new WarehouseBillInfoYJiajialvModel($id,21)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$olddo = array();
		
		$apiModel = new ApiStyleModel();
		$catType= $apiModel->GetCatType(_Request::getInt('style_type_id'));
		$style_type = $catType ? current($catType) : null;
		$style_type_name = isset($style_type) ? $style_type['name'] : '';
		
		$newdo=array(
			'style_type_id'=>_Request::getInt('style_type_id'),
			'style_type_name'=>$style_type_name,//物流信息
			'jiajialv'=>_Request::getString('jiajialv'),
			'active_date'=>_Request::getString('active_date'),
			'creator' => $_SESSION['userName'],
			'create_time'=> date("Y-m-d H:i:s",time()),
			'remark'=>_Request::getString('remark'),
			'status'=> '1'
		);

		$newmodel =  new WarehouseBillInfoYJiajialvModel(22);
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

		$newmodel =  new WarehouseBillInfoYJiajialvModel($id,22);

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
	 *	cancel，取消
	 */
	public function cancel ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new WarehouseBillInfoYJiajialvModel($id,22);
		$model->setValue('status',3);
		$res = $model->save(true);
		//联合删除？
		//$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
			$result['error'] = '加价率取消成功。';
		}else{
			$result['error'] = "取消失败";
		}
		Util::jsonExit($result);
	}
}

?>