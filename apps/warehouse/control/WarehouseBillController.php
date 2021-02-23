<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 11:04:15
 *   @update	:
 *  仓储管理-仓储单据-单据查询
 *  -------------------------------------------------
 */
class WarehouseBillController extends CommonController
{
	protected $smartyDebugEnabled = true;
	protected $whitelist = array('search','printcode','printHunbohui','downPfBill','download_detail','printCodeNew','download_chengben','download_sale');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{

		$args = array(
				'mod'				=> _Request::get("mod"),
				'con'				=> substr(__CLASS__, 0, -10),
				'act'				=> __FUNCTION__,
				'bill_type'			=> _Request::get("bill_type"),		
				'bill_status'		=> _Request::get("bill_status"),			
				'create_user'		=> _Request::get("create_user"),
				'block'		=> _Request::get("block"),
								
		);	
		//批发客户
		$wholesModel = new JxcWholesaleModel(21);
		$whoList = array();
		if($_SESSION['companyId']==58)
		    $whoList = $wholesModel->select2(' `wholesale_id` , `wholesale_sn` , `wholesale_name` ' ,  " `wholesale_status` = 1 " , 'all');
		else{
			$company_model = new CompanyModel(1);
			$sd_sub_company_id = $company_model->select2("id","sd_company_id='{$_SESSION['companyId']}'",1);
		    if($sd_sub_company_id){
		    	$sd_sub_company_id = array_column($sd_sub_company_id,'id');
		    	$whoList = $wholesModel->select2(' `wholesale_id` , `wholesale_sn` , `wholesale_name` ' ,  " `wholesale_status` = 1 and sign_company in (".implode(',',$sd_sub_company_id).")" , 'all');
		    }		    
		}
		//从单据统计进来	
		if($args['block']==1){
			$where = array(
					'bill_no'			=> '',
					'goods_sn'			=> '',
					'send_goods_sn'		=> '',
					'bill_type'			=> $args['bill_type'],
					'order_sn'			=> '',
					'bill_status'		=> $args['bill_status'],
					'from_company_id'	=> '',
					'to_company_id'		=> '',
					'to_warehouse_id'	=> '',
					'goods_id'			=> '',
					'processors'		=> '',
					'create_user'		=> $args['create_user'],
					'check_time_start'  => '',
					'check_time_end'    => '',
					'time_start'		=> '',
					'time_end'			=> '',
					'bill_note'			=> '',
					'account_type'      => '',
					'mohao'             => '',
					'put_in_type'       => ''			
			);
			$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
			$model = new WarehouseBillModel(21);
			$data = $model->pageList($where,$page,10,false);
			$pageData = $data;
			
			$pageData['filter'] = $args;
			$pageData['jsFuncs'] = 'warehouse_bill_search_page';
			
	
			$view = new WarehouseBillView($model);
	                //供应商
			$model_p = new ApiProModel();
			$pro_list = $model_p->GetSupplierList(array('status'=>1));
			//print_r($data);exit;
			$pay_list=$model->getPayList();
			$this->render('warehouse_bill_search_form.html',array(
				'bar'=>Auth::getBar(),
	            'view'=>$view,
	            'pro_list' => $pro_list,
				'pa'=>Util::page($pageData),
				'page_list'=>$data,
				'is_goods_id'=>$where['goods_id'],
				'is_goods_sn'=>$where['goods_sn'],				
				'dd'=> new DictView(new DictModel(1)),
				'whoList'=>$whoList,	
				'args' => $args,
				'pay_list'=> $pay_list	
					
			));
		
		}else{
			$model = new WarehouseBillModel(21);
			$view = new WarehouseBillView($model);
			//供应商
			$model_p = new ApiProModel();
			if($_SESSION['companyId']==58)
			    $pro_list = $model_p->GetSupplierList(array('status'=>1));
			else
				$pro_list = array();
			$model = new WarehouseBillModel(21);
			$pay_list=$model->getPayList();
			$this->render('warehouse_bill_search_form.html',array(
					'bar'=>Auth::getBar(),
					'view'=>$view,
					'whoList'=>$whoList,	
					'pro_list' => $pro_list,
					'pay_list'=> $pay_list	
			));
		}
	
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		ini_set('memory_limit','-1');
		set_time_limit(0);
		$args = array(
			'mod'				=> _Request::get("mod"),
			'con'				=> substr(__CLASS__, 0, -10),
			'act'				=> __FUNCTION__,
			'bill_no'			=> trim(_Request::get("bill_no")),
			'goods_sn'			=> trim(_Request::get("goods_sn")),
			'send_goods_sn'	    => trim(_Request::get("send_goods_sn")),
			'bill_type'			=> _Request::get("bill_type"),
			'order_sn'			=> trim(_Request::get("order_sn")),
			'bill_status'		=> _Request::get("bill_status"),
			'from_company_id'	=> _Request::getInt("from_company_id"),
			'to_company_id'		=> _Request::getInt("to_company_id"),
			'to_warehouse_id'	=> _Request::getInt("to_warehouse_id"),
			'down_info'			=> _Request::get('down_info')?_Request::get('down_info'):'',
			'goods_id'			=> _Request::getString("goods_id"),
			'processors'		=> _Request::get("processors"),
			'create_user'		=> _Request::get("create_user"),
			'check_time_start'  => _Request::get("check_time_start"),
			'check_time_end'    => _Request::get("check_time_end"),
			'time_start'		=> _Request::get("time_start"),
			'time_end'			=> _Request::get("time_end"),
			'bill_note'			=> _Request::get("bill_note"),
            'account_type'      => _Request::get("account_type"),
            'mohao'             => _Request::get("mohao"),
            'put_in_type'       => _Request::get('put_in_type'),
			'confirm_delivery'	=> _Request::get('confirm_delivery'),
			'p_order_sn'	=> _Request::get('p_order_sn'),
			'is_tsyd'	=> _Request::get('is_tsyd'),
			'to_customer_id'	=> _Request::get('to_customer_id'),
            'company_id_list' => '',
            'out_warehouse_type'    => _Request::get('out_warehouse_type'),
            'dep_settlement_type'    => _Request::get('dep_settlement_type'),
            'settlement_time_start'    => _Request::get('settlement_time_start'),
            'settlement_time_end'    => _Request::get('settlement_time_end'),
            'chuku_type'    => _Request::get('chuku_type'),
            'pay_id'    => _Request::get('pay_id'),
            'fin_check_status'		=> _Request::get("fin_check_status"),
            'jiejia'		=> _Request::get("jiejia"),
            'fin_check_time_start'  => _Request::get("fin_check_time_start"),
            'fin_check_time_end'    => _Request::get("fin_check_time_end"),
            'bc_sn'    => _Request::get("bc_sn")
		);
        $is_company_check = Auth::user_is_from_base_company();
		if(!$is_company_check && SYS_SCOPE == 'zhanting'){
			$args['company_id_list'] = $_SESSION['companyId'];
			if (empty($_SESSION['companyId']) || in_array($_SESSION['companyId'], array('0', '-1'))) {
				echo '找不到您的归属公司，无法进行下一步操作，请确认您的公司列表';
				exit;
			}
		}


		$where = array(
			'bill_no'			=> $args['bill_no'],
			'goods_sn'			=> $args['goods_sn'],
			'send_goods_sn'		=> $args['send_goods_sn'],
			'bill_type'			=> $args['bill_type'],
			'order_sn'			=> $args['order_sn'],
			'bill_status'		=> $args['bill_status'],
			'from_company_id'	=> $args['from_company_id'],
			'to_company_id'		=> $args['to_company_id'],
			'to_warehouse_id'	=> $args['to_warehouse_id'],
			'goods_id'			=> $args['goods_id'],
			'processors'		=> $args['processors'],
			'create_user'		=> $args['create_user'],
			'check_time_start'  => $args['check_time_start'],
			'check_time_end'    => $args['check_time_end'],
			'time_start'		=> $args['time_start'],
			'time_end'			=> $args['time_end'],
			'bill_note'			=> $args['bill_note'],
            'account_type'      => $args["account_type"],
            'mohao'             => $args["mohao"],
            'put_in_type'       => $args['put_in_type'],
			'confirm_delivery'       => $args['confirm_delivery'],
			'p_order_sn'       => $args['p_order_sn'],
			'is_tsyd'       => $args['is_tsyd'],
			'to_customer_id'       => $args['to_customer_id'],
			'company_id_list' => $args['company_id_list'],
            'out_warehouse_type' => $args['out_warehouse_type'],
            'dep_settlement_type' => $args['dep_settlement_type'],
            'settlement_time_start' => $args['settlement_time_start'],
            'settlement_time_end' => $args['settlement_time_end'],
            'chuku_type' => $args['chuku_type'],
            'pay_id'    => $args['pay_id'],
            'fin_check_status'		=> $args['fin_check_status'],
            'jiejia'		=> $args['jiejia'],
            'fin_check_time_start'  => $args['fin_check_time_start'],
            'fin_check_time_end'    => $args['fin_check_time_end'],
            'bc_sn'    => $args['bc_sn'],
		);
        //二、H单权限管控
        //总公司的能看所有的H单，
        //经销商，个体，直营看出库公司是自己的H单，
        //省代能看出库公司是自己以及下属省代的H单（并且入库公司非总公司），
        $verify = $this->verifyUserLevel();
        $where['level'] = $verify['level'];//用户级别1.总公司 、2.经销商，个体，直营 、3.省代  
        $where['dataCompInfo'] = $verify['dataCompInfo'];//可查看公司
        $where['now_compid'] = $_SESSION['companyId'];//当前所在公司

		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		//$model = new WarehouseBillModel(21);
		$model = new WarehouseBillModel(55);//只读数据库
		$wholesModel = new JxcWholesaleModel(21);
        $whoList = $wholesModel->select2(' `wholesale_id`, `wholesale_name` '," 1 = 1 ", 'all');
        $wholesaleArr=array();
        foreach ($whoList as $v){
            $wholesaleArr[$v['wholesale_id']]=$v['wholesale_name'];
        }

        if(SYS_SCOPE == 'zhanting'){
            $where['hidden'] = '0';
        }
        //导出功能
        if($args['down_info']=='down_info'){
            $data = $model->pageList($where,$page,90000000,false);
            $this->download($data,$where['goods_id'],$wholesaleArr);
            exit;
        }elseif($args['down_info']=='detail_download'){
            $data = $model->getdetailList($where);
            $this->download_detail($data,$wholesaleArr);
            exit;
        }elseif($args['down_info']=='detail_download1'){
            $data = $model->getBillIds($where);
            $ids_arr=array_column($data, "id");
            $ids_str=implode(",", $ids_arr);
            $arrs['ids']=$ids_str;
            //print_r($arrs);exit;
            $this->downPfBill($arrs);
        }
        
		if(!empty($where['goods_id']) ||!empty($where['goods_sn'])){
			$data = $model->goodsBillList($where,$page,10,false,1);
		}else{			
			$data = $model->pageList($where,$page,10,false,1);
		}

        $bar = $this->get_detail_view_bar_new($model);
        $show_pifajia = $bar[0];
        $show_mingyichenggben =$bar[1];
        $show_caigou_price = $bar[2];
		$total_num =$data['total_num']?$data['total_num']:0;
		$total_price =$data['total_price']?$data['total_price']:0;
		$total_shijia =$data['total_shijia']?$data['total_shijia']:0;
        $show_private_data_zt = Auth::user_is_from_base_company();
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_bill_search_page';
		$this->render('warehouse_bill_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'is_goods_id'=>$where['goods_id'],
			'is_goods_sn'=>$where['goods_sn'],
			'view'=>new WarehouseBillView(new WarehouseBillModel(21)),
			'dd'=> new DictView(new DictModel(1)),
			'total_num'=>$total_num,
			'total_price'=>$total_price,
			'total_shijia'=>$total_shijia,
			'wholesaleArr'=>$wholesaleArr,	
			'isViewChengbenjia'=>$this->isViewChengbenjia(),	
            'show_caigou_price'=>$show_caigou_price,
            'userType'=>$_SESSION['userType'],
            'show_private_data_zt'=>$show_private_data_zt,
            'show_mingyichenggben'=>$show_mingyichenggben,
            'show_pifajia' => $show_pifajia
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('warehouse_bill_info.html',array(
			'view'=>new WarehouseBillView(new WarehouseBillModel(1))
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}
   

	public function delBill($params)
	{
		$result = array('success' => 0,'error' => '');
		$model = new WarehouseBillModel(22);
		$res = $model->delBill($params['id']);
		if($res)
		{
			$result['success'] = 1;
		}else{
			$result['error'] = "失败了";
		}
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('warehouse_bill_info.html',array(
			'view'=>new WarehouseBillView(new WarehouseBillModel($id,1))
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
		$c = new TestController();
		$c->index();exit;
		$this->render('warehouse_bill_show.html',array(
			'view'=>new WarehouseBillView(new WarehouseBillModel($id,1))
		));
	}


	public function checkList(){

		$result = array('success' => 0,'error' =>'');
		$user_name = $_SESSION['userName'];
		$bill_id = _Post::getInt('bill_id');
		$model = new WarehouseBillModel($bill_id,21);

		if($model->getValue('bill_status') != 1)
		{
			$result['error'] = '单据不是<span style="color: #ff0000">&nbsp;已保存&nbsp;</span>状态不允许编辑';
			Util::jsonExit($result);
		}
		$bill_type = $model->getValue('bill_type');
		if($model->getValue('create_user') != $user_name && $bill_type != 'L' && $bill_type != 'T')
		{
			//$result['error'] = '单据非本人不能编辑';
			//Util::jsonExit($result);
		}
		/*if($model->getValue('bill_type') == 'W')
		{
			$result['error'] = '盘点单不允许编辑';
			Util::jsonExit($result);
		}*/
		if($bill_type == 'S')
		{
			$result['error'] = '销售单不允许编辑';
			Util::jsonExit($result);
		}

		if($bill_type == 'H' &&  $model->getValue('company_from')=='ishop')
		{
			$result['error'] = '智慧门店同步过来的H单不允许编辑';
			Util::jsonExit($result);
		}		
		if($bill_type == 'D')
		{
			//$result['error'] = '销售退货单不允许编辑';
			//Util::jsonExit($result);
		}
		$result['success'] = 1;
		Util::jsonExit($result);
		echo $res;
	}

	/***********************************************************************************************
	fun:getOrderSnByOrderId
	description:通过订单号获取订单id
	************************************************************************************************/
	public function getOrderSnByOrderId ($params)
	{
		$result = array('success' => 0,'error' => '');
		$orderSn = $params;
		$res = ApiModel::sales_api('GetOrderInfoBySn',array('order_sn'),array($orderSn));
		//var_dump($res);exit;
		if ($res['error'])
		{
			$result['error'] = "订单号不存在";
			$result['id'] = '';
		}
		else
		{
			$result['success'] = 1;
			$result['id'] = $res['return_msg']['id'];
		}
		return $result;

	}

	//导出
	public function download($data,$goods_id,$wholesaleArr) {

		$dd =new DictModel(1);
		$salemodel = new SalesModel(51);
		$view = new WarehouseBillModel(21);

        $bar = $this->get_detail_view_bar_new($view);
        $show_pifajia = $bar[0];
        $show_mingyichenggben =$bar[1];
        $show_caigou_price = $bar[2];

		$show_private_data = SYS_SCOPE == 'boss' || Auth::user_is_from_base_company();
        $is_zt_show = SYS_SCOPE == 'zhanting';
        $show_private_data_zt = Auth::user_is_from_base_company();
		if($goods_id){
			if ($data['data']) {
				$down = $data['data'];
				$xls_content = "单据编号,单据类型,状态,货品数量,订单号,销售渠道,出库公司,入库公司,入库仓,供应商,原始成本价,加价成本价,销售价,送货单号,制单人,制单时间,审核人,审核时间,复核人,复核时间,批发客户,出库类型,类别,标识,备注\r\n";
				foreach ($down as $key => $val) {
					$val['from_company_name']?$val['from_company_name']:'无';
					$val['company_from']?$val['company_from']:'无';
					$val['to_company_name']?$val['to_company_name']:'无';
					$val['to_warehouse_name']?$val['to_warehouse_name']:'无';
					
					$xls_content .= $val['bill_no'] . ",";
					$xls_content .= $view->getBillType($val['bill_type']) . ",";
					$xls_content .= $dd->getEnum('warehouse_in_status',$val['bill_status']) . ",";
					$xls_content .= $val['num'] . ",";
					$xls_content .= $val['order_sn'] . ",";
					$xls_content .= $salemodel->getSalesChannelByOrderSn($val['order_sn']) . ",";		//销售渠道
					$xls_content .= $val['from_company_name'] . ",";
					$xls_content .= $val['to_company_name'] . ",";
					$xls_content .= $val['to_warehouse_name']. ",";
					$xls_content .= $val['pro_id'] . ",";/*$val['pro_name']*/
					//$xls_content .= ( $show_private_data ? $val['total_chengben'] : '--') . ",";
                    $total_chengben = '--';
                    if($show_private_data_zt || $this->checkBillHCaiGouJia($val['id']) == 1){
                        if($val['bill_type'] == 'P'){
                            if($show_caigou_price == false || $_SESSION['userType'] == 1){
                                $total_chengben = $val['total_chengben'];
                            }
                        }elseif($val['bill_type'] == 'H' && $this->checkBillHCaiGouJia($val['id']) == 1){
                            if($show_caigou_price == false || $_SESSION['userType'] == 1){
                                $total_chengben = $val['total_chengben'];
                            }
                        }else{
                            $total_chengben = $val['total_chengben'];
                        }
                    }
                    $xls_content .= $total_chengben. ",";
					$xls_content .= $val['goods_total_jiajia'] . ",";
                    $shijia = '--';
                    if(in_array($val['bill_type'],array('P','H'))){
                        if($show_pifajia == false || $_SESSION['userType'] == 1){
                            $shijia = $val['shijia'];
                        }
                    }else{
                        $shijia = $val['shijia']. ",";
                    }
					$xls_content .= $shijia . ",";
					$xls_content .= $val['send_goods_sn'] . ",";
					$xls_content .= $val['create_user'] . ",";
					$xls_content .= $val['create_time'] . ",";
					$xls_content .= $val['check_user'] . ",";
					$xls_content .= $val['check_time'] . ",";
					$xls_content .= $val['fin_check_user'] . ",";
					$xls_content .= $val['fin_check_time'] . ",";					
                    $xls_content .= isset($wholesaleArr[$val['to_customer_id']])?$wholesaleArr[$val['to_customer_id']].",":'' . ",";
                    $xls_content .= ($val['bill_type'] == 'P' && $is_zt_show ? $dd->getEnum('warehouse.out_warehouse_type',$val['out_warehouse_type']) : '--') . ",";
                    $xls_content .= ($val['bill_type'] == 'P' && $is_zt_show ? $val['p_type'] : '--') . ",";
                    $xls_content .= $val['company_id_from'] . ",";
                    $xls_content .= str_replace(PHP_EOL, '',$val['bill_note']) . "\n";


				}
			} else {
				$xls_content = '没有数据！';
			}

		}else{

			if ($data['data']) {
				$down = $data['data'];
				$xls_content = "单据编号,单据类型,状态,货品数量,订单号,销售渠道,出库公司,入库公司,入库仓,供应商,原始成本价,加价成本价,销售价,送货单号,制单人,制单时间,审核人,审核时间,复核人,复核时间,批发客户,出库类型,类别,是否结算,标识,备注\r\n";
				foreach ($down as $key => $val) {
					empty($val['from_company_name'])?$val['from_company_name']:'无';
					empty($val['to_company_name'])?$val['to_company_name']:'无';
					empty($val['to_warehouse_name'])?$val['to_warehouse_name']:'无';
					$xls_content .= $val['bill_no']. ",";
					$xls_content .= $view->getBillType($val['bill_type']) . ",";
					$xls_content .= $dd->getEnum('warehouse_in_status',$val['bill_status']) . ",";
					$xls_content .= $val['goods_num']. ",";
					$xls_content .= $val['order_sn'] . ",";
					$xls_content .= $salemodel->getSalesChannelByOrderSn($val['order_sn']) . ",";		//销售渠道
					$xls_content .= $val['from_company_name'] . ",";
					$xls_content .= $val['to_company_name'] . ",";
					$xls_content .= $val['to_warehouse_name']. ",";
					$xls_content .= $val['pro_id'] . ",";/*$val['pro_name']*/
					$total_chengben = '--';
                    if($show_private_data_zt ||$this->checkBillHCaiGouJia($val['id']) == 1){
                        if($val['bill_type'] == 'P'){
                            if($show_caigou_price == false || $_SESSION['userType'] == 1){
                                $total_chengben = $val['total_chengben'];
                            }
                        }elseif($val['bill_type'] == 'H' && $this->checkBillHCaiGouJia($val['id']) == 1){
                            if($show_caigou_price == false || $_SESSION['userType'] == 1){
                                $total_chengben = $val['total_chengben'];
                            }
                        }else{
                            $total_chengben = $val['total_chengben'];
                        }
                    }
                    $xls_content .= $total_chengben. ",";
                    $xls_content .= $val['goods_total_jiajia'] . ",";
                    $shijia = '--';
                    if(in_array($val['bill_type'],array('P','H'))){
                        if($show_pifajia == false || $_SESSION['userType'] == 1){
                            $shijia = $val['shijia'];
                        }
                    }else{
                        $shijia = $val['shijia'];
                    }
                    $xls_content .= $shijia . ",";
					$xls_content .= $val['send_goods_sn'] . ",";
					$xls_content .= $val['create_user'] . ",";
					$xls_content .= $val['create_time'] . ",";
					$xls_content .= $val['check_user'] . ",";
					$xls_content .= $val['check_time'] . ",";
					$xls_content .= $val['fin_check_user'] . ",";
					$xls_content .= $val['fin_check_time'] . ",";					
                    $xls_content .= isset($wholesaleArr[$val['to_customer_id']])?$wholesaleArr[$val['to_customer_id']].",":"" . ",";
                    $xls_content .= ($val['bill_type'] == 'P' && $is_zt_show ? $dd->getEnum('warehouse.out_warehouse_type',$val['out_warehouse_type']) : '--') . ",";
                    $xls_content .= ($val['bill_type'] == 'P' && $is_zt_show ? $val['p_type'] : '--') . ",";
					//$xls_content .= preg_replace('~[^\p{Han}]~u', '', $val['bill_note']) . "\n";
                    $xls_content .= ($val['fin_check_status'] == 2 ? "是":"否"). ",";
                    $xls_content .= $val['company_id_from'] . ",";
                    $xls_content .= str_replace(PHP_EOL, '',$val['bill_note'])  . "\n";
				}
			} else {
				$xls_content = '没有数据！';
			}

		}

		header("Content-type: text/html; charset=gbk");
		header("Content-type:aplication/vnd.ms-excel");
		header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv");
		echo iconv("utf-8", "GB18030", $xls_content);

	}

	
	//导出
	public function download_detail($data,$wholesaleArr) {
	
		$dd =new DictModel(1);
		$salemodel = new SalesModel(51);
		$view = new WarehouseBillModel(21);
        $model = new WarehouseBillGoodsModel(21);
        $styleModel = new SelfStyleModel(11);
        $bar = $this->get_detail_view_bar_new($model);
        $show_pifajia = $bar[0];
        $show_mingyichenggben =$bar[1];
        $show_caigou_price = $bar[2];
	    $BillTypeArr=array(
	    		'S'=>'销售单',
	    		'B'=>'退货返厂单',
	    		'M'=>'调拨单',
	    		'E'=>'损益单',
	    		'C'=>'其他出库单',
	    		'W'=>'盘点单',
	    		'T'=>'其他收货单',
	    		'L'=>'收货单',
	    		'D'=>'销售退货单',
	    		'O'=>'维修退货单',
	    		'P'=>'批发销售单',
	    		'R'=>'维修发货单',
	    		'WF'=>'维修调拨单',
	    		'H'=>'批发退货单'
	    		
	    );
	    $startArr=array(
	    		1=>'已保存',
	    		2=>'已审核',
	    		3=>'已取消',
                4=>'已签收'
	    );

        $dep_settlement_type=array(
                1=>'未结算',
                2=>'已结算',
                3=>'已退货',
                ''=>''
            );
        $put_in_type_arr = array();
        $put_in_type = $dd->getEnumArray('warehouse.put_in_type');
        foreach ($put_in_type as $key => $value) {
            $put_in_type_arr[$value['name']]=$value['label'];
        }
		   //print_r($data);exit;
        $userType = $_SESSION['userType'];
        $is_zt_show = SYS_SCOPE == 'zhanting';
        $show_private_data_zt = Auth::user_is_from_base_company();
        $show_private_data = SYS_SCOPE == 'boss' || Auth::user_is_from_base_company();
			if ($data) {
				$xls_content = "批发客户,体验店名称,单据编号,单据类型,审核状态,出库公司,入库公司,入库仓,货号,款号,名称,款式分类,产品线,数量,货重,金重,证书类型,证书号,主石,主石数,主石重,主石颜色,主石净度,辅石数,辅石重,主成色,指圈,单据供应商,货品供应商,原始成本价,名义成本价,销售价,制单人,制单时间,审核人,审核时间,复核人,复核时间,备注,门店结算方式,结算操作时间,管理费,出库类型,类别,展厅标签价,布产单,标准金重范围,供应商货品条码,历史金重范围,证书费用,18K工费,PT950工费,基础工费,模号,订单号,主石条码,标识,入库方式\r\n";
				foreach ($data as $key => $val) {
                    //出库类型根据最后一个已审核的批发销售单的出库类型获取
                    if($val['bill_type'] == 'H'){
                        $val['out_warehouse_type'] = $model->getOutWarehouseTypeByGoodsId($val['goods_id']);
                    }
                    //出库类型
                    if($val['out_warehouse_type'] == 1){
                        $val['out_warehouse_type'] = '购买';
                    }elseif($val['out_warehouse_type'] == 2){
                        $val['out_warehouse_type'] = '借货';
                    }else{
                        $val['out_warehouse_type'] = '';
                    }

                    //根据款号取工费
                    $stylefee = $styleModel->getStyleWageFee($val['goods_sn']);
                    $fee_18k = $fee_pt950 = '';
                    if(!empty($stylefee)){
                        $fee_18k = isset($stylefee[1]['price'])?$stylefee[1]['price']:'';
                        $fee_pt950 = isset($stylefee[4]['price'])?$stylefee[4]['price']:'';
                    }

					$xls_content .= isset($wholesaleArr[$val['to_customer_id']])?$wholesaleArr[$val['to_customer_id']].",":"" . ",";
					$xls_content .= isset($wholesaleArr[$val['to_customer_id']])?$wholesaleArr[$val['to_customer_id']].",":"" . ",";
					$xls_content .= $val['bill_no']. ",";
					$xls_content .= $BillTypeArr[$val['bill_type']] . ",";
					$xls_content .= $startArr[$val['bill_status']] . ",";
					$xls_content .= $val['from_company_name']. ",";
					$xls_content .= $val['to_company_name']. ",";
					$xls_content .= $val['to_warehouse_name']. ",";
					$xls_content .= $val['goods_id']. ",";
					$xls_content .= $val['goods_sn'] . ",";
					$xls_content .= $val['goods_name'] . ",";
					$xls_content .= $val['cat_type1'] . ",";
					$xls_content .= $val['product_type1'] . ",";
					$xls_content .= $val['num'] . ",";
					$xls_content .= $val['zongzhong'] . ",";
					$xls_content .= $val['jinzhong'] . ",";
                    $xls_content .= $val['zhengshuleibie'] . ",";
                    $xls_content .= $val['zhengshuhao'] . ",";
					$xls_content .= $val['zhushi'] . ",";
					$xls_content .= $val['zhushilishu'] . ",";
					$xls_content .= $val['zuanshidaxiao'] . ",";
					$xls_content .= $val['zhushiyanse'] . ",";
					$xls_content .= $val['zhushijingdu'] . ",";
					$xls_content .= $val['fushilishu'] . ",";
					$xls_content .= $val['fushizhong'] . ",";
					$xls_content .= $val['caizhi'] . ",";
					$xls_content .= $val['shoucun'] . ",";
					$xls_content .= ($show_private_data ? $val['pro_id']:"--") . ",";
					$xls_content .= ($show_private_data ? $val['prc_id']:"--") . ",";/*$val['prc_name']*/
                    $yuanshichengbenjia = '————';
                    if($show_private_data_zt || $this->checkBillHCaiGouJia($val['id']) == 1){
                        if($val['bill_type'] == 'H' && $this->checkBillHCaiGouJia($val['id']) == 1){
                            if($show_caigou_price==false || $userType == 1){
                                $yuanshichengbenjia  = $val['yuanshichengbenjia'];
                            }
                        }elseif($val['bill_type'] == 'P'){
                            if($show_caigou_price==false || $userType == 1){
                                $yuanshichengbenjia  = $val['yuanshichengbenjia'];
                            }
                        }else{
                            $yuanshichengbenjia  = $val['yuanshichengbenjia'];
                        }
                    }
                    $xls_content .= $yuanshichengbenjia. ",";
                    $mingyichengben = '————';
                    if($show_private_data_zt){
                        if($show_mingyichenggben==false || $userType == 1){
                            $mingyichengben = $val['mingyichengben'];
                        }
                    }
                    $xls_content .= $mingyichengben. ",";
                    $shijia = "--";
                    if(in_array($val['bill_type'],array('P','H'))){
                        if($show_pifajia == false || $_SESSION['userType'] == 1){
                            $shijia = $val['shijia'];
                        }
                    }else{
                        $shijia = $val['shijia'];
                    }
                    $xls_content .= $shijia . ",";				
					$xls_content .= $val['create_user'] . ",";
					$xls_content .= $val['create_time'] . ",";
					$xls_content .= $val['check_user'] . ",";
					$xls_content .= $val['check_time'] . ",";
					$xls_content .= $val['fin_check_user'] . ",";
					$xls_content .= $val['fin_check_time'] . ",";					
					$xls_content .= str_replace(PHP_EOL, '',$val['bill_note'])  . ",";	
                    $xls_content .= $dep_settlement_type[$val['dep_settlement_type']]  . ",";
                    $xls_content .= $val['settlement_time']  . ",";		
                    $xls_content .= ($val['bill_type']=='P' ? $val['management_fee']:'--')  . ",";
                    $xls_content .= ($is_zt_show ? $val['out_warehouse_type'] : '--'). ",";
                    $xls_content .= ($is_zt_show && $val['bill_type']=='P' ? $val['p_type'] : '--'). ",";
                    $xls_content .= ($is_zt_show ? $val['label_price']:'--') .",";
                    $xls_content .= $val['buchan_sn'] . ",";
                    $xls_content .= $val['biaozhun_jinzhong'] . ",";
                    $xls_content .= $val['supplier_code'] . ",";
                    $xls_content .= $val['lishi_jinzhong'] . ",";
                    if(!in_array($val['bill_type'], array('P','H'))){
                        $val['certificate_fee'] = '--';
                    }
                    $xls_content .= $val['certificate_fee'] . ",";
                    $xls_content .= $fee_18k . ",";
                    $xls_content .= $fee_pt950 . ",";
                    $xls_content .= $val['operations_fee'] . ",";
                    $xls_content .= $val['mo_sn'] . ",";
                    $xls_content .= $val['order_sn'] . ",";
                    $xls_content .= $val['zhushitiaoma'] . ",";
                    $xls_content .= $val['company_id_from'] . ",";
                    $put_in_type = isset($put_in_type_arr[$val['put_in_type']])?$put_in_type_arr[$val['put_in_type']]:"";
                    $xls_content .= $put_in_type . "\n";
				}
			} else {
				$xls_content = '没有数据！';
			}
			// 临时解除内存限制：
			ini_set('memory_limit', -1);
			set_time_limit(0);
			ob_end_clean();
    		header("Content-type: text/html; charset=gbk");
    		header("Content-type:aplication/vnd.ms-excel");
    		header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv");
    		echo iconv("utf-8", "GB18030", $xls_content);
	
	}
	
	/**
	 * 批量复制货号
	 */
	public function batchCopyGoods_id(){
		$bill_id = _Post::getInt('bill_id');
		$sql = "SELECT `id`,`goods_id` FROM `warehouse_bill_goods` WHERE `bill_id` = '".$bill_id."'";
		$model = new WarehouseBillModel(21);
		$data = $model->db()->getAll($sql);
		if(empty($data) || !$data){
			echo 0;
		}else{
			$data = array_column($data,'goods_id','id');
			$str = '';
			foreach ($data as $g) {
				$str .= $g."\r\n";
			}
			$str = rtrim($str,"\r\n");
			echo $str;
		}
	}
        //打印婚博会明晰
        public function printHunbohui(){
            //获取bill_id单据id
            $id = _Request::get('id');
            //数字词典
            $model = new WarehouseBillModel($id,21);

			$data  = $model->getDataObject();
            $newmodel = new WarehouseBillInfoMModel(21);
          //  $billinfo = $newmodel->getBillGoogsList($id,'row');

           // foreach($billinfo as $key=>$val){
         //           $data[$key]=$val;
          //  }

            //货品详情
            $goods_info = $model->getDetail($id);
            //获取加工商支付信息
            $amount=0;
            $BillPay = $model->getBillPay($id);
            foreach($BillPay as $val){
                    $amount +=$val['amount'];
            }
            //计算销售价总计 成本价总计 商品数量
            $fushizhong=0;
            $jinzhong=0;
            $zuanshidaxiao=0;
            //统计 副石重 金重
            foreach($goods_info as $key=>$val){
                    $goods_id[] = substr($val['goods_id'], -1,1);
                    //获取图片 拼接进数组
                    $gallerymodel = new ApiStyleModel();
                    $gallery_data = $gallerymodel->getProductGallery($val['goods_sn'],1);
                    if(isset($gallery_data['thumb_img'])){
                            $goods_info[$key]['goods_img']=$gallery_data['thumb_img'];
                    }else{
                            $goods_info[$key]['goods_img']='';
                    }

                    $fushizhong +=$val['fushizhong'];
                    $jinzhong +=$val['jinzhong'];
                    $zuanshidaxiao +=$val['zuanshidaxiao'];
                    $data['cat_type'] = $val['cat_type'];
            }
            array_multisort($goods_id, SORT_ASC, $goods_info);

            $this->render('print_hunbohui_detail.html', array(
				'data' => $data,
				'goods_info' => $goods_info,
				'fushizhong' => $fushizhong,
				'jinzhong' => $jinzhong,
				'zuanshidaxiao' => $zuanshidaxiao,
				'BillPay' => $BillPay,
				'amount' => $amount
		));
        }

		//打印条码
	public function printcode() {
		$bill_id = _Request::get('bill_id');
		$bill_type = _Request::get('bill_type');
        //$policy_type =_Request::get('policy_type');
       
		$dd =new DictModel(1);
		$newmodel = new WarehouseBillModel(21);
		$codes =$newmodel->getGoodsIdinfoByBillId($bill_id);
		$show_private_data = SYS_SCOPE == 'boss' || Auth::user_is_from_base_company();
		if ($codes) {
			$down = $codes;
			 $xls_content = "货号,款号,入库方式,基因码,手寸,长度,主石粒数,主石重,副石粒数,副石重,加工商编号,总重,净度,颜色,证书号,证书类别,主石切工,标签备注,主石,副石,主成色,饰品分类,款式分类,名称,石3副石,石3粒数,石3重,石4副石,石4粒数,石4重,石5副石,石5粒数,石5重,主成色重,副成色,副成色重,买入工费,计价工费,加价率,最新零售价,模号,品牌,证书数量,配件数量,时尚款,系列,属性,类别,成本价,入库日期,加价率代码,主石粒重,副石粒重,标签手寸,字印,货币符号零售价,新成本价,新零售价,一口价,标价,定制价,A,B,C,D,E,F,G,H,I,HB_G,HB_H,直营店钻石颜色,直营店钻石净度,总重,国检总重\r\n";
			$model = new WarehouseGoodsModel(21);
			foreach ($down as $key => $val) {

				$val['goods_id']= trim($val['goods_id']);
				if ($val['goods_id'] == ""){
					break;
				}

				if(!$bill_type){
					$line =$model->getGoodsByGoods_id($val['goods_id']);
				}else if($bill_type=='Y'){
					$line =$model->getGoodsWithRate($val['goods_id']);
				}
				
				$ageline =$model->getGoodsAgeByGoods_id($val['goods_id']);
                                $xiangkou = $line['zuanshidaxiao'];
                               
                                $baoxianfei = $newmodel->GetBaoxianFei($xiangkou);

                $xilie = [];
                $base_style = 8; //基本款
                if($line['goods_sn']){
                    $style_sql = "SELECT xilie FROM `base_style_info` WHERE style_sn = '{$line['goods_sn']}' AND check_status = 3";
                    $xilie = explode(',',DB::cn(12)->db()->query($style_sql)->fetchColumn());
                }   
                //直营店钻石颜色、净度抓取规则、
                if(!in_array($line['cat_type1'], array('裸石','彩钻'))){
                    $zhiyingdian_yanse = array('香槟'=>'',
                                                '变色'=>'',
                                                '黄'=>'',
                                                'D'=>'D-E',
                                                'D-E'=>'D-E',
                                                'G'=>'F-G',
                                                'H+'=>'H',
                                                'H-I'=>'H',
                                                'L'=>'K-L',
                                                'SI2'=>'<N',
                                                '红'=>'',
                                                '橘色'=>'OR',
                                                '蓝色'=>'BLU',
                                                '棕色'=>'BRO',
                                                '白色'=>'白色',
                                                '粉色'=>'PI',
                                                'F-G'=>'F-G',
                                                '绿色'=>'GR',
                                                'N'=>'M-N',
                                                '紫色'=>'PU',
                                                'E-F'=>'D-E',
                                                'G-H'=>'F-G',
                                                'H'=>'H',
                                                'M'=>'M-N',
                                                '红色'=>'RE',
                                                '黄色'=>'YE',
                                                '黑色'=>'BLA',
                                                '蓝'=>'',
                                                '绿'=>'',
                                                '格雷恩'=>'',
                                                '粉'=>'',
                                                '紫'=>'',
                                                'E'=>'D-E',
                                                'I-J'=>'I-J',
                                                'J-K'=>'I-J',
                                                'K'=>'K-L',
                                                'M-N'=>'M-N',
                                                'S-T'=>'<N',
                                                '无'=>'无',
                                                '蓝紫色'=>'',
                                                '黑'=>'',
                                                '金色'=>'',
                                                '混色'=>'',
                                                '橙'=>'',
                                                'F'=>'F-G',
                                                'I'=>'I-J',
                                                'J'=>'I-J',
                                                'K-L'=>'K-L',
                                                'Q-R'=>'<N',
                                                '白'=>'白',
                                                '不分级'=>'');
                    $zhiyingdian_jingdu = array('I'=>'',
                                                'IF'=>'IF',
                                                'P'=>'',
                                                'P2'=>'',
                                                'SI2'=>'SI',
                                                'SI7'=>'SI',
                                                'I2'=>'',
                                                'I3'=>'',
                                                'SI8'=>'SI',
                                                'SI'=>'SI',
                                                'VVS'=>'VVS',
                                                'VVS1'=>'VVS1',
                                                'P3'=>'',
                                                'VS'=>'VS',
                                                'VVS2'=>'VVS2',
                                                '无'=>'',
                                                'VS2'=>'VS2',
                                                'I1'=>'',
                                                'SI3'=>'SI',
                                                'VS1'=>'VS1',
                                                'FL'=>'FL',
                                                'SI4'=>'SI',
                                                '完美无瑕'=>'LC',
                                                '不分级'=>'',
                                                'P1'=>'',
                                                'SI1'=>'SI');

                    $line['zhiying_yanse'] = isset($zhiyingdian_yanse[$line['zhushiyanse']])?$zhiyingdian_yanse[$line['zhushiyanse']]:'';
                    $line['zhiying_jingdu']= isset($zhiyingdian_jingdu[$line['zhushijingdu']])?$zhiyingdian_jingdu[$line['zhushijingdu']]:'';
                }else{
                    $line['zhiying_yanse'] = $line['zhushiyanse'];
                    $line['zhiying_jingdu'] = $line['zhushijingdu'];
                }
              
				$zhuchengse_list = array(
						'18K白金'=>'18K金',
						'18K玫瑰金'=>'18K金',
						'18K黄金'=>'18K金',
						'18K彩金'=>'18K金',
						//add
						'18K白'=>'18K金',
						'18K黄'=>'18K金',
						'18K黄白'=>'18K金',
						'18K玫瑰黄'=>'18K金',
						'18K玫瑰白'=>'18K金',
						
						'PT950'=>'铂Pt950',
						'PT900'=>'铂Pt900',
						'PT990'=>'铂Pt990',
						'9K白金'=>'9K金',
						'9K玫瑰金'=>'9K金',
						'9K黄金'=>'9K金',
						'9K彩金'=>'9K金',
						//add
						'9K白'=>'9K金',
						'9K黄'=>'9K金',
						'9K黄白'=>'9K金',
						'9K玫瑰黄'=>'9K金',
						'9K玫瑰白'=>'9K金',

						'10K白金'=>'10K金',
						'10K玫瑰金'=>'10K金',
						'10K黄金'=>'10K金',
						'10K彩金'=>'10K金',
						//add
						'10K白'=>'10K金',
						'10K黄'=>'10K金',
						'10K黄白'=>'10K金',
						'10K玫瑰黄'=>'10K金',
						'10K玫瑰白'=>'10K金',
						
						'14K金'=>'14K金',
						'14K白金'=>'14K金',
						'14K玫瑰金'=>'14K金',
						'14K黄金'=>'14K金',
						'14K彩金'=>'14K金',
						//add
						'14K白'=>'14K金',
						'14K黄'=>'14K金',
						'14K黄白'=>'14K金',
						'14K玫瑰黄'=>'14K金',
						'14K玫瑰白'=>'14K金',
						
						'Pd950'=>'钯Pd950',
						'S925'=>'银925',
						'足金'=>'足金',
						'千足金'=>'千足金',
						'千足银'=>'千足银',
						'无'=>'',
				);
                 $xiangqian_product_type = array('钻石','珍珠','翡翠','宝石','钻石饰品','珍珠饰品','翡翠饰品','宝石饰品' );//镶嵌类的产品线
				$caizhi=$line['caizhi'];
                //if($policy_type=='default')
               // {
                //    $data=$newmodel->getPriceByGoodsid($val['goods_id']);
                //    $jiajialv=$data['bj'];
                //    $jiajianum=$data['bst'];
               // }
              //  else if($policy_type=='bill_no')
              //  {
               //     $data=$newmodel->getPriceByGoodsid($val['goods_id']);
              //      $jiajialv=$data['aj'];
               //     $jiajianum=$data['ast'];
               // }
               $jiajialv=1;
               $jiajianum=0;
			     
				if($line['xianzaixiaoshou'] != 'err_AB' && $line['xianzaixiaoshou'] != 'err_WM' ){

					// $price = round($line['xianzaixiaoshou']*trim($jiajialv) + trim($jiajianum));		//取这个的话 打标C都为0
                                        //如果商品产品线镶嵌类，金托类型是空托：标签价=（名义成本+保险费）*加价率+系数
                                        if (in_array($line['product_type'], $xiangqian_product_type) && ($line['tuo_type'] == 3 || $line['tuo_type'] == 2)){
                                            
                                           $price = round(($line['mingyichengben']+$baoxianfei)*trim($jiajialv) + trim($jiajianum));
                                          
                                        }else{
                                           
                                            $price = round($line['mingyichengben']*trim($jiajialv) + trim($jiajianum));
                                        }
		              if (substr($line['caizhi'],0,3) == '18K'){
		                  $other_price = $line['jinzhong'] * 400 + $price + 500 ;
		                  $other_price_string = "PT定制价￥" . ceil($other_price);
		              }elseif ($line['caizhi'] == '铂Pt950' || $line['caizhi'] == 'PT950'){
		                  $other_price = $price - $line['jinzhong'] * 250;
		                  $other_price_string = "18K金定制价￥" . ceil($other_price);
		              }else{
		                  $other_price_string = "";
		              }                                        
                    /*                    
					if ($zhuchengse_list[$caizhi] == '18K金'){
						$other_price = $line['jinzhong'] * 400 + $price + 500 ;
						$other_price_string = "PT定制价￥" . ceil($other_price);
					}elseif ($zhuchengse_list[$caizhi] == '铂Pt950'){
						$other_price = $price - $line['jinzhong'] * 250;
						$other_price_string = "18K金定制价￥" . ceil($other_price);
					}else{
						$other_price_string = "";
					}*/
				}else{
					$price = '待核实';
				}

				//$line['caizhi'] = $zhuchengse_list[$caizhi];
				//$line['goods_name'] = str_replace($caizhi, $line['caizhi'], $line['goods_name']);
				$line['goods_name'] = str_replace(array('女戒','情侣戒','CNC情侣戒','男戒','戒托'), array('戒指','戒指','戒指','戒指','戒指'), $line['goods_name']);
				$line['goods_name'] = str_replace(array('海水海水','淡水白珠','淡水圆珠','淡水',"大溪地", "南洋金珠"), array('海水','珍珠','珍珠','',"海水","海水珍珠"), $line['goods_name']);
				if ($line['fushizhong'] > 0 && $line['fushi'] != $line['zhushi'] ){
					//$line['goods_name'] .= '配' . $line['fushi'];
				}
				if ($line['shi2zhong'] > 0 && $line['shi2'] != $line['zhushi'] && $line['shi2'] != $line['fushi']){
					//$line['goods_name'] .= '、' . $line['shi2'];
				}


				if($line['xianzaixiaoshou'] != 'err_AB' && $line['xianzaixiaoshou'] != 'err_WM' ){
					if ($caizhi == 'PT950' || $caizhi == 'PT990' || $caizhi == 'PT900'){
						$line['yikoujia'] = round($line['xianzaixiaoshou']*1.9 + 1000);
						$line['biaojia'] = round($line['yikoujia']*1.5);
					}else{
						$line['yikoujia'] = round($line['xianzaixiaoshou']*1.9 + 500);
						$line['biaojia'] = round($line['yikoujia']*1.5);
					}
				}
                
                $put_in_type=$line['put_in_type'];
                if($put_in_type==1 || $put_in_type==2)
                {
                    $put_in_type='GM';
                }
                if($put_in_type==3 || $put_in_type==4)
                {
                    $put_in_type='DX';
                }

                //按款定价
                if($ageline){
                    if(isset($ageline['is_kuanprice']) && $ageline['is_kuanprice'] == 1)
                    {
                        $line['biaojia'] = $ageline['kuanprice'];
                        $_line = $line;
                        $_line['caizhi'] = $caizhi;
                        $dz_kuan_price = $model->getDzKuanPrice($_line);
                        if($dz_kuan_price){
                            $other_price_string = $dz_kuan_price;
                        }
                    }
                }

		          $tihCz = array(
		            '24K' =>'足金',
		            '千足金银'    =>'足金银',
		            'S990'    =>'足银',
		            '千足银' =>'足银',            
		            '千足金' =>'足金',
		            'PT900'   =>'铂900',
		            'PT999'   =>'铂999',
		            'PT950'   =>'铂950',
		            '18K玫瑰黄'=>'18K金',
		            '18K玫瑰白'=>'18K金',
		            '18K玫瑰金'=>'18K金',
		            '18K黄金'=>'18K金',
		            '18K白金'=>'18K金',
		            '18K黑金'=>'18K金',
		            '18K彩金'=>'18K金',
		            '18K红'=>'18K金',
		            '18K黄白'=>'18K金',
		            '18K分色'=>'18K金',
		            '18K黄'=>'18K金',
		            '18K白'=>'18K金',
		            '9K玫瑰黄'=>'9K金',
		            '9K玫瑰白'=>'9K金',
		            '9K玫瑰金'=>'9K金',
		            '9K黄金'=>'9K金',
		            '9K白金'=>'9K金',
		            '9K黑金'=>'9K金',
		            '9K彩金'=>'9K金',
		            '9K红'=>'9K金',
		            '9K黄白'=>'9K金',
		            '9K分色'=>'9K金',
		            '9K黄'=>'9K金',
		            '9K白'=>'9K金',
		            '10K玫瑰黄'=>'10K金',
		            '10K玫瑰白'=>'10K金',
		            '10K玫瑰金'=>'10K金',
		            '10K黄金'=>'10K金',
		            '10K白金'=>'10K金',
		            '10K黑金'=>'10K金',
		            '10K彩金'=>'10K金',
		            '10K红'=>'10K金',
		            '10K黄白'=>'10K金',
		            '10K分色'=>'10K金',
		            '10K黄'=>'10K金',
		            '10K白'=>'10K金', 
		            '14K玫瑰黄'=>'14K金',
		            '14K玫瑰白'=>'14K金',
		            '14K玫瑰金'=>'14K金',
		            '14K黄金'=>'14K金',
		            '14K白金'=>'14K金',
		            '14K黑金'=>'14K金',
		            '14K彩金'=>'14K金',
		            '14K红'=>'14K金',
		            '14K黄白'=>'14K金',
		            '14K分色'=>'14K金',
		            '14K黄'=>'14K金',
		            '14K白'=>'14K金',
		            '19K黄'=>'19K金',
		            '19K白'=>'19K金',
		            '19K玫瑰黄'=>'19K金',
		            '19K玫瑰白'=>'19K金',
		            '19K玫瑰金'=>'19K金',
		            '19K黄金'=>'19K金',
		            '19K白金'=>'19K金',
		            '19K黑金'=>'19K金',
		            '19K彩金'=>'19K金',
		            '19K红'=>'19K金',
		            '19K黄白'=>'19K金',
		            '19K分色'=>'19K金',
		            '20K黄'=>'20K金',
		            '20K白'=>'20K金',
		            '20K玫瑰黄'=>'20K金',
		            '20K玫瑰白'=>'20K金',
		            '20K玫瑰金'=>'20K金',
		            '20K黄金'=>'20K金',
		            '20K白金'=>'20K金',
		            '20K黑金'=>'20K金',
		            '20K彩金'=>'20K金',
		            '20K红'=>'20K金',
		            '20K黄白'=>'20K金',
		            '20K分色'=>'20K金',
		            '21K黄'=>'21K金',
		            '21K白'=>'21K金',
		            '21K玫瑰黄'=>'21K金',
		            '21K玫瑰白'=>'21K金',
		            '21K玫瑰金'=>'21K金',
		            '21K黄金'=>'21K金',
		            '21K白金'=>'21K金',
		            '21K黑金'=>'21K金',
		            '21K彩金'=>'21K金',
		            '21K红'=>'21K金',
		            '21K黄白'=>'21K金',
		            '21K分色'=>'21K金',
		            '22K黄'=>'22K金',
		            '22K白'=>'22K金',
		            '22K玫瑰黄'=>'22K金',
		            '22K玫瑰白'=>'22K金',
		            '22K玫瑰金'=>'22K金',
		            '22K黄金'=>'22K金',
		            '22K白金'=>'22K金',
		            '22K黑金'=>'22K金',
		            '22K彩金'=>'22K金',
		            '22K红'=>'22K金',
		            '22K黄白'=>'22K金',
		            '22K分色'=>'22K金',
		            '23K黄'=>'23K金',
		            '23K白'=>'23K金',
		            '23K玫瑰黄'=>'23K金',
		            '23K玫瑰白'=>'23K金',
		            '23K玫瑰金'=>'23K金',
		            '23K黄金'=>'23K金',
		            '23K白金'=>'23K金',
		            '23K黑金'=>'23K金',
		            '23K彩金'=>'23K金',
		            '23K红'=>'23K金',
		            '23K黄白'=>'23K金',
		            '23K分色'=>'23K金',
		            'S925黄'=>'S925',
		            'S925白'=>'S925',
		            'S925玫瑰黄'=>'S925',
		            'S925玫瑰白'=>'S925',
		            'S925玫瑰金'=>'S925',
		            'S925黄金'=>'S925',
		            'S925白金'=>'S925',
		            'S925黑金'=>'S925',
		            'S925彩金'=>'S925',
		            'S925红'=>'S925',
		            'S925黄白'=>'S925',
		            'S925分色'=>'S925',
		            'S925'    =>'银925'
		            );

		        $stone_arr = array('红宝'=>'红宝石',
		            '珍珠贝'=>'贝壳',
		            '白水晶'=>'水晶',
		            '粉晶'=>'水晶',
		            '茶晶'=>'水晶',
		            '紫晶'=>'水晶',
		            '紫水晶'=>'水晶',
		            '黄水晶'=>'水晶',
		            '彩兰宝'=>'蓝宝石',
		            '彩色蓝宝'=>'蓝宝石',
		            '蓝晶'=>'水晶',
		            '黄晶'=>'水晶',
		            '柠檬晶'=>'水晶',
		            '红玛瑙'=>'玛瑙',
		            '黑玛瑙'=>'玛瑙',
		            '奥泊'=>'宝石',
		            '黑钻'=>'钻石',
		            '琥铂'=>'琥铂',
		            '虎晴石'=>'宝石',
		            '大溪地珍珠'=>'珍珠',
		            '大溪地黑珍珠'=>'珍珠',
		            '淡水白珠'=>'珍珠',
		            '淡水珍珠'=>'珍珠',
		            '南洋白珠'=>'珍珠',
		            '南洋金珠'=>'珍珠',
		            '海水香槟珠'=>'珍珠',
		            '混搭珍珠'=>'珍珠',
		            '蓝宝'=>'蓝宝石',
		        	'宝石石'=>'宝石',
		            '黄钻'=>'钻石');

                $stone_arr_s = array('红玛瑙'=>'玛瑙',
                    '和田玉'=>'和田玉',
                    '星光石'=>'星光石',
                    '莹石'=>'莹石',
                    '捷克陨石'=>'捷克陨石',
                    '绿松石'=>'绿松石',
                    '欧泊'=>'欧泊',
                    '砗磲'=>'砗磲',
                    '芙蓉石'=>'芙蓉石',
                    '坦桑石'=>'坦桑石',
                    '南洋白珠'=>'珍珠',
                    '大溪地珍珠'=>'珍珠',
                    '南洋金珠'=>'珍珠',
                    '无'=>'',
                    '黑玛瑙'=>'玛瑙',
                    '托帕石'=>'托帕石',
                    '橄榄石'=>'橄榄石',
                    '红纹石'=>'红纹石',
                    '蓝宝石'=>'蓝宝石',
                    '祖母绿'=>'祖母绿',
                    '黄水晶'=>'水晶',
                    '玉髓'=>'玉髓',
                    '异形钻'=>'钻石',
                    '粉红宝'=>'粉红宝',
                    '彩钻'=>'钻石',
                    '尖晶石'=>'尖晶石',
                    '石榴石'=>'石榴石',
                    '贝壳'=>'贝壳',
                    '珍珠贝'=>'贝壳',
                    '圆钻'=>'钻石',
                    '碧玺'=>'碧玺',
                    '葡萄石'=>'葡萄石',
                    '拉长石（月光石）'=>'拉长石（月光石）',
                    '舒俱来石'=>'舒俱来石',
                    '琥珀'=>'琥珀',
                    '黑钻'=>'钻石',
                    '混搭珍珠'=>'珍珠',
                    '碧玉'=>'',
                    '紫龙晶'=>'紫龙晶',
                    '玛瑙'=>'玛瑙',
                    '青金石'=>'青金石',
                    '虎睛石（木变石）'=>'虎睛石（木变石）',
                    '黑曜石'=>'黑曜石',
                    '珍珠'=>'珍珠',
                    '红宝石'=>'红宝石',
                    '其它'=>'',
                    '海蓝宝'=>'海蓝宝石',
                    '水晶'=>'水晶',
                    '翡翠'=>'翡翠',
                    '孔雀石'=>'孔雀石',
                    '东陵玉'=>'东陵玉',
                    '锂辉石'=>'锂辉石',
                    '珊瑚'=>'珊瑚',
                    '海水香槟珠'=>'珍珠',
                    '淡水白珠'=>'珍珠',
                    '锆石'=>'合成立方氧化锆',
                    '月光石'=>'月光石');

                $tihCt = array('耳环'=>'耳饰',
                    '吊坠'=>'吊坠',
                    '裸石（镶嵌物）'=>'裸石（镶嵌物）',
                    '女戒'=>'戒指',
                    '套装'=>'饰品',
                    '纪念币'=>'纪念币',
                    '素料类'=>'素料类',
                    '彩宝'=>'彩宝',
                    '彩钻'=>'彩钻',
                    '男戒'=>'戒指',
                    '赠品'=>'饰品',
                    '胸针'=>'饰品',
                    '脚链'=>'脚链',
                    '情侣戒'=>'戒指',
                    '金条'=>'金条',
                    '其它'=>'饰品',
                    '摆件'=>'摆件',
                    '项链'=>'项链',
                    '多功能款'=>'饰品',
                    '手链'=>'手链',
                    '耳钩'=>'耳饰',
                    '手表'=>'手表',
                    '固定资产'=>'固定资产',
                    '长链'=>'饰品',
                    '裸石（统包货）'=>'裸石（统包货）',
                    '裸石（珍珠）'=>'裸石（珍珠）',
                    '套戒'=>'戒指',
                    '领带夹'=>'领带夹',
                    '手镯'=>'手镯',
                    '原材料'=>'原材料',
                    '袖口钮'=>'饰品',
                    '耳钉'=>'耳饰',
                    '物料'=>'物料',
                    '其他'=>'饰品',
                    '耳饰'=>'耳饰');

		          //$tihCt = array(
		            //'男戒'=>'戒指',
		            //'女戒'=>'戒指',
		            //'情侣戒'=>'戒指'
		            //);

                  if($caizhi != ''){
                    $caizhi = str_replace(array_keys($tihCz), array_values($tihCz), $caizhi);
                  }
                  if($line['cat_type1'] != ''){
                    $line['cat_type1'] = str_replace(array_keys($tihCt), array_values($tihCt), $line['cat_type1']);
                  }
                  if($line['goods_name'] != ''){
                    //$line['goods_name'] = str_replace("千", "", $line['goods_name']);
                    $line['goods_name'] = str_replace('锆石','合成立方氧化锆',$line['goods_name']);
                    $line['goods_name'] = str_replace(array_keys($tihCz), array_values($tihCz), $line['goods_name']);
                    $line['goods_name'] = str_replace(array_keys($tihCt), array_values($tihCt), $line['goods_name']);
                    $line['goods_name'] = str_replace(array_keys($stone_arr_s), array_values($stone_arr_s), $line['goods_name']);
                  }

                if($line['zhushi'] != ''){
                    $line['zhushi'] = str_replace(array_keys($stone_arr_s), array_values($stone_arr_s),$line['zhushi']);
                }
                if($line['fushi'] != ''){
                    $line['fushi'] = str_replace(array_keys($stone_arr_s), array_values($stone_arr_s),$line['fushi']);
                }
                
				$xls_content .= "\"".$line['goods_id'] . "\",";
				$xls_content .= "\"".$line['goods_sn'] . "\",";
                $xls_content .= "\"".$put_in_type . "\",";
				$xls_content .= '' . ",";//$line['gene_sn'] 未知
				$xls_content .= "\"".$line['shoucun'] . "\",";
				$xls_content .= "\"".$line['changdu'] . "\",";
				$xls_content .= "\"".$line['zhushilishu'] . "\",";
				$xls_content .= "\"".$line['zuanshidaxiao'] . "\",";
				$xls_content .= "\"".$line['fushilishu'] . "\",";

				$xls_content .= "\"".$line['fushizhong'] . "\",";
				$xls_content .= '' . ",";
				$xls_content .= "\"".$line['zongzhong'] . "\",";
				$xls_content .= "\"".$line['jingdu'] . "\",";
				$xls_content .= "\"".$line['yanse'] . "\",";
				$xls_content .= "\"".$line['zhengshuhao'] . "\",";
				$xls_content .= "\"".$line['zhengshuleibie'] . "\",";
				$xls_content .= "\"".$line['zhushiqiegong'] . "\",";
				$xls_content .= "" . ",";

				$xls_content .= "\"".$line['zhushi'] . "\",";
				$xls_content .= "\"".$line['fushi'] . "\",";
				$xls_content .= "\"".$caizhi . "\",";
				$xls_content .= "\"".$line['product_type'] . "\",";
				$xls_content .= "\"".$line['cat_type1'] . "\",";
				$xls_content .= "\"".$line['goods_name'] . "\",";
				$xls_content .= "\"".$line['shi2'] . "\",";
				$xls_content .= "\"".$line['shi2lishu'] . "\",";
				$xls_content .= "\"".$line['shi2zhong'] . "\",";
				$xls_content .= "\"".$line['shi3'] . "\",";//石4
				$xls_content .= "\"".$line['shi3lishu'] . "\",";
				$xls_content .= "\"".$line['shi3zhong'] . "\",";
				$xls_content .= '' . ",";//石5
				$xls_content .= '' . ",";
				$xls_content .= '' . ",";

				$xls_content .= "\"".$line['jinzhong'] . "\",";
				$xls_content .= '' . ",";
				$xls_content .= '' . ",";
				$xls_content .= "\"".$line['mairugongfei'] . "\",";
				$xls_content .= "\"".$line['jijiagongfei'] . "\",";
				$xls_content .= "\"".$jiajialv . "\",";
				$xls_content .= "\"".$line['zuixinlingshoujia'] . "\",";
				$xls_content .= "\"".$line['mo_sn'] . "\",";

				$xls_content .= "\"".$line['pinpai'] . "\",";
				$xls_content .= '' . ",";//证书数量

				$xls_content .= "\"".$line['peijianshuliang'] . "\",";//
				$xls_content .= '' . ",";				//时尚款
				$xls_content .= "\"".(in_array($base_style,$xilie)?"基":"") . "\",";			//系列
				$xls_content .= '' . ",";			//属性
				$xls_content .= '' . ",";			//类别
			
				$xls_content .= "\"".( $show_private_data ? $line['chengbenjia'] : '--' ). "\",";
				$xls_content .= "\"".$line['addtime']. "\",";
				$xls_content .= '' . ","; 			//加价率代码
				$xls_content .= '' . ",";			//主石粒重
				$xls_content .= '' . ",";			//副石粒重
				$xls_content .= '' . ",";		//标签手寸

				$rate = (isset($line['jiajialv_y'])) ? $line['jiajialv_y'] : 0;
				$xls_content .= "\"".$line['ziyin'] . "\",";
				$xls_content .= "\"".$line['zuixinlingshoujia']. "\",";
				$xls_content .= "\"".( $show_private_data ? $line['mingyichengben'] : '--' ). "\",";
				$xls_content .= "\"".$price * (1+$rate) . "\",";
				$xls_content .= "\"".$line['yikoujia'] . "\",";
				$xls_content .= "\"".$line['biaojia'] . "\",";

				$xls_content .= "\"".$other_price_string . "\",";
				$xls_content .= "\"".'' . "\",";		    // A
				$xls_content .= "\"".$line['goods_name'] . "\",";	// B
				//$xls_content .= $this->get_c_col_value($line, $price) . ",";// C
				$xls_content .= "\"".$this->get_c_col_value($line, $price) . "\",";// C
				$xls_content .= "\"".$this->get_d_col_value($line) . "\",";	// d
				$xls_content .= "\"".$this->get_e_col_value($line) . "\",";	// e
				$xls_content .= "\"".$this->get_f_col_value($line). "\",";	// f
				$xls_content .= "\"".$this->get_g_col_value($line) . "\",";	// f
				$xls_content .= "\"".$this->get_h_col_value($line, $other_price) . "\",";	// h
				$xls_content .= "\"".$this->get_i_col_value($line) . "\",";// i
				$xls_content .= "\"".$this->get_hb_g_col_value($line). "\",";	// hb_f
                $xls_content .= "\"".$this->get_hb_h_col_value($line, $other_price) . "\",";   // hb_h
                $xls_content .= "\"".$line['zhiying_yanse'] . "\",";//直营店颜色
                $xls_content .= "\"".$line['zhiying_jingdu'] . "\",";//直营店净度
                $line['zongzhong'] = !empty($line['zongzhong'])?$line['zongzhong'].'g':'';
                $xls_content .= "\"".$line['zongzhong'] . "\",";//总重g
                $line['guojian_wgt'] = !empty($line['guojian_wgt'])?$line['guojian_wgt'].'g':'';
                $xls_content .= "\"".$line['guojian_wgt'] . "\"\n";//总重g

			}
		} else {
			$xls_content = '没有数据！';
		}
	header("Content-type: text/html; charset=gbk");
		header("Content-type:aplication/vnd.ms-excel");
		header("Content-Disposition:filename=" . "tiaoma.csv");
		echo iconv("utf-8", "GB18030", $xls_content);
		exit;
	}

	function get_c_col_value($line, $price)
	{

		$chanpinxian = $this->get_type_with_shipin_type($line["product_type"]);

		if($chanpinxian == "sujin")
		{
			if($line["caizhi"] == "千足金" || $line["cat_type"] == "银条")
			{
				// 黄金饰品及工艺品，还有黄金等投资产品的其余产品都不打价格，只打金重和工费
				return number_format($line['jinzhong'], 2, ".", "")."g";
			}
		}
                /*
		if(in_array($line["caizhi"], array("铂Pt950", "铂Pt900", "铂Pt990", "千足金")))
		{
			// 铂金,千足金 返回主成色重
			return number_format($line['jinzhong'], 2, ".", "")."g";
		}
                */
		// 其他返回价格
        $price = !empty($price)?"￥".round($price):"￥0";
		return $price;
	}


	function get_d_col_value($line)
	{
		//$chanpinxian = $this->get_type_with_shipin_type($line["product_type"]);

		//if(empty($chanpinxian)) return;	// 没有匹配的产品标签返回空
		//如果是非成品 那么显示镶口
		if($line['tuo_type']>1)
		{
			//如果镶口为空 就显示主石大小
			if($line['jietuoxiangkou']>0 && !empty($line['jietuoxiangkou']))
			{
				return $line['jietuoxiangkou']."ct";
			}else{
				$line["zhushilishu"] = empty($line["zhushilishu"]) ? "1" : $line["zhushilishu"];
				return $line["zuanshidaxiao"]."ct/".$line["zhushilishu"]."p";
			}
			
		}
		// 有主石和主石粒数的显示出来
		//elseif(($chanpinxian == "zuanshi" || $chanpinxian == "caibao") && $line["zuanshidaxiao"]>0)
		if($line["zuanshidaxiao"]>0)
		{
			$line["zhushilishu"] = empty($line["zhushilishu"]) ? "1" : $line["zhushilishu"];
			return $line["zuanshidaxiao"]."ct/".$line["zhushilishu"]."p";
		}
       

        /*

		// 翡翠手镯显示证书号
		if($line["zhushi"] == "翡翠" && $line["cat_type"] == "手镯")
		{
			return $line["zhengshuhao"];
		}
		// 珍珠主石
		if($line["zhushi"] == "珍珠")
		{

		}
		// 千足金戒指 并且字印不是3d和精工 显示指圈号
		if($line["caizhi"] == "足金" && ($line["cat_type1"] == "女戒" || $line["cat_type1"] == "男戒" ||  $line["cat_type"] == "情侣戒" ) && (strtoupper($line["ziyin"] == "3D") || $line["ziyin"] == "精工") && $line["shoucun"] > 0)
		{
			return "规格:".$line["shoucun"]."#";
		}
		// 千足金手链手镯 并且字印不是3d和精工 显示规格长度
		if($line["caizhi"] == "千足金" && ($line["cat_type"] == "手镯" || $line["cat_type"] == "手链") && (strtoupper($line["ziyin"] == "3D") || $line["ziyin"] == "精工") && $line["changdu"] != "")
		{
			return "规格:".$line["changdu"];
		}
		
	    */ 
		

	}


	function get_e_col_value($line)
	{
		$shilishu = $line["fushilishu"]+$line["shi2lishu"]+$line["shi3lishu"];
		$shizhong = $line["fushizhong"]+$line["shi2zhong"]+$line["shi3zhong"];
		//$estring="";
		if($shilishu > 0 && $shizhong > 0)
		{
			return $shizhong."ct/".$shilishu."p";
		}elseif($shilishu > 0 && empty($shizhong)){
            return $shilishu."p";
        }elseif(empty($shilishu) && $shizhong > 0){
            return $shizhong."ct";
        }else{
            return '';
        }

	}
	function get_f_col_value($line)
	{
        $jinzhong = 0;
         if($line["jinzhong"]<>"0" && $line["jinzhong"]<>""){
            $jinzhong = $line['jinzhong'];
         }
         $peijianjinchong = 0;
         if($line["peijianjinchong"]<>"0" && $line["peijianjinchong"]<>""){
            $peijianjinchong = $line['peijianjinchong'];
         }
         if($jinzhong<>'0' || $peijianjinchong<>'0'){
            $val = $jinzhong+$peijianjinchong;
            //return mb_substr($val,0,-1)."g";
            if(!empty($val)){
                return $val."g";
            }
            
         }
         return "";
		/*
		$chanpinxian = $this->get_type_with_shipin_type($line["product_type"]);
		if(empty($chanpinxian)) return;	// 没有匹配的产品标签返回空
		if($chanpinxian == "彩宝")
		{
			// 彩宝  主成色重, 空白(挂件)
			if($line["cat_type"] == "挂件")
			{
				return "";
			}
			elseif($line["jinzhong"] > 0)
			{
				return mb_substr($line["jinzhong"],0,-1)."g";
			}
		}
		elseif($chanpinxian == "zhenzhu")
		{
			// 珍珠  主成色重, 空白(s925)
			if(in_array($line["caizhi"], array("银925")))
			{
				return "";
			}
			elseif($line["jinzhong"] > 0)
			{
				return mb_substr($line["caizhi"],0,-1)."g";
			}
		}
		elseif($chanpinxian == "zuanshi" && $line["jinzhong"] > 0)
		{
			return mb_substr($line["jinzhong"],0,-1)."g";
		}
		else
		{
			// 素金  空白
			return mb_substr($line["jinzhong"],0,-1)."g";
		}
		*/
	}
	function get_g_col_value($line)
	{
		return $line["zhengshuhao"];
		/*
		$chanpinxian = $this->get_type_with_shipin_type($line["product_type"]);
		if(empty($chanpinxian)) return;	// 没有匹配的产品标签返回空

		if($chanpinxian == "zuanshi")
		{
			if($line["tuo_type"] == 2 || $line["tuo_type"] == 3)	// 托
			{
				
				return "";
			}
			else
			{
				return $line["zhengshuhao"];
			}
		}
		elseif($chanpinxian == "zhenzhu")
		{
			return $line["zhengshuhao"];
		}
		elseif($chanpinxian == "caibao")
		{
			// 翡翠手镯 返回宽度
			if($line["zhushi"] == "翡翠" && $line["cat_type"] == "手镯")
			{
				return $line["zhushiguige"];
			}
			else
			{	// 其他返回证书号
				return $line["zhengshuhao"];
			}
		}
		elseif($chanpinxian == "sujin")
		{
			// 素金产品按 材质和款式再分
			if(in_array($line["caizhi"], array("铂Pt950", "铂Pt900", "铂Pt990")))
			{
				// 返回工费
				//return "工费:￥".number_format($line["mairugongfei"],"0", ".","");
				return;
			}
			//elseif(in_array($line["caizhi"], array("千足金")) && $line["product_type"] != "工艺品")
			elseif(in_array($line["caizhi"], array("千足金")) && $line["product_type"] != "投资黄金")
			{// 金饰品 返回工费
				return "工费:￥".number_format($line['jijiagongfei']*3+$line["jinzhong"]*4,0,".","");
				if($line["cat_type"] == "金条")
				{
					// 返回工费
					//return "工费:￥".number_format($line["mairugongfei"],"0", ".","");
					return "工费:￥".number_format($line['jijiagongfei']*3+$line["jinzhong"]*4,0,".","");
				}
				// 金条 精品返回工费, 其他返回空
				else
				{
					if($line["ziyin"] == "精工")
					{
						// 返回工费
						//return "工费:￥".number_format($line["mairugongfei"],"0", ".","");
						return "工费:￥".number_format($line['jijiagongfei']*3+$line["jinzhong"]*4,0,".","");
					}
				}
			}
			elseif(in_array($line["caizhi"], array("银925", "千足银")))
			{
				// 戒指 返回指圈号
				if(strpos($line["caizhi"], "戒") !== false && $line["shoucun"] != "")
				{
					return "规格:".$line["shoucun"]."#";
				}
				// 手链,项链 规格 长度
				elseif(($line["cat_type"] == "手链" || $line["cat_type"] == "项链") && $line["changdu"] != "")
				{
					return "规格:".$line["changdu"];
				}
				// 银条 精品-> 工费,
				//elseif($line["kuanshi_type"] == "银条" && $line["ziyin"] == "精工")
				// 除了工艺品 都显示工费
				elseif($line["product_type"] != "投资黄金")
				{
					// 返回工费
					//return "工费:￥".number_format($line["mairugongfei"],"0", ".","");
					return "工费:￥".number_format($line['jijiagongfei']*3+$line["jinzhong"]*4,0,".","");
				}
				// 吊坠,耳饰 返回空
				else
				{
					return "";
				}
				
			}
			elseif(in_array($line["caizhi"], array("18K金")))
			{
				// 戒指  指圈号
				if(strpos($line["cat_type"], "戒") !== false && $line["shoucun"] != "")
				{
					return "规格:".$line["shoucun"]."#";
				}
				// 手链,项链  规格长度
				elseif(($line["cat_type"] == "手链" || $line["cat_type"] == "项链") && $line["changdu"] != "")
				{
					return "规格:".$line["changdu"];
				}
				// 吊坠,耳饰,手镯  空白
				else
				{
					return "";
				}
			}
		}*/
	}


	function get_h_col_value($line, $other_price)
	{
        $hstring="";
        if(trim($line["zhushiyanse"])!="" && trim($line["zhushiyanse"])!="0" )
            $hstring.= $line["zhushiyanse"];    
		if(trim($line["zhushijingdu"])!="" && trim($line["zhushijingdu"])!="0" )
			$hstring.= "/".$line["zhushijingdu"];
        if(trim($hstring)=="0/0")
		    $hstring="";
		return $hstring;
		/*
		$chanpinxian = $this->get_type_with_shipin_type($line["product_type"]);
		if(empty($chanpinxian)) return;	// 没有匹配的产品标签返回空

		// 珍珠 珍珠大小
		if($chanpinxian == "zhenzhu")
		{
			return $line["zhushiguige"];
		}
		// 彩宝 长度,空白
		elseif($chanpinxian == "caibao")
		{
			// 项链,手链,手镯  返回长度
			if(in_array($line["cat_type"], array("手链", "项链", "手镯")) && $line["changdu"] != "")
			{
				return "规格:".$line["changdu"];
			}
		}
		// 素金 空白,指圈号,长度
		elseif($chanpinxian == "sujin")
		{
			// pt戒指,指圈号
			if(in_array($line["caizhi"], array("铂Pt950", "铂Pt900", "铂Pt990")) && $line["shoucun"] != "")
			{
				return "规格:".$line["shoucun"]."#";
			}
			// pt项链,手链  长度
			elseif(in_array($line["caizhi"], array("铂Pt950", "铂Pt900", "铂Pt990")) && $line["changdu"] != "")
			{
				return "规格:".$line["changdu"];
			}
			// 黄金戒指 不是精品,3D  指圈号
			elseif(in_array($line["caizhi"], array("千足金")) && $line["shoucun"] >= 0 && strtoupper($line["ziyin"] != "3D") && $line["ziyin"] != "精工" && $line["kuanshi_type"] == "戒指" && $line["shoucun"]>0)
			{
				return "规格:".$line["shoucun"]."#";
			}
			// 黄金项链 不是精品,3D  规格长度
			elseif(in_array($line["caizhi"], array("千足金")) && $line["changdu"] != "" && strtoupper($line["ziyin"] != "3D") && $line["ziyin"] != "精工" && $line["shipin_type"] == "项链")
			{
				return "规格:".$line["changdu"];
			}
		}
		// 钻饰 净度颜色,定制价,空白
		elseif($chanpinxian == "zuanshi")
		{
			// 18K,PT托  定制价
			if(in_array($line["caizhi"], array("铂Pt950", "铂Pt900", "铂Pt990", "18K金")) && $line["tuo_type"] >1)
			{
				//return "￥".number_format($other_price, 0,".","");
				return "";
			}
			// 18K,PT戒指 钻石净度+钻石颜色
			else
			{
				return $line["zhushijingdu"]."/".$line["zhushiyanse"];
			}

		}*/
	}
	function get_i_col_value($line)
	{

		if(trim($line["shoucun"])<>"" && $line["shoucun"]<>"0"){
            return $line["shoucun"]."#";
        }else{
            return "";
        }
		     
		/*
		$chanpinxian = $this->get_type_with_shipin_type($line["product_type"]);
		if(empty($chanpinxian)) return;	// 没有匹配的产品标签返回空

		// 彩宝  戒指指圈号,挂件主成色重
		if($chanpinxian == "caibao")
		{
			if(trim($line["cat_type"]) == "男戒" || trim($line["cat_type"]) == "女戒"|| trim($line["cat_type"]) == "情侣戒")
			{
				return $line["shoucun"]."#";
			}
			elseif($line["cat_type"] == "挂件")
			{
				return number_format($line["jinzhong"], 2 ,".","");
			}
		}
		// 珍珠  珍珠指圈号,项链手链链长,
		elseif($chanpinxian == "zhenzhu")
		{
			if($line["cat_type"] == "男戒" || $line["cat_type"] == "女戒" || $line["cat_type"] == "情侣戒")
			{
				return $line["shoucun"]."#";
			}
			elseif(($line["cat_type"] == "项链" || $line["cat_type"] == "手链" ) && $line["changdu"] !="")
			{
				return "长:".$line["changdu"];
			}
		}
		// 钻饰  戒指戒托指圈号,手链套链规格长度
		elseif($chanpinxian == "zuanshi")
		{
			if(strpos($line["cat_type"], "戒") !== false)
			{
				return $line["shoucun"]."#";
			}
			elseif(($line["cat_type"] == "项链" || $line["cat_type"] == "套链") && $line["changdu"]!= "" )
			{
				return "规格:长".$line["changdu"];
			}
		}
		// 素金 分材质
		elseif($chanpinxian == "sujin")
		{

			if(trim($line["cat_type1"]) == "男戒" || trim($line["cat_type1"]) == "女戒"|| trim($line["cat_type1"]) == "情侣戒")
			{
				return $line["shoucun"]."#";
			}			
			// 18K 主成色重
			if(in_array($line["caizhi"], array("18K金")))
			{
				return number_format($line["jinzhong"],2,".","")."g";
			}
			// 千足金 精品,3D,空白
			elseif($line["caizhi"] == "千足金")
			{
				if($line["ziyin"] == "精工")
				{
					return "精品";
				}
				elseif(strtoupper($line["ziyin"]) == "3D")
				{
					return "3D硬金";
				}
			}
			// 银 银条精品,3D,空白 其他主成色重
			elseif($line["caizhi"] == "银925")
			{
				if($line["ziyin"] == "精工")
				{
					return "精品";
				}
				elseif(strtoupper($line["ziyin"]) == "3d")
				{
					return "3D硬金";
				}
				else
				{
					return number_format($line["jinzhong"], 2,".","")."g";
				}
			}
			// PT 空白
		}*/
	}
	function get_hb_g_col_value($line)
	{  
		$chanpinxian = $this->get_type_with_shipin_type($line["product_type"]);
		if(empty($chanpinxian)) return;	// 没有匹配的产品标签返回空
		$jindata = array('18K金','18K白金','18K玫瑰金','18K彩金','18K玫瑰白','18K白','18K黄','18K黄白','18K玫瑰黄');
		$ptdata = array('铂Pt950','PT950');
		
		//if($chanpinxian == "zuanshi"){
			//如果金托类型是成品并且款式分类是情侣戒，需要显示定制价这几个字
			if($line["tuo_type"] == 1)
			{
				if($line["cat_type"] == "情侣戒")	
				{
					//显示定制价
					if(in_array($line['caizhi'],$jindata))
					{
						return "铂PT950定制价:";
					}elseif(in_array($line['caizhi'],$ptdata))
					{
						return "金18K定制价:";
					}else{
						return "铂PT950定制价:";	
					}
				}else{
					return $line["zhengshuhao"];
				}
			}
			if($line["tuo_type"] == 2 || $line["tuo_type"] == 3)
			{
				//return $line["zhengshuhao"];
				//显示定制价
				if(in_array($line['caizhi'],$jindata))
				{
					return "铂PT950定制价:";
				}elseif(in_array($line['caizhi'],$ptdata))
				{
					return "金18K定制价:";
				}else{
					return "铂PT950定制价:";	
				}
			}
			
			/*原有逻辑
			if($line["tuo_type"] == 2 || $line["tuo_type"] == 3 || $line["cat_type"] == "情侣戒")	// 托
			{
				//return $other_price_string;
				if($line['caizhi'] == '18K金' || $line['caizhi'] == '18K白金')
				{
					return '';//"铂PT950定制价:";
				}
				elseif($line['caizhi'] == '铂Pt950' || $line['caizhi'] == 'PT950')
				{
					return '';//"金18K定制价:";
				}
			}
			else
			{
				return $line["zhengshuhao"];
			}
			*/
		//}
	}
	function get_hb_h_col_value($line, $other_price)
	{
		$chanpinxian = $this->get_type_with_shipin_type($line["product_type"]);
		if(empty($chanpinxian)) return;	// 没有匹配的产品标签返回空
		
		$dataarr = array("铂Pt950", "铂Pt900", "铂Pt990", "18K金","18K白金","PT950","18K玫瑰金","18K彩金",'18K玫瑰白');
	
		//if($chanpinxian == "zuanshi"){
			
			/*原来的
			// 18K,PT托  定制价
			if((in_array($line["caizhi"],$dataarr ) && $line["tuo_type"] >1) || $line["cat_type"] == "情侣戒")
			{
				if(in_array($line['caizhi'],array("18K金","18K白金"))){
					return "铂PT950定制价:"."￥".number_format($other_price, 0,".","");
				}elseif(in_array($line['caizhi'],array("铂Pt950", "铂Pt900", "铂Pt990","PT950"))){
					return "金18K定制价:"."￥".number_format($other_price, 0,".","");
				}
				return "￥".number_format($other_price, 0,".","");
			}else{
				return $line["zhushijingdu"]."/".$line["zhushiyanse"];
			}*/
			
			
			//如果金托类型是成品，且款式分类是情侣戒需要显示定制价
			if($line['tuo_type'] == 1)
			{
				//如果是情侣
				if($line["cat_type"] == "情侣戒")
				{
					return "￥".number_format($other_price, 0,".","");
				}else{
					return $line["zhushijingdu"]."/".$line["zhushiyanse"];
				}
			}elseif($line["tuo_type"] == 2 || $line["tuo_type"] == 3)
			{
				//return $line["zhushijingdu"]."/".$line["zhushiyanse"];
				return "￥".number_format($other_price, 0,".","");
			}
			
		//}
	}



	/*------------------------------------------------------ */
	//-- 判断条码类别
	//-- by zlj 
	/*------------------------------------------------------ */
	function get_type_with_shipin_type($shipin_type)
	{
		//黄金等投资产品,黄金饰品及工艺品,素金饰品,   钻石饰品,珍珠饰品,彩宝饰品,翡翠饰品, 其他饰品,非珠宝,配件及特殊包装
		$res = "";

		$shipin_type = trim($shipin_type);
		switch($shipin_type)
		{
			case "黄金等投资产品":
			case "黄金饰品及工艺品":
			case "素金饰品":
				$res = "sujin";
				break;
			case '钻石饰品':
				$res = "zuanshi";
				break;
			case '珍珠饰品':
				$res = "zhenzhu";
				break;
			case '彩宝饰品':
			case '翡翠饰品':
				$res = "caibao";
				break;
			case '其他':
				$res = "qita";
				break;			     
			case '裸石':
				$res = "zuanshi";
				break;	
			case '锆石':
				$res = "zuanshi";
				break;							
			case "K金":
				$res = "sujin";
				break;
			case "PT":
				$res = "sujin";
				break;
			case "银饰":
				$res = "sujin";
				break;
			case "珍珠":
				$res = "zhenzhu";
				break;
			case "钻石":
				$res = "zuanshi";
				break;
			case "翡翠":
				$res = "caibao";
				break;
			case "彩钻":
				$res = "zuanshi";
				break;
			case "宝石":
				$res = "baoshi";
				break;
			case "足金镶嵌";
				$res = "zjxqtype";
				break;
			case "普通黄金";
				$res = "pthjtype";
				break;
			case "投资黄金";
				$res = "tzhjtype";
				break;
			case "定价黄金";
				$res = "djhjtype";
				break;	
			default:
		}
		return $res;
	}


	//AJAX检测订单号是否合法
	public function CheckOrderSn($params){
		$result = array('success' => 0,'error' =>'');
		$order_sn = trim($params['order_sn']);
		$model = new WarehouseBillModel(21);
		$warehousemodel = new WarehouseModel(21);
		$salemodel = new SalesModel(27);
		$res = $salemodel->CheckOrderSn($order_sn);
		//检查订单是否合法
		if(!$res){
			$result['error'] = '未查询到此订单!';
			Util::jsonExit($result);
		}
		//获得门店信息
		$dis = $salemodel->getDistributionByOrderId($res['id']);
		if(isset($dis['distribution_type'])){
				if($dis['distribution_type'] ==1 && isset($dis['shop_name'])){
					$managemodel = new ManagementModel(1);
					//配送方式：门店(通过店铺名称获取店铺信息)
					$infos = $managemodel->GetShopInfoByShopName($dis['shop_name']);
					//通过公司ID获取该公司下的仓库
					$infos = $warehousemodel->getRepairLastWarehouse($infos['company_id']);
                    $info['warehouse_id'] =$infos['id'];
                    $info['code'] =$infos['code']; 
                    $info['warehouse_name'] =$infos['name'];

                }else{
                    //配送方式：总公司到客户
                    $info['warehouse_id'] =606;
                    $info['code']='SZWX';
                    $info['warehouse_name'] ='总公司维修库';
				}
			}
		$info['customer'] =$res['consignee'];	//顾客姓名
		//返回入库仓库信息
		$result['data'] = $info;
		$result['success'] = 1;
		Util::jsonExit($result);
	}

    //批发单批量导出功能
    public function downPfBill($params)
    {
        $ids = $params['ids'];
        $model = new WarehouseBillModel(21);
        $goodsModel = new GoodsAttributeModel(17);
        $data = array();
        if($ids){
            $id_arr = explode(",", $ids);
            $styleModel = new BaseStyleInfoModel(12);
            $ProccesorModel = new SelfProccesorModel(13);
            $rest = $styleModel->getstyleAllInfo();
            $styleSnAll = array_column($rest, 'style_sn');
            foreach ($id_arr as $bill_id) {
                $data[$bill_id] = $model->getBillPfInfo($bill_id);
                if(count($data[$bill_id])>0){
                    foreach ($data[$bill_id] as $k => &$tinfo) {
                        if(!in_array($tinfo['goods_sn'], $styleSnAll)){
                            $new_k_sn = $model->getNewStyle_sn($tinfo);
                            if($new_k_sn){
                                $tinfo['goods_sn'] = $new_k_sn;
                            }else{
                                $tinfo['goods_sn'] = '';
                            }
                        }
                        //根据主成色获取材质和颜色
                        if($tinfo['caizhi']){
                            $cZall = array('PT950'=>'白', '足金'=>'黄', '24K'=>'黄', '千足金'=>'黄', 'PT950'=>'白', 'PT900'=>'白', 'PT999'=>'白', '千足银'=>'白', 'S999'=>'白', '无'=>'无', '其它'=>'按图做', '千足金银'=>'按图做', 'S925'=>'按图做', 'S990'=>'按图做', '18K'=>'按图做', '14K'=>'按图做', '10K'=>'按图做', '9K'=>'按图做');
                            $allCzYs = $goodsModel->explodeZhuchengseToStr($tinfo['caizhi']);
                            $tinfo['caizhi1'] = $caizhi1 = $allCzYs['caizhi'];
                            $tinfo['yanse'] = $allCzYs['jinse'];
                            if($tinfo['caizhi1'] && $tinfo['yanse'] == ''){
                                $tinfo['yanse'] = $cZall[$caizhi1];
                            }
                        }
                        
                        if($tinfo['buchan_sn'] !=''){
                        	$tinfo['face_work']=$ProccesorModel->getFaceworkByBcNo($tinfo['buchan_sn']);
                        }else{
                        	$tinfo['face_work']='';
                        }
                        
                        if($tinfo['bill_type']=="M"){
                            if($tinfo['cat_type1']=='裸石'){
                            	if(empty($tinfo['zhengshuhao'])){
                            		$tinfo['linshuojia']="是裸钻，证书号不存在";
                            	}else{
                            		$tinfo['linshuojia']=$model->getLinshoujia2($tinfo['zhengshuhao']);
                            	}
                            	
                            }else{
                            	$tinfo['linshuojia']=$model->getLinshoujia($tinfo['goods_id']);
                            }	 
                        	
                        }else{
                        	$tinfo['linshuojia']='';
                        }
                        
                        //出库类型
                        if($tinfo['out_warehouse_type'] == 1){
                            $tinfo['out_warehouse_type'] = '购买';
                        }elseif($tinfo['out_warehouse_type'] == 2){
                            $tinfo['out_warehouse_type'] = '借货';
                        }else{
                            $tinfo['out_warehouse_type'] = '';
                        }
                    }
                }
            }
        }
        $this->downPfExcel($data);
    }

    //导出
    public function downPfExcel($data)
    {
        $dd =new DictModel(1);
        //$salemodel = new SalesModel(51);
        //$view = new WarehouseBillModel(21);
        if(!empty($data)){
            $is_zt_show = SYS_SCOPE == 'zhanting';
            $show_private_data = SYS_SCOPE == 'boss' || Auth::user_is_from_base_company();
                $xls_content = "单据编号,批发客户,出库公司,序号,货号,品号,款号,金重,证书号,证书类型,生产工艺,镶口,指圈,材质,材质颜色,表面工艺,主石类型,主石重,主石粒数,净度,颜色,主石切工,主石形状,荧光,抛光,对称性,颜色分级/饱和度,珍珠形状,珍珠颜色,总重,宽度(cm),高/厚度(cm),链长(cm),内直径(cm),长度/外直径(cm),副石1净度,副石1类型,副石1粒数,副石1形状,副石1颜色,副石1重,副石2类型,副石2粒数,副石2重,副石3类型,副石3粒数,副石3重,其他副石类型,其他副石粒数,其他副石重,其他石头信息描述,其他证书说明,批发价,未税零售克拉单价/金价,未税零售价,含税零售克拉单价/金价,含税零售价,管理费,克拉单价/金价,销售工费计价方式,未税批发单价,是否一口价,名称,外部订单号,订单项次,BOSS来源订单号,金托类型,备注,是否自采,原始采购成本,出库类型,类别,管理费,标注,展厅标签价\r\n";
                foreach ($data as $key => $info) {
                    foreach ($info as $xuhao => $val) {
                        $xls_content .= $val['bill_no'] . ",";
                        $xls_content .= $val['wholesale_name'] . ",";
                        $xls_content .= $val['from_company_name'] . ",";
                        $xls_content .= $xuhao + 1 . ",";
                        $xls_content .= $val['goods_id'] . ","; //销售渠道
                        $xls_content .= $val['pinhao'] . ",";
                        $xls_content .= $val['goods_sn'] . ",";
                        $xls_content .= $val['jinzhong'] . ",";
                        $xls_content .= $val['zhengshuhao']. ",";
                        $xls_content .= $val['zhengshuleibie'] . ",";
                        $xls_content .= '' . ",";
                        $xls_content .= $val['jietuoxiangkou'] . ",";
                        $xls_content .= $val['shoucun'] . ",";
                        $xls_content .= $val['caizhi1'] . ",";
                        $xls_content .= $val['yanse'] . ",";
                        $xls_content .= $val['face_work'] . ",";
                        $xls_content .= $val['zhushi'] . ",";
                        $xls_content .= $val['zuanshidaxiao'] . ",";
                        $xls_content .= $val['zhushilishu'] . ",";
                        $xls_content .= $val['zhushijingdu'] . ",";
                        $xls_content .= $val['zhushiyanse'] . ",";
                        $xls_content .= $val['zhushiqiegong'] . ",";
                        $xls_content .= $val['zhushixingzhuang'] . ",";
                        $xls_content .= $val['yingguang'] . ",";
                        $xls_content .= $val['paoguang'] . ",";
                        $xls_content .= $val['duichen'] . ",";
                        $xls_content .= $val['color_grade'] . ",";
                        $xls_content .= '' . ",";
                        $xls_content .= '' . ",";
                        $xls_content .= $val['zongzhong'] . ",";
                        $xls_content .= '' . ",";
                        $xls_content .= '' . ",";
                        $xls_content .= '' . ",";
                        $xls_content .= '' . ",";
                        $xls_content .= '' . ",";
                        $xls_content .= $val['fushijingdu'] . ",";
                        $xls_content .= $val['fushi'] . ",";
                        $xls_content .= $val['fushilishu'] . ",";
                        $xls_content .= $val['fushixingzhuang'] . ",";
                        $xls_content .= $val['fushiyanse'] . ",";
                        $xls_content .= $val['fushizhong'] . ",";
                        $xls_content .= $val['shi2'] . ",";
                        $xls_content .= $val['shi2lishu'] . ",";
                        $xls_content .= $val['shi2zhong'] . ",";
                        $xls_content .= '' . ",";
                        $xls_content .= '' . ",";
                        $xls_content .= '' . ",";
                        $xls_content .= '' . ",";
                        $xls_content .= '' . ",";
                        $xls_content .= '' . ",";
                        $xls_content .= '' . ",";
                        $xls_content .= $val['pinpai'] . ",";
                        $xls_content .= $val['shijia'] . ",";
                        $xls_content .= '' . ",";
                        $xls_content .= '' . ",";
                        $xls_content .= '' . ",";
                        $xls_content .= $val['linshuojia'] . ",";
                        $xls_content .= '' . ",";
                        $xls_content .= '' . ",";
                        $xls_content .= '' . ",";
                        $xls_content .= '' . ",";
                        $xls_content .= '' . ",";
                        $xls_content .= $val['goods_name'] . ",";
                        $xls_content .= $val['p_sn_out'] . ",";
                        $xls_content .= $val['xiangci'] . ",";
                        $xls_content .= $val['order_sn'] . ",";
                        $xls_content .= $dd->getEnum('warehouse_goods.tuo_type',$val['tuo_type']) . ",";
                        $xls_content .= $val['goods_sn1'] . ",";
                        $xls_content .= 'N' . ",";
                        $xls_content .= ($show_private_data ? $val['yuanshichengbenjia'] : '--' ). ",";
            			$xls_content .= ($is_zt_show ? $val['out_warehouse_type'] : '--' ). ",";
                        $xls_content .= $val['p_type'] . ",";
            			$xls_content .= $val['management_fee'].",";
                        $xls_content .= ($is_zt_show ? $val['label_price'] : '--' ). "\n";
                    }
                }
            } else {
                $xls_content = '没有数据！';
            }
            header("Content-type:text/csv;charset=gbk");
            header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv");
            header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
            header('Expires:0');
            header('Pragma:public');
            echo iconv("utf-8", "gbk//IGNORE", $xls_content);
            exit;
        }

        //打印标签
        public function printCodeNew($value='')
        {
            $ids = _Request::getString("_ids");
            if(empty($ids)){
                exit("ID is empty.");
            }
            $bill_model = new WarehouseBillGoodsModel(21);
            $ret = $bill_model->select2(" `goods_id` "," bill_id in(".$ids.") ",3);
            if(empty($ret)){
                exit("NO data.");
            }
            $where = array();
            $where['goods_id'] = array_column($ret,'goods_id');
            $model = new WarehouseGoodsModel(21);
            $res = $model->getprintgoodsinfo($where," `goods_name`,`goods_sn`,`zhengshuhao`,`caizhi`,`jinzhong`,`zhushilishu`,`zuanshidaxiao`,(`fushilishu`+`shi2lishu`+`shi3lishu`) as fushilishu,(`fushizhong`+`shi2zhong`+`shi3zhong`) as fushizhong,`goods_id`,`shoucun` ");
            $selfTihCz = $this->tihCzInfo;
            foreach ($res as $key => &$info) {
                if($info['caizhi'] != ''){
                    $info['caizhi'] = str_replace(array_keys($selfTihCz), array_values($selfTihCz), $info['caizhi']);
                }
                if($info['goods_name'] != ''){
                    $info['goods_name'] = str_replace('锆石','合成立方氧化锆',$info['goods_name']);
                    $info['goods_name'] = str_replace(array_keys($selfTihCz), array_values($selfTihCz), $info['goods_name']);
                    $info['goods_name'] = str_replace(array_keys($this->tihCtInfo), array_values($this->tihCtInfo), $info['goods_name']);
                    $info['goods_name'] = str_replace(array_keys($this->stoneInfo), array_values($this->stoneInfo), $info['goods_name']);
                }
            }
            $this->render('print_bill_goods_info.html',array(
                'datalist'=>$res
              )
            );
        }

	    //导出
	    public function download_chengben($params)
	    {
	        set_time_limit(0);
	        header("Content-Type: text/html; charset=gb2312");
	        header("Content-type:aplication/vnd.ms-excel");
	        header("Content-Disposition:filename=".iconv('utf-8','gb2312','导出成本').time().".xls");
            echo "<table><tr>";
            echo "<td>收货单号</td>";
            echo "<td>送货单号</td>";
            echo "<td>布产号</td>";
            echo "<td>货号</td>";
            echo "<td>款号</td>";
            echo "<td>模号</td>";
            echo "<td>饰品分类</td>";
            echo "<td>款式分类</td>";
            echo "<td>主成色</td>";
            echo "<td>主成色重</td>";
            echo "<td>金耗</td>";
            echo "<td>主成色重计价</td>";
            echo "<td>主成色买入单价</td>";
            echo "<td>主成色买入成本</td>";
            echo "<td>主成色计价单价</td>";
            echo "<td>主石</td>";
            echo "<td>主石粒数</td>";
            echo "<td>主石重</td>";
            echo "<td>主石重计价</td>";
            echo "<td>主石颜色</td>";
            echo "<td>主石净度</td>";
            echo "<td>主石买入单价</td>";
            echo "<td>主石买入成本</td>";
            echo "<td>主石计价单价</td>";
            echo "<td>主石切工</td>";
            echo "<td>主石形状</td>";
            echo "<td>主石包号</td>";
            echo "<td>主石规格</td>";
            echo "<td>副石</td>";
            echo "<td>副石粒数</td>";
            echo "<td>副石重</td>";
            echo "<td>副石重计价</td>";
            echo "<td>副石颜色</td>";
            echo "<td>副石净度</td>";
            echo "<td>副石买入单价</td>";
            echo "<td>副石买入成本</td>";
            echo "<td>副石计价单价</td>";
            echo "<td>副石形状</td>";
            echo "<td>副石包号</td>";
            echo "<td>副石规格</td>";
            echo "<td>总重</td>";
            echo "<td>买入工费单价</td>";
            echo "<td>买入工费</td>";
            echo "<td>计价工费</td>";
            echo "<td>手寸</td>";
            echo "<td>单件成本</td>";
            echo "<td>配件成本</td>";
            echo "<td>配件金重</td>";
            echo "<td>其他成本</td>";
            echo "<td>成本价</td>";
            echo "<td>计价成本</td>";
            echo "<td>成品|空托</td>";
            echo "<td>工厂</td>";
            echo "<td></td>";
            echo "<td></td>";


	        $model = new WarehouseBillModel(55);//只读数据库
            $list = $model->get_chengben_detail($params);
            foreach ($list as $key => $v) {
            	echo "<tr>";
            	echo "<td>".$v['bill_no']."</td>";
            	echo "<td>".$v['send_goods_sn']."</td>";
            	echo "<td>".$v['buchan_sn']."</td>";
            	echo "<td>".$v['goods_id']."</td>";
            	echo "<td>".$v['goods_sn']."</td>";
            	echo "<td>".$v['mo_sn']."</td>";
            	echo "<td>".$v['product_type1']."</td>";
            	echo "<td>".$v['cat_type1']."</td>";
            	echo "<td>".$v['caizhi']."</td>";
            	echo "<td>".$v['jinzhong']."</td>";
            	echo "<td>".$v['jinhao']."</td>";
            	echo "<td>".$v['zhuchengsezhongjijia']."</td>";
            	echo "<td>".$v['zhuchengsemairudanjia']."</td>";
            	echo "<td>".$v['zhuchengsemairuchengben']."</td>";
            	echo "<td>".$v['zhuchengsejijiadanjia']."</td>";
            	echo "<td>".$v['zhushi']."</td>";
            	echo "<td>".$v['zhushilishu']."</td>";
            	echo "<td>".$v['zuanshidaxiao']."</td>";
            	echo "<td>".$v['zhushizhongjijia']."</td>";
            	echo "<td>".$v['zhushiyanse']."</td>";
            	echo "<td>".$v['zhushijingdu']."</td>";
            	echo "<td>".$v['zhushimairudanjia']."</td>";
            	echo "<td>".$v['zhushimairuchengben']."</td>";
            	echo "<td>".$v['zhushijijiadanjia']."</td>";
            	echo "<td>".$v['zhushiqiegong']."</td>";
            	echo "<td>".$v['zhushixingzhuang']."</td>";
            	echo "<td>".$v['zhushibaohao']."</td>";
            	echo "<td>".$v['zhushiguige']."</td>";
            	echo "<td>".$v['fushi']."</td>";
            	echo "<td>".$v['fushilishu']."</td>";
            	echo "<td>".$v['fushizhong']."</td>";
            	echo "<td>".$v['fushizhongjijia']."</td>";
            	echo "<td>".$v['fushiyanse']."</td>";
            	echo "<td>".$v['fushijingdu']."</td>";
            	echo "<td>".$v['fushimairudanjia']."</td>";
            	echo "<td>".$v['fushimairuchengben']."</td>";
            	echo "<td>".$v['fushijijiadanjia']."</td>";
            	echo "<td>".$v['fushixingzhuang']."</td>";
            	echo "<td>".$v['fushibaohao']."</td>";
            	echo "<td>".$v['fushiguige']."</td>";
            	echo "<td>".$v['zongzhong']."</td>";
            	echo "<td>".$v['mairugongfeidanjia']."</td>";
            	echo "<td>".$v['mairugongfei']."</td>";
            	echo "<td>".$v['jijiagongfei']."</td>";
            	echo "<td>".$v['shoucun']."</td>";
            	echo "<td>".$v['danjianchengben']."</td>";
            	echo "<td>".$v['peijianchengben']."</td>";
            	echo "<td>".$v['peijianjinchong']."</td>";
            	echo "<td>".$v['qitachengben']."</td>";
            	echo "<td>".$v['chengbenjia']."</td>";
            	echo "<td>".$v['jijiachengben']."</td>";
            	echo "<td>".$v['tuo_type']."</td>";
            	echo "<td>".$v['pro_id']."</td>";/*$v['pro_name']*/
            	
                echo "</tr>";
            }
            echo "</table>";
	    }



    //导出
    public function download_sale($params)
    {
        set_time_limit(0);
        header("Content-Type: text/html; charset=gb2312");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=".iconv('utf-8','gb2312','销售单导出').time().".xls");

        $model = new WarehouseBillModel(55);//只读数据库
        $list = $model->get_sale_detail($params);
        //print_r($list);exit;
        $str = "<table border=1 cellspacing=1 cellpadding=1>";
        foreach ($list as $k => $v) {
            if ($k == 0) {
                $str .= "<tr>";
                foreach ($v as $kk => $vv) {
                    $str .=  "<td>" . $kk . "</td>";
                }
                $str .= "</tr>";
            }
            $str .= "<tr>";
            foreach ($v as $kk => $vv) {
                $str .= "<td>" . $vv . "</td>";
            }
            $str .= "</tr>";
        }
        $str .= "</table>";
        echo $str;
    }
}	    
?>