<?php
/**
 *  -------------------------------------------------
 *   @file		: TestController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: JUAN <82739364@qq.com>
 *   @date		: 2015-01-14 11:04:15
 *   @update	:
 *  仓储管理-数据特殊处理（快捷方式）---JUAN 不许开放给业务人员
 *  -------------------------------------------------
 */


class WarehouseDealController extends CommonController
{
	protected $whitelist = array('import_company','import_warehouse','batinsert');
	public function index($params)
	{
		$this->render('warehouse_deal_show.html',array(
                    'dd'	=> new DictView(new DictModel(1)),
					'view'=>new WarehouseBillView(new WarehouseBillModel(21)),
                    'companylist' => $this->company(),
				    'time'=>date("Y-m-d H:i:s",time())
                    )
                        );
	}
	//add
	public function addMbill($params)
	{
		$result = array('success' => 0,'error' =>'');
		//var_dump($_REQUEST);exit;
		$from_company_info =explode("|", _Request::get('from_company_id')) ;
		$to_company_info =explode("|", _Request::get('to_company_id')) ;
		$to_warehouse_info =explode("|", _Request::get('to_warehouse_id')) ;
		//var_dump($from_company_info);exit;
		$from_company_id = $from_company_info[0];
		$from_company_name =$from_company_info[1];
		$to_company_id =$to_company_info[0];
		$to_company_name = $to_company_info[1];
		$to_warehouse_id =$to_warehouse_info[0];
		$to_warehouse_name =$to_warehouse_info[1];
		$order_sn = _Request::get('order_sn');
		$bill_info = array(
			'bill_type' => 'M',
			'from_company_id' => $from_company_id,
			'from_company_name' => $from_company_name,
			'to_company_id' => $to_company_id,
			'to_company_name' => $to_company_name,
			'to_warehouse_id' => $to_warehouse_id,
			'to_warehouse_name' => $to_warehouse_name,
			'order_sn' => $order_sn,
			'create_time' => $params['create_time'],
			'bill_note' => '技术处理单据'
		);
		$goods = $params['goods_id'];
		$goods_arr =explode(',', $goods);
		//var_dump($goods_arr);exit;

		$model = new WarehouseBillInfoMModel(22);
		$result = $model->addBillauto($bill_info,$goods_arr);

		Util::jsonExit($result);
	}

    //批量修改配货状态
    public function up_order_dvtype($params)
    {
        $result = array('success' => 0,'error' =>'');

        $order_sn= _Request::get('order_sn');
        $delivery_status = _Request::get('delivery_status');

        if($order_sn==''){
            $result['error'] = '订单号不能为空';
            Util::jsonExit($result);
        }

        if($delivery_status==''){
            $result['error'] = '请选择配货状态';
            Util::jsonExit($result);
        }

        $order_sn_str = '';
        $args = array();
        $args['order_sn'] = str_replace(' ',',',$order_sn);
        $args['order_sn'] = str_replace('，',',',$order_sn);
        $args['order_sn'] = str_replace("\n",',',$order_sn);
        $tmp = explode(",", $args['order_sn']);
        $model = new SalesModel(27);
        $orderInfo = array();
        foreach($tmp as $order_sn){
            if($order_sn != ''){
                $check = $model->check_select_order_sn($order_sn);
                if(empty($check) || $check == false){
                    $result['error'] = '订单号‘'.$order_sn.'’不存在';
                    Util::jsonExit($result);
                }

                if(!in_array($check['order_pay_status'], array('3','4'))){
                    $result['error'] = '订单号‘'.$order_sn.'’的支付状态不是已付款或财务备案';
                    Util::jsonExit($result);
                }

                if($check['delivery_status'] == $delivery_status){
                    $result['error'] = '订单号‘'.$order_sn.'’的配货状态和要更改的配货状态相同！';
                    Util::jsonExit($result);
                }
                $order_sn_str .= "'$order_sn',";
                $orderInfo[] = $check;
            }
        }
        $order_sn_str = rtrim($order_sn_str,',');

        $res = $model->up_order_dvtype($order_sn_str,$delivery_status);
        if($res == false){
            $result['error'] = '修改失败';
        }else{
            //添加訂單日誌
            if(!empty($orderInfo)){
                foreach ($orderInfo as $key => $value) {
                    $data = array(
                        'order_id'=>$value['id'],
                        'order_status'=>$value['order_status'],
                        'shipping_status'=>$value['send_good_status'],
                        'pay_status'=>$value['order_pay_status'],
                        'create_user'=>$_SESSION['userName'],
                        'create_time'=>date("Y-m-d H:i:s"),
                        'remark'=>'更改配货状态',
                    );
                    $model->insertOrderAction($data); 
                }
            }
            $result['success'] =1;
            $result['error'] = '修改成功';
        }
        Util::jsonExit($result);
    }


	public function addMbillS($params)
	{
		$result = array('success' => 0,'error' =>'');
		$from_company_info =explode("|", _Request::get('from_company_id')) ;
		$from_company_id = $from_company_info[0];
		$from_company_name =$from_company_info[1];
		$order_sn = _Request::get('order_sn');
		if(isset($params['bill_note']) && $params['bill_note'] != ''){
			$bill_note=$params['bill_note'];
		}else{
			$bill_note='特殊处理单据';
		}

		$bill_info = array(
			'bill_type' => 'S',
			'from_company_id' => $from_company_id,
			'from_company_name' => $from_company_name,
			'order_sn' => $order_sn,
			'create_time' => $params['create_time'],
			'bill_note' => $bill_note
			//bill_note' => '技术处理单据'
		);

		$goods = $params['goods_id'];
		$goods_arr =explode(',', $goods);
		$shijia = $params['shijia'];
		
        if($from_company_id == ''){
        	$result=array('success' => 0 , 'error' =>'请选择出库公司');
        	Util::jsonExit($result);
        }
        if($order_sn == ''){
        	$result=array('success' => 0 , 'error' =>'请填写订单号');
        	Util::jsonExit($result);
        }
        $now_time = date('Y-m');
        $crt_time = date('Y-m', strtotime($bill_info['create_time']));
        if($now_time != $crt_time){
            $result=array('success' => 0 , 'error' =>'只能选择当月时间');
            Util::jsonExit($result);
        }
        $SaleModel = new SalesModel(27);
        $res=$SaleModel->CheckOrderSn($order_sn);
        if(empty($res)){
        	$result=array('success' => 0 , 'error' =>'订单号不存在');
        	Util::jsonExit($result);
        }
        if($goods == ''){
        	$result=array('success' => 0 , 'error' =>'请选填写货号');
        	Util::jsonExit($result);
        }
        
		$model = new WarehouseBillInfoMModel(22);
		$res=$model->checkGoods($goods_arr, $from_company_id);
		//$result=array('success' => 0 , 'error' =>'ss');
		//Util::jsonExit($result);
		if($res['error'] != 0 ){
			$result=$res['result'];
			Util::jsonExit($result);
		}
		
		if($shijia == ''){
			$result=array('success' => 0 , 'error' =>'请填写价格');
			Util::jsonExit($result);
		}
		$price_arr=$model->getGoodsPrice($goods,$shijia);
		//$price_arr =explode(',', $shijia);
		$result = $model->addBillauto($bill_info,$goods_arr,$price_arr);

		Util::jsonExit($result);
	}


        //更改货品信息
        public function modifyGoodsInfo($params) {
            //var_dump($params);exit;
            $c_model = new CompanyModel(1);
            $w_model = new WarehouseModel(21);
            $result = array('success' => 0,'error' =>'');
            if (isset($params['warehouse_id']) && !empty($params['warehouse_id'])){
                $warehouse_id = $params['warehouse_id'];
                $warehouse_name = $w_model->getWarehosueNameForId($params['warehouse_id']);
            }else {
                $warehouse_id='';
                $warehouse_name='';
            }
            if (isset($params['company_id']) && !empty($params['company_id'])){
                $company_id = $params['company_id'];
                $company_name = $c_model->getCompanyName($params['company_id']);
            }else {
                $company_id ='';
                $company_name ='';
            }
            if (isset($params['is_on_sale']) && !empty($params['is_on_sale'])){
                $is_on_sale = $params['is_on_sale'];
            }else {
                $is_on_sale = '';
            }

            $where = array(
                'goods_id' => $params['goods_id'],
                'company_id' => $company_id,
                'warehouse_id' => $warehouse_id,
                'is_on_sale' => $is_on_sale,
                'company' => $company_name,
                'warehouse' => $warehouse_name
            );
            $model = new WarehouseGoodsModel(21);
            $rs = $model->modifyGoodsInfo($where);
            if ($rs) {
                $result['success'] = 1;
            }else {
                $result['error'] = 1;
            }
            Util::jsonExit($result);
        }

	public function import_company()
	{
		$result = array('success' => 0,'error' =>'');
		$ret=ApiModel::jxc_api('RequestJxcProList',array());
		$list = $ret['return_msg']['list'];

		$api = new ImportDataModel(1);
		if($api->addCompanyData($list))
		{
			echo '公司信息导入成功';exit;
		}
		echo '公司信息导入失败';exit;
	}

	public function import_warehouse()
	{
		$result = array('success' => 0,'error' =>'');
		$ret=ApiModel::jxc_api('RequestJxcWarehouse',array());
		$list = $ret['return_msg']['list'];

		$api = new ImportDataModel(21);
		if($api->addWarehouseData($list))
		{
			echo '仓库信息导入成功';exit;
		}
		echo '仓库信息导入失败';exit;
	}
        public function company()
	{
		$model     = new CompanyModel(1);
		$company   = $model->getCompanyTree();//公司列表
		return $company;
	}

	public function batinsert(){
        $bigen=time();
        $result=array('success'=>0,'error'=>'');
        $upload_name = $_FILES['file_csv'];
        $tmp_name = $upload_name['tmp_name'];
        if (!$tmp_name) {
            $result['error'] = '文件不能为空';
            Util::jsonExit($result);
        }
        if (Upload::getExt($upload_name['name']) != 'csv') {
            $result['error'] = '请上传csv格式文件';
            Util::jsonExit($result);
        }
        $goods_ids = array();//批量去重数组
        $error=array();//错误信息
        $error['flag']=true;//错误标示
        $file = fopen($tmp_name, 'r');
        $i=0;

		$data = array();

		$xls_content = "货号,单据类型,新系统货品状态\r\n";

        while ($datav = fgetcsv($file)) {
            if($i==0){
                $i++;
                continue;
            }
            //货号的判断机制
            $goods_id = trim(iconv('gbk','utf-8',$datav[0]));

            $model = new WarehouseGoodsModel(21);
			$new_status = $model->getKuCunStatus($goods_id);
			$billModel = new WarehouseBillModel(21);
			$new_last_bill = $billModel->getLastBill($goods_id);

			$xls_content .= $goods_id . ",";
			$xls_content .= $new_last_bill['bill_type'] . ",";
			$xls_content .= $new_last_bill['new_status']. "\n";

            $i++;
        }

		header("Content-type: text/html; charset=gbk");
		header("Content-type:aplication/vnd.ms-excel");
		header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv");
		echo iconv("utf-8", "gbk", $xls_content);

    }

}
?>