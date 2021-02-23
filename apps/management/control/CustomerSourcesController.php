<?php
/**
 *  -------------------------------------------------
 *   @file		: CustomerSourcesController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-05 11:29:16
 *   @update	:
 *  -------------------------------------------------
 */
class CustomerSourcesController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $arr=array(0=>"全部",-1=>"其他",1=>"异业联盟",2=>"社区",3=>"BDD相关",4=>"团购",5=>"老顾客",6=>"数据",7=>"网络来源");
    

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        
	
    	$this->render('customer_sources_search_form.html',array('bar'=>Auth::getBar(),'arr'=>$this->arr));
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
			//'参数' = _Request::get("参数");
			'source_name'=>_Request::get("source_name"),
			'source_code'=>_Request::get("source_code"),
            'fenlei'=>_Request::get("fenlei"),
            'is_enabled'=>_Request::get("is_enabled"),
            'source_class'=>_Request::get("source_class")
		);
		$page = _Request::getInt("page",1);
		$where = array();
		$where['source_name'] = $args['source_name'];
		$where['source_code'] = $args['source_code'];
        $where['fenlei']=$args['fenlei'];
        $where['is_enabled']=$args['is_enabled'];
        if(!empty($args['source_class'])) $where['source_class']=$args['source_class'];

		$model = new CustomerSourcesModel(1);
		$data = $model->pageList($where,$page,50,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'customer_sources_search_page';
		$this->render('customer_sources_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
            'arr'=>$this->arr
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
        $act=__FUNCTION__;
		$result = array('success' => 0,'error' => '');
                //获取所有渠道
                $cmodel = new SalesChannelsModel(1);
                $salesChannel = $cmodel->getSalesChannelsInfo('id,channel_name', 1);
                $salesChannel = array_column($salesChannel, 'channel_name', 'id');
                $this->render('customer_sources_info.html',array(
			'view'=>new CustomerSourcesView(new CustomerSourcesModel(1)),
                        'salesChannel'=>$salesChannel,
                        'arr'=>$this->arr,
                        'act'=>$act
                        
		));

	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{      
        $act=__FUNCTION__;
        //获取所有渠道
                $cmodel = new SalesChannelsModel(1);
                $salesChannel = $cmodel->getSalesChannelsInfo('id,channel_name',1);
                $salesChannel=array_column($salesChannel,'channel_name','id');
		$id = intval($params["id"]);
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('customer_sources_info.html',array(
			'view'=>new CustomerSourcesView(new CustomerSourcesModel($id,1)),
			'tab_id'=>$tab_id,
                        'salesChannel'=>$salesChannel,
                        'arr'=>$this->arr,
                        'act'=>$act
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
		$this->render('customer_sources_show.html',array(
			'view'=>new CustomerSourcesView(new CustomerSourcesModel($id,1)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 * getData, 接收数据
	 */
	public function getData($id=0){
		$data = array();
		if($id != 0){
			$data['id'] = _Post::getInt('id');
		}
        $newmodel=new CustomerSourcesModel(1);
		$data['source_name'] = _Post::getString('source_name');	//来源名称
		$data['source_code'] = "00090017".$newmodel->maxid();	//来源编码
		$data['source_class'] = _Post::getInt('source_class');	//一级分类
		$data['source_type'] = _Post::getInt('source_type');	//二级分类
        $data['fenlei'] = _Post::getInt('fenlei');	//客户来源
        $data['is_enabled'] = _Post::getInt('is_enabled');	//是否启用
		return $data;
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

		//rules验证
		$vd = new Validator();
		$vd->set_rules('source_name', '来源名称',  'require');
		$vd->set_rules('source_class', '一级分类',  'require|isNumber');
		$vd->set_rules('source_type', '二级分类',  'require|isNumber');
        $vd->set_rules('is_enabled', '是否启用',  'isEmun');
       	

		if (!$vd->is_valid($_POST))
		{
			$result['error'] = $vd->get_errors();
			Util::jsonExit($result);
		}
		$olddo = array();
		$newdo = $this->getData();
                $newdo['source_own_id']=_Request::getInt('source_own_id');

		$newmodel =  new CustomerSourcesModel(2);

		if($newmodel->hasName($newdo['source_name']))
		{
			$result['error'] = '来源名称重复';
			Util::jsonExit($result);
		}

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
		//rules验证
		$vd = new Validator();
		$vd->set_rules('source_name', '来源名称',  'require');
		$vd->set_rules('source_code', '来源编码',  'require');
		$vd->set_rules('source_class', '一级分类',  'require|isNumber');
		$vd->set_rules('source_type', '二级分类',  'require|isNumber');
        $vd->set_rules('is_enabled', '是否启用',  'isEmun');
    

		if (!$vd->is_valid($_POST))
		{
			$result['error'] = $vd->get_errors();
			Util::jsonExit($result);
		}

		$id = _Post::getInt('id');
		$newmodel =  new CustomerSourcesModel($id,2);

		$newdo = $this->getData($id);
                $newdo['source_own_id']=_Request::getInt('source_own_id');
                $newdo['source_code']=_Request::getString('source_code');
		if($newmodel->hasName($newdo['source_name']))
		{
			$result['error'] = '来源名称重复';
			Util::jsonExit($result);
		}
		$olddo = $newmodel->getDataObject();

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
		$model = new CustomerSourcesModel($id,2);
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
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
        

}

?>