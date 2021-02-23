<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseInvoiceInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-28 10:27:45
 *   @update	:
 *  -------------------------------------------------
 */
class BaseInvoiceInfoController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('base_invoice_info_search_form.html',array('bar'=>Auth::getBar()));
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
            'invoice_num'=>  _Request::getString('invoice_num'),
            'title'=>  _Request::getString('title'),
            'order_sn'=>  _Request::getString('order_sn'),
            'status'=>  _Request::getInt('status'),
            'type'=>  _Request::getInt('type'),
            'price_start'=>  _Request::getInt('price_start'),
            'price_end'=>  _Request::getInt('price_end'),

		);
		$page = _Request::getInt("page",1);
		$where = array();
        $where['invoice_num'] = $args['invoice_num'];
        $where['title'] = $args['title'];
        $where['order_sn'] = $args['order_sn'];
        $where['status'] = $args['status'];
        $where['type'] = $args['type'];
        $where['price_start'] = $args['price_start'];
        $where['price_end'] = $args['price_end'];

		$model = new BaseInvoiceInfoModel(29);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'base_invoice_info_search_page';
		$this->render('base_invoice_info_search_list.html',array(
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
		$result['content'] = $this->fetch('base_invoice_info_info.html',array(
			'view'=>new BaseInvoiceInfoView(new BaseInvoiceInfoModel(29))
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
		$result['content'] = $this->fetch('base_invoice_info_info.html',array(
			'view'=>new BaseInvoiceInfoView(new BaseInvoiceInfoModel($id,29))
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
		$this->render('base_invoice_info_show.html',array(
			'view'=>new BaseInvoiceInfoView(new BaseInvoiceInfoModel($id,29)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$invoice_num = _Post::getFloat('invoice_num');
		$price = _Post::getFloat('price');
		$title = _Post::getString('title');
        $content = _Post::getString('content');
        $type = _Post::getInt('type');
        $order_sn = _Post::getString('order_sn');
        if(!preg_match("/^[0-9]*$/",$order_sn)){
            $result['error'] = '订单号不规范！';
            Util::jsonExit($result);
        }
        if($price < 0){
            $result['error'] = '输入的金额不规范！';
            Util::jsonExit($result);
        }
        if(empty($price)){
            $result['error'] = '发票金额不能为空';
            Util::jsonExit($result);
        }
        if(empty($title)){
            $result['error'] = '抬头不能为空';
            Util::jsonExit($result);
        }
        if(empty($content)){
            $result['error'] = '内容不能为空';
            Util::jsonExit($result);
        }
        if(empty($order_sn)){
            $result['error'] = '订单号不能为空';
            Util::jsonExit($result);
        }

		//对客订单进行验证
		if($type == 1){
			$apiOrderModel=new ApiOrderModel();
			$orderInfo=$apiOrderModel->GetOrderInfoInvoiceBySn($order_sn);
			if(empty($orderInfo)){
				$result['error'] = '订单号不存在';
				Util::jsonExit($result);
			}
			
			if($orderInfo['info']['order_status']!=2){
				$result['error'] = '未审核订单号不能开发票';
				Util::jsonExit($result);
			}
			if(!empty($orderInfo['fapiao'])){
				foreach($orderInfo['fapiao'] as $fapiao){
					if($fapiao['is_invoice']==0){
						$result['error'] = '订单不需要开发票,请确认订单信息';
						Util::jsonExit($result);
					}
					/*
					if($fapiao['invoice_num']!=''){
						$result['error'] = '订单已经开发票,请确认订单信息';
						Util::jsonExit($result);
					}*/
				}
			}
		}


		$olddo = array();
		$newdo=array();
        $newdo['invoice_num'] = $invoice_num;
        $newdo['price'] = $price;
        $newdo['title'] = $title;
        $newdo['content'] = $content;
        $newdo['type'] = $type;
        $newdo['order_sn'] = $order_sn;
        $newdo['status'] = 1;
        $newdo['create_user'] = $_SESSION['userName'];
        $newdo['create_time'] = date("Y-m-d H:i:s");

		$newmodel =  new BaseInvoiceInfoModel(30);
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
		$invoice_num = _Post::getString('invoice_num');
		$price = _Post::getFloat('price');
		$title = _Post::getString('title');
        $content = _Post::getString('content');
        $type = _Post::getInt('type');
        $order_sn = _Post::getString('order_sn');
        if(!preg_match("/^[0-9]*$/",$order_sn)){
            $result['error'] = '订单号不规范！';
            Util::jsonExit($result);
        }
        if($price < 0){
            $result['error'] = '输入的金额不规范！';
            Util::jsonExit($result);
        }
        if(empty($price)){
            $result['error'] = '发票金额不能为空';
            Util::jsonExit($result);
        }
        if(empty($title)){
            $result['error'] = '抬头不能为空';
            Util::jsonExit($result);
        }
        if(empty($content)){
            $result['error'] = '内容不能为空';
            Util::jsonExit($result);
        }
        if(empty($order_sn)){
            $result['error'] = '订单号不能为空';
            Util::jsonExit($result);
        }

		$newmodel =  new BaseInvoiceInfoModel($id,30);
		$olddo = $newmodel->getDataObject();

		if($olddo['type'] != $type){
			$result['error'] = '订单类型不能修改,请作废发票.';
			Util::jsonExit($result);
		}

		if($type == 1){
			$changeInvoiceNum=false;
			$apiOrderModel=new ApiOrderModel();
			if($olddo['order_sn'] != $order_sn ){
				$result['error'] = '订单号不能修改,请作废发票.';
				Util::jsonExit($result);
			}
				
			//新订单验证
			$orderInfo=$apiOrderModel->GetOrderInfoInvoiceBySn($order_sn);
			if(empty($orderInfo)){
				$result['error'] = '订单号不存在';
				Util::jsonExit($result);
			}
			
			if($orderInfo['info']['order_status']!=2){
				$result['error'] = '未审核订单号不能开发票';
				Util::jsonExit($result);
			}
			$order_total_fee=$orderInfo['info']['money_paid']+$orderInfo['info']['money_unpaid'];
			if(!empty($orderInfo['fapiao'])){
				foreach($orderInfo['fapiao'] as $fapiao){
					if($fapiao['is_invoice']==0){
						$result['error'] = '订单不需要开发票,请确认订单信息';
						Util::jsonExit($result);
					}
					//可以修改发票金额
					if($price>$order_total_fee){
						$result['error'] = '订单发票金额不能大于订单金额,请确认订单信息';
						Util::jsonExit($result);
					}
					//修改发票
					if($fapiao['invoice_num']!=$invoice_num){
						$changeInvoiceNum=true;
						$newmodel =  new BaseInvoiceInfoModel($id,30);
						$invoiceInfo = $newmodel->getInvoiceByInvoiceNum($invoice_num);
						if($invoiceInfo && $fapiao['invoice_amount']==$price){
							$result['error'] = '发票在其他订单使用,请确认订单信息';
							Util::jsonExit($result);
						}
					}
					if($fapiao['invoice_amount']!=$price){
						$changeInvoiceNum=true;
					}
				}
			}
			if($changeInvoiceNum){
				$apiOrderModel=new ApiOrderModel();
				$order_id=$orderInfo['info']['id'];
				$updatedata=array();
				$updatedata['invoice_num']=$invoice_num;
				$updatedata['invoice_status']=2;
				$updatedata['invoice_amount']=$price;
				$apiOrderModel->updateOrderInfoInvoiceByid($order_id,$updatedata);
			}
		}

		

		$newdo=array();
        $newdo['id'] = $id;
        $newdo['invoice_num'] = $invoice_num;
        $newdo['price'] = $price;
        $newdo['title'] = $title;
        $newdo['content'] = $content;
        $newdo['type'] = $type;
        $newdo['order_sn'] = $order_sn;

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
	 *	cancel，作废
	 */
	public function cancel ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new BaseInvoiceInfoModel($id,30);
		$olddo = $model->getDataObject();
		$price=$olddo['price'];

		
		if($olddo['status'] == 3){
			$result['error'] = "发票状态现在是已作废";
			Util::jsonExit($result);
		}
		if($olddo['type']==1){
			$apiOrderModel=new ApiOrderModel();
			//新订单验证
			$orderInfo=$apiOrderModel->GetOrderInfoInvoiceBySn($olddo['order_sn']);
			if(!empty($orderInfo)){
				$order_id=$orderInfo['info']['id'];
				$updatedata=array();
				$updatedata['invoice_status'] = 1;
				$updatedata['invoice_num'] = '';
				$ret=$apiOrderModel->updateOrderInfoInvoiceByid($order_id,$updatedata);
			}
		}
		$model->setValue('status',3);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "作废失败";
		}
		Util::jsonExit($result);
	}



    public function getOrderjiage(){
        $goods_sn =_Request::get('goods_sn');
        $model = new BaseInvoiceInfoModel(30);
        $res =  $model->getOrderjiage($goods_sn);
      if($res===false){
          $result['success'] = 0;
      }else{
        $result['success'] = $res;
      }
        Util::jsonExit($result);
    }

}

?>