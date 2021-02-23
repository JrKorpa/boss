<?php
/**
 *  -------------------------------------------------
 *   @file		: AppProductTypeController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 12:09:37
 *   @update	:
 *  -------------------------------------------------
 */
class AppProductTypeController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('app_product_type','front',11);	//生成模型后请注释该行
		//Util::V('app_product_type',11);	//生成视图后请注释该行
		$this->render('app_product_type_search_form.html',array('bar'=>Auth::getBar()));
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
//                        'product_type_name'=>  _Request::getString('product_type_name'),
//                        'product_type_status'=>  _Request::getInt('product_type_status'),  
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array(
//                     'product_type_name'=> $args['product_type_name'],
//                     'product_type_status'=> $args['product_type_status']
                );

		$model = new AppProductTypeModel(11);
//		$data = $model->pageList($where,$page,10,false);
        $data = $model->getList();

//		$pageData = $data;
//		$pageData['filter'] = $args;
//		$pageData['jsFuncs'] = 'app_product_type_search_page';
		$this->render('app_product_type_search_list.html',array(
//			'pa'=>Util::page($pageData),
			'data'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_product_type_info.html',array(
			'view'=>new AppProductTypeView(new AppProductTypeModel(11))
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
		$result['content'] = $this->fetch('app_product_type_info.html',array(
			'view'=>new AppProductTypeView(new AppProductTypeModel($id,11))
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
		$this->render('app_product_type_show.html',array(
			'view'=>new AppProductTypeView(new AppProductTypeModel($id,11))
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$product_type_name = _Post::get('product_type_name');
		$product_type_code = _Post::getString('product_type_code');
        $parent_id = _Post::getInt('parent_id');
        $display_order = time();
		$note = _Post::get('note');
        
        if($product_type_name=='')
		{
			$result['error'] ="名称不能为空！";
			Util::jsonExit($result);
		}

		/*if(!Util::isChinese($product_type_name))
		{
			$result['error'] ="名称只能是汉字！";
			Util::jsonExit($result);
		}*/

		if(empty($product_type_code)){
			$result['error'] ="编码不能为空！";
			Util::jsonExit($result);
		}
		if(preg_match('/[^\w-]/u', $product_type_code))
		{
			$result['error'] ="编码只能包含字母和横线！";
			Util::jsonExit($result);
		}
		
        $model =  new AppProductTypeModel($parent_id,11);
		$pdo = $model->getDataObject();
		$tree_path = $pdo['tree_path'];
		if(count(explode('-', $tree_path)) > 6){
			$result['error'] ="深度不可以大于7层！";
			Util::jsonExit($result);
		}

		$newmodel =  new AppProductTypeModel(12);
		$has = $newmodel->hasCode($product_type_code);
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
			"product_type_name"=>$product_type_name,
			"product_type_code"=>$product_type_code,
			"note"=>$note,
			"display_order"=>$display_order,
			"parent_id"=>$parent_id,
			"tree_path"=>$tree_path,
			"childrens"=>0,
			"pids"=>$pids,
            "product_type_status"=>1,
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
		$product_type_name = _Post::get('product_type_name');
		$product_type_code = _Post::getString('product_type_code');
        $parent_id = _Post::getInt('parent_id');
		$note = _Post::get('note');
        
        if($product_type_name=='')
		{
			$result['error'] ="名称不能为空！";
			Util::jsonExit($result);
		}

		/*if(!Util::isChinese($product_type_name))
		{
			$result['error'] ="名称只能是汉字！";
			Util::jsonExit($result);
		}*/

		if(empty($product_type_code)){
			$result['error'] ="编码不能为空！";
			Util::jsonExit($result);
		}
		if(preg_match('/[^\w-]/u', $product_type_code))
		{
			$result['error'] ="编码只能包含字母和横线！";
			Util::jsonExit($result);
		}
		
        $newmodel =  new AppProductTypeModel($id,12);
		$has = $newmodel->hasCode($product_type_code);
        if($has)
		{
			$result['error'] ="操作失败,此编码已存在！";
			Util::jsonExit($result);
		}
        
        
        $olddo = $newmodel->getDataObject();
		$newdo = $olddo;
		$newdo['product_type_name'] =$product_type_name; 
		$newdo['product_type_code'] =$product_type_code; 
		$newdo['note'] =$note; 
      
        if($parent_id!=$olddo['parent_id'])
		{//分类改变
			$model =  new AppProductTypeModel($parent_id,11);
			$pdo = $model->getDataObject();
			$tree_path = $pdo['tree_path'];
			if(count(explode('-', $tree_path)) > 6){
				$result['error'] ="深度不可以大于7层！";
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
	 *	delete，停用
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppProductTypeModel($id,12);
		$do = $model->getDataObject();
		$status = $do['product_type_status'];
        if($status == 0){
            $result['error'] = "此条数据已经停用";
            Util::jsonExit($result);
        }
        if($do['tree_path'] == '0'){
            $tp_str = '';
        	$res = $model->updateProductTypeStatus($tp_str,0);
        }else{
            $tp_str = $do['tree_path'].'-'.$do['product_type_id'];
            $res = $model->updateProductTypeStatus($tp_str,0);
        }
		$model->setValue('product_type_status',0);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}
    
	/**
	 *	enable，启用
	 */
	public function enable ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppProductTypeModel($id,12);
		$do = $model->getDataObject();
		$status = $do['product_type_status'];
        if($status == 1){
            $result['error'] = "此条数据已经启用！";
            Util::jsonExit($result);
        }
        $rel = $model->getProductStatus($do['parent_id']);
        if(!empty($rel)){
            if($rel['product_type_status'] == 0){
                $result['error'] = "请将上级先启用！";
                Util::jsonExit($result);
            }    
        }
        if($do['tree_path'] == '0'){
            $res = $model->updateProductTypeStatus(0,1);
        }else{
            $tp_str = $do['tree_path'].'-'.$do['product_type_id'];
            $res = $model->updateProductTypeStatus($tp_str,1);
        }
		$model->setValue('product_type_status',1);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "操作失败";
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