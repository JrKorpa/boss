<?php
// 老款的款属性
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
//此文件是where条件
include 'style_where.php';
include 'cf.php'; 
$conOld = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$conNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');

$old_style = 'style_style';	
$new_table = "rel_style_attribute";
$attr_table = 'app_attribute';
$attr_table_value = 'app_attribute_value';

//属性对应的属性id
$attribute_data  = array(
		//"材质"=>'3',
		"表面工艺"=>'face_work',//
		"能否刻字"=>'could_world',//
        //"证书"=>'zhengshu',//v2里没有单新项目有数据
       // "18K可做颜色"=>'kezuo_yanse',
       //"镶口"=>'1',
       //"指圈"=>'5',
 );

$_style_face_work = array("1" => "磨砂","2" => "光面","3" => "特殊","4" => "拉沙","5" => "钉沙");

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
    $new_attr_data[$row['attribute_name']]= $row['attribute_id'];//[属性名]=>属性id
    $show_type_arr[$row['attribute_name']]= $row['show_type'];//[属性名]=>展示方式
} 

//2、新数据库：获取所有属性值
$sql = "select * from `front`." . $attr_table_value;
$res = mysqli_query($conNew,$sql);
$new_attr_value_data = array ();
while ( $row = mysqli_fetch_array($res) ){
    $attr_id = $row['attribute_id'];
    $value_id = $row['att_value_id'];
    $value_name = $row['att_value_name'];
    $new_attr_value_data[$attr_id][$value_name] = $value_id;//[属性id][属性值名称]=>属性值id
}

$date_time = date("Y-m-d H:i:s");
$field = " `cat_type_id`, `product_type_id`, `style_sn`, `attribute_id`,  `show_type`, `create_time`, `create_user`, `info`, `style_id` ";

$new_cat_type_arr=array(1=>"戒指",2=>3,3=>4,4=>5,5=>5,6=>5,7=>6,8=>7,9=>9,13=>8,10=>14,11=>14,12=>14);//新项目中款式分类的id
$cat_jiezhi_arr = array("W"=>2,"M"=>10,"X"=>11);//新项目中款式分类的id：2=>"女戒",10=>"男戒",11=>"情侣戒"

//旧的数据
$old_table = 'style_style';
$sql = "SELECT count(*) FROM " . $old_table . "  WHERE `is_new` =0 and style_sn='KLRW028164' ";
$sql = "SELECT count(*) FROM " . $old_table . "  WHERE `is_new` =0  ".$where;
$t = mysqli_query($conOld,$sql);
$cnt = mysqli_fetch_row($t);

$len = $cnt[0];
$forsize = ceil($len / 1000);
for($ii = 1; $ii <= $forsize; $ii ++){
    echo $ii."----------------------------------------------------------"."\n";
    $offset = ($ii - 1) * 1000;
    $sql = "SELECT *  FROM " . $old_style . "   WHERE `is_new`=0 and style_sn='KLRW028164' limit $offset,1000";
    $sql = "SELECT *  FROM " . $old_style . "   WHERE `is_new`=0 ".$where." limit $offset,1000";
    $res = mysqli_query($conOld,$sql);
    $old_style_data = array ();
    while ( $row = mysqli_fetch_array($res) ){
		$style_id = $row['style_id'];
		$style_sn = $row['style_sn'];
		$sex = $row['style_for_cat'];
        $old_product_type= $row['pro_line'];
        $old_cat_type =  $row['style_cat'];
        $style_sex =  $row['style_sex'];//2女 1男 0中性
       
       // 款式信息
        $attr_val["style"] = unserialize($row["style_cat_attr"]);//数据库的值
        $old_attr["style"] = $_style_cat[$old_cat_type]["attr"];
        
       // $show_type = $v1_show_type[$attr["style"][$old_attr_id]["type"]];
        foreach ( $old_attr["style"] as $a_key => $a_val ){
            //属性值的id
            $old_attr_id = $a_key;
            $attr_name =  $a_val['item_name'];
            $new_attr_id = $new_attr_data[$attr_name];
            $new_value = '';
            
            //含扣的重新取
            if($attr_name == "含扣"){/*{"1": "2.4",  "2": "0.8", "3": "1"}*/
                $new_value_id ='';
               if(isset($attr_val["style"][3]) && $attr_val["style"][3]==1){
                   $new_value_id =237;
               }
               if(empty($new_attr_id)){
                   continue;
               }
               $sql = "update ".$new_table." set `attribute_value`= '".$new_value_id."'  where `attribute_id`= ".$new_attr_id." and `style_sn`='".$style_sn."' ";
           
                if(!mysqli_query($conNew,$sql)){
                    echo "\n\n\n".$sql."\n\n\n";exit;
                }else {
                    echo "1\n";continue;
                }
            }
            
            $show_type = $show_type_arr[$attr_name]; 
            if($show_type==1){
                $zhi_value = $attr_val["style"][$old_attr_id];
                if(empty($zhi_value)){
                    continue;
                }
                //新属性 名字一样
                
                $sql = "update ".$new_table." set `attribute_value`= '".$zhi_value."'  where `attribute_id`= ".$new_attr_id." and `style_sn`='".$style_sn."' ";
           
                if(!mysqli_query($conNew,$sql)){
                    echo "\n\n\n".$sql."\n\n\n";exit;
                }else {
                    echo "2\n";continue;
                }
                
            }else if($show_type == 3){//多选
                $zhi_value_arr = $attr_val["style"][$old_attr_id];
                if(empty($zhi_value_arr)){
                    continue;
                }
                //找出对应的属性值
                $new_value_id = "";
                foreach ($a_val['val'] as $va_id => $va_val){
                    if(in_array($va_id, $zhi_value_arr)){
                        $old_value_name = $va_val['item_name'];
                        $new_value_id .= $new_attr_value_data[$new_attr_id][$old_value_name].",";
                    }
                }
                if(empty($new_value_id)){
                    continue;
                }
                
                $sql = "update ".$new_table." set `attribute_value`= '".$new_value_id."'  where `attribute_id`= ".$new_attr_id." and `style_sn`='".$style_sn."' ";
                if(!mysqli_query($conNew,$sql)){
                    echo "\n\n\n".$sql."\n\n\n";exit;
                }else {
                    echo mysqli_insert_id($conNew)."\n";
                }
                
            }else{//单选
                $zhi_value_id = $attr_val["style"][$old_attr_id];
                if(empty($zhi_value_id)){
                    continue;
                }
                //找出对应的属性值
                foreach ($a_val['val'] as $va_id => $va_val){
                    if($zhi_value_id == $va_id){
                        $old_value_name = $va_val;
                    }
                }
                $new_value_id = $new_attr_value_data[$new_attr_id][$old_value_name];
                $sql = "update ".$new_table." set `attribute_value`= '".$new_value_id."'  where `attribute_id`= ".$new_attr_id." and `style_sn`='".$style_sn."' ";
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

// 根据金托信息判断此素金属于哪种类型：8  K金 ,9  PT; 19  银
//旧的数据：k金：1,8,2; pt:3,4,10; 银:5,6,9
function get_sujin_cat_type($data){
    if($data[1]['selected'] ==1 || $data[8]['selected'] ==1 || $data[2]['selected'] ==1){
        return 8;
    }
    if($data[3]['selected'] ==1 || $data[4]['selected'] ==1 || $data[10]['selected'] ==1){
       return 9; 
    }
    if($data[5]['selected'] ==1 || $data[6]['selected'] ==1 || $data[9]['selected'] ==1){
        return 19;
    }
}


//新的：8  K金 ,9  PT; 19  银
$_style_gold_type = array(
	"1" => array("gold_name" => "9K金", "price"=>"140", "gold_color"=>",1,2,3,", "loss"=>"0.15", "middle" => "10"),
	"8" => array("gold_name" => "14K金", "price"=>"217", "gold_color"=>",1,2,3,", "loss"=>"0.15", "middle" => "13"),
	"2" => array("gold_name" => "18K金", "price"=>"280", "gold_color"=>",1,2,3,", "loss"=>"0.15", "middle" => "13"),
	"3" => array("gold_name" => "PT900", "price"=>"460", "gold_color"=>",1,", "loss"=>"0.15", "middle" => "20"),
	"4" => array("gold_name" => "PT950", "price"=>"465", "gold_color"=>",1,", "loss"=>"0.15", "middle" => "20"),
	"10" => array("gold_name" => "PT999", "price"=>"338.5", "gold_color"=>",1,", "loss"=>"0.15", "middle" => "20"),
	"5" => array("gold_name" => "S925", "price"=>"", "gold_color"=>",1,"),
	"6" => array("gold_name" => "S990", "price"=>"", "gold_color"=>",1,"),
	"7" => array("gold_name" => "千足金", "price"=>"375", "gold_color"=>",2,"),//这个不属于素金
	"9" => array("gold_name" => "千足银", "price"=>"", "gold_color"=>",1,")
);