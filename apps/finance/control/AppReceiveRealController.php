<?php
/**
 *  -------------------------------------------------
 *   @file		: AppReceiveRealController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-31 12:17:00
 *   @update	:
 *  -------------------------------------------------
 */
class AppReceiveRealController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('download');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{   //获取来源
        $model = new CustomerSourcesModel(1);
        $source_list = $model->getSourcesPay();
		$jzModel = new AppJiezhangModel(29);
		$new_year = $jzModel->getYear();
		$qihao_all = array();
		if(count($new_year))
		{
			$newyearss  = $new_year[0]['year'];
			$rel = $jzModel->getQihao($newyearss);
			foreach ($rel as $value)
			{
				$qihao_all[] = $value['qihao'];
			}
		}
		$this->render('app_receive_real_search_form.html',array(
			'bar'=>Auth::getBar(),
			'source_list'=>$source_list,
			'year_list'=>$new_year,
			'all_qihao'=>$qihao_all,
			'view'=>new AppReceiveRealView(new AppReceiveRealModel(29))));
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
			'from_ad'	=> _Request::get('from_ad'),
			'real_number'	=> _Request::get('real_number'),
			'should_number'	=> _Request::get('should_number'),
			'pay_tiime_start'	=> _Request::get('pay_tiime_start'),
			'pay_tiime_end' => _Request::get('pay_tiime_end'),
			'start_year' => 	_Request::get('start_year'),
			'start_qihao' => 	_Request::get('start_qihao'),
			'end_year' => 	_Request::get('end_year'),
			'end_qihao' => 	_Request::get('end_qihao'),
			//'参数' = _Request::get("参数");
		);
		$page = _Request::getInt("page",1);
		$where = array();
		$wheres = array();
		$wheree = array();
        if($args['from_ad'])
		{
			$where['from_ad'] = $args['from_ad'];
		}
		if($args['real_number'])
		{
			$where['real_number'] = $args['real_number'];
		}
		if($args['should_number'])
		{
			$where['should_number'] = $args['should_number'];
		}
		if($args['pay_tiime_start'])
		{
			$where['pay_tiime_start'] = $args['pay_tiime_start'];
		}
		if($args['pay_tiime_end'])
		{
			$where['pay_tiime_end'] = $args['pay_tiime_end'];
		}
		if(!empty($args['start_year'])){
			$wheres['start_year'] = $args['start_year'];
		}
		if(!empty($args['start_qihao'])){
			$wheres['start_qihao'] = $args['start_qihao'];
		}
		if(!empty($args['end_year'])){
			$wheree['end_year'] = $args['end_year'];
		}
		if(!empty($args['end_qihao'])){
			$wheree['end_qihao'] = $args['end_qihao'];
		}

		$model = new AppJiezhangModel(29);
        if($wheres){
			$s_time = $model->getJiezhangtimes($wheres);
			$where['pay_tiime_start'] = $s_time;
		}
		if($wheree){
			$e_time = $model->getJiezhangtimee($wheree);
			$where['pay_tiime_end'] = $e_time;
		}


        $model = new AppReceiveRealModel(29);
		$data = $model->pageList($where,$page,10,false);
		$sourceModel = new CustomerSourcesModel(1);
        if($data['data']){
            foreach ($data['data'] as $key => $val){
                $data['data'][$key]['ad_name'] = $sourceModel->getSourceNameById($val['from_ad']);
            }
        }
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_receive_real_search_page';
		$this->render('app_receive_real_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}


    public function download() {
        $args = array(
			'from_ad'	=> _Request::get('from_ad'),
			'real_number'	=> _Request::get('real_number'),
			'should_number'	=> _Request::get('should_number'),
			'pay_tiime_start'	=> _Request::get('pay_tiime_start'),
			'pay_tiime_end' => _Request::get('pay_tiime_end'),
			'start_year' => 	_Request::get('start_year'),
			'start_qihao' => 	_Request::get('start_qihao'),
			'end_year' => 	_Request::get('end_year'),
			'end_qihao' => 	_Request::get('end_qihao'),
		);
		$where = array();
		$wheres = array();
		$wheree = array();
        if($args['from_ad'])
		{
			$where['from_ad'] = $args['from_ad'];
		}
		if($args['real_number'])
		{
			$where['real_number'] = $args['real_number'];
		}
		if($args['should_number'])
		{
			$where['should_number'] = $args['should_number'];
		}
		if($args['pay_tiime_start'])
		{
			$where['pay_tiime_start'] = $args['pay_tiime_start'];
		}
		if($args['pay_tiime_end'])
		{
			$where['pay_tiime_end'] = $args['pay_tiime_end'];
		}
		if(!empty($args['start_year'])){
			$wheres['start_year'] = $args['start_year'];
		}
		if(!empty($args['start_qihao'])){
			$wheres['start_qihao'] = $args['start_qihao'];
		}
		if(!empty($args['end_year'])){
			$wheree['end_year'] = $args['end_year'];
		}
		if(!empty($args['end_qihao'])){
			$wheree['end_qihao'] = $args['end_qihao'];
		}

		$model = new AppJiezhangModel(29);
        if($wheres){
			$s_time = $model->getJiezhangtimes($wheres);
			$where['pay_tiime_start'] = $s_time;
		}
		if($wheree){
			$e_time = $model->getJiezhangtimee($wheree);
			$where['pay_tiime_end'] = $e_time;
		}

		$data = $model->getInfoList($where);
        $title = array('实收单号','应收单号','结算商','银行交易流水号','收款时间','实收金额','制单时间','制单人');
        $content = array();
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $k => $v) {
                $val = array($v['real_number'],$v['should_number'],$v['ad_name'],$v['bank_serial_number'],$v['pay_tiime'],$v['total'],$v['maketime'],$v['makename']);
                $val = eval('return ' . iconv('utf-8', 'gbk', var_export($val, true) . ';'));
                $content[] = $val;
            }
        }
        $ymd = date("Ymd_His", time() + 8 * 60 * 60);
        header("Content-Disposition: attachment;filename=" . iconv('utf-8', 'gbk', '实收单列表') . $ymd . ".csv");
        $fp = fopen('php://output', 'w');
        $title = eval('return ' . iconv('utf-8', 'gbk', var_export($title, true) . ';'));
        fputcsv($fp, $title);
        foreach ($content as $k => $v) {
            fputcsv($fp, $v);
        }
        fclose($fp);
        exit;
    }
	public function return_content()
	{
		$result = array('success' => 0,'error' => '');
		$year = !empty($_REQUEST['years'])?$_REQUEST['years']:date('Y');

		$payrealmodel = new AppJiezhangModel(29);
		$res_list = $payrealmodel->getQihao($year);
		$htmls = '<option value="">月</option>';
		foreach($res_list as $v)
		{
			$htmls .= "<option value='".$v['qihao']."'>".$v['qihao']."</option>";
		}

		$result['success'] = 1;
		$result['html'] = $htmls;
		Util::jsonExit($result);
	}

}

?>