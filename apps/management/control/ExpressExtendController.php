<?php
/**
 *  -------------------------------------------------
 *   @file		: ExpressExtendController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2016-01-27 13:49:35
 *   @update	:
 *  -------------------------------------------------
 */
class ExpressExtendController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $expressModel = new ExpressModel(1);
        $expressInfo = $expressModel->select2("`id`,`exp_name`", "`is_deleted` = 0");

		$this->render('express_extend_search_form.html',array('bar'=>Auth::getBar(), 'expressInfo'=>$expressInfo));
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
			'express_id' => _Request::getString("express_id")

		);
		$page = _Request::getInt("page",1);
		$where = array(
            'express_id'=>$args['express_id']
            );

		$model = new ExpressExtendModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'express_extend_search_page';
		$this->render('express_extend_search_list.html',array(
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

        $expressModel = new ExpressModel(1);
        $expressInfo = $expressModel->select2("`id`,`exp_name`", "`is_deleted` = 0");

        $model = new ExpressModel(1);
        $dataReg = $model->getRegionAreas();

		$result['content'] = $this->fetch('express_extend_info.html',array(
			'view'=>new ExpressExtendView(new ExpressExtendModel(1)),
            'expressInfo'=>$expressInfo,
            'dataReg'=>$dataReg
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
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');

        $expressModel = new ExpressModel(1);
        $expressInfo = $expressModel->select2("`id`,`exp_name`", "`is_deleted` = 0");

        $model = new ExpressExtendModel($id,1);
        $do = $model->getDataObject();
        $exp_areas_id = $do['exp_areas_id'];

        $regionAll = array();
        if($exp_areas_id){
            $regionAll = explode(",", $exp_areas_id);
        }

        $dataReg = $expressModel->getRegionAreas();

		$result['content'] = $this->fetch('express_extend_info.html',array(
			'view'=>new ExpressExtendView(new ExpressExtendModel($id,1)),
			'tab_id'=>$tab_id,
            'expressInfo'=>$expressInfo,
            'dataReg'=>$dataReg,
            'regionAll'=>$regionAll
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$this->render('express_extend_show.html',array(
			'view'=>new ExpressExtendView(new ExpressExtendModel($id,1)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

        $express_id = _Post::getInt('express_id');
        $send_time_end = _Post::getString('send_time_end');
        $send_time_start = _Post::getString('send_time_start');
        $exp_areas_id = _Request::getList("exp_areas_id");

        if(empty($express_id)){

            $result['error'] = '提示：请选择快递公司！';
            Util::jsonExit($result);
        }

        if(empty($send_time_end) || empty($send_time_start)){

            $result['error'] = '提示：请选择暂停配送区域！';
            Util::jsonExit($result);
        }

        if(empty($exp_areas_id)){

            $result['error'] = '提示：请选择停止收发区域！';
            Util::jsonExit($result);
        }

        $model = new ExpressModel($express_id,1);
        $do = $model->getDataObject();
        $pause_exp_areas_list = implode(",",$exp_areas_id);
        $regionname = implode(",", array_column($model->getRegionAreasNamebyid($pause_exp_areas_list), 'region_name'));

		$olddo = array();
		$newdo=array(
            'express_id'=>$express_id,
            'express_name'=>$do['exp_name'],
            'send_time_end'=>$send_time_end." 00:00:00",
            'send_time_start'=>$send_time_start." 23:59:59",
            'exp_areas_id'=>$pause_exp_areas_list,
            'exp_areas_name'=>$regionname,
            'add_user'=>$_SESSION['userName'],
            'add_time'=>date('Y-m-d H:i:s')
            );

		$newmodel =  new ExpressExtendModel(2);
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
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');
		$id = _Post::getInt('id');
        $express_id = _Post::getInt('express_id');
        $send_time_end = _Post::getString('send_time_end');
        $send_time_start = _Post::getString('send_time_start');
        $exp_areas_id = _Request::getList("exp_areas_id");

        if(empty($express_id)){

            $result['error'] = '提示：请选择快递公司！';
            Util::jsonExit($result);
        }

        if(empty($send_time_end) || empty($send_time_start)){

            $result['error'] = '提示：请选择暂停配送区域！';
            Util::jsonExit($result);
        }

        if(empty($exp_areas_id)){

            $result['error'] = '提示：请选择停止收发区域！';
            Util::jsonExit($result);
        }

        $model = new ExpressModel($express_id,1);
        $do = $model->getDataObject();
        $pause_exp_areas_list = implode(",",$exp_areas_id);
        $regionname = implode(",", array_column($model->getRegionAreasNamebyid($pause_exp_areas_list), 'region_name'));

		$newmodel =  new ExpressExtendModel($id,2);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
            'id'=>$id,
            'express_id'=>$express_id,
            'express_name'=>$do['exp_name'],
            'send_time_end'=>$send_time_end." 00:00:00",
            'send_time_start'=>$send_time_start." 23:59:59",
            'exp_areas_id'=>$pause_exp_areas_list,
            'exp_areas_name'=>$regionname,
            'add_user'=>$_SESSION['userName'],
            'add_time'=>date('Y-m-d H:i:s')
            );

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;	
			$result['title'] = '修改此处为想显示在页签上的字段';
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
		$model = new ExpressExtendModel($id,2);
		//$do = $model->getDataObject();
		//$valid = $do['is_system'];
		//if($valid)
		//{
			//$result['error'] = "当前记录为系统内置，禁止删除";
			//Util::jsonExit($result);
		//}
		//$model->setValue('is_deleted',1);
		//$res = $model->save(true);
		//联合删除？
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