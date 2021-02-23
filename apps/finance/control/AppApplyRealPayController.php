<?php
/**
 *  -------------------------------------------------
 *   @file		: AppApplyRealPayController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-04 19:20:56
 *   @update	:
 *  -------------------------------------------------
 */
class AppApplyRealPayController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_apply_real_pay_search_form.html',array(
			'bar'=>Auth::getBar(),
			'view'=>new AppApplyRealPayView(new AppApplyRealPayModel(29))
		));
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
			'real_number'=> _Request::get("real_number"),
			'supplier_id'=>_Request::get("supplier_id")

		);
		$page = _Request::getInt("page",1);
		$where['real_number'] = $args['real_number'];
		$where['supplier_id'] = $args['supplier_id'];

		$model = new AppApplyRealPayModel(29);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_apply_real_pay_search_page';
		$this->render('app_apply_real_pay_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_apply_real_pay_info.html',array(
			'view'=>new AppApplyRealPayView(new AppApplyRealPayModel($id,29)),
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
		$this->render('app_apply_real_pay_show.html',array(
			'view'=>new AppApplyRealPayView(new AppApplyRealPayModel($id,29)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 * @author	:	yangxiaotong
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

		$olddo = array();
		$newdo=array();
		$newmodel =  new AppApplyRealPayModel(30);

		$newdo['real_number'] = $newmodel->mkRealNumber();				//实付单号
		$newdo['supplier_id'] = _Post::getInt('supplier_id');			//结算商ID
		$newdo['supplier_name'] = _Post::getString('supplier_name');	//供应商名称
		$newdo['bank_serial'] = _Post::getString('bank_serial');		//银行流水号
		$newdo['account_name'] = _Post::getString('account_name');		//户名
		$newdo['bank_name'] = _Post::getString('bank_name');			//银行名称
		$newdo['bank_account'] = _Post::getString('account');			//银行账户
		$newdo['pay_time'] = _Post::getString('pay_time');				//付款时间
		$newdo['pay_total'] = _Post::getFloat('pay_total');				//实付金额
		$newdo['apply_no'] = _Post::getString('apply_no');				//应付申请单号
		$newdo['create_id'] = $_SESSION['userId'];
		$newdo['create_name'] = $_SESSION['realName'];
		$newdo['create_time'] = time();

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			//todo 判断全部付款还是部分付款
			$id = _Post::getInt('id');
			$sql = 'UPDATE `app_apply_balance` SET `pay_total`='.$newdo['pay_total'].',`pay_status`=3 WHERE `id` = '.$id;
			$res = DB::cn(30)->query($sql);
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

		$newmodel =  new AppApplyRealPayModel($id,30);

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
		$model = new AppApplyRealPayModel($id,30);
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
}

?>