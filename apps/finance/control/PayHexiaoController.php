<?php

/**
 *  -------------------------------------------------
 *   @file		: PayHexiaoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-26 16:39:43
 *   @update	:
 *  -------------------------------------------------
 */
class PayHexiaoController extends CommonController {

    protected $smartyDebugEnabled = false;
    protected $whitelist = array('download','downloadCon');

    /**
     * 	index，搜索框
     */
    public function index($params) {
        //获取来源
        $model = new CustomerSourcesModel(1);
        $source_list = $model->getSourcesPay();
        $this->render('pay_hexiao_search_form.html', array('source_list' => $source_list,'view' => new PayHexiaoView(new PayHexiaoModel(29)), 'bar' => Auth::getBar()));
    }

    /**
     * 	search，列表
     */
    public function search($params) {
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'check_sale_number' => _Request::getString('check_sale_number'),
            'apply_number' => _Request::getInt('apply_number'),
            'cash_type' => _Request::getInt('cash_type'),
            'status' => _Request::getInt('status'),
            'from_ad' => _Request::getString('from_ad'),
            'maketime_start' => _Request::getString('maketime_start'),
            'maketime_end' => _Request::getString('maketime_end'),
            'checktime_start' => _Request::getString('checktime_start'),
            'checktime_end' => _Request::getString('checktime_end'),
                //'参数' = _Request::get("参数");
        );
        $page = _Request::getInt("page", 1);
        $where = array();
        $where['check_sale_number'] = $args['check_sale_number'];
        $where['apply_number'] = $args['apply_number'];
        $where['cash_type'] = $args['cash_type'];
        $where['cash_type'] = $args['cash_type'];
        $where['status'] = $args['status'];
        $where['from_ad'] = $args['from_ad'];
        $where['maketime_start'] = $args['maketime_start'];
        $where['maketime_end'] = $args['maketime_end'];
        $where['checktime_start'] = $args['checktime_start'];
        $where['checktime_end'] = $args['checktime_end'];

        $model = new PayHexiaoModel(29);
        $data = $model->pageList($where, $page, 10, false);
		$sourceModel = new CustomerSourcesModel(1);
        if ($data['data']) {
            foreach ($data['data'] as $key => $value) {
                $value['ad_name'] = $sourceModel->getSourceNameById($value['from_ad']);
				$data['data'][$key] = $value;
            }
        }
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'pay_hexiao_search_page';
        $this->render('pay_hexiao_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data
        ));
    }

    /**
     * 	add，渲染添加页面
     */
    public function add() {
        $tab_id = _Request::get('tab_id');
		$model = new CustomerSourcesModel(1);
        $source_list = $model->getSourcesPay();
        $this->render('pay_hexiao_info.html', array(
			'view' => new PayHexiaoView(new PayHexiaoModel(29)),
			'source_list'=>$source_list,
			'tab_id' => $tab_id
			));
    }

    public function download() {
        header("Content-type: text/html; charset=gbk");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=应收核销单.csv");
        $content = "单号\r\n";
        echo iconv('utf-8', 'gbk', $content);
        exit;
    }

    /**
     * 制单人提交已经生成的核销单
     */
    function subCon($params) {
        //1/权限检查 制单权限
        $result = array('success' => 0, 'error' => '');
        $id = _Post::get('id');
        $hexiaoModel = new PayHexiaoModel($id, 29);

        if (!($hexiaoModel->getValue('status') == 1 || $hexiaoModel->getValue('status') == 4)) {
            $result['error'] = '状态不正确核销单不能提交。';
            Util::jsonExit($result);
        }
        if ($hexiaoModel->getValue('makename') != $_SESSION['userName']) {
            $result['error'] = '只允许提交自己制的单。';
            Util::jsonExit($result);
        }
        $hexiaodetail_model = new PayHexiaoDetailModel(29);
        //2、已驳回状态下再提交，驳回原因要清空 核销单详细类表
        $hexiaodetail_model->updateByrea(array('overrule_reason' => ''), array('hx_id' => $id));
        /* 3、将销售订单改为待审核状态 */
        $jxc_order = $hexiaodetail_model->getDataOfhx_Id($id);
        $payJxcorder_model = new PayJxcOrderModel(29);
        $payJxcorder_model->update_status(array('status' => 3), $jxc_order);
        //4、核销单状态改变：待审核  2
        $hexiaoModel->update_hx(array('status' => 2, 'id' => $id, 'checkname' => '', 'checktime' => '0000-00-00 00:00:00'));
        $result['success'] = 1;
        Util::jsonExit($result);
    }

    /**
     * 制单人取消已经生成的核销单
     */
    function delCon() {
        //1/权限检查 制单权限
        $result = array('success' => 0, 'error' => '');
        $id = _Post::get('id');
        $hexiaoModel = new PayHexiaoModel($id, 30);

		if (!($hexiaoModel->getValue('status') == 1 || $hexiaoModel->getValue('status') == 2 || $hexiaoModel->getValue('status') == 4)) {
            $result['error'] = '状态不正确核销单不能提交。';
            Util::jsonExit($result);
        }
        if ($hexiaoModel->getValue('makename') != $_SESSION['userName']) {
            $result['error'] = '只能取消自己制的单。';
            Util::jsonExit($result);
        }
        //2、核销单状态改变：已取消  5
        $hexiaoModel->update_hx(array('status' => 5, 'id' => $id, 'checkname' => $_SESSION['userName'], 'checktime' => date('Y-m-d H:i:s', time())));
        /* 3、将销售订单改为取消状态 */
        $hexiaodetail_model = new PayHexiaoDetailModel(29);
        $jxc_order = $hexiaodetail_model->getDataOfhx_Id($id);
        $payJxcorder_model = new PayJxcOrderModel(30);
        //取消之后
        $payJxcorder_model->update_status(array('status' => 1, 'hexiao_number' => ''), $jxc_order);
        $result['success'] = 1;
        Util::jsonExit($result);
    }

    /**
     * 审核人审核通过
     */
    function checkCon() {
        $result = array('success' => 0, 'error' => '');
        $id = _Post::get('id');
        $hexiaoModel = new PayHexiaoModel($id, 29);
        if ($hexiaoModel->getValue('status') != 2) {
            $result['error'] = '待审核状态才能进行审核操作。';
            Util::jsonExit($result);
        }
        if ($hexiaoModel->getValue('makename') == $_SESSION['userName']) {
            $result['error'] = '不能审核自己制的单';
            Util::jsonExit($result);
        }

        $checktime = date('Y-m-d H:i:s');
        //2、核销单状态改变：已审核  3   审核时间  审核人
        $hexiaoModel->update_hx(array('status' => 3, 'id' => $id, 'checktime' => $checktime, 'checkname' => $_SESSION['userName']));

        /* 3、将销售订单改为审核状态  核销时间 */
        $hexiaodetail_model = new PayHexiaoDetailModel(29);
        $jxc_order = $hexiaodetail_model->getDataOfhx_Id($id);
        $payJxcorder_model = new PayJxcOrderModel(29);
        $payJxcorder_model->update_status(array('status' => 4, 'hexiaotime' => $checktime), $jxc_order);
        $result['success'] = 1;
        Util::jsonExit($result);
    }

    /**
     * 驳回核销单
     */
    function reCon() {
        $result = array('success' => 0, 'error' => '');
        $id = _Post::get('hx_id');
        $ids = _Post::getString('ids');
        $reasons = _Post::getString('reasons');
        $hexiaoModel = new PayHexiaoModel($id, 29);
       if ($hexiaoModel->getValue('status') != 2) {
            $result['error'] = '待审核状态才能进行驳回操作。';
            Util::jsonExit($result);
        }
        if ($hexiaoModel->getValue('makename') == $_SESSION['userName']) {
            $result['error'] = '不能驳回自己制的单';
            Util::jsonExit($result);
        }
        if (empty($ids)) {
            $result['error'] = '驳回原因至少填写一项';
            Util::jsonExit($result);
        }
        //记录驳回原因   核销单详细记录

        $ids = explode(',', $ids);
        $reasons = explode('#', $reasons);
        $detail_model = new PayHexiaoDetailModel(29);
        foreach ($ids as $k => $v) {
            $detail_model->updateByrea(array('overrule_reason' => $reasons[$k]), array('jxc_order' => $v, 'hx_id' => $id));
            //$ids_new[] = array('jxc_order'=>$v);
        }
        $jxc_order = $detail_model->getDataOfhx_Id($id);

        //销售单修改
        $payJxcorder_model = new PayJxcOrderModel(29);

        //核销单修改  记录时间  状态  审核时间  审核人
        $hexiaoModel->setValue('status', '4');
        $hexiaoModel->setValue('checkname', $_SESSION['userName']);
        $hexiaoModel->setValue('checktime', date('Y-m-d H:i:s', time()));

        if ($payJxcorder_model->update_status(array('status' => '5'), $jxc_order) && $hexiaoModel->save()) {
            $result['success'] = 1;
        } else {
            $result['error'] = "提交失败，请重新提交";
        }
        //print_r($result);exit;
        Util::jsonExit($result);
    }

    /**
     * 	edit，渲染修改页面
     */
    public function edit($params) {
		$model = new CustomerSourcesModel(1);
        $source_list = $model->getSourcesPay();
        $id = intval(_Request::get("id"));
        $tab_id = _Request::getInt("tab_id");
        $view = new PayHexiaoView(new PayHexiaoModel($id, 29));
        $detail_model = new PayHexiaoDetailModel(29);
		$data = $detail_model->getDataOfhx_Id($id);
        foreach ($data as &$val) {
            if ($val['type'] == 'S') {
                $val['type'] = '销售单';
            } else {
                $val['type'] = '销售退货单';
            }
        }
        unset($val);
        $this->render('pay_hexiao_info_edit.html', array(
			'view' => $view,
			'tab_id' => $tab_id,
			'source_list' => $source_list,
            'data' => $data));
    }

    /**
     * 	show，渲染查看页面
     */
    public function show($params){
        $id = intval($params["id"]);
        $detail_model = new PayHexiaoDetailModel(29);
        $data = $detail_model->getDataOfhx_Id($id);
        foreach ($data as &$val) {
            if ($val['type'] == 'S') {
                $val['type'] = '销售单';
            } else {
                $val['type'] = '销售退货单';
            }
        }
        unset($val);
        $this->render('pay_hexiao_show.html', array(
            'view' => new PayHexiaoView(new PayHexiaoModel($id, 29)),
            'bar' => Auth::getViewBar(),
            'data' => $data
        ));
    }

    /**
     * 	insert，信息入库
     */
    public function insert($params) {
		$result = array('success' => 0,'error' => '');
        $from_ad = $params['from_ad'];
        $cash_type = $params['cash_type'];
        if ($from_ad == '') {
			$result['error'] = "请选择订单来源";
			Util::jsonExit($result);
        }
        if ($cash_type == '') {
			$result['error'] = "请选择核销类型";
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
        //Util::alertUrl('添加核销单成功', 'index.php?mod=finance&con=PayHexiao&act=edit&id=3');

        $data = $this->checkData($_FILES['data'], $cash_type, '', $from_ad); //检查上传文件内容

        $verifdata = array(
            'status' => 1,
            'from_ad' => $from_ad,
            'cash_type' => $cash_type,
            'order_num' => $data['tongji']['order_num'],
            'goods_num' => $data['tongji']['goods_num'],
            'chengben' => $data['tongji']['chengben'],
            'shijia' => $data['tongji']['shijia'],
            'maketime' => date('Y-m-d H:i:s', time()),
            'makename' => $_SESSION['userName'],
            'checktime' => '0000-00-00 00:00:00',
            'checkname' => '',
            'apply_number' => '',
            'check_sale_number' => ''
        );
        $model = new PayHexiaoModel(29);
        $res = $model->saveDatas($verifdata, $data['data']);

        if ($res['result']) {
            //$this->apply_log_add($res['apply_id'],'生成申请单');
            $result['x_id'] = $res['id'];
            $result['label'] = 'HX'.$res['id'];
            $result['tab_id'] = _Post::getString('tab_id');
            $result['success'] = 1;
            Util::jsonExit($result);
        }
    }

    /**
     * 	update，更新信息
     */
    public function update($params) {
		$result = array('success' => 0,'error' => '');
        $from_ad = $params['from_ad'];
        $id = $params['id'];
        if ($from_ad == '') {
            Util::jsonExit(array('error' => '请选择订单来源'));
        }

        $model = new PayHexiaoModel($id,29);
        $cash_type = $model->getValue('cash_type');

        if ($model->getValue('makename') != $_SESSION['userName']) {
            Util::jsonExit(array('error' => '只能修改自己制的单。'));
        }

        if (empty($_FILES)) {//没有上传文件
            Util::jsonExit(array('error' => '请上传要修改的数据。'));
        } else {//修改有上传文件
            $data = $this->checkData($_FILES['data'], $cash_type, $id, $from_ad); //检查上传文件内容
            $verifdata = array(
                'id' => $id,
                'status' => 1,
                'from_ad' => $from_ad,
                'cash_type' => $cash_type,
                'order_num' => $data['tongji']['order_num'],
                'goods_num' => $data['tongji']['goods_num'],
                'chengben' => $data['tongji']['chengben'],
                'shijia' => $data['tongji']['shijia'],
                'makename' => $_SESSION['userName'],
                'checktime' => '0000-00-00 00:00:00',
                'checkname' => '',
                'apply_number' => '',
                'check_sale_number' => 'HX' . $id
            );
            $hexiaodetail_model = new PayHexiaoDetailModel(29);
            $model->saveDatas($verifdata, $data['data']);
        }
		$result['x_id'] = $id;
		$result['label'] = 'HX'.$id;
		$result['tab_id'] = _Post::getString('tab_id');
		$result['success'] = 1;
		Util::jsonExit($result);
    }

    /**
     * 验证上传数据
     * @param type $file
     * @param type $cash_type
     * @param type $id
     * @param type $from_ad
     * @return type
     */
    public function checkData($file, $cash_type, $id = '', $from_ad) {
        $file_array = explode(".", $file['name']);
        $file_extension = strtolower(array_pop($file_array));

        if ($file_extension != 'csv') {
            Util::jsonExit(array('error' => '请上传CSV格式的文件'));
        }
        $_model = new PayHexiaoModel(29);
        $f = fopen($file['tmp_name'], "r");
        $i = 0;
        $flag = 0;
        $str_alert = "以下单据异常，无法核销，请知悉：<br/>";
        $all_chengben = 0;
        $all_shijia = 0;
        $all_num = 0;
        $pay_hexiao_detail = array();
        while (!feof($f)) {
            $con = fgetcsv($f);
            if ($i > 0) {
                if (trim($con[0]) == '') {
                    if ($i == 1) {
                        Util::jsonExit(array('error' => '上传文件数据不能为空'));
                    }
                } else {
                    //匹配验证
                    //检查订单 有效 审核通过(都是通过的)  待核销状态:1
                    $res = $_model->get_jxc_order_info($con[0], $id, $from_ad);
                    //print_r($res);die;
                    if ($res && $res['status'] == 1) {  //订单存在  数据存储
                        $pay_hexiao_detail[] = $res;
                        if ($cash_type == 1) {  //销售出货
                            if ($res['type'] == 'S') {
                                $all_num += $res['goods_num'];
                                $all_shijia += $res['shijia'];
                                $all_chengben += $res['chengben'];
                            } else if ($res['type'] == 'B') {
                                $all_num -= $res['goods_num'];
                                $all_shijia -= $res['shijia'];
                                $all_chengben -= $res['chengben'];
                            }
                        } else if ($cash_type == 2) { //销售退货
                            if ($res['type'] == 'B') {
                                $all_num += $res['goods_num'];
                                $all_shijia += $res['shijia'];
                                $all_chengben += $res['chengben'];
                            } else if ($res['type'] == 'S') {
                                $all_num -= $res['goods_num'];
                                $all_shijia -= $res['shijia'];
                                $all_chengben -= $res['chengben'];
                            }
                        }
                    } else if ($res && ($res['status'] == 2 || $res['status'] == 3 || $res['status'] == 4)) {
						if($res['hexiao_number'] != 'HX'.$id)
						{
							$str_alert .= '单号' . $con[0] . "的核销状态非“待核销”<br/>";
							$flag++;
						}
                    } else if ($res && $res['status'] == 'no') {
                        $str_alert .= '单号' . $con[0] . "已经生成核销单，不能重复<br/>";
                        $flag++;
                    } else if ($res && $res['status'] == 'from_no') {
                        $str_alert .= '单号' . $con[0] . "来源非指定来源<br/>";
                        $flag++;
                    } else {  //订单号不存在
                        $str_alert .= '单号' . $con[0] . "错误<br/>";
                        $flag++;
                    }

                    $d[] = $con[0];
                }
            }
            $i++;
        }
        //检查重复数据
        $unique_arr = array_unique($d);
        if (count($d) != count($unique_arr)) {
			$result['error'] = "上传文件中单号有重复值，请检查后再上传。";
			Util::jsonExit($result);
        }
        //检查数据是否正确
        if ($flag > 0) {
			$result['error'] = $str_alert;
			Util::jsonExit($result);
        }
        $result['data'] = $pay_hexiao_detail;
        $result['tongji'] = array('order_num' => count($d), 'goods_num' => $all_num, 'chengben' => $all_chengben, 'shijia' => $all_shijia);
        return $result;
    }

    public function downloadCon($param) {
        $id = _Request::getInt('id');
        $model = new PayHexiaoDetailModel(29);
        $data = $model->getDataOfhx_Id($id);
        $filename = '应收核销明细表';
        $title = array('单号', '单据类型', '（货品）总数', '成本价', '销售价', '驳回原因');
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                if ($v['type'] == 'S') {
                    $v['type'] = '销售单';
                } else {
                    $v['type'] = '销售退货单';
                }
                $val = array($v['jxc_order'],$v['type'], $v['goods_num'], $v['chengben'], $v['shijia'], $v['overrule_reason']);
                $val = eval('return ' . iconv('utf-8', 'gbk', var_export($val, true) . ';'));
                $content[] = $val;
            }
        }
        $name = $filename.date("Ymd_His");
        header("Content-Disposition: attachment;filename=" . iconv('utf-8', 'gbk', $name) . ".csv");
        $fp = fopen('php://output', 'w');
        $title = eval('return ' . iconv('utf-8', 'gbk', var_export($title, true) . ';'));
        fputcsv($fp, $title);
        foreach ($content as $k => $v) {
            fputcsv($fp, $v);
        }
        fclose($fp);
        exit;
    }

}

?>