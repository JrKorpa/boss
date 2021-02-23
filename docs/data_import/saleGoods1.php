<?php
/**
 *  -------------------------------------------------
 * 文件说明     可销售商品 数据导入
 * @file        : warehouse.php
 * @date        : 2015-3-10 16:13:31
 * @author      : yangxt <yangxiaotong@163.com
 *  -------------------------------------------------
 */
header("Content-type:text/html;charset=utf-8");
require_once('MysqlDB.class.php');
date_default_timezone_set('Asia/Shanghai');

// $conf = [
//     'dsn'=>"mysql:host=192.168.1.93;dbname=warehouse_shipping;charset=utf8",
//     'user'=>"cuteman",
//     'password'=>"QW@W#RSS33#E#",
// ];

$conf = [
    'dsn'=>"mysql:host=localhost;dbname=warehouse_shipping;charset=utf8",
    'user'=>"root",
    'password'=>"yangxt",
];

$db = new DB($conf);

$filter = [
    'goods_id'=>'goods_id',
    'goods_sn'=>'goods_sn',
    'goods_name'=>'goods_name',
    'mingyichengben'=>'chengbenjia',
    'cat_type'=>'category',
    'product_type'=>'product_type',
    'jietuoxiangkou'=>'xiangkou',
    'company_id'=>'company_id',
    'company'=>'company',
    'warehouse'=>'warehouse',
    'warehouse_id'=>'warehouse_id',
    'shoucun'=>'finger',
    'is_on_sale'=>'is_sale',
];

$dict = [
    'category'=>[
        '女戒'=>'2','吊坠'=>'3','项链'=>'4','耳饰'=>'5','手镯'=>'6','手链'=>'7','其他'=>'8','脚链'=>'9','男戒'=>'10','情侣戒'=>'11',
        '套装'=>'14','金条'=>'15','摆件'=>'16','裸石'=>'17','圆钻裸石'=>'17','异形钻裸石'=>'17','空托女戒'=>'2','立体6围1女戒'=>'2',
        '精品女戒'=>'2','钻石女戒'=>'2','锆石女戒'=>'2','挂坠'=>'3','碧玺项坠'=>'3','空托吊坠'=>'3','吊坠 链'=>'3',
        '钻石吊坠'=>'3','空托男戒'=>'10','CNC情侣戒'=>'11','钻石情侣戒'=>'11','空托耳钉'=>'5','耳吊'=>'5','耳圈'=>'5','耳环'=>'5',
        '耳背'=>'5','耳迫'=>'5','耳钉'=>'5','钻石耳钉'=>'5','套链'=>'14','戒指'=>'8','0'=>'8',''=>'8',' '=>'8',
        '包装袋'=>'8','样板女戒'=>'8','素金戒指'=>'8','婚纱'=>'8','小家电'=>'8','工艺品'=>'8','户外用品'=>'8','手机壳'=>'8',
        '手机链'=>'8','水晶'=>'8','淡水珍珠'=>'8','玩具'=>'8','珍珠'=>'8','笔'=>'8','胸针'=>'8','表'=>'8','裸珠'=>'8','赠品'=>'8',
        '金箔玫瑰'=>'8','铜链'=>'8','银条'=>'8','链牌'=>'8','饰品'=>'8','首饰盒'=>'8',
        '串件'=>'8','千足金套装'=>'8','发夹'=>'8','家居用品'=>'8','康乃馨'=>'8','皮带头'=>'8','皮绳'=>'8',
    ],
    'product_type'=>[
        ''
    ],
    'is_sale'=>['1'=>'0','2'=>'1','3'=>'0','4'=>'1','5'=>'1','6'=>'0','7'=>'0','8'=>'0','9'=>'0','10'=>'0','11'=>'0','12'=>'0']
];


$default = [
    'isXianhuo'=>'1',
    'type'=>'1',
    'product_type'=>'6',//产品线
    'add_time'=>date('Y-m-d H:i:s')
];


$sel = $db->replaceFields($filter);
/*
$ids = [
    '108070257',
    '150421542310',
    '150421542311',
    '150421542312',
    '150421542313'
];
*/
$data = array();
foreach ($ids as $g) {
    $sql = "SELECT ".$sel." FROM `warehouse_goods` WHERE `goods_id` = '".$g."'";
    $res = $db->getRow($sql);
    if(!empty($res)){
        $data[] = $res;
    }
}

if(!empty($data)){

    $data = $db->replaceDict($data,$dict);
    $data = $db->setDefault($data,$default);

    $db->selectDB('front');
    $i = 0;
    foreach ($data as $row) {
        $row['is_sale'] = $is_sale[$row['is_sale']];

        $sql = "SELECT COUNT(*) FROM `base_salepolicy_goods` WHERE `goods_id` = '".$row['goods_id']."'";
        $res = $db->getOne($sql);
        if($res){
            continue;
        }else{
            $res = $db->autoExec($row,'base_salepolicy_goods');
            if($res){
                $i++;
                echo iconv('UTF-8', 'GBK', "SUCCESS INSER ".$row['goods_id']." TO [base_salepolicy_goods] ".$i."\r\n");
            }else{
                echo iconv('UTF-8', 'GBK', "LOSE INSER ".$row['goods_id']." TO [base_salepolicy_goods]\r\n");
            }
        }
    }
    echo iconv('UTF-8', 'GBK', "---===THE INSER MISSION END===---\r\n");
    echo iconv('UTF-8', 'GBK', "---===THE INSER MISSION END===---\r\n");
    echo iconv('UTF-8', 'GBK', "---===THE INSER MISSION END===---\r\n");
}



?>