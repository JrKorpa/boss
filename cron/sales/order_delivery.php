<?php	
/*
http://192.168.0.157:8181/browse/BOSS-997
要求只导入最终状态的数据，不理中间状态，相关信息如下:
1. 迁移有效订单到boss订单表；
2. 迁移步骤1中的订单明细到boss订单明细表；
3. 对于步骤2中的订单明细商品，需要将对应商品信息迁移至boss仓库商品列表且状态为绑定订单；
4. 迁移步骤1中订单的用户信息到boss顾客信息表；
5. 迁移步骤1中对应的发票到boss发票表；
6. 迁移步骤1中订单的销售单及销售单明细到boss对应表；
7. 迁移步骤1中订单金额到boss对应的订单金额表中；
8. 迁移步骤1中订单的操作日志到boss对应的操作日志表；

1.有效订单为所有订单。针对各自都要有对应关系。

2.
ecs_order_info 
base_order_info

3.
ecs_order_goods
app_order_details

4.
ecs_order_info
app_member_info

5.
ecs_order_info
app_order_invoice

6.
jxc_order
jxc_order_goods
type=S AND status=2

7.
ecs_order_info
app_order_account

8.
ecs_order_action
app_order_action


*/

error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
define('ROOT_PATH', str_replace('order_delivery.php', '', str_replace('\\', '/', __FILE__)));
require_once(ROOT_PATH.'MysqlDB.class.php');
require_once(ROOT_PATH.'function.php');
set_time_limit(0);
date_default_timezone_set("PRC");
ini_set('memory_limit','2000M');

$conOldConf = [
    'dsn'=>"mysql:host=192.168.1.61;dbname=kela_order_part",
    'user'=>"kela_jxc",
    'password'=>"kela$%jxc",
    'charset' => 'utf8'
];
$conNewConf = [
    'dsn'=>"mysql:host=192.168.1.192;dbname=app_order",
    'user'=>"cutemans",
    'password'=>"QW@W#RSS33#E#",
    'charset' => 'utf8'
];

/*$conOldConf = [
    'dsn'=>"mysql:host=192.168.0.131;dbname=kela_order_part",
    'user'=>"root",
    'password'=>"123456",
    'charset' => 'utf8'
];
$conNewConf = [
    'dsn'=>"mysql:host=192.168.0.131;dbname=app_order",
    'user'=>"root",
    'password'=>"123456",
    'charset' => 'utf8'
];*/

/*$conNewConf_W = [
    'dsn'=>"mysql:host=192.168.0.131;dbname=warehouse_shipping",
    'user'=>"root",
    'password'=>"123456",
    'charset' => 'utf8'
];
$conNewConf_F = [
    'dsn'=>"mysql:host=192.168.0.131;dbname=front",
    'user'=>"root",
    'password'=>"123456",
    'charset' => 'utf8'
];*/
$t1 = microtime(true);

$conOld = new MysqlDB($conOldConf);
$conNew = new MysqlDB($conNewConf);
//$conNewW = new MysqlDB($conNewConf_W);
//$conNewF = new MysqlDB($conNewConf_F);

/*$data = '2015-01'; //$argv[1];
//$data = $_REQUEST['data'];
if(empty($data)){
    echo "data can't be empty. ";
}else{
    if(!preg_match('/^\d{4}-\d{2}/',$data)){
        echo "data preg faild";die;
    }
}*/

$sql="select order_id from kela_order_part.ecs_order_info where 1 ";
$sql.=" AND order_sn = '2014101657894' ";
//$sql.=" AND order_time like '$data%'";
//echo $sql;die;
$all = $conOld->getAll($sql);

if($all){
    foreach($all as $key => $o){
        $order_id = $o['order_id'];
        $sql="select * from kela_order_part.ecs_order_info where order_id = $order_id ;";
        $row = $conOld->getRow($sql);
        $sql="select * from app_order.base_order_info where id = $order_id ;";
        $exist = $conNew->getRow($sql);
        if($exist){
            continue;
        }
        //1.订单
        $order_sn = $row['order_sn'];
        $favorable_price=0;
        $unfavorable_price=0;
        $orderInfo = $row;

        $orderNewInfo = array();
        //订单基本信息
        $orderNewInfo['id'] = $orderInfo['order_id'];
        $orderNewInfo['order_sn'] = $orderInfo['order_sn'];
        $orderNewInfo['old_order_id'] = $orderInfo['order_id'];
        $orderNewInfo['bespoke_id'] = $orderInfo['bespoke_id'];//确认一致
        $orderNewInfo['old_bespoke_id'] = $orderInfo['bespoke_id'];

        //订单各类状态
        //订单状态 old=>new
        //SQL：SELECT COUNT(*) AS `行数`, `order_status` FROM `ecs_order_info` GROUP BY `order_status` ORDER BY `order_status`
        //老订单审核状态：$order_status=array(0=>'未审核',1=>'已审核',2=>'审核未通过',3=>'无效',4=>'退货',5=>'关闭');
        //新订单审核状态1无效（默认待审核）2已审核3取消4关闭
        $order_status = 99;
        if($orderInfo['order_status'] == 0){
            $order_status = 1;
        }
        if($orderInfo['order_status'] == 1){
            $order_status = 2;
        }
        if($orderInfo['order_status'] == 5){
            $order_status = 4;
        }
        if($orderInfo['order_status'] == 3){
            $order_status = 3;
        }
        $orderNewInfo['order_status'] = $order_status;//99为出错状态

        //支付状态 old=>new 
        //SQL：SELECT COUNT(*) AS `行数`, `pay_status` FROM `ecs_order_info` GROUP BY `pay_status` ORDER BY `pay_status`
        //$pay_status=array('0'=>'未付款','1'=>'付款中','2'=>'已付款','3'=>'网络付款','4'=>'支付定金','5'=>'财务备案');
        //新支付状态:1未付款2部分付款3已付款4财务备案
        $pay_status=99;
        if($orderInfo['pay_status'] == 0 || $orderInfo['pay_status'] == 1){
            $pay_status=1;
        }
        if($orderInfo['pay_status'] == 2){
            $pay_status=3;
        }
        if($orderInfo['pay_status'] == 4){
            $pay_status=2;
        }
        if($orderInfo['pay_status'] == 5){
            $pay_status=4;
        }
        $orderNewInfo['order_pay_status'] = $pay_status;//99为出错状态
        //订购方式
        //SQL:SELECT COUNT(*) AS `行数`, `pay_name` FROM `ecs_order_info` GROUP BY `pay_name` ORDER BY `pay_name`
        $pay_name = $orderInfo['pay_name'];
        $orderNewInfo['order_pay_type'] = getPayType($pay_name);

        //发货状态
        //老发货状态：$shipping_status=array(0=>'未发货',1=>'已发货',2=>'已收货',3=>'允许发货',4=>'已到店');
        //新发货状态:1未发货2已发货3收货确认4允许发货5已到店
        $send_good_status=99;
        if($orderInfo['shipping_status'] == 0){
            $send_good_status=1;
        }
        if($orderInfo['shipping_status'] == 1){
            $send_good_status=2;
        }
        if($orderInfo['shipping_status'] == 2){
            $send_good_status=3;
        }
        if($orderInfo['shipping_status'] == 3){
            $send_good_status=4;
        }
        if($orderInfo['shipping_status'] == 4){
            $send_good_status=5;
        }
        $orderNewInfo['send_good_status'] = $send_good_status;//99为出错状态

        //配送状态
        //老系统：$peihuo_status_word = array('0'=>'未配货', '1'=>'配货中', '2'=>'配货缺货', '3'=>'已配完');
        //新系统：$delivery_status = array('1'=>'未配货',  '2'=>'允许配货', '3'=>'配货中', '4'=>'配货缺货', '5'=>'已配货', '6'=>'无效');
        $peihuo_status = 99;
        if($orderInfo['peihuo_status'] == 0){
            $peihuo_status = 1;
        }
        if($orderInfo['peihuo_status'] == 1){
            $peihuo_status = 3;
        }
        if($orderInfo['peihuo_status'] == 2){
            $peihuo_status = 4;
        }
        if($orderInfo['peihuo_status'] == 3){
            $peihuo_status = 5;
        }
        $orderNewInfo['delivery_status'] = $peihuo_status;
        $shipfreight_time = $orderInfo['shipping_time'];
        $is_real_invoice = $orderInfo['need_inv'];



        //布产状态
        //新布产状态：1未操作,2已布产,3生产中,4已出厂,5不需布产
        $buchan_status = $orderInfo['buchan_status'];
        $buchanStatus = array();
        $orderNewInfo['buchan_status'] = isset($buchanStatus[$buchan_status])?$buchanStatus[$buchan_status]:99;//99为出错状态

        //客户来源
        $from_ad = $orderInfo['from_ad'];
        $orderNewInfo['customer_source_id'] = getCustomerSource($from_ad);
        //销售渠道
        $department = $orderInfo['department'];
        $department_name = getOldDepartmentName($department);
        $department_id = getNewDepartmentId($department_name);
        $orderNewInfo['department_id'] = $department_id;

        $orderNewInfo['create_time'] = $orderInfo['order_time'];
        $orderNewInfo['create_user'] = $orderInfo['make_order'];
        $orderNewInfo['check_time'] = date("Y-m-d H:i:s",$orderInfo['confirm_time']);
        $orderNewInfo['check_user'] = $orderInfo['make_order'];//审单人使用制单人

        $orderNewInfo['genzong'] = $orderInfo['genzong'];
        $orderNewInfo['recommended'] = $orderInfo['recommended'];
        $orderNewInfo['modify_time'] = date("Y-m-d H:i:s");
        $orderNewInfo['order_remark'] = $orderInfo['postscript'];

        $referer_allow = array('网络订单','婚博会','展厅订单','批量导入','快速入单','管理员添加','工厂备货单','系统抓单','淘宝用户下单','主站','本站','网站');
        $orderNewInfo['referer'] = '异常';
        if(in_array($orderInfo['referer'],$referer_allow))
        {
            $orderNewInfo['referer'] = $orderInfo['referer'];
        }
        elseif(strpos($orderInfo['referer'],'管理员添加') !== false)
        {
            $orderNewInfo['referer'] = '管理员添加';
        }
        elseif(strpos($orderInfo['referer'],'婚博会') !== false)
        {
            $orderNewInfo['referer'] = '婚博会';
        }
        elseif(strpos($orderInfo['referer'],'展厅') !== false)
        {
            $orderNewInfo['referer'] = '展厅订单';
        }
        elseif($orderInfo['referer'] == '')
        {
            $orderNewInfo['referer'] = '未知';
        }

        $orderNewInfo['is_delete'] = 0;
        $orderNewInfo['apply_close'] = $orderInfo['apply_close'];
        $orderNewInfo['is_xianhuo'] = $orderInfo['buchan_status']==0?1:0;
        $orderNewInfo['is_print_tihuo'] = $orderInfo['print_thd']>0?1:0;
        $orderNewInfo['effect_date'] = $orderInfo['effect_date'];
        $orderNewInfo['is_zp'] = $orderInfo['is_zp'];
        $orderNewInfo['pay_date'] = $orderInfo['first_pay_time'];

        $apply_return = 0;
        $orderNewInfo['apply_return'] = $apply_return;

        //维修状态
        $weixiu_status = 0;
        $orderNewInfo['weixiu_status'] = $weixiu_status;

        $shipfreight_time = $orderInfo['shipping_time'];
        $is_real_invoice = $orderInfo['need_inv'];
        $out_company = getCompany($is_real_invoice);

        //用户信息
        $orderNewInfo['user_id'] = 0;//无法确认用户是否还是以前那个自增的ID
        $orderNewInfo['consignee'] = $orderInfo['consignee'];//顾客姓名
        $orderNewInfo['mobile'] = getMobile($orderInfo);//电话字段
//echo '<pre>';
//print_r($orderNewInfo);die;

        //var_dump($orderInfo,$orderNewInfo);
        //delete FROM `base_order_info` where id = 1935207;
        $ret = $conNew->insert('base_order_info',$orderNewInfo);
        //$conNewW->insert('toboss',array('tb'=>'base_order_info','zid'=>$order_id,'order_sn'=>$order_sn,'order_id'=>$order_id));
        //var_dump($ret);
        echo "1.基础信息{$order_sn}-{$order_id}生成成功"."<hr>\r\n";

        //2.订单地址信息
        if(true){
            $orderInfo;
            $app_order_addressInfo['order_id'] = $order_id;
            $app_order_addressInfo['consignee'] = $orderNewInfo['consignee'];
            $app_order_addressInfo['shop_type'] = 0;// 0 无 ,1 直营店 , 2 经销商店
            $app_order_addressInfo['shop_name'] = '';
            $distribution_type = 0;
            if($orderInfo['distribution_type'] == 1 ||$orderInfo['distribution_type'] == 3){
                $distribution_type=1;
                if($orderInfo['distribution_type'] == 1){
                    $app_order_addressInfo['shop_type'] = 1;
                }else{
                    $app_order_addressInfo['shop_type'] = 2;
                }
                $app_order_addressInfo['shop_name'] = $department_name;
            }elseif($orderInfo['distribution_type'] == 2){
                $distribution_type=2;
            }else{
                $distribution_type=9;
            }
            $app_order_addressInfo['distribution_type'] = $distribution_type;
            $app_order_addressInfo['express_id'] = $orderInfo['shipping_id'];
            $app_order_addressInfo['freight_no'] = $orderInfo['invoice_no'];

            //var_dump($orderInfo['province'],$orderInfo['city'],$orderInfo['district']);
            $p_name = getProvince($orderInfo['province'],1);
            $c_name = getCity($orderInfo['city'],2);
            $d_name = getDistrict($orderInfo['district'],3);

            //var_dump($p_name,$c_name,$d_name);

            $orderInfo['province'] = getProvinceId($p_name,1);
            $orderInfo['city'] = getCityId($c_name,$orderInfo['city']);
            $orderInfo['district'] = getDistrictId($d_name,$orderInfo['city']);
            //var_dump($orderInfo['province'],$orderInfo['city'],$orderInfo['district']);

            $app_order_addressInfo['country_id'] = $orderInfo['country'];
            $app_order_addressInfo['province_id'] = $orderInfo['province'];
            $app_order_addressInfo['city_id'] = $orderInfo['city'];
            $app_order_addressInfo['regional_id'] = $orderInfo['district'];
            $app_order_addressInfo['address'] = $orderInfo['address'];
            $app_order_addressInfo['tel'] = $orderNewInfo['mobile'];
            $app_order_addressInfo['email'] = $orderInfo['email'];
            $app_order_addressInfo['zipcode'] = $orderInfo['zipcode'];
            $app_order_addressInfo['goods_id'] = 0;
            $id = $conNew->insert('app_order_address',$app_order_addressInfo);
            //$conNewW->insert('toboss',array('tb'=>'app_order_address','zid'=>$id,'order_sn'=>$order_sn,'order_id'=>$order_id));
            echo "2.订单地址信息-{$id}-{$order_id}生成成功"."<hr>\r\n";
        }
        //3.发票
        if(true){
            $orderInvoice = array();
            $orderInvoice['order_id'] = $order_id;
            $orderInvoice['is_invoice'] = $orderInfo['need_inv'];
            $orderInvoice['invoice_title'] = trim($orderInfo['inv_payee']);
            $orderInvoice['invoice_content'] = $orderInfo['inv_content'];
            $orderInvoice['invoice_status'] = 1;//发票没有进行统计过，统一未开发票
            $invoice_amount = 0;
            if($orderInfo['inv_payee']){
                if($orderInfo['inv_amount']=='0.00'){
                    if($orderInfo['pay_status']==5){
                        $invoice_amount = $orderInfo['money_paid'] + $orderInfo['order_amount'];
                    }else{
                        $invoice_amount = $orderInfo['money_paid'];
                    }
                }
            }else{
                $invoice_amount = $orderInfo['inv_amount'];
            }
            $orderInvoice['invoice_amount'] = $invoice_amount;
            $orderInvoice['invoice_address'] = $orderInfo['inv_post_address'];
            $orderInvoice['invoice_num'] = $orderInfo['second_ship'];
            $orderInvoice['create_user'] = $orderNewInfo['create_time'];
            $orderInvoice['create_time'] = $orderNewInfo['create_time']; 
            $orderInvoice['use_user'] = '';
            $orderInvoice['use_time'] = '0000-00-00 00:00:00';
            $orderInvoice['cancel_user'] = '';
            $orderInvoice['cancel_time'] = '0000-00-00 00:00:00';
            $id = $conNew->insert('app_order_invoice',$orderInvoice);
            //$conNewW->insert('toboss',array('tb'=>'app_order_invoice','zid'=>$id,'order_sn'=>$order_sn,'order_id'=>$order_id));
            echo "3.发票信息-{$id}-{$order_id}生成成功"."<hr>\r\n";

            //8.迁移步骤1中订单的操作日志到boss对应的操作日志表；
            $order_action_sql="SELECT * FROM `kela_order_part`.`ecs_order_action` where order_id='$order_id' order by action_id asc; ";
            $aInfo=$conOld->getAll($order_action_sql);
            foreach($aInfo as $info)
            {   
                $order_action = array();
                $order_action['order_id'] = $order_id;

                $order_status=99;
                if($info['order_status'] == 0){
                    $order_status = 1;
                }
                if($info['order_status'] == 1){
                    $order_status = 2;
                }
                if($info['order_status'] == 5){
                    $order_status = 4;
                }
                if($info['order_status'] == 3){
                    $order_status = 3;
                }
                $order_action['order_status'] = $order_status;

                $send_good_status=99;
                if($info['shipping_status'] == 0){
                    $send_good_status=1;
                }
                if($info['shipping_status'] == 1){
                    $send_good_status=2;
                }
                if($info['shipping_status'] == 2){
                    $send_good_status=3;
                }
                if($info['shipping_status'] == 3){
                    $send_good_status=4;
                }
                if($info['shipping_status'] == 4){
                    $send_good_status=5;
                }
                $order_action['shipping_status'] = $send_good_status;


                $pay_status=99;
                if($info['pay_status'] == 0 || $orderInfo['pay_status'] == 1){
                    $pay_status=1;
                }
                if($info['pay_status'] == 2){
                    $pay_status=3;
                }
                if($info['pay_status'] == 4){
                    $pay_status=2;
                }
                if($info['pay_status'] == 5){
                    $pay_status=4;
                }
                $order_action['pay_status'] = $pay_status;
                $order_action['create_user'] = $info['action_user'];
                $order_action['create_time'] = $info['action_time'];
                $order_action['remark'] = $info['action_note'];
                $id = $conNew->insert('app_order_action_jxc',$order_action);
                //$conNewW->insert('toboss',array('tb'=>'app_order_action','zid'=>$id,'order_sn'=>$order_sn,'order_id'=>$order_id));
                echo "8 订单日志表 {$id}-{$order_id}生成更新成功"."<hr>\r\n";
            }
        }

        //2.订单商品
        if(true){
            $sql = "SELECT * FROM `kela_order_part`.`ecs_order_goods` where order_id = '".$order_id."';";
            $orderGoodsList = $conNew->getAll($sql);
            //var_dump($orderGoodsList);
            $orderNewDetails = array();
            $goodsList = array();

            foreach($orderGoodsList as $key => $val){
                $orderDetails = array();
                
                //基础信息
                $orderDetails['order_id'] = $order_id; 
                $orderDetails['goods_id'] = $val['goods_id']; 
                $orderDetails['goods_sn'] = $val['goods_sn']; 
                $orderDetails['ext_goods_sn'] = $val['ext_goods_sn']; 
                $orderDetails['goods_name'] = $val['goods_name']; 

                //价格信息
                $orderDetails['goods_price'] = $val['market_price']; 
                $orderDetails['favorable_price'] = $val['market_price']-$val['goods_price']; 
                $orderDetails['goods_count'] = $val['goods_number']; 
                $orderDetails['favorable_status'] = 3; //所有商品当做已审核
                $orderDetails['allow_favorable'] = 1; 
                $favorable_price += $orderDetails['favorable_price'];
                $unfavorable_price += $orderDetails['goods_price'];

                //时间信息
                $orderDetails['create_time'] = $orderNewInfo['create_time']; 
                $orderDetails['modify_time'] = $orderNewInfo['create_time']; 
                $orderDetails['create_user'] = $orderNewInfo['create_user']; 

                //状态信息
                $orderDetails['details_status'] = $val['goods_status']; 
                $orderDetails['send_good_status'] = $val['goods_status']; //arrival_status 无状态
                $orderDetails['buchan_status'] = $val['goods_status']; 
                $orderDetails['is_stock_goods'] = $orderNewInfo['is_xianhuo']?1:0;//定制单与销售单不下在同一个订单里 
                $orderDetails['is_return'] = $val['is_return']; 
                $orderDetails['details_remark'] = '';//老订单没有备注信息 

                //商品属性信息
                $orderDetails['cart'] = $val['stone']; 
                $orderDetails['cut'] = $val['cut']; 
                $orderDetails['clarity'] = $val['stone_clear']; 
                $orderDetails['color'] = $val['stone_color'];

                $certInfo = getCertInfo($val['certid']);
                $orderDetails['cert'] = $certInfo['cert']; 
                $orderDetails['zhengshuhao'] = $certInfo['certid'];

                $orderDetails['caizhi'] = strtoupper($val['gold']); 
                $orderDetails['jinse'] = $val['gold_color']; 
                $orderDetails['jinzhong'] = $val['gold_weight']; 
                $orderDetails['zhiquan'] = $val['finger']; 
                $orderDetails['kezi'] = $val['word']; 
                $orderDetails['face_work'] = $val['face_work']; 
                $orderDetails['xiangqian'] = $val['chengpin']; 
                $orderDetails['xiangkou'] = $val['jietuoxiangkou']; 


                //商品类型信息
                $orderDetails['is_zp'] = 0; 
                $orderDetails['qiban_type'] = 2; //非起版:2	有款起版:1	无款起版:0
                $goods_type = $val['goods_type'];
                if(in_array($goods_type,array('lz','luozuan'))){
                    $orderDetails['goods_type'] = 'lz'; 
                }elseif(in_array($goods_type,array('zp','zengpin'))){
                    $orderDetails['goods_type'] = 'zp'; 
                    $orderDetails['is_zp'] = 1; 
                }elseif(in_array($goods_type,array('dz','dingzhi'))){
                    $orderDetails['goods_type'] = 'qiban'; 
                    if($val['goods_sn'] == 'QIBAN' || $val['goods_sn'] == 'DINGZHI'){
                        $orderDetails['qiban_type'] = 0;
                    }else{
                        $orderDetails['qiban_type'] = 1;
                    }
                }else{
                    $orderDetails['goods_type'] = 'style_goods'; 
                }
                $orderDetails['cat_type'] = 0;      //使用数据清洗的方式
                $orderDetails['product_type'] = 0;  //使用数据清洗的方式 
                $orderDetails['kuan_sn'] = ''; 

                $orderDetails['chengbenjia'] = 0;//无意义 
                $orderDetails['bc_id'] = 0; //无意义 无存在的布产单
                $orderDetails['policy_id'] = 0;//无意义 
                $orderDetails['is_finance'] = 0;//无意义 
                $orderDetails['is_peishi'] = 0;//无意义 
                $orderDetails['weixiu_status'] = 0;//无意义 

                $id = $conNew->insert('app_order_details',$orderDetails);
                //$conNewW->insert('toboss',array('tb'=>'app_order_details','zid'=>$id,'order_sn'=>$order_sn,'order_id'=>$order_id));
                $order_goods_id = $id;
                echo "2.订单商品信息{$id}生成成功"."<hr>\r\n";
                //var_dump($ret);
                    
            }
        }
        //7.订单金额
        if(true){
            $orderInfo;
            $app_order_accountInfo['order_id'] = $order_id;
            $app_order_accountInfo['order_amount'] = $orderInfo['money_paid']+$orderInfo['order_amount']-$orderInfo['real_return_price'];
            $app_order_accountInfo['money_paid'] = $orderInfo['money_paid'];
            $app_order_accountInfo['money_unpaid'] = $orderInfo['order_amount'];
            $app_order_accountInfo['goods_return_price'] = $orderInfo['goods_return_price'];
            $app_order_accountInfo['real_return_price'] = $orderInfo['real_return_price'];
            $app_order_accountInfo['shipping_fee'] = $orderInfo['shipping_fee'];
            $app_order_accountInfo['goods_amount'] = $unfavorable_price;
            $app_order_accountInfo['coupon_price'] = 0;
            $app_order_accountInfo['favorable_price'] = $favorable_price;
            $app_order_accountInfo['card_fee'] = $orderInfo['card_fee'];
            $app_order_accountInfo['pack_fee'] = $orderInfo['pack_fee'];
            $app_order_accountInfo['pay_fee'] = $orderInfo['pay_fee'];
            $app_order_accountInfo['insure_fee'] = $orderInfo['insure_fee'];
            $id = $conNew->insert('app_order_account',$app_order_accountInfo);
            //$conNewW->insert('toboss',array('tb'=>'app_order_account','zid'=>$id,'order_sn'=>$order_sn,'order_id'=>$order_id));
            echo "7 订单金额表 {$id}-{$order_id}生成更新成功"."<hr>\r\n";
        }
    }
}

$t2 = microtime(true);
echo '耗时'.round($t2-$t1,3).'秒';


