<?php
/**
 *  -------------------------------------------------
 *   @file		: AppStyleBaoxianfeeController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 11:11:16
 *   @update	: 
 *  -------------------------------------------------
 */
class AppStyleBaoxianfeeController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('app_style_baoxianfee','front',11);	//生成模型后请注释该行
		//Util::V('app_style_baoxianfee',11);	//生成视图后请注释该行
		$this->render('app_style_baoxianfee_search_form.html',array('bar'=>Auth::getBar()));
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
			'min'	=> _Request::getString('min'),
			'max'=> _Request::getString('max'),
			'price_min' => _Request::getString('price_min'),
			'price_max' => _Request::getString('price_max'),
			'status' => _Request::getString('status')
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['min'] = _Request::getString('min');
		$where['max'] = _Request::getString('max');
		$where['price_min'] = _Request::getString('price_min');
		$where['price_max'] = _Request::getString('price_max');
		$where['status'] = _Request::getString('status');
		$model = new AppStyleBaoxianfeeModel(11);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args; 
		$pageData['jsFuncs'] = 'app_style_baoxianfee_search_page';
		$this->render('app_style_baoxianfee_search_list.html',array(
			'pa'=>Util::page($pageData),
            'dd' => new DictView(new DictModel(1)),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_style_baoxianfee_info.html',array(
			'view'=>new AppStyleBaoxianfeeView(new AppStyleBaoxianfeeModel(11))
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
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_style_baoxianfee_info.html',array(
			'view'=>new AppStyleBaoxianfeeView(new AppStyleBaoxianfeeModel($id,11))
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
		$this->render('app_style_baoxianfee_show.html',array(
			'view'=>new AppStyleBaoxianfeeView(new AppStyleBaoxianfeeModel($id,1))
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

		$olddo = array();
		$newdo=array(
			'min' => _Request::getString('min'),
			'max' => _Request::getString('max'),
			'price' => _Request::getString('price'),			
			'status' => _Request::getString('status'),			
		);	

		if($newdo['min']=='' || $newdo['min']<0){
			$result['error'] = '最小值应该大于等于0的整数';
			Util::jsonExit($result);
		}
		if($newdo['max']==''  || $newdo['max']<0){
			$result['error'] = '最大值应该大于0的整数';
			Util::jsonExit($result);
		}
		if($newdo['price']==''){
			$result['error'] = '价格不能为空';
			Util::jsonExit($result);
		}
		if($newdo['status']==''){
			$result['error'] = '请选择状态';
			Util::jsonExit($result);
		}
		if($newdo['min']>$newdo['max']){
			$result['error'] = '最小值不能大于最大值';
			Util::jsonExit($result);
		}
		
		$newmodel =  new AppStyleBaoxianfeeModel(12);

        $one = $newmodel->getQujianByMax($newdo);
        if($one){
			$result['error'] = '该区间已存在！不能重复添加';
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
        $min_old = _Request::getString('min_old');
        $max_old = _Request::getString('max_old');

		$newmodel =  new AppStyleBaoxianfeeModel($id,12);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id' => $id,
			'min' => _Request::getString('min'),
			'max' => _Request::getString('max'),
			'price' => _Request::getString('price'),			
			'status' => _Request::getString('status'),										
		);
		if($newdo['min']==''){
			$result['error'] = '最小值不能为空';
			Util::jsonExit($result);
		}
		if($newdo['max']==''){
			$result['error'] = '最大值不能为空';
			Util::jsonExit($result);
		}
		if($newdo['price']==''){
			$result['error'] = '价格不能为空';
			Util::jsonExit($result);
		}
		if($newdo['status']==''){
			$result['error'] = '请选择状态';
			Util::jsonExit($result);
		}
		if($newdo['min']>$newdo['max']){
			$result['error'] = '最小值不能大于最大值';
			Util::jsonExit($result);
		}
		
        $Row = $newmodel->getQujianByMax($newdo);
        if($Row&&($newdo['min']!=$min_old || $newdo['max']!=$max_old)){
			$result['error'] = '该区间已存在！不能重复添加';
			Util::jsonExit($result);            
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
		$model = new AppStyleBaoxianfeeModel($id,12);
		$do = $model->getDataObject();
		$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	} 

	/**
	 *	start，启用
	 */
	public function start ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppStyleBaoxianfeeModel($id,12);
        $do = $model->getDataObject();
		$status = $do['status'];
		$model->setValue('status',1);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}

	/**
	 *	stop，停用
	 */
	public function stop ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppStyleBaoxianfeeModel($id,12);
        $do = $model->getDataObject();
		$status = $do['status'];
		$model->setValue('status',2);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}
}

?>