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

$face_work_arr = array("1" => "磨砂","2" => "光面","3" => "特殊","4" => "拉砂","5" => "钉砂");
$could_world_arr =array("可刻字"=>0,"不可刻字"=>1);
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

//3、旧数据：对应的原来的值
$old_attr_value_arr = array ();
foreach ( $attribute_data as $key => $val ){
    $attr_name = $key;
    $attr_value_arr = $val . "_arr";
    $attr_id = $new_attr_data[$attr_name];//属性id
    // 属性对应多种属性值
    foreach ( $$attr_value_arr as $v_key => $v_val ){
        $attr_value_name = $v_val;
        $attr_value_zhi = $v_key;
        $old_attr_value_arr[$attr_id][$attr_value_zhi] = $attr_value_name;//[属性id][属性值id]=>属性值
    }
}


//属性
$_style_gold_type = array(
	"1" => array("gold_name" => "9K", "price"=>"140", "gold_color"=>",1,2,3,", "loss"=>"0.15", "middle" => "10"),
	"8" => array("gold_name" => "14K", "price"=>"217", "gold_color"=>",1,2,3,", "loss"=>"0.15", "middle" => "13"),
	"2" => array("gold_name" => "18K", "price"=>"280", "gold_color"=>",1,2,3,", "loss"=>"0.15", "middle" => "13"),
	"3" => array("gold_name" => "PT900", "price"=>"460", "gold_color"=>",1,", "loss"=>"0.15", "middle" => "20"),
	"4" => array("gold_name" => "PT950", "price"=>"465", "gold_color"=>",1,", "loss"=>"0.15", "middle" => "20"),
	"10" => array("gold_name" => "PT999", "price"=>"338.5", "gold_color"=>",1,", "loss"=>"0.15", "middle" => "20"),
	"5" => array("gold_name" => "S925", "price"=>"", "gold_color"=>",1,"),
	"6" => array("gold_name" => "S990", "price"=>"", "gold_color"=>",1,"),
	"7" => array("gold_name" => "千足金", "price"=>"375", "gold_color"=>",2,"),//这个不属于素金
	"9" => array("gold_name" => "千足银", "price"=>"", "gold_color"=>",1,")
);

$date_time = date("Y-m-d H:i:s");
$field = " `cat_type_id`, `product_type_id`, `style_sn`, `attribute_id`,  `show_type`, `create_time`, `create_user`, `info`, `style_id` ";

$new_cat_type_arr=array(1=>"戒指",2=>3,3=>4,4=>5,5=>5,6=>5,7=>6,8=>7,9=>9,13=>8,10=>14,11=>14,12=>14);//新项目中款式分类的id
$cat_jiezhi_arr = array("W"=>2,"M"=>10,"X"=>11);//新项目中款式分类的id：2=>"女戒",10=>"男戒",11=>"情侣戒"

//旧的数据
$old_table = 'style_style';
//$sql = "SELECT count(*) FROM " . $old_table . "  WHERE `is_new` =0 and style_sn='KLRW002865' ";
$sql = "SELECT count(*) FROM " . $old_table . "  WHERE `is_new` =0 and  `style_cat`=1 and style_sn='KLRM027602' ";
$sql = "SELECT count(*) FROM " . $old_table . "  WHERE `is_new` =0 and  `style_cat`!=1 ".$where;
$t = mysqli_query($conOld,$sql);
$cnt = mysqli_fetch_row($t);

$len = $cnt[0];
$forsize = ceil($len / 1000);
for($ii = 1; $ii <= $forsize; $ii ++){
    echo $ii."----------------------------------------------------------"."\n";
    $offset = ($ii - 1) * 1000;
   // $sql = "SELECT *  FROM " . $old_style . "   WHERE `is_new`=0 and style_sn='KLRW028164' limit $offset,1000";
   // $sql = "SELECT *  FROM " . $old_style . "   WHERE `is_new`=0 and style_sn='KLQW028162' limit $offset,1000";
   // $sql = "SELECT *  FROM " . $old_style . "   WHERE `is_new`=0 and style_sn='KLRM027602' limit $offset,1000";
    $sql = "SELECT *  FROM " . $old_style . "   WHERE `is_new`=0 and  `style_cat`!=1 ".$where."  limit $offset,1000";
    $res = mysqli_query($conOld,$sql);
    $old_style_data = array ();
    while ( $row = mysqli_fetch_array($res) ){
		$style_id = $row['style_id'];
		$style_sn = $row['style_sn'];
		$sex = $row['style_for_cat'];
        $old_product_type= $row['pro_line'];
        $old_cat_type =  $row['style_cat'];
        $style_sex =  $row['style_sex'];//2女 1男 0中性
       //材质的单跑
        
        foreach($attribute_data as $key=>$val){
            $attr_name = $key;
			$attr_id = $new_attr_data[$attr_name];//属性id
            $cloumn = $val;
            //旧数据存放的属性值id
            $old_value_id = $row[$cloumn];
            if($old_value_id ==""){
                continue;
            }
            //展示类型
			$show_type= $show_type_arr[$key];
            if($show_type == 3){//多选
                $old_value_arr = explode(",", $old_value_id);
                $new_value_id = "";
             
                foreach ($old_value_arr as $tt){
                    if(empty($tt)){
                        continue;
                    }
                    $old_value_id = $tt;
                    if(!isset($old_attr_value_arr[$attr_id][$old_value_id])){
                        continue;
                    }
                   
                    //由属性值的id推出属性值的名称
                    $old_value_name = $old_attr_value_arr[$attr_id][$old_value_id];
                 
                    //属性值的名称是一样的，由名称推出新的属性值id
                    $new_value_id.= $new_attr_value_data[$attr_id][$old_value_name].",";
                }
                $new_value_id = rtrim($new_value_id,',');
                
                $sql = "update ".$new_table." set `attribute_value`= '".$new_value_id."'  where `attribute_id`= ".$attr_id." and `style_sn`='".$style_sn."' ";
               
            }elseif ($show_type == 1) {//文本框:直接用取出的数据
                $value_zhi = $old_value_id;
                $sql = "update ".$new_table." set `attribute_value`= '".$value_zhi."'  where `attribute_id`= ".$attr_id." and `style_sn`='".$style_sn."' ";
           
            }else{
                //由属性值的id推出属性值的名称
                if(!isset($old_attr_value_arr[$attr_id][$old_value_id])){
                    continue;
                }
                $old_value_name = $old_attr_value_arr[$attr_id][$old_value_id];
                //属性值的名称是一样的，由名称推出新的属性值id
                $new_value_id = $new_attr_value_data[$attr_id][$old_value_name];
                
                $sql = "update ".$new_table." set `attribute_value`= '".$new_value_id."'  where `attribute_id`= ".$attr_id." and `style_sn`='".$style_sn."' ";
            }
            

            if(!mysqli_query($conNew,$sql)){
				echo "\n\n\n".$sql."\n\n\n";exit;
			}else {
				echo "1\n";
			}
        }
            //材质信息
            $metail_info = unserialize($row["metal_info"]);
            if(!empty($metail_info)){
                $select_caizhi = getCaiZhi($metail_info);
                if(!empty($select_caizhi)){
                    //材质属性
                    $cz_attr_id = 3;
                    $new_value_id ="";
                    foreach ($select_caizhi as $cz_val){
                        $new_value_id .= $new_attr_value_data[$cz_attr_id][$cz_val].",";
                    }
                    
                    if(!empty($new_value_id)){
                        $sql = "update ".$new_table." set `attribute_value`= '".$new_value_id."'  where `attribute_id`= ".$cz_attr_id." and `style_sn`='".$style_sn."' ";
                        if(!mysqli_query($conNew,$sql)){
                            echo "\n\n\n".$sql."\n\n\n";exit;
                        }else {
                            echo "2\n";
                        }
                    }
                    
                    //颜色
                    if($metail_info[2]['selected']==1 && $metail_info[4]['selected']==1){
						if($metail_info[2]['selected']==2){
							$sql = "update ".$new_table." set `attribute_value`= '181,183,185,187'  where `attribute_id`= 33 and `style_sn`='".$style_sn."' ";
							if(!mysqli_query($conNew,$sql)){
								echo "\n\n\n".$sql."\n\n\n";exit;
							}else {
								echo "2\n";
							}
						}
					}else{
						if($metail_info[2]['selected']==1){
							$sql = "update ".$new_table." set `attribute_value`= '181,183,185,187'  where `attribute_id`= 33 and `style_sn`='".$style_sn."' ";
							if(!mysqli_query($conNew,$sql)){
								echo "\n\n\n".$sql."\n\n\n";exit;
							}else {
								echo "2\n";
							}
						}

						if($metail_info[4]['selected']==1){
							$sql = "update ".$new_table." set `attribute_value`= '181'  where `attribute_id`= 33 and `style_sn`='".$style_sn."' ";
							if(!mysqli_query($conNew,$sql)){
								echo "\n\n\n".$sql."\n\n\n";exit;
							}else {
								echo "2\n";
							}
						}
                    
					
					}
                    
                }
            }
            
            //戒指的需要判断一下指圈
            if($old_cat_type == 1){
                $style_cat_info = unserialize($row["style_cat_attr"]);
                if(!empty($style_cat_info)){
                    if(array_key_exists(1, $style_cat_info)){
                        $min_finger = $style_cat_info[1]['min'];
                        $max_finger = $style_cat_info[1]['max'];
                        $all_finger = getAllFinger($min_finger, $max_finger);
                        $new_value_id = "";
                        $zq_attr_id = 5;
                        if($all_finger){
                            $select_finger = getZhiQuan($all_finger);
                            if($select_finger){
                                foreach ($select_finger as $s_val){
                                    //指圈的属性id：5
                                    $new_value_id .= $new_attr_value_data[$zq_attr_id][$s_val].",";
                                }
                            }
                            
                            if(!empty($new_value_id)){
                                $sql = "update ".$new_table." set `attribute_value`= '".$new_value_id."'  where `attribute_id`= ".$zq_attr_id." and `style_sn`='".$style_sn."' ";
                                if(!mysqli_query($conNew,$sql)){
                                    echo "\n\n\n".$sql."\n\n\n";exit;
                                }else {
                                    echo "3\n";
                                }
                            }
                        }
                    }
                }
            }
		}
    }
echo "\n\n\n\n完成ok！";
die();

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

function getCaiZhi( $data){
    $_style_gold_type = array(
        "1" => array("gold_name" => "9K", "price"=>"140", "gold_color"=>",1,2,3,", "loss"=>"0.15", "middle" => "10"),
        "8" => array("gold_name" => "14K", "price"=>"217", "gold_color"=>",1,2,3,", "loss"=>"0.15", "middle" => "13"),
        "2" => array("gold_name" => "18K", "price"=>"280", "gold_color"=>",1,2,3,", "loss"=>"0.15", "middle" => "13"),
        "3" => array("gold_name" => "PT900", "price"=>"460", "gold_color"=>",1,", "loss"=>"0.15", "middle" => "20"),
        "4" => array("gold_name" => "PT950", "price"=>"465", "gold_color"=>",1,", "loss"=>"0.15", "middle" => "20"),
        "10" => array("gold_name" => "PT999", "price"=>"338.5", "gold_color"=>",1,", "loss"=>"0.15", "middle" => "20"),
        "5" => array("gold_name" => "S925", "price"=>"", "gold_color"=>",1,"),
        "6" => array("gold_name" => "S990", "price"=>"", "gold_color"=>",1,"),
        "7" => array("gold_name" => "千足金", "price"=>"375", "gold_color"=>",2,"),//这个不属于素金
        "9" => array("gold_name" => "千足银", "price"=>"", "gold_color"=>",1,")
    );
    
    $select_caizhi = array();
    foreach ($data as $key=>$val){
        if($val['selected'] ==1){
            $select_caizhi[] = $_style_gold_type[$key]['gold_name'];
        }
    }
    return $select_caizhi;
}

//获取指圈
function  getZhiQuan($data){
    //$zhiquan_arr = array ("6-8"=>1,"9-10"=>1,"11-13"=>1,"14-15"=>1,"16-26"=>1);
    $select_arr = array();
    foreach ($data as $val){
        if($val <6 || $val > 26){
            continue;
        }
        if($val>=6 && $val <9){
            $select_arr["6-8"] = "6-8";
        }
        if($val>=9 && $val <11){
            $select_arr["9-10"] = "9-10";
        }
        if($val>=11 && $val <14){
            $select_arr["11-13"] = "11-13";
        }
        if($val>=14 && $val <16){
            $select_arr["14-15"] = "14-15";
        }
        if($val>=16 && $val <=26){
            $select_arr["16-26"] = "16-26";
        }
    }
    
    return $select_arr;
}

//切分手寸
function getAllFinger($min,$max){
	if($min == $max){
		return array($min);		
	}

	$data_arr = array();
	
	for($i=$min;$i<=$max;$i++){
		$data_arr[] = $i;
	}
	return $data_arr;
}