<?php
// v2,v3 款属性
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
//此文件是where条件
include 'style_where.php'; 

$conOld = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$conNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');

$old_style = 'style_style';	
$new_table = "rel_style_attribute";
$attr_table = 'app_attribute';
$attr_table_value = 'app_attribute_value';

// 老款式库
/*$t_sql = "TRUNCATE TABLE `front`.$new_table ";
$t_res = mysqli_query($conNew,$t_sql);
if($t_res){
    echo $old_style . "数据已经清空\n\n\n\n";
}*/

$product_type = 6;
$cat_type = 2;


/*主石形状、副石、副石形状
是否群镶、镶嵌方式、爪数量、是否直爪、爪钉形状、爪带钻、
、可否支持改圈、戒臂形态、戒臂带钻、戒臂表面工艺处理、是否支持刻字、证书
可做材质、镶口、颜色

//没有列的：颜色，是否有副石
注意：戒臂带钻 就是：否有副石
是否围钻 就是否群镶

v3没有数据
35 证书
29 是否有副石 ？？
17 	是否直爪
7 能否刻字
*/
$attribute_data  = array(
		//"镶口"=>'1',
		//"材质"=>'2',
		//"指圈"=>'3',
		"18K可做颜色"=>'kezuo_yanse',

		//"是否支持刻字"=>'could_world', v2
		"是否群镶"=>'is_weizuan',
		"证书"=>'zhengshu',
		"最大改圈范围"=>'style_gaiquan',
		"是否有副石"=>'is_fushi',
		//"爪形态"=>'zhua_xingtai',v3没有
		"表面工艺"=>'jiebi_gongyi',
		"戒臂带钻"=>'bi_daizuan',
		"臂形态"=>'bi_xingtai',
		"爪带钻"=>'zhua_daizuan',
		"爪钉形状"=>'zhua_xingzhuang',
		"镶嵌方式"=>'style_xiangqian',
		"爪头数量"=>'zhua_num',
		"是否直爪"=>'is_zhizhua',
		"能否刻字"=>'is_kezi',	
		//"是否支持改圈"=>'is_gaiquan',//v3 没有
 );

$xiangkou_arr = array ("0.10"=>1,"0.15"=>1,"0.20"=>1,"0.25"=>1,"0.30"=>1,"0.40"=>1,"0.50"=>1,"0.60"=>1,"0.70"=>1,"0.80"=>1,"0.90"=>1,"1.00"=>1,"1.10"=>1,"1.20"=>1,"1.30"=>1,"1.40"=>1,"1.50"=>1,"2.00"=>1);
$zhiquan_arr = array ("6-8"=>1,"9-10"=>1,"11-13"=>1,"14-15"=>1,"16-26"=>1);
$style_caizhi_arr = array("18K"=>1,"PT950"=>2);
$is_weizuan_arr = array("主钻"=>1,"群镶有主钻"=>2,"群镶无主钻"=>3,"其它"=>4);
$is_kezi_arr =array("可刻字"=>0,"不可刻字"=>1);
$style_xiangqian_arr = array("爪镶"=>1,"包镶"=>2,"夹镶"=>3,"槽镶"=>4,"钉镶"=>5,"轨道镶"=>6,"其它"=>7);
$zhua_num_arr = array("无"=>0,"一爪"=>1,"二爪"=>2,"三爪"=>3,"四爪"=>4,"五爪"=>5,"六爪"=>6,"七爪"=>7,"八爪"=>8,"九爪"=>9,"十爪"=>10,"十一爪"=>11,"十二爪"=>12,"其它"=>13);
$is_zhizhua_arr = array("是"=>1,"否"=>2);
$zhua_xingzhuang_arr = array("圆形"=>1,"心形"=>7,"V形"=>8,"水滴形"=>2,"三角形"=>5,"菱形"=>9,"方形"=>3,"梯形"=>4,"其它"=>10);
$zhua_daizuan_arr = array("有钻"=>1,"无钻"=>2);
$bi_xingtai_arr = array("直臂"=>1,"扭臂"=>2,"交臂"=>3,"高低臂"=>4,"其它"=>5);
$bi_daizuan_arr = array("带副石"=>1,"不带副石"=>0);
$zhu_shi_arr = array("无主石"=>13,"圆形"=>11,"祖母绿形"=>3,"椭圆形"=>6,"马眼形"=>14,"梨形"=>12,"心形"=>4,"垫形"=>1,"公主方形"=>2,"三角形"=>8,"水滴形"=>9,"长方形"=>10,"其它"=>5);
$fu_shi_arr = array("圆形"=>1,"祖母绿形"=>2,"椭圆形"=>3,"马眼形"=>4,"梨形"=>5,"心形"=>6,"垫形"=>7,"公主方形"=>8,"三角形"=>9,"水滴形"=>10,"长方形"=>11,"其它"=>12);
$jiebi_gongyi_arr = array("光面"=>1,"磨砂"=>2,"拉丝"=>3,"光面&磨砂"=>4,"光面&拉丝"=>5,"其它"=>6);
$is_fushi_arr = array("带副石"=>0,"无副石"=>1);
$style_gaiquan_arr = array("不可改圈"=>0,"可增减1个手寸"=>1,"可增减2个手寸"=>2,"可增1个手寸"=>3,"可增2个手寸"=>4);

$zhengshu_arr = array("AGS"=>1,"EGL"=>2,"GAC"=>3,"GemEx"=>4,"GIA"=>5,"GIC"=>6,"HRD"=>7,"IGI"=>8,"NGGC"=>9,"NGSTC"=>10,"其它"=>11);
$zhua_xingtai_arr = array("直"=>1,"扭"=>2,"花型"=>3,"雪花"=>4,"无"=>5);
$kezuo_yanse_arr = array("白色"=>1,"黄色"=>2,"玫瑰色"=>3,"分色"=>4);

//1、新数据库：查出上面这些属性的对应的id
$new_attr_data = array();
$show_type_arr = array();

foreach($attribute_data as $key=>$val){
	$sql = "SELECT * FROM `".$attr_table."` where attribute_name= '".$key."'";
	$res = mysqli_query($conNew, $sql);
	$row = mysqli_fetch_row($res);
	if(empty($row)){
		continue;
	}
	$new_attr_data[$row[1]] = $row[0];//[属性名]=>属性id
	$show_type_arr[$row[1]] = $row[3];//[属性名]=>展示方式
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
        $attr_value_name = $v_key;
        $attr_value_zhi = $v_val;
        $old_attr_value_arr[$attr_id][$attr_value_zhi] = $attr_value_name;//[属性id][属性值id]=>属性值
    }
}

//4、获取旧数据的各种属性值
$date_time = date("Y-m-d H:i:s");
$field = " `cat_type_id`, `product_type_id`, `style_sn`, `attribute_id`, `attribute_value`, `show_type`, `create_time`, `create_user`, `info`, `style_id` ";

$old_table = 'style_style';
$sql = "SELECT count(*) FROM kela_style." . $old_table . "  WHERE `is_new` =2 ".$where;
$t = mysqli_query($conOld,$sql);
$cnt = mysqli_fetch_row($t);


$len = $cnt[0];
$forsize = ceil($len / 1000);
for($ii = 1; $ii <= $forsize; $ii ++){
    echo $ii."----------------------------------------------------------"."\n";
    $offset = ($ii - 1) * 1000;
    $sql = "SELECT *  FROM " . $old_style . "   WHERE `is_new`=2 ".$where." limit $offset,1000";
    $res = mysqli_query($conOld,$sql);
    $old_style_data = array ();
    while ( $row = mysqli_fetch_array($res) ){
		$style_id = $row['style_id'];
		$style_sn = $row['style_sn'];
		
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
			//echo $attr_name."*".$show_type."\n";
			//var_dump( $old_value_id);

            if($show_type == 3){//多选
                $old_value_arr = explode(",", $old_value_id);
                $new_value_id = "";
                foreach ($old_value_arr as $tt){
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
				//echo mysqli_insert_id($conNew).'-'.$sql."\n";
			}
		}
    }
}
echo "\n\n\n\n完成ok！";
die();
