<?php
/**
 *  -------------------------------------------------
 *   @file		: RegionController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-18 13:50:41
 *   @update	:
 *  -------------------------------------------------
 */
class RegionController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('region_search_form.html',array('bar'=>Auth::getBar()));
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
			'region_name'=>_Request::get("region_name"),
			'region_type'=>_Request::get('region_type')

		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['region_name']=$args['region_name'];
		$where['region_type']=$args['region_type'];
		$model = new RegionModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'region_search_page';
		$this->render('region_search_list.html',array(
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
		$result['content'] = $this->fetch('region_info.html',array(
			'view'=>new RegionView(new RegionModel(1))
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
		$result['content'] = $this->fetch('region_info.html',array(
			'view'=>new RegionView(new RegionModel($id,1))
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
		$region_name = _Post::getString('region_name');
		$region_type = _Post::getInt('region_type');
		$parent_id = _Post::getInt('parent_id');

		if($region_name==''){
			$result['error'] = "地区命名不能为空.";
			Util::jsonExit($result);
		}
		$res = Util::isHas($region_name,'region','region_name');
		if($res){
			$result['error'] = "地区命名重复.";
			Util::jsonExit($result);
		}
		if(!$region_type){
			$result['error'] = "地区类型不能为空.";
			Util::jsonExit($result);
		}

		if($region_type && !$parent_id){
			$result['error'] = "上级地区不能为空.";
			Util::jsonExit($result);
		}

		$olddo = array();
		$newdo=array(
			'region_name'=> $region_name,
				'region_type'=>$region_type,
			'parent_id'=>$parent_id
		);

		$newmodel =  new RegionModel(2);
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
		$id = _Post::getInt('region_id');
		$region_name = _Post::getString('region_name');
		$region_type = _Post::getInt('region_type');
		$parent_id = _Post::getInt('parent_id');

		$newmodel =  new RegionModel($id,2);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'region_name'=> $region_name,
			'region_type'=>$region_type,
			'parent_id'=>$parent_id,
			'region_id'=>$id
		);

		if($region_name==''){
			$result['error'] = "地区命名不能为空.";
			Util::jsonExit($result);
		}

		if(!isset($_REQUEST['region_type'])){
			$result['error'] = "地区类型不能为空.";
			Util::jsonExit($result);
		}

		if($region_type && !$parent_id){
			$result['error'] = "上级地区不能为空.";
			Util::jsonExit($result);
		}


		if($olddo['parent_id'] != $newdo['parent_id']){
				$res = Util::isHas($id,'region','parent_id');
				if($res){
					$result['error'] = "当前地区有子地区，禁止修改层级关系";
					Util::jsonExit($result);
				}
		}
		if($olddo['region_name']!=$newdo['region_name']){
			$res = Util::isHas($region_name,'region','region_name');
			if($res){
				$result['error'] = "地区命名重复";
				Util::jsonExit($result);
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
		$model = new RegionModel($id,2);
		$res = Util::isHas($id,'region','parent_id');
		if($res){
			$result['error'] = "当前地区有子地区，禁止删除";
			Util::jsonExit($result);
		}

		$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

	public function getparent_id()
	{
		$a_id = _Post::getInt('region_type')-1;
		$model = new RegionModel(1);
		$data = $model->getRegionType($a_id);
		$this->render('region_info_options2.html', array('data' => $data));
	}

}

?>