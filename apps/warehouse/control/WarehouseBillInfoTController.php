<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoTController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-20 12:31:29
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoTController extends CommonController
{
	protected $smartyDebugEnabled = true;
	protected $whitelist = array('printBill','printSum','selectsalepolicy','printHedui','getPolicytypeInfo','printcode');

	/**
	 *	index，搜索框
	 */
	public function index ($params){}

	//获取仓库列表
	public function getPremissHouse(){
		//库房列表
		$model_w = new WarehouseModel(21);
		$warehouse = $model_w->select(array('is_delete'=>1),array("id","name",'code'));
		$warehouseids= Util::eexplode(',', $_SESSION['userWareList']);
		$quanxianwarehouseid = $this->WarehouseListO();

		if($quanxianwarehouseid!==true){
			foreach($warehouse as $key=>$val){
				if(!in_array($val['id'],$quanxianwarehouseid)){
					unset($warehouse[$key]);
				}
			}
		}
		return $warehouse;
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
	    //如果不是删除后进来的，就清除结算商session   	
    	if(!isset($_GET['_'])){
    		 setcookie('bill_pay','',time()-10);
    	}
		$user_id = $_SESSION['userId'];
		$model     = new CompanyModel(1);
		$company   = $model->getCompanyTree();//公司列表

		$bill_view = new WarehouseBillView(new WarehouseBillModel(21));
		//$houses = $this->getPremissHouse();
		$houses = $this->getOnlyMasterCompanyWarehouse();
	   if(isset($_COOKIE['bill_pay'])){
        	$olddo_str=$_COOKIE['bill_pay'];
        	$payList=unserialize($olddo_str);   	
        }else{       	
        	$payList=array();
        }
		$this->render('warehouse_bill_t_add.html',array(
			'view'=>$bill_view, 'company_list'=>$company,
			'houses'=>$houses,'user_id'=>$user_id,
			'payList'  => $payList,
		));
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
    
        $this->render('warehouse_bill_info_t_selectsalepolicy.html', array(
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
        if(!empty($policy_goods) && $policy_goods!='' ){
            $result['error'] = "$policy_goods 商品未维护相应的销售政策，请与产品中心维护人员联系添加。";
            Util::jsonExit($result);
        }
        
        $result['success']=1;
        $result['msg']=$jiajialv;
        $result['msg1']=$stavalue;
        Util::jsonExit($result);
    }
	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		//主信息
		$model = new WarehouseBillModel($id,21);
		$bill_view = new WarehouseBillView($model);
		//结算商总金额
		$payModel = new WarehouseBillPayModel(21);
		$pay_price = $payModel->getAmount($id);
		//公司列表
		$model_c     = new CompanyModel(1);
		$company   = $model_c->getCompanyTree();
		//库房列表
		$warehouse = $this->getPremissHouse();
		//供应商
		$houses = $this->getPremissHouse();

		$pro_list = $bill_view->getSupliers();
		$this->render('warehouse_bill_t_edit.html',array(
			'view'=>$bill_view,
			'company' => $company,
			'warehouse' => $warehouse,
			'pro_list' => $pro_list,
			'pay_price' => $pay_price,
			'id'=>$id,
			'houses'=>$houses,
		));
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);	//单据ID

		//主信息
		$model = new WarehouseBillModel($id,21);
		$bill_view = new WarehouseBillView($model);
		//结算商总金额
		$payModel = new WarehouseBillPayModel(21);
		$pay_price = $payModel->getAmount($id);
		//获取取单据取消时的操作用户和操作时间
		$WarehouseBillModel = new WarehouseBillModel(21);
		$billcloseArr=$WarehouseBillModel->get_bill_close_status($id);
		$this->render('warehouse_bill_t_show.html',array(
			'view' =>$bill_view, 'bar'=>Auth::getViewBar(),
			'pay_price' => $pay_price,
			'billcloseArr'=>$billcloseArr,
			'isViewChengbenjia'=>$this->isViewChengbenjia(),
		));
	}


	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		if($params['to_warehouse_id'] == '')
		{
			$result['error'] ="收货仓库必选！";
			Util::jsonExit($result);
		}
		$ar2 = explode("|",$params['to_warehouse_id']);
		$to_warehouse_id	= $ar2[0];
		$to_warehouse_name	= $ar2[1];
        /*
		$res = $this->checkHosePremission($to_warehouse_id);
		if($res !== true){
			$result = ['success'=>0,'error'=>$res];
			Util::jsonExit($result);
		}*/
		$checksession = $this->checkSession($to_warehouse_id);
		if(is_string($checksession)){
			$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$to_warehouse_name."</b>".$checksession."</span>的权限请联系管理员开通");
			Util::jsonExit($result);
		}		

		if($params['pro_id'] == '')
		{
			$result['error'] ="加工商必选！";
			Util::jsonExit($result);
		}
		$ar = explode("|",$params['pro_id']);
		$pro_id		= $ar[0];
		$pro_name	= $ar[1];
		$cinfo = $this->getCompanyInfo($to_warehouse_id);
		$to_company_id		= $cinfo['company_id'];
		$to_company_name	= $cinfo['company_name'];
		$ship_num	= _Post::get('ship_num');
		$order_sn	= _Post::get('order_sn');
		$put_in_type = _Post::getInt('put_in_type');
		$put_in_class = _Post::getInt('put_in_class');
		$jiejia		= _Post::get('jiejia');
		$bill_note	= _Post::get('bill_note');

		if($ship_num=='')
		{
			$result['error'] ="送货单号不能为空！";
			Util::jsonExit($result);
		}

		if($put_in_type=='')
		{
			$result['error'] ="入库方式必选！";
			Util::jsonExit($result);
		}
		if($jiejia=='')
		{
			$result['error'] ="是否结价必选！";
			Util::jsonExit($result);
		}
		if(!isset($_POST['create_goods_grid'])){
			$result['error'] ="请先导入文件！";
			Util::jsonExit($result);
		}
		//整理上传数据
		$time = date('Y-m-d H:i:s');
		$grid = json_decode($_POST['create_goods_grid'],true);

		$in_goods_sn = array();

		if(!count($grid))
		{
			$result['error'] = "上传内容不能为空";
			Util::jsonExit($result);
		}

		$model  = new WarehouseBillInfoTModel(22);
		//整理货品信息
		$array = $model->arrangementData($grid, $pro_id);
		$dataArr = $array['dataArr'];
		
		
		//判断款号
		$isstyle=true;
		foreach($dataArr as $v){
			$goods_sn=$v['goods_sn'];
			if($goods_sn==''){
				$result['error'] = "导入的款号不能为空！";
				Util::jsonExit($result);
			}
			if($v['cat_type'] == '裸石' || $v['cat_type'] == '彩钻'){
				continue;
			}
			if(preg_match("/^((A|B|M|W|a|b|m|w)([\S]{4}))$/",$goods_sn)||preg_match("/^((W|w)([\S]{7}))$/",$goods_sn)||preg_match("/^((KL|kl)[\S]{8})$/",$goods_sn)){
				$BaseStyleModel = new BaseStyleInfoModel(11);
				$res1=$BaseStyleModel->isBaseStyle($goods_sn);
				if(empty($res1)){
					$isstyle=false;
					$error.=$goods_sn.' ';
				}
			}
			 
			 
		}
		if(!$isstyle){
			$result['error'] = "款号{$error}不存在或未审核！";
			Util::jsonExit($result);
		}
		
		$chengbenzongjia = $array['chengbenzongjia'];

		//单据信息
		$billArr = array(
			'bill_no'=>'',
			'bill_type'=>'T',
			'goods_num'=>count($dataArr),
			'to_company_id'=>$to_company_id,
			'to_company_name'=>$to_company_name,
			'to_warehouse_id'=>$to_warehouse_id,
			'to_warehouse_name'=>$to_warehouse_name,
			'bill_note'=>$bill_note,
			'goods_total'=>$chengbenzongjia,
			'yuanshichengben' => $chengbenzongjia,
			'shijia'=>0,
			'put_in_type' => $put_in_type,
			'tuihuoyuanyin' => $put_in_class,
			'order_sn'	=> $order_sn,
			'send_goods_sn' => $ship_num,
			'pro_id' => $pro_id,
			'pro_name' => $pro_name,
			'jiejia' => $jiejia,
			'create_time'=> date("Y-m-d H:i:s"),
			'create_user' => $_SESSION['userName'],
			'create_ip'	=>Util::getClicentIp(),
			'order_sn' => !empty($params['order_sn']) ? trim($params['order_sn']) : '',
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
		/** 检查尾差不能超过±100 zzm 2015-11-26**/ 
        $cha = $chengbenzongjia - $billSum;
      	if (abs($cha)>100) {
            $result['error'] = "入库成本尾差不充许超过±100元，请检查是否制单错误。";
            Util::jsonExit($result);
          }
          /**end**/
		if (!$isbillPay) {
			$result['error'] = "<span style='color: #ff0000'>警告：供应商不存在结算列表中！</span>";
			Util::jsonExit($result);
		}
		
		
		$billgoodsmodel = new WarehouseBillGoodsModel(21);
		$model1 = new WarehouseBillInfoLModel(22);
		$companyArr=Array('拆货管理员','内部转仓','石料管理','货品组装','盘盈入库','入库成本尾差','BDD金条回购');
		if(!in_array($pro_name, $companyArr) && $pro_id != 473){
			$goodssnArr=$dataArr;
			$goodssnArr=array_unique($goodssnArr);
		
			// print_r($goodssnArr);exit;
			$isinfac=0;
			$error="<span style='color: #ff0000'>警告：供应商和款号 ";
			foreach ($goodssnArr as $val){
				$isstyle=$model1->getsStyle($val['goods_sn']);
				if(!$isstyle){
					continue;
				}
				$factoryIdArr=$model1-> getFactoryIdAll($pro_id,$val['goods_sn']);
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
		
		

		$res = $model->saveBillAllInfo($dataArr,$billArr);
		if( is_array($res) && $res['id'] > 0 )
		{
            $model->updateStylePrice($res['id']);
			$result['success'] = 1;
			$result['infomsg']=" 您的收货单编号为<br/><span style='color: #ff0000;'>" . $res['bill_no'] . "</span>";
			$result['label']=$res['bill_no'];
			$result['x_id'] =$res['id'];
			$result['tab_id'] = mt_rand();
		}else{
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

		$ar2 = explode("|",$params['to_warehouse_id']);
		$to_warehouse_id	= $ar2[0];
		$to_warehouse_name	= $ar2[1];
        /*
		$res = $this->checkHosePremission($to_warehouse_id);
		if($res !== true){
			$result = ['success'=>0,'error'=>$res];
			Util::jsonExit($result);
		}*/
		$checksession = $this->checkSession($to_warehouse_id);
		if(is_string($checksession)){
			$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$to_warehouse_name."</b>".$checksession."</span>的权限请联系管理员开通");
			Util::jsonExit($result);
		}
		
		$cinfo = $this->getCompanyInfo($to_warehouse_id);
		if(!$cinfo['company_id']){
			$result['error'] ="没查到对应的公司！";
			Util::jsonExit($result);
		}
		$to_company_id		= $cinfo['company_id'];
		$to_company_name	= $cinfo['company_name'];


		//$to_warehouse_name = _Post::get('to_warehouse_name');
		//$to_company_name = _Post::get('to_company_name');
		//$to_warehouse_id = _Post::get('to_warehouse_id');
		//$to_company_id = _Post::get('to_company_id');
		$prc = _Post::get('prc_id');
        $ar = explode("|",$prc);
        $prc_id		= $ar[0];
        $prc_name	= $ar[1];     
		$ship_num	= _Post::get('ship_num');
		$put_in_type = _Post::getInt('put_in_type');
		$put_in_class = _Post::getInt('put_in_class');
		$jiejia		= _Post::get('jiejia');
		$bill_note	= _Post::get('bill_note');
		$bill_no    =_Post::get('bill_no');
		$chengbenzongjia = _Post::get('chengbenjia');
		$goods_num = _Post::getInt('goods_num');
		$order_sn    =_Post::get('order_sn');
		$amountTotal = _Post::get('amountTotal');

		if($ship_num=='')
		{
			$result['error'] ="送货单号不能为空！";
			Util::jsonExit($result);
		}
		if($put_in_type=='')
		{
			$result['error'] ="入库方式必选！";
			Util::jsonExit($result);
		}
		if($jiejia=='')
		{
			$result['error'] ="是否结价必选！";
			Util::jsonExit($result);
		}
		if(abs($amountTotal) > 100) {
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
		
			$model1 = new WarehouseBillInfoLModel(22);
			$billgoodsmodel = new WarehouseBillGoodsModel(21);
			$companyArr=Array('拆货管理员','内部转仓','石料管理','货品组装','盘盈入库','入库成本尾差','BDD金条回购');
			if(!in_array($prc_name, $companyArr) && $pro_id != 473){
			$goodssnArr=$billgoodsmodel->select2("goods_sn", "bill_no='$bill_no'",3);
			$goodssnArr=array_unique($goodssnArr);
			$isinfac=0;
			$error="<span style='color: #ff0000'>警告：供应商和款号 ";
			foreach ($goodssnArr as $val){
				$isstyle=$model1->getsStyle($val['goods_sn']);
				if(!$isstyle){
					continue;
				}
			   $factoryIdArr=$model1-> getFactoryIdAll($prc_id,$val['goods_sn']);
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

		$model  = new WarehouseBillInfoTModel(22);
		$dataArr = array();
		if(isset($_POST['create_goods_grid'])){

			//整理上传数据
			$time = date('Y-m-d H:i:s');

			$grid = json_decode($_POST['create_goods_grid'],true);

			//整理货品信息
			$array = $model->arrangementData($grid);
			$dataArr = $array['dataArr'];
			$goods_num = count($dataArr);
			$chengbenzongjia = $array['chengbenzongjia'];

			if(!count($dataArr))
			{
				$result['error'] = "上传内容不能为空";
				Util::jsonExit($result);
			}
		}

		$billArr = array(
			'id'=>$id,
			'bill_no'=>$bill_no,
			'bill_type'=>'T',
			'goods_num'=>$goods_num,
			'to_company_name'=>$to_company_name,
			'to_warehouse_name'=>$to_warehouse_name,
			'to_company_id'=>$to_company_id,
			'to_warehouse_id'=>$to_warehouse_id,
			'pro_id'=>$prc_id,
			'pro_name'=>$prc_name,
			'bill_note'=>$bill_note,
			'yuanshichengben'=>$chengbenzongjia,
			'goods_total' => $chengbenzongjia,
			'shijia'=>0,
			'put_in_type' => $put_in_type,
			'tuihuoyuanyin' => $put_in_class,
			'send_goods_sn' => $ship_num,
			'jiejia' => $jiejia,
			'order_sn' => $order_sn,
		);
		///////////修改明细信息==============end=====================//////////
		if($model->up_info($dataArr,$billArr))
		{
			$result['success'] = 1;
		}else{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new WarehouseBillInfoTModel($id,2);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}


	/**
	 * mkJson 生成Json表单
	 */
	public function mkJson(){
		$id = _Post::getInt('id');
		$arr = Util::iniToArray(APP_ROOT.'warehouse/data/from_table_bill_l.tab');
		$detailModel = new WarehouseBillInfoLModel(21);
		$detail_arr = $detailModel->get_data(array('bill_id' => $id));
		$detail = array();
		foreach($detail_arr as $key => $val)
		{
			$detail[$key][]	= $val['goods_id'];
			$detail[$key][]	= $val['goods_sn'];
			$detail[$key][]	= $val['mo_sn'];
			$detail[$key][] = $val['product_type'];
			$detail[$key][] = $val['cat_type'];
			$detail[$key][] = $val['caizhi'];	//主成色（材质）
			$detail[$key][] = $val['jinzhong']; 	//主成色重
			$detail[$key][]	= $val['jinhao'];
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
			$detail[$key][] = $val['gemx_zhengshu'];//GEMX证书
			$detail[$key][] = $val['zhengshuhao'];	
			$detail[$key][] = $val['yanse'];
			$detail[$key][] = $val['jingdu'];
			$detail[$key][] = $val['peijianshuliang'];
			$detail[$key][] = $val['guojizhengshu'];
			$detail[$key][] = $val['zhengshuleibie'];
			$detail[$key][] = $val['goods_name'];
			
			$detail[$key][] = $val['kela_order_sn'];	//订单号			
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
           
		}
		
		$arr['data'] = $detail;
		$json = json_encode($arr);

		echo $json;
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
	    foreach ($bc_sn_list as $vo){
	        if(empty($vo['order_goods_id'])){
				 // 移除开头的字母
                $bc_id = preg_replace('/^[a-zA-Z]+/', '', strtoupper($vo['buchan_sn']));
	            $bc_ids[]= $bc_id;
	            $l_goods[$bc_id]['goods_id']   = $vo['goods_id'];
	            $l_goods[$bc_id]['cat_type']   = $vo['cat_type'];
	            $l_goods[$bc_id]['zhengshuhao'] = $vo['zhengshuhao'];
	            $l_goods[$bc_id]['goods_sn'] = $vo['goods_sn'];
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
	    //Util::rollbackExit("boss-398代码执行完毕，系统运行正常",$pdolist);
	    return $pdolist;
	}
	//单据审核
	public function checkBill($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = $params['id'];
		$billModel = new WarehouseBillModel($id,21);
		$warehousid = $billModel->getValue('to_warehouse_id');
		$warehousname = $billModel->getValue('to_warehouse_name');
		$bill_no      = $billModel->getValue('bill_no');
		//根据仓库id获取仓库是否锁定(盘点)
		$billModel ->check_warehouse_lock($warehousid);

		$checksession = $this->checkSession($warehousid);
		if(is_string($checksession)){
			$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$warehousname."</b>".$checksession."</span>的权限请联系管理员开通");
			Util::jsonExit($result);
		}

		$status = $billModel->getValue('bill_status');
		$cUser = $billModel->getValue('create_user');
		if($status != 1)
		{
			$result['error'] = "状态不正确不允许审核，已保存状态下才能审核。";
			Util::jsonExit($result);
		}
		if($cUser == $_SESSION['userName'])
		{
			$result['error'] = "不能审核自己制的单";
			Util::jsonExit($result);
		}

		//审核单据金额和结算金额必须一致
		$pro_now = $billModel->getValue('pro_id');

		$payModel = new WarehouseBillPayModel(21);
		$proarr = $payModel->getProArr($id)?$payModel->getProArr($id):array();
		if(!in_array($pro_now,$proarr)){
			$result['error'] = "<span style='color: #ff0000'>警告：供应商不存在结算列表中！</span>";
			Util::jsonExit($result);
		}

		$chengbenjia_goods = $billModel->getValue('goods_total');
		$billModel->cha_deal($id,$chengbenjia_goods);

		if($billModel->getValue('goods_total') != $payModel->getAmount($id))
		{
			#需要限制 自动尾差补充金额大小
			$re_p = $model_pay->saveData($new_array,array());
			if(!$re_p)
			{
				$result['error'] = "结算商尾差计算失败";
				Util::jsonExit($result);
			}
		}

		if($billModel->getValue('goods_total') != $payModel->getAmount($id))
		{
			$result['error'] = "单据总金额和结算总金额不符，不能审核。";
			Util::jsonExit($result);
		}
		
		//审核单据（事务处理）
		$model = new WarehouseBillInfoTModel(22);
		//添加收货时有布产号，并且布产单和订单有绑定关系，则货品绑定
		$pdolist = $this->_autoBindOrder($id,$bill_no); //返回自动绑定订单事物列表
		
		$checkResult = $model->checkBillT($id,$pdolist[22]);//$pdolist[22]是warehouse_shipping库的PDO对象
		if ($checkResult) {
		    //提交自动绑定订单的事物
		    try{
		        foreach($pdolist as $pdo){
		            $pdo->commit(); // 如果没有异常，就提交事务
		            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交
		        }
		        $result['success'] = 1;
		        //AsyncDelegate::dispatch('warehouse', array('event' => 'bill_T_checked', 'bill_id' => $id));
		    }catch (Exception $e){
		        $result['error'] = "审核失败,自动绑定订单事物提交失败";
		    }		
		}else{
			$result['error'] = "审核失败";
		}
		Util::jsonExit($result);
	}


	/**
	* 根据数字字典 属性，重组数字字段数组
	* @param $ditc_atr String 数字字典属性
	* @return $arr Array 返回重组好的二维数组
	*/
	public function getDictNewArr($ditc_atr, $atr = false){
		$array = array();
		$dd = new DictModel(1);
		$dict_ini = $dd->getEnumArray($ditc_atr);
		if(!empty($dict_ini))
		{
			foreach($dict_ini AS $val)
			{
				if(!$atr){
					$array[$val['label']]  = $val['label'];
				}else{
					$array[$val['name']]  = $val['label'];
				}
			}
			return $array;
		}
		else{
			return $array;
		}
	}


	/** 取消单据 **/
	public function closeBillInfoT($params){
		//var_dump($_REQUEST);exit;
		$result = array('success' => 0,'error' =>'');
		$bill_id = $params['id'];
		$bill_no = $params['bill_no'];
		$model = new WarehouseBillModel($bill_id,21);
		$create_user = $model->getValue('create_user');
		$now_user = $_SESSION['userName'];
	   if($create_user !== $now_user){
			$result['error'] = '亲~ 非本人单据，你是不能取消的哦！#^_^#  ';
			Util::jsonExit($result);
		}

		/** 如果单据是审核/取消状态 不允许修改 **/
		$status = $model->getValue('bill_status');
		if($status == 2){
			$result['error'] = '单据已审核，不能修改';
			Util::jsonExit($result);
		}else if($status == 3){
			$result['error'] = '单据已取消，不能修改';
			Util::jsonExit($result);
		}

		$model = new WarehouseBillInfoTModel(22);
		$res = $model->closeBillInfoT($bill_id,$bill_no);
		if($res){
			$result['success'] = 1;
			$result['error'] = '单据取消成功!!!';
		}else{
			$result['error'] = '单据取消失败!!!';
		}
		Util::jsonExit($result);
	}


	//打印详情
	public function printBill() {
		//获取bill_id单据id
		$id = _Request::get('id');
		//数字词典
		$dd =new DictModel(1);
		$model = new WarehouseBillModel($id,21);
		//打印表头信息
		$data  = $model->getDataObject();

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
		$chengbenjia = 0;
		$xianzaixiaoshou=0;
		//统计 副石重 金重
		foreach($goods_info as $val){
			$fushizhong +=$val['fushizhong'];
			$jinzhong +=$val['jinzhong'];
			$zuanshidaxiao +=$val['zuanshidaxiao'];
			$chengbenjia +=$val['chengbenjia'];
			$xianzaixiaoshou +=$val['xianzaixiaoshou'];
		}
		$this->render('qita_shouhuo_print.html', array(
				'dd' => $dd,
				'data' => $data,
				'goods_info' => $goods_info,
				'fushizhong' => $fushizhong,
				'jinzhong' => $jinzhong,
				'zuanshidaxiao' => $zuanshidaxiao,
				'BillPay' => $BillPay,
				'amount' => $amount,
				'chengbenjia'=>sprintf("%.2f",$chengbenjia),
				'xianzaixiaoshou'=> sprintf("%.2f",$xianzaixiaoshou)
		));

	}

	//打印汇总
	public function printSum() {
		//获取bill_id单据id
		$id = _Request::get('id');
		//数字词典
		$dd =new DictModel(1);
		$model = new WarehouseBillModel($id,21);
		//打印表头信息
		$data  = $model->getDataObject();

		//货品详情
		$goods_info = $model->getDetail($id);
		//获取加工商支付信息
		/* $amount=0;
		$BillPay = $model->getBillPay($id);
		foreach($BillPay as $val){
			$amount +=$val['amount'];
		} */
		$paymodel = new WarehouseBillPayModel(21);
		$payList = $paymodel->getList(array('bill_id'=>$id));
		$bill_status  = $model->getValue('bill_status');
		$amount=0;//支付总计
		foreach($payList as $val){
			$amount +=$val['amount'];
		}
		if ($bill_status !=2 )
		{
			$chengbenjia_goods = $model->getValue('goods_total');
			$zhifujia_prc	   = $paymodel->getAmount($id);
			$cha = (($chengbenjia_goods*100) - ($zhifujia_prc*100))/100;
			if ($cha !=0)
			{
				//入库尾差需要补
				$arr_cha = array(
						'id' => '',
						'bill_id' => $id,
						'pro_id' => 366,
						'pro_name' => '入库成本尾差',
						'pay_content' =>6,// $cha<0?6:7 ,
						'pay_method' => 1,
						'tax' => 2,
						'amount' => $cha
				);
				$payList[] =  $arr_cha;
			}
		}
		//计算销售价总计 成本价总计 商品数量
		$fushizhong=0;
		$jinzhong=0;
		$zuanshidaxiao=0;
		//统计 副石重 金重
		foreach($goods_info as $val){
			$fushizhong +=$val['fushizhong'];
			$jinzhong +=$val['jinzhong'];
			$zuanshidaxiao +=$val['zuanshidaxiao'];
		}

		//获取单据信息  汇总
		$ZhuchengseInfo = $model->getBillinfo($id);
		//材质信息
		$zhuchengsezhongxiaoji = $zuixinlingshoujiaxiaoji = 0;
		//$amount='';
		foreach($ZhuchengseInfo['zhuchengsedata'] as $val){
			$zhuchengsezhongxiaoji += $val['jinzhong'];
			$zhuchengsetongji[] = $val;
		}
		//主石信息
		$zhushilishuxiaoji = $zhushizhongxiaoji = 0;
		foreach ($ZhuchengseInfo['zhushidata'] as $val) {
			$zhushilishuxiaoji += $val['zhushilishu'];
			$zhushizhongxiaoji += $val['zuanshidaxiao'];
			$zhushitongji[] = $val;
		}
		//副石信息
		$fushilishuxiaoji = $fushizhongxiaoji = 0;
		foreach ($ZhuchengseInfo['fushidata'] as $val) {
			$fushilishuxiaoji += $val['fushilishu'];
			$fushizhongxiaoji += $val['fushizhong'];
			$fushitongji[] = $val;
		}
		foreach ($ZhuchengseInfo['all_price']  as $k=>$v)
		{
			$ZhuchengseInfo['all_price'][$k] =  sprintf("%.2f",$v);
		}
		$this->render('qita_shouhuo_print_ex.html', array(
				'dd' => $dd,
				'data' => $data,
				'goods_info' => $goods_info,
				'fushizhong' => $fushizhong,
				'jinzhong' => $jinzhong,
				'zuanshidaxiao' => $zuanshidaxiao,
				'BillPay' => $payList,
				'amount' => $amount,
				'zhuchengsetongji' => $zhuchengsetongji,
				'zhuchengsezhongxiaoji' => $zhuchengsezhongxiaoji,
				'zhushilishuxiaoji' => $zhushilishuxiaoji,
				'zhushizhongxiaoji' => $zhushizhongxiaoji,
				'zhushitongji' => $zhushitongji,
				'fushilishuxiaoji' => $fushilishuxiaoji,
				'fushizhongxiaoji' => $fushizhongxiaoji,
				'fushitongji' => $fushitongji,
				'all_price'=>$ZhuchengseInfo['all_price']
		));

	}
	//打印详情
	public function printHedui() {
		//获取bill_id单据id
		$id = _Request::get('id');
		//数字词典
		$dd =new DictModel(1);
		$model = new WarehouseBillModel($id,21);
		//打印表头信息
		$data  = $model->getDataObject();

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
			//获取图片 拼接进数组
			$gallerymodel = new ApiStyleModel();
			$ary_gallery_data = $gallerymodel->getProductGallery($val['goods_sn'], 1);
			
			if (count($ary_gallery_data) > 0) {
				$gallery_data = $ary_gallery_data[0];
			}
			if(isset($gallery_data['thumb_img'])){
				$goods_info[$key]['goods_img']=$gallery_data['thumb_img'];
			}else{
				$goods_info[$key]['goods_img']='';
			}
			
			$fushizhong +=$val['fushizhong'];
			$jinzhong +=$val['jinzhong'];
			$zuanshidaxiao +=$val['zuanshidaxiao'];
		}

		$this->render('qita_shouhuo_print_detail.html', array(
				'dd' => $dd,
				'data' => $data,
				'goods_info' => $goods_info,
				'fushizhong' => $fushizhong,
				'jinzhong' => $jinzhong,
				'zuanshidaxiao' => $zuanshidaxiao,
				'BillPay' => $BillPay,
				'amount' => $amount
		));

	}



}?>