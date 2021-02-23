<?php
/**
 *  -------------------------------------------------
 *   @file		: LargeAreaController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-29 11:43:53
 *   @update	:
 *  -------------------------------------------------
 */
class LargeAreaController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('large_area_search_form.html',array('bar'=>Auth::getBar()));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$model = new LargeAreaModel(1);
		$data = $model->getList();
/*		print_r($data);
		exit;*/
		$this->render('large_area_search_list.html',array(
			'data'=>$data
		));

	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('large_area_info.html',array(
			'view'=>new LargeAreaView(new LargeAreaModel(1))
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
		$result['content'] = $this->fetch('large_area_info.html',array(
			'view'=>new LargeAreaView(new LargeAreaModel($id,1))
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
		$name = _Post::getString('name');

		$parent_id = _Post::getInt('parent_id');

		$create_time = time();
		$user_id = $_SESSION['userId'];

		if(!Util::isChinese($name)){
			$result['error'] ="大区名字必需是汉字！";
			Util::jsonExit($result);
		}

		if($name==''){
			$result['error'] ="大区姓名不能为空！";
			Util::jsonExit($result);
		}


		$model = new LargeAreaModel($parent_id,1);
		$dataobj = $model->getDataObject();
		$tree_path = $dataobj['tree_path'];
		if(count(explode('-', $tree_path)) > 4){
			$result['error'] ="深度不可以大于5层！";
			Util::jsonExit($result);
		}


		if($tree_path==null){
			$tree_path =0;
		}else{
			$tree_path = $tree_path.'-'.$parent_id;
		}



		if($parent_id){
			$pids = $dataobj['pids'];
			if($pids){
				$pids.=','.$parent_id;
			}else{
				$pids = $parent_id;
			}
		}else{
			$pids = 0;
		}

		$olddo = array();
		$newdo=array(
			'name'=>$name,
			'parent_id'=>$parent_id,
			'tree_path'=>$tree_path,
			'pids'=>$pids,
			'create_time'=>$create_time,
			'create_user'=>$user_id,
		);


		$newmodel =  new LargeAreaModel(2);

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
		$id = _Post::getInt('id');
		$parent_id=_Post::getInt('parent_id');
		$name = _Post::getString('name');


		if(!Util::isChinese($name)){
			$result['error'] ="大区名字必需是汉字！";
			Util::jsonExit($result);
		}

		if($name==''){
			$result['error'] ="大区姓名不能为空！";
			Util::jsonExit($result);
		}

		$newmodel =  new LargeAreaModel($id,2);

		$olddo = $newmodel->getDataObject();
		$newdo = $olddo;

/*		$newdo=array(
			'id'=>$id,
			'parent_id'=>$parent_id,
			'name'=>$name,
		);*/
		$newdo['id']=$id;
		$newdo['parent_id']=$parent_id;
		$newdo['name']=$name;


		if($parent_id!=$olddo['parent_id']){
			//这里是改变上级分类的情况判断
			$model = new LargeAreaModel($parent_id,1);
			$pdo = $model->getDataObject();
			$tree_path = $pdo['tree_path'];

			if(count(explode('-',$tree_path))>4){
				$result['error'] = '大区的层级不能超过5层';
				Util::jsonExit($result);
			}
			if($tree_path==null){
				$tree_path = 0;
			}else{
				$tree_path = $tree_path.'-'.$parent_id;
			}
			$newdo['parent_id'] = $parent_id;
			$newdo['tree_path'] = $tree_path;

			if($parent_id){
				$pids = $pdo['pids'];
				if($pids)
				{
					$pids.=",".$parent_id;
				}
				else
				{
					$pids = $parent_id;
				}
			}else
			{//变成顶级
				$pids='';
			}
			$newdo['pids'] = strval($pids);


			$res = $newmodel->saveDatas($newdo,$olddo);

		}else{
			//这里是不改变上级分类的情况平判断
			if($newdo['pids']==null){
					$newdo['pids'] = '';
			}
			$res = $newmodel->saveData($newdo,$olddo);

		}

		if($res !== false)
		{
			$result['success'] = 1;
			
			//修改日志记录
			$dataLog['pkdata'] = array('id'=>$id);
			$dataLog['newdata'] = $newdo;
			$dataLog['olddata'] = $olddo;
			$dataLog['fields']  = $newmodel->getFieldsDefine();
			$this->operationLog("update",$dataLog);
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
		$model = new LargeAreaModel($id,2);
		$do = $model->getDataObject();

		if($do['childrens']){
			$result['error'] = "有下级地区不能删除";
			Util::jsonExit($result);
		}

		$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
			
			//日志记录
			$dataLog['pkdata'] = array('id'=>$id);
			$this->operationLog("delete",$dataLog);
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
}

?>