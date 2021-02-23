<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoLController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-21 16:59:45
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoLController extends CommonController
{
    protected $smartyDebugEnabled = true;
    protected $whitelist = array(
        'downLoadExcel',
        'downLoadEditExcel',
        'writeJs',
        'getGoodsInfo',
        'getStyle',
        'printBill',
        'printSum',
        'printHedui',
        'printcode',
        'selectsalepolicy',
        'getPolicytypeInfo',
        'print_q');

    /**
     *	index，搜索框
     */
    public function index($params)
    {
        //Util::M('warehouse_bill_info_l');	//生成模型后请注释该行
        //Util::V('warehouse_bill_info_l');	//生成视图后请注释该行
       
        $this->render('warehouse_bill_info_l_search_form.html');
    }


    /**
     *	selectsalepolicy，渲染选择销售政策页面
     */
    public function selectsalepolicy($params)
    {
        $id = intval($params["id"]);
        
        $billModel = new WarehouseBillModel($id, 21);
        $policy_id =$billModel->getGoodsIdinfoByBillId($id);
        
        $bill_no = $billModel->getValue('bill_no');
        $goods_id = $billModel->getGoodsidBybillno($bill_no);
        $kk = '';
        foreach ($goods_id as $v) {
            foreach ($v as $k) {
                $kk .= $k . "','";
            }
        }
        $kk2 = '';
        foreach ($policy_id as $v) {
            foreach ($v as $k) {
                $kk2 .= $k . "','";
            }
        }
        $kk2 = rtrim($kk2, ",'");
        $kk2="'".$kk2."'";
        
        $kk = rtrim($kk, ",'");
        $kk="'".$kk."'";
        $policy_goods=$billModel->getNotInpolicy($kk,$kk2,$id);

        $kk3 = '';
        foreach ($policy_goods as $v) {
            foreach ($v as $k) {
                $kk3 .= $k . "','";
            }
        }
        $kk3= rtrim($kk3, ",'");
        $kk3="'".$kk3."'";
        $policy_info=$billModel->getPolicyidBygoodsid($kk);
    
        $this->render('warehouse_bill_info_l_selectsalepolicy.html', array(
        'goods_id' =>$kk,
        'bill_id'=>$id,
        'policy_goods'=>$kk3,
        'policy_info'=>$policy_info,
                 'view' => new WarehouseBillView(new WarehouseBillModel($id, 21))));


    }
    public function getPolicytypeInfo()
    {   
        $billModel = new WarehouseBillModel(21);
        $goods_id=_Request::getString('goods_id');
        $policy_goods=_Request::getString('policy_goods');  
        
        $result = array('success' => 0,'error' =>'');
        $policy_type = _Request::getString('policy_type');
        
        if($policy_type=='default'){
            $policy_info=$billModel->getPolicyidBygoodsid($goods_id);
            $jiajialv=$policy_info['jiajia'];
            $stavalue=$policy_info['sta_value'];
        }
        elseif($policy_type=='bill_no'){
            $policy_info=$billModel->getPolicybillBygoodsid($goods_id);
            $jiajialv=$policy_info['jiajia'];
            $stavalue=$policy_info['sta_value'];
        }
        if(!empty($policy_goods) && $policy_goods!=''){
            $result['error'] = "$policy_goods 商品未维护相应的销售政策，请与产品中心维护人员联系添加。";
            Util::jsonExit($result);
        }
        
        $result['success']=1;
        $result['msg']=$jiajialv;
        $result['msg1']=$stavalue;
        Util::jsonExit($result);
    }
    /**
     *	add，渲染添加页面
     */
    public function add()
    {
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
            die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }    	
    	//如果不是删除后进来的，就清除结算商session   	
       if(!isset($_GET['_'])){
    	 setcookie('bill_pay','',time()-10);
    	}
        $result = array('success' => 0, 'error' => '');
        //公司列表
        $user_id = $_SESSION['userId'];
        $model_c = new CompanyModel(1);
        $company = $model_c->getCompanyTree();

        //库房列表
        $model_w = new WarehouseModel(21);
        /*$warehouse = $model_w->select(array('is_delete' => 1), array(
            "id",
            "name",
            'code'));*/
        

        $master_company_id = $_SESSION['companyId'];
        $warehouse = $model_w->getMasterWarehouse($master_company_id);

        $warehouseids= Util::eexplode(',', $_SESSION['userWareList']);
        $quanxianwarehouseid = $this->WarehouseListO();

        if ($quanxianwarehouseid !== true) {
            foreach ($warehouse as $key => $val) {
                if (!in_array($val['id'], $quanxianwarehouseid)) {
                    unset($warehouse[$key]);
                }
            }
        }
       
        //供应商
        $model_p = new ApiProModel();
        $level = $this->verifyUserLevel();//3、省代
        $where_level = array('status' => 1);
        //1、浩鹏系统，省代做收货单以及退货返厂单，加工商默认 经销商自采供应商
        if($level['level'] == '3') $where_level['p_id'] = '597';//经销商自采供应商
        $pro_list = $model_p->GetSupplierList($where_level);
        if(isset($_COOKIE['bill_pay'])){
        	$olddo_str=$_COOKIE['bill_pay'];
        	$payList=unserialize($olddo_str);   	
        }else{       	
        	$payList=array();
        }
        $dd = new DictView(new DictModel(1));
        $viewData = array(
        		'dd' => $dd,
        		'company' => $company,
        		'warehouse' => $warehouse,
        		'pro_list' => $pro_list,
        		'payList'  => $payList,
        		'user_id' => $user_id,
        		'put_in_type_list' => $dd->getEnumArray('warehouse.put_in_type'),
        		'put_in_type' => '',
        		'processor_id' => '',
        );
        
        if (SYS_SCOPE == 'zhanting' && isset($_SESSION['companyId']) && !empty($_SESSION['companyId'])) {
        	$company = new CompanyModel(1);
        	$processor_id = $company->select2('processor_id', ' id='.$_SESSION['companyId'], 3);
        	if (!empty($processor_id)) {
        		$viewData['put_in_type'] = 5;
        		$viewData['processor_id'] = $processor_id;
        		
        		foreach ($pro_list as $k => $v) {
        			if ($v['id'] == $processor_id) {
        				$viewData['pro_list'] = array($v);
        				break;
        			}
        		}
        		
        		foreach ($viewData['put_in_type_list'] as $k => $v) {
        			if ($v['name'] == '5') {
        				$viewData['put_in_type_list'] = array($v);
        				break;
        			}
        		}
        	}
        }
       
        $this->render('warehouse_bill_info_l_add.html',$viewData);
    }

    /**
     *	edit，渲染修改页面
     */
    public function edit($params)
    {
    	
        $id = intval($params["id"]);
        //主信息

        $model = new WarehouseBillModel($id, 21);
        $info = $model->getDataObject();
        //$infomodel = new WarehouseBillInfoLModel(21);
        //$info1 = $infomodel->getRow_Bill_id($id);
        //$info = array_merge($info,$info1);

        //结算商总金额
        $payModel = new WarehouseBillPayModel(21);
        $info['pay_price'] = $payModel->getAmount($id);

        //公司列表
        $model_c = new CompanyModel(1);
        $company = $model_c->getCompanyTree();

        //库房列表
        $model_w = new WarehouseModel(21);
        $warehouse = $model_w->select(array('is_delete' => 1), array(
            "id",
            "name",
            "code"));

        $warehouseids= Util::eexplode(',', $_SESSION['userWareList']);

        $quanxianwarehouseid = $this->WarehouseListO();

        if ($quanxianwarehouseid !== true) {
            foreach ($warehouse as $key => $val) {
                if (!in_array($val['id'], $quanxianwarehouseid)) {
                    unset($warehouse[$key]);
                }
            }
        }

        //供应商
        $model_p = new ApiProModel();
        $pro_list = $model_p->GetSupplierList(array('status' => 1));
       
        $dd = new DictView(new DictModel(1));
        $put_in_types = $dd->getEnumArray('warehouse.put_in_type');
        if (SYS_SCOPE == 'zhanting' && isset($_SESSION['companyId']) && !empty($_SESSION['companyId'])) {
        	$company = new CompanyModel(1);
        	$processor_id = $company->select2('processor_id', ' id='.$_SESSION['companyId'], 3);
        	if (!empty($processor_id)) {
       		
        		foreach ($pro_list as $k => $v) {
        			if ($v['id'] == $processor_id) {
        				$pro_list = array($v);
        				break;
        			}
        		}
        		
        		foreach ($put_in_typesas as $k => $v) {
        			if ($v['name'] == '5') {
        				$put_in_types= array($v);
        				break;
        			}
        		}
        	}
        }
        
        //var_dump($warehouse);exit;
        $this->render('warehouse_bill_info_l_edit.html', array(
            'view' => new WarehouseBillView(new WarehouseBillModel($id, 21)),
            'dd' => $dd,
            'info' => $info,
            'company' => $company,
            'warehouse' => $warehouse,
            'pro_list' => $pro_list,
        	'put_in_type_list' => $put_in_types,
            'id' => $id,
            ));
    }

    //单据审核 --- JUAN
    public function check($params)
    {
        $result = array('success' => 0, 'error' => '','title'=>'单据审核');
        $id = _Request::get("id");
        $billModel = new WarehouseBillModel($id, 21);
        $warehousid = $billModel->getValue('to_warehouse_id');
        $warehousname = $billModel->getValue('to_warehouse_name');
        $bill_no      = $billModel->getValue('bill_no');
        if ($bill_no == 'L201507012056509') {
            $result['error'] = "L201507012056509单据不能审核，请联系技术(张丽娟)";
            Util::jsonExit($result);
        }
        //根据仓库id获取仓库是否锁定(盘点)
        $billModel->check_warehouse_lock($warehousid);

        $checksession = $this->checkSession($warehousid);

        if (is_string($checksession)) {
            $result['error'] = "您没有<span style='color: #ff0000;'><b>" . $warehousname . "</b>" . $checksession .
                    "</span>的权限请联系管理员开通";
            Util::jsonExit($result);
        }

        $status = $billModel->getValue('bill_status');
        $cUser = $billModel->getValue('create_user');
        if ($status != 1) {
            $result['error'] = "状态不正确不允许审核，已保存状态下才能审核。";
            Util::jsonExit($result);
        }
        if ($cUser == $_SESSION['userName']) {
            $result['error'] = "不能审核自己制的单";
            Util::jsonExit($result);
        }


        //审核单据金额和结算金额必须一致
        $lmodel = new WarehouseBillInfoLModel(21);
        $payModel = new WarehouseBillPayModel(21);
        $proarr = $payModel->getProArr($id) ? $payModel->getProArr($id) : array();
        //if(!in_array($lmodel->getPro($id),$proarr)){
        if (!in_array($billModel->getValue('pro_id'), $proarr)) {
            $result['error'] = "<span style='color: #ff0000'>警告：供应商不存在结算列表中！</span>";
            //Util::jsonExit($result);
        }

        $chengbenjia_goods = $billModel->getValue('goods_total');
        $billModel->cha_deal($id, $chengbenjia_goods);
        /*
        #收货单结算商尾差计算（金额限制需要加上====￥￥￥￥）检查是否有结算商--删除已有的结算商列表中的尾差记录
        //1、计算商品总成本价
        //2、计算结算商总成本
        //3、插入结算商尾差
        //4、修改单据价格总计、支付总计为计算的商品总成本
        $model_pay = new WarehouseBillPayModel(22);
        $model_pay->delete_cha($id);
        $chengbenjia_goods = $billModel->getValue('goods_total');
        $zhifujia_prc	   = $payModel->getAmount($id);
        $cha = $chengbenjia_goods - $zhifujia_prc;
        $new_array = array(
        'bill_id'=>$id,
        'pro_id'  =>366,//暂时是0
        'pro_name'=>'入库成本尾差',
        'pay_content'=>6,//差 数据字典6
        'pay_method'=>1,//'记账'
        'tax'=>2,//数据字典 含 2
        'amount'=>$cha,
        );

        if ($cha != 0)
        {
        #需要限制 自动尾差补充金额大小
        $re_p = $model_pay->saveData($new_array,array());
        if(!$re_p)
        {
        $result['error'] = "结算商尾差计算失败";
        Util::jsonExit($result);
        }
        }
        */
        if ($billModel->getValue('goods_total') != $payModel->getAmount($id)) {
            $result['error'] = "单据总金额和结算总金额不符，不能审核。";
            Util::jsonExit($result);
        }

        //审核单据（事务处理）
        $model = new WarehouseBillInfoLModel(22);
        //添加收货时有布产号，并且布产单和订单有绑定关系，则货品绑定
        $pdolist = $this->_autoBindOrder($id,$bill_no); //返回自动绑定订单事物列表        
        
        $checkResult = $model->checkBillL($id,$pdolist[22]);//$pdolist[22]是warehouse_shipping库的PDO对象
        if ($checkResult) {
            if(defined('IS_ZHOUSHAN_SYS') && IS_ZHOUSHAN_SYS=='YES'){

                $p_result = $model->autoMake_BillInfoP($id,$pdolist[22]);
                if($p_result===false){
                    $result['error'] = "审核失败:自动生成P单失败";
                    Util::jsonExit($result);
                }
                
                $kela_model = new WarehouseBillInfoLModel(220);
                $bill_info_for_kela = $model->getData_For_Tongbu($id);
                if(!empty($bill_info_for_kela)){
                    $l_result = $kela_model->auto_CopyBillL($bill_info_for_kela);
                    if($l_result===false){
                        $pdolist[22]->rollback();//事务回滚
                        $pdolist[22]->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交                        
                        $result['error'] = "审核自动同步L单失败";
                        Util::jsonExit($result);
                    };
                    $pdolist[]=$l_result;
                }else{
                        $pdolist[22]->rollback();//事务回滚
                        $pdolist[22]->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交                     
                        $result['error'] = "审核自动同步L单失败：未获取到收货单及加价率信息";
                        Util::jsonExit($result);                    
                }    
            } 

            //提交自动绑定订单的事物
            try{
                foreach($pdolist as $pdo){
                    $pdo->commit(); // 如果没有异常，就提交事务
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交
                }
                $result['success'] = 1;
                //AsyncDelegate::dispatch('warehouse', array('event'=>'bill_L_checked', 'bill_id' => $id));
            }catch (Exception $e){
                $result['error'] = "审核失败,自动绑定订单事物提交失败";
            }            
            
        } else {
            
            $result['error'] = "审核失败";
        }
        Util::jsonExit($result);
    }


    //集团复核单据
    public function check_repeat($params)
    {
        $result = array('success' => 0, 'error' => '','title'=>'单据审核');
        $id = _Request::get("id");
        $billModel = new WarehouseBillModel($id, 21);
        $warehousid = $billModel->getValue('to_warehouse_id');
        $warehousname = $billModel->getValue('to_warehouse_name');
        $bill_no      = $billModel->getValue('bill_no');
        if($billModel->getValue('fin_check_status')=='2'){
            $result['error'] = "单据已经复核";
            Util::jsonExit($result);            
        }
       
        /*
        $checksession = $this->checkSession($warehousid);

        if (is_string($checksession)) {
            $result['error'] = "您没有<span style='color: #ff0000;'><b>" . $warehousname . "</b>" . $checksession .
                    "</span>的权限请联系管理员开通";
            Util::jsonExit($result);
        }
        */ 

        //审核单据（事务处理）
        $model = new WarehouseBillInfoLModel(22);
        
        $checkResult = $model->checkBillL_repeat($id);//$pdolist[22]是warehouse_shipping库的PDO对象
        if ($checkResult) {           
            $result['success'] = 1;                         
        } else {            
            $result['error'] = "审核失败";
        }
        Util::jsonExit($result);
    }



    //自动绑定订单（需求BOSS-398）
    private function _autoBindOrder($bill_id,$bill_no){
        
        $result = array('success' => 0, 'error' => '','title'=>'审核单据');
        
        $salesModel = new SalesModel(27);        
        $warehouseGoodsModel = new WarehouseGoodsModel(22);
        $billInfoLModel = new WarehouseBillInfoLModel(22);
        $proccessorModel  = new SelfProccesorModel(13);
        
        $pdolist[13] = $proccessorModel->db()->db();
        $pdolist[22] = $warehouseGoodsModel->db()->db();
        $pdolist[27] = $salesModel->db()->db();
        //开启事物
        foreach ($pdolist as $pdo){
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); // 关闭sql语句自动提交
            $pdo->beginTransaction(); // 开启事务
        }
        $bc_sn_list = $billInfoLModel->getBuChanSn($bill_id);
        $bc_ids = array();//收货单中未绑定的货品布产单ID
        $l_goods = array();
        $bh_goods = array();
        foreach ($bc_sn_list as $vo){
            if(empty($vo['order_goods_id'])){
                // 移除开头的字母
                $bc_id = preg_replace('/^[a-zA-Z]+/', '', strtoupper($vo['buchan_sn']));
                $bc_ids[]= $bc_id;
                $l_goods[$bc_id]['goods_id']   = $vo['goods_id'];
                $l_goods[$bc_id]['cat_type']   = $vo['cat_type'];
                $l_goods[$bc_id]['zhengshuhao'] = $vo['zhengshuhao'];
                $l_goods[$bc_id]['goods_sn'] = $vo['goods_sn'];

                $bh_goods[$bc_id][] = $vo;
            }
        }
        $goods_list = $salesModel->getAllowBindGoodsByBcIds($bc_ids);
        /**
         * foreach内部变量特别说明：
         * $goods_id 订单商品货号
         * $order_goods_id 订单商品明细主键ID（app_order_details主键ID）
         * $l_goods_id 收货单商品货号
         * $l_zhengshuhao 收货单商品证书号
         * $l_tuo_type 收货单拖类型
         * $bind_goods_id 原订单商品绑定的货号 
         * $remark 订单日志备注
         */
        foreach ($goods_list as $vo){
 
            $goods_id = $vo['goods_id'];
            $order_goods_id = $vo['order_detail_id'];
            $bc_id = $vo['bc_id'];
            if(!isset($l_goods[$bc_id]['goods_id'])){
                //$result['error'] = "系统异常，文件warehouseBillInfoLController,行".__LINE__;
                Util::rollbackExit("自动绑定订单失败：文件WarehouseBillInfoLController,行".__LINE__,$pdolist);
            }else{
                //获取收货单商品货号($l_goods_id),如果获取失败，应当立即终止并提示报错
                $l_goods_id    =  $l_goods[$bc_id]['goods_id'];
                $l_zhengshuhao =  $l_goods[$bc_id]['zhengshuhao'];
                //$l_tuo_type    =  $l_goods[$bc_id]['tuo_type'];
                $l_cat_type    = $l_goods[$bc_id]['cat_type'];
                $l_goods_sn    = $l_goods[$bc_id]['goods_sn'];
            }
            
            if($l_cat_type == '裸石')
            {
                if(!(strpos($l_zhengshuhao,$vo['zhengshuhao']) || $vo['zhengshuhao'] == $l_zhengshuhao))
                {
                    //'新输入的货号证书号和所下订单证书号不一致，不可以换货！';
                    continue;
                }
            }else{
                if(strtoupper(trim($l_goods_sn))!= strtoupper(trim($vo['goods_sn']))){
                    //'收货单的货号与订单的货号不是同一款式,不可以换货！';
                    continue;
                }
            }

            //查询订单是否绑定商品（$bind_goods_id）
            $bind_goods_id = $warehouseGoodsModel->select2("goods_id","order_goods_id='{$order_goods_id}'");
            //如果原订单商品已绑定货品 begin
            if(!empty($bind_goods_id)){
                //原订单货号进行解绑
                $data = array(
                    'order_goods_id'=>''                    
                );
                $res = $warehouseGoodsModel->update($data,"order_goods_id='{$order_goods_id}'");
                if(!$res){
                    Util::rollbackExit("自动绑定订单失败：原订单货号解绑失败,事物已回滚",$pdolist);
                }
            }//如果原订单商品已绑定货品 end

            //货号自动绑定订单 begin
            $data = array(
                'order_goods_id'=>$order_goods_id,
            );
            $res = $warehouseGoodsModel->update($data,"goods_id='{$l_goods_id}'");
            if(!$res){
                Util::rollbackExit("自动绑定订单失败,事物已回滚",$pdolist);
            }//货号自动绑定订单 end
            
            //将新货号同步到订单明细 begin
            $data = array(
                'goods_id'=>$l_goods_id,           
            );
            $res = $salesModel->updateAppOrderDetail($data,"id={$order_goods_id}");
            if(!$res){
                Util::rollbackExit("自动绑定订单失败：同步订单信息失败,事物已回滚",$pdolist);
            }//将新货号同步到订单明细 end
            
            //订单日志begin
            if(!empty($bind_goods_id)){
                $remark = "收货单{$bill_no}审核后货号{$l_goods_id}自动绑定订单，原货号{$goods_id}自动解绑";
            }else{
                $remark = "收货单{$bill_no}审核后货号{$l_goods_id}自动绑定订单";
            }
            $data = array(
			    'order_id'=>$vo['order_id'],
			    'order_status'=>$vo['order_status'],
			    'shipping_status'=>$vo['send_good_status'],
			    'pay_status'=>$vo['order_pay_status'],
			    'create_user'=>$_SESSION['userName'],
			    'create_time'=>date("Y-m-d H:i:s"),
			    'remark'=>$remark,
			);
            $res = $salesModel->insertOrderAction($data);            
            if(!$res){
                Util::rollbackExit("自动绑定订单失败：订单日志写入失败,事物已回滚",$pdolist);
            }//订单日志end
            
            //布产日志begin
            if(!empty($bind_goods_id)){
                $remark = "收货单{$bill_no}审核后货号{$l_goods_id}自动绑定订单，原货号{$goods_id}自动解绑";
            }else{
                $remark = "收货单{$bill_no}审核后货号{$l_goods_id}自动绑定订单";
            }
            $res = $proccessorModel->addBuchanOpraLog($bc_id,$remark);
            if(!$res){
                Util::rollbackExit("自动绑定订单失败：订单日志布产日志写入失败,事物已回滚",$pdolist);
            }//布产日志end
        }

        //判断剩余布产单号是否外部订单备货绑定单号
        //布产单匹配不到订单明细的布产单号
        $mappingDo = array_column($goods_list,'bc_id');
        $diffBcIds = array_diff($bc_ids,$mappingDo);//查询是否是采购单绑定的
        if(!empty($diffBcIds)){
            //布产单ID查询布产单信息
            $p_ids = $proccessorModel->selectProductInfo(" `id`,`p_id`,`style_sn` ", " `id` in(".implode(",", $diffBcIds).") and `from_type` = 1 ","1");
            if(!empty($p_ids)){
                foreach ($p_ids as $k => $info) {
                    $bc_id = $info['id'];
                    //根据采购单明细ID查出所有需要绑定的订单明细ID
                    $proInfo = $salesModel->getBcSnOrderId($info['p_id']);
                    if(!empty($proInfo)){
                        foreach ($proInfo as $val) {
                            $order_sn = isset($val['order_sn'])?$val['order_sn']:'';
                            $detail_id = isset($val['detail_id'])?$val['detail_id']:'';
                            $poi_id = isset($val['poi_id'])?$val['poi_id']:'0';
                            if(!empty($detail_id)){
                                $l_goods_id = '';
                                $bg_goods_info = $bh_goods[$bc_id];//布产单对于所有货品信息
                                if(!empty($bg_goods_info)){
                                    foreach ($bg_goods_info as $k => $rp) {
                                        $l_cat_type = $rp['cat_type'];
                                        $l_goods_sn = $rp['goods_sn'];
                                        if($l_cat_type != '裸石')
                                        {
                                            if(strtoupper(trim($l_goods_sn))!= strtoupper(trim($info['style_sn']))){
                                                //'收货单的货号的款号与布产单款号不是同一款式,不可以换货！';
                                                continue;
                                            }
                                        }
                                        $l_goods_id = $rp['goods_id'];//获取第一条货号
                                        if($l_goods_id){
                                            unset($bh_goods[$bc_id][$k]);//匹配上则绑定，并删除这件货品，其余进行下次绑定
                                            break;
                                        }
                                    }
                                }
                                if($l_goods_id == '') continue;
                                //货号自动绑定订单 begin
                                $data = array(
                                    'order_goods_id'=>$detail_id,
                                );
                                $res = $warehouseGoodsModel->update($data,"goods_id='{$l_goods_id}'");
                                if(!$res){
                                    Util::rollbackExit("占用备货订单自动绑定订单失败,事物已回滚",$pdolist);
                                }//货号自动绑定订单 end
                                
                                //将新货号同步到订单明细,并且更改订单货品状态为现货 begin
                                $data = array(
                                    'goods_id'=>$l_goods_id,
                                    'is_stock_goods'=>1
                                );
                                $res = $salesModel->updateAppOrderDetail($data,"id={$detail_id}");
                                if(!$res){
                                    Util::rollbackExit("占用备货订单自动绑定订单失败：同步订单信息失败,事物已回滚",$pdolist);
                                }//将新货号同步到订单明细 end

                                //将新货号同步到备货订单关联表 begin
                                $data = array(
                                    'bd_goods_id'=>$l_goods_id,           
                                );
                                $res = $salesModel->updatePurchaseOrderInfo($data,"id={$poi_id}");
                                if(!$res){
                                    Util::rollbackExit("占用备货订单自动绑定订单失败：更新备货订单关联货号失败,事物已回滚",$pdolist);
                                }
                                
                                //订单日志begin
                                $remark = "收货单{$bill_no}审核后货号{$l_goods_id}自动绑定订单";
                                $data = array(
                                    'order_id'=>$val['id'],
                                    'order_status'=>$val['order_status'],
                                    'shipping_status'=>$val['send_good_status'],
                                    'pay_status'=>$val['order_pay_status'],
                                    'create_user'=>$_SESSION['userName'],
                                    'create_time'=>date("Y-m-d H:i:s"),
                                    'remark'=>$remark,
                                );
                                $res = $salesModel->insertOrderAction($data);            
                                if(!$res){
                                    Util::rollbackExit("占用备货订单自动绑定订单失败：订单日志写入失败,事物已回滚",$pdolist);
                                }//订单日志end
                                //如果占用备货的订单都已经绑定货号为现货，订单中没有期货，订单转为现货单，配货状态改为允许配货
                                if($order_sn){
                                    $order_id=$val['id'];
                                    $order_detail_data = $salesModel->getOrderDetailByOrderId($order_id);
                                    $is_peihuo = false;
                                    if(!empty($order_detail_data)){
                                        $xianhuo= 1;
                                        $is_peihuo = true;
                                    }
                                    foreach($order_detail_data as $tmp){
                                        //排除：现货货号 + 不需布产的 + 已出厂 + 已退货
                                        if($tmp['is_stock_goods'] == 0 && $tmp['buchan_status'] !=9 && $tmp['buchan_status'] != 11 && $tmp['is_return'] != 1 ){
                                            $is_peihuo = false;
                                        }
                                        //判断订单所属的商品是否有期货
                                        if($tmp['is_stock_goods'] == 0 && $tmp['is_return'] != 1){
                                            $xianhuo= 0;
                                        }
                                        
                                    }
                                    //当已经变成全款时或者财务备案
                                    if($val['order_pay_status']==3 || $val['order_pay_status']==4){
                                        if($is_peihuo && $val['delivery_status']==1){
                                            $data = array(
                                                'delivery_status'=>2
                                            );
                                            $res = $salesModel->updateBaseOrderInfo($data,"order_sn='{$order_sn}'");
                                            if(!$res){
                                                Util::rollbackExit("占用备货订单自动绑定订单失败：同步订单配货状态信息失败,事物已回滚",$pdolist);
                                            }
                                        }
                                    }
                                    //更新订单期货现货状态
                                    $data = array(
                                        'is_xianhuo'=>$xianhuo
                                    );
                                    $res = $salesModel->updateBaseOrderInfo($data,"order_sn='{$order_sn}'");
                                    if(!$res){
                                        Util::rollbackExit("占用备货订单自动绑定订单失败：同步订单现货期货信息失败,事物已回滚",$pdolist);
                                    }

                                    if($xianhuo == 1){
                                         //订单日志begin
                                        $remark = "订单所有期货都配货成功，已改订单为现货单！";
                                        $data = array(
                                            'order_id'=>$val['id'],
                                            'order_status'=>$val['order_status'],
                                            'shipping_status'=>$val['send_good_status'],
                                            'pay_status'=>$val['order_pay_status'],
                                            'create_user'=>$_SESSION['userName'],
                                            'create_time'=>date("Y-m-d H:i:s"),
                                            'remark'=>$remark,
                                        );
                                        $res = $salesModel->insertOrderAction($data);            
                                        if(!$res){
                                            Util::rollbackExit("占用备货订单自动绑定订单失败：订单日志写入失败,事物已回滚",$pdolist);
                                        }//订单日志end
                                    }
                                }
                                
                                //布产日志begin
                                $remark = "收货单{$bill_no}审核后货号{$l_goods_id}自动绑定订单";
                                $res = $proccessorModel->addBuchanOpraLog($bc_id,$remark);
                                if(!$res){
                                    Util::rollbackExit("占用备货订单自动绑定订单失败：订单日志布产日志写入失败,事物已回滚",$pdolist);
                                }//布产日志end
                            }
                        }
                    }
                }
            }
        }
        //Util::rollbackExit("boss-398代码执行完毕，系统运行正常",$pdolist);
        return $pdolist;
    } 
    /**
     *	show，渲染查看页面
     */
    public function show($params)
    {
        #财务连接
        if (isset($params['bill_no'])) {
            $model = new WarehouseBillModel(21);
            $id = $model->getIdByBillNo(trim($params['bill_no']));
        } else {
            $id = intval($params["id"]);
        }
        //$id = intval($params["id"]);	//单据ID

        #获取单据附加表ID warehouse_bill_info_l
        $model = new WarehouseBillInfoLModel(21);
        //$row = $model-> getRow_Bill_id($id);

        //结算商总金额
        $payModel = new WarehouseBillPayModel(21);
        $pay_price = $payModel->getAmount($id);
        
        //获取取单据取消时的操作用户和操作时间
        $WarehouseBillModel = new WarehouseBillModel(21);
        $billcloseArr=$WarehouseBillModel->get_bill_close_status($id);
       
        $this->render('warehouse_bill_info_l_show.html', array(
            'view' => new WarehouseBillView(new WarehouseBillModel($id, 21)),
            'dd' => new DictView(new DictModel(1)),
            'bar' => Auth::getViewBar(),
            'pay_price' => $pay_price,
        	'billcloseArr'=>$billcloseArr,
        	'isViewChengbenjia'=>$this->isViewChengbenjia(),
            ));
    }
    //详情页面的明细
    public function showlist($params)
    {
        //财务成本采购需要查看根据单据号
        if (isset($params['bill_no'])) {
            $model = new WarehouseBillModel(21);
            $id = $model->getIdByBillNo(trim($params['bill_no']));
        } else {
            $id = intval($params["id"]);
        }
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__class__, 0, -10),
            'act' => __function__,
            'id' => $id);
        $modeldo = new WarehouseBillModel($id, 21);
        $do = $modeldo->getDataObject();
        $detailModel = new WarehouseBillInfoLModel(21);
        $SalesModel = new SalesModel(27);
        $where = array('bill_id' => $id);
        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;
        $data = $detailModel->getPagetList($where, $page, 10, false);
        if(!empty($data['data'])){
            if($do['bill_status'] != 1){
                foreach ($data['data'] as $k => $val) {
                    $bd_order_sn = $SalesModel->getOrderSnByGoodsId($val['goods_id']);
                    $data['data'][$k]['bind_order_sn'] = $bd_order_sn;
                }
            }else{
                $bc_info = array_column($data['data'], 'buchan_sn');
                if(!empty($bc_info)){
                    $res = $SalesModel->showBcBillToCaigou($bc_info);
                    if(!empty($res)){
                        foreach ($data['data'] as $k => $val) {
                            if(isset($res[$val['buchan_sn']]) 
                                && !empty($res[$val['buchan_sn']])){
                                $bclist = $res[$val['buchan_sn']];
                                foreach ($bclist as $key => $sn) {
                                    $data['data'][$k]['bind_order_sn'] = $sn;
                                    unset($res[$val['buchan_sn']][$key]);
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'warehouse_bill_l_show';
        $this->render('warehouse_bill_info_l_showlist.html', array(
            'pa' => Util::page($pageData),
            'dd' => new DictView(new DictModel(1)),
            'data' => $data,
        	'isViewChengbenjia'=>$this->isViewChengbenjia(),
            ));
    }
    /*
    * 收货单打印详情 added by linphie
    */
    public function print_q($params)
    {
        $id = intval($params['id']);
        $where = array('bill_id' => $id);
        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;
        $detailModel = new WarehouseBillInfoLModel(21);
        $data = $detailModel->getPagetList($where, $page, 10, false);
        $data = $data['data'];
        //var_dump($data);exit;
        //header("Content-Disposition: attachment;filename=huopin.csv");
        $str = "货号,款号,商品名称,模号,饰品分类,款式分类,主成色,主成色重,金耗,主成色重计价,主成色买入单价,主成色买入成本,主成色计价单价,主石,主石粒数,主石重,主石重计价,主石颜色,主石净度," .
            "主石买入单价,主石买入成本,主石计价单价,主石切工,主石形状,主石包号,主石规格,副石粒数,副石重,副石重计价,副石颜色,副石净度,副石买入单价\n";
        foreach ($data as $key => $val) {
            $str .= $val['goods_id'] . ",";
            $str .= $val['goods_sn'] . ",";
            $str .= $val['goods_name'] . ",";
            $str .= $val['mo_sn'] . ",";
            $str .= $val['product_type'] . ",";
            $str .= $val['cat_type'] . ",";
            $str .= $val['caizhi'] . ",";
            $str .= $val['jinzhong'] . ",";
            $str .= $val['jinhao'] . ",";
            $str .= $val['zhuchengsezhongjijia'] . ",";
            $str .= $val['zhuchengsemairudanjia'] . ",";
            $str .= $val['zhuchengsemairuchengben'] . ",";
            $str .= $val['zhuchengsejijiadanjia'] . ",";
            $str .= $val['zhushi'] . ",";
            $str .= $val['zhushilishu'] . ",";
            $str .= $val['jinzhong'] . ",";
            $str .= $val['zhushizhongjijia'] . ",";
            $str .= $val['zhushiyanse'] . ",";
            $str .= $val['zhushijingdu'] . ",";
            $str .= $val['zhushimairudanjia'] . ",";
            $str .= $val['zhushimairuchengben'] . ",";
            $str .= $val['zhushijijiadanjia'] . ",";
            $str .= $val['zhushiqiegong'] . ",";
            $str .= $val['zhushixingzhuang'] . ",";
            $str .= $val['zhushibaohao'] . ",";
            $str .= $val['zhushiguige'] . ",";
            $str .= $val['fushilishu'] . ",";
            $str .= $val['fushizhong'] . ",";
            $str .= $val['fushizhongjijia'] . ",";
            $str .= $val["fushiyanse"] . ",";
            $str .= $val['fushijingdu'] . ",";
            $str .= $val['fushimairudanjia'] . "\n";

        }
        header("Content-type: text/html; charset=gbk");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=" . iconv("utf-8", "gbk", date("Y-m-d")) .
            "_print_detail.csv");
        echo iconv("utf-8", "gbk", $str);


    }
    /**
     * mkJson 生成Json表单
     */
    public function mkJson()
    {
        $id = _Post::getInt('id');
        $arr = Util::iniToArray(APP_ROOT . 'warehouse/data/from_table_bill_l.tab');
        $detailModel = new WarehouseBillInfoLModel(21);
        $detail_arr = $detailModel->get_data(array('bill_id' => $id));

        $detail = array();
        foreach ($detail_arr as $key => $val) {
            $detail[$key][] = $val['goods_id'];
            $detail[$key][] = $val['goods_sn'];
            $detail[$key][] = $val['mo_sn'];
            $detail[$key][] = $val['product_type'];
            $detail[$key][] = $val['cat_type'];            
            $detail[$key][] = $val['caizhi']; //主成色（材质）
            $detail[$key][] = $val['jinzhong']; //主成色重
            $detail[$key][] = $val['jinhao'];
            $detail[$key][] = $val['zhuchengsezhongjijia'];
            $detail[$key][] = $val['zhuchengsemairudanjia'];
            
            $detail[$key][] = $val['zhuchengsemairuchengben'];
            $detail[$key][] = $val['zhuchengsejijiadanjia'];
            $detail[$key][] = $val['zhushi'];
            $detail[$key][] = $val['zhushilishu'];
            $detail[$key][] = $val['zuanshidaxiao'];
            $detail[$key][] = $val['zhushizhongjijia'];
            $detail[$key][] = $val['zhushiyanse'];
            $detail[$key][] = $val['zhushijingdu'];
            $detail[$key][] = $val['zhushimairudanjia'];
            $detail[$key][] = $val['zhushimairuchengben'];
            
            $detail[$key][] = $val['zhushijijiadanjia'];
            $detail[$key][] = $val['zhushiqiegong'];
            $detail[$key][] = $val['zhushixingzhuang'];
            $detail[$key][] = $val['zhushibaohao'];
            $detail[$key][] = $val['zhushiguige'];           
            $detail[$key][] = $val['fushi'];
            $detail[$key][] = $val['fushilishu'];
            $detail[$key][] = $val['fushizhong'];
            $detail[$key][] = $val['fushizhongjijia'];
            $detail[$key][] = $val['fushiyanse'];
            
            $detail[$key][] = $val['fushijingdu'];
            $detail[$key][] = $val['fushimairudanjia'];
            $detail[$key][] = $val['fushimairuchengben'];
            $detail[$key][] = $val['fushijijiadanjia'];
            $detail[$key][] = $val['fushixingzhuang'];            
            $detail[$key][] = $val['fushibaohao'];
            $detail[$key][] = $val['fushiguige'];
            $detail[$key][] = $val['zongzhong'];
            $detail[$key][] = $val['mairugongfeidanjia'];
            $detail[$key][] = $val['mairugongfei'];
            
            $detail[$key][] = $val['jijiagongfei'];
            $detail[$key][] = $val['shoucun'];
            $detail[$key][] = $val['ziyin'];
            $detail[$key][] = $val['danjianchengben'];
            $detail[$key][] = $val['peijianchengben'];
            $detail[$key][] = $val['qitachengben'];
            $detail[$key][] = $val['chengbenjia'];
            $detail[$key][] = $val['jijiachengben'];
            $detail[$key][] = $val['jiajialv'];
            $detail[$key][] = $val['zuixinlingshoujia'];
            
            $detail[$key][] = $val['pinpai'];
            $detail[$key][] = $val['luozuanzhengshu'];
            $detail[$key][] = $val['changdu'];
            $detail[$key][] = $val['gemx_zhengshu']; //GEMX证书号
            $detail[$key][] = $val['zhengshuhao'];
            $detail[$key][] = $val['yanse'];
            $detail[$key][] = $val['jingdu'];
            $detail[$key][] = $val['peijianshuliang'];
            $detail[$key][] = $val['guojizhengshu'];
            $detail[$key][] = $val['zhengshuleibie'];
            $detail[$key][] = $val['goods_name'];
            
            $detail[$key][] = $val['kela_order_sn']; //订单号
            $detail[$key][] = $val['shi2'];
            $detail[$key][] = $val['shi2lishu'];
            $detail[$key][] = $val['shi2zhong'];
            $detail[$key][] = $val['shi2zhongjijia'];
            $detail[$key][] = $val['shi2mairudanjia'];
            $detail[$key][] = $val['shi2mairuchengben'];
            $detail[$key][] = $val['shi2jijiadanjia'];
            $detail[$key][] = $val['qiegong'];
            $detail[$key][] = $val['paoguang'];
            
            $detail[$key][] = $val['duichen'];
            $detail[$key][] = $val['yingguang'];            
            $detail[$key][] = $val['buchan_sn'];
            $detail[$key][] = $val['mingyichengben'];
            $detail[$key][] = $val['zuanshizhekou'];
            $detail[$key][] = $val['zhengshuhao2'];
           
            $detail[$key][] = $val['guojibaojia'];
            $detail[$key][] = $val['gongchangchengben'];
            if($val['tuo_type']==1){
            	$detail[$key][]='成品';
            }elseif($val['tuo_type']==2){
            	$detail[$key][]='空托女戒';
            }elseif($val['tuo_type']==3){
            	$detail[$key][]='空托';
            }else{
            	$detail[$key][]='';
            }           
            
            
            
            
            
            $detail[$key][] = $val['jietuoxiangkou'];
            $detail[$key][] = $val['zhushitiaoma'];
            $detail[$key][] = $val['color_grade'];
            $detail[$key][] = $val['supplier_code'];
            $detail[$key][] = $val['peijianjinchong'];
            $detail[$key][] = $val['shi2baohao'];
            $detail[$key][] = $val['shi3'];
            $detail[$key][] = $val['shi3lishu'];
            $detail[$key][] = $val['shi3zhong'];
            $detail[$key][] = $val['shi3zhongjijia'];
            $detail[$key][] = $val['shi3mairudanjia'];
            $detail[$key][] = $val['shi3mairuchengben'];
            $detail[$key][] = $val['shi3jijiadanjia'];
            $detail[$key][] = $val['shi3baohao'];
           

        }
        $arr['data'] = $detail;
        $json = json_encode($arr);

        echo $json;
    }

    /**
     *	insert，信息入库
     */
    public function insert($params)
    {
        $result = array('success' => 0, 'error' => '');
        $tab_id = _Post::getInt('tab_id');

        if ($params['to_warehouse_id'] == '') {
            $result['error'] = "入库仓库必选！";
            Util::jsonExit($result);
        }
        $ar2 = explode("|", $params['to_warehouse_id']);
        $to_warehouse_id = $ar2[0];
        $to_warehouse_name = $ar2[1];

        /*
        $res = $this->checkHosePremission($to_warehouse_id);
        if ($res !== true) {
            $result = ['success' => 0, 'error' => $res];
            Util::jsonExit($result);
        }*/
        		$checksession = $this->checkSession($to_warehouse_id);
        		if(is_string($checksession)){
        			$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$to_warehouse_name."</b>".$checksession."</span>的权限请联系管理员开通");
        			Util::jsonExit($result);
        		}

        if ($params['prc_id'] == '') {
            $result['error'] = "加工商必选！";
            Util::jsonExit($result);
        }
        
        $put_in_type = _Post::getInt('put_in_type');
        if ($put_in_type == '') {
        	$result['error'] = "入库方式必选！";
        	Util::jsonExit($result);
        }
        
        $ar = explode("|", $params['prc_id']);
        $pro_id = $ar[0];
        $pro_name = $ar[1];
        
        if (SYS_SCOPE == 'zhanting' && isset($_SESSION['companyId']) && !empty($_SESSION['companyId'])) {
        	$company = new CompanyModel(1);
        	$processor_id = $company->select2('processor_id', ' id='.$_SESSION['companyId'], 3);
        	if (!empty($processor_id) && $pro_id != $processor_id) {
        		$result['error'] = "所选加工商不是当前公司关联的加工商!";
        		Util::jsonExit($result);
        	}
        	if (!empty($processor_id) && $put_in_type != '5') {
        		$result['error'] = "外协加工商时做收货单，入库方式须为自采!";
        		Util::jsonExit($result);
        	}
        } 
        
        $cinfo = $this->getCompanyInfo($to_warehouse_id);
        $to_company_id = $cinfo['company_id'];
        $to_company_name = $cinfo['company_name'];
        $ship_num = _Post::get('ship_num');
        $order_sn = _Post::get('order_sn');
        $jiejia = _Post::get('jiejia');
        $bill_note = _Post::get('bill_note');
        $tab_id = _Post::getInt('tab_id');

        if ($ship_num == '') {
            $result['error'] = "送货单号不能为空！";
            Util::jsonExit($result);
        }


        if ($jiejia == '') {
            $result['error'] = "是否结价必选！";
            Util::jsonExit($result);
        }
        if (!isset($_POST['create_goods_grid'])) {
            $result['error'] = "请先导入文件！";
            Util::jsonExit($result);
        }

        //整理上传数据
        $time = date('Y-m-d H:i:s');
        $grid = json_decode($_POST['create_goods_grid'], true);

        $in_goods_sn = array();

        if (!count($grid)) {
            $result['error'] = "上传内容不能为空";
            Util::jsonExit($result);
        }
        $SelfProccesorModel = new SelfProccesorModel(13);
        $pro_array=$SelfProccesorModel->getProInfo('prc_id='.$pro_id,'production_manager_name');
        $production_manager_name=$pro_array['production_manager_name'];
        $model = new WarehouseBillInfoLModel(22);
        //整理货品信息
        $array = $model->arrangementData($grid, $pro_id,$put_in_type);
        $dataArr = $array['dataArr'];
        $chengbenzongjia = $array['chengbenzongjia'];
		//print_r($dataArr);exit;
        //判断款号       
        $isstyle=true;
        $error = '';
        foreach($dataArr as $v){
        	$goods_sn=$v['goods_sn'];
        	if($goods_sn==''){
        		$result['error'] = "导入的款号不能为空！";
        		Util::jsonExit($result);
        	}
			if($v['cat_type'] == '裸石' || $v['cat_type'] == '彩钻'){
				continue;
			}
			if(preg_match("/^((A|B|M|W|a|b|m|w)([\S]{4}))$/",$goods_sn)||preg_match("/^((W|w)([\S]{7}))$/",$goods_sn)||preg_match("/^((KL|kl)[\S]{8})$/",$goods_sn)) {
                $BaseStyleModel = new BaseStyleInfoModel(11);
                $res1 = $BaseStyleModel->isBaseStyle($goods_sn);
                if (empty($res1)) {
                    $isstyle = false;
                    $error .= $goods_sn . ' ';                   
                }
            }
        }
        if(!$isstyle){
        	$result['error'] = "款号{$error}不存在或未审核！";
        	Util::jsonExit($result);
        }
       
        //单据信息
        $billArr = array(
            'bill_no' => '',
            'bill_type' => 'L',
            'goods_num' => count($dataArr),
            'to_company_id' => $to_company_id,
            'to_company_name' => $to_company_name,
            'to_warehouse_id' => $to_warehouse_id,
            'to_warehouse_name' => $to_warehouse_name,
            'bill_note' => $bill_note,
            'goods_total' => $chengbenzongjia,
            'yuanshichengben' => $chengbenzongjia,
            'shijia' => 0,
            'put_in_type' => $put_in_type,
            'send_goods_sn' => $ship_num,
            'order_sn' => $order_sn,
            'pro_id' => $pro_id,
            'pro_name' => $pro_name,
            'jiejia' => $jiejia,
            'create_time' => $time,
            'create_user' => $_SESSION['userName'],
            'create_ip' => Util::getClicentIp(),
        	'production_manager_name'=>$production_manager_name,	
            );
        
          //获取添加的结算商
          //$billPayArr=$_SESSION['bill_pay'];
          $olddo_str = $_COOKIE['bill_pay'];
		  $billPayArr=unserialize($olddo_str);         
          $isbillPay=false;
          $billSum = 0;
          foreach($billPayArr as $row){
          	if($row['pro_id']==$pro_id){
          		$isbillPay = true;
          	}
          	$billSum += $row['amount'];
          }
          /** 检查尾差不能超过±100 zzm 2015-11-20**/ 
          $cha = $chengbenzongjia - $billSum;
    	  if ($cha > 100 || $cha < -100) {
            $result['error'] = "入库成本尾差不充许超过±100元，请检查是否制单错误。";
            Util::jsonExit($result);
          }
          /**end**/
          if (!$isbillPay) {
          	$result['error'] = "<span style='color: #ff0000'>警告：供应商不存在结算列表中！</span>";
          	Util::jsonExit($result);
          }



        /* 2019-02-25
         $billgoodsmodel = new WarehouseBillGoodsModel(21);

         $companyArr=Array('拆货管理员','内部转仓','石料管理','货品组装','盘盈入库','入库成本尾差','BDD金条回购');
         if(!in_array($pro_name, $companyArr) && $pro_id != 473){
             $goodssnArr=$dataArr;
             $goodssnArr=array_unique($goodssnArr);

             // print_r($goodssnArr);exit;

             $isinfac=0;
             $error="<span style='color: #ff0000'>警告：供应商和款号 ";
             foreach ($goodssnArr as $val){
                 $isstyle=$model->getsStyle($val['goods_sn']);
                 if(!$isstyle){
                     continue;
                 }
                 $factoryIdArr=$model-> getFactoryIdAll($pro_id,$val['goods_sn']);
                 if(empty($factoryIdArr)){
                     $isinfac=1;
                     $error.=$val['goods_sn']." ";
                 }

             }

             if($isinfac==1 && SYS_SCOPE == 'boss'){
                 $result['error']=$error." 维护的工厂及关联工厂不对应</span>";
                 Util::jsonExit($result);
             }
         }
         */
        
        $res = $model->add_info($dataArr, $billArr); //$dataArr是excel传过来的数据        $billArr,入仓库warehouse_bill
        if (is_array($res) && $res['id'] > 0) {
			$model->updateStylePrice($res['id']);
            $result['success'] = 1;
            $result['infomsg'] = " 您的收货单编号为<br/><span style='color: #ff0000;'>" . $res['bill_no'] .
                "</span>";
            $result['label'] = $res['bill_no'];
            $result['x_id'] = $res['id'];
            $result['tab_id'] = mt_rand();   
        } else {
            $result['error'] = empty($res) ? '添加失败' : $res;
        }
        Util::jsonExit($result);
    }

    public function col($str)
    {
        $index = 0;
        for ($i = 0; $i <= strlen($str) - 1; $i++) {
            $v = ord(strtoupper($str[$i])) - 65;
            $index += pow(26, $i) + $v;
        }
        return $index - 1;
    }

    /**
     *	update，更新信息
     */
    public function update($params)
    {
        $result = array('success' => 0, 'error' => '');
        $id = _Post::getInt('id');

        $put_in_type = _Post::getInt('put_in_type');
        if ($put_in_type == '') {
        	$result['error'] = "入库方式必选！";
        	Util::jsonExit($result);
        }
        
        $prc = _Post::get('prc_id');
        $ar = explode("|",$prc);
        $prc_id		= $ar[0];
        $prc_name	= $ar[1];   
        
        if (SYS_SCOPE == 'zhanting' && isset($_SESSION['companyId']) && !empty($_SESSION['companyId'])) {
        	$company = new CompanyModel(1);
        	$processor_id = $company->select2('processor_id', ' id='.$_SESSION['companyId'], 3);
        	if (!empty($processor_id) && $prc_id != $processor_id) {
        		$result['error'] = "所选加工商不是当前公司关联的加工商!";
        		Util::jsonExit($result);
        	}
        	if (!empty($processor_id) && $put_in_type != '5') {
        		$result['error'] = "外协加工商时做收货单，入库方式须为自采!";
        		Util::jsonExit($result);
        	}
        } 
        
        $ar2 = explode("|", $params['to_warehouse_id']);
        $to_warehouse_id = $ar2[0];
        $to_warehouse_name = $ar2[1];
        $checksession = $this->checkSession($to_warehouse_id);
        if(is_string($checksession)){
                    $result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$to_warehouse_name."</b>".$checksession."</span>的权限请联系管理员开通");
                    Util::jsonExit($result);
        }
        /*
        $res = $this->checkHosePremission($to_warehouse_id);
        if ($res !== true) {
            $result = ['success' => 0, 'error' => $res];
            Util::jsonExit($result);
        }*/

        $cinfo = $this->getCompanyInfo($to_warehouse_id);
        if (!$cinfo['company_id']) {
            $result['error'] = "没查到对应的公司！";
            Util::jsonExit($result);
        }
        $to_company_id = $cinfo['company_id'];
        $to_company_name = $cinfo['company_name'];

        //$to_warehouse_name = _Post::get('to_warehouse_name');
        //$to_company_name = _Post::get('to_company_name');
        //$to_warehouse_id = _Post::get('to_warehouse_id');
        //$to_company_id = _Post::get('to_company_id');
    
        $ship_num = _Post::get('ship_num');

        $jiejia = _Post::get('jiejia');
        $bill_note = _Post::get('bill_note');
        $bill_no = _Post::get('bill_no');
        $tab_id = _Post::getInt('tab_id');
        $chengbenzongjia = _Post::get('chengbenjia');
        $goods_num = _Post::getInt('goods_num');
        $amountTotal = _Post::get('amountTotal');

        $SelfProccesorModel = new SelfProccesorModel(13);
        $pro_array = $SelfProccesorModel->getProInfo('prc_id='.$prc_id,'production_manager_name');
        if(!empty($pro_array['production_manager_name'])){
            $production_manager_name = $pro_array['production_manager_name'];
        }else{
            $production_manager_name = '';
        }
        
        $billmodel = new WarehouseBillModel($id, 21);
        $info = $billmodel->getDataObject();

        if ($info['create_user'] != $_SESSION['userName']) {
            //$result['error'] = '单据非本人不能编辑';
            //Util::jsonExit($result);
        }
        if ($ship_num == '') {
            $result['error'] = "送货单号不能为空！";
            Util::jsonExit($result);
        }
        if ($jiejia == '') {
            $result['error'] = "是否结价必选！";
            Util::jsonExit($result);
        }
        if (abs($amountTotal) > 100) {
            $result['error'] = "入库成本尾差不充许超过±100元，请检查是否制单错误。";
            Util::jsonExit($result);
        }
       
        $payModel = new WarehouseBillPayModel(21);
        $proarr = $payModel->getProArr($id) ? $payModel->getProArr($id) : array();
        //if(!in_array($lmodel->getPro($id),$proarr)){
        if (!in_array($prc_id, $proarr)) {
        	$result['error'] = "<span style='color: #ff0000'>警告：供应商不存在结算列表中！</span>";
        	Util::jsonExit($result);
        }
        
        
        $model = new WarehouseBillInfoLModel(22);
        $billgoodsmodel = new WarehouseBillGoodsModel(21);
        
        $companyArr=Array('拆货管理员','内部转仓','石料管理','货品组装','盘盈入库','入库成本尾差','BDD金条回购');
        if(!in_array($prc_name, $companyArr) && $prc_id != 473){
	        $goodssnArr = $billgoodsmodel->select2("distinct goods_sn", "bill_no='$bill_no'",3);
	        //$goodssnArr = array_unique($goodssnArr);	           
	         // print_r($goodssnArr);exit;
	        $isinfac=0;
	        $error="<span style='color: #ff0000'>警告：供应商和款号 ";
	        foreach ($goodssnArr as $val){    
	        	$isstyle=$model->getsStyle($val['goods_sn']);
	        	if(!$isstyle){
	        		continue;
	        	}   	        	
	        	$factoryIdArr=$model-> getFactoryIdAll($prc_id,$val['goods_sn']);                  	
	        	if(empty($factoryIdArr)){
	        		$isinfac=1;
	        		$error.=$val['goods_sn']." ";
	        	}
	        	     	
	        }
	       
	       if($isinfac==1 && SYS_SCOPE == 'boss'){
	       	$result['error']=$error." 维护的工厂及关联工厂不对应</span>";
	       	Util::jsonExit($result);
	       }
        }
        //编辑的时候没有引入文件，则是没有修改商品明细
        ///////////修改明细信息==============start=====================//////////
        if(empty($_POST['create_goods_grid'])){
            $grid = $model->getBillGoodsGridByBillId($id);
        }else{
            $grid = json_decode($_POST['create_goods_grid'], true);
        }
        $dataArr = array();
        if (isset($_POST['create_goods_grid'])) {

            //整理上传数据
            $time = date('Y-m-d H:i:s');
            //整理货品信息
            $array = $model->arrangementData($grid, $prc_id,$put_in_type);
            $dataArr = $array['dataArr'];
            $goods_num = count($dataArr);
            $chengbenzongjia = $array['chengbenzongjia'];

            if (!count($dataArr)) {
                $result['error'] = "上传内容不能为空";
                Util::jsonExit($result);
            }
        }

        $billArr = array(
            'id' => $id,
            'bill_no' => $bill_no,
            'bill_type' => 'L',
            'goods_num' => $goods_num,
            'to_company_name' => $to_company_name,
            'to_warehouse_name' => $to_warehouse_name,
            'to_company_id' => $to_company_id,
            'to_warehouse_id' => $to_warehouse_id,
            'pro_id' => $prc_id,
            'pro_name' => $prc_name,
            'bill_note' => $bill_note,
            'yuanshichengben' => $chengbenzongjia,
            'goods_total' => $chengbenzongjia,
            'shijia' => 0,
            'put_in_type' => $put_in_type,
            'send_goods_sn' => $ship_num,
        	'production_manager_name'=>$production_manager_name,
            'jiejia' => $jiejia);
        ///////////修改明细信息==============end=====================//////////
        $res = $model->up_info($dataArr, $billArr);
        if ($res['success']) {
            $result['success'] = 1;
        } else {
            $result['error'] = "修改失败。".$res['error'];
        } 
        Util::jsonExit($result);
    }


    public function getTemplate()
    {
        $str = "序号,款号,名称,数量,所在公司,所在仓库,产品线,款式分类,材质,金重,金耗,颜色,钻石大小,成本价,名义成本,税费,客来石信息\n";
        header("Content-Disposition: attachment;filename=shouhuo.csv");
        echo iconv("utf-8", "gbk", $str);
    }


    public function downLoadExcel()
    {
        // 		echo 'downLoadExcel';exit;
        $user_id = _Get::getInt('user_id');
        if (SYS_SCOPE == 'boss') {
            $temexcel_file = 'apps/warehouse/exceltemp/shouhuo.xls';
            if(defined('IS_ZHOUSHAN_SYS') && IS_ZHOUSHAN_SYS=='YES')
               $temexcel_file = 'apps/warehouse/exceltemp/shouhuo_zs.xls'; 
        } else if (SYS_SCOPE == 'zhanting') {
            $temexcel_file = 'apps/warehouse/exceltemp/shouhuo_zt.xls';
        }
        //$filedir = "apps/warehouse/exceltemp/";
        $user_file = 'kela_2010' . $user_id . "kela_2015.xls";
        $file = fopen($temexcel_file, 'r');

        header('Content-type: application/octet-stream');
        header("Accept-Ranges:bytes");
        header("Accept-length:" . filesize($temexcel_file));
        header('Content-Disposition: attachment;filename=' . $user_file);
        ob_clean();
        $a = fread($file, filesize($temexcel_file));
        fclose($file);
        echo $a;
    }


    //修改单据列表的Excel
    public function downLoadEditExcel()
    {

        $user_id = $_SESSION['userId'];
        $order_id = _Get::getString('order_id');
        //$order_type =_Get::getInt('order_type');
        if (SYS_SCOPE == 'boss') {
            $temexcel_file = 'apps/warehouse/exceltemp/shouhuoedit.xls';
            if(defined('IS_ZHOUSHAN_SYS') && IS_ZHOUSHAN_SYS=='YES')
                $temexcel_file = 'apps/warehouse/exceltemp/shouhuoedit_zs.xls';
        } else {
            $temexcel_file = 'apps/warehouse/exceltemp/shouhuoedit_zt.xls';
        }
        $user_file = 'kela_2010' . $user_id . '_' . $order_id . 'kela_2015.xls';
        //通过模板来生成符合excel规范的excel文件   提供 user_id和order_id
        $file = fopen($temexcel_file, 'r');
        $file_size = filesize($temexcel_file);
        header('Content-type: application/octet-stream');
        header("Accept-Ranges:bytes");
        header("Accept-length:" . $file_size);
        header('Content-Disposition: attachment;filename=' . $user_file);
        ob_clean();

        $buffer = 1024;
        $file_count = 0;

        while (!feof($file) && $file_count < $file_size) {
            $file_con = fread($file, $buffer);
            $file_count += $buffer;
            echo $file_con;
        }
        //$a = fread($file, filesize($temexcel_file));
        //echo $a;
        fclose($file);
    }

    //这里推送excel要修改的数据
    public function getGoodsInfo($params)
    {
        //$res = array("error" => "0", "msg" => "");
        $model = new WarehouseBillInfoLModel(22);

        $ret = $model->get_Bill_goods($params['billno']);

        $str = "";
        foreach ($ret as $key => $line) {
            $line['id'] = $key + 1;
            if ($line['tuo_type'] == '1') {
                $tuo_type = '成品';
            } elseif ($line['tuo_type'] == '2') {
                $tuo_type = '空托女戒';
            } elseif ($line['tuo_type'] == '3') {
                $tuo_type = '空托';
            }
            $line['tuo_type'] = $tuo_type;

            foreach ($line as $k => $v) {
                $str .= trim($v) . ';';
            }
            $str = substr($str, 0, -1);
            $str .= '|||';
        }
        $str = substr($str, 0, -3);
        //echo $str;
        echo $str;
        exit;
        /*
        $arr =array('bill_no'=>_Get::getString('billno'));
        $model = new WarehouseBillInfoLModel(22);
        $goods_info = $model->get_data($arr);
        if($goods_info === array()){
        $res = array("error" => "1", "msg" => "没有对应单号的商品");
        echo $this->arrtoxml($res);
        exit;
        }
        $res['goods_list'] = $goods_info;
        echo $this->arrtoxml($res);
        exit;
        */
    }

    //Excel 推过来的数据 进行创建写入 文件
    public function writeJs()
    {

        $content = $_POST['content'];
        $contents = explode('|', $content);
        $sn = $_POST['sn'];

        $l_arr = array();
        $j_error = "";

        $chengbenjia = $cnt = 0;
        $contents_cnt = count($contents);
     for ($i = $contents_cnt - 1  ; $i >= 0 ; $i -- ){
			if ($contents[$i] != null){
				$d = explode(';', $contents[$i]);

				if ($d[1] != ''){
                    if(trim($d[4])=="裸石" || trim($d[4])=="彩钻"){
					    //$xiaoshouchengben = ($d[72] == '' || $d[72] == 0) ? round(trim($d[46]),2) : round(trim($d[72]),2);
                        $xiaoshouchengben = $d[72];
                        $certificate_fee = $d[85] === ''?0:$d['85'];
                        //$xiaoshouchengben = ($d[72] == '' || $d[72] == 0) ? round(trim($d[46]),2) : round(trim($d[72]),2);
					}else{ 
						//$xiaoshouchengben = trim($d[46]) !='' ? round(trim($d[46]),2) : '0';
                        $xiaoshouchengben = $d[72];
                        $certificate_fee = $d[85];
                    } 
					if ('空托' == trim($d[77])){
						$tuo_type = '3';
					}else{
						$tuo_type = '1';
					}
					$d[1] = strtoupper($d[1]);
					$goods[] = array(
						'og_id'=>'',
						'goods_id'=>trim($d[0]),
						'goods_sn'=>trim($d[1]),
						'mo_sn'=>trim($d[2]),
						'product_type'=>trim($d[3]),
						'cat_type'=>trim($d[4]),
						'caizhi'=>trim($d[5]),
						'jinzhong'=>round(trim($d[6]),3),
						'jinhao'=>trim($d[7]),
						'zhuchengsezhongjijia'=>trim($d[8]),
						'zhuchengsemairudanjia'=>round(trim($d[9]),2),
						'zhuchengsemairuchengben'=>round(trim($d[10]),2),
						'zhuchengsejijiadanjia'=>round(trim($d[11]),2),
						'zhushi'=>trim($d[12]),
						'zhushilishu'=>trim($d[13]),
						'zuanshidaxiao'=>round(trim($d[14]),3),
						'zhushizhongjijia'=>trim($d[15]),
						'zhushiyanse'=>trim($d[16]),
						'zhushijingdu'=>trim($d[17]),
						'zhushimairudanjia'=>round(trim($d[18]),2),
						'zhushimairuchengben'=>round(trim($d[19]),2),
						'zhushijijiadanjia'=>round(trim($d[20]),2),
						'zhushiqiegong'=>trim($d[21]),
						'zhushixingzhuang'=>trim($d[22]),
						'zhushibaohao'=>trim($d[23]),
						'zhushiguige'=>trim($d[24]),
						'fushi'=>trim($d[25]),
						'fushilishu'=>trim($d[26]),
						'fushizhong'=>round(trim($d[27]),3),
						'fushizhongjijia'=>trim($d[28]),
						'fushiyanse'=>trim($d[29]),
						'fushijingdu'=>trim($d[30]),
						'fushimairudanjia'=>round(trim($d[31]),2),
						'fushimairuchengben'=>round(trim($d[32]),2),
						'fushijijiadanjia'=>round(trim($d[33]),2),
						'fushixingzhuang'=>trim($d[34]),
						'fushibaohao'=>trim($d[35]),
						'fushiguige'=>trim($d[36]),
						'zongzhong'=>round(trim($d[37]),3),
						'mairugongfeidanjia'=>round(trim($d[38]),2),
						'mairugongfei'=>round(trim($d[39]),2),
						'jijiagongfei'=>round(trim($d[40]),2),
						'shoucun'=>trim($d[41]),
						'ziyin'=>trim($d[42]),
						'danjianchengben'=>round(trim($d[43]),2),
						'peijianchengben'=>round(trim($d[44]),2),
						'qitachengben'=>round(trim($d[45]),2),
						'chengbenjia'=>round(trim($d[46]),2),
						'jijiachengben'=>round(trim($d[47]),2),
						'jiajialv'=>round(trim($d[48]),2),
						'zuixinlingshoujia'=>round(trim($d[49]),2),
						'xianzaixiaoshou'=>round(trim($d[49]),2),//根据老系统来讲，现在销售价格和最新零售价格是一样的--JUAN
						'pinpai'=>trim($d[50]),//品牌
						'changdu'=>trim($d[51]),
						'zhengshuhao'=>trim($d[52]),
						'yanse'=>trim($d[53]),
						'jingdu'=>trim($d[54]),
						'peijianshuliang'=>trim($d[55]),
						'guojizhengshu'=>trim($d[56]),
						'zhengshuleibie'=>trim($d[57]),
						'goods_name'=>trim($d[58]),
						'kela_order_sn'=>trim($d[59]),
						'shi2'=>trim($d[60]),
						'shi2lishu'=>trim($d[61]),
						'shi2zhong'=>round(trim($d[62]),3),
						'shi2zhongjijia'=>trim($d[63]),
						'shi2mairudanjia'=>round(trim($d[64]),2),
						'shi2mairuchengben'=>round(trim($d[65]),2),
						'shi2jijiadanjia'=>round(trim($d[66]),2),
						'qiegong'=>trim($d[67]),
						'paoguang'=>trim($d[68]),
						'duichen'=>trim($d[69]),
						'yingguang'=>trim($d[70]),
						'buchanhao'=>trim($d[71]),
						//'gene_sn'=>trim($d[72]),
						'xiaoshouchengben'=>$xiaoshouchengben,
						'zuanshizhekou'=>trim($d[73]),
						'zhengshuhao2'=>trim($d[74]),
						'guojibaojia'=>trim($d[75]),
						'gongchangchengben'=>trim($d[76]),
						'tuo_type'=>$tuo_type,
						'gemx_zhengshu'=>trim($d[78]),
						'jietuoxiangkou'=>trim($d[79]),
						'zhushitiaoma'=>trim($d[80]),//主石条码
						'color_grade'=>trim($d[81]),
						'supplier_code' =>trim($d[82]),
						'luozuanzhengshu' =>trim($d[83]),
						'with_fee' =>trim($d[84]),
						'certificate_fee' =>$certificate_fee,
						'operations_fee' =>trim($d[86]),
						'peijianjinchong' =>empty(trim($d[87]))?0:trim($d[87]),
					    'shi2baohao'=>trim($d[88]),
                        'shi3'=>trim($d[89]),
                        'shi3lishu'=>trim($d[90]),
                        'shi3zhong'=>round(trim($d[91]),3),
                        'shi3zhongjijia'=>trim($d[92]),
                        'shi3mairudanjia'=>round(trim($d[93]),2),
                        'shi3mairuchengben'=>round(trim($d[94]),2),
                        'shi3jijiadanjia'=>round(trim($d[95]),2),
                        'shi3baohao'=>trim($d[96]),
					    'shi3baohao'=>trim($d[96]),
					    'guojian_wgt'=>trim($d[97]),//国检总重
					);
					$chengbenjia += round(trim($d[46]),2);
					$cnt++;
				}
			}
		}

        if (!empty($j_error)) {
            echo $j_error;
            exit;
        }

        $str = json_encode($goods);

        header('Content-Type: text/html; charset=utf-8');
        $file = "apps/warehouse/exceltemp/" . $sn . ".html";
        if (is_file($file)) {
            unlink($file);
        }

        $fp = fopen($file, "a+");

        fwrite($fp, "<input type='hidden' name=create_goods_grid  value= '" . $str .
            "'/>\n");
        fwrite($fp, "<input type='hidden' name=create_goods_cnt  value= '" . $cnt . "'/>\n");
        fwrite($fp, "<input type='hidden' name=create_goods_num  value= '" . $chengbenjia .
            "'/>\n");
        fclose($fp);
        echo "ok";
    }

    //这里是导入html文件导入
    public function importJs()
    {

        if (isset($_POST['user_id'])) {
            $user_id = _Post::getInt('user_id');

            $file = 'apps/warehouse/exceltemp/' . $user_id . '.html';

            if (file_exists($file)) {
                $ht = $this->fetch('../../exceltemp/' . $user_id . '.html');
                echo $ht;
            } else {
                echo 0;
            }
        }
        if (isset($_POST['goods_sn'])) {
            $goods_sn = _Post::getString('goods_sn');
            $file = 'apps/warehouse/exceltemp/' . $goods_sn . '.html';

            if (file_exists($file)) {
                $ht = $this->fetch('../../exceltemp/' . $goods_sn . '.html');
                echo $ht;
            } else {
                echo 0;
            }
        }

    }

    //对数组进行xml处理
    private function arrtoxml($arr, $dom = 0, $item = 0)
    {
        if (!$dom) {
            $dom = new DOMDocument("1.0");
        }
        if (!$item) {
            $item = $dom->createElement("root");
            $dom->appendChild($item);
        }
        foreach ($arr as $key => $val) {
            $itemx = $dom->createElement(is_string($key) ? $key : "item");
            $item->appendChild($itemx);
            if (!is_array($val)) {
                $text = $dom->createTextNode($val);
                $itemx->appendChild($text);

            } else {
                $this->arrtoxml($val, $dom, $itemx);
            }
        }
        return $dom->saveXML();
    }


    /**
     * 为excel提供款的信息
     */
    public function getStyle()
    {
        $goods_sn = _Get::getString('goods_sn');
        $model = new WarehouseBillInfoLModel(21);
        $res = $model->getStyle($goods_sn);
        if (!$res) {
            echo 0;
            exit;
        }
        echo $res;
    }

    //通过公司来获取仓库列表
    public function warehouseTree()
    {
        $company_id = _Post::getInt('company_id');
        $model = new WarehouseBillInfoLModel(21);
        $res = $model->warehouseTree($company_id);
        if (!$res) {
            return false;
        }
        echo $this->fetch('warehouse_bill_info_l_add_option.html', array('warehouse' =>
                $res));
    }


    /** 取消单据 **/
    public function closeBillInfoL($params)
    {
        //var_dump($_REQUEST);exit;
        $result = array('success' => 0, 'error' => '');
        $bill_id = $params['id'];
        $bill_no = $params['bill_no'];
        $model = new WarehouseBillModel($bill_id, 21);
        $create_user = $model->getValue('create_user');
        $now_user = $_SESSION['userName'];
        if($create_user !== $now_user){
			$result['error'] = '亲~ 非本人单据，你是不能取消的哦！#^_^#  ';
			Util::jsonExit($result);
		}

        /** 如果单据是审核/取消状态 不允许修改 **/
        $status = $model->getValue('bill_status');
        if ($status == 2) {
            $result['error'] = '单据已审核，不能修改';
            Util::jsonExit($result);
        } else
            if ($status == 3) {
                $result['error'] = '单据已取消，不能修改';
                Util::jsonExit($result);
            }

        $lmodel = new WarehouseBillInfoLModel(22);
        $res = $lmodel->closeBillInfoL($bill_id, $bill_no);
        if ($res) {
            $result['success'] = 1;
            $result['error'] = '单据取消成功!!!';
        } else {
            $result['error'] = '单据取消失败!!!';
        }
        Util::jsonExit($result);
    }


    //打印详情
    public function printBill()
    {

        $dd = new DictModel(1);
        $id = _Request::get('id');
        $model = new WarehouseBillModel($id, 21);
        $data = $model->getDataObject();


        //获取商品信息
        $goods_info = $model->getDetail($id);

        //获取加工商支付信息
        $newmodel = new WarehouseBillInfoLModel(21);
        $BillPay = $newmodel->getBillPay($id);
        $amount = 0;
        foreach ($BillPay as $val) {
            $amount += $val['amount'];
        }

        //统计数据
        $zuanshidaxiao = 0;
        $fushizhong = 0;
        $jinzhong = 0;
        $chengbenjia = 0;
        $xianzaixiaoshou = 0;
        //var_dump($goods_info);exit;
        foreach ($goods_info as $val) {
            $zuanshidaxiao += $val['zuanshidaxiao'];
            $fushizhong += $val['fushizhong'];
            $shi2zhong += $val['shi2zhong'];
            $shi3zhong += $val['shi3zhong'];
            $jinzhong += $val['jinzhong'];
            $chengbenjia += $val['chengbenjia'];
            $chengbenjia_zs += $val['yuanshichengbenjia_zs'];
            $xianzaixiaoshou += $val['xianzaixiaoshou'];
            //echo $val['xianzaixiaoshou'];
        }
        //var_dump($xianzaixiaoshou);exit;

        $this->render('shouhuo_print.html', array(
            'data' => $data,
            'goods_info' => $goods_info,
            'BillPay' => $BillPay,
            'jinzhong' => $jinzhong,
            'zuanshidaxiao' => $zuanshidaxiao,
            'fushizhong' => $fushizhong,
            'shi2zhong' => $shi2zhong,
            'shi3zhong' => $shi3zhong,
            'dd' => $dd,
            'amount' => sprintf("%.2f", $amount),
            'chengbenjia' => sprintf("%.2f", $chengbenjia),
            'chengbenjia_zs' => sprintf("%.2f", $chengbenjia_zs),
            'xianzaixiaoshou' => sprintf("%.2f", $xianzaixiaoshou)));

    }

    //打印详情
    public function printSum()
    {
        //获取单据bill_id
        $id = _Request::get('id');
        $dd = new DictModel(1);
        $model = new WarehouseBillModel($id, 21);
        //获取单据主表基础信息
        $data = $model->getDataObject();
        $newmodel = new WarehouseBillInfoLModel(21);

        //获取加工商支付信息
        /*
        $BillPay = $newmodel->getBillPay($id);
        $amount=0;
        foreach($BillPay as $val){
        $amount +=$val['amount'];
        }*/
        $paymodel = new WarehouseBillPayModel(21);
        $payList = $paymodel->getList(array('bill_id' => $id));
        $bill_status = $model->getValue('bill_status');
        $amount = 0; //支付总计
        foreach ($payList as $val) {
            $amount += $val['amount'];
        }
        if ($bill_status != 2) {
            $chengbenjia_goods = $model->getValue('goods_total');
            $zhifujia_prc = $paymodel->getAmount($id);
            $cha = (($chengbenjia_goods * 100) - ($zhifujia_prc * 100)) / 100;
            if ($cha != 0) {
                //入库尾差需要补
                $arr_cha = array(
                    'id' => '',
                    'bill_id' => $id,
                    'pro_id' => 366,
                    'pro_name' => '入库成本尾差',
                    'pay_content' => 6, // $cha<0?6:7 ,
                    'pay_method' => 1,
                    'tax' => 2,
                    'amount' => $cha);
                $payList[] = $arr_cha;
            }
        }

        //获取单据信息  汇总
        $ZhuchengseInfo = $model->getBillinfo($id);
        //材质信息
        $zhuchengsezhongxiaoji = $zuixinlingshoujiaxiaoji = 0;
        $zhuchengsetongji = array();
        foreach ($ZhuchengseInfo['zhuchengsedata'] as $val) {
            $zhuchengsezhongxiaoji += $val['jinzhong'];
            $zhuchengsetongji[] = $val;
        }
        //主石信息
        $zhushilishuxiaoji = $zhushizhongxiaoji = 0;
        $zhushitongji = array();
        foreach ($ZhuchengseInfo['zhushidata'] as $val) {
            $zhushilishuxiaoji += $val['zhushilishu'];
            $zhushizhongxiaoji += $val['zuanshidaxiao'];
            $zhushitongji[] = $val;
        }
        //副石信息
        $fushilishuxiaoji = $fushizhongxiaoji = 0;
        $fushitongjia = array();
        foreach ($ZhuchengseInfo['fushidata'] as $val) {
            $fushilishuxiaoji += $val['fushilishu'];
            $fushizhongxiaoji += $val['fushizhong'];
            $fushitongji[] = $val;
        }
        foreach ($ZhuchengseInfo['all_price'] as $k => $v) {
            $ZhuchengseInfo['all_price'][$k] = sprintf("%.2f", $v);
        }
        //计价成本总计、总零售价总计
        $this->render('shouhuo_print_ex.html', array(
            'data' => $data,
            'amount' => sprintf("%.2f", $amount),
            'BillPay' => $payList,
            'zhuchengsetongji' => $zhuchengsetongji,
            'zhuchengsezhongxiaoji' => $zhuchengsezhongxiaoji,
            'zhushilishuxiaoji' => $zhushilishuxiaoji,
            'zhushizhongxiaoji' => $zhushizhongxiaoji,
            'zhushitongji' => $zhushitongji,
            'fushilishuxiaoji' => $fushilishuxiaoji,
            'fushizhongxiaoji' => $fushizhongxiaoji,
            'fushitongji' => $fushitongji,
            'all_price' => $ZhuchengseInfo['all_price']));

    }

    //打印核对明细
    public function printHedui()
    {
        //var_dump($_REQUEST);exit;
        $dd = new DictModel(1);
        $id = _Request::get('id');
        $model = new WarehouseBillModel($id, 21);
        $data = $model->getDataObject();

        //获取商品信息
        $goods_info = $model->getDetail($id);
        //获取加工商支付信息
        $newmodel = new WarehouseBillInfoLModel(21);
        $BillPay = $newmodel->getBillPay($id);
        $amount = 0;
        foreach ($BillPay as $val) {
            $amount += $val['amount'];
        }

        //统计数据
        $zuanshidaxiao = 0;
        $fushizhong = 0;
        $jinzhong = 0;

        foreach ($goods_info as $key => $val) {
            //获取图片 拼接进数组
            $gallerymodel = new ApiStyleModel();
            $ary_gallery_data = $gallerymodel->getProductGallery($val['goods_sn'], 1);
            if (count($ary_gallery_data) > 0) {
                $gallery_data = $ary_gallery_data[0];
            }
            if (isset($gallery_data['thumb_img'])) {
                $goods_info[$key]['goods_img'] = $gallery_data['thumb_img'];
            } else {
                $goods_info[$key]['goods_img'] = '';
            }

            $zuanshidaxiao += $val['zuanshidaxiao'];
            $fushizhong += $val['fushizhong'];
            $jinzhong += $val['jinzhong'];
        }
        $this->render('shouhuo_print_detail.html', array(
            'data' => $data,
            'goods_info' => $goods_info,
            'BillPay' => $BillPay,
            'jinzhong' => $jinzhong,
            'zuanshidaxiao' => $zuanshidaxiao,
            'fushizhong' => $fushizhong,
            'dd' => $dd,
            'amount' => $amount));

    }

    /*生成还石单**/
    public function getshibao($params)
    {
        $id = $params['id'];
        $bill_model = new WarehouseBillModel($id, 21);
        $bill_no = $bill_model->getValue('bill_no');
        $bill_status = $bill_model->getValue('bill_status');
        $model_l = new WarehouseBillInfoLModel(21);
        $arr_l = $model_l->getBillgoods($id);
        $prc_id = $arr_l['pro_id'];
        $prc_name = $arr_l['pro_name'];
        $send_goods_sn = $arr_l['send_goods_sn'];
        $row = $bill_model->getDetail($id);
        //只有已审核单据才能做还石单
        if ($bill_status != 2) {
            //$result['error'] ='只有审核状态的才能生成还石单！';
            //Util::jsonExit($result);
        }
        //var_dump($row );exit;
        /* 整理石包 */
        $goods = array();
        $i = 0;
        foreach ($row as $gg) {
            if ($gg['zhushibaohao'] != '' && $gg['zuanshidaxiao'] != '' && $gg['zhushilishu'] !=
                '') {
                $i++;
                $goods[$i]['shibao'] = $gg['zhushibaohao'];
                $goods[$i]['num'] = $gg['zhushilishu'];
                $goods[$i]['zongzhong'] = $gg['zuanshidaxiao'];
            }
            if ($gg['fushibaohao'] != '' && $gg['fushizhong'] != '' && $gg['fushilishu'] !=
                '') {
                $i++;
                $goods[$i]['shibao'] = $gg['fushibaohao'];
                $goods[$i]['num'] = $gg['fushilishu'];
                $goods[$i]['zongzhong'] = $gg['fushizhong'];
            }
            if ($gg['shi2baohao'] != '' && $gg['shi2zhong'] != '' && $gg['shi2lishu'] !=
                '') {
                $i++;
                $goods[$i]['shibao'] = $gg['shi2baohao'];
                $goods[$i]['num'] = $gg['shi2lishu'];
                $goods[$i]['zongzhong'] = $gg['shi2zhong'];
            }

            if ($gg['shi3baohao'] != '' && $gg['shi3zhong'] != '' && $gg['shi3lishu'] !=
                '') {
                $i++;
                $goods[$i]['shibao'] = $gg['shi3baohao'];
                $goods[$i]['num'] = $gg['shi3lishu'];
                $goods[$i]['zongzhong'] = $gg['shi3zhong'];
            }                     
        }
        /* 检测石包 */
        $order_goods = array();
        foreach ($goods as $k => $v) {

            $shibao_info = ApiModel::shibao_api('getShibaoInfo', array('shibao' => $v['shibao']));
            if (isset($shibao_info['error']) && $shibao_info['error'] == 1) {
                $result['error'] = $v['shibao'] . '石包不存在';
                Util::jsonExit($result);
            }

            $info = $shibao_info['return_msg'];
            //var_dump($info);exit;
            $og['order_type'] = 'HS';
            $og['shibao'] = $v['shibao'];
            $og['num'] = $v['num'];
            $og['zongzhong'] = $v['zongzhong'];
            $og['caigouchengben'] = $info['caigouchengben'];
            $og['xiaoshouchengben'] = $info['xiaoshouchengben'];
            $og['zhengshuhao'] = '';
            $og['zhong'] = '';
            $og['yanse'] = '';
            $og['jingdu'] = '';
            $og['qiegong'] = '';
            $og['duichen'] = '';
            $og['paoguang'] = '';
            $og['yingguang'] = '';

            if (substr($og['shibao'], 0, 2) != 'KL') {
                $result['error'] = $og['shibao'] . 'BDD的石包号必须以KL开头';
                Util::jsonExit($result);
            }
            if ($og['num'] <= 0) {
                $result['error'] = $og['shibao'] . '石包数量不能小于0';
                Util::jsonExit($result);
            }
            if ($og['zongzhong'] <= 0) {
                echo $og['zongzhong'];
                $result['error'] = $og['shibao'] . '石包重量不能小于0';
                Util::jsonExit($result);
            }
            if ($og['num'] > $info['SS_cnt'] - $info['TS_cnt']) {
                $result['error'] = $og['shibao'] . '石包数量不足';
                Util::jsonExit($result);
            }
            if ($og['zongzhong'] > $info['SS_zhong'] - $info['TS_zhong']) {
                $result['error'] = $og['shibao'] . '石包重量不足';
                Util::jsonExit($result);
            }

            $shibaos[] = $og['shibao'];
            $order_goods[] = $og;
        }
        //没有数据的不能生成还石单
        if (!count($order_goods)) {
            $result['error'] = '没有找到符合条件的石包';
            Util::jsonExit($result);
        }

        if (count($shibaos) != count($order_goods)) {
            //没用
            $result['error'] = '同一张单据中石包不能重复';
            Util::jsonExit($result);
        }
        /* 还石单信息 */
        $order = array(
            'type' => 'HS',
            'status' => 1,
            'order_time' => date('Y-m-d'),
            'in_warehouse_type' => 0,
            'account_type' => 0,
            'send_goods_sn' => $send_goods_sn,
            'shijia' => 0,
            'make_order' => $_SESSION['userName'],
            'prc_id' => $prc_id,
            'prc_name' => $prc_name,
            'addtime' => date('Y-m-d H:i:s'),
            'info' => $bill_no,
            'times' => time() . rand(10000, 99999),
            );

        //var_dump($order);var_dump($order_goods);exit;
        /* 生成还石单 */
        $shibao_info = ApiModel::shibao_api('addHsOrder', array('info' => $order, 'data' =>
                $order_goods));
        if ($shibao_info['error'] == 1) {
            $result['error'] = $shibao_info['error_msg'];
            Util::jsonExit($result);
        } else {
            $result['error'] = $shibao_info['return_msg'];
            Util::jsonExit($result);
        }
    }

    /*生成还石单**/
    public function saveYSD($params)
    {
        $id = $params['id'];
        $bill_model = new WarehouseBillModel($id, 21);
        $bill_no = $bill_model->getValue('bill_no');
        $bill_status = $bill_model->getValue('bill_status');
        $factory_id=$bill_model->getValue('pro_id');
        $factory_name=$bill_model->getValue('pro_name');
        $model_l = new WarehouseBillInfoLModel(21);
        $arr_l = $model_l->getBillgoods($id);
        $prc_id = $arr_l['pro_id'];
        $prc_name = $arr_l['pro_name'];
        $paper_no= $arr_l['send_goods_sn'];
        $send_goods_sn = $arr_l['send_goods_sn'];
        $row = $bill_model->getDetail($id);
        //只有已审核单据才能做用石单
        if ($bill_status == 3) {
            $result['error'] ='已取消状态的收货单不能生成用石单！';
            Util::jsonExit($result);
        }
        $stone_model=new StoneModel(46);  
        /*
        if($stone_model->getStoneBill($bill_no)){
            $result['error'] ='此收货单已生成用石单';
            Util::jsonExit($result);
        }*/     
      
        $sups=$model_l->getSups($id);
        if(empty($sups)){
            $result['error'] ='没有查到有效状态石包数据不能生成用石单';
            Util::jsonExit($result);            
        }
        /*
        $unvalid_stone=$model_l->checkStone($id);
        if(!empty($unvalid_stone)){
            $result['error'] ='石包不是有效状态'.json_encode($unvalid_stone);
            Util::jsonExit($result);            
        }*/
        
        $pdo= $stone_model->db()->db();
        //开启事物        
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); // 关闭sql语句自动提交
        $pdo->beginTransaction();        
        $stone_bill_no=array(); 
        try{             
        foreach ($sups as $k1 => $sup) {            
            $bill=array('bill_no'=>'0',
               'bill_type'=>'YSD',
               'status'=>1,
               'processors_id'=>$sup['sup_id'],
               'processors_name'=>$sup['sup_name'],
               'factory_id'=>$factory_id,
               'factory_name'=>$factory_name,
               'source'=>'1',
               'source_no'=>$bill_no,
               'paper_no'=>$paper_no,
               'create_user'=>$_SESSION['userName'],
               'remark'=>$bill_no
             );
            $stone_bill_id=$stone_model->saveStoneBill($bill);
            if($stone_bill_id==false){
                $pdo->rollback(); //事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交                
                $result['error'] ='生成用石单失败';
                Util::jsonExit($result); 
            }              
            $shibaos=$model_l->getZhushibaos($id,$sup['sup_id']);
            $num=0;
            $weight=0;
            $price_total=0;
            $bill_detail=array();
            foreach($shibaos as $k2 => $shibao){
                $stone_bill_detail=array(
                    'bill_id'=>$stone_bill_id,
                    'dia_package'=>$shibao['dia_package'],
                    'purchase_price'=>$shibao['purchase_price'],
                    'specification'=>$shibao['color'],
                    'color'=>$shibao['color'],
                    'neatness'=>$shibao['neatness'],
                    'cut'=>$shibao['cut'],
                    'symmetry'=>$shibao['symmetry'],
                    'polishing'=>$shibao['polishing'],
                    'fluorescence'=>$shibao['fluorescence'],
                    'num'=>$shibao['num'],
                    'weight'=>$shibao['weight'],
                    'price'=>bcmul($shibao['purchase_price'],$shibao['weight'],3)
                );
                $num+=$shibao['num'];
                $weight=bcadd($weight,$shibao['weight'],3);
                $price_total=bcadd($price_total,bcmul($shibao['purchase_price'],$shibao['weight'],3),3);
                $res=$stone_model->saveStoneBill_detail($stone_bill_detail);
                if($res==false){
                    $pdo->rollback(); //事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交                
                    $result['error'] ='生成用石单失败';
                    Util::jsonExit($result); 
                } 
            }            
            $price_total=round($price_total,2);             
            $tmpbill_no='YSD'.date('Ymd').substr(str_pad($stone_bill_id,8,'0',STR_PAD_LEFT),4);
            $bill=array(
                'id'=>$stone_bill_id, 
                'bill_no'=>$tmpbill_no,
                'num'=>$num,
                'weight'=>$weight,
                'price_total'=>$price_total
            );
            $res=$stone_model->updateStoneBill($bill);
            if($res==false){
                $pdo->rollback(); //事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交                 
                $result['error'] ='生成用石单失败';
                Util::jsonExit($result);                 
            }            
            $stone_bill_no[]=$tmpbill_no; 
        }}catch (Exception $e){
            $pdo->rollback(); //事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交                 
            $result['error'] ='生成用石单失败'.json_encode($e);
            Util::jsonExit($result); 
        }    

        $pdo->commit(); //事务提交
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
        $result['error'] ='生成用石单:'.implode(',',$stone_bill_no);
        Util::jsonExit($result);         
     
    }

}

?>