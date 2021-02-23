<?php

header("Content-type:text/html;charset=utf8;");
set_time_limit(0);
error_reporting(0);
//error_reporting( E_ALL&~E_NOTICE );

function getnewimg(){
    $db1 = mysqli_connect('203.130.44.199', 'cuteman', 'QW@W#RSS33#E#', 'front');
    //$db1 = mysqli_connect('127.0.0.1', 'develop', '123456', 'front');
    $sql = "set names utf8;";
    $arr = mysqli_query($db1, $sql);
    $sql = "select style_id,style_sn from `front`.`base_style_info`;";
    $arr = mysqli_query($db1, $sql);
    $data= array();
    while($w=mysqli_fetch_assoc($arr)){
        $data[$w['style_id']] = $w['style_sn'];
    }
    $sql = "select style_id,image_place,thumb_img from `front`.`app_style_gallery` where `image_place` = 0";
    $arr = mysqli_query($db1, $sql);
    $fac_data= array();
    while($w=mysqli_fetch_assoc($arr)){
        $fac_data[$w['style_id']][] = $w['thumb_img'];
    }
    $ret = array('base'=>$data,'fac'=>$fac_data);
    return $ret;
}

function getoldimg(){
    $db1 = mysqli_connect('192.168.1.55', 'style_zyy', 'KELAzhangyuanyuan123', 'kela_style');
    //$db1 = mysqli_connect('127.0.0.1', 'root', '', 'kela_style');
    $sql = "set names utf8;";
    $arr = mysqli_query($db1, $sql);
    $sql = "select style_id,style_sn from `kela_style`.`style_style`;";
    $arr = mysqli_query($db1, $sql);
    $data= array();
    while($w=mysqli_fetch_assoc($arr)){
        $data[$w['style_id']] = $w['style_sn'];
    }
    $sql = "select style_id,image_place,thumb_img from `kela_style`.`style_gallery` where `image_place` = 0";
    $arr = mysqli_query($db1, $sql);
    $fac_data= array();
    while($w=mysqli_fetch_assoc($arr)){
        $fac_data[$w['style_id']][] = $w['thumb_img'];
    }
    $ret = array('base'=>$data,'fac'=>$fac_data);
    return $ret;
}

$newimgData = getnewimg();
$oldimgData = getoldimg();

$style_sn_arr = array_unique($oldimgData['base'] + $newimgData['base']);

//echo '<pre>';
//print_r($newimgData['fac']);
//print_r($oldimgData['fac']);
//
$new_style = array_keys($oldimgData['fac']);
$old_style = array_keys($newimgData['fac']);
$all_style = array_unique($old_style + $new_style);

foreach($all_style as $key => $style_id){

    if(array_key_exists($style_id,$oldimgData['fac'])){


        $all_style_list[$style_id]['old'] = array('style_id'=>$style_id,'imgempty'=>1,'img'=>$oldimgData['fac'][$style_id]);

    
        
    }else{
        $all_style_list[$style_id]['old'] = array('style_id'=>$style_id,'imgempty'=>0);
    }


    if(array_key_exists($style_id,$newimgData['fac'])){

        $all_style_list[$style_id]['new'] = array('style_id'=>$style_id,'imgempty'=>1,'img'=>$newimgData['fac'][$style_id]);
    }else{
        $all_style_list[$style_id]['new'] = array('style_id'=>$style_id,'imgempty'=>0);
    }
}


foreach($all_style_list as $key => $style_info){
    //echo '<pre>';
    //var_dump($style_info);die;
    //print_r($style_info);die;
    $all_style_list[$key] = getDiff($style_info);


}
function getDiff($style_info){
    if($style_info['old']['imgempty']==0 && $style_info['new']['imgempty']==0){
        $style_info['allemptyType'] = 'all_empty';
        return $style_info;
    }
    if($style_info['old']['imgempty']==0 || $style_info['new']['imgempty']==0){
        if($style_info['new']['imgempty'] == 1){
            $style_info['emptyType'] = 'new';
           
            return $style_info;
        }else{
            $style_info['emptyType'] = 'old';
            return $style_info;
        }
    }
    $old = $style_info['old']['img'];
    $new = $style_info['new']['img'];
    if($old == null){
        $old = array();
    }
    if($new == null){
        $new = array();
    }
//echo '<pre>';
//print_r($new);die;
//
    foreach ($new as $k => $v) {
        # code...
        $new[$k] = str_replace("http://style.kela.cn/","",$v);
    }

    foreach ($new as $key => $value) {
        # code...
        //var_dump(in_array($value, $old));die;
        if(!in_array($value,$old)){
            $style_info['new']['diff'] = 'no';
            $style_info['new']['img'][$key] = '<span style="color:red">'.$style_info['new']['img'][$key].'</span>';
        }else{
            $style_info['new']['diff'] = 'yes';
        }
    }

    foreach ($old as $key => $value) {
        # code...
        if(!in_array($value,$new)){
            $style_info['old']['diff'] = 'no'; 
            $style_info['old']['img'][$key] = '<span style="color:red">'.$style_info['old']['img'][$key].'</span>';
        }else{
            $style_info['old']['diff'] = 'yes';
        }
    }
//echo '<pre>';
//print_r($style_info);die;

    /*foreach($placeExits as $val){
        if(!array_key_exists($val,$old) && !array_key_exists($val,$new)){
            $style_info['diff'][$val] = 0;
        }elseif(array_key_exists($val,$old) && array_key_exists($val,$new)){
            $style_info['diff'][$val] = str_replace("http://style.kela.cn/","",$new[$val]) == $old[$val]?0:1;
        }else{
            $style_info['diff'][$val] = 1;
        }
    }
    $style_info['emptyType'] = array_sum($style_info['diff']) > 0 ?'diff': 'same';*/
    return $style_info;
}

echo "<table border=1>";
        echo "<tr>";
        echo "<td width='10%'>";
        echo "</td>";
        echo "<td width='45%'>";
        echo "</td>";
        echo "<td width='45%'>";
        echo "</td>";
        echo "</tr>";
foreach($all_style_list as $key => $style_info){
    if($style_info['old']['diff'] == 'yes' && $style_info['new']['diff'] == 'yes'){
        continue;
    }elseif($style_info['old']['diff'] == 'no' || $style_info['new']['diff'] == 'no'){
        echo "<tr>";
        echo "<td>";
        echo $style_sn_arr[$key];
        echo "</td>";
        echo "<td>";      
        echo "<table border=1>";
        foreach($style_info['old']['img'] as $val){
            echo "<tr>";
            echo "<td>";
            echo $val;
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</td>";
        echo "<td>";
        echo "<table border=1>";
        foreach($style_info['new']['img'] as $val){
            echo "<tr>";
            echo "<td>";
            echo $val;
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</td>";
        echo "<td>";
        echo "</td>";
        echo "</tr>";
    }
}
echo "</table>";