<?php
// v1 款属性
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
// define('ROOT_PATH',str_replace('crate_style_info.php', '', str_replace('\\', '/', __FILE__)));
// require_once(ROOT_PATH . 'config/shell_config.php');

$conOld = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$conNew =  mysqli_connect('192.168.1.97','cuteman','QW@W#RSS33#E#');

include 'cf.php';

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

// 取出所有v3款式的属性：及数据库字段（属性） ;值为属性对应的值
$table_arr = 'app_attribute';
$table_arr_value = 'app_attribute_value';
$rel_cat_attr_table = 'rel_cat_attribute';
$rel_style_attr_table = 'rel_style_attribute';

// 取出所有的属性来找到对应出的属性id
$sql = "SELECT * FROM front." . $table_arr;
$res = mysqli_query($conNew,$sql);
$new_attr_data = array ();
$new_attr_id_data = array ();
while ( $row = mysqli_fetch_array($res) ){
    $new_attr_data[$row['attribute_name']] = $row['attribute_id'];
    $new_attr_id_data[$row['attribute_id']] = $row['attribute_name'];
}

// 获取所有属性值
$sql = "select * from front." . $table_arr_value;
$res = mysqli_query($conNew,$sql);
$new_attr_value_data = array ();
while ( $row = mysqli_fetch_array($res) ){
    $attr_id = $row['attribute_id'];
    $value_id = $row['att_value_id'];
    $value_name = $row['att_value_name'];
    $new_attr_value_data[$attr_id][$value_name] = $value_id;
}

// 获取所有产品线及分类的属性
$sql = "select * from front." . $rel_cat_attr_table . " ";
$res = mysqli_query($conNew,$sql);
$new_pro_cat_attr_data = array ();
while ( $row = mysqli_fetch_array($res) ){
    $attr_id = $row['attribute_id'];
    $product_type = $row['product_type_id'];
    $cat_type = $row['cat_type_id'];
    // $new_pro_cat_attr_data[$product_type][$cat_type][$attr_id] = $attr_id;
    // $new_pro_cat_attr_data[$cat_type][$attr_id] = $new_attr_value_data[$attr_id];
    $new_pro_cat_attr_data[$cat_type][$new_attr_id_data[$attr_id]] = $attr_id;
}

$new_pro_line = array (
        "0" => 5, // 其他(原名)
        "1" => 14,
        "2" => 4,
        "3" => 13, // 黄金饰品(原名)
        "4" => 6, // 结婚钻石饰品
                  // "5" => array("item_name" => "钻石饰品"),
        "6" => 15,
        "7" => 17, // 彩宝及翡翠饰品(原名)
        "8" => 6,
        "9" => 16,
        "10" => 10,
        "11" => 12 
);

$v1_show_type = array (
        'text' => 1,
        'radio' => 2,
        'checkbox' => 3 
);
$data_time = date("Y-m-d H:i:s");

$old_table = 'style_style';
$sql = "SELECT count(*) FROM kela_style." . $old_table . " ";
$t = mysqli_query($conOld,$sql);
$cnt = mysqli_fetch_row($t);

$len = $cnt[0];
$forsize = ceil($len / 1000);
for($ii = 1; $ii <= $forsize; $ii ++){
    $offset = ($ii - 1) * 1000;
    $sql = "SELECT * FROM kela_style." . $old_table . "  limit $offset,1000";
    $res = mysqli_query($conOld,$sql);
    $old_style_data = array ();
    while ( $row = mysqli_fetch_array($res) ){
        $old_style_data[] = $row;
    }
    
    $field = " `cat_type_id`, `product_type_id`, `style_sn`, `attribute_id`, `attribute_value`, `show_type`, `create_time`, `create_user`, `info`, `style_id`";
    $cat_attr_show_type = array ();
    foreach ( $_style_cat as $s_key => $s_val ){
        $cat_type = $s_key+1;
        foreach ( $s_val['attr'] as $n_key => $n_val ){
            $old_attr_id = $n_key;
            $old_attr_name = $n_val['item_name'];
            $old_attr_all[$cat_type][$old_attr_id] = $old_attr_name;
            // 取数据库中属性id
            //$attr_id = $cat_attr_show_typenew_pro_cat_attr_data[$cat_type][$old_attr_name];
//             var_dump($new_pro_cat_attr_data);
//             var_dump($new_pro_cat_attr_data[$cat_type]);
//             var_dump($new_pro_cat_attr_data[$cat_type][$old_attr_name]);exit;
//             var_dump($cat_type);
//             var_dump($old_attr_name);exit;
            if($old_attr_name == '手链扣'){
                //var_dump($cat_type);exit;
            }
            $attr_id = $new_pro_cat_attr_data[$cat_type][$old_attr_name];
            $cat_attr_show_type[$cat_type][$old_attr_name] = $v1_show_type[$n_val['type']];
        }
    }
    
    foreach ( $old_style_data as $val ){
        $style_sn = $val['style_sn'];
        $product_type = $new_pro_line[$val['pro_line']];
        $cat_type = $val['style_cat'];
        
        // 款式信息
        $attr_val["style"] = unserialize($val["style_cat_attr"]);
        $attr["style"] = $_style_cat[$cat_type]["attr"];
        
        $show_type = $v1_show_type[$attr["style"][$old_attr_id]["type"]];
        
        foreach ( $attr_val["style"] as $a_key => $a_val ){
            $old_attr_id = $a_key;
            $old_attr_name = $old_attr_all[$cat_type][$old_attr_id];
            $new_attr_id = $new_pro_cat_attr_data[$cat_type][$old_attr_name];
            $new_value = '';
            
            if($attr["style"][$old_attr_id]["type"] == "text"){
                $new_value = $a_val;
            }else if($cat_attr_show_type == "radio"){
                
                if(is_array($attr["style"][$old_attr_id]["val"])){
                    foreach ( $attr["style"][$old_attr_id]["val"] as $ss_key => $ss_val ){
                        if($ss_key == $a_val){
                            $old_value_id = $ss_key;
                            $old_value_name = $ss_val['item_name'];
                            $new_value = $new_attr_value_data[$new_attr_id][$old_value_name];
                        }
                    }
                }
            }else if($cat_attr_show_type == "checkbox"){
                
                if(is_array($attr["style"][$old_attr_id]["val"])){
                    if(is_array($a_val)){ // 选的多个值
                        foreach ( $a_val as $tt_val ){
                            $old_value_id = $tt_val;
                            $old_value_name = $attr["style"][$old_attr_id]["val"][$old_value_id]['item_name'];
                            $new_value_arr[] = $new_attr_value_data[$new_attr_id][$old_value_name];
                        }
                        $new_value = explode($new_value_arr);
                    }
                }
            }
            if(!$new_attr_id) continue;
            if(!$show_type) $show_type=1; 
            $sql = "INSERT INTO `front`.`" . $rel_style_attr_table . "` (" . $field . ")  VALUES ( '" . $cat_type . "', '" . $product_type . "','" . $style_sn . "','" . $new_attr_id . "','" . $new_value . "' ,". $show_type .",  '" . date("Y-m-d H:i:s") . "', 'addmin', '','10001')";
            if(! mysqli_query($conNew,$sql)){
                echo "\n\n\n" . $sql . "\n\n\n";
                exit();
            }else{
                echo mysqli_insert_id($conNew) . "\n";
            }
        }
    }
}
die("ok");
// 更新款id

// 镶口那些东西

// 材质

/*
 * //旧款式库：的属性 $old_attribute_arr = array(); foreach ($attribute_arr['戒指'] as $key=>$val){ $old_attribute_arr[$val] = $key; } //先清款式对应的属性 $t_sql = "TRUNCATE TABLE $rel_style_attr_table "; $t_res = mysqli_query($con, $t_sql); if($t_res){ echo $rel_style_attr_table."数据已经清空"; } //钻石：产品线id；戒指;分类id $cat_type_id = 2; $product_type_id = 6; $date_time = date("Y-m-d H:i:s"); $field =" `cat_type_id`, `product_type_id`, `style_sn`, `attribute_id`, `attribute_value`, `show_type`, `create_time`, `create_user`, `info`, `style_id`"; //单选：下拉列表的 foreach($old_style_data as $s_val){ $style_id = $s_val['style_id']; $style_sn = $s_val['style_sn']; $is_new = $s_val['is_new']; foreach ($old_attribute_arr as $a_key=>$a_val){ $attr_name = $a_val; $value_zhi = $s_val[$a_key]; $new_attr_value_id = ''; if($value_zhi !=''){ $attr_id = $new_attr_data[$attr_name]; $attr_value_name = $old_attr_value_arr[$attr_id][$value_zhi]; $new_attr_value_id = $new_attr_value_data[$attr_id][$attr_value_name]; } $show_type = $attr_show_type_arr[$attr_name]; if($show_type == 4){//下拉列表 if($attr_name =="zhua_xingtai" &&　$s_val['zhua_xingtai']==""){ $new_attr_value_id = $new_attr_value_data[$attr_id]["无"]; } $sql= "INSERT INTO `".$rel_style_attr_table."` (".$field.") VALUES ( '".$cat_type_id."', '".$product_type_id."','".$style_sn."','".$attr_id."','".$new_attr_value_id."' ,$show_type, '".$date_time."', 'addmin', '',$style_id)" ; echo $sql."<br>"; mysqli_query($con,$sql); } } } //多选：复选框 foreach($old_style_data as $s_val){ $style_id = $s_val['style_id']; $style_sn = $s_val['style_sn']; foreach ($old_attribute_arr as $a_key=>$a_val){ $attr_name = $a_val; $value_zhi = $a_val[$a_key]; $new_attr_value_id = ''; $show_type = $attr_show_type_arr[$attr_name]; if($show_type == 3){//下拉列表 if($value_zhi !=''){ $tmp_value_id_arr = explode(',', $value_zhi); foreach ($tmp_value_id_arr as $t_val){ if($t_val){ $tmp_zhi = $t_val; $attr_id = $new_attr_data[$attr_name]; $attr_value_name = $old_attr_value_arr[$attr_id][$tmp_zhi]; $new_attr_value_id .= $new_attr_value_data[$attr_id][$attr_value_name].','; } } } $sql= "INSERT INTO `".$rel_style_attr_table."` (".$field.") VALUES ( '".$cat_type_id."', '".$product_type_id."','".$style_sn."','".$attr_id."','".$new_attr_value_id."' ,$show_type, '".$date_time."', 'addmin', '',$style_id)" ; // mysqli_query($con,$sql); } } }
 */

echo "<br>表已经生成ok";
die();


