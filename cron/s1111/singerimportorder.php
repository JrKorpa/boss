<?php
header('Content-type: text/html; charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
error_reporting(0);
//引入淘宝api文件
define('ROOT_PATH',str_replace('\\','/',realpath(dirname(__FILE__))));//定义目录
define('API_ROOT',ROOT_PATH.'/lib/');
//目前只处理淘宝的   为了扩展预留为数组
$from_arr = array(
	2 => array("ad_name"=> "淘宝B店", "api_path" =>"taobaoOrderApi")
);
//仓库信息
$warehouse_arr=array(
	2=>'线上低值库',
	79=>'深圳珍珠库',
	96=>'总公司后库',
	184=>'黄金网络库',
	386=>'彩宝库',
	482=>'淘宝黄金',
	484=>'淘宝素金',
	486=>'线上钻饰库',
	516=>'物控库',
	672=>'轻奢库',
	673=>'彩钻库'
);
$warehouseid = implode("','",array_keys($warehouse_arr));
//快递数组
$shipping_list = array(
	"4" => "SF",
	"9" => "EMS",
	"12" => "YTO",
	"14" => "STO",
	"19" => "ZTO"
);
$from_type = 2;  //默认来自淘宝B店
$from_ad = "000200230544";
$apiname = $from_arr[$from_type]["api_path"];
$file_path = API_ROOT.$apiname.'/index.php';
//引入接口文件
require_once($file_path);
//实例化淘宝类
$apiModel = new $apiname();

//引入订单操作类文件
include_once(API_ROOT.'PdoModel.php');
include_once(API_ROOT.'OrderClassModel.php');
//引入规则文件
include_once(ROOT_PATH.'/include/newrule.php');
//引入赠品配置文件
include(ROOT_PATH.'/include/giftconfig.php');
//引入赠品操作文件
include(ROOT_PATH.'/include/getgift.php');
//设置各个开关
$open_trade_status = true;    //是否开启淘宝订单状态过滤
$open_check = true;           //是否开启淘宝订单抓取过滤
$open_ys_status = false;      //是否开启预售订单


$taobaoid = isset($_REQUEST['taobaoid'])?$_REQUEST['taobaoid']:'';
if(empty($taobaoid))
{
	exit('请输入需要录入订单的淘宝id');
}

//内部推广的用户备注
$company_people = array('黎慧慧','温海花','冯峰','刘桥林','姚旋','向宙');
//小黄鸭转运珠 备注的赠品
$xhygift = array(
	'KLSX028387','KLSX028388','KLSX028386','KLSW029179','KLSW029180','KLSW029181','KLSW029182'
);
$company_message = array('八周年生日快乐','双11预定','双十一预定','提前抢购双11');
//新品银质吊坠 压缩版 KLPW029315 立体版 KLPW029314  默认赠送银链 
$ys_goodssn = array('KLPW029315','KLPW029314');
$orderModel = new OrderClassModel();
$result = $orderModel->check($taobaoid);
//通过开关来判断
if($result && $open_check)
{
	echo '订单'.$taobaoid.'已经抓取过了,不需要重复抓取,<br/>';
	continue;
}
echo '订单id:'.$taobaoid.'开始抓取<br/>';
//开始通过淘宝订单id  获取淘宝订单信息
$orderinfo = $apiModel->getorderinfo($taobaoid);
if(trim($orderinfo -> code))
{
	$error = '淘宝订单不存在呢,看是否需要记录下来';
	$data['out_order_sn'] = $taobaoid;
	$data['reason'] = '没有找到淘宝订单:'.$taobaoid;
	$orderModel->recordInsert($data);
	echo $data['reason'].'<br/>';
	continue;
}

//如果不是等待卖家发货状态  并且开关已经开启
if($open_trade_status)
{
	//如果开启了预售开关
	if($open_ys_status)
	{
		$astatus = array('WAIT_SELLER_SEND_GOODS','WAIT_BUYER_PAY');
		if(!in_array(trim(@$orderinfo->trade->status),$astatus))
		{
			$data['out_order_sn'] = $taobaoid;
			$data['reason'] = '淘宝预售订单:'.$taobaoid.'状态出错 我不抓取';
			$data['order_status'] = $orderinfo->trade->status;
			$orderModel->recordInsert($data);
			echo $data['reason'].'<br/>';
			continue;
		}
	}elseif(trim(@$orderinfo->trade->status) != "WAIT_SELLER_SEND_GOODS")
	{
		$data['out_order_sn'] = $taobaoid;
		$data['reason'] = '淘宝订单:'.$taobaoid.'不是等待卖家发货状态 我不抓取';
		$data['order_status'] = $orderinfo->trade->status;
		$orderModel->recordInsert($data);
		echo $data['reason'].'<br/>';
		continue;
	}
}

//买家备注
$buyer_message = empty($orderinfo->trade->buyer_message)?'':$orderinfo->trade->buyer_message;

if(in_array($buyer_message,$company_message))
{
	$data['out_order_sn'] = $taobaoid;
	$data['reason'] = '淘宝订单:'.$taobaoid.'是市场推广的录单,我不抓取';
	$data['order_status'] = $orderinfo->trade->status;
	$orderModel->recordInsert($data);
	echo $data['reason'].'<br/>';
	continue;
}


//拿取客服备注信息
$remark = isset($orderinfo->trade->seller_memo)?$orderinfo->trade->seller_memo:'';

if(in_array($remark,$company_people))
{
	$data['out_order_sn'] = $taobaoid;
	$data['reason'] = '淘宝订单:'.$taobaoid.'是用户'.$remark.'推广的录单,我不抓取';
	$data['ispreorder'] = 0;
	$data['order_status'] = $orderinfo->trade->status;
	$orderModel->recordInsert($data);
	echo $data['reason'].'<br/>';
	continue;
}

//为了和顾客写贺卡里面用中文逗号,所以不做中文逗号转换
//update by liulinyan 20151012
//$remark = str_replace('，',',',$remark);

//如果没有填写备注的情况下,自动默认(针对静默订单)  不包括群钻
if($remark=="")
{
	$remark="辛娟,无,无,KLBZ";
}

//设置备注标识符
$bzflag = true;
//客户备注里面一个,KLBZ,代表一个商品结束 这个必须要统一,//有多少个商品就有多少个,KL(BDD)这个可以变
$remark = strip_tags($remark);
$remark_list = explode(',KLBZ',$remark);
$remark_list = array_filter($remark_list);
//备注新修改
/*
$newbzlist = array();
foreach($remark_list as $rv)
{
	$newarr = implode(',',$rv);
	if(count($newarr)==3)
	{
		$newbzlist['three'] = $rv;
	}elseif(count($newarr==5))
	{
		//单钻戒指
		$newbzlist['five'] = $rv;
	}elseif(count($newarr==11))
	{
		//群钻戒指的
		$newbzlist['eleven']=$rv;
	}elseif(count($newarr == 9))
	{
		//群钻非戒指的
		$newbzlist['nine']=$rv;
	}
}*/




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

//设置临时变量
$errorinfo = '';
$rules = array();
$i=0;
//发票内容
$invoice_content = '';

//定义一个数组用来装退款的商品
$tuikuan = array();

$yshou = false;  //预售单标识
foreach($goodsarr as $goodsinfo)
{
	/*eg:
	$flag = $goodsinfo->title.'/dz';
	$smarkname = '白 18K金 98分 I-J/SI';
	$remark = '刘桥林,10#,lql,无,无';
	*/
	$flag = $goodsinfo->title;
	if(strpos($flag,'预售')!=false)
	{
		$yshou = true;
	}
	
	$invoice_content .= $flag.'<br/>';
	
	$smarkname = $goodsinfo->sku_properties_name;
	if($remark == "辛娟,无,无")
	{
		$remark_list[$i] = '辛娟,无,无';
	}
	$remark = $remark_list[$i];
	$i++;
	
	
	$tmpgoodssn = $goodsinfo->outer_sku_id =="" ? $goodsinfo->outer_iid:$goodsinfo->outer_sku_id;
	if(in_array($tmpgoodssn,$ys_goodssn))
	{
		$where = array();
	}else{
		$where = makeruledata($flag,$smarkname,$remark);
	}
	
	if(isset($where['error']) && !empty($where['error']))
	{
		//只要这里出现了错误 就整条线都不执行了
		$errorinfo = $where['error'];
		break;
	}else{
		array_push($rules,$where);
	}
	
	//退款状态
	if($goodsinfo->refund_status !='NO_REFUND')
	{
		array_push($tuikuan,$flag);
	}
	
}

if($yshou && $trim(@$orderinfo->trade->status) != "WAIT_BUYER_PAY")
{
	//外部导单成功与否记录日志
	$record['out_order_sn']= $taobaoid;
	$record['reason'] = '淘宝订单:'.$taobaoid.'是预售单,但不是等待买家付款状态';
	$record['order_status'] = $orderinfo->trade->status;
	$orderModel->recordInsert($record);
	echo $record['reason'].'<br/>';
	continue;
}
	
if(!empty($errorinfo))
{
	//外部导单成功与否记录日志
	$record['out_order_sn']= $taobaoid;
	$record['reason'] = '淘宝订单:'.$taobaoid.$errorinfo;
	$record['order_status'] = $orderinfo->trade->status;
	$orderModel->recordInsert($record);
	echo $record['reason'].'<br/>';
	continue;
}

if(!empty($tuikuan))
{
	//外部导单成功与否记录日志
	$record['out_order_sn']= $taobaoid;
	$tuikuan_title = implode(',',$tuikuan);
	$record['reason'] = '淘宝订单:'.$taobaoid.'中的商品'.$tuikuan_title.'发生了退款操作';
	$record['order_status'] = $orderinfo->trade->status;
	$orderModel->recordInsert($record);
	echo $record['reason'].'<br/>';
	continue;
}


//循环判断备注是否满足条件
foreach($remark_list as $goods_remark)
{
	if(empty($goods_remark))
	{
		continue;
	}	
	$khxm_arr = explode(',',$goods_remark);
	$khxm_arr = array_filter($khxm_arr);
	$khxm = $khxm_arr[0];  //获取名称
	if(strlen($khxm)>12)
	{
		$data['out_order_sn'] = $taobaoid;
		$data['reason'] = '淘宝订单:'.$taobaoid.'客服备注信息不正确或者客服姓名太长我不抓取';
		$data['order_status'] = $orderinfo->trade->status;
		$orderModel->recordInsert($data);
		$bzflag = false;
		echo $data['reason'].'<br/>';
		break;
	}
	if(count($khxm_arr)<3)
	{
		//print_r($khxm_arr);
		$data['out_order_sn'] = $taobaoid;
		$data['reason'] = '淘宝订单:'.$taobaoid.'客服备注信息不正确,没有按照规则划分';
		$data['order_status'] = $orderinfo->trade->status;
		$orderModel->recordInsert($data);
		$bzflag = false;
		echo $data['reason'].'<br/>';
		break;
	}
	
}	

//如果备注不满足条件 跳出这次循环
if(!$bzflag)
{
	continue;
}






$orderModel->mysqli->autocommit(false);// 开始事务
$stepflag = true;
$reason = '';

//创建BDD订单号
$order_sn = date('Ymd').mt_rand(3,9).str_pad(mt_rand(1, 99999),5,'1',STR_PAD_LEFT);
//如果上面都满足条件 从这里开始一步步入库   优化有默认的可以不填写的先去掉 减少资源浪费

/********************    base_order_info表    ********************/
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
$bz = explode(',',$remark_list[0]);
//$baseinfo['create_user'] = substr($bz[0],0,6);        //从订单备注里面抓取  $remark_list[0];
$baseinfo['create_user'] = isset($bz[0]) ? $bz[0]: '辛娟';  //默认辛娟
//修改时间
$baseinfo['modify_time'] = date("Y-m-d H:i:s");
//备注信息
$baseinfo['order_remark'] = '买家备注为:'.$buyer_message.'<br/>卖家备注为:'.$orderinfo->trade->seller_memo;
//录单来源
$baseinfo['referer'] = '双11抓单';
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

//淘宝订单商品
$rule_index = 0;
//统计商品的总优惠金额
$goodsallyh =0;
$orderallprice = 0;

//买就送的标识
$buygift = false;

//备注序号
//订单是否现货标识
$orderisxh = 1;
$bzindex = 0;

//商品总额
$goodstotalprice = 0;
//优惠券总额
$yhqtotalprice = 0;

foreach($goodsarr as $k=>$goodsinfo)
{
	
	//$yhqtotalprice = bcadd($yhqtotalprice,$goodsinfo->part_mjz_discount,3);
	//各个商品的备注
	$goodsbz = $remark_list[$bzindex];
	$goodsbzarr = explode(',',$goodsbz);
	//备注赠品取倒数第二个
	$gcount = count($goodsbzarr);
	$kcount = $gcount-2;
	
	
	
	//订单的总金额
	$orderallprice = bcadd($orderallprice,$goodsinfo->payment,2);
	
	
	//商品的总额
	//$goodstotalprice = bcadd($goodstotalprice,$goodsinfo->price,2);
	
	//特殊处理php对小数点不支持的情况
	$goodsallyh = bcadd($goodsallyh, $goodsinfo->discount_fee,2);
	//加上使用了优惠券的金额
	$goodsallyh = bcadd($goodsallyh, $goods->part_mjz_discount,2);
	//商品sn
	$goods_sn = $goodsinfo->outer_sku_id =="" ? $goodsinfo->outer_iid:$goodsinfo->outer_sku_id;
	//如果商品数量出现多个就进行价格拆分
	$g_num = $goodsinfo->num;
	
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
	//第二步根据商品标签和备注拿出获取商品的条件
	$where = $rules[$rule_index];
	$where['goods_sn'] = $goods_sn;
	$where['warehouseid'] = $warehouseid;
	//去掉写着无的
	$newwhere =  array();
	foreach($where as $wk=>$wv)
	{
		if($wv !='无')
		{
			$newwhere[$wk] = $wv;
		}
	}
	
	//根据拆分出来的去匹配数据
	
	//设置货品类型默认为现货 ,如果匹配不到那就是期货
	$isxianhuo=1;
	//需要取多少条
	$result = $orderModel->getgoodsinfo($newwhere,$g_num);
	$g_infos = array();
	if($result)
	{
		if($result->num_rows>0)
		 {
			 while($obj = $result->fetch_assoc())
			 {
				 array_push($g_infos,$obj);
			 }
		 }else{
			 $isxianhuo = 0;
			 $orderisxh = 0;
		 }
	}else{
		$isxianhuo = 0;
		$orderisxh = 0;
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
	$details['create_user'] = isset($goodsbzarr[0]) ? $goodsbzarr[0]: '辛娟';             //备注里面的客服
	//
	$details['details_status'] = '';
	//1未发货2已发货3收货确认4允许发货5已到店
	$details['send_good_status'] = 1;        //默认为1
	//布产状态:1初始化2待分配3已分配4生产中7部分出厂9已出厂10已取消
	$details['buchan_status'] = 1;           //默认为1
	//是否是现货：1现货 0期货
	$details['is_stock_goods'] = $isxianhuo;          //默认为1 如果查不到信息就为期货0
	//退货产品 0未退货1已退货
	$details['is_return'] = 0;               //默认为0
	//备注
	$details['details_remark'] = $goodsinfo->sku_properties_name;          //淘宝上面的商品标签
	//镶嵌要求
	$details['xiangqian'] = '无';
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
	
	
	
	//根据商品的数量进行循环拼装数据
	
	//商品价格  //php对小数点不支持这样运算 所以换了一种
	//$gprice = bcadd($goodsinfo->payment, $goodsinfo->discount_fee,2);
	//$price = bcdiv($gprice, $g_num, 2);
	$price = $goodsinfo->price;
	
	//商品的优惠等于商品优惠+优惠券的钱
	$allyouhui = bcadd($goodsinfo->discount_fee,$goodsinfo->part_mjz_discount,2);
	$favorable_price = bcdiv($allyouhui, $g_num, 2);
	
	//如果商品数量大于1个
	//循环商品个数，直接入库
	for($i=0;$i<$g_num;$i++)
	{
		//商品的总额
		$goodstotalprice = bcadd($goodstotalprice,$goodsinfo->price,2);
		if(!empty($g_infos) && isset($g_infos[$i]))
		{
			$g_data = $g_infos[$i];
			//更改里面的值
			$details['goods_id'] = $g_data['goods_id'];
			$details['ext_goods_sn'] = $g_data['goods_id'];
			$details['cart'] = $g_data['zuanshidaxiao'];
			$details['cut'] = $g_data['cut'];
			$details['clarity'] = $g_data['clarity'];
			$details['color'] = $g_data['color'];
			$details['cert'] = $g_data['cert'];
			$details['zhengshuhao'] = $g_data['zhengshuhao'];
			$details['caizhi'] = $g_data['caizhi'];
			$details['jinse'] = $g_data['jinse'];
			$details['jinzhong'] = $g_data['jinzhong'];
			$details['zhiquan'] = $g_data['zhiquan'];
			$details['buchan_status'] = 9;           //已经出厂了
			$details['xiangkou'] = $g_data['xiangkou'];
			$details['chengbenjia'] = $g_data['chengbenjia'];
		}else{
			
			$details['caizhi']='';
			$details['jinse'] ='';
			$details['cart']='';
			$details['clarity']='';
			$details['color'] ='';
			if(isset($where['jinliao']) && !empty($where['jinliao']))
			{
				$details['caizhi'] = $where['jinliao'];
			}
			if(isset($where['yanse']) && !empty($where['yanse']))
			{
				$details['jinse'] = $where['yanse'];
			}
			if(isset($where['zuanshidaxiao']) && !empty($where['zuanshidaxiao']))
			{
				$details['cart'] = $where['zuanshidaxiao'];
			}
			if(isset($where['clarity']) && !empty($where['clarity']))
			{
				$details['clarity'] = $where['clarity'];
			}
			if(isset($where['color']) && !empty($where['color']))
			{
				$details['color'] = $where['color'];
			}
			$details['zhiquan'] = isset($where['shoucun']) ? $where['shoucun']:'';
		}
		$details['kezi'] = isset($where['ziyin']) ? $where['ziyin']:'';
		$details['goods_price'] = $price;
		$details['favorable_price'] = $favorable_price;
		
		//入库2
		$details = array_filter($details);
		//处理必须要有值的
		if(!isset($details['is_stock_goods']))
		{
			$details['is_stock_goods']	= 0;
		}
		$res2 = $orderModel->autoinsert('app_order_details',$details);
		if($res2)
		{
			echo '录入app_order_details成功<br/>';
			if(isset($details['goods_id']) && $details['goods_id']>0)
			{
				//修改商品绑定订单
				$orderModel->updateorderid($res2,$details['goods_id']);
				echo '货号'.$details['goods_id'].'已经绑定订单商品自增id'.$res2.'<br/>';
			}
		}else{
			$stepflag = false;
			$reason .= "录入app_order_details失败<br/>";
			echo '录入app_order_details失败<br/>';
		}
		
		
		
		//这里是每个商品的赠品信息入库   因为不能直接插入赠品数量 所以是多条记录
		/**********          商品赠品开始          **********/
		$goodstitle = $goodsinfo->title;
		$createname = isset($goodsbzarr[0]) ? $goodsbzarr[0]: '辛娟';
		$zparr = isset($goodsbzarr[$kcount]) ? $goodsbzarr[$kcount] : '无';
		$xhydefault = true;
		if($zparr != '无')
		{
			$zparr = explode('/',$zparr);
			$zparr = array_filter($zparr);
			//如果备注里面的赠品不为空 ，则取消买就送的 赠品
			if(!empty($zparr))
			{
				$buygift = false;
				foreach($zparr as $zpv)
				{
					if(in_array($zpv,$xhygift))
					{
						//说明小黄鸭转运珠备注了赠品
						$xhydefault = false;
					}
					
					$paramesarr['goods_sn'] = $zpv;
					$paramesarr['name'] = '来自商品备注';
					$paramesarr['desc'] = '来自商品备注';
					$bz_gifts = combinedata($id,$createname,$paramesarr);
					if(!empty($bz_gifts))
					{
						/**********          备注赠品入库app_order_details表          ***********/
						$giftinfo = $bz_gifts['order_gift'];
						$res_gift = $orderModel->autoinsert('app_order_details',$giftinfo);
						if($res_gift)
						{
							echo '商品备注赠品录入app_order_details成功<br/>';
						}else{
							$stepflag = false;
							$reason .= '商品赠品录入app_order_details失败<br/>';
							echo '商品备注赠品录入app_order_details失败<br/>';
						}
						/**********          备注赠品入库rel_gift_order表          ***********/
						$relgiftinfo = $bz_gifts['rel_gift_order'];
						$res_relgift = $orderModel->autoinsert('rel_gift_order',$relgiftinfo);
						if($res_relgift)
						{
							echo '商品备注赠品录入rel_gift_order成功<br/>';
						}else{
							$stepflag = false;
							$reason .= '商品备注赠品录入rel_gift_order失败<br/>';
							echo '商品备注赠品录入rel_gift_order失败<br/>';
						}
					}
					/**********          商品赠品结束          **********/
				}
			}
		}
		//如果出现材质，并且是出现了k就拿取18k对应的赠品，否则拿取pt950
		if(isset($where['caizhi']) && strpos($where['caizhi'],'K'))
		{
			$giftcaizhi = '18K';
		}else{
			$giftcaizhi = '';	
		}
		$goods_gifts = getgiftinfo($id,$goodstitle,$createname,$goods_sn,$giftcaizhi);
		if($xhydefault && !empty($goods_gifts))
		{
			/**********          赠品入库app_order_details表          ***********/
			$giftinfo = $goods_gifts['order_gift'];
			
			$res_gift = $orderModel->autoinsert('app_order_details',$giftinfo);
			if($res_gift)
			{
				echo '商品赠品录入app_order_details成功<br/>';
			}else{
				$stepflag = false;
				$reason .= '商品赠品录入app_order_details失败<br/>';
				echo '商品赠品录入app_order_details失败<br/>';
			}
			/**********          赠品入库rel_gift_order表          ***********/
			$relgiftinfo = $goods_gifts['rel_gift_order'];
			$res_relgift = $orderModel->autoinsert('rel_gift_order',$relgiftinfo);
			if($res_relgift)
			{
				echo '商品赠品录入rel_gift_order成功<br/>';
			}else{
				$stepflag = false;
				$reason .= '商品赠品录入rel_gift_order失败<br/>';
				echo '商品赠品录入rel_gift_order失败<br/>';
			}
		}
		/**********          商品赠品结束          **********/	
	}
	$bzindex++;
	$rule_index++;
	
}
//如果是现货单则进行修改
if($orderisxh < 1 )
{
	$orderModel->updateXianhuo(0,$id);
	echo '修改订单:'.$order_sn.',变成期货单<br/>';
}

/********************    app_order_account表    ********************/

//买家可能使用积分  暂定100个积分一块钱
if($orderinfo->trade->point_fee > 0)
{
	$jfmoney = bcdiv($orderinfo->trade->point_fee,100,2);
}else{
	$jfmoney =0;
}
$totalmoney = bcadd($orderinfo->trade->payment,$jfmoney,2);
//订单ID
$account['order_id'] = $id;
//订单总金额
$account['order_amount'] = $totalmoney;     //淘宝上面的实付金额
//已付      把录单和款项分开走,所以这里已付必须为0
$account['money_paid'] = 0;
//未付      订单总金额-已付=未付
$account['money_unpaid'] = $totalmoney;
//商品实际退款
$account['goods_return_price'] = 0;                 //默认为0
//实退金额
$account['real_return_price'] = 0;                  //默认为0
//快递费
$account['shipping_fee'] = $orderinfo->trade->post_fee;    //双十一默认免费
//商品总额
$account['goods_amount'] = $goodstotalprice;        //bcadd($totalmoney, $goodsallyh,2);
//订单优惠券金额
$account['coupon_price'] = $yhqtotalprice;          //把所有分摊的都加上
//订单商品优惠金额
$account['favorable_price']= $goodsallyh;           //循环商品的时候把这个累加上填写进来的
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
$phone = trim($orderinfo->trade->receiver_phone);
$address['tel'] = $mobile=="" ? $phone : $mobile;
//email
$address['email'] = trim($orderinfo->trade->buyer_email);
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
$invoice['invoice_amount'] = $totalmoney;         //trim($orderinfo->trade->payment);
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


/**********          订单赠品开始          **********/
$createname = $baseinfo['create_user'];
$ordergiftdata = getgiftbyprice($id,$createname,$orderinfo->trade->payment);
if($buygift && !empty($ordergiftdata))
{
	/**********          赠品入库app_order_details表          ***********/
	$ordergiftinfo = $ordergiftdata['order_gift'];
	
	$res_ordergift = $orderModel->autoinsert('app_order_details',$ordergiftinfo);
	if($res_ordergift)
	{
		echo '订单赠品录入app_order_details成功<br/>';
	}else{
		$stepflag = false;
		$reason .= '订单赠品录入app_order_details失败<br/>';
		echo '订单赠品录入app_order_details失败<br/>';
	}
	/**********          赠品入库rel_gift_order表          ***********/
	$orderrelgiftinfo = $ordergiftdata['rel_gift_order'];
	$res_orderrelgift = $orderModel->autoinsert('rel_gift_order',$orderrelgiftinfo);
	if($res_orderrelgift)
	{
		echo '订单赠品录入rel_gift_order成功<br/>';
	}else{
		$stepflag = false;
		$reason .= '赠品录入rel_gift_order失败<br/>';
		echo '订单赠品录入rel_gift_order失败<br/>';
	}
}
/**********          订单赠品结束          **********/


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
print_r($s11info);
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
$ation['create_user'] = $bz[0];
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
	echo $order11log['reason'].'<br/>';
}
if($stepflag)
{
	echo '淘宝订单'.$taobaoid.'抓单ok对应的BDD订单是'.$order_sn.'<hr/>';
}
?>