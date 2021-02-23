<?php   
header("Content-type:text/html;charset=utf8;");
error_reporting(E_ALL);
set_time_limit(0);
date_default_timezone_set("PRC");
ini_set('memory_limit','2000M');

define('ROOT_PATH', str_replace('orderO.php', '', str_replace('\\', '/', __FILE__)));
require_once(ROOT_PATH.'MysqlDB.class.php');
require_once(ROOT_PATH.'function.php');

$conOldConf = [
    'dsn'=>"mysql:host=192.168.0.91;dbname=kela_order_part",
    'user'=>"root",
    'password'=>"123456",
    'charset' => 'utf8'
];
$conOldJxcConf = [
    'dsn'=>"mysql:host=192.168.0.91;dbname=jxc",
    'user'=>"root",
    'password'=>"123456",
    'charset' => 'utf8'
];
$conNewConf = [
    'dsn'=>"mysql:host=192.168.0.91;dbname=app_order",
    'user'=>"root",
    'password'=>"123456",
    'charset' => 'utf8'
];
$conNewConf_W = [
    'dsn'=>"mysql:host=192.168.0.91;dbname=warehouse_shipping",
    'user'=>"root",
    'password'=>"123456",
    'charset' => 'utf8'
];
$conNewConf_F = [
    'dsn'=>"mysql:host=192.168.0.91;dbname=front",
    'user'=>"root",
    'password'=>"123456",
    'charset' => 'utf8'
];

$conOld = new MysqlDB($conOldConf);
$conNew = new MysqlDB($conNewConf);
$conNewW = new MysqlDB($conNewConf_W);
$conNewF = new MysqlDB($conNewConf_F);
$conJxc = new MysqlDB($conOldJxcConf);
//维修单
$sql="select * 
from `jxc_order` `b` inner join `jxc_order_goods` `d` on `b`.`order_id` = `d`.`order_id`
where `b`.`type` in('O','R','WF') 
and `b`.`status` in (1,2) 
and `d`.`goods_id` in(select `goods_id` from `jxc_goods` where `weixiu_status` in (1,2,4,5));";
//$sql.=" AND order_sn = '2015041113716' ";
//echo $sql;die;
$all = $conJxc->getAll($sql);

foreach ($all as $key => $value) {
    # code...
    $allData[$value['order_id']] = $value;
}

foreach ($allData as $order) {
    # code...
    
    $order_id = $order['order_id'];

    $jxc_order_sql = "select * 
from `jxc_order` `b` inner join `jxc_order_goods` `d` on `b`.`order_id` = `d`.`order_id`
where `b`.`type` in('O','R','WF') 
and `b`.`status` in (1,2) 
and `b`.`order_id` = '$order_id' 
and `d`.`goods_id` in(select `goods_id` from `jxc_goods` where `weixiu_status` in (1,2,4,5));";
//echo $jxc_order_sql;die;
    $jxcInfo = $conJxc->getAll($jxc_order_sql);

    $warehouse_billInfo = array();
    $warehouse_bill_goodsList = array();
    foreach($jxcInfo as $key => $jxc_info){

        $warehouse_billInfo['bill_no'] = $jxc_info['type'].$jxc_info['order_id'];
        $warehouse_billInfo['bill_type'] = $jxc_info['type'];
        $warehouse_billInfo['bill_status'] = 2;
        $warehouse_billInfo['order_sn'] = $jxc_info['kela_order_sn'];
        $warehouse_billInfo['goods_num'] = $jxc_info['goods_num'];
        $warehouse_billInfo['put_in_type'] = $jxc_info['in_warehouse_type'];
        $warehouse_billInfo['jiejia'] = $jxc_info['jiejia'];
        $warehouse_billInfo['tuihuoyuanyin'] = $jxc_info['tuihuoyuanyin'];
        $warehouse_billInfo['send_goods_sn'] = $jxc_info['send_goods_sn'];


        $warehouse_billInfo['pro_id'] = $jxc_info['prc_id'];
        $warehouse_billInfo['pro_name'] = $jxc_info['prc_name'];
        $warehouse_billInfo['goods_total'] = $jxc_info['goods_total'];
        $warehouse_billInfo['goods_total_jiajia'] = 0;//未知加价
        $warehouse_billInfo['shijia'] = $jxc_info['shijia'];
        $warehouse_billInfo['to_warehouse_id'] = $jxc_info['to_warehouse_id'];
        $warehouse_billInfo['to_warehouse_name'] = $jxc_info['to_warehouse'];
        $warehouse_billInfo['to_company_id'] = $jxc_info['to_company_id'];
        $warehouse_billInfo['to_company_name'] = $jxc_info['to_company'];
        $warehouse_billInfo['from_company_id'] = $jxc_info['from_company_id'];
        $warehouse_billInfo['from_company_name'] = $jxc_info['from_company'];
        $warehouse_billInfo['bill_note'] = $jxc_info['info'];
        $warehouse_billInfo['yuanshichengben'] = $jxc_info['chengben'];
        $warehouse_billInfo['check_user'] = $jxc_info['check_order'];
        $warehouse_billInfo['check_time'] = $jxc_info['checktime'];
        $warehouse_billInfo['create_user'] = $jxc_info['make_order'];
        $warehouse_billInfo['create_time'] = $jxc_info['addtime'];
        $warehouse_billInfo['fin_check_status'] = 0;
        $warehouse_billInfo['fin_check_time'] = $jxc_info['fin_check_time'];
        $warehouse_billInfo['to_customer_id'] = $jxc_info['shipping_id'];
        $warehouse_billInfo['pifajia'] = $jxc_info['pf_shijia'];
        $warehouse_billInfo['consignee'] = '';//缺失此字段 顾客姓名
        $warehouse_billInfo['company_id_from'] = 0;//业务组织公司ID
        $warehouse_billInfo['company_from'] = 0;//业务组织公司名称
        //$warehouse_billInfo['from_bill_id'] = '';//来源单据id
//echo '<pre>';
//print_r($warehouse_billInfo);die;


        $warehouse_bill_goodsInfo;
        $warehouse_bill_goodsInfo['bill_id'] = 0; 
        $warehouse_bill_goodsInfo['bill_no'] = $warehouse_billInfo['bill_no']; 
        $warehouse_bill_goodsInfo['bill_type'] = $warehouse_billInfo['bill_type']; 
        $warehouse_bill_goodsInfo['goods_id'] = $jxc_info['goods_id'];
        $warehouse_bill_goodsInfo['goods_sn'] = $jxc_info['goods_sn'];
        $warehouse_bill_goodsInfo['goods_name'] = $jxc_info['goods_name'];
        $warehouse_bill_goodsInfo['num'] = $jxc_info['num'];
        $warehouse_bill_goodsInfo['warehouse_id'] = 0;//warehouse_id

//echo '<pre>';
//print_r($warehouse_bill_goodsInfo);die;

        $goods_info = getGoodsInfo($jxc_info['goods_id']);

        $warehouse_bill_goodsInfo['caizhi'] = $goods_info['caizhi'];
        $warehouse_bill_goodsInfo['jinzhong'] = $goods_info['jinzhong'];
        $warehouse_bill_goodsInfo['jingdu'] = $goods_info['jingdu'];
        $warehouse_bill_goodsInfo['jinhao'] = $goods_info['jinhao'];
        $warehouse_bill_goodsInfo['yanse'] = $goods_info['yanse'];
        $warehouse_bill_goodsInfo['zhengshuhao'] = $goods_info['zhengshuhao'];
        $warehouse_bill_goodsInfo['zuanshidaxiao'] = $goods_info['zuanshidaxiao'];
        $warehouse_bill_goodsInfo['in_warehouse_type'] = $goods_info['in_warehouse_type'];
        $warehouse_bill_goodsInfo['account'] = $goods_info['account'];//是否结价0、默认无。1、未结价。2、已结价
        $warehouse_bill_goodsInfo['yuanshichengben'] = $goods_info['yuanshichengben'];

        $warehouse_bill_goodsInfo['chengbenjia'] = $jxc_info['caigou_chengben'];
        $warehouse_bill_goodsInfo['mingyijia'] = $jxc_info['sale_price'];
        $warehouse_bill_goodsInfo['xiaoshoujia'] = $jxc_info['shijia'];
        $warehouse_bill_goodsInfo['addtime'] = $jxc_info['addtime'];
        $warehouse_bill_goodsInfo['pandian_status'] = 0;//盘点状态 参考数字字典
        $warehouse_bill_goodsInfo['guiwei'] = $jxc_info['num'];
        $warehouse_bill_goodsInfo['detail_id'] = $jxc_info['num'];//销售单和退后单存订单的detail_id所用
        $warehouse_bill_goodsInfo['pandian_guiwei'] = $jxc_info['pandian_guiwei'];
        $warehouse_bill_goodsInfo['pandian_user'] = '';
        $warehouse_bill_goodsInfo['pifajia'] = $jxc_info['pf_shijia'];
        $warehouse_bill_goodsInfo['sale_price'] = $jxc_info['sale_price'];
        $warehouse_bill_goodsInfo['shijia'] = $jxc_info['shijia'];
        $warehouse_bill_goodsInfo['bill_y_id'] = '';//Y单号
        $warehouse_bill_goodsInfo['jiajialv'] = 0;//用于Y单号
//echo '<pre>';
//print_r($warehouse_bill_goodsInfo);die;
        $warehouse_bill_goodsList[] = $warehouse_bill_goodsInfo;
    }
//echo '<pre>';
//print_r($warehouse_billInfo);die;
    $bill_id = $conNewW->insert('warehouse_bill',$warehouse_billInfo);    //var_dump($bill_id);die;
//var_dump($bill_id);die;
    //$conNewW->insert('toboss',array('tb'=>'warehouse_bill','zid'=>$bill_id,'order_id'=>$order_id));
    echo "3.维修单-{$bill_id}生成更新成功"."<hr>";
    foreach($warehouse_bill_goodsList as $warehouse_bill_goods)
    {
        $warehouse_bill_goods['bill_id'] = $bill_id;
        //echo '<pre>';
        //print_r($warehouse_bill_goods);die;
        $id=$conNewW->insert('warehouse_bill_goods',$warehouse_bill_goods);
        //$conNewW->insert('toboss',array('tb'=>'warehouse_bill_goods','zid'=>$id,'order_id'=>$order_id));
        echo "3.1 维修单-{$bill_id}-{$id}生成更新成功"."<hr>";
        
        $goods_id = $warehouse_bill_goods['goods_id'];
        $order_goods_id_sql = "select * from `jxc`.`jxc_goods` where goods_id = '$goods_id';";
        $rec_info=$conOld->getRow($order_goods_id_sql);
        if($rec_info)
        {
            $goods_id = $rec_info['goods_id'];
            $goods_sql = "select * from `warehouse_shipping`.`warehouse_goods` where goods_id = '$goods_id';";
            $row=$conNew->getRow($goods_sql);
            if($row){
                echo "2.订单商品信息-仓库商品信息{$row['goods_id']}已存在成功"."<hr>";
            }else{
            
                $time = date('Y-m-d H:i:s');
                $value = $rec_info;

                $order_goods_id_sql = "select company_sn from `cuteframe`.`company` where id = '".$value['company']."';";
                $company_name = $conNew->getOne($order_goods_id_sql);

                $sql = "select name from `warehouse_shipping`.`warehouse` where id = ".$value['warehouse'];
                $warehouse_name = $conNew->getOne($sql);
                $put_in_type = array('1','2','3','4');

                //商品导入
                $sql_arr=array(
                        'goods_id'=>$value['goods_id'],
                        'goods_sn'=>$value['goods_sn'],
                        'buchan_sn'=>'',
                        'order_goods_id'=>$value['order_goods_id'],
                        'product_type'=>$value['shipin_type'],
                        'cat_type'=>$value['kuanshi_type'],
                        'is_on_sale'=>3,
                        'prc_id'=>$value['prc_id']?$value['prc_id']:0,
                        'prc_name'=>$value['prc_name'],
                        'mo_sn'=>$value['mo_sn'],
                        'put_in_type'=>isset($put_in_type[$value['storage_mode']])?$put_in_type[$value['storage_mode']]:0,
                        'goods_name'=>$value['goods_name'],
                        'company'=>$company_name,
                        'warehouse'=>$warehouse_name,
                        'company_id'=>$value['company']?$value['company']:0,
                        'warehouse_id'=>$value['warehouse']?$value['warehouse']:0,
                        'caizhi'=>$value['zhuchengse'],
                        'jinzhong'=>$value['zhuchengsezhong']?$value['zhuchengsezhong']:'0.00',
                        'jinhao'=>$value['jinhao'],
                        'zhushi'=>$value['zhushi'],
                        'zhuchengsezhongjijia'=>$value['zhuchengsezhongjijia']?$value['zhuchengsezhongjijia']:'0.00',
                        'zhuchengsemairudanjia'=>$value['zhuchengsemairudanjia']?$value['zhuchengsemairudanjia']:'0.00',
                        'zhuchengsemairuchengben'=>$value['zhuchengsemairuchengben']?$value['zhuchengsemairuchengben']:'0.00',
                        'zhuchengsejijiadanjia'=>$value['zhuchengsejijiadanjia']?$value['zhuchengsejijiadanjia']:'0.00',
                        'zhushilishu'=>$value['zhushilishu'],
                        'zuanshidaxiao'=>$value['zhushizhong']?$value['zhushizhong']:'0.00',
                        'zhushizhongjijia'=>$value['zhushizhongjijia'],
                        'zhushiyanse'=>$value['zhushiyanse'],
                        'zhushijingdu'=>$value['zhushijingdu'],
                        'zhushimairudanjia'=>$value['zhushimairudanjia']?$value['zhushimairudanjia']:'0.00',
                        'zhushimairuchengben'=>$value['zhushimairuchengben']?$value['zhushimairuchengben']:'0.00',
                        'zhushijijiadanjia'=>$value['zhushijijiadanjia']?$value['zhushijijiadanjia']:'0.00',
                        'zhushiqiegong'=>$value['zhushiqiegong'],
                        'zhushixingzhuang'=>$value['zhushixingzhuang'],
                        'zhushibaohao'=>$value['zhushibaohao'],
                        'zhushiguige'=>$value['zhushiguige'],
                        'fushi'=>$value['fushi'],
                        'fushilishu'=>$value['fushilishu'],
                        'fushizhong'=>$value['fushizhong']?$value['fushizhong']:'0.00',
                        'fushizhongjijia'=>$value['fushizhongjijia'],
                        'fushiyanse'=>$value['fushiyanse'],
                        'fushijingdu'=>$value['fushijingdu'],
                        'fushimairuchengben'=>$value['fushimairuchengben']?$value['fushimairuchengben']:'0.00',
                        'fushimairudanjia'=>$value['fushimairudanjia']?$value['fushimairudanjia']:'0.00',
                        'fushijijiadanjia'=>$value['fushijijiadanjia']?$value['fushijijiadanjia']:'0.00',
                        'fushixingzhuang'=>$value['fushixingzhuang'],
                        'fushibaohao'=>$value['fushibaohao'],
                        'fushiguige'=>$value['fushiguige'],
                        'zongzhong'=>$value['zongzhong'],
                        'mairugongfeidanjia'=>$value['mairugongfeidanjia']?$value['mairugongfeidanjia']:'0.00',
                        'mairugongfei'=>$value['mairugongfei']?$value['mairugongfei']:'0.00',
                        'jijiagongfei'=>$value['jijiagongfei']?$value['jijiagongfei']:'0.00',
                        'shoucun'=>$value['shoucun']?$value['shoucun']:0,
                        'ziyin'=>$value['ziyin'],

                        'danjianchengben'=>$value['danjianchengben']?$value['danjianchengben']:'0.00',
                        'peijianchengben'=>$value['peijianchengben']?$value['peijianchengben']:'0.00',
                        'qitachengben'=>$value['qitachengben']?$value['qitachengben']:'0.00',
                        'yuanshichengbenjia'=>$value['yuanshichengbenjia']?$value['yuanshichengbenjia']:'0.00',




                    


                        'chengbenjia'=>$value['chengbenjia']?$value['chengbenjia']:'0.00',
                        'jijiachengben'=>$value['jijiachengben'],
                        'jiajialv'=>$value['jiajialv']?$value['jiajialv']:'0.00',
                        'kela_order_sn'=>$value['kela_order_sn'],
                        'zuixinlingshoujia'=>$value['zuixinlingshoujia']?$value['zuixinlingshoujia']:'0.00',
                        'pinpai'=>$value['pinpai'],
                        'changdu'=>$value['changdu'],
                        'zhengshuhao'=>$value['zhengshuhao'],
                        'zhengshuhao2'=>$value['zhengshuhao2'],
                        'yanse'=>$value['zhushiyanse'],
                        'jingdu'=>$value['zhushijingdu'],
                        'peijianshuliang'=>$value['peijianshuliang'],
                        'guojizhengshu'=>$value['guojizhengshu'],
                        'zhengshuleibie'=>$value['zhengshuleibie'],
                        'gemx_zhengshu'=>$value['gemx_zhengshu'],
                        'num'=>$value['num'],
                        'addtime'=>$value['addtime'],
                        'shi2'=>$value['shi2'],
                        'shi2lishu'=>$value['shi2lishu'],
                        'shi2zhong'=>$value['shi2zhong']?$value['shi2zhong']:'0.00',
                        'shi2zhongjijia'=>$value['shi2zhongjijia'],
                        'shi2mairudanjia'=>$value['shi2mairudanjia']?$value['shi2mairudanjia']:'0.00',
                        'shi2mairuchengben'=>$value['shi2mairuchengben']?$value['shi2mairuchengben']:'0.00',


                        'shi2jijiadanjia'=>$value['shi2jijiadanjia']?$value['shi2jijiadanjia']:'0.00',
                        'qiegong'=>$value['qiegong'],
                        'paoguang'=>$value['paoguang'],
                        'duichen'=>$value['duichen'],
                        'yingguang'=>$value['yingguang'],
                        'mingyichengben'=>$value['xianzaichengben']?$value['xianzaichengben']:'0.00',
                        'xianzaixiaoshou'=>$value['xianzaixiaoshou']?$value['xianzaixiaoshou']:'0.00',
                        'zuanshizhekou'=>$value['zuanshizhekou'],
                        'guojibaojia'=>$value['guojibaojia'],
                        'gongchangchengben'=>$value['gongchangchengben'],
                        'account'=>$value['account']?$value['account']:0,
                        'account_time'=>$value['account_time'],
                        'tuo_type'=>$value['tuo_type']?$value['tuo_type']:0,
                        'att1'=>$value['att1'],
                        'att2'=>$value['att2'],
                        'huopin_type'=>$value['huopin_type']?$value['huopin_type']:0,
                        'dia_sn'=>$value['dia_sn'],
                        'zhushipipeichengben'=>$value['zhushipipeichengben']?$value['zhushipipeichengben']:'0.00',
                        'biaoqianjia'=>$value['biaoqianjia']?$value['biaoqianjia']:'0.00',
                        'jietuoxiangkou'=>$value['jietuoxiangkou']?$value['jietuoxiangkou']:'0.000',
                        'caigou_chengbenjia'=>$value['caigou_chengbenjia']?$value['caigou_chengbenjia']:'0.00',
                        'box_sn'=>$value['tmp_sn']?$value['tmp_sn']:'0-00-0-0',
                        'pass_sale'=>1,
                        'old_set_w'=>1,
                        'weixiu_status'=>$value['weixiu_status']?$value['weixiu_status']:0,
                        'jiejia'=>$value['account']?$value['account']:0,
                        'oldsys_id'=>$value['id']?$value['id']:0
                        );

                $id = $conNewW->insert('warehouse_goods',$sql_arr);
                //$conNewW->insert('toboss',array('tb'=>'warehouse_goods','zid'=>$id,'order_id'=>$order_id));
                echo "2.订单商品信息-仓库商品信息{$id}-{$value['goods_id']}生成成功"."<hr>";
            }
        }
    }
}