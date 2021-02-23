<?php
/**
 *  -------------------------------------------------
 *   @file		: CompanyController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-31 17:52:34
 *   @update	:
 *  -------------------------------------------------
 */
class CompanyController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('company','cuteframe');	//生成模型后请注释该行
		//Util::V('company');	//生成视图后请注释该行
	    $viewmodel =new CompanyView(new CompanyModel(1));
	    $viewmodel->dd = $this->dd;
		$this->render('company_search_form.html',array(
		    'bar'=>Auth::getBar(),
		    'view'=>$viewmodel    
		    
		));;
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
			'company_name'=>_Request::get("company_name"),
			'contact'=>_Request::get("contact"),
			'is_deleted'=>0,
		    'is_shengdai'=>_Request::get('is_shengdai'),
		    'sd_company_id'=>_Request::get('sd_company_id')
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['company_name']=$args['company_name'];
		$where['contact']=$args['contact'];
		$where['is_deleted']=$args['is_deleted'];
		$where['is_shengdai']=$args['is_shengdai'];
		$where['sd_company_id']=$args['sd_company_id'];

		$model = new CompanyModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'company_search_page';
		$this->render('company_search_list.html',array(
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
		$viewmodel =new CompanyView($model);
		$viewmodel->dd = $this->dd;
		$result['content'] = $this->fetch('company_info.html',array(
			'view'=>$viewmodel,
			'processors' => $model->get_processors()	
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
		$model = new CompanyModel($id, 1);
		$viewmodel =new CompanyView($model);
		$viewmodel->dd = $this->dd;
		$result['content'] = $this->fetch('company_info.html',array(
			'view'=>$viewmodel,
			'processors' => $model->get_processors()	
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
		$this->render('company_show.html',array(
			'view'=>new CompanyView(new CompanyModel($id,1)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$company_name = _Post::getString('company_name');
		$company_sn = _Post::getString('company_sn');
		$parent_id =1;
		$contact =_Post::getString('contact');
		$phone = _Post::getString('phone');
		$address= _Post::getString('address');
		$account = _Post::getString('account');
		$receipt = _Post::getInt('receipt');
		$is_sign = _Post::getInt('is_sign');
		$bank_of_deposit = _Post::getString('bank_of_deposit');
		$remark = _Post::getString('remark');
		$create_time = time();
		$user_id = $_SESSION['userId'];
        $is_shengdai = _Post::getInt('is_shengdai');
        $sd_company_id = _Post::getInt('sd_company_id');   
        $company_type = _POST::getInt('company_type');
        $processor_id = _POST::getInt('processor_id');
        
        if($is_shengdai == 1 && in_array($id,array(58,515))){
            $result['error'] = '总公司 和 浩鹏 不能设为省代公司！';
            Util::jsonExit($result);
        }
        if($company_type<=0){
            $result['error'] = '公司类型不能为空！';
            Util::jsonExit($result);
        } else if ($company_type == 4 && $processor_id <= 0) {
        	$result['error'] = '公司类型为供应商时，需设置关联供应商';
        	Util::jsonExit($result);
        }
        
		$olddo = array();
		$newdo=array(
			'company_name'=>$company_name,
			'company_sn'=>$company_sn,
			'parent_id'=>$parent_id,
			'contact'=>$contact,
			'phone'=>$phone,
			'address'=>$address,
			'account'=>$account,
			'receipt'=>$receipt,
			'is_sign'=>$is_sign,
			'bank_of_deposit'=>$bank_of_deposit,
			'remark'=>$remark,
			'create_time'=>$create_time,
			'create_user'=>$user_id,
		    'is_shengdai'=>$is_shengdai,
		    'sd_company_id'=>$sd_company_id,
		    'company_type'=>$company_type,
			'processor_id' => $processor_id
		);
        
		$newmodel =  new CompanyModel(2);
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
		$id = _Post::getInt('id');
		$company_name = _Post::getString('company_name');
		$company_sn = _Post::getString('company_sn');
		$parent_id =_Post::getInt('parent_id');
		$contact =_Post::getString('contact');
		$phone = _Post::getString('phone');
		$address= _Post::getString('address');
		$account = _Post::getString('account');
		$receipt = _Post::getInt('receipt');
		$is_sign = _Post::getInt('is_sign');
		$bank_of_deposit = _Post::getString('bank_of_deposit');
		$remark = _Post::getString('remark');
		$create_time = time();
		$user_id = $_SESSION['userId'];
		$is_shengdai = _Post::getInt('is_shengdai');
		$sd_company_id = _Post::getInt('sd_company_id');
		$company_type = _POST::getInt('company_type');
		$processor_id = _POST::getInt('processor_id');
		
		if($is_shengdai == 1 && in_array($id,array(58,515))){
		    $result['error'] = '总公司 和 浩鹏 不能设为省代公司！';
		    Util::jsonExit($result);
		}
		if($company_type<=0){
			$result['error'] = '公司类型不能为空！';
			Util::jsonExit($result);
		} else if ($company_type == 4 && $processor_id <= 0) {
			$result['error'] = '公司类型为供应商时，需设置关联供应商';
			Util::jsonExit($result);
		}
		
		$newmodel =  new CompanyModel($id,2);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id'=>$id,
			'company_name'=>$company_name,
			'company_sn'=>$company_sn,
			'parent_id'=>$parent_id,
			'contact'=>$contact,
			'phone'=>$phone,
			'address'=>$address,
			'account'=>$account,
			'receipt'=>$receipt,
			'is_sign'=>$is_sign,
			'bank_of_deposit'=>$bank_of_deposit,
			'remark'=>$remark,
			'create_time'=>$create_time,
			'create_user'=>$user_id,
		    'is_shengdai'=>$is_shengdai,
		    'sd_company_id'=>$sd_company_id,
		    'company_type'=>$company_type,
			'processor_id' =>$processor_id
		);

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
		//删除公司的同时，禁用公司底下的仓库和柜位
		$warehouseModel = new ApiWarehouseModel();
		$ret = $warehouseModel->stopWarehouseByCompanyId($id);
		if(is_array($ret)){
			$result['error'] = "<span style='color:red;'>".$ret[0]."</span>";
			Util::jsonExit($result);
		}
	
		$model = new CompanyModel($id,2);
		$do = $model->getDataObject();
		if($do['is_system']==1){
			$result['error'] = "系统内置无法删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;			
			//日志记录
			$dataLog['pkdata'] = array('id'=>$id);
			$dataLog['remark'] = "删除公司的同时，禁用了公司底下的仓库和柜位";
			$this->operationLog("delete",$dataLog);
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
}

?>