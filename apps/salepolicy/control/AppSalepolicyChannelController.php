<?php
/**
 *  -------------------------------------------------
 *   @file		: AppSalepolicyChannelController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-11 10:48:52
 *   @update	:
 *  -------------------------------------------------
 */
class AppSalepolicyChannelController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('base_salepolicy_info','front',17);	//生成模型后请注释该行
		//Util::V('base_salepolicy_info',17);	//生成视图后请注释该行
		$this->render('app_salepolicy_channel_search_form.html',array('bar'=>Auth::getBar()));
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
            'policy_name' => _Request::getString("policy_name")

		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['policy_name'] = $args['policy_name'];

		$model = new AppSalepolicyChannelModel(17);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'base_salepolicy_info_search_page';
		$this->render('app_salepolicy_channel_search_list.html',array(
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
        $policy_id = _Request::getString('policy_id');


        $res= $this->ChannelListO();
        if($res===true){
            //获取全部的有效的销售渠道
            $SalesChannelsModel = new SalesChannelsModel(1);
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
        }else{
            $channellist = $this->getchannelinfo($res);
        }
        
        $policyModel = new BaseSalepolicyInfoModel($policy_id,17);
        $data = $policyModel->getDataObject();
        if($data['bsi_status']==3){
            /*$result['content'] ="<pre style='color: red'>销售政策渠道已审核不能添加渠道</pre>";
            $result['title'] = '添加渠道';
            Util::jsonExit($result);*/
        }



        //$saleschannelsmodel = new SalesChannelsModel(17);
        //接口
/*        $SalesChannelsmodel = new SalesChannelsModel(1);
        $SalesChannels=array();
        $SalesChannels=$SalesChannelsmodel->getSalesChannelsInfo('*',array());
        $channelList=array();
        foreach($SalesChannels as $k=>$v){
            $channelList[$v['id']]=$v['channel_name'];
        }*/

  /*      $department =  new DepartmentModel(1);
       $datalist =  $department->getDepartmentInfo('id,name',array());*/
        //获取该销售政策下的所有渠道id
        $channellist=array_column($channellist,'channel_name','id');
        $pchanel = $policyModel->getPchannellist($policy_id);
        if(!empty($pchanel)){
            $pchanel = array_column($pchanel,'channel');
            foreach($pchanel as $val){
                unset($channellist[$val]);
            }
        }


		$result['content'] = $this->fetch('app_salepolicy_channel_info.html',array(
			'view'=>new AppSalepolicyChannelView(new AppSalepolicyChannelModel(17)),
            'edit'=>false,
            'policy_id'=>$policy_id,
            'channelList'=>$channellist
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
        $policy_id = _Get::getInt('id');

        $res= $this->ChannelListO();
        if($res===true){
            //获取全部的有效的销售渠道
            $SalesChannelsModel = new SalesChannelsModel(1);
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
        }else{
            $channellist = $this->getchannelinfo($res);
        }

        $policyModel = new BaseSalepolicyInfoModel($policy_id,17);
        $data = $policyModel->getDataObject();
        if($data['bsi_status']==3){
            $result['content'] ="<pre style='color: red'>销售政策渠道已审核不能编辑渠道</pre>";
            $result['title'] = '编辑渠道';
            Util::jsonExit($result);
        }


        
        //$saleschannelsmodel = new SalesChannelsModel(17);
        //接口
        $SalesChannelsmodel = new SalesChannelsModel(1);
        $SalesChannels=array();
        $SalesChannels=$SalesChannelsmodel->getSalesChannelsInfo('*',array());
        $channelList=array();
        foreach($SalesChannels as $k=>$v){
            $channelList[$v['id']]=$v['channel_name'];
        }
        $department =  new DepartmentModel(1);
        $datalist =  $department->getDepartmentInfo('id,name',array());
        $result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_salepolicy_channel_info_e.html',array(
			'view'=>new AppSalepolicyChannelView(new AppSalepolicyChannelModel($id,17)),
            'edit'=>true,
            'policy_id'=>$policy_id,
            'channelList'=>$channellist,
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

		$this->render('app_salepolicy_channel_show.html',array(
			'view'=>new AppSalepolicyChannelView(new AppSalepolicyChannelModel($id,17)),
			'id' => $id,
			'dd'=>new DictView(new DictModel(1))
		));
	}

	/**
	 *	shows，销售政策详情
	 */
	public function shows ($params)
	{
		$id = intval($params["id"]);
		$result['content'] = $this->fetch('base_salepolicy_info_show_list_show.html',array(
			'view'=>new AppSalepolicyChannelView(new AppSalepolicyChannelModel($id,17))
		));
		$result['title'] = '销售政策详情';
		Util::jsonExit($result);

	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

		$olddo = array();
		$newdo=array(
			'policy_id'	=> _Request::getString("policy_id"),	
			'channel'	=> _Request::getList("channel_id"),
			'channel_level'	=> 1,	
			'create_time'	=> date("Y-m-d H:i:s"),		
			'create_user'	=> $_SESSION['userName'],	
			'status'	=> 1,	
			'is_delete'	=> 1,
		);



		
		if(empty($newdo['policy_id'])){
			$result['error'] = '销售策略未选择';
			Util::jsonExit($result);
		}
		if(empty($newdo['channel'])){
			$result['error'] = '渠道不能为空';
			Util::jsonExit($result);
		}
		if(empty($newdo['channel_level'])){
			$result['error'] = '等级不能为空';
			Util::jsonExit($result);
		}


		$newmodel =  new AppSalepolicyChannelModel(18);
	//	$res = $newmodel->saveData($newdo,$olddo);
        $res = $newmodel->saveAllC($newdo);
		if($res !== false)
		{
			$result['success'] = 1;
            $logmodel =  new AppSalepolicyChannelLogModel(18);
            $bespokeActionLog=array();
            $bespokeActionLog['policy_id']=$newdo['policy_id'];
            $bespokeActionLog['create_user']=$_SESSION['userName'];
            $bespokeActionLog['create_time']=date("Y-m-d H:i:s");
            $bespokeActionLog['IP']=Util::getClicentIp();
            $bespokeActionLog['status']=1;
            $bespokeActionLog['remark']=$bespokeActionLog['create_user']."保存状态";
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
		$id = _Post::getInt('id');
        $channel=_Request::getString('channel');

		$newmodel =  new AppSalepolicyChannelModel($id,18);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
            'id'=>$id,
            'channel'=>$channel
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
		$model = new AppSalepolicyChannelModel($id,2);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

	/**
	 *	bsi_statust，申请
	 */
	public function bsi_statust ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');

		$newmodel =  new AppSalepolicyChannelModel($id,17);

		$olddo = $newmodel->getDataObject();
		if($olddo['status']!=1){
			$result['error'] = '状态错误，不能申请！';
			Util::jsonExit($result);		
		}
		if($olddo['is_delete']==2){
			$result['error'] = '状态错误，不能申请！';
			Util::jsonExit($result);			
		}
		$newdo=array(
			'id'	=> $id,		
			'status'	=> 2,	
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
            $logmodel =  new AppSalepolicyChannelLogModel(18);
            $bespokeActionLog=array();
            $bespokeActionLog['policy_id']=$olddo['policy_id'];
            $bespokeActionLog['create_user']=$_SESSION['userName'];
            $bespokeActionLog['create_time']=date("Y-m-d H:i:s");
            $bespokeActionLog['IP']=Util::getClicentIp();
            $bespokeActionLog['status']=2;
            $bespokeActionLog['remark']=$bespokeActionLog['create_user']."申请审核";
            $logmodel->saveData($bespokeActionLog,array());
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	bsi_statusts，审核通过
	 */
	public function bsi_statusts ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');

		$newmodel =  new AppSalepolicyChannelModel($id,17);
		$olddo = $newmodel->getDataObject();
		if($olddo['status']!=2){
			$result['error'] = '状态错误，不能审核通过！';
			Util::jsonExit($result);		
		}		
		if($olddo['is_delete']==2){
			$result['error'] = '状态错误，不能申请！';
			Util::jsonExit($result);			
		}		
		$newdo=array(
			'id'	=> $id,		
			'status'	=> 3,	
			'check_time'	=> date("Y-m-d H:i:s"),	
			'check_user'	=> $_SESSION['userName'],	
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
            $logmodel =  new AppSalepolicyChannelLogModel(18);
            $bespokeActionLog=array();
            $bespokeActionLog['policy_id']=$olddo['policy_id'];
            $bespokeActionLog['create_user']=$_SESSION['userName'];
            $bespokeActionLog['create_time']=date("Y-m-d H:i:s");
            $bespokeActionLog['IP']=Util::getClicentIp();
            $bespokeActionLog['status']=3;
            $bespokeActionLog['remark']=$bespokeActionLog['create_user']."审核通过";
            $logmodel->saveData($bespokeActionLog,array());
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	not_bsi_statusts，未通过
	 */
	public function not_bsi_statusts ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');

		$newmodel =  new AppSalepolicyChannelModel($id,17);
		$olddo = $newmodel->getDataObject();
		if($olddo['status']!=2){
			$result['error'] = '状态错误，不能驳回！';
			Util::jsonExit($result);		
		}		
		if($olddo['is_delete']==2){
			$result['error'] = '状态错误，不能申请！';
			Util::jsonExit($result);			
		}
		$newdo=array(
			'id'	=> $id,		
			'status'	=> 4,
			'check_time'	=> date("Y-m-d H:i:s"),	
			'check_user'	=> $_SESSION['userName'],			
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
            $logmodel =  new AppSalepolicyChannelLogModel(18);
            $bespokeActionLog=array();
            $bespokeActionLog['policy_id']=$olddo['policy_id'];
            $bespokeActionLog['create_user']=$_SESSION['userName'];
            $bespokeActionLog['create_time']=date("Y-m-d H:i:s");
            $bespokeActionLog['IP']=Util::getClicentIp();
            $bespokeActionLog['status']=4;
            $bespokeActionLog['remark']=$bespokeActionLog['create_user']."审核未通过";
            $logmodel->saveData($bespokeActionLog,array());
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	bsi_statusted，取消
	 */
	public function bsi_statusted ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');

		$newmodel =  new AppSalepolicyChannelModel($id,17);

		$olddo = $newmodel->getDataObject();
		if($olddo['status']!=1){
			$result['error'] = '状态错误，不能取消！';
			Util::jsonExit($result);		
		}
		if($olddo['is_delete']==2){
			$result['error'] = '状态错误，不能申请！';
			Util::jsonExit($result);			
		}		
		$newdo=array(
			'id'	=> $id,		
			'status'	=> 5,	
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
            $logmodel =  new AppSalepolicyChannelLogModel(18);
            $bespokeActionLog=array();
            $bespokeActionLog['policy_id']=$olddo['policy_id'];
            $bespokeActionLog['create_user']=$_SESSION['userName'];
            $bespokeActionLog['create_time']=date("Y-m-d H:i:s");
            $bespokeActionLog['IP']=Util::getClicentIp();
            $bespokeActionLog['status']=5;
            $bespokeActionLog['remark']=$bespokeActionLog['create_user']."已取消状态";
            $logmodel->saveData($bespokeActionLog,array());
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	deletesed，删除
	 */
	public function deletesed ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');

		$newmodel =  new AppSalepolicyChannelModel($id,17);

		$olddo = $newmodel->getDataObject();	

		$newdo=array(
			'id'	=> $id,		
			'is_delete'	=> 2,	
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
            $logmodel =  new AppSalepolicyChannelLogModel(18);
            $bespokeActionLog=array();
            $bespokeActionLog['policy_id']=$olddo['policy_id'];
            $bespokeActionLog['create_user']=$_SESSION['userName'];
            $bespokeActionLog['create_time']=date("Y-m-d H:i:s");
            $bespokeActionLog['IP']=Util::getClicentIp();
            $bespokeActionLog['is_delete']=2;
            $bespokeActionLog['remark']=$bespokeActionLog['create_user']."已删除";
            $logmodel->saveData($bespokeActionLog,array());
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	//销售政策详情 查看详情
	public function showlist($params)
	{
		$id = intval($params["_id"]);
/*        $SalesChannelsmodel = new SalesChannelsModel(1);
        $SalesChannels=array();
        $SalesChannels=$SalesChannelsmodel->getSalesChannelsInfo('*',array());
        $channelList=array();
        foreach($SalesChannels as $k=>$v){
            $channelList[$v['id']]=$v['channel_name'];
        }*/
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'id'	=>$id
		);
		
		$g_model = new AppSalepolicyChannelModel(17);

		$where = array('policy_id'=>$id);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$data = $g_model->pageList($where,$page,10,false);

        $saleschannelsM =  new SalesChannelsModel(1);
        $saleschannelsdata =  $saleschannelsM->getSalesChannelsInfo('id,channel_name',array());



        foreach($saleschannelsdata as $k=>$v){
            $datalist[$v['id']]=$v['channel_name'];
        }
        unset($datalist[0]);

		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_salepolicy_channel_search_page';
		$this->render('app_salepolicy_channel_show_list_list.html',array(
			'pa'=>Util::page($pageData),
			'dd'=>new DictView(new DictModel(1)),
			'data' => $data,
			'channelList'=>$datalist
		));
	}




	/**
	 *	showsed，渲染查看页面
	 */
	public function showsed ($params)
	{
		$id = intval($params["id"]);
		$result['content'] = $this->fetch('app_salepolicy_channel_show_show.html',array(
			'view'=>new AppSalepolicyChannelView(new AppSalepolicyChannelModel($id,17)),
			'dd'=>new DictView(new DictModel(1))
		));
		$result['title'] = '渠道详情';
		Util::jsonExit($result);

	}

	public function getqdlist()
	{
		$shoptype = _Post::getInt('shop_type');
		$onelev =  _Post::getInt('channel_class');
		$twolev =  _Post::getInt('channel_type');
		$postdata = array(
			'shoptype'=>$shoptype,
			'onelev'=>$onelev,
			'twolev'=>$twolev
		);
		
		$data = array();
		//根据传过来的值去搜索所有的渠道
		//获取全部的满足条件的有效的销售渠道
		$SalesChannelsModel = new SalesChannelsModel(1);
		//$channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
		$channellist = $SalesChannelsModel->getallchannels($postdata);
		if(!empty($channellist['data']))
		{
			$data = $channellist['data'];
		}
		$num = count($data);
		$this->render('app_salepolicy_channel_info_options2.html', array('data' => $data,'num'=>$num));
	}


    public function getBaseSalepolicyOption(){
        $change_id = _Request::get('change_id');
        $g_model = new AppSalepolicyChannelModel(17);
        $data = $g_model->getSalepolicyByChannel($change_id);
        $num = count($data);
        $i = 0;
        foreach ($data as $vo){
            $i ++;
            $selected = $i==1 && $num==1 ?' selected':'';
            echo "<option value='{$vo['policy_id']}'{$selected}> {$vo['policy_name']}</option>";
        }



    }

}

?>