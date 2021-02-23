<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseHunbohuiInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-05-10 11:06:56
 *   @update	:
 *  -------------------------------------------------
 */
class BaseHunbohuiInfoController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$SalesChannelsModel = new SalesChannelsModel(1);
        $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
		$this->render('base_hunbohui_info_search_form.html',array('bar'=>Auth::getBar(),'channellist' => $channellist));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$result = array('success' => 0, 'error' => '');
		//获取全部的有效的销售渠道
		 $allSalesChannelsData = array();
        $SalesChannelsModel = new SalesChannelsModel(1);
        $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
		foreach ($getSalesChannelsInfo as $val) {
            $allSalesChannelsData[$val['id']] = $val['channel_name'];
			
        }
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			//'参数' = _Request::get("参数");
			'name' =>_Request::getString('name'),
			'department' =>_Request::getInt('department'),
			'start_time' =>_Request::getString('start_time'),
			'end_time' =>_Request::getString('end_time'),
			'active_start_time' =>_Request::getString('active_start_time'),
			'active_end_time' =>_Request::getString('active_end_time')

		);
		$page = _Request::getInt("page",1);
		$where = array(
			'name' => $args['name'],
            'department' => $args['department'],
            'start_time' => $args['start_time'],
            'end_time' => $args['end_time'],
            'active_start_time' => $args['active_start_time'],
            'active_end_time' => $args['active_end_time']
		);
		$model = new BaseHunbohuiInfoModel(1);
		$data = $model->pageList($where,$page,10,false);
        //获取所有数据
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'base_hunbohui_info_search_page';
		$this->render('base_hunbohui_info_search_list.html',array(
			'pa'=>Util::page($pageData),
            'allSalesChannelsData' => $allSalesChannelsData,
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
      
        $res = $this->ChannelListO();
        $CustomerSourcesData=$this->getCustomerSourcesList();
        if ($res === true) {
            //获取全部的有效的销售渠道
            $SalesChannelsModel = new SalesChannelsModel(1);
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
        } else {
            $channellist = $this->getchannelinfo($res);
        }
//        var_dump($channellist);die;
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('base_hunbohui_info_info.html',array(
			'view'=>new BaseHunbohuiInfoView(new BaseHunbohuiInfoModel(1)), 'channellist' => $channellist, 
            'CustomerSourcesData' => $CustomerSourcesData
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
        $res = $this->ChannelListO();
        $CustomerSourcesData=$this->getCustomerSourcesList();
        if ($res === true) {
            //获取全部的有效的销售渠道
            $SalesChannelsModel = new SalesChannelsModel(1);
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
        } else {
            $channellist = $this->getchannelinfo($res);
        }
		$id = intval($params["id"]);
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('base_hunbohui_info_info.html',array(
			'view'=>new BaseHunbohuiInfoView(new BaseHunbohuiInfoModel($id,1))
            , 'channellist' => $channellist
            , 'CustomerSourcesData' => $CustomerSourcesData
			,'tab_id'=>$tab_id
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
		$this->render('base_hunbohui_info_show.html',array(
			'view'=>new BaseHunbohuiInfoView(new BaseHunbohuiInfoModel($id,1)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
        $department = _Request::getInt('department');
        $start_time = _Request::getString('start_time');
        $end_time = _Request::getString('end_time');
        $active_start_time = _Request::getString('active_start_time');
        $active_end_time = _Request::getString('active_end_time');
        $from_ad = _Request::getString('from_ad');
        $name = _Request::getString('from_adHtml');
		$olddo = array();
        if(empty($department)){
            $result['error'] = '请选择录单部门';
            Util::jsonExit($result);
        }
        if(empty($start_time)){
            $result['error'] = '允许录单开始时间不能为空';
            Util::jsonExit($result);
        }
        if(empty($end_time)){
            $result['error'] = '允许录单结束时间不能为空';
            Util::jsonExit($result);
        }
        if(empty($start_time)){
            $result['error'] = '活动举行开始时间不能为空';
            Util::jsonExit($result);
        }
        if(empty($end_time)){
            $result['error'] = '活动举行结束时间不能为空';
            Util::jsonExit($result);
        }
        if(empty($from_ad)){
            $result['error'] = '活动名称不能为空';
            Util::jsonExit($result);
        }
        
		$newdo=array(
            'name' => $name,
            'department' => $department,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'active_start_time' => $active_start_time,
            'active_end_time' => $active_end_time,
            'user_name' => $_SESSION['userName'],
            'from_ad' => $from_ad,
            'warehouse' => 'SZ',
            'manager' => $_SESSION['userName'],
            'is_delete' => 0,
        );


		$newmodel =  new BaseHunbohuiInfoModel(2);
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
		$name = _Request::getString('from_adHtml');
        $department = _Request::getInt('department');
        $start_time = _Request::getString('start_time');
        $end_time = _Request::getString('end_time');
        $active_start_time = _Request::getString('active_start_time');
        $active_end_time = _Request::getString('active_end_time');
        $from_ad = _Request::getString('from_ad');
		$olddo = array();
        if(empty($from_ad)){
            $result['error'] = '活动名称不能为空';
            Util::jsonExit($result);
        }
        if(empty($department)){
            $result['error'] = '请选择录单部门';
            Util::jsonExit($result);
        }
        if(empty($start_time)){
            $result['error'] = '允许录单开始时间不能为空';
            Util::jsonExit($result);
        }
        if(empty($end_time)){
            $result['error'] = '允许录单结束时间不能为空';
            Util::jsonExit($result);
        }
        if(empty($active_start_time)){
            $result['error'] = '活动举行开始时间不能为空';
            Util::jsonExit($result);
        }
        if(empty($active_end_time)){
            $result['error'] = '活动举行结束时间不能为空';
            Util::jsonExit($result);
        }
        
		$newmodel =  new BaseHunbohuiInfoModel($id,2);
		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id' =>$id,
            'name' => $name,
            'department' => $department,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'active_start_time' => $active_start_time,
            'active_end_time' => $active_end_time,
            'user_name' => $_SESSION['userName'],
            'from_ad' => $from_ad,
            'warehouse' => 'SZ',
            'manager' => $_SESSION['userName'],
        );
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;	
			$result['title'] = '修改此处为想显示在页签上的字段';
			
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
		$model = new BaseHunbohuiInfoModel($id,2);
		$do = $model->getDataObject();
		$valid = $do['is_delete'];
		if($valid==1)
		{
			$result['error'] = "当前记录已经删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_delete',1);
		$res = $model->save(true);
		//联合删除？
		//$res = $model->delete();
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

	/**
	 *	getCustomerSourcesList，来源
	 */
	public function getCustomerSourcesList ()
	{
        //来源 
		$CustomerSourcesModel = new CustomerSourcesModel(1);
        $CustomerSourcesInfo = $CustomerSourcesModel->getCustomerSourcesList("`source_name`,`source_code`");
        $CustomerSourcesData=array();
        //获取所有数据
        if($CustomerSourcesInfo){
            foreach ($CustomerSourcesInfo as $key=>$val){
                $CustomerSourcesData[$key]['source_code'] = $val['source_code'];
                $CustomerSourcesData[$key]['source_name'] = $val['source_name'];
            }
        }
        return $CustomerSourcesData;
	}
}

?>