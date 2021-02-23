<?php
/**
 * author:liulinyan
 * date: 2015-11-04
 * filename:618.php
 * used: 618自动抓单入库boss
*/
define('ROOT_PATH',str_replace('\\','/',realpath(dirname(__FILE__))));//定义目录

define('TB_API_URL', 'http://114.55.12.230');

define('API_AUTH_KEYS',json_encode(array('taobaoapi'=> ':AoN8Rt9l5103s')));
include(ROOT_PATH.'/Util.class.php');

//引入淘宝api文件
include_once(ROOT_PATH.'/taobaoapi.php');
//引入订单操作类文件
include_once(API_ROOT.'PdoModel.php');
include_once(API_ROOT.'OrderNewClassModel.php');
$orderModel = new OrderNewClassModel();
//设置各个开关
$open_trade_status = true;    //是否开启淘宝订单状态过滤
$open_check = true;          //是否开启淘宝订单抓取过滤
//是否当个录入的标识
$issingle = 0;

$allids = $orderModel->gettaobaoids();
//定义所有淘宝id的数组
if(empty($allids))
{
	exit('休息下吧,没有需要发货的淘宝订单了');
}
//$allids = array('26377862144994124','30586749879136173','9893198995812253');
//开始循环订单拿取订单的明细
foreach($allids as $obj)
{
	//判断订单是否已经抓取过
	$taobaoid = $obj['order_sn'];
	$jfb = $obj['jfb'];
	$result = $orderModel->check($taobaoid);
	if($result && $open_check){
		echo '订单'.$taobaoid.'已经抓取过了,不需要重复抓取,<br/>';
		continue;	
	}
	//获取订单信息
	$orderinfo = $apiModel->get_order_info($taobaoid);
	file_put_contents(dirname(__FILE__).'/1.log', $taobaoid.':'. json_encode($orderinfo).PHP_EOL, FILE_APPEND);
	/*计算优惠的金额*/
	$promotion_details = $orderinfo->trade->promotion_details;
	$promotion_detial = $promotion_details->promotion_detail;
	$tmobprice = calcprice($promotion_detial);
	//淘宝要承担的
	$tbobprice = $tmobprice['taobao'];
	//红包
	$hb = $orderinfo->trade->coupon_fee ? $orderinfo->trade->coupon_fee : 0;
	//发票金额 = 商品总金额-总优惠-红包-集分宝
	$fp_price = bcsub($orderinfo->trade->price,$tmobprice['total'],2);
	$hb_price = 0;
	if($hb > 0){
		$hb_price =  bcdiv($hb,100,2);
	}
	$fp_price = bcsub($fp_price,$hb_price,2);
	$fp_price = bcsub($fp_price,$jfb,2);
	//订单金额 =  商品总金额-总优惠 + (共同承担的优惠/2) +淘宝全力承担的
	$order_amount = bcsub(floatval($orderinfo->trade->price),floatval($tmobprice['total']),2);
	$pt_yh = bcdiv($tmobprice['yiqi'],2,2);
	$order_amount = bcadd($order_amount,$pt_yh,2);
	$order_amount = bcadd($order_amount,$tmobprice['taobao'],2);
	
	//买家可能使用积分  暂定100个积分一块钱
	if($orderinfo->trade->point_fee > 0)
	{
		$jfmoney = bcdiv($orderinfo->trade->point_fee,100,2);
	}else{
		$jfmoney =0;
	}
	$order_amount = bcadd($order_amount,$jfmoney,2);
	
	//订单商品明细
	$goodsarr = $orderinfo->trade->orders->order;
	$goodsarr = $goodsarr[0];
	if(isset($orderinfo->code))
	{
		$error = '淘宝订单不存在呢,看是否需要记录下来';
		$data['out_order_sn'] = $taobaoid;
		$data['reason'] = '没有找到淘宝订单:'.$taobaoid;
		$orderModel->recordInsert($data);
		if($issingle>0)
		{
			echo $data['reason'];
		}
		continue;
	}
	//如果不是等待卖家发货状态  并且开关已经开启
	if($open_trade_status)
	{
		if(trim(@$orderinfo->trade->status) != "WAIT_SELLER_SEND_GOODS")
		{
			$data['out_order_sn'] = $taobaoid;
			$data['reason'] = '淘宝订单:'.$taobaoid.'不是等待卖家发货状态 我不抓取';
			$data['order_status'] = $orderinfo->trade->status;
			$orderModel->recordInsert($data);
			if($issingle>0)
			{
				echo $data['reason'];
			}
			continue;
		}
	}
	//买家备注
	$buyer_message = empty($orderinfo->trade->buyer_message)?'无':$orderinfo->trade->buyer_message;
	//拿取客服备注信息
	$remark = isset($orderinfo->trade->seller_memo)?$orderinfo->trade->seller_memo:'无';
	
	
	
	//根据订单信息组装城市id信息
	//省
	$region_province = str_replace('省','',trim($orderinfo->trade->receiver_state));
	//市
	$region_city = str_replace('市','',trim($orderinfo->trade->receiver_city));
	//区
	$region_regional = trim($orderinfo->trade->receiver_district);
	$regiondata = $orderModel->getcityids($region_province,$region_city,$region_regional);
	
	
	//淘宝订单商品信息
	$goodsarr = $orderinfo->trade->orders->order;
	$goodsarr = $goodsarr[0];
	$tuikuan = array();
	if(is_array($goodsarr)){
		foreach($goodsarr as $goodsinfo)
		{
			$flag = $goodsinfo->title;
			if($goodsinfo->refund_status !='NO_REFUND')
			{
				array_push($tuikuan,$flag);
			}
		}
	}else{
		$flag = $goodsarr->title;
		if($goodsarr->refund_status !='NO_REFUND')
		{
			array_push($tuikuan,$flag);
		}	
	}
	/**********  检查是否有退款的 start   **********/
		if(!empty($tuikuan))
		{
			//外部导单成功与否记录日志
			$record['out_order_sn']= $taobaoid;
			$tuikuan_title = implode(',',$tuikuan);
			$record['reason'] = '淘宝订单:'.$taobaoid.'中的商品'.$tuikuan_title.'发生了退款操作';
			$record['order_status'] = $orderinfo->trade->status;
			$orderModel->recordInsert($record);
			if($issingle>0)
			{
				echo $record['reason'];
			}
			continue;
		}
	/**********  检查是否有退款的 end   **********/
	
	
	/**********  开始入库操作了start   **********/
	$orderModel->mysqli->autocommit(false);// 开始事务
	$stepflag = true;
	$reason = '';
	$order_sn = date('Ymd').mt_rand(3,9).str_pad(mt_rand(1, 99999),5,'1',STR_PAD_LEFT);
	//如果上面都满足条件 从这里开始一步步入库   优化有默认的可以不填写的先去掉 减少资源浪费
	/********************    base_order_info表    ********************/
	$baseinfo = step_order($order_sn,$buyer_message,$orderinfo);
	$res1 = $orderModel->autoinsert('base_order_info',$baseinfo);
	$id = $res1;  //自动生成的订单id
	if($res1)
	{
		echo '录入base_order_info成功<br/>';
	}else{
		$stepflag = false;
		$reason .= '录入base_order_info失败<br/>';
		echo '录入base_order_info失败<br/>';
	}
	//$id = $res1;  //自动生成的订单id
	
	
	
	
	//淘宝订单商品
	$rule_index = 0;
	//统计商品的总优惠金额
	$goodsallyh =0;
	
	//买就送的标识
	$buygift = false;
	//订单是否现货标识
	$orderisxh = 1;
	//商品总额
	$goodstotalprice = 0;
	//优惠券总额
	$yhqtotalprice = 0;
	$invoice_content = '';
	//只适合一条的哟
	$goodsinfo = $goodsarr;
	$invoice_content .= $goodsinfo->title.'<br/>';
	$favorable_price = bcsub($goodsinfo->price,$order_amount,2);
	//商品sn
	$goods_sn = empty($goodsinfo->outer_sku_id) ? $goodsinfo->outer_iid: $goodsinfo->outer_sku_id;
	$goods_sn  = empty($goods_sn) ? 'KLSW033199' : $goods_sn;
	//如果商品数量出现多个就进行价格拆分
	//$g_num = $goodsinfo->num;
	$g_num = 2;
	
	//第一步根据商品款号拿出后面需要用到的产品线和款式分类id
	$styleiinfo = array('product_type'=>'','cat_type'=>'');
	if(!empty($goods_sn))
	{
		$styleinfo = $orderModel->getstyleinfo($goods_sn);
		if(!empty($styleinfo))
		{
			$styleiinfo['product_type'] = $styleinfo['product_type'];
			$styleiinfo['cat_type'] = $styleinfo['style_type'];
		}
	}
	/********************    app_order_details表    ********************/
	//订单号
	$details['order_id'] = $id;
	//货号
	$details['goods_id'] = 0;
	//款号
	$details['goods_sn'] = $goods_sn;
	//原始款号(其实就是原始货号)
	$details['ext_goods_sn'] = 0;
	//商品名称
	$details['goods_name'] = $goodsinfo->title;
	//商品价格
	$details['goods_price'] = 0;
	//优惠价格:正数代表减钱，负数代表加钱
	$details['favorable_price'] = 0;
	//商品个数
	$details['goods_count'] = 1;              //默认为1,因为如果是多个 我也要进行拆分
	//添加时间
	$details['create_time'] = date("Y-m-d H:i:s");
	//修改时间
	$details['modify_time'] = date("Y-m-d H:i:s");
	//创建人
	$details['create_user'] = '魏少华';             //备注里面的客服
	//
	$details['details_status'] = '';
	//1未发货2已发货3收货确认4允许发货5已到店
	$details['send_good_status'] = 1;        //默认为1
	//布产状态:1初始化2待分配3已分配4生产中7部分出厂9已出厂10已取消
	$details['buchan_status'] = 1;           //默认为1
	//是否是现货：1现货 0期货
	$details['is_stock_goods'] = 1;          //默认为1 如果查不到信息就为期货0
	//退货产品 0未退货1已退货
	$details['is_return'] = 0;               //默认为0
	//备注
	$details['details_remark'] = $goodsinfo->sku_properties_name;          //淘宝上面的商品标签
	//镶嵌要求
	$details['xiangqian'] = '成品';
	//优惠审核状态1：保存；2：提交申请；3：审核通过；4：审核驳回
	$details['favorable_status'] = 3;		//默认为3
	//款式分类
	$details['cat_type'] = $styleiinfo['cat_type'];
	//产品线
	$details['product_type'] = $styleiinfo['product_type'];
	//是否赠品0否 1.是
	$details['is_zp']=0;    		//默认为0,当添加赠品的时候就是赠品
	//是否销账,2.是  1否
	$details['is_finance'] = 2;     //默认是
	$details['cart'] = '';
	$details['cut'] = '';
	$details['clarity'] = '';
	$details['color'] = '';
	$details['cert'] = '';
	$details['zhengshuhao'] = '';
	$details['caizhi'] = '';
	$details['jinse'] = '';
	$details['jinzhong'] = '';
	$details['zhiquan'] = '';
	$details['xiangkou'] = '';
	$details['chengbenjia'] = '';
	//根据商品的数量进行循环拼装数据
	//商品价格
	$price = $goodsinfo->price;

	//如果商品数量大于1个
	//循环商品个数，直接入库
	for($i=0;$i<$g_num;$i++)
	{
		$details['goods_price'] = $price;
		$details['favorable_price'] = $favorable_price;
		//入库2
		$details = array_filter($details);
		$res2 = $orderModel->autoinsert('app_order_details',$details);
		if($res2)
		{
			echo '录入app_order_details成功<br/>';
		}else{
			$stepflag = false;
			$reason .= "录入app_order_details失败<br/>";
			echo '录入app_order_details失败<br/>';
		}
	}
	
	/********************    追加一个赠品             ********************/
	//赠品入库订单商品表	
	$order_gift=array(
		'order_id'=>$id,
		'goods_id'=>0,
		'goods_sn'=>'KLSX033076',
		'ext_goods_sn'=>0,
		'goods_name'=>'足金手链',
		'goods_price'=>0,
		'favorable_price'=>0,
		'goods_count'=>1,
		'create_time'=>date("Y-m-d H:i:s"),
		'modify_time'=>date("Y-m-d H:i:s"),
		'create_user'=> '魏少华',
		'details_status'=>0,
		'send_good_status'=>1,
		'buchan_status'=>1,
		'is_stock_goods'=>1,//赠品默认为现货
		'is_return'=>0,
		'details_remark'=>'赠送',
		'xiangqian'=>0,
		'favorable_status'=>1,
		'cat_type'=>7,
		'product_type'=>7,
		'is_zp'=>1,
		'is_finance'=>2
	);
	//$orderModel->autoinsert('app_order_details',$order_gift);
	//echo $reason .= "录入赠品成功<br/>";
	/********************    app_order_account表    ********************/
	
	
	
	
	
	
	//订单ID
	$account['order_id'] = $id;
	//订单总金额
	$account['order_amount'] = $order_amount;     //淘宝上面的实付金额
	//已付      把录单和款项分开走,所以这里已付必须为0
	$account['money_paid'] = 0;
	//未付      订单总金额-已付=未付
	$account['money_unpaid'] = $order_amount;
	//商品实际退款
	$account['goods_return_price'] = 0;                 //默认为0
	//实退金额
	$account['real_return_price'] = 0;                  //默认为0
	//快递费
	$account['shipping_fee'] = $orderinfo->trade->post_fee;    //双十一默认免费
	//商品总额
	$account['goods_amount'] = $orderinfo->trade->price;        //bcadd($totalmoney, $goodsallyh,2);
	//订单优惠券金额
	$account['coupon_price'] = 0;         //把所有分摊的都加上
	//订单商品优惠金额
	$account['favorable_price']= bcsub($orderinfo->trade->price,$order_amount,2);           //循环商品的时候把这个累加上填写进来的
	$account['card_fee'] = 0;
	$account['pack_fee'] = 0;
	$account['pay_fee'] = 0;
	$account['insure_fee'] = 0;
	//入库3
	$res3 = $orderModel->autoinsert('app_order_account',$account);
	if($res3)
	{
		echo '录入app_order_account成功<br/>';
	}else{
		$stepflag = false;
		$reason .= "录入app_order_account失败<br/>";
		echo '录入app_order_account失败<br/>';
	}
	
	
	/********************    app_order_address表    ********************/
	//订单id
	$address['order_id'] = $id;
	//收货人的姓名
	$address['consignee'] = trim($orderinfo->trade->receiver_name);
	//配送方式 							
	$address['distribution_type']= 2; // 默认为总公司到客户 2  门店是1
	//快递公司id
	$address['express_id'] = ($orderinfo->trade->payment >= 500) ? 4: 19;   //默认顺丰快递
	//快递号
	$address['freight_no'] = 0; 
	//国家id
	$address['country_id'] = 1;   //默认为1 中国
	//省
	$province = trim($orderinfo->trade->receiver_state);
	//市
	$city = trim($orderinfo->trade->receiver_city);
	//区
	$regional = trim($orderinfo->trade->receiver_district);
	$address['province_id']=$regiondata['province_id'];
	$address['city_id']= $regiondata['city_id'];         
	$address['regional_id']=$regiondata['regional_id'];
	//体验店类型
	$address['shop_type'] = 2;             //默认为2 淘宝销售部
	//体验店名称
	$address['shop_name'] = '淘宝销售部';    //默认为销售部
	//详细地址
	$address['address'] = $province.$city.$regional.trim($orderinfo->trade->receiver_address);
	//tel
	
	$mobile = trim($orderinfo->trade->receiver_mobile);
	//$phone = trim($orderinfo->trade->receiver_phone);
	$address['tel'] = $mobile;
	//email
	$address['email'] = @$orderinfo->trade->buyer_email;
	//收货人的邮编
	$address['zipcode'] = isset($orderinfo->trade->receiver_zip)?trim($orderinfo->trade->receiver_zip):'';
	//入库6
	$address = array_filter($address);
	$res6 = $orderModel->autoinsert('app_order_address',$address);
	if($res6)
	{
		echo '录入app_order_address成功<br/>';
	}else{
		$stepflag = false;
		$reason .='录入app_order_address失败<br/>';
		echo '录入app_order_address失败<br/>';
	}
	
	/********************    app_order_invoice表    ********************/
	//订单id
	$invoice['order_id'] = $id;
	//是否需要发票 1:需要 0：不需要  
	$invoice['is_invoice']= 1;     //默认需要
	//发票抬头
	$invoice['invoice_title'] = '个人'; //默认填写个人
	//发票内容
	$invoice['invoice_content'] = $invoice_content;   //订单所有的商品名称 换行叠加
	//1未开发票2已开发票3发票作废
	$invoice['invoice_status'] = 1;       //默认为未开发票
	//发票金额
	$invoice['invoice_amount'] = $fp_price;         //trim($orderinfo->trade->payment);
	//发票邮寄地址
	$invoice['invoice_address'] = $address['address'];  //默认和收货地址一样
	//创建时间
	$invoice['create_time'] = date("Y-m-d H:i:s");     //默认当前时间
	//入库4
	$invoice = array_filter($invoice);
	$res4 = $orderModel->autoinsert('app_order_invoice',$invoice);
	if($res4)
	{
		echo '录入app_order_invoice成功<br/>';
	}else{
		$stepflag = false;
		$reason .='录入app_order_invoice失败<br/>';
		echo '录入app_order_invoice失败<br/>';
	}
	
	/********************    rel_out_order表    ********************/
	$relout['order_id'] = $id;
	//外部订单编号 
	$relout['out_order_sn']= $taobaoid;
	//入库5
	$relout = array_filter($relout);
	$res5 = $orderModel->autoinsert('rel_out_order',$relout);
	if($res5)
	{
		echo '录入rel_out_order成功<br/>';
	}else{
		$stepflag = false;
		$reason .='录入rel_out_order失败<br/>';
		echo '录入rel_out_order失败<br/>';
	}
	
	$createname = $baseinfo['create_user'];
	
	/********************    s11_order_info表    ********************/
	//外部订单号信息
	$s11info['out_order_sn'] = $taobaoid;
	//BDD生成订单的主键id
	$s11info['order_id'] = $id;
	//订单编号
	$s11info['order_sn'] = $order_sn;
	//是否成功 0失败1成功
	$s11info['res'] = 1;
	//出错原因
	$s11info['reason'] = '成功';
	$s11info['order_status'] = $orderinfo->trade->status;
	$s11info['add_time'] = date('Y-m-d H:i:s');
	//入库6
	//$s11info = array_filter($s11info);
	//print_r($s11info);
	$res6 = $orderModel->autoinsert('s11_order_info',$s11info);
	if($res6)
	{
		echo '录入s11_order_info成功<br/>';
	}else{
		$stepflag = false;
		$reason .='录入s11_order_info失败<br/>';
		echo '录入s11_order_info失败<br/>';
	}
	
	/********************    app_order_action表    ********************/
	//操作日志
	$ation['order_status'] = 2;
	$ation['order_id'] = $id;
	$ation['shipping_status'] = 1;
	$ation['pay_status'] = 1;
	$ation['create_user'] = '魏少华';
	$ation['create_time'] = date("Y-m-d H:i:s");
	$ation['remark'] = "生成订单外部订单号:[".$taobaoid."]成功";
	$res8 = $orderModel->autoinsert('app_order_action',$ation);
	if($res8)
	{
		echo 'app_order_action操作日志添加成功<br/>';
	}else{
		$stepflag = false;
		$reason .= "app_order_action操作日志添加失败<br/>";
		echo 'app_order_action操作日志添加失败<br/>';
	}
	
	//上面全部操作结束  对错误进行判断
	if(!$stepflag)
	{
		echo '信息已经回滚了<br/>';
		$reason .= '信息已经回滚了<br/>';
		$orderModel->mysqli->rollback();
	}
	$orderModel->mysqli->autocommit(TRUE);
	//再次做判断,录入错误信息和操作日志
	if(!$stepflag)
	{
		//外部导单成功与否记录日志
		$order11log['out_order_sn']= $taobaoid;
		$order11log['order_id'] = $id;
		$order11log['order_sn']= $order_sn;
		$order11log['res'] = '0';
		$order11log['reason'] =$reason;
		$order11log['order_status'] = $orderinfo->trade->status;
		$order11log['add_time'] = date('Y-m-d H:i:s');
		$orderModel->autoinsert('s11_order_info',$order11log);
	}
	if($stepflag)
	{
		echo '淘宝订单'.$taobaoid.'抓单ok对应的BDD订单是'.$order_sn.'<hr/>';
	}
}


/********************    base_order_info表    ********************/
function step_order($order_sn,$buyer_message,$orderinfo)
{
	//订单编号
	$baseinfo['order_sn'] = $order_sn;
	//老订单号
	$baseinfo['old_order_id'] = 0;    //默认不填写为0
	//预约号
	$baseinfo['bespoke_id '] = 0;     //默认不填写为0
	//会员id
	$baseinfo['user_id'] = 0;         //默认用户为0 后期一次性清洗加快订单录入速度
	//名字
	$baseinfo['consignee'] = trim($orderinfo->trade->receiver_name);   //收货人的名字
	//手机号
	$baseinfo['mobile'] = trim($orderinfo->trade->receiver_mobile);
	//订单审核状态1无效（默认待审核）2已审核3取消4关闭
	$baseinfo['order_status'] = 2;       //默认为已经审核
	//支付状态:1未付款2部分付款3已付款4财务备案
	$baseinfo['order_pay_status'] = 1;   //默认为1
	//支付类型;0:默认，1:展厅订购,2:货到付款
	$baseinfo['order_pay_type'] = 24;     //默认为淘宝代收款
	//配货状态(1,未配货;2允许配货;3,配货中;4,配货缺货;5,已配货;6,无效)
	$baseinfo['delivery_status'] = 1;    //默认为1
	//(发货状态1未发货2已发货3收货确认4允许发货5已到店)
	$baseinfo['send_good_status'] = 1;   //默认为1
	//布产状态:1未操作,2已布产,3生产中,4已出厂,5不需布产
	$baseinfo['buchan_status'] = 1;      //默认为1
	//客户来源id
	$baseinfo['customer_source_id'] = 544; //默认为淘宝B店
	//订单部门
	$baseinfo['department_id']= 2;         //默认为淘宝销售部
	//制单时间
	$baseinfo['create_time'] = date("Y-m-d H:i:s");
	//制单人
	$baseinfo['create_user'] = '魏少华';  //默认辛娟
	//增加审核时间和审核人
	$baseinfo['check_time']= date("Y-m-d H:i:s");
	$baseinfo['check_user'] = '魏少华';
	//修改时间
	$baseinfo['modify_time'] = date("Y-m-d H:i:s");
	//备注信息
	$seller_msg = (array)($orderinfo->trade->seller_memo);
	$baseinfo['order_remark'] = '买家备注为:'.$buyer_message.'<br/>卖家备注为:'.(!empty($seller_msg) ?  $orderinfo->trade->seller_memo : '');
	//录单来源
	$baseinfo['referer'] = '618抓单';
	//订单状态0有效1删除
	$baseinfo['is_delete']=0;             //默认为0
	//申请关闭:0=未申请，1=申请关闭 
	$baseinfo['apply_close'] = 0;
	//是否是现货：1现货 0定制
	$baseinfo['is_xianhuo'] =  1;         //默认为1  如果没有匹配到任何一个商品再把这个修改为0
	//是否打印提货单 0否 1是
	$baseinfo['is_print_tihuo'] = 0;      //默认为0
	//订单生效时间(确定布产) 
	$baseinfo['effect_date'] = date("Y-m-d H:i:s");
	//订单是否是赠品订单 1是0不是
	$baseinfo['is_zp'] = 0;              //默认为0
	//1未操作2正在退款
	$baseinfo['apply_return']=1;        //默认为1
	//定义一个变量用来标识每个步骤是否ok
	echo '第一步录入订单基本信息表<br/>:';
	return $baseinfo;
}


//算出淘宝要承担的费用
function calcprice($promotion_detail){
	$tb_ob = array('天猫购物券黄金珠宝专用','天猫购物券服饰大额专用','天猫购物券珠宝饰品专用');
	$pt_ob = array('618购物券');
	$zj_ob = array('618狂欢价','年中 满500减60','618-200元券');
	$tb_obprice = 0;    //淘宝的
	$yiqi_obprice = 0; //一起的
	$zj_obprice = 0;  //自己的
	$totl = 0;
	if(empty($promotion_detail)){
		$allyh['total'] = 0;
		$allyh['taobao'] = 0;
		return $allyh;
	}

	//
	foreach($promotion_detail as $obj)
	{
		$promotion_name = trim($obj->promotion_name);
		$discount_fee =   $obj->discount_fee;
		//淘宝全部承担的,发票金额要减去
		if(in_array($promotion_name,$tb_ob)){
			$tb_obprice = bcadd($tb_obprice,$discount_fee,2);
		}
		//商家和淘宝分摊的
		if(in_array($promotion_name,$pt_ob)){
			$yiqi_obprice = bcadd($yiqi_obprice,$discount_fee,2);	
		}
		if(in_array($promotion_name,$zj_ob)){
			$zj_obprice = bcadd($zj_obprice,$discount_fee,2);
		}
		$totl = bcadd($totl,$discount_fee,2);
	}
	$allyh['total'] = $totl;
	$allyh['taobao'] = $tb_obprice;
	$allyh['zj'] = $zj_obprice;
	$allyh['yiqi'] = $yiqi_obprice;
	return $allyh;
}
?>
