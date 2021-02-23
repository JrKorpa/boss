<?php
/**
 * Oracle 小数据表快速转移
 */
set_time_limit(0);
include("adodb/adodb.inc.php");         //包含adodb类库文件
include("adodb/adodb-pager.inc.php"); //包含adodb-pager类库文件
$dsn_list=array(
    '1' =>array('type'=>'T100正式区','user'=>'GAOPENG','pwd'=>'y92vbWd609rnQ','server'=>'TOPPRD','ip'=>'192.168.1.191'),
    '2' =>array('type'=>'T100练习区','user'=>'DSDATA','pwd'=>'RD8h21trxw','server'=>'TOPTST','ip'=>'192.168.1.191'),
    '3' =>array('type'=>'EC正式区','user'=>'MAWENZHU','pwd'=>'qQ0knmtz2fa8MA','server'=>'B2B','ip'=>'192.168.1.191'), 
    '4' =>array('type'=>'EC正式区','user'=>'LIRUZONG','pwd'=>'jC9eWhWu9zFXMw','server'=>'B2B','ip'=>'192.168.1.191'),    
);

if(isset($_GET['do'])){
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    echo "Begin Time:".date("Y-m-d H:i:s")."<br/>";   
     
    if(!empty($_GET['dsn1']) && isset($dsn_list[$_GET['dsn1']])){
        $dsn_arr1 = $dsn_list[$_GET['dsn1']];
    }else{
        $dsn_arr1 = $dsn_list[1];
    }
    if(!empty($_GET['dsn2']) && isset($dsn_list[$_GET['dsn2']])){
        $dsn_arr2 = $dsn_list[$_GET['dsn2']];
    }else{
        $dsn_arr2 = $dsn_list[2];
    }
    
    $dsn1 = "oci8://{$dsn_arr1['user']}:{$dsn_arr1['pwd']}@{$dsn_arr1['ip']}/{$dsn_arr1['server']}?charset=UTF8";
    $dsn2 = "oci8://{$dsn_arr2['user']}:{$dsn_arr2['pwd']}@{$dsn_arr2['ip']}/{$dsn_arr2['server']}?charset=UTF8";
    
    $conn1 = ADONewConnection($dsn1);
    $conn2 = ADONewConnection($dsn2);
    if(!$conn1){
        exit("conn1 连接失败！");
    }
    if(!$conn2){
        exit("conn2 连接失败！");
    }
    $tableName1 = isset($_GET['table1'])?trim($_GET['table1']):'';
    $tableName2 = 'temp_'.preg_replace("/^tt_|temp_/is","",$tableName1);
    if($tableName1==''){
        exit("未指定数据表");
    }
    $tableName1 = strtoupper($tableName1);
    $tableName2 = strtoupper($tableName2);
    $user1 = strtoupper($dsn_arr1['user']);
    $user2 = strtoupper($dsn_arr2['user']);
    
    $count = $conn1->getOne("select count(*) from {$tableName1}");
    if($count===false){
        exit("数据表{$tableName1}不存在：".$conn1->ErrorMsg());        
    }
     
    $conn2->Execute("drop table {$tableName2}");
    $sql = "select dbms_metadata.get_ddl('TABLE','{$tableName1}') from dual";
    $sql = $conn1->getOne($sql);
    $sql = str_replace("\"{$user1}\".\"{$tableName1}\"", "\"{$user2}\".\"{$tableName2}\"", $sql);
    if($conn2->Execute($sql)===false){
        exit("创建目标临时表{$tableName2}失败:".$conn2->ErrorMsg());
    }    
    $page =1;
    $pageSize=100;
    $pageCount=1;
    $index = 0;//首行
    $num = 0;//控制总记录
    $fieldArr = array();
    $list_sql = "select * from {$tableName1}";
    $pager = new ADODB_Pager($conn1,$list_sql); //根据连接对象和SQL语句创建分页对象
    while($page <= $pageCount){
        $rs = $pager->db->PageExecute($list_sql,$pageSize,$page);
        if($index == 0){
            $index=1;
            $pageCount = $rs->LastPageNo();
            $ncols = $rs->FieldCount();
            for ($i=0; $i < $ncols; $i++) {
                $field = $rs->FetchField($i);
                $fieldArr[$i] = $field->name;
            }
        }
        while (!$rs->EOF) {
            $num ++;
            $val_sql = '';
            $field_sql='';
            $row = $rs->fields;
            for ($i=0; $i < $ncols; $i++) {
                $field = $fieldArr[$i];
                $field_sql .= $field.",";
                $val_sql .= "'".trim($row[$field])."',";
            }
            $field_sql = trim($field_sql,",");
            $val_sql = trim($val_sql,",");
            $insert_sql = "insert into {$tableName2}({$field_sql}) values({$val_sql})";
            if ($conn2->Execute($insert_sql) === false) {
                exit("{$num}:error insert data: ".$conn2->ErrorMsg()."<br/>");
            }else{
                echo ".";
            }
    
            $rs->MoveNext();
        }
        $page++;
        }
        echo "<br/>success！总数据：{$num}<br/>";
        echo "End Time:".date("Y-m-d H:i:s")."<br/>";
}else{
    ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Oracle 数据转移</title>
<style> 
body{padding:0;margin:0; background-color:#66CCFF;}
table{width:100%; border-spacing:0; border:0;margin: 0px auto}
table td{ padding:5px 0px; font-weight:bold}
</style>
</head>
<body>
<div style="width:900px;margin: 0px auto">
<table  style="background-color:#66CCFF; height:100px;">
  <form action="" method="get" name="form_id" id="form_id" target="_blank">
  <tr>
  <td >FROM</td>
  </tr><tr>
  <td><input name="do" value="1" type="hidden" />
  <select name="dsn1">
  <?php foreach ($dsn_list as $key=>$vo){?>
          <option value="<?php echo $key?>"><?php echo $vo['type']?>(<?php echo $vo['user']?>)</option>
  <?php }?>
   </select>
  </td>
  </tr><tr>
  <td><input name="table1" value="" /> <font color='red'>请填写要转移的数据表名称</font></td>
  </tr>
  <tr>
  <td>TO</td>
  </tr><tr>
  <td><select name="dsn2">
  <?php foreach ($dsn_list as $key=>$vo){?>
          <option value="<?php echo $key?>"><?php echo $vo['server']?>(<?php echo $vo['user']?>)</option>
  <?php }?>
   </select></td>
   </tr><tr>
   <td></td></tr>
  <tr><td><input type="submit" id="btn_submit" value="开始转移数据" ></td></tr>
  </form>
</table>

</div>
 
</body>
</html>
    <?php 
    
    
    
}


?>