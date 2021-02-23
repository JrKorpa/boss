<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseMemberInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 18:13:27
 *   @update	:
 *  -------------------------------------------------
 */
class BaseMemberInfoController extends CommonController
{
	protected $smartyDebugEnabled = true;
	protected $allSalesChannelsData = array();
    public function __construct() {
        parent::__construct();
        $departmentModel = new DepartmentModel(1);

        $causeInfo = $departmentModel->getDepartmentInfo("`id`,`name`",array('parent_id'=>1));
        //获取事业部
        $causeData=array();
        foreach ($causeInfo as $val){
            $causeData[$val['id']] = $val['name'];
        }

        $departmentInfo = $departmentModel->getDepartmentInfo("`id`,`name`",array('is_deleted'=>0));
        //获取部门数据 
        $departmentData = array();
        foreach ($departmentInfo as $val){
            $departmentData[$val['id']] = $val['name'];
        }
        $this->assign('causeData', $causeData);
        $this->assign('departmentData', $departmentData);

		//渠道
		$SalesChannelsModel = new SalesChannelsModel(1);
        $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
        //获取所有数据
        foreach ($getSalesChannelsInfo as $val){
            $this->allSalesChannelsData[$val['id']] = $val['channel_name'];
        }
		$this->assign('sales_channels_idData', $this->allSalesChannelsData);
    }
	/**
	 *	index，搜索框 
	 */
	public function index ($params)
	{
        $SalesChannelsModel = new SalesChannelsModel(1);
        $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
        //获取所有数据
        $allSalesChannelsData = array();
        foreach ($getSalesChannelsInfo as $val){
            $allSalesChannelsData[$val['id']] = $val['channel_name'];
        }
		$this->render('base_member_info_search_form.html',array(
			'bar'=>Auth::getBar(),'allSalesChannelsData'=>$allSalesChannelsData
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
            'member_name' => _Request::getString('member_name'),
            'cause_id'=> _Request::getInt('cause_id'),
            'department_id'=> _Request::getInt('department_id'),
            'member_phone'=> _Request::getInt('member_phone'),
            'member_type'=> _Request::getInt('member_type'),
		);

        $where = array(
            'member_name' => _Request::getString('member_name'),
            'department_id'=> _Request::getInt('department_id'),
            'member_phone'=> _Request::getInt('member_phone'),
            'member_type'=> _Request::getInt('member_type'),
        );
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
        //当时事业部被选择，而没有选择部门,择获取此事业部所有的部门
         $departmentModel = new DepartmentModel(1);
        if(!empty($args['cause_id']) && empty($args['department_id'])){
            $info_arr = $departmentModel->getDepartmentInfo("`id`",array('parent_id'=>$args['cause_id']));
            $depart_data = array();
            $depart_ids = "";
            foreach ($info_arr as $val){
                $depart_data[]= $val['id'];
            }
            $depart_ids = implode(',', $depart_data);
            $where['department_id_in'] = $depart_ids;
        } else if ($_SESSION['userType'] > 1) {
            $depts = $departmentModel->db()->getAll("select distinct dept_id from cuteframe.organization where user_id={$_SESSION['userId']}");
            if (!empty($depts)) {
                $where['department_id_in'] = implode(',', array_column($depts, 'dept_id'));
            } else {
                //TODO: 此处必须有代码终止请求
                die('没有找到归属部门, 无法查询.');
            }
        }

		$model = new BaseMemberInfoModel(17);
		$data = $model->pageList($where,$page,10,false);

        $allDepartmentInfo = $departmentModel->getDepartmentInfo("`id`,`name`",array('parent_id_no'=>0));
        //获取所有数据
        $allDeaprtmentData = array();
        foreach ($allDepartmentInfo as $val){
            $allDeaprtmentData[$val['id']] = $val['name'];
        }

		//渠道
		$SalesChannelsModel = new SalesChannelsModel(1);
        $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
        //获取所有数据
        $allSalesChannelsData = array();
        foreach ($getSalesChannelsInfo as $val){
            $allSalesChannelsData[$val['id']] = $val['channel_name'];
        }
        $view = new BaseMemberInfoView(new BaseMemberInfoModel(17));
		$pageData = $data;

		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'base_member_info_search_page';
		$this->render('base_member_info_search_list.html',array(
			'view'=>$view,
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'allDeaprtmentData'=>$allDeaprtmentData,
			'allSalesChannelsData'=>$allSalesChannelsData,
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
       $res= $this->ChannelListO();

        if($res===true){
            $SalesChannelsModel = new SalesChannelsModel(1);
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
        }else{
            $channellist = $this->getchannelinfo($res);

        }

		$sourceModel = new CustomerSourcesModel(1);
		$source = $sourceModel->getSources();

		$this->render('base_member_info_info.html',array(
			'view'=>new BaseMemberInfoView(new BaseMemberInfoModel(17)),
			'parent_id'=>0,'source'=>$source,'edit'=>false,'tab_id'=>_Request::getInt('tab_id'),'channellist'=>$channellist,
            'order_id'=>_Request::getInt('order_id') // 订单更换新用户时使用
		));
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval(_Get::getString('id'));
        $tab_id = _Request::getInt('tab_id');
        //获取此条记录
        $row = new BaseMemberInfoView(new BaseMemberInfoModel($id,17));

        //获取部门，推出所在事业部
        $department_id = $row->get_department_id();
        $departmentModel = new DepartmentModel(1);
        $department_info  = $departmentModel->getDepartmentInfo("`parent_id`",array('id'=>$department_id));
        $parent_id = 0;
        if($department_info){
             $parent_id = $department_info[0]['parent_id'];
        }


		$sourceModel = new CustomerSourcesModel(1);
		$source = $sourceModel->getSources();
        $res= $this->ChannelListO();
        if($res===true){
            $SalesChannelsModel = new SalesChannelsModel(1);
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
        }else{
             $channellist = $this->getchannelinfo($res);
        }

        $this->render('base_member_info_info.html',array(
			'view'=>$row,'source'=>$source,'tab_id'=>$tab_id,
			'parent_id'=>$parent_id,'edit'=>true,'channellist'=>$channellist
		));

	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
        $departmentModel = new DepartmentModel(1);
        $allDepartmentInfo = $departmentModel->getDepartmentInfo("`id`,`name`",array('parent_id_no'=>0));
        $allDeaprtmentData = array_column($allDepartmentInfo, 'name', 'id');

		$id = intval($params["id"]);
		$order_id = intval($params["order_id"]);
		$this->render('base_member_info_show.html',array(
			'view'=>new BaseMemberInfoView(new BaseMemberInfoModel($id,17)),
            'allDepartmentData'=>$allDeaprtmentData,
            'order_id'=>$order_id // 订单更换新用户时使用
		));
	}

	/**
	 *	showUrl，渲染查看页面
	 */
	public function showUrl ($params)
	{
        $departmentModel = new DepartmentModel(1);
        $allDepartmentInfo = $departmentModel->getDepartmentInfo("`id`,`name`",array('parent_id_no'=>0));
        //获取所有数据
        $allDeaprtmentData = array();
        foreach ($allDepartmentInfo as $val){
            $allDeaprtmentData[$val['id']] = $val['name'];
        }
        $id = intval($params["id"]);
        $model= new  BaseMemberInfoModel($id,17);
        $data = $model->getDataObject();


		$id = intval($params["id"]);
		$this->render('base_member_info_showUrl.html',array(
			'view'=>new BaseMemberInfoView(new BaseMemberInfoModel($id,17)),
            'allDepartmentData'=>$this->allSalesChannelsData,
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

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
		if(!is_numeric($member_age) && !empty($member_age)){
			$result['error'] ="会员年龄请填写数字！";
			Util::jsonExit($result);
        }
        $member_type = _Request::getString('member_type');
		if($member_type=='')
		{
			$result['error'] ="会员类型不能为空！";
			Util::jsonExit($result);
		}
        $department_id = _Request::getInt('department_id');
        if($member_type=='')
		{
			$result['error'] ="请选择部门！";
			Util::jsonExit($result);
		}
        $source_id = _Request::getInt('source_id');
        if($source_id=='')
		{
			$result['error'] ="请选择来源！";
			Util::jsonExit($result);
		}
        $confirmPass = _Request::get('confirmPass');
        $member_password = _Request::get('member_password');
        if($confirmPass != $member_password){
            $result['error'] ="确认密码和密码不一致，请重新填写！";
            Util::jsonExit($result);
        }
        $member_email = _Request::get('member_email');
        $member_truename = _Request::get('member_truename');
        $member_tel = _Request::get('member_tel');
        $member_qq = _Request::get('member_qq');
        $member_msn = _Request::get('member_msn');
        $member_sex = _Request::get('member_sex');
        $member_birthday = _Request::get('member_birthday');
        $member_wedding = _Request::get('member_wedding');
        $member_dudget = _Request::get('member_dudget');
        if(!is_numeric($member_dudget) && $member_dudget){
            $result['error'] ="会员结婚预算请填写数字！";
            Util::jsonExit($result);
        }
        $member_password = _Request::get('member_password');
        $member_aliww = _Request::get('member_aliww');
        $member_maristatus=_Request::get('member_maristatus');
        $member_question=_Request::get('member_question');
        $member_answer=_Request::get('member_answer');

		$newmodel =  new BaseMemberInfoModel(18);
		if($newmodel->hasMemberPhone($member_phone))
		{
            $result['error'] ="会员手机号码已存在！";
            Util::jsonExit($result);
		}

        $uploadObj = new Upload();
        $path = KELA_ROOT.'/apps/salepolicy/upload/';
        $uploadObj->base_path=$path;
        if(isset($_FILES['head_img'])){
            $res = $uploadObj->toUP($_FILES['head_img']);
            if(!is_array($res)){
                $result['error'] = '图片上传失败';
                Util::jsonExit($result);
            }else{
                $head_img = $res['url'];
            }
        }else{
            $head_img = '';
        }

		$olddo = array();
		$newdo=array(
			'member_name'=>$member_name,
			'department_id'=>$department_id,
			'member_phone'=>$member_phone,
            'member_age'=>$member_age?$member_age:0,
			'member_type'=>$member_type,
			'customer_source_id'=>$source_id,
            'member_email'=>$member_email,
            'member_tel'=>$member_tel,
            'member_qq'=>$member_qq,
            'member_msn'=>$member_msn,
            'member_sex'=>$member_sex,
            'member_birthday'=>$member_birthday?$member_birthday:0,
            'member_wedding'=>$member_wedding?$member_wedding:0,
            'member_aliww'=>$member_aliww,
            'member_password'=>$member_password,
            'member_dudget'=>$member_dudget?$member_dudget:0.00,
            'member_truename'=>$member_truename,
            'member_maristatus'=>$member_maristatus?:1,
            'member_question'=>$member_question,
            'member_answer'=>$member_answer,
            'head_img'=>$head_img,
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
            $result['x_id'] = $res;
            $result['tab_id'] = _Post::getInt('tab_id');

            $order_id = _Post::getInt('order_id');
            if ($order_id) {
                $user_id = is_numeric($res) ? $res : $res['member_id'];
                $flag = $newmodel->setOrderMember($order_id, array('user_id'=>$user_id, 'consignee'=>$member_name,'mobile'=>$member_phone));
                if (!$flag) {
                    $result = array('success' => 0,'error' =>'新用户已经创建成功，但是回写到订单失败！');
                }
            }
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
		$newmodel =  new BaseMemberInfoModel($id,18);
        $tab_id=_Request::getInt('tab_id');
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
		if($newmodel->hasMemberPhone($member_phone))
		{
            $result['error'] ="会员手机号码已存在！";
            Util::jsonExit($result);
		}

        $member_age = _Request::getString('member_age');
		if(!is_numeric($member_age) && !empty($member_age)){
			$result['error'] ="会员年龄请填写数字！";
			Util::jsonExit($result);
        }
        if($member_age>150){
            $result['error'] ="会员年龄有误！";
			Util::jsonExit($result);
        }
        $member_type = _Request::getString('member_type');
		if($member_type=='')
		{
			$result['error'] ="会员类型不能为空！";
			Util::jsonExit($result);
		}
        $department_id = _Request::getInt('department_id');
        if($member_type=='')
		{
			$result['error'] ="请选择部门！";
			Util::jsonExit($result);
		}
        $source_id = _Request::getInt('source_id');
        if($source_id=='')
		{
			$result['error'] ="请选择来源！";
			Util::jsonExit($result);
		}
        $confirm_password = _Request::get('confirmPass');
        $member_password = _Request::get('member_password');
        if ($confirm_password != $member_password) {
            $result['error'] ="确认密码和密码不一致，请重新填写！";
			Util::jsonExit($result);
        }
        $member_email = _Request::get('member_email');
        $member_truename = _Request::get('member_truename');
        $member_tel = _Request::get('member_tel');
        $member_qq = _Request::get('member_qq');
        $member_msn = _Request::get('member_msn');
        $member_sex = _Request::get('member_sex');
        $member_birthday = _Request::get('member_birthday');
        $member_wedding = _Request::get('member_wedding');
        $member_dudget = _Request::get('member_dudget');
        $member_password = _Request::get('member_password');
        $member_aliww = _Request::get('member_aliww');
        $member_maristatus=_Request::get('member_maristatus');
        $member_question=_Request::get('member_question');
        $member_answer=_Request::get('member_answer');

        $savepath =  KELA_ROOT.'/apps/salepolicy';
        $olddo = $newmodel->getDataObject();

        //这里是会员上传图片的跟路径

        if(isset($_FILES['head_img'])){
            $uploadObj = new Upload();
            $path = KELA_ROOT.'/apps/salepolicy/upload/';
            $uploadObj->base_path=$path;
            $res = $uploadObj->toUP($_FILES['head_img']);
            if(!is_array($res)){
                $result['error'] = '图片上传失败';
                Util::jsonExit($result);
            }else{
                Upload::removeAbsoluteFile($savepath.$olddo['head_img']);
            }

            $head_img = $res['url'];
        }else{
            $head_img =$olddo['head_img'];
        }



		$newdo=array(
            'member_id'=>$id,
            'member_name'=>$member_name,
            'department_id'=>$department_id,
            'member_phone'=>$member_phone,
            'member_age'=>$member_age?$member_age:0,
            'customer_source_id'=>$source_id,
            'member_type'=>$member_type,
             'member_email'=>$member_email,
            'member_tel'=>$member_tel,
            'member_qq'=>$member_qq,
            'member_msn'=>$member_msn,
            'member_sex'=>$member_sex,
            'member_birthday'=>$member_birthday?$member_birthday:0,
            'member_wedding'=>$member_wedding?$member_wedding:0,
            'member_aliww'=>$member_aliww,
            'member_password'=>$member_password,
            'member_dudget'=>$member_dudget?$member_dudget:0,
            'member_truename'=>$member_truename,
            'member_maristatus'=>$member_maristatus?$member_maristatus:0,
            'member_question'=>$member_question,
            'member_answer'=>$member_answer,
            'head_img'=>$head_img,
		);


		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
            $result['x_id'] = $id;
            $result['tab_id'] = $tab_id;
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
		$model = new BaseMemberInfoModel($id,18);
		$do = $model->getDataObject();
		$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

    /*
     *
     */
    public function getDepartmentInfo(){
        $result = array('success' => 0,'error' => '');
       // cause_id:cause_id,parent_id:2
        $cause_id = _Request::getInt('cause_id');
        $departmentModel = new DepartmentModel(1);
        $departmentData = $departmentModel->getDepartmentInfo("`id`,`name`",array('parent_id'=>$cause_id));
        $str = "<option value=''></option>";
        foreach ($departmentData as $val){
            $id = $val['id'];
            $name = $val['name'];
            $str .= "<option value='{$id}'>{$name}</option>";
        }
        $result['content'] = $str;
        Util::jsonExit($result);
    }
}

?>