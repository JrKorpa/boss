<?php
/*
 * 创建属性
 */
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
// define('ROOT_PATH',str_replace('crate_style_info.php', '', str_replace('\\', '/', __FILE__)));
include ('Pinyin.class.php');



$conNew = mysqli_connect('192.168.1.97','cuteman','QW@W#RSS33#E#');


// require_once(ROOT_PATH . 'config/shell_config.php');
$pinyin_obj = new Pinyin();

include ('cf.php');
// 款式基本信息
$table = 'app_attribute';
mysqli_select_db($conNew,'front');

$t_sql = "TRUNCATE TABLE front.$table ";
$t_res = mysqli_query($conNew,$t_sql);
if($t_res){
    echo "数据已经清空\n";
}

$field = " `attribute_name`, `attribute_code`, `show_type`, `attribute_status`, `create_time`, `create_user`, `attribute_remark`";

// 这个主要时戒指v3的属性
$date_time = date("Y-m-d H:i:s");
foreach ( $attribute_arr['戒指'] as $key => $val ){
    $attr_name = $key;
    $attr_code = $pinyin_obj->getQianpin($key);
    $show_type = $attr_show_type_arr[$attr_name];
    $sql = "SELECT count(*) FROM `front`." . $table . " WHERE attribute_name='$attr_name'";
    $x = mysqli_query($conNew,$sql);
    $cnt = mysqli_fetch_row($x);
    if(! $cnt[0]){
        $sql = "INSERT INTO `" . $table . "` (" . $field . ")  VALUES ( '" . $attr_name . "',  '" . $attr_code . "', $show_type, 1, '" . $date_time . "', 'admin','' )";
        if(! mysqli_query($conNew,$sql)){
            echo $sql;
            exit();
        }
    }
}

// v1老系统的属性
// '1=>''文本框'',2=>''单选'',3=>''多选'',4=>''下拉列表''',
$v1_show_type = array (
        'text' => 1,
        'radio' => 2,
        'checkbox' => 3 
);
// 把款式分类对属性的
foreach ( $_style_cat as $key => $val ){
    if($key > 1){
        foreach ( $val['attr'] as $z_key => $z_val ){
            $attr_name = $z_val['item_name'];
            $type = $z_val['type'];
            $show_type = $v1_show_type[$type];
            $attr_code = $pinyin_obj->getQianpin($attr_name);
            $sql = "SELECT count(*) FROM `front`." . $table . " WHERE attribute_name='$attr_name'";
            $x = mysqli_query($conNew,$sql);
            $cnt = mysqli_fetch_row($x);
            if(! $cnt[0]){
                $sql = "INSERT INTO `front`.`" . $table . "` (" . $field . ")  VALUES ( '" . $attr_name . "',  '" . $attr_code . "', $show_type, 1, '" . $date_time . "', 'admin','' )";
                if(! mysqli_query($conNew,$sql)){
                    echo $sql;
                    exit();
                }
            }
        }
    }
}


echo "属性完成";

