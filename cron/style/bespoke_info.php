<?php
error_reporting(E_ALL ^ E_DEPRECATED);
$dbnme1='`ecs_bespoke`'; // 预约
$dbnme2='`app_bespoke_info`';
$conn=mysqli_connect('192.168.70.251','yangfuyou','yangfuyou1q2w3e') or die("数据库连接失败！") ; 
mysqli_query("set names 'utf8'");
mysqli_select_db('kelashop'); 
 
$sql ="select * from ".$dbnme1; //SQL语句
$result = mysqli_query($sql,$conn); //查询
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

$conn=mysqli_connect('192.168.70.251','yangfuyou','yangfuyou1q2w3e') or die("数据库连接失败！") ; 
mysqli_query("set names 'utf8'");
mysqli_select_db('front'); 

foreach($foodsPic as $k=>$v){
    foreach($v as $j=>$l){
        if(is_int($l)){
            continue;
        }
       $v[$j]=iconv("gbk","utf-8",$l);
    }
    $s='';
    $q='';
    if($v['department']){
        $s.='`department_id`,';
        $q.="'".$v['department']."',";
    }
    if($v['user_id']){
        $s.='`mem_id`,';
        $q.="'".$v['user_id']."',";        
    }
    if($v['from_ad']){
        $s.='`customer_source_id`,';
        $q.="'".$v['from_ad']."',";        
    }
    if($v['bespoke_man']){
        $s.='`customer`,';
        $q.="'".$v['bespoke_man']."',";        
    }
    if($v['mobile']){
        $s.='`customer_mobile`,';
        $q.="'".$v['mobile']."',";        
    }
    if($v['email']){
        $s.='`customer_email`,';
        $q.="'".$v['email']."',";        
    }
    if($v['add_time']){
        $s.='`create_time`,';
        $q.="'".$v['add_time']."',";        
    }
    if($v['shop_time']){
        $s.='`bespoke_inshop_time`,';
        $q.="'".$v['shop_time']."',";        
    }
    if($v['make_order']){
        $s.='`make_order`,';
        $q.="'".$v['make_order']."',";        
    }
    if($v['bespok_status']){
        $s.='`bespoke_status`,';
        $q.="'".$v['bespok_status']."',";        
    }
    if($v['bespok_remark']){
        $s.='`remark`,';
        $q.="'".$v['bespok_remark']."',";        
    }
    $sql="INSERT INTO $dbnme2(".rtrim($s, ',').") VALUES (".rtrim($q, ',').")";
    $query=mysqli_query($sql,$conn);
    if($query){
        echo "插入成功<br>";
    }else{
        echo "插入失败<br>";
    }
}
?>