<?php
/**
 *  -------------------------------------------------
 *   @file		: AppXilieController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 16:41:05
 *   @update	:
 *  -------------------------------------------------
 */
class AppXilieController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('app_jinsun','front',11);	//生成模型后请注释该行
		//Util::V('app_jinsun',11);	//生成视图后请注释该行
           $model= new AppXilieModel(11);
        $xilie=$model->getAllXilieName();
		$this->render('app_xilie_search_form.html',array('bar'=>Auth::getBar() ,'xilie_arr'=>$xilie));
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
			'xilie_id'	=> _Request::getString("xilie_id"),
			'xilie_status'	=> _Request::getString("xilie_status"),	
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['xilie_id'] = $args['xilie_id'];
		$where['xilie_status'] = $args['xilie_status'];
		$model = new AppXilieModel(11);
		$data = $model->pageList($where,$page,20,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_xilie_search_page';
		$this->render('app_xilie_search_list.html',array(
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
		$result['content'] = $this->fetch('app_xilie_info.html',array(
			'view'=>new AppXilieView(new AppXilieModel(11))
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
		$result['content'] = $this->fetch('app_xilie_info.html',array(
			'view'=>new AppXilieView(new AppXilieModel($id,11))
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
		$this->render('app_xilie_show.html',array(
			'view'=>new AppXilieView(new AppXilieModel($id,1))
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
				'name'	=> _Request::getString("xilie_name"),
				'status'	=> _Request::getInt("status"),	
				
				);
		//var_dump($newdo);exit;

		$newmodel =  new AppXilieModel(12);
		$name = $newmodel->getXilieName($newdo);
        if(!empty($name)){
            $result['error'] = '系列名称已存在！';
		    Util::jsonExit($result);
        }

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '添加失败！';
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

		$newmodel =  new AppXilieModel($id,12);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
				'id'	=> $id,
				'name'	=> _Request::getString("xilie_name"),
				'status'	=> _Request::getInt("status"),	
				
				);
		$name = $newmodel->getXilieName($newdo);
     
        if(!empty($name)){
		  $result['error'] = '系列名称已存在！';
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

/* 	删除
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppJinsunModel($id,12);
		$do = $model->getDataObject();
		$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	} */
	/**
	*停用
	*/
	public function delete($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppXilieModel($id,12);
		$do = $model->getDataObject();
		$status = $do['status'];
        if($status == 0){
            $result['error'] = "此条数据已经停用";
            Util::jsonExit($result);
        }
		$model->setValue('status',0);
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
		$model = new AppXilieModel($id,12);
        $do = $model->getDataObject();
		$status = $do['status'];
        if($status == 1){
            $result['error'] = "此条数据已经启用";
            Util::jsonExit($result);
        }
		$model->setValue('status',1);
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
