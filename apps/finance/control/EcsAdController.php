<?php
/**
 *  -------------------------------------------------
 *   @file		: EcsAdController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-27 14:32:16
 *   @update	:
 *  -------------------------------------------------
 */
class EcsAdController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('ecs_ad_search_form.html',array('view'=>new EcsAdView(new EcsAdModel(29)),'bar'=>Auth::getBar()));
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
			'company'=>_Request::get('company'),
			'from_ad'=>_Request::get('from_ad'),
			'select_type1'=>_Request::get('select_type1'),
			'start_year'=>_Request::get('start_year'),
			'start_qihao'=>_Request::get('start_qihao'),
			'end_year'=>_Request::get('end_year'),
			'end_qihao'=>_Request::get('end_qihao'),
			'pay_time_s'=>_Request::get('pay_time_s'),
			'pay_time_e'=>_Request::get('pay_time_e'),
			//'参数' = _Request::get("参数");


		);
		$page = _Request::getInt("page",1);
		$where = array();
        if($args['company'] != ''){
			$where['company'] = $args['company'];
		}

		if(!empty($args['from_ad'])){
			$where['from_ad'] = $args['from_ad'];
		}
        if($args['select_type1'] == 1)
		{	//按账期查询
			$zq_start_time = array();
			$zq_end_time = array();
			if(!empty($args['start_year'])){
				$zq_start_time['start_year'] = $args['start_year'];
			}
			if(!empty($args['start_qihao'])){
				$zq_start_time['start_qihao'] = $args['start_qihao'];
			}
			if(!empty($args['end_year'])){
				$zq_end_time['end_year'] = $args['end_year'];
			}
			if(!empty($args['end_qihao'])){
				$zq_end_time['end_qihao'] = $args['end_qihao'];
			}

			$payrealmodel = new PayRealModel();
			if($zq_start_time){
				$where['pay_tiime_start']  = $payrealmodel->getJiezhangtimes($zq_start_time);
			}
			if($zq_end_time){
				$where['pay_tiime_end']  = $payrealmodel->getJiezhangtimee($zq_end_time);
			}

		}else if($args['select_type1'] == 2){	//按时段查询
			if(!empty($args['pay_time_s'])){
				$where['pay_time_s'] = $args['pay_time_s'];
			}
			if(!empty($args['pay_time_e'])){
				$where['pay_time_e'] = $args['pay_time_e'];
			}
		}

		$model = new EcsAdModel(29);
		$data = $model->pageList($where,$page,10,false);
        $count = array('count_ys'=>0 , 'count_ss'=>0 , 'count_ds'=>0);	//count_ys 应收总计  count_ss  实收总计  count_ds  代收统计
		foreach ($data as $key => $value) {
			$count['count_ys'] += $value['ystotal'];
			$count['count_ss'] += $value['sytotal'];
			$count['count_ds'] += $value['dstotal'];
		}
       
        $_model = new PayJxcOrderModel(29);
        $adList = $_model->getAdList();
        $companyList = $model->getCompanyList();
		$pageData['jsFuncs'] = 'ecs_ad_search_page';
		$this->render('ecs_ad_search_list.html',array(
			'page_list'=>$data,
            'count'=>$count
		));
	}
    
    
    /**
     * 财务账期二级联动
     */
	function return_content()
	{
		$result = array('success' => 0,'error' => '');
		$year = !empty(_Request::getString('years'))?_Request::getString('years'):date('Y');
		$model = new EcsAdModel(29);
		$res_list = $model->getJiezhangInfoList($year);
		$htmls = '';
		foreach($res_list as $v)
		{
			$htmls .= "<option value='".$v['qihao']."'>".$v['qihao']."</option>";
		}
		$result['success'] = 1;
		$result['error'] = $htmls;
		Util::jsonExit($result);
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('ecs_ad_info.html',array(
			'view'=>new EcsAdView(new EcsAdModel(29))
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
		$result['content'] = $this->fetch('ecs_ad_info.html',array(
			'view'=>new EcsAdView(new EcsAdModel($id,29)),
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
		$this->render('ecs_ad_show.html',array(
			'view'=>new EcsAdView(new EcsAdModel($id,29)),
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

		$newmodel =  new EcsAdModel(30);
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

		$newmodel =  new EcsAdModel($id,30);

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
		$model = new EcsAdModel($id,30);
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