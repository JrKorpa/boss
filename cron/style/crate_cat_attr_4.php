<?php
/*
 * 产品线，款式分类对应的属性
 */
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
// define('ROOT_PATH',str_replace('crate_style_info.php', '', str_replace('\\', '/', __FILE__)));
include ('Pinyin.class.php');
include ('cf.php');
// require_once(ROOT_PATH . 'config/shell_config.php');
$pinyin_obj = new Pinyin();
$conOld = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$conNew =  mysqli_connect('192.168.1.97','cuteman','QW@W#RSS33#E#');


$sql2 = "select * from `rel_cat_attribute` where `product_type_id`=6 and `cat_type_id`=2 and `attribute_id`=1";
$res11 = mysqli_query($conNew,$sql2);

$row = mysqli_num_rows($res11);



$table_arr = 'app_attribute';
$table_arr_value = 'app_attribute_value';
$rel_cat_attr_table = 'rel_cat_attribute';
$old_table = 'style_style';
$t_sql = "TRUNCATE TABLE front.$rel_cat_attr_table ";
$t_res = mysqli_query($conNew,$t_sql);
if($t_res){
    echo "数据已经清空\n";
}

// 旧款式库：的属性
$old_attribute_arr = array ();
foreach ( $attribute_arr['戒指'] as $key => $val ){
    $old_attribute_arr[$val] = $key;
}

$sql = "SELECT count(1) FROM " . $old_table . "  WHERE `is_new` =0";
$t = mysqli_query($conOld,$sql);
$cnt = mysqli_fetch_row($t);


$len = $cnt[0];

$pagesize = 1000;
$forsize = ceil($len / $pagesize);
//$forsize = 1;
for($ii = 1; $ii <= $forsize; $ii ++){
    $offset = ($ii-1)*$pagesize;
    $sql = "SELECT * FROM kela_style." . $old_table . "  WHERE `is_new` =0  limit $offset,$pagesize";
    $res = mysqli_query($conOld,$sql);
    $old_data = array ();
    while ( $row = mysqli_fetch_array($res) ){
        $product_type = $row['pro_line'];
        $cat_type = $row['style_cat'];
        $old_data[$product_type][$cat_type] = $cat_type;
    }
    
    // 取出所有的属性来找到对应出的属性id
    $sql = "SELECT * FROM front." . $table_arr;
    $res = mysqli_query($conNew,$sql);
    $new_attr_data = array ();
    while ( $row = mysqli_fetch_array($res) ){
        $new_attr_data[$row['attribute_name']] = $row['attribute_id'];
    }

    $date_time = date("Y-m-d H:i:s");
    // 先清空产品线，及款式分类对应的属性
    
    echo $ii."\n";
    
    // 老的数据库字段对应新的属性
    $field = "`cat_type_id`, `product_type_id`, `attribute_id`, `is_show`, `is_default`, `is_require`, `status`, `attr_type`, `create_time`, `create_user`, `info`, `default_val`";
    
	
    // 获取所有的老数据然后看时有几种产品及款式分类过略出来
	foreach ( $old_data as $key => $val ){
        $product_type = $new_pro_line[$key];
        foreach ( $val as $n_key => $n_val ){
            $cat_type = $n_key;
            if($cat_type == 1){ // 戒指
                foreach ( $attribute_arr['戒指'] as $attr_name => $attr_val ){
                    $attr_id = $new_attr_data[$attr_name];
                    $is_show = 1;
                    $is_default = 1;
                    $is_require = 1;
                    $type = 1;
					$sql2 = "select * from `front`.`" . $rel_cat_attr_table . "` where `product_type_id`=".$product_type." and `cat_type_id`=2 and `attribute_id`=".$attr_id;
					echo $sql2."<br>@@@";
					$res11 = mysqli_query($conNew,$sql2);
					$row = mysqli_num_rows($res11);
					
					if($row){
					  continue;
					}
                    $sql = "INSERT INTO `front`.`" . $rel_cat_attr_table . "` (" . $field . ")  VALUES ( '2', '" . $product_type . "','" . $attr_id . "',$is_show ,$is_default, $is_require, 1, $type, '" . $date_time . "', 'addmin', '','')";
					echo $sql."\n";
                    if(!mysqli_query($conNew,$sql)){
                        echo $sql;exit;
                    };
                }
            }
        }
    }
    //var_dump($attribute_arr);exit;
    // 戒指类的

  foreach ( $old_data as $o_key => $o_val ){
        $product_type = $new_pro_line[$o_key];
        foreach ( $o_val as $n_val ){
            $cat_type = $n_val;
            if($cat_type != 1){ // 非戒指
//                 var_dump($_style_cat[$cat_type]['attr'] );exit;
                if(!isset($_style_cat[$cat_type])) continue;
                foreach ( $_style_cat[$cat_type]['attr'] as $tt_val ){
                    $attr_name = $tt_val['item_name'];
                    $attr_id = $new_attr_data[$attr_name];
                    $is_show = 1;
                    $is_default = 1;
                    $is_require = 1;
                    $type = 1;
                    $cat_type+=1;

					$sql2 = "select * from `front`.`" . $rel_cat_attr_table . "` where `product_type_id`=".$product_type." and `cat_type_id`=2 and `attribute_id`=".$attr_id;
					echo $sql2."<br>@@@";
					$res11 = mysqli_query($conNew,$sql2);
					$row = mysqli_num_rows($res11);
					
					if($row){
					  continue;
					}
                    
                    $sql = "INSERT INTO `front`.`" . $rel_cat_attr_table . "` (" . $field . ")  VALUES ( '" . $cat_type . "', '" . $product_type . "','" . $attr_id . "',$is_show ,$is_default, $is_require, 1, $type, '" . $date_time . "', 'addmin', '','')";
                    if(!mysqli_query($conNew,$sql)){
                        echo $sql;exit;
                    }else{
                        echo mysqli_insert_id($conNew)."\n";
                    };
                }
            }
        }
    }

}
