<?php
//v3款式信息
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");

header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=钻石饰品.xls");

$conOld = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
//$conNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');


$cat_type_arr=array(1=>"戒指",2=>"吊坠",3=>"项链",4=>"耳钉",5=>"耳环",6=>"耳坠",7=>"手镯",8=>"手链",9=>"脚链",13=>"其他");//新项目中款式

//获取原来款式基本信息
$old_table = 'style_style';
$old_xiangkou = 'style_xiangkou';


$table_title = "款号\t款式名称\t镶口\t指圈\t副石1重\t副石1数量\t副石2重\t副石2数量\t副石3重\t副石3数量\t18K金重\t18K上公差\t18K下公差\t PT950金重\tPT950上公差\tPT950下公差\n";
//取钻石饰品数据
//$sql = "SELECT a.style_sn as style_sn_1,a.style_name,b.* FROM kela_style." . $old_table . " as a left join kela_style.".$old_xiangkou." as b on a.style_sn = b.style_sn where pro_line = 4 and `zuofei_type` =1 ";
$sql = "SELECT a.style_sn as style_sn_1,a.style_name,b.* FROM kela_style." . $old_table . " as a , kela_style.".$old_xiangkou." as b where a.style_sn = b.style_sn and pro_line = 4 and `zuofei_type` =1 ";
//echo $sql; exit;
//echo "<br>";
echo iconv('UTF-8','GBK',$table_title);

$res = mysqli_query($conOld, $sql);
$old_data = array();
while ($val = mysqli_fetch_array($res)){
/*$add_time = $val['create_time'];
$update_time = $val['last_update'];
$cat_name = $cat_type_arr[$val['style_cat']];
$changxiao ="";
$xilie = "";*/
$is_new = $val['is_new'];
$style_name = $val['style_name'];
$style_sn = $val['style_sn_1'];
$xiangkou = $val['stone'];
$finger = $val['finger'];
$fs1 = $val['main_stone_weight'];
$fsnum1 = $val['main_stone_num'];
$fs2 = $val['sec_stone_weight'];
$fsnum2 = $val['sec_stone_num'];
$fs3 = $val['sec_stone_weight_other'];
$fsnum3 = $val['sec_stone_num_other'];
$g18 = $val['g18_weight'];
$g18_up = $val['g18_weight_more'];
$g18_down = $val['g18_weight_more2'];
$pt = $val['gpt_weight'];
$pt_up = $val['gpt_weight_more'];
$pt_down = $val['gpt_weight_more2'];


echo iconv('UTF-8','GBK',$style_sn)."\t".iconv('UTF-8','GBK',$style_name)."\t".$xiangkou."\t"."'".$finger."\t".$fs1."\t".$fsnum1."\t".$fs2."\t".$fsnum2."\t".$fs3."\t".$fsnum3."\t".$g18."\t".$g18_up."\t".$g18_down."\t".$pt."\t".$pt_up."\t".$pt_down."\n";

}

