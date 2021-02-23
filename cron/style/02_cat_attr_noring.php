<?php
// 老款的款属性
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
// define('ROOT_PATH',str_replace('crate_style_info.php', '', str_replace('\\', '/', __FILE__)));
// require_once(ROOT_PATH . 'config/shell_config.php');
include 'cf.php'; 
$conOld = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$conNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');

$old_style = 'style_style';	
$new_cat_table = "rel_cat_attribute";
$attr_table = 'app_attribute';
$attr_table_value = 'app_attribute_value';

//属性对应的属性id
$attribute_data  = array(
        "材质"=>'3',
		"表面工艺"=>'27',//face_work
		"能否刻字"=>'7',//could_world
//         "证书"=>'zhengshu',//v2里没有单新项目有数据
       "18K可做颜色"=>'kezuo_yanse',
//        "镶口"=>'1',
//        "指圈"=>'5',
 );
$is_require_arr =array("镶口"=>'1',"材质"=>'2',"指圈"=>'3',"18K可做颜色"=>'4');

/*原款式分类——新款式分类
戒指——按戒指分类分别男戒、女戒、情侣戒导入
耳钉、耳环、耳坠——耳饰
其他——其他。黄金等投资产品下其他-按投资黄金金条导入
*/
$all_attr_arr = array();
$show_type_arr = array();

//取出所有的属性来找到对应出的属性id
$sql = "SELECT * FROM ".$attr_table;
$res = mysqli_query($conNew, $sql);
$new_attr_data = array();
$new_attr_id_data = array();
while ($row = mysqli_fetch_array($res)){
    $all_attr_arr[$row['attribute_name']]= $row['attribute_id'];
    $show_type_arr[$row['attribute_name']]= $row['show_type'];
} 

$date_time = date("Y-m-d H:i:s");
$field = "`cat_type_id`, `product_type_id`, `attribute_id`, `is_show`, `is_default`, `is_require`, `status`, `attr_type`, `create_time`, `create_user`";

$new_cat_type_arr=array(1=>"戒指",2=>3,3=>4,4=>5,5=>5,6=>5,7=>6,8=>7,9=>9,13=>8,10=>14,11=>14,12=>14);//新项目中款式分类的id
$cat_jiezhi_arr = array("W"=>2,"M"=>10,"X"=>11);//新项目中款式分类的id：2=>"女戒",10=>"男戒",11=>"情侣戒"

$sql = "SELECT pro_line, style_cat FROM ".$old_style." WHERE style_cat !=1 and is_new =0  GROUP BY pro_line, style_cat";
$res = mysqli_query($conOld,$sql);
$old_style_data = array ();
while ( $row = mysqli_fetch_array($res) ){
    $old_product_type= $row['pro_line'];
    $old_cat_type =  $row['style_cat'];
    $product_type = $new_pro_line[$old_product_type];
    $cat_type = $new_cat_type_arr[$old_cat_type];
     //如果是戒指的
    if($old_cat_type ==1 ){
        continue;
    }
    //如果产品线是素金=>2 ,需要根据金托信息来对产品线:8  K金 ,9  PT; 19  银
    if($old_product_type == 2){
        $sujin_type_arr = array(8,9,19);
        $caizhi_yanse = array();
       
        foreach ($sujin_type_arr as $sj_val){
            $product_type = $sj_val;
            
            foreach ($attribute_data as $a_key=>$a_val){
                $attr_name = $a_key;
                $show_type= $show_type_arr[$attr_name];
                $is_require = 0;
                $attr_type =1;
                if(array_key_exists($attr_name, $is_require_arr)){
                    $is_require = 1;
                    $attr_type =2;
                }
                
                $attr_id = $all_attr_arr[$attr_name];
                $sql_1 = "select `rel_id` from ".$new_cat_table." where `cat_type_id`=".$cat_type." and `product_type_id`=".$product_type." and `attribute_id` = ".$attr_id;
                $res_1 = mysqli_query($conNew,$sql);
                $row_1 = mysqli_fetch_array($res_1);
                if(!empty($row_1)){
                    continue;
                }
                $sql = "INSERT INTO `".$new_cat_table."` (" . $field . ")  VALUES ('" . $cat_type . "', '" . $product_type . "','" . $attr_id . "','".$show_type."' ,1,$is_require,1,$attr_type,'".$date_time."',  'admin')";
                if(!mysqli_query($conNew,$sql)){
                    write_error("18-conmon---: ".$sql."\n\n");//写入错误数据
                    echo "\n\n\n".$sql."\n\n\n";exit;
                }else {
                    echo mysqli_insert_id($conNew)."\n";
                }
            }
            
         foreach ($_style_cat[$old_cat_type]['attr'] as $val){
                $attr_name = $val['item_name'];
                $attr_id = $all_attr_arr[$attr_name];
                $show_type = $show_type_arr[$attr_name];
                $is_require =0;
                $attr_type=1;
                $sql_1 = "select `rel_id` from ".$new_cat_table." where `cat_type_id`=".$cat_type." and `product_type_id`=".$product_type." and `attribute_id` = ".$attr_id;
                $res_1 = mysqli_query($conNew,$sql);
                $row_1 = mysqli_fetch_array($res_1);
                if(!empty($row_1)){
                    continue;
                }
                $sql = "INSERT INTO `".$new_cat_table."` (" . $field . ")  VALUES ('" . $cat_type . "', '" . $product_type . "','" . $attr_id . "','".$show_type."' ,1,$is_require,1,$attr_type,'".$date_time."',  'admin')";
                if(!mysqli_query($conNew,$sql)){
                    write_error("18-other---: ".$sql."\n\n");//写入错误数据
                    echo "\n\n\n".$sql."\n\n\n";
                }else {
                    echo mysqli_insert_id($conNew)."\n";
                }
            }
        }
    }else{
        foreach ($attribute_data as $a_key=>$a_val){
                $attr_name = $a_key;
                $show_type= $show_type_arr[$attr_name];
                $is_require = 0;
                $attr_type =1;
                if(array_key_exists($attr_name, $is_require_arr)){
                    $is_require = 1;
                    $attr_type =2;
                }
                
                $attr_id = $all_attr_arr[$attr_name];
                $sql_1 = "select `rel_id` from ".$new_cat_table." where `cat_type_id`=".$cat_type." and `product_type_id`=".$product_type." and `attribute_id` = ".$attr_id;
                $res_1 = mysqli_query($conNew,$sql);
                $row_1 = mysqli_fetch_array($res_1);
                if(!empty($row_1)){
                    continue;
                }
                $sql = "INSERT INTO `".$new_cat_table."` (" . $field . ")  VALUES ('" . $cat_type . "', '" . $product_type . "','" . $attr_id . "','".$show_type."' ,1,$is_require,1,$attr_type,'".$date_time."',  'admin')";
                if(!mysqli_query($conNew,$sql)){
                    write_error("18-conmon---: ".$sql."\n\n");//写入错误数据
                    echo "\n\n\n".$sql."\n\n\n";exit;
                }else {
                    echo mysqli_insert_id($conNew)."\n";
                }
            }
        
         if(!isset($_style_cat[$old_cat_type])){
             continue;
         }
         
         foreach ($_style_cat[$old_cat_type]['attr'] as $val){
            $attr_name = $val['item_name'];
            $attr_id = $all_attr_arr[$attr_name];
            $show_type = $show_type_arr[$attr_name];
            $is_require =0;
            $attr_type=1;
            $sql_1 = "select `rel_id` from ".$new_cat_table." where `cat_type_id`=".$cat_type." and `product_type_id`=".$product_type." and `attribute_id` = ".$attr_id;
            $res_1 = mysqli_query($conNew,$sql);
            $row_1 = mysqli_fetch_array($res_1);
            if(!empty($row_1)){
                continue;
            }
            $sql = "INSERT INTO `".$new_cat_table."` (" . $field . ")  VALUES ('" . $cat_type . "', '" . $product_type . "','" . $attr_id . "','".$show_type."' ,1,$is_require,1,$attr_type,'".$date_time."',  'admin')";
            if(!mysqli_query($conNew,$sql)){
                write_error("18-other---: ".$sql."\n\n");//写入错误数据
                echo "\n\n\n".$sql."\n\n\n";
            }else {
                echo mysqli_insert_id($conNew)."\n";
            }
        }
    }

}
echo "\n\n\n\n完成ok！";
die();

function writefile($file,$info) {
    for($i=0;$i<10;$i++){
        $fh = fopen($file, "a");
        echo fwrite($fh, $info);    // 输出：6
    }
    fclose($fh);
}