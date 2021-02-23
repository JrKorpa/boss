<?php
/**
 *  -------------------------------------------------
 *   @file		: AppCatTypeController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 10:11:01
 *   @update	:
 *  -------------------------------------------------
 */
class AppCatTypeController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('app_cat_type','front',11);	//生成模型后请注释该行
		//Util::V('app_cat_type',11);	//生成视图后请注释该行
		$this->render('app_cat_type_search_form.html',array('bar'=>Auth::getBar()));
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
//                        'cat_type_name'=>  _Request::get('cat_type_name'),
//                        'cat_type_status'=>  _Request::get('cat_type_status'),  
		);
        
		//$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
//		$where = array(
//                     'cat_type_name'=> $args['cat_type_name'],
//                     'cat_type_status'=> $args['cat_type_status']
//                );

		$model = new AppCatTypeModel(11);
		//$data = $model->pageList($where,$page,10,false);
        $data = $model->getList();
        $show_jiajialv = 'NO';
        if(defined('IS_ZHOUSHAN_SYS') && IS_ZHOUSHAN_SYS=='YES')
            $show_jiajialv = 'YES';
		
//		$pageData = $data;
//		$pageData['filter'] = $args;
//		$pageData['jsFuncs'] = 'app_cat_type_search_page';
		$this->render('app_cat_type_search_list.html',array(
			//'pa'=>Util::page($pageData),
			'data'=>$data,
			'show_jiajialv' => $show_jiajialv,
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_cat_type_info.html',array(
			'view'=>new AppCatTypeView(new AppCatTypeModel(11))
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
		$result['content'] = $this->fetch('app_cat_type_info.html',array(
			'view'=>new AppCatTypeView(new AppCatTypeModel($id,11))
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
		$this->render('app_cat_type_show.html',array(
			'view'=>new AppCatTypeView(new AppCatTypeModel($id,11))
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$cat_type_name = _Post::get('cat_type_name');
		$cat_type_code = _Post::getString('cat_type_code');
        $parent_id = _Post::getInt('parent_id');
        $display_order = time();
		$note = _Post::get('note');
        
        if($cat_type_name=='')
		{
			$result['error'] ="名称不能为空！";
			Util::jsonExit($result);
		}

		if(!Util::isChinese($cat_type_name))
		{
			$result['error'] ="名称只能是汉字！";
			Util::jsonExit($result);
		}

		if(empty($cat_type_code)){
			$result['error'] ="编码不能为空！";
			Util::jsonExit($result);
		}
		if(preg_match('/[^\w-]/u', $cat_type_code))
		{
			$result['error'] ="编码只能包含字母和横线！";
			Util::jsonExit($result);
		}
		
        $model =  new AppCatTypeModel($parent_id,11);
		$pdo = $model->getDataObject();
		$tree_path = $pdo['tree_path'];
		if(count(explode('-', $tree_path)) > 4){
			$result['error'] ="深度不可以大于5层！";
			Util::jsonExit($result);
		}

		$newmodel =  new AppCatTypeModel(12);
		$has = $newmodel->hasCode($cat_type_code);
        if($has)
		{
			$result['error'] ="操作失败,此编码已存在！";
			Util::jsonExit($result);
		}
        
        if($tree_path==null)
		{
			$tree_path = 0;
		}
		else
		{
			$tree_path .= "-".$parent_id;	
		}

		if($parent_id)
		{
			$pids = $pdo['pids'];
			if($pids)
			{
				$pids.=",".$parent_id;	
			}
			else
			{
				$pids = $parent_id;
			}
		}
		else
		{
			$pids='';
		}

		$olddo = array();
		$newdo=array(
			"cat_type_name"=>$cat_type_name,
			"cat_type_code"=>$cat_type_code,
			"note"=>$note,
			"display_order"=>$display_order,
			"parent_id"=>$parent_id,
			"tree_path"=>$tree_path,
			"childrens"=>0,
			"pids"=>$pids
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
		$id = _Post::getInt('id');
		$cat_type_name = _Post::get('cat_type_name');
		$cat_type_code = _Post::getString('cat_type_code');
        $parent_id = _Post::getInt('parent_id');
		$note = _Post::get('note');
        
        if($cat_type_name=='')
		{
			$result['error'] ="名称不能为空！";
			Util::jsonExit($result);
		}

		if(!Util::isChinese($cat_type_name))
		{
			$result['error'] ="名称只能是汉字！";
			Util::jsonExit($result);
		}

		if(empty($cat_type_code)){
			$result['error'] ="编码不能为空！";
			Util::jsonExit($result);
		}
		if(preg_match('/[^\w-]/u', $cat_type_code))
		{
			$result['error'] ="编码只能包含字母和横线！";
			Util::jsonExit($result);
		}
		
        $newmodel =  new AppCatTypeModel($id,12);
		$has = $newmodel->hasCode($cat_type_code);
        if($has)
		{
			$result['error'] ="操作失败,此编码已存在！";
			Util::jsonExit($result);
		}
        
        
        $olddo = $newmodel->getDataObject();
		$newdo = $olddo;
		$newdo['cat_type_name'] =$cat_type_name; 
		$newdo['cat_type_code'] =$cat_type_code; 
		$newdo['note'] =$note; 
      
        if($parent_id!=$olddo['parent_id'])
		{//分类改变
			$model =  new AppCatTypeModel($parent_id,11);
			$pdo = $model->getDataObject();
			$tree_path = $pdo['tree_path'];
			if(count(explode('-', $tree_path)) > 4){
				$result['error'] ="深度不可以大于5层！";
				Util::jsonExit($result);
			}
			if($tree_path==null)
			{
				$tree_path = 0;
			}
			else
			{
				$tree_path .= "-".$parent_id;	
			}
			$newdo['parent_id'] = $parent_id;
			$newdo['tree_path'] = $tree_path;

			if($parent_id)
			{
				$pids = $pdo['pids'];
				if($pids)
				{
					$pids.=",".$parent_id;	
				}
				else
				{
					$pids = $parent_id;
				}	
			}
			else
			{//变成顶级
				$pids='';
			}
			$newdo['pids'] = $pids;
			$res = $newmodel->saveDatas($newdo,$olddo);
		}
		else
		{//没有改变分类
			if($newdo['pids']==null)
			{
				$newdo['pids']='';	
			}
			$res = $newmodel->saveData($newdo,$olddo);
		}

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
		
		$model = new AppCatTypeModel($id,12);
		$do = $model->getDataObject();
		
		$status = $do['cat_type_status'];
		//var_dump($status);exit;
        if($status == 0){
            $result['error'] = "此条数据已经停用";
            Util::jsonExit($result);
        }
        $ids=$do['parent_id'];
        if($ids==0){
        	$tree = $model->getTreeIdInfo($ids);
        	$a  = array_column($tree,'cat_type_id');
        	$tree = implode(",",$a);
        	//var_dump($tree);exit;
        	$ress = $model->updateStatus($tree);
        }
        $model->setValue('cat_type_status',0);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
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
		$model = new AppCatTypeModel($id,12);
		$do = $model->getDataObject();
		$status = $do['cat_type_status'];
        if($status == 1){
            $result['error'] = "此条数据已经启用";
            Util::jsonExit($result);
        }
       /*  $ids=$do['parent_id'];
        $tree = $model->getTreeIdInfo($ids);
        $a  = array_column($tree,'cat_type_id');
        $tree = implode(",",$a);
        //var_dump($tree);exit;
        $ress = $model->updateStatuson($tree); */
        $ids=$do['parent_id'];
        if($ids==0){
        	$tree = $model->getTreeIdInfo($ids);
        	$a  = array_column($tree,'cat_type_id');
        	$tree = implode(",",$a);
        	//var_dump($tree);exit;
        	$ress = $model->updateStatuson($tree);
        }
        
        $model->setValue('cat_type_status',1);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}
  
	/**
	 *	on_cat，启用
	 */
	public function on_cat ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppCatTypeModel($id,12);
		$do = $model->getDataObject();
		
        $model->setValue('cat_type_status',1);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "更新失败";
		}
		Util::jsonExit($result);
	}        
        
        /**
         * 转换编码
         */
        public function createCode() {
               $value = _Request::getString('value');
               if(empty($value)){
                   exit('');
               }
               $code = Pinyin::getQianpin($value);
               echo $code;
        }
        
}

?>