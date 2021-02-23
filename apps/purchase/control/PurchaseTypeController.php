<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseTypeController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-07 20:29:42
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseTypeController extends Controller
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('purchase_type','purchase',23);	//生成模型后请注释该行
		//Util::V('purchase_type',23);	//生成视图后请注释该行
               $model = new PurchaseTypeModel(23);
		$this->render('purchase_type_search_form.html',array(
                        'bar'=>Auth::getBar(),
                        'view'=>new PurchaseTypeView(new PurchaseTypeModel(23)),
                         )
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
			't_name'=> _Request::get("t_name"),
			'is_enabled'=> _Request::get("is_enabled") === ""?'':_Request::getInt("is_enabled")
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array('t_name'=>$args['t_name'],'is_enabled'=>$args['is_enabled']);
		$model = new PurchaseTypeModel(23);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'purchase_type_search_page';
		$this->render('purchase_type_search_list.html',array(
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
		$result['content'] = $this->fetch('purchase_type_info.html',array(
			'view'=>new PurchaseTypeView(new PurchaseTypeModel(23)),
			'dd' => new DictView(new DictModel(1))
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
		$result['content'] = $this->fetch('purchase_type_info.html',array(
			'view'=>new PurchaseTypeView(new PurchaseTypeModel($id,23)),
			'dd' => new DictView(new DictModel(1))
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
		$this->render('purchase_type_show.html',array(
			'view'=>new PurchaseTypeView(new PurchaseTypeModel($id,1))
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$t_name = _Post::get('t_name');
		$is_auto = _Post::get('is_auto');
		$is_enabled = _Post::getInt('is_enabled');
         
		if($t_name=='')
		{
			$result['error'] ="采购分类名称不能为空！";
			Util::jsonExit($result);
		}

		if(!preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $t_name))
		{
			$result['error'] ="采购分类名称只能是汉字！";
			Util::jsonExit($result);
		}
		$newmodel =  new PurchaseTypeModel(24);
		$row = $newmodel->getOfname($t_name);
		if(count($row))
		{
			$result['error'] ="采购分类名称已存在，请检查。";
			Util::jsonExit($result);
		}
		$olddo = array();
		$newdo=array(
			"t_name"=>$t_name,
			"is_auto"=>$is_auto,
			"is_enabled"=>$is_enabled
		);
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
		$t_name = _Post::get('t_name');
		$is_auto = _Post::get('is_auto');
		$is_enabled = _Post::getInt('is_enabled');

		if($t_name=='')
		{
			$result['error'] ="采购分类名称不能为空！";
			Util::jsonExit($result);
		}

		if(!preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $t_name))
		{
			$result['error'] ="采购分类名称只能是汉字！";
			Util::jsonExit($result);
		}

		$id = _Post::getInt('id');
		$newmodel =  new PurchaseTypeModel($id,24);
		$olddo = $newmodel->getDataObject();
		$newdo=array(
			"id"=>$id,
			"t_name"=>$t_name,
			"is_auto"=>$is_auto,
			"is_enabled"=>$is_enabled
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

		$pinfo_model = new PurchaseInfoModel(23);
		$c = $pinfo_model->getCountForType($id);
		if($c)
		{
			$result['error'] = "此分类下有数据不能删除";
		}else{
			$model = new PurchaseTypeModel($id,24);

			$is_system = $model->getValue('is_system');
			if($is_system == 1){
				$result['error'] = "系统内置的分类，严禁删除...";
			}else{
				$model->setValue('is_deleted',1);
				$res = $model->save(true);
				if($res !== false){
					$result['success'] = 1;
				}else{
					$result['error'] = "删除失败";
				}
			}

		}
		Util::jsonExit($result);
	}
}

?>