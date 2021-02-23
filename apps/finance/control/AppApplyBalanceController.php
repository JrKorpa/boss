<?php
/**
 *  -------------------------------------------------
 *   @file		: AppApplyBalanceController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-04 12:54:27
 *   @update	:
 *  -------------------------------------------------
 */
class AppApplyBalanceController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_apply_balance_search_form.html',array(
			'bar'=>Auth::getBar(),'view'=>new AppApplyBalanceView(new AppApplyBalanceModel(29)),
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
			'supplier_id'=>_Request::get('supplier_id'),
			'pay_status'=>_Request::get('pay_status'),
			'balance_no'=>_Request::get('balance_no'),
		);
		$page = _Request::getInt("page",1);
		$where = array();
		$where['supplier_id'] = $args['supplier_id'];
		$where['pay_status'] = $args['pay_status'];
		$where['balance_no'] = $args['balance_no'];

		$model = new AppApplyBalanceModel(29);
		$data = $model->pageList($where,$page,10,false);
		$dict = new DictView(new DictModel(1));

		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_apply_balance_search_page';
		$this->render('app_apply_balance_search_list.html',array(
			'view'=>new AppApplyBalanceView(new AppApplyBalanceModel(29)),
			'pa'=>Util::page($pageData),'page_list'=>$data,'dict'=>$dict
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_apply_balance_info.html',array(
			'view'=>new AppApplyBalanceView(new AppApplyBalanceModel(29))
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
//	public function edit ($params)
//	{
//		$id = intval($params["id"]);
//		$tab_id = _Request::getInt("tab_id");
//		$result = array('success' => 0,'error' => '');
//		$result['content'] = $this->fetch('app_apply_balance_info.html',array(
//			'view'=>new AppApplyBalanceView(new AppApplyBalanceModel($id,29)),
//			'tab_id'=>$tab_id
//		));
//		$result['title'] = '编辑';
//		Util::jsonExit($result);
//	}

	/**
	 * 渲染付款页面
	 */
	public function toPay($params){
		$id = intval($params["id"]);

		$model = new AppApplyBalanceModel($id,29);
		$view = new AppApplyBalanceView($model);
		$balance_status = $view->get_balance_status();
		$total_real = $view->get_total_real();	//应付
		$pay_total = $view->get_pay_total();	//已付
		$remnant = $total_real-$pay_total; 		//剩余金额

		if($remnant<=0){
			$result['content'] = "已付清,无需再次付款!!!";
			Util::jsonExit($result);
		}

		if($balance_status != 2){
			$result['content'] = "审核未通过,不可支付!!!";
			Util::jsonExit($result);
		}

		$supplier_id = $view->get_supplier_id();
		$payinfo = $model->getSupplierPay($supplier_id);
		$dict = new DictView(new DictModel(1));

		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_apply_balance_pay.html',array(
			'view'=>$view,'payinfo'=>$payinfo, 'dict'=>$dict,'remnant'=>$remnant
		));
		$result['title'] = '财务付款';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$this->render('app_apply_balance_show.html',array(
			'view'=>new AppApplyBalanceView(new AppApplyBalanceModel($id,29)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 * 申请单明细列表
	 */
	public function showBalance(){
		$apply_arr = _Post::getString('apply_arr');
		$apply_arr = explode(',',$apply_arr);

		$model = new AppApplyBalanceModel(29);
		$bills = $model->getBills($apply_arr);

		$this->render('app_apply_balance_bills_list.html',["data"=>$bills]);

	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');

		$is_adjust = _Post::getInt('is_adjust');

		$balance_reason = _Post::getString('balance_reason');
		if($is_adjust && $balance_reason==""){
			echo '<pre>';
			print_r("请填写调整原因！！！");
			echo '</pre>';
			exit;
		}

		$olddo = array();
		$newdo=array();
		$newmodel =  new AppApplyBalanceModel(30);

		$newdo['balance_no'] = $newmodel->mkBalanceNO();
		$newdo['apply_array'] = _Post::getString('apply_array');//申请单号数组
		$newdo['supplier_id'] = _Post::getInt('supplier_id');
		$newdo['supplier_name'] = _Post::getString('supplier');
		$newdo['total_sys'] = _Post::getFloat('sys_money');//系统金额
		$newdo['total_dev'] = _Post::getFloat('balance_money');//调整金额
		$newdo['total_real'] = _Post::getFloat('deal_money');//应付金额
		$newdo['pay_type'] = _Post::getString('pay_type');
		$newdo['balance_status'] = 1;	//单据状态,新增默认1=待审核
		$newdo['pay_status'] = 1;		//付款状态,1=未付款
		$newdo['create_id'] = $_SESSION['userId'];
		$newdo['create_name'] = $_SESSION['realName'];
		$newdo['create_time'] = time();

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$order_id = $res;
			$view = new AppApplyBalanceView(new AppApplyBalanceModel($res,30));
			$balance_no = $view->get_balance_no();//应付单号

			$arr = explode(',',$newdo['apply_array']);
			//回写应付单号,单据状态,提交人，提交时间
			foreach ($arr as $v) {
				$sql = "UPDATE `app_apply_bills` SET `pay_number`='".$balance_no."',`bills_type` =2,`check_id`='".$newdo['create_id']."',`check_name`='".$newdo['create_name']."',`check_time`=".time()." WHERE `id` = ".$v;
				$res = DB::cn(29)->query($sql);
			}

			//生成日志
			$logModel = new AppApplyPayLogModel(30);

			$log['order_type'] = 3;						//3=应付单据
			$log['order_id'] = $order_id;				//单据ID
			$log['order_no'] = $newdo['balance_no'];	//单据号码
			$log['handle_type'] = 1;					//1=生成
			$log['create_id'] = $_SESSION['userId'];
			$log['create_time'] = time();
			$log['create_name'] = $_SESSION['userName'];
			$log['content'] = '生成应付单据';
			if($balance_reason){
				$log['content'] .= ';调整金额为：'.$newdo['total_dev'];
			}
			$res = $logModel->saveData($log,$olddo);
			if($res){
				$result['success'] = 1;
			}
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
//	public function update ($params)
//	{
//		$result = array('success' => 0,'error' =>'');
//		$_cls = _Post::getInt('_cls');
//		$tab_id = _Post::getInt('tab_id');
//
//		$id = _Post::getInt('id');
//		echo '<pre>';
//		print_r ($_POST);
//		echo '</pre>';
//		exit;
//
//		$newmodel =  new AppApplyBalanceModel($id,30);
//
//		$olddo = $newmodel->getDataObject();
//		$newdo=array(
//		);
//
//		$res = $newmodel->saveData($newdo,$olddo);
//		if($res !== false)
//		{
//			$result['success'] = 1;
//			$result['_cls'] = $_cls;
//			$result['tab_id'] = $tab_id;
//			$result['title'] = '修改此处为想显示在页签上的字段';
//		}
//		else
//		{
//			$result['error'] = '修改失败';
//		}
//		Util::jsonExit($result);
//	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppApplyBalanceModel($id,30);
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

	/**
	 * 审核通过
	 */
	public function checkPass($params){
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppApplyBalanceModel($id,30);
		$self = $model->checkSelf($id);
		if($self){
			$result['error'] = '操作失败!!!不可审核自己的单';
			Util::jsonExit($result);
		}

		$view = new AppApplyBalanceView($model);
		$balance_status = $view->get_balance_status();
		if($balance_status != 1){
			$result['error'] = "非待审核状态不允许操作";
			Util::jsonExit($result);
		}

		$model->setValue('balance_status',2);
		$model->setValue('check_id',$_SESSION['userId']);
		$model->setValue('check_name',$_SESSION['realName']);
		$model->setValue('check_time',time());
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}

	/**
	 * 审核驳回
	 */
	public function checkOff($params){
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppApplyBalanceModel($id,30);
		$self = $model->checkSelf($id);
		if($self){
			$result['error'] = '操作失败!!!不可审核自己的单';
			Util::jsonExit($result);
		}
		
		$view = new AppApplyBalanceView($model);
		$balance_status = $view->get_balance_status();
		if($balance_status != 1){
			$result['error'] = "非待审核状态不允许操作";
			Util::jsonExit($result);
		}
		$model->setValue('balance_status',3);
		$model->setValue('check_id',$_SESSION['userId']);
		$model->setValue('check_name',$_SESSION['realName']);
		$model->setValue('check_time',time());
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}


}

?>