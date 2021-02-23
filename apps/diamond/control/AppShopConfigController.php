<?php
/**
 *  -------------------------------------------------
 *   @file		: AppShopConfigController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-18 14:53:19
 *   @update	:
 *  -------------------------------------------------
 */
class AppShopConfigController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('app_shop_config','front',17);	//生成模型后请注释该行
		//Util::V('app_shop_config',17);	//生成视图后请注释该行
		$this->render('app_shop_config_search_form.html', array('bar' => Auth::getBar()));
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
			'name'=>  _Request::getString('name'),
			'code'=>  _Request::getString('code')
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array(
				'name'=>  $args['name'],
				'code'=>  $args['code']
			);

		$model = new AppShopConfigModel(17);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_shop_config_search_page';
		$this->render('app_shop_config_search_list.html',array(
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
		$result['content'] = $this->fetch('app_shop_config_info.html',array(
			'view'=>new AppShopConfigView(new AppShopConfigModel(17))
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
		$result['content'] = $this->fetch('app_shop_config_info.html',array(
			'view'=>new AppShopConfigView(new AppShopConfigModel($id,17))
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
		$this->render('app_shop_config_show.html',array(
			'view'=>new AppShopConfigView(new AppShopConfigModel($id,17))
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
				'name'=>  _Request::getString('name'),
				'code'=>  _Request::getString('code'),
				'value'=>  _Request::getString('value')
			);
		if($newdo['name']==''){
			$result['error'] = '名称不能为空';
			Util::jsonExit($result);
		}

		if($newdo['code']==''){
			$result['error'] = '编码不能为空';
			Util::jsonExit($result);
		}

		if($newdo['value']==''){
			$result['error'] = '值不能为空';
			Util::jsonExit($result);
		}
        $model    = new AppShopConfigModel(18);
        $has      = $model->hasConfig($newdo['name'],$newdo['code']);
        if ($has){
            $result['error'] = "已经存在相同名称或编码,请重新填写";
            Util::jsonExit($result);
        }
		$newmodel =  new AppShopConfigModel(18);
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

		$newmodel =  new AppShopConfigModel($id,18);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
				'id'=>  $id,
				'name'=>  _Request::getString('name'),
				'code'=>  _Request::getString('code'),
				'value'=>  _Request::getString('value')
			);
		if($newdo['name']==''){
			$result['error'] = '名称不能为空';
			Util::jsonExit($result);
		}

		if($newdo['code']==''){
			$result['error'] = '编码不能为空';
			Util::jsonExit($result);
		}

		if($newdo['value']==''){
			$result['error'] = '值不能为空';
			Util::jsonExit($result);
		}
		
        $has      = $newmodel->hasConfig($newdo['name'],$newdo['code']);
        if ($has){
            $result['error'] = "已经存在相同名称或编码,请重新填写";
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
		$model = new AppShopConfigModel($id,18);
		$do = $model->getDataObject();
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