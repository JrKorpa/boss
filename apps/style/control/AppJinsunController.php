<?php
/**
 *  -------------------------------------------------
 *   @file		: AppJinsunController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 16:41:05
 *   @update	:
 *  -------------------------------------------------
 */
class AppJinsunController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('app_jinsun','front',11);	//生成模型后请注释该行
		//Util::V('app_jinsun',11);	//生成视图后请注释该行
		$this->render('app_jinsun_search_form.html',array('bar'=>Auth::getBar(),'price_type'=>array('1'=>'男戒','2'=>'女戒','3'=>'情侣男戒','4'=>'情侣女戒')));
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
			'price_type'	=> _Request::getString("price_type"),
			'material_id'	=> _Request::getString("material_id"),
			'jinsun_status'	=> _Request::getString("jinsun_status"),	
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['price_type'] = $args['price_type'];
		$where['material_id'] = $args['material_id'];
		$where['jinsun_status'] = $args['jinsun_status'];
		$model = new AppJinsunModel(11);
		$data = $model->pageList($where,$page,20,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_jinsun_search_page';
		$this->render('app_jinsun_search_list.html',array(
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
		$result['content'] = $this->fetch('app_jinsun_info.html',array(
			'view'=>new AppJinsunView(new AppJinsunModel(11))
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
		$result['content'] = $this->fetch('app_jinsun_info.html',array(
			'view'=>new AppJinsunView(new AppJinsunModel($id,11))
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
		$this->render('app_jinsun_show.html',array(
			'view'=>new AppJinsunView(new AppJinsunModel($id,1))
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
				'price_type'	=> _Request::getInt("price_type"),
				'material_id'	=> _Request::getInt("material_id"),	
				'lv'	=> _Request::getFloat("lv"),	
				);
		//var_dump($newdo);exit;
		if(empty($newdo['lv'])){
			$result['error'] = '金损率不能为空！';	
			Util::jsonExit($result);
		}
		if($newdo['lv']<0 || $newdo['lv']>1){
			$result['error'] = '金损率必须是大于0小于1的小数！';
			Util::jsonExit($result);
		}
		$newmodel =  new AppJinsunModel(12);
		$list = $newmodel->getNumList($newdo);
        if($list){
            $result['error'] = '此材质金损已存在！';
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

		$newmodel =  new AppJinsunModel($id,12);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
				's_id'	=> $id,
				'price_type'	=> _Request::getString("price_type"),
				'material_id'	=> _Request::getString("material_id"),	
				'lv'	=> _Request::getFloat("lv"),	
				);
		if(empty($newdo['lv'])){
			$result['error'] = '金损率不能为空';	
			Util::jsonExit($result);
		}
		if($newdo['lv']<0 || $newdo['lv']>1){
			$result['error'] = '金损率必须是大于0小于1的小数！';
			Util::jsonExit($result);
		}
		$list = $newmodel->getNumList($newdo);
        if($list){
			if($olddo['lv']!=$newdo['lv']){
			
			}else{
				$result['error'] = '属性已存在,无需修改';
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
		$model = new AppJinsunModel($id,12);
		$do = $model->getDataObject();
		$status = $do['jinsun_status'];
        if($status == 0){
            $result['error'] = "此条数据已经停用";
            Util::jsonExit($result);
        }
		$model->setValue('jinsun_status',0);
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
		$model = new AppJinsunModel($id,12);
        $do = $model->getDataObject();
		$status = $do['jinsun_status'];
        if($status == 1){
            $result['error'] = "此条数据已经启用";
            Util::jsonExit($result);
        }
		$model->setValue('jinsun_status',1);
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
