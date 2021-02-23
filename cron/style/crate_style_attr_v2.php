<?php
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
define('ROOT_PATH',str_replace('crate_style_info.php', '', str_replace('\\', '/', __FILE__)));
include('Pinyin.class.php');
//require_once(ROOT_PATH . 'config/shell_config.php');
$pinyin_obj = new Pinyin();

//v3款的对的原来的字段
 $attribute_arr  = array(
	"戒指"=>array(
		"镶口"=>'xiangkou',
		"材质"=>'style_caizhi',//v2 v3
		"指圈"=>'zhiquan',
		"是否刻字"=>'is_kezi',
		"是否围钻"=>'is_weizuan',
		"爪形态"=>'zhua_xingtai',//?? v3中没有  v2 有 
		"镶嵌方式"=>'style_xiangqian',
		"爪头数量"=>'zhua_num',
		"是否直爪"=>'is_zhizhua',
		"爪钉形状"=>'zhua_xingzhuang', 
		"爪带钻"=>'zhua_daizuan',
		"臂形态"=>'bi_xingtai',
		"戒臂带钻"=>'bi_daizuan',
		"戒臂表面工艺处理"=>'jiebi_gongyi',
		"是否有副石"=>'is_fushi',
		//"是否支持改圈"=>'is_gaiquan',//?? v3中没有  v2：为0是1 否
		"是否支持改圈"=>'style_gaiquan',
		"18K可做颜色"=>'kezuo_yanse',
		"证书"=>'zhengshu'
	)
 );

 //v2
 array(
 'zhushishu'=>'主石数',
 'zhushi_xingzhuang'=>'主石形状',

 )，
 
  //'1=>''文本框'',2=>''单选'',3=>''多选'',4=>''下拉列表''',
 $attr_show_type_arr  = array(
		"镶口"=>'3',
		"材质"=>'3',
		"指圈"=>'3',
		"是否刻字"=>'4',
		"是否支持刻字"=>'4',
		"是否围钻"=>'4',
		"爪形态"=>'4',
		"镶嵌方式"=>'4',
		"爪头数量"=>'4',
		"是否直爪"=>'4',
		"爪钉形状"=>'4',
		"爪带钻"=>'4',
		"臂形态"=>'4',
		"戒臂表面工艺处理"=>'3',
		"是否有副石"=>'4',
		//"是否支持改圈"=>'4',
		"最大改圈范围"=>'4',
		"18K可做颜色"=>'3',
		"证书"=>'3'
 );



$xiang_kou_arr = array ("0.10"=>1,"0.15"=>1,"0.20"=>1,"0.25"=>1,"0.30"=>1,"0.40"=>1,"0.50"=>1,"0.60"=>1,"0.70"=>1,"0.80"=>1,"0.90"=>1,"1.00"=>1,"1.10"=>1,"1.20"=>1,"1.30"=>1,"1.40"=>1,"1.50"=>1,"2.00"=>1);
$zhiquan_arr = array ("6,7,8"=>1,"9,10"=>1,"11,12,13"=>1,"14,15"=>1,"16,17,18,19,20,21,22,23,24,25,26"=>1);
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
$kezuo_yanse_arr = array("白色","黄色","红色","分色");
$zhengshu_arr = array("AGS"=>1,"EGL"=>2,"GAC"=>3,"GemEx"=>4,"GIA"=>5,"GIC"=>6,"HRD"=>7,"IGI"=>8,"NGGC"=>9,"NGSTC"=>10,"其它"=>11);
$zhua_xingtai_arr = array("无"=>'',"直"=>1,"扭"=>2,"花型"=>3,"雪花"=>4);

 //旧的产品线
$_style_pro_line = array(
	"0" => array("item_name" => "其他饰品"),//其他(原名)
    "1" => array("item_name" => "黄金等投资产品"),
    "2" => array("item_name" => "素金饰品"),
    "3" => array("item_name" => "黄金饰品及工艺品"),//黄金饰品(原名)
    "4" => array("item_name" => "钻石饰品"),//结婚钻石饰品
  //  "5" => array("item_name" => "钻石饰品"),
    "6" => array("item_name" => "珍珠饰品"),
    "7" => array("item_name" => "彩宝饰品"),//彩宝及翡翠饰品(原名)
    "8" => array("item_name" => "成品钻"),
    "9" => array("item_name" => "翡翠饰品"),
    "10" => array("item_name" => "配件及特殊包装"),
    "11" => array("item_name" => "非珠宝"),
);

//??????????????旧的产品线在新项目中产品的id 的key的对应
$new_pro_line = array(
	"5" => array("item_name" => "其他饰品"),//其他(原名)
    "14" => array("item_name" => "黄金等投资产品"),
    "4" => array("item_name" => "素金饰品"),
    "13" => array("item_name" => "黄金饰品及工艺品"),//黄金饰品(原名)
    "6" => array("item_name" => "钻石饰品"),//结婚钻石饰品
  //  "5" => array("item_name" => "钻石饰品"),
    "15" => array("item_name" => "珍珠饰品"),
    "7" => array("item_name" => "彩宝饰品"),//彩宝及翡翠饰品(原名)
    "6" => array("item_name" => "成品钻"),
    "16" => array("item_name" => "翡翠饰品"),
    "10" => array("item_name" => "配件及特殊包装"),
    "12" => array("item_name" => "非珠宝"),
);

//老款式库
$con = mysqli_connect('192.168.70.251','yangfuyou','yangfuyou1q2w3e');
//$con = mysqli_connect('localhost','root','');
if(!$con){
	die("Can not connect con");
}

//取出所有v3款式的属性：及数据库字段（属性） ;值为属性对应的值
$old_table = 'style_style';
mysqli_select_db($con,'kela_style');
$sql = "SELECT * FROM ".$old_table."  WHERE `is_new` =2 and `is_confirm`!=4";
$res = mysqli_query($con, $sql);
$old_style_data = array();
while ($row = mysqli_fetch_array($res)){
    $old_style_data[]= $row;
}


//var_dump($old_attr_value_arr);
mysqli_close($con);
//??????? 款式v3的所有属性中是没有镶口和指圈的需要，单取，材质，颜色需要考虑
//新项目
$con = mysqli_connect('192.168.70.251','yangfuyou','yangfuyou1q2w3e');
//$con = mysqli_connect('localhost','root','');
if(!$con){
	die("Can not connect con");
}
mysqli_query($con,'SET NAMES UTF8');

$table_arr = 'app_attribute';
$table_arr_value = 'app_attribute_value';
$rel_cat_attr_table = 'rel_cat_attribute';
$rel_style_attr_table = 'rel_style_attribute';
mysqli_select_db($con,'front');

//取出所有的属性来找到对应出的属性id
$sql = "SELECT * FROM ".$table_arr;
$res = mysqli_query($con, $sql);
$new_attr_data = array();
$new_attr_id_data = array();
while ($row = mysqli_fetch_array($res)){
    $new_attr_data[$row['attribute_name']]= $row['attribute_id'];
    $new_attr_id_data[$row['attribute_id']]= $row['attribute_name'];
} 

//获取所有属性值
$sql = "select * from ".$table_arr_value;
$res = mysqli_query($con, $sql);
$new_attr_value_data = array();
while ($row = mysqli_fetch_array($res)){
    $attr_id = $row['attribute_id'];
    $value_id = $row['att_value_id'];
    $value_name = $row['att_value_name'];
    $new_attr_value_data[$attr_id][$value_name]= $value_id;
} 

//遍历戒指的所有属性
$old_attr_value_arr = array();
foreach ($attribute_arr['戒指'] as $key=>$val){
    $attr_name = $key;
    $attr_value_arr = $val."_arr";
	$attr_id = $new_attr_data[$attr_name];
    //属性对应多种属性值
    foreach ($$attr_value_arr as $v_key=>$v_val){
		$attr_value_name = $v_key;
		$attr_value_zhi = $v_val;

        $old_attr_value_arr[$attr_id][$attr_value_zhi] = $attr_value_name; 
    }
}

//旧款式库：的属性
$old_attribute_arr = array();
foreach ($attribute_arr['戒指'] as $key=>$val){
	$old_attribute_arr[$val] = $key;
}

//先清款式对应的属性
$t_sql = "TRUNCATE TABLE $rel_style_attr_table ";
$t_res = mysqli_query($con, $t_sql);
if($t_res){
    echo $rel_style_attr_table."数据已经清空";
}

//钻石：产品线id；戒指;分类id
$cat_type_id = 2;
$product_type_id = 6;
$date_time = date("Y-m-d H:i:s");
$field =" `cat_type_id`, `product_type_id`, `style_sn`, `attribute_id`, `attribute_value`, `show_type`, `create_time`, `create_user`, `info`, `style_id`";
//单选：下拉列表的
foreach($old_style_data as $s_val){
    $style_id = $s_val['style_id'];
    $style_sn = $s_val['style_sn'];
    foreach ($old_attribute_arr as $a_key=>$a_val){
        $attr_name = $a_val;
        $value_zhi = $s_val[$a_key];
        $new_attr_value_id = '';
        if($value_zhi !=''){
            $attr_id = $new_attr_data[$attr_name];
            $attr_value_name = $old_attr_value_arr[$attr_id][$value_zhi];
            $new_attr_value_id = $new_attr_value_data[$attr_id][$attr_value_name];
        }
        $show_type = $attr_show_type_arr[$attr_name];
        if($show_type == 4){//下拉列表
            $sql= "INSERT INTO `".$rel_style_attr_table."` (".$field.")  VALUES ( '".$cat_type_id."', '".$product_type_id."','".$style_sn."','".$attr_id."','".$new_attr_value_id."' ,$show_type,  '".$date_time."', 'addmin', '','')" ;
            echo $sql."<br>";
           mysqli_query($con,$sql);
        }
    }
}

//多选：复选框
foreach($old_style_data as $s_val){
    $style_id = $s_val['style_id'];
    $style_sn = $s_val['style_sn'];
    foreach ($old_attribute_arr as $a_key=>$a_val){
        $attr_name = $a_val;
        $value_zhi = $a_val[$a_key];
        $new_attr_value_id = '';
        $show_type = $attr_show_type_arr[$attr_name];
        if($show_type == 3){//下拉列表
            if($value_zhi !=''){
                $tmp_value_id_arr = explode(',', $value_zhi);
                foreach ($tmp_value_id_arr as $t_val){
                    if($t_val){
                        $tmp_zhi = $t_val;
                        $attr_id = $new_attr_data[$attr_name];
                        $attr_value_name = $old_attr_value_arr[$attr_id][$tmp_zhi];
                        $new_attr_value_id .= $new_attr_value_data[$attr_id][$attr_value_name].',';
                    }
                }
            }
            echo "<br>---<br>";
            $sql= "INSERT INTO `".$rel_style_attr_table."` (".$field.")  VALUES ( '".$cat_type_id."', '".$product_type_id."','".$style_sn."','".$attr_id."','".$new_attr_value_id."' ,$show_type,  '".$date_time."', 'addmin', '','')" ;
            echo $sql."<br>";
          //  mysqli_query($con,$sql);
        }
    }
}

echo "<br>表已经生成ok";
die;
