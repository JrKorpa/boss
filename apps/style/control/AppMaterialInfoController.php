<?php
/**
 *  -------------------------------------------------
 *   @file		: AppMaterialInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 17:49:52
 *   @update	:
 *  -------------------------------------------------
 */
class AppMaterialInfoController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('app_material_info','front',11);	//生成模型后请注释该行
		//Util::V('app_material_info',11);	//生成视图后请注释该行
                
		$this->render('app_material_info_search_form.html',array('bar'=>Auth::getBar()));
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
            'material_name'=>  _Request::get('material_name'),
            'material_status'=>  _Request::get('material_status'),

		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array(
            'material_name'=> $args['material_name'],
            'material_status'=> $args['material_status']
        );

		$model = new AppMaterialInfoModel(11);
		$data = $model->pageList($where,$page,10,false);
 
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_material_search_page';
		$this->render('app_material_info_search_list.html',array(
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
		$result['content'] = $this->fetch('app_material_info_info.html',array(
			'view'=>new AppMaterialInfoView(new AppMaterialInfoModel(11))
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
		$result['content'] = $this->fetch('app_material_info_info.html',array(
			'view'=>new AppMaterialInfoView(new AppMaterialInfoModel($id,11))
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
		$this->render('app_material_info_show.html',array(
			'view'=>new AppMaterialInfoView(new AppMaterialInfoModel($id,11))
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
                $material_name = _Post::get('material_name');
				$price = _Post::getInt('price');
				$tax_point = _Post::getString('tax_point');
		//$is_enabled = _Post::getInt('is_enabled');
		$olddo = array();
		$newdo=array(
                    'material_name'=>$material_name,
                    'create_time'=>date("Y-m-d H:i:s"),
                    'material_status'=>1,
                    'tax_point'=>$tax_point,
                    'price'=>$price,
                    'create_user'=>$_SESSION['userName']
                );
		if(strlen($newdo['tax_point'])>6){
			$result['error'] = "税点最长只能输入6位数字!";
			Util::jsonExit($result);            
        }
		$newmodel =  new AppMaterialInfoModel(12);
		$ret= $newmodel->getMaterialName($material_name);
		if($ret&&strtolower($ret['material_name'])==strtolower($material_name)){
			$result['error'] = "材质名称已经存在!";
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
		$material_name = _Post::getString('material_name');
        $material_status = _Post::getInt('material_status');
        $tax_point = _Post::getString('tax_point');
        $price = _Post::getInt('price');
	

		$newmodel =  new AppMaterialInfoModel($id,12);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
                    'material_id'=>$id,
                    'material_name'=>$material_name,
                    'material_status'=>$material_status,
                    'tax_point'=>$tax_point,
                    'price'=>$price,
                    
		);
		if(strlen($newdo['tax_point'])>6){
			$result['error'] = "税点最长只能输入6位数字!";
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
		$model = new AppMaterialInfoModel($id,12);
		$do = $model->getDataObject();
		$status = $do['material_status'];
        if($status == 0){
            $result['error'] = "此条数据已经停用";
            Util::jsonExit($result);
        }
		$model->setValue('material_status',0);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}
	/**
	 *	delete，启用
	 */
	public function enable ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppMaterialInfoModel($id,12);
        $do = $model->getDataObject();
		$status = $do['material_status'];
        if($status == 1){
            $result['error'] = "此条数据已经启用";
            Util::jsonExit($result);
        }
		$model->setValue('material_status',1);
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