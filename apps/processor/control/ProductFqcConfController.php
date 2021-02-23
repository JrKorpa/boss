<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductFqcConfController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-03 17:24:09
 *   @update	:
 *  -------------------------------------------------
 */
class ProductFqcConfController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('product_fqc_conf_search_form.html',array(
			'bar'=>Auth::getBar(),'view'=>new ProductFqcConfView(new ProductFqcConfModel(13))
		));
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


		);
		$page = _Request::getInt("page",1);
		$where = array();

		$model = new ProductFqcConfModel(13);
		//$data = $model->pageList($where,$page,10,false);
                $data = $model->get_list();
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'product_fqc_conf_search_page';
		$this->render('product_fqc_conf_search_list.html',array(
			'view'=>new ProductFqcConfView(new ProductFqcConfModel(13)),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
        $model = new ProductFqcConfModel(13);
        $data =  $model->get_list();
        $view = new ProductFqcConfView($model);
        $data = $view->getOrderTree($data);
		//var_dump($data);exit;
		$result['content'] = $this->fetch('product_fqc_conf_info.html',array(
			'view'=>$view,
            'data'=>$data,
		));
		$result['title'] = '添加分类';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
                $parend_id = _Request::getInt("parent_id");
		$tab_id = _Request::getInt("tab_id");
                $model = new ProductFqcConfModel($id,13);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('product_fqc_conf_info.html',array(
			'view'=>new ProductFqcConfView($model),
			'tab_id'=>$tab_id,
		));
		$result['title'] = '编辑分类';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$this->render('product_fqc_conf_show.html',array(
			'view'=>new ProductFqcConfView(new ProductFqcConfModel($id,13)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
                $cat_name = _Post::get('cat_name');
                $parent_id = _Post::getInt('parent_id');
                $id = _Post::getInt("id");
                $display_order = time();
		$newmodel =  new ProductFqcConfModel($parent_id,13);
                $pdo = $newmodel->getDataObject();
                $tree_path = $pdo['tree_path'];
                if(count(explode('-', $tree_path)) > 4){
			$result['error'] ="深度不可以大于5层！";
			Util::jsonExit($result);
		}
                /*在此添加判断是否已经添加重复分类*/
                $newmodel =  new ProductFqcConfModel(13);
		$has = $newmodel->if_has_catname($parent_id,$cat_name,$id);
                 if($has)
		{
			$result['error'] ="此分类名称已存在";
			Util::jsonExit($result);
		}
                //组合路径
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
                
                if(empty($parent_id)) {
                    $parent_id = 0;
                }
                $olddo = array();
		$newdo=array(
			"cat_name"=>$cat_name,
			"display_order"=>$display_order,
			"parent_id"=>$parent_id,
			"tree_path"=>$tree_path,
			"childrens"=>0,
			"pids"=>$pids
		);
                
                $newmodel =  new ProductFqcConfModel($parent_id,13);
		$res = $newmodel->saveDatas($newdo,$olddo);
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
		$cat_name = _Post::get('cat_name');
		$parent_id = _Post::getInt('parent_id');
		$newmodel =  new ProductFqcConfModel($id,13);
		$has = $newmodel->if_has_catname($parent_id,$cat_name,$id);
                if ($has) {
                    $result['error'] ="此分类名称已存在";
		    Util::jsonExit($result);
                }

		$newmodel =  new ProductFqcConfModel($id,13);
                $olddo = $newmodel->getDataObject();
		$newdo = $olddo;
		$newdo['cat_name'] =$cat_name;
		$newdo['id'] =$id;
		$newdo['parent_id'] =$parent_id;
                
                if($parent_id!=$olddo['parent_id'])
		{
			$model =  new ProductFqcConfModel($parent_id,13);
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
			{
				$pids=0;
			}
			$newdo['pids'] = $pids;
			$res = $newmodel->saveDatas($newdo,$olddo);
		}
		else
		{
                               //         var_dump($newdo);exit;
			if($newdo['pids']==null)
			{
				$newdo['pids']=0;
			}
			$res = $newmodel->saveDatas($newdo,$olddo);
		}

		$res = $newmodel->saveDatas($newdo,$olddo);
                
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;
			$result['title'] = $cat_name;
		}
		else
		{
                    
                    $n_arr = array_diff($newdo, $olddo);
                    if (empty($n_arr)){
                        $result['error'] = '没有做任何修改';
                    }else{
			$result['error'] = '修改失败';
                    }
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
                $cat_name = _Post::get('cat_name');
		$parent_id = _Post::getInt('parent_id');

		$model = new ProductFqcConfModel($id,14);
		$do = $model->getDataObject();
                $pids     = $do['pids'];

                $chidrens = $model->if_has_chidrens($id,$pids);
                if(!empty($chidrens))
                {
                        $result['error'] = "有子分类，禁止删除";
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