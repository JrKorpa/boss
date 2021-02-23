<?php

/**
 *  -------------------------------------------------
 *   @file		: AppReceiveApplyController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-26 18:44:32
 *   @update	:
 *  -------------------------------------------------
 */
class AppReceiveApplyController extends CommonController {

    protected $smartyDebugEnabled = false;
    protected $whitelist = array('downLoad','downloadDemo');

    /**
     * 	index，搜索框
     */
    public function index($params) {
        //获取来源
        $model = new CustomerSourcesModel(1);
        $source_list = $model->getSourcesPay();
        $this->render('app_receive_apply_search_form.html', array('bar' => Auth::getBar(), 'source_list' => $source_list));
    }

    /**
     * 	search，列表
     */
    public function search($params) {
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'apply_number' => _Request::getString('apply_number'),
            'cash_type' => _Request::getString('cash_type'),
            'from_ad' => _Request::getString('from_ad'),
            'status' => _Request::getString('status'),
            'storage_mode' => _Request::getString('storage_mode'),
            'check_sale_number' => _Request::getString('check_sale_number'),
            'make_time_start' => _Request::getString('make_time_start'),
            'make_time_end' => _Request::getString('make_time_end'),
            'check_time_start' => _Request::getString('check_time_start'),
            'check_time_end' => _Request::getString('check_time_end'),
        );
        $page = _Request::getInt("page", 1);
        $where = array(
            'apply_number' => _Request::getString('apply_number'),
            'cash_type' => _Request::getString('cash_type'),
            'from_ad' => _Request::getString('from_ad'),
            'status' => _Request::getString('status'),
            'check_sale_number' => _Request::getString('check_sale_number'),
            'make_time_start' => _Request::getString('make_time_start'),
            'make_time_end' => _Request::getString('make_time_end'),
            'check_time_start' => _Request::getString('check_time_start'),
            'check_time_end' => _Request::getString('check_time_end'),
        );

        if (in_array(1, _Request::getList('storage_mode'))) {
            $where['sale_total_cha'] = 1;
        }
        if (in_array(2, _Request::getList('storage_mode'))) {
            $where['make_total_cha'] = 1;
        }

        $model = new AppReceiveApplyModel(29);
        $data = $model->pageList($where, $page, 10, false);
		$sourceModel = new CustomerSourcesModel(1);
        if($data['data']){
            foreach ($data['data'] as $key => $val){
                $data['data'][$key]['ad_name'] = $sourceModel->getSourceNameById($val['from_ad']);
            }
        }
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'app_receive_apply_search_page';
        $this->render('app_receive_apply_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data,
        ));
    }

    public function downLoad() {
        $where = array(
            'apply_number' => _Request::getString('apply_number'),
            'cash_type' => _Request::getString('cash_type'),
            'from_ad' => _Request::getString('from_ad'),
            'status' => _Request::getString('status'),
            'check_sale_number' => _Request::getString('check_sale_number'),
            'make_time_start' => _Request::getString('make_time_start'),
            'make_time_end' => _Request::getString('make_time_end'),
            'check_time_start' => _Request::getString('check_time_start'),
            'check_time_end' => _Request::getString('check_time_end'),
        );
        //var_dump($_REQUEST);die;
        if (in_array(1, _Request::getList('storage_mode'))) {
            $where['sale_total_cha'] = 1;
        }
        if (in_array(2, _Request::getList('storage_mode'))) {
            $where['make_total_cha'] = 1;
        }

        $model = new AppReceiveApplyModel(29);
        $data = $model->allData($where);
        //var_dump($data);
        $content = "";
        header("Content-type: text/html; charset=gbk");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=receiveApply.csv");

        $title = array(
            '应收申请单号',
            '收款类型',
            '订单来源/结算商',
            '外部金额',
            'BDD金额',
            '制单误差',
            '应收现金',
            '制单时间',
            '制单人',
            '审核时间',
            '审核人',
            '状态',
            '销账金额',
            '销账误差',
            '账务误差',
            '应收单号');
        if (empty($data)) {

        } else {
            //获取所有的状态
            foreach ($data as $k => $v) {
                $cash_type = $this->dd->getEnum("rec_cash_type", $v['cash_type']);
                $status = $this->dd->getEnum("rec_apply_status", $v['status']);
                $val = array(
                    $v['apply_number'],
                    $cash_type,
                    $v['ad_name'],
                    !empty($v['external_total_all']) ? $v['external_total_all'] : 0, //外部金额
                    $v['kela_total_all'], //BDD金额
                    $v['make_total_cha'], //制单误差
                    $v['total'],
                    $v['make_time'],
                    $v['make_name'],
                    $v['check_time'],
                    $v['check_name'],
                    $status,
                    $v['jxc_total_all'], //销账金额
                    $v['sale_total_cha'], //销账误差
                    $v['jxc_total_all'] - $v['external_total_all'], //财务误差	账务误差：=销账金额-外部金额
                    $v['should_number']);

                $val = eval('return ' . iconv('utf-8', 'gbk', var_export($val, true) . ';'));
                $content[] = $val;
            }

            $this->detail_csv('应收申请单列表', $title, $content);
        }
//       var_dump($content);
        echo iconv('utf-8', 'gbk', $content);
        exit;
    }

    //转换编码格式，导出csv数据
    public function detail_csv($name, $title, $content) {

        $ymd = date("Ymd_His", time() + 8 * 60 * 60);
        header("Content-Disposition: attachment;filename=" . iconv('utf-8', 'gbk', $name) . $ymd . ".csv");
        $fp = fopen('php://output', 'w');
        $title = eval('return ' . iconv('utf-8', 'gbk', var_export($title, true) . ';'));
        fputcsv($fp, $title);
        foreach ($content as $k => $v) {
            fputcsv($fp, $v);
        }
        fclose($fp);
        exit;
    }

    /**
     * 	add，渲染添加页面
     */
    public function add() {
        $view = new AppReceiveApplyView(new AppReceiveApplyModel(29));
        $model = new CustomerSourcesModel(1);
        $source_list = $model->getSourcesPay();
        $this->render('app_receive_apply_info.html', array('view' => $view,'source_list'=>$source_list));
    }

    /**
     * 	edit，渲染修改页面
     */
    public function edit($params) {
        $id = intval($params["id"]);
        $modelDetail = new AppReceiveApplyDetailModel(29);
        $dataDetail = $modelDetail->getDataOfapply_Id($id,1);

		$view = new AppReceiveApplyView(new AppReceiveApplyModel($id,29));
        $model = new CustomerSourcesModel(1);
        $source_list = $model->getSourcesPay();

        $this->render('app_receive_apply_info_edit.html', array(
			'view' => $view,
			'source_list'=>$source_list,
            'data'=> $dataDetail
			));
    }

    /**
     * 	show，渲染查看页面
     */
    public function show($params) {
        $id = intval($params["id"]);
        $modelDetail = new AppReceiveApplyDetailModel(29);
        $dataDetail = $modelDetail->getDataOfapply_Id($id,1);
        $this->render('app_receive_apply_show.html', array(
            'view' => new AppReceiveApplyView(new AppReceiveApplyModel($id, 29)),
            'bar' => Auth::getViewBar(),
            'data'=> $dataDetail
        ));
    }

    /**
     * 	showLogs，渲染查看日志页面
     */
    public function showLogs($params) {
        $id = intval($params["id"]);
        $this->render('app_receive_apply_show_logs.html', array(
            'view' => new AppReceiveApplyView(new AppReceiveApplyModel($id, 29)),
            'bar' => Auth::getViewBar(),
        ));
    }
    /**
     * 	insert，信息入库
     */
    public function insert($params) {
        $result = array('success' => 0, 'error' => '');
        $from_ad = $params['from_ad'];
        $cash_type = $params['cash_type'];

        if ($from_ad == '') {
            $result['error'] = "请选择订单来源";
            Util::jsonExit($result);
        }
        if ($cash_type == '') {
            $result['error'] = "请选择收款类型";
            Util::jsonExit($result);
        }
        if (empty($_FILES)) {
            $result['error'] = "请上传要申请的数据";
            Util::jsonExit($result);
        }
        if (empty($_FILES['data']['tmp_name'])) {
            $result['error'] = "上传文件不能为空";
            Util::jsonExit($result);
        }

        $data = $this->checkData($_FILES['data'], $cash_type, $from_ad); //检查上传文件内容
        $applydata = array(
            'apply_number' => '',
            'status' => 1,
            'should_number' => '',
            'from_ad' => $from_ad,
            'cash_type' => $cash_type,
            'amount' => $data['tongji']['amount'],
            'total' => $data['tongji']['total'],
            'external_total_all' => $data['tongji']['external_total_all'], //外部金额的总和
            'make_time' => date('Y-m-d H:i:s'),
            'make_name' => $_SESSION['userName'],
            'check_time' => '0000-00-00 00:00:00',
            'check_name' => '',
            'kela_total_all' => 0,
            'jxc_total_all' => 0,
            'check_sale_number' => ''
        );

		/*计算相应BDD订单号的 外部金额 ,将得到的结果累加到销售明细的相应的BDD订单号的 外部金额中*/
		$detailModel = new PayOrderInfoModel(30);
		foreach($data['data'] as $kk =>$kval){
			if($applydata['cash_type'] == 1){	//销售收款
				$str = 'external_total_all = external_total_all+'.$kval['external_total'];
			}else if($applydata['cash_type'] == 2){	//销售退款
				$str = 'external_total_all = external_total_all-'.$kval['external_total'];
			}
			$detailModel->updateTotal($str ,' kela_sn='.$kval['kela_sn']);
		}


        $model = new AppReceiveApplyModel(30);
        $res = $model->saveDatas($applydata, $data['data']);
        if ($res['result']) {
            $wucha = array();
            $jxcorderModel = new PayJxcOrderModel(30);
            $payappDetailModel = new AppReceiveApplyDetailModel(30);
            $wucha['kela_total_all'] = $payappDetailModel->CountKelaTotal($res['id']); //BDD金额
            $wucha['jxc_total_all'] = $jxcorderModel->CountJxcTotalApply($res['id']); //销账金额
            $model->updateNoId($wucha, $res['id']);
            //记录日志
            $logModel = new AppReceiveOperatLogModel(30);
            $logs = array(
                'related_id' => $res['id'],
                'type' => 1,
                'operat_name'=>$_SESSION['userName'],
                'operat_time'=>date("Y-m-d H:i:s"),
                'operat_content' => '制单：应收金额' . $applydata['total'] . '元',
            );
            $ret = $logModel->saveData($logs,array());
        }
        if ($ret !== false) {
            $result['success'] = 1;
			$result['label']= 'YSSQ'.$res['id'];
			$result['x_id'] =$res['id'];
			$result['tab_id'] = mt_rand();
        } else {
            $result['error'] = '添加失败';
        }
        Util::jsonExit($result);
    }

    /**
     * 	update，更新信息
     */
    public function update($params) {
        $result = array('success' => 0, 'error' => '');
        $apply_id = $params['id'];
        $model = new AppReceiveApplyModel($apply_id, 30);
		if ($model->getValue('make_name') != $_SESSION['userName'])
		{
			$result['error'] = "只能修改自己制的单。";
            Util::jsonExit($result);
		}
		if($model->getValue('status') != 1)
		{
			$result['error'] = "新增状态才能编辑";
            Util::jsonExit($result);
		}
		if (empty($_FILES['data']['tmp_name'])) {
            $result['error'] = "上传文件不能为空";
            Util::jsonExit($result);
        }
		if (!empty($_FILES['data']['name']))//上传文件
		{//修改有上传文件
			$data = $this->checkData($_FILES['data'], $model->getValue('cash_type'),$model->getValue('from_ad'),$apply_id);//检查上传文件内容

			$applydata = array(
				'id'=>$apply_id,
				'from_ad'   =>  $model->getValue('from_ad'),
				'cash_type' =>  $model->getValue('cash_type'),
				'amount' =>  $data['tongji']['amount'],
				'total'		=>  $data['tongji']['total'],
				'status'    => 1,
				'external_total_all' =>$data['tongji']['external_total_all']

			);
			if ($model->getValue('cash_type') == 1) //收款执行
			{
				//修改应付单状态  将旧数据状态为待申请状态  应付号清空
				$payapply_detail_model = new AppReceiveApplyDetailModel(29);
				$kelaorder   = $payapply_detail_model->getDataOfapply_Id($apply_id);//通过应付申请号获取所有BDD订单号
				//print_r($kelaorder);exit;
				$xiaoshou_model   = new PayOrderInfoModel(30);
				if ($kelaorder) //待申请状态
				{
					$xiaoshou_model->update_pl(array('apply_number'=>'','status'=>1), $kelaorder);
				}
			}
			$model->saveDatas($applydata,$data['data']);//保存数据
		}
        $result['success'] = 1;
		$result['label']= 'YSSQ'.$apply_id;
		$result['x_id'] =$apply_id;
		$result['tab_id'] = mt_rand();
        Util::jsonExit($result);
    }

    function downloadDemo() {
        header("Content-type: text/html; charset=gbk");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=应收申请单demo.csv");

        $content = "订单信息,,,,客户支付信息,,,,费用,,,,,,,,收益,\r\n";
        $content .= "对账日期,外部订单号,订单金额/退货货款,BDD订单号,现金支付,平台积分,平台优惠券支付,BDD优惠券支付,佣金,京豆、京券,运费,卖家赔付,差价,活动优惠,违约罚款,其他,反邮,其他";
        echo iconv('utf-8', 'gbk', $content);
        exit;
    }

    /**
     * 检查数据是否符合要求
     * 1、BDD订单号为空，则提示第几行BDD订单号不能为空
     * 2、检查BDD订单号正确存在，正确则返回， 订单一张BDD订单号只能在一张申请单中成功申请单 ；查询BDD订单号是否在已经申请
     * @param type $file
     * @param type $cash_type
     * @param type $from_ad
     * @param type $apply_number
     * @return type
     */
    function checkData($file, $cash_type, $from_ad, $apply_number = '') {
        $file_array = explode(".", $file['name']);
        $file_extension = strtolower(array_pop($file_array));
        if ($file_extension != 'csv') {
            $result['error'] = '请上传CSV格式的文件';
            Util::jsonExit($result);
        }
        $f = fopen($file['tmp_name'], "r");
        $i = 0;
        $flag = 0;
        $all_total = 0;
        $str_alert = "以下单据异常，请知悉：<br/>";
        $arr = array();
        $waibu_arr = array();
        $j = 0;
        $external_total_all = 0; //应收金额

        $order_info_model = new PayOrderInfoModel(29);
        while (!feof($f)) {
            $con = fgetcsv($f);
            $con = eval('return '.iconv('gbk','utf-8',var_export($con,true).';')) ;
            if ($i > 1) {
                if (!is_array($con) || $con == '') {
                    if ($i == 2) {
                        $result['error'] = '上传文件数据不能为空';
                        Util::jsonExit($result);
                    }
                } else {
                    /* * *验证 start** */
                    //匹配验证
                    if ($con[0] == '') { //对账日期匹配
                        $str = '第' . ($i + 1) . '行对账日期不能为空';
                        $result['error'] = $str;
                        Util::jsonExit($result);
                    }
                    //验证日期格式
                    if (!preg_match('/^\d+/', $con[0]) || !checkdate(substr($con[0], 4, 2), substr($con[0], 6, 2), substr($con[0], 0, 4))) {
                        $str = '第' . ($i + 1) . '行对账日期格式不正确';
                        $result['error'] = $str;
                        Util::jsonExit($result);
                    }
                    //金额验证正则
                    $z = "/^(\d+)(\.\d+)?$/";
                    if ($con[2] == '0' || !preg_match($z, $con[2])) { //订单金额 退货贷款
                        $str = '第' . ($i + 1) . '行"订单金额/退货货款"只能为数字并且是正数。';
                        $result['error'] = $str;
                        Util::jsonExit($result);
                    }
                    //外部订单号验证
                    if ($con[1] == '' && $cash_type == 1) {//收款必填
                        $str = '第' . ($i + 1) . '行外部订单号不能为空。';
                        $result['error'] = $str;
                        Util::jsonExit($result);
                    }
                    if ($con[1] != '' && !preg_match("/^\d+/", $con[1])) {//收款必填
                        $str = '第' . ($i + 1) . '行外部订单号不正确。';
                        $result['error'] = $str;
                        Util::jsonExit($result);
                    }
                    //BDD订单号验证
                    if ($con[3] == '' && $cash_type == 1) {//收款必填
                        $str = '第' . ($i + 1) . '行BDD订单号不能为空。';
                        $result['error'] = $str;
                        Util::jsonExit($result);
                    }
                    if ($con[3] != '' && !preg_match("/^\d+/", $con[3])) {//收款必填
                        $str = '第' . ($i + 1) . '行BDD订单号只能为数字。';
                        $result['error'] = $str;
                        Util::jsonExit($result);
                    }
                    if ($con[4] != '' && !preg_match($z, $con[4])) {
                        $str = '第' . ($i + 1) . '行现金支付只能为数字并且是正数。';
                        $result['error'] = $str;
                        Util::jsonExit($result);
                    }
                    if ($con[5] != '' && !preg_match($z, $con[5])) {
                        $str = '第' . ($i + 1) . '行平台积分支付只能为数字并且是正数。';
                        $result['error'] = $str;
                        Util::jsonExit($result);
                    }
                    if ($con[6] != '' && !preg_match($z, $con[6])) {
                        $str = '第' . ($i + 1) . '行平台优惠券支付只能为数字并且是正数。';
                        $result['error'] = $str;
                        Util::jsonExit($result);
                    }
                    if ($con[7] != '' && !preg_match($z, $con[7])) {
                        $str = '第' . ($i + 1) . '行BDD优惠券支付只能为数字并且是正数。';
                        $result['error'] = $str;
                        Util::jsonExit($result);
                    }

                    if ($con[8] != '' && !preg_match($z, $con[8])) {
                        $str = '第' . ($i + 1) . '行佣金只能为数字并且是正数。';
                        $result['error'] = $str;
                        Util::jsonExit($result);
                    }

                    if ($con[9] != '' && !preg_match($z, $con[9])) {
                        $str = '第' . ($i + 1) . '行京豆、京券只能为数字并且是正数。';
                        $result['error'] = $str;
                        Util::jsonExit($result);
                    }
                    if ($con[10] != '' && !preg_match($z, $con[10])) {
                        $str = '第' . ($i + 1) . '行运费只能为数字并且是正数。';
                        $result['error'] = $str;
                        Util::jsonExit($result);
                    }
                    if ($con[11] != '' && !preg_match($z, $con[11])) {
                        $str = '第' . ($i + 1) . '行买家赔付只能为数字并且是正数。';
                        $result['error'] = $str;
                        Util::jsonExit($result);
                    }
                    if ($con[12] != '' && !preg_match($z, $con[12])) {
                        $str = '第' . ($i + 1) . '行退差价只能为数字并且是正数。';
                        $result['error'] = $str;
                        Util::jsonExit($result);
                    }
                    if ($con[13] != '' && !preg_match($z, $con[13])) {
                        $str = '第' . ($i + 1) . '行活动优惠只能为数字并且是正数。';
                        $result['error'] = $str;
                        Util::jsonExit($result);
                    }
                    if ($con[14] != '' && !preg_match($z, $con[14])) {
                        $str = '第' . ($i + 1) . '行违约罚款只能为数字并且是正数。';
                        $result['error'] = $str;
                        Util::jsonExit($result);
                    }
                    if ($con[15] != '' && !preg_match($z, $con[15])) {
                        $str = '第' . ($i + 1) . '行其他（销售费用）只能为数字并且是正数。';
                        $result['error'] = $str;
                        Util::jsonExit($result);
                    }
                    if ($con[16] != '' && !preg_match($z, $con[16])) {
                        $str = '第' . ($i + 1) . '行反邮只能为数字并且是正数。';
                        $result['error'] = $str;
                        Util::jsonExit($result);
                    }
                    if ($con[17] != '' && !preg_match($z, $con[17])) {
                        $str = '第' . ($i + 1) . '行其他（销售收益）只能为数字并且是正数。';
                        $result['error'] = $str;
                        Util::jsonExit($result);
                    }
                    /* 验证 end */
                    /* 对BDD订单号和外部订单号进行验证 */

                    if ($cash_type == 1) { //只有销售收款才会验证
                        $error_num = $order_info_model->checkOrderData($con[3], $con[1], $from_ad, $apply_number);
                        if ($error_num == 1) {
                            $str_alert .= 'BDD订单号' . $con[3] . "错误<br/>";
                            $flag++;
                        } else if ($error_num == 2) {
                            $str_alert .= 'BDD订单号' . $con[3] . "已申请过应收<br/>";
                            $flag++;
                        } else if ($error_num == 3) {
                            $str_alert .= 'BDD订单' . $con[3] . "应收申请状态非‘待申请’<br/>";
                            $flag++;
                        } else if ($error_num == 4) {
                            $str_alert .= 'BDD订单' . $con[3] . "订单来源非指定来源<br/>";
                            $flag++;
                        } else if ($error_num == 5) {
                            $str_alert .= '外部订单号' . $con[1] . "已申请过应收<br/>";
                            $flag++;
                        } else if ($error_num == 6) {
                            $newcon = array();
                            $newcon['replytime'] = $con[0];
                            $newcon['external_sn'] = $con[1];
                            $newcon['external_total'] = $con[2];
                            $newcon['kela_sn'] = $con[3];
                            $newcon['pay_xj'] = $con[4]?$con[4]:0;
                            $newcon['pay_jf'] = $con[5]?$con[5]:0;
                            $newcon['pay_pt_yhq'] = $con[6]?$con[6]:0;
                            $newcon['pay_kela_yhq'] = $con[7]?$con[7]:0;
							$newcon['f_koudian'] = number_format($con[8]/$con[2]*100,2);  //扣点 = 佣金/订单金额
                            $newcon['f_yongjin'] = $con[8]?$con[8]:0;
                            $newcon['f_jingdong'] = $con[9]?$con[9]:0;
                            $newcon['f_yunfei'] = $con[10]?$con[10]:0;
                            $newcon['f_peifu'] = $con[11]?$con[11]:0;
                            $newcon['f_chajia'] = $con[12]?$con[12]:0;
                            $newcon['f_youhui'] = $con[13]?$con[13]:0;
                            $newcon['f_weiyue'] = $con[14]?$con[14]:0;
                            $newcon['f_qita'] = $con[15]?$con[15]:0;
                            $newcon['sy_fanyou'] = $con[16]?$con[16]:0;
                            $newcon['sy_qita'] = $con[17]?$con[17]:0;
                            $newcon['total'] = ($con[2] - $newcon['pay_kela_yhq'] - $newcon['f_yongjin'] - $newcon['f_jingdong'] - $newcon['f_yunfei'] - $newcon['f_peifu'] - $newcon['f_chajia'] - $newcon['f_youhui'] - $newcon['f_weiyue'] - $newcon['f_qita'] + $newcon['sy_fanyou'] + $newcon['sy_qita']); //订单金额/退货货款-BDD购物券+平台优惠券支付-费用+收益
                            $all_total += $newcon['total'];
                            $newcon['reoverrule_reason'] = '';
                            $arr[] = $newcon;
                            //echo $newcon['external_sn'];
                            $external_total_all += $con[2];
                            $j++;
                        }
                        $waibu_arr[] = $con['1'];
                    }
                    //销售退款
                    else {
                        //不做限定  如果有BDD订单号则只需检查BDD订单号是否正确
                        if ($con[3]) {
                            if ($order_info_model->checkKelaSn($con[3], $from_ad) < 1) {
                                $str = '第' . ($i + 1) . '行BDD订单' . $con[3] . '号错误或者订单来源非指定来源。';
                                $result['error'] = $str;
                                Util::jsonExit($result);
                            }
                        }
                        $newcon = array();
                        $newcon['replytime'] = $con[0];
                        $newcon['external_sn'] = $con[1];
                        $newcon['external_total'] = $con[2];
                        $newcon['kela_sn'] = $con[3];
                        $newcon['pay_xj'] = $con[4]?$con[4]:0;
                        $newcon['pay_jf'] = $con[5]?$con[5]:0;
                        $newcon['pay_pt_yhq'] = $con[6]?$con[6]:0;
                        $newcon['pay_kela_yhq'] = $con[7]?$con[7]:0;
						$newcon['f_koudian'] = number_format($con[8]/$con[2]*100,2);  //扣点 = 佣金/订单金额
                        $newcon['f_yongjin'] = $con[8]?$con[8]:0;
                        $newcon['f_jingdong'] = $con[9]?$con[9]:0;
                        $newcon['f_yunfei'] = $con[10]?$con[10]:0;
                        $newcon['f_peifu'] = $con[11]?$con[11]:0;
                        $newcon['f_chajia'] = $con[12]?$con[12]:0;
                        $newcon['f_youhui'] = $con[13]?$con[13]:0;
                        $newcon['f_weiyue'] = $con[14]?$con[14]:0;
                        $newcon['f_qita'] = $con[15]?$con[15]:0;
                        $newcon['sy_fanyou'] = $con[16]?$con[16]:0;
                        $newcon['sy_qita'] = $con[17]?$con[17]:0;
                        $newcon['total'] = -($con[2] - $newcon['pay_kela_yhq'] - $newcon['f_yongjin'] - $newcon['f_jingdong'] - $newcon['f_yunfei'] - $newcon['f_peifu'] - $newcon['f_chajia'] - $newcon['f_youhui'] - $newcon['f_weiyue'] - $newcon['f_qita'] + $newcon['sy_fanyou'] + $newcon['sy_qita']); //订单金额/退货货款-BDD购物券+平台优惠券支付-费用+收益
                        $all_total += $newcon['total'];
                        $arr[] = $newcon;
                        if ($newcon['external_sn'] != '') {
                            $waibu_arr[] = $newcon['external_sn'];  //去除空值
                        }
                        $external_total_all += $con[2];
                        $j++;
                    }
                }
            }
            $i++;
        }

        if ($i == 2) {
            $result['error'] = '上传文件数据不能为空';
            Util::jsonExit($result);
        }
        //检查重复数据
        $unique_arr = array_unique($waibu_arr);
        if (count($waibu_arr) != count($unique_arr)) {
            $result['error'] = '上传文件中外部单号有重复值，请检查后再上传。';
            Util::jsonExit($result);
        }
        //检查数据是否正确
        if ($flag > 0) {
            $result['error'] = $str_alert;
            Util::jsonExit($result);
        }
        $result['data'] = $arr;
        $result['tongji'] = array('amount' => $j, 'total' => $all_total, 'external_total_all' => $external_total_all);
        return $result;
    }

    /*审核页面的显示*/
	function checkCon()
	{
        $result = array('success' => 0,'error' => '');
		$id = _Request::get('id');
		$_cls = _Request::get('_cls');
        $tab_id = _Request::getInt('tab_id');
		$model = new AppReceiveApplyModel($id,29);
		if($model->getValue('status') != 2)
		{
			$result['content'] = '待审核状态才能操作';
			Util::jsonExit($result);
		}
		$result['content'] = $this->fetch('app_receive_apply_check.html',array('apply_id'=>$id,'tab_id'=>$tab_id,'cls'=>$_cls));
		$result['title'] = '申请单审核';
		Util::jsonExit($result);
	}

	/**
     * 审核
     */
	function check()
	{
        $result = array('success' => 0,'error' => '');
		$id = _Request::get('apply_id');
		$tab_id = _Request::getInt('tab_id');
		$check_sale_number = _Request::get('check_sale_numbers');
		$AppReceiveApply_model = new AppReceiveApplyModel($id,29);
		// 1、a验证核销单不存在 b非已审核 c此单号已关联其他应收单  d收款类型不匹配

		if($AppReceiveApply_model->getValue('make_name') == $_SESSION['userName']){
           // $result['error'] = "不能审核自己的单据";
			//Util::jsonExit($result);	//不能审核自己的单子
		}
		$hexiao_model = new PayHexiaoModel(29);
		$result = $hexiao_model->select(array('check_sale_number'=>$check_sale_number));
        $num = 1;
		if ($result) //存在
		{
			$res = $result[0];
			if ($res['status'] != 3)
			{
				//非已审核
                $result['error'] = "应收核销单据未经审核或审核未通过，无法提交";
                Util::jsonExit($result);
			}
			else if ($res['apply_number'] != '')
			{
				//已关联
                $result['error'] = "当前应收核销单据已关过其他收款申请单，请填写正确单号后提交";
                Util::jsonExit($result);
			}
			else if($res['cash_type'] != $AppReceiveApply_model->getValue('cash_type'))
			{
				//收款类型
                $result['error'] = "核销单的核销类型和应收申请单的收款类型不匹配";
                Util::jsonExit($result);
			}
			else if($res['from_ad'] != $AppReceiveApply_model->getValue('from_ad'))
			{
				//订单来源不同
                $result['error'] = "应收单与核销单不是同一个订单来源，无法提交";
                //Util::jsonExit($result);
			}
		}
		else //不存在
		{
			//不存在;
            $result['error'] = "应收核销单号无效，请输入正确单号后提交";
            Util::jsonExit($result);
		}

		$AppReceiveApply_model    = new AppReceiveApplyModel($id,30);

		// 1、a验证核销单不存在 b非已审核 c此单号已关联其他应收单  d收款类型不匹配
		$hexiao_model = new PayHexiaoModel(29);
		$res = $hexiao_model->select(array('check_sale_number'=>$check_sale_number));
	    $shijia = $res[0]['shijia'];

		$external_total_all = $AppReceiveApply_model->getValue('external_total_all');
		$cha = $external_total_all - $shijia;

        $result['success'] = 1;
        $result['id'] = $id;
        $result['tab_id'] = $tab_id;
        $result['shijia'] = $shijia;
        $result['cha'] = $cha;
        $result['external_total_all'] = $external_total_all;
        $result['check_sale_number'] = $check_sale_number;
        Util::jsonExit($result);
	}


    /**
     * 审核通过
     */
    function checkOver($params) {
        $id = _Request::get('id');
		$check_sale_number = _Request::get('check_sale_number');
        if(!$id && !$check_sale_number){
            $result['error'] = '所需参数不全';
			Util::jsonExit($result);
        }
		$AppReceiveApply_model    = new AppReceiveApplyModel($id,30);
		if($AppReceiveApply_model->getValue('make_name') == $_SESSION['userName'])
		{
			$result['error'] = '不能审核自己制的单';
			Util::jsonExit($result);
		}
		//审核通过需要修改的地方：1、应收申请（状态待生成应付单、核销单号、审核人、审核时间）
		$check_time = date('Y-m-d H:i:s',time());
		$AppReceiveApply_model->update(array('status'=>5,'id'=>$id,'check_time'=>$check_time,'check_name'=>$_SESSION['userName'],'check_sale_number'=>$check_sale_number));
		//2、订单表修改
		if ($AppReceiveApply_model->getValue('cash_type') == 1) //只有销售收款时
		{
			$detail_model = new AppReceiveApplyDetailModel(29);
			$kelasn   =  $detail_model->getDataOfapply_Id($id);
			$pay_order = new PayOrderInfoModel(30);
			$pay_order->update_pl(array('status'=>'4'),$kelasn);
		}
		// 3、核销单 添加上应收申请单号
		$hexiao_model = new PayHexiaoModel(30);
		$hexiao_model->update(array('apply_number'=>'YSSQ'.$id),array('check_sale_number'=>$check_sale_number));

		//记录日志 [审核成功]
		$logModel = new AppReceiveOperatLogModel(30);
		$logs = array(
			'related_id'=>$id,
			'type'=>1,
            'operat_name'=>$_SESSION['userName'],
            'operat_time'=>date("Y-m-d H:i:s"),
			'operat_content'=>'审核',
			);
		$res = $logModel->saveData($logs,array());

		if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '失败';
        }
		Util::jsonExit($result);
    }


    /**
     * 制单人取消已经生成的应收单
     */
    function delCon($params)
	{
		$result   = array('success' => 0,'error' =>'');
		$id       = $params['id'];
		$AppReceiveApply_model = new AppReceiveApplyModel($id,30);

		/*只有已驳回状态和新增状态才可以提交*/
		if (!($AppReceiveApply_model->getValue('status') == 1 || $AppReceiveApply_model ->getValue('status') == 3))
		{
			$result['error'] = '只有已驳回状态和新增状态才可以提交';
			Util::jsonExit($result);
		}
		if($AppReceiveApply_model->getValue('make_name') != $_SESSION['userName'])
		{
			$result['error'] = '只能取消自己制的单。';
			Util::jsonExit($result);
		}

		//2、应收单状态改变：已取消  4
		$AppReceiveApply_model->update(array('status'=>4,'id'=>$id));
		/*3、将销售订单改为待申请  只有销售收款时改变*/
		if ($AppReceiveApply_model->getValue('cash_type') == 1 )
		{
			$AppReceiveApplyDetail_model = new AppReceiveApplyDetailModel(30);
			$kela_sn = $AppReceiveApplyDetail_model->getDataOfapply_Id($id);
			$pay_order_info = new PayOrderInfoModel(30);
			$pay_order_info->update_pl(array('status'=>1,'apply_number'=>''),$kela_sn);
		}

		/*跟新 销售明细里 相应的BDD订单的外部金额*/
		$ApplyModel = new AppReceiveApplyModel(29);
		$pay_order_info = new PayOrderInfoModel(29);
		$appinfo = $ApplyModel->renovate($id);
		foreach($appinfo as $ak => $av){
			if($av['cash_type']==1){
				$str = 'external_total_all = external_total_all-'.$av['external_total'];
			}else if($av['cash_type']==2){
				$str = 'external_total_all = external_total_all+'.$av['external_total'];
			}
			$pay_order_info->updateTotal($str,"kela_sn={$av['kela_sn']}");
		}

		//记录日志
        $logModel = new AppReceiveOperatLogModel(30);
        $logs = array(
            'related_id' => $id,
            'type' => 1,
            'operat_name'=>$_SESSION['userName'],
            'operat_time'=>date("Y-m-d H:i:s"),
            'operat_content' => '取消',
        );
        $res = $logModel->saveData($logs,array());
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '失败';
        }
		Util::jsonExit($result);
	}

    /**
     * 制单人提交已经生成的应收单
     */
    function subCon($params)
	{
	   //1/权限检查 制单权限
		$result = array('success' => 0,'error' =>'');
		$id = $params['id'];
		$AppReceiveApply_model = new AppReceiveApplyModel($id,30);
        if($AppReceiveApply_model->getValue('make_name') != $_SESSION['userName'])
		{
			$result['error'] = '只能提交自己制的单。';
			Util::jsonExit($result);
		}
		/*只有已驳回状态和新增状态才可以提交*/
		if (!($AppReceiveApply_model->getValue('status') == 1 || $AppReceiveApply_model ->getValue('status') == 3))
		{
			$result['error'] = '只有已驳回状态和新增状态才可以提交';
			Util::jsonExit($result);
		}

		$AppReceiveApplyDetail_model = new AppReceiveApplyDetailModel(30);
		//2、已驳回状态下再提交，驳回原因要清空 核销单详细类表
		$AppReceiveApplyDetail_model->update(array('reoverrule_reason'=>''),array('apply_id'=>$id));
		/*3、将销售订单改为待审核状态(收款时)*/
		if ($AppReceiveApply_model->getValue('cash_type') == 1)
		{
			$kela_sn = $AppReceiveApplyDetail_model->getDataOfapply_Id($id);
			$pay_order_info = new PayOrderInfoModel(30);
			$pay_order_info->update_pl(array('status'=>2),$kela_sn);
		}
		//4、应收单状态改变：待审核  2
		$AppReceiveApply_model->update(array('status'=>2,'id'=>$id));
		$result['success'] = 1;
		Util::jsonExit($result);
	}

    /**
     * 申请单驳回
     */
    function reCon()
	{
		//有审核权限的人才能驳回    应付单修改  核销单详细修改 销售单状态修改
        $result = array('success' => 0, 'error' => '');
        $id = _Request::get('id');
        $ids = _Post::get('ids');
        $reasons = _Post::get('reasons');
        //echo $ids;echo $reasons;exit;
        $payapply_model = new AppReceiveApplyModel($id,30);
        if ($payapply_model->getValue('make_name') == $_SESSION['userName']) {
            $result['error'] = '不能驳回自己制的单';
            Util::jsonExit($result);
        }
        if (empty($ids)) {
            $result['error'] = '驳回原因至少填写一项';
            Util::jsonExit($result);
        }
        //记录驳回原因   应收单详细记录
        $ids = explode(',', $ids);
        $reasons = explode('#', $reasons);
        $detail_model = new AppReceiveApplyDetailModel(30);
        foreach ($ids as $k => $v) {
            $detail_model->update(array('reoverrule_reason' => $reasons[$k]), array('detail_id' => $v, 'apply_id' => $id));
        }
        //销售单修改
        if ($payapply_model->getValue('cash_type') == 1) { //只有销售收款时
            $kelasn = $detail_model->getDataOfapply_Id($id);
            $pay_order = new PayOrderInfoModel(30);
            $pay_order->update_pl(array('status' => '5'), $kelasn);
        }
        //应收单修改  记录时间  状态  审核时间  审核人
        $payapply_model->setValue('status', '3');
        $payapply_model->setValue('check_name', $_SESSION['userName']);
        $payapply_model->setValue('check_time', date('Y-m-d H:i:s', time()));
        if ($payapply_model->save()) {
            //记录日志
            $logModel = new AppReceiveOperatLogModel(30);
            $logs = array(
                'related_id' => $id,
                'type' => 1,
                'operat_name'=>$_SESSION['userName'],
                'operat_time'=>date("Y-m-d H:i:s"),
                'operat_content' => '驳回',
            );
            $res = $logModel->saveData($logs,array());
            $result['success'] = 1;
        } else {
            $result['error'] = "驳回失败，请重新操作";
        }
        Util::jsonExit($result);
    }


    /**
     * 	delete，删除
     */
    public function delete($params) {
        $result = array('success' => 0, 'error' => '');
        $id = intval($params['id']);
        $model = new AppReceiveApplyModel($id, 30);
        $do = $model->getDataObject();
        $valid = $do['is_system'];
        if ($valid) {
            $result['error'] = "当前记录为系统内置，禁止删除";
            Util::jsonExit($result);
        }
        $model->setValue('is_deleted', 1);
        $res = $model->save(true);
        //联合删除？
        //$res = $model->delete();
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = "删除失败";
        }
        Util::jsonExit($result);
    }

}

?>