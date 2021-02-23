<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillSController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-15 14:43:03
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoDController extends CommonController
{
	protected $smartyDebugEnabled = true;
	protected $whitelist = array('printBill','printSum');


	/***
	获取有效的仓库
	 ***/
	public function warehouse()
	{
		$model_w	= new WarehouseModel(21);
		$warehouse  = $model_w->select(array('is_delete'=>1),array("id","name",'code'));
		return $warehouse;
	}

	public function add()
	{
		if(SYS_SCOPE=='boss'){
			if(!in_array($_SESSION['userName'],array("admin","sz张宇","谭碧玉","梁全升","程丹蕾","韦芦芸","深圳李平")))
			{
				echo '此功能不开放到业务同事，单据有问题联系技术部。';exit;
			}
	    }
		$company=$this->getCompanyList();
		$this->render('warehouse_bill_info_d.html',array(
			'view'=>new WarehouseBillView(new WarehouseBillModel(21)),
			'dd'=>new DictView(new DictModel(1)),
			'company'=>$company,
			'warehouse'=>$this->warehouse()
			));

	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$billModel = new WarehouseBillModel($id,21);

		$this->render('warehouse_bill_info_d.html',array(
			'view'=>new WarehouseBillView($billModel),
			'tab_id'=>$tab_id,
			'dd' => new DictModel(1),
			'company' =>$this->getCompanyList(),
			'warehouse'=>$this->warehouse()
		));
	}

	/** table 插件**/
	public function mkJson()
	{
		$id = _Post::getInt('id');

		$arr = Util::iniToArray(APP_ROOT.'warehouse/data/from_table_bill_d.tab');

		if(!$id)
		{
			$arr['data_bill_d'] = [];
		}
		else
		{
			$arr['data_bill_d'] = array();
			$model = new WarehouseBillGoodsModel(21);
			$data = $model->get_bill_data($id);

			foreach($data as $k => $v)
			{
				$arr['data_bill_d'][] =[
					"{$v['goods_id']}", "{$v['goods_sn']}", "{$v['jinzhong']}", "{$v['zuanshidaxiao']}",
					"{$v['zhengshuhao']}", "{$v['mairugongfei']}", "{$v['sale_price']}", "{$v['shijia']}","{$v['shoucun']}",
					"{$v['changdu']}",  "{$v['caizhi']}","{$v['zhushi']}" ,
					"{$v['zhushilishu']}" , "{$v['fushi']}" , "{$v['fushilishu']}" , "{$v['fushizhong']}" , "{$v['zongzhong']}" ,
					"{$v['zhushijingdu']}" ,"{$v['zhushiyanse']}" , "{$v['goods_name']}"
				];
			}
			//var_dump($arr);exit;
		}
		$json = json_encode($arr);
		echo $json;
	}
	/*销售退货单保存*/
	public function insert($params)
	{
		$result = array('success' => 0,'error' =>'');
		$bill_info = array(
			'bill_note'=> trim($params['bill_note']),
			'order_sn' => $params['order_sn'],
			'goods_num' => 0,
			'shijia' => 0,
			'to_warehouse_id' => trim($params['to_warehouse_id'])
		);
		if(empty($bill_info['order_sn']))
		{
			$result['error'] = '请输入订单号';
			Util::jsonExit($result);
		}

		if(empty($bill_info['to_warehouse_id']))
		{
			$result['error'] = '请选择入库仓库';
			Util::jsonExit($result);
		}

		//根据仓库id获取仓库名称、公司id、公司名称
		$warehouse_model = new WarehouseModel(21);
		$warehouse_name  = $warehouse_model->getWarehosueNameForId($bill_info['to_warehouse_id']);
		$rel_model       = new WarehouseRelModel(21);
		$company_id      = $rel_model->GetCompanyByWarehouseId($bill_info['to_warehouse_id']);
		$company_model   = new CompanyModel(1);
		$company_name    = $company_model->getCompanyName($company_id );

		$bill_info['to_warehouse_name']  = $warehouse_name;
		$bill_info['to_company_id']      = $company_id;
		$bill_info['to_company_name']    = $company_name;
		$weixiuCheck = $warehouse_model->checkOrderWeixiuStatus($bill_info['order_sn']);
		if($weixiuCheck){
		    $result ['error'] = "订单{$bill_info['order_sn']}有维修进行中的商品，不能做销售退货单!";
		    Util::jsonExit($result);
		}
		if(!$company_id || !$company_name)
		{
			$result['error'] = $warehouse_name.'查不到所属公司';
			Util::jsonExit($result);
		}

		if( !isset($params['data']))
		{
			$result['error'] = '请输入货品明细';
			Util::jsonExit($result);
		}

		$order_goods = $params['data'];
		$goods_list  = array();
		foreach ($order_goods as $key => $val)
		{
			//提出没有填的栏
			if($val[0] == '')
			{
				unset($order_goods[$key]);
				continue;
			}
			if($val['7'] == '')
			{
				$result['error'] = '请输入退货价';
				Util::jsonExit($result);
			}
			$goods_list[$key]['goods_id'] = $val['0'];
			$goods_list[$key]['shijia'] = $val['7'];
		}
		$bill_info['goods_num'] = count($order_goods);
		if($bill_info['goods_num']<=0) 
		{
			$result['error'] = '请输入货号';
			Util::jsonExit($result);
		}

		if($bill_info['to_company_id']==58){
			$checksession = $this->checkSession($bill_info['to_warehouse_id']);
			if(is_string($checksession)){
				$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$warehouse_name."</b>".$checksession."</span>的权限请联系管理员开通");
				Util::jsonExit($result);
			}
		}	

		//var_dump($bill_info);var_dump($goods_list);exit;
		$model =  new WarehouseBillInfoDModel(22);

		$res = $model->add_shiwu($bill_info,$goods_list);
		if($res['success'] == 1)
		{
			$result['success'] = 1;
			$result['x_id'] = $res['x_id'];
			$result['label'] = $res['label'];
			$result['tab_id'] = mt_rand();
			$result['error'] = '添加退货销售单成功！';
		}
		else
		{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}


		/*销售退货单保存*/
	public function update($params)
	{
		$id = $params['id'];
		$result = array('success' => 0,'error' =>'');
		$bill_info = array(
			'id' => $id,
			'bill_note'=> trim($params['bill_note']),
			'order_sn' => $params['order_sn'],
			'goods_num' => 0,
			'shijia' => 0,
			'to_warehouse_id' => trim($params['to_warehouse_id'])
		);

		if(empty($bill_info['order_sn']))
		{
			$result['error'] = '请输入订单号';
			Util::jsonExit($result);
		}

		if(empty($bill_info['to_warehouse_id']))
		{
			$result['error'] = '请选择入库仓库';
			Util::jsonExit($result);
		}

		$model_hh =  new WarehouseBillModel($bill_info['id'],22);
		$bill_info['bill_no'] = $model_hh->getValue('bill_no');
		//根据仓库id获取仓库名称、公司id、公司名称
		$warehouse_model = new WarehouseModel(21);
		$warehouse_name  = $warehouse_model->getWarehosueNameForId($bill_info['to_warehouse_id']);
		$rel_model       = new WarehouseRelModel(21);
		$company_id      = $rel_model->GetCompanyByWarehouseId($bill_info['to_warehouse_id']);
		$company_model   = new CompanyModel(1);
		$company_name    = $company_model->getCompanyName($company_id );

		$bill_info['to_warehouse_name']  = $warehouse_name;
		$bill_info['to_company_id']      = $company_id;
		$bill_info['to_company_name']    = $company_name;
		if($bill_info['to_company_id']==58){
			$checksession = $this->checkSession($bill_info['to_warehouse_id']);
			if(is_string($checksession)){
				$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$warehouse_name."</b>".$checksession."</span>的权限请联系管理员开通");
				Util::jsonExit($result);
			}
		}	

		if( !isset($params['data']))
		{
			$result['error'] = '请输入货品明细';
			Util::jsonExit($result);
		}
		$order_goods = $params['data'];
		$goods_list  = array();
		foreach ($order_goods as $key => $val)
		{
			//提出没有填的栏
			if($val[0] == '')
			{
				unset($order_goods[$key]);
				continue;
			}
			if($val['7'] == '' || $val['7'] == 0.00)
			{
				$result['error'] = '请输入退货价';
				Util::jsonExit($result);
			}
			$goods_list[$key]['goods_id'] = $val['0'];
			$goods_list[$key]['shijia'] = $val['7'];
		}
		$bill_info['goods_num'] = count($order_goods);
		//var_dump($bill_info);var_dump($goods_list);exit;
		$model =  new WarehouseBillInfoDModel($bill_info['id'],22);
		$res = $model->update_shiwu($bill_info,$goods_list);
		if($res['success'] == 1)
		{
			$result['success'] = 1;
			$result['title'] = '修改销售退货单成功';
		}
		else
		{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}
	/** 根据货号，查询货品信息 (table插件查询使用) **/
	public function getGoodsInfoByGoodsID($params)
	{
		//$goods_id = $params['goods_id'];
		$g_ids = _Get::getList('g_ids');
		$g_ids = array_filter($g_ids);	
        $return_json=array();	
		//if($goods_id)
		$error="";
		foreach ($g_ids as $key => $goods_id) {
			$model = new WarehouseBillInfoDModel(21);
			$goods = $model->getGoodsInfoByGoodsID($goods_id);
            
			if(!empty($goods))
			{
				if($goods['is_on_sale'] == 3)
				{	//库存状态
					$return_json[] = ["{$goods['goods_sn']}","{$goods['jinzhong']}","{$goods['zuanshidaxiao']}","{$goods['zhengshuhao']}","{$goods['mairugongfei']}","{$goods['zuixinlingshoujia']}","","{$goods['shoucun']}","{$goods['changdu']}","{$goods['caizhi']}","{$goods['zhushi']}","{$goods['zhushilishu']}", "{$goods['fushi']}", "{$goods['fushilishu']}", "{$goods['fushizhong']}", "{$goods['zongzhong']}", "{$goods['jingdu']}", "{$goods['yanse']}", "{$goods['goods_name']}"];
					//echo json_encode($return_json);exit;
				}
				else
				{
					/*
					if($_SESSION['userName'] == 'admin') //补单据的时候用
					{
						$return_json = ["{$goods['goods_sn']}","{$goods['jinzhong']}","{$goods['zuanshidaxiao']}","{$goods['zhengshuhao']}","{$goods['mairugongfei']}","{$goods['zuixinlingshoujia']}","","{$goods['shoucun']}","{$goods['changdu']}","{$goods['caizhi']}","{$goods['zhushi']}","{$goods['zhushilishu']}", "{$goods['fushi']}", "{$goods['fushilishu']}", "{$goods['fushizhong']}", "{$goods['zongzhong']}", "{$goods['jingdu']}", "{$goods['yanse']}", "{$goods['goods_name']}"];
						echo json_encode($return_json);exit;
					}
					else
					{*/
						$error .= "货号为<span style='color:red;'>{$goods_id}</span>的货品不是已销售状态，不能制销售退货单<br>";
						//$return_json = ['success' =>0 , 'error'=>$error];
						//echo json_encode($return_json);exit;
					//}
				}
			}
			else
			{
				$error .= "仓库查不到货号为<span style='color:red;'>{$goods_id}</span>的货品<br>";
				//$return_json = ['success' =>0 , 'error'=>$error];
				//echo json_encode($return_json);exit;
			}
		}
		if(!empty($error)){
			$return_json = ['success' =>0 , 'error'=>$error];
		}
		echo json_encode($return_json);exit;
	}



	/**
	 *	show，渲染查看页面
	 */
	public function show($params)
	{
		$id = intval($params["id"]);	//单据ID
		$model = new WarehouseBillModel($id,21);
		$status = $model->getValue('bill_status');

		$this->render('warehouse_bill_d_show.html',array(
			'view' => new WarehouseBillView(new WarehouseBillModel($id, 21)),
			'dd' => new DictView(new DictModel(1)),
			'bar'=>Auth::getViewBar(),
			'status'=>$status,
			'isViewChengbenjia'=>$this->isViewChengbenjia(),
		));
	}

	public function search() {
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'bill_id'=>_Request::get('id'),
		);
		//print_r($args);exit;
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where['bill_id'] =$args['bill_id'];
		$model = new WarehouseBillGoodsModel(21);
		$data = $model->pageList($where,$page,10,false);
		$this->addJiajiaChengben($data, $where['bill_id']);

		$pageData=$data;
		$pageData['filter'] = $args;
		//$pageData['recordCount'] = count($pageData['data']); // 一页显示所有
		$pageData['jsFuncs'] = 'warehouse_bill_info_d_d_search_page';
		$this->render('warehouse_bill_info_d_list.html',array(
				'pa'=>Util::page($pageData),
				'page_list'=>$data,
				'isViewChengbenjia'=>$this->isViewChengbenjia(),
		));
	}


	//人工添加退货商品
	public function AddGoodsForBill($params){
		$this->render('warehouse_bill_d_add_goods.html' , array());
	}

	//添加退货商品的操作
	public function addgoods($params){
		$result = array('success' => 0,'error' =>'');
		$args = array(
			'bill_no' => trim($params['bill_no']),
			'goods_id' => trim($params['goods_id']),
			'tuihuojia' => floatval($params['tuihuojia'])
		);

		$model = new WarehouseBillInfoDModel(22);
		$res = $model->addDetailGoods($args);
		if($res['success'] == true){
			$result['success'] = 1;
			$result['error'] = '添加成功';
		}else{
			$result['error'] = $res['error'];
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
		$this->render('xiaoshoutuihuo_print.html', array(
				'dd' => $dd,
				'data' => $data,
				'goods_info' => $goods_info,
				'fushizhong' => $fushizhong,
				'jinzhong' => $jinzhong,
				'zuanshidaxiao' => $zuanshidaxiao,

		));

	}


	public function CloseBill($params)
	{
		$result = array('success' => 0,'error' =>'');
		$bill_id = $params['id'];
		$bill_no = $params['bill_no'];

		$model = new WarehouseBillModel($bill_id,21);
		$dmodel = new WarehouseBillInfoDModel(22);

		/** 如果单据是审核/取消状态 不允许修改 **/
		$status = $model->getValue('bill_status');
		if($status == 2)
		{
			$result['error'] = '单据已审核，不能取消';
			Util::jsonExit($result);
		}
		else if($status == 3)
		{
			$result['error'] = '单据已取消，不能重复操作';
			Util::jsonExit($result);
		}
		$create_user = $model->getValue('create_user');
		if($create_user !== $_SESSION['userName'])
		{
			$result['error'] = '不能取消别人的单据';
			Util::jsonExit($result);
		}
		$res = $dmodel->closebill($bill_id , $bill_no);
		if($res['success'] == 1)
		{
			$result['success'] = 1;
			$result['error'] = '取消成功';
		}
		else
		{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}
	/******************************************************************************
	func:checkBillInfoD
	decription:审核销售退货单
	*******************************************************************************/
	public function checkBillInfoD($params)
	{
		$result = array('success' => 0,'error' =>'');
		$bill_id = $params['id'];

		$model = new WarehouseBillModel($bill_id,21);
		$dmodel = new WarehouseBillInfoDModel(22);

		/** 如果单据是审核/取消状态 不允许修改 **/
		$status = $model->getValue('bill_status');
		if($status == 2)
		{
			$result['error'] = '单据已审核，不能审核';
			Util::jsonExit($result);
		}
		else if($status == 3)
		{
			$result['error'] = '单据已取消，不能审核';
			Util::jsonExit($result);
		}
		$create_user = $model->getValue('create_user');

		//if(!in_array($_SESSION['userName'],array("admin","sz张宇","谭碧玉","梁全升","程丹蕾","韦芦芸"))){
			if($create_user === $_SESSION['userName'] && $create_user !='admin')
			{
				$result['error'] = '不能审核自己的单据';
				Util::jsonExit($result);
			}
		//}
		$checksession = $this->checkSession($model->getValue('to_warehouse_id'));
		if(is_string($checksession)){
			$result = array('success' => 0,'error' =>"您没有<span style='color: #ff0000;'><b>".$model->getValue('to_warehouse_name')."</b>".$checksession."</span>的权限请联系管理员开通");
			Util::jsonExit($result);
		}

		$res = $dmodel->checkBill($bill_id );
		if($res['success'] == 1)
		{
			$result['success'] = 1;
			$result['error'] = '审核销售退货单成功';
			//AsyncDelegate::dispatch('warehouse', array('event'=>'bill_D_checked', 'bill_id' => $bill_id));
		}
		else
		{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
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
		$zhuchengsetongji=array();
		foreach($ZhuchengseInfo['zhuchengsedata'] as $val){
			$zhuchengsezhongxiaoji += $val['jinzhong'];
			$zhuchengsetongji[] = $val;
		}
		//主石信息
		$zhushilishuxiaoji = $zhushizhongxiaoji = 0;
		$zhushitongji=array();
		foreach ($ZhuchengseInfo['zhushidata'] as $val) {
			$zhushilishuxiaoji += $val['zhushilishu'];
			$zhushizhongxiaoji += $val['zuanshidaxiao'];
			$zhushitongji[] = $val;
		}
		//副石信息
		$fushilishuxiaoji = $fushizhongxiaoji = 0;
		$fushitongji=array();
		foreach ($ZhuchengseInfo['fushidata'] as $val) {
			$fushilishuxiaoji += $val['fushilishu'];
			$fushizhongxiaoji += $val['fushizhong'];
			$fushitongji[] = $val;
		}

		$this->render('xiaoshoutuihuo_print_ex.html', array(
				'dd' => $dd,
				'data' => $data,
				'goods_info' => $goods_info,
				'fushizhong' => $fushizhong,
				'jinzhong' => $jinzhong,
				'zuanshidaxiao' => $zuanshidaxiao,
				'zhuchengsetongji' => $zhuchengsetongji,
				'zhuchengsezhongxiaoji' => $zhuchengsezhongxiaoji,
				'zhushilishuxiaoji' => $zhushilishuxiaoji,
				'zhushizhongxiaoji' => $zhushizhongxiaoji,
				'zhushitongji' => $zhushitongji,
				'fushilishuxiaoji' => $fushilishuxiaoji,
				'fushizhongxiaoji' => $fushizhongxiaoji,
				'fushitongji' => $fushitongji

		));

	}

	// 增加字段 M调拨单：加价成本价
	private function addJiajiaChengben(&$data, $bill_id){
		$warehousebillModle = new WarehouseBillModel(21);
		$to_company_id = $warehousebillModle->GetBillInfoByid('to_company_id', 'id='.$bill_id, 'getOne');

		$warehouseModel = new WarehouseGoodsModel(21);
		foreach($data['data'] AS &$item){
			// 默认等于 原始采购价
			$item['jiajiachengben'] = $item['yuanshichengbenjia'];
			if (!in_array($to_company_id, array(58,223))) {
				$jiajialv = $warehouseModel->getBillJiajiaInfo(null, 1, $item['goods_id'],$bill_id);
				if ($jiajialv) {
					$item['jiajiachengben'] = number_format($item['yuanshichengbenjia'] * (1 + $jiajialv / 100), 2);
				}
			}
		}
	}
}

?>