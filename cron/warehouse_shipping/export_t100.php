<?php
/**
 * 万能导出脚本，仅支持导出指定表空间的数据任意表
 */
set_time_limit(0);
$_fileName = !empty($_GET['file_name'])?$_GET['file_name']:date("YmdHi");
$_tableName= !empty($_GET['table_name'])?strtoupper($_GET['table_name']):date("YmdHi");
$_user = !empty($_GET['user'])?strtoupper($_GET['user']):"GAOPENG";
if(preg_match("/dsdata|ds|sys/is",$_user)){
    $msg="不支持导出{$_user}表空间下的数据";
    error_msg($msg);
}
include("adodb/adodb.inc.php");         //包含adodb类库文件
include("adodb/adodb-pager.inc.php"); //包含adodb-pager类库文件
$dsn ="oci8://gaopeng:y92vbWd609rnQ@192.168.1.191/TOPPRD?charset=UTF8";
$conn = ADONewConnection($dsn);

$page =!empty($_GET['page'])?$_GET['page']:1;
$pageSize=100;
$pageCount=!empty($_GET['page_count'])?$_GET['page_count']:1;
$tableName = $_user.".".$_tableName;

$list_sql = "select * from {$tableName}"; //定义要执行导出的SQL语句
$pager = new ADODB_Pager($conn,$list_sql); //根据连接对象和SQL语句创建分页对象
if(!isset($_GET['down'])){
    $total_count = $conn->getOne("select count(*) from {$tableName}");
    if($total_count===false){
        $msg="Table {$tableName} not find!";
        error_msg($msg);
    }
    if($total_count>200000){
        //超过20万数据，建议用户分批下载
        $rs = $pager->db->PageExecute($list_sql,$pageSize,$page);
        $pageCount = $rs->LastPageNo();
        $ppsize = ceil($pageCount/2000);
        echo "总数据{$total_count}，数据过大建议分批下载，每批次默认20万：<hr/>";
        for($i=1;$i<=$ppsize;$i++){
            $page = ($i-1)*2000+1;
            $page_count = $i*2000;
            $fileName = $_fileName."(第{$i}批)";
            $href = "export_t100.php?page={$page}&page_count={$page_count}&table_name={$_tableName}&file_name={$fileName}&user={$_user}&down=1";
            echo "{$i}.<a href='{$href}'>{$fileName}.csv</a><br/>";
        }
        echo "<br/><hr/>";
        echo "不想分批,直接一次下载：<hr/>";
        echo "<a href='export_t100.php?table_name={$_tableName}&file_name={$_fileName}&user={$_user}&down=1'>{$_fileName}.csv</a><br/>";
        exit;
    }
    echo "总数据{$total_count},你可以直接下载：<hr/>";
    echo "<a href='export_t100.php?table_name={$_tableName}&file_name={$_fileName}&user={$_user}&down=1'>{$_fileName}.csv</a><br/>";
    exit;
}
if(!isset($_GET['debug'])){
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . $_fileName.".csv");
}
$i = 0;
while($page <= $pageCount){
    
    $rs = $pager->db->PageExecute($list_sql,$pageSize,$page);    
    if($i == 0){
        $i=1;
        $pageCount = $rs->LastPageNo();
        $output = '';
        $ncols = $rs->FieldCount();
        for ($i=0; $i < $ncols; $i++) {
            $field = $rs->FetchField($i);
            $output.= @iconv("UTF-8","GBK",$field->name).",";
        }
        $output=trim($output,',')."\r\n";
        echo $output;
    }
    while (!$rs->EOF) {
        $output = '';        
        for ($i=0; $i < $ncols; $i++) {            
            $output.= @iconv("UTF-8","GBK",str_filter($rs->fields[$i])).",";
        }
        $output=trim($output,',')."\r\n";
        echo $output;  
        $rs->MoveNext();
    }
    $page++;
}

function error_msg($msg){
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    echo $msg;
    exit;
}
function str_filter($str){
    $reg=array(",","\"","'","　","\t","\n","\r");
    $replace=array("，","“","’","","","","");
    $str = str_replace($reg,$replace, $str);
    return $str;
}
?>