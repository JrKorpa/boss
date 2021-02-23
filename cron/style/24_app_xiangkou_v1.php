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

$field = " `style_id`, `style_sn`, `stone`, `finger`, `main_stone_weight`, `main_stone_num`, `sec_stone_weight`, `sec_stone_num`, `sec_stone_weight_other`, `sec_stone_num_other`, `g18_weight`, `g18_weight_more`, `g18_weight_more2`, `gpt_weight`, `gpt_weight_more`, `gpt_weight_more2`, `sec_stone_price_other`";

//旧的数据,戒指类的
$sql = "SELECT count(*) FROM " . $old_table . "  WHERE `is_new`=0  and `style_cat`=1 ".$where;
$t = mysqli_query($conOld,$sql);
$cnt = mysqli_fetch_row($t);
$date_time = date("Y-m-d H:i:s");
$len = $cnt[0];

$num = 1000;

$forsize = ceil($len / $num);

$unsetArr = array('style_cat','gongfei','baomiangongyi_gongfei','fushixiangshifei');
//var_dump($forsize);exit;
for($ii = 1; $ii <= $forsize; $ii ++){
    echo $ii."----------------------------------------------------------"."\n";
    $offset = ($ii - 1) * $num;
    $sql = "SELECT * FROM " . $old_table . " WHERE `is_new`=0   and `style_cat`=1 ".$where." limit $offset,$num";
    $res = mysqli_query($conOld,$sql);
    $old_style_data = array();
	$val_sql=array();    
    while ( $row = mysqli_fetch_assoc($res) ){
        $style_id = $row['style_id'];
        $style_sn = $row['style_sn'];
        
        $g18_weight = 0;
        $gpt_weight = 0;
        
        //指圈
        $style_cat_info = unserialize($row["style_cat_attr"]);
        $select_finger = array();
        if(!empty($style_cat_info)){
            if(array_key_exists(1, $style_cat_info)){
                $min_finger = $style_cat_info[1]['min'];
                $max_finger = $style_cat_info[1]['max'];
                $all_finger = getAllFinger($min_finger, $max_finger);
                $new_value_id = "";
                $zq_attr_id = 5;
                if($all_finger){
                    $select_finger = getZhiQuan($all_finger);
                }
            }
        }
        //手寸为空，跳过
        if(empty($select_finger)){
             $info = $style_sn."\n";
              echo "no_finger\n";
             writefile('no_finger.txt',$info);
            continue;
        }
        
        //镶口和主石头数据
        $is_zhushi = $row['main_stone_cat'];
        $zhushi_attr = unserialize($row['main_stone_attr']);
        $select_stone = array();//镶口
        $main_stone_weight = 0;
        $main_stone_num = 0;
        
//        var_dump($is_zhushi,$zhushi_attr);
        if($is_zhushi == 1){
            $main_stone_weight = isset($zhushi_attr[1])?$zhushi_attr[1]:0 ;
            $main_stone_num = isset($zhushi_attr[2])?$zhushi_attr[2]:0 ;
            $stone_min = isset($zhushi_attr[3]['min'])?$zhushi_attr[3]['min']:0 ;
            $stone_max = isset($zhushi_attr[3]['max'])?$zhushi_attr[3]['max']:0 ;
           
            $select_stone = getStone($stone_min, $stone_max);
            // var_dump($stone_min,$stone_max,$select_stone);
            //镶口为空，跳过
            if(empty($select_stone)){
                 $info = $style_sn."\n";
                 echo "no_stone\n";
                writefile('no_stone.txt',$info);
                continue;
            }
        }
        
        //副石数据
        $is_fushi = $row['sec_stone_cat'];
        $fushi_attr = unserialize($row['sec_stone_attr']);
        $sec_stone_weight =0 ;
        $sec_stone_num = 0;
        if($is_fushi ==1){
            $sec_stone_weight =isset($fushi_attr[1])?$fushi_attr[1]:0 ;
            $sec_stone_num = isset($fushi_attr[2])?$fushi_attr[2]:0;
        }
        
         //材质信息
        $metail_info = unserialize($row["metal_info"]);
        if(!empty($metail_info)){
            $caizhi_18k = getCaiZhi( $metail_info,2);//18k
            $caizhi_pt = getCaiZhi( $metail_info,4);//pt
            if(empty($caizhi_18k) && empty($caizhi_pt)){
                $info = $style_sn."\n";
                echo "no_caizhi\n";
                writefile('no_caizhi.txt',$info);
                continue;
            }
            if(!empty($caizhi_18k)){
                $caizhi = 1;
                $g18_weight = isset($caizhi_18k['gold_weigth']) ? $caizhi_18k['gold_weigth']:0;
            }
            if(!empty($caizhi_pt)){
                $caizhi = 2;
                $gpt_weight = isset($caizhi_pt['gold_weigth']) ? $caizhi_pt['gold_weigth']:0;
            }
        }
        //var_dump($select_finger,$select_stone);
        //插入数据
        foreach ($select_finger as $f_val){
            foreach ($select_stone as $s_val){
                if(empty($main_stone_weight)){
                    $main_stone_weight = 0;
                }
                if(empty($main_stone_num)){
                    $main_stone_num = 0;
                }
                if(empty($sec_stone_weight)){
                    $sec_stone_weight = 0;
                }
                if(empty($sec_stone_num)){
                    $sec_stone_num = 0;
                }
                $sql = "INSERT INTO `".$new_table."` (" . $field . ")  VALUES (".$style_id.",'".$style_sn."','".$s_val."','".$f_val."','".$main_stone_weight."','".$main_stone_num."','".$sec_stone_weight."','".$sec_stone_num."','0','0','". $g18_weight."','0','0','".$gpt_weight."','0','0','0')" ; 
               // echo $sql;
                if(!mysqli_query($conNew,$sql)){
                    writefile('no_xk_sql',$sql."\n\n\n");
                   echo "\n\n\n".$sql."\n\n\n";
                }else{
                    echo "1\n\n\n";;
                }
            }
        }
		
    }
}
echo "\n\n\n\n完成ok！";
die();


function getCaiZhi( $data,$type){
    $_style_gold_type = array(
//        "1" => array("gold_name" => "9K", "price"=>"140", "gold_color"=>",1,2,3,", "loss"=>"0.15", "middle" => "10"),
//        "8" => array("gold_name" => "14K", "price"=>"217", "gold_color"=>",1,2,3,", "loss"=>"0.15", "middle" => "13"),
        "2" => array("gold_name" => "18K", "price"=>"280", "gold_color"=>",1,2,3,", "loss"=>"0.15", "middle" => "13"),
//        "3" => array("gold_name" => "PT900", "price"=>"460", "gold_color"=>",1,", "loss"=>"0.15", "middle" => "20"),
        "4" => array("gold_name" => "PT950", "price"=>"465", "gold_color"=>",1,", "loss"=>"0.15", "middle" => "20"),
//        "10" => array("gold_name" => "PT999", "price"=>"338.5", "gold_color"=>",1,", "loss"=>"0.15", "middle" => "20"),
//        "5" => array("gold_name" => "S925", "price"=>"", "gold_color"=>",1,"),
//        "6" => array("gold_name" => "S990", "price"=>"", "gold_color"=>",1,"),
//        "7" => array("gold_name" => "千足金", "price"=>"375", "gold_color"=>",2,"),//这个不属于素金
//        "9" => array("gold_name" => "千足银", "price"=>"", "gold_color"=>",1,")
    );
    
    if(isset($data[$type]['selected']) && $data[$type]['selected']==1){
        return TRUE;
    }else{
        return FALSE;
    }
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


function writefile($file,$info) {
    $fh = fopen($file, "a");
    echo fwrite($fh, $info);    // 输出：6
    fclose($fh);
}

function getStone($min,$max) {
    $stone_arr = array("0.10","0.15","0.20","0.25","0.30","0.40","0.50","0.60","0.70","0.80","0.90","1.00","1.10","1.20","1.30","1.40","1.50","2.00");
    $select_stone = array();
    if($min > $max){
        return $select_stone;
    }
    /*if($max <0.1 || $min >2){
        return $select_stone;
    }*/
    
	if($min<0.1 && $min>0){
		$min =0.1;
	}

	if($max<0.1 && $max>0){
		$max =0.1;
	}
    
    foreach ($stone_arr as $val){
        if($val>=$min && $val<=$max){
            $select_stone[]= $val;
        }
    }
    return $select_stone;
    
}


