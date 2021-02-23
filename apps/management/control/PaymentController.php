<?php
/**
 *  -------------------------------------------------
 *   @file		: PaymentController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-18 11:49:31
 *   @update	:
 *  -------------------------------------------------
 */
class PaymentController extends CommonController
{
	protected $smartyDebugEnabled = false;
	
	public function __construct()
	{
			parent::__construct();
			$_SESSION['userType'] != 1 && $this->checkPermission();
			
	} 

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('payment_search_form.html',array('bar'=>Auth::getBar()));
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
			'pay_name'=>_Request::getString('pay_name'),
            'is_range'=>_Request::get('is_range'),
            'is_way'=>_Request::get('is_way'),
            'is_enabled'=>_Request::get('is_enabled')

		);
		$page = _Request::getInt("page",1);
		$where['is_deleted'] = 0;
		$where['pay_name'] = $args['pay_name'];
        $where['is_range'] = $args['is_range'];
        $where['is_way'] = $args['is_way'];
        $where['is_enabled'] = $args['is_enabled'];
		$model = new PaymentModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'payment_search_page';
		$this->render('payment_search_list.html',array(
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
		$result['content'] = $this->fetch('payment_info.html',array(
			'view'=>new PaymentView(new PaymentModel(1))
		));
		$result['title'] = '支付方式-添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('payment_info.html',array(
			'view'=>new PaymentView(new PaymentModel($id,1))
		));
		$result['title'] = '支付方式-编辑';
		Util::jsonExit($result);
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

		//rules验证
		$vd = new Validator();
		$vd->set_rules('pay_name', '支付名称',  'require');
		$vd->set_rules('pay_code', '拼音名称',  'require|isEnglish');
		$vd->set_rules('is_enabled', '是否启用',  'isEmun');

		if (!$vd->is_valid($_POST))
		{
			$result['error'] = $vd->get_first_error();
			Util::jsonExit($result);
		}
		//接收数据
		foreach ($_POST as $k => $v) {
			$$k = _Post::get($k);
		}
		if($pay_fee>1){
			$result['error'] = "手续费不能大于1！";
			Util::jsonExit($result);
		}
		$res = Util::isHas($pay_name,'payment','pay_name');
		if($res){
			$result['error'] = "很抱歉,请换个名称——【支付名称】！";
			Util::jsonExit($result);
		}
//		$res = Util::isHas($pay_code,'payment','pay_code');
//		if($res){
//			$result['error'] = "很抱歉,请换个名称——【拼音名称】！";
//			Util::jsonExit($result);
//		}

		$is_online = (isset($is_online))?$is_online:'0';
		$is_offline = (isset($is_offline))?$is_offline:'0';
        $is_order = (isset($is_order))?$is_order:'0';
		$is_balance = (isset($is_balance))?$is_balance:'0';
		$is_cod = (isset($is_cod))?$is_cod:'0';
		$is_web = (isset($is_web))?$is_web:'1';
		$is_beian = (isset($is_beian))?$is_beian:'0';
        $is_pfls = (isset($is_pfls))?$is_pfls:'0';

		$olddo = array();
		$newdo=array(
			"pay_name"=>$pay_name,
			"pay_code"=>strtoupper($pay_code),
			"pay_fee"=>$pay_fee,
			"pay_desc"=>$pay_desc,
			"pay_config"=>$pay_config,
			"is_enabled"=>$is_enabled,
			"is_cod"=>$is_cod,
			"is_online"=>$is_online,
			"is_offline"=>$is_offline,
            "is_order"=>$is_order,
			"is_balance"=>$is_balance,
			"is_display"=>1,
			"is_web"=>$is_web,
		    'is_beian'=>$is_beian,
            'is_pfls'=>$is_pfls,
			"add_time"=>time(),
		);


		$newmodel =  new PaymentModel(2);
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
		$vd->set_rules('pay_name', '支付名称',  'require');
		$vd->set_rules('pay_code', '拼音名称',  'require|isEnglish');
		$vd->set_rules('is_enabled', '是否启用',  'isEmun');

		if (!$vd->is_valid($_POST))
		{
			$result['error'] = $vd->get_errors();
			Util::jsonExit($result);
		}

		foreach ($_POST as $k => $v) {
			$$k = _Post::get($k);
		}

		$is_online = (isset($is_online))?$is_online:'0';
		$is_offline = (isset($is_offline))?$is_offline:'0';
        $is_order = (isset($is_order))?$is_order:'0';
		$is_balance = (isset($is_balance))?$is_balance:'0';
		$is_beian = (isset($is_beian))?$is_beian:'0';
        $is_pfls = (isset($is_pfls))?$is_pfls:'0';

		$newmodel =  new PaymentModel($id,2);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id'=>$id,
			"pay_name"=>$pay_name,
			"pay_code"=>strtoupper($pay_code),
			"pay_fee"=>$pay_fee,
			"pay_desc"=>$pay_desc,
			"pay_config"=>$pay_config,
			"is_enabled"=>$is_enabled,
//			"is_cod"=>$is_cod,
			"is_online"=>$is_online,
			"is_offline"=>$is_offline,
            "is_order"=>$is_order,
			"is_balance"=>$is_balance,
		    'is_beian'=>$is_beian,
//			"is_display"=>$is_display,
//			"is_web"=>$is_web,
            "is_pfls"=>$is_pfls
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
		$model = new PaymentModel($id,2);
		$do = $model->getDataObject();
		if($do['is_enabled'])
		{
			$result['error'] = "当前记录已启用，禁止删除";
			Util::jsonExit($result);
		}
		if($do['is_deleted'])
		{
			$result['success'] = 1;
			Util::jsonExit($result);
		}
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

	/**
	 * 生产拼音名称
	 */
	public function mkCode(){
		$pay_name = _Post::getString('pay_name');
		$code = Pinyin::getFirstCode($pay_name);

		echo strtoupper($code);
	}

	/**
	 *	listAll，排序页面
	 */
	public function listAll ()
	{
		$result = array('success' => 0,'error' => '');
		$menu_id = _Post::getInt('id');
		$model = new PaymentModel($menu_id,1);
		$pays = $model->getEnabled();
		$result['content'] = $this->fetch('payment_sort.html',array('data'=>$pays));
		$result['title'] = '支付方式-排序';
		Util::jsonExit($result);
	}

	/**
	 *	saveSort,排序保存
	 */
	public function saveSort ()
	{
		$result = array('success' => 0,'error' => '');
		$pays = _Post::getList('paymentsArray');
		krsort($pays);
		$pays = array_values($pays);
		$model = new PaymentModel(1);
		$res = $model->sortPayment($pays);
		if($res)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}
	
	/**
	 *	checkPermission,查看权限
	 */
	public function checkPermission ()
	{
		$model = new OrganizationModel(1);
		$permission_arr = array_column($model->getDeptUser(30),'id');
		//array_push($permission_arr,1);
		if(!in_array($_SESSION['userId'],$permission_arr)) die('你没有查看权限');
	}
	
	
}

?>