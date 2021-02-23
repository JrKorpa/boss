<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillTypeController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 10:24:34
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillTypeController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//var_dump(Auth::getBar());exit;
		//Util::M('warehouse_bill_type','warehouse_shipping',21);	//生成模型后请注释该行
		//Util::V('warehouse_bill_type',21);	//生成视图后请注释该行
		//var_dump(Auth::getBar());exit;
		$this->render('warehouse_bill_type_search_form.html',
					array('bar'=>Auth::getBar())
		);
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
			'type_SN'		=> _Request::get("type_SN"),
			'type_name'		=> _Request::get("type_name"),
			'is_enabled'	=> _Request::get("is_enabled"),

		);
		//var_dump($args);exit;
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array(
			'is_enabled'=>$args['is_enabled'],
			'type_name' =>$args['type_name'],
			'type_SN'	=>$args['type_SN'],
			);

		$model = new WarehouseBillTypeModel(21);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_bill_type_search_page';
		$this->render('warehouse_bill_type_search_list.html',array(
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
		$this->dd = new DictView(new DictModel(1));
		$this->assign('dd',$this->dd);//数据字典
		$result['content'] = $this->fetch('warehouse_bill_type_info.html',array(
			'view'=>new WarehouseBillTypeView(new WarehouseBillTypeModel(21))
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
		$this->dd = new DictView(new DictModel(1));
		$this->assign('dd',$this->dd);//数据字典
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('warehouse_bill_type_info.html',array(
			'view'=>new WarehouseBillTypeView(new WarehouseBillTypeModel($id,21))
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		die('开发中');
		$id = intval($params["id"]);
		$this->render('warehouse_bill_type_show.html',array(
			'view'=>new WarehouseBillTypeView(new WarehouseBillTypeModel($id,1))
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result			= array('success' => 0,'error' =>'');
		$type_name		= _Post::get('type_name');
		$type_SN		= _Post::get('type_SN');
		$is_enabled		= _Post::getInt('is_enabled');
		$in_out			= _Post::getInt('in_out');
		$date_time		= date('Y-m-d H:i:s',time());
		//echo '<pre>';
		//print_r ($_POST);
		//echo '</pre>';
		//exit;
		//验证标识不能重复

		$olddo = array();
		$newdo=array(
			"type_name"		=>$type_name,
			"type_SN"		=>$type_SN,
			'is_enabled'	=>1,//默认开启
			'opra_time'		=>$date_time,
			'opra_name'     =>$_SESSION['realName'],
			'opra_ip'		=>Util::getClicentIp(),
			'opra_uid'		=>$_SESSION['userId'],
			'in_out'		=>$in_out
			);
		//var_dump($newdo);exit;
		$newmodel =  new WarehouseBillTypeModel(22);
		$num = $newmodel->check_type_SN($type_SN);
		//var_dump($num);exit;
		if ($num > 0)
		{
			$result['error'] = '单据标识不能重复';
			Util::jsonExit($result);
		}
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
		$id = _Post::getInt('id');
		$type_name		= _Post::get('type_name');
		$is_enabled		= _Post::getInt('is_enabled');
		//echo '<pre>';
		//print_r ($_POST);
		//echo '</pre>';
		//exit;

		$newmodel =  new WarehouseBillTypeModel($id,22);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			"id"			=>$id,
			"type_name"		=>$type_name,
			//'is_enabled'	=>$is_enabled,
		);

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
		$model = new WarehouseBillTypeModel($id,2);
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
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
}

?>