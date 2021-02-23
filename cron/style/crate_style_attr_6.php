<?php
// v2,v3 款属性
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
// define('ROOT_PATH',str_replace('crate_style_info.php', '', str_replace('\\', '/', __FILE__)));
// require_once(ROOT_PATH . 'config/shell_config.php');

$conOld = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$conNew =  mysqli_connect('192.168.1.97','cuteman','QW@W#RSS33#E#');

include 'cf.php';


$xiangkou_arr = array (
        "0.10" => 1,
        "0.15" => 1,
        "0.20" => 1,
        "0.25" => 1,
        "0.30" => 1,
        "0.40" => 1,
        "0.50" => 1,
        "0.60" => 1,
        "0.70" => 1,
        "0.80" => 1,
        "0.90" => 1,
        "1.00" => 1,
        "1.10" => 1,
        "1.20" => 1,
        "1.30" => 1,
        "1.40" => 1,
        "1.50" => 1,
        "2.00" => 1 
);
$zhiquan_arr = array (
        "6-8" => 1,
        "9-10" => 1,
        "11-13" => 1,
        "14-15" => 1,
        "16-26" => 1 
);
$style_caizhi_arr = array (
        "18K" => 1,
        "PT950" => 2 
);
$is_weizuan_arr = array (
        "主钻" => 1,
        "群镶有主钻" => 2,
        "群镶无主钻" => 3,
        "其它" => 4 
);
$is_kezi_arr = array (
        "是" => 1,
        "否" => 2 
);
$style_xiangqian_arr = array (
        "爪镶" => 1,
        "包镶" => 2,
        "夹镶" => 3,
        "槽镶" => 4,
        "钉镶" => 5,
        "轨道镶" => 6,
        "其它" => 7 
);
$zhua_num_arr = array (
        "无" => 0,
        "一爪" => 1,
        "二爪" => 2,
        "三爪" => 3,
        "四爪" => 4,
        "五爪" => 5,
        "六爪" => 6,
        "七爪" => 7,
        "八爪" => 8,
        "九爪" => 9,
        "十爪" => 10,
        "十一爪" => 11,
        "十二爪" => 12,
        "其它" => 13 
);
$is_zhizhua_arr = array (
        "是" => 1,
        "否" => 2 
);
$zhua_xingzhuang_arr = array (
        "圆形" => 1,
        "心形" => 7,
        "V形" => 8,
        "水滴形" => 2,
        "三角形" => 5,
        "菱形" => 9,
        "方形" => 3,
        "梯形" => 4,
        "其它" => 10 
);
$zhua_daizuan_arr = array (
        "有钻" => 1,
        "无钻" => 2 
);
$bi_xingtai_arr = array (
        "直臂" => 1,
        "扭臂" => 2,
        "交臂" => 3,
        "高低臂" => 4,
        "其它" => 5 
);
$bi_daizuan_arr = array (
        "带副石" => 1,
        "不带副石" => 2 
);
$zhu_shi_arr = array (
        "无主石" => 13,
        "圆形" => 11,
        "祖母绿形" => 3,
        "椭圆形" => 6,
        "马眼形" => 14,
        "梨形" => 12,
        "心形" => 4,
        "垫形" => 1,
        "公主方形" => 2,
        "三角形" => 8,
        "水滴形" => 9,
        "长方形" => 10,
        "其它" => 5 
);
$fu_shi_arr = array (
        "圆形" => 1,
        "祖母绿形" => 2,
        "椭圆形" => 3,
        "马眼形" => 4,
        "梨形" => 5,
        "心形" => 6,
        "垫形" => 7,
        "公主方形" => 8,
        "三角形" => 9,
        "水滴形" => 10,
        "长方形" => 11,
        "其它" => 12 
);
$jiebi_gongyi_arr = array (
        "光面" => 1,
        "磨砂" => 2,
        "拉丝" => 3,
        "光面&磨砂" => 4,
        "光面&拉丝" => 5,
        "其它" => 6 
);
$is_fushi_arr = array (
        "带副石" => 1,
        "无副石" => 2 
);
$style_gaiquan_arr = array (
        "不可改圈" => 0,
        "可增减1个手寸" => 1,
        "可增减2个手寸" => 2,
        "可增1个手寸" => 3,
        "可增2个手寸" => 4 
);
$kezuo_yanse_arr = array (
        "白色"=>1,
        "黄色"=>2,
        "红色"=>3,
        "分色"=>4 
);
$zhengshu_arr = array (
        "AGS" => 1,
        "EGL" => 2,
        "GAC" => 3,
        "GemEx" => 4,
        "GIA" => 5,
        "GIC" => 6,
        "HRD" => 7,
        "IGI" => 8,
        "NGGC" => 9,
        "NGSTC" => 10,
        "其它" => 11 
);
$zhua_xingtai_arr = array (
        "直" => 1,
        "扭" => 2,
        "花型" => 3,
        "雪花" => 4,
        "无" => 5 
);

$table_arr = 'app_attribute';
$table_arr_value = 'app_attribute_value';
$rel_cat_attr_table = 'rel_cat_attribute';
$rel_style_attr_table = 'rel_style_attribute';

// 老款式库
// 旧款式库：的属性
$old_attribute_arr = array ();
foreach ( $attribute_arr['戒指'] as $key => $val ){
    $old_attribute_arr[$val] = $key;
}

// 先清款式对应的属性
$t_sql = "TRUNCATE TABLE `front`.$rel_style_attr_table ";
$t_res = mysqli_query($conNew,$t_sql);
if($t_res){
    echo $rel_style_attr_table . "数据已经清空\n\n\n\n";
}

// 取出所有的属性来找到对应出的属性id
$sql = "SELECT * FROM `front`." . $table_arr;
$res = mysqli_query($conNew,$sql);
$new_attr_data = array ();
$new_attr_id_data = array ();
while ( $row = mysqli_fetch_array($res) ){
    $new_attr_data[$row['attribute_name']] = $row['attribute_id'];
    $new_attr_id_data[$row['attribute_id']] = $row['attribute_name'];
}
// var_dump($new_attr_data);exit;
// 获取所有属性值
$sql = "select * from `front`." . $table_arr_value;
$res = mysqli_query($conNew,$sql);
$new_attr_value_data = array ();
while ( $row = mysqli_fetch_array($res) ){
    $attr_id = $row['attribute_id'];
    $value_id = $row['att_value_id'];
    $value_name = $row['att_value_name'];
    $new_attr_value_data[$attr_id][$value_name] = $value_id;
}
// 遍历戒指的所有属性
$old_attr_value_arr = array ();
foreach ( $attribute_arr['戒指'] as $key => $val ){
    $attr_name = $key;
    $attr_value_arr = $val . "_arr";
    $attr_id = $new_attr_data[$attr_name];
    // 属性对应多种属性值
    foreach ( $$attr_value_arr as $v_key => $v_val ){
        $attr_value_name = $v_key;
        $attr_value_zhi = $v_val;
        $old_attr_value_arr[$attr_id][$attr_value_zhi] = $attr_value_name;
    }
}

// 取出所有v3款式的属性：及数据库字段（属性） ;值为属性对应的值
$old_table = 'style_style';
$sql = "SELECT count(*) FROM kela_style." . $old_table . "  WHERE `is_new` !=0  and `is_confirm`!=4";
$t = mysqli_query($conOld,$sql);
$cnt = mysqli_fetch_row($t);

$len = $cnt[0];
$forsize = ceil($len / 1000);
for($ii = 1; $ii <= $forsize; $ii ++){
    echo $ii."----------------------------------------------------------"."\n";
    $offset = ($ii - 1) * 1000;
    $sql = "SELECT * FROM kela_style." . $old_table . "  WHERE `is_new` !=0  and `is_confirm`!=4 limit $offset,1000";
    $res = mysqli_query($conOld,$sql);
    $old_style_data = array ();
    while ( $row = mysqli_fetch_array($res) ){
        $old_style_data[] = $row;
    }
    
    // 钻石：产品线id；戒指;分类id
    $cat_type_id = 2;
    $product_type_id = 6;
    $date_time = date("Y-m-d H:i:s");
    $field = " `cat_type_id`, `product_type_id`, `style_sn`, `attribute_id`, `attribute_value`, `show_type`, `create_time`, `create_user`, `info`, `style_id`";
    // 单选：下拉列表的
    foreach ( $old_style_data as $s_val ){
        $style_id = $s_val['style_id'];
        $style_sn = $s_val['style_sn'];
        $is_new = $s_val['is_new'];
        foreach ( $old_attribute_arr as $a_key => $a_val ){
            $attr_name = $a_val;
//             if($attr_name =="材质") continue;
//             echo $attr_name;
            if(!isset($s_val[$a_key]))  continue;
            $value_zhi = $s_val[$a_key];//数据存在的zhi
            $new_attr_value_id = '';
            if($value_zhi != ''){
                $attr_id = $new_attr_data[$attr_name];
                if(!isset($old_attr_value_arr[$attr_id][$value_zhi])) continue;
                $attr_value_name = $old_attr_value_arr[$attr_id][$value_zhi];
//                 var_dump($new_attr_value_data);
//                 var_dump($new_attr_value_data[$attr_id]);exit;
                $new_attr_value_id = $new_attr_value_data[$attr_id][$attr_value_name];
            }
            $show_type = $attr_show_type_arr[$attr_name];
            if($show_type == 4){ // 下拉列表
                if($attr_name == "zhua_xingtai" && $s_val['zhua_xingtai'] == ""){
                    $new_attr_value_id = $new_attr_value_data[$attr_id]["无"];
                }
                
                $sql = "INSERT INTO `front`.`" . $rel_style_attr_table . "` (" . $field . ")  VALUES ( '" . $cat_type_id . "', '" . $product_type_id . "','" . $style_sn . "','" . $attr_id . "','" . $new_attr_value_id . "' ,$show_type,  '" . $date_time . "', 'addmin', '',$style_id)";
                if(!mysqli_query($conNew,$sql)){
                    echo "\n\n\n".$sql."\n\n\n";exit;
                }else {
                    echo mysqli_insert_id($conNew)."\n";
                }
            }
        }
    }
    
    // 多选：复选框
    foreach ( $old_style_data as $s_val ){
        $style_id = $s_val['style_id'];
        $style_sn = $s_val['style_sn'];
        foreach ( $old_attribute_arr as $a_key => $a_val ){
            $attr_name = $a_val;
            $value_zhi = $a_val[$a_key];
            $new_attr_value_id = '';
            $show_type = $attr_show_type_arr[$attr_name];
            if($show_type == 3){ // 下拉列表
                if($value_zhi != ''){
                    $tmp_value_id_arr = explode(',',$value_zhi);
                    foreach ( $tmp_value_id_arr as $t_val ){
                        if($t_val){
                            $tmp_zhi = $t_val;
                            $attr_id = $new_attr_data[$attr_name];
                            $attr_value_name = $old_attr_value_arr[$attr_id][$tmp_zhi];
                            $new_attr_value_id .= $new_attr_value_data[$attr_id][$attr_value_name] . ',';
                        }
                    }
                }
                $sql = "INSERT INTO `front`.`" . $rel_style_attr_table . "` (" . $field . ")  VALUES ( '" . $cat_type_id . "', '" . $product_type_id . "','" . $style_sn . "','" . $attr_id . "','" . $new_attr_value_id . "' ,$show_type,  '" . $date_time . "', 'addmin', '',$style_id)";
                if(!mysqli_query($conNew,$sql)){
                    echo "\n\n\n".$sql."\n\n\n";exit;
                }else {
                    echo mysqli_insert_id($conNew)."\n";
                }
            }
        }
    }
}
echo "\n\n\n\n完成ok！";
die();
