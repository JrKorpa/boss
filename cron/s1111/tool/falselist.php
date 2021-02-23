<?php
include('../lib/PdoModel.php');
include('../lib/OrderClassModel.php');
$orderclass = new OrderClassModel();
/*默认为查看当天*/
$btime = date('Y-m-d');
$etime = date('Y-m-d');
$where = array(
	'btime'=>$btime,
	'etime'=>$etime,
	'isyushou'=>0
);
$_submit = isset($_REQUEST['timesearch'])?$_REQUEST['timesearch']:'无';
//是否点击了查看
if($_submit == '查看')
{
	$begintime = $_REQUEST['begintime'];
	$endtime = $_REQUEST['endtime'];
	$yushou = $_REQUEST['isyushou'];
	if(!empty($begintime))
	{
		$where['btime'] = $begintime;
	}
	if(!empty($endtime))
	{
		$where['etime'] = $endtime;
	}
	if($endtime < $begintime)
	{
		$where['etime'] = $endtime;
	}
	$where['isyushou'] = $yushou;
}elseif($_submit == 'showtongji')
{
	$data = $_REQUEST['data'];
	$data = base64_decode($data);
	$data = json_decode($data);
	$where['btime'] = $data->btime;
	$where['etime'] = $data->etime;
	$where['isyushou'] = $data->isyushou;
}
$shotitle = $where['isyushou'] >0 ? '预售单':'常规订单';


$import = json_encode($where);
$importstr = base64_encode($import);


$tradstatus = array(
'TRADE_NO_CREATE_PAY'=>'没有创建支付宝交易',
'WAIT_BUYER_PAY'=>'等待买家付款',
'SELLER_CONSIGNED_PART'=>'卖家部分发货',
'WAIT_SELLER_SEND_GOODS'=>'等待卖家发货,即:买家已付款',
'TRADE_FINISHED'=>'交易成功',
'TRADE_CLOSED'=>'付款以后用户退款成功，交易自动关闭',
'TRADE_CLOSED_BY_TAOBAO'=>'付款以前，卖家或买家主动关闭交易',
'PAY_PENDING'=>'国际信用卡支付付款确认中',
'WAIT_PRE_AUTH_CONFIRM'=>'0元购合约中',
'WAIT_BUYER_CONFIRM_GOODS'=>'等待买家确认收货,即:卖家已发货',
'TRADE_BUYER_SIGNED'=>'买家已签收,货到付款专用',
'无'=>'获取异常'
);
$ordertype = array('常规订单','预售订单');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<style type="text/css">
	.page{margin:0 auto; width:900px; height:auto; border-top:1px solid #CCC;}
	.line{
		width:900px;
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
    table tr td{border:1px solid #CCC; padding:10px;}
	table a{color:#09F; text-decoration:underline; float:none;}
</style>
<script type="text/javascript" src="../js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="My97DatePicker/WdatePicker.js"></script>
</head>

<body>
<div class="page">
 
	<div class="line"><?php echo $shotitle;?>抓单失败列表
	<?php 
		$timeshow = $where['btime'].'to'.$where['etime'];
		echo $timeshow;
	?>
    </div>
    <div class="line">
    	<form action="./falselist.php" method="post" enctype="multipart/form-data">
        订单类型:
        <select name="isyushou">
        	<option value="0">常规订单</option>
            <option value="1">预售订单</option>
        </select>&nbsp;&nbsp;
    	时间：<input type="text" class="Wdate" name="begintime" 
        onFocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" />&nbsp;&nbsp;to&nbsp;&nbsp;
  		<input type="text" class="Wdate" name="endtime" 
        onFocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" />&nbsp;&nbsp;
        <input type="submit" value="查看" name="timesearch" />
        </form>
    </div>
    <table>
    	<tr>
        	<td>淘宝订单</td>
            <td>失败原因</td>
            <td>淘宝订单状态</td>
            <td>订单类型</td>
            <td>录入时间</td>
        </tr>
        <?php
			$data = $orderclass->getfalselist($where);
			if($data && $data->num_rows>0)
			{
				while($obj = $data->fetch_assoc())
				{
					$isys = $obj['ispreorder'];
					$ordertypename = $ordertype[$isys];
					
					//状态
					$status = $obj['order_status'];
					$statusname = $tradstatus[$status];
					echo <<< HTML
		<tr>
			<td>{$obj['out_order_sn']}</td>
			<td>{$obj['reason']}</td>
			<td>{$statusname}</td>
			<td>{$ordertypename}</td>
			<td>{$obj['add_time']}</td>	
		</tr>
HTML;
				}
		echo <<< HTML
		<tr>
        	<td colspan="5" align="center">
				<a href="importfalse.php?data=$importstr" target="_blank">
					直接导出信息
				</a>
			</td>
        </tr>
HTML;
			}else{
				echo <<< HTML
				<tr>
					<td colspan="5" align="center">没有找到数据哟</td>
				<tr>
HTML;
			}
		?>
    </table>
</div>
</body>
</html>