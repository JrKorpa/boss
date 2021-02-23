<?php
/**
 *  -------------------------------------------------
 *   @file		: AppPayRealController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-27 12:17:56
 *   @update	:
 *  -------------------------------------------------
 */
class AppPayRealController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//所属公司
		 $company_list  = array(
				'58'	=>	'总公司'
			);
		//应付类型
		 $payType = array(
			1=>'代销借货',
			2=>'成品采购',
			3=>'石包采购',
		);
		$model = new AppPayRealModel(29);
		$jiesuanshang_list = $model->getJiesuanshangList();
		$jizhang_list = $model->getJiezhangList();
		$new_year = array();
		foreach($jizhang_list as $k=>$v)
		{
			$new_year[$k] = $v['year'];
		}

		$new_years = $model->getJiezhangLists();
		$newyearss  = $new_years[0]['year'];
		$rel = $model->getJiezhangInfoList($newyearss);
		foreach ($rel as $value)
		{
			$qihao_all[] = $value['qihao'];
		}
				
		$this->render('app_pay_real_search_form.html',array('bar'=>Auth::getBar(),'company_list'=>$company_list,'j_list'=>$jiesuanshang_list,'payType'=>$payType,'year_list'=>$new_year,'all_qihao_s'=>$qihao_all,'all_qihao_e'=>$qihao_all));
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
			'company' => _Request::getInt("company"),
			'prc_id' => _Request::getInt("prc_id"),
			'pay_type' => 	_Request::get('pay_type'),
			'pay_real_number' => 	_Request::get('pay_real_number'),
			'pay_number' => 	_Request::get('pay_number'),
			'make_name' => 	_Request::get('make_name'),
			'pay_time_s' => 	_Request::get('pay_time_s'),
			'pay_time_e' => 	_Request::get('pay_time_e'),
			'start_year' => 	_Request::get('start_year'),
			'start_qihao' => 	_Request::get('start_qihao'),
			'end_year' => 	_Request::get('end_year'),
			'end_qihao' => 	_Request::get('end_qihao'),
		);
		$page = _Request::getInt("page",1);
		$where = array(
			'company' => _Request::getInt("company"),
			'prc_id' => _Request::getInt("prc_id"),
			'pay_type' => 	_Request::get('pay_type'),
			'pay_real_number' => 	_Request::get('pay_real_number'),
			'pay_number' => 	_Request::get('pay_number'),
			'make_name' => 	_Request::get('make_name'),
			'pay_time_s' => 	_Request::get('pay_time_s'),
			'pay_time_e' => 	_Request::get('pay_time_e'),
			'start_year' => 	_Request::get('start_year'),
			'start_qihao' => 	_Request::get('start_qihao'),
			'end_year' => 	_Request::get('end_year'),
			'end_qihao' => 	_Request::get('end_qihao'),	
		);

		$model = new AppPayRealModel(29);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_pay_real_search_page';
		$this->render('app_pay_real_search_list.html',array(
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
		$result['content'] = $this->fetch('app_pay_real_info.html',array(
			'view'=>new AppPayRealView(new AppPayRealModel(29))
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
		$result['content'] = $this->fetch('app_pay_real_info.html',array(
			'view'=>new AppPayRealView(new AppPayRealModel($id,29)),
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
		$this->render('app_pay_real_show.html',array(
			'view'=>new AppPayRealView(new AppPayRealModel($id,29)),
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

		$newmodel =  new AppPayRealModel(30);
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

		$newmodel =  new AppPayRealModel($id,30);

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
		$model = new AppPayRealModel($id,30);
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