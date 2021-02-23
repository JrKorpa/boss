<?php
error_reporting(E_ALL ^ E_DEPRECATED);

$dbnme1='`ecs_users`'; // 会员
$dbnme2='`base_member_info`';
$conn=mysql_connect('localhost','root','123456') or die("数据库连接失败！") ; 
mysql_query("set names 'utf8'");
mysql_select_db('kelaweb'); 
 
$sql ="select * from ".$dbnme1; //SQL语句
$result = mysql_query($sql,$conn); //查询
$foodsPic=array();
while($row=mysql_fetch_assoc($result)){
    foreach($row as $k=>$v){
        if(is_int($v)){
            continue;
        }
       $row[$k]=iconv("utf-8","gbk",$v);
    }
    $foodsPic[]=$row;
}

$conn=mysql_connect('192.168.1.63','develop','123456') or die("数据库连接失败！") ; 
mysql_query("set names 'utf8'");
mysql_select_db('front'); 

foreach($foodsPic as $k=>$v){
    foreach($v as $j=>$l){
        if(is_int($l)){
            continue;
        }
       $v[$j]=iconv("gbk","utf-8",$l);
    }
    $s='';
    $q='';
    if($v['user_name']){
        $s.='`member_name`,';
        $q.="'".$v['user_name']."',";
    }
    if($v['user_card']){
        $s.='`mem_card_sn`,';
        $q.="'".$v['user_card']."',";        
    }
    if($v['user_card']){
        $s.='`department_id`,';
        $q.="'".$v['user_card']."',";        
    }
    if($v['mobile']){
        $s.='`member_phone`,';
        $q.="'".$v['mobile']."',";        
    }
    if($v['qq']){
        $s.='`member_qq`,';
        $q.="'".$v['qq']."',";        
    }
    if($v['email']){
        $s.='`member_email`,';
        $q.="'".$v['email']."',";        
    }
    if($v['wangwang']){
        $s.='`member_aliww`,';
        $q.="'".$v['wangwang']."',";        
    }
    if($v['address']){
        $s.='`member_address`,';
        $q.="'".$v['address']."',";        
    }
    if($v['user_type']){
        $s.='`member_type`,';
        $q.="'".$v['user_type']."',";        
    }
    $sql="INSERT INTO $dbnme2(".rtrim($s, ',').") VALUES (".rtrim($q, ',').")";
    if(mysql_query($sql,$conn)){
        echo "成功<br>";
    }else{
        echo "失败<br>";
    }
}
?>