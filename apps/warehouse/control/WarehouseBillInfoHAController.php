<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoHController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-21 21:31:17
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoHAController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('printBill','printSum');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//库房列表
		$model_w = new WarehouseModel(21);
		$warehouse = $model_w->select(array('is_delete'=>1),array("id","name",'code'));
		$warehouseids=Util::eexplode(',', $_SESSION['userWareList']);
		
		$quanxianwarehouseid = $this->WarehouseListO();

		if($quanxianwarehouseid!==true){
			foreach($warehouse as $key=>$val){
				if(!in_array($val['id'],$quanxianwarehouseid)){
					unset($warehouse[$key]);
				}
			}
		}


		$JxcWholesaleModel =  new JxcWholesaleModel(22);
		$JxcWholesale = $JxcWholesaleModel->select2(' * ' , 'wholesale_status=1' , $type = 'all');
		$this->render('warehouse_bill_info_ha.html',array(
				//'bar'=>Auth::getBar(),
				'warehouse' => $warehouse,
				'JxcWholesale' => $JxcWholesale,
				'view'=>new WarehouseBillView(new WarehouseBillModel(21))

		));
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
			//'参数' = _Request::get("参数");


		);




		$page = _Request::getInt("page",1);
		$where = array();

		$model = new WarehouseBillInfoHModel(21);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_bill_info_ha_search_page';
		$this->render('warehouse_bill_info_ha_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('warehouse_bill_info_ha_info.html',array(
			'view'=>new WarehouseBillInfoHView(new WarehouseBillInfoHModel(21))
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		//库房列表
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$model = new WarehouseBillInfoHModel(21);

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

		$JxcWholesaleModel =  new JxcWholesaleModel(22);
		$JxcWholesale = $JxcWholesaleModel->select2(' * ' , 'wholesale_status=1' , $type = 'all');

		$WarehouseBillModel = new WarehouseBillModel($id,21);
		$ret = $WarehouseBillModel->getDataObject();
		$to_customer_id =$ret['to_customer_id'];
		//查询加工商 出库类型
		$this->render('warehouse_bill_info_ha_edit.html',array(
			'view'=>new WarehouseBillView(new WarehouseBillModel($id,21)),
			'dd'=>new DictView(new DictModel(1)),
			'JxcWholesale' => $JxcWholesale,
			'to_customer_id' => $to_customer_id,
			'warehouse' => $warehouse
			));
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);	//单据ID
		$model = new WarehouseBillModel($id,21);
		$status = $model->getValue('bill_status');

		#获取单据附加表ID warehouse_bill_info_m
		//$model = new WarehouseBillInfoMModel(21);
		//$row = $model->getMinfo($id);
		//获取取单据取消时的操作用户和操作时间
		$WarehouseBillModel = new WarehouseBillModel(21);
		$billcloseArr=$WarehouseBillModel->get_bill_close_status($id);
		
		$this->render('warehouse_bill_info_ha_show.html',array(
			'view' => new WarehouseBillView(new WarehouseBillModel($id, 21)),
			'bar'=>Auth::getViewBar(),
			'status'=>$status,
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
		$data = ( isset($_POST['data']) && !empty($_POST['data']) ) ? $_POST['data'] : '';
		if(empty($data)){
			Util::jsonExit($result['error'] = "请添加单据货品明细");
		}
		$model  = new WarehouseBillInfoHModel(22);
		//制单时间
		$time   = date("Y-m-d H:i:s");
		//退货备注
		$bill_note =_Request::get('bill_note');
		//未定 存哪
		$to_customer_id=_Request::get('to_customer_id');

		$bill_no = '';
		//实价总金额（对应 成本价）
		//批发总金额（对应 名义价）
		//差价总金额（对应 销售价）
		//退货仓id 和 名称
		if (empty($params['to_warehouse_id']))
		{
			$result['error'] = '请选择仓库';
			Util::jsonExit($result);
		}
		$ar2 = explode("|",$params['to_warehouse_id']);
		$to_warehouse_id	= $ar2[0];
		$to_warehouse_name	= $ar2[1];

		$rel_model       = new WarehouseRelModel(21);
		$company_id      = $rel_model->GetCompanyByWarehouseId($to_warehouse_id);
		$company_model   = new CompanyModel(1);
		$company_name    = $company_model->getCompanyName($company_id );

		if(!$to_customer_id){
			$result['error'] = '请选择退货客户！';
			Util::jsonExit($result);
		}
		if(empty($to_warehouse_id)){
			$result['error'] = '请选择入库仓！';
			Util::jsonExit($result);
		}
		if(empty($bill_note)){
		    $result['error'] = '退货备注必填，请填写备注！';
		    Util::jsonExit($result);
		}
		//echo 222;exit;
		$c_info = array(
			'bill_no'=>$bill_no,
			'bill_note'=>$bill_note,
			'create_user'=>$_SESSION['userName'],
			'create_time'=>$time,
			'bill_type'=>'H',
			'to_warehouse_id'=>$to_warehouse_id,
			'to_warehouse_name'=>$to_warehouse_name,
			'to_customer_id'=>$to_customer_id,
			'to_company_id'=>$company_id,
			'to_company_name'=>$company_name
			);
		//var_dump($c_info);exit;
		//验证。。。。。每个货品都有效
		//统计  货品总数量  货品成本总价  货品销售价总和
		$checksession = $this->checkSession($to_warehouse_id);
		if(is_string($checksession)){
				$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>[".$to_warehouse_name."</b>] ".$checksession."</span>,请授权后再来处理");
				Util::jsonExit($result);
		} 

		$goods_sum   = 0;
		$xiaoshoujia = 0;
		$chengbenjia = 0;
		$mingyijia   = 0;

		foreach ($data as $key => $value)
		{
			/** 剔除什么也没填的数据 **/
			if( ($value[0]=='') && ($value[1]=='') && ($value[2]=='') && ($value[3]=='') && ($value[4]=='') && ($value[5]=='') && ($value[6]=='') && ($value[7]=='') && ($value[8]=='')  && ($value[9]=='')  && ($value[10]=='')  && ($value[11]=='') && ($value[12]=='') && ($value[13]=='') && ($value[14]=='')&& ($value[15]=='')&& ($value[16]=='')&& ($value[17]=='')&& ($value[18]=='')&& ($value[19]=='') && ($value[20]=='') ){
				unset($data[$key]);continue;
			}
			$chengbenjia += $value[5];
			$xiaoshoujia += $value[6];
			$mingyijia += $value[7];

		}
		if (!count($data))
		{
			$result['error'] = '请添加货品';
			Util::jsonExit($result);
		}

		$c_info['goods_num']   = count($data);
		$c_info['goods_total'] = $chengbenjia;//采购价  即原始成本价
		$c_info['shijia'] = $xiaoshoujia;//实价
		$c_info['pifajia'] = $mingyijia;//存退货货品的批发价 
		//单据插入  商品插入
		$res= $model->add_info_ha($data,$c_info);

		if($res['success'] == 1)
		{
			$result['success'] = 1;
			$result['id'] = $res['x_id'];
			$result['label'] = $res['label'];
			$result['tab_id'] = mt_rand();
			$result['error'] = '添加批发退货单成功！';
		}
		else
		{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}


	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		//print_r($_REQUEST);exit;
		$result = array('success' => 0,'error' =>'');
		//整理数据
		$id = _Post::getInt('id');

		$billModel = new WarehouseBillModel($id, 21);
		$status = $billModel->getValue('bill_status');
		$bill_no = $billModel->getValue('bill_no');
		if($status == 2){
			$result['error'] = '当前单据已经审核，不能修改';
			Util::jsonExit($result);
		}
		if($status == 3){
			$result['error'] = '当前单据已经取消，不能修改';
			Util::jsonExit($result);
		}
		
		if(empty($_POST['data'])){
		    $result['error'] = '请添加货品';
		    Util::jsonExit($result);
		}
		$checksession = $this->checkSession($billModel->getValue('to_warehouse_id'));
		if(is_string($checksession)){
				$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>[".$billModel->getValue('to_warehouse_name')."</b>] ".$checksession."</span>,请授权后再来处理");
				Util::jsonExit($result);
		} 
		
		$data	= $_POST['data'];
		$model  = new WarehouseBillInfoHModel(22);
		//制单时间
		//退货备注
		$bill_note =_Request::get('bill_note');
		$c_info = array(
			'id'=>$id,
			'bill_no'=>$bill_no,
			'bill_note'=>$bill_note,
			'create_user'=>$_SESSION['userName'],
			'bill_type'=>'H',
		);
		//统计  货品总数量  货品成本总价  货品销售价总和
		$chengbenjia= 0;
		$mingyijia = 0;
		$xiaoshoujia = 0;
		foreach ($data as $key => $value)
		{
			/** 剔除什么也没填的数据 **/
			if( ($value[0]=='') && ($value[1]=='') && ($value[2]=='') && ($value[3]=='') && ($value[4]=='') && ($value[5]=='') && ($value[6]=='') && ($value[7]=='') && ($value[8]=='')  && ($value[9]=='')  && ($value[10]=='')  && ($value[11]=='') && ($value[12]=='') && ($value[13]=='') && ($value[14]=='')&& ($value[15]=='')&& ($value[16]=='')&& ($value[17]=='')&& ($value[18]=='')&& ($value[19]=='')&& ($value[20]=='')){
				unset($data[$key]);continue;
			}
			if(empty($value[7])){
			    $value[7] = $value[5];
			    $data[$key][7]=$value[5];			    
			}
			if(empty($value[6])){
			    $value[6] = $value[5];
			    $data[$key][6]=$value[5];
			}
			$chengbenjia += $value[5];
			$mingyijia   += $value[7];
			$xiaoshoujia += $value[6];

		}		

		$c_info['goods_num']   = count($data);
		$c_info['goods_total'] = $chengbenjia;
		$c_info['shijia'] = $xiaoshoujia;//实价
		$c_info['pifajia'] = $mingyijia;//存退货货品的批发价 

		//单据插入  商品插入
		$res   = $model->get_data_h($id);//需要删除的数据信息
		$del_data = '';
		foreach ($res as $key=>$val)
		{
			if ($key)
			{
				$del_data .= ",'".$val['goods_id']."'";
			}
			else
			{
				$del_data .= "'".$val['goods_id']."'";
			}
		}
		$res= $model->update_info_h($data,$c_info,$del_data);

		if($res !== false)
		{
			$result['success'] = 1;
			$result['error'] = '修改成功！';
		}
		else
		{
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
		$model = new WarehouseBillInfoHModel($id,22);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		//联合删除？
		//$res = $model->delete();
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

		//$arr = Util::iniToArray(APP_ROOT.'warehouse/data/from_table_bill_m.tab');
		$view = new WarehouseBillInfoHView(new WarehouseBillInfoCModel(21));
		$arr = $view->js_table_ha;
	//var_dump($arr);exit;
		if(!$id){
			$arr['data_bill_ha'] = [
			];
		}else{
			$arr['data_bill_ha'] = $view->getTableGoods($id);
		}
		//var_dump($arr['data_bill_h']);exit;
		$json = json_encode($arr);
		echo $json;
	}


	/** 根据货号，查询货品信息 (table插件查询使用) **/
	public function getGoodsInfoByGoodsID($params){

		//var_dump($_REQUEST);exit;
		$goods_id = $params['goods_id'];
		$bill_id = isset($params['bill_id']) ? $params['bill_id'] : 0 ;
		$to_customer_id = isset($params['to_customer_id']) ? $params['to_customer_id'] : 0 ;
		$model = new WarehouseBillInfoHModel(21);
		//判断是否是当前单据的明细，如果是当前单据的明细，说明用户啥也没有输入，掠过查询货品信息的过程
		$exists = false;
		if($bill_id){
			$exists = $model->checkDetail($goods_id, $bill_id);
		}
		//var_dump($params);exit;
		if(!$exists){
			if($goods_id){
			    $goodsinfo = $model->get_pifa_xiaoshoujia($goods_id,$to_customer_id,3);
				//var_dump($goodsinfo);exit;
				if(empty($goodsinfo)){
					$error = "该货品没查出符合条件批发销售单！";
					$return_json = ['success' =>0 , 'error'=>$error];
					echo json_encode($return_json);exit;
				}
			    $return_json = ["{$goodsinfo['goods_sn']}", "{$goodsinfo['jinzhong']}", "{$goodsinfo['zuanshidaxiao']}", "{$goodsinfo['zhengshuhao']}", "{$goodsinfo['chengbenjia']}","{$goodsinfo['shijia_price']}", "{$goodsinfo['pifa_price']}", "0", "{$goodsinfo['shoucun']}", "{$goodsinfo['changdu']}", "{$goodsinfo['caizhi']}", "{$goodsinfo['zhushi']}","{$goodsinfo['zhushilishu']}","{$goodsinfo['fushi']}","{$goodsinfo['fushilishu']}","{$goodsinfo['fushizhong']}","{$goodsinfo['zongzhong']}","{$goodsinfo['jingdu']}","{$goodsinfo['yanse']}","{$goodsinfo['goods_name']}"];
    			$json = json_encode($return_json);
    			//echo "<pre>";print_r($json);exit;
    			echo $json;
			}
		}
	}


	/** 取消单据 **/
	public function closeBillInfoH($params){
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

		$model = new WarehouseBillInfoHModel(22);
		$goods_list = $model->getBillGoogsList($bill_id);	#获取明细列表
		$goods_ids ="";//货号所有
		foreach ($goods_list as $key=>$val)
		{
			if ($key)
			{
				$goods_ids .= ",'".$val['goods_id']."'";
			}
			else
			{
				$goods_ids .= "'".$val['goods_id']."'";
			}
		}
		$res = $model->closeBillInfoH($bill_id,$bill_no,$goods_ids);
		if($res){
			$result['success'] = 1;
			$result['error'] = '单据取消成功!!!';
		}else{
			$result['error'] = '单据取消失败!!!';
		}
		Util::jsonExit($result);
	}


	/** 详情页明细获取 **/
	public function getGoodsInDetails($params){
		//var_dump($_REQUEST);exit;
		$bill_id = $params['bill_id'];
		$args = array(
				'mod'	=> _Request::get("mod"),
				'con'	=> substr(__CLASS__, 0, -10),
				'act'	=> __FUNCTION__,
				'bill_id'	=>$bill_id
		);

		$g_model = new WarehouseBillGoodsModel(21);

		$where = array('bill_id'=>$bill_id);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$data = $g_model->pageList($where,$page,10,false);
        if(!empty($data['data'])){
            foreach ($data['data'] as &$val) {
                $val['out_warehouse_type'] = $g_model->getOutWarehouseTypeByGoodsId($val['goods_id']);
            }
        }
		//var_dump($data);exit;
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_bill_goods_show_page';

		$this->render('warehouse_bill_goods.html',array(
				'pa' =>Util::page($pageData),
				'dd' => new DictView(new DictModel(1)),
				'data' => $data,
				'isViewChengbenjia'=>$this->isViewChengbenjia(),
		));
	}


	/**审核单据**/
	public function checkBillInfoH($params){
		$result = array('success' => 0,'error' =>'');
		$bill_id = $params['id'];
		$bill_no = $params['bill_no'];

		$model = new WarehouseBillModel($bill_id,21);

		//根据仓库id获取仓库是否锁定(盘点)
		$model ->check_warehouse_lock($model->getValue('to_warehouse_id'));

		$checksession = $this->checkSession($model->getValue('to_warehouse_id'));
		if(is_string($checksession)){
			$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$model->getValue('to_warehouse_name')."</b>".$checksession."</span>的权限请联系管理员开通");
			Util::jsonExit($result);
		}

		$create_user = $model->getValue('create_user');
		if($create_user == $_SESSION['userName']){
		      $result['error'] = '不能审核自己的单据';
		      Util::jsonExit($result);
		}

		/** 如果单据是审核/取消状态 不允许修改 **/
		$status = $model->getValue('bill_status');

		if($status == 2){
			$result['error'] = '单据已审核，不能审核';
			Util::jsonExit($result);
		}else if($status == 3){
			$result['error'] = '单据已取消，不能审核';
			Util::jsonExit($result);
		}

		//取得单据信息
		$data  = $model->getDataObject();
		$newmodel = new WarehouseBillInfoHModel(22);
		$goods_list = $newmodel->getBillGoogsList($bill_id);	#获取明细列表
		$goods_ids ="";
		foreach ($goods_list as $key=>$val)
		{
			if ($key)
			{
				$goods_ids .= ",'".$val['goods_id']."'";
			}
			else
			{
				$goods_ids .= "'".$val['goods_id']."'";
			}
		}
		//data:单据信息 goods_ids：所有货品拼接
		$res = $newmodel->checkBillInfoH($bill_id,$bill_no,$data,$goods_ids,$model->getValue('to_warehouse_id'));
		if($res){
			$result['success'] = 1;
			$result['error'] = '审核成功';
		}else{
			$result['error'] = '审核失败';
		}
		Util::jsonExit($result);
	}

	//打印详情
	public function printBill() {

		$id = _Request::get('id');
		$model = new WarehouseBillModel($id,21);
		$data  = $model->getDataObject();
		$goods_info = $model->getDetail($id);
		$zuanshidaxiao=0;
		$fushizhong=0;
		$jinzhong=0;
		$shijia_total=0;
		foreach($goods_info as $val){
			$zuanshidaxiao +=$val['zuanshidaxiao'];
			$fushizhong +=$val['fushizhong'];
			$jinzhong +=$val['jinzhong'];
			$shijia_total += $val['shijia'];
		}
		
		$wholesModel = new JxcWholesaleModel(21);
		$whoList = $wholesModel->select2(' `wholesale_id`, `wholesale_name` '," `wholesale_status` = 1 ", 'all');
		$wholesaleArr=array();
		foreach ($whoList as $v){
		    $wholesaleArr[$v['wholesale_id']]=$v['wholesale_name'];
		}
		$this->render('xiaoshoutuihuo_print.htm', array(
				'data' => $data,
				'goods_info' => $goods_info,
				'jinzhong' => $jinzhong,
				'zuanshidaxiao' => $zuanshidaxiao,
				'fushizhong' => $fushizhong,
		        'shijia_total' => $shijia_total,
		        'wholesaleArr'=>$wholesaleArr
		));

	}


	public function printSum() {
		//获取单据bill_id
		$id = _Request::get('id');
		$model = new WarehouseBillModel($id,21);
		$data  = $model->getDataObject();
		//获取货品详情
		$goods_info = $model->getDetail($data["id"]);

		//获取单据信息
		$ZhuchengseInfo = $model->getBillinfo($id);
		//材质信息
		$zhuchengsezhongxiaoji = $zuixinlingshoujiaxiaoji = 0;
		$zhuchengsetongji[]=0;
		foreach($ZhuchengseInfo['zhuchengsedata'] as $val){
			$zhuchengsezhongxiaoji += $val['jinzhong'];
			$zhuchengsetongji[] = $val;
		}
		//主石信息
		$zhushitongji[]=0;
		$zhushilishuxiaoji = $zhushizhongxiaoji = 0;
		foreach ($ZhuchengseInfo['zhushidata'] as $val) {
			$zhushilishuxiaoji += $val['zhushilishu'];
			$zhushizhongxiaoji += $val['zuanshidaxiao'];
			$zhushitongji[] = $val;
		}
		//副石信息
		$fushitongji[]=0;
		$fushilishuxiaoji = $fushizhongxiaoji = 0;
		foreach ($ZhuchengseInfo['fushidata'] as $val) {
			$fushilishuxiaoji += $val['fushilishu'];
			$fushizhongxiaoji += $val['fushizhong'];
			$fushitongji[] = $val;
		}
		//统计数据
		$zuanshidaxiao=0;
		$fushizhong=0;
		$jinzhong=0;
		foreach($goods_info as $val){
			$zuanshidaxiao +=$val['zuanshidaxiao'];
			$fushizhong +=$val['fushizhong'];
			$jinzhong +=$val['jinzhong'];
		}
		$wholesModel = new JxcWholesaleModel(21);
		$whoList = $wholesModel->select2(' `wholesale_id`, `wholesale_name` '," `wholesale_status` = 1 ", 'all');
		$wholesaleArr=array();
		foreach ($whoList as $v){
		    $wholesaleArr[$v['wholesale_id']]=$v['wholesale_name'];
		}
		$this->render('xiaoshoutuihuo_print_ex.htm', array(
				'data' => $data,
				'goods_info' => $goods_info,
				'jinzhong' => $jinzhong,
				'zuanshidaxiao' => $zuanshidaxiao,
				'fushizhong' => $fushizhong,
				'zhuchengsetongji' => $zhuchengsetongji,
				'zhuchengsezhongxiaoji' => $zhuchengsezhongxiaoji,
				'zhushilishuxiaoji' => $zhushilishuxiaoji,
				'zhushizhongxiaoji' => $zhushizhongxiaoji,
				'zhushitongji' => $zhushitongji,
				'fushilishuxiaoji' => $fushilishuxiaoji,
				'fushizhongxiaoji' => $fushizhongxiaoji,
				'fushitongji' => $fushitongji,
		        'wholesaleArr'=>$wholesaleArr
		));

	}


	public function getGoodsInfos(){

		//var_dump($_REQUEST);exit;
		$g_ids = _Get::getList('g_ids');
		$g_ids = array_filter($g_ids);
		$bill_id = _Get::getInt('bill_id');
		$to_customer_id=_Request::get('to_customer_id');
		if(empty($to_customer_id)){
			//$error = "请选择退货客户！";
			$error = array('1'=>'请选择退货客户！');
			$return_json = ['success' =>0 , 'error'=>$error];
			exit(json_encode($res)); 
		}
		$model = new WarehouseGoodsModel(21);
		$newmodel = new WarehouseBillInfoHModel(21);
		$view = new WarehouseBillInfoHView($model);
	 	foreach($g_ids as $key=>$goods_id){
			$goodsinfo[$key] = $newmodel->get_pifa_xiaoshoujia_ha($goods_id);
			if(empty($goodsinfo[$key])){
				$error = array('2'=>"货号{$goods_id}没查出符合条件的货品");
				$res = ['success' =>0 , 'error'=>$error];
				exit(json_encode($res)); 				
			}
		}
		//$res = $model->table_GetGoodsInfo($g_ids,$view->js_table['lable'],3,$bill_id);
		$res = $model->table_GetGoodsInfo($g_ids,$view->js_table_ha['lable'],3,$bill_id, 0,0,0,0,0,array('to_customer_id'=>$to_customer_id));
		exit(json_encode($res)); 
	}




}

?>
