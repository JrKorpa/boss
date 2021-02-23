<?php
/**
 *  -------------------------------------------------
 *   @file		: TsydDiscountConfigController.php
 *   @link		:  tsyd.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: liangrongjun
 *   @date		: 2016-03-11 15:37:36
 *   @update	:
 *  -------------------------------------------------
 */
class TsydDiscountConfigController extends CommonController
{

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$TsydDiscountConfig = new TsydDiscountConfigView(new TsydDiscountConfigModel(99));
		$ChannelsModel = new SalesChannelsModel(1);
		$channelinfos = $ChannelsModel->getAllChannelInfo();
		$discounttype = $TsydDiscountConfig->get_discount_type();
		$this->render('tsyd_discount_config_index.html',array('bar'=>Auth::getBar(),'discounttype'=>$discounttype,'channelinfos'=>$channelinfos));
	}

	/*
	* 搜索
	*
	*/
	public function search(){

		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
            'channel_id' => _Request::getString("channel_id"),
            'type' => _Request::getInt("type"),
            'enabled' => _Request::getString("enabled"),
		);

		$page = _Request::getInt("page",1);
		$where = array();
        $ChannelsModel = new SalesChannelsModel(1);
        $where = array(
                'channel_id' => $args['channel_id'],
                'type' => $args['type'],
                'enabled' => $args['enabled']
            );
        $discount_model =new TsydDiscountConfigModel(99);
		$data = $discount_model->pageList($where,$page,10,false);
        $discounttypes  = $discount_model->getDiscountType();

        //获取渠道名称
        foreach ($data['data'] as $key=>&$val){
            $channel_id = $val['channel_id'];
            $val['type'] =$discounttypes[$val['type']];
            $val['channel_name']= $ChannelsModel->getChannelNameByChannelId($channel_id);
        }
        
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'tsyd_discount_config_search_page';
		$this->render('tsyd_discount_config_search_list.html',array(
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
		$ChannelsModel = new SalesChannelsModel(1);
		//所有渠道
		$channelinfos = $ChannelsModel->getAllChannelInfo();
		$result['content'] = $this->fetch('tsyd_discount_config_info.html',array(
			'view'=>new TsydDiscountConfigView(new TsydDiscountConfigModel(99)),
			'channelinfos'=>$channelinfos
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
        $newmodel = new TsydDiscountConfigModel(99);
        $discount_data = $newmodel->getDiscountById($id);  // 折扣信息
        //所有渠道
        $ChannelsModel = new SalesChannelsModel(1);
        $channelinfos = $ChannelsModel->getAllChannelInfo();
		$discounts = $newmodel->getDiscountByChannelId($discount_data['channel_id']);   //获取该渠道的所有信息

		foreach($discounts as $key=>$val){
			$new_discount_dat[$val['type']] = $val['zhekou'];

		}
		$new_discount_dat['channel_id'] = $discount_data['channel_id'];  //渠道ID
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('tsyd_discount_config_info.html',array(
			'view'=>new TsydDiscountConfigView(new TsydDiscountConfigModel($id,99)),
			'tab_id'=>$tab_id,
			'channelinfos'=>$channelinfos,
            'data'=>$new_discount_dat
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
		$this->render('base_lz_discount_config_show.html',array(
			'view'=>new BaseLzDiscountConfigView(new BaseLzDiscountConfigModel($id,99)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$channel_id = _Request::getString('channel_id');
        $zhekou_1 = _Request::getFloat('zhekou_1');
        $zhekou_2 = _Request::getFloat('zhekou_2');
        $zhekou_3 = _Request::getFloat('zhekou_3');
        $zhekou_4 = _Request::getFloat('zhekou_4');
        $zhekou_5 = _Request::getFloat('zhekou_5');
		$olddo = array();
        
        $newmodel =  new TsydDiscountConfigModel(99);
        for($i=1;$i<6;$i++){
            $tmp_str = "zhekou_".$i;
            $nn = $$tmp_str;
            if($nn<0.01 || $nn >1){
                $result['error'] = '折扣范围在0.01到1之间';
                Util::jsonExit($result);
            }
        }

        $discounttypes = $newmodel->getDiscountType();

         //查询该渠道折扣是否已经设置
        for($i=1;$i<6;$i++){
            $tmp_str = "zhekou_".$i;
            $nn = $$tmp_str;
            $count = $newmodel->getDiscountExists($channel_id,$i);
            if($count >0){
                $result['error'] = $discounttypes[$i]."折扣已经存在！";
                Util::jsonExit($result);
            }else{
                $newdo = array('channel_id'=>$channel_id,'type'=>$i,'zhekou'=>$nn);
                $res = $newmodel->saveData($newdo,$olddo);
            }
        }
		
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
        $channel_id = _Request::getString('channel_id');
        $zhekou_1 = _Request::getFloat('zhekou_1');
        $zhekou_2 = _Request::getFloat('zhekou_2');
        $zhekou_3 = _Request::getFloat('zhekou_3');
        $zhekou_4 = _Request::getFloat('zhekou_4');
        $zhekou_5 = _Request::getFloat('zhekou_5');

		$id = _Post::getInt('id');

         for($i=1;$i<6;$i++){
            $tmp_str = "zhekou_".$i;
            $nn = $$tmp_str;
            if($nn<0.01 || $nn >1){
                $result['error'] = '折扣范围在0.01到1之间';
                Util::jsonExit($result);
            }
        }

         for($i=1;$i<6;$i++){
            $tmp_str = "zhekou_".$i;
            $nn = $$tmp_str;

            $newmodel = new TsydDiscountConfigModel(99);
            $discount_data = $newmodel->getDiscountResult($channel_id,$i);
            if($discount_data){
                $id = $discount_data['id'];
                $newmodel = new TsydDiscountConfigModel($id,99);
                $olddo = $newmodel->getDataObject();
                $newdo = array('id'=>$id,'channel_id'=>$channel_id,'type'=>$i,'zhekou'=>$nn);
                $res = $newmodel->saveData($newdo,$olddo);
            }else{
                $newdo = array('channel_id'=>$channel_id,'type'=>$i,'zhekou'=>$nn);
                $res = $newmodel->saveData($newdo,$olddo);
            }
        }

		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;	
			$result['title'] = '操作成功';
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	delete，禁用
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new TsydDiscountConfigModel($id,99);
		$do = $model->getDataObject();
		$enabled = $do['enabled'];
        if($enabled==0){
            $result['error'] = "此状态已经禁用";
            Util::jsonExit($result);
        }
		
		$model->setValue('enabled',0);
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
	 *	recover，启用
	 */
	public function recover ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new TsydDiscountConfigModel($id,99);
		$do = $model->getDataObject();
		$enabled = $do['enabled'];
        if($enabled==1){
            $result['error'] = "此状态已经启用";
            Util::jsonExit($result);
        }
		
		$model->setValue('enabled',1);
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


    
}

?>