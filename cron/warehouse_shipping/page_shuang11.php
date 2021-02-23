<?php 
 include("adodb/adodb.inc.php");         //包含adodb类库文件 
 $conn = NewADOConnection('oci8');
 $db_server ="TOPPRD";
 /*$db_server = "DESCRIPTION =
    (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.1.191)(PORT = 1521))
    (CONNECT_DATA =
      (SERVER = DEDICATED)
      (SERVICE_NAME = TOPPRD)
    )";*/
 $db_user = "erpread";
 $db_pass = "xp5NKV6Tw";
 $conn->Connect($db_server,$db_user,$db_pass);  
 
  
if(empty($_GET['begin_date'])){
    $begin_date = date("Y-m-d H:i:s",strtotime('-1 days'));
    $_GET['begin_date'] = date("Y-m-d",strtotime('-1 days'));
 }else{
    $begin_date = $_GET['begin_date'].' 00:00:00';
 }
 if(empty($_GET['end_date'])){
     $end_date = date("Y-m-d").' 23:59:59';
     $_GET['end_date'] = date("Y-m-d");
 }else{
     $end_date = $_GET['end_date'].' 23:59:59';
 }
 if($begin_date>$end_date){
     $begin_date = date("Y-m-d H:i:s",strtotime('-1 days',strtotime($end_date)));
     $_GET['begin_date'] = date("Y-m-d",strtotime('-1 days',strtotime($end_date)));
 }
 $shop_code = "";
 if(isset($_GET['shop_code'])){
     $shop_code = $_GET['shop_code'];
 }
 $sql = "SELECT xmbduc001,xmbdluc003  FROM dsdata.xmbduc_t,dsdata.xmbdluc_t WHERE xmbducent=xmbdlucent AND xmbduc001=xmbdluc001  AND xmbdluc002='zh_CN'  AND xmbducent=10  ORDER BY xmbduc001";
 $shop_list = $conn->GetArray($sql);
 $shop_list = array_column($shop_list,"XMBDLUC003","XMBDUC001");

 $location = preg_replace("/\&t\=.*/is","",$_SERVER['REQUEST_URI'])."&t=".time();
 $auto_time = empty($_GET['auto_time'])?"0":$_GET['auto_time'];

 $sql_where = $shop_code!=""?"xmdaua043 ='{$shop_code}'":"xmdaua043 is not null";
 $sql_where .= " AND xmdasite='1000' and xmdaua021 not in('5','6') and xmdastus<>'X' and XMDADOCDT>=to_date('{$begin_date}','yyyy-mm-dd hh24:mi:ss') and XMDADOCDT<=to_date('{$end_date}','yyyy-mm-dd hh24:mi:ss')";
 

 //定制单总数
 //$sql = "select count(*) from DSDATA.XMDA_T where XMDADOCNO in(select XMAAUCDOCNO from DSDATA.XMAAUC_T) and {$sql_where}";
 $sql = "select count(*) from dsdata.xmda_t where xmdadocno in(select DISTINCT XMBIUCDOCNO from DSDATA.XMBIUC_T where xmbiuc026 is null) and ".$sql_where;
 $order_count_dingzhi = $conn->getOne($sql);
 //当天下单已发货总数
 //$sql = "select count(*) from DSDATA.XMDA_T where XMDADOCNO not in(select XMAAUCDOCNO from DSDATA.XMAAUC_T) and xmdadocno in(SELECT	DISTINCT xmdh001 FROM DSDATA.xmdh_t WHERE xmdhua174=5) and {$sql_where}";
 $sql = "select count(*) from DSDATA.XMDA_T where XMDADOCNO in(select DISTINCT XMBIUCDOCNO from DSDATA.XMBIUC_T where xmbiuc026 is null) and xmdadocno in(SELECT	DISTINCT xmdh001 FROM DSDATA.xmdh_t WHERE xmdhua174=5) and {$sql_where}";
 $order_count_dingzhi_shipped = $conn->getOne($sql);

 //定制单未发货总数
 //$sql = "select count(*) from DSDATA.XMDA_T where XMDADOCNO not in(select XMAAUCDOCNO from DSDATA.XMAAUC_T) and xmdadocno not in(SELECT	DISTINCT xmdh001 FROM DSDATA.xmdh_t WHERE xmdhua174=5) and xmdaua043 not in('9','40') and {$sql_where}";
 //$sql = "select count(*) from DSDATA.XMDA_T where XMDADOCNO in(select DISTINCT XMBIUCDOCNO from DSDATA.XMBIUC_T where xmbiuc026 is null) and xmdadocno not in(SELECT	DISTINCT xmdh001 FROM DSDATA.xmdh_t WHERE xmdhua174=5) and xmdaua043 not in('9','40') and {$sql_where}";
 //$order_count_dingzhi_unshipped = $conn->getOne($sql);
 
 //现货单总数
 //$sql = "select count(*) from DSDATA.XMDA_T where XMDADOCNO not in(select XMAAUCDOCNO from DSDATA.XMAAUC_T) and {$sql_where}";
 $sql = "select count(*) from dsdata.xmda_t where xmdadocno not in(select DISTINCT XMBIUCDOCNO from DSDATA.XMBIUC_T where xmbiuc026 is null) and ".$sql_where;
 $order_count_xianhuo = $conn->getOne($sql);
 //当天下单已发货
 //$sql = "select count(*) from DSDATA.XMDA_T where XMDADOCNO not in(select XMAAUCDOCNO from DSDATA.XMAAUC_T) and xmdadocno in(SELECT	DISTINCT xmdh001 FROM DSDATA.xmdh_t WHERE xmdhua174=5) and {$sql_where}";
 $sql = "select count(*) from DSDATA.XMDA_T where xmdadocno in(SELECT	DISTINCT xmdh001 FROM DSDATA.xmdh_t WHERE xmdhua174='5') and {$sql_where}";
 $order_count_shipped = $conn->getOne($sql);
 //已验货未发货
 if(in_array($shop_code,array('9'))){
     $sql = "select count(*) from DSDATA.XMDA_T where xmdadocno in(SELECT	DISTINCT xmdh001 FROM DSDATA.xmdh_t WHERE xmdhua174 in('3','4')) and {$sql_where}";
 }else{
     $sql = "select count(*) from DSDATA.XMDA_T where xmdadocno in(SELECT	DISTINCT xmdh001 FROM DSDATA.xmdh_t WHERE xmdhua174 in('3','4')) and xmdaua043 not in('9') and {$sql_where}";
 }
 $order_count_yanhuo_unshiped = $conn->getOne($sql);
 //echo $sql;
 //现货单未发货总数
 if(in_array($shop_code,array('9'))){
     $sql = "select count(*) from DSDATA.XMDA_T where XMDADOCNO not in(select DISTINCT XMBIUCDOCNO from DSDATA.XMBIUC_T where xmbiuc026 is null) and xmdadocno not in(SELECT DISTINCT xmdh001 FROM DSDATA.xmdh_t WHERE xmdhua174=5) and {$sql_where}";
 }else{
     $sql = "select count(*) from DSDATA.XMDA_T where XMDADOCNO not in(select DISTINCT XMBIUCDOCNO from DSDATA.XMBIUC_T where xmbiuc026 is null) and xmdadocno not in(SELECT DISTINCT xmdh001 FROM DSDATA.xmdh_t WHERE xmdhua174=5) and xmdaua043 not in('9') and {$sql_where}";
 }
 $order_count_xianhuo_unshipped = $conn->getOne($sql);
 //echo $sql;
 //订单已发货总量
 $sql = "select count(*) from DSDATA.xmdk_t where XMDKSITE='1000' AND xmdk000 = '1' AND xmdk002 <> '8' and xmdk007<>'2000' AND xmdkua008='2'  AND XMDKDOCDT>=to_date('{$begin_date}','yyyy-mm-dd hh24:mi:ss') and XMDKDOCDT<=to_date('{$end_date}','yyyy-mm-dd hh24:mi:ss')";
 $order_total_shipped = $conn->getOne($sql);

 //订单总数
 $sql = "select count(*) from DSDATA.XMDA_T where {$sql_where}";
 $order_count = $conn->getOne($sql);
 //订单总金额
 $sql = "select SUM(xmdaua055) from DSDATA.XMDA_T where {$sql_where}";
 $order_amount = $conn->getOne($sql);
 //已付款金额
 $sql = "select SUM(xmdaua045) from DSDATA.XMDA_T where {$sql_where}";
 $order_amount_payed = $conn->getOne($sql);
 //未付款金额
 $sql = "select SUM(xmdaua055-xmdaua045) from DSDATA.XMDA_T where {$sql_where}";
 $order_amount_unpayed = $conn->getOne($sql);
 ?> 
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>BDD双十一订单汇总报表</title>
<style> 
body{padding:0;margin:0; background-color:#0033CC;}
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
<style>

</style>
<table  style="background-color:#66CCFF; height:100px">
  <tr>
  <td style="padding-top:20px"><?php echo $_GET["begin_date"]?> 到 <?php echo $_GET["end_date"]?> 订单统计</td>
  </tr>
  <form action="" method="get" name="form_id" id="form_id">
  <tr>
  <td style="padding-bottom:20px">
    <select name="shop_code" id="shop_code">
	<option value="">所有店铺</option>
	<?php foreach ($shop_list as $k=>$v){?>
	<option value="<?php echo $k?>" <?php echo $shop_code!="" && $shop_code==$k?"selected":"";?>><?php echo $k.'.'.iconv("GBK","UTF-8", $v)?></option>
	<?php }?>
	</select>
    <input type="text" id="begin_date" name="begin_date" value="<?php echo $_GET["begin_date"]?>" onClick="WdatePicker()"> - 
	<input type="text" id="end_date" name="end_date" value="<?php echo $_GET["end_date"]?>" onClick="WdatePicker()">
	<input type="button" id="btn_submit" value="搜索查看" onclick="f_submit()">
	<select name="auto_time" id="auto_time" onchange="f_submit()">
	<option value="0">不自动刷新</option>
	<?php for ($i=1;$i<=10;$i++){?>
	<option value="<?php echo $i?>" <?php echo $auto_time==$i?"selected":"";?>><?php echo $i?>分钟自动刷新</option>
	<?php }?>
	</select><br/><br/>
	最近查询时间:<?php echo date("Y-m-d H:i:s")?>
	</td>
  </tr>
  </form>
</table> 
<table style="background-color:#FFCC66; padding:50px 0px 40px 0">  
  <tr>
    <td>当天已发货订单: <?php echo $order_total_shipped;?></td>
  </tr> 
  <tr>
    <td>当天下单已发货: <?php echo $order_count_shipped;?></td>
  </tr> 
  <tr>
    <td>当天现货未发货: <?php echo $order_count_xianhuo_unshipped;?></td>
  </tr>
  <tr>
    <td>已验货未发货: <?php echo $order_count_yanhuo_unshiped;?></td>
  </tr>
  
</table>
<!--<table style="background-color:#999999; padding:20px 0px"> 
  <tr>
    <td>当天定制单数:<?php echo $order_count_dingzhi;?></td>
  </tr>
   <tr>
    <td>定制已发货:  <?php echo $order_count_dingzhi_shipped;?></td>
  </tr>
  <tr>
    <td>定制未发货:  <?php echo $order_count_dingzhi_unshipped;?></td>
  </tr>  
</table>-->
<table style="background-color:#999999; padding:30px 0px 30px 0">  
   <tr>
    <td>定制单数: <?php echo $order_count_dingzhi;?></td>
  </tr>
  <tr>
    <td>现货单数: <?php echo $order_count_xianhuo;?></td>
  </tr>  
</table>
<table style="background-color:#0033CC; color:#FFFFFF; padding:30px 0px 20px 0">  
  <tr>
    <td>订单总数:  <?php echo $order_count;?> </td>
  </tr>    
  <tr>
    <td>订单总金额:  <?php echo number_format($order_amount,2);?>元</td>
  </tr>
  <tr>
    <td>已付款金额:  <?php echo number_format($order_amount_payed,2);?>元</td>
  </tr>
  <tr style="background-color:#0033CC; color:#FFFFFF">
    <td>未付款金额:  <?php echo number_format($order_amount_unpayed,2);?>元</td>
  </tr>
</table> 

<?php if($auto_time>=1){?>
<script type="text/javascript">
setTimeout(function(){f_submit()},<?php echo $auto_time*60*1000?>);
</script>
<?php }?>
</body>
</html>