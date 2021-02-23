<?php
/**
 * EC缺失品号导出
 */
set_time_limit(0);
//料号属性特殊转码
$caizhiArr=array("无"=>"0","9K"=>"9K","10K"=>"10K","14K"=>"14K","18K"=>"18K","24K"=>"24K","PT900"=>"PT900","PT950"=>"PT950","PT999"=>"PT999","足金"=>"G99","千足金"=>"G999","千足金银"=>"GS999","S925"=>"S925","S990"=>"S990","千足银"=>"S999","其它"=>"OT","其他"=>"OT");
$jinseArr=array("无"=>"0","白"=>"W","黄金"=>"Y","黄"=>"Y","玫瑰金"=>"R","分色"=>"C","玫瑰白"=>"RW","黄白"=>"YW","玫瑰黄"=>"RY","彩金"=>"H","按图做"=>"DIY","白金"=>"W");
$colorArr=array("D"=>"D","D-E"=>"D1","E"=>"E","E-F"=>"E1","F"=>"F","F-G"=>"F1","G"=>"G","G-H"=>"G1","H"=>"H","H+"=>"H1","H-I"=>"H2","I"=>"I","I-J"=>"I1","J"=>"J","J-K"=>"J1","K"=>"K","K-L"=>"K1","L"=>"L","M"=>"M","N"=>"N","白色"=>"WHITE","黄"=>"CY","蓝"=>"CB","粉"=>"CP","橙"=>"CO","绿"=>"CG","红"=>"CR","香槟"=>"CC","格雷恩"=>"CGL","紫"=>"CV","混色"=>"CM","蓝紫色"=>"CBP","黑"=>"CD","变色"=>"CCC","金色"=>"CGD","浅黄"=>"CY","咖"=>"NO","黑色"=>"CD","VVS2"=>"NO","VVS1"=>"NO","VS2"=>"NO","VS1"=>"NO","VLBY"=>"NO","SI2"=>"NO","SI1"=>"NO","KL"=>"K1","IJ"=>"I1","I - J"=>"I1","HI"=>"H2","FY"=>"F","FLY"=>"F","FLP"=>"F","FIY"=>"F","FIOY"=>"F","FIGY"=>"F","FG"=>"F","FDGYB"=>"F","F/J"=>"F");
$jingduArr=array("FL"=>"FL","IF"=>"IF","VVS2"=>"VVS2","VVS1"=>"VVS1","VVS"=>"VVS","VS2"=>"VS2","VS"=>"VS","VS1"=>"VS1","SI2"=>"SI2","SI1"=>"SI1","SI"=>"SI","I2"=>"I2","I1"=>"I1","I"=>"I","P1"=>"P1","P"=>"P","不分级"=>"NO","完美无瑕"=>"LC","P2"=>"P2","P3"=>"P3","FIY"=>"IF","FLY"=>"FL","FLPP"=>"FL","I - J"=>"I1","SI2-3"=>"SI2","SI1-2"=>"SI1","SI3-I1"=>"SI3","I-J"=>"I1","G"=>"VS","SI1/VS2"=>"SI1","D"=>"NO","E"=>"NO","F"=>"NO","H"=>"NO","J"=>"NO","K"=>"NO","L"=>"NO","LC"=>"LC");
$shapeArr=array('圆形'=>1,'心形'=>2,'水滴形'=>3,'梨形'=>4,'椭圆形'=>5,'垫'=>6,'公主方'=>7,'马眼'=>8,
    '橄榄形'=>9,'祖母绿形'=>10,'盾形'=>11,'菱形'=>12,'正方形'=>13,'长方形'=>14,'雷迪恩'=>15,'梯方形'=>16,
    '其它'=>17,'蛋形'=>18,'三角形'=>19,'阿斯切(Asscher)'=>20,'无'=>21,'枕形'=>22,'刻面形'=>23,'珠形'=>24,
    '弧面形'=>25,'任何形状'=>'NO','垫形'=>6,'祖母绿'=>10,'垫型'=>6,'坐垫形'=>6,'刻面型'=>23,'方形'=>14,'公主方形'=>7,
    '改心形'=>2,'方形祖母绿'=>10,'雕件'=>17,'异形'=>17,'水滴'=>3,'梯形'=>16);
$stoneCatArr=array('圆钻'=>1,'异形钻'=>2,'彩钻'=>3,'黑钻'=>4,'红宝石'=>5,'蓝宝石'=>6,
    '海蓝宝'=>7,'祖母绿'=>8,'水晶'=>9,'黄水晶'=>10,'紫龙晶'=>11,'玛瑙'=>12,'红玛瑙'=>13,'黑玛瑙'=>14,
    '玉髓'=>15,'碧玺'=>16,'和田玉'=>17,'翡翠'=>18,'青金石'=>19,'托帕石'=>20,'粉红宝'=>21,'葡萄石'=>22,
    '石榴石'=>23,'橄榄石'=>24,'虎睛石（木变石）'=>25,'拉长石（月光石）'=>26,'星光石'=>27,'黑曜石'=>28,
    '孔雀石'=>29,'绿松石'=>30,'红纹石'=>31,'东陵玉'=>32,'莹石'=>33,'捷克陨石'=>34,'欧泊'=>35,
    '舒俱来石'=>36,'锂辉石'=>37,'砗磲'=>38,'琥珀'=>39,'珊瑚'=>40,'芙蓉石'=>41,'坦桑石'=>42,'尖晶石'=>43,
    '珍珠'=>44,'海水香槟珠'=>45,'南洋白珠'=>46,'大溪地珍珠'=>47,'南洋金珠'=>48,'淡水白珠'=>49,'混搭珍珠'=>50,
    '贝壳'=>51,'其它'=>52,'无'=>53,'珍珠贝'=>54,'锆石'=>55,'鑽石'=>1,'钻石'=>1,'紫水晶'=>9,'紫晶'=>9,'珍珠贝'=>54,
    '珍珠'=>44,'玉髓'=>15,'玉'=>17,'拖帕石'=>20,'托帕石'=>20,'水晶'=>9,'石榴石'=>23,'青金石'=>19,'葡萄石'=>22,
    '柠檬晶'=>9,'南洋金珠'=>48,'南洋白珠'=>46,'玛瑙'=>12,'蓝玉髓'=>15,'蓝宝'=>6,'混搭珍珠'=>50,'黄水晶'=>10,
    '黄晶'=>10,'花奇楠木'=>52,'虎晴石'=>25,'虎睛石'=>25,'虎晶石'=>25,'红玛瑙'=>13,'红宝石'=>5,'红宝'=>5,
    '黑曜石'=>28,'黑檀木'=>52,'黑玛瑙'=>14,'和田玉'=>17,'海水珍珠'=>45,'海水香槟珠'=>45,'海蓝宝石'=>7,'海蓝宝'=>7,
    '锆石'=>55,'橄榄石'=>24,'芙蓉石'=>41,'粉晶'=>9,'翡翠'=>18,'发晶'=>9,'淡水珍珠'=>49,'淡水白珠'=>49,'大溪地珍珠'=>47,
    '大溪地黑珍珠'=>47,'砗磲'=>38,'砭石'=>52,'碧玺'=>16,'白水晶'=>9,'奥泊'=>35,'钻'=>1,'黄钻'=>3,'蓝色托帕石晶'=>20,
    '蓝晶'=>9,'透辉石'=>52,'猫眼石'=>25,'坦桑石'=>42,'白玛瑙'=>12,'黑皮绳'=>52,'蓝砂石'=>52,'彩兰宝'=>6,'金箔玫瑰'=>52,
    '彩色蓝宝'=>6,'月光石'=>26,'贝壳'=>51,'宝石'=>5,'黑钻'=>4,'彩贝'=>51,'红蓝宝石'=>6,'红蓝宝'=>6,'红蓝'=>6,'粉钻'=>3,
    '方钻'=>2,'EX'=>2);
$gongyiArr=array("光面"=>"1","拉砂"=>"2","喷砂"=>"3","钉砂"=>"4","光面&拉砂"=>"5","光面&磨砂"=>"6","特殊"=>"7","其它"=>"8","无"=>"9");
$yingguangArr = array("无"=>"N","弱"=>"F","中"=>"M","强"=>"S","其他"=>"OT","其它"=>"OT","很强"=>"V","非常强"=>"VS");
$paoguangArr = array("其他"=>"OT","其它"=>"OT");
$duichenArr = array("其他"=>"OT","其它"=>"OT");
$colorGradeArr = array("Faint(微)"=>"F","Very light(微浅)"=>"VL","Light(浅)"=>"L",
    "Fancy light(淡彩)"=>"FL", "Fancy（中彩）"=>"FC",
    "Fancy dark（暗彩）"=>"FD", "Fancy intense（浓彩）"=>"FI",
    "Fancy deep（深彩）"=>"FS", "Fancy vivid（艳彩）"=>"FV");//颜色分级
$certTypeArr = array("GIA"=>1,"AGS"=>2,"EGL"=>3,"GAC"=>4,"GemEx"=>5,"GIC"=>6,"HRD"=>7,"IGI"=>8,"NGGC"=>9,"NGSTC"=>10,"其它"=>11,"其他"=>11,"无"=>12,"HRD-D"=>13,"NGTC"=>14,"HRD-S"=>15,"DIA"=>16);


if(empty($_GET['begin_date'])){
    $begin_date = date("Y-m-d");
    $_GET['begin_date'] = date("Y-m-d");
}else{
    $begin_date = $_GET['begin_date'].' 00:00:00';
}
if(empty($_GET['end_date'])){
    $end_date = date("Y-m-d").' 23:59:59';
    $_GET['end_date'] = date("Y-m-d");
}else{
    $end_date = $_GET['end_date'].' 23:59:59';
}

include("adodb/adodb.inc.php");         //包含adodb类库文件
include("adodb/adodb-pager.inc.php"); //包含adodb-pager类库文件

$dsn ="oci8://mawenzhu:qQ0knmtz2fa8MA@192.168.1.191/B2B?charset=UTF8";
$ec_conn = ADONewConnection($dsn);

if(isset($_GET['toptst'])){
    $dsn ="oci8://dsdata:RD8h21trxw@192.168.1.191/TOPTST?charset=UTF8";
    $t100_conn = ADONewConnection($dsn);
}else{
    $dsn ="oci8://gaopeng:y92vbWd609rnQ@192.168.1.191/TOPPRD?charset=UTF8";
    $t100_conn = ADONewConnection($dsn);
}
$ec_pinhao_table = "tt_pinhao".date("Ymd");

$page =!empty($_GET['page'])?$_GET['page']:1;
$pageSize=100;
$pageCount=!empty($_GET['page_count'])?$_GET['page_count']:1;

$do_action = false;
if (php_sapi_name() === 'cli' && isset($argv[1])) {
    $do_action = $argv[1];
} else if (isset($_GET['do'])) {
    $do_action = $_GET['do'];
}


if($do_action == "download"){
    download_ec_pinhao();    
}else if($do_action == "import"){
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    echo "<br/>Start:EC查询品号记录：<hr/>";
    echo "Begin Time:".date("Y-m-d H:i:s")."<br/>";
    $total_count = create_ec_pinhao_table();
    echo "查询总记录:{$total_count}<br/>";
    echo "END Time:".date("Y-m-d H:i:s")."<br/>";
    //$t100_conn->StartTrans();
    import_inaauc_imaa();   
    import_imaa();
    //$t100_conn->CompleteTrans();
    ////$t100_conn->RollbackTrans();
    echo "<hr/>success!";
}else if($do_action == "import1"){
    //$t100_conn->StartTrans();
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    import_inaauc_imaa();
    //$t100_conn->CompleteTrans();
    echo "<hr/>success!";
}else if($do_action == "import2"){
    //导入正式表
    //$t100_conn->StartTrans();
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    import_imaa();
    //$t100_conn->CompleteTrans();
    echo "<hr/>success!";
}else{
    $total_count = create_ec_pinhao_table();
    init_form_html($total_count);
}
function create_ec_pinhao_table(){
    global $ec_conn,$begin_date,$end_date,$ec_pinhao_table;
    $sql = "DROP TABLE {$ec_pinhao_table}";
    $ec_conn->Execute($sql);
    $sql="create table {$ec_pinhao_table} as select
    tapl.LOT_NO as inaauc001,/*货号*/
    tapl.PROD_CODE as inaauc002,/*料号*/
    capl.style_No as inaauc004,/*款号*/
    tapl.wholesale_Price as inaauc106,/*批发价*/
    capl.GOLD_BASE_TYPE as inaauc030,/*金拖类型*/
    capl.SALES_PRICE as inaauc109,/*销售价*/
    capl.SURFACE_TECH  as inaauc037,/*表面工艺*/
    capl.C_MATERIAL as inaauc038,/*材质*/
    capl.MATERIAL_COL as inaauc039,/*材质颜色*/
    capl.MAIN_STONE_TYPE as inaauc045,/*主石类型*/
    capl.MAIN_STONE_NUM as inaauc057,/*主石粒数*/
    capl.MEASURE  as inaauc046,/*镶口*/
    capl.MAIN_STONE_COL as inaauc047,/*主石颜色*/
    capl.MAIN_STONE_PURE as inaauc048,/*主石净度*/
    capl.MAIN_STONE_CUT  as inaauc049,/*主石切工*/
    capl.MAIN_STONE_SHAPE  as inaauc050,/*主石形状*/
    capl.FL_LIGHT as inaauc051,/*荧光*/
    capl.POLISH as inaauc052,/*抛光*/
    capl.SYMMETRY as inaauc053,/*对称性*/
    capl.COLO_CLASS as inaauc054,/*颜色分级/饱和度*/
    capl.PEAR_SHAPE as inaauc055,/*珍珠形状*/
    capl.PEAR_COL as inaauc056,/*珍珠颜色*/
    capl.main_Stone_Weight as inaauc058,/*主石重*/
    capl.ring_No as inaauc060,/*指圈*/
    capl.chain_Length as inaauc063,/*链长*/
    capl.side1_Stone_Pure as inaauc066,/*副石1净度*/
    capl.side1_Stone_Type as inaauc067,/*副石1类型*/
    capl.side1_Stone_Num as inaauc068,/*副石1粒数*/
    capl.side1_Stone_shape as inaauc069,/*副石1形状*/
    capl.side1_Stone_Col as inaauc070,/*副石1颜色*/
    capl.side1_Stone_Weight as inaauc071,/*副石1重*/
    capl.side2_Stone_Type as inaauc072,/*副石2类型*/
    capl.side2_Stone_Num as inaauc073,/*副石2粒数*/
    capl.side2_Stone_Weight as inaauc074,/*副石2重*/
    capl.side3_Stone_Type as inaauc075,/*副石3类型*/
    capl.side3_Stone_Num as inaauc076,/*副石3粒数*/
    capl.side3_Stone_Weight as inaauc077,/*副石3重*/
    capl.self_pur as inaauc034, /*自采否*/
    capl.GIA_TYPE as inaauc043, /*证书类型*/
    cas.PROD_TYPE as inaauc139 /*商品分类*/
    from B2B.T_A_PR_LINE tapl inner join B2B.C_A_PR_LINE capl on tapl.DOC_NO = capl.DOC_NO and tapl.LINE_NO = capl.LINE_NO
    and tapl.BUY_COMP_CODE = capl.BUY_COMP_CODE and tapl.TenantGroup = capl.TenantGroup
    left JOIN B2B.T_A_PR tap on tap.DOC_TYPE_CODE = capl.DOC_TYPE_CODE and tap.DOC_NO = capl.DOC_NO and tap.BUY_COMP_CODE = capl.BUY_COMP_CODE
    and tap.TenantGroup = capl.TenantGroup
    left join B2B.C_A_STYLE cas on cas.TenantGroup = capl.TenantGroup
    and cas.STYLE_NO = capl.STYLE_NO
    left join B2B.t_a_org_ml om on  om.org_code=tap.buy_comp_code and om.TenantGroup=tap.TenantGroup
    left join  B2B.T_A_PROD_GROUP_ML pgm4  ON capl.PROD_CLASSIFICATION=pgm4.PROD_GROUP_CODE  and capl.PROD_CLASSIFICATION_GROUP_COMP=pgm4.COMP_CODE
    AND capl.PROD_CLASS_GROUP_TYPE_COMP =pgm4.group_type_comp_code and capl.TenantGroup=pgm4.TenantGroup and pgm4.prod_group_type_code='1005'
    left join B2B.t_a_inv_ml im on im.inv_code=capl.INV_IN_TRANS and im.comp_code=capl.buy_comp_code and  im.TenantGroup = capl.TenantGroup
    where 1=1 and tap.DOC_DATE<=to_date('{$end_date}','yyyy-mm-dd hh24:mi:ss') and tap.DOC_DATE>=to_date('{$begin_date}','yyyy-mm-dd hh24:mi:ss') and tap.SIGN_STATUS='1' and  tap.TenantGroup ='10'";
    if ($ec_conn->Execute($sql) === false) {
        print 'error create table: '.$ec_conn->ErrorMsg().'<BR>';exit;
    }
    $total_count = $ec_conn->getOne("select count(*) from {$ec_pinhao_table}");
    if($total_count===false){
        $msg="Table {$ec_pinhao_table} not find!";
        error_msg($msg);
    }
    return $total_count;
}
function download_ec_pinhao(){
    global $page,$pageCount,$pageSize,$ec_conn,$ec_pinhao_table;
    if(!isset($_GET['debug'])){
        $fileName = "EC缺失品号".date("YmdHi");
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $fileName.".csv");
    }
    
    $list_sql = "select * from {$ec_pinhao_table}"; //定义要执行导出的SQL语句
    $pager = new ADODB_Pager($ec_conn,$list_sql); //根据连接对象和SQL语句创建分页对象
    
    $index = 0;//首行
    $fieldArr = array();
    while($page <= $pageCount){
        $rs = $pager->db->PageExecute($list_sql,$pageSize,$page);
        if($index == 0){
            $index = 1;
            $pageCount = $rs->LastPageNo();
            $output = '';
            $ncols = $rs->FieldCount();
            for ($i=0; $i < $ncols; $i++) {
                $field = $rs->FetchField($i);
                $fieldArr[$i] = $field->name;
                $output.= @iconv("UTF-8","GBK",$field->name).",";
            }
            $output=trim($output,',')."\r\n";
            echo $output;
        }
        while (!$rs->EOF) {
            $output = '';
            $row = filter_values($rs->fields);
            for ($i=0; $i < $ncols; $i++) {
                $field = $fieldArr[$i];
                $output.= @iconv("UTF-8","GBK",str_filter($row[$field])).",";
            }
            $output=trim($output,",")."\r\n";
            echo $output;
            $rs->MoveNext();
        }
        $page++;
    }
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
function filter_values($row){
    global  $caizhiArr,$jinseArr,$colorArr,$jinduArr,$shapeArr,$stoneCatArr,$gongyiArr,$yingguangArr,$paoguangArr,$duichenArr,$certTypeArr;
    /*表面工艺*/
    if(!empty($row['INAAUC037']) && isset($gongyiArr[$row['INAAUC037']])){
        $row['INAAUC037'] = $gongyiArr[$row['INAAUC037']];
    }
    /*材质*/
    if(!empty($row['INAAUC038'])&&isset($caizhiArr[$row['INAAUC038']])){
        $row['INAAUC038'] = $caizhiArr[$row['INAAUC038']];
    }
    /*材质颜色*/
    if(!empty($row['INAAUC039']) && isset($jinduArr[$row['INAAUC039']])){
        $row['INAAUC039'] = $jinduArr[$row['INAAUC039']];
    }
    /*主石类型*/
    if(!empty($row['INAAUC045']) && isset($stoneCatArr[$row['INAAUC045']])){
        $row['INAAUC045'] = $stoneCatArr[$row['INAAUC045']];
    }
    /*主石颜色*/
    if(!empty($row['INAAUC047']) && isset($colorArr[$row['INAAUC047']])){
        $row['INAAUC047'] = $colorArr[$row['INAAUC047']];
    }
    /*主石净度*/
    if(!empty($row['INAAUC048']) && isset($jinduArr[$row['INAAUC048']])){
        $row['INAAUC048'] = $jinduArr[$row['INAAUC048']];
    }
    /*主石切工*/
    /* if(!empty($row['INAAUC049']) && isset($shapeArr[$row['INAAUC049']])){
     $row['INAAUC049'] = $shapeArr[$row['INAAUC049']];
     } */
    /*主石形状*/
    if(!empty($row['INAAUC050']) && isset($shapeArr[$row['INAAUC050']])){
        $row['INAAUC050'] = $shapeArr[$row['INAAUC050']];
    }
    /*荧光*/
    if(!empty($row['INAAUC051']) && isset($yingguangArr[$row['INAAUC051']])){
        $row['INAAUC051'] = $yingguangArr[$row['INAAUC051']];
    }
    /*抛光*/
    if(!empty($row['INAAUC052']) && isset($paoguangArr[$row['INAAUC052']])){
        $row['INAAUC052'] = $paoguangArr[$row['INAAUC052']];
    }
    /*对称性*/
    if(!empty($row['INAAUC053']) && isset($duichenArr[$row['INAAUC053']])){
        $row['INAAUC053'] = $duichenArr[$row['INAAUC053']];
    }
    /*副石1净度*/
    if(!empty($row['INAAUC066']) && isset($jinduArr[$row['INAAUC066']])){
        $row['INAAUC066'] = $jinduArr[$row['INAAUC066']];
    }
    /*副石1类型*/
    if(!empty($row['INAAUC067']) && isset($stoneCatArr[$row['INAAUC067']])){
        $row['INAAUC067'] = $stoneCatArr[$row['INAAUC067']];
    }
    /*副石1形状*/
    if(!empty($row['INAAUC069']) && isset($shapeArr[$row['INAAUC069']])){
        $row['INAAUC069'] = $shapeArr[$row['INAAUC069']];
    }
    /*副石1颜色*/
    if(!empty($row['INAAUC070']) && isset($colorArr[$row['INAAUC070']])){
        $row['INAAUC070'] = $colorArr[$row['INAAUC070']];
    }
    /*副石2类型*/
    if(!empty($row['INAAUC072']) && isset($stoneCatArr[$row['INAAUC072']])){
        $row['INAAUC072'] = $stoneCatArr[$row['INAAUC072']];
    }
    /*副石3类型*/
    if(!empty($row['INAAUC075']) && isset($stoneCatArr[$row['INAAUC075']])){
        $row['INAAUC075'] = $stoneCatArr[$row['INAAUC075']];
    }
    /*证书类型*/
    if(!empty($row['INAAUC043']) && isset($certTypeArr[$row['INAAUC043']])){
        $row['INAAUC043'] = $certTypeArr[$row['INAAUC043']];
    }
    return $row;
}
function import_inaauc_imaa(){
    global $page,$pageCount,$pageSize,$ec_conn,$t100_conn,$ec_pinhao_table;
    echo "<br/>Start:将EC品号数据转导入到T100数据库:<hr/>";  
    echo "Begin Time:".date("Y-m-d H:i:s")."<br/>";
    //T100数据库连接
    $tableName ="TT_INAAUC_IMAA";
    $sql = "DROP TABLE {$tableName}";
    $t100_conn->Execute($sql);
    $sql = "create table {$tableName} as select * from dsdata.inaauc_t where 1=0";
    if ($t100_conn->Execute($sql) === false) {
        //$t100_conn->RollbackTrans();
        $error = "error create table {$tableName}: ".$t100_conn->ErrorMsg()."<BR>";
        error_msg($error);
    }
    
    $list_sql = "select * from {$ec_pinhao_table}"; //EC品号临时表2
    $pager = new ADODB_Pager($ec_conn,$list_sql); //根据连接对象和SQL语句创建分页对象
    
    $index = 0;//首行
    $num = 0;//控制总记录
    $fieldArr = array();
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
            $val_sql = "";
            $row = filter_values($rs->fields);
            for ($i=0; $i < $ncols; $i++) {
                $field = $fieldArr[$i];
                $val_sql .= "'".trim($row[$field])."',";
            }
            $val_sql = trim($val_sql,",");
            $insert_sql = "insert into {$tableName}(inaauc001,inaauc002,inaauc004,inaauc106,inaauc030,inaauc109,inaauc037,inaauc038,inaauc039,inaauc045,
            inaauc057,inaauc046,inaauc047,inaauc048,inaauc049,inaauc050,inaauc051,inaauc052,inaauc053,inaauc054,inaauc055,
            inaauc056,inaauc058,inaauc060,inaauc063,inaauc066,inaauc067,inaauc068,inaauc069,inaauc070,inaauc071,inaauc072,
            inaauc073,inaauc074,inaauc075,inaauc076,inaauc077,inaauc034,inaauc043,inaauc139) values({$val_sql})";
            if ($t100_conn->Execute($insert_sql) === false) {
                echo "货号{$row['INAAUC001']} - error create table: ".$t100_conn->ErrorMsg()."<br/>";
            }
    
            $rs->MoveNext();
        }
        $page++;
    }
    echo "<br/>导入临时表{$tableName}完毕！总数据：{$num}<br/>";
    echo "End Time:".date("Y-m-d H:i:s")."<br/>";
}
function import_imaa(){
    global $t100_conn;
    echo "<br/>Start:导入T100正式料件档,共6个步骤:<hr/>";
    echo "Begin Time:".date("Y-m-d H:i:s")."<br/>";
    $tableName = "tt_imaa";
    $sql = "DROP TABLE {$tableName}";
    $t100_conn->Execute($sql);
    $sql = "create table {$tableName} as SELECT * from DSDATA.imaa_t where 1=0";
    if ($t100_conn->Execute($sql) === false) {
        $error ='error create table: '.$t100_conn->ErrorMsg().'<BR>';
        error_msg($error);
    }    
    $sql ="insert into {$tableName}(IMAAENT,IMAA001,IMAA002,IMAA003,IMAA004,IMAA006,IMAA009,IMAA010,IMAA011,IMAA012,IMAA027,IMAA034,IMAA036,IMAA037,IMAA038,IMAA043,IMAA044,IMAA100,IMAA104,IMAA105,IMAA106,IMAA107,IMAA108,IMAA109,IMAA115,IMAA116,IMAA119,IMAA120,IMAA126,IMAA142,IMAA144,IMAA145,IMAA146,IMAA147,IMAA148,IMAA149,IMAAUA001,IMAAUA002,IMAAUA003,IMAAUA004,IMAAUA005,IMAAUA006,IMAAUA007,IMAAUA008,IMAAUA009,IMAAUA014,IMAAUA016,IMAAUA031,IMAAUA032,IMAAUA010,IMAAUA011,IMAAUA017,IMAAUA023,IMAAUA024,IMAAUA025,IMAAUA026,IMAAUA027,IMAAUA028,IMAAUA083,IMAAUA117,IMAAUA118,IMAAUA119,IMAAUA120,IMAAUA121,IMAAUA012,IMAAUA013,IMAAUA018,IMAAUA019,IMAAUA015,IMAAUA020,IMAAUA021,IMAAUA022,IMAAUA029,IMAAUA034,IMAAUA037,IMAAUA077,IMAAUA078,IMAAUA057,IMAAUA062,IMAAUA065,IMAAUA079,IMAAUA036,IMAAUA038,IMAAUA041,IMAAUA044,IMAAUA046,IMAAUA047,IMAAUA048,IMAAUA049,IMAAUA050,IMAAUA051,IMAAUA052,IMAAUA054,IMAAUA055,IMAAUA056,IMAAUA058,IMAAUA059,IMAAUA061,IMAAUA064,IMAAUA070,IMAAUA071,IMAAUA072,IMAAUA080,IMAAUA081,IMAAUA095,IMAAUA096,IMAAUA097,IMAAUA108,IMAAUA109,IMAAUA110,IMAAUA111,IMAAUA112,IMAAUA113,IMAAUA115,IMAAUA116,IMAAUA122,IMAASTUS) SELECT '10' AS imaaent,--企业编号 企业编号
INAAUC002 AS imaa001,--料号
'1' AS imaa002,--目前版本 默认为1
INAAUC004 AS imaa003,--款式编码 来源款式库的编码
imca004 AS imaa004,--料件类别 默认与款式库一致
imca006 AS imaa006,--基础单位 默认与款式库一致
imcaua002 AS imaa009,--产品分类 默认与款式库一致
imca010 AS imaa010,--生命周期状态 默认与款式库一致
'0' AS imaa011,--产出类型 默认为0
'N' AS imaa012,--允许副产品 默认为N            
'N' AS imaa027,--为包装容器 默认为N            
'0' AS imaa034,--料号来源 默认为0
'N' AS imaa036,--记录位置(插件) 默认为N
'N' AS imaa037,--组装位置须勾稽 默认为N
'N' AS imaa038,--工程料件 默认为N            
'N' AS imaa043,--据点研发可调整组件 默认为N
'3' AS imaa044,--AVL控管点 默认为3
'2' AS imaa100,--条码分类 默认为2            
imca006 AS imaa104,--库存单位 默认为款式库的基础单位编码
imcaua101 AS imaa105,--销售单位 默认为款式库的销售单位编码
imcaua102 AS imaa106,--销售计价单位 默认为款式库的销售计价单位编码
imcaua099 AS imaa107,--采购单位 默认为款式库的采购单位编码
'1' AS imaa108,--商品种类 默认为1
'1' AS imaa109,--条码类型 默认为1
'0' AS imaa115,--预计进货价格 默认为0
'0' AS imaa116,--预计销货价格 默认为0            
'0' AS imaa119,--试销金额 默认为0
'0' AS imaa120,--试销数量 默认为0            
'KELA' AS imaa126,--品牌 默认为空            
'1000' AS imaa142,--制定组织
'N' AS imaa144,--库存多单位 默认为N
imcaua100 AS imaa145,--采购计价单位 默认=款式库的采购计价单位
imca006 AS imaa146,--成本单位 默认=基础单位      
'0' AS imaa147,--默认商品临期比例 默认为0
'0' AS imaa148,--商品临期天数 默认为0
'1' AS imaa149,--临期控管方式 默认为1
imcaua003 AS imaaua001,--按款管理否 Y/N
imcaua004 AS imaaua002,--虚拟款/料号否 Y/N
imcaua005 AS imaaua003,--仅售现货 Y/N
imcaua006 AS imaaua004,--一口价否 Y/N
imcaua007 AS imaaua005,--群镶否 Y/N
imcaua008 AS imaaua006,--有石否 Y/N
imcaua009 AS imaaua007,--有副石否 Y/N
imcaua010 AS imaaua008,--款式形态 来源对应款式编码的款式形态编码
imcaua011 AS imaaua009,--款式形状 来源对应款式编码的款式形状编码
imcaua034 AS imaaua014,--臂形态 来源对应款式编码的臂形态编码
imcaua017 AS imaaua016,--镶嵌方式 来源对应款式编码的镶嵌方式编码
imcaua032 AS imaaua031,--活口 来源对应款式编码的值，Y/N
imcaua033 AS imaaua032,--戒臂带钻 来源对应款式编码的值，Y/N

CASE WHEN instr(imcaua012,'|')>0 THEN '9' ELSE imcaua012 END AS imaaua010,--生产工艺 来源对应款式编码的生产工艺，值只能在款式库的生产工艺范围内
CASE WHEN inaauc037 is not null THEN inaauc037 WHEN imcaua013 is null THEN '0' ELSE substr(imcaua013,0,instr(imcaua013, '|', 1, 1)-1 ) END AS imaaua011,--表面工艺 来源对应款式编码的表面工艺，值只能在款式库的表面工艺范围内
CASE WHEN inaauc043 is not null THEN inaauc043 WHEN instr(imcaua018,'|')>0 THEN '11' ELSE imcaua018 END AS imaaua017,--证书类型 来源对应款式编码的证书类型编码，值只能在款式库的证书类型范围内
CASE WHEN inaauc053 is not null THEN inaauc053 WHEN instr(imcaua024,'|')>0 THEN 'OT' ELSE imcaua024 END AS imaaua023,--对称性 来源对应款式编码的对称性编码。
CASE WHEN inaauc054 is not null THEN inaauc054 WHEN instr(imcaua025,'|')>0 THEN 'OT' ELSE imcaua025 END AS imaaua024,--颜色分级/饱和度 来源对应款式编码的颜色分级/饱和度编码
CASE WHEN inaauc052 is not null THEN inaauc052 WHEN instr(imcaua027,'|')>0 THEN 'OT' ELSE imcaua027 END AS imaaua025,--抛光 来源对应款式编码的抛光编码
CASE WHEN inaauc051 is not null THEN inaauc051 WHEN instr(imcaua026,'|')>0 THEN 'OT' ELSE imcaua026 END AS imaaua026,--荧光 来源对应款式编码的荧光编码
CASE WHEN inaauc055 is not null THEN inaauc055 WHEN instr(imcaua028,'|')>0 THEN 'OT' ELSE imcaua028 END AS imaaua027,--珍珠形状 来源对应款式编码的珍珠形状编码
CASE WHEN inaauc056 is not null THEN inaauc056 WHEN instr(imcaua029,'|')>0 THEN 'OT' ELSE imcaua029 END AS imaaua028,--珍珠颜色 来源对应款式编码的珍珠颜色编码
CASE WHEN imcaua064 IS NULL THEN 'N' ELSE 'Y' END AS imaaua083,--成对否 Y/N
CASE WHEN NVL(inaauc068,0)>0 THEN round(inaauc071/NVL(inaauc068,0),6) ELSE 0 END AS imaaua117,--副石1重 默认为0
CASE WHEN NVL(inaauc072,0)>0 THEN round(inaauc074/NVL(inaauc072,0),6) ELSE 0 END AS imaaua118,--副石2重 默认为0
CASE WHEN NVL(inaauc076,0)>0 THEN round(inaauc077/NVL(inaauc076,0),6) ELSE 0 END AS imaaua119,--副石3重 默认为0

'0' AS imaaua120,--其他副石重 默认为0，若存在，则维护单粒石头最多重量。
nvl(inaauc034,'N') AS imaaua121,--自采否，Y/N
inaauc038 AS imaaua012,--CAIZHI材质 来源对应款式编码的材质，值只能在款式库的材质范围内
inaauc039 AS imaaua013,--CAIZHIYANSE材质颜色 来源对应款式编码的材质颜色，值只能在款式库的材质颜色范围内
inaauc045 AS imaaua018,--ZHUSHI主石类型 来源对应款式编码的主石类型编码，值只能在款式库的主石类型范围内
inaauc057 AS imaaua019,--JINGDU主石净度 来源对应款式编码的主石净度编码，值只能在款式库的主石净度范围内
inaauc046 AS imaaua015,--XIANGKOU镶口 来源对应款式编码的镶口编码，值只能在款式库的镶口范围内。起版情况，直接维护，可与款式库的副石关系表不一致
inaauc049 AS imaaua020,--CUT主石切工 来源对应款式编码的主石切工编码，值只能在款式库的主石切工范围内。
inaauc050 AS imaaua021,--SHAPE主石形状 来源对应款式编码的主石形状编码，值只能在款式库的主石形状范围内
inaauc047 AS imaaua022,--COLOR主石颜色
NVL(inaauc057,0) AS imaaua029,--ZHUSHI_NUM主石粒数 来源对应款式编码的值，若不存在则为0
inaauc030 AS imaaua034,--TUO_TYPE金托类型 1.成品；2.空托；1，2两种情况默认与款式库一致。款式库为3的情况，料号时，按实际情况维护1或2
inaauc060 AS imaaua037,--ZHIQUAN指圈 默认来源款式库的信息.值必须在款式库范围内
inaauc106 AS imaaua077,--DINGZHICHENGBEN参考采购单价（未税） 有则维护，没有则默认为0
inaauc106 AS imaaua078,--DINGZHICHENGBEN参考名义成本单价（未税） 有则维护，没有则默认为0
inaauc068 AS imaaua057,--FUSHI_NUM1副石1粒数 直接维护，来源款式库的副石关系表的值。若不存在则为0
inaauc072 AS imaaua062,--FUSHI_NUM2副石2粒数
inaauc076 AS imaaua065,--FUSHI_NUM3副石3粒数
inaauc109 AS imaaua079,--SALE_PRICE参考零售价 有则维护，没有则默认为0
'0' AS imaaua036,--总重 默认为0，按实际情况维护数字
imcaua038 AS imaaua038,--改圈范围(+/-) 默认为0，按实际情况维护数字
imcaua041 AS imaaua041,--链长(cm) 默认来源款式库的信息.值必须在款式库范围内
imcaua044 AS imaaua044,--能否刻字 默认来源款式库的信息.Y/N
imcaua046 AS imaaua046,--3D否 默认来源款式库的信息.Y/N
imcaua047 AS imaaua047,--欧版戒否 默认来源款式库的信息.Y/N
imcaua048 AS imaaua048,--直爪否 默认来源款式库的信息.Y/N
imcaua049 AS imaaua049,--爪带钻 默认来源款式库的信息
imcaua050 AS imaaua050,--爪钉形状 默认来源款式库的信息
imcaua051 AS imaaua051,--爪头数量 默认来源款式库的信息
imcaua052 AS imaaua052,--爪形态 默认来源款式库的信息
imcaua054 AS imaaua054,--图案 默认来源款式库的图案编码编码。
CASE WHEN inaauc066 is not null THEN inaauc066 ELSE imcaua083 END AS imaaua055,--副石1净度 默认来源款式库的副石1净度编码编码。起版情况，直接维护，可与款式库的副石关系表不一致
CASE WHEN inaauc067 is not null THEN inaauc067 ELSE imcaua109 END AS imaaua056,--副石1类型 默认来源款式库的其他副石1类型编码编码。起版情况，直接维护，可与款式库的副石关系表不一致
CASE WHEN inaauc069 is not null THEN inaauc069 ELSE imcaua110 END AS imaaua058,--副石1形状 默认来源款式库的副石1形状编码编码。
CASE WHEN inaauc070 is not null THEN inaauc070 ELSE imcaua084 END AS imaaua059,--副石1颜色 默认来源款式库的副石1颜色编码编码。
CASE WHEN inaauc072 is not null THEN inaauc072 ELSE imcaua111 END AS imaaua061,--副石2类型 默认来源款式库的其他副石2类型编码编码
CASE WHEN inaauc075 is not null THEN inaauc075 ELSE imcaua112 END AS imaaua064,--副石3类型 默认来源款式库的副石3类型编码编码
imcaua055 AS imaaua070,--刻字图案 默认来源款式库的刻字图案编码。可为空。为空业务相关单据不控制刻字图案
imcaua058 AS imaaua071,--工厂模号
'0' AS imaaua072,--销售工费 有则维护，没有则默认为0
'0' AS imaaua080,--参考会员价 有则维护，没有则默认为0
imcaua064 AS imaaua081,--关联款号 来源款式库的款式编码。多个值中间用“|”分隔
imcaua077 AS imaaua095,--统包货否 Y/N
imcaua078 AS imaaua096,--销售工费计价方式 维护1或2；
imcaua079 AS imaaua097,--裸石否 Y/N
imcaua089 AS imaaua108,--款式分类 来源款式库的款式分类码
imcaua090 AS imaaua109,--畅销度 来源款式库的畅销度编码
imcaua091 AS imaaua110,--产品线 来源款式库的产品线编码
imcaua092 AS imaaua111,--品牌 来源款式库的品牌编码
imcaua093 AS imaaua112,--款式风格 来源款式库的款式风格编码
imcaua094 AS imaaua113,--款式性别 来源款式库的款式性别编码
imcaua096 AS imaaua115,--市场细分 来源款式库的市场细分编码
imcaua097 AS imaaua116,--系列及款式归属 来源款式库的系列及款式归属编码
imcaua117 AS imaaua122, --商品分类，与款式信息同步
'Y' as imaastus
FROM TT_INAAUC_IMAA LEFT JOIN DSDATA.IMCA_T ON inaauc004=imca001";
    if ($t100_conn->Execute($sql) === false) {
        //$t100_conn->RollbackTrans();
        $error =  "T100品号转标准料件数据格式失败：".$t100_conn->ErrorMsg().'<BR>';
        error_msg($error);        
    }else{
        echo "1.T100品号转标准料件数据格式 成功！<br/>";
    } 
    //2.品号去重
    $sql ="delete from tt_imaa where imaa001 is null or imaa006 is null or imaa001 like '%--%'";
    $t100_conn->Execute($sql);
    $sql ="delete from tt_imaa where imaa001 in (select imaa001 from tt_imaa group by imaa001 having count(*)>1) 
and rowid not in (select min(rowid) from tt_imaa group by imaa001 having count(*)>1)";
    if ($t100_conn->Execute($sql) === false) {
        //$t100_conn->RollbackTrans();
        $error =  "2.T100品号临时表去重失败: ".$t100_conn->ErrorMsg().'<BR>';
        error_msg($error);
    }else{
        echo "2.T100品号临时表去重成功！<br/>";
    }
    //3.清洗TT_IMAA表默认值及料件状态
    $sql = "update tt_imaa set imaastus='Y',imaacrtdt = sysdate,imaamoddt = sysdate";//料件状态，时间
    $t100_conn->Execute($sql);
    if ($t100_conn->Execute($sql) === false) {
        //$t100_conn->RollbackTrans();
        $error =  "3.T100品号临时表默认值更新失败: ".$t100_conn->ErrorMsg().'<hr>';
        error_msg($error);
    }else{
        echo "3.T100品号临时表默认值更新成功！<br/>";
    } 
    
    //4.更新正式数据中与临时表重复的记录 用于下发EC
    $sql ="update dsdata.imaa_t set imaastus='Y',imaamoddt = sysdate where imaa001 in(select imaa001 from tt_imaa)";
    $t100_conn->Execute($sql);
    if ($t100_conn->Execute($sql) === false) {
        //$t100_conn->RollbackTrans();
        $error =  "4.T100正式表已存在品号下发失败: ".$t100_conn->ErrorMsg().'<hr>';
        error_msg($error);
    }else{
        echo "4.T100正式表已存在品号下发成功！<br/>";
    }
    //5.插入新料号
    $sql = "insert into dsdata.imaa_t select * from tt_imaa where imaa001 not in(select imaa001 from dsdata.imaa_t)";
    $t100_conn->Execute($sql);
    if ($t100_conn->Execute($sql) === false) {
        //$t100_conn->RollbackTrans();
        $error =  "5.T100正式表新增料号失败: ".$t100_conn->ErrorMsg().'<hr>';
        error_msg($error);
    }else{
        echo "5.T100正式表新增料号成功！<br/>";
    }
    //6.新增品名
    $sql = "delete from dsdata.imaal_t where imaal001 in(select imaa001 from tt_imaa)";
    $t100_conn->Execute($sql);
    $sql = "insert into dsdata.imaal_t select 10,a.imaa001 ,'zh_CN' ,b.imcaua001 ,'' ,'' from dsdata.imaa_t a left join dsdata.imca_t b on a.imaa003=b.imca001 where a.imaa001 in(select imaa001 from tt_imaa)";
    if ($t100_conn->Execute($sql) === false) {
        //$t100_conn->RollbackTrans();
        $error =  "6.T100正式表新增品名失败: ".$t100_conn->ErrorMsg().'<hr>';
        error_msg($error);
    }else{
        echo "6.T100正式表新增品名成功！<br/>";
    } 
    echo "End Time:".date("Y-m-d H:i:s")."<br/>";
}
function init_form_html($total_count){
    
    ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>EC缺失品号导出报表</title>
<style> 
body{padding:0;margin:0; background-color:#66CCFF;}
table{width:100%; border-spacing:0; border:0; text-align:center}
table td{ padding:5px 0px; font-weight:bold}
</style>
<script src="http://www.my97.net/dp/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<script>
function f_submit(){
   window.document.form_id.submit();
}
</script>
</head>
<body>
<table  style="background-color:#66CCFF; height:100px">
  <tr>
  <td style="padding-top:20px"><?php echo $_GET["begin_date"]?> 到 <?php echo $_GET["end_date"]?> EC缺失品号报表</td>
  </tr>
  <form action="" method="get" name="form_id" id="form_id">
  <tr>
  <td style="padding-bottom:20px">
    <input type="text" id="begin_date" name="begin_date" value="<?php echo $_GET["begin_date"]?>" onClick="WdatePicker()"> - 
	<input type="text" id="end_date" name="end_date" value="<?php echo $_GET["end_date"]?>" onClick="WdatePicker()">
	<input type="submit" id="btn_submit" value="搜索查看" ><br/><br/>
	最近查询时间:<?php echo date("Y-m-d H:i:s")?>
	<br/><br/>
	<a href="export_pinhao.php?begin_date=<?php echo $_GET["begin_date"]?>&end_date=<?php echo $_GET["end_date"]?>&do=download">1.点击下载EC品号数据(总数据<?php echo $total_count?>)</a>
	<br/><br/>
	<a href="export_pinhao.php?begin_date=<?php echo $_GET["begin_date"]?>&end_date=<?php echo $_GET["end_date"]?>&do=import1" target="_blank">2.确认EC品号下载数据无误，开始导入T100临时表(总数据<?php echo $total_count?>)</a>
	<br/><br/>
	<a href="export_pinhao.php?begin_date=<?php echo $_GET["begin_date"]?>&end_date=<?php echo $_GET["end_date"]?>&do=import2" target="_blank">3.确认导入T100临时表成功，开始导入T100料件正式表IMAA_T</a>
	<hr/>
	<a href="export_pinhao.php?begin_date=<?php echo $_GET["begin_date"]?>&end_date=<?php echo $_GET["end_date"]?>&do=import" target="_blank">一键导入T100正式区料件库</a>
	<hr/>
	<a href="export_pinhao.php?begin_date=<?php echo $_GET["begin_date"]?>&end_date=<?php echo $_GET["end_date"]?>&do=import&toptst=1" target="_blank">一键导入T100练习区料件库</a>
	</td>
  </tr>
  </form>
</table> 
    <?php 
    exit;
}
?>