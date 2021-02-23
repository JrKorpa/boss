<?php
/**
 *  -------------------------------------------------
 *   @file		: AppBespokeInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 16:15:00
 *   @update	:
 *  -------------------------------------------------
 */
class AppBespokeInfoController extends CommonController
{
	protected $smartyDebugEnabled = true;
    protected $whitelist = array('dow');
    protected $channellist = '';
    protected $CustomerSourcesData = array();
    protected $CustomerSourcesName = array();
    protected $channel = array();
    protected $channelName = array();
    public function __construct() {
        parent::__construct();
        $departmentModel = new DepartmentModel(1);
        $departmentInfo = $departmentModel->getDepartmentInfo("`id`,`name`",array('is_deleted'=>0));
       
        $departmentData = array();
        if($departmentInfo){
        foreach ($departmentInfo as $val){
            $departmentData[$val['id']] = $val['name'];
        }
        }
        $this->assign('departmentData', $departmentData);

        $causeInfo = $departmentModel->getDepartmentInfo("`id`,`name`",array('parent_id'=>1));
        
        //获取事业部
        $causeData = array();
        if($causeInfo){
        foreach ($causeInfo as $val){
            $causeData[$val['id']] = $val['name'];
        }
        }
        $this->assign('causeData', $causeData);

		//渠道
		$SalesChannelsModel = new SalesChannelsModel(1);
        $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
        //获取所有数据
        $allSalesChannelsData = array();
        if($getSalesChannelsInfo){
        foreach ($getSalesChannelsInfo as $val){
            $allSalesChannelsData[$val['id']] = $val['channel_name'];
        }
        }
		$this->assign('sales_channels_idData', $allSalesChannelsData);

        //来源 
		$CustomerSourcesModel = new CustomerSourcesModel(1);
        $CustomerSourcesInfo = $CustomerSourcesModel->getCustomerSourcesList("`id`,`source_name`",'');
        //获取所有数据
        if($CustomerSourcesInfo){
        foreach ($CustomerSourcesInfo as $val){
            $this->CustomerSourcesData[$val['id']] = $val['source_name'];
            $this->CustomerSourcesName[$val['source_name']] = $val['id'];
        }
        }
        $this->assign('CustomerSourcesData', $this->CustomerSourcesData);

        //取出有权限的渠道
        $res= $this->ChannelListO();
        if($res===true){
            $SalesChannelsModel = new SalesChannelsModel(1);
            $this->channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
        }else{
            $this->channellist = $this->getchannelinfo($res);

        }
        if($this->channellist){
        foreach($this->channellist as $k=>$v){
            $this->channel[$v['id']]=$v['channel_name'];
            $this->channelName[$v['channel_name']]=$v['id'];
        }
        }
        $this->assign('channellist', $this->channellist);
    }

    /**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $SalesChannelsModel = new SalesChannelsModel(1);
        $res = $this->ChannelListO();
        if ($res === true) {
            //获取全部的有效的销售渠道
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
        } else {
            $channellist = $this->getchannelinfo($res);
        }


        $tomorrow=date("Y-m-d",time()+86400);
        $today=date("Y-m-d");
        if($channellist){
            $channellist =  array_column($channellist,'channel_name','id');
        }else{
            $channellist=array();
        }
		$this->render('app_bespoke_info_search_form.html',array('bar'=>Auth::getBar(),
            'allSalesChannelsData'=>$channellist,
            'tomorrow'=>$tomorrow,
			'crm_url' => defined('CRM_YY_URL') ? constant('CRM_YY_URL') : '', 	
            'today'=>$today,
			'userName'=>$_SESSION['userName'],
            'todoBes'=>isset($_REQUEST['todoBes']) ? $_REQUEST['todoBes'] : 0 // 0|1 是否显示待处理预约
            ));
	}

	/**
	 *	search，列表 
	 */
	public function search ($params) {
        $HB = $this->getShopHB(); // 所有店铺人员
        $HBleader = array_column($HB,'dp_leader_name','id');
        // 当前用户属于某些店的店长
        $dz_shop_ids = array();
        foreach($HBleader as $shop_id=>$strNames) {
            if (in_array($_SESSION['userName'], explode(',', $strNames))) {
                $dz_shop_ids[$shop_id] = $strNames;
            }
        }
        // 1. 取 is_dz, channel
        $is_dz=false; // 是否店长
        if(!empty($_REQUEST['department_id'])){
            $channel = $_REQUEST['department_id'];
        } else {
            $qudao = explode(',',$_SESSION['qudao']);
            if($dz_shop_ids){
                $channel = '';
                foreach($dz_shop_ids as $shop_id=>$strNames){
                    if(in_array($shop_id, $qudao)){
                        $channel .= $shop_id.",";
                    } 
                }
                $is_dz=true;
                $channel = trim($channel, ",");
            }else{
                $channel = $_SESSION['qudao'];
            }
           
            // fix bug NEW-2209
            if (intval($_SESSION['userType']) == 1) {
            	$channel = '';
                $is_dz = false;
            }
        }

        //--------------------boss_1212
        $make_order = _Request::getString('make_order') ? _Request::getString('make_order') : '';

        //如果此用户是总公司网销的顾问，并且有店铺管理权限及店长，则可以查看该店铺下所有顾问添加的预约信息
        if(strpos($channel, '163') !== false){//163，表示总公司网销渠道
            $channel = '';
            if($is_dz == true){//是店长
                $dp_people_name = array_column($HB,'dp_people_name','id');
                $dp_l_name = isset($HBleader[163]) ? $HBleader[163] : '';
                $dp_p_name = isset($dp_people_name[163]) ? $dp_people_name[163] : '';
                $make_order = rtrim($dp_l_name.','.$dp_p_name, ",");//店铺所有顾问
            }else{
                $make_order = $_SESSION['userName'];
            }
        }
        $make_order_arr = array();
        if($make_order != ''){
            $make_order_arr = explode(",", $make_order);
        }
        //-------------------end

        // 2. 取 客服帐号，这个渠道属于体验店 店长和超级管理看全部 销售顾问看个人的接待预约
        $kefu_name = '';
        if(!empty($_REQUEST['department_id'])){
            $channel_id = $_REQUEST['department_id'];
        } else {
            //获取全部的有效的销售渠道
            $res = $this->ChannelListO();
            $channel_id = $res===true ? '' : $res[0];
        }
        if($channel_id && empty($dz_shop_ids[$channel_id]) && intval($_SESSION['userType'])!=1) {
            $kefu_name = $_SESSION['userName'];
        }

		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'bespoke_sn'	=> _Request::getString("bespoke_sn"),
			'customer'	=> _Request::getString("customer"),
			'fenpei_sn'	=> _Request::getString("fenpei_sn"),
            'department_id'=> $channel,
            'customer_mobile'=> _Request::getString('customer_mobile'),
            'start_time'=> _Request::getString('start_time'),
            'end_time'=> _Request::getString('end_time'),
            'real_inshop_time_start'=> _Request::getString('real_inshop_time_start'),
            'real_inshop_time_end'=> _Request::getString('real_inshop_time_end'),
            'bespoke_inshop_time_start'=> _Request::getString('bespoke_inshop_time_start'),
            'bespoke_inshop_time_end'=> _Request::getString('bespoke_inshop_time_end'),
            'customer_source_id'=> _Request::getInt('customer_source_id'),
            're_status'=> _Request::getString('re_status'),
            'deal_status'=> _Request::getInt('deal_status'),
            'queue_status'=> _Request::getInt('queue_status'),
			'hf_status'=> _Request::getString('hf_status'),
            'bespoke_status'=> _Request::getString('bespoke_status'),
            'PageSize'=> _Request::getInt('PageSize'),
            'accecipt_man'=>_Request::getString("accecipt_man"),
            'recommender_sn'=>_Request::getString("recommender_sn"),
            'make_order' => $make_order_arr,//boss_1212
            'kefu_name'=>$kefu_name,
            'is_dz'=> $is_dz,
            'todo'=> _Request::getInt('todo')
		);

        $filter=array();
        $filter['start_time']=strtotime($args['start_time']);
        $filter['end_time']=strtotime($args['end_time'])+24*3600;
        if($filter['end_time']-$filter['start_time']>100*24*3600){
            die('预约添加时间不能超过100天');
        }

        $filter['real_inshop_time_start']=strtotime($args['real_inshop_time_start']);
        $filter['real_inshop_time_end']=strtotime($args['real_inshop_time_end'])+24*3600;
        if($filter['real_inshop_time_end']-$filter['real_inshop_time_start']>100*24*3600){
            die('实际到店时间不能超过100天');
        }

        $filter['bespoke_inshop_time_start']=strtotime($args['bespoke_inshop_time_start']);
        $filter['bespoke_inshop_time_end']=strtotime($args['bespoke_inshop_time_end'])+24*3600;
        if($filter['bespoke_inshop_time_end']-$filter['bespoke_inshop_time_start']>180*24*3600){
            die('预约到店时间不能超过180天');
        }


        $PageSize=$args['PageSize']?$args['PageSize']:40;
		//渠道  
		$SalesChannelsModel = new SalesChannelsModel(1);
        $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
        //获取所有数据
        $allSalesChannelsData = array();
        foreach ($getSalesChannelsInfo as $val){
            $allSalesChannelsData[$val['id']] = $val['channel_name'];
        }

        //来源
		$CustomerSourcesModel = new CustomerSourcesModel(1);
        $CustomerSourcesInfo = $CustomerSourcesModel->getCustomerSourcesList("`id`,`source_name`",'');
        //获取所有数据
        $CustomerSourcesData = array();
        foreach ($CustomerSourcesInfo as $val){
            $CustomerSourcesData[$val['id']] = $val['source_name'];
        }
        
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array(
            'bespoke_sn'=>$args['bespoke_sn'],
            'customer'=>$args['customer'],
            'accecipt_man'=>$args['accecipt_man'],
            'make_order'=>$args['make_order'],
            'kefu_name'=>$args['kefu_name'],
            'fenpei_sn'=>$args['fenpei_sn'],
            'department_id'=>$args['department_id'],
            'customer_mobile'=>$args['customer_mobile'],
            'start_time'=>$args['start_time'],
            'end_time'=>$args['end_time'],
            'real_inshop_time_start'=>$args['real_inshop_time_start'],
            'real_inshop_time_end'=>$args['real_inshop_time_end'],
            'bespoke_inshop_time_start'=>$args['bespoke_inshop_time_start'],
            'bespoke_inshop_time_end'=>$args['bespoke_inshop_time_end'],
            'customer_source_id'=>$args['customer_source_id'],
            're_status'=>$args['re_status'],
            'deal_status'=>$args['deal_status'],
            'queue_status'=>$args['queue_status'],
			'hf_status'=>$args['hf_status'],
            'bespoke_status'=>$args['bespoke_status'],
            'recommender_sn'=>$args['recommender_sn'],            
            'is_dz'=>$args['is_dz']
         );
        // 如果是查询待处理预约，重写条件
        if ($args['todo']==1) {
            $where['todo'] = 1;
            $where['re_status'] = 2;
            $where['bespoke_status'] = [1,2];
            $where['bespoke_inshop_time_start'] = date('Y-m-d', strtotime('-29days', strtotime(date('Y-m-d'))));
            $where['bespoke_inshop_time_end'] = date('Y-m-d', strtotime('+1days', strtotime(date('Y-m-d'))));
        }

        if($args['bespoke_sn']||$args['customer']||$args['customer_mobile']){
			$where=array();
			if($args['bespoke_sn']){
				$where['bespoke_sn']=$args['bespoke_sn'];
			}elseif($args['customer']){
				$where['customer']=$args['customer'];
			}elseif($args['customer_mobile']){
				$where['customer_mobile']=$args['customer_mobile'];
			}
        }

        /*select count(1) from app_bespoke_info where bespoke_status<>3 and re_status=2 and department_id in ({$shop_ids})
            and (accecipt_man='{$user_name}' or (accecipt_man='' and {$is_leader}))
            and bespoke_inshop_time>'{$start_date}' and bespoke_inshop_time<'{$end_date}'*/
        
		//$model = new AppBespokeInfoModel(17);
        $bespokeInfoModelR = new AppBespokeInfoModel(17);
		$data = $bespokeInfoModelR->pageList($where,$page,$PageSize,false);
		$pageData = $data;
		$args['department_id']=$_REQUEST['department_id'];
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_bespoke_info_search_page';
		$this->render('app_bespoke_info_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'allSalesChannelsData'=>$allSalesChannelsData,
			'CustomerSourcesData'=>$CustomerSourcesData,
		));
	}
	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
        $SalesChannelsModel = new SalesChannelsModel(1);
        $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
        if($_SESSION['userType']==1 || strpos($_SESSION['qudao'], '163')){//boss_1212 163表示总公司网销
            //管理员应能看到所有渠道
            $bumen =array_column($channellist,'id');
        }else{
            $bumen=$_SESSION['qudao'];
            $bumen = explode(',',$bumen);
        }
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_bespoke_info_info.html',array(
			'view'=>new AppBespokeInfoView(new AppBespokeInfoModel(17)),'edit'=>false,'parent_id'=>0,'channellist'=>$channellist,'bumen'=>$bumen,'accecipt_man'=>$_SESSION['userName']
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
            
            $newmodel =  new AppBespokeInfoModel($id,18);
            $olddo = $newmodel->getDataObject();
                  
            if ($olddo['bespoke_status'] == 2) { //已审核预约不能再编辑
                //$result['content'] = "已审核预约不能再编辑！";
                //Util::jsonExit($result);
            }

            $res= $this->ChannelListO();
            if($res===true){
                $SalesChannelsModel = new SalesChannelsModel(1);
                $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
            }else{
                $channellist = $this->getchannelinfo($res);

            }
            $result['content'] = $this->fetch('app_bespoke_info_info.html',array(
                    'view'=>new AppBespokeInfoView(new AppBespokeInfoModel($id,17)),'edit'=>true,'parent_id'=>3,'channellist'=>$channellist,
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
        $this->render('app_bespoke_info_show.html',array(
            'view'=>new AppBespokeInfoView(new AppBespokeInfoModel($id,17)),
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
        $member_type=1;
		$newdo = array();

		$mobile = _Request::getString('customer_mobile');
        if(empty($mobile)){
            $result['error'] = '请输入客户手机!';
    		Util::jsonExit($result);
        }
        if(!preg_match("/^1\d{10}$/",$mobile)){
            $result['error'] = '客户手机格式不对!';
    		Util::jsonExit($result);        
        }
		$customer=_Request::getString('customer');
        if(empty($customer)){
            $result['error'] = '请输入客户姓名!';
    		Util::jsonExit($result);
        }
        /*if(Util::isChineseE($customer)){
            $result['error'] = '客户姓名只能是汉字和字母!';
    		Util::jsonExit($result);
        } 
		*/
		$bespoke_inshop_time=_Request::getString('bespoke_inshop_time');
        if(empty($bespoke_inshop_time)){
            $result['error'] = '请输入预约到店时间!';
    		Util::jsonExit($result);
        }

        $remark = _Request::getString('remark');
        if(empty($remark)){
            //$result['error'] = '请输入备注!';
    		//Util::jsonExit($result);
        }
        $customer_source_id = _Request::getInt('customer_source_id');
        if(empty($customer_source_id)){
            $result['error'] = '请输入客户来源!';
    		Util::jsonExit($result);
        }
		$newdo['department_id']=_Request::getInt('department_id');
        if(empty($newdo['department_id'])){
            $result['error'] = '请输入渠道部门!';
    		Util::jsonExit($result);
        }

        $where['id']=$newdo['department_id'];
        //渠道
        $SalesChannelsModel = new SalesChannelsModel(1);
        $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`,`channel_class`",$where);
        if($getSalesChannelsInfo){
            if($getSalesChannelsInfo[0]['channel_class']==2){
                $newdo['bespoke_status']=2;
            }
        }

		$nowdatetime = date("Y-m-d H:i:s");
		if($nowdatetime>$bespoke_inshop_time." ".date("H:i:s")){
            $result['error'] = '预约到店时间早于当前时间，不能选择!';
    		Util::jsonExit($result);			
		}

		$accecipt_man = _Request::getString("accecipt_man");
		if(empty($accecipt_man)){
            $result['error'] = '接待人不能为空!';
    		Util::jsonExit($result);			
		}
		$model = new UserModel(1);
        $accecipt = $model->getAccountId($accecipt_man);
		if(empty($accecipt)){
			$result['error'] = '接待人，系统不存在!';
    		Util::jsonExit($result);	
		}
        $newmodel =  new AppBespokeInfoModel(18);
        $olddo = $newmodel->get_repeat_bespoke2($mobile, $newdo['department_id']);
        if ($olddo) {
            $result['error'] = $olddo['bespoke_sn'].'：预约已存在, 同一手机号 同一渠道不允许同时存在2个未结束服务的预约';
            Util::jsonExit($result);
        }

		//唯一预约号
		do{
			$besp_sn=$newmodel->create_besp_sn();
			//error
			if(!$newmodel->get_bespoke_by_besp_sn($besp_sn)){
				break;
			}
		}while(true);

		$newdo['customer']=$customer;
		$newdo['customer_mobile']=$mobile;
		$newdo['customer_email']=_Request::getString('customer_email');
		$newdo['bespoke_sn']=$besp_sn;

		$newdo['create_time']=date("Y-m-d H:i:s");
		$newdo['bespoke_inshop_time']=$bespoke_inshop_time;
		$newdo['make_order']=$_SESSION['userName'];
		$newdo['accecipt_man']=$accecipt_man;
        $newdo['remark']=$remark;
        $newdo['customer_source_id']=$customer_source_id;
        $basememberModel=new BaseMemberInfoModel(17);
        $userInfo=$basememberModel->getMemByMobile($mobile);
        if(!$userInfo){
            $basemember=array();
            $basemember['customer_source_id']=$customer_source_id;
            $basemember['member_name']=$customer;
            $basemember['department_id']=$newdo['department_id'];
            $basemember['member_phone']=$mobile;
            $basemember['member_email']=$newdo['customer_email'];
            $basemember['member_type'] = $member_type;
            $basemember['reg_time'] = time();
            $basemember['make_order'] = $_SESSION['userName'];
            $userInfo=$basememberModel->saveData($basemember,array()); 
            $newdo['mem_id']=$userInfo;
        }else{
            $newdo['mem_id']=$userInfo['member_id'];
        }
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;

            $logmodel =  new AppBespokeActionLogModel(17);
            $bespokeActionLog=array();
            $bespokeActionLog['bespoke_id']=$res;
            $bespokeActionLog['create_user']=$_SESSION['userName'];
            $bespokeActionLog['create_time']=date("Y-m-d H:i:s");
            $bespokeActionLog['IP']=Util::getClicentIp();
            $bespokeActionLog['remark']="'".$_SESSION['userName']."'"."添加预约";
            if($getSalesChannelsInfo){
                if($getSalesChannelsInfo[0]['channel_class']==2){
                    $bespokeActionLog['bespoke_status']=2;
                }else{
                    $bespokeActionLog['bespoke_status']=1;
                }
            }else{
                $bespokeActionLog['bespoke_status']=1; 
            }
            $logmodel->saveData($bespokeActionLog,array());
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

            //$department_id=_Request::getInt('department_id');
            //if(empty($department_id)){
            //    $result['error'] = '请输入预约部门!';
            //            Util::jsonExit($result);
            //}
            
		$newmodel =  new AppBespokeInfoModel($id,18);

		$olddo = $newmodel->getDataObject();
		$Bespokeolddo = $olddo;
                
		$newdo=array();
		$newdo['bespoke_id']=$id;
        $newdo['customer_mobile'] = _Request::getString('customer_mobile');
        $bespoke_status = $olddo['bespoke_status'];
        if ($bespoke_status == 3) { //作废状态下不允许编辑
            $result['error'] = "作废状态无法保存！";
            Util::jsonExit($result);
        }
        if(empty($newdo['customer_mobile'])){
            $result['error'] = '请输入客户手机!';
    		Util::jsonExit($result);
        }
        if(!preg_match("/^1\d{10}$/",$newdo['customer_mobile'])){
            $result['error'] = '客户手机格式不对!';
    		Util::jsonExit($result);        
        }
        //$newdo['department_id']=$department_id;
        //if(empty($newdo['department_id'])){
        //    $result['error'] = '请输入部门!';
    		//Util::jsonExit($result);
        //}
		$newdo['customer']=_Request::getString('customer');
        if(empty($newdo['customer'])){
            $result['error'] = '请输入客户姓名!';
    		Util::jsonExit($result);
        }
		$newdo['customer_email']=_Request::getString('customer_email');
		$newdo['accecipt_man']=_Request::getString('accecipt_man');
		$model = new UserModel(1);
        $accecipt = $model->getAccountId($newdo['accecipt_man']);
		if(empty($accecipt)){
			$result['error'] = '接待人，系统不存在!';
    		Util::jsonExit($result);	
		}
		/*$newdo['customer_address']=_Request::getString('customer_address');
		if(empty($newdo['customer_address'])){
            $result['error'] = '请输入客户地址!';
    		Util::jsonExit($result);			
		}*/
        $newdo['bespoke_inshop_time']=_Request::getString('bespoke_inshop_time');
        if(empty($newdo['bespoke_inshop_time'])){
            $result['error'] = '请输入预约到店时间!';
    		Util::jsonExit($result);
        }
        //$newdo['customer_source_id']=_Request::getInt('customer_source_id');
        //if(empty($newdo['customer_source_id'])){
        //    $result['error'] = '请输入客户来源!';
    		//Util::jsonExit($result);
        //}
        $newdo['remark']=_Request::getString('remark');
        if(empty($newdo['remark'])){
            $result['error'] = '请输入备注!';
    		Util::jsonExit($result);
        }

		$nowdatetime = date("Y-m-d H:i:s");

		if($nowdatetime>$newdo['bespoke_inshop_time']." ".date("H:i:s")){
            $result['error'] = '预约到店时间早于当前时间，不能选择!';
    		Util::jsonExit($result);			
		}

        $bes = $newmodel->get_repeat_bespoke2($newdo['customer_mobile'], $olddo['department_id']);
        if ($bes) {
            $result['error'] = $bes['bespoke_sn'].'：预约已存在, 同一手机号 同一渠道不允许同时存在2个未结束服务的预约';
            Util::jsonExit($result);
        }

		$res = $newmodel->saveData($newdo,$olddo);

		if($res !== false)
		{
			$result['success'] = 1;
            $basememberModel=new BaseMemberInfoModel(17);
            $olddo = $basememberModel->getDataObject();
		    $mobile = $newdo['customer_mobile'];    
            $User_List = $basememberModel->getMemByMobile($mobile);
            if($User_List){
				$DasememberModel=new BaseMemberInfoModel($User_List['member_id'],17);
				//$DasememberModel->setValue('customer_source_id',$newdo['customer_source_id']);
				$DasememberModel->setValue('member_name',$newdo['customer']);
				//$DasememberModel->setValue('department_id',$newdo['department_id']);
				$DasememberModel->setValue('member_phone',$newdo['customer_mobile']);
				$DasememberModel->setValue('member_email',$newdo['customer_email']);
				$DasememberModel->save(true);
            }
            //操作日志
            $where='';
            if($newdo['customer_mobile']!=$Bespokeolddo['customer_mobile']){
                $where.="'".$_SESSION['userName']."'已将手机号'".$Bespokeolddo['customer_mobile']."'改为'".$newdo['customer_mobile']."',";
            }
            if($newdo['customer']!=$Bespokeolddo['customer']){
                $where.="'".$_SESSION['userName']."'已将客户姓名'".$Bespokeolddo['customer']."'改为'".$newdo['customer']."',";
            }
            if($newdo['customer_email']!=$Bespokeolddo['customer_email']){
                $where.="'".$_SESSION['userName']."'已将客户email'".$Bespokeolddo['customer_email']."'改为'".$newdo['customer_email']."',";
            }
            if($newdo['accecipt_man']!=$Bespokeolddo['accecipt_man']){
                $where.="'".$_SESSION['userName']."'已将接待人'".$Bespokeolddo['accecipt_man']."'改为'".$newdo['accecipt_man']."',";
            }
            if($newdo['bespoke_inshop_time']!=$Bespokeolddo['bespoke_inshop_time']){
                $where.="'".$_SESSION['userName']."'已将预约到店时间'".$Bespokeolddo['bespoke_inshop_time']."'改为'".$newdo['bespoke_inshop_time']."',";
            }
            if($newdo['remark']!=$Bespokeolddo['remark']){
                $where.="'".$_SESSION['userName']."'已将预约备注'".$Bespokeolddo['remark']."'改为'".$newdo['remark']."',";
            }
            
            if($where){
                $logmodel =  new AppBespokeActionLogModel(17);
                $bespokeActionLog=array();
                $bespokeActionLog['bespoke_id']=$newdo['bespoke_id'];
                $bespokeActionLog['create_user']=$_SESSION['userName'];
                $bespokeActionLog['create_time']=date("Y-m-d H:i:s");
                $bespokeActionLog['IP']=Util::getClicentIp();
                $bespokeActionLog['remark']="'".$_SESSION['userName']."'"."编辑预约,操作：".$where;
                $bespokeActionLog['bespoke_status']=$Bespokeolddo['bespoke_status'];
                $logmodel->saveData($bespokeActionLog,array());
            }

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
		$model = new AppBespokeInfoModel($id,18);
		$do = $model->getDataObject();
        if($do['bespoke_status']==2){
            $result['error'] = '预约已审核，不能删除';
            Util::jsonExit($result);
        }
        if($do['re_status']==1){
            $result['error'] = '预约已到店，不能删除';
            Util::jsonExit($result);
        }
		$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

	/**
	 *	bespokeCheck，审核
	 */
	public function bespokeCheck ($params)
	{
		$id = intval($params["id"]);

		$newmodel =  new AppBespokeInfoModel($id,17);
		$olddo = $newmodel->getDataObject();
		$bespoke_status = $olddo['bespoke_status'];
        if($bespoke_status!=1){
            $result['content'] = '保存状态下才能审核预约！';
		    Util::jsonExit($result);
		}

        $model = new UserChannelModel(1);
        $accecipt_manlist = $model->get_user_channel_by_channel_id($olddo['department_id']);

		$view = new AppBespokeInfoView(new AppBespokeInfoModel($id,17));
		$this->render('app_bespoke_info_edit_check.html',
			array(
				'view'=>$view,
				'accecipt_manlist'=>$accecipt_manlist
			)
		); 
	}
	/**
	 *	bespokeCheck，审核通过
	 */
	public function bespokeCheckDo ($params)
	{
		$result = array('success' => 0,'error' =>'');

		$id = _Request::getInt('id');
		$remark = _Request::getString('remark');
		$accecipt_man = _Request::getString('accecipt_man');

		$newmodel =  new AppBespokeInfoModel($id,17);
		$olddo = $newmodel->getDataObject();
		$bespoke_status = $olddo['bespoke_status'];

        if($bespoke_status!=1){
            $result['error'] = '保存状态下才能审核预约！';
		    Util::jsonExit($result);
		}

		$newdo=array();
		$newdo['bespoke_id']=$id;
		$newdo['accecipt_man']= $accecipt_man;
		$newdo['bespoke_status']=2;
		$res = $newmodel->saveData($newdo,$olddo);

		if($res !== false)
		{
			$logmodel = new AppBespokeActionLogModel(17);
			$olddo = array();
			$newdo=array(
			'bespoke_id'=>$id,
			'create_user'=>$_SESSION['userName'],
			'create_time'=>date("Y-m-d H:i:s"),
			'IP'=>Util::getClicentIp(),
			'bespoke_status'=>$bespoke_status,
			'remark'=>"'".$_SESSION['userName']."'"."审核预约,接待人是'".$newdo['accecipt_man']."',操作：".$remark
			);

			$newmodel =  new AppBespokeActionLogModel(18);
			$res = $newmodel->saveData($newdo,$olddo);
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

    /**
	 *	BespokeRestatusedAdd，到店提示
	 */
	public function BespokeRestatusedAdd ($params)
	{
		$result = array('success' => 0,'error' => '');
        $id = intval($params['id']);
        //$UserModel=new UserModel(1);
        //$make_order=$UserModel->getUserList();
		$newmodel =  new AppBespokeInfoModel($id,18);
		$olddo = $newmodel->getDataObject();

        $model = new UserChannelModel(1);
        $make_order = $model->get_channels_person_by_channel_id($olddo['department_id']);
        if(empty($make_order['dp_people']) || empty($make_order['dp_people_name'])){
            $make_order = $model->get_user_channel_by_channel_id($olddo['department_id']);
        }else{
            $dp_people_name = explode(",",$make_order['dp_people_name']);
            $dp_people_name = array_filter($dp_people_name);

            $dp_leader_name = explode(",",$make_order['dp_leader_name']);
            $dp_leader_name = array_filter($dp_leader_name);
            $make_order=array();
            foreach($dp_people_name as $k=>$v){
                $make_order[]['account']=$v;
            }
            foreach($dp_leader_name as $k=>$v){
                $make_order[]['account']=$v;
            }
        }

		$view = new AppBespokeInfoView(new AppBespokeInfoModel(intval($params["id"]),17));

        if($view->get_re_status() == 1){
            $result['content'] = '预约已执行到店';
            Util::jsonExit($result);
        }

        if($view->get_bespoke_status() == 1){
            $result['title'] = '未到店';
            $result['content'] = '保存状态不能到店！';
            Util::jsonExit($result);
        }
        // add by geng, 到店抽奖码
        $re_lot_code = $this->getReLotCode($newmodel);
		$result['content'] = $this->fetch('daodian.html',array(
			'view'=>$view,'edit'=>1,'make_order'=>$make_order,'re_lot_code'=>$re_lot_code
		));
		$result['title'] = '到店';
		Util::jsonExit($result);
    }
    // add by geng, 到店抽奖码
    private function getReLotCode($newmodel) {
        $re_lot_code = Util::random(6);
        $olddo = $newmodel->get_bespoke_by_re_lot_code($re_lot_code, '1');
        if ($olddo) {
            $this->getReLotCode($newmodel);
        } else {
            return $re_lot_code;
        }
    }

    /**
	 *	BespokeRestatused，到店
	 */
	public function BespokeRestatused ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Request::getInt('id');
                /*
                $newmodel =  new AppBespokeInfoModel($id,18);
                $do = $newmodel->getDataObject();
                $bespoke_status = $do['bespoke_status'];
                if ($bespoke_status == 3) { //作废状态下不允许编辑
                    $result['error'] = "作废状态不允许编辑";
                    Util::jsonExit($result);
                }
                 * 
                 */
		$make_order = _Request::getString('make_order');
		$re_lot_code = _Request::getString('re_lot_code');

		$newmodel =  new AppBespokeInfoModel($id,18);

		$olddo = $newmodel->getDataObject();
        if($olddo['bespoke_status']!=2 && $olddo['re_status']==1){
            $result['error'] = '状态出错';
		    Util::jsonExit($result);
        }
		if($olddo['bespoke_status']==3){
            $result['error'] = '此预约已经作废';
		    Util::jsonExit($result);		
		}
        $newdo=array();
		$newdo['bespoke_id']=$id;
		$newdo['re_status']=1;
		$newdo['accecipt_man']=$make_order;
        //$newdo['make_order']= $make_order;
        $newdo['re_lot_code']=$re_lot_code;
		$newdo['queue_status']=2;
        $newdo['withuserdo'] = $olddo['withuserdo']+1;
        $newdo['real_inshop_time']=date("Y-m-d H:i:s");
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
            $logmodel =  new AppBespokeActionLogModel(17);
            $bespokeActionLog=array();
            $bespokeActionLog['bespoke_id']=$id;
            $bespokeActionLog['create_user']=$_SESSION['userName'];
            $bespokeActionLog['create_time']=date("Y-m-d H:i:s");
            $bespokeActionLog['IP']=Util::getClicentIp();
            $bespokeActionLog['bespoke_status']=$olddo['bespoke_status'];
            $bespokeActionLog['remark']="操作:到店接待人 “{$make_order}”, 到店抽奖码：".$re_lot_code;
            $logmodel->saveData($bespokeActionLog,array());
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

    /**
	 *	BespokeRestatusAdd，未到店提示
	 */
	public function BespokeRestatusAdd ($params)
	{
		$result = array('success' => 0,'error' => '');
        $id = intval($params['id']);
        //$UserModel=new UserModel(1);
        //$make_order=$UserModel->getUserList();
		$newmodel =  new AppBespokeInfoModel($id,18);
		$olddo = $newmodel->getDataObject();
        
        $model = new UserChannelModel(1);
        $make_order = $model->get_user_channel_by_channel_id($olddo['department_id']);

		$result['content'] = $this->fetch('daodian1.html',array(
			'view'=>new AppBespokeInfoView(new AppBespokeInfoModel(intval($params["id"]),17)),'edit'=>'','make_order'=>$make_order,
		));
        
		$result['title'] = '未到店';
		Util::jsonExit($result);        
    }

    /**
	 *	BespokeRestatus，未到店
	 */
	public function BespokeRestatus ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Request::getInt('id');
        $make_order = _Request::getString('make_order');
        $status = _Request::get('status');
       // var_dump($status);exit;
		$newmodel =  new AppBespokeInfoModel($id,18);

		$olddo = $newmodel->getDataObject();
        if($olddo['bespoke_status']!=2 && $olddo['re_status']==2){
            $result['error'] = '状态出错';
		    Util::jsonExit($result);
        }
		if($olddo['bespoke_status']==3){
            $result['error'] = '此预约已经作废';
		    Util::jsonExit($result);		
		}
        $newdo=array();
		$newdo['bespoke_id']=$id;
		$newdo['re_status']=$status;
        $newdo['accecipt_man']=$make_order;
        $newdo['make_order']=$make_order;
        $newdo['queue_status']=1;
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
	public function BespokeIsdelete ($params) {
		$id = _Request::getInt('id');
		$ids = _Request::getList('_ids');
        if ($ids) {
            foreach($ids as $id) {
                $result = $this->_bespokeIsdelete($id);
                // 有一个不成功，就提示失败
                if (empty($result['success'])) break;
            }
        } else {
            $result = $this->_bespokeIsdelete($id);
        }
		Util::jsonExit($result);
	}
    private function _bespokeIsdelete ($id)
    {
        $result = array('success' => 0,'error' =>'');
        $newmodel =  new AppBespokeInfoModel($id,18);

        $olddo = $newmodel->getDataObject();
        if($olddo['re_status']==1){
            $result['error'] = $olddo['bespoke_sn'].': 此预约已到店，不能作废！';
            return $result;
        }
        $newdo=array();
        $newdo['bespoke_id']=$id;
        $newdo['is_delete']=1;
        $newdo['bespoke_status']=3;

        $res = $newmodel->saveData($newdo,$olddo);

        if($res !== false)
        {
            $result['success'] = 1;
            $logmodel =  new AppBespokeActionLogModel(18);
            $bespokeActionLog=array();
            $bespokeActionLog['bespoke_id']=$id;
            $bespokeActionLog['create_user']=$_SESSION['userName'];
            $bespokeActionLog['create_time']=date("Y-m-d H:i:s");
            $bespokeActionLog['IP']=Util::getClicentIp();
            $bespokeActionLog['bespoke_status']=3;
            $bespokeActionLog['remark']=$bespokeActionLog['create_user']."取消预约";
            $logmodel->saveData($bespokeActionLog,array());
        } else {
            $result['error'] = $olddo['bespoke_sn'].'修改失败';
        }
        return $result;
    }

	/**
	 *	getDepartment，取部门
	 */
	public function getDepartment ()
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Request::getInt('dep');

        $departmentModel = new DepartmentModel(1);
        $departmentInfo = $departmentModel->getDepartmentInfo("`id`,`name`",array('parent_id'=>$id));
        $this->render('area_info_options1.html', array('data' => $departmentInfo));
	}

	/**
	 *	getCustomerSource，取客户来源
	 */
	public function getCustomerSource ()
	{
		$result = array('success' => 0,'error' =>'');
        //来源
		$CustomerSourcesModel = new CustomerSourcesModel(1);
        $CustomerSourcesInfo = $CustomerSourcesModel->getCustomerSourcesList("`id`,`source_name`",'');
        //获取所有数据
        $CustomerSourcesData = array();
        foreach ($CustomerSourcesInfo as $val){
            $CustomerSourcesData[$val['id']] = $val['source_name'];
        }
        
		if($CustomerSourcesData !== '')
		{
			$result['success'] = 1;
			$result['content'] = $CustomerSourcesData;
		}
		else
		{
			$result['error'] = '无客户来源';
		}
		Util::jsonExit($result);
	}

	/**
	 *	getUserBymobile，取用户
	 */
	public function getUserBymobile ()
	{
		$result = array('success' => 0,'error' =>'');
		$mobile = _Request::getString('mobile');

        $BaseMemberInfoModel = new BaseMemberInfoModel(17);
        $User_List = $BaseMemberInfoModel->getMemByMobile($mobile);

		if(!empty($User_List))
		{
			$result['success'] = 1;
			$result['content'] = $User_List;
		}
		else
		{
			$result['error'] = '没有该用户！';
		}
		Util::jsonExit($result);
	}

    /**
	 *	modvisit，已回访
	 */
	public function modvisit ($params)
	{
            $id = intval($params["id"]);
            $result = array('success' => 0,'error' => '');
            $result['content'] = $this->fetch('app_bespoke_info_withuserdo.html',array(
                    'view'=>new AppBespokeInfoView(new AppBespokeInfoModel($id,17)),
            ));
            $result['title'] = '已回访';
            Util::jsonExit($result);       
    }

	/**
	 *	ModvisitiSelect，回访查询
	 */
	public function ModvisitiSelect ($params)
	{               
            $bespoke_id = _Request::getString('bespoke_id');
            $result = array('success' => 0,'error' => '');          

            $newmodel =  new AppBespokeInfoModel($bespoke_id,17);

            $olddo = $newmodel->getDataObject();
            $logmodel =  new AppBespokeActionLogModel(17);
            $BespokeActionLog = $logmodel->getActionLogByBespokeID($bespoke_id);
            //krsort($BespokeActionLog);
            $l='';
            if(!empty($BespokeActionLog)){
                foreach(array_reverse($BespokeActionLog) as $k=>$v){
                    if(!strstr($v['remark'],'操作:第')&&!strstr($v['remark'],'次回访--')){
                        $v['remark']="添加备注：".$v['remark'];
                    }
                    $l.="<b><font color='#FF0000'>".$v['create_user']."</font></b>"."[".$v['create_time']."]".":<br>&nbsp;&nbsp;&nbsp;&nbsp;".$v['remark']."<br><hr>";
                }
            }else{
                    $l.="<b><font color='#FF0000'></font></b>:<br>&nbsp;&nbsp;&nbsp;&nbsp;<br><hr>";            
            }
            $result['content'] = $l;
            $result['num'] = $olddo['withuserdo']+1;
			$result['bespoke_inshop_time_old'] = $olddo['bespoke_inshop_time'];
		    $result['success'] = 1;
            $result['title'] = '编辑';
            Util::jsonExit($result);
	}

	/**
	 *	ModvisitiInsert，回访插入
	 */
	public function ModvisitiInsert ($params)
	{
		$result = array('success' => 0,'error' =>'');

		$bespoke_id = _Request::getString('bespoke_id');
		$newmodel =  new AppBespokeInfoModel($bespoke_id,17);
		$olddo = $newmodel->getDataObject();
		$bespoke_inshop_time_old=$olddo['bespoke_inshop_time'];
		$remark = _Request::getString('remark');
		$bespoke_inshop_time = _Request::getString('bespoke_inshop_time');
        if(empty($remark)){
            $result['error'] = '请输入备注!';
    		Util::jsonExit($result);
        }
		$newmodel =  new AppBespokeInfoModel($bespoke_id,18);
		$olddo = $newmodel->getDataObject();
		$newdo=array();
		$newdo['bespoke_id']=$bespoke_id;
        $newdo['withuserdo'] = $olddo['withuserdo']+1;
        $newdo['bespoke_inshop_time'] = $bespoke_inshop_time;
        $newmodel->saveData($newdo,$olddo);

        $logmodel =  new AppBespokeActionLogModel(17);
        $bespokeActionLog=array();
        $bespokeActionLog['bespoke_id']=$bespoke_id;
        $bespokeActionLog['create_user']=$_SESSION['userName'];
        $bespokeActionLog['create_time']=date("Y-m-d H:i:s");
        $bespokeActionLog['IP']=Util::getClicentIp();
        $bespokeActionLog['bespoke_status']=$olddo['bespoke_status'];
		if($bespoke_inshop_time_old!=$bespoke_inshop_time){
			$bespokeActionLog['remark']="操作:第".$newdo['withuserdo']."次回访--备注：".$remark."--到店时间由:".$bespoke_inshop_time_old."修改为:".$bespoke_inshop_time;
			$newmodel =  new AppBespokeInfoModel($bespoke_id,18);
			$newmodel->setValue('bespoke_inshop_time',$bespoke_inshop_time);	
			$newmodel->save();
		}else{
			$bespokeActionLog['remark']="操作:第".$newdo['withuserdo']."次回访--".$remark;
		}
        $res=$logmodel->saveData($bespokeActionLog,array());
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
	 *	get_bespoke，领单操作 
	 */
	public function get_bespoke ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Request::getInt('bespoke_id');

		$newmodel =  new AppBespokeInfoModel($id,18);
		
        if(!empty($_SESSION['bespoke'])){
            $result['error'] = "<span style='color:red;'>提示：</span><br/>您已经领取了一个预约单（".$_SESSION['bespoke']['bespoke_sn']."）！<br/>注意：如果没有显示正在服务的顾客请刷新页面！";
            //<span style='color:red;'>服务结束后才能领取其他预约单</span> 
		    Util::jsonExit($result);
        	$newmodel->pauseBespoke($_SESSION['bespoke']['bespoke_sn']);
        }

		$olddo = $newmodel->getDataObject();
        if($olddo['re_status']!=1){
            $result['error'] = '该预约单是未到店状态，请先点击到店';
            Util::jsonExit($result);
        }
        $_SESSION['bespoke']=$olddo;

		$newdo=array();
		$newdo['bespoke_id']=$id;
		$newdo['queue_status']=3;

		$res = $newmodel->saveData($newdo,$olddo);

		if($res !== false)
		{
			$result['success'] = 1;
            $logmodel =  new AppBespokeActionLogModel(17);
            $bespokeActionLog=array();
            $bespokeActionLog['bespoke_id']=$newdo['bespoke_id'];
            $bespokeActionLog['create_user']=$_SESSION['userName'];
            $bespokeActionLog['create_time']=date("Y-m-d H:i:s");
            $bespokeActionLog['IP']=Util::getClicentIp();
            $bespokeActionLog['remark']="'".$_SESSION['userName']."'"."领单操作";
            $bespokeActionLog['bespoke_status']=$olddo['bespoke_status'];
            $logmodel->saveData($bespokeActionLog,array());
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);        
    }

    /**
	 *	reject_bespoke，拒绝领单
	 */
	public function reject_bespoke ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Request::getInt('bespoke_id');

		$newmodel =  new AppBespokeInfoModel($id,18);

		$olddo = $newmodel->getDataObject();

		$newdo=array();
		$newdo['bespoke_id']=$id;
        $newdo['accecipt_man']=''; // make_order
        $newdo['bespoke_status']=2; // 已审核
        $newdo['queue_status']=2; // 待服务，领单后是服务中3

		$res = $newmodel->saveData($newdo,$olddo);

		if($res !== false)
		{
			$result['success'] = 1;
            $logmodel =  new AppBespokeActionLogModel(17);
            $bespokeActionLog=array();
            $bespokeActionLog['bespoke_id']=$newdo['bespoke_id'];
            $bespokeActionLog['create_user']=$_SESSION['userName'];
            $bespokeActionLog['create_time']=date("Y-m-d H:i:s");
            $bespokeActionLog['IP']=Util::getClicentIp();
            $bespokeActionLog['remark']="'".$_SESSION['userName']."'"."拒绝领单";
            $bespokeActionLog['bespoke_status']=$newdo['bespoke_status'];
            $logmodel->saveData($bespokeActionLog,array());
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);        
    }

	/**
	 *	finish_Bespoke_box，你正在服务的用户
	 */
	public function finish_Bespoke_box ($params)
	{
            $id = intval($params["bespoke_id"]);
            $newmodel =  new AppBespokeInfoModel($id,18);
            $olddo = $newmodel->getDataObject();
            
            $model = new UserChannelModel(1);
            $make_order = $model->get_user_channel_by_channel_id($olddo['department_id']);
            $this->render('app_bespoke_info_finish_Bespoke.html',array(
                          'view'=>new AppBespokeInfoView(new AppBespokeInfoModel($id,17)),'make_order'=>$make_order,"beid"=>$_SESSION['bespoke']['bespoke_id']
                    ));
	}

    /**
     *
     * 结束预约单
     * @author changmark
     */
    public function release_bespoke(){
        $result = array('success' => 0,'error' =>'');
        // 改变状态操作 
        $is_ajax=_Request::getInt('is_ajax');
        $bespoke_id=_Request::getInt('beid', 0);
        if ($bespoke_id <= 0) $bespoke_id=_Request::getInt('bespoke_id', 0);
        $action_note=_Request::getString('action_note');
        $salesstage=_Request::getString('salesstage', 1);
        $brandimage=_Request::getString('brandimage', 1);

        if($is_ajax==1&&$bespoke_id>0){

            $newmodel =  new AppBespokeInfoModel($bespoke_id,18);
            $olddo = $newmodel->getDataObject();

            $updatedata=array();
            $updatedata['bespoke_id']=$bespoke_id;
            $updatedata['queue_status']=4;
            $updatedata['salesstage']=$salesstage;
            $updatedata['brandimage']=$brandimage;
            $res = $newmodel->saveData($updatedata,$olddo);
            if($res !== false){
                unset($_SESSION['bespoke']);
                $logmodel =  new AppBespokeActionLogModel(17);
                $bespokeActionLog=array();
                $bespokeActionLog['bespoke_id']=$bespoke_id;
                $bespokeActionLog['create_user']=$_SESSION['userName'];
                $bespokeActionLog['create_time']=date("Y-m-d H:i:s");
                $bespokeActionLog['IP']=Util::getClicentIp();
                $bespokeActionLog['bespoke_status']=$olddo['bespoke_status'];
                $bespokeActionLog['remark']="'".$_SESSION['userName']."'结束预约".($action_note?",操作：$action_note":'');
                $logmodel->saveData($bespokeActionLog,array());
                $result['success'] = 1;
            }else{
                $result['error'] = '操作失败';
            }
        }else{
            $result['error'] = '预约单号不能为空';
            Util::jsonExit($result);
        }
        Util::jsonExit($result); 
    }

	/**
	 *
	 * 移交预约	//from sale.php  
	 * @author changmark
	 */
	public function handover_bespoke(){
        $result = array('success' => 0,'error' =>'');
		$is_ajax=_REQUEST::getInt('is_ajax');
		$bespoke_id=_REQUEST::getInt('bespoke_id');
		if($is_ajax==1&&$bespoke_id>0){
			$make_order=urldecode(_REQUEST::getString('make_order'));
			if($make_order==''){
                $result['error'] = '转交销售顾问不能为空';
                Util::jsonExit($result);
			}else{
                $newmodel =  new AppBespokeInfoModel($bespoke_id,18);
                $olddo = $newmodel->getDataObject();

                $updatedata=array();
                $updatedata['bespoke_id']=$bespoke_id;
                $updatedata['accecipt_man']=$make_order;
                $updatedata['queue_status']=2;
                $res = $newmodel->saveData($updatedata,$olddo);
				if($res !== false){
					unset($_SESSION['bespoke']);
					$result['content']='已经转交给销售顾问：'.$make_order;
					$result['success'] = 1;
                    $logmodel =  new AppBespokeActionLogModel(17);
                    $bespokeActionLog=array();
                    $bespokeActionLog['bespoke_id']=$updatedata['bespoke_id'];
                    $bespokeActionLog['create_user']=$_SESSION['userName'];
                    $bespokeActionLog['create_time']=date("Y-m-d H:i:s");
                    $bespokeActionLog['IP']=Util::getClicentIp();
                    $bespokeActionLog['remark']="'".$_SESSION['userName']."'"."已转交预约给'".$updatedata['make_order']."'";
                    $bespokeActionLog['bespoke_status']=$olddo['bespoke_status'];
                    $logmodel->saveData($bespokeActionLog,array());
				}else{
                    $result['error'] = '操作失败，请刷新页面后尝试!';
                    Util::jsonExit($result);
				}
			}
		}else{
            $result['error'] = '预约单号不能为空';
            Util::jsonExit($result);
		}
		Util::jsonExit($result); 
	}

	/**
	 *
	 * Enter 暂停服务
	 * @author changmark
	 */
	public function bespoke_pause(){
        $result = array('success' => 0,'error' =>'');
		$is_ajax=_REQUEST::getInt('is_ajax');
		$bespoke_id=_REQUEST::getInt('bespoke_id');
		if($is_ajax==1&&$bespoke_id>0){
            $newmodel =  new AppBespokeInfoModel($bespoke_id,18);
            $olddo = $newmodel->getDataObject();

            $updatedata=array();
            $updatedata['bespoke_id']=$bespoke_id;
            //update by liulinyan 20151225 for boss_1018 结束服务不能修改预约单的创建人
			//$updatedata['make_order']=$_SESSION['userName'];
            $updatedata['bespoke_status']=2;
            $updatedata['re_status']=1;
            $updatedata['queue_status']=2;
            $res = $newmodel->saveData($updatedata,$olddo);
			if($res !== false){
                unset($_SESSION['bespoke']);
                $result['success'] = 1;
                $result['error'] = '已暂停服务!';
                $logmodel =  new AppBespokeActionLogModel(17);
                $bespokeActionLog=array();
                $bespokeActionLog['bespoke_id']=$updatedata['bespoke_id'];
                $bespokeActionLog['create_user']=$_SESSION['userName'];
                $bespokeActionLog['create_time']=date("Y-m-d H:i:s");
                $bespokeActionLog['IP']=Util::getClicentIp();
                $bespokeActionLog['remark']="'".$_SESSION['userName']."'"."暂停服务";
                $bespokeActionLog['bespoke_status']=$updatedata['bespoke_status'];
                $logmodel->saveData($bespokeActionLog,array());
			}else{
                $result['error'] = '预操作失败，请刷新页面后尝试!';
                Util::jsonExit($result);
			}
		}else{
            $result['error'] = '预约单号不能为空!';
            Util::jsonExit($result);
		}
		Util::jsonExit($result);
	}

	/**
	 *
	 * to_look_into 预约查看
	 * @author changmark
	 */
	public function to_look_into(){
        $result = array('success' => 0,'error' =>'');
		$is_ajax=_REQUEST::getInt('is_ajax');
		$bespoke_id=_REQUEST::getInt('bespoke_id');
        $newmodel =  new AppBespokeInfoModel($bespoke_id,99);
        //$olddo = $newmodel->getDataObject();

        $res=$newmodel->get_bespoke_list_by_accecipt_man($_SESSION['userName'],2);
        $s=$newmodel->get_bespoke_list_by_accecipt_man($_SESSION['userName'],3);
        if($s){
            $_SESSION['bespoke']=$s[0];
            $res = array_merge($s, $res);
        }

        if($res !== false){
            $result['success'] = 1;
            $result['content'] = $this->fetch('tolookinto.html', array(
                'besp_list0'=>$res,'count_besp_list0'=>count($res)
            ));
        }else{
            $result['error'] = '没有该预约信息!';
            Util::jsonExit($result);
        }

		Util::jsonExit($result);
	}

    /**
     * 	upload，批量上传预约
     */
    public function upload() {
		$result = array('success' => 0,'error' => '');

		$result['content'] = $this->fetch('app_bespoke_info_upload.html',array(
			'view'=>new AppBespokeInfoView(new AppBespokeInfoModel(17)),'edit'=>false,'parent_id'=>0
		));
		$result['title'] = '批量上传';
		Util::jsonExit($result);
    }

	/**
	 *	dow，模板
	 */
	public function dow ()
	{
        //$customer_source_id=_REQUEST::getString('customer_source_id');
        //$department_id=_REQUEST::getString('department_id');
        $title = array(
				'*客户手机',
				'*客户姓名',
				'客户email',
				'*预约到店时间',
				'顾问',
				'*接待人',
                '预约备注');
			$newdo = array();
			$newdo[0]['customer_mobile'] = '18319098978';
			$newdo[0]['customer'] = '测试';
			$newdo[0]['customer_email'] = '18319098978@kela.cn';
            $newdo[0]['bespoke_inshop_time'] = '2015-03-21';
			$newdo[0]['make_order'] = '测试';
			$newdo[0]['accecipt_man'] = '技术测试';
			$newdo[0]['remark'] = '测试备注';
            Util::downloadCsv("预约列表",$title,$newdo);
    }

    /**
     * 	upload_ins，批量上传预约
     */
    public function upload_ins() {
        $result = array('success' => 0, 'error' => '');
		$upload_name = $_FILES;
        if (!$upload_name) {
            $result['error'] = '文件不能为空';
            Util::jsonExit($result);
        }		
        if (Upload::getExt($upload_name['file_price']['name']) != 'csv') {
            $result['error'] = '请上传csv格式文件';
            Util::jsonExit($result);
        }
        $customer_source_id=_REQUEST::getString('customer_source_id');
        $department_id=_REQUEST::getString('department_id');
		$file = fopen($upload_name['file_price']['tmp_name'], 'r');
        while ($data = fgetcsv($file)) {
			foreach($data as $k => $v){
				$data[$k] = iconv("GBK","UTF-8",$v);
			}
			$data_r[]=$data;
        }

        $bespokeinfoModel = new AppBespokeInfoModel(17);
        $basememberModel=new BaseMemberInfoModel(17);

		$header_target="*客户手机,*客户姓名,客户email,*预约到店时间,顾问,*接待人,预约备注";
		$header=implode(',',array_shift($data_r));

		if($header != $header_target){
			$result['error'] = '表头出错';
			Util::jsonExit($result);
		}

        if(!$data_r){
            $result['error'] = '无信息上传!';
            Util::jsonExit($result);
        }
        if(!$customer_source_id){
            $result['error'] = '请选择客户来源！';
            Util::jsonExit($result);
        }
        if(!$department_id){
            $result['error'] = '请选择销售渠道！';
            Util::jsonExit($result);
        }
        $i=1;
		foreach($data_r as $k=>$v){

            $member_type=1;
			$olddo=array();
			$newdo = array();
            //唯一预约号
            do{
                $besp_sn=$bespokeinfoModel->create_besp_sn();
                //error
                if(!$bespokeinfoModel->get_bespoke_by_besp_sn($besp_sn)){
                    break;
                }
            }while(true);
			$newdo['bespoke_sn'] = $besp_sn;
			$newdo['customer_mobile'] = $v[0];
			$newdo['customer'] = $v[1];
			$newdo['customer_source_id'] = $customer_source_id;
			$newdo['customer_email'] = $v[2];
            $newdo['bespoke_inshop_time'] = $v[3];
            $newdo['department_id']=$department_id;
			$newdo['make_order'] = $v[4];
			$newdo['accecipt_man'] = $v[5];

            $i++;
            if($newdo['customer_mobile'] == '' || $newdo['customer'] == '' || $newdo['accecipt_man'] == '' || $newdo['bespoke_inshop_time'] == ''){
                $result['error'] = "第".$i."行, 手机号、客户姓名、接待人必填、预约到店时间！";
                Util::jsonExit($result);
            }
            if(!preg_match('/^1\d{10}$/',$newdo['customer_mobile'])){

                $result['error'] = "第".$i."行, 手机号码不合法！";
                Util::jsonExit($result);
            }

			$model = new UserModel(1);
			$accecipt = $model->getAccountId($newdo['accecipt_man']);
			if(empty($accecipt)){
				$result['error'] = '接待人'.$newdo['accecipt_man'].'，系统不存在!';
				Util::jsonExit($result);
			}
            $userInfo=$basememberModel->getMemByMobile($newdo['customer_mobile']);
            if(!$userInfo){
                $basemember=array();
                $basemember['customer_source_id']=$newdo['customer_source_id'];
                $basemember['member_name']=$newdo['customer'];
                $basemember['department_id']=$newdo['department_id'];
                $basemember['member_phone']=$newdo['customer_mobile'];
                $basemember['member_email']=$newdo['customer_email'];
                //$basemember['member_address']=$newdo['customer_address'];
                $basemember['member_type'] = $member_type;
                $userInfo=$basememberModel->saveData($basemember,array()); 
                $newdo['mem_id']=$userInfo;
            }else{
                $newdo['mem_id']=$userInfo['member_id'];
            }
			$newdo['create_time'] = date("Y-m-d H:i:s");
			$newdo['remark'] = $v[6];
			$res = $bespokeinfoModel->saveData($newdo, $olddo);
		}
		if ($res !== false) {
			$result['success'] = 1;
		} else {
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
    }

    /**
	 *	accecipt_man，接待人
	 */
	public function accecipt_man ($params)
	{
		$result = array('success' => 0,'error' => '');
        $id = intval($params['id']);
		$newmodel =  new AppBespokeInfoModel($id,18);
		$olddo = $newmodel->getDataObject();

        if($olddo['bespoke_status']==3){
            $result['content'] = '此预约已作废';
		    Util::jsonExit($result);
        }

        if($olddo['queue_status']==3){
            $result['content'] = '服务状态中不可分配接待人';
		    Util::jsonExit($result);
        }elseif($olddo['queue_status']==4){
            $result['content'] = '此预约已结束服务';
		    Util::jsonExit($result);            
        }

		/*if($olddo['re_status']!=1){
            $result['content'] = '此预约未到店';
		    Util::jsonExit($result);		
		}*/       
        $model = new UserChannelModel(1);
        $make_order = $model->get_channels_person_by_channel_id($olddo['department_id']);
        if($make_order['dp_people']=='' || $make_order['dp_people_name']==''){
            //die(1);
            $make_order = $model->get_user_channel_by_channel_id($olddo['department_id']);
        }else{
            //$dp_people = explode(",",$make_order['dp_people']);
            $dp_people_name = explode(",",$make_order['dp_people_name']);
            $dp_people_name = array_filter($dp_people_name);
            $dp_leader_name = explode(",",$make_order['dp_leader_name']);
            $dp_leader_name = array_filter($dp_leader_name);
            $make_order=array();
            foreach($dp_people_name as $k=>$v){
                $make_order[]['account']=$v;
            }
            foreach($dp_leader_name as $k=>$v){
                $make_order[]['account']=$v;
            }
        }
        
		$view = new AppBespokeInfoView(new AppBespokeInfoModel(intval($params["id"]),17));

		$result['content'] = $this->fetch('accecipt_man.html',array(
			'view'=>$view,'edit'=>2,'make_order'=>$make_order,
		));
		$result['title'] = '接待人';
		Util::jsonExit($result);        
    }
    
    
    /**
	 *	batch_accecipt_man，批量分配接待人
	 */
	public function batch_accecipt_man ($params)
	{
		$result = array('success' => 0,'error' => '');
        $ids = $params['_ids'];
        $newmodel =  new AppBespokeInfoModel(18);
        $_ids = implode(',', $ids);
        $data_info = $newmodel->getInfoByIds($_ids);
        
        //验证每条数据，筛选出不符合条件分配接待人的数据
        $no_accecipt_man = array();
        $dempartment_data = array();
        foreach($data_info as $val){
            if($val['bespoke_status']==3){
                $no_accecipt_man[$val['bespoke_id']] = $val['bespoke_id'];
            }
            if($val['queue_status']==3 || $val['queue_status']==4){
                $no_accecipt_man[$val['bespoke_id']] = $val['bespoke_id'];
            }
            $dempartment_data[$val['department_id']] = $val['department_id'];
        }
        
        //不符合条件分配接待人的数据，抛出错误提示
        if(count($no_accecipt_man)){
            $result['content'] = '未服务和待服务的预约才可以分配接待人';
            $result['title'] = '友情提示';
		    Util::jsonExit($result);
        }
        
        //预约数据多余一个销售渠道，抛出错误提示
        if(count($dempartment_data)>1){
            $result['content'] = '同一个销售渠道的预约才可以批量分配接待人';
            $result['title'] = '友情提示';
		    Util::jsonExit($result);
        }
        $model = new UserChannelModel(1);
        $make_order = $model->get_channels_person_by_channel_id(current($dempartment_data));
        if($make_order['dp_people']=='' || $make_order['dp_people_name']==''){
            //die(1);
            $make_order = $model->get_user_channel_by_channel_id(current($dempartment_data));
        }else{
            //$dp_people = explode(",",$make_order['dp_people']);
            $dp_people_name = explode(",",$make_order['dp_people_name']);
            $dp_people_name = array_filter($dp_people_name);
            $dp_leader_name = explode(",",$make_order['dp_leader_name']);
            $dp_leader_name = array_filter($dp_leader_name);
            $make_order=array();
            foreach($dp_people_name as $k=>$v){
                $make_order[]['account']=$v;
            }
            foreach($dp_leader_name as $k=>$v){
                $make_order[]['account']=$v;
            }
        }

		$view = new AppBespokeInfoView(new AppBespokeInfoModel(current($ids),17));

		$result['content'] = $this->fetch('accecipt_man.html',array(
			'view'=>$view,'edit'=>22,'make_order'=>$make_order,'batch_ids'=>$_ids
		));
		$result['title'] = '接待人';
		Util::jsonExit($result);        
    }

    /**
	 *	accecipt_maned，分配接待人
	 */
	public function accecipt_maned ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Request::getInt('id');
		$make_order = _Request::getString('make_order');
        
		$newmodel =  new AppBespokeInfoModel($id,18);
		$olddo = $newmodel->getDataObject();
        if($olddo['queue_status']==3){
            unset($_SESSION['bespoke']);
        }

        $newdo=array();
		$newdo['bespoke_id']=$id;
		$newdo['accecipt_man']=$make_order;
		$newdo['queue_status']=2;

		$res = $newmodel->saveData($newdo,$olddo);

		if($res !== false)
		{
			$result['success'] = 1;
            $logmodel =  new AppBespokeActionLogModel(17);
            $bespokeActionLog=array();
            $bespokeActionLog['bespoke_id']=$newdo['bespoke_id'];
            $bespokeActionLog['create_user']=$_SESSION['userName'];
            $bespokeActionLog['create_time']=date("Y-m-d H:i:s");
            $bespokeActionLog['IP']=Util::getClicentIp();
            $bespokeActionLog['bespoke_status']=$olddo['bespoke_status']; 
            $bespokeActionLog['remark']="操作:分配接待人 “{$make_order}”";
            $logmodel->saveData($bespokeActionLog,array());
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}
    
    /**
	 *	batch_accecipt_maned，批量分配接待人
	 */
	public function batch_accecipt_maned ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$_ids = _Request::getString('id');
		$make_order = _Request::getString('make_order');

        $logmodel =  new AppBespokeActionLogModel(17);
		$newmodel =  new AppBespokeInfoModel(18);
        
        $data_info = $newmodel->getInfoByIds($_ids);
        foreach($data_info as $val){
            if($val['queue_status']==3){
                unset($_SESSION['bespoke']);
            }
        }

        foreach($data_info as $val){
            $newdo=array();
            $newdo['bespoke_id']=$val['bespoke_id'];
            $newdo['accecipt_man']=$make_order;
            if($val['re_status']==1){
                $newdo['make_order']=$make_order;
                $newdo['queue_status']=2;
            }
            $newModel = new AppBespokeInfoModel($val['bespoke_id'],18);
            $olddo = $newModel->getDataObject();
            $res = $newModel->saveData($newdo,$olddo);
            if($res !== false)
            {
                $result['success'] = 1;
                $bespokeActionLog=array();
                $bespokeActionLog['bespoke_id']=$newdo['bespoke_id'];
                $bespokeActionLog['create_user']=$_SESSION['userName'];
                $bespokeActionLog['create_time']=date("Y-m-d H:i:s");
                $bespokeActionLog['IP']=Util::getClicentIp();
                $bespokeActionLog['bespoke_status']=$olddo['bespoke_status']; 
                $bespokeActionLog['remark']="操作:批量分配接待人 “{$make_order}”";
                $logmodel->saveData($bespokeActionLog,array());
            }
        }
		

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
