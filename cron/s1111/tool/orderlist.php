<style type="text/css">
table{
	width:700px;
	margin:0 auto;
	height:auto;
	font-size:14px;
	border:1px solid #999;
	text-align:left;
	border-collapse:collapse;
	border-width:0px 0 0 1px; 
}
table tr th{border:1px solid #000; padding:10px 5px; background-color:#099; font-size:16px; font-weight:bold; color:#fff;}
table tr td{border:1px solid #000; padding:5px; }
</style>
<?php
include('../taobaoapi.php');
/*
$s_data = '2015-09-25 00:00:00';
$e_data = '2015-09-25 23:59:59';
$allids = $apiModel->getTaobaoOrderList($s_data,$e_data,2,100);
*/
$orderModel = new OrderClassModel();
$orderlist = $orderModel->getorderlist();
echo '
<table>
<tr><th>序号</th><th>淘宝订单id</th><th>BDD订单编号</th><th>结果</th></tr>';
$i=1;
while($obj = $orderlist->fetch_assoc())
{
	$ok = $obj['order_sn'] > 0 ? $obj['order_sn']:'<font color="#FF0000">抓单失败没有生成BDD订单</font>';
	echo <<< HTML
	<tr>
		<td>$i</td>
		<td>{$obj['out_order_sn']}</td>
		<td>{$ok}</td>
		<td>{$obj['reason']}</td>
	</tr>
HTML;
	$i++;
}
echo '</table>';

?>