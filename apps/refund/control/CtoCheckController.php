<?php
/**
 *  -------------------------------------------------
 *   @file		: AppReturnCheckController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166322@qq.com>
 *   @date		: 2015-02-13 16:08:10
 *   @update	:
 *  -------------------------------------------------
 */
class CtoCheckController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('cto_check_search_form.html',array('view'=>new AppReturnGoodsView(new AppReturnGoodsModel(31)),'bar'=>Auth::getBar()));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		if($_SESSION['userType']==1){
            $department = _Request::getInt('department')?_Request::getInt('department'):0;
        }else{
           // if(isset($_REQUEST['department'])){
                $department = _Request::getInt('department')?_Request::getInt('department'):($_SESSION['qudao']?$_SESSION['qudao']:-1);
           // }else{
              //  $department = _Request::getInt('department')?_Request::getInt('department'):($_SESSION['qudao']?current(explode(',', $_SESSION['qudao'])):-1);
            //}
        }
		$args = array(
            'mod'   => _Request::get("mod"),
            'con'   => substr(__CLASS__, 0, -10),
            'act'   => __FUNCTION__,
			'return_id'	=> _Request::getInt("return_id"),
			'order_sn'	=> _Request::getString("order_sn"),
			'return_type'	=> _Request::getInt("return_type"),
			'start_time'	=> _Request::getString("start_time"),
			'end_time'	=> _Request::getString("end_time"),
			'department'	=> $department,
		);
		$page = _Request::getInt("page",1);
		$where = array(
            'check_status' => 3,
            'return_id'=>$args['return_id'],
            'order_sn'=>$args['order_sn'],
            'return_type'=>$args['return_type'],
            'start_time'=>$args['start_time'],
            'end_time'=>$args['end_time'],
            'department'=>$args['department'],
        );

                if($args['return_id']){
                        $where =array();
                        $where['return_id']=$args['return_id'];
                }
		$model = new AppReturnGoodsModel(31);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'cto_check_search_page';
		$this->render('cto_check_search_list.html',array(
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
		$result['content'] = $this->fetch('cto_check_info.html',array(
			'view'=>new AppReturnCheckView(new AppReturnCheckModel(31))
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
		$result['content'] = $this->fetch('cto_check_info.html',array(
			'view'=>new AppReturnCheckView(new AppReturnCheckModel($id,31)),
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
		$this->render('cto_check_show.html',array(
			'view'=>new AppReturnCheckView(new AppReturnCheckModel($id,31)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		echo 1111;
		exit;
		$olddo = array();
		$newdo=array();

		$newmodel =  new AppReturnCheckModel(32);
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

		$id = _Post::get('id');
		$status = _Post::get('status');
		$info = _Post::get('info');
		if(empty($info)){
            $result['error'] = 'CTO负责人意见不能为空！';
            Util::jsonExit($result);
        }
		if($status < 1){
            $result['error'] = '审核意见不能为空！';
            Util::jsonExit($result);
        }
        $goodsModel = new AppReturnGoodsModel($id,32);
        $do = $goodsModel->getDataObject();
        if(count($do)<1){
            $result['error'] = '退款申请不存在！';
            Util::jsonExit($result);
        }
        if($do['check_status']=='3'){
            $result['error'] = '事业部已审核通过，无需重复申请审核！';
            Util::jsonExit($result);
        }
		$checkModel =  new AppReturnCheckModel(32);
        $check_id = $checkModel->getCheckId($id);
        $checkModel =  new AppReturnCheckModel($check_id,32);
        $olddo = $checkModel->getDataObject();
		$newdo=array(
            'id'=>$check_id,
            'cto_id'=> $_SESSION ['userId'],
            'cto_status'=>$status,
            'cto_res'=>$info,
            'cto_time'=>date ( "Y-m-d H:i:s" )
		);

        $is_mory = false;//是否是0元退款,并且不退商品
		if($status ==1){
            if($do['return_by'] == '2' && $do['apply_return_amount'] == '0' && $do["order_goods_id"] == '0'){
                $is_mory = true;
                $goodsModel->setValue('check_status', 5);
            }else{
                $goodsModel->setValue('check_status', 3);
            }
            $goodsModel->save(true);
		}

        //如果是不退商品且申请退款金额为0元时，直接跳过财务审核。
        if($is_mory){

            $newdo ['deparment_finance_res'] = '系统操作,不需部门财务操作';
            $newdo ['deparment_finance_id'] = $_SESSION ['userId'];
            $newdo ['deparment_finance_status'] =1;
            $newdo ['deparment_finance_time'] = date ( "Y-m-d H:i:s" );
            
            $newdo ['finance_id'] = $_SESSION ['userId'];
            $newdo ['finance_status'] = 1;
            $newdo ['finance_res'] = '系统操作,不需财务操作';
            $newdo ['finance_time'] = date ( "Y-m-d H:i:s" );
        }

        $res = $checkModel->saveData($newdo, $olddo);
		$newmodel =  new AppReturnLogModel($id,32);

		$even_time = date("Y-m-d H:i:s");
		$name =$_SESSION['userName'];

		$newdolog=array(
            'return_id'=>$id,
            'even_time'=>$even_time,
            'even_user'=>$name,
            'even_content'=>'CTO审核'.$info
        );
		$res = $newmodel->saveData($newdolog,array());
        //订单操作日志
        $apiModel = new ApiRefundModel();
		$order_info = $apiModel->GetExistOrderSn($do['order_sn'],"`oi`.`id`,`oi`.`order_status`,`oi`.`send_good_status`,`oi`.`order_pay_status`,`oi`.`buchan_status`");
		$insert_action = array ();
		$insert_action ['order_id'] = $order_info ['id'];
		$insert_action ['order_status'] = $order_info ['order_status'];
		$insert_action ['shipping_status'] = $order_info ['send_good_status'];
		$insert_action ['pay_status'] = $order_info ['order_pay_status'];
		$insert_action ['remark'] = '退款/退货单:事业部已经审核通过';
		$insert_action ['create_user'] = $_SESSION ['userName'];
		$insert_action ['create_time'] = date ( 'Y-m-d H:i:s' );
        $res = $apiModel->AddOrderActionInfo($insert_action);

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
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppReturnCheckModel($id,32);
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

	/**
	 * 	check_status，事业部负责人批准
	 */
	public function check_status($params) {
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }			
		$id = intval($params["id"]);
		$result = array('success' => 0, 'error' => '');
		$result['content'] = $this->fetch('cto_check_info.html',array('id'=>$id));
		$result['title'] = '事业部审核';
		Util::jsonExit($result);
	}
}

?>
