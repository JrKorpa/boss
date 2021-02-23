<?php
error_reporting(E_ALL ^ E_DEPRECATED);
$dbnme1='`ecs_diamond`'; // 裸钻
$dbnme2='`diamond_info`';
$conn= mysqli_connect('192.168.70.251','yangfuyou','yangfuyou1q2w3e') or die("数据库连接失败！") ; 
mysqli_query($conn,"set names 'utf8'");
//mysqli_select_db($conn,'kelashop'); 
mysqli_select_db($conn,'diamond'); 
 
$sql ="select * from ".$dbnme1." limit 2"; //SQL语句
$result = mysqli_query($conn,$sql); //查询
var_dump($result);
echo '----';
$foodsPic=array();
while($row=mysqli_fetch_assoc($result)){
    foreach($row as $k=>$v){
        if(is_int($v)){
            continue;
        }
       $row[$k]=iconv("utf-8","gbk",$v);
    }
    $foodsPic[]=$row;
}

$conn = mysqli_connect('192.168.70.251','yangfuyou','yangfuyou1q2w3e') or die("数据库连接失败！") ; 
mysqli_query($conn,"set names 'utf8'");
mysqli_select_db($conn,'front'); 

foreach($foodsPic as $k=>$v){
    foreach($v as $j=>$l){
        if(is_int($l)){
            continue;
        }
       $v[$j]=iconv("gbk","utf-8",$l);
    }
    $s='';
    $q='';
    if($v['goods_sn']){
        $s.='`goods_sn`,';
        $q.="'".$v['goods_sn']."',";
    }
    if($v['goods_name']){
        $s.='`goods_name`,';
        $q.="'".$v['goods_name']."',";        
    }
    if($v['goods_number']){
        $s.='`goods_number`,';
        $q.="'".$v['goods_number']."',";        
    }
    if($v['goods_type']){
        $s.='`good_type`,';
        $q.="'".$v['goods_type']."',";        
    }
    if($v['market_price']){
        $s.='`market_price`,';
        $q.="'".$v['market_price']."',";        
    }
    if($v['chengben']){
        $s.='`chengben_jia`,';
        $q.="'".$v['chengben']."',";        
    }
    if($v['carat']){
        $s.='`carat`,';
        $q.="'".$v['carat']."',";        
    }
    if($v['clarity']){
        $s.='`clarity`,';
        $q.="'".$v['clarity']."',";        
    }
    if($v['cut']){
        $s.='`cut`,';
        $q.="'".$v['cut']."',";        
    }
    if($v['color']){
        $s.='`color`,';
        $q.="'".$v['color']."',";        
    }
    if($v['cat_id']){
        $s.='`shape`,';
        $q.="'".$v['cat_id']."',";        
    }
    if($v['depth']){
        $s.='`depth_lv`,';
        $q.="'".$v['depth']."',";        
    }
    if($v['table']){
        $s.='`table_lv`,';
        $q.="'".$v['table']."',";        
    }
    if($v['symmetry']){
        $s.='`symmetry`,';
        $q.="'".$v['symmetry']."',";        
    }
    if($v['polish']){
        $s.='`polish`,';
        $q.="'".$v['polish']."',";        
    }
    if($v['fluorescence']){
        $s.='`fluorescence`,';
        $q.="'".$v['fluorescence']."',";        
    }
    if($v['cert']){
        $s.='`cert`,';
        $q.="'".$v['cert']."',";        
    }
    if($v['cert_id']){
        $s.='`cert_id`,';
        $q.="'".$v['cert_id']."',";        
    }
    if($v['gemx_zhengshu']){
        $s.='`gemx_zhengshu`,';
        $q.="'".$v['gemx_zhengshu']."',";        
    }
    if($v['add_time']){
        $s.='`add_time`,';
        $q.="'".$v['add_time']."',";        
    }
    if($v['is_active']){
        $s.='`is_active`,';
        $q.="'".$v['is_active']."',";        
    }
    $sql="INSERT INTO $dbnme2(".rtrim($s, ',').") VALUES (".rtrim($q, ',').")";
	echo $sql;
    $query=mysqli_query($conn,$sql);
    if($query){
        echo "插入成功<br>";
    }else{
        echo "插入失败<br>";
    }
}
?>