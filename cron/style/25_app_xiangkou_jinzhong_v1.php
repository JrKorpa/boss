<?php
// 老款的金重
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
//此文件是where条件
include 'style_where.php';
$conOld = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$conNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');

$old_table = "style_style";	
$new_table = "app_xiangkou";

//旧的数据,戒指类的
$sql = "SELECT count(*) FROM " . $old_table . "  WHERE `is_new`=0 and `style_cat`=1  ".$where;
$t = mysqli_query($conOld,$sql);
$cnt = mysqli_fetch_row($t);
$date_time = date("Y-m-d H:i:s");
$len = $cnt[0];
$num = 1000;
$forsize = ceil($len / $num);

//var_dump($forsize);exit;
for($ii = 1; $ii <= $forsize; $ii ++){
    echo $ii."----------------------------------------------------------"."\n";
    $offset = ($ii - 1) * $num;
    $sql = "SELECT * FROM " . $old_table . " WHERE `is_new`=0 and `style_cat`=1 and style_sn='KLRW002865' limit $offset,$num";
    $sql = "SELECT * FROM " . $old_table . " WHERE `is_new`=0 and `style_cat`=1 and style_sn='KLRW028033' limit $offset,$num";
    $sql = "SELECT * FROM " . $old_table . " WHERE `is_new`=0 and `style_cat`=1 and style_sn='KLRM027602' limit $offset,$num";
    $sql = "SELECT * FROM " . $old_table . " WHERE `is_new`=0 and `style_cat`=1  ".$where." limit $offset,$num";
    
    $res = mysqli_query($conOld,$sql);
    $old_style_data = array();
	$val_sql=array();    
    while ( $row = mysqli_fetch_assoc($res) ){
        $g18_weight = 0;
        $gpt_weight = 0;
        $style_sn = $row['style_sn'];
        
        //材质信息
        $metail_info = unserialize($row["metal_info"]);
        if(!empty($metail_info)){
            //18k 2
            if(isset($metail_info[2])) {
                if($metail_info[2]['selected'] == 1){
                    $g18_weight = $metail_info[2]['gold_weigth'];
                }
            }
            //pt 2
            if(isset($metail_info[4])) {
                if($metail_info[4]['selected'] == 1){
                    $gpt_weight = $metail_info[4]['gold_weigth'];
                }
            }
           
            if($g18_weight ==0 && $gpt_weight == 0){
                $info = $style_sn."\n";
                writefile('no_25_caizhi.txt',$info);
                continue;
            }
            
            $sql_5 = "update ".$new_table." set `g18_weight`= ".$g18_weight." ,`gpt_weight`=".$gpt_weight."  WHERE `style_sn`='".$style_sn."'";
            if(!mysqli_query($conNew,$sql_5)){
                echo "\n\n\n".$sql."\n\n\n";exit;
            }else {
                echo $ii."\n";
            }
        }else{
            if(empty($caizhi_18k) && empty($caizhi_pt)){
                $info = $style_sn."\n";
                writefile('no_25_caizhi.txt',$info);
                continue;
            }
        }
        
       
    }

}

echo "ok";
//写入错误信息
function writefile($file,$info) {
    $fh = fopen($file, "a");
    echo fwrite($fh, $info);    // 输出：6
    fclose($fh);
}


