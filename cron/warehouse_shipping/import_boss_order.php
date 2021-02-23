<?php
/**
 * T100 指定表转移到BOSS 
 */
set_time_limit(0);
include("adodb/adodb.inc.php");         //包含adodb类库文件
include("adodb/adodb-pager.inc.php"); //包含adodb-pager类库文件

if(isset($_GET['do'])){
    $tableName1 = isset($_GET['table1'])?trim($_GET['table1']):'';
    $tableName2 = 't100_'.preg_replace("/^tt_|temp_/is","",$tableName1);
    if($tableName1==''){
        exit("未指定数据表");
    }
    if(!empty($_GET['down'])){
        //下载sql
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' .$tableName2.".sql");
    }else{
        //转移数据
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo "Begin Time:".date("Y-m-d H:i:s")."<br/>";   
    }
     
    $conn1 = ADONewConnection("oci8://gaopeng:y92vbWd609rnQ@192.168.1.191/TOPPRD?charset=UTF8");
    if(!$conn1){
        exit("conn1 连接失败！");
    }

    if(empty($_GET['down'])){
        $conn2 = NewADOConnection('mysqli'); //创建数据库连接对象
        $conn2->Connect('192.168.0.131', 'root', '123456','test' ); //连接数据库
        if(!$conn2){
            exit("conn2 连接失败！");
        }
        
        $count = $conn1->getOne("select count(*) from {$tableName1}");
        if($count===false){
            exit("数据表{$tableName1}不存在：".$conn1->ErrorMsg());
        }
        $count = $conn2->getOne("select count(*) from {$tableName2}");
        if($count===false){
            exit("数据表{$tableName2}不存在：".$conn1->ErrorMsg());
        }
        if($conn2->Execute("truncate table {$tableName2}")===false){
            exit("初始化标临时表{$tableName2}失败:".$conn2->ErrorMsg());
        }
    }else{
        echo "truncate table {$tableName2};\r\n";
    }    
    $page =1;
    $pageSize=100;
    $pageCount=!empty($_GET['page_count'])?$_GET['page_count']:1;
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
                if(preg_match('/_DESC$/is',$field) || $row[$field]==''){
                    continue;
                } 
                if(preg_match("/^\.\d+$/is",$row[$field])) {
                    $row[$field] = '0'.$row[$field];
                }              
                $field_sql .= $field.",";   
                $row[$field] = str_replace('\'','',$row[$field]);
                $val_sql .= "'".trim($row[$field])."',";
            }
            $field_sql = trim($field_sql,",");
            $val_sql = trim($val_sql,",");
            $insert_sql = "insert into {$tableName2}({$field_sql}) values({$val_sql})";
            if(!empty($_GET['down'])){
                echo $insert_sql.";\r\n";
            }else{
                if($conn2->Execute($insert_sql) === false) {
                    echo $insert_sql;
                    exit("{$num}:error insert data: ".$conn2->ErrorMsg()."<br/>");
                }else{
                    echo ".";
                }
            }
    
            $rs->MoveNext();
        }
        $page++;
        }
        if(empty($_GET['down'])){
            echo "<br/>inesert into {$tableName2} success！总数据：{$num}<br/>";
            echo "End Time:".date("Y-m-d H:i:s")."<br/>";
        }
}else{
    $table_list1 = array(
        'tt_base_order_info',
        'tt_app_order_account',
        'tt_app_order_details',
        'tt_app_order_address',
        'tt_app_order_invoice',
        'tt_app_order_pay_action',
        'tt_rel_out_order',
        'tt_warehouse_bill_s',
        'tt_warehouse_bill_goods_s',
    );

    ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>T100订单数据转移到BOSS</title>
<style> 
body{padding:0;margin:0; background-color:#66CCFF;}
table{width:100%; border-spacing:0; border:0;margin: 0px auto}
table td{ padding:5px 0px; font-weight:bold}
</style>
</head>
<body>
<div style="width:500px;margin: 0px auto">
<table  style="background-color:#66CCFF; height:100px;">
  <form action="" method="get" name="form_id" id="form_id" target="_blank">
  <tr>
  <td ><select name="down">
  <option value="">转移数据表</option>
  <option value="1">下载数据sql</option>
  </select></td>
  </tr><tr>
  <td><input name="do" value="1" type="hidden" />
  <select name="table1">
  <?php foreach ($table_list1 as $key=>$vo){?>
          <option value="<?php echo $vo?>"><?php echo $vo?></option>
  <?php }?>
   </select>
  </td>
  </tr>  
   <td></td></tr>
  <tr><td><input type="submit" id="btn_submit" value="DONE" ></td></tr>
  </form>
</table>

</div>
 
</body>
</html>
    <?php 
    
    
    
}


?>