<?php
/**
 *  -------------------------------------------------
 *   @file		: PayYfRealController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-08 17:48:20
 *   @update	:
 *  -------------------------------------------------
 */
class PayYfRealController extends CommonController
{
	protected $smartyDebugEnabled = true;
	protected $whitelist  = array('download','search');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$payrealmodel = new PayYfRealModel(29);
		$jizhang_list = $payrealmodel->getJiezhangList();
		$new_year = array();
		$qihao_all = array();
		foreach($jizhang_list as $k=>$v)
		{
			$new_year[$k] = $v['year'];
		}

		if(count($new_year))
		{
			$newyearss  = $new_year[0];
			$rel = $payrealmodel->getJiezhangInfoList($newyearss);
			foreach ($rel as $value)
			{
				$qihao_all[] = $value['qihao'];
			}
		}

		$processorModel = new ApiProcessorModel();
		$process_list = $processorModel->GetSupplierList();

		$this->render('pay_yf_real_search_form.html',array(
			'bar'=>Auth::getBar(),
			'view'=>new AppApplyRealPayView(new AppApplyRealPayModel(29)),
			'year_list'=>$new_year,
			'all_qihao_s'=>$qihao_all,
			'all_qihao_e'=>$qihao_all,
			'process_list' => $process_list
		));

	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$page_action = array(
			'mod'	=> $_REQUEST["mod"],
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'company' => 	_Request::get('company'),
			'prc_id'	=> 		_Request::get('prc_id'),
			'pay_type' => 	_Request::get('pay_type'),
			'pay_real_number' => 	_Request::get('pay_real_number'),
			'pay_number' => 	_Request::get('pay_number'),
			'make_name' => 	_Request::get('make_name'),
			'pay_time_s' => 	_Request::get('make_time_s'),
			'pay_time_e' => 	_Request::get('make_time_e'),
			'start_year' => 	_Request::get('start_year'),
			'start_qihao' => 	_Request::get('start_qihao'),
			'end_year' => 	_Request::get('end_year'),
			'end_qihao' => 	_Request::get('end_qihao'),
			'down_info' => 	_Request::get('down_info')?_Request::get('down_info'):'',
		);

		$where = array();
		$wheres = array();
		$wheree = array();
		if(!empty($page_action['company'])){
			$where['company'] = $page_action['company'];
		}
		if(!empty($page_action['prc_id'])){
			$where['prc_id'] = $page_action['prc_id'];
		}
		if(!empty($page_action['pay_type'])){
			$where['pay_type'] = $page_action['pay_type'];
		}
		if(!empty($page_action['pay_real_number'])){
			$where['pay_real_number'] = $page_action['pay_real_number'];
		}
		if(!empty($page_action['pay_number'])){
			$where['pay_number'] = $page_action['pay_number'];
		}
		if(!empty($page_action['make_name'])){
			$where['make_name'] = $page_action['make_name'];
		}
		if(!empty($page_action['pay_time_s'])){
			$where['pay_time_s'] = $page_action['pay_time_s'];
		}
		if(!empty($page_action['pay_time_e'])){
			$where['pay_time_e'] = $page_action['pay_time_e'];
		}
		if(!empty($page_action['start_year'])){
			$wheres['start_year'] = $page_action['start_year'];
		}
		if(!empty($page_action['start_qihao'])){
			$wheres['start_qihao'] = $page_action['start_qihao'];
		}
		if(!empty($page_action['end_year'])){
			$wheree['end_year'] = $page_action['end_year'];
		}
		if(!empty($page_action['end_qihao'])){
			$wheree['end_qihao'] = $page_action['end_qihao'];
		}
		$payrealmodel = new PayYfRealModel(29);
		if($wheres){
			$s_time = $payrealmodel->getJiezhangtimes($wheres);
			$where['pay_time_s'] = $s_time;
		}
		if($wheree){
			$e_time = $payrealmodel->getJiezhangtimee($wheree);
			$where['pay_time_e'] = $e_time;
		}
        if(SYS_SCOPE == 'zhanting'){
            $where['hidden'] = '0';
        }
		if($page_action['down_info']=='down_info'){//导出
			/* $spay_list = C::$payType;
			$content = array();
			$rows = $payrealmodel->getRealinfoByWhere($where);
			$title = array('财务实付单号','财务应付单号','实付类型','供货商/结算商','银行交易流水号','收款方帐号','财务付款时间','实付金额','制单时间','制单人');
			if (is_array($rows)){
			   foreach($rows as $k=>$v)
			   {
					$val = array($v['pay_real_all_name'],$v['pay_number'],$spay_list[$v['pay_type']],$v['prc_name'],$v['bank_serial_number'],$v['bank_name'].$v['bank_account'],$v['pay_time'],$v['total'],$v['make_time'],$v['make_name']);
					$val = eval('return '.iconv('utf-8','gbk',var_export($val,true).';')) ;
					$content[] = $val;
			   }
			}
			$this->down_csv('pay_real',$title,$content);
			exit; */
			$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
			$data = $payrealmodel->getRealListByWhere($where,$page,10000000,false);
			
			$all_price = $payrealmodel->getRealallprice($where);
			
			if($page_action['start_year'] || $page_action['end_year']){
				$newyearss = $page_action['start_year'];
				$newyearss = $page_action['end_year'];
			}
			$this->download($data);
			exit;
			
		}else{
			$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
			$data = $payrealmodel->getRealListByWhere($where,$page,10,false);

			$all_price = $payrealmodel->getRealallprice($where);

			if($page_action['start_year'] || $page_action['end_year']){
				$newyearss = $page_action['start_year'];
				$newyearss = $page_action['end_year'];
			}

			$pageData = $data;
			$pageData['filter'] = $page_action;
			$pageData['jsFuncs'] = 'pay_yf_real_search_page';

			$this->render("pay_yf_real_search_list.html",array(
				'dd' => new DictView(new DictModel(1)),
				'page_list'=>$data,
				'all_price'=>$all_price,
				'pa'=>Util::page($pageData),
				));
		}

	}


	public function return_content()
	{
		$result = array('success' => 0,'error' => '');
		$year = !empty($_REQUEST['years'])?$_REQUEST['years']:date('Y');

		$payrealmodel = new PayYfRealModel(29);
		$res_list = $payrealmodel->getJiezhangInfoList($year);
		$htmls = '<option value="">月</option>';
		foreach($res_list as $v)
		{
			$htmls .= "<option value='".$v['qihao']."'>".$v['qihao']."</option>";
		}

		$result['success'] = 1;
		$result['html'] = $htmls;
		Util::jsonExit($result);
	}


	//转换编码格式，导出csv数据
/* 	public function down_csv($name,$title,$content)
	{
		$ymd = date("Ymd_His", time()+8*60*60);
		header("Content-Disposition: attachment;filename=".$name.$ymd.".csv");
		$fp = fopen('php://output', 'w');
		$title = eval('return '.iconv('utf-8','gbk',var_export($title,true).';')) ;
		fputcsv($fp, $title);
	   foreach($content as $k=>$v)
	   {
			fputcsv($fp, $v);
	   }
		fclose($fp);exit;
	} */
	
	//下载导出
	public function download($data) {
		//var_dump($_REQUEST);exit;
		
		$dd =new DictModel(1);
		//echo "<pre>";print_r($data['data']);exit;
		if ($data['data']) {
			$down = $data['data'];
			$xls_content = "实付单号,应付单号,实付类型,结算商,银行交易流水,收款方帐号,付款时间,实付金额,制单时间,制单人\r\n";
			foreach ($down as $key => $val) {
	
				$xls_content .= $val['pay_real_all_name'] . ",";
				$xls_content .= $val['pay_number']. ",";
				$xls_content .= $dd->getEnum('app_pay_should.pay_type',$val['pay_type']) . ",";
				$xls_content .= $val['prc_name']. ",";
				$xls_content .= $val['bank_serial_number'] . ",";
				$xls_content .= $val['bank_name'].$val['bank_account'] . ",";
				$xls_content .= $val['pay_time'] . ",";
				$xls_content .= $val['total'] . ",";
				$xls_content .= $val['make_time']. ",";
				$xls_content .= $val['make_name'] . "\n";
	
					
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