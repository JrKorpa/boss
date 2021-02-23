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
class WarehouseBillInfoSController extends CommonController
{
	protected $smartyDebugEnabled = true;
	protected $whitelist =array('printBill','printSum','printHedui');

	/**
	 *	index，添加商品到销售单中---显示页面
	 */
	public function index ($params)
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }		
		$this->render('warehouse_bill_info_s_info.html',array(
			));
	}
		/**
	 *	insert，添加商品到销售单中---保存
	 */
	public function insert ($params)
	{

		$result = array('success' => 0,'error' =>'','compare' =>0);
		$goods_id = $params['goods_id']?$params['goods_id']:_Request::get("goods_id");//货品
		$order_sn = $params['order_sn']?$params['order_sn']:_Request::get("order_sn");//销售单号

		#1、取得货号、货品价格（库存状态）
		$arr = array();
		$goods_model = new WarehouseGoodsModel(21);
		$goods_info  = $goods_model->getGoodsByGoods_id($goods_id);
		if(!$goods_info)
		{
			$result['error'] = $goods_id."货号不存在，请检查";
			Util::jsonExit($result);
		}
		if($goods_info['is_on_sale'] != 2 )
		{
			$result['error'] = $goods_id."货号不是库存状态，请检查";
			Util::jsonExit($result);
		}

		#2、取得销售单信息(单号，销账总金额)和商品信息(货号和成本价)
		$model_s       = new WarehouseBillInfoSModel(22);
		$xiaoshou_info = $model_s->getXiaoshouInfo($order_sn);
		if(!$xiaoshou_info)
		{
			$result['error'] = $order_sn."销售单不存在，请检查";
			Util::jsonExit($result);
		}
		
		#3、钻补销操作提醒
		$orderDetailInfo = ApiSalesModel::GetOrderDetailByOrderId($order_sn);
		foreach ($orderDetailInfo as $detail){
			//找到托钻对应关系的那个商品
			$goodsInfo = ApiSalesModel::getGoodsInfoByZhengshuhao($detail['zhengshuhao'],$detail['order_id']);
		}

		
		if(!empty($goodsInfo) && $goodsInfo['goods_id'] == $goods_id)
		{
			$result['error'] = $order_sn."销售单不可添加货号为".$goods_id."的商品，该商品已经有另外一条订单！" ;
			Util::jsonExit($result);
		}

        //添加的货品仓储位置必须和单据的出库公司一致
		if ($xiaoshou_info[0]['from_company_id'] != $goods_info['company_id'])
		{

			$result['error'] = $goods_id."所在公司与销售单的出库公司不一致，请将该货品做调拨单";
			Util::jsonExit($result);
		}

		$price_all = $goods_info['chengbenjia'];//用来存放成本加总和
		$xiaoshou_price = $xiaoshou_info[0]['xiaoshoujia'];


		
		#判断销售单状态
		$bill_status = $xiaoshou_info[0]['bill_status'];
		$kela_order_sn = $xiaoshou_info[0]['order_sn'];

		//admin可以跳过这步，是为了技术部直接添加销售单用的。
		if ($bill_status!=1 && $_SESSION['userName'] != 'admin' && $_SESSION['userName'] != '梁全升')
		{
			$result['error'] = $order_sn."销售单不是保存状态，请检查";
			Util::jsonExit($result);
		}
		
		//已经审核，如果跨月，就是特殊人员（admin，梁全升）也不能添加
		$check_time=$xiaoshou_info[0]['check_time'];
		$check_time=date("Y-m",strtotime($check_time));
		$new_time=date("Y-m",time());
		if($bill_status==2 && $check_time != $new_time){
			$result['error'] = $order_sn."销售单审核时间不是本月，不能补单";
			Util::jsonExit($result);
		}
		
		/** 2015-12-25 zzm boss-1015 **/
		if(isset($_GET['compare']) && !empty($_GET['compare'])){
			$mingyichengben = $goods_info['mingyichengben'] ? $goods_info['mingyichengben'] : 0;
			$bill_goods_model = new WarehouseBillGoodsModel(21);
			$bill_goods_price_arr = $bill_goods_model->getBillPrice($order_sn);
			$total_mingyichengben = $mingyichengben + $bill_goods_price_arr['mingyichengben'];
			if($bill_goods_price_arr['shijia'] < $total_mingyichengben){
				$result['error'] = "订单金额低于总成本，是否继续？";
				$result['compare'] = 1;
				$result['goods_id'] = $goods_id;
				$result['order_sn'] = $order_sn;
				Util::jsonExit($result);
			}
		}

		$price_all = $goods_info['yuanshichengbenjia'];//用来存放成本加总和
		$xiaoshou_price = $xiaoshou_info[0]['zongshijia'];

		foreach ($xiaoshou_info as $key=>$val)
		{
			$price_all += $val['yuanshichengben'];
		}
		$arr['bill_no']     = $order_sn;
		$arr['goods_id']	= $goods_info['goods_id'];
		$arr['yuanshichengben'] = $goods_info['yuanshichengbenjia'];
		//$arr['shijia']		= $xiaoshou_price;
		//$arr['id']			= $xiaoshou_info[0]['id'];

		$xiaoshou_info[] = $arr;

		#3、按照成本价比例计算销售价
		$price_sum_last = 0;//用于存除最后一个商品的所以商品成本价格总和
		for ($i=0;$i<count($xiaoshou_info);$i++)
		{
			 if($i==count($xiaoshou_info)-1)
			 {
				 //echo "uuu".$price_sum_last."<br>".$xiaoshou_price;
				 $xiaoshou_info[$i]['xiaoshoujia_goods'] = $xiaoshou_price-$price_sum_last;
			 }
			 else
			 {
				 $xiaoshou_info[$i]['xiaoshoujia_goods'] =  round(( $xiaoshou_info[$i]['yuanshichengben']*$xiaoshou_price)/$price_all ,2);
				 $price_sum_last += $xiaoshou_info[$i]['xiaoshoujia_goods'];
			 }
		}
		#4、修改销售单商品价格
		$res = $model_s->addGoods($goods_info,$xiaoshou_info);
		if(!$res)
		{
			$result['error'] = "添加失败";
			Util::jsonExit($result);
		}
		$userName = $_SESSION['userName'] == 'admin'?'SYSTEM':$_SESSION['userName'];
		ApiModel::sales_api(	//回写订单日志
		'AddOrderLog',
		array('order_no','create_user','remark'),
		array($kela_order_sn , $userName , '销售单中补销账商品，货号：'.$goods_id));

		$result['success'] = 1;
		Util::jsonExit($result);
	}
	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$off = '<button class="btn btn-sm yellow" onclick="util.closeTab(this);" data-url="" name="离开" title="关闭当前页签" list-id="84" data-title="">离开 <i class="fa fa-mail-reply"></i></button>';
		die('亲~ 销售单是不能编辑的哦！#^_^#  '.$off);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show($params)
	{
		//$id = intval($params["id"]);	//单据ID
		#财务连接需要单据号
		if (isset($params['bill_no']))
		{
			$model	= new WarehouseBillModel(21);
			$id     = $model->getIdByBillNo(trim($params['bill_no']));
			if(!$id)
			{
				echo "销售单不存在";exit;
			}
		}
		else
		{
			$id = intval($params["id"]);
		}
		$model = new WarehouseBillModel($id,21);
		$status = $model->getValue('bill_status');
		$this->render('warehouse_bill_s_show.html',array(
			'view' => new WarehouseBillView(new WarehouseBillModel($id, 21)),
			'dd' => new DictView(new DictModel(1)),
			'bar'=>Auth::getViewBar(),
			'status'=>$status,
		));
	}

	/**
	* 货品明细
	*/
	public function GoodsDetail($params){
		$args = array(
			'mod'   => _Request::get("mod"),
			'con'   => substr(__CLASS__, 0, -10),
			'act'   => __FUNCTION__,
		);
		$infos_id = $params['id'];
		$page = _Request::getInt("page",1);
		$where['bill_id'] = $infos_id;
		$model = new WarehouseBillGoodsModel(21);
		$pageData = $model->pageList($where,$page,10,false);
		$this->addJiajiaChengben($pageData, $infos_id);

		$pageData['filter'] = $args;
		//$pageData['recordCount'] = count($pageData['data']);
		$pageData['jsFuncs'] = 'warehouse_goods_list';
		$this->render('warehouse_bill_s_goods.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$pageData,
		    'isViewChengbenjia'=>$this->isViewChengbenjia(),
			'warehouse_view' => new WarehouseView(new WarehouseModel(21)),
			'isViewChengbenjia'=>$this->isViewChengbenjia(),
		));
	}
        //打印核对单 销售单
        public function printHedui(){
            //获取bill_id单据id
		$id = _Request::get('id');

		//数字词典
		$dd =new DictModel(1);
		$model = new WarehouseBillModel($id,21);
		//打印表头信息
		$data  = $model->getDataObject();

		$newmodel = new WarehouseBillInfoSModel(21);
                $billSinfo = $newmodel->getBillXiaoshou($id);


		foreach($billSinfo as $key=>$val){
			$data[$key]=$val;
		}

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

		$this->render('xiaoshou_print_detail.html', array(
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

	//打印详情
	public function printBill() {
		//获取单据bill_id
		$id = _Request::get('id');
		$model = new WarehouseBillModel($id,21);
		$data  = $model->getDataObject();
		//获取货品详情
		$goods_info = $model->getDetail($id);
		// 统计数据
		$zuanshidaxiao=0;
		$fushizhong=0;
		$jinzhong=0;
		$num=0;
		foreach($goods_info as $val){
			$zuanshidaxiao +=$val['zuanshidaxiao'];
			$fushizhong +=$val['fushizhong'];
			$jinzhong +=$val['jinzhong'];
		}
		$this->render('xiaoshou_print.html', array(
				'data' => $data,
				'goods_info' => $goods_info,
				'jinzhong' => $jinzhong,
				'zuanshidaxiao' => $zuanshidaxiao,
				'fushizhong' => $fushizhong,
				'num' => $num,
		));

	}


	//打印详情
	public function printSum() {

		//获取单据bill_id
		$id = _Request::get('id');
		$dd =new DictModel(1);
		$model = new WarehouseBillModel($id,21);
		$data  = $model->getDataObject();
		//获取货品详情
		$goods_info = $model->getDetail($id);

		//获取单据信息
		$ZhuchengseInfo = $model->getBillinfo($id);
		//echo "<pre>"; print_r($BillInfo);exit;
		//材质信息
		$zhuchengsezhongxiaoji = 0;
		$zhuchengsetongji[]=0;
		foreach($ZhuchengseInfo['zhuchengsedata'] as $val){
			$zhuchengsezhongxiaoji += $val['jinzhong'];
			$zhuchengsetongji[] = $val;
		}
		//主石信息
		$zhushilishuxiaoji = $zhushizhongxiaoji = 0;
		$zhushitongji[]=0;
		foreach ($ZhuchengseInfo['zhushidata'] as $val) {
			$zhushilishuxiaoji += $val['zhushilishu'];
			$zhushizhongxiaoji += $val['zuanshidaxiao'];
			$zhushitongji[] = $val;
		}
		//副石信息
		$fushilishuxiaoji = $fushizhongxiaoji = 0;
		$fushitongji[]=0;
		foreach ($ZhuchengseInfo['fushidata'] as $val) {
			$fushilishuxiaoji += $val['fushilishu'];
			$fushizhongxiaoji += $val['fushizhong'];
			$fushitongji[] = $val;
		}

		//统计数据
		$zuanshidaxiao=0;
		$fushizhong=0;
		$jinzhong=0;
		$num=0;
		foreach($goods_info as $val){
			$zuanshidaxiao +=$val['zuanshidaxiao'];
			$fushizhong +=$val['fushizhong'];
			$jinzhong +=$val['jinzhong'];
		}
		$this->render('xiaoshou_print_ex.html', array(
				'data' => $data,
				'goods_info' => $goods_info,
				'jinzhong' => $jinzhong,
				'zuanshidaxiao' => $zuanshidaxiao,
				'fushizhong' => $fushizhong,
				'num' => $num,
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


	/** 取消单据 **/
	public function closeBillInfoS($params){
		//var_dump($_REQUEST);exit;
		$result = array('success' => 0,'error' =>'');
		$bill_id = $params['id'];
		$bill_no = $params['bill_no'];
		$order_sn = $params['order_sn'];
		$date_time		= date('Y-m-d H:i:s',time());
		//var_dump($order_sn);exit;
		$model = new WarehouseBillModel($bill_id,21);
		$create_user = $model->getValue('create_user');
		$now_user = $_SESSION['userName'];
		if($create_user !== $now_user){
			$result['error'] = '亲~ 非本人单据，你是不能编辑的哦！#^_^#';
			Util::jsonExit($result);
		}

		/** 如果单据是审核/取消状态 不允许修改 **/
		$status = $model->getValue('bill_status');
		if($status == 2){
			$result['error'] = '单据已审核，不能取消';
			Util::jsonExit($result);
		}else if($status == 3){
			$result['error'] = '单据已取消，不能取消';
			Util::jsonExit($result);
		}

		$model = new WarehouseBillInfoSModel(22);
		#验证订单状态是否能操作
		//代码屏蔽掉，取消销售单，不用做什么验证。---juan
		//$apisalesmodel =  ApiSalesModel::VerifyOrderStatus($order_sn);

		#取消销售单状态
		$model_s = new WarehouseBillInfoSModel(22);
		$s_res   =$model_s->cancel_info_s($order_sn);
		if ($s_res['status'] != true)
		{
			$result['error'] = '[取消失败-销售单] '.$s_res['error'];
			Util::jsonExit($result);
		}
		#配货状态修改为配货中
		$model_sale = new ApiSalesModel();
		$sale_res = $model_sale->EditOrderdeliveryStatus(array($order_sn),3,$date_time,$_SESSION['userId']);
		if ($sale_res['error'])
		{
			$result['error'] = '操作失败-配货';
			Util::jsonExit($result);
		}

		if($s_res){
			$result['success'] = 1;
			//修改可销售商品状态
			$goodsInfo=ApiModel::sales_api('GetOrderInfoByOrdersn',array('order_sn'),array($order_sn));
			$goods_data = $goodsInfo['return_msg']['data'];
			$change=[];$where=[];
			foreach ($goods_data as $k => $v) {
				$where[$k]['goods_id'] = $v['goods_id'];
				$change[$k]['is_sale'] = '1';	//上架
				$change[$k]['is_valid'] = '1';	//有效
			}
			$ApiSalePolcy = new ApiSalepolicyModel();
			$ApiSalePolcy->setGoodsUnsell_t($change,$where);

			//变更订单的发货状态（变成未发货）
			$xxx = ApiModel::sales_api('UpdateSendGoodStatus',array('order_sn','send_good_status'),array(array($order_sn), 1));

			//记录订单日志
			$OrderFqcConfModel =new OrderFqcConfModel(21);
			ApiSalesModel::addOrderAction($order_sn , $_SESSION['userName'] , "销售单".$bill_no."已手工取消");	//回写订单日志

			$result['error'] = '已手工取消订单!!!';
		}else{
			$result['error'] = '单据取消失败!!!';
		}
		Util::jsonExit($result);
	}

	// 增加字段 M调拨单：加价成本价
	private function addJiajiaChengben(&$data, $bill_id){
		$warehousebillModle = new WarehouseBillModel(21);
		$from_company_id = $warehousebillModle->GetBillInfoByid('from_company_id', 'id='.$bill_id, 'getOne');

		$warehouseModel = new WarehouseGoodsModel(21);
		foreach($data['data'] AS &$item){
		    if(!empty($item['pifajia'])){
		        $item['jiajiachengben'] = $item['pifajia'];
		        continue;
		    } 
			// 默认等于 原始采购价
			$item['jiajiachengben'] = $item['yuanshichengbenjia'];
			if (!in_array($from_company_id, array(58,223))) {
				$jiajialv = $warehouseModel->getBillJiajiaInfo(null, 1, $item['goods_id'],$bill_id);
				if ($jiajialv) {
					$item['jiajiachengben'] = number_format($item['yuanshichengbenjia'] * (1 + $jiajialv/100),2);
				}
			}
		}
	}
}

?>