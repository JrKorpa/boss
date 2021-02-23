<?php
/**
 *  -------------------------------------------------
 *   @file		: AppPayShouldController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-27 10:41:29
 *   @update	:
 *  -------------------------------------------------
 */
class AppPayShouldController extends CommonController
{
	protected $smartyDebugEnabled = false;
	var $spay_list='';
	var $payShouldOrderStatus='';
	var $payShouldStatus='';
	public function __construct() {
		parent::__construct();
		 $company_list  = array(
				'58'	=>	'总公司'
			);
		$this->assign('company_list',$company_list);

		//单据状态
		 $this->payShouldStatus =  array(
			'1'=>'待审核',
			'2'=>'已审核',
			'3'=>'已取消',
		);
		$this->assign('payShouldStatus',$this->payShouldStatus);

		//应付类型
		 $this->spay_list = array(
			1=>'代销借货',
			2=>'成品采购',
			3=>'石包采购',
		);
		$this->assign('spay_list', $this->spay_list);
		//单据状态
		 $this->payShouldOrderStatus =  array(
			'1'=>'待审核',
			'2'=>'已审核',
			'3'=>'已取消',
		);

		//付款状态
		 $payStatuse =  array(
			'1'=>'未付款',
			'2'=>'部分付款',
			'3'=>'已付款',
		);
		 $this->assign('payStatuse',$payStatuse);
	}
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$model = new AppPayRealModel(29);
		$jiesuanshang_list = $model->getJiesuanshangList();
		$this->render('app_pay_should_search_form.html',array('bar'=>Auth::getBar(),'j_list'=>$jiesuanshang_list));
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
			'company' => _Request::getInt("company"),
			'status' => _Request::getInt("status"),
			'pay_status' => _Request::getInt("pay_status"),
			'prc_id' => _Request::getInt("prc_id"),
			'pay_type' => _Request::getInt("pay_type"),
			'pay_should_all_name' => _Request::getString("pay_should_all_name"),
			'make_time_s' => _Request::getString("make_time_s"),
			'make_time_e' => _Request::getString("make_time_e"),
			'check_time_s' => _Request::getString("check_time_s"),
			'check_time_e' => _Request::getString("check_time_e"),
		);
		$page = _Request::getInt("page",1);
		$where = array(
				'company' => $args['company'],
				'status' => $args['status'],
				'pay_status' => $args['pay_status'],
				'prc_id' => $args['prc_id'],
				'pay_type' => $args['pay_type'],
				'pay_should_all_name' => $args['pay_should_all_name'],
				'make_time_s' => $args['make_time_s'],
				'make_time_e' => $args['make_time_e'],
				'check_time_s' => $args['check_time_s'],
				'check_time_e' => $args['check_time_e'],
			);
		//付款状态
		 $payStatus =  array(
			'1'=>'未付款',
			'2'=>'部分付款',
			'3'=>'已付款',
		);
		$model = new AppPayShouldModel(29);
		$data = $model->pageList($where,$page,10,false);
		foreach ($data['data'] as $key=> $gll){
			$data['data'][$key]['ps_status'] = $this->payShouldOrderStatus[$gll['status']];
			$data['data'][$key]['pay_status2'] = $payStatus[$gll['pay_status']];
			$data['data'][$key]['pay_types'] = $this->spay_list[$gll['pay_type']];
		}
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_pay_should_search_page';
		$this->render('app_pay_should_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	showlist，列表 getDataObject
	 */
	public function showlist ($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			//'参数' = _Request::get("参数");
			'pay_number' => _Request::getInt("id")
		);
		$page = _Request::getInt("page",1);
		$where = array(
				'pay_number' => $args['pay_number']
			);

		$model = new AppPayShouldModel($where['pay_number'],29);
        $info = $model->getDataObject();
        $ps_status = $this->payShouldStatus[$info['status']];
        $spay_list = $this->spay_list[$info['pay_type']];
		$data = $model->getShouldDetailpage($where,$page,10,false);

		foreach ($data['data'] as $key=> $gll){
			$data['data'][$key]['ps_status'] = $ps_status;
			$data['data'][$key]['pay_types'] = $spay_list;
			$data['data'][$key]['pay_apply_numberS'] = str_replace("T-YFSQ", "", $gll['pay_apply_number']);
			$data['data'][$key]['pay_apply_numberS'] = str_replace("YFSQ", "", $data['data'][$key]['pay_apply_numberS']);
		}
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_pay_should_search_page';
		$this->render('app_pay_should_search_show_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	check_order，审核
	 */
	public function check_order ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');

		$id = _Post::getInt('id');
        $status = _Post::getInt('status');
		$newmodel =  new AppPayShouldModel($id,30);
        $info = $newmodel->getShouldInfo($id);

		if($status == 2){//自己不能审核自己的单子
			if($info && $info[0]['make_name'] == $_SESSION['userName']){
				$result['error'] ="自己不能审核自己的单子。";
				Util::jsonExit($result);
			}
		}
		if($status == 3){//自己可以取消自己的单子，审单人可以所有
			if($info && $info[0]['make_name'] != $_SESSION['userName']){
				$result['error'] ="没有审核权限或者不是自己制的单子。";
				Util::jsonExit($result);
			}
		}
		$olddo = $newmodel->getDataObject();
		$newdo=array(
		);
		if(!empty($id) && !empty($status)){
			if($status == 2 && $info && $info[0]['t_cope'] == '0.00'){
				$newmodel->setValue('pay_status',3);
			}
			$newmodel->setValue('status',$status);
			$newmodel->setValue('check_name',$_SESSION['userName']);
			$newmodel->setValue('check_time', date('Y-m-d H:i:s',time()));
			$should_info = $newmodel->save(true);

			$result['success'] = 1;
		}
		Util::jsonExit($result);
	}

}

?>