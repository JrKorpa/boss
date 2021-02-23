<?php
/**
 *  -------------------------------------------------
 *   @file		: ExpressController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 17:14:56
 *   @update	:
 *  -------------------------------------------------
 */
class ExpressController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('express_search_form.html',['bar'=>Auth::getBar()]);
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
			'exp_name'=>_Request::get("exp_name"),
			'exp_code'=>_Request::get("exp_code"),
			'exp_tel'=>_Request::get("exp_tel"),

		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['exp_name'] = $args['exp_name'];
		$where['exp_code'] = $args['exp_code'];
		$where['exp_tel'] = $args['exp_tel'];

		$model = new ExpressModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'express_search_page';
		$this->render('express_search_list.html',array(
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

        $model = new ExpressModel(1);
        $dataReg = $model->getRegionAreas();

		$result['content'] = $this->fetch('express_info.html',array(
			'view'=>new ExpressView($model),
            'dataReg'=>$dataReg
		));
		$result['title'] = '添加-快递公司';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
        $result = array('success' => 0,'error' => '');
		$id = intval($params["id"]);
        $model = new ExpressModel($id,1);
        $do = $model->getDataObject();
        $pause_exp_areas = $do['pause_exp_areas'];

        $regionAll = array();
        if($pause_exp_areas){
            $regionAll = explode(",", $pause_exp_areas);
        }

        $dataReg = $model->getRegionAreas();
		
		$result['content'] = $this->fetch('express_info.html',array(
			'view'=>new ExpressView($model),
            'regionAll'=>$regionAll,
            'dataReg'=>$dataReg
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

		//rules验证
		$vd = new Validator();
		$vd->set_rules('exp_name', '快递公司',  'require');
		$vd->set_rules('exp_code', '公司编码',  'require');
		$vd->set_rules('exp_tel', '快递电话',  'require|isPhone');
		$vd->set_rules('exp_areas', '配送区域',  'require');

		if (!$vd->is_valid($_POST))
		{
			$result['error'] = $vd->get_errors();
			Util::jsonExit($result);
		}

		//接收数据
        foreach ($_POST as $k => $v) {
            if($k != 'pause_exp_areas'){
                
                $$k = _Post::get($k);
            }
        }

        $model =  new ExpressModel(1);
        $pause_exp_areas_list = '';
        $pause_exp_areas = _Request::getList("pause_exp_areas");

        $regionname = '';
        if(!empty($pause_exp_areas)){

            $pause_exp_areas_list = implode(",",$pause_exp_areas);
            $regionname = implode(",", array_column($model->getRegionAreasNamebyid($pause_exp_areas_list), 'region_name'));

        }

        if($pause_send_time){

            $pause_send_time = $pause_send_time." 00:00:00";
        }else{
            $pause_send_time = "0000-00-00 00:00:00";
        }

        if($recovery_send_time){

            $recovery_send_time = $recovery_send_time." 23:59:59";
        }else{
            $recovery_send_time = "0000-00-00 00:00:00";
        }

		$olddo = array();
		$newdo=array(
			'exp_name'=>$exp_name,
			'exp_code'=>$exp_code,
			'exp_tel'=>$exp_tel,
			'exp_areas'=>$exp_areas,
			'exp_note'=>$exp_note,
            'pause_send_time'=>$pause_send_time,
            'recovery_send_time'=>$recovery_send_time,
            'pause_exp_areas'=>$pause_exp_areas_list,
            'pause_exp_areas_name'=>$regionname
		);

		$newmodel =  new ExpressModel(2);
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

		//rules验证
		$vd = new Validator();
		$vd->set_rules('exp_name', '快递公司',  'require');
		$vd->set_rules('exp_code', '公司编码',  'require');
		$vd->set_rules('exp_tel', '快递电话',  'require|isNumber');
		$vd->set_rules('exp_areas', '配送区域',  'require');

		if (!$vd->is_valid($_POST))
		{
			$result['error'] = $vd->get_errors();
			Util::jsonExit($result);
		}

		//接收数据
        foreach ($_POST as $k => $v) {
            if($k != 'pause_exp_areas'){
                
                $$k = _Post::get($k);
            }
        }

        $pause_exp_areas_list = '';
        $pause_exp_areas = _Request::getList("pause_exp_areas");

        $model =  new ExpressModel(1);
        $regionname = '';
        if(!empty($pause_exp_areas)){

            $pause_exp_areas_list = implode(",",$pause_exp_areas);
            $regionname = implode(",", array_column($model->getRegionAreasNamebyid($pause_exp_areas_list), 'region_name'));
        }

        if($pause_send_time){

            $pause_send_time = $pause_send_time." 00:00:00";
        }else{
            $pause_send_time = "0000-00-00 00:00:00";
        }

        if($recovery_send_time){

            $recovery_send_time = $recovery_send_time." 23:59:59";
        }else{
            $recovery_send_time = "0000-00-00 00:00:00";
        }

        $newmodel =  new ExpressModel($id,2);

        $olddo = $newmodel->getDataObject();
        $newdo=array(
            'id'=>$id,
            'exp_name'=>$exp_name,
            'exp_code'=>$exp_code,
            'exp_tel'=>$exp_tel,
            'exp_areas'=>$exp_areas,
            'exp_note'=>$exp_note,
            'pause_send_time'=>$pause_send_time,
            'recovery_send_time'=>$recovery_send_time,
            'pause_exp_areas'=>$pause_exp_areas_list,
            'pause_exp_areas_name'=>$regionname
        );

		$res = $newmodel->saveData($newdo,$olddo);
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
		$model = new ExpressModel($id,2);
//		$do = $model->getDataObject();
//		$valid = $do['is_system'];
//		if($valid)
//		{
//			$result['error'] = "当前记录为系统内置，禁止删除";
//			Util::jsonExit($result);
//		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
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