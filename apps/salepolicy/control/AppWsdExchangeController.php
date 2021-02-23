<?php
/**
 *  -------------------------------------------------
 *   @file		: AppWsdExchangeController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-12-25 17:49:45
 *   @update	:
 *  -------------------------------------------------
 */
class AppWsdExchangeController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        //渠道
        $model = new UserChannelModel(1);
        $data = $model->getChannels($_SESSION['userId'],0);
        if(empty($data)){

            die('请先联系管理员授权渠道!');
        }
        $this->assign('sales_channels', $data);
		$this->render('app_wsd_exchange_search_form.html',array('bar'=>Auth::getBar()));
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
			'wsd_code' => _Request::get("wsd_code"),
            'wsd_name' => _Request::get("wsd_name"),
            'wsd_user' => _Request::get("wsd_user"),
            'wsd_is_bespoke' => _Request::get("wsd_is_bespoke"),
            'wsd_mobile' => _Request::get("wsd_mobile"),
            'wsd_department' => _Request::get("wsd_department")

		);

        $model = new AppWsdExchangeModel(17);
        if(!$args['wsd_code'] && !$args['wsd_name']  && !$args['wsd_mobile'] ){

            //渠道
            if(!$args['wsd_department']){

                $modelChannel = new UserChannelModel(1);
                $qudao = $modelChannel->getChannels($_SESSION['userId'],0);
                if(empty($qudao)){

                    die('请先联系管理员授权渠道!');
                }

                $dep_id = '';//只能查看自己所属渠道的记录
                foreach ($qudao as $v) {
                    # code...
                    $dep_id .= $v['id'].",";
                }

                $args['wsd_department'] = trim($dep_id,",");
            }else{

                $department = explode("|", $args['wsd_department']);
                $args['wsd_department'] = $department[0];//部门ID
            }

            if($_SESSION['userType'] != 1){

                $channelsInfo = $modelChannel->get_all_channels_by_channel_id($_SESSION['qudao']);

                foreach ($channelsInfo as $key => $value) {
                    # code...
                    $arr = array_filter(explode(",", $value['dp_leader_name']));
                    if(!in_array($_SESSION['userName'],$arr)){

                        $args['wsd_user'] = $_SESSION['userName'];
                    }
                }
            }
        }else{

            $code_string = '';
            if($args['wsd_code']){
                $findsIn = array();
                $findsIn['finds'] = 'wsd_code';
                $findsIn['values'] = $args['wsd_code'];
                $checkin = $model->checkSearchInfo($findsIn);
                if($checkin){

                    $code_string.="'".$checkin."',";
                    $args['wsd_code'] = '';
                }
            }

            if($args['wsd_name']){
                $findsIn = array();
                $findsIn['finds'] = 'wsd_name';
                $findsIn['values'] = $args['wsd_name'];
                $checkin = $model->checkSearchInfo($findsIn);
                if($checkin){
                    
                    $code_string.="'".$checkin."',";
                    $args['wsd_name'] = '';
                }
            }

            if($args['wsd_mobile']){
                $findsIn = array();
                $findsIn['finds'] = 'wsd_mobile';
                $findsIn['values'] = $args['wsd_mobile'];
                $checkin = $model->checkSearchInfo($findsIn);
                if($checkin){
                    
                    $code_string.="'".$checkin."',";
                    $args['wsd_mobile'] = '';
                }
            }
        }

        //根据卡号，顾客姓名，手机号条件搜索可以查询兑换记录（不受渠道限制）。
        //三个条件只要一个成立即可查出
        if($code_string){

            $args['wsd_code'] = trim($code_string, ",");
        }

		$page = _Request::getInt("page",1);
		$where = array(

            'wsd_code'=>$args['wsd_code'],
            'wsd_name'=>$args['wsd_name'],
            'wsd_user'=>$args['wsd_user'],
            'wsd_is_bespoke'=>$args['wsd_is_bespoke'],
            'wsd_mobile'=>$args['wsd_mobile'],
            'wsd_department'=>$args['wsd_department']
            );
        
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_wsd_exchange_search_page';
		$this->render('app_wsd_exchange_search_list.html',array(
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
        //渠道
        $model = new UserChannelModel(1);
        $data = $model->getChannels($_SESSION['userId'],0);
        if(empty($data)){
            
            die('请先联系管理员授权渠道!');
        }
        $this->assign('sales_channels', $data);
		$result['content'] = $this->fetch('app_wsd_exchange_info.html',array(
			'view'=>new AppWsdExchangeView(new AppWsdExchangeModel(17))
		));
		$result['title'] = '万事达礼品兑换';
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
		$result['content'] = $this->fetch('app_wsd_exchange_info.html',array(
			'view'=>new AppWsdExchangeView(new AppWsdExchangeModel($id,17)),
			'tab_id'=>$tab_id
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
		$this->render('app_wsd_exchange_show.html',array(
			'view'=>new AppWsdExchangeView(new AppWsdExchangeModel($id,17)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

        if(!_Post::getString('wsd_code'))
        {
            $result['error'] = "请填写万事达卡号！";
            Util::jsonExit($result);
        }
        if(!_Post::getString('wsd_name'))
        {
            $result['error'] = "请填写顾客姓名！";
            Util::jsonExit($result);
        }
        if(!_Post::getString('wsd_mobile'))
        {
            $result['error'] = "请填写顾客电话！";
            Util::jsonExit($result);
        }
        if(!preg_match("/^1\d{10}$/",_Post::getString('wsd_mobile'))){
            $result['error'] = '顾客电话格式不对！';
            Util::jsonExit($result);        
        }
        if(!_Post::getString('wsd_department'))
        {
            $result['error'] = "请选择渠道部门！";
            Util::jsonExit($result);
        }
        $department = explode("|", _Post::getString('wsd_department'));
        $wsd_department = $department[0];//部门ID
        $wsd_department_name = $department[1];//部门名称
        $model =  new AppWsdExchangeModel(17);
        $check_info = $model->checkWsdCode(_Post::getString('wsd_code'));
        if(!empty($check_info)){

            $result['error'] = "此卡已在".$check_info['wsd_department_name']."兑换，不能再次兑换。兑换人：".$check_info['wsd_name']."，兑换时间：".date('Y-m-d',strtotime($check_info['wsd_time']));
            Util::jsonExit($result);
        }
		$olddo = array();
		$newdo = array(

            'wsd_code'=>_Post::getString('wsd_code'),
            'wsd_name'=>_Post::getString('wsd_name'),
            'wsd_mobile'=>_Post::getString('wsd_mobile'),
            'wsd_department'=>$wsd_department,
            'wsd_department_name'=>$wsd_department_name,
            'wsd_user'=>$_SESSION['userName'],
            'wsd_time'=>date("Y-m-d H:i:s")
            );

		$newmodel =  new AppWsdExchangeModel(18);
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
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;

		$newmodel =  new AppWsdExchangeModel($id,18);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
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
		$model = new AppWsdExchangeModel($id,18);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		//联合删除？
		//$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

    /**
     *  wsd_bespoke，预约
     */
    public function wsd_bespoke ($params)
    {
        $result = array('success' => 0,'error' => '');
        $id = intval($params['id']);
        $model = new AppWsdExchangeModel($id,18);
        $do = $model->getDataObject();
        $wsd_is_bespoke = $do['wsd_is_bespoke'];
        if($wsd_is_bespoke != 1)
        {
            $result['error'] = "此记录已预约，无需重复预约！";
            Util::jsonExit($result);
        }
        if($do['wsd_user'] != $_SESSION['userName']){
            $result['error'] = "不是自己制的单不能点击预约！";
            Util::jsonExit($result);
        }

        //推送至会员表
        $MemberInfo = array();
        $MemberInfo['customer_source_id'] = 2896;//客户来源2896
        $MemberInfo['member_name'] = $do['wsd_name'];//顾客姓名
        $MemberInfo['department_id'] = $do['wsd_department'];//渠道
        $MemberInfo['member_phone'] = $do['customer_mobile'];//顾客手机号
        $MemberInfo['member_tel'] = $do['customer_mobile'];//会员电话

        $mem_id = $model->saveMemberInfo($MemberInfo);//会员ID

        //唯一预约号
        do{
            $besp_sn = $model->create_besp_sn();
            //error
            if(!$model->get_bespoke_by_besp_sn($besp_sn))
            {
                break;
            }
        }while(true);

        //整理预约数据
        $data = array();
        $data['bespoke_sn'] = $besp_sn;//预约号
        $data['mem_id'] = $mem_id;//会员ID
        $data['customer'] = $do['wsd_name'];//顾客姓名
        $data['department_id'] = $do['wsd_department'];//渠道
        $data['customer_mobile'] = $do['wsd_mobile'];//顾客手机号
        $data['customer_source_id'] = 2896;//客户来源2896
        $data['bespoke_inshop_time'] = date("Y-m-d H:i:s");//预约到店时间
        $data['make_order'] = $do['wsd_user'];//制单人
        $data['accecipt_man'] = $do['wsd_user'];//接待人
        $data['sales_channels_id'] = $do['wsd_department'];//渠道
        $data['bespoke_status'] = 2;//预约单状态
        $model->saveBespokeInfo($data);
        $model->setValue('wsd_is_bespoke',2);
        $res = $model->save(true);
        //联合删除？
        //$res = $model->delete();
        if($res !== false){
            $result['success'] = 1;
        }else{
            $result['error'] = "预约失败！";
        }
        Util::jsonExit($result);
    }
}

?>