<?php
/**
 *  -------------------------------------------------
 *   @file		: SalesChannelsController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 15:25:16
 *   @update	:
 *  -------------------------------------------------
 */
class SalesChannelsController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		/* 
	    $bumen = !empty($_SESSION['bumen'])?explode(',',$_SESSION['bumen']):array();
		if(Auth::$userType>2 && !in_array(42,$bumen))
		{
			die('操作禁止,只有超级管理员和经销商管理部的人员才可以操作');
		}*/
		$this->render('sales_channels_search_form.html',array('bar'=>Auth::getBar()));
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
			'channel_name'=>_Request::get("channel_name"),
			'channel_code'=>_Request::get("channel_code")


		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['channel_name'] = $args['channel_name'];
		$where['channel_code'] = $args['channel_code'];
		$where['is_deleted'] = 0;

		$model = new SalesChannelsModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'sales_channels_search_page';
		$this->render('sales_channels_search_list.html',array(
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
		$model = new CompanyModel(1);
		$companys = $model->getCompanyTree();
		$JxcWholesaleArr=$model->getJxcWholesale();
		$result['content'] = $this->fetch('sales_channels_info.html',array(
			'view'=>new SalesChannelsView(new SalesChannelsModel(1)),
			'companys'=>$companys,
			'JxcWholesaleArr'=>$JxcWholesaleArr,	
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
		$model = new CompanyModel(1);
		$companys = $model->getCompanyTree();

		$company_name = array();
		foreach($companys as $k=>$v){
			$company_name[] = $v['company_name'];
		}

		$JxcWholesaleArr=$model->getJxcWholesale();
		$result['content'] = $this->fetch('sales_channels_info.html',array(
			'view'=>new SalesChannelsView(new SalesChannelsModel($id,1)),
			'companys'=>$companys,
			'company_name'=>$company_name,
			'JxcWholesaleArr'=>$JxcWholesaleArr,
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
		$this->render('sales_channels_show.html',array(
			'view'=>new SalesChannelsView(new SalesChannelsModel($id,1)),
			'bar'=>Auth::getViewBar()
		));
	}

	/*
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		//rules验证
		$vd = new Validator();
		$vd->set_rules('channel_name', '渠道名称',  'require');
		$vd->set_rules('channel_code', '渠道编码',  'require');
		$vd->set_rules('channel_class', '一级分类',  'require|isNumber');
		$vd->set_rules('channel_type', '二级分类',  'require|isNumber');
		$vd->set_rules('channel_own_id', '渠道归属',  'require|isNumber');
       
		if (!$vd->is_valid($_POST))
		{
			$result['error'] = $vd->get_errors();
			Util::jsonExit($result);
		}
		//接收数据
		foreach ($_POST as $k => $v) {
			$$k = _Post::get($k);
		}
		$newmodel =  new SalesChannelsModel(2);
		$olddo = array();
		$newdo = $newmodel->mkNewdo();
		$is_tsyd = _Post::getInt('is_tsyd');
		$wholesale_id = _Post::getInt('wholesale_id');
		$newdo['is_tsyd']=$is_tsyd;
		if($is_tsyd==0){
			$newdo['wholesale_id']=='';
		}else{
			if($wholesale_id==0){
				$result['error']="请选择批发客户";
				Util::jsonExit($result);
			}
			$wholesaleArr=$newmodel->getSalesChannelsInfo('wholesale_id',1);
			$wholesale_arr=array_unique(array_column($wholesaleArr, 'wholesale_id'));
			if(in_array($wholesale_id, $wholesale_arr)){
				$result['error']="所选批发客户已经被用";
				Util::jsonExit($result);
			}
			
			$newdo['wholesale_id']=$wholesale_id;
			
		}
		//获取枚举值
		$dict = new DictModel(1);

		$type = $dict->getEnumArray("sales_channels_type");
		$tmp=array();
		foreach($type as $key=>$val){
			$tmp[]=$val['name'];
		}

		if(!in_array($newdo['channel_type'],$tmp)){
			$result['error'] = '二级分类错误！';
		}else{
			switch ($newdo['channel_type']) {
				case $tmp[0]:
					$table = 'department';
					$name = 'name';
					break;
				case $tmp[1]:
					$table = 'shop_cfg';
					$name = 'shop_name';
					break;
				case $tmp[2]:
					$table = 'company';
					$name = 'company_name';
					break;
				default:
					$result['error'] = '二级分类错误！';
					break;
			}

			if($newmodel->hasName($newdo['channel_name']))
			{
				$result['error'] = '渠道名称已存在！';
				Util::jsonExit($result);
			}
			$sql = 'SELECT '.$name.' FROM '.$table.' WHERE id = '.$newdo['channel_own_id'];
			$channel_own = $newmodel->db()->getOne($sql);

			$newdo['channel_own'] = $channel_own;

			//判断如果为体验店渠道所属是否已经存在
			if($newdo['channel_type'] ==2){
				if($newmodel->getExpStore($newdo['channel_own_id']) !=0){
					$result['error'] = '渠道所属体验店名称已存在！';
					Util::jsonExit($result);
				}
			}
			
			/*begin---------------上传图片------------------begin*/
			if(isset($_FILES['qrcode'])){
				$uploadObj = new Upload();
				$res = $uploadObj->toUP($_FILES['qrcode']);
				if(!is_array($res)){
					$result['error'] = '图片上传失败';
					Util::jsonExit($result);
				}else{
					$newdo['qrcode'] = $res['url'];
				}
			}
			if (empty($newdo['qrcode'])){
				$newdo['qrcode'] = 'public/img/QR.jpg';
			}
			/*end----------------上传图片------------------end*/
			
			$res = $newmodel->saveData($newdo,$olddo);
			if($res !== false)
			{
				$result['success'] = 1;
				//如果添加成功,那么检查该体验店是否存在sku的商品信息
				//调用sku商品自动绑定销售渠道
				$this->autobindshop($res);
			}
			else
			{
				$result['error'] = '添加失败';
			}
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
		
		$old_channel_own_id=_Post::getString('channel_own_id');
		//rules验证
		$vd = new Validator();
		$vd->set_rules('channel_name', '渠道名称',  'require');
		$vd->set_rules('channel_code', '渠道编码',  'require');
		$vd->set_rules('channel_class', '一级分类',  'require|isNumber');
		$vd->set_rules('channel_type', '二级分类',  'require|isNumber');
		$vd->set_rules('channel_own_id', '渠道归属',  'require|isNumber');
		$vd->set_rules('company_id', '所属公司',  'require');
		if (!$vd->is_valid($_POST))
		{
			$result['error'] = $vd->get_errors();
			Util::jsonExit($result);
		}
		//接收数据
		$newmodel =  new SalesChannelsModel($id,2);	
		$newdo = $newmodel->mkNewdo();
		$wholesale_id1=$newmodel->getValue('wholesale_id');
		$newdo['id'] = $id;
		$is_tsyd = _Post::getInt('is_tsyd');
		$newdo['is_tsyd']=$is_tsyd;
		$wholesale_id = _Post::getInt('wholesale_id');
		if($is_tsyd==0){
			$newdo['wholesale_id']=='';
		}else{
			if($wholesale_id==0){
				$result['error']="请选择批发客户";
				Util::jsonExit($result);
			}
			
			$wholesaleArr=$newmodel->getSalesChannelsInfo('wholesale_id',1);
			$wholesale_arr=array_unique(array_column($wholesaleArr, 'wholesale_id'));
			
			foreach ($wholesale_arr as $k=>$v){	
				
				if($v==$wholesale_id1){					
					unset($wholesale_arr[$k]);	
				}
				
			}

			if(in_array($wholesale_id, $wholesale_arr)){
				$result['error']="所选批发客户已经被用";
				Util::jsonExit($result);
			}
			
			$newdo['wholesale_id']=$wholesale_id;
		}
		//获取枚举值
		$dict = new DictModel(1);

		$type = $dict->getEnumArray("sales_channels_type");

		$tmp=array();
		foreach($type as $key=>$val){
			$tmp[]=$val['name'];
		}

		if(!in_array($newdo['channel_type'],$tmp)){
			$result['error'] = '二级分类错误！';
		}else{
			switch ($newdo['channel_type']) {
				case $tmp[0]:
					$table = 'department';
					$name = 'name';
					break;
				case $tmp[1]:
					$table = 'shop_cfg';
					$name = 'shop_name';
					break;
				case $tmp[2]:
					$table = 'company';
					$name = 'company_name';
					break;
				default:
					$result['error'] = '二级分类错误！';
					Util::jsonExit($result);
			}

			$sql = 'SELECT '.$name.' FROM `'.$table.'` WHERE id = '.$newdo['channel_own_id'];

			$channel_own = $newmodel->db()->getOne($sql);

			$newdo['channel_own'] = $channel_own;

			if($newmodel->hasName($newdo['channel_name']))
			{
				$result['error'] = '渠道名称已存在！';
				Util::jsonExit($result);
			}

			//判断如果为体验店渠道所属是否已经存在
			if($newdo['channel_type'] ==2){
				if($newmodel->getExpStore($newdo['channel_own_id']) !=0 && ($newmodel->getChannelOwnIdById($id) != $old_channel_own_id)){
					$result['error'] = '该体验店已与其他销售渠道绑定！';
					Util::jsonExit($result);
				}
			}
			
			/*begin---------------上传图片------------------begin*/
			if(isset($_FILES['qrcode'])){
				$uploadObj = new Upload();
				$res = $uploadObj->toUP($_FILES['qrcode']);
				if(!is_array($res)){
					$result['error'] = '图片上传失败';
					Util::jsonExit($result);
				}else{
					$newdo['qrcode'] = $res['url'];
				}
			}
			/*end----------------上传图片------------------end*/

			
			$olddo = $newmodel->getDataObject();
			if (empty($newdo['qrcode'])){
				$newdo['qrcode'] = $olddo['qrcode'];
			}
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
				$result['error'] = '添加失败';
			}
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
		$model = new SalesChannelsModel($id,2);
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
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
	 * toChannelCode
	 */
	public function toChannelCode(){
		$c_name = _Post::getString('channel_name');
		$str = (string)Pinyin::getQianpin($c_name);
		echo strtoupper($str);
	}

	/**
	 * appendHtml
	 */
	public function appendHtml(){
		$type = _Post::get('type');
		$model = new SalesChannelsModel(1);
		$data = $model->getChannelOwns($type);
		
		$this->render('sales_channels_info_options.html',array('data'=>$data));
	}
	
	
	//官网这边在添加了销售渠道之后需要默认的为这个渠道绑定上sku商品
	public function autobindshop($channelid)
	{
		//因为只是在新加店的时候没有,所有直接就是添加即可
		$model = new SalesChannelsModel(1);
		$model->copyskugoods($channelid);
	}

}

