<?php

/**
 *  -------------------------------------------------
 *   @file		: AppProcessorRecordController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-15 17:11:58
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorARecordController extends CommonController {

	protected $smartyDebugEnabled = false;

    /**
     * 	index，搜索框
     */
    public function index($params) {
		$view = new AppProcessorARecordView(new AppProcessorARecordModel(13));
        $this->render('app_processor_record_search_form.html', array('view' =>$view, 'bar' => Auth::getBar()));
    }

    /**
     * 	add，渲染添加页面
     */
    public function add() {

        $this->render('app_processor_record_info.html', array(
            'view' => new AppProcessorARecordView(new AppProcessorARecordModel(13)),
            'dict'=>new DictView(new DictModel(1))
        ));

    }

    /**
     * 	edit，渲染修改页面
     */
    public function edit($params) {
        $id = intval($params["id"]);


        $this->render('app_processor_record_info.html', array(
            'view' => new AppProcessorARecordView(new AppProcessorARecordModel($id,13)),
            'dict'=>new DictView(new DictModel(1))
        ));

    }

    /**
     * 	search，列表
     */
    public function search($params) {

        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'name' => _Request::getString('name'),
            'code' => _Request::getString('code'),
            'pro_contact' => _Request::getString('pro_contact'),
            'business_scope' => _Request::getString('business_scope'),
            'status' => _Request::getInt('status'),
            'check_status' => _Request::getInt('check_status'),
            'shousuo' => _Request::getInt('shousuo'),
        );
        $page = _Request::getInt("page", 1);
        $model = new AppProcessorARecordModel(13);

        if($args['shousuo']){
            $data = $model->pageList($args, $page, 10, false,1);
        }else{
            $data = $model->pageList($args, $page, 10, false,2);
        }

        if ($data['data']) {
            foreach ($data['data'] as $key => &$value) {
                $value['status'] = $model->getStatusList($value['status']);
                $value['check_status'] = $model->getCheckStatusList($value['check_status']);
            }
            unset($value);
        }
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'app_processor_record_search_page';
        $this->render('app_processor_record_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data,
            'view' => new AppProcessorARecordView(new AppProcessorARecordModel(13))
        ));
    }

    /**
     * 	insert，信息入库
     */
    public function insert() {
        $result = array('success' => 0, 'error' => '');
        $this->rules();
        $name = _Post::getString('name');//供应商名称
        $code = _Post::getString('code');//供应商编码
        $pro_email = _Post::getString('pro_email');//供应商邮箱
        $is_open = _Post::getInt('is_open');//是否开通系统
        $password = _Post::getString('password');//密码
        $balance_type = _Post::getInt('balance_type');//付款周期
        $balance_day = _Post::getString('balance_type');
        $cycle = _Post::getString('cycle');//出货周期
        $purchase_amount = _Post::getFloat('purchase_amount');//采购额度
        $business_scope = _Post::getList('business_scope');//经营范围[数组]
        $business_scope = implode(',', $business_scope);//经营范围[1,2,3]

        $bank_name = _Post::getString('bank_name');//开户银行
        $account_name = _Post::getString('account_name');//户名
        $account = _Post::getString('account');//银行账户
        $pay_type = _Post::getList('pay_type');
        $pay_type = implode(',',$pay_type);//结算方式
        $tax_invoice = _Post::getFloat('tax_invoice');//增值税发票
        if(!isset($_POST['point'])|| empty($_POST['point'])){
            $result['error'] = '请选择税点!!!';
            Util::jsonExit($result);
        }else{
            $point = $_POST['point'];//税点
            if(array_search('',$point)){
                $result['error'] = '税点不能为空!!!';Util::jsonExit($result);
            }
            $tax_point = '';
            foreach ($point as $k=>$v) {
                $tax_point .= $k.'|'.$v.',';
            }
            $tax_point = substr($tax_point,0,-1);

        }
        $tax_registry_no = _Post::getString('tax_registry_no');//税务登记证号
        $business_license = _Post::getString('business_license');//营业执照号码
        $business_license_region1 = _Post::getInt('business_license_region_1');//营业执照地址[省]
        $business_license_region2 = _Post::getInt('business_license_region_2');//营业执照地址[市]
        $business_license_region3 = _Post::getInt('business_license_region_3');//营业执照地址[区]
        $business_license_address = _Post::getString('business_license_address');//营业执照地址[详细地址]

        $pro_region1 = _Post::getInt('pro_region_1');   //取货地址[省]
        $pro_region2 = _Post::getInt('pro_region_2');   //取货地址[市]
        $pro_region3 = _Post::getInt('pro_region_3');   //取货地址[区]
        $pro_address = _Post::getString('pro_address'); //取货地址[详细地址]
        $pro_contact = _Post::getString('pro_contact'); //公司联系人
        $pro_phone = _Post::getString('pro_phone');     //公司联系电话
        $pro_qq = _Post::getString('pro_qq');           //公司联系QQ
        $contact = _Post::getString('contact');         //BDD紧急联系人
        $kela_phone = _Post::getString('kela_phone');   //BDD紧急联系电话
        $kela_qq = _Post::getString('kela_qq');         //BDD紧急联系QQ
        $info = _Post::getString('info');//备注

        $olddo = array();
        $newdo = array();
        $newdo['name'] = $name;//供应商名称
        $newdo['code'] = $code;//供应商编码
        $newdo['pro_email'] = $pro_email;//供应商邮箱
        $newdo['is_open'] = $is_open;//是否开通系统
        if($newdo['is_open'] == 1){
            $newdo['password'] = $password;//密码
        }
        $newdo['balance_type'] = $balance_type;//付款周期
        if($newdo['balance_type']==2){
            $newdo['balance_day'] = $balance_day;//付款周期
        }
        $newdo['cycle'] = $cycle;//出货周期
        $newdo['purchase_amount'] = $purchase_amount;//采购额度
        $newdo['business_scope'] = $business_scope;//经营范围[1,2,3]

        $newdo['bank_name'] = $bank_name;//开户银行
        $newdo['account_name'] = $account_name;//户名
        $newdo['account'] = $account;//银行账户
        $newdo['pay_type'] = $pay_type;//结算方式
        $newdo['tax_invoice'] = $tax_invoice;//增值税发票
        $newdo['tax_point'] = $tax_point;//税点
        $newdo['tax_registry_no'] = $tax_registry_no;//税务登记证号
        $newdo['business_license'] = $business_license;//营业执照号码
        $newdo['business_license_region'] = $business_license_region1 . ',' . $business_license_region2 . ',' . $business_license_region3;
        $newdo['business_license_address'] = $business_license_address;//营业执照地址[详细地址]

        $newdo['pro_region'] = $pro_region1 . ',' . $pro_region2 . ',' . $pro_region3;
        $newdo['pro_address'] = $pro_address;//取货地址[详细地址]
        $newdo['pro_contact'] = $pro_contact;//公司联系人
        $newdo['pro_phone'] = $pro_phone;//公司联系电话
        $newdo['pro_qq'] = $pro_qq;//公司联系QQ
        $newdo['contact'] = $contact;//BDD紧急联系人
        $newdo['kela_phone'] = $kela_phone;//BDD紧急联系电话
        $newdo['kela_qq'] = $kela_qq;//BDD紧急联系QQ
        $newdo['info'] = $info;//备注

        $newdo['create_id'] = $_SESSION['userId'];      //创建人
        $newdo['create_user'] = $_SESSION['userName'];  //创建人
        $newdo['create_time'] = date("Y-m-d H:i:s");    //创建时间
        $newdo['status'] = 2;                           //新建默认禁用
        $newdo['check_status'] = (_Post::getString('submit_type') == 'tijiao')?2:1;//1=保存,2=提交,3=审批中,4=拒绝,5=修改,6=删除,7=通过
        $newdo['department_id'] = _Post::getInt('department_id');//申请部门
        $newdo['is_A_company'] = 'Y';//是否A公司
        if(empty($newdo['department_id'])){
            $result['error'] = '请选择申请部门!!!';
            Util::jsonExit($result);
        }

        $newmodel = new AppProcessorARecordModel(14);
        $newmodel_info = new AppProcessorInfoModel(13);
        //校验供应商名称
        $res = $newmodel_info->checkSupplierName($newdo['name']);
        if($res !== false){
            $result['error'] = '供应商名称已被占用,请更换供应商名称!!!';
            Util::jsonExit($result);
        }
        //校验供应商编码
        $res = $newmodel_info->checkSupplierCode($newdo['code']);
        if($res !== false){
            $result['error'] = '编码已被占用,请更换供应商编码!!!';
            Util::jsonExit($result);
        }
        //上传附件
        if(isset($_FILES) && !empty($_FILES)){
            $files = $this->getFiles();
            $newdo = array_merge($newdo,$files);
        }
        $res = $newmodel->saveData($newdo, $olddo);
        if ($res !== false) {
            /*-----更新审核人-----*/
            $model = new AppProcessorARecordModel($res,14);
            $view = new AppProcessorARecordView($model);
            $porcess_id = $view->getProcess();//流程ID
            if($porcess_id){
                $user_id = $view->getCurrentCheckUser($porcess_id,1);
                $model->setValue('check_user',$user_id);
                $model->save(true);
            }
            /*----更新审核人END------*/
            $_model = new AppProcessorOperationModel(14);
            $arr = array();
            $arr['name'] = $name;
            $arr['operation_type'] = $newdo['check_status'];
            $arr['processor_id'] = $res;//申请ID
            $arr['operation_content'] = ($arr['operation_type']==1)?'添加申请':'提交审核';
            $arr['create_user'] = $_SESSION['userName'];
            $arr['create_time'] = date("Y-m-d H:i:s");
            $_model->saveData($arr, $olddo);

            $result['success'] = 1;
        } else {
            $result['error'] = '添加失败';
        }
        Util::jsonExit($result);
    }

	public function checkCode ($data)
	{
		$str = isset($data['name']) ? $data['name'] : '';
		$p = "/[^a-z\d\x{4e00}-\x{9fa5}\(\)]/ui";
		if(preg_match($p,$str))
		{
			echo 'false';
			exit;
		}
		else
		{
			echo 'true';
			exit;
		}
	}

    /**
     * 	update，更新信息
     */
    public function update() {
        $result = array('success' => 0, 'error' => '');
        $this->rules();
        $id = _Post::getInt('id');
        $name = _Post::getString('name');//供应商名称
        $code = _Post::getString('code');//供应商编码
        $pro_email = _Post::getString('pro_email');//供应商邮箱
        $is_open = _Post::getInt('is_open');//是否开通系统
        $password = _Post::getString('password');//密码
        $balance_type = _Post::getInt('balance_type');//付款周期
        $balance_day = _Post::getString('balance_day');
        $cycle = _Post::getString('cycle');//出货周期
        $purchase_amount = _Post::getFloat('purchase_amount');//采购额度
        $business_scope = _Post::getList('business_scope');//经营范围[数组]
        $business_scope = implode(',', $business_scope);//经营范围[1,2,3]

        $bank_name = _Post::getString('bank_name');//开户银行
        $account_name = _Post::getString('account_name');//户名
        $account = _Post::getString('account');//银行账户
        $pay_type = _Post::getList('pay_type');
        $pay_type = implode(',',$pay_type);//结算方式
        $tax_invoice = _Post::getFloat('tax_invoice');//增值税发票

        if(!isset($_POST['point'])|| empty($_POST['point'])){
            $result['error'] = '请选择税点!!!';
            Util::jsonExit($result);
        }else{
            $point = $_POST['point'];//税点
            if(array_search('',$point)){
                $result['error'] = '税点不能为空!!!';Util::jsonExit($result);
            }
            $tax_point = '';
            foreach ($point as $k=>$v) {
                $tax_point .= $k.'|'.$v.',';
            }
            $tax_point = substr($tax_point,0,-1);

        }
        $tax_registry_no = _Post::getString('tax_registry_no');//税务登记证号
        $business_license = _Post::getString('business_license');//营业执照号码
        $business_license_region1 = _Post::getInt('business_license_region_1');//营业执照地址[省]
        $business_license_region2 = _Post::getInt('business_license_region_2');//营业执照地址[市]
        $business_license_region3 = _Post::getInt('business_license_region_3');//营业执照地址[区]
        $business_license_address = _Post::getString('business_license_address');//营业执照地址[详细地址]

        $pro_region1 = _Post::getInt('pro_region_1');   //取货地址[省]
        $pro_region2 = _Post::getInt('pro_region_2');   //取货地址[市]
        $pro_region3 = _Post::getInt('pro_region_3');   //取货地址[区]
        $pro_address = _Post::getString('pro_address'); //取货地址[详细地址]
        $pro_contact = _Post::getString('pro_contact'); //公司联系人
        $pro_phone = _Post::getString('pro_phone');     //公司联系电话
        $pro_qq = _Post::getString('pro_qq');           //公司联系QQ
        $contact = _Post::getString('contact');         //BDD紧急联系人
        $kela_phone = _Post::getString('kela_phone');   //BDD紧急联系电话
        $kela_qq = _Post::getString('kela_qq');         //BDD紧急联系QQ
        $info = _Post::getString('info');//备注

        $newdo = array();
        $newdo['id'] = $id;
        $newdo['name'] = $name;//供应商名称
        $newdo['code'] = $code;//供应商编码
        $newdo['pro_email'] = $pro_email;//供应商邮箱
        $newdo['is_open'] = $is_open;//是否开通系统
        if($newdo['is_open'] == 1){
            $newdo['password'] = $password;//密码
        }
        $newdo['balance_type'] = $balance_type;//付款周期
        if($newdo['balance_type']==2){
            $newdo['balance_day'] = $balance_day;//付款周期
        }
        $newdo['cycle'] = $cycle;//出货周期
        $newdo['purchase_amount'] = $purchase_amount;//采购额度
        $newdo['business_scope'] = $business_scope;//经营范围[1,2,3]

        $newdo['bank_name'] = $bank_name;//开户银行
        $newdo['account_name'] = $account_name;//户名
        $newdo['account'] = $account;//银行账户
        $newdo['pay_type'] = $pay_type;//结算方式
        $newdo['tax_invoice'] = $tax_invoice;//增值税发票
        $newdo['tax_point'] = $tax_point;//税点
        $newdo['tax_registry_no'] = $tax_registry_no;//税务登记证号
        $newdo['business_license'] = $business_license;//营业执照号码
        $newdo['business_license_region'] = $business_license_region1 . ',' . $business_license_region2 . ',' . $business_license_region3;
        $newdo['business_license_address'] = $business_license_address;//营业执照地址[详细地址]

        $newdo['pro_region'] = $pro_region1 . ',' . $pro_region2 . ',' . $pro_region3;
        $newdo['pro_address'] = $pro_address;//取货地址[详细地址]
        $newdo['pro_contact'] = $pro_contact;//公司联系人
        $newdo['pro_phone'] = $pro_phone;//公司联系电话
        $newdo['pro_qq'] = $pro_qq;//公司联系QQ
        $newdo['contact'] = $contact;//BDD紧急联系人
        $newdo['kela_phone'] = $kela_phone;//BDD紧急联系电话
        $newdo['kela_qq'] = $kela_qq;//BDD紧急联系QQ
        $newdo['info'] = $info;//备注

        $newdo['update_id'] = $_SESSION['userId'];      //修改人ID
        $newdo['update_user'] = $_SESSION['userName'];  //修改人
        $newdo['update_time'] = date("Y-m-d H:i:s");    //修改时间
        $newdo['audit_plan'] = 0;                       //审批进度重置
        $newdo['status'] = 2;                           //是否启用重置
        $newdo['check_status'] = (_Post::getString('submit_type') == 'tijiao')?2:1;//1=保存,2=提交,3=审批中,4=拒绝,5=修改,6=删除,7=通过
        $newdo['department_id'] = _Post::getInt('department_id');//申请部门
        $newdo['is_A_company'] = 'Y';//是否A公司
        //上传附件
        if(isset($_FILES) && !empty($_FILES)){
            $files = $this->getFiles();
            $newdo = array_merge($newdo,$files);
        }
        $newmodel = new AppProcessorARecordModel($id, 14);
        $olddo = $newmodel->getDataObject();
        $res = $newmodel->saveData($newdo, $olddo);
        if ($res !== false) {
            /*-----更新审核人-----*/
            $model = new AppProcessorARecordModel($id,14);
            $view = new AppProcessorARecordView($model);
            $porcess_id = $view->getProcess();//流程ID
            if($porcess_id){
                $user_id = $view->getCurrentCheckUser($porcess_id,1);
                $model->setValue('check_user',$user_id);
                $model->save(true);
            }
            /*----更新审核人END------*/
            $_model = new AppProcessorOperationModel(14);
            $arr = array();
            $arr['name'] = $name;
            $arr['operation_type'] = 1;
            $arr['processor_id'] = $id;//申请ID
            if(_Post::getString('submit_type') == 'tijiao'){
                $arr['operation_type'] = 2;
            }
            $arr['operation_content'] = '修改供应商';
            $arr['create_user'] = $_SESSION['userName'];
            $arr['create_time'] = date("Y-m-d H:i:s");
            $_model->saveData($arr, $olddo);

            $sql = 'DELETE FROM `app_processor_audit` WHERE `record_id` ='.$id;
            $res = DB::cn(14)->db()->exec($sql);
            if($res !==false){
                $result['success'] = 1;
            }else{
                $result['error'] = '修改失败';
            }
        } else {
            $result['error'] = '修改失败';
        }
        Util::jsonExit($result);

    }

    public function getCity()
    {
        $a_id = _Post::getInt('province');
        $model = new RegionModel(1);
        $data = $model->getRegion($a_id);
        $this->render('area_info_options1.html', array('data' => $data));
    }

    public function getDistrict()
    {
        $a_id = _Post::getInt('city');
        $model = new RegionModel(1);
        $data = $model->getRegion($a_id);
        $this->render('area_info_options2.html', array('data' => $data));
    }

    /**
     * 	show，渲染查看页面
     */
    public function show($params) {
        $id = intval($params["id"]);
        $view = new AppProcessorARecordView(new AppProcessorARecordModel($id,13));

        $check_status = $view->get_check_status();
        if($check_status == 1 ){//2=提交状态
            echo "<div class='alert alert-info'>很抱歉!请先提交！！！</div>";
            exit;
        }
        $process_id = $view->getProcess();//流程ID
        if(!$process_id){
            echo "<div class='alert alert-info'>很抱歉!没有找到对应的审批流程!请重新申请!</div>";
            exit;
        }
        $department_name = $view->getDepartName();//部门名称
        $user = $view->getCheckUser();  //获取审核人员
        $user = $view->getAuditStatus($user,$id); //获取审核状态
		$now=array();
		foreach($user as $key=>$v)
		{

			if($_SESSION['userId']==$v['user_id'] and in_array($check_status,array(2,3)))
			{
				if($key==0 and $v['audit_status']==false)
				{
					$now=$v;
					break;
				}
				if($key>0)
				{
					if($user[$key-1]['audit_status']==1 and $v['audit_status']==false)
					{
						$now=$v;
					    break;
					}
				}
			}

		}
		//当前进度
        $dict = new DictView(new DictModel(1));
        // $user[3]['audit_status'] =1;
        // echo "<pre>";
        // print_r($user);
        // echo "</pre>";

        $this->render('app_processor_record_show.html', array(
            'view'=>$view,'bar'=>Auth::getViewBar(),
            'user'=>$user,'process_id'=>$process_id,
            'user_sum'=>count($user),'department_name'=>$department_name,
            'dict'=>$dict,
			'now'=>$now,
			'user_id'=>$_SESSION['userId']
        ));
    }

    /**
     * 审核日志
     */
    public function mkCheckLog(){
        $msg = _Post::getString('msg');//审核消息
        $record_id = _Post::getInt('record');//申请供应商ID

        $view = new AppProcessorARecordView(new AppProcessorARecordModel($record_id,13));
        $record_name = $view->get_name();//供应商名称

        $logModel = new AppProcessorOperationModel(14);
        $olddo = array();
        $newdo = array(
            'processor_id'=>$record_id,             //供应商ID
            'name'=>$record_name,                   //供应商名称
            'operation_type'=>3,                    //审批类型：审批
            'operation_content'=>$msg,              //审核消息
            'create_time'=>date('Y-m-d H:i:s'),     //操作时间
            'create_user_id'=>$_SESSION['userId'],  //操作人ID
            'create_user'=>$_SESSION['userName'],   //操作人
        );

        $res = $logModel->saveData($newdo, $olddo);

        if ($res !== false) {
            echo "1";
        } else {
            echo "0";
        }

    }

    /**
     * 拒绝原因
     */
    public function sunmitCause(){
        $result = array('success' => 0,'error' =>'');

        $process_id = _Post::getInt('id');
        $process_name = _Post::getString('process_name');
        $content = _Post::getString('refuse_cause');

        $olddo = array();
        $newdo = array(
            'processor_id'=>$process_id,                       //供应商ID
            'name'=>$process_name,                             //供应商名称
            'operation_type'=>4,                               //审批类型:拒绝
            'operation_content'=>"拒绝原因：".$content,          //审核消息
            'create_time'=>date('Y-m-d H:i:s'),                 //操作时间
            'create_user_id'=>$_SESSION['userId'],              //操作人ID
            'create_user'=>$_SESSION['userName'],               //操作人
        );
        $logModel = new AppProcessorOperationModel(14);

        $res = $logModel->saveData($newdo, $olddo);
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '操作失败';
        }
        Util::jsonExit($result);

    }

    /**
     * 读取操作日志
     */
    public function getLog(){
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'processor_id'=> _Request::get('processor_id'),
        );
        $page = _Request::getInt("page", 1);
        $where = array();
        $where['processor_id'] = $args['processor_id'];

        $model = new AppProcessorOperationModel(13);
        $data = $model->pageList($where, $page, 10, false);
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'app_processor_record_log_page';
        $this->render('app_processor_record_log_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $data
        ));
    }

    /**
     * rules 验证
     */
    public function rules(){
        //rules验证
        $vd = new Validator();
        $vd->set_rules('name', '供应商名称',  'require');
        $vd->set_rules('code', '供应商编码',  'require');
        $vd->set_rules('is_open', '是否开通系统',  'require');
        $vd->set_rules('balance_type', '付款周期',  'require');

        $vd->set_rules('bank_name', '开户银行',  'require');
        $vd->set_rules('account_name', '户名',  'require');
        $vd->set_rules('account', '银行账户',  'require');
        $vd->set_rules('tax_registry_no', '税务登记证号',  'require');
        $vd->set_rules('tax_invoice', '增值税发票',  'require');
        $vd->set_rules('business_license', '营业执照号码',  'require');
        $vd->set_rules('business_license_region_3', '营业执照地址',  'require');
        $vd->set_rules('business_license_address', '营业执照地址详细地址',  'require');

        $vd->set_rules('contact', 'BDD紧急联系人',  'require');
        $vd->set_rules('kela_phone', 'BDD紧急联系人电话',  'require|isPhone');
        $vd->set_rules('pro_contact', '公司联系人',  'require');
        $vd->set_rules('pro_phone', '公司联系电话',  'require|isPhone');

        if (!$vd->is_valid($_POST))
        {
            $result['error'] = $vd->get_errors();
            Util::jsonExit($result);
        }
    }

    public function submit($params){
        $result = array('success' => 0,'error' => '');
        $id = intval($params['id']);
        $model = new AppProcessorARecordModel($id,14);
        $v = new AppProcessorARecordView($model);
        $check_status = $v->get_check_status();
        if($check_status == 1){
            $model->setValue('check_status',2);
            $res = $model->save(true);
            if($res !== false){
                //记录日志
                $logModel = new AppProcessorOperationModel(14);
				$l_do=array();
                $l_do['name'] = $v->get_name();
                $l_do['operation_type'] = 2;
				$l_do['processor_id'] = $id;//申请ID
                $l_do['operation_content']='提交申请';
                $l_do['create_time']=date('Y-m-d H:i:s');
                $l_do['create_user_id']=$_SESSION['userId'];
                $l_do['create_user']=$_SESSION['userName'];
                $logModel->saveData($l_do,array());
                $result['success'] = 1;
            }else{
                $result['error'] = "提交失败";
            }
        }else{
            $result['error'] = "非保存状态,不允许提交！";
        }

        Util::jsonExit($result);

    }

    public function autoCode(){
        $c_name = _Post::getString('name');
        $str = Pinyin::getFirstCode($c_name);
        echo substr($str,0,31);
    }

    public function getPoint(){
        $point = _Post::getInt('point');
        $label = _Post::getString('label');
        $this->render('app_processor_tax_point.html', array(
            'point'=>$point,'label'=>$label
        ));
    }

    public function showPoint(){
        $points = $_POST['points'];
        $this->render('app_processor_tax_points.html', array(
            'points'=>$points
        ));
    }


    public function getFiles(){
        if(isset($_FILES) && !empty($_FILES))
        {
            $newdo = array();
            $upload = new Upload();
            if(isset($_FILES['pact_doc'])){
                $p_ext = Upload::getExt($_FILES['pact_doc']['name']);
                if(!in_array($p_ext,$upload->doc) && !in_array($p_ext,$upload->img)){
                    $result['error'] = "合同-文件不符合类型！";
                    Util::jsonExit($result);
                }
                $pact_doc = $upload->toUP($_FILES['pact_doc']);
                if(is_array($pact_doc)){
                    $newdo['pact_doc'] = $pact_doc['url'];
                }else{
                    $result['error'] = $pact_doc;
                    Util::jsonExit($result);
                }
            }

            if(isset($_FILES['license_jpg'])){
                $l_ext = Upload::getExt($_FILES['license_jpg']['name']);
                if(!in_array($l_ext,$upload->img)){
                    $result['error'] = "营业执照-文件不符合类型！";
                    Util::jsonExit($result);
                }
                $license_jpg = $upload->toUP($_FILES['license_jpg']);
                if(is_array($license_jpg)){
                    $newdo['license_jpg'] = $license_jpg['url'];
                }else{
                    $result['error'] = $license_jpg;
                    Util::jsonExit($result);
                }
            }

            if(isset($_FILES['tax_jpg'])){
                $t_ext = Upload::getExt($_FILES['tax_jpg']['name']);
                if(!in_array($t_ext,$upload->img)){
                    $result['error'] = "税务登记证-文件不符合类型！";
                    Util::jsonExit($result);
                }
                $tax_jpg = $upload->toUP($_FILES['tax_jpg']);
                if(is_array($tax_jpg)){
                    $newdo['tax_jpg'] = $tax_jpg['url'];
                }else{
                    $result['error'] = $tax_jpg;
                    Util::jsonExit($result);
                }
            }
            return $newdo;
        }
        return false;
    }


     /*
    *   取消申请(保存、提交、审批中)
    *
    */
    public function cancelApply($params){

        $id = intval($params['id']);
        $recordmodel = new AppProcessorARecordModel($id,14);
        $check_status = $recordmodel->getValue('check_status');
        if(!in_array($check_status, array(1,2,3))){
            $result['error'] = '审批状态为[保存]、[提交]、[审批中]的申请才允许取消!';
            Util::jsonExit($result);
        }
        $res = $recordmodel->delProcessorRecordById($id);
        if($res){
            $result['success'] = 1;
        }else{
            $result['error'] = '取消申请失败!';
        }
        Util::jsonExit($result);

    }




}

?>