<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderInvoiceController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-30 14:45:45
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderInvoiceController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)	
	{   
	    $model = new AppOrderInvoiceModel(27);
	    $view = new AppOrderInvoiceView($model);
		$this->render('app_order_invoice_search_all_form.html',
		    array('bar'=>Auth::getBar(),'view'=>$view)
		);
	}
	/**
	 *订单明细 发票列表
	 */
	public function search ($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
            '_id'=> _Request::getInt('_id'),
		    'order_id'=> _Request::getInt('order_id'),
        );
		$where = $args;
        $page = _Request::getInt("page",1);        

        $model = new AppOrderInvoiceModel(27);
        $data = $model->pageList($where,$page,10,false);

        $pageData = $data;

        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'app_order_invoice_search_page';
        $this->render('app_order_invoice_search_list.html',array(
            'pa'=>Util::page($pageData),
            'page_list'=>$data,
        ));
    }
    
    /**
     *	add，渲染添加页面
     */
    public function add ()
    {
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
            die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }       
        $result = array('success' => 0,'error' => '','title'=>'添加');
        $order_id = _Request::get('_id');        
        $model =  new AppOrderInvoiceModel(28);
        $view  = new AppOrderInvoiceView($model);
        $res = $model->checkOrderHasInvoivce($order_id);
        if($res){
            $result['content'] = "发票记录已存在，一个订单只能有一条发票记录！";
            Util::jsonExit($result);
        } 
        if($view->get_invoice_address()==''){
            $ordermodel = new AppOrderAddressModel(27);
            $res = $ordermodel->getAddressById($order_id);
            if (!empty($res)) {
                $view->set_invoice_address($res['address']);
            }
        }  
        if($view->get_invoice_email()==""){
            $email = $model->getMemberEmailByOrderId($order_id);
            $view->set_invoice_email($email);
        }     
        $result['content'] = $this->fetch('app_order_invoice_info.html',array(
            'view'=>$view,
            '_id'=>_Post::getInt('_id')
        ));
        Util::jsonExit($result);
    }

    /**
     *	edit，渲染修改页面
     */
    public function edit ($params)
    {
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
            die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }         
        $id = intval($params["id"]);
        $order_id = _Request::getInt('order_id');
        $tab_id = _Request::getInt("tab_id");
        $result = array('success' => 0,'error' => '');
        $order_info = new BaseOrderInfoModel($order_id,27);
        $department_id = $order_info->getValue('department_id');
        $channel_model = new SalesChannelsModel($department_id,1);
        $channel_class = $channel_model->getValue('channel_class');
        if(!empty($channel_class) && $channel_class==1){
            if(!in_array($order_info->getValue('send_good_status'),array(1,4))){
                $result['content'] = "订单已发货不能编辑发票信息";
                Util::jsonExit($result);            
            }
        }     

        $model = new AppOrderInvoiceModel($id,27);
        $view = new AppOrderInvoiceView($model);
        if($view->get_invoice_address()==''){
            $ordermodel = new AppOrderAddressModel(27);
            $res = $ordermodel->getAddressById($order_id);
            if (!empty($res)) {
                $view->set_invoice_address($res['address']);
            }
        }
        if($view->get_invoice_email()==""){
            $email = $model->getMemberEmailByOrderId($order_id);
            $view->set_invoice_email($email);
        }  
        $result['content'] = $this->fetch('app_order_invoice_info.html',array(
            'view'=>$view,
            'tab_id'=>$tab_id,
            '_id'=>$order_id,
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
        $this->render('app_order_invoice_show.html',array(
            'view'=>new AppOrderInvoiceView(new AppOrderInvoiceModel($id,27)),
            'bar'=>Auth::getViewBar()
        ));
    }

    /**
     *	insert，信息入库
     */
    public function insert ($params)
    {
        $result = array('success' => 0,'error' =>'');
        $order_id = _Request::getInt('_id');
        if(empty($order_id)){
            $result['error'] = "数据有误！";
            Util::jsonExit($result);
        }
        $newmodel =  new AppOrderInvoiceModel(28);
        $res = $newmodel->checkOrderHasInvoivce($order_id);
        if($res){
            $result['error'] = "发票记录已存在，一个订单只能有一条发票记录！";
            Util::jsonExit($result);
        }
        
        $is_invoice = _Post::getInt('is_invoice');
        //如果是不需要开发票那么不需要保存
        /* if($is_invoice ==0){
            $result['success'] = 1;
            Util::jsonExit($result);
        }   */      
        //判断订单是否有效
        $orderModel= new BaseOrderInfoModel(27);
        $order_info = $orderModel->getOrderInfoById($order_id);
        if(empty($order_info)){
            $result['error'] = "此订单的数据不存在！";
            Util::jsonExit($result);
        }
        if($order_info['is_delete']==1){
            $result['error'] = "此订单已删除！";
            Util::jsonExit($result);
        }
        if($order_info['order_status']!=1){
            $result['error'] = "此订单状态已经审核或取消或关闭！";
            Util::jsonExit($result);
        }
        if($order_info['order_pay_status']!=1){
            $result['error'] = "此订单已支付！";
            Util::jsonExit($result);
        }
        $invoice_amount = _Request::getString('invoice_amount','0.00');        
        //获取订单金额
        $AccountInfo = $orderModel->getOrderAccount($order_id);
        if($AccountInfo['order_amount']){
            $order_price = $AccountInfo['order_amount'];
        }        
        if($invoice_amount <= $order_price){
            $order_price = $invoice_amount;
        }else{
            $result['error'] = "发票金额不能大于订单金额!";
            Util::jsonExit($result);
        }
        //is_invoice:1是  0否
        $olddo = array();
        $newdo=array(
            'is_invoice'=>  $is_invoice,
            'invoice_status' =>_Post::getInt('invoice_status'),
            'order_id'=> _Request::getInt('_id'),
            'invoice_title'=> _Request::getString('invoice_title'),
            'invoice_type'=> _Request::getString('invoice_type'),
            'taxpayer_sn'=> _Request::getString('taxpayer_sn'),
            'invoice_content'=> _Request::getString('invoice_content'),
            'invoice_amount'=> $invoice_amount,
            'invoice_address'=> _Request::getString('invoice_address'),
            'invoice_num'=> _Request::getString('invoice_num'),
            'invoice_num'=>1,//默认未开发票
            'create_time'=>  date("Y-m-d H:i:s"),
            'create_user'=> $_SESSION['userName'],
            'title_type'=>_Post::get('title_type'),
            'invoice_email'=>_Post::get('invoice_email'),
        );
        $result['error'] = '添加失败!'.var_export($newdo,true);
        Util::jsonExit($result);
        $res = $this->checkData($newdo);
        if($res['success'] ==0){
            $result['error'] = $res['error'];
            Util::jsonExit($result);
        }        
        $res = $newmodel->saveData($newdo,$olddo);
        //$result['success'] = 0;
        //$result['error'] = '添加失败!'.var_export($res,true);
        // Util::jsonExit($result);
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
    protected function checkData($params){
        $result = array('success'=>0,'error'=>'');
        $title_type = isset($params['title_type'])?$params['title_type']:'';
        if(!isset($params['is_invoice'])){
            $result['error'] = "是否开发票不能为空";
            return $result;
        }
        if(empty($params['invoice_title'])){
            $result['error'] = "发票抬头不能为空";
            return $result;
        }
        if(empty($params['invoice_type'])){
            $result['error'] = "请选择发票类型！";
            return $result;
        }
        if(empty($params['invoice_status'])){
            $result['error'] = "发票状态不能为空！";
            return $result;
        }
        if($params['invoice_status']==2){
            if(empty($params['invoice_num'])){
                $result['error'] = "已开票状态时，发票号不能为空！";
                return $result;
            }
            if($title_type ==2 && empty($params['taxpayer_sn'])){
                $result['error'] = "已开票状态时，公司发票的纳税人识别号必须填写!";
                return $result;
            }
        }
        
        if($title_type ==1 && !empty($params['taxpayer_sn'])){
            $result['error'] = "当发票抬头为个人时，不需要填写纳税人识别号，请清空 纳税人识别号!";
            return $result;
        }        
        $result['success'] = 1;
        return $result;
        
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
        $is_invoice = _Request::getInt('is_invoice');
        $invoice_status = _Request::getInt('invoice_status');
        $order_id = _Request::getInt('_id');        
        $invoice_title = _Request::getString('invoice_title');
        $invoice_content = _Request::getString('invoice_content');
        $invoice_num = _Request::getString('invoice_num');
		$invoice_address= _Request::getString('invoice_address');
		$invoice_amount = _Post::getFloat('invoice_amount');
		if(empty($order_id)){
		    $result['error'] =  "数据有误！";
		    Util::jsonExit($result);
		}
		$newdo=array(
		    'id'=> $id,		    
		    'is_invoice'=>_Post::getInt('is_invoice'),
		    'invoice_num'=>_Post::getString('invoice_num'),
		    'invoice_title'=>_Post::getString('invoice_title'),
		    'invoice_content'=>_Post::getString('invoice_content'),
		    'invoice_amount'=>_Post::getFloat('invoice_amount'),
		    'invoice_address'=>_Post::getString('invoice_address'),
		    'invoice_type'=>_Post::get('invoice_type'),
		    'invoice_status'=>_Post::get('invoice_status'),
		    'taxpayer_sn'=>_Post::get('taxpayer_sn'),
		    'title_type'=>_Post::get('title_type'),
		    'invoice_email'=>_Post::get('invoice_email'),
		);
		$res = $this->checkData($newdo);
		if($res['success'] ==0){
		    $result['error'] = $res['error'];
		    Util::jsonExit($result);
		}
        
        //判断订单是否有效
        $orderModel= new BaseOrderInfoModel(27);
        $order_info = $orderModel->getOrderInfoById($order_id);
        if(empty($order_info)){
            $result['error'] = "此订单的数据不存在！";
            Util::jsonExit($result);
        }
        if($order_info['is_delete']==1){
            $result['error'] = "此订单已删除！";
            Util::jsonExit($result);
        }
        //获取订单金额
        $AccountInfo = $orderModel->getOrderAccount($order_id);
        if($AccountInfo['order_amount']){
            $order_price = $AccountInfo['order_amount'];
        }

		if($invoice_amount<=$order_price){
			$order_price=$invoice_amount;
		}else{
            $result['error'] = "发票金额不能大于订单金额!";
            Util::jsonExit($result);
		}

        $newmodel =  new AppOrderInvoiceModel($id,28);
	
        $create_user = $_SESSION['userName'];
		$apiFinanceModel = new ApiFinanceModel();
		$invoiceList = $apiFinanceModel->getInvoiceInfo($order_info['order_sn']);

		$olddo = $newmodel->getDataObject();
		
		//不开发票
		if($is_invoice == 0){
			//原来要开发票
			if($olddo['is_invoice'] != 0){
				//确认原来发票状况
				if(!empty($olddo['invoice_num'])){
					$invoiceInfo = $apiFinanceModel->getInvoiceInfoByInvoiceNum($olddo['invoice_num']);
					//如果真存在发票
					if($invoiceInfo){
						//把老发票作废或直接删除
						if($invoiceInfo['status']==1){
							$apiFinanceModel->deleteInvoiceInfoByInvoiceNum($olddo['invoice_num']);
						}elseif($invoiceInfo['status']==2){
							$apiFinanceModel->updateInvoiceInfoByInvoiceNum($olddo['invoice_num'],array('status'=>3));
						}
					}
				}
			}
			//对发票全部作废
			if($invoiceList){
				foreach($invoiceList as $invoice){
					if($invoice['status'] != 3){
						$ids[]=$invoice['id'];
					}
				}
				if(!empty($ids)){
					$ret=$apiFinanceModel->updateInvoiceInfoStatusByIds($ids,3);
				}
			}

			$newdo['invoice_status']=$invoice_status;
			$newdo['is_invoice']=0;
			$newdo['invoice_num']="";
		}else{
			//确认原来发票状况
			//可能之后由其他部门来添加
			/*
			if(empty($invoice_num)){
				$result['error'] = '修改失败,发票号不能为空!';
				Util::jsonExit($result);
			}
			*/
			if($invoice_num){
				if($olddo['invoice_num']!=$invoice_num){
					if(!empty($invoice_num)){
						//新发票存在
						$invoiceInfo = $apiFinanceModel->getInvoiceInfoByInvoiceNum($invoice_num);
						//如果真存在发票
						if(!empty($invoiceInfo)){
							//$result['error'] = '修改失败,发票信息已存在!';
							//Util::jsonExit($result);
						}
					}
					if(!empty($olddo['invoice_num'])){
						//$invoiceInfo = $apiFinanceModel->getInvoiceInfoByInvoiceNum($olddo['invoice_num']);
						//如果真存在发票
						if(!empty($invoiceInfo)){
							//$ret=$apiFinanceModel->updateInvoiceInfoStatusByIds(array($invoiceInfo['id']),3);
						}
					}

					//添加发票
					$insertdata=array();
					$insertdata['price'] = $order_price;
					$insertdata['invoice_num'] = $invoice_num;
					$insertdata['title'] = $invoice_title;
					$insertdata['content'] = $invoice_content;
					$insertdata['status'] = 2;
					$insertdata['create_user'] = $_SESSION['userName'];
					$insertdata['create_time'] = date("Y-m-d H:i:s");
					$insertdata['use_user'] = $_SESSION['userName'];
					$insertdata['use_time'] = date("Y-m-d H:i:s");
					$insertdata['type'] = 1;
					$insertdata['order_sn'] = $order_info['order_sn'];
					$ret=$apiFinanceModel->createInvoiceInfo($insertdata);
					if($ret['error']>0){
						$result['error'] = '修改失败,发票信息未正常生成!';
						Util::jsonExit($result);
					}else{
						$invoice_id = $ret['data'];
					}
				}else{
					$updatedata=array();
					$updatedata['status']=$invoice_status;
					$updatedata['price']=$order_price;
					$updatedata['title']=$invoice_title;
					$updatedata['content']=$invoice_content;
					$updateDo = $apiFinanceModel->updateInvoiceInfoByInvoiceNum($invoice_num,$updatedata);
				}
			}
		
			$newdo['invoice_status']=$invoice_status;
			$newdo['is_invoice']=$is_invoice;
			$newdo['invoice_num']=$invoice_num;
			$newdo['invoice_content']=$invoice_content;
		}
        $res = $newmodel->saveData($newdo,$olddo);
        if($res !== false)
        {
            $orderInfoModel = new BaseOrderInfoModel(27);
            $orderInfo = $orderInfoModel->getOrderInfoById($order_id);
            if($is_invoice != $orderInfo['is_real_invoice']){
                $orderInfoModel->updateOrderInvoiceByOrder($order_id,$is_invoice);
            }
            $logInfo = array(
                'order_id'=>$order_id,
                'order_status'=>$orderInfo['order_status'],
                'shipping_status'=>$orderInfo['send_good_status'],
                'pay_status'=>$orderInfo['order_pay_status'],
                'create_user'=>$_SESSION['userName'],
                'create_time'=>date("Y-m-d H:i:s"),
                'remark'=>'订单修改发票信息'
            );
            //修改后如果发票里有信息就修改


            //写入订单日志
            $orderInfoModel->addOrderAction($logInfo);
            $result['success'] = 1;
            $result['_cls'] = $_cls;
            $result['tab_id'] = $tab_id;
            $result['title'] = '修改成功';
        }
        else
        {
            $result['error'] = '修改失败';
        }
        Util::jsonExit($result);
    }

    //获取订单价格 用于自动添加到发票价格
    public function getOrderPrice(){
        $result = array('success' => 0,'error' =>'');
        $_cls = _Post::getInt('_cls');
        $tab_id = _Post::getInt('tab_id');
        $id = _Post::getInt('id');
        //$is_invoice = _Request::getInt('is_invoice');
        $order_id = _Request::getInt('order_id');
        if(empty($order_id)){
            $result['error'] =  "数据有误！";
            Util::jsonExit($result);
        }

        //判断订单是否有效
        $orderModel= new BaseOrderInfoModel(27);
        $order_info = $orderModel->getOrderInfoById($order_id);
        if(empty($order_info)){
            $result['error'] = "此订单的数据不存在！";
            Util::jsonExit($result);
        }
        if($order_info['is_delete']==1){
            $result['error'] = "此订单已删除！";
            Util::jsonExit($result);
        }
        //获取订单金额
        $AccountInfo = $orderModel->getOrderAccount($order_id);
        if($AccountInfo['order_amount']){
            $result['error'] = $AccountInfo['order_amount'];
            $result['success'] = 1;
        }else{
            $result['error'] = "删除失败";
        }
        //var_dump($order_price);exit;
        Util::jsonExit($result);
    }

    /**
     *	delete，删除
     */
    public function delete ($params)
    {
        $result = array('success' => 0,'error' => '');
        $id = intval($params['id']);
        $model = new AppOrderInvoiceModel($id,28);
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