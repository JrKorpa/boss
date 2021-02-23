<?php
/**
 *  -------------------------------------------------
 *   @file		: SalesChannelsPersonController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-05-27 16:57:44
 *   @update	:
 *  -------------------------------------------------
 */
class SalesChannelsPersonController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$channellist = $this->accessChannels();
		$def_id = isset(current($channellist)['id']) ? current($channellist)['id'] : 0;
		$this->render('sales_channels_person_search_form.html',array('bar'=>Auth::getBar(),'sales_channels_idData' => $channellist,'def_id'=>$def_id));
	}
	// 抽离渠道访问权限
	private function accessChannels() {
		if($_SESSION['userType'] == 1){
			// 超级用户查看所有线下体验店
			$salesChannelsModel = new SalesChannelsModel(1);
			$channellist = $salesChannelsModel->getSalesChannelsInfo('id,channel_name',array('channel_class'=>2,'channel_type'=>2));
            $channellist[] = array('id'=>163, 'channel_name'=>'总公司网销');//BOSS-1212，暂时写死
		}else{
			//当前用户所处渠道
			$saleChannelmodel = new UserChannelModel(1);
			$list = $saleChannelmodel->getChannels($_SESSION['userId'],0);
			// 所有销售渠道
			$model = new SalesChannelsPersonModel(1);
			$ids = $model->getAllInfo();
			$ids = array_column($ids,'id','id');
			// 组装渠道
			$channellist = array();
			foreach ($list as $item) {
				if (isset($ids[$item['id']])) {
					$channellist[] = $item;
				}
			}
		}
		return $channellist;
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$model = new SalesChannelsPersonModel(1);
        $ids = explode(',', $_SESSION['qudao']);
        $order_department = current($ids);
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			//'参数' = _Request::get("参数");
            'order_department'=> _Request::getInt('order_department')?_Request::getInt('order_department'):$order_department,
			'dp_is_netsale'=>_Request::getInt('is_netsales')?_Request::getInt('is_netsales'):'',

		);
		$page = _Request::getInt("page",1);
		$where = array(
            'order_department'=>$args['order_department']
        );
		
		//是否网销
		$netvalue = $args['dp_is_netsale'];
		
        $dp_people_name = array();
		$net_people_name = array();
        $array = array();
		//增加是否网销
		$isnetsale = array();
		$data = $model->getInfoList($where);
		
        if(count($data)){
            //取得当前用户id,userName
            $userId = $_SESSION['userId'];
            $userName = $_SESSION['userName'];
            //判断当前用户是否为店长
            $dp_leader_name = explode(',', $data['dp_leader_name']);
			
            $dp_people_name = explode(',', $data['dp_people_name']);
            $dp_people_name = array_merge($dp_leader_name,$dp_people_name);
			
			//判断是否网销
			$net_people_name = explode(',',$data['dp_is_netsale']);
			
            foreach ($dp_leader_name as $val){
				$array[$val] = $val;
				//$isnetsale[$val] = 
            }
//            if($_SESSION['userType']==1){
//                $dp_people_name = explode(',', $data['dp_people_name']);
//            }elseif(!empty($data['dp_leader'])){
//                $dp_leader_name = explode(',', $data['dp_leader_name']);
//                if(in_array($userName, $dp_leader_name)){
//                    $dp_people_name = explode(',', $data['dp_people_name']);
//                }else{
//                    $tmp = explode(',', $data['dp_people_name']);
//                    if(in_array($userName, $tmp)){
//                        $dp_people_name = array($userId=>$userName);
//                    }
//                }
//            }
//            $dp_people = explode(',', $data['dp_people']);
        }
        $salesChannelModel = new SalesChannelsModel($args['order_department'],1);
        $do = $salesChannelModel->getDataObject();
        $channel_name = $salesChannelModel->getValue('channel_name');
        if(count($dp_people_name)){
            foreach ($dp_people_name as $key=>$val){
                if(empty($val)){
                    unset($dp_people_name[$key]);
                }
            }
        }
		//网销数据组装
		$netarr = array();
		if(count($net_people_name))
		{
			foreach($net_people_name as $key=>$val)
			{
				if(empty($val))
				{
					unset($net_people_name[$key]);
				}
				$netarr[$val] = $val;
			}
		}
		
		$netarrv = array_values($netarr);
		
		//过滤到搜索的网销
		foreach($dp_people_name as $k=>$v)
		{
			//如果是非网销
			if($netvalue == 1 )
			{
				if(in_array($v,$netarrv))
				{
					unset($dp_people_name[$k]);
				}
			}
			if( $netvalue == 2)
			{
				if(!in_array($v,$netarrv))
				{
					unset($dp_people_name[$k]);
				}
			}	
		}
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'sales_channels_person_search_page';
		$this->render('sales_channels_person_search_list.html',array(
			'page_list'=>$dp_people_name,
			'channel_name'=>$channel_name,
			'arr_data'=>$array,
			'net_data'=>$netarr,
            'id'=>$args['order_department']
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$channellist = $this->accessChannels();
		$def_id = isset(current($channellist)['id']) ? current($channellist)['id'] : 0;

        $model = new UserChannelModel(1);
        $make_order = $model->get_user_channel_by_channel_id($def_id);
		$result['content'] = $this->fetch('sales_channels_person_info.html',array(
			'view'=>new SalesChannelsPersonView(new SalesChannelsPersonModel(1)),
			'sales_channels_idData' => $channellist,'def_id'=>$def_id,'user_info'=>$make_order
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}
    
    
    public function getUserInfo($param) {
        $id = intval($param['id']);
        $model = new UserChannelModel(1);
        $make_order = $model->get_user_channel_by_channel_id($id);
        $str = '';
        if(count($make_order)){
            $str = '<option value=""></option>';
            foreach ($make_order as $val){
                $str .= '<option value="'.$val['user_id'].'">'.$val['account'].'</option>';
            }
        }
        echo $str;
    }
    

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('sales_channels_person_info.html',array(
			'view'=>new SalesChannelsPersonView(new SalesChannelsPersonModel($id,1)),
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
		$this->render('sales_channels_person_show.html',array(
			'view'=>new SalesChannelsPersonView(new SalesChannelsPersonModel($id,1)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 *  2015-11-30 19:00:00 modify by gengchao，这个功能自带的saveData实现不了添加只能实现保存，所以我写了添加的方法
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = intval($params['order_department']);
		$user_name = $params['user_name'];
		$is_leader = intval($params['is_leader']);
		$is_netsale = intval($params['is_netsale']);
        if($id<1){
            $result['error'] = '请选择销售渠道！';
            Util::jsonExit($result);
        }
        if($is_leader<1){
            $result['error'] = '请选择是否为店面管理权限！';
            Util::jsonExit($result);
        }
        if(empty($user_name)){
            $result['error'] = '请添写销售顾问账号！';
            Util::jsonExit($result);
        }
        
        $model = new UserChannelModel(1);
        $make_order = $model->get_user_channel_by_channel_id($id);
        $make_order = array_column($make_order,'account','user_id');
        //$userModel = new UserModel(1);
        //$userId = $userModel->getAccountId($user_name);
        if(!array_key_exists($user_name, $make_order)){
            $result['error'] = '当前销售渠道没有该销售顾问账号！';
            Util::jsonExit($result);
        }
		$newmodel =  new SalesChannelsPersonModel($id,2);
        $olddo = $newmodel->getDataObject();
		$insert = false;
		if (empty($olddo)) {
			$olddo = array('dp_leader_name'=>'', 'dp_people_name'=>'', 'dp_is_netsale'=>'');
			$insert = true;
		} else {
			if(strrpos($olddo['dp_leader_name'], $make_order[$user_name])){
				$result['error'] = '预约销售管理已有该销售顾问账号！';
				Util::jsonExit($result);
			}
			if(strrpos($olddo['dp_people_name'], $make_order[$user_name])){
				$result['error'] = '预约销售管理已有该销售顾问账号！';
				Util::jsonExit($result);
			}
		}
		$newdo = array('id'=>$id, 'dp_leader_name'=>$olddo['dp_leader_name'], 'dp_people_name'=>$olddo['dp_people_name']);
		// 是否网销
		$dp_is_netsale = $olddo['dp_is_netsale'];
		if($is_netsale > 1) {
			$dp_is_netsale = ltrim($dp_is_netsale.','.$make_order[$user_name], ',');
		}
		$newdo['dp_is_netsale'] = $dp_is_netsale;
		// 是否店长
		if($is_leader==2){
			$newdo['dp_leader_name'] = ltrim($olddo['dp_leader_name'].','.$make_order[$user_name], ',');
		}else{
			$newdo['dp_people_name'] = ltrim($olddo['dp_people_name'].','.$make_order[$user_name], ',');
		}
		if ($insert) {
			$res = $newmodel->addChannelPerson($newdo);
		} else {
			$res = $newmodel->saveData($newdo,$olddo);
		}
		if($res !== false)
		{
			$result['success'] = 1;
		} else {
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

		$newmodel =  new SalesChannelsPersonModel($id,2);

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
		$id = $params['id'];
        $arr = explode('|', $id);
        $model = new SalesChannelsPersonModel($arr[0],2);
		$do = $model->getDataObject();
        $dp_people = explode(',', $do['dp_people']);
        $dp_people_name = explode(',', $do['dp_people_name']);
		$dp_is_netsale = explode(',', $do['dp_is_netsale']);
		
        foreach ($dp_people_name as $key => $value) {
            if(empty($value) || $arr[1]==$value){
                unset($dp_people[$key]);
                unset($dp_people_name[$key]);
            }
        }
		//删除网销
		foreach($dp_is_netsale as $key=>$v)
		{
			if(empty($value) || $arr[1] == $v)
			{
				unset($dp_is_netsale[$key]);
			}
		}
			
		
        $str_dp_people = implode(',', $dp_people);
        $str_dp_people_name = implode(',', $dp_people_name);
		$str_dp_isnet_name = implode(',',$dp_is_netsale);
		$newdo = array(
            'id'=>$arr[0],
            'dp_people'=>  $str_dp_people,
            'dp_people_name'=>  $str_dp_people_name,
			'dp_is_netsale'=>$str_dp_isnet_name
        );
        
        //取店长
        if(!empty($do['dp_leader_name'])){
            $dp_leader = explode(',', $do['dp_leader']);
            $dp_leader_name = explode(',', $do['dp_leader_name']);
            if(in_array($arr[1], $dp_leader_name)){
                foreach ($dp_leader_name as $key => $value) {
                    if(empty($value) || $arr[1]==$value){
                        unset($dp_leader[$key]);
                        unset($dp_leader_name[$key]);
                    }
                }
                $str_dp_leader = implode(',', $dp_leader);
                $str_dp_leader_name = implode(',', $dp_leader_name);
                $newdo['dp_leader'] = $str_dp_leader;
                $newdo['dp_leader_name'] = $str_dp_leader_name;
            }
        }
        
		$res = $model->saveData($newdo,$do);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
}

?>