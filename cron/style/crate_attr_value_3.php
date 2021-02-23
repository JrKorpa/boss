<?php
/*
 * 创建属性值
 */
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
// define('ROOT_PATH',str_replace('crate_style_info.php', '', str_replace('\\', '/', __FILE__)));

//require_once(ROOT_PATH . 'config/shell_config.php');
$con = mysqli_connect('192.168.1.97','cuteman','QW@W#RSS33#E#');
// $con = mysqli_connect('localhost','root','');
if(!$con){
    die("Can not connect con");
}
mysqli_query($con,'SET NAMES UTF8');

include 'cf.php'; 

$xiangkou_arr = array ("0.10"=>1,"0.15"=>1,"0.20"=>1,"0.25"=>1,"0.30"=>1,"0.40"=>1,"0.50"=>1,"0.60"=>1,"0.70"=>1,"0.80"=>1,"0.90"=>1,"1.00"=>1,"1.10"=>1,"1.20"=>1,"1.30"=>1,"1.40"=>1,"1.50"=>1,"2.00"=>1);
$zhiquan_arr = array ("6-8"=>1,"9-10"=>1,"11-13"=>1,"14-15"=>1,"16-26"=>1);
$style_caizhi_arr = array("18K"=>1,"PT950"=>1);
$is_weizuan_arr = array("主钻"=>1,"群镶有主钻"=>2,"群镶无主钻"=>3,"其它"=>4);
$is_kezi_arr =array("是"=>1,"否"=>2);
$style_xiangqian_arr = array("爪镶"=>1,"包镶"=>2,"夹镶"=>3,"槽镶"=>4,"钉镶"=>5,"轨道镶"=>6,"其它"=>7);
$zhua_num_arr = array("无"=>0,"一爪"=>1,"二爪"=>2,"三爪"=>3,"四爪"=>4,"五爪"=>5,"六爪"=>6,"七爪"=>7,"八爪"=>8,"九爪"=>9,"十爪"=>10,"十一爪"=>11,"十二爪"=>12,"其它"=>13);
$is_zhizhua_arr = array("是"=>1,"否"=>2);
$zhua_xingzhuang_arr = array("圆形"=>1,"心形"=>7,"V形"=>8,"水滴形"=>2,"三角形"=>5,"菱形"=>9,"方形"=>3,"梯形"=>4,"其它"=>10);
$zhua_daizuan_arr = array("有钻"=>1,"无钻"=>2);
$bi_xingtai_arr = array("直臂"=>1,"扭臂"=>2,"交臂"=>3,"高低臂"=>4,"其它"=>5);
$bi_daizuan_arr = array("带副石"=>1,"不带副石"=>2);
$zhu_shi_arr = array("无主石"=>13,"圆形"=>11,"祖母绿形"=>3,"椭圆形"=>6,"马眼形"=>14,"梨形"=>12,"心形"=>4,"垫形"=>1,"公主方形"=>2,"三角形"=>8,"水滴形"=>9,"长方形"=>10,"其它"=>5);
$fu_shi_arr = array("圆形"=>1,"祖母绿形"=>2,"椭圆形"=>3,"马眼形"=>4,"梨形"=>5,"心形"=>6,"垫形"=>7,"公主方形"=>8,"三角形"=>9,"水滴形"=>10,"长方形"=>11,"其它"=>12);
$jiebi_gongyi_arr = array("光面"=>1,"磨砂"=>2,"拉丝"=>3,"光面&磨砂"=>4,"光面&拉丝"=>5,"其它"=>6);
$is_fushi_arr = array("带副石"=>1,"无副石"=>2);
$style_gaiquan_arr = array("不可改圈"=>0,"可增减1个手寸"=>1,"可增减2个手寸"=>2,"可增1个手寸"=>3,"可增2个手寸"=>4);

$zhengshu_arr = array("AGS"=>1,"EGL"=>2,"GAC"=>3,"GemEx"=>4,"GIA"=>5,"GIC"=>6,"HRD"=>7,"IGI"=>8,"NGGC"=>9,"NGSTC"=>10,"其它"=>11);
$zhua_xingtai_arr = array("直"=>1,"扭"=>2,"花型"=>3,"雪花"=>4,"无"=>5);
$kezuo_yanse_arr = array("白色"=>1,"黄色"=>1,"红色"=>1,"分色"=>1);

//款式基本信息
$attr_table = 'app_attribute';
$table = 'app_attribute_value';
mysqli_select_db($con,'front');

//先清款式对应的属性
$t_sql = "TRUNCATE TABLE $table ";
$t_res = mysqli_query($con, $t_sql);
if($t_res){
    echo $table."clear";
}

//获取所有的属性
$sql = "SELECT * FROM `".$attr_table."` ";
$res = mysqli_query($con, $sql);
$all_attr_arr = array();
while ($row = mysqli_fetch_array($res)){
    $all_attr_arr[$row['attribute_name']] = $row['attribute_id'];
}

$date_time = date("Y-m-d H:i:s");
$field = " `attribute_id`, `att_value_name`, `att_value_status`, `create_time`, `create_user`, `att_value_remark`";

foreach ($attribute_arr['戒指'] as $key=>$val){
    $attr_name = $key;
    $attr_value_arr = $val."_arr";
//     var_dump($attr_value_arr);exit;
	$attr_id = $all_attr_arr[$attr_name];
    foreach ($$attr_value_arr as $v_key=>$v_val){
		$attr_value = $v_key;
        $sql= "INSERT INTO `".$table."` (".$field.")  VALUES ( '".$attr_id."',  '".$attr_value."', 1, '".$date_time."', 'admin','' )" ;
        if(!mysqli_query($con,$sql)){
            echo $sql;exit;
        };
    }
}
echo "v2 v3 ok\n";

//v1老系统的属性
//'1=>''文本框'',2=>''单选'',3=>''多选'',4=>''下拉列表''',
$v1_show_type = array('text'=>1,'radio'=>2,'checkbox'=>3);
//老款是证书号上面已经有了，只有耳迫类型

// 耳迫
$_style_ear_force = array(
    "1" => array("item_name" => "压力耳迫"),
    "2" => array("item_name" => "枢纽耳迫"),
    "3" => array("item_name" => "塑制耳迫")
);


foreach($_style_ear_force as  $z_key=>$z_val){
		$attr_name = "耳迫";
		$attr_id = $all_attr_arr[$attr_name];
		$attr_value =$z_val['item_name'];
		
        $sql= "INSERT INTO `".$table."` (".$field.")  VALUES ( '".$attr_id."',  '".$attr_value."', 1, '".$date_time."', 'admin','' )" ;
        if(!mysqli_query($con,$sql)){
            echo $sql;exit;
        };
}
mysqli_close($con);

echo "v1 ok\n";