<?php
/**
 *  -------------------------------------------------
 *   @file		: AppCouponPolicyController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-11 15:46:48
 *   @update	:
 *  -------------------------------------------------
 */
class AppCouponPolicyController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $model = new AppCouponPolicyModel(17);
        $statusList = $model->getPolicyStatusList();
		$this->render('app_coupon_policy_search_form.html',array('statusList'=>$statusList,'bar'=>Auth::getBar()));
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
			'policy_status'	=> _Request::getInt('status'),
			'valid_time_start'	=> _Request::getString('time_start'),
			'valid_time_end'	=> _Request::getString('time_end'),
			//'参数' = _Request::get("参数");


		);
		$page = _Request::getInt("page",1);
		$where = array(
            'policy_status'=>$args['policy_status'],
            'valid_time_start'=>$args['valid_time_start'],
            'valid_time_end'=>$args['valid_time_end'],
        );

		$model = new AppCouponPolicyModel(17);
		$data = $model->pageList($where,$page,10,false);
		if($data['data']){
			foreach($data['data'] as &$val){
				$val['policy_status'] = $model->getPolicyStatusList($val['policy_status']);
			}
			unset($val);
		}
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_coupon_policy_search_page';
		$this->render('app_coupon_policy_search_list.html',array(
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
		$result['content'] = $this->fetch('app_coupon_policy_info.html',array(
			'view'=>new AppCouponPolicyView(new AppCouponPolicyModel(17))
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
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
        $model = new AppCouponPolicyModel($id,17);
        if($model->getValue('policy_status')!=1){
            $result['content'] = '只有保存状态的政策可以修改';
        }else{
            $result['content'] = $this->fetch('app_coupon_policy_info.html',array(
                'view'=>new AppCouponPolicyView($model),
                'tab_id'=>$tab_id
            ));
            $result['title'] = '编辑';
        }
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$this->render('app_coupon_policy_show.html',array(
			'view'=>new AppCouponPolicyView(new AppCouponPolicyModel($id,17)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$policy_name = _Post::getString('policy_name');
		$policy_price = _Post::getFloat('policy_price');
		$policy_type = _Post::getInt('policy_type');
		$time_start = _Post::getString('time_start');
		$time_end = _Post::getString('time_end');
		$policy_desc = _Post::getString('policy_desc');

        if($policy_name==''){
            $result['error'] = '优惠券政策名称不能为空！';
            Util::jsonExit($result);
        }
        if(strlen($policy_name)>30){
        	$result['error'] = '优惠券政策名称过长！';
        	Util::jsonExit($result);
        }
        if(strlen($policy_desc)> 60) {
            $result['error'] = '优惠描述不能超过60字!';
        	Util::jsonExit($result);
        }
        if($policy_price==''){
            $result['error'] = '优惠金额不能为空！';
            Util::jsonExit($result);
        }
        if($policy_price>99999999){
        	$result['error'] = '优惠金额超出范围！';
        	Util::jsonExit($result);
        }
        if($policy_type==''){
            $result['error'] = '优惠类型不能为空！';
            Util::jsonExit($result);
        }
        if($time_start==''){
            $result['error'] = '开始时间不能为空！';
            Util::jsonExit($result);
        }
        if($time_end==''){
            $result['error'] = '结束时间不能为空！';
            Util::jsonExit($result);
        }

		$olddo = array();
		$newdo=array();
		$newdo['policy_name'] = $policy_name;
		$newdo['policy_price'] = $policy_price;
		$newdo['policy_type'] = $policy_type;
		$newdo['policy_status'] = 1;
		$newdo['valid_time_start'] = $time_start;
		$newdo['valid_time_end'] = $time_end;
		$newdo['create_time'] = date("Y-m-d H:i:s");
		$newdo['create_user'] = $_SESSION['userName'];
		$newdo['policy_desc'] = $policy_desc;

		$newmodel =  new AppCouponPolicyModel(18);
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
     * update，修改信息入库
     * @param type $param
     */
    public function update($param) {
        $result = array('success' => 0,'error' =>'');
        $id = _Post::getInt('id');
		$policy_name = _Post::getString('policy_name');
		$policy_price = _Post::getFloat('policy_price');
		$policy_type = _Post::getInt('policy_type');
		$time_start = _Post::getString('time_start');
		$time_end = _Post::getString('time_end');
		$policy_desc = _Post::getString('policy_desc');

        if($policy_name==''){
            $result['error'] = '优惠券政策名称不能为空！';
            Util::jsonExit($result);
        }
        if(strlen($policy_name)>30){
        	$result['error'] = '优惠券政策名称过长！';
        	Util::jsonExit($result);
        }
        if(strlen($policy_desc)> 60) {
            $result['error'] = '优惠描述不能超过60字!';
        	Util::jsonExit($result);
        }
        if($policy_price==''){
            $result['error'] = '优惠金额不能为空！';
            Util::jsonExit($result);
        }
        if($policy_price>99999999){
        	$result['error'] = '优惠金额超出范围！';
        	Util::jsonExit($result);
        }
        if($policy_type==''){
            $result['error'] = '优惠类型不能为空！';
            Util::jsonExit($result);
        }
        if($time_start==''){
            $result['error'] = '开始时间不能为空！';
            Util::jsonExit($result);
        }
        if($time_end==''){
            $result['error'] = '结束时间不能为空！';
            Util::jsonExit($result);
        }

		$newmodel =  new AppCouponPolicyModel($id,18);
		$olddo = $newmodel->getDataObject();
		$newdo=array();
		$newdo['id'] = $id;
		$newdo['policy_name'] = $policy_name;
		$newdo['policy_price'] = $policy_price;
		$newdo['policy_type'] = $policy_type;
		$newdo['valid_time_start'] = $time_start;
		$newdo['valid_time_end'] = $time_end;
		$newdo['policy_desc'] = $policy_desc;

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
    }
    
    /**
     * 提交申请
     * @param type $param
     */
    public function submit_apply($param) {
        $result = array('success' => 0,'error' => '');
		$id = intval($param['id']);
		$model = new AppCouponPolicyModel($id,18);
		$do = $model->getDataObject();
		if($do['policy_status'] != 1)
		{
			$result['error'] = "保存状态下数据才可以提交申请";
			Util::jsonExit($result);
		}
		$model->setValue('policy_status',2);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "提交申请失败";
		}
		Util::jsonExit($result);
    }
    

	/**
	 *	checkTrue，审核通过
	 */
	public function checkTrue ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppCouponPolicyModel($id,18);
		$do = $model->getDataObject();
		if($do['policy_status'] != 2)
		{
			$result['error'] = "只有提交申请的数据才可以审核通过";
			Util::jsonExit($result);
		}
		$model->setValue('policy_status',4);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "审核通过失败";
		}
		Util::jsonExit($result);
	}

	/**
	 *	checkCancel，审核驳回
	 */
	public function checkCancel ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppCouponPolicyModel($id,18);
		$do = $model->getDataObject();
		if($do['policy_status'] != 2)
		{
			$result['error'] = "只有提交申请的数据才可以审核驳回";
			Util::jsonExit($result);
		}
		$model->setValue('policy_status',5);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "审核驳回失败";
		}
		Util::jsonExit($result);
	}

	/**
	 *	cancel，作废
	 */
	public function cancel ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppCouponPolicyModel($id,18);
		$do = $model->getDataObject();
		if($do['policy_status'] == 3)
		{
			$result['error'] = "该条记录已是作废状态！";
			Util::jsonExit($result);
		}
        if($do['policy_status'] != 1)
        {
            $result['error'] = "只有保存状态才可以作废！";
            Util::jsonExit($result);
        }
		$model->setValue('policy_status',3);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "作废失败！";
		}
		Util::jsonExit($result);
	}
}

?>