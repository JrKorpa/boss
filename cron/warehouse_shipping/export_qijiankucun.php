<?php
set_time_limit(0);
$begin_date = !empty($_GET['begin_date'])?$_GET['begin_date']:date("Y-m-d");
$end_date = !empty($_GET['end_date'])?$_GET['end_date']:date("Y-m-d");
if(!isset($_GET['debug'])){
    $fileName ="export_qijiankucun{$begin_date}_{$end_date}.csv";
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . $fileName);
}
include("adodb/adodb.inc.php");         //包含adodb类库文件
include("adodb/adodb-pager.inc.php"); //包含adodb-pager类库文件
//$conn = NewADOConnection('oci8');
//$db_server ="TOPPRD";
//$db_user = "erpread";
//$db_pass = "xp5NKV6Tw";
//$db_user = "gaopeng";
//$db_pass = "y92vbWd609rnQ"; 
$dsn ="oci8://gaopeng:y92vbWd609rnQ@192.168.1.191/TOPPRD?charset=UTF8";
$conn = ADONewConnection($dsn);
//$conn->Connect($db_server,$db_user,$db_pass);
$table_name = "export_inaj".preg_replace("/-/is","",$begin_date)."_".preg_replace("/-/is","",$end_date);

$sql = "DROP TABLE {$table_name}";
$conn->Execute($sql);
$sql="CREATE TABLE {$table_name} AS SELECT inajent, inajsite, imaaua108 款式分类, inaj005, inaj008, inaj009, inaj010, inaj012, SUM (ssum) 期初, SUM (spi) 采购入库, SUM (swi) 委外入库, SUM (ssi) 销退, SUM (sti) 调拨入库, SUM (soi) 杂收入, SUM (soo) 杂发出, SUM (swo) 调拨出库, SUM (spo) 仓退, SUM (sao) 工单扣料, SUM (sso) 销售出货, SUM (ssum) + SUM (spi) + SUM (ssi) + SUM (sti) + SUM (swi) + SUM (soi) + SUM (soo) + SUM (swo) + SUM (spo) + SUM (sao) + SUM (sso) 期末, ( SUM (ssum) + SUM (spi) + SUM (ssi) + SUM (sti) + SUM (soi) + SUM (swi) + SUM (soo) + SUM (swo) + SUM (spo) + SUM (sao) + SUM (sso) ) * inaauc099 合计金额  FROM ( SELECT inajent, inajsite, inaj005, inaj008, inaj009, inaj010, inaj012, 0 ssum, SUM (pi) spi, SUM (si) ssi, SUM (wi) swi, SUM (ti) sti, SUM (oi) soi, SUM (oo) soo, SUM (wo) swo, SUM (po) spo, SUM (ao) sao, SUM (so) sso FROM ( SELECT inajent, inajsite, inaj005, inaj008, inaj009, inaj010, inaj012, CASE WHEN inaj004 = 1 THEN CASE WHEN inaj015 IN ( 'apmt570', 'cpmt570', 'cpmt571', 'cpmt573' ) THEN inaj011 * inaj004 ELSE 0 END ELSE 0 END pi, CASE WHEN inaj004 = 1 THEN CASE WHEN inaj015 IN ('cpmt572') THEN inaj011 * inaj004 ELSE 0 END ELSE 0 END wi, CASE WHEN inaj004 = 1 THEN CASE WHEN inaj015 IN ( 'cxmt600', 'cxmt601', 'cxmt803', 'cxmt806', 'axmt600' ) THEN inaj011 * inaj004 ELSE 0 END ELSE 0 END si, CASE WHEN inaj004 = 1 THEN CASE WHEN inaj015 IN ( 'cint330', 'cint331', 'cint333', 'cint334', 'cint335', 'cint336', 'cint337', 'cint390', 'cint391' ) THEN inaj011 * inaj004 ELSE 0 END ELSE 0 END ti, CASE WHEN inaj004 = 1 THEN CASE WHEN inaj015 IN ( 'cint301', 'cint302', 'cint303', 'cint304', 'cint306', 'cint307', 'cint308', 'cint309', 'cint311', 'cint312' ) THEN inaj011 * inaj004 ELSE 0 END ELSE 0 END oi,  CASE WHEN inaj004 =- 1 THEN CASE WHEN inaj015 IN ( 'cint301', 'cint302', 'cint303', 'cint304', 'cint306', 'cint307', 'cint308', 'cint309', 'cint311', 'cint312' ) THEN inaj011 * inaj004 ELSE 0 END ELSE 0 END oo,  CASE WHEN inaj004 =- 1 THEN CASE WHEN inaj015 IN ( 'cint330', 'cint331', 'cint333', 'cint334', 'cint335', 'cint336', 'cint337', 'cint390', 'cint391' ) THEN inaj011 * inaj004 ELSE 0 END ELSE 0 END wo,  CASE WHEN inaj004 =- 1 THEN CASE WHEN inaj015 IN ( 'apmt580', 'cpmt581', 'cpmt582', 'cpmt583' ) THEN inaj011 * inaj004 ELSE 0 END ELSE 0 END po,  CASE WHEN inaj004 =- 1 THEN CASE WHEN inaj015 IN ('csft314') THEN inaj011 * inaj004 ELSE 0 END ELSE 0 END ao,  CASE WHEN inaj004 =- 1 THEN CASE WHEN inaj015 IN ( 'axmt540', 'cxmt540', 'cxmt541', 'cxmt544', 'cxmt545', 'cxmt803', 'cxmt805' ) THEN inaj011 * inaj004 ELSE 0 END ELSE 0 END so,  inaj015,  inaj004 FROM dsdata.inaj_t WHERE inaj022 BETWEEN TO_DATE ( '{$begin_date}', 'yyyy-mm-dd' ) AND TO_DATE ('{$end_date}', 'yyyy-mm-dd') AND inajsite = '1000' ) GROUP BY inajent, inajsite, inaj005, inaj008, inaj009, inaj010, inaj012, 0 UNION ALL ( SELECT inajent, inajsite, inaj005, inaj008, inaj009, inaj010, inaj012, SUM (inaj011 * inaj004) ssum, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 FROM dsdata.inaj_t LEFT OUTER JOIN dsdata.inaauc_t ON inaauc001 = inaj010 WHERE inaj022 < TO_DATE ( '{$begin_date}', 'yyyy-mm-dd' ) AND inajsite = '1000' GROUP BY inajent, inajsite, inaj005, inaj008, inaj009, inaj010, inaj012 ) ) LEFT OUTER JOIN dsdata.inaauc_t ON inaj010 = inaauc001 LEFT OUTER JOIN dsdata.imaa_t ON imaa001 = inaj005 GROUP BY inajent, inajsite, inaj005, inaj008, inaj009, inaj010, inaj012, inaauc099, imaaua108 HAVING SUM (ssum) + SUM (spi) + SUM (ssi) + SUM (sti) + SUM (soi) + SUM (soo) + SUM (swo) + SUM (spo) + SUM (sao) + SUM (sso) + SUM (swi) <> 0 ORDER BY 期末 ASC";
#$sql = @iconv("UTF-8","GBK",$sql);
if ($conn->Execute($sql) === false) {
    print 'error create table: '.$conn->ErrorMsg().'<BR>';exit;
}
$sql = "select * from {$table_name}"; //定义要执行的SQL语句
$pager = new ADODB_Pager($conn, $sql); //根据连接对象和SQL语句创建分页对象
$page =1;
$pageSize=50;
$pageCount=1;

while($page <= $pageCount){
    
    $rs = $pager->db->PageExecute($sql,$pageSize,$page);    
    if($page == 1){
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
            $output.= @iconv("UTF-8","GBK",$rs->fields[$i]).",";
        }
        $output=trim($output,',')."\r\n";
        echo $output;  
        $rs->MoveNext();
    }
    $page++;
}
