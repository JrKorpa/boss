<?php
/**
 *  -------------------------------------------------
 *   @file		: AppUserBespokeLogController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 16:15:00
 *   @update	:
 *  -------------------------------------------------
 */
class AppUserBespokeLogController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('AppUserBespokeLog','front',17);	//生成模型后请注释该行
		//Util::V('AppUserBespokeLog',17);	//生成视图后请注释该行
		$this->render('app_user_bespoke_log_search_form.html',array('bar'=>Auth::getBar(),'id'=>_Request::getString("id")));
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
			'mem_id'	=> _Request::getString("id"),

		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where = array('mem_id'=>$args['mem_id']);

		$model = new AppUserBespokeLogModel(17);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_user_bespoke_log_search_page';
		$this->render('app_user_bespoke_log_search_list.html',array(
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
		$result['content'] = $this->fetch('app_user_bespoke_log_info.html',array(
			'view'=>new AppUserBespokeLogView(new AppUserBespokeLogModel(17)),'edit'=>false,'bespoke_id'=>_Request::getString('bespoke_id'),'mem_id'=>_Request::getString('mem_id')
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
		$result['content'] = $this->fetch('app_user_bespoke_log_info.html',array(
			'view'=>new AppUserBespokeLogView(new AppUserBespokeLogModel($id,17)),'edit'=>true
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
        $bespokeStatus=array(
            1=>'保存',
            2=>'已经审核',
            3=>'取消'
        );
		$newmodel =  new AppBespokeActionLogModel(17);
        $actionLog=$newmodel->getActionLogByBespokeID($id);
		$this->render('app_user_bespoke_log_show.html',array(
			'view'=>new AppUserBespokeLogView(new AppUserBespokeLogModel($id,17)),
            'actionLog'=>$actionLog,
            'bespokeStatus'=>$bespokeStatus
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

        $remark = _Request::getString('remark');
		if($remark=='')
		{
			$result['error'] ="备注不能为空！";
			Util::jsonExit($result);
		}
		$olddo = array();
		$newdo=array(
			'remark'=>$remark,
			'bespoke_id'=>_Request::getString('bespoke_id'),
            'mem_id'=>_Request::getString('mem_id'),
			'create_user'=>$_SESSION['userName'],
			'create_time'=>date("Y-m-d H:i:s"),
			'IP'=>Util::getClicentIp(),
		);

		$newmodel =  new AppUserBespokeLogModel(18);
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
		$id = _Request::getInt('bespoke_id');

        $department_id=_Request::getInt('department_id');
        if(empty($department_id)){
            $result['error'] = '预约部门不能为空!';
		    Util::jsonExit($result);
        }

		$newmodel =  new AppUserBespokeLogModel($id,18);

		$olddo = $newmodel->getDataObject();
		$newdo=array();
		$newdo['bespoke_id']=$id;
		$newdo['department_id']=$department_id;
		$newdo['customer']=_Request::getString('customer');
		$newdo['customer_mobile']=_Request::getString('customer_mobile');
		$newdo['customer_email']=_Request::getString('customer_email');
		$newdo['customer_address']=_Request::getString('customer_address');
        $newdo['bespoke_inshop_time']=_Request::getString('bespoke_inshop_time');
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
		$model = new AppUserBespokeLogModel($id,18);
		$do = $model->getDataObject();
		$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

	/**
	 *	bespokeCheck，审核通过
	 */
	public function bespokeCheck ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Request::getInt('id');

		$newmodel =  new AppUserBespokeLogModel($id,18);

		$olddo = $newmodel->getDataObject();
        if($olddo['bespoke_status']!=1){
            $result['error'] = '状态出错';
		    Util::jsonExit($result);
        }
		$newdo=array();
		$newdo['bespoke_id']=$id;
		$newdo['bespoke_status']=1;

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
	 *	BespokeRestatused，到店
	 */
	public function BespokeRestatused ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Request::getInt('id');

		$newmodel =  new AppUserBespokeLogModel($id,18);

		$olddo = $newmodel->getDataObject();
        if($olddo['bespoke_status']!=2 && $olddo['re_status']==1){
            $result['error'] = '状态出错';
		    Util::jsonExit($result);
        }

        $newdo=array();
		$newdo['bespoke_id']=$id;
		$newdo['re_status']=1;
        $newdo['real_inshop_time']=date("Y-m-d H:i:s");
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
	 *	BespokeIsdelete，取消预约
	 */
	public function BespokeIsdelete ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Request::getInt('id');

		$newmodel =  new AppUserBespokeLogModel($id,18);

		$olddo = $newmodel->getDataObject();
        if($olddo['bespoke_status']!=1){
            $result['error'] = '状态出错';
		    Util::jsonExit($result);
        }
		$newdo=array();
		$newdo['bespoke_id']=$id;
		$newdo['is_delete']=1;
		$newdo['bespoke_status']=3;

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
}

?>