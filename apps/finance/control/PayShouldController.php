<?php
/**
 *  -------------------------------------------------
 *   @file		: PayShouldController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-08 16:48:26
 *   @update	:
 *  -------------------------------------------------
 */
class PayShouldController extends CommonController
{
	protected $smartyDebugEnabled = true;
	protected $whitelist  = array('download','search');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$processorModel = new ApiProcessorModel();
		$process_list = $processorModel->GetSupplierList();
		$this->render('pay_should_search_form.html',array(
			'bar'=>Auth::getBar(),
			'view'=>new PayShouldView(new PayShouldModel(29)),
			'process_list' => $process_list
		));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$page_action = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'company' => 	_Request::get('company'),
			'status' => 	_Request::get('status'),
			'prc_id' => 	_Request::get('prc_id'),
			'pay_type' => 	_Request::get('pay_type'),
			'pay_status' => 	_Request::get('pay_status'),
			'pay_should_all_name' => 	_Request::get('pay_should_all_name'),
			'make_time_s' => 	_Request::get('make_time_s'),
			'make_time_e' => 	_Request::get('make_time_e'),
			'check_time_s' => 	_Request::get('check_time_s'),
			'check_time_e' => 	_Request::get('check_time_e'),
			'down_info' => 	_Request::get('down_info')?_Request::get('down_info'):'',
		);

		$where = array();
		if(!empty($page_action['company'])){
			$where['company'] = $page_action['company'];
		}
		if(!empty($page_action['pay_status'])){
			$where['pay_status'] = $page_action['pay_status'];
		}
		if(!empty($page_action['status'])){
			$where['status'] = $page_action['status'];
		}
		if(!empty($page_action['prc_id'])){
			$where['prc_id'] = $page_action['prc_id'];
		}
		if(!empty($page_action['pay_type'])){
			$where['pay_type'] = $page_action['pay_type'];
		}
		if(!empty($page_action['pay_should_all_name'])){
			$where['pay_should_all_name'] = $page_action['pay_should_all_name'];
		}
		if(!empty($page_action['make_time_s'])){
			$where['make_time_s'] = $page_action['make_time_s'];
		}
		if(!empty($page_action['make_time_e'])){
			$where['make_time_e'] = $page_action['make_time_e'];
		}
		if(!empty($page_action['check_time_s'])){
			$where['check_time_s'] = $page_action['check_time_s'];
		}
		if(!empty($page_action['check_time_e'])){
			$where['check_time_e'] = $page_action['check_time_e'];
		}

        if(SYS_SCOPE == 'zhanting'){
            $where['hidden'] = '0';
        }
		$payshouldmodel = new PayShouldModel(29);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		
		//导出功能
		if($page_action['down_info']=='down_info'){
			$data = $payshouldmodel->getShouldListByWhere($where,$page,100000000,false);
			$all_price = $payshouldmodel->getShouldallprice($where);
			$this->download($data);
			exit;	
		}
		$data = $payshouldmodel->getShouldListByWhere($where,$page,10,false);

		$all_price = $payshouldmodel->getShouldallprice($where);

		$pageData = $data;
		$pageData['filter'] = $page_action;
		$pageData['jsFuncs'] = 'pay_should_search_page';
		$this->render("pay_should_search_list.html",array(
			'page_list'=>$data,
			'all_price'=>$all_price,
			'pa'=>Util::page($pageData),
			'dd'=>new DictView(new DictModel(1))
			));
	}

	//点击生成应付单，先检查数据的准确定和计算总金额
	public function shouldAddCheck($params)
	{
		$result = array('success' => 0,'error' =>'');
		$ids = $params['ids'];

		$res = $this->checkShouldCon($ids);
		$result['error'] = $res['error'];
		if($res['success'])
		{
			$applyModel = new PayApplyModel(29);
			$total = $applyModel->getTotalOfIds($ids);
			$result['success'] = 1;
			$result['total'] = $total;
		}
		Util::jsonExit($result);
	}


//生成应付单提交
	public function shouldAddSub($params)
	{
		$result = array('success' => 0,'error' =>'');
		$ids = $params['ids'];

		if($this->checkShouldCon($ids))//检查数据成功
		{
			//添加数据
			$model = new PayShouldModel(29);
			$res = $model->add($ids);
			if($res['error'])
			{
				$result['error'] = '生成应付单 CWYF'.$res['id'];
				$result['success']	= 1;
				$result['id'] = $res['id'];
			}else{
				$result['error'] = '生成应付单失败';
			}
		}
		Util::jsonExit($result);
	}
/*------------------------------------------------------ */
	//-- 检查生成应付单的数据 是否同一个结算商，同一个类型，是否是“待生成应付单”状态。是否已经存在在其他应付单里面
	// 返回结果
	//-- by Zlj
	/*------------------------------------------------------ */
	public function checkShouldCon($ids)
	{
		$result = array('success' => 0,'error' =>'');
		$applyModel = new PayApplyModel(29);
		if(!$applyModel->checkDistinct('prc_id',$ids))
		{
			$result['error'] = '所选单据不是同一个结算商，不能提交。';
			return $result;
		}
		if(!$applyModel->checkDistinct('pay_type',$ids))
		{
			$result['error'] = '所选单据应付类型不同，不能提交。';
			return $result;
		}
		$ids = explode(',',$ids);
		foreach($ids as $k => $v)
		{
			$gRow = $applyModel->getRow($v);
			if($gRow['pay_number'] != '')
			{
				$result['error'] = '申请单 '.$gRow['pay_apply_number'].' 已经存在于应付单据 '.$gRow['pay_number'].' 中，不能提交。';
				return $result;
			}
			if($gRow['status'] != '5')
			{
				$result['error'] = '申请单 '.$gRow['pay_apply_number'].' 状态不对，不能提交。';
				return $result;
			}
		}
		$result['success'] = 1;
		return $result;
	}

	public function show($params)
	{
		$info_id = $params['id'];
		$payshouldmodel = new PayShouldModel(29);
		$info = $payshouldmodel->getRow($info_id);
		$should_info = $payshouldmodel->getShouldDetail($info_id);
		$num = count($should_info);

		$this->render("pay_should_show.html",array(
			'info' => $info,
			'num'=>$num,
			'bar' => Auth::getViewBar(),
			//'j_list'=>$jiesuanshang_list,
			));
	}

	//详情页面的明细
	public function showlist($params)
	{
		$id = intval($params["id"]);
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'id'	=>$id
		);

		$detailModel = new PayShouldDetailModel(29);
		$where = array('pay_number'=>$id);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$data = $detailModel->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'pay_should_show_page';

		$this->render('pay_should_show_list.html',array(
			'pa' =>Util::page($pageData),
			'data' => $data,
		));
	}

	function checkPass($params){
		$result = array('success' => 0,'error' => '');
		$id = $params['id'];
		$payshouldmodel = new PayShouldModel(29);
		$info = $payshouldmodel->getShouldInfo($id);
		$row = $payshouldmodel->getRow($id);

		if($row['status'] != 1)
		{
			$result['error'] = '只有待审核的状态才能审核';
			Util::jsonExit($result);
		}
		if($info[0]['make_name'] == $_SESSION['userName']){
			$result['error'] ="自己不能审核自己的单子。";
			Util::jsonExit($result);
		}


		if($info[0]['t_cope'] == '0.00'){
			$value['pay_status'] = 3;
		}
		$value['status'] = 2;
		$value['check_name'] = $_SESSION['userName'];
		$value['check_time'] = date('Y-m-d H:i:s');
		if($payshouldmodel->update($value,array('pay_number_id' => $id)))
		{
			$result['success'] = 1;
		}
		Util::jsonExit($result);
	}

	function checkOff($params){
		$result = array('success' => 0,'error' => '');
		$id = $params['id'];
		$payshouldmodel = new PayShouldModel(29);
		$info = $payshouldmodel->getShouldInfo($id);
		$row = $payshouldmodel->getRow($id);

		if($row['status'] != 1)
		{
			$result['error'] = '只有待审核的状态才能取消';
			Util::jsonExit($result);
		}


		$applyModel = new PayApplyModel(29);
		$pay_number = $row['pay_should_all_name'];
		$applyRet = $applyModel->update(array('pay_number'=>'','status'=>'5'),array('pay_number' =>$pay_number));
		$value['status'] = 3;
		$value['check_name'] = $_SESSION['userName'];
		$value['check_time'] = date('Y-m-d H:i:s');
		$shouldRet = $payshouldmodel->update($value,array('pay_number_id' => $id));

		if($shouldRet && $applyRet)
		{
			$result['success'] = 1;
		}
		Util::jsonExit($result);
	}

	//付款
	public function toPay($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = $params['id'];
		$payshouldmodel = new PayShouldModel(29);
		$info = $payshouldmodel->getRow($id);

		if($info['status'] != 2)
		{
			$result['content'] = "已审核状态的单据才能进行付款";
			Util::jsonExit($result);
		}
		if($info['pay_status'] == 3)
		{
			$result['content'] = "已付款完成，不能继续付款。";
			Util::jsonExit($result);
		}


		$wf_price = floatval($info['total_cope']) - floatval($info['total_real']);

		$prc_id = $info['prc_id'];
		$processModel = new ApiProcessorModel();
		$prc_bank_info = $processModel->GetSupplierPay(array('id'),array($prc_id));
		if(!count($prc_bank_info))
		{
			$result['content'] = "供应商".$info['prc_name']."银行  信息不全，请联系补全。";
			Util::jsonExit($result);
		}else{
			$prc_bank_info = $prc_bank_info['data'];
		}

		$result['content'] = $this->fetch('pay_should_topay.html',array(
			'dd' => new DictView(new DictModel(1)),
			'wf_price' => $wf_price,
			'id' => $id,
			'prc_bank_info' => $prc_bank_info
		));
		$result['title'] = '付款';
		Util::jsonExit($result);
	}

	public function pay_form($params)
	{
		$result = array('success' => 0,'error' => '');

		if(empty($params['bank_text']) || empty($params['bank_sn']))
		{
			$result['error'] ="此加工商没有银行信息，请联系相关人员维护";
			Util::jsonExit($result);
		}

		if(empty($params['bank_water']))
		{
			$result['error'] ="银行交易流水号必填";
			Util::jsonExit($result);
		}

		$payment_amount = (float)trim($params['payment_amount']);

		if($payment_amount == 0.00)
		{
			$result['error'] ="付款金额必须大于0";
			Util::jsonExit($result);
		}

		if (empty($params['payment_time'])) {
			$result['error'] ="财务付款时间必选";
			Util::jsonExit($result);
		}
		if($params['payment_time'] > date("Y-m-d")){
			$result['error'] ="付款时间不能大于今天！";
			Util::jsonExit($result);
		}

		$id = $params['id'];
		$payshouldmodel = new PayShouldModel(29);
		$should_info = $payshouldmodel->getShouldInfo($id);

		$y_f_p = $payment_amount + floatval($should_info[0]['total_real']);
		if($should_info[0]['t_cope'] > 0){
			if($y_f_p > $should_info[0]['t_cope']){
				$result['error'] ="实付金额不能大于应付金额！";
				Util::jsonExit($result);
			}
			if($y_f_p < 0){
				$result['error'] ="实付金额有问题！";
				Util::jsonExit($result);
			}
		}elseif($should_info[0]['t_cope'] < 0){
			if($y_f_p < $should_info[0]['t_cope']){
				$result['error'] ="实付金额不能小于应付金额！";
				Util::jsonExit($result);
			}
			if($y_f_p > 0){
				$result['error'] ="实付金额有问题！";
				Util::jsonExit($result);
			}
		}
		$payshouldmodel->updatePayshouldTotal_real($id,$y_f_p);
		//$kaihuhang = $payshouldmodel->selectZhanghuInfo($should_info[0]['prc_id']);

		$data_arr = array(
			'pay_number'	=>$should_info[0]['pay_should_all_name'],
			'pay_type'		=>$should_info[0]['pay_type'],
			'prc_id'		=>$should_info[0]['prc_id'],
			'prc_name'		=>$should_info[0]['prc_name'],
			'company'		=>$should_info[0]['company'],
			'bank_serial_number'=>$params['bank_water'],
			'pay_time'		=>$params['payment_time'],
			'total'			=>$payment_amount,
			'make_time'		=>date("Y-m-d H:i:s"),
			'bank_name'		=>$params['bank_text'],
			'bank_account'	=>$params['bank_sn'],
			'make_name'		=>$_SESSION['userName']
		);
		$payrealmodel = new PayYfRealModel(29);
		$s = $payrealmodel->add($data_arr);
		if($s){
			$s_data = $payrealmodel->updatePayrealallname($s,"CWSF".$s);
			$s_data = $payshouldmodel->getShouldInfo($id);
			if($s_data[0]['t_cope'] == $s_data[0]['total_real']){//如果付款完成改成以付款状态
				$payshouldmodel->updateFinPayshouldStatus($id,3);
			}else{
				$payshouldmodel->updateFinPayshouldStatus($id,2);
			}
			$result['success'] =1;
		}
		Util::jsonExit($result);

	}
	
	public function download($data) {
		
		$dd =new DictModel(1);
		//echo "<pre>";print_r($data['data']);exit;
		if ($data['data']) {
			$down = $data['data'];
			$xls_content = "应付单号,应付类型,结算商,付款周期,应付金额,制单时间,制单人,审核人,审核时间,审核状态,付款状态,财务实付金额\r\n";
			foreach ($down as $key => $val) {
	
				$xls_content .= $val['pay_should_all_name'] . ",";
				$xls_content .= $dd->getEnum('app_pay_should.pay_type',$val['pay_type']). ",";
				$xls_content .= $val['prc_name'] . ",";
				$xls_content .= $dd->getEnum('supplier_pay.method',$val['settle_mode']). ",";
				$xls_content .= $val['total_cope'] . ",";
				$xls_content .= $val['make_time'] . ",";
				$xls_content .= $val['make_name'] . ",";
				$xls_content .= $val['check_time'] . ",";
				$xls_content .= $val['check_name'] . ",";
				$xls_content .= $dd->getEnum('app_pay_should.status',$val['status']) . ",";
				$xls_content .= $dd->getEnum('pay_status',$val['pay_status']) . ",";
				$xls_content .= $val['total_real'] . "\n";
	
					
			}
		} else {
			$xls_content = '没有数据！';
		}
		header("Content-type: text/html; charset=gbk");
		header("Content-type:aplication/vnd.ms-excel");
		header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv");
		echo iconv("utf-8", "gbk", $xls_content);
	
	}
}

?>