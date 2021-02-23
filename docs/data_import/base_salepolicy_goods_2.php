<?php
/**
 *  -------------------------------------------------
 * 文件说明		可销售商品 数据导入
 * @file		: base_salepolicy_goods.php
 * @date 		: 2015-3-10 16:13:31
 * @author		: yangxt <yangxiaotong@163.com
 *  -------------------------------------------------
 */
header("Content-type:text/html;charset=utf-8");
require_once('ExportData2.1.class.php');
date_default_timezone_set('Asia/Shanghai');
set_time_limit(0);
ini_set('memory_limit','2000M');

/*=============================================================
		从新系统warehouse_goods 导数据
==============================================================*/
ini_set('memory_limit','2000M');
$new_conf = [
    'dsn'=>"mysql:host=192.168.1.93;dbname=front",
    'user'=>"cuteman",
    'password'=>"QW@W#RSS33#E#",
];

$old_conf = [
    'dsn'=>"mysql:host=192.168.1.93;dbname=warehouse_shipping",
    'user'=>"cuteman",
    'password'=>"QW@W#RSS33#E#",
];



$n_table = 'base_salepolicy_goods';
$o_table = 'warehouse_goods';

$pass = true;		//记录导出状态
$old_id = false;	//保留旧表主键

//总公司需推送库[2,79,184,386,482,483,484,485,486,546,487,308,96,443,5,342,369,523,546]

//表查询条件
$where = "`m`.`is_on_sale` = '2' AND `m`.`pass_sale` = '0' AND (`order_goods_id` = '0' OR `order_goods_id` ='') ";
//$where .= " AND `warehouse_id` IN (2,79,184,386,482,483,484,485,486,546,487,308,96,443,5,342,369,523,546) ";

//$sql_w = "SELECT `id` FROM `warehouse` WHERE `name` LIKE '%维修%' OR `name` LIKE '%待取%'";
$where .= " AND `company_id` <> '58' AND `cat_type` <> '裸钻' AND `warehouse_id` NOT IN (SELECT `id` FROM `warehouse` WHERE `name` LIKE '%维修%' OR `name` LIKE '%待取%') ";

// 旧表联查
$table2 = false;
$no = [];
$field_arr = [
];


$model = new ExportData($new_conf,$old_conf,$n_table,$o_table,$pass,$old_id);

//设置导数量
$model->len = '0';
$model->pass_status = 'pass_sale';
// var_dump($model->pass_status);exit;
//去除不需要的字段
$model->cannelField = [
    'caizhi','yanse','id'
];

//设置字段
$model->filter = [
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
];


//替换数据字典 'filed'=>['0'=>'1'],
$model->dict = [
    'category'=>[
        '女戒'=>'2',
        '吊坠'=>'3',
        '项链'=>'4',
        '耳饰'=>'5',
        '手镯'=>'6',
        '手链'=>'7',
        '其他'=>'8',
        '脚链'=>'9',
        '男戒'=>'10',
        '情侣戒'=>'11',
        '套装'=>'14',
        '金条'=>'15',
        '摆件'=>'16',
        '裸石'=>'17',
        '圆钻裸石'=>'17',
        '异形钻裸石'=>'17',
        '空托女戒'=>'2',
        '立体6围1女戒'=>'2',
        '精品女戒'=>'2',
        '钻石女戒'=>'2',
        '锆石女戒'=>'2',
        '挂坠'=>'3',
        '碧玺项坠'=>'3',
        '空托吊坠'=>'3',
        '吊坠 链'=>'3',
        '钻石吊坠'=>'3',
        '空托男戒'=>'10',
        'CNC情侣戒'=>'11',
        '钻石情侣戒'=>'11',
        '空托耳钉'=>'5',
        '耳吊'=>'5',
        '耳圈'=>'5',
        '耳环'=>'5',
        '耳背'=>'5',
        '耳迫'=>'5',
        '耳钉'=>'5',
        '钻石耳钉'=>'5',
        '套链'=>'14',
        '戒指'=>'8',
        '0'=>'8',
        ''=>'8',
        ' '=>'8',
        '包装袋'=>'8',
        '样板女戒'=>'8',
        '素金戒指'=>'8',
        '婚纱'=>'8',
        '小家电'=>'8',
        '工艺品'=>'8',
        '户外用品'=>'8',
        '手机壳'=>'8',
        '手机链'=>'8',
        '水晶'=>'8',
        '淡水珍珠'=>'8',
        '玩具'=>'8',
        '珍珠'=>'8',
        '笔'=>'8',
        '胸针'=>'8',
        '表'=>'8',
        '裸珠'=>'8',
        '赠品'=>'8',
        '金箔玫瑰'=>'8',
        '铜链'=>'8',
        '银条'=>'8',
        '链牌'=>'8',
        '饰品'=>'8',
        '首饰盒'=>'8',
        '串件'=>'8',
        '千足金套装'=>'8',
        '发夹'=>'8',
        '家居用品'=>'8',
        '康乃馨'=>'8',
        '皮带头'=>'8',
        '皮绳'=>'8',
    ],
    'product_type'=>[
        ' '=>'5',
        '0'=>'5',
        '其他'=>'5',
        '其他饰品'=>'5',
        '其它'=>'5',
        '吊坠'=>'5',
        '女戒'=>'5',
        '异形钻裸石'=>'6',
        '彩宝及翡翠饰品'=>'16',
        '彩宝饰品'=>'5',
        '情侣戒'=>'5',
        '成品钻'=>'6',
        '珍珠饰品'=>'5',
        '素金戒指'=>'4',
        '素金饰品'=>'5',
        '翡翠饰品'=>'5',
        '裸石'=>'7',
        '裸钻'=>'6',
        '配件及特殊包装'=>'5',
        '钻石吊坠'=>'5',
        '钻石女戒'=>'5',
        '钻石饰品'=>'5',
        '镶嵌'=>'3',
        '非珠宝'=>'12',
        '黄金等投资产品'=>'14',
        '黄金饰品'=>'5',
        '黄金饰品及工艺品'=>'5'
    ]
];

//设置默认值
$model->default = [
    'isXianhuo'=>'1',
    'is_sale'=>'1',
    'type'=>'1',
    'add_time'=>date('Y-m-d H:i:s')
];

//////////////////////////////////////////////////////////////////////

//获取旧表数据
$model->getOldDate($where,$pass,$table2,$no,$field_arr);

$data = $model->old_data;

if(empty($data)){
    echo "not data to update";
    exit;
}else{
//	 print_r(count($data));exit;
//     print_r($data[0]);exit;
    $model->insertData();
}

////////////////////////////////////////////////////////////////
//导完旧数据之后，更新 style_id 字段
// $model->updateNewItem('style_id','style_sn','base_style_info','1000');





?>