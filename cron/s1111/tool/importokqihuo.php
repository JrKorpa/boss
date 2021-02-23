<?php
include('../lib/PdoModel.php');
include('../lib/OrderClassModel.php');
$orderclass = new OrderClassModel();
$data = $_REQUEST['data'];
$data = base64_decode($data);
$data = json_decode($data);
$where['btime'] = $data->btime;
$where['etime'] = $data->etime;
$where['name'] = $data->name;
$where['isyushou'] = $data->isyushou;

$isys = '成功的常规期货订单信息';
if($where['isyushou']>0)
{
	$isys = '成功的预售期货订单信息';
}

$name = $where['btime'].'到'.$where['btime'].$isys;
header('content-type:charset=utf-8');
header("Content-Type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename={$name}.xls");    
header("Pragma:no-cache");
header("Expires:0");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<style type="text/css">
	.page{margin:0 auto; width:800px; height:auto; border-top:1px solid #CCC;}
	.line{
		width:800px;
		height:30px;
		float:left;
		line-height:20px;
		font-size:16px;
		text-align:center;
		display:block;
		background-color:#6CF;
	}
	.box{width:300px; height:200px;line-height:40px; margin:20px 30px 10px 30px; padding:20px; }
	.green{background-color:#39F; float:left;color:#fff;font-weight:bold; }
	.red{background-color:#F00; float:right;}
	.gray{background-color:#CCC; float:left;}
	.yellow{background-color:#FF0; float:right;}
	a{color:#FFF; float:right;}
	table{border-collapse:collapse; width:100%; font-size:12px;}
    table tr td{border:1px solid #000; padding:10px;}
	table a{color:#09F; text-decoration:underline; float:none;}
</style>
<script type="text/javascript" src="../js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="My97DatePicker/WdatePicker.js"></script>
</head>

<body>
<div class="page">
 
	<table>
    	<tr>
        	<td>淘宝订单</td>
            <td>BDD订单</td>
            <td>订单总金额</td>
            <td>已付款金额</td>
            <td>未付款金额</td>
            <td>录入时间</td>
        </tr>
        <?php
			$data = $orderclass->getallokqihuolist($where,1);
			if($data)
			{
				while($obj = $data->fetch_assoc())
				{
					echo <<< HTML
		<tr>
			<td>&nbsp;{$obj['out_order_sn']}</td>
			<td>&nbsp;{$obj['order_sn']}</td>
			<td>期货单</td>
			<td>{$obj['add_time']}</td>	
		</tr>
HTML;
				}
			}else{
				echo <<< HTML
				<tr>
					<td colspan="4">没有找到数据哟</td>
				<tr>
HTML;
			}
		?>
    </table>
</div>
</body>
</html>