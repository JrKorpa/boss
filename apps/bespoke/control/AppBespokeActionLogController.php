<?php
/**
 *  -------------------------------------------------
 *   @file		: AppBespokeActionLogController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 18:13:27
 *   @update	:
 *  -------------------------------------------------
 */
class AppBespokeActionLogController extends Controller
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('app_bespoke_action_log','front',17);	//生成模型后请注释该行
		//Util::V('app_bespoke_action_log',17);	//生成视图后请注释该行
		$this->render('app_bespoke_action_log_search_form.html',array('bar'=>Auth::getBar()));
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
            'member_name' => _Request::getString('member_name')
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['member_name'] = $args['member_name'];

		$model = new AppBespokeActionLogModel(17);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_bespoke_action_log_search_page';
		$this->render('app_bespoke_action_log_search_list.html',array(
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
		$result['content'] = $this->fetch('app_bespoke_action_log_info.html',array(
			'view'=>new AppBespokeActionLogView(new AppBespokeActionLogModel(17)),
			'bespoke_id'=>_Request::getInt('bespoke_id'),
			'bespoke_status'=>_Request::getInt('bespoke_status'),
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
		$result['content'] = $this->fetch('app_bespoke_action_log_info.html',array(
			'view'=>new AppBespokeActionLogView(new AppBespokeActionLogModel($id,17))
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
		$this->render('app_bespoke_action_log_show.html',array(
			'view'=>new AppBespokeActionLogView(new AppBespokeActionLogModel($id,17))
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

        $bespoke_id = _Request::getInt('bespoke_id');
        $bespoke_status = _Request::getInt('bespoke_status');
        $remark = _Request::getString('remark');
		if($remark=='')
		{
			$result['error'] ="备注不能为空！";
			Util::jsonExit($result);
		}
		$olddo = array();
		$newdo=array(
			'bespoke_id'=>$bespoke_id,
			'create_user'=>$_SESSION['userName'],
            'create_time'=>date("Y-m-d H:i:s"),
			'IP'=>Util::getClicentIp(),
			'bespoke_status'=>$bespoke_status,
			'remark'=>$remark
		);

		$newmodel =  new AppBespokeActionLogModel(18);
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
		$id = _Post::getInt('member_id');
		$newmodel =  new AppBespokeActionLogModel($id,18);

        $member_name = _Request::getString('member_name');
		if($member_name=='')
		{
			$result['error'] ="会员名不能为空！";
			Util::jsonExit($result);
		}
        $member_phone = _Request::getString('member_phone');
		if($member_phone=='')
		{
			$result['error'] ="会员电话不能为空！";
			Util::jsonExit($result);
		}
        $member_age = _Request::getString('member_age');
		if($member_age=='')
		{
			$result['error'] ="会员年龄不能为空！";
			Util::jsonExit($result);
		}
        $member_type = _Request::getString('member_type');
		if($member_type=='')
		{
			$result['error'] ="会员类型不能为空！";
			Util::jsonExit($result);
		}
		$olddo = $newmodel->getDataObject();
		$newdo=array(
            'member_id'=>$id,
            'member_name'=>$member_name,
            'member_phone'=>$member_phone,
            'member_age'=>$member_age,
            'member_type'=>$member_type
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
		$model = new AppBespokeActionLogModel($id,18);
		$do = $model->getDataObject();
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